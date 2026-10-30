<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .profil-wrapper {
    font-family: 'Nunito', 'Segoe UI', Roboto, sans-serif;
    color: #2D3748;
    background-color: #F7FAFC;
    padding: 20px 0;
  }
  .profil-title-section {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E2E8F0;
  }
  .profil-title-section h1 {
    font-size: 26px;
    font-weight: 800;
    color: #1A365D;
    margin: 0 0 5px 0;
    letter-spacing: -0.5px;
  }
  .profil-title-section p { color: #718096; margin: 0; font-size: 14px; }
  .search-bar-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .search-bar-wrapper input {
    flex: 1;
    border: 2px solid #E2E8F0;
    border-radius: 8px;
    padding: 9px 14px;
    font-family: 'Nunito', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #2D3748;
    transition: border-color 0.2s;
    outline: none;
  }
  .search-bar-wrapper input:focus { border-color: #3182CE; }
  .search-bar-wrapper input::placeholder { color: #A0AEC0; font-weight: 400; }
  .search-icon { color: #A0AEC0; font-size: 18px; }
  .stats-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-family: 'Nunito', sans-serif;
    font-size: 13px;
    color: #718096;
    font-weight: 600;
  }
  .stats-bar .badge-count {
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }
  .dosen-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    margin-bottom: 30px;
  }
  @media (max-width: 1400px) { .dosen-grid { grid-template-columns: repeat(5, 1fr); } }
  @media (max-width: 1200px) { .dosen-grid { grid-template-columns: repeat(4, 1fr); } }
  @media (max-width: 992px) { .dosen-grid { grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 768px) { .dosen-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 576px) { .dosen-grid { grid-template-columns: 1fr; } }
  .dosen-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid rgba(224,224,224,0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.25s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .dosen-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3182CE, #63B3ED);
    opacity: 0;
    transition: opacity 0.25s;
  }
  .dosen-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(49,130,206,0.15);
    border-color: #BEE3F8;
  }
  .dosen-card:hover::before { opacity: 1; }
  .dosen-avatar {
    width: 86px; height: 86px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #EBF8FF;
    box-shadow: 0 4px 12px rgba(49,130,206,0.2);
    margin-bottom: 14px;
    transition: border-color 0.25s;
    background: #EDF2F7;
  }
  .dosen-card:hover .dosen-avatar { border-color: #90CDF4; }
  .dosen-avatar-fallback {
    width: 86px; height: 86px;
    border-radius: 50%;
    border: 3px solid #EBF8FF;
    box-shadow: 0 4px 12px rgba(49,130,206,0.2);
    margin-bottom: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: white;
    font-weight: 800;
    font-family: 'Nunito', sans-serif;
  }
  .dosen-name { font-size: 13px; font-weight: 800; color: #1A365D; line-height: 1.3; margin-bottom: 5px; }
  .dosen-nidn { font-size: 11px; font-weight: 600; color: #A0AEC0; letter-spacing: 0.5px; margin-bottom: 8px; }
  .dosen-jabatan-badge {
    background: #EBF8FF; color: #2B6CB0;
    font-size: 10px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px; margin-bottom: 6px; letter-spacing: 0.3px;
  }
  .dosen-status-badge {
    background: #F0FFF4; color: #276749;
    font-size: 10px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px;
  }
  .dosen-status-badge.nonaktif { background: #FFF5F5; color: #C53030; }
  .btn-detail {
    margin-top: 12px; width: 100%; padding: 8px 0;
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white; border: none; border-radius: 8px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    font-family: 'Nunito', sans-serif; transition: all 0.2s; letter-spacing: 0.3px;
  }
  .btn-detail:hover {
    background: linear-gradient(135deg, #2B6CB0 0%, #2c5282 100%);
    box-shadow: 0 4px 12px rgba(49,130,206,0.3);
  }
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 8px;
  }
  @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
  .skeleton-card {
    background: #fff; border-radius: 16px; padding: 24px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid rgba(224,224,224,0.5);
    display: flex; flex-direction: column; align-items: center;
  }
  .skeleton-avatar { width: 86px; height: 86px; border-radius: 50%; margin-bottom: 14px; }
  .skeleton-line { height: 12px; width: 80%; margin-bottom: 8px; }
  .skeleton-line.short { width: 50%; }
  .skeleton-line.btn-line { width: 100%; height: 32px; border-radius: 8px; margin-top: 4px; }

  /* Modal 2-Row Design - Fixed Header & Banner, Scroll Inside Tab Panel Only */
  .modal-profil .modal-dialog {
    max-width: 1100px;
    width: 95%;
    margin: 20px auto;
    height: calc(100vh - 40px);
    display: flex;
    align-items: center;
  }
  .modal-profil .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    font-family: 'Nunito', sans-serif;
    overflow: hidden;
    height: calc(100vh - 40px);
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    width: 100%;
  }
  #modalContent {
    display: none;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    height: 100%;
    overflow: hidden;
  }
  .modal-header-profil {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 24px;
    border-bottom: 1px solid #E2E8F0;
    background: #FFFFFF;
    border-radius: 16px 16px 0 0;
    flex-shrink: 0;
  }
  .modal-title-profil {
    font-size: 17px; font-weight: 800; color: #1A365D; margin: 0;
  }
  .modal-close-btn {
    background: #EDF2F7; border: none; color: #4A5568;
    width: 32px; height: 32px; border-radius: 50%; font-size: 15px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
  }
  .modal-close-btn:hover { background: #E2E8F0; color: #E53E3E; transform: rotate(90deg); }
  
  .modal-body-profil {
    padding: 16px 20px 20px;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #F8FAFC;
    flex: 1;
    min-height: 0;
    overflow: hidden; /* TIDAK ADA SCROLL DI LEVEL MODAL BODY */
  }

  body.modal-open,
  body.modal-open .page-content,
  body.modal-open .page-content-wrapper {
    overflow: hidden !important;
  }

  /* Row 1: Header / Banner Profil Ringkas (DIAM / FIXED DI ATAS) */
  .modal-banner-row {
    background: linear-gradient(135deg, #1A365D 0%, #2B6CB0 100%);
    border-radius: 12px;
    padding: 16px 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 16px rgba(26,54,93,0.18);
    flex-shrink: 0; /* Row 1 tidak mengecil dan tidak ikut ter-scroll */
  }
  @media (max-width: 768px) {
    .modal-banner-row { flex-direction: column; text-align: center; gap: 12px; padding: 14px; }
  }
  .modal-banner-avatar {
    width: 88px; height: 118px;
    border-radius: 8px;
    border: 3px solid rgba(255,255,255,0.4);
    object-fit: cover;
    box-shadow: 0 4px 14px rgba(0,0,0,0.22);
    background: #EDF2F7;
    flex-shrink: 0;
  }
  .modal-banner-avatar-fallback {
    width: 88px; height: 118px;
    border-radius: 8px;
    border: 3px solid rgba(255,255,255,0.4);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; font-weight: 800;
    box-shadow: 0 4px 14px rgba(0,0,0,0.22);
    flex-shrink: 0;
  }
  .modal-banner-info { flex: 1; min-width: 0; }
  .modal-banner-nama { font-size: 19px; font-weight: 800; margin: 0 0 3px; line-height: 1.25; color: #FFFFFF; }
  .modal-banner-nidn { font-size: 12px; font-weight: 600; opacity: 0.88; margin-bottom: 8px; }
  .modal-banner-badges { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
  .modal-banner-badges span {
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35);
    color: white; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
  }
  .modal-banner-meta-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px 14px;
    border-top: 1px solid rgba(255,255,255,0.15); padding-top: 8px; font-size: 12px;
  }
  .modal-banner-meta-item { display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,0.92); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .modal-banner-meta-item i { opacity: 0.75; width: 13px; }

  /* Row 2: Tabs Section */
  .modal-tabs-row {
    background: #FFFFFF;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #E2E8F0;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .modal-tabs-nav-bar {
    display: flex; flex-wrap: wrap; gap: 4px;
    padding: 10px 16px;
    background: #EDF2F7;
    border-bottom: 1px solid #E2E8F0;
    border-radius: 12px 12px 0 0;
    flex-shrink: 0; /* Tab menu tetap diam di atas panel */
  }
  .main-tab {
    white-space: nowrap; padding: 8px 16px; border-radius: 8px;
    font-size: 12.5px; font-weight: 800; color: #4A5568; cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; gap: 7px; background: transparent;
  }
  .main-tab i { font-size: 13.5px; }
  .main-tab:hover { background: rgba(255,255,255,0.6); color: #1A365D; }
  .main-tab.active { background: #3182CE; color: white; box-shadow: 0 4px 12px rgba(49,130,206,0.3); }

  /* TAB CONTENT PANEL - TEMPAT TERJADINYA VERTICAL SCROLL */
  .main-pane {
    display: none;
  }
  .main-pane.active {
    display: block;
    flex: 1;
    min-height: 0;
    overflow-y: auto !important;
    overflow-x: hidden;
    padding: 20px 22px;
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
    animation: fadeIn 0.25s ease;
  }
  /* Modern Custom Scrollbar khusus Tab Panel */
  .main-pane::-webkit-scrollbar {
    width: 8px;
    display: block !important;
  }
  .main-pane::-webkit-scrollbar-track {
    background: #F7FAFC;
    border-radius: 4px;
  }
  .main-pane::-webkit-scrollbar-thumb {
    background: #CBD5E0;
    border-radius: 4px;
  }
  .main-pane::-webkit-scrollbar-thumb:hover {
    background: #A0AEC0;
  }
  
  @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
  
  .info-section-title {
    font-size: 13px; font-weight: 800; color: #1A365D;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px; border-bottom: 2px solid #EDF2F7; padding-bottom: 8px;
  }
  .info-section-title i { color: #3182CE; font-size: 15px; }
  
  .info-grid-2col {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 30px; margin-bottom: 16px;
  }
  @media (max-width: 768px) { .info-grid-2col { grid-template-columns: 1fr; } }
  
  .info-row {
    display: flex; flex-direction: row; gap: 14px; padding: 9px 0;
    border-bottom: 1px solid #F7FAFC; align-items: flex-start;
  }
  .info-row:last-child { border-bottom: none; }
  .info-label { font-size: 12.5px; font-weight: 700; color: #718096; width: 170px; flex-shrink: 0; }
  .info-value { font-size: 13.5px; font-weight: 700; color: #2D3748; word-break: break-word; }
  .info-value.empty { color: #CBD5E0; font-style: italic; font-weight: 400; }

  /* Sub-tab Tridharma */
  .tridharma-tabs {
    display: flex; gap: 4px; background: #EDF2F7;
    border-radius: 10px; padding: 4px; margin-bottom: 18px;
  }
  .tridharma-tab {
    flex: 1; text-align: center; padding: 9px 6px; border-radius: 8px;
    font-size: 12.5px; font-weight: 800; color: #718096; cursor: pointer; transition: all 0.2s;
  }
  .tridharma-tab.active { background: white; color: #3182CE; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  .tridharma-pane { display: none; }
  .tridharma-pane.active { display: block; animation: fadeIn 0.3s ease; }
  
  .tridharma-item {
    display: flex; gap: 14px; padding: 12px 14px; border-radius: 8px;
    border-bottom: 1px solid #F7FAFC; align-items: center; transition: background 0.15s;
  }
  .tridharma-item:hover { background: #F7FAFC; }
  .tridharma-item:last-child { border-bottom: none; }
  .tridharma-num {
    min-width: 24px; height: 24px; background: #EBF8FF; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #2B6CB0; flex-shrink: 0;
  }
  .tridharma-text { font-size: 13.5px; font-weight: 700; color: #1A365D; line-height: 1.4; flex: 1; }
  .tridharma-text small { display: block; color: #718096; font-weight: 600; margin-top: 3px; font-size: 12px; }
  .tridharma-empty { text-align: center; color: #A0AEC0; font-size: 14px; padding: 30px 0; font-weight: 600; }
  .tridharma-empty i { font-size: 32px; display: block; margin-bottom: 8px; color: #CBD5E0; }

  /* Modern Table for Pendidikan, Pekerjaan, Publikasi, BKD, Tridharma */
  .modal-table-wrap {
    background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px;
    overflow-x: auto; margin-bottom: 20px;
  }
  .modal-table-scroll {
    max-height: 380px;
    overflow-y: auto !important;
    overflow-x: auto;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    background: #FFFFFF;
    margin-bottom: 16px;
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
  }
  .modal-table-scroll::-webkit-scrollbar {
    width: 7px;
    height: 7px;
  }
  .modal-table-scroll::-webkit-scrollbar-track {
    background: #F7FAFC;
    border-radius: 4px;
  }
  .modal-table-scroll::-webkit-scrollbar-thumb {
    background: #CBD5E0;
    border-radius: 4px;
  }
  .modal-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #A0AEC0;
  }
  .modal-table-scroll thead th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #EDF2F7 !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  }
  .modal-custom-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
  }
  .modal-custom-table th {
    background: #F7FAFC; color: #4A5568; font-weight: 800;
    text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.5px;
    padding: 11px 14px; border-bottom: 2px solid #E2E8F0; text-align: left;
  }
  .modal-custom-table td {
    padding: 12px 14px; border-bottom: 1px solid #EDF2F7; color: #2D3748;
    vertical-align: middle;
  }
  .modal-custom-table tr:hover td { background: #F8FAFC; }

  /* BKD UI */
  .bkd-status-card {
    display: flex; align-items: center; gap: 14px; border-radius: 10px; border: 1px solid;
  }
  .bkd-status-card.hijau { background: #F0FFF4; border-color: #C6F6D5; }
  .bkd-status-card.merah { background: #FFF5F5; border-color: #FED7D7; }
  .bkd-status-card.abu   { background: #FFFFF0; border-color: #FEFCBF; }
  .bkd-dot { border-radius: 50%; flex-shrink: 0; }
  .bkd-dot.hijau { background: #38A169; }
  .bkd-dot.merah { background: #E53E3E; }
  .bkd-dot.abu   { background: #D69E2E; }
  .bkd-progress-bar-wrap { background: #E2E8F0; border-radius: 999px; overflow: hidden; }
  .bkd-progress-bar { height: 100%; border-radius: 999px; transition: width 0.4s ease; }

  .modal-spinner-wrap { display: flex; flex-direction: column; align-items: center; padding: 60px 0; }
  .modal-spinner {
    width: 40px; height: 40px; border: 4px solid rgba(49,130,206,0.15);
    border-top: 4px solid #3182CE; border-radius: 50%; animation: spin 0.9s linear infinite; margin-bottom: 12px;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="page-content" style="padding-top: 0;">
  <div class="profil-wrapper">
    <div class="container-fluid">
      
      <!-- Title -->
      <div class="row">
        <div class="col-md-12">
          <div class="profil-title-section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h1><i class="fa fa-id-card-o" style="color: #3182CE; margin-right: 10px;"></i>Profil Dosen SISTER</h1>
                <p>Direktori dosen aktif lengkap dengan data tridharma, publikasi &amp; BKD terintegrasi SISTER Kemdiktisaintek</p>
              </div>
              <div style="text-align: right; font-size: 12px; color: #38A169; font-family: 'Nunito', sans-serif; font-weight: 700; background: #F0FFF4; padding: 8px 14px; border-radius: 8px; border: 1px solid #C6F6D5;">
                <i class="fa fa-circle" style="margin-right: 4px;"></i> Terhubung SISTER Pusat
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search -->
      <div class="row">
        <div class="col-md-12">
          <div class="search-bar-wrapper">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="searchDosen" placeholder="Cari nama dosen, NIDN, atau jabatan fungsional..." oninput="filterDosen(this.value)">
          </div>
        </div>
      </div>

      <!-- Stats + Grid -->
      <div class="row">
        <div class="col-md-12">
          <div class="stats-bar" id="statsBar" style="display:none;">
            Menampilkan <span class="badge-count" id="visibleCount">0</span> dari <span id="totalCount">0</span> dosen
          </div>
          
          <div class="dosen-grid" id="dosenGrid">
            <!-- Skeletons -->
            <?php for ($i = 0; $i < 12; $i++): ?>
            <div class="skeleton-card">
              <div class="skeleton skeleton-avatar"></div>
              <div class="skeleton skeleton-line"></div>
              <div class="skeleton skeleton-line short"></div>
              <div class="skeleton skeleton-line short" style="width:40%; height:18px; border-radius:999px;"></div>
              <div class="skeleton skeleton-line btn-line"></div>
            </div>
            <?php endfor; ?>
          </div>

          <div id="noResult" style="display:none; text-align:center; padding: 50px 0; color:#A0AEC0; font-family:'Nunito',sans-serif;">
            <i class="fa fa-database" style="font-size: 38px; margin-bottom: 12px; display:block;"></i>
            <div style="font-weight:700; font-size:15px; margin-bottom:6px; color:#4A5568;">Belum ada data dosen dari SISTER</div>
            <div style="font-size:13px; max-width:360px; margin:0 auto; line-height:1.6;">Hanya dosen yang sudah tersinkronisasi akan ditampilkan. Jalankan <strong>Sinkronisasi Massal</strong> dari Dashboard Eksekutif terlebih dahulu.</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Detail (Layout 2 Rows) -->
<div class="modal fade modal-profil" id="modalProfilDosen" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div id="modalLoadingSpinner" class="modal-spinner-wrap" style="display:none;">
        <div class="modal-spinner"></div>
        <div style="font-size:13px; color:#718096; font-weight:700; font-family:'Nunito',sans-serif;">Memuat data lengkap dosen...</div>
      </div>

      <div id="modalContent" style="display:none; position:relative;">
        <div class="modal-header-profil">
          <h5 class="modal-title-profil" id="modalTitleProfil">Profil Dosen</h5>
          <button class="modal-close-btn" onclick="$('#modalProfilDosen').modal('hide')"><i class="fa fa-times"></i></button>
        </div>
        
        <div class="modal-body-profil">
          
          <!-- ROW 1: Banner Profil Ringkas (Foto + Data Utama) -->
          <div class="modal-banner-row">
            <div id="modalAvatarWrap"></div>
            <div class="modal-banner-info">
              <h3 class="modal-banner-nama" id="modalBannerNama"></h3>
              <div class="modal-banner-nidn" id="modalBannerNidn"></div>
              <div class="modal-banner-badges" id="modalBannerBadges"></div>
              <div class="modal-banner-meta-grid" id="modalBannerMetaGrid"></div>
            </div>
          </div>

          <!-- ROW 2: Tabs Menu Navigasi Lengkap -->
          <div class="modal-tabs-row">
            <div class="modal-tabs-nav-bar">
              <div class="main-tab active" onclick="switchMainTab('kepegawaian', this)"><i class="fa fa-briefcase"></i> Kepegawaian</div>
              <div class="main-tab" onclick="switchMainTab('kependudukan', this)"><i class="fa fa-id-card"></i> Kependudukan &amp; Keluarga</div>
              <div class="main-tab" onclick="switchMainTab('tridharma', this)"><i class="fa fa-graduation-cap"></i> Tridharma</div>
              <div class="main-tab" onclick="switchMainTab('publikasi', this)"><i class="fa fa-newspaper-o" style="color:#805AD5;"></i> Publikasi</div>
              <div class="main-tab" onclick="switchMainTab('bkd', this)"><i class="fa fa-bar-chart" style="color:#38A169;"></i> Riwayat BKD</div>
            </div>

            <!-- Tab 1: Kepegawaian -->
            <div id="tab-kepegawaian" class="main-pane active">
              <div class="info-section-title"><i class="fa fa-user"></i> Identitas Dosen</div>
              <div class="info-grid-2col" id="modalIdentitasKepegawaian"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-building"></i> Tempat Bekerja (Penugasan)</div>
              <div class="info-grid-2col" id="modalPenugasan"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-flask"></i> Bidang Ilmu</div>
              <div id="modalBidangIlmu" style="margin-bottom: 20px;"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-briefcase"></i> Riwayat Jabatan Fungsional</div>
              <div id="modalJabatanFungsional" style="margin-bottom: 20px;"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-certificate"></i> Riwayat Inpassing (KUM)</div>
              <div id="modalInpassing" style="margin-bottom: 20px;"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-university"></i> Riwayat Pendidikan Formal</div>
              <div id="modalPendidikanFormal" style="margin-bottom: 20px;"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-history"></i> Riwayat Pekerjaan / Pengalaman Kerja</div>
              <div id="modalRiwayatPekerjaan"></div>
            </div>

            <!-- Tab 2: Kependudukan & Keluarga -->
            <div id="tab-kependudukan" class="main-pane">
              <div class="info-section-title"><i class="fa fa-id-card"></i> Data Kependudukan</div>
              <div class="info-grid-2col" id="modalKependudukan"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-users"></i> Data Keluarga</div>
              <div class="info-grid-2col" id="modalKeluarga"></div>
              
              <div class="info-section-title" style="margin-top: 24px;"><i class="fa fa-address-book"></i> Kontak &amp; Alamat</div>
              <div class="info-grid-2col" id="modalKontakAlamat"></div>
            </div>

            <!-- Tab 3: Tridharma -->
            <div id="tab-tridharma" class="main-pane">
              <div class="tridharma-tabs">
                <div class="tridharma-tab active" onclick="switchTridharma('pengajaran', this)"><i class="fa fa-book" style="margin-right:5px;"></i> Pengajaran</div>
                <div class="tridharma-tab" onclick="switchTridharma('penelitian', this)"><i class="fa fa-flask" style="margin-right:5px;"></i> Penelitian</div>
                <div class="tridharma-tab" onclick="switchTridharma('pengabdian', this)"><i class="fa fa-users" style="margin-right:5px;"></i> Pengabdian</div>
              </div>
              <div id="tridharma-pengajaran" class="tridharma-pane active"></div>
              <div id="tridharma-penelitian" class="tridharma-pane"></div>
              <div id="tridharma-pengabdian" class="tridharma-pane"></div>
            </div>

            <!-- Tab 4: Publikasi -->
            <div id="tab-publikasi" class="main-pane">
              <div class="info-section-title"><i class="fa fa-newspaper-o" style="color:#805AD5;"></i> Riwayat Publikasi Ilmiah</div>
              <div id="modalPublikasiWrap"></div>
            </div>

            <!-- Tab 5: Riwayat BKD -->
            <div id="tab-bkd" class="main-pane">
              <div id="modalBKDWrap"></div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
var allDosen = [];
var filteredDosen = [];
var currentModalBkd = null;
var modalBkdChartInstance = null;

document.addEventListener('DOMContentLoaded', function() {
  loadDosenList();
});

function loadDosenList() {
  $.ajax({
    url: '<?= site_url("C_sister/ajax_daftar_profil_dosen") ?>',
    type: 'POST',
    data: { <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp) {
      if (resp.status === 'success') {
        allDosen = resp.data;
        filteredDosen = allDosen;
        renderGrid(filteredDosen);
        updateStats();
      } else {
        showError(resp.message || 'Gagal memuat data dosen.');
      }
    },
    error: function() { showError('Terjadi kesalahan koneksi saat memuat daftar dosen.'); }
  });
}

function renderGrid(data) {
  var grid = document.getElementById('dosenGrid');
  if (data.length === 0) {
    grid.innerHTML = '';
    document.getElementById('noResult').style.display = 'block';
    return;
  }
  document.getElementById('noResult').style.display = 'none';
  var html = '';
  data.forEach(function(d) {
    var initials = getInitials(d.nama_lengkap || d.nama);
    var avatarHtml = d.foto_url
      ? '<img src="' + esc(d.foto_url) + '" class="dosen-avatar" alt="' + esc(d.nama_lengkap || d.nama) + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
        + '<div class="dosen-avatar-fallback" style="display:none;">' + initials + '</div>'
      : '<div class="dosen-avatar-fallback">' + initials + '</div>';
    var nama = d.nama_lengkap || d.nama || '-';
    var nidn = d.nidn || d.username || '-';
    // Badge tridharma mini
    var tridharma = '';
    if ((d.jml_pengajaran || 0) > 0) {
      tridharma += '<span style="background:#EBF8FF;color:#2B6CB0;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin:2px;display:inline-block;">' + d.jml_pengajaran + ' Ajar</span>';
    }
    if ((d.jml_penelitian || 0) > 0) {
      tridharma += '<span style="background:#FFF5F5;color:#C53030;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin:2px;display:inline-block;">' + d.jml_penelitian + ' Riset</span>';
    }
    if ((d.jml_pengabdian || 0) > 0) {
      tridharma += '<span style="background:#F0FFF4;color:#276749;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin:2px;display:inline-block;">' + d.jml_pengabdian + ' Abdimas</span>';
    }
    if (d.kum) {
      tridharma += '<span style="background:#FAF5FF;color:#6B46C1;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin:2px;display:inline-block;">KUM ' + d.kum + '</span>';
    }
    html += '<div class="dosen-card" onclick="lihatDetail(\'' + esc(d.username) + '\')">'
          + '<div style="display:flex;flex-direction:column;align-items:center;">' + avatarHtml + '</div>'
          + '<div class="dosen-name">' + esc(nama) + '</div>'
          + '<div class="dosen-nidn">NIDN: ' + esc(nidn) + '</div>'
          + (tridharma ? '<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0;margin-bottom:8px;">' + tridharma + '</div>' : '')
          + '<button class="btn-detail" onclick="event.stopPropagation();lihatDetail(\'' + esc(d.username) + '\')">'
          + '<i class="fa fa-user" style="margin-right:4px;"></i> Lihat Profil</button>'
          + '</div>';
  });
  grid.innerHTML = html;
}

function updateStats() {
  document.getElementById('statsBar').style.display = 'flex';
  document.getElementById('visibleCount').textContent = filteredDosen.length;
  document.getElementById('totalCount').textContent = allDosen.length;
}

function filterDosen(kw) {
  kw = kw.toLowerCase().trim();
  filteredDosen = kw ? allDosen.filter(function(d) {
    return (d.nama_lengkap || d.nama || '').toLowerCase().indexOf(kw) !== -1
        || (d.nidn || d.username || '').toLowerCase().indexOf(kw) !== -1;
  }) : allDosen;
  renderGrid(filteredDosen);
  updateStats();
}

function lihatDetail(username) {
  document.getElementById('modalLoadingSpinner').style.display = 'flex';
  document.getElementById('modalContent').style.display = 'none';
  // Reset tabs
  document.querySelectorAll('.main-tab').forEach(function(t,i){ t.classList.toggle('active', i===0); });
  document.querySelectorAll('.main-pane').forEach(function(p,i){ p.classList.toggle('active', i===0); });
  document.querySelectorAll('.tridharma-tab').forEach(function(t,i){ t.classList.toggle('active', i===0); });
  document.querySelectorAll('.tridharma-pane').forEach(function(p,i){ p.classList.toggle('active', i===0); });
  
  $('#modalProfilDosen').modal('show');
  
  $.ajax({
    url: '<?= site_url("C_sister/ajax_detail_profil_dosen") ?>',
    type: 'POST',
    data: { username: username, <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp) {
      document.getElementById('modalLoadingSpinner').style.display = 'none';
      if (resp.status === 'success') {
        renderModalDetail(resp.data);
        document.getElementById('modalContent').style.display = 'flex';
      } else {
        $('#modalProfilDosen').modal('hide');
        alert('Gagal memuat detail: ' + (resp.message || 'Error'));
      }
    },
    error: function() { $('#modalProfilDosen').modal('hide'); alert('Terjadi kesalahan koneksi.'); }
  });
}

function renderModalDetail(d) {
  var nama = d.nama_lengkap || d.nama || '-';
  var initials = getInitials(nama);
  document.getElementById('modalTitleProfil').innerText = 'Profil ' + nama;
  
  // 1. ROW 1: BANNER PROFIL RINGKAS
  document.getElementById('modalAvatarWrap').innerHTML = d.foto_url
    ? '<img src="' + esc(d.foto_url) + '" class="modal-banner-avatar" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
      + '<div class="modal-banner-avatar-fallback" style="display:none;">' + initials + '</div>'
    : '<div class="modal-banner-avatar-fallback">' + initials + '</div>';
    
  document.getElementById('modalBannerNama').innerText = nama;
  document.getElementById('modalBannerNidn').innerText = 'NIDN / NUPTK: ' + (d.nidn || '-') + (d.nuptk ? ' / ' + d.nuptk : '');

  // Badges
  var badges = '';
  var aktif = d.status_aktif == '1' || d.status_aktif === 'Aktif';
  badges += '<span style="background:' + (aktif ? 'rgba(56,161,105,0.3);border-color:rgba(56,161,105,0.6)' : 'rgba(229,62,62,0.3);border-color:rgba(229,62,62,0.6)') + '"><i class="fa fa-circle" style="font-size:8px;margin-right:4px;"></i>' + (aktif ? 'Dosen Aktif' : 'Non-Aktif') + '</span>';
  if (d.jabatan_fungsional || d.jabatan) badges += '<span><i class="fa fa-briefcase" style="margin-right:4px;"></i>' + esc(d.jabatan_fungsional || d.jabatan) + '</span>';
  if (d.gelar_akademik) badges += '<span><i class="fa fa-graduation-cap" style="margin-right:4px;"></i>' + esc(d.gelar_akademik) + '</span>';
  if (d.pendidikan_terakhir) badges += '<span><i class="fa fa-university" style="margin-right:4px;"></i>' + esc(d.pendidikan_terakhir) + '</span>';
  
  // KUM Inpassing
  var kum = '';
  if (d.list_inpassing && d.list_inpassing.length > 0) {
    for (var i = 0; i < d.list_inpassing.length; i++) {
      if (d.list_inpassing[i].angka_kredit) {
        kum = d.list_inpassing[i].angka_kredit;
        break;
      }
    }
  }
  if (kum) badges += '<span style="background:rgba(128,90,213,0.3);border-color:rgba(128,90,213,0.6);"><i class="fa fa-certificate" style="margin-right:4px;"></i>KUM ' + esc(kum) + '</span>';
  document.getElementById('modalBannerBadges').innerHTML = badges;

  // Banner Meta Grid (Email & Unit Kerja / Program Studi)
  var metaGridHtml = ''
    + '<div class="modal-banner-meta-item"><i class="fa fa-envelope-o"></i> <span>' + esc(d.email || d.sister_email || '-') + '</span></div>'
    + '<div class="modal-banner-meta-item"><i class="fa fa-building-o"></i> <span>' + esc(d.unit_kerja || '-') + '</span></div>';
  document.getElementById('modalBannerMetaGrid').innerHTML = metaGridHtml;

  // 2. ROW 2 - TAB KEPEGAWAIAN
  var jkText = d.jenis_kelamin === 'L' ? 'Laki-laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : (d.jenis_kelamin || '-'));
  var ttlText = (d.tempat_lahir ? d.tempat_lahir : '') + (d.tempat_lahir && d.tanggal_lahir ? ', ' : '') + d.tanggal_lahir;
  var identitasFields = [
    ['Nama Lengkap', d.nama_lengkap || d.nama],
    ['Gelar Akademik', d.gelar_akademik],
    ['Jenis Kelamin', jkText],
    ['Tempat, Tgl Lahir', ttlText],
    ['Nama Ibu Kandung', d.nama_ibu_kandung],
    ['Agama', d.agama],
    ['NIDN / NUPTK', (d.nidn || '-') + (d.nuptk ? ' / ' + d.nuptk : '')],
    ['NIP', d.nip],
    ['Status Perkawinan', d.stat_kawin]
  ];
  document.getElementById('modalIdentitasKepegawaian').innerHTML = renderInfoRows(identitasFields);

  var penugasanFields = [
    ['Perguruan Tinggi', d.perguruan_tinggi],
    ['Program Studi / Unit Kerja', d.unit_kerja],
    ['Status Kepegawaian', d.status_kepegawaian],
    ['Ikatan Kerja', d.ikatan_kerja_dosen],
    ['Tanggal Mulai Bekerja', d.tanggal_mulai_penugasan]
  ];
  document.getElementById('modalPenugasan').innerHTML = renderInfoRows(penugasanFields);

  // Bidang Ilmu
  var bidangIlmuHtml = '';
  if (d.list_bidang_ilmu && d.list_bidang_ilmu.length > 0) {
    bidangIlmuHtml += '<ul style="margin:0; padding-left:20px; color:#2D3748; font-size:13.5px; font-weight:700; line-height:1.7;">';
    d.list_bidang_ilmu.forEach(function(b) {
      bidangIlmuHtml += '<li>' + esc(b.kelompok_bidang) + '</li>';
    });
    bidangIlmuHtml += '</ul>';
  } else {
    bidangIlmuHtml += '<div style="color:#A0AEC0; font-size:13px; font-style:italic;">Belum ada bidang ilmu tercatat</div>';
  }
  document.getElementById('modalBidangIlmu').innerHTML = bidangIlmuHtml;

  // Jabatan Fungsional
  var jabfungHtml = '';
  if (d.list_jabatan_fungsional && d.list_jabatan_fungsional.length > 0) {
    d.list_jabatan_fungsional.forEach(function(j) {
      jabfungHtml += '<div style="margin-bottom: 12px; padding: 12px 16px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">'
                   + '<div style="font-weight: 800; color: #1A365D; font-size: 14px;"><i class="fa fa-briefcase" style="color:#3182CE; margin-right:6px;"></i>' + esc(j.jabatan_fungsional) + '</div>'
                   + '<div style="font-size: 12.5px; color: #718096; margin-top: 4px;">Nomor SK: <span style="color:#2D3748; font-weight:700;">' + esc(j.sk || '-') + '</span> &nbsp;|&nbsp; Mulai: <span style="color:#2D3748; font-weight:700;">' + esc(j.tanggal_mulai || '-') + '</span></div>'
                   + '</div>';
    });
  } else {
    jabfungHtml += '<div style="color:#A0AEC0; font-size:13px; font-style:italic;">Belum ada riwayat jabatan fungsional</div>';
  }
  document.getElementById('modalJabatanFungsional').innerHTML = jabfungHtml;

  // Inpassing
  var inpassingHtml = '';
  if (d.list_inpassing && d.list_inpassing.length > 0) {
    d.list_inpassing.forEach(function(i) {
      inpassingHtml += '<div style="margin-bottom: 12px; padding: 12px 16px; background:#FAF5FF; border:1px solid #E9D8FD; border-radius:8px;">'
                   + '<div style="font-weight: 800; color: #553C9A; font-size: 14px;"><i class="fa fa-certificate" style="color:#805AD5; margin-right:6px;"></i>' + esc(i.pangkat_golongan || '-') + ' <span style="font-size:12px; font-weight:800; color:#6B46C1; background:#FFFFFF; border:1px solid #D6BCFA; padding:2px 10px; border-radius:999px; margin-left:8px;">KUM ' + esc(i.angka_kredit || '0') + '</span></div>'
                   + '<div style="font-size: 12.5px; color: #718096; margin-top: 4px;">SK: <span style="color:#2D3748; font-weight:700;">' + esc(i.sk || '-') + '</span> &nbsp;|&nbsp; Tgl SK: <span style="color:#2D3748; font-weight:700;">' + esc(i.tanggal_sk || '-') + '</span> &nbsp;|&nbsp; Mulai: <span style="color:#2D3748; font-weight:700;">' + esc(i.tanggal_mulai || '-') + '</span></div>'
                   + '</div>';
    });
  } else {
    inpassingHtml += '<div style="color:#A0AEC0; font-size:13px; font-style:italic;">Belum ada riwayat inpassing</div>';
  }
  document.getElementById('modalInpassing').innerHTML = inpassingHtml;

  // Riwayat Pendidikan Formal (Tabel)
  var pendHtml = '';
  if (d.list_pendidikan_formal && d.list_pendidikan_formal.length > 0) {
    pendHtml += '<div class="modal-table-wrap"><table class="modal-custom-table">'
              + '<thead><tr><th width="45">No</th><th>Jenjang &amp; Perguruan Tinggi</th><th>Gelar</th><th>Bidang Studi</th><th>Tahun Lulus</th></tr></thead><tbody>';
    d.list_pendidikan_formal.forEach(function(p, idx) {
      pendHtml += '<tr>'
                + '<td style="color:#718096;font-weight:700;">' + (idx + 1) + '</td>'
                + '<td><div style="font-weight:800;color:#1A365D;">' + esc(p.jenjang_pendidikan || '-') + '</div><div style="font-size:12px;color:#718096;">' + esc(p.nama_perguruan_tinggi || '-') + '</div></td>'
                + '<td><span style="background:#EBF8FF;color:#2B6CB0;padding:2px 8px;border-radius:6px;font-weight:700;font-size:11.5px;">' + esc(p.gelar_akademik || '-') + '</span></td>'
                + '<td style="font-weight:600;">' + esc(p.bidang_studi || '-') + '</td>'
                + '<td style="font-weight:700;color:#2D3748;">' + esc(p.tahun_lulus || '-') + '</td>'
                + '</tr>';
    });
    pendHtml += '</tbody></table></div>';
  } else {
    pendHtml += '<div style="color:#A0AEC0; font-size:13px; font-style:italic; padding:10px 0;">Belum ada data pendidikan formal tercatat</div>';
  }
  document.getElementById('modalPendidikanFormal').innerHTML = pendHtml;

  // Riwayat Pekerjaan (Tabel)
  var pekHtml = '';
  if (d.list_riwayat_pekerjaan && d.list_riwayat_pekerjaan.length > 0) {
    pekHtml += '<div class="modal-table-wrap"><table class="modal-custom-table">'
             + '<thead><tr><th width="45">No</th><th>Instansi / Perusahaan</th><th>Jabatan &amp; Divisi</th><th>Jenis &amp; Bidang Usaha</th><th>Periode Bekerja</th></tr></thead><tbody>';
    d.list_riwayat_pekerjaan.forEach(function(pk, idx) {
      var periode = (pk.mulai_bekerja || '-') + ' s/d ' + (pk.selesai_bekerja || 'Sekarang');
      pekHtml += '<tr>'
               + '<td style="color:#718096;font-weight:700;">' + (idx + 1) + '</td>'
               + '<td style="font-weight:800;color:#1A365D;">' + esc(pk.instansi || '-') + '</td>'
               + '<td><div style="font-weight:700;color:#2D3748;">' + esc(pk.nama_jabatan || '-') + '</div><div style="font-size:12px;color:#718096;">' + esc(pk.divisi || '') + '</div></td>'
               + '<td style="font-size:12.5px;color:#4A5568;">' + esc(pk.jenis_pekerjaan || '-') + (pk.bidang_usaha ? ' &bull; ' + esc(pk.bidang_usaha) : '') + '</td>'
               + '<td><span style="background:#EDF2F7;color:#2B6CB0;padding:3px 8px;border-radius:6px;font-weight:700;font-size:12px;"><i class="fa fa-calendar-check-o"></i> ' + periode + '</span></td>'
               + '</tr>';
    });
    pekHtml += '</tbody></table></div>';
  } else {
    pekHtml += '<div style="color:#A0AEC0; font-size:13px; font-style:italic; padding:10px 0;">Belum ada riwayat pekerjaan / pengalaman kerja tercatat</div>';
  }
  document.getElementById('modalRiwayatPekerjaan').innerHTML = pekHtml;

  // 3. ROW 2 - TAB KEPENDUDUKAN & KELUARGA
  var kependudukanFields = [
    ['NIK', d.nik],
    ['Agama', d.agama],
    ['Kewarganegaraan', d.kewarganegaraan === 'ID' ? 'Indonesia' : d.kewarganegaraan]
  ];
  document.getElementById('modalKependudukan').innerHTML = renderInfoRows(kependudukanFields);

  var keluargaFields = [
    ['Status Perkawinan', d.stat_kawin],
    ['Nama Pasangan (Suami/Istri)', d.nm_suami_istri],
    ['NIP Pasangan', d.nip_suami_istri]
  ];
  document.getElementById('modalKeluarga').innerHTML = renderInfoRows(keluargaFields);

  var kontakFields = [
    ['Email SISTER / Institusi', d.sister_email || d.email],
    ['No HP (WhatsApp)', d.no_hp],
    ['No Telp Rumah', d.no_tel_rmh],
    ['Alamat Lengkap', d.alamat]
  ];
  document.getElementById('modalKontakAlamat').innerHTML = renderInfoRows(kontakFields);

  // 4. ROW 2 - TAB TRIDHARMA
  renderTridharma('pengajaran', d.pengajaran || []);
  renderTridharma('penelitian', d.penelitian || []);
  renderTridharma('pengabdian', d.pengabdian || []);

  // 5. ROW 2 - TAB PUBLIKASI
  renderModalPublikasi(d.publikasi || []);

  // 6. ROW 2 - TAB BKD
  currentModalBkd = d.bkd || { laporan: [], kegiatan_by_smt: {} };
  renderModalBKD(currentModalBkd);
}

function renderInfoRows(fields) {
  var html = '';
  fields.forEach(function(f) {
    var v = f[1] && String(f[1]).trim() !== '' && f[1] !== 'null' && f[1] !== '-' ? f[1] : null;
    html += '<div class="info-row"><div class="info-label">' + f[0] + '</div>'
          + (v ? '<span class="info-value">' + esc(v) + '</span>' : '<span class="info-value empty">-</span>')
          + '</div>';
  });
  return html;
}

function renderTridharma(type, items) {
  var el = document.getElementById('tridharma-' + type);
  if (!items || items.length === 0) {
    el.innerHTML = '<div class="tridharma-empty"><i class="fa fa-inbox"></i> Belum ada data ' + type + ' tercatat.</div>';
    return;
  }
  
  var html = '<div class="modal-table-scroll"><table class="modal-custom-table">';
  
  if (type === 'pengajaran') {
    html += '<thead><tr><th width="45">No</th><th>Nama Mata Kuliah</th><th width="150">Tahun Ajaran</th><th width="120">Semester</th><th width="90" style="text-align:right;">Beban SKS</th></tr></thead><tbody>';
    items.forEach(function(item, i) {
      html += '<tr>'
            + '<td style="color:#718096;font-weight:700;">' + (i+1) + '</td>'
            + '<td style="font-weight:800;color:#1A365D;font-size:13.5px;">' + esc(item.nama_mata_kuliah || '-') + '</td>'
            + '<td><span style="background:#EBF8FF;color:#2B6CB0;padding:2px 8px;border-radius:6px;font-weight:700;font-size:12px;">' + esc(item.tahun_ajaran || '-') + '</span></td>'
            + '<td style="font-weight:700;color:#4A5568;">' + esc(item.semester || '-') + '</td>'
            + '<td style="text-align:right;font-weight:800;color:#2D3748;font-size:13.5px;">' + (item.sks || '0') + ' SKS</td>'
            + '</tr>';
    });
  } else if (type === 'penelitian') {
    html += '<thead><tr><th width="45">No</th><th>Judul Penelitian</th><th width="110">Tahun</th><th width="160">Peran</th><th>Sumber Dana</th></tr></thead><tbody>';
    items.forEach(function(item, i) {
      html += '<tr>'
            + '<td style="color:#718096;font-weight:700;">' + (i+1) + '</td>'
            + '<td style="font-weight:800;color:#1A365D;font-size:13.5px;line-height:1.4;">' + esc(item.judul_penelitian || '-') + '</td>'
            + '<td><span style="background:#FFF5F5;color:#C53030;padding:2px 8px;border-radius:6px;font-weight:700;font-size:12px;">' + esc(item.tahun || '-') + '</span></td>'
            + '<td style="font-weight:700;color:#4A5568;">' + esc(item.peran || '-') + '</td>'
            + '<td style="font-size:12.5px;color:#718096;">' + esc(item.sumber_dana || '-') + '</td>'
            + '</tr>';
    });
  } else if (type === 'pengabdian') {
    html += '<thead><tr><th width="45">No</th><th>Judul Pengabdian</th><th width="110">Tahun</th><th width="160">Peran</th><th>Mitra</th></tr></thead><tbody>';
    items.forEach(function(item, i) {
      html += '<tr>'
            + '<td style="color:#718096;font-weight:700;">' + (i+1) + '</td>'
            + '<td style="font-weight:800;color:#1A365D;font-size:13.5px;line-height:1.4;">' + esc(item.judul_pengabdian || '-') + '</td>'
            + '<td><span style="background:#F0FFF4;color:#276749;padding:2px 8px;border-radius:6px;font-weight:700;font-size:12px;">' + esc(item.tahun || '-') + '</span></td>'
            + '<td style="font-weight:700;color:#4A5568;">' + esc(item.peran || '-') + '</td>'
            + '<td style="font-size:12.5px;color:#718096;">' + esc(item.mitra || '-') + '</td>'
            + '</tr>';
    });
  }
  
  html += '</tbody></table></div>';
  el.innerHTML = html;
}

function renderModalPublikasi(items) {
  var wrap = document.getElementById('modalPublikasiWrap');
  if (!items || items.length === 0) {
    wrap.innerHTML = '<div class="tridharma-empty"><i class="fa fa-newspaper-o" style="color:#CBD5E0;"></i> Belum ada data publikasi ilmiah tercatat.</div>';
    return;
  }
  var html = '<div class="modal-table-wrap"><table class="modal-custom-table">'
           + '<thead><tr><th width="45">No</th><th>Judul Publikasi &amp; Jurnal</th><th>Jenis Publikasi</th><th>Tanggal</th><th>Penerbit / Tautan</th><th>Penulis</th></tr></thead><tbody>';
  items.forEach(function(pub, idx) {
    var links = [];
    if (pub.penerbit) links.push('<span style="color:#718096;">' + esc(pub.penerbit) + '</span>');
    if (pub.doi) links.push('<a href="' + (pub.doi.startsWith('http') ? pub.doi : 'https://doi.org/' + pub.doi) + '" target="_blank" style="color:#3182CE; font-weight:700;"><i class="fa fa-external-link"></i> DOI</a>');
    if (pub.tautan && pub.tautan !== pub.doi) links.push('<a href="' + esc(pub.tautan) + '" target="_blank" style="color:#38A169; font-weight:700;"><i class="fa fa-link"></i> URL</a>');
    
    html += '<tr>'
          + '<td style="color:#718096;font-weight:700;">' + (idx + 1) + '</td>'
          + '<td><div style="font-weight:800;color:#1A365D;font-size:13.5px;line-height:1.4;">' + esc(pub.judul || '-') + '</div>'
          + (pub.nama_jurnal ? '<div style="font-size:12px;color:#4A5568;margin-top:3px;"><i class="fa fa-book text-muted"></i> <strong>' + esc(pub.nama_jurnal) + '</strong></div>' : '')
          + '</td>'
          + '<td><span style="background:#EBF8FF;color:#2B6CB0;padding:3px 8px;border-radius:6px;font-size:11.5px;font-weight:700;display:inline-block;">' + esc(pub.jenis_publikasi || '-') + '</span></td>'
          + '<td style="font-weight:600;font-size:12.5px;">' + esc(pub.tanggal || '-') + '</td>'
          + '<td style="font-size:12px;">' + (links.length ? links.join('<br>') : '-') + '</td>'
          + '<td style="font-size:12px;color:#2D3748;max-width:250px;">' + esc(pub.penulis_list || '-') + '</td>'
          + '</tr>';
  });
  html += '</tbody></table></div>';
  wrap.innerHTML = html;
}

function renderModalBKD(bkd) {
  var wrap = document.getElementById('modalBKDWrap');
  var laporan = bkd.laporan || [];
  var kegiatan_by_smt = bkd.kegiatan_by_smt || {};

  if (!laporan.length) {
    wrap.innerHTML = '<div class="tridharma-empty"><i class="fa fa-bar-chart" style="color:#CBD5E0;"></i> Belum ada riwayat laporan BKD yang tersinkronisasi.</div>';
    return;
  }

  var html = '';
  // Card Grafik Performa BKD 5 Tahun
  html += '<div style="background:#FFFFFF; border:1px solid #E2E8F0; border-radius:12px; padding:18px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.02);">';
  html += '  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #EDF2F7;">';
  html += '    <div>';
  html += '      <div style="font-size:13px; font-weight:800; color:#1A365D; text-transform:uppercase; letter-spacing:0.6px;">';
  html += '        <i class="fa fa-line-chart" style="color:#3182CE; margin-right:8px; font-size:15px;"></i>GRAFIK TREN PERFORMA BKD (SKS TOTAL)';
  html += '      </div>';
  html += '      <div style="font-size:12px; color:#718096; font-weight:600; margin-top:2px;">';
  html += '        Riwayat capaian total beban SKS per semester (Standar Beban: 12 - 16 SKS)';
  html += '      </div>';
  html += '    </div>';
  html += '    <div style="display:flex; gap:14px; align-items:center; font-size:11.5px; font-weight:700;">';
  html += '      <span style="display:inline-flex; align-items:center; gap:5px; color:#276749;"><span style="width:10px; height:10px; border-radius:2px; background:#38A169; display:inline-block;"></span> Memenuhi (&ge;12 SKS)</span>';
  html += '      <span style="display:inline-flex; align-items:center; gap:5px; color:#9B2C2C;"><span style="width:10px; height:10px; border-radius:2px; background:#E53E3E; display:inline-block;"></span> Belum Memenuhi (&lt;12 SKS)</span>';
  html += '    </div>';
  html += '  </div>';
  html += '  <div style="position:relative; height:190px; width:100%;">';
  html += '    <canvas id="modalBkdChart"></canvas>';
  html += '  </div>';
  html += '</div>';

  // Selector Semester
  html += '<div style="background:#F7FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">';
  html += '  <div>';
  html += '    <div style="font-size:12px; font-weight:800; color:#4A5568; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:3px;"><i class="fa fa-calendar" style="color:#3182CE; margin-right:6px; font-size:14px;"></i>PILIH TAHUN AJARAN / SEMESTER</div>';
  html += '    <div style="font-size:13px; color:#718096; font-weight:600;">Lihat rincian pemenuhan beban kerja &amp; rincian kegiatan dosen</div>';
  html += '  </div>';
  html += '  <div style="min-width:260px;">';
  html += '    <select class="form-control" style="width:100%; font-size:14px; font-weight:800; color:#1A365D; border-radius:8px; border:2px solid #CBD5E0; padding:6px 14px; height:42px; background:#fff; cursor:pointer;" onchange="showModalBKDSmt(this.value)" id="modalBkdSelector">';
  laporan.forEach(function(l, i){
    html += '      <option value="'+i+'">'+esc(l.label_smt)+' ('+esc(l.stat_kewajiban)+')</option>';
  });
  html += '    </select>';
  html += '  </div>';
  html += '</div>';

  // Container Detail Semester
  html += '<div id="modal-bkd-detail-wrap">';
  var initRecap = laporan[0];
  var initDetail = (kegiatan_by_smt[initRecap.id_smt]) ? kegiatan_by_smt[initRecap.id_smt] : { ajar:[], penelitian:[], pengmas:[], tunjang:[], pendidikan:[] };
  html += renderModalBKDDetail(initDetail, initRecap);
  html += '</div>';

  wrap.innerHTML = html;

  setTimeout(function(){
    initModalBKDChart(laporan);
  }, 60);
}

function renderModalBKDDetail(detail, recap) {
  var h = '';
  var sks = parseFloat(recap.sks_total || 0);
  var pct = Math.min((sks / 16) * 100, 100).toFixed(0);
  var isMemenuhi = (recap.stat_kewajiban === 'Memenuhi' || sks >= 12);
  var color = isMemenuhi ? '#38A169' : (sks >= 6 ? '#D69E2E' : '#E53E3E');
  var statusCls = isMemenuhi ? 'hijau' : (sks >= 6 ? 'abu' : 'merah');

  h += '<div style="font-size:14px;font-weight:800;color:#1A365D;text-transform:uppercase;letter-spacing:0.6px;padding-bottom:10px;border-bottom:2px solid #EBF8FF;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">';
  h += '  <span><i class="fa fa-bullseye" style="color:#3182CE;margin-right:8px;font-size:16px;"></i>Rekapitulasi Kinerja — '+esc(recap.label_smt)+'</span>';
  h += '  <span style="font-size:13px;font-weight:700;color:#718096;text-transform:none;">Status: <strong style="color:'+color+';font-size:14px;">'+esc(recap.stat_kewajiban)+'</strong> (Asesor: <strong style="color:#2D3748;">'+esc(recap.simpulan_asesor||'-')+'</strong>)</span>';
  h += '</div>';

  // Status Box Utama
  h += '<div class="bkd-status-card '+statusCls+'" style="padding:12px 18px; margin-bottom:16px;">';
  h += '  <div class="bkd-dot '+statusCls+'" style="width:14px; height:14px;"></div>';
  h += '  <div style="flex:1;">';
  h += '    <div style="font-weight:800;font-size:15px;color:#2D3748;">'+sks.toFixed(2)+' SKS <span style="font-size:12px;font-weight:600;color:#718096;">/ Standar Beban (12 - 16 SKS)</span></div>';
  h += '    <div class="bkd-progress-bar-wrap" style="height:8px; margin-top:5px;"><div class="bkd-progress-bar" style="width:'+pct+'%;background:'+color+';"></div></div>';
  h += '  </div>';
  h += '  <div style="text-align:right;padding-left:14px;">';
  h += '    <div style="font-weight:800;font-size:14px;color:'+color+';"><i class="fa '+(isMemenuhi?'fa-check-circle':'fa-exclamation-circle')+'"></i> '+esc(recap.stat_kewajiban)+'</div>';
  h += '    <div style="font-size:11.5px;color:#718096;font-weight:600;margin-top:1px;">Simpulan Asesor: <strong style="color:#2D3748;">'+esc(recap.simpulan_asesor||'-')+'</strong></div>';
  h += '  </div>';
  h += '</div>';

  // KPI Cards per unsur
  var unsur = [
    {label:'PENGAJARAN (AJAR)', val: parseFloat(recap.sks_ajar||0), count: (detail.ajar||[]).length, color:'#3182CE', icon:'fa-book', bg:'#EBF8FF'},
    {label:'PENELITIAN (LITMAS)', val: parseFloat(recap.sks_lit||0), count: (detail.penelitian||[]).length, color:'#E53E3E', icon:'fa-flask', bg:'#FFF5F5'},
    {label:'PENGABDIAN (PENGMAS)', val: parseFloat(recap.sks_pengmas||0), count: (detail.pengmas||[]).length, color:'#38A169', icon:'fa-users', bg:'#F0FFF4'},
    {label:'PENUNJANG',  val: parseFloat(recap.sks_penunjang||0), count: (detail.tunjang||[]).length, color:'#D69E2E', icon:'fa-star', bg:'#FFFFF0'},
  ];
  if(parseFloat(recap.sks_didik||0) > 0 || (detail.pendidikan||[]).length > 0){
    unsur.push({label:'PENDIDIKAN', val: parseFloat(recap.sks_didik||0), count: (detail.pendidikan||[]).length, color:'#805AD5', icon:'fa-graduation-cap', bg:'#FAF5FF'});
  }

  h += '<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px;">';
  unsur.forEach(function(u){
    var up = Math.min((u.val/16)*100, 100).toFixed(0);
    h += '<div style="flex:1;min-width:130px;background:'+u.bg+';border-radius:10px;padding:10px 14px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 4px rgba(0,0,0,0.02);">';
    h += '  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;gap:6px;">';
    h += '    <span style="font-size:10.5px;font-weight:800;color:'+u.color+';letter-spacing:0.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fa '+u.icon+'"></i> '+u.label+'</span>';
    h += '    <span style="font-size:10.5px;font-weight:700;color:#718096;white-space:nowrap;flex-shrink:0;">'+u.count+' Keg.</span>';
    h += '  </div>';
    h += '  <div style="font-size:18px;font-weight:800;color:#2D3748;margin-bottom:4px;">'+u.val.toFixed(2)+' <span style="font-size:11.5px;font-weight:700;color:#718096;">SKS</span></div>';
    h += '  <div class="bkd-progress-bar-wrap" style="height:6px;margin-top:2px;"><div class="bkd-progress-bar" style="width:'+up+'%;background:'+u.color+';"></div></div>';
    h += '</div>';
  });
  h += '</div>';

  // Rincian kegiatan per unsur
  var sections = [
    {title:'1. Pelaksanaan Perkuliahan & Pengajaran (Ajar)', icon:'fa-book', data: detail.ajar||[], color:'#3182CE'},
    {title:'2. Pelaksanaan Penelitian & Publikasi (Litmas)', icon:'fa-flask', data: detail.penelitian||[], color:'#E53E3E'},
    {title:'3. Pelaksanaan Pengabdian Kepada Masyarakat (Pengmas)', icon:'fa-users', data: detail.pengmas||[], color:'#38A169'},
    {title:'4. Pelaksanaan Penunjang Tridharma (Tunjang)', icon:'fa-star', data: detail.tunjang||[], color:'#D69E2E'},
    {title:'5. Pendidikan & Pengembangan Diri', icon:'fa-graduation-cap', data: detail.pendidikan||[], color:'#805AD5'},
  ];

  sections.forEach(function(s){
    h += '<div style="margin-bottom:24px;">';
    h += '  <div style="font-size:13.5px;font-weight:800;color:#2D3748;text-transform:uppercase;letter-spacing:0.5px;padding:6px 0 10px;border-bottom:2px solid #EBF8FF;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">';
    h += '    <span><i class="fa '+s.icon+'" style="color:'+s.color+';margin-right:8px;font-size:15px;"></i>'+s.title+'</span>';
    h += '    <span style="font-size:12px;font-weight:800;color:'+(s.data.length ? s.color : '#CBD5E0')+';">'+s.data.length+' ITEM</span>';
    h += '  </div>';
    
    if(!s.data.length){
      h += '  <div style="font-size:13px;color:#A0AEC0;font-weight:600;padding:12px 16px;background:#F7FAFC;border-radius:8px;border:1px dashed #E2E8F0;text-align:center;"><i class="fa fa-info-circle" style="margin-right:6px;"></i>Tidak ada kegiatan tercatat pada unsur ini di semester '+esc(recap.label_smt)+'.</div>';
    } else {
      h += '  <div class="modal-table-wrap" style="overflow:visible;"><table class="modal-custom-table"><thead><tr><th width="45">#</th><th>Nama Kegiatan</th><th>Kategori Kegiatan</th><th width="120" style="text-align:right;">Beban SKS</th><th width="90" style="text-align:right;">Nilai</th></tr></thead><tbody>';
      s.data.forEach(function(item, i){
        h += '    <tr>'
           + '      <td style="color:#718096;font-weight:700;">'+(i+1)+'</td>'
           + '      <td style="font-weight:700;color:#1A365D;font-size:13.5px;line-height:1.45;">'+esc(item.judul_keg||'-')+'</td>'
           + '      <td style="font-size:12.5px;color:#4A5568;line-height:1.4;">'+esc(item.nm_kat||'-')+'</td>'
           + '      <td style="font-weight:800;color:#2D3748;text-align:right;font-size:13.5px;">'+parseFloat(item.beban_sks||0).toFixed(2)+'</td>'
           + '      <td style="text-align:right;color:#718096;font-weight:700;font-size:13px;">'+parseFloat(item.nilai||0).toFixed(2)+'</td>'
           + '    </tr>';
      });
      h += '  </tbody></table></div>';
    }
    h += '</div>';
  });

  return h;
}

function showModalBKDSmt(idx) {
  if (currentModalBkd && currentModalBkd.laporan) {
    var laporan = currentModalBkd.laporan;
    var kegiatan_by_smt = currentModalBkd.kegiatan_by_smt || {};
    var recap = laporan[idx];
    if (recap) {
      var detail = (kegiatan_by_smt[recap.id_smt]) ? kegiatan_by_smt[recap.id_smt] : { ajar:[], penelitian:[], pengmas:[], tunjang:[], pendidikan:[] };
      var wrap = document.getElementById('modal-bkd-detail-wrap');
      if (wrap) {
        wrap.innerHTML = renderModalBKDDetail(detail, recap);
      }
    }
  }
}

function initModalBKDChart(laporan) {
  if (!window.Chart || !laporan || !laporan.length) return;
  var canvas = document.getElementById('modalBkdChart');
  if (!canvas) return;

  if (modalBkdChartInstance) {
    modalBkdChartInstance.destroy();
    modalBkdChartInstance = null;
  }

  var last5Years = laporan.slice(0, 10);
  var chronoLaporan = last5Years.slice().reverse();

  var labels = chronoLaporan.map(function(l){ return l.id_smt ? String(l.id_smt) : l.label_smt; });
  var dataSks = chronoLaporan.map(function(l){ return parseFloat(l.sks_total || 0); });
  var bgColors = chronoLaporan.map(function(l){
    var s = parseFloat(l.sks_total || 0);
    return (l.stat_kewajiban === 'Memenuhi' || s >= 12) ? 'rgba(56, 161, 105, 0.85)' : 'rgba(229, 62, 62, 0.85)';
  });
  var borderColors = chronoLaporan.map(function(l){
    var s = parseFloat(l.sks_total || 0);
    return (l.stat_kewajiban === 'Memenuhi' || s >= 12) ? '#276749' : '#9B2C2C';
  });

  modalBkdChartInstance = new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Total SKS Akhir',
          data: dataSks,
          backgroundColor: bgColors,
          borderColor: borderColors,
          borderWidth: 1.5,
          borderRadius: 6,
          borderSkipped: false,
          barPercentage: 0.45
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(26, 54, 93, 0.95)',
          titleFont: { size: 12.5, weight: 'bold', family: 'Nunito' },
          bodyFont: { size: 11.5, family: 'Nunito' },
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            title: function(items){
              var idx = items[0].dataIndex;
              var lap = chronoLaporan[idx];
              return lap.label_smt + ' (' + (lap.id_smt || '-') + ')';
            },
            label: function(item){
              var idx = item.dataIndex;
              var lap = chronoLaporan[idx];
              return [
                ' Total Beban SKS: ' + parseFloat(lap.sks_total||0).toFixed(2) + ' SKS',
                ' Status Kewajiban: ' + lap.stat_kewajiban + ' (Asesor: ' + (lap.simpulan_asesor||'-') + ')'
              ];
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          suggestedMax: 20,
          grid: { color: 'rgba(226, 232, 240, 0.8)', borderDash: [4, 4] },
          ticks: {
            font: { size: 11, weight: '700', family: 'Nunito' },
            color: '#718096',
            callback: function(v){ return v + ' SKS'; }
          }
        },
        x: {
          grid: { display: false },
          ticks: { font: { size: 11.5, weight: '800', family: 'Nunito' }, color: '#2D3748' }
        }
      },
      onClick: function(e, elements){
        if (elements && elements.length > 0) {
          var clickedChronoIdx = elements[0].index;
          var clickedItem = chronoLaporan[clickedChronoIdx];
          var originalIdx = laporan.findIndex(function(l){ return l.id_smt === clickedItem.id_smt; });
          if (originalIdx !== -1) {
            var sel = document.getElementById('modalBkdSelector');
            if (sel) {
              sel.value = originalIdx;
              showModalBKDSmt(originalIdx);
            }
          }
        }
      }
    }
  });
}

function switchMainTab(type, el) {
  document.querySelectorAll('.main-tab').forEach(function(t){ t.classList.remove('active'); });
  document.querySelectorAll('.main-pane').forEach(function(p){ p.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('tab-' + type).classList.add('active');

  // Trigger Chart resize if switching to BKD
  if (type === 'bkd' && modalBkdChartInstance) {
    setTimeout(function(){
      modalBkdChartInstance.resize();
    }, 50);
  }
}

function switchTridharma(type, el) {
  document.querySelectorAll('.tridharma-tab').forEach(function(t){ t.classList.remove('active'); });
  document.querySelectorAll('.tridharma-pane').forEach(function(p){ p.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('tridharma-' + type).classList.add('active');
}

function getInitials(nama) {
  if (!nama) return '?';
  var parts = nama.trim().split(/\s+/);
  return parts.length === 1 ? parts[0][0].toUpperCase() : (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
}

function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function showError(msg) {
  document.getElementById('dosenGrid').innerHTML =
    '<div style="grid-column:1/-1;text-align:center;padding:60px 0;font-family:\'Nunito\',sans-serif;">'
    + '<i class="fa fa-exclamation-circle" style="font-size:34px;color:#E53E3E;margin-bottom:10px;display:block;"></i>'
    + '<div style="font-weight:800;font-size:15px;color:#E53E3E;margin-bottom:4px;">Gagal Memuat Data</div>'
    + '<div style="font-size:13px;color:#718096;">' + msg + '</div></div>';
}
</script>
