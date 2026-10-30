<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> </span>
            <span class="caption-helper">TAHUN AJARAN <?= $this->session->userdata('setting')->tahun_ajaran_aktif; ?></span>

            <?php if ($this->session->flashdata('msg')) { ?>
              <div class="alert alert-danger margin-top-10"> <?= $this->session->flashdata('msg') ?> </div>
            <?php } ?>

          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container table-responsive-2">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <a href="<?= site_url("Lecturer_persetujuan_krs/show_makul_prodi"); ?>" class="btn btn-default btn-sm blue-stripe" target="_new">
                <i class="fa fa-life-ring"></i> Daftar Kelas Kuliah Prodi</a>
            </div>
            
            <div class="row">
              <div class="col-md-6" style="margin-bottom: 20px">
                <div class="form-group">
                  <label class="control-label col-md-4">Tampilkan Berdasar Status</label>
                  <div class="col-md-8">
                    <select id='ddstatus' name='ddstatus' class="form-control col-md-6">
                      <option value='0'>All</option>
                      <option value='1'>Mendaftar mata kuliah</option>
                      <option value='2'>Membutuhkan Persetujuan/Approval</option>
                      <option value='3'>Final/Selesai</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>Action</th>
                  
                  <th>NIM | Nama</th>
                  <th>SKS</th>
                  <th>Messages</th>
                  <th>Status</th>
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