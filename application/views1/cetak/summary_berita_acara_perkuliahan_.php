<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Summary BAP</title>
  

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


</head>

<body>
  
  <div class="wrapper">
    

    <div class="col-md-12 col-sm-12">
      <table class="table <?= $class_table; ?>">
        <thead>
          <tr>
            <th>No</th>
            <th>Pertemuan | Tanggal</th>

            <th>Bahan Kajian/Sumber Belajar</th>
            <th>Kehadiran</th>
          </tr>
        </thead>
        <?php
        $i = 1;
        $jumlah_konsisten = 0;
        $jumlah_hadir = 0;
        foreach ($list_pertemuan as $l) {
          $bacaan_materi = '';
          $tgl_realisasi = !empty($l->tanggal_realisasi) ? 'Realisasi: ' . date('d-m-Y', strtotime($l->tanggal_realisasi)) : '';
          $tgl_rencana = !empty($l->tanggal_rencana) ? date('d-m-Y', strtotime($l->tanggal_rencana)) : 'Belum diisi';

          

          $hadir = $this->db->select('count(*) as jumlah', FALSE)
            ->from('siakad_absen_detail_baru')
            ->where(array(
              'meet_' . $i => 'H',
              'id_jadwal' => $l->id_jadwal
            ))
            ->get()->row()->jumlah;
          $jumlah_hadir += $hadir;

          if (empty($l->bacaan_materi) || $l->bacaan_materi == '') {
            $bacaan_materi1 = $this->db->where(['id_rps' => $id_rps, 'kolom_1' => $l->pertemuan])->get('siakad_rps_online_detail')->row()->kolom_5;
            $bacaan_materi = !empty($bacaan_materi1) ? nl2br($bacaan_materi1) : '-';
          } else {
            $bacaan_materi =  nl2br($l->bacaan_materi);
          }

        ?>
          <tr>
            <td><?= $i; ?></td>
            <td><?= 'Pertemuan: ' . $l->pertemuan . '<br>Rencana: ' . $tgl_rencana . '<br>' . $tgl_realisasi; ?></td>
            <td><?= nl2br($l->berita_acara_rencana) . '<br><br><b>Sumber Belajar :</b><br>' . $bacaan_materi; ?></td>
            <td class="text-center"><?= $hadir . '/' . $total; ?></td>
          </tr>
        <?php
          $i++;
        }

        ?>
      </table>
      <table class="table table-borderless" style="margin-top: 20px">
        <tr>
          <td class="text-right">
            <div class="col-md-3 text-center">
              Dosen Pengampu Mata Kuliah,
              <br>
              <br>
              <br>
              <br>
              (<?= $nm_ptk; ?>)<br>
              <?= 'NIDN : ' . $nidn; ?>
            </div>
          </td>
        </tr>
      </table>
    </div>

    
  </div>
  
</body>

</html>