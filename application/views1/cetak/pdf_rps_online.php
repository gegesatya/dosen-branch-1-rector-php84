<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Preview RPS</title>
  

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
      padding: 5px !important;
      font-size: 11px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #7a7a7a !important;
      padding: 5px !important;
      font-size: 12px !important;
      vertical-align: top;

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

    .divTable {
      display: table;
      width: 100%;
    }

    .divTable {
      display: table;
      width: 100%;
    }

    .divTableRow {
      display: inline-block;
    }

    .divTableHeading {
      background-color: #EEE;
      display: table-header-group;
    }

    .divTableCell,
    .divTableHead {
      border: 1px solid #7a7a7a;
      display: table-cell;
      padding: 3px 10px;
    }

    .divTableHeading {
      background-color: #EEE;
      display: table-header-group;
      font-weight: bold;
    }

    .divTableFoot {
      background-color: #EEE;
      display: table-footer-group;
      font-weight: bold;
    }

    .divTableBody {
      display: table-row-group;
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
  

  
  <section>
    <?php
    $tanggal_usulan = !empty($data_rps->tanggal_rps) ? date('d-m-Y', strtotime($data_rps->tanggal_rps)) : '-';
    $tanggal_pengesahan = !empty($data_rps->tanggal_pengesahan) ? date('d-m-Y', strtotime($data_rps->tanggal_pengesahan)) : '-';
    ?>

    <table autosize="1" class="table table-black" style="margin-bottom: 3px !important;">
      <tbody>
        <tr>
          <td class="text-center" style="padding:10px;" colspan="2">
            <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="160px" />
          </td>
          <td class="text-center" style="vertical-align: middle;" colspan=3>
            <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
            <p style="font-size: 12px;margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
          </td>
          <td class="text-center" style="vertical-align: middle; width:100px">
            <h4><strong>KODE REVISI</strong></h4>
            <h1><strong><?= $data_rps->kode_revisi; ?></strong></h1>
          </td>
        </tr>
        <tr>
          <td class="text-center" colspan="6">
            <h2><strong>RENCANA PEMBELAJARAN SEMESTER</strong></h2>
          </td>

        </tr>
        <tr>
          <td style="width: 120px;">Jenis Dokumen: <br>FORMULIR MUTU</td>
          <td style="width: 180px;">Disusun Oleh: <br><br><br><br>(<?= ucwords(strtolower($data_rps->nm_ptk)); ?>)<br>Tanggal: <?= $tanggal_usulan; ?></td>
          <td colspan="2">Dosen Pengampu:<br><?= $data_rps->dosen_pengampu; ?></td>
          <?php
          if ($koordinator_rps->num_rows() > 0) {
          ?>
            <td colspan="2">Disetujui Oleh:<br><?= $koordinator_rps->row()->jabatan; ?><br><br><br>(<?= $koordinator_rps->row()->nm_koordinator; ?>)<br>Tanggal:</td>
          <?php
          } else {
          ?>
            <td colspan="2">Disetujui Oleh:<br><br><br><br>(_________________________________)<br>Tanggal:</td>
          <?php
          }
          ?>

        </tr>
        <tr>
          <td colspan="2">
            Program Studi:<br>
            <?php
            if ($mk_prodi <> 'NIL') {
              echo "<ol>";
              foreach ($mk_prodi as $key => $value) {
                echo "<li>" . $value->nm_lemb . '</li>';
              }
              echo "</ol>";
            }
            ?>
          </td>
          <td style="width: 5px !important;">
            Sem:<br>
            <?php
            if ($mk_prodi <> 'NIL') {

              echo "<ul>";
              foreach ($mk_prodi as $key => $value) {
                echo "<li>" . substr($value->nm_kls, 1, 1) . '</li>';
              }
              echo "</ul>";
            }
            ?>
          </td>
          <td>
            Nama Matakuliah:<br>
            <?php
            if ($mk_prodi <> 'NIL') {
              echo "<ul>";
              foreach ($mk_prodi as $key => $value) {
                echo "<li>" . $value->nm_mk . '</li>';
              }
              echo "</ul>";
            }
            ?>
          </td>
          <td>
            Kode:<br>
            <?php
            if ($mk_prodi <> 'NIL') {
              echo "<ul>";
              foreach ($mk_prodi as $key => $value) {
                echo "<li>" . $value->kode_mk . ' (' . $value->nm_prodi_english . ')</li>';
              }
              echo "</ul>";
            }
            ?>
          </td>
          <td>
            Bobot (SKS):<br>
            <?php
            if ($mk_prodi <> 'NIL') {
              echo "<ul>";
              foreach ($mk_prodi as $key => $value) {
                echo "<li>" . $value->sks_mk * 1 . '</li>';
              }
              echo "</ul>";
            }
            ?>
          </td>
        </tr>
        <tr>
          <td colspan="6"><b>Capaian Pembelajaran Lulusan yang dibebankan kepada matakuliah</b></td>
        </tr>
        <tr>
          <td colspan="2"><b>Sikap</b></td>
          <td colspan="4" style="word-wrap: break-word">
            <div>
              <?= strip_tags($data_rps->capaian_sikap, '<br><p>'); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2"><b>Pengetahuan</b></td>
          <td colspan="4" style="word-wrap: break-word">
            <?= strip_tags($data_rps->capaian_pengetahuan, '<br><p>'); ?><br>
          </td>
        </tr>
        <tr>
          <td colspan="2"><b>Keterampilan Umum</b></td>
          <td colspan="4" style="word-wrap: break-word">
            <?= strip_tags($data_rps->capaian_keterampilan_umum, '<br><p>'); ?><br>
          </td>
        </tr>
        <tr>
          <td colspan="2"> <b>Keterampilan Khusus</b></td>
          <td colspan="4" style="word-wrap: break-word">
            <?= strip_tags($data_rps->capaian_keterampilan_khusus, '<br><p>'); ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">Deskripsi Matakuliah</td>
          <td colspan="4"><?= strip_tags($data_rps->deskripsi_mata_kuliah, '<br><p>'); ?></td>
        </tr>
        <tr>
          <td colspan="2">Capaian Pembelajaran Matakuliah</td>
          <td colspan="4"><?= strip_tags($data_rps->capaian_pembelajaran_mata_kuliah, '<br><p>'); ?></td>
        </tr>
      </tbody>
    </table>


    <!-- <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
      <div class="divTableBody">
        <div class="divTableRow">
          <div class="divTableCell" style="padding-left: 100px;"><b><u>Sikap</u></b><br><?= strip_tags($data_rps->capaian_sikap, '<br><p>'); ?></div>
        </div>
        <div class="divTableRow">
          <div class="divTableCell" style="padding-left: 100px;"><b><u>Pengetahuan</u></b><br><?= strip_tags($data_rps->capaian_pengetahuan, '<br><p>'); ?></div>
        </div>
        <div class="divTableRow">
          <div class="divTableCell" style="padding-left: 100px;"><b><u>Keterampilan Umum</u></b><br><?= strip_tags($data_rps->capaian_keterampilan_umum, '<br><p>'); ?></div>
        </div>
        <div class="divTableRow">
          <div class="divTableCell" style="padding-left: 100px;"><b><u>Keterampilan Khusus</u></b><br><?= strip_tags($data_rps->capaian_keterampilan_khusus, '<br><p>'); ?></div>
        </div>
        <div class="divTableRow">
          <div class="divTableCell"><b><u>Deskripsi Matakuliah</u></b><br><?= strip_tags($data_rps->deskripsi_mata_kuliah, '<br><p>'); ?></div>
        </div>
        <div class="divTableRow">
          <div class="divTableCell"><b><u>Capaian Pembelajaran Matakuliah</u></b><br><?= strip_tags($data_rps->capaian_pembelajaran_mata_kuliah, '<br><p>'); ?></div>
        </div>
      </div>
    </div> -->

    <div style="page-break-after: always;"></div>
    <h3><b><u>DETAIL PERTEMUAN RPS</u></b></h3>
    <table class="table table-black" style="margin-bottom: 3px !important">
      <thead>
        <tr>
          <th rowspan="2">Minggu<br>Ke</th>
          <th rowspan="2">Perte-<br>muan</th>
          <th colspan="2">Waktu<br>(Menit)</th>
          <th rowspan="2">Referensi</th>
          <th rowspan="2">Kemampuan Akhir Tiap <br>Tahapan Belajar</th>
          <th rowspan="2">Bahan Kajian<br>(Materi dan Pustaka)</th>
          <th colspan="2">Metode Pembelajaran</th>
          <th rowspan="2">Pengalaman Belajar <br>(Evaluasi)</th>
          <th rowspan="2">Kriteria/Indikator<br> Penilaian</th>
          <th rowspan="2">Bobot <br>Penilaian (%)</th>

        </tr>
        <tr>
          <th>T</th>
          <th>P</th>
          <th>Daring</th>
          <th>Luring</th>
        </tr>
        <tr>
          <th class="text-center">(1)</th>
          <th class="text-center">(2)</th>
          <th class="text-center">(3)</th>
          <th class="text-center">(4)</th>
          <th class="text-center">(5)</th>
          <th class="text-center">(6)</th>
          <th class="text-center">(7)</th>
          <th class="text-center">(8)</th>
          <th class="text-center">(9)</th>
          <th class="text-center">(10)</th>
          <th class="text-center">(11)</th>
          <th class="text-center">(12)</th>
        <tr>
      </thead>
      <tbody>
        <?php
        foreach ($data_rps_detail as $key => $value) {
        ?>
          <tr>
            <td class="text-center"><?= $value->kolom_1; ?></td>
            <td class="text-center"><?= $value->kolom_2; ?></td>
            <td class="text-center"><?= $value->kolom_3; ?></td>
            <td class="text-center"><?= $value->kolom_4; ?></td>
            <td><?= $value->kolom_5; ?></td>
            <td><?= $value->kolom_6; ?></td>
            <td><?= $value->kolom_7; ?></td>
            <td><?= $value->kolom_8; ?></td>
            <td><?= $value->kolom_9; ?></td>
            <td><?= $value->kolom_10; ?></td>
            <td><?= $value->kolom_11; ?></td>
            <td class="text-center"><?= $value->kolom_12; ?></td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
    <br>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>
            <h3><b><u>REFERENSI UTAMA</u></b></h3>
            <br>
            <?= strip_tags($data_rps->referensi, '<br><p>'); ?>
          </td>
        <tr>
          <td>
            <h3><b><u>TUGAS MAHASISWA DALAM 1 SEMESTER</u></b></h3>
            <br>
            <?= strip_tags($data_rps->tugas_mahasiswa, '<br><p>'); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
  

  
</body>

</html>