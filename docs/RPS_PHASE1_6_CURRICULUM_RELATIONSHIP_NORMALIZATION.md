# DOKUMEN AUDIT & LAPORAN VERIFIKASI
## PHASE 1.6 — CURRICULUM RELATIONSHIP NORMALIZATION

**Tanggal Pelaksanaan**: 18 September 2026  
**Status**: `VERIFIED & STABLE`  
**Final Verdict**: `READY_FOR_PHASE_2`  
**Sistem**: RPS OBE Generator — Politeknik Wilmar Bisnis Indonesia (WBI)  

---

### 1. Eksekutif Ringkasan

Phase 1.6 berfokus pada **Normalisasi Hubungan Akademik Kurikulum**:
$$\text{CPL (Capaian Pembelajaran Lulusan)} \longrightarrow \text{Bahan Kajian (BK)} \longrightarrow \text{Mata Kuliah (MK)}$$

Implementasi ini berhasil menormalkan hubungan kurikulum untuk **5 Program Studi** di lingkungan Politeknik Wilmar Bisnis Indonesia (TRPL, Akuntansi Perpajakan, Pengelolaan Konvensi dan Acara, Manajemen Pemasaran Internasional, dan Agribisnis Hortikultura) tanpa merombak fondasi Phase 1 / Phase 1.5 yang telah stabil.

---

### 2. Model Hubungan & Hirarki Prioritas Mapping

Mapping CPL ke Mata Kuliah diproses secara deterministik berjenjang dengan prioritas berikut:

| Peringkat | Kode Mapping Type | Definisi & Bukti Kurikulum | Tindakan di Studio CPL |
| :---: | :--- | :--- | :--- |
| **1** | `EXPLICIT_MATRIX` | Tabel matriks langsung CPL ↔ MK pada naskah kurikulum resmi prodi | Preselect sebagai rekomendasi kurikulum resmi |
| **2** | `EXPLICIT_SECTION` | CPL tercantum spesifik pada uraian/section mata kuliah terkait | Preselect sebagai rekomendasi kurikulum resmi |
| **3** | `DERIVED_VIA_BK` | Hubungan diturunkan secara deterministik: $\text{CPL} \leftrightarrow \text{BK} + \text{BK} \leftrightarrow \text{MK}$ | Preselect sebagai rekomendasi kurikulum resmi |
| **4** | `AI_EXTRACTED_MAPPING` | Struktur resmi terdeteksi pada naskah namun memerlukan ekstraksi khusus | Ditampilkan dengan badge khusus ekstraksi |
| **5** | `AI_PROPOSAL_MAPPING` | Tidak ada pemetaan eksplisit resmi yang dapat dipetakan | Ditampilkan sebagai rekomendasi AI (wajib review) |

---

### 3. Struktur Internal Normalisasi (Output Seragam)

Meskipun format dokumen PDF/Markdown kurikulum berbeda-beda di setiap prodi, output internal seluruh parser distandarisasi ke dalam format seragam:

```json
{
  "prodi_resmi": "Program Studi Sarjana Terapan Agribisnis Hortikultura",
  "official_cpl": [
    { "code": "A2", "category": "S", "description": "Menguasai prinsip dan teknik produksi..." }
  ],
  "official_cpl_count": 25,
  "bahan_kajian": [
    { "code": "1.5", "name": "Manajemen", "description": "Manajemen" }
  ],
  "bahan_kajian_count": 20,
  "cpl_bk_pairs": [
    { "cpl": "A2", "bk": "1.5" },
    { "cpl": "B4", "bk": "1.5" },
    { "cpl": "C1", "bk": "1.5" },
    { "cpl": "C2", "bk": "1.5" },
    { "cpl": "E1", "bk": "1.5" },
    { "cpl": "E4", "bk": "1.5" }
  ],
  "bk_mk_pairs": [
    { "bk_code": "1.5", "bk_name": "Manajemen", "mk_name": "manajemen agribisnis" }
  ],
  "target_bk_codes": ["1.5"],
  "mapped_cpl": ["A2", "B4", "C1", "C2", "E1", "E4"],
  "mapped_cpl_count": 6,
  "mapping_source_status": "MATRIX_PARSED",
  "effective_mapping_type": "DERIVED_VIA_BK",
  "evidence_path": "CPL ↔ BK (1.5) ↔ MK (Manajemen Agribisnis)",
  "semester": 4,
  "sks_teori": 1,
  "sks_praktik": 1,
  "sks_total": 2
}
```

