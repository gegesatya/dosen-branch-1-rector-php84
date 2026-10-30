<!-- Toastr & SweetAlert2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap DateRangePicker & TimePicker CSS -->
<link rel="stylesheet" href="<?= base_url('assets/theme/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/theme/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') ?>">
<!-- Bootstrap DateRangePicker & TimePicker JS (moment.js already loaded globally) -->
<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') ?>"></script>
<script type="text/javascript">
function showPremiumToast(type, title, message) {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000",
    };
    toastr[type](message, title);
}

$(document).ready(function() {
    <?php if (!isset($is_lulus) || !$is_lulus): ?>
    // AJAX Cek Tunggakan (Sokaru) - Hanya untuk mahasiswa aktif
    var nipd = '<?= isset($data_skripsi->nipd) ? $data_skripsi->nipd : "" ?>';
    if (nipd !== "") {
        // Tampilkan loading & disable form sementara
        $('#alert_loading_tunggakan').css('display', 'flex');
        
        var btnAgendaHtml = $('#btn_buat_agenda_utama').html();
        
        // Disable interaksi sementara loading
        $('#btn_buat_agenda_utama').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
        $('#wrapper_btn_buat_agenda').find('button').prop('disabled', true);
        $('#form_balas_pesan textarea').prop('disabled', true);
        $('#form_balas_pesan select').prop('disabled', true);
        $('#form_balas_pesan input').prop('disabled', true);
        $('#btnSave').prop('disabled', true);
        $('#chatInputMsg').prop('disabled', true);
        $('#btnSendChat').prop('disabled', true);

        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_cek_tunggakan_mhs/') ?>" + nipd,
            type: "GET",
            dataType: "JSON",
            success: function(res) {
                $('#alert_loading_tunggakan').hide();
                if (res.status && res.is_menunggak == 1) {
                    $('#alert_tunggakan_container').css('display', 'flex');
                    
                    $('#btn_buat_agenda_utama').css({
                        'background': '#D1D5DB',
                        'color': '#6B7280',
                        'opacity': '1',
                        'box-shadow': 'none'
                    }).html('<i class="fa fa-lock" style="font-size: 18px;"></i> <span>Buat Agenda (Dikunci)</span>');
                    
                    $('#form_balas_pesan textarea').attr('placeholder', 'Dikunci karena mahasiswa menunggak');
                    $('#chatInputMsg').attr('placeholder', 'Interaksi dikunci...');
                    
                    showPremiumToast('warning', 'Peringatan Tunggakan', 'Mahasiswa memiliki tunggakan uang kuliah. Interaksi dinonaktifkan.');
                } else {
                    // Re-enable form (Bebas tunggakan)
                    $('#btn_buat_agenda_utama').prop('disabled', false).css({'opacity': '1', 'cursor': 'pointer'}).html(btnAgendaHtml);
                    $('#wrapper_btn_buat_agenda').find('button').prop('disabled', false);
                    $('#form_balas_pesan textarea').prop('disabled', false);
                    $('#form_balas_pesan select').prop('disabled', false);
                    $('#form_balas_pesan input').prop('disabled', false);
                    $('#btnSave').prop('disabled', false);
                    $('#chatInputMsg').prop('disabled', false);
                    $('#btnSendChat').prop('disabled', false);
                    
                    // Trigger select change to restore placeholder
                    $('select[name="status_bimbingan"]').trigger('change');
                }
            },
            error: function() {
                // Failsafe: if error, allow interaction to not block the flow
                $('#alert_loading_tunggakan').hide();
                $('#btn_buat_agenda_utama').prop('disabled', false).css({'opacity': '1', 'cursor': 'pointer'}).html(btnAgendaHtml);
                $('#wrapper_btn_buat_agenda').find('button').prop('disabled', false);
                $('#form_balas_pesan textarea').prop('disabled', false);
                $('#form_balas_pesan select').prop('disabled', false);
                $('#form_balas_pesan input').prop('disabled', false);
                $('#btnSave').prop('disabled', false);
                $('#chatInputMsg').prop('disabled', false);
                $('#btnSendChat').prop('disabled', false);
                $('select[name="status_bimbingan"]').trigger('change');
            }
        });
    }
    <?php else: ?>
    // Sembunyikan loading jika mahasiswa sudah lulus, langsung trigger enable
    $('#alert_loading_tunggakan').hide();
    <?php endif; ?>

    // Sembunyikan progress & ubah placeholder textarea saat status berubah
    $('select[name="status_bimbingan"]').change(function() {
        var val = $(this).val();
        var $ta = $('textarea[name="catatan_dosen_wali"]');
        var btnText = 'Kirim Review Bimbingan';

        if (val === 'Konsultasi ke Pendamping') {
            $('#progress_container').hide();
            $ta.attr('placeholder', 'Delegasi ke Dosen Pendamping — tuliskan alasan atau hal yang perlu ditindaklanjuti oleh pendamping...');
            btnText = 'Delegasi ke Pendamping';
        } else if (val === 'Lanjut ke Utama') {
            $('#progress_container').hide();
            $ta.attr('placeholder', 'Teruskan ke Pembimbing Utama — tuliskan ringkasan hasil konsultasi atau catatan untuk pembimbing utama...');
            btnText = 'Teruskan ke Utama';
        } else if (val === 'Tanya / Informasi') {
            $('#progress_container').hide();
            $ta.attr('placeholder', 'Tuliskan jawaban, informasi, atau klarifikasi untuk pertanyaan mahasiswa...');
            btnText = 'Kirim Balasan / Info';
        } else if (val === 'Revisi' || val === 'Perlu Revisi') {
            $('#progress_container').show();
            $ta.attr('placeholder', 'Tuliskan poin-poin revisi yang harus diperbaiki oleh mahasiswa...');
            btnText = 'Kirim Instruksi Revisi';
        } else if (val === 'Disetujui') {
            $('#progress_container').show();
            $ta.attr('placeholder', 'Tuliskan catatan persetujuan atau arahan lanjutan untuk mahasiswa...');
            btnText = 'Setujui & Kirim Catatan';
        } else {
            $('#progress_container').show();
            $ta.attr('placeholder', 'Ketik catatan bimbingan atau feedback untuk mahasiswa...');
        }

        $('#btnSave').html('<i class="fa fa-check-circle" style="margin-right:6px;"></i> ' + btnText);
        $('#btnSave').data('original-text', btnText);
    });

    // Trigger on page load to set the initial button text
    $('select[name="status_bimbingan"]').trigger('change');

    // Render markdown pada konten AI review yang sudah tersimpan di DB
    $('.ai-review-content').each(function() {
        var $reviewDiv = $(this).find('.ai-review-text');
        var raw = $(this).find('.ai-raw-text').val();
        if (raw && raw.trim().length > 0) {
            $reviewDiv.html(markdownToHtml(raw));
        }
    });

    // =============================================================
    // FORM BALAS PESAN (Dosen memberikan review/catatan bimbingan)
    // =============================================================
    $('#form_balas_pesan').submit(function(e) {
        e.preventDefault();

        // VALIDASI TEXTAREA
        var catatan = $('textarea[name="catatan_dosen_wali"]').val().trim();
        if (catatan.length === 0) {
            showPremiumToast('warning', 'Peringatan', 'Catatan bimbingan atau feedback wajib diisi.');
            $('textarea[name="catatan_dosen_wali"]').focus();
            return;
        }

        var wordCount = catatan.split(/\s+/).filter(function(word) {
            return word.length > 0;
        }).length;

        if (wordCount < 5) {
            showPremiumToast('warning', 'Peringatan', 'Catatan bimbingan terlalu singkat. Mohon ketik minimal 5 kata.');
            $('textarea[name="catatan_dosen_wali"]').focus();
            return;
        }

        var btnOriginalText = $('#btnSave').data('original-text') || 'Kirim Review Bimbingan';
        $('#btnSave').html('<i class="fa fa-spinner fa-spin" style="margin-right:6px;"></i> Menyimpan...');
        $('#btnSave').attr('disabled', true);

        var statusBimbingan = $('select[name="status_bimbingan"]').val();
        var progressPersen  = $('input[name="progress_persen"]').val();

        $.ajax({
            url: "<?php echo site_url('E_skripsi_bimbingan/ajax_balas_pesan') ?>",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: function(data) {
                if (data.status) {
                    $('#btnSave').html('<i class="fa fa-check-circle" style="margin-right:6px;"></i> Berhasil Disimpan');
                    // Biarkan button disabled karena halaman akan me-reload
                    
                    var toastType = 'info';
                    var toastTitle = 'Review Dikirim';
                    var toastMsg = 'Review bimbingan Anda berhasil disimpan.';

                    if (statusBimbingan === 'Disetujui') {
                        toastType = 'success';
                        toastTitle = 'Bimbingan Disetujui';
                        toastMsg = 'Bimbingan telah disetujui! Progress diset ke ' + progressPersen + '%.';
                    } else if (statusBimbingan === 'Perlu Revisi') {
                        toastType = 'warning';
                        toastTitle = 'Perlu Revisi';
                        toastMsg = 'Catatan revisi telah dikirim. Progress diset ke ' + progressPersen + '%.';
                    } else if (statusBimbingan === 'Tanya / Informasi') {
                        toastType = 'info';
                        toastTitle = 'Info / Tanya Dikirim';
                        toastMsg = 'Pertanyaan atau informasi berhasil dikirim ke mahasiswa.';
                    } else if (statusBimbingan === 'Menunggu Review') {
                        toastType = 'info';
                        toastTitle = 'Pending State';
                        toastMsg = 'Catatan bimbingan berhasil disimpan sebagai Menunggu Review / Pending State.';
                    } else if (statusBimbingan === 'Konsultasi ke Pendamping') {
                        toastType = 'info';
                        toastTitle = 'Dialihkan ke Pendamping';
                        toastMsg = 'Log bimbingan telah diteruskan ke Dosen Pendamping untuk di-review.';
                    } else if (statusBimbingan === 'Lanjut ke Utama') {
                        toastType = 'info';
                        toastTitle = 'Dikembalikan ke Utama';
                        toastMsg = 'Hasil review Anda diteruskan ke Dosen Utama.';
                    }

                    showPremiumToast(toastType, toastTitle, toastMsg);
                    setTimeout(function() { location.reload(); }, 1800);
                } else {
                    $('#btnSave').html('<i class="fa fa-check-circle" style="margin-right:6px;"></i> ' + btnOriginalText);
                    $('#btnSave').attr('disabled', false);
                    alert('Terjadi kesalahan saat menyimpan.');
                }
            },
            error: function() {
                $('#btnSave').html('<i class="fa fa-check-circle" style="margin-right:6px;"></i> ' + btnOriginalText);
                $('#btnSave').attr('disabled', false);
                alert('Error submitting data');
            }
        });
    });

    // =============================================================
    // FLOATING CHAT WIDGET (Dosen)
    // =============================================================
    $('#skripsiChatWidget').appendTo('body');
    $('#chatBubbleFab').appendTo('body');
    
    var chatOpen = false;
    var idJudulSkripsi = '<?= $data_skripsi ? $data_skripsi->id_judul_skripsi : "" ?>';
    var currentUserId = <?= $this->ion_auth->user()->row()->id ?? 0 ?>;
    var chatPollingInterval = null;

    $('#btnToggleChat').click(function() {
        chatOpen = false;
        $('#skripsiChatWidget').css({ transform: 'translateY(120%)', opacity: 0 });
        setTimeout(function() {
            $('#skripsiChatWidget').hide();
            $('#chatBubbleFab').css('display', 'flex').hide().fadeIn(200);
        }, 300);
        clearInterval(chatPollingInterval);
    });

    $('#chatBubbleFab').click(function() {
        chatOpen = true;
        $(this).fadeOut(200, function() {
            $('#skripsiChatWidget').css('display', 'flex');
            // Force reflow
            $('#skripsiChatWidget')[0].offsetHeight;
            $('#skripsiChatWidget').css({ transform: 'translateY(0)', opacity: 1 });
            $('#chatUnreadBadge').hide().text('0');
            loadChats();
            startChatPolling();
        });
    });

    function formatChatLinks(text) {
        if (!text) return '';
        var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
        return text.replace(urlRegex, function(url) {
            var cleanUrl = url.trim();
            if (cleanUrl.indexOf('drive.google.com') !== -1 || cleanUrl.indexOf('docs.google.com') !== -1) {
                var embedUrl = cleanUrl;
                if (embedUrl.indexOf('/view') !== -1) {
                    embedUrl = embedUrl.replace(/\/view.*?$/, '/preview');
                }
                var clickHandler = "if (typeof openDriveModal === 'function') { openDriveModal('" + embedUrl + "'); } else if (typeof bukaGdrive === 'function') { bukaGdrive('" + embedUrl + "'); }";
                return `<br><button onclick="${clickHandler}" class="btn btn-xs btn-primary font-white" style="margin: 6px 0; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 6px; background: #2563EB; border: none; padding: 5px 10px; color: #ffffff !important; box-shadow: 0 2px 4px rgba(37,99,235,0.2); outline: none;"><i class="fa fa-google"></i> Preview Google Drive</button><br>`;
            } else {
                var clickHandler = "if (typeof openDriveModal === 'function') { openDriveModal('" + cleanUrl + "'); } else if (typeof bukaGdrive === 'function') { bukaGdrive('" + cleanUrl + "'); }";
                return `<br><button onclick="${clickHandler}" class="btn btn-xs btn-default" style="margin: 6px 0; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; border-radius: 6px; border: 1px solid #D1D5DB; padding: 5px 10px; color: #374151 !important; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); outline: none;"><i class="fa fa-external-link"></i> Lihat Tautan</button><br>`;
            }
        });
    }

    function loadChats() {
        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_get_chat/') ?>" + idJudulSkripsi,
            type: "GET",
            dataType: "JSON",
            success: function(res) {
                if (res.status) {
                    renderChats(res.data);
                }
            }
        });
    }

    function renderChats(chats) {
        var html = '';
        if (chats.length === 0) {
            html = '<div style="text-align:center; padding:30px 10px; color:#9CA3AF;"><div style="font-size:30px; margin-bottom:10px;">👋</div>Mulai percakapan dengan mahasiswa.</div>';
        } else {
            chats.forEach(function(chat) {
                var isMe = (chat.pengirim_type === 'dosen' && chat.pengirim_id == currentUserId);
                var align = isMe ? 'flex-end' : 'flex-start';
                var bg = isMe ? '#4F46E5' : '#E5E7EB';
                var color = isMe ? '#fff' : '#374151';
                var radius = isMe ? '16px 16px 0 16px' : '16px 16px 16px 0';
                
                var timeParts = chat.created_at.split(' ');
                var timeStr = timeParts[1].substring(0, 5);

                var senderName = chat.first_name ? $.trim(chat.first_name + ' ' + (chat.last_name || '')) : (chat.pengirim_type === 'dosen' ? 'Dosen' : 'Mahasiswa');
                var nameHtml = !isMe ? `<div style="font-size:10px; font-weight:700; margin-bottom:4px; color:#4F46E5;">${senderName}</div>` : '';

                html += `
                    <div style="display:flex; flex-direction:column; align-items:${align}; margin-bottom:4px;">
                        <div style="max-width:85%; background:${bg}; color:${color}; padding:8px 12px; border-radius:${radius}; font-size:13px; line-height:1.5; box-shadow:0 1px 2px rgba(0,0,0,0.05); word-break:break-word;">
                            ${nameHtml}
                            ${formatChatLinks(chat.pesan.replace(/\\n/g, '<br>'))}
                        </div>
                        <div style="font-size:10px; color:#9CA3AF; margin-top:4px;">${timeStr}</div>
                    </div>
                `;
            });
        }
        $('#chatMessagesContainer').html(html);
        var scrollBox = $('#chatBodyScroll')[0];
        scrollBox.scrollTop = scrollBox.scrollHeight;
    }

    $('#btnSendChat').click(function() {
        var pesan = $('#chatInputMsg').val().trim();
        if (!pesan) return;

        $('#chatInputMsg').val('');
        $('#chatInputMsg').focus();

        // Optimistic UI
        var timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        var html = `
            <div style="display:flex; flex-direction:column; align-items:flex-end; margin-bottom:4px; opacity:0.7;">
                <div style="max-width:85%; background:#4F46E5; color:#fff; padding:8px 12px; border-radius:16px 16px 0 16px; font-size:13px; line-height:1.5; box-shadow:0 1px 2px rgba(0,0,0,0.05); word-break:break-word;">
                    ${formatChatLinks(pesan.replace(/\\n/g, '<br>'))}
                </div>
                <div style="font-size:10px; color:#9CA3AF; margin-top:4px;">Mengirim...</div>
            </div>
        `;
        if ($('#chatMessagesContainer').children().length === 1 && $('#chatMessagesContainer').text().includes('Mulai percakapan')) {
            $('#chatMessagesContainer').html('');
        }
        $('#chatMessagesContainer').append(html);
        var scrollBox = $('#chatBodyScroll')[0];
        scrollBox.scrollTop = scrollBox.scrollHeight;

        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_send_chat') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                id_judul_skripsi: idJudulSkripsi,
                pesan: pesan,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    loadChats();
                } else {
                    alert(res.msg);
                }
            }
        });
    });

    $('#chatInputMsg').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#btnSendChat').trigger('click');
        }
    });

    function startChatPolling() {
        var checkUnread = function() {
            if (chatOpen) {
                loadChats();
            } else {
                $.ajax({
                    url: "<?= site_url('E_skripsi_bimbingan/ajax_get_unread_chat/') ?>" + idJudulSkripsi,
                    type: "GET",
                    dataType: "JSON",
                    success: function(res) {
                        if (res.status && res.count > 0) {
                            $('#chatUnreadBadge').text(res.count).show();
                        } else {
                            $('#chatUnreadBadge').hide();
                        }
                    }
                });
            }
        };

        // Run immediately
        checkUnread();

        // Poll every 5 seconds
        chatPollingInterval = setInterval(checkUnread, 5000);
    }

    // Initialize chat hidden state
    $('#skripsiChatWidget').css({ transform: 'translateY(120%)', opacity: 0 }).hide();
    $('#chatBubbleFab').css('display', 'flex');

    // Start polling immediately for unread messages if it is closed
    startChatPolling();

});

