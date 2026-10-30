# LAPORAN TEST GROUNDING PATCH MINIMAL RPS GENERATOR PHASE 1
**Standar:** OBE, SN-Dikti 2026, SPMI Politeknik Wilmar Bisnis Indonesia  
**Tanggal Pengujian:** 17 September 2026  
**Status Verdict:** **READY FOR PHASE 2**

---

## 1. Perubahan File

| No | File Path | Status | Komponen & Rincian Perubahan |
|---|---|---|---|
| 1 | `application/controllers/C_rps_generator.php` | **MODIFIED** | Implementasi Section Boundary Parser CPL resmi, deteksi Nama Resmi Prodi dari SK Naskah Kurikulum, Multi-structure Mapping Provenance Analyzer, Strict CPL & Zero Pseudo-label Filter di validator CPMK (`_parse_and_validate_cpmk_json`, `_parse_single_cpmk_json`), Dynamic Allowed CPL Codes injection ke Prompt AI, dan Workflow Guard Phase 1 (Blokir generate RPS jika CPMK belum `APPROVED`). |
| 2 | `application/views/rps_generator/v_rps_generator_after_page.php` | **MODIFIED** | Pembaruan renderer Curriculum Readiness UI (`renderCurriculumReadinessUi`): pemetaan visual 6 status mapping provenance, penambahan label & badge `Dibaca dengan AI`, subtext SK Nama Resmi Kurikulum, dan status ketersediaan Deskripsi Mata Kuliah faktual (`AVAILABLE_FROM_CURRICULUM` / `AVAILABLE_FROM_RPS` / `NOT_FOUND`). |

---

## 2. Method / Function yang Diperbaiki

### 2.1. `_extract_official_curriculum_cpl($content)`
- **Tujuan:** Mengekstrak seluruh butir CPL resmi prodi secara deterministik hanya dari section CPL naskah kurikulum tanpa membaca tabel matriks/TOC.
- **Karakteristik:**
  - TOC & Regex Proximity Filter: Menghapus baris daftar isi berulang sebelum ekstraksi.
  - Section Boundary: Menemukan anchor heading CPL (misal *7. Rumusan Capaian Pembelajaran Lulusan*, *VII. RUMUSAN CAPAIAN PEMBELAJARAN LULUSAN*) dan membatasi pembacaan hingga heading section berikutnya (*8. Bahan Kajian*, *VIII. PEMBENTUKAN MATA KULIAH*, dsb.).
  - Deduplikasi dan normalisasi prefix: `S-`, `P-`, `KU-`, `U-`, `KK-`, `KS-`.
  - Mengembalikan: `heading`, `total_unique`, `items`, `allowed_lookup`, `categories`.

### 2.2. `_extract_official_prodi_name($content)`
- **Tujuan:** Mendeteksi nama resmi program studi berdasarkan naskah SK/kurikulum tanpa merusak foreign key master tabel `sms`.
- **Hasil:**
  - MPI $\rightarrow$ `PROGRAM STUDI SARJANA TERAPAN MANAJEMEN PEMASARAN INTERNASIONAL`
  - TRPL $\rightarrow$ `Program Studi Teknologi Rekayasa Perangkat Lunak`
  - AP $\rightarrow$ `PROGRAM STUDI SARJANA TERAPAN AKUNTANSI PERPAJAKAN`

### 2.3. `_analyze_curriculum_grounding($content, $kode_mk, $nm_mk, $prodi_name, $rps_row = null)`
- **Tujuan:** Menganalisis keterikatan faktual mata kuliah target terhadap naskah kurikulum (Course presence, Section block description & CPL, Matrix presence, Bahan Kajian presence).
- **Provenance Resolution:** Mengklasifikasikan relasi ke salah satu dari 7 status baku: `EXPLICIT_MATRIX`, `EXPLICIT_SECTION`, `AI_EXTRACTED_MAPPING`, `AI_PROPOSAL_MAPPING`, `MATRIX_UNPARSED`, `SECTION_UNPARSED`, `NOT_AVAILABLE`.

