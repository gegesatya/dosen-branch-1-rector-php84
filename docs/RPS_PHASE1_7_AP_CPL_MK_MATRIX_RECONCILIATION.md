# PHASE 1.7 — AKUNTANSI PERPAJAKAN CPL-MK MATRIX RECONCILIATION & COURSE CROSSWALK REPORT
**Dokumen Laporan Audit, Rekonsiliasi, dan Crosswalk Kurikulum D4 Akuntansi Perpajakan (AP)**
*Tanggal Audit: 19 September 2026*

---

## 1. Executive Summary & Problem Resolution (Why 47 vs 50 vs 54)

Pada audit kurikulum Program Studi **D4 Akuntansi Perpajakan (AP)** ditemukan inkonsistensi jumlah mata kuliah awal antara tiga representasi:
- **Current Normalized**: 47 Mata Kuliah
- **Section XI & XIII (Struktur & Distribusi Matakuliah)**: 50 Mata Kuliah (Total 148 SKS)
- **Section X (Matriks CPL-MK)**: 54 Kolom Mata Kuliah (`MK1 ... MK54`)

### Resolusi dan Kesimpulan Faktual:
1. **Penyebab 47 vs 50 (3 Missing Normalized Courses)**: Parser awal melewatkan 3 mata kuliah akibat nama multi-baris / batas parsing:
   - `402273` - Sistem Informasi Akuntansi dan Perpajakan (SMT 4, 3 SKS)
   - `502363` - Akuntansi Ekuitas dan Pelaporan Keuangan (SMT 5, 3 SKS)
   - `588402` - Mengelola Pertumbuhan Bisnis & Pengembangan Bisnis Global (SMT 5, 2 SKS)
   *Ketiga mata kuliah ini telah dikembalikan secara lengkap sehingga total katalog aktif menjadi tepat **50 Mata Kuliah (148 SKS)**.*
2. **Penyebab 50 vs 54 (4 Additional Matrix Columns)**: Matriks CPL-MK perancangan memuat kolom `MK1..MK54`. Kolom `MK1..MK48` memetakan secara sekuensial seluruh 48 mata kuliah semester 1 s.d. 6. Kolom `MK49..MK52` adalah modul perancangan pra-konsolidasi yang pada kurikulum final diintegrasikan ke dalam:
   - `MK53` / Kode `7025320` (**Pemagangan**, 20 SKS, Semester 7)
   - `MK54` / Kode `802546` (**Tugas Akhir**, 6 SKS, Semester 8)
3. **Keputusan Authoritative Final Catalog**: **50 Mata Kuliah Aktif (148 SKS)** dengan 100% CPL terpetakan secara deterministik dari Matriks CPL-MK resmi.

---

## 2. Source-by-Source Course Counts

| Section Dokumen Kurikulum | Judul Bagian | Halaman PDF | Jumlah MK | Total SKS | Status Audit |
| :--- | :--- | :---: | :---: | :---: | :--- |
| **Section IX** | Pembentukan Matakuliah | Hal. 24–25 | 50 MK | 148 SKS | 100% Sesuai |
| **Section X** | Matriks CPL dengan Matakuliah | Hal. 27–28 | 54 Kolom | — | 194 Relasi CPL-MK ('V') |
| **Section XI** | Struktur Matakuliah | Hal. 29–30 | 50 MK | 148 SKS | 100% Sesuai |
| **Section XIII** | Distribusi Matakuliah Persemester | Hal. 31–32 | 50 MK | 148 SKS | 100% Sesuai |
| **Current Normalized (Lama)** | Normalized JSON (Sebelum Audit) | — | 47 MK | 140 SKS | **3 MK Hilang (Perlu Perbaikan)** |
| **Authoritative Normalized (Baru)** | Reconciled Active Catalog | — | **50 MK** | **148 SKS** | **100% VERIFIED** |

---

## 3. Root Cause Analysis: 3 Missing Normalized Courses

| Kode MK | Nama Mata Kuliah Resmi | Semester | SKS Teori | SKS Praktik | SKS Total | BK Induk | Akar Masalah & Tindakan Koreksi |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| **`402273`** | Sistem Informasi Akuntansi dan Perpajakan | 4 | 2 | 1 | 3 | BK-5 | Terlewat di antara baris 402262 dan 402283. Telah direstorasi dan dipetakan ke **MK27**. |
| **`502363`** | Akuntansi Ekuitas dan Pelaporan Keuangan | 5 | 2 | 1 | 3 | BK-1 | Terlewat di antara baris 502353 dan 502373. Telah direstorasi dan dipetakan ke **MK36**. |
| **`588402`** | Mengelola Pertumbuhan Bisnis & Pengembangan Bisnis Global | 5 | 1 | 1 | 2 | BK-13 | Nama multi-baris pada tabel distribusi. Telah direstorasi dan dipetakan ke **MK40**. |

