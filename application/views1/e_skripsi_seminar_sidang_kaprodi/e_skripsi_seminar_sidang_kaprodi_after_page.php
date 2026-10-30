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
                    "url": "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_list/' . $index_page) ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6],
                    "orderable": false,
                }, ],
                "createdRow": function(row, data, dataIndex) {
                    if (data[7] == 1) {
                        $(row).addClass('warning');
                    }
                }

            });
        }

        return {
            init: function() {
                handleRecords();
            }

        };

    }();

    $(document).ready(function() {
        load_dosen_penguji();
        load_penguji_sidang();

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $("#revisi_judul").hide();

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

        $('#form_submit_kaprodi').submit(function(e) {
            e.preventDefault();
            $('#btn_Save').text('Saving...');
            $('#btn_Save').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_submit_akademik') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_submit_kaprodi').serialize(),
                success: function(data) {

                    if (data.status) {
                        $('#modal_form_submit_kaprodi').modal('hide');
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }

                    $('#btn_Save').text('Submit');
                    $('#btn_Save').attr('disabled', false);
                }
            });
        });

        $('#form_submit_sidang_kaprodi').submit(function(e) {
            e.preventDefault();
            $('#btn_Save_Sidang').text('Saving...');
            $('#btn_Save_Sidang').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_submit_sidang_akademik') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_submit_sidang_kaprodi').serialize(),
                success: function(data) {
                    if (data.status) {
                        $('#modal_form_submit_sidang_kaprodi').modal('hide');
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }

                    $('#btn_Save_Sidang').text('Submit');
                    $('#btn_Save_Sidang').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_submit_sidang_kaprodi').modal('hide');
                        reload_table();
                    }
                }
            });
        });
    });

    function load_dosen_penguji() {
        $.ajax({
            url: "<?= base_url('E_skripsi_seminar_sidang_kaprodi/getDosenPenguji/') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="dosen_penguji_sempro_1"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji_sempro_1"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="dosen_penguji_sempro_1"]').append(opsi_dosen);
                });

                $('[name="dosen_penguji_sempro_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji_sempro_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="dosen_penguji_sempro_2"]').append(opsi_dosen);
                });

                $('[name="dosen_penguji_sempro_3"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji_sempro_3"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="dosen_penguji_sempro_3"]').append(opsi_dosen);
                });
            }
        });
    }

    function load_penguji_sidang() {
        $.ajax({
            url: "<?= base_url('E_skripsi_seminar_sidang_kaprodi/getDosenPenguji/') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="penguji_sidang_1"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="penguji_sidang_1"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="penguji_sidang_1"]').append(opsi_dosen);
                });

                $('[name="penguji_sidang_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="penguji_sidang_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="penguji_sidang_2"]').append(opsi_dosen);
                });

                $('[name="penguji_sidang_3"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="penguji_sidang_3"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nama_lengkap}</option>`;
                    $('[name="penguji_sidang_3"]').append(opsi_dosen);
                });
            }
        });
    }

    function edit_penguji(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="uniq"]').val(data.uniq);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="judul_ditetapkan"]').val(data.judul_ditetapkan);
                $('[name="kategori_pengajuan"]').val(data.kategori_pengajuan);
                $('[name="nm_pembimbing_utama"]').val(data.nm_pembimbing_utama);
                $('[name="nm_pembimbing_pendamping"]').val(data.nm_pembimbing_pendamping);

                if (data.dosen_penguji_sempro_1) {
                    $('[name="dosen_penguji_sempro_1"]').val(data.dosen_penguji_sempro_1);
                } else {
                    $('[name="dosen_penguji_sempro_1"]').val(data.dosen_pembimbing_1);
                }

                if (data.dosen_penguji_sempro_2) {
                    $('[name="dosen_penguji_sempro_2"]').val(data.dosen_penguji_sempro_2);
                } else {
                    $('[name="dosen_penguji_sempro_2"]').val(data.dosen_pembimbing_2);
                }

                $('[name="dosen_penguji_sempro_3"]').val(data.dosen_penguji_sempro_3);

                $("#revisi_judul").hide();

                if (data.approval_p1 == 1) {
                    $('[name="approval_p1"]').prop("checked", true);
                }

                if (data.approval_p2 == 1) {
                    $('[name="approval_p2"]').prop("checked", true);
                }

                $('#modal_form').modal('show');
                $('.modal-title').text("Penetapan Penguji oleh Kaprodi");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_penguji_1(id) {
        save_method = 'update';
        $('#form_1')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="uniq"]').val(data.uniq);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="judul_ditetapkan"]').val(data.judul_ditetapkan);
                $('[name="kategori_pengajuan"]').val(data.kategori_pengajuan);
                $('[name="nm_pembimbing_utama"]').val(data.nm_pembimbing_utama);
                $('[name="nm_pembimbing_pendamping"]').val(data.nm_pembimbing_pendamping);

                $('[name="dosen_penguji_sempro_1"]').val(data.dosen_penguji_sempro_1);



                $("#revisi_judul").hide();

                if (data.approval_p1 == 1) {
                    $('[name="approval_p1"]').prop("checked", true);
                }

                if (data.approval_p2 == 1) {
                    $('[name="approval_p2"]').prop("checked", true);
                }

                $('#modal_form_1').modal('show');
                $('.modal-title').text("Penetapan Penguji oleh Kaprodi");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function disposisi_akademik(id) {

        save_method = 'update';
        $('#form_submit_kaprodi')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_edit_pengajuan_akademik/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="uniq"]').val(data.uniq);
                $('#modal_form_submit_kaprodi').modal('show');
                $('.modal-title').text("Submit Data ke Akademik");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function disposisi_sidang_ke_akademik(id) {
        save_method = 'update';
        $('#form_submit_sidang_kaprodi')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                /*  let penguji_sidang_2;
                 let penguji_sidang_3;

                 if (data.dosen_pembimbing_2 != '') {
                     penguji_sidang_2 = data.dosen_pembimbing_2;
                     penguji_sidang_3 = data.dosen_penguji_sempro_1;
                 } else {
                     penguji_sidang_2 = data.dosen_penguji_sempro_1;
                     penguji_sidang_3 = data.dosen_penguji_sempro_2;
                 } */

                $('[name="uniq"]').val(data.uniq);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="judul_ditetapkan"]').val(data.judul_ditetapkan);
                $('[name="kategori_pengajuan"]').val(data.kategori_pengajuan);
                $('#nm_pembimbing_utama').val(data.nm_pembimbing_utama);
                $('#nm_pembimbing_pendamping').val(data.nm_pembimbing_pendamping);
                $('#nm_penguji_1').val(data.nm_penguji_1);
                $('#nm_penguji_2').val(data.nm_penguji_2);
                $('[name="penguji_sidang_1"]').val(data.dosen_penguji_sempro_1);
                $('[name="penguji_sidang_2"]').val(data.dosen_penguji_sempro_2);
                $('[name="penguji_sidang_3"]').val(data.dosen_penguji_sempro_3);

                $('#modal_form_submit_sidang_kaprodi').modal('show');
                $('.modal-title').text("Submit Pengajuan Sidang ke Akademik");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }



    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        url = "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_save_penguji') ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
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
    }

    function save_1() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        url = "<?php echo site_url('E_skripsi_seminar_sidang_kaprodi/ajax_save_penguji') ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_1').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form_1').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
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
    }

    function view_petunjuk() {
        $('#modal_form_petunjuk').modal('show');
        $('.modal-title').text("Petunjuk Pengajuan dan Persetujuan");
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
                <input type="hidden" value="" name="id_judul_skripsi" />
                <input type="hidden" value="" name="uniq" />
                <input type="hidden" value="" name="judul_lama" />
                <input type="hidden" value="0" name="is_approval_p1" />
                <input type="hidden" value="0" name="is_approval_p2" />
                <input type="hidden" value="" name="tgl_approval_p1" />
                <input type="hidden" value="" name="tgl_approval_p2" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input name="nipd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama</label>
                        <div class="col-md-9">
                            <input name="nm_pd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan" rows="2" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing Utama</label>
                        <div class="col-md-9">
                            <input name="nm_pembimbing_utama" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing Pendamping</label>
                        <div class="col-md-9">
                            <input name="nm_pembimbing_pendamping" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pengajuan</label>
                        <div class="col-md-9">
                            <select name="kategori_pengajuan" class="form-control" readonly>
                                <option value="">-- Pilh --</option>
                                <option value="1">Seminar Proposal</option>
                                <option value="2">Seminar Hasil</option>
                                <option value="3">Sidang Tugas Akhir</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_satu">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_p1" id="approval_p1">Sudah Disetujui oleh Pembimbing Utama</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_dua">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_p2" id="approval_p2">Sudah Disetujui oleh Pembimbing Pendamping</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji 1</label>
                        <div class="col-md-9">
                            <select name="dosen_penguji_sempro_1" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji 2</label>
                        <div class="col-md-9">
                            <select name="dosen_penguji_sempro_2" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji 3</label>
                        <div class="col-md-9">
                            <select name="dosen_penguji_sempro_3" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_1'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <input type="hidden" value="" name="uniq" />
                <input type="hidden" value="" name="judul_lama" />
                <input type="hidden" value="0" name="is_approval_p1" />
                <input type="hidden" value="0" name="is_approval_p2" />
                <input type="hidden" value="" name="tgl_approval_p1" />
                <input type="hidden" value="" name="tgl_approval_p2" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-4">NIM</label>
                        <div class="col-md-8">
                            <input name="nipd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nama</label>
                        <div class="col-md-8">
                            <input name="nm_pd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Judul</label>
                        <div class="col-md-8">
                            <textarea name="judul_ditetapkan" rows="2" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Pembimbing Utama</label>
                        <div class="col-md-8">
                            <input name="nm_pembimbing_utama" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Pembimbing Pendamping/Penguji 1</label>
                        <div class="col-md-8">
                            <input name="nm_pembimbing_pendamping" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Pengajuan</label>
                        <div class="col-md-8">
                            <select name="kategori_pengajuan" class="form-control" readonly>
                                <option value="">-- Pilh --</option>
                                <option value="1">Seminar Proposal</option>
                                <option value="2">Seminar Hasil</option>
                                <option value="3">Sidang Tugas Akhir</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_satu">
                        <div class="col-md-8 col-md-offset-4">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_p1" id="approval_p1">Sudah Disetujui oleh Pembimbing Utama</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_dua">
                        <div class="col-md-8 col-md-offset-4">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_p2" id="approval_p2">Sudah Disetujui oleh Pembimbing Pendamping</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Penguji 2</label>
                        <div class="col-md-8">
                            <select name="dosen_penguji_sempro_1" class="form-control">
                                <option value="">-- Pilh --</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSave" onclick="save_1()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_petunjuk" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Pengajuan Seminar Proposal/Seminar Hasil/Sidang Tugas Akhir, diajukan oleh mahasiswa</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Setelah Mahasiswa melakukan pengajuan, Dosen Pembimbing Utama dan Dosen Pembimbing Pendamping (jika ada), melakukan persetujuan</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Dosen Pembimbing Utama meneruskan pengajuan ke KAPRODI untuk penetapan jadwal Seminar/Sidang. Selain itu, KAPRODI juga menentukan Dosen Penguji untuk Seminar Proposal dan Seminar Hasil</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Jika pengajuan mahasiswa adalah pengajuan Ujian Tugas Akhir, maka KAPRODI menetapkan Penguji 1, Penguji 2 dan Penguji 3</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Mahasiswa dapat mengajukan 3 tipe pengajuan, yaitu Seminar Proposal, Seminar Hasil dan Sidang/Ujian Tugas Akhir. Masing-masing proses diketahui oleh Dosen Pembimbing</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Proses pengajuan bersifat berurutan. Proses urutan yaitu Seminar Proposal, Seminar Hasil dan Sidang/Ujian Tugas Akhir</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>Setelah proses di KAPRODI, selanjutnya KAPRODI melanjutkan proses ke Bagian Akademik untuk penetapan Jadwal, penerbitan Surat Tugas dan Surat Undangan</td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_submit_kaprodi" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="form-body">
                    <?php
                    $attributes = ['class' => 'form', 'id' => 'form_submit_kaprodi'];
                    echo form_open('#', $attributes);
                    ?>
                    <input type="hidden" value="" name="uniq" />
                    <div class="form-group">
                        <label class="control-label">Pesan Singkat</label>
                        <select name="pesan_singkat_kaprodi" class="form-control">
                            <option value="">-- Pilh --</option>
                            <option value="Mohon untuk dibuatkan Surat Tugas Untuk Penguji">Mohon untuk dibuatkan Surat Tugas Untuk Penguji</option>
                            <option value="Mohon untuk dibuatkan Surat Undangan Seminar Hasil">Mohon untuk dibuatkan Surat Undangan Seminar Hasil</option>
                            <option value="Mohon untuk dibuatkan Surat Undangan Sidang Tugas Akhir">Mohon untuk dibuatkan Surat Undangan Sidang Tugas Akhir</option>
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?php echo form_submit('btn_Save', 'Save', 'class="btn btn-primary"'); ?>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_submit_sidang_kaprodi" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="form-body">
                    <?php
                    $attributes = ['class' => 'form-horizontal', 'id' => 'form_submit_sidang_kaprodi'];
                    echo form_open('#', $attributes);
                    ?>
                    <input type="hidden" value="" name="uniq" />

                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input name="nipd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama</label>
                        <div class="col-md-9">
                            <input name="nm_pd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan" rows="2" class="form-control" readonly></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing Utama</label>
                        <div class="col-md-9">
                            <input id="nm_pembimbing_utama" class="form-control" type="text" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing Pendamping</label>
                        <div class="col-md-9">
                            <input id="nm_pembimbing_pendamping" class="form-control" type="text" readonly>
                        </div>
                    </div>
                    


                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji Sidang 1</label>
                        <div class="col-md-9">
                            <select name="penguji_sidang_1" class="form-control"></select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji Sidang 2</label>
                        <div class="col-md-9">
                            <select name="penguji_sidang_2" class="form-control"></select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Penguji Sidang 3</label>
                        <div class="col-md-9">
                            <select name="penguji_sidang_3" class="form-control"></select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pesan Singkat</label>
                        <div class="col-md-9">
                            <select name="pesan_singkat_kaprodi" class="form-control">
                                <option value="">-- Pilh --</option>
                                <option value="Mohon untuk dibuatkan Surat Tugas Untuk Penguji">Mohon untuk dibuatkan Surat Tugas Untuk Penguji</option>
                                <option value="Mohon untuk dibuatkan Surat Undangan Seminar Hasil">Mohon untuk dibuatkan Surat Undangan Seminar Hasil</option>
                                <option value="Mohon untuk dibuatkan Surat Undangan Sidang Tugas Akhir">Mohon untuk dibuatkan Surat Undangan Sidang Tugas Akhir</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?php echo form_submit('btn_Save_Sidang', 'Save', 'class="btn btn-primary"'); ?>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>