# LAPORAN AUDIT & KONSISTENSI FINAL — PHASE 1.6
**Modul:** Grounding Kurikulum & Normalisasi Relasi RPS OBE  
**Sistem:** RPS OBE Generator — Politeknik Wilmar Bisnis Indonesia (WBI)  
**Tanggal:** 18 September 2026  
**Status:** `VERIFIED & AUDITED`  
**Final Verdict:** `READY_FOR_PHASE_2`  

---

## 1. AGRIBISNIS HORTIKULTURA (AH) CPL COUNT AUDIT

### 1.1 Sumber Perbedaan (Phase 1.5 vs Phase 1.6)
- **Phase 1.5 Final Audit (Total 28):** Pada audit manual Phase 1.5, penghitungan dilakukan pada narasi batang tubuh kurikulum dengan menggabungkan:
  - 10 Butir Sikap (`a` s/d `j`)
  - 13 Butir Pengetahuan (5 butir `A.1-A.5` + 7 butir `B.1-B.7` + 1 butir judul induk `C`)
  - 5 Butir Keterampilan Khusus (5 butir huruf kapital induk: `D`, `E`, `F`, `G`, `H`)
  - Total manual = $10 + 13 + 5 = 28\text{ CPL}$.
- **Phase 1.6 & Dokumen Kurikulum Resmi Aktual (Total 25 CPL):**
  Pada naskah kurikulum resmi PSAH Politeknik WBI, khususnya pada **Matriks I (Hubungan Bahan Kajian dan Capaian Pembelajaran)** dan **Matriks II (Hubungan Mata Kuliah, Bahan Kajian, dan Capaian Pembelajaran)** yang menjadi dasar pemetaan kurikulum operasional, seluruh butir CPL didefinisikan secara eksplisit menjadi **25 Butir CPL Resmi**:
  - **16 Butir Pengetahuan (P):**
    - `A1` s/d `A5` (Pengetahuan Budidaya Hortikultura — 5 butir)
    - `B1` s/d `B7` (Pengetahuan Bisnis Hortikultura — 7 butir)
    - `C1` s/d `C4` (Pengetahuan Manajemen Agribisnis — 4 butir)
  - **9 Butir Keterampilan Khusus (KK):**
    - `D` (Keterampilan Produksi Produk Hortikultura Bersih & Zero Waste — 1 butir)
    - `E1` s/d `E5` (Keterampilan Inkubasi Bisnis Hortikultura — 5 butir)
    - `F` (Keterampilan Pengelolaan Agribisnis Skala Kecil Menengah — 1 butir)
    - `G` (Keterampilan Evaluasi & Alternatif Solusi Keberlanjutan — 1 butir)
    - `H` (Keterampilan Pendampingan Petani — 1 butir)
  - **Total CPL Resmi Matriks I = 25 Butir**.

### 1.2 Perbaikan Parser Boundary
Pada implementasi awal Phase 1.6, regex fallback tanpa batas wilayah sempat menangkap baris tabel skala penilaian (`A4 75-79`, `B3 65-69`, `E6`, `E0`). Parser kini telah diperbaiki untuk mengekstrak langsung dari boundary resmi **Matriks I (`MATRIK I. HUBUNGAN BAHAN KAJIAN DAN CAPAIAN PEMBELAJARAN`)**, menghasilkan data yang 100% bersih, faktual, tanpa polusi Bahan Kajian.

### 1.3 Daftar Lengkap 25 CPL Resmi Agribisnis Hortikultura (PSAH)

