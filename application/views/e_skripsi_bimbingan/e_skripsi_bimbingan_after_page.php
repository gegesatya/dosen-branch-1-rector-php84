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

<div class="modal fade" id="modal_strategic_plan" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 10050;">
    <div class="modal-dialog modal-lg" style="width: 1100px; max-width: 95vw;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); border-radius: 16px 16px 0 0; padding: 20px 24px; border-bottom: none; display: flex; justify-content: space-between; align-items: center;">
                <h4 class="modal-title" style="color: white; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; display: flex; align-items: center; gap: 10px; font-size: 18px;">
                    <span style="background: rgba(255,255,255,0.2); padding: 6px 10px; border-radius: 8px;">✨</span> AI Strategic Plan
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8; text-shadow: none; font-size: 24px; margin-top: -2px;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #F9FAFB; max-height: 75vh; overflow-y: auto;">
                
                <!-- State: Loading -->
                <div id="sp_loading" style="display: none; text-align: center; padding: 40px 20px;">
                    <div style="display: inline-block; width: 50px; height: 50px; border: 4px solid #E0E7FF; border-top-color: #4F46E5; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <h4 style="color: #4F46E5; font-weight: 800; margin-top: 20px; font-size: 16px;">AI Sedang Menganalisis Kondisi Bimbingan...</h4>
                    <p style="color: #6B7280; font-size: 13px;">Ini membutuhkan waktu sekitar 10-15 detik. AI sedang mengevaluasi mahasiswa Anda dengan prioritas < 6 Bulan.</p>
                </div>

                <!-- State: Belum Ada Plan -->
                <div id="sp_empty" style="display: none; text-align: center; padding: 40px 20px; background: white; border-radius: 12px; border: 1px dashed #D1D5DB;">
                    <div style="font-size: 40px; margin-bottom: 15px;">📊</div>
                    <h4 style="color: #111827; font-weight: 800; font-size: 16px; margin-bottom: 8px;">Belum Ada Rencana Strategis Bulan Ini</h4>
                    <p style="color: #6B7280; font-size: 13px; max-width: 400px; margin: 0 auto 20px auto; line-height: 1.5;">Hasilkan Strategic Plan bulanan menggunakan AI untuk memetakan kondisi mahasiswa Anda dan mendapatkan rekomendasi percepatan penyelesaian TA.</p>
                    <button class="btn btn-primary" onclick="generateStrategicPlan()" style="background: #4F46E5; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                        ✨ Generate AI Strategic Plan
                    </button>
                </div>

                <!-- State: Result -->
                <div id="sp_result" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <span id="sp_bulan" style="background: #E0E7FF; color: #4F46E5; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 13px;">Bulan: YYYY-MM</span>
                        <button class="btn btn-sm btn-default" onclick="generateStrategicPlan()" style="border-radius: 6px; font-weight: 600; font-size: 12px; background: white; border: 1px solid #D1D5DB; color: #374151;">
                            🔄 Regenerate Plan
                        </button>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <h5 style="color: #111827; font-weight: 800; font-size: 14px; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F3F4F6; padding-bottom: 8px;">
                            <span>👁️</span> Review Kondisi Saat Ini
                        </h5>
                        <div id="sp_review_kondisi" style="font-size: 14.5px; color: #4B5563; line-height: 1.75;"></div>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <h5 style="color: #111827; font-weight: 800; font-size: 14px; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #F3F4F6; padding-bottom: 8px;">
                            <span>📈</span> Evaluasi Progres
                        </h5>
                        <div id="sp_evaluasi" style="font-size: 14.5px; color: #4B5563; line-height: 1.75;"></div>
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; padding: 20px; border-left: 4px solid #10B981; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <h5 style="color: #065F46; font-weight: 800; font-size: 14px; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #ECFDF5; padding-bottom: 8px;">
                            <span>🚀</span> Strategic Plan (Rencana Strategis)
                        </h5>
                        <div id="sp_strategi" style="font-size: 14.5px; color: #374151; line-height: 1.75;"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="padding: 16px 24px; background: #FFFFFF; border-top: 1px solid #E5E7EB; border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px; background: #F3F4F6; border: none; color: #4B5563;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    #modal_strategic_plan ul, #modal_strategic_plan ol { padding-left: 24px; margin-bottom: 14px; }
    #modal_strategic_plan li { margin-bottom: 8px; line-height: 1.75; }
    #modal_strategic_plan p { margin-bottom: 14px; line-height: 1.75; }
    #modal_strategic_plan strong { color: #111827; }
    #modal_strategic_plan table { width: 100%; margin-top: 10px; margin-bottom: 20px; border-collapse: collapse; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    #modal_strategic_plan th, #modal_strategic_plan td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #E5E7EB; }
    #modal_strategic_plan th { background-color: #F3F4F6; color: #111827; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    #modal_strategic_plan td { color: #374151; font-size: 14px; line-height: 1.6; }
    #modal_strategic_plan tr:last-child td { border-bottom: none; }
