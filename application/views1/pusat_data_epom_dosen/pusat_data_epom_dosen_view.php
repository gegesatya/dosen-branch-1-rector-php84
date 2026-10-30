<?php
$id_smt = $this->session->userdata('id_smt');
$ts = substr($id_smt, 0, 4);

$ts_2 = $ts - 2;
$ts_1 = $ts - 1;

$id_smt_1 = $ts_2 . "1";
$id_smt_2 = $ts_2 . "2";
$id_smt_3 = $ts_1 . "1";
$id_smt_4 = $ts_1 . "2";
$id_smt_5 = $ts . "1";
$id_smt_6 = $ts . "2";
?>
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
                  <th>NIDN</th>
                  <th>Dosen</th>
                  <th>Ikatan kerja</th>
                  <th>TS-2 <br>(<?= $id_smt_1; ?>)</th>
                  <th>TS-2 <br>(<?= $id_smt_2; ?>)</th>
                  <th>TS-1 <br>(<?= $id_smt_3; ?>)</th>
                  <th>TS-1 <br>(<?= $id_smt_4; ?>)</th>
                  <th>TS <br>(<?= $id_smt_5; ?>)</th>
                  <th>TS <br>(<?= $id_smt_6; ?>)</th>
                  
                  <th width="10%">Detail</th>
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