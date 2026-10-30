# PHASE 1.7 — GLOBAL BAHAN KAJIAN (BK) EXTRACTION & COMPLETENESS AUDIT REPORT

**Project:** Politeknik Wilmar Bisnis Indonesia (WBI) — Structured Curriculum Library  
**Phase:** Phase 1.7 (Global Bahan Kajian Extraction & Completeness Audit)  
**Date:** 18 September 2026  
**Status:** **READY_FOR_PHASE_2**  

---

## 1. Executive Summary

Audit komprehensif ekstraksi **Bahan Kajian (BK)** telah berhasil dilaksanakan pada seluruh **5 Program Studi** di Structured Curriculum Library. Perbaikan ini membedakan secara tegas dan ketat antara **Master Bahan Kajian resmi**, **relasi BK-MK**, dan **relasi CPL-BK**, serta mengeliminasi seluruh polusi dokumen PDF, duplikasi, maupun nomor bab yang sebelumnya sempat tertangkap secara parsial.

Seluruh 5 pustaka kurikulum telah dibangun ulang (*rebuilt*) dengan format Canonical BK Object, melewati validasi ketat `_validate_bahan_kajian_completeness()`, dan tersimpan dengan status `VERIFIED`.

---

## 2. Source Section & Parser Strategy per Program Studi

| No | Program Studi | Source Section Heading | Source Boundary / Format | Parser Strategy |
|:---|:---|:---|:---|:---|
| 1 | **Teknologi Rekayasa Perangkat Lunak (TRPL)** | `Bahan Kajian/Body of Knowledge (ACM/IEEE-CS)` | Section 8 ACM/IEEE-CS Software Engineering BOK (`BK-1` s.d `BK-11`) | Deterministic ACM/IEEE-CS SE BOK parser with regex pattern matching boundary preceding Section 9/10/Matriks |
| 2 | **Akuntansi Perpajakan (AP)** | `Bahan Kajian / Body of Knowledge` | Tabel Body of Knowledge IAI/Akuntansi Perpajakan (`BK-1` s.d `BK-13`) | Multi-line robust table parser matching `BK-1` s.d `BK-13` with multi-line label tolerance |
| 3 | **Pengelolaan Konvensi dan Acara (PKA)** | `BAHAN KAJIAN (EMBOK)` | 5 Domain Event Management Body of Knowledge (EMBOK: `1.1` s.d `5.6`) | Hierarchical EMBOK domain extractor mapping Administration (1.1–1.7), Design (2.1–2.7), Operations (3.1–3.8), Marketing (4.1–4.4), Risk Management (5.1–5.6) |
| 4 | **Manajemen Pemasaran Internasional (MPI)** | `Penetapan Bahan Kajian` | Section Penetapan Bahan Kajian (`BK-1` s.d `BK-10`) | Robust code+name line extractor with multi-line wrap resolution (mengatasi PDF table misalignment) |
| 5 | **Agribisnis Hortikultura (AH)** | `BAHAN KAJIAN` | 6 Domain Agribisnis Hortikultura (`1.1` s.d `6.4`) | Strict numeric 2-level hierarchical extractor (`1.1`..`6.4`) parsing 20 official items |

---

## 3. Before vs After Master BK Count & Extraction Audit Table

| Program Studi | BK Count Existing (Before) | BK Count Audit (After) | Status Ekstraksi | CPL-BK Status | BK-MK Pairs | Validation Result |
|:---|:---:|:---:|:---:|:---:|:---:|:---:|
| **Teknologi Rekayasa Perangkat Lunak (TRPL)** | 8 *(polusi)* | **11** | `COMPLETE` | `AVAILABLE_UNPARSED` | 0 (Direct CPL-MK) | **PASS (VERIFIED)** |
| **Akuntansi Perpajakan (AP)** | 10 *(parsial)* | **13** | `COMPLETE` | `AVAILABLE_UNPARSED` | 0 (Direct CPL-MK) | **PASS (VERIFIED)** |
| **Pengelolaan Konvensi dan Acara (PKA)** | 32 | **32** | `COMPLETE` | `AVAILABLE_UNPARSED` | 0 (Direct CPL-MK) | **PASS (VERIFIED)** |
| **Manajemen Pemasaran Internasional (MPI)** | 1 *(tabel rusak)* | **10** | `COMPLETE` | `AVAILABLE_UNPARSED` | 0 (Direct CPL-MK) | **PASS (VERIFIED)** |
| **Agribisnis Hortikultura (AH)** | 20 | **20** | `COMPLETE` | `PARSED` (25 CPL) | 6 (Resolved) | **PASS (VERIFIED)** |

