<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Review</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/favicon.png'); ?>" />
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/theme/assets/global/plugins/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/theme/assets/global/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <script src="<?= base_url('assets/theme/assets/global/plugins/jquery.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap/js/bootstrap.min.js') ?>" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            background: #F3F4F6;
        }
        .sbs-container {
            display: flex;
            flex-direction: row-reverse;
            height: 100vh;
            width: 100vw;
        }
        .sbs-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .sbs-right {
            width: 50%; /* Review panel takes exactly half */
            display: flex;
            flex-direction: column;
            background: #fff;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
            border-right: 1px solid #E5E7EB;
            z-index: 10;
        }
        .sbs-header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
            flex-shrink: 0;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        /* Scoped styles from detail view */
        @keyframes rpb-spin {
            to { transform: rotate(360deg); }
        }
        .rpb-bab-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            margin-bottom: 4px;
        }
        .rpb-bab-item:hover {
            background: rgba(255,255,255,0.05);
        }
        .rpb-bab-item.active {
            background: rgba(79, 70, 229, 0.2);
            border-left: 3px solid #818CF8;
        }
        .rpb-bab-item.active .bab-title {
            color: #fff !important;
            font-weight: 700 !important;
        }
        
        /* Markdown styles */
        #rpb_result_content { font-size: 13.5px; line-height: 1.7; color: #000; }
        #rpb_result_content h1, #rpb_result_content h2, #rpb_result_content h3 { color: #111827; margin-top: 24px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; }
        #rpb_result_content h1 { font-size: 22px; border-bottom: 2px solid #E5E7EB; padding-bottom: 8px; }
        #rpb_result_content h2 { font-size: 18px; color: #4F46E5; }
        #rpb_result_content h3 { font-size: 15px; }
        #rpb_result_content p { margin-bottom: 16px; }
        #rpb_result_content ul, #rpb_result_content ol { margin-bottom: 16px; padding-left: 20px; }
        #rpb_result_content li { margin-bottom: 6px; }
        #rpb_result_content blockquote { border-left: 4px solid #818CF8; background: #EEF2FF; padding: 12px 16px; margin: 0 0 16px 0; color: #4338CA; font-style: italic; border-radius: 0 8px 8px 0; }
        #rpb_result_content code { background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 12px; color: #DB2777; }
        #rpb_result_content pre { background: #1E293B; color: #F8FAFC; padding: 16px; border-radius: 8px; overflow-x: auto; margin-bottom: 16px; }
        #rpb_result_content pre code { background: transparent; padding: 0; color: inherit; }
        #rpb_result_content strong { color: #111827; font-weight: 700; }
        #rpb_result_content table { width: 100%; margin-bottom: 16px; border-collapse: collapse; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        #rpb_result_content th, #rpb_result_content td { padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-size: 13px; }
        #rpb_result_content th { background: #F3F4F6; font-weight: 700; color: #000; text-transform: uppercase; letter-spacing: 0.5px; }
        #rpb_result_content tr:nth-child(even) { background: #F9FAFB; }
    </style>
</head>
<body>

