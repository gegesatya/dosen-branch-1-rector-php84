<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .modern-spinner {
    width: 60px;
    height: 60px;
    border: 5px solid rgba(49, 130, 206, 0.2);
    border-top: 5px solid #3182CE;
    border-radius: 50%;
    animation: spinModern 1s linear infinite;
    margin: 0 auto;
  }
  @keyframes spinModern {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .loader-text {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 16px;
    color: #3182CE;
    letter-spacing: 2px;
    animation: pulseText 1.5s infinite;
  }
  @keyframes pulseText {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
  /* Custom modern fonts and style rules matching the premium design request */
  .linimasa-wrapper {
    font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
    color: #2D3748;
    background-color: #F7FAFC;
    padding: 20px 0;
  }

  .timeline-title-section {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E2E8F0;
  }

  .timeline-title-section h1 {
    font-size: 26px;
    font-weight: 800;
    color: #1A365D;
    margin: 0 0 5px 0;
    letter-spacing: -0.5px;
  }

  .timeline-title-section p {
    color: #718096;
    margin: 0;
    font-size: 14px;
  }

  /* Left column: Feed cards */
  .feed-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .student-card {
    background: #FFFFFF;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(16, 37, 66, 0.04);
    border: 1px solid #E2E8F0;
    padding: 24px;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .student-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(16, 37, 66, 0.08);
  }

  .card-number-badge {
    position: absolute;
    top: 15px;
    right: 20px;
    background-color: #EDF2F7;
    color: #4A5568;
    font-size: 12px;
    font-weight: 800;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1px solid #E2E8F0;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }

  /* Accent stripe based on progress */
  .student-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 6px;
    background: linear-gradient(180deg, #3182CE 0%, #63B3ED 100%);
  }

  .student-card.progress-sidang::before {
    background: linear-gradient(180deg, #38A169 0%, #68D391 100%);
  }

  .student-card.progress-semhas::before {
    background: linear-gradient(180deg, #319795 0%, #4FD1C5 100%);
  }

  .student-card.progress-sempro::before {
    background: linear-gradient(180deg, #D69E2E 0%, #F6E05E 100%);
  }

  /* Header styles */
  .card-header-flex {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
  }

  .avatar-circle {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #E2E8F0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    background-color: #EDF2F7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #4A5568;
    flex-shrink: 0;
  }

  .avatar-initials {
    background: linear-gradient(135deg, #E2E8F0 0%, #CBD5E0 100%);
    text-transform: uppercase;
    font-size: 18px;
  }

  .student-meta {
    flex-grow: 1;
    min-width: 0;
  }

  .student-name {
    font-size: 16px;
    font-weight: 700;
    color: #1A202C;
    margin: 0 0 2px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .student-nim-prodi {
    font-size: 13px;
    color: #718096;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .prodi-badge {
    background-color: #EDF2F7;
    color: #4A5568;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
  }

  /* Body styles */
  .ta-title-box {
    background-color: #F8FAFC;
    border: 1px dashed #CBD5E0;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
  }

  .ta-title-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #A0AEC0;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .ta-title-text {
    font-size: 14.5px;
    font-weight: 600;
    color: #2D3748;
    line-height: 1.5;
    margin: 0;
  }

  /* Advisors list */
  .advisors-section {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 24px;
    font-size: 13px;
  }

  .advisor-item {
    display: flex;
    align-items: center;
    gap: 8px;
    background-color: #EDF2F7;
    padding: 6px 12px;
    border-radius: 999px;
    color: #4A5568;
    font-weight: 500;
  }

  .advisor-badge-primary {
    background-color: #3182CE;
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    text-transform: uppercase;
  }

  .advisor-badge-sec {
    background-color: #4A5568;
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    text-transform: uppercase;
  }

  /* Milestone Progress bar */
  .milestone-stepper {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    margin: 15px 0 25px 0;
    padding: 0 10px;
  }

  .milestone-stepper::before {
    content: '';
    position: absolute;
    top: 14px;
    left: 40px;
    right: 40px;
    height: 4px;
    background-color: #E2E8F0;
    z-index: 1;
  }

  /* Active connection lines */
  .stepper-progress-line {
    position: absolute;
    top: 14px;
    left: 40px;
    height: 4px;
    background: linear-gradient(90deg, #3182CE 0%, #38A169 100%);
    z-index: 2;
    transition: width 0.4s ease;
    width: 0%;
  }

  .milestone-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 3;
    width: 80px;
    text-align: center;
  }

  .step-node {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #FFFFFF;
    border: 3px solid #CBD5E0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    color: #A0AEC0;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }

  .step-node i {
    display: none;
    font-size: 12px;
  }

  .step-label {
    margin-top: 8px;
    font-size: 11px;
    font-weight: 700;
    color: #A0AEC0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    transition: color 0.3s ease;
  }

  /* Completed step styles */
  .milestone-step.completed .step-node {
    background-color: #48BB78;
    border-color: #38A169;
    color: white;
  }

  .milestone-step.completed .step-node span {
    display: none;
  }

  .milestone-step.completed .step-node i {
    display: block;
  }

  .milestone-step.completed .step-label {
    color: #2F855A;
  }

  /* Current step styles */
  .milestone-step.active-step .step-node {
    border-color: #3182CE;
    color: #3182CE;
    box-shadow: 0 0 0 4px rgba(66, 153, 225, 0.2);
  }

  .milestone-step.active-step .step-label {
    color: #2B6CB0;
  }

  /* Card footer */
  .card-footer-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #EDF2F7;
    padding-top: 14px;
    font-size: 12.5px;
    color: #718096;
  }

  .time-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background-color: #EBF8FF;
    color: #2B6CB0;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
  }

  .time-badge.alert-old {
    background-color: #FFF5F5;
    color: #C53030;
  }

  .time-badge.alert-medium {
    background-color: #FFFAF0;
    color: #DD6B20;
  }

  .time-badge.alert-appreciation {
    background-color: #F0FFF4;
    color: #276749;
    border: 1px solid #9AE6B4;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  /* Sticky sidebar styling */
  .sticky-sidebar {
    position: -webkit-sticky;
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 25px;
  }

  .stats-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
    border: 1px solid rgba(224, 224, 224, 0.4);
    transition: all 0.3s ease;
  }
  .stats-card:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  }

  .stats-header {
    font-size: 18px;
    font-weight: 700;
    color: #102542;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .btn-action-prodi {
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white !important;
    border: none;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(49, 130, 206, 0.3);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.3px;
  }
  .btn-action-prodi:hover {
    background: linear-gradient(135deg, #2B6CB0 0%, #2c5282 100%);
    box-shadow: 0 4px 10px rgba(49, 130, 206, 0.4);
    transform: translateY(-2px);
  }
  .btn-action-prodi:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(49, 130, 206, 0.2);
  }

  /* AI Dashboard Stat Cards (Bootstrap 5 Mimic) */
  .ai-stat-card {
    border-radius: 16px !important;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    min-height: 120px;
  }

  .ai-stat-card .icon {
    position: absolute;
    right: 15px;
    bottom: 10px;
    font-size: 55px;
    opacity: 0.15;
  }

  .stat-bg-blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
  .stat-bg-red { background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%); }
  .stat-bg-orange { background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); }
  .stat-bg-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }

  .ai-stat-value {
    font-size: 32px;
    font-weight: 800;
    margin: 5px 0 0 0;
    font-family: 'Outfit', sans-serif;
  }

  .ai-stat-label {
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    opacity: 0.9;
  }

  .ai-stat-sub {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 5px;
  }

  /* Global count hero widget */
  .hero-stat-widget {
    background: linear-gradient(135deg, #1A365D 0%, #2A4365 100%);
    color: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(26, 54, 93, 0.2);
  }

  /* Custom metric numbers and metric info styles removed as they are replaced by ai-stat-card */

  /* List of departments styling */
  .prodi-bar-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .prodi-bar-item {
    font-size: 12px;
  }

  .prodi-bar-header {
    display: flex;
    justify-content: space-between;
    font-weight: 700;
    color: #4A5568;
    margin-bottom: 4px;
  }

  .prodi-progress-bg {
    height: 8px;
    background-color: #EDF2F7;
    border-radius: 999px;
    overflow: hidden;
  }

  .prodi-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3182CE 0%, #63B3ED 100%);
    border-radius: 999px;
  }

  /* Average duration badge */
  .avg-duration-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #EDF2F7;
    border-radius: 12px;
    padding: 14px;
    margin-top: 5px;
  }

  .avg-duration-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background-color: #CBD5E0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #4A5568;
  }

  .avg-duration-value {
    font-size: 15px;
    font-weight: 800;
    color: #2D3748;
  }

  .avg-duration-lbl {
    font-size: 11px;
    color: #718096;
    font-weight: 500;
  }

  /* Load More Button styling */
  .load-more-section {
    text-align: center;
    margin-top: 15px;
    margin-bottom: 40px;
  }

  .btn-load-more {
    background-color: #3182CE;
    color: #FFFFFF;
    font-weight: 700;
    padding: 12px 32px;
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 10px rgba(49, 130, 206, 0.3);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-load-more:hover {
    background-color: #2B6CB0;
    box-shadow: 0 6px 15px rgba(49, 130, 206, 0.4);
    transform: translateY(-1px);
  }

  .btn-load-more:active {
    transform: translateY(1px);
  }

  .btn-load-more:disabled {
    background-color: #A0AEC0;
    box-shadow: none;
    cursor: not-allowed;
  }

  /* Floating animation */
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .spinner {
    display: none;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 0.8s linear infinite;
  }

  /* Empty State styling */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
  }

  .empty-state-icon {
    font-size: 48px;
    color: #CBD5E0;
    margin-bottom: 15px;
  }

  .empty-state-text {
    font-size: 16px;
    font-weight: 700;
    color: #718096;
    margin: 0;
  }

  /* Filter Pills */
  .filter-pill {
    padding: 6px 14px;
    background-color: #EDF2F7;
    color: #4A5568;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    user-select: none;
  }
  .filter-pill:hover {
    background-color: #E2E8F0;
  }
  .filter-pill.active {
    background-color: #3182CE;
    color: white;
    box-shadow: 0 2px 6px rgba(49, 130, 206, 0.4);
  }
  .filter-group-status .filter-pill.active {
    background-color: #38A169;
    box-shadow: 0 2px 6px rgba(56, 161, 105, 0.4);
  }
  .filter-group-status .filter-pill[data-value="kritis"].active,
  .filter-group-status .filter-pill[data-value="menghilang"].active {
    background-color: #E53E3E;
    box-shadow: 0 2px 6px rgba(229, 62, 62, 0.4);
  }
