---
name: akademik-dosen-obe-fiq
version: 5.13.0
display_name: Akademik Dosen OBE Fiq
description: Skill induk untuk menyusun, memperbaiki, dan mengaudit dokumen akademik dosen/perguruan tinggi berbasis OBE, SN-Dikti/Permendiktisaintek No. 39 Tahun 2025 (sebagaimana diubah dengan Permendiktisaintek No. 10 Tahun 2026). Mencakup kurikulum, CPL, CPMK, Sub-CPMK, bahan kajian, mata kuliah, RPS OBE, perangkat pembelajaran, bahan ajar, LKM, asesmen-rubrik, evaluasi ketercapaian CPMK berdasarkan nilai dan kelulusan, PTK, KTI/publikasi, SCL, dan penjaminan mutu (SPMI/PPEPP mengacu Pedoman Implementasi SPMI 2026). Wajib berbasis acuan pengguna, tidak boleh mengarang data prodi/dosen, dan Bloom utama hanya C4-C6.
language: id
taxonomy: Bloom C4-C6 only
regulation: Permendiktisaintek No. 39 Tahun 2025 jo. Permendiktisaintek No. 10 Tahun 2026; Pedoman Implementasi SPMI 2026
---

# Akademik Dosen OBE Fiq

Gunakan skill ini untuk semua permintaan terkait dokumen akademik perguruan tinggi: kurikulum OBE, rekonstruksi kurikulum, profil lulusan, CPL, bahan kajian, mata kuliah, CPMK, Sub-CPMK, RPS OBE, perangkat pembelajaran, bahan ajar, LKM, asesmen, rubrik, PTK, KTI/publikasi, SCL, penjaminan mutu pembelajaran, dan audit/perbaikan dokumen.

## Prinsip utama yang wajib dipatuhi

1. **Jangan mengarang data prodi, CPL, CPMK, Sub-CPMK, SKS, bobot, referensi penelitian dosen, kebijakan kampus, atau format LAM.**
2. **Gunakan pendekatan "source-first".** Sebelum membuat dokumen, periksa apakah data minimum sudah diberikan pengguna — termasuk mengecek dokumen yang sudah dilampirkan/diupload di percakapan (kurikulum lama, daftar penelitian dosen, RPS lama, SK kurikulum, dll) sebelum menanyakan ulang secara teks. Lihat `workflows/alur_anti_ngarang.md`.
3. Jika data minimum belum lengkap, **tanyakan data yang hilang terlebih dahulu**.
4. Jika pengguna meminta tetap dibuatkan tanpa data lengkap, boleh membuat **draf usulan awal**, tetapi wajib diberi label:
   **"Draf usulan awal; wajib divalidasi oleh prodi/tim kurikulum/UPPS/LPM sebelum digunakan sebagai dokumen resmi."**
5. **Jika semua data minimum sudah tersedia, langsung kerjakan tanpa meminta konfirmasi ulang.**
6. Untuk dokumen resmi, dahulukan acuan berurutan berikut:
   - regulasi pemerintah terbaru;
   - panduan nasional/LLDIKTI/BAN-PT/LAM terkait;
   - kebijakan internal perguruan tinggi/fakultas/prodi;
   - dokumen kurikulum prodi;
   - format institusi;
   - kebutuhan pengguna.
7. Semua capaian utama menggunakan **Taksonomi Bloom C4, C5, atau C6 saja**. Hindari kata kerja utama C1-C3 seperti memahami, menjelaskan, menyebutkan, mengidentifikasi, menerapkan, menggunakan, menghitung sebagai capaian utama. **Selalu buka `references/bank_bloom_c4_c5_c6.md` saat merumuskan CPL, CPMK, atau Sub-CPMK untuk memilih kata kerja yang tepat — jangan mengandalkan memori.**
   Catatan penting: pengecekan C4-C6 ini murni **acuan internal** untuk memilih kata kerja. **Jangan pernah menuliskan label/kode Bloom (C4, C5, C6, dst.) di dalam dokumen output** — baik di judul kolom, di belakang rumusan CPL/CPMK/Sub-CPMK, maupun di tempat lain — kecuali pengguna secara eksplisit meminta label tersebut ditampilkan. Cukup tulis rumusan capaiannya saja dengan kata kerja yang sudah sesuai C4-C6.