<div class="sbs-container">
    <!-- Panel Kiri: Dokumen -->
    <div class="sbs-left">
        <div class="sbs-header" style="background: #1E1E2E;">
            <h4 style="margin: 0; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-google" style="color: #60A5FA;"></i> Dokumen Mahasiswa
            </h4>
            <a href="<?= htmlspecialchars($link_preview) ?>" target="_blank" class="btn btn-xs btn-default" style="color: #1E1E2E; background: #fff; font-weight: 700; border-radius: 4px;">
                <i class="fa fa-external-link"></i> Buka di Tab Baru
            </a>
        </div>
        <div style="flex: 1; overflow: hidden; position: relative;">
            <iframe src="<?= htmlspecialchars($link_preview) ?>" title="Google Drive Document"></iframe>
        </div>
    </div>

    <!-- Panel Kanan: AI Review -->
    <div class="sbs-right">
        <div class="sbs-header">
            <h4 style="font-weight: 800; font-size: 15px; margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 8px; font-size: 14px;">🤖</span>
                <div>
                    <div>Review Per Bab</div>
                    <div id="rpb_subtitle" style="font-size: 13px; font-weight: 500; opacity: 0.9; margin-top: 2px; white-space: normal; line-height: 1.4;">Memuat data...</div>
                </div>
            </h4>
            <button onclick="window.close()" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: #fff; border: none; border-radius: 6px;">
                <i class="fa fa-times"></i> Tutup
            </button>
        </div>

        <!-- Body Review -->
        <div style="padding: 0; background: #F3F4F6; flex: 1; overflow: hidden; display: flex; gap: 0;">
            <!-- Sidebar Bab -->
            <div id="rpb_sidebar" style="width: 240px; flex-shrink: 0; background: #1E1E2E; display: flex; flex-direction: column; overflow: hidden;">
                
                <!-- Info Mahasiswa -->
                <div style="padding: 15px 12px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 12px;">
                    <img src="<?= htmlspecialchars($mhs_foto) ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #4F46E5;">
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; font-weight: 700; color: #E5E7EB; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($mhs_nama) ?></div>
                        <div style="font-size: 10px; color: #9CA3AF; margin-top: 2px;"><?= htmlspecialchars($mhs_nipd) ?></div>
                    </div>
                </div>

                <div style="padding: 12px 10px 8px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <div style="font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;">Daftar Bab Naskah</div>
                    <div id="rpb_info_log" style="font-size: 9px; color: #6B7280; line-height: 1.4;"></div>
                </div>
                <div id="rpb_bab_list" style="flex: 1; overflow-y: auto; padding: 8px 6px;">
                    <!-- Loading -->
                </div>
            </div>

            <!-- Konten Review -->
            <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #fff;">
                <div id="rpb_status_bar" style="background: #F8FAFC; border-bottom: 1px solid #E5E7EB; padding: 8px 15px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-shrink: 0; min-height: 40px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                        <div id="rpb_progress_info" style="font-size: 11px; color: #6B7280; font-weight: 500;">Pilih bab dari sidebar.</div>
                        <div id="rpb_chain_badge" style="display: none; font-size: 9px; font-weight: 700; padding: 3px 8px; border-radius: 20px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE;">
                            🔗 Review Berkesinambungan
                        </div>
                    </div>
                    <div id="rpb_font_settings" style="display: none; align-items: center; gap: 6px;">
                        <span style="font-size: 10px; font-weight: 600; color: #6B7280; margin-right: 4px;">Ukuran Teks:</span>
                        <button onclick="rpbChangeFontSize(-1)" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 2px 6px; border: 1px solid #D1D5DB; background: #fff;" title="Perkecil Teks"><i class="fa fa-minus" style="font-size: 10px; color: #6B7280;"></i></button>
                        <button onclick="rpbChangeFontSize(1)" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 2px 6px; border: 1px solid #D1D5DB; background: #fff;" title="Perbesar Teks"><i class="fa fa-plus" style="font-size: 10px; color: #6B7280;"></i></button>
                    </div>
                </div>
                
                <div id="rpb_overall_progress" style="height: 3px; background: #E5E7EB; flex-shrink: 0;">
                    <div id="rpb_progress_bar" style="height: 100%; background: linear-gradient(90deg, #4F46E5, #7C3AED); width: 0%; transition: width 0.5s ease;"></div>
                </div>
                
                <div id="rpb_review_area" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column;">
                    <div id="rpb_initial_state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; color: #9CA3AF; padding: 20px;">
                        <div style="font-size: 40px; margin-bottom: 15px;">📚</div>
                        <div style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 8px;">Ini adalah halaman Side-by-Side Review</div>
                        <div style="font-size: 12px; line-height: 1.6; max-width: 350px;">
                            Pilih bab dari sidebar untuk melihat hasil review.<br><br>
                            <span style="color:#EF4444; font-weight: 600;">Jangan lupa untuk menutup halaman ini jika sudah selesai.</span>
                        </div>
                    </div>
                    <div id="rpb_loading_state" style="display: none; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; padding: 20px;">
                        <div class="rpb-spinner" style="width: 40px; height: 40px; border: 3px solid #E5E7EB; border-top: 3px solid #4F46E5; border-radius: 50%; animation: rpb-spin 1s linear infinite; margin: 0 auto 15px;"></div>
                        <div id="rpb_loading_title" style="font-size: 14px; font-weight: 700; color: #374151; margin-bottom: 6px;">AI Sedang Membaca & Menganalisis...</div>
                        <div id="rpb_loading_desc" style="font-size: 11px; color: #6B7280; line-height: 1.6; max-width: 350px;"></div>
                    </div>
                    <div id="rpb_result_content" style="display: none;"></div>
                    <textarea id="rpb_edit_textarea" style="display:none; flex: 1; width:100%; min-height:300px; padding:15px; border-radius:8px; border:1px solid #D1D5DB; font-family:monospace; font-size:12px; line-height:1.6; resize:none; box-sizing: border-box;" placeholder="Teks markdown review..."></textarea>
                </div>
                
                <div id="rpb_footer_actions" style="display: none; border-top: 1px solid #E5E7EB; padding: 10px 15px; background: #F8FAFC; display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-shrink: 0; flex-wrap: wrap;">
                    <div id="rpb_current_bab_info" style="font-size: 11px; color: #6B7280; font-weight: 500;"></div>
                    <div style="display: flex; gap: 6px;">
                        <button id="rpb_btn_edit" onclick="rpbEditReview()" class="btn btn-sm btn-default" style="font-weight: 600; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button id="rpb_btn_save_edit" onclick="rpbSaveEditReview()" class="btn btn-sm btn-success" style="display:none; font-weight: 700; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <button id="rpb_btn_cancel_edit" onclick="rpbCancelEditReview()" class="btn btn-sm btn-danger" style="display:none; font-weight: 600; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                            Batal
                        </button>
                        <div style="display: flex; align-items: center; gap: 4px; background: #EEF2FF; padding: 4px 8px; border-radius: 6px; border: 1px solid #C7D2FE;">
                            <input type="checkbox" id="rpb_toggle_share" onchange="rpbToggleShareReview(this.checked)" style="cursor: pointer; width: 14px; height: 14px; margin:0;">
                            <label for="rpb_toggle_share" style="cursor: pointer; font-weight: 700; font-size: 10px; color: #4338CA; margin: 0; user-select: none;">
                                Bagikan
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // State management for AI Review (Adapted for SBS)
    var rpbState = {
        id_log: "<?= $log->id_log ?>",
        id_judul_skripsi: "<?= $id_judul_skripsi ?>",
        babs: [], 
        summaries: [],
        currentReviews: {}, 
        currentBabIndex: -1,
        isRunningAll: false,
        stopRequested: false,
        is_ai_review_visible: 0,
        ajaxUrl: {
            reviewChunk : '<?= site_url("E_skripsi_bimbingan/ajax_review_bab_chunk") ?>',
        },
        csrfToken: '<?= $this->security->get_csrf_token_name() ?>',
        csrfHash: '<?= $this->security->get_csrf_hash() ?>'
    };

    function showPremiumToast(type, title, message) {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000",
        };
        toastr[type](message, title);
    }

    // Include the exact logic from detail view but slightly adapted since there is no modal.
    $(document).ready(function() {
        rpbInit();
    });

    function rpbInit() {
        $('#rpb_subtitle').html('<i class="fa fa-spinner fa-spin"></i> Sedang memuat daftar bab...');
        
        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_get_chunks_bab') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                id_judul_skripsi: rpbState.id_judul_skripsi,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    rpbState.babs = res.babs;
                    rpbState.id_log = res.id_log;
                    rpbState.is_ai_review_visible = res.is_ai_review_visible;
                    
                    $('#rpb_toggle_share').prop('checked', res.is_ai_review_visible == 1);
                    
                    $('#rpb_subtitle').text(res.judul_ta);
                    
                    var logInfo = "Dari upload log #" + rpbState.id_log + "<br>" + res.babs.length + " bab terdeteksi";
                    $('#rpb_info_log').html(logInfo);
                    
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
                    rpbUpdateProgress();

                    if (reviewedCount > 0) {
                        $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Lanjutkan Review (' + reviewedCount + '/' + res.babs.length + ')');
                    } else {
                        $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Review Semua Bab (Sequential)');
                    }
                    
                    if (!hasMissing) {
                        $('#rpb_btn_review_all').hide();
                    } else {
                        $('#rpb_btn_review_all').show();
                    }

                    rpbShowState('initial');
                } else {
                    $('#rpb_subtitle').text("Gagal memuat atau tidak ada bab");
                    $('#rpb_bab_list').html('<div style="padding:20px; text-align:center; color:#EF4444; font-size:12px;">' + res.msg + '</div>');
                }
            },
            error: function() {
                $('#rpb_subtitle').text("Error jaringan");
            }
        });
    }

    function rpbRenderSidebar() {
        var html = '';
        for (var i = 0; i < rpbState.babs.length; i++) {
            var bab = rpbState.babs[i];
            var isActive = (i === rpbState.currentBabIndex) ? 'active' : '';
            var statusIcon = '';
            var statusColor = '#6B7280';
            
            if (bab.has_review) {
                statusIcon = '<i class="fa fa-check" style="color:#10B981; font-size:12px;"></i>';
            } else if (i === rpbState.currentBabIndex && rpbState.isRunningAll) {
                statusIcon = '<i class="fa fa-spinner fa-spin" style="color:#60A5FA; font-size:12px;"></i>';
                statusColor = '#60A5FA';
            }
            
            html += '<div class="rpb-bab-item ' + isActive + '" onclick="rpbSelectBab(' + i + ')">';
            html += '   <div style="width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">';
            
            if (bab.has_review) {
                html += '<i class="fa fa-check-circle" style="color:#10B981; font-size:14px;"></i>';
            } else {
                html += '<i class="fa fa-file-text-o" style="color:' + statusColor + ';"></i>';
            }
            
            html += '   </div>';
            html += '   <div style="flex:1; overflow:hidden;">';
            html += '       <div class="bab-title" style="font-size:11px; font-weight:600; color:#D1D5DB; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + bab.bab_label + '</div>';
            html += '       <div style="font-size:9px; color:#6B7280; text-transform:uppercase; margin-top:2px;">' + bab.bab_title + '</div>';
            html += '   </div>';
            html += '   <div style="flex-shrink:0;">' + statusIcon + '</div>';
            html += '</div>';
        }
        $('#rpb_bab_list').html(html);
    }

    function rpbUpdateProgress() {
        var reviewedCount = 0;
        for (var i = 0; i < rpbState.babs.length; i++) {
            if (rpbState.babs[i].has_review) reviewedCount++;
        }
        var percent = Math.round((reviewedCount / rpbState.babs.length) * 100);
        $('#rpb_progress_bar').css('width', percent + '%');
    }

    function rpbShowState(state) {
        $('#rpb_initial_state, #rpb_loading_state, #rpb_result_content, #rpb_edit_textarea').hide();
        $('#rpb_btn_regen, #rpb_btn_edit, #rpb_btn_save_edit, #rpb_btn_cancel_edit').hide();
        
        if (state === 'initial') {
            $('#rpb_initial_state').css('display', 'flex');
        } else if (state === 'loading') {
            $('#rpb_loading_state').css('display', 'flex');
        } else if (state === 'result') {
            $('#rpb_result_content').show();
        }
    }

    function rpbSelectBab(idx) {
        if (rpbState.isRunningAll) return; // block klik saat auto-run

        rpbState.currentBabIndex = idx;
        rpbRenderSidebar();
        $('#rpb_review_area').scrollTop(0);
        
        var bab = rpbState.babs[idx];
        
        if (bab.has_review) {
            // Sudah ada review
            if (rpbState.currentReviews[bab.id]) {
                rpbDisplayReview(bab, rpbState.currentReviews[bab.id], true);
            } else {
                // Fetch from DB if not loaded
                $.ajax({
                    url: "<?= site_url('E_skripsi_bimbingan/ajax_get_bab_review/') ?>" + bab.id,
                    type: "GET",
                    success: function(res) {
                        rpbState.currentReviews[bab.id] = res.review;
                        rpbDisplayReview(bab, res.review, true);
                    }
                });
            }
        } else {
            // Belum direview, mulai generate otomatis untuk bab ini
            rpbGenerateReview(idx);
        }
    }

    function rpbDisplayReview(bab, reviewText, isCached) {
        $('#rpb_review_area').scrollTop(0);
        $('#rpb_status_bar').css('background', '#F0FDF4');
        $('#rpb_progress_info').html('<i class="fa fa-check-circle" style="color:#10B981;"></i> Menampilkan hasil review <strong>' + bab.bab_label + '</strong>');
        $('#rpb_chain_badge').hide();
        $('#rpb_font_settings').css('display', 'flex');
        
        $('#rpb_result_content').html(markdownToHtml(reviewText));
        rpbShowState('result');
        
        $('#rpb_footer_actions').css('display', 'flex');
        $('#rpb_current_bab_info').text(bab.bab_label + ' — ' + bab.bab_title);
        $('#rpb_btn_edit').show();
        
        // Sync toggle share (using global state)
        $('#rpb_toggle_share').prop('checked', rpbState.is_ai_review_visible == 1);
    }

    function rpbUpdateSummaries(bab, newSummaryText) {
        var existing = rpbState.summaries.find(function(s) { return s.bab_label === bab.bab_label; });
        if (existing) {
            existing.summary = newSummaryText;
        } else {
            rpbState.summaries.push({
                bab_label: bab.bab_label,
                bab_title: bab.bab_title,
                summary  : newSummaryText
            });
            // Re-sort summaries to match babs order
            rpbState.summaries.sort(function(a, b) {
                return rpbState.babs.findIndex(function(x) { return x.bab_label === a.bab_label; }) - 
                       rpbState.babs.findIndex(function(x) { return x.bab_label === b.bab_label; });
            });
        }
    }

    function rpbGenerateReview(idx, force) {
        var bab = rpbState.babs[idx];
        rpbState.currentBabIndex = idx;
        
        // Bangun previous summaries dari bab SEBELUM bab ini
        var prevSummaries = rpbState.summaries.filter(function(s) {
            return rpbState.babs.findIndex(function(b) {
                return b.bab_label === s.bab_label;
            }) < idx;
        });

        rpbRenderSidebar();
        
        rpbShowState('loading');
        $('#rpb_status_bar').css('background', '#EEF2FF');
        $('#rpb_progress_info').html('<i class="fa fa-spinner fa-spin" style="color:#4F46E5;"></i> Sedang membaca <strong>' + bab.bab_label + '</strong>...');
        $('#rpb_loading_title').text('AI Sedang Menganalisis ' + bab.bab_label);
        
        if (prevSummaries.length > 0) {
            $('#rpb_loading_desc').html('Mengkorelasikan konteks dari ' + prevSummaries.length + ' bab sebelumnya dengan isi bab ini...');
            $('#rpb_chain_badge').css('display', 'inline-block');
        } else {
            $('#rpb_loading_desc').html('Membaca isi naskah ' + bab.bab_label + ' dan merumuskan saran perbaikan...');
            $('#rpb_chain_badge').hide();
        }

        rpbFetchReviewChunk(bab, force, function(res) {
            if (res.status === true || res.status === undefined || res.status) {
                bab.has_review = true;
                bab.ai_review_summary = res.summary;
                rpbState.currentReviews[bab.id] = res.review;
                rpbUpdateSummaries(bab, res.summary);
                
                rpbUpdateProgress();
                rpbRenderSidebar();
                
                if (rpbState.isRunningAll) {
                    rpbDisplayReview(bab, res.review, false);
                    
                    // Lanjut ke bab berikutnya
                    var nextIdx = idx + 1;
                    if (nextIdx < rpbState.babs.length) {
                        if (rpbState.stopRequested) {
                            rpbStopRunning();
                            showPremiumToast('info', 'Dihentikan', 'Proses review sequential dihentikan.');
                        } else {
                            setTimeout(function() {
                                rpbGenerateReview(nextIdx, false);
                            }, 1500); // jeda sedikit agar UX mulus
                        }
                    } else {
                        rpbStopRunning();
                        showPremiumToast('success', 'Selesai!', 'Semua bab berhasil direview.');
                        $('#rpb_btn_review_all').hide();
                    }
                } else {
                    rpbDisplayReview(bab, res.review, false);
                    showPremiumToast('success', 'Berhasil', 'Review ' + bab.bab_label + ' selesai dibuat.');
                }
            } else {
                rpbStopRunning();
                rpbShowState('initial');
                Swal.fire({
                    icon: 'error',
                    title: 'AI Gagal Memproses',
                    text: res.msg || 'Terjadi kesalahan saat memanggil AI untuk bab ini.'
                });
            }
        }, prevSummaries);
    }

    function rpbFetchReviewChunk(bab, forceRegenerate, callback, previousSummaries) {
        var summariesToSend = previousSummaries || [];
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

    function rpbStartReviewAll() {
        if (rpbState.isRunningAll) {
            rpbState.stopRequested = true;
            $('#rpb_btn_review_all').html('<i class="fa fa-spinner fa-spin"></i> Menghentikan...');
            return;
        }

        rpbState.isRunningAll = true;
        rpbState.stopRequested = false;
        $('#rpb_btn_review_all').html('<i class="fa fa-stop-circle"></i> Hentikan Proses').css('background', '#EF4444');
        
        // Cari bab pertama yang belum di-review
        var startIdx = -1;
        for (var i = 0; i < rpbState.babs.length; i++) {
            if (!rpbState.babs[i].has_review) {
                startIdx = i;
                break;
            }
        }
        
        if (startIdx !== -1) {
            rpbGenerateReview(startIdx, false);
        } else {
            rpbStopRunning();
            showPremiumToast('info', 'Sudah Lengkap', 'Semua bab sudah direview.');
        }
    }

    function rpbStopRunning() {
        rpbState.isRunningAll = false;
        rpbState.stopRequested = false;
        $('#rpb_btn_review_all').html('<i class="fa fa-magic"></i> Lanjutkan Review').css('background', 'linear-gradient(135deg, #4F46E5, #7C3AED)');
        
        var hasMissing = false;
        for (var i = 0; i < rpbState.babs.length; i++) {
            if (!rpbState.babs[i].has_review) hasMissing = true;
        }
        if (!hasMissing) $('#rpb_btn_review_all').hide();
    }

    function rpbRegenerateCurrentBab() {
        if (rpbState.currentBabIndex === -1) return;
        var bab = rpbState.babs[rpbState.currentBabIndex];
        
        Swal.fire({
            title: 'Generate Ulang ' + bab.bab_nama + '?',
            text: "Hasil review sebelumnya akan ditimpa. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Generate Ulang'
        }).then((result) => {
            if (result.isConfirmed) {
                rpbGenerateReview(rpbState.currentBabIndex, true);
            }
        });
    }

    function rpbResetAllReviews() {
        Swal.fire({
            title: 'Reset Semua Review?',
            text: "Tindakan ini akan menghapus permanen seluruh hasil AI review yang sudah ada pada skripsi ini. Apakah Anda yakin?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus Semua'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= site_url('E_skripsi_bimbingan/ajax_reset_all_chunk_reviews/') ?>" + rpbState.id_judul_skripsi,
                    type: "POST",
                    dataType: "JSON",
                    data: { '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>' },
                    success: function(res) {
                        if (res.status) {
                            showPremiumToast('success', 'Berhasil', 'Semua review berhasil direset.');
                            setTimeout(function(){
                                location.reload();
                            }, 1000);
                        } else {
                            Swal.fire('Gagal', res.msg, 'error');
                        }
                    }
                });
            }
        });
    }

    function rpbToggleShareReview(isChecked) {
        if (!rpbState.id_log) return;
        
        $.ajax({
            url: "<?= site_url('E_skripsi_bimbingan/ajax_toggle_share_review') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                id_log: rpbState.id_log,
                is_shared: isChecked ? 1 : 0,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    rpbState.is_ai_review_visible = isChecked ? 1 : 0;
                    if (isChecked) {
                        showPremiumToast('success', 'Dibagikan', 'Review naskah ini kini bisa dilihat oleh mahasiswa.');
                    } else {
                        showPremiumToast('info', 'Akses Dicabut', 'Review naskah ini disembunyikan dari mahasiswa.');
                    }
                } else {
                    $('#rpb_toggle_share').prop('checked', !isChecked); // revert
                    showPremiumToast('error', 'Gagal', 'Terjadi kesalahan saat menyimpan pengaturan.');
                }
            }
        });
    }

    function rpbEditReview() {
        var bab = rpbState.babs[rpbState.currentBabIndex];
        if (!bab) return;
        var text = rpbState.currentReviews[bab.id] || '';
        
        $('#rpb_result_content').hide();
        $('#rpb_edit_textarea').val(text).show();
        
        $('#rpb_btn_edit').hide();
        $('#rpb_btn_save_edit').show();
        $('#rpb_btn_cancel_edit').show();
        $('#rpb_status_bar').css('background', '#FFFBEB');
        $('#rpb_progress_info').html('<i class="fa fa-pencil" style="color:#D97706;"></i> Mode Edit: <strong>' + bab.bab_label + '</strong>');
    }

    function rpbCancelEditReview() {
        var bab = rpbState.babs[rpbState.currentBabIndex];
        $('#rpb_edit_textarea').hide();
        $('#rpb_result_content').show();
        
        $('#rpb_btn_edit').show();
        $('#rpb_btn_save_edit').hide();
        $('#rpb_btn_cancel_edit').hide();
        $('#rpb_status_bar').css('background', '#F0FDF4');
        $('#rpb_progress_info').html('<i class="fa fa-check-circle" style="color:#10B981;"></i> Menampilkan hasil review <strong>' + bab.bab_label + '</strong>');
    }

    function rpbSaveEditReview() {
        var bab = rpbState.babs[rpbState.currentBabIndex];
        if (!bab) return;
        
        var newText = $('#rpb_edit_textarea').val();
        $('#rpb_btn_save_edit').html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
        
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
                $('#rpb_btn_save_edit').html('<i class="fa fa-save"></i> Simpan Edit').attr('disabled', false);
                if (res.status) {
                    rpbState.currentReviews[bab.id] = newText;
                    rpbDisplayReview(bab, newText, true);
                    showPremiumToast('success', 'Berhasil', 'Perubahan pada review berhasil disimpan.');
                } else {
                    Swal.fire('Gagal Menyimpan', res.msg || 'Terjadi kesalahan', 'error');
                }
            },
            error: function() {
                $('#rpb_btn_save_edit').html('<i class="fa fa-save"></i> Simpan Edit').attr('disabled', false);
                Swal.fire('Gagal Menghubungi Server', 'Koneksi terputus', 'error');
            }
        });
    }

    // Custom Markdown Parser to Table format
    function markdownToHtml(text) {
        var escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var linesRaw = escaped.split('\n');
        var processedLines = [];
        var inTemuan = false;
        var currentAspek = '';
        var inTable = false;

        for (var i = 0; i < linesRaw.length; i++) {
            var lr = linesRaw[i].trim();

            if (lr.indexOf('### 🔍 Temuan Evaluasi') !== -1 || lr.indexOf('### ? Temuan Evaluasi') !== -1) {
                inTemuan = true;
                processedLines.push(lr.replace('?', '🔍'));
                continue;
            }

            if (inTemuan && (lr.indexOf('### ✅') !== -1 || lr.indexOf('### 📌') !== -1 || lr.indexOf('### 📋') !== -1 || lr.indexOf('### ? Kesimpulan') !== -1 || lr.indexOf('### ? Ringkasan') !== -1)) {
                inTemuan = false;
                if (inTable) { processedLines.push('</tbody></table></div>'); inTable = false; }
                processedLines.push(lr.replace('?', '✅'));
                continue;
            }

            if (inTemuan) {
                var aspekMatch = lr.match(/^\*\*(.*?)\*\*$/);
                if (aspekMatch) {
                    currentAspek = aspekMatch[1];
                    if (!inTable) {
                        processedLines.push('<div class="table-responsive" style="margin-top:15px; margin-bottom:15px;"><table class="table table-bordered table-hover" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05); width: 100%; border-collapse: collapse;">');
                        processedLines.push('<thead style="background:#f8fafc; color:#1e293b;"><tr><th style="width:22%; padding:10px; border:1px solid #E5E7EB;">Aspek</th><th style="padding:10px; border:1px solid #E5E7EB;">Detail Temuan & Saran</th></tr></thead>');
                        processedLines.push('<tbody>');
                        inTable = true;
                    }
                    continue;
                }

                var findingMatch = lr.match(/^-\s*(🔴|🟡|🟢|\?)\s*(.*)/);
                if (findingMatch) {
                    var icon = findingMatch[1] === '?' ? '🟡' : findingMatch[1];
                    var detail = findingMatch[2];
                    // Clean up bolding on Saran if any
                    detail = detail.replace(/\*\*\s*Saran:\s*\*\*/ig, 'Saran:');
                    
                    // Inject Kutipan label right after the bold title if followed by a quote
                    detail = detail.replace(/(\*\*.+?\*\*)\s*(?:—|-|💡|📝|💭|\?|:)?\s*(?=[*\"“'])/g, '$1<br><strong style="color:#000;">Kutipan:</strong> ');
                    
                    // Inject Saran label
                    detail = detail.replace(/(?:→|➡️|💡|📝|💭|\?)?\s*\**Saran:\**/ig, '<br><strong style="color:#059669;">Saran:</strong> ');
                    detail = applyInlineMarkdown(detail);

                    if (!inTable) {
                        processedLines.push('<div class="table-responsive" style="margin-top:15px; margin-bottom:15px;"><table class="table table-bordered table-hover" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05); width: 100%; border-collapse: collapse;">');
                        processedLines.push('<thead style="background:#f8fafc; color:#1e293b;"><tr><th style="width:22%; padding:10px; border:1px solid #E5E7EB;">Aspek</th><th style="padding:10px; border:1px solid #E5E7EB;">Detail Temuan & Saran</th></tr></thead>');
                        processedLines.push('<tbody>');
                        inTable = true;
                    }

                    processedLines.push('<tr>');
                    processedLines.push('<td style="font-weight:700; color:#000; vertical-align:top; padding:10px; border:1px solid #E5E7EB;"><div style="display:flex; align-items:flex-start; gap:8px;"><span style="font-size:18px; line-height:1;">' + icon + '</span><span style="padding-top:2px;">' + currentAspek + '</span></div></td>');
                    processedLines.push('<td style="line-height:1.6; vertical-align:top; padding:10px; border:1px solid #E5E7EB;">' + detail + '</td>');
                    processedLines.push('</tr>');
                    continue;
                }
                
                if (inTable && lr === '') continue;
                
                if (inTable && !findingMatch && !aspekMatch) {
                    if (lr.startsWith('- ')) {
                        processedLines.push('<tr><td style="border:1px solid #E5E7EB;"></td><td style="padding:10px; border:1px solid #E5E7EB;">' + applyInlineMarkdown(lr.replace(/^- /, '• ')) + '</td></tr>');
                        continue;
                    } else {
                        processedLines.push('</tbody></table></div>');
                        inTable = false;
                    }
                }
            }
            processedLines.push(lr);
        }

        if (inTable) processedLines.push('</tbody></table></div>');

        var lines = processedLines;
        var html = '';
        var inList = false;

        lines.forEach(function(line) {
            var trimmed = line.trim();
            if (/^<div|^<table|^<thead|^<tbody|^<tr|^<td|^<th|^<\/tbody|^<\/table|^<\/div/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += trimmed + '\n';
                return;
            }
            if (/^###\s+(.+)/.test(trimmed) || /^###\s*\?\s*(.+)/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<h5 style="margin: 14px 0 4px; font-weight: 700; color: #1e3a5f; font-size: 15px;">' + trimmed.replace(/^###\s*\??\s*/, '') + '</h5>';
            } else if (/^##\s+(.+)/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<h4 style="margin: 16px 0 4px; font-weight: 700; color: #1e3a5f; font-size: 18px;">' + trimmed.replace(/^##\s+/, '') + '</h4>';
            } else if (/^#\s+(.+)/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<h3 style="margin: 18px 0 4px; font-weight: 800; color: #1e3a5f; font-size: 22px;">' + trimmed.replace(/^#\s+/, '') + '</h3>';
            } else if (/^---+$/.test(trimmed) || /^\*\*\*+$/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<hr style="border: none; border-top: 1px solid #e5e7eb; margin: 10px 0;">';
            } else if (/^[-*+]\s+(.+)/.test(trimmed)) {
                if (!inList) { html += '<ul style="margin: 4px 0 4px 16px; padding-left: 20px;">'; inList = true; }
                html += '<li style="margin-bottom: 6px;">' + applyInlineMarkdown(trimmed.replace(/^[-*+]\s+/, '')) + '</li>';
            } else if (/^\d+\.\s+(.+)/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<p style="margin: 3px 0; margin-bottom: 6px;"><strong style="color:#000;">' + trimmed.match(/^(\d+\.)/)[1] + '</strong> ' + applyInlineMarkdown(trimmed.replace(/^\d+\.\s+/, '')) + '</p>';
            } else if (trimmed === '') {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<div style="height: 16px;"></div>';
            } else {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<p style="margin: 3px 0; margin-bottom: 16px;">' + applyInlineMarkdown(trimmed) + '</p>';
            }
        });
        if (inList) html += '</ul>';
        return html;
    }

    function applyInlineMarkdown(text) {
        text = text.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\*([^\s*][^*]*[^\s*]|[^\s*])\*/g, '<em>$1</em>');
        text = text.replace(/`([^`]+)`/g, '<code style="background:#f3f4f6;padding:1px 4px;border-radius:3px;font-size:92%;color:#DB2777;font-family:monospace;">$1</code>');
        return text;
    }

    var currentFontSize = 13;
    function rpbChangeFontSize(step) {
        currentFontSize += step;
        if (currentFontSize < 10) currentFontSize = 10;
        if (currentFontSize > 24) currentFontSize = 24;
        $('#rpb_result_content').css('font-size', currentFontSize + 'px');
        $('#rpb_result_content table, #rpb_result_content th, #rpb_result_content td').css('font-size', currentFontSize + 'px');
        $('#rpb_result_content h1, #rpb_result_content h2, #rpb_result_content h3, #rpb_result_content h4, #rpb_result_content h5').css('font-size', (currentFontSize + 3) + 'px');
    }
</script>

</body>
</html>