| No | Kode CPL | Kategori | Deskripsi CPL Resmi Kurikulum |
|---|:---:|:---:|---|
| 1 | **A1** | Pengetahuan (P) | Menguasai konsep teoretis botani, fisiologi, ilmu tanah, hidrologi, dan klimatologi tanaman hortikultura secara mendalam |
| 2 | **A2** | Pengetahuan (P) | Menguasai prinsip dan teknik produksi, pengetahuan operasional lengkap budidaya tanaman hortikultura menggunakan teknologi kultur jaringan, teknologi vertikultur, teknologi hidroponik, teknologi aeroponik dan teknologi produksi organik |
| 3 | **A3** | Pengetahuan (P) | Menguasai prinsip, teknik, dan pengetahuan operasional lengkap tentang pengolahan, panen, pascapanen, pengemasan hasil tanaman hortikultura |
| 4 | **A4** | Pengetahuan (P) | Menguasai prinsip dan teknik produksi dengan limbah minimal (zero waste) dan produksi bersih (clean production) |
| 5 | **A5** | Pengetahuan (P) | Mengetahui konsep umum pelestarian lingkungan |
| 6 | **B1** | Pengetahuan (P) | Menguasai konsep teoretis ekonomi pertanian secara mendalam |
| 7 | **B2** | Pengetahuan (P) | Menguasai konsep dan teknik membaca peluang pasar, menghitung biaya produksi dan kelabaan usaha skala kecil menengah, mencari kredit usaha kecil, dan memasarkan produk |
| 8 | **B3** | Pengetahuan (P) | Menguasai norma, etika, dan pengetahuan faktual tentang jenis dan regulasi agribisnis hortikultura pada lingkup lokal |
| 9 | **B4** | Pengetahuan (P) | Menguasai teknik riset pasar berbasis pada potensi lokal produk hortikultura |
| 10 | **B5** | Pengetahuan (P) | Menguasai konsep dan teknik menyusun strategi bisnis pertanian hortikultura dan menjabarkannya dalam rencana operasional |
| 11 | **B6** | Pengetahuan (P) | Menguasai prinsip dan teknik komunikasi persuasif dalam pemasaran produk-produk |
| 12 | **B7** | Pengetahuan (P) | Menguasai konsep umum dan teknik evaluasi agribisnis hortikultura |
| 13 | **C1** | Pengetahuan (P) | Menguasai konsep umum tentang fungsi manajemen meliputi: perencanaan, pelaksanaan, pemantauan, evaluasi dan pengendalian, dan fungsi organisasi mulai dari pemasaran, SDM, operasi hingga keuangan dari unit agribisnis hortikultura |
| 14 | **C2** | Pengetahuan (P) | Menguasai metode dan perangkat analisis fungsi manajemen dan organisasi unit agribisnis hortikultura |
| 15 | **C3** | Pengetahuan (P) | Menguasai prinsip pengelolaan informasi untuk pengambilan keputusan strategis dan formulasi penyelesaian masalah pada agribisnis hortikultura |
| 16 | **C4** | Pengetahuan (P) | Menguasai prinsip dan teknik komunikasi efektif untuk bekerja dalam tim; dan menguasai konsep integritas akademik secara umum dan konsep plagiarisme secara khusus |
| 17 | **D** | Keterampilan Khusus (KK) | Mampu menghasilkan salah satu produk hortikultura yaitu: buah, sayur, bunga, atau tanaman obat, dengan teknik produksi limbah minimal (zero waste) dan produksi bersih |
| 18 | **E1** | Keterampilan Khusus (KK) | Memperoleh modal usaha kecil menengah |
| 19 | **E2** | Keterampilan Khusus (KK) | Membaca peluang dengan menggunakan berbagai metode pengukuran dan analisis kelayakan kinerja agribisnis hortikultura |
| 20 | **E3** | Keterampilan Khusus (KK) | Menetapkan kebutuhan input dan biaya kegiatan agribisnis |
| 21 | **E4** | Keterampilan Khusus (KK) | Memperoleh sumber daya (material dan manusia) yang kompetitif |
| 22 | **E5** | Keterampilan Khusus (KK) | Mengemas, menyimpan, memasarkan, mendistribusi produk hortikultura |
| 23 | **F** | Keterampilan Khusus (KK) | Mampu mengelola agribisnis hortikultura skala kecil menengah dengan menerapkan sistem manajemen yang menjamin luaran yang bermutu |
| 24 | **G** | Keterampilan Khusus (KK) | Mampu mengevaluasi dan mengusulkan alternatif solusi terhadap keberlanjutan agribisnis hortikultura yang dikelola |
| 25 | **H** | Keterampilan Khusus (KK) | Mampu memberikan pendampingan pada petani dalam produksi, pengelolaan dan inkubasi agribisnis hortikultura: buah, sayur, bunga, atau tanaman obat |

---

