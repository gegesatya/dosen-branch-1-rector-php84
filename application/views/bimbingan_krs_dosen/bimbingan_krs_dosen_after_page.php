<script type="text/javascript">

var save_method; //for save method string
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
            "url": "<?php echo site_url('c_bimbingan_krs_dosen/ajax_list')?>",
            "type": "POST"
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

$(document).ready(function() {

    /*table = $('#table').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('c_bimbingan_krs_dosen/ajax_list')?>",
            "type": "POST",

        },
        "bFilter":true,
        "bPaginate":true,
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        },
        ],
    });*/

    var jumlah_pengajuan_krs = $.ajax({
                                    url : "<?php echo site_url('c_bimbingan_krs_dosen/get_mahasiswa_mengajukan_krs')?>/",
                                    type: "POST",
                                    "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
                                    "dataType": "JSON",
                                    success: function(data)
                                    {
                                        $('#jumlah-pengajuan-krs').text(data.jumlah_mahasiswa_mengajukan_krs);
                                        $('#jumlah-krs-selesai').text(data.jumlah_krs_selesai);
                                    }
                                });
    
/*    $('#js-swal').on('click', function () {
        var type = $(this).data('type');

        if (type === 'prompt') {
            showPromptMessage();
        }
        
    });*/
    
});


function perbaikan_krs(id_reg_pd)
{
   /* swal({
        title: "Perbaikan",
        text: "Tulis alasan perbaikan:",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "Write something"
    }, function (inputValue) {
        if (inputValue === false) return false;
        if (inputValue === "") {
            swal.showInputError("Alasan perbaikan harus diisi!"); return false
        }*/

        var obj = { };
        obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
        //obj['inputValue'] = inputValue;

        $.ajax({
            url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_set_fase_2')?>/"+id_reg_pd,
            type: "POST",
            data: obj,
            success: function(data)
            {
                //swal("Terima kasih,", "Jangan lupa untuk menyetujui kembali KRS mahasiswa.", "success");
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error data');
            }
        });
     /*   
    });
*/
}


function add_matakuliah(){
    tbl = $('#mhs_table').DataTable({ 
            "bDestroy": true,
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('c_bimbingan_krs/get_kelaskuliah')?>",
                "type": "POST"
            },

            //Set column definition initialisation properties.
            "columnDefs": [
            { 
                "targets": [ 0,1,2,3,4,5,-1 ], //last column
                "orderable": false, //set not orderable
            },
            ],

        });

    $('#mhs_modal_form').modal('show');
    $('.modal-title').text("Daftar Mata Kuliah Tersedia");

}

$(document).on('change', '.mk_check', function() {
  // Does some stuff and logs the event to the console
   var id_reg_pd = $(this).attr("data-id_reg_pd"); // gets task ID of clicked checkbox
   var id_kls = $(this).attr("data-id_kls"); // gets task ID of clicked checkbox
   var state = $(this).is(':checked'); // gets if checkbox is checked or not
 
    if(state){ 
        $.post( "<?=site_url('c_bimbingan_krs/add_krs');?>"+ '/0/' + id_kls + '/' + id_reg_pd, function( data ) {
           reload_table();
        });
    }else{
       $.post( "<?=site_url('c_bimbingan_krs/add_krs');?>"+ '/1/'+ id_kls + '/' + id_reg_pd, function( data ) {
            reload_table();
        });
    }

});

function _select2(x){
    if(x == ''){
        return -1;
    }else{
        return x;
    }
}

function _date(x){

    if(x == null){
        return '';
    }else{
        var _mydate = new Date(x);
       
        var dd = _mydate.getDate();
        var mm = _mydate.getMonth()+1;

        var yyyy = _mydate.getFullYear();
        if(dd<10){
            dd='0'+dd;
        } 
        if(mm<10){
            mm='0'+mm;
        } 
        var _mydate = dd+'-'+mm+'-'+yyyy;

        return _mydate;
    }
}

function _time(x){
    if(x==null){ 
        return '';
    }else{
        return x.slice(0,-3);
    }
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
    get_jumlah_sks();
   
}

