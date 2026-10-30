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
          "url": "<?php echo site_url('C_skema_nilai/ajax_list') ?>",
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

  $(function() {
    $(".form-control").change(function() {
      /* var total = Number($('[name="sikap"]').val()) + 
                  Number($('[name="quiz"]').val()) + 
                  Number($('[name="tugas_terstruktur"]').val()) +
                  Number($('[name="uts"]').val()) + 
                  Number($('[name="uas"]').val()) +
                  Number($('[name="unjuk_kinerja"]').val()) +
                  Number($('[name="proyek"]').val()) + 
                  Number($('[name="portofolio"]').val()) + 
                  Number($('[name="produk"]').val()) +
                  Number($('[name="presentasi"]').val());
      $('#jumlah_total').text(total);  */
      $(this).parent().parent().removeClass('has-error');
      if ($(this).next().hasClass("help-block")) {
        $(this).next().empty();
      } else {
        $(this).parent().next().empty();
      }
    });

    $(".komponen-reguler").on('input', function(e) {
      var total = Number($('[name="sikap"]').val()) +
        Number($('[name="quiz"]').val()) +
        Number($('[name="tugas_terstruktur"]').val()) +
        Number($('[name="uts"]').val()) +
        Number($('[name="uas"]').val()) +
        Number($('[name="unjuk_kinerja"]').val()) +
        Number($('[name="proyek"]').val()) +
        Number($('[name="portofolio"]').val()) +
        Number($('[name="produk"]').val()) +
        Number($('[name="presentasi"]').val());
      $('#jumlah_total').text(total);
    });
  });





  function edit_skema_nilai(id) {
    save_method = 'update_skema_nilai';
    $('#form_skema_nilai')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#btnSave').text('Simpan');
    $('#btnSave').attr('disabled', false);

    $.ajax({
      url: "<?php echo site_url('C_skema_nilai/ajax_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id"]').val(data.id);
        $('[name="tag_name"]').val(data.tag_name);
        $('[name="sikap"]').val(data.sikap);
        $('[name="quiz"]').val(data.quiz);
        $('[name="tugas_terstruktur"]').val(data.tugas_terstruktur);
        $('[name="uts"]').val(data.uts);
        $('[name="uas"]').val(data.uas);
        $('[name="unjuk_kinerja"]').val(data.unjuk_kinerja);
        $('[name="proyek"]').val(data.proyek);
        $('[name="portofolio"]').val(data.portofolio);
        $('[name="produk"]').val(data.produk);
        $('[name="presentasi"]').val(data.presentasi);
        $('#jumlah_total').text(data.total);


        $('#modal_form_skema_nilai').modal('show');
        $('.modal-title').text("Edit Skema Nilai " + data.tag_name);
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

    url = "<?php echo site_url('C_skema_nilai/ajax_update_skema_nilai') ?>";
    data = $('#form_skema_nilai').serializeArray()

    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_form_skema_nilai').modal('hide');
          reload_table();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            /* if(save_method == 'update_skema_nilai'){
                $('[name="jumlah_total"]').addClass('has-error');
                $('[name="jumlah_total"]').text(data.error_string[i]);

                $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                if($('[name="'+data.inputerror[i]+'"]').next().hasClass('help-block')){
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }else{
                    $('[name="'+data.inputerror[i]+'"]').parent().next().text(data.error_string[i]);
                }
            }else{
                $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                if($('[name="'+data.inputerror[i]+'"]').next().hasClass('help-block')){
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }else{
                    $('[name="'+data.inputerror[i]+'"]').parent().next().text(data.error_string[i]);
                }
            } */

            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
              $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
            }
          }

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

<div class="modal fade" id="modal_form_skema_nilai" role="dialog">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form_skema_nilai'];
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
            <label class="control-label col-md-3">Sikap (%)</label>
            <div class="col-md-4">
              <input name="sikap" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          
          <div class="form-group">
            <label class="control-label col-md-3">Quiz (%)</label>
            <div class="col-md-3">
              <input name="quiz" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
            <label class="control-label col-md-3">Tugas (%)</label>
            <div class="col-md-3">
              <input name="tugas_terstruktur" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">UTS (%)</label>
            <div class="col-md-3">
              <input name="uts" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
            <label class="control-label col-md-3">UAS (%)</label>
            <div class="col-md-3">
              <input name="uas" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          
          <div class="form-group">
            <label class="control-label col-md-3">Unjuk Kinerja (%)</label>
            <div class="col-md-3">
              <input name="unjuk_kinerja" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
            <label class="control-label col-md-3">Proyek (%)</label>
            <div class="col-md-3">
              <input name="proyek" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Portofolio (%)</label>
            <div class="col-md-3">
              <input name="portofolio" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
            <label class="control-label col-md-3">Produk (%)</label>
            <div class="col-md-3">
              <input name="produk" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3">Presentasi (%)</label>
            <div class="col-md-3">
              <input name="presentasi" class="form-control input-sm komponen-reguler" type="number">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3"></label>
            <div class="col-md-9">
              <div class="alert alert-info text-center" id='jumlah_total' name='jumlah_total'></div>
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