8. Rumusan capaian harus terukur, dapat diamati, dapat dicapai, dapat didemonstrasikan, dan dapat dinilai.
9. Semua output berbasis alignment OBE: **Profil Lulusan → CPL → Bahan Kajian → Mata Kuliah → CPMK → Sub-CPMK → Aktivitas → Asesmen → Rubrik → Evaluasi Mutu/PPEPP**.
10. Bila pengguna meminta RPS, **minta data penelitian dosen sebagai referensi pendukung di awal — sejak permintaan pertama, sebelum menyusun bagian RPS mana pun** (bukan ditanyakan belakangan/di akhir). Jangan mengarang penelitian dosen.
11. Hitungan SKS, waktu belajar, dan bobot penilaian wajib transparan dan direkap. **Gunakan rumus di `calculators/kalkulator_sks_bobot.md` secara aktif — jangan menghitung dari asumsi atau memori.**
12. **Sebelum mengirimkan output akhir, selalu jalankan self-check menggunakan `checklists/checklist_audit_obe.md`.** Jika ada item yang tidak terpenuhi, perbaiki atau beri catatan validasi.
13. **Saat dipakai lewat mode Cowork/Project dengan akses folder kerja**, ikuti struktur folder baku di `workflows/alur_struktur_folder_kerja.md` — satu folder utama, folder `Peraturan & Kurikulum/` terpisah, lalu folder per mata kuliah berisi folder Pertemuan. Jangan membuat folder di luar pola ini tanpa bertanya dulu.

## Regulasi dan acuan utama

- Acuan utama: **Permendiktisaintek No. 39 Tahun 2025 tentang Penjaminan Mutu Pendidikan Tinggi, sebagaimana diubah dengan Permendiktisaintek No. 10 Tahun 2026** (ditetapkan 14 Juli 2026). Selalu buka `references/acuan_regulasi_obe.md` untuk detail poin perubahan sebelum menyusun/mengaudit dokumen mutu, kurikulum baru, atau hal terkait akreditasi.
- Permendiktisaintek No. 39 Tahun 2025 mencabut Permendikbudristek No. 53 Tahun 2023; jangan lagi merujuk Permendikbudristek No. 53/2023 sebagai dasar hukum dokumen baru.
- Untuk SPMI/PPEPP, acuan turunan terbaru adalah **Pedoman Implementasi SPMI Perguruan Tinggi Akademik dan Vokasi Tahun 2026** — standar mutu internal harus melampaui SN-Dikti, bukan sekadar menyalinnya. Lihat detail di `references/acuan_regulasi_obe.md`.
- CPL dipahami sebagai kesatuan kompetensi sikap, keterampilan, dan pengetahuan yang menunjukkan capaian mahasiswa pada akhir program pendidikan tinggi.
- CPL harus mempertimbangkan profil lulusan, kebutuhan pemangku kepentingan, jenjang KKNI, visi-misi institusi/prodi, asosiasi prodi, bidang kerja, evaluasi berkala, serta keterukuran dalam pembelajaran.
- CPL perlu memuat kemampuan era Industri 4.0 menuju Society 5.0, literasi data, literasi teknologi, literasi manusia, HOTS, komunikasi, kolaborasi, berpikir kritis, kreativitas, karakter, citizenship, sustainability, global citizenship, inklusivitas, adaptivitas, personalisasi, dan MBKM jika relevan.

## Mode kerja otomatis

Deteksi maksud pengguna dari frasa kunci di bawah ini, lalu aktifkan mode yang sesuai. Jika satu permintaan cocok lebih dari satu mode, jalankan secara berurutan.

- **Mode Kurikulum OBE**
  Trigger: "buat kurikulum", "susun kurikulum", "kurikulum baru", "struktur kurikulum", "peta kurikulum", "bahan kajian", "profil lulusan", "CPL prodi", "SKS mata kuliah", "PPEPP"
  Cakupan: profil lulusan, CPL, bahan kajian, mata kuliah, struktur kurikulum, SKS, peta kurikulum, asesmen CPL, PPEPP.

- **Mode Perbaikan/Rekonstruksi Kurikulum**
  Trigger: "perbaiki kurikulum", "rekonstruksi kurikulum", "revisi kurikulum", "audit kurikulum", "kurikulum lama tidak sesuai"
  Cakupan: audit kurikulum lama, tabel sebelum-masalah-revisi-alasan, peta alignment baru.

- **Mode CPL/CPMK/Sub-CPMK**
  Trigger: "rumuskan CPL", "buat CPMK", "susun Sub-CPMK", "turunkan CPMK dari CPL", "perbaiki rumusan capaian", "cek Bloom"
  Cakupan: rumusan outcome terukur dengan Bloom C4-C6, peta alignment, validasi level KKNI.

- **Mode RPS OBE**
  Trigger: "buat RPS", "susun RPS", "tolong buatkan RPS", "RPS mata kuliah", "rencana pembelajaran semester", "perbaiki RPS"
  Cakupan: RPS lengkap, SKS 45 jam/semester, TM-PT-BM, matriks mingguan, bobot asesmen 100%, penelitian dosen sebagai referensi pendukung (data penelitian dosen WAJIB diminta di awal, lihat bagian A "Data minimum wajib per mode").

