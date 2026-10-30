<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Absen Mahasiswa</title>
  

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

<body <?= $style; ?>>
  
  <div class="wrapper">
    
    <?php


    ?>
    <h3 class="text-center">DAFTAR ABSEN MAHASISWA</h3>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>DOSEN</td>
          <td>: <?= $nm_ptk; ?></td>
          <td>PRODI</td>
          <td>: <?= $nm_lemb; ?></td>
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
    <?php
    if ($absen_master->num_rows() == 0) {
    ?>
      <div class="well">
        <h2>Kelas kuliah ini belum mengisi absen.</h2>
      </div>
    <?php
    } else {
    ?>
      <div class="col-md-12 col-sm-12">
        <h3>Absen Pertemuan Teori</h3>
        <table class="table <?= $class_table; ?>">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">NIM</th>
              <th class="text-center">Nama</th>
              <?php
              foreach ($absen_master->result() as $key => $value) {
                $tgl = date('d/m', strtotime($value->tanggal_berita_acara));
              ?>
                <th class="text-center"><?= $tgl; ?><br>(<?= $value->minggu_ke; ?>)</th>
              <?php
              }
              ?>
              <th>%</th>
            </tr>
          </thead>
          <?php
          $i = 1;
          $body_tabel = '';
          foreach ($absen_detail as $key => $value) {
            $uniq_master = '';
            $count_total_hadir = 0;
            $row_data = '';

            foreach ($absen_master->result() as $key => $m) {
              $status_kehadiran = '';
              $uniq_master = $m->uniq;
              $detail_per_mahasiswa = $this->db->select('*')
                ->from('arsip_absen_detail')
                ->where(array(
                  'id_absen_master' => $uniq_master,
                  'id_reg_pd' => $value->id_reg_pd
                ))->get();

              $detail_per_mahasiswa_praktek = $this->db->select('d.*, m.kategori_pertemuan')
                ->from('arsip_absen_master m')
                ->join('arsip_absen_detail d', 'm.uniq = d.id_absen_master')
                ->where(array(
                  'id_reg_pd' => $value->id_reg_pd,
                  'kategori_pertemuan' => 'Praktek',
                  'tanggal_berita_acara' => $m->tanggal_berita_acara
                ))->get();
              $text_tampil = '';

              if ($detail_per_mahasiswa->num_rows() > 0) {
                $status_kehadiran_teori = $detail_per_mahasiswa->row()->status_kehadiran;

                if ($status_kehadiran_teori == 'Hadir') {
                  $text_tampil = '<span class="label label-primary">H</span>';
                  $count_total_hadir += 1;
                } else {
                  $text_tampil = '<span class="label label-danger">A</span>';
                }
              }
              $row_data .= '<td class="text-center">' . $text_tampil . '</td>';
            }

            $count_teori = $total_hadir = $persen = 0;
            $count_teori = $this->db->select('*')
              ->from('arsip_absen_master m')
              ->join('arsip_absen_detail d', 'm.uniq = d.id_absen_master')
              ->where(['id_kls' => $id_kls, 'id_reg_pd' => $value->id_reg_pd, 'status_kehadiran' => 'Hadir'])
              ->get()->num_rows();


            $total_hadir = $count_teori;

            if ($total_pertemuan == 0) {
              $persen = 0;
            } else {
              $persen = round(($total_hadir / $total_pertemuan) * 100, 2);
            }

            $body_tabel .= '<tr>
                                            <td>' . $i . '</td>
                                            <td style="min-width:90px">' . $value->nipd . '</td>
                                            <td style="min-width:250px">' . $value->nm_pd . '</td>
                                            ' . $row_data . '<td class="text-center">' . $persen . '</td>
                                          </tr>';
            $i++;
          }
          ?>
          <?= $body_tabel; ?>

        </table>
      </div>

    <?php
    }
    ?>
  </div>
</body>

</html>