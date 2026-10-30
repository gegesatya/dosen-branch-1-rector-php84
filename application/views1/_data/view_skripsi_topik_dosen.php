<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small> </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container table-responsive-2">
                        <div class="margin-top-10 margin-bottom-10">
                            <button class="btn btn-sm btn-primary" onclick="add_topik_dosen()"><i class="fa fa-plus"></i> Add</button>
                            <button class="btn btn-sm btn-secondary" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Reload</button>
                        </div>

                        <table id="ajax_tabel" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">No.</th>
                                    <th>Topik Skripsi</th>
                                    <th>Dosen</th>
                                    <th style="width: 180px;">Action</th>
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
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['id' => 'form', 'class' => 'form-horizontal'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_dosen_topik" />
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Topik</label>
                        <div class="col-md-9">
                            <select name="id_topik" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Dosen</label>
                        <div class="col-md-9">
                            <select name="id_ptk" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>

                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('_layout/_script_datatables');
?>

<script src="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url('assets/theme'); ?>/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets/theme'); ?>/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    var save_label;
    var table;

    $(document).ready(function() {
        load_kategori();
        load_topik();
        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        };

        table = $("#ajax_tabel").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#ajax_tabel_filter input')
                    .off('.DT')
                    .on('keyup.DT', function(e) {
                        api.search(this.value).draw();
                    });
            },
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?= base_url() . 'E_skripsi_topik_dosen/get_all' ?>",
                "type": "POST",
                "data": function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
            },
            columns: [{
                    "data": "id_dosen_topik",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "topik_skripsi",
                    "orderable": false,
                },
                {
                    "data": "nm_ptk",
                    "orderable": false,
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [
                [1, 'asc']
            ],
            rowId: function(a) {
                return a;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
        });

        /* $('#modal_form').on('shown.bs.modal', function(e) {
            load_kategori();
        }); */

        $('#modal_form').on('hidden.bs.modal', function(e) {
            var inputs = $('#form input, #form textarea, #form select');
            inputs.removeClass('is-valid is-invalid');
        });
    });


    function load_kategori() {
        $.ajax({
            url: "<?= base_url('E_skripsi_topik_dosen/getDosen') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="id_ptk"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_ptk"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="id_ptk"]').append(opsi_dosen);
                });
            }
        });
    }

    function load_topik() {
        $.ajax({
            url: "<?= base_url('E_skripsi_topik_dosen/getTopik') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_topik;
                $('[name="id_topik"]').html('');
                opsi_topik = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_topik"]').append(opsi_topik);
                $.each(categories, function(key, val) {
                    opsi_topik = `<option value="${val.id_topik}">${val.topik_skripsi}</option>`;
                    $('[name="id_topik"]').append(opsi_topik);
                });
            }
        });
    }

    function reload_ajax() {
        table.ajax.reload(null, false);
    }

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function add_topik_dosen() {
        save_label = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('[name="id_ptk"]').attr("disabled", false);

        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Dosen Pembimbing/Penguji');
    }

    function edit_topik_dosen(id) {
        save_label = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= base_url('E_skripsi_topik_dosen/edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('[name="id_dosen_topik"]').val(data.id_dosen_topik);
                $('[name="id_ptk"]').val(data.id_ptk);
                $('[name="id_topik"]').val(data.id_topik);
                /* $('[name="id_ptk"]').attr("disabled", true);

                if (data.is_pembimbing == 'Y') {
                    $('[name="is_pembimbing"]').prop("checked", true);
                }

                if (data.is_penguji == 'Y') {
                    $('[name="is_penguji"]').prop("checked", true);
                } */

                $('#modal_form').modal('show');
                $('.modal-title').text('Edit Dosen Pembimbing/Penguji');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url, method;

        if (save_label == 'add') {
            url = "<?= base_url('E_skripsi_topik_dosen/add') ?>";
            method = 'disimpan';
        } else {
            url = "<?= base_url('E_skripsi_topik_dosen/update') ?>";
            method = 'diupdate';
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "json",
            success: function(data) {
                console.log(data);
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_ajax();
                    swalert(method);
                } else {
                    $.each(data.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);
            }
        });

        $('#form input').on('keyup', function() {
            $(this).removeClass('is-valid is-invalid');
        });
        $('#form select').on('change', function() {
            $(this).removeClass('is-valid is-invalid');
        });
    }


    function hapus_topik_dosen(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('E_skripsi_topik_dosen/delete') ?>/" + id,
                    type: "DELETE",
                    dataType: "json",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                    success: function(data) {
                        reload_ajax();
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