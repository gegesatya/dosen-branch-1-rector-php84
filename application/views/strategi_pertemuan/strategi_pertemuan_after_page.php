<style>
  .btn-action-edit {
    background: #2563eb !important;
    border-color: #1d4ed8 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
    border-radius: 5px !important;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25) !important;
    transition: all 0.15s ease-in-out !important;
  }
  .btn-action-edit:hover, .btn-action-edit:focus {
    background: #1d4ed8 !important;
    border-color: #1e40af !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(29, 78, 216, 0.35) !important;
    transform: translateY(-1px);
  }
  .form-section-box {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 14px 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .form-section-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .scl-chip {
    cursor: pointer;
    font-size: 11.5px !important;
    border-radius: 14px !important;
    padding: 4px 11px !important;
    margin: 2px !important;
    transition: all 0.15s ease !important;
    outline: none !important;
    line-height: 1.4 !important;
  }
  .scl-chip-inactive {
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
  }
  .scl-chip-inactive:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #94a3b8 !important;
  }
  .scl-chip-active {
    background: #1e40af !important;
    color: #ffffff !important;
    border: 1px solid #1e40af !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 3px rgba(30,64,175,0.3) !important;
  }
  .bap-tindak-chip {
    cursor: pointer;
    font-size: 11.5px !important;
    border-radius: 14px !important;
    padding: 3px 10px !important;
    margin: 2px !important;
    transition: all 0.15s ease !important;
    outline: none !important;
    line-height: 1.4 !important;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
  }
  .bap-tindak-chip:hover {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fde68a !important;
  }
  .bap-tindak-chip.active {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #b45309 !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 3px rgba(217,119,6,0.3) !important;
  }
  .bap-kendala-chip {
    cursor: pointer;
    font-size: 11.5px !important;
    border-radius: 14px !important;
    padding: 3px 10px !important;
    margin: 2px !important;
    transition: all 0.15s ease !important;
    outline: none !important;
    line-height: 1.4 !important;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
  }
  .bap-kendala-chip:hover {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #fca5a5 !important;
  }
  .bap-kendala-chip.active {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #b91c1c !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 3px rgba(220,38,38,0.3) !important;
  }
  .bap-kendala-chip.active[data-val="Tidak ada kendala"] {
    background: #16a34a !important;
    border-color: #15803d !important;
    box-shadow: 0 1px 3px rgba(22,163,74,0.3) !important;
  }
  .bap-textarea-resizable {
    resize: vertical !important;
    min-height: 75px;
    font-size: 12.5px !important;
    line-height: 1.5 !important;
  }
  .bap-kesesuaian-chip {
    cursor: pointer;
    font-size: 12px !important;
    border-radius: 6px !important;
    padding: 5px 12px !important;
    margin: 2px !important;
    transition: all 0.15s ease !important;
    outline: none !important;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
    font-weight: 600 !important;
  }
  .bap-kesesuaian-chip:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
  }
  .bap-kesesuaian-chip.active[data-val="Sesuai RPS"] {
    background: #15803d !important;
    color: #ffffff !important;
    border-color: #166534 !important;
    box-shadow: 0 1px 4px rgba(22,101,52,0.3) !important;
  }
  .bap-kesesuaian-chip.active[data-val="Sebagian besar sesuai"] {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #b45309 !important;
    box-shadow: 0 1px 4px rgba(217,119,6,0.3) !important;
  }
  .bap-kesesuaian-chip.active[data-val="Tidak sesuai"] {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #b91c1c !important;
    box-shadow: 0 1px 4px rgba(220,38,38,0.3) !important;
  }
  .subcpmk-container {
    max-height: 230px;
    overflow-y: auto;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px;
    background: #f8fafc;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
  }
  .subcpmk-container::-webkit-scrollbar {
    width: 6px;
  }
  .subcpmk-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
  }
  .subcpmk-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }
  .subcpmk-item {
    cursor: pointer;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 12px;
    margin-bottom: 7px;
    background: #ffffff;
    transition: all 0.18s ease;
    display: flex;
    align-items: flex-start;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
  }
  .subcpmk-item:last-child {
    margin-bottom: 0;
  }
  .subcpmk-item:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
  }
  .subcpmk-item.active {
    background: #eff6ff !important;
    border: 1.5px solid #2563eb !important;
    box-shadow: 0 2px 6px rgba(37,99,235,0.12) !important;
  }
  .subcpmk-item input[type="checkbox"] {
    cursor: pointer;
    width: 16px;
    height: 16px;
    margin-top: 3px;
    margin-right: 10px;
    accent-color: #2563eb;
    flex-shrink: 0;
  }
  .subcpmk-badge-code {
    background: #1e40af !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    padding: 3px 8px !important;
    border-radius: 4px !important;
    display: inline-block !important;
    letter-spacing: 0.3px !important;
    box-shadow: 0 1px 2px rgba(30,64,175,0.2) !important;
  }
  .subcpmk-badge-parent {
    background: #e2e8f0 !important;
    color: #334155 !important;
    font-weight: 600 !important;
    font-size: 10.5px !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    border: 1px solid #cbd5e1 !important;
    display: inline-block !important;
    margin-left: 5px !important;
  }
  .subcpmk-desc-text {
    font-size: 12.5px;
    color: #334155;
    line-height: 1.5;
    margin-top: 4px;
    transition: color 0.15s ease;
  }
  .subcpmk-item.active .subcpmk-desc-text {
    color: #0f172a;
    font-weight: 600;
  }
</style>

