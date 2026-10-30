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

    /* Weekly Cards Grid */
    .weekly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
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
                        <a href="<?= site_url('C_laporan_magang') ?>" class="btn btn-default btn-sm" style="border-radius: 8px; font-weight: bold; background: #EEF2FF !important; color: #4F46E5 !important; border: 1px solid #C7D2FE !important;"><i class="fa fa-arrow-left"></i> Kembali</a>
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
                    <div class="weekly-grid">
                        <?php if(!empty($logs)): ?>
                            <?php foreach($logs as $log): ?>
                                <?php 
                                $has_review = !empty($log->review_dpl);
                                $score_badge_html = '';
                                if ($has_review) {
                                    if (preg_match('/Nilai Kelengkapan Laporan:\s*([0-9]+)\/100/i', strip_tags($log->review_dpl), $matches)) {
                                        $score_badge_html = '<span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 10.5px; font-weight: 800; padding: 3px 8px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;"><i class="fa fa-star" style="color:#F59E0B;"></i> Skor: ' . $matches[1] . '</span>';
                                    }
                                }
                                $badge_style = $has_review 
                                    ? 'background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;' 
                                    : 'background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;';
                                $badge_text = $has_review ? 'Sudah Direview' : 'Belum Direview';
                                ?>
                                <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 24px 20px; display: flex; flex-direction: column; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: all 0.25s ease; position: relative; gap: 14px;"
                                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.06)'; this.style.borderColor='#CBD5E1';" 
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.02)'; this.style.borderColor='#E5E7EB';">
                                     


                                    <!-- WEEK TITLE -->
                                    <h3 style="margin: 0; font-weight: 800; font-size: 15px; color: #1E293B; letter-spacing: 0.5px;">WEEK <?= $log->week ?></h3>
                                    
                                    <!-- DATES -->
                                    <div style="font-size: 11px; color: #64748B; font-weight: 600; margin-top: -6px;">
                                        <?= $log->periode_laporan ?>
                                    </div>
                                    
                                    <!-- PILL BADGES ROW -->
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                                        <?php
                                        $terisi_days = 6 - count($log->empty_days ?? []);
                                        ?>
                                        <span style="background: #EEF2FF; color: #4F46E5; font-size: 11.5px; font-weight: 800; padding: 4px 14px; border-radius: 8px;">Terisi <?= $terisi_days ?> / 6 Hari</span>
                                        
                                        <?php if(isset($log->is_copas) && $log->is_copas): ?>
                                            <span style="background: #FFF1F2; color: #E11D48; border: 1px solid #FDA4AF; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 8px;" title="Isi laporan untuk setiap harinya sama persis (Copy-Paste)"><i class="fa fa-copy"></i> Indikasi Copas</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- GREY BOX DETAILS -->
                                    <div style="background: #F8FAFC; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px; text-align: left; flex: 1;">
                                        
                                        <!-- ROW: KETERLAMBATAN -->
                                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #E2E8F0; padding-bottom: 10px;">
                                            <div style="font-size: 12.5px; color: #64748B; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                                <i class="fa fa-exclamation-circle" style="color: #EF4444; font-size: 14px;"></i> Keterlambatan
                                            </div>
                                            <?php if(isset($log->is_late) && $log->is_late): ?>
                                                <div style="font-size: 12.5px; font-weight: 800; color: #EF4444;">Telat <?= $log->late_days ?> Hari</div>
                                            <?php else: ?>
                                                <div style="font-size: 12.5px; font-weight: 800; color: #10B981;">Tepat Waktu</div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- ROW: NILAI SAYA -->
                                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #E2E8F0; padding-bottom: 10px;">
                                            <div style="font-size: 12.5px; color: #64748B; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                                <i class="fa fa-star" style="color: #F59E0B; font-size: 14px;"></i> Score
                                            </div>
                                            <?php 
                                                $nilai_text = '-';
                                                if ($has_review && preg_match('/Nilai Kelengkapan Laporan:\s*([0-9]+)\/100/i', strip_tags($log->review_dpl), $matches)) {
                                                    $nilai_text = $matches[1];
                                                }
                                            ?>
                                            <div style="font-size: 13.5px; font-weight: 800; color: #F59E0B;"><?= $nilai_text ?></div>
                                        </div>
                                        
                                        <!-- ROW: STATUS REVIEW -->
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="font-size: 12.5px; color: #64748B; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                                <i class="fa fa-check-square-o" style="color: #059669; font-size: 14px;"></i> Status
                                            </div>
                                            <div style="font-size: 11px; font-weight: 800; color: <?= $has_review ? '#059669' : '#D97706' ?>;">
                                                <?= $has_review ? 'Sudah Direview' : 'Belum Direview' ?>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <!-- BUTTONS -->
                                    <div style="margin-top: auto; display: flex; gap: 10px;">
                                        <?php if(isset($log->is_incomplete) && $log->is_incomplete): ?>
                                            <a href="<?= site_url('C_review_laporan/detail/' . $log->id_tracking) ?>" class="btn btn-sm" style="flex: 1; background: linear-gradient(135deg, #4F46E5, #3730A3) !important; color: white !important; border: none; border-radius: 8px; font-weight: 800; padding: 10px; box-shadow: 0 4px 10px rgba(79,70,229,0.15); transition: all 0.2s;" title="Laporan hari ke-<?= implode(', ', $log->empty_days) ?> belum diisi penuh">
                                                <i class="fa fa-eye" style="margin-right: 4px;"></i> Lihat
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= site_url('C_review_laporan/detail/' . $log->id_tracking) ?>" class="btn btn-sm" style="flex: 1; background: linear-gradient(135deg, #4F46E5, #3730A3) !important; color: white !important; border: none; border-radius: 8px; font-weight: 800; padding: 10px; box-shadow: 0 4px 10px rgba(79,70,229,0.15); transition: all 0.2s;">
                                                <i class="fa fa-eye" style="margin-right: 4px;"></i> Review
                                            </a>
                                        <?php endif; ?>
                                        <?php $has_foto = !empty($folder_id_map[$log->id_tracking]); ?>
                                        <button type="button" <?= $has_foto ? "onclick=\"openFotoModal('modalBerkas{$log->id_tracking}', '{$folder_id_map[$log->id_tracking]}')\"" : 'disabled' ?> class="btn btn-sm" style="flex: 1; background: <?= $has_foto ? '#FFFFFF' : '#F8FAFC' ?> !important; color: <?= $has_foto ? '#334155' : '#94A3B8' ?> !important; border: 1px solid <?= $has_foto ? '#CBD5E1' : '#E2E8F0' ?>; border-radius: 8px; font-weight: 800; padding: 10px; transition: all 0.2s; <?= !$has_foto ? 'cursor: not-allowed; opacity: 0.7;' : '' ?>" <?= !$has_foto ? 'title="Belum ada link foto"' : '' ?>>
                                            <i class="fa fa-picture-o" style="color: <?= $has_foto ? '#059669' : '#94A3B8' ?>; margin-right: 4px;"></i> Foto
                                        </button>
                                    </div>
                                    
                                </div>

                                <!-- Modal Berkas -->
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
                        <?php else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; color: #64748B; background: #FFFFFF; border-radius: 16px; border: 2px dashed #E2E8F0;">
                                <i class="fa fa-folder-open-o" style="font-size: 54px; margin-bottom: 12px; color: #94A3B8;"></i>
                                <p style="font-weight: 700; margin: 0; font-size: 15px;">Belum ada data laporan mingguan untuk mahasiswa ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
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