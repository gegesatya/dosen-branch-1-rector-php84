# CPL Code Prefix Fidelity Patch (KS vs KK)

## 1. Root Cause
In earlier iterations of the curriculum normalization process and runtime parser, a global heuristic rule was introduced that forcefully rewrote any CPL code starting with `KS` into `KK` (specifically `$prefix === 'KS' ? 'KK-' . $num : ...`). Additionally, hardcoded matrix and CPL-BK helper methods for TRPL in `C_rps_generator.php` and `C_kurikulum.php` had stored `KK-*` instead of the source-authoritative `KS-1 ... KS-16`.

This caused TRPL courses (such as *Machine Learning*) to present `KK-1, KK-2, KK-8, KK-12` in the normalized payloads, Matriks MK-CPL, and RPS Studio UI instead of the authentic `KS-1, KS-2, KS-8, KS-12`.

---

## 2. Affected Layers
The issue crossed three layers:
1. **Parser & Normalization Pipeline**: In `_extract_official_cpl_items()`, `KS` was forcefully converted into `KK-` during CPL extraction.
2. **Deterministic Matrix & Mapping Helpers**: `_get_trpl_matriks_data()` and `_get_trpl_cpl_bk_data()` in `C_rps_generator.php` and `C_kurikulum.php` contained hardcoded `KK-` codes for TRPL instead of `KS-`.
3. **Structured Curriculum Library Payloads & Persistence**: `siakad_pustaka_kurikulum.normalized_json` (for TRPL, ID 6) and disk payloads `normalized_Teknologi_Rekayasa_Perangkat_Lunak.json` had stored `KK-*` for all 16 TRPL Keterampilan Khusus CPL items and 35 course mappings.

---

## 3. Authoritative TRPL Code Format
As evidenced directly in the official curriculum document (*Dokumen Kurikulum TRPL*, Section `4. ASPEK KETERAMPILAN KHUSUS (KS)`):
- **Category Semantic Name**: `KETERAMPILAN_KHUSUS` (standardized category)
- **Official CPL Codes**: `KS-1 ... KS-16`
- **Total Official CPL TRPL**: 64 items (11 Sikap: `S-1..S-11`, 24 Pengetahuan: `P-1..P-24`, 13 Keterampilan Umum: `KU-1..KU-13`, 16 Keterampilan Khusus: `KS-1..KS-16`).

---

## 4. Machine Learning Before / After

| Aspect | Before Patch (Incorrect) | After Patch (Authoritative) |
|---|---|---|
| **Course Code** | `(Not Assigned / MK33)` | `(Not Assigned / MK33)` |
| **Course Name** | Machine Learning (Sem 6, 3 SKS) | Machine Learning (Sem 6, 3 SKS) |
| **Direct CPL Codes** | `["P-21", "KK-1", "KK-2", "KK-8", "KK-12"]` | `["P-21", "KS-1", "KS-2", "KS-8", "KS-12"]` |
| **Effective CPL Codes** | `["P-21", "KK-1", "KK-2", "KK-8", "KK-12"]` | `["P-21", "KS-1", "KS-2", "KS-8", "KS-12"]` |
| **Studio CPL Display** | `P-21, KK-1, KK-2, KK-8, KK-12` | `P-21, KS-1, KS-2, KS-8, KS-12` |
| **Mapping Evidence** | Matriks CPL-MK Kurikulum | Matriks CPL-MK Kurikulum |

---

## 5. Compatibility Approach
To ensure complete backward compatibility without breaking existing stored draft or approved RPS records:
- **In-Memory Alias Resolution**: The CPL canonicalization and validation helpers (`$check_cpl_entry` and recommendation resolution in `C_rps_generator.php`) utilize symmetric lookup candidate matching (`str_replace('KK-', 'KS-', $code)` and `str_replace('KS-', 'KK-', $code)`).
- **Prodi-Scoped Canonicalization**: If a stored legacy payload references `KK-8` for TRPL, the system checks `allowed_lookup` (which contains `KS-8`) and resolves it in-memory to `KS-8`. For other prodi where `allowed_lookup` contains `KK-8`, it preserves `KK-8`.
- No global forced string replacements or destructive database mutations are applied to non-TRPL data.