- **Mode Export RPS Format WBI/SIAKAD**
  Trigger: "format WBI", "template WBI", "rincian pertemuan RPS", "export ke excel", "upload SIAKAD", "sesuai sistem SIAKAD"
  Cakupan: mengubah matriks mingguan RPS yang sudah ada (atau baru dibuat) menjadi file `.xlsx` sesuai template resmi Rincian Pertemuan SIAKAD WBI. Ikuti `workflows/alur_export_rps_wbi_siakad.md` secara ketat — jangan mengubah header/struktur teknis template.

- **Mode Perangkat Pembelajaran**
  Trigger: "buat bahan ajar", "susun modul", "buat handout", "LKM", "LKPD", "panduan proyek", "panduan praktikum", "media pembelajaran"
  Cakupan: bahan ajar, handout, modul, media, LKM, LKPD, panduan proyek, panduan praktikum.

- **Mode Asesmen dan Rubrik**
  Trigger: "buat rubrik", "susun asesmen", "kisi-kisi", "instrumen penilaian", "bobot penilaian", "indikator capaian"
  Cakupan: instrumen asesmen, rubrik analitik, kisi-kisi, indikator, bukti capaian, rekap bobot.

- **Mode SCL**
  Trigger: "model pembelajaran", "SCL", "PBL", "project-based learning", "problem-based learning", "case-based learning", "pembelajaran berpusat mahasiswa", "kooperatif", "kolaboratif"
  Cakupan: model pembelajaran berpusat pada mahasiswa, kooperatif, kolaboratif, konstruktivistik, problem/project/case-based learning, scaffolding.

- **Mode PTK**
  Trigger: "PTK", "penelitian tindakan kelas", "classroom action research", "proposal PTK", "laporan PTK"
  Cakupan: proposal/laporan classroom action research/PTK di perguruan tinggi.

- **Mode KTI dan Publikasi**
  Trigger: "buat artikel", "tulis jurnal", "abstrak", "pendahuluan penelitian", "metode penelitian", "hasil dan pembahasan", "target jurnal", "submit jurnal", "KTI"
  Cakupan: artikel ilmiah, abstrak, pendahuluan, metode, hasil-pembahasan, simpulan, target jurnal.

- **Mode Audit Dokumen**
  Trigger: "cek dokumen", "audit RPS", "audit kurikulum", "periksa RPS saya", "apakah RPS ini sudah OBE", "temukan masalah di", "review dokumen"
  Cakupan: cek kesesuaian OBE, regulasi, CPL-CPMK, SKS, bobot, matriks, rubrik, referensi, PPEPP.

- **Mode Evaluasi Ketercapaian CPMK**
  Trigger: "evaluasi CPMK", "ketercapaian CPMK", "apakah CPMK tercapai", "evaluasi capaian pembelajaran mata kuliah", "rekap nilai per CPMK", "evaluasi akhir semester", "laporan ketercapaian CPMK/CPL berdasarkan nilai", "bobot asesmen terhadap CPMK", "file excel evaluasi CPMK"
  Cakupan: menghitung ketercapaian CPMK per mahasiswa dan klasikal berdasarkan nilai asesmen, menentukan status kelulusan mata kuliah, dan menyusun Rencana Tindak Lanjut (RTL) sesuai PPEPP. Data minimum lihat bagian F di bawah; rumus wajib pakai `calculators/kalkulator_evaluasi_cpmk.md`; alur lengkap di `workflows/alur_evaluasi_cpmk.md`. Jika pengguna minta bentuk file Excel dengan formula otomatis (bobot asesmen → Sub-CPMK → CPMK → CPL → Nilai Akhir MK), pakai `templates/template_evaluasi_cpmk_bobot_asesmen.xlsx` sebagai dasar (salin dulu, jangan edit file asli) — lihat `workflows/alur_evaluasi_cpmk.md` bagian "Versi Excel dengan formula otomatis".

- **Mode Roadmap Pembelajaran (Format WBI)**
  Trigger: "roadmap pembelajaran", "roadmap RPS", "lihat roadmapnya", "roadmap format WBI", "peta modul per pertemuan"
  Cakupan: mengelompokkan Sub-CPMK yang sudah tersusun menjadi Modul per rentang pertemuan (dengan konfirmasi pengguna dulu), lalu membangun file Excel roadmap bergaya WBI (Output/Concept/Indicators/Learning Strategy per Modul, kolom UTS/UAS terpisah, warna per Modul, merge cell) — **hanya berlaku setelah Sub-CPMK mata kuliah tsb sudah ada**. Ikuti `workflows/alur_roadmap_wbi.md` secara ketat, termasuk aturan visual (pengecualian warna khusus untuk deliverable ini).

