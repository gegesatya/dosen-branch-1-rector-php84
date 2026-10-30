<?php
$c = file_get_contents('c:\laragon\www\wbi\server\dosen\application\views\cetak\pdf_sidang.php'); 
$lines = explode("\n", $c); 
foreach($lines as $i => $l) { 
  if (strpos($l, 'Penguji 3') !== false) { 
    echo "Line ".($i+1).": ".$l."\n"; 
    echo "  Prev: ".trim($lines[$i-1])."\n"; 
    echo "  Next: ".trim($lines[$i+1])."\n\n"; 
  } 
}
