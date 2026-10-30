<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KONFIRMASI</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <style type="text/css">
    body {
      /* font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; */
      font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
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
      line-height: 1.5;
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
      line-height: 1.6;
      vertical-align: top;
      /* font-size: 10px !important; */
    }

    .table-black th,
    .table-black td {
      border: 1px solid #C0C0C0 !important;
      padding: 3px !important;
      /* font-size: 10px !important; */
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



    .text-ku {
      display: inline-block;
      border: solid 1px #000;
      min-height: 10px;
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
    

    <div class="row">
      <div class="col-md-12 text-right">
        <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="250px" />
      </div>
      <br>
      <?php
      if ($data_eoffice->id_kategori_pengantar == 1) {
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td class="text-center"><b><u>SURAT KETERANGAN AKTIF</u></b><br> Nomor : <?= $data_eoffice->nomor_surat; ?></td>
            </tr>
          </tbody>
        </table>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan=2>Yang bertandatangan di bawah ini:</td>
            </tr>
            <tr>
              <td style="width: 200px;">Nama</td>
              <td style="width: 5px;">:</td>
              <td>Dr. Jenny Elisabeth</td>
            </tr>
            <tr>
              <td>Jabatan</td>
              <td>:</td>
              <td>Direktur</td>
            </tr>
            <tr>
              <td colspan="3">dengan ini menerangkan dan menyatakan bahwa :</td>
            </tr>
            <tr>
              <td>Nama</td>
              <td>:</td>
              <td><?= $data_eoffice->nm_pd; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td>:</td>
              <td><?= $data_eoffice->nipd; ?></td>
            </tr>
            <tr>
              <td>Tempat Lahir</td>
              <td>:</td>
              <td><?= $data_eoffice->tmpt_lahir; ?></td>
            </tr>
            <tr>
              <td>Tanggal Lahir</td>
              <td>:</td>
              <!-- <td><?= date('d-m-Y', strtotime($data_eoffice->tgl_lahir)); ?></td> -->
              <td><?= tgl_indo($data_eoffice->tgl_lahir); ?></td>
            </tr>

            <tr>
              <td>Program Studi</td>
              <td>:</td>
              <td><?= $data_eoffice->nm_lemb; ?></td>
            </tr>
            <tr>
              <td style="vertical-align: top;">Alamat Rumah</td>
              <td style="vertical-align: top;">:</td>
              <td><?= $data_eoffice->mhs_alamat; ?></td>
            </tr>
            <tr>
              <td>Telepon/HP</td>
              <td>:</td>
              <td><?= $data_eoffice->mhs_seluler; ?></td>
            </tr>
            <tr>
              <td>Pekerjaan Orang Tua</td>
              <td>:</td>
              <td><?= $data_eoffice->mhs_pekerjaan_ortu; ?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>adalah benar-benar mahasiswa di Politeknik Wilmar Bisnis Indonesia dan sampai saat ini terdaftar aktif sebagai mahasiswa. </p>
              </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Demikian surat pernyataan ini dibuat dengan sesungguhnya, dan untuk digunakan sebagaimana mestinya.</p>
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td style="padding-bottom: 0px;">

                <?php
                if ($data_eoffice->id_status == 4) {
                ?>
                  <div>
                    <p>Deli Serdang, <?= tgl_indo($data_eoffice->tanggal_surat); ?></p>
                    <p>Direktur,</p>
                    <br>
                    <br>
                    <br>
                    <p>Dr. Jenny Elisabeth</p>
                  </div>


                <?php
                }
                ?>

              </td>
            </tr>
          </tbody>
        </table>
      <?php

      } elseif ($data_eoffice->id_kategori_pengantar == 2) {

      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td>Deli Serdang, <?= tgl_indo($data_eoffice->tanggal_surat); ?></td>
            </tr>
          </tbody>
        </table>

        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 100px;">Nomor</td>
              <td>: <?= $data_eoffice->nomor_surat; ?></td>
            </tr>
            <tr>
              <td>Lampiran</td>
              <td>: </td>
            </tr>
            <tr>
              <td>Hal</td>
              <td>: Permohonan Ijin Magang/PKL</td>
            </tr>
            <tr>
              <td>Kepada Yth</td>
              <td>: Pimpinan/Kepala</td>
            </tr>
            <tr>
              
              <td colspan="2"><?= $data_eoffice->magang_instansi; ?></td>
            </tr>
            <tr>
              
              <td colspan="2">Di tempat</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="3">Dengan hormat, </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Proses pembelajaran di Politeknik Wilmar Bisnis Indonesia didesain agar mahasiswa bisa memperoleh
                  pemahaman pengetahuan dan pemerolehan keterampilan yang selaras dengan perkembangan proses bisnis terkini.
                  Salah satu proses pembelajaran yang dirancang untuk mendekatkan mahasiswa dengan praktik-praktik bisnis
                  terkini adalah dengan menyelenggarakan Magang/PKL.</p>
              </td>
            </tr>
            <tr>
              <td colspan="3">
                <p>Sehubungan dengan hal tersebut kami mohon untuk memperkenankan mahasiswa kami : </p>
              </td>
            </tr>
            <tr>
              <td style="width: 200px;">Nama</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nm_pd; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nipd; ?></td>
            </tr>
            <tr>
              <td>Tempat Lahir</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->tmpt_lahir; ?></td>
            </tr>
            <tr>
              <td>Tanggal Lahir</td>
              <td style="width: 5px;">:</td>
              <td><?= tgl_indo($data_eoffice->tgl_lahir); ?></td>
            </tr>
            <tr>
              <td>Program Studi</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nm_lemb; ?></td>
            </tr>
            <tr>
              <td>Alamat Rumah</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->mhs_alamat; ?></td>
            </tr>
            <tr>
              <td>Telepon/HP</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->mhs_seluler; ?></td>
            </tr>
            <tr>
              <td colspan="3">
                <p>untuk dapat melaksanakan kegiatan magang/PKL di instansi/perusahaan yang Bapak/Ibu pimpin.</p>
              </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Demikian surat permohonan ini kami sampaikan, atas bantuan dan kerjasama yang baik diucapkan terima kasih.</p>
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td style="padding-bottom: 0px;">

                <?php
                if ($data_eoffice->id_status == 4) {
                ?>
                  <div>
                    <p>Direktur,</p>
                    <br>
                    <br>
                    <br>
                    <p>Dr. Jenny Elisabeth</p>
                  </div>
                <?php
                }
                ?>

              </td>
            </tr>

          </tbody>
        </table>
      <?php
      } elseif ($data_eoffice->id_kategori_pengantar == 3) {
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td>Deli Serdang, <?= tgl_indo($data_eoffice->tanggal_surat); ?></td>
            </tr>
          </tbody>
        </table>

        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 100px;">Nomor</td>
              <td>: <?= $data_eoffice->nomor_surat; ?></td>
            </tr>

            <tr>
              <td>Hal</td>
              <td>: Pengantar Studi Literatur dan Pengambilan Data</td>
            </tr>
            <tr>
              <td>Kepada Yth</td>
              <td>: Pimpinan/Kepala</td>
            </tr>
            <tr>
              
              <td colspan="2"><?= $data_eoffice->literatur_instansi; ?></td>
            </tr>
            <tr>
              
              <td colspan="2">Di tempat</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="3">Dengan hormat, </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Yang bertandatangan di bawah ini Direktur Politeknik Wilmar Bisnis Indonesia menerangkan bahwa :</p>
              </td>
            </tr>
            <tr>
              <td style="width:200px">Nama</td>
              <td style="width:5px">: </td>
              <td><?= $data_eoffice->nm_pd; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td>: </td>
              <td><?= $data_eoffice->nipd; ?></td>
            </tr>
            <tr>
              <td>Program Studi</td>
              <td>: </td>
              <td><?= $data_eoffice->nm_lemb; ?></td>
            </tr>
            <tr>
              <td>Alamat Tinggal</td>
              <td>: </td>
              <td><?= $data_eoffice->mhs_alamat; ?></td>
            </tr>
            <tr>
              <td>Rencana Judul</td>
              <td>: </td>
              <td><?= $data_eoffice->literatur_judul_skripsi; ?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Adalah benar-benar mahasiswa Politeknik Wilmar Bisnis Indonesia yang bermaksud untuk melaksanakan studi literatur
                  dan pengambilan data di instansi/perusahaan Bapak/Ibu untuk keperluan penulisan skripsi dalam rangka penyelesaian studi.
                  Untuk hal tersebut, kami mohon bantuan agar mahasiswa tersebut diperkenankan memeroleh data sesuai dengan kebutuhan
                  penelitiannya. </p>
              </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Demikian kami sampaikan, atas bantuan dan kerjasama yang baik diucapkan terima kasih.</p>
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td style="padding-bottom: 0px;">
                <?php
                if ($data_eoffice->id_status == 4) {
                ?>
                  <div>
                    <p>Direktur,</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p>Dr. Jenny Elisabeth</p>
                  </div>
                <?php
                }
                ?>

              </td>
            </tr>
          </tbody>
        </table>
      <?php
      } elseif ($data_eoffice->id_kategori_pengantar == 4) {
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td>Deli Serdang, <?= tgl_indo($data_eoffice->tanggal_surat); ?></td>
            </tr>
          </tbody>
        </table>

        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 100px;">Nomor</td>
              <td style="width:2px">:</td>
              <td><?= $data_eoffice->nomor_surat; ?></td>
            </tr>

            <tr>
              <td>Hal</td>
              <td style="width:2px">:</td>
              <td>Surat Pengantar Pembukaan Rekening<br>Bagi Mahasiswa Politeknik WBI</td>
            </tr>
            <tr>
              <td>Kepada Yth. </td>
              <td style="width:2px">:</td>
              <td>Pimpinan Bank </td>
            </tr>
            <tr>
              <td>Di <br>Tempat</td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="3">Yang bertandatangan di bawah ini :</td>
            </tr>
            <tr>
              <td style="width: 200px;">Nama</td>
              <td style="width: 5px;">:</td>
              <td>Dr. Jenny Elisabeth</td>
            </tr>
            <tr>
              <td>Jabatan</td>
              <td>:</td>
              <td>Direktur</td>
            </tr>
            <tr>
              <td colspan=3>dengan ini menyatakan dan menerangkan bahwa :</td>
            </tr>
            <tr>
              <td style="width: 200px;">Nama</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nm_pd; ?></td>
            </tr>
            <tr>
              <td>NIM</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nipd; ?></td>
            </tr>
            <tr>
              <td>Tempat Lahir</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->tmpt_lahir; ?></td>
            </tr>
            <tr>
              <td>Tanggal Lahir</td>
              <td style="width: 5px;">:</td>
              <td><?= tgl_indo($data_eoffice->tgl_lahir); ?></td>
            </tr>
            <tr>
              <td>Program Studi</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->nm_lemb; ?></td>
            </tr>
            <tr>
              <td>Alamat Rumah</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->mhs_alamat; ?></td>
            </tr>
            <tr>
              <td>Telepon/HP</td>
              <td style="width: 5px;">:</td>
              <td><?= $data_eoffice->mhs_seluler; ?></td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Adalah benar-benar mahasiswa Politeknik Wilmar Bisnis Indonesia dalam hal ini bermaksud untuk membuka rekening bank guna keperluan keuangan yang bersangkutan selama melaksanakan studi.</p>
              </td>
            </tr>
            <tr>
              <td colspan="3" style="text-align: justify;letter-spacing: 0;">
                <p>Demikian surat pernyataan ini dibuat dengan sesungguhnya, dan untuk digunakan sebagaimana mestinya.</p>
              </td>
            </tr>
          </tbody>
        </table>
        <br>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td style="width: 450px;">&nbsp;</td>
              <td style="padding-bottom: 0px;">

                <?php
                if ($data_eoffice->id_status == 4) {
                ?>
                  <div>
                    <p>Deli Serdang, <?= tgl_indo($data_eoffice->tanggal_surat); ?><br>Direktur,</p>
                    <br>
                    <br>
                    <br>
                    <p>Dr. Jenny Elisabeth</p>
                  </div>
                <?php
                }
                ?>

              </td>
            </tr>

          </tbody>
        </table>
      <?php
      }
      ?>


    </div>
  </div>
  <?php
  if ($data_eoffice->id_status == 4) {
  ?>
    <div style="position: absolute; left:<?= $left; ?>; right: 0; top: <?= $top; ?>; bottom: 0;">
      <img src="<?= FCPATH . 'assets/image_web/tandatangan.png'; ?>" style="width: 200px; margin: 0;" />
    </div>
  <?php
  }
  ?>

  <div style="font-size: 10px; color: #009999 ;bottom:10;position:absolute">
    <p>Jalan Kapten Batu Sihombing<br>
      Desa Medan Estate, Kec. Percut Sei Tuan<br>
      Kabupaten Deli Serdang – Sumatera Utara<br>
      (061) 42074111
    </p>
  </div>


</body>

</html>