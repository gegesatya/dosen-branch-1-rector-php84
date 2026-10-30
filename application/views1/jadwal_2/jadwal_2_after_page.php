<script type="text/javascript">

var save_method;
var table;

var TableAjax = function () {
    var handleRecords = function () {
        table = $('#datatable_ajax').DataTable({ 
            "processing": true,
            "serverSide": true,
            "pagingType": "full_numbers",
            "order": [],
            "ajax": {
                "url": "<?php echo site_url('C_jadwal_2/ajax_list')?>",
                "type": "POST",
                data:  function(dtRequest) {
                  dtRequest[ '<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>' ;
                  return dtRequest;
                },
            },

            "columnDefs": [
                { 
                    "targets": [ -1,0,1,2,3,4 ],
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

function reload_table_ajax()
{
    table.ajax.reload(null,false);
}

$(function() {
    $('#inputselect').change(function() {
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

    $('#form_copy').submit(function(e){
        e.preventDefault();
        $('#btnSave').text('Saving...');
        $('#btnSave').attr('disabled',true);
        $.ajax({
            url:'<?php echo site_url('C_jadwal_2/ajax_skema_copy_data/')?>',
            type:"post",
            dataType: "JSON",
            data:$('#form_copy').serialize(),
            success: function(data){
                $('#btnSave').text('Submit'); 
                $('#btnSave').attr('disabled',false);

                if(data.status){
                    $('#modal_form_copy').modal('hide');
                    reload_table_ajax();
                }else{
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="'+data.inputerror[i]+'"]').text(data.error_string[i]);
                        $('[name="'+data.inputerror[i]+'"]').addClass('has-error');
                    }
                }
            }
        });
    });
});



function edit_skema_nilai(id_kls)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');

    $.ajax({
        url : "<?php echo site_url('C_jadwal_2/ajax_skema_edit')?>" +"/"+id_kls,
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
                $('#inputselect').val('t');
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 40 && data.bobot_keterampilan == 40){
                $('#inputselect').val('tp');
            }else if(data.bobot_sikap==20 && data.bobot_pengetahuan == 30 && data.bobot_keterampilan == 50){
                $('#inputselect').val('p');
            }
           

            $('#modal_form').modal({backdrop: 'static', keyboard: false});
            $('.modal-title').text("SKEMA NILAI");
            $('.modal-sub').text('['+data.nm_kls+'] '+data.nm_mk); 

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save(){
    $('#btnSave').text('Saving...');
    $('#btnSave').attr('disabled',true);
    var data = $('#form').serializeArray();
    data.push({name: '<?php echo $this->security->get_csrf_token_name(); ?>', value: '<?php echo $this->security->get_csrf_hash(); ?>'});
   
    $.ajax({
        type:"POST",
        url:"<?php echo site_url('C_jadwal_2/ajax_skema_update'); ?>",
        data:data,
        dataType: "JSON",

        success:function (data) {
            $('#btnSave').text('Submit'); 
            $('#btnSave').attr('disabled',false);

            if(data.status){
                $('#modal_form').modal('hide');
                reload_table_ajax();
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
}

function ajax_copy(id_kls)
{
    $('#form_copy')[0].reset();
    $('.form-group').removeClass('has-error');

    $.ajax({
        url : "<?php echo site_url('C_jadwal_2/get_copy_data')?>" +"/"+id_kls,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if(data){
                $('[name="id_kls"]').val(id_kls);
                $('[name="sumber_data"]').val(data.nm_kls+' | '+data.nm_mk);

                $('#modal_form_copy').modal({backdrop: 'static', keyboard: false});
                $('.modal-title').text("Copy Skema Nilai");
                //$('.modal-sub').text('['+data.nm_kls+'] '+data.nm_mk); 
            }else{
                alert(data.message);
            } 

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function ajax_reset_bap(id) {
    if (confirm('Are you sure?')) {
        $.ajax({
            url: "<?php echo site_url('C_jadwal_2/ajax_reset_bap') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            data: function(dtRequest) {
                dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                return dtRequest;
            },
            success: function(data) {
                reload_table();
            },
            error: function(jqXHR, textStatus, errorThrown) {
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
                <h4 class="modal-sub">Person Form</h4>
            </div>
            <div class="modal-body form">
                <?php
                    $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                    echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    <input type="hidden" name="id_kls"  />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Pilih Kategori Perkuliahan</label>
                        <div class="col-md-8">
                            <select class="form-control" id="inputselect">
                                <option value="0">-- Pilih --</option>
                                <option value="t">Teori</option>
                                <option value="tp">Teori + Praktik/Praktikum</option>
                                <option value="p">Praktik/Praktikum</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Sikap</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Sikap" data-container="body"></i>
                                <input name="bobot_sikap" class="form-control" required="required" value="" type="text" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Pengetahuan</label>
                        <div class="col-md-8">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen pengetahuan" data-container="body"></i>
                            <input name="bobot_pengetahuan" class="form-control has-error" required="required" value="" type="text" readonly="readonly">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">a. [QUI] Quiz, pre test, post test</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Quiz" data-container="body"></i>
                            <input name="bobot_pengetahuan_quiz" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">b. [TTS] Tugas Terstruktur</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Tugas Terstruktur" data-container="body"></i>
                            <input name="bobot_pengetahuan_tugas_terstruktur" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">c. [UTS] Ujian Tengah Semester</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Ujian Tengah Semester" data-container="body"></i>
                            <input name="bobot_pengetahuan_uts" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">d. [UAS] Ujian Akhir Semester</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="Bobot persen Ujian Akhir Semester" data-container="body"></i>
                            <input name="bobot_pengetahuan_uas" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Keterampilan</label>
                        <div class="col-md-8">
                        <div class="input-icon right">
                            <i class="fa fa-warning tooltips" data-original-title="please provide an email" data-container="body"></i>
                            <input name="bobot_keterampilan" class="form-control" required="required" value="" type="text" readonly="readonly">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">a. [UKN] Unjuk Kinerja</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                            <input name="bobot_keterampilan_unjuk_kinerja" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">b. [PRY] Proyek</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                            <input name="bobot_keterampilan_proyek" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">c. [POR] Portofolio</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                            <input name="bobot_keterampilan_portofolio" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">d. [PRD] Produk</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                            <input name="bobot_keterampilan_produk" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 col-md-offset-4 control-label">e. [PRE] Presentasi</label>
                        <div class="col-md-4">
                        <div class="input-icon right">
                            <i class="fa fa-info-circle tooltips" data-original-title="You look OK!" data-container="body"></i>
                            <input name="bobot_keterampilan_presentasi" class="form-control" required="required" value="" type="number">
                        </div>
                        </div>
                    </div>
                                
                </div>
                <?php
                    echo form_close();
                ?>
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
                    <input type="hidden" name="id_kls"  />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Sumber Data</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='sumber_data'/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Copy skema ke kelas</label>
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