<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Kritik Saran Mahasiswa</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <style type="text/css">
    body {
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 12px;
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

    */ .table>tbody+tbody {
      border-top: 2px solid #ddd;
    }

    .table .table {
      background-color: #fff;
    }

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 13px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 5px !important;
      font-size: 11px !important;
    }

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
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


    ?>
    <h3 class="text-center">KRITIK DAN SARAN MAHASISWA</h3>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>DOSEN</td>
          <td>: <?= $nm_ptk; ?></td>
          <!-- <td>PRODI</td>
          <td>: <?= $nm_lemb; ?></td> -->
        </tr>
        <tr>
          <td>MATA KULIAH</td>
          <td>: <?= $nm_mk; ?></td>
          <td>TAHUN AJARAN</td>
          <td>: <?= $nm_smt; ?></td>
        </tr>

      </tbody>
    </table>
    <hr />

    <div class="col-md-12 col-sm-12">
      <p>Catatan : Data kritik dan saran tidak berdasarkan nomor induk mahasiswa ataupun abjad nama mahasiswa. Data kritik dan saran tampil secara acak.</p>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Kritik dan Saran</th>

          </tr>
        </thead>
        <?php
        $i = 1;
        foreach ($kritik_saran as $key => $value) {
        ?>
          <tr>
            <td><?= $i; ?></td>
            <td><?= !empty($value->kritik_saran) ? $value->kritik_saran : ''; ?></td>
          </tr>
        <?php
          $i++;
        }
        ?>
      </table>
    </div>
  </div>
</body>

</html>