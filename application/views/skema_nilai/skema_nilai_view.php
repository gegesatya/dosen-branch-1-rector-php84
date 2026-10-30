<div class="page-content" style="padding-top: 0;">
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
          <div class="well">
            <p>Petunjuk:</p>
            <ul>
              <li>Sikap = Persen Nilai Sikap (Wajib diisi)</li>
              <li>QUI = Persen Nilai Quiz (Wajib diisi)</li>
              <li>TTS = Persen Nilai Tugas Terstruktur (Wajib diisi)</li>
              <li>UTS = Persen Nilai Ujian Tengah Semester (Wajib diisi)</li>
              <li>UAS = Persen Nilai Ujian Akhir Semester (Wajib diisi)</li>
              <li>UKN = Persen Nilai Unjuk Kinerja (Wajib diisi)</li>
              <li>PRY = Persen Nilai Proyek</li>
              <li>POT = Persen Nilai Portofolio</li>
              <li>PRD = Persen Nilai Produk</li>
              <li>PRE = Persen Nilai Presentasi</li>
            </ul>
          </div>
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Jadwal</th>
                  <th>Mata Kuliah</th>
                  <th>Sikap</th>
                  <th>QUI</th>
                  <th>TTS</th>
                  <th>UTS</th>
                  <th>UAS</th>
                  <th>UKN</th>
                  <th>PRY</th>
                  <th>POT</th>
                  <th>PRD</th>
                  <th>PRE</th>
                  <th style="width:100px;">Action</th>
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