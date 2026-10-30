<script type="text/javascript">
    var save_method; //for save method string
    var table;
    var table_evaluasi_akademik;

    $(document).ready(function() {
        var obj = {};
        obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
        //datatables
        table = $('#table').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "paging": false,
            "ordering": false,
            "info": false,
            "bFilter": false,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('evaluasi_diri/ajax_list') ?>",
                "type": "POST",
                "data": obj
            },

            //Set column definition initialisation properties.
            "columnDefs": [{
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },

            ],

        });


        //set input/textarea/select event when change value, remove class error and remove text help block 
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

        //$("input[name='control_tugas_mandiri']").hide();

        $("input[name='metode_evaluasi_tugas_mandiri']").change(function() {
            if (this.checked) {
                $(".ctrl").show();
            } else {
                $(".ctrl").hide();
            }

            $(".ctrl").val("");
        });

        $("input[name='metode_evaluasi_quiz']").change(function() {
            if (this.checked) {
                $(".ctrl1").show();
            } else {
                $(".ctrl1").hide();
            }

            $(".ctrl1").val("");
        });

        $("input[name='metode_evaluasi_quiz']").change(function() {
            if (this.checked) {
                $(".ctrl1").show();
            } else {
                $(".ctrl1").hide();
            }

            $(".ctrl1").val("");
        });

        $("input[name='metode_evaluasi_tugas_kelompok']").change(function() {
            if (this.checked) {
                $(".ctrl2").show();
            } else {
                $(".ctrl2").hide();
            }

            $(".ctrl2").val("");
        });

        $("input[name='metode_evaluasi_partisipasi']").change(function() {
            if (this.checked) {
                $(".ctrl3").show();
            } else {
                $(".ctrl3").hide();
            }

            $(".ctrl3").val("");
        });

        $("input[name='metode_evaluasi_uts']").change(function() {
            if (this.checked) {
                $(".ctrl4").show();
            } else {
                $(".ctrl4").hide();
            }

            $(".ctrl4").val("");
        });

        $("input[name='metode_evaluasi_uas']").change(function() {
            if (this.checked) {
                $(".ctrl5").show();
            } else {
                $(".ctrl5").hide();
            }

            $(".ctrl5").val("");
        });


    });

    function form_evaluasi_diri(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.gege-class').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('evaluasi_diri/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_jadwal"]').val(id);

                $("input[name='materi_kuliah_referensi'][value='" + data.materi_kuliah_referensi + "']").prop("checked", true);
                $("input[name='materi_kuliah_diktat'][value='" + data.materi_kuliah_diktat + "']").prop("checked", true);
                $("input[name='materi_kuliah_handout'][value='" + data.materi_kuliah_handout + "']").prop("checked", true);
                $("input[name='materi_kuliah_lain_lain'][value='" + data.materi_kuliah_lain_lain + "']").prop("checked", true);
                $("input[name='metode_kuliah_lecturing'][value='" + data.metode_kuliah_lecturing + "']").prop("checked", true);
                $("input[name='metode_kuliah_diskusi'][value='" + data.metode_kuliah_diskusi + "']").prop("checked", true);
                $("input[name='metode_kuliah_smallgroup'][value='" + data.metode_kuliah_smallgroup + "']").prop("checked", true);
                $("input[name='metode_kuliah_presentasi_mhs'][value='" + data.metode_kuliah_presentasi_mhs + "']").prop("checked", true);
                $("input[name='metode_kuliah_studi_kasus'][value='" + data.metode_kuliah_studi_kasus + "']").prop("checked", true);
                $("input[name='metode_kuliah_studi_proyek'][value='" + data.metode_kuliah_studi_proyek + "']").prop("checked", true);
                $("input[name='metode_kuliah_lain_lain'][value='" + data.metode_kuliah_lain_lain + "']").prop("checked", true);

                $("input[name='metode_evaluasi_tugas_mandiri'][value='" + data.metode_evaluasi_tugas_mandiri + "']").prop("checked", true);
                $("input[name='metode_evaluasi_quiz'][value='" + data.metode_evaluasi_quiz + "']").prop("checked", true);
                $("input[name='metode_evaluasi_tugas_kelompok'][value='" + data.metode_evaluasi_tugas_kelompok + "']").prop("checked", true);
                $("input[name='metode_evaluasi_partisipasi'][value='" + data.metode_evaluasi_partisipasi + "']").prop("checked", true);
                $("input[name='metode_evaluasi_uts'][value='" + data.metode_evaluasi_uts + "']").prop("checked", true);
                $("input[name='metode_evaluasi_uas'][value='" + data.metode_evaluasi_uas + "']").prop("checked", true);

                if ($("input[name='metode_evaluasi_tugas_mandiri']").is(':checked')) {
                    $(".ctrl").show();
                } else {
                    $(".ctrl").hide();
                }

                if ($("input[name='metode_evaluasi_quiz']").is(':checked')) {
                    $(".ctrl1").show();
                } else {
                    $(".ctrl1").hide();
                }

                if ($("input[name='metode_evaluasi_tugas_kelompok']").is(':checked')) {
                    $(".ctrl2").show();
                } else {
                    $(".ctrl2").hide();
                }

                if ($("input[name='metode_evaluasi_partisipasi']").is(':checked')) {
                    $(".ctrl3").show();
                } else {
                    $(".ctrl3").hide();
                }

                if ($("input[name='metode_evaluasi_uts']").is(':checked')) {
                    $(".ctrl4").show();
                } else {
                    $(".ctrl4").hide();
                }

                if ($("input[name='metode_evaluasi_uas']").is(':checked')) {
                    $(".ctrl5").show();
                } else {
                    $(".ctrl5").hide();
                }

                $('#modal_form').modal('show');
                $('.modal-makul').text(data.nm_mk);
                $('.modal-dosen').text(data.nm_ptk);
                $('.modal-prodi').text(data.nm_lemb);

                $('#jumlah-peserta').text(data.int_jumlah_peserta + ' Orang');
                $('#nilai-rata-rata').val(data.nilai_rata_kelas);
                $('#distribusi_nilai_a').text(data.distribusi_nilai_a);
                $('#distribusi_nilai_b_plus').text(data.distribusi_nilai_b_plus);
                $('#distribusi_nilai_b').text(data.distribusi_nilai_b);
                $('#distribusi_nilai_c_plus').text(data.distribusi_nilai_c_plus);
                $('#distribusi_nilai_c').text(data.distribusi_nilai_c);
                $('#distribusi_nilai_d').text(data.distribusi_nilai_d);
                $('#distribusi_nilai_e').text(data.distribusi_nilai_e);
                $('#distribusi_nilai_kosong').text(data.distribusi_nilai_kosong);
                $('#persen_nilai_a').text(data.persen_nilai_a);
                $('#persen_nilai_b_plus').text(data.persen_nilai_b_plus);
                $('#persen_nilai_b').text(data.persen_nilai_b);
                $('#persen_nilai_c_plus').text(data.persen_nilai_c_plus);
                $('#persen_nilai_c').text(data.persen_nilai_c);
                $('#persen_nilai_d').text(data.persen_nilai_d);
                $('#persen_nilai_e').text(data.persen_nilai_e);
                $('#persen_nilai_kosong').text(data.persen_nilai_kosong);
                $('#jumlah_tatap_muka').val(data.jumlah_tatap_muka);
                $('#persen_tatap_muka').val(data.persen_tatap_muka);
                $('#materi_tersampaikan').val(data.materi_tersampaikan);

                $('#metode_evaluasi_tugas_mandiri_jumlah').val(data.metode_evaluasi_tugas_mandiri_jumlah);
                $('#metode_evaluasi_tugas_mandiri_bobot').val(data.metode_evaluasi_tugas_mandiri_bobot);
                $('#metode_evaluasi_quiz_jumlah').val(data.metode_evaluasi_quiz_jumlah);
                $('#metode_evaluasi_quiz_bobot').val(data.metode_evaluasi_quiz_bobot);
                $('#metode_evaluasi_tugas_kelompok_jumlah').val(data.metode_evaluasi_tugas_kelompok_jumlah);
                $('#metode_evaluasi_tugas_kelompok_bobot').val(data.metode_evaluasi_tugas_kelompok_bobot);

                $('#metode_evaluasi_uts_bobot').val(data.metode_evaluasi_uts_bobot);
                $('#metode_evaluasi_uas_bobot').val(data.metode_evaluasi_uas_bobot);
                $('#metode_evaluasi_partisipasi_bobot').val(data.metode_evaluasi_partisipasi_bobot);

                $('#text_kelemahan_dan_kendala').val(data.text_kelemahan_dan_kendala);
                $('#text_rekomendasi').val(data.text_rekomendasi);

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
            url = "<?php echo site_url('evaluasi_diri/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('evaluasi_diri/ajax_update') ?>";
        }

        var data = $('#form').serializeArray();
        data.push({
            name: '<?php echo $this->security->get_csrf_token_name(); ?>',
            value: '<?php echo $this->security->get_csrf_hash(); ?>'
        });

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('#' + data.inputerror[i]).next().text(data.error_string[i]);
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
</script>
<style>
    .borderless-text {
        border: none;
        border-bottom: 1px solid;
        padding: 5px;

    }
