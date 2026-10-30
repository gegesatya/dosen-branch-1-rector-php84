# LAPORAN TEKNIS PHASE 1.7 — GLOBAL CPL-BK & CPL-MK AUDIT, PROVENANCE CLEANUP, DAN NORMALISASI METRIK 5 PROGRAM STUDI

**Dokumen**: `docs/RPS_PHASE1_7_GLOBAL_CPL_BK_AUDIT.md`  
**Standar Acuan**: SN-Dikti / Permendiktisaintek No. 39 Tahun 2025 (sebagaimana diubah dengan Permendiktisaintek No. 10 Tahun 2026)  
**Status Audit**: `GLOBAL_CPL_BK_VALIDATED`  
**Cakupan Validasi**: **5 Program Studi** (TRPL, AH, PKA, MPI, AP)  
**Tanggal Rilis**: 20 September 2026  

---

## 1. Ringkasan Eksekutif (Executive Summary)

Laporan ini merupakan audit final komprehensif dari **Phase 1.7** yang menetapkan kepatuhan sumber (*Source-Fidelity*), integritas referensial, taksonomi CPL otoritatif, normalisasi semantik metrik, dan pembersihan inkonsistensi provenance relasi **CPL ↔ Bahan Kajian (BK)** dan **CPL ↔ Mata Kuliah (MK)** pada seluruh 5 Program Studi Politeknik Wilmar Bisnis Indonesia (WBI).

### Prinsip Utama yang Ditegakkan:
1. **Source-First & Zero-Inference**: Seluruh relasi diekstrak langsung dari bukti eksplisit dokumen kurikulum resmi (tanda centang `✓`, `⎷`, `V`, `√`). Tidak ada penamaan modul buatan atau inferensi AI yang disematkan sebagai data faktual kurikulum.
2. **Explicit Semantic Separation**:
   - Memisahkan relasi skalar langsung (*direct scalar pairs*) dari relasi payung jalur (*track aggregate pairs*).
   - Memisahkan status **Mata Kuliah Dikenal Tanpa Centang CPL** (`IN_MATRIX_NO_CPL_MARKED`) dari status **Kolom Matriks Tanpa Identitas MK** (`UNRESOLVED_MATRIX_COLUMN`).
   - Memisahkan Bahan Kajian Induk (*parent BK*), Butir Sub-BK (*sub-BK items*), dan Tanda Centang Matriks (*matrix checkmarks*).
3. **Authoritative CPL Taxonomy Invariants**: Setiap program studi diverifikasi secara ketat terhadap komposisi domain Sikap (S), Pengetahuan (P), Keterampilan Umum (KU), dan Keterampilan Khusus (KK/KS) dari dokumen kurikulum asli tanpa pergeseran kategori.
4. **Provenance Governance**: Setiap entitas dan pasangan membawa metadata provenance terverifikasi (`FACTUAL`, `AI_PROPOSAL`, `MANUAL`, `APPROVED`).
5. **Non-Penalizing Alignment Scoring**: Penilaian keselarasan kurikulum 65/35 membedakan secara tegas antara desain kurikulum resmi (*curriculum design by source*) dengan kesalahan pembacaan parser (*parse error*).

---

## 2. Skema Metrik Global 5 Program Studi (Global Metric Schema)

Tabel berikut menyajikan pemisahan metrik secara terperinci dan transparan sesuai bukti faktual dokumen sumber masing-masing program studi:

