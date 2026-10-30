<?php
if (!function_exists('clean_markdown_delimiters')) {
    function clean_markdown_delimiters($text)
    {
        $text = trim($text);
        $text = preg_replace('/^[\s\*\-_#:]+/', '', $text);
        $text = preg_replace('/[\s\*\-_#:]+$/', '', $text);
        return trim($text);
    }
}

if (!function_exists('parse_bimbingan_catatan')) {
    function parse_bimbingan_catatan($text)
    {
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
    function format_semester_nama($id_smt)
    {
        if (empty($id_smt))
            return '-';
        $year = substr($id_smt, 0, 4);
        $term = substr($id_smt, 4, 1);
        $term_name = ($term == '1') ? 'Ganjil' : (($term == '2') ? 'Genap' : 'Pendek');
        $next_year = $year + 1;
        return "$year/$next_year - $term_name";
    }
}
?>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
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

    .bimbingan-wrapper * {
        box-sizing: border-box;
    }

    /* Premium Toast Styles */
    .premium-toast {
        position: fixed;
        top: 30px;
        right: 30px;
        background: #FFFFFF;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
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

    .premium-toast.success {
        border-left-color: #10B981;
    }

    .premium-toast.success .premium-toast-icon {
        background: #E6FBF0;
        color: #10B981;
    }

    .premium-toast.warning {
        border-left-color: #F59E0B;
    }

    .premium-toast.warning .premium-toast-icon {
        background: #FEF3C7;
        color: #F59E0B;
    }

    .premium-toast.info {
        border-left-color: #3B82F6;
    }

    .premium-toast.info .premium-toast-icon {
        background: #EFF6FF;
        color: #3B82F6;
    }

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

    .thread-card:last-child {
        margin-bottom: 0;
    }



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
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.04);
    }

    .thread-card.balas-thread::after {
        display: none;
    }

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

    .thread-meta {
        flex: 1;
    }

    .thread-meta strong {
        font-size: 14px;
        display: block;
    }

    .thread-meta span {
        font-size: 12px;
        color: var(--text-muted);
    }

    .thread-badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 600;
        border: 1px solid;
    }

    .thread-badge.update {
        background: #EEF2FF;
        color: var(--primary);
        border-color: #C7D2FE;
    }

    .thread-badge.review {
        background: #FEF3C7;
        color: #92400E;
        border-color: #FDE68A;
    }

    .thread-body {
        padding: 15px 16px;
        font-size: 14px;
    }

    .thread-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 15px;
    }

    .thread-grid .label {
        color: var(--text-muted);
        font-weight: 600;
    }

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
        background-image: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1) 0%, transparent 40%), radial-gradient(circle at bottom left, rgba(0, 0, 0, 0.2) 0%, transparent 40%);
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

    .icon-dosen {
        background: #EEF2FF;
        color: #4F46E5;
    }

    .icon-health {
        background: #F0FDF4;
        color: #10B981;
    }

    .icon-progress {
        background: #FEF3C7;
        color: #D97706;
    }

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

    .progress-bar-container {
        height: 6px;
        background: #E5E7EB;
        border-radius: 999px;
        margin-top: 8px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--primary);
    }

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
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
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

    .timeline-title.title-aktivitas {
        color: #4F46E5;
        background-color: #EEF2FF;
    }

    .timeline-title.title-kendala-warn {
        color: #EF4444;
        background-color: #FEE2E2;
    }

    .timeline-title.title-kendala-safe {
        color: #10B981;
        background-color: #E6FBF0;
    }

    .timeline-title.title-target {
        color: #0891B2;
        background-color: #ECFDF5;
    }

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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
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

    /* Responsiveness */
    @media (max-width: 1199px) {
        .bimbingan-wrapper {
            flex-direction: column-reverse !important;
            gap: 20px !important;
        }

        .sidebar-panel {
            width: 100% !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            padding: 20px !important;
            box-sizing: border-box !important;
            align-items: flex-start !important;
            position: static !important;
        }

        .sidebar-cover {
            width: 120px !important;
            height: 120px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            background: none !important;
        }

        .sidebar-body {
            flex: 1 !important;
            padding: 0 0 0 20px !important;
            text-align: left !important;
        }

        .sidebar-judul {
            font-size: 13px !important;
        }

        .sidebar-footer {
            display: none !important;
        }

        .thread-panel {
            width: 100% !important;
        }
    }
</style>