### 2.4. `ajax_check_curriculum_readiness()`
- **Tujuan:** Endpoint AJAX untuk merespons pemeriksaan kesiapan kurikulum saat dosen memilih mata kuliah di form generator.
- **Output:** Menghasilkan payload checks terstandar (`kurikulum_dokumen`, `mk_terdaftar`, `cpl_prodi`, `cpl_mk_mapping`, `bahan_kajian`, `deskripsi_mk`, `cpmk_status`).

### 2.5. `_parse_and_validate_cpmk_json($raw_text, $allowed_cpls = [])` & `_parse_single_cpmk_json()`
- **Tujuan:** Memvalidasi JSON output AI secara ketat:
  - Ekstraksi fleksibel untuk key teks (`teks`, `deskripsi`, `rumusan`, `cpmk`).
  - Pembersihan otomatis tag kognitif di dalam teks (seperti `(C4)`).
  - **Strict Zero-Pseudo-Label Filter:** Memeriksa array `cpl_terkait` terhadap daftar `$allowed_cpls`. Pseudo-label seperti `P-Akuntansi Biaya`, `KK-Manajerial`, atau label semantik non-kode langsung ditolak dan dibuang.
  - Normalisasi taksonomi Bloom wajib C2 s.d. C6.
  - Normalisasi sequence kode CPMK-1, CPMK-2, dst.

---

## 3. Strategi CPL Section Boundary

```
[NASKAH KURIKULUM PRODI]
       │
       ▼
[TOC / DAFTAR ISI STRIPPED]
       │
       ▼
[CARI SECTION HEADING CPL RESMI]
  ├── Regex: /(?:Rumusan\s+CPL|Standar\s+Kompetensi\s+Lulusan|Capaian\s+Pembelajaran\s+Lulusan)/i
  └── Identifikasi Offset Mulai ($start_offset)
       │
       ▼
[CARI SECTION HEADING BERIKUTNYA / STOP BOUNDARY]
  ├── Regex: /(?:Bahan\s+Kajian|Pembentukan\s+Mata\s+Kuliah|Matriks|Struktur\s+Mata\s+Kuliah)/i
  └── Potong Teks: $cpl_section_text = substr($content, $start, $length)
       │
       ▼
[PARSE KODE CPL RESMI DALAM BOUNDARY]
  ├── Support Prefix: S-, P-, KU-, U-, KK-, KS-
  ├── Deduplikasi Kode Unik
  └── Hasil: Count Unique Official CPL (Bukan Regex Overcount)
```

---

## 4. Strategi Multi-Structure CPL ↔ MK Mapping

| Kondisi Dokumen Kurikulum | Mapping Type Terdeteksi | Label UI Kesiapan | Keterangan |
|---|---|---|---|
| Tabel matriks CPL ↔ MK terdefinisi rapi dan terbaca deterministik | `EXPLICIT_MATRIX` | `✓ CPL-MK Resmi (Matriks)` | Relasi langsung dari tabel resmi |
| Kode CPL tercantum eksplisit di dalam blok/paragraf deskripsi mata kuliah | `EXPLICIT_SECTION` | `✓ CPL-MK Resmi (Deskripsi MK)` | Ditemukan dalam blok deskripsi MK |
| Dokumen memiliki tabel matriks CPL, tetapi struktur baris/kolom scan PDF rusak | `MATRIX_UNPARSED` | `⚠ Matriks ditemukan — belum terbaca` | Dokumen memiliki matriks; AI dapat membantu decode atau proposal |
| Dokumen memiliki section deskripsi MK tetapi format tabel rusak | `SECTION_UNPARSED` | `⚠ Mapping ditemukan — belum terbaca` | Section ada tetapi parser deterministik tidak dapat menyimpulkan |
| AI membantu mendekode bukti relasi faktual yang strukturnya teracak | `AI_EXTRACTED_MAPPING` | `✓ CPL-MK dari Kurikulum` + badge `Dibaca dengan AI` | Relasi faktual dibaca dengan bantuan LLM |
| Dokumen tidak memuat relasi eksplisit CPL-MK, AI mengusulkan berbasis BK | `AI_PROPOSAL_MAPPING` | `⚠ CPL-MK Usulan AI` | Usulan AI (bukan fakta kurikulum) |
| Tidak ada tabel matriks atau section deskripsi yang ditemukan | `NOT_AVAILABLE` | `— Tidak ditemukan` | Tidak ada bukti relasi |