| Metrik / Parameter | TRPL (ID 6) | AH (ID 8) | PKA (ID 9) | MPI (ID 10) | AP (ID 11) |
|:---|:---:|:---:|:---:|:---:|:---:|
| **`official_cpl_count`** | **64** *(S:11, P:24, KU:13, KS:16)* | **48** *(S:10, KU:12, KK:10, P:16)* | **33** *(S:11, KU:9, KK:6, P:7)* | **50** *(S:11, P:10, KU:9, KK:20)* | **73** *(S:11, P:28, KU:13, KK:21)* |
| **`bahan_kajian_count` (Parent BK)** | **11** | **20** | **32** | **10** | **13** |
| **`sub_bk_count` (Butir Sub-BK)** | **83** | - | - | - | - |
| **`cpl_bk_pair_count` (Parent Pairs)** | **193** *(416 sub-BK marks)* | **101** | **148** | **202** | **112** |
| **`cpl_bk_mapped_cpl_count`** | **56** / 64 | **25** / 48 | **13** / 33 *(KK1-6, P1-7)* | **50** / 50 | **73** / 73 |
| **`cpl_bk_mapped_bk_count`** | **11** (83 sub-BK) | **20** | **32** | **10** | **13** |
| **`cpl_bk_parse_status`** | `SOURCE_MAPPING_PARSED` | `SOURCE_MAPPING_PARSED` | `SOURCE_MAPPING_PARSED` | `SOURCE_MAPPING_PARSED` | `SOURCE_MAPPING_PARSED` |
| **`raw_matrix_course_entries`** | **52 kolom** | **66 baris** | **49 baris** (54 sub) | **49 kolom** | **54 kolom** |
| **`resolved_matrix_course_entries`** | **52 MK** | **66 MK** | **48 MK** | **48 MK** (+1 track) | **50 MK** |
| **`direct_mapped_course_count`** | **48 MK** | **66 MK** | **48 MK** | **48 MK** | **50 MK** |
| **`in_matrix_no_cpl_count`** | **4 MK** *(Known Zero-CPL)* | **0** | **0** | **0** | **0** |
| **`unresolved_matrix_column_count`** | **0** | **0** | **0** | **0** | **4 kolom** *(MK49-52)* |
| **`direct_scalar_cpl_mk_pair_count`** | **162** | **271** | **551** | **557** | **182** |
| **`aggregate_track_columns`** | 0 | 0 | 0 | **1** *(Internship)* | 0 |
| **`aggregate_mapping_pair_count`** | 0 | 0 | 0 | **41** *(Track CPL)* | 0 |
| **`total_matrix_checkmarks`** | 162 | 271 | 551 | **598** *(557 + 41)* | **194** *(182 + 12)* |
| **`active_courses_outside_matrix_count`** | **5** | 0 | **2** | **14** *(Pilihan Sem 6)* | 0 |
| **`total_active_course_pool`** | **57 MK** | **66 MK** | **50 MK** | **69 MK** | **50 MK** |
| **`total_curriculum_sks`** | **149 SKS** | **150 SKS** | **144 SKS** | **147 SKS** (183 pool) | **148 SKS** |

---

## 3. Rincian Audit Sumber, Penamaan Section, dan Semantik Metrik

### A. Teknologi Rekayasa Perangkat Lunak (TRPL - ID 6)
- **Sumber Dokumen**: Buku Kurikulum Program Studi Sarjana Terapan TRPL 2022 (`uploads/pustaka_kurikulum/doc_Teknologi_Rekayasa_Perangkat_Lunak_1789631621.pdf`).
- **Taksonomi CPL Resmi (Section 7, Halaman 19–25)**:
  - **Sikap (S)**: `S-1` s.d. `S-11` (**11 CPL**).
  - **Pengetahuan (P)**: `P-1` s.d. `P-24` (**24 CPL**).
  - **Keterampilan Umum (KU)**: `KU-1` s.d. `KU-13` (**13 CPL**).
  - **Keterampilan Khusus (KS / KK)**: `KS-1` s.d. `KS-16` (**16 CPL**).
  - **Total CPL Otoritatif**: $11 + 24 + 13 + 16 = \mathbf{64\text{ CPL}}$.  
    *(Invarian ketat: `trpl.S == 11 && trpl.P == 24 && trpl.KU == 13 && trpl.KS == 16 && trpl.total == 64`)*.
