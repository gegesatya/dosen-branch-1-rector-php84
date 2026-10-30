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
    .skeleton-line { border-radius: 6px; }
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
        
        <!-- Left Side: Detail & Review form inside Metronic Portlet -->
        <div class="thread-panel">
            <div class="portlet light" style="margin-bottom: 0;">
                <div class="portlet-title" style="display: flex !important; justify-content: space-between !important; align-items: center !important; border-bottom: 1px solid #E5E7EB !important; padding-bottom: 12px !important; margin-bottom: 20px !important; width: 100% !important; float: none !important;">
                    <div class="caption" style="display: flex !important; flex-direction: column !important; gap: 2px !important; text-align: left !important; float: none !important; padding: 0 !important; margin: 0 !important;">
                        <span class="caption-subject font-green-sharp bold uppercase" style="font-weight: 800; font-size: 16px; color: #111827;">📅 Laporan Kegiatan - Week <?= $log->week ?> <small style="color: #6B7280;"><i class="fa fa-calendar"></i> <?= $log->periode_laporan ?></small></span>
                    </div>
                    <div class="actions" style="margin: 0 !important; float: none !important; padding: 0 !important;">
                        <a href="<?= site_url('C_review_laporan/index/' . $nipd) ?>" class="btn btn-default btn-sm" style="border-radius: 0; font-weight: bold; background: #EEF2FF !important; color: #4F46E5 !important; border: 1px solid #C7D2FE !important;"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                
                <div class="portlet-body">
                    
                    <!-- Daily Activities Navtabs -->
                    <style>
                        .nav-tabs.custom-tabs {
                            border-bottom: 2px solid #E2E8F0;
                            margin-bottom: 15px;
                        }
                        .nav-tabs.custom-tabs > li {
                            margin-bottom: -2px;
                        }
                        .nav-tabs.custom-tabs > li > a {
                            border: none;
                            color: #64748B;
                            font-weight: 600;
                            font-size: 13.5px;
                            padding: 10px 16px;
                            border-bottom: 3px solid transparent;
                            border-radius: 0;
                        }
                        .nav-tabs.custom-tabs > li > a:hover {
                            background: transparent;
                            color: #4F46E5;
                            border-bottom: 3px solid #C7D2FE;
                        }
                        .nav-tabs.custom-tabs > li.active > a,
                        .nav-tabs.custom-tabs > li.active > a:hover,
                        .nav-tabs.custom-tabs > li.active > a:focus {
                            background: transparent;
                            border: none;
                            color: #4F46E5;
                            border-bottom: 3px solid #4F46E5;
                        }
                    </style>
                    <div style="margin-bottom: 24px;">
                        <ul class="nav nav-tabs custom-tabs">
                            <?php for($i=1; $i<=6; $i++): ?>
                                <li class="<?= $i == 1 ? 'active' : '' ?>">
                                    <a href="#tab_hari_<?= $i ?>" data-toggle="tab">Hari <?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                        <div class="tab-content" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0; padding: 20px; border-left: 4px solid #4F46E5;">
                            <?php for($i=1; $i<=6; $i++): 
                                $key = 'kegiatan_hari_' . $i;
                                $raw_val = $log->$key ?? '';
                                $is_json = false;
                                $js = @json_decode($raw_val, true);
                                if (is_array($js) && (array_key_exists('pagi', $js) || array_key_exists('siang', $js) || array_key_exists('sore', $js) || array_key_exists('tanggal_kegiatan', $js))) {
                                    $is_json = true;
                                }
                            ?>
                                <?php
                                $isEmpty = function($html) {
                                    if (empty($html)) return true;
                                    $clean = trim(strip_tags(str_replace(['&nbsp;', '<p>', '</p>', '<br>', '<br/>', "\r", "\n", "\t"], '', $html)));
                                    return empty($clean);
                                };
                                $pagi_teks = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
                                $pagi_created = is_array($js['pagi'] ?? null) ? ($js['pagi']['created_at'] ?? '') : '';
                                $pagi_updated = is_array($js['pagi'] ?? null) ? ($js['pagi']['updated_at'] ?? '') : '';
                                
                                $siang_teks = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
                                $siang_created = is_array($js['siang'] ?? null) ? ($js['siang']['created_at'] ?? '') : '';
                                $siang_updated = is_array($js['siang'] ?? null) ? ($js['siang']['updated_at'] ?? '') : '';
                                
                                $sore_teks = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
                                $sore_created = is_array($js['sore'] ?? null) ? ($js['sore']['created_at'] ?? '') : '';
                                $sore_updated = is_array($js['sore'] ?? null) ? ($js['sore']['updated_at'] ?? '') : '';
                                
                                $tanggal_kegiatan = $js['tanggal_kegiatan'] ?? '';
                                
                                $late_warning = '';
                                if ($is_json && !empty($tanggal_kegiatan)) {
                                    $created_dates = [];
                                    if (!empty($pagi_created)) $created_dates[] = strtotime($pagi_created);
                                    if (!empty($siang_created)) $created_dates[] = strtotime($siang_created);
                                    if (!empty($sore_created)) $created_dates[] = strtotime($sore_created);
                                    
                                    if (count($created_dates) > 0) {
                                        $max_created = max($created_dates);
                                        $bulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
                                        $bulanEng = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                        $tgl_parsed = str_replace($bulanIndo, $bulanEng, $tanggal_kegiatan);
                                        $tgl_parsed = preg_replace('/^[a-zA-Z]+,\s*/', '', $tgl_parsed);
                                        $time_kegiatan = strtotime($tgl_parsed);
                                        
                                        if ($time_kegiatan) {
                                            $diff_days = floor(($max_created - $time_kegiatan) / 86400);
                                            if ($diff_days > 2) {
                                                $late_warning = '<div style="margin-top: 12px; padding: 10px; background: #FEF2F2; border: 1px dashed #FCA5A5; border-radius: 8px; color: #DC2626; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa fa-exclamation-triangle" style="font-size: 14px;"></i> Peringatan: Laporan hari ini diisi terlambat (' . $diff_days . ' hari setelah kegiatan).</div>';
                                            }
                                        }
                                    }
                                }
                                ?>
                                <div class="tab-pane fade <?= $i == 1 ? 'active in' : '' ?>" id="tab_hari_<?= $i ?>">
                                    <h5 style="margin: 0 0 10px 0; font-weight: 800; font-size: 13px; color: #1E293B; text-transform: uppercase; letter-spacing: 0.5px;">Kegiatan Hari Ke-<?= $i ?><?= !empty($tanggal_kegiatan) ? " ($tanggal_kegiatan)" : "" ?></h5>
                                    <div style="font-size: 13.5px; line-height: 1.7; color: #334155; font-weight: 500;">
                                        <?php if(empty($raw_val) || (!$is_json && $isEmpty($raw_val)) || ($is_json && $isEmpty($pagi_teks) && $isEmpty($siang_teks) && $isEmpty($sore_teks))): ?>
                                            <span style="font-style: italic; color: #94A3B8;">Tidak ada kegiatan dicatat.</span>
                                        <?php elseif($is_json): ?>
                                            <div style="margin-bottom: 12px; padding: 12px; background: #FFFBEB; border-left: 4px solid #F59E0B;">
                                                <strong style="color: #B45309; display: block; margin-bottom: 4px;">🌅 Pagi : 08.00 sd 11.00</strong>
                                                <div style="word-wrap: break-word;"><?= !$isEmpty($pagi_teks) ? htmlspecialchars_decode($pagi_teks) : '<span style="font-style: italic; color: #D97706;">Kosong</span>' ?></div>
                                                <?php if(!empty($pagi_created)): ?>
                                                    <div style="margin-top: 6px; color: #B45309; font-size: 11px; opacity: 0.8; font-weight: 600;">
                                                        <small><i class="fa fa-clock-o"></i> Ditambahkan: <?= date('d/m/Y H:i', strtotime($pagi_created)) ?></small>
                                                        <?php if(!empty($pagi_updated) && $pagi_updated !== $pagi_created): ?>
                                                            <small style="margin-left: 8px;"><i class="fa fa-pencil"></i> Diubah: <?= date('d/m/Y H:i', strtotime($pagi_updated)) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="margin-bottom: 12px; padding: 12px; background: #ECFEFF; border-left: 4px solid #06B6D4;">
                                                <strong style="color: #0E7490; display: block; margin-bottom: 4px;">☀️ Siang : 11:00 sd 14:00</strong>
                                                <div style="word-wrap: break-word;"><?= !$isEmpty($siang_teks) ? htmlspecialchars_decode($siang_teks) : '<span style="font-style: italic; color: #0891B2;">Kosong</span>' ?></div>
                                                <?php if(!empty($siang_created)): ?>
                                                    <div style="margin-top: 6px; color: #0E7490; font-size: 11px; opacity: 0.8; font-weight: 600;">
                                                        <small><i class="fa fa-clock-o"></i> Ditambahkan: <?= date('d/m/Y H:i', strtotime($siang_created)) ?></small>
                                                        <?php if(!empty($siang_updated) && $siang_updated !== $siang_created): ?>
                                                            <small style="margin-left: 8px;"><i class="fa fa-pencil"></i> Diubah: <?= date('d/m/Y H:i', strtotime($siang_updated)) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="padding: 12px; background: #F5F3FF; border-left: 4px solid #8B5CF6;">
                                                <strong style="color: #6D28D9; display: block; margin-bottom: 4px;">🌆 Sore : 14:00 sd 17:00</strong>
                                                <div style="word-wrap: break-word;"><?= !$isEmpty($sore_teks) ? htmlspecialchars_decode($sore_teks) : '<span style="font-style: italic; color: #7C3AED;">Kosong</span>' ?></div>
                                                <?php if(!empty($sore_created)): ?>
                                                    <div style="margin-top: 6px; color: #6D28D9; font-size: 11px; opacity: 0.8; font-weight: 600;">
                                                        <small><i class="fa fa-clock-o"></i> Ditambahkan: <?= date('d/m/Y H:i', strtotime($sore_created)) ?></small>
                                                        <?php if(!empty($sore_updated) && $sore_updated !== $sore_created): ?>
                                                            <small style="margin-left: 8px;"><i class="fa fa-pencil"></i> Diubah: <?= date('d/m/Y H:i', strtotime($sore_updated)) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?= $late_warning ?>
                                        <?php else: ?>
                                            <div style="word-wrap: break-word;"><?= $raw_val ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Reflections -->
                    <h4 style="font-weight: 800; font-size: 15px; color: #1E293B; margin-top: 30px; margin-bottom: 16px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">🤔 Refleksi &amp; Pengetahuan Baru</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div style="background: #F0FDF4; border: 1px solid #DCFCE7; border-radius: 0; padding: 15px; border-left: 4px solid #10B981;">
                            <h5 style="margin: 0 0 6px 0; font-weight: 800; font-size: 12.5px; color: #14532D; text-transform: uppercase;">Pengetahuan Baru yang Diperoleh</h5>
                            <div style="font-size: 13.5px; line-height: 1.6; color: #166534; font-weight: 500;">
                                <?= !empty($log->pengetahuan_baru) ? $log->pengetahuan_baru : '<span style="font-style: italic; color: #86EFAC;">Belum dicatat.</span>' ?>
                            </div>
                        </div>
                        <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 0; padding: 15px; border-left: 4px solid #3B82F6;">
                            <h5 style="margin: 0 0 6px 0; font-weight: 800; font-size: 12.5px; color: #1E40AF; text-transform: uppercase;">Keterampilan Baru yang Diperoleh</h5>
                            <div style="font-size: 13.5px; line-height: 1.6; color: #1E40AF; font-weight: 500;">
                                <?= !empty($log->keterampilan_baru) ? $log->keterampilan_baru : '<span style="font-style: italic; color: #93C5FD;">Belum dicatat.</span>' ?>
                            </div>
                        </div>
                        <div style="background: #FEF3C7; border: 1px solid #FEEB9A; border-radius: 0; padding: 15px; border-left: 4px solid #D97706;">
                            <h5 style="margin: 0 0 6px 0; font-weight: 800; font-size: 12.5px; color: #78350F; text-transform: uppercase;">Tujuan Internship yang Dicapai</h5>
                            <div style="font-size: 13.5px; line-height: 1.6; color: #78350F; font-weight: 500;">
                                <?= !empty($log->tujuan_internship) ? $log->tujuan_internship : '<span style="font-style: italic; color: #FCD34D;">Belum dicatat.</span>' ?>
                            </div>
                        </div>
                        <?php if(!empty($log->catatan_lain)): ?>
                        <div style="background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 0; padding: 15px; border-left: 4px solid #64748B;">
                            <h5 style="margin: 0 0 6px 0; font-weight: 800; font-size: 12.5px; color: #334155; text-transform: uppercase;">Catatan Lain-lain</h5>
                            <div style="font-size: 13.5px; line-height: 1.6; color: #334155; font-weight: 500;">
                                <?= $log->catatan_lain ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                        
                    <!-- Data Pendukung (Gdrive Link) -->
                    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 0; padding: 15px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                        <div style="font-weight: 700; font-size: 13px; color: #374151;">Link Data Pendukung (Gdrive):</div>
                        <div>
                            <?php
                            $folder_id = '';
                            $has_link = !empty($log->link_google_drive);
                            if ($has_link) {
                                if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $log->link_google_drive, $matches)) {
                                    $folder_id = $matches[1];
                                } elseif (preg_match('/id=([a-zA-Z0-9_-]+)/', $log->link_google_drive, $matches)) {
                                    $folder_id = $matches[1];
                                }
                            }
                            $has_folder = !empty($folder_id);
                            ?>
                            
                            <button type="button" <?= $has_folder ? 'onclick="openDetailFotoModal()"' : 'disabled' ?> class="btn btn-sm" style="font-weight: bold; border-radius: 8px; background-color: <?= $has_folder ? '#10B981' : '#F1F5F9' ?> !important; color: <?= $has_folder ? 'white' : '#94A3B8' ?> !important; border: <?= $has_folder ? 'none' : '1px solid #E2E8F0' ?> !important; margin-right: 8px; box-shadow: <?= $has_folder ? '0 4px 10px rgba(16,185,129,0.15)' : 'none' ?>; <?= !$has_folder ? 'cursor: not-allowed;' : '' ?>" <?= !$has_folder ? 'title="Belum ada link foto"' : '' ?>>
                                <i class="fa fa-picture-o" <?= !$has_folder ? 'style="color: #94A3B8;"' : '' ?>></i> Foto Kegiatan
                            </button>

                            <?php if ($has_link): ?>
                                <a href="<?= $log->link_google_drive ?>" target="_blank" class="btn btn-sm btn-primary" style="font-weight: bold; border-radius: 8px; background-color: #4F46E5 !important; color: white !important; border: none !important; box-shadow: 0 4px 10px rgba(79,70,229,0.15);">
                                    <i class="fa fa-external-link"></i> Buka Link Original
                                </a>
                            <?php else: ?>
                                <button type="button" disabled class="btn btn-sm btn-primary" style="font-weight: bold; border-radius: 8px; background-color: #F1F5F9 !important; color: #94A3B8 !important; border: 1px solid #E2E8F0 !important; box-shadow: none; cursor: not-allowed;" title="Belum ada link">
                                    <i class="fa fa-external-link" style="color: #94A3B8;"></i> Buka Link Original
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if(isset($log->is_incomplete) && $log->is_incomplete): ?>
                        <div style="margin-top: 40px; padding: 20px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; display: flex; align-items: flex-start; gap: 12px; color: #B45309;">
                            <i class="fa fa-info-circle" style="font-size: 24px; color: #F59E0B;"></i>
                            <div>
                                <h4 style="margin: 0 0 6px 0; font-weight: 800; font-size: 15px;">Laporan Belum Lengkap</h4>
                                <p style="margin: 0; font-size: 13.5px;">Mahasiswa belum mengisi penuh laporan untuk minggu ini. Form review dan penilaian dari DPL baru akan tersedia setelah mahasiswa melengkapi seluruh harinya.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- DPL Review Section (Form & AI Review) -->
                        <h4 style="font-weight: 800; font-size: 16px; color: #1E293B; margin-top: 40px; margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span>📝 Form Review &amp; Penilaian DPL</span>
                        </h4>

                        <!-- AI Review Trigger Button -->
                        <button type="button" id="btn_ai_review" class="btn btn-block" style="background: linear-gradient(135deg, #7C3AED, #4F46E5) !important; color: white !important; border: none !important; font-weight: 800; padding: 12px; border-radius: 0; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(124,58,237,0.15); transition: all 0.25s ease; display: flex; align-items: center; justify-content: center; gap: 8px;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(124,58,237,0.25)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(124,58,237,0.15)';">
                            <i class="fa fa-magic" style="font-size: 14px;"></i> AI Review Laporan
                        </button>

                        <!-- AI Review feedback inline card (hidden by default) -->
                        <div id="ai_review_feedback_box" style="display: none; margin-bottom: 20px; padding: 18px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0; box-shadow: 0 4px 8px rgba(0,0,0,0.02);">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px solid #E2E8F0; padding-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #4F46E5; font-weight: 800; font-size: 13.5px;">
                                    <i class="fa fa-magic"></i>
                                    <span>Analisis AI Laporan Mingguan</span>
                                </div>
                                <span id="ai_score_badge" class="badge" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-weight: 800; font-size: 11px; padding: 3px 8px; border-radius: 0;">Skor: -</span>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 11.5px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Analisis &amp; Kesesuaian</h5>
                                <p id="ai_evaluasi_analisis" style="font-size: 13px; color: #334155; margin: 0; line-height: 1.5; font-weight: 500;"></p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 11.5px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Detail Poin Evaluasi</h5>
                                <div id="ai_feedback_points" style="font-size: 13px; color: #334155; line-height: 1.6;"></div>
                            </div>
                            <div style="margin-bottom: 15px; background: #F1F5F9; padding: 12px; border-radius: 0; border-left: 4px solid #4F46E5;">
                                <h5 style="margin: 0 0 6px 0; font-weight: 800; font-size: 11.5px; color: #1E293B; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
                                    <span>📝</span> Draf Review Rekomendasi
                                </h5>
                                <p id="ai_rekomendasi_review" style="font-size: 13px; font-style: italic; color: #334155; margin: 0; line-height: 1.5; font-weight: 500;"></p>
                            </div>
                            <div style="text-align: right;">
                                <button type="button" id="btn_apply_ai_review" class="btn btn-xs btn-success" style="font-weight: bold; background-color: #10B981 !important; border-color: #10B981 !important; padding: 5px 12px; border-radius: 0; display: inline-flex; align-items: center; gap: 4px; color: white !important;">
                                    <i class="fa fa-check"></i> Gunakan Review Ini
                                </button>
                            </div>
                        </div>

                        <!-- Review Input Form -->
                        <form action="<?= site_url('C_review_laporan/save_review') ?>" method="POST" id="form_review_dpl">
                            <input type="hidden" name="id_tracking" value="<?= $log->id_tracking ?>">
                            <input type="hidden" name="nipd" value="<?= $log->nipd ?>">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                            
                            <div class="form-group" style="margin-bottom: 20px;">
                                <textarea class="form-control summernote-review" id="review_dpl" name="review_dpl"><?= $log->review_dpl ?></textarea>
                            </div>
                            
                            <div style="display: flex; gap: 12px; margin-top: 15px;">
                                <a href="<?= site_url('C_review_laporan/index/' . $log->nipd) ?>" class="btn btn-default" style="flex: 1; border-radius: 0; font-weight: bold; padding: 11px; text-align: center; background: #F3F4F6 !important; color: #4B5563 !important; border: 1px solid #D1D5DB !important;">Batal</a>
                                <button type="submit" class="btn btn-success" style="flex: 1; background-color: #10B981 !important; border-color: #10B981 !important; color: white !important; border-radius: 0; font-weight: bold; padding: 11px;">Simpan Review</button>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Right Side: Student Profile Sidebar Panel -->
        <div class="sidebar-panel">
            <div class="sidebar-cover"></div>
            
            <div class="avatar-wrapper" style="text-align: center; margin-top: -40px; position: relative; z-index: 5; margin-bottom: 8px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid #FFFFFF; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: inline-block; background: #F3F4F6;">
                    <?php 
                    $photo_thumb_src = !empty($foto_mhs) 
                        ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($foto_mhs) 
                        : base_url('assets/no_profile_photo.png');
                    ?>
                    <img src="<?= $photo_thumb_src ?>" 
                         alt="<?= $nm_pd ?>"
                         onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';"
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
            
            <div class="sidebar-body">
                <h3 class="sidebar-student-name"><?= $nm_pd ?></h3>
                <div class="sidebar-nim">NIM: <?= $nipd ?></div>
                
                <div class="sidebar-footer">
                    <div class="sidebar-meta-item">
                        <div class="sidebar-meta-icon">🎓</div>
                        <div class="sidebar-meta-text">
                            <div class="sidebar-meta-label">Program Studi</div>
                            <div class="sidebar-meta-value"><?= !empty($nm_prodi) ? $nm_prodi : '-' ?></div>
                        </div>
                    </div>
                    
                    <div class="sidebar-meta-item">
                        <div class="sidebar-meta-icon">🏢</div>
                        <div class="sidebar-meta-text">
                            <div class="sidebar-meta-label">Lokasi Magang</div>
                            <div class="sidebar-meta-value"><?= !empty($nama_perusahaan) ? $nama_perusahaan : '-' ?></div>
                        </div>
                    </div>
                    
                    <div class="sidebar-meta-item">
                        <div class="sidebar-meta-icon">👨‍🏫</div>
                        <div class="sidebar-meta-text">
                            <div class="sidebar-meta-label">Dosen Pembimbing Lapangan</div>
                            <div class="sidebar-meta-value"><?= !empty($nm_dpl) ? $nm_dpl : '-' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Foto Kegiatan (Lazy Load) -->
