<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak SK Pembimbing dan Penguji Skripsi</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"> -->
  <!--  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>"> -->
  <?php $this->load->view('cetak/inc_pdf_common_css'); ?>

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 12px;
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
      margin-left: 55px;
      margin-right: 45px;
      padding-top: 100px !important;
    }

    .nomargin {
      padding: 0;
      margin: 0;
    }
  </style>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
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
              <p style="font-size:12px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">KEPUTUSAN</p>
              <p style="font-size:12px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">DIREKTUR POLITEKNIK WILMAR BISNIS INDONESIA</p>
              <p style="font-size:13px;margin-top: 3px">NOMOR : <?= $judul->nomor_sk; ?></p><br>
              <p style="font-size:12px;">TENTANG</p><br>
              <p style="font-size:12px;font-weight: bold;margin-top: 1px;margin-bottom: 30px">PENGANGKATAN DOSEN PEMBIMBING TUGAS AKHIR MAHASISWA</p><br>
            </td>
          </tr>
        </tbody>
      </table>
      <p style="font-size:12px;font-weight: bold;" class="text-center">DIREKTUR POLITEKNIK WILMAR BISNIS INDONESIA</p>
      <div class="row">
        <div class="column left">Menimbang</div>
        <div class="column middle">:</div>
        <div class="column right">
          <ol class="nomargin">
            <li>bahwa untuk mengakhiri studi pada Program Studi <?= $judul->nm_lemb; ?> Politeknik Wilmar Bisnis Indonesia mahasiswa diwajibkan menyusun Tugas Akhir ;</li>
            <li>bahwa berdasarkan pertimbangan sebagaimana dimaksud dalam butir 1 tersebut perlu
              ditetapkan Surat Keputusan Direktur Politeknik Wilmar Bisnis Indonesia tentang
              pengangkatan dosen pembimbing, pendamping pembimbing Tugas Akhir mahasiswa
              Tahun Akademik <?= $judul->tahun_akademik_sk; ?>.</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="column left">Mengingat</div>
        <div class="column middle">:</div>
        <div class="column right">
          <ol class="nomargin">
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
            <li>Undang-Undang Nomor 12 Tahun 2012 tentang Pendidikan Tinggi;</li>
            <li>Peraturan Pemerintah Nomor 37 Tahun 2009 tentang Dosen;</li>
            <li>Peraturan Presiden nomor 8 tahun 2012, tentang KKNI</li>
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 154 Tahun 2014 tentang rumpun ilmu pengetahuan dan teknologi serta gelar lulusan perguruan tinggi.</li>
            <li>Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi dan pengelolaan perguruan tinggi.</li>
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 73 Tahun 2014 tentang Penerapan KKNI di Pendidikan Tinggi.</li>
            <li>Peraturan Menteri Riset Teknologi dan Pendidikan Tinggi Nomor 62 Tahun 2016 tentang Sistem Penjaminan Mutu Pendidikan Tinggi;</li>
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Republik Indonesia Nomor 3 Tahun 2020, tentang Standar Nasional Pendidikan Tinggi;</li>
            <li>Surat Keputusan Dewan Pengurus Yayasan Murni Sadar Nomor Kep.007/SK/YMS/XII/2020 Tentang Statuta Politeknik Wilmar Bisnis Indonesia.</li>
            <li>Keputusan Direktur Politeknik Wilmar Bisnis Indonesia Nomor 634/WBI/PP/09/2016 tentang Standar Mutu Politeknik Wilmar Bisnis Indonesia.</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="column left">Memperhatikan</div>
        <div class="column middle">:</div>
        <div class="column right">
          Usulan Ketua Program Studi <?= $judul->nm_lemb; ?> tentang penetapan judul Tugas Akhir mahasiswa dan penetapan dosen pembimbing Tugas Akhir tanggal <?= tgl_indo($judul->tanggal_usulan_kaprodi); ?>.
        </div>
      </div>

      <div class="row">
        <p>&nbsp;</p>
        <div class="text-center" style="font-weight: bold;">MEMUTUSKAN : </div>
      </div>
      <div class="row">
        <div class="column left">Menetapkan</div>
        <div class="column middle">:</div>

        <div class="column right" style="font-weight: bold;">
          <?php
          if ($id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20') {
          ?>
            KEPUTUSAN DIREKTUR TENTANG PENGANGKATAN DOSEN PEMBIMBING TUGAS AKHIR PROGRAM STUDI <?= strtoupper((string)$judul->nm_lemb); ?> TAHUN AKADEMIK <?= $judul->tahun_akademik_sk; ?>
          <?php
          } else {
          ?>
            KEPUTUSAN DIREKTUR TENTANG PENGANGKATAN DOSEN PEMBIMBING DAN
            PENDAMPING PEMBIMBING TUGAS AKHIR PROGRAM STUDI <?= strtoupper((string)$judul->nm_lemb); ?> TAHUN AKADEMIK <?= $judul->tahun_akademik_sk; ?>
          <?php
          }
          ?>

        </div>
      </div>
      <div style="page-break-after: always;"></div>
      <div class="row">
        <div class="column left">Pertama</div>
        <div class="column middle">:</div>

        <div class="column right">
          Menugaskan nama-nama yang tersebut dibawah ini :
          <?= $myTable; ?>

          untuk melakukan pembimbingan dan melakukan pengujian atas hasil penelitian kepada mahasiswa tersebut dibawah ini :

          <table class="table table-borderless">
            <tbody>
              <tr>
                <td style="width: 150px;">Nama Mahasiswa</td>
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
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="column left">Kedua</div>
        <div class="column middle">:</div>
        <div class="column right">
          Surat Keputusan ini berlaku sejak tanggal ditetapkan sampai dengan berakhirnya masa bimbingan dan apabila dikemudian hari terdapat kekeliruan dalam penetapan ini akan diadakan perbaikan sebagaimana mestinya.
        </div>
      </div>

      <div class="row">
        <table class="table table-borderless2">
          <tbody>
            <tr>
              <td style="width:50%" class="text-center"></td>
              <td style="width: 100px;padding-top:10px">Ditetapkan di</td>
              <td style="width: 5px;padding-top:10px">:</td>
              <td style="padding-top: 10px;">Deli Serdang</td>
            </tr>
            <tr>
              <td style="width:50%" class="text-center"></td>
              <td>Pada tanggal</td>
              <td>:</td>
              <td><?= tgl_indo($judul->tanggal_sk); ?></td>
            </tr>
            <tr>
              <td style="width:60%" class="text-center"></td>
              <td colspan="3" style="padding-top: 10px;">
                <span id="el_direktur">Direktur,</span><br>
                <img src="<?= $qr_name; ?>" width="65" height="65"><br>
                <span style="font-weight: bold;"><?= DIREKTUR; ?></span>
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