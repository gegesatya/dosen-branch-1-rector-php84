<script type="text/javascript">

var save_method; 
var table;
var table2;

var TableAjax = function () {
    var handleRecords = function () {
    table = $('#datatable_ajax').DataTable({ 
        "processing": true,
        "serverSide": true,
        "pagingType": "full_numbers",
        "order": [],
        "ajax": {
            "url": "<?php echo site_url('c_form_konsultasi/ajax_list')?>",
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

function reload_table()
{
    table.ajax.reload(null,false);
}

function reload_table_konsultasi(){
    table2.ajax.reload(null,false);
}


function konsultasi(id_reg_pd){
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    //table2.destroy();
    $.ajax({
        url : "<?php echo site_url('c_form_konsultasi/ajax_konsultasi_by_id/')?>" + '/' + id_reg_pd,
        type: "GET",
        
        dataType: "JSON",
        success: function(data)
        {
            $('[name="id_pd"]').val(data.id_pd);
            $('[name="id_reg_pd"]').val(data.id_reg_pd);
            //$('[name="no_stampbook"]').val(data.no_stampbook);
            $('[name="nipd"]').val(data.nipd +' | '+ data.nm_pd);
            

            var obj = { };
            obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

            table2 = $('#table_konsultasi').DataTable({ 

                "processing": true, //Feature control the processing indicator.
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                "order": [], //Initial no order.

                // Load data for the table's content from an Ajax source
                "ajax": {
                    "url": "<?php echo site_url('c_form_konsultasi/ajax_konsultasi_list')?>" + '/' + id_reg_pd,
                    "data" :  obj,
                    "type": "POST"
                },
                "bFilter":true,
                "bPaginate":true,
                "bLengthChange": false,
                destroy:true,
                pageLength: 5,
                //Set column definition initialisation properties.
                "columnDefs": [
                { 
                    "targets": [ 1,2,3, 4,-1 ], //last column
                    "orderable": false, //set not orderable
                },
                ],

            });
            

            $('#modal_form').modal('show');
            $('.modal-title').text("Form Konsultasi Mahasiswa");
            //$('#btnSave').attr('disabled',true); 
            $('#btnCancel').text('Tutup');

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });

}

function save()
{
    var url;
    url = "<?php echo site_url('c_form_konsultasi/ajax_konsultasi_add')?>";
    var data = $('#form').serializeArray();
    
    data.push({name: '<?php echo $this->security->get_csrf_token_name(); ?>', value: '<?php echo $this->security->get_csrf_hash(); ?>'});

    $.ajax({
        url : url,
        type: "POST",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            if(data.status)
            {
                //$('[name="tanggal_konsultasi"]').val('');
                $('[name="konsultasi_akademik"]').val('');
                $('[name="catatan_dosen"]').val('');

                $('.form-group').removeClass('has-error'); 
                $('.help-block').empty();
                reload_table_konsultasi();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }
            }
            $('#btnSave').text('Save');
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

function delete_konsultasi(id)
{
    if(confirm('Are you sure delete this data??'))
    {
        // ajax delete data to database
            var obj = { };
            obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({

            url : "<?php echo site_url('c_form_konsultasi/ajax_konsultasi_delete')?>/"+id,
            type: "POST",
            data:obj,

            success: function(data)
            {
                //if success reload ajax table
                
                reload_table_konsultasi();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }
}

function transkrip_mahasiswa(id_reg_pd){
    var obj = { };
    obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

    $.ajax({
        url : "<?php echo site_url('c_transkrip_mahasiswa/get_transkrip')?>/"+id_reg_pd,
        type: "POST",
        data: obj,
        success: function(data)
        {
            $("#modal-body-transkrip").html(data);
            $('#modal_transkrip').modal('show');
            $('.modal-title').text("Transkrip Mahasiswa");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error data');
        }
    });
}



</script>

<div class="modal fade" id="modal_form" role="dialog" >
    <div class="modal-dialog" style="width:1200px !important">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3AB1B7;">
                <h3 class="modal-title" id="largeModalLabel" style="color: #fff; margin-bottom: 15px">Modal title</h3>
            </div>
            <div class="modal-body">
                <div class="clearfix">
                    <form action="#" id="form" class="form-horizontal">
                        <input type="hidden" value="" name="id_pd"/> 
                        <input type="hidden" value="" name="id_reg_pd"/> 
                        <div class="form-group">
                            <label class="control-label col-md-3">NIM / Nama</label>
                            <div class="col-md-9">
                                <input name="nipd" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-3">Tanggal Konsultasi</label>
                            <div class="col-md-3">
                                <div class="form-line demo-masked-input">
                                    <input class="form-control date" name="tanggal_konsultasi" placeholder="Contoh: 30-07-2016" required value="<?=date('d-m-Y');?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipe Konsultasi</label>
                            <div class="col-md-9">
                                <div class="form-line">
                                    <select class="form-control" style="font-size: 15px !important" name="tipe_konsultasi" >
                                      <option value="">--Silahkan Pilih--</option>
                                    <?php
                                        foreach ($tipe_konsultasi as $key => $value) {
                                          ?>
                                            <option value="<?=$value;?>"><?=$value;?></option>
                                          <?php
                                        }
                                    ?>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Catatan Dosen</label>
                            <div class="col-md-9">
                                <div class="form-line">
                                    <textarea rows="3" class="form-control" name="catatan_dosen" placeholder=""></textarea>
                                    <span class="help-block"></span>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Konsultasi Akademik</label>
                            <div class="col-md-9">
                                <div class="form-line">
                                    <textarea rows="3" class="form-control" name="konsultasi_akademik" placeholder=""></textarea>
                                    <span class="help-block"></span>
                                    
                                </div>
                            </div>
                        </div>

                        
                    </form>
                </div>
                <div class="clearfix">
                    <div class="col-md-12">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-9">
                            <button type="button" class="btn bg-teal waves-effect" id="btnSave" onclick="save()">SAVE</button>
                            
                        </div>
                    </div>
                </div>

                <div class="clearfix">
                    <div class="col-md-12">
                        <table id="table_konsultasi" width="100%" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:15px">#</th>
                                    <th style="width:80px">Tanggal</th>
                                    <th>Tipe Konsultasi</th>
                                    <th>Catatan Dosen</th>
                                    <th>Konsultasi Akademik</th>
                                    <th>Feedback Mahasiswa</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="modal-footer" style="background-color: #3AB1B7;">
                <button type="button" class="btn btn-link waves-effect" style="color: #fff" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_transkrip" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:70%" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3AB1B7;">
                <h3 class="modal-title" id="largeModalLabel" style="color: #fff; margin-bottom: 15px">Modal title</h3>
            </div>
            <div class="modal-body" >
                <div id="modal-body-transkrip">
                    
                </div>
            </div>
            <div class="modal-footer" style="background-color: #3AB1B7;">
                <button type="button" class="btn btn-link waves-effect" style="color: #fff" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_bimbingan" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:80%" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3AB1B7;">
                <h3 class="modal-title" id="largeModalLabel" style="color: #fff; margin-bottom: 15px">Modal title</h3>
            </div>
            <div class="modal-body" >
                <div id="modal-body-bimbingan">
                    
                </div>
            </div>
            <div class="modal-footer" style="background-color: #3AB1B7;">
                <button type="button" class="btn btn-link waves-effect" style="color: #fff" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>