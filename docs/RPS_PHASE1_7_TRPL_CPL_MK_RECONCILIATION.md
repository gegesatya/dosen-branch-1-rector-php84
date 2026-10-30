# LAPORAN AUDIT & REKONSILIASI MATRIKS CPL-MK PRODI TEKNOLOGI REKAYASA PERANGKAT LUNAK (TRPL)
**Status**: COMPLETE & FACTUAL VALIDATED  
**Standard**: OBE / SN-Dikti / Politeknik Wilmar Bisnis Indonesia (WBI)  
**Dokumen Sumber**: `doc_Teknologi_Rekayasa_Perangkat_Lunak_1789631621.pdf` / `Dokumen Kurikulum TRPL.pdf` (53 Halaman)

---

## 1. RINGKASAN EKSEKUTIF & REKONSILIASI JUMLAH MK

| Parameter | Faktual Dokumen Resmi | Keterangan & Provenance |
| :--- | :---: | :--- |
| **Official CPL** | **64** | S: 11, P: 24, KU: 13, KK (KS): 16 |
| **Bahan Kajian (BoK)** | **11** | 11 Domain BoK Utama (83 rincian topik pada Matriks 10.1) |
| **Kolom Matriks CPL-MK (Section 10.2)** | **52** | MK1 s.d. MK52 (Halaman 38–39 PDF) |
| **Baris Distribusi Semester (Section 10.4)** | **57** | Semester I–VIII, Total **149 SKS** (Halaman 41–43 PDF) |
| **Struktur Pembentukan MK (Section 9)** | **57** | Total **149 SKS** (Halaman 34–36 PDF) |
| **Direct CPL-MK Pairs (Section 10.2)** | **162** | Diekstrak langsung dari sel centang 'v' Matriks Resmi |
| **Mapping Source Exists** | **TRUE** | Section 10.2 Matriks CPL-MK |
| **Mapping Parse Status** | **SOURCE_MAPPING_PARSED** | Direct primary evidence |

### Penjelasan Diskrepansi Jumlah MK (52 vs 54 vs 57):
1. **52 MK (Matriks CPL-MK Section 10.2)**:
   - Matriks CPL-MK memuat 52 kolom (MK1..MK52) yang berkorespondensi 1:1 dengan penomoran mata kuliah pada **Section 8: Bahan Kajian/Body of Knowledge** (nomor 1 s.d. 52).
2. **54 MK (Parser Lama)**:
   - Angka 54 pada catalog normalisasi lama adalah artefak parsing teks markdown, di mana 3 mata kuliah terpotong karena nama mata kuliah terbagi menjadi dua baris (multiline) pada markdown (`Metode dan Model Pengembangan Perangkat Lunak` [Sem 3], `Kerangka Kerja Pengembangan Antarmuka Website` [Sem 4], dan `Pengembangan Game dan Teknologi Immersive` [Sem 6]).
3. **57 MK (Authoritative Active Catalog)**:
   - Jumlah mata kuliah aktif yang sah pada kurikulum TRPL adalah **57 MK (149 SKS)**, sebagaimana tercantum secara konsisten pada **Section 9 (Pembentukan Mata Kuliah)** dan **Section 10.4 (Distribusi Mata Kuliah Persemester)**.
   - 5 mata kuliah tambahan di luar 52 kolom matriks adalah:
     - `Pancasila` (terdaftar di Section 8 no. 53, Sem 3, 2 SKS)
     - `Kerja Praktek` (Sem 7, 8 SKS)
     - `Seminar` (Sem 7, 2 SKS)
     - `Proyek Pengembangan Perangkat Lunak` (Sem 7, 6 SKS)
     - `Tugas Akhir` (Sem 8, 6 SKS)

---

## 2. CROSSWALK DETERMINISTIK MK1..MK52 KE MATA KULIAH RESMI