</style>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
function showStrategicPlanModal() {
    $('#modal_strategic_plan').modal('show');
    loadStrategicPlan();
}

function loadStrategicPlan() {
    $('#sp_loading').hide();
    $('#sp_empty').hide();
    $('#sp_result').hide();
    $('#sp_loading').show();

    $.ajax({
        url: '<?= site_url('e_skripsi_bimbingan/ajax_get_strategic_plan') ?>',
        type: 'GET',
        dataType: 'JSON',
        success: function(data) {
            $('#sp_loading').hide();
            if (data.status && data.data) {
                renderStrategicPlan(data.data);
            } else {
                $('#sp_empty').show();
            }
        },
        error: function() {
            $('#sp_loading').hide();
            $('#sp_empty').show();
            alert('Gagal mengambil data Strategic Plan.');
        }
    });
}

function generateStrategicPlan() {
    $('#sp_empty').hide();
    $('#sp_result').hide();
    $('#sp_loading').show();

    // Pastikan csrf dikirim jika diaktifkan (walaupun ini method GET kadang butuh POST. Kita ubah ke POST untuk aman)
    var postData = {};
    postData['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

    $.ajax({
        url: '<?= site_url('e_skripsi_bimbingan/ajax_generate_ai_strategic_plan') ?>',
        type: 'POST',
        data: postData,
        dataType: 'JSON',
        success: function(data) {
            $('#sp_loading').hide();
            if (data.status && data.data) {
                renderStrategicPlan(data.data);
            } else {
                $('#sp_empty').show();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghubungi API AI',
                    text: data.msg || 'Terjadi kesalahan saat generate plan.',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-cog"></i> Konfigurasi API Key',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#6B7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= site_url('setting_api_key') ?>';
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            $('#sp_loading').hide();
            $('#sp_empty').show();
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Gagal menghubungi server atau waktu permintaan habis (timeout).',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-cog"></i> Periksa Konfigurasi API',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url('setting_api_key') ?>';
                }
            });
        }
    });
}

function renderStrategicPlan(plan) {
    $('#sp_bulan').text('Periode: ' + plan.bulan_tahun);
    $('#sp_review_kondisi').html(marked.parse(plan.review_kondisi));
    $('#sp_strategi').html(marked.parse(plan.strategi_percepatan));
    $('#sp_evaluasi').html(marked.parse(plan.evaluasi_progres));
    
    $('#sp_result').show();
}

