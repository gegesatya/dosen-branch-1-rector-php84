<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            // var angkatan = $('#ddAngkatan :selected').val();
            var status_krs = $('#ddstatus :selected').val();

            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('Lecturer_persetujuan_krs/ajax_list') ?>" + "/" + status_krs,
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4],
                    "orderable": false,
                }, {
                    "targets": -1,
                    "width": 500,
                }, {
                    "targets": 0,
                    "width": 100,
                }],

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
            todayHighlight: true,
        });

        // $('select[name=ddAngkatan]').on('change', function() {
        //     TableAjax.init();
        // });

        $('select[name=ddstatus]').on('change', function() {
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

        $('#form_balas_pesan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_balas_pesan') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: (function() {
                    var formData = $('#form_balas_pesan').serializeArray();
                    // Basic client-side sanitization: remove script tags and encode basic entities
                    for (var i = 0; i < formData.length; i++) {
                        if (formData[i].name === 'catatan_dosen_wali') {
                            formData[i].value = formData[i].value.replace(/<script\b[^>]*>([\s\S]*?)<\/script>/gmi, "");
                            // We don't necessarily need to encode everything here since server does it, 
                            // but it's good as a first layer.
                        }
                    }
                    return formData;
                })(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        // Refresh message history
                        var id_rencana_studi = $('#form_balas_pesan [name="id_rencana_studi"]').val();
                        var nipd = $('#msg_nipd').val();
                        var id_smt = "<?php echo $this->session->userdata('setting')->tahun_ajaran_aktif; ?>";
                        
                        // Call view_pesan again to refresh history
                        $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(msgData) {
                            $('#data_pesan').html(msgData.data_pesan);
                            $('#form_balas_pesan [name="catatan_dosen_wali"]').val('');
                            
                            // Scroll to bottom
                            var d = $('#data_pesan');
                            d.scrollTop(d.prop("scrollHeight"));
                        });
                    }
                }
            });
        });

        // Refresh datatable only when message modal is closed
        $('#modal_form_balas_pesan').on('hidden.bs.modal', function () {
            reload_table();
        });

        $('#form_persetujuan').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            url = "<?php echo site_url('Lecturer_persetujuan_krs/persetujuan_krs') ?>";

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form_persetujuan').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_krs').modal('hide');
                        reload_table();
                    }
                }
            });
        });
    });

    function edit_pengajuan_krs(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $.ajax({
                    url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_get_paket_detail/') ?>/" + data.id_paket_krs,
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
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_get_krs/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(data.id_rencana_studi);
                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);
                $('[name="nm_sdm"]').val(data.nm_ptk);
                $('[name="total_sks"]').val(data.total_sks);
                $('[name="registrasi_alamat"]').val(data.registrasi_alamat);
                $('[name="registrasi_no_hp"]').val(data.registrasi_no_hp);
                $('[name="registrasi_no_hp_ortu"]').val(data.registrasi_no_hp_ortu);
                $('[name="registrasi_nominal_spp"]').val(data.registrasi_nominal_spp);
                $('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);

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
            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('Lecturer_persetujuan_krs/ajax_update') ?>";
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
        $('#form_persetujuan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/view_krs') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                //$('[name="catatan_dosen_wali"]').val(data.catatan_dosen_wali);
                //$('[name="id_status_krs"]').val(data.id_status_krs);
                $('#nm_pd').val(data.nm_pd);
                $('#nipd').val(data.nipd);
                $('#last_ipk').val(data.last_ipk);
                $('#semester_tempuh').val(data.semester_tempuh);
                $('#tabel_krs').html(data.tabel_krs);
                $('#attention').html(data.attention);
                $('#tabel_akademik_record').html(data.tabel_akademik_record);
                $('#log_message').html(data.log_message);
                $('#foto').html(data.pic);
                $('#kip_info').html(data.kip_status);
                $('#risk_info').html(data.risk_status);
                $('#semester_tempuh').val(data.semester_tempuh + " Semester");
                $('#masa_studi').val(data.masa_studi_text);
                $('#catatan_dosen_wali').val('');

                $('#modal_form_krs').modal('show');
                $('.modal-title').text("Kartu Rencana Studi / Card Of Study Plan");

                // Render Chart
                var options = {
                    series: [{
                        name: 'IPS',
                        data: data.ips_values
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false }
                    },
                    colors: ['#2563eb'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        offsetY: -20,
                        style: { fontSize: '12px', colors: ["#304758"] }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        position: 'bottom',
                        labels: { style: { fontSize: '10px' } }
                    },
                    yaxis: {
                        title: { text: 'Indeks Prestasi' },
                        max: 4,
                        tickAmount: 4
                    },
                    title: {
                        text: 'Grafik Perolehan IPS',
                        align: 'center',
                        style: { fontSize: '14px', fontWeight: 'bold' }
                    }
                };

                $('#ips_chart').empty();
                var chart = new ApexCharts(document.querySelector("#ips_chart"), options);
                chart.render();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function show_makul() {
        //$('#modal_makul').modal('show');
        // $('.modal-title').text("Daftar Mata Kuliah Prodi");
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/show_makul_prodi') ?>",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#id_smt').val(data.id_smt);
                $('#tabel_makul').html(data.tabel);
                $('#modal_makul').modal('show');
                $('.modal-title').text("Daftar Mata Kuliah Prodi");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function balas_pesan(id_rencana_studi, nipd, id_smt) {
        $('#form_balas_pesan')[0].reset();
        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rencana_studi"]').val(id_rencana_studi);
                $('#data_pesan').html(data.data_pesan);
                $('#msg_foto').html(data.pic);
                $('#msg_nm_pd').val(data.nm_pd);
                $('#msg_nm_pd').val(data.nm_pd);
                $('#msg_nipd').val(data.nipd);
                $('#msg_last_ipk').val(data.last_ipk);
                $('#msg_semester_tempuh').val(data.semester_tempuh + " Semester");
                $('#msg_masa_studi').val(data.masa_studi_text);
                $('#msg_kip_info').html(data.kip_status);

                $('#modal_form_balas_pesan').modal('show');
                $('.modal-title').text("Kirim Pesan ke Mahasiswa");

                // Scroll to bottom after modal shown
                setTimeout(function() {
                    var d = $('#data_pesan');
                    d.scrollTop(d.prop("scrollHeight"));
                }, 200);

                // Render Chart
                var options = {
                    series: [{
                        name: 'IPS',
                        data: data.ips_values
                    }],
                    chart: {
                        type: 'bar',
                        height: 200,
                        toolbar: { show: false }
                    },
                    colors: ['#0e9447'],
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) { return val.toFixed(2); },
                        style: { fontSize: '10px' }
                    },
                    xaxis: {
                        categories: data.ips_labels,
                        labels: { style: { fontSize: '9px' } }
                    },
                    yaxis: { max: 4, tickAmount: 4 },
                    title: {
                        text: 'Riwayat IPS',
                        align: 'center',
                        style: { fontSize: '12px' }
                    }
                };

                $('#msg_ips_chart').empty();
                var chart = new ApexCharts(document.querySelector("#msg_ips_chart"), options);
                chart.render();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function perbaikan_krs(id) {
        if (confirm('Apakah Anda yakin untuk Perbaikan KRS Mahasiswa ini?')) {
            var obj = {};
            obj['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({

                url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_perbaikan_krs') ?>/" + id,
                type: "GET",
                data: obj,

                success: function(data) {
                    reload_table();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
    }

    function toggle_history(id_rencana_studi, nipd, id_smt) {
        var container = $('#msg_container_' + id_rencana_studi);
        var btn = $('#btn_toggle_' + id_rencana_studi);

        if (container.is(':visible')) {
            container.slideUp();
            btn.html('<i class="fa fa-comments"></i> <small>Lihat History</small>');
            btn.removeClass('red-stripe').addClass('green-stripe');
        } else {
            if (container.html() == '') {
                container.html('<div style="text-align: center; padding: 10px;"><i class="fa fa-spinner fa-spin"></i></div>');
                container.show();
                $.getJSON("<?php echo site_url('Lecturer_persetujuan_krs/view_pesan') ?>/" + id_rencana_studi + "/" + nipd + "/" + id_smt, function(data) {
                    container.hide();
                    container.html(data.data_pesan);
                    container.slideDown();
                    btn.html('<i class="fa fa-eye-slash"></i> <small>Tutup History</small>');
                    btn.removeClass('green-stripe').addClass('red-stripe');
                    
                    // Scroll to bottom
                    container.animate({ scrollTop: container.prop("scrollHeight") }, 500);
                });
            } else {
                container.slideDown();
                btn.html('<i class="fa fa-eye-slash"></i> <small>Tutup History</small>');
                btn.removeClass('green-stripe').addClass('red-stripe');
            }
        }
    }

    function view_foto(id) {
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('Lecturer_persetujuan_krs/ajax_view_foto') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="nipd"]').val(data.nipd);
                $('[name="nm_pd"]').val(data.nm_pd);

                $("#pas_foto").attr("src", "https://files.wbi.ac.id/siakad_file_mahasiswa/" + data.foto);

                $('#modal_foto').modal('show');
                $('.modal-title').text("Pas foto Mahasiswa");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>

<div class="modal fade in" id="modal_form_krs" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div id="foto"></div>

                    </div>
                    <div class="col-md-8">
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
                                        <label class="control-label col-md-3">IPK Terakhir:</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id='last_ipk' readonly style="font-weight: bold; color: #2ecc71;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Status KIP:</label>
                                        <div class="col-md-9" id="kip_info" style="padding-top: 8px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Smt Tempuh:</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id='semester_tempuh' readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Masa Studi:</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id='masa_studi' readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Resiko Lulus:</label>
                                        <div class="col-md-9" id="risk_info" style="padding-top: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div id="ips_chart"></div>
                    </div>
                </div>

                <h4>A. Mata Kuliah Diajukan:</h4>
                <div class="table-responsive" id="tabel_krs"></div>
                <h4>B. Rekam Akademik Mahasiswa:</h4>
                <div class="table-responsive" id="tabel_akademik_record"></div>
                <h4>C. Nilai Mahasiswa Perlu Perhatian:</h4>
                <div class="table-responsive" id="attention"></div>
                <?php
                $attributes = ['class' => 'form', 'id' => 'form_persetujuan'];
                echo form_open('#', $attributes);
                ?>
                <div class="modal-body form">
                    <input type="hidden" value="" name="id_rencana_studi" />
                    <div class="well" id="log_message">
                    </div>

                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label ">Kirim Pesan Sebagai Catatan ke Mahasiswa: </label>
                            <div>
                                <textarea class="form-control" rows="3" required name="catatan_dosen_wali"></textarea>
                                <span class="help-block"></span>
                            </div>
                            <label class="control-label ">Pilih Persetujuan:</label>
                            <div>
                                <?php
                                echo form_dropdown('id_status_krs', $dd_status_krs, '', 'class="form-control" required');
                                ?>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btn_submit', 'Simpan', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_makul" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <div id="tabel_makul"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade in" id="modal_form_balas_pesan" tabindex="-1" role="basic" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_balas_pesan'];
                echo form_open('#', $attributes);
                ?>

                <div class="modal-body form">
                    <input type="hidden" value="" name="id_rencana_studi" />

                    <div class="row">
                        
                        <div class="col-md-5" style="border-right: 1px solid #eee;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div id="msg_foto" style="display: inline-block; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 4px;"></div>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Nama:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_nm_pd" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">NIM:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_nipd" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">IPK Akhir:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_last_ipk" readonly style="font-weight: bold; color: #333;" />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Smt Tempuh:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_semester_tempuh" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Masa Studi:</label>
                                <div class="col-md-8">
                                    <input class="form-control input-sm" id="msg_masa_studi" readonly />
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 5px;">
                                <label class="control-label col-md-4" style="text-align: left;">Status KIP:</label>
                                <div class="col-md-8" id="msg_kip_info" style="padding-top: 7px;"></div>
                            </div>

                            <div id="msg_ips_chart" style="margin-top: 20px;"></div>
                        </div>

                        
                        <div class="col-md-7">
                            <h4 style="margin-top: 0;">Riwayat Pesan:</h4>
                            <div class="well" id="data_pesan" style="height: 300px; overflow-y: auto; background-color: #f9f9f9;">
                            </div>
                            
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label" style="font-weight: bold; margin-bottom: 10px; display: block; text-align: left;">Tulis Pesan Baru:</label>
                                    <div style="padding: 0 15px;">
                                        <textarea class="form-control" rows="4" name="catatan_dosen_wali" placeholder="Ketik pesan untuk mahasiswa di sini..."></textarea>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div style="text-align: right; padding: 0 15px;">
                                    <button type="submit" id="btnSubmitMsg" class="btn btn-primary">
                                        <i class="fa fa-paper-plane"></i> Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_foto" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body ">
                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="nipd" name="nipd">
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group ">
                            <img style="padding: 10px; border: 1px solid;display: block; margin: 0 auto;width: 400px;object-fit: cover;" id="pas_foto" src=""  alt="Card image cap">
                        </div>

                        <div class="form-group ">
                            <label for="nipd" class="col-sm-3 col-form-label">NIM</label>
                            <div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" id="nipd" name="nipd">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="nm_pd" class="col-sm-3 col-form-label">Nama</label>
                            <div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" id="nm_pd" name="nm_pd">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<style>
    /* Fix background scroll when modal open */
    body.modal-open {
        overflow: hidden !important;
        height: 100%;
    }
    
    /* Custom scrollbar for chat history */
    #data_pesan {
        scrollbar-width: thin;
        scrollbar-color: #ccc #f9f9f9;
        display: flex;
        flex-direction: column;
        padding: 15px;
    }
    #data_pesan::-webkit-scrollbar {
        width: 6px;
    }
    #data_pesan::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    
    /* Bubble Chat Styling */
    .chat-bubble {
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 12px;
        max-width: 85%;
        position: relative;
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .chat-right {
        background-color: #dcf8c6;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
        color: #333;
    }
    .chat-left {
        background-color: #ffffff;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        color: #333;
        border: 1px solid #e0e0e0;
    }
    .chat-name {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 3px;
        display: block;
        color: #555;
    }
    
    /* Modal content styling */
    #modal_form_balas_pesan .modal-body {
        padding: 20px;
    }

    /* Small bubble adjustment for datatables */
    .msg-history-container {
        display: flex;
        flex-direction: column;
    }
    .msg-history-container .chat-bubble {
        max-width: 95%;
        font-size: 11px;
        padding: 5px 8px;
        margin-bottom: 8px;
    }
    .msg-history-container .chat-name {
        font-size: 9px;
    }
</style>