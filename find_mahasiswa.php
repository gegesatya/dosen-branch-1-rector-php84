<?php
$mysqli = new mysqli("localhost", "root", "", "portal_wbi_server_2");

$tables = [];
$res = $mysqli->query("SHOW TABLES LIKE '%mahasiswa%'");
while($r = $res->fetch_array()) {
    $tables[] = $r[0];
}

$id_pd = '7f240f18-5085-11ee-bf10-3d11a0edc40e';
foreach ($tables as $t) {
    $res = $mysqli->query("SHOW COLUMNS FROM `$t` LIKE 'id_pd'");
    if ($res && $res->num_rows > 0) {
        $check = $mysqli->query("SELECT * FROM `$t` WHERE id_pd = '$id_pd'");
        if ($check && $check->num_rows > 0) {
            echo "Found in $t!\n";
            print_r($check->fetch_assoc());
        }
    }
    
    // Check nipd just in case
    $res2 = $mysqli->query("SHOW COLUMNS FROM `$t` LIKE 'nipd'");
    if ($res2 && $res2->num_rows > 0) {
        $check2 = $mysqli->query("SELECT * FROM `$t` WHERE nipd = '2301010048'");
        if ($check2 && $check2->num_rows > 0) {
            echo "Found nipd in $t!\n";
            // don't print_r twice if it's already printed
        }
    }
}
$mysqli->close();
?>