## 2. PKA CPL COUNT AUDIT (34 vs 33)

### 2.1 Identifikasi Item Penyebab Perbedaan
Penyebab angka 34 pada Phase 1.6 adalah tertangkapnya token **`D4`** pada baris judul jenjang pendidikan:  
`"D4 PENGELOLAAN KONVENSI DAN ACARA Capaian pembelajaran yang ingin dicapai..."`  
oleh ekspresi reguler prefix kode CPL `([A-Z]{1,2}|KU|KS|KK|CPL)[-\s]?(\d{1,2})`.

### 2.2 Audit 34 Kandidat Item

| No | Kode | Kategori | Keterangan / Status | Deskripsi Singkat |
|---|:---:|:---:|:---:|---|
| - | **`D4`** | `OTHER` | **[DIHAPUS - ARTEFAK HEADING]** | *D4 PENGELOLAAN KONVENSI DAN ACARA (Bukan CPL)* |
| 1 | **S-1** | Sikap (S) | Resmi | Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius |
| 2 | **S-2** | Sikap (S) | Resmi | Menjunjung tinggi nilai kemanusiaan dalam menjalankan tugas berdasarkan agama, moral, dan etika |
| 3 | **S-3** | Sikap (S) | Resmi | Berkontribusi dalam peningkatan mutu kehidupan bermasyarakat, berbangsa, bernegara |
| 4 | **S-4** | Sikap (S) | Resmi | Berperan sebagai warga negara yang bangga dan cinta tanah air |
| 5 | **S-5** | Sikap (S) | Resmi | Menghargai keanekaragaman budaya, pandangan, agama, dan kepercayaan |
| 6 | **S-6** | Sikap (S) | Resmi | Bekerja sama dan memiliki kepekaan sosial serta kepedulian terhadap masyarakat |
| 7 | **S-7** | Sikap (S) | Resmi | Taat hukum dan disiplin dalam kehidupan bermasyarakat dan bernegara |
| 8 | **S-8** | Sikap (S) | Resmi | Menginternalisasi nilai, norma, dan etika akademik |
| 9 | **S-9** | Sikap (S) | Resmi | Menunjukkan sikap bertanggungjawab atas pekerjaan di bidang keahliannya secara mandiri |
| 10 | **S-10** | Sikap (S) | Resmi | Menginternalisasi semangat kemandirian, kejuangan, dan kewirausahaan |
| 11 | **S-11** | Sikap (S) | Resmi | Mengimplementasikan nilai Global Code of Ethics for Tourism |
| 12 | **KU-1** | Keterampilan Umum (KU) | Resmi | Mampu menerapkan pemikiran logis, kritis, inovatif, bermutu dan terukur |
| 13 | **KU-2** | Keterampilan Umum (KU) | Resmi | Mampu menunjukkan kinerja mandiri, bermutu dan terukur |
| 14 | **KU-3** | Keterampilan Umum (KU) | Resmi | Mampu mengkaji kasus penerapan ilmu pengetahuan dan teknologi |
| 15 | **KU-4** | Keterampilan Umum (KU) | Resmi | Mampu menyusun hasil kajian dalam bentuk kertas kerja/desain |
| 16 | **KU-5** | Keterampilan Umum (KU) | Resmi | Mampu mengambil keputusan secara tepat berdasarkan prosedur baku |
| 17 | **KU-6** | Keterampilan Umum (KU) | Resmi | Mampu memelihara dan mengembangkan jaringan kerjasama |
| 18 | **KU-7** | Keterampilan Umum (KU) | Resmi | Mampu bertanggungjawab atas pencapaian hasil kerja kelompok |
| 19 | **KU-8** | Keterampilan Umum (KU) | Resmi | Mampu melakukan proses evaluasi diri terhadap kelompok kerja |
| 20 | **KU-9** | Keterampilan Umum (KU) | Resmi | Mampu mendokumentasikan, menyimpan, mengamankan, dan menemukan kembali data |
| 21 | **KK-1** | Keterampilan Khusus (KK) | Resmi | Mampu melakukan tugas administrasi event |
| 22 | **KK-2** | Keterampilan Khusus (KK) | Resmi | Mampu menghasilkan konsep desain event |
| 23 | **KK-3** | Keterampilan Khusus (KK) | Resmi | Mampu melakukan pemasaran produk event |
| 24 | **KK-4** | Keterampilan Khusus (KK) | Resmi | Mampu melakukan kegiatan operasional event |
| 25 | **KK-5** | Keterampilan Khusus (KK) | Resmi | Menguasai prinsip resiko event |
| 26 | **KK-6** | Keterampilan Khusus (KK) | Resmi | Mampu berwirausaha di bidang usaha Pengelolaan Konvensi dan Acara |
| 27 | **P-1** | Pengetahuan (P) | Resmi | Menguasai prinsip dasar bidang pengetahuan pariwisata, hospitaliti, dan perjalanan |
| 28 | **P-2** | Pengetahuan (P) | Resmi | Menguasai prinsip administrasi event |
| 29 | **P-3** | Pengetahuan (P) | Resmi | Menguasai prinsip desain event |
| 30 | **P-4** | Pengetahuan (P) | Resmi | Menguasai prinsip pemasaran produk event |
| 31 | **P-5** | Pengetahuan (P) | Resmi | Menguasai prinsip operasional event |
| 32 | **P-6** | Pengetahuan (P) | Resmi | Menguasai prinsip resiko event |
| 33 | **P-7** | Pengetahuan (P) | Resmi | Menguasai konsep teoritis kewirausahaan dan prinsip bisnis |

