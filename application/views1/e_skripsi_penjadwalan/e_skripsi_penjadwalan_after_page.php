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
                    "url": "<?php echo site_url('E_skripsi_penjadwalan/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] =
                            '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5],
                    "orderable": false,
                }],
                "createdRow": function(row, data, dataIndex) {
                    if (data[7] == 1) {
                        $(row).addClass('success');
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


        TableAjax.init();

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


        $('#form_balas_pesan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_penjadwalan/ajax_balas_pesan') ?>";

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


    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) $(id).show();
        else $(id).hide();
    }

    function balas_pesan(uniq) {
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_penjadwalan/view_pesan') ?>/" + uniq,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="uniq"]').val(uniq);
                $('#data_pesan').html(data.data_pesan);
                $('.summernote').summernote('code', '');
                $('#nipd').val(data.nipd);
                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("Diskusi Jadwal Dosen dan Akademik");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function get_info(id_judul_skripsi) {
        $('#form_info')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_penjadwalan/view_info') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#nipd').val(data.nipd);
                $('#nm_pd').val(data.nm_pd);
                $('#no_handphone').val(data.no_handphone);
                $('#judul_ditetapkan').val(data.judul_ditetapkan);
                //$('#nm_doping_1').val(data.nm_doping_1);
                //$('#nm_doping_2').val(data.nm_doping_2);

                $('#modal_form_info').modal('show');
                $('.modal-title').text("Info Data Tugas Akhir");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog" style="width: 800px !important;">
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
                    <input type="hidden" value="" name="uniq" />
                    <div class="form-group">
                        <label class="control-label">Diskusi Jadwal Seminar/Sidang</label>
                        <div>
                            <textarea class="summernote form-control" rows="3" name="catatan_dosen_wali"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>