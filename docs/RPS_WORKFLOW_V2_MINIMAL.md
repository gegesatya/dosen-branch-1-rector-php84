# DESAIN ARSITEKTUR & ALUR KERJA RPS GENERATOR V2 (MINIMAL & PRAGMATIS)
**Sistem Informasi Akademik (SIAKAD) Politeknik Wilmar Bisnis Indonesia (WBI)**  
*Dokumen Desain Teknis Pembaruan Workflow Berbasis Skill `akademik-dosen-obe-fiq`*

---

## 1. Ringkasan Eksekutif & Prinsip Desain

Dokumen ini merancang pembaruan alur kerja (*workflow*) modul **RPS Generator Studio** (`C_rps_generator`) untuk menerapkan domain policy **Source-First, Provenance, Bertahap (CPMK → Sub-CPMK → RPS 16 Pertemuan), dan Bloom C2–C6 Proporsional** tanpa melakukan perombakan besar (*over-engineering*) dan tanpa menulis ulang (*rewriting*) mesin generator yang sudah berjalan stabil.

### Prinsip Utama Desain Minimal (V2):
1. **Zero / Minimal Database Changes:** Struktur tabel existing (`siakad_rps_obe` dan `siakad_rps_obe_detail`) dipertahankan sepenuhnya. Data terstruktur CPMK dan Sub-CPMK cukup disimpan dalam 2 kolom `TEXT/JSON` tambahan yang *nullable*.
2. **Reuse Existing Engine:** Mempertahankan 100% controller `C_rps_generator`, gateway `Ai_reviewer`, layout 2-panel (*Two-Pane Responsive*), dropzone upload Excel 13 kolom, PDF penelitian dosen, multi-provider AI (Gemini/Groq/DeepSeek/OpenAI), token continuation, auto-sync BAP, serta ekspor dokumen WBI.
3. **Step-by-Step Progressive Disclosure:** Alur linear yang intuitif pada panel kanan:
   - **Langkah 1:** *Curriculum Readiness Check* (Verifikasi Faktual).
   - **Langkah 2:** *CPMK Studio Card Editor* (Drafting AI `AI_PROPOSAL` → Review/Edit Dosen `MANUAL` → Persetujuan `APPROVED`).
   - **Langkah 3:** *Sub-CPMK Studio Card Editor* (Diturunkan hanya dari CPMK `APPROVED` → Persetujuan `APPROVED`).
   - **Langkah 4:** *RPS 16 Pertemuan & 5-in-1 Suite Generator* (Menggunakan generator existing dengan context terkunci).
4. **State Sederhana (Tanpa Complex State Machine):** Menggunakan status sederhana:
   - Status Provenance: `FACTUAL`, `AI_PROPOSAL`, `MANUAL`, `APPROVED`.
   - Domain Rule Dependensi: *Jika CPMK APPROVED diubah kembali, Sub-CPMK dan artefak turunannya harus dianggap memerlukan validasi ulang sebelum dapat dinyatakan final.*

---

## 2. Pemetaan Alur End-to-End V2 (Minimal)

```mermaid
flowchart TD
    A[1. Dosen Memilih Mata Kuliah via Select2] --> B[2. Frontend & Backend: Cek Curriculum Readiness]
    B --> C{Apakah CPL, BK, & Mapping Ditemukan?}
    C -- Ya --> D[Tampilkan Status Readiness: Lengkap / Parsial]
    C -- Tidak --> E[Peringatan Anti-Ngarang: Dokumen Kurikulum Belum Ada / MK Tidak Terdaftar]
    
    D --> F{Apakah CPMK Sudah Tersedia di DB/RPS Lama?}
    F -- Sudah Ada (FACTUAL / APPROVED) --> G[Tampilkan Daftar CPMK Aktif di Card Editor]
    F -- Belum Ada --> H[Tampilkan Tombol 'Susun Draft CPMK dengan AI']
    
    H --> I[AI Menghasilkan Draft CPMK JSON (AI_PROPOSAL)]
    I --> G
    
    G --> J[Dosen Melakukan Review: Edit Inline / Tambah / Hapus / Reorder / Re-prompt]
    J --> K[Dosen Klik: 'Setujui CPMK' -> Status APPROVED]
    
    K --> L[Langkah Sub-CPMK Aktif: AI Menghasilkan Draft Sub-CPMK (AI_PROPOSAL)]
    L --> M[Dosen Review Sub-CPMK: 1 Sub-CPMK untuk 1 s.d. Beberapa Pertemuan]
    M --> N[Dosen Klik: 'Setujui Sub-CPMK' -> Status APPROVED]
    
    N --> O[Langkah RPS 16 Pertemuan: Eksekusi Generator Existing ajax_process]
    O --> P[Render 5-in-1 Suite: RPS 16 Minggu, Kontrak, Tugas, Roadmap, Strategi/BAP]
    P --> Q[Simpan ke Database / Sync ke Jadwal & BAP Kelas]
```

