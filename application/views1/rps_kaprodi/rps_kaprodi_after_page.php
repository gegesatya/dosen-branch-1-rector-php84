<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            var angkatan = $('#ddAngkatan :selected').val();
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_rps_kaprodi/ajax_list') ?>" + "/" + angkatan,
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
                "createdRow": function(row, data, dataIndex) {
                    if (data[5] < 2.00) {
                        $(row).find('td:eq(5)').css('color', 'red');
                    }

                    if (data[9] < 2.00) {
                        $(row).find('td:eq(9)').css('color', 'red');
                    }

                    if (data[13] < 2.00) {
                        $(row).find('td:eq(13)').css('color', 'red');
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

        $('select[name=ddAngkatan]').on('change', function() {
            TableAjax.init();
        });

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

    function edit_evaluasi(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_rps_kaprodi/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_nilai_sempro"]').val(data.nipd);
                $('[name="id_judul_skripsi"]').val(data.id_ptk);
                $('[name="nipd_hidden"]').val(data.nipd);
                $('[name="nim"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="evaluasi_1"]').val(data.evaluasi_1);
                $('[name="evaluasi_2"]').val(data.evaluasi_2);
                $('[name="evaluasi_3"]').val(data.evaluasi_3);
                $('[name="evaluasi_kaprodi"]').val(data.evaluasi_kaprodi);
                $('[name="rekomendasi_1"]').val(data.rekomendasi_1);
                $('[name="rekomendasi_2"]').val(data.rekomendasi_2);
                $('[name="rekomendasi_3"]').val(data.rekomendasi_3);
                $('[name="rekomendasi_kaprodi"]').val(data.rekomendasi_kaprodi);

                $('#tbl_nilai').html(data.tbl_nilai);

                $('#modal_form').modal('show');
                $('.modal-title').text("Evaluasi Keberhasilan Mahasiswa");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function save_evaluasi() {
        $('#btnSave').text('Save');
        $('#btnSave').attr('disabled', false);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('C_rps_kaprodi/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('C_rps_kaprodi/ajax_update') ?>";
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
                    $('#btnSave').text('Save');
                    $('#btnSave').attr('disabled', false);
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }

                $('#btnSave').text('Save');
                $('#btnSave').attr('disabled', false);



            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('Save');
                $('#btnSave').attr('disabled', false);

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
                <input type="hidden" value="" name="nipd_hidden" />
                <input type="hidden" value="" name="id_ptk" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input name="nim" placeholder="NIM" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama</label>
                        <div class="col-md-9">
                            <input name="nm_pd" placeholder="Nama Mahasiswa" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Kemajuan Mahasiswa</label>
                        <div class="col-md-9" id="tbl_nilai">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Review Tahun Pertama</label>
                        <div class="col-md-9">
                            <textarea name="evaluasi_1" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Rekomendasi 1</label>
                        <div class="col-md-9">
                            <input name="rekomendasi_1" readonly class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Review Tahun Kedua</label>
                        <div class="col-md-9">
                            <textarea name="evaluasi_2" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Rekomendasi 2</label>
                        <div class="col-md-9">
                            <input name="rekomendasi_2" readonly class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Review Tahun Ketiga</label>
                        <div class="col-md-9">
                            <textarea name="evaluasi_3" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Rekomendasi 3</label>
                        <div class="col-md-9">
                            <input name="rekomendasi_3" readonly class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="control-label col-md-3">Review Kaprodi</label>
                        <div class="col-md-9">
                            <textarea name="review_kaprodi" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Rekomendasi Kaprodi</label>
                        <div class="col-md-9">
                            <select class="form-control" name="rekomendasi_kaprodi">
                                <option value="Tidak Ada Peringatan">Tidak Ada Peringatan</option>
                                <option value="Surat Peringatan">Surat Peringatan</option>
                                <option value="Surat Peringatan Dengan Saran Pindah atau Dikeluarkan (Drop Out)">Surat Peringatan Dengan Saran Pindah atau Dikeluarkan (Drop Out)</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSave" onclick="save_evaluasi()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>