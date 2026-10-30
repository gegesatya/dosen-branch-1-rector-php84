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
            <span class="caption-subject font-green-sharp bold uppercase">Bimbingan KRS Online</span>
            
          </div>
        </div>
        <div class="portlet-body">
          
            <div class="clearfix margin-bottom-10">
                <button class="btn btn-default bg-navy"  onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            <div class="row">
              <div class="col-md-6" style="margin-bottom: 20px">
                <div class="form-group">
                  <label class="control-label col-md-4">Pilih Angkatan</label>
                  <div class="col-md-8">
                    <select id='ddAngkatan' name='ddAngkatan' class="form-control col-md-6">
                      <option value='0'>All</option> 
                      <option value='2015'>2015</option> 
                      <option value='2016'>2016</option> 
                      <option value='2017'>2017</option> 
                      <option value='2018'>2018</option> 
                      <option value='2019'>2019</option> 
                      <option value='2020'>2020</option> 
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive">
                <table id="datatable_ajax" class="table table-bordered" >
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th style="width: 50px;">Tahun</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>IPK / SKS</th>  
                            <th>Pengajuan</th>  
                            <th>Status</th>  
                            <th>Action</th>  
                            <th>Revisi</th>  
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