---

## 3. Komponen Perubahan yang Benar-Benar Diperlukan

### 3.1. Langkah 1: Curriculum Readiness (Panel Kiri)
Begitu mata kuliah dipilih pada input Select2, sistem menjalankan pengecekan cepat (*Curriculum Readiness Check*) dan menampilkan widget ringkas di panel kiri:

| Item Pengecekan | Sumber Faktual | Status Tampilan UI |
|---|---|---|
| **CPL Prodi** | `siakad_pustaka_kurikulum` / naskah kurikulum prodi | `[✓] Ditemukan (X butir)` atau `[✗] Tidak Ditemukan` |
| **Mapping CPL ↔ MK** | Naskah kurikulum / matriks CPL-MK kurikulum | `[✓] Terpetakan Resmi` atau `[!] Perlu Usulan AI` |
| **Bahan Kajian (BK)** | Dokumen kurikulum prodi | `[✓] Tersedia` atau `[✗] Tidak Ditemukan` |
| **Deskripsi MK** | Master MK / Kurikulum prodi | `[✓] Tersedia dari Kurikulum` atau `[✗] Tidak Ditemukan` |
| **CPMK Mata Kuliah** | `siakad_rps_obe.cpmk_json` / `tujuan_belajar` | `[✓] Tersedia (X butir)` atau `[—] Belum Ada` |

---

### 3.2. Langkah 2: CPMK Editor (Panel Kanan - Mode Step 2)
Jika CPMK belum ada atau dosen ingin menyusun ulang, panel kanan menampilkan tombol aksi utama: **"✨ Susun CPMK dengan AI"**.

#### Karakteristik CPMK Editor:
1. **Generasi Langsung (Non-Blocking):** AI langsung merekomendasikan jumlah proporsional (2–6 butir CPMK sesuai klaster bahan kajian) berformat JSON terstruktur tanpa dialog pemblokiran.
2. **Kesesuaian Taksonomi Bloom C2–C6:** Menggunakan kata kerja operasional terukur (menjelaskan, menerapkan, menganalisis, mengevaluasi, merancang) dengan progresi kognitif menuju HOTS.
3. **Card-Based UI:** Setiap butir CPMK ditampilkan dalam card interaktif:
   - Badge status: `[FACTUAL]`, `[AI_PROPOSAL]`, atau `[MANUAL]`.
   - Kode CPMK (mis. CPMK-1, CPMK-2).
   - Teks rumusan capaian (dapat diedit langsung *inline / contenteditable*).
   - Tag CPL terkait (mis. `CPL-02`, `CPL-05`).
   - Tombol Aksi per Card:
     - 🔼 / 🔽 : Geser urutan CPMK.
     - 🗑️ : Hapus CPMK.
     - 🪄 *Perbaiki*: Prompt cepat untuk merevisi 1 butir CPMK spesifik (mis. *"Perjelas aspek praktiknya"*).
4. **Tombol Global:**
   - ➕ *Tambah Butir CPMK Manual*.
   - ✅ **Setujui CPMK (Jadikan Acuan Sub-CPMK)** $\rightarrow$ Mengubah status seluruh CPMK aktif menjadi `APPROVED`.

---

### 3.3. Langkah 3: Sub-CPMK Editor (Panel Kanan - Mode Step 3)
Setelah CPMK berstatus `APPROVED`, tahap penyusunan Sub-CPMK terbuka:

