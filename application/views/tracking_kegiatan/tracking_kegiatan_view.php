<div class="page-content">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small> </span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container table-responsive-2">
            <div style="margin-bottom: 20px">
              <button class="btn btn-success btn-sm" onclick="add_tracking()"><i class="glyphicon glyphicon-plus"></i> Add </button>
              <button class="btn btn-default btn-sm" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              
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
            <table id="datatable_ajax" class="table table-bordered">
              <thead>
                <tr>
                  <th style="min-width:20px">No</th>
                  <th>Week</th>
                  <th>Periode Laporan</th>
                  
                  <th style="min-width:150px">Action</th>
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