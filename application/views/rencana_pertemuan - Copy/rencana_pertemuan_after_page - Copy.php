<script type="text/javascript">
  var save_method;
  var table;
  var arr_tahun = [];
  var TableAjax = function() {
    var handleRecords = function() {
      var id_kls = $('#ddKelasKuliah :selected').val();
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagination": true,
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_rencana_pertemuan/ajax_list') ?>" + "/" + id_kls,
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2, 3, 4, 5, -1],
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

    $('select[name=ddKelasKuliah]').on('change', function() {
      $my_var = $("select[name=ddKelasKuliah] option:selected").text();
      reload_table();
      if ($(this).val() == '') {
        $('#kontrol').hide();
        $('#sub-caption').text('');
        TableAjax.init();
      } else {
        $.ajax({
          url: "<?php echo site_url('C_rencana_pertemuan/save_id_kls_session/') ?>/" + $(this).val(),
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            $('#kontrol').show();
            $('#sub-caption').text($my_var);
            TableAjax.init();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }

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
        url: '<?php echo site_url('C_rencana_pertemuan/ajax_pertemuan_copy_data/') ?>',
        type: "post",
        dataType: "JSON",
        data: $('#form_copy').serialize(),
        success: function(data) {
          $('#btnSave').text('Submit');
          $('#btnSave').attr('disabled', false);

          if (data.status) {
            $('#modal_form_copy').modal('hide');
            reload_table();
          } else {
            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').text(data.error_string[i]);
              $('[name="' + data.inputerror[i] + '"]').addClass('has-error');
            }
          }
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

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function add_rencana() {
    save_method = 'add';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
      url: "<?php echo site_url('c_rencana_pertemuan/get_kelas_kuliah/') ?>/",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="jam_mulai"]').val(data.jam_mulai);
        $('[name="jam_selesai"]').val(data.jam_selesai);
        $('[name="div_pertemuan"]').hide();
        $(".tanya_realisasi").hide();


        $('#modal_form').modal({
          backdrop: 'static',
          keyboard: false
        });
        $('#modal_form').draggable({
          handle: ".modal-header"
        });
        $('.modal-title').text('TAMBAH Rencana/Realiasi Perkuliahan');
        $('.modal-sub').text('[' + data.nm_kls + '] ' + data.nm_mk);

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
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
        //$('[name="tanggal_rencana"]').val(format_tanggal(data.tanggal_rencana));
        $('[name="jam_mulai"]').val(data.jam_mulai);
        $('[name="jam_selesai"]').val(data.jam_selesai);
        $('[name="berita_acara_rencana"]').val(data.berita_acara_rencana);
        $('[name="bacaan_materi"]').val(data.bacaan_materi);
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
        $('#modal_form').draggable({
          handle: ".modal-header"
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

  function save() {
    $('#btnSave').text('saving...');
    $('#btnSave').attr('disabled', true);
    var url;

    if (save_method == 'add') {
      url = "<?php echo site_url('c_rencana_pertemuan/ajax_add') ?>";
    } else {
      url = "<?php echo site_url('c_rencana_pertemuan/ajax_update') ?>";
    }

    var dataku = $('#form').serializeArray();
    dataku.push({
      name: '<?php echo $this->security->get_csrf_token_name(); ?>',
      value: '<?php echo $this->security->get_csrf_hash(); ?>'
    });

    $.ajax({
      url: url,
      type: "POST",
      data: dataku,
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_form').modal('hide');
          reload_table();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
          }
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
  }

  function delete_rencana(id) {
    if (confirm('PERINGATAN..!! Menghapus Data Rencana Perkuliahan, Akan Menghapus Data Pada Tabel Absen Yang Memiliki Relasi Data. Apakah Ingin Melanjutkan?')) {
      var obj = {};
      obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

      $.ajax({
        url: "<?php echo site_url('C_rencana_pertemuan/ajax_delete') ?>/" + id,
        type: "POST",
        dataType: "JSON",
        data: obj,
        success: function(data) {
          if (data.status) {
            reload_table();
          } else {
            alert(data.message);
          }

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error deleting data');
        }
      });
    }
  }

  function hapus_semua() {
    if (confirm('PERINGATAN..!! Akan menghapus semua data, kecuali data pertemuan yang sudah realisasi.')) {
      var obj = {};
      obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

      $.ajax({
        url: "<?php echo site_url('C_rencana_pertemuan/ajax_delete_all') ?>",
        type: "POST",
        dataType: "JSON",
        data: obj,
        success: function(data) {
          if (data.status) {
            reload_table();
          } else {
            alert(data.message);
          }

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error deleting data');
        }
      });
    }
  }


  function ajax_copy() {
    $('#form_copy')[0].reset();
    $('.form-group').removeClass('has-error');

    $.ajax({
      url: "<?php echo site_url('C_rencana_pertemuan/get_copy_data') ?>",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.status == false) {
          alert(data.message);
        } else {
          $('[name="id_kls"]').val(data.id_kls);
          $('[name="sumber_data"]').val(data.nm_kls + ' | ' + data.nm_mk);
          $('#modal_form_copy').modal({
            backdrop: 'static',
            keyboard: false
          });

          $('.modal-title').text("Copy Rencana Perkuliahan");
        }

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
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" value="" name="uniq" />
          <div class="form-body">
            <div class="form-group" name="div_pertemuan">
              <label class="control-label col-md-4">Pertemuan Ke</label>
              <div class="col-md-3">
                <input type="text" class="form-control" readonly name="pertemuan">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-4">Tanggal Rencana</label>
              <div class="col-md-3">
                <input type="text" class="form-control datepicker" name="tanggal_rencana">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-4">Jam Mulai/Selesai</label>
              <div class="col-md-2">
                <div class="bootstrap-timepicker">
                  <input name="jam_mulai" placeholder="HH:mm" class="form-control timepicker" type="text">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="bootstrap-timepicker">
                  <input name="jam_selesai" placeholder="HH:mm" class="form-control timepicker" type="text">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Alokasi Waktu T/P (menit)</label>
              <div class="col-md-2">
                <input name="jam_teori" class="form-control" type="number">
                <span class="help-block"></span>
              </div>
              <div class="col-md-2">
                <input name="jam_praktek" class="form-control" type="number">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Bahan Kajian</label>
              <div class="col-md-8">
                <textarea name="berita_acara_rencana" rows="3" placeholder="" class="form-control"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4">Sumber Belajar</label>
              <div class="col-md-8">
                <textarea name="bacaan_materi" rows="3" placeholder="" class="form-control"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group tanya_realisasi">
              <label class="control-label col-md-4">Realisasi</label>
              <div class="col-md-2">
                <div class="checkbox">
                  <label><input type="checkbox" name='is_realisasi' id='is_realisasi'>Sudah realisasi</label>
                </div>
              </div>
              <div class='tgl_realisasi'>
                <label class="control-label col-md-2">Tgl Realisasi</label>
                <div class="col-md-3">
                  <input type="text" class="form-control datepicker" name="tanggal_realisasi">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

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
            <label class="col-md-4 control-label">Sumber Data</label>
            <div class="col-md-8">
              <input type="text" class='form-control' name='sumber_data' />
            </div>
          </div>
          <div class="form-group">
            <label class="col-md-4 control-label">Copy Rencana ke kelas</label>
            <div class="col-md-8">
              <?php
              echo form_dropdown('id_kls_2', $dd_kelas_kuliah, '', 'class="form-control input-sm" id="id_kls_2" required="required"');
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
      <?php
      echo form_close();
      ?>
    </div>
  </div>
</div>