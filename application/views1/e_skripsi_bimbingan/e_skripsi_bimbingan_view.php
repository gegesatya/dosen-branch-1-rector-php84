<?php
if (!function_exists('time_ago')) {
    function time_ago($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }
}
?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .bimbingan-dashboard {
        --primary: #4F46E5;
        --primary-hover: #4338CA;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --background: #F3F4F6;
        --surface: #FFFFFF;
        --text-main: #111827;
        --text-muted: #6B7280;
        --border: #E5E7EB;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-main);
    }

    .bimbingan-dashboard * { box-sizing: border-box; }

    /* Override Bootstrap/Metronic clearfixes inside flex header */
    .bimbingan-dashboard.portlet .portlet-title {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        border-bottom: 1px solid #E5E7EB !important;
        padding-bottom: 12px !important;
        margin-bottom: 20px !important;
        width: 100% !important;
        float: none !important;
        min-height: auto !important;
        height: auto !important;
    }
    .bimbingan-dashboard.portlet .portlet-title::before,
    .bimbingan-dashboard.portlet .portlet-title::after {
        display: none !important;
    }
    .bimbingan-dashboard.portlet .portlet-title .caption {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
        text-align: left !important;
        float: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .bimbingan-dashboard.portlet .portlet-title .actions {
        margin: 0 !important;
        padding: 0 !important;
        float: none !important;
        display: flex !important;
        align-items: center !important;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: var(--surface);
        padding: 20px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .summary-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .icon.all { background: #EEF2FF; color: var(--primary); }
    .icon.safe { background: #D1FAE5; color: var(--success); }
    .icon.warning { background: #FEF3C7; color: var(--warning); }
    .icon.danger { background: #FEE2E2; color: var(--danger); }

    .summary-card .info { flex: 1; }
    .summary-card .info span { display: block; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
    .summary-card .info strong { display: block; font-size: 24px; font-weight: 800; }

    /* Student Grid 4 Cols */
    .student-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    /* Student Grid 5 Cols */
    .student-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
    }
    
    /* Student Grid 6 Cols */
    .student-grid-6 {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }

    @media (max-width: 1600px) {
        .student-grid-6 { grid-template-columns: repeat(5, 1fr); }
        .student-grid-5 { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 1440px) {
        .summary-cards { grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .student-grid-6 { grid-template-columns: repeat(4, 1fr); }
        .student-grid-5, .student-grid-4 { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 1200px) {
        .summary-cards { grid-template-columns: repeat(3, 1fr); }
        .student-grid-6 { grid-template-columns: repeat(3, 1fr); }
        .student-grid-4, .student-grid-5 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 992px) {
        .summary-cards { grid-template-columns: repeat(2, 1fr); }
        .student-grid-6 { grid-template-columns: repeat(2, 1fr); }
        .student-grid-4, .student-grid-5 { grid-template-columns: repeat(1, 1fr); }
    }
    @media (max-width: 768px) {
        .summary-cards { grid-template-columns: 1fr; }
        .student-grid-4, .student-grid-5, .student-grid-6 { grid-template-columns: 1fr; }
    }

    .student-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 0;
        overflow: hidden;
        border: 1px solid var(--border);
        position: relative;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .student-card:hover { border-color: #D1D5DB; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08); }

    .student-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .student-info h3 { font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0 0 4px 0; }
    .student-info p { font-size: 13px; color: var(--text-muted); margin:0; }

    .badge { padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .badge.safe { background: #D1FAE5; color: #065F46; }
    .badge.warning { background: #FEF3C7; color: #92400E; }
    .badge.danger { background: #FEE2E2; color: #991B1B; }
    .badge.neutral { background: #F3F4F6; color: #4B5563; border: 1px solid #D1D5DB; }

    /* Small Card Overrides */
    .student-card-small .card-header-band { height: 75px !important; }
    .student-card-small .card-avatar-wrapper { margin-top: -38px !important; margin-bottom: 8px !important; }
    .student-card-small .card-avatar-wrapper div { width: 70px !important; height: 70px !important; border-width: 3px !important; }
    .student-card-small .card-student-body { padding: 0 15px 15px 15px !important; }
    .student-card-small .student-info h3 { font-size: 14.5px !important; }
    .student-card-small .student-info p { font-size: 11px !important; }
    .student-card-small .progress-header span { font-size: 11px !important; }
    .student-card-small .btn-action { padding: 8px 12px !important; font-size: 12px !important; }

    /* XSmall Card Overrides for Lulus */
    .student-card-xsmall .card-header-band { height: 60px !important; }
    .student-card-xsmall .card-avatar-wrapper { margin-top: -30px !important; margin-bottom: 8px !important; }
    .student-card-xsmall .card-avatar-wrapper div { width: 50px !important; height: 50px !important; border-width: 2px !important; }
    .student-card-xsmall .card-student-body { padding: 0 12px 12px 12px !important; }
    .student-card-xsmall .student-info h3 { font-size: 13px !important; }
    .student-card-xsmall .student-info p { font-size: 11px !important; }
    .student-card-xsmall .progress-header span { font-size: 10px !important; }
    .student-card-xsmall .btn-action { padding: 6px 10px !important; font-size: 11px !important; }

    .progress-section { margin-bottom: 20px; }
    .progress-header { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: var(--text-muted); font-weight: 600; }
    .progress-bar-bg { height: 6px; background: var(--border); border-radius: 999px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: var(--primary); border-radius: 999px; }

    .card-actions { display: flex; gap: 10px; }
    .btn-action {
        flex: 1; padding: 10px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; text-align: center; border: none; text-decoration: none; cursor:pointer;
    }
    .btn-action.primary { background: var(--primary); color: white; }
    .btn-action.primary:hover { background: var(--primary-hover); color: white; text-decoration: none;}

    .student-card.has-pending {
        background: linear-gradient(135deg, #FFFFFF 0%, #EEF2FF 100%);
        border-color: #818CF8;
        box-shadow: 0 4px 15px -3px rgba(79, 70, 229, 0.15);
    }
    .student-card.has-pending:hover {
        border-color: #4F46E5;
        box-shadow: 0 10px 20px -3px rgba(79, 70, 229, 0.25);
    }
    .badge.pending-badge {
        background: #F59E0B;
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        animation: pulse-glow 2s infinite;
    }
    @keyframes pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.6); }
        70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    @keyframes pulse-glow-indigo {
        0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.6); }
        70% { box-shadow: 0 0 0 8px rgba(99, 102, 241, 0); }
        100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
    }
    .pending-icon {
        font-size: 18px;
        animation: swing 1s ease-in-out infinite alternate;
        display: inline-block;
        transform-origin: top center;
    }
    @keyframes swing {
        0% { transform: rotate(-15deg); }
        100% { transform: rotate(15deg); }
    }

    /* Knowledge Base Modal Styling */
    .kb-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-top: 24px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid #E5E7EB;
        padding-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .kb-section-title:first-of-type {
        margin-top: 0;
    }
    .kb-workflow {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
        position: relative;
        padding-left: 20px;
    }
    .kb-workflow::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #E5E7EB;
    }
    .kb-step {
        position: relative;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .kb-step-number {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #4F46E5;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        position: absolute;
        left: -19px;
        top: 3px;
        border: 2px solid #FFFFFF;
        box-shadow: 0 0 0 2px #4F46E5;
    }
    .kb-step-content {
        background: #FFFFFF;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        flex: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .kb-step-content h5 {
        margin: 0 0 4px 0;
        font-size: 13px;
        font-weight: 700;
        color: #1F2937;
    }
    .kb-step-content p {
        margin: 0;
        font-size: 11.5px;
        color: #4B5563;
        line-height: 1.5;
    }

    /* Metric & Formula styles */
    .kb-grid-metrics {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .kb-grid-metrics {
            grid-template-columns: 1fr;
        }
    }
    .kb-metric-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .kb-metric-card h4 {
        margin: 0 0 10px 0;
        font-size: 13.5px;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kb-metric-card p {
        margin: 0 0 12px 0;
        font-size: 11.5px;
        color: #4B5563;
        line-height: 1.5;
    }
    .kb-formula-container {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .kb-formula-item {
        font-size: 11px;
        padding: 6px 10px;
        background: #F3F4F6;
        border-radius: 6px;
        font-weight: 600;
        color: #374151;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .kb-formula-item.penalty {
        background: #FEE2E2;
        color: #991B1B;
    }

    /* Status card styles */
    .kb-status-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 10px;
    }
    @media (max-width: 768px) {
        .kb-status-grid {
            grid-template-columns: 1fr;
        }
    }
    .kb-status-card {
        background: #FFFFFF;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        padding: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-left: 5px solid #E5E7EB;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .kb-status-card.unstarted { border-left-color: #2a93e2; }
    .kb-status-card.safe { border-left-color: #087f46; }
    .kb-status-card.warning { border-left-color: #c78c1a; }
    .kb-status-card.danger { border-left-color: #c51f42; }

    .kb-status-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2px;
    }
    .kb-status-header h5 {
        margin: 0;
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .kb-status-card.unstarted h5 { color: #2a93e2; }
    .kb-status-card.safe h5 { color: #087f46; }
    .kb-status-card.warning h5 { color: #c78c1a; }
    .kb-status-card.danger h5 { color: #c51f42; }
    
    .kb-status-badge {
        font-size: 9.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .kb-status-card.unstarted .kb-status-badge { background: #EEF2FF; color: #2a93e2; }
    .kb-status-card.safe .kb-status-badge { background: #E6FBF0; color: #087f46; }
    .kb-status-card.warning .kb-status-badge { background: #FEF3C7; color: #c78c1a; }
    .kb-status-card.danger .kb-status-badge { background: #FEE2E2; color: #c51f42; }

    .kb-status-card p {
        margin: 0;
        font-size: 11.5px;
        color: #4B5563;
        line-height: 1.5;
    }
    .kb-status-action {
        font-size: 11px;
        background: #F9FAFB;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px dashed #D1D5DB;
        margin-top: 6px;
        color: #374151;
    }
    .kb-status-action strong {
        color: #1F2937;
    }

    /* Simulation Case Study styling */
    .kb-cases-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }
    @media (max-width: 768px) {
        .kb-cases-container {
            grid-template-columns: 1fr;
        }
    }
    .kb-case-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-top: 4px solid #c51f42; /* Red band for Danger case examples */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kb-case-card h5 {
        margin: 0 0 10px 0;
        font-size: 13px;
        font-weight: 800;
        color: #c51f42;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .kb-case-details {
        font-size: 11.5px;
        color: #4B5563;
        margin-bottom: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .kb-case-details span {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed #F3F4F6;
        padding-bottom: 4px;
    }
    .kb-case-details span strong {
        color: #1F2937;
    }
    .kb-case-calc {
        background: #F3F4F6;
        padding: 8px 12px;
        border-radius: 8px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11.5px;
        color: #1F2937;
        margin-bottom: 10px;
        text-align: center;
        font-weight: 700;
        border: 1px dashed #D1D5DB;
    }
    .kb-case-status {
        font-size: 11px;
        background: #FEE2E2;
        color: #991B1B;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        line-height: 1.4;
        border: 1px solid #FCA5A5;
    }
</style>

<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bimbingan-dashboard">
                <div class="portlet-title" style="display: flex !important; justify-content: space-between !important; align-items: center !important; border-bottom: 1px solid #E5E7EB !important; padding-bottom: 12px !important; margin-bottom: 20px !important; width: 100% !important; float: none !important;">
                    <div class="caption" style="display: flex !important; flex-direction: column !important; gap: 2px !important; text-align: left !important; float: none !important; padding: 0 !important;">
                        <span class="caption-subject font-green-sharp bold uppercase" style="font-weight: 800; font-size: 16px; color: #111827; text-align: left !important; display: block !important;"><?= $judul_besar; ?></span>
                        <span class="caption-helper" style="font-size: 12px; color: #6B7280; font-weight: 500; text-align: left !important; display: block !important;"><?= $judul_kecil; ?></span>
                    </div>
                    <div class="actions" style="margin: 0 !important; float: none !important; padding: 0 !important; display: flex !important; align-items: center !important;">
                        <button class="btn btn-default btn-sm" onclick="showKnowledgeBaseModal()" style="border-radius: 8px; font-weight: 700; padding: 8px 16px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.05); font-family: 'Plus Jakarta Sans', sans-serif;">
                            <span>📚</span> Knowledge Base Bimbingan
                        </button>
                    </div>
                </div>
    <div class="portlet-body">

        <?php 
            $count_all = count($data_mahasiswa);
            $count_safe = 0; $count_warning = 0; $count_danger = 0; $count_unstarted = 0;
            foreach($data_mahasiswa as $m) {
                                $is_belum_mulai = ($m->total_bimbingan == 0 || empty($m->last_bimbingan_date));
                if ($is_belum_mulai) {
                    $count_unstarted++;
                }
                
                                if ($m->kategori_health == 'Risiko Tinggi') {
                    $count_danger++;
                } else if ($m->kategori_health == 'Perlu Perhatian') {
                    $count_warning++;
                } else if ($m->kategori_health == 'Aman') {
                    $count_safe++;
                }
            }
        ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin:0; font-size: 1.5rem; color: #111827; font-weight: 800;">Dashboard Bimbingan</h2>
            <a href="<?= base_url('e_skripsi_bimbingan/cetak_rekap_bimbingan') ?>" target="_blank" class="btn btn-primary" style="background: #0D9488; border-color: #0F766E; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);">
                <i class="fa fa-print" style="margin-right: 6px;"></i> Cetak Rekap Bimbingan
            </a>
        </div>

        
        <div class="summary-cards">
            <div class="summary-card">
                <div class="icon all">👥</div>
                <div class="info">
                    <span>Total Bimbingan</span>
                    <strong><?= $count_all ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon" style="background: #F3F4F6; color: #6B7280;">⚪</div>
                <div class="info">
                    <span>Belum Mulai</span>
                    <strong><?= $count_unstarted ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon safe">🟢</div>
                <div class="info">
                    <span>Status Aman</span>
                    <strong><?= $count_safe ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon warning">🟡</div>
                <div class="info">
                    <span>Perlu Perhatian</span>
                    <strong><?= $count_warning ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon danger">🔴</div>
                <div class="info">
                    <span>Risiko Tinggi</span>
                    <strong><?= $count_danger ?></strong>
                </div>
            </div>
        </div>

        <div>
            <?php 
                $pending_mhs = [];
                $other_mhs = [];
                $lulus_mhs = [];
                
                foreach($data_mahasiswa as $m) {
                    $is_pending_pendamping = ($m->jumlah_pending == 0 && isset($m->pending_di_pendamping) && $m->pending_di_pendamping > 0);
                    
                    if (isset($m->hasil_sidang) && $m->hasil_sidang == 'LULUS') {
                        $lulus_mhs[] = $m;
                    } elseif ($m->jumlah_pending > 0) {
                        $pending_mhs[] = $m;
                    } else {
                        $other_mhs[] = $m;
                    }
                }

                // Sort pending_mhs by tanggal pengajuan bimbingan (older first)
                usort($pending_mhs, function($a, $b) {
                    $dateA_val = !empty($a->tgl_pending) ? $a->tgl_pending : (!empty($a->tgl_pending_pendamping) ? $a->tgl_pending_pendamping : $a->last_bimbingan_date);
                    $dateB_val = !empty($b->tgl_pending) ? $b->tgl_pending : (!empty($b->tgl_pending_pendamping) ? $b->tgl_pending_pendamping : $b->last_bimbingan_date);
                    
                    $dateA = !empty($dateA_val) ? strtotime($dateA_val) : 0;
                    $dateB = !empty($dateB_val) ? strtotime($dateB_val) : 0;
                    return $dateA - $dateB;
                });

                // Sort other_mhs by nim (nipd)
                usort($other_mhs, function($a, $b) {
                    return strcmp($a->nipd, $b->nipd);
                });

                // Sort lulus_mhs by nim (nipd)
                usort($lulus_mhs, function($a, $b) {
                    return strcmp($a->nipd, $b->nipd);
                });

                $groups = [];
                if (count($pending_mhs) > 0) {
                    $groups[] = [
                        'title' => '<i class="fa fa-bell" style="color: #F59E0B; margin-right: 6px;"></i> Prioritas: Butuh Respon',
                        'data' => $pending_mhs,
                        'grid_class' => 'student-grid-5',
                        'card_class' => 'student-card-small'
                    ];
                }
                
                if (count($other_mhs) > 0) {
                    $groups[] = [
                        'title' => (count($pending_mhs) > 0) ? '<i class="fa fa-users" style="color: #6B7280; margin-right: 6px;"></i> Mahasiswa Lainnya' : '',
                        'data' => $other_mhs,
                        'grid_class' => 'student-grid-5',
                        'card_class' => 'student-card-small'
                    ];
                }

                if (count($lulus_mhs) > 0) {
                    $groups[] = [
                        'title' => '<i class="fa fa-graduation-cap" style="color: #10B981; margin-right: 6px;"></i> Sudah Lulus Sidang',
                        'data' => $lulus_mhs,
                        'grid_class' => 'student-grid-6',
                        'card_class' => 'student-card-xsmall'
                    ];
                }
            ?>
            
            <?php foreach($groups as $group): ?>
                <?php if (!empty($group['title'])): ?>
                    <h4 style="font-weight: 800; color: #374151; margin-top: <?= ($group === $groups[0]) ? '0' : '30px' ?>; margin-bottom: 18px; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px; font-size: 16px;">
                        <?= $group['title'] ?> (<?= count($group['data']) ?>)
                    </h4>
                <?php endif; ?>
                
                <div class="<?= $group['grid_class'] ?>">
                    <?php foreach($group['data'] as $m): ?>
                    <?php 
                        $has_never_bimbingan = ($m->total_bimbingan == 0 || empty($m->last_bimbingan_date));
                        $is_unstarted = $has_never_bimbingan && ((floatval($m->academic_health_score) == 0 && intval($m->current_progress) == 0) || $m->kategori_health == 'Belum Ada Bimbingan' || empty($m->kategori_health)) && $m->kategori_health != 'Risiko Tinggi';
                        
                        $alert = null;
                        $now = time();
                        $is_lulus = (isset($m->hasil_sidang) && $m->hasil_sidang == 'LULUS');
                        
                        if ($has_never_bimbingan && !empty($m->tanggal_disetujui) && !$is_lulus) {
                            $waktu_setuju = strtotime($m->tanggal_disetujui);
                            $diff_days = floor(($now - $waktu_setuju) / (60 * 60 * 24));
                            $tgl_str = date('d M Y', $waktu_setuju);
                            
                            if ($diff_days >= 365) {
                                $alert = ['icon' => '🚨', 'text' => "Sudah lebih dari 1 tahun sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FFE4E6', 'text_c' => '#E11D48', 'border' => '#FDA4AF'];
                            } elseif ($diff_days >= 180) {
                                $alert = ['icon' => '🛑', 'text' => "Sudah lebih dari 6 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FEE2E2', 'text_c' => '#B91C1C', 'border' => '#F87171'];
                            } elseif ($diff_days >= 90) {
                                $alert = ['icon' => '⚠️', 'text' => "Sudah lebih dari 3 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FFEDD5', 'text_c' => '#C2410C', 'border' => '#FDBA74'];
                            } elseif ($diff_days >= 30) {
                                $alert = ['icon' => '🔔', 'text' => "Sudah lebih dari 1 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FEF9C3', 'text_c' => '#A16207', 'border' => '#FDE047'];
                            }
                        } elseif (!$has_never_bimbingan && !empty($m->last_bimbingan_date)) {
                            $waktu_terakhir = strtotime($m->last_bimbingan_date);
                            $diff_days = floor(($now - $waktu_terakhir) / (60 * 60 * 24));
                            $tgl_str = tgl_indo_short($m->last_bimbingan_date);
                            
                            if ($diff_days >= 180) {
                                $alert = ['icon' => '🚨', 'text' => "Bimbingan terhenti lebih dari 6 bulan (terakhir $tgl_str).", 'bg' => '#FFE4E6', 'text_c' => '#E11D48', 'border' => '#FDA4AF'];
                            } elseif ($diff_days >= 90) {
                                $alert = ['icon' => '🛑', 'text' => "Bimbingan terhenti lebih dari 3 bulan (terakhir $tgl_str).", 'bg' => '#FEE2E2', 'text_c' => '#B91C1C', 'border' => '#F87171'];
                            } elseif ($diff_days >= 60) {
                                $alert = ['icon' => '⚠️', 'text' => "Bimbingan terhenti lebih dari 2 bulan (terakhir $tgl_str).", 'bg' => '#FFEDD5', 'text_c' => '#C2410C', 'border' => '#FDBA74'];
                            } elseif ($diff_days >= 21) {
                                $alert = ['icon' => '🔔', 'text' => "Sudah lebih dari 21 hari sejak bimbingan terakhir ($tgl_str).", 'bg' => '#FEF9C3', 'text_c' => '#A16207', 'border' => '#FDE047'];
                            } else {
                                $alert = ['icon' => '✨', 'text' => "Mahasiswa aktif berkonsultasi sebanyak " . $m->total_bimbingan . "x.", 'bg' => '#E6FBF0', 'text_c' => '#087f46', 'border' => '#A7F3D0'];
                            }
                        }
                        
                        if ($is_unstarted) {
                            $badge_class = 'neutral'; $badge_text = '⚪ Belum Mulai'; $bar_color = '#2a93e2';
                            $status_color = '#2a93e2'; 
                        } else if ($m->kategori_health == 'Perlu Perhatian') {
                            $badge_class = 'warning'; $badge_text = '🟡 Perhatian'; $bar_color = '#c78c1a';
                            $status_color = '#c78c1a'; 
                        } else if ($m->kategori_health == 'Risiko Tinggi') {
                            $badge_class = 'danger'; $badge_text = '🔴 Risiko Tinggi'; $bar_color = '#c51f42';
                            $status_color = '#c51f42'; 
                        } else {
                            $badge_class = 'safe'; $badge_text = '🟢 Aman'; $bar_color = '#087f46';
                            $status_color = '#087f46'; 
                        }
                        
                        $is_pending_pendamping = ($m->jumlah_pending == 0 && isset($m->pending_di_pendamping) && $m->pending_di_pendamping > 0);
                    ?>
                    <div class="student-card <?= $group['card_class'] ?> <?= ($m->jumlah_pending > 0 || $is_pending_pendamping) ? 'has-pending' : '' ?>" id="card-<?= $m->id_judul_skripsi ?>" <?= ($m->jumlah_pending > 0 || $is_pending_pendamping) ? '' : ($m->kategori_health == 'Risiko Tinggi' ? 'style="border-color: #FECACA;"' : '') ?>>
                        <div class="card-header-band" style="background: <?= $status_color ?>; height: 95px; position: relative;">
                            <div class="badge <?= $badge_class ?>" style="position: absolute; top: 12px; left: 12px; z-index: 10; font-weight: 700; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"><?= $badge_text ?></div>
                            <?php if ($m->jumlah_pending > 0): ?>
                                <span class="badge pending-badge" style="position: absolute; top: 12px; right: 12px; z-index: 10; font-weight: 700; box-shadow: 0 2px 5px rgba(0,0,0,0.15); margin: 0; display: inline-flex; align-items: center; gap: 4px;">
                                    <span class="pending-icon" style="font-size: 13px;">🔔</span> Butuh Respon
                                </span>
                            <?php elseif ($is_pending_pendamping): ?>
                                <span class="badge pending-badge" style="position: absolute; top: 12px; right: 12px; z-index: 10; font-weight: 700; box-shadow: 0 2px 5px rgba(0,0,0,0.15); margin: 0; display: inline-flex; align-items: center; gap: 4px; background: #6366F1; animation: pulse-glow-indigo 2s infinite;">
                                    <span class="pending-icon" style="font-size: 13px;">⏳</span> Review Pendamping
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-avatar-wrapper" style="text-align: center; margin-top: -48px; position: relative; z-index: 5; margin-bottom: 12px;">
                            <div style="width: 88px; height: 88px; border-radius: 50%; overflow: hidden; border: 4px solid #FFFFFF; box-shadow: 0 4px 10px rgba(0,0,0,0.12); display: inline-block; background: #F3F4F6;">
                                <?php if (!empty($m->foto)): ?>
                                    <img src="https://files.wbi.ac.id/siakad_file_mahasiswa/<?= rawurlencode($m->foto) ?>" 
                                         onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/no_profile_photo.png') ?>" alt="No Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-student-body" style="padding: 0 20px 20px 20px; text-align: center;">
                            <h3 style="font-size: 15px; font-weight: 800; color: <?= $status_color ?>; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.3px;">
                                <?= $m->nm_pd ?>
                            </h3>
                            <p style="font-size: 12px; color: #6B7280; margin: 0 0 10px 0; font-weight: 600;"><?= $m->nipd ?></p>
                            
                            <div style="margin-bottom: 15px; display: flex; justify-content: center; align-items: center; height: 21px;">
                                <?php if (!$is_unstarted): ?>
                                    <span class="badge" style="background:#F3F4F6; color:#4B5563; border:1px solid #D1D5DB; font-weight:700; font-size: 11px; padding: 2px 10px;">Score: <?= round($m->academic_health_score) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php 
                                $show_snippet = ($m->jumlah_pending > 0 && !empty($m->tgl_pending)) || ($is_pending_pendamping && !empty($m->tgl_pending_pendamping));
                                $snippet_date = !empty($m->tgl_pending) ? $m->tgl_pending : (!empty($m->tgl_pending_pendamping) ? $m->tgl_pending_pendamping : $m->last_bimbingan_date);
                                
                                $action_text = "Diposting";
                                $poster_name = $m->nm_pd;
                                
                                if (!empty($m->last_updater_name)) {
                                    $first_name_student = explode(' ', $m->nm_pd)[0];
                                    if (stripos($m->last_updater_name, $first_name_student) === false) {
                                        $action_text = "Dioper";
                                        $poster_name = "Dosen Utama (" . $m->last_updater_name . ")";
                                    }
                                }
                            ?>
                            <?php if ($show_snippet): ?>
                                <div style="font-size: 11.5px; color: #4F46E5; background: #EEF2FF; padding: 8px 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 6px; border: 1px solid #C7D2FE; text-align: left;">
                                    <span>💬</span>
                                    <span><?= $action_text ?> oleh <strong><?= $poster_name ?></strong> <?= time_ago($snippet_date) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($alert)): ?>
                                <div style="font-size: 11.5px; color: <?= $alert['text_c'] ?>; background: <?= $alert['bg'] ?>; padding: 8px 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; border: 1px solid <?= $alert['border'] ?>; text-align: left; line-height: 1.4;">
                                    <span style="font-size: 16px;"><?= $alert['icon'] ?></span>
                                    <span><?= $alert['text'] ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="progress-section" style="margin-bottom: 20px; text-align: left;">
                                <div class="progress-header" style="font-size: 12px; font-weight: 700;">
                                    <span>Progress Keseluruhan</span>
                                    <span style="color: <?= $status_color ?>;"><?= $m->current_progress ?>%</span>
                                </div>
                                <div class="progress-bar-bg" style="margin-bottom: 10px;">
                                    <div class="progress-bar-fill" style="width: <?= $m->current_progress ?>%; background: <?= $bar_color ?>;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); font-weight: 600; flex-wrap: wrap; gap: 8px;">
                                    <span>📋 Total: <strong><?= $m->total_bimbingan ?>x</strong></span>
                                    <?php if ($m->total_bimbingan > 0 && !empty($m->last_bimbingan_date)): ?>
                                        <span>🕒 Aktif: <strong><?= tgl_indo_short($m->last_bimbingan_date) ?></strong></span>
                                    <?php else: ?>
                                        <span>🕒 Belum Aktif</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-actions">
                                <a href="<?= site_url('E_skripsi_bimbingan/detail/'.$m->id_judul_skripsi) ?>" 
                                   class="btn-action primary"
                                   style="background: <?= $status_color ?>; border-radius: 8px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px <?= $status_color ?>30;"
                                   onclick="saveScrollPos(<?= $m->id_judul_skripsi ?>)">
                                    Lihat Aktifitas Bimbingan
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <?php if(empty($data_mahasiswa)): ?>
                <div style="grid-column: 1 / -1; text-align:center; padding: 40px; color:#6B7280;">
                    Belum ada mahasiswa bimbingan yang aktif.
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


<div class="modal fade" id="modalKnowledgeBase" tabindex="-1" role="dialog" aria-labelledby="kbModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px; font-family: 'Plus Jakarta Sans', sans-serif; margin: 30px auto;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4F46E5 0%, #312E81 100%); padding: 20px 24px; border: none; display: flex; align-items: center; justify-content: space-between; position: relative;">
                <h4 class="modal-title" id="kbModalLabel" style="color: #FFFFFF; font-weight: 800; font-size: 18px; margin: 0; display: flex; align-items: center; gap: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <span>📚</span> Knowledge Base Bimbingan Tugas Akhir (TA)
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #FFFFFF; opacity: 0.8; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; transition: all 0.2s; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #F9FAFB; max-height: 70vh; overflow-y: auto;">
                
                
                <div class="kb-section-title">
                    <span>🔁</span> Alur & Workflow Bimbingan TA
                </div>
                <div class="kb-workflow">
                    <div class="kb-step">
                        <div class="kb-step-number">1</div>
                        <div class="kb-step-content">
                            <h5>Pengajuan Bimbingan oleh Mahasiswa</h5>
                            <p>Mahasiswa memulai proses dengan mengajukan bimbingan melalui portal skripsi. Mahasiswa mengunggah file draft bab skripsi serta mengisi catatan deskripsi kemajuan atau pertanyaan kendala penelitian.</p>
                        </div>
                    </div>
                    <div class="kb-step">
                        <div class="kb-step-number">2</div>
                        <div class="kb-step-content">
                            <h5>Notifikasi Masuk ke Dosen Pembimbing</h5>
                            <p>Dosen pembimbing akan menerima notifikasi "Butuh Respon" secara real-time pada dashboard bimbingan, lengkap dengan indikator waktu sejak pengajuan dikirimkan oleh mahasiswa.</p>
                        </div>
                    </div>
                    <div class="kb-step">
                        <div class="kb-step-number">3</div>
                        <div class="kb-step-content">
                            <h5>Peninjauan & Respon Umpan Balik</h5>
                            <p>Dosen mengulas draf dokumen secara online atau melalui tatap muka, lalu memberikan komentar bimbingan, arahan perbaikan, status persetujuan bab, serta menentukan persentase progress.</p>
                        </div>
                    </div>
                    <div class="kb-step">
                        <div class="kb-step-number">4</div>
                        <div class="kb-step-content">
                            <h5>Kalkulasi Otomatis Indikator Kinerja</h5>
                            <p>Sistem secara otomatis memperbarui total frekuensi konsultasi, memperbarui persentase progress keseluruhan mahasiswa, serta melakukan kalkulasi ulang terhadap <strong>Academic Health Score</strong>.</p>
                        </div>
                    </div>
                    <div class="kb-step">
                        <div class="kb-step-number">5</div>
                        <div class="kb-step-content">
                            <h5>Tindak Lanjut & Kelulusan</h5>
                            <p>Siklus bimbingan diulang kembali secara terjadwal dan berkala hingga progress skripsi mahasiswa mencapai 100% (seluruh Bab disetujui) dan mahasiswa dinyatakan siap mendaftar Sidang TA.</p>
                        </div>
                    </div>
                </div>

                
                <div class="kb-section-title">
                    <span>📊</span> Penjelasan Metrik Utama (Score & Progress)
                </div>
                <div class="kb-grid-metrics">
                    <div class="kb-metric-card">
                        <h4><span>❤️</span> Academic Health Score (0 - 100)</h4>
                        <p>Indikator kesehatan akademis mahasiswa yang dihitung secara real-time berdasarkan intensitas bimbingan, kemajuan bab skripsi, dan tingkat keaktifan konsultasi.</p>
                        <div class="kb-formula-container">
                            <div class="kb-formula-item">
                                <span>Skor Awal (Base Score)</span>
                                <span>+50 Poin</span>
                            </div>
                            <div class="kb-formula-item">
                                <span>Bonus Frekuensi (+5 per Bimbingan)</span>
                                <span>Maks +25 Poin</span>
                            </div>
                            <div class="kb-formula-item">
                                <span>Bonus Progress Skripsi (Proporsional)</span>
                                <span>Maks +25 Poin</span>
                            </div>
                            <div class="kb-formula-item penalty">
                                <span>Penalti Tidak Bimbingan &gt; 14 Hari</span>
                                <span>-20 Poin</span>
                            </div>
                        </div>
                    </div>
                    <div class="kb-metric-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h4><span>📈</span> Progress Keseluruhan (0% - 100%)</h4>
                            <p>Menggambarkan akumulasi pencapaian penulisan skripsi mahasiswa dari awal pendaftaran judul hingga bab penutup selesai.</p>
                            <p style="margin-bottom: 0;">Nilai persentase ini dikelola secara langsung oleh Dosen Pembimbing melalui verifikasi draf per bab yang diajukan oleh mahasiswa.</p>
                        </div>
                        <div style="background: #EEF2FF; padding: 12px; border-radius: 8px; border: 1px solid #C7D2FE; font-size: 11px; color: #4F46E5; font-weight: 600; line-height: 1.4; margin-top: 15px;">
                            💡 <strong>Penting untuk Dosen</strong>: Mengupdate progress secara berkala membantu sistem memantau kondisi akademis mahasiswa secara lebih akurat dan presisi!
                        </div>
                    </div>
                </div>

                
                <div style="font-size: 12.5px; font-weight: 700; color: #111827; margin-top: 15px; display: flex; align-items: center; gap: 6px;">
                    <span>📝</span> Contoh Simulasi Perhitungan Risiko Tinggi
                </div>
                <div class="kb-cases-container">
                    <div class="kb-case-card">
                        <h5>
                            <span>🔴 Kasus A: Baru 1x Bimbingan & Pasif</span>
                            <span style="font-size: 9.5px; background: #FEE2E2; color: #EF4444; padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">Skor &lt; 50</span>
                        </h5>
                        <div class="kb-case-details">
                            <span>Jumlah Bimbingan: <strong>1 kali</strong></span>
                            <span>Progress Skripsi: <strong>10%</strong></span>
                            <span>Aktivitas Terakhir: <strong>18 hari yang lalu (&gt; 14 hari)</strong></span>
                        </div>
                        <div class="kb-case-calc">
                            50 + 5 + 2.5 - 20 = 37.5
                        </div>
                        <div class="kb-case-status">
                            🚨 <strong>Status: Risiko Tinggi</strong><br>Karena skor akhir 37.5 (di bawah ambang batas 50), mahasiswa otomatis masuk kategori Risiko Tinggi.
                        </div>
                    </div>
                    
                    <div class="kb-case-card">
                        <h5>
                            <span>🔴 Kasus B: Sering Bimbingan tapi Vakum</span>
                            <span style="font-size: 9.5px; background: #FEF3C7; color: #D97706; padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">Skor = 50</span>
                        </h5>
                        <div class="kb-case-details">
                            <span>Jumlah Bimbingan: <strong>3 kali</strong></span>
                            <span>Progress Skripsi: <strong>20%</strong></span>
                            <span>Aktivitas Terakhir: <strong>30 hari yang lalu (&gt; 14 hari)</strong></span>
                        </div>
                        <div class="kb-case-calc">
                            50 + 15 + 5 - 20 = 50.0
                        </div>
                        <div class="kb-case-status">
                            ⚠️ <strong>Status: Ambang Batas Risiko</strong><br>Skor tepat 50. Jika progress skripsi di bawah 20%, skor akan kurang dari 50 dan mahasiswa langsung jatuh ke Risiko Tinggi.
                        </div>
                    </div>
                </div>

                
                <div class="kb-section-title">
                    <span>🚦</span> Kategori Status & Rekomendasi Tindakan
                </div>
                <div class="kb-status-grid">
                    <div class="kb-status-card unstarted">
                        <div class="kb-status-header">
                            <h5>⚪ Belum Mulai</h5>
                            <span class="kb-status-badge">Unstarted</span>
                        </div>
                        <p>Mahasiswa belum pernah melakukan aktivitas bimbingan sama sekali sejak tanggal judul skripsi disetujui (frekuensi bimbingan = 0x), dan waktu persetujuan judul masih kurang dari 6 bulan.</p>
                        <div class="kb-status-action">
                            🎯 <strong>Tindakan</strong>: Ingatkan mahasiswa untuk segera melakukan pengajuan bimbingan perdana.
                        </div>
                    </div>
                    <div class="kb-status-card safe">
                        <div class="kb-status-header">
                            <h5>🟢 Status Aman</h5>
                            <span class="kb-status-badge">Safe</span>
                        </div>
                        <p>Mahasiswa yang aktif berkonsultasi dalam 14 hari terakhir dengan nilai Academic Health Score &ge; 70. Kondisi belajar-mengajar berjalan lancar dan optimal.</p>
                        <div class="kb-status-action">
                            🎯 <strong>Tindakan</strong>: Berikan apresiasi dan pertahankan ritme konsultasi rutin mingguan.
                        </div>
                    </div>
                    <div class="kb-status-card warning">
                        <div class="kb-status-header">
                            <h5>🟡 Perlu Perhatian</h5>
                            <span class="kb-status-badge">Warning</span>
                        </div>
                        <p>Menunjukkan indikasi keterlambatan awal. Kriteria: Academic Health Score berkisar antara 50 - 69, ATAU mahasiswa tidak melakukan bimbingan selama 14 s.d 21 hari.</p>
                        <div class="kb-status-action">
                            🎯 <strong>Tindakan</strong>: Hubungi mahasiswa secara persuasif untuk menanyakan kendala penyusunan bab.
                        </div>
                    </div>
                    <div class="kb-status-card danger">
                        <div class="kb-status-header">
                            <h5>🔴 Risiko Tinggi</h5>
                            <span class="kb-status-badge">Danger</span>
                        </div>
                        <p>Kondisi akademis kritis dan terancam terlambat kelulusan. Kriteria: Academic Health Score &lt; 50, ATAU judul skripsi disetujui &gt; 6 bulan namun frekuensi bimbingan masih 0x.</p>
                        <div class="kb-status-action">
                            🎯 <strong>Tindakan</strong>: Panggil mahasiswa khusus, jadwalkan bimbingan wajib, atau ajukan evaluasi kelayakan judul.
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="padding: 14px 24px; background: #FFFFFF; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 700; padding: 8px 20px; border: 1px solid #D1D5DB; background: #FFFFFF; color: #4B5563; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.2s;">Mengerti, Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showKnowledgeBaseModal() {
    $('#modalKnowledgeBase').modal('show');
}

// ============================================================
// SCROLL RESTORE + ONE-TIME REFRESH saat balik dari detail
// ============================================================
function saveScrollPos(id_judul_skripsi) {
    sessionStorage.setItem('bimbingan_scroll', window.scrollY);
    sessionStorage.setItem('bimbingan_last_card', id_judul_skripsi);
    sessionStorage.setItem('bimbingan_needs_refresh', '1');
}

document.addEventListener('DOMContentLoaded', function() {
    var savedScroll  = sessionStorage.getItem('bimbingan_scroll');
    var lastCard     = sessionStorage.getItem('bimbingan_last_card');
    var needsRefresh = sessionStorage.getItem('bimbingan_needs_refresh');

    // Bersihkan flag sebelum proses
    sessionStorage.removeItem('bimbingan_scroll');
    sessionStorage.removeItem('bimbingan_last_card');
    sessionStorage.removeItem('bimbingan_needs_refresh');

    if (needsRefresh === '1') {
        // Refresh card data sekali via AJAX
        fetch('<?= site_url('E_skripsi_bimbingan/ajax_refresh_cards') ?>')
            .then(function(res) { return res.json(); })
            .then(function(cards) {
                var hasStructuralChange = false;
                cards.forEach(function(m) {
                    var card = document.getElementById('card-' + m.id_judul_skripsi);
                    if (!card) return;

                    var pendingBadge = card.querySelector('.pending-badge');
                    var pendingIcon  = card.querySelector('.pending-icon');

                    if (m.jumlah_pending > 0 && !pendingBadge) {
                        // Ada notifikasi baru yg belum ada di DOM — perlu reload penuh
                        hasStructuralChange = true;
                    } else if (m.jumlah_pending === 0) {
                        card.classList.remove('has-pending');
                        if (pendingBadge) pendingBadge.style.display = 'none';
                        if (pendingIcon)  pendingIcon.style.display  = 'none';
                    }

                    // Update progress bar
                    var fillEl = card.querySelector('.progress-bar-fill');
                    var pctEl  = card.querySelector('.progress-pct');
                    if (fillEl) fillEl.style.width = m.current_progress + '%';
                    if (pctEl)  pctEl.textContent  = m.current_progress + '%';
                });

                // Update sidebar badge dynamically
                var totalPending = 0;
                cards.forEach(function(m) {
                    if (parseInt(m.jumlah_pending) > 0) {
                        totalPending++;
                    }
                });
                var sidebarBadge = document.getElementById('bimbingan_pending_badge');
                if (sidebarBadge) {
                    if (totalPending > 0) {
                        sidebarBadge.textContent = totalPending;
                        sidebarBadge.style.display = 'inline-block';
                    } else {
                        sidebarBadge.style.display = 'none';
                    }
                }

                // Setelah data diupdate, restore scroll ke posisi semula
                if (savedScroll !== null) {
                    window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                }

                // Highlight card terakhir yg diklik
                if (lastCard) {
                    var el = document.getElementById('card-' + lastCard);
                    if (el) {
                        el.style.transition = 'box-shadow 0.5s ease';
                        el.style.boxShadow  = '0 0 0 3px rgba(79,70,229,0.4)';
                        setTimeout(function() { el.style.boxShadow = ''; }, 1500);
                    }
                }

                // Jika ada perubahan struktur, lakukan reload sambil simpan scroll
                if (hasStructuralChange) {
                    sessionStorage.setItem('bimbingan_scroll', savedScroll);
                    sessionStorage.setItem('bimbingan_last_card', lastCard);
                    location.reload();
                }
            })
            .catch(function(e) { 
                // Jika AJAX gagal, tetap restore scroll
                if (savedScroll !== null) {
                    window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
                }
                console.warn('Bimbingan refresh error:', e); 
            });
    }
});
</script>