---

## 4. Full Master Bahan Kajian (BK) List per Program Studi

### A. Teknologi Rekayasa Perangkat Lunak (11 BK)
1. `BK-1` : Computing Essentials
2. `BK-2` : Mathematical and Engineering Fundamentals
3. `BK-3` : Professional Practice
4. `BK-4` : Software Modeling and Analysis
5. `BK-5` : Requirements Analysis and Specification
6. `BK-6` : Software Design
7. `BK-7` : Software Verification and Validation
8. `BK-8` : Software Process
9. `BK-9` : Software Quality
10. `BK-10` : Security
11. `BK-11` : Software Management

### B. Akuntansi Perpajakan (13 BK)
1. `BK-1` : Etika Profesi dan Tata Kelola
2. `BK-2` : Akuntansi Keuangan
3. `BK-3` : Akuntansi Manajemen
4. `BK-4` : Perpajakan
5. `BK-5` : Sistem Informasi Akuntansi
6. `BK-6` : Auditing dan Asurans
7. `BK-7` : Manajemen Keuangan
8. `BK-8` : Hukum Bisnis dan Regulasi
9. `BK-9` : Ekonomi dan Bisnis
10. `BK-10` : Statistika dan Metode Kuantitatif
11. `BK-11` : Manajemen Strategik dan Kepemimpinan
12. `BK-12` : Komunikasi Bisnis dan Kewirausahaan
13. `BK-13` : Pengembangan Karakter dan Kebangsaan

### C. Pengelolaan Konvensi dan Acara (32 BK — EMBOK)
- **Domain Administration (1.1 - 1.7):**
  1. `1.1` : Financial Management
  2. `1.2` : Human Resources Management
  3. `1.3` : Information Management
  4. `1.4` : Procurement Management
  5. `1.5` : Stakeholder Management
  6. `1.6` : Systems Management
  7. `1.7` : Time Management
- **Domain Design (2.1 - 2.7):**
  8. `2.1` : Content Management
  9. `2.2` : Theme Management
  10. `2.3` : Program Management
  11. `2.4` : Environment Management
  12. `2.5` : Production Management
  13. `2.6` : Entertainment Management
  14. `2.7` : Catering Management
- **Domain Operations (3.1 - 3.8):**
  15. `3.1` : Attendees Management
  16. `3.2` : Communications Management
  17. `3.3` : Infrastructure Management
  18. `3.4` : Logistics Management
  19. `3.5` : Participant Management
  20. `3.6` : Site Management
  21. `3.7` : Technical Management
  22. `3.8` : Venue Management
- **Domain Marketing (4.1 - 4.4):**
  23. `4.1` : Marketing Plan Management
  24. `4.2` : Materials Management
  25. `4.3` : Merchandise Management
  26. `4.4` : Promotion Management
- **Domain Risk Management (5.1 - 5.6):**
  27. `5.1` : Compliance Management
  28. `5.2` : Emergency Management
  29. `5.3` : Health and Safety Management
  30. `5.4` : Insurance Management
  31. `5.5` : Security Management
  32. `5.6` : Risk Assessment Management

### D. Manajemen Pemasaran Internasional (10 BK)
1. `BK-1` : Entrepreneurship / Kewirausahaan
2. `BK-2` : Pemasaran
3. `BK-3` : Organisasi dan Manajemen
4. `BK-4` : Keuangan
5. `BK-5` : Ilmu Ekonomi
6. `BK-6` : Metode Kuantitatif
7. `BK-7` : Teknologi Informasi
8. `BK-8` : Akuntansi
9. `BK-9` : Komunikasi Bisnis
10. `BK-10` : Pengembangan Karakter

### E. Agribisnis Hortikultura (20 BK)
- **Domain 1 — Bisnis & Manajemen:**
  1. `1.1` : Kebijakan Bisnis
  2. `1.2` : Keuangan dan Pembiayaan
  3. `1.3` : Pemasaran
  4. `1.4` : Rantai Pasok
  5. `1.5` : Manajemen
- **Domain 2 — Ekonomi & Pembangunan:**
  6. `2.1` : Ekonomi Mikro dan Makro
  7. `2.2` : Pembangunan Pertanian
  8. `2.3` : Sosiologi Pedesaan
  9. `2.4` : Komunikasi dan Penyuluhan
- **Domain 3 — Kuantitatif & Penelitian:**
  10. `3.1` : Kuantitatif dan Riset Operasi
  11. `3.2` : Metode Penelitian
