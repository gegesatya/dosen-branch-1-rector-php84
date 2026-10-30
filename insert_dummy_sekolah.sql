INSERT IGNORE INTO dapo_sekolah (id, npsn, sekolah, bentuk, status, kabupaten_kota, lintang, bujur) VALUES 
(UUID(), CONCAT('MANUAL-', SUBSTRING(MD5(RAND()), 1, 6)), 'PPA NAFITRI KEMENANGAN T. BALAI', 'MA', 'SWASTA', 'Kota Tanjung Balai', 0, 0),
(UUID(), CONCAT('MANUAL-', SUBSTRING(MD5(RAND()), 1, 6)), 'SMAS PEMDA LANGKAT', 'SMA', 'SWASTA', 'Kab. Langkat', 0, 0);
