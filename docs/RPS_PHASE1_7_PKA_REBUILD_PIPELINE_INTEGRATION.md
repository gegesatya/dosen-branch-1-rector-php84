# DOKUMEN INTEGRASI PIPELINE REBUILD KURIKULUM PKA & FIX PAYLOAD STALE
**Fase:** Phase 1.7 — PKA Rebuild Pipeline Integration & Stale Payload Fix  
**Program Studi:** Pengelolaan Konvensi dan Acara (PKA)  
**Status Verdict:** `PKA_REBUILD_INTEGRATED`  
**Tanggal:** 2026-09-20  

---

## 1. Executive Summary & Root Cause Analysis

### A. Latar Belakang Masalah
1. Setelah audit manual Matriks III kurikulum Pengelolaan Konvensi dan Acara (PKA) diselesaikan dengan status `PKA_MATRIX_VALIDATED` (551 pasangan CPL-MK langsung, 50 MK katalog, 144 SKS, 48 MK terpetakan, 2 MK aktif di luar matriks), eksekusi tombol **"Bangun Ulang Data"** pada antarmuka admin kurikulum awalnya menghasilkan payload *legacy/stale* (hanya 1 MK terpetakan, 8 pasangan).
2. Setelah integrasi builder, muncul error validasi referensial:
   `Referential Integrity Error: effective_cpl_mk_pair uses invalid cpl_code 'S3'; 'KU2'; 'S6' ...`

### B. Akar Masalah Teknis (Root Cause)
1. **Hardcoded Legacy Stub di Builder**:
   Di dalam `application/controllers/C_kurikulum.php` dan `application/controllers/C_rps_generator.php`, pemetaan matriks PKA sebelumnya menggunakan *stub legacy* darurat yang hanya memetakan satu mata kuliah (`PKA32306`).
2. **Katalog MK Menggunakan Regex Parser Mentah**:
   Fungsi `_extract_curriculum_course_catalog()` mengandalkan multiline regex parsing alih-alih memanfaatkan katalog terverifikasi 50 MK (144 SKS).
3. **Format Standar Kode CPL (Hyphenation Discrepancy)**:
   Modul ekstraksi CPL standar kurikulum menghasilkan kode standar dengan tanda hubung (`S-1`, `KU-1`, `KK-1`, `P-1`). Namun, raw matrix PKA mendefinisikan kode tanpa tanda hubung (`S1`, `KU1`, `KK1`, `P1`). Hal ini menyebabkan `_validate_curriculum_integrity()` menolak 551 pasangan karena ketidakcocokan string literal pada `cpl_lookup`.
4. **Deteksi Sumber Pemetaan**:
   Fungsi `_detect_curriculum_mapping_source()` sebelumnya belum memiliki cabang `$is_pka`.

---

## 2. Corrected Execution Path Trace

Alur eksekusi saat pengguna menekan tombol **"Bangun Ulang Data"** kini sepenuhnya terintegrasi dan lolos validasi integritas:

