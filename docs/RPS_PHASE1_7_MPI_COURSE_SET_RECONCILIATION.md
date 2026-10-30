# Laporan Rekonsiliasi Himpunan Mata Kuliah & Jalur Kurikulum MPI (Phase 1.7)

**Program Studi**: D4 Manajemen Pemasaran Internasional (MPI)  
**Dokumen Kurikulum Acuan**: `Buku Kurikulum prodi MPI 2022 - MBKM .pdf`  
**Tahun Kurikulum**: 2022 (*Operational Curriculum*)  
**Source Hash**: `8c012ba9f17bb56883a9fbbb62a7699d5fab56f9b92eac407f52861d52539972`  
**Tanggal Audit**: 2026-09-20  
**Status Verdict**: **`MPI_MATRIX_VALIDATED`**

---

## 1. Mathematical Course-Set Reconciliation & Proof of Balance

Audit ini menyelesaikan ambiguitas relasional antara **49 Kolom Matriks Kurikulum** dan **63 Mata Kuliah Katalog Aktif**.

### A. Definisi Himpunan Eksplisit (Sets Definition)
- **Himpunan $A$ (Active Course Catalog)**: Seluruh 63 mata kuliah unik yang ditawarkan pada katalog operasional aktif (Semester 1 s.d 7, total 164 SKS).  
  $$\mathbf{|A| = 63}$$
- **Himpunan $B$ (Resolved Matrix Columns)**: Seluruh 49 kolom mata kuliah yang tercantum pada Matriks CPL-MK resmi (Hal. 17–18 PDF).  
  $$\mathbf{|B| = 49}$$
- **Himpunan $A \cap B$ (Active Mapped Courses in Matrix)**: Mata kuliah aktif yang berkorespondensi langsung dengan kolom pada matriks CPL-MK.  
  $$\mathbf{|A \cap B| = 43}$$
- **Himpunan $D = B - A$ (Matrix Columns Not in Active Catalog)**: Kolom pada matriks resmi yang tidak termasuk dalam katalog aktif 63 MK (terdiri atas 5 MK Institutional Entrepreneurship Series $E_1..E_5$ dan 1 MK Capstone Tugas Akhir Semester 8).  
  $$\mathbf{|D| = |B - A| = 6}$$
- **Himpunan $C = A - B$ (Active Courses Outside Matrix)**: Mata kuliah aktif yang tidak tercantum pada 49 kolom matriks (terdiri atas 14 MK Pilihan Konsentrasi Semester 6 dan 6 MK Modul Spesifik Jalur Magang Semester 7).  
  $$\mathbf{|C| = |A - B| = 20}$$
- **Himpunan $E$ (Duplicate / Equivalent Course Identities)**: $\emptyset$ ($|E| = 0$, tidak ada duplikasi identitas).
- **Himpunan $F$ (Elective & Track Alternatives)**: 20 mata kuliah pilihan konsentrasi dan jalur magang ($|F| = 20$).

---

### B. Pembuktian Aritmetika Himpunan (Set Arithmetic Proof)

1. **Persamaan Katalog Aktif (Active Catalog Balance)**:
   $$|A| = |A \cap B| + |C| = 43 + 20 = 63 \quad \mathbf{[\text{TERBUKTI SEIMBANG}]}$$
2. **Persamaan Kolom Matriks (Matrix Columns Balance)**:
   $$|B| = |A \cap B| + |D| = 43 + 6 = 49 \quad \mathbf{[\text{TERBUKTI SEIMBANG}]}$$
3. **Persamaan Saling Lepas (Disjoint Set Verification)**:
   $$B \cap C = \emptyset \quad \mathbf{[\text{VALID}]}$$
   $$D \cap A = \emptyset \quad \mathbf{[\text{VALID}]}$$
4. **Rekonstruksi Himpunan $A$**:
   $$(A \cap B) \cup C = A \quad \mathbf{[\text{LENGKAP 100\%}]}$$

---

## 2. Rincian Eksplisit 6 Kolom Matriks di Luar Katalog Aktif ($D = B - A$)

Berikut adalah 6 kolom pada matriks resmi yang **tidak dihitung** ke dalam `direct_mapped_active_course_count = 43`:

