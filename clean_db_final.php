<?php
define("BASEPATH", true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db["default"]["hostname"];
$user = $db["default"]["username"];
$pass = $db["default"]["password"];
$dbname = $db["default"]["database"];

$conn = new mysqli($host, $user, $pass, $dbname);

function extract_teks_recursive($val) {
    if (is_array($val)) {
        if (isset($val["teks"])) {
            return extract_teks_recursive($val["teks"]);
        }
        return "";
    }
    
    if (is_string($val)) {
        $val = trim($val);
        if (strpos($val, "{") === 0) {
            $clean_raw = preg_replace("/\\\\([^\"\\\\\/bfnrtu])/", "$1", $val);
            $clean_raw = preg_replace("/[\x00-\x1F\x7F]/", "", $clean_raw);
            $last_brace = strrpos($clean_raw, "}");
            if ($last_brace !== false) {
                $clean_raw = substr($clean_raw, 0, $last_brace + 1);
            }
            $parsed = @json_decode($clean_raw, true);
            if (is_array($parsed)) {
                return extract_teks_recursive($parsed);
            }
        }
        return $val;
    }
    return "";
}

$sql = "SELECT id_tracking, nipd, week, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6 FROM simlapkerma_tracking_kegiatan";
$result = $conn->query($sql);

$fixed_count = 0;

while ($row = $result->fetch_assoc()) {
    $needs_update = false;
    $update_data = [];
    
    for ($i = 1; $i <= 6; $i++) {
        $col = "kegiatan_hari_" . $i;
        $val = $row[$col];
        
        if (empty($val) || trim($val) === "<p><br></p>") continue;
        
        $clean_raw = preg_replace("/\\\\([^\"\\\\\/bfnrtu])/", "$1", $val);
        $clean_raw = preg_replace("/[\x00-\x1F\x7F]/", "", $clean_raw);
        $last_brace = strrpos($clean_raw, "}");
        if ($last_brace !== false) {
            $clean_raw = substr($clean_raw, 0, $last_brace + 1);
        }
        
        $js = @json_decode($clean_raw, true);
        
        if (is_array($js)) {
            $new_js = $js;
            $changed_slot = false;
            
            foreach (["pagi", "siang", "sore"] as $slot) {
                if (isset($js[$slot])) {
                    if (is_array($js[$slot]) && isset($js[$slot]["teks"])) {
                        $teks_val = $js[$slot]["teks"];
                        if (is_string($teks_val) && strpos(trim($teks_val), "{") === 0) {
                            $extracted = extract_teks_recursive($teks_val);
                            if ($extracted !== $teks_val) {
                                $new_js[$slot]["teks"] = $extracted;
                                $changed_slot = true;
                                $needs_update = true;
                            }
                        }
                    } else if (is_string($js[$slot]) && strpos(trim($js[$slot]), "{") === 0) {
                        $extracted = extract_teks_recursive($js[$slot]);
                        if ($extracted !== $js[$slot]) {
                            $new_js[$slot] = $extracted;
                            $changed_slot = true;
                            $needs_update = true;
                        }
                    }
                }
            }
            
            if ($changed_slot) {
                $update_data[$col] = json_encode($new_js);
            }
        }
    }
    
    if ($needs_update && !empty($update_data)) {
        $set_clause = [];
        $types = "";
        $params = [];
        
        foreach ($update_data as $col => $new_val) {
            $set_clause[] = "$col = ?";
            $types .= "s";
            $params[] = $new_val;
        }
        
        $types .= "i";
        $params[] = $row["id_tracking"];
        
        $sql_update = "UPDATE simlapkerma_tracking_kegiatan SET " . implode(", ", $set_clause) . " WHERE id_tracking = ?";
        $stmt = $conn->prepare($sql_update);
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        
        echo "Fixed ID " . $row["id_tracking"] . " (NIPD: " . $row["nipd"] . " Week: " . $row["week"] . ")\n";
        $fixed_count++;
    }
}

echo "Total rows fixed (double JSON removed): $fixed_count\n";
$conn->close();
?>