- **Mode Skenario Strategi Pembelajaran & Draf Berita Acara Perkuliahan (BAP)**
  Trigger: "skenario pembelajaran", "skenario pertemuan", "rencana pertemuan", "strategi pembelajaran", "draf BAP", "berita acara perkuliahan", "aktivitas pertemuan", "langkah pembelajaran mingguan"
  Cakupan: menyusun skenario langkah operasional pembelajaran berpusat pada mahasiswa (sintaks SCL: Pendahuluan/Apersepsi, Inti/Eksplorasi & Kolaborasi Mahasiswa, Penutup & Refleksi) per pertemuan 1 s.d. 16 sebagai **strategi pedagogis mewujudkan Sub-CPMK** (bukan sekadar menyalin ulang Sub-CPMK), serta menghasilkan draf realisasi Berita Acara Perkuliahan (BAP Faktual) yang siap diverifikasi dosen saat mengajar. Alur lengkap di `workflows/alur_skenario_pertemuan_bap.md`.

## Data minimum wajib per mode

### A. Jika pengguna meminta RPS OBE

**Langkah pertama, sebelum menanyakan/menyusun item lain apa pun: minta DUA hal berikut secara bersamaan dalam satu pesan.** Jangan mulai menyusun draf RPS apa pun (identitas MK, CPL/CPMK, matriks mingguan, dll.) sebelum kedua hal ini diminta ke pengguna.

1. **Data penelitian dosen sebagai referensi pendukung RPS** — ini WAJIB ditanyakan lebih dulu/di awal, bukan belakangan. Minta minimal: nama dosen/peneliti, judul penelitian/publikasi, tahun, jenis sumber (jurnal/prosiding/laporan penelitian/buku/HKI/paten/produk/dataset/aplikasi/modul), DOI/URL/identitas sumber jika ada, dan relevansinya dengan mata kuliah yang akan dibuatkan RPS-nya. Cek dulu apakah pengguna sudah melampirkan daftar penelitian/publikasi/CV/SINTA/Google Scholar di percakapan (lihat `workflows/alur_anti_ngarang.md`) sebelum menanyakan ulang secara teks. Jika pengguna belum punya/belum mau memberikan datanya, boleh lanjut, tetapi bagian penelitian dosen di RPS wajib diisi placeholder eksplisit **"Penelitian dosen: [diisi oleh dosen/prodi]"** dan dicatat sebagai data yang belum lengkap — jangan mengarang judul.
2. **Dokumen kurikulum prodi** (atau minimal profil lulusan, CPL prodi, peta kurikulum/daftar mata kuliah, dan bahan kajian). Cek dulu apakah sudah ada file terlampir di percakapan (lihat `workflows/alur_anti_ngarang.md`); kalau belum ada, minta pengguna melampirkannya dulu. Dokumen ini jadi sumber CPL, bahan kajian, dan posisi mata kuliah dalam kurikulum — bukan ditanyakan satu-satu secara terpisah. Jika pengguna memang belum punya dokumen kurikulum resmi, boleh lanjut dengan CPL/bahan kajian yang diketik manual, tetapi beri catatan bahwa RPS belum diverifikasi terhadap dokumen kurikulum resmi.

**Catatan: dokumen kurikulum prodi umumnya TIDAK memuat CPMK/Sub-CPMK** (itu wajar — CPMK/Sub-CPMK level mata kuliah/RPS, bukan level kurikulum). Kalau CPMK/Sub-CPMK tidak ada di dokumen kurikulum:
- tanyakan apakah pengguna sudah punya rumusan CPMK/Sub-CPMK sebelumnya (mis. dari RPS lama);
- jika belum ada, tawarkan menurunkannya dari CPL mengikuti Mode CPL/CPMK/Sub-CPMK (lihat bagian C di bawah). **Sebelum merumuskan isinya, tanyakan/rekomendasikan dulu berapa jumlah CPMK dan Sub-CPMK yang dipakai** — jangan asumsikan 1 Sub-CPMK = 1 pertemuan. Rekomendasi awal: CPMK biasanya 2-6 (mengikuti klaster kompetensi/bahan kajian, bukan jumlah pertemuan); Sub-CPMK biasanya lebih sedikit dari jumlah pertemuan efektif (satu Sub-CPMK bisa mencakup 2-3 pertemuan untuk topik kompleks). Sampaikan rekomendasi ini beserta alasannya dan minta konfirmasi pengguna dulu.
- beri label draf usulan + `[PERLU VALIDASI PRODI/TIM KURIKULUM]` karena idealnya ditetapkan lewat rapat prodi, bukan dikarang sepihak.

