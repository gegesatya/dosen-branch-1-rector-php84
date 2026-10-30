---
name: wbi-spmi-dokumen-pembelajaran
version: 1.0.0
display_name: WBI SPMI Dokumen Pembelajaran OBE
description: Skill pendukung (supporting skill) yang memperluas 'akademik-dosen-obe-fiq' untuk penyusunan rangkaian dokumen pembelajaran berstandar SPMI Politeknik Wilmar Bisnis Indonesia (WBI). Mencakup formulir SPMI RPS 16 Pertemuan, Kontrak Pembelajaran, Rancangan Tugas Mahasiswa berbasis Case Method / Project-Based Learning, serta Roadmap Pembelajaran Berbasis Modul WBI.
parent_skill: akademik-dosen-obe-fiq
language: id
taxonomy: Bloom C4-C6 only
institution: Politeknik Wilmar Bisnis Indonesia (WBI)
regulation: Permendiktisaintek No. 39 Tahun 2025 jo. Permendiktisaintek No. 10 Tahun 2026; Pedoman Implementasi SPMI 2026
---

# WBI SPMI Dokumen Pembelajaran OBE

Gunakan skill ini sebagai spesialisasi lanjutan dari `akademik-dosen-obe-fiq` saat menyusun atau mengaudit naskah dokumen pembelajaran resmi di lingkungan **Politeknik Wilmar Bisnis Indonesia (WBI)**.

---

## 🏛️ Hubungan dengan Skill Induk (`akademik-dosen-obe-fiq`)

Skill ini mewarisi seluruh prinsip dasar dari skill induk:
1. **Source-First & Anti-Ngarang**: Data CPL, CPMK, Sub-CPMK, bobot, dan referensi wajib bersumber secara faktual dari Dokumen Kurikulum prodi.
2. **Taksonomi Bloom C4–C6 Only**: Capaian utama wajib menggunakan kata kerja operasional tingkat Analisis (C4), Evaluasi (C5), atau Kreasi (C6) tanpa menuliskan label/kode seperti `(C4)` atau `[C5]` di teks naskah.
3. **Standar Beban SKS**: 1 SKS = 45 jam = 2.700 menit per semester (Tatap Muka 50 menit, Penugasan Terstruktur 60 menit, Belajar Mandiri 60 menit per minggu).
4. **Bobot Penilaian Wajib Total 100%**.

---

## 📋 4 Mode Dokumen Pembelajaran Utama WBI

### 1. Mode RPS Lengkap 16 Pertemuan (Formulir SPMI WBI)
* **Tujuan**: Menghasilkan naskah lengkap Rencana Pembelajaran Semester 16 Pertemuan (termasuk UTS di minggu ke-8 dan UAS di minggu ke-16).
* **Template**: `templates/template_rps_16_pertemuan_spmi_wbi.md`
* **Struktur Wajib**:
  - `## 1. IDENTITAS DOKUMEN & MATA KULIAH`
  - `## 2. CAPAIAN PEMBELAJARAN LULUSAN (CPL) YANG DIBEBANKAN PADA MATA KULIAH`
  - `## 3. CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK), SUB-CPMK & MATRIKS DISTRIBUSI BOBOT ASESMEN OBE`
    - Tabel Matriks Rumusan CPMK & Pemetaan CPL Terkait
    - Tabel Matriks Distribusi Bobot Asesmen terhadap Sub-CPMK & CPMK (Blueprint Asesmen OBE)
  - `## 4. DESKRIPSI SINGKAT MATA KULIAH & PRASYARAT`
    - *Aturan Prasyarat*: Jika tidak tertulis eksplisit di kurikulum, wajib ditulis `Tidak ada`.
  - `## 5. DAFTAR REFERENSI`
  - `## 6. DETAIL RENCANA PEMBELAJARAN MINGGUAN (TABEL 16 PERTEMUAN)`
    - Format alokasi waktu per baris: `[menit_teori]/[menit_praktek]` (misal: `50/100`).

### 2. Mode Kontrak Pembelajaran (Formulir SPMI WBI)
* **Tujuan**: Menyusun kontrak perkuliahan resmi yang disepakati oleh Dosen Pengampu, Komting Kelas (Pihak II), dan disetujui Ketua Program Studi.
* **Template**: `templates/template_kontrak_kuliah_wbi.md`
* **Single Source of Truth**: Wajib mengadopsi 100% rumusan CPMK, Matriks Asesmen beserta Bobotnya, dan topik mingguan dari Dokumen Master RPS.
* **Skala Nilai Resmi**: Memuat tabel skala konversi nilai huruf Politeknik WBI (`references/skala_nilai_wbi.md`).