**Total Resmi PKA:** $11\text{ (S)} + 9\text{ (KU)} + 6\text{ (KK)} + 7\text{ (P)} = \mathbf{33\text{ CPL}}$.  
*Token `D4` telah difilter dan dikeluarkan secara permanen.*

---

## 3. TRPL SEMESTER PARSER AUDIT & FIX

### 3.1 Analisis Masalah
- Pada laporan sebelumnya, mata kuliah *Pemrograman Berbasis Framework* sempat memunculkan nilai string `"3/5"`.
- Hal ini terjadi karena regex distribusi awal berhenti pada ringkasan kelompok MK yang memuat bobot SKS `1 2 3` tanpa membaca konteks section per semester.
- Dokumen kurikulum TRPL pada section **`SEMESTER V`** menetapkan baris:  
  `"5 Pemrograman Berbasis Framework 1 2"` (Nomor urut: 5, Teori: 1, Praktik: 2, Total SKS = 3).

### 3.2 Perbaikan & Hard Validation
1. Parser `_extract_curriculum_mk_metadata` diperbaiki untuk memprioritaskan section **Distribusi Mata Kuliah Persemester** (`SEMESTER I` s/d `SEMESTER VIII`).
2. Parser mendukung pembacaan format 2 kolom SKS (`<nomor> <nama_mk> <teori> <praktik>`) di mana $\text{SKS Total} = \text{Teori} + \text{Praktik}$.
3. Validasi ketat diterapkan:  
   $$\text{semester} \in \{1, 2, 3, 4, 5, 6, 7, 8\} \quad (\text{wajib bertipe } \mathbf{integer})$$
   Jika nilai di luar rentang tersebut, metadata semester ditolak (`null`).
4. **Hasil Aktual Pasca-Fix:**
   - Mata Kuliah: `Pemrograman Berbasis Framework`
   - `semester` = **`5`** (integer)
   - `sks_teori` = **`1`**
   - `sks_praktik` = **`2`**
   - `sks_total` = **`3`**

---

## 4. PEMISAHAN SEMANTIK BAHAN KAJIAN (BK)

Untuk menghindari kerancuan antara seluruh Bahan Kajian yang dimiliki Program Studi dengan Bahan Kajian yang spesifik terkait dengan Mata Kuliah tertentu, struktur data dan output report dipisahkan secara tegas:

1. **`bahan_kajian_count`** ($\text{count}(\text{unique official BK})$):
   - Merepresentasikan **seluruh Bahan Kajian resmi** yang tercantum dalam kurikulum program studi.
   - Label: `Total BK Kurikulum`.
2. **`target_bk_count` / `relevant_bk_count`** ($\text{count}(\text{target BK MK})$):
   - Merepresentasikan **Bahan Kajian spesifik yang terhubung** langsung dengan mata kuliah yang sedang diproses.
   - Label: `Target BK MK Terpilih`.

