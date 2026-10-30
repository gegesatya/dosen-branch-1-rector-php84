<div class="portlet box blue-hoki" style="margin-bottom:0; border: 1px solid #B0C4DE; border-radius: 8px; overflow: hidden;">
    <div class="portlet-title" style="background: #4B77BE; color: white; padding: 10px 15px; display: flex; align-items: center; justify-content: space-between;">
        <div class="caption" style="font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            <i class="fa fa-table"></i> Rekapitulasi Kinerja Tahunan (TA <?= $selected_year; ?>/<?= $selected_year + 1; ?>)
        </div>
    </div>
    <div class="portlet-body" style="padding: 15px; background: #fff;">
        <div class="table-responsive">
            <table class="table table-bordered table-hover bkd-detail-table" style="font-size: 12.5px; margin-bottom: 0;">
                <thead>
                    <tr class="active" style="background: #F0F4F8; font-weight: 800; color: #2D3748;">
                        <th rowspan="2" width="180" class="text-center" style="vertical-align:middle; border: 1px solid #CBD5E0;"> Indikator Kinerja </th>
                        <th rowspan="2" width="300" class="text-center" style="vertical-align:middle; border: 1px solid #CBD5E0;"> Kewajiban </th>
                        <th colspan="2" class="text-center" style="border: 1px solid #CBD5E0;"> Semester Ganjil </th>
                        <th colspan="2" class="text-center" style="border: 1px solid #CBD5E0;"> Semester Genap </th>
                        <th rowspan="2" width="100" class="text-center" style="vertical-align:middle; border: 1px solid #CBD5E0;"> Total </th>
                    </tr>
                    <tr class="active" style="background: #F0F4F8; font-weight: 800; color: #2D3748;">
                        <th class="text-center" width="250" style="border: 1px solid #CBD5E0;"> SKS </th>
                        <th class="text-center" width="80" style="border: 1px solid #CBD5E0;"> Sub Total </th>
                        <th class="text-center" width="250" style="border: 1px solid #CBD5E0;"> SKS </th>
                        <th class="text-center" width="80" style="border: 1px solid #CBD5E0;"> Sub Total </th>
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
                        if (in_array($row->ganjil->id_kinerja ?? 0, [12, 13, 14])) {
                            $skripsi_items[] = $row;
                        } else {
                            $main_kinerja[] = $row;
                        }
                    }

                    foreach ($main_kinerja as $row) : 
                        $idx++;
                        
                        if ($idx <= 5) {
                            $grand_total_ganjil_poin += $row->ganjil->calculated_poin ?? 0;
                            $grand_total_genap_poin += ($row->genap->calculated_poin ?? 0);
                        } else {
                            $grand_total_kinerja += $row->total_poin_yearly ?? 0;
                        }
                    ?>
                        <tr>
                            <?php if ($idx == 1) : ?>
                                <td rowspan="<?= count($main_kinerja) + 1 + 1 + 2 + count($skripsi_items) + 2; ?>" class="bold" style="vertical-align: middle; background-color: #ffffff; font-weight: 800; color: #1A202C; border: 1px solid #CBD5E0; text-align: center; width: 180px;"> 
                                    1. Pendidikan dan Pengajaran 
                                </td>
                            <?php endif; ?>

                            <td style="background-color: #e3f2fd; color: #1A365D; font-weight: 600; border: 1px solid #CBD5E0;"> 
                                <?= $row->display_label; ?>
                            </td>
                            
                            <!-- Semester Ganjil SKS -->
                            <td class="text-center" style="background-color: #fff9c4; font-size: 0.9em; vertical-align: middle; border: 1px solid #CBD5E0;"> 
                                <?php if (!empty($row->ganjil->schedules)) : ?>
                                    <div class="text-left" style="font-size: 11px; line-height: 1.4;">
                                        <?php foreach ($row->ganjil->schedules as $s) : ?>
                                            <?= $s->tag_name; ?> (<?= $s->sks_mk; ?> SKS) = <?= number_format($s->sks_mk / $s->divider, 2); ?>
                                            <br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif (isset($row->ganjil->student_count) && $row->ganjil->student_count > 0) : ?>
                                    <span style="font-weight: 700;"><?= $row->ganjil->student_count; ?></span>
                                <?php elseif (!empty($row->ganjil->structural_info)) : ?>
                                    <span style="font-weight: 700;"><?= $row->ganjil->structural_credit ?? 0; ?></span>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <!-- Semester Ganjil Sub Total -->
                            <td class="text-center" style="background-color: #fffde7; vertical-align: middle; font-weight: 700; border: 1px solid #CBD5E0;"> 
                                <?= ($row->ganjil->calculated_poin ?? 0) > 0 ? number_format($row->ganjil->calculated_poin, 2) : '0'; ?> 
                            </td>

                            <!-- Semester Genap SKS -->
                            <td class="text-center" style="background-color: #e3f2fd; font-size: 0.9em; vertical-align: middle; border: 1px solid #CBD5E0;"> 
                                <?php if (!empty($row->genap->schedules)) : ?>
                                    <div class="text-left" style="font-size: 11px; line-height: 1.4;">
                                        <?php foreach ($row->genap->schedules as $s) : ?>
                                            <?= $s->tag_name; ?> (<?= $s->sks_mk; ?> SKS) = <?= number_format($s->sks_mk / $s->divider, 2); ?>
                                            <br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif (isset($row->genap->student_count) && $row->genap->student_count > 0) : ?>
                                    <span style="font-weight: 700;"><?= $row->genap->student_count; ?></span>
                                <?php elseif (!empty($row->genap->structural_info)) : ?>
                                    <span style="font-weight: 700;"><?= $row->genap->structural_credit ?? 0; ?></span>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <!-- Semester Genap Sub Total -->
                            <td class="text-center" style="background-color: #f1f8fe; vertical-align: middle; font-weight: 700; border: 1px solid #CBD5E0;"> 
                                <?= ($row->genap->calculated_poin ?? 0) > 0 ? number_format($row->genap->calculated_poin, 2) : '0'; ?> 
                            </td>

                            <!-- Total -->
                            <td class="text-center" style="background-color: #f5f5f5; vertical-align: middle; font-weight: 700; border: 1px solid #CBD5E0;"> 
                                <?php if ($idx > 5) : ?>
                                    <?= ($row->total_poin_yearly ?? 0) > 0 ? number_format($row->total_poin_yearly, 2) : '0'; ?> 
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
                            <tr style="background-color: #ffe082; font-weight: 800; color: #2D3748;">
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> TOTAL PENGAJARAN </td>
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> </td>
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($teaching_total_ganjil, 2); ?> </td>
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> </td>
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($teaching_total_genap, 2); ?> </td>
                                <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($teaching_yearly_total, 2); ?> </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <tr style="background: #EDF2F7; font-weight: 800;">
                        <td colspan="5" class="text-right" style="padding-right: 15px; border: 1px solid #CBD5E0; color: #2D3748;"> GRAND TOTAL </td>
                        <td class="text-center font-white" style="background-color: #3182CE; color: #fff; font-size: 13.5px; border: 1px solid #CBD5E0;"> <?= number_format($grand_total_kinerja, 2); ?> </td>
                    </tr>
                    
                    <!-- PERHITUNGAN KINERJA TAHUNAN (LULUSAN) -->
                    <tr class="active" style="background: #2B6CB0; color: #fff; font-weight: 800;">
                        <th colspan="6" class="text-center" style="border: 1px solid #CBD5E0; padding: 8px;"> Perhitungan Kinerja Tahunan (Lulusan) </th>
                    </tr>
                    <tr class="active" style="background: #F0F4F8; font-weight: 800; color: #2D3748;">
                        <th class="text-center" style="border: 1px solid #CBD5E0;"> Kewajiban </th>
                        <th colspan="2" class="text-center" style="border: 1px solid #CBD5E0;"> Semester Ganjil </th>
                        <th colspan="2" class="text-center" style="border: 1px solid #CBD5E0;"> Semester Genap </th>
                        <th class="text-center" style="border: 1px solid #CBD5E0;"> Total </th>
                    </tr>
                    <?php 
                        $skripsi_ganjil_sum = 0;
                        $skripsi_genap_sum = 0;
                        $skripsi_yearly_sum = 0;
                        foreach ($skripsi_items as $si) : 
                        $grand_total_kinerja += $si->total_poin_yearly ?? 0;
                        $skripsi_ganjil_sum += $si->ganjil->calculated_poin ?? 0;
                        $skripsi_genap_sum += ($si->genap->calculated_poin ?? 0);
                        $skripsi_yearly_sum += $si->total_poin_yearly ?? 0;
                    ?>
                        <tr>
                            <td style="background-color: #e3f2fd; font-weight: 600; color: #1A365D; border: 1px solid #CBD5E0;"> 
                                <?= $si->display_label; ?> 
                            </td>
                            <td class="text-center" style="background-color: #fff9c4; font-weight: 700; border: 1px solid #CBD5E0;"> 
                                <span><?= $si->ganjil->student_count ?? 0; ?></span>
                            </td>
                            <td class="text-center" style="background-color: #fffde7; font-weight: 700; border: 1px solid #CBD5E0;"> <?= number_format($si->ganjil->calculated_poin ?? 0, 2); ?> </td>
                            <td class="text-center" style="background-color: #e3f2fd; font-weight: 700; border: 1px solid #CBD5E0;"> 
                                <span><?= $si->genap->student_count ?? 0; ?></span>
                            </td>
                            <td class="text-center" style="background-color: #f1f8fe; font-weight: 700; border: 1px solid #CBD5E0;"> <?= number_format($si->genap->calculated_poin ?? 0, 2); ?> </td>
                            <td class="text-center bold" style="background-color: #f5f5f5; font-weight: 700; border: 1px solid #CBD5E0;"> <?= number_format($si->total_poin_yearly ?? 0, 2); ?> </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr style="background-color: #ffe082; font-weight: 800; color: #2D3748;">
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> Pembimbingan Tugas Akhir mahasiswa 12 orang lulusan max per tahun </td>
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> </td>
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($skripsi_ganjil_sum, 2); ?> </td>
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> </td>
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($skripsi_genap_sum, 2); ?> </td>
                        <td class="text-center" style="border: 1px solid #CBD5E0;"> <?= number_format($skripsi_yearly_sum, 2); ?> </td>
                    </tr>
                    
                    <tr style="background: #E2E8F0; font-weight: 800;">
                        <td colspan="5" class="text-right" style="padding-right: 15px; vertical-align: middle; border: 1px solid #CBD5E0; color: #1A202C;"> 
                            <button type="button" class="btn btn-sm btn-success" style="margin-right: 15px; border-radius: 6px; font-weight: 700; padding: 4px 10px;" onclick="bukaModalMahasiswaBimbingan()">
                                <i class="fa fa-users"></i> Lihat Mahasiswa
                            </button>
                            GRAND TOTAL AKHIR 
                        </td>
                        <td class="text-center font-white" style="background-color: #2B6CB0; color: #fff; font-size: 14px; font-weight: 800; border: 1px solid #CBD5E0; vertical-align: middle;"> <?= number_format($grand_total_kinerja, 2); ?> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Mahasiswa Bimbingan -->