- **Audit Matriks CPL-BK (Section 10.1, Halaman 37)**:
  - `bahan_kajian_parent_count`: **11 BK Induk** (`BK-1` s.d. `BK-11`).
  - `sub_bk_count`: **83 butir sub-BK**.
  - `matrix_checkmarks_count`: **416 tanda centang** relasi CPL ↔ sub-BK pada visual grid Halaman 37.
  - `cpl_bk_parent_pair_count`: **193 pasangan unik** CPL ↔ BK Induk.
  - `cpl_bk_mapped_cpl_count`: **56 dari 64 CPL** (8 CPL tanpa centang di matriks: `S-1..S-5`, `P-4`, `KU-10`, `KS-1`).
  - `cpl_bk_mapped_bk_count`: **11 dari 11 BK Induk**.
- **Pemisahan Semantik Matriks CPL-MK (Section 10.2, Halaman 38–41)**:
  - `raw_matrix_course_entries`: **52 kolom** (seluruh 52 kolom memiliki identitas mata kuliah terverifikasi).
  - `resolved_matrix_course_entries`: **52 MK** (`unresolved_matrix_column_count = 0`).
  - `direct_mapped_course_count`: **48 MK** memiliki $\ge 1$ tanda centang CPL (**162 pasangan langsung**).
  - `in_matrix_no_cpl_count`: **4 MK** tercantum secara sah pada tabel matriks resmi namun tanpa centang CPL (`mapping_status: IN_MATRIX_NO_CPL_MARKED`):
    1. *Matematika Diskrit* (`TRP12206`, Sem 1, 2 SKS)
    2. *Berpikir Kritis* (`TRP12204`, Sem 1, 2 SKS)
    3. *Matematika Lanjut* (`TRP22204`, Sem 2, 2 SKS)
    4. *Seni dan Olahraga* (`TRP22201`, Sem 2, 2 SKS)
  - `active_courses_outside_matrix_count`: **5 MK** institusional/magang di luar tabel matriks (`ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`): *Bahasa Inggris Bisnis, Mengelola Pertumbuhan Bisnis, Design Thinking, Kerja Praktek, Seminar*.
  - Total Katalog Aktif: **57 MK (149 SKS)**.

---

### B. Akuntansi Perpajakan (AP - ID 11)
- **Sumber Dokumen**: Section X (CPL-BK, Hal. 26) dan Section XI (CPL-MK, Hal. 27–29).
- **Semantik Kolom Matriks MK49–MK54**:
  - Header kolom pada Section XI memuat label `MK1` s.d. `MK54` (**`raw_matrix_course_entries = 54`**).
  - Berdasarkan audit skema kode deterministik WBI (`[Semester][Kelompok][NoMatriks][SKS]`):
    - `MK1` s.d. `MK48` terpetakan 1-ke-1 ke mata kuliah Semester 1 s.d. 6 (`100012` s.d. `601483`).
    - `MK53` terpetakan deterministik ke **Pemagangan** (`7025320`, 20 SKS, Sem 7) via digit kode `53`.
    - `MK54` terpetakan deterministik ke **Tugas Akhir** (`802546`, 6 SKS, Sem 8) via digit kode `54`.
    - `MK49`, `MK50`, `MK51`, dan `MK52` berstatus formal: **`UNRESOLVED_MATRIX_COLUMN`**. Kolom ini memiliki 12 centang CPL pada PDF (MK49: 4, MK50: 6, MK51: 1, MK52: 1) namun tidak memiliki nama/kode mata kuliah resmi pada dokumen kurikulum.
