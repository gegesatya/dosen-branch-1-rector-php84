<script>
$(document).ready(function() {
    
    // Eager load the catatan data silently so it doesn't overwrite later injections
    if (typeof edit_catatan === 'function') {
        var myIdNilaiSempro = '<?= isset($id_nilai_sempro) ? $id_nilai_sempro : "" ?>';
        if (myIdNilaiSempro !== '') {
            setTimeout(function() {
                edit_catatan(myIdNilaiSempro, false);
            }, 500);
        }
    }

    // Setup Dynamic Interests
    var prodi_clean = "<?= isset($skripsi->nm_lemb) ? strtolower(trim((string)$skripsi->nm_lemb)) : '' ?>";
    var interestsMap = {
        general: [
            "Pernah menguji paper/topik serupa",
            "Pernah terlibat proyek/praktik serupa",
            "Pakar Metodologi Penelitian"
        ],
        "manajemen pemasaran internasional": [
            "Digital Marketing", "Pemasaran Strategis", "Consumer Behavior", "Branding", "Export-Import"
        ],
        "akuntansi perpajakan": [
            "Audit Keuangan", "Perpajakan Korporat", "Akuntansi Manajemen", "Sistem Informasi Akuntansi", "Tax Planning"
        ],
        "pengelolaan konvensi dan acara": [
            "Event Management", "MICE", "Sponsorship & PR", "Event Marketing", "Logistik Acara"
        ],
        "pengelolaan agribisnis": [
            "Supply Chain Management", "Ekonomi Pertanian", "Pemasaran Agribisnis", "Manajemen Risiko Agribisnis", "Agroindustri"
        ],
        "bina wisata": [
            "Manajemen Destinasi", "Hospitality", "Ekowisata", "Tour Planning", "Pariwisata Berkelanjutan"
        ]
    };
    
    var optionsHtml = '';
    optionsHtml += '<optgroup label="General">';
    interestsMap.general.forEach(function(item) {
        optionsHtml += '<option value="'+item+'">'+item+'</option>';
    });
    optionsHtml += '</optgroup>';
    
    var matchedProdi = null;
    for(var key in interestsMap) {
        if(key !== 'general' && prodi_clean.indexOf(key) !== -1) {
            matchedProdi = key;
            break;
        }
    }
    
    if(matchedProdi) {
        optionsHtml += '<optgroup label="Topik Khusus Prodi">';
        interestsMap[matchedProdi].forEach(function(item) {
            optionsHtml += '<option value="'+item+'">'+item+'</option>';
        });
        optionsHtml += '</optgroup>';
    } else {
        // Fallback for general 'manajemen' or other unmapped
        if (prodi_clean.indexOf('manajemen') !== -1) {
             optionsHtml += '<optgroup label="Topik Khusus Prodi">';
             interestsMap["manajemen pemasaran internasional"].forEach(function(item) {
                 optionsHtml += '<option value="'+item+'">'+item+'</option>';
             });
             optionsHtml += '</optgroup>';
        }
    }
    
    $('#interest_penguji').html(optionsHtml);
    if($.fn.select2) {
        $('#interest_penguji').select2({
            placeholder: "Pilih interest...",
            maximumSelectionLength: 3,
            allowClear: true
        });
    }
    
    // Checkbox limitation (Max 3)
    function updateCheckboxStyling() {
        var checkedCount = $('.role-checkbox:checked').length;
        if (checkedCount >= maxRolesAllowed) {
            $('.role-checkbox:not(:checked)').prop('disabled', true).closest('.role-option').css({
                'opacity': '0.6',
                'cursor': 'not-allowed',
                'background': '#F9FAFB'
            });
        } else {
            $('.role-checkbox:not(:checked)').each(function() {
                if (!$(this).closest('.role-option').hasClass('locked')) {
                    $(this).prop('disabled', false).closest('.role-option').css({
                        'opacity': '1',
                        'cursor': 'pointer',
                        'background': '#ffffff'
                    });
                }
            });
        }
    }
    $('.role-checkbox').on('change', updateCheckboxStyling);
    
    // Document Preview Logic
    function getPreviewUrl(url) {
        if (!url) return null;
        var match = url.match(/(?:docs|drive)\.google\.com\/(?:.*?\/)?d\/([a-zA-Z0-9_-]+)/);
        if (match) {
            var docId = match[1];
            if (url.indexOf('drive.google.com') !== -1) {
                return 'https://drive.google.com/file/d/' + docId + '/preview';
            } else if (url.indexOf('/document/') !== -1) {
                return 'https://docs.google.com/document/d/' + docId + '/preview';
            } else if (url.indexOf('/presentation/') !== -1) {
                return 'https://docs.google.com/presentation/d/' + docId + '/preview';
            } else if (url.indexOf('/spreadsheets/') !== -1) {
                return 'https://docs.google.com/spreadsheets/d/' + docId + '/preview';
            }
            return url.replace(/\/(edit|view).*$/, '/preview');
        }
        return null;
    }
    
    // Clear iframe on modal close (if any other modals use it)
    $('#modal_preview_doc').on('hidden.bs.modal', function () {
        $('#preview_iframe').attr('src', '');
    });
    
        <?php if (!empty($my_ai_result)): ?>
        var savedData = <?= json_encode($my_ai_result) ?>;
        setTimeout(function() {
            renderResult(savedData);
            $('#ai-result-container').show();
        }, 50);
        updateCheckboxStyling();
    <?php else: ?>
        updateCheckboxStyling(); // Call it once initially
    <?php endif; ?>
    
    $('#form-ai-analyze').on('submit', function(e) {
        e.preventDefault();
        
        var checkedCount = $('.role-checkbox:checked').length;
        if (checkedCount === 0) {
            Swal.fire('Opps', 'Silakan pilih minimal 1 peran (Maks ' + maxRolesAllowed + ') untuk melanjutkan.', 'warning');
            return;
        }

        // Get selected roles for confirmation
        var selectedRoleNames = [];
        $('.role-checkbox:checked').each(function() {
            var roleText = $(this).closest('.role-option').find('.role-title').clone().children().remove().end().text().trim();
            selectedRoleNames.push(roleText);
        });
        
        var formData = $(this).serialize();
        var csrfParam = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        var dataString = formData + '&' + csrfParam + '=' + csrfHash;

        Swal.fire({
            title: 'Konfirmasi Peran',
            html: 'Apakah Anda yakin ingin mengambil peran pengujian berikut?<br><br><b>' + selectedRoleNames.join('<br>') + '</b>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Step 1: Check and Lock
                Swal.fire({
                    title: 'Memeriksa Ketersediaan...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "<?= site_url('E_skripsi_nilai_sempro/ajax_check_and_lock_roles') ?>",
                    type: "POST",
                    data: dataString,
                    dataType: "json",
                    success: function(lockRes) {
                        if (lockRes.status) {
                            // Proceed to analyze
                            Swal.close();
                            startAnalysis(dataString);
                        } else {
                            Swal.fire('Gagal', lockRes.msg, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memverifikasi peran. Coba lagi.', 'error');
                    }
                });
            }
        });
    });

    function startAnalysis(formData) {
        const $btn = $('#btn-generate');
        const originalText = $btn.html();
        
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Sedang Menganalisis...').prop('disabled', true);
        $('#ai-result-container').html('<div style="text-align:center; padding:40px;"><i class="fa fa-spinner fa-spin fa-3x" style="color:#4F46E5;"></i><p style="margin-top:16px; color:#4B5563;">AI sedang menganalisis dokumen proposal dan merumuskan daftar pertanyaan. Mohon tunggu beberapa saat...</p></div>').slideDown();

        $.ajax({
            url: "<?= site_url('E_skripsi_nilai_sempro/ajax_generate_ai_review') ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    renderResult(res.data);
                    Swal.fire('Berhasil', 'Analisis berhasil diselesaikan!', 'success');
                    
                    // Lock UI immediately without needing refresh
                    $btn.html('<i class="fa fa-check-circle"></i> Hasil Analisis AI Telah Disimpan')
                        .prop('disabled', true)
                        .css({
                            'background-color': '#10B981',
                            'cursor': 'not-allowed'
                        });
                    $('input[name="file_url"]').prop('readonly', true);
                    $('.role-checkbox').prop('disabled', true);
                    $('.role-option').addClass('locked').css({
                        'opacity': '0.6',
                        'cursor': 'not-allowed',
                        'background': '#F9FAFB'
                    });
                    
                } else {
                    $btn.html(originalText).prop('disabled', false);
                    $('#ai-result-container').html('<div class="alert alert-danger"><strong>Gagal:</strong> ' + (res.msg || "Terjadi kesalahan saat menghubungi AI.") + '</div>');
                    Swal.fire('Gagal', res.msg || "Terjadi kesalahan saat menghubungi AI.", 'error');
                    
                    if (res.msg && res.msg.indexOf('API Key') !== -1) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('setting_api_key') ?>';
                        }, 2000);
                    }
                }
            },
            error: function(xhr, status, error) {
                $btn.html(originalText).prop('disabled', false);
                $('#ai-result-container').html('<div class="alert alert-danger"><strong>Error:</strong> Koneksi gagal atau server timeout. Silakan coba lagi.</div>');
                Swal.fire('Error', 'Koneksi gagal atau server timeout. Silakan coba lagi.', 'error');
                console.error(error);
            }
        });
    }
    
    function renderResult(data) {
        if (!data) return;
        
        const $container = $('#ai-result-container');
        $container.empty();
        
        // Helper to format lists
        const formatList = (arr) => {
            if (!arr || !arr.length) return '<p class="result-text">Tidak ada catatan.</p>';
            let html = '<ul class="result-list">';
            arr.forEach(item => {
                html += `<li>${item}</li>`;
            });
            html += '</ul>';
            return html;
        };

        // Helper to format suggestions list with injection UI
        const formatSuggestionsList = (arr) => {
            if (!arr || !arr.length) return '<p class="result-text">Tidak ada catatan.</p>';
            let html = '<ul class="result-list" style="list-style: none; padding-left: 0;">';
            arr.forEach((item, index) => {
                
                // Determine pre-selected target based on dbCatatan
                let preSelectedTarget = 'catatan_judul'; // default
                let isAlreadyInDb = false;
                if (typeof dbCatatan !== 'undefined' && dbCatatan) {
                    for (const [key, val] of Object.entries(dbCatatan)) {
                        if (val && val.includes(item)) {
                            preSelectedTarget = key;
                            isAlreadyInDb = true;
                            break;
                        }
                    }
                }
                
                let buttonHtml = `<button type="button" class="btn btn-sm btn-inject-suggestion" style="background: #8B5CF6; color: #FFF; border: none; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 4px 12px;">
                            <i class="fa fa-arrow-right"></i> Masukkan
                        </button>`;
                let infoHtml = ``;
                
                const rubrikNames = {
                    'catatan_judul': 'A. Judul',
                    'catatan_latar_belakang': 'B. Latar Belakang',
                    'catatan_rumusan_masalah': 'C. Rumusan Masalah',
                    'catatan_tujuan_penelitian': 'D. Tujuan/Manfaat',
                    'catatan_tinjauan_pustaka': 'E. Tinjauan Pustaka',
                    'catatan_metode_penelitian': 'F. Metode Penelitian',
                    'catatan_daftar_pustaka': 'G. Daftar Pustaka',
                    'catatan_format_penulisan': 'H. Format Penulisan'
                };
                
                if (isAlreadyInDb) {
                    buttonHtml = `<button type="button" class="btn btn-sm btn-inject-suggestion" disabled style="background: #10B981; color: #FFF; border: none; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 4px 12px; cursor: not-allowed; opacity: 0.8;">
                            <i class="fa fa-check"></i> Disimpan
                        </button>
                        <button type="button" class="btn btn-sm btn-reset-suggestion" style="background: #EF4444; color: #FFF; border: none; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 4px 12px;" title="Hapus usulan ini">
                            <i class="fa fa-refresh"></i> Reset
                        </button>`;
                    infoHtml = `<div class="suggestion-info-text" style="width: 100%; font-size: 11px; color: #10B981; margin-top: 6px;"><i class="fa fa-info-circle"></i> Sudah diusulkan untuk revisi pada rubrik <strong>${rubrikNames[preSelectedTarget]}</strong></div>`;
                }
                
                const selectedAttr = (key) => key === preSelectedTarget ? 'selected' : '';

                html += `
                <li style="margin-bottom: 16px; padding: 16px; border: 1px solid #E5E7EB; border-radius: 8px; background: #F9FAFB;">
                    <div style="font-weight: 500; color: #374151; margin-bottom: 12px; line-height: 1.5;"><i class="fa fa-lightbulb-o" style="color: #8B5CF6; margin-right: 8px;"></i><span class="suggestion-text">${item}</span></div>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #D1D5DB;">
                        <span style="font-size: 12px; color: #4B5563; font-weight: 600;">Masukkan ke Usulan:</span>
                        <select class="form-control target-section-select" ${isAlreadyInDb ? 'disabled' : ''} style="width: 200px; height: 30px; padding: 2px 10px; font-size: 12px; border-radius: 4px;">
                            <option value="catatan_judul" ${selectedAttr('catatan_judul')}>A. Judul</option>
                            <option value="catatan_latar_belakang" ${selectedAttr('catatan_latar_belakang')}>B. Latar Belakang</option>
                            <option value="catatan_rumusan_masalah" ${selectedAttr('catatan_rumusan_masalah')}>C. Rumusan Masalah</option>
                            <option value="catatan_tujuan_penelitian" ${selectedAttr('catatan_tujuan_penelitian')}>D. Tujuan/Manfaat</option>
                            <option value="catatan_tinjauan_pustaka" ${selectedAttr('catatan_tinjauan_pustaka')}>E. Tinjauan Pustaka</option>
                            <option value="catatan_metode_penelitian" ${selectedAttr('catatan_metode_penelitian')}>F. Metode Penelitian</option>
                            <option value="catatan_daftar_pustaka" ${selectedAttr('catatan_daftar_pustaka')}>G. Daftar Pustaka</option>
                            <option value="catatan_format_penulisan" ${selectedAttr('catatan_format_penulisan')}>H. Format Penulisan</option>
                        </select>
                        ${buttonHtml}
                        ${infoHtml}
                    </div>
                </li>`;
            });
            html += '</ul>';
            return html;
        };

        // Helper to format interactive questions
        const formatQuestionsList = (arr) => {
            if (!arr || !arr.length) return '<p class="result-text">Tidak ada catatan.</p>';
            let html = '<ul class="result-list" style="list-style: none; padding-left: 0;">';
            arr.forEach((item, index) => {
                let score = '';
                let note = '';
                if (typeof myQuestionsEvaluation !== 'undefined' && myQuestionsEvaluation && myQuestionsEvaluation[index]) {
                    score = myQuestionsEvaluation[index].score || '';
                    note = myQuestionsEvaluation[index].note || '';
                }
                
                html += `
                <li style="margin-bottom: 16px; padding: 16px; border: 1px solid #E5E7EB; border-radius: 8px; background: #F9FAFB;">
                    <div style="font-weight: 500; color: #374151; margin-bottom: 12px; line-height: 1.5;"><i class="fa fa-caret-right" style="color: #4F46E5; margin-right: 8px;"></i>${item}</div>
                    <button type="button" class="btn btn-sm" style="background: #FFF; border: 1px solid #D1D5DB; color: #4B5563; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 4px 10px;" onclick="$(this).next('.question-evaluation').slideToggle();">
                        <i class="fa fa-commenting-o"></i> Evaluasi Jawaban Mahasiswa
                    </button>
                    <div class="question-evaluation" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #D1D5DB;">
                        <div class="row">
                            <div class="col-md-3">
                                <label style="font-size: 12px; font-weight: 700; color: #4B5563;">Skor (25-100)</label>
                                <input type="number" class="form-control eval-score" data-index="${index}" min="25" max="100" placeholder="25 - 100" style="height: 34px; font-size: 13px;" value="${score}">
                            </div>
                            <div class="col-md-9">
                                <label style="font-size: 12px; font-weight: 700; color: #4B5563; display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    Catatan Dosen
                                    <span class="quick-notes" style="display: flex; gap: 4px;">
                                        <button type="button" class="btn btn-xs" style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 10px;" onclick="window.addQuickNote(this, 'Sangat Baik - Mahasiswa dapat menjawab dengan sempurna dan logis.')">Sangat Baik</button>
                                        <button type="button" class="btn btn-xs" style="background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; font-size: 10px;" onclick="window.addQuickNote(this, 'Baik - Mahasiswa dapat menjawab sebagian besar inti pertanyaan.')">Baik</button>
                                        <button type="button" class="btn btn-xs" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 10px;" onclick="window.addQuickNote(this, 'Cukup - Mahasiswa menjawab seadanya atau kurang mendalam.')">Cukup</button>
                                        <button type="button" class="btn btn-xs" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; font-size: 10px;" onclick="window.addQuickNote(this, 'Kurang - Mahasiswa tidak dapat menjawab atau salah konsep.')">Kurang</button>
                                    </span>
                                </label>
                                <textarea class="form-control eval-note" data-index="${index}" rows="2" placeholder="Tulis catatan spesifik di sini..." style="font-size: 13px; resize: vertical;">${note}</textarea>
                            </div>
                        </div>
                    </div>
                </li>`;
            });
            html += '</ul>';
            
            html += `
            <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; background: #F9FAFB; padding: 12px 16px; border: 1px solid #E5E7EB; border-radius: 8px;">
                <div style="font-weight: 700; color: #4B5563; font-size: 14px;">
                    Rata-rata Skor: <span class="avg-score-display" style="color: #10B981; font-size: 18px; margin-left: 8px;">0.00</span>
                </div>
                <button type="button" class="btn btn-save-eval" style="background: #10B981; color: white; font-weight: 600; border-radius: 6px; padding: 8px 16px; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;">
                    <i class="fa fa-save"></i> Simpan Seluruh Evaluasi
                </button>
            </div>`;
            return html;
        };
        
        // Integrity Check
        if (data.integrity_check) {
            $container.append(`
                <div class="result-section">
                    <h4 style="color: #6B7280; border-color: #E5E7EB;"><i class="fa fa-shield"></i> Integrity Check</h4>
                    <p class="result-text" style="font-weight: 600;">${data.integrity_check}</p>
                </div>
            `);
        }
        
        // Verdict
        if (data.verdict) {
            $container.append(`
                <div class="result-section">
                    <h4 class="verdict"><i class="fa fa-gavel"></i> Rekomendasi (Verdict)</h4>
                    <p class="result-text" style="font-weight: 700; font-size: 16px;">${data.verdict}</p>
                </div>
            `);
        }
        
        // Summary
        if (data.summary) {
            $container.append(`
                <div class="result-section">
                    <h4 style="color: #4B5563;"><i class="fa fa-file-text-o"></i> Ringkasan Proposal</h4>
                    <p class="result-text">${data.summary}</p>
                </div>
            `);
        }
        
        // Strengths & Weaknesses
        const swHtml = `
            <div class="row">
                <div class="col-md-6">
                    <div class="result-section">
                        <h4 class="strengths"><i class="fa fa-thumbs-up"></i> Kekuatan / Keunggulan</h4>
                        ${formatList(data.strengths)}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="result-section">
                        <h4 class="weaknesses"><i class="fa fa-exclamation-triangle"></i> Celah / Kelemahan Kritis</h4>
                        ${formatList(data.weaknesses)}
                    </div>
                </div>
            </div>
            <div class="text-center" style="margin-top: 24px; margin-bottom: 24px;">
                <button type="button" id="btn-open-split" class="btn" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-weight: 700; padding: 10px 20px; border-radius: 8px; transition: all 0.2s;">
                    <i class="fa fa-columns"></i> Buka Mode Split View
                </button>
            </div>
        `;
        $container.append(swHtml);
        
        // Questions
        if (data.questions) {
            $container.append(`
                <div class="result-section">
                    <h4 class="questions"><i class="fa fa-question-circle"></i> Rekomendasi Pertanyaan Sidang</h4>
                    ${formatQuestionsList(data.questions)}
                </div>
            `);
        }
        
        // Suggestions
        if (data.suggestions) {
            $container.append(`
                <div class="result-section">
                    <h4 class="suggestions" style="color: #8B5CF6; border-color: #DDD6FE;"><i class="fa fa-lightbulb-o"></i> Saran Perbaikan (Untuk Mahasiswa)</h4>
                    ${formatSuggestionsList(data.suggestions)}
                </div>
            `);
        }
        
        $container.append(`
            <div class="text-center" style="margin-top: 24px; margin-bottom: 24px;">
                <button type="button" id="btn-open-split-q" class="btn" style="background: #F3E8FF; color: #7E22CE; border: 1px solid #E9D5FF; font-weight: 700; padding: 10px 20px; border-radius: 8px; transition: all 0.2s;">
                    <i class="fa fa-columns"></i> Buka Mode Split View (Pertanyaan & Saran)
                </button>
            </div>
        `);
        
        $container.slideDown();
        setTimeout(window.updateAverageScore, 100);
    }
    
    // Split View Event Listeners
    $(document).on('click', '#btn-open-split', function() {
        var url = $('#file_url_input').val().trim();
        var previewUrl = getPreviewUrl(url);
        
        if (previewUrl) {
            $('#split_iframe').attr('src', previewUrl);
            
            // Clone the container
            var $clone = $('#ai-result-container').clone();
            
            // Remove the split button from the clone
            $clone.find('#btn-open-split').parent().remove();
            
            // Hide questions and suggestions in the clone (only show up to Celah)
            $clone.find('.questions').closest('.result-section').remove();
            $clone.find('.fa-lightbulb-o').closest('.result-section').remove();
            
            $('#split_ai_content').html($clone.html());
            $('#modal_split_view').modal('show');
        } else {
            Swal.fire('Opps', 'Link dokumen tidak valid untuk dibuka dalam Split View.', 'warning');
        }
    });
    
    $(document).on('click', '#btn-open-split-q', function() {
        var url = $('#file_url_input').val().trim();
        var previewUrl = getPreviewUrl(url);
        
        if (previewUrl) {
            $('#split_iframe').attr('src', previewUrl);
            
            // Clone the container
            var $clone = $('#ai-result-container').clone();
            
            // Remove everything EXCEPT questions and suggestions
            var $newContent = $('<div></div>');
            var $questions = $clone.find('.questions').closest('.result-section');
            var $suggestions = $clone.find('.suggestions').closest('.result-section');
            
            if ($questions.length) $newContent.append($questions);
            if ($suggestions.length) $newContent.append($suggestions);
            
            $('#split_ai_content').html($newContent.html());
            $('#modal_split_view').modal('show');
        } else {
            Swal.fire('Opps', 'Link dokumen tidak valid untuk dibuka dalam Split View.', 'warning');
        }
    });

    // Clear iframe when modal closed
    $('#modal_split_view').on('hidden.bs.modal', function () {
        $('#split_iframe').attr('src', '');
        $('#split_ai_content').empty();
    });

    // Inject Suggestion to Summernote
    $(document).on('click', '.btn-inject-suggestion', function() {
        var $li = $(this).closest('li');
        var suggestionText = $li.find('.suggestion-text').text().trim();
        var targetField = $li.find('.target-section-select').val();
        
        var $summernote = $('textarea[name="' + targetField + '"]');
        if ($summernote.length > 0) {
            var currentCode = $summernote.summernote('code');
            
            // Prevent duplicate insertion
            if (currentCode.includes(suggestionText)) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Saran ini sudah ada di rubrik tersebut!',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }
            
            if (currentCode === '<p><br></p>' || currentCode === '<br>') {
                currentCode = '';
            }
            var newHtml = currentCode;
            newHtml += '<p style="margin-bottom: 15px;">' + suggestionText + '</p>';
            
            $summernote.summernote('code', newHtml);
            
            // Auto-save to database silently
            $('[name="id_nilai_sempro"]').val('<?= isset($id_nilai_sempro) ? $id_nilai_sempro : "" ?>');
            var formData = $('#form_catatan').serialize();
            $.ajax({
                url: "<?= site_url('E_skripsi_nilai_sempro/ajax_update_catatan') ?>",
                type: "POST",
                data: formData,
                dataType: "JSON",
                success: function(res) {
                    console.log('Auto-saved suggestion to DB');
                }
            });
            
            // Notification
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Saran berhasil dimasukkan dan disimpan otomatis!',
                showConfirmButton: false,
                timer: 2000
            });
            
            // change button appearance to indicate success and disable it
            var $btn = $(this);
            $btn.html('<i class="fa fa-check"></i> Disimpan').css({'background': '#10B981', 'opacity': '0.8', 'cursor': 'not-allowed'}).prop('disabled', true);
            var $select = $li.find('.target-section-select');
            $select.prop('disabled', true);
            var selectedText = $select.find('option:selected').text();
            
            var resetBtnHtml = `<button type="button" class="btn btn-sm btn-reset-suggestion" style="background: #EF4444; color: #FFF; border: none; font-size: 12px; font-weight: 600; border-radius: 4px; padding: 4px 12px;" title="Hapus usulan ini">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>`;
            $btn.after(resetBtnHtml);
            
            $select.parent().append(`<div class="suggestion-info-text" style="width: 100%; font-size: 11px; color: #10B981; margin-top: 6px;"><i class="fa fa-info-circle"></i> Sudah diusulkan untuk revisi pada rubrik <strong>${selectedText}</strong></div>`);
            
        } else {
            Swal.fire('Error', 'Kolom form tidak ditemukan.', 'error');
        }
    });

    // Reset Suggestion
    $(document).on('click', '.btn-reset-suggestion', function() {
        var $btnReset = $(this);
        var $li = $btnReset.closest('li');
        var suggestionText = $li.find('.suggestion-text').text().trim();
        var targetField = $li.find('.target-section-select').val();
        
        var $summernote = $('textarea[name="' + targetField + '"]');
        if ($summernote.length > 0) {
            var currentCode = $summernote.summernote('code');
            
            var htmlToReplace = '<p style="margin-bottom: 15px;">' + suggestionText + '</p>';
            if (currentCode.includes(htmlToReplace)) {
                currentCode = currentCode.replace(htmlToReplace, '');
            } else if (currentCode.includes(suggestionText)) {
                currentCode = currentCode.replace(suggestionText, '');
            }
            
            if (currentCode.trim() === '') {
                currentCode = '<p><br></p>';
            }
            
            $summernote.summernote('code', currentCode);
            
            // Auto-save
            $('[name="id_nilai_sempro"]').val('<?= isset($id_nilai_sempro) ? $id_nilai_sempro : "" ?>');
            var formData = $('#form_catatan').serialize();
            $.ajax({
                url: "<?= site_url('E_skripsi_nilai_sempro/ajax_update_catatan') ?>",
                type: "POST",
                data: formData,
                dataType: "JSON",
                success: function(res) {
                    console.log('Auto-removed suggestion from DB');
                }
            });
            
            // Revert UI
            var $btnInject = $li.find('.btn-inject-suggestion');
            $btnInject.html('<i class="fa fa-arrow-right"></i> Masukkan').css({'background': '#8B5CF6', 'opacity': '1', 'cursor': 'pointer'}).prop('disabled', false);
            $li.find('.target-section-select').prop('disabled', false);
            $li.find('.suggestion-info-text').remove();
            $btnReset.remove();
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Saran berhasil dihapus dari usulan revisi!',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });

    // Save Evaluation Event Listener
    $(document).on('click', '.btn-save-eval', function() {
        var $btn = $(this);
        var $container = $btn.closest('.result-section');
        
        var evaluations = {};
        $container.find('.eval-score').each(function() {
            var index = $(this).data('index');
            var score = $(this).val();
            var note = $container.find('.eval-note[data-index="'+index+'"]').val();
            evaluations[index] = {
                score: score,
                note: note
            };
        });

        var dataString = 'id_judul_skripsi=<?= $skripsi->id_judul_skripsi ?>&evaluations=' + encodeURIComponent(JSON.stringify(evaluations)) + '&<?= $this->security->get_csrf_token_name() ?>=<?= $this->security->get_csrf_hash() ?>';

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: "<?= site_url('E_skripsi_nilai_sempro/ajax_save_evaluation') ?>",
            type: "POST",
            data: dataString,
            dataType: "json",
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Seluruh Evaluasi');
                if (res.status) {
                    if (typeof myQuestionsEvaluation !== 'undefined') {
                        myQuestionsEvaluation = evaluations; // update global
                    }
                    // Sync values everywhere (e.g. from modal back to main page)
                    for (var idx in evaluations) {
                        $('.eval-score[data-index="'+idx+'"]').val(evaluations[idx].score);
                        $('.eval-note[data-index="'+idx+'"]').val(evaluations[idx].note);
                    }
                    Swal.fire({
                        title: 'Tersimpan!',
                        text: 'Evaluasi pertanyaan berhasil disimpan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.msg, 'error');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Seluruh Evaluasi');
                Swal.fire('Error', 'Gagal menyimpan evaluasi.', 'error');
            }
        });
    });
});

