<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PROFIL Mahasiswa</title>
  

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link href="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="<?= base_url('assets/Font_Awesome/css/font-awesome.min.css'); ?>">

  <style type="text/css">
    .table-borderless th,
    .table-borderless td {
      border: 0px solid #fff !important;
      padding: 3px !important;
      font-size: 12px !important;
      margin-bottom: 10px
    }

    .table-gg th,
    .table-gg td {
      border-top: 1px solid #ddd !important;

      padding: 4px !important;
      font-size: 12px !important;
      margin-bottom: 10px
    }

    .table-black th,
    .table-black td {
      border: 1px solid #000 !important;
      padding: 4px !important;
      font-size: 12px !important;

    }


    hr {
      margin-top: 1px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #000;
    }
  </style>
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body style="margin-left: 50px">
  
  <div class="wrapper">
    
    <section class="invoice">
      

      <table class="table table-borderless">
        <tbody>
          <tr>
            <td rowspan="3">
              <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="150px" style="margin-top:10px;margin-right: 20px;" />
            </td>
            <td>
              <h4 style="font-size: 14px; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
            </td>
          </tr>
          <tr>
            <td>
              <p style="font-size: 12px;">Jl. Kapten Batu Sihombing Medan Estate Deli Serdang Sumatera Utara</p>
            </td>
          </tr>
          <tr>
            <td>
              <p style="font-size: 12px;">Phone: +62 61 4207 4111, Website: http://www.wbi.ac.id</p>
            </td>
          </tr>
        </tbody>
      </table>
      <hr />
      <table class="table table-gg">
        <tbody>
          <tr>
            <td colspan="2">
              <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">BIODATA </h4>
            </td>
          </tr>
          <tr>
            <td width="300px">Nama </td>
            <td>: <?= $mahasiswa->nm_pd; ?></td>
          </tr>
          <tr>
            <td>Jenis Kelamin</td>
            <td>: <?= $mahasiswa->jk == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
          </tr>
          <tr>
            <td>Tempat/Tgl Lahir</td>
            <td>: <?= $mahasiswa->tmpt_lahir . '/' . $mahasiswa->tgl_lahir; ?></td>
          </tr>
          <tr>
            <td>NIK</td>
            <td>: <?= $mahasiswa->nik; ?></td>
          </tr>
          <tr>
            <td>NISN</td>
            <td>: <?= $mahasiswa->nisn; ?></td>
          </tr>
          <tr>
            <td>Agama</td>
            <td>: <?= !empty($mahasiswa->id_agama) ? $this->db->where('id_agama', $mahasiswa->id_agama)->get('agama')->row()->nm_agama : '-'; ?></td>
          </tr>
          <tr>
            <td>Suku</td>
            <td>: <?= $mahasiswa->suku; ?></td>
          </tr>
          <tr>
            <td>Alamat</td>
            <td>: <?= $mahasiswa->jln; ?></td>
          </tr>
          <tr>
            <td>RT/RW</td>
            <td>: <?= $mahasiswa->rt . '/' . $mahasiswa->rw; ?></td>
          </tr>
          <tr>
            <td>Dusun</td>
            <td>: <?= $mahasiswa->nm_dsn; ?></td>
          </tr>
          <tr>
            <td>Kelurahan</td>
            <td>: <?= $mahasiswa->ds_kel; ?></td>
          </tr>
          <tr>
            <td>Kecamatan</td>
            <td>: <?= !empty($mahasiswa->id_wil) ? $this->db->where('id_wil', $mahasiswa->id_wil)->get('wilayah')->row()->nm_wil : '-'; ?></td>
          </tr>
          <tr>
            <td>Propinsi</td>
            <td>: <?= !empty($mahasiswa->id_propinsi) ? $this->db->where('id_wil', $mahasiswa->id_propinsi)->get('wilayah')->row()->nm_wil : '-'; ?></td>
          </tr>
          <tr>
            <td>Alamat Kos</td>
            <td>: <?= $mahasiswa->alamat_kos; ?></td>
          </tr>
          <tr>
            <td>Telp. Rumah</td>
            <td>: <?= $mahasiswa->telepon_rumah; ?></td>
          </tr>
          <tr>
            <td>Telp. Seluler</td>
            <td>: <?= $mahasiswa->telepon_seluler; ?></td>
          </tr>
          <tr>
            <td>Email</td>
            <td>: <?= $mahasiswa->email; ?></td>
          </tr>
          <tr>
            <td>Nama Ayah</td>
            <td>: <?= $mahasiswa->nm_ayah; ?></td>
          </tr>
          <tr>
            <td>Tempat/Tgl Lahir Ayah</td>
            <td>: <?= $mahasiswa->tmpt_lahir_ayah . ' / ' . $mahasiswa->tgl_lahir_ayah; ?></td>
          </tr>
          <tr>
            <td>Pekerjaan Ayah</td>
            <td>: <?= !empty($mahasiswa->id_pekerjaan_ayah) ? $this->db->where('id_pekerjaan', $mahasiswa->id_pekerjaan_ayah)->get('pekerjaan')->row()->nm_pekerjaan : '-'; ?></td>
          </tr>
          <tr>
            <td>Penghasilan Ayah</td>
            <td>: <?= !empty($mahasiswa->id_penghasilan_ayah) ? $this->db->where('id_penghasilan', $mahasiswa->id_penghasilan_ayah)->get('penghasilan')->row()->nm_penghasilan : '-'; ?></td>
          </tr>
          <tr>
            <td>Nama Ibu Kandung</td>
            <td>: <?= $mahasiswa->nm_ibu_kandung; ?></td>
          </tr>
          <tr>
            <td>Tempat/Tgl Lahir Ibu</td>
            <td>: <?= $mahasiswa->tmpt_lahir_ibu . ' / ' . $mahasiswa->tgl_lahir_ibu; ?></td>
          </tr>
          <tr>
            <td>Pekerjaan ibu</td>
            <td>: <?= !empty($mahasiswa->id_pekerjaan_ibu) ? $this->db->where('id_pekerjaan', $mahasiswa->id_pekerjaan_ibu)->get('pekerjaan')->row()->nm_pekerjaan : '-'; ?></td>
          </tr>
          <tr>
            <td>Penghasilan ibu</td>
            <td>: <?= !empty($mahasiswa->id_penghasilan_ibu) ? $this->db->where('id_penghasilan', $mahasiswa->id_penghasilan_ibu)->get('penghasilan')->row()->nm_penghasilan : '-'; ?></td>
          </tr>

        </tbody>
      </table>
      <table class="table table-gg">
        <tbody>
          <tr>
            <td colspan="2">
              <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">AKADEMIK </h4>
            </td>
          </tr>
          <tr>
            <td width="300px">NIM </td>
            <td>: <?= $mahasiswa->nipd; ?></td>
          </tr>
          <tr>
            <td>Program Studi</td>
            <td>: <?= $mahasiswa->nm_lemb; ?></td>
          </tr>
          <tr>
            <td>Tanggal Masuk</td>
            <td>: <?= $mahasiswa->tgl_masuk_sp; ?></td>
          </tr>
          <tr>
            <td>Dosen Wali</td>
            <td>: <?= $mahasiswa->nm_ptk; ?></td>
          </tr>
        </tbody>
      </table>
      <p style="page-break-before: always">
      <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">DATA NILAI </h4>

      <?php
      if (is_array($tabel)) {
        foreach ($tabel as $t) {
          echo $t;
        }
      } else {
        echo $tabel;
      }
      ?>

      <p style="page-break-before: always">
      <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">KRITIK SARAN </h4>

      <?php
      if (is_array($tbl_kritik_saran)) {
        foreach ($tbl_kritik_saran as $t) {
          echo $t;
        }
      } else {
        echo $tbl_kritik_saran;
      }
      ?>

  </div>
  </section>
  </div>
</body>

</html>