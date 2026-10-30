<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Penilaian Seminar Hasil</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php $this->load->view('cetak/inc_pdf_common_css'); ?>

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 12px;
    }

    /* @page {
      margin-top: 50mm;
    } */

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }

    .column {
      float: left;
      padding: 3px;
      height: auto;
      text-align: justify;
      text-justify: inter-word;
    }

    .left {
      width: 115px;
      white-space: nowrap;
    }

    .left-60 {
      width: 400px;
      white-space: nowrap;
    }

    .left-100 {
      width: 200px;
      white-space: nowrap;
    }

    .right {
      width: auto;

    }

    .middle {
      width: 23px;
    }

    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    .wrapper {
      margin-left: 0px;

    }

    .nomargin {
      padding: 0;
      margin: 0;
    }

    .td-warna {
      background-color: #e5e7e9;
      font-weight: bold;
    }

    .td-high {
      line-height: 1.5;
      text-align: justify;
    }

    .custom {
      border-top: 1px solid #000 !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;
      background-color: #e5e7e9;
      font-weight: bold
    }

    .custom-2 {
      border-top: none !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;
      line-height: 1.5;
      text-align: justify
    }
  </style>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>
  <div class="wrapper">
    <div class="form-container">
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN SEMINAR HASIL TUGAS AKHIR</p>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower((string)($judul->nm_pd ?? ''))); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd ?? ''; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb ?? ''; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <?= $myTable; ?>
      Keterangan:<br>
      Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
      Nilai = bobot × skor

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                Deli Serdang, <?= tgl_indo($berita_acara->semhas_tanggal); ?><br>Dosen Pembimbing Utama<br>
                <img src="<?= $qr_doping_1; ?>" width="65" height="65"><br>
                <span style="font-weight: bold;"><?= $dosen_nama->nama_lengkap; ?></span><br>
                NIDN : <?= $dosen_nama->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">BERITA ACARA SEMINAR HASIL TUGAS AKHIR</p>
      <div class="row">
        <div class="column right">
          Pada hari ini, <?= $berita_acara->semhas_hari; ?> tanggal <?= tgl_indo($berita_acara->semhas_tanggal); ?> telah dilaksanakan Seminar Hasil Tugas Akhir:
        </div>
      </div>
      <table class="table table-borderless2">
        <tbody>
          <tr>
            <td style="width: 10px;">1.</td>
            <td style="width:160px">Nama</td>
            <td style="width: 10px;">:</td>
            <td><?= ucwords(strtolower((string)($judul->nm_pd ?? ''))); ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">2.</td>
            <td>NIM</td>
            <td style="width: 10px;">:</td>
            <td><?= $judul->nipd; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">3.</td>
            <td>Judul</td>
            <td style="width: 10px;">:</td>
            <td><?= $judul->judul_ditetapkan; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">4.</td>
            <td>Moderator</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->semhas_moderator . ' (' . $berita_acara->semhas_nim_moderator . ')'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">5.</td>
            <td>Pembahas</td>
            <td style="width: 10px;">:</td>
            <td>
              <ol>
                <li><?= $berita_acara->semhas_pembahas_1 . ' (' . $berita_acara->semhas_nim_pembahas_1 . ')'; ?></li>
                <li><?= $berita_acara->semhas_pembahas_2 . ' (' . $berita_acara->semhas_nim_pembahas_2 . ')'; ?></li>
              </ol>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;">6.</td>
            <td>Jumlah Peserta</td>
            <td style="width: 10px;">:</td>
            <td></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td>Mahasiswa</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->semhas_jumlah_mahasiswa ?? ''; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td>Dosen Pembimbing/Penguji</td>
            <td style="width: 10px;">:</td>
            <td></td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td colspan="3">
              <table class="table table-borderless2">
                <tbody>
                  <?php
                  $i = 1;
                  if (!empty($penguji)) {
                    if ($nilai_semhas_p1->semhas_nilai_akhir > 0) {
                      echo '<tr>
                      <td class="text-right">' . $i++ . '.</td>
                      <td class="text-left">' . $penguji->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p1->semhas_nilai_akhir . ')</td>
                    </tr>';
                    }
                  }
                  ?>
                  <?php
                  if (!empty($penguji_2)) {
                    if ($nilai_semhas_p2->semhas_nilai_akhir > 0) {
                      echo '<tr>
                          <td class="text-right">' . $i++ . '.</td>
                          <td class="text-left">' . $penguji_2->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p2->semhas_nilai_akhir . ')</td>
                        </tr>';
                    }
                  }
                  ?>
                  <?php

                  echo '<tr>
                          <td class="text-right">' . $i++ . '.</td>
                          <td class="text-left">' . $penguji_3->nama_lengkap . ' ( Nilai : ' . $nilai_semhas_p3->semhas_nilai_akhir . ')</td>
                        </tr>';

                  ?>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;">7.</td>
            <td>Tempat</td>
            <td style="width: 10px;">:</td>
            <td><?= $berita_acara->semhas_tempat ?? ''; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">8.</td>
            <td>Mulai Pukul</td>
            <td style="width: 10px;">:</td>
            <td><?= (!empty($berita_acara->semhas_jam_mulai) ? substr((string)$berita_acara->semhas_jam_mulai, 0, 5) : '-')  . ' WIB sampai dengan ' . (!empty($berita_acara->semhas_jam_selesai) ? substr((string)$berita_acara->semhas_jam_selesai, 0, 5) : '-') . ' WIB'; ?></td>
          </tr>
          <tr>
            <td style="width: 10px;">9.</td>
            <td colspan="3">Rekapitulasi Nilai :
              <br><br>
              <table class="table table-black">
                <tbody>
                  <tr>
                    <td colspan="3" class="text-center">Perhitungan Nilai Akhir</td>
                  </tr>
                  <tr>
                    <td style="width:33.33%" class="text-center">Pembimbing Utama</td>
                    <td style="width:33.33%" class="text-center">Pembimbing<br>Pendamping/Penguji *)</td>
                    <td style="width:33.33%" class="text-center">Penguji</td>
                  </tr>
                  <tr>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p1->semhas_nilai_akhir; ?></td>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p2->semhas_nilai_akhir; ?></td>
                    <td style="width:33.33%" class="text-center"><?= $nilai_semhas_p3->semhas_nilai_akhir; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-center"><?= $rata_rata; ?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          <tr>
            <td style="width: 10px;"></td>
            <td colspan="3">
              <?php
              if ($hasil_semhas == 'layak') {
                echo '<p>Dinyatakan bahwa Hasil Penelitian <span style="font-weight: bold;">Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Ujian Tugas Akhir</span></p>';
              } elseif ($hasil_semhas == 'tidak_layak') {
                echo '<p>Dinyatakan bahwa Hasil Penelitian <span style="font-weight: bold;">Tidak Layak</span> untuk dapat dilanjutkan pada tahapan <span style="font-weight: bold;">Ujian Tugas Akhir</span></p>';
              } else {
                echo '<p>Belum ada hasil seminar </p>';
              }
              ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div style="page-break-after: always;"></div>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">CATATAN PELAKSANAAN SEMINAR HASIL</p>
            </td>
          </tr>
        </tbody>
      </table>
      <p style="font-weight: bold; margin-top: 10px; margin-bottom: 8px;">Ringkasan Pelaksanaan Diskusi:</p>
      <?php
      $topics_semhas = [
        'semhas_catatan_judul'              => ['code' => 'a', 'title' => 'Revisi Judul',                          'empty' => 'Tidak ada revisi judul'],
        'semhas_catatan_latar_belakang'      => ['code' => 'b', 'title' => 'Revisi Latar Belakang',                  'empty' => 'Tidak ada revisi latar belakang'],
        'semhas_catatan_rumusan_masalah'     => ['code' => 'c', 'title' => 'Revisi Rumusan Masalah',                 'empty' => 'Tidak ada revisi rumusan masalah'],
        'semhas_catatan_tujuan_penelitian'    => ['code' => 'd', 'title' => 'Revisi Tujuan Penelitian',                'empty' => 'Tidak ada revisi tujuan penelitian'],
        'semhas_catatan_tinjauan_pustaka'    => ['code' => 'e', 'title' => 'Revisi Tinjauan Pustaka',                'empty' => 'Tidak ada revisi tinjauan pustaka'],
        'semhas_catatan_metode_penelitian'   => ['code' => 'f', 'title' => 'Revisi Metode Penelitian',               'empty' => 'Tidak ada revisi metode penelitian'],
        'semhas_catatan_daftar_pustaka'      => ['code' => 'g', 'title' => 'Revisi Daftar Pustaka',                  'empty' => 'Tidak ada revisi daftar pustaka'],
        'semhas_catatan_format_penulisan'    => ['code' => 'h', 'title' => 'Revisi Format Penulisan',                'empty' => 'Tidak ada revisi format penulisan'],
        'semhas_desain'                      => ['code' => 'i', 'title' => 'Revisi Desain dan Pembuatan',            'empty' => 'Tidak ada revisi desain dan pembuatan'],
        'semhas_pengujian'                   => ['code' => 'j', 'title' => 'Revisi Pengujian dan Analisa',           'empty' => 'Tidak ada revisi pengujian dan analisa'],
        'semhas_kesimpulan'                  => ['code' => 'k', 'title' => 'Revisi Kesimpulan dan Rekomendasi',     'empty' => 'Tidak ada revisi kesimpulan dan rekomendasi penerapan'],
      ];
      ?>

      <?php if (!empty($usulan_semhas)): ?>
        <?php foreach ($usulan_semhas as $dosen): ?>
          <div class="dosen-card">
            <div class="dosen-card-header">
              <?= $dosen->nama_lengkap; ?>
              <?php if (!empty($dosen->nidn)): ?>
                <span class="nidn-tag">&nbsp;&bull;&nbsp; NIDN: <?= $dosen->nidn; ?></span>
              <?php endif; ?>
            </div>
            <table class="revisi-table">
              <tbody>
                <?php foreach ($topics_semhas as $field => $meta): 
                  $val = clean_catatan_revisi($dosen->$field ?? null);
                ?>
                  <tr>
                    <td class="revisi-label">
                      <?= $meta['code']; ?>. <?= $meta['title']; ?>
                    </td>
                    <td style="width: 2%; text-align: center;">:</td>
                    <td style="width: 70%;">
                      <?php if ($val !== null): ?>
                        <div class="revisi-content-filled"><?= render_catatan_revisi($val); ?></div>
                      <?php else: ?>
                        <span class="revisi-content-empty"><?= $meta['empty']; ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: #94a3b8; font-style: italic;">Tidak ada catatan pelaksanaan diskusi.</p>
      <?php endif; ?>


      <p style="margin-top: 20px;">Demikian catatan pelaksanaan seminar hasil ini dibuat dengan sebenarnya dan segala sesuatu yang ditulis telah diterima oleh pengusul Proposal Tugas Akhir untuk dilaksanakan sebagaimana mestinya.</p>
      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td style="padding-top: 10px;">
                Deli Serdang, <?= tgl_indo($berita_acara->semhas_tanggal); ?><br>
                Pembimbing/Penguji<br>
                <img src="<?= $qr_doping_1; ?>" width="65" height="65"><br>
                <span style="font-weight: bold;"><?= $dosen_nama->nama_lengkap; ?></span><br>
                NIDN : <?= $dosen_nama->nidn; ?>
              </td>
            </tr>


          </tbody>
        </table>
      </div>

    </div>
  </div>
</body>

</html>