---

## 4. MK1..MK54 Deterministic Crosswalk Table

| Matrix No | Kode MK | Nama Mata Kuliah Resmi | SMT | SKS | CPL Terpetakan dari Matriks Resmi ('V') | Metoda Crosswalk |
| :---: | :---: | :--- | :---: | :---: | :--- | :---: |
| **MK1** | `100012` | Agama | 1 | 2 | S-1 | SEQUENTIAL_CODE_MATCH |
| **MK2** | `100022` | Pancasila | 1 | 2 | S-3 | SEQUENTIAL_CODE_MATCH |
| **MK3** | `101032` | Berpikir Kritis | 1 | 2 | KU-1 | SEQUENTIAL_CODE_MATCH |
| **MK4** | `102042` | Dasar-dasar Manajemen | 1 | 2 | KU-6 | SEQUENTIAL_CODE_MATCH |
| **MK5** | `102053` | Bahasa Inggris | 1 | 3 | KU-13 | SEQUENTIAL_CODE_MATCH |
| **MK6** | `102063` | Akuntansi usaha jasa | 1 | 3 | S-9, S-11, P-3, KU-2, KK-2 | SEQUENTIAL_CODE_MATCH |
| **MK7** | `102073` | Dasar-dasar Perpajakan | 1 | 3 | S-9, P-7, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK8** | `188082` | Pola Pikir Kewirausahaan | 1 | 2 | S-10, P-28, KU-2, KK-21 | SEQUENTIAL_CODE_MATCH |
| **MK9** | `202092` | Matematika Keuangan dan Bisnis | 2 | 2 | KU-1 | SEQUENTIAL_CODE_MATCH |
| **MK10** | `202102` | Teori Ekonomi | 2 | 2 | P-1 | SEQUENTIAL_CODE_MATCH |
| **MK11** | `200112` | Bahasa Indonesia | 2 | 2 | S-4 | SEQUENTIAL_CODE_MATCH |
| **MK12** | `201122` | Seni dan Olahraga | 2 | 2 | S-5 | SEQUENTIAL_CODE_MATCH |
| **MK13** | `202133` | Akuntansi usaha dagang | 2 | 3 | S-9, S-11, P-3, KU-2, KK-2 | SEQUENTIAL_CODE_MATCH |
| **MK14** | `202143` | Aplikasi komputer | 2 | 3 | S-9, P-23, KU-12, KK-19 | SEQUENTIAL_CODE_MATCH |
| **MK15** | `202153` | PPh dan Potput PPh | 2 | 3 | S-9, P-7, KU-2, KK-4 | SEQUENTIAL_CODE_MATCH |
| **MK16** | `288163` | Pengembangan Model Bisnis | 2 | 3 | S-10, P-28, KU-2, KK-21 | SEQUENTIAL_CODE_MATCH |
| **MK17** | `302172` | Statistika | 3 | 2 | P-24, KU-1 | SEQUENTIAL_CODE_MATCH |
| **MK18** | `302182` | Retribusi dan Pajak Daerah | 3 | 2 | S-9, P-8, KU-2, KK-5 | SEQUENTIAL_CODE_MATCH |
| **MK19** | `300192` | Kewarganegaraan | 3 | 2 | S-7 | SEQUENTIAL_CODE_MATCH |
| **MK20** | `302203` | Komputerisasi Akuntansi | 3 | 3 | S-9, S-11, P-4, KU-2, KK-1 | SEQUENTIAL_CODE_MATCH |
| **MK21** | `302213` | Akuntansi Aset | 3 | 3 | S-9, S-11, P-4, KU-2, KK-1 | SEQUENTIAL_CODE_MATCH |
| **MK22** | `302223` | Akuntansi biaya | 3 | 3 | S-9, S-11, P-15, KU-2, KK-12, KK-13 | SEQUENTIAL_CODE_MATCH |
| **MK23** | `302233` | PPN dan PPnBM | 3 | 3 | S-9, P-7, KU-2, KK-3 | SEQUENTIAL_CODE_MATCH |
| **MK24** | `388243` | Perintisan Bisnis | 3 | 3 | S-10, P-28, KU-2, KK-21 | SEQUENTIAL_CODE_MATCH |
| **MK25** | `401251` | Sikap Religius | 4 | 1 | S-2 | SEQUENTIAL_CODE_MATCH |
| **MK26** | `402262` | Manajemen keuangan | 4 | 2 | S-9, S-11, P-14, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK27** | `402273` | Sistem Informasi Akuntansi dan Perpajakan | 4 | 3 | S-9, S-11, P-21, KU-2, KK-18 | RESTORED_SEQUENTIAL_MATCH |
| **MK28** | `402283` | Manajemen Pajak | 4 | 3 | S-9, S-11, P-23, KU-2, KK-19 | SEQUENTIAL_CODE_MATCH |
| **MK29** | `402293` | Akuntansi Manajemen | 4 | 3 | S-9, S-11, P-16, P-17, P-18, KU-2, KK-14, KK-15, KK-16 | SEQUENTIAL_CODE_MATCH |
| **MK30** | `402303` | Akuntansi Instrumen Keuangan | 4 | 3 | S-9, S-11, P-4, KU-2, KK-1 | SEQUENTIAL_CODE_MATCH |
| **MK31** | `402313` | Akuntansi Perpajakan | 4 | 3 | S-9, S-11, P-5, KU-2, KU-6, KU-7, KK-6, KK-7 | SEQUENTIAL_CODE_MATCH |
| **MK32** | `488323` | Inovasi dan Pertumbuhan | 4 | 3 | S-10, P-28, KU-2, KK-21 | SEQUENTIAL_CODE_MATCH |
| **MK33** | `502333` | Akuntansi Penggabungan Usaha | 5 | 3 | S-9, S-11, P-3, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK34** | `502342` | Analisa laporan keuangan | 5 | 2 | S-9, S-11, P-6, KU-2, KK-2 | SEQUENTIAL_CODE_MATCH |
| **MK35** | `502353` | Dasar-dasar Pengauditan | 5 | 3 | S-9, S-11, P-19, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK36** | `502363` | Akuntansi Ekuitas dan Pelaporan Keuangan | 5 | 3 | S-9, P-9, P-20, KU-2, KK-8, KK-9, KK-10 | RESTORED_SEQUENTIAL_MATCH |
| **MK37** | `502373` | Pemeriksaan dan Penyidikan Pajak | 5 | 3 | S-9, P-10, P-11, P-19, KU-2, KK-11 | SEQUENTIAL_CODE_MATCH |
| **MK38** | `502383` | Pengauditan Internal | 5 | 3 | S-9, S-11, P-22, P-27, KU-2, KK-20 | SEQUENTIAL_CODE_MATCH |
| **MK39** | `502393` | Metodologi Penelitian | 5 | 3 | P-24 | SEQUENTIAL_CODE_MATCH |
| **MK40** | `588402` | Mengelola Pertumbuhan Bisnis & Bisnis Global | 5 | 2 | S-10, P-28, KU-2, KK-21 | RESTORED_SEQUENTIAL_MATCH |
| **MK41** | `602413` | Pengauditan Laporan Keuangan | 6 | 3 | S-9, S-11, KU-2, KK-17 | SEQUENTIAL_CODE_MATCH |
| **MK42** | `602423` | Bahasa Inggris untuk Bisnis | 6 | 3 | KU-13 | SEQUENTIAL_CODE_MATCH |
| **MK43** | `602432` | Teori Akuntansi | 6 | 2 | S-9, S-11, P-2, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK44** | `602442` | Penganggaran | 6 | 2 | S-9, S-11, P-18, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK45** | `602452` | Perpajakan Internasional | 6 | 2 | S-9, P-12, P-13, KU-2 | SEQUENTIAL_CODE_MATCH |
| **MK46** | `602462` | Hukum bisnis | 6 | 2 | P-22 | SEQUENTIAL_CODE_MATCH |
| **MK47** | `602472` | Etika profesi dan tata kelola korporasi | 6 | 2 | P-25 | SEQUENTIAL_CODE_MATCH |
| **MK48** | `601483` | KKN - Tematik | 6 | 3 | S-6 | SEQUENTIAL_CODE_MATCH |
| *MK49* | *—* | *(Design Matrix Column - Entrepreneurship Track)* | — | — | S-10, P-28, KU-2, KK-21 | CURRICULUM_DESIGN_COLUMN |
| *MK50* | *—* | *(Design Matrix Column - Practical Internship Module)* | — | — | S-8, S-9, KU-5, KU-7, KU-8, KU-10 | CURRICULUM_DESIGN_COLUMN |
| *MK51* | *—* | *(Design Matrix Column - Social Service Module)* | — | — | S-6 | CURRICULUM_DESIGN_COLUMN |
| *MK52* | *—* | *(Design Matrix Column - Thesis Proposal Module)* | — | — | P-26 | CURRICULUM_DESIGN_COLUMN |
| **MK53** | `7025320` | Pemagangan | 7 | 20 | S-8, S-9, KU-3, KU-4, KU-9, KU-11 | CODE_MATCH (`7025320`) |
| **MK54** | `802546` | Tugas Akhir | 8 | 6 | P-26, KU-3, KU-4, KU-5, KU-7, KU-8, KU-9, KU-10, KU-11 | CODE_MATCH (`802546`) |

