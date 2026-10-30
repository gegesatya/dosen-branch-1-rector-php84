<?php
define('BASEPATH', '1');
require 'c:/laragon/www/wbi/server/dosen/application/config/database.php';
$db_config = $db['default'];

$pdo = new PDO('mysql:host=' . $db_config['hostname'] . ';dbname=' . $db_config['database'] . ';charset=utf8', $db_config['username'], $db_config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get dosen_pa and current id_smt
$stmt = $pdo->query("SELECT DISTINCT m.dosen_pa, sk.id_smt FROM siakad_krs sk JOIN mahasiswa_pt mp ON mp.nipd = sk.nipd JOIN mahasiswa m ON m.id_pd = mp.id_pd WHERE m.dosen_pa IS NOT NULL LIMIT 1");
$sample = $stmt->fetch(PDO::FETCH_ASSOC);
$id_ptk = $sample['dosen_pa'];
$id_smt = $sample['id_smt'];

// Let's check all siakad_krs records for this id_ptk in id_smt
$stmt = $pdo->prepare("SELECT sk.id_status_krs, 
    COUNT(*) as total,
    SUM(CASE WHEN sk.pengajuan_date IS NOT NULL AND sk.pengajuan_date != '0000-00-00 00:00:00' THEN 1 ELSE 0 END) as has_pengajuan,
    SUM(CASE WHEN sk.registrasi_no_hp IS NOT NULL AND sk.registrasi_no_hp != '' THEN 1 ELSE 0 END) as has_hp,
    SUM(CASE WHEN sk.registrasi_alamat IS NOT NULL AND sk.registrasi_alamat != '' THEN 1 ELSE 0 END) as has_alamat
    FROM siakad_krs sk 
    JOIN mahasiswa_pt mp ON mp.nipd = sk.nipd 
    JOIN mahasiswa m ON m.id_pd = mp.id_pd 
    WHERE m.dosen_pa = ? AND sk.id_smt = ?
    GROUP BY sk.id_status_krs");
$stmt->execute([$id_ptk, $id_smt]);
echo "siakad_krs stats for current id_smt ($id_smt):\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Check ALL active advisees in mahasiswa_pt (who have NOT graduated/dropped out)
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total_advisees,
    SUM(CASE WHEN sk.id_rencana_studi IS NOT NULL THEN 1 ELSE 0 END) as has_krs_this_sem,
    SUM(CASE WHEN sk.id_rencana_studi IS NULL THEN 1 ELSE 0 END) as no_krs_this_sem
    FROM mahasiswa m 
    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd 
    LEFT JOIN siakad_krs sk ON sk.nipd = mp.nipd AND sk.id_smt = ?
    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL");
$stmt->execute([$id_smt, $id_ptk]);
echo "\nAdvisees stats:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Check angkatan of advisees who have NO krs this semester
$stmt = $pdo->prepare("SELECT YEAR(mp.tgl_masuk_sp) as tahun_masuk, SUBSTR(mp.nipd, 1, 2) as angkatan_2d, COUNT(*) as cnt
    FROM mahasiswa m 
    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd 
    LEFT JOIN siakad_krs sk ON sk.nipd = mp.nipd AND sk.id_smt = ?
    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL AND sk.id_rencana_studi IS NULL
    GROUP BY tahun_masuk, angkatan_2d");
$stmt->execute([$id_smt, $id_ptk]);
echo "\nAdvisees without KRS by angkatan:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

