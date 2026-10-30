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
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px">
              <div class="form-group">
                <label class="control-label col-md-4">Pilih Kelas Kuliah</label>
                <div class="col-md-8">
                  <?php
                  echo form_dropdown('ddKelasKuliah', $dd_kelas_kuliah, '', 'class="form-control input-sm" id="ddKelasKuliah"');
                  ?>
                </div>
              </div>
            </div>
            <div class="margin-bottom-20" id="kontrol">
              <div class="col-md-12 col-lg-12 margin-bottom-10">
                <a class="btn btn-default btn-sm blue-stripe" href="<?= site_url('c_isi_nilai_excel_mbkm/download_excel'); ?>">
                  <i class="fa fa-file-excel-o"></i> Download Excel</a>
              </div>
              <div class="col-md-12 col-lg-12 margin-bottom-15">
                <?php
                echo form_open('#', array('class' => 'form-inline', 'id' => 'form_submit'));
                echo '<div class="form-group">';
                echo form_label('Pilih File', 'Excel');
                echo form_upload(array(
                  'name' => 'userfile',
                  'id' => 'userfile',
                  'class' => 'form-control',
                  'value' => '',
                  'required' => 'required',
                  'data-icon' => 'false'
                ));
                $datax = array(
                  'name' => 'importfile',
                  'id' => 'importfile-id',
                  'class' => 'btn btn-default btn-sm blue-stripe',
                  'value' => 'Upload Data',
                );
                echo form_submit($datax, 'Upload Data');
                echo '</div>';
                echo form_close();
                ?>
              </div>
            </div>
          </div>
          <div class='col-md-12'>
            <div id="myalert"></div>
          </div>
          <div class="table-container" id="tabel_kehadiran"></div>
        </div>
      </div>
    </div>
  </div>
</div>