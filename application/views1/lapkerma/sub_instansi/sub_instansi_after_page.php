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
                "url": "<?php echo site_url('C_sub_instansi/ajax_list') ?>",
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
                url = "<?php echo site_url('C_sub_instansi/ajax_add') ?>";
                method = 'disimpan';
            } else {
                url = "<?php echo site_url('C_sub_instansi/ajax_update') ?>";
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

    });

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function load_instansi() {
        $.ajax({
            url: "<?= base_url('C_sub_instansi/get_instansi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_topik;
                $('[name="id_instansi"]').html('');
                opsi_topik = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_instansi"]').append(opsi_topik);
                $.each(categories, function(key, val) {
                    opsi_topik = `<option value="${val.id_jenis_instansi}">${val.nm_jenis_instansi}</option>`;
                    $('[name="id_instansi"]').append(opsi_topik);
                });
            }
        });
    }

    function add_sub_instansi() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Sub Instansi');
    }

    function edit_sub_instansi(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('C_sub_instansi/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_instansi"]').val(data.id_instansi);
                $('[name="id_sub_instansi"]').val(data.id_sub_instansi);
                $('[name="nm_sub_instansi"]').val(data.nm_sub_instansi);
                $('[name="kode_sub_instansi"]').val(data.kode_sub_instansi);

                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Sub Instansi");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function delete_sub_instansi(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data Sub Instansi akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('C_sub_instansi/ajax_delete') ?>/" + id,
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
    <div class="modal-dialog">
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
                <input type="hidden" id="id_sub_instansi" name="id_sub_instansi">
                <div class="form-group row">
                    <label for="instansi" class="col-sm-4 col-form-label">Instansi</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_instansi" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="kode_instansi" class="col-sm-4 col-form-label">Kode Sub Instansi</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="kode_sub_instansi" name="kode_sub_instansi">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nm_instansi" class="col-sm-4 col-form-label">Nama Sub Instansi</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="nm_sub_instansi" name="nm_sub_instansi">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>