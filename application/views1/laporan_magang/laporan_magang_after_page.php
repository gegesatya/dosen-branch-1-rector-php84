<script type="text/javascript">
  var save_method;

  function reload_table() {
    window.location.reload();
  }

  function edit_ami(id) {
    save_method = 'update_skema_nilai';
    $('#form_lapor_nilai')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#btnSave').text('Simpan');
    $('#btnSave').attr('disabled', false);

    $.ajax({
      url: "<?php echo site_url('C_laporan_magang/ajax_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_pelaksanaan_ami"]').val(data.id_pelaksanaan_ami);
        $('[name="daftar_tilik"]').val(data.daftar_tilik);
        $('[name="referensi"]').val(data.referensi);
        $('[name="jenis_dokumen"]').val(data.jenis_dokumen);
        $('[name="link_google"]').val(data.link_google);


        $('#modal_form_lapor_nilai').modal('show');
        $('.modal-title').text("Formulir Daftar Tilik Audit Mutu");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function save() {
    $('#btnSave').text('Saving...');
    $('#btnSave').attr('disabled', true);
    var url;
    var data;

    url = "<?php echo site_url('C_laporan_magang/ajax_update') ?>";
    data = $('#form_lapor_nilai').serializeArray()

    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_form_lapor_nilai').modal('hide');
          reload_table();
        } else {
          $('#btnSave').text('Simpan');
          $('#btnSave').attr('disabled', false);
        }

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error adding / update data');
        $('#btnSave').text('Simpan');
        $('#btnSave').attr('disabled', false);
      }
    });
  }

  // function openReview(url) {

  //   window.open(
  //     url,
  //     "ReviewLaporan",
  //     "width=1200,height=800,left=200,top=100,toolbar=0,status=0,resizable=yes,scrollbars=yes"
  //   );
  // }

  function openReview(url) {
    window.location.href = url;
  }

  function showPdf(url) {
    $('#iframe_pdf').attr('src', url);
    $('#modal_pdf').modal('show');
  }

  $(document).ready(function() {
    $('#modal_pdf').on('hidden.bs.modal', function () {
      $('#iframe_pdf').attr('src', '');
    });
  });

  function openNilai(nipd) {
    $('#form_nilai')[0].reset();
    $('#course_inputs_container').empty();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
      url: "<?php echo site_url('C_laporan_magang/ajax_get_nilai/') ?>" + nipd,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.student) {
          $('[name="nilai_nipd"]').val(data.student.nipd);
          $('[name="nipd"]').val(data.student.nipd);
          $('[name="nilai_nm_pd"]').val(data.student.nm_pd);
          $('[name="nilai_rata_fasilitator"]').val(data.student.nilai_rata_fasilitator);

          if (data.courses && data.courses.length > 0) {
            var html = '';
            $.each(data.courses, function(index, c) {
              var val_angka = (c.nilai_angka !== null) ? c.nilai_angka : '';
              var val_huruf = (c.nilai_huruf !== null) ? c.nilai_huruf : '';

              html += '<div class="form-group">';
              html += '  <label class="control-label col-md-4" style="text-align: left; padding-left: 25px;">' + c.nm_mk + '</label>';
              html += '  <div class="col-md-3">';
              html += '    <input name="course_grades[' + c.id_kls + '][nilai_angka]" class="form-control score-input" type="number" step="0.01" min="0" max="100" value="' + val_angka + '" placeholder="Nilai Angka" data-id-kls="' + c.id_kls + '">';
              html += '  </div>';
              html += '  <div class="col-md-3">';
              html += '    <input name="course_grades[' + c.id_kls + '][nilai_huruf]" id="letter_' + c.id_kls + '" class="form-control letter-output" type="text" value="' + val_huruf + '" readonly placeholder="Mutu">';
              html += '  </div>';
              html += '</div>';
            });
            $('#course_inputs_container').html(html);

            $('.score-input').on('input change keyup', function() {
              var val = $(this).val();
              var id_kls = $(this).attr('data-id-kls');
              var details = getGradeDetails(val);
              $('#letter_' + id_kls).val(details.letter);
            });
          } else {
            $('#course_inputs_container').html('<div class="alert alert-info" style="margin: 0 15px;">Tidak ada mata kuliah PKL/MBKM aktif yang ditemukan di KRS mahasiswa ini.</div>');
          }

          $('#modal_nilai').modal('show');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function getGradeDetails(score) {
    if (score === '' || score === null || isNaN(score)) {
      return { letter: '', index: '' };
    }
    var val = parseFloat(score);
    if (val >= 80.00) return { letter: 'A', index: '4.00' };
    if (val >= 75.00) return { letter: 'B+', index: '3.50' };
    if (val >= 70.00) return { letter: 'B', index: '3.00' };
    if (val >= 65.00) return { letter: 'C+', index: '2.50' };
    if (val >= 56.00) return { letter: 'C', index: '2.00' };
    if (val >= 41.00) return { letter: 'D', index: '1.00' };
    return { letter: 'E', index: '0.00' };
  }

  function saveNilai() {
    $('#btnSaveNilai').text('Saving...');
    $('#btnSaveNilai').attr('disabled', true);

    $.ajax({
      url: "<?php echo site_url('C_laporan_magang/ajax_save_nilai') ?>",
      type: "POST",
      data: $('#form_nilai').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_nilai').modal('hide');
          reload_table();
        } else {
          alert('Gagal menyimpan nilai.');
        }
        $('#btnSaveNilai').text('Simpan');
        $('#btnSaveNilai').attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error saving data');
        $('#btnSaveNilai').text('Simpan');
        $('#btnSaveNilai').attr('disabled', false);
      }
    });
  }
