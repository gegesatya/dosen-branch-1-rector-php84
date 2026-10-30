<style type="text/css">
    /* Anti-Clipping & Right-Aligned Dropdown in DataTable */
    .table-scrollable, .table-responsive, .table-container, .dataTables_wrapper, .portlet-body {
        overflow: visible !important;
    }
    #datatable_ajax tbody tr .btn-group {
        position: relative;
    }
    #datatable_ajax .btn-group.open {
        z-index: 1050 !important;
    }
    #datatable_ajax .btn-group .dropdown-menu {
        z-index: 9999 !important;
        min-width: 205px;
        right: 0 !important;
        left: auto !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2), 0 8px 10px -6px rgba(0,0,0,0.1);
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 0;
    }
    #datatable_ajax .btn-group .dropdown-menu:before {
        position: absolute;
        top: -7px;
        right: 18px !important;
        left: auto !important;
        display: inline-block !important;
        border-right: 7px solid transparent;
        border-bottom: 7px solid #cbd5e1;
        border-left: 7px solid transparent;
        content: '';
    }
    #datatable_ajax .btn-group .dropdown-menu:after {
        position: absolute;
        top: -6px;
        right: 19px !important;
        left: auto !important;
        display: inline-block !important;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #fff;
        border-left: 6px solid transparent;
        content: '';
    }
    #datatable_ajax .btn-group.dropup .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin-bottom: 4px !important;
        margin-top: 0 !important;
        box-shadow: 0 -10px 25px -5px rgba(0,0,0,0.2), 0 -8px 10px -6px rgba(0,0,0,0.1) !important;
    }
    #datatable_ajax .btn-group.dropup .dropdown-menu:before {
        top: auto !important;
        bottom: -7px !important;
        border-top: 7px solid #cbd5e1 !important;
        border-bottom: 0 !important;
    }
    #datatable_ajax .btn-group.dropup .dropdown-menu:after {
        top: auto !important;
        bottom: -6px !important;
        border-top: 6px solid #fff !important;
        border-bottom: 0 !important;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a {
        padding: 7px 14px;
        font-size: 12px;
        color: #334155;
        font-weight: 500;
        display: block;
        clear: both;
        white-space: nowrap;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a i {
        margin-right: 8px;
        width: 14px;
        text-align: center;
    }
    #datatable_ajax th:nth-child(5), #datatable_ajax td:nth-child(5) {
        width: 140px !important;
        min-width: 140px !important;
    }
    #datatable_ajax th:nth-child(6), #datatable_ajax td:nth-child(6) {
        width: 145px !important;
        min-width: 145px !important;
    }
</style>

