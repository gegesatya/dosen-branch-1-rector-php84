<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak EPOM Dosen</title>
  

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

    .table>tbody+tbody {
      border-top: 2px solid #ddd;
    }

    .table .table {
      background-color: #fff;
    }

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 10px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 3px !important;
      font-size: 10px !important;
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
  
  <div class="wrapper" style="margin-left:20px;margin-right: 10px;margin-top: 0px;margin-bottom: 20px">
    
    <section class="invoice" style="padding-top: 0px !important">
      <div class="form-container">

        <table class="table table-borderless" style="margin-bottom: 3px !important">
          <tbody>
            <tr style="vertical-align: middle;">
              <td colspan="3">
                <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
                <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
                <p style="font-size: 12px;margin:0 0 5px !important">LAPORAN EVALUASI PEMBELAJARAN OLEH MAHASISWA (EPOM)</p>
              </td>
              <td>
                <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="150px" />
              </td>

            </tr>
          </tbody>
        </table>
        <hr />
        <table class="table table-borderless" style="margin-bottom: 2px !important">
          <tbody>
            <tr>
              <td class="col-md-2 col-sm-2"><strong>MATA KULIAH</strong></td>
              <td>: <?= $nm_mk; ?></td>
              <td class="col-md-2 col-sm-2"><strong>TAHUN AJARAN</strong></td>
              <td>: <?= $nm_smt; ?></td>
            </tr>
            <tr>
              <td><strong>NAMA DOSEN</strong></td>
              <td>: <?= $nm_ptk; ?></td>
              <td><strong>PARTISIPASI</strong></td>
              <td>: <?= $partisipasi; ?></td>
            </tr>
            <tr>
              <td><strong>PROGRAM STUDI</strong></td>
              <td>: <?= $nm_lemb; ?></td>
            </tr>
          </tbody>
        </table>
        <?php
        echo $tabel_html;
        ?>
      </div>
    </section>
    
  </div>
  
</body>

</html>