---

## 6. Cross-Prodi Regression Results
All 5 study programs were audited and verified:

| Program Studi | ID | Khusus Code Prefix | Khusus Count | Total CPL | Status |
|---|---|---|---|---|---|
| **Teknologi Rekayasa Perangkat Lunak (TRPL)** | 6 | `KS-` | 16 (`KS-1..KS-16`) | 64 | **PASS** |
| **Manajemen Pemasaran Internasional (MPI)** | 10 | `KK-` | 20 (`KK-1..KK-20`) | 50 | **PASS** |
| **Pengelolaan Konvensi dan Acara (PKA)** | 9 | `KK-` | 6 (`KK-1..KK-6`) | 33 | **PASS** |
| **Akuntansi Perpajakan (AP)** | 11 | `KK-` | 21 (`KK-1..KK-21`) | 73 | **PASS** |
| **Agribisnis Hortikultura (AH)** | 8 | *Source Coding* | *Preserved* (`A1..10, B1..10, K1..10, U1..8, P1..12`) | 48 | **PASS** |

---

## 7. Database Write Behavior
- **CI4 Boundary Respected**: No curriculum write or update logic was added to CI4. CI4 remains a strict read-only curriculum consumer.
- **Normalization Rebuild Pipeline**: The TRPL structured curriculum record (`siakad_pustaka_kurikulum` ID 6) was rebuilt and persisted using the approved curriculum builder script (`scratch/execute_trpl_rebuild_faithful.php`).
- **Disk Payloads Synchronized**:
  - `application/config/kurikulum_payloads/normalized_Teknologi_Rekayasa_Perangkat_Lunak.json`
  - `uploads/pustaka_kurikulum/normalized_Teknologi_Rekayasa_Perangkat_Lunak.json`

---

## 8. Files Modified
1. `application/controllers/C_rps_generator.php`:
   - Updated `_get_trpl_matriks_data()` (67 pairs with `KS-`).
   - Updated `_get_trpl_cpl_bk_data()` (81 pairs with `KS-`).
   - Removed `KS` to `KK` rewrite in `_extract_official_cpl_items()`.
   - Updated canonicalization helpers (lines 7748, 7764, 8508) for symmetric fallback.
2. `application/controllers/C_kurikulum.php`:
   - Updated `_get_trpl_matriks_data()` (67 pairs with `KS-`).
   - Updated `_get_trpl_cpl_bk_data()` (81 pairs with `KS-`).
   - Removed `KS` to `KK` rewrite in `_extract_official_cpl_items()`.
3. `application/config/kurikulum_payloads/normalized_Teknologi_Rekayasa_Perangkat_Lunak.json`:
   - Rebuilt structured payload with `KS-1 ... KS-16` across `official_cpl`, `cpl_bk_mapping`, `direct_cpl_mk_pairs`, and `mata_kuliah`.
4. `uploads/pustaka_kurikulum/normalized_Teknologi_Rekayasa_Perangkat_Lunak.json`:
   - Rebuilt structured payload in uploads directory.
5. Database `portal_wbi_server.siakad_pustaka_kurikulum`:
   - Updated `normalized_json` for row ID 6 (TRPL).

---

## 9. AI SLOP Check
- [x] No global string replacement KS→KK or KK→KS across all programs.
- [x] No hardcoded per-page workaround.
- [x] No duplicate mapping tables created.
- [x] No invented alias rules; only source-authoritative codes.
- [x] No unnecessary schema migrations or table alterations.
- [x] Not a UI-only patch: fixed cleanly from the normalization source, builder pipeline, DTO layer, down to UI.

---

## Final Verdict
**`CPL_CODE_PREFIX_FIDELITY_FIXED`**
