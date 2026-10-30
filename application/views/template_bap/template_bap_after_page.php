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
                  "url": "<?php echo site_url('C_template_bap/ajax_list')?>",
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

  function add_template()
{
    save_method = 'add';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
        url : "<?php echo site_url('C_template_bap/get_kelas_kuliah/')?>/",
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="jam_mulai"]').val(data.jam_mulai);
            $('[name="jam_selesai"]').val(data.jam_selesai);
            $('[name="div_pertemuan"]').hide();
            $('#modal_form').modal({backdrop: 'static', keyboard: false});
            $('#modal_form').draggable({handle: ".modal-header"});
            $('.modal-title').text('TAMBAH Template BAP');
            //$('.modal-sub').text('['+data.nm_kls+'] '+data.nm_mk);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function edit_rencana(id)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
        url : "<?php echo site_url('C_template_bap/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="uniq"]').val(data.uniq);
            $('[name="tanggal_berita_acara"]').val(data.tanggal_berita_acara);
            $('[name="minggu_ke"]').val(data.minggu_ke);
            //$('[name="tanggal_rencana"]').val(format_tanggal(data.tanggal_rencana));
            $('[name="jam_mulai"]').val(data.jam_mulai);
            $('[name="jam_selesai"]').val(data.jam_selesai);
            $('[name="isi_berita_acara"]').val(data.isi_berita_acara);
            $('[name="bacaan_materi"]').val(data.bacaan_materi);
            $('[name="jumlah_hadir"]').val(data.jumlah_hadir);
            $('[name="jumlah_tidak_hadir"]').val(data.jumlah_tidak_hadir);
            //$('[name="kategori_pertemuan"]').val(data.kategori_pertemuan);
            //$('[name="pertemuan"]').val(data.pertemuan);
            $('[name="jam_teori"]').val(data.jam_teori);
            $('[name="jam_praktek"]').val(data.jam_praktek);
            //$('[name="kemampuan_akhir"]').val(data.kemampuan_akhir);
            //$('[name="indikator_capaian"]').val(data.indikator_capaian);
            //$('[name="metode_pembelajaran"]').val(data.metode_pembelajaran);
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

function save()
{
    $('#btnSave').text('saving...');
    $('#btnSave').attr('disabled',true);
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('C_template_bap/ajax_add')?>";
    } else {
        url = "<?php echo site_url('C_template_bap/ajax_update')?>";
    }

    var dataku = $('#form').serializeArray();
    dataku.push({name: '<?php echo $this->security->get_csrf_token_name(); ?>', value: '<?php echo $this->security->get_csrf_hash(); ?>'});
    
    $.ajax({
        url : url,
        type: "POST",
        data: dataku,
        dataType: "JSON",
        success: function(data)
        {
            if(data.status) 
            {
                $('#modal_form').modal('hide');
                reload_table();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); 
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }
            }
            $('#btnSave').text('save');
            $('#btnSave').attr('disabled',false);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('save');
            $('#btnSave').attr('disabled',false);

        }
    });
}

function delete_rencana(id)
{
    if(confirm('PERINGATAN..!! Menghapus Data Berita Acara Perkuliahan, Akan Menghapus Data Pada Tabel Absen Yang Memiliki Relasi Data. Apakah Ingin Melanjutkan?'))
    {
        var obj = { };
        obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url : "<?php echo site_url('C_template_bap/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            data:obj,
            success: function(data)
            {
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });
    }
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
        
        <?php
          $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
          echo form_open('#', $attributes);
        ?>
          <input type="hidden" value="" name="uniq"/>
          <div class="form-body">
           

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