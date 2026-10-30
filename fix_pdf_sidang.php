<?php
$file = 'c:\laragon\www\wbi\server\dosen\application\views\cetak\pdf_sidang.php';
$content = file_get_contents($file);

// Find Komisi Penguji
$pos = strpos($content, '<span style="font-weight: bold;">Komisi Penguji</span></div>');
if ($pos === false) die("Not found Komisi Penguji\n");

// Find formulir dibuat rangkap 2
$pos2 = strpos($content, '<p>Formulir dibuat rangkap 2 untuk:</p>');
if ($pos2 === false) die("pos2 not found\n");

$header = substr($content, 0, $pos);
$footer = substr($content, $pos2);

$new_middle = '<span style="font-weight: bold;">Komisi Penguji</span></div>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 1/Pembimbing</p><br>
                <img src="<?= $qr_penguji_1; ?>"><br>
                <?= $penguji_1->nama_lengkap . \'<br>NIDN. \' . $penguji_1->nidn; ?>
              </td>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 2</p><br>
                <img src="<?= $qr_penguji_2; ?>"><br>
                <?= $penguji_2->nama_lengkap . \'<br>NIDN. \' . $penguji_2->nidn; ?>
              </td>
              <?php if (!empty($penguji_3)): ?>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 3</p><br>
                <img src="<?= $qr_penguji_3; ?>"><br>
                <?= $penguji_3->nama_lengkap . \'<br>NIDN. \' . $penguji_3->nidn; ?>
              </td>
              <?php endif; ?>
            </tr>
          </tbody>
        </table>
        ';

file_put_contents($file, $header . $new_middle . $footer);
echo "Fixed Komisi Penguji!\n";