function showPremiumToast(type, title, message) {
    var icon = '✨';
    if (type === 'success') icon = '✅';
    else if (type === 'warning') icon = '⚠️';
    else if (type === 'info') icon = 'ℹ️';

    var toastHtml = `
        <div class="premium-toast ${type}">
            <div class="premium-toast-icon">${icon}</div>
            <div class="premium-toast-content">
                <div class="premium-toast-title">${title}</div>
                <div class="premium-toast-message">${message}</div>
            </div>
        </div>
    `;

    var $toast = $(toastHtml).appendTo('body');
    $toast[0].offsetHeight;
    $toast.addClass('show');

    setTimeout(function() {
        $toast.removeClass('show');
        setTimeout(function() { $toast.remove(); }, 400);
    }, 6000);
}


function bukaGdrive(url, id_log = null) {
    if (url.includes('/view')) {
        url = url.replace('/view', '/preview');
    }
    $('#iframeGdrive').attr('src', url);
    
    // Reset AI panel
    $('#modalGdriveAiReviewContainer').hide();
    
    // Check if AI review exists for this log
    if (id_log) {
        var rawContent = $('#aiReviewContent_' + id_log + ' .ai-raw-text').val();
        var existingContent = $('#aiReviewContent_' + id_log).html();
        
        if (!rawContent || rawContent.trim() === '') {
            // Check the visible container if it's already rendered in PHP
            var visibleRaw = $('#aiReviewContainer_' + id_log + ' .ai-raw-text').val();
            if (visibleRaw && visibleRaw.trim() !== '') {
                rawContent = visibleRaw;
                existingContent = $('#aiReviewContainer_' + id_log + ' .ai-review-content').html();
            }
        }
        
        if (rawContent && rawContent.trim() !== '') {
            $('#modalGdriveAiReviewContent').html(existingContent);
            $('#modalGdriveAiReviewContainer').css('display', 'flex');
        }
    }

    $('#modalGdrive').modal('show');
}

/**
 * Konversi teks Markdown sederhana ke HTML.
 * Menangani: heading (#/##/###), bold (**), italic (*), hr (---), list item (-), dan baris baru.
 */
