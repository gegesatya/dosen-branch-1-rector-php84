<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
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
                    "url": "<?php echo site_url('C_jadwal/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5, 6, 7],
                    "orderable": false,
                }, ],

            });
        }

        return {
            init: function() {
                handleRecords();
            }

        };

    }();

    $(document).ready(function() {
        // Prevent scroll propagation on modal scrollers
        $('#modal_krs_notification .scroller').on('mousewheel DOMMouseScroll', function(e) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;

            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "auto",
            todayBtn: true,
            todayHighlight: true,
            forceParse: false
        });

        $('#form_copy').submit(function(e) {
            e.preventDefault();
            var idJadwal = $('[name="id_jadwal"]').val();
            var syncFreq = $('input[name="sync_freq"]:checked', this).val();
            var idJadwal2 = $('[name="id_jadwal_2"]', this).val();

            if (syncFreq == '2' && !idJadwal2) {
                alert('Silakan pilih slot Jadwal Sesi 2 (Praktik / Pertemuan Genap) terlebih dahulu.');
                $('[name="id_jadwal_2"]').focus();
                return;
            }

            $('#btnSave').html('<i class="fa fa-spinner fa-spin"></i> Generating...');
            $('#btnSave').attr('disabled', true);
            $.ajax({
                url: '<?php echo site_url('C_jadwal/ajax_generate_bap/') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_copy').serialize(),
                success: function(data) {
                    $('#btnSave').html('<i class="fa fa-magic"></i> Generate Pertemuan');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_copy').modal('hide');
                        reload_table_ajax();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Pertemuan Berhasil Digenerate!',
                                html: (data.msg || '16 Rencana Pertemuan dan Draf BAP berhasil disinkronkan.') + '<br><br><a href="<?= site_url("C_strategi_pertemuan/index/"); ?>/' + idJadwal + '" class="btn btn-sm btn-info mr-1" style="margin-top:8px; color:#fff;"><i class="fa fa-book"></i> Buka Strategi Pertemuan</a> <a href="<?= site_url("C_rencana_pertemuan/index/"); ?>/' + idJadwal + '" class="btn btn-sm btn-primary" style="margin-top:8px;"><i class="fa fa-calendar-check-o"></i> Virtual Class</a>',
                                icon: 'success',
                                confirmButtonColor: '#059669',
                                confirmButtonText: 'Tutup'
                            });
                        } else {
                            alert(data.msg || 'Pertemuan berhasil digenerate.');
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Gagal Generate',
                                text: data.msg || 'Terjadi kesalahan saat generate pertemuan.',
                                icon: 'error',
                                confirmButtonColor: '#dc2626'
                            });
                        } else {
                            alert('Gagal: ' + (data.msg || 'Terjadi kesalahan.'));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#btnSave').html('<i class="fa fa-magic"></i> Generate Pertemuan');
                    $('#btnSave').attr('disabled', false);
                    alert('Terjadi kesalahan jaringan: ' + (xhr.responseText || error));
                }
            });
        });

        $('#form_rps').submit(function(e) {
            e.preventDefault();
            $('#btnSaveRps').text('Saving...');
            $('#btnSaveRps').attr('disabled', true);
            $.ajax({
                url: '<?php echo site_url('C_jadwal/ajax_save_rps/') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_rps').serialize(),
                success: function(data) {
                    $('#btnSaveRps').text('Save');
                    $('#btnSaveRps').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_rps').modal('hide');
                        reload_table_ajax();
                    } else {

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });

        $('#form_skema_nilai').submit(function(e) {
            e.preventDefault();
            $('#btnSkema').text('Saving...');
            $('#btnSkema').attr('disabled', true);


            $.ajax({
                url: '<?php echo site_url('C_skema_nilai/ajax_update_skema_nilai') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_skema_nilai').serialize(),
                success: function(data) {
                    $('#btnSkema').text('Save');
                    $('#btnSkema').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_skema_nilai').modal('hide');
                        reload_table_ajax();
                    } else {

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });

        <?php if (!empty($krs_stats['need_approval']) || !empty($krs_stats['draft'])) : ?>
            $('#modal_krs_notification').modal('show');
        <?php endif; ?>
    });

    function reload_table_ajax() {
        table.ajax.reload(null, false);
    }



    var copyAvailableSchedules = [];

    function toggleCopyFreq(freq) {
        if (freq == 2) {
            $('#box_copy_sesi_2').slideDown(200);
        } else {
            $('#box_copy_sesi_2').slideUp(200);
        }
    }

    function ajax_copy(id_rps, id_jadwal) {
        $('#form_copy')[0].reset();
        $('.form-group').removeClass('has-error');
        $('input[name="sync_freq"][value="1"]').prop('checked', true);
        $('#box_copy_sesi_2').hide();

        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_copy_data') ?>" + "/" + id_rps,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(id_rps);
                $('[name="id_jadwal"]').val(id_jadwal);
                $('#copy_badge_kode').text(data.kode_mk || 'MK');
                $('#copy_text_mk').text(data.nm_mk || data.general_mk || 'Mata Kuliah');
                $('#copy_text_dosen').text(data.nm_ptk || data.dosen_pengampu || '-');

                // Populate Dropdown Sesi 2 jika ada jadwal aktif lain
                copyAvailableSchedules = data.schedules || [];
                var $sel2 = $('#select_copy_jadwal_2');
                $sel2.empty().append('<option value="">-- Pilih Slot Jadwal Sesi 2 --</option>');
                
                if (copyAvailableSchedules.length > 1) {
                    copyAvailableSchedules.forEach(function(s) {
                        if (s.id_jadwal == id_jadwal) return;
                        $sel2.append('<option value="' + s.id_jadwal + '">' + s.label_formatted + '</option>');
                    });
                } else {
                    $sel2.append('<option value="">-- Hanya ada 1 slot jadwal aktif --</option>');
                }

                $('#modal_form_copy').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error mengambil data RPS dari server');
            }
        });
    }

    function ajax_pilih_rps(id_jadwal) {
        $('#form_rps')[0].reset();
        $('.form-group').removeClass('has-error');

        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_rps') ?>" + "/" + id_jadwal,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="nama_hari"]').val(data.nama_hari);
                $('[name="tag_name"]').val(data.tag_name);
                $('[name="jam"]').val(data.jam);
                $('[name="matkul"]').html(data.matkul);
                $('[name="id_jadwal"]').val(id_jadwal);

                $('#modal_form_rps').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Pilih RPS");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function ajax_download(id_smt) {
        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_journey_by_smt') ?>" + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#tabel').html(data.tabel);

                $('#modal_form_download').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Perkuliahan Tahun " + id_smt);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function delete_rps(id_jadwal) {
        if (confirm('Anda akan melakukan reset RPS, data pertemuan dan absen yang sudah di generate juga akan dihapus. Apakah Anda ingin melanjutkan??')) {
            $.ajax({
                url: "<?php echo site_url('C_jadwal/ajax_delete_rps') ?>/" + id_jadwal,
                type: "GET",
                dataType: "JSON",
                data: function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
                success: function(data) {
                    reload_table_ajax();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
    }

    var currentObeBlueprint = null;

    $(function() {
        $(".form-control").change(function() {
            $(this).parent().parent().removeClass('has-error');
            if ($(this).next().hasClass("help-block")) {
                $(this).next().empty();
            }
        });

        $(".komponen-reguler").on('input change', function(e) {
            hitungTotalSkemaNilai();
        });
    });

    function updateSlotActiveStates() {
        var keys = ['sikap', 'quiz', 'tugas_terstruktur', 'uts', 'uas', 'proyek', 'unjuk_kinerja', 'portofolio', 'produk', 'presentasi'];
        keys.forEach(function(k) {
            var val = Number($('[name="' + k + '"]').val()) || 0;
            var $card = $('#slot_card_' + k);
            if (val > 0) {
                $card.addClass('has-value');
            } else {
                $card.removeClass('has-value');
            }
        });
    }

    function hitungTotalSkemaNilai() {
        var s_sikap = Number($('[name="sikap"]').val()) || 0;
        var s_quiz = Number($('[name="quiz"]').val()) || 0;
        var s_tugas = Number($('[name="tugas_terstruktur"]').val()) || 0;
        var s_uts = Number($('[name="uts"]').val()) || 0;
        var s_uas = Number($('[name="uas"]').val()) || 0;
        var s_kinerja = Number($('[name="unjuk_kinerja"]').val()) || 0;
        var s_proyek = Number($('[name="proyek"]').val()) || 0;
        var s_porto = Number($('[name="portofolio"]').val()) || 0;
        var s_produk = Number($('[name="produk"]').val()) || 0;
        var s_pres = Number($('[name="presentasi"]').val()) || 0;

        var total = s_sikap + s_quiz + s_tugas + s_uts + s_uas + s_kinerja + s_proyek + s_porto + s_produk + s_pres;

        // Update progress bar
        var clampedProgress = Math.min(total, 100);
        $('#skema_progress_bar').css('width', clampedProgress + '%');
        $('#skema_total_text').text(total + '%');

        var $box = $('#jumlah_total');
        if (total === 100) {
            $('#skema_progress_bar').css('background-color', '#10b981');
            $box.removeClass('alert-info alert-warning alert-danger').addClass('alert-success')
                .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#166534;"><i class="fa fa-check-circle"></i> Total Bobot: 100%</strong> — <span style="font-size:12px; color:#15803d;">Sesuai Standar SPMI &amp; Siap Disimpan</span></div><span class="badge" style="background:#16a34a; font-size:11px; padding:4px 8px;"><i class="fa fa-lock"></i> Valid 100%</span></div>');
            $('#btnSkema').prop('disabled', false).css({'opacity': '1', 'cursor': 'pointer'});
        } else if (total > 100) {
            var over = total - 100;
            $('#skema_progress_bar').css('background-color', '#ef4444');
            $box.removeClass('alert-info alert-success alert-warning').addClass('alert-danger')
                .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#991b1b;"><i class="fa fa-times-circle"></i> Total Bobot: ' + total + '%</strong> — <span style="font-size:12px; color:#b91c1c;">Melebihi 100% (Kurangi ' + over + '%)</span></div><span class="badge" style="background:#dc2626; font-size:11px; padding:4px 8px;">Over +' + over + '%</span></div>');
            $('#btnSkema').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
        } else {
            var under = 100 - total;
            $('#skema_progress_bar').css('background-color', '#f59e0b');
            $box.removeClass('alert-info alert-success alert-danger').addClass('alert-warning')
                .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#92400e;"><i class="fa fa-exclamation-triangle"></i> Total Bobot: ' + total + '%</strong> — <span style="font-size:12px; color:#b45309;">Belum mencapai 100% (Kurang ' + under + '% lagi)</span></div><span class="badge" style="background:#d97706; font-size:11px; padding:4px 8px;">Kurang -' + under + '%</span></div>');
            $('#btnSkema').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
        }

        updateSlotActiveStates();
    }

    /* Drag & Drop HTML5 Handlers */
    function handleObeDragStart(ev, cardId, percent, suggestedTarget) {
        var dataPayload = JSON.stringify({
            cardId: cardId,
            percent: percent,
            suggestedTarget: suggestedTarget
        });
        ev.dataTransfer.setData("text/plain", dataPayload);
        ev.dataTransfer.effectAllowed = "copy";
        $(ev.currentTarget).addClass('dragging');
    }

    function handleObeDragEnd(ev) {
        $(ev.currentTarget).removeClass('dragging');
    }

    function handleObeDragOver(ev) {
        ev.preventDefault();
        ev.dataTransfer.dropEffect = "copy";
        $(ev.currentTarget).addClass('slot-drag-hover');
    }

    function handleObeDragLeave(ev) {
        $(ev.currentTarget).removeClass('slot-drag-hover');
    }

    function handleObeDrop(ev, targetSlotKey) {
        ev.preventDefault();
        $(ev.currentTarget).removeClass('slot-drag-hover');
        var raw = ev.dataTransfer.getData("text/plain");
        if (!raw) return;
        try {
            var payload = JSON.parse(raw);
            $('[name="' + targetSlotKey + '"]').val(payload.percent).trigger('input');
            
            var $slot = $('#slot_card_' + targetSlotKey);
            $slot.addClass('slot-pulse');
            setTimeout(function() { $slot.removeClass('slot-pulse'); }, 600);
        } catch(e) {
            console.error("Drop error:", e);
        }
    }

    function quickAssignObeCard(targetSlotKey, percent) {
        $('[name="' + targetSlotKey + '"]').val(percent).trigger('input');
        var $slot = $('#slot_card_' + targetSlotKey);
        $slot.addClass('slot-pulse');
        setTimeout(function() { $slot.removeClass('slot-pulse'); }, 600);
    }

    function clearSlot(targetSlotKey) {
        $('[name="' + targetSlotKey + '"]').val('').trigger('input');
    }

    function autoMapObeToSiakad() {
        if (!currentObeBlueprint || !currentObeBlueprint.auto_map_values) {
            alert('Data matriks asesmen OBE tidak tersedia.');
            return;
        }
        var autoMap = currentObeBlueprint.auto_map_values;
        for (var key in autoMap) {
            if (autoMap.hasOwnProperty(key)) {
                $('[name="' + key + '"]').val(autoMap[key] > 0 ? autoMap[key] : '');
            }
        }
        hitungTotalSkemaNilai();

        $('.siakad-slot-card').addClass('slot-pulse');
        setTimeout(function() {
            $('.siakad-slot-card').removeClass('slot-pulse');
        }, 600);
    }

    function resetSiakadSlots() {
        var keys = ['sikap', 'quiz', 'tugas_terstruktur', 'uts', 'uas', 'unjuk_kinerja', 'proyek', 'portofolio', 'produk', 'presentasi'];
        keys.forEach(function(k) {
            $('[name="' + k + '"]').val('');
        });
        hitungTotalSkemaNilai();
    }

    function notify_need_rps() {
        if (typeof swal === 'function') {
            swal({
                title: 'RPS Belum Diterapkan',
                text: 'Jadwal perkuliahan ini belum memiliki RPS / rencana pertemuan aktif. Silakan buat atau terapkan RPS OBE terlebih dahulu melalui Studio RPS.',
                type: 'warning',
                confirmButtonText: 'Buka Studio RPS',
                showCancelButton: true,
                cancelButtonText: 'Tutup'
            }).then(function(result) {
                if (result && result.value) {
                    window.open('<?php echo site_url("C_rps_generator"); ?>', '_blank');
                }
            });
        } else {
            alert('Jadwal perkuliahan ini belum memiliki RPS / rencana pertemuan aktif. Silakan buat atau terapkan RPS OBE terlebih dahulu.');
        }
    }

    function edit_skema_nilai(id) {
        save_method = 'update_skema_nilai';
        $('#form_skema_nilai')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#btnSkema').html('<i class="fa fa-check"></i> Simpan Skema Nilai');
        $('#btnSkema').attr('disabled', false);

        $.ajax({
            url: "<?php echo site_url('C_skema_nilai/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.status === false || data.need_rps) {
                    if (typeof swal === 'function') {
                        swal({
                            title: 'RPS Belum Diterapkan',
                            text: data.msg || 'Jadwal perkuliahan ini belum memiliki RPS / rencana pertemuan aktif. Silakan buat atau terapkan RPS OBE terlebih dahulu di Studio RPS.',
                            type: 'warning',
                            confirmButtonText: 'Buka Studio RPS',
                            showCancelButton: true,
                            cancelButtonText: 'Tutup'
                        }).then(function(result) {
                            if (result && result.value) {
                                window.open('<?php echo site_url("C_rps_generator"); ?>', '_blank');
                            }
                        });
                    } else {
                        alert(data.msg || 'Harap buat atau terapkan RPS OBE terlebih dahulu.');
                    }
                    return;
                }

                $('[name="id"]').val(data.id);
                $('[name="tag_name"]').val(data.tag_name);
                $('[name="sikap"]').val(data.sikap || '');
                $('[name="quiz"]').val(data.quiz || '');
                $('[name="tugas_terstruktur"]').val(data.tugas_terstruktur || '');
                $('[name="uts"]').val(data.uts || '');
                $('[name="uas"]').val(data.uas || '');
                $('[name="unjuk_kinerja"]').val(data.unjuk_kinerja || '');
                $('[name="proyek"]').val(data.proyek || '');
                $('[name="portofolio"]').val(data.portofolio || '');
                $('[name="produk"]').val(data.produk || '');
                $('[name="presentasi"]').val(data.presentasi || '');

                currentObeBlueprint = data.obe_blueprint || null;

                // Render Kartu Komponen RPS OBE
                var $cardsContainer = $('#obe_cards_container');
                $cardsContainer.empty();

                if (currentObeBlueprint && currentObeBlueprint.cards && currentObeBlueprint.cards.length > 0) {
                    currentObeBlueprint.cards.forEach(function(c) {
                        var cTitle = c.title || c.name || 'Komponen';
                        var cWeight = (c.weight !== undefined) ? c.weight : (c.percent || 0);
                        var cTarget = c.target_key || c.suggested_target || 'tugas_terstruktur';
                        var cTargetLabel = c.target_label || c.suggested_target_label || 'Tugas Terstruktur';
                        var cBreakdown = c.breakdown ? '<div style="font-size:10px; color:#64748b; margin-top:3px; line-height:1.2; font-style:italic;"><i class="fa fa-info-circle"></i> ' + c.breakdown + '</div>' : '';

                        var cardHtml = 
                            '<div class="obe-drag-card" draggable="true" ' +
                            'ondragstart="handleObeDragStart(event, \'' + c.id + '\', ' + cWeight + ', \'' + cTarget + '\')" ' +
                            'ondragend="handleObeDragEnd(event)">' +
                                '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                                    '<div style="font-weight:700; font-size:12.5px; color:#0f172a; display:flex; align-items:center; gap:6px;">' +
                                        '<i class="fa fa-arrows" style="color:#94a3b8; font-size:11px; cursor:grab;" title="Seret kartu ini"></i> ' +
                                        '<i class="' + c.icon + '" style="color:' + c.color + ';"></i> ' +
                                        '<span>' + cTitle + '</span>' +
                                    '</div>' +
                                    '<span class="badge" style="background:' + c.color + '; font-size:12px; font-weight:800; padding:3px 8px;">' + cWeight + '%</span>' +
                                '</div>' +
                                cBreakdown +
                                '<div style="font-size:10.5px; color:#475569; margin-top:6px; display:flex; justify-content:space-between; align-items:center;">' +
                                    '<span><i class="fa fa-bullseye" style="color:' + c.color + ';"></i> Saran: <strong>' + cTargetLabel + '</strong></span>' +
                                    '<button type="button" class="btn btn-default btn-xs" onclick="quickAssignObeCard(\'' + cTarget + '\', ' + cWeight + ')" title="Terapkan langsung ke ' + cTargetLabel + '" style="padding:2px 8px; font-size:10.5px; border-radius:4px; background:' + c.bg + '; color:' + c.color + '; border-color:' + c.color + '; font-weight:700;">' +
                                        'Petakan &rarr;' +
                                    '</button>' +
                                '</div>' +
                            '</div>';
                        $cardsContainer.append(cardHtml);
                    });

                    $('#obe_blueprint_total_badge').text('Total ' + (currentObeBlueprint.total_sum || 100) + '%');
                    $('#obe_blueprint_table_wrap').html(currentObeBlueprint.table_html || '<p class="text-muted">Tidak ada tabel.</p>');
                } else {
                    $cardsContainer.html(
                        '<div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:16px; text-align:center; color:#64748b; font-size:12px;">' +
                            '<i class="fa fa-info-circle" style="font-size:20px; color:#94a3b8; margin-bottom:6px; display:block;"></i>' +
                            'Belum ada matriks asesmen terdeteksi pada RPS mata kuliah ini.<br>Anda dapat mengisi bobot secara manual pada 10 slot di sebelah kanan.' +
                        '</div>'
                    );
                    $('#obe_blueprint_total_badge').text('Default SPMI');
                    $('#obe_blueprint_table_wrap').html('<p class="text-muted">Matriks asesmen RPS tidak tersedia.</p>');
                }

                // Subtitle & Status Tag
                if (currentObeBlueprint && currentObeBlueprint.has_obe) {
                    $('#obe_status_tag').html('<span class="badge" style="background:#16a34a; font-size:11px; padding:5px 10px; border-radius:20px;"><i class="fa fa-check-circle"></i> RPS OBE Terhubung (' + (currentObeBlueprint.total_sum || 100) + '%)</span>');
                } else {
                    $('#obe_status_tag').html('<span class="badge" style="background:#f59e0b; font-size:11px; padding:5px 10px; border-radius:20px;"><i class="fa fa-info-circle"></i> Standar SPMI</span>');
                }

                hitungTotalSkemaNilai();

                $('#modal_form_skema_nilai').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#skema_modal_title').html('<i class="fa fa-sliders" style="color:#38bdf8; margin-right:8px;"></i> Studio Pemetaan Skema Nilai: <strong>' + (data.tag_name || '') + '</strong>');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error mengambil data skema nilai');
            }
        });
    }

    var currentViewingJadwalId = null;
    var currentViewingJadwalData = null;

    function open_rps_view_modal(el) {
        var raw = $(el).attr('data-rps');
        if (!raw) return;
        try {
            var d = JSON.parse(raw);
            currentViewingJadwalId = d.id_jadwal || null;
            currentViewingJadwalData = d;

            $('#modalViewKodeMk').text('[' + (d.kode_mk || 'MK') + ']');
            $('#modalViewNmMk').text(d.nm_mk || '-');
            $('#modalViewSks').html('<i class="fa fa-graduation-cap" style="color:#2563eb;"></i> <strong>' + (d.sks_mk || 3) + ' SKS</strong>');
            $('#modalViewKelas').html('<i class="fa fa-users" style="color:#4f46e5;"></i> Kelas ' + (d.nm_kls || '-'));
            $('#modalViewProdi').html('<i class="fa fa-university" style="color:#059669;"></i> ' + (d.nm_prodi || 'Program Studi'));
            $('#modalViewSemester').html('<i class="fa fa-clone" style="color:#64748b;"></i> ' + (d.semester || '-'));
            $('#modalViewStatusBadge').html((d.status_badge || '') + ' ' + (d.obe_badge || ''));
            $('#modalViewDeskripsi').html(d.deskripsi ? d.deskripsi.replace(/\n/g, '<br>') : 'Tidak ada ringkasan deskripsi capaian pembelajaran.');
            
            // Handle Skema Nilai & Status Penilaian Mahasiswa
            var jmlTotal = d.jumlah_siswa || 0;
            var jmlSudah = d.sudah_nilai || 0;
            var jmlBelum = d.belum_nilai || 0;

            $('#modalStatTotalMhs').text(jmlTotal + ' Mhs');
            $('#modalStatSudahNilai').text(jmlSudah + ' Mhs');
            $('#modalStatBelumNilai').text(jmlBelum + ' Mhs');

            if (d.is_skema == 1) {
                $('#modalSkemaStatusLabel').html('<span style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:2px 7px; border-radius:4px; font-size:10.5px; font-weight:700;"><i class="fa fa-check"></i> Skema 100% Aktif</span>');
                $('#modalViewSkemaContent').html(d.skema_html || '<span style="color:#64748b;">Skema penilaian aktif.</span>');

                if (jmlBelum == 0 && jmlTotal > 0) {
                    $('#modalStatBelumBox').css('border-color', '#bbf7d0');
                    $('#modalStatBelumIcon').css({'background': '#dcfce7', 'color': '#16a34a'}).html('<i class="fa fa-check"></i>');
                    $('#modalStatBelumLabel').css('color', '#166534').text('Status Kelulusan');
                    $('#modalStatBelumNilai').css('color', '#15803d').text('Tuntas 100%');
                } else {
                    $('#modalStatBelumBox').css('border-color', '#fecaca');
                    $('#modalStatBelumIcon').css({'background': '#fee2e2', 'color': '#dc2626'}).html('<i class="fa fa-clock-o"></i>');
                    $('#modalStatBelumLabel').css('color', '#991b1b').text('Belum Dinilai');
                    $('#modalStatBelumNilai').css('color', '#b91c1c').text(jmlBelum + ' Mhs');
                }

                $('#btnDocPdfNilai').attr('href', d.url_pdf_nilai).css('pointer-events', 'auto').css('opacity', '1');
                $('#btnDocPdfNilaiLabel').html('<i class="fa fa-file-pdf-o"></i> Cetak PDF Nilai').css('background-color', '#dc2626').css('border-color', '#b91c1c');
            } else {
                $('#modalSkemaStatusLabel').html('<span style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:2px 7px; border-radius:4px; font-size:10.5px; font-weight:700;"><i class="fa fa-exclamation-triangle"></i> Skema Belum Diatur</span>');
                $('#modalViewSkemaContent').html('<span style="color:#92400e; font-size:11.5px;"><i class="fa fa-info-circle"></i> Skema proporsi bobot evaluasi belum diisi. Silakan klik tombol <strong>Atur Skema</strong> pada tabel jadwal untuk mengaktifkan penilaian.</span>');

                $('#modalStatBelumBox').css('border-color', '#fde68a');
                $('#modalStatBelumIcon').css({'background': '#fef3c7', 'color': '#d97706'}).html('<i class="fa fa-exclamation"></i>');
                $('#modalStatBelumLabel').css('color', '#92400e').text('Perlu Skema');
                $('#modalStatBelumNilai').css('color', '#b45309').text(jmlTotal + ' Mhs');

                $('#btnDocPdfNilai').attr('href', '#').css('pointer-events', 'none').css('opacity', '0.6');
                $('#btnDocPdfNilaiLabel').html('<i class="fa fa-lock"></i> Skema Belum Diatur').css('background-color', '#94a3b8').css('border-color', '#64748b');
            }

            $('#btnDocRpsFull').attr('href', d.url_rps);
            $('#btnDocKontrak').attr('href', d.url_kontrak);
            $('#btnDocTugas').attr('href', d.url_tugas);
            $('#btnDocSkenario').attr('href', d.url_skenario || '#');
            $('#btnDocRoadmapPreview').attr('href', d.url_roadmap_preview || d.url_rps);
            $('#btnDocExcel').attr('href', d.url_excel);
            $('#btnModalStudioRps').attr('href', d.url_studio);

            $('#modalRpsView').modal('show');
        } catch(e) {
            console.error('Error opening RPS view modal:', e);
        }
    }

    function handleResetRpsJadwalDirect(idJadwal) {
        if (!idJadwal) {
            if (typeof swal !== 'undefined') {
                swal({ type: 'error', title: 'Error', text: 'ID Jadwal tidak valid.' });
            } else {
                alert('ID Jadwal tidak valid.');
            }
            return;
        }

        // Tampilkan loading saat cek status
        if (typeof swal !== 'undefined') {
            swal({
                title: 'Memeriksa Data Perkuliahan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                onOpen: function() {
                    swal.showLoading();
                }
            });
        }

        $.ajax({
            url: "<?php echo site_url('C_jadwal/ajax_check_reset_rps/') ?>" + idJadwal,
            type: "GET",
            dataType: "JSON",
            success: function(resp) {
                if (!resp.status) {
                    if (typeof swal !== 'undefined') {
                        swal({
                            type: 'error',
                            title: 'Tidak Dapat Direset',
                            text: resp.message || 'Gagal memeriksa status jadwal.'
                        });
                    } else {
                        alert(resp.message || 'Gagal memeriksa status jadwal.');
                    }
                    return;
                }

                if (resp.has_realisasi) {
                    // KONDISI ADA REALISASI: Tampilkan dialog proteksi data bertingkat
                    var listHtml = '<div style="text-align:left; max-height:130px; overflow-y:auto; background:#fef2f2; border:1px solid #fca5a5; border-radius:6px; padding:8px 12px; margin:10px 0; font-size:12px; color:#991b1b;">';
                    listHtml += '<strong>Pertemuan yang sudah terealisasi:</strong><ul style="margin:4px 0 0 16px; padding:0;">';
                    resp.realized_meets.forEach(function(m) {
                        listHtml += '<li>' + m + '</li>';
                    });
                    listHtml += '</ul></div>';

                    if (typeof swal !== 'undefined') {
                        swal({
                            title: '⚠️ Ada Pertemuan Berjalan!',
                            html: '<p style="font-size:13px; color:#334155;">Jadwal <strong>' + resp.tag_name + '</strong> telah memiliki <strong>' + resp.total_realisasi + ' pertemuan yang terlaksana (BAP/Presensi terisi)</strong>.</p>' +
                                  listHtml +
                                  '<p style="font-size:12.5px; color:#475569; margin-top:8px;">Klik <strong>Bersihkan Sisa Rencana</strong> untuk membersihkan pertemuan yang belum terlaksana tanpa menghapus BAP yang sudah ada.</p>',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa fa-shield"></i> Bersihkan Sisa Rencana Saja (Aman)',
                            confirmButtonColor: '#2563eb',
                            cancelButtonText: 'Batal',
                            cancelButtonColor: '#64748b'
                        }).then(function(result) {
                            if (result && result.value) {
                                executeResetRps(idJadwal, 'unrealized_only');
                            }
                        });
                    } else {
                        if (confirm('Bersihkan rencana pertemuan yang belum terlaksana?')) {
                            executeResetRps(idJadwal, 'unrealized_only');
                        }
                    }
                } else {
                    // KONDISI BELUM ADA REALISASI (0 Realisasi): Konfirmasi standar
                    if (typeof swal !== 'undefined') {
                        swal({
                            title: 'Reset Rencana Jadwal Ini?',
                            html: '<p style="font-size:13px; color:#334155;">Sebanyak <strong>' + resp.total_rencana + ' rencana pertemuan</strong> pada <strong>' + resp.tag_name + '</strong> akan dibersihkan.</p>' +
                                  '<p style="font-size:12px; color:#64748b;">Status jadwal akan kembali bersih sehingga Anda dapat menyinkronkan ulang rencana pertemuan dari Studio RPS kapan saja.</p>',
                            type: 'question',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa fa-trash-o"></i> Ya, Reset Rencana Jadwal',
                            confirmButtonColor: '#dc2626',
                            cancelButtonText: 'Batal',
                            cancelButtonColor: '#64748b'
                        }).then(function(result) {
                            if (result && result.value) {
                                executeResetRps(idJadwal, 'full_reset');
                            }
                        });
                    } else {
                        if (confirm('Reset ' + resp.total_rencana + ' rencana pertemuan pada ' + resp.tag_name + '?')) {
                            executeResetRps(idJadwal, 'full_reset');
                        }
                    }
                }
            },
            error: function() {
                if (typeof swal !== 'undefined') {
                    swal({
                        type: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan koneksi server.'
                    });
                } else {
                    alert('Terjadi kesalahan koneksi server.');
                }
            }
        });
    }

    function handleResetRpsJadwal() {
        if (currentViewingJadwalId) {
            handleResetRpsJadwalDirect(currentViewingJadwalId);
        }
    }

    function executeResetRps(idJadwal, resetMode) {
        if (typeof swal !== 'undefined') {
            swal({
                title: 'Mereset Jadwal...',
                text: 'Sedang membersihkan data rencana pertemuan',
                allowOutsideClick: false,
                onOpen: function() {
                    swal.showLoading();
                }
            });
        }

        var postData = {
            id_jadwal: idJadwal,
            reset_mode: resetMode
        };
        postData['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url: "<?php echo site_url('C_jadwal/ajax_execute_reset_rps') ?>",
            type: "POST",
            data: postData,
            dataType: "JSON",
            success: function(res) {
                if (res.status) {
                    $('#modalRpsView').modal('hide');
                    reload_table_ajax();
                    if (typeof swal !== 'undefined') {
                        swal({
                            type: 'success',
                            title: 'Berhasil Direset!',
                            text: res.message,
                            timer: 3500,
                            showConfirmButton: true
                        });
                    } else {
                        alert(res.message);
                    }
                } else {
                    if (typeof swal !== 'undefined') {
                        swal({
                            type: 'error',
                            title: 'Gagal Mereset',
                            text: res.message || 'Terjadi kesalahan saat mereset data.'
                        });
                    } else {
                        alert(res.message || 'Terjadi kesalahan saat mereset data.');
                    }
                }
            },
            error: function() {
                if (typeof swal !== 'undefined') {
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'Gagal mengirim permintaan ke server.'
                    });
                } else {
                    alert('Gagal mengirim permintaan ke server.');
                }
            }
        });
    }
