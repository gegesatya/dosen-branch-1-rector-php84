<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    var save_method;
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            var hasil = $('#ddAngkatan :selected').val();
            //var kode_kegiatan = '<?= $this->session->userdata('kode_kegiatan'); ?>';

            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "destroy": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('C_konfirmasi_magang/ajax_list') ?>" + "/" + hasil,
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
                url = "<?php echo site_url('C_konfirmasi_magang/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('C_konfirmasi_magang/ajax_update_stempel') ?>";
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
                        // for (var i = 0; i < data.inputerror.length; i++) {
                        //     $('[name="' + data.inputerror[i] + '"]').text(data.error_string[i]);
                        //     $('[name="' + data.inputerror[i] + '"]').addClass('has-error');
                        // }

                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
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

        $('select[name=ddAngkatan]').on('change', function() {
            TableAjax.init();
        });

    });




    function update_finish(id) {
        if (confirm('Apakah Anda ingin mengubah status menjadi SELESAI?')) {
            $.ajax({
                url: "<?php echo site_url('C_konfirmasi_magang/ajax_finish') ?>/" + id,
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
                url: "<?php echo site_url('C_konfirmasi_magang/ajax_diambil') ?>/" + id,
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

    function view_permohonan(id_daftar_magang) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('C_konfirmasi_magang/ajax_edit') ?>/" + id_daftar_magang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_daftar_magang"]').val(data.id_daftar_magang);
                $('[name="nm_instansi"]').text(data.nm_instansi);
                $('[name="alamat_instansi"]').text(data.alamat_instansi);
                $('[name="telepon"]').text(data.telepon);
                $('[name="ipk_minimal"]').text(data.ipk_minimal);
                $('[name="job_title"]').text(data.job_title);
                $('[name="kualifikasi"]').html(data.kualifikasi);
                $('[name="fasilitas"]').html(data.fasilitas);
                $('[name="nm_pd"]').text(data.nm_pd);
                $('[name="nipd"]').text(data.nipd);
                $('[name="ipk"]').text(data.ipk);
                $('[name="tmpt_lahir"]').text(data.tmpt_lahir);
                $('[name="tgl_lahir"]').text(data.tgl_lahir);
                $('[name="nm_lemb"]').text(data.nm_lemb);
                $('[name="mhs_alamat"]').html(data.jln);
                $('[name="mhs_seluler"]').text(data.telepon_seluler);
                $('[name="tgl_awal_magang"]').val(data.tgl_awal_magang);
                $('[name="tgl_akhir_magang"]').val(data.tgl_akhir_magang);

                var title = '';
                $('#modal_form').modal('show');

                title = "Konfirmasi Magang";
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

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function delete_konfirmasi_magang(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data Konfirmasi Magang akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('C_konfirmasi_magang/ajax_delete') ?>/" + id,
                    type: "DELETE",
                    dataType: "json",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                    success: function(data) {
                        reload_table();
                        swalert('dihapus');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error deleting data');
                    }
                });
            }
        });
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
                <input type="hidden" value="" name="id_daftar_magang" />

                <div class="form-body">
                    <div class="form-group">

                        <div id='halaman' class="col-md-12">
                            <div class="alert alert-info">
                                <table class='table table-stripped'>
                                    <tr>
                                        <th colspan="3">Instansi Tujuan Magang</th>
                                    </tr>
                                    <tr>
                                        <td style="width: 150px;">Instansi</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="nm_instansi"></span></td>
                                    </tr>
                                    
                                    <tr>
                                        <td style="width: 150px;">Job Title</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="job_title"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 150px;">IPK Minimal</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="ipk_minimal"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 150px;">Kualifikasi</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="kualifikasi"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 150px;">Fasilitas</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="width: 65%;"><span name="fasilitas"></span></td>
                                    </tr>
                                </table>
                            </div>
                            <table class='table table-stripped'>
                                
                                <tr>
                                    <th colspan="3">Pendaftar</th>
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
                                    <td>IPK Mahasiswa</td>
                                    <td>:</td>
                                    <td><span name="ipk"></span></td>
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
                                <tr id='telepon'>
                                    <td style="vertical-align: top;">Telepon/HP</td>
                                    <td style="vertical-align: top;">:</td>
                                    <td><span name="mhs_seluler"></span></td>
                                </tr>
                            </table>

                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tgl Mulai Magang</label>
                        <div class="col-md-8">
                            <input type="text" name="tgl_awal_magang" class="form-control datepicker" placeholder="Tgl Mulai Magang">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tgl Akhir Magang</label>
                        <div class="col-md-8">
                            <input type="text" name="tgl_akhir_magang" class="form-control datepicker" placeholder="Tgl akhir Magang">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Tipe Surat</label>
                        <div class="col-md-8">
                            <select name="tipe_surat_tugas" class="form-control" required>
                                <option value="pkl">PKL/Magang</option>
                                <option value="mbkm">MBKM</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Dosen Pembimbing Lapangan (DPL)</label>
                        <div class="col-md-8">
                            <?php
                            echo form_dropdown('dpl', $dd_dpl, '', 'class="form-control" required');
                            ?>
                            <span class="help-block"></span>
                            <span class="text-danger">* Setelah permohonan di approve, maka tanggal dan nomor Surat Tugas akan dikeluarkan oleh Bagian SSC</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Approve', 'class="btn btn-primary"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>