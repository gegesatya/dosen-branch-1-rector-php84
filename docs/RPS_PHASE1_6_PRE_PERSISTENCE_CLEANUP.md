# Laporan Audit & Pembersihan Pra-Persistensi (Pre-Persistence Cleanup)

**Dokumen**: `docs/RPS_PHASE1_6_PRE_PERSISTENCE_CLEANUP.md`  
**Verdict**: `READY_FOR_CURRICULUM_LIBRARY` (100% PASSED)  
**Tanggal**: 18 September 2026  
**Scope**: Pembersihan Nama Resmi Program Studi MPI & Standardisasi Semantik CPL ↔ BK

---

## 1. Penyelarasan Nama Resmi Program Studi MPI

Sesuai naskah kurikulum resmi WBI, identitas program studi untuk MPI telah ditetapkan:

* **Nama Resmi Dokumen Kurikulum**: `Manajemen Pemasaran Internasional` (Program Studi Sarjana Terapan Manajemen Pemasaran Internasional).
* **Kebijakan Master Database**: Master tabel `sms` tidak dimodifikasi secara paksa. Penamaan resmi hanya dioperasikan pada context grounding, ekstraksi naskah kurikulum (`_extract_official_prodi_name`), lookup file pustaka, dan antarmuka kurikulum.
* **Toleransi Aliasing**: Sistem mendukung pencarian multi-kata kunci (`mpi`, `manajemen`, `pemasaran`, `perusahaan`) agar kompatibel dengan legacy query tanpa merusak integritas canonical prodi.

---

## 2. Standardisasi Semantik Pemetaan CPL ↔ BK

Nilai `cpl_bk_mappable_count = 0` tidak lagi digunakan secara ambigu untuk menandakan bahwa parser tidak menggunakan jalur CPL↔BK. Sistem kini memisahkan status pemetaan dan jumlah secara eksplisit:

### Status `cpl_bk_mapping_status`:
1. **`PARSED`**:
   * Matriks CPL ↔ BK diuraikan secara deterministik oleh parser.
   * `cpl_bk_mappable_count` bernilai **integer** (contoh: AH = `25`).
2. **`AVAILABLE_UNPARSED`**:
   * Section / tabel matriks CPL ↔ BK tersedia di dalam naskah kurikulum, namun jalur eviden utama untuk mata kuliah menggunakan pemetaan langsung CPL ↔ MK (`EXPLICIT_MATRIX`).
   * `cpl_bk_mappable_count` bernilai **`null`** (bukan 0).
3. **`NOT_AVAILABLE`**:
   * Dokumen kurikulum tidak memiliki tabel / section hubungan CPL dan Bahan Kajian terpisah.
   * `cpl_bk_mappable_count` bernilai **`null`**.

---

## 3. Hasil Audit Komprehensif 5 Program Studi

| Program Studi | Mata Kuliah Uji | Official CPL | CPL ↔ BK Status | CPL-BK Mappable Count | Mapped CPL MK | Mapping Type | Direct CPL-MK Status | Verdict |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **D4 Agribisnis Hortikultura (AH)** | Manajemen Agribisnis | **48** | `PARSED` | **25** | **6** (`A2, B4, C1, C2, E1, E4`) | `DERIVED_VIA_BK` | `N/A` | **PASS** |
| **D4 Agribisnis Hortikultura (AH)** | Riset Pemasaran | **48** | `PARSED` | **25** | **7** (`A2, B3, B4, C1, C2, E1, E4`) | `DERIVED_VIA_BK` | `N/A` | **PASS** |
| **D4 Teknologi Rekayasa Perangkat Lunak (TRPL)** | Pemrograman Berbasis Framework | **64** | `AVAILABLE_UNPARSED` | `null` | **1** (`KS-12`) | `EXPLICIT_MATRIX` | `PARSED` | **PASS** |
| **D4 Akuntansi Perpajakan (AP)** | Perpajakan Internasional | **73** | `AVAILABLE_UNPARSED` | `null` | **1** (`P-12`) | `EXPLICIT_MATRIX` | `PARSED` | **PASS** |
| **D4 Pengelolaan Konvensi dan Acara (PKA)** | Pengelolaan Acara Korporasi | **33** | `AVAILABLE_UNPARSED` | `null` | **8** (`S, KU, KK, P`) | `EXPLICIT_MATRIX` | `PARSED` | **PASS** |
| **D4 Manajemen Pemasaran Internasional (MPI)** | Riset Pemasaran | **50** | `AVAILABLE_UNPARSED` | `null` | **1** (`P-6`) | `EXPLICIT_MATRIX` | `PARSED` | **PASS** |

---

## 4. Tampilan Checklist Kesiapan (Readiness UI)

* **AH**:
  * `CPL Prodi`: `48 CPL ditemukan`
  * `Pemetaan CPL ↔ BK`: `25 CPL memiliki mapping BK` (Status: `PARSED`)
  * `Pemetaan CPL ↔ MK`: `6 CPL terpetakan untuk MK terpilih` (atau `7 CPL...`)
  * `Bahan Kajian`: `20 BK kurikulum / 2 BK terkait MK`
* **TRPL / AP / PKA / MPI**:
  * `CPL Prodi`: `64 / 73 / 33 / 50 CPL ditemukan`
  * `Pemetaan CPL ↔ BK`: `Tersedia di Kurikulum` (Status: `AVAILABLE_UNPARSED`)
  * `Pemetaan CPL ↔ MK`: `1 / 8 CPL terpetakan untuk MK terpilih`
  * `Bahan Kajian`: Tetap utuh (contoh TRPL = `8 BK kurikulum / 1 BK terkait MK`, AP = `10 BK`, PKA = `32 BK`, MPI = `1 BK`)

---

## 5. Kesimpulan Final

Seluruh penyesuaian semantik dan nama resmi dokumen kurikulum telah terintegrasi dengan bersih tanpa modifikasi pada master table database `sms` dan tanpa mengubah struktur parser canonical prodi lain.

**Verdict Akhir**: `READY_FOR_CURRICULUM_LIBRARY`
