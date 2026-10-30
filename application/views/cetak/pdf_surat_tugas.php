<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Surat Tugas Penguji Sempro</title>
  <!-- Tell the browser to be responsive to screen width -->

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php $this->load->view('cetak/inc_pdf_common_css'); ?>

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 13px;
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
            <td colspan="3" class="text-center">
              <h5 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">SURAT TUGAS</h5>
            </td>
          </tr>
          <tr>
            <td colspan="3" class="text-center">
              <h5 style="margin-top: 1px;margin-bottom: 30px">Nomor : <?= $judul->no_surat_tugas; ?></h5>

            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column right">
          Yang bertandatangan di bawah ini Direktur Politeknik Wilmar Bisnis Indonesia dengan ini menugaskan dosen berikut :
          <br><br>
          <?= $myTable; ?>
          sebagai penguji pada Seminar Proposal dan Seminar Hasil Tugas Akhir pada semester <?= $judul->tahun_akademik_sk; ?> terhadap mahasiswa :
          <br><br>
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

        <div class="column right">
          Demikian surat tugas ini agar dapat dilaksanakan dengan seksama dan penuh rasa tanggungjawab.
        </div>
      </div>

      <div class="row">
        <table class="table table-borderless2">
          <tbody>
            <tr>
              <td style="width:50%" class="text-center"></td>

              <td style="padding-top: 10px;">Deli Serdang, <?= tgl_indo($judul->tanggal_surat_tugas); ?></td>
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