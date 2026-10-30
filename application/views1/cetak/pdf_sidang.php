<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ujian/Sidang Tugas Akhir</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>">

  <style type="text/css">
    body {
      font-family: Calibri;
      font-size: 12px;
    }

    /* @page {
      margin-top: 50mm;
    } */

    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      /*font-size: 13px !important;*/
      /*  margin-bottom: 10px; */
      vertical-align: top;
    }

    .table-borderless2 th,
    .table-borderless2 td {
      border: 0px solid #fff !important;
      padding: 5px !important;
      font-size: 12px !important;
      vertical-align: top;
    }

    .table-borderless-small th,
    .table-borderless-small td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 10px !important;
      margin-bottom: 10px;
      vertical-align: top;
    }

    .table-black th,
    .table-black td {
      border: 1px solid #000 !important;
      padding: 3px !important;
      font-size: 12px !important;

    }

    .table-black-2 th,
    .table-black-2 td {
      border-top: 1px solid #000 !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;

    }

    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }

    .coret {
      text-decoration: line-through;
    }

    .column {
      float: left;
      padding: 3px;
      height: auto;
      text-align: justify;
      text-justify: inter-word;
    }

    .left {
      width: 100px;
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

    .custom-3 {
      border-top: none !important;
      border-bottom: 1px solid #000 !important;
      border-left: none !important;
      border-right: none !important;
      padding: 4px !important;
      font-size: 12px !important;
      line-height: 1.5;
      text-align: justify;

    }
  </style>
  
  
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
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN UJIAN TUGAS AKHIR</p>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower($judul->nm_pd)); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <div class="row">
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Kriteria Penilaian</th>
              <th class="text-center">Bobot<br>(%)</th>
              <th class="text-center">Skor</th>
              <th class="text-center">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1.</td>
              <td>Bobot / Kualitas Laporan Hasil (Manuskrip)<br>
                a. Kekinian Masalah yang Dihadapi (kejelasan dan relevansi masalah dengan hasil penelitian yang dicapai).<br>
                b. Relevansi Metodologi dalam Perumusan Hasil Penelitian<br>
                c. Kejelasan Rumusan Hasil Menguraikan Jawaban Permasalahan Penelitian
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_a; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_1->sidang_nilai_a * 1; ?></td>
            </tr>
            <tr>
              <td>2.</td>
              <td>Sistematika Penulisan (kesesuaian penulisan dengan panduan yang ditetapkan).</td>
              <td class="text-center">15</td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_b; ?></td>
              <td class="text-center"><?= 0.15 * $nilai_penguji_1->sidang_nilai_b * 1; ?></td>
            </tr>
            <tr>
              <td>3.</td>
              <td>Penguasaan Materi dan Argumentasi (kemampuan menjelaskan dan menjawab pertanyaan-pertanyaan terkait penelitian)</td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_c; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_1->sidang_nilai_c * 1; ?></td>
            </tr>
            <tr>
              <td>4.</td>
              <td>Kemampuan / Potensi Luaran<br>
                (kualitas luaran, potensi aplikasi luaran pada inovasi, implementasi luaran oleh mitra TA)
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_d; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_1->sidang_nilai_d * 1; ?></td>
            </tr>
            <tr>
              <td>5.</td>
              <td>Sikap dan Penampilan</td>
              <td class="text-center">10</td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_e; ?></td>
              <td class="text-center"><?= 0.1 * $nilai_penguji_1->sidang_nilai_e * 1; ?></td>
            </tr>
            <tr>
              <td></td>
              <td>Total</td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_akhir * 1; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <small><br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 1,<br>
                <img src="<?= $qr_penguji_1; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_1->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_1->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN UJIAN TUGAS AKHIR</p>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower($judul->nm_pd)); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <div class="row">
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Kriteria Penilaian</th>
              <th class="text-center">Bobot<br>(%)</th>
              <th class="text-center">Skor</th>
              <th class="text-center">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1.</td>
              <td>Bobot / Kualitas Laporan Hasil (Manuskrip)<br>
                a. Kekinian Masalah yang Dihadapi (kejelasan dan relevansi masalah dengan hasil penelitian yang dicapai).<br>
                b. Relevansi Metodologi dalam Perumusan Hasil Penelitian<br>
                c. Kejelasan Rumusan Hasil Menguraikan Jawaban Permasalahan Penelitian
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_a; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_2->sidang_nilai_a * 1; ?></td>
            </tr>
            <tr>
              <td>2.</td>
              <td>Sistematika Penulisan (kesesuaian penulisan dengan panduan yang ditetapkan).</td>
              <td class="text-center">15</td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_b; ?></td>
              <td class="text-center"><?= 0.15 * $nilai_penguji_2->sidang_nilai_b * 1; ?></td>
            </tr>
            <tr>
              <td>3.</td>
              <td>Penguasaan Materi dan Argumentasi (kemampuan menjelaskan dan menjawab pertanyaan-pertanyaan terkait penelitian)</td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_c; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_2->sidang_nilai_c * 1; ?></td>
            </tr>
            <tr>
              <td>4.</td>
              <td>Kemampuan / Potensi Luaran<br>
                (kualitas luaran, potensi aplikasi luaran pada inovasi, implementasi luaran oleh mitra TA)
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_d; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_2->sidang_nilai_d * 1; ?></td>
            </tr>
            <tr>
              <td>5.</td>
              <td>Sikap dan Penampilan</td>
              <td class="text-center">10</td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_e; ?></td>
              <td class="text-center"><?= 0.1 * $nilai_penguji_2->sidang_nilai_e * 1; ?></td>
            </tr>
            <tr>
              <td></td>
              <td>Total</td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_akhir * 1; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <small>Keterangan:<br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 2,<br>
                <img src="<?= $qr_penguji_2; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_2->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_2->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td class="text-center">
              <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PENILAIAN UJIAN TUGAS AKHIR</p>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower($judul->nm_pd)); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <br>
      <div class="row">
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Kriteria Penilaian</th>
              <th class="text-center">Bobot<br>(%)</th>
              <th class="text-center">Skor</th>
              <th class="text-center">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1.</td>
              <td>Bobot / Kualitas Laporan Hasil (Manuskrip)<br>
                a. Kekinian Masalah yang Dihadapi (kejelasan dan relevansi masalah dengan hasil penelitian yang dicapai).<br>
                b. Relevansi Metodologi dalam Perumusan Hasil Penelitian<br>
                c. Kejelasan Rumusan Hasil Menguraikan Jawaban Permasalahan Penelitian
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_a; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_3->sidang_nilai_a * 1; ?></td>
            </tr>
            <tr>
              <td>2.</td>
              <td>Sistematika Penulisan (kesesuaian penulisan dengan panduan yang ditetapkan).</td>
              <td class="text-center">15</td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_b; ?></td>
              <td class="text-center"><?= 0.15 * $nilai_penguji_3->sidang_nilai_b * 1; ?></td>
            </tr>
            <tr>
              <td>3.</td>
              <td>Penguasaan Materi dan Argumentasi (kemampuan menjelaskan dan menjawab pertanyaan-pertanyaan terkait penelitian)</td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_c; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_3->sidang_nilai_c * 1; ?></td>
            </tr>
            <tr>
              <td>4.</td>
              <td>Kemampuan / Potensi Luaran<br>
                (kualitas luaran, potensi aplikasi luaran pada inovasi, implementasi luaran oleh mitra TA)
              </td>
              <td class="text-center">25</td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_d; ?></td>
              <td class="text-center"><?= 0.25 * $nilai_penguji_3->sidang_nilai_d * 1; ?></td>
            </tr>
            <tr>
              <td>5.</td>
              <td>Sikap dan Penampilan</td>
              <td class="text-center">10</td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_e; ?></td>
              <td class="text-center"><?= 0.1 * $nilai_penguji_3->sidang_nilai_e * 1; ?></td>
            </tr>
            <tr>
              <td></td>
              <td>Total</td>
              <td class="text-center"></td>
              <td class="text-center"></td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_akhir * 1; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <small>Keterangan:<br>
        Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)<br>
        Nilai = bobot × skor</small>

      <div class="row">
        <table class="table table-borderless2" style="margin-top: 20px;">
          <tbody>
            <tr>
              <td style="width:55%" class="text-center"></td>
              <td>
                Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br>
                Penguji 3,<br>
                <img src="<?= $qr_penguji_3; ?>"><br>
                <span style="font-weight: bold;"><?= $penguji_3->nama_lengkap; ?></span><br>
                NIDN : <?= $penguji_3->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">REKAPITULASI NILAI UJIAN TUGAS AKHIR</p>
      <div class="row">
        <div class="column left">Nama</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= ucwords(strtolower($judul->nm_pd)); ?></div>
      </div>
      <div class="row">
        <div class="column left">NIM</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nipd; ?></div>
      </div>
      <div class="row">
        <div class="column left">Prodi</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->nm_lemb; ?></div>
      </div>
      <div class="row">
        <div class="column left">Judul Tugas Akhir</div>
        <div class="column middle text-right">:</div>
        <div class="column right"><?= $judul->judul_ditetapkan; ?></div>
      </div>
      <div class="row">
        <table class="table table-black">
          <thead>
            <tr>
              <th>Penguji</th>
              <th class="text-center">Nilai Sidang</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= $penguji_1->nama_lengkap; ?></td>
              <td class="text-center"><?= $nilai_penguji_1->sidang_nilai_akhir * 1; ?></td>
            </tr>
            <tr>
              <td><?= $penguji_2->nama_lengkap; ?></td>
              <td class="text-center"><?= $nilai_penguji_2->sidang_nilai_akhir * 1; ?></td>
            </tr>
            <tr>
              <td><?= $penguji_3->nama_lengkap; ?></td>
              <td class="text-center"><?= $nilai_penguji_3->sidang_nilai_akhir * 1; ?></td>
            </tr>
            <tr>
              <td>Nilai Sidang Rata-rata</td>
              <td class="text-center"><?= $rata_rata * 1; ?></td>
            </tr>
          </tbody>
        </table>
        <table class="table table-black">
          <thead>
            <tr>
              <th>Uraian</th>
              <th class="text-center">Nilai (N)</th>
              <th class="text-center">SKS (K)</th>
              <th class="text-center">N*K</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Seminar Proposal</td>
              <td class="text-center"><?= $nilai_sempro; ?></td>
              <td class="text-center">1</td>
              <td class="text-center"><?= $nilai_sempro * 1; ?></td>
            </tr>
            <tr>
              <td>Seminar Hasil</td>
              <td class="text-center"><?= $nilai_semhas; ?></td>
              <td class="text-center">1</td>
              <td class="text-center"><?= $nilai_semhas * 1; ?></td>
            </tr>
            <tr>
              <td>Ujian/Sidang Tugas Akhir</td>
              <td class="text-center"><?= $rata_rata * 1; ?></td>
              <td class="text-center">4</td>
              <td class="text-center"><?= $rata_rata * 4 * 1; ?></td>
            </tr>
            <tr>
              <td colspan="2" class="text-center">Total</td>
              <td class="text-center">6</td>
              <td class="text-center"><?= $jumlah_total; ?></td>
            </tr>
            <tr>
              <td colspan="2" class="text-center">&#931;(N*K)/&#931;K</td>
              <td colspan="2" class="text-center"><?= round($jumlah_total / 6, 2); ?></td>
            </tr>
            <tr>
              <td colspan="2" class="text-center"><b>NILAI</b></td>
              <td colspan="2" class="text-center"><b><?= $nilai_mutu; ?></b></td>
            </tr>
          </tbody>
        </table>
        <table class="table table-borderless-small" style="width: 50%;">
          <tbody>
            <tr>
              <td colspan="2">KETERANGAN NILAI :</td>
            </tr>
            <tr>
              <td>> 6,51 = A</td>
              <td>4,51 – 5,50 = C+</td>
            </tr>
            <tr>
              <td>6,01 – 6,50 = B+</td>
              <td>3,51 – 4,50 = C</td>
            </tr>
            <tr>
              <td>5,51 – 6,00 = B</td>
              <td>
                <=3,50=Tidak lulus</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="text-center">Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br><span style="font-weight: bold;">Komisi Penguji</span></div>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 1/Pembimbing</p><br>
                <img src="<?= $qr_penguji_1; ?>"><br>
                <?= $penguji_1->nama_lengkap . '<br>NIDN. ' . $penguji_1->nidn; ?>
              </td>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 2</p><br>
                <img src="<?= $qr_penguji_2; ?>"><br>
                <?= $penguji_2->nama_lengkap . '<br>NIDN. ' . $penguji_2->nidn; ?>
              </td>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 3</p><br>
                <img src="<?= $qr_penguji_3; ?>"><br>
                <?= $penguji_3->nama_lengkap . '<br>NIDN. ' . $penguji_3->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>
        <p>Formulir dibuat rangkap 2 untuk:</p>
        <ol>
          <li>Mahasiswa</li>
          <li>Program Studi</li>
        </ol>
      </div>


      <div style="page-break-after: always;"></div>
      <p style="font-size:14px;font-weight: bold;" class="text-center">BERITA ACARA UJIAN TUGAS AKHIR</p>
      <div class="row">
        <div class="column right">
          Pada hari ini: <?= $berita_acara->sidang_hari; ?>, tanggal <?= tgl_indo($berita_acara->sidang_tanggal); ?> telah diselenggarakan Ujian Tugas Akhir di ruang Sidang Tugas Akhir Politeknik Wilmar Bisnis Indonesia yang ke -1 (satu).
        </div>
      </div>
      <div class="row">
        <table class="table table-borderless2">
          <tbody>
            <tr>
              <td style="width:180px">Nama</td>
              <td style="width: 10px;">:</td>
              <td><?= ucwords(strtolower($judul->nm_pd)); ?></td>
            </tr>
            <tr>
              <td>Nomor Induk Mahasiswa (NIM)</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nipd; ?></td>
            </tr>
            <tr>
              <td>Program Studi</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nm_lemb; ?></td>
            </tr>
            <tr>
              <td colspan="3" style="font-weight: bold;">Dengan mempertahankan Tugas Akhir yang berjudul:</td>
            </tr>
            <tr>
              <td colspan="3" style="font-weight: bold;padding:10px;" class="text-center">
                "<?= $judul->judul_ditetapkan; ?>"
              </td>
            </tr>
            <tr>
              <td>Dosen Pembimbing Utama</td>
              <td>:</td>
              <td><?= $dosen_nama->nama_lengkap; ?></td>
            </tr>
            <tr>
              <td colspan="3">Majelis Penguji</td>
            </tr>
            <tr>
              <td>Penguji 1/Ketua</td>
              <td>:</td>
              <td><?= $penguji_1->nama_lengkap; ?></td>
            </tr>
            <tr>
              <td>Penguji 2</td>
              <td>:</td>
              <td><?= $penguji_2->nama_lengkap; ?></td>
            </tr>
            <tr>
              <td>Penguji 3</td>
              <td>:</td>
              <td><?= $penguji_3->nama_lengkap; ?></td>
            </tr>
            
            <tr>
              <td colspan="3">Sidang Panitia Ujian Tugas Akhir memutuskan bahwa yang bersangkutan dinyatakan :</td>
            </tr>
          </tbody>
        </table>

        <table class="table table-black">
          <tbody>
            <tr>
              <td class="text-center" style="font-weight: bold;padding:15px"><?= $judul->sidang_hasil; ?></td>
            </tr>

          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="text-center">Deli Serdang, <?= tgl_indo($berita_acara->sidang_tanggal); ?><br><span style="font-weight: bold;">Tim Penguji,</span></div>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 1/Pembimbing</p><br>
                <img src="<?= $qr_penguji_1; ?>"><br>
                <?= $penguji_1->nama_lengkap . '<br>NIDN. ' . $penguji_1->nidn; ?>
              </td>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 2</p><br>
                <img src="<?= $qr_penguji_2; ?>"><br>
                <?= $penguji_2->nama_lengkap . '<br>NIDN. ' . $penguji_2->nidn; ?>
              </td>
              <td class="text-center" style="width: 33.33%;">
                <p style="font-weight:bold">Penguji 3</p><br>
                <img src="<?= $qr_penguji_3; ?>"><br>
                <?= $penguji_3->nama_lengkap . '<br>NIDN. ' . $penguji_3->nidn; ?>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
      <div class="row">
        <div class="text-center"><span style="font-weight: bold;">Disahkan Oleh,<br>Ketua,</span></div>
        <div class="text-center"><img src="<?= $qr_kaprodi; ?>"></div>
        <div class="text-center"><?= $nama_kaprodi->nm_pejabat_khs_1; ?></div>
        <div class="text-center">NIDN. <?= $nama_kaprodi->nidn_pejabat_khs_1; ?></div>
      </div>
      <div style="page-break-after: always;"></div>
      <?php
      $nama = $nidn = $titel = '';
      $i = 1;
      foreach ($usulan_semhas as $key => $value) {
        if ($value->id_penilai == $penguji_1->id_ptk) {
          $nama = $penguji_1->nama_lengkap;
          $nidn = $penguji_1->nidn;
          $titel = "Penguji 1,";
        } elseif ($value->id_penilai == $penguji_2->id_ptk) {
          $nama = $penguji_2->nama_lengkap;
          $nidn = $penguji_2->nidn;
          $titel = "Penguji 2,";
        } elseif ($value->id_penilai == $penguji_3->id_ptk) {
          $nama = $penguji_3->nama_lengkap;
          $nidn = $penguji_3->nidn;
          $titel = "Penguji 3,";
        }
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="6" class="text-center">
                <p style="font-size:13px; font-weight: bold;margin-top: 3px;margin-bottom: 3px">PERMINTAAN REVISI UJIAN TUGAS AKHIR</p>
              </td>
            </tr>
            <tr>
              <td style="width:50px">Nama</td>
              <td style="width: 10px;">:</td>
              <td><?= ucwords(strtolower($judul->nm_pd)); ?></td>
              <td rowspan="2" style="width:90px">Program Studi</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nm_lemb; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td style="width: 10px;">:</td>
              <td><?= $judul->nipd; ?></td>
            </tr>
          </tbody>
        </table>
        <div class="custom">URAIAN REVISI</div>
        <div class="custom-3">
          <?php
          echo  nl2br($value->sidang_uraian_revisi)  . '</p><br>';
          ?>
        </div>
        <div class="row">
          <table class="table table-borderless2">
            <tbody>
              <tr>
                <td style="width:55%" class="text-center"></td>
                <td>Persetujuan telah melakukan Revisi,<br>Deli Serdang, ______________________<br><?= $titel; ?></td>
              </tr>
              <tr>
                <td style="width:55%" class="text-center"></td>
                <td>
                  <br>
                  <br>
                  <span style="font-weight: bold;"><?= $nama; ?></span><br>
                  NIDN : <?= $nidn; ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <?php
        if ($i < 3) {
        ?>
          <div style="page-break-after: always;"></div>
        <?php
        }
        ?>

      <?php
        $i++;
      }
      ?>
    </div>
  </div>
</body>

</html>