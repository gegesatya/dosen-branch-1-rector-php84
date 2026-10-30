# LAPORAN PERBAIKAN SECTION-AWARE DESCRIPTION PARSER (PHASE 1.5)
**Modul:** RPS Generator — Curriculum Course Description Parser  
**Tanggal:** 18 September 2026  
**Status:** COMPLETED  
**Verdict:** `DESCRIPTION_PARSER_PASS`

---

## 1. LATAR BELAKANG PERBAIKAN

Hasil audit sebelumnya mengidentifikasi adanya *false-negative* (deskripsi resmi tersedia di dokumen kurikulum namun gagal diekstrak) dan potensi *false-positive* (ekstraksi mengambil teks dari section Bahan Kajian atau tabel silabus).

Perbaikan difokuskan secara presisi pada **Section-Aware Description Parser** di `C_rps_generator.php` tanpa mengubah logika CPL parser, validator Taksonomi Bloom, maupun arsitektur controller lainnya.

---

## 2. ARSITEKTUR & ATURAN PARSER TERBARU

### 2.1 Boundary Section per Program Studi

Parser tidak lagi melakukan pencarian global yang rentan tabrakan teks, melainkan mengisolasi pencarian di dalam batas heading resmi:

| Program Studi | Heading Mulai (START) | Heading Berhenti (STOP) | Metadata `source_section` |
|---|---|---|---|
| **MPI** | `Deskripsi Dan Capaian Pembelajaran Mata Kuliah` | `13. Implementasi MBKM` / `BAB...` | `MPI_DESCRIPTION_CPMK_SECTION` |
| **Akuntansi Perpajakan (AP)** | `URAIAN RINGKAS DESKRIPSI MATA KULIAH` | `XV. MEKANISME KEBIJAKAN KAMPUS MERDEKA` / `BAB...` | `AP_URAIAN_RINGKAS` |
| **PKA** | `URAIAN RINGKAS DESKRIPSI MATA KULIAH` | `1. KODE PRODI` / `SISTEM KODE` / `BAB...` | `PKA_URAIAN_RINGKAS` |
| **Agribisnis Hortikultura (AH)** | `URAIAN RINGKAS DESKRIPSI MATA KULIAH` | `1. Penilaian pembelajaran` / `BAB...` | `AH_URAIAN_RINGKAS` |
| **TRPL** | *Tidak ada section deskripsi resmi* | — | `NOT_FOUND` |

---

### 2.2 Normalized Matching & Toleransi Konversi Dokumen

Untuk menangani variasi format PDF/Markdown (seperti pemenggalan kata/line-break dan tanda baca):
1. **Normalisasi String:** Mengonversi string nama mata kuliah ke format seragam (huruf kecil, membuang spasi berlebih dan karakter non-alfanumerik).
2. **Kamus Alias Terkontrol:** Menangani sinonim baku WBI (misal: *Riset Pemasaran* $\leftrightarrow$ *Penelitian Pemasaran*, *Perpajakan Internasional* $\leftrightarrow$ *Pengantar Perpajakan Internasional*, *Pemasaran Internasional* $\leftrightarrow$ *Manajemen Pemasaran Global*).
3. **Multi-line Title Handling:** Menggabungkan baris judul yang terpotong line-break sebelum memisahkan paragraf deskripsi.

---

### 2.3 Logika Ekstraksi Struktur per Dokumen

1. **MPI (`MPI_DESCRIPTION_CPMK_SECTION`):**
   - Mendeteksi baris nama mata kuliah.
   - Mengambil blok teks deskripsi (diawali `Mata kuliah ini...` / paragraf pengantar).
   - Berhenti tepat sebelum blok Capaian Pembelajaran Mata Kuliah (CPMK) yang diawali `Mampu...` atau `Mahasiswa mampu...`.
2. **AP (`AP_URAIAN_RINGKAS`):**
   - Mendeteksi penomoran baris tabular (`\n1 ...`, `\n2 ...`).
   - Mengambil deskripsi mata kuliah setelah nama mata kuliah dan berhenti pada nomor entry berikutnya.
   - Membersihkan artefak halaman seperti `| P a g e`.
3. **PKA (`PKA_URAIAN_RINGKAS`):**
   - Mendeteksi kode `P K A \d...` atau `PKA\d...`.
   - Mengambil baris judul multi-line lalu mengekstrak paragraf deskripsi hingga kode MK berikutnya, mengabaikan baris catatan referensi tambahan.
4. **AH (`AH_URAIAN_RINGKAS`):**
   - Mendeteksi kode `AGH\d...`.
   - Mengekstrak nama mata kuliah dan paragraf deskripsi yang berakhir sebelum notasi SKS (misal: `3 (2-1)`).
5. **TRPL (`NOT_FOUND`):**
   - TRPL tidak memiliki section deskripsi resmi (hanya Bahan Kajian per MK).
   - Sistem secara deterministik mengembalikan `NOT_FOUND` dan tidak mengubah Bahan Kajian menjadi deskripsi resmi.

---

### 2.4 Guard Anti False-Positive

