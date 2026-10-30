# RPS PHASE 1.7 — MPI INTERNSHIP MATRIX AGGREGATE AUDIT & SEMANTIC RECONCILIATION

**Program Studi**: D4 Manajemen Pemasaran Internasional (MPI)  
**Dokumen Kurikulum**: `Buku Kurikulum prodi MPI 2022 - MBKM .pdf` (`doc_Manajemen_Pemasaran_Internasional_1789144000.pdf`)  
**Tahun Kurikulum**: 2022  
**Source SHA256**: `8c012ba9f17bb56883a9fbbb62a7699d5fab56f9b92eac407f52861d52539972`  
**Status**: **`MPI_SOURCE_FIDELITY_VALIDATED`**  

---

## 1. Latar Belakang & Critical Source Audit

Berdasarkan audit mendalam terhadap **Section 10 (Matriks Capaian Pembelajaran Lulusan Dengan Mata Kuliah, Halaman 17–18)** dan **Section 11 (Distribusi Mata Kuliah Semesteran, Halaman 24–25)** dokumen kurikulum resmi MPI 2022:

1. **Kolom Matriks ke-21 ("Internship")**:
   - Pada Matriks CPL-MK resmi, kolom ke-21 hanya bertuliskan header **`Internship`** (tanpa kode spesifik `MPI75301` maupun judul parsial *Komunikasi Pemasaran Terpadu*).
   - Kolom ini memiliki **41 tanda centang resmi (checkmarks)** terhadap CPL Prodi (S, KU, P, KK).
   
2. **Distribusi Semester 7 ("Magang (Internship)")**:
   - Pada Section 11 Semester 7, program magang MBKM dijabarkan ke dalam **7 modul mata kuliah** dengan akumulasi bobot tepat **20 SKS Praktik**:
     1. `MPI75301` Komunikasi Pemasaran Terpadu (Internship) — 3 SKS
     2. `MPI75302` Manajemen Hubungan Pelanggan (Internship) — 3 SKS
     3. `MPI75203` Ekspor - Impor (Internship) — 2 SKS
     4. `MPI75304` Strategi Pemasaran (Internship) — 3 SKS
     5. `MPI75305` Penelitian pemasaran (Internship) — 3 SKS
     6. `MPI75306` Intrapreneurship (Internship) — 3 SKS
     7. `MPI75307` Intelijen Pemasaran (Internship) — 3 SKS

3. **Temuan Semantik**:
   - Memetakan kolom matriks `Internship` secara skalar 1-ke-1 hanya ke `MPI75301` adalah **keliru** dan tidak didukung bukti sumber (*unproven 1:1 scalar binding*).
   - Secara struktural, kolom `Internship` merupakan representasi **Aggregate Track** (jalur paket utuh Magang Industri Semester 7 berbobot 20 SKS) yang memayungi ketujuh sub-modul magang (`MPI75301` s.d `MPI75307`).

---

## 2. Model Normalisasi Aggregate Track (`TRACK_AGGREGATE`)

Sistem menerapkan model domain agregat yang setara dengan penanganan `TATA_NILAI` pada kurikulum Politeknik WBI:

```json
{
  "matrix_column": "Internship",
  "matrix_column_index": 21,
  "mapping_scope": "TRACK_AGGREGATE",
  "aggregate_code": "INTERNSHIP_TRACK",
  "aggregate_domain": "INTERNSHIP_TRACK",
  "member_courses": [
    "MPI75301",
    "MPI75302",
    "MPI75203",
    "MPI75304",
    "MPI75305",
    "MPI75306",
    "MPI75307"
  ],
  "member_course_count": 7,
  "total_sks": 20,
  "mapping_type": "EXPLICIT_MATRIX",
  "evidence": "Kolom Internship pada matriks kurikulum resmi merepresentasikan paket aggregate Magang Industri Semester 7 (20 SKS)"
}
```

---

## 3. Status Pemetaan Tingkat Mata Kuliah (Course-Level Status)

Untuk mencegah kontaminasi data (*CPL pollution*) pada RPS sub-modul magang individu:

1. **Status Status Pemetaan**:
   - Ketujuh mata kuliah magang (`MPI75301` s.d `MPI75307`) **TIDAK** diklasifikasikan sebagai `ACTIVE_COURSE_OUTSIDE_CPL_MATRIX`.
   - Ketujuh mata kuliah tersebut kini berstatus resmi:  
     **`COVERED_BY_AGGREGATE_MATRIX_TRACK`**
   - Bukti pemetaan (*mapping evidence*):  
     `"Covered by official matrix aggregate column: Internship"`

