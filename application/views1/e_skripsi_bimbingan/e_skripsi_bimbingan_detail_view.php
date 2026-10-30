<?php
if (!function_exists('clean_markdown_delimiters')) {
    function clean_markdown_delimiters($text) {
        $text = trim($text);
        $text = preg_replace('/^[\s\*\-_#:]+/', '', $text);
        $text = preg_replace('/[\s\*\-_#:]+$/', '', $text);
        return trim($text);
    }
}

if (!function_exists('parse_bimbingan_catatan')) {
    function parse_bimbingan_catatan($text) {
                $patterns = [
            'aktivitas' => '/(?:<[^>]+>|\*\*|\*)*Aktivitas(?:<[^>]+>|\*\*|\*|:)*\s*(.*?)(?=(?:<[^>]+>|\*\*|\*|\s)*Kendala|(?:<[^>]+>|\*\*|\*|\s)*Target|$)/is',
            'kendala' => '/(?:<[^>]+>|\*\*|\*)*Kendala(?:<[^>]+>|\*\*|\*|:)*\s*(.*?)(?=(?:<[^>]+>|\*\*|\*|\s)*Target|$)/is',
            'target' => '/(?:<[^>]+>|\*\*|\*)*Target(?: Berikutnya)?(?:<[^>]+>|\*\*|\*|:)*\s*(.*)/is'
        ];

        $result = [];
        $found = false;

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $content = trim($matches[1]);
                $content = clean_markdown_delimiters($content);
                                $content = strip_tags($content);
                if (!empty($content)) {
                    $result[$key] = $content;
                    $found = true;
                }
            }
        }

        if ($found) {
            return $result;
        }

        return null;
    }
}

