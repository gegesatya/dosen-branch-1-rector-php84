<div class="page-content" style="padding-top:0">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-line-chart font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
                    </div>
                    <div class="actions">
                        <form action="<?= site_url('C_kinerja'); ?>" method="get" class="form-inline">
                            <select name="id_smt" class="form-control input-sm" onchange="this.form.submit()">
                                <?php foreach ($list_smt as $smt) : ?>
                                    <option value="<?= $smt->id_smt; ?>" <?= $id_smt_selected == $smt->id_smt ? 'selected' : ''; ?>>
                                        <?= $smt->nm_smt; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="dashboard-stat blue-madison">
                                <div class="visual">
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="details">
                                    <div class="number">
                                        <?= number_format($total_poin, 2); ?>
                                    </div>
                                    <div class="desc">
                                        Total Kredit Kinerja
                                    </div>
                                </div>
                                <a class="more" href="javascript:;">
                                    Berdasarkan Rubrik <i class="m-icon-swapright m-icon-white"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="note note-info">
                                <h4 class="block">Ringkasan Kinerja</h4>
                                <p>Perhitungan kinerja didasarkan pada jadwal perkuliahan yang terdaftar pada semester <strong><?= $id_smt_selected; ?></strong>. Nilai kredit dikalikan dengan bobot SKS masing-masing mata kuliah sesuai dengan kategori jadwal.</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="50"> No </th>
                                    <th> Kategori Rubrik (e_kinerja_nilai) </th>
                                    <th> Mata Kuliah Terdeteksi </th>
                                    <th class="text-center"> Total SKS </th>
                                    <th class="text-center"> Kredit </th>
                                    <th class="text-center"> Total Poin </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data_kinerja)) : ?>
                                    <tr>
                                        <td colspan="6" class="text-center"> Data rubrik tidak ditemukan. </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; foreach ($data_kinerja as $row) : ?>
                                        <tr>
                                            <td> <?= $no++; ?> </td>
                                            <td> 
                                                <strong><?= $row->display_label; ?></strong>
                                                <br><small class="text-muted">ID Kategori: <?= $row->id_kategori_jadwal; ?></small>
                                            </td>
                                            <td> 
                                                <?php if (!empty($row->schedules)) : ?>
                                                    <ul class="margin-bottom-0" style="padding-left:15px">
                                                        <?php foreach ($row->schedules as $s) : ?>
                                                            <li>
                                                                <?= $s->tag_name; ?> (<?= $s->sks_mk; ?> SKS) 
                                                                <?php if ($s->student_count > 0) : ?>
                                                                    <span class="badge badge-success"><?= $s->student_count; ?> Mhs</span>
                                                                <?php endif; ?>
                                                                <?php if ($s->divider > 1) : ?>
                                                                    <span class="badge badge-info">Team (<?= $s->divider; ?> Dosen)</span>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php elseif (isset($row->perwalian_formula)) : ?>
                                                    <div class="alert alert-info margin-bottom-0" style="padding: 5px 10px;">
                                                        <i class="fa fa-info-circle"></i> Mahasiswa Aktif (Approve KRS): <strong><?= $row->student_count; ?> Mhs</strong>
                                                        <br><small>Rumus: <?= $row->perwalian_formula; ?></small>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="label label-sm label-warning">Belum ada data matching</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"> <?= $row->total_sks; ?> </td>
                                            <td class="text-center"> <?= number_format($row->nilai_kredit_detected, 2); ?> </td>
                                            <td class="text-center"> <strong><?= number_format($row->calculated_poin, 2); ?></strong> </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="5" class="text-right"> TOTAL AKUMULASI POIN KINERJA </th>
                                    <th class="text-center"> <span class="badge badge-success"> <?= number_format($total_poin, 2); ?> </span> </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
