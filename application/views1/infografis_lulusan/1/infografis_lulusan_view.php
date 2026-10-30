<style>
  iframe {
    width: 100%;
    height: 80vh;
    /* 90% dari tinggi viewport */
  }
</style>
<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <iframe src="<?= site_url('C_infografis_lulusan/generate') ?>" style="border:none;"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>