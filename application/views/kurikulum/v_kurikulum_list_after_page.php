<!-- SweetAlert2 (Local + CDN fallback) & Marked.js -->
<link rel="stylesheet" href="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.min.css'); ?>">
<script src="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js'); ?>"></script>
<script>
  if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
  var currentAuditMarkdown = '';

  // Dialog Helpers (Swal2 -> Swal1 -> Native Fallback)
  function appSwalConfirm(title, text, confirmBtnText, callback, icon) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        title: title,
        text: text,
        icon: icon || 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f766e',
        cancelButtonColor: '#64748b',
        confirmButtonText: confirmBtnText || 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
      }).then(function(r) {
        if (r.value || r.isConfirmed) {
          callback();
        }
      });
    } else if (typeof swal === 'function') {
      swal({
        title: title,
        text: text,
        type: icon || 'warning',
        showCancelButton: true,
        confirmButtonText: confirmBtnText || 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        closeOnConfirm: true
      }, function(isConfirm) {
        if (isConfirm) {
          callback();
        }
      });
    } else {
      if (confirm(text ? (title + '\n\n' + text) : title)) {
        callback();
      }
    }
  }

  function appSwalLoading(title, text) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        didOpen: function() {
          if (typeof Swal.showLoading === 'function') {
            Swal.showLoading();
          }
        }
      });
    }
  }

  function appSwalSuccess(title, text, callback) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        confirmButtonColor: '#0f766e'
      }).then(function() {
        if (typeof callback === 'function') callback();
      });
    } else if (typeof swal === 'function') {
      swal(title, text, 'success');
      if (typeof callback === 'function') setTimeout(callback, 600);
    } else {
      alert(title + '\n\n' + text);
      if (typeof callback === 'function') callback();
    }
  }

  function appSwalError(title, text) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        confirmButtonColor: '#dc2626'
      });
    } else if (typeof swal === 'function') {
      swal(title, text, 'error');
    } else {
      alert(title + '\n\n' + text);
    }
  }

  $(document).ready(function() {
    // Configure Marked
    if (typeof marked !== 'undefined') {
      marked.setOptions({
        gfm: true,
        breaks: true,
        smartLists: true
      });
    }

    // Submit Upload Form via AJAX
    $('#formUploadKurikulum').on('submit', function(e) {
      e.preventDefault();

      var fileInput = document.getElementById('upload_file_input');
      if (!fileInput.files.length) {
        alert('Silakan pilih berkas dokumen kurikulum terlebih dahulu.');
        return;
      }

      var formData = new FormData(this);
      var $btn = $('#btn_submit_upload');
      var origText = $btn.html();

      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mengekstrak Teks Dokumen...');

      $.ajax({
        url: '<?= site_url("C_kurikulum/ajax_upload"); ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
          $btn.prop('disabled', false).html(origText);
          if (res.status) {
            $('#modalUploadKurikulum').modal('hide');
            appSwalSuccess('Berhasil!', res.msg, function() {
              location.reload();
            });
          } else {
            appSwalError('Gagal Mengunggah', res.msg);
          }
        },
        error: function(xhr, status, error) {
          $btn.prop('disabled', false).html(origText);
          appSwalError('Kesalahan Server', 'Terjadi kesalahan koneksi server: ' + (xhr.responseText || error));
        }
      });
    });
  });

  function bukaModalUpload() {
    $('#formUploadKurikulum')[0].reset();
    $('#dropzone_icon').attr('class', 'fa fa-file-text-o').css('color', '#0f766e');
    $('#dropzone_text').text('Klik atau seret file PDF / DOCX Kurikulum ke sini');
    $('#modalUploadKurikulum').modal('show');
  }

  function previewSelectedFile(input) {
    if (input.files && input.files[0]) {
      var file = input.files[0];
      $('#dropzone_icon').attr('class', 'fa fa-check-circle').css('color', '#16a34a');
      $('#dropzone_text').html('<strong>' + file.name + '</strong> (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)');
      
      // Auto-fill judul if empty
      var currentJudul = $.trim($('#upload_judul').val());
      var prodi = $('#upload_nama_prodi').val();
      if (!currentJudul && prodi) {
        $('#upload_judul').val('Dokumen Kurikulum & CPL ' + prodi);
      }
    }
  }

  function lihatTeksKurikulum(id) {
    $('#view_teks_title').html('<i class="fa fa-spinner fa-spin text-primary"></i> Memuat Berkas...');
    $('#view_teks_content').text('Sedang membaca teks dokumen dari server...');
    $('#modalViewTeks').modal('show');

    $.ajax({
      url: '<?= site_url("C_kurikulum/ajax_get_content"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          $('#view_teks_title').html('<i class="fa fa-file-text-o text-primary"></i> ' + res.data.judul_dokumen + ' (' + res.data.nama_prodi + ')');
          $('#view_teks_content').text(res.data.content);
          $('#view_teks_meta').html('<strong>' + Number(res.data.total_karakter).toLocaleString() + ' karakter</strong> · Diperbarui: ' + res.data.updated_at + ' WIB oleh ' + res.data.uploaded_by_nama);
        } else {
          $('#view_teks_content').text('Gagal memuat: ' + res.msg);
        }
      },
      error: function(xhr, status, error) {
        $('#view_teks_content').text('Terjadi kesalahan jaringan: ' + (xhr.responseText || error));
      }
    });
  }

  function renderMarkdownClean(rawMd) {
    if (!rawMd) return '';
    var clean = rawMd.replace(/<think>[\s\S]*?<\/think>/gi, '');
    clean = clean.replace(/^```(?:markdown)?\s*\n?/i, '').replace(/\n?```\s*$/i, '');
    var pos = clean.indexOf('# LAPORAN AUDIT');
    if (pos > 0) {
      clean = clean.substring(pos);
    }
    return typeof marked !== 'undefined' ? marked.parse(clean.trim()) : clean.trim();
  }

  function auditKurikulumAi(id, namaProdi) {
    currentAuditMarkdown = '';
    $('#audit_modal_subtitle').text('Program Studi: ' + namaProdi + ' · SN-Dikti Permendiktisaintek No. 39/2025 jo No. 10/2026');
    $('#audit_loading').show();
    $('#audit_output').hide();
    $('#modalAuditAi').modal('show');

    $.ajax({
      url: '<?= site_url("C_kurikulum/ajax_audit_ai"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        $('#audit_loading').hide();
        if (res.status) {
          currentAuditMarkdown = res.audit_text;
          var rendered = renderMarkdownClean(res.audit_text);
          $('#audit_rendered_content').html(rendered);
          $('#audit_output').fadeIn(150);

          // Update table cell dynamically
          if ($('#cell_audit_' + id).length) {
            var cellHtml = '<div style="font-size: 12.5px; line-height: 1.45; color: #334155;">' +
                           '<div style="margin-bottom: 3px;">' +
                           '<strong style="color: #0f172a;">' + $('<div>').text(res.audit_oleh_nama).html() + '</strong>' +
                           '<span style="font-size: 11px; color: #94a3b8; margin-left: 4px;">• ' + res.audit_terakhir_at + '</span>' +
                           '</div>' +
                           '<div class="audit-summary" style="font-size: 12px; color: #475569; margin-bottom: 4px;">' +
                           res.kesimpulan_singkat +
                           '</div>' +
                           '<div>' +
                           '<a href="javascript:;" onclick="lihatHasilAuditTersimpan(' + id + ')" style="font-size: 11.5px; font-weight: 600; color: #2563eb; text-decoration: none;">' +
                           'Lihat Hasil Audit Lengkap &raquo;' +
                           '</a>' +
                           '</div>' +
                           '</div>';
            $('#cell_audit_' + id).html(cellHtml);
          }
        } else {
          $('#audit_rendered_content').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal melakukan audit AI: ' + res.msg + '</div>');
          $('#audit_output').fadeIn(150);
        }
      },
      error: function(xhr, status, error) {
        $('#audit_loading').hide();
        $('#audit_rendered_content').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan jaringan saat memanggil AI: ' + (xhr.responseText || error) + '</div>');
        $('#audit_output').fadeIn(150);
      }
    });
  }

  function lihatHasilAuditTersimpan(id) {
    currentAuditMarkdown = '';
    $('#audit_modal_subtitle').text('Memuat laporan hasil audit tersimpan...');
    $('#audit_loading').show();
    $('#audit_output').hide();
    $('#modalAuditAi').modal('show');

    $.ajax({
      url: '<?= site_url("C_kurikulum/ajax_get_audit_result"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        $('#audit_loading').hide();
        if (res.status && res.data) {
          var d = res.data;
          $('#audit_modal_subtitle').text('Program Studi: ' + d.nama_prodi + ' · Diaudit oleh ' + d.audit_oleh_nama + ' (' + d.audit_terakhir_at + ')');
          currentAuditMarkdown = d.audit_full_result;
          var rendered = renderMarkdownClean(d.audit_full_result);
          $('#audit_rendered_content').html(rendered);
          $('#audit_output').fadeIn(150);
        } else {
          $('#audit_rendered_content').html('<div class="alert alert-warning"><i class="fa fa-info-circle"></i> ' + (res.msg || 'Belum ada data audit tersimpan.') + '</div>');
          $('#audit_output').fadeIn(150);
        }
      },
      error: function(xhr, status, error) {
        $('#audit_loading').hide();
        $('#audit_rendered_content').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal memuat hasil audit: ' + (xhr.responseText || error) + '</div>');
        $('#audit_output').fadeIn(150);
      }
    });
  }

  function hapusKurikulum(id, namaProdi) {
    var promptMsg = 'Apakah Anda yakin ingin menghapus dokumen kurikulum prodi ' + namaProdi + '?\n\nTindakan ini tidak dapat dibatalkan.';
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Hapus Kurikulum?',
        text: 'Dokumen kurikulum prodi ' + namaProdi + ' akan dihapus dari pustaka.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.value || result.isConfirmed) {
          eksekusiHapus(id);
        }
      });
    } else {
      if (confirm(promptMsg)) {
        eksekusiHapus(id);
      }
    }
  }

  function eksekusiHapus(id) {
    $.ajax({
      url: '<?= site_url("C_kurikulum/ajax_delete"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          $('#row_kuri_' + id).fadeOut(300, function() {
            $(this).remove();
          });
        } else {
          alert(res.msg);
        }
      },
      error: function() {
        alert('Gagal menghapus data.');
      }
    });
  }

  function salinHasilAudit() {
    if (!currentAuditMarkdown) {
      alert('Belum ada laporan audit yang dapat disalin.');
      return;
    }
    if (navigator.clipboard) {
      navigator.clipboard.writeText(currentAuditMarkdown).then(function() {
        alert('Laporan audit berhasil disalin ke clipboard!');
      });
    } else {
      var dummy = document.createElement("textarea");
      document.body.appendChild(dummy);
      dummy.value = currentAuditMarkdown;
      dummy.select();
      document.execCommand("copy");
      document.body.removeChild(dummy);
      alert('Laporan audit berhasil disalin!');
    }
  }

  function cetakHasilAudit() {
    var htmlContent = $('#audit_rendered_content').html();
    if (!htmlContent) return;

    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Laporan Audit Kurikulum OBE</title>');
    printWindow.document.write('<style>body{font-family:Arial,sans-serif;padding:30px;color:#1e293b;line-height:1.6;} table{width:100%;border-collapse:collapse;margin:15px 0;} th,td{border:1px solid #cbd5e1;padding:8px 10px;font-size:12px;} th{background:#f1f5f9;font-weight:bold;} h1{font-size:18px;border-bottom:2px solid #334155;padding-bottom:6px;} h2{font-size:14px;border-bottom:1px solid #cbd5e1;padding-bottom:4px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(htmlContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
      printWindow.print();
    }, 400);
  }

  // =========================================================================
  // ADMIN NORMALIZATION & STRUCTURED CURRICULUM ACTIONS (PHASE 1.7)
  // =========================================================================

  var currentNormalizedPayload = null;
  var currentNormalizedKurikulumId = null;
  var currentNormalizedValidation = null;
  var currentNormalizedStatus = null;

  /**
   * 4. LIHAT HASIL NORMALISASI (MODAL SUMMARY)
   */
  function lihatHasilNormalisasi(id) {
    currentNormalizedKurikulumId = id;
    currentNormalizedPayload = null;
    currentNormalizedValidation = null;

    $('#norm_loading').show();
    $('#norm_empty_state').hide();
    $('#norm_content_view').hide();
    $('#modalNormalizedSummary').modal('show');

    $.ajax({
      url: '<?= site_url("C_kurikulum/ajax_get_normalized_summary"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        $('#norm_loading').hide();

        if (!res.status) {
          alert('Gagal memuat ringkasan: ' + (res.msg || 'Terjadi kesalahan.'));
          $('#modalNormalizedSummary').modal('hide');
          return;
        }

        var p = res.payload || {};
        var v = res.validation || {};

        $('#norm_header_prodi').text(res.nama_prodi || (p.prodi && p.prodi.nama_resmi) || '-');
        var tahunVal = (p.curriculum && p.curriculum.tahun) ? p.curriculum.tahun : (res.tahun_kurikulum || '-');
        $('#norm_header_tahun').text(tahunVal);

        if (!res.has_normalized) {
          // Dokumen belum pernah dinormalisasi
          $('#norm_header_tahun').text('-');
          $('#norm_header_hash').text('-');
          $('#norm_header_badge').attr('class', 'kuri-badge').css({'background':'#475569', 'color':'#f8fafc'}).html('<i class="fa fa-hourglass-start"></i> Belum Dinormalisasi');
          $('#btn_modal_empty_normalize').off('click').on('click', function() {
            normalisasiKurikulum(id);
          });
          $('#btn_modal_verify').hide();
          $('#btn_modal_rebuild').hide();
          $('#norm_empty_state').show();
          return;
        }

        // Simpan state
        currentNormalizedPayload = p;
        currentNormalizedValidation = v;
        currentNormalizedStatus = res.normalization_status;

        // Render Header Meta & Badge
        $('#norm_header_tahun').text((p.curriculum && p.curriculum.tahun) ? p.curriculum.tahun : (res.tahun_kurikulum || '-'));

        if (res.normalization_status === 'VERIFIED') {
          $('#norm_header_badge').attr('class', 'kuri-badge success').css({'background':'#dcfce7', 'color':'#15803d', 'font-weight':'700'}).html('<i class="fa fa-check-circle"></i> VERIFIED (Pustaka Resmi)');
        } else if (res.normalization_status === 'NORMALIZED') {
          $('#norm_header_badge').attr('class', 'kuri-badge info').css({'background':'#e0f2fe', 'color':'#0369a1', 'font-weight':'700'}).html('<i class="fa fa-cogs"></i> NORMALIZED (Menunggu Verifikasi)');
        } else if (res.normalization_status === 'STALE') {
          $('#norm_header_badge').attr('class', 'kuri-badge').css({'background':'#fef3c7', 'color':'#b45309', 'font-weight':'700'}).html('<i class="fa fa-exclamation-triangle"></i> STALE (Source Berubah)');
        } else {
          $('#norm_header_badge').attr('class', 'kuri-badge').css({'background':'#f1f5f9', 'color':'#64748b'}).html('<i class="fa fa-hourglass-start"></i> ' + res.normalization_status);
        }

        // Validation Alert Banner (Row validasi di-hide, hanya ditampilkan jika terdapat kegagalan integritas)
        if (v && v.valid) {
          $('#norm_validation_banner').hide().html('');
        } else {
          var errList = (v && v.errors) ? v.errors.join('<br>• ') : 'Terjadi kegagalan validasi payload.';
          var bannerHtml = '<div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:10px 16px; border-radius:8px; display:flex; align-items:flex-start; gap:10px;">' +
                       '<i class="fa fa-exclamation-triangle" style="font-size:18px; margin-top:2px;"></i>' +
                       '<div><strong>Peringatan Validasi Integritas:</strong><br>• ' + errList + '</div>' +
                       '</div>';
          $('#norm_validation_banner').show().html(bannerHtml);
        }

        // Render Key Metrics
        var cpls = p.official_cpl || [];
        var bks  = p.bahan_kajian || [];
        var mks  = p.mata_kuliah || [];
        var directPairs = p.direct_cpl_mk_pairs || [];
        var cplBk = p.cpl_bk_mapping || {};

        var catCounts = { S: 0, P: 0, KU: 0, KK: 0 };
        cpls.forEach(function(c) {
          var cat = (c.category || '').toUpperCase();
          if (cat === 'U') cat = 'KU';
          if (cat === 'KS' || cat === 'K') cat = 'KK';
          if (catCounts[cat] !== undefined) catCounts[cat]++;
        });

        $('#stat_official_cpl').text(cpls.length);
        $('#stat_cpl_breakdown').text('S:' + catCounts.S + ' · P:' + catCounts.P + ' · KU:' + catCounts.KU + ' · KK:' + catCounts.KK);
        $('#stat_bk_count').text(bks.length);

        $('#stat_cpl_bk_status').text(cplBk.status || '-');
        if (cplBk.status === 'PARSED') {
          $('#stat_cpl_bk_count').text((cplBk.mappable_count !== null ? cplBk.mappable_count : (cplBk.pairs || []).length) + ' CPL Terpetakan');
        } else if (cplBk.status === 'AVAILABLE_UNPARSED') {
          $('#stat_cpl_bk_count').text('Matriks Tersedia (Unparsed)');
        } else {
          $('#stat_cpl_bk_count').text('Jalur Langsung CPL↔MK');
        }

        var directCount = p.direct_cpl_mk_pair_count !== undefined ? p.direct_cpl_mk_pair_count : (p.direct_cpl_mk_pairs || []).length;
        var derivedCount = p.derived_cpl_mk_pair_count !== undefined ? p.derived_cpl_mk_pair_count : (p.derived_cpl_mk_pairs || []).length;
        var effectiveCount = p.effective_cpl_mk_pair_count !== undefined ? p.effective_cpl_mk_pair_count : (p.effective_cpl_mk_pairs || []).length;
        var mappedCourseCount = p.direct_mapped_active_course_count !== undefined ? p.direct_mapped_active_course_count : (p.mapped_course_count !== undefined ? p.mapped_course_count : mks.filter(function(m) { return (m.effective_cpl_codes || []).length > 0; }).length);

        $('#stat_cpl_mk_pairs').text(effectiveCount);
        $('#stat_cpl_mk_subtext').html('<span style="color:#0f766e; font-weight:700;">' + effectiveCount + ' Total</span> (Langsung: ' + directCount + ' · Via BK: ' + derivedCount + ')');
        $('#stat_mk_count').text(mks.length);

        var totalSks = 0;
        mks.forEach(function(m) {
          totalSks += Number(m.sks_total || m.sks || 0);
        });
        $('#stat_sks_summary').text(totalSks + ' SKS Terdistribusi');

        $('#tab_badge_cpl').text(cpls.length);
        $('#tab_badge_bk').text(bks.length);
        $('#tab_badge_cpl_bk').text((cplBk.pairs || []).length);
        $('#tab_badge_mk').text(effectiveCount);
        $('#tab_badge_mk_list').text(mks.length);
        
        if (p.matrix_reconciliation && p.matrix_reconciliation.in_matrix_no_cpl_count !== undefined) {
          var mr = p.matrix_reconciliation;
          $('#mk_total_badge').text('Matrix: ' + mr.matrix_course_count + '/' + mr.matrix_course_count + ' · Direct: ' + mr.direct_mapped_active_course_count + ' MK · 0 CPL: ' + mr.in_matrix_no_cpl_count + ' · Luar Matriks: ' + mr.outside_matrix_count + ' (' + effectiveCount + ' Pasangan CPL-MK)');
        } else {
          $('#mk_total_badge').text(mappedCourseCount + ' MK Terpetakan (' + effectiveCount + ' Pasangan CPL-MK)');
        }

        // BK Diagnostic & Source Section
        var bkSourceHeading = p.bahan_kajian_source_section || (bks.length > 0 && bks[0].source_section ? bks[0].source_section : 'BAHAN KAJIAN');
        var bkExtractionStatus = p.bahan_kajian_extraction_status || (bks.length > 0 ? 'COMPLETE' : 'NOT_AVAILABLE');
        var bkDiagClass = bkExtractionStatus === 'COMPLETE' ? 'background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;' : (bkExtractionStatus === 'PARTIAL' ? 'background:#fffbeb; border:1px solid #fef3c7; color:#92400e;' : 'background:#f8fafc; border:1px solid #e2e8f0; color:#475569;');
        var bkDiagIcon = bkExtractionStatus === 'COMPLETE' ? 'fa-check-circle' : (bkExtractionStatus === 'PARTIAL' ? 'fa-exclamation-triangle' : 'fa-info-circle');

        $('#stat_bk_subtext').html('<span style="font-weight:700; color:' + (bkExtractionStatus === 'COMPLETE' ? '#16a34a' : '#ea580c') + ';">' + bkExtractionStatus + '</span> · ' + $('<div>').text(bkSourceHeading).html());

        $('#bk_diagnostic_alert').html(
          '<div style="' + bkDiagClass + ' padding:9px 14px; border-radius:6px; font-size:12.5px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">' +
          '<div><i class="fa ' + bkDiagIcon + '"></i> <strong>Status Ekstraksi BK:</strong> <span style="text-decoration:underline;">' + bkExtractionStatus + '</span> &nbsp;·&nbsp; <strong>Source Section:</strong> <em>' + $('<div>').text(bkSourceHeading).html() + '</em></div>' +
          '<div><span class="badge" style="background:rgba(0,0,0,0.08); color:inherit; font-weight:700; padding:4px 8px;">' + bks.length + ' Master BK Terdaftar</span></div>' +
          '</div>'
        );

        // TAB 1: Render Official CPL
        var cplRowsHtml = '';
        if (cpls.length > 0) {
          cpls.forEach(function(c, idx) {
            var cat = (c.category || '').toUpperCase();
            var catLabel = 'Lainnya';
            var badgeStyle = 'background:#e2e8f0; color:#1e293b;';
            if (cat === 'S') {
              catLabel = 'Sikap (S)';
              badgeStyle = 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;';
            } else if (cat === 'P') {
              catLabel = 'Pengetahuan (P)';
              badgeStyle = 'background:#fef9c3; color:#854d0e; border:1px solid #fde047;';
            } else if (cat === 'KU' || cat === 'U') {
              catLabel = 'Keterampilan Umum (KU)';
              badgeStyle = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;';
            } else if (cat === 'KK' || cat === 'KS' || cat === 'K') {
              catLabel = 'Keterampilan Khusus (KK)';
              badgeStyle = 'background:#fce7f3; color:#9d174d; border:1px solid #fbcfe8;';
            }

            cplRowsHtml += '<tr class="cpl-row" data-category="' + cat + '" data-search="' + (c.code + ' ' + c.description).toLowerCase() + '">' +
                           '<td style="text-align:center; font-weight:600; color:#94a3b8;">' + (idx + 1) + '</td>' +
                           '<td><strong style="color:#0f172a; font-family:monospace; font-size:13px;">' + $('<div>').text(c.code).html() + '</strong></td>' +
                           '<td><span style="display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:0.02em; white-space:nowrap; ' + badgeStyle + '">' + catLabel + '</span></td>' +
                           '<td style="color:#334155; line-height:1.5;">' + $('<div>').text(c.description).html() + '</td>' +
                           '</tr>';
          });
        } else {
          cplRowsHtml = '<tr><td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data Official CPL terdeteksi.</td></tr>';
        }
        $('#tbody_official_cpl').html(cplRowsHtml);

        // TAB 2: Render Bahan Kajian (BK)
        var bkRowsHtml = '';
        if (bks.length > 0) {
          bks.forEach(function(b, idx) {
            var isNormalizedCode = b.is_normalized_code === true || b.is_normalized_code === 1 || b.is_normalized_code === '1';
            var codeBadge = isNormalizedCode
              ? '<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:10px; font-weight:700; padding:3px 7px;">NORMALIZED-CODE</span>'
              : '<span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:10px; font-weight:700; padding:3px 7px;">SOURCE-EXACT</span>';
            
            var srcCode = b.source_code || b.code || '-';
            var srcName = b.source_name || b.name || '';
            var nameHtml = '<strong style="color:#0f172a;">' + $('<div>').text(b.name).html() + '</strong>';
            if (b.name_normalization === 'DISPLAY_ONLY' && srcName && srcName !== b.name) {
              nameHtml += '<div style="font-size:11px; color:#64748b; margin-top:2px;"><em>Sumber: ' + $('<div>').text(srcName).html() + '</em></div>';
            }

            var descText = b.description ? $('<div>').text(b.description).html() : '';
            if (b.source_section) {
              descText += (descText ? ' · ' : '') + '<span style="font-size:11px; color:#64748b;"><i class="fa fa-folder-o"></i> ' + $('<div>').text(b.source_section).html() + '</span>';
            }

            bkRowsHtml += '<tr>' +
                          '<td style="text-align:center; font-weight:600; color:#94a3b8;">' + (idx + 1) + '</td>' +
                          '<td><strong style="color:#0f766e; font-family:monospace; font-size:13px;">' + $('<div>').text(b.code).html() + '</strong></td>' +
                          '<td><span style="font-family:monospace; color:#475569; font-size:12px;">' + $('<div>').text(srcCode).html() + '</span></td>' +
                          '<td>' + nameHtml + '</td>' +
                          '<td style="text-align:center;">' + codeBadge + '</td>' +
                          '<td style="color:#475569;">' + (descText || '-') + '</td>' +
                          '</tr>';
          });
        } else {
          bkRowsHtml = '<tr><td colspan="6" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data Bahan Kajian terdeteksi.</td></tr>';
        }
        $('#tbody_bk').html(bkRowsHtml);

        // TAB 3: Render Pemetaan CPL-BK
        var cplBkAlertHtml = '';
        if (cplBk.status === 'PARSED') {
          cplBkAlertHtml = '<div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:8px 14px; border-radius:6px; font-size:12px;">' +
                           '<i class="fa fa-check-circle"></i> <strong>Status: PARSED</strong> — Matriks CPL ke Bahan Kajian berhasil diekstrak (' + (cplBk.mappable_count || (cplBk.pairs || []).length) + ' CPL mappable). Pasangan pemetaan di bawah:' +
                           '</div>';
        } else if (cplBk.status === 'AVAILABLE_UNPARSED') {
          cplBkAlertHtml = '<div style="background:#fffbeb; border:1px solid #fef3c7; color:#92400e; padding:8px 14px; border-radius:6px; font-size:12px;">' +
                           '<i class="fa fa-info-circle"></i> <strong>Status: AVAILABLE_UNPARSED</strong> — Dokumen kurikulum memiliki matriks CPL-BK, namun RPS Generator memprioritaskan pemetaan direct CPL-MK yang eksplisit.' +
                           '</div>';
        } else {
          cplBkAlertHtml = '<div style="background:#f8fafc; border:1px solid #e2e8f0; color:#475569; padding:8px 14px; border-radius:6px; font-size:12px;">' +
                           '<i class="fa fa-info-circle"></i> <strong>Status: NOT_AVAILABLE</strong> — Kurikulum ini langsung menggunakan relasi direct CPL ke Mata Kuliah.' +
                           '</div>';
        }
        $('#cpl_bk_info_alert').html(cplBkAlertHtml);

        var cplBkRowsHtml = '';
        var pairs = cplBk.pairs || [];
        if (pairs.length > 0) {
          pairs.forEach(function(pair, idx) {
            cplBkRowsHtml += '<tr>' +
                             '<td style="text-align:center; font-weight:600; color:#94a3b8;">' + (idx + 1) + '</td>' +
                             '<td><span style="display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe; font-family:monospace;">' + $('<div>').text(pair.cpl_code).html() + '</span></td>' +
                             '<td><span style="display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-family:monospace;">' + $('<div>').text(pair.bk_code).html() + '</span></td>' +
                             '<td style="color:#475569;">Relasi CPL <strong>' + $('<div>').text(pair.cpl_code).html() + '</strong> dengan Bahan Kajian <strong>' + $('<div>').text(pair.bk_code).html() + '</strong></td>' +
                             '</tr>';
          });
        } else {
          cplBkRowsHtml = '<tr><td colspan="4" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data pasangan CPL-BK terpisah.</td></tr>';
        }
        $('#tbody_cpl_bk').html(cplBkRowsHtml);

        // TAB 4: Render Pemetaan CPL-MK & MK
        var mappingAudit = p.cpl_mk_mapping_audit || {
          mapping_source_exists: (p.mapping_source_exists !== undefined ? !!p.mapping_source_exists : true),
          mapping_source_section: p.mapping_source_section || '',
          mapping_parse_status: p.mapping_parse_status || 'SOURCE_MAPPING_AVAILABLE_UNPARSED',
          mapping_parse_message: p.mapping_parse_message || ''
        };
        var globalMappingStatus = mappingAudit.mapping_parse_status;

        var mkRowsHtml = '';
        if (mks.length > 0) {
          mks.forEach(function(m, idx) {
            var cplBadges = '';
            var directCpls = m.direct_cpl_codes || [];
            var derivedCpls = m.derived_cpl_codes || [];
            var effectiveCpls = m.effective_cpl_codes || directCpls || m.mapped_cpls || m.cpls || [];
            var aggDomains = m.aggregate_domains || [];

            if (effectiveCpls.length > 0) {
              effectiveCpls.forEach(function(cp) {
                cplBadges += '<span style="display:inline-block; font-size:10.5px; font-weight:700; margin-right:3px; margin-bottom:3px; padding:2px 8px; border-radius:20px; font-family:monospace; background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe;">' + $('<div>').text(cp).html() + '</span>';
              });
            } else if (m.effective_mapping_type === 'COVERED_BY_AGGREGATE_MATRIX_TRACK' || (aggDomains.length > 0 && aggDomains.indexOf('INTERNSHIP_TRACK') !== -1)) {
              cplBadges = '<span style="color:#0369a1; font-size:11px; font-weight:600;"><i class="fa fa-cubes"></i> Terhubung melalui Matriks Internship (aggregate)</span>';
            } else if (aggDomains.length > 0) {
              cplBadges = '<span style="color:#92400e; font-size:11px; font-weight:600;"><i class="fa fa-info-circle"></i> Domain Tata Nilai</span>';
            } else if (m.effective_mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
              cplBadges = '<span style="color:#d97706; font-size:11px; font-weight:600;"><i class="fa fa-info-circle"></i> Ada pada matriks resmi, namun tidak diberi CPL pada dokumen</span>';
            } else if (m.effective_mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
              cplBadges = '<span style="color:#64748b; font-size:11px; font-style:italic;"><i class="fa fa-info-circle"></i> Mata kuliah aktif — tidak tercantum pada Matriks CPL-MK resmi</span>';
            } else {
              if (globalMappingStatus === 'SOURCE_MAPPING_AVAILABLE_UNPARSED' || globalMappingStatus === 'SOURCE_MAPPING_PARTIAL') {
                cplBadges = '<span style="color:#d97706; font-size:11px; font-style:italic;"><i class="fa fa-clock-o"></i> Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem</span>';
              } else if (globalMappingStatus === 'SOURCE_MAPPING_PARSER_ERROR') {
                cplBadges = '<span style="color:#dc2626; font-size:11px; font-style:italic;"><i class="fa fa-exclamation-triangle"></i> Pemetaan tersedia di kurikulum — terjadi kendala parsing</span>';
              } else {
                cplBadges = '<span style="color:#94a3b8; font-size:11px; font-style:italic;">Tidak ada pemetaan resmi pada dokumen kurikulum</span>';
              }
            }

            // Domain Aggregate badges (e.g. Tata Nilai / Internship Track)
            var aggBadges = '';
            if (aggDomains.length > 0) {
              aggDomains.forEach(function(dom) {
                var domLabel = (dom === 'TATA_NILAI') ? 'Tata Nilai' : (dom === 'INTERNSHIP_TRACK' ? 'Internship Track' : dom);
                var domBg = (dom === 'INTERNSHIP_TRACK') ? 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;' : 'background:#fef3c7; color:#92400e; border:1px solid #fde68a;';
                aggBadges += '<span class="badge" style="' + domBg + ' font-size:10.5px; font-weight:700; padding:3px 7px;">' + $('<div>').text(domLabel).html() + '</span> ';
              });
            } else {
              aggBadges = '<span style="color:#94a3b8; font-size:11px;">—</span>';
            }

            // Sumber Pemetaan badge
            var sourceBadge = '';
            if (directCpls.length > 0 && derivedCpls.length > 0) {
              sourceBadge = '<span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:10px; font-weight:700; padding:3px 8px;">Langsung + Evidence BK</span>';
            } else if (directCpls.length > 0) {
              sourceBadge = '<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:10px; font-weight:700; padding:3px 8px;">Langsung</span>';
            } else if (derivedCpls.length > 0) {
              sourceBadge = '<span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:10px; font-weight:700; padding:3px 8px;">Via BK</span>';
            } else if (m.effective_mapping_type === 'COVERED_BY_AGGREGATE_MATRIX_TRACK' || (aggDomains.length > 0 && aggDomains.indexOf('INTERNSHIP_TRACK') !== -1)) {
              sourceBadge = '<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:10px; font-weight:700; padding:3px 8px;">Aggregate Track</span>';
            } else if (aggDomains.length > 0) {
              sourceBadge = '<span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:10px; font-weight:700; padding:3px 8px;">Domain Matrix</span>';
            } else if (m.effective_mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
              sourceBadge = '<span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:10px; font-weight:700; padding:3px 8px;">Ada di Matriks (0 CPL)</span>';
            } else if (m.effective_mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
              sourceBadge = '<span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; font-size:10px; font-weight:700; padding:3px 8px;">Luar Matriks</span>';
            } else {
              if (globalMappingStatus === 'SOURCE_MAPPING_AVAILABLE_UNPARSED') {
                sourceBadge = '<span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:10px; font-weight:600; padding:3px 8px;">Tersedia di Kurikulum (Unparsed)</span>';
              } else if (globalMappingStatus === 'SOURCE_MAPPING_PARTIAL') {
                sourceBadge = '<span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:10px; font-weight:600; padding:3px 8px;">Sebagian Terbaca</span>';
              } else if (globalMappingStatus === 'SOURCE_MAPPING_PARSER_ERROR') {
                sourceBadge = '<span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; font-size:10px; font-weight:600; padding:3px 8px;">Kendala Parsing</span>';
              } else {
                sourceBadge = '<span class="badge" style="background:#f1f5f9; color:#64748b; font-size:10px; padding:3px 8px;">Tidak Ada Pemetaan Resmi</span>';
              }
            }

            var fallbackEvidence = (globalMappingStatus === 'SOURCE_MAPPING_NOT_AVAILABLE') ? 'Tidak ada pemetaan resmi pada dokumen kurikulum' : 'Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem';
            var evidenceText = m.mapping_evidence || (effectiveCpls.length > 0 ? 'Tersedia pada kurikulum' : fallbackEvidence);

            var sksT = m.sks_teori !== undefined ? m.sks_teori : '-';
            var sksP = m.sks_praktik !== undefined ? m.sks_praktik : '-';
            var sksL = m.sks_lapangan !== undefined ? m.sks_lapangan : '-';
            var sksTot = m.sks_total !== undefined ? m.sks_total : (m.sks || 0);

            mkRowsHtml += '<tr>' +
                          '<td style="text-align:center; font-weight:600; color:#94a3b8;">' + (idx + 1) + '</td>' +
                          '<td><code style="font-size:11.5px; color:#2563eb;">' + $('<div>').text(m.kode_mk || m.code || '-').html() + '</code></td>' +
                          '<td><strong style="color:#0f172a;">' + $('<div>').text(m.nama_mk || m.name || '-').html() + '</strong></td>' +
                          '<td style="text-align:center;"><span class="kuri-badge purple" style="font-size:11px;">Sem ' + (m.semester || '-') + '</span></td>' +
                          '<td style="text-align:center;"><strong style="color:#0f172a;">' + sksTot + ' SKS</strong> <br><small style="color:#64748b; font-size:10.5px;">(' + sksT + '-' + sksP + '-' + sksL + ')</small></td>' +
                          '<td>' + cplBadges + '</td>' +
                          '<td style="text-align:center;">' + aggBadges + '</td>' +
                          '<td style="text-align:center;">' + sourceBadge + '</td>' +
                          '<td style="font-size:11.5px; color:#475569; line-height:1.4;">' + $('<div>').text(evidenceText).html() + '</td>' +
                          '</tr>';
          });
        } else {
          mkRowsHtml = '<tr><td colspan="9" style="text-align:center; padding:30px; color:#94a3b8;">Tidak ada data Mata Kuliah terdeteksi.</td></tr>';
        }
        $('#tbody_mk').html(mkRowsHtml);

        // TAB 5: Render Daftar MK Per Semester
        renderMkListBySemester(mks);

        // TAB 5: Render Validation Checklist & Raw JSON
        var checkHtml = '<ul style="list-style:none; padding-left:0; margin:0;">';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Schema Version:</strong> ' + (p.schema_version || '1.0') + '</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Nama Resmi Prodi:</strong> ' + ((p.prodi && p.prodi.nama_resmi) ? p.prodi.nama_resmi : '-') + '</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Official CPL Count:</strong> ' + cpls.length + ' butir (Lengkap dengan deskripsi & kategori)</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>CPL↔BK Mapping Status:</strong> ' + (cplBk.status || 'N/A') + '</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Pemetaan CPL-MK Status:</strong> <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700;">' + (mappingAudit.mapping_parse_status || 'N/A') + '</span> ' + (mappingAudit.mapping_source_section ? ('(' + mappingAudit.mapping_source_section + ')') : '') + '</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Source Hash SHA-256:</strong> ' + (res.source_hash ? '<code style="font-size:11px;">' + res.source_hash + '</code>' : '-') + '</li>';
        checkHtml += '<li style="margin-bottom:8px;"><i class="fa fa-check-circle text-success" style="width:20px;"></i> <strong>Waktu Normalisasi:</strong> ' + (res.normalized_at || '-') + '</li>';
        checkHtml += '</ul>';
        $('#validation_checklist').html(checkHtml);

        $('#norm_raw_json').text(JSON.stringify(p, null, 2));

        // Update Modal Footer Buttons (Admin)
        if ($('#btn_modal_verify').length) {
          $('#btn_modal_verify').show();
          $('#btn_modal_rebuild').show();

          if (res.normalization_status === 'VERIFIED') {
            $('#btn_modal_verify').attr('class', 'btn btn-default btn-sm').prop('disabled', true).html('<i class="fa fa-check"></i> Sudah Terverifikasi (VERIFIED)');
          } else {
            if (v && v.valid) {
              $('#btn_modal_verify').attr('class', 'btn btn-success btn-sm').prop('disabled', false).html('<i class="fa fa-check-circle"></i> Verifikasi Pustaka Ini (VERIFIED)');
            } else {
              $('#btn_modal_verify').attr('class', 'btn btn-default btn-sm').prop('disabled', true).html('<i class="fa fa-ban"></i> Verifikasi Dinonaktifkan (Gagal Validasi)');
            }
          }
        }

        $('a[href="#tab_cpl"]').tab('show');
        $('#norm_content_view').show();
      },
      error: function(xhr, status, error) {
        $('#norm_loading').hide();
        alert('Gagal menghubungi server: ' + (xhr.responseText || error));
      }
    });
  }

  /**
   * 1. NORMALISASI KURIKULUM (Khusus gege.satya@wbi.ac.id)
   * Status menjadi NORMALIZED. TIDAK auto-verify.
   */
  function normalisasiKurikulum(id) {
    appSwalConfirm(
      'Jalankan Normalisasi Kurikulum?',
      'Sistem akan mengekstrak official CPL, Bahan Kajian, dan relasi matriks secara deterministik. Status akan diset NORMALIZED (memerlukan verifikasi eksplisit admin).',
      'Ya, Jalankan Normalisasi',
      function() {
        appSwalLoading('Menjalankan Normalisasi...', 'Sistem sedang mengekstrak CPL resmi, Bahan Kajian, dan memetakan matriks kurikulum...');

        $.ajax({
          url: '<?= site_url("C_kurikulum/ajax_normalize_curriculum"); ?>',
          type: 'POST',
          data: {
            id: id,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
          },
          dataType: 'json',
          success: function(res) {
            if (res.status) {
              $('#badge_status_' + id).html('<span class="kuri-badge info" style="font-weight:700; font-size:11.5px; padding:3px 8px; background:#e0f2fe; color:#0369a1;"><i class="fa fa-cogs"></i> NORMALIZED</span>');
              appSwalSuccess('Normalisasi Berhasil!', res.msg, function() {
                lihatHasilNormalisasi(id);
              });
            } else {
              var errMsg = res.msg + (res.errors ? '\n• ' + res.errors.join('\n• ') : '');
              appSwalError('Normalisasi Gagal', errMsg);
            }
          },
          error: function(xhr, status, error) {
            appSwalError('Error Jaringan', xhr.responseText || error);
          }
        });
      },
      'question'
    );
  }

  /**
   * 2. VERIFIKASI PUSTAKA (Khusus gege.satya@wbi.ac.id)
   * Hanya aktif jika lolos validasi. Status menjadi VERIFIED.
   */
  function verifikasiKurikulum(id) {
    appSwalConfirm(
      'Verifikasi Pustaka Kurikulum?',
      'Dokumen dan data terstruktur ini akan disahkan sebagai Factual Grounding Resmi (VERIFIED) untuk Studio RPS.',
      'Ya, Verifikasi Sekarang',
      function() {
        appSwalLoading('Memverifikasi...', 'Memvalidasi payload dan menetapkan status VERIFIED...');

        $.ajax({
          url: '<?= site_url("C_kurikulum/ajax_verify_curriculum"); ?>',
          type: 'POST',
          data: {
            id: id,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
          },
          dataType: 'json',
          success: function(res) {
            if (res.status) {
              $('#badge_status_' + id).html('<span class="kuri-badge success" style="font-weight:700; font-size:11.5px; padding:3px 8px;"><i class="fa fa-check-circle"></i> VERIFIED</span>');
              appSwalSuccess('Pustaka Terverifikasi!', res.msg, function() {
                lihatHasilNormalisasi(id);
              });
            } else {
              appSwalError('Verifikasi Gagal', res.msg);
            }
          },
          error: function(xhr, status, error) {
            appSwalError('Error', xhr.responseText || error);
          }
        });
      },
      'warning'
    );
  }

  /**
   * 3. BANGUN ULANG DATA TERSTRUKTUR (Khusus gege.satya@wbi.ac.id)
   * Menghitung ulang source_hash dan menjalankan normalizer ulang, status menjadi NORMALIZED.
   */
  function rebuildKurikulum(id) {
    appSwalConfirm(
      'Bangun Ulang Data Terstruktur?',
      'Gunakan tindakan ini jika berkas sumber berubah. Sistem akan menghitung ulang source_hash dan menjalankan normalisasi ulang. Status akan diset NORMALIZED.',
      'Ya, Bangun Ulang',
      function() {
        appSwalLoading('Membangun Ulang Data...', 'Menghitung ulang source hash dan mengekstrak ulang seluruh komponen...');

        $.ajax({
          url: '<?= site_url("C_kurikulum/ajax_rebuild_curriculum"); ?>',
          type: 'POST',
          data: {
            id: id,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
          },
          dataType: 'json',
          success: function(res) {
            if (res.status) {
              $('#badge_status_' + id).html('<span class="kuri-badge info" style="font-weight:700; font-size:11.5px; padding:3px 8px; background:#e0f2fe; color:#0369a1;"><i class="fa fa-cogs"></i> NORMALIZED</span>');
              appSwalSuccess('Bangun Ulang Berhasil!', res.msg, function() {
                lihatHasilNormalisasi(id);
              });
            } else {
              var errMsg = res.msg + (res.errors ? '\n• ' + res.errors.join('\n• ') : '');
              appSwalError('Bangun Ulang Gagal', errMsg);
            }
          },
          error: function(xhr, status, error) {
            appSwalError('Error', xhr.responseText || error);
          }
        });
      },
      'question'
    );
  }

  // Helper trigger dari modal footer
  function modalVerifyClick() {
    if (currentNormalizedKurikulumId) {
      verifikasiKurikulum(currentNormalizedKurikulumId);
    }
  }

  function modalRebuildClick() {
    if (currentNormalizedKurikulumId) {
      rebuildKurikulum(currentNormalizedKurikulumId);
    }
  }

  // Filter Kategori CPL pada Tab 1
  function filterCplCategory(cat, btn) {
    $('#cpl_filter_pills button').removeClass('btn-primary active').addClass('btn-default');
    $(btn).removeClass('btn-default').addClass('btn-primary active');

    if (cat === 'ALL') {
      $('.cpl-row').show();
    } else {
      $('.cpl-row').each(function() {
        var rowCat = $(this).attr('data-category');
        if (rowCat === cat) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    }
  }

  // Live Search CPL pada Tab 1
  function searchCplTable() {
    var query = $.trim($('#cpl_search_input').val()).toLowerCase();
    $('.cpl-row').each(function() {
      var searchStr = $(this).attr('data-search') || '';
      if (!query || searchStr.indexOf(query) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  // Salin JSON Payload
  function copyNormalizedJson() {
    if (!currentNormalizedPayload) {
      alert('Tidak ada payload JSON.');
      return;
    }
    var jsonStr = JSON.stringify(currentNormalizedPayload, null, 2);
    if (navigator.clipboard) {
      navigator.clipboard.writeText(jsonStr).then(function() {
        alert('Payload JSON berhasil disalin ke clipboard!');
      });
    } else {
      var dummy = document.createElement("textarea");
      document.body.appendChild(dummy);
      dummy.value = jsonStr;
      dummy.select();
      document.execCommand("copy");
      document.body.removeChild(dummy);
      alert('Payload JSON berhasil disalin!');
    }
  }

  // =========================================================================
  // TAB 5: DAFTAR MATA KULIAH PER SEMESTER
  // =========================================================================

  var _mkListAll = []; // cache untuk filter

  function renderMkListBySemester(mks) {
    _mkListAll = mks || [];

    // Build semester filter pills
    var semesters = [];
    _mkListAll.forEach(function(m) {
      var sem = m.semester || 0;
      if (semesters.indexOf(sem) === -1) semesters.push(sem);
    });
    semesters.sort(function(a, b) { return a - b; });

    var pillsHtml = '<button type="button" class="btn btn-xs btn-primary active" onclick="filterMkSemester(\'ALL\', this)" style="border-radius:6px; font-weight:700; padding:4px 10px;">Semua Semester</button>';
    semesters.forEach(function(s) {
      pillsHtml += ' <button type="button" class="btn btn-xs btn-default" onclick="filterMkSemester(\'SEM' + s + '\', this)" style="border-radius:6px; font-weight:600; padding:4px 10px;">Semester ' + s + '</button>';
    });
    $('#mk_sem_filter_pills').html(pillsHtml);

    _renderMkGroups(_mkListAll);
  }

  function _renderMkGroups(list) {
    if (!list || list.length === 0) {
      $('#mk_list_by_semester').html('<div style="text-align:center; padding:40px; color:#94a3b8; font-size:13px;"><i class="fa fa-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>Tidak ada data Mata Kuliah terdeteksi.</div>');
      return;
    }

    // Group by semester
    var groups = {};
    list.forEach(function(m) {
      var sem = m.semester || 0;
      if (!groups[sem]) groups[sem] = [];
      groups[sem].push(m);
    });

    var semKeys = Object.keys(groups).map(Number).sort(function(a, b) { return a - b; });
    var html = '';

    semKeys.forEach(function(sem) {
      var semMks = groups[sem];
      var semSks = 0;
      semMks.forEach(function(m) { semSks += Number(m.sks_total || m.sks || 0); });

      html += '<div class="mk-sem-group" data-semester="SEM' + sem + '" style="margin-bottom:20px;">';
      html += '<div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">';
      html += '<div style="background:#0f172a; color:#f8fafc; font-weight:800; font-size:11.5px; padding:4px 12px; border-radius:20px; white-space:nowrap;">';
      html += sem > 0 ? 'Semester ' + sem : 'Semester Tidak Diketahui';
      html += '</div>';
      html += '<div style="font-size:11px; color:#64748b; background:#f1f5f9; padding:3px 10px; border-radius:20px;">';
      html += semMks.length + ' Mata Kuliah · ' + semSks + ' SKS';
      html += '</div>';
      html += '<div style="flex:1; height:1px; background:#e2e8f0;"></div>';
      html += '</div>';

      html += '<div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0;">';
      html += '<table class="table table-hover" style="margin-bottom:0; font-size:12.5px;">';
      html += '<thead><tr style="background:#f8fafc; color:#475569;">';
      html += '<th style="width:40px; text-align:center;">No</th>';
      html += '<th style="width:100px;">Kode MK</th>';
      html += '<th>Nama Mata Kuliah</th>';
      html += '<th style="width:100px; text-align:center;">SKS</th>';
      html += '<th>CPL Terpetakan</th>';
      html += '</tr></thead>';
      html += '<tbody>';

      semMks.forEach(function(m, idx) {
        var cplBadges = '';
        var mappedCpls = m.direct_cpl_codes || m.mapped_cpls || m.cpls || [];
        if (mappedCpls.length > 0) {
          mappedCpls.forEach(function(cp) {
            cplBadges += '<span style="display:inline-block; font-size:10px; font-weight:700; margin-right:2px; margin-bottom:2px; padding:2px 8px; border-radius:20px; font-family:monospace; background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe;">' + $('<div>').text(cp).html() + '</span>';
          });
        } else {
          cplBadges = '<span style="color:#94a3b8; font-size:11px; font-style:italic;">-</span>';
        }

        var sksT   = m.sks_teori   !== undefined ? m.sks_teori   : '-';
        var sksP   = m.sks_praktik !== undefined ? m.sks_praktik : '-';
        var sksL   = m.sks_lapangan !== undefined ? m.sks_lapangan : '-';
        var sksTot = m.sks_total !== undefined ? m.sks_total : (m.sks || 0);

        var searchStr = ((m.kode_mk || m.code || '') + ' ' + (m.nama_mk || m.name || '')).toLowerCase();

        html += '<tr class="mk-list-row" data-semester="SEM' + sem + '" data-search="' + searchStr + '">';
        html += '<td style="text-align:center; font-weight:600; color:#94a3b8;">' + (idx + 1) + '</td>';
        html += '<td><code style="font-size:11.5px; color:#2563eb;">' + $('<div>').text(m.kode_mk || m.code || '-').html() + '</code></td>';
        html += '<td><strong style="color:#0f172a;">' + $('<div>').text(m.nama_mk || m.name || '-').html() + '</strong></td>';
        html += '<td style="text-align:center;"><strong style="color:#0f172a;">' + sksTot + '</strong><br><small style="color:#64748b; font-size:10px;">(' + sksT + '-' + sksP + '-' + sksL + ')</small></td>';
        html += '<td>' + cplBadges + '</td>';
        html += '</tr>';
      });

      html += '</tbody></table></div></div>'; // close group
    });

    $('#mk_list_by_semester').html(html);
  }

  function filterMkSemester(sem, btn) {
    $('#mk_sem_filter_pills button').removeClass('btn-primary active').addClass('btn-default');
    $(btn).removeClass('btn-default').addClass('btn-primary active');

    // Reset search
    $('#mk_search_input').val('');

    if (sem === 'ALL') {
      _renderMkGroups(_mkListAll);
    } else {
      var filtered = _mkListAll.filter(function(m) {
        return 'SEM' + (m.semester || 0) === sem;
      });
      _renderMkGroups(filtered);
    }
  }

  function searchMkTable() {
    var query = $.trim($('#mk_search_input').val()).toLowerCase();
    if (!query) {
      // Show all rows & groups
      $('.mk-list-row').show();
      $('.mk-sem-group').show();
      return;
    }
    $('.mk-list-row').each(function() {
      var searchStr = $(this).attr('data-search') || '';
      $(this).toggle(searchStr.indexOf(query) !== -1);
    });
    // Hide groups where all rows are hidden
    $('.mk-sem-group').each(function() {
      var visible = $(this).find('.mk-list-row:visible').length;
      $(this).toggle(visible > 0);
    });
  }
</script>