| Kolom | Kode MK | Nama Kolom Matriks | Semester | Tipe Jalur Kurikuler | Alasan Sumber Faktual |
| :---: | :---: | :--- | :---: | :--- | :--- |
| **Col 01** | `MPI12205` | Pola Pikir Kewirausahaan (E1) | 1 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E1` | Mata kuliah pembentuk CPL Institusional ($E_1$) pada matriks desain, diadministrasikan terpisah dari 63 katalog reguler prodi |
| **Col 02** | `MPI22302` | Pengembangan Model Bisnis (E2) | 2 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E2` | Mata kuliah pembentuk CPL Institusional ($E_2$) pada matriks desain, diadministrasikan terpisah dari 63 katalog reguler prodi |
| **Col 03** | `MPI32301` | Perintisan Bisnis (E3) | 3 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E3` | Mata kuliah pembentuk CPL Institusional ($E_3$) pada matriks desain, diadministrasikan terpisah dari 63 katalog reguler prodi |
| **Col 04** | `MPI42307` | Inovasi dan Pertumbuhan (E4) | 4 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E4` | Mata kuliah pembentuk CPL Institusional ($E_4$) pada matriks desain, diadministrasikan terpisah dari 63 katalog reguler prodi |
| **Col 05** | `MPI52201` | Mengelola Pertumbuhan Bisnis (E5) | 5 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E5` | Mata kuliah pembentuk CPL Institusional ($E_5$) pada matriks desain, diadministrasikan terpisah dari 63 katalog reguler prodi |
| **Col 33** | `80122696` | Skripsi/Tugas Akhir | 8 | `CAPSTONE_TUGAS_AKHIR_SEM8` | Mata kuliah Capstone Tugas Akhir Semester 8 (6 SKS) tercantum pada matriks kurikulum namun tidak termasuk dalam katalog aktif 63 MK (Sem 1-7) |

---

## 3. Rincian Eksplisit 20 Mata Kuliah Aktif di Luar Matriks ($C = A - B$)

Berikut adalah 20 mata kuliah aktif pada katalog yang berada di luar 49 kolom matriks:

### A. 14 Mata Kuliah Pilihan Konsentrasi (Semester 6)
1. `MPI64301`: Proses Entrepreneurial (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
2. `MPI64302`: Sifat/Perilaku Entrepreneurial (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
3. `MPI64203`: Berpikir Desain (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
4. `MPI64204`: Pengetahuan Bisnis Dasar (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
5. `MPI64305`: Keahlian Komunikasi dan Interpersonal (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
6. `MPI64206`: Kemampuan Digital (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
7. `MPI64307`: Kegiatan Bisnis (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
8. `MPI64308`: Bisnis Global Digital (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
9. `MPI64309`: Proyek Pemasaran Digital (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
10. `MPI64210`: Komunikasi Digital (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
11. `MPI64211`: Pembayaran Digital (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
12. `MPI64212`: Proyek Perdagangan Daring (2 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
13. `MPI64313`: Proyek Ekspor (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`
14. `MPI64314`: Pengembangan Ide Ekspor (3 SKS) — `ELECTIVE_CONCENTRATION_SEM6`

### B. 6 Mata Kuliah Jalur Magang Khusus (Semester 7)
15. `MPI75302`: Manajemen Hubungan Pelanggan (Internship) (3 SKS) — `INTERNSHIP_TRACK_SEM7`
16. `MPI75203`: Ekspor - Impor (Internship) (2 SKS) — `INTERNSHIP_TRACK_SEM7`
17. `MPI75304`: Strategi Pemasaran (Internship) (3 SKS) — `INTERNSHIP_TRACK_SEM7`
18. `MPI75305`: Penelitian pemasaran (Internship) (3 SKS) — `INTERNSHIP_TRACK_SEM7`
19. `MPI75306`: Intrapreneurship (Internship) (3 SKS) — `INTERNSHIP_TRACK_SEM7`
20. `MPI75307`: Intelijen Pemasaran (Internship) (3 SKS) — `INTERNSHIP_TRACK_SEM7`

*Catatan*: `MPI75301` (*Komunikasi Pemasaran Terpadu - Internship*) bertindak sebagai *canonical cluster representative* untuk kolom `Internship` (Col 21) pada matriks.