function showScoreModal(nm_pd, score, total_bimbingan, current_progress, last_bimbingan_date, tanggal_disetujui) {
    $('#scoreModal_nm_pd').text(nm_pd);
    $('#scoreModal_score').text(score);
    $('#scoreModal_total_bimbingan').text(total_bimbingan + ' kali');
    $('#scoreModal_current_progress').text(current_progress + '%');
    
    var baseScore = 50;
    var freqBoost = Math.min(total_bimbingan * 5, 25);
    var progBoost = Math.min(current_progress * 0.25, 25);
    var penalty = 0;
    
    if (total_bimbingan == 0) {
        var daysApproved = 0;
        if (tanggal_disetujui && tanggal_disetujui != '0000-00-00 00:00:00' && tanggal_disetujui != '0000-00-00') {
            daysApproved = (new Date() - new Date(tanggal_disetujui)) / (1000 * 60 * 60 * 24);
        }
        if (daysApproved > 180) {
            baseScore = 0;
            freqBoost = 0;
            progBoost = 0;
        } else {
            baseScore = 0;
            freqBoost = 0;
            progBoost = 0;
        }
    } else {
        if (last_bimbingan_date && last_bimbingan_date != '0000-00-00') {
            var daysDiff = (new Date() - new Date(last_bimbingan_date)) / (1000 * 60 * 60 * 24);
            if (daysDiff > 14) {
                penalty = 20;
            }
        } else {
            penalty = 20;
        }
    }
    
    $('#scoreModal_base').text('+' + baseScore);
    $('#scoreModal_freq').text('+' + freqBoost);
    $('#scoreModal_prog').text('+' + progBoost);
    $('#scoreModal_penalty').text('-' + penalty);
    
    $('#modalScoreDetail').modal('show');
}
</script>

<div class="modal fade" id="modalScoreDetail" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="width: 90%; max-width: 400px; margin: 15vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div class="modal-header" style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title" style="font-weight: 800; font-size: 15px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 18px;">📈</span> Detail Academic Health Score
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none; transition: all 0.2s;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #F9FAFB;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h3 id="scoreModal_nm_pd" style="font-size: 14px; font-weight: 800; color: #1F2937; margin: 0 0 4px 0; text-transform: uppercase;"></h3>
                    <div style="font-size: 36px; font-weight: 900; color: #4F46E5; line-height: 1;" id="scoreModal_score"></div>
                    <div style="font-size: 11px; color: #6B7280; margin-top: 4px; font-weight: 600;">Skor Kesehatan Akademis</div>
                </div>
                
                <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 16px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 12px;">
                        <span style="font-size: 12.5px; color: #4B5563; font-weight: 600;">Skor Awal (Base Score)</span>
                        <strong style="font-size: 14px; color: #10B981; background: #D1FAE5; padding: 2px 8px; border-radius: 6px;" id="scoreModal_base"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 12px;">
                        <span style="font-size: 12.5px; color: #4B5563; font-weight: 600;">Bonus Bimbingan (<span id="scoreModal_total_bimbingan" style="color:#4F46E5;"></span>)</span>
                        <strong style="font-size: 14px; color: #10B981; background: #D1FAE5; padding: 2px 8px; border-radius: 6px;" id="scoreModal_freq"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 12px;">
                        <span style="font-size: 12.5px; color: #4B5563; font-weight: 600;">Bonus Progress (<span id="scoreModal_current_progress" style="color:#4F46E5;"></span>)</span>
                        <strong style="font-size: 14px; color: #10B981; background: #D1FAE5; padding: 2px 8px; border-radius: 6px;" id="scoreModal_prog"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 12.5px; color: #4B5563; font-weight: 600;">Penalti Kelambatan</span>
                        <strong style="font-size: 14px; color: #EF4444; background: #FEE2E2; padding: 2px 8px; border-radius: 6px;" id="scoreModal_penalty"></strong>
                    </div>
                </div>
                
                <p style="font-size: 11.5px; color: #6B7280; text-align: center; margin: 0; line-height: 1.5;">
                    <i class="fa fa-info-circle" style="color: #4F46E5;"></i> Nilai maksimal adalah 100 poin. Penalti dikenakan jika bimbingan vakum lebih dari 14 hari.
                </p>
            </div>
            <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E5E7EB; padding: 12px 24px; text-align: center; display: flex; justify-content: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 700; padding: 8px 30px; color: #4F46E5; background: #EEF2FF; border: 1px solid #C7D2FE; transition: all 0.2s;">Tutup</button>
            </div>
        </div>
    </div>
</div>