<?php
define('BASEPATH', true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_tracking, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6 FROM simlapkerma_tracking_kegiatan";
$result = $conn->query($sql);

$found = 0;

while ($row = $result->fetch_assoc()) {
    foreach (['kegiatan_hari_1', 'kegiatan_hari_2', 'kegiatan_hari_3', 'kegiatan_hari_4', 'kegiatan_hari_5', 'kegiatan_hari_6'] as $field) {
        $val = $row[$field];
        if (empty($val)) continue;
        
        if (strpos($val, 'pointer-events-auto') !== false || strpos($val, 'data-turn-id') !== false || strpos($val, 'data-turn="assistant"') !== false || strpos($val, 'data-conversation-screenshot-content') !== false) {
            echo "Found garbage in id_tracking: " . $row['id_tracking'] . " field: " . $field . "\n";
            $found++;
        }
    }
}

echo "Total garbage found: $found\n";
$conn->close();