</script>

<!-- MODAL DOKUMEN JADWAL (HUB DOKUMEN PEMBELAJARAN & NILAI) -->
<div class="modal fade" id="modalRpsView" tabindex="-1" role="dialog" aria-labelledby="modalRpsViewTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 860px; margin: 30px auto;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);">
      
      <!-- Modal Header (WBI GREEN) -->
      <div class="modal-header" style="background: #1b4332; color: #ffffff; padding: 16px 22px; border-bottom: 1px solid #143527;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8; font-size: 22px; text-shadow: none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalRpsViewTitle" style="font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; color: #ffffff; margin: 0;">
          <i class="fa fa-folder-open-o" style="color: #6ee7b7;"></i> Dokumen Jadwal Perkuliahan
        </h4>
        <p style="margin: 3px 0 0 0; font-size: 12px; color: #a7f3d0;">
          Standar Outcome-Based Education (SN-Dikti 2026 &amp; SPMI WBI)
        </p>
      </div>

      <!-- Modal Body -->
      <div class="modal-body" style="padding: 22px; background: #f8fafc;">
        
        <!-- Course Info Box -->
        <div style="background: #ffffff; border-radius: 6px; padding: 16px 18px; border: 1px solid #e2e8f0; margin-bottom: 18px;">
          <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
              <span id="modalViewKodeMk" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 12px; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe;">[MK]</span>
              <h4 id="modalViewNmMk" style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.3;">Nama Mata Kuliah</h4>
            </div>
            <div style="font-size: 12px; color: #475569; display: flex; gap: 8px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
              <span id="modalViewSks"><i class="fa fa-graduation-cap" style="color:#2563eb;"></i> <strong>3 SKS</strong></span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewKelas"><i class="fa fa-users" style="color:#4f46e5;"></i> Kelas -</span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewProdi" style="background:#ecfdf5; color:#065f46; font-size:11px; font-weight:600; padding:1px 6px; border-radius:4px; border:1px solid #a7f3d0;"><i class="fa fa-university"></i> Prodi</span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewSemester"><i class="fa fa-clone" style="color:#475569;"></i> Semester -</span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewStatusBadge"></span>
            </div>
          </div>

          <!-- Deskripsi / CPMK -->
          <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
            <div style="font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
              <i class="fa fa-bullseye"></i> Deskripsi &amp; Sasaran Pembelajaran (CPMK)
            </div>
            <div id="modalViewDeskripsi" style="font-size: 12px; color: #1e293b; line-height: 1.5; max-height: 100px; overflow-y: auto; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border: 1px solid #e2e8f0;">
              Deskripsi mata kuliah...
            </div>
          </div>

          <!-- Section Skema & Penilaian Mahasiswa -->
          <div id="modalViewSkemaBox" style="margin-top: 14px; padding-top: 14px; border-top: 1px solid #f1f5f9;">
            <div style="font-size: 11.5px; font-weight: 700; color: #334155; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
              <span><i class="fa fa-calculator text-primary" style="margin-right: 4px;"></i> Progres Penilaian &amp; Skema Bobot</span>
              <span id="modalSkemaStatusLabel"></span>
            </div>

            <!-- 3 Communicative Stat Cards for Student Assessment Progress -->
            <div class="row" style="margin-left: -5px; margin-right: -5px; margin-bottom: 10px;">
              <!-- Card 1: Total Mahasiswa -->
              <div class="col-xs-4" style="padding-left: 5px; padding-right: 5px;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; gap: 8px;">
                  <span style="background: #e0f2fe; color: #0284c7; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fa fa-users"></i>
                  </span>
                  <div>
                    <div style="font-size: 10px; color: #64748b; font-weight: 600;">Total Mahasiswa</div>
                    <div id="modalStatTotalMhs" style="font-size: 13.5px; font-weight: 800; color: #0f172a;">0 Mhs</div>
                  </div>
                </div>
              </div>

              <!-- Card 2: Sudah Dinilai -->
              <div class="col-xs-4" style="padding-left: 5px; padding-right: 5px;">
                <div style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; gap: 8px;">
                  <span style="background: #dcfce7; color: #16a34a; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fa fa-check-circle"></i>
                  </span>
                  <div>
                    <div style="font-size: 10px; color: #166534; font-weight: 600;">Sudah Dinilai</div>
                    <div id="modalStatSudahNilai" style="font-size: 13.5px; font-weight: 800; color: #15803d;">0 Mhs</div>
                  </div>
                </div>
              </div>

              <!-- Card 3: Belum Dinilai -->
              <div class="col-xs-4" style="padding-left: 5px; padding-right: 5px;">
                <div id="modalStatBelumBox" style="background: #ffffff; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; gap: 8px;">
                  <span id="modalStatBelumIcon" style="background: #fee2e2; color: #dc2626; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fa fa-clock-o"></i>
                  </span>
                  <div>
                    <div id="modalStatBelumLabel" style="font-size: 10px; color: #991b1b; font-weight: 600;">Belum Dinilai</div>
                    <div id="modalStatBelumNilai" style="font-size: 13.5px; font-weight: 800; color: #b91c1c;">0 Mhs</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Skema Bobot Nilai Box -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 8px 12px;">
              <div style="font-size: 10.5px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                <i class="fa fa-sliders" style="color: #64748b; margin-right: 3px;"></i> Komponen &amp; Proporsi Bobot:
              </div>
              <div id="modalViewSkemaContent" style="line-height: 1.6;">
              </div>
            </div>
          </div>
        </div>

        <!-- Section Title -->
        <div style="font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
          <i class="fa fa-folder-open-o text-primary"></i> Pilih Dokumen Pembelajaran &amp; Nilai untuk Dibuka / Diunduh:
        </div>

        <!-- Document Grid (6 Cards) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
          
          <!-- Doc 1: RPS Full -->
          <a id="btnDocRpsFull" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #bae6fd; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-pdf-o"></i>
                  </span>
                  <div>
                    <strong style="color: #0369a1; font-size: 13px; display: block;">RPS Full (Lengkap)</strong>
                    <span style="font-size: 10.5px; color: #64748b;">Standar SN-Dikti 2026</span>
                  </div>
                </div>
                <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Dokumen RPS lengkap: identitas, CPL, CPMK, matriks 16 minggu, evaluasi &amp; pustaka.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #0284c7; color: #ffffff !important; padding: 6px 10px; border: 1px solid #0369a1;">
                <i class="fa fa-external-link"></i> Buka RPS Full
              </span>
            </div>
          </a>

          <!-- Doc 2: Kontrak Perkuliahan -->
          <a id="btnDocKontrak" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #e0e7ff; color: #4338ca; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-text-o"></i>
                  </span>
                  <div>
                    <strong style="color: #3730a3; font-size: 13px; display: block;">Kontrak Kuliah</strong>
                    <span style="font-size: 10.5px; color: #64748b;">Standar SPMI WBI</span>
                  </div>
                </div>
                <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Pedoman hak &amp; kewajiban mahasiswa, tata tertib, jadwal, dan skema evaluasi nilai.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #4338ca; color: #ffffff !important; padding: 6px 10px; border: 1px solid #3730a3;">
                <i class="fa fa-external-link"></i> Buka Kontrak
              </span>
            </div>
          </a>

          <!-- Doc 3: Rancangan Tugas -->
          <a id="btnDocTugas" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #99f6e4; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #ccfbf1; color: #0f766e; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-tasks"></i>
                  </span>
                  <div>
                    <strong style="color: #0f766e; font-size: 13px; display: block;">Rancangan Tugas</strong>
                    <span style="font-size: 10.5px; color: #64748b;">SCL / PjBL / Kasus</span>
                  </div>
                </div>
                <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Instruksi tugas mandiri &amp; kelompok, batasan pengerjaan, dan rubrik penilaian.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #0f766e; color: #ffffff !important; padding: 6px 10px; border: 1px solid #115e59;">
                <i class="fa fa-external-link"></i> Buka Rancangan Tugas
              </span>
            </div>
          </a>

          <!-- Doc 5: Roadmap Belajar -->
          <a id="btnDocRoadmapPreview" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #fde68a; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #fef3c7; color: #b45309; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-sitemap"></i>
                  </span>
                  <div>
                    <strong style="color: #92400e; font-size: 13px; display: block;">Roadmap Belajar</strong>
                    <span style="font-size: 10.5px; color: #64748b;">Peta Jalan 16 Minggu</span>
                  </div>
                </div>
                <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Peta jalan modul, durasi waktu TM/Praktek, serta alur pencapaian sub-CPMK.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #b45309; color: #ffffff !important; padding: 6px 10px; border: 1px solid #92400e;">
                <i class="fa fa-external-link"></i> Buka Roadmap
              </span>
            </div>
          </a>

          <!-- Doc 5: Ekspor Excel (13 Kolom) -->
          <a id="btnDocExcel" href="#" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #dcfce7; color: #15803d; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-excel-o"></i>
                  </span>
                  <div>
                    <strong style="color: #166534; font-size: 13px; display: block;">Matriks Excel 13 Kolom</strong>
                    <span style="font-size: 10.5px; color: #64748b;">Format Arsip SPMI</span>
                  </div>
                </div>
                <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Ekspor data matriks 13 kolom RPS lengkap ke file spreadsheet Excel (.xlsx).
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #15803d; color: #ffffff !important; padding: 6px 10px; border: 1px solid #166534;">
                <i class="fa fa-download"></i> Unduh Excel RPS
              </span>
            </div>
          </a>

          <!-- Doc 6: Rekap Nilai Mahasiswa PDF -->
          <div id="wrapperDocPdfNilai" style="display: flex;">
            <a id="btnDocPdfNilai" href="#" target="_blank" style="text-decoration: none; display: flex; width: 100%;">
              <div style="background: #ffffff; border: 1px solid #fecaca; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                    <span style="background: #fee2e2; color: #dc2626; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                      <i class="fa fa-file-pdf-o"></i>
                    </span>
                    <div>
                      <strong style="color: #991b1b; font-size: 13px; display: block;">Rekap Nilai (PDF)</strong>
                      <span style="font-size: 10.5px; color: #64748b;">Daftar Nilai Akhir</span>
                    </div>
                  </div>
                  <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                    Cetak berkas PDF rekapitulasi nilai mahasiswa, komponen evaluasi, dan nilai mutu.
                  </p>
                </div>
                <span id="btnDocPdfNilaiLabel" style="display: block; text-align: center; font-weight: 600; font-size: 11.5px; border-radius: 4px; background-color: #dc2626; color: #ffffff !important; padding: 6px 10px; border: 1px solid #b91c1c;">
                  <i class="fa fa-download"></i> Cetak PDF Nilai
                </span>
              </div>
            </a>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
          <a id="btnModalStudioRps" href="#" target="_blank" class="btn btn-default" style="font-weight: 600; font-size: 12.5px; color: #0f766e; border: 1px solid #99f6e4; background: #f0fdfa;">
            <i class="fa fa-paint-brush"></i> Buka di Studio RPS
          </a>
        </div>
        <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600; font-size: 12.5px; padding: 6px 20px;">
          Tutup
        </button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modal_form_skema_nilai" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1180px; margin: 25px auto;">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 20px 45px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); color: #ffffff; padding: 18px 24px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; font-size: 24px; text-shadow: none;"><span aria-hidden="true">&times;</span></button>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding-right:20px;">
                    <div>
                        <h4 class="modal-title" id="skema_modal_title" style="font-weight: 700; color: #ffffff; margin: 0; font-size: 16.5px; letter-spacing: -0.2px;">
                            <i class="fa fa-sliders" style="color:#38bdf8; margin-right:8px;"></i> Studio Pemetaan Skema Nilai OBE ke SIAKAD
                        </h4>
                        <div id="skema_modal_subtitle" style="font-size: 12px; color: #94a3b8; margin-top: 3px;">
                            Matriks Asesmen CPMK/Sub-CPMK RPS OBE &rarr; Komponen Evaluasi SIAKAD Politeknik WBI
                        </div>
                    </div>
                    <div id="obe_status_tag" style="display:flex; align-items:center; gap:6px;">
                        <!-- dynamic badge -->
                    </div>
                </div>
            </div>

            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form_skema_nilai', 'style' => 'margin: 0;'];
            echo form_open('#', $attributes);
            ?>
            <div class="modal-body" style="background: #f1f5f9 !important; padding: 20px 24px !important; max-height: calc(100vh - 210px); overflow-y: auto;">
                <input type="hidden" value="" name="id" />
                <input type="hidden" name="tag_name" />

                <!-- Panduan & Quick Action Bar -->
                <div style="background: #ffffff; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 12px 18px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="font-size: 12.5px; color: #334155;">
                        <i class="fa fa-info-circle" style="color: #3b82f6; font-size: 15px; margin-right: 6px;"></i>
                        <strong>Panduan Pemetaan:</strong> Seret (<em>drag & drop</em>) kartu komponen OBE di sebelah kiri ke slot SIAKAD di kanan, atau gunakan tombol <strong>"⚡ Terapkan Bobot OBE"</strong> untuk pemetaan instan (Total wajib <strong>100%</strong>).
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-xs btn-primary" onclick="autoMapObeToSiakad()" style="border-radius:6px; font-weight:700; padding:6px 12px; background:#2563eb; border-color:#1d4ed8; box-shadow:0 2px 4px rgba(37,99,235,0.2);">
                            <i class="fa fa-bolt"></i> ⚡ Terapkan Bobot OBE (100%)
                        </button>
                        <button type="button" class="btn btn-xs btn-default" onclick="resetSiakadSlots()" style="border-radius:6px; font-weight:600; padding:6px 12px; color:#64748b;">
                            <i class="fa fa-refresh"></i> Reset (0%)
                        </button>
                    </div>
                </div>

                <div class="row" style="margin-left: -8px !important; margin-right: -8px !important;">
                    
                    <!-- PANEL KIRI (col-md-5): SUMBER MATRIKS ASESMEN OBE -->
                    <div class="col-md-5 col-xs-12" style="padding-left: 8px; padding-right: 8px; margin-bottom: 16px;">
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); height: 100%; display: flex; flex-direction: column;">
                            
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; display:flex; align-items:center; gap:6px;">
                                        <i class="fa fa-th-large" style="color:#6366f1;"></i> Kartu Komponen RPS OBE
                                    </div>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                        Sumber: Matriks Asesmen CPMK/Sub-CPMK
                                    </div>
                                </div>
                                <span id="obe_blueprint_total_badge" class="badge" style="background:#4f46e5; font-size:11.5px; padding:4px 9px;">Total 100%</span>
                            </div>

                            <div style="font-size: 11.5px; color: #64748b; margin-bottom: 10px; line-height: 1.4;">
                                <i class="fa fa-hand-pointer-o" style="color:#6366f1;"></i> <em>Seret kartu di bawah ini ke kotak SIAKAD tujuan di sebelah kanan:</em>
                            </div>

                            <!-- List Kartu Draggable -->
                            <div id="obe_cards_container" style="display:flex; flex-direction:column; gap:8px; margin-bottom: 14px; min-height: 180px;">
                                <!-- Rendered dynamically by JS -->
                            </div>

                            <!-- Collapsible Preview Tabel Blueprint Lengkap -->
                            <div style="margin-top:auto; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                                <button type="button" class="btn btn-default btn-xs btn-block" data-toggle="collapse" data-target="#collapseBlueprintTable" style="border-radius:6px; font-weight:600; color:#475569; background:#f8fafc; padding:6px 10px; text-align:left; display:flex; justify-content:space-between; align-items:center;">
                                    <span><i class="fa fa-table" style="color:#0ea5e9; margin-right:5px;"></i> Preview Matriks Asesmen Lengkap</span>
                                    <i class="fa fa-chevron-down" style="font-size:10px;"></i>
                                </button>
                                <div class="collapse" id="collapseBlueprintTable" style="margin-top:8px;">
                                    <div id="obe_blueprint_table_wrap" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:8px; font-size:11px; max-height:220px; overflow-y:auto; overflow-x:auto;">
                                        <!-- Table HTML rendered here -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- PANEL KANAN (col-md-7): 10 TARGET SLOT PENILAIAN SIAKAD -->
                    <div class="col-md-7 col-xs-12" style="padding-left: 8px; padding-right: 8px; margin-bottom: 16px;">
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                            
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; display:flex; align-items:center; gap:6px;">
                                        <i class="fa fa-check-square-o" style="color:#059669;"></i> Slot Komponen Penilaian SIAKAD
                                    </div>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                        Drop kartu ke slot terkait atau ketik angka persentase secara manual
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <span style="font-size:11px; font-weight:600; color:#64748b;">Target Total: </span>
                                    <span style="font-size:12px; font-weight:700; color:#0f172a;">100%</span>
                                </div>
                            </div>

                            <!-- Group 1: Evaluasi Teoretis & Kognitif (5 Komponen) -->
                            <div style="margin-bottom: 14px;">
                                <div style="font-size: 11.5px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display:flex; align-items:center; gap:6px;">
                                    <i class="fa fa-book text-primary"></i> 1. Evaluasi Formatif, Sikap &amp; Kognitif
                                </div>
                                
                                <div class="row" style="margin-left:-4px !important; margin-right:-4px !important;">
                                    <!-- Sikap -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_sikap" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'sikap')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-heartbeat text-danger"></i> Sikap / Partisipasi</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('sikap')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="sikap" id="slot_input_sikap" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quiz -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_quiz" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'quiz')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-question-circle text-warning"></i> Quiz / Kuis</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('quiz')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="quiz" id="slot_input_quiz" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tugas Terstruktur -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_tugas_terstruktur" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'tugas_terstruktur')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-tasks text-info"></i> Tugas Terstruktur</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('tugas_terstruktur')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="tugas_terstruktur" id="slot_input_tugas_terstruktur" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UTS -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_uts" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'uts')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-file-text-o text-primary"></i> UTS</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('uts')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="uts" id="slot_input_uts" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- UAS -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_uas" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'uas')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-graduation-cap text-danger"></i> UAS</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('uas')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="uas" id="slot_input_uas" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Group 2: Evaluasi Kinerja, Proyek & Portofolio SCL (5 Komponen) -->
                            <div>
                                <div style="font-size: 11.5px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display:flex; align-items:center; gap:6px;">
                                    <i class="fa fa-cubes text-success"></i> 2. Evaluasi Kinerja, Proyek &amp; Luaran (SCL)
                                </div>
                                
                                <div class="row" style="margin-left:-4px !important; margin-right:-4px !important;">
                                    <!-- Proyek -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_proyek" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'proyek')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-briefcase text-success"></i> Proyek / Case Method</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('proyek')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="proyek" id="slot_input_proyek" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Unjuk Kinerja -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_unjuk_kinerja" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'unjuk_kinerja')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-cogs" style="color:#0284c7;"></i> Unjuk Kinerja / Praktik</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('unjuk_kinerja')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="unjuk_kinerja" id="slot_input_unjuk_kinerja" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Portofolio -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_portofolio" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'portofolio')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-folder-open-o" style="color:#8b5cf6;"></i> Portofolio</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('portofolio')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="portofolio" id="slot_input_portofolio" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Produk -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_produk" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'produk')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-archive" style="color:#d97706;"></i> Produk / Luaran</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('produk')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="produk" id="slot_input_produk" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Presentasi -->
                                    <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                                        <div class="siakad-slot-card" id="slot_card_presentasi" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'presentasi')">
                                            <div class="slot-header">
                                                <span class="slot-title"><i class="fa fa-television" style="color:#ec4899;"></i> Presentasi</span>
                                                <button type="button" class="btn-slot-clear" onclick="clearSlot('presentasi')" title="Kosongkan"><i class="fa fa-times"></i></button>
                                            </div>
                                            <div class="slot-input-wrap">
                                                <div class="input-group input-group-sm">
                                                    <input name="presentasi" id="slot_input_presentasi" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                                                    <span class="input-group-addon font-bold">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- PROGRESS BAR & VALIDATION NOTIFICATION -->
                <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; margin-top:4px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <span style="font-size:12px; font-weight:700; color:#334155;">
                            <i class="fa fa-bar-chart" style="color:#4f46e5;"></i> Akumulasi Total Bobot Penilaian
                        </span>
                        <span id="skema_total_text" style="font-size:14px; font-weight:800; color:#0f172a;">0%</span>
                    </div>
                    <div class="progress" style="height: 10px; margin-bottom: 10px; border-radius: 5px; background: #e2e8f0; overflow: hidden;">
                        <div id="skema_progress_bar" class="progress-bar" role="progressbar" style="width: 0%; background-color: #f59e0b; transition: width 0.3s ease, background-color 0.3s ease;"></div>
                    </div>
                    <div class="alert text-center" id="jumlah_total" style="border-radius: 8px; margin-bottom: 0; padding: 10px 14px; font-size: 12.5px;"></div>
                </div>

            </div>
            <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px 28px; text-align: right; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:11.5px; color:#64748b; text-align:left;">
                    <i class="fa fa-shield text-success"></i> Validasi Sistem: Tombol simpan hanya aktif saat total tepat 100%.
                </div>
                <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600; padding: 8px 20px; margin-right: 8px; color: #475569;">Batal</button>
                    <button type="submit" id="btnSkema" class="btn btn-primary" style="background-color: #1e293b !important; border: 1px solid #0f172a !important; color: #ffffff !important; border-radius: 6px; font-weight: 700; padding: 8px 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                        <i class="fa fa-check"></i> Simpan Skema Nilai
                    </button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<style>
    /* Fix background scroll when modal is open */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }
    
    #modal_krs_notification .scroller {
        padding-right: 10px !important;
    }

    #modal_form_skema_nilai .modal-body {
        padding: 20px 24px !important;
    }
    #modal_form_skema_nilai .row {
        margin-left: -8px !important;
        margin-right: -8px !important;
    }
    #modal_form_skema_nilai .form-group {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    /* Drag and Drop Studio Styles */
    .obe-drag-card {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 12px;
        cursor: grab;
        transition: all 0.2s ease;
        user-select: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .obe-drag-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99,102,241,0.18);
        transform: translateY(-2px);
    }
    .obe-drag-card:active {
        cursor: grabbing;
    }
    .obe-drag-card.dragging {
        opacity: 0.45;
        border-style: dashed;
        border-color: #6366f1;
    }

    .siakad-slot-card {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        padding: 8px 10px;
        transition: all 0.2s ease;
        position: relative;
    }
    .siakad-slot-card.has-value {
        background: #ffffff;
        border: 1.5px solid #94a3b8;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .siakad-slot-card.slot-drag-hover {
        background: #eff6ff !important;
        border: 2px dashed #2563eb !important;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(37,99,235,0.2);
    }
    .siakad-slot-card.slot-pulse {
        animation: slotPulseAnim 0.6s ease;
    }
    @keyframes slotPulseAnim {
        0% { transform: scale(1); background: #dcfce7; }
        50% { transform: scale(1.04); background: #bbf7d0; border-color: #16a34a; }
        100% { transform: scale(1); }
    }

    .siakad-slot-card .slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }
    .siakad-slot-card .slot-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .siakad-slot-card .btn-slot-clear {
        background: none;
        border: none;
        color: #94a3b8;
        padding: 0 4px;
        font-size: 11px;
        cursor: pointer;
        line-height: 1;
    }
    .siakad-slot-card .btn-slot-clear:hover {
        color: #ef4444;
    }
    .siakad-slot-card input.form-control {
        font-weight: 700;
        color: #0f172a;
        font-size: 13px;
        border-radius: 4px 0 0 4px !important;
    }
    .siakad-slot-card .input-group-addon {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        border-radius: 0 4px 4px 0 !important;
    }
</style>

<div class="modal fade" id="modal_krs_notification" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue-hoki font-white">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="color:white !important"><i class="fa fa-info-circle"></i> Dashboard Monitoring KRS Mahasiswa</h3>
            </div>
            <div class="modal-body" style="background: #f4f6f9;">
                
                <div class="row margin-bottom-20">
                    <div class="col-md-4">
                        <div class="dashboard-stat blue-madison">
                            <div class="visual"><i class="fa fa-check-square-o"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['need_approval']); ?></div>
                                <div class="desc">Butuh Approval</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-stat yellow-gold">
                            <div class="visual"><i class="fa fa-pencil"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['draft']); ?></div>
                                <div class="desc">Sedang Daftar</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-stat red-intense">
                            <div class="visual"><i class="fa fa-times-circle"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['belum_krs']); ?></div>
                                <div class="desc">Belum KRS</div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab_approval" data-toggle="tab">Butuh Approval <span class="badge badge-danger"><?= count($krs_stats['need_approval']); ?></span></a>
                        </li>
                        <li>
                            <a href="#tab_draft" data-toggle="tab">Sedang Daftar <span class="badge badge-warning"><?= count($krs_stats['draft']); ?></span></a>
                        </li>
                        <li>
                            <a href="#tab_belum" data-toggle="tab">Belum KRS <span class="badge badge-default"><?= count($krs_stats['belum_krs']); ?></span></a>
                        </li>
                    </ul>
                    <div class="tab-content" style="background: white; padding: 15px; border: 1px solid #ddd; border-top: none;">
                        
                        <div class="tab-pane active" id="tab_approval">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['need_approval'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Tidak ada mahasiswa yang butuh approval.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['need_approval'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tab_draft">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['draft'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Tidak ada mahasiswa yang sedang daftar.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['draft'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tab_belum">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['belum_krs'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Semua mahasiswa sudah KRS.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['belum_krs'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= site_url('Lecturer_persetujuan_krs'); ?>" class="btn btn-primary btn-block">Kelola Approval KRS</a>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>