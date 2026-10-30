<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            //var angkatan = $('#ddAngkatan :selected').val();
            //var kode_kegiatan = '<?= $this->session->userdata('kode_kegiatan'); ?>';

            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_eoffice_handling_time/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [{
                    "targets": [-1, 0, 1, 2, 3, 4],
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

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);

            if (save_method == 'add') {
                url = "<?php echo site_url('C_eoffice_handling_time/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('C_eoffice_handling_time/ajax_update_stempel') ?>";
            }

            $.ajax({
                url: url,
                type: "post",
                dataType: "JSON",
                data: $('#form').serialize(),
                success: function(data) {
                    $('#btnSave').text('Submit');
                    $('#btnSave').attr('disabled', false);

                    if (data.status) {
                        $('#modal_form').modal('hide');
                        reload_table();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').text(data.error_string[i]);
                            $('[name="' + data.inputerror[i] + '"]').addClass('has-error');
                        }
                    }
                }
            });
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

        $('select[name=ddAngkatan]').on('change', function() {
            TableAjax.init();
        });

    });




    function update_finish(id) {
        if (confirm('Apakah Anda ingin mengubah status menjadi SELESAI?')) {
            $.ajax({
                url: "<?php echo site_url('C_eoffice_handling_time/ajax_finish') ?>/" + id,
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

    function update_diambil(id) {
        if (confirm('Apakah Anda ingin mengubah status menjadi SUDAH DIAMBIL?')) {
            $.ajax({
                url: "<?php echo site_url('C_eoffice_handling_time/ajax_diambil') ?>/" + id,
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

    function view_permohonan(id_eoffice) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_eoffice_handling_time/ajax_edit/') ?>/" + id_eoffice,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_eoffice"]').val(data.id_eoffice);
                $('[name="tanggal_surat"]').text(data.tanggal_surat);
                $('[name="nomor_surat"]').text(data.nomor_surat);
                $('[name="nm_kategori_pengantar"]').text(data.nm_kategori_pengantar);
                $('[name="nipd"]').text(data.nipd);
                $('[name="nm_pd"]').text(data.nm_pd);
                $('[name="create_date"]').text(data.create_date);
                $('[name="tmpt_lahir"]').text(data.tmpt_lahir);
                $('[name="tgl_lahir"]').text(data.tgl_lahir);
                $('[name="mhs_alamat"]').html(data.mhs_alamat);
                $('[name="mhs_seluler"]').text(data.mhs_seluler);
                $('[name="mhs_pekerjaan_ortu"]').text(data.mhs_pekerjaan_ortu);
                $('[name="nm_lemb"]').text(data.nm_lemb);
                $('[name="keterangan"]').html(data.keterangan);
                $('[name="magang_instansi"]').text(data.magang_instansi);
                $('[name="magang_alamat"]').html(data.magang_alamat);
                $('[name="magang_no_telepon"]').text(data.magang_no_telepon);
                $('[name="literatur_judul_skripsi"]').text(data.literatur_judul_skripsi);
                $('[name="literatur_instansi"]').text(data.literatur_instansi);
                $('[name="literatur_alamat"]').html(data.literatur_alamat);
                $('[name="academic_record"]').html(data.academic_record);
                $('[name="literatur_no_telepon"]').text(data.literatur_no_telepon);

                var title = '';
                $('#modal_form').modal('show');
                if (data.id_kategori_pengantar == 1) {
                    title = "Konfirmasi Surat Keterangan Mahasiswa Aktif";
                    $('#data_magang').hide();
                } else if (data.id_kategori_pengantar == 2) {
                    title = "Konfirmasi Surat Pengantar Magang/PKL";
                    $('#data_magang').show();
                } else if (data.id_kategori_pengantar == 3) {
                    title = "Konfirmasi Surat Pengantar Penelitian";
                    $('#data_magang').hide();
                } else if (data.id_kategori_pengantar == 4) {
                    title = "Konfirmasi Surat Pengantar Pembukaan Rekening";
                    $('#data_magang').hide();
                }
                $('.modal-title').text(title);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }
</script>


<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" value="" name="id_eoffice" />

                <div class="form-body">
                    <div class="form-group">
                        <div id='halaman' class="col-md-12">
                            <table class='table table-stripped'>
                                <tr>
                                    <td style="width: 150px;">Tgl Surat</td>
                                    <td style="width: 10px;">:</td>
                                    <td style="width: 65%;"><span name="tanggal_surat"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;">No Surat</td>
                                    <td style="width: 10px;">:</td>
                                    <td style="width: 65%;"><span name="nomor_surat"></span></td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;">Kategori</td>
                                    <td style="width: 10px;">:</td>
                                    <td style="width: 65%;"><span name="nm_kategori_pengantar"></span></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Keterangan/Tujuan</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td>
                                        <div name="keterangan"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 150px;">Nama</td>
                                    <td style="width: 10px;">:</td>
                                    <td style="width: 65%;"><span name="nm_pd"></span></td>
                                </tr>
                                <tr>
                                    <td>NIM</td>
                                    <td>:</td>
                                    <td><span name="nipd"></span></td>
                                </tr>
                                <tr>
                                    <td>Tempat Lahir</td>
                                    <td>:</td>
                                    <td><span name="tmpt_lahir"></span></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td>:</td>
                                    <td><span name="tgl_lahir"></span></td>
                                </tr>
                                <tr>
                                    <td>Program Studi</td>
                                    <td>:</td>
                                    <td><span name="nm_lemb"></span></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Alamat Rumah</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td>
                                        <div name="mhs_alamat"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Telepon/HP</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td><span name="mhs_seluler"></span></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Pekerjaan Orang Tua</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td><span name="mhs_pekerjaan_ortu"></span></td>
                                </tr>
                                <tr>
                                    <td>Data Akademik</td>
                                    <td>:</td>
                                    <td>
                                        <div name="academic_record"></div>
                                    </td>
                                </tr>
                            </table>
                            <div id='data_magang'>
                                <table class='table table-stripped'>

                                    <tr>
                                        <td style="width: 150px;">Instansi</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="magang_instansi"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">Alamat</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td>
                                            <div name="magang_alamat"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 150px;">No Telepon</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="magang_no_telepon"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Terbitkan Surat (Ttd dan Stempel)', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>