</style>


<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog" style="width:1200px !important">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="text-center">LAPORAN EVALUASI DIRI</h3>
                <h4 class="text-center">PBM SEMESTER <?= $this->session->userdata('nm_smt'); ?></h4>
                
                <table>
                    <tr>
                        <td style="width:180px">MATA KULIAH</td>
                        <td>:</td>
                        <td><span style="margin-left: 10px" class="modal-makul"></span></td>
                    </tr>
                    <tr>
                        <td>PROGRAM STUDI</td>
                        <td>:</td>
                        <td><span style="margin-left: 10px" class="modal-prodi"></span></td>
                    </tr>
                    <tr>
                        <td>DOSEN PENGASUH</td>
                        <td>:</td>
                        <td><span style="margin-left: 10px" class="modal-dosen"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id_jadwal" />
                    <div class="form-body">
                        <table class="table table-stripped table-condensed">
                            <tbody>
                                <tr class="bg-blue">
                                    <td></td>
                                    <td>A. KINERJA KELAS</td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>Jumlah Peserta <span id="jumlah-peserta"></span>

                                    </td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Bahan/Materi yang diberikan pada mahasiswa (boleh lebih dari satu)
                                        <div class="gege-class">
                                            <label class="radio-inline"><input type="checkbox" name="materi_kuliah_referensi" value="Buku Referensi" class="minimal"> Buku Referensi </label>
                                            <label class="radio-inline"><input type="checkbox" name="materi_kuliah_diktat" value="Diktat Kuliah" class="minimal"> Diktat Kuliah </label>
                                            <label class="radio-inline"><input type="checkbox" name="materi_kuliah_handout" value="Handout" class="minimal"> Handout </label>
                                            <label class="radio-inline"><input type="checkbox" name="materi_kuliah_lain_lain" value="Lain-lain" class="minimal"> Lain-lain </label>
                                        </div>
                                        <span class="help-block label label-danger"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Metode perkuliahan yang dilakukan (boleh lebih dari satu)
                                        <div class="gege-class" id="pedagogik_3">
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_lecturing" value="Lecturing" class="minimal"> Lecturing
                                            </label>
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_diskusi" value="Diskusi" class="minimal"> Diskusi
                                            </label>
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_smallgroup" value="Small Group Discussion" class="minimal"> Small Group Discussion
                                            </label>
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_presentasi_mhs" value="Presentasi Mahasiswa" class="minimal"> Presentasi Mahasiswa
                                            </label>
                                        </div>
                                        <div class="gege-class" id="pedagogik_3">
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_studi_kasus" value="Studi Kasus" class="minimal"> Studi Kasus
                                            </label>
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_studi_proyek" value="Studi Proyek" class="minimal"> Studi Proyek
                                            </label>
                                            <label class="radio-inline" style="width:220px">
                                                <input type="checkbox" name="metode_kuliah_lain_lain" value="Lain-lain" class="minimal"> Lain-lain
                                            </label>
                                        </div>
                                        <span class="help-block label label-danger"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4.</td>
                                    <td>Metode Evaluasi Hasil Belajar Mahasiswa (boleh lebih dari satu)
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_tugas_mandiri" value="Tugas Mandiri" class="minimal"> Tugas Mandiri
                                            </label>
                                            <input class="borderless-text text-center ctrl" placeholder="Jumlah" type="text" id="metode_evaluasi_tugas_mandiri_jumlah" name="metode_evaluasi_tugas_mandiri_jumlah"> kali / Bobot
                                            <input class="borderless-text text-center ctrl" placeholder="Bobot" type="text" id="metode_evaluasi_tugas_mandiri_bobot" name="metode_evaluasi_tugas_mandiri_bobot"> %
                                        </div>
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_quiz" value="Quiz"> Quiz
                                            </label>
                                            <input class="borderless-text text-center ctrl1" placeholder="Jumlah" type="text" id="metode_evaluasi_quiz_jumlah" name="metode_evaluasi_quiz_jumlah"> kali / Bobot
                                            <input class="borderless-text text-center ctrl1" placeholder="Bobot" type="text" id="metode_evaluasi_quiz_bobot" name="metode_evaluasi_quiz_bobot"> %
                                        </div>
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_tugas_kelompok" value="Tugas Kelompok"> Tugas Kelompok
                                            </label>
                                            <input class="borderless-text text-center ctrl2" placeholder="Jumlah" type="text" id="metode_evaluasi_tugas_kelompok_jumlah" name="metode_evaluasi_tugas_kelompok_jumlah"> kali / Bobot
                                            <input class="borderless-text text-center ctrl2" placeholder="Bobot" type="text" id="metode_evaluasi_tugas_kelompok_bobot" name="metode_evaluasi_tugas_kelompok_bobot"> %
                                        </div>
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_partisipasi" value="Partisipasi"> Keaktifan di kelas / Partisipasi Bobot
                                            </label>
                                            <input class="borderless-text text-center ctrl3" type="text" placeholder="Bobot" id="metode_evaluasi_partisipasi_bobot" name="metode_evaluasi_partisipasi_bobot"> %
                                        </div>
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_uts" value="Ujian Tengah Semester"> Ujian Tengah Semester Bobot
                                            </label>
                                            <input class="borderless-text text-center ctrl4" type="text" placeholder="Bobot" id="metode_evaluasi_uts_bobot" name="metode_evaluasi_uts_bobot"> %
                                        </div>
                                        <div class="gege-class" id="pedagogik_4">
                                            <label class="radio-inline" style="width:280px">
                                                <input type="checkbox" name="metode_evaluasi_uas" value="Ujian Akhir Semester"> Ujian Akhir Semester Bobot
                                            </label>
                                            <input class="borderless-text text-center ctrl5" type="text" placeholder="Bobot" id="metode_evaluasi_uas_bobot" name="metode_evaluasi_uas_bobot"> %
                                        </div>
                                        <span class="help-block label label-danger"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5.</td>
                                    <td>Nilai Rata-Rata Kelas :
                                        <label class="radio-inline"><input class="borderless-text text-center " type="text" id="nilai-rata-rata" readonly="readonly" name="nilai_rata_kelas" class="minimal"></label>
                                        <span class="help-block label label-danger"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>6.</td>
                                    <td>Distribusi Nilai
                                        <div class="gege-class">
                                            <table class="table table-bordered table-condensed">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Nilai</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th class="text-center">Persen</th>
                                                        <th class="text-center">Nilai</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th class="text-center">Persen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">A</td>
                                                        <td class="text-center"><span id="distribusi_nilai_a"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_a"></td>
                                                        <td class="text-center">C</td>
                                                        <td class="text-center"><span id="distribusi_nilai_c"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_c"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">B+</td>
                                                        <td class="text-center"><span id="distribusi_nilai_b_plus"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_b_plus"></span></td>
                                                        <td class="text-center">D</td>
                                                        <td class="text-center"><span id="distribusi_nilai_d"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_d"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">B</td>
                                                        <td class="text-center"><span id="distribusi_nilai_b"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_b"></span></td>
                                                        <td class="text-center">E</td>
                                                        <td class="text-center"><span id="distribusi_nilai_e"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_e"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">C+</td>
                                                        <td class="text-center"><span id="distribusi_nilai_c_plus"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_c_plus"></span></td>
                                                        <td class="text-center">Tidak Diisi</td>
                                                        <td class="text-center"><span id="distribusi_nilai_kosong"></span></td>
                                                        <td class="text-center"><span id="persen_nilai_kosong"></span></td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                        <span class="help-block label label-danger"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>7.</td>
                                    <td>Jumlah Tatap Muka :
                                        <input class="borderless-text text-center" placeholder="Jumlah" type="text" id="jumlah_tatap_muka" name="jumlah_tatap_muka" class="minimal"> Kali /
                                        <input class="borderless-text text-center" placeholder="Persen" type="text" id="persen_tatap_muka" name="persen_tatap_muka" class="minimal"> %

                                        <p class="text text-primary">Isikan jumlah tatap muka terealisasi. Persen tatap muka merupakan hasil jumlah realisasi dibagi jumlah rencana tatap muka.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>8.</td>
                                    <td>Materi Tersampaikan
                                        <input class="borderless-text text-center" placeholder="Persen" type="text" id="materi_tersampaikan" name="materi_tersampaikan" class="minimal"> %
                                    </td>
                                </tr>

                                <tr class="bg-blue">
                                    <td></td>
                                    <td>B. ANALISA KELAS DAN MAHASISWA <BR> (Berikan uraian yang berkaitan dengan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>Kelemahan/Kesulitan dan Kendala dalam Pelaksanaan Perkuliahan
                                        <div class="gege-class" id="profesional_1">
                                            <textarea class="form-control" rows="5" name="text_kelemahan_dan_kendala" id="text_kelemahan_dan_kendala"></textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Rekomendasi (Berikan uraian saran/rekomendasi maupun rencana perbaikan dengan peningkatan suasana kelas, motivasi mahasiswa, daya tangkap, inisiatif, sarana pendukung, dll)
                                        <div class="gege-class" id="profesional_2">
                                            <textarea class="form-control" rows="5" name="text_rekomendasi" id="text_rekomendasi"></textarea>
                                        </div>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
