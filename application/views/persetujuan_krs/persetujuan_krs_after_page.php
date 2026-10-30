<style>
    body.modal-open {
        overflow: hidden !important;
        height: 100vh;
    }

    /* Modal Form KRS Height & Scrolling Constraints */
    #modal_form_krs .modal-dialog {
        width: 95% !important;
        max-width: 1300px !important;
        margin: 20px auto !important;
    }
    #modal_form_krs .modal-content {
        max-height: calc(100vh - 40px) !important;
        display: flex !important;
        flex-direction: column !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    #modal_form_krs .modal-header {
        flex-shrink: 0 !important;
    }
    #modal_form_krs .modal-body {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        max-height: calc(100vh - 115px) !important;
        padding: 0 !important;
    }
    #modal_form_krs .modal-footer {
        flex-shrink: 0 !important;
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

    /* Action Dropdown Styling */
    .table-scrollable, .table-responsive-2, .table-container, .dataTables_wrapper, .portlet-body {
        overflow: visible !important;
    }
    #datatable_ajax tbody tr .btn-group {
        position: relative;
    }
    #datatable_ajax .btn-group.open {
        z-index: 1050 !important;
    }
    #datatable_ajax .btn-group > .btn {
        width: 95px !important;
        text-align: center !important;
    }
    #datatable_ajax .btn-group .dropdown-menu {
        z-index: 9999 !important;
        min-width: 165px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 0;
    }
    #datatable_ajax .btn-group.dropup .dropdown-menu {
        top: auto !important;
        bottom: 100% !important;
        margin-bottom: 4px !important;
        margin-top: 0 !important;
        box-shadow: 0 -10px 25px -5px rgba(0,0,0,0.15), 0 -8px 10px -6px rgba(0,0,0,0.1) !important;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a {
        padding: 7px 14px;
        font-size: 12px;
        color: #334155;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    #datatable_ajax .btn-group .dropdown-menu > li > a i {
        margin-right: 8px;
        width: 14px;
        text-align: center;
    }
</style>

