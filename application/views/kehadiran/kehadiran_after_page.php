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
          "url": "<?php echo site_url('C_kehadiran/ajax_list/' . $id_jadwal) ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, -1],
          "orderable": false
        }, {
          "width": "15%",
          "targets": 1
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
    TableAjax.init();
    $('#form_submit').submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: '<?php echo site_url('C_kehadiran/save_excel/') ?>',
        type: "post",
        dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        success: function(data) {
          $("#myalert").removeClass();

          if (data.tipe == 0) {
            $("#myalert").addClass("alert alert-success");
            $("#myalert").text("Sukses! " + data.message);

            $("#myalert").fadeTo(3000, 500).slideUp(500, function() {
              $("#myalert").slideUp(500);
            });

          } else {
            $("#myalert").addClass("alert alert-danger");
            $("#myalert").text("Error! " + data.message);
          }

          $('#userfile').val('');
          reload_table();
        }
      });
    });

    $('#kontrol').hide();


    // show_bantuan();

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

  function show_bantuan() {
    $('#modal_form_bantuan').modal('show');
    $('.modal-title').text("Panduan Pengisian");

  }

  function sinkronisasi() {
    var dataku = $('#form').serializeArray();
    dataku.push({
      name: '<?php echo $this->security->get_csrf_token_name(); ?>',
      value: '<?php echo $this->security->get_csrf_hash(); ?>'
    });

    $.ajax({
      url: "<?php echo site_url('C_kehadiran/sinkronisasi/' . $id_jadwal) ?>",
      type: "POST",
      data: dataku,
      dataType: "JSON",
      success: function(data) {
        reload_table();
        $my_var = $("select[name=ddKelasKuliah] option:selected").val();
        dropdown_change($my_var);
      },
      error: function(jqXHR, textStatus, errorThrown) {

      }
    });
  }

  // function edit_absen(status_hadir, pertemuan, uniq) {
  //   var obj = {};
  //   obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

  //   if (status_hadir == 'H') {
  //     $.post("<?= site_url('C_kehadiran/edit_status_kehadiran'); ?>" + '/0/' + pertemuan + '/' + uniq, obj, function(data) {
  //       reload_table();
  //     });
  //   } else {
  //     $.post("<?= site_url('C_kehadiran/edit_status_kehadiran'); ?>" + '/1/' + pertemuan + '/' + uniq, obj, function(data) {
  //       reload_table();
  //     });
  //   }
  // }
</script>

<div class="modal fade" id="modal_form_bantuan" role="dialog">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Modal Content is Responsive</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body form">
        <div class="well margin-20 ">
          <p>Mohon untuk dibaca dengan teliti :</p>
          <ol>
            <li>Download terlebih dulu file excel daftar absen mahasiswa melalui tombol download</li>
            <li>Jika mahasiswa tidak muncul, klik tombol <b>Sinkronisasi</b></li>
            <li>Editlah data Kehadiran pada file excel tersebut, ikuti petunjuk yang ada</li>
            <li>Isi Data Kehadiran dengan huruf <b>H</b> jika hadir dan huruf <b>A</b> jika tidak hadir</li>
            <li>Sistem hanya menerima nilai H dan A</li>
            <li>Jika pertemuan hanya sampai 16 kali, cukup mengisi kehadiran pada menu Presensi 1. Dimulai dari pertemuan 1 sd 16 (termasuk UTS dan UAS)</li>
            <li>Untuk presensi 2, pertemuan dimulai dari pertemuan 17 sampai ke pertemuan yang diinginkan. Maksimal sampai 30 pertemuan</li>
            <li>Jangan lupa untuk menyimpan file excel sebelum di upload, dan jangan mengupload data absen di kelas yang tidak sesuai</li>
            <li>Pilih file yang sudah diedit, kemudian klik Upload Data</li>
          </ol>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>