<div class="page-content">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?></span>
            <span class="caption-helper"><?= $judul_kecil; ?></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm green-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <button class="btn btn-default btn-sm blue-stripe" onclick="view_petunjuk()"><i class="glyphicon glyphicon-search"></i> Petunjuk</button>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>NIM | Nama</th>
                  <th>Pengajuan</th>
                  <th>Penguji</th>
                  <th>Status Disposisi</th>
                  <th>Seminar</th>
                  <th style="width:60px;">Action</th>
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