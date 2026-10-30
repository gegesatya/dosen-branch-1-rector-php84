<script type="text/javascript">
  var save_method;
  var table;
  var arr_tahun = [];
  var TableAjax = function() {
    var handleRecords = function() {
      var id_smt = $('#ddAngkatan :selected').val();
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagingType": "full_numbers",
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_kaprodi_data_nilai/ajax_list') ?>" + "/" + id_smt,
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [0, 1, 2, 3, 4],
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
    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
      orientation: "top auto",
      todayBtn: true,
      todayHighlight: true,
    });

    $('select[name=ddAngkatan]').on('change', function() {
      TableAjax.init();
    });


  });

  function reload_table() {
    table = $('#table').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        "url": urlz,
        "type": "POST",
      },
      "destroy": true,
      "columnDefs": [{
        "targets": [0, 1, 2, 3, 4],
        "orderable": false,
      }, ],

    });
  }
</script>