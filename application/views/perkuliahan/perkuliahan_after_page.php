<script type="text/javascript">
var table;

var TableAjax = function () {
    var handleRecords = function () {
    table = $('#datatable_ajax').DataTable({ 
        "processing": true,
        "serverSide": true,
        "bPaginate":true,
        
        "pagingType": "full_numbers",
        "order": [],
        "ajax": {
            "url": "<?php echo site_url('v4/c_rencana_pertemuan/ajax_list')?>",
            "type": "POST",
            "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
        },

        "columnDefs": [
            { 
                "targets": [ -1 ],
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

$(function() {
    get_ajax_data("<?=$this->session->userdata('id_kls');?>");

    $('input[type=radio][name=kategori_p]').change(function() {
        if (this.value == 't') {
            $('[name="bobot_sikap"]').val(20);
            $('[name="bobot_pengetahuan"]').val(50);
            $('[name="bobot_keterampilan"]').val(30);
        }
        else if (this.value == 'tp') {
            $('[name="bobot_sikap"]').val(20);
            $('[name="bobot_pengetahuan"]').val(40);
            $('[name="bobot_keterampilan"]').val(40);
        }
        else if (this.value == 'p') {
            $('[name="bobot_sikap"]').val(20);
            $('[name="bobot_pengetahuan"]').val(30);
            $('[name="bobot_keterampilan"]').val(50);
        }
    });

    $("#form-skema-nilai").submit(function(e) { 
        e.preventDefault(); 
        var dataku = $('#form-skema-nilai').serializeArray();
        dataku.push({name: '<?php echo $this->security->get_csrf_token_name(); ?>', value: '<?php echo $this->security->get_csrf_hash(); ?>'});
        $.ajax({
            url: "<?=site_url('C_perkuliahan/ajax_update_skema_nilai')?>",
            type: "POST",
            dataType: "json",
            data: dataku,
            success:function(data){
                
                if(data.status){
                    alert('Sukses. Data berhasil diubah.');
                }else{
                    $('#modal_skema_error').modal('show');
                    $('#titleku').text('Kesalahan Input Data');
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="'+data.inputerror[i]+'"]').text(data.error_string[i]);
                        $('[name="'+data.inputerror[i]+'"]').addClass('has-error');
                    }
                }
            }
        });
    });

    
});

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function get_ajax_data(id_kls){
    
    $.ajax({
        url : "<?php echo site_url('C_perkuliahan/ajax_skema_edit')?>" +"/"+id_kls,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_kls"]').val(id_kls);
            $('[name="bobot_sikap"]').val(data.bobot_sikap);
            $('[name="bobot_pengetahuan"]').val(data.bobot_pengetahuan);
            $('[name="bobot_pengetahuan_quiz"]').val(data.bobot_pengetahuan_quiz);
            $('[name="bobot_pengetahuan_tugas_terstruktur"]').val(data.bobot_pengetahuan_tugas_terstruktur);
            $('[name="bobot_pengetahuan_uts"]').val(data.bobot_pengetahuan_uts);
            $('[name="bobot_pengetahuan_uas"]').val(data.bobot_pengetahuan_uas);
            $('[name="bobot_keterampilan"]').val(data.bobot_keterampilan);
            $('[name="bobot_keterampilan_unjuk_kinerja"]').val(data.bobot_keterampilan_unjuk_kinerja);
            $('[name="bobot_keterampilan_proyek"]').val(data.bobot_keterampilan_proyek);
            $('[name="bobot_keterampilan_portofolio"]').val(data.bobot_keterampilan_portofolio);
            $('[name="bobot_keterampilan_produk"]').val(data.bobot_keterampilan_produk);
            $('[name="bobot_keterampilan_presentasi"]').val(data.bobot_keterampilan_presentasi);
            //$('#teori').prop("checked", true);
            
            if(data.bobot_sikap == 20 && data.bobot_pengetahuan == 50 && data.bobot_keterampilan == 30){
                //$('#teori').prop("checked", true);
                $("#teori").parent().addClass('checked');
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 40 && data.bobot_keterampilan == 40){
                $('#teoripraktek').parent().addClass('checked');
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 30 && data.bobot_keterampilan == 50){
                $('#praktek').parent().addClass('checked');
            }

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
    
}

function edit_skema_nilai(id_kls)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');

    $.ajax({
        url : "<?php echo site_url('c_jadwal_perkuliahan/ajax_skema_edit')?>" +"/"+id_kls,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id_kls"]').val(id_kls);
            $('[name="bobot_sikap"]').val(data.bobot_sikap);
            $('[name="bobot_pengetahuan"]').val(data.bobot_pengetahuan);
            $('[name="bobot_pengetahuan_quiz"]').val(data.bobot_pengetahuan_quiz);
            $('[name="bobot_pengetahuan_tugas_terstruktur"]').val(data.bobot_pengetahuan_tugas_terstruktur);
            $('[name="bobot_pengetahuan_uts"]').val(data.bobot_pengetahuan_uts);
            $('[name="bobot_pengetahuan_uas"]').val(data.bobot_pengetahuan_uas);
            $('[name="bobot_keterampilan"]').val(data.bobot_keterampilan);
            $('[name="bobot_keterampilan_unjuk_kinerja"]').val(data.bobot_keterampilan_unjuk_kinerja);
            $('[name="bobot_keterampilan_proyek"]').val(data.bobot_keterampilan_proyek);
            $('[name="bobot_keterampilan_portofolio"]').val(data.bobot_keterampilan_portofolio);
            $('[name="bobot_keterampilan_produk"]').val(data.bobot_keterampilan_produk);
            $('[name="bobot_keterampilan_presentasi"]').val(data.bobot_keterampilan_presentasi);
           

            if(data.bobot_sikap == 20 && data.bobot_pengetahuan == 50 && data.bobot_keterampilan == 30){
                $('#teori').prop("checked", true);
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 40 && data.bobot_keterampilan == 40){
                $('#teoripraktek').prop("checked", true);
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 30 && data.bobot_keterampilan == 50){
                $('#praktek').prop("checked", true);
            }

            $('#modal_form').modal({backdrop: 'static', keyboard: false});
            $('#modal_form').draggable({handle: ".modal-header"});
            $('.modal-title').text("SKEMA NILAI");
            $('.modal-sub').text('['+data.nm_kls+'] '+data.nm_mk);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
</script>

<div class="modal fade" id="modal_skema_error" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="titleku"></h4>
            </div>
            <div class="modal-body">
                  <p name="induk_error"></p>
                  <p name="quiz_error"></p>
                  <p name="tugas_error"></p>
                  <p name="uts_error"></p>
                  <p name="uas_error"></p>

                  <p name="ukn_error"></p>
                  <p name="pry_error"></p>
                  <p name="por_error"></p>
                  <p name="prd_error"></p>
                  <p name="pre_error"></p>


                  <p name="sub_keterampilan_error"></p>
                  <p name="sub_pengetahuan_error"></p> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>    
    </div>
</div> 

<div class="modal fade" id="modal_skema_success" role="dialog" >
    <div class="modal-dialog" style="width:400ox !important">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 id="titleku"></h4>
            </div>
            <div class="modal-body">
                  <p name="induk_error"></p>
                  <p name="sub_keterampilan_error"></p>
                  <p name="sub_pengetahuan_error"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>    
    </div>
</div>