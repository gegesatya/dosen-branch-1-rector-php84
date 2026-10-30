<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            var id_kurikulum_sp = $('[name="id_kurikulum_sp"] :selected').val();
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                'destroy': true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_pengelolaan_rps/ajax_list') ?>" + "/" + id_kurikulum_sp,
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

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('C_pengelolaan_rps/ajax_update') ?>";

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

        $('select[name="id_kurikulum_sp"]').on('change', function() {
            TableAjax.init();
        });

        $("select").change(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });


    });

    function edit_matakuliah(id_kurikulum_sp, id_mk) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_pengelolaan_rps/ajax_edit/') ?>/" + id_kurikulum_sp + "/" + id_mk,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_mk"]').val(data.id_mk);
                $('[name="id_kurikulum_sp"]').val(data.id_kurikulum_sp);
                $('[name="kode_mk"]').val(data.kode_mk);
                $('[name="nm_mk"]').val(data.nm_mk);
                $('[name="sks_tm"]').val(data.sks_tm);
                $('[name="sks_prak"]').val(data.sks_prak);
                $('[name="sks_prak_lap"]').val(data.sks_prak_lap);

                $('#modal_form').modal('show');
                $('.modal-title').text("Pengelolaan RPS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }



    function delete_matakuliah(id) {
        if (confirm('Are you sure delete this data?')) {
            $.ajax({
                url: "<?php echo site_url('C_pengelolaan_rps/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
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
                <input type="hidden" value="" name="id_mk" />
                <input type="hidden" value="" name="id_kurikulum_sp" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-4">Kode Mata Kuliah</label>
                        <div class="col-md-4">
                            <input name="kode_mk" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Mata Kuliah</label>
                        <div class="col-md-8">
                            <input name="nm_mk" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">SKS Tatap Muka</label>
                        <div class="col-md-3">
                            <input name="sks_tm" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">SKS Praktek</label>
                        <div class="col-md-3">
                            <input name="sks_prak" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">SKS Praktek Lapangan</label>
                        <div class="col-md-3">
                            <input name="sks_prak_lap" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-md-4">Pilih RPS</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('id_rps', $dd_rps, '', 'class="form-control"');

                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>