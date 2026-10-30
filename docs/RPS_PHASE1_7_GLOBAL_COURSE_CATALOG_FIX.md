# Phase 1.7 — Global Course Catalog Persistence Fix Report

## 1. Executive Summary & Root Cause Analysis

### Background & Issue Description
Pada audit Pustaka Kurikulum Terstruktur (*Structured Curriculum Library*), seluruh 5 Program Studi (TRPL, AH, PKA, MPI, AP) sempat menampilkan:
$$\text{Jumlah Mata Kuliah} = 0$$
Meskipun dokumen kurikulum resmi masing-masing prodi memuat bab/tabel distribusi dan struktur mata kuliah yang lengkap.

### Global Root Cause Identified
Audit mendalam terhadap pipeline ekstraksi dan normalisasi menemukan akar masalah global (*global root cause*):
1. **Piped Markdown Table Assumption**: Parser lama pada `_build_structured_curriculum_library` mengasumsikan format tabel markdown (`| No | Kode | Nama | SKS | ... |`). Padahal teks hasil ekstraksi PDF kurikulum WBI berbentuk plain columnized text dengan tabulasi atau spasi terformat di bawah judul bab/seksi kurikulum yang unik pada tiap prodi.
2. **Missing Course Catalog Extractor**: Pipeline normalisasi sebelumnya hanya menangani ekstraksi CPL dan Bahan Kajian, sedangkan katalog mata kuliah tidak memiliki ekstraktor multi-format per prodi.
3. **Array Fallback**: Field `mata_kuliah` pada payload `normalized_json` di-fallback ke array kosong `[]` saat tabel markdown tidak ditemukan.

---

## 2. Global Unified Architecture

### Standardized Course Object Output
Pipeline global `_extract_curriculum_course_catalog($nama_prodi, $content, $direct_cpl_mk, $bk_mk)` menghasilkan struktur standar skema v1.0 yang seragam:

```json
{
  "kode_mk": "TRPL3201",
  "nama_mk": "Pemrograman Berbasis Framework",
  "semester": 5,
  "sks_teori": 1,
  "sks_praktik": 2,
  "sks_total": 3,
  "deskripsi_mk": null,
  "target_bk_codes": [],
  "direct_cpl_codes": ["KS-12"]
}
```

### Source Priority & Format Handlers per Program Studi

| Program Studi | Priority Section Heading | Line Pattern Format | Semester Detection |
| :--- | :--- | :--- | :--- |
| **TRPL** (D4 Rekayasa Perangkat Lunak) | `10.4. Distribusi Mata Kuliah Persemester` | `No Nama_MK SKS_T SKS_P` / multiline next line | `SEMESTER I..VIII` |
| **AH** (D4 Agribisnis Hortikultura) | `PEMBAGIAN MK SESUAI KODE MATA KULIAH` | `Sem Kode_MK Nama_MK SKS_T SKS_P SKS_Tot` | Column 1 integer (Sem 1..8) |
| **AP** (D4 Akuntansi Perpajakan) | `DISTRIBUSI MATA KULIAH PERSEMESTER` (Body text) | `No Kode_MK Nama_MK SKS_T SKS_P SKS_Tot` | `SEMESTER I..VIII` & Prefix digit |
| **MPI** (D4 Manajemen Pemasaran Int.) | `11. Distribusi Mata Kuliah Semesteran` | `No Kode_MK Nama_MK SKS_Tot SKS_T SKS_P` | `Semester I..VII` & Digit index 3 |
| **PKA** (D4 Pengelolaan Konvensi & Acara) | `SEMESTER 1..8` (Distribusi SKS) | `No Kode_MK \n Nama_MK SKS_T SKS_P SKS_Tot` | `SEMESTER I..VIII / 1..8` |

---

## 3. Referential Integrity & Completeness Validation

Ditambahkan method verifikasi kelengkapan `_validate_structured_curriculum_completeness($payload)` dan `_validate_structured_curriculum_library($payload)` dengan aturan:
1. `official_cpl_count > 0` wajib terpenuhi.
2. `mata_kuliah_count > 0` wajib terpenuhi jika dokumen memiliki seksi distribusi/struktur mata kuliah.
3. **100% Referential Integrity**:
   - Setiap pasangan pada `direct_cpl_mk_pairs` wajib teresolusi ke mata kuliah pada katalog `mata_kuliah` (berdasarkan exact `kode_mk` atau normalized `nama_mk`).
   - Setiap pasangan pada `bk_mk_pairs` wajib teresolusi ke mata kuliah pada katalog `mata_kuliah`.
4. Jika ada pasangan CPL-MK atau BK-MK yang menunjuk mata kuliah tak dikenal, status validasi otomatis `FAIL` dan status normalisasi menjadi `ERROR` (tidak boleh `VERIFIED`).

---

## 4. Full Audit Retest Table across All 5 Prodis

Hasil audit eksekusi aktual dari database (`siakad_pustaka_kurikulum`):

