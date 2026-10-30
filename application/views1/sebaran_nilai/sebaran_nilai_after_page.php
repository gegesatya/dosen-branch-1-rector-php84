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
          "url": "<?php echo site_url('C_kaprodi_sebaran_nilai/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
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
</script>