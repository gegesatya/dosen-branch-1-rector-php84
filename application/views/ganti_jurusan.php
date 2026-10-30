<!-- <div class="page-content">
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
            <span class="caption-subject font-green-sharp bold uppercase">Setting Ganti Tahun Ajaran</span>
            <span class="caption-helper">Prodi <?=$this->db->where('id_sms', $this->session->userdata('prodi'))->get('sms')->row()->nm_lemb;?></span>
          </div>
        </div>
        <div class="portlet-body">
          <?php echo form_open(site_url("ganti_jurusan/simpan"), "role='form'");?>
            <label for="exampleInputEmail1" class="col-md-2 form-control-label">Tahun Ajaran </label>
            <div class="col-md-6">
              <?php 
               $dd_tahun_attribute = 'class="form-control"';
               $val_tahun = !empty($this->session->userdata('tahun_ajaran'))?$this->session->userdata('tahun_ajaran'):'';
               echo form_dropdown('tahun_ajaran', $tahun, $val_tahun, $dd_tahun_attribute);
              ?>
            </div>

            <?php echo form_submit('submit', 'Ubah', 'class="btn btn-primary col-md-2');?>
            
            <?php echo form_close(); ?>

        </div>
      </div>
    </div>
  </div>
</div> -->

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
            <span class="caption-helper">Prodi <?=$this->db->where('id_sms', $this->session->userdata('prodi'))->get('sms')->row()->nm_lemb;?></span>
          </div>
        </div>
        <div class="portlet-body">
          <?php echo form_open(site_url("ganti_jurusan/simpan"), "role='form'");?>
            <label for="exampleInputEmail1" class="col-md-2 form-control-label">Tahun Ajaran </label>
            <div class="col-md-6">
              <?php 
               $dd_tahun_attribute = 'class="form-control"';
               $val_tahun = !empty($this->session->userdata('tahun_ajaran'))?$this->session->userdata('tahun_ajaran'):'';
               echo form_dropdown('tahun_ajaran', $tahun, $val_tahun, $dd_tahun_attribute);
              ?>
            </div>

            <?php echo form_submit('submit', 'Ubah', 'class="btn btn-primary');?>
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>