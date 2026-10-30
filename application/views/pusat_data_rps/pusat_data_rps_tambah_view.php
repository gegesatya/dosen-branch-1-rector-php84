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
                <label class="col-md-3 control-label">Koordinator RPS</label>
                <div class="col-md-9">
                  <?php
                  echo form_dropdown('koordinator_rps', $dd_koordinator, '', 'class="form-control" required');
                  ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Tanggal RPS</label>
                <div class="col-md-9">
                  <input name="tanggal_rps" placeholder="" class="form-control datepicker" type="text" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Mata Kuliah</label>
                <div class="col-md-9">
                  <input name="general_mk" placeholder="Nama mata kuliah secara umum, misal: Bahasa Indonesia" class="form-control" type="text" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Deskripsi Mata Kuliah</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="deskripsi_mata_kuliah" name="deskripsi_mata_kuliah" required></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 control-label">Capaian Pembelajaran Mata Kuliah</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" required id="capaian_pembelajaran_mata_kuliah" name="capaian_pembelajaran_mata_kuliah"></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 control-label">Referensi Utama</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="referensi" name="referensi" required></textarea>
                </div>
              </div>
              <div class="well">
                <h4>Capaian Pembelajaran Lulusan yang dibebankan kepada matakuliah</h4>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Sikap</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="capaian_sikap" name="capaian_sikap" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Pengetahuan</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="capaian_pengetahuan" name="capaian_pengetahuan" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Keterampilan Umum</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="capaian_keterampilan_umum" name="capaian_keterampilan_umum" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Keterampilan Khusus</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="capaian_keterampilan_khusus" name="capaian_keterampilan_khusus" required></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Tugas Mahasiswa dalam 1 Semester</label>
                <div class="col-md-9">
                  <textarea class="form-control summernote" id="tugas_mahasiswa" name="tugas_mahasiswa" required></textarea>
                </div>
              </div>

            </div>
            <div class="form-actions">
              <div class="row">
                <div class="col-md-offset-3 col-md-9">
                  <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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