<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            // var angkatan = $('#ddAngkatan :selected').val();
            var status_krs = $('#ddstatus :selected').val();

            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('Lecturer_persetujuan_krs/ajax_list') ?>" + "/" + status_krs,
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
                    "targets": -1,
                    "width": 500,
                }, {
                    "targets": 0,
                    "width": 100,
                }],
                "drawCallback": function(settings) {
                    window.scrollTo(0, 0);
                },
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

        // $('select[name=ddAngkatan]').on('change', function() {
        //     TableAjax.init();
        // });

        $('select[name=ddstatus]').on('change', function() {
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

            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_balas_pesan') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: (function() {
                    var formData = $('#form_balas_pesan').serializeArray();
                    // Basic client-side sanitization: remove script tags and encode basic entities
                    for (var i = 0; i < formData.length; i++) {
                        if (formData[i].name === 'catatan_dosen_wali') {
                            formData[i].value = formData[i].value.replace(/<script\b[^>]*>([\s\S]*?)<\/script>/gmi, "");
                            // We don't necessarily need to encode everything here since server does it, 
                            // but it's good as a first layer.
                        }
                    }
                    return formData;
                })(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        // Refresh message history
                        var id_rencana_studi = $('#form_balas_pesan [name="id_rencana_studi"]').val();
                        var nipd = $('#msg_nipd').val();
                        var id_smt = "<?php echo $this->session->userdata('tahun_ajaran'); ?>";
                        
                        // Call view_pesan again to refresh history
                        $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(msgData) {
                            $('#data_pesan').html(msgData.data_pesan);
                            $('#form_balas_pesan [name="catatan_dosen_wali"]').val('');
                            
                            // Scroll to bottom
                            var d = $('#data_pesan');
                            d.scrollTop(d.prop("scrollHeight"));
                        });
                    }
                }
            });
        });

        // Refresh datatable only when message modal is closed
        $('#modal_form_balas_pesan').on('hidden.bs.modal', function () {
            reload_table();
        });

        $('#form_persetujuan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('Lecturer_persetujuan_krs/persetujuan_krs') ?>";

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
                    }
                }
            });
        });
    });

    function edit_pengajuan_krs(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $.ajax({
                    url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_get_paket_detail/') ?>/" + data.id_paket_krs,
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
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_get_krs/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(data.id_rencana_studi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="nm_sdm"]').val(data.nm_ptk);
                $('[name="total_sks"]').val(data.total_sks);
                $('[name="registrasi_alamat"]').val(data.registrasi_alamat);
                $('[name="registrasi_no_hp"]').val(data.registrasi_no_hp);
                $('[name="registrasi_no_hp_ortu"]').val(data.registrasi_no_hp_ortu);
                $('[name="registrasi_nominal_spp"]').val(data.registrasi_nominal_spp);
                $('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);

                if (data.cek_val) {
                    $('#modal_form_approval').modal('show');
                    $('.modal-title').text("Persetujuan KRS");
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
            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_update') ?>";
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form_krs').modal('hide');
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

    function show_krs(id_rencana_studi, nipd, id_smt) {
        $('#form_persetujuan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/view_krs') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                //$('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);
                //$('[name="id_status_krs"]').val(data.id_status_krs);
                $('#nm_pd').val(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#last_ipk').val(data.last_ipk);
                $('#semester_tempuh').val(data.semester_tempuh);
                $('#tabel_krs').html(data.tabel_krs);
                $('#attention').html(data.attention);
                $('#tabel_akademik_record').html(data.tabel_akademik_record);
                $('#log_message').html(data.log_message);
                $('#foto').html(data.pic);
                $('#kip_info').html(data.kip_status);
                $('#risk_info').html(data.risk_status);
                $('#semester_tempuh').val(data.semester_tempuh + " Semester");
                $('#masa_studi').val(data.masa_studi_text);
                $('#catatan_dosen_wali').val('');

                // Toggle between Detail and Approval mode
                if (data.id_status_krs != 2 || data.is_deadline_passed) {
                    $('#form_column').hide();
                    $('#chat_column').removeClass('col-md-6').addClass('col-md-12');
                    $('.modal-title').text("Detail Kartu Rencana Studi");
                } else {
                    $('#form_column').show();
                    $('#chat_column').removeClass('col-md-12').addClass('col-md-6');
                    $('.modal-title').text("Persetujuan Kartu Rencana Studi");
                }

                $('#modal_form_krs').modal('show');

                // Scroll to bottom after modal shown
                setTimeout(function() {
                    var d = $('#log_message');
                    d.scrollTop(d.prop("scrollHeight"));
                }, 200);

                // Render Chart
                var options = {
                    series: [{
                        name: 'IPS',
                        data: data.ips_values
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#0e9447'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        style: { fontSize: '10px' }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        labels: { style: { fontSize: '9px' } }
                    },
                    yaxis: { max: 4, tickAmount: 4 },
                    title: {
                        text: 'Riwayat IPS',
                        align: 'center',
                        style: { fontSize: '12px' }
                    }
                };

                $('#ips_chart').empty();
                var chart = new ApexCharts(document.querySelector("#ips_chart"), options);
                chart.render();

                // Handle Perwalian Button
                currentStudentData = data;
                if (!data.has_perwalian) {
                    $('#perwalian_btn_container').show();
                } else {
                    $('#perwalian_btn_container').hide();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function show_makul() {
        //$('#modal_makul').modal('show');
        // $('.modal-title').text("Daftar Mata Kuliah Prodi");
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/show_makul_prodi') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#id_smt').val(data.id_smt);
                $('#tabel_makul').html(data.tabel);
                $('#modal_makul').modal('show');
                $('.modal-title').text("Daftar Mata Kuliah Prodi");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function balas_pesan(id_rencana_studi, nipd, id_smt) {
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                $('#data_pesan').html(data.data_pesan);
                $('#msg_foto').html(data.pic);
                $('#msg_nm_pd').val(data.nm_pd);
                $('#msg_nm_pd').val(data.nm_pd);
                $('#msg_nipd').val(data.nipd);
                $('#msg_last_ipk').val(data.last_ipk);
                $('#msg_semester_tempuh').val(data.semester_tempuh + " Semester");
                $('#msg_masa_studi').val(data.masa_studi_text);
                $('#msg_kip_info').html(data.kip_status);

                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("Kirim Pesan ke Mahasiswa");

                // Scroll to bottom after modal shown
                setTimeout(function() {
                    var d = $('#data_pesan');
                    d.scrollTop(d.prop("scrollHeight"));
                }, 200);

                // Render Chart
                var options = {
                    series: [{
                        name: 'IPS',
                        data: data.ips_values
                    }],
                    chart: {
                        type: 'bar',
                        height: 200,
                        toolbar: { show: false }
                    },
                    colors: ['#0e9447'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        style: { fontSize: '10px' }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        labels: { style: { fontSize: '9px' } }
                    },
                    yaxis: { max: 4, tickAmount: 4 },
                    title: {
                        text: 'Riwayat IPS',
                        align: 'center',
                        style: { fontSize: '12px' }
                    }
                };

                $('#msg_ips_chart').empty();
                var chart = new ApexCharts(document.querySelector("#msg_ips_chart"), options);
                chart.render();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function perbaikan_krs(id, nm, nipd) {
        $('#form_perbaikan_krs')[0].reset();
        $('#id_rencana_studi_perbaikan').val(id);
        $('#nm_mhs_perbaikan').text(nm);
        $('#nim_mhs_perbaikan').text(nipd);
        $('#modal_perbaikan_krs').modal('show');
    }

    $(document).off('click', '#btnConfirmPerbaikan').on('click', '#btnConfirmPerbaikan', function() {
        var id = $('#id_rencana_studi_perbaikan').val();
        var alasan = $('#alasan_perbaikan').val();
        
        if (!alasan || alasan.trim() == "") {
            alert("Mohon isi alasan perbaikan terlebih dahulu.");
            return false;
        }

        var btn = $(this);
        btn.text('Processing...');
        btn.attr('disabled', true);

        var obj = {};
        obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
        obj['alasan'] = alasan;

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_perbaikan_krs') ?>/" + id,
            type: "POST",
            data: obj,
            dataType: "JSON",
            success: function(data) {
                $('#modal_perbaikan_krs').modal('hide');
                btn.html('<i class="fa fa-check"></i> Konfirmasi Buka Perbaikan');
                btn.attr('disabled', false);
                reload_table();
                
                // Optional: Metronic standard alert if available
                if (typeof App !== 'undefined' && App.alert) {
                    App.alert({
                        type: 'success',
                        icon: 'check',
                        message: 'KRS Mahasiswa berhasil dibuka untuk perbaikan.',
                        container: '#alert_container',
                        place: 'append'
                    });
                } else {
                    alert('KRS Mahasiswa berhasil dibuka untuk perbaikan.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error processing data');
                btn.html('<i class="fa fa-check"></i> Konfirmasi Buka Perbaikan');
                btn.attr('disabled', false);
            }
        });
    });

    function toggle_history(id_rencana_studi, nipd, id_smt) {
        var container = $('#msg_container_' + id_rencana_studi);
        var btn = $('#btn_toggle_' + id_rencana_studi);

        if (container.is(':visible')) {
            container.slideUp();
            btn.html('<i class="fa fa-comments"></i> <small>Lihat History</small>');
            btn.removeClass('red-stripe').addClass('green-stripe');
        } else {
            if (container.html() == '') {
                container.html('<div style="text-align: center; padding: 10px;"><i class="fa fa-spinner fa-spin"></i></div>');
                container.show();
                $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(data) {
                    container.hide();
                    container.html(data.data_pesan);
                    container.slideDown();
                    btn.html('<i class="fa fa-eye-slash"></i> <small>Tutup History</small>');
                    btn.removeClass('green-stripe').addClass('red-stripe');
                    
                    // Scroll to bottom
                    container.animate({ scrollTop: container.prop("scrollHeight") }, 500);
                });
            } else {
                container.slideDown();
                btn.html('<i class="fa fa-eye-slash"></i> <small>Tutup History</small>');
                btn.removeClass('green-stripe').addClass('red-stripe');
            }
        }
    }

    function view_foto(id) {
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_view_foto') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                
                var foto_url = "<?php echo base_url('assets/images.png') ?>";
                if (data.foto) {
                    foto_url = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                }
                $("#pas_foto").attr("src", foto_url);

                $('#modal_foto').modal('show');
                $('#modal_foto .modal-title').text(data.nm_pd + " (" + data.nipd + ")");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    var currentStudentData = null;
    function generate_perwalian_insight() {
        if (!currentStudentData) return;
        
        var ipk = currentStudentData.last_ipk;
        var failed = currentStudentData.failed_count;
        var smt = currentStudentData.semester_tempuh;
        var name = currentStudentData.nm_pd;
        
        var text = "[PERWALIAN]\n";
        text += "Halo " + name + ", saya sudah mereview rencana studi (KRS) yang Anda usulkan. ";
        
        // KRS Review Narration
        if (currentStudentData.krs_review) {
            var kr = currentStudentData.krs_review;
            text += "Pada semester ini Anda mengambil " + kr.total_mk + " mata kuliah dengan total " + kr.total_sks + " SKS.\n\n";
            
            text += "Berdasarkan riwayat akademik, Anda saat ini memasuki Semester " + kr.expected_semester + ". ";
            
            if (kr.total_offered_this_sem > 0) {
                var pass_names = (kr.already_passed_names && kr.already_passed_names.length > 0) ? kr.already_passed_names.join(', ') : null;
                var not_taken = (kr.not_taken_names && kr.not_taken_names.length > 0) ? kr.not_taken_names.join(', ') : null;

                if (pass_names) {
                    text += "Pada semester ini prodi menawarkan " + kr.total_offered_this_sem + " MK untuk Semester " + kr.expected_semester + ", namun saya mencatat MK lainnya seperti (" + pass_names + ") sudah Anda ambil dan lulus pada semester sebelumnya.";
                } else if (!not_taken) {
                    text += "Pada semester ini prodi menawarkan " + kr.total_offered_this_sem + " MK untuk Semester " + kr.expected_semester + ".";
                }

                if (not_taken) {
                    text += (pass_names ? "\n\n" : " ") + "WASPADA: Terdapat " + kr.not_taken_names.length + " MK penawaran Semester " + kr.expected_semester + " yang BELUM Anda ambil (" + not_taken + "). Mohon pastikan kembali alasan mahasiswa tidak mengambil MK tersebut.";
                }
            }
            text += "\n\nDari daftar MK yang diambil semester ini:\n";
            
            if (kr.mk_current > 0) {
                text += "- Terdapat " + kr.mk_current + " MK yang sesuai dengan kurikulum semester " + kr.expected_semester + ".\n";
            }
            if (kr.mk_repeat > 0) {
                text += "- Terdapat " + kr.mk_repeat + " MK yang merupakan mata kuliah bawah/mengulang.\n";
            }
            if (kr.mk_advance > 0) {
                text += "- Terdapat " + kr.mk_advance + " MK yang merupakan mata kuliah atas.\n";
            }
            text += "\nBerikut adalah beberapa catatan tambahan untuk perwalian Anda:\n\n";
        } else {
            text += "Berikut adalah beberapa catatan akademik untuk perwalian semester ini:\n\n";
        }
        
        // IPS/IPK Insight with Granular Logic & Trend Analysis
        var ipk_val = parseFloat(ipk);
        var ips_vals = currentStudentData.ips_values || [];
        var target_ips = "3.25";
        var advice_text = "";
        var trend_text = "";

        // Determine Target & Advice based on IPK
        if (ipk_val < 2.0) {
            target_ips = "2.75";
            advice_text = "Anda perlu melakukan perbaikan serius pada pola belajar. Tingkatkan kehadiran dan fokus pada tugas agar IPK bisa segera naik ke zona aman.";
        } else if (ipk_val < 2.75) {
            target_ips = "3.10";
            advice_text = "Tingkatkan fokus belajar Anda agar IPS semester depan bisa mencapai minimal 3.10 untuk mendongkrak IPK ke angka 3.00.";
        } else if (ipk_val < 3.25) {
            target_ips = "3.50";
            advice_text = "Peluang lulus dengan predikat Cum Laude masih terbuka lebar. Maksimalkan performa di semester ini dengan target IPS 3.50.";
        } else if (ipk_val < 3.75) {
            target_ips = "3.75";
            advice_text = "Prestasi Anda sudah sangat baik. Pertahankan konsistensi belajar Anda agar tetap berada di jalur prestasi.";
        } else {
            target_ips = "3.85";
            advice_text = "Luar biasa! Pertahankan standar tinggi Anda dan jadilah inspirasi bagi rekan-rekan mahasiswa lainnya.";
        }

        // Analyze Trend (Last 2 Semesters)
        if (ips_vals.length >= 2) {
            var last_ips = parseFloat(ips_vals[ips_vals.length - 1]);
            var prev_ips = parseFloat(ips_vals[ips_vals.length - 2]);
            if (last_ips > prev_ips) {
                trend_text = " Saya senang melihat tren IPS Anda yang meningkat dari semester sebelumnya. Pertahankan!";
            } else if (last_ips < prev_ips) {
                trend_text = " Waspada terhadap tren IPS yang sedikit menurun dari semester sebelumnya, mari kita perbaiki di semester ini.";
            }
        }

        text += "1. CAPAIAN AKADEMIK: IPK Anda saat ini adalah " + ipk + "." + trend_text + " \n   " + advice_text + " Targetkan IPS semester depan minimal " + target_ips + ".\n\n";
        
        // Failed & Improvement Courses Insight
        var e_count = currentStudentData.e_count || 0;
        var d_count = currentStudentData.d_count || 0;
        
        if (e_count > 0 || d_count > 0) {
            text += "2. MATA KULIAH:";
            if (e_count > 0) {
                text += " Ada " + e_count + " mata kuliah yang belum lulus (Nilai E). Saya instruksikan agar mata kuliah tersebut segera diambil kembali (mengulang) pada semester ini agar tidak menghambat kelulusan.";
            }
            if (d_count > 0) {
                text += (e_count > 0 ? "\n   " : " ") + "Terdapat " + d_count + " mata kuliah bernilai D. Meskipun secara teknis lulus, saya sangat menyarankan Anda untuk mengambil ulang mata kuliah tersebut guna memperbaiki capaian IPK Anda.";
            }
            text += "\n\n";
        } else {
            text += "2. MATA KULIAH: Seluruh mata kuliah sebelumnya sudah lulus dengan baik. Tetap fokus pada mata kuliah yang baru diajukan ini.";
            text += "\n\n";
        }
        
        // Progress Insight
        text += "3. MASA STUDI: Anda saat ini berada di semester " + smt + ". Manfaatkan waktu dengan baik agar bisa menyelesaikan tugas akhir tepat waktu (Target 8 Semester).";
        
        // KIP Insight
        if (currentStudentData.kip_status && currentStudentData.kip_status.indexOf('label-warning') !== -1) {
            text += "\n\n4. KIP KULIAH: Sebagai penerima bantuan KIP, pastikan prestasi tetap stabil agar bantuan tidak terhenti.";
        }

        $('#catatan_perwalian_input').val(text);
        $('#perwalian_btn_container').hide(); // Hide once used
    }
</script>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Persetujuan KRS Mahasiswa</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-5" style="border-right: 1px solid #eee;">
                        <div class="row" style="margin-bottom: 15px; display: flex; align-items: center;">
                            <div class="col-md-4" style="text-align: center;">
                                <div id="foto" style="display: inline-block;"></div>
                            </div>
                            <div class="col-md-8">
                                <div id="ips_chart" style="min-height: 250px;"></div>
                            </div>
                        </div>
                        
                        <div class="form-horizontal">
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Nama:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id='nm_pd' readonly style="background: #f9f9f9;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">NIM:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id='nipd' readonly style="background: #f9f9f9;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">IPK Terakhir:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id='last_ipk' readonly style="font-weight: bold; color: #333; background: #f9f9f9;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Status KIP:</label>
                                <div class="col-md-8" id="kip_info" style="padding-top: 8px;"></div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Smt Tempuh:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id='semester_tempuh' readonly style="background: #f9f9f9;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Masa Studi:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id='masa_studi' readonly style="background: #f9f9f9;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Resiko Lulus:</label>
                                <div class="col-md-8" id="risk_info" style="padding-top: 8px;"></div>
                            </div>
                        </div>
                        
                        
                        <div id="attention" class="compact-table" style="margin-top: 15px; max-height: 200px; overflow-y: auto; border-bottom: 1px solid #eee;"></div>
                    </div>

                    
                    <div class="col-md-7">
                        <h5 style="margin-top: 0; font-weight: bold;"><i class="fa fa-list"></i> Mata Kuliah Diajukan:</h5>
                        <div class="table-responsive compact-table" id="tabel_krs" style="margin-bottom: 15px; border: 1px solid #eee; border-radius: 4px;"></div>
                        
                        <h5 style="font-weight: bold;"><i class="fa fa-comments"></i> Perwalian dan Pesan:</h5>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-6" id="chat_column">
                                <div class="well" id="log_message" style="height: 300px; overflow-y: auto; background-color: #f9f9f9; display: flex; flex-direction: column; padding: 10px; font-size: 12px; margin-bottom: 0; border: 1px solid #eee;">
                                </div>
                            </div>
                            <div class="col-md-6" id="form_column">
                                <div style="background-color: #fff; padding: 0; border-radius: 4px;">
                                    <?php
                                    $attributes = ['class' => 'form', 'id' => 'form_persetujuan'];
                                    echo form_open('#', $attributes);
                                    ?>
                                    <input type="hidden" value="" name="id_rencana_studi" />
                                    
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <textarea class="form-control" rows="10" name="catatan_dosen_wali" id="catatan_perwalian_input" style="height: 255px; resize: none; font-size: 12px;" placeholder="Ketik pesan untuk mahasiswa di sini..."></textarea>
                                        <div id="perwalian_btn_container" style="margin-top: 5px; text-align: right; display: none;">
                                            <button type="button" class="btn btn-xs btn-info" onclick="generate_perwalian_insight()" style="border-radius: 20px;">
                                                <i class="fa fa-magic"></i> Saran Perwalian
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <div class="row">
                                            <div class="col-md-7" style="padding-right: 5px;">
                                                <?php
                                                echo form_dropdown('id_status_krs', $dd_status_krs, '', 'class="form-control input-sm" required');
                                                ?>
                                            </div>
                                            <div class="col-md-5" style="padding-left: 5px;">
                                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                                    <i class="fa fa-save"></i> Simpan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
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
    <div class="modal-dialog modal-lg">
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
                    <input type="hidden" value="" name="id_rencana_studi" />

                    <div class="row">
                        
                        <div class="col-md-5" style="border-right: 1px solid #eee;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div id="msg_foto" style="display: inline-block; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 4px;"></div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Nama:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_nm_pd" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">NIM:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_nipd" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">IPK Akhir:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_last_ipk" readonly style="font-weight: bold; color: #333;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Smt Tempuh:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_semester_tempuh" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Masa Studi:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_masa_studi" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Status KIP:</label>
                                <div class="col-md-8" id="msg_kip_info" style="padding-top: 7px;"></div>
                            </div>

                            <div id="msg_ips_chart" style="margin-top: 20px;"></div>
                        </div>

                        
                        <div class="col-md-7">
                            <h4 style="margin-top: 0;">Riwayat Pesan:</h4>
                            <div class="well" id="data_pesan" style="height: 300px; overflow-y: auto; background-color: #f9f9f9;">
                            </div>
                            
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label" style="font-weight: bold; margin-bottom: 10px; display: block; text-align: left;">Tulis Pesan Baru:</label>
                                    <div style="padding: 0 15px;">
                                        <textarea class="form-control" rows="4" name="catatan_dosen_wali" placeholder="Ketik pesan untuk mahasiswa di sini..."></textarea>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div style="text-align: right; padding: 0 15px;">
                                    <button type="submit" id="btnSubmitMsg" class="btn btn-primary">
                                        <i class="fa fa-paper-plane"></i> Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_perbaikan_krs" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Konfirmasi Buka Perbaikan KRS</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" style="margin-bottom: 20px;">
                    Mahasiswa: <strong id="nm_mhs_perbaikan"></strong> (<span id="nim_mhs_perbaikan"></span>)
                </div>
                <form id="form_perbaikan_krs" class="form-horizontal">
                    <input type="hidden" name="id_rencana_studi_perbaikan" id="id_rencana_studi_perbaikan">
                    <div class="form-group">
                        <label class="control-label col-md-12" style="text-align: left; font-weight: bold; margin-bottom: 10px;">Alasan Pembukaan Perbaikan KRS:</label>
                        <div class="col-md-12">
                            <textarea name="alasan_perbaikan" id="alasan_perbaikan" class="form-control" rows="4" placeholder="Contoh: Penambahan mata kuliah atas saran PA, atau Penyesuaian SKS..."></textarea>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
                    <i class="fa fa-info-circle"></i> Mahasiswa akan dapat mengedit kembali KRS mereka setelah Anda menekan tombol Konfirmasi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmPerbaikan" class="btn btn-danger">
                    <i class="fa fa-check"></i> Konfirmasi Buka Perbaikan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_foto" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body ">
                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="nipd" name="nipd">
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group ">
                            <img style="padding: 10px; border: 1px solid;display: block; margin: 0 auto;width: 400px;object-fit: cover;" id="pas_foto" src=""  alt="Card image cap">
                        </div>

                        <div class="form-group ">
                            <label for="nipd" class="col-sm-3 col-form-label">NIM</label>
                            <div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" id="nipd" name="nipd">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nm_pd" class="col-sm-3 col-form-label">Nama</label>
                            <div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" id="nm_pd" name="nm_pd">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<style>
    /* Fix background scroll when modal open */
    body.modal-open {
        overflow: hidden !important;
    }
    
    body.modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }
    
    /* Custom scrollbar for chat history */
    #data_pesan {
        scrollbar-width: thin;
        scrollbar-color: #ccc #f9f9f9;
        display: flex;
        flex-direction: column;
        padding: 15px;
    }
    #data_pesan::-webkit-scrollbar {
        width: 6px;
    }
    #data_pesan::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    
    /* Bubble Chat Styling */
    .chat-bubble {
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 12px;
        max-width: 85%;
        position: relative;
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .chat-right {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
        color: #333;
    }
    .chat-left {
        background-color: #ffffff;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        color: #333;
        border: 1px solid #e0e0e0;
    }
    .chat-name {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 3px;
        display: block;
        color: #555;
    }
    
    /* Modal content styling */
    #modal_form_balas_pesan .modal-body {
        padding: 20px;
    }

    /* Small bubble adjustment for datatables */
    .msg-history-container {
        display: flex;
        flex-direction: column;
    }
    .msg-history-container .chat-bubble {
        max-width: 95%;
        font-size: 11px;
        padding: 5px 8px;
        margin-bottom: 8px;
    }
    .msg-history-container .chat-name {
        font-size: 9px;
    }

    /* Compact view for KRS Approval Table */
    .compact-table table {
        font-size: 11px !important;
        margin-bottom: 0 !important;
    }
    .compact-table table th, .compact-table table td {
        padding: 4px 8px !important;
    }

    /* Responsive adjustments for 12-inch laptops and smaller screens */
    @media (max-width: 1366px) {
        #modal_form_krs .modal-dialog {
            width: 95% !important;
            margin: 10px auto !important;
        }
        #modal_form_krs .modal-body {
            padding: 15px 10px !important;
        }
        /* Adjust column padding for tighter fit */
        #modal_form_krs .col-md-5, #modal_form_krs .col-md-7 {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        /* Slightly reduce chat and chart space to fit vertically */
        #log_message {
            height: 160px !important;
        }
        #ips_chart {
            min-height: 220px !important;
        }
        /* Ensure font sizes stay readable but compact */
        .form-horizontal .control-label {
            font-size: 11px !important;
        }
        .form-horizontal .form-control {
            font-size: 11px !important;
            height: 28px !important;
            padding: 4px 8px !important;
        }
    }
</style>