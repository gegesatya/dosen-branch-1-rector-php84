-- ==============================================================================
-- MIGRATION SCRIPT: SINKRONISASI STRUKTUR DATABASE PRODUCTION (PORTAL WBI SERVER)
-- Terkait Modul RPS OBE, Skenario Pembelajaran SCL 16 Pertemuan, & Audit Kurikulum
-- ==============================================================================

-- 1. Tabel siakad_rps_obe: Tambahkan kolom skenario_pembelajaran_markdown
ALTER TABLE `siakad_rps_obe` 
ADD COLUMN `skenario_pembelajaran_markdown` LONGTEXT NULL COMMENT 'Naskah Skenario Strategi Pembelajaran SCL & Draf BAP 16 Pertemuan';

-- 2. Tabel siakad_rencana_pertemuan: Tambahkan kolom integrasi OBE & Skenario BAP
ALTER TABLE `siakad_rencana_pertemuan` 
ADD COLUMN `sub_cpmk` TEXT NULL COMMENT 'Sub-CPMK terkait pertemuan',
ADD COLUMN `metode_pembelajaran` VARCHAR(255) NULL COMMENT 'Metode Pembelajaran SCL / Case Method / PjBL',
ADD COLUMN `indikator_evaluasi` TEXT NULL COMMENT 'Indikator & Kriteria Evaluasi / Bobot',
ADD COLUMN `draf_bap` MEDIUMTEXT NULL COMMENT 'Draf Realisasi Berita Acara Perkuliahan Faktual',
ADD COLUMN `catatan_refleksi` TEXT NULL COMMENT 'Catatan Refleksi Pembelajaran Dosen';

-- 3. Tabel siakad_pustaka_kurikulum: Tambahkan kolom hasil audit kurikulum OBE
ALTER TABLE `siakad_pustaka_kurikulum` 
ADD COLUMN `audit_terakhir_at` DATETIME NULL COMMENT 'Waktu audit kurikulum terakhir',
ADD COLUMN `audit_oleh_ptk` INT NULL COMMENT 'ID PTK Auditor',
ADD COLUMN `audit_oleh_nama` VARCHAR(150) NULL COMMENT 'Nama Auditor Kurikulum',
ADD COLUMN `audit_kesimpulan` TEXT NULL COMMENT 'Kesimpulan / Ringkasan Hasil Audit',
ADD COLUMN `audit_full_result` MEDIUMTEXT NULL COMMENT 'Hasil Lengkap Audit OBE / JSON';

-- 4. Tabel siakad_rps_obe & siakad_rps_online: Kolom soft delete
ALTER TABLE `siakad_rps_obe` 
ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `change_date`,
ADD COLUMN `deleted_at` DATETIME NULL AFTER `is_deleted`,
ADD COLUMN `deleted_by` VARCHAR(100) NULL AFTER `deleted_at`;

ALTER TABLE `siakad_rps_online` 
ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `change_date`,
ADD COLUMN `deleted_at` DATETIME NULL AFTER `is_deleted`,
ADD COLUMN `deleted_by` VARCHAR(100) NULL AFTER `deleted_at`;

