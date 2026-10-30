# Laporan Audit & Dokumentasi Semantik CPL (Phase 1.6 Final Fix)

**Dokumen**: `docs/RPS_PHASE1_6_SEMANTIC_CPL_FIX.md`  
**Status**: `PASSED (100% VERIFIED)`  
**Tanggal**: 18 September 2026  
**Scope**: Final Semantic Separation of CPL Counts (D4 Agribisnis Hortikultura & All 5 Prodis)

---

## 1. Definisi Semantik Resmi CPL & Bahan Kajian

Telah ditetapkan pemisahan semantik secara ketat dan deterministik antara 3 konsep metrik akademik:

| Konsep Semantik | Key Sistem / Variabel | Definisi Akademik | D4 Agribisnis Hortikultura (AH) |
| :--- | :--- | :--- | :--- |
| **Official CPL Prodi** | `official_cpl`<br>`official_cpl_count` | Seluruh butir CPL formal yang tercantum pada naskah kurikulum Bab *CAPAIAN PEMBELAJARAN D4 AGRIBISNIS HORTIKULTURA* meliputi Sikap (S), Pengetahuan (P), Keterampilan Umum (KU), dan Keterampilan Khusus (KK). | **48 CPL**<br>(S: 10, P: 16, KU: 12, KK: 10) |
| **CPL-BK Mappable** | `cpl_bk_mappable`<br>`cpl_bk_mappable_count` | Subset butir CPL (Pengetahuan & Keterampilan Khusus) yang tercantum pada tabel *Matriks I: HUBUNGAN BAHAN KAJIAN DAN CAPAIAN PEMBELAJARAN*. | **25 CPL**<br>(A1..A5, B1..B7, C1..C4, D, E1..E5, F, G, H) |
| **Mapped CPL MK** | `mapped_cpls`<br>`mapped_cpl_count` | Subset butir CPL yang terpetakan untuk mata kuliah tertentu, baik melalui direct matrix (`EXPLICIT_MATRIX`), blok uraian kurikulum (`EXPLICIT_SECTION`), maupun penurunan deterministik Bahan Kajian (`DERIVED_VIA_BK`). | **Mengikuti MK masing-masing**<br>• Manajemen Agribisnis: **6 CPL**<br>• Riset Pemasaran: **7 CPL** |

---

## 2. Hasil Audit Komprehensif 5 Program Studi

Pengujian dilakukan menggunakan automated test runner terhadap endpoint `ajax_check_curriculum_readiness` dan `ajax_get_cpl_studio`.

### A. D4 Agribisnis Hortikultura (AH)
* **Official CPL**: `48 CPL` (S: 10, P: 16, KK: 10, KU: 12)
* **CPL-BK Mappable**: `25 CPL`
* **Mata Kuliah 1: AGH42204 — Manajemen Agribisnis**
  * Semester: `4`, SKS: `1/1/2`
  * Target BK: `1.5, 6.4` (2 BK terkait dari 20 BK kurikulum)
  * Mapped CPL: `6 CPL` (`A2, B4, C1, C2, E1, E4`)
  * Effective Mapping Type: `DERIVED_VIA_BK`
  * Evidence Path: `CPL ↔ BK (1.5, 6.4) ↔ MK (Manajemen Agribisnis)`
  * Studio CPL Badges: `6 Terpetakan via BK`, `42 CPL Resmi / Belum Terpetakan`
* **Mata Kuliah 2: AGH52206 — Riset Pemasaran**
  * Semester: `5`, SKS: `0/2/2`
  * Target BK: `1.3, 6.4` (2 BK terkait dari 20 BK kurikulum)
  * Mapped CPL: `7 CPL` (`A2, B3, B4, C1, C2, E1, E4`)
  * Effective Mapping Type: `DERIVED_VIA_BK`
  * Evidence Path: `CPL ↔ BK (1.3, 6.4) ↔ MK (Riset Pemasaran)`
  * Studio CPL Badges: `7 Terpetakan via BK`, `41 CPL Resmi / Belum Terpetakan`

---

### B. D4 Teknologi Rekayasa Perangkat Lunak (TRPL)
* **Official CPL**: `64 CPL` (S: 11, P: 24, KU: 13, KK: 16)
* **CPL-BK Mappable**: `0` (Mapping langsung CPL ↔ MK)
* **Mata Kuliah: TRPL3201 — Pemrograman Berbasis Framework**
  * Semester: `5`, SKS: `1/2/3`
  * Mapped CPL: `1 CPL` (`KS-12`)
  * Effective Mapping Type: `EXPLICIT_MATRIX`
  * Evidence: `Matriks Langsung CPL ↔ MK Kurikulum (KS-12)`
  * Studio CPL Badges: `1 Terpetakan Langsung`, `63 CPL Resmi / Belum Terpetakan`

