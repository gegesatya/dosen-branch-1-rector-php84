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
                    "url": "<?php echo site_url('E_skripsi_daftar_penetapan_judul/ajax_list') ?>" + "/" + angkatan,
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4],
                    "orderable": false,
                }, {
                    "targets": 0,
                    "width": 50,
                }, {
                    "targets": 1,
                    "width": 300,
                }, {
                    "targets": 2,
                    "width": 450,
                }, {
                    "targets": 3,
                    "width": 450,
                }, {
                    "targets": 4,
                    "width": 50,
                }],
                "createdRow": function(row, data, dataIndex) {
                    if (data[6] == 2) {
                        $(row).addClass('warning');
                    } else if (data[6] == 1) {
                        $(row).addClass('danger');
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

        load_pembimbing();
        load_dosen_penguji();

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

        $('#form_balas_pesan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_daftar_penetapan_judul/ajax_balas_pesan') ?>";

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

        $('#form_persetujuan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('E_skripsi_daftar_penetapan_judul/persetujuan_judul') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_persetujuan').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_krs').modal('hide');
                        reload_table();
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

    function copy_a() {
        $('#judul_ditetapkan').val($('#judul_skripsi_1').val());
    }

    function copy_b() {
        $('#judul_ditetapkan').val($('#judul_skripsi_2').val());
    }

    function copy_c() {
        $('#judul_ditetapkan').val($('#judul_skripsi_3').val());
    }

    function load_pembimbing() {
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_penetapan_judul/getDosen/') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="dosen_pembimbing_1"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_pembimbing_1"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_pembimbing_1"]').append(opsi_dosen);
                });

                $('[name="dosen_pembimbing_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_pembimbing_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_pembimbing_2"]').append(opsi_dosen);
                });
            }
        });
    }

    function load_dosen_penguji() {
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_penetapan_judul/getDosenPenguji/') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="dosen_penguji"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_penguji"]').append(opsi_dosen);
                });

                $('[name="dosen_penguji_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_penguji_2"]').append(opsi_dosen);
                });
            }
        });
    }



    function reload_table() {
        table.ajax.reload(null, false);
    }

    /* function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('E_skripsi_daftar_penetapan_judul/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_daftar_penetapan_judul/ajax_update') ?>";
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
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
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
    } */

    function approve_judul(id_judul_skripsi) {
        $('#form_persetujuan')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_daftar_penetapan_judul/view_data_pengajuan') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#nm_pd').val(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#id_smt').val(data.id_smt);
                $('#judul_skripsi_1').val(data.judul_skripsi_1);
                $('#judul_skripsi_2').val(data.judul_skripsi_2);
                $('#judul_skripsi_3').val(data.judul_skripsi_3);
                $('#tanggal_diajukan').val(data.tanggal_diajukan);
                $('#target_selesai').val(data.target_selesai);
                $('#analisis_sks_lulus').val(data.analisis_sks_lulus);
                $('#analisis_sks_tidak_lulus').val(data.analisis_sks_tidak_lulus);
                $('#analisis_sks_total').val(data.analisis_sks_total);
                $('#log_message').html(data.log_message);
                $('#analisis_ipk').val(data.analisis_ipk);
                $('#catatan_dosen_wali').val('');

                $('#judul_ditetapkan').val(data.judul_ditetapkan);
                $('#tanggal_usulan_kaprodi').val(data.tanggal_usulan_kaprodi);
                $('#dosen_pembimbing_1').val(data.dosen_pembimbing_1);
                $('#dosen_pembimbing_2').val(data.dosen_pembimbing_2);
                //$('#dosen_penguji').val(data.dosen_penguji);
                //$('#dosen_penguji_2').val(data.dosen_penguji_2);
                $('#status_penetapan_judul').val(data.status_penetapan_judul);

                $('#modal_form_krs').modal('show');
                $('.modal-title').text("Persetujuan Judul Tugas Akhir");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function balas_pesan(id_judul_skripsi) {
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('E_skripsi_daftar_penetapan_judul/view_pesan') ?>/" + id_judul_skripsi,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_judul_skripsi"]').val(id_judul_skripsi);
                $('#data_pesan').html(data.data_pesan);
                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("Kirim Pesan ke Mahasiswa");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog" style="width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_persetujuan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label col-md-3">Nama:</label>
                                <div class="col-md-9">
                                    <input class="form-control" id='nm_pd' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">NIM:</label>
                                <div class="col-md-9">
                                    <input class="form-control" id='nipd' value='' readonly />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Judul 1:</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id='judul_skripsi_1' rows="2"></textarea>
                                    <span><a href="javascript:void(0)" class='btn btn-xs btn-primary' onclick='copy_a()'>Klik untuk pilih judul</a></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Judul 2:</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id='judul_skripsi_2' rows="2"></textarea>
                                    <span><a href="javascript:void(0)" class='btn btn-xs btn-primary' onclick='copy_b()'>Klik untuk pilih judul</a></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Judul 3:</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id='judul_skripsi_3' rows="2"></textarea>
                                    <span><a href="javascript:void(0)" class='btn btn-xs btn-primary' onclick='copy_c()'>Klik untuk pilih judul</a></span>
                                </div>
                            </div>
                            <div class="note note-info">Berikut ini adalah data yang diusulkan KAPRODI ke Bagian Akademik</div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Judul dipilih:</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" id='judul_ditetapkan' name='judul_ditetapkan' rows="2"></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Tgl Diusulkan Kaprodi</label>
                                <div class="col-md-4">
                                    <input name="tanggal_usulan_kaprodi" class="form-control datepicker" type="text">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><strong>Dosen Pembimbing 1</strong></label>
                                <div class="col-md-9">
                                    <select name="dosen_pembimbing_1" class="form-control">
                                        <option value="">-- Pilh --</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><strong>Dosen Pembimbing 2</strong></label>
                                <div class="col-md-9">
                                    <select name="dosen_pembimbing_2" class="form-control">
                                        <option value="">-- Pilh --</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            

                            <div class="form-group">
                                <label class="control-label col-md-3">Pilih Persetujuan:</label>
                                <div class="col-md-9">
                                    <?php
                                    echo form_dropdown('status_penetapan_judul', $dd_status_penetapan_judul, '', 'class="form-control" ');
                                    ?>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="note note-info">
                                <strong>Data Akademik Mahasiswa</strong>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">IPK:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='analisis_ipk' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">SKS Lulus:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='analisis_sks_lulus' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">SKS Tdk Lulus:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='analisis_sks_tidak_lulus' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">SKS Total:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='analisis_sks_total' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Tahun Ajaran:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='id_smt' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Tgl Pengajuan:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='tanggal_diajukan' value='' readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Target Selesai:</label>
                                <div class="col-md-8">
                                    <input class="form-control" id='target_selesai' value='' readonly />
                                </div>
                            </div>
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

<div class="modal fade in" id="modal_makul" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div id="tabel_makul"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_balas_pesan'];
                echo form_open('#', $attributes);
                ?>

                <div class="modal-body form">
                    <input type="hidden" value="" name="id_judul_skripsi" />

                    <div class="well" id="data_pesan">

                    </div>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label">Tulis Pesan Disini:</label>
                            <div>
                                <textarea class="form-control" rows="3" name="catatan_dosen_wali"></textarea>
                                <span class="help-block"></span>
                            </div>
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