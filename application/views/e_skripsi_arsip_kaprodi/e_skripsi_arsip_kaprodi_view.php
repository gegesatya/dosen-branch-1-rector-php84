<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-folder font-dark"></i>
                        <span class="caption-subject bold uppercase"> Arsip TA Kedaluwarsa (> 1 Tahun)</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="note note-warning">
                        <h4 class="block">Perhatian!</h4>
                        <p> Daftar di bawah ini adalah mahasiswa yang SK Tugas Akhirnya telah melewati masa 1 tahun dan belum dinyatakan Lulus Sidang. Anda sebagai Kaprodi berhak melakukan wawancara dan mengevaluasi kelanjutan SK mereka. </p>
                    </div>
                    <div class="table-container table-responsive-2">
                        <table class="table table-striped table-bordered table-hover" id="table">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-center"> No </th>
                            <th style="width: 20%;"> Mahasiswa </th>
                            <th style="width: 20%;"> Pembimbing Utama </th>
                            <th style="width: 30%;"> Judul Skripsi </th>
                            <th style="width: 15%;"> Status Kedaluwarsa </th>
                            <th style="width: 10%;"> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perpanjang SK -->
<div class="modal fade" id="modal_perpanjang" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Perpanjang SK Tugas Akhir</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_perpanjang" class="form-horizontal">
                    <input type="hidden" value="" name="id_judul_skripsi"/> 
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-body">
                        <div class="form-group" id="info_pencapaian_container" style="display: none;">
                            <label class="control-label col-md-4">Pencapaian Terakhir</label>
                            <div class="col-md-8">
                                <div id="info_pencapaian" style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; font-size: 13px; border-radius: 4px;">
                                    Memuat data pencapaian...
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Dosen Pembimbing Utama</label>
                            <div class="col-md-8">
                                <select name="dosen_pembimbing_1" class="form-control" style="width: 100%;">
                                    <?php foreach ($list_dosen as $d) : ?>
                                        <option value="<?= $d->id_ptk ?>"><?= $d->nama_lengkap ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block">Biarkan jika tidak ada pergantian dosen.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Opsi Reset Penguji</label>
                            <div class="col-md-8">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="clear_penguji" value="true" checked> Hapus Data Dewan Penguji Lama (Jika ada)
                                    <span></span>
                                </label>
                                <span class="help-block" style="font-size:11px;">Mereset Dosen Penguji agar bisa ditentukan ulang.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Opsi Reset Nomor SK</label>
                            <div class="col-md-8">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="clear_nomor_sk" value="true" checked> Hapus Nomor SK Lama
                                    <span></span>
                                </label>
                                <span class="help-block" style="font-size:11px;">Mereset Nomor SK lama agar admin bisa meng-generate Nomor SK yang baru.</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_perpanjang()" class="btn btn-primary">Simpan & Perpanjang</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
