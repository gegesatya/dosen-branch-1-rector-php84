<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <br><small><?= $judul_kecil; ?></small> </span>
                        <br>
                    </div>
                </div>
                <div class="note note-info">
                    <h4 class="block">Judul Skripsi: </h4>
                    <h4><strong><?= $judul_skripsi; ?></strong></h4>
                </div>
                <div class="portlet-body">
                    <button class="btn btn-sm btn-default margin-bottom-10" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Refresh</button>
                    <table id="ajax_tabel" class="table table-bordered ">
                        <thead>
                            <tr>
                                <th style="width: 10px;vertical-align: middle;">No.</th>
                                <th style="vertical-align: middle;">Tanggal</th>
                                <th style="width: 400px;vertical-align: middle;">Materi Bimbingan</th>
                                <th style="vertical-align: middle;">Respon</th>
                                <th style="width: 80px !important;vertical-align: middle;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <div class='col-md-12 col-sm-12'>
                    <div class='portlet light'>
                        <div class='portlet-title'>
                            <div class='caption caption-md'>
                                <i class='icon-bar-chart theme-font-color hide'></i>
                                <span class='caption-subject theme-font-color bold uppercase'>Respon dan Diskusi</span>
                                <span class='caption-helper'></span>
                            </div>
                        </div>
                        <div class='portlet-body'>
                            <div class="well">
                                <p><strong>Materi Bimbingan / Pertanyaan</strong></p>
                                <textarea class="form-control summernote2" row=2 id="materi_bimbingan" name="materi_bimbingan"></textarea>
                            </div>
                            <div class='general-item-list' name='my-items' id='my-items' style="height: 250px; overflow-y: scroll;"></div>
                        </div>
                    </div>
                </div>
                <?php
                $attributes = ['id' => 'form', 'class' => 'form-horizontal'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <input type="hidden" value="" name="id_bimbingan" />
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3"><strong>Message</strong></label>
                        <div class="col-md-9">
                            <textarea class="form-control summernote" row=2 id="respon_bimbingan" name="respon_bimbingan"></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<?php
$this->load->view('_layout/_script_datatables');
?>

<script src="<?= base_url('assets/theme'); ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url('assets/theme'); ?>/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets/theme'); ?>/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    var save_label;
    var table;

    $(document).ready(function() {


        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "auto",
            todayBtn: true,
            todayHighlight: true,
            forceParse: false
        });

        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        };

        $('.summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 80,
            dialogsInBody: true,
            toolbar: [
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
            ]
        });

        $('.summernote2').summernote({
            airMode: true
        });

        table = $("#ajax_tabel").DataTable({
            initComplete: function() {
                var api = this.api();
                $('#ajax_tabel_filter input')
                    .off('.DT')
                    .on('keyup.DT', function(e) {
                        api.search(this.value).draw();
                    });
            },
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?= base_url() . 'E_skripsi_kartu_bimbingan/get_all/' . $nipd ?>",
                "type": "POST",
                "data": function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
            },
            columns: [{
                    "data": "id_bimbingan",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "tanggal_bimbingan",
                    "orderable": false,
                    "render": function(data) {
                        var formattedDate = new Date(data);
                        var d = formattedDate.getDate();
                        var m = formattedDate.getMonth();
                        m += 1;
                        var y = formattedDate.getFullYear();
                        return d.toString().padStart(2, 0) + "/" + m.toString().padStart(2, 0) + "/" + y;
                    }
                },
                {
                    "data": "materi_bimbingan",
                    "orderable": false
                },
                {
                    "data": "html_respon",
                    "orderable": false
                },
                {
                    "data": "view",
                    "orderable": false,
                    "searchable": false
                }
            ],
            order: [
                [1, 'asc']
            ],
            rowId: function(a) {
                return a;
            },
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                var index = page * length + (iDisplayIndex + 1);
                $('td:eq(0)', row).html(index);
            }
        });

        $('#modal_form').on('shown.bs.modal', function(e) {
            var messageBody = document.querySelector('#my-items');
            messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
            //alert(messageBody.scrollTop);
        });

        $('#modal_form').on('hidden.bs.modal', function(e) {
            var inputs = $('#form input, #form textarea, #form select');
            inputs.removeClass('is-valid is-invalid');
        });

        /*  $("input").change(function() {
             $(this).parent().parent().removeClass('has-error');
             $(this).next().empty();
         });
         $("textarea").change(function() {
             $(this).parent().parent().removeClass('has-error');
             $(this).next().empty();
         });
         $("select").change(function() {
             $(this).parent().parent().removeClass('has-error');
             $(this).next().empty();
         }); */
    });

    function reload_ajax() {
        table.ajax.reload(null, false);
    }

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function edit_respon(id) {
        save_label = 'update';
        $('#form')[0].reset();
        $('[name="respon_bimbingan"]').summernote("code", "<p><br></p>");
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= base_url('E_skripsi_kartu_bimbingan/edit_respon') ?>/" + id,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('[name="id_judul_skripsi"]').val(data[0].id_judul_skripsi);
                $('[name="id_bimbingan"]').val(data[0].id_bimbingan);
                $('[name="materi_bimbingan"]').summernote("code", data[0].materi_bimbingan);
                $('[name="my-items"]').html(data[1]);

                $('#modal_form').modal('show');
                $('.modal-title').text('Respon dan Diskusi');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }



    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url, method;

        if (save_label == 'update') {
            url = "<?= base_url('E_skripsi_kartu_bimbingan/add_respon') ?>";
            method = 'Disimpan';
        }
        /* else {
                   url = "<?= base_url('E_skripsi_kartu_bimbingan/update') ?>";
                   method = 'diupdate';
               } */

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "json",
            success: function(data) {
                console.log(data);
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_ajax();
                    swalert(method);
                } else {
                    $.each(data.errors, function(key, value) {
                        $('[name="' + key + '"]').parent().parent().addClass('has-error');
                        $('[name="' + key + '"]').next().text(value);
                        if (value == "") {
                            $('[name="' + key + '"]').parent().parent().removeClass('has-error');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save');
                $('#btnSave').attr('disabled', false);
            }
        });

        $('#form input').on('keyup', function() {
            $(this).removeClass('is-valid is-invalid');
        });
        $('#form select').on('change', function() {
            $(this).removeClass('is-valid is-invalid');
        });
    }
</script>