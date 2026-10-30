<?php
define("BASEPATH", true);
require_once "c:\\laragon\\www\\wbi\\server\\dosen\\application\\config\\database.php";

$host = $db["default"]["hostname"];
$user = $db["default"]["username"];
$pass = $db["default"]["password"];
$dbname = $db["default"]["database"];

$conn = new mysqli($host, $user, $pass, $dbname);

// Fungsi untuk mengekstrak teks dari JSON ganda/bersarang
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

$sql = "SELECT id_tracking, nipd, week, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6, pengetahuan_baru, keterampilan_baru, tujuan_internship, catatan_lain FROM simlapkerma_tracking_kegiatan";
$result = $conn->query($sql);

$fixed_count = 0;
$allowed_tags = '<p><br><br/><ul><li><ol><strong><b><i><em><u><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6>';
$chatgpt_indicators = ['pointer-events-auto', 'data-turn="assistant"', 'data-turn-id', 'data-conversation-screenshot-content'];

while ($row = $result->fetch_assoc()) {
    $needs_update = false;
    $update_data = [];
    
    for ($i = 1; $i <= 6; $i++) {
        $col = "kegiatan_hari_" . $i;
        $val = $row[$col];
        
        if (empty($val) || trim($val) === "<p><br></p>") continue;
        
        // Cek apakah ada indikator ChatGPT
        $is_chatgpt = false;
        foreach ($chatgpt_indicators as $indicator) {
            if (strpos($val, $indicator) !== false) {
                $is_chatgpt = true;
                break;
            }
        }
        
        $js = @json_decode($val, true);
        
        if (!is_array($js)) {
            $clean_raw = preg_replace("/\\\\([^\"\\\\\/bfnrtu])/", "$1", $val);
            // Clean null bytes without triggering warning in PHP
            $clean_raw = str_replace(chr(0), '', $clean_raw);
            $clean_raw = preg_replace("/[\x01-\x1F\x7F]/", "", $clean_raw);
            $last_brace = strrpos($clean_raw, "}");
            if ($last_brace !== false) {
                $clean_raw = substr($clean_raw, 0, $last_brace + 1);
            }
            $js = @json_decode($clean_raw, true);
        }
        
        if (is_array($js)) {
            $new_js = $js;
            $changed_slot = false;
            
            foreach (["pagi", "siang", "sore"] as $slot) {
                if (isset($js[$slot])) {
                    $teks_val = "";
                    if (is_array($js[$slot]) && isset($js[$slot]["teks"])) {
                        $teks_val = $js[$slot]["teks"];
                    } else if (is_string($js[$slot])) {
                        $teks_val = $js[$slot];
                    }
                    
                    if (is_string($teks_val)) {
                        $extracted = $teks_val;
                        
                        // 1. Ekstrak JSON bersarang
                        if (strpos(trim($extracted), "{") === 0) {
                            $extracted = extract_teks_recursive($extracted);
                        }

                        // Hapus karakter backslash (\) yang nyasar
                        $extracted = str_replace('\\', '', $extracted);
                        
                        // 2. Jika BUKAN ChatGPT, bersihkan tag HTML berbahaya
                        if (!$is_chatgpt) {
                            $extracted = strip_tags($extracted, $allowed_tags);
                        }

                        if ($extracted !== $teks_val) {
                            if (is_array($js[$slot])) {
                                $new_js[$slot]["teks"] = $extracted;
                            } else {
                                $new_js[$slot] = $extracted;
                            }
                            $changed_slot = true;
                        }
                    }
                }
            }
            
            if ($changed_slot) {
                $update_data[$col] = json_encode($new_js);
                $needs_update = true;
            }
        } else if (strpos(trim($val), "{") !== 0 && !$is_chatgpt) {
            // Jika benar-benar BUKAN JSON (format lama) dan bukan ChatGPT, bersihkan HTML-nya
            $clean_val = strip_tags($val, $allowed_tags);
            $clean_val = str_replace('\\', '', $clean_val);
            if ($clean_val !== $val) {
                $update_data[$col] = $clean_val;
                $needs_update = true;
            }
        }
    }
    
    // Cek reflection fields
    foreach (["pengetahuan_baru", "keterampilan_baru", "tujuan_internship", "catatan_lain"] as $f) {
        $val = $row[$f];
        if (!empty($val)) {
            $is_chatgpt = false;
            foreach ($chatgpt_indicators as $indicator) {
                if (strpos($val, $indicator) !== false) {
                    $is_chatgpt = true;
                    break;
                }
            }
            
            $extracted = extract_teks_recursive($val);
            $extracted = str_replace('\\', '', $extracted);

            if (!$is_chatgpt) {
                $extracted = strip_tags($extracted, $allowed_tags);
            }
            
            if ($extracted !== $val) {
                $update_data[$f] = $extracted;
                $needs_update = true;
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

echo "Total rows cleaned and fixed: $fixed_count\n";
$conn->close();
?>
