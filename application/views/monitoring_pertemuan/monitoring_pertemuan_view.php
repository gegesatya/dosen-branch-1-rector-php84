<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
        </div>
        <div class="portlet-body form">
          
          <form action="#" class="form-horizontal">
            <div class="form-body">
              <div class="form-group">
                <label class="col-md-4 control-label">Nama Dosen</label>
                <div class="col-md-6">
                  <input type="text" class="form-control" value="<?= $jadwal->nm_ptk; ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Mata Kuliah</label>
                <div class="col-md-6">
                  <input type="text" class="form-control" value="<?= $jadwal->tag_name; ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Hari</label>
                <div class="col-md-6">
                  <input type="text" class="form-control" value="<?= $jadwal->nama_hari; ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Jam</label>
                <div class="col-md-3">
                  <input type="text" class="form-control" value="<?= $jadwal->jam_mulai; ?>">
                </div>
                <div class="col-md-3">
                  <input type="text" class="form-control" value="<?= $jadwal->jam_selesai; ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Ruang Kuliah</label>
                <div class="col-md-6">
                  <input type="text" class="form-control" value="<?= $jadwal->nama_ruang; ?>">
                </div>
              </div>

            </div>
          </form>
          
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="portlet light">

      <div class="portlet-body">
        <div class="table-container">
          <div style="margin-bottom: 20px">
            <button class="btn btn-default btn-sm" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
          </div>
          <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Pertemuan</th>
                <th>Bahasan</th>
                <th>Kehadiran</th>
                <th>Realisasi</th>
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