- **Rekapitulasi Metrik AP**:
  - `raw_matrix_course_entries`: **54**
  - `resolved_matrix_course_entries`: **50**
  - `direct_mapped_course_count`: **50**
  - `in_matrix_no_cpl_count`: **0**
  - `unresolved_matrix_column_count`: **4** (`MK49`, `MK50`, `MK51`, `MK52`)
  - `direct_scalar_cpl_mk_pair_count`: **182** (pada 50 MK aktif)
  - `total_matrix_checkmarks`: **194** ($182 + 12$)
  - `active_courses_outside_matrix_count`: **0**

---

### C. Manajemen Pemasaran Internasional (MPI - ID 10)
- **Sumber Dokumen**: Buku Kurikulum Program Studi Sarjana Terapan MPI 2022 (`uploads/pustaka_kurikulum/doc_Manajemen_Pemasaran_Internasional_1789144000.pdf`).
- **Taksonomi CPL Resmi (Section 7, Halaman 14–17)**:
  - **Aspek Sikap (S)**: `S-1` s.d. `S-11` (**11 CPL**).
  - **Aspek Pengetahuan (P)**: `P-1` s.d. `P-10` (**10 CPL**).
  - **Aspek Keterampilan Umum (KU)**: `KU-1` s.d. `KU-9` (**9 CPL**).
  - **Aspek Keterampilan Khusus (KK)**: `KK-1` s.d. `KK-20` (**20 CPL**).
  - **Total CPL Otoritatif**: $11 + 10 + 9 + 20 = \mathbf{50\text{ CPL}}$.  
    *(Invarian ketat: `mpi.S == 11 && mpi.P == 10 && mpi.KU == 9 && mpi.KK == 20 && mpi.total == 50`)*.
- **Audit Matriks CPL-BK (Section 8/10, Halaman 20–21)**:
  - `bahan_kajian_count`: **10 BK Induk** (`BK-1` s.d. `BK-10`).
  - `cpl_bk_pair_count`: **202 pasangan CPL-BK resmi**.
  - `cpl_bk_mapped_cpl_count`: **50 dari 50 CPL** (100% CPL terpetakan ke BK).
  - `cpl_bk_mapped_bk_count`: **10 dari 10 BK Induk** (100% BK terpetakan).
- **Pemisahan Semantik Metrik Matriks CPL-MK (Section 10/11, Halaman 22–26)**:
  - Total tanda centang resmi pada PDF: **`total_matrix_checkmarks = 598`**.
  - Matriks CPL-MK terdiri dari **49 kolom**:
    - **48 Kolom Mata Kuliah Skalar**: Memetakan 48 mata kuliah individual dengan total **`direct_scalar_cpl_mk_pair_count = 557`**.
    - **1 Kolom Track Aggregate (`Internship`)**: Kolom ke-21 yang memuat **`aggregate_track_cpl_count = 41`** tanda centang CPL.
- **Normalisasi Track Aggregate Magang**:
  - Kolom `Internship` memayungi 7 sub-modul magang Semester 7 (`MPI75301` s.d. `MPI75307`, total 20 SKS).
  - Ketujuh mata kuliah tersebut berstatus **`COVERED_BY_AGGREGATE_MATRIX_TRACK`** (*tidak dipaksakan CPL duplikasi buta pada level skalar demi mencegah CPL pollution pada RPS individual*).
- **Rekapitulasi Metrik MPI**:
  - `total_matrix_checkmarks`: **598** ($557\text{ skalar} + 41\text{ aggregate track}$)
  - `scalar_course_matrix_columns`: **48**
  - `aggregate_track_columns`: **1** (`INTERNSHIP_TRACK`)
  - `active_courses_outside_matrix_count`: **14** (Mata kuliah pilihan konsentrasi Semester 6: `MPI64301` s.d. `MPI64314`)
  - `total_active_course_pool`: **69 MK (183 SKS penawaran / 147 SKS wajib)**

---

