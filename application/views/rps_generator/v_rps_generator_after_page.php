<!-- Marked.js & SweetAlert2 for rendering and alert dialogs -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="<?= base_url('assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js'); ?>"></script>
<script>
  if (typeof Swal === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
  }
</script>

<script>
  // State Storage for Extracted Documents
  var uploadedDocs = {
    kurikulum: '',
    penelitian: '',
    rps_lama: '',
    excel_pertemuan_md: '',
    excel_matrix_json: '',
    excel_filename: ''
  };

  /**
   * Helper: Bersihkan tag HTML, spasi ganda, dan entity menjadi teks bersih
   */
  function cleanHtmlPlainText(raw) {
    if (!raw) return '';
    var text = String(raw);
    text = text.replace(/\\r/g, '').replace(/\\n/g, '\n').replace(/\\t/g, ' ');
    text = text.replace(/<!--\[if[\s\S]*?\]>[\s\S]*?<!\[endif\]-->/gi, '');
    text = text.replace(/<!--[\s\S]*?-->/g, '');
    text = text.replace(/<br\s*\/?>/gi, '\n');
    text = text.replace(/<\/(p|div|li|tr|h[1-6]|blockquote)>/gi, '\n');
    text = text.replace(/<(p|div|li|tr|h[1-6]|blockquote)[^>]*>/gi, '');
    text = text.replace(/<[^>]+>/g, '');
    
    var txtNode = document.createElement('textarea');
    txtNode.innerHTML = text;
    text = txtNode.value;
    
    text = text.replace(/\u00a0/g, ' ').replace(/&nbsp;/gi, ' ');
    text = text.replace(/\{\s*"1"\s*:\s*2\s*,\s*"2"\s*:\s*"([\s\S]*?)"\s*\}/g, '$1');
    text = text.replace(/\r\n|\r/g, '\n');
    return text.trim();
  }

  /**
   * Helper: Bersihkan dan rapikan teks deskripsi mata kuliah
   */
  function cleanDeskripsiText(raw) {
    if (!raw) return '';
    var text = String(raw);
    text = text.replace(/\\r/g, '').replace(/\\n/g, '\n').replace(/\\t/g, ' ');
    text = text.replace(/<!--\[if[\s\S]*?\]>[\s\S]*?<!\[endif\]-->/gi, '');
    text = text.replace(/<!--[\s\S]*?-->/g, '');
    text = text.replace(/<br\s*\/?>/gi, '\n');
    text = text.replace(/<\/(p|div|li|tr|h[1-6]|blockquote)>/gi, '\n\n');
    text = text.replace(/<(p|div|li|tr|h[1-6]|blockquote)[^>]*>/gi, '');
    text = text.replace(/<[^>]+>/g, '');
    
    var txtNode = document.createElement('textarea');
    txtNode.innerHTML = text;
    text = txtNode.value;
    
    text = text.replace(/\u00a0/g, ' ').replace(/&nbsp;/gi, ' ');
    text = text.replace(/\{\s*"1"\s*:\s*2\s*,\s*"2"\s*:\s*"([\s\S]*?)"\s*\}/g, '$1');
    text = text.replace(/\r\n|\r/g, '\n');

    var paragraphs = text.split('\n\n');
    var out = [];
    for (var i = 0; i < paragraphs.length; i++) {
      var cleanP = paragraphs[i].replace(/\s+/g, ' ').trim();
      if (cleanP !== '') {
        out.push(cleanP);
      }
    }
    return out.join('\n\n');
  }

  /**
   * Helper: Bersihkan dan rapikan teks referensi / pustaka (setiap nomor 1., 2., 3. selalu di baris baru)
   */
  function cleanReferensiText(raw) {
    if (!raw) return '';
    var text = String(raw);
    text = text.replace(/\\r/g, '').replace(/\\n/g, '\n').replace(/\\t/g, ' ');
    text = text.replace(/<!--\[if[\s\S]*?\]>[\s\S]*?<!\[endif\]-->/gi, '');
    text = text.replace(/<!--[\s\S]*?-->/g, '');
    text = text.replace(/<br\s*\/?>/gi, '\n');
    text = text.replace(/<\/(p|div|li|tr|h[1-6]|blockquote)>/gi, '\n');
    text = text.replace(/<(p|div|li|tr|h[1-6]|blockquote)[^>]*>/gi, '');
    text = text.replace(/<[^>]+>/g, '');
    
    var txtNode = document.createElement('textarea');
    txtNode.innerHTML = text;
    text = txtNode.value;
    
    text = text.replace(/\u00a0/g, ' ').replace(/&nbsp;/gi, ' ');
    text = text.replace(/\{\s*"1"\s*:\s*2\s*,\s*"2"\s*:\s*"([\s\S]*?)"\s*\}/g, '$1');
    text = text.replace(/\r\n|\r/g, '\n');
    
    // Pisahkan nomor urut 1., 2., 3., dst yang didahului tanda baca akhir (. , ) , ; , ])
    text = text.replace(/(\.)\s*(\d{1,2})\.\s*/g, '$1\n$2. ');
    text = text.replace(/(\))\s*(\d{1,2})\.\s*/g, '$1\n$2. ');
    text = text.replace(/(\;)\s*(\d{1,2})\.\s*/g, '$1\n$2. ');
    text = text.replace(/(\])\s*(\d{1,2})\.\s*/g, '$1\n$2. ');
    text = text.replace(/(?:\.|\;|\))\s*\[(\d{1,2})\]\s*/g, '.\n$1. ');
    text = text.replace(/(?:^|\n)\s*(\d{1,2})[\.\)]\s*/gm, '\n$1. ');
    text = text.replace(/(?:^|\n)\s*\[(\d{1,2})\]\s*/gm, '\n$1. ');

    var rawLines = text.split('\n');
    var items = [];
    var currentItem = '';

    for (var i = 0; i < rawLines.length; i++) {
      var lt = rawLines[i].replace(/\s+/g, ' ').trim();
      if (lt === '') continue;

      if (/^\d{1,2}\.\s+/.test(lt)) {
        if (currentItem !== '') {
          items.push(currentItem);
        }
        currentItem = lt;
      } else {
        if (currentItem !== '') {
          currentItem += ' ' + lt;
        } else {
          currentItem = lt;
        }
      }
    }
    if (currentItem !== '') {
      items.push(currentItem);
    }

    if (items.length <= 1 && currentItem !== '') {
      var entries = currentItem.split(/(?<=[\.\"\)\]])\s+(?=[A-Z][a-zA-Z\u00C0-\u017F\-\'\s]+,\s+[A-Z][a-zA-Z\u00C0-\u017F\-\'\s]+(?:\s+and\s+[A-Z][a-zA-Z\u00C0-\u017F\-\'\s]+)?\.\s+(?:\(\d{4}\)|\d{4}\.))/);
      if (entries.length <= 1) {
        entries = currentItem.split(/(?<=[\.\"\)\]])\s+(?=[A-Z][a-zA-Z\u00C0-\u017F\-\'\s]{2,40}\.\s+(?:\(\d{4}\)|\d{4}\.))/);
      }
      if (entries.length > 1) {
        items = [];
        for (var k = 0; k < entries.length; k++) {
          var et = entries[k].replace(/\s+/g, ' ').trim();
          if (et !== '') {
            items.push((k + 1) + '. ' + et);
          }
        }
      }
    }

    return items.join('\n');
  }

  var activeCplMkList = []; // Array of { kode, teks, role: 'PRIMARY'|'SUPPORTING', provenance, reasoning }
  var activeCplMkStatus = null; // null, 'DRAFT', 'APPROVED'
  var activeDeskripsiMk = '';
  var activeDeskripsiMkSource = 'CURRICULUM'; // 'CURRICULUM', 'AI_DRAFT', 'MANUAL'
  var activeReferensi = '';
  var cplStudioAllProdiList = []; // Array of all official CPLs from prodi curriculum
  var cplStudioMappedCandidates = []; // Array of CPL codes mapped to course in curriculum
  var cplStudioFilter = 'all'; // 'all', 'selected', 'recommended'

  var activeCpmkList = [];
  var activeCpmkStatus = null; // null, 'AI_PROPOSAL', 'APPROVED'
  var curriculumReadinessData = null;
  var curriculumReadinessCache = {};
  var readinessDebounceTimer = null;
  var readinessStepTimer = null;

  window.bukaModalApiKey = function() {
    $('#apiKeyModal').modal('show');
  };

  var currentMarkdownOutput = '';
  var currentMatrixJson = null;
  var currentRoadmapJson = null;

  // Per-mode document store (so switching tabs restores previously generated/loaded content)
  var modeDocStore = {
    studio_cpl: null,
    susun_cpmk: null,
    susun_rps: null,
    kontrak_kuliah: null,
    rancangan_tugas: null,
    roadmap_wbi: null,
    skenario_pembelajaran: null
  };

  // Per-mode prompt interaction history (Chat Stream)
  var modePromptHistory = {
    studio_cpl: [],
    susun_cpmk: [],
    susun_rps: [],
    kontrak_kuliah: [],
    rancangan_tugas: [],
    roadmap_wbi: [],
    skenario_pembelajaran: []
  };

  var activeMode = 'studio_cpl';

  // Metadata per Document Mode
  var modeTabLabels = {
    studio_cpl: {
      label: 'Deskripsi dan CPL',
      shortLabel: 'Deskripsi dan CPL',
      icon: 'fa-th-list',
      color: '#0284c7',
      emptyTitle: 'Deskripsi dan CPL',
      emptyDesc: 'Tinjau CPL resmi prodi, tentukan CPL yang dibebankan pada MK (Utama / Pendukung), susun deskripsi mata kuliah, dan cantumkan daftar referensi wajib.'
    },
    susun_cpmk: {
      label: 'CPMK',
      shortLabel: 'CPMK',
      icon: 'fa-bullseye',
      color: '#0d9488',
      emptyTitle: 'CPMK Belum Disusun',
      emptyDesc: 'Klik tombol Susun Draft CPMK untuk merumuskan Capaian Pembelajaran Mata Kuliah secara grounded berbasis CPL-MK yang telah disetujui dan taksonomi Bloom C2–C6.'
    },
    susun_subcpmk: {
      label: 'sub-cpmk',
      shortLabel: 'sub-cpmk',
      icon: 'fa-cubes',
      color: '#0284c7',
      emptyTitle: 'Sub-CPMK Belum Disusun',
      emptyDesc: 'Perumusan Sub-CPMK turunan dari butir CPMK.'
    },
    susun_rps: {
      label: 'RPS (16 Pertemuan)',
      shortLabel: 'RPS',
      icon: 'fa-book',
      color: '#065f46',
      emptyTitle: 'RPS 16 Pertemuan Siap Disusun',
      emptyDesc: 'Menyusun Capaian Pembelajaran (CPMK & Sub-CPMK), Matriks Evaluasi 16 Pertemuan, dan Referensi standar SN-Dikti 2026 & WBI.'
    },
    kontrak_kuliah: {
      label: 'kontrak kuliah',
      shortLabel: 'kontrak kuliah',
      icon: 'fa-handshake-o',
      color: '#1e40af',
      emptyTitle: 'Kontrak Kuliah Belum Dibuat',
      emptyDesc: 'Menyusun formulir kesepakatan tata tertib perkuliahan, kehadiran, dan pembobotan penilaian semester yang terhubung langsung dari RPS.'
    },
    rancangan_tugas: {
      label: 'rancangan tugas',
      shortLabel: 'rancangan tugas',
      icon: 'fa-tasks',
      color: '#6b21a8',
      emptyTitle: 'Rancangan Tugas Mahasiswa Belum Dibuat',
      emptyDesc: 'Klik tombol Susun / Generate Dokumen AI untuk menyusun panduan tugas terstruktur, luaran tugas, dan rubrik analitik berbasis RPS.'
    },
    roadmap_wbi: {
      label: 'roadmap',
      shortLabel: 'roadmap',
      icon: 'fa-sitemap',
      color: '#b45309',
      emptyTitle: 'Roadmap Pembelajaran Format WBI Belum Dibuat',
      emptyDesc: 'Memetakan modul topik, alokasi jam tatap muka & praktikum, serta target capaian per modul pembelajaran format WBI.'
    },
    skenario_pembelajaran: {
      label: 'Strategi Pertemuan & Draft Evaluasi',
      shortLabel: 'Strategi Pertemuan',
      icon: 'fa-calendar-check-o',
      color: '#0284c7',
      emptyTitle: 'Strategi Pertemuan Belum Dibuat',
      emptyDesc: 'Menurunkan RPS menjadi langkah strategi pembelajaran operasional (Pendahuluan, Inti/Kolaborasi Mahasiswa, Penutup/Refleksi) per pertemuan 1–16 serta draft evaluasi pertemuan.'
    }
  };

  /**
   * Auto-resize Textarea saat pengguna mengetik
   */
  function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = Math.max(58, Math.min(el.scrollHeight, 160)) + 'px';
  }

  /**
   * Toggle / Buka Tutup Riwayat Prompt
   */
  function togglePromptHistory() {
    var $list = $('#prompt_history_list');
    var $icon = $('#icon_toggle_history');
    var $label = $('#label_toggle_history');

    if ($list.is(':visible')) {
      $list.slideUp(150);
      $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
      $label.text('Buka');
    } else {
      $list.slideDown(150);
      $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
      $label.text('Tutup');
    }
  }

  /**
   * Render Riwayat Prompt untuk mode aktif
   */
  function renderPromptHistory(mode) {
    if (!mode) mode = activeMode || 'susun_rps';
    var history = modePromptHistory[mode] || [];
    var count = history.length;
    $('#prompt_history_count').text('(' + count + ' riwayat)');

    if (count === 0) {
      $('#history_empty_state').show();
      $('#prompt_history_list .rps-history-item').remove();
      $('#rps_history_panel').hide();
      return;
    }

    $('#rps_history_panel').show();
    $('#history_empty_state').hide();

    var html = '';
    history.forEach(function(item) {
      var timeStr = item.time || '';
      var promptSafe = escapeHtml(item.prompt || '');
      var statusBadge = (item.status === 'pending') 
        ? '<span style="color:#b45309;"><i class="fa fa-spinner fa-spin"></i> Sedang memproses revisi...</span>'
        : '<span style="color:#15803d;"><i class="fa fa-check-circle"></i> Selesai diterapkan</span>';

      html += '<div class="rps-history-item user-msg">'
            + '<div class="rps-history-meta">'
            + '<span><i class="fa fa-user-circle"></i> <strong>Instruksi Anda</strong> · ' + timeStr + '</span>'
            + '<span class="rps-history-reuse" onclick="reusePrompt(\'' + escapeJsString(item.prompt) + '\')"><i class="fa fa-repeat"></i> Gunakan Lagi</span>'
            + '</div>'
            + '<div class="rps-history-text">' + promptSafe + '</div>'
            + '<div style="margin-top:4px; font-size:10px; text-align:right;">' + statusBadge + '</div>'
            + '</div>';
    });

    $('#prompt_history_list').html(html);
    var el = document.getElementById('prompt_history_list');
    if (el) el.scrollTop = el.scrollHeight;
  }

  /**
   * Gunakan ulang prompt dari riwayat ke textarea
   */
  function reusePrompt(text) {
    var $ta = $('#mk_catatan');
    $ta.val(text);
    autoResizeTextarea($ta[0]);
    $ta.focus();
  }

  function escapeJsString(str) {
    return (str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n');
  }

  /**
   * Switch Tab Navigasi — Selalu bebas diklik kapanpun
   */
  /**
   * Switch Tab Navigasi — Selalu bebas diklik kapanpun
   */
  function switchTab(mode) {
    if (!mode) mode = 'studio_cpl';
    activeMode = mode;
    var meta = modeTabLabels[mode] || modeTabLabels['studio_cpl'];

    // Update active class pada tab navigasi
    $('.rps-tab-item').removeClass('active');
    $('#tab_' + mode).addClass('active');

    // Update label tombol command bar dan info footer
    $('#label_generate_mode').text(meta.shortLabel);
    $('#footer_active_doc_name').text(meta.label);

    // Render riwayat prompt untuk tab ini
    renderPromptHistory(mode);

    // 1. Skenario Khusus Mode: Studio CPL & Deskripsi MK
    if (mode === 'studio_cpl') {
      $('#rps_command_bar').hide();
      $('#btn_unduh_roadmap').hide();
      $('#btn_sync_jadwal_kelas').hide();
      $('#btn_unduh_evaluasi_excel').hide();
      $('#rps_continue_banner').hide();
      $('#canvas_output').hide();
      $('#canvas_loading').hide();
      $('#canvas_cpmk_view').hide();

      var hasCourse = Boolean($('#mk_nama').val() || $('#mk_kode').val());

      if (hasCourse) {
        $('#canvas_placeholder').hide();
        $('#canvas_cpl_studio_view').fadeIn(100);
        $('#canvas_tools').css('display', 'none');

        $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
        if (activeCplMkStatus === 'APPROVED') {
          $('#canvas_badge').attr('class', 'rps-tag tag-success').html('<i class="fa fa-check"></i> APPROVED');
        } else {
          $('#canvas_badge').attr('class', 'rps-tag tag-warning').text(activeCplMkStatus || 'DRAFT');
        }

        if (cplStudioAllProdiList.length === 0) {
          loadCplStudioData();
        } else {
          renderCplStudioUi();
        }
      } else {
        // Belum ada Mata Kuliah yang dipilih di sidebar
        $('#canvas_cpl_studio_view').hide();
        $('#canvas_tools').css('display', 'none');

        $('#empty_icon').html('<i class="fa ' + meta.icon + '"></i>');
        $('#empty_title').text(meta.emptyTitle || 'Studio CPL & Deskripsi Mata Kuliah');
        $('#empty_desc').html('Silakan cari dan pilih <strong>Mata Kuliah</strong> pada panel kiri terlebih dahulu untuk memuat kurikulum resmi prodi, menentukan CPL yang dibebankan, serta menyusun deskripsi mata kuliah.');
        $('#btn_empty_generate').html('<i class="fa fa-search"></i> Cari Mata Kuliah di Panel Kiri').prop('disabled', false);

        $('#canvas_placeholder').fadeIn(100);
        $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
        $('#canvas_badge').attr('class', 'rps-tag').text('Menunggu Pilihan MK');
      }
      return;
    }

    // 2. Skenario Khusus Mode: CPMK Studio
    if (mode === 'susun_cpmk') {
      $('#rps_command_bar').hide();
      $('#canvas_cpl_studio_view').hide();
      $('#btn_unduh_roadmap').hide();
      $('#btn_sync_jadwal_kelas').hide();
      $('#btn_unduh_evaluasi_excel').hide();
      $('#rps_continue_banner').hide();
      $('#canvas_output').hide();
      $('#canvas_loading').hide();

      var hasCourse = Boolean($('#mk_nama').val() || $('#mk_kode').val());
      if (hasCourse) {
        $('#canvas_placeholder').hide();
        $('#canvas_cpmk_view').fadeIn(100);
        $('#canvas_tools').css('display', 'none');

        $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
        if (activeCpmkStatus === 'APPROVED') {
          $('#canvas_badge').attr('class', 'rps-tag tag-success').html('<i class="fa fa-check"></i> APPROVED');
        } else {
          $('#canvas_badge').attr('class', 'rps-tag tag-warning').text(activeCpmkStatus || 'DRAFT');
        }
        loadCpmkStudioData();
      } else {
        $('#canvas_cpmk_view').hide();
        $('#canvas_tools').css('display', 'none');
        $('#empty_icon').html('<i class="fa ' + meta.icon + '"></i>');
        $('#empty_title').text(meta.emptyTitle);
        $('#empty_desc').html('Silakan cari dan pilih <strong>Mata Kuliah</strong> pada panel kiri terlebih dahulu untuk mengakses Studio CPMK.');
        $('#btn_empty_generate').html('<i class="fa fa-search"></i> Cari Mata Kuliah di Panel Kiri').prop('disabled', false);
        $('#canvas_placeholder').fadeIn(100);
        $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
        $('#canvas_badge').attr('class', 'rps-tag').text('Menunggu Pilihan MK');
      }
      updateWorkflowStepper('susun_cpmk');
      return;
    }

    // Untuk tab dokumen/naskah lainnya, tampilkan command bar dan sembunyikan view Studio CPL & CPMK
    $('#rps_command_bar').show();
    $('#canvas_cpl_studio_view').hide();
    $('#canvas_cpmk_view').hide();

    var store = modeDocStore[mode];
    if (store && store.markdown) {
      // Dokumen ada isinya -> Tampilkan output
      currentMarkdownOutput = store.markdown;
      currentMatrixJson = store.matrixJson;
      currentRoadmapJson = store.roadmapJson;

      $('#rendered_markdown').html(store.html);
      $('#canvas_placeholder').hide();
      $('#canvas_loading').hide();
      $('#canvas_output').fadeIn(100);
      $('#canvas_tools').css('display', 'flex');

      $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
      $('#canvas_badge').attr('class', 'rps-tag tag-success').text('Selesai');
      $('#btn_submit_generate').html('<i class="fa fa-magic" style="font-size:15px;"></i><span style="line-height:1.2;">Perbarui <strong id="label_generate_mode">' + meta.shortLabel + '</strong></span>');

      if (store.roadmapJson) {
        $('#btn_unduh_roadmap').show();
      } else {
        $('#btn_unduh_roadmap').hide();
      }

      if (mode === 'skenario_pembelajaran' && store.markdown) {
        $('#btn_sync_jadwal_kelas').show();
      } else {
        $('#btn_sync_jadwal_kelas').hide();
      }

      if (mode === 'evaluasi_cpmk') {
        $('#btn_unduh_evaluasi_excel').show();
      } else {
        $('#btn_unduh_evaluasi_excel').hide();
      }

      if (store.isTruncated) {
        $('#rps_continue_banner').fadeIn(150);
      } else {
        $('#rps_continue_banner').hide();
      }
    } else {
      // Dokumen belum dibuat -> Tampilkan placeholder ramah pengguna untuk tab ini
      currentMarkdownOutput = '';
      $('#canvas_output').hide();
      $('#canvas_loading').hide();
      $('#rps_continue_banner').hide();
      $('#btn_sync_jadwal_kelas').hide();

      if (mode === 'evaluasi_cpmk') {
        $('#canvas_tools').css('display', 'flex');
        $('#btn_unduh_evaluasi_excel').show();
      } else {
        $('#canvas_tools').css('display', 'none');
        $('#btn_unduh_evaluasi_excel').hide();
      }

      var hasRpsMaster = (modeDocStore['susun_rps'] && modeDocStore['susun_rps'].markdown);
      var displayDesc = meta.emptyDesc;
      if (mode !== 'susun_rps' && hasRpsMaster) {
        displayDesc += '<span style="display:block; margin-top:6px; color:#059669; font-weight:600;"><i class="fa fa-check-circle"></i> Terkoneksi ke Dokumen RPS: Komponen & bobot asesmen, CPMK, serta jadwal 16 minggu akan otomatis diselaraskan secara presisi.</span>';
      }

      $('#empty_icon').html('<i class="fa ' + meta.icon + '"></i>');
      $('#empty_title').text(meta.emptyTitle);

      // Tampilkan pesan berbeda jika RPS mode dan CPL / CPMK belum approved
      var isRpsMode = (typeof RPS_GENERATION_MODES !== 'undefined' && RPS_GENERATION_MODES.indexOf(mode) !== -1);
      var cplApproved = (activeCplMkStatus === 'APPROVED');
      var cpmkApproved = (activeCpmkStatus === 'APPROVED');

      if (isRpsMode && !cplApproved) {
        displayDesc = '<span style="color:#b45309; font-weight:600; font-size:12px;"><i class="fa fa-lock"></i> CPL Mata Kuliah harus disetujui (APPROVED) di Studio CPL terlebih dahulu.</span>'
                    + '<br><span style="font-size:11.5px; color:#64748b;">Klik tab <strong>Studio CPL &amp; Deskripsi</strong>, tentukan CPL-MK dan setujui untuk melanjutkan.</span>';
      } else if (isRpsMode && !cpmkApproved) {
        displayDesc = '<span style="color:#b45309; font-weight:600; font-size:12px;"><i class="fa fa-lock"></i> CPMK harus disetujui (APPROVED) sebelum ' + meta.shortLabel + ' dapat digenerate.</span>'
                    + '<br><span style="font-size:11.5px; color:#64748b;">Klik tab <strong>CPMK Studio</strong>, edit dan setujui CPMK terlebih dahulu.</span>';
      }
      $('#empty_desc').html(displayDesc);
      $('#btn_empty_generate').html('<i class="fa fa-magic"></i> Generate ' + meta.shortLabel);

      $('#canvas_placeholder').fadeIn(100);
      $('#canvas_title').html('<i class="fa ' + meta.icon + '" style="color:' + meta.color + '"></i> ' + meta.label);
      if (mode !== 'susun_rps' && hasRpsMaster) {
        $('#canvas_badge').attr('class', 'rps-tag tag-info').html('<i class="fa fa-link"></i> Selaras dengan RPS');
      } else {
        $('#canvas_badge').attr('class', 'rps-tag').text('Belum Dibuat');
      }
      $('#btn_submit_generate').html('<i class="fa fa-magic" style="font-size:15px;"></i><span style="line-height:1.2;">Generate <strong id="label_generate_mode">' + meta.shortLabel + '</strong></span>');
      $('#btn_unduh_roadmap').hide();
    }

    // Update tombol Generate berdasarkan status CPMK untuk mode ini
    updateGenerateBtnState(mode);
    updateWorkflowStepper(mode);
  }

  /**
   * Helper untuk update status visual workflow stepper
   */
  function updateWorkflowStepper(currentMode) {
    if (!currentMode) currentMode = activeMode || 'studio_cpl';
    var isCplApproved = (activeCplMkStatus === 'APPROVED');
    var isCpmkApproved = (activeCpmkStatus === 'APPROVED');
    var hasRps = !!(modeDocStore['susun_rps'] && modeDocStore['susun_rps'].markdown);

    // Step 1: CPL Studio
    var $s1 = $('#step_cpl_studio');
    $s1.removeClass('active completed');
    if (currentMode === 'studio_cpl') {
      $s1.addClass('active').css({ color: '#0f766e', fontWeight: '700' });
      $s1.find('.rps-step-num').css({ background: '#0f766e', color: '#fff' }).html('1');
    } else if (isCplApproved) {
      $s1.addClass('completed').css({ color: '#16a34a', fontWeight: '600' });
      $s1.find('.rps-step-num').css({ background: '#16a34a', color: '#fff' }).html('<i class="fa fa-check"></i>');
    } else {
      $s1.css({ color: '#64748b', fontWeight: '600' });
      $s1.find('.rps-step-num').css({ background: '#cbd5e1', color: '#475569' }).html('1');
    }

    // Step 2: CPMK Studio
    var $s2 = $('#step_cpmk_studio');
    $s2.removeClass('active completed locked');
    if (currentMode === 'susun_cpmk') {
      $s2.addClass('active').css({ color: '#0d9488', fontWeight: '700' });
      $s2.find('.rps-step-num').css({ background: '#0d9488', color: '#fff' }).html('2');
    } else if (isCpmkApproved) {
      $s2.addClass('completed').css({ color: '#16a34a', fontWeight: '600' });
      $s2.find('.rps-step-num').css({ background: '#16a34a', color: '#fff' }).html('<i class="fa fa-check"></i>');
    } else if (!isCplApproved) {
      $s2.addClass('locked').css({ color: '#94a3b8', fontWeight: '500' });
      $s2.find('.rps-step-num').css({ background: '#f1f5f9', color: '#94a3b8' }).html('<i class="fa fa-lock"></i>');
    } else {
      $s2.css({ color: '#64748b', fontWeight: '600' });
      $s2.find('.rps-step-num').css({ background: '#cbd5e1', color: '#475569' }).html('2');
    }

    // Step 3: Sub-CPMK Studio (Phase 2 - Locked)
    var $s3 = $('#step_subcpmk_studio');
    $s3.css({ color: '#94a3b8', fontWeight: '500' });
    $s3.find('.rps-step-num').css({ background: '#f1f5f9', color: '#94a3b8' }).html('<i class="fa fa-lock"></i>');

    // Step 4: RPS Lengkap
    var $s4 = $('#step_rps_lengkap');
    $s4.removeClass('active completed locked');
    if (currentMode === 'susun_rps') {
      $s4.addClass('active').css({ color: '#065f46', fontWeight: '700' });
      $s4.find('.rps-step-num').css({ background: '#065f46', color: '#fff' }).html('4');
    } else if (hasRps) {
      $s4.addClass('completed').css({ color: '#16a34a', fontWeight: '600' });
      $s4.find('.rps-step-num').css({ background: '#16a34a', color: '#fff' }).html('<i class="fa fa-check"></i>');
    } else if (!isCpmkApproved) {
      $s4.addClass('locked').css({ color: '#94a3b8', fontWeight: '500' });
      $s4.find('.rps-step-num').css({ background: '#f1f5f9', color: '#94a3b8' }).html('<i class="fa fa-lock"></i>');
    } else {
      $s4.css({ color: '#64748b', fontWeight: '600' });
      $s4.find('.rps-step-num').css({ background: '#cbd5e1', color: '#475569' }).html('4');
    }

    // Step 5: Dokumen Turunan
    var isDerivative = ['kontrak_kuliah', 'rancangan_tugas', 'roadmap_wbi', 'skenario_pembelajaran'].indexOf(currentMode) !== -1;
    var $s5 = $('#step_dokumen_turunan');
    $s5.removeClass('active completed locked');
    if (isDerivative) {
      $s5.addClass('active').css({ color: '#1e40af', fontWeight: '700' });
      $s5.find('.rps-step-num').css({ background: '#1e40af', color: '#fff' }).html('5');
    } else {
      $s5.css({ color: '#64748b', fontWeight: '600' });
      $s5.find('.rps-step-num').css({ background: '#cbd5e1', color: '#475569' }).html('5');
    }
  }

  /**
   * Memuat data Studio CPL dari Controller
   */
  function loadCplStudioData(forceReload) {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi = $.trim($('#mk_prodi').val());
    var sks = $('#mk_sks').val();
    var sem = $('#mk_semester').val();
    var idRps = $('#id_rps_loaded').val() || '';

    if (!namaMk && !kodeMk) {
      $('#cpl_studio_list_container').html('<div style="text-align:center; padding:30px; color:#94a3b8; font-size:12px;"><i class="fa fa-info-circle" style="font-size:24px; margin-bottom:8px; display:block;"></i>Silakan pilih Mata Kuliah pada panel kiri terlebih dahulu.</div>');
      return;
    }

    $('#cpl_studio_list_container').html('<div style="text-align:center; padding:25px; color:#64748b; font-size:12px;"><i class="fa fa-spinner fa-spin" style="font-size:18px; margin-bottom:6px; display:block;"></i>Memuat data CPL resmi prodi &amp; pemetaan kurikulum...</div>');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_get_cpl_studio"); ?>',
      type: 'POST',
      data: {
        id_rps: idRps,
        nama_mk: namaMk,
        kode_mk: kodeMk,
        nama_prodi: prodi,
        sks: sks,
        semester: sem,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          var rawList = res.cpl_list || res.all_cpls || [];
          cplStudioAllProdiList = rawList.map(function(item) {
            var k = item.code || item.kode || item.kode_cpl || '';
            return {
              kode: k,
              kategori: item.category || item.kategori || 'CPL',
              deskripsi: item.description || item.deskripsi || item.teks || '',
              selected: !!item.selected,
              role: item.role || 'SUPPORTING',
              source: item.source || 'MANUAL',
              approved: !!item.approved,
              is_mapped: !!item.is_mapped,
              mapping_badge: item.mapping_badge || '',
              mapping_type: item.mapping_type || ''
            };
          });

          cplStudioMappedCandidates = res.mapped_cpls || res.mapped_candidates || [];

          // Sinkronisasi data CPL terpilih dengan pencocokan normalized upper-case & hyphens
          var existingMap = {};
          if (activeCplMkList && activeCplMkList.length > 0) {
            activeCplMkList.forEach(function(x) {
              var k = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
              if (k) existingMap[k] = x;
            });
          }

          cplStudioAllProdiList.forEach(function(c) {
            var k = (c.kode || '').toUpperCase().replace(/\s+/g, '-');
            if (existingMap[k]) {
              c.selected = true;
              c.role = existingMap[k].role || c.role;
              c.source = existingMap[k].source || c.source;
            } else if (c.selected) {
              existingMap[k] = {
                kode: c.kode,
                code: c.kode,
                teks: c.deskripsi,
                description: c.deskripsi,
                role: c.role || 'PRIMARY',
                provenance: c.source || 'MANUAL',
                source: c.source || 'MANUAL',
                reasoning: ''
              };
            }
          });

          activeCplMkList = [];
          cplStudioAllProdiList.forEach(function(c) {
            if (c.selected) {
              activeCplMkList.push({
                kode: c.kode,
                code: c.kode,
                teks: c.deskripsi,
                description: c.deskripsi,
                role: c.role || 'PRIMARY',
                included: true,
                provenance: c.source || 'MANUAL',
                source: c.source || 'MANUAL',
                reasoning: ''
              });
            }
          });

          if (res.cpl_mk_status) {
            activeCplMkStatus = res.cpl_mk_status;
          } else if (!activeCplMkStatus) {
            activeCplMkStatus = (activeCplMkList.length > 0 ? 'DRAFT' : null);
          }

          if (res.deskripsi_mata_kuliah || res.deskripsi_mk) {
            activeDeskripsiMk = cleanDeskripsiText(res.deskripsi_mata_kuliah || res.deskripsi_mk);
          } else {
            activeDeskripsiMk = '';
          }
          if (res.deskripsi_mk_source) {
            activeDeskripsiMkSource = res.deskripsi_mk_source;
          } else {
            activeDeskripsiMkSource = 'CURRICULUM';
          }
          if (res.referensi) {
            activeReferensi = cleanReferensiText(res.referensi);
          } else {
            activeReferensi = '';
          }

          if (activeCplMkList.length > 0) {
            cplStudioFilter = 'selected';
          } else {
            cplStudioFilter = 'all';
          }

          renderCplStudioUi();
        } else {
          $('#cpl_studio_list_container').html('<div style="padding:15px; color:#dc2626; font-size:12px; text-align:center;"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(res.msg || 'Gagal memuat CPL Studio') + '</div>');
        }
      },
      error: function(xhr, status, error) {
        $('#cpl_studio_list_container').html('<div style="padding:15px; color:#dc2626; font-size:12px; text-align:center;"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan jaringan saat memuat CPL Studio.</div>');
      }
    });
  }

  /**
   * Filter daftar CPL Studio
   */
  function filterCplStudioList(type) {
    cplStudioFilter = type || 'all';
    $('#cpl_filter_buttons .btn').removeClass('active');
    if (type === 'selected') {
      $('#cpl_filter_buttons .btn:nth-child(2)').addClass('active');
    } else if (type === 'recommended') {
      $('#cpl_filter_buttons .btn:nth-child(3)').addClass('active');
    } else {
      $('#cpl_filter_buttons .btn:nth-child(1)').addClass('active');
    }
    renderCplStudioListOnly();
  }

  /**
   * Toggle seleksi CPL
   */
  function toggleCplSelect(kode) {
    if (activeCplMkStatus === 'APPROVED') return;
    var normKode = (kode || '').toUpperCase().replace(/\s+/g, '-');
    var existingIdx = activeCplMkList.findIndex(function(x) {
      var k = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
      return k === normKode;
    });

    if (existingIdx !== -1) {
      activeCplMkList.splice(existingIdx, 1);
    } else {
      var item = cplStudioAllProdiList.find(function(x) {
        var k = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
        return k === normKode;
      });
      if (item) {
        activeCplMkList.push({
          kode: item.kode,
          code: item.kode,
          teks: item.deskripsi,
          description: item.deskripsi,
          role: item.role || 'SUPPORTING',
          included: true,
          provenance: item.source || 'MANUAL',
          source: item.source || 'MANUAL',
          reasoning: ''
        });
      }
    }

    renderCplStudioUi();
  }

  /**
   * Ganti Role CPL (PRIMARY / SUPPORTING)
   */
  function changeCplRole(kode, role) {
    if (activeCplMkStatus === 'APPROVED') return;
    var normKode = (kode || '').toUpperCase().replace(/\s+/g, '-');
    var item = activeCplMkList.find(function(x) {
      var k = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
      return k === normKode;
    });
    if (item) {
      item.role = role;
      renderCplStudioUi();
    }
  }

  /**
   * Render seluruh elemen UI pada Studio CPL
   */
  function renderCplStudioUi() {
    if (!cplStudioAllProdiList) return;

    var isApproved = (activeCplMkStatus === 'APPROVED');

    // 1. Tampilkan Canvas View
    if (activeMode === 'studio_cpl') {
      var hasCourse = !!($.trim($('#mk_nama').val()) || $.trim($('#mk_kode').val()));
      if (hasCourse) {
        $('#canvas_placeholder').hide();
        $('#canvas_cpl_studio_view').show();
        $('#canvas_tools').hide();
      }
    }

    // 2. Update Status Badge & Action Controls
    if (isApproved) {
      $('#cpl_studio_status_badge').html('<i class="fa fa-check-circle"></i> APPROVED').css({
        background: '#dcfce7', color: '#15803d', borderColor: '#86efac'
      });
      $('#cpl_approved_notice').fadeIn(150);
      $('#studio_deskripsi_mk').prop('readonly', true).css({ background: '#f8fafc', color: '#334155' });
      $('#studio_referensi_mk').prop('readonly', true).css({ background: '#f8fafc', color: '#334155' });
      $('#btn_ambil_desc_rps_lama, #btn_ambil_desc_kurikulum, #btn_draft_desc_ai, #btn_simpan_draft_cpl, #btn_setujui_cpl, #btn_cpl_rps_lama, #btn_rekomendasi_cpl_ai').hide().prop('disabled', true);
      $('#btn_revisi_cpl').show();
    } else {
      $('#cpl_studio_status_badge').text(activeCplMkStatus || 'DRAFT').css({
        background: '#fef3c7', color: '#b45309', borderColor: '#fde68a'
      });
      $('#cpl_approved_notice').hide();
      $('#studio_deskripsi_mk').prop('readonly', false).css({ background: '#ffffff', color: '#0f172a' });
      $('#studio_referensi_mk').prop('readonly', false).css({ background: '#ffffff', color: '#0f172a' });
      $('#btn_ambil_desc_rps_lama, #btn_ambil_desc_kurikulum, #btn_draft_desc_ai, #btn_simpan_draft_cpl, #btn_setujui_cpl, #btn_cpl_rps_lama, #btn_rekomendasi_cpl_ai').show().prop('disabled', false);
      $('#btn_revisi_cpl').hide();
    }

    // 3. Update Deskripsi & Source Badge
    $('#studio_deskripsi_mk').val(activeDeskripsiMk || '');
    if (activeDeskripsiMkSource === 'AI_DRAFT') {
      $('#badge_desc_source').text('Sumber: Draft AI').css({ background: '#f3e8ff', color: '#7e22ce', borderColor: '#d8b4fe' });
    } else if (activeDeskripsiMkSource === 'MANUAL') {
      $('#badge_desc_source').text('Sumber: Manual Dosen').css({ background: '#fef3c7', color: '#b45309', borderColor: '#fde68a' });
    } else if (activeDeskripsiMkSource === 'RPS_LAMA' || activeDeskripsiMkSource === 'RPS_ONLINE') {
      $('#badge_desc_source').text('Sumber: RPS Online (siakad_rps_online)').css({ background: '#e0e7ff', color: '#3730a3', borderColor: '#c7d2fe' });
    } else {
      $('#badge_desc_source').text('Sumber: Kurikulum').css({ background: '#e0f2fe', color: '#0369a1', borderColor: '#bae6fd' });
    }

    // 4. Update Referensi
    $('#studio_referensi_mk').val(activeReferensi || '');

    // 5. Update Counts & Summary
    var totalCpl = cplStudioAllProdiList.length;
    var selectedCount = activeCplMkList.length;
    var primaryCount = activeCplMkList.filter(function(x) { return x.role === 'PRIMARY'; }).length;
    var supportingCount = activeCplMkList.filter(function(x) { return x.role === 'SUPPORTING'; }).length;

    $('#count_cpl_all').text(totalCpl);
    $('#count_cpl_selected').text(selectedCount);
    $('#summary_cpl_selection').html('<strong>' + selectedCount + ' CPL Terpilih</strong> (' + primaryCount + ' Utama, ' + supportingCount + ' Pendukung)');

    // Update filter button active states
    $('#cpl_filter_buttons .btn').removeClass('active');
    if (cplStudioFilter === 'selected') {
      $('#cpl_filter_buttons .btn:nth-child(2)').addClass('active');
    } else if (cplStudioFilter === 'recommended') {
      $('#cpl_filter_buttons .btn:nth-child(3)').addClass('active');
    } else {
      $('#cpl_filter_buttons .btn:nth-child(1)').addClass('active');
    }

    // 6. Render List
    renderCplStudioListOnly();

    // 7. Sync Stepper & Button States
    updateWorkflowStepper(activeMode || 'studio_cpl');
    if (typeof updateGenerateBtnState === 'function') {
      updateGenerateBtnState(activeMode);
    }
    if (curriculumReadinessData && typeof renderCurriculumReadinessUi === 'function') {
      renderCurriculumReadinessUi(curriculumReadinessData);
    }
  }

  /**
   * Render hanya container daftar CPL (dipakai saat filter / toggle)
   */
  function renderCplStudioListOnly() {
    var $container = $('#cpl_studio_list_container');
    if (!$container.length) return;

    if (!cplStudioAllProdiList || cplStudioAllProdiList.length === 0) {
      if (activeCplMkList && activeCplMkList.length > 0) {
        cplStudioAllProdiList = activeCplMkList.map(function(x) {
          var k = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
          return {
            kode: k,
            kategori: x.kategori || x.category || 'CPL',
            deskripsi: x.deskripsi || x.description || x.teks || '',
            selected: true,
            role: x.role || 'SUPPORTING',
            source: x.source || x.provenance || 'MANUAL',
            approved: (activeCplMkStatus === 'APPROVED')
          };
        });
      } else {
        $container.html('<div style="text-align:center; padding:25px; color:#94a3b8; font-size:12px;"><i class="fa fa-info-circle"></i> Tidak ada data CPL resmi untuk program studi ini.</div>');
        return;
      }
    }

    var isApproved = (activeCplMkStatus === 'APPROVED');
    var list = cplStudioAllProdiList;

    if (cplStudioFilter === 'selected') {
      list = cplStudioAllProdiList.filter(function(item) {
        var ki = (item.kode || item.code || '').toUpperCase().replace(/\s+/g, '-');
        return activeCplMkList.some(function(x) {
          var kx = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
          return kx === ki;
        });
      });
      if (list.length === 0) {
        $container.html('<div style="text-align:center; padding:25px; color:#64748b; font-size:12px;"><i class="fa fa-filter"></i> Belum ada CPL yang dipilih. Klik tab <strong>Semua</strong> untuk memilih CPL.</div>');
        return;
      }
    } else if (cplStudioFilter === 'recommended') {
      list = cplStudioAllProdiList.filter(function(item) {
        var ki = (item.kode || item.code || '').toUpperCase().replace(/\s+/g, '-');
        var isCand = cplStudioMappedCandidates.some(function(cand) {
          return String(cand).toUpperCase().replace(/\s+/g, '-') === ki;
        });
        var inSelected = activeCplMkList.some(function(x) {
          var kx = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
          return kx === ki && (x.provenance === 'AI_RECOMMENDATION' || x.provenance === 'CURRICULUM_MAPPED');
        });
        return isCand || inSelected;
      });
      if (list.length === 0) {
        $container.html('<div style="text-align:center; padding:25px; color:#64748b; font-size:12px;"><i class="fa fa-magic"></i> Belum ada rekomendasi mapping CPL. Klik tombol <strong>Rekomendasi AI</strong> untuk meminta saran.</div>');
        return;
      }
    }

    var html = '';
    list.forEach(function(item, idx) {
      var kode = item.kode || ('CPL-' + (idx + 1));
      var deskripsi = item.deskripsi || item.teks || '';
      var kat = item.kategori || 'CPL';
      var normKode = kode.toUpperCase().replace(/\s+/g, '-');

      // Find in active selection
      var selItem = activeCplMkList.find(function(x) {
        var kx = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
        return kx === normKode;
      });
      var isChecked = !!selItem;
      var role = selItem ? (selItem.role || 'SUPPORTING') : 'SUPPORTING';
      var isMappedInCurriculum = (cplStudioMappedCandidates.indexOf(kode) !== -1);
      var reasoning = selItem ? (selItem.reasoning || '') : '';

      // Kategori Badge Colors
      var katBg = '#f1f5f9', katColor = '#475569';
      if (/sikap|attitude/i.test(kat)) { katBg = '#fef3c7'; katColor = '#b45309'; }
      else if (/pengetahuan|knowledge/i.test(kat)) { katBg = '#e0f2fe'; katColor = '#0369a1'; }
      else if (/umum|general/i.test(kat)) { katBg = '#ccfbf1'; katColor = '#0f766e'; }
      else if (/khusus|special|specific/i.test(kat)) { katBg = '#f3e8ff'; katColor = '#7e22ce'; }

      var cardBorder = isChecked ? (role === 'PRIMARY' ? 'border:1.5px solid #0f766e; background:#f0fdf4;' : 'border:1.5px solid #0284c7; background:#f0f9ff;') : 'border:1px solid #e2e8f0; background:#ffffff;';

      html += '<div style="' + cardBorder + ' border-radius:8px; padding:10px 14px; display:flex; flex-direction:column; gap:6px; transition:all 0.15s ease;">'
            + '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">'
            + '<div style="display:flex; align-items:center; gap:8px;">';

      if (!isApproved) {
        html += '<input type="checkbox" style="width:16px; height:16px; cursor:pointer;" ' + (isChecked ? 'checked="checked"' : '') + ' onchange="toggleCplSelect(\'' + escapeJsString(kode) + '\')">';
      } else {
        html += isChecked ? '<i class="fa fa-check-square text-success" style="font-size:16px;"></i>' : '<i class="fa fa-square-o text-muted" style="font-size:16px;"></i>';
      }

      html += '<span style="font-weight:800; font-size:12.5px; color:#0f172a;">' + escapeHtml(kode) + '</span>'
            + '<span style="font-size:10px; font-weight:700; background:' + katBg + '; color:' + katColor + '; padding:1px 6px; border-radius:4px;">' + escapeHtml(kat) + '</span>';

      var isMappedInCurriculum = (cplStudioMappedCandidates.indexOf(kode) !== -1) || item.is_mapped;
      var mappingBadgeText = item.mapping_badge || (isMappedInCurriculum ? 'Terpetakan' : 'CPL Resmi');

      if (mappingBadgeText === 'Terpetakan Langsung') {
        html += '<span style="font-size:10px; font-weight:700; background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:1px 6px; border-radius:4px;" title="Terpetakan langsung dari matriks/uraian kurikulum"><i class="fa fa-link"></i> Terpetakan Langsung</span>';
      } else if (mappingBadgeText === 'Terpetakan via BK') {
        html += '<span style="font-size:10px; font-weight:700; background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; padding:1px 6px; border-radius:4px;" title="Diturunkan melalui matriks CPL-BK dan BK-MK"><i class="fa fa-sitemap"></i> Terpetakan via BK</span>';
      } else if (isMappedInCurriculum) {
        html += '<span style="font-size:10px; font-weight:700; background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; padding:1px 6px; border-radius:4px;" title="Terpetakan pada dokumen kurikulum"><i class="fa fa-link"></i> Terpetakan</span>';
      } else {
        html += '<span style="font-size:10px; font-weight:600; background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; padding:1px 6px; border-radius:4px;" title="CPL resmi program studi (belum terpetakan ke MK ini)"><i class="fa fa-shield"></i> CPL Resmi</span>';
      }

      html += '</div>'; // close left flex

      // Right role selector (only when checked)
      if (isChecked) {
        if (!isApproved) {
          html += '<div class="btn-group btn-group-xs">'
                + '<button type="button" class="btn ' + (role === 'PRIMARY' ? 'btn-primary' : 'btn-default') + '" style="' + (role === 'PRIMARY' ? 'background:#0f766e; border-color:#0f766e; font-weight:700;' : 'color:#64748b;') + '" onclick="changeCplRole(\'' + escapeJsString(kode) + '\', \'PRIMARY\')"><i class="fa fa-star"></i> Utama (Primary)</button>'
                + '<button type="button" class="btn ' + (role === 'SUPPORTING' ? 'btn-info' : 'btn-default') + '" style="' + (role === 'SUPPORTING' ? 'background:#0284c7; border-color:#0284c7; font-weight:700;' : 'color:#64748b;') + '" onclick="changeCplRole(\'' + escapeJsString(kode) + '\', \'SUPPORTING\')"><i class="fa fa-shield"></i> Pendukung</button>'
                + '</div>';
        } else {
          var roleBadge = (role === 'PRIMARY')
            ? '<span style="font-size:10px; font-weight:700; background:#0f766e; color:#ffffff; padding:2px 8px; border-radius:4px;"><i class="fa fa-star"></i> Utama (Primary)</span>'
            : '<span style="font-size:10px; font-weight:700; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:4px;"><i class="fa fa-shield"></i> Pendukung</span>';
          html += '<div>' + roleBadge + '</div>';
        }
      }

      html += '</div>'; // close header flex

      // Body text
      html += '<div style="font-size:11.5px; color:#334155; line-height:1.5; padding-left:' + (isApproved ? '0' : '24px') + ';">'
            + escapeHtml(deskripsi)
            + '</div>';

      // Reasoning if any
      if (reasoning) {
        html += '<div style="font-size:10.5px; color:#6b21a8; background:#faf5ff; padding:3px 8px; border-radius:4px; margin-left:' + (isApproved ? '0' : '24px') + '; border:1px dashed #e9d5ff;">'
              + '<i class="fa fa-lightbulb-o"></i> <em>' + escapeHtml(reasoning) + '</em>'
              + '</div>';
      }

      html += '</div>'; // close card
    });

    $container.html(html);
  }

  /**
   * Minta AI memberikan rekomendasi CPL-MK
   */
  function mintaRekomendasiCplAi() {
    if (activeCplMkStatus === 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'info',
          title: 'CPL Sudah Disetujui (APPROVED)',
          text: 'CPL-MK & Deskripsi saat ini telah dikunci (APPROVED). Silakan klik tombol "Revisi CPL & Deskripsi" jika ingin mengubah atau meminta rekomendasi ulang.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('CPL telah berstatus APPROVED. Silakan klik "Revisi CPL & Deskripsi" terlebih dahulu.');
      }
      return;
    }

    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());
    var sks    = $.trim($('#mk_sks').val());
    var sem    = $.trim($('#mk_semester').val());

    if (!namaMk && !kodeMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    var $btnAi = $('#btn_minta_rekomendasi_cpl_ai');
    var origBtnHtml = $btnAi.length ? $btnAi.html() : '';
    if ($btnAi.length) {
      $btnAi.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menganalisis...');
    }

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Menganalisis Rekomendasi CPL...',
        html: '<div style="padding:12px 0 6px 0; text-align:center;">' +
              '  <div style="display:inline-block; width:48px; height:48px; border:4px solid #e2e8f0; border-top-color:#0f766e; border-radius:50%; animation:rpsSpinLoader 0.8s linear infinite; margin-bottom:14px;"></div>' +
              '  <div style="font-size:13.5px; color:#334155; font-weight:500; line-height:1.5;">AI sedang menganalisis CPL resmi prodi dan bahan kajian yang sesuai...</div>' +
              '  <div style="font-size:11.5px; color:#64748b; margin-top:6px;">Mohon tunggu beberapa detik, proses ini tidak memerlukan konfirmasi manual.</div>' +
              '</div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: function() {
          if (typeof Swal.showLoading === 'function') {
            Swal.showLoading();
          }
        }
      });
    }

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_generate_cpl_recommendation"); ?>',
      type: 'POST',
      data: {
        nama_mk: namaMk,
        kode_mk: kodeMk,
        nama_prodi: prodi,
        sks: sks,
        semester: sem,
        teks_kurikulum: uploadedDocs.kurikulum,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      complete: function() {
        if ($btnAi.length) {
          $btnAi.prop('disabled', false).html(origBtnHtml);
        }
      },
      success: function(res) {
        if (typeof Swal !== 'undefined') Swal.close();
        if (res.status && (res.recommendations || res.rekomendasi)) {
          var recs = res.recommendations || res.rekomendasi || [];
          var mergedMap = {};

          // 1. Masukkan seluruh CPL yang terpetakan langsung secara faktual dari kurikulum (Faktual - WAJIB ADA & TIDAK BOLEH HILANG)
          cplStudioAllProdiList.forEach(function(c) {
            var normK = (c.kode || '').toUpperCase().replace(/\s+/g, '-');
            var isDirectMapped = !!c.is_mapped || (cplStudioMappedCandidates && cplStudioMappedCandidates.indexOf(c.kode) !== -1);
            if (isDirectMapped) {
              mergedMap[normK] = {
                kode: c.kode,
                code: c.kode,
                teks: c.deskripsi,
                description: c.deskripsi,
                role: c.role || 'PRIMARY',
                provenance: 'FACTUAL',
                source: c.source || 'EXPLICIT_MATRIX',
                reasoning: 'Pemetaan faktual kurikulum resmi program studi.'
              };
            }
          });

          // 2. Pertahankan pilihan non-AI yang sudah ada sebelumnya jika ada
          if (activeCplMkList && activeCplMkList.length > 0) {
            activeCplMkList.forEach(function(x) {
              var normK = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
              if (normK && !mergedMap[normK] && x.provenance !== 'AI_PROPOSAL_MAPPING') {
                mergedMap[normK] = x;
              }
            });
          }

          // 3. Gabungkan rekomendasi AI (update peran untuk CPL faktual, atau tambahkan usulan CPL pendukung baru)
          recs.forEach(function(item) {
            var cplCode = item.code || item.kode || item.kode_cpl || '';
            var normK = cplCode.toUpperCase().replace(/\s+/g, '-');
            // Cek variasi alias KK vs KS
            var existingObj = cplStudioAllProdiList.find(function(c) {
              var ck = (c.kode || '').toUpperCase().replace(/\s+/g, '-');
              return ck === normK || ck === normK.replace('KS-', 'KK-') || ck === normK.replace('KK-', 'KS-');
            });
            var canonicalCode = existingObj ? existingObj.kode : cplCode;
            var canonicalNorm = canonicalCode.toUpperCase().replace(/\s+/g, '-');
            var aiRole = (item.role && item.role.toUpperCase() === 'PRIMARY') ? 'PRIMARY' : 'SUPPORTING';
            var aiReason = item.reason || item.reasoning || item.justifikasi || '';

            if (mergedMap[canonicalNorm]) {
              // Jika CPL faktual sudah ada, perannya diupdate oleh AI dan alasannya dicatat, status faktualnya TETAP DIPERTAHANKAN
              mergedMap[canonicalNorm].role = aiRole;
              if (aiReason) mergedMap[canonicalNorm].reasoning = aiReason;
            } else if (existingObj) {
              // Tambahkan CPL usulan baru dari AI
              mergedMap[canonicalNorm] = {
                kode: canonicalCode,
                code: canonicalCode,
                teks: existingObj.deskripsi || item.deskripsi || item.description || '',
                description: existingObj.deskripsi || item.deskripsi || item.description || '',
                role: aiRole,
                included: true,
                provenance: 'AI_PROPOSAL_MAPPING',
                source: 'AI_PROPOSAL_MAPPING',
                reasoning: aiReason
              };
            }
          });

          // 4. Update activeCplMkList & cplStudioAllProdiList
          activeCplMkList = [];
          for (var k in mergedMap) {
            if (mergedMap.hasOwnProperty(k)) {
              activeCplMkList.push(mergedMap[k]);
            }
          }

          cplStudioAllProdiList.forEach(function(c) {
            var ck = (c.kode || '').toUpperCase().replace(/\s+/g, '-');
            if (mergedMap[ck]) {
              c.selected = true;
              c.role = mergedMap[ck].role;
              c.source = mergedMap[ck].source;
            } else {
              c.selected = false;
            }
          });

          activeCplMkStatus = 'DRAFT';
          if (res.deskripsi_mk && !$('#studio_deskripsi_mk').val()) {
            activeDeskripsiMk = res.deskripsi_mk;
            activeDeskripsiMkSource = 'AI_DRAFT';
          }
          cplStudioFilter = 'selected';
          $('#cpl_filter_buttons .btn').removeClass('active');
          $('#cpl_filter_buttons .btn:nth-child(2)').addClass('active');
          renderCplStudioUi();

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Rekomendasi CPL Berhasil',
              text: 'Ditemukan ' + activeCplMkList.length + ' butir CPL rekomendasi yang relevan untuk ' + namaMk + '.',
              timer: 2000,
              showConfirmButton: false
            });
          }
        } else {
          alert('Gagal mendapatkan rekomendasi CPL: ' + (res.msg || 'Format respon tidak sesuai.'));
        }
      },
      error: function(xhr, status, error) {
        if (typeof Swal !== 'undefined') Swal.close();
        alert('Terjadi kesalahan jaringan saat meminta rekomendasi CPL.');
      }
    });
  }

  /**
   * Buka Modal Ambil Deskripsi & Referensi dari database siakad_rps_online (RPS Lama)
   */
  function ambilDeskripsiDariRpsLama() {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    if (!namaMk && !kodeMk) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Pilih Mata Kuliah Dahulu',
          text: 'Silakan pilih Mata Kuliah di panel kiri terlebih dahulu sebelum mengambil arsip RPS lama.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      }
      return;
    }

    $('#modal_rps_lama_target_mk').text(namaMk || '—');
    $('#modal_rps_lama_target_kode').text(kodeMk || '—');
    $('#input_modal_search_rps_lama').val('');
    $('#modal_ambil_rps_lama').modal('show');

    cariArsipRpsLamaModal();
  }

  /**
   * Pencarian Arsip RPS Online di Database untuk Modal
   */
  function cariArsipRpsLamaModal() {
    var namaMk  = $.trim($('#mk_nama').val());
    var kodeMk  = $.trim($('#mk_kode').val());
    var keyword = $.trim($('#input_modal_search_rps_lama').val());

    var $container = $('#modal_rps_lama_candidates_container');
    $container.html(
      '<div style="text-align:center; padding:35px 20px; color:#64748b; font-size:13px; background:#ffffff; border-radius:8px; border:1px solid #e2e8f0;">' +
      '  <i class="fa fa-spinner fa-spin fa-2x text-primary" style="margin-bottom:8px; display:block;"></i>' +
      '  Mencari arsip RPS di database siakad_rps_online...' +
      '</div>'
    );

    window.modalRpsLamaCandidates = [];

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_search_rps_lama_modal"); ?>',
      type: 'POST',
      data: {
        kode_mk: kodeMk,
        nama_mk: namaMk,
        keyword: keyword,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status && res.candidates && res.candidates.length > 0) {
          window.modalRpsLamaCandidates = res.candidates;
          renderArsipRpsLamaCandidates(res.candidates, kodeMk);
        } else {
          $container.html(
            '<div style="text-align:center; padding:35px 20px; color:#64748b; font-size:13px; background:#ffffff; border-radius:8px; border:1px solid #e2e8f0;">' +
            '  <i class="fa fa-info-circle fa-2x" style="color:#94a3b8; margin-bottom:8px; display:block;"></i>' +
            '  <strong style="color:#334155; font-size:14px;">Tidak Ada Arsip RPS Ditemukan</strong><br>' +
            '  <span style="font-size:12px; color:#64748b;">Tidak ditemukan arsip RPS lama yang sesuai dengan kata kunci atau kode MK ini pada database.</span>' +
            '</div>'
          );
        }
      },
      error: function() {
        $container.html(
          '<div style="text-align:center; padding:25px 20px; color:#dc2626; font-size:12.5px; background:#fef2f2; border-radius:8px; border:1px solid #fecaca;">' +
          '  <i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i> Terjadi kesalahan jaringan saat memuat arsip database RPS online.' +
          '</div>'
        );
      }
    });
  }

  /**
   * Render Kartu Kandidat Arsip RPS Lama
   */
  function renderArsipRpsLamaCandidates(candidates, currentKodeMk) {
    var $container = $('#modal_rps_lama_candidates_container');
    var html = '';

    candidates.forEach(function(c, idx) {
      var isExactCode = (currentKodeMk && c.kode_mk && currentKodeMk.toLowerCase() === c.kode_mk.toLowerCase());
      var codeBadgeClass = isExactCode ? 'background:#dcfce7; color:#15803d; border:1px solid #86efac;' : 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;';
      var codeBadgeIcon = isExactCode ? '<i class="fa fa-check-circle text-success" style="margin-right:3px;"></i>' : '<i class="fa fa-tag" style="margin-right:3px;"></i>';

      var cleanDesc = cleanDeskripsiText(c.deskripsi_mk);
      var cleanRef = cleanReferensiText(c.referensi);
      var descSnippet = cleanDesc ? (cleanDesc.length > 220 ? cleanDesc.substring(0, 220) + '...' : cleanDesc) : '<em class="text-muted">Tidak ada narasi deskripsi.</em>';
      var refSnippet = cleanRef ? (cleanRef.length > 150 ? cleanRef.substring(0, 150) + '...' : cleanRef) : '';

      html += '<div class="rps-candidate-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; box-shadow:0 1px 3px rgba(0,0,0,0.03); transition:all 0.15s ease;">';
      
      // Header Bar
      html += '  <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:8px;">';
      html += '    <div>';
      html += '      <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">';
      if (c.kode_mk) {
        html += '        <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px; ' + codeBadgeClass + '">' + codeBadgeIcon + escapeHtml(c.kode_mk) + '</span>';
      }
      html += '        <strong style="font-size:14px; color:#0f172a;">' + escapeHtml(c.general_mk || c.nm_mk || 'Mata Kuliah') + '</strong>';
      if (c.kode_revisi) {
        html += '        <span style="font-size:10px; font-weight:600; background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:1px 6px; border-radius:3px;">Revisi ' + escapeHtml(c.kode_revisi) + '</span>';
      }
      html += '      </div>';
      html += '    </div>';
      
      // Badges Tahun Ajaran
      html += '    <div>';
      html += '      <span style="font-size:11px; font-weight:700; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:3px 9px; border-radius:5px;">';
      html += '        <i class="fa fa-calendar-check-o" style="margin-right:3px;"></i> ' + escapeHtml(c.tahun_ajaran || 'Tahun Tidak Tercatat');
      html += '      </span>';
      html += '    </div>';
      html += '  </div>';

      // Meta Data: Penyusun, Tanggal RPS
      html += '  <div style="font-size:12px; color:#475569; margin-bottom:10px; display:flex; align-items:center; flex-wrap:wrap; gap:12px; background:#f8fafc; padding:6px 10px; border-radius:5px; border:1px solid #f1f5f9;">';
      html += '    <span><i class="fa fa-user text-primary" style="margin-right:4px;"></i> <strong>Penyusun / Dosen:</strong> ' + escapeHtml(c.penyusun || '—') + '</span>';
      if (c.tanggal_rps && c.tanggal_rps !== '0000-00-00') {
        html += '    <span><i class="fa fa-clock-o text-muted" style="margin-right:4px;"></i> <strong>Tgl RPS:</strong> ' + escapeHtml(c.tanggal_rps) + '</span>';
      }
      html += '  </div>';

      // Preview Deskripsi Snippet
      html += '  <div style="font-size:12px; color:#334155; line-height:1.5; margin-bottom:8px; background:#ffffff; border-left:3px solid #3b82f6; padding:6px 10px; border-radius:0 4px 4px 0;">';
      html += '    <div style="font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Deskripsi Mata Kuliah:</div>';
      html += '    <div>' + escapeHtml(descSnippet) + '</div>';
      html += '  </div>';

      // Preview Referensi Snippet jika ada
      if (refSnippet) {
        html += '  <div style="font-size:11.5px; color:#475569; line-height:1.4; margin-bottom:10px; background:#ffffff; border-left:3px solid #10b981; padding:5px 10px; border-radius:0 4px 4px 0;">';
        html += '    <div style="font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:2px;">Referensi / Pustaka:</div>';
        html += '    <div>' + escapeHtml(refSnippet) + '</div>';
        html += '  </div>';
      }

      // Action Button
      html += '  <div style="text-align:right; margin-top:8px; border-top:1px solid #f1f5f9; padding-top:8px;">';
      html += '    <button type="button" class="btn btn-sm btn-primary" onclick="terapkanDeskripsiRpsLama(' + idx + ')" style="background:#2563eb; border-color:#1d4ed8; font-weight:700; border-radius:6px; font-size:11.5px; padding:5px 14px; color:#ffffff;">';
      html += '      <i class="fa fa-check-circle"></i> Gunakan Deskripsi &amp; Referensi Ini';
      html += '    </button>';
      html += '  </div>';

      html += '</div>';
    });

    $container.html(html);
  }

  /**
   * Terapkan Deskripsi & Referensi dari Kandidat Terpilih ke Studio Form
   */
  function terapkanDeskripsiRpsLama(index) {
    if (!window.modalRpsLamaCandidates || !window.modalRpsLamaCandidates[index]) {
      return;
    }

    var item = window.modalRpsLamaCandidates[index];
    if (item.deskripsi_mk) {
      activeDeskripsiMk = cleanDeskripsiText(item.deskripsi_mk);
      activeDeskripsiMkSource = 'RPS_LAMA';
      $('#studio_deskripsi_mk').val(activeDeskripsiMk);
      
      var sourceLabel = 'Sumber: RPS Online (' + (item.tahun_ajaran || 'Arsip') + ' - ' + (item.penyusun || 'Dosen') + ')';
      $('#badge_desc_source').text(sourceLabel).css({ background: '#e0e7ff', color: '#3730a3', borderColor: '#c7d2fe' });
    }

    if (item.referensi) {
      activeReferensi = cleanReferensiText(item.referensi);
      $('#studio_referensi_mk').val(activeReferensi);
    }

    $('#modal_ambil_rps_lama').modal('hide');

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Arsip RPS Berhasil Diterapkan!',
        html: '<div style="text-align:left; font-size:12.5px; color:#334155; line-height:1.5;">' +
              '  <strong>Mata Kuliah:</strong> ' + escapeHtml(item.general_mk || item.nm_mk) + '<br>' +
              '  <strong>Penyusun:</strong> ' + escapeHtml(item.penyusun || '—') + '<br>' +
              '  <strong>Tahun Ajaran:</strong> ' + escapeHtml(item.tahun_ajaran || '—') + '<br><br>' +
              '  Deskripsi dan daftar referensi perkuliahan telah diisikan ke dalam form Studio CPL.' +
              '</div>',
        timer: 2400,
        showConfirmButton: false
      });
    } else {
      alert('Deskripsi & referensi dari arsip RPS (' + (item.tahun_ajaran || '') + ') berhasil diterapkan.');
    }
  }

  // Data holder untuk modal CPL RPS Lama
  window.modalCplRpsLamaData = null;
  window.modalCplRpsLamaSelection = {};

  /**
   * Buka Modal Pembanding & Pemilihan CPL dari Arsip RPS Lama
   */
  function bukaModalCplRpsLama(idRpsLama) {
    if (activeCplMkStatus === 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'info',
          title: 'CPL Sudah Disetujui (APPROVED)',
          text: 'CPL-MK & Deskripsi saat ini telah dikunci (APPROVED). Silakan klik tombol "Revisi CPL & Deskripsi" jika ingin mengubah atau membandingkan kembali dengan RPS lama.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('CPL telah berstatus APPROVED. Silakan klik "Revisi CPL & Deskripsi" terlebih dahulu.');
      }
      return;
    }

    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());

    if (!namaMk && !kodeMk) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Pilih Mata Kuliah Dahulu',
          text: 'Silakan pilih Mata Kuliah pada panel kiri terlebih dahulu sebelum membandingkan CPL RPS lama.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      }
      return;
    }

    $('#modal_cpl_rps_lama_info_mk').text(namaMk || 'Mata Kuliah');
    $('#modal_cpl_rps_lama_info_penyusun').text('Memuat...');
    $('#modal_cpl_rps_lama_info_tahun').text('...');
    
    $('#modal_cpl_rps_lama_alert').html(
      '<div style="text-align:center; padding:15px; color:#64748b; font-size:12px; background:#f1f5f9; border-radius:6px;">' +
      '  <i class="fa fa-spinner fa-spin fa-lg text-primary" style="margin-right:6px;"></i> Memuat data CPL dari arsip RPS online & mencocokkan dengan Kurikulum 2026...' +
      '</div>'
    );
    $('#container_old_cpl_raw_text').html('<div style="text-align:center; padding:40px 10px; color:#94a3b8;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    $('#container_cpl_rps_lama_mapping_cards').html('<div style="text-align:center; padding:40px 10px; color:#94a3b8;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');

    $('#modal_cpl_rps_lama').modal('show');

    loadCplRpsLamaData(idRpsLama);
  }

  /**
   * Memuat data CPL RPS Lama dari server via AJAX
   */
  function loadCplRpsLamaData(idRpsLama) {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_get_cpl_rps_lama"); ?>',
      type: 'POST',
      data: {
        id_rps_lama: idRpsLama || '',
        kode_mk: kodeMk,
        nama_mk: namaMk,
        nama_prodi: prodi,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status && res.found) {
          window.modalCplRpsLamaData = res;
          renderCplRpsLamaModalUi(res);
        } else {
          $('#modal_cpl_rps_lama_alert').html(
            '<div class="alert alert-warning" style="border-left:4px solid #f59e0b; border-radius:6px; padding:12px 14px; font-size:12px; margin-bottom:0; background:#fffbeb; color:#92400e;">' +
            '  <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i> <strong>Arsip RPS Lama Tidak Ditemukan</strong>' +
            '  <div style="margin-top:4px; color:#92400e;">' + escapeHtml(res.msg || 'Tidak ada arsip RPS Online yang cocok untuk mata kuliah ini di database.') + '</div>' +
            '</div>'
          );
          $('#container_old_cpl_raw_text').html('<div style="text-align:center; padding:40px 15px; color:#94a3b8; font-size:12px;"><i class="fa fa-folder-open-o fa-2x" style="margin-bottom:8px; display:block;"></i> Tidak ada arsip teks CPL lama.</div>');
          $('#container_cpl_rps_lama_mapping_cards').html('<div style="text-align:center; padding:40px 15px; color:#94a3b8; font-size:12px;"><i class="fa fa-info-circle fa-2x" style="margin-bottom:8px; display:block;"></i> Rekomendasi: Gunakan Rekomendasi AI atau pilih dari Kurikulum Resmi.</div>');
        }
      },
      error: function() {
        $('#modal_cpl_rps_lama_alert').html(
          '<div class="alert alert-danger" style="border-radius:6px; padding:12px 14px; font-size:12px; margin-bottom:0;">' +
          '  <i class="fa fa-exclamation-circle" style="margin-right:6px;"></i> Terjadi kesalahan jaringan saat memuat CPL dari arsip RPS online.' +
          '</div>'
        );
      }
    });
  }

  /**
   * Render UI Komparasi CPL RPS Lama vs CPL Resmi 2026
   */
  function renderCplRpsLamaModalUi(res) {
    // 1. Header Info
    $('#modal_cpl_rps_lama_info_mk').text(res.general_mk || $('#mk_nama').val() || 'Mata Kuliah');
    $('#modal_cpl_rps_lama_info_penyusun').text(res.penyusun || 'Dosen Pengampu');
    $('#modal_cpl_rps_lama_info_tahun').text(res.tahun_ajaran || 'TA Tercatat');

    // 2. Alert Relevansi
    var alertHtml = '';
    if (res.relevance_type === 'SUCCESS') {
      alertHtml = '<div class="alert alert-success" style="border-left:4px solid #10b981; border-radius:6px; padding:10px 14px; font-size:12px; margin-bottom:0; background:#f0fdf4; border-color:#bbf7d0; color:#166534;">' +
                  '  <div style="font-weight:700; font-size:12.5px; display:flex; align-items:center; gap:6px;">' +
                  '    <i class="fa fa-check-circle text-success" style="font-size:16px;"></i> ' + escapeHtml(res.relevance_title) +
                  '  </div>' +
                  '  <div style="margin-top:4px; font-size:11.5px; color:#15803d; line-height:1.4;">' + escapeHtml(res.relevance_msg) + '</div>' +
                  '</div>';
    } else if (res.relevance_type === 'WARNING') {
      alertHtml = '<div class="alert alert-warning" style="border-left:4px solid #f59e0b; border-radius:6px; padding:10px 14px; font-size:12px; margin-bottom:0; background:#fffbeb; border-color:#fde68a; color:#92400e;">' +
                  '  <div style="font-weight:700; font-size:12.5px; display:flex; align-items:center; gap:6px;">' +
                  '    <i class="fa fa-exclamation-triangle text-warning" style="font-size:16px;"></i> ' + escapeHtml(res.relevance_title) +
                  '  </div>' +
                  '  <div style="margin-top:4px; font-size:11.5px; color:#b45309; line-height:1.4;">' + escapeHtml(res.relevance_msg) + '</div>' +
                  '</div>';
    } else {
      alertHtml = '<div class="alert alert-info" style="border-left:4px solid #3b82f6; border-radius:6px; padding:10px 14px; font-size:12px; margin-bottom:0; background:#eff6ff; border-color:#bfdbfe; color:#1e40af;">' +
                  '  <div style="font-weight:700; font-size:12.5px; display:flex; align-items:center; gap:6px;">' +
                  '    <i class="fa fa-info-circle text-info" style="font-size:16px;"></i> ' + escapeHtml(res.relevance_title) +
                  '  </div>' +
                  '  <div style="margin-top:4px; font-size:11.5px; color:#1d4ed8; line-height:1.4;">' + escapeHtml(res.relevance_msg) + '</div>' +
                  '</div>';
    }
    $('#modal_cpl_rps_lama_alert').html(alertHtml);

    // 3. Render Kolom Kiri: Teks Asli CPL RPS Lama
    var rawTextHtml = '';
    var oldCpl = res.old_cpl || {};
    var sections = [
      { key: 'sikap', label: '1. Capaian Sikap (S)', icon: 'fa-heart', color: '#b45309', bg: '#fef3c7', text: oldCpl.sikap },
      { key: 'pengetahuan', label: '2. Capaian Pengetahuan (P)', icon: 'fa-lightbulb-o', color: '#0369a1', bg: '#e0f2fe', text: oldCpl.pengetahuan },
      { key: 'ku', label: '3. Keterampilan Umum (KU)', icon: 'fa-cubes', color: '#0f766e', bg: '#ccfbf1', text: oldCpl.ku },
      { key: 'kk', label: '4. Keterampilan Khusus (KK)', icon: 'fa-star', color: '#4338ca', bg: '#e0e7ff', text: oldCpl.kk },
      { key: 'cpmk', label: '5. CPMK Lama', icon: 'fa-graduation-cap', color: '#6d28d9', bg: '#f3e8ff', text: oldCpl.cpmk }
    ];

    sections.forEach(function(s) {
      var cleanVal = cleanReferensiText(s.text);
      rawTextHtml += '<div style="margin-bottom:12px; border:1px solid #e2e8f0; border-radius:6px; overflow:hidden;">';
      rawTextHtml += '  <div style="background:' + s.bg + '; color:' + s.color + '; padding:4px 8px; font-weight:700; font-size:11px; display:flex; align-items:center; gap:5px;">';
      rawTextHtml += '    <i class="fa ' + s.icon + '"></i> ' + s.label;
      rawTextHtml += '  </div>';
      rawTextHtml += '  <div style="padding:8px; font-size:11px; line-height:1.5; color:#334155; white-space:pre-line; max-height:130px; overflow-y:auto; background:#ffffff;">';
      rawTextHtml += cleanVal ? escapeHtml(cleanVal) : '<em style="color:#94a3b8;">(Tidak dicantumkan pada RPS lama)</em>';
      rawTextHtml += '  </div>';
      rawTextHtml += '</div>';
    });
    $('#container_old_cpl_raw_text').html(rawTextHtml);

    // 4. Render Kolom Kanan: Kartu CPL Resmi 2026 yang Dipetakan
    window.modalCplRpsLamaSelection = {};
    var comparedList = res.compared_cpls || [];
    var cardsHtml = '';

    if (comparedList.length === 0) {
      cardsHtml = '<div style="text-align:center; padding:30px 10px; color:#94a3b8; font-size:12px;"><i class="fa fa-info-circle fa-2x" style="margin-bottom:8px; display:block;"></i> Tidak ada butir CPL resmi prodi ditemukan pada kurikulum.</div>';
    } else {
      comparedList.forEach(function(item, idx) {
        var code = item.code;
        var normCode = (code || '').toUpperCase().replace(/\s+/g, '-');
        
        // Cek apakah sudah pernah dipilih di Studio CPL atau direkomendasikan RPS Lama
        var existingInStudio = activeCplMkList.find(function(x) {
          var kx = (x.kode || x.code || x.cpl_code || '').toUpperCase().replace(/\s+/g, '-');
          return kx === normCode;
        });

        var isChecked = !!existingInStudio || (item.matched && item.confidence === 'HIGH');
        var currentRole = existingInStudio ? (existingInStudio.role || 'SUPPORTING') : (item.suggested_role || 'SUPPORTING');

        window.modalCplRpsLamaSelection[normCode] = {
          code: code,
          category: item.category,
          description: item.description,
          checked: isChecked,
          role: currentRole,
          matched: item.matched,
          confidence: item.confidence,
          match_reason: item.match_reason
        };

        // Badge Relevansi
        var relBadge = '';
        if (item.confidence === 'HIGH') {
          relBadge = '<span style="font-size:10px; font-weight:700; background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:2px 7px; border-radius:4px;"><i class="fa fa-check-circle"></i> Sangat Relevan (' + item.overlap_score + '%)</span>';
        } else if (item.confidence === 'MEDIUM') {
          relBadge = '<span style="font-size:10px; font-weight:700; background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:2px 7px; border-radius:4px;"><i class="fa fa-dot-circle-o"></i> Kemiripan Sebagian (' + item.overlap_score + '%)</span>';
        } else {
          relBadge = '<span style="font-size:10px; color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; padding:2px 6px; border-radius:4px;">Tidak Ada di RPS Lama</span>';
        }

        var borderStyle = isChecked ? 'border-color:#3b82f6; background:#f8fafc;' : 'border-color:#e2e8f0; background:#ffffff;';

        cardsHtml += '<div class="cpl-rps-compare-card" id="card_cpl_compare_' + normCode + '" style="border:1px solid; ' + borderStyle + ' border-radius:7px; padding:10px 12px; margin-bottom:8px; transition:all 0.15s ease;">';
        cardsHtml += '  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:6px;">';
        cardsHtml += '    <label style="display:flex; align-items:center; gap:7px; margin:0; cursor:pointer; font-weight:700; font-size:13px; color:#0f172a;">';
        cardsHtml += '      <input type="checkbox" style="width:16px; height:16px; cursor:pointer;" ' + (isChecked ? 'checked' : '') + ' onchange="toggleCplRpsLamaItem(\'' + normCode + '\', this.checked)">';
        cardsHtml += '      <span>' + escapeHtml(code) + '</span>';
        cardsHtml += '      <span style="font-size:10px; font-weight:600; background:#e0f2fe; color:#0369a1; padding:1px 5px; border-radius:3px;">' + escapeHtml(item.category || 'CPL') + '</span>';
        cardsHtml += '    </label>';
        cardsHtml += '    <div>' + relBadge + '</div>';
        cardsHtml += '  </div>';

        // Deskripsi CPL
        cardsHtml += '  <div style="font-size:11.5px; color:#334155; line-height:1.45; margin-bottom:6px;">';
        cardsHtml += escapeHtml(item.description);
        cardsHtml += '  </div>';

        // Alasan / Snippet Match
        if (item.match_reason) {
          cardsHtml += '  <div style="font-size:10.5px; color:#0f766e; background:#f0fdfa; border:1px solid #ccfbf1; padding:3px 8px; border-radius:4px; margin-bottom:6px;">';
          cardsHtml += '    <i class="fa fa-info-circle"></i> ' + escapeHtml(item.match_reason);
          cardsHtml += '  </div>';
        }

        // Switcher Peran (Utama / Pendukung)
        var isPrimary = (currentRole === 'PRIMARY');
        cardsHtml += '  <div style="display:flex; justify-content:flex-end; align-items:center; gap:6px;">';
        cardsHtml += '    <span style="font-size:10.5px; color:#64748b;">Peran CPL-MK:</span>';
        cardsHtml += '    <div class="btn-group btn-group-xs" id="role_group_' + normCode + '">';
        cardsHtml += '      <button type="button" class="btn btn-xs ' + (isPrimary ? 'btn-primary' : 'btn-default') + '" onclick="setRoleCplRpsLamaItem(\'' + normCode + '\', \'PRIMARY\')" style="font-size:10.5px; padding:1px 7px;">★ Utama</button>';
        cardsHtml += '      <button type="button" class="btn btn-xs ' + (!isPrimary ? 'btn-info' : 'btn-default') + '" onclick="setRoleCplRpsLamaItem(\'' + normCode + '\', \'SUPPORTING\')" style="font-size:10.5px; padding:1px 7px;">🛡 Pendukung</button>';
        cardsHtml += '    </div>';
        cardsHtml += '  </div>';

        cardsHtml += '</div>';
      });
    }

    $('#container_cpl_rps_lama_mapping_cards').html(cardsHtml);
    updateCountSelectedCplRpsLamaModal();
  }

  /**
   * Toggle Checkbox pada Modal
   */
  function toggleCplRpsLamaItem(normCode, isChecked) {
    if (window.modalCplRpsLamaSelection && window.modalCplRpsLamaSelection[normCode]) {
      window.modalCplRpsLamaSelection[normCode].checked = isChecked;
      var $card = $('#card_cpl_compare_' + normCode);
      if (isChecked) {
        $card.css({ borderColor: '#3b82f6', background: '#f8fafc' });
      } else {
        $card.css({ borderColor: '#e2e8f0', background: '#ffffff' });
      }
      updateCountSelectedCplRpsLamaModal();
    }
  }

  /**
   * Ubah Role (Utama / Pendukung) pada Modal
   */
  function setRoleCplRpsLamaItem(normCode, role) {
    if (window.modalCplRpsLamaSelection && window.modalCplRpsLamaSelection[normCode]) {
      window.modalCplRpsLamaSelection[normCode].role = role;
      var $btnUtama = $('#role_group_' + normCode + ' button:first-child');
      var $btnPendukung = $('#role_group_' + normCode + ' button:last-child');
      if (role === 'PRIMARY') {
        $btnUtama.removeClass('btn-default').addClass('btn-primary');
        $btnPendukung.removeClass('btn-info').addClass('btn-default');
      } else {
        $btnUtama.removeClass('btn-primary').addClass('btn-default');
        $btnPendukung.removeClass('btn-default').addClass('btn-info');
      }
    }
  }

  /**
   * Pilih semua rekomendasi atau batal semua
   */
  function centangSemuaCplRpsLamaModal(onlyMatched) {
    if (!window.modalCplRpsLamaSelection) return;
    Object.keys(window.modalCplRpsLamaSelection).forEach(function(normCode) {
      var item = window.modalCplRpsLamaSelection[normCode];
      var check = onlyMatched ? (item.matched && item.confidence !== 'NONE') : false;
      item.checked = check;
      var $input = $('#card_cpl_compare_' + normCode + ' input[type="checkbox"]');
      $input.prop('checked', check);
      var $card = $('#card_cpl_compare_' + normCode);
      if (check) {
        $card.css({ borderColor: '#3b82f6', background: '#f8fafc' });
      } else {
        $card.css({ borderColor: '#e2e8f0', background: '#ffffff' });
      }
    });
    updateCountSelectedCplRpsLamaModal();
  }

  /**
   * Update count summary
   */
  function updateCountSelectedCplRpsLamaModal() {
    if (!window.modalCplRpsLamaSelection) return;
    var count = 0;
    Object.keys(window.modalCplRpsLamaSelection).forEach(function(k) {
      if (window.modalCplRpsLamaSelection[k].checked) count++;
    });
    $('#modal_cpl_rps_lama_count_selected').text(count + ' butir');
  }

  /**
   * Terapkan CPL Terpilih dari Modal RPS Lama ke Studio CPL
   */
  function terapkanCplRpsLamaKeStudio() {
    if (!window.modalCplRpsLamaSelection) return;
    
    var selectedCodes = [];
    Object.keys(window.modalCplRpsLamaSelection).forEach(function(normCode) {
      var item = window.modalCplRpsLamaSelection[normCode];
      if (item.checked) {
        selectedCodes.push(item);
      }
    });

    if (selectedCodes.length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Tidak Ada CPL Dipilih',
          text: 'Silakan centang setidaknya 1 butir CPL yang ingin diadopsi ke Studio CPL.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Silakan centang setidaknya 1 butir CPL.');
      }
      return;
    }

    // Perbarui activeCplMkList
    activeCplMkList = [];
    selectedCodes.forEach(function(item) {
      activeCplMkList.push({
        kode: item.code,
        code: item.code,
        teks: item.description,
        description: item.description,
        role: item.role || 'SUPPORTING',
        included: true,
        provenance: 'RPS_LAMA_ADOPTED',
        source: 'RPS_LAMA',
        reasoning: item.match_reason || 'Diadopsi dari arsip RPS Online'
      });
    });

    // Sinkronisasi status di cplStudioAllProdiList
    var selMap = {};
    activeCplMkList.forEach(function(x) {
      var k = (x.kode || x.code || '').toUpperCase().replace(/\s+/g, '-');
      selMap[k] = x;
    });

    cplStudioAllProdiList.forEach(function(c) {
      var k = (c.kode || '').toUpperCase().replace(/\s+/g, '-');
      if (selMap[k]) {
        c.selected = true;
        c.role = selMap[k].role;
        c.source = 'RPS_LAMA';
      } else {
        c.selected = false;
      }
    });

    cplStudioFilter = 'selected';
    $('#cpl_filter_buttons .btn').removeClass('active');
    $('#cpl_filter_buttons .btn:nth-child(2)').addClass('active');

    renderCplStudioUi();
    $('#modal_cpl_rps_lama').modal('hide');

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'CPL Berhasil Diterapkan!',
        text: selectedCodes.length + ' butir CPL dari Arsip RPS Lama berhasil diterapkan pada Studio CPL.',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert(selectedCodes.length + ' butir CPL dari Arsip RPS Lama berhasil diterapkan.');
    }
  }

  /**
   * Buka selector arsip lain
   */
  function cariLainArsipCplRpsLama() {
    $('#modal_cpl_rps_lama').modal('hide');
    bukaModalAmbilRpsLama();
  }


  /**
   * Ambil Deskripsi Mata Kuliah langsung dari dokumen kurikulum
   */
  function ambilDeskripsiDariKurikulum() {
    if (curriculumReadinessData && curriculumReadinessData.deskripsi_mk) {
      activeDeskripsiMk = cleanDeskripsiText(curriculumReadinessData.deskripsi_mk);
      activeDeskripsiMkSource = 'CURRICULUM';
      $('#studio_deskripsi_mk').val(activeDeskripsiMk);
      $('#badge_desc_source').text('Sumber: Kurikulum').css({ background: '#e0f2fe', color: '#0369a1', borderColor: '#bae6fd' });
    } else {
      var namaMk = $.trim($('#mk_nama').val());
      var kodeMk = $.trim($('#mk_kode').val());
      var prodi  = $.trim($('#mk_prodi').val());
      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_get_cpl_studio"); ?>',
        type: 'POST',
        data: {
          nama_mk: namaMk,
          kode_mk: kodeMk,
          nama_prodi: prodi,
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
          if (res.status && res.deskripsi_mk) {
            activeDeskripsiMk = cleanDeskripsiText(res.deskripsi_mk);
            activeDeskripsiMkSource = 'CURRICULUM';
            $('#studio_deskripsi_mk').val(activeDeskripsiMk);
            $('#badge_desc_source').text('Sumber: Kurikulum').css({ background: '#e0f2fe', color: '#0369a1', borderColor: '#bae6fd' });
          } else {
            alert('Deskripsi mata kuliah tidak ditemukan pada dokumen kurikulum.');
          }
        }
      });
    }
  }

  /**
   * Susun Draft Deskripsi Mata Kuliah dengan AI berdasarkan CPL & Bahan Kajian
   */
  function draftDeskripsiDenganAi() {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());
    var sks    = $.trim($('#mk_sks').val());
    var sem    = $.trim($('#mk_semester').val());

    if (!namaMk && !kodeMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    var $btn = $('#btn_draft_desc_ai');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyusun...');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_generate_course_description"); ?>',
      type: 'POST',
      data: {
        nama_mk: namaMk,
        kode_mk: kodeMk,
        nama_prodi: prodi,
        sks: sks,
        semester: sem,
        cpls_json: JSON.stringify(activeCplMkList),
        teks_kurikulum: uploadedDocs.kurikulum,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      complete: function() {
        $btn.prop('disabled', false).html(origHtml);
      },
      success: function(res) {
        if (res.status && res.deskripsi) {
          activeDeskripsiMk = res.deskripsi;
          activeDeskripsiMkSource = 'AI_DRAFT';
          $('#studio_deskripsi_mk').val(activeDeskripsiMk);
          $('#badge_desc_source').text('Sumber: Draft AI').css({ background: '#f3e8ff', color: '#7e22ce', borderColor: '#d8b4fe' });
        } else {
          alert('Gagal menyusun deskripsi: ' + (res.msg || 'Respon AI tidak valid.'));
        }
      },
      error: function() {
        alert('Terjadi kesalahan jaringan saat menyusun deskripsi.');
      }
    });
  }

  /**
   * Simpan Draft CPL Studio
   */
  function simpanDraftCplStudio() {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());
    var sks    = $.trim($('#mk_sks').val());
    var sem    = $.trim($('#mk_semester').val());
    var dosen  = $.trim($('#mk_dosen').val());

    if (!namaMk && !kodeMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    activeDeskripsiMk = $.trim($('#studio_deskripsi_mk').val());
    activeReferensi   = $.trim($('#studio_referensi_mk').val());

    // Pastikan flag included konsisten
    activeCplMkList.forEach(function(x) {
      if (typeof x.included === 'undefined') x.included = true;
    });

    var $btn = $('#btn_simpan_draft_cpl');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_save_cpl_studio"); ?>',
      type: 'POST',
      data: {
        id_rps: $('#id_rps_loaded').val(),
        nama_mk: namaMk,
        kode_mk: kodeMk,
        nama_prodi: prodi,
        sks: sks,
        semester: sem,
        dosen: dosen,
        cpl_mk_json: JSON.stringify(activeCplMkList),
        deskripsi_mata_kuliah: activeDeskripsiMk,
        deskripsi_mk: activeDeskripsiMk,
        deskripsi_mk_source: activeDeskripsiMkSource,
        referensi: activeReferensi,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      complete: function() {
        $btn.prop('disabled', false).html(origHtml);
      },
      success: function(res) {
        if (res.status) {
          if (res.id_rps) $('#id_rps_loaded').val(res.id_rps);
          activeCplMkStatus = 'DRAFT';
          renderCplStudioUi();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Draft CPL-MK Tersimpan',
              text: res.msg || 'Draf CPL & Deskripsi MK berhasil disimpan ke database.',
              timer: 1800,
              showConfirmButton: false
            });
          } else {
            alert('Draf CPL & Deskripsi MK berhasil disimpan.');
          }
        } else {
          alert('Gagal menyimpan draf CPL: ' + (res.msg || 'Unknown error'));
        }
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan jaringan saat menyimpan draf CPL.');
      }
    });
  }

  /**
   * Setujui CPL & Deskripsi MK (APPROVED)
   */
  function setujuiCplStudio() {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());
    var sks    = $.trim($('#mk_sks').val());
    var sem    = $.trim($('#mk_semester').val());
    var dosen  = $.trim($('#mk_dosen').val());

    if (!namaMk && !kodeMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    activeDeskripsiMk = $.trim($('#studio_deskripsi_mk').val());
    activeReferensi   = $.trim($('#studio_referensi_mk').val());

    // Validasi 1: Minimal 1 CPL digunakan dalam RPS
    activeCplMkList.forEach(function(x) {
      if (typeof x.included === 'undefined') x.included = true;
    });
    var selectedCpls = activeCplMkList.filter(function(x) { return x && (x.included !== false); });
    var totalIncluded = selectedCpls.length;
    if (totalIncluded === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Belum Ada CPL Dipilih',
          html: 'Pilih <strong>minimal 1 butir CPL resmi prodi</strong> yang digunakan dalam fokus RPS mata kuliah ini.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Peringatan: Pilih minimal satu butir CPL resmi yang digunakan dalam RPS.');
      }
      return;
    }

    // Validasi 2: Minimal 1 CPL Utama (PRIMARY)
    var primaryCpls = selectedCpls.filter(function(x) { return x.role === 'PRIMARY'; });
    if (primaryCpls.length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'CPL Utama Belum Dipilih',
          html: 'Wajib memilih <strong>minimal 1 CPL dengan peran Utama (Primary)</strong> yang dibebankan pada mata kuliah ini.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Peringatan: Wajib memilih minimal 1 CPL Utama (Primary).');
      }
      return;
    }

    // Validasi 3: Kelengkapan Peran (Tidak boleh ada CPL terpilih dengan role UNCLASSIFIED)
    var unclassifiedCpls = selectedCpls.filter(function(x) { return (!x.role || x.role === 'UNCLASSIFIED'); });
    if (unclassifiedCpls.length > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Peran CPL Belum Ditentukan',
          html: 'Masih terdapat <strong>' + unclassifiedCpls.length + ' CPL terpilih</strong> yang belum ditentukan perannya sebagai <strong>Utama</strong> atau <strong>Pendukung</strong>.<br><br>Silakan tentukan peran untuk seluruh CPL yang digunakan dalam RPS sebelum menyetujui.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Peringatan: Masih terdapat CPL terpilih yang belum ditentukan perannya sebagai Utama atau Pendukung.');
      }
      return;
    }

    // Validasi 4: Deskripsi Mata Kuliah
    if (!activeDeskripsiMk || activeDeskripsiMk.length < 15) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Deskripsi MK Masih Kosong',
          html: 'Deskripsi mata kuliah wajib diisi secara ringkas (minimal 15 karakter) sebagai grounding perumusan CPMK.',
          confirmButtonColor: '#0f766e'
        }).then(function() {
          $('#studio_deskripsi_mk').focus();
        });
      } else {
        alert('Peringatan: Deskripsi mata kuliah wajib diisi.');
        $('#studio_deskripsi_mk').focus();
      }
      return;
    }

    var doApprove = function() {
      var $btn = $('#btn_setujui_cpl');
      var origHtml = $btn.html();
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyetujui...');

      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_approve_cpl_studio"); ?>',
        type: 'POST',
        data: {
          id_rps: $('#id_rps_loaded').val(),
          nama_mk: namaMk,
          kode_mk: kodeMk,
          nama_prodi: prodi,
          sks: sks,
          semester: sem,
          dosen: dosen,
          cpl_mk_json: JSON.stringify(activeCplMkList),
          deskripsi_mata_kuliah: activeDeskripsiMk,
          deskripsi_mk: activeDeskripsiMk,
          deskripsi_mk_source: activeDeskripsiMkSource,
          referensi: activeReferensi,
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        complete: function() {
          $btn.prop('disabled', false).html(origHtml);
        },
        success: function(res) {
          if (res.status) {
            if (res.id_rps) $('#id_rps_loaded').val(res.id_rps);
            activeCplMkStatus = 'APPROVED';
            renderCplStudioUi();

            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'CPL & Deskripsi MK Disetujui!',
                html: '<div style="text-align:left; font-size:13px; color:#334155; line-height:1.6;">'
                    + '<strong>' + activeCplMkList.length + ' butir CPL</strong> dan <strong>Deskripsi Mata Kuliah</strong> telah disetujui (APPROVED).<br><br>'
                    + 'Tahap berikutnya: Buka <strong>Studio CPMK</strong> untuk menyusun draft Capaian Pembelajaran Mata Kuliah.'
                    + '</div>',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa fa-bullseye"></i> Buka Studio CPMK',
                cancelButtonText: 'Tetap di Sini'
              }).then(function(result) {
                if (result.value || result.isConfirmed) {
                  switchTab('susun_cpmk');
                }
              });
            } else {
              alert('CPL & Deskripsi MK berhasil disetujui.');
            }
          } else {
            alert('Gagal menyetujui CPL Studio: ' + (res.msg || 'Unknown error'));
          }
        },
        error: function(xhr, status, error) {
          alert('Terjadi kesalahan jaringan saat menyetujui CPL.');
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Setujui CPL & Deskripsi MK?',
        html: 'Anda akan menetapkan <strong>' + activeCplMkList.length + ' CPL</strong> (' + primaryCpls.length + ' Utama) sebagai acuan resmi perkuliahan mata kuliah <strong>' + escapeHtml(namaMk) + '</strong>.<br><br>Status CPL-MK akan menjadi <strong>APPROVED</strong> dan membuka Studio CPMK.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f766e',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-check-circle"></i> Ya, Setujui CPL-MK',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.value || result.isConfirmed) {
          doApprove();
        }
      });
    } else {
      if (confirm('Setujui ' + activeCplMkList.length + ' CPL untuk mata kuliah ' + namaMk + '?')) {
        doApprove();
      }
    }
  }

  /**
   * Buka Kembali CPL Studio untuk Revisi
   */
  function revisiCplStudio() {
    var doReopen = function() {
      var namaMk = $.trim($('#mk_nama').val());
      var kodeMk = $.trim($('#mk_kode').val());
      var prodi  = $.trim($('#mk_prodi').val());

      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_revise_cpl_studio"); ?>',
        type: 'POST',
        data: {
          id_rps: $('#id_rps_loaded').val(),
          kode_mk: kodeMk,
          nama_mk: namaMk,
          nama_prodi: prodi,
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
          if (res.status) {
            activeCplMkStatus = 'DRAFT';
            renderCplStudioUi();
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'info',
                title: 'Status CPL-MK Kembali ke DRAFT',
                text: 'Anda dapat mengedit kembali pilihan CPL, perannya, deskripsi mata kuliah, dan referensi.',
                timer: 2000,
                showConfirmButton: false
              });
            }
          }
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Buka Revisi CPL & Deskripsi?',
        html: '<div style="text-align:left; font-size:13px; color:#334155; line-height:1.6;">'
            + 'Status CPL-MK akan kembali menjadi <strong>DRAFT</strong>.<br><br>'
            + '<span style="color:#b45309;"><i class="fa fa-warning"></i> Catatan: Jika ada butir CPMK yang telah disusun sebelumnya, pastikan untuk meninjau kembali keselarasan CPL terkait.</span>'
            + '</div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-pencil"></i> Ya, Buka Revisi',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.value || result.isConfirmed) {
          doReopen();
        }
      });
    } else {
      if (confirm('Buka kembali CPL & Deskripsi untuk revisi?')) {
        doReopen();
      }
    }
  }

  // Configure Marked.js options
  if (typeof marked !== 'undefined') {
    marked.setOptions({
      gfm: true,
      breaks: false, // Breaks false ensures markdown tables are not split by accidental linebreaks
      tables: true
    });
  }

  /**
   * Normalisasi format tabel Markdown agar selalu valid GFM:
   * - Menyatukan pecahan baris delimiter yang terputus enter
   * - Menghilangkan delimiter yatim/penutup (|---|---|) yang tidak memiliki header valid
   * - Mencegah baris kosong yang memutus tabel di tengah-tengah
   * - Memastikan jarak baris kosong sebelum dan sesudah tabel/heading
   */
  function sanitizeMarkdownTables(md) {
    if (!md) return '';
    md = md.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    var lines = md.split('\n');
    var cleanedLines = [];
    var inTable = false;
    var tableHasDelimiter = false;

    for (var i = 0; i < lines.length; i++) {
      var raw = lines[i];
      var line = raw.trim();

      // Check if line is a table row
      var isRow = line.length > 0 && line.charAt(0) === '|' && (line.match(/\|/g) || []).length >= 2;
      var isDelimiter = isRow && /^\|?[ \t]*[-:| \t]+\|?$/.test(line) && !/[a-zA-Z0-9]/.test(line) && (line.match(/-/g) || []).length >= 2;

      if (isDelimiter) {
        if (inTable && !tableHasDelimiter) {
          // Delimiter untuk header tabel saat ini
          tableHasDelimiter = true;
          cleanedLines.push(line);
          continue;
        } else if (inTable && tableHasDelimiter) {
          // Delimiter redundant di tengah tabel -> lewati
          continue;
        }
      }

      if (isRow) {
        if (!inTable) {
          // Memulai tabel baru
          if (cleanedLines.length > 0 && cleanedLines[cleanedLines.length - 1].trim() !== '') {
            cleanedLines.push('');
          }
          inTable = true;
          tableHasDelimiter = false;
        } else {
          // Jika di tengah tabel muncul header berulang (misal | Minggu | Kemampuan...), lewati header & delimiter tambahannya
          if (/^\|\s*(?:Minggu|No|Kemampuan|CPMK|Sub-CPMK|Modul)\s*\|/i.test(line)) {
            if (i + 1 < lines.length && /^\|?[ \t]*[-:| \t]+\|?$/.test(lines[i + 1].trim())) {
              i++; // lewati delimiter duplikat juga
            }
            continue;
          }
        }
        cleanedLines.push(line);
        continue;
      }

      // Baris kosong
      if (line === '') {
        // Jika baris berikutnya masih baris tabel, JANGAN putus tabel dengan baris kosong!
        var nextIsRow = false;
        for (var j = i + 1; j < lines.length; j++) {
          if (lines[j].trim() === '') continue;
          if (lines[j].trim().charAt(0) === '|') nextIsRow = true;
          break;
        }
        if (inTable && nextIsRow) {
          // Lewati baris kosong di tengah tabel agar tabel tetap 1 kesatuan utuh
          continue;
        }
        inTable = false;
        tableHasDelimiter = false;
        cleanedLines.push('');
        continue;
      }

      // Non-table line
      inTable = false;
      tableHasDelimiter = false;

      // Jika heading, beri jarak baris kosong sebelumnya
      if (/^(?:#{1,6}\s+|[A-Z]\.\s+[A-Z])/.test(line)) {
        if (cleanedLines.length > 0 && cleanedLines[cleanedLines.length - 1].trim() !== '') {
          cleanedLines.push('');
        }
      }

      cleanedLines.push(line);
    }

    return cleanedLines.join('\n');
  }

  function renderMarkdownSafe(md) {
    if (!md) return '';
    var cleanMd = sanitizeMarkdownTables(md);
    if (typeof marked !== 'undefined') {
      return marked.parse(cleanMd);
    }
    return '<pre style="white-space:pre-wrap; background:#f8fafc; border:1px solid #e2e8f0; padding:15px; border-radius:6px;">' + escapeHtml(cleanMd) + '</pre>';
  }

  /**
   * Helper: Mencocokkan nilai Semester ke elemen dropdown #mk_semester
   */
  function setSemesterValue(sem) {
    if (!sem) return;
    var semStr = String(sem).trim();
    if (!semStr) return;

    // 1. Coba pencocokan nilai langsung
    $('#mk_semester').val(semStr);
    if ($('#mk_semester').val()) return;

    // 2. Jika berupa angka (contoh: '1' s/d '8')
    var matchDigit = semStr.match(/\d+/);
    if (matchDigit) {
      var d = matchDigit[0];
      $('#mk_semester').val('Semester ' + d);
      if ($('#mk_semester').val()) return;
    }

    // 3. Cari berdasarkan pencocokan teks di opsi select
    $('#mk_semester option').each(function() {
      var val = $(this).val();
      var txt = $(this).text();
      if (val && (val.toLowerCase().indexOf(semStr.toLowerCase()) !== -1 || txt.toLowerCase().indexOf(semStr.toLowerCase()) !== -1)) {
        $('#mk_semester').val(val);
        return false;
      }
    });
  }

  /**
   * Mengisi otomatis Nama MK, Kode MK, SKS, dan Program Studi saat dipilih dari Select2 AJAX MK
   */
  function pilihMkObject(item) {
    if (!item || (!item.nm_mk && !item.text)) {
      $('#mk_nama').val('');
      $('#mk_kode').val('');
      $('#mk_sks').val('');
      $('#mk_sks_display').val('');
      $('#mk_prodi').val('');
      $('#mk_semester').val('');
      $('#id_rps_loaded').val('');
      activeCplMkList = [];
      activeCplMkStatus = null;
      activeDeskripsiMk = '';
      activeDeskripsiMkSource = 'CURRICULUM';
      activeReferensi = '';
      cplStudioAllProdiList = [];
      cplStudioMappedCandidates = [];
      activeCpmkList = [];
      activeCpmkStatus = null;
      curriculumReadinessData = null;
      $('#section_curriculum_readiness').slideUp(150);
      resetPustakaUi();
      switchTab('studio_cpl');
      return;
    }

    var val   = item.nm_mk || '';
    var kode  = item.kode_mk || '';
    var sks   = parseInt(item.sks) || 0;
    var prodi = item.nm_prodi || '';

    $('#mk_nama').val(val);
    $('#mk_kode').val(kode);
    $('#mk_prodi').val(prodi);

    // Reset CPL Studio state for new MK selection
    activeCplMkList = [];
    activeCplMkStatus = null;
    activeDeskripsiMk = '';
    activeDeskripsiMkSource = 'CURRICULUM';
    activeReferensi = '';
    cplStudioAllProdiList = [];
    cplStudioMappedCandidates = [];

    if (sks > 0) {
      var jam  = sks * 45;
      $('#mk_sks').val(sks);
      $('#mk_sks_display').val(sks + ' SKS (' + jam + ' Jam)');
    } else {
      $('#mk_sks').val('');
      $('#mk_sks_display').val('');
    }

    // Otomatis periksa kesiapan kurikulum (Curriculum Readiness - Faktual)
    checkCurriculumReadiness();

    // Otomatis cek apakah pustaka kurikulum prodi ini sudah ada
    if (prodi) {
      checkPustakaProdi(prodi);
    } else {
      resetPustakaUi();
    }

    // Default ke Studio CPL saat MK baru dipilih
    switchTab('studio_cpl');

    // Otomatis periksa apakah sudah ada dokumen RPS & turunannya yang tersimpan di database untuk MK ini
    if (kode || val) {
      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_check_existing_rps_mk"); ?>',
        type: 'POST',
        data: {
          kode_mk: kode,
          nm_mk: val,
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
          if (res.status && res.found && res.rps) {
            $('#id_rps_loaded').val(res.rps.id_rps);
            var rProdi = res.rps.nama_prodi || res.rps.prodi || prodi;
            if (rProdi) {
              $('#mk_prodi').val(rProdi);
              checkPustakaProdi(rProdi);
            }
            if (res.rps.semester) {
              setSemesterValue(res.rps.semester);
            }
            populateModeDocStoreFromRps(res.rps, res.details);

            var isCplApproved = (res.rps.cpl_mk_status === 'APPROVED');
            var isCpmkApproved = (res.rps.cpmk_status === 'APPROVED');
            var hasCpmk = (activeCpmkList && activeCpmkList.length > 0);
            var hasRpsMaster = (res.rps.raw_ai_output && res.rps.raw_ai_output.trim().length > 0);

            if (!isCplApproved) {
              switchTab('studio_cpl');
            } else if (hasCpmk && !isCpmkApproved) {
              switchTab('susun_cpmk');
            } else if (isCpmkApproved && hasRpsMaster) {
              switchTab('susun_rps');
            } else if (hasCpmk) {
              switchTab('susun_cpmk');
            } else {
              switchTab('studio_cpl');
            }
          }
        }
      });
    }
  }

  /**
   * Cek Curriculum Readiness (Faktual) untuk Mata Kuliah yang dipilih
   */
  /**
   * Helper: Render / Update Progress Bar for Curriculum Readiness
   */
  function renderReadinessProgressBar(pct, stepText) {
    var html = '<div id="readiness_progress_wrapper" style="padding:10px 4px 12px 4px;">'
             + '<div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; margin-bottom:6px; color:#475569;">'
             + '<span id="readiness_step_text" style="font-weight:600;"><i class="fa fa-spinner fa-spin" style="color:#0f766e; margin-right:4px;"></i> ' + escapeHtml(stepText) + '</span>'
             + '<span id="readiness_step_pct" style="font-weight:700; color:#0f766e; font-size:11px;">' + pct + '%</span>'
             + '</div>'
             + '<div style="height:6px; background:#e2e8f0; border-radius:10px; overflow:hidden; position:relative;">'
             + '<div id="readiness_progress_bar" style="height:100%; width:' + pct + '%; background:linear-gradient(90deg, #0f766e, #06b6d4); transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius:10px;"></div>'
             + '</div>'
             + '<div style="font-size:10px; color:#94a3b8; margin-top:5px; text-align:center;">Verifikasi Faktual Dokumen Kurikulum OBE</div>'
             + '</div>';
    $('#readiness_checklist_container').html(html);
  }

  function updateReadinessProgressBar(pct, stepText) {
    $('#readiness_step_text').html('<i class="fa fa-spinner fa-spin" style="color:#0f766e; margin-right:4px;"></i> ' + escapeHtml(stepText));
    $('#readiness_step_pct').text(pct + '%');
    $('#readiness_progress_bar').css('width', pct + '%');
  }

  /**
   * Helper: Sinkronisasi data kesiapan kurikulum ke state aktif (Phase 1.6)
   */
  function applyCurriculumReadinessData(res) {
    if (!res) return;
    curriculumReadinessData = res;

    // Auto-fill Semester dari kurikulum jika belum terisi
    if (res.semester && (!parseInt($('#mk_semester').val()) || $('#mk_semester').val() === '')) {
      if (typeof setSemesterValue === 'function') {
        setSemesterValue(res.semester);
      } else {
        $('#mk_semester').val(res.semester);
      }
    }

    // Auto-fill SKS dari kurikulum jika belum terisi
    if (res.sks_total && (!parseInt($('#mk_sks').val()) || parseInt($('#mk_sks').val()) === 0)) {
      var sksVal = parseInt(res.sks_total);
      var jamVal = sksVal * 45;
      $('#mk_sks').val(sksVal);
      $('#mk_sks_display').val(sksVal + ' SKS (' + jamVal + ' Jam)');
    }

    if (res.cpl_mk_data && res.cpl_mk_data.length > 0 && activeCplMkList.length === 0) {
      activeCplMkList = res.cpl_mk_data;
      activeCplMkStatus = res.cpl_mk_status || 'DRAFT';
    }
    if (res.deskripsi_mk && !activeDeskripsiMk) {
      activeDeskripsiMk = cleanDeskripsiText(res.deskripsi_mk);
      activeDeskripsiMkSource = res.deskripsi_mk_source || 'CURRICULUM';
    }
    if (res.referensi && !activeReferensi) {
      activeReferensi = cleanReferensiText(res.referensi);
    }
    if (res.cpmk_data && res.cpmk_data.length > 0 && activeCpmkList.length === 0) {
      activeCpmkList = res.cpmk_data;
      activeCpmkStatus = res.cpmk_status || 'AI_PROPOSAL';
      renderCpmkCards();
    }
    loadCplStudioData();
  }

  /**
   * Cek Curriculum Readiness (Faktual) untuk Mata Kuliah yang dipilih
   * Dilengkapi client-side cache & debouncing untuk mencegah beban berlebih ke server
   */
  function checkCurriculumReadiness(forceRefresh) {
    var namaMk = $.trim($('#mk_nama').val());
    var kodeMk = $.trim($('#mk_kode').val());
    var prodi  = $.trim($('#mk_prodi').val());
    var sks    = $.trim($('#mk_sks').val());
    var sem    = $.trim($('#mk_semester').val());

    if (!namaMk && !kodeMk) {
      $('#section_curriculum_readiness').slideUp(150);
      curriculumReadinessData = null;
      return;
    }

    var cacheKey = (prodi + '___' + kodeMk + '___' + namaMk).toLowerCase();

    // Jika sudah ada di cache dan tidak forceRefresh, tampilkan langsung secara instan (0 beban server)
    if (!forceRefresh && curriculumReadinessCache[cacheKey]) {
      $('#section_curriculum_readiness').slideDown(150);
      var cached = curriculumReadinessCache[cacheKey];
      applyCurriculumReadinessData(cached);
      renderCurriculumReadinessUi(cached, false);
      return;
    }

    if (readinessDebounceTimer) {
      clearTimeout(readinessDebounceTimer);
    }
    if (readinessStepTimer) {
      clearTimeout(readinessStepTimer);
    }

    $('#section_curriculum_readiness').slideDown(150);
    renderReadinessProgressBar(20, '1/3 Memeriksa Dokumen Kurikulum...');

    readinessStepTimer = setTimeout(function() {
      updateReadinessProgressBar(65, '2/3 Mencocokkan CPL & Bahan Kajian...');
    }, 120);

    readinessDebounceTimer = setTimeout(function() {
      executeCheckCurriculumReadiness(namaMk, kodeMk, prodi, sks, sem, cacheKey);
    }, 180);
  }

  function executeCheckCurriculumReadiness(namaMk, kodeMk, prodi, sks, sem, cacheKey) {
    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_check_curriculum_readiness"); ?>',
      type: 'POST',
      data: {
        nama_mk: namaMk,
        kode_mk: kodeMk,
        nama_prodi: prodi,
        sks: sks,
        semester: sem,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          // Tuntaskan visual progress bar ke 100%
          updateReadinessProgressBar(100, '3/3 Verifikasi Faktual Selesai!');
          // Simpan ke cache
          curriculumReadinessCache[cacheKey] = res;
          applyCurriculumReadinessData(res);

          // Beri sedikit jeda mikro (180ms) agar transisi 100% terlihat mulus sebelum checklist muncul
          setTimeout(function() {
            renderCurriculumReadinessUi(res, true);
          }, 180);
        } else {
          $('#readiness_checklist_container').html('<div style="padding:10px; color:#dc2626; font-size:12px;"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(res.msg || 'Gagal memeriksa kurikulum') + '</div>');
        }
      },
      error: function(xhr, status, error) {
        var errDetail = '';
        if (xhr.responseJSON && xhr.responseJSON.msg) {
          errDetail = xhr.responseJSON.msg;
        } else if (xhr.responseText) {
          try {
            var parsed = JSON.parse(xhr.responseText);
            if (parsed.msg) errDetail = parsed.msg;
          } catch(e) {}
        }
        $('#readiness_checklist_container').html('<div style="padding:10px; color:#dc2626; font-size:12px;"><i class="fa fa-exclamation-triangle"></i> ' + (errDetail ? escapeHtml(errDetail) : 'Terjadi kesalahan saat memeriksa kurikulum.') + '</div>');
      }
    });
  }

  /**
   * Render Badge & Checklist Faktual Curriculum Readiness di Panel Kiri
   * Dilengkapi animasi sequential reveal bertahap (staggered) - Phase 1.6
   */
  function renderCurriculumReadinessUi(res, animate) {
    if (!res) return;
    var c = res.checks || {};
    var getCheck = function(key, defaultLabel) {
      return (c && c[key]) ? c[key] : { status: false, label: defaultLabel || 'Tidak tersedia', detail: '' };
    };

    var kd = getCheck('kurikulum_dokumen', 'Tidak Ditemukan');
    var mk = getCheck('mk_terdaftar', 'Tidak Ditemukan');
    var cp = getCheck('cpl_prodi', 'Tidak Ada');
    var cbk = getCheck('cpl_bk_mapping', 'Tidak Ada');
    var cm = getCheck('cpl_mk_mapping', '— Tidak ditemukan');
    var bk = getCheck('bahan_kajian', 'Tidak Ditemukan');
    var dm = getCheck('deskripsi_mk', 'Tidak Ditemukan');
    var cs = getCheck('cpmk_status', 'Belum Ada');

    // Mapping Type Logic (Multi-Struktur & Provenance Terpisah - Phase 1.7 Trust & Governance Guard)
    var mSrcStatus = cm.mapping_parse_status || cm.mapping_source_status || 'SOURCE_MAPPING_NOT_AVAILABLE';
    var mType      = cm.effective_mapping_type || cm.mapping_type || (cm.status ? 'EXPLICIT_MATRIX' : 'NOT_AVAILABLE');
    var mappingOk  = (mType === 'EXPLICIT_MATRIX' || mType === 'EXPLICIT_SECTION' || mType === 'DERIVED_VIA_BK' || mType === 'AI_EXTRACTED_MAPPING' || (mSrcStatus === 'SOURCE_MAPPING_PARSED' && mType !== 'IN_MATRIX_NO_CPL_MARKED' && mType !== 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX'));
    var mappingWarn = (mType === 'AI_PROPOSAL_MAPPING' || mType === 'IN_MATRIX_NO_CPL_MARKED' || mType === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX' || mSrcStatus === 'SOURCE_MAPPING_PARTIAL' || mSrcStatus === 'SOURCE_MAPPING_AVAILABLE_UNPARSED' || mSrcStatus === 'SOURCE_MAPPING_PARSER_ERROR' || mSrcStatus === 'MATRIX_UNPARSED' || mSrcStatus === 'SECTION_UNPARSED');
    
    var mappingLabel = cm.label;
    if (!mappingLabel || mappingLabel.indexOf('—') === 0 || mappingLabel === 'Belum terpetakan') {
      if (mType === 'IN_MATRIX_NO_CPL_MARKED') {
        mappingLabel = 'Ada pada matriks resmi, namun tidak diberi CPL pada dokumen';
      } else if (mType === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
        mappingLabel = 'Mata kuliah aktif — tidak tercantum pada Matriks CPL-MK resmi';
      } else if (mSrcStatus === 'SOURCE_MAPPING_AVAILABLE_UNPARSED' || mSrcStatus === 'MATRIX_UNPARSED' || mSrcStatus === 'SECTION_UNPARSED') {
        mappingLabel = 'Pemetaan tersedia di kurikulum — belum berhasil dibaca sistem';
      } else if (mSrcStatus === 'SOURCE_MAPPING_PARTIAL') {
        mappingLabel = 'Pemetaan sebagian berhasil dibaca';
      } else if (mSrcStatus === 'SOURCE_MAPPING_PARSER_ERROR') {
        mappingLabel = 'Pemetaan tersedia di kurikulum — terjadi kendala parsing';
      } else if (mType === 'DERIVED_VIA_BK') {
        mappingLabel = (cm.mapped_cpl_count ? cm.mapped_cpl_count + ' CPL terpetakan untuk MK terpilih' : 'Diturunkan melalui Bahan Kajian');
      } else if (mType === 'EXPLICIT_MATRIX' || mType === 'EXPLICIT_SECTION' || mType === 'AI_EXTRACTED_MAPPING' || mSrcStatus === 'SOURCE_MAPPING_PARSED') {
        mappingLabel = (cm.mapped_cpl_count ? cm.mapped_cpl_count + ' CPL terpetakan untuk MK terpilih' : 'Mapping langsung dari kurikulum');
      } else if (mType === 'AI_PROPOSAL_MAPPING') {
        mappingLabel = 'Rekomendasi AI — bukan mapping resmi kurikulum';
      } else if (mSrcStatus === 'SOURCE_MAPPING_NOT_AVAILABLE') {
        mappingLabel = 'Tidak ada pemetaan resmi pada dokumen kurikulum';
      } else {
        mappingLabel = '— Tidak ditemukan';
      }
    }

    var descOk = (dm.status === true || dm.label === 'Tersedia dari Kurikulum');
    var descWarn = (dm.label === 'Tersedia dari RPS');

    var rows = [
      {
        label: 'Dokumen Kurikulum',
        ok: !!kd.status,
        badge: kd.label || (kd.status ? 'Tersedia' : 'Tidak Ditemukan'),
        detail: kd.detail || '',
        subtext: kd.nama_prodi_resmi ? ('SK: ' + kd.nama_prodi_resmi) : ''
      },
      {
        label: 'Mata Kuliah di Kurikulum',
        ok: !!mk.status,
        badge: mk.label || (mk.status ? 'Ditemukan' : 'Tidak Ditemukan'),
        detail: mk.detail || ''
      },
      {
        label: 'CPL Prodi',
        ok: !!cp.status,
        badge: cp.label || (cp.status ? ((cp.total_cpl || cp.official_cpl_count) ? (cp.total_cpl || cp.official_cpl_count) + ' CPL ditemukan' : 'Ditemukan') : 'Tidak Ada'),
        detail: cp.detail || ''
      },
      {
        label: 'Pemetaan CPL ↔ BK',
        ok: (cbk.status === true || cbk.cpl_bk_mapping_status === 'PARSED'),
        warn: (cbk.cpl_bk_mapping_status === 'AVAILABLE_UNPARSED'),
        badge: cbk.label || ((cbk.cpl_bk_mapping_status === 'PARSED' && cbk.cpl_bk_mappable_count !== null)
          ? (cbk.cpl_bk_mappable_count + ' CPL memiliki mapping BK')
          : (cbk.cpl_bk_mapping_status === 'AVAILABLE_UNPARSED' ? 'Tersedia di Kurikulum' : 'Tidak Ada')),
        detail: cbk.detail || ''
      },
      {
        label: 'Pemetaan CPL ↔ MK',
        ok: mappingOk,
        warn: mappingWarn,
        badge: mappingLabel,
        detail: cm.detail || '',
        isDerivedBk: (mType === 'DERIVED_VIA_BK'),
        isAiProposal: (mType === 'AI_PROPOSAL_MAPPING')
      },
      {
        label: 'Bahan Kajian',
        ok: !!bk.status,
        badge: bk.label || (bk.status ? 'Ditemukan' : 'Tidak Ditemukan'),
        detail: bk.detail || ''
      },
      {
        label: 'Deskripsi Mata Kuliah',
        ok: descOk,
        warn: descWarn,
        badge: dm.label || (descOk ? 'Tersedia dari Kurikulum' : 'Tidak Ditemukan'),
        detail: dm.detail || ''
      },
      {
        label: 'CPMK Terkini',
        ok: (cs.status === 'APPROVED'),
        warn: (cs.status === 'AI_PROPOSAL'),
        badge: cs.label || (cs.status === 'APPROVED' ? 'APPROVED' : (cs.status === 'AI_PROPOSAL' ? 'Draft AI' : 'Belum Ada')),
        detail: cs.detail || ''
      }
    ];

    var html = '<div style="display:flex; flex-direction:column; gap:6px;">';
    rows.forEach(function(r, idx) {
      var icon = r.ok ? '<i class="fa fa-check-circle" style="color:#16a34a; font-size:13px;"></i>' : (r.warn ? '<i class="fa fa-exclamation-circle" style="color:#d97706; font-size:13px;"></i>' : '<i class="fa fa-circle-o" style="color:#94a3b8; font-size:13px;"></i>');
      var bgBadge = r.ok ? '#dcfce7' : (r.warn ? '#fef3c7' : '#f1f5f9');
      var colorBadge = r.ok ? '#15803d' : (r.warn ? '#b45309' : '#64748b');
      var borderBadge = r.ok ? '#86efac' : (r.warn ? '#fde68a' : '#cbd5e1');

      var extraBadge = r.isAiExtracted ? '<span style="font-size:8.5px; background:#e0e7ff; color:#3730a3; padding:1px 4px; border-radius:3px; margin-left:3px;">AI</span>' : '';

      var rowStyle = animate
        ? 'style="display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px dashed #f1f5f9; font-size:11.5px; opacity:0; transform:translateY(4px); transition:opacity 0.22s ease-out, transform 0.22s ease-out;"'
        : 'style="display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px dashed #f1f5f9; font-size:11.5px;"';

      html += '<div class="readiness-item-row" ' + rowStyle + '>'
            + '<div style="display:flex; flex-direction:column;">'
            + '<div style="display:flex; align-items:center; gap:6px; color:#334155;">'
            + icon + ' <span>' + escapeHtml(r.label) + '</span>'
            + '</div>'
            + (r.subtext ? '<span style="font-size:10px; color:#64748b; margin-left:19px;">' + escapeHtml(r.subtext) + '</span>' : '')
            + '</div>'
            + '<div>'
            + '<span style="background:' + bgBadge + '; color:' + colorBadge + '; border:1px solid ' + borderBadge + '; font-size:10px; font-weight:700; padding:1px 6px; border-radius:4px;" title="' + escapeHtml(r.detail || '') + '">'
            + escapeHtml(r.badge)
            + '</span>'
            + extraBadge
            + '</div>'
            + '</div>';
    });
    html += '</div>';

    var totalCpl = (cp.total_cpl !== undefined) ? parseInt(cp.total_cpl) : (cp.status ? 1 : 0);
    var hasCpl = (cp.status === true && totalCpl > 0);

    // Readiness minimal untuk enable Generate CPMK:
    // Dokumen kurikulum = tersedia AND mata kuliah ditemukan AND official CPL count > 0 AND (bahan kajian OR deskripsi)
    var canGenerate = (res.can_generate_cpmk !== undefined)
      ? !!res.can_generate_cpmk
      : (kd.status && mk.status && hasCpl && (bk.status || descOk || descWarn));

    // Update overall badge in section title
    if (canGenerate) {
      $('#badge_readiness_overall').text('SIAP FAKTUAL').css({ background: '#dcfce7', color: '#15803d', border: '1px solid #86efac' });
    } else if (!hasCpl || !kd.status || !mk.status) {
      $('#badge_readiness_overall').text('TIDAK SIAP').css({ background: '#fee2e2', color: '#b91c1c', border: '1px solid #fca5a5' });
    } else {
      $('#badge_readiness_overall').text('Sebagian').css({ background: '#fef3c7', color: '#b45309', border: '1px solid #fde68a' });
    }

    $('#readiness_checklist_container').html(html);

    if (animate) {
      $('#readiness_checklist_container').find('.readiness-item-row').each(function(index) {
        var $row = $(this);
        setTimeout(function() {
          $row.css({ opacity: 1, transform: 'translateY(0)' });
        }, index * 35);
      });
    }
  }

  function bukaCpmkStudio() {
    if (curriculumReadinessData && curriculumReadinessData.can_generate_cpmk === false) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'CPL Resmi Belum Tersedia',
          text: 'Tidak dapat menyusun CPMK karena CPL resmi Program Studi belum tersedia pada dokumen kurikulum.',
          confirmButtonColor: '#dc2626'
        });
      } else {
        alert('Tidak dapat menyusun CPMK: CPL resmi Program Studi belum tersedia pada dokumen kurikulum.');
      }
      return;
    }

    if (activeCplMkStatus !== 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Setujui CPL & Deskripsi Terlebih Dahulu',
          html: '<div style="text-align:left; font-size:13px; line-height:1.6;">'
              + 'Sebelum membuka Studio CPMK, silakan <strong>pilih CPL yang dibebankan dan setujui CPL &amp; Deskripsi MK</strong> pada tab <strong>Studio CPL &amp; Deskripsi</strong>.<br><br>'
              + '<span style="font-size:12px; color:#64748b;"><i class="fa fa-info-circle"></i> Ini memastikan bahwa CPMK hanya dirumuskan dari butir CPL resmi yang telah diverifikasi dosen.</span>'
              + '</div>',
          confirmButtonColor: '#0f766e',
          confirmButtonText: '<i class="fa fa-th-list"></i> Buka Studio CPL'
        }).then(function() {
          switchTab('studio_cpl');
        });
      } else {
        alert('Sebelum membuka Studio CPMK, silakan pilih dan setujui CPL-MK pada Studio CPL terlebih dahulu.');
        switchTab('studio_cpl');
      }
      return;
    }

    switchTab('susun_cpmk');
    if (!activeCpmkList || activeCpmkList.length === 0) {
      jalankanAksi('susun_cpmk');
    }
  }

  function jalankanGenerateCpmk() {
    if (curriculumReadinessData && curriculumReadinessData.can_generate_cpmk === false) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'CPL Resmi Belum Tersedia',
          text: 'Tidak dapat menyusun CPMK karena CPL resmi Program Studi belum tersedia pada dokumen kurikulum.',
          confirmButtonColor: '#dc2626'
        });
      } else {
        alert('Tidak dapat menyusun CPMK: CPL resmi Program Studi belum tersedia pada dokumen kurikulum.');
      }
      return;
    }

    if (activeCplMkStatus !== 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'CPL & Deskripsi Belum Disetujui',
          text: 'Harap setujui CPL-MK pada Studio CPL terlebih dahulu sebelum generate CPMK.',
          confirmButtonColor: '#0f766e'
        });
      } else {
        alert('Harap setujui CPL-MK pada Studio CPL terlebih dahulu.');
      }
      return;
    }

    jalankanAksi('susun_cpmk');
  }

  var studioCpmkData = null;

  /**
   * Muat data Studio CPMK dari backend (Official CPMK first, Approved CPL grounding, Coverage matrix)
   */
  function loadCpmkStudioData(callback) {
    var idRps = $('#id_rps_loaded').val();
    var kodeMk = $('#mk_kode').val();
    var namaMk = $('#mk_nama').val();
    var prodi = $('#mk_prodi').val();

    if (!namaMk) {
      $('#cpmk_cards_container').html('<div style="text-align:center; padding:30px; color:#94a3b8; font-size:12px;"><i class="fa fa-info-circle" style="font-size:24px; margin-bottom:8px; display:block;"></i>Silakan pilih Mata Kuliah pada panel kiri terlebih dahulu.</div>');
      return;
    }

    $('#cpmk_cards_container').html('<div style="text-align:center; padding:30px; color:#64748b; font-size:12px;"><i class="fa fa-spinner fa-spin" style="font-size:18px; margin-bottom:6px; display:block;"></i>Memuat data Studio CPMK &amp; inspeksi CPMK resmi kurikulum...</div>');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_get_cpmk_studio"); ?>',
      type: 'POST',
      data: {
        id_rps: idRps,
        kode_mk: kodeMk,
        nama_mk: namaMk,
        prodi: prodi,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.gate_locked) {
          activeCpmkStatus = 'LOCKED';
          $('#canvas_cpmk_view').show();
          $('#cpmk_official_source_banner').hide();
          $('#cpmk_matrix_wrapper').hide();
          $('#cpmk_validation_box').hide();
          $('#cpmk_cards_container').html('<div style="padding:20px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; text-align:center; color:#b45309; font-size:12.5px;"><i class="fa fa-lock" style="font-size:20px; margin-bottom:8px; display:block;"></i><strong>Studio CPMK Terkunci</strong><br>' + escapeHtml(res.msg || 'Anda wajib menyetujui pemetaan CPL pada Studio CPL terlebih dahulu.') + '<br><br><button type="button" class="btn btn-warning btn-sm" onclick="switchTab(\'studio_cpl\')" style="font-weight:700; border-radius:6px;"><i class="fa fa-arrow-left"></i> Buka Studio CPL</button></div>');
          $('#btn_tambah_cpmk, #btn_susun_ulang_cpmk, #btn_simpan_draft_cpmk, #btn_setujui_cpmk, #btn_revisi_cpmk').hide();
          return;
        }

        if (res.status) {
          studioCpmkData = res;
          activeCpmkList = res.cpmk_items || [];
          activeCpmkStatus = res.cpmk_status || 'DRAFT';
          if (res.id_rps) $('#id_rps_loaded').val(res.id_rps);

          renderCpmkStudio(res);
          if (typeof callback === 'function') callback(res);
        } else {
          $('#cpmk_cards_container').html('<div style="padding:15px; color:#dc2626; font-size:12px; text-align:center;"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(res.msg || 'Gagal memuat data CPMK') + '</div>');
        }
      },
      error: function(xhr, status, error) {
        $('#cpmk_cards_container').html('<div style="padding:15px; color:#dc2626; font-size:12px; text-align:center;"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan jaringan saat memuat Studio CPMK.</div>');
      }
    });
  }

  /**
   * Render seluruh komponen Studio CPMK
   */
  function renderCpmkStudio(res) {
    if (!res) res = studioCpmkData;
    if (!res) return;

    var isApproved = (activeCpmkStatus === 'APPROVED');

    // 1. Status Badge
    var $statusBadge = $('#cpmk_global_status_badge');
    if (isApproved) {
      $statusBadge.text('APPROVED').css({ background:'#dcfce7', color:'#15803d', borderColor:'#bbf7d0' });
    } else {
      $statusBadge.text('DRAFT').css({ background:'#fef3c7', color:'#b45309', borderColor:'#fde68a' });
    }

    // 2. Official CPMK Source Banner
    var $offBanner = $('#cpmk_official_source_banner');
    if (res.official_cpmk_found && res.official_cpmk_meta) {
      var meta = res.official_cpmk_meta;
      var bannerHtml = '<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 16px; color:#166534;">'
                     + '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px; margin-bottom:6px;">'
                     + '  <div style="font-weight:700; font-size:13px; display:flex; align-items:center; gap:6px;">'
                     + '    <span class="badge" style="background:#15803d; color:#ffffff; font-size:11px; padding:3px 8px;"><i class="fa fa-check-circle"></i> FAKTUAL KURIKULUM</span>'
                     + '    <span>CPMK Resmi Ditemukan di Dokumen Kurikulum</span>'
                     + '  </div>'
                     + '  <span style="font-size:11px; color:#15803d; font-weight:600;"><i class="fa fa-bookmark"></i> ' + escapeHtml(meta.source_section || 'Bagian Kurikulum') + '</span>'
                     + '</div>'
                     + '<div style="font-size:12px; color:#1e293b; background:#ffffff; border:1px solid #dcfce7; border-radius:6px; padding:8px 12px; font-style:italic; margin-bottom:6px;">'
                     + '  &ldquo;' + escapeHtml(meta.source_evidence || '') + '&rdquo;'
                     + '</div>'
                     + '<div style="font-size:11.5px; color:#15803d;">'
                     + '  <i class="fa fa-info-circle"></i> Naskah CPMK resmi diimpor langsung sebagai draf awal. AI tidak akan menimpa naskah resmi ini secara otomatis.'
                     + '</div>'
                     + '</div>';
      $offBanner.html(bannerHtml).show();
    } else {
      var bannerHtml = '<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; color:#1e40af; font-size:12px;">'
                     + '<i class="fa fa-info-circle" style="color:#2563eb; margin-right:4px;"></i>'
                     + '<strong>Belum ada CPMK resmi pada dokumen kurikulum untuk mata kuliah ini.</strong> '
                     + 'CPMK disusun berbasis CPL Primary &amp; Supporting yang telah disetujui melalui draf usulan AI atau input manual.'
                     + '</div>';
      $offBanner.html(bannerHtml).show();
    }

    // 3. Approved CPL Grounding Summary Header
    var primaryPills = '';
    (res.primary_cpls || []).forEach(function(pc) {
      primaryPills += '<span class="badge" style="background:#0f766e; color:#ffffff; font-size:11px; font-weight:700; margin-right:4px;" title="' + escapeHtml(pc.desc || '') + '">' + escapeHtml(pc.code) + '</span>';
    });
    if (!primaryPills) primaryPills = '<span style="color:#94a3b8; font-size:11px; font-style:italic;">Tidak ada</span>';
    $('#cpmk_primary_cpl_badges').html(primaryPills);

    var supportingPills = '';
    (res.supporting_cpls || []).forEach(function(sc) {
      supportingPills += '<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:11px; font-weight:700; margin-right:4px;" title="' + escapeHtml(sc.desc || '') + '">' + escapeHtml(sc.code) + '</span>';
    });
    if (!supportingPills) supportingPills = '<span style="color:#94a3b8; font-size:11px; font-style:italic;">Tidak ada</span>';
    $('#cpmk_supporting_cpl_badges').html(supportingPills);

    $('#cpmk_cpl_summary_counts').text((res.primary_cpls || []).length + ' Primary, ' + (res.supporting_cpls || []).length + ' Supporting');

    // 4. Render Coverage Matrix
    renderCoverageMatrix(res.coverage_matrix, res.primary_cpls, res.supporting_cpls, activeCpmkList);

    // 5. Render CPMK Cards
    renderCpmkCards();

    // 6. Action Toolbar & Notice
    if (isApproved) {
      $('#btn_simpan_draft_cpmk, #btn_tambah_cpmk, #btn_susun_ulang_cpmk').hide();
      $('#btn_setujui_cpmk').hide();
      $('#btn_revisi_cpmk').show();
      $('#cpmk_approved_notice').show();
    } else {
      $('#btn_simpan_draft_cpmk, #btn_tambah_cpmk, #btn_susun_ulang_cpmk').show();
      $('#btn_setujui_cpmk').show().html('<i class="fa fa-check-circle"></i> Setujui CPMK (APPROVED)').prop('disabled', false);
      $('#btn_revisi_cpmk').hide();
      $('#cpmk_approved_notice').hide();
    }
  }

  /**
   * Render Coverage Matrix Table
   */
  function renderCoverageMatrix(coverageData, primaryCpls, supportingCpls, cpmkItems) {
    if (!primaryCpls) primaryCpls = (studioCpmkData ? studioCpmkData.primary_cpls : []) || [];
    if (!supportingCpls) supportingCpls = (studioCpmkData ? studioCpmkData.supporting_cpls : []) || [];
    if (!cpmkItems) cpmkItems = activeCpmkList || [];

    var cpmkCount = cpmkItems.length;

    // Build Table Headers
    var thHtml = '<th style="width:70px; text-align:center;">Kode CPL</th>'
               + '<th style="width:90px; text-align:center;">Peran</th>'
               + '<th>Deskripsi CPL</th>';
    if (cpmkCount === 0) {
      thHtml += '<th style="text-align:center; min-width:140px;">Pemetaan CPMK</th>';
    } else {
      for (var i = 0; i < cpmkCount; i++) {
        var cCode = cpmkItems[i].code || cpmkItems[i].kode || ('CPMK-' + (i + 1));
        thHtml += '<th style="text-align:center; min-width:65px;" title="' + escapeHtml(cpmkItems[i].rumusan || cpmkItems[i].teks || '') + '">' + escapeHtml(cCode) + '</th>';
      }
    }
    thHtml += '<th style="width:90px; text-align:center;">Status</th>';
    $('#table_cpmk_coverage_matrix thead tr').html(thHtml);

    // Build Table Body
    var tbodyHtml = '';
    var allCpls = [];
    primaryCpls.forEach(function(p) { allCpls.push({ code: p.code, desc: p.desc, role: 'PRIMARY' }); });
    supportingCpls.forEach(function(s) { allCpls.push({ code: s.code, desc: s.desc, role: 'SUPPORTING' }); });

    var totalPrimaryCovered = 0;

    allCpls.forEach(function(cpl) {
      var isPrimary = (cpl.role === 'PRIMARY');
      var rowCovered = false;

      var cellsHtml = '';
      if (cpmkCount === 0) {
        cellsHtml += '<td style="text-align:center; color:#94a3b8; font-style:italic; font-size:11px;">Belum dirumuskan</td>';
      } else {
        for (var j = 0; j < cpmkCount; j++) {
          var item = cpmkItems[j];
          var pList = item.primary_cpl || item.cpl_terkait || [];
          if (typeof pList === 'string') pList = pList.split(',').map(function(s){return s.trim();});
          var sList = item.supporting_cpl || [];
          if (typeof sList === 'string') sList = sList.split(',').map(function(s){return s.trim();});

          var hasPrimary = (pList.indexOf(cpl.code) !== -1);
          var hasSupport = (sList.indexOf(cpl.code) !== -1);

          if (hasPrimary) {
            cellsHtml += '<td style="text-align:center; background:#ecfdf5;"><span class="badge" style="background:#15803d; color:#ffffff; font-size:10.5px;" title="Pendorong Utama">&#10003; Primary</span></td>';
            rowCovered = true;
          } else if (hasSupport) {
            cellsHtml += '<td style="text-align:center; background:#f0f9ff;"><span class="badge" style="background:#0284c7; color:#ffffff; font-size:10.5px;" title="Kontekstual">&#9675; Support</span></td>';
          } else {
            cellsHtml += '<td style="text-align:center; color:#cbd5e1;">&mdash;</td>';
          }
        }
      }

      var roleBadge = isPrimary 
        ? '<span class="badge" style="background:#0f766e; color:#ffffff; font-size:10px;">★ PRIMARY</span>'
        : '<span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:10px;">SUPPORTING</span>';

      var statusBadge = '';
      if (isPrimary) {
        if (cpmkCount === 0) {
          statusBadge = '<span class="badge" style="background:#fef3c7; color:#b45309; font-size:10.5px; font-weight:700;">Menunggu CPMK</span>';
        } else if (rowCovered) {
          totalPrimaryCovered++;
          statusBadge = '<span class="badge" style="background:#dcfce7; color:#15803d; font-size:10.5px; font-weight:700;"><i class="fa fa-check"></i> Tercover</span>';
        } else {
          statusBadge = '<span class="badge" style="background:#fee2e2; color:#b91c1c; font-size:10.5px; font-weight:700;"><i class="fa fa-warning"></i> Belum Ada</span>';
        }
      } else {
        statusBadge = '<span class="badge" style="background:#f1f5f9; color:#64748b; font-size:10px;">Kontekstual</span>';
      }

      tbodyHtml += '<tr style="' + (!rowCovered && isPrimary && cpmkCount > 0 ? 'background:#fff5f5;' : '') + '">'
                 + '<td style="text-align:center; font-weight:700; color:#0f172a;">' + escapeHtml(cpl.code) + '</td>'
                 + '<td style="text-align:center;">' + roleBadge + '</td>'
                 + '<td style="font-size:11.5px; color:#334155; line-height:1.4;">' + escapeHtml(cpl.desc || '') + '</td>'
                 + cellsHtml
                 + '<td style="text-align:center;">' + statusBadge + '</td>'
                 + '</tr>';
    });

    if (allCpls.length === 0) {
      tbodyHtml = '<tr><td colspan="' + (4 + (cpmkCount || 1)) + '" style="text-align:center; padding:15px; color:#94a3b8;">Belum ada data CPL yang disetujui.</td></tr>';
    }

    $('#tbody_cpmk_coverage_matrix').html(tbodyHtml);

    // Matrix status badge
    if (cpmkCount === 0) {
      $('#matrix_coverage_status_badge').html('<span class="badge" style="background:#fef3c7; color:#b45309;"><i class="fa fa-info-circle"></i> Menunggu Rumusan CPMK</span>');
      $('#cpmk_validation_box').html('<i class="fa fa-info-circle" style="margin-right:4px;"></i><strong>Studio CPMK Siap:</strong> Belum ada rumusan CPMK. Silakan klik <strong>"Usulan AI (Fallback)"</strong> untuk menyusun draf otomatis atau <strong>"Tambah CPMK"</strong> untuk input manual.').show();
    } else {
      var allPrimaryCovered = (primaryCpls.length > 0 && totalPrimaryCovered === primaryCpls.length);
      if (allPrimaryCovered) {
        $('#matrix_coverage_status_badge').html('<span class="badge" style="background:#dcfce7; color:#15803d;"><i class="fa fa-check-circle"></i> 100% Primary Tercover (' + totalPrimaryCovered + '/' + primaryCpls.length + ')</span>');
        $('#cpmk_validation_box').hide();
      } else if (primaryCpls.length > 0) {
        var uncoveredCount = primaryCpls.length - totalPrimaryCovered;
        $('#matrix_coverage_status_badge').html('<span class="badge" style="background:#fee2e2; color:#b91c1c;"><i class="fa fa-warning"></i> ' + uncoveredCount + ' Primary Belum Tercover</span>');
        $('#cpmk_validation_box').html('<i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i><strong>Peringatan Cakupan CPL:</strong> Terdapat <strong>' + uncoveredCount + ' Primary CPL</strong> yang belum dipetakan ke butir CPMK manapun. Semua Primary CPL wajib dipetakan sebelum CPMK dapat disetujui.').show();
      }
    }
  }

  /**
   * Render CPMK Cards List
   */
  function renderCpmkCards() {
    var $container = $('#cpmk_cards_container');
    if (!$container.length) return;

    if (!activeCpmkList || activeCpmkList.length === 0) {
      $container.html('<div style="text-align:center; padding:35px; color:#94a3b8; background:#ffffff; border-radius:8px; border:1px dashed #cbd5e1;">'
                    + '<i class="fa fa-cubes" style="font-size:28px; margin-bottom:10px; display:block; color:#cbd5e1;"></i>'
                    + 'Belum ada rumusan CPMK.<br><span style="font-size:11.5px; color:#64748b;">Klik <strong>"Tambah CPMK"</strong> untuk menambah manual atau <strong>"Usulan AI (Fallback)"</strong> untuk menyusun draf otomatis.</span>'
                    + '</div>');
      return;
    }

    var isAllApproved = (activeCpmkStatus === 'APPROVED');
    var html = '';

    activeCpmkList.forEach(function(item, idx) {
      var cpmkId = item.id || ('cpmk_' + (idx + 1));
      var kode = item.code || item.kode || ('CPMK-' + (idx + 1));
      var rumusan = item.rumusan || item.teks || '';
      var bloom = String(item.level_bloom || item.bloom || 'C4').toUpperCase();
      var provenance = item.provenance || 'AI_PROPOSAL';

      var pList = item.primary_cpl || item.cpl_terkait || [];
      if (typeof pList === 'string') pList = pList.split(',').map(function(s){return s.trim();}).filter(Boolean);
      var sList = item.supporting_cpl || [];
      if (typeof sList === 'string') sList = sList.split(',').map(function(s){return s.trim();}).filter(Boolean);

      // Provenance Badge
      var provBadge = '';
      if (provenance === 'APPROVED' || isAllApproved) {
        provBadge = '<span class="cpmk-prov-badge prov-approved" style="background:#15803d; color:#ffffff;"><i class="fa fa-check"></i> APPROVED</span>';
      } else if (provenance === 'FACTUAL') {
        provBadge = '<span class="cpmk-prov-badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px;"><i class="fa fa-shield"></i> Faktual Kurikulum</span>';
      } else if (provenance === 'MANUAL_DERIVED_FROM_OFFICIAL') {
        provBadge = '<span class="cpmk-prov-badge" style="background:#e0e7ff; color:#3730a3; border:1px solid #c7d2fe; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px;"><i class="fa fa-pencil"></i> Manual (Turunan Resmi)</span>';
      } else if (provenance === 'MANUAL') {
        provBadge = '<span class="cpmk-prov-badge prov-manual" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;"><i class="fa fa-pencil"></i> Manual</span>';
      } else {
        provBadge = '<span class="cpmk-prov-badge prov-proposal" style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff;"><i class="fa fa-magic"></i> AI Proposal</span>';
      }

      // Bloom Dropdown (C2-C6 strictly, no C1)
      var bloomBadge = '';
      if (isAllApproved) {
        bloomBadge = '<span class="cpmk-bloom-badge" title="Taksonomi Bloom" style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff; font-weight:700;"><i class="fa fa-graduation-cap"></i> ' + escapeHtml(bloom) + '</span>';
      } else {
        var bloomLevels = ['C2', 'C3', 'C4', 'C5', 'C6'];
        bloomBadge = '<div style="display:inline-flex; align-items:center; gap:4px;" title="Ubah Level Taksonomi Bloom">'
                   + '<i class="fa fa-graduation-cap" style="color:#7e22ce; font-size:12px;"></i>'
                   + '<select class="cpmk-bloom-select" onchange="onCpmkBloomChange(' + idx + ', this.value)" style="border-radius:4px; border:1px solid #cbd5e1; font-size:11.5px; font-weight:700; color:#7e22ce; padding:2px 4px;">';
        bloomLevels.forEach(function(lvl) {
          bloomBadge += '<option value="' + lvl + '"' + (bloom === lvl ? ' selected' : '') + '>' + lvl + '</option>';
        });
        bloomBadge += '</select></div>';
      }

      // Primary CPL Pills
      var primaryPills = '';
      if (pList.length > 0) {
        pList.forEach(function(cpl) {
          if (cpl) {
            var removeBtn = !isAllApproved ? '<span class="cpmk-cpl-remove" onclick="removePrimaryCplFromCpmk(' + idx + ', \'' + escapeHtml(cpl) + '\')" title="Hapus CPL ini">&times;</span>' : '';
            primaryPills += '<span class="cpmk-cpl-pill" style="background:#0f766e; color:#ffffff; font-weight:700;" title="Primary CPL"><i class="fa fa-tag" style="font-size:9px;"></i> ' + escapeHtml(cpl) + removeBtn + '</span>';
          }
        });
      } else {
        primaryPills = '<span style="font-size:11px; color:#ef4444; font-style:italic;"><i class="fa fa-warning"></i> Belum ada Primary CPL</span>';
      }

      // Supporting CPL Pills
      var supportingPills = '';
      if (sList.length > 0) {
        sList.forEach(function(cpl) {
          if (cpl) {
            var removeBtn = !isAllApproved ? '<span class="cpmk-cpl-remove" onclick="removeSupportingCplFromCpmk(' + idx + ', \'' + escapeHtml(cpl) + '\')" title="Hapus CPL ini">&times;</span>' : '';
            supportingPills += '<span class="cpmk-cpl-pill" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;" title="Supporting CPL"><i class="fa fa-tag" style="font-size:9px;"></i> ' + escapeHtml(cpl) + removeBtn + '</span>';
          }
        });
      }

      var editCplBtn = '';
      if (!isAllApproved) {
        editCplBtn = '<button type="button" class="btn btn-default btn-xs" onclick="bukaModalPilihCplCpmk(' + idx + ')" style="border-radius:4px; font-size:10.5px; padding:2px 8px; font-weight:600; color:#0f766e; border-color:#99f6e4; background:#f0fdfa; margin-left:6px;"><i class="fa fa-pencil-square-o"></i> Atur Pemetaan CPL</button>';
      }

      var isFirst = (idx === 0);
      var isLast = (idx === activeCpmkList.length - 1);
      var textareaAttr = isAllApproved ? 'readonly style="background:#f8fafc; cursor:default; border-color:#e2e8f0; color:#334155;"' : '';

      var actionButtons = '';
      if (!isAllApproved) {
        actionButtons = '<div class="cpmk-card-actions" style="margin-top:10px; display:flex; justify-content:space-between; align-items:center;">'
                      + '<div style="display:flex; gap:4px;">'
                      + '<button type="button" class="btn btn-default btn-xs cpmk-action-btn" ' + (isFirst ? 'disabled style="opacity:0.4;"' : '') + ' onclick="moveCpmkUp(' + idx + ')" title="Pindahkan ke atas"><i class="fa fa-arrow-up"></i> Naik</button>'
                      + '<button type="button" class="btn btn-default btn-xs cpmk-action-btn" ' + (isLast ? 'disabled style="opacity:0.4;"' : '') + ' onclick="moveCpmkDown(' + idx + ')" title="Pindahkan ke bawah"><i class="fa fa-arrow-down"></i> Turun</button>'
                      + '</div>'
                      + '<div style="display:flex; gap:4px;">'
                      + '<button type="button" class="btn btn-default btn-xs cpmk-action-btn" onclick="bukaModalRepromptCpmk(' + idx + ')" title="Minta AI merevisi atau mempertajam rumusan CPMK ini"><i class="fa fa-magic text-primary"></i> Perbaiki AI</button>'
                      + '<button type="button" class="btn btn-default btn-xs cpmk-action-btn text-danger" onclick="deleteCpmk(' + idx + ')" title="Hapus CPMK"><i class="fa fa-trash"></i> Hapus</button>'
                      + '</div>'
                      + '</div>';
      }

      html += '<div class="cpmk-card" id="card_cpmk_' + idx + '" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">'
            + '<div class="cpmk-card-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">'
            + '  <div class="cpmk-card-title" style="display:flex; align-items:center; gap:8px;">'
            + '    <span style="font-weight:800; font-size:13.5px; color:#0f172a;"><i class="fa fa-bookmark" style="color:#0f766e; margin-right:4px;"></i>' + escapeHtml(kode) + '</span>'
            + '    ' + provBadge
            + '    ' + bloomBadge
            + '  </div>'
            + '  <span style="font-size:11px; color:#94a3b8; font-weight:600;">#' + (idx + 1) + '</span>'
            + '</div>'
            + '<div class="cpmk-card-body">'
            + '  <textarea class="cpmk-textarea form-control" rows="3" ' + textareaAttr + ' placeholder="Ketik atau sesuaikan rumusan kalimat CPMK..." oninput="onCpmkTextChange(' + idx + ', this.value);" style="font-size:12.5px; line-height:1.45; border-radius:6px;">' + escapeHtml(rumusan) + '</textarea>'
            + '</div>'
            + '<div class="cpmk-cpl-section" style="margin-top:8px; padding-top:8px; border-top:1px dashed #f1f5f9; display:flex; align-items:center; flex-wrap:wrap; gap:6px;">'
            + '  <span style="font-size:11.5px; font-weight:700; color:#0f766e;"><i class="fa fa-crosshairs"></i> Primary:</span>'
            + '  <div style="display:inline-flex; align-items:center; flex-wrap:wrap; gap:4px;">' + primaryPills + '</div>'
            + (supportingPills ? '  <span style="font-size:11.5px; font-weight:700; color:#0369a1; margin-left:6px;"><i class="fa fa-info-circle"></i> Support:</span><div style="display:inline-flex; align-items:center; flex-wrap:wrap; gap:4px;">' + supportingPills + '</div>' : '')
            + '  ' + editCplBtn
            + '</div>'
            + actionButtons
            + '</div>';
    });

    $container.html(html);

    if (typeof updateGenerateBtnState === 'function') {
      updateGenerateBtnState(activeMode);
    }
  }

  function onCpmkTextChange(index, val) {
    if (!activeCpmkList[index]) return;
    activeCpmkList[index].rumusan = val;
    activeCpmkList[index].teks = val;

    if (activeCpmkList[index].provenance !== 'APPROVED') {
      if (activeCpmkList[index].provenance === 'FACTUAL') {
        activeCpmkList[index].provenance = 'MANUAL_DERIVED_FROM_OFFICIAL';
        activeCpmkList[index].is_edited = true;
      } else {
        activeCpmkList[index].provenance = 'MANUAL';
      }
      renderCpmkCards();
    }
  }

  function onCpmkBloomChange(index, val) {
    if (!activeCpmkList || !activeCpmkList[index]) return;
    activeCpmkList[index].level_bloom = val;

    if (activeCpmkList[index].provenance !== 'APPROVED') {
      if (activeCpmkList[index].provenance === 'FACTUAL') {
        activeCpmkList[index].provenance = 'MANUAL_DERIVED_FROM_OFFICIAL';
        activeCpmkList[index].is_edited = true;
      } else {
        activeCpmkList[index].provenance = 'MANUAL';
      }
      renderCpmkCards();
    }
  }

  function removePrimaryCplFromCpmk(index, cplCode) {
    if (!activeCpmkList || !activeCpmkList[index]) return;
    var list = activeCpmkList[index].primary_cpl || activeCpmkList[index].cpl_terkait || [];
    if (typeof list === 'string') list = list.split(',').map(function(s){return s.trim();});
    
    activeCpmkList[index].primary_cpl = list.filter(function(c) { return String(c).trim() !== String(cplCode).trim(); });
    activeCpmkList[index].cpl_terkait = activeCpmkList[index].primary_cpl;

    if (activeCpmkList[index].provenance !== 'APPROVED') {
      activeCpmkList[index].provenance = (activeCpmkList[index].provenance === 'FACTUAL') ? 'MANUAL_DERIVED_FROM_OFFICIAL' : 'MANUAL';
    }
    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();
  }

  function removeSupportingCplFromCpmk(index, cplCode) {
    if (!activeCpmkList || !activeCpmkList[index]) return;
    var list = activeCpmkList[index].supporting_cpl || [];
    if (typeof list === 'string') list = list.split(',').map(function(s){return s.trim();});
    
    activeCpmkList[index].supporting_cpl = list.filter(function(c) { return String(c).trim() !== String(cplCode).trim(); });

    if (activeCpmkList[index].provenance !== 'APPROVED') {
      activeCpmkList[index].provenance = (activeCpmkList[index].provenance === 'FACTUAL') ? 'MANUAL_DERIVED_FROM_OFFICIAL' : 'MANUAL';
    }
    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();
  }

  function moveCpmkUp(index) {
    if (index <= 0) return;
    var temp = activeCpmkList[index];
    activeCpmkList[index] = activeCpmkList[index - 1];
    activeCpmkList[index - 1] = temp;
    reindexCpmkCodes();
    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();
  }

  function moveCpmkDown(index) {
    if (index >= activeCpmkList.length - 1) return;
    var temp = activeCpmkList[index];
    activeCpmkList[index] = activeCpmkList[index + 1];
    activeCpmkList[index + 1] = temp;
    reindexCpmkCodes();
    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();
  }

  function reindexCpmkCodes() {
    activeCpmkList.forEach(function(item, i) {
      item.code = 'CPMK-' + (i + 1);
      item.kode = 'CPMK-' + (i + 1);
    });
  }

  function deleteCpmk(index) {
    if (!activeCpmkList[index]) return;
    var kode = activeCpmkList[index].code || activeCpmkList[index].kode || ('CPMK-' + (index + 1));
    if (confirm('Apakah Anda yakin ingin menghapus ' + kode + '?')) {
      activeCpmkList.splice(index, 1);
      reindexCpmkCodes();
      renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
      renderCpmkCards();
    }
  }

  function tambahCpmkManual() {
    if (!activeCpmkList) activeCpmkList = [];
    var nextIdx = activeCpmkList.length + 1;

    var defaultPrimary = [];
    if (studioCpmkData && studioCpmkData.primary_cpls && studioCpmkData.primary_cpls.length > 0) {
      defaultPrimary.push(studioCpmkData.primary_cpls[0].code);
    }

    activeCpmkList.push({
      id: 'cpmk_' + nextIdx,
      code: 'CPMK-' + nextIdx,
      kode: 'CPMK-' + nextIdx,
      rumusan: '',
      teks: '',
      primary_cpl: defaultPrimary,
      cpl_terkait: defaultPrimary,
      supporting_cpl: [],
      bahan_kajian: [],
      level_bloom: 'C4',
      provenance: 'MANUAL',
      source_type: 'MANUAL'
    });

    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();

    setTimeout(function() {
      var $ta = $('#card_cpmk_' + (activeCpmkList.length - 1) + ' .cpmk-textarea');
      if ($ta.length) $ta.focus();
    }, 100);
  }

  function simpanDraftCpmk() {
    if (!activeCpmkList || activeCpmkList.length === 0) {
      alert('Belum ada CPMK untuk disimpan.');
      return;
    }

    var namaMk = $.trim($('#mk_nama').val());
    if (!namaMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    var $btn = $('#btn_simpan_draft_cpmk');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_save_cpmk_studio"); ?>',
      type: 'POST',
      data: {
        id_rps: $('#id_rps_loaded').val(),
        kode_mk: $('#mk_kode').val(),
        mata_kuliah: namaMk,
        sks: $('#mk_sks').val(),
        semester: $('#mk_semester').val(),
        prodi: $('#mk_prodi').val(),
        dosen: $('#mk_dosen').val(),
        cpmk_items: JSON.stringify(activeCpmkList),
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      complete: function() {
        $btn.prop('disabled', false).html(origHtml);
      },
      success: function(res) {
        if (res.status) {
          if (res.id_rps) $('#id_rps_loaded').val(res.id_rps);
          activeCpmkStatus = 'DRAFT';
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Draf CPMK Tersimpan',
              text: res.msg || 'Draf CPMK berhasil disimpan ke database.',
              timer: 2000,
              showConfirmButton: false
            });
          } else {
            alert(res.msg || 'Draf CPMK berhasil disimpan.');
          }
          loadCpmkStudioData();
        } else {
          alert('Gagal menyimpan draf CPMK: ' + (res.msg || 'Unknown error'));
        }
      },
      error: function(xhr, status, error) {
        alert('Kesalahan jaringan: ' + (xhr.responseText || error));
      }
    });
  }

  function setujuiCpmk() {
    if (!activeCpmkList || activeCpmkList.length === 0) {
      alert('Belum ada rumusan CPMK yang dapat disetujui.');
      return;
    }

    var namaMk = $.trim($('#mk_nama').val());
    if (!namaMk) {
      alert('Silakan pilih Mata Kuliah terlebih dahulu.');
      return;
    }

    var doApprove = function() {
      var $btn = $('#btn_setujui_cpmk');
      var origHtml = $btn.html();
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memvalidasi &amp; Menyetujui...');

      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_approve_cpmk_studio"); ?>',
        type: 'POST',
        data: {
          id_rps: $('#id_rps_loaded').val(),
          kode_mk: $('#mk_kode').val(),
          mata_kuliah: namaMk,
          sks: $('#mk_sks').val(),
          semester: $('#mk_semester').val(),
          prodi: $('#mk_prodi').val(),
          dosen: $('#mk_dosen').val(),
          cpmk_items: JSON.stringify(activeCpmkList),
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        complete: function() {
          $btn.prop('disabled', false).html(origHtml);
        },
        success: function(res) {
          if (res.status) {
            if (res.id_rps) $('#id_rps_loaded').val(res.id_rps);
            activeCpmkStatus = 'APPROVED';

            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'CPMK Disetujui (APPROVED)',
                html: '<strong>CPMK telah disetujui dan siap digunakan sebagai dasar penyusunan Sub-CPMK &amp; RPS.</strong><br><br><span style="font-size:12px; color:#475569;">Rumusan CPMK terkunci dan tersimpan di database.</span>',
                confirmButtonColor: '#0f766e',
                confirmButtonText: '<i class="fa fa-arrow-right"></i> Lanjut ke RPS Lengkap'
              }).then(function() {
                loadCpmkStudioData();
              });
            } else {
              alert('CPMK telah disetujui dan siap digunakan sebagai dasar penyusunan RPS.');
              loadCpmkStudioData();
            }
          } else {
            if (res.validation_failed && typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'error',
                title: 'Validasi Persetujuan CPMK Belum Terpenuhi',
                html: res.msg,
                confirmButtonColor: '#b91c1c'
              });
            } else {
              alert('Gagal menyetujui CPMK: ' + (res.msg || 'Unknown error'));
            }
          }
        },
        error: function(xhr, status, error) {
          alert('Kesalahan jaringan: ' + (xhr.responseText || error));
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Setujui Rumusan CPMK?',
        html: 'Anda akan menyetujui <strong>' + activeCpmkList.length + ' butir CPMK</strong> untuk mata kuliah <strong>' + escapeHtml(namaMk) + '</strong>.<br><br>Status CPMK akan ditandai sebagai <strong>APPROVED</strong>.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#15803d',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-check"></i> Ya, Setujui CPMK',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.value || result.isConfirmed) {
          doApprove();
        }
      });
    } else {
      if (confirm('Anda akan menyetujui ' + activeCpmkList.length + ' CPMK. Status CPMK akan ditandai sebagai APPROVED.')) {
        doApprove();
      }
    }
  }

  function revisiCpmk() {
    var doReopen = function() {
      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_revise_cpmk_studio"); ?>',
        type: 'POST',
        data: {
          id_rps: $('#id_rps_loaded').val(),
          kode_mk: $('#mk_kode').val(),
          nm_mk: $('#mk_nama').val(),
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
          if (res.status) {
            activeCpmkStatus = 'DRAFT';
            loadCpmkStudioData();
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'info',
                title: 'Status CPMK: DRAFT',
                text: res.msg || 'Studio CPMK dibuka kembali untuk pengeditan.',
                timer: 2000,
                showConfirmButton: false
              });
            }
          } else {
            alert('Gagal merevisi CPMK: ' + (res.msg || ''));
          }
        },
        error: function(xhr, status, error) {
          alert('Kesalahan jaringan: ' + (xhr.responseText || error));
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Buka Kembali untuk Revisi?',
        text: 'CPMK yang telah disetujui akan dibuka kembali untuk diedit (status menjadi DRAFT).',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-pencil"></i> Ya, Buka Revisi',
        cancelButtonText: 'Batal'
      }).then(function(result) {
        if (result.value || result.isConfirmed) {
          doReopen();
        }
      });
    } else {
      if (confirm('Buka kembali CPMK untuk revisi?')) {
        doReopen();
      }
    }
  }

  var targetRepromptIndex = -1;

  function bukaModalRepromptCpmk(index) {
    if (!activeCpmkList[index]) return;
    targetRepromptIndex = index;
    var item = activeCpmkList[index];

    $('#reprompt_cpmk_kode').text(item.code || item.kode || ('CPMK-' + (index + 1)));
    $('#reprompt_cpmk_current_text').text(item.rumusan || item.teks || '(Teks masih kosong)');
    $('#reprompt_cpmk_instruction').val('').focus();
    $('#modalRepromptCpmk').modal('show');
  }

  function kirimRepromptCpmk() {
    if (targetRepromptIndex < 0 || !activeCpmkList[targetRepromptIndex]) return;
    var prompt = $.trim($('#reprompt_cpmk_instruction').val());
    if (!prompt) {
      alert('Harap ketikkan instruksi atau arahan perbaikan untuk AI.');
      $('#reprompt_cpmk_instruction').focus();
      return;
    }

    var item = activeCpmkList[targetRepromptIndex];
    var $btn = $('#btn_execute_reprompt_cpmk');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memperbaiki...');

    var namaMk = $.trim($('#mk_nama').val());
    var postData = {
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
      mode: 'revisi_cpmk_item',
      mata_kuliah: namaMk,
      kode_mk: $('#mk_kode').val(),
      sks: $('#mk_sks').val(),
      semester: $('#mk_semester').val(),
      dosen: $('#mk_dosen').val(),
      prodi: $('#mk_prodi').val(),
      target_cpmk_json: JSON.stringify(item),
      catatan_khusus: prompt,
      teks_kurikulum: uploadedDocs.kurikulum
    };

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_process"); ?>',
      type: 'POST',
      data: postData,
      dataType: 'json',
      complete: function() {
        $btn.prop('disabled', false).html(origHtml);
      },
      success: function(res) {
        if (res.status && res.cpmk) {
          var updated = res.cpmk;
          activeCpmkList[targetRepromptIndex].rumusan = updated.rumusan || updated.teks || item.rumusan;
          activeCpmkList[targetRepromptIndex].teks = activeCpmkList[targetRepromptIndex].rumusan;
          if (updated.level_bloom) activeCpmkList[targetRepromptIndex].level_bloom = updated.level_bloom;
          if (updated.primary_cpl && updated.primary_cpl.length > 0) activeCpmkList[targetRepromptIndex].primary_cpl = updated.primary_cpl;
          if (updated.supporting_cpl) activeCpmkList[targetRepromptIndex].supporting_cpl = updated.supporting_cpl;
          activeCpmkList[targetRepromptIndex].provenance = 'AI_PROPOSAL';

          $('#modalRepromptCpmk').modal('hide');
          renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
          renderCpmkCards();

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'CPMK Telah Diperbarui',
              text: (item.code || item.kode) + ' berhasil disesuaikan dengan instruksi AI.',
              timer: 2000,
              showConfirmButton: false
            });
          }
        } else {
          alert('Gagal memperbaiki CPMK: ' + (res.msg || 'Format respon tidak sesuai.'));
        }
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan jaringan: ' + (xhr.responseText || error));
      }
    });
  }

  function bukaModalPilihCplCpmk(index) {
    if (!activeCpmkList || !activeCpmkList[index]) return;
    var item = activeCpmkList[index];
    $('#pilih_cpl_cpmk_target_index').val(index);
    var cpmkCode = item.code || item.kode || ('CPMK-' + (index + 1));
    $('#modal_pilih_cpl_cpmk_title').text('Atur Pemetaan CPL — ' + cpmkCode);
    $('#modal_pilih_cpl_cpmk_text_preview').text(item.rumusan || item.teks || '(Rumusan CPMK masih kosong)');

    var currentPrimaries = item.primary_cpl || item.cpl_terkait || [];
    if (typeof currentPrimaries === 'string') {
      currentPrimaries = currentPrimaries.split(',').map(function(s){ return s.trim(); });
    }
    var currentSupportings = item.supporting_cpl || [];
    if (typeof currentSupportings === 'string') {
      currentSupportings = currentSupportings.split(',').map(function(s){ return s.trim(); });
    }

    var allCpls = [];
    if (studioCpmkData && studioCpmkData.all_cpls && studioCpmkData.all_cpls.length > 0) {
      allCpls = studioCpmkData.all_cpls;
    } else if (studioCpmkData) {
      (studioCpmkData.primary_cpls || []).forEach(function(c){ allCpls.push(Object.assign({}, c, { cpl_type: 'PRIMARY' })); });
      (studioCpmkData.supporting_cpls || []).forEach(function(c){ allCpls.push(Object.assign({}, c, { cpl_type: 'SUPPORTING' })); });
    }

    var html = '';
    if (allCpls.length === 0) {
      html = '<div style="text-align:center; padding:20px; color:#94a3b8; font-size:12px;">Tidak ada CPL yang disetujui untuk mata kuliah ini.</div>';
    } else {
      allCpls.forEach(function(cpl, cIdx) {
        var code = cpl.code;
        var desc = cpl.deskripsi || cpl.desc || '';
        var isPrimaryChecked = currentPrimaries.some(function(p){ return String(p).trim() === String(code).trim(); });
        var isSupportingChecked = currentSupportings.some(function(s){ return String(s).trim() === String(code).trim(); });
        var isNoneChecked = (!isPrimaryChecked && !isSupportingChecked);

        var cplTypeBadge = (cpl.cpl_type === 'PRIMARY') 
          ? '<span class="badge" style="background:#0f766e; color:#fff; font-size:10px; font-weight:700;">Studio: PRIMARY</span>'
          : '<span class="badge" style="background:#0284c7; color:#fff; font-size:10px; font-weight:700;">Studio: SUPPORTING</span>';

        html += '<div class="cpl-cpmk-select-row" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:10px 12px; margin-bottom:8px;">'
              + '  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">'
              + '    <div style="display:flex; align-items:center; gap:8px;">'
              + '      <span style="font-weight:700; font-size:13px; color:#0f172a;">' + escapeHtml(code) + '</span>'
              + '      ' + cplTypeBadge
              + '    </div>'
              + '    <div class="btn-group btn-group-xs" data-toggle="buttons">'
              + '      <label class="btn btn-default btn-xs ' + (isPrimaryChecked ? 'active btn-success' : '') + '" style="font-size:10.5px; font-weight:600; padding:2px 8px;">'
              + '        <input type="radio" name="cpl_map_mode_' + cIdx + '" data-cpl-code="' + escapeHtml(code) + '" value="PRIMARY" ' + (isPrimaryChecked ? 'checked' : '') + '> Primary'
              + '      </label>'
              + '      <label class="btn btn-default btn-xs ' + (isSupportingChecked ? 'active btn-info' : '') + '" style="font-size:10.5px; font-weight:600; padding:2px 8px;">'
              + '        <input type="radio" name="cpl_map_mode_' + cIdx + '" data-cpl-code="' + escapeHtml(code) + '" value="SUPPORTING" ' + (isSupportingChecked ? 'checked' : '') + '> Supporting'
              + '      </label>'
              + '      <label class="btn btn-default btn-xs ' + (isNoneChecked ? 'active btn-default' : '') + '" style="font-size:10.5px; font-weight:600; padding:2px 8px;">'
              + '        <input type="radio" name="cpl_map_mode_' + cIdx + '" data-cpl-code="' + escapeHtml(code) + '" value="NONE" ' + (isNoneChecked ? 'checked' : '') + '> Tidak Terkait'
              + '      </label>'
              + '    </div>'
              + '  </div>'
              + '  <div style="font-size:11.5px; color:#475569; line-height:1.4;">' + escapeHtml(desc) + '</div>'
              + '</div>';
      });
    }

    $('#pilih_cpl_cpmk_list').html(html);
    $('#modalPilihCplCpmk').modal('show');
  }

  function selectAllCplForCpmk(selectAll) {
    $('#pilih_cpl_cpmk_list .cpl-cpmk-select-row').each(function() {
      var $row = $(this);
      if (selectAll) {
        $row.find('input[value="PRIMARY"]').prop('checked', true);
        $row.find('label').removeClass('active btn-success btn-info btn-default');
        $row.find('label:has(input[value="PRIMARY"])').addClass('active btn-success');
      } else {
        $row.find('input[value="NONE"]').prop('checked', true);
        $row.find('label').removeClass('active btn-success btn-info btn-default');
        $row.find('label:has(input[value="NONE"])').addClass('active btn-default');
      }
    });
  }

  function simpanPilihanCplCpmk() {
    var rawIdx = $('#pilih_cpl_cpmk_target_index').val();
    if (rawIdx === '' || rawIdx === null) return;
    var idx = parseInt(rawIdx);
    if (!activeCpmkList || !activeCpmkList[idx]) return;

    var selectedPrimaries = [];
    var selectedSupportings = [];

    $('#pilih_cpl_cpmk_list .cpl-cpmk-select-row').each(function() {
      var $checked = $(this).find('input[type="radio"]:checked');
      if ($checked.length > 0) {
        var val = $checked.val();
        var code = $checked.data('cpl-code');
        if (code) {
          if (val === 'PRIMARY') {
            selectedPrimaries.push(code);
          } else if (val === 'SUPPORTING') {
            selectedSupportings.push(code);
          }
        }
      }
    });

    if (selectedPrimaries.length === 0) {
      if (!confirm('Peringatan: CPMK ini tidak memiliki Primary CPL yang dipilih. Setiap CPMK wajib memiliki minimal 1 Primary CPL sebelum dapat disetujui. Apakah Anda yakin ingin melanjutkan?')) {
        return;
      }
    }

    activeCpmkList[idx].primary_cpl = selectedPrimaries;
    activeCpmkList[idx].cpl_terkait = selectedPrimaries;
    activeCpmkList[idx].supporting_cpl = selectedSupportings;

    if (activeCpmkList[idx].provenance !== 'APPROVED') {
      activeCpmkList[idx].provenance = (activeCpmkList[idx].provenance === 'FACTUAL') ? 'MANUAL_DERIVED_FROM_OFFICIAL' : 'MANUAL';
    }

    $('#modalPilihCplCpmk').modal('hide');
    renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
    renderCpmkCards();
  }

  // Fallback kompatibilitas jika ada pemanggilan pilihMkDiampu lama
  function pilihMkDiampu(sel) {
    var val = $(sel).val();
    if (!val) {
      pilihMkObject(null);
      return;
    }
    var selectedOpt = $(sel).find(':selected');
    pilihMkObject({
      nm_mk: val,
      kode_mk: selectedOpt.data('kode') || '',
      sks: parseInt(selectedOpt.data('sks')) || 0,
      nm_prodi: selectedOpt.data('prodi') || ''
    });
  }

  /**
   * Reset Tampilan UI Pustaka Kurikulum
   */
  function resetPustakaUi() {
    uploadedDocs.kurikulum = '';
    $('#select_pustaka_kurikulum').val('');
    $('#info_pustaka_aktif').slideUp(150);
    $('#badge_status_pustaka').text('Belum Terpilih').css({ background:'#f1f5f9', color:'#64748b' });
    $('#box_kurikulum').removeClass('has-doc');
    $('#desc_kurikulum').text('Pilih PDF / DOCX (Otomatis disimpan ke pustaka)').css('color', '#64748b');
  }

  /**
   * Cek ketersediaan pustaka kurikulum prodi via AJAX
   */
  function checkPustakaProdi(namaProdi) {
    $('#badge_status_pustaka').text('Mengecek...').css({ background:'#fef3c7', color:'#b45309' });

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_check_pustaka_prodi"); ?>',
      type: 'POST',
      data: {
        nama_prodi: namaProdi,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status && res.found) {
          // Pustaka ditemukan!
          uploadedDocs.kurikulum = res.content;
          $('#select_pustaka_kurikulum').val(res.pustaka.id);
          renderPustakaMeta(res.pustaka);

          $('#badge_status_pustaka').text('🟢 Pustaka Aktif').css({ background:'#dcfce7', color:'#15803d' });
          $('#box_kurikulum').addClass('has-doc');
          $('#desc_kurikulum').text('Pustaka aktif terpasang. Klik jika ingin mengganti/unggah berkas baru.').css('color', '#15803d');
        } else {
          // Pustaka belum ada untuk prodi ini
          resetPustakaUi();
          $('#badge_status_pustaka').text('⚪ Perlu Diunggah').css({ background:'#fef3c7', color:'#b45309' });
          $('#desc_kurikulum').text('Belum ada pustaka untuk ' + namaProdi + '. Silakan unggah berkas.').css('color', '#b45309');
        }
      },
      error: function() {
        $('#badge_status_pustaka').text('Gagal Cek').css({ background:'#fee2e2', color:'#991b1b' });
      }
    });
  }

  /**
   * Helper untuk render status metadata pustaka aktif (hanya judul & nama file dokumen)
   */
  function renderPustakaMeta(pustaka) {
    if (!pustaka) return;
    var docName = (pustaka.judul_dokumen || 'Dokumen Kurikulum') + (pustaka.file_name ? ' (' + pustaka.file_name + ')' : '');
    $('#pustaka_nama_doc').text(docName);
    $('#info_pustaka_aktif').slideDown(200);
  }

  /**
   * AJAX Rebuild Structured Curriculum Library
   */
  function rebuildStructuredCurriculum() {
    var selId = $('#select_pustaka_kurikulum').val();
    var prodi = $('#mk_prodi').val() || '';
    if (!selId && !prodi) {
      alert('Pilih pustaka kurikulum terlebih dahulu.');
      return;
    }

    $('#btn_rebuild_structured').text('Membangun...').css('pointer-events', 'none');
    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_rebuild_structured_curriculum"); ?>',
      type: 'POST',
      data: {
        id: selId,
        nama_prodi: prodi,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        $('#btn_rebuild_structured').text('Bangun Ulang').css('pointer-events', 'auto');
        if (res.status) {
          alert('✅ ' + res.msg + '\nOfficial CPL: ' + res.cpl_count + ' | BK: ' + res.bk_count + ' | MK: ' + res.mk_count);
          if (prodi) {
            checkPustakaProdi(prodi);
          } else if (selId) {
            pilihPustakaManual($('#select_pustaka_kurikulum')[0]);
          }
        } else {
          alert('❌ Gagal: ' + res.msg);
        }
      },
      error: function() {
        $('#btn_rebuild_structured').text('Bangun Ulang').css('pointer-events', 'auto');
        alert('Terjadi kesalahan koneksi saat membangun ulang data terstruktur.');
      }
    });
  }

  /**
   * Memilih pustaka secara manual dari dropdown (mendukung skenario lintas prodi)
   */
  function pilihPustakaManual(sel) {
    var id = $(sel).val();
    if (!id) {
      resetPustakaUi();
      return;
    }

    $('#badge_status_pustaka').text('Memuat...').css({ background:'#fef3c7', color:'#b45309' });

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_load_pustaka"); ?>',
      type: 'POST',
      data: {
        id: id,
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          uploadedDocs.kurikulum = res.content;
          renderPustakaMeta(res.pustaka);

          $('#badge_status_pustaka').text('🟢 Pustaka Terpilih').css({ background:'#dcfce7', color:'#15803d' });
          $('#box_kurikulum').addClass('has-doc');
          $('#desc_kurikulum').text('Pustaka manual terpasang. Klik jika ingin mengganti/unggah berkas baru.').css('color', '#15803d');
        } else {
          resetPustakaUi();
          $('#badge_status_pustaka').text('Gagal Memuat').css({ background:'#fee2e2', color:'#991b1b' });
        }
      },
      error: function() {
        resetPustakaUi();
        $('#badge_status_pustaka').text('Gagal Memuat').css({ background:'#fee2e2', color:'#991b1b' });
      }
    });
  }

  /**
   * File Upload Handler
   */
  function handleFileUpload(input, type) {
    var file = input.files[0];
    if (!file) return;

    var box = $('#box_' + type);
    var desc = $('#desc_' + type);
    box.css('opacity', '0.6');
    desc.text('Mengekstrak teks...').css('color', '#b45309');

    var formData = new FormData();
    formData.append('doc_file', file);
    formData.append('doc_type', type);
    formData.append('nama_prodi', $('#mk_prodi').val() || '');
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_upload_doc"); ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        box.css('opacity', '1');
        if (res.status) {
          box.addClass('has-doc');
          uploadedDocs[type] = res.text;
          var charsKb = Math.round(res.char_count / 1000);
          desc.text(file.name + ' (' + charsKb + 'k karakter)').css('color', '#15803d');

          // Khusus Kurikulum: update tampilan Pustaka
          if (type === 'kurikulum' && res.pustaka_info) {
            var pi = res.pustaka_info;
            $('#pustaka_nama_doc').text('Kurikulum & CPL ' + pi.nama_prodi + ' (' + pi.file_name + ')');
            $('#pustaka_meta').text('Diunggah: ' + pi.updated_at + ' oleh ' + pi.uploaded_by_nama + ' (' + charsKb + 'k karakter)');
            $('#info_pustaka_aktif').slideDown(200);
            $('#badge_status_pustaka').text('🟢 Tersimpan di Pustaka').css({ background:'#dcfce7', color:'#15803d' });

            // Tambahkan / pilih di dropdown pustaka jika belum ada
            if ($('#select_pustaka_kurikulum option[value="' + pi.id + '"]').length === 0) {
              var newOpt = $('<option>', {
                value: pi.id,
                text: '📚 ' + pi.nama_prodi + ' (' + pi.file_name + ')'
              });
              $('#select_pustaka_kurikulum').append(newOpt);
            }
            $('#select_pustaka_kurikulum').val(pi.id);
          }
        } else {
          box.removeClass('has-doc');
          uploadedDocs[type] = '';
          desc.text('Gagal: ' + res.msg).css('color', '#b91c1c');
        }
      },
      error: function(xhr, status, error) {
        box.css('opacity', '1').removeClass('has-doc');
        uploadedDocs[type] = '';
        desc.text('Gagal membaca dokumen').css('color', '#b91c1c');
      }
    });
  }

  /**
   * Upload & Parse File Spreadsheet Excel 16 Pertemuan (13 Kolom)
   */
  function handleExcelPertemuanUpload(input) {
    var file = input.files[0];
    if (!file) return;

    var box = $('#box_excel_pertemuan');
    var desc = $('#desc_excel_pertemuan');
    var title = $('#title_excel_pertemuan');
    var btnHapus = $('#btn_hapus_excel_pertemuan');
    var infoAktif = $('#info_excel_aktif');

    box.css('opacity', '0.6');
    desc.text('Mengekstrak 13 kolom pertemuan...').css('color', '#b45309');

    var formData = new FormData();
    formData.append('excel_file', file);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_upload_excel_pertemuan"); ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        box.css('opacity', '1');
        if (res.status) {
          box.addClass('has-doc').css({ background:'#f0fdf4', borderColor:'#86efac' });
          title.text('Excel 16 Pertemuan Terhubung').css('color', '#166534');
          desc.text(file.name + ' (' + res.total_pertemuan + ' pertemuan terbaca)').css('color', '#15803d');
          btnHapus.show();

          uploadedDocs.excel_pertemuan_md = res.markdown_table;
          uploadedDocs.excel_matrix_json = JSON.stringify(res.matrix_data);
          uploadedDocs.excel_filename = file.name;

          $('#info_excel_summary').text(res.total_pertemuan + ' Pertemuan Terverifikasi');
          infoAktif.slideDown(200);

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Excel 16 Pertemuan Terbaca',
              html: '<strong>' + res.total_pertemuan + ' pertemuan (13 kolom)</strong> berhasil diekstrak dari file <em>' + escapeHtml(file.name) + '</em>.<br><br><span style="font-size:12px; color:#166534;">Saat Anda klik Generate RPS OBE, AI tidak perlu membuat ulang 16 pertemuan dan langsung menyelaraskan capaian (CPL/CPMK/Blueprint Asesmen) dengan data Excel ini.</span>',
              timer: 3500,
              showConfirmButton: true,
              confirmButtonText: 'OK, Siap'
            });
          }
        } else {
          hapusExcelPertemuan(null);
          desc.text('Gagal: ' + (res.msg || 'Format Excel tidak valid')).css('color', '#b91c1c');
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Gagal Membaca Excel',
              text: res.msg || 'Terjadi kesalahan saat memproses file Excel.'
            });
          }
        }
      },
      error: function(xhr, status, error) {
        box.css('opacity', '1');
        hapusExcelPertemuan(null);
        desc.text('Gagal mengunggah file Excel.').css('color', '#b91c1c');
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan Jaringan',
            text: 'Gagal menghubungi server untuk membaca file Excel.'
          });
        }
      }
    });
  }

  /**
   * Reset / Hapus File Excel 16 Pertemuan
   */
  function hapusExcelPertemuan(e) {
    if (e) {
      e.stopPropagation();
      e.preventDefault();
    }
    $('#file_excel_pertemuan').val('');
    $('#box_excel_pertemuan').removeClass('has-doc').css({ background:'#f8fafc', borderColor:'#cbd5e1' });
    $('#title_excel_pertemuan').text('Upload File Excel 16 Pertemuan').css('color', '#334155');
    $('#desc_excel_pertemuan').text('Pilih file spreadsheet (.xlsx/.xls) jika sudah ada').css('color', '#64748b');
    $('#btn_hapus_excel_pertemuan').hide();
    $('#info_excel_aktif').slideUp(150);

    uploadedDocs.excel_pertemuan_md = '';
    uploadedDocs.excel_matrix_json = '';
    uploadedDocs.excel_filename = '';
  }

  /**
   * Progress Steps per Mode
   */
  var modeProgressSteps = {
    susun_cpmk: [
      'Membaca Dokumen Kurikulum, CPL Prodi, &amp; Profil Mata Kuliah...',
      'Menyelaraskan Pemetaan CPL ↔ MK dan Bahan Kajian...',
      'Merumuskan Draf CPMK berbasis Bloom C2–C6 Proporsional...',
      'Memvalidasi Struktur JSON CPMK &amp; Menyiapkan Card Editor...'
    ],
    susun_rps: [
      'Membaca Dokumen Kurikulum, CPL Prodi, &amp; Profil Mata Kuliah...',
      'Menyusun Capaian Pembelajaran Mata Kuliah (CPMK) Bloom C4–C6...',
      'Memetakan Matriks Evaluasi &amp; Distribusi 16 Pertemuan WBI...',
      'Memvalidasi Format Standar &amp; Menyiapkan Dokumen RPS Final...'
    ],
    kontrak_kuliah: [
      'Membaca data RPS &amp; Profil Mata Kuliah...',
      'Menyusun aturan perkuliahan, kehadiran, &amp; tata tertib...',
      'Merancang skema penilaian &amp; bobot evaluasi SPMI...',
      'Menyiapkan Formulir Kontrak Kuliah sesuai standar WBI...'
    ],
    rancangan_tugas: [
      'Membaca CPMK &amp; Sub-CPMK dari dokumen RPS...',
      'Merancang deskripsi tugas, obyek garapan, &amp; tujuan capaian...',
      'Menyusun kriteria &amp; rubrik penilaian analitik...',
      'Menyiapkan Rancangan Tugas Mahasiswa sesuai standar SPMI WBI...'
    ],
    roadmap_wbi: [
      'Membaca Kurikulum &amp; Profil Mata Kuliah dari dokumen acuan...',
      'Mengidentifikasi topik utama &amp; sub-topik setiap modul...',
      'Memetakan distribusi 16 pertemuan &amp; alokasi waktu T/P...',
      'Menyusun Roadmap Modul dalam format tabel WBI...'
    ],
    skenario_pembelajaran: [
      'Membaca Master RPS, Sub-CPMK, Bahan Kajian &amp; Bentuk Pembelajaran...',
      'Merancang sintaks strategi SCL (Pendahuluan, Inti/Kolaborasi, Penutup)...',
      'Menyusun rincian langkah operasional &amp; alokasi waktu per pertemuan...',
      'Menyiapkan draf Berita Acara Perkuliahan (BAP Faktual) 16 pertemuan...'
    ]
  };

  var progressTimer = null;

  function renderProgressSteps(mode) {
    var steps = modeProgressSteps[mode] || modeProgressSteps['susun_rps'];
    var html = '';
    steps.forEach(function(text, i) {
      var activeClass = (i === 0) ? ' active' : '';
      html += '<div class="rps-step-item' + activeClass + '" id="step_progress_' + (i + 1) + '">'
            + '<div class="rps-step-bullet"><i class="fa fa-circle"></i></div>'
            + '<div class="rps-step-text">' + (i + 1) + '. ' + text + '</div>'
            + '</div>';
    });
    $('#rps_progress_steps_container').html(html);
  }

  function startProgressStepper(mode) {
    renderProgressSteps(mode || activeMode || 'susun_rps');

    var stepIndex = 1;
    clearInterval(progressTimer);
    progressTimer = setInterval(function() {
      stepIndex++;
      if (stepIndex <= 4) {
        $('#step_progress_' + (stepIndex - 1)).removeClass('active').addClass('completed');
        $('#step_progress_' + stepIndex).addClass('active');
      }
    }, 2500);
  }

  function stopProgressStepper() {
    clearInterval(progressTimer);
    $('.rps-step-item').removeClass('active').addClass('completed');
  }

  /**
   * Helper untuk menggabungkan naskah lanjutan ke naskah sebelumnya secara mulus
   */
  function cleanAndMergeMarkdown(prev, next) {
    if (!prev) return next || '';
    if (!next) return prev || '';

    // 1. Bersihkan penanda warning
    var cleanedPrev = prev.replace(/>\s*\[!WARNING\][\s\S]*$/i, '').trim();
    var cleanedNext = next.replace(/>\s*\[!WARNING\][\s\S]*$/i, '').trim();

    // 2. Bersihkan markdown code fences dari next jika AI membungkusnya dalam ```
    cleanedNext = cleanedNext.replace(/^```(?:markdown|text)?\s*/i, '').replace(/\s*```$/i, '').trim();

    // 3. Bersihkan preamble / teks pengantar AI dari next (misal "Berikut lanjutan...")
    cleanedNext = cleanedNext.replace(/^(?:(?:\*{0,2}(?:Lanjutan|Berikut|Berikutnya|Sisa|Tabel)[^\n]*\*{0,2})\s*\n+)+/i, '').trim();

    // 4. Potong jika ada halusinasi bab lanjutan setelah Pertemuan 16 (Rancangan Tugas, Penutup, Lampiran)
    cleanedNext = cleanedNext.replace(/(?:\n+|\A)(?:[#*]{1,4}\s*)?(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z\.\-_]+\s*)?(?:RANCANGAN\s*TUGAS|TUGAS\s*MAHASISWA|LEMBAR\s*PENGESAHAN|PENGESAHAN|LAMPIRAN|PENUTUP)[\s\S]*$/i, '').trim();

    // 5. Analisis batas potongan antara prev dan next
    var prevLines = cleanedPrev.split('\n');
    while (prevLines.length > 0 && prevLines[prevLines.length - 1].trim() === '') {
      prevLines.pop();
    }
    var lastPrevLine = prevLines.length > 0 ? prevLines[prevLines.length - 1].trim() : '';

    var nextLines = cleanedNext.split('\n');
    while (nextLines.length > 0 && nextLines[0].trim() === '') {
      nextLines.shift();
    }

    // Jika ujung prev adalah baris tabel (|)
    if (lastPrevLine.charAt(0) === '|') {
      var firstNextLine = nextLines.length > 0 ? nextLines[0].trim() : '';

      // Jika next diawali dengan header tabel yang diulang, buang baris header dan delimiter tersebut
      if (/^\|\s*(?:Minggu|No|Kemampuan|CPMK|Sub-CPMK|Modul)\s*\|/i.test(firstNextLine)) {
        nextLines.shift(); // Buang header
        if (nextLines.length > 0 && /^\|?[ \t]*[-:| \t]+\|?$/.test(nextLines[0].trim())) {
          nextLines.shift(); // Buang delimiter
        }
        while (nextLines.length > 0 && nextLines[0].trim() === '') {
          nextLines.shift();
        }
        firstNextLine = nextLines.length > 0 ? nextLines[0].trim() : '';
      }

      // Deteksi nomor minggu pada baris terakhir prev dan baris pertama next
      var mPrev = lastPrevLine.match(/^\|\s*(\d{1,2})\s*\|/);
      var mNext = firstNextLine.match(/^\|\s*(\d{1,2})\s*\|/);

      if (mPrev && mNext) {
        var numPrev = parseInt(mPrev[1], 10);
        var numNext = parseInt(mNext[1], 10);

        // Jika baris terakhir prev adalah baris yang terpotong / belum lengkap dari nomor yang sama dengan next
        if (numPrev === numNext) {
          prevLines.pop(); // Buang baris terpotong dari prev, gunakan baris utuh dari next
        }
      } else if (lastPrevLine.charAt(0) === '|' && (lastPrevLine.match(/\|/g) || []).length < 5) {
        // Baris terakhir prev terpotong parah di tengah-tengah kata
        prevLines.pop();
      }

      cleanedPrev = prevLines.join('\n');
      cleanedNext = nextLines.join('\n');

      // Sambungkan langsung dengan single newline agar tabel menyatu utuh
      return sanitizeMarkdownTables(cleanedPrev + "\n" + cleanedNext);
    }

    return sanitizeMarkdownTables(cleanedPrev + "\n\n" + cleanedNext);
  }

  /**
   * Fungsi Aksi Lanjutkan Dokumen yang terpotong karena limitasi token
   */
  function lanjutkanDokumen() {
    jalankanAksi(activeMode, true);
  }

  /**
   * Eksekusi Generate / Revisi / Lanjutan Dokumen AI
   */
  // Mode-mode yang membutuhkan CPMK APPROVED sebelum bisa dijalankan
  var RPS_GENERATION_MODES = ['susun_rps', 'kontrak_kuliah', 'rancangan_tugas', 'roadmap_wbi', 'skenario_pembelajaran'];

  /**
   * Update state tombol Generate di command bar berdasarkan mode aktif dan status CPMK.
   * Dipanggil setelah switchTab() atau setelah status CPMK berubah.
   */
  /**
   * Update state tombol Generate di command bar berdasarkan mode aktif dan status CPL/CPMK.
   * Dipanggil setelah switchTab() atau setelah status CPL/CPMK berubah.
   */
  function updateGenerateBtnState(mode) {
    if (!mode) mode = activeMode || 'studio_cpl';
    var meta = modeTabLabels[mode] || modeTabLabels['studio_cpl'];
    var isRpsMode = (RPS_GENERATION_MODES.indexOf(mode) !== -1);
    var cplApproved = (activeCplMkStatus === 'APPROVED');
    var cpmkApproved = (activeCpmkStatus === 'APPROVED');

    if (mode === 'studio_cpl') {
      $('#btn_submit_generate')
        .prop('disabled', false)
        .attr('title', 'Simpan draf CPL dan Deskripsi Mata Kuliah')
        .css({ opacity: '', cursor: '' });
      $('#btn_empty_generate')
        .prop('disabled', false)
        .attr('title', '');
    } else if (mode === 'susun_cpmk' && !cplApproved) {
      $('#btn_submit_generate')
        .prop('disabled', true)
        .attr('title', 'CPL Mata Kuliah & Deskripsi harus disetujui (APPROVED) di Studio CPL terlebih dahulu')
        .css({ opacity: '0.55', cursor: 'not-allowed' });
      $('#btn_empty_generate')
        .prop('disabled', true)
        .attr('title', 'CPL Mata Kuliah & Deskripsi harus disetujui di Studio CPL terlebih dahulu');
    } else if (isRpsMode && (!cplApproved || !cpmkApproved)) {
      var lockReason = !cplApproved
        ? 'CPL Mata Kuliah harus disetujui di Studio CPL terlebih dahulu'
        : 'CPMK harus disetujui (APPROVED) sebelum generate ' + meta.shortLabel;
      $('#btn_submit_generate')
        .prop('disabled', true)
        .attr('title', lockReason)
        .css({ opacity: '0.55', cursor: 'not-allowed' });
      $('#btn_empty_generate')
        .prop('disabled', true)
        .attr('title', lockReason);
    } else {
      // Tombol aktif
      $('#btn_submit_generate')
        .prop('disabled', false)
        .attr('title', '')
        .css({ opacity: '', cursor: '' });
      $('#btn_empty_generate')
        .prop('disabled', false)
        .attr('title', '');
    }
  }

  function jalankanAksi(mode, isContinuation) {
    if (!mode) mode = activeMode || 'studio_cpl';
    var namaMk = $.trim($('#mk_nama').val());
    if (!namaMk) {
      alert('Silakan pilih Mata Kuliah yang Diampu dari dropdown terlebih dahulu.');
      $('#select_mk_diampu').focus();
      return;
    }

    // ==========================================================================
    // STUDIO CPL ACTION: Trigger Save Draft CPL
    // ==========================================================================
    if (mode === 'studio_cpl') {
      simpanDraftCplStudio();
      return;
    }

    // ==========================================================================
    // PHASE 1.5 CPL APPROVAL GUARD (FRONTEND)
    // Mode susun_cpmk MANDATORY memiliki CPL-MK berstatus APPROVED.
    // ==========================================================================
    if (mode === 'susun_cpmk' && activeCplMkStatus !== 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'CPL & Deskripsi Belum Disetujui',
          html: '<div style="text-align:left; font-size:13px; line-height:1.7;">'
              + 'Sebelum menyusun draft CPMK, Anda wajib memilih dan <strong>menyetujui CPL yang dibebankan serta Deskripsi MK</strong> pada <strong>Studio CPL &amp; Deskripsi</strong>.<br><br>'
              + '<strong>Prinsip Source-First:</strong> CPMK hanya dirumuskan secara terarah dan terikat pada butir CPL resmi yang telah disetujui.'
              + '</div>',
          confirmButtonText: '<i class="fa fa-th-list"></i> Buka Studio CPL',
          confirmButtonColor: '#0f766e',
          showCancelButton: true,
          cancelButtonText: 'Tutup'
        }).then(function(result) {
          if (result.isConfirmed) {
            switchTab('studio_cpl');
          }
        });
      } else {
        alert('Silakan pilih dan setujui CPL-MK pada Studio CPL terlebih dahulu.');
        switchTab('studio_cpl');
      }
      return;
    }

    // ==========================================================================
    // PHASE 1 MANDATORY CPL GUARD (FRONTEND)
    // Mode susun_cpmk MANDATORY memiliki CPL resmi prodi (> 0).
    // ==========================================================================
    if (mode === 'susun_cpmk' && curriculumReadinessData && curriculumReadinessData.can_generate_cpmk === false) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'CPL Resmi Belum Tersedia',
          html: '<div style="text-align:left; font-size:13px; line-height:1.6;">'
              + 'Tidak dapat menyusun CPMK karena <strong>CPL resmi Program Studi</strong> belum berhasil ditemukan pada Dokumen Kurikulum acuan.<br><br>'
              + '<strong>Prinsip Source-First:</strong> CPMK wajib diturunkan secara faktual dari butir CPL resmi prodi dan tidak boleh dikarang oleh AI.'
              + '</div>',
          confirmButtonText: 'Mengerti',
          confirmButtonColor: '#dc2626'
        });
      } else {
        alert('Tidak dapat menyusun CPMK: CPL resmi Program Studi belum tersedia pada dokumen kurikulum.');
      }
      return;
    }

    // ==========================================================================
    // PHASE 1 WORKFLOW GUARD (FRONTEND)
    // Mode RPS dan dokumen turunan membutuhkan CPMK berstatus APPROVED.
    // ==========================================================================
    if (RPS_GENERATION_MODES.indexOf(mode) !== -1 && activeCpmkStatus !== 'APPROVED') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'CPMK Belum Disetujui',
          html: '<div style="text-align:left; font-size:13px; line-height:1.7;">'
              + 'Dokumen <strong>' + (modeTabLabels[mode] ? modeTabLabels[mode].label : mode) + '</strong> '
              + 'hanya dapat digenerate setelah CPMK mata kuliah ini disetujui.'
              + '<br><br>'
              + '<strong>Alur yang benar:</strong><br>'
              + '① Pilih Mata Kuliah<br>'
              + '② Periksa Curriculum Readiness<br>'
              + '③ Susun Draft CPMK dengan AI<br>'
              + '④ Edit & Setujui CPMK (klik <strong>Setujui CPMK</strong>)<br>'
              + '<strong>⑤ Generate ' + (modeTabLabels[mode] ? modeTabLabels[mode].shortLabel : mode) + '</strong>'
              + '</div>',
          confirmButtonText: '<i class="fa fa-cubes"></i> Buka CPMK Studio',
          showCancelButton: true,
          cancelButtonText: 'Tutup',
          confirmButtonColor: '#0f766e'
        }).then(function(result) {
          if (result.isConfirmed) {
            switchTab('susun_cpmk');
            if (!activeCpmkList || activeCpmkList.length === 0) {
              jalankanAksi('susun_cpmk');
            }
          }
        });
      } else {
        alert('CPMK harus disetujui terlebih dahulu sebelum generate ' + mode + '.');
      }
      return;
    }

    activeMode = mode;
    var meta = modeTabLabels[mode] || modeTabLabels['susun_rps'];

    // Ambil prompt instruksi dari textarea
    var promptText = isContinuation ? 'Lanjutkan penulisan dokumen dari bagian yang terpotong secara lengkap dan tuntas sampai pertemuan 16 (UAS). Jangan sertakan bagian lain di luar tabel 16 pertemuan.' : $.trim($('#mk_catatan').val());

    // Catat ke riwayat interaksi jika ada prompt khusus
    var historyIndex = -1;
    if (promptText && !isContinuation) {
      var now = new Date();
      var timeStr = (now.getHours() < 10 ? '0' : '') + now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes() + ' WIB';
      
      modePromptHistory[mode].push({
        prompt: promptText,
        time: timeStr,
        status: 'pending'
      });
      historyIndex = modePromptHistory[mode].length - 1;
      renderPromptHistory(mode);

      // Kosongkan textarea dan reset ukurannya
      $('#mk_catatan').val('').css('height', '58px');
    }

    // Pastikan Tab aktif
    $('.rps-tab-item').removeClass('active');
    $('#tab_' + mode).addClass('active');

    var titleText = meta.label;

    // Naskah sebelumnya untuk revisi iteratif presisi / lanjutan
    var prevMarkdown = modeDocStore[mode] ? modeDocStore[mode].markdown : currentMarkdownOutput;

    // Update UI to Loading State
    $('#canvas_placeholder').hide();
    $('#canvas_output').hide();
    $('#canvas_tools').css('display', 'none');
    $('#btn_unduh_roadmap').hide();
    $('#rps_continue_banner').hide();

    if (isContinuation) {
      $('#btn_submit_generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin" style="font-size:15px;"></i><span style="line-height:1.2;">Menyambung <strong>' + meta.shortLabel + '...</strong></span>');
      $('#btn_empty_generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyambung...');
      $('#btn_lanjutkan_generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyambung Dokumen...');
      $('#loading_label').html('AI sedang melanjutkan penyusunan <span style="color:#1b4332;">' + titleText + '</span>...');
      $('#loading_subtext').text('Menyambung langsung dari baris terakhir tanpa mengulang bagian yang sudah selesai.');
    } else {
      $('#btn_submit_generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin" style="font-size:15px;"></i><span style="line-height:1.2;">Menyusun <strong>' + meta.shortLabel + '...</strong></span>');
      $('#btn_empty_generate').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyusun...');
      if (mode === 'susun_cpmk') {
        $('#btn_susun_ulang_cpmk').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyusun CPMK...');
        $('#cpmk_cards_container').html('<div style="text-align:center; padding:35px; color:#0f766e; background:#f0fdf4; border-radius:8px; border:1px dashed #86efac;">'
          + '<i class="fa fa-circle-o-notch fa-spin" style="font-size:28px; margin-bottom:10px; display:block; color:#15803d;"></i>'
          + '<strong>AI sedang merumuskan draf CPMK berbasis CPL resmi...</strong><br><span style="font-size:12px; color:#475569;">Menyelaraskan CPL prodi, Bahan Kajian, Profil MK, &amp; Taksonomi Bloom C2–C6.</span>'
          + '</div>');
        $('#loading_label').html('AI sedang merumuskan <span style="color:#1b4332;">Draft CPMK Berbasis Kurikulum</span>...');
        $('#loading_subtext').text('Menyelaraskan CPL prodi, Bahan Kajian, Profil MK, & Taksonomi Bloom C2–C6.');
      } else if (mode === 'susun_rps') {
        if (uploadedDocs.excel_pertemuan_md) {
          $('#loading_label').html('AI sedang menyusun <span style="color:#1b4332;">RPS OBE (Menyelaraskan dengan Excel 16 Pertemuan)</span>...');
          $('#loading_subtext').text('Menggunakan data 16 pertemuan dari Excel dosen. AI menyelaraskan CPL, CPMK, Deskripsi, & Blueprint Asesmen.');
        } else {
          $('#loading_label').html('AI sedang menyusun <span style="color:#1b4332;">RPS lengkap 16 pertemuan dan Kontrak Kuliah</span>...');
          $('#loading_subtext').text('Menyelaraskan data acuan dengan regulasi SN-Dikti 2026, Taksonomi Bloom C4-C6, & Format WBI.');
        }
      } else {
        $('#loading_label').html('AI Sedang Menyusun <span style="color:#1b4332;">' + titleText + '</span>...');
        $('#loading_subtext').text('Menyelaraskan data acuan dengan regulasi SN-Dikti 2026, Taksonomi Bloom C4-C6, & Format WBI.');
      }
    }

    $('#canvas_loader_bar').fadeIn(100);
    $('#canvas_loading').fadeIn(150);

    $('#canvas_title').html('<i class="fa fa-circle-o-notch fa-spin text-primary"></i> ' + titleText);
    $('#canvas_badge').attr('class', 'rps-tag tag-info').text('Sedang Diproses...');

    startProgressStepper(mode);

    // Prepare Payload
    var postData = {
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
      mode: mode,
      is_continuation: isContinuation ? 1 : 0,
      mata_kuliah: namaMk,
      kode_mk: $('#mk_kode').val(),
      sks: $('#mk_sks').val(),
      semester: $('#mk_semester').val(),
      dosen: $('#mk_dosen').val(),
      prodi: $('#mk_prodi').val(),
      pertemuan_target: '1',
      catatan_khusus: promptText,
      naskah_sebelumnya: prevMarkdown,
      teks_kurikulum: uploadedDocs.kurikulum,
      teks_penelitian: uploadedDocs.penelitian,
      teks_rps_lama: uploadedDocs.rps_lama,
      excel_pertemuan_md: uploadedDocs.excel_pertemuan_md,
      excel_matrix_json: uploadedDocs.excel_matrix_json,
      teks_rps_induk: (modeDocStore['susun_rps'] && modeDocStore['susun_rps'].markdown) ? modeDocStore['susun_rps'].markdown : '',
      cpmk_status: activeCpmkStatus || ''  // Dikirim ke backend sebagai konfirmasi frontend
    };

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_process"); ?>',
      type: 'POST',
      data: postData,
      dataType: 'json',
      complete: function() {
        stopProgressStepper();
        $('#btn_submit_generate').prop('disabled', false);
        $('#btn_empty_generate').prop('disabled', false);
        $('#btn_susun_ulang_cpmk').prop('disabled', false).html('<i class="fa fa-magic text-primary"></i> Usulan AI (Fallback)');
        $('#btn_lanjutkan_generate').prop('disabled', false).html('<i class="fa fa-play-circle"></i> Lanjutkan Penyusunan Dokumen');
      },
      success: function(res) {
        $('#canvas_loader_bar').hide();
        $('#canvas_loading').hide();

        if (res.status) {
          if (mode === 'susun_cpmk') {
            $('#canvas_badge').attr('class', 'rps-tag tag-warning').text('AI_PROPOSAL');
            $('#cpmk_global_status_badge').text('DRAFT (AI)').css({ background:'#fef3c7', color:'#b45309', borderColor:'#fde68a' });
            if (res.cpmk_data && Array.isArray(res.cpmk_data)) {
              activeCpmkList = res.cpmk_data;
              activeCpmkStatus = res.cpmk_status || 'AI_PROPOSAL';
              if (studioCpmkData) {
                studioCpmkData.cpmk_items = activeCpmkList;
                studioCpmkData.cpmk_status = activeCpmkStatus;
              }
              renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
              renderCpmkCards();

              if (curriculumReadinessData && curriculumReadinessData.checks && curriculumReadinessData.checks.cpmk_status) {
                curriculumReadinessData.checks.cpmk_status.status = 'AI_PROPOSAL';
                curriculumReadinessData.checks.cpmk_status.label = 'Draft AI (' + activeCpmkList.length + ' CPMK)';
                renderCurriculumReadinessUi(curriculumReadinessData);
              }

              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  icon: 'success',
                  title: 'Draft CPMK Berhasil Disusun',
                  html: 'AI telah merumuskan <strong>' + activeCpmkList.length + ' CPMK</strong> berbasis kurikulum dan pemetaan CPL.<br><br><span style="font-size:12px; color:#475569;">Anda dapat mengedit redaksi secara bebas, menambah, menghapus, atau menyetujui CPMK.</span>',
                  timer: 3500,
                  showConfirmButton: false
                });
              }
            } else {
              renderCpmkCards();
            }
            return;
          }
          if (isContinuation) {
            currentMarkdownOutput = cleanAndMergeMarkdown(prevMarkdown, res.output);
          } else {
            currentMarkdownOutput = res.output;
          }

          if (res.matrix_data) {
            currentMatrixJson = res.matrix_data;
          }

          var isTruncated = !!(res.is_truncated || (res.output && res.output.indexOf('MAX_TOKENS') !== -1));

          // Update status riwayat prompt
          if (historyIndex >= 0 && modePromptHistory[mode][historyIndex]) {
            modePromptHistory[mode][historyIndex].status = 'done';
            renderPromptHistory(mode);
          }

          // Check if output contains Roadmap JSON & Bersihkan dari tampilan markdown teks
          currentRoadmapJson = null;
          var displayMarkdown = currentMarkdownOutput;
          if (mode === 'roadmap_wbi') {
            var rmClean = extractAndCleanRoadmapJson(currentMarkdownOutput);
            if (rmClean.json) {
              currentRoadmapJson = rmClean.json;
            }
            displayMarkdown = rmClean.cleanMarkdown;
            currentMarkdownOutput = displayMarkdown;
          } else {
            displayMarkdown = currentMarkdownOutput.replace(/```(?:json_roadmap|json)?\s*\{[\s\S]*?"modules"[\s\S]*?\}\s*```/g, '').trim();
          }

          // Render Markdown & Visual Grid jika mode Roadmap
          var htmlOutput = '';
          if (mode === 'roadmap_wbi' && currentRoadmapJson) {
            htmlOutput += renderRoadmapVisualGrid(currentRoadmapJson, namaMk);
          }
          htmlOutput += renderMarkdownSafe(displayMarkdown);

          // Simpan ke memory store dokumen
          modeDocStore[mode] = {
            markdown: currentMarkdownOutput,
            html: htmlOutput,
            matrixJson: currentMatrixJson,
            roadmapJson: currentRoadmapJson,
            isTruncated: isTruncated
          };

          // Jika menyusun RPS Induk, otomatis selaraskan Kontrak Kuliah dan bobot asesmennya
          if (mode === 'susun_rps') {
            syncKontrakKuliahFromRps(currentMarkdownOutput);
          }

          // Tampilkan output tab
          switchTab(mode);

          if (isContinuation && !isTruncated) {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Penyusunan Tuntas!',
                text: 'Dokumen berhasil disambung dan diselesaikan secara lengkap.',
                timer: 2500,
                showConfirmButton: false
              });
            }
          }

        } else {
          // Tangani error khusus CPMK belum approved (kembalikan ke CPMK Studio)
          if (res.is_cpmk_not_approved) {
            stopProgressStepper();
            $('#canvas_loader_bar').hide();
            $('#canvas_loading').hide();
            switchTab('susun_cpmk');
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'warning',
                title: 'CPMK Belum Disetujui',
                html: res.msg || 'CPMK harus disetujui sebelum generate RPS.',
                confirmButtonText: '<i class="fa fa-cubes"></i> Buka CPMK Studio',
                confirmButtonColor: '#0f766e'
              });
            }
            return;
          }
          switchTab(mode);
          $('#canvas_title').html('<i class="fa fa-exclamation-circle text-danger"></i> Gagal Memproses');
          $('#canvas_badge').attr('class', 'rps-tag').text('Error');

          var rawMsg = res.msg || 'Terjadi kesalahan saat memproses AI.';
          var formattedHtml = rawMsg
            .replace(/\n\n/g, '<br><br>')
            .replace(/\n/g, '<br>')
            .replace(/⚠️/g, '<span style="font-size:18px;">⚠️</span>')
            .replace(/💡/g, '<span style="font-size:18px;">💡</span>');

          if (typeof Swal !== 'undefined') {
            if (res.is_kurikulum_mismatch) {
              Swal.fire({
                title: 'Dokumen Acuan Kurikulum',
                html: '<div style="text-align:left; font-size:13px; line-height:1.6; color:#334155;">' + formattedHtml + '</div>',
                icon: 'warning',
                confirmButtonColor: '#0f766e',
                confirmButtonText: '<i class="fa fa-check"></i> Saya Mengerti, Siapkan Acuan'
              });
            } else {
              Swal.fire({
                title: 'Peringatan Sistem AI',
                html: '<div style="text-align:left; font-size:13px; line-height:1.6; color:#334155;">' + formattedHtml + '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0D9488',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa fa-key"></i> Buka Setting API Key',
                cancelButtonText: 'Tutup'
              }).then(function(result) {
                if (result.value || result.isConfirmed) {
                  window.location.href = '<?= site_url("setting_api_key") ?>';
                }
              });
            }
          } else {
            alert(rawMsg);
          }
        }
      },
      error: function(xhr, status, error) {
        $('#canvas_loader_bar').hide();
        $('#canvas_loading').hide();
        switchTab(mode);
        $('#canvas_title').html('<i class="fa fa-exclamation-circle text-danger"></i> Gangguan Jaringan');
        $('#canvas_badge').attr('class', 'rps-tag').text('Koneksi Terputus');
        
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Koneksi Terputus',
            text: 'Gagal menghubungi server: ' + (xhr.responseText || error),
            icon: 'error',
            confirmButtonColor: '#0D9488'
          });
        } else {
          alert('Gagal menghubungi server: ' + (xhr.responseText || error));
        }
      }
    });
  }

  function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function strtoupper(text) {
    if (text === null || text === undefined) return '';
    return String(text).toUpperCase();
  }

  /**
   * Salin Markdown ke Clipboard
   */
  function salinHasil() {
    if (!currentMarkdownOutput) return;
    navigator.clipboard.writeText(currentMarkdownOutput).then(function() {
      alert('Naskah berhasil disalin ke clipboard.');
    }, function() {
      var textarea = document.createElement('textarea');
      textarea.value = currentMarkdownOutput;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
      alert('Naskah berhasil disalin ke clipboard.');
    });
  }

  /**
   * Unduh File Markdown (.md)
   */
  function unduhMarkdown() {
    if (!currentMarkdownOutput) return;
    var mkNama = ($('#mk_nama').val() || 'RPS_OBE').replace(/[^a-zA-Z0-9_-]/g, '_');
    var blob = new Blob([currentMarkdownOutput], { type: 'text/markdown;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = (activeMode || 'Dokumen') + '_' + mkNama + '.md';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  /**
   * Cetak Hasil
   */
  function cetakHasil() {
    var content = document.getElementById('rendered_markdown').innerHTML;
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Dokumen RPS OBE</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #0f172a; padding: 25px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11px; }');
    printWindow.document.write('th, td { border: 1px solid #94a3b8; padding: 6px 8px; vertical-align: top; }');
    printWindow.document.write('th { background: #f1f5f9; font-weight: bold; }');
    printWindow.document.write('h1, h2, h3 { color: #0f172a; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-top: 20px; }');
    printWindow.document.write('@media print { button { display: none; } }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
      printWindow.print();
    }, 400);
  }

  /**
   * Unduh Excel Roadmap Format WBI
   */
  function unduhRoadmapExcel() {
    if (!currentRoadmapJson) {
      alert('Data roadmap belum tersedia untuk diekspor ke Excel. Silakan susun dokumen Roadmap terlebih dahulu.');
      return;
    }

    $('#export_roadmap_kode_mk').val($('#mk_kode').val());
    $('#export_roadmap_nama_mk').val($('#mk_nama').val());
    $('#export_roadmap_json').val(JSON.stringify(currentRoadmapJson));
    $('#formExportRoadmap').submit();
  }

  /**
   * Unduh Template Excel Formula Evaluasi Nilai CPMK
   */
  function unduhTemplateEvaluasiExcel() {
    var namaMk = $.trim($('#mk_nama').val()) || 'Mata_Kuliah';
    window.open('<?= site_url("C_rps_generator/download_template_evaluasi_cpmk"); ?>?nama_mk=' + encodeURIComponent(namaMk), '_blank');
  }

  /**
   * Cek kelengkapan dan status keterpotongan (token limit) seluruh tab dokumen
   */
  function checkAllDocsCompletion() {
    var truncatedDocs = [];
    var tabModes = [
      { key: 'susun_rps', label: 'RPS Lengkap 16 Pertemuan' },
      { key: 'kontrak_kuliah', label: 'Kontrak Perkuliahan' },
      { key: 'rancangan_tugas', label: 'Rancangan Tugas Mahasiswa' },
      { key: 'roadmap_wbi', label: 'Roadmap Modul WBI' },
      { key: 'skenario_pembelajaran', label: 'Strategi Pertemuan' }
    ];

    tabModes.forEach(function(item) {
      var store = modeDocStore[item.key];
      var isCurrent = (activeMode === item.key);
      var md = store ? store.markdown : (isCurrent ? currentMarkdownOutput : '');
      var isTrunc = store ? !!store.isTruncated : false;

      if (md) {
        var reasons = [];
        if (isTrunc || /MAX_TOKENS|finish_reason\s*:\s*length/i.test(md) || />\s*\[!WARNING\][\s\S]*terpotong/i.test(md)) {
          reasons.push('Terpotong karena limitasi token output AI');
        } else if (item.key === 'susun_rps' && !/(?:Minggu\s*(?:ke\s*[-–—:]?\s*)?16|Evaluasi Akhir Semester|UAS)/i.test(md)) {
          reasons.push('Tabel rincian pertemuan belum tuntas sampai Minggu 16 (UAS)');
        } else if (item.key === 'skenario_pembelajaran' && !/(?:PERTEMUAN\s*(?:KE\s*[-–—:]?\s*)?16|EAS|UAS)/i.test(md)) {
          reasons.push('Skenario perkuliahan belum tuntas sampai Pertemuan 16 (UAS)');
        }

        if (reasons.length > 0) {
          truncatedDocs.push({
            mode: item.key,
            label: item.label,
            reason: reasons.join(', ')
          });
        }
      }
    });

    return truncatedDocs;
  }

  /**
   * Simpan Hasil RPS OBE ke Database (Tabel siakad_rps_obe & siakad_rps_obe_detail)
   */
  function simpanKeDatabaseRpsObe(forceSave) {
    var mkNama = $('#mk_nama').val();
    if (!mkNama) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Nama Mata Kuliah Kosong',
          text: 'Harap pilih Mata Kuliah terlebih dahulu.',
          icon: 'warning',
          confirmButtonColor: '#0D9488'
        });
      } else {
        alert('Harap pilih Mata Kuliah terlebih dahulu.');
      }
      return;
    }

    // 1. Validasi Token Limit / Dokumen Terpotong di Semua Tab
    if (!forceSave) {
      var truncatedList = checkAllDocsCompletion();
      if (truncatedList.length > 0) {
        var listHtml = '<ul style="text-align:left; font-size:13px; margin:10px 0; padding-left:20px; color:#991b1b; line-height:1.6;">';
        truncatedList.forEach(function(item) {
          listHtml += '<li><strong>' + item.label + '</strong>: ' + item.reason + '</li>';
        });
        listHtml += '</ul>';

        var firstTruncMode = truncatedList[0].mode;
        var firstTruncLabel = truncatedList[0].label;

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: '⚠️ Dokumen Belum Tuntas!',
            html: '<div style="text-align:left; font-size:13px; color:#334155; line-height:1.6;">'
                + 'Ditemukan dokumen yang <strong>belum selesai disusun secara lengkap</strong> (terpotong karena batas token AI):'
                + listHtml
                + '<p style="margin-top:10px; color:#475569;">Sangat disarankan untuk <strong>melanjutkan penyusunan dokumen</strong> terlebih dahulu agar data RPS OBE tersimpan utuh dan sempurna.</p>'
                + '</div>',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#0284c7',
            denyButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa fa-play-circle"></i> Lanjutkan ' + firstTruncLabel,
            denyButtonText: '<i class="fa fa-save"></i> Tetap Simpan Draf Parsial',
            cancelButtonText: 'Batal & Periksa Dulu'
          }).then(function(result) {
            if (result.isConfirmed) {
              // Beralih ke tab yang terpotong dan jalankan lanjutan
              switchTab(firstTruncMode);
              lanjutkanDokumen();
            } else if (result.isDenied) {
              // Pengguna memilih untuk tetap menyimpan draf parsial
              simpanKeDatabaseRpsObe(true);
            }
          });
          return;
        } else {
          var proceed = confirm('Peringatan: Dokumen pada ' + firstTruncLabel + ' belum selesai karena batas token. Apakah Anda yakin ingin tetap menyimpan draf parsial?');
          if (!proceed) return;
        }
      }
    }

    // 2. Validasi Mutlak: DAFTAR REFERENSI (Bagian 5) Wajib Terisi oleh Dosen
    var rpsCheckMd = (modeDocStore['susun_rps'] && modeDocStore['susun_rps'].markdown) ? modeDocStore['susun_rps'].markdown : (activeMode === 'susun_rps' ? currentMarkdownOutput : '');
    var matchRef = rpsCheckMd.match(/(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DAFTAR REFERENSI|REFERENSI|DAFTAR PUSTAKA|PUSTAKA)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DETAIL RENCANA|RENCANA PEMBELAJARAN|TABEL|MATRIKS|DISTRIBUSI|DESKRIPSI))|\Z)/i);
    var refContent = matchRef ? matchRef[1].trim() : '';
    refContent = refContent.replace(/(?:\n+|\A)\s*\|{1,2}[\s\S]*$/i, '').trim();

    if (!refContent || refContent === '-' || refContent === 'Tidak ada' || /belum\s+diisi|wajib\s+(?:menginput|memasukkan)|\[Referensi/i.test(refContent)) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '⚠️ Referensi Belum Diisi!',
          html: '<div style="text-align:left; font-size:13.5px; color:#334155; line-height:1.6;">'
              + '<p>Dokumen RPS <strong>tidak dapat disimpan ke database</strong> karena <strong>Daftar Referensi (Bagian 5)</strong> masih kosong.</p>'
              + '<p style="margin-top:8px; color:#0f766e; background:#f0fdf4; padding:10px; border-radius:6px; border:1px solid #bbf7d0;">'
              + '<i class="fa fa-info-circle"></i> <strong>Petunjuk untuk Dosen:</strong><br>'
              + 'Silakan ketikkan daftar buku referensi/modul perkuliahan pada kotak <strong>Prompt Instruksi / Catatan Tambahan</strong> di sebelah kiri (contoh: <em>"Tambahkan referensi: 1. Penulis (Tahun). Judul Buku. Penerbit."</em>), lalu klik tombol <strong>Susun / Generate Dokumen AI</strong>.</p>'
              + '</div>',
          icon: 'warning',
          confirmButtonColor: '#0284c7',
          confirmButtonText: '<i class="fa fa-pencil"></i> Saya Mengerti, Isi Prompt Sekarang'
        }).then(function() {
          $('#mk_catatan').focus();
        });
      } else {
        alert('Peringatan: Dokumen RPS tidak dapat disimpan karena Daftar Referensi (Bagian 5) masih kosong. Dosen wajib menginput daftar referensi perkuliahan melalui kolom prompt instruksi terlebih dahulu.');
        $('#mk_catatan').focus();
      }
      return;
    }

    var rpsData = modeDocStore['susun_rps'] ? modeDocStore['susun_rps'].markdown : currentMarkdownOutput;
    var roadmapData = modeDocStore['roadmap_wbi'] ? modeDocStore['roadmap_wbi'].roadmapJson : currentRoadmapJson;

    var $btn = $('#btn_save_db_rps_obe');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan Seluruh Dokumen...');

    var rpsMd      = (modeDocStore['susun_rps'] && modeDocStore['susun_rps'].markdown) ? modeDocStore['susun_rps'].markdown : (activeMode === 'susun_rps' ? currentMarkdownOutput : '');
    var roadmapMd  = (modeDocStore['roadmap_wbi'] && modeDocStore['roadmap_wbi'].markdown) ? modeDocStore['roadmap_wbi'].markdown : (activeMode === 'roadmap_wbi' ? currentMarkdownOutput : '');
    var kontrakMd  = (modeDocStore['kontrak_kuliah'] && modeDocStore['kontrak_kuliah'].markdown) ? modeDocStore['kontrak_kuliah'].markdown : (activeMode === 'kontrak_kuliah' ? currentMarkdownOutput : '');
    var tugasMd    = (modeDocStore['rancangan_tugas'] && modeDocStore['rancangan_tugas'].markdown) ? modeDocStore['rancangan_tugas'].markdown : (activeMode === 'rancangan_tugas' ? currentMarkdownOutput : '');
    var skenarioMd = (modeDocStore['skenario_pembelajaran'] && modeDocStore['skenario_pembelajaran'].markdown) ? modeDocStore['skenario_pembelajaran'].markdown : (activeMode === 'skenario_pembelajaran' ? currentMarkdownOutput : '');

    var roadmapData = (modeDocStore['roadmap_wbi'] && modeDocStore['roadmap_wbi'].roadmapJson) ? modeDocStore['roadmap_wbi'].roadmapJson : currentRoadmapJson;

    var payload = {
      id_rps: $('#id_rps_loaded').val(),
      mata_kuliah: mkNama,
      kode_mk: $('#mk_kode').val(),
      sks: $('#mk_sks').val(),
      semester: $('#mk_semester').val(),
      prodi: $('#mk_prodi').val(),
      dosen: $('#mk_dosen').val(),
      raw_ai_output: rpsMd,
      roadmap_wbi_markdown: roadmapMd,
      kontrak_kuliah_markdown: kontrakMd,
      rancangan_tugas_markdown: tugasMd,
      skenario_pembelajaran_markdown: skenarioMd,
      roadmap_json: roadmapData ? JSON.stringify(roadmapData) : '',
      matrix_json: currentMatrixJson ? JSON.stringify(currentMatrixJson) : '',
      cpmk_json: (activeCpmkList && activeCpmkList.length > 0) ? JSON.stringify(activeCpmkList) : '',
      cpmk_status: activeCpmkStatus || '',
      ai_provider: $('#ai_provider_select').val() || 'gemini',
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
    };

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_save_to_db"); ?>',
      type: 'POST',
      data: payload,
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false).html(origHtml);
        if (res.status) {
          $('#id_rps_loaded').val(res.id_rps);
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              title: 'Berhasil Disimpan!',
              html: res.msg + '<br><br><a href="<?= site_url("C_rps_obe"); ?>" class="btn btn-sm btn-primary" style="margin-top:10px;"><i class="fa fa-list"></i> Lihat Daftar RPS OBE</a>',
              icon: 'success',
              confirmButtonColor: '#16a34a',
              confirmButtonText: 'Tutup'
            });
          } else {
            alert(res.msg);
          }
        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              title: 'Gagal Menyimpan',
              text: res.msg,
              icon: 'error',
              confirmButtonColor: '#0D9488'
            });
          } else {
            alert('Gagal: ' + res.msg);
          }
        }
      },
      error: function(xhr, status, error) {
        $btn.prop('disabled', false).html(origHtml);
        alert('Terjadi kesalahan jaringan saat menyimpan ke database: ' + (xhr.responseText || error));
      }
    });
  }

  var availableDosenSchedules = [];

  /**
   * Buka Modal Sinkronisasi Skenario & BAP ke Jadwal Kelas
   */
  function bukaModalSyncJadwal() {
    var kodeMk = $('#mk_kode').val() || '';
    var namaMk = $('#mk_nama').val() || '';
    var skenarioMd = (modeDocStore['skenario_pembelajaran'] && modeDocStore['skenario_pembelajaran'].markdown) ? modeDocStore['skenario_pembelajaran'].markdown : currentMarkdownOutput;

    if (!skenarioMd) {
      alert('Silakan susun / generate Skenario Pembelajaran & Draf BAP terlebih dahulu.');
      return;
    }

    $('#label_mk_sync').text(namaMk ? (namaMk + (kodeMk ? ' [' + kodeMk + ']' : '')) : (kodeMk || 'Mata Kuliah Ini'));
    $('#modalSyncJadwalKelas').modal('show');
    $('#loading_schedules').show();
    $('#container_matching_classes').empty();
    $('#container_other_classes').empty();
    $('#box_other_schedules_wrapper').hide();

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_get_dosen_schedules"); ?>',
      type: 'POST',
      data: {
        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
        kode_mk: kodeMk
      },
      dataType: 'json',
      success: function(res) {
        $('#loading_schedules').hide();

        if (res.status && res.schedules && res.schedules.length > 0) {
          availableDosenSchedules = res.schedules;
          var matching = res.matching_schedules || [];
          var others = res.other_schedules || [];

          // Render matching classes (default checked)
          var htmlMatching = '';
          if (matching.length > 0) {
            matching.forEach(function(s) {
              var timeRange = (s.jam_mulai ? s.jam_mulai.substring(0,5) : '') + ' - ' + (s.jam_selesai ? s.jam_selesai.substring(0,5) : '');
              var statusBap = s.total_rencana > 0 
                ? '<span style="display:inline-block; font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:4px; background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;"><i class="fa fa-check-circle"></i> ' + s.total_rencana + '/16 Rencana</span>'
                : '<span style="display:inline-block; font-size:10.5px; font-weight:600; padding:2px 7px; border-radius:4px; background:#fef3c7; color:#92400e; border:1px solid #fde68a;"><i class="fa fa-clock-o"></i> Belum Ada BAP</span>';

              var mkInfoHtml = '';
              if (s.kelas_list && s.kelas_list.length > 0) {
                mkInfoHtml = s.kelas_list.map(function(k) {
                  return '<div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:2px;">'
                    + (k.kode_mk ? '<span style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-weight:700; font-size:11px; padding:1px 6px; border-radius:4px; border:1px solid #cbd5e1;">' + escapeHtml(k.kode_mk) + '</span>' : '')
                    + '<strong style="color:#0f172a; font-size:13px;">' + escapeHtml(k.nm_mk || 'Mata Kuliah') + '</strong>'
                    + '<span style="background:#ede9fe; color:#5b21b6; font-weight:700; font-size:11px; padding:1px 6px; border-radius:4px; border:1px solid #ddd6fe;"><i class="fa fa-users" style="font-size:9.5px;"></i> Kelas ' + escapeHtml(k.nm_kls) + '</span>'
                    + (k.nm_prodi ? '<span style="background:#ecfdf5; color:#065f46; font-weight:600; font-size:11px; padding:1px 6px; border-radius:4px; border:1px solid #a7f3d0;"><i class="fa fa-university" style="font-size:9.5px;"></i> ' + escapeHtml(k.nm_prodi) + '</span>' : '')
                    + '</div>';
                }).join('');
              } else {
                mkInfoHtml = '<div style="font-weight:700; color:#0f172a; font-size:13px;">' + escapeHtml(s.label_kelas || s.tag_name) + '</div>';
              }

              htmlMatching += '<div class="sync-class-card" style="background:#ffffff; border:1.5px solid #0284c7; border-radius:8px; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px; cursor:pointer;" onclick="toggleSyncClassCard(this)">'
                + '<div style="display:flex; align-items:center; gap:12px;">'
                + '<input type="checkbox" name="id_jadwal_list[]" value="' + s.id_jadwal + '" data-hari="' + (s.nama_hari || '') + '" data-dayid="' + (s.id_hari || 0) + '" checked style="width:18px; height:18px; cursor:pointer; accent-color:#0284c7; margin:0;" onclick="event.stopPropagation(); updateSyncSchedulePreview();">'
                + '<div>'
                + mkInfoHtml
                + '<div style="font-size:11.5px; color:#475569; display:flex; align-items:center; gap:8px; margin-top:3px; flex-wrap:wrap;">'
                + '<span><i class="fa fa-calendar" style="color:#0284c7;"></i> <strong>' + escapeHtml(s.nama_hari || '') + '</strong>, ' + timeRange + ' WIB</span>'
                + '<span style="color:#cbd5e1;">•</span>'
                + '<span><i class="fa fa-map-marker text-danger"></i> ' + escapeHtml(s.nama_ruang || 'Ruang Kuliah') + '</span>'
                + '<span style="background:#f1f5f9; color:#475569; font-size:10px; font-weight:600; padding:1px 5px; border-radius:4px;">' + escapeHtml(s.tag_name || 'Reguler') + '</span>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '<div>' + statusBap + '</div>'
                + '</div>';
            });
            $('#container_matching_classes').html(htmlMatching);
          } else {
            $('#container_matching_classes').html('<div style="padding:14px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; font-size:12px; color:#92400e;"><i class="fa fa-exclamation-triangle"></i> Tidak ada jadwal kelas yang menggunakan kode mata kuliah <strong>' + escapeHtml(kodeMk) + '</strong> secara langsung. Silakan pilih dari jadwal kelas lainnya di bawah.</div>');
          }

          // Render other schedules if any
          if (others.length > 0) {
            var htmlOthers = '';
            others.forEach(function(s) {
              var timeRange = (s.jam_mulai ? s.jam_mulai.substring(0,5) : '') + ' - ' + (s.jam_selesai ? s.jam_selesai.substring(0,5) : '');
              var statusBap = s.total_rencana > 0 
                ? '<span style="display:inline-block; font-size:10.5px; font-weight:700; padding:2px 7px; border-radius:4px; background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;"><i class="fa fa-check-circle"></i> ' + s.total_rencana + '/16 Rencana</span>'
                : '<span style="display:inline-block; font-size:10.5px; font-weight:600; padding:2px 7px; border-radius:4px; background:#fef3c7; color:#92400e; border:1px solid #fde68a;"><i class="fa fa-clock-o"></i> Belum Ada BAP</span>';

              var mkInfoHtml = '';
              if (s.kelas_list && s.kelas_list.length > 0) {
                mkInfoHtml = s.kelas_list.map(function(k) {
                  return '<div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:2px;">'
                    + (k.kode_mk ? '<span style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-weight:700; font-size:11px; padding:1px 6px; border-radius:4px; border:1px solid #cbd5e1;">' + escapeHtml(k.kode_mk) + '</span>' : '')
                    + '<strong style="color:#0f172a; font-size:12.5px;">' + escapeHtml(k.nm_mk || 'Mata Kuliah') + '</strong>'
                    + '<span style="background:#ede9fe; color:#5b21b6; font-weight:700; font-size:10.5px; padding:1px 6px; border-radius:4px; border:1px solid #ddd6fe;"><i class="fa fa-users" style="font-size:9.5px;"></i> Kelas ' + escapeHtml(k.nm_kls) + '</span>'
                    + (k.nm_prodi ? '<span style="background:#ecfdf5; color:#065f46; font-weight:600; font-size:10.5px; padding:1px 6px; border-radius:4px; border:1px solid #a7f3d0;"><i class="fa fa-university" style="font-size:9.5px;"></i> ' + escapeHtml(k.nm_prodi) + '</span>' : '')
                    + '</div>';
                }).join('');
              } else {
                mkInfoHtml = '<div style="font-weight:700; color:#0f172a; font-size:12.5px;">' + escapeHtml(s.label_kelas || s.tag_name) + '</div>';
              }

              htmlOthers += '<div class="sync-class-card" style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:9px 12px; display:flex; align-items:center; justify-content:space-between; gap:10px; cursor:pointer;" onclick="toggleSyncClassCard(this)">'
                + '<div style="display:flex; align-items:center; gap:12px;">'
                + '<input type="checkbox" name="id_jadwal_list[]" value="' + s.id_jadwal + '" data-hari="' + (s.nama_hari || '') + '" data-dayid="' + (s.id_hari || 0) + '" style="width:18px; height:18px; cursor:pointer; accent-color:#0284c7; margin:0;" onclick="event.stopPropagation(); updateSyncSchedulePreview();">'
                + '<div>'
                + mkInfoHtml
                + '<div style="font-size:11px; color:#64748b; display:flex; align-items:center; gap:8px; margin-top:2px; flex-wrap:wrap;">'
                + '<span><i class="fa fa-calendar" style="color:#0284c7;"></i> <strong>' + escapeHtml(s.nama_hari || '') + '</strong>, ' + timeRange + ' WIB</span>'
                + '<span style="color:#cbd5e1;">•</span>'
                + '<span><i class="fa fa-map-marker text-danger"></i> ' + escapeHtml(s.nama_ruang || '-') + '</span>'
                + '<span style="background:#f1f5f9; color:#475569; font-size:10px; font-weight:600; padding:1px 5px; border-radius:4px;">' + escapeHtml(s.tag_name || 'Reguler') + '</span>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '<div>' + statusBap + '</div>'
                + '</div>';
            });
            $('#container_other_classes').html(htmlOthers);
            $('#box_other_schedules_wrapper').show();
          }

          updateSyncSchedulePreview();
        } else {
          availableDosenSchedules = [];
          $('#container_matching_classes').html('<div style="padding:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; font-size:12px; color:#64748b; text-align:center;">Tidak ada jadwal mengajar aktif pada semester ini.</div>');
          $('#btn_execute_sync_jadwal').prop('disabled', true);
        }
      },
      error: function() {
        $('#loading_schedules').hide();
        $('#container_matching_classes').html('<div style="padding:14px; background:#fee2e2; border:1px solid #fecaca; border-radius:8px; font-size:12px; color:#991b1b;"><i class="fa fa-exclamation-circle"></i> Gagal memuat jadwal dari server.</div>');
      }
    });
  }

  /**
   * Toggle Checkbox saat Card Kelas di-klik
   */
  function toggleSyncClassCard(el) {
    var $cb = $(el).find('input[type="checkbox"]');
    $cb.prop('checked', !$cb.is(':checked'));
    updateSyncSchedulePreview();
  }

  /**
   * Pilih Semua / Hapus Pilihan Kelas
   */
  function selectAllSyncClasses(selectAll) {
    $('#container_matching_classes input[type="checkbox"]').prop('checked', selectAll);
    updateSyncSchedulePreview();
  }

  /**
   * Hitung dan Perbarui Tampilan Proyeksi Tanggal & Tombol Eksekusi
   */
  function updateSyncSchedulePreview() {
    var selectedCheckboxes = $('#formSyncJadwal input[name="id_jadwal_list[]"]:checked');
    var count = selectedCheckboxes.length;
    var tglMulai = $('#input_sync_tanggal_mulai').val();
    var baseDate = tglMulai ? new Date(tglMulai + 'T00:00:00') : new Date();

    if (count === 0) {
      $('#sync_date_projection_text').html('<span style="color:#dc2626;"><i class="fa fa-exclamation-circle"></i> Belum ada kelas yang dipilih. Silakan centang minimal satu kelas di atas.</span>');
      $('#btn_execute_sync_jadwal').prop('disabled', true);
      $('#btn_execute_sync_text').text('Pilih Minimal 1 Kelas');
      return;
    }

    var projHtml = '<strong>Proyeksi Tanggal Pertemuan Pertama (P1):</strong><ul style="margin:4px 0 0 0; padding-left:18px;">';
    
    selectedCheckboxes.each(function() {
      var hariName = $(this).attr('data-hari') || 'Jadwal';
      var dayId = parseInt($(this).attr('data-dayid')) || 0;
      
      // Calculate first meeting date based on day ID (1=Senin ... 7=Minggu)
      var d = new Date(baseDate.getTime());
      if (dayId > 0) {
        var curDay = d.getDay(); // 0=Sun, 1=Mon ... 6=Sat
        var isoDay = (curDay === 0) ? 7 : curDay;
        var diff = dayId - isoDay;
        if (diff < 0) diff += 7;
        d.setDate(d.getDate() + diff);
      }
      
      var options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
      var dateStr = d.toLocaleDateString('id-ID', options);
      projHtml += '<li><strong>' + escapeHtml(hariName) + ':</strong> Pertemuan 1 = <span style="font-weight:700; color:#0369a1;">' + dateStr + '</span> (16 Pertemuan hingga akhir semester)</li>';
    });
    projHtml += '</ul>';

    $('#sync_date_projection_text').html(projHtml);
    $('#btn_execute_sync_jadwal').prop('disabled', false);

    if (count === 1) {
      $('#btn_execute_sync_text').text('Terapkan ke 1 Kelas Terpilih');
    } else {
      $('#btn_execute_sync_text').text('Terapkan ke ' + count + ' Kelas Sekaligus');
    }
  }

  /**
   * Eksekusi Sinkronisasi Skenario & BAP ke Jadwal Kelas Terpilih
   */
  function eksekusiSyncJadwal() {
    var selectedIds = [];
    $('#formSyncJadwal input[name="id_jadwal_list[]"]:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
      alert('Silakan pilih minimal 1 kelas perkuliahan terlebih dahulu.');
      return;
    }

    var tglMulai = $('#input_sync_tanggal_mulai').val();
    if (!tglMulai) {
      alert('Silakan tentukan tanggal mulai perkuliahan semester.');
      $('#input_sync_tanggal_mulai').focus();
      return;
    }

    var skenarioMd = (modeDocStore['skenario_pembelajaran'] && modeDocStore['skenario_pembelajaran'].markdown) ? modeDocStore['skenario_pembelajaran'].markdown : currentMarkdownOutput;
    var $btn = $('#btn_execute_sync_jadwal');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyinkronkan ke ' + selectedIds.length + ' Kelas...');

    var payload = {
      '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>',
      id_jadwal_list: selectedIds,
      id_rps: $('#id_rps_loaded').val() || '',
      tanggal_mulai: tglMulai,
      skenario_markdown: skenarioMd,
      matrix_json: currentMatrixJson ? JSON.stringify(currentMatrixJson) : '',
      overwrite: $('#check_sync_overwrite').is(':checked') ? 1 : 0
    };

    $.ajax({
      url: '<?= site_url("C_rps_generator/ajax_sync_to_jadwal_kelas"); ?>',
      type: 'POST',
      data: payload,
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false).html(origHtml);
        if (res.status) {
          $('#modalSyncJadwalKelas').modal('hide');
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              title: 'Sinkronisasi Berhasil!',
              html: res.msg + '<br><br><a href="<?= site_url("C_jadwal"); ?>" class="btn btn-sm btn-primary" style="margin-top:10px;"><i class="fa fa-list"></i> Buka Jadwal Mengajar</a>',
              icon: 'success',
              confirmButtonColor: '#0284c7',
              confirmButtonText: 'Tutup'
            });
          } else {
            alert(res.msg);
          }
        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              title: 'Gagal Sinkronisasi',
              text: res.msg,
              icon: 'error',
              confirmButtonColor: '#0284c7'
            });
          } else {
            alert('Gagal: ' + res.msg);
          }
        }
      },
      error: function(xhr, status, error) {
        $btn.prop('disabled', false).html(origHtml);
        alert('Terjadi kesalahan jaringan: ' + (xhr.responseText || error));
      }
    });
  }

  /**
   * Helper: Memetakan seluruh data RPS & dokumen turunannya dari database ke store tab studio
   */
  /**
   * Helper: Memetakan seluruh data RPS & dokumen turunannya dari database ke store tab studio
   */
  function populateModeDocStoreFromRps(r, details) {
    if (!r) return;

    // -1. Studio CPL & Deskripsi MK
    if (r.cpl_mk_json) {
      try {
        var parsedCplMk = JSON.parse(r.cpl_mk_json);
        if (parsedCplMk && parsedCplMk.items && Array.isArray(parsedCplMk.items)) {
          parsedCplMk = parsedCplMk.items;
        }
        if (Array.isArray(parsedCplMk) && parsedCplMk.length > 0) {
          activeCplMkList = parsedCplMk;
          activeCplMkStatus = r.cpl_mk_status || 'DRAFT';
          cplStudioFilter = 'selected';
        }
      } catch(e) {}
    } else {
      activeCplMkList = [];
      activeCplMkStatus = null;
      cplStudioFilter = 'all';
    }
    activeDeskripsiMk = r.deskripsi_mata_kuliah || '';
    activeDeskripsiMkSource = r.deskripsi_mk_source || 'CURRICULUM';
    activeReferensi = r.referensi || '';
    renderCplStudioUi();
    loadCplStudioData();

    // 0. CPMK Studio
    if (r.cpmk_json) {
      try {
        var parsedCpmk = JSON.parse(r.cpmk_json);
        if (Array.isArray(parsedCpmk) && parsedCpmk.length > 0) {
          activeCpmkList = parsedCpmk;
          activeCpmkStatus = r.cpmk_status || 'AI_PROPOSAL';
          renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
          renderCpmkCards();
        }
      } catch(e) {}
    } else {
      activeCpmkList = [];
      activeCpmkStatus = null;
      renderCoverageMatrix(null, studioCpmkData ? studioCpmkData.primary_cpls : [], studioCpmkData ? studioCpmkData.supporting_cpls : [], activeCpmkList);
      renderCpmkCards();
    }

    // 1. Susun RPS
    var mdRps = r.raw_ai_output || '';
    if (!mdRps || mdRps.trim() === '') {
      mdRps = '# RENCANA PEMBELAJARAN SEMESTER (RPS) OBE\n\n'
            + '### **' + (r.nm_mk || '') + ' (' + (r.kode_mk || '') + ')**\n\n'
            + '- **Bobot SKS:** ' + (r.sks_mk || '') + ' SKS (' + ((r.sks_mk || 0) * 45) + ' Jam)\n'
            + '- **Semester:** ' + (r.semester || '') + '\n'
            + '- **Dosen Pengampu:** ' + (r.dosen_pengampu || '') + '\n\n'
            + '## 1. Capaian Pembelajaran Lulusan (CPL)\n' + (r.cpl || '_Belum ada data CPL_') + '\n\n'
            + '## 2. Tujuan Belajar (CPMK & Sub-CPMK)\n' + (r.tujuan_belajar || '_Belum ada data tujuan belajar_') + '\n\n'
            + '## 3. Deskripsi Singkat Mata Kuliah\n' + (r.deskripsi_mata_kuliah || '_Belum ada deskripsi mata kuliah_') + '\n\n'
            + '## 4. Referensi & Pustaka\n' + (r.referensi || '_Belum ada data referensi_') + '\n\n';

      if (details && details.length > 0) {
        mdRps += '## 5. Rencana Pembelajaran 16 Pertemuan\n\n'
              + '| Mg | Temu | Kemampuan Akhir (Sub-CPMK) | Bahan Kajian | Bentuk & Metode Pembelajaran | Pengalaman Belajar | Waktu | Evaluasi & Bobot |\n'
              + '|:--:|:----:|:---------------------------|:-------------|:-----------------------------|:-------------------|:-----:|:-----------------|\n';
        details.forEach(function(d) {
          mdRps += '| ' + (d.kolom_1 || '') + ' | ' + (d.kolom_2 || '') + ' | ' 
                + (d.kolom_3 || '').replace(/\n/g, ' ') + ' | ' 
                + (d.kolom_4 || '').replace(/\n/g, ' ') + ' | ' 
                + (d.kolom_5 || '') + ' (' + (d.kolom_6 || '') + ') | ' 
                + (d.kolom_7 || '').replace(/\n/g, ' ') + ' | T:' + (d.kolom_8 || 0) + 'm P:' + (d.kolom_9 || 0) + 'm | ' 
                + (d.kolom_10 || '') + ' (' + (d.kolom_12 || 0) + '%) |\n';
        });
      }
    }

    var parsedRoadmap = null;
    if (r.roadmap_modul_json) {
      try {
        parsedRoadmap = JSON.parse(r.roadmap_modul_json);
      } catch(e) {}
    }

    var parsedMatrix = null;
    if (details && details.length > 0) {
      parsedMatrix = details.map(function(d) {
        return {
          minggu: d.kolom_1,
          pertemuan: d.kolom_2,
          sub_cpmk: d.kolom_3,
          bahan_kajian: d.kolom_4,
          bentuk_pembelajaran: d.kolom_5,
          metode: d.kolom_6,
          pengalaman_belajar: d.kolom_7,
          waktu_teori: d.kolom_8,
          waktu_praktek: d.kolom_9,
          kriteria_evaluasi: d.kolom_10,
          indikator_evaluasi: d.kolom_11,
          bobot: d.kolom_12,
          sumber_belajar: d.kolom_13
        };
      });
    }

    if (mdRps && mdRps.trim() !== '') {
      modeDocStore['susun_rps'] = {
        markdown: mdRps,
        html: renderMarkdownSafe(mdRps),
        matrixJson: parsedMatrix,
        roadmapJson: parsedRoadmap
      };
    }

    // 2. Roadmap WBI
    if (r.roadmap_wbi_markdown && r.roadmap_wbi_markdown.trim() !== '') {
      var rawMdRoadmap = r.roadmap_wbi_markdown;
      var cleanRm = extractAndCleanRoadmapJson(rawMdRoadmap);
      var rmJson = parsedRoadmap || cleanRm.json;

      var rmHtml = '';
      if (rmJson) {
        rmHtml += renderRoadmapVisualGrid(rmJson, r.nm_mk);
      }
      rmHtml += renderMarkdownSafe(cleanRm.cleanMarkdown);

      modeDocStore['roadmap_wbi'] = {
        markdown: cleanRm.cleanMarkdown,
        html: rmHtml,
        matrixJson: null,
        roadmapJson: rmJson
      };
    }

    // 3. Kontrak Kuliah
    var mdKontrak = r.kontrak_kuliah_markdown || '';
    if (!mdKontrak || mdKontrak.trim() === '') {
      var kontrakEval = r.kontrak_kriteria_penilaian || '';
      if (!kontrakEval && r.tujuan_belajar && r.tujuan_belajar.indexOf('|') !== -1) {
        kontrakEval = r.tujuan_belajar;
      }
      if (!kontrakEval && r.raw_ai_output) {
        var mEval = r.raw_ai_output.match(/(?:[#*]{1,4}\s*)?(?:\d+[\.\)]\s*)?(?:TUJUAN BELAJAR|CPMK|MATRIKS KORELASI CPMK|BLUEPRINT ASESMEN)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:\d+[\.\)]\s*)?(?:DESKRIPSI|REFERENSI|DETAIL RENCANA))|\Z)/i);
        if (mEval && mEval[1]) kontrakEval = mEval[1].trim();
      }

      mdKontrak = "# KONTRAK PEMBELAJARAN\n\n"
        + "### **" + (r.nm_mk || '') + (r.kode_mk ? " (" + r.kode_mk + ")" : "") + "**\n\n"
        + "- **Program Studi:** " + ($('#mk_prodi').val() || 'Politeknik Wilmar Bisnis Indonesia') + "\n"
        + "- **Bobot SKS:** " + (r.sks_mk || '') + " SKS\n"
        + "- **Semester:** " + (r.semester || '') + "\n"
        + "- **Dosen Pengampu:** " + (r.dosen_pengampu || '') + "\n\n"
        + "## 1. DESKRIPSI MATA KULIAH\n" + (r.deskripsi_mata_kuliah || '-') + "\n\n"
        + "## 2. MANFAAT MATA KULIAH\n" + (r.manfaat_mata_kuliah || 'Mata kuliah ini membekali mahasiswa dengan penguasaan kompetensi analitis dan praktis yang esensial, mendukung profil lulusan Program Studi, serta membentuk kesiapan profesional di dunia industri dan bisnis.') + "\n\n"
        + "## 3. CP MATA KULIAH (CPMK)\n" + (r.cp_mata_kuliah || r.tujuan_belajar || '-') + "\n\n"
        + "## 4. STRATEGI PERKULIAHAN\n" + (r.strategi_perkuliahan || 'Perkuliahan diselenggarakan dengan pendekatan Student-Centered Learning (SCL) yang mengintegrasikan metode Case Method, Project-Based Learning, diskusi interaktif, dan simulasi praktikum.') + "\n\n"
        + "## 5. MATERI / BAHAN BACAAN\n" + (r.materi_bahan_bacaan || r.referensi || '-') + "\n\n"
        + "## 6. TUGAS-TUGAS\n" + (r.tugas_tugas || 'Tugas terstruktur terdiri dari analisis studi kasus industri, tugas kelompok mini proyek, kuis berkala, dan evaluasi hasil belajar mandiri.') + "\n\n"
        + "## 7. KRITERIA & BOBOT PENILAIAN\n" + (kontrakEval || 'Penilaian mengacu pada Matriks Blueprint Asesmen RPS OBE dengan total bobot 100%.') + "\n\n"
        + "## 8. TATA TERTIB & KETENTUAN PERKULIAHAN\n" + (r.tata_kelola_perkuliahan || '- Kehadiran minimal mahasiswa adalah 75% dari total tatap muka untuk berhak mengikuti Ujian Akhir Semester (UAS).\n- Toleransi keterlambatan maksimal 15 menit.\n- Berpakaian rapi, sopan, dan mematuhi etika akademik Politeknik Wilmar Bisnis Indonesia.\n- Plagiarisme atau kecurangan dalam ujian/tugas berakibat sanksi nilai E.') + "\n";
    }

    modeDocStore['kontrak_kuliah'] = {
      markdown: mdKontrak,
      html: renderMarkdownSafe(mdKontrak),
      matrixJson: null,
      roadmapJson: null
    };

    // 3. Rancangan Tugas (HANYA dimuat jika data naskah tersimpan di database; JANGAN diisi otomatis sebelum tombol Generate ditekan)
    if (r.rancangan_tugas_markdown && r.rancangan_tugas_markdown.trim() !== '') {
      modeDocStore['rancangan_tugas'] = {
        markdown: r.rancangan_tugas_markdown,
        html: renderMarkdownSafe(r.rancangan_tugas_markdown),
        matrixJson: null,
        roadmapJson: null
      };
    } else {
      modeDocStore['rancangan_tugas'] = null;
    }

    // 4. Skenario Pembelajaran & BAP
    var mdSkenario = r.skenario_pembelajaran_markdown || '';
    if (mdSkenario && mdSkenario.trim() !== '') {
      modeDocStore['skenario_pembelajaran'] = {
        markdown: mdSkenario,
        html: renderMarkdownSafe(mdSkenario),
        matrixJson: null,
        roadmapJson: null
      };
    }
  }

  /**
   * Helper: Menyelaraskan Kontrak Kuliah secara otomatis saat Dokumen RPS diperbarui/diprompting
   */
  function syncKontrakKuliahFromRps(rpsMd) {
    if (!rpsMd) return;

    var assessmentTable = '';
    var matchAsesmen = rpsMd.match(/(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:TUJUAN BELAJAR|CPMK|MATRIKS KORELASI CPMK|BLUEPRINT ASESMEN)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DESKRIPSI|REFERENSI|DAFTAR PUSTAKA|DETAIL RENCANA))|\Z)/i);
    if (matchAsesmen && matchAsesmen[1]) {
      var rawAsesmen = matchAsesmen[1].trim();
      if (rawAsesmen.indexOf('|') !== -1) {
        assessmentTable = rawAsesmen;
      }
    }

    var matchDeskripsi = rpsMd.match(/(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DESKRIPSI SINGKAT|DESKRIPSI MATA KULIAH|DESKRIPSI)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA|DETAIL RENCANA|MATRIKS|BLUEPRINT))|\Z)/i);
    var deskripsi = matchDeskripsi ? matchDeskripsi[1].trim() : '';
    if (deskripsi) {
      deskripsi = deskripsi.replace(/(?:\n+|\A)(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA|DETAIL RENCANA|MATRIKS|BLUEPRINT)[\s\S]*$/i, '').trim();
    }

    var matchRef = rpsMd.match(/(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DAFTAR REFERENSI|REFERENSI|DAFTAR PUSTAKA|PUSTAKA)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DETAIL RENCANA|RENCANA PEMBELAJARAN|TABEL|MATRIKS|DISTRIBUSI|DESKRIPSI))|\Z)/i);
    var referensi = matchRef ? matchRef[1].trim() : '';
    if (referensi) {
      referensi = referensi.replace(/(?:\n+|\A)(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DETAIL RENCANA|RENCANA PEMBELAJARAN|TABEL|MATRIKS|DISTRIBUSI|DESKRIPSI|BLUEPRINT|PENILAIAN)[\s\S]*$/i, '');
      referensi = referensi.replace(/(?:\n+|\A)\s*\|{1,2}(?:\s*Minggu|\s*Temu|\s*No|\s*1\s*\||\s*Sub-CPMK|\s*CPMK|\s*Bentuk|\s*Kriteria|\s*ETS|\s*UTS|\s*EAS|\s*UAS)[\s\S]*$/i, '').trim();
    }

    var mkNama = $('#mk_nama').val() || 'Mata Kuliah';
    var mkKode = $('#mk_kode').val() || '';
    var mkSks = $('#mk_sks').val() || '';
    var mkSemester = $('#mk_semester').val() || '';
    var mkDosen = $('#mk_dosen').val() || '';
    var mkProdi = $('#mk_prodi').val() || 'Politeknik Wilmar Bisnis Indonesia';

    var skalaNilaiTable = "\n\n### Skala Nilai Huruf Politeknik Wilmar Bisnis Indonesia\n\n"
      + "| Nilai Angka | Nilai Huruf | Bobot | Kategori |\n"
      + "|---|---|---|---|\n"
      + "| >= 85 | A | 4.00 | Istimewa |\n"
      + "| 75 - 84 | B+ | 3.50 | Sangat Baik |\n"
      + "| 70 - 74 | B | 3.00 | Baik |\n"
      + "| 65 - 69 | C+ | 2.50 | Cukup Baik |\n"
      + "| 55 - 64 | C | 2.00 | Cukup |\n"
      + "| 45 - 54 | D | 1.00 | Kurang |\n"
      + "| < 45 | E | 0.00 | Gagal |";

    var fullAssessmentSection = (assessmentTable ? assessmentTable : "Penilaian mengacu pada Matriks Blueprint Asesmen RPS OBE dengan total bobot 100%.");
    if (fullAssessmentSection.indexOf('Skala Nilai Huruf') === -1) {
      fullAssessmentSection += skalaNilaiTable;
    }

    if (modeDocStore['kontrak_kuliah'] && modeDocStore['kontrak_kuliah'].markdown) {
      var oldKontrak = modeDocStore['kontrak_kuliah'].markdown;
      var updatedKontrak = oldKontrak;
      if (assessmentTable) {
        if (oldKontrak.match(/##\s*\d*\.?\s*KRITERIA[^\n]*/i)) {
          updatedKontrak = oldKontrak.replace(/(##\s*\d*\.?\s*KRITERIA[^\n]*\n+)([\s\S]*?)(?=\n+##\s*\d*\.|\Z)/i, '$1' + fullAssessmentSection + '\n\n');
        } else {
          updatedKontrak += '\n\n## 7. KRITERIA & BOBOT PENILAIAN\n' + fullAssessmentSection;
        }
      }
      modeDocStore['kontrak_kuliah'].markdown = updatedKontrak;
      modeDocStore['kontrak_kuliah'].html = renderMarkdownSafe(updatedKontrak);
    } else {
      var newKontrak = "# KONTRAK PEMBELAJARAN\n\n"
        + "### **" + mkNama + (mkKode ? " (" + mkKode + ")" : "") + "**\n\n"
        + "- **Program Studi:** " + mkProdi + "\n"
        + "- **Bobot SKS:** " + mkSks + " SKS\n"
        + "- **Semester:** " + mkSemester + "\n"
        + "- **Dosen Pengampu:** " + mkDosen + "\n\n"
        + "## 1. DESKRIPSI MATA KULIAH\n" + (deskripsi || '-') + "\n\n"
        + "## 2. MANFAAT MATA KULIAH\nMata kuliah ini membekali mahasiswa dengan penguasaan kompetensi analitis dan praktis yang esensial, mendukung profil lulusan Program Studi, serta membentuk kesiapan profesional di dunia industri dan bisnis.\n\n"
        + "## 3. CP MATA KULIAH (CPMK)\n" + (deskripsi || '-') + "\n\n"
        + "## 4. STRATEGI PERKULIAHAN\nPerkuliahan diselenggarakan dengan pendekatan Student-Centered Learning (SCL) yang mengintegrasikan metode Case Method, Project-Based Learning, diskusi interaktif, dan simulasi praktikum.\n\n"
        + "## 5. MATERI / BAHAN BACAAN\n" + (referensi || '-') + "\n\n"
        + "## 6. TUGAS-TUGAS\nTugas terstruktur terdiri dari analisis studi kasus industri, tugas kelompok mini proyek, kuis berkala, dan evaluasi hasil belajar mandiri.\n\n"
        + "## 7. KRITERIA & BOBOT PENILAIAN\n" + fullAssessmentSection + "\n\n"
        + "## 8. TATA TERTIB & KETENTUAN PERKULIAHAN\n- Kehadiran minimal mahasiswa adalah 75% dari total tatap muka untuk berhak mengikuti Ujian Akhir Semester (UAS).\n- Toleransi keterlambatan maksimal 15 menit.\n- Berpakaian rapi, sopan, dan mematuhi etika akademik Politeknik Wilmar Bisnis Indonesia.\n- Plagiarisme atau kecurangan dalam ujian/tugas berakibat sanksi nilai E.\n";

      modeDocStore['kontrak_kuliah'] = {
        markdown: newKontrak,
        html: renderMarkdownSafe(newKontrak),
        matrixJson: null,
        roadmapJson: null
      };
    }
  }

  /**
   * Helper: Mengekstrak objek Roadmap JSON dan membersihkannya dari naskah teks markdown
   */
  function extractAndCleanRoadmapJson(rawText) {
    if (!rawText) return { json: null, cleanMarkdown: '' };
    var parsedJson = null;
    var cleaned = rawText;

    // 1. Cocokkan di dalam fenced block ```json_roadmap ... ``` atau ```json ... ``` atau ``` ... ```
    var matchFenced = rawText.match(/```(?:json_roadmap|json)?\s*(\{[\s\S]*?"modules"[\s\S]*?\})\s*```/i);
    if (matchFenced && matchFenced[1]) {
      try {
        parsedJson = JSON.parse(matchFenced[1]);
        cleaned = cleaned.replace(/```(?:json_roadmap|json)?\s*\{[\s\S]*?"modules"[\s\S]*?\}\s*```/gi, '');
      } catch(e) {}
    }

    // 2. Cocokkan raw JSON block di akhir atau dalam teks
    if (!parsedJson) {
      var matchRaw = rawText.match(/(\{[\s\S]*?"learning_outcome"[\s\S]*?"modules"[\s\S]*?\})\s*$/i)
                  || rawText.match(/(\{[\s\S]*?"modules"\s*:\s*\[[\s\S]*?\][\s\S]*?\})\s*$/i);
      if (matchRaw && matchRaw[1]) {
        try {
          parsedJson = JSON.parse(matchRaw[1]);
          cleaned = cleaned.replace(matchRaw[1], '');
        } catch(e) {}
      }
    } else {
      cleaned = cleaned.replace(/\{[\s\S]*?"learning_outcome"[\s\S]*?"modules"[\s\S]*?\}\s*$/gi, '');
    }

    // 3. Bersihkan triple backticks sisa
    cleaned = cleaned.replace(/```(?:json_roadmap|json)?\s*```/gi, '').trim();

    return {
      json: parsedJson,
      cleanMarkdown: cleaned
    };
  }

  /**
   * Helper: Merender Visual Grid Roadmap WBI (16 Pertemuan) yang cantik dan responsif
   */
  function renderRoadmapVisualGrid(roadmapData, namaMk) {
    if (!roadmapData || !roadmapData.modules || !Array.isArray(roadmapData.modules) || roadmapData.modules.length === 0) return '';

    var palette = ['#dbeafe', '#dcfce7', '#ffedd5', '#f3e8ff', '#cffafe', '#ffe4e6', '#e0e7ff'];
    var colorUtsUas = '#fef08a';

    var learningOutcome = roadmapData.learning_outcome || ('ROADMAP PEMBELAJARAN ' + (namaMk || ''));
    var modules = roadmapData.modules;

    var html = '<div class="roadmap-visual-box" style="margin-bottom:24px; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.05);">';
    html += '<div style="background:#0f172a; color:#ffffff; padding:10px 16px; display:flex; align-items:center; justify-content:space-between;">'
          + '<div style="font-weight:700; font-size:13px; letter-spacing:0.5px;"><i class="fa fa-th-large" style="color:#38bdf8; margin-right:8px;"></i>VISUAL GRID ROADMAP WBI (16 PERTEMUAN)</div>'
          + '<div style="font-size:11px; color:#94a3b8;"><i class="fa fa-file-excel-o" style="color:#22c55e;"></i> Siap diekspor ke format Excel resmi WBI</div>'
          + '</div>';
    html += '<div style="overflow-x:auto; padding:12px;">';
    html += '<table style="width:100%; border-collapse:collapse; font-size:11px; table-layout:fixed; min-width:980px; border:1px solid #94a3b8;">';

    // Baris 1: Learning Outcome Header
    html += '<tr>'
          + '<th style="width:130px; background:#e2e8f0; border:1px solid #94a3b8; padding:8px; font-weight:bold; text-align:left; vertical-align:middle;">LEARNING OUTCOME</th>'
          + '<td colspan="16" style="background:#f1f5f9; border:1px solid #94a3b8; padding:8px 12px; font-weight:bold; color:#0f172a; text-align:center; vertical-align:middle; font-size:12px;">' + escapeHtml(learningOutcome) + '</td>'
          + '<td rowspan="7" style="width:36px; background:#e2e8f0; border:1px solid #94a3b8; padding:4px; text-align:center; vertical-align:middle; writing-mode:vertical-rl; transform:rotate(180deg); font-weight:bold; font-size:11px; color:#334155;">' + escapeHtml(namaMk || 'MATA KULIAH') + '</td>'
          + '</tr>';

    var categories = [
      { key: 'module_name', label: 'MODULE', default: 'Modul', bold: true, minHeight: '34px' },
      { key: 'output', label: 'OUTPUT', default: '-', bold: false, minHeight: '60px' },
      { key: 'concept', label: 'CONCEPT', default: '-', bold: false, minHeight: '60px' },
      { key: 'indicators', label: 'INDICATORS', default: '-', bold: false, minHeight: '60px' },
      { key: 'strategy', label: 'LEARNING STRATEGY', default: '-', bold: false, minHeight: '45px' }
    ];

    categories.forEach(function(cat) {
      html += '<tr>';
      html += '<th style="background:#e2e8f0; border:1px solid #94a3b8; padding:6px 8px; font-weight:bold; text-align:left; vertical-align:top;">' + cat.label + '</th>';

      var colorIdx = 0;
      modules.forEach(function(mod) {
        var startM = Math.max(1, Math.min(16, parseInt(mod.start_meeting) || 1));
        var endM = Math.max(1, Math.min(16, parseInt(mod.end_meeting) || startM));
        if (endM < startM) endM = startM;
        var span = (endM - startM + 1);

        var isUtsUas = (startM === 8 && endM === 8) || (startM === 16 && endM === 16) || (mod.module_name && (mod.module_name.indexOf('UTS') !== -1 || mod.module_name.indexOf('UAS') !== -1));
        var bgColor = isUtsUas ? colorUtsUas : (palette[colorIdx % palette.length]);
        if (!isUtsUas) colorIdx++;

        var cellVal = mod[cat.key] || cat.default;
        var style = 'background:' + bgColor + '; border:1px solid #94a3b8; padding:6px 8px; vertical-align:top; font-size:10.5px; line-height:1.4; min-height:' + cat.minHeight + ';';
        if (cat.bold) style += ' font-weight:bold; text-align:center;';

        html += '<td colspan="' + span + '" style="' + style + '">' + escapeHtml(cellVal).replace(/\n/g, '<br>') + '</td>';
      });

      html += '</tr>';
    });

    // Baris Terakhir: Meeting 1 s.d 16
    html += '<tr>';
    html += '<th style="background:#e2e8f0; border:1px solid #94a3b8; padding:6px 8px; font-weight:bold; text-align:left; vertical-align:middle;">MEETING</th>';
    for (var m = 1; m <= 16; m++) {
      var lbl = m;
      var bg = '#f8fafc';
      if (m === 8) { lbl = '8 (UTS)'; bg = colorUtsUas; }
      if (m === 16) { lbl = '16 (UAS)'; bg = colorUtsUas; }
      html += '<td style="background:' + bg + '; border:1px solid #94a3b8; padding:6px 2px; text-align:center; font-weight:bold; font-size:10px;">' + lbl + '</td>';
    }
    html += '</tr>';

    html += '</table>';
    html += '</div>';
    html += '</div>';

    return html;
  }

  // Load awal, Inisialisasi Select2 AJAX MK, dan handle query param load_rps_obe
  // Load awal, Inisialisasi Select2 AJAX MK, dan handle query param load_rps_obe
  $(document).ready(function() {
    // Inisialisasi tab aktif awal ke Studio CPL
    switchTab('studio_cpl');

    // Auto-clean saat user mengedit atau mem-paste ke textarea deskripsi & referensi
    $('#studio_deskripsi_mk').on('change blur', function() {
      var val = cleanDeskripsiText($(this).val());
      $(this).val(val);
      activeDeskripsiMk = val;
    });

    $('#studio_referensi_mk').on('change blur', function() {
      var val = cleanReferensiText($(this).val());
      $(this).val(val);
      activeReferensi = val;
    });

    // Inisialisasi Select2 Server-Side AJAX untuk Pencarian Mata Kuliah (min. 3 karakter)
    if ($.fn.select2) {
      $('#select_mk_diampu').select2({
        placeholder: '🔍 Ketik min. 3 huruf nama atau kode MK...',
        allowClear: true,
        minimumInputLength: 3,
        quietMillis: 250,
        ajax: {
          url: '<?= site_url("C_rps_generator/ajax_search_mata_kuliah"); ?>',
          dataType: 'json',
          type: 'GET',
          data: function (term, page) {
            return {
              q: term
            };
          },
          results: function (data, page) {
            return {
              results: data.results || []
            };
          },
          cache: true
        },
        formatResult: function(item) {
          var sksText = item.sks ? ' <span class="rps-mk-sks">' + item.sks + ' SKS</span>' : '';
          var diampuBadge = item.diampu ? ' <span class="rps-mk-diampu"><i class="fa fa-check mr-1"></i>Diampu</span>' : '';
          var prodiLine = item.nm_prodi ? '<div class="rps-mk-prodi"><i class="fa fa-graduation-cap"></i> ' + escapeHtml(item.nm_prodi) + '</div>' : '';
          return '<div class="rps-mk-item">' +
                 '<span class="rps-mk-title">[' + escapeHtml(item.kode_mk || '-') + '] ' + escapeHtml(item.nm_mk) + '</span>' +
                 sksText + diampuBadge +
                 prodiLine +
                 '</div>';
        },
        formatSelection: function(item) {
          if (!item) return '';
          if (item.text && (!item.kode_mk && !item.nm_mk)) return item.text;
          var sksText = item.sks ? ' (' + item.sks + ' SKS)' : '';
          var prodiText = item.nm_prodi ? ' — ' + item.nm_prodi : '';
          return '[' + (item.kode_mk || '-') + '] ' + item.nm_mk + sksText + prodiText;
        },
        formatInputTooShort: function (input, min) {
          var n = min - input.length;
          return 'Ketik minimal ' + n + ' karakter lagi untuk mencari...';
        },
        formatNoMatches: function () {
          return 'Tidak ditemukan mata kuliah yang cocok.';
        },
        formatSearching: function () {
          return 'Mencari di database mata kuliah...';
        },
        escapeMarkup: function (m) { return m; }
      }).on('change', function(e) {
        var selectedData = e.added || $('#select_mk_diampu').select2('data');
        if (selectedData && (selectedData.nm_mk || selectedData.kode_mk)) {
          pilihMkObject(selectedData);
        } else {
          pilihMkObject(null);
        }
      });
    }

    var loadId = $('#id_rps_loaded').val();
    if (loadId) {
      $.ajax({
        url: '<?= site_url("C_rps_generator/ajax_load_rps_obe"); ?>',
        type: 'POST',
        data: {
          id_rps: loadId,
          '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(res) {
          if (res.status && res.rps) {
            var r = res.rps;
            var prodi = r.nama_prodi || r.prodi || '';

            // Set data pada Select2 AJAX
            if ($.fn.select2) {
              var displayText = '[' + (r.kode_mk || '-') + '] ' + r.nm_mk + (r.sks_mk ? ' (' + r.sks_mk + ' SKS)' : '') + (prodi ? ' — ' + prodi : '');
              $('#select_mk_diampu').select2('data', {
                id: r.id_mk || r.kode_mk || r.nm_mk,
                text: displayText,
                id_mk: r.id_mk,
                kode_mk: r.kode_mk,
                nm_mk: r.nm_mk,
                sks: r.sks_mk,
                nm_prodi: prodi
              });
            }

            $('#mk_nama').val(r.nm_mk);
            $('#mk_kode').val(r.kode_mk);
            $('#mk_sks').val(r.sks_mk);
            $('#mk_sks_display').val(r.sks_mk + ' SKS (' + (r.sks_mk * 45) + ' Jam)');
            if (r.semester) {
              setSemesterValue(r.semester);
            }
            $('#mk_dosen').val(r.dosen_pengampu);

            if (prodi) {
              $('#mk_prodi').val(prodi);
              checkPustakaProdi(prodi);
            }

            // Jalankan pengecekan kesiapan kurikulum secara otomatis
            checkCurriculumReadiness();

            populateModeDocStoreFromRps(r, res.details);

            // Navigasi cerdas ke tab berikutnya sesuai progres RPS
            var isCplApproved = (r.cpl_mk_status === 'APPROVED');
            var isCpmkApproved = (r.cpmk_status === 'APPROVED');
            var hasCpmk = (activeCpmkList && activeCpmkList.length > 0);
            var hasRpsMaster = (r.raw_ai_output && r.raw_ai_output.trim().length > 0);

            if (!isCplApproved) {
              switchTab('studio_cpl');
            } else if (hasCpmk && !isCpmkApproved) {
              switchTab('susun_cpmk');
            } else if (isCpmkApproved && hasRpsMaster) {
              switchTab('susun_rps');
            } else if (hasCpmk) {
              switchTab('susun_cpmk');
            } else {
              switchTab('studio_cpl');
            }
          }
        }
      });
    } else {
      // Periksa apakah browser me-restore nilai mata kuliah saat halaman di-refresh
      setTimeout(function() {
        var currentNamaMk = $.trim($('#mk_nama').val());
        var currentKodeMk = $.trim($('#mk_kode').val());
        var currentProdi  = $.trim($('#mk_prodi').val());
        var currentSks    = parseInt($('#mk_sks').val()) || 0;

        if (currentNamaMk || currentKodeMk) {
          if (currentSks > 0) {
            $('#mk_sks_display').val(currentSks + ' SKS (' + (currentSks * 45) + ' Jam)');
          }
          if (currentProdi) {
            checkPustakaProdi(currentProdi);
          }
          checkCurriculumReadiness();

          // Cek data RPS tersimpan untuk MK ini di database
          $.ajax({
            url: '<?= site_url("C_rps_generator/ajax_check_existing_rps_mk"); ?>',
            type: 'POST',
            data: {
              kode_mk: currentKodeMk,
              nm_mk: currentNamaMk,
              '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(res) {
              if (res.status && res.found && res.rps) {
                $('#id_rps_loaded').val(res.rps.id_rps);
                if (res.rps.semester) {
                  setSemesterValue(res.rps.semester);
                }
                var rProdi = res.rps.nama_prodi || res.rps.prodi || currentProdi;
                if (rProdi) {
                  $('#mk_prodi').val(rProdi);
                  checkPustakaProdi(rProdi);
                }
                populateModeDocStoreFromRps(res.rps, res.details);

                var isCplApproved = (res.rps.cpl_mk_status === 'APPROVED');
                var isCpmkApproved = (res.rps.cpmk_status === 'APPROVED');
                var hasCpmk = (activeCpmkList && activeCpmkList.length > 0);
                var hasRpsMaster = (res.rps.raw_ai_output && res.rps.raw_ai_output.trim().length > 0);

                if (!isCplApproved) {
                  switchTab('studio_cpl');
                } else if (hasCpmk && !isCpmkApproved) {
                  switchTab('susun_cpmk');
                } else if (isCpmkApproved && hasRpsMaster) {
                  switchTab('susun_rps');
                } else if (hasCpmk) {
                  switchTab('susun_cpmk');
                } else {
                  switchTab('studio_cpl');
                }
              } else {
                switchTab('studio_cpl');
                if (cplStudioAllProdiList.length === 0) {
                  loadCplStudioData();
                }
              }
            },
            error: function() {
              switchTab('studio_cpl');
            }
          });
        } else {
          switchTab('studio_cpl');
        }
      }, 60);
    }
  });
</script>