function markdownToHtml(text) {
    // Escape HTML terlebih dahulu agar karakter khusus aman
    var escaped = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    // Pre-processing: Sulap daftar temuan menjadi tabel HTML
    var linesRaw = escaped.split('\n');
    var processedLines = [];
    var inTemuan = false;
    var currentAspek = '';
    var inTable = false;

    for (var i = 0; i < linesRaw.length; i++) {
        var lr = linesRaw[i].trim();

        if (lr.indexOf('### 🔍 Temuan Evaluasi') !== -1) {
            inTemuan = true;
            processedLines.push(lr);
            continue;
        }

        if (inTemuan && (lr.indexOf('### ✅') !== -1 || lr.indexOf('### 📌') !== -1 || lr.indexOf('### 📋') !== -1)) {
            inTemuan = false;
            if (inTable) {
                processedLines.push('</tbody></table></div>');
                inTable = false;
            }
            processedLines.push(lr);
            continue;
        }

        if (inTemuan) {
            // Deteksi Aspek (bold text tanpa bullet)
            var aspekMatch = lr.match(/^\*\*(.*?)\*\*$/);
            if (aspekMatch) {
                currentAspek = aspekMatch[1];
                if (!inTable) {
                    processedLines.push('<div class="table-responsive" style="margin-top:15px; margin-bottom:15px;"><table class="table table-bordered table-hover" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">');
                    processedLines.push('<thead style="background:#f8fafc; color:#1e293b;"><tr><th style="width:22%;">Aspek</th><th>Detail Temuan & Saran</th></tr></thead>');
                    processedLines.push('<tbody>');
                    inTable = true;
                }
                continue;
            }

            // Deteksi Temuan (bullet point dengan icon)
            var findingMatch = lr.match(/^-\s*(🔴|🟡|🟢|\?)\s*(.*)/);
            if (findingMatch) {
                var icon = findingMatch[1] === '?' ? '🟡' : findingMatch[1];
                var detail = findingMatch[2];
                
                // Rapikan text detail
                detail = detail.replace(/\*\*\s*Saran:\s*\*\*/ig, 'Saran:');
                detail = detail.replace(/(\*\*.+?\*\*)\s*(?:—|-|💡|📝|💭|\?|:)?\s*(?=[*\"“'])/g, '$1<br><strong style="color:#000;">Kutipan:</strong> ');
                detail = detail.replace(/(?:→|➡️|💡|📝|💭|\?)?\s*\**Saran:\**/ig, '<br><strong style="color:#059669;">Saran:</strong> ');
                // Bold/Italic parser for detail
                detail = applyInlineMarkdown(detail);

                if (!inTable) {
                    processedLines.push('<div class="table-responsive" style="margin-top:15px; margin-bottom:15px;"><table class="table table-bordered table-hover" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">');
                    processedLines.push('<thead style="background:#f8fafc; color:#1e293b;"><tr><th style="width:22%;">Aspek</th><th>Detail Temuan & Saran</th></tr></thead>');
                    processedLines.push('<tbody>');
                    inTable = true;
                }

                processedLines.push('<tr>');
                processedLines.push('<td style="font-weight:700; color:#000; vertical-align:top;"><div style="display:flex; align-items:flex-start; gap:8px;"><span style="font-size:18px; line-height:1;">' + icon + '</span><span style="padding-top:2px;">' + currentAspek + '</span></div></td>');
                processedLines.push('<td style="line-height:1.6; vertical-align:top;">' + detail + '</td>');
                processedLines.push('</tr>');
                continue;
            }
            
            // Skip baris kosong di dalam tabel, tapi jangan tutup tabel
            if (inTable && lr === '') continue;
            
            // Jika ada teks lain di dalam section temuan yg tidak match
            if (inTable && !findingMatch && !aspekMatch) {
                if (lr.startsWith('- ')) {
                    // List biasa di dalam temuan
                    processedLines.push('<tr><td></td><td>' + applyInlineMarkdown(lr.replace(/^- /, '• ')) + '</td></tr>');
                    continue;
                } else {
                    // Teks biasa (bukan bullet, bukan aspek, bukan kosong)
                    // Tutup tabel agar paragraf ini muncul DI BAWAH tabel, bukan di-hoist oleh browser
                    processedLines.push('</tbody></table></div>');
                    inTable = false;
                }
            }
        }

        processedLines.push(lr);
    }

    if (inTable) {
        processedLines.push('</tbody></table></div>');
    }

    var lines = processedLines;
    var html = '';
    var inList = false;

    lines.forEach(function(line) {
        var trimmed = line.trim();

        // Bypass HTML tag yg dihasilkan dari pre-processing
        if (/^<div|^<table|^<thead|^<tbody|^<tr|^<td|^<th|^<\/tbody|^<\/table|^<\/div/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            html += trimmed + '\n';
            return;
        }

        // Heading ### / ## / #
        if (/^###\s+(.+)/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<h5 style="margin: 14px 0 4px; font-weight: 700; color: #1e3a5f;">' + trimmed.replace(/^###\s+/, '') + '</h5>';
        } else if (/^##\s+(.+)/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<h4 style="margin: 16px 0 4px; font-weight: 700; color: #1e3a5f;">' + trimmed.replace(/^##\s+/, '') + '</h4>';
        } else if (/^#\s+(.+)/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<h3 style="margin: 18px 0 4px; font-weight: 700; color: #1e3a5f;">' + trimmed.replace(/^#\s+/, '') + '</h3>';

        // Horizontal rule
        } else if (/^---+$/.test(trimmed) || /^\*\*\*+$/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<hr style="border: none; border-top: 1px solid #e5e7eb; margin: 10px 0;">';

        // List item
        } else if (/^[-*+]\s+(.+)/.test(trimmed)) {
            if (!inList) { html += '<ul style="margin: 4px 0 4px 16px; padding: 0;">'; inList = true; }
            var itemContent = trimmed.replace(/^[-*+]\s+/, '');
            itemContent = applyInlineMarkdown(itemContent);
            html += '<li style="margin-bottom: 4px;">' + itemContent + '</li>';

        // Numbered list item  
        } else if (/^\d+\.\s+(.+)/.test(trimmed)) {
            if (inList) { html += '</ul>'; inList = false; }
            var numContent = trimmed.replace(/^\d+\.\s+/, '');
            numContent = applyInlineMarkdown(numContent);
            html += '<p style="margin: 3px 0;"><strong style="color:#000;">' + trimmed.match(/^(\d+\.)/)[1] + '</strong> ' + numContent + '</p>';

        // Empty line → spacing
        } else if (trimmed === '') {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<div style="height: 6px;"></div>';

        // Normal paragraph line
        } else {
            if (inList) { html += '</ul>'; inList = false; }
            html += '<p style="margin: 3px 0;">' + applyInlineMarkdown(trimmed) + '</p>';
        }
    });

    if (inList) html += '</ul>';
    return html;
}

/** Terapkan bold, italic, dan inline code pada satu baris teks. */
function applyInlineMarkdown(text) {
    // Bold+italic ***text***
    text = text.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    // Bold **text**
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    // Italic *text* (hindari emoji dan simbol tunggal)
    text = text.replace(/\*([^\s*][^*]*[^\s*]|[^\s*])\*/g, '<em>$1</em>');
    // Inline code `text`
    text = text.replace(/`([^`]+)`/g, '<code style="background:#f3f4f6;padding:1px 4px;border-radius:3px;font-size:92%;">$1</code>');
    return text;
}

function generateAiReview(id_log, id_judul_skripsi, reviewType = '', forceReview = 0) {
    var $btn = $('#btnAiReview_' + id_log);
    var $container = $('#aiReviewContainer_' + id_log);
    var $content = $('#aiReviewContent_' + id_log);
    
    $btn.html('<i class="fa fa-spinner fa-spin"></i> AI Sedang Membaca...');
    $btn.attr('disabled', true);
    
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_generate_ai_review') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_log: id_log,
            id_judul_skripsi: id_judul_skripsi,
            review_type: reviewType,
            force_review: forceReview,
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            $btn.html('<i class="fa fa-magic"></i> Minta AI Review');
            $btn.attr('disabled', false);
            
            if (res.status === 'require_warning') {
                Swal.fire({
                    title: 'Perhatian',
                    html: '<div style="font-size:14px; text-align:left;">' + res.msg + '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-arrow-right"></i> Tetap Lanjutkan Review AI',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal / Tunggu Pendamping',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generateAiReview(id_log, id_judul_skripsi, reviewType, 1);
                    }
                });
                return;
            }

            if (res.status === true) {
                // Konversi markdown ke HTML menggunakan fungsi terpusat
                var htmlContent = markdownToHtml(res.review);
                
                if ($content.length) {
                    $content.find('.ai-review-text').html(htmlContent);
                    $content.find('.ai-raw-text').val(res.review);
                    $container.slideDown();
                    $btn.hide(); // Hide button after success
                    $('#btnSalinAI').show(); // Tampilkan tombol salin di form
                } else {
                    location.reload();
                }
                showPremiumToast('success', 'Review Selesai', res.msg);
            } else if (res.status === false) {
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        },
        error: function() {
            $btn.html('<i class="fa fa-magic"></i> Minta AI Review');
            $btn.attr('disabled', false);
            showPremiumToast('error', 'Error', 'Terjadi kesalahan komunikasi dengan server.');
        }
    });
}




function resetAiReview(id_log, id_judul_skripsi) {
    if (!confirm('Hapus hasil AI Review ini?\n\nReview yang sudah ada akan dihapus permanen sehingga bisa digenerate ulang (misalnya setelah delegasi ke Dosen Pendamping).')) {
        return;
    }

    var $btnReset = $('#btnResetAiReview_' + id_log);
    $btnReset.html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);

    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_reset_ai_review') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_log: id_log,
            id_judul_skripsi: id_judul_skripsi,
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            if (res.status) {
                // Sembunyikan card AI review
                $('#aiReviewContainer_' + id_log).slideUp(300, function() {
                    $(this).find('.ai-review-text').html('');
                    $(this).find('.ai-raw-text').val('');
                });

                // Tampilkan kembali tombol "Minta AI Review"
                var $btnGenerate = $('#btnAiReview_' + id_log);
                if ($btnGenerate.length) {
                    $btnGenerate.html('<i class="fa fa-magic"></i> Minta AI Review').show().attr('disabled', false);
                }

                // Sembunyikan tombol salin di form bawah (jika log ini yang aktif)
                $('#btnSalinAI').hide();

                showPremiumToast('success', 'Berhasil', 'Hasil AI Review berhasil dihapus. Silakan generate ulang.');
            } else {
                $btnReset.html('<i class="fa fa-trash-o"></i> Reset').attr('disabled', false);
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        },
        error: function() {
            $btnReset.html('<i class="fa fa-trash-o"></i> Reset').attr('disabled', false);
            showPremiumToast('warning', 'Error', 'Terjadi kesalahan sistem. Coba lagi.');
        }
    });
}

function salinKesimpulanAi(id_log) {
    var textContent = $('#aiReviewContent_' + id_log + ' .ai-raw-text').val();
    if (!textContent) {
        textContent = $('#aiReviewContent_' + id_log + ' .ai-review-text').text() || '';
    }
    
    // Cari bagian kesimpulan
    var marker = "Kesimpulan untuk Catatan Dosen ke Mahasiswa:";
    var idx = textContent.indexOf(marker);
    var kesimpulan = "";
    
    if (idx !== -1) {
        kesimpulan = textContent.substring(idx + marker.length).trim();
    } else {
        // Jika tidak ketemu persis, coba variasi lain atau ambil 300 karakter terakhir
        var marker2 = "Kesimpulan untuk Catatan Dosen";
        var idx2 = textContent.indexOf(marker2);
        if (idx2 !== -1) {
            kesimpulan = textContent.substring(idx2 + marker2.length).trim();
            // hilangkan "ke Mahasiswa:" jika terbawa
            if (kesimpulan.startsWith("ke Mahasiswa:")) {
                kesimpulan = kesimpulan.substring("ke Mahasiswa:".length).trim();
            }
        } else {
            // Fallback: Minta dosen meng-copy manual jika format tidak sesuai
            showPremiumToast('warning', 'Gagal', 'Bagian "Kesimpulan" tidak ditemukan secara otomatis.');
            return;
        }
    }
    
    if (kesimpulan) {
        // Bersihkan format markdown dasar agar tidak ikut tersalin
        kesimpulan = kesimpulan
            .replace(/\*\*(.*?)\*\*/g, '$1') // hapus bold
            .replace(/\*(.*?)\*/g, '$1')     // hapus italic
            .replace(/#{1,6}\s?/g, '')       // hapus heading
            .replace(/`(.*?)`/g, '$1')       // hapus inline code
            .replace(/\[(.*?)\]\(.*?\)/g, '$1'); // bersihkan link
            
        var $textarea = $('textarea[name="catatan_dosen_wali"]');
        if ($textarea.length && $textarea.is(':visible')) {
            var currentVal = $textarea.val();
            if (currentVal.trim() !== '') {
                $textarea.val(currentVal + '\n\n' + kesimpulan);
            } else {
                $textarea.val(kesimpulan);
            }
            
            // Scroll to textarea
            $('html, body').animate({
                scrollTop: $textarea.offset().top - 100
            }, 500);
            
            showPremiumToast('success', 'Berhasil Disalin', 'Kesimpulan telah disalin ke form catatan.');
        } else {
            showPremiumToast('warning', 'Form Tidak Tersedia', 'Teks berhasil disalin ke clipboard! Form catatan saat ini tidak aktif karena bukan giliran review Anda atau bimbingan ini sudah selesai.');
            // Copy to clipboard fallback
            navigator.clipboard.writeText(kesimpulan);
        }
    }
}

function salinKesimpulanAiModal() {
    var textContent = $('#modalGdriveAiReviewContent .ai-raw-text').val();
    if (!textContent) {
        textContent = $('#modalGdriveAiReviewContent').text() || '';
    }
    
    // Cari bagian kesimpulan
    var marker = "Kesimpulan untuk Catatan Dosen ke Mahasiswa:";
    var idx = textContent.indexOf(marker);
    var kesimpulan = "";
    
    if (idx !== -1) {
        kesimpulan = textContent.substring(idx + marker.length).trim();
    } else {
        var marker2 = "Kesimpulan untuk Catatan Dosen";
        var idx2 = textContent.indexOf(marker2);
        if (idx2 !== -1) {
            kesimpulan = textContent.substring(idx2 + marker2.length).trim();
            if (kesimpulan.startsWith("ke Mahasiswa:")) {
                kesimpulan = kesimpulan.substring("ke Mahasiswa:".length).trim();
            }
        } else {
            showPremiumToast('warning', 'Gagal', 'Bagian "Kesimpulan" tidak ditemukan secara otomatis.');
            return;
        }
    }
    
    if (kesimpulan) {
        // Bersihkan format markdown dasar agar tidak ikut tersalin
        kesimpulan = kesimpulan
            .replace(/\*\*(.*?)\*\*/g, '$1') // hapus bold
            .replace(/\*(.*?)\*/g, '$1')     // hapus italic
            .replace(/#{1,6}\s?/g, '')       // hapus heading
            .replace(/`(.*?)`/g, '$1')       // hapus inline code
            .replace(/\[(.*?)\]\(.*?\)/g, '$1'); // bersihkan link
            
        var $textarea = $('textarea[name="catatan_dosen_wali"]');
        if ($textarea.length && $textarea.is(':visible')) {
            var currentVal = $textarea.val();
            if (currentVal.trim() !== '') {
                $textarea.val(currentVal + '\n\n' + kesimpulan);
            } else {
                $textarea.val(kesimpulan);
            }
            
            // Tutup modal
            $('#modalGdrive').modal('hide');
            
            // Scroll to textarea
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $textarea.offset().top - 100
                }, 500);
            }, 300);
            
            showPremiumToast('success', 'Berhasil Disalin', 'Kesimpulan telah disalin ke form catatan.');
        } else {
            showPremiumToast('warning', 'Form Tidak Tersedia', 'Teks berhasil disalin ke clipboard! Form catatan saat ini tidak aktif karena bukan giliran review Anda atau bimbingan ini sudah selesai.');
            navigator.clipboard.writeText(kesimpulan);
        }
    }
}

// --- AGENDA BIMBINGAN JS ---

function showBuatAgendaModal() {
    var $btn = $('#btn_buat_agenda_utama');
    var originalHtml = $btn.html();
    
    // Cegah double click
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin" style="font-size: 18px;"></i> <span>Memeriksa Status...</span>');

    var id_judul = $.trim('<?= $data_skripsi->id_judul_skripsi ?>');
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_get_agenda/') ?>" + id_judul,
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            try {
                // Kembalikan tombol ke semula
                $btn.prop('disabled', false).html(originalHtml);
                
                if (res.status && res.agenda) {
                    // Double preventive hit!
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Aksi Ditolak',
                            text: 'Terdapat Agenda Bimbingan yang masih aktif berjalan. Silakan selesaikan atau akhiri agenda tersebut terlebih dahulu.',
                            confirmButtonColor: '#DB2777'
                        });
                    } else {
                        alert('Aksi Ditolak: Terdapat Agenda Bimbingan yang masih aktif berjalan. Silakan selesaikan atau akhiri agenda tersebut terlebih dahulu.');
                    }
                    loadAgendaAktif(); // Paksa sync UI (menggembok tombol)
                } else {
                    // Aman, tidak ada agenda aktif, buka modal
                    _openAgendaModal();
                }
            } catch (err) {
                alert("Error in ajax success: " + err.message);
            }
        },
        error: function(xhr, status, error) {
            $btn.prop('disabled', false).html(originalHtml);
            showPremiumToast('warning', 'Koneksi Gagal', 'Gagal memverifikasi status agenda dengan server.');
            alert('Error load agenda: ' + error + ' (Status: ' + xhr.status + ')');
        }
    });
}

function _openAgendaModal() {
    try {
        // Reset form fields MANUALLY - jangan pakai form.reset() karena akan hapus teks picker
        $('#agenda_instruksi').val('');
        $('#agenda_ai_review').val('');
        $('select[name="prioritas"]').val('Sedang');
        $('select[name="fase_seminar"]').val('Belum Sempro');
        $('#todoListContainer').empty();
        $('#aiReviewAgendaPreview').hide().html('');
        // Reset kondisi fields
        $('#ctx_bab').val('');
        $('#ctx_progress_persen').val(0);
        $('#ctx_progress_label').text('0%');
        $('#ctx_frekuensi').val('');
        $('#ctx_responsif').val('');
        $('#ctx_kendala').val('');
        $('#ctx_catatan_tambahan').val('');
        
        // Reset tahapan stepper ke default (Proposal)
        if($('.tahapan-card[data-val="Proposal"]').length > 0) {
            selectTahapan($('.tahapan-card[data-val="Proposal"]')[0]);
        }

        var today = moment().format('YYYY-MM-DD');

        // Set tanggal default ke hari ini
        $('#agenda_tanggal').val(today);
        $('#hidden_tgl_selesai').val(today);

        // Ketika tanggal diubah, sesuaikan tgl_selesai
        $('#agenda_tanggal').off('change').on('change', function() {
            $('#hidden_tgl_selesai').val($(this).val());
        });

        // Reset jam ke default dan inisialisasi timepicker format 24 jam
        $('#jam_mulai_display').val('08:00');
        $('#jam_selesai_display').val('10:00');
        
        if($.fn.timepicker) {
            $('.timepicker').timepicker({
                showMeridian: false, // 24hr format
                minuteStep: 5,
                defaultTime: false,
                icons: {
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down'
                }
            });
        }

        $('#modalBuatAgenda').modal('show');
    } catch (e) {
        alert("Error saat membuka modal: " + e.message);
        console.error("Open modal error:", e);
    }
}

function selectTahapan(el) {
    // Hapus active dari semua card
    $('.tahapan-card').each(function() {
        $(this).removeClass('active')
               .css({ 'border-color': '#E5E7EB', 'background': '#F9FAFB', 'box-shadow': 'none' });
        $(this).find('div:first-child').css('transform', 'scale(1)');
        $(this).find('div:nth-child(2)').css('color', '#374151');
        $(this).find('div:nth-child(3)').css('color', '#9CA3AF');
    });
    // Set active pada yang diklik
    var $el = $(el);
    $el.addClass('active')
       .css({ 'border-color': '#4F46E5', 'background': '#EEF2FF', 'box-shadow': '0 4px 12px rgba(79,70,229,0.2)' });
    $el.find('div:first-child').css('transform', 'scale(1.15)');
    $el.find('div:nth-child(2)').css('color', '#3730A3');
    $el.find('div:nth-child(3)').css('color', '#6366F1');
    // Update hidden input
    $('#agenda_tahapan').val($el.data('val'));
}

function generateAiAgenda() {
    var id_judul = $('#agenda_id_judul').val();
    var instruksi = $('#agenda_instruksi').val();
    var fase_seminar = $('#agenda_fase_seminar').val();
    var prioritas = $('select[name="prioritas"]').val();

    // Kumpulkan semua data kondisi mahasiswa
    var ctx_bab = $('#ctx_bab').val();
    var ctx_progress = $('#ctx_progress_persen').val() + '%';
    var ctx_frekuensi = $('#ctx_frekuensi').val();
    var ctx_responsif = $('#ctx_responsif').val();
    var ctx_kendala = $('#ctx_kendala').val();
    var ctx_catatan = $('#ctx_catatan_tambahan').val();
    var tahapan = $('#agenda_tahapan').val();

    var $btn = $('#btn_generate_ai_agenda');
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Generating...').prop('disabled', true);

    // Bangun objek konteks yang kaya
    var konteks = {
        "Instruksi Dosen": instruksi || '(tidak diisi)',
        "Tahapan Bimbingan": tahapan,
        "Prioritas Agenda": prioritas,
        "Fase Seminar": fase_seminar
    };
    if (ctx_bab) konteks["Fokus Bagian/Bab (Opsional)"] = ctx_bab;
    konteks["Estimasi Progress pada Tahapan Bimbingan Terpilih"] = ctx_progress;
    if (ctx_frekuensi) konteks["Frekuensi Bimbingan"] = ctx_frekuensi;
    if (ctx_responsif) konteks["Responsivitas Mahasiswa"] = ctx_responsif;
    if (ctx_kendala) konteks["Kendala yang Diketahui Dosen"] = ctx_kendala;
    if (ctx_catatan) konteks["Catatan Tambahan"] = ctx_catatan;

    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_generate_ai_agenda') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_judul_skripsi: id_judul,
            fase_seminar: fase_seminar,
            konteks: konteks,
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            $btn.html('<i class="fa fa-magic"></i> &#x2728; Generate via AI').prop('disabled', false);
            if (res.status && res.data) {
                var data = res.data;
                // Preview AI Review
                var reviewHtml = `
                    <div style="margin-bottom:10px;"><strong style="color:#111827;">Analisis AI:</strong><br>${markdownToHtml(data.review_kondisi)}</div>
                    <div><strong style="color:#111827;">Rekomendasi Strategi:</strong><br>${markdownToHtml(data.strategi_percepatan)}</div>
                `;
                $('#aiReviewAgendaPreview').html(reviewHtml).show();
                $('#agenda_ai_review').val(JSON.stringify({review: data.review_kondisi, strategi: data.strategi_percepatan}));
                
                // Populate Todo List
                if (data.todo_list && data.todo_list.length > 0) {
                    var tasks = data.todo_list.map(function(item, index) {
                        return (index + 1) + ". " + item.task;
                    });
                    $('#todoTextarea').val(tasks.join('\n'));
                } else {
                    $('#todoTextarea').val('');
                }
                
                showPremiumToast('success', 'AI Generate Berhasil', 'To-Do list dan analisis berhasil dibuat.');
                // Hapus error banner jika ada
                $('#ai_error_banner').remove();
            } else {
                var errMsg = res.msg || 'Terjadi kesalahan.';
                // Cek apakah error karena API Key
                if (errMsg.toLowerCase().indexOf('api key') !== -1 || errMsg.toLowerCase().indexOf('api_key') !== -1) {
                    // Tampilkan inline banner yang informatif di dalam modal
                    var apiBanner = `
                        <div id="ai_error_banner" style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:14px 18px; margin-bottom:16px; display:flex; align-items:flex-start; gap:12px;">
                            <span style="font-size:22px; flex-shrink:0;">&#x1F511;</span>
                            <div style="flex:1;">
                                <div style="font-weight:800; color:#991B1B; font-size:13px; margin-bottom:3px;">API Key AI Belum Dikonfigurasi</div>
                                <div style="font-size:12px; color:#B91C1C; line-height:1.5;">Fitur Generate via AI membutuhkan API Key yang valid. Silakan konfigurasi terlebih dahulu di menu <strong>Settings &rarr; AI Configuration</strong>.</div>
                            </div>
                        </div>
                    `;
                    $('#aiReviewAgendaPreview').html(apiBanner).show();
                } else {
                    showPremiumToast('warning', 'AI Generate Gagal', errMsg);
                    $('#ai_error_banner').remove();
                }
            }
        },
        error: function() {
            $btn.html('<i class="fa fa-magic"></i> &#x2728; Generate via AI').prop('disabled', false);
            showPremiumToast('warning', 'Error', 'Gagal menghubungi server.');
        }
    });
}

function simpanAgenda() {
    // Validate
    if (!$('#formBuatAgenda')[0].checkValidity()) {
        $('#formBuatAgenda')[0].reportValidity();
        return;
    }
    
    var $btn = $('#btnSimpanAgenda');
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
    
    var todos = [];
    var lines = $('#todoTextarea').val().split('\n');
    var urutan = 1;
    lines.forEach(function(line) {
        var task = line.trim();
        task = task.replace(/^[\d\.\-\*]+\s*/, '');
        if (task !== '') {
            todos.push({ urutan: urutan++, task: task });
        }
    });
    
    var dataForm = $('#formBuatAgenda').serializeArray();
    dataForm.push({ name: 'todos', value: JSON.stringify(todos) });
    dataForm.push({ name: '<?= $this->security->get_csrf_token_name() ?>', value: '<?= $this->security->get_csrf_hash() ?>' });
    
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_save_agenda') ?>",
        type: "POST",
        dataType: "JSON",
        data: dataForm,
        success: function(res) {
            if (res.status) {
                $('#modalBuatAgenda').modal('hide');
                showPremiumToast('success', 'Berhasil', res.msg);
                // Reset form and UI
                $('#formBuatAgenda')[0].reset();
                $('#btn_generate_ai_agenda').html('<i class="fa fa-magic"></i> Generate To-Do List dengan AI').prop('disabled', false);
                $('#ai_response_container').hide();
                loadAgendaAktif();
            } else {
                $btn.html('Simpan Agenda').prop('disabled', false);
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        },
        error: function() {
            $btn.html('Simpan Agenda').prop('disabled', false);
            showPremiumToast('warning', 'Error', 'Gagal menghubungi server.');
        }
    });
}

function loadAgendaAktif() {
    var id_judul = '<?= $data_skripsi->id_judul_skripsi ?>';
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_get_agenda/') ?>" + id_judul,
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            if (res.status && res.agenda) {
                // renderAgendaPanel(res.agenda, res.todos);
                // $('#panel_agenda_aktif_container').show();
                $('#panel_agenda_aktif_container').hide();
                // Gembok tombol inisiatif jika agenda sedang berjalan
                $('#btn_buat_agenda_utama').prop('disabled', true)
                    .html('<i class="fa fa-lock" style="font-size: 18px;"></i> <span>Agenda Sedang Berjalan</span>')
                    .css({
                        'background': '#D1D5DB',
                        'color': '#6B7280',
                        'cursor': 'not-allowed',
                        'box-shadow': 'none'
                    }).removeAttr('onclick');
            } else {
                $('#panel_agenda_aktif_container').empty().hide();
            }
            // Render arsip agenda (selalu, meski tidak ada yang aktif)
            if (res.arsip && res.arsip.length > 0) {
                renderAgendaArsipSection(res.arsip);
                $('#panel_agenda_arsip_container').show();
            } else {
                $('#panel_agenda_arsip_container').empty().hide();
            }
        }
    });
}

function renderAgendaPanel(agenda, todos) {
    var html = `
    <div style="background: #FFFFFF; border: 2px solid #818CF8; border-radius: 14px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.15); position: relative; overflow: hidden;">
        <!-- Banner/Badge -->
        <div style="position: absolute; top: 0; right: 0; background: #4F46E5; color: white; padding: 6px 16px; border-bottom-left-radius: 14px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fa fa-calendar-check-o"></i> AGENDA AKTIF
        </div>
        
        <h4 style="margin: 0 0 16px 0; color: #3730A3; font-weight: 800; font-size: 18px; display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; background: #EEF2FF; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #4F46E5;">
                <i class="fa fa-bullseye"></i>
            </div>
            Target Bimbingan Dosen
        </h4>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
            <div style="background: #F9FAFB; padding: 12px 16px; border-radius: 8px; border: 1px solid #E5E7EB; flex: 1; min-width: 200px;">
                <div style="font-size: 11px; color: #6B7280; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Timeline Pelaksanaan</div>
                <div style="color: #111827; font-weight: 600; font-size: 14px;">
                    <i class="fa fa-calendar text-primary"></i> ${formatDate(agenda.agenda_tgl_mulai)} - ${formatDate(agenda.agenda_tgl_selesai)}
                </div>
            </div>
            <div style="background: #F9FAFB; padding: 12px 16px; border-radius: 8px; border: 1px solid #E5E7EB; flex: 1; min-width: 200px;">
                <div style="font-size: 11px; color: #6B7280; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Fase & Prioritas</div>
                <div style="display: flex; gap: 8px;">
                    <span class="badge" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-weight: 700;">${agenda.agenda_fase_seminar || '-'}</span>
                    <span class="badge" style="background: ${getPriorityBg(agenda.agenda_prioritas)}; color: ${getPriorityColor(agenda.agenda_prioritas)}; border: 1px solid ${getPriorityBorder(agenda.agenda_prioritas)}; font-weight: 700;">${agenda.agenda_prioritas || '-'}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <div style="font-size: 12px; color: #4B5563; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Instruksi / Catatan:</div>
            <div style="font-size: 14px; color: #1F2937; line-height: 1.6; background: #FEFCE8; padding: 16px; border-left: 4px solid #F59E0B; border-radius: 0 8px 8px 0;">
                ${(agenda.catatan_mahasiswa || '-').replace(/\n/g, '<br>')}
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <div style="font-size: 12px; color: #4B5563; font-weight: 700; text-transform: uppercase; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span>To-Do List Mahasiswa:</span>
                <span style="font-size: 11px; font-weight: 600; color: #6B7280;">${countTodoStats(todos)}</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                ${renderTodoItems(todos)}
            </div>
        </div>
        
        <div style="border-top: 1px solid #E5E7EB; padding-top: 16px; display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn btn-sm btn-default" onclick="showEditTodoModal('${agenda.id_log}', '<?= $data_skripsi->id_judul_skripsi ?>')" style="font-weight: 600; border-radius: 6px; padding: 8px 16px; border-color: #C7D2FE; color: #4F46E5; background: #EEF2FF;">
                <i class="fa fa-pencil"></i> Edit To-Do List
            </button>
        </div>
    </div>
    `;
    $('#panel_agenda_aktif_container').html(html);
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    var d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function getPriorityBg(p) { return p === 'Tinggi' ? '#FEE2E2' : (p === 'Sedang' ? '#FEF3C7' : '#F3F4F6'); }
function getPriorityColor(p) { return p === 'Tinggi' ? '#991B1B' : (p === 'Sedang' ? '#92400E' : '#374151'); }
function getPriorityBorder(p) { return p === 'Tinggi' ? '#FCA5A5' : (p === 'Sedang' ? '#FCD34D' : '#D1D5DB'); }

function countTodoStats(todos) {
    if (!todos || todos.length === 0) return '';
    var done = todos.filter(function(t) { return t.verified_dosen === 1 || t.done_by_mhs === 1; }).length;
    return `${done}/${todos.length} selesai`;
}

function renderTodoItems(todos) {
    if (!todos || todos.length === 0) return '<div style="color: #9CA3AF; font-size: 13px; font-style: italic;">Tidak ada to-do list.</div>';
    var html = '';
    todos.forEach(function(t) {
        var statusBadge = '';
        var verifyBtn = '';
        var itemBg = '#FFFFFF';
        var itemBorder = '1px solid #E5E7EB';
        var checkIcon = 'fa-square-o';
        var checkColor = '#9CA3AF';
        var textDecoration = 'none';
        var textColor = '#111827';

        if (t.done_by_mhs === 1) {
            itemBg = '#F0FDF4';
            itemBorder = '1px solid #BBF7D0';
            checkIcon = 'fa-check-square-o';
            statusBadge = `<span class="badge" style="background:#D1FAE5; color:#065F46; font-size:10px;"><i class="fa fa-flag"></i> Dilaporkan Selesai</span>`;
            if (t.verified_dosen === 0) {
                verifyBtn = `
                    <button class="btn btn-xs btn-success" onclick="verifyTodoDosen('${t.id_todo}', 1, this)" style="border-radius:4px; font-weight:700;"><i class="fa fa-check"></i> Setujui</button>
                    <button class="btn btn-xs btn-danger" onclick="verifyTodoDosen('${t.id_todo}', -1, this)" style="border-radius:4px; font-weight:700;"><i class="fa fa-times"></i> Tolak</button>
                `;
                checkColor = '#10B981';
            } else if (t.verified_dosen === 1) {
                itemBg = '#EFF6FF';
                itemBorder = '1px solid #BFDBFE';
                checkColor = '#2563EB';
                textDecoration = 'line-through';
                textColor = '#6B7280';
                statusBadge += ` <span class="badge" style="background:#4F46E5; color:#fff; font-size:10px;"><i class="fa fa-check-circle"></i> Disetujui Dosen</span>`;
            } else if (t.verified_dosen === -1) {
                itemBg = '#FFF1F2';
                itemBorder = '1px solid #FECDD3';
                checkIcon = 'fa-times-circle-o';
                checkColor = '#EF4444';
                statusBadge = `<span class="badge" style="background:#FEE2E2; color:#991B1B; font-size:10px;"><i class="fa fa-times-circle"></i> Ditolak Dosen</span>`;
                verifyBtn = `<button class="btn btn-xs btn-primary" onclick="verifyTodoDosen('${t.id_todo}', 1, this)" style="border-radius:4px; font-weight:700;"><i class="fa fa-redo"></i> Setujui Ulang</button>`;
            }
        } else {
            statusBadge = `<span class="badge" style="background:#F3F4F6; color:#4B5563; font-size:10px;">Belum Selesai</span>`;
        }
        
        html += `
        <div style="display: flex; align-items: flex-start; gap: 12px; background: ${itemBg}; border: ${itemBorder}; padding: 12px; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition: all 0.2s;">
            <div style="color: ${checkColor}; font-size: 18px; margin-top: 2px; flex-shrink: 0;">
                <i class="fa ${checkIcon}"></i>
            </div>
            <div style="flex: 1;">
                <div style="font-size: 14px; color: ${textColor}; font-weight: 500; text-decoration: ${textDecoration};">
                    ${t.task}
                </div>
                <div class="status-badge-container" style="margin-top: 6px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    ${statusBadge}
                </div>
                ${t.notes_mahasiswa ? `<div style="margin-top:8px; font-size:12px; color:#4B5563; background:#F3F4F6; padding:8px; border-radius:6px; border-left:3px solid #9CA3AF;"><strong>Catatan Mhs:</strong> ${t.notes_mahasiswa}</div>` : ''}
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;">
                ${verifyBtn}
            </div>
        </div>
        `;
    });
    return html;
}

function verifyTodoDosen(id_todo, verified, btnEl) {
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_update_todo_dosen') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_todo: id_todo,
            verified: verified,
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            if (res.status) {
                showPremiumToast('success', 'Berhasil', 'Status To-Do berhasil diperbarui.');
                
                if (btnEl) {
                    var $parentTodo = $(btnEl).closest('div[style*="background"]');
                    var $actionDiv = $(btnEl).parent();
                    var $badgeContainer = $parentTodo.find('.status-badge-container');
                    var $textTask = $parentTodo.find('> div:eq(1) > div:first-child');
                    var $iconTask = $parentTodo.find('> div:eq(0) i');
                    var $iconContainer = $parentTodo.find('> div:eq(0)');
                    var badgeStyle = "display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; line-height: 1.2;";
                    
                    $badgeContainer.find('span.badge:contains("Dosen")').remove();
                    
                    if (verified == 1) {
                        $parentTodo.css({'background': '#EFF6FF', 'border': '1px solid #BFDBFE'});
                        $textTask.css({'text-decoration': 'line-through', 'color': '#6B7280'});
                        $iconTask.removeClass().addClass('fa fa-check-square-o');
                        $iconContainer.css({'color': '#2563EB'});
                        $actionDiv.empty();
                        $badgeContainer.append('<span class="badge" style="background:#4F46E5; color:#fff; ' + badgeStyle + '"><i class="fa fa-check-circle"></i> Disetujui Dosen</span>');
                    } else if (verified == -1) {
                        $parentTodo.css({'background': '#FFF1F2', 'border': '1px solid #FECDD3'});
                        $textTask.css({'text-decoration': 'none', 'color': '#111827'});
                        $iconTask.removeClass().addClass('fa fa-times-circle-o');
                        $iconContainer.css({'color': '#EF4444'});
                        var btnStyle = "border-radius: 6px; font-weight: 700; padding: 5px 12px; font-size: 11px; display: inline-flex; align-items: center; gap: 5px;";
                        $actionDiv.html(
                            '<button class="btn btn-sm btn-primary" onclick="verifyTodoDosen(\'' + id_todo + '\', 1, this)" style="' + btnStyle + ' box-shadow: 0 2px 4px rgba(59,130,246,0.2);"><i class="fa fa-redo"></i> Setujui Ulang</button>'
                        );
                        $badgeContainer.append('<span class="badge" style="background:#FEE2E2; color:#991B1B; ' + badgeStyle + '"><i class="fa fa-times-circle"></i> Ditolak Dosen</span>');
                    }
                }
                
                loadAgendaAktif();
            } else {
                showPremiumToast('warning', 'Gagal', 'Terjadi kesalahan.');
            }
        }
    });
}



$(function() {
    loadAgendaAktif();
});

// ----- EDIT TODO MODAL -----
var _editTodoIdLog = null;
var _editTodoIdJudul = null;

function showEditTodoModal(id_log, id_judul) {
    _editTodoIdLog = id_log;
    _editTodoIdJudul = id_judul;

    // Load current todos
    $('#editTodoListContainer').html('<div style="text-align:center; padding:20px;"><i class="fa fa-spinner fa-spin fa-2x" style="color:#CBD5E1;"></i></div>');
    $('#modalEditTodo').modal('show');

    // Get todos from server
    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_get_agenda/') ?><?= $data_skripsi->id_judul_skripsi ?>",
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            $('#editTodoListContainer').empty();
            if (res.status && res.todos && res.todos.length > 0) {
                res.todos.forEach(function(t) {
                    addEditTodoItem(t.task);
                });
            } else {
                addEditTodoItem('');
            }
        },
        error: function() {
            $('#editTodoListContainer').html('<p class="text-danger">Gagal memuat data.</p>');
        }
    });
}

function addEditTodoItem(val) {
    var id = 'edit_todo_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
    var escapedVal = val.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    var html = `
        <div class="todo-item-row" id="${id}" style="display:flex; gap:10px; align-items:flex-start; margin-bottom:8px;">
            <div style="flex:1;">
                <textarea class="form-control edit-todo-input" placeholder="Deskripsi tugas..." style="border-radius:6px; border:1px solid #D1D5DB; padding:8px 12px; min-height:70px; resize:vertical; line-height:1.5;">${escapedVal}</textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="$('#${id}').remove()" style="border-radius:6px; padding:8px 12px;"><i class="fa fa-trash"></i></button>
        </div>
    `;
    $('#editTodoListContainer').append(html);
}

function simpanEditTodo() {
    var todos = [];
    var i = 1;
    $('.edit-todo-input').each(function() {
        var v = $(this).val().trim();
        if (v !== '') { todos.push({ urutan: i++, task: v }); }
    });

    if (todos.length === 0) {
        showPremiumToast('warning', 'Peringatan', 'To-Do list tidak boleh kosong.');
        return;
    }

    var $btn = $('#btnSimpanEditTodo');
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

    $.ajax({
        url: "<?= site_url('E_skripsi_bimbingan/ajax_edit_todo_list') ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_log: _editTodoIdLog,
            id_judul_skripsi: _editTodoIdJudul,
            todos: JSON.stringify(todos),
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            $btn.html('Simpan Perubahan').prop('disabled', false);
            if (res.status) {
                $('#modalEditTodo').modal('hide');
                showPremiumToast('success', 'Berhasil', res.msg);
                loadAgendaAktif();
            } else {
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        },
        error: function() {
            $btn.html('Simpan Perubahan').prop('disabled', false);
            showPremiumToast('warning', 'Error', 'Gagal menghubungi server.');
        }
    });
}

// ----- ARSIP AGENDA -----
function renderAgendaArsipSection(arsipList) {
    if (!arsipList || arsipList.length === 0) {
        $('#panel_agenda_arsip_container').empty().hide();
        return;
    }

    var arsipHtml = arsipList.map(function(a, idx) {
        var aiData = null;
        try { if (a.ai_review_agenda) aiData = JSON.parse(a.ai_review_agenda); } catch(e) {}

        var doneTodos = (a.todos || []).filter(function(t) { return t.verified_dosen == 1; }).length;
        var totalTodos = (a.todos || []).length;

        var aiSection = '';
        if (aiData) {
            aiSection = `<div style="margin-top:12px; background:#EEF2FF; border-radius:8px; padding:12px 16px; font-size:12.5px; color:#3730A3;">
                <strong style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">&#x1F9E0; AI Review Agenda:</strong><br>
                <div style="margin-top:6px; color:#000; line-height:1.6;">${aiData.review ? markdownToHtml(aiData.review).substring(0, 300) + '...' : ''}</div>
            </div>`;
        }

        var todoListHtml = (a.todos && a.todos.length > 0)
            ? a.todos.map(function(t) {
                var ico = t.verified_dosen == 1 ? 'fa-check-square-o' : (t.done_by_mhs == 1 ? 'fa-check-square-o' : 'fa-square-o');
                var col = t.verified_dosen == 1 ? '#10B981' : (t.done_by_mhs == 1 ? '#6B7280' : '#D1D5DB');
                var td  = t.verified_dosen == 1 ? 'line-through' : 'none';
                return `<div style="display:flex; align-items:center; gap:8px; font-size:13px; padding:6px 0; border-bottom:1px dashed #F3F4F6; color:${t.verified_dosen == 1 ? '#6B7280' : '#374151'}; text-decoration:${td};"><i class="fa ${ico}" style="color:${col};"></i>${t.task}</div>`;
            }).join('')
            : '<div style="color:#9CA3AF; font-size:13px; font-style:italic;">Tidak ada to-do.</div>';

        var closedDate = a.reviewed_at_pendamping ? formatDate(a.reviewed_at_pendamping.substring(0,10)) : '-';

        return `
        <div style="background:#FAFAFA; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; margin-bottom:10px;">
            <div onclick="$('#arsip_body_${idx}').slideToggle(); $(this).find('i.arsip-toggle').toggleClass('fa-chevron-down fa-chevron-up');" style="padding:14px 20px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; user-select:none;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:32px; height:32px; background:#E5E7EB; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#6B7280; font-size:14px;"><i class="fa fa-check-circle"></i></div>
                    <div>
                        <div style="font-weight:700; font-size:13px; color:#374151;">
                            ${formatDate(a.agenda_tgl_mulai)} &rarr; ${formatDate(a.agenda_tgl_selesai)}
                            <span class="badge" style="background:${getPriorityBg(a.agenda_prioritas)}; color:${getPriorityColor(a.agenda_prioritas)}; font-size:10px; margin-left:6px;">${a.agenda_prioritas || '-'}</span>
                        </div>
                        <div style="font-size:11px; color:#9CA3AF; margin-top:2px;">Ditutup ${closedDate} &bull; ${doneTodos}/${totalTodos} tugas selesai</div>
                    </div>
                </div>
                <i class="fa fa-chevron-down arsip-toggle" style="color:#9CA3AF;"></i>
            </div>
            <div id="arsip_body_${idx}" style="display:none; padding:16px 20px; background:#FFF; border-top:1px solid #F3F4F6;">
                <div style="font-size:12px; color:#4B5563; font-weight:700; text-transform:uppercase; margin-bottom:8px;">Instruksi Agenda:</div>
                <div style="font-size:13px; color:#374151; margin-bottom:16px; background:#FEFCE8; padding:12px; border-left:4px solid #F59E0B; border-radius:0 8px 8px 0;">${(a.catatan_mahasiswa || '-').replace(/\n/g, '<br>')}</div>
                <div style="font-size:12px; color:#4B5563; font-weight:700; text-transform:uppercase; margin-bottom:8px;">To-Do List:</div>
                ${todoListHtml}
                ${aiSection}
            </div>
        </div>
        `;
    }).join('');

    var html = `
    <div style="margin-top:20px;">
        <div onclick="$('#arsipAgendaBody').slideToggle(); $(this).find('i.arsip-main-toggle').toggleClass('fa-chevron-down fa-chevron-up');" style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#F3F4F6; border-radius:10px; cursor:pointer; user-select:none; margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:8px; font-weight:700; color:#4B5563; font-size:13px;">
                <i class="fa fa-history" style="color:#6B7280;"></i> Riwayat Agenda Selesai (${arsipList.length})
            </div>
            <i class="fa fa-chevron-down arsip-main-toggle" style="color:#9CA3AF;"></i>
        </div>
        <div id="arsipAgendaBody" style="display:none;">${arsipHtml}</div>
    </div>
    `;
    $('#panel_agenda_arsip_container').html(html);
}

</script>

<!-- ============================================================ -->
<!-- AI REVIEW PER BAB — JavaScript Engine                        -->
<!-- ============================================================ -->
<script>
(function() {
    // =========================================================
    // STATE
    // =========================================================
    var rpbState = {
        id_judul_skripsi : null,
        babs             : [],         // array bab dari server
        summaries        : [],         // [{bab_label, bab_title, summary}] untuk chaining
        currentBabIndex  : null,       // index bab yang sedang ditampilkan
        isRunningAll     : false,      // apakah mode "review semua" aktif
        currentReviews   : {},         // { id_chunk: reviewText } cache client-side
        dosen_role       : '<?= (isset($is_pendamping) && $is_pendamping && !(isset($is_utama) && $is_utama)) ? "pendamping" : ((isset($is_utama) && $is_utama && !(isset($is_pendamping) && $is_pendamping)) ? "utama" : "kombinasi") ?>',
        csrfToken        : '<?= $this->security->get_csrf_token_name() ?>',
        csrfHash         : '<?= $this->security->get_csrf_hash() ?>',
        ajaxUrl          : {
            getChunks   : '<?= site_url("E_skripsi_bimbingan/ajax_get_chunks_bab") ?>',
            reviewChunk : '<?= site_url("E_skripsi_bimbingan/ajax_review_bab_chunk") ?>',
        }
    };

    // Helper: label mode analisis berdasarkan peran dosen
    function rpbGetModeLabel() {
        if (rpbState.dosen_role === 'pendamping') {
            return '<span style="display:inline-block;margin-top:6px;padding:3px 10px;background:#EEF2FF;color:#4338CA;border-radius:20px;font-size:11px;font-weight:700;">' +
                   '🔍 Mode: Proofreading — tata bahasa, sitasi/daftar pustaka &amp; risiko plagiasi</span>';
        } else if (rpbState.dosen_role === 'utama') {
            return '<span style="display:inline-block;margin-top:6px;padding:3px 10px;background:#F0FDF4;color:#15803D;border-radius:20px;font-size:11px;font-weight:700;">' +
                   '🎓 Mode: Substansi — tata tulis, kesinambungan antar bab &amp; kesimpulan holistik</span>';
        }
        return '<span style="display:inline-block;margin-top:6px;padding:3px 10px;background:#FFF7ED;color:#C2410C;border-radius:20px;font-size:11px;font-weight:700;">' +
               '📋 Mode: Komprehensif — substansi &amp; tata tulis</span>';
    }

    // =========================================================
    // OPEN MODAL
    // =========================================================
    window.openReviewPerBabModal = function(id_judul_skripsi) {
        rpbState.id_judul_skripsi  = id_judul_skripsi;
        rpbState.babs              = [];
        rpbState.summaries         = [];
        rpbState.currentBabIndex   = null;
        rpbState.isRunningAll      = false;
        rpbState.currentReviews    = {};

        // Reset UI
        $('#rpb_bab_list').html('<div style="text-align:center;padding:40px 0;color:#6B7280;font-size:13px;"><i class="fa fa-spinner fa-spin" style="font-size:24px;margin-bottom:10px;display:block;"></i>Memuat daftar bab...</div>');
        $('#rpb_subtitle').text('Memuat data...');
        $('#rpb_info_log').text('');
        $('#rpb_progress_info').text('Pilih bab dari sidebar untuk melihat atau generate review.');
        $('#rpb_chain_badge').hide();
        rpbShowState('initial');
        $('#rpb_footer_actions').hide();
        rpbSetProgressBar(0);
        $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Review Semua Bab (Sequential)').prop('disabled', false);

        $('#modalReviewPerBab').modal('show');

        // Ambil data chunks dari server
        $.ajax({
            url  : rpbState.ajaxUrl.getChunks,
            type : 'POST',
            dataType: 'JSON',
            data : {
                id_judul_skripsi : id_judul_skripsi,
                [rpbState.csrfToken]: rpbState.csrfHash
            },
            success: function(res) {
                if (!res.status) {
                    $('#rpb_bab_list').html('<div style="padding:20px;color:#EF4444;font-size:12px;text-align:center;"><i class="fa fa-exclamation-triangle" style="font-size:20px;margin-bottom:8px;display:block;"></i>' + res.msg + '</div>');
                    return;
                }

                rpbState.babs = res.babs;
                rpbState.id_log = res.id_log;
                $('#rpb_subtitle').text(res.judul_ta.substring(0, 50) + (res.judul_ta.length > 50 ? '...' : ''));
                $('#rpb_info_log').html('Dari upload log #' + res.id_log + '<br>' + res.babs.length + ' bab terdeteksi');

                // Set status checkbox share review
                $('#rpb_toggle_share').prop('checked', res.is_ai_review_visible == 1);

                // Hitung yang sudah direview
                var doneCount = res.babs.filter(function(b) { return b.has_review; }).length;
                $('#rpb_progress_info').text(doneCount + ' dari ' + res.babs.length + ' bab sudah direview.');
                rpbSetProgressBar(res.babs.length > 0 ? (doneCount / res.babs.length) * 100 : 0);

                // Prefill summaries dari yang sudah ada
                res.babs.forEach(function(b) {
                    if (b.has_review && b.ai_review_summary) {
                        rpbState.summaries.push({
                            bab_label: b.bab_label,
                            bab_title: b.bab_title,
                            summary  : b.ai_review_summary
                        });
                        if (b.ai_review_result) {
                            rpbState.currentReviews[b.id] = b.ai_review_result;
                        }
                    }
                });

                rpbRenderSidebar();
                
                // Otomatis pilih bab pertama agar layar tidak kosong
                if (res.babs.length > 0) {
                    rpbSelectBab(0);
                }
            },
            error: function() {
                $('#rpb_bab_list').html('<div style="padding:20px;color:#EF4444;font-size:12px;text-align:center;">Gagal menghubungi server.</div>');
            }
        });
    };

    // =========================================================
    // RENDER SIDEBAR
    // =========================================================
    function rpbRenderSidebar() {
        var html = '';
        rpbState.babs.forEach(function(bab, idx) {
            var isReviewed = bab.has_review || rpbState.currentReviews[bab.id];
            var isActive   = (rpbState.currentBabIndex === idx);
            var cls = 'rpb-bab-item' + (isReviewed ? ' reviewed' : '') + (isActive ? ' active' : '');

            var iconContent = isReviewed
                ? '<i class="fa fa-check-circle"></i>'
                : (bab.bab_number);

            var statusBadge = isReviewed
                ? '<span class="rpb-bab-status" style="color:#6EE7B7;font-size:10px;">✓ Done</span>'
                : '<span class="rpb-bab-status" style="color:#475569;font-size:10px;">' + Math.round((bab.char_count || 0) / 1000) + 'K chr</span>';

            html += '<div class="' + cls + '" onclick="rpbSelectBab(' + idx + ')" id="rpb_bab_item_' + idx + '">' +
                        '<div class="rpb-bab-icon">' + iconContent + '</div>' +
                        '<div style="flex:1;min-width:0;">' +
                            '<div style="font-weight:700;font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:0.5px;">' + bab.bab_label + '</div>' +
                            '<div style="font-size:11.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + bab.bab_title + '</div>' +
                        '</div>' +
                        statusBadge +
                    '</div>';
        });
        $('#rpb_bab_list').html(html);
    }

    // =========================================================
    // PILIH BAB (klik sidebar)
    // =========================================================
    window.rpbSelectBab = function(idx) {
        if (rpbState.isRunningAll) return; // block klik saat auto-run

        rpbState.currentBabIndex = idx;
        rpbRenderSidebar();
        
        // Reset scroll position
        $('#rpb_review_area').scrollTop(0);

        var bab = rpbState.babs[idx];
        if (!bab) return;

        // Cek apakah sudah ada review (cached di client atau server)
        var cachedReview = rpbState.currentReviews[bab.id];

        if (cachedReview) {
            rpbDisplayReview(bab, cachedReview, true);
        } else if (bab.has_review) {
            // Muat dari server
            rpbShowState('loading');
            $('#rpb_loading_title').text('Memuat review ' + bab.bab_label + '...');
            $('#rpb_loading_desc').text('Mengambil hasil review yang sudah tersimpan.');
            rpbFetchReviewChunk(bab, false, function(res) {
                if (res.status === true || res.status === undefined || res.status) {
                    rpbState.currentReviews[bab.id] = res.review;
                    rpbDisplayReview(bab, res.review, true);
                } else {
                    rpbShowError(res.msg);
                }
            });
        } else {
            // Belum ada review — tawarkan generate
            var canGenerate = true;
            for(var i = 0; i < idx; i++) {
                if (!rpbState.babs[i].has_review && !rpbState.currentReviews[rpbState.babs[i].id]) {
                    canGenerate = false;
                    break;
                }
            }

            var btnHtml = '';
            var warningHtml = '';
            
            if (canGenerate) {
                btnHtml = '<button onclick="rpbGenerateSingleBab(' + idx + ')" class="btn btn-primary" style="background:#4F46E5;border:none;font-weight:700;font-size:13px;padding:10px 24px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;">' +
                          '<i class="fa fa-magic"></i> Generate Review ' + bab.bab_label + '' +
                          '</button>';
            } else {
                warningHtml = '<br><br><span style="color:#EF4444;font-weight:700;"><i class="fa fa-lock"></i> Review Terkunci</span><br>Anda harus me-review bab-bab sebelumnya terlebih dahulu agar evaluasi AI berkesinambungan.';
                btnHtml = '<button disabled class="btn btn-secondary" style="background:#D1D5DB;border:none;color:#6B7280;font-weight:700;font-size:13px;padding:10px 24px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;cursor:not-allowed;">' +
                          '<i class="fa fa-lock"></i> Generate Review ' + bab.bab_label + '' +
                          '</button>';
            }

            rpbShowState('initial');
            $('#rpb_initial_state').html(
                '<div style="font-size:50px;margin-bottom:16px;">📄</div>' +
                '<div style="font-size:17px;font-weight:700;color:#374151;margin-bottom:8px;">' + bab.bab_label + ' – ' + bab.bab_title + '</div>' +
                '<div style="font-size:13px;color:#6B7280;margin-bottom:20px;max-width:360px;line-height:1.6;">' +
                    'Belum ada review untuk bab ini. ' + (canGenerate ? 'Klik tombol di bawah untuk minta AI menganalisis <strong>' + bab.bab_label + '</strong>.' : '') +
                    (canGenerate && rpbState.summaries.length > 0 ? '<br><br><span style="color:#4F46E5;font-weight:700;">🔗 Konteks dari ' + rpbState.summaries.length + ' bab sebelumnya akan disertakan.</span>' : '') +
                    warningHtml +
                '</div>' +
                btnHtml
            );
            $('#rpb_footer_actions').hide();
        }
    };

    // =========================================================
    // GENERATE SINGLE BAB
    // =========================================================
    window.rpbGenerateSingleBab = function(idx) {
        var bab = rpbState.babs[idx];
        if (!bab) return;

        rpbState.currentBabIndex = idx;

        // Bangun previous summaries dari bab SEBELUM bab ini
        var prevSummaries = rpbState.summaries.filter(function(s) {
            return rpbState.babs.findIndex(function(b) {
                return b.bab_label === s.bab_label;
            }) < idx;
        });

        rpbShowState('loading');
        $('#rpb_loading_title').text('AI sedang menganalisis ' + bab.bab_label + '...');
        $('#rpb_loading_desc').html(
            '<strong>' + bab.bab_label + ' – ' + bab.bab_title + '</strong><br>' +
            (prevSummaries.length > 0 ? '🔗 Review berkesinambungan — menyertakan konteks dari ' + prevSummaries.length + ' bab sebelumnya.' : 'Review mandiri tanpa konteks bab sebelumnya.') +
            '<br>' + rpbGetModeLabel() +
            '<br><br><em style="color:#9CA3AF;font-size:11px;">Proses ini memerlukan beberapa saat. Harap tunggu...</em>'
        );

        rpbSetItemStatus(idx, 'reviewing');

        rpbFetchReviewChunk(bab, true, function(res) {
            if (res.status === true || res.status === undefined || res.status) {
                rpbState.currentReviews[bab.id] = res.review;
                bab.has_review = true;
                bab.ai_review_summary = res.summary;

                // Update summaries untuk chaining
                rpbUpdateSummaries(bab, res.summary);

                rpbSetItemStatus(idx, 'reviewed');
                rpbUpdateProgressBar();
                rpbDisplayReview(bab, res.review, res.cached);
                showPremiumToast('success', 'Review Selesai', bab.bab_label + ' berhasil dianalisis AI.');
            } else {
                rpbSetItemStatus(idx, null);
                rpbShowError(res.msg);
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        }, prevSummaries);
    };

    // =========================================================
    // REVIEW SEMUA BAB SEQUENTIAL (AUTO-RUN)
    // =========================================================
    window.rpbStartReviewAll = function() {
        if (rpbState.babs.length === 0) {
            showPremiumToast('warning', 'Belum Ada Data', 'Tunggu hingga daftar bab selesai dimuat.');
            return;
        }

        rpbState.isRunningAll = true;
        rpbState.summaries    = [];
        $('#rpb_btn_review_all').html('<i class="fa fa-spinner fa-spin"></i> Sedang Mereview...').prop('disabled', true);
        $('#rpb_btn_reset_all').prop('disabled', true);
        $('#rpb_chain_badge').show();

        // Mulai dari bab pertama yang belum ada review,
        // tapi bab yang sudah ada review akan di-skip (ambil dari cache) dan tetap disertakan sebagai context
        rpbRunNextBab(0);
    };

    function rpbRunNextBab(idx) {
        if (idx >= rpbState.babs.length) {
            // Selesai semua
            rpbState.isRunningAll = false;
            $('#rpb_btn_review_all').html('<i class="fa fa-check-circle"></i> Semua Bab Selesai!').prop('disabled', false);
            $('#rpb_btn_reset_all').prop('disabled', false);
            rpbUpdateProgressBar();
            rpbRenderSidebar();
            showPremiumToast('success', 'Review Lengkap', 'Seluruh ' + rpbState.babs.length + ' bab berhasil dianalisis secara berkesinambungan.');
            // Tampilkan tombol Buka Tampilan Bersebelahan
            $('#btnSbs_' + rpbState.id_log).show();
            return;
        }

        var bab = rpbState.babs[idx];
        rpbState.currentBabIndex = idx;
        rpbRenderSidebar();

        // Update status bar
        $('#rpb_progress_info').text('Mereview ' + bab.bab_label + ' (' + (idx + 1) + '/' + rpbState.babs.length + ')...');

        // Jika sudah ada review & ada summary → skip generate, ambil cache
        if (bab.has_review && bab.ai_review_summary) {
            rpbState.currentReviews[bab.id] = rpbState.currentReviews[bab.id] || '__cached__';
            rpbUpdateSummaries(bab, bab.ai_review_summary);

            // Load review untuk ditampilkan
            rpbFetchReviewChunk(bab, false, function(res) {
                if (res.status === true || res.status === undefined || res.status) {
                    rpbState.currentReviews[bab.id] = res.review;
                    rpbDisplayReview(bab, res.review, true);
                }
                rpbUpdateProgressBar();
                setTimeout(function() { rpbRunNextBab(idx + 1); }, 400);
            });
            return;
        }

        // Belum ada review → generate
        rpbShowState('loading');
        $('#rpb_loading_title').text('AI sedang menganalisis ' + bab.bab_label + '...');
        $('#rpb_loading_desc').html(
            '<strong>' + bab.bab_label + ' – ' + bab.bab_title + '</strong><br>' +
            (rpbState.summaries.length > 0
                ? '🔗 Menyertakan konteks dari ' + rpbState.summaries.length + ' bab sebelumnya.'
                : 'Bab pertama — tanpa konteks sebelumnya.') +
            '<br>' + rpbGetModeLabel() +
            '<br><br><em style="color:#9CA3AF;font-size:11px;">Proses ini memerlukan beberapa saat. Harap tunggu...</em>'
        );
        rpbSetItemStatus(idx, 'reviewing');

        rpbFetchReviewChunk(bab, true, function(res) {
            if (res.status === 'require_warning') {
                rpbState.isRunningAll = false;
                $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Lanjutkan Review (dari ' + bab.bab_label + ')').prop('disabled', false);
                $('#rpb_btn_reset_all').prop('disabled', false);
                rpbSetItemStatus(idx, null);
                
                Swal.fire({
                    title: 'Perhatian',
                    html: '<div style="font-size:14px; text-align:left;">' + res.msg + '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-arrow-right"></i> Tetap Lanjutkan Review AI',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal / Tunggu Pendamping',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        rpbState.isRunningAll = true;
                        $('#rpb_btn_review_all').html('<i class="fa fa-spinner fa-spin"></i> Menjalankan...').prop('disabled', true);
                        $('#rpb_btn_reset_all').prop('disabled', true);
                        
                        rpbShowState('loading');
                        rpbSetItemStatus(idx, 'reviewing');
                        rpbFetchReviewChunk(bab, true, function(res2) {
                            if (res2.status === true || res2.status === undefined || res2.status) {
                                rpbState.currentReviews[bab.id] = res2.review;
                                bab.has_review        = true;
                                bab.ai_review_summary = res2.summary;
                                rpbUpdateSummaries(bab, res2.summary);
                                rpbSetItemStatus(idx, 'reviewed');
                                rpbUpdateProgressBar();
                                rpbDisplayReview(bab, res2.review, res2.cached);
                                showPremiumToast('info', bab.bab_label + ' Selesai', 'Lanjut ke bab berikutnya...');
                                setTimeout(function() { rpbRunNextBab(idx + 1); }, 800);
                            } else {
                                rpbState.isRunningAll = false;
                                $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Lanjutkan Review (dari ' + bab.bab_label + ')').prop('disabled', false);
                                $('#rpb_btn_reset_all').prop('disabled', false);
                                rpbSetItemStatus(idx, null);
                                rpbShowError(res2.msg + '<br><br><strong>Auto-review dihentikan. Silakan coba lagi.</strong>');
                                showPremiumToast('warning', 'Error pada ' + bab.bab_label, res2.msg);
                            }
                        }, rpbState.summaries.slice(), 1);
                    } else {
                        rpbShowError('Review dibatalkan sementara menunggu Dosen Pendamping.');
                    }
                });
                return;
            }

            if (res.status === true || res.status === undefined || res.status) {
                rpbState.currentReviews[bab.id] = res.review;
                bab.has_review        = true;
                bab.ai_review_summary = res.summary;
                rpbUpdateSummaries(bab, res.summary);
                rpbSetItemStatus(idx, 'reviewed');
                rpbUpdateProgressBar();
                rpbDisplayReview(bab, res.review, res.cached);
                showPremiumToast('info', bab.bab_label + ' Selesai', 'Lanjut ke bab berikutnya...');
                setTimeout(function() { rpbRunNextBab(idx + 1); }, 800);
            } else {
                rpbState.isRunningAll = false;
                $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Lanjutkan Review (dari ' + bab.bab_label + ')').prop('disabled', false);
                $('#rpb_btn_reset_all').prop('disabled', false);
                rpbSetItemStatus(idx, null);
                rpbShowError(res.msg + '<br><br><strong>Auto-review dihentikan. Silakan coba lagi.</strong>');
                showPremiumToast('warning', 'Error pada ' + bab.bab_label, res.msg);
            }
        }, rpbState.summaries.slice());
    }

    // =========================================================
    // FETCH KE SERVER (core function)
    // =========================================================
    function rpbFetchReviewChunk(bab, forceRegenerate, callback, previousSummaries, forceReview = 0) {
        var summariesToSend = previousSummaries || rpbState.summaries.filter(function(s) {
            return rpbState.babs.findIndex(function(b) { return b.bab_label === s.bab_label; }) <
                   rpbState.babs.findIndex(function(b) { return b.id === bab.id; });
        });

        $.ajax({
            url     : rpbState.ajaxUrl.reviewChunk,
            type    : 'POST',
            dataType: 'JSON',
            timeout : 180000, // 3 menit timeout
            data    : {
                id_chunk          : bab.id,
                id_judul_skripsi  : rpbState.id_judul_skripsi,
                force_regenerate  : forceRegenerate ? '1' : '0',
                previous_summaries: JSON.stringify(summariesToSend),
                force_review      : forceReview,
                [rpbState.csrfToken]: rpbState.csrfHash
            },
            success: callback,
            error: function(xhr) {
                var msg = 'Gagal menghubungi server.';
                if (xhr.status === 0) msg = 'Koneksi terputus atau server timeout.';
                else if (xhr.status === 500) msg = 'Server error (500). Cek log PHP.';
                callback({ status: false, msg: msg });
            }
        });
    }

    // =========================================================
    // DISPLAY REVIEW
    // =========================================================
    function rpbDisplayReview(bab, reviewText, isCached) {
        rpbShowState('result');
        $('#rpb_review_area').scrollTop(0);
        var htmlContent = markdownToHtml(reviewText);
        var cachedBadge = isCached
            ? '<span style="font-size:10px;background:#E0F2FE;color:#0284C7;padding:2px 8px;border-radius:10px;font-weight:700;margin-left:8px;">cache</span>'
            : '<span style="font-size:10px;background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:10px;font-weight:700;margin-left:8px;">baru</span>';

        var reviewHtml = '<div style="max-width:860px;">' +
            '<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid #E5E7EB;">' +
                '<div style="background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🤖</div>' +
                '<div>' +
                    '<div style="font-weight:800;font-size:15px;color:#111827;">' + bab.bab_label + ' – ' + bab.bab_title + cachedBadge + '</div>' +
                    '<div style="font-size:11px;color:#9CA3AF;margin-top:2px;">AI Review ' + (rpbState.summaries.length > 0 ? '· Berkesinambungan (' + rpbState.summaries.length + ' bab sebelumnya)' : '· Mandiri') + '</div>' +
                '</div>' +
            '</div>' +
            '<div class="rpb-review-text" style="font-size:13.5px;line-height:1.8;color:#374151;">' + htmlContent + '</div>' +
            '<textarea class="rpb-raw-text" style="display:none;">' + $('<div>').text(reviewText).html() + '</textarea>' +
        '</div>';

        $('#rpb_result_content').html(reviewHtml);

        // Update footer
        $('#rpb_current_bab_info').text(bab.bab_label + ' – ' + bab.bab_title);
        $('#rpb_footer_actions').css('display', 'flex');

        // Update progress bar & status
        rpbUpdateProgressBar();
    }

    // =========================================================
    // REGENERATE CURRENT BAB
    // =========================================================
    window.rpbRegenerateCurrentBab = function() {
        var idx = rpbState.currentBabIndex;
        if (idx === null || idx === undefined) return;
        var bab = rpbState.babs[idx];
        if (!bab) return;

        if (!confirm('Generate ulang review ' + bab.bab_label + '?\n\nHasil review lama akan diganti dengan yang baru.')) return;

        // Reset di state supaya di-regenerate
        delete rpbState.currentReviews[bab.id];
        bab.has_review = false;

        // Ambil previous summaries: semua bab SEBELUM bab ini yang sudah ada summary
        var prevSummaries = [];
        for (var i = 0; i < idx; i++) {
            var prevBab = rpbState.babs[i];
            var foundSummary = rpbState.summaries.find(function(s) { return s.bab_label === prevBab.bab_label; });
            if (foundSummary) prevSummaries.push(foundSummary);
        }

        rpbShowState('loading');
        $('#rpb_loading_title').text('AI sedang menganalisis ulang ' + bab.bab_label + '...');
        $('#rpb_loading_desc').html(
            '<strong>' + bab.bab_label + ' – ' + bab.bab_title + '</strong><br>' +
            (prevSummaries.length > 0 ? '🔗 Menyertakan konteks dari ' + prevSummaries.length + ' bab sebelumnya.' : 'Review mandiri.') +
            '<br>' + rpbGetModeLabel() +
            '<br><br><em style="color:#9CA3AF;font-size:11px;">Proses ini memerlukan beberapa saat. Harap tunggu...</em>'
        );
        rpbSetItemStatus(idx, 'reviewing');

        rpbFetchReviewChunk(bab, true, function(res) {
            if (res.status === 'require_warning') {
                rpbSetItemStatus(idx, null);
                Swal.fire({
                    title: 'Perhatian',
                    html: '<div style="font-size:14px; text-align:left;">' + res.msg + '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-arrow-right"></i> Tetap Lanjutkan Review AI',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal / Tunggu Pendamping',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        rpbShowState('loading');
                        rpbSetItemStatus(idx, 'reviewing');
                        rpbFetchReviewChunk(bab, true, function(res2) {
                            if (res2.status === true || res2.status === undefined || res2.status) {
                                rpbState.currentReviews[bab.id] = res2.review;
                                bab.has_review        = true;
                                bab.ai_review_summary = res2.summary;
                                rpbUpdateSummaries(bab, res2.summary);
                                rpbSetItemStatus(idx, 'reviewed');
                                rpbUpdateProgressBar();
                                rpbDisplayReview(bab, res2.review, false);
                                showPremiumToast('success', 'Review Ulang Selesai', bab.bab_label + ' berhasil dianalisis ulang.');
                            } else {
                                rpbSetItemStatus(idx, null);
                                rpbShowError(res2.msg);
                                showPremiumToast('warning', 'Gagal', res2.msg);
                            }
                        }, prevSummaries, 1);
                    } else {
                        rpbShowError('Review dibatalkan sementara menunggu Dosen Pendamping.');
                    }
                });
                return;
            }

            if (res.status === true || res.status === undefined || res.status) {
                rpbState.currentReviews[bab.id] = res.review;
                bab.has_review        = true;
                bab.ai_review_summary = res.summary;
                rpbUpdateSummaries(bab, res.summary);
                rpbSetItemStatus(idx, 'reviewed');
                rpbUpdateProgressBar();
                rpbDisplayReview(bab, res.review, false);
                showPremiumToast('success', 'Review Ulang Selesai', bab.bab_label + ' berhasil dianalisis ulang.');
            } else {
                rpbSetItemStatus(idx, null);
                rpbShowError(res.msg);
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        }, prevSummaries);
    };

    // =========================================================
    // RESET SEMUA REVIEW
    // =========================================================
    window.rpbResetAllReviews = function() {
        if (!confirm('Reset semua hasil Review Per Bab?\n\nSemua review yang tersimpan di database akan dihapus dan harus di-generate ulang.')) return;

        // Hapus dari database dengan update langsung (tidak ada endpoint khusus, kita reset per-chunk via regenerate flag)
        // Karena tidak ada endpoint delete batch, kita update state saja dan biarkan user generate ulang
        rpbState.summaries    = [];
        rpbState.currentReviews = {};
        rpbState.currentBabIndex = null;

        // Reset state babs
        rpbState.babs.forEach(function(b) {
            b.has_review = false;
            b.ai_review_summary = null;
        });

        rpbShowState('initial');
        $('#rpb_footer_actions').hide();
        rpbRenderSidebar();
        rpbSetProgressBar(0);
        $('#rpb_progress_info').text('Semua review direset. Klik "Review Semua Bab" untuk mulai ulang.');
        $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Review Semua Bab (Sequential)').prop('disabled', false);
        showPremiumToast('info', 'Reset Berhasil', 'Semua status review direset. Data di database tidak dihapus — generate ulang untuk memperbarui.');
    };

    // =========================================================
    // TOGGLE SHARE REVIEW KE MAHASISWA
    // =========================================================
    window.rpbToggleShareReview = function(isChecked) {
        if (!rpbState.babs.length) return;
        var id_log = rpbState.babs[0].id_log;
        
        // Simpan ke database via AJAX
        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_toggle_share_review') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                id_log: id_log,
                is_visible: isChecked ? 1 : 0,
                [rpbState.csrfToken]: rpbState.csrfHash
            },
            success: function(res) {
                if (res.status) {
                    if (isChecked) {
                        showPremiumToast('success', 'Berhasil Dibagikan', 'Hasil Review kini dapat dilihat oleh mahasiswa.');
                        
                        // Tambahkan pesan otomatis ke catatan dosen
                        var $textarea = $('textarea[name="catatan_dosen_wali"]');
                        if ($textarea.length && $textarea.is(':visible')) {
                            var cur = $textarea.val();
                            var msg = "Saya telah membagikan hasil review terhadap naskah Anda, silakan cek menu bimbingan Anda.";
                            if (cur.indexOf(msg) === -1) {
                                $textarea.val(cur ? cur + '\n\n' + msg : msg);
                                setTimeout(function() {
                                    $('html, body').animate({ scrollTop: $textarea.offset().top - 100 }, 500);
                                }, 400);
                            }
                        }
                    } else {
                        showPremiumToast('info', 'Akses Dicabut', 'Hasil Review disembunyikan dari mahasiswa.');

                        // Hapus pesan otomatis dari catatan dosen jika di-uncheck
                        var $textarea = $('textarea[name="catatan_dosen_wali"]');
                        if ($textarea.length && $textarea.is(':visible')) {
                            var cur = $textarea.val();
                            var msg = "Saya telah membagikan hasil review terhadap naskah Anda, silakan cek menu bimbingan Anda.";
                            if (cur.indexOf(msg) !== -1) {
                                cur = cur.replace('\n\n' + msg, '').replace(msg + '\n\n', '').replace(msg, '');
                                $textarea.val(cur);
                            }
                        }
                    }
                } else {
                    // Revert checkbox if failed
                    $('#rpb_toggle_share').prop('checked', !isChecked);
                    showPremiumToast('warning', 'Gagal', res.msg);
                }
            },
            error: function() {
                $('#rpb_toggle_share').prop('checked', !isChecked);
                showPremiumToast('warning', 'Error', 'Gagal menghubungi server.');
            }
        });
    };

    // =========================================================
    // HELPERS
    // =========================================================
    function rpbShowState(state) {
        $('#rpb_initial_state').hide();
        $('#rpb_loading_state').hide();
        $('#rpb_result_content').hide();

        if (state === 'initial') {
            $('#rpb_initial_state').css('display', 'flex');
            $('#rpb_close_btn').show();
        } else if (state === 'loading') {
            $('#rpb_loading_state').css('display', 'flex');
            $('#rpb_close_btn').hide();
        } else if (state === 'result') {
            $('#rpb_result_content').show();
            $('#rpb_close_btn').show();
        }
    }

    function rpbShowError(msg) {
        rpbShowState('initial');
        $('#rpb_initial_state').html(
            '<div style="font-size:40px;margin-bottom:16px;">⚠️</div>' +
            '<div style="font-size:15px;font-weight:700;color:#EF4444;margin-bottom:8px;">Terjadi Kesalahan</div>' +
            '<div style="font-size:13px;color:#6B7280;line-height:1.7;max-width:400px;">' + msg + '</div>'
        );
    }

    function rpbSetItemStatus(idx, status) {
        var $item = $('#rpb_bab_item_' + idx);
        $item.removeClass('reviewed reviewing');
        $item.find('.rpb-bab-icon').html(rpbState.babs[idx] ? rpbState.babs[idx].bab_number : idx + 1);

        if (status === 'reviewed') {
            $item.addClass('reviewed');
            $item.find('.rpb-bab-icon').html('<i class="fa fa-check-circle"></i>');
            $item.find('.rpb-bab-status').html('<span style="color:#6EE7B7;font-size:10px;">✓ Done</span>');
        } else if (status === 'reviewing') {
            $item.addClass('reviewing');
            $item.find('.rpb-bab-icon').html('<i class="fa fa-spinner fa-spin" style="font-size:11px;"></i>');
            $item.find('.rpb-bab-status').html('<span style="color:#A5B4FC;font-size:10px;">⚡ AI...</span>');
        }
    }

    function rpbUpdateSummaries(bab, summary) {
        // Upsert summary di array
        var existing = rpbState.summaries.findIndex(function(s) { return s.bab_label === bab.bab_label; });
        var obj = { bab_label: bab.bab_label, bab_title: bab.bab_title, summary: summary };
        if (existing !== -1) {
            rpbState.summaries[existing] = obj;
        } else {
            rpbState.summaries.push(obj);
        }
    }

    function rpbSetProgressBar(pct) {
        $('#rpb_progress_bar').css('width', Math.min(100, Math.max(0, pct)) + '%');
    }

    function rpbUpdateProgressBar() {
        var total = rpbState.babs.length;
        if (total === 0) { rpbSetProgressBar(0); return; }
        var done = rpbState.babs.filter(function(b) { return b.has_review || rpbState.currentReviews[b.id]; }).length;
        rpbSetProgressBar((done / total) * 100);
        if (!rpbState.isRunningAll) {
            $('#rpb_progress_info').text(done + ' dari ' + total + ' bab sudah direview.');
        }
    }

    // =========================================================
    // EDIT REVIEW
    // =========================================================
    window.rpbEditReview = function() {
        var bab = rpbState.babs[rpbState.currentBabIndex];
        if (!bab) return;
        var text = rpbState.currentReviews[bab.id] || '';
        
        $('#rpb_result_content').hide();
        $('#rpb_edit_textarea').val(text).show();
        
        $('#rpb_btn_regen').hide();
        $('#rpb_btn_edit').hide();
        $('#rpb_btn_save_edit').show();
        $('#rpb_btn_cancel_edit').show();
    };

    window.rpbCancelEditReview = function() {
        $('#rpb_edit_textarea').hide();
        $('#rpb_result_content').show();
        
        $('#rpb_btn_save_edit').hide();
        $('#rpb_btn_cancel_edit').hide();
        $('#rpb_btn_regen').show();
        $('#rpb_btn_edit').show();
    };

    window.rpbSaveEditReview = function() {
        var bab = rpbState.babs[rpbState.currentBabIndex];
        if (!bab) return;
        var newText = $('#rpb_edit_textarea').val();
        
        $('#rpb_btn_save_edit').html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
        $('#rpb_btn_cancel_edit').prop('disabled', true);

        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_save_ai_review') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                id_chunk: bab.id,
                edited_text: newText,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                $('#rpb_btn_save_edit').html('<i class="fa fa-save"></i> Simpan Edit').prop('disabled', false);
                $('#rpb_btn_cancel_edit').prop('disabled', false);

                if (res.status) {
                    rpbState.currentReviews[bab.id] = newText;
                    
                    // Render ulang hasil dengan markdown terbaru
                    rpbCancelEditReview(); // kembali ke view mode
                    
                    // Gunakan markdownToHtml agar tabel kustom kita tetap dirender
                    var htmlContent = typeof markdownToHtml === 'function' ? markdownToHtml(newText) : newText;
                    $('#rpb_result_content').html('<div style="max-width:860px;"><div class="rpb-review-text" style="font-size:13.5px;line-height:1.8;color:#374151;">' + htmlContent + '</div><textarea class="rpb-raw-text" style="display:none;">' + $('<div>').text(newText).html() + '</textarea></div>');

                    
                    alert('Berhasil menyimpan perubahan review.');
                } else {
                    alert(res.msg);
                }
            },
            error: function() {
                $('#rpb_btn_save_edit').html('<i class="fa fa-save"></i> Simpan Edit').prop('disabled', false);
                $('#rpb_btn_cancel_edit').prop('disabled', false);
                alert('Gagal menghubungi server.');
            }
        });
    };

})(); // end IIFE
</script>
