<?php
$nama_tabel = '';
if ($list_detail_kelas->is_final == 'N') {
  $nama_tabel = 'tabel_nilai';
}

if ($list_detail_kelas->status_isi_komponen_nilai == 1) {
?>
  <section class="content">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-files-o"></i> Kelas Kuliah [<?= $this->session->userdata('bc_nm_kls'); ?>] <?= $this->session->userdata('title_nm_mk'); ?></h3>
      </div>
      <div class="box-body" style="min-height: 600px">
        <div class="col-md-12 col-sm-6" style="margin-bottom: 20px">
          <a href="#" onclick="show_passing_grade()" class="btn btn-primary bg-navy btn-flat">Tabel Nilai Mutu dan Indeks WBI</a>
        </div>
        <div class="col-md-12 col-sm-6">
          <div class="table-responsive">
            <table class="table table-bordered table-center-th" id="<?= $nama_tabel; ?>">
              <thead>
                <tr>
                  <?php
                  if ($list_detail_kelas->is_final == 'N') {
                    echo "<th></th>";
                  }
                  $ukn = !empty($bobot_keterampilan_unjuk_kinerja) ? '(' . $bobot_keterampilan_unjuk_kinerja . '%)' : '';
                  $pry = !empty($bobot_keterampilan_proyek) ? '(' . $bobot_keterampilan_proyek . '%)' : '';
                  $por = !empty($bobot_keterampilan_portofolio) ? '(' . $bobot_keterampilan_portofolio . '%)' : '';
                  $prd = !empty($bobot_keterampilan_produk) ? '(' . $bobot_keterampilan_produk . '%)' : '';
                  $pre = !empty($bobot_keterampilan_presentasi) ? '(' . $bobot_keterampilan_presentasi . '%)' : '';

                  $colspan_keterampilan = 5;
                  $style_ukn = "";
                  $style_pry = "";
                  $style_por = "";
                  $style_prd = "";
                  $style_pre = "";

                  if ($ukn == '') {
                    $colspan_keterampilan -= 1;
                    $style_ukn = "style = 'display:none'";
                  }

                  if ($pry == '') {
                    $colspan_keterampilan -= 1;
                    $style_pry = "style = 'display:none'";
                  }

                  if ($por == '') {
                    $colspan_keterampilan -= 1;
                    $style_por = "style = 'display:none'";
                  }

                  if ($prd == '') {
                    $colspan_keterampilan -= 1;
                    $style_prd = "style = 'display:none'";
                  }

                  if ($pre == '') {
                    $colspan_keterampilan -= 1;
                    $style_pre = "style = 'display:none'";
                  }

                  ?>
                  <th rowspan="2" style="width: 30px">No.</th>
                  <th rowspan="2" style="width: 300px;min-width: 250px">NIM / Nama</th>
                  <th rowspan="2" style="width: 60px">% Hadir </th>
                  <th rowspan="2" style="width: 60px">Sikap <br> <?= !empty($bobot_sikap) ? '(' . $bobot_sikap . '%)' : ''; ?></th>
                  <th colspan="4">SUB Pengetahuan</th>
                  <th rowspan="2">Pengetahuan <br><?= !empty($bobot_pengetahuan) ? '(' . $bobot_pengetahuan . '%)' : ''; ?></th>
                  <th colspan="<?= $colspan_keterampilan; ?>">SUB Keterampilan</th>
                  <th rowspan="2">Keterampilan <br><?= !empty($bobot_keterampilan) ? '(' . $bobot_keterampilan . '%)' : ''; ?></th>
                  <th rowspan="2">Angka<br>(FINAL)</th>
                  <th rowspan="2">Huruf</th>
                  <th rowspan="2">Indeks</th>
                </tr>
                <tr>
                  <?php
                  if ($list_detail_kelas->is_final == 'N') {
                    echo "<th></th>";
                  }
                  ?>
                  <th>QUI <br><?= !empty($bobot_pengetahuan_quiz) ? '(' . $bobot_pengetahuan_quiz . '%)' : ''; ?></th>
                  <th>TTS <br><?= !empty($bobot_pengetahuan_tugas_terstruktur) ? '(' . $bobot_pengetahuan_tugas_terstruktur . '%)' : ''; ?></th>
                  <th>UTS <br><?= !empty($bobot_pengetahuan_uts) ? '(' . $bobot_pengetahuan_uts . '%)' : ''; ?></th>
                  <th>UAS <br><?= !empty($bobot_pengetahuan_uas) ? '(' . $bobot_pengetahuan_uas . '%)' : ''; ?></th>

                  <th <?= $style_ukn; ?>>UKN <br><?= $ukn ?></th>
                  <th <?= $style_pry; ?>>PRY <br><?= $pry ?></th>
                  <th <?= $style_por; ?>>POR <br><?= $por ?></th>
                  <th <?= $style_prd; ?>>PRD <br><?= $prd ?></th>
                  <th <?= $style_pre; ?>>PRE <br><?= $pre ?></th>
                </tr>

              </thead>
              <tbody>
                <?php
                $i = 1;
                foreach ($nilai as $key => $value) {
                                    

                  
                ?>
                  <tr>
                    <?php
                    if ($list_detail_kelas->is_final == 'N') {
                      echo "<td>" . $value->id_reg_pd . "|" . $value->id_kls . "</td>";
                    }
                    ?>
                    <td class="text-right"><?= $i; ?>.</td>
                    <td><?= $value->nipd; ?>
                      <hr style="margin:0px"><?= $value->nm_pd; ?>
                    </td>
                    <td><?= $value->persen_kehadiran; ?></td>
                    <td><?= $value->n_sikap_2; ?></td>
                    <td><?= $value->n_pengetahuan_2_quiz; ?></td>
                    <td><?= $value->n_pengetahuan_2_tugas_terstruktur; ?></td>
                    <td><?= $value->n_pengetahuan_2_uts; ?></td>
                    <td><?= $value->n_pengetahuan_2_uas; ?></td>
                    <!-- <td class="success"><?= $value->n_pengetahuan_2; ?></td> -->
                    <td <?= $style_ukn; ?>><?= $value->n_keterampilan_2_unjuk_kinerja; ?></td>
                    <td <?= $style_pry; ?>><?= $value->n_keterampilan_2_proyek; ?></td>
                    <td <?= $style_por; ?>><?= $value->n_keterampilan_2_portofolio; ?></td>
                    <td <?= $style_prd; ?>><?= $value->n_keterampilan_2_produk; ?></td>
                    <td <?= $style_pre; ?>><?= $value->n_keterampilan_2_presentasi; ?></td>
                    <!-- <td class="success"><?= $value->n_keterampilan_2; ?></td> -->
                    <td class="text-center"><span style="font-weight: bold;"><?= $value->nilai_angka; ?></span></td>
                    <td class="text-center"><span style="font-weight: bold;"><?= $value->nilai_huruf; ?></span></td>
                    <td class="text-center"><span style="font-weight: bold;"><?= $value->nilai_indeks; ?></span></td>

                  </tr>
                <?php
                  
                  $i++;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
} else {
?>
  <section class="content">
    <div class="col-md-12">
      Skema Komposisi Nilai belum diisi. Silahkan mengisi skema komposisi nilai terlebih dahulu.
    </div>
  </section>
<?php
}
?>

