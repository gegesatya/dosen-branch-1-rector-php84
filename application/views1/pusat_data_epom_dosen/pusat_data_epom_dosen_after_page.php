<script type="text/javascript">
  var save_method;
  var table;
  var arr_tahun = [];
  var TableAjax = function() {
    var handleRecords = function() {
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagination": true,
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_pusat_data_epom_dosen/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2],
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

  function detail_epom(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
      url: "<?php echo site_url('C_pusat_data_epom_dosen/ajax_edit/') ?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id_ptk"]').val(data[0].id_ptk);
        $('[name="nm_ptk"]').val(data[0].nm_ptk);
        $('[name="nidn"]').val(data[0].nidn);
        $('[name="nm_ikatan_kerja"]').val(data[0].nm_ikatan_kerja);
        $('#epom_tabel').html(data[1]);
        $('#epom_tabel_avg').html(data[2]);
        /* 
        $('[name="deskripsi_mata_kuliah"]').summernote("code", data.deskripsi_mata_kuliah);
        */

        $('#modal_form').modal('show');
        $('.modal-title').text("Detail EPOM");

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }
</script>

<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
        echo form_open('#', $attributes);
        ?>
        <input type="hidden" value="" name="id_ptk" />
        <div class="form-body">


          <div class="form-group">
            <label class="control-label col-md-4">Nama Dosen</label>
            <div class="col-md-8">
              <input name="nm_ptk" placeholder="" class="form-control" type="text" required>
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4">NIDN</label>
            <div class="col-md-8">
              <input name="nidn" placeholder="" class="form-control" type="text" required>
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4">Ikatan Kerja</label>
            <div class="col-md-8">
              <input name="nm_ikatan_kerja" placeholder="" class="form-control" type="text" required>
              <span class="help-block"></span>
            </div>
          </div>
          <div id="epom_tabel"></div>
          <div id="epom_tabel_avg"></div>
          
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
      <?php
      echo form_close();
      ?>
    </div>
  </div>
</div>