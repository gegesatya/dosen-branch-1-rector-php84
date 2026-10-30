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
    color: #1e3c72;
    letter-spacing: 2px;
    animation: pulseText 1.5s infinite;
  }
  @keyframes pulseText {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
</style>

<div class="page-content" style="position: relative; min-height: 80vh;">
  <div id="global-loader" style="display: flex; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.85); z-index: 9999; justify-content: flex-start; padding-top: 150px; align-items: center; flex-direction: column; backdrop-filter: blur(3px); border-radius: 8px;">
    <div class="modern-spinner" style="width: 70px; height: 70px;"></div>
    <h4 class="loader-text" style="margin-top: 25px;">MEMUAT ACADEMIC INTELLIGENCE...</h4>
  </div>
  <script>
    window.addEventListener('load', function() {
      setTimeout(function() {
        var loader = document.getElementById('global-loader');
        if(loader) loader.style.display = 'none';
      }, 1000);
    });
  </script>
  
  <style>
    .ai-card {
      background: #ffffff;
      border-radius: 16px !important;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
      margin-bottom: 24px;
      border: 1px solid rgba(224, 224, 224, 0.4) !important;
      transition: all 0.3s ease;
    }

    .ai-card:hover {
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
      transform: translateY(-2px);
    }

    .ai-stat-card {
      border-radius: 16px !important;
      padding: 20px;
      margin-bottom: 24px;
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

    .stat-bg-blue {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

    .stat-bg-red {
      background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%);
    }

    .stat-bg-orange {
      background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
    }

    .stat-bg-green {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

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

    .ai-section-title {
      font-size: 18px;
      font-weight: 700;
      color: #102542;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .ai-section-title i {
      color: #2a5298;
    }

    .ai-table {
      margin-bottom: 0 !important;
    }

    .ai-table th {
      background-color: #f8fafc !important;
      color: #334155 !important;
      font-weight: 600 !important;
      border-bottom: 2px solid #e2e8f0 !important;
      font-size: 12px;
      text-transform: uppercase;
    }

    .ai-table td {
      font-size: 13px;
      color: #475569;
      vertical-align: middle !important;
    }

    .badge-soft-danger {
      background-color: #fef2f2;
      color: #ef4444;
      border: 1px solid #fca5a5;
    }

    .badge-soft-warning {
      background-color: #fffbeb;
      color: #d97706;
      border: 1px solid #fcd34d;
    }

    .badge-soft-success {
      background-color: #f0fdf4;
      color: #16a34a;
      border: 1px solid #86efac;
    }

    .insight-box {
      border-left: 4px solid #3b82f6;
      background: #f8fafc;
      padding: 15px;
      border-radius: 0 12px 12px 0;
      margin-bottom: 15px;
    }

    .insight-box.danger {
      border-left-color: #ef4444;
    }

    .insight-box.warning {
      border-left-color: #f59e0b;
    }

    .insight-box.success {
      border-left-color: #10b981;
    }
  </style>

  
  <div class="row">
    <div class="col-md-12" style="position: relative;">
      
      <div class="pull-right print-hide" style="margin-top: 15px;">
        <form action="<?= site_url('C_academic_intelligence/index') ?>" method="get" class="form-inline m-0">
          <select name="prodi" class="form-control" onchange="document.getElementById('global-loader').style.display='flex'; var f = this.form; setTimeout(function(){ f.submit(); }, 1000);" style="border-radius: 20px; font-weight: 500; font-size: 14px; min-width: 280px; background-color: #ffffff; border: 2px solid #a8cbfb; color: #1e293b; padding: 6px 18px !important; height: 40px !important; line-height: 24px !important; cursor: pointer; box-shadow: 0 2px 10px rgba(168, 203, 251, 0.15); outline: none; transition: border-color 0.2s;">
            <option value="">Semua Program Studi</option>
            <?php foreach ($list_prodi as $prodi): ?>
              <option value="<?= $prodi->id_sms ?>" <?= ($selected_prodi == $prodi->id_sms) ? 'selected' : '' ?>>
                <?= $prodi->nm_lemb ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <h3 class="page-title" style="font-weight: 800; color: #102542; font-family: 'Outfit', sans-serif;">
        Academic Intelligence Dashboard <small style="font-weight: 500;">Sistem Pemantauan Kualitas & Kesehatan Akademik</small>
      </h3>
      <ul class="page-breadcrumb breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="#">Dashboard</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Infografis WBI</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Academic Intelligence</a>
        </li>
      </ul>
    </div>
  </div>
  

  
  <div class="row">
    <div class="col-md-3 col-sm-6">
      <div class="ai-stat-card stat-bg-blue">
        <div class="ai-stat-label">Rata-rata IPK</div>
        <div class="ai-stat-value"><?= $stats->avg_ipk; ?></div>
        <div class="ai-stat-sub"><i class="fa fa-arrow-circle-up"></i> <?= !empty($selected_prodi) ? 'Prodi Terpilih' : 'Seluruh Mahasiswa Aktif'; ?></div>
        <i class="fa fa-graduation-cap icon"></i>
      </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
      <div class="ai-stat-card stat-bg-red">
        <div class="ai-stat-label">Mahasiswa Risiko Tinggi</div>
        <div class="ai-stat-value"><?= $stats->total_risk; ?></div>
        <div class="ai-stat-sub"><i class="fa fa-warning"></i> IPS Terakhir < 2.00</div>
        <i class="fa fa-users icon"></i>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="ai-stat-card stat-bg-orange">
        <div class="ai-stat-label">MK Gagal Tinggi</div>
        <div class="ai-stat-value"><?= $stats->total_gagal_mk; ?></div>
        <div class="ai-stat-sub"><i class="fa fa-exclamation-triangle"></i> Failure Rate > 30%</div>
        <i class="fa fa-book icon"></i>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="ai-stat-card stat-bg-green">
        <div class="ai-stat-label">Kelulusan Tepat Waktu</div>
        <div class="ai-stat-value"><?= $stats->completion_rate; ?>%</div>
        <div class="ai-stat-sub"><i class="fa fa-line-chart"></i> Prediksi Algoritma Cohort</div>
        <i class="fa fa-calendar-check-o icon"></i>
      </div>
    </div>
  </div>
  

  
  <div class="row">
    <div class="col-md-6">
      <div class="ai-card">
        <div class="ai-section-title">
          <i class="fa fa-line-chart"></i> Tren Performa Rata-rata IPK per Prodi
        </div>
        <div style="position: relative; height: 320px;">
          <canvas id="ipkTrendChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="ai-card">
        <div class="ai-section-title">
          <i class="fa fa-pie-chart"></i> Distribusi Academic Health
        </div>
        <div style="position: relative; height: 320px;">
          <canvas id="healthChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  

  
  <div class="row">
    <div class="col-md-6">
      <div class="ai-card">
        <div class="ai-section-title">
          <i class="fa fa-bar-chart"></i> Failure Rate per Kelompok Mata Kuliah
        </div>
        <div style="position: relative; height: 280px;">
          <canvas id="failureChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="ai-card">
        <div class="ai-section-title">
          <i class="fa fa-dot-circle-o"></i> Scatter Plot Beban SKS vs Performa IPS
        </div>
        <div style="position: relative; height: 280px;">
          <canvas id="scatterChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  

  
  <div class="row">
    <div class="col-md-7">
      <div class="ai-card" style="min-height: 380px;">
        <div class="ai-section-title">
          <i class="fa fa-table"></i> Pemantauan Kinerja Cohort per Angkatan
        </div>
        <div class="table-responsive">
          <table class="table table-hover ai-table">
            <thead>
              <tr>
                <th>Angkatan</th>
                <th class="text-center">Avg IPK</th>
                <th class="text-center">Dropout Risk</th>
                <th class="text-center">Tepat Waktu (%)</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cohort_analysis as $c): ?>
              <tr>
                <td style="font-weight: 600;"><?= $c->angkatan; ?></td>
                <td class="text-center" style="font-weight: 700; color: #1e3c72;"><?= $c->avg_ipk; ?></td>
                <td class="text-center"><?= $c->dropout_risk; ?></td>
                <td class="text-center" style="font-weight: 600;"><?= $c->tepat_waktu; ?></td>
                <td class="text-center">
                  <span class="badge badge-soft-<?= $c->status_class; ?>"><?= $c->status; ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="ai-card" style="min-height: 380px;">
        <div class="ai-section-title">
          <i class="fa fa-magic"></i> Insight Akademik
        </div>
        <div class="insight-container">
          <?php foreach ($ai_insights as $insight): ?>
          <div class="insight-box <?= $insight->class; ?>">
            <span class="label label-<?= $insight->class; ?>" style="font-size: 10px; font-weight: 600; text-transform: uppercase; border-radius: 4px; padding: 3px 6px;">
              <?= $insight->badge; ?>
            </span>
            <p style="margin-top: 10px; margin-bottom: 0; font-size: 12.5px; line-height: 1.6; color: #334155;">
              <?= $insight->text; ?>
            </p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  

  
  <div class="row">
    <div class="col-md-12">
      <div class="ai-card">
        <div class="ai-section-title">
          <i class="fa fa-exclamation-triangle" style="color: #ef4444;"></i> Mata Kuliah dengan Failure Rate Tertinggi
        </div>
        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
          <table class="table table-hover ai-table">
            <thead>
              <tr>
                <th>Nama Mata Kuliah</th>
                <th>Program Studi</th>
                <th class="text-center">Jumlah Peserta</th>
                <th class="text-center">Jumlah Gagal</th>
                <th class="text-center">Failure Rate</th>
                <th class="text-center">Rata-rata Kelas</th>
                <th class="text-center">Tindakan</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($high_risk_courses)): ?>
                <?php foreach($high_risk_courses as $index => $mk): ?>
                <tr>
                  <td style="font-weight: 600; color: #1e3c72;"><?= $mk->nm_mk; ?></td>
                  <td><?= $mk->prodi ? $mk->prodi : 'Umum'; ?></td>
                  <td class="text-center"><?= $mk->total_peserta; ?></td>
                  <td class="text-center" style="color: #ef4444; font-weight: 600;"><?= $mk->total_gagal; ?></td>
                  <td class="text-center" style="font-weight: 700; color: #ef4444;"><?= $mk->failure_rate; ?>%</td>
                  <td class="text-center"><?= $mk->avg_grade; ?></td>
                  <td class="text-center">
                    <span class="label label-danger" style="font-weight: 600; border-radius: 4px;">Perlu Evaluasi</span>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted">Seluruh Mata Kuliah Berada Dalam Batas Aman Kelulusan.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
</div>
