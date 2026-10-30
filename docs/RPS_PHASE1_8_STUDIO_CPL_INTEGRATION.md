# DOKUMENTASI IMPLEMENTASI PHASE 1.8 — STUDIO CPL INTEGRATION & ROLE COMPLETENESS PATCH

## 1. Executive Summary & Final Verdict
- **Status Integrasi**: `STUDIO_CPL_READY_FOR_CPMK`
- **Fokus Phase 1.8 Final Role Completeness Patch**:
  1. **Pemisahan Tegas Tiga Konsep Fundamental**:
     - **A. Pemetaan Kurikulum Resmi (*Official Curriculum Mapping*)**: `official_mapped = true`. Menyatakan bahwa dokumen kurikulum resmi menetapkan mata kuliah ini berkontribusi pada butir CPL tersebut. Data faktual ini **tidak boleh dihapus atau diubah** oleh dosen maupun AI.
     - **B. Inklusi Fokus RPS (*RPS Inclusion*)**: `included = true / false`. Menyatakan apakah dosen memilih butir CPL resmi tersebut untuk secara operasional ditekankan dan ditelusuri pada RPS mata kuliah ini. Nilai `included = false` **bukan berarti pemetaan kurikulum hilang**, melainkan berstatus `selection_status = 'NOT_SELECTED_FOR_RPS'`.
     - **C. Peran RPS (*RPS Role*)**: Untuk butir dengan `included = true`, dosen wajib menetapkan perannya sebagai `PRIMARY` (CPL Utama) atau `SUPPORTING` (CPL Pendukung).
  2. **Aturan Kelengkapan Approval (*Approval Completeness Rule*)**:
     - Memerlukan $\ge 1$ butir CPL berstatus `included = true` dengan peran `PRIMARY`.
     - **Seluruh** CPL yang berstatus `included = true` wajib memiliki peran definitif (`PRIMARY` atau `SUPPORTING`).
     - **Tidak boleh ada** CPL berstatus `included = true` yang tertinggal dengan peran `UNCLASSIFIED` saat menyetujui.
     - Jika masih terdapat butir `UNCLASSIFIED`, sistem memblokir persetujuan dengan pesan penolakan:
       > *"Masih terdapat CPL terpilih yang belum ditentukan perannya sebagai Utama atau Pendukung."*
  3. **Pengecualian Opsional (*Optional Exclusion / "Gunakan dalam RPS" Toggle*)**:
     - Dosen dapat mengalihkan saklar/checkbox *Gunakan dalam RPS* menjadi **OFF** untuk butir CPL resmi yang tidak ingin difokuskan pada semester berjalan.
     - Kartu CPL tersebut **tetap tampil pada daftar pemetaan resmi** dengan lencana `[ Tidak digunakan sebagai fokus RPS ]` sebagai bukti kurikulum yang transparan.
  4. **Kontrak Masukan untuk Studio CPMK (*CPMK Input Contract*)**:
     - **PRIMARY CPL** $\to$ Penggerak utama perumusan CPMK (*direct CPMK generation drivers*).
     - **SUPPORTING CPL** $\to$ Batasan kontekstual dan capaian transversal (*contextual constraints / transversal outcomes*).
     - **OFFICIAL MAPPED BUT NOT SELECTED** $\to$ Dipertahankan sebagai bukti kurikulum, **tidak** digunakan sebagai pemicu perumusan CPMK mandiri.

---

## 2. Struktur Payload Rekaman Tersimpan & Approved

