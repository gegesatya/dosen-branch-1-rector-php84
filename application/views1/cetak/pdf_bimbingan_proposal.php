<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Kegiatan Konsultasi</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>">

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 13px;
    }

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 13px !important;
      margin-bottom: 10px;
      vertical-align: top;
    }

    .table-borderless2 th,
    .table-borderless2 td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 13px !important;
      margin-bottom: 10px;
      vertical-align: top;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #000 !important;
      padding: 5px !important;
      font-size: 11px !important;

    }

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }

    .column {
      float: left;
      padding: 3px;
      height: auto;
      text-align: justify;
      text-justify: inter-word;
    }

    .left {
      width: 130px;
      white-space: nowrap;
    }

    .left-60 {
      width: 400px;
      white-space: nowrap;
    }

    .right {
      width: auto;
    }

    .middle {
      width: 25px;
    }

    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    .wrapper {
      margin-left: 35px;
      margin-right: 10px;
      padding-top: 70px !important;
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body onload="window.print();">
  <div class="wrapper">
    <div class="form-container">
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td colspan="3" class="text-center">
              <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">LAPORAN KEGIATAN KONSULTASI</h4>
            </td>
          </tr>
          <tr>
            <td colspan="3" class="text-center">
              <h5 style="margin-top: 1px;margin-bottom: 30px"></h5>
              <h5 style="margin-top: 1px;margin-bottom: 30px"></h5>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Judul</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $data->judul_ditetapkan; ?></div>
      </div>
      <div class="row">
        <div class="column left">Nama Mahasiswa</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $data->nm_pd; ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $data->nipd; ?></div>
      </div>
      <!-- <div class="row">
        <div class="column left">Program Studi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $data->nm_lemb; ?></div>
      </div> -->
      <div class="row">
        <div class="column left">Pembimbing</div>
        <div class="column middle text-right">:</div>
        <div class="column right">
          <ol>
            <li><?= $nm_doping_1; ?></li>
            <li><?= $nm_doping_2; ?></li>
          </ol>
        </div>
      </div>

      <div class="row" style="margin-top: 20px;">
        <table class="table table-black">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Uraian Kegiatan</th>
              <th>Nama/Paraf</th>
            </tr>
          </thead>
          <tbody>
            <?= $html_table; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>


</body>

</html>