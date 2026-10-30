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
$allowed_tags = '<p><br><ul><ol><li><b><i><u><span><a><table><tr><td><th><tbody><thead>';

while ($row = $result->fetch_assoc()) {
    $updates = [];
    foreach (['kegiatan_hari_1', 'kegiatan_hari_2', 'kegiatan_hari_3', 'kegiatan_hari_4', 'kegiatan_hari_5', 'kegiatan_hari_6'] as $field) {
        $val = $row[$field];
        if (empty($val)) continue;
        
        if (strpos($val, 'data-conversation-screenshot-content') !== false || strpos($val, 'message-author-role') !== false || strpos($val, 'pointer-events-auto') !== false || strpos($val, 'data-turn="assistant"') !== false) {
            
            $val_trimmed = trim($val);
            if (strpos($val_trimmed, '{') === 0) {
                $js = json_decode($val, true);
                if (is_array($js)) {
                    $changed = false;
                    foreach (['pagi', 'siang', 'sore'] as $time) {
                        if (isset($js[$time]['teks'])) {
                            $teks = $js[$time]['teks'];
                            $new_teks = strip_tags($teks, $allowed_tags);
                            $new_teks = preg_replace('/\\*\\]:pointer-events-auto.*?"assistant">/s', '', $new_teks);
                            $new_teks = preg_replace('/\\*\\]:pointer-events-auto.*?(Membantu|Melakukan|Kegiatan|Menghadiri|Mencatat)/i', '$1', $new_teks);
                            
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
            } else {
                // It is a raw HTML string (old format)
                $new_teks = strip_tags($val, $allowed_tags);
                $new_teks = preg_replace('/\\*\\]:pointer-events-auto.*?"assistant"/s', '', $new_teks);
                $new_teks = preg_replace('/\\*\\]:pointer-events-auto.*?(Membantu|Melakukan|Kegiatan|Menghadiri|Mencatat)/i', '$1', $new_teks);
                
                // Sometimes the tag regex leaves trailing `>`
                $new_teks = preg_replace('/xss=removed>/i', '>', $new_teks);
                
                if ($new_teks !== $val) {
                    $updates[$field] = $new_teks;
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

echo "Successfully cleaned $fixed_count rows of ChatGPT HTML tags!\n";
$conn->close();
