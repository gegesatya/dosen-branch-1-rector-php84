<script src="<?=base_url('assets/elite-admin');?>/assets/node_modules/datatables/datatables.min.js"></script>

<script type="text/javascript">

var save_method; 
var table;

$(document).ready(function() {

    //datatables
    table = $('#table').DataTable({ 

        "processing": true, 
        "serverSide": true, 
        "order": [], 
        "pageLength": 10,

        "ajax": {
            "url": "<?php echo site_url('setting/ajax_list')?>",
            "type": "POST",
            "data":{'<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'},
        },

        "columnDefs": [
        { 
            "targets": [ -1],
            "orderable": false,
        },
        ],

    });

    $('#tanggal_realisasi').bootstrapMaterialDatePicker({ weekStart: 0, time: false });
    $('#tanggal_reschedule').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

    $('#open_time_start').bootstrapMaterialDatePicker({ format: 'YYYY-MM-DD HH:mm' });
    $('#open_time_end').bootstrapMaterialDatePicker({ format: 'YYYY-MM-DD HH:mm' });

    $("input").change(function(){
        $(this).parent().parent().removeClass('has-danger');
        $(this).next().empty();
    });
    $("textarea").change(function(){
        $(this).parent().parent().removeClass('has-danger');
        $(this).next().empty();
    });
    $("select").change(function(){
        $(this).parent().parent().removeClass('has-danger');
        $(this).next().empty();
    });

});

function add_jadwal()
{
    save_method = 'add';
    $('#form')[0].reset(); 
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#modal_form').modal('show');
    $('.modal-title').text('Tambah Jadwal');
}

function edit_jadwal(id)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-danger');
    $('.form-control-feedback').empty();

    $.ajax({
        url : "<?php echo site_url('setting/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="id"]').val(data.id);
            $('[name="tanggal_start"]').val(data.tanggal_start);
            $('[name="tanggal_end"]').val(data.tanggal_end);
            
            $('#modal_form').modal('show'); 
            $('.modal-title').text("Edit Jadwal");

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false);
}

function save()
{
    $('#btnSave').text('saving...');
    $('#btnSave').attr('disabled',true);
    var url;
    var data;

    if(save_method == 'add') {
        url = "<?php echo site_url('setting/ajax_add')?>";
    } else {
        url = "<?php echo site_url('setting/ajax_update')?>";
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
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-danger'); 
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }
            }
            $('#btnSave').text('Save'); 
            $('#btnSave').attr('disabled',false); 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('Save'); 
            $('#btnSave').attr('disabled',false);  

        }
    });
}

function delete_jadwal(id)
{
    if(confirm('Are you sure delete this data?'))
    {
        // ajax delete data to database
        var obj = { };
        obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url : "<?php echo site_url('setting/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            data: obj,
            success: function(data)
            {
                $('#modal_form').modal('hide');
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


<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal Content is Responsive</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body form">
                <form class="form-horizontal p-t-10" id="form" method="post">
                    <input type="hidden" id="id" name="id" >
                    <div class="form-group row" style="margin-bottom: 0.5rem;">
                        <label for="open_time_start" class="col-sm-4 col-form-label">Mulai</label>
                        <div class="col-sm-8 col-md-8">
                            <input type="text" class="form-control" id="open_time_start" required name="tanggal_start" data-dtp="dtp_00aWj">
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="form-group row" style="margin-bottom: 0.5rem;">
                        <label for="open_time_end" class="col-sm-4 col-form-label">Selesai</label>
                        <div class="col-sm-8 col-md-8">
                            <input type="text" class="form-control" id="open_time_end" required name="tanggal_end" data-dtp="dtp_00aWj">
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>