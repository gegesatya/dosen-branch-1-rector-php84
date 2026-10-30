<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cetak Evaluasi Diri</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <style type="text/css">
    body {
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 13px;
      line-height: 1.42857143;
      color: #333;
      background-color: #fff;

    }

    .gege-class {
      width: 100% !important;
      max-width: 100%;
      border: 1px solid #ddd !important;
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
      font-size: 11px !important;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 3px !important;
      font-size: 11px !important;
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
      display: inline-block;
      border: solid 1px #000;
      min-height: 10px;
      width: 300px;
    }
  </style>
</head>

<body>
  
  <div class="wrapper">
    
    <?php
    $ch_materi_buku_referensi = '';
    $ch_materi_diktat_kuliah = '';
    $ch_materi_handout = '';
    $ch_materi_lain_lain = '';
    $ch_metode_lecturing = '';
    $ch_metode_diskusi = '';
    $ch_metode_small_group_discussion = '';
    $ch_metode_presentasi_mahasiswa = '';
    $ch_metode_studi_kasus = '';
    $ch_metode_studi_proyek = '';
    $ch_metode_lain_lain = '';
    $ch_evaluasi_quiz = '';
    $ch_evaluasi_tugas_mandiri = '';
    $ch_evaluasi_tugas_kelompok = '';
    $ch_evaluasi_partisipasi = '';
    $ch_evaluasi_uts = '';
    $ch_evaluasi_uas = '';

    

    if ($evaluasi->materi_kuliah_referensi == 'Buku Referensi') {
      $ch_materi_buku_referensi = 'checked = "checked"';
    }
    if ($evaluasi->materi_kuliah_diktat == 'Diktat Kuliah') {
      $ch_materi_diktat_kuliah = 'checked = "checked"';
    }
    if ($evaluasi->materi_kuliah_handout == 'Handout') {
      $ch_materi_handout = 'checked = "checked"';
    }
    if ($evaluasi->materi_kuliah_lain_lain == 'Lain-lain') {
      $ch_materi_lain_lain = 'checked = "checked"';
    }

    if ($evaluasi->metode_kuliah_lecturing == 'Lecturing') {
      $ch_metode_lecturing = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_diskusi == 'Diskusi') {
      $ch_metode_diskusi = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_smallgroup == 'Small Group Discussion') {
      $ch_metode_small_group_discussion = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_presentasi_mhs == 'Presentasi Mahasiswa') {
      $ch_metode_presentasi_mahasiswa = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_studi_kasus == 'Studi Kasus') {
      $ch_metode_studi_kasus = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_studi_proyek == 'Studi Proyek') {
      $ch_metode_studi_proyek = 'checked = "checked"';
    }
    if ($evaluasi->metode_kuliah_lain_lain == 'Lain-lain') {
      $ch_metode_lain_lain = 'checked = "checked"';
    }



    if ($evaluasi->metode_evaluasi_tugas_mandiri == 'Tugas Mandiri') {
      $ch_evaluasi_tugas_mandiri = 'checked = "checked"';
    }
    if ($evaluasi->metode_evaluasi_quiz == 'Quiz') {
      $ch_evaluasi_quiz = 'checked = "checked"';
    }
    if ($evaluasi->metode_evaluasi_tugas_kelompok == 'Tugas Kelompok') {
      $ch_evaluasi_tugas_kelompok = 'checked = "checked"';
    }

    if ($evaluasi->metode_evaluasi_partisipasi == 'Partisipasi') {
      $ch_metode_evaluasi_partisipasi = 'checked = "checked"';
    }

    if ($evaluasi->metode_evaluasi_uts == 'Ujian Tengah Semester') {
      $ch_metode_evaluasi_uts = 'checked = "checked"';
    }

    if ($evaluasi->metode_evaluasi_uas == 'Ujian Akhir Semester') {
      $ch_metode_evaluasi_uas = 'checked = "checked"';
    }

    ?>
    <table class="table table-borderless">
      <tbody>
        <tr>
          <td>
            <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="150px" style="margin-top:10px;margin-right: 20px;" />
          </td>
          <td colspan="3">
            <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
            <p style="font-size: 12px;margin:0 0 5px !important">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
          </td>

        </tr>
      </tbody>
    </table>
    <hr />
    <h3 class="text-center">LAPORAN EVALUASI DIRI</h3>
    <h4 class="text-center">PBM SEMESTER <?= $this->db->where('id_smt', $jadwal->id_smt)->get('semester')->row()->nm_smt; ?></h4>
    <table class="table table-borderless">
      <tr>
        <td style="width:180px">MATA KULIAH</td>
        <td style="width:10px">:</td>
        <td><?= $jadwal->tag_name; ?></td>
      </tr>
      <tr>
        <td>DOSEN PENGASUH</td>
        <td>:</td>
        <td><?= $dosen->nm_ptk; ?></td>
      </tr>
      <tr>
        <td>PROGRAM STUDI</td>
        <td>:</td>
        <td><?= $this->db->where('id_sms', $jadwal->id_sms)->get('sms')->row()->nm_lemb; ?></td>
      </tr>
    </table>

    <table class="table table-borderless table-condensed">
      <tbody>
        <tr>
          <td colspan="2" style="font-weight: bold">A. KINERJA KELAS</td>
        </tr>
        <tr>
          <td style="vertical-align: top">1.</td>
          <td>Jumlah Peserta <?= $evaluasi->int_jumlah_peserta . ' Orang'; ?></span>

          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">2.</td>
          <td>Bahan/Materi yang diberikan pada mahasiswa (boleh lebih dari satu)
            <table>
              <tr>
                <td>
                  <input type="checkbox" name="materi_kuliah_referensi" value="Buku Referensi" class="minimal" <?= $ch_materi_buku_referensi; ?>> Buku Referensi
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_diktat" value="Diktat Kuliah" class="minimal" <?= $ch_materi_diktat_kuliah; ?>> Diktat Kuliah
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_handout" value="Handout" class="minimal" <?= $ch_materi_handout; ?>> Handout
                </td>
                <td>
                  <input type="checkbox" name="materi_kuliah_lain_lain" value="Lain-lain" class="minimal" <?= $ch_materi_lain_lain; ?>> Lain-lain
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
                  <input type="checkbox" name="metode_kuliah_lecturing" value="Lecturing" class="minimal" <?= $ch_metode_lecturing; ?>> Lecturing
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_diskusi" value="Diskusi" class="minimal" <?= $ch_metode_diskusi; ?>> Diskusi
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_smallgroup" value="Small Group Discussion" class="minimal" <?= $ch_metode_small_group_discussion; ?>> Small Group Discussion
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_presentasi_mhs" value="Presentasi Mahasiswa" class="minimal" <?= $ch_metode_presentasi_mahasiswa; ?>> Presentasi Mahasiswa
                </td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="metode_kuliah_studi_kasus" value="Studi Kasus" class="minimal" <?= $ch_metode_studi_kasus; ?>> Studi Kasus
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_studi_proyek" value="Studi Proyek" class="minimal" <?= $ch_metode_studi_proyek; ?>> Studi Proyek
                </td>
                <td>
                  <input type="checkbox" name="metode_kuliah_lain_lain" value="Lain-lain" class="minimal" <?= $ch_metode_lain_lain; ?>> Lain-lain
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">4.</td>
          <td>Metode Evaluasi Hasil Belajar Mahasiswa (boleh lebih dari satu)
            <table class="table table-black">
              <thead>
                <tr>
                  <th style="width:30px">&nbsp;</th>
                  <th>Metode Evaluasi</th>
                  <th>Jumlah Evaluasi</th>
                  <th>Bobot (%)</th>
                </tr>
              </thead>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_tugas_mandiri" value="Tugas Mandiri" class="minimal" <?= $ch_evaluasi_tugas_mandiri; ?>></td>
                <td>Tugas Mandiri</td>
                <td style="min-width: 150px;" class="text-center"><?= $evaluasi->metode_evaluasi_tugas_mandiri_jumlah; ?></td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_tugas_mandiri_bobot; ?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_quiz" value="Quiz" class="minimal" <?= $ch_evaluasi_quiz; ?>></td>
                <td>Quiz</td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_quiz_jumlah; ?></td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_quiz_bobot; ?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="metode_evaluasi_tugas_kelompok" value="Tugas Kelompok" class="minimal" <?= $ch_evaluasi_tugas_kelompok; ?>></td>
                <td>Tugas Kelompok</td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_tugas_kelompok_jumlah; ?></td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_tugas_kelompok_bobot; ?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_partisipasi" value="Partisipasi" class="minimal" <?= $ch_evaluasi_partisipasi; ?>></td>
                <td>Keaktifan di kelas / Partisipasi</td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_partisipasi_jumlah; ?></td>
                <td class="text-center"> <?= $evaluasi->metode_evaluasi_partisipasi_bobot; ?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_uts" value="Ujian Tengah Semester" class="minimal" <?= $ch_evaluasi_uts; ?>></td>
                <td> Ujian Tengah Semester</td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_uts_jumlah; ?></td>
                <td class="text-center"> <?= $evaluasi->metode_evaluasi_uts_bobot; ?></td>
              </tr>
              <tr>
                <td class="text-center"><input type="checkbox" name="ch_metode_evaluasi_uas" value="Ujian Akhir Semester" class="minimal" <?= $ch_evaluasi_uas; ?>> </td>
                <td>Ujian Akhir Semester</td>
                <td class="text-center"><?= $evaluasi->metode_evaluasi_uas_jumlah; ?></td>
                <td class="text-center"> <?= $evaluasi->metode_evaluasi_uas_bobot; ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">5.</td>
          <td>Nilai Rata-Rata Kelas :
            <label class="radio-inline"><?= $evaluasi->nilai_rata_kelas; ?></label>

          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">6.</td>
          <td>Distribusi Nilai
            <div class="gege-class">
              <table class="table table-black">
                <thead>
                  <tr>
                    <th></th>
                    <th>A</th>
                    <th>B+</th>
                    <th>B</th>
                    <th>C+</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                    <th>Kosong</th>
                  </tr>
                </thead>
                <tbody>

                  <tr>
                    <td>Jumlah</td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_a; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_b_plus; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_b; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_c_plus; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_c; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_d; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_e; ?></td>
                    <td class="text-center"><?= $evaluasi->distribusi_nilai_kosong; ?></td>
                  </tr>
                  <tr>
                    <td>Persen</td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_a; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_b_plus; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_b; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_c_plus; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_c; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_d; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_e; ?></td>
                    <td class="text-center"><?= $evaluasi->persen_nilai_kosong; ?></td>
                  </tr>
                </tbody>

              </table>
            </div>
          </td>
        </tr>

        <tr>
          <td style="vertical-align: top">7.</td>
          <td>Jumlah Tatap Muka :
            <?= $evaluasi->jumlah_tatap_muka; ?> Kali /
            <?= $evaluasi->persen_tatap_muka; ?> %
          </td>
        </tr>
        <tr>
          <td>8.</td>
          <td>Materi Tersampaikan <?= $evaluasi->materi_tersampaikan; ?> % </td>
        </tr>

        <tr>
          <td colspan="2" style="font-weight: bold;padding-top: 20px">B. ANALISA KELAS DAN MAHASISWA <BR> (Berikan uraian yang berkaitan dengan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll)</td>

        </tr>
        <tr>
          <td style="vertical-align: top">1.</td>
          <td><b>Kelemahan/Kesulitan dan Kendala dalam Pelaksanaan Perkuliahan : </b>
            <div>
              <?= nl2br($evaluasi->text_kelemahan_dan_kendala); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td style="vertical-align: top">2.</td>
          <td><b>Rekomendasi (Berikan uraian saran/rekomendasi maupun rencana perbaikan dengan peningkatan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll):</b>
            <div>
              <span class="col-md-12 col-sm-12 col-lg-12"><?= nl2br($evaluasi->text_rekomendasi); ?></span>
            </div>
          </td>
        </tr>


      </tbody>
    </table>
    
  </div>
  
</body>

</html>