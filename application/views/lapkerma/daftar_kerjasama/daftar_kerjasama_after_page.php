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
                "url": "<?php echo site_url('C_daftar_kerjasama/ajax_list') ?>",
                "type": "POST",
                "data": {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
            },

            "columnDefs": [{
                "targets": [-1],
                "orderable": false,
            }, ],

        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
            var method;
            if (save_method == 'add') {
                url = "<?php echo site_url('C_daftar_kerjasama/ajax_add') ?>";
                method = 'disimpan';
            } else {
                url = "<?php echo site_url('C_daftar_kerjasama/ajax_update') ?>";
                method = 'diupdate';
            }

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form').modal('hide');
                        swalert(method);
                        reload_table();
                    } else {
                        $.each(data.errors, function(key, value) {
                            $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                            $('[name="' + key + '"]').next().text(value);
                        });
                    }
                }
            });
        });

        $("input").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });
        $("textarea").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });
        $("select").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });

        $('#tanggal_awal').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false
        });
        $('#tanggal_akhir').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false
        });

        load_dokumen();
        load_status();
        load_instansi();
        load_bidang_kerjasama();
        load_bidang_usaha();
        load_bentuk_kegiatan();

    });

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function load_dokumen() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_dokumen') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dok;
                $('[name="id_dokumen_kerjasama"]').html('');
                opsi_dok = `<option value="0">Silahkan Pilih</option>`;
                $('[name="id_dokumen_kerjasama"]').append(opsi_dok);
                $.each(categories, function(key, val) {
                    opsi_dok = `<option value="${val.id_dokumen_kerjasama}">${val.nm_dokumen}</option>`;
                    $('[name="id_dokumen_kerjasama"]').append(opsi_dok);
                });
            }
        });
    }

    function load_status() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_status') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_status;
                $('[name="status_kerjasama"]').html('');
                opsi_status = `<option value="">Silahkan Pilih</option>`;
                $('[name="status_kerjasama"]').append(opsi_status);
                $.each(categories, function(key, val) {
                    opsi_status = `<option value="${val.id_status_kerjasama}">${val.nm_status_kerjasama}</option>`;
                    $('[name="status_kerjasama"]').append(opsi_status);
                });
            }
        });
    }

    function load_instansi() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_instansi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_sub_instansi"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_sub_instansi"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_sub_instansi}">${val.nm_instansi +" - "+val.nm_sub_instansi}</option>`;
                    $('[name="id_sub_instansi"]').append(opsi_instansi);
                });
            }
        });
    }

    function load_bidang_kerjasama() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_bidang_kerjasama') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bidang_kerjasama;
                $('[name="id_bidang_kerjasama"]').html('');
                opsi_bidang_kerjasama = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_bidang_kerjasama"]').append(opsi_bidang_kerjasama);
                $.each(categories, function(key, val) {
                    opsi_bidang_kerjasama = `<option value="${val.id_bidang_kerjasama}">${val.nm_bidang_kerjasama}</option>`;
                    $('[name="id_bidang_kerjasama"]').append(opsi_bidang_kerjasama);
                });
            }
        });
    }

    function load_bidang_usaha() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_bidang_usaha') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bidang_usaha;
                $('[name="id_bidang_usaha"]').html('');
                opsi_bidang_usaha = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_bidang_usaha"]').append(opsi_bidang_usaha);
                $.each(categories, function(key, val) {
                    opsi_bidang_usaha = `<option value="${val.id_bidang_usaha}">${val.nm_bidang_usaha}</option>`;
                    $('[name="id_bidang_usaha"]').append(opsi_bidang_usaha);
                });
            }
        });
    }

    function load_bentuk_kegiatan() {
        $.ajax({
            url: "<?= base_url('C_daftar_kerjasama/get_bentuk_kegiatan') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bentuk_kegiatan;
                $('[name="id_bentuk_kegiatan"]').html('');
                opsi_bentuk_kegiatan = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_bentuk_kegiatan"]').append(opsi_bentuk_kegiatan);
                $.each(categories, function(key, val) {
                    opsi_bentuk_kegiatan = `<option value="${val.id_bentuk_kegiatan}">${val.nm_bentuk_kegiatan}</option>`;
                    $('[name="id_bentuk_kegiatan"]').append(opsi_bentuk_kegiatan);
                });
            }
        });
    }

    function add_kerjasama() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Kerjasama');
    }

    function edit_kerjasama(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('C_daftar_kerjasama/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_kerjasama"]').val(data.id_kerjasama);
                $('[name="id_dokumen_kerjasama"]').val(data.id_dokumen_kerjasama);
                $('[name="nomor_dokumen"]').val(data.nomor_dokumen);
                $('[name="judul_kerjasama"]').val(data.judul_kerjasama);
                $('[name="deskripsi_kerjasama"]').val(data.deskripsi_kerjasama);
                $('[name="status_kerjasama"]').val(data.status_kerjasama);
                $('[name="tanggal_awal"]').val(data.tanggal_awal);
                $('[name="tanggal_akhir"]').val(data.tanggal_akhir);
                $('[name="instansi_mitra"]').val(data.instansi_mitra);
                $('[name="alamat_mitra"]').val(data.alamat_mitra);
                $('[name="penandatangan_mitra"]').val(data.penandatangan_mitra);
                $('[name="jabatan_mitra"]').val(data.jabatan_mitra);
                $('[name="id_sub_instansi"]').val(data.id_sub_instansi);
                $('[name="id_bidang_kerjasama"]').val(data.id_bidang_kerjasama);
                $('[name="id_bidang_usaha"]').val(data.id_bidang_usaha);
                $('[name="id_bentuk_kegiatan"]').val(data.id_bentuk_kegiatan);
                $('[name="magang_mpi"]').val(data.magang_mpi);
                $('[name="magang_ap"]').val(data.magang_ap);
                $('[name="magang_ah"]').val(data.magang_ah);
                $('[name="magang_pka"]').val(data.magang_pka);
                $('[name="magang_trpl"]').val(data.magang_trpl);

                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Kerjasama");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function delete_kerjasama(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data Kerjasama akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('C_daftar_kerjasama/ajax_delete') ?>/" + id,
                    type: "DELETE",
                    dataType: "json",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                    success: function(data) {
                        reload_table();
                        swalert('dihapus');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error deleting data');
                    }
                });
            }
        });
    }
