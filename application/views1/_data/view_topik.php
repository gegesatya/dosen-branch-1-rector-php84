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
                            <button class="btn btn-sm btn-primary" onclick="add_topik()"><i class="fa fa-plus"></i> Tambah Topik</button>
                            <button class="btn btn-sm btn-secondary" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Reload</button>
                        </div>

                        <table id="ajax_tabel" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">No.</th>
                                    <th>Topik</th>
                                    <th>Deskripsi</th>
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
                <input type="hidden" value="" name="id_topik" />
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Topik Skripsi</label>
                        <div class="col-md-9">
                            <input name="topik_skripsi" class="form-control" type="text">
                            <span class="invalid-feedback"></span>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Deskripsi</label>
                        <div class="col-md-9">
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <span class="invalid-feedback"></span>
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
                    "url": "<?= base_url() . 'E_skripsi_topik/get_all' ?>",
                    "type": "POST",
                    "data": function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },
                columns: [{
                        "data": "id_topik",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "topik_skripsi",
                        "orderable": false,
                    },
                    {
                        "data": "deskripsi",
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

            $('#modal_form').on('shown.bs.modal', function(e) {
                load_kategori();
            });

            $('#modal_form').on('hidden.bs.modal', function(e) {
                var inputs = $('#form input, #form textarea, #form select');
                inputs.removeClass('is-valid is-invalid');
            });
        });



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

        function add_topik() {
            save_label = 'add';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.invalid-feedback').empty();
            $('#modal_form').modal('show');
            $('.modal-title').text('Tambah Topik');
        }

        function edit_topik(id) {
            save_label = 'update';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.invalid-feedback').empty();

            //Ajax Load data from ajax
            $.ajax({
                url: "<?= base_url('E_skripsi_topik/edit/') ?>/" + id,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    $('[name="id_topik"]').val(data.id_topik);
                    $('[name="topik_skripsi"]').val(data.topik_skripsi);
                    $('[name="deskripsi"]').val(data.deskripsi);
                    $('#modal_form').modal('show');
                    $('.modal-title').text('Edit Barang');
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
                url = "<?= base_url('E_skripsi_topik/add') ?>";
                method = 'disimpan';
            } else {
                url = "<?= base_url('E_skripsi_topik/update') ?>";
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
                            $('[name="' + key + '"]').parent().parent().addClass('has-error');
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


        function hapus_topik(id) {
            Swal({
                title: 'Anda yakin?',
                text: "Data topik akan dihapus!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus data!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?= base_url('E_skripsi_topik/delete') ?>/" + id,
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