<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<style>
    .table-borderless-small th,
    .table-borderless-small td {
        border: 0px solid #fff !important;
        padding: 3px !important;
        font-size: 10px !important;
        margin-bottom: 10px;
        vertical-align: top;
    }

    .modal-student-header {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 16px;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        text-align: left !important;
    }

    .modal-student-photo {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #cbd5e1;
        flex-shrink: 0;
    }

    .modal-student-info {
        flex: 1;
    }

    .modal-student-info h4 {
        margin: 0 0 4px 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
    }

    .modal-student-info p.nim {
        margin: 0 0 8px 0;
        font-size: 0.9rem;
        font-weight: 500;
        color: #64748b;
    }

    .modal-student-info p.title {
        margin: 0;
        font-size: 0.95rem;
        color: #334155;
        line-height: 1.4;
        font-style: italic;
        background: #f1f5f9;
        padding: 6px 10px;
        border-radius: 0 6px 6px 0;
        border-left: 3px solid #3b82f6;
        white-space: normal;
        word-wrap: break-word;
        word-break: break-word;
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

                if (data.berita_acara_set) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah nilai.',
                            type: 'warning',
                            icon: 'warning'
                        });
                    } else if (typeof swal !== 'undefined') {
                        swal("Peringatan", "Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah nilai.", "warning");
                    } else {
                        alert("Peringatan: Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah nilai.");
                    }
                    setTimeout(function() {
                        $('.sweet-alert').css('z-index', 99999);
                        $('.sweet-overlay').css('z-index', 99998);
                        $('.swal2-container').css('z-index', 99999);
                    }, 100);
                }

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

                if (data.berita_acara_set) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah usulan revisi.',
                            type: 'warning',
                            icon: 'warning'
                        });
                    } else if (typeof swal !== 'undefined') {
                        swal("Peringatan", "Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah usulan revisi.", "warning");
                    } else {
                        alert("Peringatan: Berita Acara Sidang sudah Ditetapkan, Harap Berhati-hati jika ingin mengubah usulan revisi.");
                    }
                    setTimeout(function() {
                        $('.sweet-alert').css('z-index', 99999);
                        $('.sweet-overlay').css('z-index', 99998);
                        $('.swal2-container').css('z-index', 99999);
                    }, 100);
                }

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
                $('#revisi_judul').hide();
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                var fallback = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=3b82f6&color=fff";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback + "';}; this.src='" + original_photo + "';";
                $('#ba_student_photo').attr('src', photo).attr('onerror', onerror_script);
                $('#ba_student_nim').text(data.nipd);
                $('#ba_student_name').text(data.nm_pd);
                $('#ba_student_title').text(data.judul_ditetapkan);

                $('[name="judul_ditetapkan_1"]').val(data.judul_ditetapkan);
                $('[name="judul_ditetapkan_2"]').val(data.judul_ditetapkan);

                $('[name="sidang_hari"]').val(data.sidang_hari);
                $('[name="sidang_tanggal"]').val(data.sidang_tanggal);
                $('[name="sidang_predikat"]').val(data.sidang_predikat);
                $('[name="sidang_kembali_mengulang"]').val(data.sidang_kembali_mengulang);
                $('[name="sidang_nilai_mutu"]').val(data.nilai_mutu);
                $('[name="sidang_hasil"]').val(data.hasil_sidang);
                $('#tbl_nilai').html(data.tbl_nilai);

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

    function view_metode_penilaian() {
        // Load tabel nilai huruf jika belum
        if ($('#tbl_rentang_nilai').find('tr').length === 0) {
            $('#tbl_rentang_nilai').html('<tr><td colspan="2" class="text-center"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>');
            $.ajax({
                url: '<?php echo site_url("E_skripsi_sidang/ajax_nilai_huruf"); ?>',
                type: 'GET',
                dataType: 'JSON',
                success: function(data) {
                    var html = '';
                    $.each(data, function(i, row) {
                        html += '<tr><td class="text-center">' + row.range_bawah + ' &ndash; ' + row.range_atas + '</td>';
                        html += '<td class="text-center"><strong>' + row.nilai_huruf + '</strong></td></tr>';
                    });
                    $('#tbl_rentang_nilai').html(html);
                },
                error: function() {
                    $('#tbl_rentang_nilai').html('<tr><td colspan="2" class="text-center text-danger">Gagal memuat data.</td></tr>');
                }
            });
        }
        $('#modal_metode_penilaian').modal('show');
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
                        $('[name="' + data.inputerror[i] + '"]').closest('.form-group').addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').closest('.form-group').find('.help-block').text(data.error_string[i]);
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
                <h3 class="modal-title">Berita Acara Sidang Tugas Akhir</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_berita_acara'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_nilai_sempro" />
                <input type="hidden" value="" name="id_judul_skripsi" />
                <div class="form-body">
                    <!-- Student Card -->
                    <div class="modal-student-header" style="margin-bottom: 20px;">
                        <img id="ba_student_photo" src="" alt="Photo" class="modal-student-photo"
                            onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                        <div class="modal-student-info">
                            <h4>[<span id="ba_student_nim"></span>] <span id="ba_student_name"></span></h4>
                            <p class="title" id="ba_student_title">Judul Penelitian</p>
                        </div>
                    </div>

                    <div style="display:none;">
                        <input name="nipd" class="form-control" type="text" readonly>
                        <input name="nm_pd" class="form-control" type="text" readonly>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Judul</label>
                        <div class="col-md-9">
                            <textarea name="judul_ditetapkan_1" rows="2" class="form-control" readonly></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <div class="checkbox">
                                <label><input type="checkbox" onclick="toggle('#revisi_judul', this)"
                                        name="status_revisi_judul"> Centang untuk mengganti judul, kemudian berikan
                                    judul baru di bawah ini.</label>
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
                        <label class="control-label col-md-3">Tanggal/Hari</label>
                        <div class="col-md-9">
                            <input name="sidang_tanggal" class="form-control" type="date"
                                style="width:45%; display:inline-block;"
                                onchange="const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; document.getElementsByName('sidang_hari')[0].value = this.value ? days[new Date(this.value).getDay()] : '';">
                            <input name="sidang_hari" placeholder="Hari" class="form-control" type="text"
                                style="width:45%; display:inline-block; margin-left: 2%;" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai</label>
                        <div class="col-md-9" id="tbl_nilai">

                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai Akhir</label>
                        <div class="col-md-3">
                            <input name="sidang_nilai_mutu" class="form-control text-center" type="text" style="font-weight: bold; font-size: 18px; color: #000;" readonly>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Hasil Sidang</label>
                        <div class="col-md-9">
                            <input name="sidang_hasil" class="form-control text-center" type="text" style="font-weight: bold; font-size: 18px; color: #000;" readonly>

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

<!-- Modal Metode Penilaian Baru -->
<div class="modal fade" id="modal_metode_penilaian" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a7431, #28a745); color: #fff; border-radius: 4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" style="font-weight:700;">
                    <i class="glyphicon glyphicon-info-sign"></i> Metode Penilaian Tugas Akhir
                </h3>
            </div>
            <div class="modal-body">

                <!-- Penjelasan -->
                <div style="background:#f8fff9; border-left: 4px solid #28a745; padding: 14px 18px; border-radius:4px; margin-bottom:20px;">
                    <p style="margin:0 0 10px 0; font-size:13px; line-height:1.7;">
                        Penilaian Tugas Akhir berdasarkan komponen penilaian yang telah ditetapkan.
                        Penggabungan nilai dari kegiatan <strong>Seminar Proposal</strong>, <strong>Seminar Hasil</strong> dan <strong>Sidang</strong>
                        berdasarkan persen berikut:
                    </p>
                    <table style="width:100%; border-collapse:collapse;">
                        <tbody>
                            <tr>
                                <td style="width:30px; padding:6px 10px; vertical-align:top;">
                                    <span style="display:inline-block;background:#28a745;color:#fff;border-radius:50%;width:24px;height:24px;text-align:center;line-height:24px;font-weight:bold;font-size:12px;">1</span>
                                </td>
                                <td style="padding:6px 10px; font-size:13px;">
                                    <strong style="color:#1a7431;">10%</strong> dari Rata-rata <strong>Seminar Proposal</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 10px; vertical-align:top;">
                                    <span style="display:inline-block;background:#28a745;color:#fff;border-radius:50%;width:24px;height:24px;text-align:center;line-height:24px;font-weight:bold;font-size:12px;">2</span>
                                </td>
                                <td style="padding:6px 10px; font-size:13px;">
                                    <strong style="color:#1a7431;">10%</strong> dari Rata-rata <strong>Seminar Hasil</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 10px; vertical-align:top;">
                                    <span style="display:inline-block;background:#28a745;color:#fff;border-radius:50%;width:24px;height:24px;text-align:center;line-height:24px;font-weight:bold;font-size:12px;">3</span>
                                </td>
                                <td style="padding:6px 10px; font-size:13px;">
                                    <strong style="color:#1a7431;">80%</strong> dari Rata-rata <strong>Sidang (Ujian Akhir)</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="margin:12px 0 0 0; font-size:13px; line-height:1.7; color:#444;">
                        Total dari ketiga nilai tersebut akan <strong>dikonversi menjadi skala 100</strong>.
                        Rentang nilai Tugas Akhir disesuaikan dengan rentang nilai perkuliahan sebagai berikut:
                    </p>
                </div>

                <!-- Tabel Rentang Nilai -->
                <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                    <thead style="background:#28a745; color:#fff;">
                        <tr>
                            <th class="text-center" style="width:50%; color:#fff;">Rentang Nilai</th>
                            <th class="text-center" style="width:50%; color:#fff;">Nilai Huruf</th>
                        </tr>
                    </thead>
                    <tbody id="tbl_rentang_nilai">
                        <!-- diisi via AJAX -->
                    </tbody>
                </table>

                <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:4px; padding:10px 14px; font-size:12px; color:#856404; margin-top:5px;">
                    <i class="glyphicon glyphicon-warning-sign"></i>
                    <strong>Catatan:</strong> Nilai di bawah batas minimum (<strong>&lt; 56.0</strong>) dinyatakan <strong>TIDAK LULUS</strong>.
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <i class="glyphicon glyphicon-ok"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>