#### Karakteristik Sub-CPMK Editor:
1. **Hanya Turunan dari CPMK Approved:** AI menyusun draf Sub-CPMK dengan mengacu ketat pada CPMK yang telah disetujui.
2. **Alokasi Fleksibel Pertemuan:** Satu Sub-CPMK dapat dialokasikan untuk 1, 2, atau 3 pertemuan (mis. Sub-CPMK 1.1 dialokasikan untuk Minggu 1 & 2; tidak memaksakan asumsi kaku 1 pertemuan = 1 Sub-CPMK).
3. **Card UI Serupa:**
   - Badge CPMK Induk (mis. `Mendukung CPMK-1`).
   - Kode Sub-CPMK (mis. `Sub-CPMK 1.1`).
   - Rumusan kemampuan akhir (editable).
   - Alokasi Pertemuan / Minggu (mis. `Minggu 1–2`).
   - Bentuk Asesmen Awal (mis. `Studi Kasus / Kuis`).
4. **Tombol Persetujuan:**
   - ✅ **Setujui Sub-CPMK (Lanjut ke RPS 16 Pertemuan)** $\rightarrow$ Menetapkan status Sub-CPMK menjadi `APPROVED`.

---

### 3.4. Langkah 4: Generator RPS 16 Pertemuan & Dokumen Turunan (Panel Kanan - Mode Step 4)
- Begitu Sub-CPMK berstatus `APPROVED`, sistem mengaktifkan generator RPS 16 pertemuan existing.
- Data CPMK dan Sub-CPMK `APPROVED` dikirimkan sebagai **faktual input** ke `ajax_process()`.
- AI hanya bertugas menjabarkan matriks mingguan 16 pertemuan (bentuk SCL, pengalaman belajar, TM/PT/BM, kriteria/indikator evaluasi, dan bobot 100%).
- Seluruh 5 Tab Dokumen (*RPS 16 Minggu*, *Kontrak Kuliah*, *Rancangan Tugas*, *Roadmap WBI*, *Strategi Pertemuan/BAP*) terisi secara sinkron dan utuh seperti sistem saat ini.

---

## 4. Wireframe Antarmuka (UI Layout)

### 4.1. Wireframe Panel Kiri: Curriculum Readiness Card
```text
+-------------------------------------------------------------+
| IDENTITAS MATA KULIAH                                       |
| [TRPL201] Pemrograman Web Lanjut (3 SKS) - Semester 4       |
| Prodi: D4 Teknologi Rekayasa Perangkat Lunak                |
| Dosen: M. Syahril, M.Kom                                    |
+-------------------------------------------------------------+
| 📊 KESIAPAN KURIKULUM (CURRICULUM READINESS)                |
|                                                             |
| [✓] CPL Prodi           : 6 Butir Ditemukan (Kurikulum 2026)|
| [✓] Pemetaan CPL-MK     : S-01, P-02, KU-01, KK-03 (Faktual)|
| [✓] Bahan Kajian        : Arsitektur MVC, RESTful API, ORM  |
| [✓] Deskripsi MK        : Tersedia dari Kurikulum           |
| [!] CPMK Mata Kuliah    : Belum Disusun                     |
|                                                             |
| Status: SIAP UNTUK PENYUSUNAN CPMK                          |
+-------------------------------------------------------------+
| DOKUMEN PENDUKUNG (OPSIONAL)                                |
| [📁 Upload Excel 13 Kolom]  [📄 Upload Penelitian Dosen]   |
+-------------------------------------------------------------+
```

---