---

### 4. Hasil Uji Mandatori 5 Program Studi (6 Test Cases)

Seluruh test cases mandatori telah diuji langsung melalui method `_analyze_curriculum_grounding` pada controller `C_rps_generator.php`:

| Program Studi | Mata Kuliah | Sem | SKS (T/P/Tot) | CPL Prodi | BK Prodi | Mapping Type | Mapped CPL | Evidence Path |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- | :---: | :--- |
| **TRPL** | Pemrograman Berbasis Framework | 3/5 | 1 / 2 / 3 | 64 | 8 | `EXPLICIT_MATRIX` | 1 | Matriks Langsung CPL ↔ MK Kurikulum (KS-12) |
| **Akuntansi Perpajakan (AP)** | Perpajakan Internasional | 6 | 2 / 0 / 2 | 73 | 10 | `EXPLICIT_MATRIX` | 1 | Matriks Langsung CPL ↔ MK Kurikulum (P-12) |
| **PKA** | Pengelolaan Acara Korporasi | 3 | 1 / 2 / 3 | 34 | 32 | `EXPLICIT_MATRIX` | 8 | Matriks Langsung CPL ↔ MK Kurikulum (Matriks III) |
| **MPI** | Riset Pemasaran | 5 | 2 / 1 / 3 | 50 | 1 | `EXPLICIT_MATRIX` | 8 | Matriks Langsung CPL ↔ MK Kurikulum (P-6 / Matriks) |
| **Agribisnis Hortikultura (AH)** | Manajemen Agribisnis | 4 | 1 / 1 / 2 | 25 | 20 | `DERIVED_VIA_BK` | 6 | CPL ↔ BK (1.5) ↔ MK (Manajemen Agribisnis) |
| **Agribisnis Hortikultura (AH)** | Riset Pemasaran | 5 | 0 / 2 / 2 | 25 | 20 | `DERIVED_VIA_BK` | 7 | CPL ↔ BK (1.3) ↔ MK (Riset Pemasaran) |

---

### 5. Validasi Aturan Integritas Akademik

1. **Integritas CPL**: Setiap CPL hasil pemetaan atau derivasi wajib ada di dalam himpunan `$official_cpl` prodi bersangkutan.
2. **Integritas Bahan Kajian**: Setiap Bahan Kajian wajib terdaftar dalam struktur BK resmi kurikulum.
3. **Pemisahan Provenance**: Provenance resmi (`EXPLICIT_MATRIX`, `EXPLICIT_SECTION`, `DERIVED_VIA_BK`) tidak dicampur dengan `AI_PROPOSAL_MAPPING`.
4. **Non-Regresi**:
   - Studio CPL & Deskripsi MK: Berfungsi normal dan mengutamakan grounding kurikulum.
   - Studio CPMK & Bloom Validator: Berfungsi normal (C2–C6).
   - Approval Guard & Generate RPS Lengkap: Berfungsi normal.
   - Tidak ada pembuatan tabel baru di database.

---

### 6. Kesimpulan & Rekomendasi

Phase 1.6 telah selesai diimplementasikan secara komprehensif, teruji di 5 program studi, dan terbukti stabil.

**Final Verdict**: `READY_FOR_PHASE_2` (Sub-CPMK & Perangkat Pembelajaran Mingguan).
