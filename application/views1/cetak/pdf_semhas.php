<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Penilaian Seminar Hasil</title>
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
      width: 115px;
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
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN SEMINAR HASIL TUGAS AKHIR</p>
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
      <?= $myTable; ?>
      Keterangan:<br>
      Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
      Nilai = bobot × skor

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                Deli Serdang, <?= tgl_indo($berita_acara->semhas_tanggal); ?><br>Dosen Pembimbing Utama<br>
                <img src="<?= $qr_doping_1; ?>"><br>
                <span style="font-weight: bold;"><?= $dosen_nama->nama_lengkap; ?></span><br>
                NIDN : <?= $dosen_nama->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">BERITA ACARA SEMINAR HASIL TUGAS AKHIR</p>
      <div class="row">
        <div class="column right">
          Pada hari ini, <?= $berita_acara->semhas_hari; ?> tanggal <?= tgl_indo($berita_acara->semhas_tanggal); ?> telah dilaksanakan Seminar Hasil Tugas Akhir:
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
            <td><?= $berita_acara->semhas_moderator . ' (' . $berita_acara->semhas_nim_moderator . ')'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">5.</td>
            <td>Pembahas</td>
            <td style="width: 10px;">:</td>
            <td>
              <ol>
                <li><?= $berita_acara->semhas_pembahas_1 . ' (' . $berita_acara->semhas_nim_pembahas_1 . ')'; ?></li>
                <li><?= $berita_acara->semhas_pembahas_2 . ' (' . $berita_acara->semhas_nim_pembahas_2 . ')'; ?></li>
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
            <td><?= $berita_acara->semhas_jumlah_mahasiswa; ?></td>
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
                  if (!empty($penguji)) {
                    if ($nilai_semhas_p1->semhas_nilai_akhir > 0) {
                      echo '<tr>
                      <td class="text-right">' . $i++ . '.</td>
                      <td class="text-left">' . $penguji->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p1->semhas_nilai_akhir . ')</td>
                    </tr>';
                    }
                  }
                  ?>
                  <?php
                  if (!empty($penguji_2)) {
                    if ($nilai_semhas_p2->semhas_nilai_akhir > 0) {
                      echo '<tr>
                          <td class="text-right">' . $i++ . '.</td>
                          <td class="text-left">' . $penguji_2->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p2->semhas_nilai_akhir . ')</td>
                        </tr>';
                    }
                  }
                  ?>
                  <?php

                  echo '<tr>
                          <td class="text-right">' . $i++ . '.</td>
                          <td class="text-left">' . $penguji_3->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p3->semhas_nilai_akhir . ')</td>
                        </tr>';

                  ?>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;">7.</td>
            <td>Tempat</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->semhas_tempat; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">8.</td>
            <td>Mulai Pukul</td>
            <td style="width: 10px;">:</td>
            <td><?= substr($berita_acara->semhas_jam_mulai, 0, 5)  . ' WIB sampai dengan ' . substr($berita_acara->semhas_jam_selesai, 0, 5) . ' WIB'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">9.</td>
            <td colspan="3">Rekapitulasi Nilai :
              <br><br>
              <table class="table table-black">
                <tbody>
                  <tr>
                    <td colspan="3" class="text-center">Perhitungan Nilai Akhir</td>
                  </tr>
                  <tr>
                    <td style="width:33.33%" class="text-center">Pembimbing Utama</td>
                    <td style="width:33.33%" class="text-center">Pembimbing<br>Pendamping/Penguji *)</td>
                    <td style="width:33.33%" class="text-center">Penguji</td>
                  </tr>
                  <tr>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p1->semhas_nilai_akhir; ?></td>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p2->semhas_nilai_akhir; ?></td>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p3->semhas_nilai_akhir; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-center"><?= $rata_rata; ?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td colspan="3">
              <?php
              if ($hasil_semhas == 'layak') {
                echo '<p>Dinyatakan bahwa Hasil Penelitian <span style="font-weight: bold;">Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Ujian Tugas Akhir</span></p>';
              } elseif ($hasil_semhas == 'tidak_layak') {
                echo '<p>Dinyatakan bahwa Hasil Penelitian <span style="font-weight: bold;">Tidak Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Ujian Tugas Akhir</span></p>';
              } else {
                echo '<p>Belum ada hasil seminar </p>';
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
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">CATATAN PELAKSANAAN SEMINAR HASIL</p>
            </td>
          </tr>
        </tbody>
      </table>
      Ringkasan Pelaksanaan Diskusi:
      <div class="custom">A. Usulan Revisi Judul</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_judul)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">B. Usulan Revisi Latar Belakang</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_latar_belakang)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">C. Usulan Revisi Rumusan Masalah</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_rumusan_masalah)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">D. Usulan Revisi Tujuan Penelitian</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_tujuan_penelitian)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">E. Usulan Revisi Tinjauan Pustaka</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_tinjauan_pustaka)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">F. Usulan Revisi Metode Penelitian</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_metode_penelitian)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">G. Usulan Revisi Daftar Pustaka</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_daftar_pustaka)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">H. Usulan Revisi Format Penulisan</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_catatan_format_penulisan)  . '</p><br>';
        }
        ?>
      </div>

      <div class="custom">I. Desain dan Pembuatan</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_desain)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">J. Pengujian dan Analisa</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_pengujian)  . '</p><br>';
        }
        ?>
      </div>
      <div class="custom">K. Kesimpulan dan Rekomendasi Penerapan</div>
      <div class="custom-2">
        <?php
        foreach ($usulan_semhas as $key => $value) {
          echo '<span style="font-weight: bold;">' . $value->nama_lengkap . ' : </span><p style="margin-left:20px">' . nl2br($value->semhas_kesimpulan)  . '</p><br>';
        }
        ?>
      </div>


      <p style="margin-top: 20px;">Demikian catatan pelaksanaan seminar hasil ini dibuat dengan sebenarnya dan segala sesuatu yang ditulis telah diterima oleh pengusul Proposal Tugas Akhir untuk dilaksanakan sebagaimana mestinya.</p>
      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                Deli Serdang, <?= tgl_indo($berita_acara->semhas_tanggal); ?><br>
                Pembimbing/Penguji<br>
                <img src="<?= $qr_doping_1; ?>"><br>
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