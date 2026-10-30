<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ======================================================
       INIT LOADING OVERLAY — Progress Bar
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
</style>

<!-- =====================================================================
     INIT LOADING OVERLAY
     ===================================================================== -->
<div id="init-loading-overlay">
    <div class="init-loading-box">
        <div class="init-logo-area">
            <div class="init-icon">
                <i class="fa fa-users"></i>
            </div>
            <h2>Dashboard Perwalian</h2>
            <p>Menyiapkan analitik &amp; prediksi mahasiswa...</p>
        </div>

        <div class="init-progress-container">
            <div class="init-progress-labels">
                <span class="init-step-label active" id="lbl-init">Inisiasi</span>
                <span class="init-step-label" id="lbl-atas">Panel Atas</span>
                <span class="init-step-label" id="lbl-bawah">Panel Bawah</span>
            </div>

            <div class="init-progress-track">
                <div class="init-progress-bar" id="init-progress-bar" style="width: 10%;"></div>
            </div>
            <div class="init-progress-pct" id="init-progress-pct">10%</div>
            <div class="init-status-text" id="init-status-text">Memulai proses...</div>

            <div class="init-steps-row">
                <div class="init-step-chip loading" id="chip-init">
                    <i class="fa fa-power-off step-icon"></i>
                    <span class="step-name">Init</span>
                </div>
                <div class="init-step-chip" id="chip-atas">
                    <i class="fa fa-bar-chart step-icon"></i>
                    <span class="step-name">Chart</span>
                </div>
                <div class="init-step-chip" id="chip-bawah">
                    <i class="fa fa-table step-icon"></i>
                    <span class="step-name">Data</span>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
