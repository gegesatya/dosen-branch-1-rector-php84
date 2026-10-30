<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-book font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?></span>
            <span class="caption-helper"><?= $judul_kecil; ?></span>
          </div>
          <div class="actions">
            <a href="<?= site_url('Lecturer_persetujuan_krs') ?>" class="btn btn-sm" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 6px; font-weight: 600;">
              <i class="fa fa-arrow-left"></i> Kembali ke Persetujuan KRS
            </a>
          </div>
        </div>
        <div class="portlet-body">
          <div id="makul_prodi_container">
            <?= $content_html ?? ''; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function load_makul_prodi(id_sms) {
  window.location.href = "<?= site_url('Lecturer_persetujuan_krs/show_makul_prodi') ?>/" + id_sms;
}
</script>
