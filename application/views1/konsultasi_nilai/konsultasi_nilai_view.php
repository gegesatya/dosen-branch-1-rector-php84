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
            <p>Keterangan:</p>
            <ul>
              <li>Menu Konsultasi Nilai Merupakan Wadah Untuk Melakukan Pelacakan Terhadap Nilai Mahasiswa Perwalian </li>
              <li>Nilai Yang Dilacak Adalah Nilai Mata Kuliah Tidak Lulus</li>
              <li>Dalam Hal Perwalian, Dosen Wali Akan Melakukan Konsultasi Perwalian Dengan Bertanya ke Mahasiswa Mengenai Nilai Tidak Lulus</li>
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
                  <th>NIM | Nama</th>
                  <th>Jumlah MK Tdk Lulus</th>
                  <th>Nasihat Akademis</th>
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