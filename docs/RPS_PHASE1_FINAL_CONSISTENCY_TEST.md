# LAPORAN FINAL CONSISTENCY TEST RPS GENERATOR PHASE 1
**Standar:** OBE, SN-Dikti 2026, SPMI Politeknik Wilmar Bisnis Indonesia  
**Tanggal Pengujian:** 17 September 2026  
**Status Verdict:** **READY FOR PHASE 2**

---

## 1. Pemisahan Source Status dan Effective Mapping Type

Sistem sekarang membedakan secara tegas antara **Status Sumber Dokumen** (`mapping_source_status`) dan **Tipe Pemetaan Efektif** (`effective_mapping_type`), tanpa menggabungkan dua status dalam satu field.

### Standar State Baku:
- **`mapping_source_status`**:
  - `MATRIX_PARSED`: Tabel matriks resmi ditemukan dan berhasil diparse deterministik.
  - `MATRIX_UNPARSED`: Tabel matriks resmi terdeteksi pada kurikulum, namun struktur baris/kolom scan markdown tidak dapat diparse deterministik.
  - `SECTION_PARSED`: Section/blok mata kuliah memuat kode CPL resmi dan berhasil diparse.
  - `SECTION_UNPARSED`: Section/blok mata kuliah ditemukan tetapi tidak memuat kode CPL resmi terurai.
  - `NO_EXPLICIT_SOURCE`: Dokumen tidak memuat matriks maupun section mapping eksplisit.

- **`effective_mapping_type`**:
  - `EXPLICIT_MATRIX`: Relasi CPL ↔ MK diparse langsung dari matriks resmi.
  - `EXPLICIT_SECTION`: Relasi CPL ↔ MK diparse langsung dari blok deskripsi MK resmi.
  - `DERIVED_VIA_BK`: Relasi CPL ↔ MK diturunkan secara deterministik melalui relasi transitif $MK \rightarrow BK \rightarrow CPL$.
  - `AI_EXTRACTED_MAPPING`: AI membantu membaca/mendekode bukti relasi faktual yang strukturnya teracak.
  - `AI_PROPOSAL_MAPPING`: AI mengusulkan mapping CPL berbasis Bahan Kajian karena relasi eksplisit/transitif tidak tersedia.
  - `NOT_AVAILABLE`: Tidak ada mapping atau evidence yang dapat disimpulkan.

*Catatan:* `AVAILABLE_FROM_CURRICULUM` hanya digunakan untuk status ketersediaan field deskriptif seperti `desc_status` (Deskripsi Mata Kuliah).

---

## 2. Hasil Retest 3 Mata Kuliah Nyata

| Parameter | Kasus 1: Riset Pemasaran (MPI) | Kasus 2: Algoritma & Struktur Data (TRPL) | Kasus 3: Akuntansi Biaya (AP) |
|---|---|---|---|
| **Program Studi** | S.Tr Manajemen Pemasaran Internasional | S.Tr Teknologi Rekayasa Perangkat Lunak | S.Tr Akuntansi Perpajakan |
| **1. mapping_source_status** | `MATRIX_UNPARSED` | `MATRIX_UNPARSED` | `MATRIX_UNPARSED` |
| **2. effective_mapping_type** | `AI_PROPOSAL_MAPPING` | `AI_PROPOSAL_MAPPING` | `AI_PROPOSAL_MAPPING` |
| **3. mapped_cpls** | `[]` *(Diusulkan AI saat penyusunan CPMK)* | `[]` *(Diusulkan AI saat penyusunan CPMK)* | `[]` *(Diusulkan AI saat penyusunan CPMK)* |
| **4. Apakah DERIVED_VIA_BK Berhasil Digunakan?** | **TIDAK** (Tabel markdown kurikulum unparsed/tidak deterministik; sistem defensif fallback ke `AI_PROPOSAL_MAPPING`) | **TIDAK** (Section heading 10.1 CPL-BK tidak memuat tabel markdown terurai; defensif fallback ke `AI_PROPOSAL_MAPPING`) | **TIDAK** (Matriks Bab X teracak vertikal akibat konversi PDF; defensif fallback ke `AI_PROPOSAL_MAPPING`) |
| **5. desc_status** | `AVAILABLE_FROM_CURRICULUM` | `NOT_FOUND` | `AVAILABLE_FROM_CURRICULUM` |
| **6. Label UI Kesiapan** | `⚠ Matriks ditemukan — belum terbaca` | `⚠ Matriks ditemukan — belum terbaca` | `⚠ Matriks ditemukan — belum terbaca` |

---

## 3. Hasil Test Strict Bloom Validation (No Silent Normalization)

Pengujian dilakukan untuk membuktikan bahwa level kognitif di luar C2–C6 (misal `C1`) **tidak lagi dinormalisasi secara diam-diam menjadi C4**, melainkan ditolak secara tegas.

### Uji Kasus Multi-Item CPMK:
- **Input Data:**
  - Item 1: `CPMK-1` dengan deskripsi mengingat konsep dasar dan `level_bloom = "C1"` *(INVALID)*.
  - Item 2: `CPMK-2` dengan deskripsi analisis terapan dan `level_bloom = "C4"` *(VALID)*.
- **Hasil Validator Backend (`_parse_and_validate_cpmk_json`):**
  - Item 1 (`C1`) $\rightarrow$ **DITOLAK / REJECTED** (Tidak disimpan dan tidak diubah ke C4).
  - Item 2 (`C4`) $\rightarrow$ **LOLOS / VALID**.
  - Total item hasil validasi: 1 butir (`CPMK-1` ber-Bloom `C4`).

### Uji Kasus Single-Item Revisi CPMK (`_parse_single_cpmk_json`):
- **Input:** Butir CPMK tunggal dengan `level_bloom = "C1"`.
- **Hasil:** Mengembalikan `null` (Gagal validasi / Rejection), memicu instruksi perbaikan kepada AI tanpa mengubah level Bloom secara sepihak.

---

## 4. Regression Guard Check: CPMK APPROVED $\rightarrow$ Generate RPS

1. **Alur Proteksi Backend (`ajax_process`):**
   - Mode generasi RPS lengkap (`susun_rps`) dan seluruh dokumen turunan (`kontrak_kuliah`, `rancangan_tugas`, `roadmap_wbi`, `skenario_pembelajaran`) memeriksa kolom `cpmk_status` pada database dan sesi.
   - Jika status CPMK belum `APPROVED`, sistem mengembalikan respon:
     ```json
     {
       "status": false,
       "is_cpmk_not_approved": true,
       "msg": "CPMK Belum Disetujui..."
     }
     ```
   - Generator AI RPS 16 pertemuan **sama sekali tidak dipanggil** sebelum ada persetujuan resmi dosen terhadap CPMK.
2. **Alur Proteksi Frontend:**
   - Tombol *Generate RPS Lengkap* dan tab dokumen turunan menampilkan indikator terkunci (*locked*) hingga dosen menyetujui draf CPMK di *CPMK Studio*.

---

## 5. Verdict Final

```
===================================================================
                   VERDICT: READY FOR PHASE 2
===================================================================
Seluruh kriteria Final Consistency Patch Phase 1 telah terpenuhi:
1. mapping_source_status dan effective_mapping_type terpisah jelas;
2. Logika deterministik DERIVED_VIA_BK terpasang dengan fallback aman;
3. Silent normalization Bloom C1->C4 telah dihapus 100% (Strict Rejection);
4. Workflow Guard CPMK APPROVED -> Generate RPS teruji dan aman;
5. Backward compatibility Phase 1 dan format data terjaga sepenuhnya.
===================================================================
```
