<?php
define('BASEPATH', '1');
require 'c:/laragon/www/wbi/server/dosen/application/config/database.php';
$db_config = $db['default'];

$pdo = new PDO('mysql:host=' . $db_config['hostname'] . ';dbname=' . $db_config['database'] . ';charset=utf8', $db_config['username'], $db_config['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Find sample dosen
$stmt = $pdo->query("SELECT DISTINCT m.dosen_pa, sk.id_smt FROM siakad_krs sk JOIN mahasiswa_pt mp ON mp.nipd = sk.nipd JOIN mahasiswa m ON m.id_pd = mp.id_pd WHERE m.dosen_pa IS NOT NULL LIMIT 1");
$sample = $stmt->fetch(PDO::FETCH_ASSOC);
$id_ptk = $sample['dosen_pa'];
$id_smt = $sample['id_smt'];

echo "id_ptk: $id_ptk, id_smt: $id_smt\n";

// Let's check status_krs in siakad_status_krs
$stmt = $pdo->query("SELECT * FROM siakad_status_krs");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Let's check all siakad_krs for this dosen and this id_smt
$stmt = $pdo->prepare("SELECT sk.id_status_krs, sks.nm_status_krs, COUNT(*) as cnt 
    FROM siakad_krs sk 
    JOIN mahasiswa_pt mp ON mp.nipd = sk.nipd 
    JOIN mahasiswa m ON m.id_pd = mp.id_pd 
    LEFT JOIN siakad_status_krs sks ON sks.id_status_krs = sk.id_status_krs
    WHERE m.dosen_pa = ? AND sk.id_smt = ?
    GROUP BY sk.id_status_krs");
$stmt->execute([$id_ptk, $id_smt]);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Let's check all active students under this dosen
$stmt = $pdo->prepare("SELECT mp.id_jns_keluar, COUNT(*) as cnt 
    FROM mahasiswa m 
    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd 
    WHERE m.dosen_pa = ? 
    GROUP BY mp.id_jns_keluar");
$stmt->execute([$id_ptk]);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

// Check if there is status 0 in siakad_krs: What is status 0?
$stmt = $pdo->prepare("SELECT sk.id_rencana_studi, sk.nipd, sk.id_status_krs, sk.registrasi_alamat, sk.registrasi_no_hp, sk.create_date, sk.pengajuan_date 
    FROM siakad_krs sk 
    JOIN mahasiswa_pt mp ON mp.nipd = sk.nipd 
    JOIN mahasiswa m ON m.id_pd = mp.id_pd 
    WHERE m.dosen_pa = ? AND sk.id_smt = ? AND sk.id_status_krs = 0 LIMIT 5");
$stmt->execute([$id_ptk, $id_smt]);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

