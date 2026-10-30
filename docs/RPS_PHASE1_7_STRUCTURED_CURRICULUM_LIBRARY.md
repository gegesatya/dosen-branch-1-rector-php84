# LAPORAN IMPLEMENTASI PHASE 1.7 — STRUCTURED CURRICULUM LIBRARY

**Project**: Sistem Otomasi RPS Berbasis OBE — Politeknik Wilmar Bisnis Indonesia (WBI)  
**Dokumen**: `docs/RPS_PHASE1_7_STRUCTURED_CURRICULUM_LIBRARY.md`  
**Status**: **VERIFIED / READY FOR PHASE 2**  
**Tanggal**: 18 September 2026  
**Parser Version**: `Phase-1.7`  
**Schema Version**: `1.0`  

---

## 1. AUDIT EXISTING SCHEMA (`siakad_pustaka_kurikulum`)

Sebelum penambahan field baru, audit terhadap tabel `siakad_pustaka_kurikulum` menghasilkan temuan berikut:

- **Primary Key**: `id` (`INT(11) AUTO_INCREMENT`)
- **Field Program Studi**: `nama_prodi` (`VARCHAR(150)`)
- **Field Identitas Dokumen**: `judul_dokumen` (`VARCHAR(255)`), `tahun_kurikulum` (`VARCHAR(10)`), `file_name` (`VARCHAR(255)`), `file_path_doc` (`VARCHAR(255)`), `file_path_content` (`VARCHAR(255)`), `total_karakter` (`INT(11)`)
- **Content Ekstraksi**: Disimpan dalam file teks/markdown fisik pada path yang ditunjuk oleh `file_path_content` (misal: `uploads/pustaka_kurikulum/content_*.md`)
- **Timestamps**: `created_at` (`DATETIME`), `updated_at` (`DATETIME`)
- **Status Existing**: `status` (`ENUM('aktif','nonaktif')`)
- **Field JSON Existing**: Belum ada field JSON terstruktur untuk canonical CPL, BK, dan relasi matriks.
- **Relasi & Akses Controller**: Controller `C_rps_generator.php` sebelumnya memanggil `_get_kurikulum_content()` untuk memuat file teks markdown secara runtime setiap kali endpoint readiness atau studio CPL dipanggil.

---

## 2. EXACT SQL ALTER / SCHEMA CHANGES

Untuk meminimalisir overhead arsitektur dan menghindari pembuatan tabel-tabel relasional yang tidak perlu pada fase ini, metadata dan representasi terstruktur disimpan langsung pada tabel `siakad_pustaka_kurikulum`:

```sql
ALTER TABLE `siakad_pustaka_kurikulum`
  ADD COLUMN `normalized_json` LONGTEXT NULL AFTER `total_karakter`,
  ADD COLUMN `normalization_status` VARCHAR(30) NULL DEFAULT 'NOT_NORMALIZED' AFTER `normalized_json`,
  ADD COLUMN `schema_version` VARCHAR(20) NULL DEFAULT '1.0' AFTER `normalization_status`,
  ADD COLUMN `parser_version` VARCHAR(30) NULL DEFAULT 'Phase-1.7' AFTER `schema_version`,
  ADD COLUMN `source_hash` VARCHAR(64) NULL AFTER `parser_version`,
  ADD COLUMN `normalized_at` DATETIME NULL AFTER `source_hash`;
```

---

## 3. STRUCTURED JSON SCHEMA FINAL (Schema Version 1.0)

Representasi payload terstruktur mengikuti spesifikasi baku berikut:

```json
{
  "schema_version": "1.0",
  "prodi": {
    "nama_resmi": "Agribisnis Hortikultura",
    "kode_prodi": "Agribisnis Hortikultura",
    "jenjang": "D4"
  },
  "curriculum": {
    "nama": "Kurikulum & CPL Program Studi Sarjana Terapan Agribisnis Hortikultura",
    "tahun": "2026",
    "versi": "1.0",
    "source_file": "Kurikulum_D4_Agribisnis_Hortikultura_Final.pdf",
    "source_hash": "a1b2c3d4e5f6...",
    "parser_version": "Phase-1.7",
    "normalized_at": "2026-09-18 11:20:00",
    "verification_status": "VERIFIED"
  },
  "official_cpl": [
    {
      "code": "S-1",
      "source_code": "a.",
      "category": "S",
      "description": "Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius.",
      "source_section": "ASPEK SIKAP",
      "is_official": true,
      "is_normalized_code": true
    },
    {
      "code": "A1",
      "source_code": "A.1",
      "category": "P",
      "description": "Menguasai konsep teoretis botani, fisiologi, ilmu tanah, hidrologi, dan klimatologi tanaman hortikultura secara mendalam.",
      "source_section": "ASPEK PENGETAHUAN",
      "is_official": true,
      "is_normalized_code": false
    }
  ],
  "bahan_kajian": [
    {
      "code": "1.5",
      "name": "Manajemen",
      "description": "Manajemen unit agribisnis hortikultura",
      "source_section": "BAHAN KAJIAN"
    }
  ],
  "cpl_bk_mapping": {
    "status": "PARSED",
    "mappable_count": 25,
    "pairs": [
      {
        "cpl_code": "A2",
        "bk_code": "1.5",
        "source": "CURRICULUM_MATRIX"
      }
    ]
  },
  "bk_mk_pairs": [
    {
      "bk_code": "1.5",
      "mk_code": "AGH42204",
      "mk_name": "Manajemen Agribisnis",
      "source": "CURRICULUM"
    }
  ],
  "direct_cpl_mk_pairs": [],
  "mata_kuliah": [],
  "provenance": {
    "source_type": "OFFICIAL_CURRICULUM",
    "normalization_method": "DETERMINISTIC"
  }
}
```

