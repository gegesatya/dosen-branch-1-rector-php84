<script type="text/javascript">
  var save_method;
  var table;

  var TableAjax = function() {
    var handleRecords = function() {
      var id_agama = $('[name="id_agama"] :selected').val();
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagingType": "full_numbers",
        'destroy': true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_kaprodi_data_krs_mahasiswa_baru/ajax_list') ?>" + "/" + id_agama,
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4, 5],
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
    $('select[name="id_agama"]').on('change', function() {
      TableAjax.init();
    });
  });

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function show_krs(id_rencana_studi, nipd) {
    $.ajax({
      url: "<?php echo site_url('C_kaprodi_data_krs_mahasiswa_baru/view_krs/') ?>/" + id_rencana_studi + "/" + nipd,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('#nm_pd').val(data.nm_pd);
        $('#nipd').val(data.nipd);
        $('#id_smt').val(data.id_smt);
        $('#dosen_wali').val(data.dosen_wali);
        $('#tabel_nilai').html(data.tabel);
        $('#modal_form_krs').modal('show');
        $('.modal-title').text("Kartu Rencana Studi / Card Of Study Plan");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }
</script>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Modal Title</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <form class="form-horizontal" role="form">
            <div class="form-body">
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-md-3">Nama:</label>
                  <div class="col-md-9">
                    <input class="form-control" id='nm_pd' value='' readonly />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-md-3">NIM:</label>
                  <div class="col-md-9">
                    <input class="form-control" id='nipd' value='' readonly />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-md-3">Dosen Wali:</label>
                  <div class="col-md-9">
                    <input class="form-control" id='dosen_wali' value='' readonly />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-md-3">Tahun Ajaran:</label>
                  <div class="col-md-9">
                    <input class="form-control" id='id_smt' value='' readonly />
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div id="tabel_nilai">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>