<div class="page-content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light thread-container">
                <div class="portlet-title">
                    <div class="caption">
                        <a href="<?= site_url('E_skripsi_bimbingan') ?>" class="btn btn-default btn-xs"
                            style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Kembali</a>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?></span>
                    </div>
                    <div class="actions"></div>
                </div>
                <div class="portlet-body">
                    <div class="bimbingan-wrapper">


                        <div class="thread-panel">

                            <!-- Overlay Loading Tunggakan -->
                            <div id="alert_loading_tunggakan"
                                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center;">
                                <div
                                    style="background: #ffffff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 24px 32px; display: flex; align-items: center; gap: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); max-width: 450px; animation: modalPop 0.3s ease-out forwards;">
                                    <style>
                                        @keyframes modalPop {
                                            0% {
                                                transform: scale(0.9);
                                                opacity: 0;
                                            }

                                            100% {
                                                transform: scale(1);
                                                opacity: 1;
                                            }
                                        }
                                    </style>
                                    <div
                                        style="background: #EFF6FF; color: #3B82F6; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="margin: 0 0 6px 0; color: #1E40AF; font-weight: 800; font-size: 16px; letter-spacing: 0.5px;">
                                            Memeriksa Administrasi...
                                        </h4>
                                        <p
                                            style="margin: 0; color: #4B5563; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                            Sistem sedang memverifikasi status pembayaran mahasiswa. Mohon tunggu
                                            sebentar.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Container Peringatan Tunggakan (Default Hidden) -->
                            <div id="alert_tunggakan_container"
                                style="display: none; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                <div
                                    style="background: #EF4444; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h4
                                        style="margin: 0 0 4px 0; color: #991B1B; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">
                                        MAHASISWA MEMILIKI TUNGGAKAN
                                    </h4>
                                    <p
                                        style="margin: 0; color: #991B1B; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                        Mahasiswa yang bersangkutan memiliki tunggakan biaya perkuliahan. Segala bentuk
                                        interaksi bimbingan dan pembuatan agenda dinonaktifkan sementara hingga
                                        tunggakan diselesaikan.
                                    </p>
                                </div>
                            </div>

                            <?php
                            $pending_pendamping = 0;
                            $pending_utama = 0;
                            $oldest_pending_pendamping = null;
                            $oldest_pending_utama = null;
                            foreach ($logs as $l) {
                                if ($l->initiator_type == 'mahasiswa' && !empty($l->catatan_mahasiswa)) {
                                    // Bimbingan dianggap pending (menunggu dosen) selama belum mencapai keputusan final
                                    if ($l->status_bimbingan !== 'Disetujui' && $l->status_bimbingan !== 'Perlu Revisi') {

                                        if (!empty($data_skripsi->dosen_pembimbing_2)) {
                                            // Ada Dosen Pendamping
                                            if (empty($l->status_pendamping) || $l->status_pendamping == 'Menunggu Review' || $l->status_pendamping == 'Konsultasi ke Pendamping') {
                                                $pending_pendamping++;
                                                if ($oldest_pending_pendamping === null || strtotime($l->created_at) < strtotime($oldest_pending_pendamping)) {
                                                    $oldest_pending_pendamping = $l->created_at;
                                                }
                                            } else {
                                                // Jika pendamping sudah memproses (misal: 'Lanjut ke Utama')
                                                $pending_utama++;
                                                if ($oldest_pending_utama === null || strtotime($l->created_at) < strtotime($oldest_pending_utama)) {
                                                    $oldest_pending_utama = $l->created_at;
                                                }
                                            }
                                        } else {
                                            // Tidak ada Dosen Pendamping, langsung Utama
                                            $pending_utama++;
                                            if ($oldest_pending_utama === null || strtotime($l->created_at) < strtotime($oldest_pending_utama)) {
                                                $oldest_pending_utama = $l->created_at;
                                            }
                                        }
                                    }
                                }
                            }

                            $get_age_text = function ($date) {
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
                                <div
                                    style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                    <div
                                        style="background: #3B82F6; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <i class="fa fa-info-circle"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="margin: 0 0 4px 0; color: #1E40AF; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">
                                            INFO PROSES BIMBINGAN
                                        </h4>
                                        <p
                                            style="margin: 0; color: #1E40AF; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                            <?php if ($pending_pendamping > 0): ?>
                                                Terdapat <strong><?= $pending_pendamping ?> berkas bimbingan</strong> yang telah
                                                diajukan sejak <strong><?= $get_age_text($oldest_pending_pendamping) ?></strong>
                                                dan saat ini masih menunggu review / persetujuan dari <strong>Dosen
                                                    Pendamping</strong>. Skor bimbingan mahasiswa tidak akan bertambah sebelum
                                                disetujui.<br>
                                            <?php endif; ?>
                                            <?php if ($pending_utama > 0): ?>
                                                Terdapat <strong><?= $pending_utama ?> berkas bimbingan</strong> yang telah
                                                diajukan sejak <strong><?= $get_age_text($oldest_pending_utama) ?></strong> dan
                                                saat ini menunggu review / persetujuan dari Anda (<strong>Dosen Pembimbing
                                                    Utama</strong>).
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($is_utama) && $is_utama): ?>
                                <?php if ($is_lulus): ?>
                                    <!-- Banner LULUS — tombol agenda disembunyikan -->
                                    <div
                                        style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border: 1px solid #6EE7B7; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                                        <span style="font-size: 32px; flex-shrink:0;">🎓</span>
                                        <div style="flex:1;">
                                            <div style="font-size: 14px; font-weight: 800; color: #065F46; margin-bottom: 2px;">
                                                Mahasiswa Sudah Lulus Sidang</div>
                                            <div style="font-size: 12px; color: #047857; font-weight: 500;">Selamat! Mahasiswa
                                                ini telah dinyatakan <strong>LULUS</strong> pada sidang akhir. Agenda bimbingan
                                                tidak lagi diperlukan.</div>
                                        </div>
                                        <span
                                            style="background: #059669; color: white; font-size: 11px; font-weight: 800; padding: 5px 12px; border-radius: 20px; white-space: nowrap; letter-spacing: 0.5px;">✓
                                            LULUS</span>
                                    </div>
                                <?php else: ?>
                                    <div id="wrapper_btn_buat_agenda" style="margin-bottom: 24px;">
                                        <?php if ($pending_pendamping > 0 || $pending_utama > 0): ?>
                                            <div
                                                style="background: #FEF9C3; border: 1px dashed #FDE047; border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; gap: 14px;">
                                                <span style="font-size: 22px; flex-shrink:0;">🔒</span>
                                                <div style="flex:1;">
                                                    <div
                                                        style="font-size: 13.5px; font-weight: 800; color: #713F12; margin-bottom: 2px;">
                                                        Tidak Bisa Membuat Agenda</div>
                                                    <div style="font-size: 12px; color: #92400E; font-weight: 500;">Masih ada
                                                        bimbingan mahasiswa yang menunggu review/persetujuan. Selesaikan terlebih
                                                        dahulu sebelum membuat agenda baru.</div>
                                                </div>
                                                <button type="button" disabled
                                                    style="background: #D1D5DB; color: #6B7280; border: none; border-radius: 8px; font-weight: 700; padding: 10px 18px; cursor: not-allowed; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; flex-shrink:0;">
                                                    <i class="fa fa-lock"></i> Buat Agenda
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <button type="button" id="btn_buat_agenda_utama" onclick="try { showBuatAgendaModal(); } catch(e) { alert('ERROR: ' + e.message); }"
                                                style="width: 100%; background: linear-gradient(135deg, #7C3AED 0%, #DB2777 100%); color: white; border: none; border-radius: 12px; font-weight: 800; padding: 16px 24px; box-shadow: 0 4px 20px rgba(124, 58, 237, 0.35); transition: all 0.3s ease; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 10px; letter-spacing: 0.5px; cursor: pointer;">
                                                <i class="fa fa-calendar-plus-o" style="font-size: 18px;"></i>
                                                <span>&#x1F3AF; Inisiatif Buat Agenda Bimbingan</span>
                                            </button>
                                            <style>
                                                #btn_buat_agenda_utama:hover {
                                                    transform: translateY(-2px);
                                                    box-shadow: 0 8px 28px rgba(124, 58, 237, 0.45);
                                                }

                                                #btn_buat_agenda_utama:active {
                                                    transform: translateY(0);
                                                }
                                            </style>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- CONTAINER AGENDA -->
                            <div id="panel_agenda_aktif_container" style="display:none; margin-bottom: 24px;"></div>
                            <div id="panel_agenda_arsip_container" style="display:none; margin-bottom: 24px;"></div>
                            <!-- END CONTAINER AGENDA -->



                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
                                <?php if (!empty($data_skripsi->kategori_health) && in_array($data_skripsi->kategori_health, ['Perlu Perhatian', 'Risiko Tinggi'])):
                                    $alert_bg = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#FEF2F2' : '#FEFCE8';
                                    $alert_border = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#FECACA' : '#FEF08A';
                                    $alert_text = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#991B1B' : '#854D0E';
                                    $alert_icon = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
                                    $alert_badge_bg = ($data_skripsi->kategori_health == 'Risiko Tinggi') ? '#EF4444' : '#EAB308';

                                    $reason_text = "Mahasiswa membutuhkan perhatian khusus berdasarkan evaluasi algoritma akademik.";
                                    if ($data_skripsi->kategori_health == 'Perlu Perhatian') {
                                        $real_logs = array_filter($logs, function ($l) {
                                            return !empty($l->catatan_mahasiswa) && $l->status_bimbingan == 'Disetujui'; });
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
                                        $real_logs = array_filter($logs, function ($l) {
                                            return !empty($l->catatan_mahasiswa) && $l->status_bimbingan == 'Disetujui'; });
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
                                                $reasons[] = "sudah " . round($days_diff / 30, 1) . " bulan menghilang / absen bimbingan";
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
                                    <div
                                        style="background: <?= $alert_bg ?>; border: 1px solid <?= $alert_border ?>; border-radius: 12px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: 0; flex: 1;">
                                        <div
                                            style="background: <?= $alert_badge_bg ?>; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <i class="fa <?= $alert_icon ?>"></i>
                                        </div>
                                        <div>
                                            <h4
                                                style="margin: 0 0 4px 0; color: <?= $alert_text ?>; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;">
                                                STATUS: <?= strtoupper((string)$data_skripsi->kategori_health) ?>
                                            </h4>
                                            <p
                                                style="margin: 0; color: <?= $alert_text ?>; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                                <?= $reason_text ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Petunjuk Singkat Bimbingan Dosen -->
                                <?php if (isset($petunjuk_dosen)): ?>
                                    <div
                                        style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: 0; flex: 1;">
                                        <div
                                            style="background: #10B981; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <div style="width: 100%;">
                                            <h4
                                                style="margin: 0 0 8px 0; color: #14532D; font-weight: 800; font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
                                                <span><?= $petunjuk_dosen['title'] ?></span>
                                            </h4>
                                            <p
                                                style="margin: 0 0 8px 0; color: #166534; font-size: 13.5px; line-height: 1.5; font-weight: 500;">
                                                <?= $petunjuk_dosen['desc'] ?>
                                            </p>
                                            <ul
                                                style="margin: 0 0 12px 0; padding-left: 20px; color: #166534; font-size: 13px; line-height: 1.6; font-weight: 700; list-style-type: decimal;">
                                                <?php foreach ($petunjuk_dosen['steps'] as $step): ?>
                                                    <li><?= $step ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <div
                                                style="margin-top: 12px; padding-top: 8px; border-top: 1px dashed #BBF7D0; color: #15803D; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                                <span style="font-size: 16px;">💡</span>
                                                <?= $petunjuk_dosen['action_tip'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($riwayat_seminar)): ?>
                                <div style="margin-bottom: 24px;">
                                    <h4
                                        style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <span>🎓</span> Riwayat Seminar &amp; Sidang
                                    </h4>
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">

                                        <!-- Penetapan Judul -->
                                        <div
                                            style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                            <div
                                                style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #3B82F6; letter-spacing: 0.5px; margin-bottom: 8px;">
                                                Penetapan Judul</div>
                                            <?php if (!empty($data_skripsi->tanggal_disetujui)): ?>
                                                <div
                                                    style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                                                    <?= tgl_indo_short(date('Y-m-d', strtotime($data_skripsi->tanggal_disetujui))) ?>
                                                </div>
                                                <div
                                                    style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                                    <i class="fa fa-clock-o"></i> Umur Judul:
                                                    <?= !empty($umur_judul) ? $umur_judul : '-' ?>
                                                </div>
                                            <?php else: ?>
                                                <div
                                                    style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">
                                                    Belum ditetapkan</div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Sempro -->
                                        <div
                                            style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                            <div
                                                style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6366F1; letter-spacing: 0.5px; margin-bottom: 8px;">
                                                Seminar Proposal</div>
                                            <?php if (!empty($riwayat_seminar['sempro']['tanggal'])): ?>
                                                <div
                                                    style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                                                    <?= tgl_indo_short($riwayat_seminar['sempro']['tanggal']) ?></div>
                                                <?php if (!empty($riwayat_seminar['sempro']['hasil'])): ?>
                                                    <span class="badge"
                                                        style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 10px; padding: 2px 8px; margin-bottom: 8px; display: inline-block;">Hasil:
                                                        <?= ucfirst(str_replace('_', ' ', $riwayat_seminar['sempro']['hasil'])) ?></span>
                                                <?php endif; ?>
                                                <div
                                                    style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['sempro']['durasi'] ?>
                                                    <br> <span
                                                        style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sempro']['label_durasi'] ?></span>
                                                </div>
                                                <?php if (!empty($riwayat_seminar['sempro']['abnormal']) && $riwayat_seminar['sempro']['abnormal']): ?>
                                                    <div
                                                        style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($riwayat_seminar['sempro']['ongoing']): ?>
                                                    <div
                                                        style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">
                                                        Belum dilaksanakan</div>
                                                    <div
                                                        style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br>
                                                        <strong><?= $riwayat_seminar['sempro']['durasi'] ?></strong> <br> <span
                                                            style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sempro']['label_durasi'] ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">
                                                        Belum dilaksanakan</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Semhas -->
                                        <div
                                            style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                            <div
                                                style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #F59E0B; letter-spacing: 0.5px; margin-bottom: 8px;">
                                                Seminar Hasil</div>
                                            <?php if (!empty($riwayat_seminar['semhas']['tanggal'])): ?>
                                                <div
                                                    style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                                                    <?= tgl_indo_short($riwayat_seminar['semhas']['tanggal']) ?></div>
                                                <?php if (!empty($riwayat_seminar['semhas']['hasil'])): ?>
                                                    <span class="badge"
                                                        style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; font-size: 10px; padding: 2px 8px; margin-bottom: 8px; display: inline-block;">Hasil:
                                                        <?= ucfirst(str_replace('_', ' ', $riwayat_seminar['semhas']['hasil'])) ?></span>
                                                <?php endif; ?>
                                                <div
                                                    style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['semhas']['durasi'] ?>
                                                    <br> <span
                                                        style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['semhas']['label_durasi'] ?></span>
                                                </div>
                                                <?php if (!empty($riwayat_seminar['semhas']['abnormal']) && $riwayat_seminar['semhas']['abnormal']): ?>
                                                    <div
                                                        style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($riwayat_seminar['semhas']['ongoing']): ?>
                                                    <div
                                                        style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">
                                                        Belum dilaksanakan</div>
                                                    <div
                                                        style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br>
                                                        <strong><?= $riwayat_seminar['semhas']['durasi'] ?></strong> <br> <span
                                                            style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['semhas']['label_durasi'] ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">
                                                        Belum dilaksanakan</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Sidang -->
                                        <div
                                            style="background: #FFFFFF; border: 1px solid var(--border); border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                            <div
                                                style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #10B981; letter-spacing: 0.5px; margin-bottom: 8px;">
                                                Sidang Meja Hijau</div>
                                            <?php if (!empty($riwayat_seminar['sidang']['tanggal'])): ?>
                                                <div
                                                    style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px;">
                                                    <?= tgl_indo_short($riwayat_seminar['sidang']['tanggal']) ?></div>
                                                <div
                                                    style="font-size: 11px; color: #6B7280; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #E5E7EB;">
                                                    <i class="fa fa-clock-o"></i> <?= $riwayat_seminar['sidang']['durasi'] ?>
                                                    <br> <span
                                                        style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sidang']['label_durasi'] ?></span>
                                                </div>
                                                <?php if (!empty($riwayat_seminar['sidang']['abnormal']) && $riwayat_seminar['sidang']['abnormal']): ?>
                                                    <div
                                                        style="font-size: 10px; color: #DC2626; background: #FEE2E2; padding: 4px 6px; border-radius: 4px; font-weight: 600; margin-top: 8px; display: inline-block;">
                                                        <i class="fa fa-exclamation-triangle"></i> Terlalu cepat (&lt; 21 hr)
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($riwayat_seminar['sidang']['ongoing']): ?>
                                                    <div
                                                        style="font-size: 13px; color: #EF4444; font-weight: 600; font-style: italic; margin-bottom: 4px;">
                                                        Belum dilaksanakan</div>
                                                    <div
                                                        style="font-size: 11px; color: #DC2626; font-weight: 600; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #FECACA;">
                                                        <i class="fa fa-exclamation-circle"></i> Belum ada action selama <br>
                                                        <strong><?= $riwayat_seminar['sidang']['durasi'] ?></strong> <br> <span
                                                            style="font-weight: 400; font-size: 10px;"><?= $riwayat_seminar['sidang']['label_durasi'] ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        style="font-size: 13px; color: #9CA3AF; font-weight: 500; font-style: italic;">
                                                        Belum dilaksanakan</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Old Bimbingan Archive System (Transisi) -->
                            <?php if (!empty($old_bimbingan) && !empty($old_bimbingan_formatted_html)): ?>
                                <div
                                    style="margin-bottom: 24px; background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                    <div onclick="$('#oldBimbinganArchive').slideToggle(); $(this).find('i.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');"
                                        style="padding: 15px 20px; font-weight: 800; color: #3730A3; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="fa fa-history" style="color: #4F46E5; font-size: 16px;"></i>
                                            <span>Arsip Bimbingan Sistem Lama (Masa Transisi)</span>
                                        </div>
                                        <i class="fa fa-chevron-down toggle-icon" style="color: #6366F1;"></i>
                                    </div>
                                    <div id="oldBimbinganArchive"
                                        style="display: none; padding: 20px; border-top: 1px solid #C7D2FE; background: #FAF5FF;">
                                        <div class="old-bimbingan-chat-container"
                                            style="max-height: 400px; overflow-y: auto; padding-right: 8px;">
                                            <?= $old_bimbingan_formatted_html ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($logs)): ?>
                                <div class="alert alert-info"
                                    style="border-radius:12px; border:none; background:#EFF6FF; color:#1E40AF; padding:16px 20px; font-weight:600; display:flex; align-items:center; gap:10px; margin-bottom: 24px;">
                                    <span style="font-size:18px;">ℹ️</span>
                                    <span>Belum ada riwayat bimbingan yang tercatat untuk mahasiswa ini. Berikut adalah
                                        informasi akademik terkait mahasiswa:</span>
                                </div>

                                <div class="dashboard-empty-grid">
                                    <div class="dashboard-empty-card">
                                        <div class="dashboard-empty-icon" style="background:#E0F2FE; color:#0284C7;">👨‍🏫
                                        </div>
                                        <div class="dashboard-empty-content">
                                            <div class="dashboard-empty-label">Dosen Wali / Pembimbing Akademik</div>
                                            <div class="dashboard-empty-value">
                                                <?= !empty($nm_dosen_wali) ? $nm_dosen_wali : '-' ?></div>
                                            <div class="dashboard-empty-subtext">Mendampingi perkembangan akademik mahasiswa
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dashboard-empty-card">
                                        <div class="dashboard-empty-icon" style="background:#FEF3C7; color:#D97706;">⏳</div>
                                        <div class="dashboard-empty-content">
                                            <div class="dashboard-empty-label">Umur Judul Tugas Akhir</div>
                                            <div class="dashboard-empty-value">
                                                <?= !empty($umur_judul) ? $umur_judul : '-' ?></div>
                                            <div class="dashboard-empty-subtext">
                                                <?php if (!empty($data_skripsi->tanggal_disetujui)): ?>
                                                    Disetujui sejak:
                                                    <?= tgl_indo_short(date('Y-m-d', strtotime($data_skripsi->tanggal_disetujui))) ?>
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
                                            <span class="badge"
                                                style="background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px;">
                                                Semester <?= format_semester_nama($krs_terakhir->id_smt) ?> &bull; Status:
                                                <?= $krs_terakhir->nm_status_krs ?>
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
                                                            <td><code
                                                                    style="background:#F3F4F6; color:#374151; font-weight:700; padding:2px 6px; border-radius:4px; font-size:12px;"><?= $course->kode_mk ?></code>
                                                            </td>
                                                            <td style="font-weight: 600; color: #111827;"><?= $course->nm_mk ?></td>
                                                            <td
                                                                style="text-align: center; font-weight: 700; color: var(--primary);">
                                                                <?= round($course->sks_mk) ?></td>
                                                            <td style="text-align: center;"><span class="badge"
                                                                    style="background:#EEF2FF; color:#4F46E5; font-weight:600; font-size:11px;"><?= $course->nm_kls ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr
                                                        style="background: #F9FAFB; font-weight: 700; border-top: 2px solid var(--border);">
                                                        <td colspan="2"
                                                            style="text-align: right; padding: 12px; color: var(--text-muted);">
                                                            Total Kredit:</td>
                                                        <td style="text-align: center; color: var(--primary); font-size: 15px;">
                                                            <?= $total_sks_krs ?> SKS</td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div style="text-align:center; padding: 40px 20px; color: var(--text-muted);">
                                            <span style="font-size:36px; display:block; margin-bottom:12px;">📁</span>
                                            <p style="font-weight: 600; margin: 0; font-size: 14px;">Belum ada data Kartu
                                                Rencana Studi (KRS) untuk semester terbaru.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php
                                if (!function_exists('get_avatar_initials')) {
                                    function get_avatar_initials($name, $default = 'DS')
                                    {
                                        $titles = ['dr', 'dr.', 'drg', 'drg.', 'prof', 'prof.', 'ir', 'ir.', 'h', 'h.', 'hj', 'hj.', 'drs', 'drs.', 'dra', 'dra.', 'ns', 'ns.', 's.s.t.', 'm.si'];
                                        $words = explode(' ', $name);
                                        $clean = array_filter($words, function ($w) use ($titles) {
                                            $w = strtolower(trim($w));
                                            if (in_array($w, $titles))
                                                return false;
                                            if (strpos($w, '.') !== false)
                                                return false;
                                            if (strpos($w, ',') !== false)
                                                return false;
                                            return strlen($w) > 0;
                                        });
                                        $clean = array_values($clean);
                                        if (count($clean) >= 2)
                                            return strtoupper(substr($clean[0], 0, 1) . substr($clean[1], 0, 1));
                                        if (count($clean) == 1)
                                            return strtoupper(substr($clean[0], 0, 2));
                                        return $default;
                                    }
                                }

                                $archived_logs = [];
                                $active_logs = [];
                                $total_logs = count($logs);

                                $current_thread = [];
                                foreach ($logs as $index => $log) {
                                    $current_thread[] = $log;
                                    if ($log->status_bimbingan == 'Disetujui') {
                                        // Sebuah bimbingan selesai, lipat seluruh thread ini ke archive
                                        foreach ($current_thread as $t_log) {
                                            $archived_logs[] = $t_log;
                                        }
                                        $current_thread = []; // Reset untuk bimbingan selanjutnya
                                    }
                                }

                                // Jika masih ada log yang belum disetujui (sisa thread terakhir), jadikan active
                                if (!empty($current_thread)) {
                                    foreach ($current_thread as $t_log) {
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
                                        if ($jenis === 'balas' || $jenis === 'tanya')
                                            continue;
                                        if (!empty($chk->catatan_mahasiswa)) {
                                            $mhs_logs[] = $chk;
                                        }
                                    }

                                    $last_mhs_log = !empty($mhs_logs) ? end($mhs_logs) : null;
                                    $prev_mhs_log = count($mhs_logs) > 1 ? $mhs_logs[count($mhs_logs) - 2] : null;

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
                                            if ((empty($last_mhs_log->catatan_pendamping) && $expected_reviewer == 'pendamping') || $last_mhs_log->status_pendamping == 'Konsultasi ke Pendamping') {
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

                                <?php foreach ($sections as $sec): ?>
                                    <?php if (empty($sec['logs']))
                                        continue; ?>

                                    <?php if ($sec['title'] == 'archive'): ?>
                                        <div
                                            style="margin-bottom: 24px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden;">
                                            <div onclick="$('#archivedLogs').slideToggle(); $(this).find('i.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');"
                                                style="padding: 15px 20px; font-weight: 700; color: #4B5563; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; user-select: none;">
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <i class="fa fa-archive" style="color: #6B7280;"></i>
                                                    <?php
                                                    $jml_mhs = 0;
                                                    foreach ($sec['logs'] as $sl) {
                                                        if ($sl->initiator_type == 'mahasiswa')
                                                            $jml_mhs++;
                                                    }
                                                    ?>
                                                    <span>Tampilkan Riwayat Bimbingan Selesai (<?= $jml_mhs ?>)</span>
                                                </div>
                                                <i class="fa fa-chevron-down toggle-icon" style="color: #9CA3AF;"></i>
                                            </div>
                                            <div id="archivedLogs"
                                                style="display: none; padding: 20px; border-top: 1px solid #E5E7EB; background: #fff;">
                                            <?php endif; ?>

                                            <?php $prev_initiator = null;
                                            foreach ($sec['logs'] as $log_index => $log):
                                                $is_latest = ($sec['title'] == 'active' && $log_index === count($sec['logs']) - 1) || ($sec['title'] == 'archive' && empty($active_logs) && $log_index === count($sec['logs']) - 1);

                                                $is_reply_to_agenda = ($log->initiator_type == 'mahasiswa' && $prev_initiator == 'dosen');
                                                $indent_base = $is_reply_to_agenda ? 40 : 0;

                                                if ($log->initiator_type == 'dosen'):
                                                    $display_name_d = trim((string)$log->first_name);
                                                    if (!empty($log->last_name) && strpos(strtolower((string)$log->first_name), strtolower((string)$log->last_name)) === false) {
                                                        $display_name_d .= ' ' . trim((string)$log->last_name);
                                                    }
                                                    if (empty($display_name_d))
                                                        $display_name_d = $nm_doping_1;
                                                    $initials_d = get_avatar_initials($display_name_d, 'DU');
                                                    ?>
                                                    <div class="thread-card"
                                                        style="border-left: 4px solid #4F46E5; background: #F9FAFB; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);">
                                                        <div class="thread-header">
                                                            <div class="avatar dosen"
                                                                style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                                                                <?= $initials_d ?></div>
                                                            <div class="thread-meta">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                    <strong
                                                                        style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $display_name_d ?></strong>
                                                                    <span class="badge"
                                                                        style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Dosen
                                                                        Pembimbing Utama</span>
                                                                </div>
                                                            </div>
                                                            <div class="thread-badge update"
                                                                style="background:#EEF2FF; border-color:#C7D2FE; color:#4F46E5; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                <i class="fa fa-bullseye"></i> Bimbingan Terjadwal</div>
                                                        </div>
                                                        <div
                                                            style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                                            <?php
                                                            $tgl = (!empty($log->tanggal_bimbingan) && $log->tanggal_bimbingan != '0000-00-00') ? tgl_indo_short($log->tanggal_bimbingan) : ((!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? tgl_indo_short(date('Y-m-d', strtotime($log->created_at))) : 'Tanggal Tidak Tersedia');
                                                            $jam = (!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? substr($log->created_at, 11, 5) : '-';
                                                            ?>
                                                            <span style="font-weight:600;"><i class="fa fa-clock-o"></i> <?= $tgl ?>
                                                                &bull; <?= $jam ?></span>
                                                        </div>
                                                        <div class="thread-body" style="padding-top: 5px;">
                                                            <div
                                                                style="font-size: 14px; line-height: 1.65; color: #374151; font-weight: 500;">
                                                                <strong>Instruksi Agenda:</strong><br>
                                                                <?= nl2br($log->catatan_mahasiswa) ?>
                                                            </div>

                                                            <?php if (!empty($log->agenda_tgl_mulai) || !empty($log->agenda_jam_mulai) || !empty($log->agenda_prioritas)): ?>
                                                                <div
                                                                    style="background: #EEF2FF; padding: 12px 15px; border-radius: 8px; border: 1px solid #C7D2FE; margin-top: 15px; font-size: 13px; color: #374151;">
                                                                    <div style="font-weight: 700; color: #4338CA; margin-bottom: 8px;"><i
                                                                            class="fa fa-calendar-check-o"></i> Jadwal & Target Pelaksanaan
                                                                    </div>
                                                                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                                                        <?php if (!empty($log->agenda_tgl_mulai)): ?>
                                                                            <div>
                                                                                <strong style="color: #4B5563;">Tanggal:</strong>
                                                                                <?php if (!empty($log->agenda_tgl_selesai) && $log->agenda_tgl_selesai != $log->agenda_tgl_mulai): ?>
                                                                                    <?= tgl_indo_short($log->agenda_tgl_mulai) ?> s/d
                                                                                    <?= tgl_indo_short($log->agenda_tgl_selesai) ?>
                                                                                <?php else: ?>
                                                                                    <?= tgl_indo_short($log->agenda_tgl_mulai) ?>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <?php if (!empty($log->agenda_jam_mulai) && !empty($log->agenda_jam_selesai)): ?>
                                                                            <div>
                                                                                <strong style="color: #4B5563;">Jam:</strong>
                                                                                <?= substr($log->agenda_jam_mulai, 0, 5) ?> -
                                                                                <?= substr($log->agenda_jam_selesai, 0, 5) ?>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <?php if (!empty($log->agenda_prioritas)): ?>
                                                                            <div>
                                                                                <strong style="color: #4B5563;">Prioritas:</strong>
                                                                                <?php
                                                                                $prioColor = '#6B7280';
                                                                                if ($log->agenda_prioritas == 'Tinggi')
                                                                                    $prioColor = '#DC2626';
                                                                                elseif ($log->agenda_prioritas == 'Sedang')
                                                                                    $prioColor = '#D97706';
                                                                                elseif ($log->agenda_prioritas == 'Rendah')
                                                                                    $prioColor = '#059669';
                                                                                ?>
                                                                                <span
                                                                                    style="color: <?= $prioColor ?>; font-weight: 600;"><?= htmlspecialchars($log->agenda_prioritas) ?></span>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <?php if (!empty($log->agenda_fase_seminar)): ?>
                                                                            <div>
                                                                                <strong style="color: #4B5563;">Fase:</strong>
                                                                                <?= htmlspecialchars($log->agenda_fase_seminar) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <div
                                                                style="margin-top: 15px; border-top: 1px dashed #E5E7EB; padding-top: 15px;">
                                                                <div
                                                                    style="display: flex; justify-content: space-between; align-items: center; user-select: none;">
                                                                    <div onclick="$('#agenda_todo_list_<?= $log->id_log ?>').slideToggle(); $(this).find('i.fa-chevron-down, i.fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');"
                                                                        style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                                                        <div
                                                                            style="font-size: 12px; color: #4B5563; font-weight: 700; text-transform: uppercase;">
                                                                            To-Do List Mahasiswa:</div>
                                                                        <i class="fa fa-chevron-up" style="color: #9CA3AF;"></i>
                                                                    </div>
                                                                    <?php
                                                                    $has_completed_todo = false;
                                                                    if (!empty($log->todos)) {
                                                                        foreach ($log->todos as $t) {
                                                                            if ($t->done_by_mhs == 1) {
                                                                                $has_completed_todo = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <?php if ($log->initiator_type == 'dosen'): ?>
                                                                        <?php if ($has_completed_todo): ?>
                                                                            <button class="btn btn-xs btn-default" disabled
                                                                                title="Terkunci karena mahasiswa sudah mengerjakan progres"
                                                                                style="font-weight: 600; border-radius: 4px; padding: 3px 10px; font-size: 11px; border-color: #D1D5DB; color: #9CA3AF; background: #F3F4F6; cursor: not-allowed;">
                                                                                <i class="fa fa-lock"></i> Edit Terkunci
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <button class="btn btn-xs btn-default"
                                                                                onclick="showEditTodoModal('<?= $log->id_log ?>', '<?= $data_skripsi->id_judul_skripsi ?>')"
                                                                                style="font-weight: 600; border-radius: 4px; padding: 3px 10px; font-size: 11px; border-color: #C7D2FE; color: #4F46E5; background: #EEF2FF; box-shadow: 0 1px 2px rgba(79,70,229,0.1);">
                                                                                <i class="fa fa-pencil"></i> Edit To-Do List
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div id="agenda_todo_list_<?= $log->id_log ?>"
                                                                    style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
                                                                    <?php if (empty($log->todos)): ?>
                                                                        <div style="color: #9CA3AF; font-size: 13px; font-style: italic;">
                                                                            Tidak ada to-do list.</div>
                                                                    <?php else: ?>
                                                                        <?php foreach ($log->todos as $t):
                                                                            $itemBg = '#FFFFFF';
                                                                            $itemBorder = '1px solid #E5E7EB';
                                                                            $checkIcon = 'fa-square-o';
                                                                            $checkColor = '#9CA3AF';
                                                                            $textDecoration = 'none';
                                                                            $textColor = '#111827';
                                                                            $statusBadge = '';
                                                                            $verifyBtn = '';

                                                                            $badgeStyle = "display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; line-height: 1.2;";
                                                                            $btnStyle = "border-radius: 6px; font-weight: 700; padding: 5px 12px; font-size: 11px; display: inline-flex; align-items: center; gap: 5px;";

                                                                            if ($t->done_by_mhs == 1) {
                                                                                $itemBg = '#F0FDF4';
                                                                                $itemBorder = '1px solid #BBF7D0';
                                                                                $checkIcon = 'fa-check-square-o';
                                                                                $statusBadge = '<span class="badge" style="background:#D1FAE5; color:#065F46; ' . $badgeStyle . '"><i class="fa fa-flag"></i> Dilaporkan Selesai</span>';

                                                                                if ($t->verified_dosen == 0) {
                                                                                    $verifyBtn = '
                                                                <div style="display: flex; gap: 6px; align-items: center;">
                                                                    <button class="btn btn-sm btn-success" title="Setujui" onclick="verifyTodoDosen(\'' . $t->id_todo . '\', 1, this)" style="' . $btnStyle . ' width: 32px; height: 32px; justify-content: center; padding: 0; box-shadow: 0 2px 4px rgba(16,185,129,0.2);"><i class="fa fa-check" style="margin: 0; font-size: 14px;"></i></button>
                                                                    <button class="btn btn-sm btn-danger" title="Tolak" onclick="verifyTodoDosen(\'' . $t->id_todo . '\', -1, this)" style="' . $btnStyle . ' width: 32px; height: 32px; justify-content: center; padding: 0; box-shadow: 0 2px 4px rgba(239,68,68,0.2);"><i class="fa fa-times" style="margin: 0; font-size: 14px;"></i></button>
                                                                </div>
                                                            ';
                                                                                    $checkColor = '#10B981';
                                                                                } elseif ($t->verified_dosen == 1) {
                                                                                    $itemBg = '#EFF6FF';
                                                                                    $itemBorder = '1px solid #BFDBFE';
                                                                                    $checkColor = '#2563EB';
                                                                                    $textDecoration = 'line-through';
                                                                                    $textColor = '#6B7280';
                                                                                    $statusBadge .= ' <span class="badge" style="background:#4F46E5; color:#fff; ' . $badgeStyle . '"><i class="fa fa-check-circle"></i> Disetujui Dosen</span>';
                                                                                } elseif ($t->verified_dosen == -1) {
                                                                                    $itemBg = '#FFF1F2';
                                                                                    $itemBorder = '1px solid #FECDD3';
                                                                                    $checkIcon = 'fa-times-circle-o';
                                                                                    $checkColor = '#EF4444';
                                                                                    $statusBadge = '<span class="badge" style="background:#FEE2E2; color:#991B1B; ' . $badgeStyle . '"><i class="fa fa-times-circle"></i> Ditolak Dosen</span>';
                                                                                    $verifyBtn = '<button class="btn btn-sm btn-primary" onclick="verifyTodoDosen(\'' . $t->id_todo . '\', 1, this)" style="' . $btnStyle . ' box-shadow: 0 2px 4px rgba(59,130,246,0.2);"><i class="fa fa-redo"></i> Setujui Ulang</button>';
                                                                                }
                                                                            } else {
                                                                                $statusBadge = '<span class="badge" style="background:#F3F4F6; color:#4B5563; ' . $badgeStyle . '">Belum Selesai</span>';
                                                                            }
                                                                            ?>
                                                                            <div
                                                                                style="display: flex; align-items: flex-start; gap: 12px; background: <?= $itemBg ?>; border: <?= $itemBorder ?>; padding: 12px; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition: all 0.2s;">
                                                                                <div
                                                                                    style="color: <?= $checkColor ?>; font-size: 18px; margin-top: 2px; flex-shrink: 0;">
                                                                                    <i class="fa <?= $checkIcon ?>"></i>
                                                                                </div>
                                                                                <div style="flex: 1;">
                                                                                    <div
                                                                                        style="font-size: 13px; color: <?= $textColor ?>; text-decoration: <?= $textDecoration ?>;">
                                                                                        <?= htmlspecialchars($t->task) ?>
                                                                                    </div>
                                                                                    <div class="status-badge-container"
                                                                                        style="margin-top: 6px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                                        <?= $statusBadge ?>
                                                                                    </div>
                                                                                </div>
                                                                                <div style="flex-shrink: 0;">
                                                                                    <?= $verifyBtn ?>
                                                                                </div>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>

                                                                    <div
                                                                        style="margin-top: 5px; padding: 12px 15px; background-color: #EFF6FF; border-left: 4px solid #3B82F6; border-radius: 4px; font-size: 12px; color: #1E3A8A; line-height: 1.5;">
                                                                        <i class="fa fa-info-circle" style="margin-right: 5px;"></i>
                                                                        <strong>Keterangan:</strong> Setelah semua to-do list dikerjakan
                                                                        mahasiswa dan mahasiswa melakukan bimbingan terjadwal, maka
                                                                        mahasiswa mengirimkan kegiatan bimbingan untuk disetujui oleh
                                                                        dosen.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php
                                                if (!empty($log->catatan_mahasiswa) && $log->initiator_type != 'dosen'):
                                                    $display_name_mhs = $nm_pd;
                                                    $words = explode(' ', $display_name_mhs);
                                                    $initials_mhs = (count($words) >= 2) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($words[0], 0, 2));
                                                    ?>
                                                    <div class="thread-card" <?= $indent_base > 0 ? 'style="margin-left: ' . $indent_base . 'px; position: relative;"' : '' ?>>
                                                        <?php if ($indent_base > 0): ?>
                                                            <div
                                                                style="position: absolute; left: -26px; top: -15px; border-left: 2px dashed #C7D2FE; border-bottom: 2px dashed #C7D2FE; width: 20px; height: 35px; border-bottom-left-radius: 8px;">
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="thread-header">
                                                            <div class="avatar"><?= $initials_mhs ?></div>
                                                            <div class="thread-meta">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                    <strong
                                                                        style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $display_name_mhs ?></strong>
                                                                    <span class="badge"
                                                                        style="background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Mahasiswa</span>
                                                                </div>
                                                            </div>
                                                            <?php if ($is_latest && empty($log->catatan_dosen) && empty($log->status_pendamping)): ?>
                                                                <div class="thread-badge update"
                                                                    style="background:#EEF2FF; border-color:#C7D2FE; color:var(--primary); font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    Menunggu Review</div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div
                                                            style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                                            <?php
                                                            $tgl = (!empty($log->tanggal_bimbingan) && $log->tanggal_bimbingan != '0000-00-00') ? tgl_indo_short($log->tanggal_bimbingan) : ((!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? tgl_indo_short(date('Y-m-d', strtotime($log->created_at))) : 'Tanggal Tidak Tersedia');
                                                            $jam = (!empty($log->created_at) && $log->created_at != '0000-00-00 00:00:00') ? substr($log->created_at, 11, 5) : '-';
                                                            ?>
                                                            <span style="font-weight:600;"><i class="fa fa-clock-o"></i> <?= $tgl ?>
                                                                &bull; <?= $jam ?></span>
                                                        </div>
                                                        <div class="thread-body" style="padding-top: 5px;">
                                                            <div class="modern-log-container">
                                                                <div class="progress-row" style="margin-bottom: 22px;">
                                                                    <span
                                                                        style="font-weight: 700; color: var(--text-muted); font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Progress
                                                                        Bimbingan:</span>
                                                                    <span class="badge"
                                                                        style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; font-weight: 700; font-size: 13.5px; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; line-height: 1.4; vertical-align: middle; white-space: normal; height: auto;">
                                                                        <?= $log->tahapan ?> (<?= $log->progress_persen ?>%)
                                                                    </span>
                                                                </div>
                                                                <?php
                                                                $parsed = parse_bimbingan_catatan($log->catatan_mahasiswa);
                                                                if ($parsed):
                                                                    ?>
                                                                    <div
                                                                        style="margin-top: 15px; display: flex; flex-direction: column; gap: 12px;">
                                                                        <div
                                                                            style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                                            <h5
                                                                                style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">
                                                                                Aktivitas Mingguan</h5>
                                                                            <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['aktivitas']) ? htmlspecialchars($parsed['aktivitas']) : '<em style="color:#9CA3AF;">Tidak ada catatan aktivitas</em>' ?></div>
                                                                        </div>

                                                                        <div
                                                                            style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                                            <h5
                                                                                style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">
                                                                                Kendala &amp; Hambatan</h5>
                                                                            <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['kendala']) ? htmlspecialchars($parsed['kendala']) : '<em style="color:#9CA3AF;">Tidak ada catatan kendala yang dilaporkan</em>' ?></div>
                                                                        </div>

                                                                        <div
                                                                            style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                                            <h5
                                                                                style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">
                                                                                Target Selanjutnya</h5>
                                                                            <div style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500; white-space: pre-wrap;"><?= isset($parsed['target']) ? htmlspecialchars($parsed['target']) : '<em style="color:#9CA3AF;">Tidak ada target spesifik</em>' ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div
                                                                        style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-top: 15px;">
                                                                        <h5
                                                                            style="margin: 0 0 6px 0; font-weight: 700; font-size: 13.5px; color: #111827;">
                                                                            Catatan Bimbingan Mahasiswa</h5>
                                                                        <div
                                                                            style="font-size: 13px; color: #4B5563; line-height: 1.6; font-weight: 500;">
                                                                            <?= nl2br($log->catatan_mahasiswa) ?></div>
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
                                                            <?php if ($log->file_lampiran_mahasiswa): ?>
                                                                <?php if ($hide_docs): ?>
                                                                    <div
                                                                        style="margin-top: 15px; padding: 10px 15px; background: #FEF2F2; color: #991B1B; border: 1px dashed #FCA5A5; border-radius: 8px; font-size: 12.5px; display: inline-block;">
                                                                        <i class="fa fa-lock" style="margin-right: 5px;"></i> <?= $hide_msg ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">
                                                                        <a href="javascript:;"
                                                                            onclick="bukaGdrive('<?= $log->file_lampiran_mahasiswa ?>', '<?= $log->id_log ?>')"
                                                                            class="file-attachment" style="margin-top: 0;">
                                                                            <i class="fa fa-file-text-o"></i> Lihat Dokumen Mahasiswa (Google
                                                                            Drive)
                                                                        </a>
                                                                        <?php if (isset($is_dosen) || true): // Any dosen could trigger it theoretically ?>
                                                                            <?php if (isset($log->has_chunks) && $log->has_chunks): ?>
                                                                                <!-- Tombol Baru: AI Review Per Bab -->
                                                                                <button type="button" onclick="openReviewPerBabModal(<?= $data_skripsi->id_judul_skripsi ?>)" 
                                                                                    class="file-attachment"
                                                                                    style="margin-top: 0; background: linear-gradient(135deg, #0EA5E9 0%, #3B82F6 100%); color: white; border: none; cursor: pointer; border-color: #2563EB;">
                                                                                    🤖 AI Review Per Bab (Analisis Naskah)
                                                                                </button>
                                                                                <!-- Tombol Baru: Side by Side -->
                                                                                <a href="<?= site_url('E_skripsi_bimbingan/sbs_review/'.$log->id_log) ?>" target="_blank" id="btnSbs_<?= $log->id_log ?>"
                                                                                    class="file-attachment"
                                                                                    style="margin-top: 0; background: #10B981; color: white; border: none; cursor: pointer; border-color: #059669; <?= (isset($log->is_all_reviewed) && $log->is_all_reviewed) ? '' : 'display: none;' ?>">
                                                                                    <i class="fa fa-columns"></i> Buka Tampilan Bersebelahan
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <?php if ($has_api_key): ?>
                                                                                    <button id="btnAiReview_<?= $log->id_log ?>"
                                                                                        onclick="generateAiReview('<?= $log->id_log ?>', '<?= $data_skripsi->id_judul_skripsi ?>')"
                                                                                        class="file-attachment"
                                                                                        style="margin-top: 0; background: linear-gradient(135deg, #7C3AED, #4F46E5); color: white; border: none; cursor: pointer; border-color: #6D28D9; <?= !empty($ai_review_data) ? 'display: none;' : '' ?>">
                                                                                        <i class="fa fa-magic"></i> Minta AI Review
                                                                                    </button>
                                                                                <?php else: ?>
                                                                                    <a href="<?= site_url('Setting_api_key') ?>" class="file-attachment"
                                                                                        style="margin-top: 0; background: linear-gradient(135deg, #f97316, #ea580c); color: white; border: none; cursor: pointer; border-color: #c2410c; text-decoration: none; <?= !empty($ai_review_data) ? 'display: none;' : '' ?>"
                                                                                        id="btnAiReview_<?= $log->id_log ?>">
                                                                                        <i class="fa fa-key"></i> Setting API Key
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                            <?php endif; ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                            <?php if (!$hide_docs): ?>
                                                                <?php if (!empty($ai_review_data)): ?>
                                                                    <div id="aiReviewContainer_<?= $log->id_log ?>"
                                                                        style="background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                                                        <div
                                                                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                                                            <div style="display: flex; align-items: center; gap: 10px; color: #4F46E5; cursor: pointer; flex: 1;"
                                                                                onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).closest('div').find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');">
                                                                                <i class="fa fa-magic" style="font-size: 18px;"></i>
                                                                                <h4 style="margin: 0; font-weight: 800; font-size: 14px;">
                                                                                    Catatan Review AI</h4>
                                                                            </div>
                                                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                                                <i class="fa fa-chevron-down toggle-icon"
                                                                                    style="color: #6B7280; cursor: pointer;"
                                                                                    onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).toggleClass('fa-chevron-down fa-chevron-up');"></i>
                                                                                <button type="button" id="btnResetAiReview_<?= $log->id_log ?>"
                                                                                    onclick="resetAiReview('<?= $log->id_log ?>', '<?= $data_skripsi->id_judul_skripsi ?>')"
                                                                                    title="Hapus hasil AI Review ini agar dapat digenerate ulang"
                                                                                    style="background: none; border: 1px solid #FCA5A5; color: #DC2626; border-radius: 6px; padding: 3px 8px; font-size: 12px; cursor: pointer; line-height: 1; margin-left: 4px; transition: all 0.2s;"
                                                                                    onmouseover="this.style.background='#FEE2E2'"
                                                                                    onmouseout="this.style.background='none'">
                                                                                    <i class="fa fa-trash-o"></i> Reset
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div id="aiReviewContent_<?= $log->id_log ?>" class="ai-review-content"
                                                                            style="font-size: 13px; color: #374151; line-height: 1.7; font-weight: 500; display: none; margin-top: 15px;">
                                                                            <div class="ai-review-text">
                                                                                <?= nl2br(htmlspecialchars($ai_review_data)) ?></div>
                                                                            <textarea class="ai-raw-text"
                                                                                style="display:none;"><?= htmlspecialchars($ai_review_data) ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div id="aiReviewContainer_<?= $log->id_log ?>"
                                                                        style="display: none; background: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; cursor: pointer;"
                                                                            onclick="$('#aiReviewContent_<?= $log->id_log ?>').slideToggle(); $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');">
                                                                            <div
                                                                                style="display: flex; align-items: center; gap: 10px; color: #4F46E5;">
                                                                                <i class="fa fa-magic" style="font-size: 18px;"></i>
                                                                                <h4 style="margin: 0; font-weight: 800; font-size: 14px;">
                                                                                    Catatan Review AI</h4>
                                                                            </div>
                                                                            <i class="fa fa-chevron-up toggle-icon" style="color: #6B7280;"></i>
                                                                        </div>
                                                                        <div id="aiReviewContent_<?= $log->id_log ?>" class="ai-review-content"
                                                                            style="font-size: 13px; color: #374151; line-height: 1.7; font-weight: 500; margin-top: 15px;">
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
                                                    <div class="thread-card dosen-reply"
                                                        style="border-left-color: #059669; margin-left: <?= 100 + $indent_base ?>px;">
                                                        <div class="thread-header">
                                                            <div class="avatar dosen"
                                                                style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                                                                <?= $initials_p ?></div>
                                                            <div class="thread-meta">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                    <strong
                                                                        style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $nm_pend ?></strong>
                                                                    <span class="badge"
                                                                        style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Dosen
                                                                        Pendamping</span>
                                                                </div>
                                                            </div>
                                                            <?php if ($log->status_pendamping == 'Perlu Revisi'): ?>
                                                                <div class="thread-badge review"
                                                                    style="background:#FEE2E2; border-color:#FCA5A5; color:#991B1B; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    Perlu Revisi</div>
                                                            <?php else: ?>
                                                                <div class="thread-badge review"
                                                                    style="background:#D1FAE5; border-color:#6EE7B7; color:#065F46; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    <?= $log->status_pendamping ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($log->reviewed_at_pendamping) && $log->reviewed_at_pendamping != '0000-00-00 00:00:00'): ?>
                                                            <div
                                                                style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                                                <span style="font-weight:600;"><i class="fa fa-clock-o"></i>
                                                                    <?= tgl_indo_short(substr($log->reviewed_at_pendamping, 0, 10)) . ' &bull; ' . substr($log->reviewed_at_pendamping, 11, 5) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="thread-body" style="padding-top: 5px;">
                                                            <div
                                                                style="font-size: 14px; line-height: 1.65; color: #374151; font-weight: 500;">
                                                                <?= nl2br($log->catatan_pendamping) ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php
                                                if (!empty($log->catatan_dosen)):
                                                    $display_name_d = trim((string)$log->first_name);
                                                    if (!empty($log->last_name) && strpos(strtolower((string)$log->first_name), strtolower((string)$log->last_name)) === false) {
                                                        $display_name_d .= ' ' . trim((string)$log->last_name);
                                                    }
                                                    if (empty($display_name_d))
                                                        $display_name_d = $nm_doping_1;
                                                    $initials_d = get_avatar_initials($display_name_d, 'DU');
                                                    ?>
                                                    <div class="thread-card dosen-reply"
                                                        style="margin-left: <?= (!empty($log->catatan_pendamping) ? 150 : 100) + $indent_base ?>px;">
                                                        <div class="thread-header">
                                                            <div class="avatar dosen"><?= $initials_d ?></div>
                                                            <div class="thread-meta">
                                                                <div
                                                                    style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                    <strong
                                                                        style="font-weight: 700; font-size: 14.5px; color: var(--text-main); margin-bottom: 0;"><?= $display_name_d ?></strong>
                                                                    <span class="badge"
                                                                        style="background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0; line-height: 1.4; display: inline-block;">Dosen
                                                                        Pembimbing</span>
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
                                                            <?php if ($badge_text_utama == 'Perlu Revisi'): ?>
                                                                <div class="thread-badge review"
                                                                    style="background:#FEE2E2; border-color:#FCA5A5; color:#991B1B; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    Perlu Revisi</div>
                                                            <?php elseif ($badge_text_utama == 'Disetujui'): ?>
                                                                <div class="thread-badge review"
                                                                    style="background:#D1FAE5; border-color:#6EE7B7; color:#065F46; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    Disetujui</div>
                                                            <?php elseif ($badge_text_utama == 'Konsultasi ke Pendamping'): ?>
                                                                <div class="thread-badge update"
                                                                    style="background:#FEF3C7; border-color:#FDE68A; color:#92400E; font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    Delegasi ke Pendamping</div>
                                                            <?php else: ?>
                                                                <div class="thread-badge update"
                                                                    style="background:#EEF2FF; border-color:#C7D2FE; color:var(--primary); font-weight:700; font-size:11px; padding:4px 10px; border-radius:6px; margin: 0; line-height: 1.4; display: inline-block;">
                                                                    <?= $badge_text_utama ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div
                                                            style="font-size: 11.5px; color: var(--text-muted); margin: 12px 16px 0 16px;">
                                                            <span style="font-weight:600;"><i class="fa fa-clock-o"></i>
                                                                <?= tgl_indo_short($log->tanggal_bimbingan) . ' &bull; ' . substr($log->created_at, 11, 5) ?></span>
                                                        </div>
                                                        <div class="thread-body" style="padding-top: 5px;">
                                                            <div
                                                                style="font-size: 14px; line-height: 1.65; color: #374151; font-weight: 500;">
                                                                <?= nl2br($log->catatan_dosen) ?></div>
                                                            <?php if ($log->file_lampiran_dosen): ?>
                                                                <br>
                                                                <a href="<?= base_url('assets/uploads/bimbingan/' . $log->file_lampiran_dosen) ?>"
                                                                    target="_blank" class="file-attachment" style="margin-top: 15px;">
                                                                    <i class="fa fa-download"></i> Unduh Lampiran Dosen
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php
                                                $prev_initiator = $log->initiator_type;
                                            endforeach; ?>

                                            <?php if ($sec['title'] == 'archive'): ?>
                                            </div> <!-- Close archivedLogs -->
                                        </div> <!-- Close archive container -->
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            <?php endif; ?>



                            <?php
                            // Logika $pending_bimbingan_log sudah dipindah ke atas
                            ?>

                            <?php if (!empty($pending_bimbingan_log)): ?>

                                <div class="reply-box">
                                    <h3 style="font-size:14px; margin:0 0 4px; font-weight:700;">Review Bimbingan Mahasiswa
                                    </h3>
                                    <p style="font-size:12px; color:#6B7280; margin:0 0 14px;">Berikan penilaian dan catatan
                                        untuk bimbingan yang diajukan mahasiswa.</p>
                                    <form id="form_balas_pesan">
                                        <input type="hidden" name="id_judul_skripsi"
                                            value="<?= $data_skripsi->id_judul_skripsi ?>">
                                        <input type="hidden" name="id_log_bimbingan"
                                            value="<?= $pending_bimbingan_log->id_log ?>">
                                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                                            value="<?= $this->security->get_csrf_hash(); ?>">
                                        <div class="row" style="margin-bottom:12px;">
                                            <div class="col-md-6">
                                                <label style="font-size:12px; color:#6B7280;">Status Bimbingan</label>
                                                <select name="status_bimbingan" id="select_status_bimbingan"
                                                    class="form-control" style="border-radius:8px;">
                                                    <?php if ($is_p && !$is_u): ?>
                                                        <option value="Lanjut ke Utama">Lanjut ke Utama</option>
                                                        <option value="Perlu Revisi">Perlu Revisi</option>
                                                    <?php else: ?>
                                                        <option value="Disetujui">Disetujui</option>
                                                        <option value="Perlu Revisi">Perlu Revisi</option>
                                                        <option value="Menunggu Review">Menunggu Review / Pending State</option>
                                                        <?php if (!empty($data_skripsi->dosen_pembimbing_2) && $data_skripsi->dosen_pembimbing_2 != '-' && empty($pending_bimbingan_log->catatan_pendamping)): ?>
                                                            <option value="Konsultasi ke Pendamping">Konsultasikan ke Dosen
                                                                Pendamping</option>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <?php
                                                    $is_agenda = ($pending_bimbingan_log->initiator_type == 'dosen' && !empty($pending_bimbingan_log->agenda_tgl_mulai)) ? 'true' : 'false';
                                                    $agenda_date = !empty($pending_bimbingan_log->agenda_tgl_mulai) ? $pending_bimbingan_log->agenda_tgl_mulai : "";
                                                    $agenda_date_formatted = !empty($agenda_date) ? date('d M Y', strtotime($agenda_date)) : "";
                                                    $today = date('Y-m-d');
                                                ?>
                                                <div id="agenda_early_warning" style="display:none; font-size:11px; color:#D97706; background:#FEF3C7; padding:8px 10px; border-radius:6px; margin-top:8px; border:1px solid #FCD34D; line-height:1.4;">
                                                    <i class="fa fa-info-circle"></i> <strong>Perhatian:</strong> Anda menyetujui bimbingan ini lebih awal dari jadwal yang ditetapkan (<strong><?= $agenda_date_formatted ?></strong>).
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        $('#select_status_bimbingan').on('change', function() {
                                                            var val = $(this).val();
                                                            var isAgenda = <?= $is_agenda ?>;
                                                            var agendaDate = '<?= $agenda_date ?>';
                                                            var today = '<?= $today ?>';
                                                            
                                                            if (val === 'Disetujui' && isAgenda && today < agendaDate) {
                                                                $('#agenda_early_warning').slideDown(200);
                                                            } else {
                                                                $('#agenda_early_warning').slideUp(200);
                                                            }
                                                        });
                                                        // Trigger check on load
                                                        $('#select_status_bimbingan').trigger('change');
                                                    });
                                                </script>
                                            </div>
                                            <?php if (!$is_p || $is_u): ?>
                                                <div class="col-md-6" id="progress_container">
                                                    <label style="font-size:12px; color:#6B7280;">Set Progress (%)</label>
                                                    <input type="number" name="progress_persen" class="form-control"
                                                        style="border-radius:8px;"
                                                        value="<?= $data_skripsi->current_progress ?>">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <textarea name="catatan_dosen_wali" class="form-control"
                                            style="resize:vertical; min-height:90px; border-radius:8px; margin-bottom:14px;"
                                            placeholder="Ketik catatan bimbingan atau feedback untuk mahasiswa..."></textarea>
                                        <?php
                                        $pending_ai_data = null;
                                        if (isset($is_utama) && $is_utama)
                                            $pending_ai_data = $pending_bimbingan_log->ai_review_utama;
                                        elseif (isset($is_pendamping) && $is_pendamping)
                                            $pending_ai_data = $pending_bimbingan_log->ai_review_pendamping;
                                        ?>
                                        <div style="text-align:right;">
                                            <button type="button" id="btnSalinAI"
                                                onclick="salinKesimpulanAi('<?= $pending_bimbingan_log->id_log ?>')"
                                                class="btn btn-outline-primary"
                                                style="<?= empty($pending_ai_data) ? 'display:none;' : '' ?> border-radius:8px; font-weight:600; padding:8px 22px; margin-right: 8px;">
                                                <i class="fa fa-copy" style="margin-right:6px;"></i> Salin Kesimpulan AI
                                            </button>
                                            <button type="submit" id="btnSave" class="btn btn-primary"
                                                style="background:var(--primary); border-radius:8px; font-weight:600; border:none; padding:8px 22px;">
                                                <i class="fa fa-check-circle" style="margin-right:6px;"></i> Kirim Review
                                                Bimbingan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            <?php else: ?>

                                <div class="reply-box"
                                    style="border-top-color: #9CA3AF; text-align:center; padding: 30px 20px;">
                                    <div style="font-size:36px; margin-bottom:10px;">☕</div>
                                    <h3 style="font-size:16px; margin:0 0 8px; font-weight:700; color:#374151;">Tidak Ada
                                        Bimbingan Pending</h3>
                                    <p style="font-size:13px; color:#6B7280; margin:0;">Mahasiswa belum mengajukan bimbingan
                                        baru atau seluruh bimbingan telah direview.</p>
                                </div>
                            <?php endif; ?>

                        </div>


                        <div class="sidebar-panel">
                            <div class="sidebar-body">
                                <!-- Student Profile side-by-side -->
                                <div class="sidebar-profile-header"
                                    style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px; text-align: left;">
                                    <?php
                                    $photo_detail_src = !empty($foto_mhs)
                                        ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . rawurlencode($foto_mhs)
                                        : base_url('assets/no_profile_photo.png');
                                    $photo_thumb_src = !empty($foto_mhs)
                                        ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($foto_mhs)
                                        : base_url('assets/no_profile_photo.png');
                                    ?>
                                    <div onclick="$('#modalFotoMahasiswa').modal('show')"
                                        style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 2px solid #E5E7EB; flex-shrink: 0; background: #F3F4F6; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: pointer; transition: transform 0.2s, border-color 0.2s;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.style.borderColor='#4F46E5'"
                                        onmouseout="this.style.transform='scale(1)'; this.style.borderColor='#E5E7EB'"
                                        title="Klik untuk melihat detail foto">
                                        <img src="<?= $photo_thumb_src ?>" alt="<?= $nm_pd ?>"
                                            onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="display: flex; flex-direction: column; min-width: 0;">
                                        <div
                                            style="color: #6B7280; font-weight: 700; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 1px;">
                                            <?= $data_skripsi->nipd ?>
                                        </div>
                                        <h3
                                            style="color: #111827; font-size: 14px; font-weight: 800; line-height: 1.3; margin: 0 0 4px 0; text-transform: uppercase; word-break: break-word;">
                                            <?= $nm_pd ?>
                                        </h3>
                                        <a href="javascript:void(0)" onclick="$('#modalFotoMahasiswa').modal('show')"
                                            style="font-size: 11px; color: #4F46E5; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa fa-search-plus"></i> Lihat Foto Detail
                                        </a>
                                    </div>
                                </div>

                                <!-- Judul TA -->
                                <div class="sidebar-judul"
                                    style="color: #374151; font-size: 13px; line-height: 1.5; font-weight: 600; margin-bottom: 20px; padding: 12px 14px; background: #F9FAFB; border-radius: 10px; border: 1px solid #E5E7EB; text-align: left; word-break: break-word;">
                                    "<?= $data_skripsi->judul_ditetapkan ?>"
                                </div>

                                <div class="sidebar-footer">
                                    <div class="sidebar-meta-item">
                                        <div class="sidebar-meta-icon icon-dosen">👨‍🏫</div>
                                        <div class="sidebar-meta-text">
                                            <div class="sidebar-meta-label">Dosen Pembimbing</div>
                                            <div class="sidebar-meta-value"><?= $nm_doping_1 ?></div>
                                            <?php if (!empty($nm_doping_2) && $nm_doping_2 != '-'): ?>
                                                <div class="sidebar-meta-label" style="margin-top: 8px;">Dosen Pendamping
                                                </div>
                                                <div class="sidebar-meta-value"><?= $nm_doping_2 ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php
                                    $kategori_color = '#10B981';
                                    $bg_color = '#D1FAE5';
                                    $icon = '🟢';
                                    if ($data_skripsi->kategori_health == 'Perlu Perhatian') {
                                        $kategori_color = '#D97706';
                                        $bg_color = '#FEF3C7';
                                        $icon = '🟡';
                                    }
                                    if ($data_skripsi->kategori_health == 'Risiko Tinggi') {
                                        $kategori_color = '#DC2626';
                                        $bg_color = '#FEE2E2';
                                        $icon = '🔴';
                                    }
                                    if ($data_skripsi->kategori_health == 'Belum Ada Bimbingan') {
                                        $kategori_color = '#6B7280';
                                        $bg_color = '#F3F4F6';
                                        $icon = '⚪';
                                    }
                                    ?>
                                    <div class="sidebar-meta-item">
                                        <div class="sidebar-meta-icon"
                                            style="background:<?= $bg_color ?>; color:<?= $kategori_color ?>;">
                                            <?= $icon ?></div>
                                        <div class="sidebar-meta-text">
                                            <div class="sidebar-meta-label">Academic Health</div>
                                            <div class="sidebar-meta-value" style="color:<?= $kategori_color ?>;">
                                                <?= $data_skripsi->kategori_health ?> (Score:
                                                <?= round($data_skripsi->academic_health_score) ?>)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sidebar-meta-item">
                                        <div class="sidebar-meta-icon icon-progress">📈</div>
                                        <div class="sidebar-meta-text" style="width:100%;">
                                            <div class="sidebar-meta-label"
                                                style="display:flex; justify-content:space-between;">
                                                <span>Progress Bimbingan</span>
                                                <span
                                                    style="color:var(--primary); font-weight:800;"><?= $data_skripsi->current_progress ?>%</span>
                                            </div>
                                            <div class="progress-bar-container" style="margin-top:4px;">
                                                <div class="progress-bar-fill"
                                                    style="width: <?= $data_skripsi->current_progress ?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>


            <div id="skripsiChatWidget"
                style="position: fixed; bottom: 30px; right: 30px; width: 350px; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: none; flex-direction: column; z-index: 9999; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s ease; height: 500px; transform: translateY(120%); opacity: 0; border: 1px solid #E5E7EB;">

                <div class="chat-header"
                    style="background: linear-gradient(135deg, #4F46E5, #4338CA); color: white; padding: 16px; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div
                            style="width: 36px; height: 36px; border-radius: 50%; background: #fff; padding: 2px; overflow: hidden;">
                            <img src="<?= isset($photo_thumb_src) ? $photo_thumb_src : base_url('assets/no_profile_photo.png') ?>"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 14px; font-weight: 700; color: #fff;">Chat Skripsi</h4>
                            <div
                                style="font-size: 11px; color: #E0E7FF; opacity: 0.9; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                <?= $nm_pd ?></div>
                        </div>
                    </div>
                    <button id="btnToggleChat"
                        style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; outline: none; transition: transform 0.2s;">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                </div>


                <div id="chatBodyScroll"
                    style="flex: 1; overflow-y: auto; padding: 16px; background: #F9FAFB; display: flex; flex-direction: column; gap: 12px;">
                    <div style="text-align: center; color: #9CA3AF; font-size: 11px; margin-bottom: 10px;">Gunakan chat
                        ini untuk komunikasi santai di luar log bimbingan resmi.</div>
                    <div id="chatMessagesContainer" style="display: flex; flex-direction: column; gap: 12px;">

                        <div style="text-align: center; padding: 20px;">
                            <i class="fa fa-circle-o-notch fa-spin fa-2x" style="color: #CBD5E1;"></i>
                        </div>
                    </div>
                </div>


                <div class="chat-footer"
                    style="padding: 12px 16px; background: #fff; border-top: 1px solid #E5E7EB; display: flex; gap: 8px; align-items: flex-end;">
                    <textarea id="chatInputMsg" rows="1" placeholder="Tulis pesan..."
                        style="flex: 1; border: 1px solid #D1D5DB; border-radius: 20px; padding: 10px 14px; font-size: 13px; outline: none; resize: none; max-height: 100px; overflow-y: auto; line-height: 1.4; transition: border-color 0.2s; font-family: inherit;"></textarea>
                    <button id="btnSendChat"
                        style="width: 40px; height: 40px; border-radius: 50%; background: #4F46E5; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; flex-shrink: 0; transition: background 0.2s; outline: none;">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>


            <button id="chatBubbleFab"
                style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #4338CA); color: white; border: none; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); display: none; align-items: center; justify-content: center; font-size: 24px; cursor: pointer; z-index: 9999; outline: none; transition: transform 0.2s;">
                <i class="fa fa-comments"></i>

                <span id="chatUnreadBadge"
                    style="position: absolute; top: -2px; right: -2px; background: #EF4444; color: white; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 10px; border: 2px solid #fff; display: none;">0</span>
            </button>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="modalGdrive" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg"
        style="width: 95%; max-width: 1400px; margin: 3vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content"
            style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; height: 92vh; display: flex; flex-direction: column;">
            <div class="modal-header"
                style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                <h4 class="modal-title"
                    style="font-weight: 800; font-size: 16px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span>📄</span> Pratinjau Dokumen Mahasiswa
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none; transition: all 0.2s;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"
                style="padding: 0; background: #F3F4F6; flex: 1; overflow: hidden; position: relative; display: flex;">
                <div id="modalGdriveIframeContainer" style="flex: 1; height: 100%; transition: all 0.3s;">
                    <iframe id="iframeGdrive" src=""
                        style="width: 100%; height: 100%; border: none; display: block; background: #FFFFFF;"></iframe>
                </div>
                <div id="modalGdriveAiReviewContainer"
                    style="width: 400px; height: 100%; background: #FFFFFF; border-left: 1px solid #E5E7EB; display: none; flex-direction: column; flex-shrink: 0; position: relative;">
                    <div
                        style="padding: 16px; background: #F8FAFC; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-magic" style="color: #4F46E5; font-size: 16px;"></i>
                        <h5 style="margin: 0; font-weight: 700; color: #1E293B; font-size: 14px;">Catatan Review AI</h5>
                    </div>
                    <div id="modalGdriveAiReviewContent"
                        style="padding: 20px; overflow-y: auto; flex: 1; font-size: 13px; color: #334155; line-height: 1.7;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- Modal AI Review Per Bab (dari Chunking Naskah)            -->
<!-- ========================================================== -->
<div class="modal fade" id="modalReviewPerBab" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="width: 96%; max-width: 1350px; margin: 2vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; height: 94vh; display: flex; flex-direction: column;">
            <!-- Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); border-bottom: none; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                <h4 class="modal-title" style="font-weight: 800; font-size: 16px; color: #fff; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="background: rgba(255,255,255,0.2); padding: 6px 10px; border-radius: 8px;">🤖</span>
                    <div>
                        <div>Review Per Bab — Analisis Naskah Berkesinambungan</div>
                        <div id="rpb_subtitle" style="font-size: 11px; font-weight: 500; opacity: 0.85; margin-top: 2px;">Memuat data...</div>
                    </div>
                </h4>
                <button type="button" id="rpb_close_btn" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Body -->
            <div class="modal-body" style="padding: 0; background: #F3F4F6; flex: 1; overflow: hidden; display: flex; gap: 0;">

                <!-- Panel Kiri: Daftar Bab -->
                <div id="rpb_sidebar" style="width: 280px; flex-shrink: 0; background: #1E1E2E; display: flex; flex-direction: column; overflow: hidden;">
                    <div style="padding: 16px 14px 10px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <div style="font-size: 11px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;">Daftar Bab Naskah</div>
                        <div id="rpb_info_log" style="font-size: 10px; color: #6B7280; line-height: 1.4;"></div>
                    </div>
                    <div id="rpb_bab_list" style="flex: 1; overflow-y: auto; padding: 10px 8px;">
                        <div style="text-align: center; padding: 40px 0; color: #6B7280; font-size: 13px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            Memuat daftar bab...
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div style="padding: 12px; border-top: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; gap: 8px;">
                        <button id="rpb_btn_review_all" onclick="rpbStartReviewAll()" class="btn btn-block" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-weight: 700; font-size: 12px; border: none; border-radius: 8px; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer;">
                            <i class="fa fa-magic"></i> Review Semua Bab (Sequential)
                        </button>
                        <button id="rpb_btn_reset_all" onclick="rpbResetAllReviews()" class="btn btn-block" style="background: rgba(239,68,68,0.15); color: #EF4444; font-weight: 600; font-size: 11px; border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 8px; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer;">
                            <i class="fa fa-refresh"></i> Reset Semua Review
                        </button>
                    </div>
                </div>

                <!-- Panel Kanan: Konten Review -->
                <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #fff;">
                    <!-- Status Bar -->
                    <div id="rpb_status_bar" style="background: #F8FAFC; border-bottom: 1px solid #E5E7EB; padding: 10px 20px; display: flex; align-items: center; gap: 12px; flex-shrink: 0; min-height: 48px;">
                        <div id="rpb_progress_info" style="font-size: 13px; color: #6B7280; font-weight: 500; flex: 1;">Pilih bab dari sidebar untuk melihat atau generate review.</div>
                        <div id="rpb_chain_badge" style="display: none; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE;">
                            🔗 Review Berkesinambungan Aktif
                        </div>
                    </div>
                    <!-- Progress Indicator -->
                    <div id="rpb_overall_progress" style="height: 4px; background: #E5E7EB; flex-shrink: 0;">
                        <div id="rpb_progress_bar" style="height: 100%; background: linear-gradient(90deg, #4F46E5, #7C3AED); width: 0%; transition: width 0.5s ease;"></div>
                    </div>
                    <!-- Review Content Area -->
                    <div id="rpb_review_area" style="flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column;">
                        <div id="rpb_initial_state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; color: #9CA3AF; padding: 40px;">
                            <div style="font-size: 60px; margin-bottom: 20px;">📚</div>
                            <div style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 8px;">Review Naskah Per Bab</div>
                            <div style="font-size: 13px; line-height: 1.7; max-width: 400px;">
                                Fitur ini mereview setiap bab naskah tugas akhir secara <strong>berkesinambungan</strong> — hasil review bab sebelumnya menjadi konteks untuk bab berikutnya.
                                <br><br>
                                Klik tombol <strong>"Review Semua Bab"</strong> untuk memulai proses secara otomatis, atau pilih bab tertentu dari sidebar.
                            </div>
                        </div>
                        <div id="rpb_loading_state" style="display: none; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; padding: 40px;">
                            <div class="rpb-spinner" style="width: 50px; height: 50px; border: 4px solid #E5E7EB; border-top: 4px solid #4F46E5; border-radius: 50%; animation: rpb-spin 1s linear infinite; margin: 0 auto 20px;"></div>
                            <div id="rpb_loading_title" style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 8px;">AI Sedang Membaca & Menganalisis...</div>
                            <div id="rpb_loading_desc" style="font-size: 13px; color: #6B7280; line-height: 1.7; max-width: 450px;"></div>
                            <div style="font-size: 12px; color: #EF4444; font-weight: 700; margin-top: 15px;"><i class="fa fa-warning"></i> Jangan menutup form ini</div>
                        </div>
                        <div id="rpb_result_content" style="display: none;"></div>
                        <textarea id="rpb_edit_textarea" style="display:none; flex: 1; width:100%; min-height:400px; padding:15px; border-radius:8px; border:1px solid #D1D5DB; font-family:monospace; font-size:13px; line-height:1.6; resize:none; box-sizing: border-box;" placeholder="Teks markdown review AI..."></textarea>
                    </div>
                    <!-- Footer Actions (tampil hanya saat ada review) -->
                    <div id="rpb_footer_actions" style="display: none; border-top: 1px solid #E5E7EB; padding: 12px 20px; background: #F8FAFC; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-shrink: 0;">
                        <div id="rpb_current_bab_info" style="font-size: 12px; color: #6B7280; font-weight: 500;"></div>
                        <div style="display: flex; gap: 8px;">
                            <button id="rpb_btn_regen" onclick="rpbRegenerateCurrentBab()" class="btn btn-sm btn-default" style="font-weight: 600; font-size: 12px; border-radius: 6px; border-color: #D1D5DB; color: #4B5563; display: flex; align-items: center; gap: 5px;">
                                <i class="fa fa-refresh"></i> Generate Ulang
                            </button>
                            <button id="rpb_btn_edit" onclick="rpbEditReview()" class="btn btn-sm btn-default" style="font-weight: 600; font-size: 12px; border-radius: 6px; border-color: #D1D5DB; color: #4B5563; display: flex; align-items: center; gap: 5px;">
                                <i class="fa fa-edit"></i> Edit Review
                            </button>
                            <button id="rpb_btn_save_edit" onclick="rpbSaveEditReview()" class="btn btn-sm btn-success" style="display:none; font-weight: 700; font-size: 12px; border-radius: 6px; background: #10B981; border: none; align-items: center; gap: 5px;">
                                <i class="fa fa-save"></i> Simpan Edit
                            </button>
                            <button id="rpb_btn_cancel_edit" onclick="rpbCancelEditReview()" class="btn btn-sm btn-danger" style="display:none; font-weight: 600; font-size: 12px; border-radius: 6px; background: #EF4444; border: none; align-items: center; gap: 5px;">
                                Batal
                            </button>
                            <div style="display: flex; align-items: center; gap: 6px; background: #EEF2FF; padding: 6px 12px; border-radius: 6px; border: 1px solid #C7D2FE;">
                                <input type="checkbox" id="rpb_toggle_share" onchange="rpbToggleShareReview(this.checked)" style="cursor: pointer; width: 16px; height: 16px; accent-color: #4F46E5; margin:0;">
                                <label for="rpb_toggle_share" style="cursor: pointer; font-weight: 700; font-size: 12px; color: #4338CA; margin: 0; user-select: none;">
                                    Bagikan ke Mahasiswa
                                </label>
                            </div>
                            <button id="rpb_btn_close_footer" type="button" data-dismiss="modal" class="btn btn-sm btn-default" style="font-weight: 600; font-size: 12px; border-radius: 6px; border-color: #D1D5DB; color: #4B5563; display: flex; align-items: center; gap: 5px;">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
    border: 1px solid transparent;
    color: #CBD5E1;
    font-size: 12.5px;
    font-weight: 600;
}
.rpb-bab-item:hover {
    background: rgba(255,255,255,0.07);
    border-color: rgba(255,255,255,0.1);
}
.rpb-bab-item.active {
    background: rgba(79,70,229,0.3);
    border-color: rgba(79,70,229,0.6);
    color: #A5B4FC;
}
.rpb-bab-item .rpb-bab-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
    background: rgba(255,255,255,0.08);
    color: #94A3B8;
}
.rpb-bab-item.reviewed .rpb-bab-icon {
    background: rgba(16,185,129,0.2);
    color: #6EE7B7;
}
.rpb-bab-item.reviewing .rpb-bab-icon {
    background: rgba(79,70,229,0.3);
    color: #A5B4FC;
}
.rpb-bab-item .rpb-bab-status {
    margin-left: auto;
    font-size: 10px;
}
</style>