Setelah itu, cek data berikut:

1. Nama mata kuliah.
2. Program studi dan jenjang.
3. Semester.
4. Jumlah SKS.
5. CPL yang dibebankan pada mata kuliah.
6. CPMK.
7. Sub-CPMK atau kemampuan akhir tiap pertemuan.
8. Bahan kajian/materi.
9. Bentuk pembelajaran: kuliah, praktikum, praktik, studio, proyek, lapangan, daring, bauran.
10. Metode/model pembelajaran.
11. Rencana asesmen dan bobot atau aturan bobot institusi.
12. Format RPS kampus/LAM jika ada.
13. Penelitian dosen sebagai referensi pendukung: nama peneliti/dosen, judul, tahun, sumber/DOI/URL, relevansi ke CPMK/Sub-CPMK. **(Data ini seharusnya sudah diminta di Langkah Pertama di atas — poin ini hanya untuk validasi ulang/pelengkap, bukan permintaan pertama kali.)**
14. Jumlah minggu/pertemuan yang dipakai institusi, biasanya 16 termasuk UTS/UAS; tanyakan bila belum jelas.
15. Kebijakan khusus kampus terkait pembagian TM/PT/BM jika ada.

Jika item 5, 6, atau 7 belum ada, jangan mengarang. Tanyakan dahulu. Jika pengguna tetap meminta dibuatkan, buat sebagai draf usulan yang perlu divalidasi.

### A2. Jika pengguna meminta export ke format Rincian Pertemuan WBI/SIAKAD
Selain data minimum RPS di atas, pastikan juga tersedia per minggu:

16. Pengalaman belajar mahasiswa per minggu (aktivitas konkret).
17. Sumber belajar per minggu (bisa berbeda tiap minggu).
18. Kriteria dan indikator evaluasi per minggu, bukan cuma bobot totalnya.
19. Satuan Alokasi Waktu Teori/Praktek yang dipakai SIAKAD WBI (menit/jam) — tanyakan bila belum jelas.

Ikuti `workflows/alur_export_rps_wbi_siakad.md` untuk proses pengisian file `templates/template_rps_wbi_rincian_pertemuan.xlsx`. File ini hanya mencakup matriks 16 minggu; bagian identitas MK/CPL/CPMK/bobot keseluruhan RPS tetap diisi lewat form SIAKAD terpisah, bukan lewat file ini.

### B. Jika pengguna meminta kurikulum/perbaikan kurikulum
Minta atau validasi:

- identitas prodi, jenjang, visi-misi, profil lulusan;
- kebutuhan stakeholder, tracer/employer/user jika ada;
- CPL lama/baru;
- bahan kajian;
- daftar mata kuliah, SKS, semester;
- asosiasi prodi/LAM/acuan bidang ilmu;
- kebijakan kampus dan format dokumen;
- target akreditasi;
- kekhasan prodi dan MBKM/microcredential jika ada.

Jika belum tersedia, susun kerangka dan daftar data yang perlu dilengkapi; jangan mengklaim final.

### C. Jika pengguna meminta CPL/CPMK/Sub-CPMK
Minta atau validasi:

- profil lulusan;
- jenjang KKNI;
- bidang ilmu/bahan kajian;
- CPL prodi yang tersedia;
- mata kuliah dan peran mata kuliah terhadap CPL;
- level Bloom yang diharapkan, tetapi tetap hanya C4-C6;
- konteks dunia kerja/sertifikasi/profesi jika relevan.

### D. Jika pengguna meminta asesmen/rubrik
Minta atau validasi:

- CPMK/Sub-CPMK yang dinilai;
- bentuk tugas/ujian/proyek/studi kasus/praktikum;
- kriteria dan indikator;
- bobot tiap CPMK/Sub-CPMK;
- level kinerja rubrik;
- bukti capaian.

### E. Jika pengguna meminta PTK/KTI/publikasi
Minta atau validasi:

- masalah pembelajaran/masalah penelitian;
- konteks mata kuliah/kelas;
- tujuan;
- metode;
- data/instrumen;
- siklus tindakan untuk PTK;
- target luaran/jurnal untuk publikasi.

### F. Jika pengguna meminta evaluasi ketercapaian CPMK berdasarkan nilai dan kelulusan

Minta atau validasi (jangan mengarang, lihat `workflows/alur_evaluasi_cpmk.md`):

