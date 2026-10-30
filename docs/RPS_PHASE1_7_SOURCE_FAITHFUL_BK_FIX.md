# PHASE 1.7 — SOURCE-FAITHFUL BK CANONICALIZATION AUDIT REPORT

**Dokumen Referensi:** Kurikulum Resmi 5 Program Studi Politeknik Wilmar Bisnis Indonesia (WBI)  
**Status Audit:** APPROVED & VERIFIED  
**Final Verdict:** `READY_FOR_PHASE_2`  
**Tanggal Audit:** 18 September 2026  

---

## 1. Executive Summary & Problem Resolution

Pada Phase 1.7, dilakukan audit komprehensif terhadap seluruh Master Bahan Kajian (BK) yang tersimpan di Structured Curriculum Library (`siakad_pustaka_kurikulum`). 

### Masalah Aktual Sebelumnya:
1. Beberapa Bahan Kajian hasil normalisasi awal mengalami penggabungan semantik atau penamaan ulang berbasis interpretasi eksternal (misalnya pada Agribisnis Hortikultura: `Ekonomi Mikro dan Makro`, `Kuantitatif dan Riset Operasi`).
2. Pada TRPL, penamaan sempat berpotensi bergeser ke istilah generik framework luar.
3. Ketiadaan pelacakan eksplisit antara kode sumber (`source_code`) dan kode canonical sistem (`code`), serta nama sumber literal (`source_name`) dan nama display (`name`).

### Resolusi yang Diterapkan:
1. **Source of Truth Absolut:** Naskah PDF kurikulum resmi setiap program studi dijadikan acuan tunggal literal tanpa reinterpretasi akademik, pemecahan/penggabungan BK, maupun penggantian istilah eksternal.
2. **Canonical BK Object v1.0:** Seluruh BK menyimpan atribut provenance lengkap:
   ```json
   {
     "code": "...",
     "source_code": "...",
     "name": "...",
     "source_name": "...",
     "description": "...",
     "source_section": "...",
     "is_official": true,
     "is_normalized_code": false,
     "name_normalization": "NONE"
   }
   ```
3. **Rebuild & Reset Status:** Seluruh 5 pustaka kurikulum telah di-rebuild dan di-reset ke status `NORMALIZED` dengan integritas schema 100% valid.

---

## 2. Master Bahan Kajian Audit Table (5 Program Studi)

### 1. Teknologi Rekayasa Perangkat Lunak (TRPL - ID 6)
- **Source Section:** `8.1. Standar Association for Computing Machinery dan Institute of Electrical and Electronics Engineers`
- **Total Master BK:** 11 Butir
- **Kode Sumber Dokumen:** `1` s.d. `11` | **Kode Sistem:** `BK-1` s.d. `BK-11` (`is_normalized_code: true`)

| No | Normalized Code | Source Code | Normalized Name | Source Name | Exact Match? | Normalization Type | Source Section |
|:---|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `BK-1` | `1` | Prinsip-prinsip Software Engineering | Prinsip-prinsip Software Engineering | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 2 | `BK-2` | `2` | Metodologi Pengembangan Perangkat Lunak | Metodologi Pengembangan Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 3 | `BK-3` | `3` | Rekayasa Kebutuhan | Rekayasa Kebutuhan | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 4 | `BK-4` | `4` | Arsitektur Perangkat Lunak | Arsitektur Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 5 | `BK-5` | `5` | Pengujian Perangkat Lunak | Pengujian Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 6 | `BK-6` | `6` | Manajemen Proyek Perangkat Lunak | Manajemen Proyek Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 7 | `BK-7` | `7` | Pengelolaan Konfigurasi dan Versi | Pengelolaan Konfigurasi dan Versi | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 8 | `BK-8` | `8` | Kualitas Perangkat Lunak | Kualitas Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 9 | `BK-9` | `9` | Keamanan Perangkat Lunak | Keamanan Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |
| 10 | `BK-10` | `10` | Interaksi Manusia-Komputer | Interaksi Manusia-Komputer (Human-Computer Interaction, HCI) | YES (Display) | CODE_NORM + DISPLAY_ONLY | 8.1 Standar ACM/IEEE |
| 11 | `BK-11` | `11` | Pemrograman dan Teknologi Perangkat Lunak | Pemrograman dan Teknologi Perangkat Lunak | YES | CODE_NORM | 8.1 Standar ACM/IEEE |

