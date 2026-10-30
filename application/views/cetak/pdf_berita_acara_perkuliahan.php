<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Berita Acara Perkuliahan</title>
  

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
    
    <?php
    if ($id_smt < 20181) {
    ?>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td>
              <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;"><?= $nm_mk; ?></h4>
              <p style="font-size: 12px;margin:0 0 5px !important">Dosen <?= $nm_ptk; ?></p>
              <p style="font-size: 12px;margin:0 0 5px !important">Program Studi <?= $nm_lemb; ?></p>
            </td>
            <td class="text-right">
              <h4 style="font-size: 12px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
              <p style="font-size: 10px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
              <p style="font-size: 10px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
            </td>

          </tr>
        </tbody>
      </table>
      <hr style="" />
      <div class="col-md-12 col-sm-12">
        <h4>Berita Acara Perkuliahan Semester <?= $nm_smt; ?></h4>
        <table class="table <?= $class_table; ?>">
          <thead>
            <tr>
              <th>Nos</th>
              <th>Tgl Realisasi</th>
              <th>Pertemuan Ke</th>
              <th>Bahan Kajian</th>
              <th>Jumlah Hadir</th>
            </tr>
          </thead>
          <?php
          $i = 1;
          $jumlah_konsisten = 0;
          $jumlah_hadir = 0;
          foreach ($list_pertemuan as $l) {
            $tgl_realisasi = !empty($l->tanggal_berita_acara) ? date('d-m-Y', strtotime($l->tanggal_berita_acara)) : 'Belum diisi';

            $hadir = $this->db->select('count(*) as jumlah')
              ->from('arsip_absen_detail d')
              ->join('arsip_absen_master m', 'm.uniq = d.id_absen_master')
              ->where(array(
                'status_kehadiran' => 'Hadir',
                'm.id_kls' => $l->id_kls, 'minggu_ke' => $l->minggu_ke
              ))
              ->get()->row()->jumlah;
            $jumlah_hadir += $hadir;


          ?>
            <tr>
              <td><?= $i; ?></td>

              <td><?= $tgl_realisasi; ?></td>

              <td><?= $l->minggu_ke; ?></td>
              <td><?= nl2br($l->berita_acara_rencana); ?></td>
              <td><?= $hadir; ?></td>
            </tr>
          <?php
          }
          ?>

        </table>
      </div>
    <?php
    } else {
    ?>
      <table class="table table-borderless">
        <tbody>
          <tr>

            <td>
              <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;"><?= $nm_mk; ?></h4>
              <p style="font-size: 12px;margin:0 0 5px !important">Dosen <?= $nm_ptk; ?></p>
              <p style="font-size: 12px;margin:0 0 5px !important">Program Studi <?= $nm_lemb; ?></p>
            </td>
            <td class="text-right">
              <h4 style="font-size: 12px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
              <p style="font-size: 10px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
              <p style="font-size: 10px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
            </td>

          </tr>
        </tbody>
      </table>
      <hr />

      <div class="col-md-12 col-sm-12">
        <h4>Berita Acara Perkuliahan Semester <?= $nm_smt; ?></h4>
        <table class="table <?= $class_table; ?>">
          <thead>
            <tr>
              <th>No</th>
              <th>Tgl Rencana</th>
              <th>Tgl Realisasi</th>
              <th>Minggu Ke</th>
              <th>Bahan Kajian/Sumber Belajar</th>
              <th>Kehadiran</th>
            </tr>
          </thead>
          <?php
          $i = 1;
          $jumlah_konsisten = 0;
          $jumlah_hadir = 0;
          foreach ($list_pertemuan as $l) {
            $tgl_realisasi = !empty($l->tanggal_berita_acara) ? date('d-m-Y', strtotime($l->tanggal_berita_acara)) : 'Belum diisi';
            $tgl_rencana = !empty($l->tanggal_rencana_perkuliahan) ? date('d-m-Y', strtotime($l->tanggal_rencana_perkuliahan)) : 'Belum diisi';

            $konsistensi = '';

            $hadir = $this->db->select('count(*) as jumlah')
              ->from('arsip_absen_detail d')
              ->join('arsip_absen_master m', 'm.uniq = d.id_absen_master')
              ->where(array(
                'status_kehadiran' => 'Hadir',
                'm.id_kls' => $l->id_kls, 'minggu_ke' => $l->minggu_ke
              ))
              ->get()->row()->jumlah;
            $jumlah_hadir += $hadir;

            if (!empty($l->tanggal_berita_acara)) {
              $konsistensi = $l->tanggal_berita_acara == $l->tanggal_rencana_perkuliahan ? 'Ya' : 'Tidak';
              $jumlah_konsisten = $l->tanggal_berita_acara == $l->tanggal_rencana_perkuliahan ? $jumlah_konsisten + 1 : $jumlah_konsisten;
            } else {
              $konsistensi = '-';
            }
            $bacaan_materi = !empty($l->bacaan_materi) ? nl2br($l->bacaan_materi) : '-';
          ?>
            <tr>
              <td><?= $i; ?></td>
              <td><?= $tgl_rencana; ?></td>
              <td><?= $tgl_realisasi; ?></td>
              <td><?= $l->minggu_ke; ?></td>
              <td><?= nl2br($l->berita_acara_rencana) . '<br><small><b>Sumber Belajar :</b><br>' . $bacaan_materi . '</small>'; ?></td>
              <td><?= $hadir; ?></td>
            </tr>
          <?php
            $i++;
          }
          
          ?>
        </table>
      </div>
      <!-- <div class="col-md-12" style="margin-top:25px">
          <table class="table <?= $class_table; ?>">
            <tbody>
              <tr><td>Jumlah Pelaksanaan Pembelajaran Konsisten</td><td><?= $jumlah_konsisten . ' (' . $persen_konsisten . ' %)'; ?></td></tr>
              <tr><td>Jumlah Total Mahasiswa Dalam Kelas</td><td><?= $statistik['jumlah_mahasiswa']; ?></td></tr>
              <tr><td>Rata-rata Kehadiran</td><td><?= $rata_rata_kehadiran; ?></td></tr>
              <tr><td>Jumlah Pertemuan Teori dalam satu semester</td><td><?= $statistik['jumlah_teori']; ?></td></tr>
              <tr><td>Jumlah Pertemuan Praktek dalam satu semester</td><td><?= $statistik['jumlah_praktek']; ?></td></tr>
              <tr><td>Jumlah Pertemuan dalam satu semester</td><td><?= $statistik['jumlah_pertemuan']; ?></td></tr>
            </tbody>
          </table> 
        </div> -->

    <?php
    }
    ?>
    
  </div>
  
</body>

</html>