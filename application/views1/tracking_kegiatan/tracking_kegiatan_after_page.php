<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax;

    TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_tracking_kegiatan/ajax_list') ?>",
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
        // TableAjax.init();
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
            height: 80,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                // ['color', ['color']],
                // ['para', ['ul', 'ol', 'paragraph']],
                ['para', ['ul']],
            ]
        });

        // $('#modalform').on('show.bs.modal', function(e) {
        //     var button = $(e.relatedTarget);
        //     var mode = button.data('mode'); // misalnya tombol punya data-mode="create" / "edit"

        //     if (mode === 'create') {
        //         $('.summernote').summernote('code', '');
        //     }
        // });


        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            if (save_method == 'add') {
                url = "<?php echo site_url('C_tracking_kegiatan/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('C_tracking_kegiatan/ajax_update') ?>";
            }

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
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        }
                    }
                }
            });
        });



    });


    function add_tracking() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('#kegiatan_hari_1').summernote('code', '');
        $('#kegiatan_hari_2').summernote('code', '');
        $('#kegiatan_hari_3').summernote('code', '');
        $('#kegiatan_hari_4').summernote('code', '');
        $('#kegiatan_hari_5').summernote('code', '');
        $('#kegiatan_hari_6').summernote('code', '');
        $('#pengetahuan_baru').summernote('code', '');
        $('#keterampilan_baru').summernote('code', '');
        $('#tujuan_internship').summernote('code', '');
        $('#catatan_lain').summernote('code', '');
        $('.help-block').empty();

        $('#modal_form').modal('show');
        $('.modal-title').text('Ringkasan Laporan Mingguan Internship');
    }

    function edit_tracking(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_tracking_kegiatan/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_tracking"]').val(data.id_tracking);
                $('[name="week"]').val(data.week);
                $('[name="periode_laporan"]').val(data.periode_laporan);
                $('#modal_form').modal('show');
                $('.modal-title').text("Ringkasan Laporan Mingguan Internship");

                $('[name="kegiatan_hari_1"]').summernote("code", data.kegiatan_hari_1);
                $('[name="kegiatan_hari_2"]').summernote("code", data.kegiatan_hari_2);
                $('[name="kegiatan_hari_3"]').summernote("code", data.kegiatan_hari_3);
                $('[name="kegiatan_hari_4"]').summernote("code", data.kegiatan_hari_4);
                $('[name="kegiatan_hari_5"]').summernote("code", data.kegiatan_hari_5);
                $('[name="kegiatan_hari_6"]').summernote("code", data.kegiatan_hari_6);
                $('[name="pengetahuan_baru"]').summernote("code", data.pengetahuan_baru);
                $('[name="keterampilan_baru"]').summernote("code", data.keterampilan_baru);
                $('[name="tujuan_internship"]').summernote("code", data.tujuan_internship);
                $('[name="catatan_lain"]').summernote("code", data.catatan_lain);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function delete_tracking(id) {
        if (confirm('PERINGATAN..!! Anda akan menghapus data Permohonan Surat Keterangan. Apakah ingin melanjutkan?')) {
            var obj = {};
            obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

            $.ajax({
                url: "<?php echo site_url('C_tracking_kegiatan/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                data: obj,
                success: function(data) {
                    reload_table();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });
        }
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }
</script>

<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Form</h4>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_tracking" />

                <div class="form-body">
                    
                    <div class="form-group">
                        <label class="control-label col-md-4">Minggu Ke</label>
                        <div class="col-md-8">
                            <input name="week" placeholder="" class="form-control" type="text" value="">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Periode Laporan</label>
                        <div class="col-md-8">
                            <input name="periode_laporan" placeholder="" class="form-control" type="text" value="">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 1</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_1" name="kegiatan_hari_1"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 2</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_2" name="kegiatan_hari_2"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 3</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_3" name="kegiatan_hari_3"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 4</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_4" name="kegiatan_hari_4"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 5</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_5" name="kegiatan_hari_5"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kegiatan Hari ke 6</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="kegiatan_hari_6" name="kegiatan_hari_6"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Pengetahuan baru yang diperoleh</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="pengetahuan_baru" name="pengetahuan_baru"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Keterampilan baru yang diperoleh</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="keterampilan_baru" name="keterampilan_baru"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tujuan internship yang dicapai</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="tujuan_internship" name="tujuan_internship"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Catatan Lain-lain</label>
                        <div class="col-md-8">
                            <textarea class="form-control summernote" id="catatan_lain" name="catatan_lain"></textarea>
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