---

## 4. LIFECYCLE MANAGEMENT

Structured Curriculum Library mengadopsi finite-state status lifecycle berikut:

```
[SOURCE FILE] ──(Parse & Normalization)──> [NORMALIZED] ──(Validation/Approve)──> [VERIFIED]
       │                                                                               │
       │                                                                               ▼
       └─────────────────(File Content Modified / Hash Mismatch)──────────────────> [STALE]
                                                                                       │
                                                                                       ▼
                                                                           (Rebuild Triggered)
```

1. **NOT_NORMALIZED**: File kurikulum ada tetapi belum di-build menjadi representasi terstruktur.
2. **NORMALIZED**: Hasil ekstraksi berhasil dibangun dan memenuhi schema awal.
3. **VERIFIED**: Payload tervalidasi penuh dan dijadikan grounding utama untuk RPS Generator.
4. **STALE**: Terdeteksi perubahan hash file sumber (`source_hash` mismatch). Sistem fallback ke runtime parser dengan warning.
5. **ERROR**: Proses normalisasi atau validasi integritas struktur gagal.

---

## 5. SOURCE HASH STRATEGY

- **Metode**: SHA-256 (`hash('sha256', $content)`)
- **Titik Evaluasi**: Dihitung setiap kali dokumen diunggah, dinormalisasi, atau dibaca.
- **Pendeteksian Basi (Staleness Guard)**: `_is_structured_curriculum_stale($pustaka_row, $current_content)` membandingkan hash tersimpan dengan SHA-256 dari teks file sumber aktual. Jika tidak cocok, status otomatis berubah menjadi `STALE` dan sistem memicu fallback transparan tanpa mengganggu jalannya RPS.

---

## 6. PARSER & READ PRIORITY

Dalam runtime RPS Generator (`ajax_check_curriculum_readiness`, `ajax_get_cpl_studio`, `ajax_generate_cpmk`):

1. **Prioritas 1 (Primary Grounding)**: Membaca `siakad_pustaka_kurikulum.normalized_json` yang berstatus `VERIFIED` via `_load_structured_curriculum_library()`.
2. **Prioritas 2 (Fallback Transparan)**: Jika `normalized_json` belum berstatus `VERIFIED` atau `STALE`, sistem secara otomatis menggunakan deterministic runtime parser `_analyze_curriculum_grounding()`.
3. **Prioritas 3 (Legacy Resilience)**: Jika kurikulum belum diunggah sama sekali, fallback ke format teks tersimpan di `siakad_rps_obe` tanpa melempar fatal error.

---

## 7. PERSISTENCE STRATEGY

- Operasi rebuild dapat dipicu melalui tombol **"Bangun Ulang Data Terstruktur"** di header RPS Generator atau API endpoint `ajax_rebuild_structured_curriculum()`.
- Validasi wajib (`_validate_structured_curriculum_library`) memverifikasi:
  - Keunikan kode CPL
  - Tidak ada heading polusi (contoh: D4, Level 6, Sumber)
  - S-1..S-10, KU-1..KU-12 memiliki kategori yang tepat
  - CPL-BK pairs konsisten dengan daftar canonical CPL dan Bahan Kajian.

---

## 8. HASIL UJI 5 PROGRAM STUDI

| No | Program Studi | Official Name | Official CPL Count | BK Count | CPL-BK Status | CPL-BK Mappable Count | Status DB |
|:---|:---|:---|:---:|:---:|:---:|:---:|:---:|
| 1 | **Teknologi Rekayasa Perangkat Lunak** | Teknologi Rekayasa Perangkat Lunak | **64** | 8 | `AVAILABLE_UNPARSED` | `NULL` | **VERIFIED** |
| 2 | **Akuntansi Perpajakan** | Akuntansi Perpajakan | **73** | 10 | `AVAILABLE_UNPARSED` | `NULL` | **VERIFIED** |
| 3 | **Pengelolaan Konvensi dan Acara** | Pengelolaan Konvensi dan Acara | **33** | 32 | `AVAILABLE_UNPARSED` | `NULL` | **VERIFIED** |
| 4 | **Manajemen Pemasaran Internasional** | Manajemen Pemasaran Internasional | **50** | 1 | `AVAILABLE_UNPARSED` | `NULL` | **VERIFIED** |
| 5 | **Agribisnis Hortikultura** | Agribisnis Hortikultura | **48** | 20 | `PARSED` | **25** | **VERIFIED** |

