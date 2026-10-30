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
        display: flex;
        flex-direction: column;
        height: 100%;
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
    
    #search-bimbingan:focus {
        border-color: var(--primary) !important;
        background: #FFFFFF !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    /* ======================================================
       LOADING OVERLAY
       ====================================================== */
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.6s ease, visibility 0.6s ease;
    }
    .init-loading-box {
        width: 380px;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        padding: 25px 20px;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transform: translateY(0) scale(1);
        transition: transform 0.6s ease;
    }
    .init-logo-area {
        text-align: center;
        margin-bottom: 20px;
        animation: fadeInDown 0.6s ease both;
    }
    .init-logo-area .init-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6, #06b6d4);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin: 0 auto 15px;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
    }
    .init-logo-area h2 {
        font-size: 18px;
        font-weight: 800;
        color: #f1f5f9;
        margin: 0 0 6px;
        letter-spacing: -0.5px;
    }
    .init-logo-area p {
        font-size: 12px;
        color: #94a3b8;
        margin: 0;
        font-weight: 400;
    }
    .init-progress-container {
        width: 100%;
        animation: fadeInUp 0.6s ease 0.2s both;
    }
    .init-progress-labels {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .init-progress-labels .init-step-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    .init-progress-labels .init-step-label.active {
        color: #60a5fa;
    }
    .init-progress-labels .init-step-label.done {
        color: #34d399;
    }
    .init-progress-track {
        width: 100%;
        height: 6px;
        background: rgba(255,255,255,0.08);
        border-radius: 99px;
        overflow: hidden;
        position: relative;
    }
    .init-progress-bar {
        height: 100%;
        width: 0%;
        border-radius: 99px;
        background: linear-gradient(90deg, #3b82f6, #06b6d4, #8b5cf6);
        background-size: 200% 100%;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        animation: gradientShift 2s linear infinite;
        box-shadow: 0 0 12px rgba(59, 130, 246, 0.6);
    }
    .init-progress-pct {
        text-align: center;
        margin-top: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #60a5fa;
        letter-spacing: 0.5px;
    }
    .init-status-text {
        text-align: center;
        margin-top: 4px;
        font-size: 11px;
        color: #475569;
        min-height: 16px;
        transition: opacity 0.3s ease;
    }
    .init-steps-row {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        gap: 8px;
    }
    .init-step-chip {
        flex: 1;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 8px;
        padding: 8px 4px;
        text-align: center;
        transition: all 0.4s ease;
    }
    .init-step-chip .step-icon {
        font-size: 14px;
        color: #334155;
        margin-bottom: 4px;
        display: block;
        transition: color 0.3s ease;
    }
    .init-step-chip .step-name {
        font-size: 9px;
        color: #475569;
        font-weight: 600;
        display: block;
        transition: color 0.3s ease;
    }
    .init-step-chip.loading {
        background: rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.2);
    }
    .init-step-chip.loading .step-icon { color: #3b82f6; }
    .init-step-chip.loading .step-name { color: #60a5fa; }
    .init-step-chip.done {
        background: rgba(52, 211, 153, 0.08);
        border-color: rgba(52, 211, 153, 0.2);
    }
    .init-step-chip.done .step-icon { color: #34d399; }
    .init-step-chip.done .step-name { color: #34d399; }

    @keyframes gradientShift {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
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
                    <div class="actions" style="margin: 0 !important; float: none !important; padding: 0 !important; display: flex !important; align-items: center !important; gap: 10px;">
                        <button class="btn btn-default btn-sm" onclick="showStrategicPlanModal()" style="border-radius: 8px; font-weight: 700; padding: 8px 16px; background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%) !important; color: #FFFFFF !important; border: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.2); font-family: 'Plus Jakarta Sans', sans-serif;">
                            <span style="color: #FFFFFF !important;">✨</span> AI Strategic Plan
                        </button>
                        <button class="btn btn-default btn-sm" onclick="showKnowledgeBaseModal()" style="border-radius: 8px; font-weight: 700; padding: 8px 16px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.05); font-family: 'Plus Jakarta Sans', sans-serif;">
                            <span>📚</span> Knowledge Base Bimbingan
                        </button>
                        <a href="<?= site_url('C_wall') ?>" class="btn btn-sm" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 8px; font-weight: 600; padding: 8px 16px; display: inline-flex; align-items: center; gap: 6px;"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
    <div class="portlet-body">

        <div id="dashboard-skeleton-container" style="display:block;">
            <style>
                .skeleton-shimmer {
                    background: #f6f7f8;
                    background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
                    background-repeat: no-repeat;
                    background-size: 800px 100%; 
                    animation-duration: 1.5s;
                    animation-fill-mode: forwards; 
                    animation-iteration-count: infinite;
                    animation-name: placeholderShimmer;
                    animation-timing-function: linear;
                }
                @keyframes placeholderShimmer {
                    0% { background-position: -468px 0; }
                    100% { background-position: 468px 0; }
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
            
            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div class="skeleton-shimmer" style="height: 90px; flex: 1; border-radius: 12px;"></div>
                <div class="skeleton-shimmer" style="height: 90px; flex: 1; border-radius: 12px;"></div>
                <div class="skeleton-shimmer" style="height: 90px; flex: 1; border-radius: 12px;"></div>
                <div class="skeleton-shimmer" style="height: 90px; flex: 1; border-radius: 12px;"></div>
                <div class="skeleton-shimmer" style="height: 90px; flex: 1; border-radius: 12px;"></div>
            </div>
            
            <div class="skeleton-shimmer" style="height: 40px; width: 300px; border-radius: 8px; margin-bottom: 24px;"></div>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
                <div class="skeleton-shimmer" style="height: 350px; border-radius: 16px;"></div>
            </div>
        </div>

        <div id="dashboard-live-container" style="display:none;"></div>

        <div id="loading-overlay">
            <div class="init-loading-box">
                <div class="init-logo-area">
                    <div class="init-icon">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h2>Bimbingan TA</h2>
                    <p>Sinkronisasi data mahasiswa...</p>
                </div>

                <div class="init-progress-container">
                    <div class="init-progress-labels">
                        <span class="init-step-label active" id="lbl-mahasiswa">Data</span>
                        <span class="init-step-label" id="lbl-bimbingan">Bimbingan</span>
                        <span class="init-step-label" id="lbl-health">Health</span>
                    </div>
                    <div class="init-progress-track">
                        <div class="init-progress-bar" id="loading-progress"></div>
                    </div>
                    <div class="init-progress-pct" id="loading-pct">0%</div>
                    <div class="init-status-text" id="init-status-text">Menghitung skor Academic Health...</div>

                    <div class="init-steps-row">
                        <div class="init-step-chip loading" id="chip-mahasiswa">
                            <span class="step-icon"><i class="fa-solid fa-users"></i></span>
                            <span class="step-name">Data</span>
                        </div>
                        <div class="init-step-chip" id="chip-bimbingan">
                            <span class="step-icon"><i class="fa-solid fa-comments"></i></span>
                            <span class="step-name">Bimbingan</span>
                        </div>
                        <div class="init-step-chip" id="chip-health">
                            <span class="step-icon"><i class="fa-solid fa-heart-pulse"></i></span>
                            <span class="step-name">Health</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            let progress = 0;
            let overlay = document.getElementById('loading-overlay');
            let progressBar = document.getElementById('loading-progress');
            let progressText = document.getElementById('loading-pct');
            let statusText = document.getElementById('init-status-text');
            
            // Animasi persentase loading buatan agar user tidak bosan
            let progressInterval = setInterval(function() {
                if (progress < 85) {
                    progress += Math.floor(Math.random() * 5) + 1;
                    if (progress > 85) progress = 85;
                    progressBar.style.width = progress + '%';
                    progressText.textContent = progress + '%';

                    if (progress > 30 && progress <= 60) {
                        $('#chip-mahasiswa').removeClass('loading').addClass('done');
                        $('#lbl-mahasiswa').removeClass('active').addClass('done');
                        
                        $('#chip-bimbingan').addClass('loading');
                        $('#lbl-bimbingan').addClass('active');
                        
                        statusText.textContent = "Menghitung riwayat bimbingan...";
                    } else if (progress > 60) {
                        $('#chip-bimbingan').removeClass('loading').addClass('done');
                        $('#lbl-bimbingan').removeClass('active').addClass('done');
                        
                        $('#chip-health').addClass('loading');
                        $('#lbl-health').addClass('active');
                        
                        statusText.textContent = "Menghitung skor Academic Health...";
                    } else {
                        statusText.textContent = "Menyiapkan data mahasiswa...";
                    }
                }
            }, 100);

            // Fetch AJAX data cards
            $.ajax({
                url: "<?= site_url('E_skripsi_bimbingan/ajax_load_cards_html') ?>",
                type: "GET",
                dataType: "html",
                success: function(response) {
                    clearInterval(progressInterval);
                    progress = 100;
                    progressBar.style.width = progress + '%';
                    progressText.textContent = progress + '%';
                    
                    $('#chip-health').removeClass('loading').addClass('done');
                    $('#lbl-health').removeClass('active').addClass('done');
                    statusText.textContent = "Selesai!";


                    setTimeout(function() {
                        overlay.style.opacity = '0';
                        setTimeout(function() {
                            overlay.style.display = 'none';
                            document.getElementById('dashboard-skeleton-container').style.display = 'none';
                            
                            var liveContainer = document.getElementById('dashboard-live-container');
                            liveContainer.innerHTML = response;
                            liveContainer.style.display = 'block';

                            // Call restore scroll if exist
                            if (typeof window.restoreSavedScroll === 'function') {
                                window.restoreSavedScroll();
                            }
                        }, 500);
                    }, 300);
                },
                error: function(xhr, status, error) {
                    clearInterval(progressInterval);
                    console.error("AJAX Error Response:", xhr.responseText);
                    
                    var errMsg = xhr.responseText;
                    var fatalMatch = errMsg.match(/<b>(?:Fatal error|Parse error|Warning|Type Error)<\/b>:(.*?)<br/i);
                    var displayError = fatalMatch ? "Error PHP: " + fatalMatch[1] : "Lihat pesan lengkap di Console (F12). Atau perhatikan potongan ini: " + errMsg.substring(errMsg.length - 800);
                    
                    alert("Gagal memuat data bimbingan.\n\n" + displayError);
                    overlay.style.display = 'none';
                }
            });
        });
        </script>

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

function filterBimbingan() {
    var input = document.getElementById('search-bimbingan');
    var filter = input.value.toLowerCase().trim();
    var clearBtn = document.getElementById('clear-bimbingan-search');
    
    if (filter.length > 0) {
        clearBtn.style.display = 'block';
    } else {
        clearBtn.style.display = 'none';
    }
    
    var cards = document.getElementsByClassName('student-card');
    var grids = document.querySelectorAll('.student-grid-4, .student-grid-5, .student-grid-6');
    var totalVisible = 0;
    
    grids.forEach(function(grid) {
        var gridCards = grid.getElementsByClassName('student-card');
        var visibleInGrid = 0;
        
        for (var i = 0; i < gridCards.length; i++) {
            var card = gridCards[i];
            var cardText = card.textContent.toLowerCase();
            
            if (cardText.includes(filter)) {
                card.style.display = '';
                visibleInGrid++;
                totalVisible++;
            } else {
                card.style.display = 'none';
            }
        }
        
        var heading = grid.previousElementSibling;
        while (heading && heading.tagName !== 'H4') {
            heading = heading.previousElementSibling;
        }
        
        if (heading) {
            if (visibleInGrid === 0) {
                heading.style.display = 'none';
                grid.style.display = 'none';
            } else {
                heading.style.display = '';
                grid.style.display = '';
            }
        }
    });
    
    var noResults = document.getElementById('no-bimbingan-results');
    var container = document.querySelector('.portlet-body');
    
    if (totalVisible === 0 && cards.length > 0) {
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.id = 'no-bimbingan-results';
            noResults.style.cssText = 'text-align: center; padding: 60px 20px; color: #64748B; background: #FFFFFF; border-radius: 16px; border: 2px dashed #E2E8F0; margin-top: 20px;';
            noResults.innerHTML = '<i class="fa fa-search-minus" style="font-size: 54px; margin-bottom: 12px; color: #94A3B8;"></i>' +
                                  '<p style="font-weight: 700; margin: 0; font-size: 15px;">Mahasiswa dengan nama atau NIM tersebut tidak ditemukan.</p>';
            container.appendChild(noResults);
        } else {
            noResults.style.display = 'block';
        }
    } else {
        if (noResults) {
            noResults.style.display = 'none';
        }
    }
}

function clearBimbinganSearch() {
    var input = document.getElementById('search-bimbingan');
    input.value = '';
    filterBimbingan();
    input.focus();
}

// ============================================================
// SCROLL RESTORE & CARD HIGHLIGHT (After Progressive Load)
// ============================================================
function saveScrollPos(id_judul_skripsi) {
    sessionStorage.setItem('bimbingan_scroll', window.scrollY);
    sessionStorage.setItem('bimbingan_last_card', id_judul_skripsi);
}

document.addEventListener('DOMContentLoaded', function() {
    var savedScroll  = sessionStorage.getItem('bimbingan_scroll');
    var lastCard     = sessionStorage.getItem('bimbingan_last_card');

    // Bersihkan session storage agar tidak menumpuk
    sessionStorage.removeItem('bimbingan_scroll');
    sessionStorage.removeItem('bimbingan_last_card');
    sessionStorage.removeItem('bimbingan_needs_refresh'); // hapus flag lama jika ada

    // Ekspor fungsi ini agar bisa dipanggil oleh ajax_load_cards_html setelah innerHTML dirender
    window.restoreSavedScroll = function() {
        if (savedScroll !== null) {
            // Beri sedikit jeda agar DOM browser mengkalkulasi tinggi elemen setelah innerHTML di-set
            setTimeout(function() {
                window.scrollTo({ top: parseInt(savedScroll), behavior: 'instant' });
            }, 50);
        }

        if (lastCard) {
            setTimeout(function() {
                var el = document.getElementById('card-' + lastCard);
                if (el) {
                    el.style.transition = 'box-shadow 0.5s ease';
                    el.style.boxShadow  = '0 0 0 3px rgba(79,70,229,0.4)';
                    setTimeout(function() { el.style.boxShadow = ''; }, 1500);
                }
            }, 100);
        }
    };
});

// ============================================================
// ZOOM FOTO MAHASISWA
// ============================================================
function showZoomFotoBimbingan(src, name, nipd, judul, is_lulus) {
    var imgElement = document.getElementById('zoomFotoImgBimbingan');
    var shimmerElement = document.getElementById('zoomFotoShimmer');
    
    // Clear previous photo and show shimmer
    imgElement.style.display = 'none';
    shimmerElement.style.display = 'block';
    
    // Generate thumbnail URL fallback
    var thumbSrc = src;
    if (src.indexOf('/siakad_file_mahasiswa/') !== -1 && src.indexOf('/thumbs/thumb_') === -1) {
        thumbSrc = src.replace('/siakad_file_mahasiswa/', '/siakad_file_mahasiswa/thumbs/thumb_');
    }
    
    // Set up a timeout to load thumbnail if full photo is too slow
    var loadTimeout = setTimeout(function() {
        if (imgElement.src !== thumbSrc && shimmerElement.style.display !== 'none') {
            console.log("Full image took too long, falling back to thumbnail");
            imgElement.src = thumbSrc;
        }
    }, 10000); // 10 seconds timeout
    
    // Set new image source (full size)
    imgElement.src = src;
    
    // When image loads, hide shimmer and show image
    imgElement.onload = function() {
        clearTimeout(loadTimeout);
        shimmerElement.style.display = 'none';
        imgElement.style.display = 'block';
    };

    document.getElementById('zoomFotoNameBimbingan').innerText = name;
    document.getElementById('zoomFotoNipdBimbingan').innerText = nipd;
    document.getElementById('zoomFotoJudulBimbingan').innerText = judul || 'Belum Ditetapkan';
    
    $('#modalZoomFotoBimbingan').modal('show');
}
</script>

<style>
.shimmer-loading {
    animation: shimmer 2s infinite linear;
    background: linear-gradient(to right, #eff1f3 4%, #e2e8f0 25%, #eff1f3 36%);
    background-size: 1000px 100%;
}
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
</style>

<!-- Modal Zoom Foto Mahasiswa -->
<div class="modal fade" id="modalZoomFotoBimbingan" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.85);">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px;">
        <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
            <div class="modal-header" style="border: none; padding: 0; position: absolute; right: -20px; top: -20px; z-index: 10;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #FFF; text-shadow: none; opacity: 1; font-size: 32px; background: rgba(0,0,0,0.5); width: 40px; height: 40px; border-radius: 50%; outline: none; padding: 0; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; position: relative;">
                <div style="text-align: center; position: relative;">
                    <div id="zoomFotoShimmer" class="shimmer-loading" style="width: 100%; max-width: 300px; height: 400px; margin: 0 auto; border-radius: 16px; border: 4px solid #FFF;"></div>
                    <img id="zoomFotoImgBimbingan" src="" alt="Foto Mahasiswa" style="width: auto; max-width: 100%; max-height: 65vh; margin: 0 auto; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 4px solid #FFF; display: none;">
                </div>
                
                <div style="background: #FFF; border-radius: 12px; padding: 15px; margin-top: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); text-align: center;">
                    <div style="text-align: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #E5E7EB;">
                        <div id="zoomFotoNameBimbingan" style="font-size: 16px; color: #111827; font-weight: 800; margin-bottom: 2px;"></div>
                        <div id="zoomFotoNipdBimbingan" style="font-size: 13px; color: #4F46E5; font-weight: 700; font-family: monospace; letter-spacing: 0.5px;"></div>
                    </div>
                    
                    <div style="margin-bottom: 8px;">
                        <div style="font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase;">Judul Tugas Akhir</div>
                        <div id="zoomFotoJudulBimbingan" style="font-size: 13px; color: #374151; font-weight: 600; line-height: 1.4;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>