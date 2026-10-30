<style>
  .seamless-iframe {
    width: 100%;
    min-height: 850px;
    border: none;
    overflow: hidden;
    transition: height 0.2s ease;
  }
  
  /* ── Loading Overlay ── */
  #init-loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
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
      font-family: 'Inter', sans-serif;
  }
  #init-loading-overlay.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
  }
  #init-loading-overlay.hidden .init-loading-box {
      transform: translateY(20px) scale(0.95);
  }
  .init-logo-area { text-align: center; margin-bottom: 20px; }
  .init-logo-area .init-icon {
      width: 48px; height: 48px; border-radius: 14px;
      background: linear-gradient(135deg, #3b82f6, #06b6d4);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: white; margin: 0 auto 15px;
      box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
  }
  .init-logo-area h2 { font-size: 18px; font-weight: 800; color: #f1f5f9; margin: 0 0 6px; }
  .init-logo-area p { font-size: 12px; color: #94a3b8; margin: 0; font-weight: 400; }
  .init-progress-container { width: 100%; }
  .init-progress-labels { display: flex; justify-content: space-between; margin-bottom: 8px; }
  .init-progress-labels .init-step-label { font-size: 11px; color: #64748b; font-weight: 600; transition: color 0.3s; }
  .init-progress-labels .init-step-label.active { color: #60a5fa; }
  .init-progress-labels .init-step-label.done { color: #34d399; }
  .init-progress-track { width: 100%; height: 6px; background: rgba(255,255,255,0.08); border-radius: 99px; overflow: hidden; }
  .init-progress-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #06b6d4); border-radius: 99px; width: 0%; transition: width 0.3s ease; }
</style>

<!-- ── Loading Overlay ── -->
<div id="init-loading-overlay">
    <div class="init-loading-box">
        <div class="init-logo-area">
            <div class="init-icon">🎓</div>
            <h2>Menyiapkan Data Intake...</h2>
            <p id="loading-msg">Menganalisis sebaran sekolah mahasiswa</p>
        </div>
        <div class="init-progress-container">
            <div class="init-progress-labels">
                <span class="init-step-label active" id="step1-label">KPI</span>
                <span class="init-step-label" id="step2-label">Top 5</span>
                <span class="init-step-label" id="step3-label">Sebaran</span>
                <span class="init-step-label" id="step4-label">Detail</span>
            </div>
            <div class="init-progress-track">
                <div class="init-progress-fill" id="init-progress-bar"></div>
            </div>
        </div>
    </div>
</div>

<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-university font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
        </div>
        <div class="portlet-body" style="padding: 0; position: relative; min-height: 600px;">
          <iframe id="intake-iframe"
            src="<?= site_url('C_infografis_intake_quality/generate') ?>"
            class="seamless-iframe"
            scrolling="no">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'setHeight') {
      document.getElementById('intake-iframe').style.height = e.data.height + 'px';
    }
    if (e.data && e.data.type === 'filterChange') {
      var iframe = document.getElementById('intake-iframe');
      
      // Reset & show loading overlay on filter change
      document.getElementById('init-loading-overlay').classList.remove('hidden');
      document.getElementById('init-progress-bar').style.width = '0%';
      document.getElementById('loading-msg').innerText = 'Menganalisis sebaran sekolah mahasiswa';
      
      for(var i=1; i<=4; i++) {
        var el = document.getElementById('step'+i+'-label');
        if(el) {
          el.classList.remove('active');
          el.classList.remove('done');
        }
      }
      document.getElementById('step1-label').classList.add('active');
      
      iframe.src = '<?= site_url('C_infografis_intake_quality/generate') ?>' + '?tahun=' + e.data.tahun;
    }
    // Handle progress updates from iframe
    if (e.data && e.data.type === 'progressUpdate') {
      var step = e.data.step;
      var pct = e.data.pct;
      var msg = e.data.msg;
      
      document.getElementById('init-progress-bar').style.width = pct + '%';
      document.getElementById('loading-msg').innerText = msg;
      
      if(step > 0) {
          document.getElementById('step'+step+'-label').classList.add('done');
      }
      if(step < 4) {
          document.getElementById('step'+(step+1)+'-label').classList.add('active');
      }
    }
    // Handle loading completion
    if (e.data && e.data.type === 'loadingComplete') {
      setTimeout(() => {
          document.getElementById('init-loading-overlay').classList.add('hidden');
      }, 500);
    }
  }, false);
</script>