2. **Aturan Penurunan CPL (No Blind Inheritance)**:
   - Ke-41 CPL yang terhubung pada kolom payung `Internship` **tidak dipaksakan/diduplikasi secara buta** ke masing-masing mata kuliah individual `MPI75301`..`MPI75307`.
   - `direct_cpl_codes` dan `effective_cpl_codes` pada level mata kuliah individual dibiarkan kosong (`[]`) dengan anotasi aggregate, sehingga dosen pengampu / koordinator magang dapat menentukan CPL spesifik sesuai capaian teknis masing-masing modul saat menyusun RPS di Studio.

---

## 4. Rekapitulasi Metrik Matriks (Matrix & Set Metrics)

| Parameter Metrik | Nilai Otoritatif | Penjelasan |
|---|:---:|---|
| **`matrix_column_count`** | **49** | Total kolom pada Matriks Capaian Pembelajaran CPL-MK |
| **`scalar_course_matrix_columns`** | **48** | Kolom matriks yang memetakan langsung mata kuliah skalar individual |
| **`aggregate_track_columns`** | **1** | Kolom matriks yang memetakan jalur payung (`Internship`) |
| **`direct_scalar_matrix_course_count`** | **48** | Jumlah mata kuliah kurikulum yang memiliki kolom skalar tersendiri |
| **`aggregate_track_covered_courses_count`** | **7** | Jumlah mata kuliah magang yang dipayungi oleh kolom `Internship` |
| **`true_active_courses_outside_matrix_count`** | **14** | Jumlah mata kuliah aktif yang benar-benar di luar matriks (14 MK Pilihan Sem 6) |
| **`course_offering_pool_count`** | **69** | Total baris penawaran mata kuliah di Section 11 |
| **`curriculum_required_sks`** | **147** | Total beban SKS wajib tempuh kelulusan mahasiswa |
| **`course_pool_sks`** | **183** | Total bobot SKS seluruh mata kuliah penawaran |
| **`direct_cpl_mk_pair_count`** | **598** | Total tanda centang CPL-MK resmi yang diekstrak |

---

## 5. Rekonsiliasi Himpunan Otoritatif (Set Theory Proof)

$$\begin{aligned}
A_{\text{scalar}} &= \text{Mata kuliah skalar langsung pada matriks} = 48\text{ MK} \\
A_{\text{aggregate}} &= \text{Mata kuliah magang terpayungi aggregate track} = 7\text{ MK} \quad (\text{MPI75301 s.d MPI75307}) \\
A_{\text{outside}} &= \text{Mata kuliah pilihan konsentrasi di luar matriks} = 14\text{ MK} \quad (\text{MPI64301 s.d MPI64314}) \\
\\
|A_{\text{total}}| &= |A_{\text{scalar}}| + |A_{\text{aggregate}}| + |A_{\text{outside}}| = 48 + 7 + 14 = \mathbf{69\text{ MK}}
\end{aligned}$$

$$\begin{aligned}
B_{\text{columns}} &= \text{Total kolom matriks Section 10} = \mathbf{49\text{ Kolom}} \quad (48\text{ Skalar} + 1\text{ Track Aggregate}) \\
B_{\text{unmapped}} &= \mathbf{0\text{ Kolom}} \quad (\text{100\% kolom matriks terhubung valid})
\end{aligned}$$

---

## 6. Integrasi UI & Studio RPS (Studio CPL Semantics)

1. **Kurikulum List (Tabel Detail Mata Kuliah)**:
   - Mata Kuliah Skalar (48 MK): Menampilkan badge CPL eksplisit dan badge `Langsung`.
   - Modul Magang Semester 7 (7 MK): Menampilkan pesan status:
     `<span style="color:#0369a1; font-size:11px; font-weight:600;"><i class="fa fa-cubes"></i> Terhubung melalui Matriks Internship (aggregate)</span>`
     serta badge `Aggregate Track` dan `Internship Track`.
   - MK Pilihan Konsentrasi (14 MK): Menampilkan badge `Luar Matriks`.

2. **Studio RPS Generator**:
   - Untuk mata kuliah skalar: Sistem melakukan *pre-select* otomatis terhadap CPL langsung dari matriks.
   - Untuk modul magang Semester 7: Sistem menyajikan daftar referensi 41 CPL dari domain `Internship Track` sebagai panduan, tanpa memaksakan centang otomatis secara membabi-buta, memberikan fleksibilitas penuh bagi dosen penyusun RPS.

---

## Final Verdict

# **`MPI_SOURCE_FIDELITY_VALIDATED`**
