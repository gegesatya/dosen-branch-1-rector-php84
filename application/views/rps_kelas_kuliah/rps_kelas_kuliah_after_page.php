<script type="text/javascript">
  var save_method;
  var table;
  var arr_tahun = [];
  var TableAjax = function() {
    var handleRecords = function() {
      var id_kls = $('#ddRps :selected').val();
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagination": true,
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_rps_kelas_kuliah/ajax_list') ?>" + "/" + id_kls,
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4, 5],
          "orderable": false,
        }],

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


    $('#form_mass_update').submit(function(e) {
      e.preventDefault();
      $('#btnSave').text('Saving...');
      $('#btnSave').attr('disabled', true);
      $.ajax({
        url: '<?php echo site_url('C_rps_kelas_kuliah/ajax_update/') ?>',
        type: "post",
        dataType: "JSON",
        data: $('#form_mass_update').serialize(),
        success: function(data) {
          $('#btnSave').text('Submit');
          $('#btnSave').attr('disabled', false);

          if (data.status) {
            $('#modal_form_mass_update').modal('hide');
            reload_table();
            $my_var = $("select[name=ddRps] option:selected").val();
            dropdown_change($my_var);

          } else {
            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').text(data.error_string[i]);
              $('[name="' + data.inputerror[i] + '"]').addClass('has-error');
            }
          }
        }
      });
    });

    $('select[name=ddRps]').on('change', function() {
      $my_var = $("select[name=ddRps] option:selected").text();
      if ($(this).val() == '') {
        $('#kontrol').hide();
        $('#sub-caption').text('');
      } else {
        TableAjax.init();

        $.ajax({
          url: "<?php echo site_url('C_rps_kelas_kuliah/save_id_kls_session/') ?>/" + $(this).val(),
          type: "GET",
          dataType: "JSON",
          success: function(data) {
            $('#kontrol').show();
            $('#sub-caption').text($my_var);
            $('#jumlah_kelas_kuliah').html('Jumlah Kelas Kuliah: ' + data.jumlah_kelas_kuliah);
            $('#jumlah_sudah_rps').html('Jumlah Sudah Ada RPS: ' + data.jumlah_sudah_rps);
            //alert(data.length);
            var html = '';
            var i;
            $('#pertemuan').html('');
            /* for(i=0; i<data.length; i++){
                html += '<option value='+data[i]+'>'+data[i]+'</option>';
                
            } */
            $.each(data, function(index, value) {
              console.log(value);
              // Will stop running after "three"
              //return (value !== 'three');
              html += '<option value=' + index + '>' + value + '</option>';
            });
            $('#pertemuan').html(html);

          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          }
        });
      }
    });
  });

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function mass_edit() {
    save_method = 'update';
    $('#form_mass_update')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#modal_form_mass_update').modal({
      backdrop: 'static',
      keyboard: false
    });
    $('#modal_form_mass_update').draggable({
      handle: ".modal-header"
    });
    $('.modal-title').text("Ubah data presensi keseluruhan");
    $('.modal-sub').text('[' + data.nm_kls + '] ' + data.nm_mk);

  }

  function sinkronisasi() {
    var dataku = $('#form').serializeArray();
    dataku.push({
      name: '<?php echo $this->security->get_csrf_token_name(); ?>',
      value: '<?php echo $this->security->get_csrf_hash(); ?>'
    });

    $.ajax({
      url: "<?php echo site_url('C_rps_kelas_kuliah/sinkronisasi/') ?>",
      type: "POST",
      data: dataku,
      dataType: "JSON",
      success: function(data) {
        reload_table();
        $my_var = $("select[name=ddRps] option:selected").val();
        dropdown_change($my_var);
      },
      error: function(jqXHR, textStatus, errorThrown) {

      }
    });
  }

  function edit_mk_prodi(tipe, id_rps, id_mk) {
    var obj = {};
    obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

    $.post("<?= site_url('C_rps_kelas_kuliah/edit_mk_prodi'); ?>" + '/' + tipe + '/' + id_rps + '/' + id_mk, obj, function(data) {
      reload_table();
    });
  }
</script>