<!-- Modal Buat Agenda Bimbingan -->
<div class="modal fade" id="modalBuatAgenda" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg"
        style="width: 90%; max-width: 800px; margin: 5vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content"
            style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div class="modal-header"
                style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title"
                    style="font-weight: 800; font-size: 16px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-calendar-plus-o text-primary"></i> Buat Agenda Bimbingan
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none; transition: all 0.2s;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #F9FAFB;">
                <form id="formBuatAgenda">
                    <input type="hidden" name="id_judul_skripsi" id="agenda_id_judul"
                        value="<?= $data_skripsi->id_judul_skripsi ?>">
                    <input type="hidden" name="ai_review_agenda" id="agenda_ai_review">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="font-weight:700; font-size:13px; color:#374151;">Tanggal Agenda <span
                                        class="text-danger">*</span></label>
                                <div class="input-group" style="border-radius:8px; overflow:hidden;">
                                    <span class="input-group-addon"
                                        style="background:#EEF2FF; border-color:#D1D5DB; border-right:none;"><i
                                            class="fa fa-calendar" style="color:#6366F1;"></i></span>
                                    <input type="date" class="form-control" id="agenda_tanggal" name="tgl_mulai"
                                        required
                                        style="border-color:#D1D5DB; padding:10px; height:auto; cursor:pointer; background:white;">
                                    <input type="hidden" name="tgl_selesai" id="hidden_tgl_selesai">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight:700; font-size:13px; color:#374151;">Jam Pelaksanaan</label>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="flex:1; display:flex; align-items:center; gap:0; border:1px solid #D1D5DB; border-radius:8px; overflow:hidden;">
                                        <span
                                            style="background:#EEF2FF; border-right:1px solid #D1D5DB; padding:10px 12px; display:flex; align-items:center;"><i
                                                class="fa fa-clock-o" style="color:#6366F1;"></i></span>
                                        <input type="text" class="form-control timepicker" id="jam_mulai_display" name="jam_mulai"
                                            value="08:00"
                                            style="border:none; border-radius:0; padding:10px; height:auto; font-size:14px; font-weight:600; color:#374151; cursor:pointer;" autocomplete="off" readonly>
                                    </div>
                                    <span style="font-weight:700; color:#6B7280; flex-shrink:0;">s/d</span>
                                    <div
                                        style="flex:1; display:flex; align-items:center; gap:0; border:1px solid #D1D5DB; border-radius:8px; overflow:hidden;">
                                        <span
                                            style="background:#EEF2FF; border-right:1px solid #D1D5DB; padding:10px 12px; display:flex; align-items:center;"><i
                                                class="fa fa-clock-o" style="color:#6366F1;"></i></span>
                                        <input type="text" class="form-control timepicker" id="jam_selesai_display"
                                            name="jam_selesai" value="10:00"
                                            style="border:none; border-radius:0; padding:10px; height:auto; font-size:14px; font-weight:600; color:#374151; cursor:pointer;" autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="font-weight:700; font-size:13px; color:#374151;">Prioritas</label>
                                <select class="form-control" name="prioritas"
                                    style="border-radius:8px; border:1px solid #D1D5DB; padding:10px; height:auto;">
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Sedang" selected>Sedang</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="font-weight:700; font-size:13px; color:#374151;">Fase Seminar</label>
                                <select class="form-control" name="fase_seminar" id="agenda_fase_seminar"
                                    style="border-radius:8px; border:1px solid #D1D5DB; padding:10px; height:auto;">
                                    <option value="Belum Sempro">Belum Sempro</option>
                                    <option value="Pasca Sempro">Pasca Sempro</option>
                                    <option value="Persiapan Semhas">Persiapan Semhas</option>
                                    <option value="Pasca Semhas">Pasca Semhas</option>
                                    <option value="Persiapan Sidang">Persiapan Sidang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- TAHAPAN BIMBINGAN STEPPER -->
                    <input type="hidden" name="tahapan" id="agenda_tahapan" value="Proposal">
                    <div class="form-group" style="margin-top: 16px;">
                        <label
                            style="font-weight:700; font-size:13px; color:#374151; display:block; margin-bottom:10px;">&#x1F4CB;
                            Tahapan Bimbingan Saat Ini <span class="text-danger">*</span></label>
                        <div id="tahapan_stepper" style="display:grid; grid-template-columns: repeat(4,1fr); gap:10px;">
                            <div class="tahapan-card active" data-val="Proposal" onclick="selectTahapan(this)"
                                style="cursor:pointer; border-radius:10px; border:2px solid #4F46E5; background:#EEF2FF; padding:12px 10px; text-align:center; transition:all 0.2s;">
                                <div style="font-size:22px; margin-bottom:4px;">&#x1F4DD;</div>
                                <div style="font-size:11.5px; font-weight:800; color:#3730A3; line-height:1.3;">Proposal
                                </div>
                                <div style="font-size:10px; color:#6366F1; margin-top:2px;">Tahap 1</div>
                            </div>
                            <div class="tahapan-card" data-val="Bab 1 s.d. Bab 3" onclick="selectTahapan(this)"
                                style="cursor:pointer; border-radius:10px; border:2px solid #E5E7EB; background:#F9FAFB; padding:12px 10px; text-align:center; transition:all 0.2s;">
                                <div style="font-size:22px; margin-bottom:4px;">&#x1F4D6;</div>
                                <div style="font-size:11.5px; font-weight:800; color:#374151; line-height:1.3;">Naskah
                                    Bab 1&ndash;3</div>
                                <div style="font-size:10px; color:#9CA3AF; margin-top:2px;">Tahap 2</div>
                            </div>
                            <div class="tahapan-card" data-val="Bab 4 s.d. Bab 6" onclick="selectTahapan(this)"
                                style="cursor:pointer; border-radius:10px; border:2px solid #E5E7EB; background:#F9FAFB; padding:12px 10px; text-align:center; transition:all 0.2s;">
                                <div style="font-size:22px; margin-bottom:4px;">&#x1F4DA;</div>
                                <div style="font-size:11.5px; font-weight:800; color:#374151; line-height:1.3;">Naskah
                                    Bab 4&ndash;6</div>
                                <div style="font-size:10px; color:#9CA3AF; margin-top:2px;">Tahap 3</div>
                            </div>
                            <div class="tahapan-card" data-val="Finalisasi" onclick="selectTahapan(this)"
                                style="cursor:pointer; border-radius:10px; border:2px solid #E5E7EB; background:#F9FAFB; padding:12px 10px; text-align:center; transition:all 0.2s;">
                                <div style="font-size:22px; margin-bottom:4px;">&#x2705;</div>
                                <div style="font-size:11.5px; font-weight:800; color:#374151; line-height:1.3;">
                                    Finalisasi &amp; Revisi</div>
                                <div style="font-size:10px; color:#9CA3AF; margin-top:2px;">Tahap 4</div>
                            </div>
                        </div>
                        <style>
                            .tahapan-card:hover {
                                border-color: #818CF8 !important;
                                background: #F5F3FF !important;
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
                            }

                            .tahapan-card.active {
                                border-color: #4F46E5 !important;
                                background: #EEF2FF !important;
                                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
                            }

                            .tahapan-card.active div:first-child {
                                transform: scale(1.15);
                            }
                        </style>
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                        <label style="font-weight:700; font-size:13px; color:#374151;">Instruksi / Catatan untuk
                            Mahasiswa <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="instruksi" id="agenda_instruksi" rows="3" required
                            style="border-radius:8px; border:1px solid #D1D5DB; padding:12px; resize:vertical;"
                            placeholder="Tuliskan arahan umum atau fokus bimbingan kali ini..."></textarea>
                    </div>

                    <!-- SECTION: Kondisi & Konteks Mahasiswa untuk AI -->
                    <div
                        style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 20px; margin-top: 20px;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                            <span style="font-size:20px;">&#x1F9E0;</span>
                            <div>
                                <h5 style="margin:0 0 2px 0; font-weight:800; color:#92400E; font-size:14px;">Kondisi
                                    &amp; Konteks Mahasiswa</h5>
                                <p style="margin:0; font-size:11.5px; color:#B45309;">Isi sedetail mungkin &mdash;
                                    informasi ini digunakan AI untuk membuat to-do list yang relevan dan spesifik.</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight:700; font-size:12px; color:#374151;">&#x1F4D6; Bab/Bagian
                                        yang Sedang Dikerjakan</label>
                                    <select class="form-control" id="ctx_bab"
                                        style="border-radius:8px; border:1px solid #FCD34D; padding:9px; height:auto; background:#FFFDF5;">
                                        <option value="">-- Pilih Fokus Bab/Bagian --</option>
                                        <optgroup label="Penyusunan Proposal">
                                            <option value="Bab 1 - Pendahuluan">Bab 1 - Pendahuluan</option>
                                            <option value="Bab 2 - Tinjauan Pustaka">Bab 2 - Tinjauan Pustaka</option>
                                            <option value="Bab 3 - Metodologi Penelitian">Bab 3 - Metodologi Penelitian
                                            </option>
                                            <option value="Keseluruhan Naskah Proposal">Keseluruhan Naskah Proposal
                                            </option>
                                        </optgroup>
                                        <optgroup label="Penyusunan Lanjut">
                                            <option value="Bab 4 - Hasil dan Pembahasan">Bab 4 - Hasil dan Pembahasan
                                            </option>
                                            <option value="Bab 5 - Kesimpulan dan Saran">Bab 5 - Kesimpulan dan Saran
                                            </option>
                                            <option value="Bagian Akhir (Daftar Pustaka, Lampiran)">Bagian Akhir (Daftar
                                                Pustaka, Lampiran)</option>
                                        </optgroup>
                                        <option value="Revisi Komprehensif / Finalisasi">Revisi Komprehensif /
                                            Finalisasi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        style="font-weight:700; font-size:12px; color:#374151; margin-bottom:2px;">&#x1F4CA;
                                        Estimasi % Progress Naskah</label>
                                    <small
                                        style="display:block; font-size:10.5px; color:#B45309; line-height:1.2; margin-bottom:6px;">Seberapa
                                        rampung naskah pada tahapan/fase yang dipilih di atas?</small>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <input type="range" id="ctx_progress_persen" min="0" max="100" step="5"
                                            value="0"
                                            oninput="document.getElementById('ctx_progress_label').textContent = this.value + '%'"
                                            style="flex:1; accent-color:#F59E0B;">
                                        <span id="ctx_progress_label"
                                            style="font-weight:800; color:#92400E; width:42px; text-align:right;">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight:700; font-size:12px; color:#374151;">&#x1F4CA; Frekuensi
                                        Bimbingan</label>
                                    <select class="form-control" id="ctx_frekuensi"
                                        style="border-radius:8px; border:1px solid #FCD34D; padding:9px; height:auto; background:#FFFDF5;">
                                        <option value="">-- Pilih --</option>
                                        <option value="Belum pernah bimbingan">Belum pernah bimbingan</option>
                                        <option value="Di bawah 5x">Di bawah 5x</option>
                                        <option value="Di atas 5x">Di atas 5x</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-weight:700; font-size:12px; color:#374151;">&#x23F1;&#xFE0F;
                                        Responsivitas Mahasiswa</label>
                                    <select class="form-control" id="ctx_responsif"
                                        style="border-radius:8px; border:1px solid #FCD34D; padding:9px; height:auto; background:#FFFDF5;">
                                        <option value="">-- Pilih --</option>
                                        <option value="Aktif dan responsif">Aktif &amp; responsif</option>
                                        <option value="Cukup responsif">Cukup responsif</option>
                                        <option value="Kurang responsif">Kurang responsif</option>
                                        <option value="Sangat lambat / susah dihubungi">Sangat lambat / susah dihubungi
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="font-weight:700; font-size:12px; color:#374151;">&#x26A0;&#xFE0F; Kendala yang
                                Diketahui Dosen</label>
                            <textarea class="form-control" id="ctx_kendala" rows="2"
                                placeholder="Contoh: Mahasiswa kesulitan di bagian analisis data, jarang hadir konsultasi, data penelitian belum lengkap..."
                                style="border-radius:8px; border:1px solid #FCD34D; padding:10px; resize:vertical; background:#FFFDF5;"></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-weight:700; font-size:12px; color:#374151;">&#x1F4AC; Catatan Tambahan
                                untuk AI <span style="font-weight:400; color:#92400E;">(opsional)</span></label>
                            <textarea class="form-control" id="ctx_catatan_tambahan" rows="2"
                                placeholder="Informasi lain yang relevan, misalnya: mahasiswa sudah punya outline, sudah sempro tapi naskah belum direvisi..."
                                style="border-radius:8px; border:1px solid #FCD34D; padding:10px; resize:vertical; background:#FFFDF5;"></textarea>
                        </div>
                    </div>

                    <div
                        style="background: #EEF2FF; border: 1px dashed #A5B4FC; border-radius: 12px; padding: 20px; margin-top: 24px;">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <div>
                                <h5 style="margin:0 0 4px 0; font-weight:800; color:#3730A3; font-size:14px;"><i
                                        class="fa fa-list-check"></i> To-Do List Mahasiswa</h5>
                                <p style="margin:0; font-size:12px; color:#4F46E5;">Rincian tugas konkret yang harus
                                    dikerjakan mahasiswa untuk agenda ini.</p>
                            </div>
                            <button type="button" id="btn_generate_ai_agenda" onclick="generateAiAgenda()"
                                style="background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%); color: white; border: none; border-radius: 8px; font-weight: 700; padding: 9px 18px; font-size: 13px; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4); cursor: pointer; white-space: nowrap; transition: all 0.2s; letter-spacing: 0.3px;">
                                <i class="fa fa-magic"></i> &#x2728; Generate via AI
                            </button>
                            <style>
                                #btn_generate_ai_agenda:hover {
                                    transform: translateY(-1px);
                                    box-shadow: 0 6px 18px rgba(124, 58, 237, 0.5);
                                }
                            </style>
                        </div>

                        <div id="aiReviewAgendaPreview"
                            style="display:none; background:#FFF; padding:15px; border-radius:8px; border:1px solid #E0E7FF; margin-bottom:16px; font-size:12.5px; color:#374151; max-height:200px; overflow-y:auto;">
                        </div>

                        <div style="margin-top: 15px;">
                            <textarea id="todoTextarea" class="form-control" rows="5"
                                placeholder="Tuliskan tiap tugas dalam satu baris (pisahkan dengan menekan Enter)&#10;Contoh:&#10;1. Perbaiki bab 1&#10;2. Cari jurnal referensi terbaru&#10;3. Hubungi tempat riset"
                                style="border-radius:8px; border:1px solid #C7D2FE; padding:10px; resize:vertical; background:#FFFFFF;"></textarea>
                            <p style="margin-top: 8px; font-size: 11.5px; color: #6B7280;"><i
                                    class="fa fa-info-circle"></i> Setiap baris baru akan dihitung sebagai 1 item To-Do
                                List terpisah.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E5E7EB; padding: 16px 24px;">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                    style="border-radius: 8px; font-weight: 600; padding: 10px 20px; color: #374151; border-color: #D1D5DB;">Batal</button>
                <button type="button" id="btnSimpanAgenda" onclick="simpanAgenda()"
                    style="border-radius: 8px; font-weight: 800; padding: 10px 24px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35); cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;"><i
                        class="fa fa-check"></i> Simpan Agenda</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit To-Do List Agenda Aktif -->