### 3. Mode Rancangan Tugas Mahasiswa (Formulir SPMI WBI)
* **Tujuan**: Menyusun panduan penugasan terstruktur berbasis *Case Method* atau *Project-Based Learning*.
* **Template**: `templates/template_rancangan_tugas_spmi_wbi.md`
* **Struktur Wajib**: Identitas Tugas, Tujuan Tugas (selaras Sub-CPMK), Uraian Tugas (Obyek Garapan, Batasan, Metode, Luaran), Kriteria Penilaian & Rubrik Analitik (4 level performa: Sangat Baik, Baik, Cukup, Kurang), serta Jadwal Pelaksanaan/Deadline.

### 4. Mode Roadmap Pembelajaran Berbasis Modul WBI
* **Tujuan**: Mengelompokkan capaian dan topik 16 pertemuan ke dalam 3–5 Modul Pembelajaran terpadu.
* **Template**: `templates/template_roadmap_modul_wbi.md`
* **Blok Wajib**: Selain tabel matriks markdown, wajib menyertakan blok kode ````json_roadmap```` di akhir naskah agar dapat dirender secara visual dan diekspor ke format Excel Roadmap WBI.

### 5. Mode Skenario Strategi Pembelajaran & Draf Berita Acara Perkuliahan (BAP)
* **Tujuan**: Menurunkan RPS OBE menjadi skenario langkah pembelajaran mingguan (sintaks SCL: Pendahuluan, Inti/Kolaborasi Mahasiswa, Penutup/Refleksi) per pertemuan 1 s.d. 16 sebagai strategi mewujudkan Sub-CPMK, dilengkapi draf realisasi Berita Acara Perkuliahan (BAP) faktual siap pakai.
* **Template**: `templates/template_skenario_pertemuan_bap_wbi.md`
* **Single Source of Truth**: Wajib mengadopsi 100% rumusan Sub-CPMK (Kolom 3), Bahan Kajian (Kolom 4), Bentuk & Metode (Kolom 5 & 6), Pengalaman Belajar (Kolom 7), Alokasi Waktu (Kolom 8 & 9), serta Kriteria & Bobot (Kolom 10–12) dari Dokumen Master RPS.
* **Struktur Wajib Per Pertemuan**:
  1. Identitas & Target Capaian (Minggu ke, Sub-CPMK, Bahan Kajian, Alokasi Waktu, Sumber Belajar, Kriteria/Bobot).
  2. Skenario Langkah Pembelajaran SCL (Tahap Pendahuluan/Apersepsi, Tahap Inti/Eksplorasi & Kolaborasi Mahasiswa, Tahap Penutup & Refleksi).
  3. Draf Realisasi Berita Acara Perkuliahan (BAP Faktual) yang merekam aktivitas riil di kelas.

---

## ⚡ Protokol Eksekusi Khusus

1. **Aturan Format Kanonikal (Universal Canonical Style)**:
   - Dilarang menambahkan kata pengantar/pembuka (*preamble*) seperti "Baik, berikut adalah draf...".
   - Respons naskah **wajib langsung dimulai dari baris pertama** (`# [JUDUL DOKUMEN]`).
   - Dilarang menambahkan penutup kesimpulan atau tanda tangan di bagian akhir dokumen.
   - Dilarang membuat numbered list (`1. `) untuk bagian yang hanya berisi 1 item/paragraf tunggal (contoh: Deskripsi Singkat, Prasyarat).

2. **Prosedur Dokumen Terpotong (*Tail-Context Continuation*)**:
   - Jika dokumen terhenti karena batas token (*length/max_tokens*), jalankan prosedur di `workflows/alur_lanjutan_dokumen_terpotong.md` dengan mengambil 4.000 karakter terakhir tanpa mengulang identitas dari awal.

3. **Prosedur Sinkronisasi Lintas Dokumen**:
   - Ikuti alur di `workflows/alur_sinkronisasi_rps_ke_turunan.md` untuk menyuntikkan Master RPS ke dalam prompt Kontrak Kuliah, Tugas, dan Roadmap.