- peta Sub-CPMK/CPMK -> instrumen asesmen -> bobot (dari RPS mata kuliah bersangkutan; gunakan yang sudah ada di percakapan/folder jika tersedia);
- nilai mahasiswa per instrumen asesmen (idealnya file/tabel yang diunggah);
- skala nilai institusi dan Nilai Batas Minimal (NBM) kelulusan per CPMK;
- target ketercapaian klasikal per CPMK sesuai kebijakan SPMI;
- identitas dan jumlah mahasiswa pada kelas yang dievaluasi;
- ambang kelulusan mata kuliah (nilai akhir dan/atau huruf mutu).

Jika data di atas belum lengkap, tanyakan dahulu. Jika pengguna tetap minta dihitung dengan angka contoh, beri label **[ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI]** pada seluruh hasil terkait.

## Aturan SKS dan pembagian waktu

**Selalu gunakan rumus di `calculators/kalkulator_sks_bobot.md` saat menghitung SKS dan bobot — jangan mengandalkan perkiraan.**

1. Gunakan dasar: **1 SKS = 45 jam per semester = 2.700 menit per semester**.
2. Total beban belajar mata kuliah:
   - total jam = SKS × 45;
   - total menit = SKS × 2.700.
3. Jika jumlah pertemuan efektif adalah 16, total menit per minggu/pertemuan:
   - menit per pertemuan = SKS × 2.700 / 16.
4. Pembagian TM/PT/BM wajib mengikuti kebijakan kampus jika diberikan.
5. Jika kebijakan kampus tidak diberikan, gunakan opsi default berbasis proporsi konvensional **50:60:60** untuk mata kuliah teori, tetapi normalisasi agar total tetap 45 jam/semester.
6. Untuk 1 SKS teori selama 16 pertemuan, pembagian exact berdasarkan total 2.700 menit:
   - total per pertemuan = 168,75 menit;
   - TM = 49,63 menit;
   - PT = 59,56 menit;
   - BM = 59,56 menit.
7. Untuk N SKS teori selama 16 pertemuan:
   - TM = N × 49,63 menit/pertemuan;
   - PT = N × 59,56 menit/pertemuan;
   - BM = N × 59,56 menit/pertemuan;
   - total = N × 168,75 menit/pertemuan.
8. Jika kampus menggunakan pembulatan lama 50 menit TM + 60 menit PT + 60 menit BM per SKS per minggu, tuliskan sebagai **konvensi pembulatan institusi**. Rekap totalnya = 2.720 menit/SKS/semester atau 45 jam 20 menit, sehingga harus ditandai sebagai pembulatan yang sedikit melebihi 45 jam.
9. Untuk mata kuliah praktik/praktikum/studio/lapangan/proyek, jangan paksa TM/PT/BM teori. Tanyakan distribusi kampus; jika tidak ada, buat draf distribusi berdasarkan bentuk pembelajaran dan beri label usulan.
10. RPS wajib memuat rekap:
    - total jam/menit per semester;
    - total TM;
    - total PT;
    - total BM;
    - total praktik/proyek/lapangan jika ada;
    - pemeriksaan bahwa total = SKS × 45 jam.

## Aturan bobot penilaian

**Selalu gunakan rumus di `calculators/kalkulator_sks_bobot.md` saat menghitung bobot — jangan mengandalkan perkiraan.**

1. Total bobot asesmen wajib **100%**.
2. Bobot tidak boleh dibagi rata tanpa alasan.
3. Bobot ditentukan dari:
   - kontribusi CPMK/Sub-CPMK ke CPL;
   - level Bloom C4/C5/C6;
   - kedalaman dan keluasan bahan kajian;
   - kompleksitas bukti capaian;
   - waktu belajar yang dialokasikan;
   - bentuk pembelajaran dan asesmen;
   - kebijakan kampus jika ada.
4. Gunakan rumus kerja bila data kuantitatif tersedia:
   - Skor Bobot Sub-CPMK = Kontribusi CPL × Kompleksitas Bloom × Beban Waktu × Kompleksitas Bukti.
   - Normalisasi: Bobot Sub-CPMK = Skor Sub-CPMK / Total Skor × 100%.
5. Bobot Bloom default jika kampus tidak memberi aturan:
   - C4 = 1,00;
   - C5 = 1,15;
   - C6 = 1,30.
6. Bobot bukti capaian default:
   - kuis/refleksi analitis = 0,80–1,00;
   - tugas analisis/studi kasus = 1,00–1,15;
   - presentasi/debat/evaluasi = 1,10–1,20;
   - proyek/desain/produk = 1,20–1,40;
   - artikel/proposal/portofolio komprehensif = 1,30–1,50.
7. Setelah membuat bobot, tampilkan tabel pemeriksaan: subtotal tugas, UTS, UAS/proyek, total 100%.
8. Jika total tidak 100%, koreksi otomatis dan jelaskan normalisasi.

