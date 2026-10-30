# Dokumentasi Perubahan Struktur Tabel & SQL Query
**Modul: RPS Generator AI & Kurikulum OBE WBI**  
**Tanggal Update:** 17 September 2026  
**Database:** `portal_wbi_2026`  

---

## 1. Ringkasan Status Database

| No | Nama Tabel | Status | Deskripsi & Kegunaan |
|:---:|---|:---:|---|
| 1 | `siakad_rps_obe` | **Tabel Baru** | Menyimpan master dokumen RPS OBE lengkap, Kontrak Kuliah, Rancangan Tugas (*Case Method/PjBL*), Roadmap Modul WBI, Skenario Pembelajaran SCL & Draf BAP 16 Pertemuan, serta status *provenance* CPL/CPMK. |
| 2 | `siakad_rps_obe_detail` | **Tabel Baru** | Menyimpan matriks detail 16 pertemuan mingguan (Sub-CPMK, Bahan Kajian, Bentuk, Metode SCL, Waktu Teori/Praktek, Rubrik Asesmen, Bobot %, Sumber Belajar). Relasi *Cascade* ke `siakad_rps_obe`. |
| 3 | `siakad_pustaka_kurikulum` | **Tabel Baru** | Pustaka dokumen kurikulum prodi (PDF/DOCX/TXT) sebagai basis referensi RAG (*Retrieval-Augmented Generation*) dan modul Audit Kurikulum OBE. |
| 4 | `siakad_api_key` | **Tabel Baru** | Menyimpan kredensial API Key AI multi-provider (*Google Gemini, Groq, OpenAI, DeepSeek*) yang diisolasi per Dosen (`id_ptk`). |

---

## 2. SQL Query Pembuatan Tabel Baru (`CREATE TABLE`)