<div class="modal fade" id="modalEditTodo" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"
        style="width: 90%; max-width: 850px; margin: 8vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content"
            style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div class="modal-header"
                style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title"
                    style="font-weight: 800; font-size: 16px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-pencil text-primary"></i> Edit To-Do List Agenda
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #F9FAFB;">
                <p style="font-size: 13px; color: #6B7280; margin-bottom: 16px;">
                    <i class="fa fa-info-circle text-primary"></i>
                    Perubahan akan mengganti seluruh to-do list agenda aktif. Tugas yang sudah diverifikasi akan ikut
                    dihapus.
                </p>
                <div id="editTodoListContainer" style="display: flex; flex-direction: column; gap: 0;">
                    <!-- Items will be loaded here -->
                </div>
                <button type="button" onclick="addEditTodoItem('')"
                    style="margin-top: 12px; border-radius: 8px; border: 2px dashed #A5B4FC; color: #4F46E5; background: transparent; width: 100%; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    <i class="fa fa-plus"></i> + Tambah Tugas
                </button>
            </div>
            <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E5E7EB; padding: 16px 24px;">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                    style="border-radius: 8px; font-weight: 600; padding: 10px 20px; color: #374151; border-color: #D1D5DB;">Batal</button>
                <button type="button" id="btnSimpanEditTodo" onclick="simpanEditTodo()"
                    style="border-radius: 8px; font-weight: 800; padding: 10px 24px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35); cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;"><i
                        class="fa fa-save"></i> Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Foto Mahasiswa -->
