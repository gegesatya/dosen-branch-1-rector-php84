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
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips, 2); ?></td>
            
            <td class="text-center"><?= round($sem_a_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips, 2); ?></td>
            
            <td class="text-center"><?= round($sem_b_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips, 2); ?></td>
            
            <td class="text-center"><?= round($sem_c_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips, 2); ?></td>
            
            <td class="text-center"><?= round($sem_d_ipk, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a + $mhs_aktif_b + $mhs_aktif_c + $mhs_aktif_d; ?></td>
          </tr>
          <tfoot>
      </table>
      <h5>B. Rata-rata IPS dan IPK Mahasiswa WBI PRODI Manajemen Pemasaran Internasional</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_mpi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_mpi; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_mpi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_mpi; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_mpi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_mpi; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips_mpi, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_mpi, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_mpi; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_mpi + $mhs_aktif_b_mpi + $mhs_aktif_c_mpi + $mhs_aktif_d_mpi; ?></td>
          </tr>
          <tfoot>
      </table>

      <h5>C. Rata-rata IPS dan IPK Mahasiswa WBI PRODI Akuntansi Perpajakan</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_ap, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_ap; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_ap, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_ap; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_ap, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_ap; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips_ap, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_ap, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_ap; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_ap + $mhs_aktif_b_ap + $mhs_aktif_c_ap + $mhs_aktif_d_ap; ?></td>
          </tr>
          <tfoot>
      </table>
      <h5>D. Rata-rata IPS dan IPK Mahasiswa WBI PRODI Agribisnis Hortikultura</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_ah, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_ah; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_ah, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_ah; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_ah, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_ah; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips_ah, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_ah, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_ah; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_ah + $mhs_aktif_b_ah + $mhs_aktif_c_ah + $mhs_aktif_d_ah; ?></td>
          </tr>
          <tfoot>
      </table>

      <h5>E. Rata-rata IPS dan IPK Mahasiswa WBI PRODI Pengelolaan Konvensi Acara</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_pka, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_pka; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_pka, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_pka; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_pka, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_pka; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips_pka, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_pka, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_pka; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_pka + $mhs_aktif_b_pka + $mhs_aktif_c_pka + $mhs_aktif_d_pka; ?></td>
          </tr>
          <tfoot>
      </table>

      <h5>F. Rata-rata IPS dan IPK Mahasiswa WBI PRODI TRPL</h5>
      <table class="table table-black">
        <thead>
          <tr>
            <th class="text-center" style="width: 20px;">No</th>
            <th class="text-center">SMT</th>
            <th class="text-center">MK Institusi</th>
            <th class="text-center">IPS</th>
            <th class="text-center">IPK</th>
            <th class="text-center">MHS Aktif</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td class="text-center"><?= $sem_a; ?></td>
            <td class="text-center"><?= round($sem_a_ip_institusi_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ips_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_a_ipk_trpl, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_a_trpl; ?></td>
          </tr>
          <tr>
            <td class="text-center">2</td>
            <td class="text-center"><?= $sem_b; ?></td>
            <td class="text-center"><?= round($sem_b_ip_institusi_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ips_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_b_ipk_trpl, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_b_trpl; ?></td>
          </tr>
          <tr>
            <td class="text-center">3</td>
            <td class="text-center"><?= $sem_c; ?></td>
            <td class="text-center"><?= round($sem_c_ip_institusi_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ips_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_c_ipk_trpl, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_c_trpl; ?></td>
          </tr>
          <tr>
            <td class="text-center">4</td>
            <td class="text-center"><?= $sem_d; ?></td>
            <td class="text-center"><?= round($sem_d_ip_institusi_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ips_trpl, 2); ?></td>
            <td class="text-center"><?= round($sem_d_ipk_trpl, 2); ?></td>
            <td class="text-center"><?= $mhs_aktif_d_trpl; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-center"><?= $mhs_aktif_a_trpl + $mhs_aktif_b_trpl + $mhs_aktif_c_trpl + $mhs_aktif_d_trpl; ?></td>
          </tr>
          <tfoot>
      </table>

      <h5>F. Sebaran IP Semester Mahasiswa</h5>
      <?php
      $total_wbi = $sebaran_ips_wbi[0] + $sebaran_ips_wbi[1] + $sebaran_ips_wbi[2] + $sebaran_ips_wbi[3];
      $total_mpi = $sebaran_ips_mpi[0] + $sebaran_ips_mpi[1] + $sebaran_ips_mpi[2] + $sebaran_ips_mpi[3];
      $total_ap = $sebaran_ips_ap[0] + $sebaran_ips_ap[1] + $sebaran_ips_ap[2] + $sebaran_ips_ap[3];
      $total_ah = $sebaran_ips_ah[0] + $sebaran_ips_ah[1] + $sebaran_ips_ah[2] + $sebaran_ips_ah[3];
      $total_pka = $sebaran_ips_pka[0] + $sebaran_ips_pka[1] + $sebaran_ips_pka[2] + $sebaran_ips_pka[3];

      $persen_wbi_a = round($sebaran_ips_wbi[0] * 100 / $total_wbi, 2);
      $persen_wbi_b = round($sebaran_ips_wbi[1] * 100 / $total_wbi, 2);
      $persen_wbi_c = round($sebaran_ips_wbi[2] * 100 / $total_wbi, 2);
      $persen_wbi_d = round($sebaran_ips_wbi[3] * 100 / $total_wbi, 2);

      $persen_mpi_a = round($sebaran_ips_mpi[0] * 100 / $total_mpi, 2);
      $persen_mpi_b = round($sebaran_ips_mpi[1] * 100 / $total_mpi, 2);
      $persen_mpi_c = round($sebaran_ips_mpi[2] * 100 / $total_mpi, 2);
      $persen_mpi_d = round($sebaran_ips_mpi[3] * 100 / $total_mpi, 2);

      $persen_ap_a = round($sebaran_ips_ap[0] * 100 / $total_ap, 2);
      $persen_ap_b = round($sebaran_ips_ap[1] * 100 / $total_ap, 2);
      $persen_ap_c = round($sebaran_ips_ap[2] * 100 / $total_ap, 2);
      $persen_ap_d = round($sebaran_ips_ap[3] * 100 / $total_ap, 2);

      $persen_ah_a = round($sebaran_ips_ah[0] * 100 / $total_ah, 2);
      $persen_ah_b = round($sebaran_ips_ah[1] * 100 / $total_ah, 2);
      $persen_ah_c = round($sebaran_ips_ah[2] * 100 / $total_ah, 2);
      $persen_ah_d = round($sebaran_ips_ah[3] * 100 / $total_ah, 2);

      $persen_pka_a = round($sebaran_ips_pka[0] * 100 / $total_pka, 2);
      $persen_pka_b = round($sebaran_ips_pka[1] * 100 / $total_pka, 2);
      $persen_pka_c = round($sebaran_ips_pka[2] * 100 / $total_pka, 2);
      $persen_pka_d = round($sebaran_ips_pka[3] * 100 / $total_pka, 2);
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
            <td class="text-center">MPI</td>
            <td class="text-center"><?= $persen_mpi_a; ?> %</td>
            <td class="text-center"><?= $persen_mpi_b; ?> %</td>
            <td class="text-center"><?= $persen_mpi_c; ?> %</td>
            <td class="text-center"><?= $persen_mpi_d; ?> %</td>
          </tr>
          <tr>
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
          </tr>
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

      <?php
      if ($id_smt > 20192) {
      ?>
        <h5>G. Evaluasi Pembelajaran Oleh Mahasiswa</h5>
        <h6>Dosen Tetap</h6>
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center" style="width: 20px;">No</th>
              <th class="text-center">Keterangan</th>
              <th class="text-center">Jml Kelas</th>
              <th class="text-center">Rata-rata EPOM</th>
            </tr>
          </thead>
          <tbody>
            <?php


            $no = 1;
            foreach ($rerata_epom_dosen_tetap->result() as $key => $value) {
              $epom_dosen_tetap = $this->db->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja' => 'A', 'fk.id_sms' => $value->id_sms])
                ->select('*')->from('siakad_rekap_epom re')
                ->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
                ->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
                ->join('siakad_jadwal sj', 'sj.id = re.id_jadwal')
                ->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
                ->get();



            ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $this->db->where('id_sms', $value->id_sms)->get('sms')->row()->nm_lemb; ?></td>
                <td class="text-center"><?= $epom_dosen_tetap->num_rows(); ?></td>
                <td class="text-center"><?= round($value->nilai_epom, 2) * 1; ?></td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
        <h6>Dosen Tidak Tetap</h6>
        <table class="table table-black">
          <thead>
            <tr>
              <th class="text-center" style="width: 20px;">No</th>
              <th class="text-center">Keterangan</th>
              <th class="text-center">Jml Kelas</th>
              <th class="text-center">Rata-rata EPOM</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            foreach ($rerata_epom_dosen_tidak_tetap->result() as $key => $value) {
              $epom_dosen_tidak_tetap = $this->db->where(['re.id_smt' => $id_smt, 'id_ikatan_kerja <>' => 'A', 'fk.id_sms' => $value->id_sms])
                ->select('*')->from('siakad_rekap_epom re')
                ->join('feeder_kelas_kuliah fk', 'fk.id_kls = re.id_kls')
                ->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
                ->join('siakad_jadwal sj', 'sj.id = re.id_jadwal')
                ->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
                ->get();
            ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $this->db->where('id_sms', $value->id_sms)->get('sms')->row()->nm_lemb; ?></td>
                <td class="text-center"><?= $epom_dosen_tidak_tetap->num_rows(); ?></td>
                <td class="text-center"><?= round($value->nilai_epom, 2) * 1; ?></td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      <?php
      }
      ?>
  </div>
  </section>
  </div>
</body>

</html>