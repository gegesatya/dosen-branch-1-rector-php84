<style>
    /* ===== MODAL SLATE STYLE (Mirror Sempro) ===== */
    .modal-slate .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        background-color: #f8fafc;
        overflow: hidden;
    }

    .modal-slate .modal-header {
        background-color: #ffffff;
        color: #1e293b;
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-slate .modal-header .close {
        color: #64748b;
        opacity: 0.8;
        text-shadow: none;
        font-size: 1.5rem;
        margin-top: -8px;
    }

    .modal-slate .modal-header .close:hover {
        opacity: 1;
        color: #0f172a;
    }

    .modal-slate .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .modal-slate .modal-body {
        padding: 16px 20px;
    }

    /* Student card inside modal */
    .modal-student-header {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 16px;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .modal-student-header .modal-student-photo {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #cbd5e1;
        flex-shrink: 0;
    }

    .modal-student-header h4 {
        margin: 0 0 4px 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .modal-student-header p.title {
        margin: 0;
        font-size: 0.85rem;
        color: #334155;
        line-height: 1.4;
        font-style: italic;
        background: #f1f5f9;
        padding: 5px 8px;
        border-radius: 0 6px 6px 0;
        border-left: 3px solid #0D9488;
        white-space: normal;
        word-wrap: break-word;
        word-break: break-word;
    }

    .modal-student-header div:last-child {
        flex: 1;
        min-width: 0;
    }

    /* Grading scale box */
    .modal-slate .grading-scale {
        background: #F0FDFA;
        border: 1px solid #99F6E4;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
        color: #0F766E;
        font-size: 0.9rem;
    }

    .modal-slate .grading-scale-title {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .scale-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .scale-badge {
        background: #CCFBF1;
        color: #0F766E;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #99F6E4;
    }

    /* Score input row */
    .score-row {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #E5E7EB;
        padding: 12px 16px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .score-row-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        flex: 1;
        line-height: 1.4;
    }

    .score-row-input {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .score-row-input input {
        width: 70px;
        text-align: center;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #D1D5DB;
        font-weight: 700;
        font-size: 15px;
    }

    .score-display-badge {
        background: #CCFBF1;
        color: #0F766E;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 13px;
        font-weight: 800;
        min-width: 40px;
        text-align: center;
    }

    .total-row {
        background: #0D9488;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
    }

    .total-row-label {
        font-size: 14px;
        font-weight: 800;
        color: #CCFBF1;
    }

    .total-row-value {
        font-size: 22px;
        font-weight: 900;
        color: #FFFFFF;
    }

    /* Usulan Revisi tab system */
    .modal-slate .nav-pills>li>a {
        color: #374151;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
    }

    .modal-slate .nav-pills>li.active>a,
    .modal-slate .nav-pills>li.active>a:focus,
    .modal-slate .nav-pills>li.active>a:hover {
        background-color: #0D9488;
        color: #fff;
    }

    .modal-slate .tab-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        min-height: 300px;
    }

    .modal-slate .tab-content h4 {
        margin-top: 0;
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 16px;
    }

    /* Modern Tab Menu in Modal */
    .nav-tabs-modern {
        border-bottom: 1px solid #CBD5E1;
        margin-bottom: 16px;
    }
    .nav-tabs-modern > li > a {
        border: 1px solid transparent;
        border-bottom: none;
        border-radius: 4px 4px 0 0 !important;
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        padding: 8px 16px;
        background: #F1F5F9;
        transition: all 0.15s;
    }
    .nav-tabs-modern > li > a:hover {
        background: #E2E8F0;
        color: #1E293B;
    }
    .nav-tabs-modern > li.active > a,
    .nav-tabs-modern > li.active > a:focus,
    .nav-tabs-modern > li.active > a:hover {
        background: #FFFFFF !important;
        color: #0D9488 !important;
        border: 1px solid #CBD5E1 !important;
        border-bottom-color: #FFFFFF !important;
        font-weight: 700;
    }

    /* Clean Examiner Notes Card */
    .sempro-note-card {
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        margin-bottom: 10px;
        background: #FFFFFF;
    }
    .sempro-note-header {
        background: #F8FAFC;
        padding: 8px 12px;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }
    .sempro-note-body {
        padding: 10px 12px;
        font-size: 12px;
        color: #334155;
    }
    .sempro-note-item {
        margin-bottom: 6px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #F1F5F9;
    }
    .sempro-note-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .sempro-note-label {
        font-weight: 700;
        color: #0D9488;
        font-size: 11px;
        margin-bottom: 2px;
    }
</style>

<script type="text/javascript">
    var save_method;
    var current_rerata = 0;

    function check_kelulusan_semhas(obj) {
        if ($(obj).prop('checked') && current_rerata > 0 && current_rerata < 3) {
            Swal({
                title: 'Nilai Rata-rata Rendah',
                text: 'Nilai rata-rata dari ketiga penguji di bawah 3 (' + current_rerata + '). Apakah Anda ingin menyatakan layak untuk dilanjutkan?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0D9488',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.value) {
                    $(obj).prop('checked', false);
                }
            });
        }
    }

    // ===================== DATEPICKER =====================
    $(document).ready(function () {
        $('.datepicker').datepicker({ autoclose: true, format: "yyyy-mm-dd", todayHighlight: true, orientation: "top auto" });
        $("#revisi_judul").hide();

        // NIM lookup for Berita Acara
        function nim_lookup(nimField, namaField) {
            $(nimField).on('input', function () {
                var nim = $(this).val();
                $.ajax({
                    url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                    data: { nim: nim, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
                    type: 'post', dataType: "JSON"
                }).done(function (r) {
                    $(namaField).val(r ? r.nm_pd : '');
                }).fail(function () { $(namaField).val(''); });
            });
        }
        nim_lookup('[name="semhas_nim_moderator"]', '[name="semhas_moderator"]');
        nim_lookup('[name="semhas_nim_pembahas_1"]', '[name="semhas_pembahas_1"]');
        nim_lookup('[name="semhas_nim_pembahas_2"]', '[name="semhas_pembahas_2"]');

        // Score live calculation
        function calcScore(fieldName, multiplier, displayId) {
            $('[name="' + fieldName + '"]').on('input', function () {
                var v = Math.round(Number($(this).val()) * multiplier * 100) / 100;
                $(displayId).text(v || 0);
                updateTotal();
            });
        }
        calcScore('semhas_nilai_a', 0.20, '#n1');
        calcScore('semhas_nilai_b', 0.10, '#n2');
        calcScore('semhas_nilai_c', 0.15, '#n3');
        calcScore('semhas_nilai_d', 0.15, '#n4');
        calcScore('semhas_nilai_e', 0.30, '#n5');
        calcScore('semhas_nilai_f', 0.10, '#n6');

        function updateTotal() {
            var total = ['#n1', '#n2', '#n3', '#n4', '#n5', '#n6'].reduce(function (s, id) { return s + Number($(id).text()); }, 0);
            $('#total').val(Math.round(total * 100) / 100);
        }

        // Summernote init for catatan
        if ($.fn.summernote) {
            $('.summernote-catatan').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'strikethrough']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        }
    });

    function toggle(id, obj) {
        var $input = $(obj);
        if ($input.prop('checked')) {
            $(id).show();
            if (id === '#revisi_judul') {
                var old_title = $('[name="judul_ditetapkan_1"]').val();
                $('[name="judul_ditetapkan_2"]').val(old_title);
            }
        } else {
            $(id).hide();
            if (id === '#revisi_judul') {
                $('[name="judul_ditetapkan_2"]').val('');
            }
        }
    }

    function reload_cards() { window.location.reload(); }
    function view_rubrik() { $('#modal_form_rubrik').modal('show'); }

    // ===================== EDIT NILAI & RECORD SEMPRO =====================
    function edit_nilai_sempro(id, target_tab, only_record) {
        if (target_tab === undefined) target_tab = '#tab_penilaian_semhas';
        if (only_record === undefined) only_record = false;
        save_method = 'update';
        $('#form')[0].reset();
        $('#n1,#n2,#n3,#n4,#n5,#n6').text('0');
        $('#total').val('0');

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_hasil/ajax_edit/') ?>/" + id,
            type: "GET", dataType: "JSON",
            success: function (data) {
                var fallback = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=0D9488&color=fff";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback + "';}; this.src='" + original_photo + "';";
                $('#nilai_student_photo').attr('src', photo).attr('onerror', onerror_script);
                $('#nilai_student_name').text(data.nm_pd);
                $('#nilai_student_nim').text(data.nipd);
                $('#nilai_student_title').text(data.judul_ditetapkan || '');

                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="semhas_nilai_a"]').val(data.semhas_nilai_a);
                $('[name="semhas_nilai_b"]').val(data.semhas_nilai_b);
                $('[name="semhas_nilai_c"]').val(data.semhas_nilai_c);
                $('[name="semhas_nilai_d"]').val(data.semhas_nilai_d);
                $('[name="semhas_nilai_e"]').val(data.semhas_nilai_e);
                $('[name="semhas_nilai_f"]').val(data.semhas_nilai_f);

                var a = Math.round(data.semhas_nilai_a * 0.20 * 100) / 100;
                var b = Math.round(data.semhas_nilai_b * 0.10 * 100) / 100;
                var c = Math.round(data.semhas_nilai_c * 0.15 * 100) / 100;
                var d = Math.round(data.semhas_nilai_d * 0.15 * 100) / 100;
                var e = Math.round(data.semhas_nilai_e * 0.30 * 100) / 100;
                var f = Math.round(data.semhas_nilai_f * 0.10 * 100) / 100;
                $('#n1').text(a); $('#n2').text(b); $('#n3').text(c);
                $('#n4').text(d); $('#n5').text(e); $('#n6').text(f);
                $('#total').val(Math.round((a + b + c + d + e + f) * 100) / 100);

                // --- POPULATE RECORD SEMPRO TAB ---
                $('#sempro_tab_badge').text(data.sempro_rerata ? (data.sempro_rerata * 1) : '-');
                $('#sempro_stat_rerata').text(data.sempro_rerata ? (data.sempro_rerata * 1) : '-');

                var hasil_text = data.sempro_hasil_akhir ? data.sempro_hasil_akhir : '-';
                var hasil_class = 'label label-default';
                if (hasil_text.toLowerCase() === 'layak') hasil_class = 'label label-success';
                else if (hasil_text.toLowerCase() === 'tidak layak') hasil_class = 'label label-danger';
                $('#sempro_stat_hasil').html('<span class="' + hasil_class + '" style="font-size:11px; padding:3px 8px;">' + hasil_text + '</span>');

                var jadwal_str = data.sempro_tanggal ? (data.sempro_tanggal + (data.sempro_jam ? ' (' + data.sempro_jam + ' WIB)' : '')) : '-';
                if (data.sempro_tempat && data.sempro_tempat !== '-') jadwal_str += ' &bull; ' + data.sempro_tempat;
                $('#sempro_stat_jadwal').html(jadwal_str);

                if (data.sempro_link_dokumen) {
                    $('#sempro_stat_dokumen').html('<a href="' + data.sempro_link_dokumen + '" target="_blank" class="btn btn-default btn-xs" style="font-size:11px;"><i class="fa fa-file-text-o"></i> Buka Berkas Proposal</a>');
                } else {
                    $('#sempro_stat_dokumen').html('<span class="text-muted" style="font-size:11px; font-style:italic;">Berkas belum diunggah</span>');
                }

                // Render Tabel Nilai Dosen Penguji Sempro
                var tbody_html = '';
                var list_penguji = data.sempro_penguji_data || [];
                if (list_penguji.length > 0) {
                    var no = 1;
                    list_penguji.forEach(function(item) {
                        tbody_html += '<tr>' +
                            '<td class="text-center" style="width:35px;">' + (no++) + '</td>' +
                            '<td>' +
                                '<strong>' + (item.nama_lengkap || '-') + '</strong>' +
                                '<div style="font-size:11px; color:#64748B;">' + item.role_dosen + '</div>' +
                            '</td>' +
                            '<td class="text-center">' + (item.nilai_a || 0) + '</td>' +
                            '<td class="text-center">' + (item.nilai_b || 0) + '</td>' +
                            '<td class="text-center">' + (item.nilai_c || 0) + '</td>' +
                            '<td class="text-center">' + (item.nilai_d || 0) + '</td>' +
                            '<td class="text-center">' + (item.nilai_e || 0) + '</td>' +
                            '<td class="text-center" style="font-weight:bold; font-size:13px; color:#0F766E;">' + (item.nilai_akhir || 0) + '</td>' +
                        '</tr>';
                    });
                } else {
                    tbody_html = '<tr><td colspan="8" class="text-center text-muted" style="padding:15px;">Belum ada data nilai penguji Sempro.</td></tr>';
                }
                $('#sempro_table_body').html(tbody_html);
                $('#sempro_table_rerata_val').text(data.sempro_rerata ? (data.sempro_rerata * 1) : '0');

                // Render Catatan Usulan Revisi Sempro Per Penguji
                var catatan_html = '';
                var total_catatan_ada = 0;
                if (list_penguji.length > 0) {
                    list_penguji.forEach(function(item) {
                        var kriteria_list = [
                            { key: 'catatan_judul', label: 'Judul' },
                            { key: 'catatan_latar_belakang', label: 'Latar Belakang' },
                            { key: 'catatan_rumusan_masalah', label: 'Rumusan Masalah' },
                            { key: 'catatan_tujuan_penelitian', label: 'Tujuan / Manfaat' },
                            { key: 'catatan_tinjauan_pustaka', label: 'Tinjauan Pustaka' },
                            { key: 'catatan_metode_penelitian', label: 'Metode Penelitian' },
                            { key: 'catatan_daftar_pustaka', label: 'Daftar Pustaka' },
                            { key: 'catatan_format_penulisan', label: 'Format Penulisan' }
                        ];

                        var notes_content = '';
                        var count_penguji_note = 0;

                        kriteria_list.forEach(function(k) {
                            var val = item[k.key];
                            if (val && val.trim() !== '' && val !== '<p><br></p>') {
                                count_penguji_note++;
                                total_catatan_ada++;
                                notes_content += '<div class="sempro-note-item">' +
                                    '<div class="sempro-note-label">' + k.label + ':</div>' +
                                    '<div style="font-size:12px; line-height:1.4;">' + val + '</div>' +
                                '</div>';
                            }
                        });

                        catatan_html += '<div class="sempro-note-card">' +
                            '<div class="sempro-note-header">' +
                                '<div><strong>' + (item.nama_lengkap || '-') + '</strong> <span class="text-muted">(' + item.role_dosen + ')</span></div>' +
                                '<div><span class="label label-default" style="font-size:10px;">Nilai: ' + (item.nilai_akhir || 0) + '</span></div>' +
                            '</div>' +
                            '<div class="sempro-note-body">' +
                                (count_penguji_note > 0 ? notes_content : '<span class="text-muted" style="font-style:italic; font-size:11px;">Tidak ada catatan usulan revisi dari penguji ini.</span>') +
                            '</div>' +
                        '</div>';
                    });
                } else {
                    catatan_html = '<p class="text-muted" style="font-style:italic; font-size:12px; text-align:center; padding:15px;">Belum ada data catatan Seminar Proposal.</p>';
                }
                $('#sempro_catatan_container').html(catatan_html);

                // Handling Mode Tampilan
                if (only_record) {
                    $('#modal_form_tabs li:first-child').hide();
                    $('#modal_form_tabs li:last-child').show().find('a').tab('show');
                    $('#btnSave').hide();
                    $('#modal_form .modal-title').text("Riwayat Seminar Proposal");
                } else {
                    $('#modal_form_tabs li:first-child').show();
                    $('#modal_form_tabs li:last-child').show();
                    $('#btnSave').show();
                    $('#modal_form_tabs a[href="' + target_tab + '"]').tab('show');
                    $('#modal_form .modal-title').text("Nilai Seminar Hasil");
                }

                $('#modal_form').modal('show');
            },
            error: function () { alert('Error get data from ajax'); }
        });
    }

    function view_record_sempro(id, only_record) {
        if (only_record === undefined) only_record = true;
        edit_nilai_sempro(id, '#tab_record_sempro', only_record);
    }

    // ===================== EDIT CATATAN =====================
    var catatanCache = {};

    function edit_catatan(id, show_modal) {
        if (show_modal === undefined) show_modal = true;
        save_method = 'update';

        if (catatanCache[id]) {
            if (show_modal) $('#modal_form_catatan').modal('show');
            return;
        }

        $('#form_catatan')[0].reset();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_hasil/ajax_edit/') ?>/" + id,
            type: "GET", dataType: "JSON",
            success: function (data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                var fallback = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=0D9488&color=fff";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback + "';}; this.src='" + original_photo + "';";
                $('#catatan_student_photo').attr('src', photo).attr('onerror', onerror_script);
                $('#catatan_student_nim').text(data.nipd);
                $('#catatan_student_name').text(data.nm_pd);
                $('#catatan_student_title').text(data.judul_ditetapkan || '');

                if ($.fn.summernote) {
                    $('[name="semhas_catatan_judul"]').summernote('code', data.semhas_catatan_judul || '');
                    $('[name="semhas_catatan_latar_belakang"]').summernote('code', data.semhas_catatan_latar_belakang || '');
                    $('[name="semhas_catatan_rumusan_masalah"]').summernote('code', data.semhas_catatan_rumusan_masalah || '');
                    $('[name="semhas_catatan_tujuan_penelitian"]').summernote('code', data.semhas_catatan_tujuan_penelitian || '');
                    $('[name="semhas_catatan_tinjauan_pustaka"]').summernote('code', data.semhas_catatan_tinjauan_pustaka || '');
                    $('[name="semhas_catatan_metode_penelitian"]').summernote('code', data.semhas_catatan_metode_penelitian || '');
                    $('[name="semhas_catatan_daftar_pustaka"]').summernote('code', data.semhas_catatan_daftar_pustaka || '');
                    $('[name="semhas_catatan_format_penulisan"]').summernote('code', data.semhas_catatan_format_penulisan || '');
                    $('[name="semhas_desain"]').summernote('code', data.semhas_desain || '');
                    $('[name="semhas_pengujian"]').summernote('code', data.semhas_pengujian || '');
                    $('[name="semhas_kesimpulan"]').summernote('code', data.semhas_kesimpulan || '');
                }

                $('.nav-pills a[href="#semhas_tab_judul"]').tab('show');
                catatanCache[id] = true;

                if (show_modal) {
                    $('#modal_form_catatan').modal('show');
                }
                $('#modal_form_catatan .modal-title').text("Usulan Revisi Seminar Hasil");
            },
            error: function () { alert('Error get data from ajax'); }
        });
    }

    // ===================== EDIT BERITA ACARA =====================
    function edit_berita_acara(id) {
        save_method = 'update';
        $('#form_berita_acara')[0].reset();
        $("#revisi_judul").hide();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_hasil/ajax_edit/') ?>/" + id,
            type: "GET", dataType: "JSON",
            success: function (data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="judul_ditetapkan_1"]').val(data.judul_ditetapkan);

                var fallback = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=0D9488&color=fff";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback + "';}; this.src='" + original_photo + "';";
                $('#ba_student_photo').attr('src', photo).attr('onerror', onerror_script);
                $('#ba_student_nim').text(data.nipd);
                $('#ba_student_name').text(data.nm_pd);
                $('#ba_student_title').text(data.judul_ditetapkan);

                $('[name="semhas_hari"]').val(data.semhas_hari);
                $('[name="semhas_tanggal"]').val(data.semhas_tanggal);
                $('[name="semhas_moderator"]').val(data.semhas_moderator);
                $('[name="semhas_nim_moderator"]').val(data.semhas_nim_moderator);
                $('[name="semhas_pembahas_1"]').val(data.semhas_pembahas_1);
                $('[name="semhas_nim_pembahas_1"]').val(data.semhas_nim_pembahas_1);
                $('[name="semhas_pembahas_2"]').val(data.semhas_pembahas_2);
                $('[name="semhas_nim_pembahas_2"]').val(data.semhas_nim_pembahas_2);
                $('[name="semhas_jumlah_mahasiswa"]').val(data.semhas_jumlah_mahasiswa);
                $('[name="semhas_tempat"]').val(data.semhas_tempat);
                $('[name="semhas_jam_mulai"]').val(data.semhas_jam_mulai);
                $('[name="semhas_jam_selesai"]').val(data.semhas_jam_selesai);
                if (data.semhas_hasil == 'layak') {
                    $('[name="semhas_hasil"]').prop('checked', true);
                } else {
                    $('[name="semhas_hasil"]').prop('checked', false);
                }
                current_rerata = data.rerata_nilai || 0;
                $('#tbl_nilai').html(data.tbl_nilai);

                $('#modal_form_berita_acara').modal('show');
                $('#modal_form_berita_acara .modal-title').text("Berita Acara Seminar Hasil");
            },
            error: function () { alert('Error get data from ajax'); }
        });
    }

    // ===================== SAVE FUNCTIONS =====================
    function save() {
        $('#btnSave').text('Menyimpan...').attr('disabled', true);
        var url = "<?php echo site_url('E_skripsi_seminar_hasil/ajax_update') ?>";
        $.ajax({
            url: url, type: "POST", data: $('#form').serialize(), dataType: "JSON",
            success: function (data) {
                if (data.status) {
                    $('#modal_form').modal('hide');
                    window.location.reload();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('Simpan').attr('disabled', false);
            },
            error: function () {
                alert('Error adding / update data');
                $('#btnSave').text('Simpan').attr('disabled', false);
            }
        });
    }

    function save_catatan() {
        $('#btnSaveCatatan').text('Menyimpan...').attr('disabled', true);
        var url = "<?php echo site_url('E_skripsi_seminar_hasil/ajax_update_catatan') ?>";
        $.ajax({
            url: url, type: "POST", data: $('#form_catatan').serialize(), dataType: "JSON",
            success: function (data) {
                if (data.status) {
                    $('#modal_form_catatan').modal('hide');
                    catatanCache = {};
                    window.location.reload();
                }
                $('#btnSaveCatatan').text('Simpan').attr('disabled', false);
            },
            error: function () {
                alert('Error saving data');
                $('#btnSaveCatatan').text('Simpan').attr('disabled', false);
            }
        });
    }

    function save_berita_acara() {
        $('#btnSaveBeritaAcara').text('Menyimpan...').attr('disabled', true);
        $.ajax({
            url: "<?php echo site_url('E_skripsi_seminar_hasil/ajax_update_berita_acara') ?>",
            type: "POST", data: $('#form_berita_acara').serialize(), dataType: "JSON",
            success: function (data) {
                if (data.status) {
                    $('#modal_form_berita_acara').modal('hide');
                    window.location.reload();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSaveBeritaAcara').text('Simpan').attr('disabled', false);
            },
            error: function () {
                alert('Error saving data');
                $('#btnSaveBeritaAcara').text('Simpan').attr('disabled', false);
            }
        });
    }
</script>

<!-- ===================== MODAL: NILAI SEMHAS & RECORD SEMPRO ===================== -->
<div class="modal fade modal-slate" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Penilaian & Record Seminar</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Student Card Header -->
                <div class="modal-student-header">
                    <img id="nilai_student_photo" src="" alt="Photo" class="modal-student-photo"
                        onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                    <div>
                        <h4>[<span id="nilai_student_nim"></span>] <span id="nilai_student_name"></span></h4>
                        <p class="title" id="nilai_student_title">Judul Penelitian</p>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-modern" id="modal_form_tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#tab_penilaian_semhas">Penilaian Seminar Hasil</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#tab_record_sempro">
                            Record Seminar Proposal
                            <span id="sempro_tab_badge" class="badge badge-info" style="font-size:10px; margin-left:4px;">-</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="border:none; padding:0; background:transparent; min-height:auto;">
                    <!-- ================= TAB 1: PENILAIAN SEMHAS ================= -->
                    <div id="tab_penilaian_semhas" class="tab-pane fade in active">
                        <?php echo form_open('#', ['class' => 'form-horizontal', 'id' => 'form']); ?>
                        <input type="hidden" name="id_nilai_sempro">
                        <input type="hidden" name="nipd">
                        <input type="hidden" name="nm_pd">

                        <!-- Grading Scale -->
                        <div class="grading-scale">
                            <div class="grading-scale-title"><i class="fa fa-info-circle"></i> Keterangan Skala Penilaian</div>
                            <div class="scale-badges">
                                <span class="scale-badge">1 = Buruk</span>
                                <span class="scale-badge">2 = Sangat Kurang</span>
                                <span class="scale-badge">3 = Kurang</span>
                                <span class="scale-badge">5 = Cukup</span>
                                <span class="scale-badge">6 = Baik</span>
                                <span class="scale-badge">7 = Sangat Baik</span>
                            </div>
                        </div>

                        <!-- Score Inputs -->
                        <div class="score-row">
                            <div class="score-row-label">Latar Belakang & Perumusan Masalah <small style="color:#6B7280;">(Bobot 20%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_a" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n1">0</div>
                            </div>
                        </div>
                        <div class="score-row">
                            <div class="score-row-label">Tinjauan Pustaka <small style="color:#6B7280;">(Bobot 10%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_b" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n2">0</div>
                            </div>
                        </div>
                        <div class="score-row">
                            <div class="score-row-label">Kerangka Konseptual <small style="color:#6B7280;">(Bobot 15%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_c" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n3">0</div>
                            </div>
                        </div>
                        <div class="score-row">
                            <div class="score-row-label">Metode Penelitian <small style="color:#6B7280;">(Bobot 15%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_d" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n4">0</div>
                            </div>
                        </div>
                        <div class="score-row">
                            <div class="score-row-label">Hasil & Pembahasan <small style="color:#6B7280;">(Bobot 30%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_e" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n5">0</div>
                            </div>
                        </div>
                        <div class="score-row">
                            <div class="score-row-label">Kemampuan Menjelaskan <small style="color:#6B7280;">(Bobot 10%)</small></div>
                            <div class="score-row-input">
                                <input name="semhas_nilai_f" class="form-control" type="number" min="1" max="7" step="1"
                                    placeholder="1-7" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                <div class="score-display-badge" id="n6">0</div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="total-row">
                            <span class="total-row-label"><i class="fa fa-star"></i> NILAI AKHIR SEMHAS</span>
                            <input name="total" id="total" type="text" readonly
                                style="background:transparent; border:none; font-size:22px; font-weight:900; color:#FFF; width:80px; text-align:right; outline:none;">
                        </div>
                        </form>
                    </div>

                    <!-- ================= TAB 2: RECORD SEMPRO ================= -->
                    <div id="tab_record_sempro" class="tab-pane fade">
                        <!-- Ringkasan Info Sempro -->
                        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:10px 14px; margin-bottom:14px; font-size:12px;">
                            <div class="row">
                                <div class="col-sm-3" style="border-right:1px solid #E2E8F0;">
                                    <div style="color:#64748B; font-size:11px;">Rata-rata Nilai Penguji:</div>
                                    <div style="font-size:18px; font-weight:800; color:#0F172A; margin-top:2px;">
                                        <span id="sempro_stat_rerata">-</span> <small style="font-size:11px; font-weight:normal; color:#64748B;">/ 7</small>
                                    </div>
                                </div>
                                <div class="col-sm-3" style="border-right:1px solid #E2E8F0;">
                                    <div style="color:#64748B; font-size:11px;">Hasil Sempro:</div>
                                    <div id="sempro_stat_hasil" style="margin-top:4px;">-</div>
                                </div>
                                <div class="col-sm-4" style="border-right:1px solid #E2E8F0;">
                                    <div style="color:#64748B; font-size:11px;">Jadwal & Tempat:</div>
                                    <div id="sempro_stat_jadwal" style="font-weight:600; color:#334155; margin-top:2px;">-</div>
                                </div>
                                <div class="col-sm-2 text-center" style="display:flex; align-items:center; justify-content:center;">
                                    <div id="sempro_stat_dokumen"></div>
                                </div>
                            </div>
                        </div>

                        <!-- 1. Detail Nilai Para Penguji Sempro -->
                        <div style="margin-bottom:18px;">
                            <div style="font-weight:700; font-size:13px; color:#1E293B; margin-bottom:6px;">
                                Rincian Nilai Penguji Seminar Proposal
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed" style="font-size:12px; margin-bottom:4px;">
                                    <thead>
                                        <tr style="background:#F1F5F9; color:#334155;">
                                            <th style="width:35px; text-align:center;">No</th>
                                            <th>Dosen Penguji</th>
                                            <th style="text-align:center; width:65px;" title="Latar Belakang (25%)">A (25%)</th>
                                            <th style="text-align:center; width:65px;" title="Perumusan Masalah (20%)">B (20%)</th>
                                            <th style="text-align:center; width:65px;" title="Tinjauan Pustaka (25%)">C (25%)</th>
                                            <th style="text-align:center; width:65px;" title="Kerangka Konsep (15%)">D (15%)</th>
                                            <th style="text-align:center; width:65px;" title="Metode Penelitian (15%)">E (15%)</th>
                                            <th style="text-align:center; width:85px;">Nilai Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sempro_table_body">
                                        <!-- Dynamic rows -->
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#F8FAFC; font-weight:bold;">
                                            <td colspan="7" style="text-align:right; padding-right:10px;">Rata-rata Nilai Penguji:</td>
                                            <td style="text-align:center; font-size:13px; color:#0F766E;" id="sempro_table_rerata_val">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <small class="text-muted" style="display:block; font-size:11px;">
                                * Bobot: <b>A</b> (Latar Belakang 25%), <b>B</b> (Perumusan Masalah 20%), <b>C</b> (Tinjauan Pustaka 25%), <b>D</b> (Kerangka Konsep 15%), <b>E</b> (Metode Penelitian 15%).
                            </small>
                        </div>

                        <!-- 2. Catatan Usulan Revisi Sempro -->
                        <div>
                            <div style="font-weight:700; font-size:13px; color:#1E293B; margin-bottom:8px;">
                                Catatan Usulan Revisi Sempro dari Penguji
                            </div>
                            <div id="sempro_catatan_container">
                                <!-- Dynamic Examiner Notes -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan Nilai Semhas</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: USULAN REVISI ===================== -->
<div class="modal fade modal-slate" id="modal_form_catatan" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Usulan Revisi Seminar Hasil</h3>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php echo form_open('#', ['class' => 'form-horizontal', 'id' => 'form_catatan']); ?>
                <input type="hidden" name="id_nilai_sempro">
                <input type="hidden" name="nipd">
                <input type="hidden" name="nm_pd">

                <!-- Student Card -->
                <div class="modal-student-header">
                    <img id="catatan_student_photo" src="" alt="Photo" class="modal-student-photo"
                        onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                    <div>
                        <h4>[<span id="catatan_student_nim"></span>] <span id="catatan_student_name"></span></h4>
                        <p class="title" id="catatan_student_title">Judul Penelitian</p>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="row">
                    <div class="col-md-3">
                        <ul class="nav nav-pills nav-stacked"
                            style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden; padding:4px;">
                            <li class="active"><a data-toggle="pill" href="#semhas_tab_judul"
                                    style="border-radius:6px;">A. Judul</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_latar" style="border-radius:6px;">B. Latar
                                    Belakang</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_rumusan" style="border-radius:6px;">C. Rumusan
                                    Masalah</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_tujuan" style="border-radius:6px;">D.
                                    Tujuan/Manfaat</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_tinjauan" style="border-radius:6px;">E. Tinjauan
                                    Pustaka</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_metode" style="border-radius:6px;">F. Metode
                                    Penelitian</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_daftar" style="border-radius:6px;">G. Daftar
                                    Pustaka</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_format" style="border-radius:6px;">H. Format
                                    Penulisan</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_desain" style="border-radius:6px;">I. Desain &
                                    Pembuatan</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_pengujian" style="border-radius:6px;">J.
                                    Pengujian & Analisa</a></li>
                            <li><a data-toggle="pill" href="#semhas_tab_kesimpulan" style="border-radius:6px;">K.
                                    Kesimpulan</a></li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div id="semhas_tab_judul" class="tab-pane fade in active">
                                <h4>Catatan: Judul</h4>
                                <textarea name="semhas_catatan_judul"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_latar" class="tab-pane fade">
                                <h4>Catatan: Latar Belakang</h4>
                                <textarea name="semhas_catatan_latar_belakang"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_rumusan" class="tab-pane fade">
                                <h4>Catatan: Rumusan Masalah</h4>
                                <textarea name="semhas_catatan_rumusan_masalah"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_tujuan" class="tab-pane fade">
                                <h4>Catatan: Tujuan & Manfaat</h4>
                                <textarea name="semhas_catatan_tujuan_penelitian"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_tinjauan" class="tab-pane fade">
                                <h4>Catatan: Tinjauan Pustaka</h4>
                                <textarea name="semhas_catatan_tinjauan_pustaka"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_metode" class="tab-pane fade">
                                <h4>Catatan: Metode Penelitian</h4>
                                <textarea name="semhas_catatan_metode_penelitian"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_daftar" class="tab-pane fade">
                                <h4>Catatan: Daftar Pustaka</h4>
                                <textarea name="semhas_catatan_daftar_pustaka"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_format" class="tab-pane fade">
                                <h4>Catatan: Format Penulisan</h4>
                                <textarea name="semhas_catatan_format_penulisan"
                                    class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_desain" class="tab-pane fade">
                                <h4>Catatan: Desain & Pembuatan</h4>
                                <textarea name="semhas_desain" class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_pengujian" class="tab-pane fade">
                                <h4>Catatan: Pengujian & Analisa</h4>
                                <textarea name="semhas_pengujian" class="form-control summernote-catatan"></textarea>
                            </div>
                            <div id="semhas_tab_kesimpulan" class="tab-pane fade">
                                <h4>Catatan: Kesimpulan & Rekomendasi</h4>
                                <textarea name="semhas_kesimpulan" class="form-control summernote-catatan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" id="btnSaveCatatan" onclick="save_catatan()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: BERITA ACARA ===================== -->
<div class="modal fade" id="modal_form_berita_acara" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Berita Acara Seminar Hasil</h3>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form">
                <?php echo form_open('#', ['class' => 'form-horizontal', 'id' => 'form_berita_acara']); ?>
                <input type="hidden" name="id_nilai_sempro">
                <input type="hidden" name="id_judul_skripsi">

                <div class="form-body">
                    <!-- Student Card -->
                    <div class="modal-student-header" style="margin-bottom: 20px;">
                        <img id="ba_student_photo" src="" alt="Photo" class="modal-student-photo"
                            onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                        <div>
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
                        <div class="col-md-9"><textarea name="judul_ditetapkan_1" rows="2" class="form-control"
                                readonly></textarea></div>
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
                        <div class="col-md-9"><textarea name="judul_ditetapkan_2" rows="2"
                                class="form-control"></textarea></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Hari/Tanggal</label>
                        <div class="col-md-9">
                            <input name="semhas_hari" placeholder="Hari" class="form-control" type="text"
                                style="width:45%; display:inline-block;" readonly>
                            <input name="semhas_tanggal" class="form-control" type="date"
                                style="width:45%; display:inline-block; margin-left: 2%;"
                                onchange="const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; document.getElementsByName('semhas_hari')[0].value = this.value ? days[new Date(this.value).getDay()] : '';">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Jam</label>
                        <div class="col-md-9">
                            <input name="semhas_jam_mulai" placeholder="hh:mm" class="form-control text-center"
                                type="text" maxlength="5" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                oninput="this.value = this.value.replace(/[^0-9:]/g, '');"
                                style="width:40%; display:inline-block;">
                            <span
                                style="display:inline-block; width:10%; text-align:center; font-weight:bold;">s/d</span>
                            <input name="semhas_jam_selesai" placeholder="hh:mm" class="form-control text-center"
                                type="text" maxlength="5" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                oninput="this.value = this.value.replace(/[^0-9:]/g, '');"
                                style="width:40%; display:inline-block;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Tempat</label>
                        <div class="col-md-9"><input name="semhas_tempat" class="form-control" type="text"><span
                                class="help-block"></span></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Nilai Penguji</label>
                        <div class="col-md-9" id="tbl_nilai"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Moderator / NIM</label>
                        <div class="col-md-4">
                            <input name="semhas_nim_moderator" placeholder="NIM Moderator" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-5">
                            <input name="semhas_moderator" placeholder="Nama Moderator (auto)" class="form-control"
                                type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas 1 / NIM</label>
                        <div class="col-md-4">
                            <input name="semhas_nim_pembahas_1" placeholder="NIM Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-5">
                            <input name="semhas_pembahas_1" placeholder="Nama Pembahas (auto)" class="form-control"
                                type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas 2 / NIM</label>
                        <div class="col-md-4">
                            <input name="semhas_nim_pembahas_2" placeholder="NIM Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-5">
                            <input name="semhas_pembahas_2" placeholder="Nama Pembahas (auto)" class="form-control"
                                type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Jumlah Peserta</label>
                        <div class="col-md-3"><input name="semhas_jumlah_mahasiswa" class="form-control"
                                type="text"><span class="help-block"></span></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Status Kelulusan</label>
                        <div class="col-md-9">
                            <div style="padding-top: 7px;">
                                <label
                                    style="font-weight: 600; color: #10B981; margin-bottom: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="semhas_hasil" value="layak"
                                        style="margin: 0; cursor: pointer;" onclick="check_kelulusan_semhas(this)">
                                    Penelitian Layak untuk dilanjutkan ke Ujian Tugas Akhir
                                </label>
                            </div>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" id="btnSaveBeritaAcara" onclick="save_berita_acara()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL: RUBRIK PENILAIAN ===================== -->
<div class="modal fade modal-slate" id="modal_form_rubrik" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Rubrik Penilaian Seminar Hasil</h3>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-condensed" style="font-size:12px;">
                        <thead style="background:#F0FDFA; color:#0F766E;">
                            <tr>
                                <th>Skor</th>
                                <th>Latar Belakang & Rumusan</th>
                                <th>Tinjauan Pustaka</th>
                                <th>Kerangka Konseptual</th>
                                <th>Metode Penelitian</th>
                                <th>Hasil & Pembahasan</th>
                                <th>Kemampuan Menjelaskan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>1</b></td>
                                <td>Latar belakang, permasalahan, tujuan dan kontribusi penelitian tidak terkait</td>
                                <td>Tinjauan dan daftar pustaka tidak relevan dan tidak mutakhir</td>
                                <td>Tidak memiliki kerangka konsep</td>
                                <td>Metode tidak sesuai panduan</td>
                                <td>Teknik analisa, ketajaman dan solusi tidak saling terkait</td>
                                <td>Tidak mampu menjawab seluruh pertanyaan</td>
                            </tr>
                            <tr>
                                <td><b>2</b></td>
                                <td>Latar belakang, permasalahan, tujuan tidak terkait</td>
                                <td>Sebagian besar tidak relevan dan tidak mutakhir</td>
                                <td>Dijabarkan namun tidak terkait</td>
                                <td>Tidak tepat, tidak sesuai</td>
                                <td>Teknik dan ketajaman belum mampu menjawab masalah</td>
                                <td>Mampu menjelaskan, menjawab sedikit pertanyaan</td>
                            </tr>
                            <tr>
                                <td><b>3</b></td>
                                <td>Latar belakang dan permasalahan tidak terkait</td>
                                <td>Sebagian relevan namun tidak mutakhir</td>
                                <td>Dijabarkan namun sedikit terkait</td>
                                <td>Tahapan sesuai, namun metode analisis belum sesuai</td>
                                <td>Teknik dan ketajaman belum mampu menjawab</td>
                                <td>Mampu menjelaskan, menjawab sebagian</td>
                            </tr>
                            <tr>
                                <td><b>5</b></td>
                                <td>Terkait namun belum didasarkan data valid</td>
                                <td>Relevan namun tidak mutakhir</td>
                                <td>Sebagian besar terkait namun tidak runtut</td>
                                <td>Tahapan tepat, analisis belum sesuai</td>
                                <td>Teknik dan ketajaman sesuai namun solusi belum mampu menjawab</td>
                                <td>Menjawab hampir seluruh pertanyaan</td>
                            </tr>
                            <tr>
                                <td><b>6</b></td>
                                <td>Terkait dan didasarkan data valid</td>
                                <td>Relevan, sebagian tidak mutakhir</td>
                                <td>Terkait namun tidak runtut</td>
                                <td>Tahapan tepat, bahasa belum baku</td>
                                <td>Teknik dan ketajaman sesuai, solusi belum sempurna</td>
                                <td>Mampu menjawab seluruh pertanyaan</td>
                            </tr>
                            <tr>
                                <td><b>7</b></td>
                                <td>Terkait, data valid, runtut</td>
                                <td>Sangat relevan dan sangat mutakhir</td>
                                <td>Terkait dan runtut</td>
                                <td>Tahapan tepat, bahasa baku</td>
                                <td>Teknik, ketajaman, solusi sempurna</td>
                                <td>Menjawab seluruh pertanyaan dan meyakinkan penguji</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>