</script>


<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal Content is Responsive</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body form">


                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_kerjasama" name="id_kerjasama">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-dark">
                                <h4 class="m-b-0 text-white">Masa Berlaku</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="status_kerjasama" class="col-sm-4 col-form-label">Status</label>
                                    <div class="col-sm-8 col-md-8">
                                        <select name="status_kerjasama" class="form-control">
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
                                <div class="form-group">
                                    <label for="tanggal_akhir">Deskripsi Kerjasama</label>
                                    <div>
                                        <textarea class="form-control" rows="5" id="deskripsi_kerjasama" name="deskripsi_kerjasama"></textarea>
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <p>Data Luaran Magang</p>
                                <div class="form-group row">
                                    <label for="magang_mpi" class="col-sm-4 col-form-label">PSMPI</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="magang_mpi" name="magang_mpi">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="magang_ap" class="col-sm-4 col-form-label">PSAP</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="magang_ap" name="magang_ap">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="magang_ah" class="col-sm-4 col-form-label">PSAH</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="magang_ah" name="magang_ah">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="magang_pka" class="col-sm-4 col-form-label">PSPKA</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="magang_pka" name="magang_pka">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="magang_trpl" class="col-sm-4 col-form-label">PSTRPL</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input type="text" class="form-control" id="magang_trpl" name="magang_trpl">
                                        <div class="form-control-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="id_dokumen_kerjasama" class="col-sm-4 col-form-label">Jenis Dokumen</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_dokumen_kerjasama" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nomor_dokumen" class="col-sm-4 col-form-label">Nomor Dokumen</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="nomor_dokumen" name="nomor_dokumen">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="judul_kerjasama" class="col-sm-4 col-form-label">Judul Kerjasama</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="2" id="judul_kerjasama" name="judul_kerjasama"></textarea>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="instansi_mitra" class="col-sm-4 col-form-label">Instansi Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="instansi_mitra" name="instansi_mitra">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_mitra" class="col-sm-4 col-form-label">Alamat Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="2" id="alamat_mitra" name="alamat_mitra"></textarea>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="penandatangan_mitra" class="col-sm-4 col-form-label">Penandatangan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penandatangan_mitra" name="penandatangan_mitra">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_mitra" class="col-sm-4 col-form-label">Jabatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="jabatan_mitra" name="jabatan_mitra">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_sub_instansi" class="col-sm-4 col-form-label">Jenis Instansi</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_sub_instansi" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_bidang_usaha" class="col-sm-4 col-form-label">Bidang Usaha</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_bidang_usaha" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_bidang_kerjasama" class="col-sm-4 col-form-label">Bidang Kerjasama</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_bidang_kerjasama" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_bentuk_kegiatan" class="col-sm-4 col-form-label">Bentuk Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_bentuk_kegiatan" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-info"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>