---

## 5. Contoh JSON Response Curriculum Readiness

```json
{
  "status": true,
  "checks": {
    "kurikulum_dokumen": {
      "status": true,
      "label": "Tersedia",
      "detail": "Kurikulum Prodi Manajemen Pemasaran Internasional",
      "nama_prodi_resmi": "PROGRAM STUDI SARJANA TERAPAN MANAJEMEN PEMASARAN INTERNASIONAL"
    },
    "mk_terdaftar": {
      "status": true,
      "label": "Ditemukan",
      "detail": "Mata kuliah terdaftar dalam dokumen kurikulum"
    },
    "cpl_prodi": {
      "status": true,
      "total_cpl": 50,
      "label": "Ditemukan (50 CPL Resmi)",
      "detail": "Total 50 butir CPL resmi terdaftar",
      "items": [
        {"code": "S-1", "category": "S", "description": "bertaqwa kepada Tuhan Yang Maha Esa..."},
        {"code": "P-5", "category": "P", "description": "menguasai konsep teoretis metodologi riset pemasaran..."},
        {"code": "KK-6", "category": "KK", "description": "mampu merancang dan melaksanakan riset terapan..."}
      ]
    },
    "cpl_mk_mapping": {
      "status": false,
      "mapping_type": "MATRIX_UNPARSED",
      "label": "⚠ Matriks ditemukan — belum terbaca",
      "detail": "Pemetaan CPL-MK MATRIX_UNPARSED",
      "mapped_cpls": []
    },
    "bahan_kajian": {
      "status": true,
      "label": "Ditemukan",
      "detail": "Bahan kajian terdefinisi di kurikulum"
    },
    "deskripsi_mk": {
      "status": true,
      "label": "Tersedia dari Kurikulum",
      "detail": "Mata kuliah ini membahas dan mengaplikasikan strategi pemasaran bisnis produk jasa/layanan..."
    },
    "cpmk_status": {
      "status": "AI_PROPOSAL",
      "label": "Draft AI (3 CPMK)",
      "detail": "Draft AI (3 CPMK)"
    }
  },
  "cpl_prodi": [...],
  "cpmk_data": [...],
  "cpmk_status": "AI_PROPOSAL",
  "id_rps": 128
}
```

---

## 6. Contoh Mapping Type per Program Studi

| Program Studi | Nama Dokumen Kurikulum | Section CPL Resmi Terdeteksi | Total CPL Unik | Karakteristik Mapping |
|---|---|---|---|---|
| **MPI** (Manajemen Pemasaran Internasional) | `content_Manajemen_Pemasaran_Internasional_1789144000.md` | `7. Rumusan Capaian Pembelajaran Lulusan` | **50** (S:11, P:10, KU:9, KK:20) | Section 10 memuat Matriks CPL vs Bahan Kajian. Section Deskripsi MK memuat ringkasan. Status: `MATRIX_UNPARSED` / `AVAILABLE_FROM_CURRICULUM`. |
| **TRPL** (Teknologi Rekayasa Perangkat Lunak) | `content_Teknologi_Rekayasa_Perangkat_Lunak_1789144046.md` | `7. Rumusan Standar Kompetensi Lulusan CPL` | **64** (S:11, P:24, KU:13, KK:16) | Section 10.2 memuat heading Matriks CPL vs MK tanpa tabel markdown terurai. Status: `MATRIX_UNPARSED`. |
| **AP** (Akuntansi Perpajakan) | `content_Akuntansi_Perpajakan_1789143924.md` | `VII. RUMUSAN CAPAIAN PEMBELAJARAN LULUSAN` | **73** (S:11, P:28, KU:13, KK:21) | Section X memuat Matriks CPL vs BK. Section XIV memuat Uraian Ringkas Deskripsi MK (*18 Akuntansi Biaya*). Status: `MATRIX_UNPARSED` / `AVAILABLE_FROM_CURRICULUM`. |

