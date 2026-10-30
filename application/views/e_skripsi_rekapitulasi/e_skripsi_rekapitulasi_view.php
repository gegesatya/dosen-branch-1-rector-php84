<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> </span>
            <span class="caption-helper">TAHUN AJARAN <?= $this->session->userdata('setting')->tahun_ajaran_aktif; ?></span>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container table-responsive-2">
            <div style="margin-bottom: 20px">
              <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            </div>
            
            <?php 
              $id_sms = $this->session->userdata('prodi');
              $is_akuntansi_perpajakan = ($id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');
            ?>

            <table id="datatable_ajax" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th style="width: 50px;">No</th>
                  <th>Nama Dosen</th>
                  <th>Pembimbing Utama</th>
                  <?php if ($is_akuntansi_perpajakan): ?>
                    <th>Penguji 1</th>
                    <th>Penguji 2</th>
                  <?php else: ?>
                    <th>Pembimbing Pendamping</th>
                    <th>Penguji</th>
                  <?php endif; ?>
                  <th>Total</th>
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