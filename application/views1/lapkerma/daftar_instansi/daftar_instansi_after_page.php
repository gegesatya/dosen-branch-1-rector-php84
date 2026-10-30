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
                "url": "<?php echo site_url('lapkerma/C_daftar_instansi/ajax_list') ?>",
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
                url = "<?php echo site_url('lapkerma/C_daftar_instansi/ajax_add') ?>";
                method = 'disimpan';
            } else {
                url = "<?php echo site_url('lapkerma/C_daftar_instansi/ajax_update') ?>";
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

        load_instansi();
        load_bidang_usaha();

    });

    function load_instansi() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_jenis_instansi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_sub_instansi"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_sub_instansi"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_sub_instansi}">${val.nm_jenis_instansi +" - "+val.nm_sub_instansi}</option>`;
                    $('[name="id_sub_instansi"]').append(opsi_instansi);
                });
            }
        });
    }

    function load_bidang_usaha() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_bidang_usaha') ?>",
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

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };


    function edit_instansi(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('lapkerma/C_daftar_instansi/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_daftar_instansi"]').val(data.id_daftar_instansi);
                $('[name="nm_instansi"]').val(data.nm_instansi);
                $('[name="alamat_instansi"]').val(data.alamat_instansi);
                $('[name="telepon"]').val(data.telepon);
                $('[name="handphone"]').val(data.handphone);
                $('[name="email"]').val(data.email);
                $('[name="pic"]').val(data.pic);
                $('[name="jabatan"]').val(data.jabatan);
                $('[name="id_sub_instansi"]').val(data.id_sub_instansi);
                if (data.id_bidang_usaha == 0 || data.id_bidang_usaha == '') {
                    $('[name="id_bidang_usaha"]').val("");
                } else {
                    $('[name="id_bidang_usaha"]').val(data.id_bidang_usaha);
                }


                $('#modal_form').modal('show');
                $('.modal-title').text("Dat Mitra/Instansi Kerjasama");

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
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                <h4 class="modal-title">Modal Content is Responsive</h4>
            </div>
            <div class="modal-body ">

                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_daftar_instansi" name="id_daftar_instansi">
                <div class="form-group row">
                    <label for="open_time_start" class="col-sm-4 col-form-label">Nama Instansi</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="nm_instansi" name="nm_instansi">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="open_time_start" class="col-sm-4 col-form-label">Alamat Instansi</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" id="alamat_instansi" name="alamat_instansi" rows="2"></textarea>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="open_time_end" class="col-sm-4 col-form-label">Telepon</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="telepon" name="telepon">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="open_time_end" class="col-sm-4 col-form-label">Seluler</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="handphone" name="handphone">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="open_time_end" class="col-sm-4 col-form-label">Email</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="email" name="email">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="open_time_end" class="col-sm-4 col-form-label">PIC</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="pic" name="pic">
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

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>