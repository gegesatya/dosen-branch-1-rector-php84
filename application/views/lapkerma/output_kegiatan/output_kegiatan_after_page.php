<script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/datatables/datatables.min.js"></script>

<script type="text/javascript">
    var save_method;
    var table;

    $(document).ready(function() {
        table = $('#table').DataTable({

            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,

            "ajax": {
                "url": "<?php echo site_url('C_output_kegiatan/ajax_list') ?>",
                "type": "POST",
                "data": {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
            },

            "columnDefs": [{
                "targets": [-1],
                "orderable": false,
            }, ],

        });

        $('#form').submit(function(e) {
            e.preventDefault();
            $('#btnSave').text('Saving...');
            $('#btnSave').attr('disabled', true);
            var method;
            if (save_method == 'add') {
                url = "<?php echo site_url('C_output_kegiatan/ajax_add') ?>";
                method = 'disimpan';
            } else {
                url = "<?php echo site_url('C_output_kegiatan/ajax_update') ?>";
                method = 'diupdate';
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
                        swalert(method);
                        reload_table();
                    } else {
                        $.each(data.errors, function(key, value) {
                            if (value) {
                                $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                                $('[name="' + key + '"]').next().text(value);
                            }
                        });
                        /* for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + key + '"]').parent().parent().addClass('has-danger');
                            $('[name="' + key + '"]').next().text(data.error_string[i]);
                        } */
                    }
                }
            });
        });

        $("input").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });
        $("textarea").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });
        $("select").change(function() {
            $(this).parent().parent().removeClass('has-danger');
            $(this).next().empty();
        });

        $('#tanggal_awal_moa').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false
        });
        $('#tanggal_akhir_moa').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false
        });

        //load_moa();
        load_kategori_output();
        load_kegiatan();
        //load_bidang_kerjasama();
        //load_bidang_usaha();
        //load_bentuk_kegiatan();

    });

    function swalert(method) {
        Swal({
            title: 'Success',
            text: 'Data berhasil ' + method,
            type: 'success'
        });
    };

    function load_kategori_output() {
        $.ajax({
            url: "<?= base_url('C_dropdown/get_kategori_output_kegiatan') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bentuk_kegiatan;
                $('[name="id_kategori_output"]').html('');
                opsi_bentuk_kegiatan = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_kategori_output"]').append(opsi_bentuk_kegiatan);
                $.each(categories, function(key, val) {
                    opsi_bentuk_kegiatan = `<option value="${val.id_kategori_output}">${val.nm_kategori_output}</option>`;
                    $('[name="id_kategori_output"]').append(opsi_bentuk_kegiatan);
                });
            }
        });
    }

    function load_kegiatan() {
        $.ajax({
            url: "<?= base_url('C_dropdown/get_kegiatan') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bentuk_kegiatan;
                $('[name="id_kegiatan"]').html('');
                opsi_bentuk_kegiatan = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_kegiatan"]').append(opsi_bentuk_kegiatan);
                $.each(categories, function(key, val) {
                    opsi_bentuk_kegiatan = `<option value="${val.id_kegiatan}">${val.judul_kegiatan}</option>`;
                    $('[name="id_kegiatan"]').append(opsi_bentuk_kegiatan);
                });
            }
        });
    }

    function add_data_kegiatan() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Data Kegiatan');
    }

    function edit_data_kegiatan(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('C_output_kegiatan/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_output_kegiatan"]').val(data.id_output_kegiatan);
                $('[name="id_kegiatan"]').val(data.id_kegiatan);
                $('[name="id_kategori_output"]').val(data.id_kategori_output);
                $('[name="dokumen_pdf"]').val(data.dokumen_pdf);
                $('[name="judul_penelitian_dosen"]').val(data.judul_penelitian_dosen);
                $('[name="judul_penelitian_mahasiswa"]').val(data.judul_penelitian_mahasiswa);
                $('[name="lingkup_pkl_mahasiswa"]').val(data.lingkup_pkl_mahasiswa);
                $('[name="kontribusi_dosen_wbi"]').val(data.kontribusi_dosen_wbi);
                $('[name="dosen_tamu_nama"]').val(data.dosen_tamu_nama);
                $('[name="dosen_tamu_topik"]').val(data.dosen_tamu_topik);
                $('[name="dosen_undang_nama"]').val(data.dosen_undang_nama);
                $('[name="dosen_undang_topik"]').val(data.dosen_undang_topik);
                $('[name="kurikulum_prodi"]').val(data.kurikulum_prodi);
                $('[name="kurikulum_ruang_lingkup"]').val(data.kurikulum_ruang_lingkup);
                $('[name="penyusunan_modul_prodi"]').val(data.penyusunan_modul_prodi);
                $('[name="penyusunan_modul_mk"]').val(data.penyusunan_modul_mk);
                $('[name="penyusunan_modul_ruang_lingkup"]').val(data.penyusunan_modul_ruang_lingkup);
                $('[name="beasiswa_jumlah"]').val(data.beasiswa_jumlah);
                $('[name="beasiswa_dana"]').val(data.beasiswa_dana);
                $('[name="investor_startup_jumlah"]').val(data.investor_startup_jumlah);
                $('[name="investor_startup_nama_bisnis"]').val(data.investor_startup_nama_bisnis);
                $('[name="investor_startup_jumlah_dana"]').val(data.investor_startup_jumlah_dana);
                $('[name="mbkm_mk"]').val(data.mbkm_mk);
                $('[name="mbkm_pic"]').val(data.mbkm_pic);
                $('[name="kerjasama_bisnis_jenis"]').val(data.kerjasama_bisnis_jenis);
                $('[name="lowongan_pekerjaan_jumlah_mahasiswa"]').val(data.lowongan_pekerjaan_jumlah_mahasiswa);
                $('[name="lowongan_pekerjaan_prodi"]').val(data.lowongan_pekerjaan_prodi);
                $('[name="lowongan_pekerjaan_angkatan"]').val(data.lowongan_pekerjaan_angkatan);


                $('#modal_form').modal('show');
                $('.modal-title').text("Edit Data Kegiatan");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function delete_data_kegiatan(id) {
        Swal({
            title: 'Anda yakin?',
            text: "Data Kegiatan akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus data!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url('C_output_kegiatan/ajax_delete') ?>/" + id,
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


<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal Content is Responsive</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body form">
                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_output_kegiatan" name="id_output_kegiatan">
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="id_kegiatan" class="col-sm-4 col-form-label">Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_kegiatan" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="id_kategori_output" class="col-sm-4 col-form-label">Data Kegiatan</label>
                            <div class="col-sm-8 col-md-8">
                                <select name="id_kategori_output" class="form-control">
                                    <option value="">-- Pilh --</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dokumen_pdf" class="col-sm-4 col-form-label">Dokumen Kegiatan (pdf)</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" placeholder="Cantumkan link dari Google Drive" id="dokumen_pdf" name="dokumen_pdf">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Penelitian Dosen</p>
                        <div class="form-group row">
                            <label for="judul_penelitian_dosen" class="col-sm-4 col-form-label">Judul</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="judul_penelitian_dosen" name="judul_penelitian_dosen">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Penelitian Mahasiswa</p>
                        <div class="form-group row">
                            <label for="judul_penelitian_mahasiswa" class="col-sm-4 col-form-label">Judul</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="judul_penelitian_mahasiswa" name="judul_penelitian_mahasiswa">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Lingkup PKL</p>
                        <div class="form-group row">
                            <label for="lingkup_pkl_mahasiswa" class="col-sm-4 col-form-label">Lingkup PKL Mahasiswa</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="lingkup_pkl_mahasiswa" name="lingkup_pkl_mahasiswa">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Kontribusi Dosen WBI</p>
                        <div class="form-group row">
                            <label for="kontribusi_dosen_wbi" class="col-sm-4 col-form-label">Kontribusi Dosen WBI</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="kontribusi_dosen_wbi" name="kontribusi_dosen_wbi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Dosen Tamu</p>
                        <div class="form-group row">
                            <label for="dosen_tamu_nama" class="col-sm-4 col-form-label">Nama</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="dosen_tamu_nama" name="dosen_tamu_nama">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dosen_tamu_topik" class="col-sm-4 col-form-label">Topik</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="dosen_tamu_topik" name="dosen_tamu_topik">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Dosen Undangan</p>
                        <div class="form-group row">
                            <label for="dosen_undang_nama" class="col-sm-4 col-form-label">Nama</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="dosen_undang_nama" name="dosen_undang_nama">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dosen_undang_topik" class="col-sm-4 col-form-label">Topik</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="dosen_undang_topik" name="dosen_undang_topik">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Penyusunan Kurikulum</p>
                        <div class="form-group row">
                            <label for="kurikulum_prodi" class="col-sm-4 col-form-label">Prodi</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="kurikulum_prodi" name="kurikulum_prodi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="kurikulum_ruang_lingkup" class="col-sm-4 col-form-label">Ruang Lingkup</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="kurikulum_ruang_lingkup" name="kurikulum_ruang_lingkup">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Penyusunan Modul Mata Kuliah</p>
                        <div class="form-group row">
                            <label for="penyusunan_modul_prodi" class="col-sm-4 col-form-label">Prodi</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penyusunan_modul_prodi" name="penyusunan_modul_prodi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="penyusunan_modul_mk" class="col-sm-4 col-form-label">Mata Kuliah</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penyusunan_modul_mk" name="penyusunan_modul_mk">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="penyusunan_modul_ruang_lingkup" class="col-sm-4 col-form-label">Ruang Lingkup</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="penyusunan_modul_ruang_lingkup" name="penyusunan_modul_ruang_lingkup">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold">Beasiswa</p>
                        <div class="form-group row">
                            <label for="beasiswa_jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="beasiswa_jumlah" name="beasiswa_jumlah">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="beasiswa_dana" class="col-sm-4 col-form-label">Dana</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="beasiswa_dana" name="beasiswa_dana">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Investor Startup</p>
                        <div class="form-group row">
                            <label for="investor_startup_jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="investor_startup_jumlah" name="investor_startup_jumlah">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="investor_startup_nama_bisnis" class="col-sm-4 col-form-label">Nama Bisnis</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="investor_startup_nama_bisnis" name="investor_startup_nama_bisnis">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="investor_startup_jumlah_dana" class="col-sm-4 col-form-label">Jumlah Dana</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="investor_startup_jumlah_dana" name="investor_startup_jumlah_dana">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Studi Independen Bersertifikat untuk MBKM</p>
                        <div class="form-group row">
                            <label for="mbkm_mk" class="col-sm-4 col-form-label">Nama Mata Kuliah</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="mbkm_mk" name="mbkm_mk">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mbkm_pic" class="col-sm-4 col-form-label">PIC</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="mbkm_pic" name="mbkm_pic">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Kerjasama Bisnis</p>
                        <div class="form-group row">
                            <label for="kerjasama_bisnis_jenis" class="col-sm-4 col-form-label">Jenis</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="kerjasama_bisnis_jenis" name="kerjasama_bisnis_jenis">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <p class="font-weight-bold"><i class="fa fa-check text-info"></i> Lowongan Pekerjaan</p>
                        <div class="form-group row">
                            <label for="lowongan_pekerjaan_jumlah_mahasiswa" class="col-sm-4 col-form-label">Jumlah Mahasiswa</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="lowongan_pekerjaan_jumlah_mahasiswa" name="lowongan_pekerjaan_jumlah_mahasiswa">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="lowongan_pekerjaan_prodi" class="col-sm-4 col-form-label">Prodi</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="lowongan_pekerjaan_prodi" name="lowongan_pekerjaan_prodi">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="lowongan_pekerjaan_angkatan" class="col-sm-4 col-form-label">Angkatan</label>
                            <div class="col-sm-8 col-md-8">
                                <input type="text" class="form-control" id="lowongan_pekerjaan_angkatan" name="lowongan_pekerjaan_angkatan">
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <?php echo form_submit('btnSave', 'Save', 'class="btn btn-info"'); ?>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>