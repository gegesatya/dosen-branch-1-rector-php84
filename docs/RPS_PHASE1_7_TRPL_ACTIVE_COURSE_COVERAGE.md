# PHASE 1.7 — TRPL ACTIVE COURSE COVERAGE SEMANTIC FIX REPORT

**Program Studi**: Teknologi Rekayasa Perangkat Lunak (TRPL)  
**Dokumen Kurikulum**: Kurikulum & CPL Program Studi Teknologi Rekayasa Perangkat Lunak (D4)  
**Metrik Standar**: 64 CPL | 11 BK | 57 MK Aktif | 149 SKS | 52 Kolom Matriks | 162 Pasangan CPL-MK  
**Status Audit**: `TRPL_SOURCE_MATRIX_VALIDATED`

---

## 1. Executive Summary & Semantic Separation

Tujuan utama dari Phase 1.7 ini adalah **memisahkan secara tegas dan semantik** antara:
1. **Status Kelengkapan Parsing Matriks Sumber (`matrix_parse_status = SOURCE_MAPPING_PARSED`)**:  
   Menandakan bahwa Matriks 10.2 resmi dari dokumen kurikulum (seluruh 52 kolom MK1..MK52 dan 64 baris CPL) telah berhasil diekstrak dan dipetakan secara deterministik 100% tanpa kegagalan teknis parser.
2. **Status Cakupan Pemetaan Mata Kuliah Aktif (`active_course_mapping_coverage_status = PARTIAL_BY_SOURCE_DESIGN`)**:  
   Menandakan bahwa berdasarkan desain kurikulum aslinya, matriks resmi mencakup 52 mata kuliah, di mana terdapat 4 mata kuliah bernilai 0 CPL pada dokumen asli dan 5 mata kuliah aktif di kurikulum yang tidak tercantum pada matriks CPL-MK resmi.

---

## 2. Coverage Metrics & Arithmetic Validation

```
active_course_count (57) = matrix_courses_with_cpl (48) 
                         + matrix_courses_without_cpl (4) 
                         + active_courses_outside_matrix (5)

Direct CPL-MK Pairs: 162 (100% diekstrak dari Matriks 10.2)
Total SKS: 149 SKS
```

### Rekapitulasi Metrik:
| Metrik | Nilai | Definisi & Keterangan |
| :--- | :---: | :--- |
| `official_cpl_count` | **64** | CPL Resmi TRPL (Sikap 10, Pengetahuan 26, Keterampilan Umum 10, Keterampilan Khusus 18) |
| `bahan_kajian_count` | **11** | Bahan Kajian Resmi TRPL (BK01 s.d BK11) |
| `active_course_count` | **57** | Total Mata Kuliah Aktif Semester 1 s.d 8 pada Section 10.4 |
| `total_sks` | **149** | Total Bobot SKS Kurikulum TRPL (149 SKS) |
| `matrix_course_count` | **52** | Jumlah Kolom MK pada Matriks 10.2 (MK1..MK52) |
| `direct_cpl_mk_pair_count` | **162** | Jumlah Tanda Relasi "v" Valid pada Matriks 10.2 |
| `matrix_courses_with_cpl` | **48** | Mata Kuliah pada Matriks yang Memiliki Relasi CPL Resmi (Direct Mapped) |
| `matrix_courses_without_cpl` | **4** | Mata Kuliah pada Matriks yang Bernilai 0 CPL pada Dokumen Resmi |
| `active_courses_outside_matrix` | **5** | Mata Kuliah Aktif Kurikulum yang Tidak Tercantum pada Matriks CPL-MK |
| `active_course_direct_mapping_coverage_pct` | **84.21%** | Rasio Pemetaan Langsung CPL Matriks terhadap MK Aktif (48 / 57) |

---

## 3. Rincian Mata Kuliah dengan Status Khusus

### A. Mata Kuliah Dalam Matriks Tanpa CPL (`IN_MATRIX_NO_CPL_MARKED`)
*Status UI Wording*: **"Ada pada matriks resmi, namun tidak diberi CPL pada dokumen"**

| No | Nomor Matriks | Nama Mata Kuliah | Semester | SKS | Keterangan Dokumen Sumber |
| :---: | :---: | :--- | :---: | :---: | :--- |
| 1 | **MK01** | Matematika Diskrit | 1 | 2 | Tercantum pada kolom MK1 matriks resmi, baris CPL kosong (0 tanda v) |
| 2 | **MK05** | Matematika Lanjut | 2 | 2 | Tercantum pada kolom MK5 matriks resmi, baris CPL kosong (0 tanda v) |
| 3 | **MK40** | Seni dan Olahraga | 2 | 2 | Tercantum pada kolom MK40 matriks resmi, baris CPL kosong (0 tanda v) |
| 4 | **MK50** | Berpikir Kritis | 1 | 2 | Tercantum pada kolom MK50 matriks resmi, baris CPL kosong (0 tanda v) |

