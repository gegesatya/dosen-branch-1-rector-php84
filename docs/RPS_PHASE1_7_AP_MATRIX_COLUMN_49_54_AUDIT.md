# PHASE 1.7 — AP MATRIX COLUMN 49–54 SOURCE-FIDELITY AUDIT REPORT
**Dokumen Audit Faktualitas Sumber dan Crosswalk Kolom MK49–MK54 Kurikulum D4 Akuntansi Perpajakan**
*Tanggal Audit: 19 September 2026*

---

## 1. Executive Summary & Audit Objective

Audit ini dilakukan untuk memastikan bahwa identitas kolom **MK49–MK54** pada Matriks CPL-MK Program Studi **D4 Akuntansi Perpajakan (AP)** ditentukan **hanya dari bukti eksplisit dokumen resmi kurikulum**, tanpa menggunakan asumsi semantic, inferensi AI, atau penamaan modul buatan yang tidak tertulis pada sumber asli.

---

## 2. Source Evidence Analysis for Columns MK49–MK54

Berdasarkan pemeriksaan dokumen kurikulum resmi (*uploads/pustaka_kurikulum/doc_Akuntansi_Perpajakan_1789143924.pdf*):

### 2.1. Bukti Struktur Penomoran Kode Mata Kuliah WBI (Section XI & XIII)
Standar penamaan kode mata kuliah Politeknik WBI untuk Program Studi AP menggunakan pola 6/7 digit deterministik:
$$\text{[Semester (1 digit)]} + \text{[Kelompok MK (2 digit)]} + \mathbf{[Nomor\ Matriks\ MK\ (2\ digit)]} + \text{[SKS (1-2 digit)]}$$

Bukti penomoran kode pada dokumen resmi:
- `100` **`01`** `2` $\rightarrow$ Digit 4–5: **`01`** $\rightarrow$ Mata Kuliah **Agama** = **`MK1`**
- `100` **`02`** `2` $\rightarrow$ Digit 4–5: **`02`** $\rightarrow$ Mata Kuliah **Pancasila** = **`MK2`**
- $\dots$
- `602` **`47`** `2` $\rightarrow$ Digit 4–5: **`47`** $\rightarrow$ Mata Kuliah **Etika profesi dan tata kelola korporasi** = **`MK47`**
- `601` **`48`** `3` $\rightarrow$ Digit 4–5: **`48`** $\rightarrow$ Mata Kuliah **KKN - Tematik** = **`MK48`**
- `702` **`53`** `20` $\rightarrow$ Digit 4–5: **`53`** $\rightarrow$ Mata Kuliah **Pemagangan** = **`MK53`** *(Bukan MK49!)*
- `802` **`54`** `6` $\rightarrow$ Digit 4–5: **`54`** $\rightarrow$ Mata Kuliah **Tugas Akhir** = **`MK54`** *(Bukan MK50!)*

### 2.2. Bukti Fisik Kolom Matriks (Section X, Hal. 27–28)
- Header tabel matriks pada Halaman 27 secara eksplisit mencetak label kolom `MK1` s.d. `MK54`.
- Kolom `MK49`, `MK50`, `MK51`, dan `MK52` memuat tanda 'V' relasi CPL:
  - `MK49`: `S-10, P-28, KU-2, KK-21` (4 'V')
  - `MK50`: `S-8, S-9, KU-5, KU-7, KU-8, KU-10` (6 'V')
  - `MK51`: `S-6` (1 'V')
  - `MK52`: `P-26` (1 'V')
- Namun, **tidak ada teks judul/nama mata kuliah** maupun kode mata kuliah dengan digit `49`, `50`, `51`, atau `52` pada Section IX, X, XI, XIII, maupun XIV.

---

## 3. Alternative Crosswalk Hypotheses Evaluation

| Hipotesis | Formulasi Pemetaan | Dasar Bukti Dokumen | Evaluasi & Status |
| :--- | :--- | :--- | :---: |
| **HYPOTHESIS A (Accepted)** | • `MK1`..`MK48` = MK Semester 1..6 (`100012` s.d. `601483`)<br>• `MK49`..`MK52` = `UNRESOLVED_MATRIX_COLUMN`<br>• `MK53` = Pemagangan (`7025320`)<br>• `MK54` = Tugas Akhir (`802546`) | **Sangat Kuat**: Cocok 100% dengan digit 4–5 kode resmi mata kuliah (`7025320` memuat `53`, `802546` memuat `54`). CPL Tugas Akhir (`P-26`: Karya Ilmiah) cocok dengan profil `MK54`. | **ACCEPTED**<br>(Deterministic by Code Match) |
| **HYPOTHESIS B (Rejected)** | • `MK1`..`MK48` = MK Semester 1..6<br>• `MK49` = Pemagangan<br>• `MK50` = Tugas Akhir<br>• `MK51`..`MK54` = Unresolved Extra Columns | **Lemah / Bertentangan**: Bertentangan dengan kode resmi `7025320` (digit `53`) dan `802546` (digit `54`). CPL MK50 tidak memuat CPL inti tugas akhir (`P-26`). | **REJECTED** |
| **HYPOTHESIS C (Rejected)** | • Sekuensial murni tanpa verifikasi kode. | **Tidak Valid**: Mengabaikan struktur pengkodean resmi WBI yang tertulis di dokumen. | **REJECTED** |

