<div class="page-content" style="padding-top: 0;">
  <!-- <div class="page-head">
    <div class="page-title">
      <h1><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></h1>
    </div>
  </div> -->
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <button class="btn btn-default btn-sm blue-stripe" onclick="view_rubrik()"><i class="glyphicon glyphicon-search"></i> Rubrik Penilaian Sidang TA</button>

            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>NIM | Nama</th>
                  <th>Status Dan Nilai</th>
                  <th>Penilaian<br> Rata-rata</th>
                  <th>Nilai & Revisi</th>
                  <th>Berita Acara</th>
                  <th style="width:40px;">Cetak</th>
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