<div class="modal fade" id="modalFotoMahasiswa" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"
        style="max-width: 400px; margin: 10vh auto; font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="modal-content"
            style="border-radius: 16px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div class="modal-header"
                style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;">
                <h4 class="modal-title"
                    style="font-weight: 800; font-size: 15px; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-user text-primary"></i> Foto Mahasiswa
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #4B5563; opacity: 0.7; font-size: 24px; font-weight: 300; background: none; border: none; cursor: pointer; padding: 0; margin: 0; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px; background: #F9FAFB; text-align: center;">
                <div
                    style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 4px solid #FFFFFF; background: #FFFFFF; max-width: 280px; margin: 0 auto;">
                    <img src="<?= $photo_detail_src ?>" alt="<?= $nm_pd ?>"
                        onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';"
                        style="width: 100%; height: auto; display: block; object-fit: contain;">
                </div>
                <div
                    style="margin-top: 15px; font-weight: 700; color: #111827; font-size: 14px; text-transform: uppercase;">
                    <?= $nm_pd ?>
                </div>
                <div style="font-size: 12px; color: #6B7280; font-weight: 600; margin-top: 2px;">
                    NIM: <?= $data_skripsi->nipd ?>
                </div>
            </div>
            <div class="modal-footer" style="background: #FFFFFF; border-top: 1px solid #E5E7EB; padding: 12px 24px;">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                    style="border-radius: 8px; font-weight: 600; padding: 8px 20px; color: #374151; border-color: #D1D5DB; width: 100%;">Tutup</button>
            </div>
        </div>
    </div>
</div>