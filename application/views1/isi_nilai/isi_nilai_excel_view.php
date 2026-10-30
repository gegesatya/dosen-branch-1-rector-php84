


<?php
$param = $this->uri->segment(3);
?>
<div class="row page-titles">
  <div class="col-md-5 align-self-center">
    <h5 class="text-themecolor"><?= $judul_besar; ?></h5>
    <?= $judul_kecil; ?>
  </div>
  <div class="col-md-7 align-self-center text-right">
    <div class="d-flex justify-content-end align-items-center">
      <button class="btn waves-effect waves-light btn-success d-none d-lg-block m-l-10" onclick="show_bantuan()"><i class="fa fa-life-ring"></i> Panduan</button>
      <?php
      if ($is_skema) {
      ?>
        <a class="btn waves-effect waves-light btn-dark d-none d-lg-block m-l-10" href="<?= site_url('c_isi_nilai_excel/download_excel/' . $id_jadwal); ?>"><i class="fa fa-solid fa-download"></i> Form Excel</a>
      <?php
      }
      ?>
      <button class="btn waves-effect waves-light btn-info d-none d-lg-block m-l-10" type="button" onclick="reload_table()"><i class="fas fa-angle-double-up"></i> Reload</button>
    </div>
  </div>
</div>






<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="m-b-15">
          <?php
          if ($is_skema) {
            echo form_open('#', array('class' => 'form-inline', 'id' => 'form_submit'));
            echo "<input type='hidden' name='id_jadwal' value='$param'/>";
            echo '<div class="form-group">';
            echo form_label('Pilih File', 'Excel');
            echo form_upload(array(
              'name' => 'userfile',
              'id' => 'userfile',
              'class' => 'form-control',
              'value' => '',
              'required' => 'required',
              'data-icon' => 'false'
            ));
            $datax = array(
              'name' => 'importfile',
              'id' => 'importfile-id',
              'class' => 'btn btn-default m-l-10',
              'value' => 'Upload Data',
            );
            echo form_submit($datax, 'Upload Data');
            echo '</div>';
            echo form_close();
          }
          ?>
        </div>
        <div id="myalert"></div>
        <div class="table-container" id="tabel_kehadiran"></div>
      </div>
    </div>
  </div>
</div>


