


<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
    <?= $judul_kecil; ?>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <button class="btn waves-effect waves-light btn-success d-none d-lg-block m-l-10" onclick="show_bantuan()"><i class="fa fa-life-ring"></i> Panduan</button>
      <a class="btn waves-effect waves-light btn-dark d-none d-lg-block m-l-10" href="<?= site_url('c_kehadiran/download_excel/' . $id_jadwal); ?>"><i class="fa fa-solid fa-download"></i> Form Excel</a>
      <a href="javascript:void(0)" onclick="sinkronisasi()" class="btn waves-effect waves-light btn-primary d-none d-lg-block m-l-10"><i class="fa fa-solid fa-bell"></i> Sinkron</a>
      <button class="btn waves-effect waves-light btn-info d-none d-lg-block m-l-10" type="button" onclick="reload_table()"><i class="fas fa-angle-double-up"></i> Reload</button>
    </div>
  </div>
</div>






<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <?php
          echo form_open('#', array('class' => 'form-inline mt-4', 'id' => 'form_submit'));
          echo '<div class="form-group">';
          echo form_label('Upload File Absen, Pilih File : ', 'Excel', array('class' => 'form-label'));
          echo form_upload(array(
            'name' => 'userfile',
            'id' => 'userfile',
            'class' => 'form-control',
            'value' => '',
            'required' => 'required',
            'data-icon' => 'false'
          ));
          echo '</div>';

          $datax = array(
            'name' => 'importfile',
            'id' => 'importfile-id',
            'value' => 'Upload',
            'class' => 'btn m-l-10'
          );

          echo form_submit($datax);

          echo form_close();
          ?>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-sm" id="datatable_ajax">
            <thead>
              <tr role="row" class="heading">
                <th style="width: 10px !important;">#</th>
                <th>NIM | Nama</th>
                <th>P1</th>
                <th>P2</th>
                <th>P3</th>
                <th>P4</th>
                <th>P5</th>
                <th>P6</th>
                <th>P7</th>
                <th>P8</th>
                <th>P9</th>
                <th>P10</th>
                <th>P11</th>
                <th>P12</th>
                <th>P13</th>
                <th>P14</th>
                <th>P15</th>
                <th>P16</th>
                <th>%</th>
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