<script type="text/javascript">
    var save_method;
    var table;

    // Smart Auto-Dropup jika posisi dropdown berada di dekat batas bawah layar
    $(document).on('shown.bs.dropdown', '#datatable_ajax .btn-group', function() {
        var $btnGroup = $(this);
        var $menu = $btnGroup.find('.dropdown-menu');
        var offsetTop = $btnGroup.offset().top;
        var windowScrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();
        var menuHeight = $menu.outerHeight() || 220;
        var spaceBelow = windowHeight - (offsetTop - windowScrollTop + $btnGroup.outerHeight());
        
        if (spaceBelow < menuHeight) {
            $btnGroup.addClass('dropup');
        } else {
            $btnGroup.removeClass('dropup');
        }
    });

    $(document).on('hide.bs.dropdown', '#datatable_ajax .btn-group', function() {
        $(this).removeClass('dropup');
    });

    var TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_rps_new/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4, 5],
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


            url = "<?php echo site_url('C_rps_new/ajax_update') ?>";


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

        $('#form_rancangan_tugas').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);


            url = "<?php echo site_url('C_rps_new/ajax_update_rancangan_tugas') ?>";


            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_rancangan_tugas').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_rancangan_tugas').modal('hide');
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

        $('#form_kontrak').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);


            url = "<?php echo site_url('C_rps_new/ajax_update_kontrak') ?>";


            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_kontrak').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_kontrak').modal('hide');
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
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);

                if (data.id_mk) {
                    if ($('[name="id_mk"] option[value="' + data.id_mk + '"]').length === 0) {
                        var mk_label = (data.kode_mk ? '[' + data.kode_mk + '] ' : '') + (data.nm_mk || 'Mata Kuliah');
                        $('[name="id_mk"]').append($('<option>', {
                            value: data.id_mk,
                            text: mk_label
                        }));
                    }
                    $('[name="id_mk"]').val(data.id_mk);
                } else {
                    $('[name="id_mk"]').val('');
                }

                if (data.koordinator_rps && $('[name="koordinator_rps"] option[value="' + data.koordinator_rps + '"]').length === 0) {
                    $('[name="koordinator_rps"]').append($('<option>', {
                        value: data.koordinator_rps,
                        text: data.koordinator_rps
                    }));
                }
                if (data.kaprodi_rps && $('[name="kaprodi_rps"] option[value="' + data.kaprodi_rps + '"]').length === 0) {
                    $('[name="kaprodi_rps"]').append($('<option>', {
                        value: data.kaprodi_rps,
                        text: data.kaprodi_rps
                    }));
                }

                $('[name="dosen_pengampu"]').val(data.dosen_pengampu);
                $('[name="koordinator_rps"]').val(data.koordinator_rps);
                $('[name="kaprodi_rps"]').val(data.kaprodi_rps);
                $('[name="deskripsi_mata_kuliah"]').summernote("code", data.deskripsi_mata_kuliah);
                $('[name="prasyarat_mata_kuliah"]').summernote("code", data.prasyarat_mata_kuliah);
                $('[name="referensi"]').summernote("code", data.referensi);
                $('[name="cpl"]').summernote("code", data.cpl);
                $('[name="tujuan_belajar"]').summernote("code", data.tujuan_belajar);

                $('#modal_form').modal('show');
                $('.modal-title').text("Edit RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_rancangan_tugas(id) {
        save_method = 'update';
        $('#form_rancangan_tugas')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);
                $('[name="id_mk"]').val(data.id_mk);
                $('[name="koordinator_rps"]').val(data.koordinator_rps);
                $('[name="kaprodi_rps"]').val(data.kaprodi_rps);
                $('[name="tugas_ke"]').val(data.tugas_ke);
                $('[name="tugas_minggu_ke"]').val(data.tugas_minggu_ke);
                $('[name="tujuan_tugas"]').summernote("code", data.tujuan_tugas);
                $('[name="tugas_obyek_garapan"]').summernote("code", data.tugas_obyek_garapan);
                $('[name="tugas_batasan"]').summernote("code", data.tugas_batasan);
                $('[name="tugas_metode"]').summernote("code", data.tugas_metode);
                $('[name="tugas_luaran"]').summernote("code", data.tugas_luaran);
                $('[name="tugas_penilaian_kerapian"]').summernote("code", data.tugas_penilaian_kerapian);
                $('[name="tugas_ketepatan"]').summernote("code", data.tugas_ketepatan);

                $('#modal_form_rancangan_tugas').modal('show');
                $('.modal-title').text("Rancangan Tugas");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_kontrak(id) {
        save_method = 'update';
        $('#form_kontrak')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);
                $('[name="id_mk"]').val(data.id_mk);
                $('[name="koordinator_rps"]').val(data.koordinator_rps);
                $('[name="kaprodi_rps"]').val(data.kaprodi_rps);
                $('[name="manfaat_mata_kuliah"]').summernote("code", data.manfaat_mata_kuliah);
                $('[name="cp_mata_kuliah"]').summernote("code", data.cp_mata_kuliah);
                $('[name="analisis_instruksional"]').summernote("code", data.analisis_instruksional);
                $('[name="strategi_perkuliahan"]').summernote("code", data.strategi_perkuliahan);
                $('[name="materi_bahan_bacaan"]').summernote("code", data.materi_bahan_bacaan);
                $('[name="tugas_tugas"]').summernote("code", data.tugas_tugas);
                $('[name="kontrak_kriteria_penilaian"]').summernote("code", data.kontrak_kriteria_penilaian);
                $('[name="jadwal_perkuliahan"]').summernote("code", data.jadwal_perkuliahan);
                $('[name="tata_kelola_perkuliahan"]').summernote("code", data.tata_kelola_perkuliahan);

                $('#modal_form_kontrak').modal('show');
                $('.modal-title').text("Kontrak Pembelajaran");

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
                url: "<?php echo site_url('C_rps_new/ajax_delete') ?>/" + id,
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
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>/" + id,
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

    function form_upload_gambar(id) {
        save_method = 'upload';
        $('#form_upload_gambar')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_rps"]').val(data.id_rps);
                $('#file_upload').attr('src', data.image_organisasi);

                $('#modal_form_upload_gambar').modal('show');
                $('.modal-title').text("Upload Gambar");

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
            url: "<?php echo site_url('C_rps_new/ajax_edit/') ?>/" + id,
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
                        <label class="col-md-4 control-label">Ketua Program Studi / Wadir 1</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('kaprodi_rps', $dd_kaprodi, '', 'class="form-control" required');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Koordinator RPS</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('koordinator_rps', $dd_koordinator, '', 'class="form-control" required');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Dosen Pengampu</label>
                        <div class="col-md-8">
                            <input name="dosen_pengampu" placeholder="" class="form-control" type="text" required>
                            <span class="help-block"></span>
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
                            <?php
                            echo form_dropdown('id_mk', $dd_mata_kuliah, '', 'class="form-control" required');
                            ?>
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
                        <label class="control-label col-md-4">Prasyarat Mata Kuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="prasyarat_mata_kuliah" name="prasyarat_mata_kuliah" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Daftar Referensi</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="referensi" name="referensi" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="well">
                        <h4>Capaian Pembelajaran</h4>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Capaian Pembelajaran Lulusan <br>yang dibebankan kepada matakuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="cpl" name="cpl" required></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tujuan Belajar (CPMK)</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tujuan_belajar" name="tujuan_belajar" required></textarea>
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
            echo form_open_multipart(site_url("C_rps_new/save_excel"), $attributes);
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

<div class="modal fade" id="modal_form_rancangan_tugas" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_rancangan_tugas'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_rps" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tugas Ke</label>
                        <div class="col-md-8">
                            <input name="tugas_ke" placeholder="" class="form-control" type="text" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Minggu Ke</label>
                        <div class="col-md-8">
                            <input name="tugas_minggu_ke" placeholder="" class="form-control" type="text" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Tujuan Tugas</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tujuan_tugas" name="tujuan_tugas" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Obyek Garapan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_obyek_garapan" name="tugas_obyek_garapan" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Yang harus dikerjakan dan batasan-batasan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_batasan" name="tugas_batasan" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Metode/Cara Pengerjaan, acuan yang digunakan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_metode" name="tugas_metode" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Deskripsi luaran tugas yang dihasilkan/dikerjakan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_luaran" name="tugas_luaran" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Kriteria Penilaian Kerapian Tugas</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_penilaian_kerapian" name="tugas_penilaian_kerapian" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Kriteria Penilaian Ketepatan/kebenaran Tugas</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_ketepatan" name="tugas_ketepatan" required></textarea>
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

<div class="modal fade" id="modal_form_kontrak" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_kontrak'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_rps" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Manfaat Mata Kuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="manfaat_mata_kuliah" name="manfaat_mata_kuliah" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">CP Mata Kuliah</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="cp_mata_kuliah" name="cp_mata_kuliah" required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Analisis Instruksional/Organisasi Materi</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="analisis_instruksional" name="analisis_instruksional" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Strategi Perkuliahan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="strategi_perkuliahan" name="strategi_perkuliahan" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Materi/Bahan Bacaan Perkuliahan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="materi_bahan_bacaan" name="materi_bahan_bacaan" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tugas-tugas</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tugas_tugas" name="tugas_tugas" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Kriteria Penilaian</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kontrak_kriteria_penilaian" name="kontrak_kriteria_penilaian" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Jadwal Perkuliahan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="jadwal_perkuliahan" name="jadwal_perkuliahan" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tata Kelola Perkuliahan</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tata_kelola_perkuliahan" name="tata_kelola_perkuliahan" required></textarea>
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

<div class="modal fade" id="modal_form_upload_gambar" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form_upload_gambar'];
            echo form_open_multipart(site_url("C_rps_new/upload_organisasi_materi"), $attributes);
            ?>
            <div class="modal-body form">
                <input type="hidden" value="" name="id_rps" />
                <div class="form-body">
                    <div class="col-sm-12 col-md-12">
                        <div class="thumbnail">
                            <img src="" id="file_upload" alt="" style="display: block; margin:auto" class="img-responsive">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 control-label col-form-label">Gambar Analisis Instruksional/Organisasi Materi </label>
                        <div class="col-md-8">
                            <input id="file_upload" type="file" data-browse-label="Upload" name="file_upload" data-show-upload="false" data-show-preview="false" class="form-control file" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Upload File', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EXECUTIVE SUMMARY RPS OBE & EVALUASI 16 PERTEMUAN                   -->
<!-- ========================================================================= -->
<!-- Modal Summary RPS OBE & Evaluasi 16 Pertemuan                             -->
<!-- ========================================================================= -->
<!-- <?php $this->load->view('rps_new/modal_summary_rps'); ?> -->