if (!function_exists('format_semester_nama')) {
    function format_semester_nama($id_smt) {
        if (empty($id_smt)) return '-';
        $year = substr($id_smt, 0, 4);
        $term = substr($id_smt, 4, 1);
        $term_name = ($term == '1') ? 'Ganjil' : (($term == '2') ? 'Genap' : 'Pendek');
        $next_year = $year + 1;
        return "$year/$next_year - $term_name";
    }
}
?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .bimbingan-wrapper {
        --primary: #4F46E5;
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
    
    .bimbingan-wrapper * { box-sizing: border-box; }

    /* Premium Toast Styles */
    .premium-toast {
        position: fixed;
        top: 30px;
        right: 30px;
        background: #FFFFFF;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border-left: 5px solid #4F46E5;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(100px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        max-width: 380px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .premium-toast.show {
        transform: translateX(0);
        opacity: 1;
    }
    .premium-toast-icon {
        font-size: 18px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .premium-toast.success { border-left-color: #10B981; }
    .premium-toast.success .premium-toast-icon { background: #E6FBF0; color: #10B981; }

    .premium-toast.warning { border-left-color: #F59E0B; }
    .premium-toast.warning .premium-toast-icon { background: #FEF3C7; color: #F59E0B; }

    .premium-toast.info { border-left-color: #3B82F6; }
    .premium-toast.info .premium-toast-icon { background: #EFF6FF; color: #3B82F6; }

    .premium-toast-content {
        flex: 1;
    }
    .premium-toast-title {
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .premium-toast-message {
        font-size: 11.5px;
        color: #4B5563;
        line-height: 1.4;
        font-weight: 500;
    }

    /* Thread Panel */
    .thread-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .thread-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        position: relative;
        margin-bottom: 24px;
    }
    .thread-card:last-child { margin-bottom: 0; }
    


    /* Indented Dosen Reply with curved/elbow connection */
    .thread-card.dosen-reply {
        margin-left: 150px;
        background: #F9FAFB;
        border-left: 4px solid var(--primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }


    /* Tanya from dosen - indented violet */
    .thread-card.tanya-dosen {
        background: linear-gradient(135deg, #F5F3FF 0%, #EEF2FF 100%);
        border-left: 4px solid #6366F1;
    }
    /* Balas (reply thread) - compact style */
    .thread-card.balas-thread {
        margin-left: 80px;
        border-left: 4px solid #A5B4FC;
        background: #F8FAFF;
        box-shadow: 0 2px 6px rgba(99,102,241,0.04);
    }
    .thread-card.balas-thread::after { display: none; }

    .thread-header {
        padding: 11px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
        background: #F9FAFB;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }

    .avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #818CF8 0%, #4F46E5 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15);
        flex-shrink: 0;
    }
    .avatar.dosen { 
        background: linear-gradient(135deg, #10B981 0%, #059669 100%); 
        box-shadow: 0 4px 6px rgba(5, 150, 105, 0.15);
    }

    .thread-meta { flex: 1; }
    .thread-meta strong { font-size: 14px; display: block; }
    .thread-meta span { font-size: 12px; color: var(--text-muted); }

    .thread-badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 600;
        border: 1px solid;
    }
    .thread-badge.update { background: #EEF2FF; color: var(--primary); border-color: #C7D2FE; }
    .thread-badge.review { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }

    .thread-body { padding: 15px 16px; font-size: 14px; }
    
    .thread-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 15px;
    }
    .thread-grid .label { color: var(--text-muted); font-weight: 600; }

    .file-attachment {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: #F9FAFB;
        font-size: 13px;
        font-weight: 500;
        color: var(--primary);
        text-decoration: none;
        margin-top: 15px;
    }

    /* Reply Box */
    .reply-box {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-top: 20px;
    }

    /* Profile Card Sidebar */
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

    .sidebar-cover {
        height: 400px;
        background: linear-gradient(135deg, #312E81 0%, #4F46E5 100%);
        background-image: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 40%), radial-gradient(circle at bottom left, rgba(0,0,0,0.2) 0%, transparent 40%);
        position: relative;
    }

    .sidebar-student-name {
        color: #111827;
        font-size: 19px;
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
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .sidebar-judul {
        color: #4B5563;
        font-size: 14.5px;
        line-height: 1.6;
        font-weight: 500;
        margin-bottom: 24px;
        padding: 0 10px;
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
    }
    .icon-dosen { background: #EEF2FF; color: #4F46E5; }
    .icon-health { background: #F0FDF4; color: #10B981; }
    .icon-progress { background: #FEF3C7; color: #D97706; }
    
    .sidebar-meta-text {
        flex: 1;
    }
    .sidebar-meta-label {
        font-size: 11px;
        color: #6B7280;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .sidebar-meta-value {
        font-size: 13px;
        color: #111827;
        font-weight: 600;
    }

    .progress-bar-container { height: 6px; background: #E5E7EB; border-radius: 999px; margin-top: 8px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: var(--primary); }

    /* Modern Log Grid Layout */
    .modern-log-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .progress-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    /* Modern Vertical Timeline Flow for Bimbingan Catatan */
    .timeline-container {
        position: relative;
        padding-left: 20px;
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-top: 15px;
    }
    
    .timeline-container::before {
        content: '';
        position: absolute;
        top: 15px;
        bottom: 15px;
        left: 38px;
        width: 3px;
        background: linear-gradient(to bottom, rgba(79, 70, 229, 0.2) 0%, rgba(229, 231, 235, 0.5) 50%, rgba(16, 185, 129, 0.2) 100%);
        border-radius: 99px;
    }
    
    .timeline-item {
        display: flex;
        gap: 22px;
        align-items: flex-start;
        position: relative;
    }
    
    .timeline-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        z-index: 2;
        flex-shrink: 0;
        border: 3px solid #FFFFFF;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .timeline-badge.aktivitas {
        background: linear-gradient(135deg, #818CF8 0%, #4F46E5 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .timeline-badge.kendala-warn {
        background: linear-gradient(135deg, #F87171 0%, #EF4444 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .timeline-badge.kendala-safe {
        background: linear-gradient(135deg, #34D399 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }
    .timeline-badge.target {
        background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
    }

    .timeline-card {
        flex: 1;
        background: #FFFFFF;
        border: 1px solid rgba(229, 231, 235, 0.8);
        border-radius: 14px;
        padding: 18px 22px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .timeline-card.card-aktivitas {
        border-left: 5px solid #4F46E5;
        background: linear-gradient(135deg, #FFFFFF 0%, #F5F7FF 100%);
    }
    .timeline-card.card-kendala-warn {
        border-left: 5px solid #EF4444;
        background: linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);
    }
    .timeline-card.card-kendala-safe {
        border-left: 5px solid #10B981;
        background: linear-gradient(135deg, #FFFFFF 0%, #F0FDF4 100%);
    }
    .timeline-card.card-target {
        border-left: 5px solid #06B6D4;
        background: linear-gradient(135deg, #FFFFFF 0%, #F0FDFA 100%);
    }
    
    .timeline-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        border-color: rgba(209, 213, 219, 0.8);
    }
    
    .timeline-title {
        margin: 0 0 8px 0;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        gap: 6px;
    }
    
    .timeline-title.title-aktivitas { color: #4F46E5; background-color: #EEF2FF; }
    .timeline-title.title-kendala-warn { color: #EF4444; background-color: #FEE2E2; }
    .timeline-title.title-kendala-safe { color: #10B981; background-color: #E6FBF0; }
    .timeline-title.title-target { color: #0891B2; background-color: #ECFDF5; }

    .timeline-text {
        font-size: 13.5px;
        color: #374151;
        line-height: 1.65;
        white-space: pre-wrap;
        margin-top: 6px;
        font-weight: 500;
    }

    /* Fallback student log card style */
    .fallback-log-card {
        border-left: 5px solid #4F46E5; 
        background: linear-gradient(135deg, #FFFFFF 0%, #F5F7FF 100%); 
        border-top: 1px solid rgba(229, 231, 235, 0.8);
        border-right: 1px solid rgba(229, 231, 235, 0.8);
        border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        border-radius: 14px; 
        padding: 20px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s ease;
    }
    .fallback-log-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    }

    /* Info Dashboard when No Bimbingan */
    .dashboard-empty-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .dashboard-empty-card {
        background: #FFFFFF;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .dashboard-empty-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    }
    
    .dashboard-empty-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    
    .dashboard-empty-content {
        flex: 1;
    }
    
    .dashboard-empty-label {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }
    
    .dashboard-empty-value {
        font-size: 15px;
        color: var(--text-main);
        font-weight: 700;
    }
    
    .dashboard-empty-subtext {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .krs-card {
        background: #FFFFFF;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    }
    
    .krs-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
        padding-bottom: 12px;
    }
    
    .krs-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    
    .krs-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        padding: 10px 12px;
        background: #F9FAFB;
        border-bottom: 2px solid var(--border);
    }
    
    .krs-table td {
        padding: 12px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        font-weight: 500;
        color: var(--text-main);
    }
    
    .krs-table tr:last-child td {
        border-bottom: none;
    }
    
    /* Style for raw fallback bimbingan panels */
    .raw-old-bimbingan .panel.panel-default {
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        margin-bottom: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .raw-old-bimbingan .panel-heading {
        background: #F3F4F6;
        padding: 10px 15px;
        font-weight: 700;
        font-size: 12.5px;
        color: #374151;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .raw-old-bimbingan .panel-body {
        padding: 15px;
        font-size: 13.5px;
        line-height: 1.6;
        color: #4B5563;
    }
    
    /* Scrollbar style for old bimbingan container */
    .old-bimbingan-chat-container::-webkit-scrollbar {
        width: 6px;
    }
    .old-bimbingan-chat-container::-webkit-scrollbar-track {
        background: transparent;
    }
    .old-bimbingan-chat-container::-webkit-scrollbar-thumb {
        background: #C7D2FE;
        border-radius: 99px;
    }
    .old-bimbingan-chat-container::-webkit-scrollbar-thumb:hover {
        background: #818CF8;
    }
</style>

<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light thread-container">
                <div class="portlet-title">
        <div class="caption">
            <a href="<?= site_url('E_skripsi_bimbingan') ?>" class="btn btn-default btn-xs" style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Kembali</a>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="bimbingan-wrapper">

            
            <div class="thread-panel">
                <?php 
                    $pending_pendamping = 0;
                    $pending_utama = 0;
                    $oldest_pending_pendamping = null;
                    $oldest_pending_utama = null;
                    foreach ($logs as $l) {
                        if (!empty($l->catatan_mahasiswa)) {
                            if (!empty($data_skripsi->dosen_pembimbing_2) && (empty($l->status_pendamping) || $l->status_pendamping == 'Menunggu Review')) {
                                $pending_pendamping++;
                                if ($oldest_pending_pendamping === null || strtotime($l->created_at) < strtotime($oldest_pending_pendamping)) {
                                    $oldest_pending_pendamping = $l->created_at;
                                }
                            } elseif ((empty($data_skripsi->dosen_pembimbing_2) || $l->status_pendamping == 'Lanjut ke Utama') && (empty($l->catatan_dosen) || $l->status_bimbingan == 'Menunggu Review')) {
                                $pending_utama++;
                                if ($oldest_pending_utama === null || strtotime($l->created_at) < strtotime($oldest_pending_utama)) {
                                    $oldest_pending_utama = $l->created_at;
                                }
                            }
                        }
                    }
                    
                    $get_age_text = function($date) {
                        $diff = time() - strtotime($date);
                        if ($diff < 60) {
                            return $diff . ' detik yang lalu';
                        }
                        $minutes = floor($diff / 60);
                        if ($minutes < 60) {
                            return $minutes . ' menit yang lalu';
                        }
                        $hours = floor($diff / 3600);
                        if ($hours < 24) {
                            return $hours . ' jam yang lalu';
                        }
                        $days = floor($diff / 86400);
                        return $days . ' hari yang lalu';
                    };
                ?>
                <?php if ($pending_pendamping > 0 || $pending_utama > 0): ?>
                    <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <div style="background: #3B82F6; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa fa-info-circle"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 4px 0; color: #1E40AF; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">
                                INFO PROSES BIMBINGAN
                            </h4>
                            <p style="margin: 0; color: #1E40AF; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                <?php if ($pending_pendamping > 0): ?>
                                    Terdapat <strong><?= $pending_pendamping ?> berkas bimbingan</strong> yang telah diajukan sejak <strong><?= $get_age_text($oldest_pending_pendamping) ?></strong> dan saat ini masih menunggu review / persetujuan dari <strong>Dosen Pendamping</strong>. Skor bimbingan mahasiswa tidak akan bertambah sebelum disetujui.<br>
                                <?php endif; ?>
                                <?php if ($pending_utama > 0): ?>
                                    Terdapat <strong><?= $pending_utama ?> berkas bimbingan</strong> yang telah diajukan sejak <strong><?= $get_age_text($oldest_pending_utama) ?></strong> dan saat ini menunggu review / persetujuan dari Anda (<strong>Dosen Pembimbing Utama</strong>).
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <?php if (!empty($data_skripsi->kategori_health) && in_array($data_skripsi->kategori_health, ['Perlu Perhatian', 'Risiko Tinggi'])): 
                        $alert_bg = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#FEF2F2' : '#FEFCE8';
                        $alert_border = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#FECACA' : '#FEF08A';
                        $alert_text = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#991B1B' : '#854D0E';
                        $alert_icon = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
                        $alert_badge_bg = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#EF4444' : '#EAB308';
                        
                        $reason_text = "Mahasiswa membutuhkan perhatian khusus berdasarkan evaluasi algoritma akademik.";
                        if ($data_skripsi->kategori_health == 'Perlu Perhatian') {
                            $real_logs = array_filter($logs, function($l) { return !empty($l->catatan_mahasiswa) && $l->status_bimbingan == 'Disetujui'; });
                            $real_logs = array_values($real_logs);
                            $total_bimbingan = count($real_logs);
                            
                            $last_bimbingan_date = ($total_bimbingan > 0) ? $real_logs[$total_bimbingan - 1]->tanggal_bimbingan : null;
                            $days_diff = $last_bimbingan_date ? (strtotime(date('Y-m-d')) - strtotime($last_bimbingan_date)) / (60 * 60 * 24) : 0;
                            
                            $waktu_setuju = !empty($data_skripsi->tanggal_disetujui) ? strtotime($data_skripsi->tanggal_disetujui) : time();
                            $days_since_approved = floor((time() - $waktu_setuju) / (60 * 60 * 24));
                            $umur_text = ($days_since_approved >= 30) ? round($days_since_approved / 30, 1) . " bulan" : "$days_since_approved hari";
                            
                            $reasons = [];
                            if ($days_diff > 14) {
                                $reasons[] = "sudah lebih dari 14 hari tidak melakukan bimbingan (terkena penalti skor)";
                            }
                            if ($data_skripsi->current_progress < 30) {
                                $reasons[] = "progres skripsi masih lambat (" . $data_skripsi->current_progress . "%)";
                            }
                            if ($total_bimbingan < 4) {
                                $reasons[] = "frekuensi bimbingan masih minim (baru " . $total_bimbingan . " kali dalam waktu " . $umur_text . " sejak judul disetujui)";
                            }
                            
                            if (count($reasons) > 0) {
                                $reason_text = "Mahasiswa membutuhkan perhatian karena " . implode(', dan ', $reasons) . ". Total Score: " . round($data_skripsi->academic_health_score) . ".";
                            } else {
                                $reason_text = "Performa bimbingan mahasiswa atau progres skripsi perlu ditingkatkan agar aman dari risiko keterlambatan (Score: " . round($data_skripsi->academic_health_score) . ").";
                            }
                            
                            $reason_text .= "<br><br><strong style='color:#713F12;'>💡 Rekomendasi Tindakan:</strong><br>";
                            $reason_text .= "Sarankan mahasiswa untuk segera menjadwalkan bimbingan tatap muka/online, dan ingatkan untuk rutin melaporkan progres minimal setiap 14 hari sekali agar tidak terkena penalti sistem.";
                        } elseif ($data_skripsi->kategori_health == 'Risiko Tinggi') {
                            $real_logs = array_filter($logs, function($l) { return !empty($l->catatan_mahasiswa) && $l->status_bimbingan == 'Disetujui'; });
                            $real_logs = array_values($real_logs); 
                            $total_bimbingan = count($real_logs);
                            
                            $last_bimbingan_date = ($total_bimbingan > 0) ? $real_logs[$total_bimbingan - 1]->tanggal_bimbingan : null;
                            $days_diff = $last_bimbingan_date ? (strtotime(date('Y-m-d')) - strtotime($last_bimbingan_date)) / (60 * 60 * 24) : 0;
                            
                            $waktu_setuju = !empty($data_skripsi->tanggal_disetujui) ? strtotime($data_skripsi->tanggal_disetujui) : time();
                            $days_since_approved = floor((time() - $waktu_setuju) / (60 * 60 * 24));
                            $umur_text = ($days_since_approved >= 30) ? round($days_since_approved / 30, 1) . " bulan" : "$days_since_approved hari";
                            
                            if ($total_bimbingan == 0) {
                                $reason_text = "Mahasiswa berisiko tinggi karena belum pernah melakukan bimbingan sama sekali padahal judul sudah disetujui sejak " . $umur_text . " yang lalu.";
                            } else {
                                $reasons = [];
                                if ($days_diff > 30) {
                                    $reasons[] = "sudah " . round($days_diff/30, 1) . " bulan menghilang / absen bimbingan";
                                } elseif ($days_diff > 14) {
                                    $reasons[] = "sudah lebih dari 14 hari tidak bimbingan (terkena penalti skor)";
                                }
                                
                                if ($data_skripsi->current_progress < 50) {
                                    $reasons[] = "progres skripsi terhambat di angka " . $data_skripsi->current_progress . "%";
                                }
                                
                                if (count($reasons) > 0) {
                                    $reason_text = "Mahasiswa berstatus Risiko Tinggi (Score: " . round($data_skripsi->academic_health_score) . ") dikarenakan " . implode(', dan ', $reasons) . ".";
                                } else {
                                    $reason_text = "Kinerja bimbingan sangat rendah (Score: " . round($data_skripsi->academic_health_score) . "). Diperlukan pemantauan khusus.";
                                }
                            }
                            
                            $reason_text .= "<br><br><strong style='color:#7F1D1D;'>🚨 Tindakan Kritis:</strong><br>";
                            $reason_text .= "Segera hubungi mahasiswa yang bersangkutan melalui Prodi atau Wali Akademik untuk memastikan status perkuliahannya dan memanggil mahasiswa untuk menghadap.";
                        }
                        
                        $has_seminar = !empty($riwayat_seminar['sempro']['tanggal']) || !empty($riwayat_seminar['semhas']['tanggal']) || !empty($riwayat_seminar['sidang']['tanggal']);
                        if (empty($logs) && $has_seminar) {
                            $reason_text .= "<br><br><strong>⚠️ Anomali Bimbingan Terdeteksi:</strong><br>Mahasiswa ini sudah mencapai tahapan Seminar/Sidang, namun <span style='text-decoration:underline;'>belum pernah melakukan bimbingan</span> melalui sistem. Mohon dilakukan pengecekan.";
                        }
                    ?>
                    <div style="background: <?= $alert_bg ?>; border: 1px solid <?= $alert_border ?>; border-radius: 12px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: 0; flex: 1;">
                        <div style="background: <?= $alert_badge_bg ?>; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa <?= $alert_icon ?>"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 4px 0; color: <?= $alert_text ?>; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">
                                STATUS: <?= strtoupper($data_skripsi->kategori_health) ?>
                            </h4>
                            <p style="margin: 0; color: <?= $alert_text ?>; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                <?= $reason_text ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Petunjuk Singkat Bimbingan Dosen -->
                    <?php if (isset($petunjuk_dosen)): ?>
                    <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: 0; flex: 1;">
                        <div style="background: #10B981; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div style="width: 100%;">
                            <h4 style="margin: 0 0 8px 0; color: #14532D; font-weight: 800; font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
                                <span><?= $petunjuk_dosen['title'] ?></span>
                            </h4>
                            <p style="margin: 0 0 8px 0; color: #166534; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                <?= $petunjuk_dosen['desc'] ?>
                            </p>
                            <ul style="margin: 0 0 12px 0; padding-left: 20px; color: #166534; font-size: 13px; line-height: 1.6; font-weight: 700; list-style-type: decimal;">
                                <?php foreach ($petunjuk_dosen['steps'] as $step): ?>
                                    <li><?= $step ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div style="margin-top: 12px; padding-top: 8px; border-top: 1px dashed #BBF7D0; color: #15803D; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px;">💡</span> <?= $petunjuk_dosen['action_tip'] ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (isset($riwayat_seminar)): ?>
                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <span>🎓</span> Riwayat Seminar &amp; Sidang
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        
                        <!-- Penetapan Judul -->
                        <div style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #3B82F6; letter-spacing: 0.5px; margin-bottom: 8px;">Penetapan Judul</div>
                            <?php if (!empty($data_skripsi->tanggal_disetujui)): ?>
                                <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?= tgl_indo_short(date('Y-m-d', strtotime($data_skripsi->tanggal_disetujui))) ?></div>
                                <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                    <i class="fa fa-clock-o"></i> Umur Judul: <?= !empty($umur_judul) ? $umur_judul : '-' ?>
                                </div>
                            <?php else: ?>
                                <div style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">Belum ditetapkan</div>
                            <?php endif; ?>
                        </div>

                        <!-- Sempro -->
                        <div style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6366F1; letter-spacing: 0.5px; margin-bottom: 8px;">Seminar Proposal</div>
                            <?php if (!empty($riwayat_seminar['sempro']['tanggal'])): ?>
                                <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?= tgl_indo_short($riwayat_seminar['sempro']['tanggal']) ?></div>
                                <?php if (!empty($riwayat_seminar['sempro']['hasil'])): ?>
                                    <span class="badge" style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 10px; padding: 2px 8px; margin-bottom: 8px; display: inline-block;">Hasil: <?= ucfirst(str_replace('_', ' ', $riwayat_seminar['sempro']['hasil'])) ?></span>
                                <?php endif; ?>
                                <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['sempro']['durasi'] ?> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sempro']['label_durasi'] ?></span>
                                </div>
                                <?php if (!empty($riwayat_seminar['sempro']['abnormal']) && $riwayat_seminar['sempro']['abnormal']): ?>
                                    <div style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($riwayat_seminar['sempro']['ongoing']): ?>
                                    <div style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">Belum dilaksanakan</div>
                                    <div style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br> <strong><?= $riwayat_seminar['sempro']['durasi'] ?></strong> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sempro']['label_durasi'] ?></span>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">Belum dilaksanakan</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Semhas -->
                        <div style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #F59E0B; letter-spacing: 0.5px; margin-bottom: 8px;">Seminar Hasil</div>
                            <?php if (!empty($riwayat_seminar['semhas']['tanggal'])): ?>
                                <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?= tgl_indo_short($riwayat_seminar['semhas']['tanggal']) ?></div>
                                <?php if (!empty($riwayat_seminar['semhas']['hasil'])): ?>
                                    <span class="badge" style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 10px; padding: 2px 8px; margin-bottom: 8px; display: inline-block;">Hasil: <?= ucfirst(str_replace('_', ' ', $riwayat_seminar['semhas']['hasil'])) ?></span>
                                <?php endif; ?>
                                <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['semhas']['durasi'] ?> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['semhas']['label_durasi'] ?></span>
                                </div>
                                <?php if (!empty($riwayat_seminar['semhas']['abnormal']) && $riwayat_seminar['semhas']['abnormal']): ?>
                                    <div style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($riwayat_seminar['semhas']['ongoing']): ?>
                                    <div style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">Belum dilaksanakan</div>
                                    <div style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br> <strong><?= $riwayat_seminar['semhas']['durasi'] ?></strong> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['semhas']['label_durasi'] ?></span>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">Belum dilaksanakan</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Sidang -->
                        <div style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #10B981; letter-spacing: 0.5px; margin-bottom: 8px;">Sidang Meja Hijau</div>
                            <?php if (!empty($riwayat_seminar['sidang']['tanggal'])): ?>
                                <div style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?= tgl_indo_short($riwayat_seminar['sidang']['tanggal']) ?></div>
                                <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['sidang']['durasi'] ?> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sidang']['label_durasi'] ?></span>
                                </div>
                                <?php if (!empty($riwayat_seminar['sidang']['abnormal']) && $riwayat_seminar['sidang']['abnormal']): ?>
                                    <div style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($riwayat_seminar['sidang']['ongoing']): ?>
                                    <div style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">Belum dilaksanakan</div>
                                    <div style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br> <strong><?= $riwayat_seminar['sidang']['durasi'] ?></strong> <br> <span style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sidang']['label_durasi'] ?></span>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">Belum dilaksanakan</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>
                <?php endif; ?>

                <!-- Old Bimbingan Archive System (Transisi) -->
                <?php if (!empty($old_bimbingan) && !empty($old_bimbingan_formatted_html)): ?>
                    <div style="margin-bottom: 24px; background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <div onclick="$('#oldBimbinganArchive').slideToggle(); $(this).find('i.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');" style="padding: 15px 20px; font-weight: 800; color: #3730A3; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fa fa-history" style="color: #4F46E5; font-size: 16px;"></i>
                                <span>Arsip Bimbingan Sistem Lama (Masa Transisi)</span>
                            </div>
                            <i class="fa fa-chevron-down toggle-icon" style="color: #6366F1;"></i>
                        </div>
                        <div id="oldBimbinganArchive" style="display: none; padding: 20px; border-top: 1px solid #C7D2FE; background: #FAF5FF;">
                            <div class="old-bimbingan-chat-container" style="max-height: 400px; overflow-y: auto; padding-right: 8px;">
                                <?= $old_bimbingan_formatted_html ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($logs)): ?>
                    <div class="alert alert-info" style="border-radius:12px; border:none; background:#EFF6FF; color:#1E40AF; padding:16px 20px; font-weight:600; display:flex; align-items:center; gap:10px; margin-bottom: 24px;">
                        <span style="font-size:18px;">ℹ️</span>
                        <span>Belum ada riwayat bimbingan yang tercatat untuk mahasiswa ini. Berikut adalah informasi akademik terkait mahasiswa:</span>
                    </div>

                    <div class="dashboard-empty-grid">
                        <div class="dashboard-empty-card">
                            <div class="dashboard-empty-icon" style="background:#E0F2FE; color:#0284C7;">👨‍🏫</div>
                            <div class="dashboard-empty-content">
                                <div class="dashboard-empty-label">Dosen Wali / Pembimbing Akademik</div>
                                <div class="dashboard-empty-value"><?= !empty($nm_dosen_wali) ? $nm_dosen_wali : '-' ?></div>
                                <div class="dashboard-empty-subtext">Mendampingi perkembangan akademik mahasiswa</div>
                            </div>
                        </div>

                        <div class="dashboard-empty-card">
                            <div class="dashboard-empty-icon" style="background:#FEF3C7; color:#D97706;">⏳</div>
                            <div class="dashboard-empty-content">
                                <div class="dashboard-empty-label">Umur Judul Tugas Akhir</div>
                                <div class="dashboard-empty-value"><?= !empty($umur_judul) ? $umur_judul : '-' ?></div>
                                <div class="dashboard-empty-subtext">
                                    <?php if (!empty($data_skripsi->tanggal_disetujui)): ?>
                                        Disetujui sejak: <?= tgl_indo_short(date('Y-m-d', strtotime($data_skripsi->tanggal_disetujui))) ?>
                                    <?php else: ?>
                                        Belum disetujui
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="krs-card">
                        <h4 class="krs-title">
                            <span>📄 Kartu Rencana Studi (KRS) Terakhir</span>
                            <?php if ($krs_terakhir): ?>
                                <span class="badge" style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px;">
                                    Semester <?= format_semester_nama($krs_terakhir->id_smt) ?> &bull; Status: <?= $krs_terakhir->nm_status_krs ?>
                                </span>
                            <?php endif; ?>
                        </h4>

                        <?php if ($krs_terakhir && !empty($krs_courses)): ?>
                            <div class="table-responsive">
                                <table class="krs-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">Kode MK</th>
                                            <th>Nama Matakuliah</th>
                                            <th style="width: 100px; text-align: center;">SKS</th>
                                            <th style="width: 100px; text-align: center;">Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_sks_krs = 0;
                                        foreach ($krs_courses as $course): 
                                            $total_sks_krs += $course->sks_mk;
                                        ?>
                                            <tr>
                                                <td><code style="background:#F3F4F6; color:#374151; font-weight:700; padding:2px 6px; border-radius:4px; font-size:12px;"><?= $course->kode_mk ?></code></td>
                                                <td style="font-weight: 600; color: #111827;"><?= $course->nm_mk ?></td>
                                                <td style="text-align: center; font-weight: 700; color: var(--primary);"><?= round($course->sks_mk) ?></td>
                                                <td style="text-align: center;"><span class="badge" style="background:#EEF2FF; color:#4F46E5; font-weight:600; font-size:11px;"><?= $course->nm_kls ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr style="background: #F9FAFB; font-weight: 700; border-top: 2px solid var(--border);">
                                            <td colspan="2" style="text-align: right; padding: 12px; color: var(--text-muted);">Total Kredit:</td>
                                            <td style="text-align: center; color: var(--primary); font-size: 15px;"><?= $total_sks_krs ?> SKS</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center; padding: 40px 20px; color: var(--text-muted);">
                                <span style="font-size:36px; display:block; margin-bottom:12px;">📁</span>
                                <p style="font-weight: 600; margin: 0; font-size: 14px;">Belum ada data Kartu Rencana Studi (KRS) untuk semester terbaru.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php 
                        if (!function_exists('get_avatar_initials')) {
                            function get_avatar_initials($name, $default = 'DS') {
                                $titles = ['dr', 'dr.', 'drg', 'drg.', 'prof', 'prof.', 'ir', 'ir.', 'h', 'h.', 'hj', 'hj.', 'drs', 'drs.', 'dra', 'dra.', 'ns', 'ns.', 's.s.t.', 'm.si'];
                                $words = explode(' ', $name);
                                $clean = array_filter($words, function($w) use ($titles) {
                                    $w = strtolower(trim($w));
                                    if (in_array($w, $titles)) return false;
                                    if (strpos($w, '.') !== false) return false;
                                    if (strpos($w, ',') !== false) return false;
                                    return strlen($w) > 0;
                                });
                                $clean = array_values($clean);
                                if (count($clean) >= 2) return strtoupper(substr($clean[0], 0, 1) . substr($clean[1], 0, 1));
                                if (count($clean) == 1) return strtoupper(substr($clean[0], 0, 2));
                                return $default;
                            }
                        }
                    
                        $archived_logs = [];
                        $active_logs = [];
                        $total_logs = count($logs);
                        
                        $current_thread = [];
                        foreach($logs as $index => $log) {
                            $current_thread[] = $log;
                            if ($log->status_bimbingan == 'Disetujui') {
                                // Sebuah bimbingan selesai, lipat seluruh thread ini ke archive
                                foreach($current_thread as $t_log) {
                                    $archived_logs[] = $t_log;
                                }
                                $current_thread = []; // Reset untuk bimbingan selanjutnya
                            }
                        }
                        
                        // Jika masih ada log yang belum disetujui (sisa thread terakhir), jadikan active
                        if (!empty($current_thread)) {
                            foreach($current_thread as $t_log) {
                                $active_logs[] = $t_log;
                            }
                        }
                        
                        $sections = [
                            ['title' => 'archive', 'logs' => $archived_logs],
                            ['title' => 'active', 'logs' => $active_logs]
                        ];
                        
                        $pending_bimbingan_log = null;
                        if (!empty($logs)) {
                            $mhs_logs = [];
                            foreach ($logs as $chk) {
                                $jenis = isset($chk->jenis_entri) ? $chk->jenis_entri : '';
                                if ($jenis === 'balas' || $jenis === 'tanya') continue;
                                if (!empty($chk->catatan_mahasiswa)) {
                                    $mhs_logs[] = $chk;
                                }
                            }
                            
                            $last_mhs_log = !empty($mhs_logs) ? end($mhs_logs) : null;
                            $prev_mhs_log = count($mhs_logs) > 1 ? $mhs_logs[count($mhs_logs)-2] : null;

                            if ($last_mhs_log && $last_mhs_log->status_bimbingan != 'Disetujui') {
                                $is_p = isset($is_pendamping) ? $is_pendamping : false;
                                $is_u = isset($is_utama) ? $is_utama : false;
                                $has_pendamping = !empty($data_skripsi->dosen_pembimbing_2) && $data_skripsi->dosen_pembimbing_2 != '-';
                                
                                $expected_reviewer = 'utama'; 
                                if ($has_pendamping && $prev_mhs_log && $prev_mhs_log->status_bimbingan == 'Perlu Revisi') {
                                    if ($prev_mhs_log->status_pendamping == 'Perlu Revisi') {
                                        $expected_reviewer = 'pendamping';
                                    }
                                }

                                if ($is_p && !$is_u) {
                                    if ( (empty($last_mhs_log->catatan_pendamping) && $expected_reviewer == 'pendamping') || $last_mhs_log->status_pendamping == 'Konsultasi ke Pendamping' ) {
                                        $pending_bimbingan_log = $last_mhs_log;
                                    }
                                } else {
                                    if (!$has_pendamping || ($expected_reviewer == 'utama' && empty($last_mhs_log->status_pendamping)) || $last_mhs_log->status_pendamping == 'Lanjut ke Utama') {
                                        if ($last_mhs_log->status_bimbingan == 'Menunggu Review') {
                                            $pending_bimbingan_log = $last_mhs_log;
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                    
                    <?php foreach($sections as $sec): ?>
                        <?php if (empty($sec['logs'])) continue; ?>
                        
                        <?php if ($sec['title'] == 'archive'): ?>
                            <div style="margin-bottom: 24px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden;">
                                <div onclick="$('#archivedLogs').slideToggle(); $(this).find('i.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');" style="padding: 15px 20px; font-weight: 700; color: #4B5563; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fa fa-archive" style="color: #6B7280;"></i>
                                        <span>Tampilkan Riwayat Bimbingan Selesai (<?= count($sec['logs']) ?>)</span>
                                    </div>
                                    <i class="fa fa-chevron-down toggle-icon" style="color: #9CA3AF;"></i>
                                </div>
                                <div id="archivedLogs" style="display: none; padding: 20px; border-top: 1px solid #E5E7EB; background: #fff;">
                        <?php endif; ?>

                        <?php foreach($sec['logs'] as $log_index => $log): 
                            $is_latest = ($sec['title'] == 'active' && $log_index === count($sec['logs']) - 1) || ($sec['title'] == 'archive' && empty($active_logs) && $log_index === count($sec['logs']) - 1);
                    
                                                if (!empty($log->catatan_mahasiswa)):
                            $display_name_mhs = $nm_pd;
                            $words = explode(' ', $display_name_mhs);
                            $initials_mhs = (count($words) >= 2) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($words[0], 0, 2));
                    ?>
                        <div class="thread-card">
                            <div class="thread-header">
                                <div class="avatar"><?= $initials_mhs ?></div>
                                <div class="thread-meta">
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <strong style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $display_name_mhs ?></strong>
                                        <span class="badge" style="background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Mahasiswa</span>
                                    </div>
                                </div>
                                <?php if ($is_latest && empty($log->catatan_dosen) && empty($log->status_pendamping)): ?>
                                    <div class="thread-badge update" style="background:#EEF2FF; border-color:#C7D2FE; color:var(--primary); font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">Menunggu Review</div>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                <?php 
                                    $tgl = (!empty($log->tanggal_bimbingan) && $log->tanggal_bimbingan != '0000-00-00') ? tgl_indo_short($log->tanggal_bimbingan) : ((!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? tgl_indo_short(date('Y-m-d', strtotime($log->created_at))) : 'Tanggal Tidak Tersedia');
                                    $jam = (!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? substr($log->created_at, 11, 5) : '-';
                                ?>
                                <span style="font-weight:600;"><i class="fa fa-clock-o"></i> <?= $tgl ?> &bull; <?= $jam ?></span>
                            </div>
                            <div class="thread-body" style="padding-top: 5px;">
                                <div class="modern-log-container">
                                    <div class="progress-row" style="margin-bottom: 22px;">
                                        <span style="font-weight: 700; color: var(--text-muted); font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Progress Bimbingan:</span>
                                        <span class="badge" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-weight: 700; font-size: 13.5px; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; line-height: 1.4; vertical-align: middle; white-space: normal; height: auto;">
                                            <?= $log->tahapan ?> (<?= $log->progress_persen ?>%)
                                        </span>
                                    </div>
                                    <?php 
                                    $parsed = parse_bimbingan_catatan($log->catatan_mahasiswa);
                                    if ($parsed): 
                                    ?>
                                        <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 12px;">
                                            <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">Aktivitas Mingguan</h5>
                                                <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['aktivitas']) ? htmlspecialchars($parsed['aktivitas']) : '<em style="color:#9CA3AF;">Tidak ada catatan aktivitas</em>' ?></div>
                                            </div>
                                            
                                            <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">Kendala &amp; Hambatan</h5>
                                                <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['kendala']) ? htmlspecialchars($parsed['kendala']) : '<em style="color:#9CA3AF;">Tidak ada catatan kendala yang dilaporkan</em>' ?></div>
                                            </div>
                                            
                                            <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">Target Selanjutnya</h5>
                                                <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['target']) ? htmlspecialchars($parsed['target']) : '<em style="color:#9CA3AF;">Tidak ada target spesifik</em>' ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-top: 15px;">
                                            <h5 style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">Catatan Bimbingan Mahasiswa</h5>
                                            <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500;"><?= nl2br($log->catatan_mahasiswa) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php 
                                    $is_u = isset($is_utama) ? $is_utama : false;
                                    $is_p = isset($is_pendamping) ? $is_pendamping : false;
                                    $hide_docs = false;
                                    $hide_msg = "";
                                    
                                    $is_last = (isset($last_mhs_log) && $last_mhs_log && $log->id_log == $last_mhs_log->id_log);
                                    
                                    if ($is_last && isset($expected_reviewer)) {
                                        if ($is_u && $expected_reviewer == 'pendamping' && $log->status_pendamping != 'Lanjut ke Utama') {
                                            $hide_docs = true;
                                            $hide_msg = "Dokumen & AI Review disembunyikan. Bimbingan sedang ditinjau oleh <strong>Dosen Pendamping</strong>.";
                                        } elseif ($is_p && $expected_reviewer == 'utama' && $log->status_pendamping != 'Konsultasi ke Pendamping') {
                                            $hide_docs = true;
                                            $hide_msg = "Dokumen & AI Review disembunyikan. Hanya dapat diakses jika dialihkan oleh <strong>Dosen Utama</strong>.";
                                        }
                                    } else {
                                        if ($is_u) {
                                            if ($log->status_pendamping == 'Konsultasi ke Pendamping' || $log->status_pendamping == 'Perlu Revisi') {
                                                $hide_docs = true;
                                                $hide_msg = "Dokumen & AI Review disembunyikan. Bimbingan sedang ditinjau oleh <strong>Dosen Pendamping</strong>.";
                                            }
                                        } elseif ($is_p) {
                                            if ($log->status_pendamping != 'Konsultasi ke Pendamping' && $log->status_pendamping != 'Perlu Revisi' && $log->status_pendamping != 'Lanjut ke Utama') {
                                                $hide_docs = true;
                                                $hide_msg = "Dokumen & AI Review disembunyikan. Hanya dapat diakses jika dialihkan oleh <strong>Dosen Utama</strong>.";
                                            }
                                        }
                                    }
                                    
                                    // Tentukan data AI review mana yang bisa dilihat oleh dosen saat ini
                                    $ai_review_data = null;
                                    if ($is_u) {
                                        $ai_review_data = $log->ai_review_utama;
                                    } elseif ($is_p) {
                                        $ai_review_data = $log->ai_review_pendamping;
                                    }
                                ?>
                                <?php if($log->file_lampiran_mahasiswa): ?>
                                    <?php if ($hide_docs): ?>
                                        <div style="margin-top: 15px; padding: 10px 15px; background: #FEF2F2; color: #991B1B; border: 1px dashed #FCA5A5; border-radius: 8px; font-size: 12.5px; display: inline-block;">
                                            <i class="fa fa-lock" style="margin-right: 5px;"></i> <?= $hide_msg ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">
                                            <a href="javascript:;" onclick="bukaGdrive('<?= $log->file_lampiran_mahasiswa ?>', '<?= $log->id_log ?>')" class="file-attachment" style="margin-top: 0;">
                                                <i class="fa fa-file-text-o"></i> Lihat Dokumen Mahasiswa (Google Drive)
                                            </a>
                                            <?php if(isset($is_dosen) || true): // Any dosen could trigger it theoretically ?>
                                                <?php if($has_api_key): ?>
                                                    <button id="btnAiReview_<?= $log->id_log ?>" onclick="generateAiReview('<?= $log->id_log ?>', '<?= $data_skripsi->id_judul_skripsi ?>')" class="file-attachment" style="margin-top: 0; background: linear-gradient(135deg, #7C3AED, #4F46E5); color: white; border: none; cursor: pointer; border-color: #6D28D9; <?= !empty($ai_review_data) ? 'display: none;' : '' ?>">
                                                        <i class="fa fa-magic"></i> Minta AI Review
                                                    </button>
                                                <?php else: ?>
                                                    <a href="<?= site_url('Setting_api_key') ?>" class="file-attachment" style="margin-top: 0; background: linear-gradient(135deg, #f97316, #ea580c); color: white; border: none; cursor: pointer; border-color: #c2410c; text-decoration: none; <?= !empty($ai_review_data) ? 'display: none;' : '' ?>" id="btnAiReview_<?= $log->id_log ?>">
                                                        <i class="fa fa-key"></i> Setting API Key
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!$hide_docs): ?>
                                    <?php if (!empty($ai_review_data)): ?>
                                        <div id="aiReviewContainer_<?= $log->id_log ?>" style="background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                                <div style="display: flex; align-items: center; gap: 10px; color: #4F46E5; cursor: pointer; flex: 1;" onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).closest('div').find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');">
                                                    <i class="fa fa-magic" style="font-size: 18px;"></i>
                                                    <h4 style="margin: 0; font-weight: 800; font-size: 14px;">Catatan Review AI</h4>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <i class="fa fa-chevron-down toggle-icon" style="color: #6B7280; cursor: pointer;" onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).toggleClass('fa-chevron-down fa-chevron-up');"></i>
                                                    <button type="button"
                                                        id="btnResetAiReview_<?= $log->id_log ?>"
                                                        onclick="resetAiReview('<?= $log->id_log ?>', '<?= $data_skripsi->id_judul_skripsi ?>')"
                                                        title="Hapus hasil AI Review ini agar dapat digenerate ulang"
                                                        style="background: none; border: 1px solid #FCA5A5; color: #DC2626; border-radius: 6px; padding: 3px 8px; font-size: 12px; cursor: pointer; line-height: 1; margin-left: 4px; transition: all 0.2s;"
                                                        onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='none'">
                                                        <i class="fa fa-trash-o"></i> Reset
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="aiReviewContent_<?= $log->id_log ?>" class="ai-review-content" style="font-size: 13px; color: #374151; line-height: 1.7; font-weight: 500; display: none; margin-top: 15px;">
                                                <div class="ai-review-text"><?= nl2br(htmlspecialchars($ai_review_data)) ?></div>
                                                <textarea class="ai-raw-text" style="display:none;"><?= htmlspecialchars($ai_review_data) ?></textarea>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div id="aiReviewContainer_<?= $log->id_log ?>" style="display: none; background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; cursor: pointer;" onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');">
                                                <div style="display: flex; align-items: center; gap: 10px; color: #4F46E5;">
                                                    <i class="fa fa-magic" style="font-size: 18px;"></i>
                                                    <h4 style="margin: 0; font-weight: 800; font-size: 14px;">Catatan Review AI</h4>
                                                </div>
                                                <i class="fa fa-chevron-up toggle-icon" style="color: #6B7280;"></i>
                                            </div>
                                            <div id="aiReviewContent_<?= $log->id_log ?>" class="ai-review-content" style="font-size: 13px; color: #374151; line-height: 1.7; font-weight: 500; margin-top: 15px;">
                                                <div class="ai-review-text"></div>
                                                <textarea class="ai-raw-text" style="display:none;"></textarea>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php 
                                                if (!empty($log->catatan_pendamping)): 
                            $nm_pend = $nm_doping_2;
                            $initials_p = get_avatar_initials($nm_pend, 'DP');
                    ?>
                        <div class="thread-card dosen-reply" style="border-left-color: #059669; margin-left: 100px;">
                            <div class="thread-header">
                                <div class="avatar dosen" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);"><?= $initials_p ?></div>
                                <div class="thread-meta">
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <strong style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $nm_pend ?></strong>
                                        <span class="badge" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Dosen Pendamping</span>
                                    </div>
                                </div>
                                <?php if($log->status_pendamping == 'Perlu Revisi'): ?>
                                    <div class="thread-badge review" style="background:#FEE2E2; border-color:#FCA5A5; color:#991B1B; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">Perlu Revisi</div>
                                <?php else: ?>
                                    <div class="thread-badge review" style="background:#D1FAE5; border-color:#6EE7B7; color:#065F46; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;"><?= $log->status_pendamping ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($log->reviewed_at_pendamping) && $log->reviewed_at_pendamping != '0000-00-00 00:00:00'): ?>
                                <div style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                    <span style="font-weight:600;"><i class="fa fa-clock-o"></i> <?= tgl_indo_short(substr($log->reviewed_at_pendamping, 0, 10)) . ' &bull; ' . substr($log->reviewed_at_pendamping, 11, 5) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="thread-body" style="padding-top: 5px;">
                                <div style="font-size: 14px; line-height: 1.65; color: #374151; font-weight: 500;"><?= nl2br($log->catatan_pendamping) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php 
                                                if (!empty($log->catatan_dosen)): 
                            $display_name_d = trim($log->first_name);
                            if (!empty($log->last_name) && strpos(strtolower($log->first_name), strtolower($log->last_name)) === false) {
                                $display_name_d .= ' ' . trim($log->last_name);
                            }
                            if (empty($display_name_d)) $display_name_d = $nm_doping_1;
                            $initials_d = get_avatar_initials($display_name_d, 'DU');
                    ?>
                        <div class="thread-card dosen-reply" <?= !empty($log->catatan_pendamping) ? 'style="margin-left: 150px;"' : '' ?>>
                            <div class="thread-header">
                                <div class="avatar dosen"><?= $initials_d ?></div>
                                <div class="thread-meta">
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <strong style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $display_name_d ?></strong>
                                        <span class="badge" style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Dosen Pembimbing</span>
                                    </div>
                                </div>
                                <?php 
                                $badge_text_utama = $log->status_bimbingan;
                                if (!empty($log->catatan_pendamping) && !empty($log->reviewed_at_pendamping) && $log->reviewed_at_pendamping != '0000-00-00 00:00:00') {
                                    if (strtotime($log->created_at) < strtotime($log->reviewed_at_pendamping)) {
                                        $badge_text_utama = 'Konsultasi ke Pendamping';
                                    }
                                }
                                ?>
                                <?php if($badge_text_utama == 'Perlu Revisi'): ?>
                                    <div class="thread-badge review" style="background:#FEE2E2; border-color:#FCA5A5; color:#991B1B; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">Perlu Revisi</div>
                                <?php elseif($badge_text_utama == 'Disetujui'): ?>
                                    <div class="thread-badge review" style="background:#D1FAE5; border-color:#6EE7B7; color:#065F46; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">Disetujui</div>
                                <?php elseif($badge_text_utama == 'Konsultasi ke Pendamping'): ?>
                                    <div class="thread-badge update" style="background:#FEF3C7; border-color:#FDE68A; color:#92400E; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">Delegasi ke Pendamping</div>
                                <?php else: ?>
                                    <div class="thread-badge update" style="background:#EEF2FF; border-color:#C7D2FE; color:var(--primary); font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;"><?= $badge_text_utama ?></div>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                <span style="font-weight:600;"><i class="fa fa-clock-o"></i> <?= tgl_indo_short($log->tanggal_bimbingan) . ' &bull; ' . substr($log->created_at, 11, 5) ?></span>
                            </div>
                            <div class="thread-body" style="padding-top: 5px;">
                                <div style="font-size: 14px; line-height: 1.65; color: #374151; font-weight: 500;"><?= nl2br($log->catatan_dosen) ?></div>
                                <?php if($log->file_lampiran_dosen): ?>
                                    <br>
                                    <a href="<?= base_url('assets/uploads/bimbingan/'.$log->file_lampiran_dosen) ?>" target="_blank" class="file-attachment" style="margin-top: 15px;">
                                        <i class="fa fa-download"></i> Unduh Lampiran Dosen
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php endforeach; ?>
                    
                    <?php if ($sec['title'] == 'archive'): ?>
                            </div> <!-- Close archivedLogs -->
                        </div> <!-- Close archive container -->
                    <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>



                <?php
                // Logika $pending_bimbingan_log sudah dipindah ke atas
                ?>

                <?php if ($pending_bimbingan_log): ?>
                
                <div class="reply-box">
                    <h3 style="font-size:14px; margin:0 0 4px; font-weight:700;">Review Bimbingan Mahasiswa</h3>
                    <p style="font-size:12px; color:#6B7280; margin:0 0 14px;">Berikan penilaian dan catatan untuk bimbingan yang diajukan mahasiswa.</p>
                    <form id="form_balas_pesan">
                        <input type="hidden" name="id_judul_skripsi" value="<?= $data_skripsi->id_judul_skripsi ?>">
                        <input type="hidden" name="id_log_bimbingan" value="<?= $pending_bimbingan_log->id_log ?>">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <div class="row" style="margin-bottom:12px;">
                            <div class="col-md-6">
                                <label style="font-size:12px; color:#6B7280;">Status Bimbingan</label>
                                <select name="status_bimbingan" id="select_status_bimbingan" class="form-control" style="border-radius:8px;">
                                    <?php if ($is_p && !$is_u): ?>
                                    <option value="Lanjut ke Utama">Lanjut ke Utama</option>
                                    <option value="Perlu Revisi">Perlu Revisi</option>
                                    <?php else: ?>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Perlu Revisi">Perlu Revisi</option>
                                    <option value="Menunggu Review">Menunggu Review / Pending State</option>
                                    <?php if (!empty($data_skripsi->dosen_pembimbing_2) && $data_skripsi->dosen_pembimbing_2 != '-' && empty($pending_bimbingan_log->catatan_pendamping)): ?>
                                    <option value="Konsultasi ke Pendamping">Konsultasikan ke Dosen Pendamping</option>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php if (!$is_p || $is_u): ?>
                            <div class="col-md-6" id="progress_container">
                                <label style="font-size:12px; color:#6B7280;">Set Progress (%)</label>
                                <input type="number" name="progress_persen" class="form-control" style="border-radius:8px;" value="<?= $data_skripsi->current_progress ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <textarea name="catatan_dosen_wali" class="form-control" style="resize:vertical; min-height:90px; border-radius:8px; margin-bottom:14px;" placeholder="Ketik catatan bimbingan atau feedback untuk mahasiswa..."></textarea>
                        <?php 
                            $pending_ai_data = null;
                            if (isset($is_utama) && $is_utama) $pending_ai_data = $pending_bimbingan_log->ai_review_utama;
                            elseif (isset($is_pendamping) && $is_pendamping) $pending_ai_data = $pending_bimbingan_log->ai_review_pendamping;
                        ?>
                        <div style="text-align:right;">
                            <button type="button" id="btnSalinAI" onclick="salinKesimpulanAi('<?= $pending_bimbingan_log->id_log ?>')" class="btn btn-outline-primary" style="<?= empty($pending_ai_data) ? 'display:none;' : '' ?> border-radius:8px; font-weight:600; padding:8px 22px; margin-right: 8px;">
                                <i class="fa fa-copy" style="margin-right:6px;"></i> Salin Kesimpulan AI
                            </button>
                            <button type="submit" id="btnSave" class="btn btn-primary" style="background:var(--primary); border-radius:8px; font-weight:600; border:none; padding:8px 22px;">
                                <i class="fa fa-check-circle" style="margin-right:6px;"></i> Kirim Review Bimbingan
                            </button>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="reply-box" style="border-top-color: #9CA3AF; text-align:center; padding: 30px 20px;">
                    <div style="font-size:36px; margin-bottom:10px;">☕</div>
                    <h3 style="font-size:16px; margin:0 0 8px; font-weight:700; color:#374151;">Tidak Ada Bimbingan Pending</h3>
                    <p style="font-size:13px; color:#6B7280; margin:0;">Mahasiswa belum mengajukan bimbingan baru atau seluruh bimbingan telah direview.</p>
                </div>
                <?php endif; ?>
                
            </div>
            
            
            <div class="sidebar-panel">
                <div class="sidebar-cover">
                    <?php 
                    $photo_src = !empty($foto_mhs) 
                        ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . rawurlencode($foto_mhs) 
                        : base_url('assets/no_profile_photo.png');
                    ?>
                    <img src="<?= $photo_src ?>" 
                         alt="<?= $nm_pd ?>"
                         onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';"
                         style="width: 100%; height: 100%; object-fit: cover; object-position: center 35%; display: block;">
                </div>
                
                <div class="sidebar-body">
                    <div class="sidebar-student-name"><?= $nm_pd ?></div>
                    <div class="sidebar-nim">Mahasiswa &bull; <?= $data_skripsi->nipd ?></div>
                    <div class="sidebar-judul">
                        "<?= $data_skripsi->judul_ditetapkan ?>"
                    </div>
                    
                    <div class="sidebar-footer">
                        <div class="sidebar-meta-item">
                            <div class="sidebar-meta-icon icon-dosen">👨‍🏫</div>
                            <div class="sidebar-meta-text">
                                <div class="sidebar-meta-label">Dosen Pembimbing</div>
                                <div class="sidebar-meta-value"><?= $nm_doping_1 ?></div>
                                <?php if (!empty($nm_doping_2) && $nm_doping_2 != '-'): ?>
                                <div class="sidebar-meta-label" style="margin-top: 8px;">Dosen Pendamping</div>
                                <div class="sidebar-meta-value"><?= $nm_doping_2 ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php 
                            $kategori_color = '#10B981'; $bg_color = '#D1FAE5'; $icon = '🟢';
                            if ($data_skripsi->kategori_health == 'Perlu Perhatian') { $kategori_color = '#D97706'; $bg_color = '#FEF3C7'; $icon = '🟡'; }
                            if ($data_skripsi->kategori_health == 'Risiko Tinggi') { $kategori_color = '#DC2626'; $bg_color = '#FEE2E2'; $icon = '🔴'; }
                            if ($data_skripsi->kategori_health == 'Belum Ada Bimbingan') { $kategori_color = '#6B7280'; $bg_color = '#F3F4F6'; $icon = '⚪'; }
                        ?>
                        <div class="sidebar-meta-item">
                            <div class="sidebar-meta-icon" style="background:<?= $bg_color ?>; color:<?= $kategori_color ?>;"><?= $icon ?></div>
                            <div class="sidebar-meta-text">
                                <div class="sidebar-meta-label">Academic Health</div>
                                <div class="sidebar-meta-value" style="color:<?= $kategori_color ?>;">
                                    <?= $data_skripsi->kategori_health ?> (Score: <?= round($data_skripsi->academic_health_score) ?>)
                                </div>
                            </div>
                        </div>
                        
                        <div class="sidebar-meta-item">
                            <div class="sidebar-meta-icon icon-progress">📈</div>
                            <div class="sidebar-meta-text" style="width:100%;">
                                <div class="sidebar-meta-label" style="display:flex; justify-content:space-between;">
                                    <span>Progress Bimbingan</span>
                                    <span style="color:var(--primary); font-weight:800;"><?= $data_skripsi->current_progress ?>%</span>
                                </div>
                                <div class="progress-bar-container" style="margin-top:4px;">
                                    <div class="progress-bar-fill" style="width: <?= $data_skripsi->current_progress ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


<div id="skripsiChatWidget" style="position: fixed; bottom: 30px; right: 30px; width: 350px; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: none; flex-direction: column; z-index: 9999; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s ease; height: 500px; transform: translateY(120%); opacity: 0; border: 1px solid #E5E7EB;">
    
    <div class="chat-header" style="background: linear-gradient(135deg, #4F46E5, #4338CA); color: white; padding: 16px; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: #fff; padding: 2px; overflow: hidden;">
                <img src="<?= $photo_src ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
            </div>
            <div>
                <h4 style="margin: 0; font-size: 14px; font-weight: 700; color: #fff;">Chat Skripsi</h4>
                <div style="font-size: 11px; color: #E0E7FF; opacity: 0.9; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"><?= $nm_pd ?></div>
            </div>
        </div>
        <button id="btnToggleChat" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; outline: none; transition: transform 0.2s;">
            <i class="fa fa-chevron-down"></i>
        </button>
    </div>

    
    <div id="chatBodyScroll" style="flex: 1; overflow-y: auto; padding: 16px; background: #F9FAFB; display: flex; flex-direction: column; gap: 12px;">
        <div style="text-align: center; color: #9CA3AF; font-size: 11px; margin-bottom: 10px;">Gunakan chat ini untuk komunikasi santai di luar log bimbingan resmi.</div>
        <div id="chatMessagesContainer" style="display: flex; flex-direction: column; gap: 12px;">
            
            <div style="text-align: center; padding: 20px;">
                <i class="fa fa-circle-o-notch fa-spin fa-2x" style="color: #CBD5E1;"></i>
            </div>
        </div>
    </div>

    
    <div class="chat-footer" style="padding: 12px 16px; background: #fff; border-top: 1px solid #E5E7EB; display: flex; gap: 8px; align-items: flex-end;">
        <textarea id="chatInputMsg" rows="1" placeholder="Tulis pesan..." style="flex: 1; border: 1px solid #D1D5DB; border-radius: 20px; padding: 10px 14px; font-size: 13px; outline: none; resize: none; max-height: 100px; overflow-y: auto; line-height: 1.4; transition: border-color 0.2s; font-family: inherit;"></textarea>
        <button id="btnSendChat" style="width: 40px; height: 40px; border-radius: 50%; background: #4F46E5; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; flex-shrink: 0; transition: background 0.2s; outline: none;">
            <i class="fa fa-paper-plane"></i>
        </button>
    </div>
</div>


<button id="chatBubbleFab" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #4338CA); color: white; border: none; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); display: none; align-items: center; justify-content: center; font-size: 24px; cursor: pointer; z-index: 9999; outline: none; transition: transform 0.2s;">
    <i class="fa fa-comments"></i>
    
    <span id="chatUnreadBadge" style="position: absolute; top: -2px; right: -2px; background: #EF4444; color: white; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 10px; border: 2px solid #fff; display: none;">0</span>
</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalGdrive" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1400px; margin: 3vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; height: 92vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                <h4 class="modal-title" style="font-weight: 800; font-size: 16px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span>📄</span> Pratinjau Dokumen Mahasiswa
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none; transition: all 0.2s;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; background: #F3F4F6; flex: 1; overflow: hidden; position: relative; display: flex;">
                <div id="modalGdriveIframeContainer" style="flex: 1; height: 100%; transition: all 0.3s;">
                    <iframe id="iframeGdrive" src="" style="width: 100%; height: 100%; border: none; display: block; background: #FFFFFF;"></iframe>
                </div>
                <div id="modalGdriveAiReviewContainer" style="width: 400px; height: 100%; background: #FFFFFF; border-left: 1px solid #E5E7EB; display: none; flex-direction: column; flex-shrink: 0; position: relative;">
                    <div style="padding: 16px; background: #F8FAFC; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-magic" style="color: #4F46E5; font-size: 16px;"></i>
                        <h5 style="margin: 0; font-weight: 700; color: #1E293B; font-size: 14px;">Catatan Review AI</h5>
                    </div>
                    <div id="modalGdriveAiReviewContent" style="padding: 20px; overflow-y: auto; flex: 1; font-size: 13px; color: #334155; line-height: 1.7;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
