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
        <a href="<?= site_url('Lecturer_data_perwalian/sinkron_data'); ?>" class="btn btn-default btn-sm blue-stripe "><i class="fa fa-download"></i> Sinkron Data</a>
        <div class="portlet-body">

          <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
              <thead>
                <tr role="row" class="heading">
                  <th rowspan="2" width="2%">#</th>
                  <th rowspan="2">NIM | Nama</th>
                  <th rowspan="1" colspan="2">Sem 1</th>
                  <th rowspan="1" colspan="2">Sem 2</th>
                  
                  <th rowspan="1" colspan="2">Sem 3</th>
                  <th rowspan="1" colspan="2">Sem 4</th>
                  <th rowspan="1" colspan="2">Sem 5</th>
                  <th rowspan="1" colspan="2">Sem 6</th>
                  
                  <th rowspan="2">Action</th>
                </tr>
                <tr role="row" class="heading">
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
                  <th rowspan="1" colspan="1">IPS</th>
                  <th rowspan="1" colspan="1">IPK</th>
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