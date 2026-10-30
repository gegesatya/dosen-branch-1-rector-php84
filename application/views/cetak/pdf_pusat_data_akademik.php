<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Data Akademik Mahasiswa</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>">

  <style type="text/css">
    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 12px !important;
      margin-bottom: 10px
    }

    .table-black th,
    .table-black td {
      border: 1px solid #000 !important;
      padding: 4px !important;
      font-size: 12px !important;

    }

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body style="margin-left: 50px">
  
  <div class="wrapper">
    
    <section class="invoice">
      

      <table class="table table-borderless">
        <tbody>
          <tr>
            <td rowspan="3">
              <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="150px" style="margin-top:10px;margin-right: 20px;" />
            </td>
            <td>
              <h4 style="font-size: 14px; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            </td>
          </tr>
          <tr>
            <td>
              <p style="font-size: 12px;">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
            </td>
          </tr>
          <tr>
            <td>
              <p style="font-size: 12px;">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
            </td>
          </tr>
        </tbody>
      </table>
      <hr />
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td colspan="4" class="text-center">
              <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">DATA AKADEMIK MAHASISWA </h4>
            </td>
          </tr>
          <tr>
            <td width="300px"><strong>Nama Mahasiswa</strong></td>
            <td>: <?= $mahasiswa->nm_pd; ?></td>
          </tr>
          <tr>
            <td><strong>Nomor Induk Mahasiswa</strong></td>
            <td>: <?= $mahasiswa->nipd; ?></td>
          </tr>


        </tbody>
      </table>
      <?php
      if (is_array($tabel)) {
        foreach ($tabel as $t) {
          echo $t;
        }
      } else {
        echo $tabel;
      }
      ?>

  </div>
  </section>
  </div>
</body>

</html>