| Kode Kolom | Nama Mata Kuliah Resmi (Section 8 & 9) | SKS (T/P/Tot) | Sem | Jumlah CPL Mapped | Status Mapping Matriks |
| :--- | :--- | :---: | :---: | :---: | :--- |
| **MK01** | Matematika Diskrit | 2/0/2 | 1 | 0 | Unmapped in matrix |
| **MK02** | Pengenalan Rekayasa Perangkat Lunak | 2/0/2 | 1 | 12 | P-1, P-7, KU-3, KU-4, KU-5, KU-6, KS-1, KS-2, KS-3, KS-7, KS-8, KS-12 |
| **MK03** | Arsitektur dan Organisasi Komputer | 2/0/2 | 1 | 2 | P-2, KS-2 |
| **MK04** | Pemrograman Dasar | 1/2/3 | 1 | 4 | P-3, P-4, KS-2, KS-6 |
| **MK05** | Matematika Lanjut | 2/0/2 | 2 | 0 | Unmapped in matrix |
| **MK06** | Logika Informatika | 2/0/2 | 2 | 4 | P-1, KU-1, KU-4, KS-2 |
| **MK07** | Algoritma dan Struktur Data | 1/1/2 | 2 | 3 | P-5, KS-6, KS-11 |
| **MK08** | Analisis dan Desain Perangkat Lunak | 2/0/2 | 2 | 7 | P-1, P-7, KU-3, KU-4, KU-6, KS-3, KS-7 |
| **MK09** | Interaksi Manusia dan Komputer | 2/0/2 | 2 | 6 | P-6, KU-3, KU-4, KU-6, KS-1, KS-5 |
| **MK10** | Pengembangan Web Dasar | 1/2/3 | 2 | 8 | P-1, P-7, P-10, KU-3, KU-4, KS-1, KS-2, KS-7 |
| **MK11** | Praktik Analisis dan Desain Perangkat Lunak | 0/2/2 | 3 | 4 | P-7, KU-8, KS-3, KS-7 |
| **MK12** | Etika Profesi | 2/0/2 | 3 | 3 | S-11, P-23, KU-9 |
| **MK13** | Metode dan Model Pengembangan Perangkat Lunak | 2/0/2 | 3 | 5 | P-7, KU-3, KU-4, KS-3, KS-7 |
| **MK14** | Perancangan Antarmuka Pengguna | 1/1/2 | 3 | 3 | P-8, KS-5, KS-10 |
| **MK15** | Pemrograman Web Lanjut | 1/2/3 | 3 | 5 | P-7, P-12, KU-4, KS-4, KS-8 |
| **MK16** | Bahasa Pemrograman Berorientasi Objek | 1/2/3 | 3 | 3 | P-9, KS-2, KS-12 |
| **MK17** | Basis Data | 1/1/2 | 3 | 3 | P-10, KS-6, KS-9 |
| **MK18** | Manajemen Proyek | 2/0/2 | 4 | 3 | P-11, KU-2, KU-7 |
| **MK19** | Pengujian Perangkat Lunak | 1/1/2 | 4 | 2 | P-12, KS-9 |
| **MK20** | Praktik Algoritma dan Pemrograman | 1/2/3 | 4 | 4 | P-5, P-7, KS-6, KS-7 |
| **MK21** | Kerangka Kerja Pengembangan Antarmuka Website | 1/2/3 | 4 | 1 | KS-10 |
| **MK22** | Animasi dan Desain Multimedia | 1/2/3 | 4 | 3 | P-13, KS-11, KS-15 |
| **MK23** | Agile Development | 1/1/2 | 5 | 2 | P-14, KS-3 |
| **MK24** | Tata Kelola IT | 1/1/2 | 5 | 3 | P-15, KS-3, KS-6 |
| **MK25** | Pemrogramman Berbasis Framework | 1/2/3 | 5 | 4 | P-12, KS-3, KS-7, KS-12 |
| **MK26** | Kecerdasan Buatan | 1/2/3 | 5 | 4 | P-16, KS-1, KS-3, KS-12 |
| **MK27** | Arsitektur Perangkat Lunak | 1/1/2 | 5 | 3 | P-2, P-7, KS-13 |
| **MK28** | Pemasaran Digital | 1/2/3 | 6 | 2 | P-14, KS-14 |
| **MK29** | Pengembangan Game Dasar | 1/2/3 | 5 | 2 | P-18, KS-14 |
| **MK30** | Scrum Development | 0/2/2 | 6 | 2 | P-18, KS-3 |
| **MK31** | Pengembangan Aplikasi Seluler | 1/2/3 | 6 | 3 | P-19, KS-1, KS-8 |
| **MK32** | Keamanan Pengembangan Perangkat Lunak | 1/1/2 | 6 | 3 | P-20, KS-4, KS-10 |
| **MK33** | Machine Learning | 1/2/3 | 6 | 5 | P-21, KS-1, KS-2, KS-8, KS-12 |
| **MK34** | Pengembangan Game dan Teknologi Immersive | 1/2/3 | 6 | 4 | P-22, KS-1, KS-15, KS-16 |
| **MK35** | Internet of Things (IoT) | 1/1/2 | 6 | 1 | KU-10 |
| **MK36** | Agama | 2/0/2 | 1 | 1 | S-1 |
| **MK37** | Bahasa Indonesia | 3/0/3 | 1 | 1 | S-3 |
| **MK38** | Pola Pikir Kewirausahaan | 0/2/2 | 1 | 2 | S-10, KS-16 |
| **MK39** | Praktik Komunikasi dan Presentasi | 0/2/2 | 1 | 2 | KU-2, KU-4 |
| **MK40** | Seni dan Olahraga | 0/2/2 | 2 | 0 | Unmapped in matrix |
| **MK41** | Bahasa Inggris | 3/0/3 | 2 | 1 | KU-13 |
| **MK42** | Pengembangan Model Bisnis | 0/3/3 | 2 | 1 | KS-16 |
| **MK43** | Perintisan Bisnis | 0/3/3 | 3 | 1 | KS-16 |
| **MK44** | Sikap Religius | 1/0/1 | 4 | 2 | S-1, S-2 |
| **MK45** | Inovasi dan Pertumbuhan | 0/3/3 | 4 | 3 | KU-3, KU-10, KU-11 |
| **MK46** | Bahasa Inggris Bisnis | 1/2/3 | 4 | 1 | KU-13 |
| **MK47** | Mengelola Pertumbuhan Bisnis | 0/2/2 | 5 | 3 | S-10, S-11, KS-16 |
| **MK48** | Design Thinking | 2/0/2 | 6 | 3 | KU-4, KS-3, KS-7 |
| **MK49** | Metode Penelitian | 2/1/3 | 7 | 4 | P-17, KU-3, KU-4, KU-7 |
| **MK50** | Berpikir Kritis | 2/0/2 | 1 | 0 | Unmapped in matrix |
| **MK51** | KKN | 0/3/3 | 8 | 7 | S-2, S-3, S-4, S-5, S-6, S-7, S-8 |
| **MK52** | Pendidikan Kewarganegaraan | 2/0/2 | 5 | 7 | S-2, S-3, S-4, S-5, S-6, S-7, S-8 |

