<script type="text/javascript">
  var save_method;
  var table;

  var TableAjax = function() {
    var handleRecords = function() {
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagingType": "full_numbers",
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_lapor_nilai/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
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

  function edit_skema_nilai(id) {
    save_method = 'update_skema_nilai';
    $('#form_lapor_nilai')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#btnSave').text('Simpan');
    $('#btnSave').attr('disabled', false);

    $.ajax({
      url: "<?php echo site_url('C_lapor_nilai/ajax_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id"]').val(data.id);
        $('[name="tag_name"]').val(data.tag_name);
        $('[name="is_lapor_nilai"]').val(data.is_lapor_nilai);
        $('#tbl').html(data.data_nilai);

        $('#modal_form_lapor_nilai').modal('show');
        $('.modal-title').text("Lapor Pengisian Nilai " + data.tag_name);
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

    url = "<?php echo site_url('C_lapor_nilai/ajax_update') ?>";
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
            <label class="control-label col-md-3">Nama Jadwal</label>
            <div class="col-md-9">
              <input class="form-control input-sm" name="tag_name" type="text" value="" readonly />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Mahasiswa Tdk Lulus</label>
            <div class="col-md-9">
              <div id="tbl"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Status Laporan</label>
            <div class="col-md-9">
              <?php
              echo form_dropdown('is_lapor_nilai', ['0' => 'Belum Isi Nilai dan Belum Publish', '1' => 'Sudah Isi Nilai dan Sudah Publish'], '', 'class="form-control" required');
              ?>
              <span class="help-block"></span>
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