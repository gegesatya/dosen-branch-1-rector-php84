<div class="page-content">
  <div class="page-head">
    <div class="page-title">
      <h1><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase">Tabel Data Mata Kuliah Kurikulum</span>
            <span class="caption-helper">Prodi <?= $this->db->where('id_sms', $this->session->userdata('prodi'))->get('sms')->row()->nm_lemb; ?></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 20px">
              <div class="form-group">
                <label class="control-label col-md-4">Pilih Kurikulum</label>
                <div class="col-md-8">
                  <?php
                  echo form_dropdown('id_kurikulum_sp', $dd_kurikulum, '', 'class="form-control"');
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="table-container">
            <div style="margin-bottom: 20px">
              
              <button class="btn btn-default btn-xs" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kurikulum</th>
                  <th>Mata Kuliah</th>
                  <th>SKS MK</th>
                  <th>SMT</th>
                  <th>Wajib?</th>
                  <th>Info RPS</th>
                  <th style="width:60px;">Action</th>
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