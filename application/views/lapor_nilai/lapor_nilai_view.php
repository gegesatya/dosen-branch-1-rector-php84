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
              <li>Pelaporan pengisian nilai sangat penting agar Bagian Akademik mengetahui Dosen sudah mengisi nilai atau belum </li>
              <li>Setelah melaporkan nilai, maka nilai yang diisi Dosen dapat diakses oleh Mahasiswa</li>
              <li>Sebelum melakukan pelaporan nilai, harap melakukan pemeriksaan kembali terhadap nilai yang diberikan</li>
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
                  <th>A</th>
                  <th>B+</th>
                  <th>B</th>
                  <th>C+</th>
                  <th>C</th>
                  <th>D</th>
                  <th>E</th>
                  <th>Status</th>
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