<div class="modal fade" id="modalMahasiswaBimbinganInternal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #2B6CB0; color: white; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title" style="font-weight: 800; font-size: 15px; margin: 0; color: white;">
                    <i class="fa fa-users" style="margin-right: 8px;"></i> Daftar Mahasiswa Bimbingan Tugas Akhir (TA <?= $selected_year; ?>/<?= $selected_year + 1; ?>)
                </h4>
                <button type="button" class="close" onclick="$('#modalMahasiswaBimbinganInternal').modal('hide')" style="color: white; opacity: 0.8; font-size: 20px;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 16px;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed" style="font-size: 12px; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #EDF2F7; color: #2D3748;">
                                <th width="40" class="text-center">No</th>
                                <th width="110" class="text-center">NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th width="110" class="text-center">Semester</th>
                                <th width="160" class="text-center">Periode Bimbingan</th>
                                <th width="120" class="text-center">Masa Penyelesaian</th>
                                <th width="100" class="text-center">Nilai Sidang</th>
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
                                        $m_idx = array_search($std->nipd, $nipds);
                                        if ($m_idx !== false && strpos($all_students[$m_idx]->smt_label, $std->smt_label) === false) {
                                            $all_students[$m_idx]->smt_label .= ', ' . $std->smt_label;
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
                                <tr style="background: #EBF8FF; font-weight: 700; color: #2B6CB0;">
                                    <td colspan="7"><?= $si->display_label; ?></td>
                                </tr>
                                <?php $no = 1; foreach ($all_students as $mhs) : 
                                    $tgl_mulai = (!empty($mhs->tanggal_usulan_kaprodi) && $mhs->tanggal_usulan_kaprodi != '0000-00-00') ? date('d-m-Y', strtotime($mhs->tanggal_usulan_kaprodi)) : '-';
                                    $tgl_selesai = (!empty($mhs->sidang_tanggal) && $mhs->sidang_tanggal != '0000-00-00') ? date('d-m-Y', strtotime($mhs->sidang_tanggal)) : ((!empty($mhs->tanggal_keluar) && $mhs->tanggal_keluar != '0000-00-00') ? date('d-m-Y', strtotime($mhs->tanggal_keluar)) : '-');
                                    
                                    $durasi_str = '-';
                                    if ($tgl_mulai != '-' && $tgl_selesai != '-') {
                                        $d1 = new DateTime($mhs->tanggal_usulan_kaprodi);
                                        $d2 = new DateTime(!empty($mhs->sidang_tanggal) && $mhs->sidang_tanggal != '0000-00-00' ? $mhs->sidang_tanggal : $mhs->tanggal_keluar);
                                        $diff = $d1->diff($d2);
                                        $months = ($diff->y * 12) + $diff->m;
                                        $durasi_str = $months . ' Bulan ' . $diff->d . ' Hari';
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center font-mono"><?= $mhs->nipd; ?></td>
                                        <td style="font-weight: 600;"><?= $mhs->nm_pd; ?></td>
                                        <td class="text-center"><span class="badge" style="background:#E2E8F0; color:#2D3748; font-size:11px;"><?= $mhs->smt_label; ?></span></td>
                                        <td class="text-center"><?= $tgl_mulai; ?> s/d <?= $tgl_selesai; ?></td>
                                        <td class="text-center"><?= $durasi_str; ?></td>
                                        <td class="text-center" style="font-weight: 700; color: #276749;"><?= $mhs->nilai_sidang ?? '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php 
                                endif;
                            endforeach; 

                            if (!$has_students) :
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="padding: 24px;">Tidak ada data bimbingan tugas akhir pada tahun akademik ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background: #F8FAFC; padding: 10px 16px;">
                <button type="button" class="btn btn-default" onclick="$('#modalMahasiswaBimbinganInternal').modal('hide')" style="border-radius: 6px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