</style>

<div class="page-content" style="padding-top: 0; position: relative; min-height: 80vh;">
  <div id="page-loader" style="display: flex; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(247,250,252,0.9); z-index: 99999; justify-content: flex-start; padding-top: 150px; align-items: center; flex-direction: column; backdrop-filter: blur(3px); border-radius: 8px;">
    <div class="modern-spinner" style="width: 70px; height: 70px;"></div>
    <h4 class="loader-text" style="margin-top: 25px;">MEMUAT LINIMASA TA...</h4>
  </div>
  <script>
    window.addEventListener('load', function() {
      setTimeout(function() {
        var loader = document.getElementById('page-loader');
        if(loader) loader.style.display = 'none';
      }, 1000);
    });
  </script>
  <div class="linimasa-wrapper">
    <div class="container-fluid">
      
      <!-- Top Title Bar -->
      <div class="row">
        <div class="col-md-12">
          <div class="timeline-title-section">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h1><?= $judul_besar; ?></h1>
                <p>Monitoring dan analisis progres Tugas Akhir mahasiswa aktif yang terdaftar</p>
              </div>
              <div>
                <span class="badge bg-primary" style="font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 6px; background-color: #3182CE;">
                  Live Analytics
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Top Dashboard Section -->
      <div class="row" style="margin-bottom: 25px;">
        <div class="col-md-12">
          <div class="stats-card" style="padding: 24px; border-radius: 16px;">
            <div class="row" style="display: flex; flex-wrap: wrap; align-items: stretch;">
              
              <!-- Left inside: Chart (col-md-7) -->
              <div class="col-md-7" style="border-right: 1px solid #E2E8F0; padding-right: 25px;">
                <div style="font-size: 14px; font-weight: 800; color: #1A365D; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                  <i class="fa fa-line-chart" style="color: #3182CE;"></i>
                  Grafik Performa Tugas Akhir per Program Studi
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                  <canvas id="prodiPerformanceChart"></canvas>
                </div>
              </div>
              
              <!-- Right inside: Strong & Weak Points (col-md-5) -->
              <div class="col-md-5" style="padding-left: 25px; display: flex; flex-direction: column; justify-content: center; gap: 15px;">
                <div style="font-size: 14px; font-weight: 800; color: #1A365D; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: flex; align-items: center; gap: 8px;">
                  <i class="fa fa-info-circle" style="color: #319795;"></i>
                  Analisis Progres & Evaluasi
                </div>
                
                <!-- Strong Point Box -->
                <div style="background-color: #F0FFF4; border-left: 4px solid #38A169; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                  <div style="font-size: 11px; font-weight: 700; color: #2F855A; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    <i class="fa fa-arrow-up" style="margin-right: 4px;"></i> Strong Point
                  </div>
                  <div style="font-size: 13px; color: #276749; line-height: 1.5; margin: 0;">
                    <?= $stats->strong_point; ?>
                  </div>
                </div>
                
                <!-- Weak Point Box -->
                <div style="background-color: #FFF5F5; border-left: 4px solid #E53E3E; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                  <div style="font-size: 11px; font-weight: 700; color: #9B2C2C; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                    <i class="fa fa-arrow-down" style="margin-right: 4px;"></i> Weak Point (Evaluasi)
                  </div>
                  <div style="font-size: 13px; color: #742A2A; line-height: 1.5; margin: 0;">
                    <?= $stats->weak_point; ?>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>

      <div class="row" style="display: flex; flex-wrap: wrap;">
        <!-- LEFT: Student Cards Feed (8 Grids) -->
        <div class="col-md-8">
          
          <!-- Filter UI -->
          <div class="filter-container" style="background: white; border-radius: 12px; padding: 15px 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <div style="font-size: 13px; font-weight: 800; color: #1A365D; text-transform: uppercase; margin-bottom: 12px;">
              <i class="fa fa-filter" style="color: #3182CE; margin-right: 6px;"></i> Live Filter Mahasiswa
            </div>
            
            <div style="margin-bottom: 15px;">
              <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase; margin-bottom: 8px;">Filter Program Studi (Pilih Salah Satu):</div>
              <div class="filter-pill-group filter-group-prodi" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <div class="filter-pill active" data-filter-type="prodi" data-value="WBI">Semua Prodi (WBI)</div>
                <?php foreach ($stats->prodi_details as $pname => $pdata): ?>
                  <div class="filter-pill" data-filter-type="prodi" data-value="<?= htmlspecialchars($pname); ?>"><?= htmlspecialchars($pname); ?></div>
                <?php endforeach; ?>
              </div>
            </div>

            <div style="border-top: 1px dashed #E2E8F0; margin-bottom: 15px;"></div>

            <div>
              <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase; margin-bottom: 8px;">Filter Status (Pilihan Ganda):</div>
              <div class="filter-pill-group filter-group-status" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <div class="filter-pill" data-filter-type="status" data-value="sempro"><i class="fa fa-check-circle" style="margin-right: 4px;"></i>Sudah Sempro</div>
                <div class="filter-pill" data-filter-type="status" data-value="semhas"><i class="fa fa-check-circle" style="margin-right: 4px;"></i>Sudah Semhas</div>
                <div class="filter-pill" data-filter-type="status" data-value="sidang"><i class="fa fa-check-circle" style="margin-right: 4px;"></i>Sudah Sidang</div>
                <div class="filter-pill" data-filter-type="status" data-value="tepat_waktu"><i class="fa fa-star" style="color: #38A169; margin-right: 4px;"></i>Lulus Tepat Waktu</div>
                <div class="filter-pill" data-filter-type="status" data-value="kritis"><i class="fa fa-warning" style="margin-right: 4px;"></i>Kritis (> 1 Thn)</div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
              <div id="filter-result-info" style="font-size: 13px; font-weight: 700; color: #4A5568; display: none;">
                <i class="fa fa-info-circle" style="color: #3182CE; margin-right: 5px;"></i> Menemukan <span id="filter-total-count" style="color: #3182CE; font-size: 14px;">0</span> mahasiswa
              </div>
              <div style="display: flex; gap: 10px; margin-left: auto;">
                <button id="btn-reset-filter" class="btn btn-light btn-sm" style="font-weight: 600; border-radius: 6px; padding: 6px 16px; border: 1px solid #E2E8F0; color: #4A5568;">
                  <i class="fa fa-refresh" style="margin-right: 5px;"></i> Reset Filter
                </button>
                <button id="btn-apply-filter" class="btn btn-primary btn-sm" style="font-weight: 600; border-radius: 6px; padding: 6px 16px; background-color: #3182CE; border-color: #3182CE;">
                  <i class="fa fa-search" style="margin-right: 5px;"></i> Terapkan Filter
                </button>
              </div>
            </div>
          </div>

          <div class="feed-container" id="timeline-feed">
            <!-- Cards will be appended here via AJAX -->
          </div>

          <!-- Load More Button -->
          <div class="load-more-section">
            <button id="btn-load-more" class="btn-load-more">
              <span class="spinner" id="load-spinner"></span>
              <span id="btn-text">Tampilkan Lebih Banyak</span>
            </button>
          </div>
        </div>

        <!-- RIGHT: Sticky Analytics Dashboard Sidebar (4 Grids) -->
        <div class="col-md-4">
          <div class="sticky-sidebar">
            
            <!-- Overall Stats Widget -->
            <div class="stats-card">
              <div class="stats-header">
                <i class="fa fa-bar-chart" style="color: #3182CE;"></i>
                Statistik Tugas Akhir
              </div>
              
            <!-- Total TA Hero Count -->
            <div class="ai-stat-card stat-bg-blue" style="padding: 16px; min-height: 95px; margin-bottom: 15px;">
              <div class="ai-stat-label" style="font-size: 12px;">Total Mahasiswa TA Aktif</div>
              <div class="ai-stat-value" style="font-size: 28px; margin-top: 0;"><?= $stats->total; ?></div>
              <div class="ai-stat-sub" style="font-size: 10px; margin-top: 2px;"><i class="fa fa-users"></i> Sedang Mengerjakan Tugas Akhir</div>
              <i class="fa fa-graduation-cap icon" style="font-size: 45px; right: 10px; bottom: 5px;"></i>
            </div>

            <div style="font-size: 11px; font-weight: 700; color: #718096; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Distribusi Potensi Kelulusan</div>
            <div class="row" style="margin-left: -6px; margin-right: -6px;">
              <div class="col-xs-4" style="padding: 0 6px;">
                <div class="ai-stat-card stat-bg-green" style="padding: 12px 5px; min-height: 75px; margin-bottom: 20px; border-radius: 12px !important; text-align: center;" title="Sudah Seminar Hasil / Sidang">
                  <div class="ai-stat-value" style="font-size: 22px; margin: 0;"><?= $stats->potensi_tinggi; ?></div>
                  <div class="ai-stat-label" style="font-size: 9.5px; margin-top: 3px;">Tinggi</div>
                </div>
              </div>
              <div class="col-xs-4" style="padding: 0 6px;">
                <div class="ai-stat-card stat-bg-orange" style="padding: 12px 5px; min-height: 75px; margin-bottom: 20px; border-radius: 12px !important; text-align: center;" title="Lulus Seminar Proposal">
                  <div class="ai-stat-value" style="font-size: 22px; margin: 0;"><?= $stats->potensi_sedang; ?></div>
                  <div class="ai-stat-label" style="font-size: 9.5px; margin-top: 3px;">Sedang</div>
                </div>
              </div>
              <div class="col-xs-4" style="padding: 0 6px;">
                <div class="ai-stat-card stat-bg-red" style="padding: 12px 5px; min-height: 75px; margin-bottom: 20px; border-radius: 12px !important; text-align: center;" title="Belum Seminar Proposal">
                  <div class="ai-stat-value" style="font-size: 22px; margin: 0;"><?= $stats->potensi_rendah; ?></div>
                  <div class="ai-stat-label" style="font-size: 9.5px; margin-top: 3px;">Rendah</div>
                </div>
              </div>
            </div>
            </div>

            <!-- Prodi breakdown widget -->
            <div class="stats-card">
              <div class="stats-header">
                <i class="fa fa-university" style="color: #319795;"></i>
                Sebaran per Program Studi
              </div>
              
              <div class="prodi-bar-list">
                <?php foreach ($stats->prodi_details as $prodi_name => $detail): ?>
                  <?php 
                    $count = $detail['total'];
                    $percent = $stats->total > 0 ? round(($count / $stats->total) * 100) : 0;
                  ?>
                  <div class="prodi-bar-item">
                    <div class="prodi-bar-header">
                      <span><?= $prodi_name; ?></span>
                      <div class="d-flex align-items-center gap-2" style="display: flex; gap: 10px;">
                        <span><?= $count; ?> Mhs (<?= $percent; ?>%)</span>
                        <button class="btn-action-prodi" onclick="showProdiDetail('<?= addslashes($prodi_name); ?>')" title="Lihat Analisis Detail Prodi">
                          <i class="fa fa-pie-chart"></i> Analisis
                        </button>
                      </div>
                    </div>
                    <div class="prodi-progress-bg">
                      <div class="prodi-progress-fill" style="width: <?= $percent; ?>%;"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Dashboard Prodi -->
