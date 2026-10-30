<div class="page-content" style="padding-top:0">
    <div class="row">
        <div class="col-md-12">
            
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-graduation font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
                    </div>
                </div>
            </div>

            
            <div class="portlet light bordered">
                <div class="portlet-body form">
                    <form action="<?= site_url('C_kinerja'); ?>" method="get" class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Tahun Akademik</label>
                                <div class="col-md-4">
                                    <select name="year" class="form-control select2" onchange="this.form.submit()">
                                        <?php foreach ($list_years as $y) : ?>
                                            <option value="<?= $y->year; ?>" <?= ($selected_year == $y->year) ? 'selected' : ''; ?>>
                                                <?= $y->label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-table"></i> Rekapitulasi Kinerja Tahunan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr class="active">
                                <th rowspan="2" width="180" class="text-center" style="vertical-align:middle"> Indikator Kinerja </th>
                                <th rowspan="2" width="300" class="text-center" style="vertical-align:middle"> Kewajiban </th>
                                <th colspan="2" class="text-center"> Semester Ganjil </th>
                                <th colspan="2" class="text-center"> Semester Genap </th>
                                <th rowspan="2" width="100" class="text-center" style="vertical-align:middle"> Total </th>
                            </tr>
                            <tr class="active">
                                <th class="text-center" width="250"> SKS </th>
                                <th class="text-center" width="80"> Sub Total </th>
                                <th class="text-center" width="250"> SKS </th>
                                <th class="text-center" width="80"> Sub Total </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $idx = 0; 
                            $grand_total_ganjil_poin = 0;
                            $grand_total_genap_poin = 0;
                            $grand_total_kinerja = 0; 
                            $skripsi_items = [];
                            $main_kinerja = [];

                                                        foreach ($data_kinerja as $row) {
                                if (in_array($row->ganjil->id_kinerja, [12, 13, 14])) {
                                    $skripsi_items[] = $row;
                                } else {
                                    $main_kinerja[] = $row;
                                }
                            }

                            foreach ($main_kinerja as $row) : 
                                $idx++;
                                
                                                                if ($idx <= 5) {
                                    $grand_total_ganjil_poin += $row->ganjil->calculated_poin;
                                    $grand_total_genap_poin += ($row->genap->calculated_poin ?? 0);
                                } else {
                                    $grand_total_kinerja += $row->total_poin_yearly;
                                }
                            ?>
                                <tr>
                                    <?php if ($idx == 1) : ?>
                                        <td rowspan="<?= count($main_kinerja) + 1 + 1 + 2 + count($skripsi_items) + 2; ?>" class="bold" style="vertical-align: middle; background-color: #ffffff;"> 
                                            1. Pendidikan dan Pengajaran 
                                        </td>
                                    <?php endif; ?>

                                    <td style="background-color: #e3f2fd;"> 
                                        <?= $row->display_label; ?>
                                    </td>
                                    
                                    
                                    <td class="text-center" style="background-color: #fff9c4; font-size: 0.85em; vertical-align: middle;"> 
                                        <?php if (!empty($row->ganjil->schedules)) : ?>
                                            <div class="text-left">
                                                <?php foreach ($row->ganjil->schedules as $s) : ?>
                                                    <?= $s->tag_name; ?> (<?= $s->sks_mk; ?> SKS) = <?= number_format($s->sks_mk / $s->divider, 2); ?>
                                                    <br>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif (isset($row->ganjil->student_count)) : ?>
                                            <span class="bold"><?= $row->ganjil->student_count; ?></span>
                                        <?php elseif (!empty($row->ganjil->structural_info)) : ?>
                                            <span class="bold"><?= $row->ganjil->structural_credit ?? 0; ?></span>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center" style="background-color: #fffde7; vertical-align: middle;"> 
                                        <?= $row->ganjil->calculated_poin > 0 ? number_format($row->ganjil->calculated_poin, 2) : '0'; ?> 
                                    </td>

                                    
                                    <td class="text-center" style="background-color: #e3f2fd; font-size: 0.85em; vertical-align: middle;"> 
                                        <?php if (!empty($row->genap->schedules)) : ?>
                                            <div class="text-left">
                                                <?php foreach ($row->genap->schedules as $s) : ?>
                                                    <?= $s->tag_name; ?> (<?= $s->sks_mk; ?> SKS) = <?= number_format($s->sks_mk / $s->divider, 2); ?>
                                                    <br>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif (isset($row->genap->student_count)) : ?>
                                            <span class="bold"><?= $row->genap->student_count; ?></span>
                                        <?php elseif (!empty($row->genap->structural_info)) : ?>
                                            <span class="bold"><?= $row->genap->structural_credit ?? 0; ?></span>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="text-center" style="background-color: #f1f8fe; vertical-align: middle;"> 
                                        <?= ($row->genap->calculated_poin ?? 0) > 0 ? number_format($row->genap->calculated_poin, 2) : '0'; ?> 
                                    </td>

                                    <td class="text-center bold" style="background-color: #f5f5f5; vertical-align: middle;"> 
                                        <?php if ($idx > 5) : ?>
                                            <?= $row->total_poin_yearly > 0 ? number_format($row->total_poin_yearly, 2) : '0'; ?> 
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php if ($idx == 5) : ?>
                                    <?php 
                                        $teaching_total_ganjil = $grand_total_ganjil_poin;
                                        $teaching_total_genap = $grand_total_genap_poin;
                                        $teaching_yearly_total = ($teaching_total_ganjil + $teaching_total_genap) * 16;
                                        $grand_total_kinerja += $teaching_yearly_total;
                                    ?>
                                    <tr class="bold" style="background-color: #ffe082;">
                                        <td class="text-center"> TOTAL PENGAJARAN </td>
                                        <td class="text-center"> </td>
                                        <td class="text-center"> <?= number_format($teaching_total_ganjil, 2); ?> </td>
                                        <td class="text-center"> </td>
                                        <td class="text-center"> <?= number_format($teaching_total_genap, 2); ?> </td>
                                        <td class="text-center"> <?= number_format($teaching_yearly_total, 2); ?> </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <tr class="active bold">
                                <td colspan="5" class="text-right"> GRAND TOTAL </td>
                                <td class="text-center bg-blue-hoki font-white"> <?= number_format($grand_total_kinerja, 2); ?> </td>
                            </tr>
                            
                            
                            <tr class="active">
                                <th colspan="6" class="text-center"> Perhitungan Kinerja Tahunan (Lulusan) </th>
                            </tr>
                            <tr class="active">
                                <th class="text-center"> Kewajiban </th>
                                <th colspan="2" class="text-center"> Semester Ganjil </th>
                                <th colspan="2" class="text-center"> Semester Genap </th>
                                <th class="text-center"> Total </th>
                            </tr>
                            <?php 
                                $skripsi_ganjil_sum = 0;
                                $skripsi_genap_sum = 0;
                                $skripsi_yearly_sum = 0;
                                foreach ($skripsi_items as $si) : 
                                $grand_total_kinerja += $si->total_poin_yearly;
                                $skripsi_ganjil_sum += $si->ganjil->calculated_poin;
                                $skripsi_genap_sum += ($si->genap->calculated_poin ?? 0);
                                $skripsi_yearly_sum += $si->total_poin_yearly;
                            ?>
                                <tr>
                                    <td style="background-color: #e3f2fd;"> 
                                        <?= $si->display_label; ?> 
                                    </td>
                                    <td class="text-center" style="background-color: #fff9c4;"> 
                                        <span class="bold"><?= $si->ganjil->student_count ?? 0; ?></span>
                                    </td>
                                    <td class="text-center" style="background-color: #fffde7;"> <?= number_format($si->ganjil->calculated_poin, 2); ?> </td>
                                    <td class="text-center" style="background-color: #e3f2fd;"> 
                                        <span class="bold"><?= $si->genap->student_count ?? 0; ?></span>
                                    </td>
                                    <td class="text-center" style="background-color: #f1f8fe;"> <?= number_format($si->genap->calculated_poin ?? 0, 2); ?> </td>
                                    <td class="text-center bold" style="background-color: #f5f5f5;"> <?= number_format($si->total_poin_yearly, 2); ?> </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr class="bold" style="background-color: #ffe082;">
                                <td class="text-center"> Pembimbingan Tugas Akhir mahasiswa 12 orang lulusan max per tahun </td>
                                <td class="text-center"> </td>
                                <td class="text-center"> <?= number_format($skripsi_ganjil_sum, 2); ?> </td>
                                <td class="text-center"> </td>
                                <td class="text-center"> <?= number_format($skripsi_genap_sum, 2); ?> </td>
                                <td class="text-center"> <?= number_format($skripsi_yearly_sum, 2); ?> </td>
                            </tr>
                            
                            
                            <tr class="active bold">
                                <td colspan="5" class="text-right"> 
                                    <button type="button" class="btn btn-sm btn-success" style="margin-right: 15px;" data-toggle="modal" data-target="#modalMahasiswaBimbingan">
                                        <i class="fa fa-users"></i> Lihat Mahasiswa
                                    </button>
                                    GRAND TOTAL AKHIR 
                                </td>
                                <td class="text-center bg-blue-hoki font-white"> <?= number_format($grand_total_kinerja, 2); ?> </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalMahasiswaBimbingan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue-hoki bg-font-blue-hoki">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title bold">Daftar Mahasiswa Bimbingan Tugas Akhir</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="120" class="text-center">NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th width="120" class="text-center">Semester</th>
                                <th width="180" class="text-center">Periode Bimbingan</th>
                                <th width="130" class="text-center">Masa Penyelesaian</th>
                                <th width="120" class="text-center">Nilai Sidang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $has_students = false;
                            foreach ($skripsi_items as $si) : 
                                $ganjil_students = isset($si->ganjil->student_list) ? $si->ganjil->student_list : [];
                                $genap_students = isset($si->genap->student_list) ? $si->genap->student_list : [];
                                
                                foreach ($ganjil_students as $std) {
                                    $std->smt_label = 'Ganjil ' . $selected_year;
                                }
                                foreach ($genap_students as $std) {
                                    $std->smt_label = 'Genap ' . $selected_year;
                                }
                                
                                $all_students = [];
                                $nipds = [];
                                foreach (array_merge($ganjil_students, $genap_students) as $std) {
                                    if (!in_array($std->nipd, $nipds)) {
                                        $all_students[] = $std;
                                        $nipds[] = $std->nipd;
                                    } else {
                                        $idx = array_search($std->nipd, $nipds);
                                        if ($idx !== false && strpos($all_students[$idx]->smt_label, $std->smt_label) === false) {
                                            $all_students[$idx]->smt_label .= ', ' . $std->smt_label;
                                        }
                                    }
                                }
                                
                                                                usort($all_students, function($a, $b) {
                                    $date_a = '';
                                    if (!empty($a->sidang_tanggal) && $a->sidang_tanggal != '0000-00-00') {
                                        $date_a = $a->sidang_tanggal;
                                    } elseif (!empty($a->tanggal_keluar) && $a->tanggal_keluar != '0000-00-00') {
                                        $date_a = $a->tanggal_keluar;
                                    }
                                    
                                    $date_b = '';
                                    if (!empty($b->sidang_tanggal) && $b->sidang_tanggal != '0000-00-00') {
                                        $date_b = $b->sidang_tanggal;
                                    } elseif (!empty($b->tanggal_keluar) && $b->tanggal_keluar != '0000-00-00') {
                                        $date_b = $b->tanggal_keluar;
                                    }
                                    
                                    if ($date_a == $date_b) return 0;
                                    return ($date_a < $date_b) ? -1 : 1;
                                });
                                
                                if (count($all_students) > 0) :
                                    $has_students = true;
                            ?>
                                <tr class="info bold">
                                    <td colspan="7"><?= $si->display_label; ?></td>
                                </tr>
                                <?php $no = 1; foreach ($all_students as $mhs) : 
                                    $tgl_mulai = (!empty($mhs->tanggal_usulan_kaprodi) && $mhs->tanggal_usulan_kaprodi != '0000-00-00') ? date('d-m-Y', strtotime($mhs->tanggal_usulan_kaprodi)) : '-';
                                    
                                    $raw_tgl_lulus = null;
                                    if (!empty($mhs->sidang_tanggal) && $mhs->sidang_tanggal != '0000-00-00') {
                                        $raw_tgl_lulus = $mhs->sidang_tanggal;
                                    } elseif (!empty($mhs->tanggal_keluar) && $mhs->tanggal_keluar != '0000-00-00') {
                                        $raw_tgl_lulus = $mhs->tanggal_keluar;
                                    }
                                    
                                    $tgl_lulus = ($raw_tgl_lulus) ? date('d-m-Y', strtotime($raw_tgl_lulus)) : '-';
                                    
                                    $masa_penyelesaian = '-';
                                    if ($tgl_mulai !== '-' && $raw_tgl_lulus) {
                                        $d_mulai = new DateTime($mhs->tanggal_usulan_kaprodi);
                                        $d_lulus = new DateTime($raw_tgl_lulus);
                                        $interval = $d_mulai->diff($d_lulus);
                                        
                                        $parts = [];
                                        if ($interval->y > 0) $parts[] = $interval->y . ' thn';
                                        if ($interval->m > 0) $parts[] = $interval->m . ' bln';
                                        if ($interval->d > 0) $parts[] = $interval->d . ' hr';
                                        
                                        $masa_penyelesaian = implode(' ', $parts);
                                        if (empty($masa_penyelesaian)) $masa_penyelesaian = '0 hr';
                                    }
                                    
                                    $nilai_huruf = !empty($mhs->sidang_nilai_mutu) ? $mhs->sidang_nilai_mutu : '-';
                                    $nilai_angka = (!empty($mhs->sidang_nilai_ratarata) && $mhs->sidang_nilai_ratarata > 0) ? rtrim(rtrim(number_format($mhs->sidang_nilai_ratarata, 2), '0'), '.') : 
                                                   ((!empty($mhs->sidang_nilai_akhir) && $mhs->sidang_nilai_akhir > 0) ? rtrim(rtrim(number_format($mhs->sidang_nilai_akhir, 2), '0'), '.') : '-');
                                    
                                    $nilai_display = $nilai_angka;
                                    if ($nilai_huruf !== '-' && $nilai_angka !== '-') {
                                        $nilai_display = $nilai_angka . ' (' . $nilai_huruf . ')';
                                    } elseif ($nilai_huruf !== '-') {
                                        $nilai_display = $nilai_huruf;
                                    }
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center"><?= htmlspecialchars($mhs->nipd); ?></td>
                                    <td><?= htmlspecialchars($mhs->nm_pd); ?></td>
                                    <td class="text-center"><?= $mhs->smt_label; ?></td>
                                    <td class="text-center"><?= $tgl_mulai; ?> sd <?= $tgl_lulus; ?></td>
                                    <td class="text-center"><span class="badge badge-success"><?= $masa_penyelesaian; ?></span></td>
                                    <td class="text-center"><?= $nilai_display; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php 
                                endif; 
                            endforeach; 
                            
                            if (!$has_students):
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data mahasiswa bimbingan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