function reload_table_konsultasi(){
    table2.ajax.reload(null,false); //reload datatable ajax 
}


function konsultasi(id_reg_pd){
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    //table2.destroy();
    $.ajax({
        url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_konsultasi_by_id/')?>" + '/' + id_reg_pd,
        type: "GET",
        
        dataType: "JSON",
        success: function(data)
        {
            $('[name="id_pd"]').val(data.id_pd);
            $('[name="id_reg_pd"]').val(data.id_reg_pd);
            //$('[name="no_stampbook"]').val(data.no_stampbook);
            $('[name="nipd"]').val(data.nipd);
            $('[name="nm_pd"]').val(data.nm_pd);

            var obj = { };
            obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

            table2 = $('#table_konsultasi').DataTable({ 

                "processing": true, //Feature control the processing indicator.
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                "order": [], //Initial no order.

                // Load data for the table's content from an Ajax source
                "ajax": {
                    "url": "<?php echo site_url('c_bimbingan_krs_dosen/ajax_konsultasi_list')?>" + '/' + id_reg_pd,
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
                    "targets": [ 1,2,3,-1 ], //last column
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
    url = "<?php echo site_url('c_bimbingan_krs_dosen/ajax_konsultasi_add')?>";
    var data = $('#form').serializeArray();
    /*var obj = { };
    obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
    data.push(obj);*/
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
                $('[name="saran"]').val('');
                $('[name="problem"]').val('');

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

            url : "<?php echo site_url('c_bimbingan_krs_dosen/ajax_konsultasi_delete')?>/"+id,
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

function bimbingan_krs(id_reg_pd){
    var obj = { };
    obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

    $.ajax({
        url : "<?php echo site_url('c_bimbingan_krs_dosen/get_validasi_krs')?>/"+id_reg_pd,
        type: "POST",
        data: obj,
        success: function(data)
        {
            //$("#body-index-bimbingan").html(data);
            //$("#title-header").text("Bimbingan KRS");

            $("#modal-body-bimbingan").html(data);
            $('#modal_bimbingan').modal('show');
            $('.modal-title').text("Kartu Rencana Studi");
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error data');
        }
    });    
}

function validasi_krk(nipd){
    var obj = { };
    obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

    $.ajax({
        url : "<?php echo site_url('c_bimbingan_krs_dosen/get_validasi_krk')?>/"+nipd,
        type: "POST",
        data: obj,
        success: function(data)
        {
            //$("#body-index-bimbingan").html(data);
            //$("#title-header").text("Bimbingan KRS");

            $("#modal-body-krk").html(data);
            $('#modal_krk').modal('show');
            $('.modal-title').text("Kartu Rencana Kegiatan SK3");
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
                            <label class="control-label col-md-3">NIM</label>
                            <div class="col-md-3">
                                <input name="nipd" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama</label>
                            <div class="col-md-3">
                                <input name="nm_pd" class="form-control" type="text" readonly>
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
                            <label class="control-label col-md-3">Topik Diskusi Akademik</label>
                            <div class="col-md-9">
                                <div class="form-line">
                                    <textarea rows="3" class="form-control no-resize" name="problem" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Saran ke Mahasiswa</label>
                            <div class="col-md-9">
                                <div class="form-line">
                                    <textarea rows="3" class="form-control no-resize" name="saran" placeholder=""></textarea>
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
                        <table id="table_konsultasi" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:15px">#</th>
                                    <th style="width:80px">Tanggal</th>
                                    <th>Topik Diskusi Akademik</th>
                                    <th>Saran Dosen ke Mahasiswa</th>
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

<div class="modal fade" id="modal_krk" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:80%" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #3AB1B7;">
                <h3 class="modal-title" id="largeModalLabel" style="color: #fff; margin-bottom: 15px">Modal title</h3>
            </div>
            <div class="modal-body" >
                <div id="modal-body-krk">
                    
                </div>
            </div>
            <div class="modal-footer" style="background-color: #3AB1B7;">
                <button type="button" class="btn btn-link waves-effect" style="color: #fff" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

