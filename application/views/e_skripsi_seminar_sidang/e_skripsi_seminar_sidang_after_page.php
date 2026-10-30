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
                    "url": "<?php echo site_url('E_skripsi_seminar_sidang/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5],
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

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $("#revisi_judul").hide();

        $("input, textarea, select").on('change keyup', function() {
            $(this).closest('.form-group').removeClass('has-error');
            $(this).closest('.form-group').find('.help-block').empty();
        });

        $('#form_submit_kaprodi').submit(function(e) {
            e.preventDefault();
            $('#btn_Save').text('Saving...');
            $('#btn_Save').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_seminar_sidang/ajax_submit_kaprodi') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_submit_kaprodi').serialize(),
                success: function(data) {
                    $('#btn_Save').text('Submit');
                    $('#btn_Save').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_submit_kaprodi').modal('hide');
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            var $elem = $('[name="' + data.inputerror[i] + '"]');
                            $elem.closest('.form-group').addClass('has-error');
                            if ($elem.next().hasClass('help-block')) {
                                $elem.next().text(data.error_string[i]);
                            } else if ($elem.parent().next().hasClass('help-block')) {
                                $elem.parent().next().text(data.error_string[i]);
                            } else {
                                $elem.closest('.form-group').find('.help-block').text(data.error_string[i]);
                            }
                        }
                    }
                }
            });
        });
    });

    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) $(id).show();
        else $(id).hide();
    }

    function edit_pengajuan(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="uniq"]').val(data.uniq);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="judul_ditetapkan"]').val(data.judul_ditetapkan);
                $('[name="judul_lama"]').val(data.judul_ditetapkan);
                $('[name="judul_ditetapkan_2"]').val('');
                $('[name="status_revisi_judul"]').prop("checked", false);
                $('[name="kategori_pengajuan"]').val(data.kategori_pengajuan);

                $("#revisi_judul").hide();

                if (data.is_doping_1) {
                    $('#dosen_satu').show();
                } else {
                    $('#dosen_satu').hide();
                }

                if (data.is_doping_2) {
                    $('#dosen_dua').show();
                    $('#ck_revisi_judul').hide();
                    $('#boleh_sempro').hide();
                } else {
                    $('#dosen_dua').hide();
                }

                if (data.approval_p1 == 1) {
                    $('[name="approval_p1"]').prop("checked", true);
                    $('[name="is_approval_p1"]').val('1');
                    $('[name="tgl_approval_p1"]').val(data.tgl_approval_p1);
                }

                if (data.approval_p2 == 1) {
                    $('[name="approval_p2"]').prop("checked", true);
                    $('[name="is_approval_p2"]').val('1');
                    $('[name="tgl_approval_p2"]').val(data.tgl_approval_p2);
                }

                $('#modal_form').modal('show');
                $('.modal-title').text("Persetujuan Seminar/Sidang");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function pengajuan_kaprodi(id) {
        save_method = 'update';
        $('#form_submit_kaprodi')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_sidang/ajax_edit_pengajuan_kaprodi/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="uniq"]').val(data.uniq);
                $('#modal_form_submit_kaprodi').modal('show');
                $('.modal-title').text("Submit ke KAPRODI");

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

        if (save_method == 'add') {
            url = "<?php echo site_url('E_skripsi_seminar_sidang/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_seminar_sidang/ajax_update') ?>";
        }

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
                        var $elem = $('[name="' + data.inputerror[i] + '"]');
                        $elem.closest('.form-group').addClass('has-error');
                        if ($elem.next().hasClass('help-block')) {
                            $elem.next().text(data.error_string[i]);
                        } else if ($elem.parent().next().hasClass('help-block')) {
                            $elem.parent().next().text(data.error_string[i]);
                        } else {
                            $elem.closest('.form-group').find('.help-block').text(data.error_string[i]);
                        }
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

    function delete_pengajuan(id) {
        if (confirm('Are you sure delete this data?')) {
            $.ajax({
                url: "<?php echo site_url('E_skripsi_seminar_sidang/ajax_delete') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                data: function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
                success: function(data) {
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
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
                    <div class="form-group" id="ck_revisi_judul">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" onclick="toggle('#revisi_judul', this)" name="status_revisi_judul" id="status_revisi_judul">Ganti Judul?</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="revisi_judul">
                        <label class="control-label col-md-3">Revisi Topik/Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan_2" rows="2" class="form-control"></textarea>
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
                                <label><input type="checkbox" name="approval_p1" id="approval_p1">Disetujui</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_dua">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_p2" id="approval_p2">Disetujui</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label>Catatan: Jika permohonan sudah disetujui dan tidak ada perubahan judul, bagi Pembimbing Utama silahkan untuk melanjutkan dengan menekan tombol Submit Ke Kaprodi.</label>
                            </div>
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
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
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
                        <select name="pesan_singkat" class="form-control">
                            <option value="">-- Pilh --</option>
                            <option value="Sudah bisa melakukan Seminar Proposal">Sudah bisa melakukan Seminar Proposal</option>
                            <option value="Sudah bisa melakukan Seminar Proposal dengan Revisi Judul">Sudah bisa melakukan Seminar Proposal dengan Revisi Judul</option>
                            <option value="Sudah bisa melakukan Seminar Hasil">Sudah bisa melakukan Seminar Hasil</option>
                            <option value="Sudah bisa melakukan Seminar Hasil dengan Revisi Judul">Sudah bisa melakukan Seminar Hasil dengan Revisi Judul</option>
                            <option value="Sudah bisa melakukan Sidang Tugas Akhir">Sudah bisa melakukan Sidang Tugas Akhir</option>
                        </select>
                    </div>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?php echo form_submit('btn_Save', 'Submit', 'class="btn btn-primary"'); ?>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>