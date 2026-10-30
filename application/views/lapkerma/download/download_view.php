


<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"><?= $judul; ?></h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active"><?= $judul; ?></li>
            </ol>
            <button type="button" class="btn btn-info d-none d-lg-block m-l-15" onclick="add_kerjasama()"><i class="fa fa-plus-circle"></i> Add</button>
            <button class="btn btn-default  d-none d-lg-block m-l-15" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
        </div>
    </div>
</div>






<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-stripped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 10px">No.</th>
                                <th>Keterangan</th>
                                <th style="width: 150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1.</td>
                                <td>Daftar Instansi/Mitra Kerjasama dengan WBI</td>
                                <td><a href="<?= site_url('C_download/download_instansi'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Daftar Instansi/Mitra Kerjasama dengan WBI dan Jumlah MOU dan MOA</td>
                                <td><a href="<?= site_url('C_download/download_instansi_mou'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Daftar MOU Aktif</td>
                                <td><a href="<?= site_url('C_download/download_mou_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Daftar MOA Aktif</td>
                                <td><a href="<?= site_url('C_download/download_moa_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Daftar MOU Tidak Aktif</td>
                                <td><a href="<?= site_url('C_download/download_mou_tidak_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>Daftar MOA Tidak Aktif</td>
                                <td><a href="<?= site_url('C_download/download_moa_tidak_aktif'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>Daftar MOU Expired</td>
                                <td><a href="<?= site_url('C_download/download_mou_expired'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                            <tr>
                                <td>8.</td>
                                <td>Daftar MOA Expired</td>
                                <td><a href="<?= site_url('C_download/download_moa_expired'); ?> " class="btn btn-success btn-sm">Download</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


