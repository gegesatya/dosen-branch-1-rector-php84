<?php
define('BASEPATH', true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$dbname = $db['default']['database'];

$conn = new mysqli($host, $user, $pass, $dbname);

$sql = "SELECT id_tracking, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6 FROM simlapkerma_tracking_kegiatan";
$result = $conn->query($sql);

$fixed_count = 0;

while ($row = $result->fetch_assoc()) {
    $updates = [];
    foreach (['kegiatan_hari_1', 'kegiatan_hari_2', 'kegiatan_hari_3', 'kegiatan_hari_4', 'kegiatan_hari_5', 'kegiatan_hari_6'] as $field) {
        $val = $row[$field];
        if (empty($val)) continue;
        
        if (strpos($val, 'pointer-events-auto') !== false || strpos($val, 'data-turn-id') !== false) {
            $js = json_decode($val, true);
            if (is_array($js)) {
                $changed = false;
                foreach (['pagi', 'siang', 'sore'] as $time) {
                    if (isset($js[$time]['teks'])) {
                        $teks = $js[$time]['teks'];
                        
                        // Regex to remove the garbage text
                        $new_teks = preg_replace('/\\*\\]:pointer-events-auto.*?"assistant">/s', '', $teks);
                        
                        // If there are other variations, just remove anything that looks like "R6Vx5W_threadScrollVars" until ">"
                        $new_teks = preg_replace('/R6Vx5W_threadScrollVars.*?data-turn="assistant">/s', '', $new_teks);

                        if ($new_teks !== $teks) {
                            $js[$time]['teks'] = $new_teks;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $updates[$field] = json_encode($js);
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

echo "Successfully fixed $fixed_count rows of ChatGPT garbage!\n";
$conn->close();
