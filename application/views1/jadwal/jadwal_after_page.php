<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_jadwal/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4, 5],
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
        // Prevent scroll propagation on modal scrollers
        $('#modal_krs_notification .scroller').on('mousewheel DOMMouseScroll', function(e) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;

            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "auto",
            todayBtn: true,
            todayHighlight: true,
            forceParse: false
        });

        $('#form_copy').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
            $.ajax({
                url: '<?php echo site_url('C_jadwal/ajax_generate_bap/') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_copy').serialize(),
                success: function(data) {
                    $('#btnSave').text('Save');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_copy').modal('hide');
                        reload_table_ajax();
                    } else {

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });

        $('#form_rps').submit(function(e) {
            e.preventDefault();
            $('#btnSaveRps').text('Saving...');
            $('#btnSaveRps').attr('disabled', true);
            $.ajax({
                url: '<?php echo site_url('C_jadwal/ajax_save_rps/') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_rps').serialize(),
                success: function(data) {
                    $('#btnSaveRps').text('Save');
                    $('#btnSaveRps').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_rps').modal('hide');
                        reload_table_ajax();
                    } else {

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });

        $('#form_skema_nilai').submit(function(e) {
            e.preventDefault();
            $('#btnSkema').text('Saving...');
            $('#btnSkema').attr('disabled', true);


            $.ajax({
                url: '<?php echo site_url('C_skema_nilai/ajax_update_skema_nilai') ?>',
                type: "post",
                dataType: "JSON",
                data: $('#form_skema_nilai').serialize(),
                success: function(data) {
                    $('#btnSkema').text('Save');
                    $('#btnSkema').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form_skema_nilai').modal('hide');
                        reload_table_ajax();
                    } else {

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                    }
                }
            });
        });

        <?php if (!empty($krs_stats['need_approval']) || !empty($krs_stats['draft'])) : ?>
            $('#modal_krs_notification').modal('show');
        <?php endif; ?>
    });

    function reload_table_ajax() {
        table.ajax.reload(null, false);
    }



    function ajax_copy(id_rps, id_jadwal) {
        $('#form_copy')[0].reset();
        $('.form-group').removeClass('has-error');

        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_copy_data') ?>" + "/" + id_rps,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_rps"]').val(id_rps);
                $('[name="id_jadwal"]').val(id_jadwal);
                $('[name="general_mk"]').val(data.nm_mk);
                $('[name="tanggal_rps"]').val(data.tanggal_rps);
                $('[name="kode_mk"]').val(data.kode_mk);
                $('[name="nm_mk"]').val(data.nm_mk);
                $('[name="dosen_pengampu"]').val(data.dosen_pengampu);
                $('[name="nm_ptk"]').val(data.nm_ptk);
                $('[name="jumlah_detail_pertemuan"]').val(data.jumlah_detail_pertemuan);

                $('#modal_form_copy').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Generate Meeting");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function ajax_pilih_rps(id_jadwal) {
        $('#form_rps')[0].reset();
        $('.form-group').removeClass('has-error');

        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_rps') ?>" + "/" + id_jadwal,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="nama_hari"]').val(data.nama_hari);
                $('[name="tag_name"]').val(data.tag_name);
                $('[name="jam"]').val(data.jam);
                $('[name="matkul"]').html(data.matkul);
                $('[name="id_jadwal"]').val(id_jadwal);

                $('#modal_form_rps').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Pilih RPS");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function ajax_download(id_smt) {
        $.ajax({
            url: "<?php echo site_url('C_jadwal/get_journey_by_smt') ?>" + "/" + id_smt,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#tabel').html(data.tabel);

                $('#modal_form_download').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('.modal-title').text("Perkuliahan Tahun " + id_smt);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function delete_rps(id_jadwal) {
        if (confirm('Anda akan melakukan reset RPS, data pertemuan dan absen yang sudah di generate juga akan dihapus. Apakah Anda ingin melanjutkan??')) {
            $.ajax({
                url: "<?php echo site_url('C_jadwal/ajax_delete_rps') ?>/" + id_jadwal,
                type: "GET",
                dataType: "JSON",
                data: function(dtRequest) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                    return dtRequest;
                },
                success: function(data) {
                    reload_table_ajax();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
    }

    $(function() {
        $(".form-control").change(function() {
            $(this).parent().parent().removeClass('has-error');
            if ($(this).next().hasClass("help-block")) {
                $(this).next().empty();
            } else {
                $(this).parent().next().empty();
            }
        });

        $(".komponen-reguler").on('input', function(e) {
            var total = Number($('[name="sikap"]').val()) +
                Number($('[name="quiz"]').val()) +
                Number($('[name="tugas_terstruktur"]').val()) +
                Number($('[name="uts"]').val()) +
                Number($('[name="uas"]').val()) +
                Number($('[name="unjuk_kinerja"]').val()) +
                Number($('[name="proyek"]').val()) +
                Number($('[name="portofolio"]').val()) +
                Number($('[name="produk"]').val()) +
                Number($('[name="presentasi"]').val());
            $('#jumlah_total').text(total);
        });
    });

    function edit_skema_nilai(id) {
        save_method = 'update_skema_nilai';
        $('#form_skema_nilai')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#btnSave').text('Simpan');
        $('#btnSave').attr('disabled', false);

        $.ajax({
            url: "<?php echo site_url('C_skema_nilai/ajax_edit/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="tag_name"]').val(data.tag_name);
                $('[name="sikap"]').val(data.sikap);
                $('[name="quiz"]').val(data.quiz);
                $('[name="tugas_terstruktur"]').val(data.tugas_terstruktur);
                $('[name="uts"]').val(data.uts);
                $('[name="uas"]').val(data.uas);
                $('[name="unjuk_kinerja"]').val(data.unjuk_kinerja);
                $('[name="proyek"]').val(data.proyek);
                $('[name="portofolio"]').val(data.portofolio);
                $('[name="produk"]').val(data.produk);
                $('[name="presentasi"]').val(data.presentasi);
                $('#jumlah_total').text(data.total);


                $('#modal_form_skema_nilai').modal('show');
                $('.modal-title').text("Edit Skema Nilai " + data.tag_name);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>


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
                    <input type="hidden" name="id_rps" />
                    <input type="hidden" name="id_jadwal" />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tanggal RPS</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='tanggal_rps' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Koordinator RPS</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='nm_ptk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Dosen Pengampu</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='dosen_pengampu' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nama RPS</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='general_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Kode MK</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='kode_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Nama MK</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='nm_mk' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Jumlah Detail Pertemuan</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='jumlah_detail_pertemuan' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Tanggal Mulai Kuliah</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control datepicker" name="tanggal_mulai" required>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_rps" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_rps'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    
                    <input type="hidden" name="id_jadwal" />
                    <div class="form-group">
                        <label class="col-md-4 control-label">Hari/Jam</label>
                        <div class="col-md-4">
                            <input type="text" class='form-control' name='nama_hari' readonly />
                        </div>
                        <div class="col-md-4">
                            <input type="text" class='form-control' name='jam' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Jadwal</label>
                        <div class="col-md-8">
                            <input type="text" class='form-control' name='tag_name' readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Mata Kuliah</label>
                        <div class="col-md-8">
                            <div class='well' name='matkul'></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Pilih RPS</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('ddRps', $dd_rps, '', 'class="form-control input-sm" id="ddRps"');
                            ?>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btnSaveRps" class="btn btn-primary">Save</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_download" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_download'];
                echo form_open('#', $attributes);
                ?>
                <div class="form-body">
                    <div id="tabel"></div>
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

<div class="modal fade" id="modal_form_skema_nilai" role="dialog">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form_skema_nilai'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id" />
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Nama Jadwal</label>
                        <div class="col-md-9">
                            <input class="form-control input-sm" name="tag_name" type="text" value="" readonly />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Sikap (%)</label>
                        <div class="col-md-4">
                            <input name="sikap" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Quiz (%)</label>
                        <div class="col-md-3">
                            <input name="quiz" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                        <label class="control-label col-md-3">Tugas (%)</label>
                        <div class="col-md-3">
                            <input name="tugas_terstruktur" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">UTS (%)</label>
                        <div class="col-md-3">
                            <input name="uts" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                        <label class="control-label col-md-3">UAS (%)</label>
                        <div class="col-md-3">
                            <input name="uas" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-md-3">Unjuk Kinerja (%)</label>
                        <div class="col-md-3">
                            <input name="unjuk_kinerja" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                        <label class="control-label col-md-3">Proyek (%)</label>
                        <div class="col-md-3">
                            <input name="proyek" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Portofolio (%)</label>
                        <div class="col-md-3">
                            <input name="portofolio" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                        <label class="control-label col-md-3">Produk (%)</label>
                        <div class="col-md-3">
                            <input name="produk" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Presentasi (%)</label>
                        <div class="col-md-3">
                            <input name="presentasi" class="form-control input-sm komponen-reguler" type="number">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3"></label>
                        <div class="col-md-9">
                            <div class="alert alert-info text-center" id='jumlah_total' name='jumlah_total'></div>
                            <span class="help-block"></span>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" id="btnSkema" class="btn btn-primary">Save</button>
            </div>

            <?php
            echo form_close();
            ?>


        </div>
</div>
</div>

<style>
    /* Fix background scroll when modal is open */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }
    
    #modal_krs_notification .scroller {
        padding-right: 10px !important;
    }
</style>

<div class="modal fade" id="modal_krs_notification" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue-hoki font-white">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" style="color:white !important"><i class="fa fa-info-circle"></i> Dashboard Monitoring KRS Mahasiswa</h3>
            </div>
            <div class="modal-body" style="background: #f4f6f9;">
                
                <div class="row margin-bottom-20">
                    <div class="col-md-4">
                        <div class="dashboard-stat blue-madison">
                            <div class="visual"><i class="fa fa-check-square-o"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['need_approval']); ?></div>
                                <div class="desc">Butuh Approval</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-stat yellow-gold">
                            <div class="visual"><i class="fa fa-pencil"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['draft']); ?></div>
                                <div class="desc">Sedang Daftar</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dashboard-stat red-intense">
                            <div class="visual"><i class="fa fa-times-circle"></i></div>
                            <div class="details">
                                <div class="number"><?= count($krs_stats['belum_krs']); ?></div>
                                <div class="desc">Belum KRS</div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab_approval" data-toggle="tab">Butuh Approval <span class="badge badge-danger"><?= count($krs_stats['need_approval']); ?></span></a>
                        </li>
                        <li>
                            <a href="#tab_draft" data-toggle="tab">Sedang Daftar <span class="badge badge-warning"><?= count($krs_stats['draft']); ?></span></a>
                        </li>
                        <li>
                            <a href="#tab_belum" data-toggle="tab">Belum KRS <span class="badge badge-default"><?= count($krs_stats['belum_krs']); ?></span></a>
                        </li>
                    </ul>
                    <div class="tab-content" style="background: white; padding: 15px; border: 1px solid #ddd; border-top: none;">
                        
                        <div class="tab-pane active" id="tab_approval">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['need_approval'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Tidak ada mahasiswa yang butuh approval.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['need_approval'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tab_draft">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['draft'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Tidak ada mahasiswa yang sedang daftar.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['draft'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tab_belum">
                            <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="1">
                                <?php if (empty($krs_stats['belum_krs'])) : ?>
                                    <div class="text-center padding-tb-20 text-muted">Semua mahasiswa sudah KRS.</div>
                                <?php else : ?>
                                    <ul class="list-group">
                                        <?php foreach ($krs_stats['belum_krs'] as $row) : ?>
                                            <li class="list-group-item" style="display: flex; align-items: center; gap: 15px;">
                                                <img src="<?= !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . $row->foto : base_url('assets/images.png'); ?>" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                                <div>
                                                    <div class="bold"><?= $row->nm_pd; ?></div>
                                                    <div class="small text-muted"><?= $row->nipd; ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= site_url('Lecturer_persetujuan_krs'); ?>" class="btn btn-primary btn-block">Kelola Approval KRS</a>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>