### 2.1. Skema JSON Terstruktur (`siakad_rps_obe.cpl_mk_json`)
```json
{
  "course_code": "PKA32306",
  "status": "APPROVED",
  "updated_at": "2026-09-21 11:20:00",
  "items": [
    {
      "code": "P-2",
      "cpl_code": "P-2",
      "official_mapped": true,
      "included": true,
      "role": "PRIMARY",
      "selection_status": "SELECTED_PRIMARY",
      "provenance": "FACTUAL",
      "source": "EXPLICIT_MATRIX",
      "approved": true
    },
    {
      "code": "S-9",
      "cpl_code": "S-9",
      "official_mapped": true,
      "included": true,
      "role": "SUPPORTING",
      "selection_status": "SELECTED_SUPPORTING",
      "provenance": "FACTUAL",
      "source": "EXPLICIT_MATRIX",
      "approved": true
    },
    {
      "code": "KU-4",
      "cpl_code": "KU-4",
      "official_mapped": true,
      "included": false,
      "role": null,
      "selection_status": "NOT_SELECTED_FOR_RPS",
      "provenance": "FACTUAL",
      "source": "EXPLICIT_MATRIX",
      "approved": true
    }
  ]
}
```

---

## 3. Komponen Antarmuka Pengguna (*UI & Visual Grouping*)

1. **Pembedaan Visual Status Kartu CPL**:
   - **Terpilih - Utama (Primary)**: Border Hijau Teal (`border:1.5px solid #0f766e; background:#f0fdf4;`), tombol *⭐ Utama (Primary)* aktif.
   - **Terpilih - Pendukung (Supporting)**: Border Biru Sky (`border:1.5px solid #0284c7; background:#f0f9ff;`), tombol *🛡️ Pendukung* aktif.
   - **Terpilih - Belum Ditentukan (Unclassified)**: Border Kuning Amber (`border:1.5px solid #f59e0b; background:#fffbeb;`), badge peringatan `Pilih Peran:` aktif.
   - **Terpetakan Resmi - Tidak Digunakan**: Border Abu-abu putus-putus (`border:1px dashed #cbd5e1; background:#f8fafc; opacity:0.92;`), lencana `[ ➖ Tidak digunakan sebagai fokus RPS ]` + tombol aksi `[ ➕ Gunakan dalam RPS ]`.
2. **Ringkasan Komprehensif Real-time**:
   - Contoh tampilan:
     > **23 CPL Resmi Terpetakan** &bull; **8 digunakan dalam RPS** (3 Utama, 5 Pendukung) &bull; **15 tidak dipilih sebagai fokus RPS**
   - Jika ada butir belum ditentukan perannya:
     > ⚠️ *Peringatan: Terdapat N CPL terpilih yang belum ditentukan perannya (Wajib pilih Utama / Pendukung sebelum disetujui).*
3. **Filter Tab Interaktif**:
   - **Semua**: Menampilkan seluruh CPL resmi prodi.
   - **Fokus RPS**: Menampilkan hanya butir yang `included = true`.
   - **Rekomendasi / Terpetakan**: Menampilkan butir dengan pemetaan resmi atau usulan AI.

---

## 4. Rekomendasi Peran AI Bersifat Penasihat (*Advisory AI Role Recommendation*)
- Tombol **Rekomendasi Peran CPL (AI)** memberikan saran klasifikasi peran (`PRIMARY` vs `SUPPORTING`) serta opsi pengecualian (`included = false`) untuk CPL yang kurang relevan.
- AI beroperasi di bawah prinsip **Zero Fabrication**:
  - Dilarang keras mengarang kode CPL baru di luar `allowed_lookup` resmi.
  - Dilarang mengubah status `official_mapped = true` pada butir kurikulum.
  - Dilarang mengubah provenance `FACTUAL` menjadi `AI_PROPOSAL` pada data kurikulum resmi.
  - Rekomendasi AI harus disetujui/disesuaikan secara sadar oleh dosen pengampu.

---

## 5. Hasil Pengujian Verifikasi & Benchmark

Hasil pengujian otomatis pada `scratch/test_phase1_8_hardening.php`:

