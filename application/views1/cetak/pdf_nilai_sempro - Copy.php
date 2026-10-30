<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Penilaian Seminar Proposal</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>">

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 12px;
    }

    /* @page {
      margin-top: 50mm;
    } */

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      /*font-size: 13px !important;*/
      margin-bottom: 10px;
      vertical-align: top;
    }

    .table-borderless2 th,
    .table-borderless2 td {
      border: 0px solid #fff !important;
      padding: 5px !important;
      font-size: 12px !important;
      vertical-align: top;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #000 !important;
      padding: 3px !important;
      font-size: 12px !important;

    }

    .table-black-2 th,
    .table-black-2 td {
      border-top: 1px solid #000 !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
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
      width: 85px;
      white-space: nowrap;
    }

    .left-60 {
      width: 400px;
      white-space: nowrap;
    }

    .left-100 {
      width: 200px;
      white-space: nowrap;
    }

    .right {
      width: auto;

    }

    .middle {
      width: 23px;
    }

    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    .wrapper {
      margin-left: 0px;

    }

    .nomargin {
      padding: 0;
      margin: 0;
    }

    .td-warna {
      background-color: #e5e7e9;
      font-weight: bold;
    }

    .td-high {
      line-height: 1.5;
      text-align: justify;
    }

    .custom {
      border-top: 1px solid #000 !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;
      background-color: #e5e7e9;
      font-weight: bold
    }

    .custom-2 {
      border-top: none !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;
      line-height: 1.5;
      text-align: justify
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>
  <div class="wrapper">
    <div class="form-container">
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN SEMINAR PROPOSAL TUGAS AKHIR</p>
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
        <div class="column left">Judul Proposal</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <?= $myTable; ?>
      Keterangan:<br>
      Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
      Nilai = bobot × skor

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">Deli Serdang, ______________________</td>
            </tr>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>Dosen Pembimbing Utama</td>
            </tr>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                <br>
                <br>
                <br>
                <span style="font-weight: bold;"><?= $dosen_nama->nama_lengkap; ?></span><br>
                NIDN : <?= $dosen_nama->nidn; ?>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">BERITA ACARA SEMINAR PROPOSAL TUGAS AKHIR</p>
      <div class="row">
        <div class="column right">
          Pada hari ini, <?= $berita_acara->seminar_hari; ?> tanggal <?= tgl_indo($berita_acara->seminar_tanggal); ?> telah dilaksanakan Seminar Proposal Tugas Akhir:
        </div>
      </div>
      <table class="table table-borderless2">
        <tbody>
          <tr>
            <td style="width: 10px;">1.</td>
            <td style="width:160px">Nama</td>
            <td style="width: 10px;">:</td>
            <td><?= ucwords(strtolower($judul->nm_pd)); ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">2.</td>
            <td>NIM</td>
            <td style="width: 10px;">:</td>
            <td><?= $judul->nipd; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">3.</td>
            <td>Judul</td>
            <td style="width: 10px;">:</td>
            <td><?= $judul->judul_ditetapkan; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">4.</td>
            <td>Moderator</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->seminar_moderator . ' (' . $berita_acara->seminar_nim_moderator . ')'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">5.</td>
            <td>Pembahas</td>
            <td style="width: 10px;">:</td>
            <td>
              <ol>
                <li><?= $berita_acara->seminar_pembahas_1 . ' (' . $berita_acara->seminar_nim_pembahas_1 . ')'; ?></li>
                <li><?= $berita_acara->seminar_pembahas_2 . ' (' . $berita_acara->seminar_nim_pembahas_2 . ')'; ?></li>
              </ol>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;">6.</td>
            <td>Jumlah Peserta</td>
            <td style="width: 10px;">:</td>
            <td></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td>Mahasiswa</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->seminar_jumlah_mahasiswa; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td>Dosen Pembimbing/Penguji</td>
            <td style="width: 10px;">:</td>
            <td></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td colspan="3">
              <table class="table table-borderless2">
                <tbody>
                  <?php
                  $i = 1;
                  if (!empty($doping_1)) {
                    echo '<tr>
                <td class="text-right">' . $i++ . '.</td>
                <td class="text-left">' . $doping_1->nama_lengkap . '</td>
                <td class="text-center">(______________________)</td>
              </tr>';
                  }
                  ?>
                  <?php
                  if (!empty($doping_2)) {
                    echo '<tr>
                <td class="text-right">' . $i++ . '.</td>
                <td class="text-left">' . $doping_2->nama_lengkap . '</td>
                <td class="text-center">(______________________)</td>
              </tr>';
                  }
                  ?>
                  <?php
                  if (!empty($penguji)) {
                    echo '<tr>
                <td class="text-right">' . $i++ . '.</td>
                <td class="text-left">' . $penguji->nama_lengkap . '</td>
                <td class="text-center">(______________________)</td>
              </tr>';
                  }
                  ?>
                  <?php
                  if (!empty($penguji_2)) {
                    echo '<tr>
                <td class="text-right">' . $i++ . '.</td>
                <td class="text-left">' . $penguji_2->nama_lengkap . '</td>
                <td class="text-center">(______________________)</td>
              </tr>';
                  }
                  ?>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;">7.</td>
            <td>Tempat</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->seminar_tempat; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">8.</td>
            <td>Mulai Pukul</td>
            <td style="width: 10px;">:</td>
            <td><?= substr($berita_acara->seminar_jam_mulai, 0, 5)  . ' WIB sampai dengan ' . substr($berita_acara->seminar_jam_selesai, 0, 5) . ' WIB'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">9.</td>
            <td colspan="3">Dinyatakan bahwa proposal penelitian :</td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td colspan="3">
              <?php
              if ($judul->hasil_sempro == 'layak') {
                echo '<p><span style="font-weight: bold;">Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Penelitian dengan usulan revisi</span></p>';
              } else {
                echo '<p><span style="font-weight: bold;">Tidak Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Penelitian</span></p>';
              }
              ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div style="page-break-after: always;"></div>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">CATATAN PELAKSANAAN SEMINAR PROPOSAL</p>
            </td>
          </tr>
        </tbody>
      </table>
      Ringkasan Pelaksanaan Diskusi:
      <div class="custom">A. Usulan Revisi Judul</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_sempro as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->catatan_judul)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">B. Usulan Revisi Latar Belakang</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_sempro as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->catatan_latar_belakang)  . '</p><br>';
        }
        ?>
      </div>

      <!--  <table class="table table-black-2">
        <tbody>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">A. Usulan Revisi Judul</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_judul)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">B. Usulan Revisi Latar Belakang</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_latar_belakang)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">C. Usulan Revisi Rumusan Masalah</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_rumusan_masalah)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">D. Usulan Revisi Tujuan Penelitian</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_tujuan_penelitian)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">E. Usulan Revisi Tinjauan Pustaka</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_tinjauan_pustaka)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">F. Usulan Revisi Metode Penelitian</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_metode_penelitian)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">G. Usulan Revisi Daftar Pustaka</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_daftar_pustaka)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
          <tr>
            <td style="background-color: #e5e7e9;font-weight:bold">H. Usulan Revisi Format Penulisan</td>
          </tr>
          <tr>
            <td style="line-height: 1.5;text-align:justify">
              <?php
              foreach ($usulan_sempro as $key => $value) {
                echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><div class="row">' . nl2br($value->catatan_format_penulisan)  . '</div><br>';
              }
              ?>
            </td>
          </tr>
        </tbody>
      </table> -->
      Demikian catatan pelaksanaan seminar proposal ini dibuat dengan sebenarnya dan segala sesuatu yang ditulis telah diterima oleh pengusul Proposal Tugas Akhir untuk dilaksanakan sebagaimana mestinya.
      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">Deli Serdang, ______________________</td>
            </tr>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>Pembimbing/Penguji</td>
            </tr>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                <br>
                <br>
                <br>
                <span style="font-weight: bold;"><?= $dosen_nama->nama_lengkap; ?></span><br>
                NIDN : <?= $dosen_nama->nidn; ?>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

    </div>
  </div>
</body>

</html>