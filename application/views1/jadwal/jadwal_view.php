<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
            <?php if ($this->session->flashdata('msg_krs')) { ?>
              <div class="alert alert-danger margin-top-10" style="display: flex; justify-content: space-between; align-items: center;"> 
                <span><?= $this->session->flashdata('msg_krs') ?></span>
                <button class="btn btn-xs btn-danger" style="margin-left: 10px;" onclick="$('#modal_krs_notification').modal('show')"><i class="fa fa-eye"></i> Tampilkan</button>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="portlet-body">
          <div class="note note-info">
            <h4 class="block">Informasi!</h4>
            <p>Untuk mengisi nilai bisa dilakukan melalui halaman ini, dengan cara mengisi Skema Nilai, kemudian klik tombol Isi Nilai. Cara ini bisa dilakukan jika tidak ingin mengisi RPS dan Meeting.</p>
            <p>Cara lainnya dapat dilakukan dengan mengakses Virtual Class. Jika sudah membuat RPS dan Generate Meeting, bisa mengakses Virtual Class.</p>
          </div>
          <div class="table-container">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Hari/Jam/Ruang</th>
                  <th>Kode MK | NM MK</th>
                  
                  <th>Data Kelas</th>
                  <th style="width:80px !important">RPS</th>
                  <th style="width:80px !important">Nilai</th>
                  <th style="width:80px !important">Meeting</th>
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