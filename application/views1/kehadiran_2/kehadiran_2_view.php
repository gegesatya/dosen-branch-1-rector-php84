<style>
  .dataTables_wrapper {
    font-family: tahoma;
    font-size: 12px !important;
    position: relative;
    clear: both;
    *zoom: 1;
    zoom: 1;
  }

  .dataTables_wrapper th {
    font-family: tahoma;
    font-size: 12px !important;
    position: relative;
    clear: both;
    *zoom: 1;
    zoom: 1;
    width: 25px;
  }
</style>
<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
          </div>
          <div class="col-md-12">
            <p id="sub-caption"></p>
          </div>
        </div>
        <div class="portlet-body">
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px">
              <div class="form-group">
                <label class="control-label col-md-4">Pilih Jadwal</label>
                <div class="col-md-8">
                  <?php
                  echo form_dropdown('ddKelasKuliah', $dd_kelas_kuliah, '', 'class="form-control input-sm" id="ddKelasKuliah"');
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class='col-md-12'>
            <div id="myalert"></div>
          </div>
          <div class="margin-bottom-10" id="kontrol">
            <div class="col-md-12 col-lg-12 margin-bottom-10">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <a href="javascript:void(0)" onclick="sinkronisasi()" class="btn btn-default btn-sm blue-stripe"><i class="glyphicon glyphicon-plus"></i> Sinkronisasi</a>
              <a class="btn btn-default btn-sm blue-stripe" href="<?= site_url('c_kehadiran/download_excel'); ?>">
                <i class="fa fa-file-excel-o"></i> Download</a>
              <button class="btn btn-default btn-sm blue-stripe" onclick="show_bantuan()"><i class="fa fa-life-ring"></i> Panduan</button>
            </div>
            <div class="col-md-12 col-lg-12">
              <?php
              echo form_open('#', array('class' => 'form-inline', 'id' => 'form_submit'));

              echo '<div class="form-group">';
              echo form_label('Upload File Absen, Pilih File: ', 'Excel');
              echo form_upload(array(
                'name' => 'userfile',
                'id' => 'userfile',
                'class' => 'form-control',
                'value' => '',
                'required' => 'required',
                'data-icon' => 'false'
              ));

              echo '</div>';
              $datax = array(
                'name' => 'importfile',
                'id' => 'importfile-id',
                'class' => 'btn btn-default btn-sm blue-stripe',
                'value' => 'Upload Data',
              );
              echo form_submit($datax, 'Upload');

              echo form_close();
              ?>
            </div>





            



            </ol>
          </div>

        </div>

        <div class="table-container">
          <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
            <thead>
              <tr role="row" class="heading">
                <th>#</th>
                <th>NIM | Nama</th>
                
                <th>P17</th>
                <th>P18</th>
                <th>P19</th>
                <th>P20</th>
                <th>P21</th>
                <th>P22</th>
                <th>P23</th>
                <th>P24</th>
                <th>P25</th>
                <th>P26</th>
                <th>P27</th>
                <th>P28</th>
                <th>P29</th>
                <th>P30</th>
                <th>%</th>

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