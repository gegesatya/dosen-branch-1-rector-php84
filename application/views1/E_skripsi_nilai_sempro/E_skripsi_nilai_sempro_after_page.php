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
                    "url": "<?php echo site_url('E_skripsi_nilai_sempro/ajax_list') ?>",
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
                "createdRow": function(row, data, dataIndex) {
                    if (data[7] == 0) {
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
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $("#revisi_judul").hide();

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

        $('[name="nilai_komponen_a"]').on('input', function(e) {
            var a = Math.round(Number($('[name="nilai_komponen_a"]').val()) * 0.25 * 100) / 100;
            $('#n1').text(a);
        });

        $('[name="nilai_komponen_b"]').on('input', function(e) {
            var a = Math.round(Number($('[name="nilai_komponen_b"]').val()) * 0.2 * 100) / 100;
            $('#n2').text(a);
        });

        $('[name="nilai_komponen_c"]').on('input', function(e) {
            var a = Math.round(Number($('[name="nilai_komponen_c"]').val()) * 0.25 * 100) / 100;
            $('#n3').text(a);
        });

        $('[name="nilai_komponen_d"]').on('input', function(e) {
            var a = Math.round(Number($('[name="nilai_komponen_d"]').val()) * 0.15 * 100) / 100;
            $('#n4').text(a);
        });

        $('[name="nilai_komponen_e"]').on('input', function(e) {
            var a = Math.round(Number($('[name="nilai_komponen_e"]').val()) * 0.15 * 100) / 100;
            $('#n5').text(a);
        });

        $('.bangke').on('input', function(e) {
            var total = Number($('#n1').text()) + Number($('#n2').text()) + Number($('#n3').text()) + Number($('#n4').text()) + Number($('#n5').text());
            total = Math.round(total * 100) / 100;
            $('#total').val(total);
        });

        $('[name="seminar_nim_moderator"]').on('input', function(e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function(responseData) {
                if (responseData) {
                    $('[name="seminar_moderator"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_moderator"]').val('');
                }
            }).fail(function() {
                $('[name="seminar_moderator"]').val('');
            });
        });

        $('[name="seminar_nim_pembahas_1"]').on('input', function(e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function(responseData) {
                if (responseData) {
                    $('[name="seminar_pembahas_1"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_pembahas_1"]').val('');
                }
            }).fail(function() {
                $('[name="seminar_pembahas_1"]').val('');
            });
        });

        $('[name="seminar_nim_pembahas_2"]').on('input', function(e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function(responseData) {
                if (responseData) {
                    $('[name="seminar_pembahas_2"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_pembahas_2"]').val('');
                }


            }).fail(function() {
                $('[name="seminar_pembahas_2"]').val('');
            });
        });

    });

    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) $(id).show();
        else $(id).hide();
    }

    function edit_nilai_sempro(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_nilai_sempro/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="nilai_komponen_a"]').val(data.nilai_komponen_a);
                $('[name="nilai_komponen_b"]').val(data.nilai_komponen_b);
                $('[name="nilai_komponen_c"]').val(data.nilai_komponen_c);
                $('[name="nilai_komponen_d"]').val(data.nilai_komponen_d);
                $('[name="nilai_komponen_e"]').val(data.nilai_komponen_e);

                var a = Math.round(data.nilai_komponen_a * 0.25 * 100) / 100;
                var b = Math.round(data.nilai_komponen_b * 0.2 * 100) / 100;
                var c = Math.round(data.nilai_komponen_c * 0.25 * 100) / 100;
                var d = Math.round(data.nilai_komponen_d * 0.15 * 100) / 100;
                var e = Math.round(data.nilai_komponen_e * 0.15 * 100) / 100;

                $('#n1').text(a);
                $('#n2').text(b);
                $('#n3').text(c);
                $('#n4').text(d);
                $('#n5').text(e);

                var total = Number(a) + Number(b) + Number(c) + Number(d) + Number(e);
                total = Math.round(total * 100) / 100;

                $('#total').val(total);

                $('#modal_form').modal('show');
                $('.modal-title').text("Nilai Seminar Proposal");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_catatan(id) {
        save_method = 'update';
        $('#form_catatan')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_nilai_sempro/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="catatan_judul"]').val(data.catatan_judul);
                $('[name="catatan_latar_belakang"]').val(data.catatan_latar_belakang);
                $('[name="catatan_rumusan_masalah"]').val(data.catatan_rumusan_masalah);
                $('[name="catatan_tujuan_penelitian"]').val(data.catatan_tujuan_penelitian);
                $('[name="catatan_tinjauan_pustaka"]').val(data.catatan_tinjauan_pustaka);
                $('[name="catatan_metode_penelitian"]').val(data.catatan_metode_penelitian);
                $('[name="catatan_daftar_pustaka"]').val(data.catatan_daftar_pustaka);
                $('[name="catatan_format_penulisan"]').val(data.catatan_format_penulisan);
                //$('[name="catatan_usulan"]').val(data.catatan_usulan);

                $('#modal_form_catatan').modal('show');
                $('.modal-title').text("Usulan Revisi");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_berita_acara(id) {
        save_method = 'update';
        $('#form_berita_acara')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_nilai_sempro/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#revisi_judul').hide();
                $('#revisi_judul').val();
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="judul_ditetapkan_1"]').val(data.judul_ditetapkan);
                $('[name="judul_ditetapkan_2"]').val(data.judul_ditetapkan);
                $('[name="seminar_hari"]').val(data.seminar_hari);
                $('[name="seminar_tanggal"]').val(data.seminar_tanggal);
                $('[name="seminar_moderator"]').val(data.seminar_moderator);
                $('[name="seminar_nim_moderator"]').val(data.seminar_nim_moderator);
                $('[name="seminar_pembahas_1"]').val(data.seminar_pembahas_1);
                $('[name="seminar_nim_pembahas_1"]').val(data.seminar_nim_pembahas_1);
                $('[name="seminar_pembahas_2"]').val(data.seminar_pembahas_2);
                $('[name="seminar_nim_pembahas_2"]').val(data.seminar_nim_pembahas_2);
                $('[name="seminar_jumlah_mahasiswa"]').val(data.seminar_jumlah_mahasiswa);
                $('[name="seminar_tempat"]').val(data.seminar_tempat);
                $('[name="seminar_jam_mulai"]').val(data.seminar_jam_mulai);
                $('[name="seminar_jam_selesai"]').val(data.seminar_jam_selesai);
                $('[name="sempro_hasil"]').val(data.sempro_hasil);
                $('#tbl_nilai').html(data.tbl_nilai);

                $('#modal_form_berita_acara').modal('show');
                $('.modal-title').text("Berita Acara Seminar");

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
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_update') ?>";
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

    function save_catatan() {
        $('#btnSaveCatatan').text('saving...');
        $('#btnSaveCatatan').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_update_catatan') ?>";
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_catatan').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form_catatan').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSaveCatatan').text('save');
                $('#btnSaveCatatan').attr('disabled', false);


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSaveCatatan').text('save');
                $('#btnSaveCatatan').attr('disabled', false);

            }
        });
    }

    function save_berita_acara() {
        $('#btnSaveBeritaAcara').text('saving...');
        $('#btnSaveBeritaAcara').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_nilai_sempro/ajax_update_berita_acara') ?>";
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_berita_acara').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form_berita_acara').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSaveBeritaAcara').text('save');
                $('#btnSaveBeritaAcara').attr('disabled', false);


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSaveBeritaAcara').text('save');
                $('#btnSaveBeritaAcara').attr('disabled', false);

            }
        });
    }

    function view_rubrik() {
        $('#modal_form_rubrik').modal('show');
        $('.modal-title').text("Rubrik Penilaian Seminar Proposal");
    }