</script>

<div class="modal fade" id="modal_form_lapor_nilai" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form_lapor_nilai'];
        echo form_open('#', $attributes);
        ?>
        <input type="hidden" value="" name="id" />
        <div class="form-body">
          <div class="form-group">
            <label class="control-label col-md-3">Daftar Tilik</label>
            <div class="col-md-9">
              <textarea class="form-control" name="daftar_tilik" type="text" value="" rows="4" readonly></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Referensi</label>
            <div class="col-md-9">
              <input class="form-control" name="referensi" type="text" value="" readonly />
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3">Jenis Dokumen</label>
            <div class="col-md-9">
              <input class="form-control" name="jenis_dokumen" type="text" value="" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Link Google Drive</label>
            <div class="col-md-9">
              <textarea class="form-control" name="link_google" type="text" value="" rows="2"></textarea>
            </div>
          </div>

        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pdf" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1200px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Dokumen Tracking Kegiatan Magang</h3>
      </div>
      <div class="modal-body" style="padding: 0; height: 80vh;">
        <iframe id="iframe_pdf" src="" style="width: 100%; height: 100%; border: none;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_nilai" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Form Input Nilai Magang</h3>
      </div>
      <div class="modal-body form">
        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form_nilai'];
        echo form_open('#', $attributes);
        ?>
        <div class="form-body">
          <div class="form-group">
            <label class="control-label col-md-4">NIM</label>
            <div class="col-md-6">
              <input name="nilai_nipd" class="form-control" type="text" readonly>
              <input type="hidden" name="nipd" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4">Nama Mahasiswa</label>
            <div class="col-md-6">
              <input name="nilai_nm_pd" class="form-control" type="text" readonly>
            </div>
          </div>
          
          <h4 style="margin: 20px 15px 10px; padding-bottom: 5px; border-bottom: 1px solid #eee; font-weight: bold;">Nilai Fasilitator Lapangan</h4>
          <div class="form-group">
            <label class="control-label col-md-4">Nilai Rata-rata Fasilitator</label>
            <div class="col-md-3">
              <input name="nilai_rata_fasilitator" class="form-control" type="number" step="0.01" min="0" max="100" placeholder="Nilai Fasilitator">
            </div>
          </div>

          <h4 style="margin: 20px 15px 10px; padding-bottom: 5px; border-bottom: 1px solid #eee; font-weight: bold;">Nilai Akademik (SIAKAD)</h4>
          <div id="course_inputs_container">
            <!-- Dynamic courses inputs will be loaded here -->
          </div>
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSaveNilai" onclick="saveNilai()" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>