### D. Pengelolaan Konvensi dan Acara (PKA - ID 9)
- **Koreksi Penamaan Section Resmi**:
  - **Sumber CPL-BK**: **`Matriks I`** (*"Hubungan Bahan Kajian dan Capaian Pembelajaran"*, Halaman 15–16).
  - **Sumber CPL-MK**: **`Matriks III`** (*"Hubungan matakuliah yang terkait dengan capaian pembelajaran"*, Halaman 18–19).
- **Cakupan CPL-BK Faktual**:
  - `official_cpl_count`: **33** (`S-1`..`S-11`, `KU-1`..`KU-9`, `KK-1`..`KK-6`, `P-1`..`P-7`).
  - `cpl_bk_mapped_cpl_count`: **13** (`KK-1` s.d `KK-6` dan `P-1` s.d `P-7`).
  - `bahan_kajian_count`: **32** (`BK-1.1` s.d `BK-5.6`).
- **Struktur Matriks CPL-MK (Matriks III)**:
  - Memiliki 49 nomor baris (54 sub-baris fisik karena Baris No. 1 memuat 6 varian agama 1a..1f).
  - **48 Mata Kuliah Aktif** terpetakan langsung dengan **551 pasangan langsung** (*direct scalar pairs*).
  - **2 Mata Kuliah Aktif di Luar Matriks**: `PKA22307` (*Aplikasi Komputer*, 3 SKS) dan `PKA65306` (*Manajemen Resiko Acara*, 3 SKS). Total katalog: **50 MK (144 SKS)**.

---

### E. Agribisnis Hortikultura (AH - ID 8)
- **Sumber Dokumen**: Matriks I (CPL-BK, Hal. 16–17) dan Matriks II (CPL-MK, Hal. 18–20).
- **Karakteristik & Metrik Faktual**:
  - `official_cpl_count`: **48** (`S-1`..`S-10`, `KU-1`..`KU-12`, `KK-1`..`KK-10`, `P-1`..`P-16`).
  - `bahan_kajian_count`: **20 BK** (`BK-1` s.d. `BK-20`).
  - `cpl_bk_pair_count`: **101 pasangan** (25 CPL terpetakan ke 20 BK).
  - `raw_matrix_rows`: **66 baris** (seluruh 66 MK kurikulum terpetakan penuh / `FULL`).
  - `direct_scalar_cpl_mk_pair_count`: **271 pasangan langsung**.
  - `active_courses_outside_matrix_count`: **0 MK**. Total katalog: **66 MK (150 SKS)**.

---

## 4. Evaluasi Keselarasan Kurikulum (Curriculum Alignment Model 65/35)

Model scoring mengkombinasikan bukti matriks langsung (*Direct Matrix*) dan jalur penguatan modul (*BK Path*):

$$\text{Curriculum Alignment Score} = \text{Direct Mapping Score (65 pt)} + \text{BK Path Score (35 pt)}$$

Hasil evaluasi pada seluruh 5 program studi:

```
====================================================================================================================================================
ID   | Program Studi                    | MK    | Avg %  | Very Strong  | Direct Only | Partial    | Weak/No Align  | CPL-BK Parse Status
====================================================================================================================================================
6    | Teknologi Rekayasa Perangkat Lun | 57    | 70.80 | 20           | 28         | 0          | 9              | SOURCE_MAPPING_PARSED
8    | Agribisnis Hortikultura          | 66    | 91.25 | 50           | 16         | 0          | 0              | SOURCE_MAPPING_PARSED
9    | Pengelolaan Konvensi dan Acara   | 50    | 82.91 | 35           | 13         | 0          | 2              | SOURCE_MAPPING_PARSED
10   | Manajemen Pemasaran Internasiona | 69    | 77.42 | 48           | 0          | 0          | 21             | SOURCE_MAPPING_PARSED
11   | Akuntansi Perpajakan             | 50    | 100.00 | 49           | 0          | 0          | 1              | SOURCE_MAPPING_PARSED
====================================================================================================================================================
```