---

## 3. 10-COURSE GOLDEN SAMPLE AUDIT

Berikut adalah verifikasi sampel 10 mata kuliah lintas semester dan ranah kompetensi:

1. **MK01: Matematika Diskrit** (Sem 1, 2 SKS)
   - *Mapped CPL*: **0 CPL** (Unmapped pada matriks resmi Section 10.2).
2. **MK02: Pengenalan Rekayasa Perangkat Lunak** (Sem 1, 2 SKS)
   - *Mapped CPL*: **12 CPL** (`P-1`, `P-7`, `KU-3`, `KU-4`, `KU-5`, `KU-6`, `KS-1`, `KS-2`, `KS-3`, `KS-7`, `KS-8`, `KS-12`).
3. **MK04: Pemrograman Dasar** (Sem 1, 3 SKS)
   - *Mapped CPL*: **4 CPL** (`P-3`, `P-4`, `KS-2`, `KS-6`).
4. **MK07: Algoritma dan Struktur Data** (Sem 2, 2 SKS)
   - *Mapped CPL*: **3 CPL** (`P-5`, `KS-6`, `KS-11`).
5. **MK10: Pengembangan Web Dasar** (Sem 2, 3 SKS)
   - *Mapped CPL*: **8 CPL** (`P-1`, `P-7`, `P-10`, `KU-3`, `KU-4`, `KS-1`, `KS-2`, `KS-7`).
