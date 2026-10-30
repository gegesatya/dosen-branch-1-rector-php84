<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_daftar_rps_lama/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2],
                    "orderable": false,
                }, ],

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

        $('.summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 150,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
            ]
        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);


            url = "<?php echo site_url('C_daftar_rps_lama/ajax_update') ?>";


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
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').text(data.error_string[i]);
                            $('[name="' + data.inputerror[i] + '"]').addClass('has-error');
                        }
                    }
                }
            });
        });

    });

    function edit_rps(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_daftar_rps_lama/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);
                $('[name="general_mk"]').val(data.general_mk);
                $('[name="koordinator_rps"]').val(data.koordinator_rps);
                $('[name="deskripsi_mata_kuliah"]').summernote("code", data.deskripsi_mata_kuliah);
                $('[name="capaian_pembelajaran_mata_kuliah"]').summernote("code", data.capaian_pembelajaran_mata_kuliah);
                $('[name="referensi"]').summernote("code", data.referensi);
                $('[name="cpl"]').summernote("code", data.cpl);
                $('[name="tujuan_belajar"]').summernote("code", data.tujuan_belajar);
                $('[name="dosen_pengampu"]').summernote("code", data.dosen_pengampu);
                $('[name="tugas_mahasiswa"]').summernote("code", data.tugas_mahasiswa);

                $('#modal_form').modal('show');
                $('.modal-title').text("RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function delete_rps(id) {
        if (confirm('Are you sure delete this data?')) {
            $.ajax({
                url: "<?php echo site_url('C_daftar_rps_lama/ajax_delete') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                data: function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
                success: function(data) {
                    if (data.status) {
                        $('#modal_form').modal('hide');
                        reload_table();
                    } else {
                        alert(data.pesan);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
    }

    function form_upload(id) {
        save_method = 'upload';
        $('#form_upload')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_daftar_rps_lama/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('#modal_form_upload').modal('show');
                $('.modal-title').text("Upload Detail RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function form_mk_prodi(id) {
        save_method = 'upload';
        $('#form_mk_prodi')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_daftar_rps_lama/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('#modal_form_mk_prodi').modal('show');
                $('.modal-title').text("Upload Detail RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_rps" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Koordinator RPS</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('koordinator_rps', $dd_koordinator, '', 'class="form-control" required');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tanggal RPS</label>
                        <div class="col-md-8">
                            <input name="tanggal_rps" placeholder="" class="form-control datepicker" type="text" required>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Mata Kuliah</label>
                        <div class="col-md-8">
                            <input name="general_mk" placeholder="" class="form-control" type="text" required>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Deskripsi Mata Kuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="deskripsi_mata_kuliah" name="deskripsi_mata_kuliah" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Referensi Utama</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="referensi" name="referensi" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="well">
                        <h4>Capaian Pembelajaran</h4>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Capaian Pembelajaran Mata Kuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" required id="capaian_pembelajaran_mata_kuliah" name="capaian_pembelajaran_mata_kuliah"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Capaian Pembelajaran Lulusan <br>yang dibebankan kepada matakuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="cpl" name="cpl" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tujuan Belajar</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tujuan_belajar" name="tujuan_belajar" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tugas Mahasiswa dalam 1 Semester</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_mahasiswa" name="tugas_mahasiswa" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Dosen Pengampu</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="dosen_pengampu" name="dosen_pengampu" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>

            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_upload" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form_upload'];
            echo form_open_multipart(site_url("C_daftar_rps_lama/save_excel"), $attributes);
            ?>
            <div class="modal-body form">
                <input type="hidden" value="" name="id_rps" />
                <div class="form-body">
                    <div class="form-group row">
                        <label class="col-md-3 control-label col-form-label">Upload Excel </label>
                        <div class="col-md-9">
                            <input id="userfile" name="userfile" type="file" data-browse-label="Upload Upload Excel" data-show-upload="false" data-show-preview="false" class="form-control file" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <?php echo form_submit('btn_submit', 'Upload File', 'class="btn btn-primary"'); ?>
            </div>
            </form>
        </div>
    </div>
</div>