---

## 4. Unresolved Matrix Columns Declaration

Sesuai prinsip kepatuhan sumber (*Source-Fidelity Principle*), sistem **DILARANG** mengarang nama modul buatan. Oleh karena itu, 4 kolom matriks berikut dideklarasikan secara formal sebagai:

```json
[
  {
    "matrix_mk_number": "MK49",
    "resolved_course_identity": null,
    "status": "UNRESOLVED_MATRIX_COLUMN",
    "cpl_codes": ["S-10", "P-28", "KU-2", "KK-21"],
    "cpl_pair_count": 4,
    "source_evidence": "Section X Matrix Column MK49",
    "resolution_method": "UNRESOLVED_NO_EXPLICIT_SOURCE_CODE"
  },
  {
    "matrix_mk_number": "MK50",
    "resolved_course_identity": null,
    "status": "UNRESOLVED_MATRIX_COLUMN",
    "cpl_codes": ["S-8", "S-9", "KU-5", "KU-7", "KU-8", "KU-10"],
    "cpl_pair_count": 6,
    "source_evidence": "Section X Matrix Column MK50",
    "resolution_method": "UNRESOLVED_NO_EXPLICIT_SOURCE_CODE"
  },
  {
    "matrix_mk_number": "MK51",
    "resolved_course_identity": null,
    "status": "UNRESOLVED_MATRIX_COLUMN",
    "cpl_codes": ["S-6"],
    "cpl_pair_count": 1,
    "source_evidence": "Section X Matrix Column MK51",
    "resolution_method": "UNRESOLVED_NO_EXPLICIT_SOURCE_CODE"
  },
  {
    "matrix_mk_number": "MK52",
    "resolved_course_identity": null,
    "status": "UNRESOLVED_MATRIX_COLUMN",
    "cpl_codes": ["P-26"],
    "cpl_pair_count": 1,
    "source_evidence": "Section X Matrix Column MK52",
    "resolution_method": "UNRESOLVED_NO_EXPLICIT_SOURCE_CODE"
  }
]
```

---

## 5. Active Course Catalog Mapping & Crosswalk Fidelity

Seluruh **50 mata kuliah aktif** pada katalog kurikulum final terpetakan secara deterministik:

- **48 Mata Kuliah (Semester 1–6)**: Dipetakan 1-to-1 ke kolom `MK1` s.d. `MK48` (`crosswalk_method: CODE_MATCH`).
- **Mata Kuliah ke-49 (Semester 7)**: `7025320` — **Pemagangan** (20 SKS) $\rightarrow$ Dipetakan ke `MK53` (`crosswalk_method: CODE_MATCH`, 6 CPL: `S-8, S-9, KU-3, KU-4, KU-9, KU-11`).
- **Mata Kuliah ke-50 (Semester 8)**: `802546` — **Tugas Akhir** (6 SKS) $\rightarrow$ Dipetakan ke `MK54` (`crosswalk_method: CODE_MATCH`, 9 CPL: `P-26, KU-3, KU-4, KU-5, KU-7, KU-8, KU-9, KU-10, KU-11`).

### Rekapitulasi Metrik Relasi CPL-MK:
- **Total Relasi 'V' pada Matriks PDF**: **194 relasi**
- **Relasi pada 50 Mata Kuliah Aktif**: **182 relasi**
- **Relasi pada Kolom Unresolved (`MK49..MK52`)**: **12 relasi** (4 + 6 + 1 + 1)
- **Active Mapped Courses**: **50 / 50 MK (100.0%)**
- **Active Unmapped Courses**: **0 MK (0.0%)**
- **Unresolved Matrix Column Count**: **4 kolom** (`MK49, MK50, MK51, MK52`)

---

## 6. Final Verdict

```
============================================================
FINAL VERDICT: AP_MATRIX_VALIDATED
============================================================
- Source evidence strictly verified against WBI course coding schema.
- Hypothesis A deterministically validated via embedded code digits (01..48, 53, 54).
- All semantic guesses/invented module names for MK49..MK52 removed.
- MK49..MK52 explicitly cataloged as UNRESOLVED_MATRIX_COLUMN.
- 50 active courses (148 SKS) mapped with 182 deterministic CPL-MK pairs.
- Zero ambiguity on active course curriculum mapping.
============================================================
```
