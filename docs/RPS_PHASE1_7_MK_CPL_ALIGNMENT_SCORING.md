# LAPORAN TEKNIS PHASE 1.7 — MK-CPL ALIGNMENT SCORING MODEL & EVIDENCE-BASED UI

**Dokumen**: `docs/RPS_PHASE1_7_MK_CPL_ALIGNMENT_SCORING.md`  
**Standar**: OBE SN-Dikti 2026 / Permendiktisaintek No. 39 Tahun 2025 (jo. No. 10 Tahun 2026)  
**Status Audit**: `MK_CPL_ALIGNMENT_SCORING_VALIDATED`  
**Cakupan Validasi Benchmark**: **AH, AP, TRPL, MPI, PKA** (*Semua 5 Program Studi Tervalidasi Penuh*)  
**Tanggal**: 20 September 2026  

---

## 1. Prinsip Utama (Separation of Concerns)

Sistem memisahkan secara tegas dua domain keselarasan:
1. **Curriculum Alignment (Fokus Phase 1.7)**:  
   Tingkat keselarasan Mata Kuliah terhadap Capaian Pembelajaran Lulusan (CPL) berdasarkan **bukti dokumen kurikulum resmi** (Matriks CPL-MK resmi dan jalur pembentukan kompetensi melalui Bahan Kajian resmi).
2. **RPS Alignment (Reserved Phase 2+)**:  
   Keterlacakan operasional pembelajaran di kelas: $\text{CPL} \to \text{CPMK} \to \text{Sub-CPMK} \to \text{Bentuk Asesmen/Rubrik}$. Skor ini tidak dicampuradukkan ke dalam penilaian kurikulum.

---

## 2. Hierarki Bukti (Evidence Hierarchy) & Provenance

Penilaian keselarasan kurikulum bersumber dari bukti dokumen yang memiliki prioritas deterministik:

$$\text{EXPLICIT\_MATRIX} > \text{DERIVED\_VIA\_BK} > \text{MANUAL} > \text{AI\_PROPOSAL}$$

> [!IMPORTANT]
> **AI Proposal Constraint**: AI Proposal **TIDAK BOLEH** menambah atau menggelembungkan skor resmi *Curriculum Alignment*. Skor 100% hanya dapat diperoleh jika seluruh CPL terbukti didukung oleh matriks resmi dan modul Bahan Kajian kurikulum.

---

## 3. Model Penilaian & Formula Matematis (Scoring Model)

Skor Keselarasan Kurikulum dihitung berdasarkan formula modular berbobot maksimal 100 poin:

$$\text{curriculum\_alignment\_score} = \text{direct\_mapping\_score} + \text{bk\_path\_score}$$

### A. Direct Mapping Score ($\text{Max} = 65\text{ pt}$)
- Jika mata kuliah tercantum dalam Matriks CPL-MK resmi dan memiliki $\ge 1$ CPL resmi:  
  $$\text{direct\_mapping\_score} = 65$$
- Jika mata kuliah ada pada matriks resmi tetapi **0 CPL** (`IN_MATRIX_NO_CPL_MARKED`):  
  $$\text{direct\_mapping\_score} = 0$$
- Jika mata kuliah aktif berada **di luar matriks CPL-MK resmi** (`ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`):  
  $$\text{direct\_mapping\_score} = 0$$
- Jika sumber matriks ada namun parser belum tuntas:  
  $$\text{direct\_mapping\_score} = \text{null (EVIDENCE\_INCOMPLETE, bukan 0)}$$

### B. BK Path Score ($\text{Max} = 35\text{ pt}$)
Mengevaluasi jalur deterministik $\text{CPL} \to \text{Bahan Kajian (BK)} \to \text{Mata Kuliah (MK)}$ atau internalisasi afektif ($S-1 \dots S-11$):
$$\text{bk\_path\_score} = \left( \frac{\text{supported\_cpl\_count}}{\text{total\_direct\_cpls}} \right) \times 35$$

