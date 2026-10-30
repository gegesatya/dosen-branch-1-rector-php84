<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="icon-bar-chart font-green-haze"></i>
            <span class="caption-subject bold uppercase font-green-haze"> Chart Perbandingan &amp; Average EPOM</span>
            <span class="caption-helper">perbandingan average EPOM setiap prodi</span>
          </div>
          <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title="">
            </a>
            
          </div>
        </div>
        <div class="portlet-body">
          <div id="graph" class="chart" style="height: 250px; overflow: hidden; text-align: left;">
            <div style="position: relative;">
            </div>
          </div>
        </div>
      </div>
      
    </div>
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Institusi</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata; ?></td>
                <td><?= $data_epom_rerata_tetap; ?> <?= $data_epom_rerata_tetap > $data_epom_rerata_tidak_tetap ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap; ?> <?= $data_epom_rerata_tetap < $data_epom_rerata_tidak_tetap ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max; ?></td>
                <td><?= $data_epom_max_tetap; ?></td>
                <td><?= $data_epom_max_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max Institusi (All)', 'max_institusi', 'wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max Institusi (Dosen Tetap)', 'max_institusi','wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max Institusi (Dosen Tidak Tetap)', 'max_institusi','wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min; ?></td>
                <td><?= $data_epom_min_tetap; ?></td>
                <td><?= $data_epom_min_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min Institusi (All)', 'min_institusi','wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min Institusi (Dosen Tetap)', 'min_institusi','wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min Institusi (Dosen Tidak Tetap)', 'min_institusi','wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper; ?></td>
                <td><?= $data_epom_upper_tetap; ?></td>
                <td><?= $data_epom_upper_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata (All)', 'upper_rerata_institusi', 'wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata (Dosen Tetap)', 'upper_rerata_institusi', 'wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata (Dosen Tidak Tetap)', 'upper_rerata_institusi', 'wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower; ?></td>
                <td><?= $data_epom_lower_tetap; ?></td>
                <td><?= $data_epom_lower_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata (All)', 'lower_rerata_institusi', 'wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata (Tetap)', 'lower_rerata_institusi', 'wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata (Tidak Tetap)', 'lower_rerata_institusi', 'wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25; ?></td>
                <td><?= $data_epom_upper_25_tetap; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 (All)', 'upper_3_institusi', 'wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 (Dosen Tetap)', 'upper_3_institusi', 'wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 (Dosen Tidak Tetap)', 'upper_3_institusi', 'wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25; ?></td>
                <td><?= $data_epom_lower_25_tetap; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 (All)', 'lower_3_institusi', 'wbi',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 (Dosen Tetap)', 'lower_3_institusi', 'wbi',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 (Dosen Tidak Tetap)', 'lower_3_institusi', 'wbi',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah / $data_epom_total * 100, 2) . ' %'; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Prodi MPI</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata_mpi; ?></td>
                <td><?= $data_epom_rerata_tetap_mpi; ?> <?= $data_epom_rerata_tetap_mpi > $data_epom_rerata_tidak_tetap_mpi ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap_mpi; ?> <?= $data_epom_rerata_tetap_mpi < $data_epom_rerata_tidak_tetap_mpi ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max_mpi; ?></td>
                <td><?= $data_epom_max_tetap_mpi; ?></td>
                <td><?= $data_epom_max_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max MPI (All)', 'max_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max MPI (Dosen Tetap)', 'max_institusi','63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max MPI (Dosen Tidak Tetap)', 'max_institusi','63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min_mpi; ?></td>
                <td><?= $data_epom_min_tetap_mpi; ?></td>
                <td><?= $data_epom_min_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min MPI (All)', 'min_institusi','63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min MPI (Dosen Tetap)', 'min_institusi','63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min MPI (Dosen Tidak Tetap)', 'min_institusi','63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper_mpi; ?></td>
                <td><?= $data_epom_upper_tetap_mpi; ?></td>
                <td><?= $data_epom_upper_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata MPI (All)', 'upper_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata MPI (Dosen Tetap)', 'upper_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata MPI (Dosen Tidak Tetap)', 'upper_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower_mpi; ?></td>
                <td><?= $data_epom_lower_tetap_mpi; ?></td>
                <td><?= $data_epom_lower_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata MPI (All)', 'lower_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata MPI (Tetap)', 'lower_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata MPI (Tidak Tetap)', 'lower_rerata_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25_mpi; ?></td>
                <td><?= $data_epom_upper_25_tetap_mpi; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 MPI (All)', 'upper_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 MPI (Dosen Tetap)', 'upper_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 MPI (Dosen Tidak Tetap)', 'upper_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25_mpi; ?></td>
                <td><?= $data_epom_lower_25_tetap_mpi; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap_mpi; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 MPI (All)', 'lower_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 MPI (Dosen Tetap)', 'lower_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 MPI (Dosen Tidak Tetap)', 'lower_3_institusi', '63f083bc-b3a7-4396-99b3-c707575f7823',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah_mpi; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum_mpi; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total_mpi; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah_mpi / $data_epom_total_mpi * 100, 2) . ' %'; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Prodi AP</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata_ap; ?></td>
                <td><?= $data_epom_rerata_tetap_ap; ?> <?= $data_epom_rerata_tetap_ap > $data_epom_rerata_tidak_tetap_ap ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap_ap; ?> <?= $data_epom_rerata_tetap_ap < $data_epom_rerata_tidak_tetap_ap ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max_ap; ?></td>
                <td><?= $data_epom_max_tetap_ap; ?></td>
                <td><?= $data_epom_max_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AP (All)', 'max_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AP (Dosen Tetap)', 'max_institusi','f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AP (Dosen Tidak Tetap)', 'max_institusi','f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min_ap; ?></td>
                <td><?= $data_epom_min_tetap_ap; ?></td>
                <td><?= $data_epom_min_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AP (All)', 'min_institusi','f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AP (Dosen Tetap)', 'min_institusi','f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AP (Dosen Tidak Tetap)', 'min_institusi','f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper_ap; ?></td>
                <td><?= $data_epom_upper_tetap_ap; ?></td>
                <td><?= $data_epom_upper_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AP (All)', 'upper_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AP (Dosen Tetap)', 'upper_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AP (Dosen Tidak Tetap)', 'upper_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower_ap; ?></td>
                <td><?= $data_epom_lower_tetap_ap; ?></td>
                <td><?= $data_epom_lower_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AP (All)', 'lower_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AP (Tetap)', 'lower_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AP (Tidak Tetap)', 'lower_rerata_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25_ap; ?></td>
                <td><?= $data_epom_upper_25_tetap_ap; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AP (All)', 'upper_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AP (Dosen Tetap)', 'upper_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AP (Dosen Tidak Tetap)', 'upper_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25_ap; ?></td>
                <td><?= $data_epom_lower_25_tetap_ap; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap_ap; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AP (All)', 'lower_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AP (Dosen Tetap)', 'lower_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AP (Dosen Tidak Tetap)', 'lower_3_institusi', 'f28c79a3-18c1-4b40-b3af-b2bedc810e20',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah_ap; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum_ap; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total_ap; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah_ap / $data_epom_total_ap * 100, 2) . ' %'; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Prodi AH</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata_ah; ?></td>
                <td><?= $data_epom_rerata_tetap_ah; ?> <?= $data_epom_rerata_tetap_ah > $data_epom_rerata_tidak_tetap_ah ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap_ah; ?> <?= $data_epom_rerata_tetap_ah < $data_epom_rerata_tidak_tetap_ah ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max_ah; ?></td>
                <td><?= $data_epom_max_tetap_ah; ?></td>
                <td><?= $data_epom_max_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AH (All)', 'max_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AH (Dosen Tetap)', 'max_institusi','01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max AH (Dosen Tidak Tetap)', 'max_institusi','01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min_ah; ?></td>
                <td><?= $data_epom_min_tetap_ah; ?></td>
                <td><?= $data_epom_min_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AH (All)', 'min_institusi','01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AH (Dosen Tetap)', 'min_institusi','01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min AH (Dosen Tidak Tetap)', 'min_institusi','01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper_ah; ?></td>
                <td><?= $data_epom_upper_tetap_ah; ?></td>
                <td><?= $data_epom_upper_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AH (All)', 'upper_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AH (Dosen Tetap)', 'upper_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata AH (Dosen Tidak Tetap)', 'upper_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower_ah; ?></td>
                <td><?= $data_epom_lower_tetap_ah; ?></td>
                <td><?= $data_epom_lower_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AH (All)', 'lower_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AH (Tetap)', 'lower_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata AH (Tidak Tetap)', 'lower_rerata_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25_ah; ?></td>
                <td><?= $data_epom_upper_25_tetap_ah; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AH (All)', 'upper_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AH (Dosen Tetap)', 'upper_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 AH (Dosen Tidak Tetap)', 'upper_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25_ah; ?></td>
                <td><?= $data_epom_lower_25_tetap_ah; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap_ah; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AH (All)', 'lower_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AH (Dosen Tetap)', 'lower_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 AH (Dosen Tidak Tetap)', 'lower_3_institusi', '01f25707-0271-4fc0-b957-eb0d9d0ce0c4',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah_ah; ?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum_ah; ?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total_ah; ?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah_ah / $data_epom_total_ah * 100, 2) . ' %'; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Prodi PKA</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata_pka; ?></td>
                <td><?= $data_epom_rerata_tetap_pka; ?> <?= $data_epom_rerata_tetap_pka > $data_epom_rerata_tidak_tetap_pka ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap_pka; ?> <?= $data_epom_rerata_tetap_pka < $data_epom_rerata_tidak_tetap_pka ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max_pka; ?></td>
                <td><?= $data_epom_max_tetap_pka; ?></td>
                <td><?= $data_epom_max_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max PKA (All)', 'max_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max PKA (Dosen Tetap)', 'max_institusi','df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max PKA (Dosen Tidak Tetap)', 'max_institusi','df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min_pka; ?></td>
                <td><?= $data_epom_min_tetap_pka; ?></td>
                <td><?= $data_epom_min_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min PKA (All)', 'min_institusi','df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min PKA (Dosen Tetap)', 'min_institusi','df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min PKA (Dosen Tidak Tetap)', 'min_institusi','df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper_pka; ?></td>
                <td><?= $data_epom_upper_tetap_pka; ?></td>
                <td><?= $data_epom_upper_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata PKA (All)', 'upper_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata PKA (Dosen Tetap)', 'upper_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata PKA (Dosen Tidak Tetap)', 'upper_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower_pka; ?></td>
                <td><?= $data_epom_lower_tetap_pka; ?></td>
                <td><?= $data_epom_lower_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata PKA (All)', 'lower_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata PKA (Tetap)', 'lower_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata PKA (Tidak Tetap)', 'lower_rerata_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25_pka; ?></td>
                <td><?= $data_epom_upper_25_tetap_pka; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 PKA (All)', 'upper_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 PKA (Dosen Tetap)', 'upper_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 PKA (Dosen Tidak Tetap)', 'upper_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25_pka; ?></td>
                <td><?= $data_epom_lower_25_tetap_pka; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap_pka; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 PKA (All)', 'lower_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 PKA (Dosen Tetap)', 'lower_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 PKA (Dosen Tidak Tetap)', 'lower_3_institusi', 'df50c13b-fd44-4583-b1a4-f22af69b23bf',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah_pka; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum_pka; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total_pka; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah_pka / $data_epom_total_pka * 100, 2) . ' %'; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">EPOM <small>Prodi TRPL</small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th>Uraian</th>
                <th>All</th>
                <th>Tetap</th>
                <th>Tdk Tetap</th>
                <th>Insight</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>Rata-rata Nilai EPOM</td>
                <td><?= $data_epom_rerata_trpl; ?></td>
                <td><?= $data_epom_rerata_tetap_trpl; ?> <?= $data_epom_rerata_tetap_trpl > $data_epom_rerata_tidak_tetap_trpl ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td><?= $data_epom_rerata_tidak_tetap_trpl; ?> <?= $data_epom_rerata_tetap_trpl < $data_epom_rerata_tidak_tetap_trpl ? "<i class='icon-arrow-up '></i>" : "<i class='icon-arrow-down '></i>"; ?></td>
                <td></td>
              </tr>
              <tr>
                <td>Nilai EPOM max</td>
                <td><?= $data_epom_max_trpl; ?></td>
                <td><?= $data_epom_max_tetap_trpl; ?></td>
                <td><?= $data_epom_max_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM max TRPL (All)', 'max_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM max TRPL (Dosen Tetap)', 'max_institusi','eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM max TRPL (Dosen Tidak Tetap)', 'max_institusi','eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Nilai EPOM min</td>
                <td><?= $data_epom_min_trpl; ?></td>
                <td><?= $data_epom_min_tetap_trpl; ?></td>
                <td><?= $data_epom_min_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM min TRPL (All)', 'min_institusi','eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM min TRPL (Dosen Tetap)', 'min_institusi','eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM min TRPL (Dosen Tidak Tetap)', 'min_institusi','eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= rata-rata</td>
                <td><?= $data_epom_upper_trpl; ?></td>
                <td><?= $data_epom_upper_tetap_trpl; ?></td>
                <td><?= $data_epom_upper_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata TRPL (All)', 'upper_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata TRPL (Dosen Tetap)', 'upper_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= rata-rata TRPL (Dosen Tidak Tetap)', 'upper_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < rata-rata</td>
                <td><?= $data_epom_lower_trpl; ?></td>
                <td><?= $data_epom_lower_tetap_trpl; ?></td>
                <td><?= $data_epom_lower_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata TRPL (All)', 'lower_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata TRPL (Tetap)', 'lower_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < rata-rata TRPL (Tidak Tetap)', 'lower_rerata_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM >= 3,0</td>
                <td><?= $data_epom_upper_25_trpl; ?></td>
                <td><?= $data_epom_upper_25_tetap_trpl; ?></td>
                <td><?= $data_epom_upper_25_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 TRPL (All)', 'upper_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 TRPL (Dosen Tetap)', 'upper_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM >= 3.0 TRPL (Dosen Tidak Tetap)', 'upper_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Jml EPOM < 3,0</td>
                <td><?= $data_epom_lower_25_trpl; ?></td>
                <td><?= $data_epom_lower_25_tetap_trpl; ?></td>
                <td><?= $data_epom_lower_25_tidak_tetap_trpl; ?></td>
                <td>
                  <a class="btn btn-circle btn-xs blue" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 TRPL (All)', 'lower_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',0)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs green" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 TRPL (Dosen Tetap)', 'lower_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',1)"><i class="fa fa-edit"></i></a>
                  <a class="btn btn-circle btn-xs red" href="javascript:void()" onclick="show_epom('Data nilai EPOM < 3.0 TRPL (Dosen Tidak Tetap)', 'lower_3_institusi', 'eea49383-ebe8-476b-85fc-b25d80f0cf32',2)"><i class="fa fa-edit"></i></a>
                </td>
              </tr>
              <tr>
                <td>Data Mahasiswa Sudah EPOM</td>
                <td><?= $data_epom_sudah_trpl; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data Mahasiswa Belum EPOM</td>
                <td><?= $data_epom_belum_trpl; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Data EPOM Total</td>
                <td><?= $data_epom_total_trpl; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Partisipasi Mahasiswa</td>
                <td><?= round($data_epom_sudah_trpl / $data_epom_total_trpl * 100, 2) . ' %'; ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>