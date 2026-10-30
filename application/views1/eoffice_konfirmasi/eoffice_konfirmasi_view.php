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
            <div class="row">
              <div class="col-md-6" style="margin-bottom: 20px">
                <div class="form-group">
                  <label class="control-label col-md-4">Pilih Status</label>
                  <div class="col-md-8">
                    <select id='ddAngkatan' name='ddAngkatan' class="form-control col-md-6">
                      <option value='NIL'>All</option>
                      <option value='3'>Menunggu Konfirmasi</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th style="width:30px;">View</th>
                  <th style="width:30px;">KRS</th>
                  <th>NIM|Nama</th>
                  <th>No Surat|Tanggal</th>
                  <th>Kategori</th>
                  <th>Status</th>
                  <th style="width:30px;"></th>
                  
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