### 4.2. Wireframe Panel Kanan: CPMK Studio Card Editor
```text
+---------------------------------------------------------------------------------------------------+
| STUDIO PENYUSUNAN CAPAIAN PEMBELAJARAN (OBE)                                                      |
| [1. Kesiapan Kurikulum ✓]  -->  [2. CPMK (Aktif)]  -->  [3. Sub-CPMK]  -->  [4. RPS 16 Pertemuan] |
+---------------------------------------------------------------------------------------------------+
| Draf CPMK (AI_PROPOSAL) - Disusun berdasarkan 4 CPL yang dibebankan ke MK ini                     |
|                                                                                                   |
| +-----------------------------------------------------------------------------------------------+ |
| | CPMK-1 [AI_PROPOSAL]                              CPL Terkait: [P-02] [KK-03]   [▲] [▼] [🗑️]   | |
| |-----------------------------------------------------------------------------------------------| |
| | [Text Area / Inline Edit]                                                                     | |
| | Mampu menganalisis kebutuhan arsitektur perangkat lunak berbasis web modern menggunakan       | |
| | pola desain MVC dan standar RESTful API secara terstruktur.                                   | |
| |                                                                                               | |
| | 🪄 [Minta AI Perbaiki]: [ "Jadikan lebih spesifik ke framework PHP..." ] [Kirim Revisi]        | |
| +-----------------------------------------------------------------------------------------------+ |
|                                                                                                   |
| +-----------------------------------------------------------------------------------------------+ |
| | CPMK-2 [AI_PROPOSAL]                              CPL Terkait: [KU-01] [KK-03]  [▲] [▼] [🗑️]   | |
| |-----------------------------------------------------------------------------------------------| |
| | [Text Area / Inline Edit]                                                                     | |
| | Mampu merancang dan membangun aplikasi web interaktif yang mengintegrasikan basis data         | |
| | relasional dengan menerapkan prinsip keamanan dan optimasi query.                             | |
| |                                                                                               | |
| | 🪄 [Minta AI Perbaiki]: [ ...                                          ] [Kirim Revisi]        | |
| +-----------------------------------------------------------------------------------------------+ |
|                                                                                                   |
| [ + Tambah Butir CPMK Manual ]                                                                    |
|                                                                                                   |
| +-----------------------------------------------------------------------------------------------+ |
| | ⚠️ Perubahan pada CPMK Approved akan menandai Sub-CPMK & RPS turunan perlu validasi ulang.     | |
| |                                                                                               | |
| | [ 🔄 Susun Ulang Draft dengan AI ]             [  ✅ SETUJUI CPMK (LANJUT KE SUB-CPMK) ->  ]  | |
| +-----------------------------------------------------------------------------------------------+ |
+---------------------------------------------------------------------------------------------------+
```

---

### 4.3. Wireframe Panel Kanan: Sub-CPMK Studio Card Editor
```text
+---------------------------------------------------------------------------------------------------+
| STUDIO PENYUSUNAN SUB-CPMK                                                                        |
| [1. Kesiapan Kurikulum ✓]  -->  [2. CPMK ✓ (Approved)]  -->  [3. Sub-CPMK (Aktif)]  -->  [4. RPS] |
+---------------------------------------------------------------------------------------------------+
| Draf Sub-CPMK (AI_PROPOSAL) - Diturunkan dari 3 CPMK yang sudah disetujui                         |
|                                                                                                   |
| +-----------------------------------------------------------------------------------------------+ |
| | Sub-CPMK 1.1 [AI_PROPOSAL]   | Menginduk ke: CPMK-1              Alokasi: [Minggu 1 - 2] [🗑️]  | |
| |-----------------------------------------------------------------------------------------------| |
| | [Inline Edit]                                                                                 | |
| | Menjelaskan prinsip arsitektur web modern, konsep stateless HTTP, dan struktur modular MVC.   | |
| | Asesmen: Kuis Pemahaman Konsep (Bobot: 5%)                                                    | |
| +-----------------------------------------------------------------------------------------------+ |
|                                                                                                   |
| +-----------------------------------------------------------------------------------------------+ |
| | Sub-CPMK 1.2 [AI_PROPOSAL]   | Menginduk ke: CPMK-1              Alokasi: [Minggu 3 - 4] [🗑️]  | |
| |-----------------------------------------------------------------------------------------------| |
| | [Inline Edit]                                                                                 | |
| | Menerapkan routing dinamis, controller middleware, dan dependency injection pada aplikasi web.| |
| | Asesmen: Tugas Praktik Lab 1 (Bobot: 10%)                                                     | |
| +-----------------------------------------------------------------------------------------------+ |
|                                                                                                   |
| [ + Tambah Sub-CPMK ]                                                                             |
|                                                                                                   |
| [ <- Kembali ke Edit CPMK ]                      [  ✅ SETUJUI SUB-CPMK & GENERATE RPS LENGKAP ->] |
+---------------------------------------------------------------------------------------------------+
```