window.updateAverageScore = function() {
    var total = 0;
    var count = 0;
    // We only calculate from the main container to avoid double counting if split view is open
    $('#ai-result-container .eval-score').each(function() {
        var val = $(this).val();
        if (val !== '') {
            var num = parseFloat(val);
            if (!isNaN(num)) {
                total += num;
                count++;
            }
        }
    });
    var avg = count > 0 ? (total / count).toFixed(2) : '0.00';
    $('.avg-score-display').text(avg);
};

$(document).on('input', '.eval-score', function() {
    // Basic bounds checking for UX (optional, HTML5 min/max handles form submission, but this gives instant feedback)
    var val = $(this).val();
    if (val !== '') {
        var num = parseFloat(val);
        if (num > 100) $(this).val(100);
        // We don't enforce min 25 on typing because they might be typing "2" then "5".
    }
    
    // Sync to split view or main view if mirrored
    var index = $(this).data('index');
    $('.eval-score[data-index="'+index+'"]').not(this).val($(this).val());
    
    window.updateAverageScore();
});

$(document).on('input', '.eval-note', function() {
    var index = $(this).data('index');
    $('.eval-note[data-index="'+index+'"]').not(this).val($(this).val());
});

window.addQuickNote = function(btn, text) {
    var $textarea = $(btn).closest('.col-md-9').find('textarea');
    $textarea.val(text).trigger('input');
    
    // Briefly flash textarea to indicate insertion
    $textarea.css('background-color', '#FEF3C7');
    setTimeout(function() {
        $textarea.css('background-color', '');
    }, 300);
};
</script>

<?php
/**
 * Reuse: Load shared modal forms (Nilai SEMPRO, Usulan Revisi, Berita Acara)
 * dari halaman utama agar tidak ada duplikasi form/JS.
 * Fungsi JS edit_nilai_sempro(), edit_catatan(), edit_berita_acara() sudah
 * tersedia di dalam file ini dan semua modal HTML ikut ter-include di bawah.
 */
$this->load->view('E_skripsi_nilai_sempro/E_skripsi_nilai_sempro_after_page');
?>
