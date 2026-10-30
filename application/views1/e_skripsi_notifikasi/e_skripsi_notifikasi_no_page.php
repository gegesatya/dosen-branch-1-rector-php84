<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> </span>
            <span class="caption-helper">TAHUN AJARAN <?= $this->session->userdata('setting')->tahun_ajaran_aktif; ?></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container table-responsive-2">
            <?= $pesan; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>