---

### 2. Agribisnis Hortikultura (AH - ID 8)
- **Source Section:** `BAHAN KAJIAN (* Daftar Bahan Kajian)`
- **Total Master BK:** 20 Butir
- **Kode Sumber Dokumen:** `1.1` s.d. `6.4` | **Kode Sistem:** `1.1` s.d. `6.4` (`is_normalized_code: false`)

| No | Normalized Code | Source Code | Normalized Name | Source Name | Exact Match? | Normalization Type | Source Section |
|:---|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `1.1` | `1.1` | Kebijakan bisnis | Kebijakan bisnis | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 2 | `1.2` | `1.2` | Keuangan dan pembiayaan | Keuangan dan pembiayaan | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 3 | `1.3` | `1.3` | Pemasaran | Pemasaran | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 4 | `1.4` | `1.4` | Operasional agribisnis | Operasional agribisnis | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 5 | `1.5` | `1.5` | Manajemen | Manajemen | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 6 | `2.1` | `2.1` | Ekonomi Mikro | Ekonomi Mikro | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 7 | `2.2` | `2.2` | Ekonomi Makro | Ekonomi Makro | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 8 | `2.3` | `2.3` | Ekonomi pertanian | Ekonomi pertanian | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 9 | `2.4` | `2.4` | Ekonomi sumber daya | Ekonomi sumber daya | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 10 | `3.1` | `3.1` | Kebijakan pertanian | Kebijakan pertanian | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 11 | `3.2` | `3.2` | Bisnis internasional | Bisnis internasional | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 12 | `4.1` | `4.1` | Strategi komunikasi | Strategi komunikasi | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 13 | `4.2` | `4.2` | Metode komunikasi | Metode komunikasi | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 14 | `4.3` | `4.3` | Sumberdaya komunikasi | Sumberdaya komunikasi | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 15 | `5.1` | `5.1` | Pendekatan kualitatif | Pendekatan kualitatif | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 16 | `5.2` | `5.2` | Pendekatan kuantitatif | Pendekatan kuantitatif | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 17 | `6.1` | `6.1` | Ilmu tanaman | Ilmu tanaman | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 18 | `6.2` | `6.2` | Ilmu Media | Ilmu Media | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 19 | `6.3` | `6.3` | Teknologi | Teknologi | YES | NONE | BAHAN KAJIAN (* Daftar BK) |
| 20 | `6.4` | `6.4` | Lingkungan | Lingkungan | YES | NONE | BAHAN KAJIAN (* Daftar BK) |

---

### 3. Pengelolaan Konvensi dan Acara (PKA - ID 9)
- **Source Section:** `BAHAN KAJIAN (EMBOK)`
- **Total Master BK:** 32 Butir
- **Kode Sumber Dokumen:** `1.1` s.d. `5.6` | **Kode Sistem:** `1.1` s.d. `5.6` (`is_normalized_code: false`)

| No | Normalized Code | Source Code | Normalized Name | Source Name | Exact Match? | Normalization Type | Source Section |
|:---|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `1.1` | `1.1` | Financial Management | Financial Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 2 | `1.2` | `1.2` | Human Resources Management | Human Resources Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 3 | `1.3` | `1.3` | Information Management | Information Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 4 | `1.4` | `1.4` | Precurement Management | Precurement Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 5 | `1.5` | `1.5` | System Management | System Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 6 | `1.6` | `1.6` | Techonology Management | Techonology Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 7 | `1.7` | `1.7` | Time Management | Time Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 8 | `2.1` | `2.1` | Catering | Catering | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 9 | `2.2` | `2.2` | Content | Content | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 10 | `2.3` | `2.3` | Entertainment | Entertainment | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 11 | `2.4` | `2.4` | Enviroment | Enviroment | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 12 | `2.5` | `2.5` | Production | Production | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 13 | `2.6` | `2.6` | Program | Program | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 14 | `2.7` | `2.7` | Theme | Theme | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 15 | `3.1` | `3.1` | Audience Management | Audience Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 16 | `3.2` | `3.2` | Communication | Communication | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 17 | `3.3` | `3.3` | Infrastructure | Infrastructure | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 18 | `3.4` | `3.4` | Logistics | Logistics | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 19 | `3.5` | `3.5` | Program Design Management | Program Design Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 20 | `3.6` | `3.6` | Site Management | Site Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 21 | `3.7` | `3.7` | Stakeholder Management | Stakeholder Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 22 | `3.8` | `3.8` | Technical Production | Technical Production | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 23 | `4.1` | `4.1` | Hospitality Management | Hospitality Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 24 | `4.2` | `4.2` | Marketing Plan | Marketing Plan | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 25 | `4.3` | `4.3` | Materials Management | Materials Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 26 | `4.4` | `4.4` | Merchandising | Merchandising | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 27 | `5.1` | `5.1` | Compliance | Compliance | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 28 | `5.2` | `5.2` | Emergency Management | Emergency Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 29 | `5.3` | `5.3` | Health & Safety Management | Health & Safety Management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 30 | `5.4` | `5.4` | Insurance management | Insurance management | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 31 | `5.5` | `5.5` | Legal & Ethnic | Legal & Ethnic | YES | NONE | BAHAN KAJIAN (EMBOK) |
| 32 | `5.6` | `5.6` | Security Managemen | Security Managemen | YES | NONE | BAHAN KAJIAN (EMBOK) |

