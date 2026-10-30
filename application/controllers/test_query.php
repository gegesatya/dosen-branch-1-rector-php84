<?php
$content = file_get_contents('c:\laragon\www\wbi\wbi-lis\application\controllers\C_rencana_pertemuan.php');
if (preg_match('/function course\s*\((.*?)\)/', $content, $m)) {
    print_r($m[0]);
} else {
    echo "Not found";
}