</script>

<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
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
                <input type="hidden" value="" name="id_nilai_sempro" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-5">NIM</label>
                        <div class="col-md-4">
                            <input name="nipd" placeholder="NIM" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Nama</label>
                        <div class="col-md-7">
                            <input name="nm_pd" placeholder="Nama Mahasiswa" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Latar Belakang (25%)</label>
                        <div class="col-md-4">
                            <input name="nilai_komponen_a" class="form-control bangke" type="number" step=1 min=1 max=7 onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div name="n1" id="n1" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Tinjauan Pustaka (20%)</label>
                        <div class="col-md-4">
                            <input name="nilai_komponen_b" class="form-control bangke" type="number" step=1 min=1 max=7 onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            
                            <div name="n2" id="n2" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Kerangka Konseptual (25%)</label>
                        <div class="col-md-4">
                            <input name="nilai_komponen_c" class="form-control bangke" type="number" step=1 min=1 max=7 onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            
                            <div name="n3" id="n3" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Metode Penelitian (15%)</label>
                        <div class="col-md-4">
                            <input name="nilai_komponen_d" class="form-control bangke" type="number" step=1 min=1 max=7 onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            
                            <div name="n4" id="n4" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Kemampuan Menjelaskan (15%)</label>
                        <div class="col-md-4">
                            <input name="nilai_komponen_e" class="form-control bangke" type="number" step=1 min=1 max=7 onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            
                            <div name="n5" id="n5" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">TOTAL</label>
                        
                        <div class="col-md-3 col-md-offset-4">
                            <input name="total" id="total" class="form-control text-center" type="text">

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="well">
                                <p>Keterangan:</p>
                                <p>Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 = baik, 7 = sangat baik)</p>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_catatan" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_catatan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_nilai_sempro" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input name="nipd" placeholder="NIM" class="form-control" type="text">
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
                        <label class="control-label col-md-3">A. Judul</label>
                        <div class="col-md-9">
                            <textarea name="catatan_judul" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">B. Latar Belakang</label>
                        <div class="col-md-9">
                            <textarea name="catatan_latar_belakang" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">C. Perumusan Masalah</label>
                        <div class="col-md-9">
                            <textarea name="catatan_rumusan_masalah" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">D. Tujuan dan Manfaat Penelitian</label>
                        <div class="col-md-9">
                            <textarea name="catatan_tujuan_penelitian" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">E. Tinjauan Pustaka</label>
                        <div class="col-md-9">
                            <textarea name="catatan_tinjauan_pustaka" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">F. Metode Penelitian</label>
                        <div class="col-md-9">
                            <textarea name="catatan_metode_penelitian" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">G. Daftar Pustaka</label>
                        <div class="col-md-9">
                            <textarea name="catatan_daftar_pustaka" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">H. Format Penulisan</label>
                        <div class="col-md-9">
                            <textarea name="catatan_format_penulisan" rows="5" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSaveCatatan" onclick="save_catatan()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_berita_acara" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_berita_acara'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_nilai_sempro" />
                <input type="hidden" value="" name="id_judul_skripsi" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">NIM</label>
                        <div class="col-md-9">
                            <input name="nipd" placeholder="NIM" class="form-control" type="text">
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
                        <label class="control-label col-md-3">Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan_1" rows="2" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" onclick="toggle('#revisi_judul', this)" name="status_revisi_judul" id="status_revisi_judul">Ganti Judul?</label>
                                <p>Klik pada checkbox ganti judul untuk mengganti judul. Kemudian masukkan judul yang baru pada Revisi Judul</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="revisi_judul">
                        <label class="control-label col-md-3">Revisi Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan_2" rows="2" class="form-control"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Hari/Tanggal</label>
                        <div class="col-md-3">
                            <input name="seminar_hari" placeholder="Hari" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <input name="seminar_tanggal" placeholder="Tanggal" class="form-control datepicker" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Jam </label>
                        <div class="col-md-3">
                            <input id="jam_mulai" name="seminar_jam_mulai" placeholder="hh:mm" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <input id="jam_selesai" name="seminar_jam_selesai" placeholder="hh:mm" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Tempat</label>
                        <div class="col-md-9">
                            <input name="seminar_tempat" placeholder="Tempat Seminar" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai</label>
                        <div class="col-md-9" id="tbl_nilai">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Moderator/NIM</label>
                        <div class="col-md-3">
                            <input name="seminar_nim_moderator" id="seminar_nim_moderator" placeholder="NIM Moderator" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_moderator" id="seminar_moderator" placeholder="Nama Moderator" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas/NIM</label>
                        <div class="col-md-3">
                            <input name="seminar_nim_pembahas_1" placeholder="NIM Pembahas" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_pembahas_1" placeholder="Nama Pembahas" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas/NIM</label>
                        <div class="col-md-3">
                            <input name="seminar_nim_pembahas_2" placeholder="NIM Pembahas" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_pembahas_2" placeholder="Nama Pembahas" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Jumlah Peserta</label>
                        <div class="col-md-3">
                            <input name="seminar_jumlah_mahasiswa" placeholder="Jumlah Mahasiswa" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Status</label>
                        <div class="col-md-9">
                            <select class="form-control" name="sempro_hasil">
                                <option value="">Silahkan Pilih</option>
                                <option value="layak">Layak untuk dapat dilanjutkan pada tahapan Penelitian dengan usulan revisi</option>
                                <option value="tidak_layak">Tidak Layak untuk dapat dilanjutkan pada tahapan Penelitian</option>
                            </select>

                            <span class="help-block"></span>
                        </div>
                    </div>

                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="btnSaveBeritaAcara" onclick="save_berita_acara()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_rubrik" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian tidak selaras dengan topik dan judul penelitian serta kemampuan menjelaskan sangat buruk diberi nilai 1</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian sebagian selaras dengan topik dan judul penelitian serta kemampuan menjelaskan sangat buruk diberi nilai 2</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian sebagian besar selaras dengan topik dan judul penelitian serta kemampuan menjelaskan buruk diberi nilai 3</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan topik dan judul penelitian namun kemampuan menjelaskan kurang baik diberi nilai 5</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan topik dan judul penelitian namun kemampuan menjelaskan baik diberi nilai 6</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan topik dan judul penelitian serta kemampuan menjelaskan sangat baik diberi nilai 7</td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>