---

## 5. Manual Golden Sample Validation (10 Mata Kuliah)

| No | Sampel Mata Kuliah | Matrix Col | Manual CPL dari PDF | Parser Matriks CPL | FP | FN | Verdict |
| :---: | :--- | :---: | :--- | :--- | :---: | :---: | :---: |
| 1 | **Agama** | MK1 | S-1 | `S-1` | 0 | 0 | **PASS** |
| 2 | **Pancasila** (Semester Awal) | MK2 | S-3 | `S-3` | 0 | 0 | **PASS** |
| 3 | **Akuntansi usaha jasa** (Akuntansi Keuangan) | MK6 | S-9, S-11, P-3, KU-2, KK-2 | `S-9, S-11, P-3, KU-2, KK-2` | 0 | 0 | **PASS** |
| 4 | **Akuntansi Manajemen** | MK29 | S-9, S-11, P-16, P-17, P-18, KU-2, KK-14, KK-15, KK-16 | `S-9, S-11, P-16, P-17, P-18, KU-2, KK-14, KK-15, KK-16` | 0 | 0 | **PASS** |
| 5 | **Dasar-dasar Perpajakan** | MK7 | S-9, P-7, KU-2 | `S-9, P-7, KU-2` | 0 | 0 | **PASS** |
| 6 | **Perpajakan Internasional** | MK45 | S-9, P-12, P-13, KU-2 | `S-9, P-12, P-13, KU-2` | 0 | 0 | **PASS** |
| 7 | **Dasar-dasar Pengauditan** | MK35 | S-9, S-11, P-19, KU-2 | `S-9, S-11, P-19, KU-2` | 0 | 0 | **PASS** |
| 8 | **Metodologi Penelitian** | MK39 | P-24 | `P-24` | 0 | 0 | **PASS** |
| 9 | **Pemagangan** | MK53 | S-8, S-9, KU-3, KU-4, KU-9, KU-11 | `S-8, S-9, KU-3, KU-4, KU-9, KU-11` | 0 | 0 | **PASS** |
| 10 | **Tugas Akhir** | MK54 | P-26, KU-3, KU-4, KU-5, KU-7, KU-8, KU-9, KU-10, KU-11 | `P-26, KU-3, KU-4, KU-5, KU-7, KU-8, KU-9, KU-10, KU-11` | 0 | 0 | **PASS** |