---

## 5. Database Change Minimum & Backward Compatibility

### 5.1. Skema Database Tambahan (Minimal)
Tidak ada tabel baru yang dibuat. Cukup menambahkan 3 kolom opsional bertipe `TEXT/VARCHAR` pada tabel master `siakad_rps_obe`:

```sql
-- Perubahan Skema Database Minimum (3 Kolom Tambahan pada siakad_rps_obe)
ALTER TABLE `siakad_rps_obe` 
ADD COLUMN `cpmk_json` LONGTEXT NULL COMMENT 'Array JSON terstruktur butir CPMK dan status provenance' AFTER `tujuan_belajar`,
ADD COLUMN `sub_cpmk_json` LONGTEXT NULL COMMENT 'Array JSON terstruktur butir Sub-CPMK dan alokasi pertemuan' AFTER `cpmk_json`,
ADD COLUMN `cpmk_status` VARCHAR(20) DEFAULT NULL COMMENT 'Status approval: AI_PROPOSAL, APPROVED, MANUAL (NULL untuk RPS lama)' AFTER `sub_cpmk_json`;
```

> **Catatan Penting:** Kolom `cpmk_status` diberi `DEFAULT NULL` agar data RPS existing yang dibuat sebelum implementasi V2 tidak otomatis dianggap `APPROVED` tanpa melalui verifikasi alur baru.

### 5.2. Struktur JSON Data Terstruktur
#### Struktur `cpmk_json`:
```json
[
  {
    "id": "cpmk_1",
    "kode": "CPMK-1",
    "teks": "Mampu menganalisis kebutuhan arsitektur perangkat lunak berbasis web modern menggunakan pola desain MVC.",
    "cpl_terkait": ["P-02", "KK-03"],
    "level_bloom": "C4",
    "provenance": "APPROVED"
  },
  {
    "id": "cpmk_2",
    "kode": "CPMK-2",
    "teks": "Mampu merancang dan membangun API layanan web RESTful yang aman dan scalable.",
    "cpl_terkait": ["KU-01", "KK-03"],
    "level_bloom": "C6",
    "provenance": "APPROVED"
  }
]
```

#### Struktur `sub_cpmk_json`:
```json
[
  {
    "id": "sub_1",
    "kode": "Sub-CPMK 1.1",
    "cpmk_id": "cpmk_1",
    "cpmk_kode": "CPMK-1",
    "teks": "Menjelaskan prinsip arsitektur web modern dan struktur direktori MVC.",
    "minggu_mulai": 1,
    "minggu_selesai": 2,
    "bentuk_asesmen": "Kuis Teori",
    "bobot_estimasi": 5,
    "provenance": "APPROVED"
  }
]
```

### 5.3. Strategi Backward Compatibility
- **RPS Lama (Existing Data):** Jika kolom `cpmk_json` bernilai `NULL`, sistem membaca data CPMK dari kolom `tujuan_belajar` dan naskah `raw_ai_output`. Tidak ada error ataupun data hilang.
- **Tabel Rincian 16 Pertemuan (`siakad_rps_obe_detail`):** Format 13 kolom WBI tetap persis sama, sehingga seluruh query export Excel, cetak PDF, dan sinkronisasi BAP tetap bekerja tanpa perubahan.

---

## 6. Endpoint Backend Minimum & Reuse `ajax_process()`

Pembaruan backend tidak memerlukan controller baru. Cukup menambahkan 1 method AJAX dan memanfaatkan method `ajax_process()` yang sudah ada:

