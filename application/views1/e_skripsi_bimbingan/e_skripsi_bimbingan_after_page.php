<script type="text/javascript">
    var save_method;
    var table;
    var _id_judul_skripsi;

    var TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('E_skripsi_bimbingan/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] =
                            '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1],
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
        });

        $('.summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 150,
            dialogsInBody: true,
            toolbar: [
                ['font', ['underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
            ]
        });

        TableAjax.init();
        $("#revisi_judul").hide();

        $("input").change(() => {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(() => {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(() => {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });


        $('#form_balas_pesan').submit((e) => {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_bimbingan/ajax_balas_pesan') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_balas_pesan').serialize(),
                success: (data) => {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        balas_pesan(_id_judul_skripsi);
                        reload_table();
                    }
                }
            });
        });


        $("#modal_form_balas_pesan").on('hide.bs.modal', function() {
            url = "<?php echo site_url('E_skripsi_bimbingan/ajax_save_draft') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_balas_pesan').serialize(),
                success: function(data) {

                }
            });
        });

        $('#form_sempro').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_bimbingan/ajax_save_sempro') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_sempro').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_sempro').modal('hide');
                        reload_table();
                    }
                }
            });
        });

        $('#form_kegiatan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_bimbingan/ajax_approve_kegiatan') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_kegiatan').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_kegiatan').modal('hide');
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });


    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) $(id).show();
        else $(id).hide();
    }

    function balas_pesan(id_judul_skripsi) {
        _id_judul_skripsi = id_judul_skripsi;
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_bimbingan/view_pesan') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#data_pesan').html(data.data_pesan);
                $('#judul').html(data.judul);
                if (data.data_draft_bimbingan) {
                    $('[name="catatan_dosen_wali"]').summernote("code", data.data_draft_bimbingan);
                } else {
                    $('[name="catatan_dosen_wali"]').summernote("code", '');
                }

                $('#nipd').val(data.nipd);
                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("BIMBINGAN " + data.nm_pd);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function get_info(id_judul_skripsi) {
        $('#form_info')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_bimbingan/view_info') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                //alert(data.timeline_data.tanggal_diajukan);
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#nipd').val(data.nipd);
                $('#nm_pd').val(data.nm_pd);
                $('#no_handphone').val(data.no_handphone);
                $('#judul_ditetapkan').val(data.judul_ditetapkan);
                $('#target_selesai').val(data.target_selesai);
                $('#nm_doping_1').val(data.nm_doping_1);
                $('#nm_doping_2').val(data.nm_doping_2);
                $('#tanggal_diajukan').text(data.timeline_data.tanggal_diajukan);
                $('#tanggal_usulan_kaprodi').text(data.timeline_data.tanggal_usulan_kaprodi);
                $('#tanggal_sk').text(data.timeline_data.tanggal_sk);
                $('#tgl_approval_sempro_p1').text(data.timeline_data.tgl_approval_sempro_p1);
                $('#tgl_approval_sempro_p2').text(data.timeline_data.tgl_approval_sempro_p2);
                $('#tanggal_surat_tugas').text(data.timeline_data.tanggal_surat_tugas);
                $('#undangan_tanggal').text(data.timeline_data.undangan_tanggal);

                $('#modal_form_info').modal('show');
                $('.modal-title').text("Info Data Tugas Akhir");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function get_sempro(id_judul_skripsi) {
        $('#form_sempro')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_bimbingan/ajax_edit') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('[name="judul_ditetapkan"]').val(data.judul_ditetapkan);
                $('[name="judul_lama"]').val(data.judul_ditetapkan);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="nipd"]').val(data.nipd);
                $("#revisi_judul").hide();

                /* if (data.status_revisi_judul == 1) {
                    $('[name="status_revisi_judul"]').prop("checked", true);
                } */

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

                if (data.sudah_boleh_maju_prodi == 1) {
                    $('[name="sudah_boleh_maju_prodi"]').prop("checked", true);
                    $('[name="is_boleh_maju_prodi"]').val('1');
                    $('[name="tgl_maju_sempro_prodi"]').val(data.tgl_maju_sempro_prodi);
                }

                if (data.approval_sempro_pembimbing_satu == 1) {
                    $('[name="approval_sempro_pembimbing_satu"]').prop("checked", true);
                    $('[name="is_approval_p1"]').val('1');
                    $('[name="tgl_approval_sempro_p1"]').val(data.tgl_approval_sempro_p1);
                }

                if (data.approval_sempro_pembimbing_dua == 1) {
                    $('[name="approval_sempro_pembimbing_dua"]').prop("checked", true);
                    $('[name="is_approval_p2"]').val('1');
                    $('[name="tgl_approval_sempro_p2"]').val(data.tgl_approval_sempro_p2);
                }

                $('#modal_form_sempro').modal('show');
                $('.modal-title').text("Persetujuan untuk melakukan Seminar Proposal");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function approve_kegiatan(id_judul_skripsi) {
        $('#form_kegiatan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_bimbingan/ajax_edit_bimbingan') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#tanggal_bimbingan').val(data.tanggal_bimbingan);
                $('#jam_bimbingan').val(data.jam_bimbingan);
                $('#tulisan_mahasiswa').val(data.tulisan_mahasiswa);
                $('[name="dosen_pembimbing"]').val(data.dosen_pembimbing);

                $('#modal_kegiatan').modal('show');
                $('.modal-title').text("Resume Kegiatan Bimbingan");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div id="judul"></div>
                <div class="general-item-list" id="data_pesan" style="height: 400px; overflow-y: auto;bottom:0;left:0"></div>
                <div class="form-body">
                    <?php
                    $attributes = ['class' => 'form', 'id' => 'form_balas_pesan'];
                    echo form_open('#', $attributes);
                    ?>
                    <input type="hidden" value="" name="id_judul_skripsi" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Progress (%):</label>
                                <input type="number" min="0" max="100" class="form-control" name="progress_persen" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Status Bimbingan:</label>
                                <select name="status_bimbingan" class="form-control">
                                    <option value="Menunggu Review">Menunggu Review / Pending State</option>
                                    <option value="Perlu Revisi">Perlu Revisi</option>
                                    <option value="Disetujui">Disetujui</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Kegiatan Konsultasi (Catatan Dosen):</label>
                        <div>
                            <textarea class="form-control summernote" rows="3" name="catatan_dosen_wali"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger text-left" data-dismiss="modal">Close</button>
                <?php echo form_submit('btn_submit', 'Save', 'class="btn btn-primary"'); ?>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_info" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body form">
                
                <?php
                $attributes = ['class' => 'form-horizontal form-row-seperated', 'id' => 'form_info'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama</label>
                        <div class="col-md-9">
                            <input type="text" id="nm_pd" name="nm_pd" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input type="text" id="nipd" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Handphone</label>
                        <div class="col-md-9">
                            <input type="text" id="no_handphone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Judul</label>
                        <div class="col-md-9">
                            <textarea id="judul_ditetapkan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Target Selesai</label>
                        <div class="col-md-9">
                            <input type="text" id="target_selesai" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing 1</label>
                        <div class="col-md-9">
                            <input type="text" id="nm_doping_1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembimbing 2</label>
                        <div class="col-md-9">
                            <input type="text" id="nm_doping_2" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php                 ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_sempro" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body form">

                <?php
                $attributes = ['class' => 'form-horizontal form-row-seperated', 'id' => 'form_sempro'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <input type="hidden" value="" name="judul_lama" />
                <input type="hidden" value="0" name="is_boleh_maju_prodi" />
                <input type="hidden" value="0" name="is_approval_p1" />
                <input type="hidden" value="0" name="is_approval_p2" />
                <input type="hidden" value="" name="tgl_approval_sempro_p1" />
                <input type="hidden" value="" name="tgl_approval_sempro_p2" />
                <input type="hidden" value="" name="tgl_maju_sempro_prodi" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama</label>
                        <div class="col-md-9">
                            <input class="form-control" id='nm_pd' name='nm_pd' />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input type="text" id="nipd" name="nipd" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Topik/Judul</label>
                        <div class="col-md-9">
                            <textarea class="form-control" id='judul_ditetapkan' name='judul_ditetapkan' rows="2"></textarea>
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
                    
                    <div class="form-group" id="dosen_satu">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_sempro_pembimbing_satu" id="approval_sempro_pembimbing_satu">Persetujuan Seminar Proposal oleh
                                    Pembimbing Utama</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dosen_dua">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="approval_sempro_pembimbing_dua" id="approval_sempro_pembimbing_dua">Persetujuan Seminar Proposal oleh Pembimbing
                                    Pendamping</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="boleh_sempro">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="sudah_boleh_maju_prodi" id="sudah_boleh_maju_prodi">Sudah boleh mengajukan Seminar Proposal, mahasiswa
                                    sudah bisa melengkapi lampiran persyaratan yang ditentukan oleh Biro
                                    Akademik.</label>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?php
                echo form_submit('btn_submit', 'Save', 'class="btn btn-primary"');
                ?>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_kegiatan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
                <div class="note note-info">
                    <h4 class="block">Petunjuk Pengisian</h4>
                    <p>
                        Setiap mahasiswa yang melakukan bimbingan, harap mencatat resume kegiatan bimbingan yang telah dilakukan. Mohon untuk menggunakan Bahasa Indonesia yang baik dan benar. Dosen Pembimbing akan memberikan persetujuan jika resume kegiatan sudah dianggap sesuai dengan konteks saat melakukan bimbingan.
                    </p>
                </div>

            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal form-row-seperated', 'id' => 'form_kegiatan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Tanggal</label>
                        <div class="col-md-4">
                            <input type="text" name="tanggal_bimbingan" id="tanggal_bimbingan" class="form-control datepicker">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Jam</label>
                        <div class="col-md-4">
                            <input type="text" name="jam_bimbingan" id="jam_bimbingan" class="form-control timepicker">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Progress (%)</label>
                        <div class="col-md-4">
                            <input type="number" name="progress_persen" id="progress_persen" min="0" max="100" class="form-control">
                            <span class="help-block">Estimasi progress TA</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Status Persetujuan</label>
                        <div class="col-md-4">
                            <select name="status_bimbingan" id="status_bimbingan" class="form-control">
                                <option value="Disetujui">Disetujui</option>
                                <option value="Perlu Revisi">Perlu Revisi</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Resume Kegiatan</label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="8" name="tulisan_mahasiswa" id="tulisan_mahasiswa"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Approve', 'class="btn btn-primary"');
                ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>