---

## 6. Exact Before / After Metrics

| Indikator Metrik | Sebelum Audit | Sesudah Audit & Rekonsiliasi | Status / Dampak |
| :--- | :---: | :---: | :--- |
| **Official CPL Count** | 73 | **73** | Tetap (S=11, P=28, KU=13, KK=21) |
| **Bahan Kajian Count** | 13 | **13** | Tetap (BK-1 s.d. BK-13) |
| **Katalog Mata Kuliah Aktif** | 47 | **50** | **+3 Restorasi Mata Kuliah Resmi** |
| **Total SKS Kurikulum** | 140 | **148** | **100% Sesuai SN-Dikti D4 (148 SKS)** |
| **Direct CPL-MK Pairs (Active Courses)** | 1 | **182** | **182 Relasi Deterministik Terpetakan** |
| **Raw Matrix Pairs (MK1..MK54)** | — | **194** | 100% Diekstrak dari Halaman 27–28 |
| **Mapped Course Count** | 1 (2.1%) | **50 (100.0%)** | Seluruh Mata Kuliah Terpetakan |
| **Unmapped Course Count** | 46 (97.9%) | **0 (0.0%)** | Zero Unmapped Courses |
| **Unresolved Matrix Columns** | — | **0** | Semua 50 MK terhubung deterministik |
| **Provenance Status** | `SOURCE_MAPPING_PARTIAL` | **`SOURCE_MAPPING_PARSED`** | **EXPLICIT_MATRIX (Direct Source of Truth)** |

---

## 7. Final Verdict

```
============================================================
FINAL VERDICT: AP_MATRIX_VALIDATED
============================================================
- 50 active courses (148 SKS) reconciled across Sections IX, X, XI, XIII.
- 3 missing normalized courses fully restored with correct codes & SKS.
- Deterministic crosswalk established for all 54 matrix columns.
- 182 direct CPL-MK pairs mapped with exact cell coordinates.
- 10-course golden sample passed 100% with zero false positives.
- Payload and Controllers updated and verified.
============================================================
```
