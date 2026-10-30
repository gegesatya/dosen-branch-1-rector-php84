<style>
    .table-borderless-small th,
    .table-borderless-small td {
        border: 0px solid #fff !important;
        padding: 3px !important;
        font-size: 10px !important;
        margin-bottom: 10px;
        vertical-align: top;
    }
</style>
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
                    "url": "<?php echo site_url('E_skripsi_sidang/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] =
                            '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6],
                    "orderable": false,
                }, ]
                // ,
                // "createdRow": function(row, data, dataIndex) {
                //     if (data[7] == 0) {
                //         $(row).addClass('success');
                //     }
                // }

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

        /* $('#jam_selesai').timepicker({
            autoclose: true,
            //minuteStep: 30,
            showSeconds: false,
            showMeridian: false
        });

        $('#jam_mulai').timepicker({
            autoclose: true,
            //minuteStep: 30,
            showSeconds: false,
            showMeridian: false
        }); */

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

        $('[name="sidang_nilai_a"]').on('input', function(e) {
            var a = Math.round(Number($('[name="sidang_nilai_a"]').val()) * 0.25 * 100) / 100;
            $('#n1').text(a);
        });

        $('[name="sidang_nilai_b"]').on('input', function(e) {
            var a = Math.round(Number($('[name="sidang_nilai_b"]').val()) * 0.15 * 100) / 100;
            $('#n2').text(a);
        });

        $('[name="sidang_nilai_c"]').on('input', function(e) {
            var a = Math.round(Number($('[name="sidang_nilai_c"]').val()) * 0.25 * 100) / 100;
            $('#n3').text(a);
        });

        $('[name="sidang_nilai_d"]').on('input', function(e) {
            var a = Math.round(Number($('[name="sidang_nilai_d"]').val()) * 0.25 * 100) / 100;
            $('#n4').text(a);
        });

        $('[name="sidang_nilai_e"]').on('input', function(e) {
            var a = Math.round(Number($('[name="sidang_nilai_e"]').val()) * 0.1 * 100) / 100;
            $('#n5').text(a);
        });

        $('.bangke').on('input', function(e) {
            var total = Number($('#n1').text()) + Number($('#n2').text()) + Number($('#n3').text()) +
                Number($('#n4').text()) + Number($('#n5').text());
            total = Math.round(total * 100) / 100;
            $('#total').val(total);
        });

    });



    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) $(id).show();
        else $(id).hide();
    }

    function edit_nilai_sidang(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_sidang/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                $('[name="sidang_nilai_a"]').val(data.sidang_nilai_a);
                $('[name="sidang_nilai_b"]').val(data.sidang_nilai_b);
                $('[name="sidang_nilai_c"]').val(data.sidang_nilai_c);
                $('[name="sidang_nilai_d"]').val(data.sidang_nilai_d);
                $('[name="sidang_nilai_e"]').val(data.sidang_nilai_e);

                var a = Math.round(data.sidang_nilai_a * 0.25 * 100) / 100;
                var b = Math.round(data.sidang_nilai_b * 0.15 * 100) / 100;
                var c = Math.round(data.sidang_nilai_c * 0.25 * 100) / 100;
                var d = Math.round(data.sidang_nilai_d * 0.25 * 100) / 100;
                var e = Math.round(data.sidang_nilai_e * 0.1 * 100) / 100;

                $('#n1').text(a);
                $('#n2').text(b);
                $('#n3').text(c);
                $('#n4').text(d);
                $('#n5').text(e);

                var total = Number(a) + Number(b) + Number(c) + Number(d) + Number(e);
                total = Math.round(total * 100) / 100;

                $('#total').val(total);

                $('#modal_form').modal('show');
                $('.modal-title').text("Nilai Sidang Tugas Akhir");

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
            url: "<?php echo site_url('E_skripsi_sidang/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="sidang_uraian_revisi"]').val(data.sidang_uraian_revisi);
                $('#modal_form_catatan').modal('show');
                $('.modal-title').text("Permintaan Revisi Ujian Tugas Akhir");

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
            url: "<?php echo site_url('E_skripsi_sidang/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="judul_ditetapkan_1"]').val(data.judul_ditetapkan);
                $('[name="sidang_hari"]').val(data.sidang_hari);
                $('[name="sidang_tanggal"]').val(data.sidang_tanggal);
                $('[name="sidang_hasil"]').val(data.sidang_hasil);
                $('[name="sidang_predikat"]').val(data.sidang_predikat);
                $('[name="sidang_kembali_mengulang"]').val(data.sidang_kembali_mengulang);
                $('[name="sidang_nilai_mutu"]').val(data.nilai_mutu);
                $('[name="sidang_hasil"]').val(data.hasil_sidang);
                $('#tbl_nilai').html(data.tbl_nilai);
                if (data.legend_html) {
                    $('#keterangan_nilai_table').html(data.legend_html);
                }

                $('#modal_form_berita_acara').modal('show');
                $('.modal-title').text("Berita Acara Sidang Tugas Akhir");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function view_rubrik() {
        $('#modal_form_rubrik').modal('show');
        $('.modal-title').text("Rubrik Penilaian Sidang TA");
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('E_skripsi_sidang/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_sidang/ajax_update') ?>";
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
            url = "<?php echo site_url('E_skripsi_sidang/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('E_skripsi_sidang/ajax_update_catatan') ?>";
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


        url = "<?php echo site_url('E_skripsi_sidang/ajax_update_berita_acara') ?>";


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
                        <label class="control-label col-md-5">Kualitas Laporan Hasil (Manuskrip) (25%)</label>
                        <div class="col-md-4">
                            <input name="sidang_nilai_a" class="form-control bangke" type="number" min=1 max=7>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div name="n1" id="n1" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Sistematika Penulisan (15%)</label>
                        <div class="col-md-4">
                            <input name="sidang_nilai_b" class="form-control bangke" type="number" min=1 max=7>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div name="n2" id="n2" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Penguasaan Materi dan Argumentasi (25%)</label>
                        <div class="col-md-4">
                            <input name="sidang_nilai_c" class="form-control bangke" type="number" min=1 max=7>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div name="n3" id="n3" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Kemampuan/Potensi Luaran (25%)</label>
                        <div class="col-md-4">
                            <input name="sidang_nilai_d" class="form-control bangke" type="number" min=1 max=7>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div name="n4" id="n4" class="text-center"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-5">Sikap dan Penampilan (10%)</label>
                        <div class="col-md-4">
                            <input name="sidang_nilai_e" class="form-control bangke" type="number" min=1 max=7>
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
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="well">
                            <p>Keterangan:</p>
                            <p>Skor: 1, 2, 3, 5, 6, 7 (1 = buruk, 2 = sangat kurang, 3 = kurang, 5 = cukup, 6 =
                                baik, 7 = sangat baik)</p>
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

<div class="modal fade" id="modal_form_rubrik" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>Skor</td>
                            <td>Latar Belakang dan Perumusan masalah</td>
                            <td>Tinjauan Pustaka</td>
                            <td>Kerangka konseptual permasalahan penelitian</td>
                            <td>Metode penelitian</td>
                            <td>Hasil dan Pembahasan</td>
                            <td>Kemampuan menjelaskan</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Latar belakang, permasalahan, tujuan dan kontribusi penelitian tidak terkait </td>
                            <td>Tinjauan pustaka dan daftar pustaka tidak relevan terhadap permasalahan penelitian dan
                                tidak mutakhir</td>
                            <td>Tidak memiliki kerangka konsep untuk untuk menjawab permasalahan penelitian </td>
                            <td>Metode penelitian disusun tidak sesuai dengan panduan</td>
                            <td>Teknik analisa, ketajaman analisa dan kesesuaian rumusan solusi tidak saling terkait.
                            </td>
                            <td>Tidak mampu menjelaskan dan menjawab seluruh pertanyaan-pertanyaan terkait penelitian
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Latar belakang, permasalahan, tujuan penelitian tidak terkait</td>
                            <td>Tinjauan pustaka dan daftar pustaka sebagian besar tidak relevan terhadap permasalahan
                                penelitian dan tidak mutakhir</td>
                            <td>Kerangka konseptual dijabarkan namun tidak terkait dengan permasalahan penelitian </td>
                            <td>Metode penelitian tidak tepat, tidak sesuai dan tidak lengkap desain yang digunakan</td>
                            <td>Teknik analisa dan ketajaman analisa serta solusi belum mampu menjawab permasalahan
                                penelitian </td>
                            <td>Mampu menjelaskan namun menjawab sedikit pertanyaan-pertanyaan terkait penelitian</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Latar belakang dan permasalahan, penelitian tidak terkait</td>
                            <td>Tinjauan pustaka dan daftar pustaka sebagian besar relevan terhadap permasalahan
                                penelitian namun tidak mutakhir</td>
                            <td>Kerangka konseptual dijabarkan namun sedikit terkait dengan permasalahan penelitian
                            </td>
                            <td>Tahapan penelitian sudah sesuai namun , metode pengumpulan data, jenis data dan metode
                                analisis data belum sesuai</td>
                            <td>Teknik analisa dan ketajaman analisa serta solusi belum mampu menjawab permasalahan
                                penelitian </td>
                            <td>Mampu menjelaskan namun menjawab sebagian pertanyaan-pertanyaan terkait penelitian</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Latar belakang, permasalahan, tujuan dan kontribusi penelitian terkait namun belum
                                didasarkan data yang valid </td>
                            <td>Tinjauan pustaka dan daftar pustaka elevan terhadap permasalahan penelitian namun tidak
                                mutakhir</td>
                            <td>Kerangka konseptual dijabarkan dan sebagian besar terkait dengan permasalahan penelitian
                                namun tidak runtut</td>
                            <td>Tahapan penelitian, metode pengumpulan data serta jenis data telah didefenisikan dengan
                                tepat namun metode analisis data belum sesuai</td>
                            <td>Teknik analisa dan ketajaman analisa sudah sesuai namun solusi belum mampu menjawab
                                permasalahan penelitian </td>
                            <td>Mampu menjelaskan namun menjawab sebagian hampir seluruh pertanyaan-pertanyaan terkait
                                penelitian</td>
                        </tr>

                        <tr>
                            <td>6</td>
                            <td>Latar belakang, permasalahan, tujuan dan kontribusi penelitian terkait dan didasarkan
                                data yang valid</td>
                            <td>Tinjauan pustaka dan daftar pustaka elevan terhadap permasalahan penelitian dan sebagian
                                tidak mutakhir</td>
                            <td>Kerangka konseptual dijabarkan dan terkait dengan permasalahan penelitian namun tidak
                                runtut</td>
                            <td>Tahapan penelitian, metode pengumpulan data. jenis data dan metode analisis data telah
                                didefenisikan dengan tepat namun penggunaan bahasa belum baku</td>
                            <td>Teknik analisa dan ketajaman analisa sudah sesuai namun solusi belum mampu menjawab
                                permasalahan penelitian secara sempurna</td>
                            <td>Mampu menjelaskan dan menjawab seluruh pertanyaan-pertanyaan terkait penelitian </td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Latar belakang, permasalahan, tujuan dan kontribusi penelitian terkait namun belum
                                didasarkan data yang valid serta runtut</td>
                            <td>Tinjauan pustaka dan daftar pustaka sangat relevan terhadap permasalahan penelitian dan
                                sangat mutakhir</td>
                            <td>Kerangka konseptual dijabarkan dan terkait dengan permasalahan penelitian serta runtut
                            </td>
                            <td>Tahapan penelitian, metode pengumpulan data. jenis data dan metode analisis data telah
                                didefenisikan dengan tepat serta penggunaan bahasa sudah baku</td>
                            <td>Teknik analisa dan ketajaman analisa serta solusi sudah sesuai dan mampu menjawab
                                permasalahan penelitian secara sempurna</td>
                            <td>Mampu menjelaskan dan menjawab seluruh pertanyaan-pertanyaan terkait penelitian serta
                                menyakinkan penguji</td>
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
                        <label class="control-label col-md-3">Usulan Revisi:</label>
                        <div class="col-md-9">
                            <textarea name="sidang_uraian_revisi" rows="5" class="form-control"></textarea>
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
                        <label class="control-label col-md-3">Hari/Tanggal</label>
                        <div class="col-md-3">
                            <input name="sidang_hari" placeholder="Hari" class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <input name="sidang_tanggal" placeholder="Tanggal" class="form-control datepicker" type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai</label>
                        <div class="col-md-9" id="tbl_nilai">

                        </div>
                        <div class="col-md-9 col-md-offset-3">
                            <table class="table table-borderless-small" style="width: 50%;">
                                <tbody id="keterangan_nilai_table">
                                    <tr>
                                        <td colspan="2">KETERANGAN NILAI :</td>
                                    </tr>
                                    <tr>
                                        <td>> 6,51 = A</td>
                                        <td>4,51 – 5,50 = C+</td>
                                    </tr>
                                    <tr>
                                        <td>6,01 – 6,50 = B+</td>
                                        <td>3,51 – 4,50 = C</td>
                                    </tr>
                                    <tr>
                                        <td>5,51 – 6,00 = B</td>
                                        <td>
                                            <=3,50=Tidak lulus</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai Akhir</label>
                        <div class="col-md-3">
                            <input name="sidang_nilai_mutu" class="form-control text-center" type="text" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Hasil Sidang</label>
                        <div class="col-md-9">
                            <input name="sidang_hasil" class="form-control text-center" type="text" readonly>

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