## Aturan penelitian dosen dalam RPS

Setiap RPS harus memiliki bagian **Penelitian Dosen sebagai Referensi Pendukung**, dan datanya **wajib diminta di awal (Langkah Pertama, lihat bagian A di atas)**, sebelum bagian RPS lain disusun — bukan ditanyakan menjelang dokumen selesai.

Data minimum yang harus diminta:

- nama dosen/peneliti;
- judul penelitian/publikasi;
- tahun;
- jenis sumber: jurnal, prosiding, laporan penelitian, buku, HKI, paten, produk, dataset, aplikasi, modul;
- DOI/URL/identitas sumber jika ada;
- relevansi dengan CPL/CPMK/Sub-CPMK;
- minggu/pertemuan yang menggunakan referensi tersebut;
- bentuk integrasi ke pembelajaran: studi kasus, data latihan, tugas, proyek, diskusi, demonstrasi, atau bahan bacaan.

Jika pengguna belum memiliki penelitian dosen, tanyakan. Jangan membuat judul palsu. Boleh memberi placeholder: "Penelitian dosen: [diisi oleh dosen/prodi]".

## Matriks yang wajib tersedia sesuai kebutuhan

Saat membuat kurikulum/RPS/perangkat, gunakan matriks berikut sesuai cakupan permintaan:

1. Profil Lulusan – CPL.
2. CPL – Bahan Kajian.
3. Bahan Kajian – Mata Kuliah.
4. CPL – Mata Kuliah.
5. Mata Kuliah – CPMK.
6. CPL – CPMK – Sub-CPMK.
7. Sub-CPMK – Bahan Kajian/Materi – Aktivitas – Asesmen.
8. RPS Mingguan.
9. SKS – Jam Belajar – TM/PT/BM.
10. Bobot Penilaian – CPMK/Sub-CPMK – Bukti Capaian.
11. Rubrik Analitik.
12. Penelitian Dosen – CPMK/Sub-CPMK – Pertemuan.
13. Struktur Kurikulum per Semester.
14. CPL – Asesmen Program.
15. PPEPP Kurikulum/Pembelajaran.
16. Audit Kesesuaian OBE.
17. Evaluasi Ketercapaian CPMK – Nilai – Kelulusan Mata Kuliah.
18. Roadmap Pembelajaran (Format WBI) – Modul – Pertemuan – Output/Concept/Indicators/Learning Strategy.

## Format respons ketika data kurang

Gunakan pola berikut:

"Bisa saya buatkan, tetapi agar tidak mengarang dan tetap sesuai OBE/SN-Dikti 2025, mohon lengkapi data berikut: …"

Jika pengguna meminta lanjut tanpa data lengkap:

"Saya buatkan draf usulan awal. Bagian yang belum memiliki acuan saya beri tanda [PERLU VALIDASI PRODI/KAMPUS]."

## Format audit dokumen

Saat memperbaiki dokumen, gunakan tabel:

| Bagian | Kondisi Saat Ini | Masalah | Revisi yang Disarankan | Alasan/Acuan | Status |
|---|---|---|---|---|---|

Kemudian tambahkan:

- ringkasan temuan;
- daftar data yang belum tersedia;
- risiko jika digunakan tanpa revisi;
- versi revisi siap pakai.

## Struktur folder kerja (mode Cowork/Project)

Kalau skill ini dijalankan dengan akses ke folder kerja pengguna (mode Cowork, atau Project yang tersambung ke folder/Project Knowledge), gunakan struktur folder baku berikut — jangan minta pengguna membuat folder manual satu per satu:

- Satu **folder utama** (mis. "Akademik 2026") yang disiapkan sekali oleh pengguna.
- Folder **`Peraturan & Kurikulum/`** khusus untuk dokumen lintas mata kuliah (kurikulum prodi, SK, regulasi, dll) — bukan per mata kuliah.
- Satu **folder per mata kuliah**, berisi dokumen tingkat mata kuliah (RPS, Modul, LKM, PPT) di root-nya, plus subfolder **`Pertemuan 1/`, `Pertemuan 2/`, dst.** untuk berkas spesifik per sesi.

Detail lengkap, aturan penempatan file, dan cara mengecek folder yang sudah ada: lihat `workflows/alur_struktur_folder_kerja.md`.

## Self-check wajib sebelum output akhir

Sebelum mengirimkan output akhir ke pengguna, jalankan checklist berikut dari `checklists/checklist_audit_obe.md`:

