<script type="text/javascript">
  var save_method;
  var table;

  var TableAjax = function() {
    var handleRecords = function() {
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_konsultasi_nilai/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4],
          "orderable": false,
        }, ],

      });
    }

    return {
      init: function() {
        handleRecords();
      }

    };

  }();

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function edit_catatan(nipd) {
    save_method = 'update_skema_nilai';
    $('#form_lapor_nilai')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#btnSave').text('Simpan');
    $('#btnSave').attr('disabled', false);

    $.ajax({
      url: "<?php echo site_url('C_konsultasi_nilai/ajax_edit/') ?>",
      type: "GET",
      dataType: "JSON",
      data: {
        nipd: nipd
      },
      success: function(data) {
        // $('[name="id_kls"]').val(data.id_kls);
        $('[name="nipd"]').val(data.nipd);
        $('[name="nm_pd"]').val(data.nm_pd);
        $('#tbl').html(data.tabel_nilai);
        $('[name="krs_terakhir"]').val(data.krs_terakhir);
        // $('[name="kode_mk"]').val(data.kode_mk);
        // $('[name="nm_mk"]').val(data.nm_mk);
        // $('[name="nilai_angka"]').val(data.nilai_angka);
        // $('[name="nilai_huruf"]').val(data.nilai_huruf);
        // $('[name="krs_terakhir"]').val(data.id_smt_krs);
        // $('[name="ipk"]').val(data.ipk);
        // $('[name="sks_kum"]').val(data.sks_kum);

        $('#data_pesan').html(data.data_pesan);


        $('#modal_form_lapor_nilai').modal('show');
        $('.modal-title').text("Catatan Konsultasi Akademik ");
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

    url = "<?php echo site_url('C_konsultasi_nilai/ajax_update') ?>";
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
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
          }
        }
        $('#btnSave').text('Simpan');
        $('#btnSave').attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error adding / update data');
        $('#btnSave').text('Simpan');
        $('#btnSave').attr('disabled', false);
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

        <input type="hidden" value="" name="nipd" />
        <div class="form-body">
          <div class="form-group">
            <label class="control-label col-md-3">NIM</label>
            <div class="col-md-9">
              <input class="form-control input-sm" name="nipd" type="text" value="" readonly />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Nama</label>
            <div class="col-md-9">
              <input class="form-control input-sm" name="nm_pd" type="text" value="" readonly />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">KRS Terakhir</label>
            <div class="col-md-9">
              <input class="form-control input-sm" name="krs_terakhir" type="text" value="" readonly />
            </div>
          </div>
          <h5>Daftar Mata Kuliah Tidak Lulus</h5>

          <div class="form-group">
            <div class="col-md-12">
              <div id="tbl"></div>
            </div>
          </div>

          <hr>
          <div class="well" id="data_pesan"></div>
          <h5>Nasihat Akademis: </h5>
          <div class="form-group">

            <div class="col-md-12">
              <textarea class="form-control" rows="3" name="konsultasi_nilai"></textarea>
              <span class="help-block"></span>
            </div>
          </div>


        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Kirim</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>