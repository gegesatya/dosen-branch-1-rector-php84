# LAPORAN AUDIT & VALIDASI IMPLEMENTASI PHASE 1
## RPS CPMK STUDIO & CURRICULUM READINESS

Dokumen ini memuat hasil audit mendalam terhadap kode aktual implementasi Phase 1 pada Controller, Views, dan Database.

---

## DAFTAR RINGKASAN TEMUAN

| No | Kategori | Tingkat Temuan | Komponen / Method | Status |
|---|---|:---:|---|:---:|
| 1 | Lifecycle Record RPS | **OK** | `C_rps_generator::ajax_save_cpmk` | Aman & Terverifikasi |
| 2 | Parameter POST Mismatch | **CRITICAL** | `v_rps_generator_after_page.php` ↔ `ajax_save_cpmk` | Perlu Patch |
| 3 | Payload Structure Mismatch | **CRITICAL** | `ajax_check_curriculum_readiness` ↔ `renderCurriculumReadinessUi` | Perlu Patch |
| 4 | State Lock Setelah Approved | **HIGH** | `v_rps_generator_after_page.php::renderCpmkCards` | Perlu Patch |
| 5 | Single Item Reprompt Context | **MEDIUM** | `ajax_process` (mode `susun_cpmk`) | Perlu Patch |
| 6 | JSON Sanitization & Empty Handling | **MEDIUM** | `C_rps_generator::_parse_and_validate_cpmk_json` | Perlu Patch |
| 7 | Security & Data Ownership | **OK** | `ajax_save_cpmk`, `ajax_check_curriculum_readiness` | Aman (Scoped to `id_ptk`) |
| 8 | Regression Compatibility | **OK** | Legacy RPS, Export Excel, Skenario/BAP, Kontrak, Tugas | Aman & Backward Compatible |

---

## RINCIAN TEMUAN AUDIT

### 1. LIFECYCLE RECORD RPS (`ajax_save_cpmk`)
- **Status:** **OK**
- **File:** `application/controllers/C_rps_generator.php`
- **Method:** `ajax_save_cpmk()` (Line 900–995)
- **Hasil Evaluasi:**
  1. **Skenario A (MK Baru / Belum Ada di `siakad_rps_obe`):**
     - `$id_rps` kosong. Query pencarian berdasarkan `kode_mk`/`nm_mk` + `id_ptk` menghasilkan `NULL`.
     - Masuk ke blok `else`: sistem membuat record minimal di `siakad_rps_obe` dengan atribut `id_ptk`, `kode_mk`, `nm_mk`, `sks_mk`, `cpmk_json`, `cpmk_status`, `id_status_pengajuan_rps = 1` (Draft), `create_by`, dan `create_date`.
     - Mengembalikan `saved_id` baru ke frontend yang kemudian disimpan di input `#id_rps_loaded`.
  2. **Skenario B (MK Sudah Memiliki RPS Existing):**
     - Jika `$id_rps` terisi dari `#id_rps_loaded`, sistem memeriksa kepemilikan dosen (`$exist->id_ptk == $id_ptk`) dan melakukan update in-place.
     - Jika `$id_rps` tidak terisi, query cadangan berdasarkan `kode_mk` / `nm_mk` + `id_ptk` menemukan record lama dan memperbaruinya tanpa membuat record duplikat.
  3. **Skenario C (RPS Legacy dengan `cpmk_json` NULL):**
     - Record lama diperbarui dengan `cpmk_json`, `cpmk_status`, dan `tujuan_belajar`. Kolom `raw_ai_output`, `roadmap_wbi_markdown`, `siakad_rps_obe_detail` tetap utuh.

---

### 2. PARAMETER POST MISMATCH PADA PENYIMPANAN CPMK
- **Tingkat Temuan:** **CRITICAL**
- **File:**
  - `application/views/rps_generator/v_rps_generator_after_page.php` (Line 932 & Line 969)
  - `application/controllers/C_rps_generator.php` (Line 914)
- **Masalah:**
  Frontend mengirim parameter dengan nama `cpmk_list`:
  ```javascript
  data: {
    id_rps: $('#id_rps_loaded').val(),
    cpmk_list: JSON.stringify(activeCpmkList),
    ...
  }
  ```
  Sedangkan Controller membaca:
  ```php
  $cpmk_raw = $this->input->post('cpmk_json');
  ```
- **Dampak:**
  Saat dosen menekan tombol **Simpan Draft CPMK** atau **Setujui CPMK**, Controller menerima `$cpmk_raw = NULL` dan merespons error `Format data CPMK tidak valid atau kosong.` Data CPMK gagal tersimpan ke database.