```sql
-- --------------------------------------------------------
-- 1. Tabel Master RPS OBE & Dokumen Pembelajaran WBI
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siakad_rps_obe` (
  `id_rps` int unsigned NOT NULL AUTO_INCREMENT,
  `id_ptk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID Dosen Penyusun / Pengampu',
  `koordinator_rps` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID Koordinator RPS',
  `kaprodi_rps` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID Kaprodi yang menyetujui',
  `tanggal_rps` date DEFAULT NULL,
  `id_mk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_mk` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nm_mk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sks_mk` int DEFAULT '3',
  `sks_tm` int DEFAULT '0' COMMENT 'SKS Tatap Muka',
  `sks_prak` int DEFAULT '0' COMMENT 'SKS Praktikum',
  `semester` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosen_pengampu` text COLLATE utf8mb4_unicode_ci,
  `deskripsi_mata_kuliah` text COLLATE utf8mb4_unicode_ci,
  `deskripsi_mk_source` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prasyarat_mata_kuliah` text COLLATE utf8mb4_unicode_ci,
  `cpl` text COLLATE utf8mb4_unicode_ci COMMENT 'Capaian Pembelajaran Lulusan',
  `tujuan_belajar` text COLLATE utf8mb4_unicode_ci COMMENT 'CPMK Bloom C4-C6',
  `cpmk_json` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Array JSON terstruktur butir CPMK dan status provenance',
  `cpmk_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status approval CPMK: AI_PROPOSAL, APPROVED, MANUAL',
  `cpl_mk_json` longtext COLLATE utf8mb4_unicode_ci,
  `cpl_mk_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referensi` text COLLATE utf8mb4_unicode_ci,
  `image_organisasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manfaat_mata_kuliah` text COLLATE utf8mb4_unicode_ci,
  `cp_mata_kuliah` text COLLATE utf8mb4_unicode_ci,
  `analisis_instruksional` text COLLATE utf8mb4_unicode_ci,
  `strategi_perkuliahan` text COLLATE utf8mb4_unicode_ci,
  `materi_bahan_bacaan` text COLLATE utf8mb4_unicode_ci,
  `tugas_tugas` text COLLATE utf8mb4_unicode_ci,
  `kontrak_kriteria_penilaian` text COLLATE utf8mb4_unicode_ci,
  `jadwal_perkuliahan` text COLLATE utf8mb4_unicode_ci,
  `tata_kelola_perkuliahan` text COLLATE utf8mb4_unicode_ci,
  `kontrak_kuliah_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Naskah Kontrak Perkuliahan Standar SPMI WBI',
  `tugas_ke` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tugas_minggu_ke` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tujuan_tugas` text COLLATE utf8mb4_unicode_ci,
  `tugas_obyek_garapan` text COLLATE utf8mb4_unicode_ci,
  `tugas_batasan` text COLLATE utf8mb4_unicode_ci,
  `tugas_metode` text COLLATE utf8mb4_unicode_ci,
  `tugas_luaran` text COLLATE utf8mb4_unicode_ci,
  `tugas_penilaian_kerapian` text COLLATE utf8mb4_unicode_ci,
  `tugas_ketepatan` text COLLATE utf8mb4_unicode_ci,
  `rancangan_tugas_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Naskah RTM Case Method / Project-Based Learning',
  `roadmap_modul_json` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Array JSON 4 Modul Pembelajaran WBI',
  `roadmap_wbi_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Naskah Roadmap Pembelajaran Berbasis Modul',
  `matrix_pertemuan_json` longtext COLLATE utf8mb4_unicode_ci,
  `skenario_pembelajaran_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Naskah Skenario Strategi Pembelajaran SCL & Draf BAP 16 Pertemuan',
  `ai_provider` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'gemini / openai / groq / deepseek',
  `ai_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_ai_output` longtext COLLATE utf8mb4_unicode_ci,
  `id_status_pengajuan_rps` int DEFAULT '1' COMMENT '1: Draft, 2: Diajukan, 3: Disetujui',
  `create_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `change_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_rps`),
  KEY `idx_id_ptk` (`id_ptk`),
  KEY `idx_id_mk` (`id_mk`),
  KEY `idx_kode_mk` (`kode_mk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel Detail Matriks 16 Pertemuan RPS OBE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siakad_rps_obe_detail` (
  `id_detail_rps` int unsigned NOT NULL AUTO_INCREMENT,
  `id_rps` int unsigned NOT NULL,
  `kolom_1` int DEFAULT NULL COMMENT 'Minggu ke- (1-16)',
  `kolom_2` int DEFAULT NULL COMMENT 'Pertemuan ke- (1-16)',
  `kolom_3` text COLLATE utf8mb4_unicode_ci COMMENT 'Sub-CPMK (Kemampuan Akhir yang Direncanakan C4-C6)',
  `kolom_4` text COLLATE utf8mb4_unicode_ci COMMENT 'Bahan Kajian / Materi Pembelajaran',
  `kolom_5` text COLLATE utf8mb4_unicode_ci COMMENT 'Bentuk Pembelajaran (Kuliah, Responsi, Praktikum)',
  `kolom_6` text COLLATE utf8mb4_unicode_ci COMMENT 'Metode Pembelajaran (SCL, Case Method, PjBL)',
  `kolom_7` text COLLATE utf8mb4_unicode_ci COMMENT 'Pengalaman Belajar Mahasiswa',
  `kolom_8` int DEFAULT '0' COMMENT 'Waktu Teori (Menit)',
  `kolom_9` int DEFAULT '0' COMMENT 'Waktu Praktek (Menit)',
  `kolom_10` text COLLATE utf8mb4_unicode_ci COMMENT 'Kriteria Penilaian / Rubrik',
  `kolom_11` text COLLATE utf8mb4_unicode_ci COMMENT 'Indikator Penilaian Terukur',
  `kolom_12` int DEFAULT '0' COMMENT 'Bobot Nilai (%)',
  `kolom_13` text COLLATE utf8mb4_unicode_ci COMMENT 'Sumber Belajar / Rujukan / Daftar Pustaka',
  PRIMARY KEY (`id_detail_rps`),
  KEY `idx_id_rps` (`id_rps`),
  CONSTRAINT `fk_rps_obe_detail` FOREIGN KEY (`id_rps`) REFERENCES `siakad_rps_obe` (`id_rps`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel Pustaka Kurikulum & Audit Kurikulum OBE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siakad_pustaka_kurikulum` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sms` varchar(50) DEFAULT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `judul_dokumen` varchar(255) NOT NULL,
  `tahun_kurikulum` varchar(20) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path_doc` text,
  `file_path_content` text,
  `total_karakter` int DEFAULT '0',
  `uploaded_by_ptk` int DEFAULT NULL,
  `uploaded_by_nama` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `audit_terakhir_at` datetime DEFAULT NULL COMMENT 'Waktu audit kurikulum terakhir',
  `audit_oleh_ptk` int DEFAULT NULL COMMENT 'ID PTK Auditor',
  `audit_oleh_nama` varchar(150) DEFAULT NULL COMMENT 'Nama Auditor Kurikulum',
  `audit_kesimpulan` text COMMENT 'Kesimpulan / Ringkasan Hasil Audit',
  `audit_full_result` mediumtext COMMENT 'Hasil Lengkap Audit OBE / JSON',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_prodi` (`nama_prodi`(50)) USING BTREE,
  KEY `idx_sms` (`id_sms`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 4. Tabel Manajemen Kunci API AI Dosen
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siakad_api_key` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ptk` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ai_provider` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'gemini',
  `ai_model` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'gemini-1.5-flash',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_ptk_provider` (`id_ptk`,`ai_provider`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## 3. SQL Query Penyesuaian Kolom Tambahan (`ALTER TABLE`)

Jika tabel-tabel di atas sudah pernah dibuat versi awalnya sebelumnya di server produksi/staging, jalankan query `ALTER TABLE` berikut untuk memastikan seluruh kolom terbaru telah sinkron:

```sql
-- 1. Tambahan kolom Studio CPL, CPMK terstruktur, dan Skenario Pembelajaran pada siakad_rps_obe
ALTER TABLE `siakad_rps_obe`
  ADD `deskripsi_mk_source` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sumber Deskripsi: CURRICULUM, RPS_LAMA, AI_DRAFT, MANUAL' AFTER `deskripsi_mata_kuliah`,
  ADD `cpmk_json` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Array JSON terstruktur butir CPMK dan status provenance' AFTER `tujuan_belajar`,
  ADD `cpmk_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status approval CPMK: AI_PROPOSAL, APPROVED, MANUAL' AFTER `cpmk_json`,
  ADD `cpl_mk_json` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Array JSON butir CPL yang dibebankan pada MK dan perannya' AFTER `cpmk_status`,
  ADD `cpl_mk_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status approval CPL-MK: DRAFT, APPROVED' AFTER `cpl_mk_json`,
  ADD `skenario_pembelajaran_markdown` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Naskah Skenario Strategi Pembelajaran SCL & Draf BAP 16 Pertemuan' AFTER `change_date`;

-- 2. Tambahan kolom Audit Kurikulum OBE pada siakad_pustaka_kurikulum (jika belum ada)
ALTER TABLE `siakad_pustaka_kurikulum`
  ADD `audit_terakhir_at` datetime DEFAULT NULL COMMENT 'Waktu audit kurikulum terakhir' AFTER `updated_at`,
  ADD `audit_oleh_ptk` int DEFAULT NULL COMMENT 'ID PTK Auditor' AFTER `audit_terakhir_at`,
  ADD `audit_oleh_nama` varchar(150) DEFAULT NULL COMMENT 'Nama Auditor Kurikulum' AFTER `audit_oleh_ptk`,
  ADD `audit_kesimpulan` text COMMENT 'Kesimpulan / Ringkasan Hasil Audit' AFTER `audit_oleh_nama`,
  ADD `audit_full_result` mediumtext COMMENT 'Hasil Lengkap Audit OBE / JSON' AFTER `audit_kesimpulan`;
```