---

### 4. Manajemen Pemasaran Internasional (MPI - ID 10)
- **Source Section:** `8. Penetapan Bahan Kajian`
- **Total Master BK:** 10 Butir
- **Kode Sumber Dokumen:** `BK-1` s.d. `BK-10` | **Kode Sistem:** `BK-1` s.d. `BK-10` (`is_normalized_code: false`)

| No | Normalized Code | Source Code | Normalized Name | Source Name | Exact Match? | Normalization Type | Source Section |
|:---|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `BK-1` | `BK-1` | Entrepreneurship (Kewirausahaan) | Entrepreneurship (Kewirausahaan) | YES | NONE | 8. Penetapan Bahan Kajian |
| 2 | `BK-2` | `BK-2` | Pemasaran | Pemasaran | YES | NONE | 8. Penetapan Bahan Kajian |
| 3 | `BK-3` | `BK-3` | Organisasi dan Manajemen | Organisasi dan Manajemen | YES | NONE | 8. Penetapan Bahan Kajian |
| 4 | `BK-4` | `BK-4` | Keuangan | Keuangan | YES | NONE | 8. Penetapan Bahan Kajian |
| 5 | `BK-5` | `BK-5` | Ilmu Ekonomi | Ilmu Ekonomi | YES | NONE | 8. Penetapan Bahan Kajian |
| 6 | `BK-6` | `BK-6` | Metode Kuantitatif | Metode Kuantitatif | YES | NONE | 8. Penetapan Bahan Kajian |
| 7 | `BK-7` | `BK-7` | Teknologi Informasi | Teknologi Informasi | YES | NONE | 8. Penetapan Bahan Kajian |
| 8 | `BK-8` | `BK-8` | Akuntansi | Akuntansi | YES | NONE | 8. Penetapan Bahan Kajian |
| 9 | `BK-9` | `BK-9` | Komunikasi Bisnis | Komunikasi Bisnis | YES | NONE | 8. Penetapan Bahan Kajian |
| 10 | `BK-10` | `BK-10` | Pengembangan Karakter | Pengembangan Karakter | YES | NONE | 8. Penetapan Bahan Kajian |

---

### 5. Akuntansi Perpajakan (AP - ID 11)
- **Source Section:** `VIII. BAHAN KAJIAN / BODY OF KNOWLEDGE`
- **Total Master BK:** 13 Butir
- **Kode Sumber Dokumen:** `BK-1` s.d. `BK-13` | **Kode Sistem:** `BK-1` s.d. `BK-13` (`is_normalized_code: false`)