/* BS5 Mock Utilities & Styles to override Metronic temporarily */
.bs5-dashboard { font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; padding: 1.5rem; margin: -25px -20px 0 -20px; min-height: calc(100vh - 50px); }
.bs5-card { background: #fff; border-radius: 1rem; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); margin-bottom: 1.5rem; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; height: calc(100% - 1.5rem); display: flex; flex-direction: column; }
.bs5-card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2); }
.bs5-card-body { padding: 1.25rem; }
.bs5-card-header { padding: 1rem 1.5rem; background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
.bs5-card-title { margin-bottom: 0; font-weight: 700; color: #4e73df; text-transform: uppercase; letter-spacing: 0.05em; }
.bs5-d-flex { display: flex; }
.bs5-align-items-center { align-items: center; }
.bs5-justify-content-between { justify-content: space-between; }
.bs5-icon-box { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: rgba(255, 255, 255, 0.85); flex-shrink: 0; }
.bs5-bg-primary-gradient { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
.bs5-bg-success-gradient { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
.bs5-bg-warning-gradient { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
.bs5-bg-danger-gradient { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
.bs5-bg-info-gradient { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
.bs5-text-muted { color: #858796; font-weight: 700; text-transform: uppercase; font-size: 1.2rem; }
.bs5-h3 { margin: 0; font-size: 2.4rem; font-weight: 700; color: #5a5c69; margin-top: 5px; }
.bs5-kpi-subtext { font-size: 1.5rem; font-weight: normal; }
.bs5-text-success { color: #1cc88a; }
.bs5-table { width: 100%; border-collapse: collapse; }
.bs5-table th { border-bottom: 2px solid #e3e6f0; padding: 0.75rem; color: #4e73df; font-weight: 700; text-transform: uppercase; text-align: left; }
.bs5-table td { padding: 0.75rem; border-bottom: 1px solid #e3e6f0; vertical-align: middle; color: #5a5c69; }
.bs5-table tbody tr:hover { background-color: #f8f9fc; }
.bs5-badge { padding: 0.4em 0.7em; font-size: 0.85em; font-weight: 700; border-radius: 10rem; color: #fff; display: inline-block; }
.bs5-badge-success { background-color: #1cc88a; }
.bs5-badge-danger { background-color: #e74a3b; }
.bs5-badge-warning { background-color: #f6c23e; }
.bs5-badge-primary { background-color: #4e73df; }
.bs5-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid #eaecf4; }
.bs5-table-wrapper { max-height: 400px; overflow-y: auto; border: 1px solid #eaecf4; border-radius: 0.5rem; }
.bs5-nav-tabs { border-bottom: 2px solid #eaecf4; display: flex; list-style: none; padding: 0; margin: 0 0 1.5rem 0; flex-wrap: wrap; }
.bs5-nav-tabs li { margin-bottom: -2px; }
.bs5-nav-tabs a { display: block; padding: 0.75rem 1.25rem; color: #858796; text-decoration: none; border-bottom: 2px solid transparent; font-weight: 600; transition: all 0.2s ease-in-out; }
.bs5-nav-tabs a:hover { color: #4e73df; background-color: #f8f9fc; border-radius: 0.5rem 0.5rem 0 0; }
.bs5-nav-tabs li.active a { color: #4e73df; border-bottom-color: #4e73df; }
.bs5-row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; align-items: stretch; }
.bs5-col { position: relative; width: 100%; padding-right: 15px; padding-left: 15px; }
@media (min-width: 992px) {
  .bs5-col-lg-3 { flex: 0 0 25%; max-width: 25%; }
  .bs5-col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
  .bs5-col-lg-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
  .bs5-col-lg-6 { flex: 0 0 50%; max-width: 50%; }
  .bs5-col-lg-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
}

.bs5-title-aman { color: #1cc88a; font-weight: 700; margin-bottom: 0.2rem; }
.bs5-title-berisiko { color: #e74a3b; font-weight: 700; margin-bottom: 0.2rem; }
.bs5-desc-kriteria { color: #858796; margin-bottom: 1rem; }
.bs5-indicator-primary { color: #4e73df; font-weight: 600; margin-top: 2px; display: inline-block; }
.bs5-indicator-danger { color: #e74a3b; font-weight: 600; margin-top: 2px; display: inline-block; }
.bs5-indicator-success { color: #1cc88a; font-weight: 600; margin-top: 2px; display: inline-block; }

.bs5-cursor-pointer { cursor: pointer; transition: transform 0.2s ease; }
.bs5-cursor-pointer:hover { transform: scale(1.1); box-shadow: 0 0.15rem 1rem rgba(0,0,0,0.15); z-index: 2; position: relative; }

.bs5-skeleton {
    background: #f6f7f8;
    background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
    background-size: 800px 104px;
    animation: bs5-shimmer 1.5s linear infinite forwards;
}
@keyframes bs5-shimmer {
    0% { background-position: -468px 0; }
    100% { background-position: 468px 0; }
}


</style>

<div class="page-content">
<div class="bs5-dashboard">
    
    <div class="bs5-row">
        <!-- KPI 1 -->
        <div class="bs5-col bs5-col-lg-3">
            <div class="bs5-card">
                <div class="bs5-card-body bs5-d-flex bs5-align-items-center bs5-justify-content-between">
                    <div>
                        <div class="bs5-text-muted">Total Mhs Bimbingan</div>
                        <div class="bs5-h3"><?= $data['kpi']['total_bimbingan']; ?></div>
                    </div>
                    <div class="bs5-icon-box bs5-bg-primary-gradient">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KPI 2 -->
        <div class="bs5-col bs5-col-lg-3">
            <div class="bs5-card">
                <div class="bs5-card-body bs5-d-flex bs5-align-items-center bs5-justify-content-between">
                    <div>
                        <div class="bs5-text-muted">KRS Disetujui</div>
                        <div class="bs5-h3"><?= $data['kpi']['sudah_krs']; ?> <span class="bs5-kpi-subtext bs5-text-success">(<?= $data['kpi']['persentase_krs']; ?>%)</span></div>
                    </div>
                    <div class="bs5-icon-box bs5-bg-success-gradient">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KPI 3 -->
        <div class="bs5-col bs5-col-lg-3">
            <div class="bs5-card">
                <div class="bs5-card-body bs5-d-flex bs5-align-items-center bs5-justify-content-between">
                    <div>
                        <div class="bs5-text-muted">Belum KRS dan Pending</div>
                        <div class="bs5-h3"><?= $data['kpi']['belum_krs']; ?> <span class="bs5-kpi-subtext">Belum</span> / <?= $data['kpi']['pending_krs']; ?> <span class="bs5-kpi-subtext">Pending</span></div>
                    </div>
                    <div class="bs5-icon-box bs5-bg-danger-gradient">
                        <i class="fa fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KPI 4 -->
        <div class="bs5-col bs5-col-lg-3">
            <div class="bs5-card">
                <div class="bs5-card-body bs5-d-flex bs5-align-items-center bs5-justify-content-between">
                    <div>
                        <div class="bs5-text-muted">Rata-rata IPK</div>
                        <div class="bs5-h3"><?= $data['kpi']['avg_ipk']; ?></div>
                    </div>
                    <div class="bs5-icon-box bs5-bg-warning-gradient">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bs5-row">
        <!-- Bar Chart KRS -->
        <div class="bs5-col bs5-col-lg-6">
            <div class="bs5-card">
                <div class="bs5-card-header">
                    <h6 class="bs5-card-title">Status KRS per Angkatan</h6>
                </div>
                <div class="bs5-card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="krsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- K-Means Clustering -->
        <div class="bs5-col bs5-col-lg-6">
            <div class="bs5-card">
                <div class="bs5-card-header">
                    <h6 class="bs5-card-title">Clustering Prestasi</h6>
                </div>
                <div class="bs5-card-body" style="flex: 1;">
                    <div class="bs5-row">
                        <div class="bs5-col bs5-col-lg-5">
                            <div style="position: relative; height: 300px; width: 100%; display: flex; align-items: center;">
                                <canvas id="clusterChart"></canvas>
                            </div>
                        </div>
                        <div class="bs5-col bs5-col-lg-7">
                            <div style="max-height: 300px; overflow-y: auto; padding-right: 10px;">
                                <div id="clusteringListContainer">
                                    <div class="bs5-skeleton" style="height: 60px; border-radius: 8px; margin-bottom: 10px;"></div>
                                    <div class="bs5-skeleton" style="height: 60px; border-radius: 8px; margin-bottom: 10px;"></div>
                                    <div class="bs5-skeleton" style="height: 60px; border-radius: 8px; margin-bottom: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bs5-row">
        <div class="bs5-col" style="width:100%;">
            <div class="bs5-card">
                <div class="bs5-card-header">
                    <h6 class="bs5-card-title" style="line-height: 1.5;">Prediksi Risiko Mahasiswa Bimbingan <br><span style="font-size: 0.75rem; font-weight: 400; color: #858796; text-transform: none;">Hanya untuk mahasiswa yang sudah KRS</span></h6>
                </div>
                <div class="bs5-card-body">
                    <ul class="bs5-nav-tabs" id="prediksiTabs">
                        <li class="active"><a href="#tab_1_1" data-toggle="tab" data-tab-key="tahun_1">Tahun Pertama (Tgt IPK: 2.50)</a></li>
                        <li><a href="#tab_1_2" data-toggle="tab" data-tab-key="tahun_2">Tahun Kedua (Tgt IPK: 2.75)</a></li>
                        <li><a href="#tab_1_3" data-toggle="tab" data-tab-key="tahun_3">Tahun Ketiga (Tgt IPK: 2.75)</a></li>
                        <li><a href="#tab_1_4" data-toggle="tab" data-tab-key="tahun_akhir">Tahun Akhir</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <?php 
                            $tabs = ['tahun_1', 'tahun_2', 'tahun_3', 'tahun_akhir'];
                            foreach($tabs as $idx => $tab_key): 
                        ?>
                        <div class="tab-pane fade <?= $idx == 0 ? 'active in' : '' ?>" id="tab_1_<?= $idx + 1 ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="bs5-title-aman"><i class="fa fa-check-circle"></i> Aman (On Track)</h4>
                                    <?php if($tab_key == 'tahun_1'): ?>
                                        <p class="bs5-desc-kriteria">Mahasiswa dengan IPK &ge; 2.50</p>
                                    <?php elseif($tab_key == 'tahun_2' || $tab_key == 'tahun_3'): ?>
                                        <p class="bs5-desc-kriteria">Mahasiswa dengan IPK &ge; 2.75</p>
                                    <?php else: ?>
                                        <p class="bs5-desc-kriteria">Masa studi &le; 4 tahun dengan IPK &ge; 2.75</p>
                                    <?php endif; ?>
                                    <div class="bs5-table-wrapper">
                                        <table class="bs5-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">No.</th>
                                                    <th style="width: auto;">NIM & Nama</th>
                                                    <?php if($tab_key == 'tahun_1'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn1</th>
                                                    <?php elseif($tab_key == 'tahun_2'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn1</th>
                                                        <th style="width: 4%; text-align: center;"></th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn2</th>
                                                    <?php elseif($tab_key == 'tahun_3'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn1</th>
                                                        <th style="width: 2%; text-align: center;"></th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn2</th>
                                                        <th style="width: 2%; text-align: center;"></th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn3</th>
                                                    <?php else: ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_aman_<?= $tab_key ?>">
                                                <tr>
                                                    <td colspan="5" style="text-align:center; padding: 2rem;">
                                                        <i class="fa fa-spinner fa-spin" style="font-size: 2rem; color: #4e73df;"></i>
                                                        <div style="margin-top: 10px; color: #858796;">Memuat data...</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4 class="bs5-title-berisiko"><i class="fa fa-exclamation-triangle"></i> Berisiko (At Risk)</h4>
                                    <?php if($tab_key == 'tahun_1'): ?>
                                        <p class="bs5-desc-kriteria">Mahasiswa dengan IPK &lt; 2.50</p>
                                    <?php elseif($tab_key == 'tahun_2' || $tab_key == 'tahun_3'): ?>
                                        <p class="bs5-desc-kriteria">Mahasiswa dengan IPK &lt; 2.75</p>
                                    <?php else: ?>
                                        <p class="bs5-desc-kriteria">Masa studi &gt; 4 tahun (lewat batas) atau IPK &lt; 2.75</p>
                                    <?php endif; ?>
                                    <div class="bs5-table-wrapper">
                                        <table class="bs5-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">No.</th>
                                                    <th style="width: auto;">NIM & Nama</th>
                                                    <?php if($tab_key == 'tahun_1'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn1</th>
                                                    <?php elseif($tab_key == 'tahun_2'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn1</th>
                                                        <th style="width: 4%; text-align: center;"></th>
                                                        <th style="width: 10%; text-align: center;">IPK Thn2</th>
                                                    <?php elseif($tab_key == 'tahun_3'): ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn1</th>
                                                        <th style="width: 2%; text-align: center;"></th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn2</th>
                                                        <th style="width: 2%; text-align: center;"></th>
                                                        <th style="width: 7%; text-align: center;">IPK Thn3</th>
                                                    <?php else: ?>
                                                        <th style="width: 13%; text-align: center;">Rating</th>
                                                        <th style="width: 10%; text-align: center;">IPK</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_berisiko_<?= $tab_key ?>">
                                                <tr>
                                                    <td colspan="5" style="text-align:center; padding: 2rem;">
                                                        <i class="fa fa-spinner fa-spin" style="font-size: 2rem; color: #e74a3b;"></i>
                                                        <div style="margin-top: 10px; color: #858796;">Memuat data...</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabel Anomali Data Akademik -->
    <div class="bs5-row" style="margin-top: 1.5rem;">
        <div class="bs5-col" style="width:100%;">
            <div class="bs5-card" style="border-left: 4px solid #f6c23e;">
                <div class="bs5-card-header" style="background-color: #fff3cd; color: #856404; border-bottom: 1px solid #ffeeba;">
                    <h6 class="bs5-card-title"><i class="fa fa-exclamation-triangle"></i> Mahasiswa dengan Anomali Data Akademik (KRS Kelang / Nilai Tidak Masuk)</h6>
                </div>
                <div class="bs5-card-body">
                    <div class="bs5-table-wrapper" style="max-height: 400px; overflow-y: auto;">
                        <table class="bs5-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: 30%;">NIM & Nama Mahasiswa</th>
                                    <th style="width: 65%;">Detail Peringatan</th>
                                </tr>
                            </thead>
                                <tbody id="anomaliTbody">
                                    <tr>
                                        <td colspan="3" style="padding: 0;">
                                            <div class="bs5-skeleton" style="height: 45px; border-radius: 4px; margin: 5px 10px;"></div>
                                            <div class="bs5-skeleton" style="height: 45px; border-radius: 4px; margin: 5px 10px;"></div>
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Mahasiswa Belum KRS -->
    <div class="bs5-row" style="margin-top: 1.5rem;">
        <div class="bs5-col" style="width:100%;">
            <div class="bs5-card">
                <div class="bs5-card-header">
                    <h6 class="bs5-card-title" id="titleBelumKrs"><i class="fa fa-exclamation-circle"></i> Mahasiswa Belum KRS (Semester Ini) - <i class="fa fa-spinner fa-spin" style="margin-left: 5px;"></i> Mahasiswa</h6>
                </div>
                <div class="bs5-card-body">
                    <div class="bs5-table-wrapper" style="max-height: 400px; overflow-y: auto;">
                        <table class="bs5-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: auto;">NIM & Nama Mahasiswa</th>
                                    <th style="width: 25%;">Keterangan</th>
                                    <th style="width: 10%; text-align: center;">Terakhir KRS</th>
                                    <th style="width: 10%; text-align: center;">Total SKS</th>
                                    <th style="width: 10%; text-align: center;">IPK Terakhir</th>
                                </tr>
                            </thead>
                            <tbody id="belumKrsTbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Photo & Analysis Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 95%; max-width: 1200px;">
        <div class="modal-content" style="border-radius: 1rem; overflow: hidden; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: #2c3e50; color: white; padding: 15px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 1; text-shadow: none; margin-top: 2px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="font-weight: 600; margin:0; color:#fff;">Detail Profil Mahasiswa</h5>
            </div>
            <div class="modal-body" style="padding: 0; background: #fff;">
                <div class="bs5-row" style="margin: 0; min-height: 550px; display: flex; flex-wrap: wrap;">
                    
                    <!-- Left Sidebar: Profile Info -->
                    <div class="bs5-col" style="flex: 0 0 33.3333%; max-width: 33.3333%; background: #f8f9fa; padding: 25px 20px; border-right: 1px solid #eaecf4;">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div id="photoSkeleton" class="bs5-skeleton" style="width: 140px; height: 140px; border-radius: 8px; margin: 0 auto;"></div>
                            <img id="photoModalImg" src="" style="width: 140px; height: 140px; object-fit: cover; border-radius: 8px; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: none; margin: 0 auto;" onload="document.getElementById('photoSkeleton').style.display='none'; this.style.display='inline-block';">
                        </div>
                        
                        <div style="font-size: 13px;">
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">Nama Lengkap:</label>
                                <span id="photoModalName" style="font-weight: 700; color: #3a3b45; line-height: 1.3; display: block;">-</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">NIM:</label>
                                <span id="photoModalNim" style="font-weight: 600; color: #4e73df; display: block;">-</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">IPK Terakhir:</label>
                                <span id="modalLastIpk" style="font-weight: 800; color: #1cc88a; font-size: 18px; display: block;">-</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">Status KIP:</label>
                                <div id="modalKipStatus">-</div>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">Semester Tempuh:</label>
                                <span id="modalSemester" style="color: #5a5c69; font-weight: 600; display: block;">-</span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; color: #858796; display: block; margin-bottom: 2px; font-weight:700;">Masa Studi:</label>
                                <span id="modalMasaStudi" style="color: #5a5c69; font-weight: 600; display: block;">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Area: Tabs -->
                    <div class="bs5-col" style="flex: 0 0 66.6666%; max-width: 66.6666%; padding: 20px;">
                        <ul class="nav nav-tabs bs5-nav-tabs" id="modalDetailTabs" style="border-bottom: 2px solid #eaecf4; margin-bottom: 20px;">
                            <li class="active"><a data-toggle="tab" href="#modal_tab_tren" style="font-weight:700; color:#4e73df;"><i class="fa fa-line-chart"></i> Tren Akademik</a></li>
                            <li><a data-toggle="tab" href="#modal_tab_skill" style="font-weight:700; color:#1cc88a;"><i class="fa fa-star"></i> Skill & Bahasa</a></li>
                            <li><a data-toggle="tab" href="#modal_tab_mk_gagal" style="font-weight:700; color:#e74a3b;"><i class="fa fa-exclamation-triangle"></i> MK Perhatian (D/E)</a></li>
                            <li><a data-toggle="tab" href="#modal_tab_narasi" style="font-weight:700; color:#f6c23e;"><i class="fa fa-commenting-o"></i> Narasi Akademik</a></li>
                        </ul>
                        
                        <div class="tab-content" style="position: relative; min-height: 500px;">
                            
                            <!-- Loading overlay for right side -->
                            <div id="modalLoadingOverlay" style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.9); z-index:10; display:flex; flex-direction:column; align-items:center; justify-content:center; padding-top: 50px;">
                                <i class="fa fa-spinner fa-spin" style="font-size: 3rem; color: #4e73df;"></i>
                                <div style="margin-top: 15px; font-weight:600; color: #858796;">Mengambil data...</div>
                            </div>
                            
                            <!-- TAB: Tren Akademik -->
                            <div id="modal_tab_tren" class="tab-pane fade in active">
                                <div id="ips_chart" style="width: 100%; min-height: 250px;"></div>
                            </div>
                            
                            <!-- TAB: Skill & Bahasa -->
                            <div id="modal_tab_skill" class="tab-pane fade">
                                <h5 style="font-weight: 800; color: #4e73df; font-size: 1.1rem; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.5px;">
                                    <i class="fa fa-lightbulb-o"></i> Mindset Entrepreneurship
                                </h5>
                                <div id="mindsetGrades" style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 1rem;"></div>
                                <div id="mindsetConclusion" style="font-size: 1.1rem; color: #4a4a5a; line-height: 1.6; background: #f8f9fc; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #4e73df; margin-bottom: 2rem;"></div>
                                
                                <h5 style="font-weight: 800; color: #1cc88a; font-size: 1.1rem; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.5px;">
                                    <i class="fa fa-language"></i> Kemampuan Bahasa
                                </h5>
                                <div id="langGrades" style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 1rem;"></div>
                                <div id="langConclusion" style="font-size: 1.1rem; color: #4a4a5a; line-height: 1.6; background: #f8f9fc; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #1cc88a;"></div>
                            </div>
                            
                            <!-- TAB: MK D/E -->
                            <div id="modal_tab_mk_gagal" class="tab-pane fade">
                                <div id="failedCoursesContainer"></div>
                            </div>
                            
                            <!-- TAB: Narasi Akademik -->
                            <div id="modal_tab_narasi" class="tab-pane fade">
                                <div style="background:#fff9e6; border: 1px solid #ffe8a1; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                                    <h5 style="margin-top:0; color: #b08d00; font-weight:700;"><i class="fa fa-lightbulb-o"></i> Ringkasan Eksekutif Akademik</h5>
                                    <p style="margin-bottom:0; color: #856a00; font-size:13px;">Narasi ini dibuat secara otomatis berdasarkan pencapaian akademik mahasiswa hingga saat ini, ditujukan untuk membantu Dosen PA dalam memberikan arahan yang tepat.</p>
                                </div>
                                <div id="narasiAkademikText" style="min-height: 270px; padding: 20px; line-height: 1.8; font-size: 14px; color: #34495e; background: #fff; border: 1px solid #eee; border-radius: 4px; text-align: justify;">Memuat narasi akademik...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #eaecf4; padding: 15px 20px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-weight: 600; border-radius: 6px;"><i class="fa fa-times"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Ensure ApexCharts is loaded
if (typeof ApexCharts === 'undefined') {
    var script = document.createElement('script');
    script.src = "https://cdn.jsdelivr.net/npm/apexcharts";
    document.head.appendChild(script);
}

var ipsChartObj = null;

function showPhotoModal(url, name, nim) {
    // Reset basic UI
    document.getElementById('photoModalImg').style.display = 'none';
    document.getElementById('photoSkeleton').style.display = 'block';
    document.getElementById('photoModalImg').src = url;
    document.getElementById('photoModalName').innerText = name;
    document.getElementById('photoModalNim').innerText = nim;
    
    // Reset and show loading overlay
    document.getElementById('modalLoadingOverlay').style.display = 'flex';
    document.getElementById('modalLoadingOverlay').innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size: 3rem; color: #4e73df;"></i><div style="margin-top: 15px; font-weight:600; color: #858796;">Mengambil data...</div>';
    document.getElementById('modalLastIpk').innerText = '-';
    document.getElementById('modalKipStatus').innerHTML = '-';
    document.getElementById('modalSemester').innerText = '-';
    document.getElementById('modalMasaStudi').innerText = '-';
    
    // Reset Tabs
    $('#modalDetailTabs a[href="#modal_tab_tren"]').tab('show');
    if (ipsChartObj) {
        ipsChartObj.destroy();
        ipsChartObj = null;
    }
    document.getElementById('failedCoursesContainer').innerHTML = '';
    document.getElementById('narasiAkademikText').value = '';
    
    $('#photoModal').modal('show');
    
    // Fetch data
    $.ajax({
        url: '<?= base_url('Lecturer_dashboard_perwalian/ajax_get_student_detail') ?>',
        type: 'POST',
        data: { 
            nipd: nim,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
            document.getElementById('modalLoadingOverlay').style.display = 'none';
            if (res.status === 'success') {
                let data = res.data;
                
                // Sidebar update
                document.getElementById('photoModalImg').src = data.pic;
                document.getElementById('modalLastIpk').innerText = data.last_ipk;
                document.getElementById('modalKipStatus').innerHTML = data.kip_status;
                document.getElementById('modalSemester').innerText = data.semester_tempuh + ' Semester';
                document.getElementById('modalMasaStudi').innerText = data.masa_studi_text;
                
                // Chart update (Tab 1)
                setTimeout(() => {
                    var options = {
                        series: [{ name: 'IPS', data: data.ips_values }, { name: 'IPK', data: data.ipk_values }],
                        chart: { type: 'bar', height: 250, toolbar: { show: false } },
                        plotOptions: { 
                            bar: { 
                                horizontal: false, 
                                columnWidth: '55%', 
                                endingShape: 'rounded',
                                dataLabels: { position: 'top' } 
                            } 
                        },
                        dataLabels: { 
                            enabled: true,
                            offsetY: -20,
                            style: {
                                fontSize: '10px',
                                colors: ['#3a3b45']
                            },
                            formatter: function (val) {
                                return parseFloat(val).toFixed(2);
                            }
                        },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: { categories: data.ips_labels },
                        fill: { opacity: 1 },
                        colors: ['#4e73df', '#1cc88a']
                    };
                    ipsChartObj = new ApexCharts(document.querySelector("#ips_chart"), options);
                    ipsChartObj.render();
                }, 200);
                
                // Skill & Bahasa update (Tab 2)
                let skillData = data.skill_data;
                if(skillData) {
                    // Generate Badges Mindset
                    let badgesHtml = '';
                    if (skillData.grades.length > 0) {
                        skillData.grades.forEach(function(g) {
                            let badgeColor = '#858796'; // gray default
                            if(g.nilai_huruf) {
                                if(g.nilai_indeks >= 3.5) badgeColor = '#1cc88a'; // green
                                else if(g.nilai_indeks >= 2.5) badgeColor = '#f6c23e'; // yellow
                                else badgeColor = '#e74a3b'; // red
                            }
                            let mkLabel = g.kode_e || 'E?';
                            let badgeTitle = g.nm_mk;
                            badgesHtml += `
                            <div style="display: inline-flex; flex-direction: column; align-items: center; margin-right: 15px; margin-bottom: 15px; width: 110px;">
                                <div style="background-color: ${badgeColor}; color: #fff; padding: 1rem; border-radius: 0.75rem; display: inline-flex; flex-direction: column; align-items: center; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: default;" title="${badgeTitle}">
                                    <span style="font-size: 1.2rem; font-weight: 800; opacity: 0.9; letter-spacing: 2px;">${mkLabel}</span>
                                    <span style="font-size: 2.5rem; font-weight: 900; line-height: 1.2;">${g.nilai_huruf || '-'}</span>
                                    <span style="font-size: 1rem; opacity: 0.85; margin-top: 4px;">${g.nilai_huruf ? parseFloat(g.nilai_indeks).toFixed(2) : 'Blm Ada'}</span>
                                </div>
                                <span style="font-size: 1.1rem; font-weight: 600; text-align: center; color: #555; margin-top: 5px; line-height: 1.2;">${badgeTitle}</span>
                            </div>`;
                        });
                    } else {
                        badgesHtml = '<p style="color: #858796; font-size: 1.1rem; font-style: italic; text-align: center; margin: 1rem 0;">Belum ada data nilai mata kuliah entrepreneurship.</p>';
                    }
                    document.getElementById('mindsetGrades').innerHTML = badgesHtml;
                    document.getElementById('mindsetConclusion').innerHTML = skillData.conclusion;
                    
                    // Generate Language Badges
                    let langHtml = '';
                    if (skillData.lang_grades && skillData.lang_grades.length > 0) {
                        skillData.lang_grades.forEach(function(g) {
                            let badgeColor = '#858796'; // gray default
                            if(g.nilai_huruf) {
                                if(g.nilai_indeks >= 3.5) badgeColor = '#1cc88a'; // green
                                else if(g.nilai_indeks >= 2.5) badgeColor = '#f6c23e'; // yellow
                                else badgeColor = '#e74a3b'; // red
                            }
                            let mkLabel = g.kode_lang;
                            let badgeTitle = g.nm_mk;
                            langHtml += `
                            <div style="display: inline-flex; flex-direction: column; align-items: center; margin-right: 15px; margin-bottom: 15px; width: 110px;">
                                <div style="background-color: ${badgeColor}; color: #fff; padding: 1rem; border-radius: 0.75rem; display: inline-flex; flex-direction: column; align-items: center; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: default;" title="${badgeTitle}">
                                    <span style="font-size: 1.2rem; font-weight: 800; opacity: 0.9; letter-spacing: 2px;">${mkLabel}</span>
                                    <span style="font-size: 2.5rem; font-weight: 900; line-height: 1.2;">${g.nilai_huruf || '-'}</span>
                                    <span style="font-size: 1rem; opacity: 0.85; margin-top: 4px;">${g.nilai_huruf ? parseFloat(g.nilai_indeks).toFixed(2) : 'Blm Ada'}</span>
                                </div>
                                <span style="font-size: 1.1rem; font-weight: 600; text-align: center; color: #555; margin-top: 5px; line-height: 1.2;">${badgeTitle}</span>
                            </div>`;
                        });
                    } else {
                        langHtml = '<p style="color: #858796; font-size: 1.1rem; font-style: italic; text-align: center; margin: 1rem 0;">Belum ada data nilai mata kuliah bahasa.</p>';
                    }
                    document.getElementById('langGrades').innerHTML = langHtml;
                    document.getElementById('langConclusion').innerHTML = skillData.lang_conclusion || '';
                }
                
                // Failed Courses update (Tab 3)
                document.getElementById('failedCoursesContainer').innerHTML = data.failed_html;
                
                // Narasi Akademik update (Tab 4)
                document.getElementById('narasiAkademikText').innerHTML = data.academic_summary;
                
            } else {
                document.getElementById('modalLoadingOverlay').style.display = 'flex';
                document.getElementById('modalLoadingOverlay').innerHTML = '<span style="color:#e74a3b;">Gagal memuat detail mahasiswa.</span>';
            }
        },
        error: function() {
            document.getElementById('modalLoadingOverlay').style.display = 'flex';
            document.getElementById('modalLoadingOverlay').innerHTML = '<span style="color:#e74a3b;">Terjadi kesalahan server saat memuat detail mahasiswa.</span>';
        }
    });
}


</script>



<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- Progress Overlay Controller ---
    const STEPS = {
        init:  { pct: 10,  chip: 'init',  lbl: 'lbl-init',  label: 'Memulai proses...' },
        atas:  { pct: 50,  chip: 'atas',  lbl: 'lbl-atas',  label: 'Memuat chart & statistik...' },
        bawah: { pct: 100, chip: 'bawah', lbl: 'lbl-bawah', label: 'Memuat data mahasiswa...' }
    };

    function setProgress(step) {
        const s = STEPS[step];
        const bar   = document.getElementById('init-progress-bar');
        const pctEl = document.getElementById('init-progress-pct');
        const txtEl = document.getElementById('init-status-text');

        if (bar)   bar.style.width = s.pct + '%';
        if (pctEl) pctEl.textContent = s.pct + '%';
        if (txtEl) txtEl.textContent = s.label;

        const steps = ['init', 'atas', 'bawah'];
        const idx   = steps.indexOf(step);
        steps.forEach(function (k, i) {
            const lbl  = document.getElementById('lbl-' + k);
            const chip = document.getElementById('chip-' + k);
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
        const overlay = document.getElementById('init-loading-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
            setTimeout(function () { overlay.style.display = 'none'; }, 700);
        }
    }

    // Fungsi Load Panel Atas (Chart KRS & Clustering)
    function loadPanelAtas() {
        setProgress('atas');
        return fetch('<?= site_url("Lecturer_dashboard_perwalian/ajax_load_panel_atas") ?>')
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    // --- Data Bar Chart KRS ---
                    const krsDataRaw = response.krs_status;
                    const labelsKrs = krsDataRaw.map(d => 'Angkatan ' + d.angkatan_2d);
                    const dataSudahKrs = krsDataRaw.map(d => d.sudah_krs);
                    const dataBelumKrs = krsDataRaw.map(d => d.belum_krs);

                    new Chart(document.getElementById('krsChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labelsKrs,
                            datasets: [
                                {
                                    label: 'Sudah KRS',
                                    backgroundColor: 'rgba(78, 115, 223, 0.85)',
                                    hoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                                    data: dataSudahKrs,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Belum KRS (Draft)',
                                    backgroundColor: 'rgba(231, 74, 59, 0.85)',
                                    hoverBackgroundColor: 'rgba(231, 74, 59, 1)',
                                    data: dataBelumKrs,
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            },
                            scales: {
                                x: { stacked: true, grid: { display: false } },
                                y: { stacked: true, beginAtZero: true, border: { dash: [5, 5] }, grid: { color: 'rgba(0,0,0,0.05)' } }
                            }
                        }
                    });

                    // --- Data Doughnut Plot K-Means ---
                    const clusterDataRaw = response.clustering;
                    
                    // Hitung jumlah mahasiswa per kluster
                    let clusterCounts = {};
                    clusterDataRaw.forEach(pt => {
                        if(!clusterCounts[pt.cluster]) {
                            clusterCounts[pt.cluster] = 0;
                        }
                        clusterCounts[pt.cluster]++;
                    });

                    let doughnutLabels = Object.keys(clusterCounts);
                    let doughnutData = Object.values(clusterCounts);
                    
                    // Warna eksplisit untuk setiap cluster
                    const exactColors = {
                        'Prestasi Tinggi (Unggul)': 'rgba(78, 115, 223, 0.85)',
                        'Prestasi Menengah (Rata-rata)': 'rgba(28, 200, 138, 0.85)',
                        'Perhatian Khusus (Bawah)': 'rgba(246, 194, 62, 0.85)'
                    };

                    const clusterColors = doughnutLabels.map(label => exactColors[label] || 'rgba(133, 135, 150, 0.85)');

                    new Chart(document.getElementById('clusterChart').getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: doughnutLabels,
                            datasets: [{
                                data: doughnutData,
                                backgroundColor: clusterColors,
                                hoverBackgroundColor: clusterColors.map(c => c.replace('0.85', '1')),
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { 
                                    position: 'bottom',
                                    labels: { boxWidth: 12, font: { size: 10 } }
                                }
                            }
                        }
                    });

                    // Inject HTML for clustering list
                    const clusteringListContainer = document.getElementById('clusteringListContainer');
                    if (clusteringListContainer && response.html_clustering_list) {
                        clusteringListContainer.innerHTML = response.html_clustering_list;
                    }
                    
                    if (response.html_clustering_modal) {
                        const existingModal = document.getElementById('clusterDetailModal');
                        if(existingModal) existingModal.remove();
                        document.body.insertAdjacentHTML('beforeend', response.html_clustering_modal);
                    }
                    
                    document.getElementById('chip-atas').className = 'init-step-chip done';
                    document.getElementById('lbl-atas').className  = 'init-step-label done';
                }
            })
            .catch(err => console.error("Error loading panel atas:", err));
    }

    // Fungsi Load Panel Bawah (Clustering List, Anomali, Belum KRS)
    function loadPanelBawah() {
        setProgress('bawah');
        return fetch('<?= site_url("Lecturer_dashboard_perwalian/ajax_load_panel_bawah") ?>')
            .then(res => res.json())
            .then(response => {
                if(response.status === 'success') {
                    const anomaliTbody = document.getElementById('anomaliTbody');
                    if (anomaliTbody) anomaliTbody.innerHTML = response.html_anomali;

                    const belumKrsTbody = document.getElementById('belumKrsTbody');
                    if (belumKrsTbody) belumKrsTbody.innerHTML = response.html_belum_krs;
                    
                    // Update judul jumlah mahasiswa belum KRS
                    const titleBelumKrs = document.getElementById('titleBelumKrs');
                    if (titleBelumKrs && typeof response.count_belum_krs !== 'undefined') {
                        titleBelumKrs.innerHTML = '<i class="fa fa-exclamation-circle"></i> Mahasiswa Belum KRS (Semester Ini) - ' + response.count_belum_krs + ' Mahasiswa';
                    }
                    
                    document.getElementById('chip-bawah').className = 'init-step-chip done';
                    document.getElementById('lbl-bawah').className  = 'init-step-label done';
                    setTimeout(dismissOverlay, 500);
                }
            })
            .catch(err => {
                console.error("Error loading panel bawah:", err);
                setTimeout(dismissOverlay, 500);
            });
    }

    // Panggil fungsi ajax load berurutan
    setProgress('init');
    setTimeout(() => {
        loadPanelAtas().then(() => {
            setTimeout(() => loadPanelBawah(), 500);
        });
    }, 400);

    // Tab switcher logic fixes for Prediksi Risiko
    document.querySelectorAll('#prediksiTabs a').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove active from all lis within #prediksiTabs
            document.querySelectorAll('#prediksiTabs li').forEach(li => li.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            // hide all panes related to prediksiTabs only
            const tabContent = document.querySelector('#prediksiTabs').nextElementSibling;
            if (tabContent && tabContent.classList.contains('tab-content')) {
                tabContent.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active', 'in');
                });
            }
            
            // show target
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            if(targetPane) {
                targetPane.classList.add('active', 'in');
            }

            // Load data if it's a prediksi tab
            const tabKey = this.getAttribute('data-tab-key');
            if (tabKey) {
                loadPrediksiRisikoData(tabKey);
            }
        });
    });

    // AJAX Load Prediksi Risiko
    function getTrendIconJS(val1, val2) {
        let v1 = parseFloat(val1 || 0);
        let v2 = parseFloat(val2 || 0);
        if (v1 > 0 && v2 > 0) {
            if (v2 > v1) return '<i class="fa fa-arrow-up" style="color: #1cc88a;" title="Naik"></i>';
            if (v2 < v1) return '<i class="fa fa-arrow-down" style="color: #e74a3b;" title="Turun"></i>';
            return '<i class="fa fa-minus" style="color: #f6c23e;" title="Tetap"></i>';
        }
        return '';
    }

    function getStarRatingJS(ipkVal) {
        let val = parseFloat(ipkVal || 0);
        if (val === 0) return '<span style="color:#ccc;">-</span>';
        let stars = '';
        let color = '';
        if (val >= 3.5) {
            stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
            color = '#1cc88a'; // hijau
        } else if (val >= 3.0) {
            stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
            color = '#1cc88a'; // hijau
        } else if (val >= 2.5) {
            stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
            color = '#f6c23e'; // kuning
        } else {
            stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i>';
            color = '#e74a3b'; // merah
        }
        return `<span style="color: ${color}; font-size: 11px; white-space: nowrap;" title="Rating IPK: ${val}">${stars}</span>`;
    }

    function renderPrediksiTable(tbodyId, dataList, tabKey, isAman) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        if (!dataList || dataList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Tidak ada data.</td></tr>';
            return;
        }

        let html = '';
        dataList.forEach((std, idx) => {
            let avatar_url = `https://ui-avatars.com/api/?name=${encodeURIComponent(std.nm_pd)}&background=random&color=fff`;
            if (std.foto && std.foto.trim() !== '') {
                avatar_url = `https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_${std.foto}`;
            }
            const fallback_url = '<?= base_url("assets/images/user.png") ?>';
            
            let badges = '';
            if (tabKey === 'tahun_akhir') {
                if (parseInt(std.tahun_kuliah) > 4) {
                    badges += `<div><span class="bs5-indicator-danger"><i class="fa fa-clock-o"></i> Tidak Tepat Waktu (>4 Thn)</span></div>`;
                } else if (!isAman && std.jml_judul_ta == 0) {
                    badges += `<div><span class="bs5-indicator-primary"><i class="fa fa-book"></i> Belum Ada Judul TA</span></div>`;
                } else if (isAman && parseInt(std.tahun_kuliah) <= 4 && std.jml_judul_ta > 0) {
                    badges += `<div><span class="bs5-indicator-success"><i class="fa fa-graduation-cap"></i> Diprediksi Lulus Tepat Waktu</span></div>`;
                }
            }

            if (parseInt(std.total_mk_mengulang || 0) > 0) {
                badges += `<div style="margin-top: 3px;"><span class="bs5-indicator-danger" title="Terdapat mata kuliah dengan nilai D/E yang perlu diulang"><i class="fa fa-exclamation-triangle"></i> Punya ${std.total_mk_mengulang} Nilai D/E</span></div>`;
            }

            if (std.anomali_akademik && std.anomali_akademik.length > 0) {
                std.anomali_akademik.forEach(msg => {
                    badges += `<div style="margin-top: 3px;"><span class="bs5-indicator-warning" style="background:#fff3cd; color:#856404; border:1px solid #ffeeba; display:inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px;" title="Peringatan Data Akademik"><i class="fa fa-exclamation-circle"></i> ${msg}</span></div>`;
                });
            }

            let ipkCols = '';
            const ipk_badge_class = isAman ? 'bs5-badge-success' : 'bs5-badge-danger';
            
            if (tabKey === 'tahun_1') {
                ipkCols = `
                    <td style="text-align: center; white-space: nowrap;">${getStarRatingJS(std.ipk_terakhir)}</td>
                    <td style="text-align: center;"><span class="bs5-badge ${ipk_badge_class}">${std.ipk_thn1 || '-'}</span></td>
                `;
            } else if (tabKey === 'tahun_2') {
                ipkCols = `
                    <td style="text-align: center; white-space: nowrap;">${getStarRatingJS(std.ipk_terakhir)}</td>
                    <td style="text-align: center;"><span class="bs5-badge bs5-badge-primary">${std.ipk_thn1 || '-'}</span></td>
                    <td style="text-align: center;">${getTrendIconJS(std.ipk_thn1, std.ipk_thn2)}</td>
                    <td style="text-align: center;"><span class="bs5-badge ${ipk_badge_class}">${std.ipk_thn2 || '-'}</span></td>
                `;
            } else if (tabKey === 'tahun_3') {
                ipkCols = `
                    <td style="text-align: center; white-space: nowrap;">${getStarRatingJS(std.ipk_terakhir)}</td>
                    <td style="text-align: center;"><span class="bs5-badge bs5-badge-primary">${std.ipk_thn1 || '-'}</span></td>
                    <td style="text-align: center;">${getTrendIconJS(std.ipk_thn1, std.ipk_thn2)}</td>
                    <td style="text-align: center;"><span class="bs5-badge bs5-badge-primary">${std.ipk_thn2 || '-'}</span></td>
                    <td style="text-align: center;">${getTrendIconJS(std.ipk_thn2, std.ipk_thn3)}</td>
                    <td style="text-align: center;"><span class="bs5-badge ${ipk_badge_class}">${std.ipk_thn3 || '-'}</span></td>
                `;
            } else {
                ipkCols = `
                    <td style="text-align: center; white-space: nowrap;">${getStarRatingJS(std.ipk_terakhir)}</td>
                    <td style="text-align: center;"><span class="bs5-badge ${ipk_badge_class}">${std.ipk_terakhir || '-'}</span></td>
                `;
            }

            // Escape quotes for HTML attribute
            const safeNameHTML = std.nm_pd.replace(/"/g, "&quot;").replace(/'/g, "&#39;");

            html += `
                <tr>
                    <td>${idx + 1}</td>
                    <td>
                        <div class="bs5-d-flex bs5-align-items-center">
                            <img src="${avatar_url}" onerror="this.onerror=null;this.src='${fallback_url}';" class="bs5-avatar bs5-cursor-pointer" alt="Foto" data-name="${safeNameHTML}" data-avatar="${avatar_url}" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '${std.nipd}')">
                            <div>
                                <div style="font-size: 11px; color: #858796; margin-bottom: 2px; font-weight: 700;">${std.nipd}</div>
                                <div style="font-weight: 600; color: #5a5c69;">${std.nm_pd} 
                                    <button type="button" class="btn btn-xs btn-outline-primary" style="padding: 0 5px; font-size: 13px; margin-left: 5px;" data-name="${safeNameHTML}" data-avatar="${avatar_url}" onclick="showPhotoModal(this.getAttribute('data-avatar'), this.getAttribute('data-name'), '${std.nipd}')"><i class="fa fa-id-card"></i> Akademik</button>
                                </div>
                                ${badges}
                            </div>
                        </div>
                    </td>
                    ${ipkCols}
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    const loadedTabs = {};

    function loadPrediksiRisikoData(tabKey) {
        if (loadedTabs[tabKey]) return; // Jika sudah pernah dimuat, biarkan

        // Set state ke loading
        $(`#tbody_aman_${tabKey}, #tbody_berisiko_${tabKey}`).html('<tr><td colspan="5" style="text-align:center; padding: 2rem;"><i class="fa fa-spinner fa-spin" style="font-size: 2rem; color: #4e73df;"></i><div style="margin-top: 10px; color: #858796;">Memuat data...</div></td></tr>');

        $.ajax({
            url: "<?= site_url('Lecturer_dashboard_perwalian/ajax_get_prediksi_risiko') ?>?tab=" + tabKey,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    renderPrediksiTable(`tbody_aman_${tabKey}`, response.data.aman, tabKey, true);
                    renderPrediksiTable(`tbody_berisiko_${tabKey}`, response.data.berisiko, tabKey, false);
                    loadedTabs[tabKey] = true;
                } else {
                    console.error("Gagal memuat data prediksi:", response.message);
                    $(`#tbody_aman_${tabKey}, #tbody_berisiko_${tabKey}`).html('<tr><td colspan="5" style="text-align:center;color:red;">Gagal memuat data.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $(`#tbody_aman_${tabKey}, #tbody_berisiko_${tabKey}`).html('<tr><td colspan="5" style="text-align:center;color:red;">Terjadi kesalahan pada server.</td></tr>');
            }
        });
    }

    // Panggil untuk tab pertama saat inisiasi
    loadPrediksiRisikoData('tahun_1');

    // Fix ApexCharts width inside Bootstrap Modals and Tabs
    $('#photoModal').on('shown.bs.modal', function () {
        window.dispatchEvent(new Event('resize'));
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        window.dispatchEvent(new Event('resize'));
    });
});
</script>
