<div class="page-content">
  <div class="page-head">
    <div class="page-title">
      <h1><?=$judul_besar;?> <small><?=$judul_kecil;?></small></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
              <i class="fa fa-gift font-green-sharp"></i>
              <span class="caption-subject font-green-sharp bold uppercase">Template Berita Acara Perkuliahan</span>
            </div>
            <div class="col-md-12">
              <p id="sub-caption"></p>
            </div>
        </div>
        <div class="portlet-body">
          <div class="col-md-12 margin-bottom-20">
            <button class="btn btn-default btn-sm green-stripe" onclick="add_template()"><i class="glyphicon glyphicon-plus"></i> Add</button>
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
          </div>
          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
            <thead>
            <tr role="row" class="heading">
              <th width="2%">#</th>
              <th>SMT</th>
              <th>NM KLS</th>
              <th>NM Mata Kuliah</th>
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