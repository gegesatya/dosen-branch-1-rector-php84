<style>
  .seamless-iframe {
    width: 100%;
    min-height: 900px;
    border: none;
    overflow: hidden;
    transition: height 0.2s ease;
  }

  /* ── Loading Overlay ── */
  #ls-loading-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(10, 20, 50, 0.75);
      backdrop-filter: blur(6px);
      z-index: 99999;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: opacity 0.5s ease, visibility 0.5s ease;
  }
  .ls-loading-box {
      width: 360px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      padding: 30px 25px;
      background: linear-gradient(135deg, #0a1628 0%, #1a2f5c 50%, #0a1628 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', 'Segoe UI', sans-serif;
  }
  #ls-loading-overlay.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
  }
  .ls-icon {
      width: 60px; height: 60px; border-radius: 16px;
      background: linear-gradient(135deg, #1e5fa8, #0e9fd6);
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: white; margin: 0 auto 18px;
      box-shadow: 0 0 30px rgba(14, 159, 214, 0.4);
  }
  .ls-loading-title { font-size: 17px; font-weight: 700; color: #e2eaf8; margin: 0 0 5px; text-align: center; }
  .ls-loading-sub { font-size: 12px; color: #8fa8cc; margin: 0 0 20px; text-align: center; }
  .ls-progress-track { width: 100%; height: 5px; background: rgba(255,255,255,0.08); border-radius: 99px; overflow: hidden; }
  .ls-progress-fill { height: 100%; background: linear-gradient(90deg, #1e5fa8, #0e9fd6); border-radius: 99px; width: 0%; transition: width 0.4s ease; animation: ls-pulse 1.5s infinite; }
  @keyframes ls-pulse { 0%,100%{ opacity:1; } 50%{ opacity:0.7; } }
</style>

<!-- Loading Overlay -->
<div id="ls-loading-overlay">
    <div class="ls-loading-box">
        <div class="ls-icon">📊</div>
        <h2 class="ls-loading-title">Menyiapkan Laporan...</h2>
        <p class="ls-loading-sub" id="ls-loading-msg">Memuat data kinerja akademik semester</p>
        <div class="ls-progress-track">
            <div class="ls-progress-fill" id="ls-progress-bar" style="width:60%;"></div>
        </div>
    </div>
</div>

<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-file-text-o font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
        </div>
        <div class="portlet-body" style="padding: 0; position: relative; min-height: 800px;">
          <iframe id="ls-iframe"
            src="<?= site_url('C_laporan_semester/generate') ?>"
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
      document.getElementById('ls-iframe').style.height = e.data.height + 'px';
    }
    if (e.data && e.data.type === 'filterChange') {
      var iframe = document.getElementById('ls-iframe');
      document.getElementById('ls-loading-overlay').classList.remove('hidden');
      document.getElementById('ls-progress-bar').style.width = '30%';
      document.getElementById('ls-loading-msg').innerText = 'Memuat laporan semester ' + e.data.nm_smt;
      iframe.src = '<?= site_url('C_laporan_semester/generate') ?>' + '?id_smt=' + e.data.id_smt;
    }
    if (e.data && e.data.type === 'loadingComplete') {
      setTimeout(function() {
        document.getElementById('ls-loading-overlay').classList.add('hidden');
      }, 400);
    }
  }, false);

  // Auto-hide if iframe loaded and no message
  document.getElementById('ls-iframe').addEventListener('load', function() {
    setTimeout(function() {
      document.getElementById('ls-loading-overlay').classList.add('hidden');
    }, 1000);
  });
</script>
