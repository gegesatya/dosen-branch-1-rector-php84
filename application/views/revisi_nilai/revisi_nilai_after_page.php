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
                    "url": "<?php echo site_url('C_revisi_nilai/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6],
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

        $('#form_memo').submit(function(e) {
            e.preventDefault();
            $('#btnSaveMemo').text('Menyimpan ...');
            $('#btnSaveMemo').attr('disabled', true);

            url = "<?php echo site_url('C_revisi_nilai/ajax_update') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_memo').serialize(),
                success: function(data) {
                    $('#btnSaveMemo').text('Simpan');
                    $('#btnSaveMemo').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_memo').modal('hide');
                        // swalert(method);
                        reload_table();
                    } else {
                        $.each(data.errors, function(key, value) {
                            $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                            $('[name="' + key + '"]').next().text(value);
                        });
                    }
                }
            });
        });


    });


    function edit(id_kls, nipd) {
        save_method = 'update';
        $('#form_memo')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_revisi_nilai/ajax_edit/') ?>/" + id_kls + "/" + nipd,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_kls"]').val(data.id_kls);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="kode_mk"]').val(data.kode_mk);
                $('[name="nm_mk"]').val(data.nm_mk);
                $('[name="nilai_angka_revisi"]').val(data.nilai_angka_revisi);
                $('[name="nilai_huruf_revisi"]').val(data.nilai_huruf_revisi);
                $('[name="nilai_indeks_revisi"]').val(data.nilai_indeks_revisi);
                $('[name="nilai_sebelumnya"]').val(data.nilai_sebelumnya);
                $('[name="dosen_pemberi_nilai"]').val(data.dosen_pemberi_nilai);
                $('[name="alasan_revisi"]').val(data.alasan_revisi);
                $('[name="is_memo_revisi"]').val(data.is_memo_revisi);
                $('#modal_form_memo').modal('show');
                $('.modal-title').text("Memo Revisi Nilai");

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


<div class="modal fade" id="modal_form_memo" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_memo'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_kls" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-4">NIM</label>
                        <div class="col-md-4">
                            <input name="nipd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nama</label>
                        <div class="col-md-8">
                            <input name="nm_pd" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Kode MK</label>
                        <div class="col-md-4">
                            <input name="kode_mk" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nama MK</label>
                        <div class="col-md-8">
                            <input name="nm_mk" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nilai Huruf Sebelum Revisi</label>
                        <div class="col-md-4">
                            <input name="nilai_sebelumnya" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nilai Angka Revisi</label>
                        <div class="col-md-4">
                            <input name="nilai_angka_revisi" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Nilai Huruf Revisi</label>
                        <div class="col-md-4">
                            <input name="nilai_huruf_revisi" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Nilai Indeks Revisi</label>
                        <div class="col-md-4">
                            <input name="nilai_indeks_revisi" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Dosen Pemberi Nilai</label>
                        <div class="col-md-8">
                            <input name="dosen_pemberi_nilai" class="form-control" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Alasan Revisi</label>
                        <div class="col-md-8">
                            <textarea name="alasan_revisi" class="form-control" rows="3" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="control-label col-md-4">Pilih Persetujuan:</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('is_memo_revisi', $dd_memo_revisi, '', 'class="form-control " required');
                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSaveMemo', 'Simpan', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>