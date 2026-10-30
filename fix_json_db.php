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

$fixed_count = 0;

while ($row = $result->fetch_assoc()) {
    $updates = [];
    foreach (['kegiatan_hari_1', 'kegiatan_hari_2', 'kegiatan_hari_3', 'kegiatan_hari_4', 'kegiatan_hari_5', 'kegiatan_hari_6'] as $field) {
        $val = $row[$field];
        if (empty($val)) continue;
        
        $val_trimmed = trim($val);
        if (strpos($val_trimmed, '{') === 0) {
            $js = json_decode($val, true);
            if (!is_array($js)) {
                // Try fixing invalid escapes in a loop
                $fixed_val = $val;
                do {
                    $prev = $fixed_val;
                    $fixed_val = preg_replace('/\\\\([^\\\\\"\/bfnrtu])/i', '$1', $fixed_val);
                    // Also strip literal control characters which could break json_decode
                    $fixed_val = preg_replace('/[\x00-\x1F]/', '', $fixed_val);
                } while ($prev !== $fixed_val);
                
                $js2 = json_decode($fixed_val, true);
                if (is_array($js2)) {
                    $updates[$field] = $fixed_val;
                } else {
                    echo "Could not fix id_tracking: " . $row['id_tracking'] . " field: " . $field . " Error: " . json_last_error_msg() . "\n";
                }
            }
        }
    }
    
    if (!empty($updates)) {
        $set_parts = [];
        $params = [];
        $types = "";
        foreach ($updates as $f => $v) {
            $set_parts[] = "$f = ?";
            $params[] = $v;
            $types .= "s";
        }
        $params[] = $row['id_tracking'];
        $types .= "i";
        
        $stmt = $conn->prepare("UPDATE simlapkerma_tracking_kegiatan SET " . implode(", ", $set_parts) . " WHERE id_tracking = ?");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $fixed_count++;
    }
}

echo "Successfully fixed $fixed_count rows!\n";
$conn->close();
