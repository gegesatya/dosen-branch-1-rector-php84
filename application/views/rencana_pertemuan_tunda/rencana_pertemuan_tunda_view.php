<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
            <div class="alert alert-danger margin-top-10"> Perhatian! Anda sedang berada pada direktori Tahun Ajaran <?= $this->session->userdata('setting')->id_smt_tunda; ?> <br><br><small> Harap berhati-hati dalam melakukan perubahan data pada Tahun Ajaran ini. Gunakan fitur ini hanya untuk melakukan revisi nilai dan untuk mengupload nilai yang belum sempat diisi pada waktu regular.</small> </div>

          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px">
              <div class="form-group">
                <label class="control-label col-md-4">Pilih Nama Jadwal</label>
                <div class="col-md-8">
                  <?php
                  echo form_dropdown('ddKelasKuliah', $dd_kelas_kuliah, '', 'class="form-control input-sm" id="ddKelasKuliah"');
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 margin-bottom-20" id="kontrol">
            
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            
          </div>
          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th>W/M</th>
                  <th>Tanggal | Jam <br> Bahasan | Referensi</th>
                  
                  <th>Materi <br> Tersampaikan (%)</th>
                  <th>Is Realisasi</th>
                  <th>Tgl Realisasi</th>
                  <th>Actions</th>
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