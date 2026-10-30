<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Nilai Mata Kuliah</title>
  
  
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
        padding: 5px !important;
        font-size: 11px !important;
      }
      .table-black th,
      .table-black td {
        border: 1px solid #C0C0C0 !important;
        padding: 6px !important;
        font-size: 11px !important;
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
       display:inline-block;
       border: solid 1px #000;
       min-height:10px;
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
                <td >
                    <h2 style="font-size: 14px;margin:0 0 3px !important; font-weight: bold;">LAPORAN NILAI MAHASISWA</h2>
                    <h3>SEMESTER <?=$nm_smt;?></h3>
                    <?=strtoupper($nm_lemb);?>
                </td>
                
                <td class="text-right">
                    <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
                    <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
                    <p style="font-size: 12px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
                </td>
            </tr>
        </tbody>
    </table>
    <hr style="" />
    <table class="table table-borderless" width="50%">
      <tr>
        <td style="width:150px">MATA KULIAH</td><td>: <?=strtoupper($nm_mk);?></td>
      </tr>
      <tr>
        <td>DOSEN</td><td>: <?=strtoupper($nm_ptk);?></td>
      </tr>
     
    </table>
  <?php
  if($metode_nilai == 1){
  ?>
    <table class="table table-black">
        <thead>
          <tr>
            <th rowspan="2" style="vertical-align: middle;">No.</th>
            <th rowspan="2" style="vertical-align: middle;">NIM</th>
            <th rowspan="2" style="vertical-align: middle;">Nama</th>
            <th>Tugas</th>
            <th>Partisipasi</th>
            <th>UTS</th>
            <th>UAS</th>
            <th rowspan="2" style="vertical-align: middle;">Angka</th>
            <th rowspan="2" style="vertical-align: middle;">Mutu</th>
            <th rowspan="2" style="vertical-align: middle;">Indeks</th>
          </tr>
          <tr>
            
            <th><?=$tugas;?> %</th>
            <th><?=$partisipasi;?> %</th>
            <th><?=$uts;?> %</th>
            <th><?=$uas;?> %</th>
            
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          foreach ($data_nilai as $key => $value) {
            ?>
              <tr>
                <td class="text-right"><?=$no;?>.</td>
                <td><?=$value->nipd;?></td>
                <td><?=strtoupper((string)$value->nm_pd);?></td>
                <td class="text-center"><?=$value->nilai_tugas;?></td>
                <td class="text-center"><?=$value->nilai_partisipasi;?></td>
                <td class="text-center"><?=$value->nilai_uts;?></td>
                <td class="text-center"><?=$value->nilai_uas;?></td>
                <td class="text-center"><?=$value->nilai_angka;?></td>
                <td class="text-center"><b><?=!empty($value->nilai_huruf) ? $value->nilai_huruf : '-';?></b></td>
                <td class="text-center"><?=$value->nilai_indeks;?></td>
              </tr>
            <?php
            $no++;
          }
        ?>
        </tbody>
    </table>
  <?php    
  }
  elseif($metode_nilai == 2){
  ?>    
    <table class="table table-black">
        <thead>
          <tr>
            <th rowspan="2" style="vertical-align: middle;">No.</th>
            <th rowspan="2" style="vertical-align: middle;">NIM</th>
            <th rowspan="2" style="vertical-align: middle;">Nama</th>
            <th style="background-color: #ddd">Sikap</th>
            <th>Nilai Harian/Quiz</th>
            <th>UTS</th>
            <th>UAS</th>
            <th style="background-color: #ddd">Pengetahuan</th>
            <th>Tugas</th>
            <th>Unjuk Kinerja</th>
            <th>Presentasi</th>
            <th style="background-color: #ddd">Keterampilan</th>
            <th rowspan="2" style="vertical-align: middle;">Angka</th>
            <th rowspan="2" style="vertical-align: middle;">Mutu</th>
            <th rowspan="2" style="vertical-align: middle;">Indeks</th>
          </tr>
          <tr>
            
            <th style="background-color: #ddd"><?=$sikap;?> %</th>
            <th><?=$pengetahuan_kuis;?> %</th>
            <th><?=$pengetahuan_uts;?> %</th>
            <th><?=$pengetahuan_uas;?> %</th>
            <th style="background-color: #ddd"><?=$pengetahuan;?> %</th>
            <th><?=$keterampilan_tugas;?> %</th>
            <th><?=$keterampilan_unjuk_kinerja;?> %</th>
            <th><?=$keterampilan_presentasi;?> %</th>
            <th style="background-color: #ddd"><?=$keterampilan;?> %</th>
            
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          foreach ($data_nilai as $key => $value) {
            ?>
              <tr>
                <td class="text-right"><?=$no;?>.</td>
                <td><?=$value->nipd;?></td>
                <td><?=strtoupper((string)$value->nm_pd);?></td>
                <td class="text-center" style="background-color: #ddd"><?=$value->n_sikap;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_kuis;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_uts;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_uas;?></td>
                <td class="text-center" style="background-color: #ddd"><?=$value->n_pengetahuan;?></td>
                <td class="text-center"><?=$value->n_keterampilan_tugas;?></td>
                <td class="text-center"><?=$value->n_keterampilan_unjuk_kinerja;?></td>
                <td class="text-center"><?=$value->n_keterampilan_presentasi;?></td>
                <td class="text-center" style="background-color: #ddd"><?=$value->n_keterampilan;?></td>
                <td class="text-center"><?=$value->nilai_angka;?></td>
                <td class="text-center"><b><?=!empty($value->nilai_huruf) ? $value->nilai_huruf : '-';?></b></td>
                <td class="text-center"><?=$value->nilai_indeks;?></td>
              </tr>
            <?php
            $no++;
          }
        ?>
        </tbody>
    </table>
  <?php
  }elseif($metode_nilai == 3){
  ?>
    <table class="table table-black">
        <thead>
          <tr>
            <th rowspan="2" style="vertical-align: middle;">No.</th>
            <th rowspan="2" style="vertical-align: middle;">NIM</th>
            <th rowspan="2" style="vertical-align: middle;">Nama</th>
            <th>Penilaian Lapangan</th>
            <th>Penilaian Penulisan</th>
            <th>Pengujian Laporan</th>
            <th rowspan="2" style="vertical-align: middle;">Angka</th>
            <th rowspan="2" style="vertical-align: middle;">Huruf</th>
            <th rowspan="2" style="vertical-align: middle;">Indeks</th>
          </tr>
          <tr>
            <th><?=$penilaian_lapangan;?> %</th>
            <th><?=$penilaian_penulisan;?> %</th>
            <th><?=$pengujian_laporan;?> %</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          foreach ($data_nilai as $key => $value) {
            ?>
              <tr>
                <td class="text-right"><?=$no;?>.</td>
                <td><?=$value->nipd;?></td>
                <td><?=$value->nm_pd;?></td>
                <td class="text-center"><?=$value->n_penilaian_lapangan;?></td>
                <td class="text-center"><?=$value->n_penilaian_penulisan;?></td>
                <td class="text-center"><?=$value->n_pengujian_laporan;?></td>
                <td class="text-center"><?=$value->nilai_angka;?></td>
                <td class="text-center"><b><?=!empty($value->nilai_huruf) ? $value->nilai_huruf : '-';?></b></td>
                <td class="text-center"><?=$value->nilai_indeks;?></td>
              </tr>
            <?php
            $no++;
          }
        ?>
        </tbody>
    </table>  
  <?php  
  }elseif($metode_nilai == 4){
  ?>
    <table class="table table-black">
        <thead>
          <tr>
            <th rowspan="3" style="vertical-align: middle;">No.</th>
            <th rowspan="3" style="vertical-align: middle;">NIM</th>
            <th rowspan="3" style="vertical-align: middle;">Nama</th>
            <th rowspan="2" style="background-color: #ddd">Sikap</th>
            <th colspan="5">Pengetahuan</th>
            <th colspan="6">Keterampilan</th>
            <th rowspan="3" style="vertical-align: middle;">Angka</th>
            <th rowspan="3" style="vertical-align: middle;">Mutu</th>
            <th rowspan="3" style="vertical-align: middle;">Indeks</th>
          </tr>
          <tr>
            
            
            <th>QUI</th>
            <th>TTS</th>
            <th>UTS</th>
            <th>UAS</th>
            <th style="background-color: #ddd">FIN1</th>
            <th>UKN</th>
            <th>PRY</th>
            <th>POR</th>
            <th>PRD</th>
            <th>PRE</th>
            <th style="background-color: #ddd">FIN2</th>
            
          </tr>
          <tr>          
            <th style="background-color: #ddd"><?=$bobot_sikap;?> %</th>
            <th><?=$bobot_pengetahuan_quiz;?> %</th>
            <th><?=$bobot_pengetahuan_tugas_terstruktur;?> %</th>
            <th><?=$bobot_pengetahuan_uts;?> %</th>
            <th><?=$bobot_pengetahuan_uas;?> %</th>
            <th style="background-color: #ddd"><?=$bobot_pengetahuan;?> %</th>
            <th><?=$bobot_keterampilan_unjuk_kinerja;?> %</th>
            <th><?=$bobot_keterampilan_proyek;?> %</th>
            <th><?=$bobot_keterampilan_portofolio;?> %</th>
            <th><?=$bobot_keterampilan_produk;?> %</th>
            <th><?=$bobot_keterampilan_presentasi;?> %</th>
            <th style="background-color: #ddd"><?=$bobot_keterampilan;?> %</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          foreach ($data_nilai as $key => $value) {
            ?>
              <tr>
                <td class="text-right"><?=$no;?>.</td>
                <td><?=$value->nipd;?></td>
                <td><?=strtoupper((string)$value->nm_pd);?></td>
                <td class="text-center" style="background-color: #ddd;"><?=$value->n_sikap_2;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_2_quiz;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_2_tugas_terstruktur;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_2_uts;?></td>
                <td class="text-center"><?=$value->n_pengetahuan_2_uas;?></td>
                <td class="text-center" style="background-color: #ddd"><?=$value->n_pengetahuan_2;?></td>
                <td class="text-center"><?=$value->n_keterampilan_2_unjuk_kinerja;?></td>
                <td class="text-center"><?=$value->n_keterampilan_2_proyek;?></td>
                <td class="text-center"><?=$value->n_keterampilan_2_portofolio;?></td>
                <td class="text-center"><?=$value->n_keterampilan_2_produk;?></td>
                <td class="text-center"><?=$value->n_keterampilan_2_presentasi;?></td>
                <td class="text-center" style="background-color: #ddd"><?=$value->n_keterampilan_2;?></td>
                <td class="text-center"><?=$value->nilai_angka;?></td>
                <td class="text-center"><b><?=!empty($value->nilai_huruf) ? $value->nilai_huruf : '-';?></b></td>
                <td class="text-center"><?=$value->nilai_indeks;?></td>
              </tr>
            <?php
            $no++;
          }
        ?>
        </tbody>
    </table>  
  <?php
  }
  ?>
    <table class="table table-borderless" style="margin-top: 20px">
      <tr>
        
        <td class="text-right">
        <div class="col-md-3 text-center">
          Deli Serdang, ___/____/______<br>Dosen Pengasuh
          <br>
          <br>
          <br>
          <br>
          ( <?=$nm_ptk;?> )
         </div> 
        </td>
      </tr>
    </table>
  
</div>

</body>
</html>
