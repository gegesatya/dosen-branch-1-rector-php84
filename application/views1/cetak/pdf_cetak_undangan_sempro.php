<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Undangan Sempro</title>
  

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
      padding: 3px !important;
      font-size: 12px !important;

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
      width: 95px;
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

      <div class="row">
        <div class="column right">
          <p class="text-right">Deli Serdang, <?= tgl_indo($judul->tanggal_undangan); ?></p>
        </div>
      </div>
      <div class="row">
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 100px;">Nomor</td>
              <td style="width: 5px">:</td>
              <td><?= $judul->nomor_undangan; ?></td>
            </tr>
            <tr>
              <td>Lampiran</td>
              <td>:</td>
              <td>Satu exemplar proposal </td>
            </tr>
            <tr>
              <td>Hal</td>
              <td>:</td>
              <td style="font-weight: bold;">Undangan Seminar Proposal</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="row">
        <div class="column right">
          <p style="margin-top: 2px;margin-bottom:2px">Kepada Yth</p>
          <p style="margin-top: 2px;margin-bottom:2px"><?=$penguji_sempro_1; ?></p>
          <p style="margin-top: 2px;margin-bottom:2px"><?=$penguji_sempro_2; ?></p>
          <p style="margin-top: 2px;margin-bottom:2px"><?=$penguji_sempro_3; ?></p>
        
          <p style="margin-top: 2px;margin-bottom:2px">Politeknik Wilmar Bisnis Indonesia</p>
        </div>
      </div>
      <div class="row">
        <div class="column right">
          Dengan hormat, dengan ini kami mengharapkan kehadiran Bapak/Ibu pada Seminar Proposal Tugas Akhir mahasiswa atas nama:
          <br><br>
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td style="width: 150px;">Nama</td>
                <td style="width: 5px">:</td>
                <td><?= $judul->nm_pd; ?></td>
              </tr>
              <tr>
                <td>NIM</td>
                <td>:</td>
                <td><?= $judul->nipd; ?></td>
              </tr>
              <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td><?= $judul->nm_lemb; ?></td>
              </tr>
              <tr>
                <td>Judul</td>
                <td>:</td>
                <td><?= $judul->judul_ditetapkan; ?></td>
              </tr>

            </tbody>
          </table>
          Yang akan dilaksanakan pada:
          <br><br>
          <?php
          $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
          );
          $date = $judul->tanggal_seminar;
          $namahari = date('l', strtotime($date));

                    ?>
          <table class="table table-borderless">
            <tbody>
              <tr>
                <td style="width: 150px;">Hari/Tanggal</td>
                <td style="width: 5px">:</td>
                <td><?= $daftar_hari[$namahari] . ' / ' . tgl_indo($judul->tanggal_seminar); ?></td>
              </tr>
              <tr>
                <td>Pukul</td>
                <td>:</td>
                <td><?= $judul->jam; ?> WIB</td>
              </tr>
              <tr>
                <td>Tempat</td>
                <td>:</td>
                <td><?= $judul->tempat; ?></td>
              </tr>
            </tbody>
          </table>
          Bersama ini pula kami lampirkan Proposal Tugas Akhir mahasiswa.
          <br>
          Demikian atas kehadiran Bapak/ibu, kami sampaikan terima kasih.

        </div>
      </div>

      <div class="row">
        <table class="table table-borderless2">
          <tbody>
            <tr>
              <td style="width:60%" class="text-center"></td>
              <td style="padding-top: 10px;">Ketua Program Studi</td>
            </tr>
            <tr>
              <td style="width:60%" class="text-center"></td>
              <td>
                <img src="<?= $qr_name; ?>"><br>
                <span style="font-weight: bold;"><?= $nama_kaprodi; ?></span>
              </td>
            </tr>
            <tr>
              <td>
                <p>Tembusan :</p>
                <p>1. Ketua Program Studi</p>
                <p>2. Arsip</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>