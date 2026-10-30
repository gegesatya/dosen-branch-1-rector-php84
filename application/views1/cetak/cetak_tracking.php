<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ringkasan Laporan Mingguan <?= $nm_pd; ?></title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

  <style type="text/css">
    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 13px !important;
      margin-bottom: 10px
    }

    .table-black th,
    .table-black td {
      border-top: 1px solid #000 !important;
      border-bottom: 1px solid #000 !important;
      padding: 4px !important;
      font-size: 13px !important;
    }

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }
  </style>

</head>

<body style="margin-left: 50px">
  
  <div class="wrapper">
    
    <section class="invoice">
      <div class="form-container" style="margin-top: 100px">
        <div class="text-right">
          <h2>LAPORAN MINGGUAN</h2>
          <br>
          <h3>PROGRAM INTERNSHIP</h3>
          <h3><?= !empty($lokasi) ? $lokasi : '<Isi Nama Perusahaan>'; ?></h3>
        </div>
        <div class="text-right" style="margin-top: 300px; margin-bottom: 50px">
          <h3><?= $nm_pd; ?></h3>
          <h3><?= $nipd; ?></h3>
          <br>
          <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="250px" />
          <br>
          <br>
          <h4>PROGRAM STUDI <?= strtoupper($nm_lemb); ?></h4>
          <h3>POLITEKNIK WILMAR BISNIS INDONESIA</h3>
          <h4><?= !empty($tahun_magang) ? $tahun_magang : '<Isi Tahun Magang>'; ?></h4>
        </div>
        <pagebreak />
        <?php
        foreach ($data as $key => $d) {
        ?>
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td colspan="4" class="text-left">
                  <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">RINGKASAN LAPORAN MINGGUAN </h4>
                </td>
              </tr>
              <tr>
                <td colspan="4" class="text-left">
                  <h5 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">PROGRAM INTERNSHIP PRODI <?= strtoupper($nm_lemb); ?></h5>
                </td>
              </tr>
              <tr>
                <td colspan="4" class="text-left">&nbsp;</td>
              </tr>

              <tr>
                <td width="250px"><strong>Nama Mahasiswa</strong></td>
                <td style="width: 5px;">:</td>
                <td><?= $d->nm_pd; ?></td>
              </tr>
              <tr>
                <td><strong>NIM</strong></td>
                <td style="width: 5px;">:</td>
                <td><?= $d->nipd; ?></td>
              </tr>
              <tr>
                <td><strong>Periode Laporan</strong></td>
                <td style="width: 5px;">:</td>
                <td><?= $d->periode_laporan; ?></td>
              </tr>

            </tbody>
          </table>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Hari Ke</th>
                <th class="text-center" style="background-color: rgba(126, 173, 197, 1);">Kegiatan yang dilakukan</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="width: 60px;">1.</td>
                <td><?= $d->kegiatan_hari_1; ?></td>
              </tr>
              <tr>
                <td style="width: 60px;">2.</td>
                <td><?= $d->kegiatan_hari_2; ?></td>
              </tr>
              <tr>
                <td style="width: 60px;">3.</td>
                <td><?= $d->kegiatan_hari_3; ?></td>
              </tr>
              <tr>
                <td style="width: 60px;">4.</td>
                <td><?= $d->kegiatan_hari_4; ?></td>
              </tr>
              <tr>
                <td style="width: 60px;">5.</td>
                <td><?= $d->kegiatan_hari_5; ?></td>
              </tr>
              <tr>
                <td style="width: 60px;">6.</td>
                <td><?= $d->kegiatan_hari_6; ?></td>
              </tr>
            </tbody>
          </table>
          <br>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Pengetahuan baru yang diperoleh</th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->pengetahuan_baru; ?></td>
              </tr>

            </tbody>
          </table>
          <br>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Keterampilan baru yang diperoleh</th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->keterampilan_baru; ?></td>
              </tr>

            </tbody>
          </table>
          <br>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Tujuan Internship yang dicapai <small>(Sesuaikan dengan Usulan Kegiatan Internship)</small></th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->tujuan_internship; ?></td>
              </tr>

            </tbody>
          </table>
          <br>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Catatan Lain-lain <small>(berikan catatan yang dianggap perlu / kejadian penting selama minggu ini)</small></th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->catatan_lain; ?></td>
              </tr>

            </tbody>
          </table>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Data Pendukung (Link Google Drive)</th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->link_google_drive; ?></td>
              </tr>

            </tbody>
          </table>
          <table class="table table-black">
            <thead>
              <tr>
                <th style="background-color: rgba(126, 173, 197, 1);">Review Dosen Pembimbing Lapangan</th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="padding-left: 20px;"><?= $d->review_dpl; ?></td>
              </tr>

            </tbody>
          </table>
        <?php
                    if ($key < count($data) - 1) {
            echo '<pagebreak />';
          }
        }
        ?>



      </div>
    </section>

    
  </div>
  
</body>

</html>