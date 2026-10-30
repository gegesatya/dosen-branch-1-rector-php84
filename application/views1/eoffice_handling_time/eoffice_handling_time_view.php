<div class="page-content">
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
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-xs" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            
            <table id="datatable_ajax" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th style="min-width: 160px;">NIM|Nama</th>
                  <th style="min-width: 160px;">No Surat|Tanggal</th>
                  <th>Permohonan</th>
                  <th>Diajukan</th>
                  <th>Approval 1</th>
                  <th>Approval 2</th>
                  <th>Time 1</th>
                  <th>Time 2</th>
                  <th>Overall</th>

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