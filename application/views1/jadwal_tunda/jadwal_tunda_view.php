<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
            <?php if ($this->session->flashdata('msg_krs')) { ?>
              <div class="alert alert-danger margin-top-10"> <?= $this->session->flashdata('msg_krs') ?> </div>
            <?php } ?>

            <div class="alert alert-danger margin-top-10"> Perhatian! Anda sedang berada pada direktori Tahun Ajaran <?= $this->session->userdata('setting')->id_smt_tunda; ?> <br><br><small> Harap berhati-hati dalam melakukan perubahan data pada Tahun Ajaran ini. Gunakan fitur ini hanya untuk melakukan revisi nilai dan untuk mengupload nilai yang belum sempat diisi pada waktu regular.</small> </div>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Hari/Jam</th>
                  <th>Ruang</th>
                  <th>Nama Jadwal</th>
                  <th>NM KLS</th>
                  <th>KODE MK | NM MK</th>
                  <th>RPS</th>
                  <th>Generate BAP</th>
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