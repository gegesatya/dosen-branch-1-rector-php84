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
                    "url": "<?php echo site_url('C_penugasan_rps/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6, 7],
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

        /*  $("input").change(function() {
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
         }); */

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            if (save_method == 'add') {
                url = "<?php echo site_url('C_penugasan_rps/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('C_penugasan_rps/ajax_update') ?>";
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
            url: "<?php echo site_url('C_penugasan_rps/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(data.id_rps);
                $('[name="kode_revisi"]').val(data.kode_revisi);
                $('[name="tanggal_pengesahan"]').val(data.tanggal_pengesahan);
                $('[name="nomor_dokumen"]').val(data.nomor_dokumen);
                $('[name="id_status_pengajuan_rps"]').val(data.id_status_pengajuan_rps);
                $('#modal_form').modal('show');
                $('.modal-title').text("Pengesahan RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
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
                        <label class="control-label col-md-4">Status Pengesahan</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('id_status_pengajuan_rps', $dd_status_pengesahan, '', 'class="form-control" required');

                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kode Revisi</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('kode_revisi', $dd_kode_revisi, '', 'class="form-control"');

                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nomor Dokumen</label>
                        <div class="col-md-8">
                            <input name="nomor_dokumen" placeholder="" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tanggal Pengesahan</label>
                        <div class="col-md-8">
                            <input name="tanggal_pengesahan" placeholder="" class="form-control datepicker" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>