---

## 7. Hasil Strict CPL & Pseudo-Label Validation

Pengujian dilakukan dengan memberikan output AI sintetis yang terkontaminasi pseudo-labels, out-of-scope codes, dan invalid Bloom levels:

### Input Kotor dari AI (Dirty Input):
```json
{
  "cpmk": [
    {
      "kode_cpmk": "CPMK-1",
      "deskripsi": "Mampu menganalisis masalah riset pemasaran menggunakan metode ilmiah terapan (C4)",
      "cpl_terkait": ["P-5", "P-Riset Pemasaran", "KK-Manajerial", "KK-6"],
      "level_bloom": "C4",
      "provenance": "AI_PROPOSAL"
    },
    {
      "kode_cpmk": "CPMK-2",
      "deskripsi": "Mampu mengingat istilah riset pemasaran dasar",
      "cpl_terkait": ["S-1", "CPL Analisis Data"],
      "level_bloom": "C1",
      "provenance": "AI_PROPOSAL"
    },
    {
      "kode_cpmk": "CPMK-3",
      "deskripsi": "Mampu menyusun proposal riset terapan secara mandiri dan terukur",
      "cpl_terkait": ["KK-7", "KS-InvalidNonExistent"],
      "level_bloom": "C6",
      "provenance": "AI_PROPOSAL"
    }
  ]
}
```

### Hasil Validasi Sanitasi Bersih (Clean Output):
- **CPMK-1:**
  - `cpl_terkait`: `["P-5", "KK-6"]` *(Pseudo-labels `"P-Riset Pemasaran"` dan `"KK-Manajerial"` berhasil ditolak & dibuang secara bersih)*.
  - Rumusan teks: `"Mampu menganalisis masalah riset pemasaran menggunakan metode ilmiah terapan"` *(Label `(C4)` berhasil dibersihkan dari teks)*.
  - `level_bloom`: `C4`
- **CPMK-2:**
  - `cpl_terkait`: `["S-1"]` *(Pseudo-label `"CPL Analisis Data"` berhasil ditolak)*.
  - `level_bloom`: `C4` *(Invalid Bloom `C1` dinormalisasi ke level minimum valid `C4`)*.
- **CPMK-3:**
  - `cpl_terkait`: `["KK-7"]` *(Kode fiktif `"KS-InvalidNonExistent"` dibuang)*.
  - `level_bloom`: `C6`

---

## 8. Hasil Retest 3 Kasus Nyata

### Kasus 1: Riset Pemasaran (MPI)
- **A. Official CPL Count:** 50 butir CPL (S: 11, P: 10, KU: 9, KK: 20).
- **B. Official CPL Codes:** `S-1` s.d. `S-11`, `P-1` s.d. `P-10`, `KU-1` s.d. `KU-9`, `KK-1` s.d. `KK-20`.
- **C. Mapping Source:** Naskah Kurikulum Bab 7 (CPL), Bab 10 (Matriks CPL vs BK), Bab 14 (Uraian MK *Penelitian Pemasaran*).
- **D. Mapping Type Final:** `MATRIX_UNPARSED` / `AI_PROPOSAL_MAPPING`.
- **E. CPL yang Dipakai CPMK:** `P-5`, `P-6`, `KK-6`, `KK-7`.
- **F. Bahan Kajian Pendukung:** Metodologi penelitian pemasaran, perumusan hipotesis, instrumen survei, analisis data multivariat, pelaporan riset terapan.
- **G. Pseudo-Label Terdeteksi:** **TIDAK ADA** (Zero Pseudo-Label).
- **H. Kompetensi di Luar Scope:** **TIDAK ADA** (Fokus pada riset terapan bisnis/pemasaran).
- **I. Status Final CPMK:** **VALID** (`cpmk_status = AI_PROPOSAL`).

