<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Nilai <?= $nm_mk; ?></title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <style type="text/css">
    body {
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 13px;
      line-height: 1.42857143;
      color: #333;
      background-color: #fff;
    }

    .table {
      border-collapse: collapse !important;
    }

    .table td,
    .table th {
      background-color: #fff !important;
    }

    .table-bordered th,
    .table-bordered td {
      border: 1px solid #ddd !important;
    }


    .table {
      width: 100%;
      max-width: 100%;
      margin-bottom: 0px;
    }

    .table>thead>tr>th,
    .table>tbody>tr>th,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>tbody>tr>td,
    .table>tfoot>tr>td {
      padding: 8px !important;
      line-height: 1.42857143;
      vertical-align: top;
      border-top: 1px solid #ddd;
    }

    .table>thead>tr>th {
      vertical-align: bottom;
      border-bottom: 2px solid #ddd;
    }

    .table>caption+thead>tr:first-child>th,
    .table>colgroup+thead>tr:first-child>th,
    .table>thead:first-child>tr:first-child>th,
    .table>caption+thead>tr:first-child>td,
    .table>colgroup+thead>tr:first-child>td,
    .table>thead:first-child>tr:first-child>td {
      border-top: 0;
    }

    .table>tbody+tbody {
      border-top: 2px solid #ddd;
    }

    .table .table {
      background-color: #fff;
    }

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 5px !important;
      font-size: 12px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 6px !important;
      font-size: 11px !important;
    }

    hr {
      margin-top: 1px;
      margin-bottom: 10px;
      border: 0;
      border-top: 1px solid #000;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .text-justify {
      text-align: justify;
    }

    .text-nowrap {
      white-space: nowrap;
    }

    .text-lowercase {
      text-transform: lowercase;
    }

    .text-uppercase {
      text-transform: uppercase;
    }

    .text-capitalize {
      text-transform: capitalize;
    }

    .wrapper {
      min-height: 100%;
      position: static;
      overflow: hidden;
    }

    .wrapper:before,
    .wrapper:after {
      content: " ";
      display: table;
    }

    .wrapper:after {
      clear: both;
    }

    .invoice {
      position: relative;
      background: #fff;
      border: 1px solid #f4f4f4;
      padding: 20px;
      margin: 10px 25px;
    }

    .invoice-title {
      margin-top: 0;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .h1,
    .h2,
    .h3,
    .h4,
    .h5,
    .h6 {
      font-family: inherit;
      font-weight: 500;
      line-height: 1.1;
      color: inherit;
    }

    .text-ku {
      display: inline-block;
      border: solid 1px #000;
      min-height: 10px;
      width: 300px;
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
  <div class="wrapper">
    <?php
    $colspan = 0;
    if ($unjuk_kinerja <> 0) {
      $colspan += 1;
    }
    if ($proyek <> 0) {
      $colspan += 1;
    }
    if ($portofolio <> 0) {
      $colspan += 1;
    }
    if ($produk <> 0) {
      $colspan += 1;
    }
    if ($presentasi <> 0) {
      $colspan += 1;
    }
    ?>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td style="width: 30% !important;">DOSEN</td>
          <td style="width: 10px !important;">:</td>
          <td> <?= $nm_ptk; ?></td>

        </tr>
        <tr>
          <td>TAHUN AJARAN</td>
          <td>:</td>
          <td><?= $nm_smt; ?></td>
        </tr>
        <tr>
          <td>JADWAL</td>
          <td>:</td>
          <td> <?= $jadwal_nya; ?></td>
        </tr>
        <tr>
          <td>KELAS/PRODI</td>
          <td>:</td>
          <td> <?= $prodi; ?></td>
        </tr>

      </tbody>
    </table>
    <br>
    <table class="table table-black">
      <thead>
        <tr>
          <th rowspan="3" style="vertical-align: middle;">No.</th>
          <th rowspan="3" style="vertical-align: middle;">NIM | Nama</th>
          <th rowspan="2" style="vertical-align: middle;">Sikap</th>
          <th colspan="4">Pengetahuan</th>
          <th colspan="<?= $colspan; ?>">Keterampilan</th>
          <th rowspan="3" style="vertical-align: middle;">Angka</th>
          <th rowspan="3" style="vertical-align: middle; background-color: #ddd">Mutu</th>
          <th rowspan="3" style="vertical-align: middle;">Indeks</th>
        </tr>
        <tr>
          <th>QUI</th>
          <th>TTS</th>
          <th>UTS</th>
          <th>UAS</th>

          <?php
          if ($unjuk_kinerja <> 0) {
          ?>
            <th>UKN</th>
          <?php
          }
          ?>
          <?php
          if ($proyek <> 0) {
          ?>
            <th>PRY</th>
          <?php
          }
          ?>
          <?php
          if ($portofolio <> 0) {
          ?>
            <th>POR</th>
          <?php
          }
          ?>
          <?php
          if ($produk <> 0) {
          ?>
            <th>PRD</th>
          <?php
          }
          ?>
          <?php
          if ($presentasi <> 0) {
          ?>
            <th>PRE</th>
          <?php
          }
          ?>

        </tr>
        <tr>
          <th><?= $sikap; ?> %</th>
          <th><?= $quiz; ?> %</th>
          <th><?= $tugas_terstruktur; ?> %</th>
          <th><?= $uts; ?> %</th>
          <th><?= $uas; ?> %</th>
          <?php
          if ($unjuk_kinerja <> 0) {
          ?>
            <th><?= $unjuk_kinerja; ?> %</th>
          <?php
          }
          ?>
          <?php
          if ($proyek <> 0) {
          ?>
            <th><?= $proyek; ?> %</th>
          <?php
          }
          ?>
          <?php
          if ($portofolio <> 0) {
          ?>
            <th><?= $portofolio; ?> %</th>
          <?php
          }
          ?>
          <?php
          if ($produk <> 0) {
          ?>
            <th><?= $produk; ?> %</th>
          <?php
          }
          ?>
          <?php
          if ($presentasi <> 0) {
          ?>
            <th><?= $presentasi; ?> %</th>
          <?php
          }
          ?>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        foreach ($data_nilai as $key => $value) {
        ?>
          <tr>
            <td class="text-right"><?= $no; ?>.</td>
            <td><?= $value->nipd . '<br>' . $value->nm_pd; ?></td>
            <td class="text-center"><?= $value->n_sikap_2 * 1; ?></td>
            <td class="text-center"><?= $value->n_pengetahuan_2_quiz * 1; ?></td>
            <td class="text-center"><?= $value->n_pengetahuan_2_tugas_terstruktur * 1; ?></td>
            <td class="text-center"><?= $value->n_pengetahuan_2_uts * 1; ?></td>
            <td class="text-center"><?= $value->n_pengetahuan_2_uas * 1; ?></td>

            <?php
            if ($unjuk_kinerja <> 0) {
            ?>
              <td class="text-center"><?= $unjuk_kinerja == 0 ? '-' : $value->n_keterampilan_2_unjuk_kinerja * 1; ?></td>
            <?php
            }
            ?>
            <?php
            if ($proyek <> 0) {
            ?>
              <td class="text-center"><?= $proyek == 0 ? '-' : $value->n_keterampilan_2_proyek * 1; ?></td>
            <?php
            }
            ?>
            <?php
            if ($portofolio <> 0) {
            ?>
              <td class="text-center"><?= $portofolio == 0 ? '-' : $value->n_keterampilan_2_portofolio * 1; ?></td>
            <?php
            }
            ?>
            <?php
            if ($produk <> 0) {
            ?>
              <td class="text-center"><?= $produk == 0 ? '-' : $value->n_keterampilan_2_produk * 1; ?></td>
            <?php
            }
            ?>
            <?php
            if ($presentasi <> 0) {
            ?>
              <td class="text-center"><?= $presentasi == 0 ? '-' : $value->n_keterampilan_2_presentasi * 1; ?></td>
            <?php
            }
            ?>

            <?php
            if ($unjuk_kinerja == 0 && $proyek == 0 && $portofolio == 0 && $produk == 0 && $presentasi == 0) {
            ?>
              <td class="text-center">&nbsp;</td>
            <?php
            }
            ?>
            <td class="text-center"><?= $value->nilai_angka * 1; ?></td>
            <td class="text-center" style="background-color: #ddd;"><b><?= !empty($value->nilai_huruf) ? $value->nilai_huruf : '-'; ?></b></td>
            <td class="text-center"><?= $value->nilai_indeks * 1; ?></td>
          </tr>
        <?php
          $no++;
        }
        ?>
      </tbody>
    </table>
    <br>
    <p class="text-center"><b>SEBARAN NILAI</b></p>

    <table class="table table-black" style="margin-top: 10px;">
      <thead>
        <tr>
          <th></th>
          <th>A</th>
          <th>B+</th>
          <th>B</th>
          <th>C+</th>
          <th>C</th>
          <th>D</th>
          <th>E</th>
        </tr>

      </thead>
      <tbody>
        <tr>
          <td>Jumlah</td>
          <td class="text-center"><?= $nilai_a; ?></td>
          <td class="text-center"><?= $nilai_b_plus; ?></td>
          <td class="text-center"><?= $nilai_b; ?></td>
          <td class="text-center"><?= $nilai_c_plus; ?></td>
          <td class="text-center"><?= $nilai_c; ?></td>
          <td class="text-center"><?= $nilai_d; ?></td>
          <td class="text-center"><?= $nilai_e; ?></td>


        </tr>
      </tbody>
    </table>

    <table class="table table-borderless" style="margin-top: 20px">
      <tr>

        <td class="text-right">
          <div class="col-md-3 text-center">
            Deli Serdang, <?= date('d-m-Y', strtotime($tanggal_lapor)); ?><br>Dosen Pengampu Mata Kuliah,
            <br>
            <br>
            <br>
            <br>
            (<?= $nm_ptk; ?>)<br>
            <?= 'NIDN : ' . $nidn; ?>
          </div>
        </td>
      </tr>
    </table>
  </div>
</body>

</html>