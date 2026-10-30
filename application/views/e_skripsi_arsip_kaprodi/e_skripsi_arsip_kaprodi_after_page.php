<script type="text/javascript">
    var table;

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?php echo site_url('e_skripsi_arsip_kaprodi/ajax_list') ?>",
                "type": "POST",
                "data": function ( dtRequest ) {
                    dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                }
            },
            "columnDefs": [
                {
                    "targets": [ 0, -1 ],
                    "orderable": false,
                },
            ],
            // Inisialisasi select2 jika ada pluginnya
            "drawCallback": function(settings) {
                if($.fn.select2) {
                    $('[name="dosen_pembimbing_1"]').select2({
                        dropdownParent: $('#modal_perpanjang')
                    });
                }
            }
        });
    });

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function perpanjang_sk(id_judul, current_pembimbing) {
        $('#form_perpanjang')[0].reset();
        $('[name="id_judul_skripsi"]').val(id_judul);
        $('[name="dosen_pembimbing_1"]').val(current_pembimbing);
        
        // Trigger change untuk select2
        if($.fn.select2) {
            $('[name="dosen_pembimbing_1"]').trigger('change');
        }

        $('#info_pencapaian_container').show();
        $('#info_pencapaian').html('Memuat data pencapaian...');

        $.ajax({
            url: "<?php echo site_url('e_skripsi_arsip_kaprodi/ajax_get_achievement/') ?>" + id_judul,
            type: "GET",
            dataType: "JSON",
            success: function(response) {
                if (response.status && response.data) {
                    var res = response.data;
                    
                    var html = '<div style="margin-bottom:12px; padding-bottom:10px; border-bottom:1px dashed #ccc;">';
                    html += '<b style="color:#1A365D; font-size:14px;">' + (res.nm_pd || '-') + ' (' + (res.nipd || '-') + ')</b><br>';
                    html += '<i style="color:#555;">"' + (res.judul_ditetapkan || '-') + '"</i>';
                    html += '</div>';

                    html += '<ul style="margin:0; padding-left: 15px;">';
                    var sempro = res.sempro_tgl ? '<b>' + res.sempro_tgl + '</b> (Hasil: ' + (res.sempro_hasil || '-') + ')' : '<i style="color:#777">Belum terlaksana</i>';
                    html += '<li style="margin-bottom:3px;">Seminar Proposal: ' + sempro + '</li>';
                    
                    var semhas = res.semhas_tgl ? '<b>' + res.semhas_tgl + '</b> (Hasil: ' + (res.semhas_hasil || '-') + ')' : '<i style="color:#777">Belum terlaksana</i>';
                    html += '<li style="margin-bottom:3px;">Seminar Hasil: ' + semhas + '</li>';
                    
                    var sidang = res.sidang_tgl ? '<b>' + res.sidang_tgl + '</b> (Hasil: ' + (res.sidang_hasil || '-') + ')' : '<i style="color:#777">Belum terlaksana</i>';
                    html += '<li style="margin-bottom:3px;">Sidang Meja Hijau: ' + sidang + '</li>';
                    
                    if (res.penguji_1 || res.penguji_2 || res.penguji_3) {
                        html += '<li style="margin-top:8px; margin-bottom:3px; list-style:none;"><b>Dewan Penguji Terdaftar:</b></li>';
                        if (res.penguji_1) html += '<li style="list-style:circle; margin-left: 10px;">Penguji 1: ' + res.penguji_1 + '</li>';
                        if (res.penguji_2) html += '<li style="list-style:circle; margin-left: 10px;">Penguji 2: ' + res.penguji_2 + '</li>';
                        if (res.penguji_3) html += '<li style="list-style:circle; margin-left: 10px;">Penguji 3: ' + res.penguji_3 + '</li>';
                    }
                    
                    html += '</ul>';
                    $('#info_pencapaian').html(html);
                } else {
                    $('#info_pencapaian').html('<i style="color:#777">Belum ada data tahapan seminar/sidang yang tercatat di sistem.</i>');
                }
            },
            error: function() {
                $('#info_pencapaian').html('<span class="text-danger">Gagal memuat data pencapaian.</span>');
            }
        });

        $('#modal_perpanjang').modal('show');
    }

    function save_perpanjang() {
        $('#btnSave').text('Menyimpan...');
        $('#btnSave').attr('disabled', true);

        $.ajax({
            url: "<?php echo site_url('e_skripsi_arsip_kaprodi/ajax_perpanjang') ?>",
            type: "POST",
            data: $('#form_perpanjang').serialize(),
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#modal_perpanjang').modal('hide');
                    reload_table();
                    alert("SK berhasil diperpanjang. Status mahasiswa kembali Aktif.");
                } else {
                    alert(data.pesan || "Terjadi kesalahan.");
                }
                $('#btnSave').text('Simpan & Perpanjang');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error memperbarui data');
                $('#btnSave').text('Simpan & Perpanjang');
                $('#btnSave').attr('disabled', false);
            }
        });
    }
</script>