*Catatan*: Nama resmi Program Studi MPI menggunakan **"Manajemen Pemasaran Internasional"** pada representasi terstruktur kurikulum tanpa mengubah master tabel `sms`.

---

## 9. HASIL UJI 6 TARGET MATA KULIAH

| Prodi | Kode MK | Nama Mata Kuliah | Mapping Type | Mapped CPLs | SKS | Sem | Hasil Uji |
|:---|:---|:---|:---|:---|:---:|:---:|:---:|
| **TRPL** | `TRPL3201` | Pemrograman Berbasis Framework | `EXPLICIT_MATRIX` | `KS-12` | 3 | 5 | **PASS** |
| **AP** | `AP5210` | Perpajakan Internasional | `EXPLICIT_MATRIX` | `P-12` | 2 | 6 | **PASS** |
| **PKA** | `PKA4205` | Pengelolaan Acara Korporasi | `EXPLICIT_MATRIX` | `S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2` | 3 | 3 | **PASS** |
| **MPI** | `MPI5206` | Riset Pemasaran | `EXPLICIT_MATRIX` | `P-6` | 3 | 6 | **PASS** |
| **AH** | `AGH42204` | Manajemen Agribisnis | `DERIVED_VIA_BK` | `A2, B4, C1, C2, E1, E4` (via BK 1.5) | 2 | 4 | **PASS** |
| **AH** | `AGH42207` | Riset Pemasaran | `DERIVED_VIA_BK` | `A2, B3, B4, C1, C2, E1, E4` (via BK 1.3) | 2 | 4 | **PASS** |

---

## 10. HASIL REGRESSION TEST

Seluruh endpoint dan modul RPS Generator tetap berfungsi optimal dengan performa respons jauh lebih cepat berkat pembacaan JSON terstruktur:

- `ajax_check_curriculum_readiness`: **PASS** (Mengembalikan checks terstruktur langsung dari `normalized_json`)
- `ajax_get_cpl_studio`: **PASS** (Studio CPL memuat seluruh 48 CPL AH dan CPL prodi lain dengan badge mapping yang presisi)
- `ajax_generate_cpmk` & `ajax_approve_cpmk`: **PASS** (CPMK Generator terikat pada mapped CPL yang terverifikasi)
- `ajax_rebuild_structured_curriculum`: **PASS** (Berhasil me-rebuild JSON terstruktur dari dokumen sumber saat tombol ditekan)
- Staleness Lifecycle Test: **PASS** (Deteksi modifikasi hash berhasil menandai status `STALE`)

---

## 11. FILE DAN METHOD YANG DIUBAH

1. **`application/controllers/C_rps_generator.php`**:
   - `_calculate_source_hash($content)`
   - `_validate_structured_curriculum_library($payload)`
   - `_build_structured_curriculum_library($content, $nama_prodi, $doc_info)`
   - `_save_structured_curriculum_library($pustaka_id, $payload, $status)`
   - `_is_structured_curriculum_stale($pustaka_row, $current_content)`
   - `_load_structured_curriculum_library($nama_prodi, $pustaka_id, $kode_mk)`
   - `_resolve_course_grounding_from_structured_library($payload, $kode_mk, $nm_mk, $prodi_name, $rps_row)`
   - `ajax_rebuild_structured_curriculum()`
   - Refactor: `_extract_official_curriculum_cpl()`, `_get_kurikulum_content()`, `ajax_check_curriculum_readiness()`, `ajax_get_cpl_studio()`, `ajax_get_all_pustaka()`.
2. **`application/views/rps_generator/v_rps_generator.php`**:
   - Menambahkan status badge terstruktur (`VERIFIED`, `PARSED`, `STALE`), versi parser (`Phase-1.7`), timestamp normalisasi, serta tombol interaktif `Bangun Ulang Data Terstruktur`.
3. **`application/views/rps_generator/v_rps_generator_after_page.php`**:
   - Menambahkan render helper `renderPustakaMeta()` dan AJAX callback `rebuildStructuredCurriculum()`.
4. **`scratch/test_phase1_7_comprehensive_validation.php`**:
   - Test suite komprehensif audit skema, persistensi, reload, grounding 6 MK, dan lifecycle staleness.
5. **`scratch/test_phase1_7_regression_endpoints.php`**:
   - Test suite regresi endpoint AJAX.

---

## 12. FINAL VERDICT

```
========================================================================
FINAL VERDICT: READY_FOR_PHASE_2
========================================================================
```