<script type="text/javascript">
  var save_method;
  var table;
  var TableAjax = function() {
    var handleRecords = function() {
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagination": true,
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_strategi_pertemuan/ajax_list/' . $id_jadwal) ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },
        "columnDefs": [{
          "targets": [0, 1, 2, 3],
          "orderable": false,
        }],
      });
    }

    return {
      init: function() {
        handleRecords();
      }
    };
  }();

  $(document).ready(function() {
    TableAjax.init();

    $(".tgl_realisasi").hide();

    $('#tanggal_realisasi').bootstrapMaterialDatePicker({
      weekStart: 0,
      time: false
    });

    $('#tanggal_rencana').bootstrapMaterialDatePicker({
      weekStart: 0,
      time: false
    });

    $('#jam_mulai, #jam_selesai').bootstrapMaterialDatePicker({
      date: false,
      shortTime: false,
      format: 'HH:mm'
    });

    $("input, textarea, select").change(function() {
      $(this).closest('.form-group').removeClass('has-error');
      $(this).closest('.form-group').find('.help-block').empty();
    });

    // Toggle edit for Sub-CPMK
    $('#btnToggleEditSubcpmk').on('click', function(e) {
      e.preventDefault();
      $('#sub_cpmk_edit_wrap').slideToggle(150, function() {
        if ($('#sub_cpmk_edit_wrap').is(':visible')) {
          $('#btnToggleEditSubcpmk').html('<i class="fa fa-eye mr-1"></i> Tutup Editor');
          $('#sub_cpmk').focus();
        } else {
          $('#btnToggleEditSubcpmk').html('<i class="fa fa-pencil mr-1"></i> Edit Teks');
        }
      });
    });



    // Toggle edit for Draf BAP
    $('#btnToggleEditBap').on('click', function(e) {
      e.preventDefault();
      $('#draf_bap_edit_wrap').slideToggle(150, function() {
        if ($('#draf_bap_edit_wrap').is(':visible')) {
          $('#btnToggleEditBap').html('<i class="fa fa-eye mr-1"></i> Tutup Editor');
          $('#bap_materi').focus();
        } else {
          $('#btnToggleEditBap').html('<i class="fa fa-pencil mr-1"></i> Edit Evaluasi');
        }
      });
    });

    // Live update preview
    $('#berita_acara_rencana').on('input', function() {
      renderSkenarioCards($(this).val());
    });

    $('#draf_bap').on('input', function() {
      renderBapPreview($(this).val());
    });
  });

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function edit_rencana(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
      url: "<?php echo site_url('C_strategi_pertemuan/ajax_edit/') ?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="uniq"]').val(data.uniq);
        $('[name="tanggal_rencana"]').val(data.tanggal_rencana);
        $('[name="pertemuan"]').val(data.pertemuan);
        $('[name="jam_mulai"]').val(data.jam_mulai ? data.jam_mulai.substring(0, 5) : '');
        $('[name="jam_selesai"]').val(data.jam_selesai ? data.jam_selesai.substring(0, 5) : '');

        var rawT = data.jam_teori ? Number(data.jam_teori) : 1;
        var rawP = data.jam_praktek ? Number(data.jam_praktek) : 2;
        var menitTeori = rawT <= 10 ? Math.round(rawT * 50) : (rawT > 300 ? 50 : Math.round(rawT));
        var menitPraktek = rawP <= 10 ? Math.round(rawP * 50) : (rawP > 600 ? 100 : Math.round(rawP));
        $('[name="jam_teori"]').val(menitTeori);
        $('[name="jam_praktek"]').val(menitPraktek);

        $('[name="bacaan_materi"]').val(data.bacaan_materi);
        $('[name="berita_acara_rencana"]').val(data.berita_acara_rencana);
        $('[name="draf_bap"]').val(data.draf_bap);
        $('[name="sub_cpmk"]').val(data.sub_cpmk);
        $('[name="metode_pembelajaran"]').val(data.metode_pembelajaran);
        $('[name="indikator_evaluasi"]').val(data.indikator_evaluasi);
        $('[name="catatan_refleksi"]').val(data.catatan_refleksi);

        $('#modal_meet_title').html('Pertemuan ke-' + data.pertemuan + ' - ' + (data.nm_mk || ''));
        $('#modal_course_name').html((data.nm_mk || '') + ' (' + (data.nm_kls || 'Kelas') + ')');

        if (data.is_realisasi == 1 || data.is_realisasi == '1') {
          $('#is_realisasi').prop("checked", true);
          $('#tanggal_realisasi').val(data.tanggal_realisasi || '');
        } else {
          $('#is_realisasi').prop("checked", false);
          $('#tanggal_realisasi').val('');
        }
        check_realisasi();

        renderSubcpmkChecklist(data.subcpmk_options, data.sub_cpmk);
        renderSclChips(data.metode_pembelajaran);
        // skenario_scl = hasil parse bersih dari RPS OBE (atau isi DB jika sudah ada)
        // berita_acara_rencana = raw DB field untuk textarea edit saja
        renderSkenarioCards(data.skenario_scl || data.berita_acara_rencana);
        
        // Populate 6 Structured BAP Fields
        var parsedBap = parseBapFields(data.draf_bap);
        $('#bap_materi').val(parsedBap.materi);
        $('#bap_aktivitas').val(parsedBap.aktivitas);
        $('#bap_capaian').val(parsedBap.capaian);
        $('#bap_kendala').val(parsedBap.kendala);
        $('#bap_tindak_lanjut').val(parsedBap.tindak_lanjut);
        selectBapKesesuaian(parsedBap.kesesuaian);
        renderBapKendalaChips(parsedBap.kendala);
        renderBapTindakChips(parsedBap.tindak_lanjut);

        renderBapPreview(data.draf_bap);

        $('#sub_cpmk_edit_wrap').hide();
        $('#btnToggleEditSubcpmk').html('<i class="fa fa-pencil mr-1"></i> Edit Teks');
        $('#draf_bap_edit_wrap').hide();
        $('#btnToggleEditBap').html('<i class="fa fa-pencil mr-1"></i> Edit Draf');

        $('#modal_form').modal('show');
        $('.modal-title').text('Detail & Strategi Pembelajaran (Meet ' + data.pertemuan + ')');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        swal("Error", "Gagal mengambil data dari server", "error");
      }
    });
  }

  function renderSkenarioCards(text) {
    if (!text || text.trim() === '') {
      $('#skenario_visual_box').html('<span class="text-muted font-italic">Skenario pembelajaran belum diisi. Klik "Edit Teks" untuk menyusun langkah pembelajaran SCL.</span>');
      return;
    }

    var clean = text.replace(/<br\s*[\/]?>/gi, '\n').replace(/&nbsp;/g, ' ').trim();
    var lines = clean.split(/\r?\n/);
    var html = '<div class="scl-cockpit-content" style="font-size: 13px; line-height: 1.65; color: #1e293b;">';
    var inList = false;

    for (var i = 0; i < lines.length; i++) {
      var tline = lines[i].trim();
      if (!tline) {
        if (inList) {
          html += '</ul>';
          inList = false;
        }
        continue;
      }

      // Clean line from rogue asterisks for stage matching
      var cleanLine = tline.replace(/\*\*/g, '').trim();
      var stageMatch = cleanLine.match(/^(\d+[\.\)]\s*)?(Tahap\s+[^:]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:]*)(?:\s*:\s*)?(.*)$/i);
      if (stageMatch) {
        if (inList) {
          html += '</ul>';
          inList = false;
        }
        var stageNum = stageMatch[1] ? stageMatch[1].trim() + ' ' : '';
        var stageTitle = stageMatch[2].trim();
        var stageExtra = stageMatch[3] ? stageMatch[3].trim() : '';

        html += '<div style="margin-top: 14px; margin-bottom: 6px; font-weight: 700; color: #0f172a; font-size: 13.5px;">' +
                '<span style="color: #0f172a; font-weight: 700;">' + $('<div>').text(stageNum + stageTitle).html() + '</span>' +
                (stageExtra ? ' <span style="font-weight: 600; color: #334155;">: ' + $('<div>').text(stageExtra).html() + '</span>' : '') +
                '</div>';
        continue;
      }

      // Check bullet point
      var bulletMatch = tline.match(/^(?:[\-\*•]|\d+[\.\)])\s*(.*)$/u);
      if (bulletMatch) {
        if (!inList) {
          html += '<ul style="margin: 0 0 10px 0; padding-left: 22px; list-style-type: disc;">';
          inList = true;
        }
        var bulletContent = bulletMatch[1].trim();
        bulletContent = $('<div>').text(bulletContent).html().replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html += '<li style="margin-bottom: 6px; color: #334155; line-height: 1.6;">' + bulletContent + '</li>';
        continue;
      }

      if (inList) {
        html += '</ul>';
        inList = false;
      }

      var lineContent = $('<div>').text(tline).html().replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
      html += '<p style="margin-bottom: 8px; color: #334155;">' + lineContent + '</p>';
    }

    if (inList) {
      html += '</ul>';
    }
    html += '</div>';

    $('#skenario_visual_box').html(html);
  }

  var presetKendala = [
    'Tidak ada kendala',
    'Waktu perkuliahan tidak mencukupi',
    'Materi membutuhkan waktu lebih lama dari rencana',
    'Mahasiswa belum memahami materi prasyarat',
    'Partisipasi mahasiswa rendah',
    'Kehadiran mahasiswa rendah',
    'Kendala perangkat/laptop mahasiswa',
    'Kendala perangkat dosen',
    'Kendala ruang/laboratorium',
    'Perkuliahan terganggu kegiatan kampus',
    'Materi harus disesuaikan dengan kondisi kelas',
    'Praktikum tidak dapat dilaksanakan optimal',
    'Lainnya'
  ];

  var presetTindakLanjut = [
    'Materi dilanjutkan minggu depan',
    'Mahasiswa mengerjakan latihan tambahan',
    'Remedial/penguatan konsep',
    'Praktikum lanjutan',
    'Persiapan asesmen'
  ];

  function parseBapFields(text) {
    var raw = text ? text.replace(/<br\s*[\/]?>/gi, '\n').trim() : '';
    var result = {
      materi: '',
      aktivitas: '',
      capaian: '',
      kendala: '',
      tindak_lanjut: '',
      kesesuaian: 'Sesuai RPS'
    };

    if (!raw) return result;

    var matchMateri     = raw.match(/Realisasi Materi\s*:\s*([\s\S]*?)(?=(?:Aktivitas|Capaian|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i);
    var matchAktivitas  = raw.match(/Aktivitas\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Capaian|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i);
    var matchCapaian    = raw.match(/Capaian\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i);
    var matchKendala    = raw.match(/Kendala\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Capaian|Tindak\s*lanjut|Kesesuaian RPS|$))/i);
    var matchTindak     = raw.match(/Tindak\s*lanjut\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Capaian|Kendala|Kesesuaian RPS|$))/i);
    var matchKesesuaian = raw.match(/Kesesuaian RPS\s*:\s*([\s\S]*?)$/i);

    if (matchMateri || matchAktivitas || matchCapaian || matchKendala || matchTindak || matchKesesuaian) {
      result.materi = matchMateri ? matchMateri[1].trim() : '';
      result.aktivitas = matchAktivitas ? matchAktivitas[1].trim() : '';
      result.capaian = matchCapaian ? matchCapaian[1].trim() : '';
      result.kendala = matchKendala ? matchKendala[1].trim() : '';
      result.tindak_lanjut = matchTindak ? matchTindak[1].trim() : '';
      var ks = matchKesesuaian ? matchKesesuaian[1].trim().replace(/\.$/, '') : 'Sesuai RPS';
      if (/sebagian/i.test(ks)) {
        result.kesesuaian = 'Sebagian besar sesuai';
      } else if (/tidak/i.test(ks)) {
        result.kesesuaian = 'Tidak sesuai';
      } else {
        result.kesesuaian = 'Sesuai RPS';
      }
    } else {
      result.materi = raw;
      result.kesesuaian = 'Sesuai RPS';
    }

    return result;
  }

  function renderBapKendalaChips(currentVal) {
    var curLower = (currentVal || '').toLowerCase();
    var html = '';
    for (var i = 0; i < presetKendala.length; i++) {
      var item = presetKendala[i];
      var isAct = false;
      if (item === 'Tidak ada kendala') {
        isAct = /tidak ada kendala/i.test(curLower);
      } else {
        isAct = curLower.indexOf(item.toLowerCase()) !== -1;
      }
      var cls = isAct ? 'bap-kendala-chip active' : 'bap-kendala-chip';
      var icon = isAct ? '<i class="fa fa-check mr-1"></i> ' : '<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ';
      html += '<button type="button" class="' + cls + '" data-val="' + item + '" onclick="toggleBapKendalaChip(this)">' + icon + item + '</button>';
    }
    $('#bap_kendala_chips').html(html);
  }

  function toggleBapKendalaChip(el) {
    var $btn = $(el);
    var val = $btn.attr('data-val');
    var isAct = $btn.hasClass('active');

    if (val === 'Tidak ada kendala') {
      if (isAct) {
        $btn.removeClass('active');
        $btn.html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + val);
      } else {
        // Activate "Tidak ada kendala" and deactivate other kendala chips
        $('.bap-kendala-chip').removeClass('active').each(function() {
          $(this).html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + $(this).attr('data-val'));
        });
        $btn.addClass('active');
        $btn.html('<i class="fa fa-check mr-1"></i> ' + val);
      }
    } else {
      if (isAct) {
        $btn.removeClass('active');
        $btn.html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + val);
      } else {
        var $btnNone = $('.bap-kendala-chip[data-val="Tidak ada kendala"]');
        if ($btnNone.hasClass('active')) {
          $btnNone.removeClass('active').html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> Tidak ada kendala');
        }
        $btn.addClass('active');
        $btn.html('<i class="fa fa-check mr-1"></i> ' + val);
      }
    }

    var selectedVals = [];
    $('.bap-kendala-chip.active').each(function() {
      selectedVals.push($(this).attr('data-val'));
    });
    $('#bap_kendala').val(selectedVals.join(', '));
    syncBapFromFields();
  }

  function onBapKendalaInput() {
    var curVal = $('#bap_kendala').val();
    var curLower = curVal.toLowerCase();
    $('.bap-kendala-chip').each(function() {
      var itemVal = $(this).attr('data-val');
      var isAct = false;
      if (itemVal === 'Tidak ada kendala') {
        isAct = /tidak ada kendala/i.test(curLower);
      } else if (itemVal) {
        isAct = curLower.indexOf(itemVal.toLowerCase()) !== -1;
      }

      if (isAct) {
        $(this).addClass('active').html('<i class="fa fa-check mr-1"></i> ' + itemVal);
      } else {
        $(this).removeClass('active').html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + itemVal);
      }
    });
    syncBapFromFields();
  }

  function renderBapTindakChips(currentVal) {
    var curLower = (currentVal || '').toLowerCase();
    var html = '';
    for (var i = 0; i < presetTindakLanjut.length; i++) {
      var item = presetTindakLanjut[i];
      var isAct = curLower.indexOf(item.toLowerCase()) !== -1;
      var cls = isAct ? 'bap-tindak-chip active' : 'bap-tindak-chip';
      var icon = isAct ? '<i class="fa fa-check mr-1"></i> ' : '<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ';
      html += '<button type="button" class="' + cls + '" data-val="' + item + '" onclick="toggleBapTindakChip(this)">' + icon + item + '</button>';
    }
    $('#bap_tindak_lanjut_chips').html(html);
  }

  function toggleBapTindakChip(el) {
    var $btn = $(el);
    var val = $btn.attr('data-val');
    var isAct = $btn.hasClass('active');

    if (isAct) {
      $btn.removeClass('active');
      $btn.html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + val);
    } else {
      $btn.addClass('active');
      $btn.html('<i class="fa fa-check mr-1"></i> ' + val);
    }

    var selectedVals = [];
    $('.bap-tindak-chip.active').each(function() {
      selectedVals.push($(this).attr('data-val'));
    });
    $('#bap_tindak_lanjut').val(selectedVals.join(', '));
    syncBapFromFields();
  }

  function onBapTindakLanjutInput() {
    var curVal = $('#bap_tindak_lanjut').val();
    var curLower = curVal.toLowerCase();
    $('.bap-tindak-chip').each(function() {
      var itemVal = $(this).attr('data-val');
      if (itemVal && curLower.indexOf(itemVal.toLowerCase()) !== -1) {
        $(this).addClass('active').html('<i class="fa fa-check mr-1"></i> ' + itemVal);
      } else {
        $(this).removeClass('active').html('<i class="fa fa-plus mr-1" style="opacity:0.6;"></i> ' + itemVal);
      }
    });
    syncBapFromFields();
  }

  function selectBapKesesuaian(val) {
    var cleanVal = val || 'Sesuai RPS';
    $('#bap_kesesuaian').val(cleanVal);
    $('.bap-kesesuaian-chip').removeClass('active');
    $('.bap-kesesuaian-chip[data-val="' + cleanVal + '"]').addClass('active');
    syncBapFromFields();
  }

  function syncBapFromFields() {
    var m = $('#bap_materi').val().trim();
    var a = $('#bap_aktivitas').val().trim();
    var c = $('#bap_capaian').val().trim();
    var k = $('#bap_kendala').val().trim();
    var t = $('#bap_tindak_lanjut').val().trim();
    var s = $('#bap_kesesuaian').val().trim() || 'Sesuai RPS';

    var parts = [];
    if (m) parts.push("Realisasi Materi:\n" + m);
    if (a) parts.push("Aktivitas:\n" + a);
    if (c) parts.push("Capaian:\n" + c);
    if (k) parts.push("Kendala:\n" + k);
    if (t) parts.push("Tindak lanjut:\n" + t);
    if (s) parts.push("Kesesuaian RPS: " + s);

    var fullText = parts.join("\n\n");
    $('#draf_bap').val(fullText);
    renderBapPreview(fullText);
  }

  function renderBapPreview(text) {
    if (!text || text.trim() === '') {
      $('#bap_preview_box').html('<span class="text-muted font-italic">Draft evaluasi pertemuan belum tersedia. Klik "Edit Evaluasi" untuk mengisi.</span>');
      return;
    }

    var p = parseBapFields(text);

    if (!p.aktivitas && !p.capaian && !p.kendala && !p.tindak_lanjut && p.materi === text.trim()) {
      $('#bap_preview_box').html('<div style="color: #212529; font-size: 12.5px; line-height: 1.6;">' + $('<div>').text(text).html().replace(/\n/g, '<br>') + '</div>');
      return;
    }

    var ksBadge = '<span class="badge badge-success px-2 py-1" style="font-size: 11px; background:#dcfce7; color:#15803d; border:1px solid #86efac;"><i class="fa fa-check-circle mr-1"></i> Sesuai RPS</span>';
    if (/sebagian/i.test(p.kesesuaian)) {
      ksBadge = '<span class="badge badge-warning px-2 py-1" style="font-size: 11px; background:#fef3c7; color:#78350f; border:1px solid #fde68a;"><i class="fa fa-adjust mr-1"></i> ' + $('<div>').text(p.kesesuaian).html() + '</span>';
    } else if (/tidak/i.test(p.kesesuaian)) {
      ksBadge = '<span class="badge badge-danger px-2 py-1" style="font-size: 11px; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;"><i class="fa fa-times-circle mr-1"></i> ' + $('<div>').text(p.kesesuaian).html() + '</span>';
    }

    var html = '<div style="font-size: 12.5px; line-height: 1.6; color: #1e293b; display: flex; flex-direction: column; gap: 8px;">';

    if (p.materi) {
      html += '<div><strong style="color: #0f172a;"><i class="fa fa-book text-primary mr-1"></i> Realisasi Materi:</strong><br><span style="color: #334155;">' + $('<div>').text(p.materi).html().replace(/\n/g, '<br>') + '</span></div>';
    }
    if (p.aktivitas) {
      html += '<div><strong style="color: #0f172a;"><i class="fa fa-clock-o text-info mr-1"></i> Aktivitas (SCL):</strong><br><span style="color: #334155;">' + $('<div>').text(p.aktivitas).html().replace(/\n/g, '<br>') + '</span></div>';
    }
    if (p.capaian) {
      html += '<div><strong style="color: #0f172a;"><i class="fa fa-trophy text-success mr-1"></i> Capaian:</strong><br><span style="color: #334155;">' + $('<div>').text(p.capaian).html().replace(/\n/g, '<br>') + '</span></div>';
    }
    if (p.kendala) {
      html += '<div><strong style="color: #0f172a;"><i class="fa fa-exclamation-triangle text-danger mr-1"></i> Kendala:</strong><br><span style="color: #334155;">' + $('<div>').text(p.kendala).html().replace(/\n/g, '<br>') + '</span></div>';
    }
    if (p.tindak_lanjut) {
      html += '<div><strong style="color: #0f172a;"><i class="fa fa-arrow-circle-right text-warning mr-1"></i> Tindak Lanjut:</strong><br><span style="color: #334155;">' + $('<div>').text(p.tindak_lanjut).html().replace(/\n/g, '<br>') + '</span></div>';
    }
    if (p.kesesuaian) {
      html += '<div style="margin-top: 4px; display: flex; align-items: center; gap: 6px;"><strong style="color: #0f172a;">Kesesuaian RPS:</strong> ' + ksBadge + '</div>';
    }

    html += '</div>';
    $('#bap_preview_box').html(html);
  }

  var currentSubcpmkOptions = [];

  function renderSubcpmkChecklist(options, currentValue) {
    currentSubcpmkOptions = options || [];
    var curVal = currentValue ? currentValue.trim() : '';

    if (!options || options.length === 0) {
      if (curVal) {
        var html = '<div class="subcpmk-item active">' +
          '<div style="flex: 1;">' +
            '<div style="display: flex; align-items: center; flex-wrap: wrap;">' +
              '<span class="subcpmk-badge-code">Sub-CPMK Terpilih</span>' +
            '</div>' +
            '<div class="subcpmk-desc-text">' + $('<div>').text(curVal).html() + '</div>' +
          '</div>' +
        '</div>';
        $('#subcpmk_checklist_container').html(html);
        $('#sub_cpmk_edit_wrap').hide();
        $('#btnToggleEditSubcpmk').html('<i class="fa fa-pencil mr-1"></i> Edit Teks');
        return;
      }
      $('#subcpmk_checklist_container').html(
        '<div class="text-muted" style="font-size: 12px; padding: 6px 4px;">' +
        '<i class="fa fa-info-circle mr-1 text-info"></i> Tidak ada butir Sub-CPMK dari RPS OBE. Silakan ketik langsung di editor teks bawah.</div>'
      );
      $('#sub_cpmk_edit_wrap').show();
      $('#btnToggleEditSubcpmk').html('<i class="fa fa-eye mr-1"></i> Tutup Editor');
      return;
    }

    var html = '';
    for (var i = 0; i < options.length; i++) {
      var opt = options[i];
      var isChecked = false;
      if (curVal) {
        if (opt.code && curVal.toLowerCase().indexOf(opt.code.toLowerCase()) !== -1) {
          isChecked = true;
        } else if (opt.desc && (curVal.toLowerCase().indexOf(opt.desc.toLowerCase()) !== -1 || opt.desc.toLowerCase().indexOf(curVal.toLowerCase()) !== -1)) {
          isChecked = true;
        } else if (opt.full && (curVal.toLowerCase().indexOf(opt.full.toLowerCase()) !== -1 || opt.full.toLowerCase().indexOf(curVal.toLowerCase()) !== -1)) {
          isChecked = true;
        }
      }

      var activeClass = isChecked ? ' active' : '';
      var checkedAttr = isChecked ? ' checked' : '';

      html += '<div class="subcpmk-item' + activeClass + '" onclick="toggleSubcpmkItem(this, event)">' +
        '<input type="checkbox" ' + checkedAttr + ' onchange="event.stopPropagation(); syncSubcpmkValues();">' +
        '<div style="flex: 1;">' +
          '<div style="display: flex; align-items: center; flex-wrap: wrap;">' +
            '<span class="subcpmk-badge-code">' + opt.code + '</span>' +
            (opt.cpmk ? '<span class="subcpmk-badge-parent">' + opt.cpmk + '</span>' : '') +
          '</div>' +
          '<div class="subcpmk-desc-text">' + (opt.desc || opt.full) + '</div>' +
        '</div>' +
      '</div>';
    }

    $('#subcpmk_checklist_container').html(html);
  }

  function toggleSubcpmkItem(el, e) {
    if (e && e.target && e.target.tagName.toLowerCase() === 'input') {
      return;
    }
    var $item = $(el);
    var $cb = $item.find('input[type="checkbox"]');
    var isChecked = !$cb.prop('checked');
    $cb.prop('checked', isChecked);
    if (isChecked) {
      $item.addClass('active');
    } else {
      $item.removeClass('active');
    }
    syncSubcpmkValues();
  }

  function syncSubcpmkValues() {
    var selectedLines = [];
    $('#subcpmk_checklist_container .subcpmk-item').each(function(idx) {
      var $cb = $(this).find('input[type="checkbox"]');
      if ($cb.prop('checked')) {
        $(this).addClass('active');
        if (currentSubcpmkOptions && currentSubcpmkOptions[idx]) {
          selectedLines.push(currentSubcpmkOptions[idx].full || (currentSubcpmkOptions[idx].code + ': ' + currentSubcpmkOptions[idx].desc));
        }
      } else {
        $(this).removeClass('active');
      }
    });

    if (currentSubcpmkOptions && currentSubcpmkOptions.length > 0) {
      $('#sub_cpmk').val(selectedLines.join('\n'));
    }
  }

  var presetSclMethods = [
    'Kuliah Interaktif',
    'Ceramah Interaktif',
    'Diskusi Kelompok',
    'Case Method',
    'Project-Based Learning (PjBL)',
    'Problem-Based Learning (PBL)',
    'Praktik / Simulasi',
    'Presentasi Mahasiswa',
    'Belajar Mandiri'
  ];

  function renderSclChips(currentValue) {
    var curLower = (currentValue || '').toLowerCase();
    var selectedArr = [];
    if (currentValue) {
      selectedArr = currentValue.split(',').map(function(s) { return s.trim().toLowerCase(); }).filter(Boolean);
    }

    var html = '';
    for (var i = 0; i < presetSclMethods.length; i++) {
      var m = presetSclMethods[i];
      var mLower = m.toLowerCase();
      var isSelected = false;
      if (curLower.indexOf(mLower) !== -1) {
        isSelected = true;
      } else {
        for (var j = 0; j < selectedArr.length; j++) {
          if (selectedArr[j] === mLower || (selectedArr[j].length > 4 && (mLower.indexOf(selectedArr[j]) !== -1 || selectedArr[j].indexOf(mLower) !== -1))) {
            isSelected = true;
            break;
          }
        }
      }

      var activeClass = isSelected ? 'scl-chip-active' : 'scl-chip-inactive';
      var icon = isSelected ? '<i class="fa fa-check mr-1"></i> ' : '<i class="fa fa-plus mr-1" style="opacity: 0.6;"></i> ';

      html += '<button type="button" class="btn btn-xs scl-chip ' + activeClass + '" data-val="' + m + '" onclick="toggleSclChip(this)">' +
        icon + m +
      '</button>';
    }

    $('#scl_checklist_container').html(html);
  }

  function toggleSclChip(el) {
    var $btn = $(el);
    var val = $btn.attr('data-val');
    var isCurrentlyActive = $btn.hasClass('scl-chip-active');

    if (isCurrentlyActive) {
      $btn.removeClass('scl-chip-active').addClass('scl-chip-inactive');
      $btn.html('<i class="fa fa-plus mr-1" style="opacity: 0.6;"></i> ' + val);
    } else {
      $btn.removeClass('scl-chip-inactive').addClass('scl-chip-active');
      $btn.html('<i class="fa fa-check mr-1"></i> ' + val);
    }

    syncSclValues();
  }

  function syncSclValues() {
    var activeVals = [];
    $('.scl-chip-active').each(function() {
      activeVals.push($(this).attr('data-val'));
    });
    $('#metode_pembelajaran').val(activeVals.join(', '));
  }

  function save() {
    $('#btnSave').text('Menyimpan...');
    $('#btnSave').attr('disabled', true);

    $.ajax({
      url: "<?php echo site_url('C_strategi_pertemuan/ajax_update') ?>",
      type: "POST",
      data: $('#form').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_form').modal('hide');
          reload_table();
          swal("Berhasil", "Strategi pertemuan berhasil diperbarui!", "success");
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').closest('.form-group').addClass('has-error');
            $('[name="' + data.inputerror[i] + '"]').closest('.form-group').find('.help-block').text(data.error_string[i]);
          }
        }
        $('#btnSave').text('Simpan Perubahan');
        $('#btnSave').attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        swal("Error", "Gagal menyimpan perubahan ke server", "error");
        $('#btnSave').text('Simpan Perubahan');
        $('#btnSave').attr('disabled', false);
      }
    });
  }

  function check_realisasi() {
    var isChecked = $("#is_realisasi").is(':checked');
    if (isChecked) {
      $(".tgl_realisasi").stop(true, true).slideDown(150);
      $("#badge_status_realisasi").css({ background: '#dcfce7', color: '#15803d', border: '1px solid #86efac' }).html('<i class="fa fa-check"></i> Sudah Terealisasi');
      $("#label_is_realisasi").css('color', '#15803d');
      var currentTgl = $('#tanggal_realisasi').val();
      if (!currentTgl || currentTgl === '0000-00-00') {
        var tglRencana = $('[name="tanggal_rencana"]').val();
        var today = new Date().toISOString().slice(0, 10);
        $('#tanggal_realisasi').val(tglRencana || today);
      }
    } else {
      $(".tgl_realisasi").stop(true, true).slideUp(150);
      $("#badge_status_realisasi").css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' }).html('<i class="fa fa-clock-o"></i> Belum Realisasi');
      $("#label_is_realisasi").css('color', '#1e293b');
      $('#tanggal_realisasi').val('');
    }
  }
