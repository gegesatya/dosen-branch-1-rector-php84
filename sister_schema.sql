CREATE TABLE IF NOT EXISTS `sister_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_pengguna` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sister_pengajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dosen` varchar(255) NOT NULL,
  `nama_mata_kuliah` varchar(255) DEFAULT NULL,
  `sks` int(11) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `tahun_ajaran` varchar(50) DEFAULT NULL,
  `last_sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sister_penelitian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dosen` varchar(255) NOT NULL,
  `judul_penelitian` text,
  `tahun` int(11) DEFAULT NULL,
  `peran` varchar(100) DEFAULT NULL,
  `sumber_dana` varchar(255) DEFAULT NULL,
  `last_sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sister_pengabdian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dosen` varchar(255) NOT NULL,
  `judul_pengabdian` text,
  `tahun` int(11) DEFAULT NULL,
  `peran` varchar(100) DEFAULT NULL,
  `mitra` varchar(255) DEFAULT NULL,
  `last_sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
