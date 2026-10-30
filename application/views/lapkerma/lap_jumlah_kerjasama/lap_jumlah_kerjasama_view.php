<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>

                    </div>
                </div>
                <div class="portlet-body">

                    <div class="table-container">

                        <table id="tablex" class="table table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 20px">No.</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td><strong>MOU</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>a. Jumlah MOU Aktif</td>
                                    <td><?= $get_data[0]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_mou_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>b. Jumlah MOU Tidak Aktif</td>
                                    <td><?= $get_data[1]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_mou_tidak_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>c. Jumlah MOU Expired</td>
                                    <td><?= $get_data[2]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_mou_expired'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>d. MOU Akan Expired dalam waktu 6 Bulan</td>
                                    <td><?= $get_data[14]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_mou_expired_soon'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>e. Total</td>
                                    <td><?= $get_data[3]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td><strong>MOA</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>a. Jumlah MOA Aktif</td>
                                    <td><?= $get_data[4]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_moa_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>b. Jumlah MOA Tidak Aktif</td>
                                    <td><?= $get_data[5]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_moa_tidak_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>c. Jumlah MOA Expired</td>
                                    <td><?= $get_data[6]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_moa_expired'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>d. MOU Akan Expired dalam waktu 6 Bulan</td>
                                    <td><?= $get_data[15]; ?></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_moa_expired_soon'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>e. Total</td>
                                    <td><?= $get_data[7]; ?></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td>3.</td>
                                    <td><strong>INSTANSI</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>a. Pemerintahan</td>
                                    <td><?= $get_data[8]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>b. Pendidikan</td>
                                    <td><?= $get_data[9]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>c. Perusahaan</td>
                                    <td><?= $get_data[10]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>d. Lainnya</td>
                                    <td><?= $get_data[11]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>e. Perpustakaan Negeri</td>
                                    <td><?= $get_data[12]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>f. Perpustakaan Swasta</td>
                                    <td><?= $get_data[13]; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>g. Download Instansi/Mitra Kerjasama WBI</td>
                                    <td></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_instansi'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>h. Download Instansi/Mitra Kerjasama dengan WBI dan Jumlah MOU dan MOA</td>
                                    <td></td>
                                    <td><a href="<?= site_url('lapkerma/C_download/download_instansi_mou'); ?> " class="btn btn-success btn-sm">Download</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>