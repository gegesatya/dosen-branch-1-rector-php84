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
                "url": "<?php echo site_url('lapkerma/C_moa/ajax_list') ?>",
                "type": "POST",
                "data": {
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
            },

            "columnDefs": [{
                "targets": [-1, 0, 1, 2, 3, 4, 5],
                "orderable": false,
            }, ],

        });





        load_mou();
        load_status();
        load_instansi();
        load_bidang_kerjasama();
        load_departement();
        load_prodi();
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


    function load_status() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_status') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_status;
                $('[name="status_moa"]').html('');
                //opsi_status = `<option value="">Silahkan Pilih</option>`;
                $('[name="status_moa"]').append(opsi_status);
                $.each(categories, function(key, val) {
                    opsi_status = `<option value="${val.id_status_kerjasama}">${val.nm_status_kerjasama}</option>`;
                    $('[name="status_moa"]').append(opsi_status);
                });
            }
        });
    }

    function load_mou() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_mou') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(mou) {
                console.log(mou);
                var opsi_instansi;
                $('[name="id_mou"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_mou"]').append(opsi_instansi);
                $.each(mou, function(key, val) {
                    opsi_instansi = `<option value="${val.id_mou}">(${val.nomor_mou}) ${val.judul_kerjasama}</option>`;
                    $('[name="id_mou"]').append(opsi_instansi);
                });
            }
        });
    }

    function load_bidang_kerjasama() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_bidang_kerjasama') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_bidang_kerjasama;
                $('[name="id_bidang_kerjasama"]').html('');
                opsi_bidang_kerjasama = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_bidang_kerjasama"]').append(opsi_bidang_kerjasama);
                $.each(categories, function(key, val) {
                    opsi_bidang_kerjasama = `<option value="${val.id_bidang_kerjasama}">${val.nm_bidang_kerjasama}</option>`;
                    $('[name="id_bidang_kerjasama"]').append(opsi_bidang_kerjasama);
                });
            }
        });
    }

    function load_instansi() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_instansi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_daftar_instansi"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_daftar_instansi"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_daftar_instansi}">${val.nm_instansi}</option>`;
                    $('[name="id_daftar_instansi"]').append(opsi_instansi);
                });
            }
        });
    }

    function load_departement() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_departement') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_departement"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_departement"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_departement}">${val.nm_departement}</option>`;
                    $('[name="id_departement"]').append(opsi_instansi);
                });
            }
        });
    }

    function load_prodi() {
        $.ajax({
            url: "<?= base_url('lapkerma/C_dropdown/get_prodi') ?>",
            method: 'GET',
            dataType: 'JSON',
            success: function(categories) {
                console.log(categories);
                var opsi_instansi;
                $('[name="id_sms"]').html('');
                opsi_instansi = `<option value="">Silahkan Pilih</option>`;
                $('[name="id_sms"]').append(opsi_instansi);
                $.each(categories, function(key, val) {
                    opsi_instansi = `<option value="${val.id_sms}">${val.nm_lemb}</option>`;
                    $('[name="id_sms"]').append(opsi_instansi);
                });
            }
        });
    }

    function edit_moa(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-danger');
        $('.form-control-feedback').empty();

        $.ajax({
            url: "<?php echo site_url('lapkerma/C_moa/ajax_edit') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="id_moa"]').val(data.id_moa);
                $('[name="id_mou"]').val(data.id_mou);
                $('[name="nomor_dokumen_moa"]').val(data.nomor_dokumen_moa);
                $('[name="judul_moa"]').val(data.judul_moa);
                $('[name="penandatangan_moa"]').val(data.penandatangan_moa);
                $('[name="jabatan_penandatangan_moa"]').val(data.jabatan_penandatangan_moa);
                $('[name="penandatangan_moa_wbi"]').val(data.penandatangan_moa_wbi);
                $('[name="jabatan_penandatangan_moa_wbi"]').val(data.jabatan_penandatangan_moa_wbi);
                $('[name="bidang_kegiatan"]').val(data.bidang_kegiatan);
                $('[name="status_moa"]').val(data.status_moa);
                $('[name="deskripsi_moa"]').val(data.deskripsi_moa);
                $('[name="tanggal_awal_moa"]').val(data.tanggal_awal_moa);
                $('[name="tanggal_akhir_moa"]').val(data.tanggal_akhir_moa);
                $('[name="dokumen_moa"]').val(data.dokumen_moa);
                $('[name="id_bidang_kerjasama"]').val(data.id_bidang_kerjasama);
                $('[name="id_daftar_instansi"]').val(data.id_daftar_instansi);
                $('[name="id_departement"]').val(data.id_departement);
                $('[name="id_sms"]').val(data.id_sms);
                $('[name="jumlah_kapasitas_magang"]').val(data.jumlah_kapasitas_magang);

                $('#modal_form').modal('show');
                $('.modal-title').text("Edit MOA");

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


<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Modal Content is Responsive</h4>
            </div>
            <div class="modal-body">
                <?php
                $attributes = ['class' => 'form-horizontal p-t-10', 'id' => 'form'];
                echo form_open('#', $attributes);
                ?>
                <input type="hidden" id="id_moa" name="id_moa">

                <div class="card-header bg-dark">
                    <h4 class="m-b-0 text-white">Masa Berlaku</h4>
                </div>
                <div class="form-group row">
                    <label for="status_kerjasama" class="col-sm-4 col-form-label">Status MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="status_moa" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tanggal_awal_moa" class="col-sm-4 col-form-label">Tgl Awal MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="tanggal_awal_moa" name="tanggal_awal_moa" data-dtp="dtp_00aWj">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tanggal_akhir_moa" class="col-sm-4 col-form-label">Tgl Akhir MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="tanggal_akhir_moa" name="tanggal_akhir_moa" data-dtp="dtp_00aWj">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi_moa" class="col-sm-4 col-form-label">Deskripsi MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" rows="3" id="deskripsi_moa" name="deskripsi_moa"></textarea>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="id_mou" class="col-sm-4 col-form-label">MOU</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_mou" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nomor_dokumen_moa" class="col-sm-4 col-form-label">Nomor MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="nomor_dokumen_moa" name="nomor_dokumen_moa">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="judul_moa" class="col-sm-4 col-form-label">Judul MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" rows="2" id="judul_moa" name="judul_moa"></textarea>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="id_bidang_kerjasama" class="col-sm-4 col-form-label">Bidang Kerjasama</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_bidang_kerjasama" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="id_daftar_instansi" class="col-sm-4 col-form-label">Instansi Mitra</label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_daftar_instansi" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="id_departement" class="col-sm-4 col-form-label">Departemen <small>yang bekerjasama</small></label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_departement" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="id_sms" class="col-sm-4 col-form-label">PRODI <small>yang bekerjasama</small></label>
                    <div class="col-sm-8 col-md-8">
                        <select name="id_sms" class="form-control">
                            <option value="">-- Pilh --</option>
                        </select>
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="jumlah_kapasitas_magang" class="col-sm-4 col-form-label">Quota Magang</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="jumlah_kapasitas_magang" name="jumlah_kapasitas_magang">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="penandatangan_moa" class="col-sm-4 col-form-label">Penandatangan Mitra</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="penandatangan_moa" name="penandatangan_moa">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="jabatan_penandatangan_moa" class="col-sm-4 col-form-label">Jabatan Mitra</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="jabatan_penandatangan_moa" name="jabatan_penandatangan_moa">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="penandatangan_moa_wbi" class="col-sm-4 col-form-label">Penandatangan Pihak WBI</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="penandatangan_moa_wbi" name="penandatangan_moa_wbi">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="jabatan_penandatangan_moa_wbi" class="col-sm-4 col-form-label">Jabatan Pihak WBI</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="jabatan_penandatangan_moa_wbi" name="jabatan_penandatangan_moa_wbi">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dokumen_moa" class="col-sm-4 col-form-label">Dokumen MOA</label>
                    <div class="col-sm-8 col-md-8">
                        <input type="text" class="form-control" id="dokumen_moa" name="dokumen_moa">
                        <div class="form-control-feedback"></div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>