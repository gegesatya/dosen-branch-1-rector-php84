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
        "searching": false,
        "paging": false,
        "info": false,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_organisasi_kelas/ajax_list/' . $id_jadwal) ?>",
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

    $("input").change(function() {
      $(this).parent().parent().removeClass('has-danger');
      $(this).next().empty();
    });
    $("textarea").change(function() {
      $(this).parent().parent().removeClass('has-danger');
      $(this).next().empty();
    });
    $("select").change(function() {
      $(this).parent().parent().removeClass('has-danger');
      $(this).next().empty();
    });

    load_keterangan();

    $('[name="nipd"]').on('input', function(e) {
      var nim = $(this).val();
      $.ajax({
        url: "<?php echo site_url('C_organisasi_kelas/get_mahasiswa/' . $id_jadwal) ?>",
        data: {
          nim: nim,
          '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        type: 'post',
        dataType: "JSON",
      }).done(function(responseData) {
        if (responseData) {
          $('[name="nm_pd"]').val(responseData.nm_pd);
        } else {
          $('[name="nm_pd"]').val('');
        }
      }).fail(function() {
        $('[name="nm_pd"]').val('');
      });
    });

    $('#form').submit(function(e) {
      e.preventDefault();
      $('#btnSave').text('saving...');
      $('#btnSave').attr('disabled', true);
      var url;



      if (save_method == 'add') {
        url = "<?php echo site_url('C_organisasi_kelas/ajax_add') ?>";
        method = 'disimpan';
      } else {
        url = "<?php echo site_url('C_organisasi_kelas/ajax_update') ?>";
        method = 'diupdate';
      }


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

            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-danger');
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
            }

            // $.each(data.errors, function(key, value) {
            //   if (value !== '') {

            //     $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-danger');
            //     $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);


            //   }
            // });
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


  function load_keterangan() {
    $.ajax({
      url: "<?= base_url('C_organisasi_kelas/get_keterangan') ?>",
      method: 'GET',
      dataType: 'JSON',
      success: function(d) {
        console.log(d);
        var opsi_keterangan;
        $('[name="keterangan"]').html('');
        opsi_keterangan = `<option value="">Silahkan Pilih</option>`;
        $('[name="keterangan"]').append(opsi_keterangan);
        $.each(d, (key, val) => {
          opsi_keterangan = `<option value="${val.keterangan}">${val.keterangan}</option>`;
          $('[name="keterangan"]').append(opsi_keterangan);
        });
      }
    });
  }

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function add_organisasi() {
    save_method = 'add';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-danger');
    $('.form-control-feedback').empty();
    $('#modal_form').modal('show');
    $('.modal-title').text('ADD Organisasi Kelas');
  }

  function edit_organisasi(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-danger');
    $('.form-control-feedback').empty();

    $.ajax({
      url: "<?php echo site_url('C_organisasi_kelas/ajax_edit') ?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {

        $('[name="id_organisasi"]').val(data.id_organisasi);
        $('[name="id_jadwal"]').val(data.id_jadwal);
        $('[name="nipd"]').val(data.nipd);
        $('[name="nm_pd"]').val(data.nm_pd);
        $('[name="keterangan"]').val(data.keterangan);

        $('#modal_form').modal({
          backdrop: 'static',
          keyboard: false
        });

        $('.modal-title').text("EDIT Organisasi Kelas");
        $('.modal-sub').text('[' + data.nm_kls + '] ' + data.nm_mk);

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function delete_organisasi(id) {
    Swal({
      title: 'Anda yakin?',
      text: "Data Organisasi Kelas akan dihapus!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus data!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('C_organisasi_kelas/ajax_delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
          success: function(data) {
            reload_table();
            swalert('dihapus');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error deleting data');
          }
        });
      }
    });
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
        <h4 class="modal-title" style="color:black">Modal Content is Responsive</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body form">

        <?php
        $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
        echo form_open('#', $attributes);
        ?>
        <input type="hidden" value="" name="id_organisasi" />
        <input type="hidden" value="<?= $id_jadwal; ?>" name="id_jadwal" />
        <div class="form-group row">
          <label for="pertemuan" class="col-sm-4 col-form-label">NIM</label>
          <div class="col-sm-8 col-md-8">
            <input type="text" class="form-control" id="nipd" name="nipd">
            <div class="form-control-feedback"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="tanggal_rencana" class="col-sm-4 col-form-label">Nama</label>
          <div class="col-sm-8 col-md-8">
            <input type="text" class="form-control" id="nm_pd" name="nm_pd" readonly>
            <div class="form-control-feedback"></div>
          </div>
        </div>
        <div class="form-group row">
          <label for="tanggal_rencana" class="col-sm-4 col-form-label">Keterangan</label>
          <div class="col-sm-8 col-md-8">
            <select name="keterangan" class="form-control">
              <option value="">-- Pilh --</option>
            </select>
            <div class="form-control-feedback"></div>
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