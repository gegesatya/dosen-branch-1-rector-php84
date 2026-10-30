<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script>
  var currentMatrixPayload = <?= !empty($parsed_data) ? json_encode($parsed_data) : 'null'; ?>;
  var _mkMatrixListAll = [];
  var _cplLookupDict = {};
  var _isDescModeActive = false;

  $(document).ready(function() {
    if (currentMatrixPayload) {
      renderMatrixData(currentMatrixPayload);
    }
  });

  function pilihProdiMatrix(id, btn) {
    $('.btn-prodi-pill').removeClass('active');
    $(btn).addClass('active');

    $('#matrix_content_container').html(
      '<div class="matrix-card" style="text-align:center; padding:60px 20px;">' +
      '  <i class="fa fa-circle-o-notch fa-spin" style="font-size:32px; color:#0f766e; margin-bottom:12px;"></i>' +
      '  <div style="font-weight:700; color:#0f172a; font-size:14px;">Memuat Matriks MK-CPL Program Studi...</div>' +
      '</div>'
    );

    $.ajax({
      url: '<?= site_url("C_matriks_mk_cpl/ajax_get_matrix"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (!res.status) {
          $('#matrix_content_container').html(
            '<div class="matrix-card" style="text-align:center; padding:40px 20px; color:#dc2626;">' +
            '  <i class="fa fa-exclamation-triangle" style="font-size:32px; margin-bottom:10px;"></i>' +
            '  <p style="font-weight:600;">' + (res.msg || 'Gagal memuat data kurikulum.') + '</p>' +
            '</div>'
          );
          return;
        }

        if (!res.is_verified) {
          var isGege = <?= !empty($is_gege) ? 'true' : 'false'; ?>;
          var gegeBtn = isGege ? '<a href="<?= site_url("C_kurikulum"); ?>" class="btn btn-warning btn-sm" style="font-weight:700; border-radius:6px; background:#d97706; border-color:#b45309; color:#ffffff; margin-top:10px;"><i class="fa fa-book"></i> Buka Kurikulum &amp; Verifikasi Dokumen</a>' : '';

          $('#matrix_content_container').html(
            '<div class="matrix-card" style="text-align:center; padding:50px 24px;">' +
            '  <div style="width:68px; height:68px; border-radius:50%; background:#fffbeb; border:1px solid #fde68a; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">' +
            '    <i class="fa fa-clock-o" style="font-size:32px; color:#d97706;"></i>' +
            '  </div>' +
            '  <h4 style="font-weight:800; font-size:17px; color:#0f172a; margin:0 0 8px 0;">Kurikulum ' + $('<div>').text(res.prodi || '').html() + ' Belum Terverifikasi Resmi</h4>' +
            '  <p style="font-size:13.5px; color:#64748b; max-width:580px; margin:0 auto 10px auto; line-height:1.55;">' + $('<div>').text(res.msg || '').html() + '</p>' +
            gegeBtn +
            '</div>'
          );
          return;
        }

        currentMatrixPayload = res.payload;
        buildMatrixHtmlStructure(res);
        renderMatrixData(res.payload);
      },
      error: function(xhr, status, err) {
        $('#matrix_content_container').html(
          '<div class="matrix-card" style="text-align:center; padding:40px 20px; color:#dc2626;">' +
          '  <i class="fa fa-wifi" style="font-size:32px; margin-bottom:10px;"></i>' +
          '  <p style="font-weight:600;">Gagal menghubungi server: ' + err + '</p>' +
          '</div>'
        );
      }
    });
  }

  function buildMatrixHtmlStructure(res) {
    var p = res.payload || {};
    var mks = p.mata_kuliah || [];
    var pairsCount = p.effective_cpl_mk_pair_count !== undefined ? p.effective_cpl_mk_pair_count : (p.effective_cpl_mk_pairs || []).length;
    
    var totalSks = 0;
    mks.forEach(function(m) {
      totalSks += Number(m.sks_total || m.sks || 0);
    });

    _isDescModeActive = false;

    var html = '' +
      '<div class="matrix-stats-grid">' +
      '  <div class="matrix-stat-card">' +
      '    <div class="matrix-stat-icon teal"><i class="fa fa-book"></i></div>' +
      '    <div><div class="matrix-stat-num">' + mks.length + '</div><div class="matrix-stat-label">Total Mata Kuliah</div></div>' +
      '  </div>' +
      '  <div class="matrix-stat-card">' +
      '    <div class="matrix-stat-icon blue"><i class="fa fa-calculator"></i></div>' +
      '    <div><div class="matrix-stat-num">' + totalSks + ' SKS</div><div class="matrix-stat-label">Beban Studi Terdistribusi</div></div>' +
      '  </div>' +
      '  <div class="matrix-stat-card">' +
      '    <div class="matrix-stat-icon purple"><i class="fa fa-tags"></i></div>' +
      '    <div><div class="matrix-stat-num">' + pairsCount + '</div><div class="matrix-stat-label">Pemetaan Pasangan CPL-MK</div></div>' +
      '  </div>' +
      '  <div class="matrix-stat-card">' +
      '    <div class="matrix-stat-icon green"><i class="fa fa-shield"></i></div>' +
      '    <div><div class="matrix-stat-num" style="font-size:15px; color:#15803d;"><i class="fa fa-check-circle"></i> VERIFIED</div><div class="matrix-stat-label">Dokumen Kurikulum ' + $('<div>').text(res.tahun || '-').html() + '</div></div>' +
      '  </div>' +
      '</div>' +
      '<div class="matrix-card">' +
      '  <div style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">' +
      '    <div style="display:flex; gap:6px; flex-wrap:wrap;" id="mk_sem_filter_pills"></div>' +
      '    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">' +
      '      <button type="button" class="btn btn-default btn-sm" id="btn_toggle_desc_view" onclick="toggleModeRinciDeskripsi()" style="font-weight:700; font-size:12px; border-radius:6px; color:#334155; border-color:#cbd5e1; display:inline-flex; align-items:center; gap:6px;">' +
      '        <i class="fa fa-align-left text-primary"></i> <span id="lbl_toggle_desc">Tampilkan Deskripsi MK</span>' +
      '      </button>' +
      '      <input type="text" id="mk_search_input" onkeyup="searchMkMatrixTable()" placeholder="Cari kode atau nama mata kuliah..." class="form-control input-sm" style="width:230px; border-radius:6px;">' +
      '    </div>' +
      '  </div>' +
      '  <div id="mk_matrix_list_container"></div>' +
      '</div>';

    $('#matrix_content_container').html(html);
  }

  function renderMatrixData(payload) {
    if (!payload) return;

    var mks = payload.mata_kuliah || [];
    var cpls = payload.official_cpl || [];

    // Build CPL lookup dictionary with full metadata
    _cplLookupDict = {};
    cpls.forEach(function(c) {
      if (c.code) {
        _cplLookupDict[c.code] = {
          description: c.description || '',
          category: c.category || '',
          source_section: c.source_section || '',
          source_code: c.source_code || ''
        };
      }
    });

    _mkMatrixListAll = mks;

    // Build semester filter pills
    var semesters = [];
    _mkMatrixListAll.forEach(function(m) {
      var sem = m.semester || 0;
      if (semesters.indexOf(sem) === -1) semesters.push(sem);
    });
    semesters.sort(function(a, b) { return a - b; });

    var pillsHtml = '<button type="button" class="btn btn-xs btn-primary active" onclick="filterMkSemester(\'ALL\', this)" style="border-radius:6px; font-weight:700; padding:5px 12px; background:#0f766e; border-color:#0f766e;">Semua Semester</button>';
    semesters.forEach(function(s) {
      pillsHtml += ' <button type="button" class="btn btn-xs btn-default" onclick="filterMkSemester(\'SEM' + s + '\', this)" style="border-radius:6px; font-weight:600; padding:5px 12px;">Semester ' + s + '</button>';
    });
    $('#mk_sem_filter_pills').html(pillsHtml);

    _renderMatrixGroups(_mkMatrixListAll);
  }

  function getCplInfo(cp) {
    var item = _cplLookupDict[cp] || {};
    var desc = item.description || '';
    var cat = (item.category || '').toUpperCase();
    var sec = (item.source_section || '').toUpperCase();
    var cpUpper = (cp || '').toUpperCase();

    var catClass = 'cat-ku';
    var catName = 'Keterampilan Umum';
    var catLabel = '<span class="label" style="background:#ede9fe; color:#6d28d9; font-weight:700; font-size:11px;">Keterampilan Umum</span>';

    // Prioritas 1: Aspek Sikap & Tata Nilai
    if (cat === 'S' || sec.indexOf('SIKAP') !== -1 || cpUpper.startsWith('S-') || cpUpper.startsWith('SIKAP')) {
      catClass = 'cat-s';
      catName = 'Sikap & Tata Nilai';
      catLabel = '<span class="label" style="background:#dcfce7; color:#15803d; font-weight:700; font-size:11px;">Sikap &amp; Tata Nilai</span>';
    }
    // Prioritas 2: Aspek Pengetahuan
    else if (cat === 'P' || sec.indexOf('PENGETAHUAN') !== -1 || cpUpper.startsWith('P-')) {
      catClass = 'cat-p';
      catName = 'Pengetahuan';
      catLabel = '<span class="label" style="background:#fef9c3; color:#854d0e; font-weight:700; font-size:11px;">Pengetahuan</span>';
    }
    // Prioritas 3: Aspek Keterampilan Khusus
    else if (cat === 'KK' || sec.indexOf('KHUSUS') !== -1 || cpUpper.startsWith('KK-') || cpUpper.startsWith('KK') || cpUpper.startsWith('KS')) {
      catClass = 'cat-kk';
      catName = 'Keterampilan Khusus';
      catLabel = '<span class="label" style="background:#fce7f3; color:#9d174d; font-weight:700; font-size:11px;">Keterampilan Khusus</span>';
    }
    // Prioritas 4: Aspek Keterampilan Umum
    else if (cat === 'KU' || sec.indexOf('UMUM') !== -1 || cpUpper.startsWith('KU-')) {
      catClass = 'cat-ku';
      catName = 'Keterampilan Umum';
      catLabel = '<span class="label" style="background:#ede9fe; color:#6d28d9; font-weight:700; font-size:11px;">Keterampilan Umum</span>';
    }

    return {
      description: desc,
      category: cat,
      catClass: catClass,
      catName: catName,
      catLabel: catLabel
    };
  }

  function sortCpls(cplList) {
    if (!cplList || !cplList.length) return [];
    var orderWeight = { 'cat-s': 1, 'cat-p': 2, 'cat-kk': 3, 'cat-ku': 4 };
    return cplList.slice().sort(function(a, b) {
      var infoA = getCplInfo(a);
      var infoB = getCplInfo(b);
      var wA = orderWeight[infoA.catClass] || 5;
      var wB = orderWeight[infoB.catClass] || 5;
      if (wA !== wB) return wA - wB;
      return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
    });
  }

  function _renderMatrixGroups(list) {
    if (!list || list.length === 0) {
      $('#mk_matrix_list_container').html(
        '<div style="text-align:center; padding:40px; color:#94a3b8; font-size:13px;">' +
        '  <i class="fa fa-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>Tidak ada data Mata Kuliah terdeteksi.' +
        '</div>'
      );
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

      html += '<div class="mk-matrix-group" data-semester="SEM' + sem + '" style="margin-bottom:24px;">';
      html += '<div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">';
      html += '<div style="background:#0f172a; color:#f8fafc; font-weight:800; font-size:12px; padding:5px 14px; border-radius:20px; white-space:nowrap; display:flex; align-items:center; gap:6px;">';
      html += '<i class="fa fa-calendar-check-o text-warning"></i> ' + (sem > 0 ? 'Semester ' + sem : 'Semester Tidak Diketahui');
      html += '</div>';
      html += '<div style="font-size:11.5px; color:#475569; font-weight:600; background:#f1f5f9; padding:4px 12px; border-radius:20px; border:1px solid #e2e8f0;">';
      html += semMks.length + ' Mata Kuliah &bull; ' + semSks + ' SKS';
      html += '</div>';
      html += '<div style="flex:1; height:1px; background:#e2e8f0;"></div>';
      html += '</div>';

      html += '<div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; overflow:hidden;">';
      html += '<table class="table table-hover" style="margin-bottom:0; font-size:12.5px;">';
      html += '<thead><tr style="background:#f8fafc; color:#475569; border-bottom:2px solid #cbd5e1;">';
      html += '<th style="width:40px; text-align:center;">No</th>';
      html += '<th style="width:105px;">Kode MK</th>';
      html += '<th style="width:250px;">Nama Mata Kuliah</th>';
      html += '<th style="width:90px; text-align:center;">SKS</th>';
      html += '<th>CPL Terpetakan (SN-Dikti 2026)</th>';
      html += '<th style="width:150px; text-align:center;">Keselarasan Kurikulum</th>';
      html += '<th style="width:85px; text-align:center;">Aksi</th>';
      html += '</tr></thead>';
      html += '<tbody>';

      semMks.forEach(function(m, idx) {
        var cplBadges = '';
        var rawMappedCpls = m.direct_cpl_codes || m.mapped_cpls || m.cpls || [];
        var mappedCpls = sortCpls(rawMappedCpls);
        var hasCpl = mappedCpls.length > 0;

        if (hasCpl) {
          mappedCpls.forEach(function(cp) {
            var info = getCplInfo(cp);
            var titleAttr = info.description ? 'title="' + $('<div>').text(cp + ' [' + info.catName + ']: ' + info.description).html() + '"' : 'title="' + $('<div>').text(cp + ' [' + info.catName + ']').html() + '"';

            cplBadges += '<span class="cpl-tag-pill ' + info.catClass + '" ' + titleAttr + ' data-toggle="tooltip" data-placement="top">' + $('<div>').text(cp).html() + '</span>';
          });
        } else if (m.curriculum_alignment_status === 'COVERED_BY_AGGREGATE_TRACK') {
          cplBadges = '<span style="color:#7c3aed; font-size:11.5px; font-weight:600;"><i class="fa fa-cubes"></i> Terpetakan via Track Magang / Konsentrasi</span>';
        } else {
          cplBadges = '<span style="color:#ef4444; font-size:11.5px; font-weight:600; font-style:italic;"><i class="fa fa-exclamation-circle"></i> Belum ada pemetaan CPL</span>';
        }

        var sksT   = m.sks_teori   !== undefined ? m.sks_teori   : '-';
        var sksP   = m.sks_praktik !== undefined ? m.sks_praktik : '-';
        var sksL   = m.sks_lapangan !== undefined ? m.sks_lapangan : '-';
        var sksTot = m.sks_total !== undefined ? m.sks_total : (m.sks || 0);

        var score = m.curriculum_alignment_score !== undefined ? m.curriculum_alignment_score : (m.alignment_score !== undefined ? m.alignment_score : null);
        var status = m.curriculum_alignment_status || '';
        var badgeHtml = '';

        if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
          badgeHtml = '<span class="label" style="background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Dokumen kurikulum belum diaudit manual"><i class="fa fa-clock-o"></i> Belum diaudit manual</span>';
        } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
          badgeHtml = '<span class="label" style="background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Terhubung melalui Aggregate Track"><i class="fa fa-cubes"></i> Aggregate Track</span>';
        } else if (m.alignment_evidence && m.alignment_evidence.direct && m.alignment_evidence.direct.mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
          badgeHtml = '<span class="label" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Ada di matriks resmi, tetapi tidak diberi CPL"><i class="fa fa-exclamation-triangle"></i> 0% Di Matriks, Tanpa CPL</span>';
        } else if (m.alignment_evidence && m.alignment_evidence.direct && m.alignment_evidence.direct.mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
          badgeHtml = '<span class="label" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="MK aktif, tidak tercantum pada matriks CPL-MK resmi"><i class="fa fa-exclamation-circle"></i> 0% Di Luar Matriks</span>';
        } else if (score === null) {
          badgeHtml = '<span class="label" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Bukti parser belum lengkap"><i class="fa fa-clock-o"></i> Evidence Incomplete</span>';
        } else if (score >= 85) {
          badgeHtml = '<span class="label" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Very Strong Curriculum Evidence: ' + score + '%"><i class="fa fa-check-circle"></i> ' + score + '% Very Strong</span>';
        } else if (score >= 65) {
          badgeHtml = '<span class="label" style="background:#f0fdfa; color:#0f766e; border:1px solid #ccfbf1; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Strong Direct Curriculum Evidence: ' + score + '%"><i class="fa fa-check-circle"></i> ' + score + '% Direct Matrix Only</span>';
        } else if (score >= 35) {
          badgeHtml = '<span class="label" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Partial / Derived Curriculum Evidence: ' + score + '%"><i class="fa fa-info-circle"></i> ' + score + '% Partial Evidence</span>';
        } else if (score > 0) {
          badgeHtml = '<span class="label" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="Weak Curriculum Evidence: ' + score + '%"><i class="fa fa-times-circle"></i> ' + score + '% Weak Evidence</span>';
        } else {
          badgeHtml = '<span class="label" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; font-size:11px; padding:3px 8px; font-weight:700; border-radius:5px; display:inline-flex; align-items:center; gap:4px;" title="No Confirmed Official CPL-MK Alignment"><i class="fa fa-exclamation-triangle"></i> 0% No Official CPL</span>';
        }

        var kodeMkStr = m.kode_mk || m.code || '-';
        var namaMkStr = m.nama_mk || m.name || '-';
        var deskripsiStr = m.deskripsi_mk || m.deskripsi || 'Mata kuliah ini membekali mahasiswa dengan penguasaan kompetensi inti dan Capaian Pembelajaran Lulusan (CPL) program studi.';

        var searchStr = (kodeMkStr + ' ' + namaMkStr).toLowerCase();

        // Cari global index di _mkMatrixListAll
        var globalIndex = _mkMatrixListAll.indexOf(m);

        html += '<tr class="mk-matrix-row" data-semester="SEM' + sem + '" data-search="' + searchStr + '">';
        html += '<td style="text-align:center; font-weight:600; color:#94a3b8; vertical-align:middle;">' + (idx + 1) + '</td>';
        html += '<td style="vertical-align:middle;"><code style="font-size:12px; color:#0f766e; font-weight:700; background:#f0fdfa; padding:2px 6px; border-radius:4px; border:1px solid #ccfbf1;">' + $('<div>').text(kodeMkStr).html() + '</code></td>';
        html += '<td style="vertical-align:middle;"><strong style="color:#0f172a; font-size:13px; cursor:pointer;" onclick="bukaModalDetailMk(' + globalIndex + ')" title="Klik untuk melihat detail lengkap">' + $('<div>').text(namaMkStr).html() + '</strong></td>';
        html += '<td style="text-align:center; vertical-align:middle;"><strong style="color:#0f172a; font-size:13px;">' + sksTot + ' SKS</strong><br><small style="color:#64748b; font-size:10.5px;">(' + sksT + '-' + sksP + '-' + sksL + ')</small></td>';
        html += '<td style="vertical-align:middle;"><div style="line-height:1.6;">' + cplBadges + '</div></td>';
        html += '<td style="text-align:center; vertical-align:middle;">' + badgeHtml + '</td>';
        html += '<td style="text-align:center; vertical-align:middle;">';
        html += '  <button type="button" class="btn btn-default btn-xs" onclick="bukaModalDetailMk(' + globalIndex + ')" style="font-weight:700; font-size:11px; border-radius:5px; color:#0f766e; border-color:#ccfbf1; background:#f0fdfa; padding:4px 8px; display:inline-flex; align-items:center; gap:4px;" title="Lihat Kode MK, Nama MK, Deskripsi & Bukti Kurikulum">';
        html += '    <i class="fa fa-info-circle"></i> Detail';
        html += '  </button>';
        html += '</td>';
        html += '</tr>';

        // Row Deskripsi Tersembunyi (Expandable)
        var descDisplay = _isDescModeActive ? '' : 'none';
        html += '<tr class="mk-desc-row" data-semester="SEM' + sem + '" style="display:' + descDisplay + '; background:#fbfcfe;">';
        html += '  <td></td>';
        html += '  <td colspan="6" style="padding:10px 14px; border-top:none; border-bottom:1px solid #e2e8f0;">';
        html += '    <div style="background:#f8fafc; border-left:3px solid #0f766e; padding:8px 12px; border-radius:0 6px 6px 0; font-size:12px; color:#334155; line-height:1.55;">';
        html += '      <strong style="color:#0f766e; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px;"><i class="fa fa-file-text-o"></i> Deskripsi Mata Kuliah:</strong> ';
        html += '      <span>' + $('<div>').text(deskripsiStr).html() + '</span>';
        html += '    </div>';
        html += '  </td>';
        html += '</tr>';
      });

      html += '</tbody></table></div></div>';
    });

    $('#mk_matrix_list_container').html(html);

    // Init tooltips
    if ($.fn.tooltip) {
      $('[data-toggle="tooltip"]').tooltip({ container: 'body' });
    }
  }

  function filterMkSemester(sem, btn) {
    $('#mk_sem_filter_pills button').removeClass('btn-primary active').addClass('btn-default').css({'background': '', 'border-color': ''});
    $(btn).removeClass('btn-default').addClass('btn-primary active').css({'background': '#0f766e', 'border-color': '#0f766e'});

    $('#mk_search_input').val('');

    if (sem === 'ALL') {
      _renderMatrixGroups(_mkMatrixListAll);
    } else {
      var filtered = _mkMatrixListAll.filter(function(m) {
        return 'SEM' + (m.semester || 0) === sem;
      });
      _renderMatrixGroups(filtered);
    }
  }

  function toggleModeRinciDeskripsi() {
    _isDescModeActive = !_isDescModeActive;
    if (_isDescModeActive) {
      $('.mk-desc-row').fadeIn(150);
      $('#lbl_toggle_desc').text('Sembunyikan Deskripsi MK');
      $('#btn_toggle_desc_view').addClass('btn-primary').removeClass('btn-default').css({'background': '#0f766e', 'border-color': '#0f766e', 'color': '#ffffff'});
      $('#btn_toggle_desc_view i').removeClass('text-primary').css('color', '#ffffff');
    } else {
      $('.mk-desc-row').fadeOut(150);
      $('#lbl_toggle_desc').text('Tampilkan Deskripsi MK');
      $('#btn_toggle_desc_view').removeClass('btn-primary').addClass('btn-default').css({'background': '', 'border-color': '', 'color': '#334155'});
      $('#btn_toggle_desc_view i').addClass('text-primary').css('color', '');
    }
  }

  function bukaModalDetailMk(idx) {
    var m = _mkMatrixListAll[idx];
    if (!m) return;

    var kodeMk = m.kode_mk || m.code || '-';
    var namaMk = m.nama_mk || m.name || '-';
    var sem    = m.semester ? 'Semester ' + m.semester : 'Semester -';
    var sksTot = (m.sks_total !== undefined ? m.sks_total : (m.sks || 0)) + ' SKS';
    var sksT   = m.sks_teori !== undefined ? m.sks_teori : '-';
    var sksP   = m.sks_praktik !== undefined ? m.sks_praktik : '-';
    var sksL   = m.sks_lapangan !== undefined ? m.sks_lapangan : '-';
    var sksRinci = sksTot + ' (' + sksT + 'T - ' + sksP + 'P - ' + sksL + 'L)';
    
    var deskripsi = m.deskripsi_mk || m.deskripsi || 'Mata kuliah ini membekali mahasiswa dengan penguasaan kompetensi inti dan Capaian Pembelajaran Lulusan (CPL) program studi sesuai standar kurikulum SN-Dikti 2026.';
    var rawMappedCpls = m.direct_cpl_codes || m.mapped_cpls || m.cpls || [];
    var mappedCpls = sortCpls(rawMappedCpls);
    var hasCpl = mappedCpls.length > 0;

    var score = m.curriculum_alignment_score !== undefined ? m.curriculum_alignment_score : (m.alignment_score !== undefined ? m.alignment_score : null);
    var status = m.curriculum_alignment_status || '';
    var verdict = m.alignment_verdict || (score !== null ? (score >= 85 ? 'Very Strong Curriculum Evidence' : 'Curriculum Evidence') : 'Evidence Incomplete');
    
    var badgeClass = 'label-danger';
    if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
      badgeClass = 'label-default';
    } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
      badgeClass = 'label-info';
    } else if (score >= 85) {
      badgeClass = 'label-success';
    } else if (score >= 65) {
      badgeClass = 'label-info';
    } else if (score >= 35) {
      badgeClass = 'label-warning';
    } else if (score === null) {
      badgeClass = 'label-default';
    }
    
    $('#modal_mk_kode').text(kodeMk);
    $('#modal_mk_nama').text(namaMk);
    $('#modal_mk_semester').text(sem);
    $('#modal_mk_sks').text(sksRinci);
    $('#modal_mk_deskripsi').text(deskripsi);
    $('#modal_mk_cpl_count').text(mappedCpls.length + ' CPL Terpetakan');

    // Section 1: Alignment Score & Dual Evidence Breakdown (Phase 1.7)
    var scoreLabelText = (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') ? 'Belum Diaudit Manual' : (score !== null ? (verdict + ' (' + score + '%)') : verdict);
    $('#modal_mk_verdict_badge').html('<span class="label ' + badgeClass + '" style="font-size:12px; font-weight:700; padding:4px 10px; border-radius:6px;"><i class="fa fa-shield"></i> ' + scoreLabelText + '</span>');
    
    var barGrad = (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') ? '#94a3b8' : ((status === 'COVERED_BY_AGGREGATE_TRACK') ? 'linear-gradient(90deg, #7c3aed 0%, #a855f7 100%)' : ((score === null) ? '#94a3b8' : ((score >= 85) ? 'linear-gradient(90deg, #0f766e 0%, #16a34a 100%)' : ((score >= 65) ? 'linear-gradient(90deg, #2563eb 0%, #0f766e 100%)' : ((score >= 35) ? 'linear-gradient(90deg, #d97706 0%, #f59e0b 100%)' : '#ef4444')))));
    var barWidth = (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') ? 0 : ((status === 'COVERED_BY_AGGREGATE_TRACK') ? 100 : (score !== null ? (score === 0 ? 100 : score) : 50));
    $('#modal_mk_score_bar').css({'width': barWidth + '%', 'background': barGrad});

    // Dual Evidence Breakdown Cards
    var ev = m.alignment_evidence || {};
    var dirEv = ev.direct || {};
    var bkEv = ev.bk_path || {};

    if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
      $('#modal_ev_direct_score').text('Pending Audit');
      $('#modal_ev_direct_status').html('<span style="color:#64748b;"><i class="fa fa-clock-o"></i> Menunggu audit manual kurikulum</span>');
      $('#modal_ev_bk_score').text('Pending Audit');
      $('#modal_ev_bk_status').html('<span style="color:#64748b;"><i class="fa fa-clock-o"></i> Menunggu audit manual kurikulum</span>');
    } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
      $('#modal_ev_direct_score').text('Track Magang');
      $('#modal_ev_direct_status').html('✓ Terhubung melalui Aggregate Track');
      $('#modal_ev_bk_score').text('Modul Terpadu');
      $('#modal_ev_bk_status').html('✓ Kurikulum Merdeka / Industri');
    } else {
      var dirScoreTxt = (dirEv.score !== null && dirEv.score !== undefined) ? (dirEv.score + ' / 65') : '- / 65';
      $('#modal_ev_direct_score').text(dirScoreTxt);
      if (dirEv.score === 65) {
        $('#modal_ev_direct_status').html('<span style="color:#15803d;"><i class="fa fa-check-circle"></i> ✓ Official Direct Matrix (' + mappedCpls.length + ' CPL)</span>');
      } else if (dirEv.mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
        $('#modal_ev_direct_status').html('<span style="color:#b91c1c;"><i class="fa fa-exclamation-triangle"></i> Ada di matriks, tetapi 0 CPL</span>');
      } else if (dirEv.mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
        $('#modal_ev_direct_status').html('<span style="color:#b91c1c;"><i class="fa fa-exclamation-circle"></i> MK aktif di luar matriks CPL resmi</span>');
      } else {
        $('#modal_ev_direct_status').html('<span style="color:#b91c1c;"><i class="fa fa-times-circle"></i> Belum ada pemetaan CPL resmi</span>');
      }

      var bkScoreTxt = (bkEv.score !== null && bkEv.score !== undefined) ? (bkEv.score + ' / 35') : '- / 35';
      $('#modal_ev_bk_score').text(bkScoreTxt);
      var supCount = (bkEv.supported_cpl_codes || []).length;
      var unsupCount = (bkEv.unsupported_cpl_codes || []).length;
      if (hasCpl && supCount === mappedCpls.length) {
        $('#modal_ev_bk_status').html('<span style="color:#15803d;"><i class="fa fa-check-circle"></i> ✓ ' + supCount + ' of ' + mappedCpls.length + ' CPL Didukung BK</span>');
      } else if (hasCpl && supCount > 0) {
        $('#modal_ev_bk_status').html('<span style="color:#b45309;"><i class="fa fa-info-circle"></i> ✓ ' + supCount + ' of ' + mappedCpls.length + ' CPL Didukung BK (' + unsupCount + ' unsupported)</span>');
      } else if (hasCpl) {
        $('#modal_ev_bk_status').html('<span style="color:#64748b;"><i class="fa fa-tag"></i> Pemetaan matriks kurikulum langsung</span>');
      } else {
        $('#modal_ev_bk_status').html('<span style="color:#94a3b8;"><i class="fa fa-minus"></i> Tidak ada CPL untuk diverifikasi BK</span>');
      }
    }

    var descText = '';
    if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
      descText = 'Dokumen kurikulum dan matriks CPL-MK program studi ini saat ini berstatus <strong>PENDING (Belum Diaudit Manual)</strong>. Penilaian keselarasan resmi akan dihitung dan disajikan setelah audit manual kurikulum selesai dilakukan.';
    } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
      descText = 'Mata kuliah ini merupakan bagian dari <strong>Aggregate Track (Magang Industri / Jalur Konsentrasi)</strong>. Pemetaan CPL dan pencapaian kompetensi diampu secara terintegrasi melalui modul track resmi program studi.';
    } else if (!hasCpl) {
      if (dirEv.mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
        descText = '<span style="color:#b91c1c; font-weight:600;"><i class="fa fa-exclamation-triangle"></i> Mata kuliah ini tercantum dalam Matriks CPL-MK resmi program studi, namun tidak dibebankan CPL (0 CPL tercentang). Perlu reviu kurikulum oleh pengelola prodi.</span>';
      } else if (dirEv.mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
        descText = '<span style="color:#b91c1c; font-weight:600;"><i class="fa fa-exclamation-circle"></i> Mata kuliah aktif pada kurikulum berjalan, namun tidak tercantum dalam Matriks CPL-MK resmi dokumen kurikulum prodi.</span>';
      } else {
        descText = '<span style="color:#b91c1c; font-weight:600;"><i class="fa fa-exclamation-triangle"></i> Mata kuliah ini belum memiliki pemetaan Capaian Pembelajaran Lulusan (CPL) dalam matriks resmi kurikulum prodi.</span>';
      }
    } else {
      descText = 'Mata kuliah ini memiliki Skor Keselarasan Kurikulum sebesar <strong>' + score + '%</strong> (Direct Matrix: ' + (dirEv.score || 0) + ' pt, BK Path: ' + (bkEv.score || 0) + ' pt). ';
      if (score >= 85) {
        descText += 'Memiliki bukti kurikulum sangat kuat yang didukung matriks resmi dan modul Bahan Kajian (Body of Knowledge).';
      } else {
        descText += 'Didukung bukti matriks kurikulum resmi dengan sebagian CPL terjustifikasi langsung via Bahan Kajian.';
      }
    }
    $('#modal_mk_score_desc').html(descText);

    // Render Supporting BKs
    var bksList = m.supported_bks || [];
    var bksHtml = '';
    if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
      bksHtml = '<span style="font-size:11px; color:#64748b; font-style:italic;"><i class="fa fa-clock-o"></i> Menunggu proses audit manual Bahan Kajian prodi.</span>';
    } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
      bksHtml = '<span class="label" style="background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe; font-size:11px; padding:3px 8px; border-radius:4px; font-weight:600;"><i class="fa fa-briefcase"></i> Modul Praktik Terpadu Magang &amp; Industri</span>';
    } else if (!hasCpl) {
      bksHtml = '<span style="font-size:11px; color:#ef4444; font-style:italic;"><i class="fa fa-info-circle"></i> Tidak ada pemetaan CPL/Bahan Kajian untuk mata kuliah ini.</span>';
    } else if (bksList.length > 0) {
      bksList.forEach(function(b) {
        var bCode = b.code || b.kode_bk || '';
        var bName = b.name || b.nama_bk || bCode;
        bksHtml += '<span class="label" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:11px; padding:3px 8px; border-radius:4px; font-weight:600;"><i class="fa fa-cube"></i> ' + $('<div>').text(bCode + ': ' + bName).html() + '</span> ';
      });
    } else {
      bksHtml = '<span style="font-size:11px; color:#64748b; font-style:italic;">Memanfaatkan modul keilmuan dan internalisasi karakter/tata nilai kurikulum institusi.</span>';
    }
    $('#modal_mk_bks_container').html(bksHtml);

    // Section 3: CPL Rows with BK Support Status column
    var cplJustMap = m.cpl_justifications || {};
    var tbodyHtml = '';
    if (status === 'NOT_YET_VALIDATED' || m.manual_validation_status === 'PENDING') {
      tbodyHtml = '<tr><td colspan="4" style="text-align:center; color:#64748b; font-weight:600; padding:22px; background:#f8fafc;"><i class="fa fa-clock-o"></i> Dokumen kurikulum dan pemetaan CPL program studi ini belum diaudit manual.</td></tr>';
    } else if (hasCpl) {
      mappedCpls.forEach(function(cp) {
        var info = getCplInfo(cp);
        var justInfo = cplJustMap[cp] || {};
        var isSup = justInfo.is_supported_bk;
        var statusLabel = justInfo.status_label || (isSup ? 'Didukung Bahan Kajian' : 'Pemetaan Matriks Langsung');
        var supBks = justInfo.supporting_bks || [];

        var justColHtml = '';
        if (info.catClass === 'cat-s' || justInfo.status === 'DIRECT_VALUE') {
          justColHtml = '<span class="label" style="background:#f0fdf4; color:#16a34a; font-weight:700; font-size:11px; padding:3px 7px; border-radius:4px; border:1px solid #dcfce7;"><i class="fa fa-check-circle"></i> ' + $('<div>').text(statusLabel).html() + '</span><div style="font-size:11px; color:#64748b; margin-top:3px;">Internalisasi Sikap &amp; Karakter</div>';
        } else if (supBks.length > 0) {
          var bkNames = supBks.map(function(sb){ return sb.code + ' (' + sb.name + ')'; }).join(', ');
          justColHtml = '<span class="label" style="background:#dcfce7; color:#15803d; font-weight:700; font-size:11px; padding:3px 7px; border-radius:4px; border:1px solid #bbf7d0;"><i class="fa fa-check-circle"></i> ' + $('<div>').text(statusLabel).html() + '</span><div style="font-size:11px; color:#475569; margin-top:3px; line-height:1.4;"><i class="fa fa-cube text-primary"></i> ' + $('<div>').text(bkNames).html() + '</div>';
        } else {
          justColHtml = '<span class="label" style="background:#f0fdfa; color:#0f766e; font-weight:700; font-size:11px; padding:3px 7px; border-radius:4px; border:1px solid #ccfbf1;"><i class="fa fa-check-circle"></i> ' + $('<div>').text(statusLabel).html() + '</span><div style="font-size:11px; color:#64748b; margin-top:3px;">Pemetaan Matriks Kurikulum</div>';
        }

        tbodyHtml += '<tr>';
        tbodyHtml += '  <td style="vertical-align:top;"><span class="cpl-tag-pill ' + info.catClass + '" style="font-size:11.5px; padding:3px 8px;">' + $('<div>').text(cp).html() + '</span></td>';
        tbodyHtml += '  <td style="vertical-align:top;">' + info.catLabel + '</td>';
        tbodyHtml += '  <td style="color:#1e293b; line-height:1.55; vertical-align:top;">' + $('<div>').text(info.description || 'Deskripsi CPL terpetakan pada dokumen kurikulum prodi.').html() + '</td>';
        tbodyHtml += '  <td style="vertical-align:top;">' + justColHtml + '</td>';
        tbodyHtml += '</tr>';
      });
    } else if (status === 'COVERED_BY_AGGREGATE_TRACK') {
      tbodyHtml = '<tr><td colspan="4" style="text-align:center; color:#6d28d9; font-weight:600; padding:22px; background:#f5f3ff;"><i class="fa fa-cubes"></i> Mata kuliah ini terhubung dan diampu melalui kurikulum Aggregate Track (Magang Industri).</td></tr>';
    } else {
      tbodyHtml = '<tr><td colspan="4" style="text-align:center; color:#b91c1c; font-weight:600; padding:22px; background:#fef2f2;"><i class="fa fa-exclamation-triangle"></i> Belum ada Capaian Pembelajaran Lulusan (CPL) yang dipetakan pada mata kuliah ini dalam matriks kurikulum resmi.</td></tr>';
    }

    $('#modal_mk_cpl_tbody').html(tbodyHtml);
    $('#modalDetailMkCpl').modal('show');
  }

  function searchMkMatrixTable() {
    var query = $.trim($('#mk_search_input').val()).toLowerCase();
    if (!query) {
      $('.mk-matrix-row').show();
      if (_isDescModeActive) {
        $('.mk-desc-row').show();
      } else {
        $('.mk-desc-row').hide();
      }
      $('.mk-matrix-group').show();
      return;
    }
    $('.mk-matrix-row').each(function() {
      var searchStr = $(this).attr('data-search') || '';
      var matched = (searchStr.indexOf(query) !== -1);
      $(this).toggle(matched);
      // Toggle associated desc row if any
      var nextDescRow = $(this).next('.mk-desc-row');
      if (nextDescRow.length) {
        if (matched && _isDescModeActive) {
          nextDescRow.show();
        } else {
          nextDescRow.hide();
        }
      }
    });
    $('.mk-matrix-group').each(function() {
      var visible = $(this).find('.mk-matrix-row:visible').length;
      $(this).toggle(visible > 0);
    });
  }
</script>

