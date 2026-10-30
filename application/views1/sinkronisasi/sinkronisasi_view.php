
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
          <?=$this->session->flashdata('pesan');?>
          
          <div class="table-container">
            <?=$html_tabel;?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>