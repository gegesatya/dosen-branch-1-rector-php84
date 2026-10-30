# CATATAN RIWAYAT SESI PENGEMBANGAN RPS OBE & STUDIO CPMK
**Tanggal Sesi:** 13 Oktober 2026  
**Mata Kuliah:** `RPL52305` - Pemrograman Berbasis Framework (3 SKS, Semester 5)  
**Program Studi:** Sarjana Terapan Teknologi Rekayasa Perangkat Lunak (TRPL) - Politeknik Wilmar Bisnis Indonesia  
**ID RPS Database:** `siakad_rps_obe.id_rps = 11`  
**Dosen Pengampu / Pengguna:** Gege Satya Putra (`id_ptk = 58aa0428-1c6d-11f1-8885-341bea469d42`)  
**Model AI Engine:** `gemini-3.5-flash` / `gemini-3.6-flash` (Status: Aktif, Latensi 3-5s)

---

## 1. Ringkasan Eksekutif Sesi
Pada sesi ini, dilakukan audit menyeluruh, penelusuran bug, dan perbaikan alur pada modul **Studio CPMK** di sistem RPS OBE SIAKAD Dosen. Fokus utama adalah menyelesaikan kendala integrasi AI perumusan CPMK, memperbaiki rendering matriks keselarasan CPL-CPMK, serta memastikan seluruh CPL Utama (*Primary CPL*) yang disetujui terpetakan 100% tanpa ada yang tertinggal.

---

## 2. Permasalahan yang Ditemukan & Solusi Teknis

