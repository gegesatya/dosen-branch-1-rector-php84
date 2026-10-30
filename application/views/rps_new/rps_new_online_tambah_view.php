<div class="page-content">
  <?php
  if ($this->session->flashdata('msg_rps')) { ?>
    <div class="alert alert-danger"> <?= $this->session->flashdata('msg_rps') ?></div>
  <?php } else { ?>
    <div class="row">
      <div class="col-md-12 ">
        <div class="portlet box green ">
          <div class="portlet-title">
            <div class="caption">
              Tambah RPS
            </div>

          </div>
          <div class="portlet-body form">
            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form', 'role' => 'form'];
            echo form_open('#', $attributes);
            ?>
            <input type="hidden" value="" name="id_rps" />
            <div class="form-body">
              <div class="form-group">
                <label class="col-md-4 control-label">Ketua Program Studi/Wadir 1</label>
                <div class="col-md-6">
                  <?php
                  echo form_dropdown('kaprodi_rps', $dd_kaprodi, '', 'class="form-control" required');
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Koordinator RPS</label>
                <div class="col-md-6">
                  <?php
                  echo form_dropdown('koordinator_rps', $dd_koordinator, '', 'class="form-control" required');
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-4">Dosen Pengampu</label>
                <div class="col-md-6">
                  <input name="dosen_pengampu" id="dosen_pengampu" placeholder="Dosen Pengampu" class="form-control" type="text" required>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Tanggal RPS</label>
                <div class="col-md-6">
                  <input name="tanggal_rps" placeholder="" class="form-control datepicker" type="text" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Mata Kuliah</label>
                <div class="col-md-6">

                  <?php
                  echo form_dropdown('id_mk', $dd_mata_kuliah, '', 'class="form-control" required');
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Deskripsi Mata Kuliah</label>
                <div class="col-md-6">
                  <textarea class="form-control summernote" id="deskripsi_mata_kuliah" name="deskripsi_mata_kuliah" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Prasyarat Mata Kuliah</label>
                <div class="col-md-6">
                  <textarea class="form-control summernote" id="prasyarat_mata_kuliah" name="prasyarat_mata_kuliah" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Daftar Referensi</label>
                <div class="col-md-6">
                  <textarea class="form-control summernote" id="referensi" name="referensi" required></textarea>
                </div>
              </div>
              <div class="well">
                <h4>Capaian Pembelajaran</h4>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Capaian Pembelajaran Lulusan <br>yang dibebankan kepada matakuliah</label>
                <div class="col-md-6">
                  <textarea class="form-control summernote" id="cpl" name="cpl" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label">Tujuan Belajar (CPMK)</label>
                <div class="col-md-6">
                  <textarea class="form-control summernote" id="tujuan_belajar" name="tujuan_belajar" required></textarea>
                </div>
              </div>


            </div>
            <div class="form-actions">
              <div class="row">
                <div class="col-md-offset-4 col-md-9">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

                  <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
                </div>
              </div>
            </div>
            <?php
            echo form_close();
            ?>
          </div>
        </div>

      </div>
    </div>
  <?php } ?>

</div>