<?php
$mysqli = new mysqli("localhost", "root", "", "portal_wbi_server_2");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$res = $mysqli->query("SELECT * FROM mahasiswa_pt WHERE nipd = '2301010048'");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    print_r($row);
    
    // Check siakad_mahasiswa
    $res2 = $mysqli->query("SELECT * FROM siakad_mahasiswa WHERE id_pd = '" . $row['id_pd'] . "'");
    if ($res2 && $res2->num_rows > 0) {
        print_r($res2->fetch_assoc());
    } else {
        echo "Found in mahasiswa_pt, but NOT in siakad_mahasiswa!\n";
    }
} else {
    echo "NIM 2301010048 NOT FOUND in mahasiswa_pt\n";
    
    // Check using LIKE just in case
    $res3 = $mysqli->query("SELECT * FROM mahasiswa_pt WHERE nipd LIKE '%2301010048%'");
    if ($res3 && $res3->num_rows > 0) {
        echo "Found using LIKE:\n";
        while($r = $res3->fetch_assoc()) print_r($r);
    }
}

// Just to be sure, check the main mahasiswa table if it exists
$res4 = $mysqli->query("SELECT * FROM mahasiswa WHERE nipd = '2301010048' OR id_pd IN (SELECT id_pd FROM mahasiswa_pt WHERE nipd='2301010048')");
if ($res4 && $res4->num_rows > 0) {
    echo "Found in `mahasiswa` table:\n";
    while($r = $res4->fetch_assoc()) print_r($r);
}

$mysqli->close();
?>
