<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Penguji Sidang Tugas Akhir</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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

    .left-300 {
      width: 160px;
      white-space: nowrap;
    }

    .left-180 {
      width: 180px;
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
              <p style="font-size:12px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">DIREKTUR POLITEKNIK WILMAR
                BISNIS INDONESIA</p>
              <p style="font-size:13px;margin-top: 3px">NOMOR : <?= $judul->no_sk_penguji_sidang; ?></p><br>
              <p style="font-size:12px;">TENTANG</p><br>
              <p style="font-size:12px;font-weight: bold;margin-top: 1px;margin-bottom: 30px">PENETAPAN DOSEN PENGUJI
                SIDANG AKHIR PENILAIAN TUGAS AKHIR MAHASISWA<br>
                POLITEKNIK WILMAR BISNIS INDONESIA
              </p><br>
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
            <li>bahwa untuk menyelesaikan studi pada tahap sarjana terapan, setiap mahasiswa diwajibkan untuk menempuh
              ujian sidang penilaian Tugas Akhir;</li>
            <li>bahwa penyelenggaraan ujian sidang penilaian Tugas Akhir perlu dibentuk tim penguji untuk
              pelaksanaannya;</li>
            <li>bahwa sehubungan dengan butir 1 dan 2 perlu diterbitkan keputusannya. </li>
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
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 154 Tahun 2014 tentang rumpun ilmu pengetahuan dan
              teknologi serta gelar lulusan perguruan tinggi.</li>
            <li>Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi dan pengelolaan
              perguruan tinggi.</li>
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 73 Tahun 2014 tentang Penerapan KKNI di Pendidikan
              Tinggi.</li>
            <li>Peraturan Menteri Riset Teknologi dan Pendidikan Tinggi Nomor 62 Tahun 2016 tentang Sistem Penjaminan
              Mutu Pendidikan Tinggi;</li>
            <li>Peraturan Menteri Pendidikan dan Kebudayaan Republik Indonesia Nomor 3 Tahun 2020, tentang Standar
              Nasional Pendidikan Tinggi;</li>
            <li>Surat Keputusan Dewan Pengurus Yayasan Murni Sadar Nomor Kep.007/SK/YMS/XII/2020 Tentang Statuta
              Politeknik Wilmar Bisnis Indonesia.</li>
            <li>Keputusan Direktur Politeknik Wilmar Bisnis Indonesia Nomor 634/WBI/PP/09/2016 tentang Standar Mutu
              Politeknik Wilmar Bisnis Indonesia.</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <p>&nbsp;</p>
        <div class="text-center" style="font-weight: bold;">MEMUTUSKAN : </div>
      </div>
      <div class="row">
        <div class="column left">Menetapkan</div>
        <div class="column middle">:</div>

      </div>

      <div class="row">
        <div class="column left">Pertama</div>
        <div class="column middle">:</div>

        <div class="column right">
          Ketua Program Studi sebagai Ketua sidang penilaian Tugas Akhir serta Bagian Akademik sebagai pelaksana teknis;
        </div>
      </div>
      <div class="row">
        <div class="column left">Kedua</div>
        <div class="column middle">:</div>
        <div class="column right">
          Membentuk tim penguji sidang penilaian Tugas Akhir mahasiswa dengan susunan sebagaimana terlampir dalam surat
          keputusan ini;
        </div>
      </div>
      <div class="row">
        <div class="column left">Ketiga</div>
        <div class="column middle">:</div>
        <div class="column right">
          Penetapan sebagai penguji sidang penilaian Tugas Akhir dikoordinasikan oleh Ketua Program studi;
        </div>
      </div>
      <div style="page-break-after: always;"></div>
      <div class="row">
        <div class="column left">Keempat</div>
        <div class="column middle">:</div>
        <div class="column right">
          Apabila terdapat kekeliruan dalam keputusan ini, akan diadakan perbaikan sebagaimana mestinya.
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
              <td><?= tgl_indo($judul->tgl_sk_penguji_sidang); ?></td>
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
      <div style="page-break-after: always;"></div>
      <div class="row">
        <div class="column left-180">Lampiran SK Direktur Nomor</div>
        <div class="column middle">:</div>
        <div class="column right"><?= $judul->no_sk_penguji_sidang; ?></div>
      </div>
      <div class="row">
        <div class="column left-180">Tanggal</div>
        <div class="column middle">:</div>
        <div class="column right"><?= tgl_indo($judul->tgl_sk_penguji_sidang); ?></div>
      </div>
      <div class="row">
        <div class="column left-180">Perihal</div>
        <div class="column middle">:</div>
        <div class="column right">Penetapan Dosen Penguji Sidang Akhir Penilaian Tugas Akhir Mahasiswa</div>
      </div>
      <br>
      <div class="row">
        <p>Menetapkan nama-nama di bawah ini untuk menjadi Penguji Sidang Tugas Akhir:</p>
        <?= $myTable; ?>
        <p>Pada Sidang Tugas Akhir berikut ini:</p>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 180px;">Nama Mahasiswa</td>
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
      </div>
      <div class="row">
        <table class="table table-borderless2">
          <tbody>
            <tr>
              <td style="width:50%" class="text-center"></td>
              <td>Deli Serdang, <?= tgl_indo($judul->tgl_sk_penguji_sidang); ?><br>Direktur,<br>
                <img src="<?= $qr_name; ?>" width="65" height="65"><br>
                <span style="font-weight: bold;"><?= DIREKTUR; ?></span>
              </td>
              <td style="width: 5px;padding-top:10px"></td>
              <td style="padding-top: 10px;"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>