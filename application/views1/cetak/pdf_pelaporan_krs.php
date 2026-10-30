<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Laporan Pelaksanaan KRS</title>
  
  
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
  
    <?php
      $ch_diarahkan = '';
      $ch_tidak_diarahkan = '';
      $ch_identifikasi_1 = '';
      $ch_identifikasi_2 = '';
      $ch_identifikasi_3 = '';
      $ch_khs_1 = '';
      $ch_khs_2 = '';
     

      if($pengarahan_mahasiswa == 'Diarahkan'){
        $ch_diarahkan = 'checked = "checked"';
      }
      if($pengarahan_mahasiswa == 'Tidak Diarahkan'){
        $ch_tidak_diarahkan = 'checked = "checked"';
      }
      
      if($identifikasi_akar_permasalahan == 'Diidentifikasi dan diberikan solusi'){
        $ch_identifikasi_1 = 'checked = "checked"';
      }

      if($identifikasi_akar_permasalahan == 'Diidentifikasi dan tidak diberikan solusi'){
        $ch_identifikasi_2 = 'checked = "checked"';
      }

      if($identifikasi_akar_permasalahan == 'Tidak diidentifikasi dan tidak diberikan solusi'){
        $ch_identifikasi_3 = 'checked = "checked"';
      }

      if($khs_diterima == 'Ya'){
        $ch_khs_1 = 'checked = "checked"';
      }

      if($khs_diterima == 'Tidak'){
        $ch_khs_2 = 'checked = "checked"';
      }
     
  ?>
    <table class="table table-borderless">
        <tbody>
            <tr>
                <td>
                <img src="<?=FCPATH.'assets/image_web/logo-white-3.png';?>" width="150px" style="margin-top:10px;margin-right: 20px;" />
                </td>
                <td colspan="3">
                    <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
                    <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
                    <p style="font-size: 12px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
                </td>
               
            </tr>
        </tbody>
    </table>
    <hr style="" />
    <h3 class="text-center">BERITA ACARA KONSULTASI AKADEMIK</h3>
    <h4 class="text-center">SEMESTER <?=$this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;?></h4>
    <p>
      Pada hari ini, tanggal <?=$tgl_submit;?>, telah dilakukan pembimbingan akademik dan konsultasi akademik oleh:
    </p>
    <table class="table table-borderless">
        <tr ><td style="width: 20% !important">NAMA DOSEN</td><td>: <?=$this->session->userdata('nm_ptk');?></td></tr>
        <tr><td>PROGRAM STUDI</td><td>: <?=$nm_lemb;?></td></tr>
    </table>
    <p>terhadap mahasiswa (daftar hadir terlampir), dari Program Studi <?=$nm_lemb;?>, Politeknik Wilmar Bisnis Indonesia. </p>
    <p>Hasil dari pembimbingan akademik tersebut adalah:</p>

    <table class="table table-black">
      <tbody>
        
        <tr>
          <td>1.</td>
          <td>Jumlah Total Bimbingan </td>
          <td class="text-center"><span><?=$total_bimbingan;?></span></td>
        </tr>
        <tr>
          <td>2.</td>
          <td>Jumlah Mahasiswa Hadir Melakukan Tatap Muka dan Bimbingan </td>
          <td class="text-center"><span><?=$total_hadir;?></span></td>
        </tr>
        <tr>
          <td>3.</td>
          <td>Jumlah Mahasiswa Terdaftar dan Belum Melakukan Tatap Muka dan Bimbingan </td>
          <td class="text-center"><span><?=$total_tidak_hadir;?></span></td>
        </tr>
        <tr>
          <td>4.</td>
          <td colspan="2">Mahasiswa yang hadir melakukan tatapi muka diarahkan dalam memilih mata kuliah (Silahkan Pilih)
              <div class="gege-class">
                  <label class="radio-inline">
                    <input type="checkbox" 
                           name="pengarahan_1" 
                           id="pengarahan_1" 
                           value="Diarahkan" 
                           class="minimal pengarahan"  
                           <?=$ch_diarahkan;?>
                           > Diarahkan </label>
                  <label class="radio-inline">
                    <input type="checkbox" 
                           name="pengarahan_2" 
                           id="pengarahan_2" 
                           value="Tidak Diarahkan" 
                           class="minimal pengarahan" 
                           <?=$ch_tidak_diarahkan;?>
                          > Tidak Diarahkan </label>
              </div>
              <span class="help-block label label-danger"></span>
          </td>
        </tr>
        <tr>
          <td>5.</td>
          <td colspan="2">Rentang Indeks Prestasi Semester Sebelumnya
              <div class="gege-class">
                  <table class="table table-bordered table-condensed">
                      <thead>
                          <tr>
                              <td>Nilai</td><td class="text-center">Jumlah</td>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td>> 3,00</td>
                              <td class="text-center"><span><?=$ip_kat_1;?></span></td>
                          </tr>
                          <tr>
                              <td>2,51 sd 3,00 </td>
                              <td class="text-center"><span><?=$ip_kat_2;?></span></td>
                          </tr>
                          <tr>
                              <td>2,00 sd 2,50 </td>
                              <td class="text-center"><span><?=$ip_kat_3;?></span></td>
                          </tr>
                          <tr>
                              <td>< 2 </td>
                              <td class="text-center"><span><?=$ip_kat_4;?></span></td>
                          </tr>
                      </tbody>
                      
                  </table>
              </div>
              <span class="help-block label label-danger"></span>
          </td>
        </tr>
        <tr>
          <td>6.</td>
          <td colspan="2">Mahasiswa yang memiliki Indeks Prestasi < 2.5 telah diidentifikasi akar permasalahan dan telah diberikan solusinya:
              <div class="gege-class">
                  <label class="radio-inline">
                    <input type="checkbox" 
                          name="identifikasi_1" 
                          id="identifikasi_1" 
                          value="Diidentifikasi dan diberikan solusi" 
                          class="minimal identifikasi" <?=$ch_identifikasi_1;?> 
                  > Diidentifikasi dan diberikan solusi </label>
                  <label class="radio-inline">
                    <input type="checkbox" 
                          name="identifikasi_2" 
                          id="identifikasi_2" 
                          value="Diidentifikasi dan tidak diberikan solusi" 
                          class="minimal identifikasi" <?=$ch_identifikasi_2;?> 
                  > Diidentifikasi dan tidak diberikan solusi </label>
                  <label class="radio-inline">
                    <input type="checkbox" 
                          name="identifikasi_3" 
                          id="identifikasi_3" 
                          value="Tidak diidentifikasi dan tidak diberikan solusi" 
                          class="minimal identifikasi" <?=$ch_identifikasi_3;?> 
                  > Tidak diidentifikasi dan tidak diberikan solusi </label>
                  
              </div>
              <span class="help-block label label-danger"></span>
          </td>
        </tr>
        <tr>
          <td>7.</td>
          <td colspan="2">Seluruh KHS mahasiswa sudah diterima oleh mahasiswa dan ditandatangani:
              <div class="gege-class">
                  <label class="radio-inline">
                    <input type="checkbox" 
                          name="khs_1" 
                          id="khs_1" 
                          value="Ya" 
                          class="minimal khs" <?=$ch_khs_1;?>
                  > Ya </label>
                  <label class="radio-inline">
                    <input type="checkbox" 
                          name="khs_2" 
                          id="khs_2" 
                          value="Tidak" 
                          class="minimal khs" <?=$ch_khs_2;?>
                    > Tidak </label>
              </div>
              <span class="help-block label label-danger"></span>
          </td>
        </tr>
      </tbody>
    </table> 
    <p>Demikianlah berita acara ini dibuat dengan sebenar-benarnya.</p>
    <div style="page-break-after: always;"></div>
    <table class="table table-black">
      <thead>
        <tr class="bg-blue">
          <td colspan="5" class="text-center"><h2>LAMPIRAN 1 - DAFTAR HADIR BIMBINGAN AKADEMIK</h2></td>
        </tr>
        <tr>
          <th>#</th>
          <th>NIM</th>
          <th>NAMA</th>
          <th class="text-center">IPS <?=$semester;?></th>
          <th>STATUS</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $i = 1;
        foreach ($data_mhs_bimbingan as $key => $value) {
          
          if($value['tot_sks'] == 0){
            $ips = 0;
          }else{
            $ips = round($value['total']/$value['tot_sks'],2);
          }

          ?>
          <tr>
            <td><?=$i;?></td>
            <td><?=$value['nipd'];?></td>
            <td><?=$value['nm_pd'];?></td>
            <td class="text-center"><?=$ips;?></td>
            <td><?=$value['status'];?></td>
          </tr>
          <?php
          $i++;
        }
      ?>
      </tbody>
    </table>
    <br>
    <table class="table table-black">
      <thead>
        <tr class="bg-blue">
          <td colspan="5" class="text-center"><h2>LAMPIRAN 2 - LEMBAR KONSULTASI</h2></td>
        </tr>
        <tr>
          <th>#</th>
          <th>NIM</th>
          <th>NAMA</th>
          <th>Konsultasi Akademik</th>
          <th>Feedback Mahasiswa</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $i = 1;
        foreach ($form_konsultasi as $key => $value) {
          ?>
          <tr>
            <td><?=$i;?></td>
            <td style="white-space: nowrap;"><?=$value['nipd'];?></td>
            <td style="white-space: nowrap;"><?=$value['nm_pd'];?></td>
            <td><?=nl2br($value['konsultasi_akademik']);?></td>
            <td><?=nl2br($value['feedback_mahasiswa']);?></td>
          </tr>
          <?php
          $i++;
        }
      ?>
      </tbody>
    </table>



    <!-- <table class="table table-borderless table-condensed">
      <tbody>
        <tr>
          <td colspan="2" style="font-weight: bold">A. PELAKSANAAN BIMBINGAB</td>
        </tr>
        <tr>
          <td style="vertical-align: top">1.</td>
          <td>Jumlah Mahasiswa Bimbingan:  <?=$total_bimbingan .' Orang';?></span>
             
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">2.</td>
          <td>Bahan/Materi yang diberikan pada mahasiswa (boleh lebih dari satu)
            <table>
              <tr>
                <td>
                  <input type="checkbox" name="materi_kuliah_referensi" value="Buku Referensi" class="minimal" <?=$ch_materi_buku_referensi;?>> Buku Referensi
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_diktat" value="Diktat Kuliah" class="minimal" <?=$ch_materi_diktat_kuliah;?>> Diktat Kuliah
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_handout" value="Handout" class="minimal" <?=$ch_materi_handout;?>> Handout
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_lain_lain" value="Lain-lain" class="minimal" <?=$ch_materi_lain_lain;?>> Lain-lain
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">3.</td>
          <td>Metode perkuliahan yang dilakukan (boleh lebih dari satu)
            <table class="table table-borderless">
              <tr>
                <td>
                  <input type="checkbox" name="metode_kuliah_lecturing" value="Lecturing" class="minimal" <?=$ch_metode_lecturing;?>> Lecturing
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_diskusi" value="Diskusi" class="minimal" <?=$ch_metode_diskusi;?>> Diskusi
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_smallgroup" value="Small Group Discussion" class="minimal" <?=$ch_metode_small_group_discussion;?>> Small Group Discussion
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_presentasi_mhs" value="Presentasi Mahasiswa" class="minimal" <?=$ch_metode_presentasi_mahasiswa;?>> Presentasi Mahasiswa
                </td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="metode_kuliah_studi_kasus" value="Studi Kasus" class="minimal" <?=$ch_metode_studi_kasus;?>> Studi Kasus
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_studi_proyek" value="Studi Proyek" class="minimal" <?=$ch_metode_studi_proyek;?>> Studi Proyek
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_lain_lain" value="Lain-lain" class="minimal" <?=$ch_metode_lain_lain;?>> Lain-lain
                </td>
              </tr>
            </table>
          </td>
        </tr>
                    <tr>
                      <td>4.</td>
                      <td>Metode Evaluasi Hasil Belajar Mahasiswa (Diambil dari data komposisi nilai)
                        <div class="row"> 
                          <div class="col-md-12 col-sm-12">
                            <table class="table table-bordered">
                              <tr>
                                <th style="width: 5px">#</th>
                                <th style="width: 350px">Kategori</th>
                                <th>Persen</th>
                                <th style="width: 40px">%</th>
                              </tr>
                              <tr>
                                <td>1.</td>
                                <td>Sikap</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-danger" id="sikap-pbar" style="width: 55%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_sikap_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>2.</td>
                                <td>Pengetahuan</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-yellow" id="pengetahuan-pbar" style="width: 70%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_pengetahuan_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>3.</td>
                                <td>Keterampilan</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-primary" id="keterampilan-pbar" style="width: 30%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_keterampilan_bobot"></span></td>
                              </tr>

                            </table>
                          </div>

                          <div class="col-md-6 col-sm-6">
                            <table class="table table-bordered">
                              <tr>
                                <th style="width: 5px">#</th>
                                <th style="width: 120px">Pengetahuan</th>
                                <th>Persen</th>
                                <th style="width: 40px">%</th>
                              </tr>
                              <tr>
                                <td>1.</td>
                                <td>Quiz</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-danger" id="quiz-pbar" style="width: 55%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_pengetahuan_quiz_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>2.</td>
                                <td>UTS</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-yellow" id="uts-pbar" style="width: 70%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_pengetahuan_uts_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>3.</td>
                                <td>UAS</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-primary" id="uas-pbar" style="width: 30%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_pengetahuan_uas_bobot"></span></td>
                              </tr>

                            </table>
                          </div>

                          <div class="col-md-6 col-sm-6">
                            <table class="table table-bordered">
                              <tr>
                                <th style="width: 5px">#</th>
                                <th style="width: 120px">Keterampilan</th>
                                <th>Persen</th>
                                <th style="width: 40px">%</th>
                              </tr>
                              <tr>
                                <td>1.</td>
                                <td>Tugas</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-danger" id="tugas-pbar" style="width: 55%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_keterampilan_tugas_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>2.</td>
                                <td>Unjuk Kinerja</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-yellow" id="unjuk-kinerja-pbar" style="width: 70%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_keterampilan_unjuk_kinerja_bobot"></span></td>
                              </tr>
                              <tr>
                                <td>3.</td>
                                <td>Presentasi</td>
                                <td>
                                  <div class="progress">
                                    <div class="progress-bar progress-bar-primary" id="presentasi-pbar" style="width: 30%"></div>
                                  </div>
                                </td>
                                <td><span id="metode_evaluasi_keterampilan_presentasi_bobot"></span></td>
                              </tr>

                            </table>
                          </div>
                        </div>
                      </td>
                    </tr>        
        <tr>
          <td style="vertical-align: top">4.</td>
          <td>Metode Evaluasi Hasil Belajar Mahasiswa (boleh lebih dari satu)
            <table class="table table-black">
              <thead>
                <tr><th style="width:30px">&nbsp;</th><th>Metode Evaluasi</th><th>Jumlah Evaluasi</th><th>Bobot (%)</th></tr>
              </thead>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_tugas_mandiri" value="Tugas Mandiri" class="minimal" <?=$ch_evaluasi_tugas_mandiri;?>></td>
                <td>Tugas Mandiri</td>
                <td style="min-width: 150px;" class="text-center"><?=$metode_evaluasi_tugas_mandiri_jumlah;?></td>
                <td class="text-center"><?=$metode_evaluasi_tugas_mandiri_bobot;?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_quiz" value="Quiz" class="minimal" <?=$ch_evaluasi_quiz;?>></td>
                <td>Quiz</td>
                <td class="text-center"><?=$metode_evaluasi_quiz_jumlah;?></td>
                <td class="text-center"><?=$metode_evaluasi_quiz_bobot;?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_tugas_kelompok" value="Tugas Kelompok" class="minimal" <?=$ch_evaluasi_tugas_kelompok;?>></td>
                <td>Tugas Kelompok</td>
                <td class="text-center"><?=$metode_evaluasi_tugas_kelompok_jumlah;?></td>
                <td class="text-center"><?=$metode_evaluasi_tugas_kelompok_bobot;?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_partisipasi" value="Partisipasi" class="minimal" <?=$ch_metode_evaluasi_partisipasi;?>></td>
                <td>Keaktifan di kelas / Partisipasi</td>
                <td class="text-center"><?=$metode_evaluasi_partisipasi_jumlah;?></td>
                <td class="text-center"> <?=$metode_evaluasi_partisipasi_bobot;?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_uts" value="Ujian Tengah Semester" class="minimal"  <?=$ch_metode_evaluasi_uts;?> ></td>
                <td> Ujian Tengah Semester</td>
                <td class="text-center"><?=$metode_evaluasi_uts_jumlah;?></td>
                <td class="text-center"> <?=$metode_evaluasi_uts_bobot;?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_uas" value="Ujian Akhir Semester" class="minimal"  <?=$ch_metode_evaluasi_uas;?>> </td>
                <td>Ujian Akhir Semester</td>
                <td class="text-center"><?=$metode_evaluasi_uas_jumlah;?></td>
                <td class="text-center"> <?=$metode_evaluasi_uas_bobot;?></td>
              </tr>
            </table>              
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">5.</td>
          <td>Nilai Rata-Rata Kelas :
              <label class="radio-inline"><?=$nilai_rata_kelas;?></label>
              
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">6.</td>
          <td>Distribusi Nilai
              <div class="gege-class">
                  <table class="table table-black">
                      <thead>
                          <tr>
                              <th></th><th>A</th><th>B+</th><th>B</th><th>C+</th><th>C</th><th>D</th><th>E</th><th>Kosong</th>
                          </tr>
                      </thead>
                      <tbody>

                          <tr>
                            <td>Jumlah</td>
                            <td class="text-center"><?=$distribusi_nilai_a;?></td>
                            <td class="text-center"><?=$distribusi_nilai_b_plus;?></td>
                            <td class="text-center"><?=$distribusi_nilai_b;?></td>
                            <td class="text-center"><?=$distribusi_nilai_c_plus;?></td>
                            <td class="text-center"><?=$distribusi_nilai_c;?></td>
                            <td class="text-center"><?=$distribusi_nilai_d;?></td>
                            <td class="text-center"><?=$distribusi_nilai_e;?></td>
                            <td class="text-center"><?=$distribusi_nilai_kosong;?></td>
                          </tr>
                          <tr>
                            <td>Persen</td>
                            <td class="text-center"><?=$persen_nilai_a;?></td>
                            <td class="text-center"><?=$persen_nilai_b_plus;?></td>
                            <td class="text-center"><?=$persen_nilai_b;?></td>
                            <td class="text-center"><?=$persen_nilai_c_plus;?></td>
                            <td class="text-center"><?=$persen_nilai_c;?></td>
                            <td class="text-center"><?=$persen_nilai_d;?></td>
                            <td class="text-center"><?=$persen_nilai_e;?></td>
                            <td class="text-center"><?=$persen_nilai_kosong;?></td>
                          </tr>
                      </tbody>
                      
                  </table>
              </div>
          </td>
        </tr>

        <tr>
          <td style="vertical-align: top">7.</td>
          <td>Jumlah Tatap Muka :
             <?=$jumlah_tatap_muka;?> Kali /
             <?=$persen_tatap_muka;?> % 
          </td>
        </tr>
        <tr>
          <td>8.</td>
          <td>Materi Tersampaikan <?=$materi_tersampaikan;?> % </td>
        </tr>

        <tr class="bg-blue">
          <td colspan="2" style="font-weight: bold;padding-top: 20px">B. ANALISA KELAS DAN MAHASISWA <BR> (Berikan uraian yang berkaitan dengan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll)</td>
         
        </tr>
        <tr>
          <td style="vertical-align: top">1.</td>
          <td><b>Kelemahan/Kesulitan dan Kendala dalam Pelaksanaan Perkuliahan : </b>
              <div class="gege-class">
                  <span class="col-md-12 col-sm-12 col-lg-12"><?=nl2br($text_kelemahan_dan_kendala);?>
                  </span>
              </div>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">2.</td>
          <td><b>Rekomendasi (Berikan uraian saran/rekomendasi maupun rencana perbaikan dengan peningkatan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll):</b>
              <div class="gege-class">
                <span class="col-md-12 col-sm-12 col-lg-12"><?=nl2br($text_rekomendasi);?></span>
              </div>
          </td>
        </tr>
      
       
      </tbody>
    </table>  -->
  
</div>

</body>
</html>