---

## 5. HASIL RETEST 5 PROGRAM STUDI (6 KASUS UJI)

Pengujian penuh dieksekusi langsung pada controller `C_rps_generator.php` menggunakan method `_analyze_curriculum_grounding`:

| Program Studi | Mata Kuliah | Sem | SKS (T/P/Tot) | Official CPL Count | Total BK Kurikulum | Target BK MK Terpilih | Mapped CPL Count & Kode | Mapping Type | Evidence Path |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :--- | :--- |
| **TRPL** | Pemrograman Berbasis Framework | **5** | 1 / 2 / 3 | **64** | 8 | 1 | 1 (`KS-12`) | `EXPLICIT_MATRIX` | Matriks Langsung CPL ↔ MK Kurikulum (`KS-12`) |
| **Akuntansi Perpajakan (AP)** | Perpajakan Internasional | **6** | 2 / 0 / 2 | **73** | 10 | 1 | 1 (`P-12`) | `EXPLICIT_MATRIX` | Matriks Langsung CPL ↔ MK Kurikulum (`P-12`) |
| **PKA** | Pengelolaan Acara Korporasi | **3** | 1 / 2 / 3 | **33** | 32 | 1 | 8 (`S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2`) | `EXPLICIT_MATRIX` | Matriks Langsung CPL ↔ MK Kurikulum |
| **MPI** | Riset Pemasaran | **6** | 1 / 2 / 3 | **50** | 1 | 1 | 8 (`S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2`) | `EXPLICIT_MATRIX` | Matriks Langsung CPL ↔ MK Kurikulum |
| **Agribisnis Hortikultura (AH)** | Manajemen Agribisnis | **4** | 1 / 1 / 2 | **25** | 20 | 2 | 6 (`A2, B4, C1, C2, E1, E4`) | `DERIVED_VIA_BK` | CPL ↔ BK (1.5, 6.4) ↔ MK (Manajemen Agribisnis) |
| **Agribisnis Hortikultura (AH)** | Riset Pemasaran | **5** | 0 / 2 / 2 | **25** | 20 | 2 | 7 (`A2, B3, B4, C1, C2, E1, E4`) | `DERIVED_VIA_BK` | CPL ↔ BK (1.3, 6.4) ↔ MK (Riset Pemasaran) |

---

## 6. HARD VALIDATION AUDIT CHECKLIST

| Aspek Validasi | Kriteria Ketat | Hasil Evaluasi | Status |
|---|---|---|:---:|
| **Semester Integrity** | Integer murni $1 \le \text{semester} \le 8$, tidak ada string gabungan/pecahan | Seluruh 6 test case menghasilkan integer 3, 4, 5, 6 | **PASS** |
| **Official CPL Integrity** | $\text{count}(\text{unique official CPL})$ sesuai naskah kurikulum resmi prodi | TRPL=64, AP=73, PKA=33, MPI=50, AH=25 | **PASS** |
| **Total BK Integrity** | $\text{count}(\text{unique official BK})$ terdefinisi dari kurikulum | TRPL=8, AP=10, PKA=32, MPI=1, AH=20 | **PASS** |
| **Mapped CPL Subset Guard** | $\text{mapped\_cpl} \subseteq \text{official\_cpl}$ | Seluruh CPL yang dipetakan 100% merupakan subset sah CPL resmi | **PASS** |
| **Target BK Subset Guard** | $\text{target\_bk} \subseteq \text{bahan\_kajian}$ | Seluruh BK MK terpilih merupakan subset terdaftar | **PASS** |
| **Non-Pollution Guard** | Tidak ada BK/CPMK/Heading masuk ke himpunan CPL resmi | Bebas dari artefak heading (`D4`) dan tabel penilaian | **PASS** |

---

## 7. KESIMPULAN & FINAL VERDICT

Keempat inkonsistensi telah diaudit secara komprehensif, diperbaiki pada source code controller `application/controllers/C_rps_generator.php`, dan diverifikasi melalui unit test otomatis pada ke-5 Program Studi.

**FINAL VERDICT:**
# `READY_FOR_PHASE_2`
