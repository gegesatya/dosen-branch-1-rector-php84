<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Data Akademik Mahasiswa</title>
  

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
      <table class="table table-borderless">
        <tbody>
          <tr>
            <td colspan="4" class="text-center">
              <h4 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">INFORMASI DAN DATA STATISTIK WBI </h4>
              <h5 style=" font-weight: bold;margin-top: 2px;margin-bottom: 1px">Tahun Ajaran <?= $nm_smt; ?> </h5>
            </td>
          </tr>

        </tbody>
      </table>
      <h5>A. Rata-rata IPS dan IPK Mahasiswa WBI</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>

            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ips, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ips, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ips, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ips, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a + $mhs_aktif_b + $mhs_aktif_c + $mhs_aktif_d; ?></td>
          </tr>
          <tfoot>
      </table>
      <h5>B. Rata-rata IPS dan IPK Mahasiswa WBI PRODI</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>

            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>

            <td class="text-center"><?= round($sem_a_ips_prodi, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_prodi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_prodi; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>

            <td class="text-center"><?= round($sem_b_ips_prodi, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_prodi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_prodi; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>

            <td class="text-center"><?= round($sem_c_ips_prodi, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_prodi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_prodi; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>

            <td class="text-center"><?= round($sem_d_ips_prodi, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_prodi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_prodi; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_prodi + $mhs_aktif_b_prodi + $mhs_aktif_c_prodi + $mhs_aktif_d_prodi; ?></td>
          </tr>
          <tfoot>
      </table>

      <h5>F. Sebaran IP Semester Mahasiswa</h5>
      <?php
      $total_wbi = $sebaran_ips_wbi[0] + $sebaran_ips_wbi[1] + $sebaran_ips_wbi[2] + $sebaran_ips_wbi[3];
      $total_prodi = $sebaran_ips_prodi[0] + $sebaran_ips_prodi[1] + $sebaran_ips_prodi[2] + $sebaran_ips_prodi[3];
      

      $persen_wbi_a = round($sebaran_ips_wbi[0] * 100 / $total_wbi, 2);
      $persen_wbi_b = round($sebaran_ips_wbi[1] * 100 / $total_wbi, 2);
      $persen_wbi_c = round($sebaran_ips_wbi[2] * 100 / $total_wbi, 2);
      $persen_wbi_d = round($sebaran_ips_wbi[3] * 100 / $total_wbi, 2);

      $persen_prodi_a = round($sebaran_ips_prodi[0] * 100 / $total_prodi, 2);
      $persen_prodi_b = round($sebaran_ips_prodi[1] * 100 / $total_prodi, 2);
      $persen_prodi_c = round($sebaran_ips_prodi[2] * 100 / $total_prodi, 2);
      $persen_prodi_d = round($sebaran_ips_prodi[3] * 100 / $total_prodi, 2);

      
      ?>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">Kriteria</th>
            <th class="text-center">> 3.5</th>
            <th class="text-center">3.0 - 3.5</th>
            <th class="text-center">2.5 - < 3.0</th>
            <th class="text-center">
              < 2.5</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center">WBI</td>
            <td class="text-center"><?= $persen_wbi_a; ?> %</td>
            <td class="text-center"><?= $persen_wbi_b; ?> %</td>
            <td class="text-center"><?= $persen_wbi_c; ?> %</td>
            <td class="text-center"><?= $persen_wbi_d; ?> %</td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center">PROGRAM STUDI</td>
            <td class="text-center"><?= $persen_prodi_a; ?> %</td>
            <td class="text-center"><?= $persen_prodi_b; ?> %</td>
            <td class="text-center"><?= $persen_prodi_c; ?> %</td>
            <td class="text-center"><?= $persen_prodi_d; ?> %</td>
          </tr>
          <!--  <tr>
            <td class="text-center">3</td>
            <td class="text-center">AP</td>
            <td class="text-center"><?= $persen_ap_a; ?> %</td>
            <td class="text-center"><?= $persen_ap_b; ?> %</td>
            <td class="text-center"><?= $persen_ap_c; ?> %</td>
            <td class="text-center"><?= $persen_ap_d; ?> %</td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center">AH</td>
            <td class="text-center"><?= $persen_ah_a; ?> %</td>
            <td class="text-center"><?= $persen_ah_b; ?> %</td>
            <td class="text-center"><?= $persen_ah_c; ?> %</td>
            <td class="text-center"><?= $persen_ah_d; ?> %</td>
          </tr>
          <tr>
            <td class="text-center">5</td>
            <td class="text-center">PKA</td>
            <td class="text-center"><?= $persen_pka_a; ?> %</td>
            <td class="text-center"><?= $persen_pka_b; ?> %</td>
            <td class="text-center"><?= $persen_pka_c; ?> %</td>
            <td class="text-center"><?= $persen_pka_d; ?> %</td>
          </tr> -->
        </tbody>
      </table>

      <!-- <h5>G. Evaluasi Pembelajaran Oleh Mahasiswa</h5>
      <h6>Dosen Tetap</h6>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">NM MK</th>
            <th class="text-center">NM Dosen</th>
            <th class="text-center">PRODI</th>
            <th class="text-center">EPOM</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          foreach ($epom_tetap as $key => $value) {
          ?>
            <tr>
              <td class="text-center"><?= $no; ?></td>
              <td><?= $value->kode_mk . ' | ' . $value->nm_mk; ?></td>
              <td><?= $value->nm_ptk; ?></td>
              <td><?= $value->nm_prodi_english; ?></td>
              <td><?= $value->nilai_epom; ?></td>
            </tr>
          <?php
            $no++;
          }
          ?>
        </tbody>
      </table>

      <h6>Dosen Tidak Tetap</h6>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">NM MK</th>
            <th class="text-center">NM Dosen</th>
            <th class="text-center">PRODI</th>
            <th class="text-center">EPOM</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          foreach ($epom_tidak_tetap as $key => $value) {
          ?>
            <tr>
              <td class="text-center"><?= $no; ?></td>
              <td><?= $value->kode_mk . ' | ' . $value->nm_mk; ?></td>
              <td><?= $value->nm_ptk; ?></td>
              <td><?= $value->nm_prodi_english; ?></td>
              <td><?= $value->nilai_epom; ?></td>
            </tr>
          <?php
            $no++;
          }
          ?>


        </tbody>
      </table> -->

  </div>
  </section>
  </div>
</body>

</html>