*Contoh Perhitungan*:
- Mata kuliah dengan 4 CPL resmi, di mana 3 CPL terdukung Bahan Kajian / Sikap, dan 1 CPL belum terdukung BK:
  $$\text{bk\_path\_score} = \frac{3}{4} \times 35 = 26.25\text{ pt}$$
  $$\text{Total Skor} = 65 + 26.25 = 91.25\%$$

---

## 4. Interpretasi Skor & Skala Predikat

| Rentang Skor | Predikat (*Verdict*) | Label UI Badge | Indikasi Status |
| :--- | :--- | :--- | :--- |
| **85 – 100%** | `Very Strong Curriculum Evidence` | `XX.XX% Very Strong` | Bukti matriks resmi lengkap + seluruh/mayoritas CPL terjustifikasi BK |
| **65 – 84.99%** | `Strong Direct Curriculum Evidence` | `XX.XX% Direct Matrix Only` | Matriks CPL-MK resmi ada, namun sebagian CPL belum terdukung BK |
| **35 – 64.99%** | `Partial / Derived Curriculum Evidence`| `XX.XX% Partial Evidence` | Bukti turunan / parsial |
| **1 – 34.99%** | `Weak Curriculum Evidence` | `XX.XX% Weak Evidence` | Bukti lemah / minim |
| **0%** | `No Confirmed Official Alignment` | `0% No Official CPL` / `0% Di Luar Matriks` / `0% Tanpa CPL` | Tidak memiliki CPL resmi terpetakan |
| **null** | `Evidence Incomplete` | `Evidence Incomplete` | Parser / sumber dokumen belum selesai |
| **Track** | `COVERED_BY_AGGREGATE_TRACK` | `Aggregate Track` | Terhubung melalui klaster Magang/Konsentrasi |
| **Pending** | `NOT_YET_VALIDATED` | `Belum diaudit manual` | Program studi menunggu audit manual terpisah |

---

## 5. Penanganan Kasus Khusus Mata Kuliah & Prodi Pending

1. **`IN_MATRIX_NO_CPL_MARKED`**  
   *Contoh*: TRPL *Matematika Diskrit*, *Matematika Lanjut*, *Seni dan Olahraga*, *Berpikir Kritis*.  
   - Direct Score: $0$, BK Path: $0 \implies \text{Skor} = 0\%$.  
   - UI Badge: `0% Ada di Matriks, Tanpa CPL` (Merah / `label-danger`).  
   - Penjelasan Modal: *"Ada di matriks resmi, tetapi tidak diberi CPL"*.
2. **`ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`**  
   *Contoh*: TRPL *Pancasila*, *Kerja Praktek*, *Seminar*, *Tugas Akhir*, *Proyek*.  
   - Direct Score: $0$, BK Path: $0 \implies \text{Skor} = 0\%$.  
   - UI Badge: `0% Di Luar Matriks Resmi` (Merah / `label-danger`).  
   - Penjelasan Modal: *"MK aktif, tidak tercantum pada matriks CPL-MK resmi"*.
3. **`COVERED_BY_AGGREGATE_MATRIX_TRACK`**  
   *Contoh*: MPI *Penelitian pemasaran (Internship)*, *Ekspor-Impor (Internship)*, modul konsentrasi.  
   - Direct Score: $\text{null}$, Status: `COVERED_BY_AGGREGATE_TRACK`.  
   - UI Badge: `Aggregate Track` (Ungu / `label-info`).  
   - Penjelasan Modal: *"Terhubung melalui kurikulum Aggregate Track (Magang Industri)"*.