- **Domain 4 — Teknologi & Produksi:**
  12. `4.1` : Teknologi Produksi Hortikultura
  13. `4.2` : Pasca Panen dan Pengolahan Hasil
  14. `4.3` : Bioteknologi Pertanian
- **Domain 5 — Sumber Daya & Lingkungan:**
  15. `5.1` : Sumber Daya Lahan dan Air
  16. `5.2` : Lingkungan dan Keberlanjutan
- **Domain 6 — Kelembagaan & Kebijakan:**
  17. `6.1` : Kelembagaan Agribisnis
  18. `6.2` : Hukum dan Etika Bisnis
  19. `6.3` : Standardisasi dan Sertifikasi Mutu
  20. `6.4` : Kewirausahaan Agribisnis

---

## 5. Referential Integrity & Validation Checks

1. **Master BK Canonical Validation (`_validate_bahan_kajian_completeness`):**
   - **Unique Code Check:** 100% PASS (seluruh kode BK unik, tidak ada duplikasi).
   - **Non-empty Name Check:** 100% PASS (seluruh nama BK terisi dan sesuai terminologi resmi kurikulum).
   - **Pollution Check:** 100% PASS (tidak ada nomor bab `2.1`, `3.1`, atau heading seperti `Landasan Filosofi` yang mengotori master BK).
   - **No Course Masquerading:** 100% PASS (mata kuliah dan bahan kajian terpisah secara tegas).
2. **BK-MK Referential Integrity:**
   - Seluruh `bk_mk_pairs[].bk_code` ter-resolve ke `bahan_kajian[].code`.
   - Seluruh `bk_mk_pairs[].mk_code` / `mk_name` ter-resolve ke `mata_kuliah[]`.
3. **CPL-BK Referential Integrity:**
   - Seluruh `cpl_bk_pairs[].bk_code` ter-resolve ke `bahan_kajian[].code`.
   - Seluruh `cpl_bk_pairs[].cpl_code` ter-resolve ke `official_cpl[].code`.
   - Status CPL-BK tetap terisolasi: `PARSED` untuk AH (25 CPL terpetakan ke 20 BK) dan `AVAILABLE_UNPARSED` untuk TRPL, AP, PKA, MPI.

---

## 6. Target Course Grounding Regression Verification

Semua course target untuk 5 Program Studi berhasil diuji dengan akurasi 100%:

```
--- STEP 4: VERIFY 6 TARGET COURSES GROUNDING VIA STRUCTURED LIBRARY ---
  [PASS] TRPL: Pemrograman Berbasis Framework
         Mapping Type: EXPLICIT_MATRIX | Mapped CPLs: (KS-12 [Total: 1]) | SKS: 3 | Sem: 5
  [PASS] AP: Perpajakan Internasional
         Mapping Type: EXPLICIT_MATRIX | Mapped CPLs: (P-12 [Total: 1]) | SKS: 2 | Sem: 6
  [PASS] PKA: Pengelolaan Acara Korporasi
         Mapping Type: EXPLICIT_MATRIX | Mapped CPLs: (S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2 [Total: 8]) | SKS: 3 | Sem: 3
  [PASS] MPI: Riset Pemasaran
         Mapping Type: EXPLICIT_MATRIX | Mapped CPLs: (P-6 [Total: 1]) | SKS: 3 | Sem: 6
  [PASS] AH: Manajemen Agribisnis
         Mapping Type: DERIVED_VIA_BK | Mapped CPLs: (A2, B4, C1, C2, E1, E4 [Total: 6]) | SKS: 2 | Sem: 4
  [PASS] AH: Riset Pemasaran
         Mapping Type: DERIVED_VIA_BK | Mapped CPLs: (A2, B3, B4, C1, C2, E1, E4 [Total: 7]) | SKS: 2 | Sem: 5
```

---

## 7. Admin UI Summary & Diagnostics

Admin UI pada `application/views/kurikulum/v_kurikulum_list.php` dan `application/views/kurikulum/v_kurikulum_list_after_page.php` telah disempurnakan dengan:
1. **Stat Card BK:** Menampilkan jumlah Master BK resmi beserta subtext status ekstraksi (`COMPLETE`) dan nama heading section sumber kurikulum.
2. **Tab 2 Bahan Kajian:** Menampilkan badge jumlah Master BK: `2. Bahan Kajian (X)`.
3. **Diagnostic Alert:** Menampilkan banner verifikasi status ekstraksi (`COMPLETE`) dan nama heading section sumber resmi di bagian atas tabel Master BK.

---

## 8. Final Verdict

**`READY_FOR_PHASE_2`**
