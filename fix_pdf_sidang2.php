<?php
$file = 'c:\laragon\www\wbi\server\dosen\application\views\cetak\pdf_sidang.php';
$content = file_get_contents($file);

// The block to replace
$bad_block = '            <?php } ?>
            <?php endif; ?>
      <div class="row">
        <div class="column right">
          Pada hari ini: <?= $berita_acara->sidang_hari; ?>, tanggal <?= tgl_indo($berita_acara->sidang_tanggal); ?> telah diselenggarakan Ujian Tugas Akhir di ruang Sidang Tugas Akhir Politeknik Wilmar Bisnis Indonesia yang ke -1 (satu).';

$good_block = '            <?php } ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="text-center">Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br><span style="font-weight: bold;">Komisi Penguji</span></div>
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
        <p>Formulir dibuat rangkap 2 untuk:</p>
        <ol>
          <li>Mahasiswa</li>
          <li>Program Studi</li>
        </ol>
      </div>

      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">BERITA ACARA UJIAN TUGAS AKHIR</p>
      <div class="row">
        <div class="column right">
          Pada hari ini: <?= $berita_acara->sidang_hari; ?>, tanggal <?= tgl_indo($berita_acara->sidang_tanggal); ?> telah diselenggarakan Ujian Tugas Akhir di ruang Sidang Tugas Akhir Politeknik Wilmar Bisnis Indonesia yang ke -1 (satu).';

$content = str_replace($bad_block, $good_block, $content);
file_put_contents($file, $content);
echo "Restored all missing parts!\n";
