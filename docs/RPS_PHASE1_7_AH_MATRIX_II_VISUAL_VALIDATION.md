# PHASE 1.7 — AH MATRIX II VECTOR PARSER MANUAL VALIDATION AUDIT REPORT
**Dokumen Audit & Validasi Visual Vektor Matriks II Agribisnis Hortikultura**
*Tanggal Audit: 19 September 2026*

---

## 1. Executive Summary & Audit Background

Audit visual manual dilakukan untuk memverifikasi akurasi ekstraksi koordinat/vektor Matriks II (*Hubungan Mata Kuliah dan Capaian Pembelajaran*) pada dokumen resmi Program Studi **D4 Agribisnis Hortikultura (AH)** (halaman 27–30 dokumen kurikulum resmi).

### Temuan Awal yang Memicu Audit (Critical Finding):
- Parser generasi sebelumnya melaporkan bahwa **Ekonomi Pertanian** memiliki 23 butir Direct CPL (`A1-A5, B1-B7, C1-C4, D, E1-E5, F`).
- Secara akademik dan visual pada tabel matriks dokumen, baris Ekonomi Pertanian tidak memuat 23 sel terisi.
- Hasil investigasi mendalam terhadap objek grafis PDF mengungkap adanya **false positive vector detection**: parser lama memperlakukan seluruh rectangle dengan `fill != null` sebagai sel pemetaan aktif, termasuk **rectangle background putih (`fill=(1.0, 1.0, 1.0)`)** yang disisipkan oleh layout Word/PDF pada sel-sel kosong.

---

## 2. Vector Object Audit & Signature Definition

### 2.1. Karakteristik Objek Grafis PDF pada Matriks II AH (Halaman 27–30)
Setiap halaman Matriks II memuat lebih dari 2.300 objek vektor dengan rincian:
- **Grid Lines / Border Tabel**: Garis tepi horizontal & vertikal digambar sebagai rectangle tipis berdimensi `w = 0.48 pt` atau `h = 0.48 pt` berwarna hitam (`fill=(0.0, 0.0, 0.0)`).
- **Header Background**: Background abu-abu muda (`fill=(0.85098, 0.85098, 0.85098)` / `#D9D9D9`) pada baris header kolom `A1..I`.
- **Empty Cell Background**: Rectangle putih (`fill=(1.0, 1.0, 1.0)`) pada beberapa baris sel kosong.
- **Actual Filled Mapping Cell**: Rectangle berwarna **Navy Blue / Biru Gelap** (`fill=(0.12155, 0.30588, 0.47451)` atau `RGB(31, 78, 121)` / `#1F4E79`). Setiap sel terisi digambar sebagai sepasang rectangle (outer cell boundary + inner text glyph box) dengan warna navy yang identik.

### 2.2. Definisi Signature Vektor Resmi (True Cell Signature)

| Tipe Objek | Karakteristik Geometri & Warna | Klasifikasi Parser |
| :--- | :--- | :--- |
| **Actual Filled Mapping Cell** | `fill ≈ (0.12155, 0.30588, 0.47451)` (`#1F4E79`), `w ≥ 3.0 pt`, `h ≥ 3.0 pt`, `y0 ≥ 175 pt` | **POSITIVE (Accepted Mapping)** |
| **Empty Cell Background** | `fill = (1.0, 1.0, 1.0)` (Putih), `w ≥ 3.0 pt`, `h ≥ 3.0 pt` | **NEGATIVE (Rejected - Empty)** |
| **Header Shading** | `fill = (0.85098, 0.85098, 0.85098)` (Abu-abu), `y0 < 175 pt` | **NEGATIVE (Rejected - Header)** |
| **Table Grid / Border** | `w < 2.0 pt` atau `h < 2.0 pt`, `fill = (0.0, 0.0, 0.0)` (Hitam) | **NEGATIVE (Rejected - Border)** |

---

## 3. Detailed Comparison: Kasus Khusus "Ekonomi Pertanian"

### Perbandingan Sel demi Sel pada Baris Ekonomi Pertanian (No. 22 / Row 22, Page 27, y=470.4–483.8 pt):

