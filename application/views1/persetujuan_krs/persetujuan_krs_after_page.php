<style>
    body.modal-open {
        overflow: hidden !important;
        height: 100vh;
    }
    
    .chat-bubble {
        max-width: 85% !important;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        position: relative;
        font-size: 13px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .chat-left {
        align-self: flex-start;
        background: #f0f0f0;
        color: #333;
        border-bottom-left-radius: 2px;
    }
    
    .chat-right {
        align-self: flex-end;
        background: #dcf8c6;
        color: #333;
        border-bottom-right-radius: 2px;
    }

    /* Ultra Compact Table for Modal */
    #modal_form_krs table.table {
        margin-bottom: 0 !important;
    }
    #modal_form_krs table.table th, 
    #modal_form_krs table.table td {
        padding: 4px 6px !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        vertical-align: middle !important;
    }
    #modal_form_krs table.table th {
        background-color: #f4f6f7 !important;
        color: #333 !important;
        font-weight: 700 !important;
    }
</style>

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
                    // Scroll ke atas tabel saat pindah halaman (Next/Prev/Search)
                    $('html, body').animate({
                        scrollTop: $("#datatable_ajax").offset().top - 150
                    }, 300);
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
            var $btn = $('#btnKirimPesan');
            var originalHtml = $btn.html();
            
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Mengirim...').attr('disabled', true);

            var url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_balas_pesan') ?>";

            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $('#form_balas_pesan').serialize(),
                success: function(data) {
                    $btn.html(originalHtml).attr('disabled', false);

                    if (data.status) {
                        var id_rencana_studi = $('#form_balas_pesan [name="id_rencana_studi"]').val();
                        var nipd = $('#msg_nipd').val();
                        var id_smt = "<?php echo $this->session->userdata('tahun_ajaran'); ?>";
                        
                        $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(msgData) {
                            $('#data_pesan').html(msgData.data_pesan);
                            $('#form_balas_pesan [name="catatan_dosen_wali"]').val('');
                            
                            var d = $('#data_pesan');
                            d.scrollTop(d.prop("scrollHeight"));
                        });
                    }
                },
                error: function() {
                    $btn.html(originalHtml).attr('disabled', false);
                }
            });
        });

        // Refresh datatable only when message modal is closed
        $('#modal_form_balas_pesan').on('hidden.bs.modal', function () {
            // reload_table(); // Dimatikan agar tidak menyebabkan scroll melompat
        });

        $('#form_persetujuan').submit(function(e) {
            e.preventDefault();
            var $btn = $('#btnSavePersetujuan');
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').attr('disabled', true);

            url = "<?php echo site_url('Lecturer_persetujuan_krs/persetujuan_krs') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_persetujuan').serialize(),
                success: function(data) {
                    if (data.status) {
                        // Stay in modal, hide action form elements
                        $('#form_persetujuan .form-group').hide();
                        $('#btnSavePersetujuan').hide();
                        
                        // Update summary and reload table in background
                        update_summary(data.summary);
                        reload_table();

                        // Refresh chat history to show system auto-notification
                        var id_rencana_studi = $('[name="id_rencana_studi"]').val();
                        var nipd = $('#nipd').val();
                        var id_smt = "<?php echo $this->session->userdata('tahun_ajaran'); ?>";
                        
                        $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_krs') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(refreshData) {
                            $('#log_message').html(refreshData.log_message);
                            setTimeout(function() {
                                var d = $('#log_message');
                                d.scrollTop(d.prop("scrollHeight"));
                            }, 100);
                        });
                    } else {
                        $btn.html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').attr('disabled', false);
                        alert('Gagal menyimpan persetujuan.');
                    }
                },
                error: function() {
                    $btn.html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').attr('disabled', false);
                    alert('Error processing request');
                }
            });
        });

        $('#form_perbaikan').submit(function(e) {
            e.preventDefault();
            var $btn = $('#btnSavePerbaikan');
            $btn.text('Processing...').attr('disabled', true);

            var id_rencana_studi = $('#perbaikan_id').val();
            var url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_perbaikan_krs') ?>/" + id_rencana_studi;

            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $('#form_perbaikan').serialize(),
                success: function(data) {
                    $btn.text('Simpan & Buka Perbaikan').attr('disabled', false);
                    if (data.status) {
                        $('#modal_perbaikan').modal('hide');
                        update_summary(data.summary);
                        reload_table();
                    }
                },
                error: function() {
                    $btn.text('Simpan & Buka Perbaikan').attr('disabled', false);
                    alert('Error processing request');
                }
            });
        });

        function update_summary(summary) {
            if (summary) {
                $('#summary_pending').text(summary.pending);
                $('#summary_at_risk').text(summary.at_risk);
                $('#summary_draft').text(summary.krs_draft);
            }
        }

        // Pastikan scroll selalu di atas saat modal baru dibuka
        $('#modal_form_krs').on('shown.bs.modal', function () {
            $(this).scrollTop(0);
            $('#right_column_scroll').scrollTop(0);
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

    function show_krs(id_rencana_studi, nipd, id_smt, mode) {
        // Cek lebar layar (Smartphone < 768px)
        if (window.innerWidth < 768) {
            alert("Maaf, modul Persetujuan KRS ini memiliki tampilan data yang kompleks dan tidak dapat dibuka melalui smartphone.\n\nHarap gunakan Tablet atau Laptop untuk melakukan Approval KRS.");
            return;
        }

        // mode: 'detail' atau 'approval'
        $('#form_persetujuan')[0].reset();
        $('#form_persetujuan .form-group').show();
        $('#btnSavePersetujuan').show();
        
        // Hide/Show elements based on mode
        if (mode === 'detail') {
            $('#approval_action_container').hide();
            $('#chat_column').removeClass('col-md-7').addClass('col-md-12');
            $('.modal-title').text("Detail Kartu Rencana Studi");
        } else {
            $('#approval_action_container').show();
            $('#chat_column').removeClass('col-md-12').addClass('col-md-7');
            $('.modal-title').text("Persetujuan Kartu Rencana Studi");
        }

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/view_krs') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                //$('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);
                //$('[name="id_status_krs"]').val(data.id_status_krs);
                $('#nm_pd').val(data.nm_pd);
                $('#nm_pd_text').text(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#nipd_text').text(data.nipd);
                $('#last_ipk').val(data.last_ipk);
                $('#last_ipk_text').text(data.last_ipk);
                $('#semester_tempuh').val(data.semester_tempuh);
                $('#semester_tempuh_text').text(data.semester_tempuh + " Semester");
                $('#tabel_krs').html(data.tabel_krs);
                $('#attention').html(data.attention);
                $('#attention_badge').text(data.failed_count || 0);
                $('#academic_summary_content').html(data.academic_summary);
                
                // Switch to KRS tab by default (Primary focus)
                $('.nav-tabs a[href="#tab_krs"]').tab('show');

                $('#tabel_akademik_record').html(data.tabel_akademik_record);
                $('#log_message').html(data.log_message);
                $('#foto').html(data.pic);
                $('#kip_info').html(data.kip_status);
                $('#risk_info').html(data.risk_status);
                $('#masa_studi_text').text(data.masa_studi_text);
                $('#catatan_dosen_wali').val('');

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
                        toolbar: { show: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 800 }
                    },
                    // Dynamic coloring: warm color for IPS < 2.7
                    colors: data.ips_values.map(function(val) {
                        return val < 2.7 ? '#e67e22' : '#3498db';
                    }),
                    grid: { borderColor: '#f1f1f1' },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '40%', // Narrower bars for cleaner look
                            distributed: true,  // Apply colors to each bar
                            dataLabels: { position: 'top' }
                        }
                    },
                    legend: { show: false },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        style: { fontSize: '10px', colors: ['#333'] }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        labels: { style: { fontSize: '9px' } }
                    },
                    yaxis: { max: 4, tickAmount: 4 },
                    title: {
                        text: 'Riwayat IPS (Index Prestasi Semester)',
                        align: 'center',
                        style: { fontSize: '12px', fontWeight: 'bold', color: '#2c3e50' }
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
                $('#msg_nm_pd_text').text(data.nm_pd);
                $('#msg_nipd').val(data.nipd);
                $('#msg_nipd_text').text(data.nipd);
                $('#msg_last_ipk').val(data.last_ipk);
                $('#msg_last_ipk_text').text(data.last_ipk);
                $('#msg_semester_tempuh').val(data.semester_tempuh + " Semester");
                $('#msg_semester_tempuh_text').text(data.semester_tempuh + " Semester");
                $('#msg_masa_studi').val(data.masa_studi_text);
                $('#msg_masa_studi_text').text(data.masa_studi_text);
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

    function perbaikan_krs(id, name, nim) {
        $('#form_perbaikan')[0].reset();
        $('#perbaikan_id').val(id);
        $('#perbaikan_nm_pd').val(name);
        $('#perbaikan_nipd').val(nim);
        
        $('#modal_perbaikan').modal('show');
    }

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

                $("#pas_foto").attr("src", "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto);

                $('#modal_foto').modal('show');
                $('.modal-title').text("Pas foto Mahasiswa");

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
        var smt = parseInt(currentStudentData.semester_tempuh) || 0;
        var name = currentStudentData.nm_pd;
        var ips_vals = currentStudentData.ips_values || [];
        var ipk_val = parseFloat(ipk) || 0;
        var is_maba = (smt === 0 || ips_vals.length === 0);
        
        var text = "[PERWALIAN]\n";
        text += "Halo " + name + ", berikut adalah beberapa catatan akademik untuk perwalian semester ini:\n\n";
        
        if (is_maba) {
            // Khusus Mahasiswa Baru (Semester 1 / Belum ada KHS)
            text += "1. CAPAIAN AKADEMIK: Selamat datang di Politeknik WBI. Anda saat ini memulai perkuliahan semester perdana. Bangun kedisiplinan dan adaptasi yang baik terhadap ritme pembelajaran di perguruan tinggi. Targetkan capaian IPS semester pertama minimal 3.25 agar memiliki modal prestasi yang kuat.\n\n";
            
            text += "2. MATA KULIAH: Seluruh mata kuliah pada semester 1 ini merupakan paket dasar penting. Pastikan hadir tepat waktu di setiap sesi, aktif berdiskusi di kelas, dan menyelesaikan semua tugas serta praktikum dengan optimal.\n\n";
            
            text += "3. MASA STUDI: Anda saat ini memulai Semester 1. Bangun fondasi akademik yang solid sejak awal agar perjalanan studi Anda lancar dan dapat lulus tepat waktu (Target 8 Semester).";
        } else {
            // Mahasiswa Semester 2 ke atas (Sudah memiliki riwayat KHS)
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
                    trend_text = " Saya melihat tren IPS Anda sedang meningkat (dari " + prev_ips.toFixed(2) + " ke " + last_ips.toFixed(2) + "), pertahankan momentum positif ini!";
                } else if (last_ips < prev_ips) {
                    trend_text = " Waspada terhadap tren IPS yang sedikit menurun dari semester sebelumnya, mari kita perbaiki di semester ini.";
                }
            }

            var badge_ungguls = (ipk_val >= 3.50) ? "[⭐⭐⭐⭐⭐] POTENSI LULUS TEPAT WAKTU\n   " : "";

            text += "1. CAPAIAN AKADEMIK: IPK Anda saat ini adalah " + ipk + "." + trend_text + " \n   " + badge_ungguls + advice_text + " Targetkan IPS semester depan minimal " + target_ips + ".\n\n";
            
            // Failed Courses Insight
            if (failed > 0) {
                text += "2. MATA KULIAH: Ada " + failed + " mata kuliah yang belum lulus (Nilai < 56). Saya instruksikan agar mata kuliah tersebut segera diambil kembali (mengulang) pada semester ini atau semester antara agar tidak menghambat kelulusan.\n\n";
            } else {
                text += "2. MATA KULIAH: Seluruh mata kuliah sebelumnya sudah lulus dengan baik. Tetap fokus pada mata kuliah yang baru diajukan ini.\n\n";
            }
            
            // Progress Insight
            text += "3. MASA STUDI: Anda saat ini berada di semester " + smt + ". Manfaatkan waktu dengan baik agar bisa menyelesaikan tugas akhir tepat waktu (Target 8 Semester).";
        }
        
        // KIP Insight
        if (currentStudentData.kip_status && (currentStudentData.kip_status.indexOf('label-warning') !== -1 || currentStudentData.kip_status.indexOf('bs5-badge-warning') !== -1 || currentStudentData.kip_status.indexOf('Peserta KIP') !== -1)) {
            text += "\n\n4. KIP KULIAH: Sebagai penerima bantuan KIP, pastikan prestasi dan kehadiran tetap optimal agar pembiayaan beasiswa berjalan lancar.";
        }

        $('#catatan_perwalian_input').val(text);
        $('#perwalian_btn_container').hide(); // Hide once used
    }
