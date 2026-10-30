<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .bimbingan-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: flex;
        gap: 30px;
        color: #111827;
        align-items: flex-start;
    }
    
    .bimbingan-wrapper * { box-sizing: border-box; }

    @keyframes blinker_edit {
        50% { opacity: 0; }
    }

    /* Custom Tooltip Bubble */
    .custom-tooltip {
        position: relative;
        display: inline-block;
    }
    
    .custom-tooltip .tooltip-text {
        visibility: hidden;
        width: 260px;
        background-color: #1E293B;
        color: #fff;
        text-align: left;
        border-radius: 8px;
        padding: 10px 14px;
        position: absolute;
        z-index: 100;
        top: 125%;
        left: 0;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.5;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        pointer-events: none;
    }
    
    .custom-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 12px;
        margin-left: -6px;
        border-width: 6px;
        border-style: solid;
        border-color: transparent transparent #1E293B transparent;
    }
    
    .custom-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .portlet-title::before,
    .portlet-title::after {
        display: none !important;
    }

    .thread-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Profile Card Sidebar */
    .sidebar-panel {
        width: 360px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        align-self: flex-start;
        flex-shrink: 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 70px;
    }

    .sidebar-cover {
        height: 100px;
        background: linear-gradient(135deg, #1E293B, #475569);
        position: relative;
    }

    .sidebar-student-name {
        color: #111827;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .sidebar-body {
        padding: 24px;
        text-align: center;
        background: white;
    }

    .sidebar-nim {
        color: #EF4444;
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: 0.8px;
        margin-bottom: 20px;
        text-transform: uppercase;
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
        padding: 10px 12px;
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
    
    .sidebar-meta-text {
        flex: 1;
        min-width: 0;
    }
    .sidebar-meta-label {
        font-size: 10px;
        color: #6B7280;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .sidebar-meta-value {
        font-size: 12.5px;
        color: #111827;
        font-weight: 600;
        line-height: 1.4;
        word-wrap: break-word;
    }

    /* Table Review Styles */
    .table-review {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .table-review thead th {
        background: #F8FAFC !important;
        color: #475569 !important;
        font-size: 11.5px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        border-bottom: 2px solid #E2E8F0 !important;
        border-top: none !important;
        padding: 13px 16px !important;
        vertical-align: middle !important;
    }
    .table-review tbody td {
        padding: 13px 16px !important;
        vertical-align: middle !important;
        border-top: 1px solid #F1F5F9 !important;
        border-bottom: none !important;
        font-size: 13px;
        color: #1E293B;
        background: #FFFFFF;
        transition: background 0.15s ease;
    }
    .table-review tbody tr:hover td {
        background: #F8FAFC !important;
    }
    .table-review tbody tr.row-recently-reviewed td {
        background: #F5F3FF !important;
    }
    .table-review tbody tr.row-recently-reviewed:hover td {
        background: #EDE9FE !important;
    }
    .table-review tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Action Buttons in Table */
    .btn-review-action {
        background: #4F46E5 !important;
        color: #FFFFFF !important;
        border: 1px solid #4338CA !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 4px rgba(79,70,229,0.25) !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }
    .btn-review-action:hover,
    .btn-review-action:focus,
    .btn-review-action:active {
        background: #4338CA !important;
        color: #FFFFFF !important;
        border-color: #3730A3 !important;
        box-shadow: 0 4px 8px rgba(79,70,229,0.35) !important;
        transform: translateY(-1px);
        text-decoration: none !important;
    }
    .btn-review-action i {
        color: #FFFFFF !important;
    }

    .btn-foto-action {
        background: #059669 !important;
        color: #FFFFFF !important;
        border: 1px solid #047857 !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        padding: 6px 12px !important;
        font-size: 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 4px rgba(5,150,105,0.25) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
    .btn-foto-action:hover:not(:disabled),
    .btn-foto-action:focus:not(:disabled) {
        background: #047857 !important;
        color: #FFFFFF !important;
        border-color: #065F46 !important;
        box-shadow: 0 4px 8px rgba(5,150,105,0.35) !important;
        transform: translateY(-1px);
    }
    .btn-foto-action i {
        color: #FFFFFF !important;
    }
    .btn-foto-action:disabled {
        background: #F1F5F9 !important;
        color: #94A3B8 !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
    }
    .btn-foto-action:disabled i {
        color: #94A3B8 !important;
    }

    @media (max-width: 1200px) {
        .bimbingan-wrapper {
            flex-direction: column-reverse;
            gap: 24px;
        }
        .sidebar-panel {
            width: 100%;
            position: static;
        }
    }

    /* ===== Skeleton Loading Styles ===== */
    @keyframes shimmer {
        0%   { background-position: -600px 0; }
        100% { background-position: 600px 0; }
    }
    .pulse {
        background: linear-gradient(90deg, #E2E8F0 25%, #F1F5F9 50%, #E2E8F0 75%);
        background-size: 600px 100%;
        animation: shimmer 1.6s infinite linear;
        border-radius: 8px;
    }
    .foto-skeleton {
        position: absolute;
        inset: 0;
        z-index: 10;
        background: #F8FAFC;
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .skeleton-header {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .skeleton-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        flex-shrink: 0;
    }
    .skeleton-line {
        border-radius: 6px;
    }
    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        flex: 1;
    }
    .skeleton-card {
        border-radius: 12px;
        min-height: 130px;
    }
    .skeleton-label {
        text-align: center;
        color: #94A3B8;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding-bottom: 8px;
    }
    /* ===== End Skeleton ===== */
</style>

<div class="page-content" style="padding-top: 0;">
    <div class="bimbingan-wrapper">
        
        <!-- Left Side: Weekly Cards Grid inside Metronic Portlet -->
        <div class="thread-panel">
            <div class="portlet light" style="margin-bottom: 0;">
                <div class="portlet-title" style="display: flex !important; justify-content: space-between !important; align-items: center !important; border-bottom: 1px solid #E5E7EB !important; padding-bottom: 12px !important; margin-bottom: 20px !important; width: 100% !important; float: none !important;">
                    <div class="caption" style="display: flex !important; flex-direction: column !important; gap: 2px !important; text-align: left !important; float: none !important; padding: 0 !important; margin: 0 !important;">
                        <span class="caption-subject font-green-sharp bold uppercase" style="font-weight: 800; font-size: 16px; color: #111827;"><?= $judul_besar; ?> <small style="color: #6B7280;"><?= $judul_kecil; ?></small></span>
                    </div>
                    <div class="actions" style="margin: 0 !important; float: none !important; padding: 0 !important;">
                        <?php 
                            $back_smt = $this->session->userdata('magang_selected_smt');
                            $back_url = site_url('C_laporan_magang' . (!empty($back_smt) ? '?id_smt=' . $back_smt : ''));
                        ?>
                        <a href="<?= $back_url ?>" class="btn btn-default btn-sm" style="border-radius: 8px; font-weight: bold; background: #EEF2FF !important; color: #4F46E5 !important; border: 1px solid #C7D2FE !important;"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                
                <div class="portlet-body">
                    <?php if (isset($missing_weeks) && $missing_weeks > 0): ?>
                        <div class="alert alert-warning" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; border-radius: 12px; padding: 15px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
                            <i class="fa fa-exclamation-triangle" style="font-size: 20px; color: #F59E0B; margin-top: 2px;"></i>
                            <div>
                                <h4 style="margin: 0 0 4px 0; font-weight: 700; font-size: 15px;">Perhatian</h4>
                                <p style="margin: 0; font-size: 13.5px;">Mahasiswa ini sudah <strong><?= $missing_weeks ?> minggu</strong> belum melengkapi laporan terhitung dari laporan terakhirnya.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                    // Precompute folder_id_map for all logs (lazy-load approach)
                    $folder_id_map = [];
                    if (!empty($logs)) {
                        foreach ($logs as $_log) {
                            $fid = '';
                            if (!empty($_log->link_google_drive)) {
                                if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $_log->link_google_drive, $_m)) {
                                    $fid = $_m[1];
                                } elseif (preg_match('/id=([a-zA-Z0-9_-]+)/', $_log->link_google_drive, $_m)) {
                                    $fid = $_m[1];
                                }
                            }
                            $folder_id_map[$_log->id_tracking] = $fid;
                        }
                    }
                    ?>
                    <!-- Table of Weekly Reports -->
                    <div class="table-responsive" style="border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); background: #FFFFFF; margin-bottom: 20px;">
                        <table class="table table-review" style="width: 100%; margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Minggu & Periode</th>
                                    <th style="text-align: center;">Kelengkapan</th>
                                    <th style="text-align: center;">Ketepatan Waktu</th>
                                    <th style="text-align: center; width: 85px;">Skor</th>
                                    <th style="text-align: center;">Status Review</th>
                                    <th style="text-align: center; width: 170px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($logs)): ?>
                                    <?php $no = 1; foreach($logs as $log): ?>
                                        <?php 
                                        $has_review = !empty($log->review_dpl);
                                        $is_recently_reviewed = false;
                                        if ($has_review && !empty($log->change_date)) {
                                            if ((time() - strtotime($log->change_date)) <= 3600) { // 1 jam
                                                $is_recently_reviewed = true;
                                            }
                                        }

                                        $skipped = $log->skipped_start_days ?? 0;
                                        $obligatory_days = 6 - $skipped;
                                        $terisi_days = $obligatory_days - count($log->empty_days ?? []);

                                        $nilai_text = '-';
                                        if (isset($log->skor_dpl) && $log->skor_dpl !== null) {
                                            $nilai_text = $log->skor_dpl;
                                        } elseif ($has_review && preg_match('/Nilai Kelengkapan Laporan:\s*([0-9]+)\/100/i', strip_tags($log->review_dpl), $matches)) {
                                            $nilai_text = $matches[1];
                                        }

                                        $show_edit_indicator = false;
                                        if(isset($log->alasan_edit) && !empty($log->alasan_edit)) {
                                            if (time() - strtotime($log->alasan_edit->created_at) <= (3 * 24 * 3600)) {
                                                $show_edit_indicator = true;
                                            }
                                        }

                                        $has_foto = !empty($folder_id_map[$log->id_tracking]);
                                        ?>
                                        <tr class="<?= $is_recently_reviewed ? 'row-recently-reviewed' : '' ?>">
                                            <td style="text-align: center; font-weight: 700; color: #64748B;">
                                                <?= $no++ ?>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span style="font-size: 13.5px; font-weight: 800; color: #1E293B; letter-spacing: 0.3px;">WEEK <?= $log->week ?></span>
                                                    <?php if($is_recently_reviewed): ?>
                                                        <span style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-size: 9.5px; font-weight: 800; padding: 1.5px 6px; border-radius: 6px; display: inline-flex; align-items: center; gap: 3px;" title="Baru saja direview">
                                                            <i class="fa fa-check-circle"></i> Baru
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if($show_edit_indicator): ?>
                                                        <?php 
                                                        $alasan_arr = @json_decode($log->alasan_edit->alasan, true);
                                                        $alasan_html = '';
                                                        if (is_array($alasan_arr)) {
                                                            if (count($alasan_arr) > 1) {
                                                                $alasan_html = '<ul style="margin: 4px 0 0 16px; padding: 0; list-style-type: disc;">';
                                                                foreach ($alasan_arr as $al) {
                                                                    $alasan_html .= '<li style="margin-bottom: 2px;">' . htmlspecialchars($al, ENT_QUOTES) . '</li>';
                                                                }
                                                                $alasan_html .= '</ul>';
                                                            } else {
                                                                $alasan_html = htmlspecialchars($alasan_arr[0], ENT_QUOTES);
                                                            }
                                                        } else {
                                                            $alasan_html = htmlspecialchars($log->alasan_edit->alasan, ENT_QUOTES);
                                                        }
                                                        $tgl_edit = date('d M Y H:i', strtotime($log->alasan_edit->created_at));
                                                        ?>
                                                        <div class="custom-tooltip" style="display: inline-flex;">
                                                            <i class="fa fa-exclamation-circle" style="color: #EF4444; font-size: 15px; animation: blinker_edit 1.5s linear infinite; cursor: help;"></i>
                                                            <span class="tooltip-text">
                                                                <strong style="color: #F87171;">Diedit Setelah Direview</strong><br/>
                                                                <span style="color: #94A3B8; font-size: 11px;">Tanggal: <?= $tgl_edit ?></span><br/>
                                                                <span style="color: #E2E8F0; margin-top: 4px; display: block;">Alasan: <?= $alasan_html ?></span>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="font-size: 11.5px; color: #64748B; font-weight: 500; margin-top: 2px;">
                                                    <i class="fa fa-calendar-o" style="margin-right: 4px; color: #94A3B8;"></i><?= $log->periode_laporan ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="background: #EEF2FF; color: #4F46E5; font-size: 11.5px; font-weight: 800; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px;">
                                                    Terisi <?= $terisi_days ?>/<?= $obligatory_days ?> Hari
                                                </span>
                                                <?php if ($skipped > 0): ?>
                                                    <div style="margin-top: 3px;">
                                                        <span style="background: #F0FDF4; color: #15803D; border: 1px solid #86EFAC; font-size: 10px; font-weight: 700; padding: 1.5px 6px; border-radius: 6px; display: inline-block;" title="<?= $skipped ?> hari pertama dikecualikan karena mahasiswa belum mulai magang">&#10003; Mulai <?= ['Sen','Sel','Rab','Kam','Jum','Sab'][$skipped] ?? '' ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if(isset($log->is_copas) && $log->is_copas): ?>
                                                    <div style="margin-top: 3px;">
                                                        <span style="background: #FFF1F2; color: #E11D48; border: 1px solid #FDA4AF; font-size: 10px; font-weight: 700; padding: 1.5px 6px; border-radius: 6px; display: inline-block;" title="Isi laporan untuk setiap harinya sama persis (Copy-Paste)"><i class="fa fa-copy"></i> Indikasi Copas</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if(isset($log->is_late) && $log->is_late): ?>
                                                    <span style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-exclamation-circle"></i> Telat <?= $log->late_days ?> Hari
                                                    </span>
                                                <?php else: ?>
                                                    <span style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-check"></i> Tepat Waktu
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($nilai_text !== '-'): ?>
                                                    <span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-star" style="color: #F59E0B;"></i> <?= $nilai_text ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #94A3B8; font-weight: 700; font-size: 13px;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($has_review): ?>
                                                    <span style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-check-circle" style="color: #10B981;"></i> Sudah Direview
                                                    </span>
                                                    <?php if($is_recently_reviewed && !empty($log->change_date)): ?>
                                                        <?php
                                                        $diff_seconds = time() - strtotime($log->change_date);
                                                        $jam_lalu = floor($diff_seconds / 3600);
                                                        $menit_lalu = floor(($diff_seconds % 3600) / 60);
                                                        $waktu_str = ($jam_lalu > 0) ? $jam_lalu . " jam lalu" : (($menit_lalu > 0) ? $menit_lalu . " mnt lalu" : "baru saja");
                                                        ?>
                                                        <div style="font-size: 10px; color: #94A3B8; font-weight: 500; margin-top: 2px;">
                                                            <i class="fa fa-clock-o"></i> <?= $waktu_str ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-clock-o" style="color: #F59E0B;"></i> Belum Direview
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <?php if(isset($log->is_incomplete) && $log->is_incomplete): ?>
                                                        <a href="javascript:void(0)" onclick="openDetail('<?= site_url('C_review_laporan/detail/' . $log->id_tracking) ?>')" class="btn-review-action" title="Laporan hari ke-<?= implode(', ', $log->empty_days) ?> belum diisi penuh">
                                                            <i class="fa fa-eye"></i> Lihat
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="javascript:void(0)" onclick="openDetail('<?= site_url('C_review_laporan/detail/' . $log->id_tracking) ?>')" class="btn-review-action" title="Review Laporan Mahasiswa">
                                                            <i class="fa fa-pencil"></i> Review
                                                        </a>
                                                    <?php endif; ?>

                                                    <button type="button" <?php if ($has_foto): ?>onclick="openFotoModal('modalBerkas<?= $log->id_tracking ?>', '<?= $folder_id_map[$log->id_tracking] ?>')"<?php else: ?>disabled<?php endif; ?> class="btn-foto-action" <?= !$has_foto ? 'title="Belum ada link foto"' : 'title="Lihat Foto Kegiatan Magang"' ?>>
                                                        <i class="fa fa-picture-o"></i> Foto
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 40px 20px; color: #64748B;">
                                            <i class="fa fa-folder-open-o" style="font-size: 44px; margin-bottom: 10px; color: #94A3B8; display: block;"></i>
                                            <p style="font-weight: 700; margin: 0; font-size: 14px;">Belum ada data laporan mingguan untuk mahasiswa ini.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Berkas (Foto) di luar tabel agar DOM tabel valid -->
                    <?php if(!empty($logs)): ?>
                        <?php foreach($logs as $log): ?>
                            <div class="modal fade foto-modal" id="modalBerkas<?= $log->id_tracking ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1100px;">
                                    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                                        <div class="modal-header" style="background: linear-gradient(135deg, #1E293B, #334155); border-bottom: none; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">
                                            <h4 class="modal-title" style="font-weight: 800; color: #FFFFFF; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 10px;">
                                                <i class="fa fa-images" style="color: #34D399;"></i>
                                                Foto Kegiatan Magang — Week <?= $log->week ?>
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0; padding: 0; color: white; opacity: 0.7;">
                                                <span aria-hidden="true" style="font-size: 24px;">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body foto-modal-body" style="padding: 0; background: #F1F5F9; position: relative; width: 100%; height: 650px; overflow: hidden;">
                                            <!-- Skeleton Loading Animation -->
                                            <div class="foto-skeleton" id="skeleton<?= $log->id_tracking ?>">
                                                <div class="skeleton-header">
                                                    <div class="skeleton-icon pulse"></div>
                                                    <div>
                                                        <div class="skeleton-line pulse" style="width: 160px; height: 14px; margin-bottom: 6px;"></div>
                                                        <div class="skeleton-line pulse" style="width: 110px; height: 11px;"></div>
                                                    </div>
                                                </div>
                                                <div class="skeleton-grid">
                                                    <?php for($sk=0; $sk<8; $sk++): ?>
                                                    <div class="skeleton-card pulse"></div>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="skeleton-label">
                                                    <i class="fa fa-image"></i>
                                                    <span>Memuat foto dari Google Drive...</span>
                                                </div>
                                            </div>
                                            <!-- iframe container (diisi via JS) -->
                                            <div id="iframeContainer<?= $log->id_tracking ?>" style="display:none; width:100%; height:100%;"></div>
                                            <!-- No link fallback -->
                                            <?php if (empty($folder_id_map[$log->id_tracking])): ?>
                                            <div style="position:absolute;inset:0; padding: 40px; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; background: #F1F5F9;">
                                                <i class="fa fa-folder-open-o" style="font-size: 48px; color: #CBD5E1; margin-bottom: 16px;"></i>
                                                <h4 style="font-weight: 700; color: #475569; margin-bottom: 8px;">Tidak Ada Foto / Link Tidak Valid</h4>
                                                <p style="color: #64748B; font-size: 14px; margin-bottom: 0;">Mahasiswa tidak melampirkan tautan Google Drive yang valid untuk minggu ini.</p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E2E8F0; padding: 14px 24px;">
                                            <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 700; padding: 8px 16px;">Tutup</button>
                                            <?php if(!empty($log->link_google_drive)): ?>
                                                <a href="<?= htmlspecialchars($log->link_google_drive) ?>" target="_blank" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; background: #4F46E5; border: none; padding: 8px 16px;"><i class="fa fa-external-link"></i> Buka Google Drive</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Side: Student Profile Sidebar Panel -->
        <div class="sidebar-panel" style="background: #FFFFFF; border-radius: 16px; padding: 0; overflow: hidden; border: 1px solid #E5E7EB; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; flex-direction: column;">
            <div class="sidebar-cover" style="height: 80px; position: relative; background: linear-gradient(135deg, #4F46E5 0%, #312E81 100%);">
                <div style="position: absolute; top: 12px; left: 12px; z-index: 10; font-weight: 700; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 4px 10px; border-radius: 999px; font-size: 10px; text-transform: uppercase; background: #E0F2FE; color: #0284C7;">
                    <i class="fa fa-user" style="margin-right: 4px;"></i> MAHASISWA PKL
                </div>
            </div>
            
            <div class="avatar-wrapper" style="text-align: center; margin-top: -40px; position: relative; z-index: 5; margin-bottom: 8px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid #FFFFFF; box-shadow: 0 4px 10px rgba(0,0,0,0.12); display: inline-block; background: #F3F4F6;">
                    <?php 
                    $photo_thumb_src = !empty($foto_mhs) 
                        ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($foto_mhs) 
                        : base_url('assets/no_profile_photo.png');
                    ?>
                    <img src="<?= $photo_thumb_src ?>" 
                         alt="<?= htmlspecialchars($nm_pd) ?>"
                         onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
            
            <div class="sidebar-body" style="padding: 0 15px 15px 15px; text-align: center; flex: 1; display: flex; flex-direction: column;">
                <h3 style="font-size: 14px; font-weight: 800; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.3px; color: #111827; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($nm_pd) ?>"><?= $nm_pd ?></h3>
                <div style="font-size: 11px; color: #6B7280; margin: 0 0 10px 0; font-weight: 600;"><?= $nipd ?></div>
                
                <div style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 10px; font-weight: 700; margin: 0 auto 12px auto; text-transform: uppercase; background: #E0F2FE; color: #0284C7;">
                    <?= !empty($nm_prodi) ? $nm_prodi : 'PROGRAM STUDI' ?>
                </div>

                <div style="text-align: left; background: #F9FAFB; border-radius: 12px; padding: 10px 12px; font-size: 11.5px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 6px; flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #E5E7EB; padding-bottom: 4px;">
                        <span style="color: #6B7280; font-weight: 600;">🏢 Lokasi Magang</span>
                        <span style="font-weight: 700; color: #111827; text-align: right;"><?= !empty($nama_perusahaan) ? $nama_perusahaan : '-' ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #E5E7EB; padding-bottom: 4px;">
                        <span style="color: #6B7280; font-weight: 600;">📅 Waktu PKL</span>
                        <span style="font-weight: 700; color: #111827; text-align: right;">
                            <?php if (!empty($tanggal_mulai) && !empty($tanggal_selesai) && function_exists('tgl_indo_short')): ?>
                                <?= tgl_indo_short($tanggal_mulai) . ' - ' . tgl_indo_short($tanggal_selesai) ?>
                            <?php elseif (!empty($tanggal_mulai) && !empty($tanggal_selesai)): ?>
                                <?= date('d M Y', strtotime($tanggal_mulai)) . ' - ' . date('d M Y', strtotime($tanggal_selesai)) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0;">
                        <span style="color: #6B7280; font-weight: 600;">👨‍🏫 Dosen DPL</span>
                        <span style="font-weight: 700; color: #111827; text-align: right;"><?= !empty($nm_dpl) ? $nm_dpl : '-' ?></span>
                    </div>
                </div>
                
                <div style="margin-top: auto; border-top: 1px dashed #E5E7EB; padding-top: 15px; text-align: left;">
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: #4B5563; margin-bottom: 6px; text-transform: uppercase;">
                            <span>Progress Pengisian Laporan</span>
                            <span style="color: #4F46E5;"><?= isset($progress_pengisian) ? $progress_pengisian : 0 ?>%</span>
                        </div>
                        <div style="height: 6px; background: #E2E8F0; border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; background: #4F46E5; width: <?= isset($progress_pengisian) ? $progress_pengisian : 0 ?>%; border-radius: 4px; transition: width 0.5s ease-in-out;"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: #4B5563; margin-bottom: 6px; text-transform: uppercase;">
                            <span>Laporan Sudah Direview</span>
                            <span style="color: #10B981;"><?= isset($progress_review) ? $progress_review : 0 ?>%</span>
                        </div>
                        <div style="height: 6px; background: #E2E8F0; border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; background: #10B981; width: <?= isset($progress_review) ? $progress_review : 0 ?>%; border-radius: 4px; transition: width 0.5s ease-in-out;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function openFotoModal(modalId, folderId) {
    var $modal = $('#' + modalId);
    $modal.modal('show');

    // Only inject iframe if folder_id exists and not already injected
    if (!folderId || folderId === '') return;

    var container = document.getElementById('iframeContainer' + modalId.replace('modalBerkas', ''));
    var skeleton  = document.getElementById('skeleton'       + modalId.replace('modalBerkas', ''));

    if (!container || container.dataset.loaded === '1') return;

    // Inject iframe after a short delay so skeleton animation is visible
    setTimeout(function() {
        var iframeUrl = 'https://drive.google.com/embeddedfolderview?id=' + folderId + '#grid';
        var iframe = document.createElement('iframe');
        iframe.src = iframeUrl;
        iframe.style.cssText = 'width: 70%; height: 455px; transform: scale(1.4285); transform-origin: 0 0; border: none; position: relative; z-index: 2; background: transparent;';
        iframe.allowFullscreen = true;

        iframe.onload = function() {
            // Fade out skeleton, show iframe
            if (skeleton) {
                skeleton.style.transition = 'opacity 0.4s ease';
                skeleton.style.opacity = '0';
                setTimeout(function() {
                    skeleton.style.display = 'none';
                    container.style.display = 'block';
                }, 400);
            } else {
                container.style.display = 'block';
            }
        };

        container.appendChild(iframe);
        container.dataset.loaded = '1';
    }, 600); // 600ms delay agar skeleton terlihat sebelum request
}

// Reset iframe saat modal ditutup
$(document).on('hidden.bs.modal', '.foto-modal', function() {
    // Reset skeleton opacity jika modal dibuka kembali untuk modal berbeda
    // (tidak hapus iframe agar tidak reload ulang)
});
</script>