### 6.1. Endpoint Baru: `ajax_check_curriculum_readiness`
- **Route:** `POST C_rps_generator/ajax_check_curriculum_readiness`
- **Tujuan:** Memeriksa kesiapan data kurikulum secara instan saat mata kuliah dipilih.
- **Input:** `kode_mk`, `nm_mk`, `prodi`, `id_mk`
- **Proses:**
  1. Lookup dokumen kurikulum prodi di `siakad_pustaka_kurikulum`.
  2. Ekstrak data CPL prodi & pemetaan ke MK.
  3. Cek deskripsi MK dan bahan kajian.
  4. Cek apakah di `siakad_rps_obe` sudah ada data CPMK untuk MK ini.
- **Output JSON:**
  ```json
  {
    "status": true,
    "cpl_found": true,
    "cpl_items": ["S-01", "P-02", "KU-01", "KK-03"],
    "mapping_found": true,
    "bahan_kajian_found": true,
    "bahan_kajian_items": ["Arsitektur MVC", "REST API", "Database ORM"],
    "deskripsi_found": true,
    "cpmk_existing": [ ... ],
    "is_ready": true
  }
  ```

---

### 6.2. Reuse Existing `ajax_process()` (Multi-Mode Orchestrator)
Method `ajax_process()` yang sudah ada diperluas dengan menambahkan 2 `mode` baru:

```php
// Pada C_rps_generator::ajax_process()
$mode = $this->input->post('mode') ?: 'susun_rps';

switch ($mode) {
    case 'susun_cpmk':
        // Prompt AI khusus merumuskan 2-6 CPMK terstruktur JSON berbasis CPL & BK faktual
        $result = $this->_generate_cpmk_json($context);
        break;

    case 'susun_sub_cpmk':
        // Prompt AI khusus merumuskan Sub-CPMK fleksibel berbasis CPMK yang berstatus APPROVED
        $cpmk_approved = $this->input->post('cpmk_approved_json');
        $result = $this->_generate_sub_cpmk_json($context, $cpmk_approved);
        break;

    case 'susun_rps':
    default:
        // Generator RPS 16 Pertemuan EXISTING:
        // Jika cpmk_approved dan sub_cpmk_approved dikirim, injeksikan sebagai GROUND TRUTH
        // sehingga AI langsung menyusun matriks 16 minggu tanpa merumuskan ulang CPMK.
        $result = $this->_generate_full_rps_existing($context);
        break;
}
```

---

## 7. Inventori File yang Perlu Diubah

