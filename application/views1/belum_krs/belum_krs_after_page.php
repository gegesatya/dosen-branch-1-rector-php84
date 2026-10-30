<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            var angkatan = $('#ddAngkatan :selected').val();
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('Lecturer_belum_krs/ajax_list') ?>" + "/" + angkatan,
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [0, 1, 3, 4, 5, 6],
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

    $(document).ready(function() {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
        });

        $('select[name=ddAngkatan]').on('change', function() {
            TableAjax.init();
        });

        $("input").change(function() {
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
        });
    });



    function edit_pengajuan_krs(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_belum_krs/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $.ajax({
                    url: "<?php echo site_url('Lecturer_belum_krs/ajax_get_paket_detail/') ?>/" + data.id_paket_krs,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data) {
                        $('#tabel_paket_detail').html(data.tabel_paket_detail);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error get data from ajax');
                    }
                });

                $('[name="id_rencana_studi"]').val(data.id_rencana_studi);
                $('[name="registrasi_alamat"]').val(data.registrasi_alamat);
                $('[name="registrasi_no_hp"]').val(data.registrasi_no_hp);
                $('[name="registrasi_no_hp_ortu"]').val(data.registrasi_no_hp_ortu);
                $('[name="registrasi_nominal_spp"]').val(data.registrasi_nominal_spp);
                $('[name="id_dosen_siakad"]').val(data.id_dosen_siakad);
                $('[name="id_paket_krs"]').val(data.id_paket_krs);
                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Pengajuan KRS");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }


    function form_approval(id) {
        save_method = 'update';
        $('#form_approval')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_belum_krs/ajax_get_krs/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(data.id_rencana_studi);
                $('[name="nm_sdm"]').val(data.nm_ptk);
                //$('[name="nm_paket_krs"]').val(data.nm_paket_krs);
                $('[name="total_sks"]').val(data.total_sks);
                $('[name="registrasi_alamat"]').val(data.registrasi_alamat);
                $('[name="registrasi_no_hp"]').val(data.registrasi_no_hp);
                $('[name="registrasi_no_hp_ortu"]').val(data.registrasi_no_hp_ortu);
                $('[name="registrasi_nominal_spp"]').val(data.registrasi_nominal_spp);
                $('[name="total_sks"]').val(data.total_sks);
                $('[name="img_slip_spp_submit"]').val(data.img_slip_spp);
                //$('[name="img_khs_submit"]').val(data.img_khs);

                if (data.cek_val) {
                    $('#modal_form_approval').modal('show');
                    $('.modal-title').text("Persetujuan KRS");
                } else {
                    alert(data);
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }



    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('Lecturer_belum_krs/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('Lecturer_belum_krs/ajax_update') ?>";
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
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
    }

    function show_krs(id_rencana_studi, nipd, id_smt) {
        $.ajax({
            url: "<?php echo site_url('Lecturer_belum_krs/view_krs') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#nm_pd').val(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#id_smt').val(data.id_smt);
                $('#tabel_nilai').html(data.tabel);
                $('#modal_form_krs').modal('show');
                $('.modal-title').text("Kartu Rencana Studi / Card Of Study Plan");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function show_chat(id_rencana_studi, nipd, id_smt) {
        $('#modal_chat').modal('show');
        $('.modal-title').text("Kartu Rencana Studi / Card Of Study Plan");
    }

    function view_foto(id) {
        $.ajax({
            url: "<?php echo site_url('Lecturer_belum_krs/ajax_view_foto') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#modal_foto').modal('show');
                $('#modal_foto .modal-title').text(data.nm_pd + " (" + data.nipd + ")");
                
                var foto_url = "<?php echo base_url('assets/images.png') ?>";
                if (data.foto) {
                    foto_url = "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto;
                }
                $('#pas_foto').attr('src', foto_url);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>



<div class="modal fade" id="modal_form_approval" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <?php
            $attributes = ['class' => 'form-horizontal', 'id' => 'form_approval'];
            echo form_open(site_url("Lecturer_belum_krs/persetujuan_krs"), $attributes);
            ?>
            <div class="modal-body form">
                <input type="hidden" value="" name="id_rencana_studi" />
                <div class="form-body">
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Dosen Wali</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="nm_sdm" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Total SKS</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="total_sks" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Alamat</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <textarea name="registrasi_alamat" readonly class="form-control" row=3></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">No Handphone</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="registrasi_no_hp" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">Nomor HP Ayah/Ibu</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="registrasi_no_hp_ortu" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="control-label col-md-4">SPP Terakhir</label>
                        <div class="col-md-8">
                            <div class="input-icon right">
                                <i class="fa fa-check tooltips" data-original-title="Data OK!" data-container="body"></i>
                                <input type="text" name="registrasi_nominal_spp" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="well">
                    <h4>Petunjuk</h4>
                    <ol>
                        <li>Sebelum menyetujui KRS Mahasiswa, lakukan pengecekan terlebih dahulu terhadap mata kuliah yang diambil</li>
                        <li>Cek juga data-data pendukung</li>
                        <li>Jika tidak menyetujui, pastikan Mahasiswa untuk melakukan pengajuan ulang</li>
                    </ol>
                    <div class="form-group">
                        <label class="control-label col-md-4">Persetujuan</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('id_status_krs', $dd_status_krs, '', 'class="form-control" required');
                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Submit KRS', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <form class="form-horizontal" role="form">
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Nama:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='nm_pd' value='' readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">NIM:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='nipd' value='' readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Tahun Ajaran:</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id='id_smt' value='' readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="tabel_nilai">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_chat" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-sm-12">
                    
                    <div class="portlet light">
                        
                        <div class="portlet-body">
                            <div class="general-item-list">
                                <div class="item">
                                    <div class="item-head">
                                        <div class="item-details">
                                            <a href="" class="item-name primary-link">Nick Larson</a>
                                            <span class="item-label">3 hrs ago</span>
                                        </div>
                                        <span class="item-status"><span class="badge badge-empty badge-success"></span> Open</span>
                                    </div>
                                    <div class="item-body">
                                        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_pesan'];
                echo form_open(site_url("Lecturer_belum_krs/persetujuan_krs"), $attributes);
                ?>
                <div class="modal-body form">
                    <input type="hidden" value="" name="id_rencana_studi" />
                    <div class="form-group">
                        <div class="col-md-12">
                            <textarea name="registrasi_alamat" class="form-control" row=3></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php echo form_submit('btn_submit_pesan', 'Kirim Pesan', 'class="btn btn-primary"'); ?>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_foto" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Pas foto Mahasiswa</h4>
            </div>
            <div class="modal-body ">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="nipd" />
                    <div class="form-body">
                        <div class="form-group">
                            <img style="padding: 10px; border: 1px solid #ddd; display: block; margin: 0 auto; width: 100%; max-width: 400px; object-fit: cover; border-radius: 8px;" id="pas_foto" src="" alt="Foto Mahasiswa">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>