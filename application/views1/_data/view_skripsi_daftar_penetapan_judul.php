<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gift font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small> </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container table-responsive-2">
                        <div class="margin-top-10 margin-bottom-10">
                            <button class="btn btn-sm btn-secondary" onclick="reload_ajax()"><i class="fa fa-refresh"></i> Reload</button>
                        </div>
                        <table id="ajax_tabel" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">No.</th>
                                    <th>Tgl Diajukan</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    
                                    <th>Judul Yg Ditetapkan</th>
                                    <th>Pembimbing</th>
                                    <th>Status</th>
                                    <th style="width: 80px;">Action</th>
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
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <div class="col-md-12" id="tabel-data" name="tabel-data"></div>
                <?php
                $attributes = ['id' => 'form', 'class' => 'form-horizontal'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_judul_skripsi" />
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                <div class="form-body">

                    <div class="form-group">
                        <label class="control-label col-md-3"><strong>Judul yang ditetapkan</strong></label>
                        <div class="col-md-9">
                            <textarea class="form-control" row=2 required id="judul_ditetapkan" name="judul_ditetapkan"></textarea>
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
        load_kategori();
        load_dosen_penguji();
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
                "url": "<?= base_url() . 'E_skripsi_daftar_penetapan_judul/get_all' ?>",
                "type": "POST",
                "data": function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
            },
            columns: [{
                    "data": "id_judul_skripsi",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "tanggal_diajukan",
                    "orderable": false,
                    "render": function(data) {
                        var formattedDate = new Date(data);
                        var d = formattedDate.getDate();
                        var m = formattedDate.getMonth();
                        m += 1;
                        var y = formattedDate.getFullYear();
                        return d + "-" + m + "-" + y;
                    }
                },
                {
                    "data": "nipd",
                    "orderable": false,
                },
                {
                    "data": "nm_pd",
                    "orderable": false,
                }
                /* ,
                                {
                                    "data": "topik_skripsi",
                                    "orderable": false,
                                } */
                ,
                {
                    "data": "judul_ditetapkan",
                    "orderable": false,
                },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "render": function(data) {
                        return "<pre>Doping 1 : " + data.pembimbing_1 + "<br>Doping 2 : " + data.pembimbing_2 + "<br>Penguji  : " + data.penguji + "<br>Penguji 2: " + data.penguji_2 + "</pre>";
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "render": function(data) {
                        var r = '';
                        if (data.status_penetapan_judul == 0) {
                            r = "<i class='fa fa-times font-red'></i>";
                        } else {
                            r = "<i class='fa fa-check font-green'></i>";
                        }
                        return r;
                    }
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

        /* $('#modal_form').on('shown.bs.modal', function(e) {
            load_kategori();
        }); */

        $('#modal_form').on('hidden.bs.modal', function(e) {
            var inputs = $('#form input, #form textarea, #form select');
            inputs.removeClass('is-valid is-invalid');
        });
    });

    function copy_a() {
        $('[name="judul_ditetapkan"]').val($('[name="judul_a"]').text());
    }

    function copy_b() {
        $('[name="judul_ditetapkan"]').val($('[name="judul_b"]').text());
    }

    function copy_c() {
        $('[name="judul_ditetapkan"]').val($('[name="judul_c"]').text());
    }

    function load_kategori() {
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_penetapan_judul/getDosen') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="dosen_pembimbing_1"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_pembimbing_1"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_pembimbing_1"]').append(opsi_dosen);
                });

                $('[name="dosen_pembimbing_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_pembimbing_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_pembimbing_2"]').append(opsi_dosen);
                });
            }
        });
    }

    function load_dosen_penguji() {
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_penetapan_judul/getDosenPenguji') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_dosen;
                $('[name="dosen_penguji"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_penguji"]').append(opsi_dosen);
                });

                $('[name="dosen_penguji_2"]').html('');
                opsi_dosen = `<option value="">Silahkan Pilih</option>`;
                $('[name="dosen_penguji_2"]').append(opsi_dosen);
                $.each(categories, function(key, val) {
                    opsi_dosen = `<option value="${val.id_ptk}">${val.nm_ptk}</option>`;
                    $('[name="dosen_penguji_2"]').append(opsi_dosen);
                });
            }
        });
    }

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

    function edit_pengajuan_judul(id) {
        save_label = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        //Ajax Load data from ajax
        $.ajax({
            url: "<?= base_url('E_skripsi_daftar_penetapan_judul/edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('[name="id_judul_skripsi"]').val(data[0].id_judul_skripsi);

                $('[name="tabel-data"]').html(data[2]);
                $('[name="judul_ditetapkan"]').val(data[0].judul_ditetapkan);
                $('[name="dosen_pembimbing_1"]').val(data[0].dosen_pembimbing_1);
                $('[name="dosen_pembimbing_2"]').val(data[0].dosen_pembimbing_2);
                $('[name="dosen_penguji"]').val(data[0].dosen_penguji);
                $('[name="dosen_penguji_2"]').val(data[0].dosen_penguji_2);

                $('#modal_form').modal('show');
                $('.modal-title').text('Penetapan Judul dan Dosen Pembimbing Skripsi');
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

        if (save_label == 'add') {
            url = "<?= base_url('E_skripsi_daftar_penetapan_judul/add') ?>";
            method = 'disimpan';
        } else {
            url = "<?= base_url('E_skripsi_daftar_penetapan_judul/update') ?>";
            method = 'diupdate';
        }

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
            $(this).removeClass('is-valid has-error');
        });
        $('#form select').on('change', function() {
            $(this).removeClass('is-valid has-error');
        });
    }
</script>