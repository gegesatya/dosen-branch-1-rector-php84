<?php
$f = 'c:/laragon/www/server/dosen/application/config/constants.php';
$c = file_get_contents($f);
$c = str_replace(chr(0), '', $c);
$c = str_replace(chr(255).chr(254), '', $c);
file_put_contents($f, $c);
echo "Cleaned!";