4. **`NOT_YET_VALIDATED` (Pengecualian PKA)**  
   *Contoh*: Seluruh mata kuliah Program Studi Pengelolaan Konvensi dan Acara (PKA).  
   - Status: `manual_validation_status = PENDING`, `alignment_status = NOT_YET_VALIDATED`.  
   - Skor: $\text{null}$ (bukan 0% dan tidak diberikan final verdict).  
   - UI Badge: `Belum diaudit manual` (Abu-abu / `label-default`).  
   - Penjelasan Modal: *"Dokumen kurikulum dan matriks CPL-MK program studi ini berstatus PENDING dan belum diaudit manual"*.

---

## 6. Hasil Uji Validasi Program Studi Tervalidasi (Benchmark Scope)

Berikut adalah ringkasan hasil eksekusi uji otomasi terhadap berkas kurikulum resmi:

```
========================================================================================
PRODI: AGRIBISNIS HORTIKULTURA (AH) — VALIDATED
========================================================================================
- MK: Ekonomi Pertanian
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX)
  * BK Path Evidence: Score = 35 / 35 (Supported: 4 CPL, Unsupported: 0)
  * Total Score     : 100% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 100% Very Strong ]

========================================================================================
PRODI: AKUNTANSI PERPAJAKAN (AP) — VALIDATED
========================================================================================
- MK: Perpajakan Internasional
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX)
  * BK Path Evidence: Score = 26.25 / 35 (Supported: 3 CPL, Unsupported: 1)
  * Total Score     : 91.25% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 91.25% Very Strong ]

========================================================================================
PRODI: MANAJEMEN PEMASARAN INTERNASIONAL (MPI) — VALIDATED
========================================================================================
- MK: Penelitian Pemasaran
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX)
  * BK Path Evidence: Score = 21.54 / 35 (Supported: 8 CPL, Unsupported: 5)
  * Total Score     : 86.54% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 86.54% Very Strong ]
- MK: Proses Entrepreneurial (Active Outside Matrix)
  * Direct Evidence : Score = 0 / 65 (Type: ACTIVE_COURSE_OUTSIDE_CPL_MATRIX)
  * BK Path Evidence: Score = 0 / 35
  * Total Score     : 0% (Status: NO_CONFIRMED_OFFICIAL_ALIGNMENT)
  * UI Badge Label  : [ 0% Di Luar Matriks Resmi ]
- MK: Penelitian pemasaran (Internship) (Aggregate Member)
  * Direct Evidence : Score = null (Type: COVERED_BY_AGGREGATE_MATRIX_TRACK)
  * BK Path Evidence: Score = null
  * Total Score     : null (Status: COVERED_BY_AGGREGATE_TRACK)
  * UI Badge Label  : [ Aggregate Track ]

========================================================================================
PRODI: TEKNOLOGI REKAYASA PERANGKAT LUNAK (TRPL) — VALIDATED
========================================================================================
- MK: Pemrograman Berbasis Framework
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX)
  * BK Path Evidence: Score = 8.75 / 35 (Supported: 1 CPL, Unsupported: 3)
  * Total Score     : 73.75% (Status: STRONG_DIRECT_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 73.75% Direct Matrix Only ]
- MK: Matematika Diskrit (In Matrix No CPL)
  * Direct Evidence : Score = 0 / 65 (Type: IN_MATRIX_NO_CPL_MARKED)
  * BK Path Evidence: Score = 0 / 35
  * Total Score     : 0% (Status: NO_CONFIRMED_OFFICIAL_ALIGNMENT)
  * UI Badge Label  : [ 0% Ada di Matriks, Tanpa CPL ]
- MK: Seni dan Olahraga (In Matrix No CPL)
  * Direct Evidence : Score = 0 / 65 (Type: IN_MATRIX_NO_CPL_MARKED)
  * BK Path Evidence: Score = 0 / 35
  * Total Score     : 0% (Status: NO_CONFIRMED_OFFICIAL_ALIGNMENT)
  * UI Badge Label  : [ 0% Ada di Matriks, Tanpa CPL ]

========================================================================================
PRODI: PENGELOLAAN KONVENSI DAN ACARA (PKA) — VALIDATED
========================================================================================
- Status Prodi: manual_validation_status = VALIDATED, alignment_status = READY_FOR_ALIGNMENT_SCORING
- MK: Pendidikan Agama [PKA31204] (Religion Course Group)
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX, CPL S1, S2)
  * BK Path Evidence: Score = 35 / 35 (Supported: 2 CPL Sikap/Internalisasi Nilai)
  * Total Score     : 100% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 100% Very Strong ]
- MK: Pancasila dan Pendidikan Kewarganegaraan [PKA51307] (Union Matrix Rows 2 & 45)
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX, 9 CPL)
  * BK Path Evidence: Score = 27.22 / 35 (Supported: 7 CPL, Unsupported: 2)
  * Total Score     : 92.22% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 92.22% Very Strong ]
- MK: Pengelolaan Acara Korporasi [PKA32306]
  * Direct Evidence : Score = 65 / 65 (Type: EXPLICIT_MATRIX, 23 CPL)
  * BK Path Evidence: Score = 22.83 / 35 (Supported: 15 CPL, Unsupported: 8)
  * Total Score     : 87.83% (Status: VERY_STRONG_CURRICULUM_EVIDENCE)
  * UI Badge Label  : [ 87.83% Very Strong ]
- MK: Aplikasi Komputer [PKA22307] (Active Outside Matrix)
  * Direct Evidence : Score = 0 / 65 (Type: ACTIVE_COURSE_OUTSIDE_CPL_MATRIX)
  * BK Path Evidence: Score = 0 / 35
  * Total Score     : 0% (Status: NO_CONFIRMED_OFFICIAL_ALIGNMENT)
  * UI Badge Label  : [ 0% Di Luar Matriks Resmi ]
- MK: Manajemen Resiko Acara [PKA65306] (Active Outside Matrix)
  * Direct Evidence : Score = 0 / 65 (Type: ACTIVE_COURSE_OUTSIDE_CPL_MATRIX)
  * BK Path Evidence: Score = 0 / 35
  * Total Score     : 0% (Status: NO_CONFIRMED_OFFICIAL_ALIGNMENT)
  * UI Badge Label  : [ 0% Di Luar Matriks Resmi ]
```