| Kolom CPL | Bounding Box Objek di PDF | Warna Fill di PDF | Status Visual Dokumen | Parser Lama | Parser Tervalidasi (Baru) | Keterangan |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| **Tata Nilai** | `(205.4, 470.9, 236.4, 483.8)` | *Tidak ada fill* | Kosong | Kosong | Kosong | Match (True Negative) |
| **A1..A5** | `x: 236.9 - 345.8` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **B1** | `(345.8, 470.9, 366.7, 483.8)` | **`fill=(0.12155, 0.30588, 0.47451)`** | **TERISI (Navy)** | Terbaca | **Terbaca (B1)** | Match (True Positive) |
| **B2..B7** | `x: 367.2 - 495.3` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **C1** | `(495.4, 470.9, 516.5, 483.8)` | **`fill=(0.12155, 0.30588, 0.47451)`** | **TERISI (Navy)** | Terbaca | **Terbaca (C1)** | Match (True Positive) |
| **C2** | `(517.0, 470.9, 539.5, 483.8)` | **`fill=(0.12155, 0.30588, 0.47451)`** | **TERISI (Navy)** | Terbaca | **Terbaca (C2)** | Match (True Positive) |
| **C3..C4** | `x: 540.0 - 586.0` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **D** | `(586.1, 470.9, 603.8, 483.8)` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **E1** | `(604.3, 470.9, 626.2, 483.8)` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **E2** | `(626.6, 470.9, 648.7, 483.8)` | **`fill=(0.12155, 0.30588, 0.47451)`** | **TERISI (Navy)** | Terbaca | **Terbaca (E2)** | Match (True Positive) |
| **E3..E5** | `x: 649.2 - 716.6` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **F** | `(716.6, 470.9, 733.2, 483.8)` | `fill=(1.0, 1.0, 1.0)` (Putih) | Kosong | Terbaca (FP) | **Kosong** | False Positive Diperbaiki |
| **G..I** | `x: 733.6 - 786.0` | *Tidak ada fill* | Kosong | Kosong | Kosong | Match (True Negative) |

**Hasil Verifikasi Faktual Ekonomi Pertanian**:
- Manual PDF: **4 Butir CPL (`B1, C1, C2, E2`)**
- Parser Tervalidasi: **4 Butir CPL (`B1, C1, C2, E2`)**
- False Positives Dieliminasi: **19 butir** (A1, A2, A3, A4, A5, B2, B3, B4, B5, B6, B7, C3, C4, D, E1, E3, E4, E5, F).

---

## 4. Manual Golden Sample Validation (10 Mata Kuliah)

Uji validasi manual visual dilakukan pada 10 mata kuliah representatif dari Semester 1 hingga Semester 8:

| No | Mata Kuliah | Kode MK | Manual CPL dari PDF Matriks II | Hasil Parser Vektor Baru | False Positives | False Negatives | Verdict |
| :---: | :--- | :---: | :--- | :--- | :---: | :---: | :---: |
| 1 | **Bahasa Indonesia** | AGH11301 | Tata Nilai, C4, F, I | `TATA_NILAI, C4, F, I` | 0 | 0 | **PASS** |
| 2 | **Biologi** | AGH12302 | A2, A3, A4, A5 | `A2, A3, A4, A5` | 0 | 0 | **PASS** |
| 3 | **Matematika Terapan** | AGH12305 | C1, C2, C3, I | `C1, C2, C3, I` | 0 | 0 | **PASS** |
| 4 | **Pola Pikir Kewirausahaan** | AGH12206 | B3, E2, E3, E4, E5 | `B3, E2, E3, E4, E5` | 0 | 0 | **PASS** |
| 5 | **Akuntansi** | AGH22203 | C1, C2, F, G, I | `C1, C2, F, G, I` | 0 | 0 | **PASS** |
| 6 | **Ekonomi Pertanian** | AGH32203 | B1, C1, C2, E2 | `B1, C1, C2, E2` | 0 | 0 | **PASS** |
| 7 | **Manajemen Agribisnis** | AGH42204 | B1, B2, B3, C1, C3, E1, E2 | `B1, B2, B3, C1, C3, E1, E2` | 0 | 0 | **PASS** |
| 8 | **Riset Pemasaran** | AGH52208 | B1, B5, E2, E3, F, I | `B1, B5, E2, E3, F, I` | 0 | 0 | **PASS** |
| 9 | **Metodologi Penelitian** | AGH72304 | B4, I | `B4, I` | 0 | 0 | **PASS** |
| 10 | **Tugas Akhir** | AGH86601 | B5, F, I | `B5, F, I` | 0 | 0 | **PASS** |

