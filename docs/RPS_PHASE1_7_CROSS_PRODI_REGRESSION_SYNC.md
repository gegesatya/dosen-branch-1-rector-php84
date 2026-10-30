# LAPORAN SINKRONISASI BASELINE REGRESI LINTAS PRODI
**Fase:** Phase 1.7 — Cross-Prodi Regression Baseline Sync  
**Modul:** Kurikulum & Structured Curriculum Library (`C_kurikulum.php`, `C_rps_generator.php`, `siakad_pustaka_kurikulum`)  
**Status Baseline:** `REGRESSION_BASELINE_SYNCED`  
**Tanggal Eksekusi:** 2026-09-19  

---

## 1. AUDIT BASELINE SOURCE & ROOT CAUSE EXPLANATION

### Temuan Sumber Angka Lama (331):
1. Angka **331** berasal dari parser vektor awal koordinat Matriks II Agribisnis Hortikultura sebelum dilakukan koreksi *false-positive vector detection*. Pada parser awal, garis *bounding box* tabel pada baris 22 (*Ekonomi Pertanian*) dan baris 61 (*Metodologi Penelitian*) terbaca sebagai sel terisi (23 dan 26 butir CPL).
2. Setelah audit manual mendalam terhadap vektor sel warna biru Matriks II PDF, diperoleh hasil deterministik:
   - **Direct CPL-MK Pairs (Scalar):** **271**
   - **Aggregate Domain Mappings (`TATA_NILAI`):** **6**
3. Di dalam array `_get_ah_matriks_ii_data()` pada `C_kurikulum.php` dan `C_rps_generator.php`, dataset telah diperbarui secara penuh ke 271 relasi skalar langsung dan 6 pemetaan agregat domain.
4. Seluruh tabel dan metrik regresi diekstraksi secara langsung (dinamis) dari record `normalized_json` di tabel basis data `siakad_pustaka_kurikulum`, bukan dari static config lama, test fixture lama, ataupun nilai hardcoded.

---

## 2. SEPARASI DIRECT SCALAR PAIRS VS AGGREGATE DOMAIN MAPPINGS

Sesuai prinsip integritas data dan kaidah kurikulum OBE:
- Pemetaan domain agregat **`TATA_NILAI`** (misalnya pada MK Agama, Bahasa Indonesia, Berpikir Kritis, Seni & Olahraga, Sikap Religius, PPKn) **tidak digabungkan (collapsed)** ke dalam direct scalar pairs.
- **Direct Scalar Pairs (Direct CPL-MK):** **271**
- **Aggregate Domain Mappings:** **6**
- Metrik tidak menampilkan 277 ataupun angka usang 331.

---

## 3. LIVE DATABASE REGRESSION AUDIT (ALL 5 PRODI)

Tabel berikut dihasilkan secara murni dengan membaca `siakad_pustaka_kurikulum.normalized_json` dari database aktif:

| No | Kode / Alias | Nama Program Studi | CPL Resmi | Bahan Kajian (BK) | Mata Kuliah (MK) | Total SKS | Direct CPL-MK | Aggregate Mapping | Status Mapping | Validasi Payload |
|:---:|:---:|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| 1 | **TRPL** | Teknologi Rekayasa Perangkat Lunak | 64 | 11 | 57 | 149 | 162 | 0 | `SOURCE_MAPPING_PARSED` | **YES** |
| 2 | **AH** | Agribisnis Hortikultura | 48 | 20 | 66 | 150 | 271 | 6 | `SOURCE_MAPPING_PARSED` | **YES** |
| 3 | **PKA** | Pengelolaan Konvensi dan Acara | 33 | 32 | 49 | 138 | 8 | 0 | `SOURCE_MAPPING_PARTIAL` | **YES** |
| 4 | **MPI** | Manajemen Pemasaran Internasional | 50 | 10 | 63 | 164 | 1 | 0 | `SOURCE_MAPPING_PARTIAL` | **YES** |
| 5 | **AP** | Akuntansi Perpajakan | 73 | 13 | 50 | 148 | 182 | 0 | `SOURCE_MAPPING_PARSED` | **YES** |

---

## 4. VERIFIKASI METRIK EXPECTED FINAL (AH & AP)

### A. Agribisnis Hortikultura (AH)
- `official_cpl_count`: **48**
- `bahan_kajian_count`: **20**
- `mata_kuliah_count`: **66**
- `total_sks`: **150**
- `direct_cpl_mk_pair_count`: **271**
- `aggregate_domain_mapping_count`: **6**
- `mapped_course_count`: **66**
- `unmapped_course_count`: **0**
- `mapping_parse_status`: **`SOURCE_MAPPING_PARSED`**

### B. Akuntansi Perpajakan (AP)
- `official_cpl_count`: **73**
- `bahan_kajian_count`: **13**
- `mata_kuliah_count`: **50**
- `total_sks`: **148**
- `direct_cpl_mk_pair_count`: **182**
- `aggregate_domain_mapping_count`: **0**
- `mapped_course_count`: **50**
- `unmapped_course_count`: **0**
- `mapping_parse_status`: **`SOURCE_MAPPING_PARSED`**

---

## 5. REPRODUCIBILITY SCRIPT

Script audit dinamis dapat dijalankan kapan saja untuk memverifikasi keselarasan database secara langsung:
```bash
php scratch/test_all_prodi_regression.php
```

---

## 6. FINAL VERDICT

```text
============================================================
FINAL VERDICT: REGRESSION_BASELINE_SYNCED
============================================================
Baseline regresi lintas prodi telah tersinkronisasi 100% 
dengan hasil normalisasi dan validasi terbaru:
- Sumber pembacaan audit: Live database (siakad_pustaka_kurikulum.normalized_json).
- Agribisnis Hortikultura: 271 direct pairs (scalar) + 6 aggregate domain mappings (TATA_NILAI).
- Akuntansi Perpajakan: 182 direct pairs (50/50 MK aktif terpetakan).
- Tidak ada nilai hardcoded atau payload usang yang tertinggal.
============================================================
```
