# PHASE 1.7 — TRUST & GOVERNANCE GUARD FOR CURRICULUM MAPPING STATUS
**Dokumen Laporan & Tata Kelola Status Pemetaan Kurikulum**
*Tanggal Implementasi: 19 September 2026*

---

## 1. Executive Summary & Core Principle

Berdasarkan prinsip dasar tata kelola kurikulum dan integritas data akademik:
1. **Dokumen Kurikulum Resmi (SK/PDF/Pustaka Kurikulum) = SOURCE OF TRUTH (Faktual Tertinggi).**
2. **Parser = Alat Ekstraksi Deterministik.**
3. **AI = Alat Bantu/Advisory (Bukan Penentu Kebenaran Mapping Resmi).**

Apabila dokumen kurikulum resmi memuat section, tabel, atau matriks pemetaan resmi (seperti Matriks CPL ↔ MK), kendala ekstraksi oleh parser **TIDAK BOLEH** diterjemahkan sebagai kesimpulan akademik *"Belum terpetakan"* atau *"Tidak ada pemetaan"*. Sistem wajib membedakan secara tegas antara *"Dokumen tidak memiliki pemetaan"* vs *"Pemetaan tersedia di dokumen namun belum berhasil dibaca sistem"*.

---

## 2. Final Status Taxonomy

Untuk membedakan status pemetaan sumber kurikulum dan derajat keterbacaannya oleh parser, ditetapkan taksonomi status berikut:

| Status Taxonomy | Definisi Semantik | Derajat Keterbacaan |
| :--- | :--- | :--- |
| **`SOURCE_MAPPING_PARSED`** | Pemetaan resmi pada dokumen kurikulum berhasil diekstraksi secara deterministik dan lengkap untuk seluruh mata kuliah. | 100% Parsed |
| **`SOURCE_MAPPING_PARTIAL`** | Matriks/section pemetaan resmi terdeteksi di dokumen, dan sebagian relasi mata kuliah berhasil dibaca deterministik, namun relasi mata kuliah lainnya belum tuntas diekstrak. | > 0% & < 100% Parsed |
| **`SOURCE_MAPPING_AVAILABLE_UNPARSED`** | Matriks/section pemetaan resmi terdeteksi di dokumen kurikulum, namun sistem parser belum berhasil mengekstrak relasi deterministiknya. | 0% Parsed (Source Exists) |
| **`SOURCE_MAPPING_PARSER_ERROR`** | Matriks/section pemetaan resmi terdeteksi di dokumen kurikulum, namun terjadi kendala teknis (parsing error / alignment crash). | Parsing Failed |
| **`SOURCE_MAPPING_NOT_AVAILABLE`** | Dokumen kurikulum resmi diverifikasi secara faktual memang **tidak memuat** matriks atau uraian pemetaan CPL-MK resmi. | Factual Absence |

---

## 3. UI Wording Standard & Enforcement

### Aturan Ketat UI:
> **DILARANG** menggunakan label *"Belum terpetakan"* sebagai kesimpulan final jika status dokumen kurikulum adalah `SOURCE_MAPPING_AVAILABLE_UNPARSED`, `SOURCE_MAPPING_PARTIAL`, atau `SOURCE_MAPPING_PARSER_ERROR`.

### Standar Wording Antarmuka (UI):

| Status Taxonomy | Label / Wording UI Wajib | Badge Styling |
| :--- | :--- | :--- |
| **`SOURCE_MAPPING_PARSED`** | `"Pemetaan resmi kurikulum berhasil dibaca lengkap"` atau `"Terpetakan Langsung"` | Success (`#dcfce7` / `#15803d`) |
| **`SOURCE_MAPPING_PARTIAL`** | `"Pemetaan sebagian berhasil dibaca"` | Warning (`#fef3c7` / `#b45309`) |
| **`SOURCE_MAPPING_AVAILABLE_UNPARSED`** | `"Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"` | Warning/Info (`#fef3c7` / `#b45309`) |
| **`SOURCE_MAPPING_PARSER_ERROR`** | `"Pemetaan tersedia di kurikulum — terjadi kendala parsing"` | Warning (`#fee2e2` / `#b91c1c`) |
| **`SOURCE_MAPPING_NOT_AVAILABLE`** | `"Tidak ada pemetaan resmi pada dokumen kurikulum"` | Slate/Neutral (`#f1f5f9` / `#64748b`) |

---

## 4. AI Guard & Fallback Advisory Policy

1. **Larangan Pre-selection Otomatis:**
   Jika mapping resmi berstatus `SOURCE_MAPPING_AVAILABLE_UNPARSED` atau `SOURCE_MAPPING_PARTIAL`, sistem **DILARANG** melakukan auto-preselection rekomendasi AI sebagai mapping resmi kurikulum di Studio CPL.
2. **Eksplisitisasi Label Provenance AI:**
   Jika dosen meminta bantuan AI untuk merekomendasikan CPL (`ajax_generate_cpl_recommendation`), seluruh butir yang disarankan wajib diberi provenance:
   - `provenance` = `AI_PROPOSAL_MAPPING`
   - `source` = `AI_PROPOSAL_MAPPING`
   - UI Label = `Rekomendasi AI — bukan mapping resmi kurikulum`