Deskripsi secara otomatis ditolak (`NOT_FOUND`) apabila:
- Mengandung heading `"BAHAN KAJIAN MATA KULIAH"` atau `"BAHAN KAJIAN DAN DAFTAR MATA KULIAH"`.
- Panjang karakter $< 25$ atau hanya berupa kode/nomor/SKS/matriks.
- Berasal dari tabel kurikulum umum tanpa konten uraian deskriptif.

---

## 3. HASIL RETEST DETERMINISTIK

Pengujian dilakukan secara deterministik pada 13 kasus uji mata kuliah dari 5 program studi:

| Program Studi | Mata Kuliah | Status Ekstraksi | `source_section` | Potongan 150 Karakter Pertama | False Positive? | False Negative? |
|---|---|:---:|---|---|:---:|:---:|
| **MPI** | Riset Pemasaran | **FOUND** | `MPI_DESCRIPTION_CPMK_SECTION` | `Mata kuliah ini menyangkut pengumpulan, perekaman dan analisis data kualitatif dan kuantitatif menyangkut pemasaran produk dan jasa untuk mengidentifi...` | NO | NO |
| **MPI** | Pemasaran Internasional | **FOUND** | `MPI_DESCRIPTION_CPMK_SECTION` | `Mata kuliah ini membahas evolusi domestik marketing ke global marketing dan pengaruhnya terhadap program dan kebijakan pemasaran. Menjelaskan pendekat...` | NO | NO |
| **MPI** | Manajemen Penjualan | **NOT_FOUND** | `MPI_DESCRIPTION_CPMK_SECTION` | `-` *(Tidak terdaftar pada section deskripsi MPI)* | NO | NO (Expected) |
| **AP** | Dasar-dasar Perpajakan | **FOUND** | `AP_URAIAN_RINGKAS` | `Mata kuliah ini membahas mengenai pengetahuan dasar perpajakan yang berlaku di Indonesia, Dasar hukum perpajakan, Jenis dan Pembagian Pajak, Sistem Pe...` | NO | NO |
| **AP** | Perpajakan Internasional | **FOUND** | `AP_URAIAN_RINGKAS` | `Mata kuliah ini akan membahas secara detail mengenai perpajakan terkait transaksi dengan pihak dari luar wilayah Indonesia, cara menghitung, membuat d...` | NO | NO |
| **AP** | Akuntansi Manajemen | **FOUND** | `AP_URAIAN_RINGKAS` | `Matakuliah ini memberikan pemahaman tentang penerapan praktik-praktik akuntansi manajemen yang mutakhir dalam rangka meningkatkan nilai korporat, Meng...` | NO | NO |
| **PKA** | General English 1 | **FOUND** | `PKA_URAIAN_RINGKAS` | `Siswa akan dapat menggunakan bahasa Inggris untuk menggambarkan kesukaan dan ketidaksukaan mereka; dapat mendiskusikan hobi dan rutinitas; aktivitas m...` | NO | NO |
| **PKA** | Pengelolaan Acara Korporasi | **FOUND** | `PKA_URAIAN_RINGKAS` | `Matakuliah ini akan memabahas tentang latar belakang dari adanya corporate event,Jenis jenis event yang termasuk kedalam corporate event, flow chart e...` | NO | NO |
| **PKA** | Hukum dan Etika Bisnis | **NOT_FOUND** | `PKA_URAIAN_RINGKAS` | `-` *(Mata kuliah resmi PKA adalah Hukum Bisnis Kepariwisataan)* | NO | NO (Expected) |
| **AH** | Bahasa Indonesia | **FOUND** | `AH_URAIAN_RINGKAS` | `Mempelajari penguasaan dan penggunaan Bahasa Indonesia secara baik. Topik yang dibahas meliputi EYD, struktur kalimat, kalimat yang efektif dan logis,...` | NO | NO |
| **AH** | Biologi | **FOUND** | `AH_URAIAN_RINGKAS` | `Biologi sebagai ilmu. Organisasi kehidupan: bahan kimia, molekul, dan organisme. Pembagian organisme dalam hubungannya dengan pertanian. Dunia tumbuha...` | NO | NO |
| **AH** | Manajemen Agribisnis | **FOUND** | `AH_URAIAN_RINGKAS` | `Membahas mengenai agribisnis dalam perspektif, mengelola agribinis, organisasi agribinis, koperasi dalam agribisnis, laporan keuangan dan analisa lapo...` | NO | NO |
| **TRPL** | Pemrograman Berbasis Framework | **NOT_FOUND** | `NOT_FOUND` | `-` *(TRPL murni berbasis Bahan Kajian per MK)* | NO | NO (Expected) |

---

## 4. KESIMPULAN & VERDICT

1. Seluruh mata kuliah yang memiliki section deskripsi resmi pada dokumen kurikulum (MPI, AP, PKA, AH) berhasil diekstrak dengan tingkat akurasi 100% tanpa kontaminasi CPMK atau Bahan Kajian.
2. Mata kuliah yang tidak memiliki section deskripsi resmi (termasuk seluruh mata kuliah TRPL yang berbasis Bahan Kajian) secara aman mengembalikan `NOT_FOUND` sehingga siap dialirkan ke fallback RPS Lama / AI proposal tanpa risiko data palsu.
3. Tidak ada modifikasi pada CPL parser, Bloom validator, atau arsitektur controller lainnya.

---

### **FINAL VERDICT:**
# `DESCRIPTION_PARSER_PASS`
