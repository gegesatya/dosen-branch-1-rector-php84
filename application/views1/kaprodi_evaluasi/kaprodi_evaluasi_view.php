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

            <div><?= $this->session->flashdata('error'); ?></div>
            <div class="row">
              <div class="col-md-6" style="margin-bottom: 20px">
                <div class="form-group">
                  <label class="control-label col-md-4">Pilih Angkatan</label>
                  <div class="col-md-8">
                    <select id='ddAngkatan' name='ddAngkatan' class="form-control col-md-6">
                      <option value='0'>All</option>
                      <option value='2015'>2015</option>
                      <option value='2016'>2016</option>
                      <option value='2017'>2017</option>
                      <option value='2018'>2018</option>
                      <option value='2019'>2019</option>
                      <option value='2020'>2020</option>
                      <option value='2021'>2021</option>
                      <option value='2022'>2022</option>
                      <option value='2023'>2023</option>
                    </select>
                  </div>
                </div>
              </div>
              <a class="btn btn-default btn-sm blue-stripe" href="<?= site_url('Kaprodi_evaluasi/download_excel'); ?>">
                <i class="fa fa-file-excel-o"></i> Download</a>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr role="row" class="heading">
                  <th rowspan="2" width="2%">#</th>
                  <th rowspan="2">NIM | Nama</th>
                  <th rowspan="2">Dosen Wali</th>
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