3. **Banner Studio CPL:**
   Ketika Studio CPL dibuka untuk mata kuliah yang dokumen kurikulumnya memiliki pemetaan unparsed / partial, muncul banner penegasan:
   > *"Dokumen kurikulum memiliki pemetaan resmi yang belum seluruhnya berhasil dibaca sistem. Pilihan CPL perlu ditinjau."*

---

## 5. Audit Trail Metadata Schema

Setiap pustaka kurikulum ternormalisasi (`siakad_pustaka_kurikulum` dan normalized payload JSON) kini menyertakan objek audit struktural:

```json
"cpl_mk_mapping_audit": {
  "mapping_source_exists": true,
  "mapping_source_section": "MATRIKS II HUBUNGAN MATA KULIAH DAN CAPAIAN PEMBELAJARAN",
  "mapping_parse_status": "SOURCE_MAPPING_PARSED",
  "mapping_parse_message": "Matriks II CPL-MK resmi berhasil dibaca deterministik untuk seluruh 66 mata kuliah (331 relasi CPL-MK)."
}
```

Setiap mata kuliah yang relasinya belum terbaca parser mengusung metadata course-level:
```json
{
  "kode_mk": "MPI102",
  "nama_mk": "Komunikasi Bisnis Internasional",
  "effective_cpl_codes": [],
  "effective_mapping_type": "SOURCE_UNPARSED",
  "mapping_evidence": "Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"
}
```

---

## 6. Real-World Case Studies Across Programs

### 6.1. Kasus Agribisnis Hortikultura (AH)
- **Kondisi Dokumen:** Memiliki Bab *"MATRIKS II — HUBUNGAN MATA KULIAH DAN CAPAIAN PEMBELAJARAN"*.
- **Hasil Parsing:** 66/66 Mata Kuliah (331 relasi CPL-MK) terbaca deterministik.
- **`mapping_source_exists`:** `true`
- **`mapping_source_section`:** `"MATRIKS II HUBUNGAN MATA KULIAH DAN CAPAIAN PEMBELAJARAN"`
- **`mapping_parse_status`:** `SOURCE_MAPPING_PARSED`
- **UI Label:** `"Terpetakan Langsung (Matriks II CPL-MK Kurikulum)"`

### 6.2. Kasus Manajemen Pemasaran Internasional (MPI)
- **Kondisi Dokumen:** Memiliki section *"MATRIKS CAPAIAN PEMBELAJARAN DENGAN MATA KULIAH"*.
- **Hasil Parsing Saat Ini:** 1/63 MK berhasil diekstrak deterministik; 62 MK lainnya dalam proses penyempurnaan parser tabel.
- **`mapping_source_exists`:** `true`
- **`mapping_source_section`:** `"MATRIKS CAPAIAN PEMBELAJARAN DENGAN MATA KULIAH"`
- **`mapping_parse_status`:** `SOURCE_MAPPING_PARTIAL`
- **UI Label (MK Terbaca):** `"Terpetakan Langsung"`
- **UI Label (MK Belum Terbaca):** `"Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"` (Bukan *"Belum terpetakan"*!)

### 6.3. Kasus TRPL, AP, dan PKA
- **TRPL (Teknologi Rekayasa Perangkat Lunak):**
  - Section Matriks terdeteksi (`mapping_source_exists: true`).
  - Status: `SOURCE_MAPPING_PARTIAL` (1/54 MK terbaca, 53 MK unparsed).
  - UI Wording: `"Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"`.
- **AP (Akuntansi Perpajakan):**
  - Section Matriks terdeteksi (`mapping_source_exists: true`).
  - Status: `SOURCE_MAPPING_PARTIAL` (1/47 MK terbaca, 46 MK unparsed).
  - UI Wording: `"Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"`.
- **PKA (Pengelolaan Konvensi dan Acara):**
  - Section Matriks terdeteksi (`mapping_source_exists: true`).
  - Status: `SOURCE_MAPPING_PARTIAL` (1/49 MK terbaca, 48 MK unparsed).
  - UI Wording: `"Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem"`.

---

## 7. Regression & Impact Analysis

1. **Kepastian Akademik Dosen:**
   Dosen tidak lagi disajikan informasi yang menyesatkan seolah-olah program studi mereka belum memiliki pemetaan CPL-MK resmi. Dosen memahami bahwa dokumen kurikulum telah memilikinya dan sistem sedang dalam proses penyempurnaan pembacaan tabel kompleks.
2. **Kemandirian Studio CPL:**
   Dosen tetap dapat memilih butir CPL resmi prodi secara mandiri (`MANUAL`) di Studio CPL dengan grounding CPL yang valid, tanpa dipaksa menerima AI mapping yang salah sasaran.
3. **Integritas Structured Curriculum Library:**
   Pustaka kurikulum tetap berstatus `NORMALIZED` / `VERIFIED` untuk struktur umum (CPL, BK, Daftar MK, Profil Lulusan), namun field `cpl_mk_mapping_audit` secara transparan mencatat status parser mapping CPL-MK pada tingkat granular.

---

## 8. Final Verdict

```
============================================================
FINAL VERDICT: PASS
============================================================
- Status taxonomy implemented in Controllers & Views.
- UI wording sanitized across all views and tabs.
- AI provenance explicitly branded as AI_PROPOSAL_MAPPING.
- Audit trail embedded in all normalized payloads.
- Zero syntax / lint regression detected across codebase.
============================================================
```
