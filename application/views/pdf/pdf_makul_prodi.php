<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Data Kelas Kuliah Prodi</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  if ($tipe_file == 1) {
  ?>
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
        padding: 5px !important;
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
        font-size: 12px !important;
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
        line-height: 0.5;
        color: inherit;
      }

      .text-ku {
        display: inline-block;
        border: solid 1px #000;
        min-height: 10px;
        width: 300px;
      }

      * {
        box-sizing: border-box;
      }

      .column {
        float: left;
        width: 100%;
        padding: 5px;
      }

      /* Clearfix (clear floats) */
      .row::after {
        content: "";
        clear: both;
        display: table;
      }

      figure {
        border: thin #c0c0c0 solid;
        display: flex;
        flex-flow: column;
        padding: 5px;
        max-width: 100%;
        margin: auto;
      }

      /*img {
            max-width: 220px;
            max-height: 150px;
        }*/

      figcaption {
        background-color: #222;
        color: #fff;
        font: italic smaller sans-serif;
        padding: 3px;
        text-align: center;
      }

      div.oto {
        max-width: 800px;
        margin: auto;
        border: 1px solid;
        padding: 80px;

      }
    </style>
  <?php
  } else {
  ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/dist/css/AdminLTE.css">
  <?php
  }
  ?>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
  
  <div class="oto">
    <h4 class="text-center">POLITEKNIK WBI</h4>
    <h4 class="text-center">DAFTAR KELAS PERKULIAHAN</h4>
    <h4 class="text-center">PROGRAM STUDI <?= strtoupper($nm_lemb); ?></h4>
    <h4 class="text-center">TAHUN AJARAN <?= strtoupper($nm_smt); ?></h4>
    <hr />
    <?= $tabel; ?>
  </div>
</body>

</html>