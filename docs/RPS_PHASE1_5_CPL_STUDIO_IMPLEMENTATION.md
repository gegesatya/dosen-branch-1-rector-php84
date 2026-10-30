# Dokumentasi Implementasi Phase 1.5: Studio CPL, Deskripsi Mata Kuliah & Referensi Wajib

Dokumen ini menjelaskan arsitektur, spesifikasi teknis, aturan akademik, alur interaksi UI, skema basis data, dan hasil pengujian validasi untuk modul **Phase 1.5 — Studio CPL & Deskripsi Mata Kuliah (dengan Referensi Wajib)** pada sistem RPS Generator OBE Politeknik Wilmar Bisnis Indonesia (WBI).

---

## 1. Executive Summary & Tujuan

Phase 1.5 menambahkan satu tahapan terstruktur sebelum penyusunan CPMK (CPMK Studio), sehingga dosen dan tim kurikulum dapat:
1. **Meninjau CPL Resmi Prodi**: Mengekstraksi butir-butir CPL resmi dari dokumen kurikulum prodi (Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus) tanpa halusinasi kode atau teks.
2. **Menetapkan CPL yang Dibebankan pada MK**: Memilih butir CPL spesifik yang dibebankan pada mata kuliah dan menetapkan peranannya (*PRIMARY* / CPL Utama vs *SUPPORTING* / CPL Pendukung).
3. **Melihat Provenance Sumber Mapping**: Memperlihatkan asal-usul pemetaan (`OFFICIAL_CURRICULUM`, `AI_PROPOSAL_MAPPING`, `MANUAL`).
4. **Menyusun Deskripsi Mata Kuliah Terpadu**: Mengambil narasi dari kurikulum resmi, menyusun draf narasi terarah menggunakan AI berbasis bahan kajian, atau menginput manual.
5. **Mengisi Referensi Perkuliahan Wajib**: Memastikan daftar pustaka/buku teks/jurnal acuan perkuliahan telah terisi sebelum persetujuan CPL-MK.
6. **Persetujuan Akademik (Approval)**: Mengunci CPL-MK, Deskripsi MK, dan Referensi dengan status `APPROVED`, yang menjadi prasyarat mutlak (*gateway*) untuk membuka **CPMK Studio**.

---

## 2. Arsitektur Tab & Visual Workflow Stepper

### 2.1 Visual Workflow Stepper
Pada bagian atas area kerja RPS Generator, terdapat stepper horizontal visual yang dinamis:
```
[1. CPL & Deskripsi MK] ➔ [2. Studio CPMK] ➔ [3. Studio Sub-CPMK] ➔ [4. RPS Lengkap] ➔ [5. Dokumen Turunan]
```
- **Active State**: Ditandai dengan warna primer gelap dan badge langkah aktif.
- **Lock State**: Ditandai dengan ikon gembok jika tahapan prasyarat belum disetujui.

### 2.2 Reorganisasi Tab Navigation
Urutan tab baru yang diterapkan:
| No | Tab ID | Label Tab | Status Kunci Default | Deskripsi |
|---|---|---|---|---|
| 1 | `tab_studio_cpl` | **Studio CPL & Deskripsi** | **ACTIVE (Default)** | Pemilihan CPL-MK, penetapan role, deskripsi MK, dan referensi wajib. |
| 2 | `tab_susun_cpmk` | **CPMK Studio** | **LOCKED** | Terkunci hingga CPL-MK berstatus `APPROVED`. Hanya menggunakan CPL-MK yang disetujui. |
| 3 | `tab_susun_subcpmk` | **Sub-CPMK** | **LOCKED (Phase 2 Placeholder)** | Terkunci untuk pengembangan Phase 2. |
| 4 | `tab_susun_rps` | **RPS Lengkap** | **LOCKED** | Terkunci hingga CPMK tersusun/disetujui. |
| 5 | `tab_kontrak_kuliah` | **Kontrak Kuliah** | Standby | Dokumen turunan SPMI WBI. |
| 6 | `tab_rancangan_tugas` | **Rancangan Tugas** | Standby | Dokumen turunan berbasis Case Method / PjBL. |
| 7 | `tab_roadmap_wbi` | **Roadmap Pembelajaran** | Standby | Dokumen turunan roadmap modul WBI. |
| 8 | `tab_skenario_pembelajaran` | **Strategi Pertemuan / BAP** | Standby | Dokumen turunan skenario pertemuan. |