```
[UI Admin Kurikulum: Tombol "Bangun Ulang Data"]
                      │
                      ▼ (AJAX POST)
[C_kurikulum::ajax_rebuild_curriculum]
  ├── Validasi Sesi & Otorisasi Pengguna
  ├── Membaca Dokumen Kurikulum Resmi Markdown
  ├── Memanggil _build_structured_curriculum_library()
  │     ├── _get_pka_course_catalog_data()   --> Menghasilkan 50 MK Authoritative (144 SKS)
  │     ├── _get_pka_matriks_data()          --> Menghasilkan 551 Pasangan CPL-MK Standar Canonical (S-1..S-11, KU-1..KU-9, KK-1..KK-6, P-1..P-7)
  │     ├── _get_pka_matrix_reconciliation_data() --> Menyematkan audit_summary & crosswalk (cpl_codes: S-1, S-2)
  │     └── _detect_curriculum_mapping_source()   --> Status: SOURCE_MAPPING_PARSED & SOURCE_MATRIX_COVERAGE_HIGH
  ├── HARD REBUILD GATE PKA:
  │     ├── Assert: MK count == 50
  │     ├── Assert: Total SKS == 144
  │     ├── Assert: Direct CPL-MK Pairs == 551
  │     ├── Assert: Mapped MKs == 48
  │     └── Assert: Outside Matrix MKs == 2
  ├── Validasi Integritas: _validate_structured_curriculum_library()
  │     ├── _validate_curriculum_integrity() --> 0 Referential Integrity Errors (100% PASSED)
  │     └── _validate_bahan_kajian_completeness() --> 0 Errors (100% PASSED)
  ├── Simpan normalized_json ke Database: siakad_pustaka_kurikulum (ID: 9)
  ├── Sinkronisasi Payload File JSON Statis:
  │     ├── application/config/kurikulum_payloads/normalized_Pengelolaan_Konvensi_dan_Acara.json
  │     └── uploads/pustaka_kurikulum/normalized_Pengelolaan_Konvensi_dan_Acara.json
  └── Return JSON Success Response ke UI
                      │
                      ▼ (Modal Reload)
[C_kurikulum::ajax_get_normalized_summary]
  └── Membaca normalized_json langsung dari DB (ID: 9) -> Menampilkan Metrik Valid 551 Pasangan & 50 MK
```

---

## 3. State Comparison (Before vs After)

| Parameter / Metrik | Kondisi Sebelum (Legacy/Stale) | Kondisi Sesudah (Production Rebuild Integrated) |
| :--- | :--- | :--- |
| **Katalog Mata Kuliah** | 49 MK (atau parsing parsial) | **50 Mata Kuliah Authoritative** |
| **Total SKS Kurikulum** | Tidak konsisten / 141 SKS | **144 SKS (Lengkap & Terdistribusi)** |
| **Pasangan CPL-MK Langsung** | 8 Pasangan (Hanya `PKA32306`) | **551 Pasangan CPL-MK Terverifikasi (Canonical Format)** |
| **MK Terpetakan di Matriks** | 1 MK | **48 MK** |
| **MK Aktif di Luar Matriks** | Tidak teridentifikasi jelas | **2 MK** (`PKA22307` & `PKA65306`) |
| **Group MK Alternatif Agama** | Tidak terakomodasi | **1 Grup (6 Varian Agama)** -> `S-1`, `S-2` |
| **Referential Integrity Validation** | Gagal (`invalid cpl_code 'S3'`) | **Lolos 100% (0 Error)** |
| **Matrix Parse Status** | `SOURCE_MAPPING_PARTIAL` | **`SOURCE_MAPPING_PARSED`** |
| **Matrix Coverage Status** | `PARTIAL` | **`SOURCE_MATRIX_COVERAGE_HIGH`** |
| **Manual Validation Status** | `PENDING` / Parsial | **`VALIDATED`** |
| **Alignment Readiness** | `NOT_YET_VALIDATED` | **`READY_FOR_ALIGNMENT_SCORING`** |
| **Database `siakad_pustaka_kurikulum` (ID 9)** | Mengandung stub 8 pasangan | **Tersimpan `normalized_json` 551 pasangan valid** |
| **File Konfigurasi Statis JSON** | Stale / Legacy | **Tersinkronisasi 100% dengan DB** |

---

## 4. Validasi Pemetaan Sampel Mata Kuliah PKA