6. **MK17: Basis Data** (Sem 3, 2 SKS)
   - *Mapped CPL*: **3 CPL** (`P-10`, `KS-6`, `KS-9`).
7. **MK25: Pemrogramman Berbasis Framework** (Sem 5, 3 SKS)
   - *Mapped CPL*: **4 CPL** (`P-12`, `KS-3`, `KS-7`, `KS-12`).
8. **MK33: Machine Learning** (Sem 6, 3 SKS)
   - *Mapped CPL*: **5 CPL** (`P-21`, `KS-1`, `KS-2`, `KS-8`, `KS-12`).
9. **MK36: Agama** (Sem 1, 2 SKS)
   - *Mapped CPL*: **1 CPL** (`S-1`).
10. **MK51: KKN** (Sem 8, 3 SKS)
    - *Mapped CPL*: **7 CPL** (`S-2`, `S-3`, `S-4`, `S-5`, `S-6`, `S-7`, `S-8`).

---

## 4. STATUS PER KATEGORI CPL (64 CPL RESMI)

- **Sikap (S-1 s.d. S-11)**:
  - S-1: MK36, MK44
  - S-2: MK44, MK51, MK52
  - S-3: MK37, MK51, MK52
  - S-4 s.d. S-8: MK51, MK52
  - S-9: -
  - S-10: MK38, MK47
  - S-11: MK12, MK47
- **Pengetahuan (P-1 s.d. P-24)**:
  - P-1: MK02, MK06, MK08, MK10
  - P-2: MK03, MK27
  - P-3, P-4: MK04
  - P-5: MK07, MK20
  - P-6: MK09
  - P-7: MK02, MK08, MK10, MK11, MK13, MK15, MK20, MK27
  - P-8: MK14 | P-9: MK16 | P-10: MK10, MK17 | P-11: MK18 | P-12: MK15, MK19, MK25 | P-13: MK22 | P-14: MK23, MK28 | P-15: MK24 | P-16: MK26 | P-17: MK49 | P-18: MK29, MK30 | P-19: MK31 | P-20: MK32 | P-21: MK33 | P-22: MK34 | P-23: MK12 | P-24: -
- **Keterampilan Umum (KU-1 s.d. KU-13)**:
  - KU-1: MK06 | KU-2: MK18, MK39 | KU-3: MK02, MK08, MK09, MK10, MK13, MK45, MK49 | KU-4: MK02, MK06, MK08, MK09, MK10, MK13, MK15, MK39, MK48, MK49 | KU-5: MK02 | KU-6: MK02, MK08, MK09 | KU-7: MK18, MK49 | KU-8: MK11 | KU-9: MK12 | KU-10: MK35, MK45 | KU-11: MK45 | KU-12: - | KU-13: MK41, MK46
- **Keterampilan Khusus (KS-1 s.d. KS-16)**:
  - KS-1: MK02, MK09, MK10, MK26, MK31, MK33, MK34 | KS-2: MK02, MK03, MK04, MK06, MK10, MK16, MK33 | KS-3: MK02, MK08, MK11, MK13, MK23, MK24, MK25, MK26, MK30, MK48 | KS-4: MK15, MK32 | KS-5: MK09, MK14 | KS-6: MK04, MK07, MK17, MK20, MK24 | KS-7: MK02, MK08, MK10, MK11, MK13, MK20, MK25, MK48 | KS-8: MK02, MK15, MK31, MK33 | KS-9: MK17, MK19 | KS-10: MK14, MK21, MK32 | KS-11: MK07, MK22 | KS-12: MK02, MK16, MK25, MK26, MK33 | KS-13: MK27 | KS-14: MK28, MK29 | KS-15: MK22, MK34 | KS-16: MK34, MK38, MK42, MK43, MK47