---

### C. D4 Akuntansi Perpajakan (AP)
* **Official CPL**: `73 CPL` (S: 11, P: 28, KU: 13, KK: 21)
* **CPL-BK Mappable**: `0` (Mapping langsung CPL ↔ MK)
* **Mata Kuliah: AP5210 — Perpajakan Internasional**
  * Semester: `6`, SKS: `2/0/2`
  * Mapped CPL: `1 CPL` (`P-12`)
  * Effective Mapping Type: `EXPLICIT_MATRIX`
  * Evidence: `Matriks Langsung CPL ↔ MK Kurikulum (P-12)`
  * Studio CPL Badges: `1 Terpetakan Langsung`, `72 CPL Resmi / Belum Terpetakan`

---

### D. D4 Pengelolaan Konvensi dan Acara (PKA)
* **Official CPL**: `33 CPL` (S: 11, KU: 9, KK: 6, P: 7)
* **CPL-BK Mappable**: `0` (Mapping langsung CPL ↔ MK)
* **Mata Kuliah: PKA4205 — Pengelolaan Acara Korporasi**
  * Semester: `3`, SKS: `1/2/3`
  * Mapped CPL: `8 CPL` (`S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2`)
  * Effective Mapping Type: `EXPLICIT_MATRIX`
  * Evidence: `Matriks Langsung CPL ↔ MK Kurikulum (S-1, S-2, KU-1, KU-2, KK-1, KK-2, P-1, P-2)`
  * Studio CPL Badges: `8 Terpetakan Langsung`, `25 CPL Resmi / Belum Terpetakan`

---

### E. D4 Manajemen Perusahaan Internasional (MPI)
* **Official CPL**: `50 CPL` (S: 11, KU: 9, P: 10, KK: 20)
* **CPL-BK Mappable**: `0` (Mapping langsung CPL ↔ MK)
* **Mata Kuliah: MPI5206 — Riset Pemasaran**
  * Semester: `6`, SKS: `1/2/3`
  * Mapped CPL: `1 CPL` (`P-6`)
  * Effective Mapping Type: `EXPLICIT_MATRIX`
  * Evidence: `Matriks Langsung CPL ↔ MK Kurikulum (P-6)`
  * Studio CPL Badges: `1 Terpetakan Langsung`, `49 CPL Resmi / Belum Terpetakan`

---

## 3. Format Tampilan Antarmuka (UI Checklist & Badges)

### A. Panel Kiri (Curriculum Readiness Checklist)
Pemisahan baris indikator kesiapan kurikulum:
1. **Dokumen Kurikulum**: `Tersedia`
2. **Mata Kuliah di Kurikulum**: `Ditemukan`
3. **CPL Prodi**: `48 CPL ditemukan`
4. **Pemetaan CPL ↔ BK**: `25 CPL memiliki mapping BK` (atau `Tidak ada matriks CPL ↔ BK` untuk prodi dengan direct mapping)
5. **Pemetaan CPL ↔ MK**: `6 CPL terpetakan untuk MK terpilih` (atau `7 CPL...` / `1 CPL...`)
6. **Bahan Kajian**: `20 BK kurikulum / 2 BK terkait MK`
7. **Deskripsi Mata Kuliah**: `Tersedia dari Kurikulum`
8. **CPMK Terkini**: `Belum Ada` / `Draft AI` / `APPROVED`

### B. Studio CPL (Badge Status Pemetaan)
Setiap kartu CPL menampilkan seluruh 48 CPL resmi dengan badge status eviden yang presisi:
- `<span class="badge"><i class="fa fa-sitemap"></i> Terpetakan via BK</span>` (untuk CPL hasil relasi BK-MK)
- `<span class="badge"><i class="fa fa-link"></i> Terpetakan Langsung</span>` (untuk CPL direct matrix/section)
- `<span class="badge"><i class="fa fa-shield"></i> CPL Resmi</span>` (untuk CPL resmi prodi lainnya yang belum terpetakan ke MK ini)

---

## 4. Kesimpulan & Verifikasi
* Seluruh 48 butir CPL resmi D4 Agribisnis Hortikultura tetap utuh tanpa ada CPL yang terhapus atau disembunyikan.
* Metrik `25` diakui secara akurat sebagai jumlah CPL yang memiliki pemetaan pada *Matriks I (CPL-BK)*.
* Parser TRPL (64 CPL), AP (73 CPL), PKA (33 CPL), dan MPI (50 CPL) tetap stabil 100% dan konsisten.
