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
                            <button class="btn btn-sm btn-secondary" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Reload</button>
                        </div>

                        <table id="ajax_tabel" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">No.</th>
                                    <th>Tgl Surat Tugas</th>
                                    <th>Nomor Surat Tugas</th>
                                    <th>Tgl Sempro</th>
                                    <th>Jam Sempro</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
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
                    "url": "<?= base_url() . 'E_skripsi_surat_tugas/get_all' ?>",
                    "type": "POST",
                    "data": function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },
                columns: [{
                        "data": "id_judul_skripsi",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "tanggal_surat_tugas",
                        "orderable": false,
                    },
                    {
                        "data": "no_surat_tugas",
                        "orderable": false,
                    },
                    {
                        "data": "tanggal_sempro",
                        "orderable": false,
                    },
                    {
                        "data": "jam_sempro",
                        "orderable": false,
                    },
                    {
                        "data": "nipd",
                        "orderable": false,
                    },
                    {
                        "data": "nm_pd",
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


        function edit_topik(id) {
            save_label = 'update';
            $('#form')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.invalid-feedback').empty();

            //Ajax Load data from ajax
            $.ajax({
                url: "<?= base_url('E_skripsi_surat_tugas/edit/') ?>/" + id,
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
    </script>