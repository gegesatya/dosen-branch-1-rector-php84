<div class="page-content">
  
  <div class="page-head">
    
    <div class="page-title">
      <h1>Jadwal Perkuliahan</small></h1>
    </div>
    

  </div>
  
  
  <ul class="page-breadcrumb breadcrumb hide">
    <li>
      <a href="javascript:;">Home</a><i class="fa fa-circle"></i>
    </li>
    <li class="active">
       Dashboard
    </li>
  </ul>
  
  

  <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
      
      <div class="portlet light ">
        <div class="portlet-title">
          <div class="caption caption-md">
            <i class="icon-bar-chart theme-font-color hide"></i>
            <span class="caption-subject theme-font-color bold uppercase">Jadwal</span>            
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-responsive" style="height: 320px; overflow-y: auto;">
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Kelas</th>
                    <th>Mata Kuliah</th>
                    <th>Ruang</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php
                    if($data_jadwal->num_rows() > 0){
                      $no = 1;
                      foreach ($data_jadwal->result() as $key => $value) {
                          ?>
                            <tr>
                              <td><?=$no;?></td>
                              <td><?=$value->nama_hari;?></td>
                              <td>
                                <?=date('H:i',strtotime($value->jam_mulai)).' sd '.date('H:i',strtotime($value->jam_selesai));?>
                              </td>
                              <td><?=$value->nm_kls;?></td>
                              <td><?=$value->nm_mk;?></td>
                              <td><?=$value->nama_ruang;?></td>
                            </tr>
                          <?php
                        $no++;
                      }
                    }
                  ?>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
    
  </div>
 
</div>