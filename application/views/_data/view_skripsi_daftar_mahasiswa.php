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
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>SKS</th>
                                    <th>SKS <br>Lulus</th>
                                    <th>SKS <br>Tdk Lulus</th>
                                    <th>IPK</th>
                                    <th>Boleh<br>Pengajuan<br>Judul</th>
                                    <th style="width: 10px;"></th>
                                    <th style="width: 10px;"></th>
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
    <div class="modal-dialog modal-lg" role="document">
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
                <input type="hidden" value="" name="nipd" />
                <input type="hidden" value="" name="analisis_ipk" />
                <input type="hidden" value="" name="analisis_sks_total" />
                <input type="hidden" value="" name="analisis_sks_lulus" />
                <input type="hidden" value="" name="analisis_sks_tidak_lulus" />
                <input type="hidden" value="" name="makul_tidak_lulus" />
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">NIM</label>
                        <div class="col-md-9">
                            <input type="text" name="nipd" class="form-control">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nama</label>
                        <div class="col-md-9">
                            <input name="nm_pd" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div id="table_transkrip"></div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Generate Evaluasi Mahasiswa</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_2" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['id' => 'form2', 'class' => 'form-horizontal'];
                echo form_open('#', $attributes);
                ?>

                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">NIM</label>
                        <div class="col-md-9">
                            <input type="text" name="nipd" class="form-control">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nama</label>
                        <div class="col-md-9">
                            <input name="nm_pd" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">SKS Total</label>
                        <div class="col-md-4">
                            <input name="analisis_sks_total" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">SKS Lulus</label>
                        <div class="col-md-4">
                            <input name="analisis_sks_lulus" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">SKS Tdk Lulus</label>
                        <div class="col-md-4">
                            <input name="analisis_sks_tidak_lulus" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">IPK</label>
                        <div class="col-md-4">
                            <input name="analisis_ipk" class="form-control" type="text">
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Mata Kuliah Tidak Lulus</label>
                        <div class="col-md-9">
                            <div name="makul_tidak_lulus"></div>
                        </div>
                        <span class="invalid-feedback"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Status</label>
                        <div class="col-md-9">
                            <div class="checkbox-list">
                                <label>
                                    <input type="checkbox" name="is_boleh_ajukan_judul"></span> Boleh Ajukan Judul
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Keterangan</label>
                        <div class="col-md-9">
                            <textarea name="keterangan" class="form-control" rows="5"></textarea>

                        </div>
                        <span class="invalid-feedback"></span>
                    </div>

                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave2" onclick="save2()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('_layout/_script_datatables');
?>

<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js'); ?>"></script>
<script src="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

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
                "url": "<?= base_url() . 'E_skripsi_daftar_mahasiswa/get_all' ?>",
                "type": "POST",
                "data": function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
            },
            columns: [{
                    "data": "nipd",
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
                    "data": "analisis_sks_total",
                    "orderable": false,
                },
                {
                    "data": "analisis_sks_lulus",
                    "orderable": false,
                },
                {
                    "data": "analisis_sks_tidak_lulus",
                    "orderable": false,
                },
                {
                    "data": "analisis_ipk",
                    "orderable": false,
                },
                {
                    "data": "is_boleh_ajukan_judul",
                    "orderable": false,
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "view_makul",
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

        $('#modal_form').on('hidden.bs.modal', function(e) {
            var inputs = $('#form input, #form textarea, #form select');
            inputs.removeClass('is-valid is-invalid');
        });
    });

    function reload_ajax() {
        table.ajax.reload(null, false);
    }

    function view_mahasiswa(id) {
        save_label = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_mahasiswa/view') ?>/" + id,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('[name="nipd"]').val(data.query.nipd);
                $('[name="nm_pd"]').val(data.query.nm_pd);
                $('[name="analisis_ipk"]').val(data.analisis_ipk);
                $('[name="analisis_sks_total"]').val(data.analisis_sks_total);
                $('[name="analisis_sks_lulus"]').val(data.analisis_sks_lulus);
                $('[name="analisis_sks_tidak_lulus"]').val(data.analisis_sks_tidak_lulus);
                $('[name="makul_tidak_lulus"]').val(data.makul_tdk_lls);
                $('#table_transkrip').html(data.nilai_tabel);

                $('#modal_form').modal('show');
                $('.modal-title').text('Evaluasi');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function view_makul(id) {
        save_label = 'update';
        $('#form2')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_mahasiswa/view_makul') ?>/" + id,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('[name="nipd"]').val(data[0].nipd);
                $('[name="nm_pd"]').val(data[0].nm_pd);
                $('[name="analisis_ipk"]').val(data[0].analisis_ipk);
                $('[name="analisis_sks_total"]').val(data[0].analisis_sks_total);
                $('[name="analisis_sks_lulus"]').val(data[0].analisis_sks_lulus);
                $('[name="analisis_sks_tidak_lulus"]').val(data[0].analisis_sks_tidak_lulus);
                $('[name="keterangan"]').val(data[0].keterangan);
                $('[name="makul_tidak_lulus"]').html(data[1]);

                if (data.is_boleh_ajukan_judul == 'Y') {
                    $('[name="is_boleh_ajukan_judul"]').prop("checked", true);
                }

                $('#modal_form_2').modal('show');
                $('.modal-title').text('Analisis Data Mahasiswa');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function replaceCommaLine(data) {
        if (data) {
            let dataToArray = data.split('\r\n').map(item => item.trim());
            return dataToArray.join("\n");
        } else {
            return '';
        }

    }

    function replaces(data) {
        if (data) {
            data.replace(/(?:\r\n|\r|\n)/g, '<br>');
        } else {
            return '';
        }

    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url, method;

        url = "<?= base_url('E_skripsi_daftar_mahasiswa/generate_analisis') ?>";
        method = 'disimpan';

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
                    swalert('Analisis data mahasiswa berhasil digenerate.');
                }
                $('#btnSave').text('Generate Evaluasi Mahasiswa');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);
            }
        });

    }

    function save2() {
        $('#btnSave2').text('saving...');
        $('#btnSave2').attr('disabled', true);
        var url, method;

        url = "<?= base_url('E_skripsi_daftar_mahasiswa/update') ?>";
        method = 'diupdate';

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form2').serialize(),
            dataType: "json",
            success: function(data) {
                console.log(data);
                if (data.status) {
                    $('#modal_form_2').modal('hide');
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
                $('#btnSave2').text('Save');
                $('#btnSave2').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave2').text('Save');
                $('#btnSave2').attr('disabled', false);
            }
        });

        $('#form input').on('keyup', function() {
            $(this).removeClass('is-valid is-invalid');
        });
        $('#form select').on('change', function() {
            $(this).removeClass('is-valid is-invalid');
        });
    }

    function swalert(method) {
        Swal({
            title: 'Success',
            text: method,
            type: 'success'
        });
    };
</script>