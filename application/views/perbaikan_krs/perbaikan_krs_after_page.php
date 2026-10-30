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
                    "url": "<?php echo site_url('Lecturer_perbaikan_krs/ajax_list') ?>" + "/" + angkatan,
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



    function edit_pengajuan_krs(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_perbaikan_krs/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $.ajax({
                    url: "<?php echo site_url('Lecturer_perbaikan_krs/ajax_get_paket_detail/') ?>/" + data.id_paket_krs,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data) {
                        $('#tabel_paket_detail').html(data.tabel_paket_detail);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });

                $('[name="id_rencana_studi"]').val(data.id_rencana_studi);
                $('[name="registrasi_alamat"]').val(data.registrasi_alamat);
                $('[name="registrasi_no_hp"]').val(data.registrasi_no_hp);
                $('[name="registrasi_no_hp_ortu"]').val(data.registrasi_no_hp_ortu);
                $('[name="registrasi_nominal_spp"]').val(data.registrasi_nominal_spp);
                $('[name="id_dosen_siakad"]').val(data.id_dosen_siakad);
                $('[name="id_paket_krs"]').val(data.id_paket_krs);
                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Pengajuan KRS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }


    function form_approval(id) {
        save_method = 'update';
        $('#form_approval')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_perbaikan_krs/ajax_get_krs_perbaikan/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_perbaikan_krs"]').val(data.id_perbaikan_krs);
                $('[name="nm_sdm"]').val(data.nm_ptk);
                $('[name="total_sks"]').val(data.total_sks);
                $('[name="alasan_perbaikan"]').val(data.alasan_perbaikan);
                $('[name="jumlah_mk_tambah"]').val(data.jumlah_mk_tambah);
                $('[name="jumlah_mk_hapus"]').val(data.jumlah_mk_hapus);

                if (data.cek_val) {
                    $('#modal_form_approval').modal('show');
                    $('.modal-title').text("Persetujuan Perbaikan KRS");
                } else {
                    alert(data);
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }



    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('Lecturer_perbaikan_krs/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('Lecturer_perbaikan_krs/ajax_update') ?>";
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
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);

            }
        });
    }

    function show_krs(id_rencana_studi, nipd) {
        $.ajax({
            url: "<?php echo site_url('Lecturer_perbaikan_krs/view_krs/') ?>/" + id_rencana_studi + "/" + nipd,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#nm_pd').val(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#id_smt').val(data.id_smt);
                $('#tabel_nilai').html(data.tabel);
                $('#modal_form_krs').modal('show');
                $('.modal-title').text("Kartu Rencana Studi / Card Of Study Plan");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>



<div class="modal fade" id="modal_form_approval" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form_approval'];
            echo form_open(site_url("Lecturer_perbaikan_krs/persetujuan_krs_perbaikan"), $attributes);
            ?>
            <div class="modal-body form">
                <input type="hidden" value="" name="id_perbaikan_krs" />
                <div class="form-body">
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Dosen Wali</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="nm_sdm" readonly class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Alasan Perbaikan</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <textarea name="alasan_perbaikan" readonly class="form-control" row=3></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">MK Revisi Tambah</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="jumlah_mk_tambah" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">MK Revisi Hapus</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="jumlah_mk_hapus" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <h4>Petunjuk</h4>
                    <ol>
                        <li>Sebelum menyetujui Perbaikan KRS Mahasiswa, lakukan pengecekan terlebih dahulu terhadap mata kuliah yang ditambah atau dihapus.</li>
                        <li>Cek juga data-data pendukung</li>
                        <li>Jika tidak menyetujui, pastikan Mahasiswa untuk melakukan pengajuan perbaikan ulang</li>
                    </ol>
                    <div class="form-group">
                        <label class="control-label col-md-4">Persetujuan</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('id_status_krs_perbaikan', $dd_status_krs, '', 'class="form-control" required');
                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Submit Perbaikan KRS', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <form class="form-horizontal" role="form">
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Nama:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='nm_pd' value='' readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">NIM:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='nipd' value='' readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Tahun Ajaran:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='id_smt' value='' readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="tabel_nilai">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>