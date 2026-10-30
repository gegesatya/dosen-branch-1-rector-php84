<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {


            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('E_skripsi_daftar_mahasiswa/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6, 7],
                    "orderable": false,
                }],

            });
        }

        return {
            init: function() {
                handleRecords();
            }
        };
    }();

    $(document).ready(function() {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        TableAjax.init();

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


    });

    function reload_table() {
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
                $('.modal-title').text('Tabel Nilai');
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

                if (data[0].is_boleh_ajukan_judul == 'Y') {
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
                    reload_table();
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
                    reload_table();
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