| No | Normalized Code | Source Code | Normalized Name | Source Name | Exact Match? | Normalization Type | Source Section |
|:---|:---|:---|:---|:---|:---:|:---:|:---|
| 1 | `BK-1` | `BK-1` | Akuntansi Keuangan | Akuntansi Keuangan | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 2 | `BK-2` | `BK-2` | Akuntansi Manajemen | Akuntansi Manajemen | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 3 | `BK-3` | `BK-3` | Perpajakan | Perpajakan | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 4 | `BK-4` | `BK-4` | Auditing | Auditing | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 5 | `BK-5` | `BK-5` | Sistem Informasi | Sistem Informasi | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 6 | `BK-6` | `BK-6` | Keuangan (Finance) | Keuangan (Finance) | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 7 | `BK-7` | `BK-7` | Organisasi dan Bisnis | Organisasi dan Bisnis | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 8 | `BK-8` | `BK-8` | Ekonomi | Ekonomi | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 9 | `BK-9` | `BK-9` | Komunikasi | Komunikasi | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 10 | `BK-10` | `BK-10` | Teknologi Informasi | Teknologi Informasi | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 11 | `BK-11` | `BK-11` | Metode Kuantitatif | Metode Kuantitatif | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 12 | `BK-12` | `BK-12` | Pengembangan Karakter | Pengembangan Karakter | YES | NONE | VIII. BAHAN KAJIAN / BOK |
| 13 | `BK-13` | `BK-13` | Kewirausahaan | Kewirausahaan | YES | NONE | VIII. BAHAN KAJIAN / BOK |

---

## 3. Course Grounding Regression Validation (6 Target Courses)

Seluruh 6 target course telah diuji secara menyeluruh untuk memastikan tidak ada degradasi (zero regression) pada pipeline downstream generator dan visualisasi kurikulum:

| No | Program Studi | Kode MK | Nama Mata Kuliah | Grounding Type | Expected CPL / BK | Result | Status |
|:---:|:---|:---|:---|:---:|:---:|:---:|:---:|
| 1 | TRPL (ID 6) | `TRPL3201` | Pemrograman Berbasis Framework | `EXPLICIT_MATRIX` | `KS-12` | Direct CPL `KS-12` (1 CPL) + 11 Master BK | **PASSED** ✅ |
| 2 | AP (ID 11) | `602452` | Perpajakan Internasional | `EXPLICIT_MATRIX` | `P-12` | Direct CPL `P-12` (1 CPL) + 13 Master BK | **PASSED** ✅ |
| 3 | PKA (ID 9) | `PKA4205` | Pengelolaan Acara Korporasi | `EXPLICIT_MATRIX` | `S-1`..`P-2` | 8 Matriks CPL (`S-1`,`S-2`,`KU-1`,`KU-2`,`KK-1`,`KK-2`,`P-1`,`P-2`) + 32 Master BK | **PASSED** ✅ |
| 4 | MPI (ID 10) | `MPI63306` | Penelitian  Pemasaran | `EXPLICIT_MATRIX` | `P-6` | Direct CPL `P-6` (1 CPL) + 10 Master BK | **PASSED** ✅ |
| 5 | AH (ID 8) | `AGH42204` | Manajemen Agribisnis | `DERIVED_VIA_BK` | `1.5` | Target BK `1.5` + 6 Derived CPL (`A2`,`B4`,`C1`,`C2`,`E1`,`E4`) | **PASSED** ✅ |
| 6 | AH (ID 8) | `AGH52208` | Riset Pemasaran | `DERIVED_VIA_BK` | `1.3` | Target BK `1.3` + 7 Derived CPL (`A2`,`B3`,`B4`,`C1`,`C2`,`E1`,`E4`) | **PASSED** ✅ |

---

## 4. UI Rendering Enhancement

Pada antarmuka admin kurikulum (`v_kurikulum_list.php` & `v_kurikulum_list_after_page.php`), Tab 2 (Bahan Kajian) kini menyajikan:
1. **Kode BK (Canonical Code)**: Menampilkan kode representasi sistem.
2. **Kode Sumber (Source Code)**: Menampilkan penomoran literal pada dokumen kurikulum.
3. **Nama Bahan Kajian**: Menampilkan nama BK dengan catatan teks sumber literal jika disederhanakan untuk display.
4. **Badge Status & Fidelity**: 
   - `SOURCE-EXACT` (Hijau) untuk penamaan literal yang identik dengan sumber.
   - `NORMALIZED-CODE` (Biru) untuk kode yang dinormalisasi dengan aman oleh sistem.
5. **Deskripsi & Source Section**: Menampilkan konteks bab/tabel asal dokumen kurikulum.

---

## 5. Final Verdict

$$\mathbf{VERDICT: \quad READY\_FOR\_PHASE\_2}$$

Seluruh 5 pustaka kurikulum resmi WBI telah terverifikasi memenuhi standar **Source Fidelity** mutlak, lulus validasi skema kurikulum v1.0, dan siap digunakan sebagai dasar perumusan CPMK dan asesmen OBE pada Phase 2.
