<div class="page-content">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?></span>
            <span class="caption-helper"><?= $judul_kecil; ?></span>
            <?php if ($this->session->flashdata('msg_rps')) { ?>
              <div class="alert alert-danger margin-top-10"> <?= $this->session->flashdata('msg_rps') ?></div>
            <?php } ?>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <!-- <a class="btn btn-sm btn-primary" href="<?= site_url('C_rps_new/sync_jadwal'); ?>">
                <i class="fa fa-file-excel-o"></i> Tambah RPS Dari Jadwal</a> -->
              <a class="btn btn-sm btn-primary" href="<?= site_url('C_rps_new/download_template'); ?>">
                <i class="fa fa-file-excel-o"></i> Download Template RPS Detail</a>
            </div>
            <?php
            if (!empty($this->session->flashdata('error'))) {
            ?>
              <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
            <?php
            }
            if (!empty($this->session->flashdata('sukses'))) {
            ?>
              <div class="alert alert-success"><?= $this->session->flashdata('sukses'); ?></div>
            <?php
            }
            ?>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Mata Kuliah</th>
                  <th>SKS</th>
                  <th>Koordinator</th>
                  
                  
                  <th style="min-width:120px;">Download</th>

                  <th style="min-width:120px;">Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>