<div class="modal fade" id="modalProdiDetail" tabindex="-1" aria-labelledby="modalProdiDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
      <div class="modal-header" style="background-color: #F7FAFC; border-bottom: 1px solid #E2E8F0; border-radius: 16px 16px 0 0; padding: 20px 24px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: 2px;">&times;</button>
        <h5 class="modal-title" id="modalProdiDetailLabel" style="font-weight: 800; color: #1A365D; font-size: 18px; margin: 0;">
          <i class="fa fa-university text-primary" style="margin-right: 8px;"></i> <span id="mdlProdiName">Nama Prodi</span>
        </h5>
      </div>
      <div class="modal-body" style="padding: 25px;">
          
          <!-- Executive Summary Narrative Box -->
          <div style="background-color: #EBF8FF; border-left: 4px solid #3182CE; border-radius: 8px; padding: 16px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div style="font-size: 12px; font-weight: 800; color: #2B6CB0; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
              <i class="fa fa-bullhorn" style="margin-right: 5px;"></i> Ringkasan Eksekutif
            </div>
            <div id="modal-executive-summary" style="font-size: 14px; color: #2A4365; line-height: 1.6; margin: 0; text-align: justify;">
              <!-- Narrative generated dynamically by JS -->
            </div>
          </div>

          <div class="row">
            <!-- Strong Point Box -->
            <div class="col-md-6">
              <div style="background-color: #F0FFF4; border-left: 4px solid #38A169; border-radius: 8px; padding: 16px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #2F855A; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                  <i class="fa fa-arrow-up" style="margin-right: 5px;"></i> Strong Point
                </div>
                <div id="modal-strong-point" style="font-size: 14px; color: #276749; line-height: 1.5; margin: 0;"></div>
              </div>
            </div>
            
            <!-- Weak Point Box -->
            <div class="col-md-6">
              <div style="background-color: #FFF5F5; border-left: 4px solid #E53E3E; border-radius: 8px; padding: 16px; height: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #9B2C2C; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                  <i class="fa fa-arrow-down" style="margin-right: 5px;"></i> Weak Point (Risiko Kritis)
                </div>
                <div id="modal-weak-point" style="font-size: 14px; color: #742A2A; line-height: 1.5; margin: 0;"></div>
              </div>
            </div>
          </div>

          <hr style="border-top: 1px dashed #CBD5E0; margin: 25px 0;">

          <div class="row">
            <!-- Progress Tahapan Chart -->
            <div class="col-md-4" style="border-right: 1px dashed #E2E8F0; margin-bottom: 25px;">
              <div style="font-size: 12px; font-weight: 800; color: #4A5568; text-transform: uppercase; margin-bottom: 15px; text-align: center;">
                <i class="fa fa-bar-chart" style="color: #38A169; margin-right: 4px;"></i> Tahapan Progres Terkini
              </div>
              <div style="position: relative; height: 180px;">
                <canvas id="progressTahapanChart"></canvas>
              </div>
            </div>

            <!-- Waktu Pengerjaan Chart -->
            <div class="col-md-4" style="border-right: 1px dashed #E2E8F0; margin-bottom: 25px;">
              <div style="font-size: 12px; font-weight: 800; color: #4A5568; text-transform: uppercase; margin-bottom: 15px; text-align: center;">
                <i class="fa fa-pie-chart" style="color: #DD6B20; margin-right: 4px;"></i> Distribusi Waktu Pengerjaan
              </div>
              <div style="position: relative; height: 180px;">
                <canvas id="prodiRiskChart"></canvas>
              </div>
            </div>
            
            <!-- KRS Status Chart -->
            <div class="col-md-4" style="margin-bottom: 25px;">
              <div style="font-size: 12px; font-weight: 800; color: #4A5568; text-transform: uppercase; margin-bottom: 15px; text-align: center;">
                <i class="fa fa-user-times" style="color: #E53E3E; margin-right: 4px;"></i> Status Keaktifan KRS
              </div>
              <div style="position: relative; height: 180px;">
                <canvas id="krsMissingChart"></canvas>
              </div>
            </div>
          </div>

          <hr style="border-top: 1px dashed #CBD5E0; margin: 0 0 25px 0;">

          <div class="row">
            <!-- Kelulusan Tepat Waktu Chart -->
            <div class="col-md-5" style="border-right: 1px dashed #E2E8F0;">
              <div style="font-size: 12px; font-weight: 800; color: #4A5568; text-transform: uppercase; margin-bottom: 15px; text-align: center;">
                <i class="fa fa-graduation-cap" style="color: #38A169; margin-right: 4px;"></i> Performa Kelulusan
              </div>
              <div style="position: relative; height: 180px;">
                <canvas id="kelulusanTepatWaktuChart"></canvas>
              </div>
            </div>

            <!-- Dosen Kritis Chart -->
            <div class="col-md-7">
              <div style="font-size: 12px; font-weight: 800; color: #4A5568; text-transform: uppercase; margin-bottom: 15px; text-align: center;">
                <i class="fa fa-users" style="color: #3182CE; margin-right: 4px;"></i> Beban Dosen (Mahasiswa Kritis)
              </div>
              <div style="position: relative; height: 180px;">
                <canvas id="dosenKritisChart"></canvas>
              </div>
            </div>
          </div>

        </div>
      <div class="modal-footer" style="border-top: 1px solid #EDF2F7; padding: 15px 24px;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight: 600;">Tutup</button>
      </div>
    </div>
  </div>
</div>
