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
              <span class="caption-subject font-green-sharp bold uppercase">Tabel Berita Acara Perkuliahan</span>
            </div>
            <div class="col-md-12">
              <p id="sub-caption"></p>
            </div>
        </div>
        <div class="portlet-body">
          <div class="well">
            <h4>Petunjuk mengisi Berita Acara Perkuliahan</h4>
            <ol>
              <li>Pilih Kelas Kuliah terlebih dahulu</li>
              <li>Akan muncul tombol Add Berita Acara Perkuliahan</li>
              <li>Klik tombol Add Berita Acara Perkuliahan</li>
              <li>Isi semua data yang ada pada form</li>
              <li>Perhatikan kembali tabel Berita Acara Perkuliahan yang sudah diisi</li>
              <li>Nomor Pertemuan harus berurut. Jika menghapus sebuah pertemuan, edit nomor pertemuan pada record setelahnya</li>
            </ol>
          </div>
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px">
              <div class="form-group">
                <label class="control-label col-md-4">Pilih Kelas Kuliah</label>
                <div class="col-md-8">
                <?php
                  echo form_dropdown('ddKelasKuliah', $dd_kelas_kuliah, '', 'class="form-control input-sm" id="ddKelasKuliah"');
                ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 margin-bottom-20" id="kontrol">
            
            <button class="btn btn-default btn-sm green-stripe" onclick="add_rencana()"><i class="glyphicon glyphicon-plus"></i> Add</button>
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            <button class="btn btn-default btn-sm yellow-stripe" onclick="ajax_copy()"><i class="fa fa-copy"></i> Generate Data</button>
          </div>
          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
            <thead>
            <tr role="row" class="heading">
              <th width="2%">#</th>
              <th>Tgl Realisasi</th>
              <th>Jam</th>
              
              <th>Bahan Kajian</th>
              <th>Sumber Belajar</th>
              <th width="10%">
                 Actions
              </th>
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