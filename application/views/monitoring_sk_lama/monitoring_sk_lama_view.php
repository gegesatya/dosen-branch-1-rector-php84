<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-settings font-dark"></i>
                        <span class="caption-subject bold uppercase"> <?= $judul_besar ?> <small><?= $judul_kecil ?></small></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="m-heading-1 border-green m-bordered">
                        <h3>Informasi Laporan</h3>
                        <p> Halaman ini digunakan untuk memantau daftar mahasiswa yang telah memiliki SK Tugas Akhir lebih dari 1 tahun, masih aktif (belum lulus/keluar), dan progress pengerjaan tugas akhirnya terindikasi masih 0 atau belum selesai. </p>
                        <p> Anda dapat men-download laporannya dalam bentuk Excel melalui tombol di bawah ini. </p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <a href="<?= site_url('c_monitoring_sk_lama/download_excel') ?>" class="btn green-jungle">
                            <i class="fa fa-file-excel-o"></i> Download Laporan Excel Sekarang
                        </a>
                    </div>
                    
                    <div class="table-container table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th>Tanggal SK</th>
                                    <th>Umur SK</th>
                                    <th>IPK</th>
                                    <th>Sempro</th>
                                    <th>Semhas</th>
                                    <th>Sidang</th>
                                    <th>Pembimbing 1</th>
                                    <th>Penguji 2</th>
                                    <th>Penguji 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach($result as $row): 
                                    $sempro = empty($row->status_sempro) ? '-' : strtoupper((string)$row->status_sempro);
                                    $semhas = empty($row->status_semhas) ? '-' : strtoupper((string)$row->status_semhas);
                                    $sidang = empty($row->status_sidang) ? '-' : strtoupper((string)$row->status_sidang);
                                    
                                    $tgl_sk = new DateTime($row->tanggal_sk);
                                    $sekarang = new DateTime();
                                    $diff = $tgl_sk->diff($sekarang);
                                    $umur_sk = $diff->y . ' Thn ' . $diff->m . ' Bln';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row->nm_pd ?></td>
                                    <td><?= $row->nipd ?></td>
                                    <td><?= $row->tanggal_sk ?></td>
                                    <td><?= $umur_sk ?></td>
                                    <td><?= $row->ipk ?></td>
                                    <td><?= $sempro ?></td>
                                    <td><?= $semhas ?></td>
                                    <td><?= $sidang ?></td>
                                    <td><?= $row->pembimbing_1 ?></td>
                                    <td><?= $row->penguji_2 ?></td>
                                    <td><?= $row->penguji_3 ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>