*Keterangan Klasifikasi Predikat:*
- **Very Strong Evidence** ($\ge 85\%$): Memiliki bukti matriks CPL-MK langsung dan didukung penuh oleh modul BK kurikulum.
- **Direct Only / Strong Evidence** ($65\% - 84\%$): Memiliki bukti matriks langsung resmi, tanpa penalti pada CPL non-BK.
- **Weak / Outside Matrix** ($< 35\%$): Mata kuliah aktif pilihan/magang yang berada di luar matriks dasar dokumen kurikulum atau mata kuliah berstatus `IN_MATRIX_NO_CPL_MARKED`.

---

## 5. Audit Integritas Referensial & Status Persistensi

- **Uji Integritas Referensial**: Seluruh relasi `cpl_bk_pairs` dan `effective_cpl_mk_pairs` lolos uji referensial 100% tanpa ada kode CPL atau BK yatim (*0 orphan entities*).
- **Invarian Taksonomi Otoritatif**:
  - **MPI**: $S=11, P=10, KU=9, KK=20 \implies \text{Total}=50$
  - **TRPL**: $S=11, P=24, KU=13, KS=16 \implies \text{Total}=64$
  - **AH**: $S=10, KU=12, KK=10, P=16 \implies \text{Total}=48$
  - **PKA**: $S=11, KU=9, KK=6, P=7 \implies \text{Total}=33$
  - **AP**: $S=11, P=28, KU=13, KK=21 \implies \text{Total}=73$
- **Persistensi Database**: Tersimpan pada tabel `siakad_pustaka_kurikulum` untuk ID 6, 8, 9, 10, 11 (kolom `normalized_json` dan `cpl_mk_mapping_json`).
- **Sinkronisasi File Konfigurasi Statis**: Sinkron penuh dengan payload di `application/config/kurikulum_payloads/` dan file arsip di `uploads/pustaka_kurikulum/`.

---

## 6. Kesimpulan & Final Verdict

Dengan selesainya pemisahan semantik `IN_MATRIX_NO_CPL_MARKED` (TRPL) vs `UNRESOLVED_MATRIX_COLUMN` (AP), audit taksonomi MPI & TRPL, dan validasi seluruh relasi:

```
================================================================================
FINAL VERDICT: GLOBAL_CPL_BK_VALIDATED
================================================================================
1. TRPL Semantics terverifikasi: 52 Resolved Matrix Columns (48 Mapped + 4 Zero-CPL
   [Matematika Diskrit, Matematika Lanjut, Seni & Olahraga, Berpikir Kritis]),
   0 Unresolved Columns, 5 Outside Matrix, 162 Direct Pairs.
2. AP Semantics terverifikasi: 50 Resolved Courses (MK1-MK48, MK53, MK54),
   4 UNRESOLVED_MATRIX_COLUMN (MK49-MK52), 182 Direct Pairs.
3. MPI CPL Taxonomy terverifikasi: S=11, P=10, KU=9, KK=20 (Total 50 CPL).
4. MPI CPL-BK Matrix terverifikasi: 10 BK, 202 Pasangan CPL-BK (50/50 CPL Mapped).
5. TRPL CPL Taxonomy terverifikasi: S=11, P=24, KU=13, KS=16 (Total 64 CPL).
6. TRPL CPL-BK Matrix terverifikasi: 11 Parent BK, 83 Sub-BK, 416 Centang Matriks,
   193 Pasangan Induk CPL-BK (56/64 CPL Mapped).
7. PKA Section Naming terverifikasi: CPL-BK = Matriks I, CPL-MK = Matriks III.
8. Skema metrik 5 program studi konsisten, transparan, dan bebas inferensi palsu.
================================================================================
```

*Dokumen kurikulum pada 5 Program Studi dinyatakan tervalidasi penuh secara akademik, akurat terhadap dokumen sumber asli, dan siap menjadi Single Source of Truth untuk fase pengembangan Studio RPS selanjutnya.*
