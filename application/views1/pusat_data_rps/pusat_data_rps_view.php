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
                  <th style="width:100px">Mata Kuliah</th>
                  <th>Dosen</th>
                  <th>PRODI</th>
                  <th>Kode | NM MK | SMT | SKS</th>
                  <th>Date</th>
                  
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