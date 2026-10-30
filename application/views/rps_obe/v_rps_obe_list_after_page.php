<script type="text/javascript">
var table;

$(document).ready(function() {
    table = $('#datatable_rps_obe').DataTable({
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "order": [],
        "ajax": {
            "url": "<?php echo site_url('C_rps_obe/ajax_list') ?>",
            "type": "POST",
            "data": function(dtRequest) {
                dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
                return dtRequest;
            }
        },
        "columnDefs": [
            {
                "targets": [0, 3],
                "orderable": false
            },
            {
                "targets": [0, 3],
                "className": "text-center"
            }
        ],
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "<div style='padding:20px;text-align:center;color:#64748b;'><i class='fa fa-folder-open-o' style='font-size:32px;display:block;margin-bottom:8px;'></i>Belum ada RPS OBE tersimpan.<br><a href='<?= site_url('C_rps_generator'); ?>' class='btn btn-sm btn-primary' style='margin-top:10px;'><i class='fa fa-magic'></i> Susun RPS Baru</a></div>",
            "info": "Menampilkan _START_ s.d. _END_ dari _TOTAL_ data RPS",
            "infoEmpty": "Tidak ada data",
            "infoFiltered": "(disaring dari _MAX_ total data)",
            "search": "Cari Mata Kuliah / CPMK:",
            "paginate": {
                "first": "Awal",
                "last": "Akhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});

function reload_table() {
    if (table) {
        table.ajax.reload(null, false);
    }
}

function open_rps_view_modal(el) {
    var raw = $(el).attr('data-rps');
    if (!raw) return;
    try {
        var d = JSON.parse(raw);
        $('#modalViewKodeMk').text('[' + (d.kode_mk || 'MK') + ']');
        $('#modalViewNmMk').text(d.nm_mk || '-');
        $('#modalViewSks').html('<i class="fa fa-graduation-cap" style="color:#2563eb;"></i> <strong>' + (d.sks_mk || 3) + ' SKS</strong>');
        $('#modalViewSemester').html('<i class="fa fa-clone" style="color:#64748b;"></i> ' + (d.semester || '-'));
        $('#modalViewStatusBadge').html((d.status_badge || '') + ' ' + (d.obe_badge || ''));
        $('#modalViewDeskripsi').html(d.deskripsi ? d.deskripsi.replace(/\n/g, '<br>') : 'Tidak ada ringkasan deskripsi capaian pembelajaran.');
        
        $('#btnDocRpsFull').attr('href', d.url_rps);
        $('#btnDocKontrak').attr('href', d.url_kontrak);
        $('#btnDocTugas').attr('href', d.url_tugas);
        $('#btnDocSkenario').attr('href', d.url_skenario || '#');
        $('#btnDocRoadmapPreview').attr('href', d.url_roadmap_preview || d.url_rps);
        $('#btnDocExcel').attr('href', d.url_excel);
        $('#btnModalStudioRps').attr('href', d.url_studio);

        $('#modalRpsView').modal('show');
    } catch(e) {
        console.error('Error opening RPS view modal:', e);
    }
}

function delete_rps_obe_btn(el) {
    var id = $(el).attr('data-id');
    var nama_mk = $(el).attr('data-name') || 'Mata Kuliah';
    if (!id) return;
    delete_rps_obe(id, nama_mk);
}

function delete_rps_obe(id, nama_mk) {
    if (!id) return;
    nama_mk = nama_mk || 'Mata Kuliah';
    
    var promptMsg = 'Apakah Anda yakin ingin menghapus data RPS untuk mata kuliah "' + nama_mk + '"?\n\nTindakan ini tidak dapat dibatalkan.';

    if (confirm(promptMsg)) {
        do_delete_rps(id);
    }
}

function do_delete_rps(id) {
    if (!id) return;

    var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var postData = { id: id };
    postData[csrfTokenName] = csrfHash;

    $.ajax({
        url: "<?php echo site_url('C_rps_obe/ajax_delete'); ?>/" + id,
        type: "POST",
        dataType: "JSON",
        data: postData,
        success: function(data) {
            if (data && data.status) {
                reload_table();
                alert(data.pesan || 'Data RPS berhasil dihapus.');
            } else {
                var msg = (data && data.pesan) ? data.pesan : 'Gagal menghapus data RPS.';
                alert('Gagal: ' + msg);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            var errMsg = 'Terjadi kesalahan saat menghubungi server (' + (jqXHR.status ? 'HTTP ' + jqXHR.status : textStatus) + ').';
            if (jqXHR.responseJSON && jqXHR.responseJSON.pesan) {
                errMsg = jqXHR.responseJSON.pesan;
            }
            alert(errMsg);
        }
    });
}
</script>