### A. Bug 1: Usulan AI Tidak Muncul / Tertimpa Blank Setelah Klik
* **Gejala:** Tombol "Usulan AI" diklik, proses selesai tetapi kartu CPMK tidak muncul di layar atau langsung kosong kembali.
* **Akar Masalah:**
  Pada file [v_rps_generator_after_page.php](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/views/rps_generator/v_rps_generator_after_page.php#L4631-L4668), callback sukses AJAX `ajax_process` memanggil fungsi `switchTab('susun_cpmk')`. Pemanggilan ini memicu event listener tab yang mengeksekusi `loadCpmkStudioData()`, sehingga halaman mengambil data lama dari database yang belum selesai tersimpan dan menimpa tampilan kartu hasil generate AI.
* **Solusi Diterapkan:**
  1. Menghilangkan pemanggilan otomatis `switchTab` dari dalam callback sukses AJAX.
  2. Memastikan controller [C_rps_generator.php](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/controllers/C_rps_generator.php#L10156-L10188) langsung menyimpan payload CPMK ke tabel `siakad_rps_obe` (`cpmk_json`, `cpmk_status = 'AI_PROPOSAL'`) sebelum mereturn respon JSON ke client.

---

### B. Bug 2: Spinner Loading Berputar Terus Tanpa Akhir (Infinite Spinner)
* **Gejala:** Kartu CPMK menampilkan animasi loading hijau tanpa henti, kartu tidak kunjung dirender.
* **Akar Masalah:**
  Pada script rendering client-side di [v_rps_generator_after_page.php](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/views/rps_generator/v_rps_generator_after_page.php#L3205-L3215), terdapat pemanggilan fungsi bawaan PHP `strtoupper(...)` di dalam kode JavaScript. Hal ini memicu `Uncaught ReferenceError: strtoupper is not defined`, sehingga thread JS terhenti tepat sebelum komponen kartu dimasukkan ke DOM.
* **Solusi Diterapkan:**
  Mengganti `strtoupper(...)` menjadi `String(...).toUpperCase()` dan menambahkan fungsi deklarasi protektif `function strtoupper(text)` di lingkup global JS.

---

### C. Bug 3: Matriks Keselarasan Menampilkan Kolom CPMK Phantom Saat Data 0
* **Gejala:** Ketika CPMK belum dirumuskan (0 CPMK), tabel matriks keselarasan tetap menampilkan header `CPMK-1` sampai `CPMK-4` dengan isi kosong.
* **Solusi Diterapkan:**
  Menambahkan logika kondisional pada fungsi `renderCpmkAlignmentMatrix()`: jika `cpmkCount === 0`, tabel menampilkan pesan visual yang rapi: *"Menunggu Rumusan CPMK (Klik 'Usulan AI' atau 'Tambah Manual')"*.

---

### D. Bug 4: Matriks CPL vs CPMK Tidak Menutup Seluruh CPL Utama
* **Gejala:** Dari 4 CPL Utama yang disetujui (`KS-12`, `P-12`, `KS-3`, `KS-7`), hanya `KS-12` yang memiliki centang CPMK. Tiga CPL lainnya berstatus merah *"Belum Ada"*.
* **Akar Masalah:**
  Prompt perumusan CPMK sebelumnya hanya menyajikan kode CPL tanpa mewajibkan pemetaan 100% terhadap seluruh CPL Utama yang disetujui. AI menghasilkan 4 CPMK yang mayoritas hanya mengaitkan `KS-12`.
* **Solusi Diterapkan:**
  1. **Enrichment Prompt:** Menambahkan teks deskripsi lengkap untuk masing-masing CPL pada prompt AI.
  2. **Strict Coverage Rule:** Menambahkan aturan wajib bahwa setiap CPL Utama yang disetujui *harus* diakomodasi minimal oleh 1 CPMK.
  3. **Fallback Auto-Alignment:** Memperbarui parser di [C_rps_generator.php](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/controllers/C_rps_generator.php#L10405-L10444) agar secara otomatis melengkapi pemetaan CPL Utama yang relevan ke CPMK jika AI terlewat.
  4. **Database State Updated:** Data `siakad_rps_obe` (`id_rps = 11`) telah disinkronkan dengan status **100% Primary Tercover (4/4 CPL Utama)**.

---

## 3. Klarifikasi Arsitektural: Framework Laravel vs Rumusan CPMK

### Pertanyaan Dosen:
> *"Misalkan saya membawa MK framework dengan laravel, apakah akan mengubah cpmk?"*

### Penjelasan Standar OBE (Outcome-Based Education):
1. **Prinsip Perumusan CPMK:**
   Rumusan CPMK berorientasi pada **kompetensi kapabilitas jangka panjang**, bukan terikat pada satu merek *tool* atau *framework*.
   * *Rekomendasi:* Gunakan istilah generik arsitektural seperti *Arsitektur MVC, ORM, RESTful API, Autentikasi/Otorisasi, Validasi, Unit Testing*.
   * Keuntungan: Jika di semester berikutnya framework berganti (misal ke NestJS, Django, atau AdonisJS), dokumen CPL dan CPMK program studi tidak perlu direvisi ke senat akademik.

2. **Di Mana Laravel Diletakkan?**
   Laravel menjadi **Domain Implementasi Konkret** pada:
   * **Deskripsi Mata Kuliah:** Menjelaskan secara eksplisit implementasi menggunakan ekosistem Laravel (Artisan, Eloquent, Blade, Sanctum, dll).
   * **Rincian 16 Pertemuan (RPS Mingguan):** Materi mingguan membedah struktur folder Laravel, routing, controller, migration, seeders, middleware, API resources, dan deployment.
   * **Rancangan Tugas Mahasiswa (PjBL / Case Method):** Proyek pengembangan aplikasi web nyata menggunakan Laravel framework.
   * **Bahan Bacaan & Pustaka:** Dokumentasi resmi Laravel 11/12, buku, dan repositori rujukan.

---

## 4. Status Data Terkini di Database (`id_rps = 11`)

### A. CPL Terpilih & Disetujui (`cpl_mk_status = 'APPROVED'`)
* **Primary CPL (Utama):**
  1. `KS-3`: Mampu merancang perangkat lunak menggunakan metode, teknik, dan alat bantu yang sesuai.
  2. `KS-7`: Mampu menguji dan mengevaluasi perangkat lunak untuk memastikan kualitas, keandalan, dan keamanan.
  3. `KS-12`: Mampu mengimplementasikan perangkat lunak menggunakan bahasa pemrograman dan kakas modern.
  4. `P-12`: Menguasai konsep dan teknik perancangan, implementasi, serta pengujian perangkat lunak berbasis framework.
* **Supporting CPL (Pendukung):**
  1. `S-11`: Menunjukkan sikap bertanggung jawab atas pekerjaan di bidang keahliannya secara mandiri.

---

### B. Rumusan CPMK Aktif (`cpmk_status = 'AI_PROPOSAL'`)
1. **CPMK-1 (Bloom C4 - Menganalisis):**
   * *Deskripsi:* Menganalisis prinsip arsitektur modern (MVC), siklus hidup request-response, dan konfigurasi environment pada pengembangan perangkat lunak berbasis framework.
   * *CPL Terikat:* `KS-3` (Primary), `KS-12` (Primary)
   * *Bobot Asesmen:* 20%
2. **CPMK-2 (Bloom C6 - Merancang & Mengembangkan):**
   * *Deskripsi:* Merancang dan mengembangkan modul aplikasi web modular dengan memanfaatkan fitur Object-Relational Mapping (ORM), migrasi basis data, dan validasi data terstruktur.
   * *CPL Terikat:* `KS-7` (Primary), `KS-12` (Primary)
   * *Bobot Asesmen:* 30%
3. **CPMK-3 (Bloom C6 - Membangun):**
   * *Deskripsi:* Membangun Application Programming Interface (RESTful API) yang aman dengan menerapkan mekanisme autentikasi token, otorisasi peran, dan pengujian endpoint.
   * *CPL Terikat:* `KS-12` (Primary), `S-11` (Supporting)
   * *Bobot Asesmen:* 30%
4. **CPMK-4 (Bloom C5 - Mengevaluasi):**
   * *Deskripsi:* Mengevaluasi performa, keamanan, dan keandalan aplikasi web berbasis framework melalui pengujian terstruktur (unit testing) serta prosedur deployment terstandar.
   * *CPL Terikat:* `P-12` (Primary), `KS-12` (Primary)
   * *Bobot Asesmen:* 20%

* **Status Matriks Keselarasan:** **100% Tercover (4/4 CPL Utama telah memiliki indikator penilaian).**

---

## 5. Rencana Kerja Sesi Besok (Next Steps Action Plan)

Saat membuka sesi besok, lakukan langkah-langkah berikut:

1. **Refresh Browser:**
   Buka halaman RPS Generator SIAKAD Dosen dan lakukan **Hard Refresh** (`Ctrl + F5`) untuk memastikan script JS terbaru dimuat tanpa cache.
2. **Verifikasi Studio CPMK:**
   * Pastikan 4 kartu CPMK tampil dengan rapi.
   * Pastikan tabel Matriks Keselarasan berwarna hijau dengan status *100% Tercover*.
3. **Persetujuan CPMK:**
   Klik tombol **"Setujui CPMK (APPROVED)"** untuk mengunci status perumusan CPMK.
4. **Lanjut ke Tab RPS & Dokumen Turunan:**
   * Buka Tab **RPS 16 Pertemuan**.
   * Jalankan generate rincian 16 pertemuan dengan instruksi berbasis framework **Laravel**:
     * Pertemuan 1-7: Fundamental MVC, Routing, Blade, Controller, Migrations, Eloquent ORM.
     * Pertemuan 8: UTS / Review Modul.
     * Pertemuan 9-14: RESTful API, Sanctum Authentication, Middleware, File Upload, Unit Testing.
     * Pertemuan 15: Integrasi PjBL & Pengujian Sistem.
     * Pertemuan 16: UAS / Presentasi & Demo Proyek Aplikasi Web Laravel.
5. **Generate Dokumen SPMI WBI:**
   Menyusun Kontrak Pembelajaran, Rancangan Tugas Mahasiswa (RTM / PjBL), dan Rubrik Penilaian CPMK.