<?php if (!empty($folder_id)): ?>
<div class="modal fade" id="modalDrivePreview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1100px;">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
      <!-- Header -->
      <div class="modal-header" style="background: linear-gradient(135deg, #1E293B, #334155); border-bottom: none; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">
        <h4 class="modal-title" style="font-weight: 800; color: #FFFFFF; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 10px;">
          <i class="fa fa-picture-o" style="color: #34D399;"></i>
          Foto Kegiatan Magang &mdash; Week <?= $log->week ?>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0; padding: 0; color: white; opacity: 0.7;">
          <span aria-hidden="true" style="font-size: 24px;">&times;</span>
        </button>
      </div>
      <!-- Body: skeleton + lazy iframe -->
      <div class="modal-body" style="padding: 0; background: #F1F5F9; position: relative; width: 100%; height: 650px; overflow: hidden;">
        <!-- Skeleton Loading -->
        <div class="foto-skeleton" id="detailFotoSkeleton">
          <div class="skeleton-header">
            <div class="skeleton-icon pulse"></div>
            <div>
              <div class="skeleton-line pulse" style="width: 160px; height: 14px; margin-bottom: 6px;"></div>
              <div class="skeleton-line pulse" style="width: 110px; height: 11px;"></div>
            </div>
          </div>
          <div class="skeleton-grid">
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
            <div class="skeleton-card pulse"></div>
          </div>
          <div class="skeleton-label">
            <i class="fa fa-image"></i>
            <span>Memuat foto dari Google Drive...</span>
          </div>
        </div>
        <!-- Iframe container (diisi via JS) -->
        <div id="detailIframeContainer" style="display:none; width:100%; height:100%;"></div>
      </div>
      <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E2E8F0; padding: 14px 24px;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 700; padding: 8px 16px;">Tutup</button>
        <a href="<?= $log->link_google_drive ?>" target="_blank" class="btn btn-primary" style="border-radius: 8px; font-weight: 700; background: #4F46E5; border: none; padding: 8px 16px;">
          <i class="fa fa-external-link"></i> Buka Google Drive
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
var detailFotoLoaded = false;
function openDetailFotoModal() {
    $('#modalDrivePreview').modal('show');
    if (detailFotoLoaded) return;

    var folderId = '<?= $folder_id ?>';
    if (!folderId) return;

    setTimeout(function() {
        var container = document.getElementById('detailIframeContainer');
        var skeleton  = document.getElementById('detailFotoSkeleton');

        var iframe = document.createElement('iframe');
        iframe.src = 'https://drive.google.com/embeddedfolderview?id=' + folderId + '#grid';
        iframe.style.cssText = 'width: 70%; height: 455px; transform: scale(1.4285); transform-origin: 0 0; border: none; position: relative; z-index: 2; background: transparent;';
        iframe.allowFullscreen = true;

        iframe.onload = function() {
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
            detailFotoLoaded = true;
        };

        container.appendChild(iframe);
    }, 600);
}
</script>

