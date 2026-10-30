<?php
$file = 'c:/laragon/www/wbi/server/dosen/application/views/cetak/pdf_sidang.php';
$lines = file($file);

$start = 181;
// We need to find where the dynamic loop ends.
// In our previous replacement, we added "<?php \n $idx++; \n endforeach; \n ?>"
// Let's find the line with "endforeach;"
$end = -1;
for ($i = 181; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'endforeach;') !== false) {
        $end = $i + 2; // to include the closing ?>
        break;
    }
}

if ($end == -1) {
    die("Could not find endforeach");
}

function get_penguji_block($idx) {
    return <<<'EOD'
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN UJIAN TUGAS AKHIR</p>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower($judul->nm_pd)); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <div class="row">
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Kriteria Penilaian</th>
              <th class="text-center">Bobot<br>(%)</th>
              <th class="text-center">Skor</th>
              <th class="text-center">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1.</td>
              <td>Bobot / Kualitas Laporan Hasil (Manuskrip)<br>
                a. Kekinian Masalah yang Dihadapi (kejelasan dan relevansi masalah dengan hasil penelitian yang dicapai).<br>
                b. Relevansi Metodologi dalam Perumusan Hasil Penelitian<br>
                c. Kejelasan Rumusan Hasil Menguraikan Jawaban Permasalahan Penelitian
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_a; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_IDX->sidang_nilai_a * 1; ?></td>
            </tr>
            <tr>
              <td>2.</td>
              <td>Sistematika Penulisan (kesesuaian penulisan dengan panduan yang ditetapkan).</td>
              <td class="text-center">15</td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_b; ?></td>
              <td class="text-center"><?= 0.15 * $nilai_penguji_IDX->sidang_nilai_b * 1; ?></td>
            </tr>
            <tr>
              <td>3.</td>
              <td>Penguasaan Materi dan Argumentasi (kemampuan menjelaskan dan menjawab pertanyaan-pertanyaan terkait penelitian)</td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_c; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_IDX->sidang_nilai_c * 1; ?></td>
            </tr>
            <tr>
              <td>4.</td>
              <td>Kemampuan / Potensi Luaran<br>
                (kualitas luaran, potensi aplikasi luaran pada inovasi, implementasi luaran oleh mitra TA)
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_d; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_IDX->sidang_nilai_d * 1; ?></td>
            </tr>
            <tr>
              <td>5.</td>
              <td>Sikap dan Penampilan</td>
              <td class="text-center">10</td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_e; ?></td>
              <td class="text-center"><?= 0.1 * $nilai_penguji_IDX->sidang_nilai_e * 1; ?></td>
            </tr>
            <tr>
              <td></td>
              <td>Total</td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"><?= $nilai_penguji_IDX->sidang_nilai_akhir * 1; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
EOD;
}

$b1 = str_replace('IDX', 1, get_penguji_block(1));
$b2 = str_replace('IDX', 2, get_penguji_block(2));
$b3 = str_replace('IDX', 3, get_penguji_block(3));

$replacement = $b1 . <<<'EOD'

      <small><br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 1,<br>
                <img src="<?= $qr_penguji_1; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_1->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_1->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
EOD;

$replacement .= "\n" . $b2 . <<<'EOD'

      <small>Keterangan:<br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 2,<br>
                <img src="<?= $qr_penguji_2; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_2->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_2->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
EOD;

$replacement .= "\n" . $b3 . <<<'EOD'

      <small>Keterangan:<br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 3,<br>
                <img src="<?= $qr_penguji_3; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_3->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_3->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
EOD;

$new_lines = array_slice($lines, 0, $start);
$new_lines[] = $replacement . "\n";
$new_lines = array_merge($new_lines, array_slice($lines, $end));

file_put_contents($file, implode("", $new_lines));
echo "Successfully restored top part!";