---

## 5. Recalculation & Before/After Comparison

Setelah parser vektor diperbaiki dengan filter warna strict navy blue (`RGB(31, 78, 121)`), dilakukan kalkulasi ulang menyeluruh pada seluruh 66 mata kuliah:

| Metrik Pemetaan | Sebelum Audit (Unfiltered Vector) | Sesudah Audit (Validated Vector) | Selisih / Koreksi |
| :--- | :---: | :---: | :--- |
| **Total Direct CPL-MK Pairs** | 331 | **271** | -60 (False Positives Dieliminasi) |
| **Total Aggregate Tata Nilai Mappings** | 8 | **6** | -2 (False Positives Dieliminasi) |
| **Total Unique Filled Cells** | 339 | **277** | -62 (Eliminasi background putih) |
| **Total Mata Kuliah (Catalog)** | 66 | **66** | 0 (Pertahankan 66 MK resmi) |
| **Mapped Course Count** | 66 | **66** | 0 (Seluruh 66 MK memiliki pemetaan) |
| **Unmapped Course Count** | 0 | **0** | 0 (Tidak ada MK tanpa CPL) |

### Daftar 6 Mata Kuliah dengan Domain Aggregate TATA_NILAI:
1. `AGH11301` - Bahasa Indonesia (PDF Row 1)
2. `AGH11207` - Agama (PDF Row 7)
3. `AGH12208` - Berpikir Kritis (PDF Row 8)
4. `AGH22210` - Seni dan Olahraga (PDF Row 19)
5. `AGH42308` - Sikap Religius (PDF Row 37)
6. `AGH51307` - Pendidikan Pancasila dan Kewarganegaraan (PDF Row 45)

---

## 6. Matrix Cell Invariant & Audit Metadata

Setiap direct pair pada payload ternormalisasi [normalized_Agribisnis_Hortikultura.json](file:///c:/laragon/www/server/dosen_branch_1/application/config/kurikulum_payloads/normalized_Agribisnis_Hortikultura.json) kini menyimpan bukti sel koordinat lengkap:

```json
{
  "kode_mk": "AGH11301",
  "nama_mk": "Bahasa Indonesia",
  "cpl_code": "C4",
  "mapping_type": "EXPLICIT_MATRIX",
  "source_page": 27,
  "source_row": 1,
  "source_column": "C4",
  "bbox": [563.04, 180.0, 585.6, 195.36],
  "evidence": "Matriks CPL-MK Kurikulum"
}
```

Audit metadata pada payload diperbarui secara konsisten:
```json
"cpl_mk_mapping_audit": {
  "mapping_source_exists": true,
  "mapping_source_section": "MATRIKS II HUBUNGAN MATA KULIAH DAN CAPAIAN PEMBELAJARAN",
  "mapping_parse_status": "SOURCE_MAPPING_PARSED",
  "mapping_parse_message": "Matriks II CPL-MK resmi berhasil dibaca deterministik untuk seluruh 66 mata kuliah (271 relasi CPL-MK)."
}
```

---

## 7. Final Verdict

```
============================================================
FINAL VERDICT: MATRIX_II_VALIDATED
============================================================
- True vector signature established: Navy Blue RGB(31, 78, 121).
- All 10 golden sample courses manually verified & passed 100%.
- Ekonomi Pertanian false positives (19 CPLs) successfully eliminated.
- Total direct pairs corrected: 271 direct CPL + 6 Tata Nilai (277 total).
- 66/66 courses mapped with exact bounding box audit trail.
- Codebase assertions & PHP syntax checks passed without error.
============================================================
```