- **Rekomendasi Perbaikan Minimum:**
  Pada `C_rps_generator::ajax_save_cpmk()`, baca kedua kemungkinan nama field:
  ```php
  $cpmk_raw = $this->input->post('cpmk_json') ?: $this->input->post('cpmk_list');
  ```
  Serta pada frontend, kirim `cpmk_json: JSON.stringify(activeCpmkList)`.

---

### 3. STRUKTUR PAYLOAD MISMATCH PADA CURRICULUM READINESS
- **Tingkat Temuan:** **CRITICAL**
- **File:**
  - `application/controllers/C_rps_generator.php` (Line 757–776)
  - `application/views/rps_generator/v_rps_generator_after_page.php` (Line 540–615)
- **Masalah:**
  Controller mengembalikan response flat:
  ```json
  {
    "status": true,
    "kurikulum_found": true,
    "mk_found": true,
    "cpl_prodi_found": true,
    "cpl_list": ["P-01", "KK-02"],
    "mapping_found": true,
    "mapped_cpl": ["P-01"],
    "bahan_kajian_found": true,
    "deskripsi_found": true,
    "deskripsi_status": "Tersedia dari Kurikulum",
    "cpmk_existing_found": false,
    "cpmk_status": null
  }
  ```
  Namun fungsi `renderCurriculumReadinessUi(res)` di frontend memeriksa nested object:
  ```javascript
  var c = res.checks;
  // membaca c.kurikulum_dokumen.status, c.mk_terdaftar.status, dll.
  ```
- **Dampak:**
  Checklist Curriculum Readiness di panel kiri tidak ter-render (menghasilkan TypeError pada JavaScript `cannot read properties of undefined (reading 'kurikulum_dokumen')`).
- **Rekomendasi Perbaikan Minimum:**
  Format response `ajax_check_curriculum_readiness` di controller agar menyertakan objek `checks` terstruktur yang memuat fakta:
  - `kurikulum_dokumen`: status boolean, label, detail
  - `mk_terdaftar`: status boolean, label, detail
  - `cpl_prodi`: status boolean, label, detail
  - `cpl_mk_mapping`: status boolean, label, detail
  - `bahan_kajian`: status boolean, label, detail
  - `deskripsi_mk`: status boolean, label ("Tersedia dari Kurikulum" / "Tidak Ditemukan"), detail
  - `cpmk_status`: status ("APPROVED" / "AI_PROPOSAL" / "BELUM_ADA"), label, detail

---

### 4. STATE LOCK SETELAH CPMK STATUS "APPROVED"
- **Tingkat Temuan:** **HIGH**
- **File:** `application/views/rps_generator/v_rps_generator_after_page.php` (Line 670–750)
- **Masalah:**
  Saat `activeCpmkStatus === 'APPROVED'`:
  1. Textarea pada kartu CPMK tidak memiliki atribut `readonly` sehingga dosen masih dapat mengetik dan memodifikasi teks.
  2. Tombol `[Naik]`, `[Turun]`, `[Perbaiki AI]`, `[Hapus]`, dan `[Tambah CPMK]` tetap aktif.
  3. Belum ada tombol eksplisit **"Revisi CPMK"** untuk membuka kunci (unlock) kembali ke status draft/proposal.
- **Dampak:**
  CPMK yang sudah berstatus `APPROVED` dapat terubah secara tidak sengaja tanpa alur persetujuan ulang.
- **Rekomendasi Perbaikan Minimum:**
  1. Jika `activeCpmkStatus === 'APPROVED'`:
     - Tambahkan atribut `readonly` dan style background `#f8fafc` pada seluruh `.cpmk-textarea`.
     - Sembunyikan atau disable tombol aksi kartu (`Naik`, `Turun`, `Perbaiki AI`, `Hapus`, `Tambah CPMK`).
     - Tampilkan tombol **"Revisi CPMK"** (`btn-warning`).
  2. Jika tombol **"Revisi CPMK"** diklik:
     - Ubah `activeCpmkStatus = 'AI_PROPOSAL'`.
     - Ubah provenance CPMK yang diedit menjadi `MANUAL`.
     - Render ulang kartu sehingga textarea dapat diedit kembali.

---