| No | File | Jenis Perubahan | Deskripsi Perubahan Minimal |
|---|---|---|---|
| 1 | [`application/controllers/C_rps_generator.php`](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/controllers/C_rps_generator.php) | **Controller** | • Tambahkan method `ajax_check_curriculum_readiness()`.<br>• Tambahkan cabang `mode === 'susun_cpmk'` dan `mode === 'susun_sub_cpmk'` di dalam `ajax_process()`.<br>• Update `ajax_save_to_db()` untuk menyimpan `cpmk_json`, `sub_cpmk_json`, dan `cpmk_status`. |
| 2 | [`application/views/rps_generator/v_rps_generator.php`](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/views/rps_generator/v_rps_generator.php) | **View (HTML)** | • Tambahkan box *Curriculum Readiness* di bawah Identitas MK (Panel Kiri).<br>• Tambahkan step indicator navigasi tab di atas Canvas Document (Panel Kanan): `[1. Kurikulum] -> [2. CPMK] -> [3. Sub-CPMK] -> [4. RPS 16 Minggu]`. |
| 3 | [`application/views/rps_generator/v_rps_generator_after_page.php`](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/views/rps_generator/v_rps_generator_after_page.php) | **View (JS Engine)** | • Handler AJAX pemanggilan `ajax_check_curriculum_readiness()` saat MK dipilih.<br>• Render komponen card editor CPMK & Sub-CPMK (inline edit, drag/reorder, hapus, add).<br>• Handler tombol *Setujui CPMK* & *Setujui Sub-CPMK*.<br>• Pengiriman payload `cpmk_json` & `sub_cpmk_json` ke generator full RPS dan `ajax_save_to_db`. |
| 4 | [`application/models/Rps_obe_model.php`](file:///c:/laragon/www/wbi/server/dosen_branch_1/application/models/Rps_obe_model.php) | **Model** | • Masukkan `cpmk_json`, `sub_cpmk_json`, dan `cpmk_status` ke dalam field select/get CRUD (otomatis tercakup pada query `get_by_id`). |

---

## 8. Analisis Risiko & Mitigasi terhadap RPS Existing

| Potensi Risiko | Tingkat Risiko | Dampak | Strategi Mitigasi (Pencegahan) |
|---|---|---|---|
| **RPS Lama Tidak Memiliki Data JSON CPMK** | Rendah | Dosen membuka RPS lama yang disimpan sebelum V2 diterapkan (`cpmk_status` bernilai `NULL`). | **Fallback Otomatis:** Jika `cpmk_json` kosong/null, frontend langsung membuka Tab 4 (RPS 16 Pertemuan) dan membaca data teks dari `tujuan_belajar` dan `raw_ai_output`. |
| **Token AI Terpotong Saat Generate CPMK / Sub-CPMK** | Sangat Rendah | Format JSON tidak valid atau terputus di tengah jalan. | **Ukuran Output Kecil:** Output JSON CPMK/Sub-CPMK hanya ~300–800 token (jauh di bawah batas 4096/8192 token model AI). Sistem dilengkapi fungsi `json_repair / parser fallback`. |
| **Dosen Mengubah CPMK yang Sudah Approved** | Sedang | Sub-CPMK dan matriks 16 minggu menjadi tidak selaras dengan CPMK baru. | **Domain Rule Dependency:** Sistem menampilkan notifikasi badge *"Memerlukan Validasi Ulang"* pada Sub-CPMK dan menonaktifkan finalisasi RPS sampai dosen menyetujui ulang alur Sub-CPMK. |
| **Penambahan Mode pada `ajax_process()` Existing** | Low Risk / Membutuhkan Regression Test | Potensi regresi pada pemanggilan mode `susun_rps` lama atau dokumen turunan (Kontrak, Tugas, Roadmap, BAP). | **Regression Testing & Strict Fallback:** Menjaga *default case* pada switch-statement `ajax_process()` tetap mengeksekusi alur `susun_rps` existing secara utuh tanpa modifikasi logika internal. Lakukan pengujian regresi pada: (1) ekspor Excel WBI 13 kolom, (2) sinkronisasi BAP jadwal kelas, dan (3) reload RPS lama. |

---

## 9. Rekomendasi Implementasi Bertahap (Incremental Rollout)

Untuk menjamin stabilitas sistem dan meminimalkan risiko regresi pada RPS yang sedang aktif digunakan dosen, implementasi V2 direkomendasikan dilakukan secara bertahap:

### 🎯 PHASE 1 (Fokus Utama):
Implementasi alur awal sampai dengan persetujuan CPMK:
1. **Select Mata Kuliah** via Select2.
2. Pengecekan & Tampilan Widget **Curriculum Readiness** di panel kiri.
3. Tombol & Handler **"✨ Susun CPMK dengan AI"** (menghasilkan draf `AI_PROPOSAL`).
4. **Card Editor CPMK** di panel kanan:
   - Edit teks CPMK inline.
   - Tambah & hapus butir CPMK.
   - Reorder urutan CPMK (🔼 / 🔽).
   - Melihat tag CPL terkait.
   - Re-prompt revisi 1 butir CPMK (🪄 *Perbaiki*).
5. Tombol & Aksi **"✅ Setujui CPMK (APPROVED)"**.

> **Batasan Phase 1:** Alur Sub-CPMK dan generator full RPS 16 pertemuan **belum diubah** pada Phase 1. Generator RPS existing tetap berjalan sebagai fallback normal setelah CPMK disetujui.

---

### 🚀 PHASE 2 (Tahap Lanjutan):
1. Implementasi **Sub-CPMK Card Editor** yang diturunkan hanya dari CPMK `APPROVED`.
2. Alokasi fleksibel Sub-CPMK ke pertemuan mingguan (1 Sub-CPMK untuk 1–3 pertemuan).
3. Integrasi CPMK & Sub-CPMK `APPROVED` sebagai *ground-truth context* ke dalam generator RPS 16 Pertemuan `ajax_process()`.
4. Mekanisme visual validasi ulang dependensi jika CPMK `APPROVED` diubah kembali oleh dosen.