---

## 4. Tabel Identitas Lengkap 49 Kolom Matriks CPL-MK

| Col | Nama Kolom Matriks (PDF) | Resolved Code | Resolved Name | Is in Active Catalog? | Semester | Track Type | Canonical Identity |
| :---: | :--- | :---: | :--- | :---: | :---: | :--- | :--- |
| 1 | Pola Pikir Kewirausahaan (E1) | `MPI12205` | Pola Pikir Kewirausahaan (E1) | **NO** | 1 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E1` | Pola Pikir Kewirausahaan (E1) |
| 2 | Pengembangan Model Bisnis (E2) | `MPI22302` | Pengembangan Model Bisnis (E2) | **NO** | 2 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E2` | Pengembangan Model Bisnis (E2) |
| 3 | Perintisan Bisnis (E3) | `MPI32301` | Perintisan Bisnis (E3) | **NO** | 3 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E3` | Perintisan Bisnis (E3) |
| 4 | Inovasi dan Pertumbuhan (E4) | `MPI42307` | Inovasi dan Pertumbuhan (E4) | **NO** | 4 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E4` | Inovasi dan Pertumbuhan (E4) |
| 5 | Mengelola Pertumbuhan Bisnis (E5) | `MPI52201` | Mengelola Pertumbuhan Bisnis (E5) | **NO** | 5 | `INSTITUTIONAL_ENTREPRENEURSHIP_SERIES_E5` | Mengelola Pertumbuhan Bisnis (E5) |
| 6 | Prinsip-Prinsip Pemasaran | `MPI12308` | Prinsip-prinsip Pemasaran | **YES** | 1 | `MANDATORY_CORE` | Prinsip-prinsip Pemasaran |
| 7 | Perilaku Konsumen | `MPI52303` | Perilaku Konsumen | **YES** | 5 | `MANDATORY_CORE` | Perilaku Konsumen |
| 8 | Saluran Pemasaran | `MPI52305` | Saluran Pemasaran | **YES** | 5 | `MANDATORY_CORE` | Saluran Pemasaran |
| 9 | Penelitian  Pemasaran | `MPI63306` | Penelitian  Pemasaran | **YES** | 6 | `MANDATORY_CORE` | Penelitian  Pemasaran |
| 10 | Pemasaran Jasa | `MPI22308` | Pemasaran Jasa | **YES** | 2 | `MANDATORY_CORE` | Pemasaran Jasa |
| 11 | Strategi Pemasaran | `MPI63304` | Strategi Pemasaran | **YES** | 6 | `MANDATORY_CORE` | Strategi Pemasaran |
| 12 | Manajemen Merek Global | `MPI52302` | Manajemen Merek Global | **YES** | 5 | `MANDATORY_CORE` | Manajemen Merek Global |
| 13 | Strategi Sosial Media | `MPI63205` | Strategi Media Sosial | **YES** | 6 | `MANDATORY_CORE` | Strategi Media Sosial |
| 14 | Intelijen Pemasaran | `MPI63307` | Intelijen Pemasaran | **YES** | 6 | `MANDATORY_CORE` | Intelijen Pemasaran |
| 15 | Pemasaran Daring | `MPI52304` | Pemasaran Daring | **YES** | 5 | `MANDATORY_CORE` | Pemasaran Daring |
| 16 | Periklanan | `MPI52306` | Periklanan | **YES** | 5 | `MANDATORY_CORE` | Periklanan |
| 17 | Perdagangan Daring | `MPI42304` | Perdagangan Daring | **YES** | 4 | `MANDATORY_CORE` | Perdagangan Daring |
| 18 | Manajemen Ritel | `MPI42303` | Manajemen Ritel | **YES** | 4 | `MANDATORY_CORE` | Manajemen Ritel |
| 19 | Manajemen Pemasaran Global | `MPI42305` | Manajemen Pemasaran Global | **YES** | 4 | `MANDATORY_CORE` | Manajemen Pemasaran Global |
| 20 | Manajemen Hubungan Pelanggan | `MPI63202` | Manajemen Hubungan Pelanggan | **YES** | 6 | `MANDATORY_CORE` | Manajemen Hubungan Pelanggan |
| 21 | Internship | `MPI75301` | Komunikasi Pemasaran Terpadu (Internship) | **YES** | 7 | `INTERNSHIP_CLUSTER_CANONICAL` | Internship Cluster |
| 22 | Pengantar Manajemen dan Bisnis | `MPI12206` | Pengantar Manajemen & Bisnis | **YES** | 1 | `MANDATORY_CORE` | Pengantar Manajemen & Bisnis |
| 23 | Manajemen Sumber Daya Manusia | `MPI32205` | Manajemen Sumberdaya Manusia | **YES** | 3 | `MANDATORY_CORE` | Manajemen Sumberdaya Manusia |
| 24 | Hukum Bisnis | `MPI22203` | Hukum Bisnis | **YES** | 2 | `MANDATORY_CORE` | Hukum Bisnis |
| 25 | Manajemen Strategik | `MPI52307` | Manajemen Stratejik | **YES** | 5 | `MANDATORY_CORE` | Manajemen Stratejik |
| 26 | Manajemen Keuangan Perusahaan | `MPI22306` | Manajemen Keuangan | **YES** | 2 | `MANDATORY_CORE` | Manajemen Keuangan |
| 27 | Penganggaran Perusahaan | `MPI42306` | Penganggaran Perusahaan | **YES** | 4 | `MANDATORY_CORE` | Penganggaran Perusahaan |
| 28 | Pengantar Ilmu Ekonomi | `MPI12307` | Pengantar Ilmu Ekonomi | **YES** | 1 | `MANDATORY_CORE` | Pengantar Ilmu Ekonomi |
| 29 | Ekspor-Impor | `MPI63303` | Ekspor - Impor | **YES** | 6 | `MANDATORY_CORE` | Ekspor - Impor |
| 30 | Ekonomi Internasional | `MPI22304` | Ekonomi Internasional | **YES** | 2 | `MANDATORY_CORE` | Ekonomi Internasional |
| 31 | Statistika Ekonomi & Bisnis | `MPI32306` | Statistika Ekonomi dan Bisnis | **YES** | 3 | `MANDATORY_CORE` | Statistika Ekonomi dan Bisnis |
| 32 | Metode Penelitian | `MPI62301` | Metode Penelitian | **YES** | 6 | `MANDATORY_CORE` | Metode Penelitian |
| 33 | Skripsi/Tugas Akhir | `80122696` | Tugas Akhir | **NO** | 8 | `CAPSTONE_TUGAS_AKHIR_SEM8` | Tugas Akhir |
| 34 | Aplikasi Komputer | `MPI32304` | Aplikasi Komputer | **YES** | 3 | `MANDATORY_CORE` | Aplikasi Komputer |
| 35 | Sistem Informasi Manajemen | `MPI42208` | Sistem Informasi Manajemen | **YES** | 4 | `MANDATORY_CORE` | Sistem Informasi Manajemen |
| 36 | Desain Multimedia | `MPI32303` | Desain Multimedia | **YES** | 3 | `MANDATORY_CORE` | Desain Multimedia |
| 37 | Pengantar Akuntansi | `MPI22205` | Pengantar Akuntansi | **YES** | 2 | `MANDATORY_CORE` | Pengantar Akuntansi |
| 38 | Komunikasi Bisnis | `MPI32202` | Komunikasi Bisnis | **YES** | 3 | `MANDATORY_CORE` | Komunikasi Bisnis |
| 39 | Bahasa Inggris | `MPI12303` | Bahasa Inggris | **YES** | 1 | `MANDATORY_CORE` | Bahasa Inggris |
| 40 | Bahasa Inggris Bisnis | `MPI42302` | Bahasa Inggris Bisnis | **YES** | 4 | `MANDATORY_CORE` | Bahasa Inggris Bisnis |
| 41 | Sikap Religius | `MPI41101` | Sikap Religius | **YES** | 4 | `MANDATORY_CORE` | Sikap Religius |
| 42 | Agama | `MPI11201` | Agama | **YES** | 1 | `MANDATORY_CORE` | Agama |
| 43 | Pendidikan Pancasila | `MPI21207` | Pendidikan Pancasila | **YES** | 2 | `MANDATORY_CORE` | Pendidikan Pancasila |
| 44 | Seni & Olah Raga | `MPI22201` | Seni & Olahraga | **YES** | 2 | `MANDATORY_CORE` | Seni & Olahraga |
| 45 | Etika Bisnis | `MPI32208` | Etika Bisnis | **YES** | 3 | `MANDATORY_CORE` | Etika Bisnis |
| 46 | Berpikir Kritis | `MPI12204` | Berpikir Kritis | **YES** | 1 | `MANDATORY_CORE` | Berpikir Kritis |
| 47 | Pendidikan Kewarganegaraan (PKn) | `MPI31207` | Pendidikan Kewarganegaraan (PKn) | **YES** | 3 | `MANDATORY_CORE` | Pendidikan Kewarganegaraan (PKn) |
| 48 | Kuliah Kerja Nyata (KKN) | `MPI63208` | KKN | **YES** | 6 | `MANDATORY_CORE` | KKN |
| 49 | Bahasa Indonesia | `MPI11202` | Bahasa Indonesia | **YES** | 1 | `MANDATORY_CORE` | Bahasa Indonesia |