> **Prinsip Tata Kelola**: Sistem **TIDAK** menganggap kondisi ini sebagai *parser error*, **TIDAK** mengisi pemetaan buatan AI sebagai pemetaan resmi, dan menyajikan status faktual apa adanya.

---

### B. Mata Kuliah Aktif di Luar Matriks CPL-MK (`ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`)
*Status UI Wording*: **"Mata kuliah aktif — tidak tercantum pada Matriks CPL-MK resmi"**

| No | Nama Mata Kuliah | Semester | SKS Teori | SKS Praktik | Total SKS | Posisi Dokumen Sumber |
| :---: | :--- | :---: | :---: | :---: | :---: | :--- |
| 1 | **Pancasila** | 3 | 2 | 0 | 2 SKS | Section 10.4 Distribusi MK Semester 3 |
| 2 | **Kerja Praktek** | 7 | 0 | 8 | 8 SKS | Section 10.4 Distribusi MK Semester 7 |
| 3 | **Seminar** | 7 | 0 | 2 | 2 SKS | Section 10.4 Distribusi MK Semester 7 |
| 4 | **Proyek Pengembangan Perangkat Lunak** | 7 | 0 | 6 | 6 SKS | Section 10.4 Distribusi MK Semester 7 |
| 5 | **Tugas Akhir** | 8 | 0 | 6 | 6 SKS | Section 10.4 Distribusi MK Semester 8 |

---

## 4. Perilaku RPS Studio (CPL Studio Behavior)

1. **Untuk 48 Mata Kuliah Direct-Mapped (`EXPLICIT_MATRIX`)**:
   - CPL resmi yang terpetakan otomatis terpilih (*preselected*) berdasarkan relasi matriks resmi kurikulum.
   - Sumber provenance tercatat sebagai `EXPLICIT_MATRIX`.
2. **Untuk 4 Mata Kuliah `IN_MATRIX_NO_CPL_MARKED`**:
   - Seluruh 64 CPL prodi resmi ditampilkan secara lengkap.
   - Tidak ada CPL resmi yang terpilih otomatis (*no preselection*).
   - Dosen pengampu diizinkan memilih CPL secara manual sesuai arahan tim teaching / KBK.
   - AI Advisory di RPS studio hanya bekerja sebagai pendamping rekomendasi jika diminta eksplisit oleh dosen.
3. **Untuk 5 Mata Kuliah `ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`**:
   - Menampilkan peringatan informatif bahwa MK aktif ini berada di luar matriks resmi kurikulum.
   - Dosen bebas memetakan CPL secara mandiri dengan provenance tercatat sebagai `MANUAL` / `STUDIO_SELECTED`.

---

## 5. Sinkronisasi Lintas Prodi (Cross-Prodi Regression Sync)

Hasil audit baseline siakad_pustaka_kurikulum pasca-pembaruan:

```
====================================================================================================================================================
ID   | Nama Prodi                     | CPL  | BK  | MK   | SKS  | Matrix               | Pairs (Dir)  | Coverage Status           | Parse Status            
====================================================================================================================================================
6    | Teknologi Rekayasa Perangkat L | 64   | 11  | 57   | 149  | 52 (0c:4, out:5)     | 162 (dir:48) | PARTIAL_BY_SOURCE_DESIGN  | SOURCE_MAPPING_PARSED 
8    | Agribisnis Hortikultura        | 48   | 20  | 66   | 150  | 66                   | 271 (dir:66) | FULL                      | SOURCE_MAPPING_PARSED 
9    | Pengelolaan Konvensi dan Acara | 33   | 32  | 49   | 138  | 49                   | 8 (dir:1)    | FULL                      | SOURCE_MAPPING_PARTIAL
10   | Manajemen Pemasaran Internasio | 50   | 10  | 63   | 164  | 63                   | 1 (dir:1)    | FULL                      | SOURCE_MAPPING_PARTIAL
11   | Akuntansi Perpajakan           | 73   | 13  | 50   | 148  | 50                   | 182 (dir:50) | FULL                      | SOURCE_MAPPING_PARSED 
====================================================================================================================================================
```

---

## 6. Final Verdict

**Verdict**: `TRPL_SOURCE_MATRIX_VALIDATED`  
- Seluruh 57 mata kuliah memiliki status semantik yang akurat.
- Matriks CPL-MK 162 pasang terverifikasi 100% sumber kurikulum.
- Integritas sistem tata kelola dan trust guard RPS OBE 2026 terpenuhi secara utuh.
