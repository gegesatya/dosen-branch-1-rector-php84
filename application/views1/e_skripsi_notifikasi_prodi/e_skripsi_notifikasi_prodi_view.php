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
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            <table id="datatable_ajax" class="table table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width: 25px !important;">No</th>
                  <th style="width: 150px !important;">Tgl Notifikasi</th>
                  <th style="width: 250px !important;">Nama</th>
                  <th style="width: 250px !important;">Jenis Notif</th>
                  <th>Notifikasi</th>
                  <th style="width: 100px !important;">Action</th>
                  
                  

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