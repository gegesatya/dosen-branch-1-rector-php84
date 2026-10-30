<?php
$file = 'c:/laragon/www/wbi/server/dosen/application/views/cetak/pdf_sidang.php';
$lines = file($file);
$lines[801] = "              <?php if (!empty(\$penguji_3)): ?>\n" . $lines[801];
$lines[805] = $lines[805] . "              <?php endif; ?>\n";
file_put_contents($file, implode('', $lines));
echo "Replaced successfully!";
