<?php
$file = 'c:/laragon/www/wbi/server/dosen/application/views/cetak/pdf_sidang.php';
$lines = file($file);

$replacement = <<< 'EOD'
        <div class="text-center"><img src="<?= $qr_kaprodi; ?>"></div>
        <div class="text-center"><?= $nama_kaprodi->nm_pejabat_khs_1; ?></div>
        <div class="text-center">NIDN. <?= $nama_kaprodi->nidn_pejabat_khs_1; ?></div>
      </div>
      <div style="page-break-after: always;"></div>
      <?php
      $nama = $nidn = $titel = '';
      $i = 1;
      foreach ($usulan_semhas as $key => $value) {
        if ($value->id_penilai == $penguji_1->id_ptk) {
          $nama = $penguji_1->nama_lengkap;
          $nidn = $penguji_1->nidn;
          $titel = 'Penguji 1/Pembimbing,';
        } else if ($value->id_penilai == $penguji_2->id_ptk) {
          $nama = $penguji_2->nama_lengkap;
          $nidn = $penguji_2->nidn;
          $titel = 'Penguji 2,';
        } else if (!empty($penguji_3) && $value->id_penilai == $penguji_3->id_ptk) {
          $nama = $penguji_3->nama_lengkap;
          $nidn = $penguji_3->nidn;
          $titel = 'Penguji 3,';
        }
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="6" class="text-center">
                <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PERMINTAAN REVISI UJIAN TUGAS AKHIR</p>
              </td>
            </tr>
            <tr>
              <td style="width:50px">Nama</td>
              <td style="width: 10px;">:</td>
              <td><?= ucwords(strtolower($judul->nm_pd)); ?></td>
              <td rowspan="2" style="width:90px">Program Studi</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nm_lemb; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nipd; ?></td>
            </tr>
          </tbody>
        </table>
        <div class="custom">URAIAN REVISI</div>
        <div class="custom-3">
          <?php
          echo  nl2br($value->sidang_uraian_revisi)  . '</p><br>';
EOD;

array_splice($lines, 815, 2, [$replacement . "\n"]);
file_put_contents($file, implode('', $lines));
echo "Fixed!";