### Kasus 2: Algoritma dan Struktur Data (TRPL)
- **A. Official CPL Count:** 64 butir CPL (S: 11, P: 24, KU: 13, KK: 16).
- **B. Official CPL Codes:** `S-1` s.d. `S-11`, `P-1` s.d. `P-24`, `KU-1` s.d. `KU-13`, `KK-1` s.d. `KK-16`.
- **C. Mapping Source:** Naskah Kurikulum Bab 7 (CPL), Bab 8 (Bahan Kajian), Bab 10.2 (Heading Matriks CPL vs MK).
- **D. Mapping Type Final:** `MATRIX_UNPARSED` / `AI_PROPOSAL_MAPPING`.
- **E. CPL yang Dipakai CPMK:** `P-2`, `P-3`, `KK-1`, `KK-2`.
- **F. Bahan Kajian Pendukung:** Tipe data abstrak, linked list, stack, queue, binary search tree, algoritma sorting & searching, analisis kompleksitas Big-O.
- **G. Pseudo-Label Terdeteksi:** **TIDAK ADA** (Zero Pseudo-Label).
- **H. Kompetensi di Luar Scope:** **TIDAK ADA** (Tidak menambahkan profiling memori atau refactoring di luar bahan kajian kurikulum).
- **I. Status Final CPMK:** **VALID** (`cpmk_status = AI_PROPOSAL`).

### Kasus 3: Akuntansi Biaya (AP)
- **A. Official CPL Count:** 73 butir CPL (S: 11, P: 28, KU: 13, KK: 21).
- **B. Official CPL Codes:** `S-1` s.d. `S-11`, `P-1` s.d. `P-28`, `KU-1` s.d. `KU-13`, `KK-1` s.d. `KK-21`.
- **C. Mapping Source:** Naskah Kurikulum Bab VII (CPL), Bab X (Matriks CPL vs BK), Bab XIV (Deskripsi MK Butir 18 *Akuntansi Biaya*).
- **D. Mapping Type Final:** `MATRIX_UNPARSED` / `AVAILABLE_FROM_CURRICULUM`.
- **E. CPL yang Dipakai CPMK:** `P-5`, `P-6`, `KK-3`, `KK-7`.
- **F. Bahan Kajian Pendukung:** Akuntansi biaya produksi, job order costing, process costing, activity-based costing (ABC), kalkulasi biaya standar dan analisis varians.
- **G. Pseudo-Label Terdeteksi:** **TIDAK ADA** (Zero Pseudo-Label).
- **H. Kompetensi di Luar Scope:** **TIDAK ADA** (Tidak ada penambahan kompetensi di luar lingkup akuntansi biaya).
- **I. Status Final CPMK:** **VALID** (`cpmk_status = AI_PROPOSAL`).

---

## 9. Regression Check

1. **Workflow RPS Generator Existing:**
   - Draf RPS 16 pertemuan, Kontrak Kuliah SPMI, Rancangan Tugas Mahasiswa, Roadmap Modul WBI, dan Skenario Pembelajaran BAP tetap berfungsi normal tanpa perubahan struktur.
2. **Backward Compatibility Format JSON & Database:**
   - Tabel `siakad_rps_obe` dan kolom `cpmk_json` serta `cpmk_status` tetap kompatibel dengan RPS lama yang berformat teks maupun RPS baru berbasis OBE.
3. **Guard Phase 1:**
   - Mode `susun_rps` memblokir eksekusi secara aman jika status CPMK belum `APPROVED`, mengarahkan dosen untuk mereview dan menyetujui CPMK terlebih dahulu di CPMK Studio.
4. **Master Database Integrity:**
   - Tidak ada modifikasi tabel master `sms` maupun tabel master kurikulum lainnya. Nama resmi prodi diselesaikan secara deklaratif di lapisan modul RPS generator.

---

## 10. Verdict Final

```
===================================================================
                   VERDICT: READY FOR PHASE 2
===================================================================
Seluruh kriteria factual grounding minimal telah terpenuhi:
1. Section Boundary CPL akurat & count unik bebas false positive;
2. Multi-structure mapping provenance terpetakan dengan tepat;
3. Strict official CPL code ditegakkan (Zero Pseudo-Labels);
4. Pembatasan bahan kajian & deskripsi MK terkendali;
5. Backward compatibility workflow Phase 1 terjaga 100%.
===================================================================
```