<script type="text/javascript">
    var save_method;
    var table;

    function handleStatusKrsChange() {
        var status = $('#form_persetujuan [name="id_status_krs"]').val();
        var $btnSaran = $('#btn_saran_perwalian');
        var $textarea = $('#catatan_perwalian_input');
        var $btnSave = $('#btnSavePersetujuan');

        if (status === '3') {
            // 1. Final (Disetujui): Tombol saran aktif, pesan minimal 5 kata
            $btnSaran.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer', 'pointer-events': 'auto' }).show();
            $textarea.attr('placeholder', 'Ketik pesan perwalian di sini (minimal 5 kata)...');
            $btnSave.html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').css('background-color', '#2980b9');
        } else if (status === '1') {
            // 3. Kesempatan Edit: Tombol saran disabled, catatan wajib ada
            $btnSaran.prop('disabled', true).css({ 'opacity': '0.35', 'cursor': 'not-allowed', 'pointer-events': 'none' });
            $textarea.attr('placeholder', 'Ketik catatan / arahan perbaikan KRS untuk mahasiswa (wajib diisi)...');
            $btnSave.html('<i class="fa fa-undo"></i> Beri Kesempatan Edit').css('background-color', '#d97706');
        } else if (status === '4') {
            // 2. Ditolak: Tombol saran disabled, alasan wajib ada
            $btnSaran.prop('disabled', true).css({ 'opacity': '0.35', 'cursor': 'not-allowed', 'pointer-events': 'none' });
            $textarea.attr('placeholder', 'Ketik alasan penolakan KRS di sini (wajib diisi)...');
            $btnSave.html('<i class="fa fa-times-circle"></i> Simpan Penolakan').css('background-color', '#dc2626');
        }
        validateCatatanPerwalian(false);
    }
    window.handleStatusKrsChange = handleStatusKrsChange;

    function validateCatatanPerwalian(showWarning) {
        var status = $('#form_persetujuan [name="id_status_krs"]').val();
        var pesan = ($('#catatan_perwalian_input').val() || '').trim();

        if (status === '3') {
            // Final (Disetujui): Minimal 5 kata
            var words = pesan.length > 0 ? pesan.split(/\s+/).filter(function(w) { return w.length > 0; }) : [];
            if (words.length < 5) {
                var countText = (words.length === 0) ? 'masih kosong' : words.length + ' kata';
                $('#catatan_warning_text').html('Catatan perwalian pada status Final (Disetujui) wajib diisi minimal <b>5 kata</b> (saat ini: <b>' + countText + '</b>). Mohon berikan arahan atau klik tombol <b><i class="fa fa-magic"></i> Saran Perwalian</b> di atas.');
                if (showWarning || words.length > 0) {
                    $('#catatan_warning_box').show();
                    $('#catatan_perwalian_input').css({ 'border-color': '#f59e0b', 'box-shadow': '0 0 0 2px rgba(245, 158, 11, 0.2)' });
                }
                return false;
            } else {
                $('#catatan_warning_box').hide();
                $('#catatan_perwalian_input').css({ 'border-color': '#10b981', 'box-shadow': 'none' });
                return true;
            }
        } else if (status === '1') {
            // Kesempatan Edit: Wajib ada isi
            if (pesan.length === 0) {
                $('#catatan_warning_text').html('Catatan / arahan perbaikan KRS <b>wajib diisi</b> agar mahasiswa mengetahui bagian yang harus diedit.');
                if (showWarning) {
                    $('#catatan_warning_box').show();
                    $('#catatan_perwalian_input').css({ 'border-color': '#f59e0b', 'box-shadow': '0 0 0 2px rgba(245, 158, 11, 0.2)' });
                }
                return false;
            } else {
                $('#catatan_warning_box').hide();
                $('#catatan_perwalian_input').css({ 'border-color': '#10b981', 'box-shadow': 'none' });
                return true;
            }
        } else if (status === '4') {
            // Ditolak: Wajib ada isi
            if (pesan.length === 0) {
                $('#catatan_warning_text').html('Alasan penolakan pengajuan KRS <b>wajib diisi</b> sebagai penjelasan bagi mahasiswa.');
                if (showWarning) {
                    $('#catatan_warning_box').show();
                    $('#catatan_perwalian_input').css({ 'border-color': '#f59e0b', 'box-shadow': '0 0 0 2px rgba(245, 158, 11, 0.2)' });
                }
                return false;
            } else {
                $('#catatan_warning_box').hide();
                $('#catatan_perwalian_input').css({ 'border-color': '#10b981', 'box-shadow': 'none' });
                return true;
            }
        }
        return true;
    }
    window.validateCatatanPerwalian = validateCatatanPerwalian;

    function filterByStatusDraft() {
        $('#ddstatus').val('1').trigger('change');
        $('html, body').animate({
            scrollTop: $("#datatable_ajax").offset().top - 120
        }, 300);
    }
    window.filterByStatusDraft = filterByStatusDraft;

    var TableAjax = function() {
        var handleRecords = function() {
            var angkatan = $('#ddAngkatan').val() || 0;
            var status_krs = $('#ddstatus').val() || 0;

            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('Lecturer_persetujuan_krs/ajax_list') ?>" + "/" + status_krs + "/" + angkatan,
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": "_all",
                    "orderable": false,
                }, {
                    "targets": 0,
                    "width": 105,
                    "className": "text-center"
                }, {
                    "targets": [4, 5],
                    "width": 95,
                }, {
                    "targets": 6,
                    "width": 75,
                    "className": "text-center"
                }],
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

        $('select[name=ddstatus]').on('change', function() {
            TableAjax.init();
        });

        $("input").change(function() {
            $(this).closest('.form-group').removeClass('has-error');
            $(this).siblings('.help-block').empty();
        });
        $("textarea").change(function() {
            $(this).closest('.form-group').removeClass('has-error');
            $(this).siblings('.help-block').empty();
        });
        $("select").change(function() {
            $(this).closest('.form-group').removeClass('has-error');
            $(this).siblings('.help-block').empty();
        });

        // Auto-detect Dropup vs Dropdown based on viewport space
        $(document).on('show.bs.dropdown', '#datatable_ajax .btn-group', function() {
            var $btnGroup = $(this);
            var $btn = $btnGroup.find('.btn-action-dropdown');
            if ($btn.length === 0) $btn = $btnGroup.find('.dropdown-toggle');
            
            var offsetTop = $btn.offset().top;
            var scrollTop = $(window).scrollTop();
            var windowHeight = $(window).height();
            var spaceBelow = windowHeight - (offsetTop - scrollTop + $btn.outerHeight());

            // If remaining space below is less than 170px, open upwards (dropup)
            if (spaceBelow < 170) {
                $btnGroup.addClass('dropup');
            } else {
                $btnGroup.removeClass('dropup');
            }
        });

        $(document).on('hide.bs.dropdown', '#datatable_ajax .btn-group', function() {
            $(this).removeClass('dropup');
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

        $(document).on('change', '#form_persetujuan [name="id_status_krs"]', function() {
            handleStatusKrsChange();
        });

        $(document).on('input keyup paste change', '#catatan_perwalian_input', function() {
            validateCatatanPerwalian(false);
        });

        $('#form_persetujuan').submit(function(e) {
            e.preventDefault();

            if (!validateCatatanPerwalian(true)) {
                $('#catatan_warning_box').show();
                $('#catatan_perwalian_input').focus();
                return false;
            }

            var $btn = $('#btnSavePersetujuan');
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

            var url = "<?php echo site_url('Lecturer_persetujuan_krs/persetujuan_krs') ?>";

            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $('#form_persetujuan').serialize(),
                success: function(data) {
                    $btn.html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').prop('disabled', false);

                    if (data.status) {
                        // Stay in modal, hide action form elements
                        $('#form_persetujuan .form-group').hide();
                        $('#btnSavePersetujuan').hide();
                        $('#catatan_warning_box').hide();
                        
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
                        var errMsg = data.message || 'Gagal menyimpan persetujuan.';
                        $('#catatan_warning_text').html(errMsg);
                        $('#catatan_warning_box').show();
                        $('#catatan_perwalian_input').focus();
                    }
                },
                error: function() {
                    $btn.html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').prop('disabled', false);
                    $('#catatan_warning_text').html('Terjadi kesalahan koneksi server.');
                    $('#catatan_warning_box').show();
                }
            });
        });

        $('#form_perbaikan').submit(function(e) {
            e.preventDefault();
            var $btn = $('#btnSavePerbaikan');
            $btn.text('Processing...').prop('disabled', true);

            var id_rencana_studi = $('#perbaikan_id').val();
            var url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_perbaikan_krs') ?>/" + id_rencana_studi;

            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $('#form_perbaikan').serialize(),
                success: function(data) {
                    $btn.text('Simpan & Buka Perbaikan').prop('disabled', false);
                    if (data.status) {
                        $('#modal_perbaikan').modal('hide');
                        update_summary(data.summary);
                        reload_table();
                    }
                },
                error: function() {
                    $btn.text('Simpan & Buka Perbaikan').prop('disabled', false);
                    alert('Error processing request');
                }
            });
        });

        function update_summary(summary) {
            if (summary) {
                $('#summary_pending').text(summary.pending);
                $('#summary_approved').text(summary.approved);
                if (summary.total_krs !== undefined) {
                    $('#summary_total_krs').text(summary.total_krs);
                }
                $('#summary_at_risk').text(summary.at_risk);
                $('#summary_draft').text(summary.krs_draft);
                if (summary.krs_draft > 0) {
                    $('#alert_draft_krs').show();
                    $('#alert_draft_count').text(summary.krs_draft);
                } else {
                    $('#alert_draft_krs').hide();
                }
            }
        }

        // Pastikan scroll selalu di atas saat modal baru dibuka
        $('#modal_form_krs').on('shown.bs.modal', function () {
            $(this).scrollTop(0);
            $('#right_column_scroll').scrollTop(0);
        });

        $('#modal_form_krs').on('hidden.bs.modal', function () {
            $('#btnSavePersetujuan').html('<i class="fa fa-check-circle"></i> Simpan Persetujuan').prop('disabled', false);
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

        // Reset state mahasiswa saat modal baru dibuka
        currentStudentData = null;
        $('#btn_saran_perwalian').prop('disabled', false);

        // mode: 'detail' atau 'approval'
        $('#form_persetujuan')[0].reset();
        $('#catatan_warning_box').hide();
        $('#catatan_perwalian_input').css({ 'border-color': '#ccc', 'box-shadow': 'none' });
        $('#form_persetujuan [name="id_status_krs"]').val('3');
        handleStatusKrsChange();
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
                currentStudentData = data;
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                //$('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);
                //$('[name="id_status_krs"]').val(data.id_status_krs);
                $('#nm_pd').val(data.nm_pd);
                $('#nm_pd_text').text(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#nipd_text').text(data.nipd);
                $('#btn_modal_academic_record').attr('href', '<?php echo site_url("Lecturer_data_perwalian/academic_record/") ?>/' + data.nipd);
                if (data.is_draft || data.id_status_krs == 1 || data.id_status_krs == 0) {
                    $('.modal-title').html('Detail Kartu Rencana Studi <span class="label" style="background-color: #ea580c; color: #fff; font-size: 11px; margin-left: 8px; vertical-align: middle; padding: 2px 8px; border-radius: 3px;"><i class="fa fa-clock-o"></i> Status Draft (Belum Submit)</span>');
                }
                $('#last_ipk').val(data.last_ipk);
                $('#last_ipk_text').html(data.last_ipk + (data.star_rating_html ? ' ' + data.star_rating_html : ''));
                $('#semester_tempuh').val(data.semester_tempuh);
                $('#semester_tempuh_text').text(data.semester_tempuh + " Semester");
                $('#tabel_krs').html(data.tabel_krs);
                $('#attention').html(data.attention);
                $('#attention_badge').text(data.failed_count || 0);
                $('#academic_summary_content').html(data.academic_summary);
                
                // Switch to KRS tab by default (Primary focus)
                $('.nav-tabs a[href="#tab_krs"]').tab('show');

                // Sembunyikan tab Perwalian jika status masih Daftar (1 atau 0)
                if (data.id_status_krs == 1 || data.id_status_krs == 0) {
                    $('#li_tab_perwalian').hide();
                } else {
                    $('#li_tab_perwalian').show();
                }

                // Populate Resume KRS Card (di atas Riwayat Perwalian)
                if (data.krs_review) {
                    var kr = data.krs_review;
                    $('#resume_total_mk').text((kr.total_mk || 0) + ' MK');
                    $('#resume_total_sks').text((kr.total_sks || 0) + ' SKS');
                    $('#resume_mk_paket').text((kr.mk_current || 0) + ' MK');
                    $('#resume_mk_repeat').text((kr.mk_repeat || 0) + ' MK');
                    var sisaDE = (data.unresolved_de_count !== undefined) ? data.unresolved_de_count : (data.failed_count || 0);
                    $('#resume_mk_failed').text(sisaDE + ' MK');

                    var doubleCount = kr.duplicate_count || 0;
                    $('#resume_mk_double').text(doubleCount + ' MK');
                    if (doubleCount > 0) {
                        $('#resume_mk_double').css({ 'color': '#dc2626', 'font-weight': '800' });
                        $('#box_resume_double_mk').css({ 'background': '#fef2f2', 'border-color': '#fecaca' });
                    } else {
                        $('#resume_mk_double').css({ 'color': '#64748b', 'font-weight': '700' });
                        $('#box_resume_double_mk').css({ 'background': '#f8fafc', 'border-color': '#f1f5f9' });
                    }

                    if (kr.mk_repeat > 0) {
                        $('#resume_mk_repeat').css({ 'color': '#ea580c', 'font-weight': '800' });
                    } else {
                        $('#resume_mk_repeat').css({ 'color': '#64748b', 'font-weight': '700' });
                    }

                    if (sisaDE > 0) {
                        $('#resume_mk_failed').css({ 'color': '#dc2626', 'font-weight': '800' });
                    } else {
                        $('#resume_mk_failed').css({ 'color': '#64748b', 'font-weight': '700' });
                    }

                    var badgeStatusHtml = '';
                    var semPaketLabel = (kr.cohort_semester && kr.cohort_semester != kr.expected_semester) ? kr.cohort_semester : kr.expected_semester;
                    if (doubleCount > 0) {
                        badgeStatusHtml = '<span class="badge" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;"><i class="fa fa-exclamation-triangle" style="color: #dc2626;"></i> ' + doubleCount + ' Double MK Terdeteksi</span>';
                    } else if (kr.mk_repeat > 0) {
                        badgeStatusHtml = '<span class="badge" style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;"><i class="fa fa-repeat" style="color: #ea580c;"></i> ' + kr.mk_repeat + ' MK Mengulang</span>';
                    } else if (kr.mk_current == kr.total_mk && kr.total_mk > 0) {
                        badgeStatusHtml = '<span class="badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;"><i class="fa fa-check-circle" style="color: #10b981;"></i> 100% Sesuai Paket Smt ' + (semPaketLabel || '') + '</span>';
                    } else if (kr.mk_advance > 0) {
                        badgeStatusHtml = '<span class="badge" style="background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;"><i class="fa fa-check-circle" style="color: #7c3aed;"></i> ' + kr.mk_advance + ' MK Lanjutan</span>';
                    } else {
                        badgeStatusHtml = '<span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Paket Smt ' + (semPaketLabel || '') + '</span>';
                    }
                    $('#resume_krs_status_badge').html(badgeStatusHtml);

                    var notesHtml = '';
                    if (kr.duplicate_mks && kr.duplicate_mks.length > 0) {
                        var dupDetails = kr.duplicate_mks.map(function(d) {
                            return '<strong>' + d.nm_mk + '</strong> (Kode: ' + d.kode_mk + ' &bull; Kelas: ' + d.classes.join(', ') + ')';
                        }).join('; ');

                        notesHtml += '<div style="color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 5px 8px; margin-top: 6px; display: flex; align-items: flex-start; gap: 6px;">'
                                  + '<i class="fa fa-exclamation-triangle" style="color: #dc2626; font-size: 12px; margin-top: 2px; flex-shrink: 0;"></i>'
                                  + '<span><strong>Peringatan Double MK:</strong> Mahasiswa mengambil <strong>' + kr.duplicate_mks.length + ' MK ganda</strong> dengan kode yang sama: ' + dupDetails + '.</span>'
                                  + '</div>';
                    }
                    if (kr.missing_labels && kr.missing_labels.length > 0) {
                        notesHtml += '<div style="color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 5px 8px; margin-top: 6px; display: flex; align-items: flex-start; gap: 6px;">'
                                  + '<i class="fa fa-info-circle" style="color: #d97706; font-size: 12px; margin-top: 2px; flex-shrink: 0;"></i>'
                                  + '<span><strong>Riwayat Perkuliahan:</strong> Tidak aktif/cuti pada <strong>' + kr.missing_labels.join(', ') + '</strong>. Mengikuti paket Smt ' + (kr.cohort_semester || '') + '.</span>'
                                  + '</div>';
                    }
                    if (kr.not_taken_names && kr.not_taken_names.length > 0 && kr.total_sks < 22) {
                        notesHtml += '<div style="color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 4px 8px; margin-top: 6px; display: flex; align-items: center; gap: 5px;">'
                                  + '<i class="fa fa-info-circle" style="color: #d97706; font-size: 11px; flex-shrink: 0;"></i>'
                                  + '<span>MK paket semester ' + kr.expected_semester + ' belum diambil: <strong>' + kr.not_taken_names.join(', ') + '</strong></span>'
                                  + '</div>';
                    }
                    if (sisaDE > 0 && kr.mk_repeat == 0) {
                        notesHtml += '<div style="color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 4px 8px; margin-top: 6px; display: flex; align-items: center; gap: 5px;">'
                                  + '<i class="fa fa-exclamation-triangle" style="color: #ef4444; font-size: 11px; flex-shrink: 0;"></i>'
                                  + '<span>Mahasiswa memiliki <strong>' + sisaDE + ' MK bernilai D/E</strong> yang belum diprogramkan mengulang.</span>'
                                  + '</div>';
                    }
                    $('#resume_krs_alert_notes').html(notesHtml);
                }

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
                    }, {
                        name: 'IPK',
                        data: data.ipk_values
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false },
                        animations: { enabled: true, easing: 'easeinout', speed: 800 }
                    },
                    colors: ['#f39c12', '#2980b9'], // Orange for IPS, Blue for IPK
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    grid: { borderColor: '#f1f1f1' },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%',
                            dataLabels: { position: 'top' }
                        }
                    },
                    legend: { show: true, position: 'bottom' },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        offsetY: -20,
                        style: { fontSize: '10px', colors: ['#333'] }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        labels: { style: { fontSize: '9px' } }
                    },
                    yaxis: { max: 4, tickAmount: 4 },
                    title: {
                        text: 'Riwayat IPK & IPS',
                        align: 'center',
                        style: { fontSize: '12px', fontWeight: 'bold', color: '#2c3e50' }
                    }
                };

                $('#ips_chart').empty();
                var chart = new ApexCharts(document.querySelector("#ips_chart"), options);
                chart.render();

                // Handle Perwalian Button
                currentStudentData = data;
                $('#btn_saran_perwalian').prop('disabled', false);

                // Fetch Entrepreneurship Mindset and Language
                $('#skill_container').html('<div style="text-align: center; padding: 20px;"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><span style="color: #666; font-size: 12px;">Memuat nilai...</span></div>');
                
                $.ajax({
                    url: "<?= site_url('Lecturer_dashboard_perwalian/ajax_get_entrepreneurship_mindset') ?>",
                    type: "POST",
                    data: {
                        nipd: nipd,
                        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: "JSON",
                    success: function(skillRes) {
                        if (skillRes.status === 'success') {
                            let skillData = skillRes.data;
                            if(skillData) {
                                // Generate Badges Mindset
                                let badgesHtml = '';
                                if (skillData.grades.length > 0) {
                                    skillData.grades.forEach(function(g) {
                                        let badgeColor = '#858796'; // gray default
                                        if(g.nilai_huruf) {
                                            if(g.nilai_indeks >= 3.5) badgeColor = '#1cc88a'; // green
                                            else if(g.nilai_indeks >= 2.5) badgeColor = '#f6c23e'; // yellow
                                            else badgeColor = '#e74a3b'; // red
                                        }
                                        let mkLabel = g.kode_e || 'E?';
                                        let badgeTitle = g.nm_mk;
                                        badgesHtml += `
                                        <div style="display: inline-flex; flex-direction: column; align-items: center; margin-right: 15px; margin-bottom: 15px; width: 110px;">
                                            <div style="background-color: ${badgeColor}; color: #fff; padding: 1rem; border-radius: 0.75rem; display: inline-flex; flex-direction: column; align-items: center; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: default;" title="${badgeTitle}">
                                                <span style="font-size: 1.2rem; font-weight: 800; opacity: 0.9; letter-spacing: 2px;">${mkLabel}</span>
                                                <span style="font-size: 2.5rem; font-weight: 900; line-height: 1.2;">${g.nilai_huruf || '-'}</span>
                                                <span style="font-size: 1rem; opacity: 0.85; margin-top: 4px;">${g.nilai_huruf ? parseFloat(g.nilai_indeks).toFixed(2) : 'Blm Ada'}</span>
                                            </div>
                                            <span style="font-size: 1.1rem; font-weight: 600; text-align: center; color: #555; margin-top: 5px; line-height: 1.2;">${badgeTitle}</span>
                                        </div>`;
                                    });
                                } else {
                                    badgesHtml = '<p style="color: #858796; font-size: 1.1rem; font-style: italic; text-align: center; margin: 1rem 0;">Belum ada data nilai mata kuliah entrepreneurship.</p>';
                                }

                                // Format Language Values
                                let langHtml = '';
                                if (skillData.lang_grades && skillData.lang_grades.length > 0) {
                                    skillData.lang_grades.forEach(function(g) {
                                        let badgeColor = '#858796'; // gray default
                                        if(g.nilai_huruf) {
                                            if(g.nilai_indeks >= 3.5) badgeColor = '#1cc88a'; // green
                                            else if(g.nilai_indeks >= 2.5) badgeColor = '#f6c23e'; // yellow
                                            else badgeColor = '#e74a3b'; // red
                                        }
                                        let mkLabel = g.kode_lang;
                                        let badgeTitle = g.nm_mk;
                                        langHtml += `
                                        <div style="display: inline-flex; flex-direction: column; align-items: center; margin-right: 15px; margin-bottom: 15px; width: 110px;">
                                            <div style="background-color: ${badgeColor}; color: #fff; padding: 1rem; border-radius: 0.75rem; display: inline-flex; flex-direction: column; align-items: center; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: default;" title="${badgeTitle}">
                                                <span style="font-size: 1.2rem; font-weight: 800; opacity: 0.9; letter-spacing: 2px;">${mkLabel}</span>
                                                <span style="font-size: 2.5rem; font-weight: 900; line-height: 1.2;">${g.nilai_huruf || '-'}</span>
                                                <span style="font-size: 1rem; opacity: 0.85; margin-top: 4px;">${g.nilai_huruf ? parseFloat(g.nilai_indeks).toFixed(2) : 'Blm Ada'}</span>
                                            </div>
                                            <span style="font-size: 1.1rem; font-weight: 600; text-align: center; color: #555; margin-top: 5px; line-height: 1.2;">${badgeTitle}</span>
                                        </div>`;
                                    });
                                } else {
                                    langHtml = '<p style="color: #858796; font-size: 1.1rem; font-style: italic; text-align: center; margin: 1rem 0;">Belum ada data nilai mata kuliah bahasa.</p>';
                                }

                                let finalHtml = `
                                    <div class="row" style="margin-bottom: 20px;">
                                        <div class="col-md-12">
                                            <h6 style="font-weight: 700; color: #4e73df; margin-bottom: 15px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                                                <i class="fa fa-lightbulb-o" style="margin-right: 5px;"></i> Mindset Entrepreneurship
                                            </h6>
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                ${badgesHtml}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h6 style="font-weight: 700; color: #1cc88a; margin-bottom: 15px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                                                <i class="fa fa-language" style="margin-right: 5px;"></i> Kemampuan Bahasa
                                            </h6>
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                ${langHtml}
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#skill_container').html(finalHtml);
                            }
                        } else {
                            $('#skill_container').html('<div style="text-align: center; color: red;">Gagal memuat data skill</div>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Skill Ajax Error:', jqXHR.responseText);
                        $('#skill_container').html('<div style="text-align: center; color: red;">Gagal memuat data skill. Kode: ' + jqXHR.status + '</div>');
                    }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function show_modal_makul_prodi(id_sms) {
        $('#modal_makul_prodi').modal('show');
        load_makul_prodi(id_sms || '');
    }

    function load_makul_prodi(id_sms) {
        $('#body_makul_prodi').html('<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x" style="color: #10b981;"></i><br><span style="margin-top: 8px; display: block; color: #64748b; font-weight: 600;">Memuat daftar kelas kuliah prodi...</span></div>');
        var url = "<?php echo site_url('Lecturer_persetujuan_krs/show_makul_prodi') ?>" + (id_sms ? "/" + id_sms : "");
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#body_makul_prodi').html(data.html);
                }
            },
            error: function() {
                $('#body_makul_prodi').html('<div class="alert alert-danger" style="margin: 20px;"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data kelas kuliah prodi.</div>');
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

    function show_modal_belum_krs() {
        $('#tbody_belum_krs').html('<tr><td colspan="6" class="text-center" style="padding: 25px;"><i class="fa fa-spinner fa-spin" style="font-size: 22px; color: #e7505a;"></i><br><span style="margin-top: 6px; display: block; color: #666;">Memuat data mahasiswa belum KRS...</span></td></tr>');
        $('#modal_belum_krs').modal('show');
        
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_list_belum_krs') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#tbody_belum_krs').html(data.html);
                    $('#summary_belum_krs').text(data.total);
                }
            },
            error: function() {
                $('#tbody_belum_krs').html('<tr><td colspan="6" class="text-center text-danger" style="padding: 20px;"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data. Silakan coba lagi.</td></tr>');
            }
        });
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
        if (!currentStudentData) {
            if (typeof swal !== 'undefined') {
                swal({
                    title: "Mohon Tunggu",
                    text: "Data mahasiswa sedang dimuat dari server...",
                    type: "info",
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert("Data mahasiswa sedang dimuat dari server, mohon tunggu sebentar...");
            }
            return;
        }
        
        var ipk = currentStudentData.last_ipk;
        var failed = (currentStudentData.unresolved_de_count !== undefined) ? currentStudentData.unresolved_de_count : currentStudentData.failed_count;
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
                advice_text = "Peluang meraih prestasi akademik terbaik masih terbuka lebar. Maksimalkan performa di semester ini dengan target IPS 3.50.";
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

        // Double MK Warning
        if (currentStudentData.krs_review && currentStudentData.krs_review.duplicate_count > 0) {
            var dupNames = currentStudentData.krs_review.duplicate_mks.map(function(d) { return d.nm_mk + " (" + d.kode_mk + " di kelas " + d.classes.join(' & ') + ")"; }).join(", ");
            text += "\n\n[PERHATIAN KHUSUS - DOUBLE MK]:\nTerdeteksi pengambilan mata kuliah ganda dengan kode MK sama: " + dupNames + ". Mohon batalkan/hapus salah satu kelas agar tidak terjadi bentrok dan duplikasi SKS.";
        }

        var $input = $('#catatan_perwalian_input');
        $input.val(text).focus();
        validateCatatanPerwalian();
        $input.css({ 'border-color': '#4F46E5', 'box-shadow': '0 0 8px rgba(79, 70, 229, 0.4)' });
        setTimeout(function() {
            $input.css({ 'border-color': '#10b981', 'box-shadow': 'none' });
        }, 1200);
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
                <div style="display: flex; flex-wrap: wrap; min-height: 550px;">
                    
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
                            <div style="margin-top: 14px;">
                                <a href="javascript:void(0)" id="btn_modal_academic_record" target="_blank" class="btn btn-default btn-xs btn-block" style="border-radius: 4px; font-weight: 600; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 6px 8px; font-size: 11px;">
                                    <i class="fa fa-graduation-cap"></i> Buka Academic Record
                                </a>
                            </div>
                        </div>
                    </div>

                        
                        <div class="col-md-9 col-sm-8" id="right_column_scroll" style="padding: 20px;">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabbable-line">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab_krs" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-list-alt"></i> KRS </a>
                                            </li>
                                            <li>
                                                <a href="#tab_attention" data-toggle="tab" style="font-weight: 600;"> 
                                                    <i class="fa fa-exclamation-triangle"></i> Nilai D/E 
                                                    <span id="attention_badge" class="badge badge-danger" style="margin-left: 5px; font-weight: 700;">0</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_chart" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-line-chart"></i> IP/IPK </a>
                                            </li>
                                            <li>
                                                <a href="#tab_narasi" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-file-text-o"></i> Narasi Akademik </a>
                                            </li>
                                            <li id="li_tab_perwalian">
                                                <a href="#tab_perwalian" data-toggle="tab" style="font-weight: 600;"> <i class="fa fa-comments"></i> Perwalian </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" style="padding: 15px 0; min-height: 500px;">
                                            
                                            <div class="tab-pane active" id="tab_krs">
                                                <div class="compact-table" id="tabel_krs" style="margin-bottom: 10px;"></div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_attention">
                                                <div id="attention" class="compact-table" style="min-height: 270px; max-height: 450px; overflow-y: auto; border: 1px solid #f2dede; border-radius: 4px; background: #fff5f5; padding: 15px;"></div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_chart">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="ips_chart" style="min-height: 250px; border: 1px solid #f0f0f0; border-radius: 4px; padding: 10px; background: #fff;"></div>
                                                        <hr style="margin: 15px 0;">
                                                        <div id="skill_container">
                                                            <div style="text-align: center; padding: 20px;">
                                                                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br>
                                                                <span style="color: #666; font-size: 12px;">Memuat nilai...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="tab_narasi">
                                                <div id="academic_summary_content" style="min-height: 270px; padding: 20px; line-height: 1.8; font-size: 14px; color: #34495e; background: #fff; border: 1px solid #eee; border-radius: 4px; text-align: justify;">
                                                    Memuat narasi akademik...
                                                </div>
                                            </div>
                                                                             <div class="tab-pane" id="tab_perwalian">
                                                <div class="row" style="margin-top: 5px; display: flex; flex-wrap: wrap; align-items: stretch;">
                                                    <div class="col-md-7 col-sm-12" id="chat_column" style="display: flex; flex-direction: column;">
                                                        <!-- Card Resume KRS Mahasiswa -->
                                                        <div id="card_resume_krs_perwalian" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); margin-bottom: 12px; flex-shrink: 0;">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 6px;">
                                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                                    <span style="background: #eff6ff; color: #2563eb; width: 22px; height: 22px; border-radius: 5px; display: inline-flex; align-items: center; justify-content: center; font-size: 10.5px; border: 1px solid #dbeafe;">
                                                                        <i class="fa fa-list-alt"></i>
                                                                    </span>
                                                                    <span style="font-weight: 700; color: #0f172a; font-size: 12px;">Resume KRS Mahasiswa:</span>
                                                                </div>
                                                                <div id="resume_krs_status_badge"></div>
                                                            </div>
                                                            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 5px;">
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Total MK</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #0f172a; margin-top: 1px;" id="resume_total_mk">0 MK</div>
                                                                </div>
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Total SKS</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #2563eb; margin-top: 1px;" id="resume_total_sks">0 SKS</div>
                                                                </div>
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Paket</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #16a34a; margin-top: 1px;" id="resume_mk_paket">0 MK</div>
                                                                </div>
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Mengulang</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #ea580c; margin-top: 1px;" id="resume_mk_repeat">0 MK</div>
                                                                </div>
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Sisa D/E</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #dc2626; margin-top: 1px;" id="resume_mk_failed">0 MK</div>
                                                                </div>
                                                                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 6px 3px; text-align: center;" id="box_resume_double_mk">
                                                                    <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase;">Double MK</div>
                                                                    <div style="font-size: 12.5px; font-weight: 800; color: #64748b; margin-top: 1px;" id="resume_mk_double">0 MK</div>
                                                                </div>
                                                            </div>
                                                            <div id="resume_krs_alert_notes" style="font-size: 10.5px; line-height: 1.35;"></div>
                                                        </div>

                                                        <h5 style="font-weight: 700; color: #2c3e50; margin-top: 0; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 6px; flex-shrink: 0;">
                                                            <i class="fa fa-comments"></i> Riwayat Perwalian:
                                                        </h5>
                                                        <div id="log_message" style="flex: 1; min-height: 300px; max-height: 400px; overflow-y: auto; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-5 col-sm-12" id="approval_action_container" style="margin-top: 0; display: flex; flex-direction: column; justify-content: space-between;">
                                                        <h5 style="font-weight: 700; color: #2c3e50; margin-top: 0; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 8px; flex-shrink: 0;">
                                                            <i class="fa fa-pencil-square-o"></i> Form Persetujuan:
                                                        </h5>
                                                        <?php
                                                         $attributes = ['class' => 'form', 'id' => 'form_persetujuan', 'style' => 'flex: 1; display: flex; flex-direction: column; justify-content: space-between;'];
                                                        echo form_open('#', $attributes);
                                                        ?>
                                                        <input type="hidden" value="" name="id_rencana_studi" />
                                                         <div class="form-group" style="flex: 1; display: flex; flex-direction: column; margin-bottom: 15px; min-height: 0;">
                                                              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-shrink: 0;">
                                                                  <label style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 0;">Catatan / Pesan Dosen Wali:</label>
                                                                  <button type="button" id="btn_saran_perwalian" class="btn btn-xs" onclick="generate_perwalian_insight()" style="border-radius: 14px !important; padding: 3px 12px !important; font-weight: 700 !important; font-size: 11px !important; background: #4F46E5 !important; color: #ffffff !important; border: 1px solid #3730A3 !important; box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3) !important;">
                                                                      <i class="fa fa-magic" style="color: #ffffff !important; margin-right: 3px;"></i> Saran Perwalian
                                                                  </button>
                                                              </div>
                                                              <textarea class="form-control" name="catatan_dosen_wali" id="catatan_perwalian_input" style="flex: 1; min-height: 230px; resize: vertical; font-size: 13px; border-radius: 8px; border: 1px solid #ccc; padding: 12px; transition: border-color 0.2s ease, box-shadow 0.2s ease;" placeholder="Ketik pesan perwalian di sini (minimal 5 kata)..."></textarea>
                                                              
                                                              <div id="catatan_warning_box" style="display: none; margin-top: 8px; padding: 8px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; font-size: 11.5px; color: #92400e; line-height: 1.4; flex-shrink: 0;">
                                                                  <div style="display: flex; align-items: flex-start; gap: 8px;">
                                                                      <i class="fa fa-exclamation-triangle" style="color: #d97706; font-size: 13px; margin-top: 2px;"></i>
                                                                      <span id="catatan_warning_text">Catatan perwalian minimal harus terdiri dari <b>5 kata</b> (saat ini: <b id="current_word_count">0</b> kata). Mohon berikan arahan atau bimbingan yang lebih lengkap, atau klik tombol <b><i class="fa fa-magic"></i> Saran Perwalian</b> di atas.</span>
                                                                  </div>
                                                              </div>
                                                          </div>          
                                                        
                                                        <div style="margin-top: auto; flex-shrink: 0;">
                                                            <div class="form-group" style="margin-bottom: 14px;">
                                                                <label style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 6px; display: block;">Status Persetujuan:</label>
                                                                <?php
                                                                echo form_dropdown('id_status_krs', $dd_status_krs, '3', 'class="form-control" style="border-radius: 6px; height: 38px; border: 1px solid #ccc;" required');
                                                                ?>
                                                                <span style="font-size: 11px; color: #7f8c8d; margin-top: 5px; display: block; line-height: 1.4;">
                                                                    <i class="fa fa-info-circle" style="color: #3498db;"></i> Jika ingin memilih status lain, silahkan klik dropdown list di atas
                                                                </span>
                                                            </div>

                                                            <button type="submit" id="btnSavePersetujuan" class="btn btn-primary btn-block" style="border-radius: 6px; height: 44px; font-weight: 700; background-color: #2980b9; border: none; box-shadow: 0 4px 6px rgba(41, 128, 185, 0.2);">
                                                                <i class="fa fa-check-circle"></i> Simpan Persetujuan
                                                            </button>
                                                        </div>
                                                        <?php echo form_close(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                    
                    <div class="col-md-4 col-sm-12 profile-column" style="border-right: 1px solid #e2e8f0; margin-bottom: 20px; padding-right: 15px;">
                        <div style="text-align: center; margin-bottom: 15px;">
                            <div id="msg_foto" style="display: inline-block; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden;"></div>
                        </div>

                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <table style="width: 100%; font-size: 12px; border-collapse: separate; border-spacing: 0 7px;">
                                <tr>
                                    <td style="color: #64748b; width: 75px; vertical-align: middle; white-space: nowrap;">Nama</td>
                                    <td style="color: #64748b; width: 8px; vertical-align: middle;">:</td>
                                    <td style="font-weight: 700; color: #0f172a; vertical-align: middle;"><span id="msg_nm_pd_text"></span></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; vertical-align: middle; white-space: nowrap;">NIM</td>
                                    <td style="color: #64748b; vertical-align: middle;">:</td>
                                    <td style="color: #334155; font-family: monospace; font-weight: 600; vertical-align: middle;"><span id="msg_nipd_text"></span></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; vertical-align: middle; white-space: nowrap;">IPK Akhir</td>
                                    <td style="color: #64748b; vertical-align: middle;">:</td>
                                    <td style="font-weight: 700; color: #0f172a; vertical-align: middle;"><span id="msg_last_ipk_text"></span></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; vertical-align: middle; white-space: nowrap;">Smt Tempuh</td>
                                    <td style="color: #64748b; vertical-align: middle;">:</td>
                                    <td style="color: #334155; vertical-align: middle;"><span id="msg_semester_tempuh_text"></span></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; vertical-align: top; padding-top: 3px; white-space: nowrap;">Status KIP</td>
                                    <td style="color: #64748b; vertical-align: top; padding-top: 3px;">:</td>
                                    <td style="vertical-align: top;"><div id="msg_kip_info"></div></td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" id="msg_nm_pd" />
                        <input type="hidden" id="msg_nipd" />
                        <input type="hidden" id="msg_last_ipk" />
                        <input type="hidden" id="msg_semester_tempuh" />
                    </div>

                    
                    <div class="col-md-8 col-sm-12">
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

<!-- Modal Daftar Kelas Kuliah Ditawarkan Prodi -->
<div class="modal fade in" id="modal_makul_prodi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 92%; max-width: 1100px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: none;">
            <div class="modal-header" style="background: #0f172a; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center;">
                <h4 class="modal-title" style="font-weight: 700; margin: 0; color: #fff; font-size: 15px;">
                    <i class="fa fa-book" style="color: #10b981; margin-right: 6px;"></i> Daftar Mata Kuliah & Kelas yang Ditawarkan Prodi
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9; font-size: 22px; margin: 0;">&times;</button>
            </div>
            <div class="modal-body" id="body_makul_prodi" style="padding: 20px; max-height: calc(85vh - 110px); overflow-y: auto; background: #f8fafc;">
            </div>
            <div class="modal-footer" style="padding: 10px 20px; background: #fff; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Tutup</button>
            </div>
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