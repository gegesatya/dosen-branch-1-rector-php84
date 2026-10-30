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
                    "url": "<?php echo site_url('E_skripsi_notifikasi/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4],
                    "orderable": false,
                }],
                "createdRow": function(row, data, dataIndex) {
                    if (data[6] == 1) {
                        $(row).addClass('success');
                    }
                    /* if (data[7] == 1) {
                        $(row).addClass('success');
                    } */
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

        $('#form_balas_pesan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_notifikasi/ajax_balas_pesan') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_balas_pesan').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_balas_pesan').modal('hide');
                        reload_table();
                    }
                }
            });
        });

        $('#form_sempro').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_notifikasi/ajax_save_sempro') ?>";

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
    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function balas_pesan(id_judul_skripsi) {
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_notifikasi/view_pesan') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                //$('#data_pesan').html(data.data_pesan);
                $('#data_pesan').html(data.data_pesan);
                $('#nipd').val(data.nipd);
                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("Diskusi dan Monitoring Proposal");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function get_info(id_judul_skripsi) {
        $('#form_info')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_notifikasi/view_info') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#nipd').val(data.nipd);
                $('#nm_pd').val(data.nm_pd);
                $('#no_handphone').val(data.no_handphone);
                $('#judul_ditetapkan').val(data.judul_ditetapkan);
                $('#target_selesai').val(data.target_selesai);
                $('#nm_doping_1').val(data.nm_doping_1);
                $('#nm_doping_2').val(data.nm_doping_2);
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
            url: "<?php echo site_url('E_skripsi_notifikasi/ajax_edit') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                /* $('#fotokopi_ktm').html(data.fotokopi_ktm);
                $('#fotokopi_krs').html(data.fotokopi_krs);
                $('#transkrip_nilai').html(data.transkrip_nilai);
                $('#kartu_partisipasi').html(data.kartu_partisipasi);
                $('#proposal_tugas_akhir').html(data.proposal_tugas_akhir); */

                /* if (data.sudah_cek_data_sempro == 1) {
                    $('[name="sudah_cek_data_sempro"]').prop("checked", true);
                } */

                if (data.sudah_boleh_maju_prodi == 1) {
                    $('[name="sudah_boleh_maju_prodi"]').prop("checked", true);
                }


                $('#modal_form_sempro').modal('show');
                $('.modal-title').text("Persetujuan untuk melakukan Seminar Proposal");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="general-item-list" id="data_pesan" style="height: 400px; overflow-y: auto;"></div>
                <div class="form-body">
                    <?php
                    $attributes = ['class' => 'form', 'id' => 'form_balas_pesan'];
                    echo form_open('#', $attributes);
                    ?>
                    <input type="hidden" value="" name="id_judul_skripsi" />

                    <div class="form-group">
                        <label class="control-label">Tulis Pesan Disini:</label>
                        <div>
                            <textarea class="form-control" rows="3" name="catatan_dosen_wali"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Kirim', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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
                            <input type="text" id="nm_pd" class="form-control">
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
    <div class="modal-dialog">
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
                <div class="form-body">
                    
                    
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" name="sudah_boleh_maju_prodi" id="sudah_boleh_maju_prodi">Sudah boleh mengajukan Seminar Proposal, mahasiswa sudah bisa melengkapi lampiran persyaratan yang ditentukan oleh Biro Akademik.</label>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <?php
                echo form_submit('btn_submit', 'Simpan', 'class="btn btn-primary"');
                ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>