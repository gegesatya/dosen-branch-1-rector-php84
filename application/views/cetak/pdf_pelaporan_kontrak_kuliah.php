<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Kontrak Kuliah</title>
  
  
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
      .table > thead > tr > th,
      .table > tbody > tr > th,
      .table > tfoot > tr > th,
      .table > thead > tr > td,
      .table > tbody > tr > td,
      .table > tfoot > tr > td {
        padding: 8px !important;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
      }
      .table > thead > tr > th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd;
      }
      .table > caption + thead > tr:first-child > th,
      .table > colgroup + thead > tr:first-child > th,
      .table > thead:first-child > tr:first-child > th,
      .table > caption + thead > tr:first-child > td,
      .table > colgroup + thead > tr:first-child > td,
      .table > thead:first-child > tr:first-child > td {
        border-top: 0;
      }
      .table > tbody + tbody {
        border-top: 2px solid #ddd;
      }
      .table .table {
        background-color: #fff;
      }

      .table-borderless th,
      .table-borderless td {
        border: 0px solid #fff !important;
        padding: 3px !important;
        font-size: 11px !important;
      }
      .table-black th,
      .table-black td {
        border: 1px solid #B0B0B0 !important;
        padding: 4px !important;
        font-size: 10px !important;
      }

      hr {
          margin-top: 1px;
          margin-bottom: 0px;
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
       display:inline-block;
       border: solid 1px #000;
       min-height:10px;
       width: 300px;
    }
    </style>
</head>
<body>

<div class="wrapper">
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>
          <img src="<?=FCPATH.'assets/image_web/logo-white-3.png';?>" width="150px" style="margin-top:10px;margin-right: 20px;" />
          </td>
          <td colspan="3">
            <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
            <p style="font-size: 12px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: https://www.wbi.ac.id</p>
          </td>
        </tr>
      </tbody>
    </table>
    <hr style="" />
    <h3 class="text-center">BERITA ACARA KONTRAK PERKULIAHAN DAN PENILAIAN</h3>
    <h4 class="text-center">SEMESTER <?=$this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;?></h4>
    <p>
      Pada hari ini, tanggal <?=$tgl_submit;?>, telah dilaporkan pelaksanaan sosialisasi kontrak perkuliahan oleh:
    </p>
    <table class="table table-borderless">
        <tr ><td style="width: 20% !important">NAMA DOSEN</td><td>: <?=$this->session->userdata('nm_ptk');?></td></tr>
        <tr><td>PROGRAM STUDI</td><td>: <?=$nm_lemb;?></td></tr>
    </table>
    <p>terhadap mahasiswa pada kelas perkuliahan berikut: </p>
    
    <table class="table table-black">
      
        <tr>
          <td rowspan="2" class="text-center">#</td>
          <td rowspan="2" class="text-center">Kelas</td>
          <td rowspan="2" class="text-center">Mata Kuliah</td>
          <td rowspan="2" class="text-center">Sikap</td>
          <td colspan="4" class="text-center">Sub Pengetahuan</td>
          <td rowspan="2" class="text-center">Pengetahuan</td>
          <td colspan="5" class="text-center">Sub Keterampilan</td>
          <td rowspan="2" class="text-center">Keterampilan</td>
        </tr>
        <tr>
          <td class="text-center">QUI</td>
          <td class="text-center">TTS</td>
          <td class="text-center">UTS</td>
          <td class="text-center">UAS</td>

          <td class="text-center">UKN</td>
          <td class="text-center">PRY</td>
          <td class="text-center">POR</td>
          <td class="text-center">PRD</td>
          <td class="text-center">PRE</td>
        </tr>
        <?php
          $i=1;
          foreach ($data as $key => $value) {
            ?>
            <tr>
              <td><?=$i;?></td>
              <td><?=$value->nm_kls;?></td>
              <td><?=$value->nm_mk;?></td>
              <td class="text-center"><?=$value->bobot_sikap;?></td>
              <td class="text-center"><?=$value->bobot_pengetahuan_quiz;?></td>
              <td class="text-center"><?=$value->bobot_pengetahuan_tugas_terstruktur;?></td>
              <td class="text-center"><?=$value->bobot_pengetahuan_uts;?></td>
              <td class="text-center"><?=$value->bobot_pengetahuan_uas;?></td>
              <td class="text-center"><?=$value->bobot_pengetahuan;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan_unjuk_kinerja;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan_proyek;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan_portofolio;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan_produk;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan_presentasi;?></td>
              <td class="text-center"><?=$value->bobot_keterampilan;?></td>
            </tr>
            <?php
            $i++;
          }
        ?>
      
    </table>
    <p style="margin:2px"><small>Keterangan: </small></p>
    <table class="table table-borderless">
      <tr><td><small>QUI = Quiz / pre test / post test</small></td><td><small>UKN = Unjuk Kinerja</small></td></tr>
      <tr><td><small>TTS = Tugas Terstruktur</small></td><td><small>PRY = Project</small></td></tr>
      <tr><td><small>UTS = Ujian Tengah Semester</small></td><td><small>POR = Portofolio</small></td></tr>
      <tr><td><small>UAS = Ujian Akhir Semester</small></td><td><small>PRD = Produk</small></td></tr>
      <tr><td><small></small></td><td><small>PRE = Presentasi</small></td></tr>
    </table>
    <p>Demikianlah berita acara ini dibuat dengan sebenar-benarnya.</p>
    <div style="page-break-after: always;"></div>

</div>
</body>
</html>
