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

            <div class="row">
              <div class="col-md-6" style="margin-bottom: 20px">
                <div class="form-group">
                  <label class="control-label col-md-4">Pilih Status</label>
                  <div class="col-md-8">
                    <select id='ddAngkatan' name='ddAngkatan' class="form-control col-md-6">
                      <option value='0'>All</option>
                      <option value='1'>Daftar</option>
                      <option value='2'>Sudah Mengajukan</option>
                      <option value='3'>Final</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>NIM | Nama</th>
                  <th>Judul Yang Ditetapkan</th>
                  <th>Messages</th>
                  <th>Status</th>
                  <th>Action</th>

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