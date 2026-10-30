<script type="text/javascript">
  var table;

  $(document).ready(function() {
    // Inisialisasi DataTable
    table = $('#tbl_nilai_berita_acara').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "pageLength": 25,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
      "ajax": {
        "url": "<?= site_url('E_skripsi_nilai_berita_acara/ajax_list') ?>",
        "type": "POST",
        "data": function(d) {
          d['<?= $this->security->get_csrf_token_name(); ?>'] = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columnDefs": [
        {
          "targets": [0],
          "orderable": false,
          "className": "text-center"
        },
        {
          "targets": [1],
          "className": "text-center"
        },
        {
          "targets": [3, 4, 5],
          "className": "text-center"
        }
      ],
      "language": {
        "processing": "<i class='fa fa-spinner fa-spin'></i> Memuat data...",
        "lengthMenu": "Tampilkan _MENU_ baris",
        "zeroRecords": "Tidak ada data mahasiswa bimbingan/ujian aktif ditemukan",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ mahasiswa",
        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 mahasiswa",
        "infoFiltered": "(disaring dari _MAX_ total data)",
        "search": "Cari (NIM / Nama / Judul):",
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
    table.ajax.reload(null, false);
  }

  function open_modal_detail(id_judul, default_tab) {
    if (!default_tab) default_tab = 'sempro';

    // Post data with CSRF
    var postData = {};
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = '<?= $this->security->get_csrf_hash(); ?>';

    $.ajax({
      url: "<?= site_url('E_skripsi_nilai_berita_acara/ajax_detail/') ?>/" + id_judul,
      type: "POST",
      data: postData,
      dataType: "JSON",
      beforeSend: function() {
        $('#modal_student_avatar').text('..');
        $('#modal_student_nim').text('Memuat...');
        $('#modal_student_name').text('');
        $('#modal_student_title').text('Sedang memuat rincian nilai...');
      },
      success: function(res) {
        if (!res.status) {
          alert(res.message || 'Gagal mengambil data.');
          return;
        }

        var stu = res.student;
        var sempro = res.sempro;
        var semhas = res.semhas;
        var sidang = res.sidang;

        // 1. Header Mahasiswa
        $('#modal_student_avatar').text(stu.initials || 'TA');
        $('#modal_student_nim').text(stu.nipd);
        $('#modal_student_name').text(stu.nm_pd);
        $('#modal_student_title').text(stu.judul_ta || 'Judul belum ditentukan');

        // 2. Tab Badges
        $('#badge_tab_sempro').text(sempro.rerata > 0 ? sempro.rerata : '-');
        $('#badge_tab_semhas').text(semhas.rerata > 0 ? semhas.rerata : '-');
        $('#badge_tab_sidang').text(sidang.rerata > 0 ? sidang.rerata : '-');

        // 3. Render SEMPRO Tab
        render_sempro_tab(sempro, stu.hash_id);

        // 4. Render SEMHAS Tab
        render_semhas_tab(semhas, stu.hash_id);

        // 5. Render SIDANG Tab
        render_sidang_tab(sidang, stu.hash_id);

        // 6. Switch to requested tab
        if (default_tab === 'semhas') {
          $('#tab_btn_semhas').tab('show');
        } else if (default_tab === 'sidang') {
          $('#tab_btn_sidang').tab('show');
        } else {
          $('#tab_btn_sempro').tab('show');
        }

        $('#modal_detail_nilai').modal('show');
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan saat memuat data nilai.');
      }
    });
  }

  function render_sempro_tab(data, hash_id) {
    // Ringkasan
    $('#sempro_stat_rerata').text(data.rerata > 0 ? data.rerata : '-');
    
    var hasil_txt = data.hasil || '-';
    var is_layak = hasil_txt.toLowerCase().indexOf('layak') !== -1 && hasil_txt.toLowerCase().indexOf('tidak') === -1;
    var badge_bg = (hasil_txt !== '-' && is_layak) ? '#10b981' : (hasil_txt !== '-' ? '#ef4444' : '#94a3b8');
    var badge_icon = is_layak ? 'fa-check-circle' : 'fa-times-circle';
    $('#sempro_stat_hasil').html('<span style="background-color: ' + badge_bg + '; color: #ffffff; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-block;"><i class="fa ' + badge_icon + '"></i> ' + hasil_txt + '</span>');

    var jadwal_str = data.tanggal ? (data.tanggal + (data.jam ? ' (' + data.jam + ' WIB)' : '')) : '-';
    if (data.tempat && data.tempat !== '-') jadwal_str += ' &bull; ' + data.tempat;
    $('#sempro_stat_jadwal').html(jadwal_str);

    if (data.link_dokumen) {
      $('#sempro_stat_dokumen').html('<a href="' + data.link_dokumen + '" target="_blank" class="btn btn-default btn-xs" style="font-size: 11px; border: 1px solid #cbd5e1; width: 100%;"><i class="fa fa-file-text-o text-primary"></i> Buka Berkas Proposal</a>');
    } else {
      $('#sempro_stat_dokumen').html('');
    }
    $('#sempro_stat_ba').html('<a href="<?= site_url('E_skripsi_nilai_sempro/cetak_pdf/') ?>/' + hash_id + '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; width: 100%;"><i class="fa fa-file-pdf-o"></i> Unduh Berita Acara</a>');

    // Tabel Penguji
    var tbody = '';
    var list = data.penguji_data || [];
    if (list.length > 0) {
      var no = 1;
      list.forEach(function(item) {
        tbody += '<tr>' +
          '<td class="text-center" style="width: 35px;">' + (no++) + '</td>' +
          '<td>' +
            '<strong>' + (item.nama_lengkap || '-') + '</strong>' +
            '<div style="font-size: 11px; color: #64748b;">' + item.role_dosen + '</div>' +
          '</td>' +
          '<td class="text-center">' + (item.nilai_a || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_b || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_c || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_d || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_e || 0) + '</td>' +
          '<td class="text-center" style="font-weight: 700; font-size: 13px; color: #0f766e;">' + (item.nilai_akhir || 0) + '</td>' +
        '</tr>';
      });
    } else {
      tbody = '<tr><td colspan="8" class="text-center text-muted" style="padding: 16px;">Belum ada data nilai penguji Sempro.</td></tr>';
    }
    $('#sempro_table_body').html(tbody);
    $('#sempro_table_rerata_val').text(data.rerata > 0 ? data.rerata : '0');

    // Catatan Revisi
    var catatan_html = '';
    var kriteria_sempro = [
      { key: 'catatan_judul', label: 'Judul' },
      { key: 'catatan_latar_belakang', label: 'Latar Belakang' },
      { key: 'catatan_rumusan_masalah', label: 'Rumusan Masalah' },
      { key: 'catatan_tujuan_penelitian', label: 'Tujuan / Manfaat' },
      { key: 'catatan_tinjauan_pustaka', label: 'Tinjauan Pustaka' },
      { key: 'catatan_metode_penelitian', label: 'Metode Penelitian' },
      { key: 'catatan_daftar_pustaka', label: 'Daftar Pustaka' },
      { key: 'catatan_format_penulisan', label: 'Format Penulisan' }
    ];

    if (list.length > 0) {
      list.forEach(function(item) {
        var notes_content = '';
        var count = 0;
        kriteria_sempro.forEach(function(k) {
          var val = item[k.key];
          if (val && val.trim() !== '' && val !== '<p><br></p>') {
            count++;
            notes_content += '<div class="sempro-note-item">' +
              '<div class="sempro-note-label">' + k.label + ':</div>' +
              '<div style="font-size: 12px; line-height: 1.45;">' + val + '</div>' +
            '</div>';
          }
        });

        catatan_html += '<div class="sempro-note-card">' +
          '<div class="sempro-note-header">' +
            '<div><strong>' + (item.nama_lengkap || '-') + '</strong> <span class="text-muted">(' + item.role_dosen + ')</span></div>' +
            '<div><span class="label label-default" style="font-size: 10.5px; background-color: #e2e8f0; color: #334155; font-weight: 600;">Nilai: ' + (item.nilai_akhir || 0) + '</span></div>' +
          '</div>' +
          '<div class="sempro-note-body">' +
            (count > 0 ? notes_content : '<span class="text-muted" style="font-style: italic; font-size: 11px;">Tidak ada catatan usulan revisi dari penguji ini.</span>') +
          '</div>' +
        '</div>';
      });
    } else {
      catatan_html = '<p class="text-muted" style="font-style: italic; font-size: 12px; text-align: center; padding: 15px;">Belum ada catatan usulan revisi Seminar Proposal.</p>';
    }
    $('#sempro_catatan_container').html(catatan_html);
  }

  function render_semhas_tab(data, hash_id) {
    // Ringkasan
    $('#semhas_stat_rerata').text(data.rerata > 0 ? data.rerata : '-');
    
    var hasil_txt = data.hasil || '-';
    var is_layak = hasil_txt.toLowerCase().indexOf('layak') !== -1 && hasil_txt.toLowerCase().indexOf('tidak') === -1;
    var badge_bg = (hasil_txt !== '-' && is_layak) ? '#10b981' : (hasil_txt !== '-' ? '#ef4444' : '#94a3b8');
    var badge_icon = is_layak ? 'fa-check-circle' : 'fa-times-circle';
    $('#semhas_stat_hasil').html('<span style="background-color: ' + badge_bg + '; color: #ffffff; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 12px; display: inline-block;"><i class="fa ' + badge_icon + '"></i> ' + hasil_txt + '</span>');

    var jadwal_str = data.tanggal ? (data.tanggal + (data.jam ? ' (' + data.jam + ' WIB)' : '')) : '-';
    if (data.tempat && data.tempat !== '-') jadwal_str += ' &bull; ' + data.tempat;
    $('#semhas_stat_jadwal').html(jadwal_str);

    if (data.link_dokumen) {
      $('#semhas_stat_dokumen').html('<a href="' + data.link_dokumen + '" target="_blank" class="btn btn-default btn-xs" style="font-size: 11px; border: 1px solid #cbd5e1; width: 100%;"><i class="fa fa-file-text-o text-primary"></i> Buka Berkas Semhas</a>');
    } else {
      $('#semhas_stat_dokumen').html('');
    }
    $('#semhas_stat_ba').html('<a href="<?= site_url('E_skripsi_seminar_hasil/cetak_pdf/') ?>/' + hash_id + '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; width: 100%;"><i class="fa fa-file-pdf-o"></i> Unduh Berita Acara</a>');

    // Tabel Penguji
    var tbody = '';
    var list = data.penguji_data || [];
    if (list.length > 0) {
      var no = 1;
      list.forEach(function(item) {
        tbody += '<tr>' +
          '<td class="text-center" style="width: 35px;">' + (no++) + '</td>' +
          '<td>' +
            '<strong>' + (item.nama_lengkap || '-') + '</strong>' +
            '<div style="font-size: 11px; color: #64748b;">' + item.role_dosen + '</div>' +
          '</td>' +
          '<td class="text-center">' + (item.nilai_a || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_b || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_c || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_d || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_e || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_f || 0) + '</td>' +
          '<td class="text-center" style="font-weight: 700; font-size: 13px; color: #0f766e;">' + (item.nilai_akhir || 0) + '</td>' +
        '</tr>';
      });
    } else {
      tbody = '<tr><td colspan="9" class="text-center text-muted" style="padding: 16px;">Belum ada data nilai penguji Semhas.</td></tr>';
    }
    $('#semhas_table_body').html(tbody);
    $('#semhas_table_rerata_val').text(data.rerata > 0 ? data.rerata : '0');

    // Catatan Revisi
    var catatan_html = '';
    var kriteria_semhas = [
      { key: 'catatan_judul', label: 'Judul' },
      { key: 'catatan_latar_belakang', label: 'Latar Belakang' },
      { key: 'catatan_rumusan_masalah', label: 'Rumusan Masalah' },
      { key: 'catatan_tujuan_penelitian', label: 'Tujuan / Manfaat' },
      { key: 'catatan_tinjauan_pustaka', label: 'Tinjauan Pustaka' },
      { key: 'catatan_metode_penelitian', label: 'Metode Penelitian' },
      { key: 'catatan_daftar_pustaka', label: 'Daftar Pustaka' },
      { key: 'catatan_format_penulisan', label: 'Format Penulisan' }
    ];

    if (list.length > 0) {
      list.forEach(function(item) {
        var notes_content = '';
        var count = 0;
        kriteria_semhas.forEach(function(k) {
          var val = item[k.key];
          if (val && val.trim() !== '' && val !== '<p><br></p>') {
            count++;
            notes_content += '<div class="sempro-note-item">' +
              '<div class="sempro-note-label">' + k.label + ':</div>' +
              '<div style="font-size: 12px; line-height: 1.45;">' + val + '</div>' +
            '</div>';
          }
        });

        catatan_html += '<div class="sempro-note-card">' +
          '<div class="sempro-note-header">' +
            '<div><strong>' + (item.nama_lengkap || '-') + '</strong> <span class="text-muted">(' + item.role_dosen + ')</span></div>' +
            '<div><span class="label label-default" style="font-size: 10.5px; background-color: #e2e8f0; color: #334155; font-weight: 600;">Nilai: ' + (item.nilai_akhir || 0) + '</span></div>' +
          '</div>' +
          '<div class="sempro-note-body">' +
            (count > 0 ? notes_content : '<span class="text-muted" style="font-style: italic; font-size: 11px;">Tidak ada catatan usulan revisi dari penguji ini.</span>') +
          '</div>' +
        '</div>';
      });
    } else {
      catatan_html = '<p class="text-muted" style="font-style: italic; font-size: 12px; text-align: center; padding: 15px;">Belum ada catatan usulan revisi Seminar Hasil.</p>';
    }
    $('#semhas_catatan_container').html(catatan_html);
  }

  function render_sidang_tab(data, hash_id) {
    // Ringkasan
    $('#sidang_stat_rerata').text(data.rerata > 0 ? data.rerata : '-');
    
    var hasil_txt = data.hasil || '-';
    if (data.nilai_mutu && data.nilai_mutu !== '-') {
      hasil_txt += ' [' + data.nilai_mutu + ']';
    }
    var is_lulus = hasil_txt.toUpperCase().indexOf('LULUS') !== -1;
    var badge_bg = (hasil_txt !== '-' && is_lulus) ? '#10b981' : (hasil_txt !== '-' ? '#ef4444' : '#94a3b8');
    var badge_icon = is_lulus ? 'fa-graduation-cap' : 'fa-times-circle';
    $('#sidang_stat_hasil').html('<span style="background-color: ' + badge_bg + '; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 12px; display: inline-block;"><i class="fa ' + badge_icon + '"></i> ' + hasil_txt + '</span>');

    var jadwal_str = data.tanggal ? (data.tanggal + (data.jam ? ' (' + data.jam + ' WIB)' : '')) : '-';
    if (data.tempat && data.tempat !== '-') jadwal_str += ' &bull; ' + data.tempat;
    $('#sidang_stat_jadwal').html(jadwal_str);

    if (data.link_dokumen) {
      $('#sidang_stat_dokumen').html('<a href="' + data.link_dokumen + '" target="_blank" class="btn btn-default btn-xs" style="font-size: 11px; border: 1px solid #cbd5e1; width: 100%;"><i class="fa fa-file-text-o text-primary"></i> Buka Berkas Skripsi</a>');
    } else {
      $('#sidang_stat_dokumen').html('');
    }
    $('#sidang_stat_ba').html('<a href="<?= site_url('E_skripsi_sidang/cetak_pdf/') ?>/' + hash_id + '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; width: 100%;"><i class="fa fa-file-pdf-o"></i> Unduh Berita Acara</a>');

    // Tabel Penguji
    var tbody = '';
    var list = data.penguji_data || [];
    if (list.length > 0) {
      var no = 1;
      list.forEach(function(item) {
        tbody += '<tr>' +
          '<td class="text-center" style="width: 35px;">' + (no++) + '</td>' +
          '<td>' +
            '<strong>' + (item.nama_lengkap || '-') + '</strong>' +
            '<div style="font-size: 11px; color: #64748b;">' + item.role_dosen + '</div>' +
          '</td>' +
          '<td class="text-center">' + (item.nilai_a || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_b || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_c || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_d || 0) + '</td>' +
          '<td class="text-center">' + (item.nilai_e || 0) + '</td>' +
          '<td class="text-center" style="font-weight: 700; font-size: 13px; color: #0f766e;">' + (item.nilai_akhir || 0) + '</td>' +
        '</tr>';
      });
    } else {
      tbody = '<tr><td colspan="8" class="text-center text-muted" style="padding: 16px;">Belum ada data nilai penguji Sidang TA.</td></tr>';
    }
    $('#sidang_table_body').html(tbody);
    $('#sidang_table_rerata_val').text(data.rerata > 0 ? data.rerata : '0');

    // Catatan Revisi Sidang
    var catatan_html = '';
    if (list.length > 0) {
      list.forEach(function(item) {
        var notes_content = '';
        var val = item.uraian_revisi;
        if (val && val.trim() !== '' && val !== '<p><br></p>') {
          notes_content = '<div class="sempro-note-item">' +
            '<div class="sempro-note-label">Uraian / Catatan Perbaikan:</div>' +
            '<div style="font-size: 12px; line-height: 1.45;">' + val + '</div>' +
          '</div>';
        }

        catatan_html += '<div class="sempro-note-card">' +
          '<div class="sempro-note-header">' +
            '<div><strong>' + (item.nama_lengkap || '-') + '</strong> <span class="text-muted">(' + item.role_dosen + ')</span></div>' +
            '<div><span class="label label-default" style="font-size: 10.5px; background-color: #e2e8f0; color: #334155; font-weight: 600;">Nilai: ' + (item.nilai_akhir || 0) + '</span></div>' +
          '</div>' +
          '<div class="sempro-note-body">' +
            (notes_content !== '' ? notes_content : '<span class="text-muted" style="font-style: italic; font-size: 11px;">Tidak ada catatan usulan revisi dari penguji ini.</span>') +
          '</div>' +
        '</div>';
      });
    } else {
      catatan_html = '<p class="text-muted" style="font-style: italic; font-size: 12px; text-align: center; padding: 15px;">Belum ada catatan usulan revisi Sidang TA.</p>';
    }
    $('#sidang_catatan_container').html(catatan_html);
  }
</script>