---

## 5. Audit Versi Dokumen Kurikulum

| Atribut Dokumen | Nilai Faktual | Keterangan Audit |
| :--- | :--- | :--- |
| **File Name** | `Buku Kurikulum prodi MPI 2022 - MBKM .pdf` | Dokumen resmi kurikulum MBKM MPI |
| **Tahun Kurikulum** | **2022** | Kurikulum operasional Program Studi D4 MPI |
| **Judul Dokumen** | `Buku Kurikulum Program Studi Sarjana Terapan MPI MBKM` | Identitas dokumen resmi |
| **Source Hash** | `8c012ba9f17bb56883a9fbbb62a7699d5fab56f9b92eac407f52861d52539972` | SHA-256 terverifikasi deterministik |
| **Status Catatan** | Typo `KURIKULUM MPI 2024.pdf` pada header laporan sebelumnya telah diperbaiki ke dokumen resmi `Buku Kurikulum prodi MPI 2022 - MBKM .pdf`. |

---

## 6. Final Metrics Balance Table

```
===========================================================================================
Set / Metric Item                                    | Count | Formula / Verification
===========================================================================================
Active Course Catalog Unique Count (|A|)             |  63   | Mandatory (42) + Elective (14) + Internship (7)
Matrix Course Columns Count (|B|)                    |  49   | Hal. 17-18 PDF Header Columns
Direct Mapped Active Courses Count (|A ∩ B|)         |  43   | 42 Mandatory Core + 1 Internship Canonical
Matrix Courses Not in Active Catalog (|D = B - A|)    |   6   | 5 Entrepreneurship (E1..E5) + 1 Tugas Akhir (Sem 8)
Active Courses Outside Matrix (|C = A - B|)          |  20   | 14 Elective Pool + 6 Internship Tracks
Duplicate / Ambiguous Course Identities (|E|)        |   0   | Tidak ada duplikasi identitas
Direct CPL-MK Pairs Extracted                        | 598   | 429 (Hal. 17) + 169 (Hal. 18)
Total Official CPL                                   |  50   | S-1..S-11, KU-1..KU-9, P-1..P-10, KK-1..KK-20
Total Bahan Kajian (BK)                              |  10   | BK 1 s.d BK 10
Total SKS                                            | 164   | 164 SKS
Active Course Direct Mapping Coverage Pct            | 68.25%| 43 / 63 = 0.6825 (PARTIAL_BY_SOURCE_DESIGN)
===========================================================================================
Persamaan 1: |A| = |A ∩ B| + |C|  ==>  63 = 43 + 20   [BALANCED & PROVED]
Persamaan 2: |B| = |A ∩ B| + |D|  ==>  49 = 43 + 6    [BALANCED & PROVED]
===========================================================================================
```

---

## 7. Final Verdict

**`MPI_MATRIX_VALIDATED`** — Seluruh persamaan aritmetika himpunan mata kuliah, pemilahan jalur kurikuler (*mandatory, elective, internship, institutional series, capstone*), dan matriks CPL-MK resmi MPI telah 100% konsisten, terbukti matematis, dan terintegrasi penuh.
