<style>
  .seamless-iframe {
    width: 100%;
    border: none;
    overflow: hidden;
    transition: height 0.2s ease;
  }
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
    font-weight: 700;
    font-size: 13px;
    color: #4A5568;
    letter-spacing: 2px;
    animation: pulseText 1.5s infinite;
  }
  @keyframes pulseText {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
</style>
<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-briefcase font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body" style="padding: 0; position: relative; min-height: 400px;">
          <div id="iframe-loader" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.85); z-index: 10; display: flex; flex-direction: column; justify-content: flex-start; padding-top: 150px; align-items: center; border-radius: 12px; backdrop-filter: blur(3px);">
            <div class="modern-spinner" style="width: 70px; height: 70px;"></div>
            <p class="loader-text" style="margin-top: 25px;">MEMUAT DASHBOARD KERJASAMA...</p>
          </div>
          <iframe id="profile-iframe" onload="setTimeout(function(){ document.getElementById('iframe-loader').style.display='none'; }, 1000);" src="<?= site_url('C_infografis_kerjasama/generate') ?>" class="seamless-iframe" scrolling="no"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'setHeight') {
      document.getElementById('profile-iframe').style.height = e.data.height + 'px';
    }
  }, false);
</script>
