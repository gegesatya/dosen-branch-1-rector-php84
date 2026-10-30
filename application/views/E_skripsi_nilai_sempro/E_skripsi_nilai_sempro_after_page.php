<style>
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

    .modal-slate .grading-scale {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
        color: #1e3a8a;
        font-size: 0.9rem;
    }

    .modal-slate .grading-scale-title {
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modal-slate .grading-scale p {
        margin: 0;
        line-height: 1.5;
    }

    .modal-slate .grading-scale .scale-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .modal-slate .scale-badge {
        background: #fff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e40af;
    }

    .modal-slate .form-horizontal .control-label {
        text-align: left;
        font-weight: 600;
        color: #334155;
        padding-top: 8px;
    }

    .modal-slate .form-group {
        margin-bottom: 8px;
        background: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .modal-slate .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        box-shadow: none;
        transition: all 0.2s;
        height: 40px;
        font-size: 1rem;
    }

    .modal-slate .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .modal-slate .score-display {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 12px;
        font-weight: 700;
        color: #0f172a;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .modal-slate .total-group {
        background: #0f172a;
        border: none;
        color: #fff;
    }

    .modal-slate .total-group .control-label {
        color: #f8fafc;
    }

    .modal-slate .total-score {
        font-size: 1.25rem;
        font-weight: 700;
        background: transparent;
        color: #fff;
        border: none;
        text-align: center;
        height: 40px;
    }

    .modal-slate .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .modal-slate .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s;
        letter-spacing: 0.3px;
    }

    .modal-slate .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .modal-slate .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
    }

    .modal-slate .btn-danger {
        background-color: #fff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .modal-slate .btn-danger:hover {
        background-color: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }
