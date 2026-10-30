<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak SK Mengajar Dosen</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php $this->load->view('cetak/inc_pdf_common_css'); ?>

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 11px;
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
      margin-right: 10px;
      padding-top: 35px !important;
    }

    .nomargin {
      padding: 0;
      margin: 0;
    }
  </style>


</head>

<body>
  <div class="wrapper">
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td class="text-center">
            <p style=" font-weight: bold;margin-bottom: 3px">KEPUTUSAN DIREKTUR POLITEKNIK WILMAR BISNIS INDONESIA</p>
            <p style="margin-top: 3px;">Nomor : <?= $nomor_sk; ?></p>
            <p style="font-weight: bold;">Tentang</p>
            <p>PENUGASAN MENGAJAR DOSEN POLITEKNIK WILMAR BISNIS INDONESIA<br> <?= strtoupper($tahun_akademik); ?></p><br>
          </td>
        </tr>
      </tbody>
    </table>
    <p style="font-weight: bold;" class="text-center">DIREKTUR POLITEKNIK WILMAR BISNIS INDONESIA</p>
    <div class="row">
      <div class="column left">Menimbang</div>
      <div class="column middle">:</div>
      <div class="column right">
        <ol class="nomargin">
          <li>Bahwa untuk kelancaran jalannya kegiatan perkuliahan di Politeknik Wilmar Bisnis Indonesia pada <?= $tahun_akademik; ?>, dipandang perlu penetapan pembagian tugas mengajar dosen Politeknik Wilmar Bisnis Indonesia sesuai bidang keahliannya;</li>
          <li>Bahwa nama-nama yang tersebut dalam keputusan ini dipandang memenuhi persyaratan untuk ditugasi sebagai tenaga pengajar sesuai dengan bidang keahliannya.</li>
        </ol>
      </div>
    </div>
    <div class="row">
      <div class="column left">Mengingat</div>
      <div class="column middle">:</div>
      <div class="column right">
        <ol class="nomargin">
          <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
          <li>Undang-Undang No 12 Tahun 2012 tentang Pendidikan Tinggi;</li>
          <li>Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Pendidikan Tinggi dan Penyelenggaraan Perguruan Tinggi;</li>
          <li>Peraturan Kementerian Pendidikan Tinggi, Sains, dan Teknologi Republik Indonesia Nomor 39 Tahun 2025, tentang Penjaminan Mutu;</li>
          <li>Peraturan Menteri Pendidikan Tinggi, Sains, dan Teknologi Republik Indonesia Nomor 52 Tahun 2025 tentang Profesi, Karier, dan Penghasilan Dosen;</li>
          <li>Surat Keputusan Direktur Politeknik Wilmar Bisnis Indonesia Nomor 2252/WBI/PP/09/2025 tentang Pedoman Akademik dan Etika Mahasiswa Politeknik Wilmar Bisnis Indonesia.</li>
        </ol>
      </div>
    </div>
    <div class="row">
      <div class="column left" style="font-weight: bold;">MENETAPKAN</div>
      <div class="column middle">:</div>
    </div>
    <div class="row">
      <div class="column left">Pertama</div>
      <div class="column middle">:</div>

      <div class="column right">
        Menugaskan Saudara yang namanya tercantum di bawah ini :
        <?= $tbl_1; ?>
        <p>Untuk mengajar pada <?= $tahun_akademik; ?> dengan mata kuliah :</p>
        <?= $tbl_ajar; ?>
      </div>
    </div>
    <div class="row">
      <div class="column left">Kedua</div>
      <div class="column middle">:</div>

      <div class="column right">
        Keputusan ini berlaku sejak tanggal ditetapkan dan apabila dikemudian hari terdapat kekeliruan di dalam penetapannya akan diperbaiki sebagaimana mestinya.

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
            <td>
              <?= $tanggal_sk; ?>
              <!-- <?= tgl_indo($judul->tanggal_sk); ?> -->
            </td>
          </tr>
          <tr>
            <td style="width:60%" class="text-center"></td>
            <td colspan="3">
              <span id="el_direktur">Direktur,</span>
              <br><br><br><br>
              <span style="font-weight: bold;"><?= DIREKTUR; ?></span>
            </td>
          </tr>
          <tr>
            <td>
              <p>Tembusan :</p>
              <p>1. Ketua Program Studi</p>
              <p>2. Yang bersangkutan untuk dilaksanakan.</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>














































<!-- <div class="row">
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
</div> -->