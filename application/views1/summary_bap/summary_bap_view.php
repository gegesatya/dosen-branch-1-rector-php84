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
          <div class="col-md-12 margin-bottom-20">
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
          </div>
          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th width="2%">#</th>
                  <th>SMT</th>
                  <th>JADWAL</th>
                  
                  <th width="10%">
                    View BAP
                  </th>
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