</script>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1300px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: #2c3e50; color: white; padding: 12px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 1;"></button>
                <h4 class="modal-title" style="font-weight: 600;">Kartu Rencana Studi / Card Of Study Plan</h4>
            </div>
            <div class="modal-body" style="padding: 0; background: #fff;">
                <div style="display: flex; flex-wrap: wrap; height: auto;">
                    
                    <div class="col-md-3 col-sm-4" style="background: #f8f9fa; padding: 20px; border-right: 1px solid #eee;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div id="foto" style="display: inline-block; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 4px;"></div>
                        </div>
                        
                        <div class="profile-details">
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">Nama Lengkap:</label>
                                <span id="nm_pd_text" style="font-weight: 700; color: #333; display: block; line-height: 1.2;"></span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">NIM:</label>
                                <span id="nipd_text" style="font-weight: 600; color: #555; display: block;"></span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">IPK Terakhir:</label>
                                <span id="last_ipk_text" style="font-weight: 800; color: #2c3e50; font-size: 16px; display: block;"></span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">Status KIP:</label>
                                <div id="kip_info"></div>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">Semester Tempuh:</label>
                                <span id="semester_tempuh_text" style="color: #444; font-weight: 600; display: block;"></span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">Masa Studi:</label>
                                <span id="masa_studi_text" style="color: #444; display: block;"></span>
                            </div>
                            <div style="margin-bottom: 0;">
                                <label style="font-size: 11px; color: #888; display: block; margin-bottom: 2px;">Resiko Lulus:</label>
                                <div id="risk_info"></div>
                            </div>
                        </div>
                    </div>

                        
                        <div class="col-md-9 col-sm-8" id="right_column_scroll" style="padding: 20px;">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabbable-line">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab_krs" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-list-alt"></i> Mata Kuliah Diajukan </a>
                                            </li>
                                            <li>
                                                <a href="#tab_attention" data-toggle="tab" style="font-weight: 600;"> 
                                                    <i class="fa fa-exclamation-triangle"></i> MK Perlu Perhatian 
                                                    <span id="attention_badge" class="badge badge-danger" style="margin-left: 5px; font-weight: 700;">0</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_chart" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-line-chart"></i> Tren Akademik </a>
                                            </li>
                                            <li>
                                                <a href="#tab_narasi" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-file-text-o"></i> Narasi Akademik </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" style="padding: 15px 0;">
                                            
                                            <div class="tab-pane active" id="tab_krs">
                                                <div class="table-responsive compact-table" id="tabel_krs" style="margin-bottom: 10px; border: 1px solid #eee; border-radius: 4px; max-height: 450px; overflow-y: auto; background: #fff;"></div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_attention">
                                                <div id="attention" class="compact-table" style="min-height: 270px; max-height: 450px; overflow-y: auto; border: 1px solid #f2dede; border-radius: 4px; background: #fff5f5; padding: 15px;"></div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_chart">
                                                <div class="row">
                                                    <div class="col-md-8 col-md-offset-2">
                                                        <div id="ips_chart" style="min-height: 250px; border: 1px solid #f0f0f0; border-radius: 4px; padding: 10px; background: #fff;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_narasi">
                                                <div id="academic_summary_content" style="min-height: 270px; padding: 20px; line-height: 1.8; font-size: 14px; color: #34495e; background: #fff; border: 1px solid #eee; border-radius: 4px; text-align: justify;">
                                                    Memuat narasi akademik...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        
                        <div class="row" style="margin-top: 10px;">
                            
                            <div class="col-md-7 col-sm-12" id="chat_column">
                                <h5 style="font-weight: 700; color: #2c3e50; margin-top: 15px; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                                    <i class="fa fa-comments"></i> Riwayat Perwalian:
                                </h5>
                                <div id="log_message" style="height: 380px; overflow-y: auto; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 20px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                </div>
                            </div>
                            
                            
                            <div class="col-md-5 col-sm-12" id="approval_action_container" style="margin-top: 15px;">
                                <h5 style="font-weight: 700; color: #2c3e50; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                                    <i class="fa fa-pencil-square-o"></i> Form Persetujuan:
                                </h5>
                                <?php
                                $attributes = ['class' => 'form', 'id' => 'form_persetujuan'];
                                echo form_open('#', $attributes);
                                ?>
                                <input type="hidden" value="" name="id_rencana_studi" />
                                
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 8px; display: block;">Catatan / Pesan Dosen Wali:</label>
                                    <textarea class="form-control" rows="8" name="catatan_dosen_wali" id="catatan_perwalian_input" style="resize: none; font-size: 13px; border-radius: 8px; border: 1px solid #ccc; padding: 12px;" placeholder="Ketik pesan perwalian di sini..."></textarea>
                                    <div id="perwalian_btn_container" style="margin-top: 10px; text-align: right; display: none;">
                                        <button type="button" class="btn btn-xs btn-info" onclick="generate_perwalian_insight()" style="border-radius: 20px; padding: 5px 15px; font-weight: 600;">
                                            <i class="fa fa-magic"></i> Saran Perwalian
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 8px; display: block;">Status Persetujuan:</label>
                                    <?php
                                    echo form_dropdown('id_status_krs', $dd_status_krs, '', 'class="form-control" style="border-radius: 6px; height: 40px; border: 1px solid #ccc;" required');
                                    ?>
                                </div>

                                <button type="submit" id="btnSavePersetujuan" class="btn btn-primary btn-block" style="border-radius: 6px; height: 45px; font-weight: 700; background-color: #2980b9; border: none; box-shadow: 0 4px 6px rgba(41, 128, 185, 0.2);">
                                    <i class="fa fa-check-circle"></i> Simpan Persetujuan
                                </button>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_perbaikan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
            <div class="modal-header" style="background: #e74c3c; color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 1;"></button>
                <h4 class="modal-title" style="font-weight: 600;"><i class="fa fa-edit"></i> Buka Perbaikan KRS</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['id' => 'form_perbaikan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" name="id_rencana_studi" id="perbaikan_id">
                <div class="form-group">
                    <label style="font-size: 12px; color: #888;">Mahasiswa:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="perbaikan_nipd" class="form-control input-sm" readonly style="width: 120px; font-weight: bold; background: #f9f9f9;">
                        <input type="text" id="perbaikan_nm_pd" class="form-control input-sm" readonly style="background: #f9f9f9;">
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #333;">Alasan / Catatan Perbaikan:</label>
                    <textarea name="alasan" class="form-control" rows="4" placeholder="Contoh: Silakan hapus mata kuliah X dan ganti dengan Y karena jadwal bentrok." required style="resize: none; border-radius: 4px;"></textarea>
                    <span class="help-block" style="font-size: 11px;">Catatan ini akan muncul di riwayat pesan mahasiswa.</span>
                </div>
                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSavePerbaikan" class="btn btn-danger" style="font-weight: 600;">Simpan & Buka Perbaikan</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(45deg, #2c3e50, #34495e); color: white; padding: 15px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 1; margin-top: 5px !important;"></button>
                <h4 class="modal-title" style="font-weight: 600; letter-spacing: 0.5px;">Kirim Pesan ke Mahasiswa</h4>
            </div>
            <div class="modal-body" style="background: #fcfcfc; padding: 20px;">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_balas_pesan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_rencana_studi" />

                <div class="row">
                    
                    <div class="col-md-5 col-sm-12 profile-column" style="border-right: 1px solid #eee; margin-bottom: 20px;">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <div id="msg_foto" style="display: inline-block; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 4px;"></div>
                        </div>

                        
                        <div id="msg_ips_chart" style="margin-bottom: 20px; min-height: 200px;"></div>
                        
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="control-label col-sm-4 col-xs-4" style="text-align: left; font-size: 12px;">Nama:</label>
                            <div class="col-sm-8 col-xs-8">
                                <input class="form-control input-sm" id="msg_nm_pd" readonly style="background: transparent; border: none; font-weight: 600;" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="control-label col-sm-4 col-xs-4" style="text-align: left; font-size: 12px;">NIM:</label>
                            <div class="col-sm-8 col-xs-8">
                                <input class="form-control input-sm" id="msg_nipd" readonly style="background: transparent; border: none;" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="control-label col-sm-4 col-xs-4" style="text-align: left; font-size: 12px;">IPK Akhir:</label>
                            <div class="col-sm-8 col-xs-8">
                                <input class="form-control input-sm" id="msg_last_ipk" readonly style="background: transparent; border: none; font-weight: bold; color: #2c3e50;" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="control-label col-sm-4 col-xs-4" style="text-align: left; font-size: 12px;">Smt Tempuh:</label>
                            <div class="col-sm-8 col-xs-8">
                                <input class="form-control input-sm" id="msg_semester_tempuh" readonly style="background: transparent; border: none;" />
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 5px;">
                            <label class="control-label col-sm-4 col-xs-4" style="text-align: left; font-size: 12px;">Status KIP:</label>
                            <div class="col-sm-8 col-xs-8" id="msg_kip_info" style="padding-top: 7px;"></div>
                        </div>
                    </div>

                    
                    <div class="col-md-7 col-sm-12">
                        <h4 style="margin-top: 0; font-size: 16px; font-weight: 600; color: #2c3e50;"><i class="fa fa-history"></i> Riwayat Pesan:</h4>
                        <div class="well" id="data_pesan" style="height: 300px; overflow-y: auto; background-color: #fff; border: 1px solid #eee; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 15px; padding: 15px;">
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label class="control-label" style="font-weight: 600; margin-bottom: 8px; display: block; text-align: left; color: #555;">Tulis Pesan:</label>
                            <div style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex-grow: 1;">
                                    <textarea class="form-control" rows="2" name="catatan_dosen_wali" placeholder="Ketik pesan di sini..." style="resize: none; border-radius: 4px; border: 1px solid #ddd;"></textarea>
                                </div>
                                <button type="submit" id="btnKirimPesan" class="btn btn-primary" style="height: 54px; padding: 0 20px; border-radius: 4px; font-weight: 600; transition: all 0.2s;">
                                    <i class="fa fa-paper-plane"></i> Kirim
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
            <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #eee;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 4px;">Tutup</button>
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
    /* Responsive Layout for Tablet & Mobile */
    @media (max-width: 991px) {
        .profile-column {
            padding-bottom: 20px;
            border-right: none !important;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
    }

    /* Bubble Chat Styling */
    .chat-bubble {
        padding: 10px 14px;
        border-radius: 18px;
        margin-bottom: 12px;
        max-width: 85%;
        position: relative;
        font-size: 13px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        word-wrap: break-word;
    }
    .chat-right {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
        margin-left: auto;
        color: #333;
    }
    .chat-left {
        background-color: #f1f0f0;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        color: #333;
    }
    .chat-name {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 4px;
        display: block;
        color: #666;
    }
    #data_pesan {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
</style>