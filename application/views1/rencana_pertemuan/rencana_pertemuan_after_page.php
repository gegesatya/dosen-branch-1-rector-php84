<script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/datatables/datatables.min.js"></script>

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
          "url": "<?php echo site_url('C_rencana_pertemuan/ajax_list/' . $id_jadwal) ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2, 3, -1],
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
    TableAjax.init();
    // $('#kontrol').hide();


    $(".tgl_realisasi").hide();

    $('#tanggal_realisasi').bootstrapMaterialDatePicker({
      weekStart: 0,
      time: false
    });

    $('#tanggal_rencana').bootstrapMaterialDatePicker({
      weekStart: 0,
      time: false
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

    $('.summernote').summernote({
      placeholder: '',
      tabsize: 2,
      height: 150,
      followingToolbar: false,
      toolbar: [
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
      ]
    });

    $('#form').submit(function(e) {
      e.preventDefault();
      $('#btnSave').text('saving...');
      $('#btnSave').attr('disabled', true);
      var url;

      url = "<?php echo site_url('c_rencana_pertemuan/ajax_update') ?>";
      // nm_pd = $('[name="nm_pd"]').val();
      method = 'Data Berhasil diupdate';

      $.ajax({
        url: url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data) {

          if (data.status) {
            $('#modal_form').modal('hide');
            swalert(method);
            reload_table();
          } else {
            $.each(data.errors, function(key, value) {
              if (value !== '') {
                // $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                // $('[name="' + key + '"]').next().text(value);

                $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
              }
            });
          }
          $('#btnSave').text('save');
          $('#btnSave').attr('disabled', false);


        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error adding / update data');
          $('#btnSave').text('save');
          $('#btnSave').attr('disabled', false);

        }
      });
    });

  });


  $(function() {
    enable_cb();
    $("#is_realisasi").click(enable_cb);
  });

  function enable_cb() {
    if (this.checked) {
      $(".tgl_realisasi").show();
      $('[name="tanggal_realisasi"]').val($('[name="tanggal_rencana"]').val());
    } else {
      $(".tgl_realisasi").hide();
    }
  }

  function format_tanggal(tgl) {
    if (Date.parse(tgl)) {
      var date = new Date(tgl);
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
    } else {
      return '';
    }
  }

  function isValidTime(str) {
    // Regex to check valid
    // time in 24-hour format
    let regex = new RegExp(/^([01]\d|2[0-3]):?([0-5]\d)$/);

    //  if str 
    // is empty return false
    if (str == null) {
      return "false";
    }

    // Return true if the str
    // matched the ReGex
    if (regex.test(str) == true) {
      return "true";
    } else {
      return "false";
    }
  }

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function edit_rencana(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
      url: "<?php echo site_url('c_rencana_pertemuan/ajax_edit') ?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {

        $('[name="uniq"]').val(data.uniq);
        $('[name="tanggal_rencana"]').val(data.tanggal_rencana);
        $('[name="jam_mulai"]').val(data.jam_mulai);
        $('[name="jam_selesai"]').val(data.jam_selesai);
        // $('[name="berita_acara_rencana"]').val(data.berita_acara_rencana);
        $('[name="berita_acara_rencana"]').summernote("code", data.berita_acara_rencana);
        $('[name="kategori_pertemuan"]').val(data.kategori_pertemuan);
        $('[name="pertemuan"]').val(data.pertemuan);
        $('[name="jam_teori"]').val(data.jam_teori);
        $('[name="jam_praktek"]').val(data.jam_praktek);
        $('[name="is_realisasi"]').val(data.is_realisasi);
        $('[name="tanggal_realisasi"]').val(data.tanggal_realisasi);


        if (data.is_realisasi == 1) {
          $('#is_realisasi').prop("checked", true);
          $(".tgl_realisasi").show();
        } else {
          $('#is_realisasi').prop("checked", false);
          $(".tgl_realisasi").hide();
        }

        $('[name="div_pertemuan"]').show();
        $(".tanya_realisasi").show();

        $('#modal_form').modal({
          backdrop: 'static',
          keyboard: false
        });

        $('.modal-title').text("EDIT Rencana/Realisasi Perkuliahan");
        $('.modal-sub').text('[' + data.nm_kls + '] ' + data.nm_mk);

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function format_tanggal(tgl) {
    if (Date.parse(tgl)) {
      var date = new Date(tgl);
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
    } else {
      return '';
    }
  }


  function swalert(method) {
    Swal({
      title: 'Success',
      html: method,
      type: 'success'
    });
  };
</script>

<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
  <div class="modal-dialog" style="max-width: 1000px !important;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Modal Content is Responsive</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body form">

        <?php
        $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
        echo form_open('#', $attributes);
        ?>
        <input type="hidden" value="" name="uniq" />
        <div class="form-group row">
          <label for="pertemuan" class="col-sm-4 col-form-label">Pertemuan Ke</label>
          <div class="col-sm-8 col-md-8">
            <input type="text" class="form-control" id="pertemuan" name="pertemuan" readonly>
            <div class="form-control-feedback"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="tanggal_rencana" class="col-sm-4 col-form-label">Tanggal Rencana</label>
          <div class="col-sm-8 col-md-8">
            <input type="text" class="form-control" id="tanggal_rencana" name="tanggal_rencana">
            <div class="form-control-feedback"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="jam_mulai" class="col-sm-4 col-form-label">Jam</label>
          <div class="col-sm-4 col-md-4">
            <input type="text" class="form-control" id="jam_mulai" name="jam_mulai">
            <div class="form-control-feedback"></div>
          </div>
          <div class="col-sm-4 col-md-4">
            <input type="text" class="form-control" id="jam_selesai" name="jam_selesai">
            <div class="form-control-feedback"></div>
          </div>
        </div>

        <div class="form-group row">
          <label for="jam_teori" class="col-sm-4 col-form-label">Alokasi Waktu (Teori | Praktek)</label>
          <div class="col-sm-4 col-md-4">
            <input type="text" class="form-control" id="jam_teori" name="jam_teori">
            <div class="form-control-feedback"></div>
          </div>
          <div class="col-sm-4 col-md-4">
            <input type="text" class="form-control" id="jam_praktek" name="jam_praktek">
            <div class="form-control-feedback"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="berita_acara_rencana" class="col-sm-4 col-form-label">Bahan Kajian</label>
          <div class="col-md-8">
            <textarea name="berita_acara_rencana" rows="3" placeholder="" class="form-control summernote"></textarea>
            <span class="help-block"></span>
          </div>
        </div>
        <div class="form-group row tanya_realisasi">
          <label class="col-sm-4 col-form-label">Realisasi</label>
          <div class="col-md-8">
            <div class="checkbox">
              <label><input type="checkbox" name='is_realisasi' id='is_realisasi'> Sudah realisasi</label>
            </div>
          </div>

        </div>
        <div class="form-group row tgl_realisasi">
          <label class="col-sm-4 col-form-label">Tgl Realisasi</label>
          <div class="col-md-8">
            <input type="text" class="form-control" name="tanggal_realisasi" id="tanggal_realisasi">
            <span class="help-block"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
        <?php echo form_submit('btnSave', 'Save', 'class="btn btn-primary"'); ?>
      </div>
      <?php
      echo form_close();
      ?>
    </div>
  </div>
</div>