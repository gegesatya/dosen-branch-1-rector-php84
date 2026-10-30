<script src="<?= base_url('assets/elite-admin/assets/node_modules/datatables/datatables.min.js'); ?>"></script>
<script src="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

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
                "url": "<?php echo site_url('lapkerma/C_kegiatan/ajax_list') ?>",
                "type": "POST",
                "data": {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
            },

            "columnDefs": [{
                "targets": [-1, 0, 1, 2, 3],
                "orderable": false,
            }, ],

        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
            var method;
            if (save_method == 'add') {
                url = "<?php echo site_url('lapkerma/C_kegiatan/ajax_add') ?>";
                method = 'disimpan';
            } else {
                url = "<?php echo site_url('lapkerma/C_kegiatan/ajax_update') ?>";
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
                            if (value) {
                                $('[name="' + key + '"]').parent().parent().addClass('has-error');
                                $('[name="' + key + '"]').next().text(value);
                            }
                        });
                        // for (var i = 0; i < data.inputerror.length; i++) {
                        //     $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                        //     $('[name="' + key + '"]').next().text(data.error_string[i]);
                        // }
                    }
                }
            });
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $("input").change(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });



        load_moa();
        load_instansi();
        load_bentuk_kegiatan();

    });

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function load_moa() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_moa') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(mou) {
                console.log(mou);
                var opsi_instansi;
                $('[name="id_moa"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_moa"]').append(opsi_instansi);
                $.each(mou, function(key, val) {
                    opsi_instansi = `<option value="${val.id_moa}">(${val.nomor_dokumen_moa}) ${val.judul_moa}</option>`;
                    $('[name="id_moa"]').append(opsi_instansi);
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

    function load_bentuk_kegiatan() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_bentuk_kegiatan') ?>",
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

    function add_kegiatan() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Laporan Kegiatan');
    }

    function edit_kegiatan(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('lapkerma/C_kegiatan/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_kegiatan"]').val(data.id_kegiatan);
                $('[name="id_daftar_instansi"]').val(data.id_daftar_instansi);
                $('[name="id_moa"]').val(data.id_moa);
                $('[name="judul_kegiatan"]').val(data.judul_kegiatan);
                $('[name="id_bentuk_kegiatan"]').val(data.id_bentuk_kegiatan);
                $('[name="tanggal_awal_kegiatan"]').val(data.tanggal_awal_kegiatan);
                $('[name="tanggal_akhir_kegiatan"]').val(data.tanggal_akhir_kegiatan);
                $('[name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
                $('[name="hasil"]').val(data.hasil);
                $('[name="laporan_singkat_kegiatan"]').val(data.laporan_singkat_kegiatan);
                $('[name="saran"]').val(data.saran);
                $('[name="dokumen_pdf"]').val(data.dokumen_pdf);
                $('[name="galeri_foto"]').val(data.galeri_foto);
                $('[name="pic_kegiatan"]').val(data.pic_kegiatan);
                $('[name="handphone_pic"]').val(data.handphone_pic);

                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Laporan Kegiatan");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function delete_kegiatan(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data Kegiatan akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('lapkerma/C_kegiatan/ajax_delete') ?>/" + id,
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Content is Responsive</h4>

            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_kegiatan" name="id_kegiatan">
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="id_daftar_instansi" class="col-sm-4 col-form-label">Instansi Mitra</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_daftar_instansi" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_moa" class="col-sm-4 col-form-label">MOA</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_moa" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 col-form-label">Tanggal Awal Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input name="tanggal_awal_kegiatan" placeholder="Tanggal Awal Kegiatan" class="form-control datepicker" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 col-form-label">Tanggal Akhir Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input name="tanggal_akhir_kegiatan" placeholder="Tanggal Akhir Kegiatan" class="form-control datepicker" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="judul_kegiatan" class="col-sm-4 col-form-label">Judul Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Judul Kegiatan" id="judul_kegiatan" name="judul_kegiatan">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lokasi_kegiatan" class="col-sm-4 col-form-label">Lokasi Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Lokasi Kegiatan" id="lokasi_kegiatan" name="lokasi_kegiatan">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_bentuk_kegiatan" class="col-sm-4 col-form-label">Bentuk Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_bentuk_kegiatan" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="hasil" class="col-sm-4 col-form-label">Hasil Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" placeholder="Hasil Kegiatan" rows="3" id="hasil" name="hasil"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="laporan_singkat_kegiatan" class="col-sm-4 col-form-label">Laporan Singkat Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="3" placeholder="Laporan Singkat" id="laporan_singkat_kegiatan" name="laporan_singkat_kegiatan"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="saran" class="col-sm-4 col-form-label">Saran</label>
                            <div class="col-sm-8 col-md-8">
                                <textarea class="form-control" rows="3" placeholder="Saran" id="saran" name="saran"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dokumen_pdf" class="col-sm-4 col-form-label">Dokumen PDF</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Cantumkan link dari Google Drive" id="dokumen_pdf" name="dokumen_pdf">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="galeri_foto" class="col-sm-4 col-form-label">Galeri Foto</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Cantumkan link dari Google Drive" id="galeri_foto" name="galeri_foto">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pic_kegiatan" class="col-sm-4 col-form-label">PIC Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="PIC Kegiatan" id="pic_kegiatan" name="pic_kegiatan">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="handphone_pic" class="col-sm-4 col-form-label">Handphone PIC</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Handphone PIC" id="handphone_pic" name="handphone_pic">
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-success"'); ?>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>