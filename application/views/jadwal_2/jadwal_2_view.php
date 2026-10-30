<div class="page-content">
  <div class="page-head">
    <div class="page-title">
      <h1><?=$judul_besar;?> <small><?=$judul_kecil;?></small></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Jadwal Kuliah</span>
            
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Hari/Jam</th>
                  <th>Ruang</th>
                  <th>Nama Jadwal</th>
                  <th>Mata Kuliah</th>
                  <th style="width:180px;">Action</th>
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