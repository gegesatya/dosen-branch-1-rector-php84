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
                    "url": "<?php echo site_url('C_jadwal_tunda/ajax_list') ?>",
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
            orientation: "auto",
            todayBtn: true,
            todayHighlight: true,
            forceParse: false
        });

        $('#form_copy').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
            $.ajax({
                url: '<?php echo site_url('C_jadwal_tunda/ajax_generate_bap/') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_copy').serialize(),
                success: function(data) {
                    $('#btnSave').text('Copy');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_copy').modal('hide');
                        reload_table_ajax();
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

    function reload_table_ajax() {
        table.ajax.reload(null, false);
    }



    function ajax_copy(id_rps, id_jadwal) {
        $('#form_copy')[0].reset();
        $('.form-group').removeClass('has-error');

        $.ajax({
            url: "<?php echo site_url('C_jadwal_tunda/get_copy_data') ?>" + "/" + id_rps,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(id_rps);
                $('[name="id_jadwal"]').val(id_jadwal);
                $('[name="general_mk"]').val(data.general_mk);
                $('[name="nm_ptk"]').val(data.nm_ptk);

                $('#modal_form_copy').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Generate BAP");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>


<div class="modal fade" id="modal_form_copy" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_copy'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    <input type="hidden" name="id_rps" />
                    <input type="hidden" name="id_jadwal" />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nama RPS</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='general_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Penyusun</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='nm_ptk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tanggal Mulai Kuliah</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control datepicker" name="tanggal_mulai" required>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>