- [ ] CPL/CPMK/Sub-CPMK tidak dikarang; berbasis data pengguna atau diberi label usulan.
- [ ] Kata kerja utama hanya C4, C5, C6 (dicek dari bank_bloom_c4_c5_c6.md).
- [ ] Tidak ada label/kode Bloom (C4/C5/C6) yang tertulis eksplisit di dokumen output, kecuali diminta pengguna.
- [ ] Semua matriks alignment yang relevan sudah ada.
- [ ] Total jam = SKS × 45 jam (dihitung dari kalkulator_sks_bobot.md).
- [ ] Total bobot asesmen = 100%.
- [ ] Penelitian dosen tersedia atau ada placeholder yang jelas.
- [ ] Catatan validasi ada di bagian akhir dokumen.
- [ ] Dokumen diberi label status: Final atau Draf Usulan.
- [ ] Acuan regulasi terkini: Permendiktisaintek No. 39/2025 jo. No. 10/2026 (bukan Permendikbudristek No. 53/2023).
- [ ] Jika mode Evaluasi Ketercapaian CPMK: NBM, target klasikal, dan ambang kelulusan berasal dari data pengguna atau diberi label [ASUMSI DEFAULT - PERLU VALIDASI PRODI/SPMI], bukan dikarang.

Jika ada item yang gagal, perbaiki sebelum output dikirim. Jika tidak memungkinkan karena data dari pengguna kurang, tambahkan ke Catatan Validasi.

## File pendukung skill

Gunakan file dalam folder ini sesuai instruksi berikut:

| File | Kapan digunakan |
|---|---|
| `references/acuan_regulasi_obe.md` | Saat merumuskan CPL atau memvalidasi kesesuaian regulasi |
| `references/aturan_sks_tm_pt_bm.md` | Saat membahas pembagian waktu belajar TM/PT/BM |
| `references/bank_bloom_c4_c5_c6.md` | **Wajib dibuka** saat merumuskan CPL, CPMK, atau Sub-CPMK |
| `workflows/alur_anti_ngarang.md` | Saat ragu apakah data boleh diasumsikan atau harus diminta |
| `workflows/alur_rps_obe.md` | Panduan langkah-langkah menyusun RPS OBE |
| `workflows/alur_export_rps_wbi_siakad.md` | **Wajib diikuti** saat mengekspor RPS ke format Rincian Pertemuan WBI/SIAKAD (`.xlsx`) |
| `workflows/alur_kurikulum_obe.md` | Panduan langkah-langkah menyusun kurikulum OBE |
| `workflows/alur_struktur_folder_kerja.md` | **Wajib diikuti** saat mode Cowork/Project punya akses folder kerja — struktur folder utama, Peraturan & Kurikulum, per mata kuliah, per Pertemuan |
| `calculators/kalkulator_sks_bobot.md` | **Wajib digunakan** saat menghitung SKS, TM/PT/BM, dan bobot asesmen |
| `workflows/alur_evaluasi_cpmk.md` | Panduan langkah-langkah evaluasi ketercapaian CPMK berdasarkan nilai dan kelulusan |
| `calculators/kalkulator_evaluasi_cpmk.md` | **Wajib digunakan** saat menghitung nilai CPMK, ketercapaian individual/klasikal, dan kelulusan mata kuliah |
| `templates/template_evaluasi_cpmk.md` | Template output evaluasi ketercapaian CPMK dalam bentuk naratif/markdown (rekap nilai, ketercapaian klasikal, kelulusan, RTL) |
| `templates/template_evaluasi_cpmk_bobot_asesmen.xlsx` | Template Excel formula-otomatis: Bobot CPMK (bobot instrumen→Sub-CPMK→CPMK) — Peta CPL — Input Nilai — Nilai CPMK per Mahasiswa — Dashboard Evaluasi Ketercapaian CPMK. Salin dulu sebelum diisi, jangan edit file asli |
| `workflows/alur_roadmap_wbi.md` | Panduan lengkap membangun Roadmap Pembelajaran format WBI (pengelompokan Modul dari Sub-CPMK, isi tiap sel, format visual) — hanya dipakai setelah Sub-CPMK mata kuliah tersusun |
| `templates/template_rps_obe_ketat.md` | Template output RPS OBE (naratif/markdown) |
| `templates/template_rps_wbi_rincian_pertemuan.xlsx` | Template resmi Excel Rincian Pertemuan SIAKAD WBI — jangan diedit langsung, salin dulu sebelum diisi |
| `templates/template_matriks_obe_lengkap.md` | Template semua matriks OBE |
| `templates/template_kurikulum_obe.md` | Template dokumen kurikulum OBE |
| `templates/template_penelitian_dosen_rps.md` | Template tabel penelitian dosen di RPS |
| `checklists/checklist_audit_obe.md` | **Wajib dijalankan** sebagai self-check sebelum output akhir |
