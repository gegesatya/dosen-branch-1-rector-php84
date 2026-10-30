<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= base_url('assets/theme/assets/admin/pages/css/timeline.css'); ?>" rel="stylesheet" type="text/css" />

<style>
    /* ======================================================
       INIT LOADING OVERLAY — Progress Bar (sekali setelah login)
       ====================================================== */
    #init-loading-overlay {
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
    #init-loading-overlay.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #init-loading-overlay.hidden .init-loading-box {
        transform: translateY(20px) scale(0.95);
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

    /* ======================================================
       SKELETON SHIMMER LOADER
       ====================================================== */
    .skeleton-pulse {
        background: linear-gradient(90deg, #f0f4f8 25%, #e2e8f0 50%, #f0f4f8 75%);
        background-size: 400% 100%;
        animation: shimmer 1.6s ease infinite;
        border-radius: 8px;
    }
    @keyframes shimmer {
        0%   { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .skeleton-chart {
        height: 270px;
        width: 100%;
        border-radius: 12px;
    }
    .skeleton-list-item {
        height: 52px;
        margin-bottom: 10px;
        border-radius: 8px;
    }
    .skeleton-warning {
        height: 72px;
        margin-bottom: 12px;
        border-radius: 8px;
    }
    .skeleton-table-row {
        height: 44px;
        margin-bottom: 8px;
        border-radius: 6px;
    }
    .skeleton-donut {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        margin: 0 auto 15px;
    }
    .skeleton-health-row {
        height: 44px;
        margin-bottom: 10px;
        border-radius: 6px;
    }
    .skeleton-feed-item {
        height: 68px;
        margin-bottom: 10px;
        border-radius: 8px;
    }

    /* ======================================================
       DASHBOARD STYLES (EXISTING)
       ====================================================== */
    .dashboard-wrapper {
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        background: #f4f6f9;
        padding: 20px;
        border-radius: 16px;
    }

    .kpi-container { margin-bottom: 25px; }

    .kpi-card {
        background: #ffffff;
        border-radius: 6px;
        padding: 24px 20px;
        border: none;
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 30px 0 rgba(34, 41, 47, 0.15);
    }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        float: right;
    }

    .bg-gradient-blue   { background: rgba(115, 103, 240, 0.12); color: #7367F0; }
    .bg-gradient-orange { background: rgba(255, 159, 67, 0.12); color: #ff9f43; }
    .bg-gradient-red    { background: rgba(234, 84, 85, 0.12); color: #ea5455; }
    .bg-gradient-green  { background: rgba(40, 199, 111, 0.12); color: #28c76f; }
    .bg-gradient-purple { background: rgba(0, 207, 232, 0.12); color: #00cfe8; }

    @media (min-width: 992px) {
        .col-kpi-5 {
            flex: 0 0 20%;
            max-width: 20%;
        }
    }

    .kpi-title {
        font-size: 13px;
        font-weight: 500;
        text-transform: capitalize;
        color: #6E6B7B;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: #5E5873;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .kpi-desc {
        font-size: 12px;
        color: #6E6B7B;
        font-weight: 400;
    }

    .dashboard-panel {
        background: #ffffff;
        border-radius: 6px;
        padding: 24px;
        border: none;
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        margin-bottom: 25px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #f0f2f5;
        padding-bottom: 12px;
    }

    .panel-title {
        font-size: 16px;
        font-weight: 500;
        color: #5E5873;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-title i { color: #7367F0; }

    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .item-row:last-child { border-bottom: none; }
    .item-details strong { font-size: 14px; color: #2c3e50; }
    .item-details span   { font-size: 12px; color: #8a909d; }

    .badge-premium {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-blue   { background: #e0f2fe; color: #0369a1; }
    .badge-orange { background: #ffedd5; color: #c2410c; }
    .badge-green  { background: #dcfce7; color: #15803d; }
    .badge-teal   { background: #ccfbf1; color: #0f766e; }
    .badge-red    { background: #fee2e2; color: #b91c1c; }

    .progress-bar-custom {
        height: 8px;
        background: #e5e7eb;
        border-radius: 99px;
        overflow: hidden;
        min-width: 120px;
        margin-bottom: 4px;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.4s ease;
    }

    .bg-bar-green  { background: #22c55e; }
    .bg-bar-teal   { background: #14b8a6; }
    .bg-bar-orange { background: #f97316; }
    .bg-bar-red    { background: #ef4444; }

    .warning-box {
        background: #f8f8f8;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .warning-box strong {
        font-size: 14px;
        color: #0f172a;
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .warning-box p { font-size: 13px; color: #475569; margin: 0 0 10px 0; line-height: 1.5; }

    .table-premium th {
        font-weight: 600;
        color: #8a909d;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #f0f2f5 !important;
    }

    .table-premium td {
        font-size: 13px;
        color: #2c3e50;
        padding: 12px 8px !important;
        vertical-align: middle !important;
    }

    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .timeline { padding: 0; margin: 0; }
    .timeline:before { left: 20px !important; background: #e2e8f0 !important; }
    .timeline-item { margin-left: 40px !important; position: relative; }
    .timeline-badge { left: -40px !important; top: 15px !important; }
    .timeline-icon {
        width: 32px !important; height: 32px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .timeline-body {
        margin-left: 15px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 16px !important;
        background: #f8f8f8 !important;
    }
    .timeline-body-title { font-weight: 700 !important; font-size: 14px !important; }
    .date-separator { margin-left: 0 !important; padding: 15px 0 !important; }
    .date-separator .timeline-body {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    /* Panel fade-in when content is injected */
    .panel-content-area {
        transition: opacity 0.4s ease;
    }
    .panel-content-area.loading-state {
        opacity: 0.4;
    }

    @media (max-width: 1440px) {
        .kpi-card { padding: 18px 14px; }
        .kpi-icon-wrapper { width: 42px; height: 42px; font-size: 18px; border-radius: 10px; }
        .kpi-value { font-size: 24px; }
        .kpi-title { font-size: 11px; }
        .kpi-desc { font-size: 10px; }

        .schedule-item-row { flex-direction: column !important; align-items: flex-start !important; }
        .schedule-item-details { max-width: 100% !important; white-space: normal !important; margin-bottom: 6px; }
    }
</style>

<!-- =====================================================================
     INIT LOADING OVERLAY
     ===================================================================== -->
<div id="init-loading-overlay">
    <div class="init-loading-box">
        <div class="init-logo-area">
            <div class="init-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h2>Academic Wall</h2>
            <p>Menyiapkan data dashboard Anda...</p>
        </div>

        <div class="init-progress-container">
            <div class="init-progress-labels">
                <span class="init-step-label active" id="lbl-kpi">KPI</span>
                <span class="init-step-label" id="lbl-chart">Chart &amp; Jadwal</span>
                <span class="init-step-label" id="lbl-warning">Early Warning</span>
                <span class="init-step-label" id="lbl-health">Academic Health</span>
                <span class="init-step-label" id="lbl-feed">Linimasa</span>
            </div>
            <div class="init-progress-track">
                <div class="init-progress-bar" id="init-progress-bar"></div>
            </div>
            <div class="init-progress-pct" id="init-progress-pct">0%</div>
            <div class="init-status-text" id="init-status-text">Menginisialisasi...</div>

            <div class="init-steps-row">
                <div class="init-step-chip loading" id="chip-kpi">
                    <span class="step-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    <span class="step-name">KPI</span>
                </div>
                <div class="init-step-chip" id="chip-chart">
                    <span class="step-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span class="step-name">Chart</span>
                </div>
                <div class="init-step-chip" id="chip-warning">
                    <span class="step-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span class="step-name">Warning</span>
                </div>
                <div class="init-step-chip" id="chip-health">
                    <span class="step-icon"><i class="fa-solid fa-heart-pulse"></i></span>
                    <span class="step-name">Health</span>
                </div>
                <div class="init-step-chip" id="chip-feed">
                    <span class="step-icon"><i class="fa-solid fa-receipt"></i></span>
                    <span class="step-name">Linimasa</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =====================================================================
     MAIN PAGE CONTENT
     ===================================================================== -->
<div class="page-content" style="background-color: #f4f6f9; min-height: 100vh;">
    
    <div class="page-head" style="margin-bottom: 25px;">
        <div class="page-title">
            <h1 style="font-weight: 800; color: #2c3e50; font-size: 24px;">
                <i class="fa-solid fa-chart-line text-primary"></i> <?= $judul_besar ?>
                <span style="font-size: 14px; font-weight: 500; color: #8a909d; display: block; margin-top: 5px;"><?= $judul_kecil ?></span>
            </h1>
        </div>
    </div>

    <div class="dashboard-wrapper">
        
        <!-- ========== ROW 1: KPI CARDS (server-side, langsung tampil) ========== -->
        <?php $col_kpi_class = (isset($is_pembimbing_pkl) && $is_pembimbing_pkl) ? 'col-kpi-5 col-lg-3 col-md-6 col-sm-12' : 'col-lg-3 col-md-6 col-sm-12'; ?>
        <div class="row kpi-container">
            <div class="<?= $col_kpi_class ?>">
                <div class="kpi-card" style="cursor: pointer;" onclick="window.location.href='<?= site_url('C_jadwal') ?>'">
                    <div class="kpi-icon-wrapper bg-gradient-blue"><i class="fa-solid fa-book"></i></div>
                    <div class="kpi-title">SKS Mengajar</div>
                    <div class="kpi-value"><?= $total_sks ?></div>
                    <div class="kpi-desc text-success"><i class="fa-solid fa-circle-check"></i> Semester Aktif</div>
                </div>
            </div>
            <div class="<?= $col_kpi_class ?>">
                <div class="kpi-card" style="cursor: pointer;" onclick="window.location.href='<?= site_url('E_skripsi_bimbingan') ?>'">
                    <div class="kpi-icon-wrapper bg-gradient-orange"><i class="fa-solid fa-user-graduate"></i></div>
                    <div class="kpi-title">Bimbingan TA</div>
                    <div class="kpi-value"><?= $total_ta ?></div>
                    <div class="kpi-desc text-warning"><i class="fa-solid fa-clock"></i> Progress Aktif</div>
                </div>
            </div>
            <div class="<?= $col_kpi_class ?>">
                <div class="kpi-card" style="cursor: pointer;" onclick="window.location.href='<?= site_url('Lecturer_persetujuan_krs') ?>'">
                    <div class="kpi-icon-wrapper bg-gradient-red"><i class="fa-solid fa-calendar-check"></i></div>
                    <div class="kpi-title">KRS Pending</div>
                    <div class="kpi-value"><?= $krs_pending ?></div>
                    <div class="kpi-desc">
                        <span class="text-danger" style="font-weight: 600;"><i class="fa-solid fa-triangle-exclamation"></i> <?= $krs_pending ?> Pending</span> &nbsp;|&nbsp;
                        <span class="text-success" style="font-weight: 600;"><i class="fa-solid fa-circle-check"></i> <?= $krs_final ?> Final</span>
                    </div>
                </div>
            </div>
            <div class="<?= $col_kpi_class ?>">
                <div class="kpi-card" style="cursor: pointer;" onclick="window.location.href='<?= site_url('E_skripsi_seminar_sidang') ?>'">
                    <div class="kpi-icon-wrapper bg-gradient-green"><i class="fa-solid fa-gavel"></i></div>
                    <div class="kpi-title">Sidang / Seminar</div>
                    <div class="kpi-value"><?= $sidang_count ?></div>
                    <div class="kpi-desc text-primary"><i class="fa-solid fa-calendar-days"></i> 7 Hari Kedepan</div>
                </div>
            </div>
            <?php if (isset($is_pembimbing_pkl) && $is_pembimbing_pkl): ?>
            <div class="<?= $col_kpi_class ?>">
                <div class="kpi-card" style="cursor: pointer;" onclick="window.location.href='<?= site_url('C_laporan_magang') ?>'">
                    <div class="kpi-icon-wrapper bg-gradient-purple"><i class="fa-solid fa-briefcase"></i></div>
                    <div class="kpi-title">Review PKL</div>
                    <div class="kpi-value"><?= isset($pkl_butuh_review) ? $pkl_butuh_review : 0 ?></div>
                    <div class="kpi-desc text-info"><i class="fa-solid fa-users"></i> Mahasiswa Butuh Review</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ========== ROW 2: CHART + JADWAL ========== -->
        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-panel" style="height: 360px; margin-bottom: 20px;">
                    <div class="panel-header" style="border-bottom: none; margin-bottom: 0px;">
                        <h3 class="panel-title" style="font-size: 18px; font-weight: 700; color: #1e293b;">Statistik Aktivitas Akademik</h3>
                        <select id="semesterToggle" class="form-control" style="width: auto; display: none; border-radius: 8px; font-size: 13px; color: #475569; border: 1px solid #cbd5e1; padding: 6px 30px 6px 12px; appearance: auto;">
                            <option value="current">Semester Ini</option>
                            <option value="last">Semester Lalu</option>
                        </select>
                    </div>
                    <!-- Skeleton -->
                    <div id="skeleton-chart" class="skeleton-pulse skeleton-chart" style="margin-top: 12px;"></div>
                    <!-- Real Chart -->
                    <div id="chart-wrapper" style="display:none; position: relative; height: 270px; width: 100%;">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-panel" style="height: 360px; margin-bottom: 20px; overflow-y: auto;">
                    <div class="panel-header">
                        <h3 class="panel-title"><i class="fa-solid fa-calendar-day"></i> Jadwal Hari Ini</h3>
                    </div>
                    <!-- Skeleton -->
                    <div id="skeleton-schedule">
                        <div class="skeleton-pulse skeleton-list-item"></div>
                        <div class="skeleton-pulse skeleton-list-item"></div>
                        <div class="skeleton-pulse skeleton-list-item" style="width:80%;"></div>
                    </div>
                    <!-- Real Content -->
                    <div id="schedule-wrapper" style="display:none;" class="panel-content-area"></div>
                </div>
            </div>
        </div>

        <!-- ========== ROW 3: LINIMASA + EARLY WARNING ========== -->
        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-panel" style="height: 750px; margin-bottom: 20px; overflow: hidden; display: flex; flex-direction: column;">
                    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 15px;">
                        <h3 class="panel-title"><i class="fa-solid fa-receipt"></i> Linimasa &amp; Peristiwa Akademik</h3>
                        <span style="background-color: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #cbd5e1;">Terbaru</span>
                    </div>

                    <!-- Early Warning Belum KRS Modal -->
<div class="modal fade" id="ewBelumKrsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #fef2f2; border-bottom: 1px solid #fecaca; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #b91c1c; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #b91c1c; font-size: 15px;"><i class="fa-solid fa-file-circle-xmark"></i> Mahasiswa Belum Daftar KRS</h5>
      </div>
      <div class="modal-body" id="ewBelumKrsModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #fecaca; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Approval Outline (tetap dipertahankan dari versi sebelumnya) -->
                    <?php if (!empty($upcoming)): ?>
                        <div id="upcoming-sidang-block" style="padding: 0 4px; margin-bottom: 24px;">
                            <h4 style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-bell" style="color: #dc2626;"></i> Agenda Sidang Mendatang
                            </h4>
                            <div class="row">
                                <?php foreach ($upcoming as $sidang): ?>
                                    <?php
                                        $kat_name = 'Sempro';
                                        if (isset($sidang->kategori_pengajuan)) {
                                            if ($sidang->kategori_pengajuan == 2) $kat_name = 'Semhas';
                                            if ($sidang->kategori_pengajuan == 3) $kat_name = 'Sidang';
                                        }
                                    ?>
                                    <div class="col-md-6" style="margin-bottom: 8px;">
                                        <div style="background: #f8f8f8; border: 1px solid #e2e8f0; padding: 12px 12px; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; height: 100%;">
                                            <div style="display: flex; align-items: center;">
                                                <?php if ($sidang->foto): ?>
                                                    <img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_<?= $sidang->foto ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 12px; border: 1px solid #e2e8f0;">
                                                <?php else: ?>
                                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #0f172a; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 14px;">
                                                        <?= substr($sidang->nm_pd, 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div style="font-size: 14px; font-weight: 600; color: #0f172a;">
                                                        <?= $sidang->nm_pd ?>
                                                        <span style="font-size: 11px; font-weight: 600; color: #dc2626; margin-left: 6px;"><?= $kat_name ?></span>
                                                    </div>
                                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                        <?= date('d M Y', strtotime($sidang->tanggal_seminar)) ?> &bull; <?= substr($sidang->jam, 0, 5) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!empty($sidang->link_dokumen)): ?>
                                                <a href="javascript:void(0)" onclick="showDocModal('<?= htmlspecialchars($sidang->link_dokumen, ENT_QUOTES) ?>', '<?= addslashes($kat_name) ?>', '<?= addslashes($sidang->nm_pd) ?>', '<?= addslashes($sidang->nipd) ?>')" style="display: inline-block; background: transparent; color: #dc2626; padding: 6px; font-size: 14px; cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#991b1b'" onmouseout="this.style.color='#dc2626'" title="Lihat Berkas">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Feed Skeleton -->
                    <div id="skeleton-feed" style="flex: 1; padding: 0 4px; overflow:hidden;">
                        <div class="skeleton-pulse skeleton-feed-item"></div>
                        <div class="skeleton-pulse skeleton-feed-item"></div>
                        <div class="skeleton-pulse skeleton-feed-item" style="width:90%;"></div>
                        <div class="skeleton-pulse skeleton-feed-item" style="width:85%;"></div>
                    </div>
                    <!-- Real Feed -->
                    <div style="flex: 1; overflow-y: auto; padding-right: 8px; display:none;" id="timeline-scroll-container">
                        <div id="timeline-container" class="custom-feed-list"></div>
                    </div>

                    <div id="load-more-status" class="text-center" style="padding: 10px 0; display: none;">
                        <i class="fa fa-spinner fa-spin fa-lg text-muted"></i><br>
                        <small class="text-muted">Memuat peristiwa lainnya...</small>
                    </div>
                    <div id="no-more-data" class="text-center text-muted" style="padding: 10px 0; display: none;">
                        <small>— Anda telah mencapai akhir linimasa —</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-panel" style="height: 750px; margin-bottom: 20px; overflow-y: auto;">
                    <div class="panel-header">
                        <h3 class="panel-title"><i class="fa-solid fa-triangle-exclamation"></i> Early Warning</h3>
                    </div>
                    <!-- Skeleton -->
                    <div id="skeleton-warning">
                        <div class="skeleton-pulse skeleton-warning"></div>
                        <div class="skeleton-pulse skeleton-warning"></div>
                        <div class="skeleton-pulse skeleton-warning"></div>
                        <div class="skeleton-pulse skeleton-warning" style="width:70%;"></div>
                    </div>
                    <!-- Real Content -->
                    <div id="warning-wrapper" style="display:none;" class="panel-content-area"></div>
                </div>
            </div>
        </div>

        <!-- ========== ROW 4: PROGRESS TA + ACADEMIC HEALTH ========== -->
        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-panel" style="height: 550px; margin-bottom: 20px; overflow-y: auto;">
                    <div class="panel-header">
                        <h3 class="panel-title"><i class="fa-solid fa-spinner"></i> Progress Tugas Akhir</h3>
                        <a href="<?= site_url('E_skripsi_bimbingan') ?>" class="btn btn-xs btn-link p-0">Detail</a>
                    </div>
                    <!-- Skeleton -->
                    <div id="skeleton-progress">
                        <div class="skeleton-pulse skeleton-table-row"></div>
                        <div class="skeleton-pulse skeleton-table-row"></div>
                        <div class="skeleton-pulse skeleton-table-row" style="width:85%;"></div>
                        <div class="skeleton-pulse skeleton-table-row" style="width:70%;"></div>
                    </div>
                    <!-- Real Content -->
                    <div id="progress-wrapper" style="display:none;" class="panel-content-area"></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-panel" style="height: 550px; margin-bottom: 20px; overflow-y: auto;">
                    <div class="panel-header" style="margin-bottom: 15px; align-items: flex-start;">
                        <div style="display: flex; flex-direction: column;">
                            <h3 class="panel-title" style="margin-bottom: 4px;"><i class="fa-solid fa-chart-pie"></i> Academic Health Insight</h3>
                            <span style="font-size: 11px; color: #64748b; font-weight: normal;">(Khusus pantauan mahasiswa 3 tahun pertama)</span>
                        </div>
                    </div>
                    <!-- Skeleton -->
                    <div id="skeleton-health">
                        <div class="skeleton-pulse skeleton-donut"></div>
                        <div class="skeleton-pulse skeleton-health-row"></div>
                        <div class="skeleton-pulse skeleton-health-row"></div>
                        <div class="skeleton-pulse skeleton-health-row" style="width:80%;"></div>
                    </div>
                    <!-- Real Content -->
                    <div id="health-wrapper" style="display:none;" class="panel-content-area">
                        <div class="row" style="margin: 0; display: flex; align-items: center;">
                            <div class="col-xs-6" style="padding-left: 0;">
                                <div style="position: relative; height: 140px; width: 100%;">
                                    <canvas id="healthChart"></canvas>
                                </div>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0; text-align: center; border-left: 1px solid #f0f2f5;">
                                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Perwalian</div>
                                <div id="health-total-perwalian" style="font-size: 32px; font-weight: 800; color: #1e293b; line-height: 1.2; margin-top: 5px;">0</div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 2px; margin-bottom: 8px;">Mahasiswa Aktif</div>
                                <div id="health-krs-info" style="display:flex; flex-direction:column; gap:4px; align-items:center;">
                                    <div style="font-size:10px; background:#fee2e2; color:#b91c1c; padding:2px 6px; border-radius:4px; display:none;" id="badge-belum-krs"><span id="health-belum-krs" style="font-weight:bold;">0</span> Belum KRS</div>
                                    <div style="font-size:10px; background:#fef9c3; color:#a16207; padding:2px 6px; border-radius:4px; display:none;" id="badge-pending-krs"><span id="health-pending-krs" style="font-weight:bold;">0</span> KRS Pending</div>
                                </div>
                            </div>
                        </div>
                        <div id="health-legend" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.dashboard-wrapper -->
</div><!-- /.page-content -->


<!-- =====================================================================
     MODALS (tetap di sini, di-populate dari JS setelah AJAX)
     ===================================================================== -->

<!-- Progress Detail Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #64748b; opacity: 1;"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title" id="progressModalTitle" style="font-weight: 700; color: #1e293b; font-size: 15px;">Detail Mahasiswa</h5>
      </div>
      <div class="modal-body" id="progressModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;"></div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Early Warning Tahun 1 Modal -->
<div class="modal fade" id="ewThn1Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #fef2f2; border-bottom: 1px solid #fecaca; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #ef4444; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #ef4444; font-size: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> Early Warning — Tahun 1 (IPK &lt; 2.75)</h5>
      </div>
      <div class="modal-body" id="ewThn1ModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #fecaca; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Early Warning Tahun 2 Modal -->
<div class="modal fade" id="ewThn2Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #fff7ed; border-bottom: 1px solid #fed7aa; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #f97316; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #f97316; font-size: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> Early Warning — Tahun 2 (IPK &lt; 2.75)</h5>
      </div>
      <div class="modal-body" id="ewThn2ModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #fed7aa; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Early Warning Tahun 3 Modal -->
<div class="modal fade" id="ewThn3Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #fffbeb; border-bottom: 1px solid #fde68a; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #f59e0b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #f59e0b; font-size: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> Early Warning — Tahun 3 (IPK &lt; 2.75)</h5>
      </div>
      <div class="modal-body" id="ewThn3ModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #fde68a; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Early Warning IPS Drop Modal -->
<div class="modal fade" id="ewIpsDropModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f5f3ff; border-bottom: 1px solid #ddd6fe; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #8b5cf6; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #8b5cf6; font-size: 15px;"><i class="fa-solid fa-arrow-trend-down"></i> Penurunan IPS Signifikan (≥ 0.2)</h5>
      </div>
      <div class="modal-body" id="ewIpsDropModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #ddd6fe; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- No Progress TA Modal -->
<div class="modal fade" id="noProgressTaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 15px;">Mahasiswa - Belum Ada Progress TA</h5>
      </div>
      <div class="modal-body" id="noProgressTaModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- No Progress Sempro Modal -->
<div class="modal fade" id="noProgressSemproModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 15px;">Mahasiswa - Sempro Layak, Belum Semhas</h5>
      </div>
      <div class="modal-body" id="noProgressSemproModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- No Progress Semhas Modal -->
<div class="modal fade" id="noProgressSemhasModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #1e293b; font-size: 15px;">Mahasiswa - Semhas Layak, Belum Sidang</h5>
      </div>
      <div class="modal-body" id="noProgressSemhasModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;">
        <div class="text-center text-muted" style="padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1100px; width: 90%;">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" id="docModalTitle" style="font-weight: 700; color: #1e293b; font-size: 15px;">Berkas Dokumen Sidang</h5>
      </div>
      <div class="modal-body" style="padding: 0; height: 75vh; position: relative;">
        <iframe id="docIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        <div id="folderWarning" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f8fafc; align-items: center; justify-content: center; flex-direction: column; text-align: center; padding: 20px;">
            <i class="fa-solid fa-folder-open" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px;"></i>
            <h4 style="font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 4px;">Mahasiswa Membagikan Folder</h4>
            <div id="folderWarningStudentName" style="font-size: 12px; font-weight: 600; color: #b91c1c; background: #fee2e2; padding: 4px 10px; border-radius: 12px; margin-bottom: 12px; display: inline-block;"></div>
            <p style="color: #64748b; font-size: 13px; max-width: 400px;">Sistem keamanan Google Drive tidak mengizinkan pratinjau seluruh folder di dalam modal ini.</p>
            <a href="#" id="folderDirectLink" target="_blank" class="btn btn-primary" style="margin-top: 10px; border-radius: 6px; font-weight: 600;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Folder</a>
        </div>
      </div>
      <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 10px 20px; display: flex; justify-content: space-between;">
          <a href="#" id="docDirectLink" target="_blank" class="btn btn-danger" style="border-radius: 6px; font-weight: 600;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka di Tab Baru</a>
          <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Detail Mengajar Modal -->
<div class="modal fade" id="mengajarModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" id="mengajarModalTitle" style="font-weight: 700; color: #1e293b; font-size: 15px;">Detail Mengajar</h5>
      </div>
      <div class="modal-body" id="mengajarModalBody" style="padding: 20px;">
        <div style="text-align: center; padding: 30px;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #94a3b8; margin-bottom: 10px;"></i><div style="color: #64748b; font-size: 13px;">Memuat data...</div></div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Health Modals (Aman / Perhatian / Risiko) — diisi dari JS -->
<div class="modal fade" id="amanModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #15803d; font-size: 15px;"><i class="fa-solid fa-circle text-success" style="margin-right:6px;"></i>Mahasiswa Aman</h5>
      </div>
      <div class="modal-body" id="amanModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;"><div class="text-center text-muted p-3"><i class="fa fa-spinner fa-spin"></i></div></div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button></div>
    </div>
  </div>
</div>
<div class="modal fade" id="perhatianModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #c2410c; font-size: 15px;"><i class="fa-solid fa-circle text-warning" style="margin-right:6px;"></i>Perlu Perhatian</h5>
      </div>
      <div class="modal-body" id="perhatianModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;"><div class="text-center text-muted p-3"><i class="fa fa-spinner fa-spin"></i></div></div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button></div>
    </div>
  </div>
</div>
<div class="modal fade" id="risikoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0;">
        <button type="button" class="close" data-dismiss="modal" style="color: #64748b; opacity: 1;"><span>&times;</span></button>
        <h5 class="modal-title" style="font-weight: 700; color: #b91c1c; font-size: 15px;"><i class="fa-solid fa-circle text-danger" style="margin-right:6px;"></i>Risiko Tinggi</h5>
      </div>
      <div class="modal-body" id="risikoModalBody" style="padding: 20px; max-height: 65vh; overflow-y: auto;"><div class="text-center text-muted p-3"><i class="fa fa-spinner fa-spin"></i></div></div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 12px 20px;"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; background: #e2e8f0; color: #475569; border: none; font-weight: 600;">Tutup</button></div>
    </div>
  </div>
</div>


<!-- =====================================================================
     JAVASCRIPT — Progressive Loader + Dashboard Logic
     ===================================================================== -->
<script>
(function () {
    'use strict';

    // --------------------------------------------------------
    // PROGRESS BAR CONTROLLER
    // --------------------------------------------------------


    // --------------------------------------------------------
    // PROGRESS BAR CONTROLLER
    // --------------------------------------------------------
    var STEPS = {
        kpi:     { pct: 15, chip: 'kpi',     lbl: 'lbl-kpi',     label: 'Inisialisasi KPI...' },
        chart:   { pct: 40, chip: 'chart',   lbl: 'lbl-chart',   label: 'Memuat chart & jadwal...' },
        warning: { pct: 65, chip: 'warning', lbl: 'lbl-warning', label: 'Menganalisis early warning...' },
        health:  { pct: 85, chip: 'health',  lbl: 'lbl-health',  label: 'Menghitung academic health...' },
        feed:    { pct: 100, chip: 'feed',   lbl: 'lbl-feed',    label: 'Memuat linimasa...' },
    };

    var currentPct = 0;

    function setProgress(step) {
        var s = STEPS[step];
        var bar   = document.getElementById('init-progress-bar');
        var pctEl = document.getElementById('init-progress-pct');
        var txtEl = document.getElementById('init-status-text');

        currentPct = s.pct;
        if (bar)   bar.style.width = currentPct + '%';
        if (pctEl) pctEl.textContent = currentPct + '%';
        if (txtEl) txtEl.textContent = s.label;

        // Update labels
        var steps = ['kpi', 'chart', 'warning', 'health', 'feed'];
        var idx   = steps.indexOf(step);
        steps.forEach(function (k, i) {
            var lbl  = document.getElementById('lbl-' + k);
            var chip = document.getElementById('chip-' + k);
            if (!lbl || !chip) return;
            if (i < idx) {
                lbl.className  = 'init-step-label done';
                chip.className = 'init-step-chip done';
            } else if (i === idx) {
                lbl.className  = 'init-step-label active';
                chip.className = 'init-step-chip loading';
            } else {
                lbl.className  = 'init-step-label';
                chip.className = 'init-step-chip';
            }
        });
    }

    function dismissOverlay() {
        var overlay = document.getElementById('init-loading-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
            setTimeout(function () { overlay.style.display = 'none'; }, 700);
        }
    }

    // --------------------------------------------------------
    // GLOBALS (chart instances etc.)
    // --------------------------------------------------------
    var activityChart = null;
    var healthChart   = null;
    var groupedProgressData = {};
    var middleData = {};
    var healthData = {};

    // infinite scroll state
    var feedOffset  = 0;
    var feedLoading = false;
    var feedAllDone = false;
    var feedLastDate = '';

    // --------------------------------------------------------
    // WAIT FOR LIBRARIES
    // --------------------------------------------------------
    function waitForLibrary(cb) {
        if (window.Chart && window.jQuery) { cb(); }
        else { setTimeout(function () { waitForLibrary(cb); }, 80); }
    }

    waitForLibrary(function () {
        var $ = window.jQuery;

        // =====================================================
        // LOAD DATA: tampilkan overlay dengan staged delays
        // =====================================================
        
        // KPI is already rendered — mark as done immediately
        setProgress('kpi');

        // ----- PHASE 1: Chart + Jadwal (~250ms) -----
        setTimeout(function () {
            setProgress('chart');
            $.ajax({
                url: '<?= site_url("C_wall/ajax_load_panel_top") ?>',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    renderChart(data.chart, $);
                    renderSchedules(data.schedules, $);
                    document.getElementById('chip-chart').className = 'init-step-chip done';
                    document.getElementById('lbl-chart').className  = 'init-step-label done';
                },
                error: function () {
                    $('#chart-wrapper').html('<div class="alert alert-warning" style="font-size:12px;">Gagal memuat chart.</div>').show();
                    $('#skeleton-chart').hide();
                    $('#semesterToggle').show();
                }
            });
        }, 250);

        // ----- PHASE 2: Early Warning + Progress TA (~600ms) -----
        setTimeout(function () {
            setProgress('warning');
            $.ajax({
                url: '<?= site_url("C_wall/ajax_load_panel_middle") ?>',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    middleData = data;
                    groupedProgressData = data.grouped_progress || {};
                    window._gpd = groupedProgressData;
                    renderWarningPanel(data, $);
                    renderProgressPanel(data, $);
                    document.getElementById('chip-warning').className = 'init-step-chip done';
                    document.getElementById('lbl-warning').className  = 'init-step-label done';
                },
                error: function () {
                    $('#warning-wrapper').html('<div class="alert alert-warning" style="font-size:12px;">Gagal memuat data.</div>').show();
                    $('#skeleton-warning').hide();
                }
            });
        }, 600);

        // ----- PHASE 3: Academic Health (~950ms) -----
        setTimeout(function () {
            setProgress('health');
            $.ajax({
                url: '<?= site_url("C_wall/ajax_load_panel_health") ?>',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    healthData = data;
                    renderHealthPanel(data, $);
                    document.getElementById('chip-health').className = 'init-step-chip done';
                    document.getElementById('lbl-health').className  = 'init-step-label done';
                },
                error: function () {
                    $('#health-wrapper').html('<div class="alert alert-warning" style="font-size:12px;">Gagal memuat data.</div>').show();
                    $('#skeleton-health').hide();
                }
            });
        }, 950);

        // ----- PHASE 4: Events Feed (~1200ms) -----
        setTimeout(function () {
            setProgress('feed');
            loadFeedChunk($, function () {
                document.getElementById('chip-feed').className = 'init-step-chip done';
                document.getElementById('lbl-feed').className  = 'init-step-label done';
                setTimeout(dismissOverlay, 400);
            });
        }, 1200);

        // --------------------------------------------------------
        // RENDER FUNCTIONS
        // --------------------------------------------------------

        function renderChart(chartData, $) {
            $('#skeleton-chart').hide();
            $('#chart-wrapper').show();
            $('#semesterToggle').show();

            var ctx = document.getElementById('activityChart').getContext('2d');
            activityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels || [],
                    datasets: [
                        {
                            label: 'Realisasi Mengajar',
                            data: chartData.mengajar || [],
                            backgroundColor: 'rgba(54, 162, 235, 0.85)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Bimbingan TA',
                            data: chartData.bimbingan || [],
                            backgroundColor: 'rgba(255, 99, 132, 0.85)',
                            borderColor: 'rgb(255, 99, 132)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Seminar & Sidang',
                            data: chartData.sidang || [],
                            backgroundColor: 'rgba(255, 159, 64, 0.85)',
                            borderColor: 'rgb(255, 159, 64)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: false, boxWidth: 24, boxHeight: 12, padding: 20,
                                font: { size: 12, family: "'Segoe UI', sans-serif", weight: '500' },
                                color: '#4b5563'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { color: '#6b7280', font: { size: window.innerWidth < 1400 ? 10 : 11, family: "'Segoe UI', sans-serif" }, padding: 10 }
                        },
                        x: {
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { 
                                color: '#6b7280', 
                                font: { size: window.innerWidth < 1400 ? 9 : 11, family: "'Segoe UI', sans-serif" }, 
                                padding: window.innerWidth < 1400 ? 5 : 10,
                                callback: function(value, index, ticks) {
                                    if (index % 2 !== 0) return ''; // Selalu selang-seling di semua ukuran layar
                                    
                                    var label = this.getLabelForValue(value);
                                    if (window.innerWidth < 1400) {
                                        return Array.isArray(label) ? label[0] : label;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Semester toggle
            $('#semesterToggle').off('change').on('change', function () {
                var mode = $(this).val();
                $(this).prop('disabled', true);
                $.ajax({
                    url: '<?= site_url("C_wall/ajax_get_chart_data") ?>',
                    type: 'GET', data: { mode: mode }, dataType: 'json',
                    success: function (resp) {
                        if (resp.labels) activityChart.data.labels = resp.labels;
                        activityChart.data.datasets[0].data = resp.mengajar;
                        activityChart.data.datasets[1].data = resp.bimbingan;
                        activityChart.data.datasets[2].data = resp.sidang;
                        activityChart.update();
                    },
                    complete: function () { $('#semesterToggle').prop('disabled', false); }
                });
            });
        }

        function renderSchedules(schedules, $) {
            var html = '';
            if (schedules && schedules.length > 0) {
                schedules.forEach(function (s) {
                    var actionBtn = '';
                    if (s.type === 'Perkuliahan') {
                        actionBtn = '<button onclick="showMengajarModal(\'' + s.id_jadwal + '\', \'' + (s.title || '').replace(/'/g, "\\'") + '\')" style="background: none; border: 1px solid #94a3b8; color: #475569; border-radius: 6px; padding: 4px 8px; font-size: 11px; cursor: pointer;" title="Detail Mengajar"><i class="fa-solid fa-users"></i></button>';
                    } else if (s.link_dokumen) {
                        var katNameJS = s.type ? s.type.replace(/'/g, "\\'") : '';
                        var nmPdJS = s.nm_pd ? s.nm_pd.replace(/'/g, "\\'") : '';
                        var nipdJS = s.nipd ? s.nipd.replace(/'/g, "\\'") : '';
                        actionBtn = '<a href="javascript:void(0)" onclick="showDocModal(\'' + s.link_dokumen + '\', \'' + katNameJS + '\', \'' + nmPdJS + '\', \'' + nipdJS + '\')" style="display: inline-block; background: none; border: 1px solid #fca5a5; color: #ef4444; border-radius: 6px; padding: 4px 8px; font-size: 11px;" title="Lihat Berkas"><i class="fa-solid fa-file-pdf"></i></a>';
                    }
                    html += '<div class="item-row schedule-item-row">';
                    html += '<div class="item-details schedule-item-details" style="max-width: 60%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">';
                    html += '<strong title="' + (s.title || '') + '">' + (s.title || '') + '</strong><br>';
                    html += '<span>' + (s.time || '') + '</span>';
                    html += '</div>';
                    html += '<div style="display: flex; gap: 8px; align-items: center;">';
                    html += '<span class="badge-premium ' + (s.badge_class || '') + '">' + (s.location || '') + '</span>';
                    html += actionBtn;
                    html += '</div></div>';
                });
            } else {
                html = '<div style="text-align: center; padding: 40px 20px; color: #8a909d;"><i class="fa-solid fa-calendar-check" style="font-size: 36px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i><p style="font-size: 13px; font-weight: 500; margin: 0; line-height: 1.4;">Tidak ada jadwal mengajar atau agenda sidang hari ini.</p></div>';
            }
            $('#skeleton-schedule').hide();
            $('#schedule-wrapper').html(html).show();
        }

        function renderWarningPanel(data, $) {
            var html = '';

            var thn1Year = data.ew_thn1_year || '';
            var thn2Year = data.ew_thn2_year || '';
            var thn3Year = data.ew_thn3_year || '';

            // Helper: warning-box with custom accent
            function makeEwCard(color, titleHtml, count, desc, btnLabel, modalFn) {
                var borderColor = color;

                var c = '<div class="warning-box">';
                c += '<strong style="display: block; margin-bottom: 6px;">' + titleHtml.replace('<i', '<i style="color:' + borderColor + ';"') + '</strong>';
                c += '<p>' + desc + '</p>';
                if (count > 0) {
                    c += '<button type="button" class="btn btn-xs" style="background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; font-weight: 600; border-radius: 6px; padding: 4px 10px; transition: all 0.2s;" onmouseover="this.style.background=\'#e2e8f0\'" onmouseout="this.style.background=\'#f1f5f9\'" onclick="' + modalFn + '()">' + btnLabel + '</button>';
                }
                c += '</div>';
                return c;
            }

            // ── Tier 1: Tahun Pertama (angkatan thn-1) ──
            html += makeEwCard(
                '#ef4444',
                '<i class="fa-solid fa-triangle-exclamation" style="margin-right:5px;"></i> Angkatan ' + thn1Year + ' — IPK < 2.75',
                data.ew_thn1_count || 0,
                (data.ew_thn1_count || 0) + ' mahasiswa angkatan ' + thn1Year + ' (Tahun 1) memiliki IPK di bawah 2.75.',
                'Lihat Detail',
                'showEwThn1Modal'
            );

            // ── Tier 2: Tahun Kedua (angkatan thn-2) ──
            html += makeEwCard(
                '#f97316',
                '<i class="fa-solid fa-triangle-exclamation" style="margin-right:5px;"></i> Angkatan ' + thn2Year + ' — IPK < 2.75',
                data.ew_thn2_count || 0,
                (data.ew_thn2_count || 0) + ' mahasiswa angkatan ' + thn2Year + ' (Tahun 2, Sem 3–4) memiliki IPK di bawah 2.75.',
                'Lihat Detail',
                'showEwThn2Modal'
            );

            // ── Tier 3: Tahun Ketiga (angkatan thn-3) ──
            html += makeEwCard(
                '#f59e0b',
                '<i class="fa-solid fa-triangle-exclamation" style="margin-right:5px;"></i> Angkatan ' + thn3Year + ' — IPK < 2.75',
                data.ew_thn3_count || 0,
                (data.ew_thn3_count || 0) + ' mahasiswa angkatan ' + thn3Year + ' (Tahun 3, Sem 5–6) memiliki IPK di bawah 2.75.',
                'Lihat Detail',
                'showEwThn3Modal'
            );

            // ── IPS Drop Ekstrim ──
            html += makeEwCard(
                '#8b5cf6',
                '<i class="fa-solid fa-arrow-trend-down" style="margin-right:5px;"></i> Penurunan IPS Signifikan',
                data.ew_ips_drop_count || 0,
                (data.ew_ips_drop_count || 0) + ' mahasiswa mengalami penurunan IPS ≥ 0.2 poin yang berdampak pada penurunan IPK.',
                'Lihat Detail',
                'showEwIpsDropModal'
            );

            // ── Belum Isi KRS ──
            html += makeEwCard(
                '#b91c1c',
                '<i class="fa-solid fa-file-circle-xmark" style="margin-right:5px;"></i> Belum Daftar KRS',
                data.ew_belum_krs_count || 0,
                (data.ew_belum_krs_count || 0) + ' mahasiswa belum mengisi atau menyelesaikan pendaftaran KRS semester ini.',
                'Lihat Detail',
                'showEwBelumKrsModal'
            );

            $('#skeleton-warning').hide();
            $('#warning-wrapper').html(html).show();

            // Populate modal bodies
            populateWarningModal('ewThn1ModalBody',    data.ew_thn1_list,     'ew_ipk');
            populateWarningModal('ewThn2ModalBody',    data.ew_thn2_list,     'ew_ipk');
            populateWarningModal('ewThn3ModalBody',    data.ew_thn3_list,     'ew_ipk');
            populateWarningModal('ewIpsDropModalBody', data.ew_ips_drop_list, 'ew_ips_drop');
            populateWarningModal('ewBelumKrsModalBody', data.ew_belum_krs_list, 'ew_belum_krs');
        }

        function formatSmt(id) {
            if (!id) return '-';
            var s = String(id);
            var yr = s.substring(0, 4);
            var sem = s.substring(4);
            return yr + (sem === '1' ? ' Ganjil' : ' Genap');
        }

        function populateWarningModal(bodyId, list, type) {
            var el = document.getElementById(bodyId);
            if (!el) return;
            if (!list || list.length === 0) {
                el.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b; font-size: 13px;">Tidak ada data.</div>';
                return;
            }
            var html = '<ul class="list-group" style="border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0;">';
            list.forEach(function (mhs, index) {
                html += '<li class="list-group-item" style="border-bottom: 1px solid #e2e8f0; padding: 12px; display: flex; align-items: center; gap: 15px; border-left: none; border-right: none; border-top: none;">';
                var urut = index + 1;
                html += '<div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 1px solid #e2e8f0; flex-shrink: 0;">' + urut + '</div>';
                html += '<div style="flex: 1; overflow: hidden;">';
                html += '<div style="font-weight: 700; color: #1e293b; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + (mhs.nm_pd || '-') + '</div>';
                html += '<div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 1px;">' + (mhs.nipd || '') + '</div>';
                if (type === 'ta_progress' && mhs.judul_ditetapkan) {
                    var j = mhs.judul_ditetapkan;
                    if (j.length > 60) j = j.substring(0, 60) + '...';
                    html += '<div style="font-size: 10px; color: #94a3b8; font-style: italic; margin-top: 4px; line-height: 1.2;">' + j + '</div>';
                }
                html += '</div>';

                // ── ew_ipk: IPK current + semester count ──
                if (type === 'ew_ipk' && mhs.ipk_curr !== undefined) {
                    var ipkVal = mhs.ipk_curr !== null ? parseFloat(mhs.ipk_curr).toFixed(2) : '-';
                    var semCount = mhs.total_smt || '-';
                    var ipkColor = (parseFloat(mhs.ipk_curr) < 2.75) ? '#ef4444' : '#22c55e';
                    html += '<div style="text-align:right; min-width:90px;">';
                    html += '<div style="font-size:8px; color:#94a3b8; font-weight:600; text-transform:uppercase;">IPK</div>';
                    html += '<div style="font-size:18px; font-weight:800; color:' + ipkColor + ';">' + ipkVal + '</div>';
                    html += '<div style="font-size:10px; color:#64748b;">' + semCount + ' Semester</div>';
                    html += '</div>';
                }
                // ── ew_ips_drop: IPS prev→curr + IPK prev→curr ──
                else if (type === 'ew_ips_drop') {
                    var ipsP = mhs.ips_prev !== null ? parseFloat(mhs.ips_prev).toFixed(2) : '-';
                    var ipsC = mhs.ips_curr !== null ? parseFloat(mhs.ips_curr).toFixed(2) : '-';
                    var ipkP = mhs.ipk_prev !== null ? parseFloat(mhs.ipk_prev).toFixed(2) : '-';
                    var ipkC = mhs.ipk_curr !== null ? parseFloat(mhs.ipk_curr).toFixed(2) : '-';
                    var ipsDrop = (mhs.ips_prev !== null && mhs.ips_curr !== null) ? (parseFloat(mhs.ips_prev) - parseFloat(mhs.ips_curr)).toFixed(2) : '';


                    var lblPrev = formatSmt(mhs.smt_prev);
                    var lblCurr = formatSmt(mhs.smt_curr);

                    html += '<div style="text-align:right; min-width:195px;">';
                    html += '<div style="display:flex; justify-content:flex-end; align-items:stretch; gap:0;">';

                    // Row labels column
                    html += '<div style="display:flex; flex-direction:column; justify-content:space-around; text-align:right; padding-right:6px; border-right:1px solid #e2e8f0; margin-right:6px;">';
                    html += '<div style="font-size:8px; color:#94a3b8; font-weight:600; height:20px; display:flex; align-items:center; justify-content:flex-end;">' + lblPrev + '</div>';
                    html += '<div style="font-size:8px; color:#ef4444; font-weight:600; height:20px; display:flex; align-items:center; justify-content:flex-end;">' + lblCurr + '</div>';
                    html += '</div>';

                    // IPS values column
                    html += '<div style="text-align:center; min-width:42px; margin-right:4px;">';
                    html += '<div style="font-size:8px; color:#64748b; font-weight:700; margin-bottom:2px;">IPS</div>';
                    html += '<div style="font-size:12px; font-weight:700; color:#1e293b; line-height:20px;">' + ipsP + '</div>';
                    html += '<div style="font-size:12px; font-weight:800; color:#ef4444; line-height:20px;">' + ipsC + '</div>';
                    html += '</div>';

                    // Arrow + ΔIPS drop badge
                    html += '<div style="display:flex; flex-direction:column; justify-content:center; align-items:center; margin:0 4px; min-width:38px;">';
                    html += '<div style="font-size:7px; color:#94a3b8; font-weight:700; letter-spacing:0.5px; margin-bottom:2px;">ΔIPS</div>';
                    html += '<i class="fa-solid fa-arrow-trend-down" style="color:#ef4444; font-size:12px;"></i>';
                    if (ipsDrop) html += '<div style="font-size:10px; color:#ef4444; font-weight:800; margin-top:1px;">-' + ipsDrop + '</div>';
                    html += '</div>';

                    // IPK values column
                    html += '<div style="text-align:center; min-width:42px;">';
                    html += '<div style="font-size:8px; color:#64748b; font-weight:700; margin-bottom:2px;">IPK</div>';
                    html += '<div style="font-size:12px; font-weight:700; color:#1e293b; line-height:20px;">' + ipkP + '</div>';
                    html += '<div style="font-size:12px; font-weight:800; color:#ef4444; line-height:20px;">' + ipkC + '</div>';
                    html += '</div>';

                    html += '</div>'; // flex row
                    html += '</div>'; // outer
                } else if (type === 'ew_belum_krs') {
                    var ang = mhs.angkatan_2d ? 'Angkatan 20' + mhs.angkatan_2d : '-';
                    var statKrs = mhs.id_status_krs ? mhs.id_status_krs : 'Belum Isi';
                    html += '<div style="text-align:right; min-width:90px;">';
                    html += '<div style="font-size:8px; color:#94a3b8; font-weight:600; text-transform:uppercase;">Status</div>';
                    html += '<div style="font-size:12px; font-weight:800; color:#ef4444; margin-top:2px;">Belum KRS</div>';
                    html += '<div style="font-size:10px; color:#64748b; margin-top:2px;">' + ang + '</div>';
                    html += '</div>';
                } else if (type === 'ta_progress') {
                    var tglFmt = mhs.tgl_penetapan_format || '-';
                    var umurStr = '';
                    if (mhs.umur_tahun > 0) umurStr += mhs.umur_tahun + ' thn ';
                    if (mhs.umur_bulan > 0) umurStr += mhs.umur_bulan + ' bln ';
                    umurStr += (mhs.umur_hari || 0) + ' hr';
                    html += '<div style="text-align: right; min-width: 140px;">';
                    html += '<div style="font-size: 10px; color: #64748b; font-weight: 600;">Tgl Ref</div>';
                    html += '<div style="font-size: 12px; font-weight: 700; color: #1e293b;">' + tglFmt + '</div>';
                    html += '<div style="font-size: 10px; color: #ef4444; font-weight: 600; margin-top: 3px;"><i class="fa-solid fa-clock"></i> ' + umurStr + '</div>';
                    html += '</div>';
                }
                html += '</li>';
            });
            html += '</ul>';
            el.innerHTML = html;
        }

        function renderProgressPanel(data, $) {
            var gp = data.grouped_progress || {};
            var html = '';
            var hasData = false;

            html += '<div class="table-responsive"><table class="table table-premium align-middle" style="margin: 0;">';
            html += '<thead><tr><th>Mahasiswa</th><th>Progress</th><th>Status</th></tr></thead><tbody>';

            var order = ['100', '75', '45', '20'];
            order.forEach(function (pv) {
                var grp = gp[pv];
                if (!grp || !grp.students || grp.students.length === 0) return;
                hasData = true;
                var cnt = grp.students.length;
                html += '<tr style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'transparent\'" onclick="showProgressModal(\'' + pv + '\')">';
                html += '<td><div style="font-weight: 700; color: #2c3e50; font-size: 13px;"><i class="fa-solid fa-users" style="color: #64748b; margin-right: 6px;"></i>' + cnt + ' Mahasiswa</div><div style="font-size: 10px; color: #8a909d; margin-top: 2px;">Klik untuk lihat detail</div></td>';
                html += '<td style="min-width: 90px; vertical-align: middle;"><div class="progress-bar-custom"><div class="progress-bar-fill ' + grp.color + '" style="width: ' + pv + '%;"></div></div><span style="font-size: 10px; font-weight: bold; color: #8a909d;">' + pv + '%</span></td>';
                html += '<td style="vertical-align: middle;"><span class="badge-premium ' + grp.badge + '" style="font-size: 9px; padding: 3px 6px; display: inline-block; white-space: nowrap;">' + grp.label + '</span></td>';
                html += '</tr>';
            });

            if (!hasData) {
                html += '<tr><td colspan="3" class="text-center text-muted" style="padding: 20px;">Belum ada mahasiswa bimbingan aktif.</td></tr>';
            }
            html += '</tbody></table></div>';

            $('#skeleton-progress').hide();
            $('#progress-wrapper').html(html).show();
        }

        function renderHealthPanel(data, $) {
            var pct    = data.health_pct || { aman: 0, perhatian: 0, risiko: 0 };
            var counts = data.health_counts || [0, 0, 0];
            var amanList     = data.aman_list || [];
            var perhatianList = data.perhatian_list || [];
            var risikoList   = data.risiko_list || [];
            
            var totalPerwalian = counts.reduce(function(a, b) { return a + b; }, 0);
            var elTotal = document.getElementById('health-total-perwalian');
            if (elTotal) elTotal.innerText = totalPerwalian;

            var allMhs = [].concat(amanList, perhatianList, risikoList);
            var belumKrsCount = 0;
            var pendingKrsCount = 0;
            allMhs.forEach(function(m) {
                if (m.id_status_krs === null || m.id_status_krs === undefined) {
                    belumKrsCount++;
                } else if ([1, 2].indexOf(parseInt(m.id_status_krs)) !== -1) {
                    pendingKrsCount++;
                }
            });

            if (belumKrsCount > 0) {
                $('#health-belum-krs').text(belumKrsCount);
                $('#badge-belum-krs').show();
            } else {
                $('#badge-belum-krs').hide();
            }

            if (pendingKrsCount > 0) {
                $('#health-pending-krs').text(pendingKrsCount);
                $('#badge-pending-krs').show();
            } else {
                $('#badge-pending-krs').hide();
            }

            var healthCtx = document.getElementById('healthChart');
            if (!healthCtx) return;

            healthChart = new Chart(healthCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Aman', 'Perhatian', 'Risiko'],
                    datasets: [{
                        label: 'Mahasiswa',
                        data: counts,
                        backgroundColor: ['rgba(34, 197, 94, 0.85)', 'rgba(249, 115, 22, 0.85)', 'rgba(239, 68, 68, 0.85)'],
                        borderColor: ['#22c55e', '#f97316', '#ef4444'],
                        borderWidth: 1, borderRadius: 8, barThickness: 24
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f0f2f5' }, ticks: { color: '#8a909d', font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { color: '#8a909d', font: { size: 10 } } }
                    }
                }
            });

            // Legend
            var legend = '<div style="padding: 10px 0; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">';
            legend += '<div><div style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 3px;"><i class="fa-solid fa-circle text-success" style="margin-right: 6px;"></i> Mahasiswa Aman</div><div style="font-size: 11px; color: #64748b; padding-left: 20px;">IPS stabil / naik (&ge; 2.75)</div></div>';
            legend += '<div style="display: flex; align-items: center; gap: 15px;"><strong style="color: #22c55e; font-size: 16px;">' + pct.aman + '%</strong><button type="button" class="btn btn-xs" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; border-radius: 6px; padding: 3px 8px;" onclick="showAmanModal()"><i class="fa-solid fa-users" style="margin-right: 4px;"></i>' + amanList.length + ' Detail</button></div>';
            legend += '</div>';
            legend += '<div style="padding: 10px 0; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">';
            legend += '<div><div style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 3px;"><i class="fa-solid fa-circle text-warning" style="margin-right: 6px;"></i> Perlu Perhatian</div><div style="font-size: 11px; color: #64748b; padding-left: 20px;">IPS menurun atau &lt; 2.75</div></div>';
            legend += '<div style="display: flex; align-items: center; gap: 15px;"><strong style="color: #f97316; font-size: 16px;">' + pct.perhatian + '%</strong><button type="button" class="btn btn-xs" style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; font-weight: 600; border-radius: 6px; padding: 3px 8px;" onclick="showPerhatianModal()"><i class="fa-solid fa-users" style="margin-right: 4px;"></i>' + perhatianList.length + ' Detail</button></div>';
            legend += '</div>';
            legend += '<div style="padding: 10px 0; display: flex; justify-content: space-between; align-items: center;">';
            legend += '<div><div style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 3px;"><i class="fa-solid fa-circle text-danger" style="margin-right: 6px;"></i> Risiko Tinggi</div><div style="font-size: 11px; color: #64748b; padding-left: 20px;">IPS sangat rendah (&lt; 2.0)</div></div>';
            legend += '<div style="display: flex; align-items: center; gap: 15px;"><strong style="color: #ef4444; font-size: 16px;">' + pct.risiko + '%</strong><button type="button" class="btn btn-xs" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; font-weight: 600; border-radius: 6px; padding: 3px 8px;" onclick="showRisikoModal()"><i class="fa-solid fa-users" style="margin-right: 4px;"></i>' + risikoList.length + ' Detail</button></div>';
            legend += '</div>';

            $('#health-legend').html(legend);
            $('#skeleton-health').hide();
            $('#health-wrapper').show();

            // Populate health modals
            populateHealthModal('amanModalBody', amanList, 'IPS stabil / naik (≥ 2.75). Termasuk mahasiswa baru yang datanya mungkin belum muncul.');
            populateHealthModal('perhatianModalBody', perhatianList, 'IPS menurun dari semester sebelumnya, atau nilainya kurang dari 2.75.');
            populateHealthModal('risikoModalBody', risikoList, 'IPS berada pada kategori sangat rendah (< 2.0).');
        }

        function populateHealthModal(bodyId, list, desc) {
            var el = document.getElementById(bodyId);
            if (!el) return;
            var html = '<div style="background: #f8fafc; padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; font-size: 12px; color: #475569; border-left: 4px solid #cbd5e1; line-height: 1.5;">' + desc + '</div>';
            if (!list || list.length === 0) {
                html += '<div style="padding: 20px; text-align: center; color: #64748b; font-size: 13px;">Tidak ada mahasiswa di kategori ini.</div>';
                el.innerHTML = html;
                return;
            }
            html += '<ul class="list-group" style="border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0;">';
            list.forEach(function (mhs, index) {
                html += '<li class="list-group-item" style="border-bottom: 1px solid #e2e8f0; padding: 12px; display: flex; align-items: center; gap: 15px; border-left: none; border-right: none; border-top: none;">';
                var urut = index + 1;
                html += '<div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; border: 1px solid #e2e8f0; flex-shrink: 0;">' + urut + '</div>';
                html += '<div style="flex: 1; overflow: hidden;">';
                html += '<div style="font-weight: 700; color: #1e293b; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + (mhs.nm_pd || '-') + '</div>';
                html += '<div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 1px;">' + (mhs.nipd || '') + '</div>';

                // KRS badge
                var krsHtml = '';
                if (mhs.id_status_krs === null || mhs.id_status_krs === undefined) {
                    krsHtml = '<span class="badge" style="background:#fee2e2; color:#b91c1c; font-size:9px; padding: 3px 6px;">Belum Isi KRS</span>';
                } else if ([1, 2].indexOf(parseInt(mhs.id_status_krs)) !== -1) {
                    krsHtml = '<span class="badge" style="background:#fef9c3; color:#a16207; font-size:9px; padding: 3px 6px;">KRS Pending</span>';
                } else {
                    krsHtml = '<span class="badge" style="background:#dcfce3; color:#166534; font-size:9px; padding: 3px 6px;">KRS Final</span>';
                }
                html += '<div style="margin-top: 4px;">' + krsHtml + '</div>';
                html += '</div>';

                // IPS, IPK, SKS info
                var ipsP = parseFloat(mhs.ips_prev || 0);
                var ipsC = parseFloat(mhs.ips_curr || 0);
                var ipkC = parseFloat(mhs.ipk_curr || 0);
                var sksC = parseInt(mhs.sks_curr || 0);
                
                var labelP = mhs.smt_prev ? formatSmt(mhs.smt_prev) : 'SMT LALU';
                var labelC = mhs.smt_curr ? formatSmt(mhs.smt_curr) : 'SMT INI';
                
                var ipsHtml = '';
                if (ipsP > 0) {
                    ipsHtml  = '<div style="display: flex; justify-content: flex-end; align-items: center; gap: 8px;">';
                    ipsHtml += '<div style="text-align: center;"><div style="font-size: 9px; color: #64748b; font-weight: 600; white-space:nowrap;">' + labelP + '</div><div style="font-size: 13px; font-weight: 700; color: #1e293b;">' + ipsP.toFixed(2) + '</div></div>';
                    ipsHtml += '<div style="color: #cbd5e1; font-size: 12px;"><i class="fa-solid fa-arrow-right"></i></div>';
                    ipsHtml += '<div style="text-align: center;"><div style="font-size: 9px; color: #64748b; font-weight: 600; white-space:nowrap;">' + labelC + '</div><div style="font-size: 14px; font-weight: 800; color: #1e293b;">' + ipsC.toFixed(2) + '</div></div>';
                    ipsHtml += '</div>';
                } else if (ipsC > 0) {
                    ipsHtml = '<div style="text-align: center;"><div style="font-size: 9px; color: #64748b; font-weight: 600;">IPS</div><div style="font-size: 14px; font-weight: 800; color: #1e293b;">' + ipsC.toFixed(2) + '</div></div>';
                } else {
                    ipsHtml = '<div style="text-align: center;"><div style="font-size: 9px; color: #64748b; font-weight: 600;">IPS</div><div style="font-size: 13px; font-weight: 700; color: #94a3b8;">-</div></div>';
                }

                var ipkStr = ipkC > 0 ? ipkC.toFixed(2) : '-';
                var sksStr = sksC > 0 ? sksC : '-';

                html += '<div style="display: flex; gap: 15px; align-items: center;">';
                html += '<div style="text-align: right; border-right: 1px solid #e2e8f0; padding-right: 15px;">';
                html += '<div style="font-size: 10px; color: #64748b; margin-bottom: 2px;">IPK: <strong style="color:#1e293b; font-size:11px;">' + ipkStr + '</strong></div>';
                html += '<div style="font-size: 10px; color: #64748b;">Tot. SKS: <strong style="color:#1e293b; font-size:11px;">' + sksStr + '</strong></div>';
                html += '</div>';
                html += '<div style="text-align: right; min-width: 100px;">' + ipsHtml + '</div>';
                html += '</div>';
                html += '</li>';
            });
            html += '</ul>';
            el.innerHTML = html;
        }

        function loadFeedChunk($, onDone) {
            if (feedLoading || feedAllDone) { if (onDone) onDone(); return; }
            feedLoading = true;

            $.ajax({
                url: '<?= site_url("C_wall/ajax_load_events") ?>',
                type: 'GET',
                data: { offset: feedOffset, last_date: feedLastDate },
                success: function (response) {
                    if ($.trim(response) === '') {
                        feedAllDone = true;
                        if (feedOffset > 0) $('#no-more-data').show();
                    } else {
                        $('#timeline-container').append(response);
                        var lastSep = $('.date-separator').last();
                        if (lastSep.length > 0) feedLastDate = lastSep.data('date');
                        feedOffset += 10;
                    }
                    feedLoading = false;

                    // Show feed, hide skeleton
                    $('#skeleton-feed').hide();
                    $('#timeline-scroll-container').show();
                    if (onDone) onDone();
                },
                error: function () {
                    feedLoading = false;
                    $('#skeleton-feed').hide();
                    $('#timeline-container').html('<div class="alert alert-info" style="border-radius:10px;">Belum ada peristiwa akademik.</div>');
                    $('#timeline-scroll-container').show();
                    if (onDone) onDone();
                }
            });
        }

        // Infinite scroll for feed
        var scrollContainer = document.getElementById('timeline-scroll-container');
        if (scrollContainer) {
            scrollContainer.addEventListener('scroll', function () {
                if (this.scrollTop + this.clientHeight >= this.scrollHeight - 50) {
                    loadFeedChunk($, null);
                }
            });
        }

    }); // end waitForLibrary

    // --------------------------------------------------------
    // GLOBAL MODAL FUNCTIONS
    // --------------------------------------------------------
    window.showEwThn1Modal    = function () { $('#ewThn1Modal').appendTo('body').modal('show'); };
    window.showEwThn2Modal    = function () { $('#ewThn2Modal').appendTo('body').modal('show'); };
    window.showEwThn3Modal    = function () { $('#ewThn3Modal').appendTo('body').modal('show'); };
    window.showEwIpsDropModal = function () { $('#ewIpsDropModal').appendTo('body').modal('show'); };
    window.showEwBelumKrsModal = function () { $('#ewBelumKrsModal').appendTo('body').modal('show'); };
    window.showNoProgressTaModal = function () { $('#noProgressTaModal').appendTo('body').modal('show'); };
    window.showNoProgressSemproModal = function () { $('#noProgressSemproModal').appendTo('body').modal('show'); };
    window.showNoProgressSemhasModal = function () { $('#noProgressSemhasModal').appendTo('body').modal('show'); };
    window.showAmanModal     = function () { $('#amanModal').appendTo('body').modal('show'); };
    window.showPerhatianModal = function () { $('#perhatianModal').appendTo('body').modal('show'); };
    window.showRisikoModal   = function () { $('#risikoModal').appendTo('body').modal('show'); };

    window.showProgressModal = function (progVal) {
        if (!window._gpd) return;
        var group = window._gpd[progVal];
        if (!group || !group.students) return;

        var modalTitle = document.getElementById('progressModalTitle');
        var modalBody  = document.getElementById('progressModalBody');
        modalTitle.innerHTML = 'Tahap <strong>' + group.status + '</strong> (' + progVal + '%)';

        var html = '<ul class="list-group" style="border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0;">';
        group.students.forEach(function (s) {
            html += '<li class="list-group-item" style="border-bottom: 1px solid #e2e8f0; padding: 12px; display: flex; align-items: center; gap: 15px; border-left: none; border-right: none; border-top: none;">';
            if (s.foto) {
                html += '<img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' + s.foto + '" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;">';
            } else {
                html += '<div style="width: 42px; height: 42px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; border: 2px solid #bae6fd;">' + (s.nm_pd ? s.nm_pd.charAt(0) : '?') + '</div>';
            }
            html += '<div style="flex: 1; overflow: hidden;">';
            html += '<div style="font-weight: 700; color: #1e293b; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + (s.nm_pd || '-') + '</div>';
            html += '<div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 1px;">' + (s.nipd || '') + '</div>';
            var judul = s.judul_ditetapkan ? s.judul_ditetapkan : 'Belum menetapkan judul';
            if (judul.length > 60) judul = judul.substring(0, 60) + '...';
            html += '<div style="font-size: 10px; color: #94a3b8; margin-top: 4px; line-height: 1.2;">' + judul + '</div>';
            html += '</div>';

            var badgeColor = 'badge-orange', statusText = 'Proses', nilaiShow = '-';
            if (progVal == '100')      { statusText = 'Sidang';    badgeColor = 'badge-green';  nilaiShow = s.nilai_sidang ? parseFloat(s.nilai_sidang).toFixed(2) : '-'; }
            else if (progVal == '75')  { statusText = 'Semhas';    badgeColor = 'badge-blue';   nilaiShow = s.nilai_semhas ? parseFloat(s.nilai_semhas).toFixed(2) : '-'; }
            else if (progVal == '45')  { statusText = 'Sempro';    badgeColor = 'badge-orange'; nilaiShow = s.nilai_sempro ? parseFloat(s.nilai_sempro).toFixed(2) : '-'; }
            else                       { statusText = 'Penetapan'; badgeColor = 'badge-red'; }

            html += '<div style="text-align: right; min-width: 80px;">';
            html += '<div style="font-size: 10px; color: #64748b; font-weight: 600; margin-bottom: 2px;">NILAI</div>';
            html += '<div style="font-size: 16px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">' + nilaiShow + '</div>';
            html += '<span class="badge-premium ' + badgeColor + '" style="font-size: 9px; padding: 2px 6px;">' + statusText + '</span>';
            html += '</div>';
            html += '</li>';
        });
        html += '</ul>';
        modalBody.innerHTML = html;
        $('#progressModal').modal('show');
    };

    window.showMengajarModal = function (id_jadwal, title) {
        var $ = window.jQuery;
        $('#mengajarModalTitle').text(title);
        $('#mengajarModalBody').html('<div style="text-align: center; padding: 30px;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #94a3b8; margin-bottom: 10px;"></i><div style="color: #64748b; font-size: 13px;">Memuat data...</div></div>');
        $('#mengajarModal').appendTo('body').modal('show');

        var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
        var postData = { id_jadwal: id_jadwal };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: '<?= site_url("C_wall/ajax_detail_mengajar") ?>',
            type: 'POST', data: postData,
            success: function (response) { $('#mengajarModalBody').html(response); },
            error: function () { $('#mengajarModalBody').html('<div class="alert alert-danger">Gagal memuat data.</div>'); }
        });
    };

    window.showDocModal = function (url, katName, nmPd, nipd) {
        katName = katName || 'Sidang'; nmPd = nmPd || 'Mahasiswa'; nipd = nipd || '';
        if (!url || url.trim() === '') { alert('Dokumen tidak tersedia atau link kosong.'); return; }
        document.getElementById('docModalTitle').innerText = 'Berkas Dokumen ' + katName;
        document.getElementById('folderWarningStudentName').innerText = nmPd + ' (' + nipd + ')';
        var iframe   = document.getElementById('docIframe');
        var warning  = document.getElementById('folderWarning');
        var footerBtn = document.getElementById('docDirectLink');
        if (url.includes('drive.google.com/drive/folders/')) {
            iframe.style.display = 'none'; warning.style.display = 'flex'; footerBtn.style.display = 'none';
            document.getElementById('folderDirectLink').href = url;
        } else {
            iframe.style.display = 'block'; warning.style.display = 'none'; footerBtn.style.display = 'inline-block';
            if (url.includes('drive.google.com/file/d/') && url.includes('/view')) url = url.replace('/view', '/preview');
            iframe.src = url;
        }
        footerBtn.href = url;
        window.jQuery('#docModal').modal('show');
    };

    // Sync groupedProgressData ke window setelah AJAX middle selesai
    // (renderProgressPanel sets groupedProgressData, we mirror to window._groupedProgressData)
    Object.defineProperty(window, '_groupedProgressData', {
        get: function () {
            // Return the local var via closure trick — expose through global set in renderProgressPanel
            return window.__gpd || {};
        }
    });

    // Clear iframe on close
    document.addEventListener('DOMContentLoaded', function () {
        var $ = window.jQuery;
        if ($) {
            $('#docModal').on('hidden.bs.modal', function () {
                document.getElementById('docIframe').src = '';
            });
        }
    });

}());
</script>