---

## 3. Komponen Studio CPL & Deskripsi MK

### 3.1 Bagian Deskripsi Mata Kuliah
- **Textarea Deskripsi**: Menampung narasi ruang lingkup dan tujuan mata kuliah.
- **Badge Sumber Deskripsi**: Menampilkan asal teks (`DARI KURIKULUM`, `AI PROPOSAL`, `INPUT MANUAL`).
- **Tombol Aksi**:
  - `Ambil dari Kurikulum`: Mengisi otomatis dari section deskripsi naskah kurikulum resmi prodi jika tersedia.
  - `Susun Draft dengan AI`: Menyintesis narasi singkat berbasis CPL terpilih dan bahan kajian prodi.

### 3.2 Bagian Referensi Perkuliahan (Wajib / Mandatory)
- **Textarea Referensi (`#studio_referensi_mk`)**: Menampung daftar pustaka utama dan pendukung (buku teks, jurnal, modul, regulasi).
- **Aturan Validasi**: Persetujuan CPL-MK ditolak jika referensi kosong atau di bawah 5 karakter.

### 3.3 Bagian Daftar CPL Resmi Prodi & Pemilihan Role
- **Filter Tab List**: `Semua CPL`, `Direkomendasikan (AI/Matrix)`, `Terpilih Saja`.
- **Rekomendasi AI Terpadu**: Tombol *Minta Rekomendasi CPL dari AI* menganalisis nama MK, deskripsi, dan bahan kajian, lalu mengklasifikasikan CPL prodi yang relevan ke dalam kategori `PRIMARY` dan `SUPPORTING`.
- **Role Assignment**: Setiap CPL yang dicentang dapat diatur perannya:
  - `PRIMARY` (CPL Utama / Inti): Wajib minimal 1 butir.
  - `SUPPORTING` (CPL Pendukung).
- **Summary Bar & Approval Action**:
  - Menampilkan ringkasan: `Total CPL Terpilih`, `CPL Utama (Primary)`, `CPL Pendukung (Supporting)`.
  - Tombol `Simpan Draf CPL & Deskripsi`: Menyimpan perubahan tanpa mengubah status menjadi APPROVED.
  - Tombol `Setujui CPL, Deskripsi & Referensi`: Memvalidasi seluruh aturan akademik, mengubah status menjadi `APPROVED`, dan membuka akses ke CPMK Studio.
  - Tombol `Revisi CPL & Deskripsi`: Membuka kembali status persetujuan menjadi `DRAFT` apabila ada penyesuaian kurikulum.

---

## 4. Aturan Validasi Akademik (Zero-Hallucination Guard)

1. **Strict Official CPL Constraint**:
   - Seluruh kode CPL yang disimpan atau direkomendasikan HANYA berasal dari naskah kurikulum resmi prodi (`siakad_pustaka_kurikulum`).
   - Kode di luar CPL resmi ditolak secara otomatis oleh backend validator.
2. **Minimal 1 Primary CPL**:
   - Mata kuliah wajib memiliki sekurang-kurangnya 1 butir CPL berstatus `PRIMARY`.
3. **Mandatory Course Description**:
   - Deskripsi MK tidak boleh kosong (minimal 15 karakter).
4. **Mandatory Course References**:
   - Referensi perkuliahan wajib diisi (minimal 5 karakter).
5. **Strict CPMK Binding**:
   - AI Generator pada CPMK Studio HANYA menerima himpunan kode CPL-MK yang telah di-approve (`cpl_mk_json` berstatus `APPROVED`).
   - Mencegah CPMK mengaitkan CPL liar yang tidak dibebankan pada mata kuliah tersebut.

---

## 5. Skema Basis Data & API Endpoints