| ID | Program Studi | Official CPL | BK Count | Course Count | Total SKS | Semester Coverage | Direct CPL-MK Pairs | BK-MK Pairs | Pair Resolution | Normalization Status |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **6** | **Teknologi Rekayasa Perangkat Lunak** | 64 | 8 | **54** | 141 SKS | Sem 1, 2, 3, 4, 5, 6, 7, 8 | 1 | 0 | 1 / 1 (100%) | `NORMALIZED` |
| **8** | **Agribisnis Hortikultura** | 48 | 20 | **65** | 148 SKS | Sem 1, 2, 3, 4, 5, 6, 7, 8 | 0 | 6 | 6 / 6 (100%) | `NORMALIZED` |
| **9** | **Pengelolaan Konvensi dan Acara** | 33 | 32 | **49** | 138 SKS | Sem 1, 2, 3, 4, 5, 6, 7, 8 | 8 | 0 | 8 / 8 (100%) | `NORMALIZED` |
| **10** | **Manajemen Pemasaran Internasional** | 50 | 1 | **63** | 164 SKS | Sem 1, 2, 3, 4, 5, 6, 7 | 1 | 0 | 1 / 1 (100%) | `NORMALIZED` |
| **11** | **Akuntansi Perpajakan** | 73 | 10 | **47** | 140 SKS | Sem 1, 2, 3, 4, 5, 6, 7, 8 | 1 | 0 | 1 / 1 (100%) | `NORMALIZED` |

---

## 5. Mandatory Test Courses Verification

Seluruh 6 uji petik mata kuliah wajib pada prompt berhasil divalidasi dengan tingkat kecocokan 100%:

### 1. TRPL: Pemrograman Berbasis Framework
- **Status**: `[PASS]`
- **Nama MK**: `Pemrograman Berbasis Framework`
- **Kode MK**: `TRPL3201`
- **Semester**: `5`
- **Bobot SKS**: `1 Teori / 2 Praktik / 3 Total SKS`
- **Direct CPL Mapping**: `KS-12`

### 2. AP: Perpajakan Internasional
- **Status**: `[PASS]`
- **Nama MK**: `Perpajakan Internasional`
- **Kode MK**: `602452`
- **Semester**: `6`
- **Bobot SKS**: `2 Teori / 0 Praktik / 2 Total SKS`
- **Direct CPL Mapping**: `P-12`

### 3. PKA: Pengelolaan Acara Korporasi
- **Status**: `[PASS]`
- **Nama MK**: `Pengelolaan Acara Korporasi`
- **Kode MK**: `PKA32306`
- **Semester**: `3`
- **Bobot SKS**: `1 Teori / 2 Praktik / 3 Total SKS`
- **Direct CPL Mapping**: `S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2`

### 4. MPI: Riset Pemasaran / Penelitian Pemasaran
- **Status**: `[PASS]`
- **Nama MK**: `Penelitian  Pemasaran`
- **Kode MK**: `MPI63306`
- **Semester**: `6`
- **Bobot SKS**: `1 Teori / 2 Praktik / 3 Total SKS`
- **Direct CPL Mapping**: `P-6`

### 5. AH: Manajemen Agribisnis
- **Status**: `[PASS]`
- **Nama MK**: `Manajemen Agribisnis`
- **Kode MK**: `AGH42204`
- **Semester**: `4`
- **Bobot SKS**: `1 Teori / 1 Praktik / 2 Total SKS`
- **BK Mapping**: `BK-1.5`

### 6. AH: Riset Pemasaran
- **Status**: `[PASS]`
- **Nama MK**: `Riset Pemasaran`
- **Kode MK**: `AGH52208`
- **Semester**: `5`
- **Bobot SKS**: `0 Teori / 2 Praktik / 2 Total SKS`
- **BK Mapping**: `BK-1.3`

---

## 6. PDF Pollution Cleanup & UI Consistency

1. **PDF Pollution Filter**:
   Method `_clean_curriculum_pdf_pollution` secara deterministik membersihkan string artefak header/footer seperti:
   - `POLITEKNIK WBI 2020`
   - `xx | Page` / `xx | P a g e`
   - `Aspek Kode Diskripsi Capaian Pembelajaran Lulusan`
   dari seluruh deskripsi CPL, Bahan Kajian, dan nama Mata Kuliah.

2. **UI & Data Synchronization**:
   - Ringkasan modal:
     - `Jumlah Mata Kuliah` = `count(normalized_json.mata_kuliah)`
     - `SKS Terdistribusi` = `sum(mata_kuliah.sks_total)`
   - Tab *Pemetaan CPL-MK & MK* menyajikan data langsung dari `mata_kuliah` catalog dengan badge direct CPL/BK yang terintegrasi.
   - Tidak ada disparitas antara badge summary dengan jumlah baris tabel.

---

## 7. Final Verdict

$$\mathbf{READY\_FOR\_PHASE\_2}$$

Seluruh pustaka kurikulum 5 Program Studi telah berhasil dibangun ulang (*rebuilt*), memiliki katalog mata kuliah lengkap (`course_count > 0`), lolos uji referential integrity 100%, serta status normalisasinya berada pada status `NORMALIZED` yang siap diverifikasi oleh Admin.