</style>
<script type="text/javascript">
    var save_method;
    var current_rerata = 0;

    function check_kelulusan_sempro(obj) {
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


    $(document).ready(function () {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $("#revisi_judul").hide();

        $("input").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });

        if ($.fn.summernote) {
            $('#form_catatan .summernote').summernote({
                height: 250,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview']]
                ]
            });
        }

        $('[name="nilai_komponen_a"]').on('input', function (e) {
            var a = Math.round(Number($('[name="nilai_komponen_a"]').val()) * 0.25 * 100) / 100;
            $('#n1').text(a);
        });

        $('[name="nilai_komponen_b"]').on('input', function (e) {
            var a = Math.round(Number($('[name="nilai_komponen_b"]').val()) * 0.2 * 100) / 100;
            $('#n2').text(a);
        });

        $('[name="nilai_komponen_c"]').on('input', function (e) {
            var a = Math.round(Number($('[name="nilai_komponen_c"]').val()) * 0.25 * 100) / 100;
            $('#n3').text(a);
        });

        $('[name="nilai_komponen_d"]').on('input', function (e) {
            var a = Math.round(Number($('[name="nilai_komponen_d"]').val()) * 0.15 * 100) / 100;
            $('#n4').text(a);
        });

        $('[name="nilai_komponen_e"]').on('input', function (e) {
            var a = Math.round(Number($('[name="nilai_komponen_e"]').val()) * 0.15 * 100) / 100;
            $('#n5').text(a);
        });

        $('.bangke').on('input', function (e) {
            var total = Number($('#n1').text()) + Number($('#n2').text()) + Number($('#n3').text()) + Number($('#n4').text()) + Number($('#n5').text());
            total = Math.round(total * 100) / 100;
            $('#total').val(total);
        });

        $('[name="seminar_nim_moderator"]').on('input', function (e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function (responseData) {
                if (responseData) {
                    $('[name="seminar_moderator"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_moderator"]').val('');
                }
            }).fail(function () {
                $('[name="seminar_moderator"]').val('');
            });
        });

        $('[name="seminar_nim_pembahas_1"]').on('input', function (e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function (responseData) {
                if (responseData) {
                    $('[name="seminar_pembahas_1"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_pembahas_1"]').val('');
                }
            }).fail(function () {
                $('[name="seminar_pembahas_1"]').val('');
            });
        });

        $('[name="seminar_nim_pembahas_2"]').on('input', function (e) {
            var nim = $(this).val();
            $.ajax({
                url: "<?php echo site_url('E_skripsi_nilai_sempro/get_mahasiswa/') ?>",
                data: {
                    nim: nim,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                type: 'post',
                dataType: "JSON",
            }).done(function (responseData) {
                if (responseData) {
                    $('[name="seminar_pembahas_2"]').val(responseData.nm_pd);
                } else {
                    $('[name="seminar_pembahas_2"]').val('');
                }


            }).fail(function () {
                $('[name="seminar_pembahas_2"]').val('');
            });
        });

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

    function edit_nilai_sempro(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_nilai_sempro/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                var fallback_url = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=random";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo_url = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback_url;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback_url + "';}; this.src='" + original_photo + "';";
                $('#nilai_student_photo').attr('src', photo_url).attr('onerror', onerror_script);
                $('#student_nim').text(data.nipd);
                $('#student_name').text(data.nm_pd);
                $('#student_title').text(data.judul_ditetapkan);
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

                if (data.ba_sudah_diisi) {
                    $('#nilai_ba_warning').show();
                } else {
                    $('#nilai_ba_warning').hide();
                }

                $('#modal_form').modal('show');
                $('.modal-title').text("Nilai Seminar Proposal");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    var catatanCache = {};

    function edit_catatan(id, show_modal = true) {
        save_method = 'update';

        if (catatanCache[id]) {
            if (catatanCache[id].ba_sudah_diisi) {
                $('#catatan_ba_warning').show();
            } else {
                $('#catatan_ba_warning').hide();
            }
            if (show_modal) {
                $('#modal_form_catatan').modal('show');
            }
            return;
        }

        $('#form_catatan')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_nilai_sempro/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {

                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                // Set student info
                var fallback_url = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=random";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo_url = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback_url;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback_url + "';}; this.src='" + original_photo + "';";
                $('#catatan_student_photo').attr('src', photo_url).attr('onerror', onerror_script);
                $('#catatan_student_nim').text(data.nipd);
                $('#catatan_student_name').text(data.nm_pd);
                $('#catatan_student_title').text(data.judul_ditetapkan);

                // Set Summernote content
                if ($.fn.summernote) {
                    $('[name="catatan_judul"]').summernote('code', data.catatan_judul || '');
                    $('[name="catatan_latar_belakang"]').summernote('code', data.catatan_latar_belakang || '');
                    $('[name="catatan_rumusan_masalah"]').summernote('code', data.catatan_rumusan_masalah || '');
                    $('[name="catatan_tujuan_penelitian"]').summernote('code', data.catatan_tujuan_penelitian || '');
                    $('[name="catatan_tinjauan_pustaka"]').summernote('code', data.catatan_tinjauan_pustaka || '');
                    $('[name="catatan_metode_penelitian"]').summernote('code', data.catatan_metode_penelitian || '');
                    $('[name="catatan_daftar_pustaka"]').summernote('code', data.catatan_daftar_pustaka || '');
                    $('[name="catatan_format_penulisan"]').summernote('code', data.catatan_format_penulisan || '');
                } else {
                    $('[name="catatan_judul"]').val(data.catatan_judul);
                    $('[name="catatan_latar_belakang"]').val(data.catatan_latar_belakang);
                    $('[name="catatan_rumusan_masalah"]').val(data.catatan_rumusan_masalah);
                    $('[name="catatan_tujuan_penelitian"]').val(data.catatan_tujuan_penelitian);
                    $('[name="catatan_tinjauan_pustaka"]').val(data.catatan_tinjauan_pustaka);
                    $('[name="catatan_metode_penelitian"]').val(data.catatan_metode_penelitian);
                    $('[name="catatan_daftar_pustaka"]').val(data.catatan_daftar_pustaka);
                    $('[name="catatan_format_penulisan"]').val(data.catatan_format_penulisan);
                }

                // Reset tabs to first tab
                $('.nav-pills a[href="#tab_judul"]').tab('show');

                if (data.ba_sudah_diisi) {
                    $('#catatan_ba_warning').show();
                } else {
                    $('#catatan_ba_warning').hide();
                }

                catatanCache[id] = { loaded: true, ba_sudah_diisi: data.ba_sudah_diisi };

                if (show_modal) {
                    $('#modal_form_catatan').modal('show');
                }
                $('#modal_form_catatan .modal-title').text("Usulan Revisi");

            },
            error: function (jqXHR, textStatus, errorThrown) {
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
            success: function (data) {
                $('#revisi_judul').hide();
                $('#revisi_judul').val();
                $('[name="id_nilai_sempro"]').val(data.id_nilai_sempro);
                $('[name="id_judul_skripsi"]').val(data.id_judul_skripsi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="judul_ditetapkan_1"]').val(data.judul_ditetapkan);

                var fallback = "https://ui-avatars.com/api/?name=" + encodeURIComponent(data.nm_pd) + "&background=3b82f6&color=fff";
                var original_photo = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                var photo = (data.foto && data.foto != '') ? "https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_" + data.foto : fallback;
                var onerror_script = "this.onerror=function(){this.onerror=null;this.src='" + fallback + "';}; this.src='" + original_photo + "';";
                $('#ba_student_photo').attr('src', photo).attr('onerror', onerror_script);
                $('#ba_student_nim').text(data.nipd);
                $('#ba_student_name').text(data.nm_pd);
                $('#ba_student_title').text(data.judul_ditetapkan);

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
                if (data.sempro_hasil == 'layak') {
                    $('[name="sempro_hasil"]').prop('checked', true);
                } else {
                    $('[name="sempro_hasil"]').prop('checked', false);
                }
                current_rerata = data.rerata_nilai || 0;
                $('#tbl_nilai').html(data.tbl_nilai);

                $('#modal_form_berita_acara').modal('show');
                $('#modal_form_berita_acara .modal-title').text("Berita Acara Seminar Proposal");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        window.location.reload();
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
            success: function (data) {

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
            error: function (jqXHR, textStatus, errorThrown) {
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
            success: function (data) {

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
            error: function (jqXHR, textStatus, errorThrown) {
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
            success: function (data) {

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
            error: function (jqXHR, textStatus, errorThrown) {
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

<div class="modal fade modal-slate" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Person Form</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_nilai_sempro" />
                <div class="form-body">

                    <!-- Student Card Section -->
                    <div class="modal-student-header">
                        <img id="nilai_student_photo" src="" alt="Photo" class="modal-student-photo">
                        <div class="modal-student-info">
                            <h4 id="student_identity" style="margin-bottom: 8px;">[<span id="student_nim">NIM
                                    Mahasiswa</span>] <span id="student_name">Nama Mahasiswa</span></h4>
                            <p class="title" id="student_title">Judul Skripsi</p>
                        </div>
                    </div>

                    <!-- Warning Berita Acara -->
                    <div id="nilai_ba_warning" style="display: none; background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                        <i class="fa fa-exclamation-triangle" style="margin-right: 6px; font-size: 15px;"></i>
                        <strong>Perhatian:</strong> Berita Acara untuk mahasiswa ini sudah disetujui. Berhati-hatilah saat mengubah nilai.
                    </div>

                    <!-- Grading Scale Section -->
                    <div class="grading-scale">
                        <div class="grading-scale-title">
                            <i class="fa fa-info-circle"></i> Keterangan Skala Penilaian
                        </div>
                        <div class="scale-badges">
                            <span class="scale-badge">1 = Buruk</span>
                            <span class="scale-badge">2 = Sangat Kurang</span>
                            <span class="scale-badge">3 = Kurang</span>
                            <span class="scale-badge">5 = Cukup</span>
                            <span class="scale-badge">6 = Baik</span>
                            <span class="scale-badge">7 = Sangat Baik</span>
                        </div>
                    </div>

                    <!-- Hidden fields for the form to not break existing logic -->
                    <input name="nipd" type="hidden">
                    <input name="nm_pd" type="hidden">

                    <div class="form-group">
                        <label class="control-label col-md-6">Latar Belakang (25%)</label>
                        <div class="col-md-3">
                            <input name="nilai_komponen_a" class="form-control bangke" type="number" step="1" min="1"
                                max="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="score-display"><span name="n1" id="n1">0</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6">Tinjauan Pustaka (20%)</label>
                        <div class="col-md-3">
                            <input name="nilai_komponen_b" class="form-control bangke" type="number" step="1" min="1"
                                max="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="score-display"><span name="n2" id="n2">0</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6">Kerangka Konseptual (25%)</label>
                        <div class="col-md-3">
                            <input name="nilai_komponen_c" class="form-control bangke" type="number" step="1" min="1"
                                max="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="score-display"><span name="n3" id="n3">0</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6">Metode Penelitian (15%)</label>
                        <div class="col-md-3">
                            <input name="nilai_komponen_d" class="form-control bangke" type="number" step="1" min="1"
                                max="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="score-display"><span name="n4" id="n4">0</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-6">Kemampuan Menjelaskan (15%)</label>
                        <div class="col-md-3">
                            <input name="nilai_komponen_e" class="form-control bangke" type="number" step="1" min="1"
                                max="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-3">
                            <div class="score-display"><span name="n5" id="n5">0</span></div>
                        </div>
                    </div>
                    <div class="form-group total-group">
                        <label class="control-label col-md-6">TOTAL NILAI</label>
                        <div class="col-md-6">
                            <input name="total" id="total" class="form-control total-score" type="text" readonly>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-slate" id="modal_form_catatan" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 900px; max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Usulan Revisi</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_catatan'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_nilai_sempro" />
                <input name="nipd" type="hidden">
                <input name="nm_pd" type="hidden">
                <div class="form-body">

                    <!-- Student Card Section -->
                    <div class="modal-student-header">
                        <img id="catatan_student_photo" src="" alt="Photo" class="modal-student-photo">
                        <div class="modal-student-info">
                            <h4 style="margin-bottom: 8px;">[<span id="catatan_student_nim">NIM</span>] <span
                                    id="catatan_student_name">Nama Mahasiswa</span></h4>
                            <p class="title" id="catatan_student_title">Judul Skripsi</p>
                        </div>
                    </div>

                    <!-- Warning Berita Acara -->
                    <div id="catatan_ba_warning" style="display: none; background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                        <i class="fa fa-exclamation-triangle" style="margin-right: 6px; font-size: 15px;"></i>
                        <strong>Perhatian:</strong> Berita Acara untuk mahasiswa ini sudah disetujui. Berhati-hatilah saat mengubah usulan revisi.
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <ul class="nav nav-pills nav-stacked"
                                style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; padding: 4px;">
                                <li class="active"><a data-toggle="pill" href="#tab_judul"
                                        style="border-radius: 6px;">A. Judul</a></li>
                                <li><a data-toggle="pill" href="#tab_latar" style="border-radius: 6px;">B. Latar
                                        Belakang</a></li>
                                <li><a data-toggle="pill" href="#tab_rumusan" style="border-radius: 6px;">C. Rumusan
                                        Masalah</a></li>
                                <li><a data-toggle="pill" href="#tab_tujuan" style="border-radius: 6px;">D.
                                        Tujuan/Manfaat</a></li>
                                <li><a data-toggle="pill" href="#tab_tinjauan" style="border-radius: 6px;">E. Tinjauan
                                        Pustaka</a></li>
                                <li><a data-toggle="pill" href="#tab_metode" style="border-radius: 6px;">F. Metode
                                        Penelitian</a></li>
                                <li><a data-toggle="pill" href="#tab_daftar" style="border-radius: 6px;">G. Daftar
                                        Pustaka</a></li>
                                <li><a data-toggle="pill" href="#tab_format" style="border-radius: 6px;">H. Format
                                        Penulisan</a></li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content"
                                style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; min-height: 350px;">
                                <div id="tab_judul" class="tab-pane fade in active">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Judul</h4>
                                    <textarea name="catatan_judul" class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_latar" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Latar Belakang</h4>
                                    <textarea name="catatan_latar_belakang" class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_rumusan" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Rumusan Masalah</h4>
                                    <textarea name="catatan_rumusan_masalah" class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_tujuan" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Tujuan & Manfaat</h4>
                                    <textarea name="catatan_tujuan_penelitian"
                                        class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_tinjauan" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Tinjauan Pustaka</h4>
                                    <textarea name="catatan_tinjauan_pustaka"
                                        class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_metode" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Metode Penelitian</h4>
                                    <textarea name="catatan_metode_penelitian"
                                        class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_daftar" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Daftar Pustaka</h4>
                                    <textarea name="catatan_daftar_pustaka" class="form-control summernote"></textarea>
                                </div>
                                <div id="tab_format" class="tab-pane fade">
                                    <h4 style="margin-top: 0; color: #1e293b; font-weight: 700; margin-bottom: 16px;">
                                        Catatan: Format Penulisan</h4>
                                    <textarea name="catatan_format_penulisan"
                                        class="form-control summernote"></textarea>
                                </div>
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

<div class="modal fade" id="modal_form_berita_acara" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Berita Acara Seminar Proposal</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
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
                        <label class="control-label col-md-3">Hari/Tanggal</label>
                        <div class="col-md-9">
                            <input name="seminar_hari" placeholder="Hari" class="form-control" type="text"
                                style="width:45%; display:inline-block;" readonly>
                            <input name="seminar_tanggal" class="form-control" type="date"
                                style="width:45%; display:inline-block; margin-left: 2%;"
                                onchange="const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; document.getElementsByName('seminar_hari')[0].value = this.value ? days[new Date(this.value).getDay()] : '';">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Jam</label>
                        <div class="col-md-9">
                            <input name="seminar_jam_mulai" placeholder="hh:mm" class="form-control text-center"
                                type="text" maxlength="5" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                oninput="this.value = this.value.replace(/[^0-9:]/g, '');"
                                style="width:40%; display:inline-block;">
                            <span
                                style="display:inline-block; width:10%; text-align:center; font-weight:bold;">s/d</span>
                            <input name="seminar_jam_selesai" placeholder="hh:mm" class="form-control text-center"
                                type="text" maxlength="5" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                oninput="this.value = this.value.replace(/[^0-9:]/g, '');"
                                style="width:40%; display:inline-block;">
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
                            <input name="seminar_nim_moderator" id="seminar_nim_moderator" placeholder="NIM Moderator"
                                class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_moderator" id="seminar_moderator" placeholder="Nama Moderator"
                                class="form-control" type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas/NIM</label>
                        <div class="col-md-3">
                            <input name="seminar_nim_pembahas_1" placeholder="NIM Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_pembahas_1" placeholder="Nama Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Pembahas/NIM</label>
                        <div class="col-md-3">
                            <input name="seminar_nim_pembahas_2" placeholder="NIM Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                        <div class="col-md-6">
                            <input name="seminar_pembahas_2" placeholder="Nama Pembahas" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Jumlah Peserta</label>
                        <div class="col-md-3">
                            <input name="seminar_jumlah_mahasiswa" placeholder="Jumlah Mahasiswa" class="form-control"
                                type="text">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Status</label>
                        <div class="col-md-9">
                            <div style="padding-top: 7px;">
                                <label
                                    style="font-weight: 600; color: #10B981; margin-bottom: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="sempro_hasil" value="layak"
                                        style="margin: 0; cursor: pointer;" onclick="check_kelulusan_sempro(this)">
                                    Layak untuk dapat dilanjutkan pada tahapan Penelitian dengan usulan revisi
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
                <button type="button" id="btnSaveBeritaAcara" onclick="save_berita_acara()"
                    class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_rubrik" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian tidak selaras
                                dengan topik dan judul penelitian serta kemampuan menjelaskan sangat buruk diberi nilai
                                1</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian sebagian
                                selaras dengan topik dan judul penelitian serta kemampuan menjelaskan sangat buruk
                                diberi nilai 2</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian sebagian besar
                                selaras dengan topik dan judul penelitian serta kemampuan menjelaskan buruk diberi nilai
                                3</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan
                                topik dan judul penelitian namun kemampuan menjelaskan kurang baik diberi nilai 5</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan
                                topik dan judul penelitian namun kemampuan menjelaskan baik diberi nilai 6</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Latar belakang, tinjauan pustaka, kerangka konspetual, metode penelitian selaras dengan
                                topik dan judul penelitian serta kemampuan menjelaskan sangat baik diberi nilai 7</td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>