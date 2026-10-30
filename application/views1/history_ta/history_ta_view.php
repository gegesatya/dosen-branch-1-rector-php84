<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
            <?php if ($this->session->flashdata('msg_krs')) { ?>
              <div class="alert alert-danger margin-top-10"> <?= $this->session->flashdata('msg_krs') ?> </div>
            <?php } ?>
          </div>
        </div>
        <div class="portlet-body">
          <div class="note note-info">
            <h4 class="block">Informasi!</h4>
            <p>Berikut ini adalah history Tugas Akhir (Pembimbing dan Penguji)</p>

          </div>

        </div>
      </div>
    </div>
  </div>
  <?php
  $dosen = array(2);
  if ($this->ion_auth->in_group($dosen)) {
  ?>
    <div class="row">
      <div class="col-md-12">
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption">
              <i class="fa fa-gift font-green-sharp"></i>
              <span class="caption-subject font-green-sharp bold uppercase"><?= "Perkuliahan "; ?> <small><?= "DOSEN"; ?></small></span>
            </div>
          </div>
          <div class="portlet-body">
            <div class="table-container">
              <?= $tugas_akhir; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php
  }
  ?>
</div>