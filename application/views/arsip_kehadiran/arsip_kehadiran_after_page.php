<script type="text/javascript">
  var save_method;
  var table;
  var arr_tahun = [];
  var TableAjax = function () {
    var handleRecords = function () {
        table = $('#datatable_ajax').DataTable({ 
                "processing": true,
                "serverSide": true,
                "pagination": true,
                "destroy":true,
                "order": [],
                "ajax": {
                  "url": "<?php echo site_url('C_arsip_kehadiran/ajax_list')?>",
                  "type": "POST",
                  data:  function(dtRequest) {
                    dtRequest[ '<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>' ;
                    return dtRequest;
                  },
                },

                "columnDefs": [
                    { 
                      "targets": [0,1,2,3,4,-1 ],
                      "orderable": false,
                    },
                ],

            });
        }

        return {
        init: function () {
          handleRecords();
        }
      };
  }();

    
    function reload_table()
    {
        table.ajax.reload(null,false);
    }
 

function edit_rencana(id)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
        url : "<?php echo site_url('c_berita_acara/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="uniq"]').val(data.uniq);
            $('[name="tanggal_berita_acara"]').val(data.tanggal_berita_acara);
            $('[name="minggu_ke"]').val(data.minggu_ke);
            $('[name="jam_mulai"]').val(data.jam_mulai);
            $('[name="jam_selesai"]').val(data.jam_selesai);
            $('[name="isi_berita_acara"]').val(data.isi_berita_acara);
            $('[name="bacaan_materi"]').val(data.bacaan_materi);
            $('[name="jumlah_hadir"]').val(data.jumlah_hadir);
            $('[name="jumlah_tidak_hadir"]').val(data.jumlah_tidak_hadir);
            $('[name="jam_teori"]').val(data.jam_teori);
            $('[name="jam_praktek"]').val(data.jam_praktek);
            $('[name="div_pertemuan"]').show();

            $('#modal_form').modal({backdrop: 'static', keyboard: false});
            $('#modal_form').draggable({handle: ".modal-header"});
            $('.modal-title').text("EDIT Berita Acara Perkuliahan");
            $('.modal-sub').text('['+data.nm_kls+'] '+data.nm_mk);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

</script>
