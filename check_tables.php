<?php
$conn = new mysqli('localhost','root','','portal_wbi');

$tables = ['feeder_mahasiswa', 'feeder_mahasiswa_pt', 'sms'];
foreach ($tables as $t) {
    $r = $conn->query("SHOW TABLES LIKE '$t'");
    echo "$t: " . ($r->num_rows > 0 ? 'EXISTS' : 'MISSING') . "\n";
}

// Also check what query error we'd get from judul_berita_acara
$enkrip = '0deb1c54814305ca9ad266f53bc82511';
$sql = "SELECT * FROM siakad_skripsi_pengajuan_judul j
    JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = j.id_judul_skripsi
    JOIN feeder_mahasiswa_pt mp ON mp.nipd = j.nipd
    JOIN feeder_mahasiswa m ON m.id_pd = mp.id_pd
    JOIN sms s ON s.id_sms = mp.id_sms
    WHERE md5(ns.id_judul_skripsi) = '$enkrip'
    LIMIT 1";

$r = $conn->query($sql);
if ($r === false) {
    echo "\njudul_berita_acara query error: " . $conn->error . "\n";
} else {
    $row = $r->fetch_object();
    echo "\nRow: " . ($row ? $row->nipd : 'NULL') . "\n";
}