</script>

<!-- MODAL FORM EDIT DETAIL STRATEGI PERTEMUAN -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
    <div class="modal-content" style="border-radius: 8px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
      
      <div class="modal-header bg-primary text-white" style="border-radius: 8px 8px 0 0; padding: 16px 22px;">
        <div>
          <h5 class="modal-title font-weight-bold mb-0 text-white" id="modalLabel">Detail & Strategi Pembelajaran</h5>
          <small class="text-light" id="modal_course_name"></small>
        </div>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-4" style="background: #f8fafc;">
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" value="" name="uniq" />
          <input type="hidden" value="" name="pertemuan" />
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

          <!-- SECTION 1: WAKTU & JADWAL PELAKSANAAN -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-clock-o text-primary mr-2"></i> Waktu & Alokasi Pembelajaran</span>
              <span class="badge badge-primary px-2 py-1" id="modal_meet_title">Pertemuan</span>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group mb-2">
                  <label class="small font-weight-bold text-dark mb-1">Tanggal Rencana</label>
                  <input name="tanggal_rencana" id="tanggal_rencana" placeholder="YYYY-MM-DD" class="form-control form-control-sm bg-white" type="text">
                  <span class="help-block text-danger small"></span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group mb-2">
                  <label class="small font-weight-bold text-dark mb-1">Jam Mulai (24 Jam)</label>
                  <input name="jam_mulai" id="jam_mulai" placeholder="13:00" class="form-control form-control-sm bg-white" type="text" autocomplete="off">
                  <span class="help-block text-danger small"></span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group mb-2">
                  <label class="small font-weight-bold text-dark mb-1">Jam Selesai (24 Jam)</label>
                  <input name="jam_selesai" id="jam_selesai" placeholder="17:00" class="form-control form-control-sm bg-white" type="text" autocomplete="off">
                  <span class="help-block text-danger small"></span>
                </div>
              </div>
            </div>
            <div class="row mt-2 pt-2 border-top">
              <div class="col-md-6">
                <div class="form-group mb-0">
                  <label class="small font-weight-bold text-dark mb-1">Alokasi Teori / Tatap Muka (Menit)</label>
                  <div class="input-group input-group-sm">
                    <input name="jam_teori" id="jam_teori" class="form-control form-control-sm" type="number" step="1" min="0" placeholder="50">
                    <div class="input-group-append">
                      <span class="input-group-text">Menit</span>
                    </div>
                  </div>
                  <small class="text-muted" style="font-size:11px;">Alokasi waktu teori (cth: 50, 100 menit)</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-0">
                  <label class="small font-weight-bold text-dark mb-1">Alokasi Praktik / Penugasan (Menit)</label>
                  <div class="input-group input-group-sm">
                    <input name="jam_praktek" id="jam_praktek" class="form-control form-control-sm" type="number" step="1" min="0" placeholder="100">
                    <div class="input-group-append">
                      <span class="input-group-text">Menit</span>
                    </div>
                  </div>
                  <small class="text-muted" style="font-size:11px;">Alokasi waktu praktik/mandiri (cth: 100, 170 menit)</small>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: SUB-CPMK OBE -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-bullseye text-danger mr-2"></i> Kemampuan Akhir / Sub-CPMK (Target Capaian)</span>
              <button type="button" class="btn btn-xs btn-outline-secondary" id="btnToggleEditSubcpmk">
                <i class="fa fa-pencil mr-1"></i> Edit Teks
              </button>
            </div>
            <div id="subcpmk_checklist_container" class="subcpmk-container mb-2"></div>
            <div id="sub_cpmk_edit_wrap" style="display: none;" class="mt-2">
              <textarea name="sub_cpmk" id="sub_cpmk" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Sub-CPMK pembelajaran..."></textarea>
              <small class="text-muted">Ketik manual jika ada Sub-CPMK khusus untuk pertemuan ini.</small>
            </div>
          </div>

          <!-- SECTION 3: METODE & BENTUK SCL -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-cogs text-info mr-2"></i> Bentuk & Metode Pembelajaran (Student-Centered Learning)</span>
            </div>
            <div id="scl_checklist_container" class="d-flex flex-wrap mb-2"></div>
            <input type="text" name="metode_pembelajaran" id="metode_pembelajaran" class="form-control form-control-sm" placeholder="Metode pembelajaran terpilih...">
          </div>

          <!-- SECTION 4: BAHAN KAJIAN & MATERI -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-book text-success mr-2"></i> Bahan Kajian / Topik Materi Pembelajaran</span>
            </div>
            <textarea name="bacaan_materi" id="bacaan_materi" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Rincian topik pokok bahasan..."></textarea>
          </div>

          <!-- SECTION 5: SKENARIO SINTAKS SCL -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-tasks text-primary mr-2"></i> Skenario & Langkah Pembelajaran (Sintaks SCL)</span>
            </div>
            <div id="skenario_visual_box" class="p-2 bg-light rounded mb-0"></div>
            <textarea name="berita_acara_rencana" id="berita_acara_rencana" style="display: none;"></textarea>
          </div>

          <!-- SECTION 6: DRAFT EVALUASI PERTEMUAN -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-file-text text-warning mr-2"></i> Draft Evaluasi Pertemuan</span>
              <button type="button" class="btn btn-xs btn-outline-secondary" id="btnToggleEditBap">
                <i class="fa fa-pencil mr-1"></i> Edit Evaluasi
              </button>
            </div>
            <div id="bap_preview_box" class="p-2 bg-light rounded mb-2" style="border-left: 3px solid #f59e0b;"></div>
            
            <!-- Structured 6 Categories Form Editor -->
            <div id="draf_bap_edit_wrap" style="display: none; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-top: 10px;">
              
              <!-- 1. Realisasi Materi -->
              <div class="form-group mb-2">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-primary mr-1" style="font-size:10px;">1</span> Realisasi Materi (Text)
                </label>
                <textarea id="bap_materi" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Rincian materi pokok bahasan nyata yang diajarkan..." oninput="syncBapFromFields()"></textarea>
              </div>

              <!-- 2. Aktivitas SCL -->
              <div class="form-group mb-2">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-info mr-1" style="font-size:10px;">2</span> Aktivitas &amp; Durasi Waktu (SCL)
                </label>
                <textarea id="bap_aktivitas" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Penjelasan konsep 30 menit, demonstrasi 30 menit, praktik mahasiswa 70 menit, diskusi 20 menit..." oninput="syncBapFromFields()"></textarea>
              </div>

              <!-- 3. Capaian -->
              <div class="form-group mb-2">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-success mr-1" style="font-size:10px;">3</span> Capaian Hasil Belajar Mahasiswa (Text)
                </label>
                <textarea id="bap_capaian" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Sebagian besar mahasiswa dapat memahami dan membangun..." oninput="syncBapFromFields()"></textarea>
              </div>

              <!-- 4. Kendala (Chips / Pill choices) -->
              <div class="form-group mb-2">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-danger mr-1" style="font-size:10px;">4</span> Kendala Pembelajaran (Pilihan / Chips &amp; Text)
                </label>
                <div id="bap_kendala_chips" class="mb-2 d-flex flex-wrap" style="gap: 5px;">
                  <!-- Dynamically rendered -->
                </div>
                <textarea id="bap_kendala" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Kendala teknis/pemahaman (pilih chips atau ketik detail)..." oninput="onBapKendalaInput()"></textarea>
              </div>

              <!-- 5. Tindak Lanjut (Chips / Pill choices) -->
              <div class="form-group mb-2">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-warning mr-1" style="font-size:10px; color:#78350f;">5</span> Tindak Lanjut (Pilihan / Chips)
                </label>
                <div id="bap_tindak_lanjut_chips" class="mb-2 d-flex flex-wrap" style="gap: 5px;">
                  <!-- Dynamically rendered -->
                </div>
                <input type="text" id="bap_tindak_lanjut" class="form-control form-control-sm" placeholder="Tindak lanjut yang dipilih..." oninput="onBapTindakLanjutInput()">
              </div>

              <!-- 6. Kesesuaian RPS -->
              <div class="form-group mb-0">
                <label class="small font-weight-bold text-dark mb-1">
                  <span class="badge badge-secondary mr-1" style="font-size:10px;">6</span> Kesesuaian RPS (Pilihan)
                </label>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                  <button type="button" class="btn btn-xs bap-kesesuaian-chip" data-val="Sesuai RPS" onclick="selectBapKesesuaian('Sesuai RPS')">
                    <i class="fa fa-check-circle mr-1 text-success"></i> Sesuai RPS
                  </button>
                  <button type="button" class="btn btn-xs bap-kesesuaian-chip" data-val="Sebagian besar sesuai" onclick="selectBapKesesuaian('Sebagian besar sesuai')">
                    <i class="fa fa-adjust mr-1 text-warning"></i> Sebagian besar sesuai
                  </button>
                  <button type="button" class="btn btn-xs bap-kesesuaian-chip" data-val="Tidak sesuai" onclick="selectBapKesesuaian('Tidak sesuai')">
                    <i class="fa fa-times-circle mr-1 text-danger"></i> Tidak sesuai
                  </button>
                </div>
                <input type="hidden" id="bap_kesesuaian" value="Sesuai RPS">
              </div>

              <!-- Hidden main textarea for backend compatibility -->
              <textarea name="draf_bap" id="draf_bap" style="display:none;"></textarea>
            </div>
          </div>

          <!-- SECTION 7: EVALUASI & REFLEKSI -->
          <div class="form-section-box">
            <div class="form-section-title">
              <span><i class="fa fa-check-square-o text-secondary mr-2"></i> Kriteria Penilaian & Catatan Refleksi</span>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label class="small font-weight-bold text-dark mb-1">Indikator & Kriteria Penilaian</label>
                <textarea name="indikator_evaluasi" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Kriteria evaluasi..."></textarea>
              </div>
              <div class="col-md-6">
                <label class="small font-weight-bold text-dark mb-1">Catatan Refleksi Pembelajaran</label>
                <textarea name="catatan_refleksi" class="form-control form-control-sm bap-textarea-resizable" rows="3" placeholder="Catatan evaluasi kelas..."></textarea>
              </div>
            </div>
          </div>

          <!-- SECTION 8: STATUS REALISASI -->
          <div class="form-section-box mb-0" style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
              
              <!-- Left: Checkbox, Label & Status Badge -->
              <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="is_realisasi" name="is_realisasi" value="1" onchange="check_realisasi()" style="width: 20px; height: 20px; cursor: pointer; accent-color: #10b981; margin: 0;">
                <label for="is_realisasi" id="label_is_realisasi" style="margin: 0; font-size: 13.5px; font-weight: 700; color: #1e293b; cursor: pointer; user-select: none;">
                  Tandai Sebagai Selesai / Sudah Terealisasi
                </label>
                <span id="badge_status_realisasi" class="badge" style="font-size: 11px; padding: 4px 8px; border-radius: 4px; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                  <i class="fa fa-clock-o"></i> Belum Realisasi
                </span>
              </div>

              <!-- Right: Tanggal Realisasi -->
              <div class="tgl_realisasi" style="display: none;">
                <div style="display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px;">
                  <label for="tanggal_realisasi" style="margin: 0; font-size: 12px; font-weight: 700; color: #334155; white-space: nowrap;">
                    <i class="fa fa-calendar text-success" style="margin-right: 4px;"></i> Tgl Realisasi:
                  </label>
                  <input name="tanggal_realisasi" id="tanggal_realisasi" placeholder="YYYY-MM-DD" class="form-control form-control-sm" type="text" style="width: 130px; height: 28px; font-size: 12px; font-weight: 600; background: #ffffff; border: 1px solid #94a3b8; border-radius: 4px; padding: 2px 8px;">
                </div>
              </div>

            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer bg-white" style="border-radius: 0 0 8px 8px; padding: 14px 22px;">
        <button type="button" class="btn btn-secondary btn-sm rounded shadow-sm" data-dismiss="modal">
          <i class="fa fa-times mr-1"></i> Batal
        </button>
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary btn-sm rounded shadow-sm px-3">
          <i class="fa fa-save mr-1"></i> Simpan Perubahan
        </button>
      </div>

    </div>
  </div>
</div>
