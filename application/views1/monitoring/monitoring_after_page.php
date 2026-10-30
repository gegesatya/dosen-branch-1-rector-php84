<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
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
                    "url": "<?php echo site_url('C_monitoring/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4],
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

    function reload_table_ajax() {
        table.ajax.reload(null, false);
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
                        <label class="col-md-4 control-label">Kode MK</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='kode_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nama MK</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='nm_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Dosen Pengampu</label>
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
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_rps" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_rps'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    
                    <input type="hidden" name="id_jadwal" />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Hari/Jam</label>
                        <div class="col-md-4">
                            <input type="text" class='form-control' name='nama_hari' readonly />
                        </div>
                        <div class="col-md-4">
                            <input type="text" class='form-control' name='jam' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Jadwal</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='tag_name' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Mata Kuliah</label>
                        <div class="col-md-8">
                            <div class='well' name='matkul'></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Pilih RPS</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('ddRps', $dd_rps, '', 'class="form-control input-sm" id="ddRps"');
                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btnSaveRps" class="btn btn-primary">Save</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>