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
                <label class="control-label col-md-4">Pilih RPS</label>
                <div class="col-md-8">
                  <?php
                  echo form_dropdown('ddRps', $dd_rps, '', 'class="form-control input-sm" id="ddRps"');
                  ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 margin-bottom-20" id="kontrol">
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
          </div>
          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th>#</th>
                  <th>Prodi</th>
                  <th>NM KLS</th>
                  <th>Kode | Nm MK</th>
                  <th>SMT</th>
                  <th>SKS</th>
                  <th>Assign RPS</th>
                  <th>Oleh</th>
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