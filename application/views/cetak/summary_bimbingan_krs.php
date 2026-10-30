<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>LEMBAR PERWALIAN</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <style type="text/css">
    body {
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 13px;
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
      padding: 5px !important;
      font-size: 12px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 6px !important;
      font-size: 12px !important;
    }

    hr {
      margin-top: 1px;
      margin-bottom: 10px;
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
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>
            <h2 style="font-size: 14px;margin:0 0 3px !important; font-weight: bold;">LEMBAR PERWALIAN</h2>
            <h3>SEMESTER <?= $nm_smt; ?></h3>

          </td>

          <td class="text-right">
            <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
            <p style="font-size: 12px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
          </td>
        </tr>
      </tbody>
    </table>
    <hr />
    <table class="table table-borderless" width="50%">

      <tr>
        <td>DOSEN</td>
        <td>: <?= strtoupper($nm_ptk); ?></td>
      </tr>

    </table>

    <table class="table table-black">
      <thead>
        <tr>
          <th style="vertical-align: middle;">No.</th>
          <th style="vertical-align: middle;">NIM | Nama</th>
          <th style="vertical-align: middle;">Tanggal</th>
          <th>Konsultasi</th>
        </tr>

      </thead>
      <tbody>
        <?php
        $no = 1;
        foreach ($krs as $key => $value) {
        ?>
          <tr>
            <td class="text-right" style="vertical-align: top;"><?= $no; ?>.</td>
            <td style="vertical-align: top;"><?= $value->nipd . '<br>' . $value->nm_pd . '<br>(IPK : ' . $value->ip_kumulatif . ', SKS Total : ' . $value->sks_kum . ')'; ?></td>
            <td class="text-center" style="vertical-align: top;">
              <p><?= 'Diajukan : ' . tgl_indo_short(date('Y-m-d', strtotime($value->pengajuan_date))); ?></p>
              <p><?= 'Disetujui : ' . tgl_indo_short(date('Y-m-d', strtotime($value->approved_date))); ?></p>

            </td>
            <td class="text-left" style="vertical-align: top;"><?= $value->catatan_dosen_wali; ?></td>

          </tr>
        <?php
          $no++;
        }
        ?>
      </tbody>
    </table>

    <!--  <table class="table table-borderless" style="margin-top: 20px">
      <tr>

        <td class="text-right">
          <div class="col-md-3 text-center">
            Deli Serdang, <?= date('d-m-Y', strtotime($tanggal_lapor)); ?><br>Dosen Pengasuh
            <br>
            <br>
            <br>
            <br>
            ( <?= $nm_ptk; ?> )
          </div>
        </td>
      </tr>
    </table> -->
  </div>
</body>

</html>