### 5.1 Skema Kolom Baru pada Tabel `siakad_rps_obe`
| Kolom | Tipe Data | Nullable | Keterangan |
|---|---|---|---|
| `cpl_mk_json` | LONGTEXT | YES | JSON array berisi CPL terpilih (`code`, `role`, `source`, `approved`). |
| `cpl_mk_status` | VARCHAR(20) | YES | Nilai: `NULL`, `DRAFT`, `APPROVED`. |
| `deskripsi_mk_source` | VARCHAR(30) | YES | Nilai: `CURRICULUM`, `AI_PROPOSAL`, `MANUAL`, `CURRICULUM_EDITED`, `AI_PROPOSAL_EDITED`. |
| `deskripsi_mata_kuliah` | TEXT / LONGTEXT | YES | Teks narasi deskripsi mata kuliah (kolom existing). |
| `referensi` | TEXT / LONGTEXT | YES | Teks daftar pustaka / referensi perkuliahan (kolom existing). |

### 5.2 Endpoint Controller `C_rps_generator.php`
- `POST /index.php/C_rps_generator/ajax_get_cpl_studio`: Memuat data lengkap CPL resmi, status, deskripsi, dan referensi.
- `POST /index.php/C_rps_generator/ajax_generate_cpl_recommendation`: Rekomendasi CPL berbasis AI строго dari kode resmi.
- `POST /index.php/C_rps_generator/ajax_generate_course_description`: Pembuatan draf deskripsi MK berbasis AI.
- `POST /index.php/C_rps_generator/ajax_save_cpl_studio`: Menyimpan draf CPL-MK, deskripsi, dan referensi (`DRAFT`).
- `POST /index.php/C_rps_generator/ajax_approve_cpl_studio`: Memvalidasi dan menyetujui CPL-MK (`APPROVED`).
- `POST /index.php/C_rps_generator/ajax_revise_cpl_studio`: Mengembalikan status CPL-MK menjadi `DRAFT`.

---

## 6. Hasil Pengujian Multi-Prodi

Pengujian menyeluruh dijalankan pada 3 program studi dengan karakteristik kurikulum yang berbeda:

```text
=================================================================================
>>> PHASE 1.5 VERIFICATION SUITE: STUDIO CPL, DESKRIPSI MK & MANDATORY REFERENSI
=================================================================================

[STEP 1] Verifying siakad_rps_obe schema...
  ✓ Column 'cpl_mk_json' exists in siakad_rps_obe
  ✓ Column 'cpl_mk_status' exists in siakad_rps_obe
  ✓ Column 'deskripsi_mk_source' exists in siakad_rps_obe
  ✓ Column 'deskripsi_mata_kuliah' exists in siakad_rps_obe
  ✓ Column 'referensi' exists in siakad_rps_obe

[STEP 2] Testing CPL Studio Parsing across 3 Test Cases...
  ✓ Case TRPL: 64 Official CPLs parsed (S-1..S-8, P-1..P-28, KU-1..KU-13, KK-1..KK-15)
  ✓ Case AKP:  73 Official CPLs parsed (S-1..S-8, P-1..P-30, KU-1..KU-13, KK-1..KK-22)
  ✓ Case MPI:  50 Official CPLs parsed (S-1..S-8, P-1..P-18, KU-1..KU-11, KK-1..KK-13)

[STEP 3] Testing Academic Validation Rules...
  ✓ Missing PRIMARY CPL correctly rejected: PASSED
  ✓ Short/empty referensi rejected: PASSED
  ✓ Short/empty deskripsi rejected: PASSED
  ✓ Hallucinated CPL Code rejected: PASSED

[STEP 4] Testing Full Save & Approve Simulation into DB...
  ✓ Successfully saved approved CPL-MK with Referensi & Deskripsi (Status: APPROVED)

[STEP 5] Testing CPMK Generation Grounding with Approved CPL-MK...
  ✓ Zero-hallucination constraint strictly enforced: Allowed codes [P-5, KU-1]
```

---

## 7. Status Kesiapan Tahap Selanjutnya

> **Status: READY FOR PHASE 2 (SUB-CPMK STUDIO)**
> Fondasi CPL-MK, Deskripsi Mata Kuliah, dan Referensi Wajib telah terintegrasi penuh dan teruji solid. CPMK Studio kini beroperasi dengan pembatasan ketat (*zero hallucination*) terhadap CPL yang telah disetujui dosen. Sistem siap melanjutkan ke Phase 2 (Penyusunan Sub-CPMK).
