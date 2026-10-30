<script type="text/javascript">
$(document).ready(function() {
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
        var existingContent = $('#aiReviewContent_' + id_log).html();
        if (!existingContent || existingContent.trim() === '') {
            // Check the visible container if it's already rendered in PHP
            var visibleContent = $('#aiReviewContainer_' + id_log + ' .ai-review-content').html();
            if (visibleContent && visibleContent.trim() !== '') {
                existingContent = visibleContent;
            }
        }
        
        if (existingContent && existingContent.trim() !== '') {
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

    var lines = escaped.split('\n');
    var html = '';
    var inList = false;

    lines.forEach(function(line) {
        var trimmed = line.trim();

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
            html += '<p style="margin: 3px 0;"><strong style="color:#374151;">' + trimmed.match(/^(\d+\.)/)[1] + '</strong> ' + numContent + '</p>';

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

function generateAiReview(id_log, id_judul_skripsi) {
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
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        },
        success: function(res) {
            $btn.html('<i class="fa fa-magic"></i> Minta AI Review');
            $btn.attr('disabled', false);
            
            if (res.status) {
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
            } else {
                showPremiumToast('warning', 'Gagal', res.msg);
            }
        },
        error: function() {
            $btn.html('<i class="fa fa-magic"></i> Minta AI Review');
            $btn.attr('disabled', false);
            showPremiumToast('warning', 'Error', 'Terjadi kesalahan sistem saat menghubungi AI.');
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

</script>
