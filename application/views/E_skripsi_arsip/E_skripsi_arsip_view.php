<style>
  .col-judul {
    width: 350px !important;
    max-width: 350px !important;
    white-space: normal !important;
    word-break: break-word !important;
  }
</style>
<div class="page-content" style="padding-top: 0;">

  
  <div class="row" style="margin-bottom: 24px;">
    
    <div class="col-md-6">
      <div class="portlet light bordered" style="border-radius: 12px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.05); min-height: 380px;">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-pie-chart font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">Distribusi Penilaian Tugas Akhir</span>
          </div>
        </div>
        <div class="portlet-body text-center d-flex flex-column align-items-center justify-content-center">
          <div style="position: relative; height: 260px; width: 100%; max-width: 320px; margin: 0 auto;">
            <canvas id="gradeDonutChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-md-6">
      <div class="portlet light bordered" style="border-radius: 12px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.05); min-height: 380px;">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-dashboard font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Ketepatan Waktu Kelulusan Mahasiswa</span>
          </div>
        </div>
        <div class="portlet-body text-center d-flex flex-column align-items-center justify-content-center">
          <div style="position: relative; height: 260px; width: 100%; max-width: 320px; margin: 0 auto;">
            <canvas id="timePieChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light bordered" style="border-radius: 12px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-blue-hoki"></i>
            <span class="caption-subject font-blue-hoki bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width: 40px;">No</th>
                  <th style="width: 220px;">NIM | Nama</th>
                  <th style="width: 80px;">Nilai TA</th>
                  <th class="col-judul">Judul TA</th>
                  <th style="width: 330px;">Dewan Penguji</th>
                  <th style="width: 140px;">Tanggal Yudisium</th>
                  <th style="width: 80px;">Aksi</th>
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
