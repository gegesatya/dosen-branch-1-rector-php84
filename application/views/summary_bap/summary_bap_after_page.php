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
          "url": "<?php echo site_url('C_summary_bap/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2, -1],
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

  $(document).ready(function() {
    $('#kontrol').hide();
    $(".tgl_realisasi").hide();

    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      orientation: "auto",
      todayBtn: true,
      todayHighlight: true,
      forceParse: false
    });

    $("input").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("textarea").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("select").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });

    $('#form_copy').submit(function(e) {
      e.preventDefault();
      $('#btnSave').text('Saving...');
      $('#btnSave').attr('disabled', true);
      $.ajax({
        url: '<?php echo site_url('C_summary_bap/ajax_pertemuan_copy_data/') ?>',
        type: "post",
        dataType: "JSON",
        data: $('#form_copy').serialize(),
        success: function(data) {
          $('#btnSave').text('Copy');
          $('#btnSave').attr('disabled', false);

          if (data.status) {
            $('#modal_form_copy').modal('hide');
            //reload_table();
          } else {
            /* for (var i = 0; i < data.inputerror.length; i++) {
                $('[name="'+data.inputerror[i]+'"]').text(data.error_string[i]);
                $('[name="'+data.inputerror[i]+'"]').addClass('has-error');
              }
 */
            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
            }
          }
        }
      });
    });

  });

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function ajax_copy(id_kls) {

    $('#form_copy')[0].reset();
    $('.form-group').removeClass('has-error');

    $.ajax({
      url: "<?php echo site_url('C_summary_bap/get_copy_data') ?>" + "/" + id_kls,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        //if(data.status == false){
        //  alert(data.message);
        //}else{
        $('[name="id_kls"]').val(data.data_kls.id_kls);
        $('[name="sumber_data"]').val(data.data_kls.nm_kls + ' | ' + data.data_kls.nm_mk);
        $('#bap_table').html(data.bap_table);
        $('#modal_form_copy').modal({
          backdrop: 'static',
          keyboard: false
        });
        $('.modal-title').text("Generate Data dari Rencana Pertemuan");
        //} 

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }
</script>

<div class="modal fade" id="modal_form_copy" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form_copy'];
        echo form_open('#', $attributes);
        ?>
        <div class="form-body">
          <input type="hidden" name="id_kls" />
          <div class="form-group">
            <label class="col-md-4 control-label">Copy to</label>
            <div class="col-md-8">
              <?php
              echo form_dropdown('id_jadwal', $dd_jadwal, '', 'class="form-control input-sm"');
              ?>
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-4 control-label">Tanggal Mulai</label>
            <div class="col-md-8">
              <input type="text" class="form-control datepicker" name="tanggal_mulai">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-4 control-label">Sumber Data</label>
            <div class="col-md-8">
              <input type="text" class='form-control' name='sumber_data' />
            </div>
            <div id="bap_table" class="col-md-11 margin-top-10" style="max-height:450px;overflow-y:scroll">

            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="btnSave" class="btn btn-primary">Copy</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
      <?php
      echo form_close();
      ?>
    </div>
  </div>
</div>