### 5. PROMPT CONTEXT PADA PERBAIKI SATU CPMK DENGAN AI
- **Tingkat Temuan:** **MEDIUM**
- **File:** `application/controllers/C_rps_generator.php` (Line 1260–1280)
- **Masalah:**
  Ketika `ajax_process` dipanggil dengan `mode: 'susun_cpmk'`, instruksi AI secara default meminta: *"Hasilkan 2 sampai 6 butir CPMK"*. Ketika dosen hanya ingin memperbaiki **satu CPMK** (misal CPMK-2), prompt umum tersebut dapat membuat AI menghasilkan daftar CPMK baru secara penuh.
- **Dampak:**
  Frontend mengambil item indeks ke-0 (`res.cpmk_data[0]`), yang berisiko menimpa CPMK target dengan CPMK-1 hasil generate baru dari AI.
- **Rekomendasi Perbaikan Minimum:**
  Pada `ajax_process()`, jika terdeteksi naskah sebelumnya berisi 1 butir CPMK tunggal dan terdapat catatan khusus revisi:
  Instruksikan AI secara eksplisit: *"Hanya perbaiki 1 butir CPMK ini tanpa membuat butir CPMK lainnya. Pertahankan CPL acuan kurikulum dan level Bloom C2-C6 yang proporsional."*

---

### 6. VALIDASI & DEDUPLIKASI KODE CPMK PADA `_parse_and_validate_cpmk_json()`
- **Tingkat Temuan:** **MEDIUM**
- **File:** `application/controllers/C_rps_generator.php` (Line 1805–1885)
- **Masalah:**
  1. Jika AI menghasilkan beberapa CPMK dengan kode duplikat (misal dua kali `CPMK-1`), backend belum melakukan re-sekuensial kode (`CPMK-1`, `CPMK-2`, dst.).
  2. Jika output AI benar-benar gagal di-parse dan menghasilkan array kosong `[]`, controller tetap mengembalikan `status: true` dengan `cpmk_data: []`.
- **Dampak:**
  Frontend menerima data kosong tanpa pesan error yang jelas jika AI gagal merespons JSON valid.
- **Rekomendasi Perbaikan Minimum:**
  1. Lakukan sanitasi sekuensial kode di backend: `$kode = 'CPMK-' . ($idx + 1)`.
  2. Jika hasil parse `$items` kosong, kembalikan `['status' => false, 'msg' => 'Format JSON CPMK dari AI tidak valid.']`.

---

### 7. SECURITY & DATA OWNERSHIP
- **Status:** **OK**
- **File:** `application/controllers/C_rps_generator.php`
- **Hasil Evaluasi:**
  - Seluruh endpoint (`ajax_save_cpmk`, `ajax_check_curriculum_readiness`, `ajax_process`) menggunakan session `$id_ptk = $this->session->userdata('id_ptk')`.
  - Pada `ajax_save_cpmk`, verifikasi kepemilikan `$exist->id_ptk == $id_ptk` dijalankan sebelum update dilakukan.
  - Dosen lain tidak dapat memodifikasi record dosen lain dengan mengganti `id_rps` pada request POST.
  - CSRF protection aktif pada seluruh form POST.

---

### 8. REGRESSION COMPATIBILITY
- **Status:** **OK**
- **File:** `application/controllers/C_rps_generator.php`
- **Hasil Evaluasi:**
  - Alur **Generate RPS Lengkap 16 Pertemuan** existing (`mode: 'susun_rps'`) tidak diubah dan tetap berfungsi normal.
  - RPS legacy dengan `cpmk_json NULL` dapat dibuka tanpa error.
  - Generator Kontrak Kuliah, Rancangan Tugas, Roadmap WBI, Strategi Pertemuan, Sinkronisasi BAP, dan Export Excel 13 Kolom tetap mempertahankan integritas dan fungsionalitas aslinya.

---

## REKOMENDASI TINDAKAN

Phase 1 memiliki fondasi arsitektur yang kuat dan aman, namun **membutuhkan patch kecil (quick patch)** untuk mengatasi 2 isu critical (mismatch nama parameter POST dan payload response readiness) serta 1 isu high (state locking approved) sebelum dapat diuji coba oleh pengguna secara optimal.

Patch yang direkomendasikan adalah patch terfokus pada:
1. Penyesuaian nama parameter `cpmk_json` / `cpmk_list` di Controller & View.
2. Penyesuaian struktur payload response di `ajax_check_curriculum_readiness()`.
3. Penambahan state `readonly` dan tombol **Revisi CPMK** pada kartu yang sudah `APPROVED`.
4. Sanitasi single-item reprompt pada mode `susun_cpmk`.
