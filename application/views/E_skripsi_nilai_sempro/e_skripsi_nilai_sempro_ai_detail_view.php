<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .ai-wrapper {
        --primary: #6D28D9;
        --primary-light: #8B5CF6;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --background: #F9FAFB;
        --surface: #FFFFFF;
        --text-main: #111827;
        --text-muted: #6B7280;
        --border: #E5E7EB;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: flex;
        gap: 30px;
        color: var(--text-main);
    }
    .ai-wrapper * { box-sizing: border-box; }

    /* Main Panel */
    .main-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .form-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 16px;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group-ai {
        margin-bottom: 24px;
    }

    .form-group-ai label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-main);
    }

    .form-control-ai {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-control-ai:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
        outline: none;
    }

    .role-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .role-option {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .role-option:hover {
        background: #F5F3FF;
        border-color: #C4B5FD;
    }
    
    .role-option input[type="checkbox"] {
        margin-top: 2px;
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
    
    .role-title {
        font-weight: 700;
        font-size: 13px;
        color: var(--text-main);
        margin-bottom: 2px;
        white-space: normal;
    }
    
    .role-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        line-height: 1.4;
        margin-bottom: 0;
        white-space: normal;
    }

    .btn-ai {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }
    .btn-ai:hover {
        box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3);
        transform: translateY(-2px);
    }
    .btn-ai:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Result Panel */
    .result-container {
        display: none;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    
    .result-section {
        margin-bottom: 24px;
    }
    
    .result-section h4 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid #F3F4F6;
        padding-bottom: 8px;
    }
    
    .result-section h4.verdict { color: var(--primary); border-color: #DDD6FE; }
    .result-section h4.strengths { color: var(--success); border-color: #D1FAE5; }
    .result-section h4.weaknesses { color: var(--danger); border-color: #FEE2E2; }
    .result-section h4.questions { color: #3B82F6; border-color: #DBEAFE; }
    
    .result-list {
        padding-left: 20px;
        margin: 0;
        color: #4B5563;
        font-size: 14px;
        line-height: 1.6;
    }
    .result-list li { margin-bottom: 8px; }
    
    .result-text {
        font-size: 14px;
        line-height: 1.6;
        color: #4B5563;
    }

    /* Sidebar Panel */
    .sidebar-panel {
        width: 380px;
        background: var(--surface);
        border: none;
        border-radius: 16px;
        align-self: flex-start;
        flex-shrink: 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 70px;
    }


    
    .sidebar-footer {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid #F3F4F6;
        text-align: left;
    }
    
    .sidebar-meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px;
        border-radius: 8px;
        background: #F9FAFB;
    }
    
    .sidebar-meta-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        background: #EEF2FF;
        color: #4F46E5;
    }
    
    .sidebar-meta-text { flex: 1; }
    .sidebar-meta-label { font-size: 11px; color: #6B7280; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
    .sidebar-meta-value { font-size: 13px; color: #111827; font-weight: 600; }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .ai-wrapper {
            flex-direction: column;
        }
        .sidebar-panel {
            width: 100%;
            position: relative;
            top: 0;
            margin-bottom: 24px;
        }
    }
    
    @media (max-width: 767px) {
        .role-options {
            grid-template-columns: 1fr;
        }
    }

    .student-card {
        display: flex;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        margin-bottom: 24px;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    
    .student-card img {
        width: 72px;
        height: 72px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .student-card .info {
        flex: 1;
    }
    
    .student-card .name {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    
    .student-card .nim {
        font-size: 13px;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 6px;
    }
    
    .student-card .title {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.4;
    }

</style>

<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light thread-container">
                <div class="portlet-title">
                    <div class="caption">
                        <a href="<?= site_url('E_skripsi_nilai_sempro') ?>" class="btn btn-default btn-xs" style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Kembali</a>
                        <span class="caption-subject font-purple-sharp bold uppercase">Examiner Assistant (AI)</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="ai-wrapper">
                        
                        <div class="main-panel">
                            
                            <?php 
                                $photo_src = !empty($mahasiswa->foto) 
                                    ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . rawurlencode($mahasiswa->foto) 
                                    : base_url('assets/no_profile_photo.png');
                            ?>
                            
                            <!-- Student Card (Visible on all screens at the top) -->
                            <div class="student-card">
                                <img src="<?= $photo_src ?>" 
                                     alt="<?= htmlspecialchars($mahasiswa->nm_pd) ?>"
                                     onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                                <div class="info">
                                    <div class="name"><?= htmlspecialchars($mahasiswa->nm_pd) ?></div>
                                    <div class="nim">Mahasiswa &bull; <?= htmlspecialchars($mahasiswa->nipd) ?></div>
                                    <div class="title">"<?= htmlspecialchars($skripsi->judul_ditetapkan) ?>"</div>
                                </div>
                            </div>

                            <div class="form-card">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
                                    <h3 class="form-title" style="margin-bottom:0;"><i class="fa fa-magic"></i> Analisis Dokumen Proposal</h3>
                                    <?php if (!empty($jadwal->link_dokumen)): ?>
                                    <a href="<?= htmlspecialchars($jadwal->link_dokumen) ?>" target="_blank" class="btn btn-sm" style="background:#EEF2FF; color:#4F46E5; border:1px solid #C7D2FE; border-radius:6px; font-weight:700;">
                                        <i class="fa fa-folder-open"></i> Buka Berkas Mahasiswa
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <p style="font-size: 14px; color: #4B5563; margin-bottom: 20px;">
                                    Asisten AI ini akan membantu Anda membaca dokumen proposal dan merumuskan daftar pertanyaan, kekuatan, serta kelemahan berdasarkan Peran Penguji yang Anda emban (sesuai standar Prodi).
                                </p>
                                
                                <form id="form-ai-analyze">
                                    <input type="hidden" name="id_judul_skripsi" value="<?= $skripsi->id_judul_skripsi ?>">
                                    
                                    <?php
                                        $default_file_url = '';
                                        if (!empty($my_file_url)) {
                                            $default_file_url = $my_file_url;
                                        } elseif (!empty($jadwal->link_dokumen)) {
                                            $link = $jadwal->link_dokumen;
                                            if (strpos($link, '/file/d/') !== false || strpos($link, '/document/d/') !== false || strpos($link, '/presentation/d/') !== false || strpos($link, '/spreadsheets/d/') !== false) {
                                                $default_file_url = $link;
                                            }
                                        }
                                    ?>
                                    <div class="form-group-ai">
                                        <label>Link Google Drive (Dokumen/PDF)</label>
                                        <div style="display: flex; gap: 8px;">
                                            <input type="url" name="file_url" id="file_url_input" class="form-control-ai" placeholder="https://docs.google.com/document/d/... atau https://drive.google.com/file/d/..." value="<?= htmlspecialchars($default_file_url) ?>" <?= !empty($my_ai_result) ? 'readonly' : 'required' ?> style="flex: 1;">
                                        </div>
                                        <small style="color: #6B7280; font-size: 12px; margin-top: 4px; display: block;">Jika link dari mahasiswa adalah folder, silakan buka foldernya melalui tombol di atas, lalu <strong style="color:#4F46E5;">copy link spesifik file PDF/Doc</strong> proposal dan paste ke kolom ini.</small>
                                    </div>
                                    
                                    <div class="form-group-ai">
                                        <?php 
                                        $roles_list = [
                                                'problem_reviewer' => ['title' => 'Problem Reviewer', 'desc' => 'Fokus pada Latar belakang, urgensi, dan rumusan masalah.'],
                                                'literature_reviewer' => ['title' => 'Literature Reviewer', 'desc' => 'Fokus pada Kajian pustaka, gap penelitian, dan kerangka teori.'],
                                                'method_reviewer' => ['title' => 'Method Reviewer', 'desc' => 'Fokus pada Desain penelitian, variabel, populasi, sampel, dan instrumen.'],
                                                'statistical_reviewer' => ['title' => 'Statistical Reviewer', 'desc' => 'Fokus pada Teknik analisis data dan ketepatan rumus statistik.'],
                                                'technical_reviewer' => ['title' => 'Technical Reviewer', 'desc' => 'Fokus pada Teknis rancang bangun sistem/aplikasi (Khusus IT).'],
                                                'industry_reviewer' => ['title' => 'Industry Reviewer', 'desc' => 'Fokus pada Relevansi bisnis/industri dan manfaat praktis.'],
                                                'ethics_reviewer' => ['title' => 'Ethics & Feasibility Reviewer', 'desc' => 'Fokus pada Kelayakan jadwal, etika, dan aksesibilitas data.']
                                            ];
                                            
                                            $prodi_clean = isset($skripsi->nm_lemb) ? strtolower(trim((string)$skripsi->nm_lemb)) : '';
                                            $is_it = (strpos($prodi_clean, 'trpl') !== false || strpos($prodi_clean, 'perangkat lunak') !== false);
                                            
                                            if (!$is_it) {
                                                unset($roles_list['technical_reviewer']);
                                            }
                                            
                                            $locked_roles = isset($locked_roles) ? $locked_roles : [];
                                            
                                            $examiner_count = 0;
                                            if (!empty($nm_penguji_1) && $nm_penguji_1 != '-') $examiner_count++;
                                            if (!empty($nm_penguji_2) && $nm_penguji_2 != '-') $examiner_count++;
                                            if (!empty($nm_penguji_3) && $nm_penguji_3 != '-') $examiner_count++;
                                            if ($examiner_count == 0) $examiner_count = 1;

                                            $max_roles = ceil(count($roles_list) / $examiner_count);
                                            ?>
                                        <script>
                                            var maxRolesAllowed = <?= $max_roles ?>;
                                        </script>
                                        
                                        <div style="margin-bottom: 12px;">
                                            <label style="margin-bottom: 0;">Peran Anda (Pilih Maks. <?= $max_roles ?> Fokus Pengujian)</label>
                                        </div>

                                        <div class="role-options">
                                            <?php foreach ($roles_list as $key => $role): ?>
                                            <?php 
                                                $is_locked = in_array($key, $locked_roles);
                                                $is_mine = isset($my_roles) && in_array($key, $my_roles);
                                                $is_visually_locked = $is_locked || !empty($my_ai_result);
                                            ?>
                                            <label class="role-option <?= $is_visually_locked ? 'locked' : '' ?>" style="<?= $is_visually_locked ? 'opacity: 0.6; cursor: not-allowed; background: #F9FAFB;' : '' ?>">
                                                <div style="display: flex; flex-direction: row; align-items: flex-start; gap: 10px; width: 100%;">
                                                    <div style="flex-shrink: 0; display: flex; align-items: center; min-height: 20px;">
                                                        <input type="checkbox" name="role_penguji[]" value="<?= $key ?>" <?= $is_visually_locked ? 'disabled' : '' ?> <?= $is_mine ? 'checked' : '' ?> class="role-checkbox" style="accent-color: #6D28D9; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                                    </div>
                                                    <div style="flex: 1; display: flex; flex-direction: column; min-width: 0; opacity: 1 !important; visibility: visible !important;">
                                                        <div class="role-title" style="color: #111827 !important; display: block !important; visibility: visible !important; opacity: 1 !important;">
                                                            <?= $role['title'] ?>
                                                            <?php if ($is_locked): ?>
                                                            <span style="color: #DC2626; font-size: 10px; font-weight: 700; margin-left: 4px; background: #FEE2E2; padding: 2px 4px; border-radius: 4px;"><i class="fa fa-lock"></i> Dipilih</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="role-desc" style="color: #6B7280 !important; display: block !important; visibility: visible !important; opacity: 1 !important; white-space: normal;"><?= $role['desc'] ?></div>
                                                    </div>
                                                </div>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group-ai" style="margin-top: 16px;">
                                        <div style="margin-bottom: 8px;">
                                            <label style="margin-bottom: 0;">Interest / Kepakaran Anda (Opsional, Maks. 3)</label>
                                            <p class="role-desc">Pilih kepakaran spesifik Anda untuk menajamkan sudut pandang analisis AI.</p>
                                        </div>
                                        <select name="interest_penguji[]" id="interest_penguji" class="form-control" multiple="multiple" style="width: 100%;" <?= !empty($my_ai_result) ? 'disabled' : '' ?>>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn-ai" id="btn-generate" <?= !empty($my_ai_result) ? 'disabled' : '' ?> style="<?= !empty($my_ai_result) ? 'background-color: #10B981; cursor: not-allowed;' : '' ?>">
                                        <script>
                                            var myAiResult = <?= !empty($my_ai_result) ? json_encode($my_ai_result) : 'null' ?>;
                                            var myQuestionsEvaluation = <?= !empty($my_questions_evaluation) ? json_encode($my_questions_evaluation) : 'null' ?>;
                                            var dbCatatan = <?= !empty($penilaian) ? json_encode([
                                                'catatan_judul' => $penilaian->catatan_judul,
                                                'catatan_latar_belakang' => $penilaian->catatan_latar_belakang,
                                                'catatan_rumusan_masalah' => $penilaian->catatan_rumusan_masalah,
                                                'catatan_tujuan_penelitian' => $penilaian->catatan_tujuan_penelitian,
                                                'catatan_tinjauan_pustaka' => $penilaian->catatan_tinjauan_pustaka,
                                                'catatan_metode_penelitian' => $penilaian->catatan_metode_penelitian,
                                                'catatan_daftar_pustaka' => $penilaian->catatan_daftar_pustaka,
                                                'catatan_format_penulisan' => $penilaian->catatan_format_penulisan
                                            ]) : '{}' ?>;
                                        </script>
                                        <?php if (!empty($my_ai_result)): ?>
                                            <i class="fa fa-check-circle"></i> Hasil Analisis AI Telah Disimpan
                                        <?php else: ?>
                                            <i class="fa fa-cogs"></i> Analisis Dokumen dengan AI
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="result-container" id="ai-result-container">
                                <!-- Hasil AI akan di-render di sini oleh JavaScript -->
                            </div>
                            
                        </div>

                        <!-- SIDEBAR -->
                        <div class="sidebar-panel">
                            <div class="sidebar-body" style="padding: 24px; background: white; border-radius: 16px;">
                                <div style="font-size: 14px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 16px; border-bottom: 2px solid #F3F4F6; padding-bottom: 12px;">
                                    <i class="fa fa-info-circle"></i> Informasi Sidang
                                </div>
                                <div class="sidebar-footer" style="border-top: none; padding-top: 0;">
                                    <div class="sidebar-meta-item">
                                        <div class="sidebar-meta-icon">👨‍🏫</div>
                                        <div class="sidebar-meta-text">
                                            <div class="sidebar-meta-label">Penguji 1</div>
                                            <div class="sidebar-meta-value"><?= $nm_penguji_1 ?></div>
                                            
                                            <?php if (!empty($nm_penguji_2) && $nm_penguji_2 != '-'): ?>
                                            <div class="sidebar-meta-label" style="margin-top: 8px;">Penguji 2</div>
                                            <div class="sidebar-meta-value"><?= $nm_penguji_2 ?></div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($nm_penguji_3) && $nm_penguji_3 != '-'): ?>
                                            <div class="sidebar-meta-label" style="margin-top: 8px;">Penguji 3</div>
                                            <div class="sidebar-meta-value"><?= $nm_penguji_3 ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="sidebar-meta-item">
                                        <div class="sidebar-meta-icon"><i class="fa fa-calendar"></i></div>
                                        <div class="sidebar-meta-text">
                                            <div class="sidebar-meta-label">Jadwal Sempro</div>
                                            <div class="sidebar-meta-value">
                                                <?php 
                                                    if (!empty($jadwal->tanggal_seminar)) {
                                                        echo date('d M Y', strtotime($jadwal->tanggal_seminar)) . ' ' . substr($jadwal->jam,0,5);
                                                    } else {
                                                        echo "Belum Dijadwalkan";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($id_nilai_sempro)): ?>
                                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #F3F4F6;">
                                    <div style="font-size: 11px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 10px;">
                                        <i class="fa fa-edit" style="margin-right: 4px;"></i> Form Penilaian
                                    </div>

                                    <?php if ($show_form_buttons): ?>
                                        <?php
                                        $is_nilai_filled = (!empty($penilaian->nilai_akhir) && $penilaian->nilai_akhir > 0);
                                        // Tombol Nilai & Catatan selama BA belum final
                                        $is_editable_ai = !$is_nilai_filled || !$ba_sudah_diisi;
                                        ?>

                                        <?php if ($is_editable_ai): ?>
                                        <button onclick="edit_nilai_sempro('<?= $id_nilai_sempro ?>')"
                                            style="width: 100%; padding: 10px 14px; margin-bottom: 8px; border-radius: 8px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; border: none; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                            <?= $is_nilai_filled ? 'Edit Nilai SEMPRO' : 'Isi Nilai SEMPRO' ?>
                                        </button>
                                        <button onclick="edit_catatan('<?= $id_nilai_sempro ?>')"
                                            style="width: 100%; padding: 10px 14px; margin-bottom: 8px; border-radius: 8px; background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; border: none; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                                            <i class="glyphicon glyphicon-list"></i> Usulan Revisi
                                        </button>
                                        <?php endif; ?>

                                        <?php if ($is_pembimbing_utama): ?>
                                            <?php if ($show_ba_button && $other_examiners_graded): ?>
                                            <button onclick="edit_berita_acara('<?= $id_nilai_sempro ?>')"
                                                style="width: 100%; padding: 10px 14px; margin-bottom: 8px; border-radius: 8px; background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%); color: white; border: none; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                                                <i class="glyphicon glyphicon-file"></i>
                                                <?= $ba_sudah_diisi ? 'Edit Berita Acara' : 'Isi Berita Acara' ?>
                                            </button>
                                            <?php elseif ($show_ba_button && !$other_examiners_graded): ?>
                                            <div style="padding: 10px 12px; border-radius: 8px; background: #FEF9C3; border: 1px solid #FDE68A; font-size: 12px; color: #92400E; font-weight: 600; margin-bottom: 8px;">
                                                <i class="fa fa-clock-o"></i>
                                                Menunggu <?= $sisa_belum_nilai ?> penguji lain mengisi nilai untuk dapat mengisi Berita Acara
                                            </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($ba_sudah_diisi): ?>
                                        <a href="<?= site_url('E_skripsi_nilai_sempro/cetak_pdf/' . md5($skripsi->id_judul_skripsi)) ?>" target="_blank"
                                            style="width: 100%; padding: 10px 14px; border-radius: 8px; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; border: none; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; text-decoration: none !important;">
                                            <i class="fa fa-download"></i> Download Berita Acara
                                        </a>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <?php if (empty($has_schedule)): ?>
                                        <div style="padding: 10px 12px; border-radius: 8px; background: #F3F4F6; border: 1px solid #E5E7EB; font-size: 12px; color: #6B7280; font-weight: 600;">
                                            <i class="fa fa-calendar-o"></i> Seminar belum dijadwalkan. Form nilai dan usulan revisi dapat diakses sehari sebelum tanggal sempro dan setelahnya.
                                        </div>
                                        <?php else: ?>
                                        <div style="padding: 10px 12px; border-radius: 8px; background: #EFF6FF; border: 1px solid #BFDBFE; font-size: 12px; color: #1D4ED8; font-weight: 600;">
                                            <i class="fa fa-calendar"></i>
                                            Jadwal seminar: <strong><?= date('d M Y', strtotime($tgl_seminar)) ?></strong>.<br>
                                            Form nilai dan usulan revisi dapat diakses pada tanggal <strong><?= date('d M Y', strtotime('-1 day', strtotime($tgl_seminar))) ?></strong> dan setelahnya.
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div style="margin-top: 16px; padding: 12px; background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 8px; text-align: left;">
                                    <p style="font-size: 12px; color: #92400E; margin: 0; font-weight: 600;">
                                        <i class="fa fa-info-circle"></i> Anda tidak memiliki tugas penilaian untuk mahasiswa ini.
                                    </p>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Split View Modal -->
<div class="modal fade" id="modal_split_view" tabindex="-1" role="dialog" aria-hidden="true" style="padding-right: 0 !important;">
    <div class="modal-dialog" style="width: 98%; max-width: 100%; height: 95vh; margin: 2.5vh auto; padding: 0 15px;">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; height: 100%;">
            <div class="modal-header" style="background: #F8FAFC; padding: 12px 24px; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                <h4 class="modal-title" style="margin: 0; font-weight: 700; color: #1E293B;"><i class="fa fa-columns"></i> Mode Split View: Dokumen & Hasil AI</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: 0; opacity: 0.6;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0; height: calc(100% - 55px); display: flex; background: #E2E8F0;">
                <div style="flex: 6; border-right: 1px solid #CBD5E1; height: 100%; background: #FFF;">
                    <iframe id="split_iframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                <div style="flex: 4; height: 100%; background: #F8FAFC; overflow-y: auto; padding: 24px;">
                    <div id="split_ai_content" style="background: #FFF; padding: 24px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <!-- Cloned content will go here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
