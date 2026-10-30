<script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/datatables/datatables.min.js"></script>

<script type="text/javascript">
    var save_method;
    var table;

    $(document).ready(function() {
        table = $('#table').DataTable({

            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,

            "ajax": {
                "url": "<?php echo site_url('lapkerma/C_daftar_mou/ajax_list') ?>",
                "type": "POST",
                "data": {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
            },

            "columnDefs": [{
                "targets": [-1, 0, 1, 2, 3, 4],
                "orderable": false,
            }, ],

        });

        load_status();
        load_instansi();

    });



    function load_status() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_status') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_status;
                $('[name="id_status_kerjasama"]').html('');
                //opsi_status = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_status_kerjasama"]').append(opsi_status);
                $.each(categories, function(key, val) {
                    opsi_status = `<option value="${val.id_status_kerjasama}">${val.nm_status_kerjasama}</option>`;
                    $('[name="id_status_kerjasama"]').append(opsi_status);
                });
            }
        });
    }

    function load_instansi() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_instansi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_daftar_instansi"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_daftar_instansi"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_daftar_instansi}">${val.nm_instansi}</option>`;
                    $('[name="id_daftar_instansi"]').append(opsi_instansi);
                });
            }
        });
    }

    function edit_kerjasama(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('lapkerma/C_daftar_mou/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_mou"]').val(data.id_mou);
                //$('[name="id_dokumen_kerjasama"]').val(data.id_dokumen_kerjasama);
                $('[name="nomor_mou"]').val(data.nomor_mou);
                $('[name="judul_kerjasama"]').val(data.judul_kerjasama);
                $('[name="deskripsi_kerjasama"]').val(data.deskripsi_kerjasama);
                $('[name="id_status_kerjasama"]').val(data.id_status_kerjasama);
                $('[name="tanggal_awal"]').val(data.tanggal_awal);
                $('[name="tanggal_akhir"]').val(data.tanggal_akhir);
                $('[name="id_daftar_instansi"]').val(data.id_daftar_instansi);
                //$('[name="alamat_mitra"]').val(data.alamat_mitra);
                $('[name="penandatangan_mitra"]').val(data.penandatangan_mitra);
                $('[name="penandatangan_wbi"]').val(data.penandatangan_wbi);
                $('[name="jabatan_mitra"]').val(data.jabatan_mitra);
                $('[name="jabatan_wbi"]').val(data.jabatan_wbi);
                $('[name="bentuk_kerjasama"]').val(data.bentuk_kerjasama);
                $('[name="dokumen_mou"]').val(data.dokumen_mou);

                //$('[name="id_bidang_usaha"]').val(data.id_bidang_usaha);
                //$('[name="id_bentuk_kegiatan"]').val(data.id_bentuk_kegiatan);
                //$('[name="magang_mpi"]').val(data.magang_mpi);
                //$('[name="magang_ap"]').val(data.magang_ap);
                //$('[name="magang_ah"]').val(data.magang_ah);
                //$('[name="magang_pka"]').val(data.magang_pka);
                //$('[name="magang_trpl"]').val(data.magang_trpl);

                $('#modal_form').modal('show');
                $('.modal-title').text("Lihat Data MOU");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }
</script>


<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Content is Responsive</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_mou" name="id_mou">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <h4 class="m-b-0 text-white">Masa Berlaku</h4>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="status_kerjasama" class="col-sm-4 col-form-label">Status</label>
                                    <div class="col-sm-8 col-md-8">
                                        <select name="id_status_kerjasama" class="form-control">
                                            <option value="">-- Pilh --</option>
                                        </select>
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="tanggal_awal" class="col-sm-4 col-form-label">Tanggal Awal</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="tanggal_awal" name="tanggal_awal" data-dtp="dtp_00aWj">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tanggal_akhir" class="col-sm-4 col-form-label">Tanggal Akhir</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="tanggal_akhir" name="tanggal_akhir" data-dtp="dtp_00aWj">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="deskripsi_kerjasama" class="col-sm-4 col-form-label">Deskripsi Kerjasama</label>
                                    <div class="col-sm-8 col-md-8">
                                        <textarea class="form-control" rows="2" id="deskripsi_kerjasama" name="deskripsi_kerjasama"></textarea>
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        
                        <div class="form-group row">
                            <label for="nomor_mou" class="col-sm-4 col-form-label">Nomor MOU</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="nomor_mou" name="nomor_mou">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="judul_kerjasama" class="col-sm-4 col-form-label">Judul MOU</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="2" id="judul_kerjasama" name="judul_kerjasama"></textarea>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_daftar_instansi" class="col-sm-4 col-form-label">Instansi Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_daftar_instansi" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="bentuk_kerjasama" class="col-sm-4 col-form-label">Bentuk Kerjasama</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="2" id="bentuk_kerjasama" name="bentuk_kerjasama"></textarea>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="penandatangan_mitra" class="col-sm-4 col-form-label">Penandatangan Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penandatangan_mitra" name="penandatangan_mitra">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_mitra" class="col-sm-4 col-form-label">Jabatan Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="jabatan_mitra" name="jabatan_mitra">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="penandatangan_wbi" class="col-sm-4 col-form-label">Penandatangan Pihak WBI</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penandatangan_wbi" name="penandatangan_wbi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_wbi" class="col-sm-4 col-form-label">Jabatan Pihak WBI</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="jabatan_wbi" name="jabatan_wbi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="dokumen_mou" class="col-sm-4 col-form-label">Dokumen</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="dokumen_mou" name="dokumen_mou">
                                <div class="help">Isi link google drive</div>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>