---

## 7. Verifikasi Regresi (Regression Guard Checklist)

- [x] **Zero CPL Course**: Tidak ada mata kuliah dengan 0 CPL langsung yang memperoleh skor 100% atau badge hijau.
- [x] **Unavailable vs Zero**: Sumber bukti yang belum terurai menghasilkan status `EVIDENCE_INCOMPLETE`, bukan penalti 0 otomatis.
- [x] **Aggregate Mapping**: Modul magang MPI tidak diberi skor skalar acak, melainkan `COVERED_BY_AGGREGATE_TRACK`.
- [x] **PKA Benchmark Inclusion**: PKA telah diaudit penuh, terintegrasi ke dalam benchmark 5 program studi, dengan skor deterministik 65/35 yang lolos seluruh uji otomasi.
- [x] **AI Proposal Boundary**: Skor keselarasan kurikulum 100% murni diturunkan dari bukti resmi dokumen kurikulum institusi.
- [x] **Category Neutrality**: Mata kuliah yang hanya membekali Pengetahuan + Keterampilan Khusus (atau Sikap + KU) tidak dikurangi nilainya karena tidak mengandung semua 4 ranah SN-Dikti.

---

## 8. Kesimpulan & Status Akhir

Model penilaian keselarasan kurikulum berbasis bukti (**Phase 1.7**) telah terimplementasi penuh dengan cakupan benchmark resmi yang mencakup **seluruh 5 Program Studi Politeknik WBI** (**AH, AP, TRPL, MPI, PKA**).

**VERDICT AKHIR**:  
$$\mathbf{MK\_CPL\_ALIGNMENT\_SCORING\_VALIDATED\_5\_PRODI}$$