| No | Pengujian | Target Uji | Hasil Verifikasi | Status |
|---|---|---|---|---|
| 1 | **Semantik Metrik MPI** | Direct Scalar = 557, Agregat = 41, Total Checkmarks = 598, Track Columns = 1 | Sesuai semantik canonical Phase 1.7 | **PASS** |
| 2 | **High-CPL Course** | `PKA32306` Pengelolaan Acara Korporasi (23 CPL) | Seluruh 23 CPL tampil faktual, inisialisasi awal UNCLASSIFIED tanpa pemaksaan PRIMARY | **PASS** |
| 3 | **Normal Course** | `RPL52305` Pemrograman Berbasis Framework (4 CPL) | 4 CPL resmi (`P-12`, `KK-3`, `KK-7`, `KK-12`) termuat otomatis dengan role UNCLASSIFIED | **PASS** |
| 4 | **Special Status Matriks** | TRPL Matematika Diskrit & Pancasila, MPI Internship Sub-module (`MPI75305`) | 0 CPL polusi, banner status khusus aktif | **PASS** |
| 5 | **Validasi Approval** | Validasi $\ge 1$ `PRIMARY` & pelepasan blokir referensi | Approval ditolak jika 0 PRIMARY, disetujui jika $\ge 1$ PRIMARY tanpa memblokir referensi | **PASS** |
| 6 | **Penolakan CPL Ambigu** | Approval dengan 1 PRIMARY + 1 UNCLASSIFIED | Ditolak dengan pesan: *"Masih terdapat CPL terpilih yang belum ditentukan perannya sebagai Utama atau Pendukung."* | **PASS** |
| 7 | **Partisi Lengkap 23 CPL** | 3 PRIMARY + 5 SUPPORTING + 15 NOT_SELECTED_FOR_RPS = 23 | Jumlah partisi utuh 23 butir, integritas pemetaan kurikulum terjaga | **PASS** |
| 8 | **Kontrak Studio CPMK** | 3 Driver CPMK + 5 Batasan Transversal + 15 Bukti Kurikulum | Skema masukan CPMK terjamin bebas ambiguitas | **PASS** |

---

## 6. Verifikasi Regresi Lintas 5 Program Studi (Phase 1.7 Canonical Metrics)

| Prodi | CPL Resmi | BK Resmi | MK Resmi | Total SKS | Direct Scalar CPL-MK | Agregat Mapping | Total Matrix Checkmarks | Status Normalisasi |
|---|---|---|---|---|---|---|---|---|
| **Akuntansi Perpajakan (AP)** | 73 | 13 | 50 | 148 | 182 | 0 | 182 | `SOURCE_MAPPING_PARSED` |
| **Manajemen Pemasaran Internasional (MPI)** | 50 | 10 | 69 | 183 | **557** | **41** | **598** | `SOURCE_MAPPING_PARSED` |
| **Pengelolaan Konvensi dan Acara (PKA)** | 33 | 32 | 50 | 144 | 551 | 0 | 551 | `SOURCE_MAPPING_PARSED` |
| **Agribisnis Hortikultura (AH)** | 48 | 20 | 66 | 150 | 271 | 6 | 277 | `SOURCE_MAPPING_PARSED` |
| **Teknologi Rekayasa Perangkat Lunak (TRPL)** | 64 | 11 | 57 | 149 | 162 | 0 | 162 | `SOURCE_MAPPING_PARSED` |

---

## 7. Kesimpulan Akhir
Dengan selesainya **Phase 1.8 Final Role Completeness Patch**:
- Seluruh rekaman yang disetujui (*APPROVED*) dijamin memiliki pemisahan peran yang tegas dan lengkap tanpa ada butir `UNCLASSIFIED` yang lolos.
- Pilihan dosen untuk mengecualikan CPL dari fokus RPS tidak menghilangkan bukti pemetaan kurikulum.
- Kontrak data untuk **Studio CPMK** telah terdefinisi secara eksplisit dan siap diimplementasikan pada fase berikutnya.

**Final Verdict: `STUDIO_CPL_READY_FOR_CPMK`**
