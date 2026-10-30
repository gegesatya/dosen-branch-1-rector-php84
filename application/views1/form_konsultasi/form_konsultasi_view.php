<div class="page-content">
  <div class="page-head">
    <div class="page-title">
      <h1><?=$halaman;?> <small><?=$sub_halaman;?></small></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Form Konsultansi Online</span>
          </div>
        </div>
        <div class="portlet-body">
          
            <div class="clearfix margin-bottom-10">
                <button class="btn btn-default bg-navy"  onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            <div class="table-responsive">
                <table id="datatable_ajax" class="table table-bordered" >
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>NIM / Nama</th>
                            <th>IPK / SKS</th>  
                            <th>Catatan Dosen</th>  
                            <th>Konsultasi Akademik</th>  
                            <th>Feedback Mahasiswa</th>  
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