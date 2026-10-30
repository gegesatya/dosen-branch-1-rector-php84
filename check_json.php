<?php
define('BASEPATH', true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

$conn = new mysqli($host, $user, $pass, $dbname);

$sql = "SELECT id_tracking, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6 FROM simlapkerma_tracking_kegiatan WHERE id_tracking=3764 OR id_tracking=3356 OR id_tracking=3766 LIMIT 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    foreach (['kegiatan_hari_4'] as $field) {
        $val = $row[$field];
        if (empty($val)) continue;
        
        $val_trimmed = trim($val);
        if (strpos($val_trimmed, '{') === 0) {
            $js = json_decode($val, true);
            if (!is_array($js)) {
                echo "Raw string hex dump (last 100 chars):\n";
                $sub = substr($val, -100);
                echo $sub . "\n";
                for($i=0; $i<strlen($sub); $i++) {
                    echo bin2hex($sub[$i]) . " ";
                }
                echo "\n";
            }
        }
    }
}