| Kode MK | Nama Mata Kuliah | Status Matriks | Jumlah CPL Mapped | Bukti Direct (Max 65) | Bukti Jalur BK (Max 35) | Total Skor | Status Alignment |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `PKA12301` | General English 1 | Terpetakan | 10 CPL | 65 / 65 | 0 / 35 | 65.00% | `STRONG_DIRECT_CURRICULUM_EVIDENCE` |
| `PKA12302` | Pola Pikir Kewirausahaan | Terpetakan | 13 CPL | 65 / 65 | 0 / 35 | 65.00% | `STRONG_DIRECT_CURRICULUM_EVIDENCE` |
| `PKA12303` | Industri Acara | Terpetakan | 16 CPL | 65 / 65 | 0 / 35 | 65.00% | `STRONG_DIRECT_CURRICULUM_EVIDENCE` |
| `PKA31204` | Pendidikan Agama (6 Varian) | Terpetakan | 2 CPL (`S-1`, `S-2`) | 65 / 65 | 35 / 35 | 100.00% | `VERY_STRONG_CURRICULUM_EVIDENCE` |
| `PKA32306` | Pengelolaan Acara Korporasi | Terpetakan | 23 CPL | 65 / 65 | 9.13 / 35 | 74.13% | `STRONG_DIRECT_CURRICULUM_EVIDENCE` |
| `PKA51307` | Pancasila & PKn | Terpetakan | 9 CPL | 65 / 65 | 27.22 / 35 | 92.22% | `VERY_STRONG_CURRICULUM_EVIDENCE` |
| `PKA22307` | Aplikasi Komputer | Di Luar Matriks | 0 CPL | 0 / 65 | 0 / 35 | 0.00% | `NO_CONFIRMED_OFFICIAL_ALIGNMENT` |
| `PKA65306` | Manajemen Resiko Acara | Di Luar Matriks | 0 CPL | 0 / 65 | 0 / 35 | 0.00% | `NO_CONFIRMED_OFFICIAL_ALIGNMENT` |

---

## 5. Regresi Antar 5 Program Studi

```
====================================================================================================================================================
ID   | Nama Prodi                     | CPL  | BK  | MK   | SKS  | Matrix Coverage      | Pairs (Direct)| Coverage Status            | Parse Status            
====================================================================================================================================================
6    | Teknologi Rekayasa Perangkat L | 64   | 11  | 57   | 149  | 52 (0c:4, out:5)     | 162 (dir:48)  | PARTIAL_BY_SOURCE_DESIGN   | SOURCE_MAPPING_PARSED 
8    | Agribisnis Hortikultura        | 48   | 20  | 66   | 150  | 66                   | 271 (dir:66)  | FULL                       | SOURCE_MAPPING_PARSED 
9    | Pengelolaan Konvensi dan Acara | 33   | 32  | 50   | 144  | 49 (0c:0, out:2)     | 551 (dir:48)  | SOURCE_MATRIX_COVERAGE_HIGH| SOURCE_MAPPING_PARSED 
10   | Manajemen Pemasaran Internasio | 50   | 10  | 69   | 183  | 49 (0c:0, out:20)    | 598 (dir:49)  | PARTIAL_BY_SOURCE_DESIGN   | SOURCE_MAPPING_PARSED 
11   | Akuntansi Perpajakan           | 73   | 13  | 50   | 148  | 54                   | 182 (dir:50)  | FULL                       | SOURCE_MAPPING_PARSED 
====================================================================================================================================================
```

---

## 6. Kesimpulan & Status Akhir

1. **Rebuild Modal UI & Endpoint Berhasil**: Modal tidak lagi menampilkan error referensial dan dapat melakukan rebuild secara mulus.
2. **Standardisasi Kode CPL Berjalan Sempurna**: Kode CPL PKA telah selaras dengan format kanonikal berskala nasional/prodi (`S-1`..`S-11`, `KU-1`..`KU-9`, `KK-1`..`KK-6`, `P-1`..`P-7`).
3. **Sistem Scoring Keselarasan Siap Digunakan**: Perhitungan skor keselarasan kurikulum 65/35 terbukti akurat dan deterministik.

**Final Verdict:** `PKA_REBUILD_INTEGRATED` (Phase 1.7 Selesai).
