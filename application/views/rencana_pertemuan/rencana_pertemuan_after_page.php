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
    border-radius: 4px;
    padding: 14px 16px;
    margin-bottom: 12px;
  }
  .form-section-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 8px;
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
  .subcpmk-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
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
  var arr_tahun = [];
  var TableAjax = function() {
    var handleRecords = function() {

      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagination": true,
        "destroy": true,
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_rencana_pertemuan/ajax_list/' . $id_jadwal) ?>",
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

    // Toggle edit for Skenario
    $('#btnToggleEditSkenario').on('click', function(e) {
      e.preventDefault();
      $('#berita_acara_rencana_edit_wrap').slideToggle(150, function() {
        if ($('#berita_acara_rencana_edit_wrap').is(':visible')) {
          $('#btnToggleEditSkenario').html('<i class="fa fa-eye mr-1"></i> Tutup Editor');
          $('#berita_acara_rencana').focus();
        } else {
          $('#btnToggleEditSkenario').html('<i class="fa fa-pencil mr-1"></i> Edit Teks');
        }
      });
    });

    // Toggle edit for Draf BAP
    $('#btnToggleEditBap').on('click', function(e) {
      e.preventDefault();
      $('#draf_bap_edit_wrap').slideToggle(150, function() {
        if ($('#draf_bap_edit_wrap').is(':visible')) {
          $('#btnToggleEditBap').html('<i class="fa fa-eye mr-1"></i> Tutup Editor');
          $('#draf_bap').focus();
        } else {
          $('#btnToggleEditBap').html('<i class="fa fa-pencil mr-1"></i> Edit Draf');
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

    $('#form').submit(function(e) {
      e.preventDefault();
      $('#btnSave').html('<i class="fa fa-spinner fa-spin mr-1"></i> Menyimpan...');
      $('#btnSave').attr('disabled', true);
      var url = "<?php echo site_url('c_rencana_pertemuan/ajax_update') ?>";
      var method = 'Rencana Perkuliahan & Eksekusi OBE Berhasil Disimpan';

      $.ajax({
        url: url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data) {

          if (data.status) {
            $('#modal_form').modal('hide');
            swalert(method);
            reload_table();
          } else {
            if (data.errors) {
              $.each(data.errors, function(key, value) {
                if (value !== '') {
                  $('[name="' + key + '"]').parent().addClass('has-error');
                  $('[name="' + key + '"]').next().text(value);
                }
              });
            }
          }
          $('#btnSave').html('<i class="fa fa-save mr-1"></i> Simpan Perubahan');
          $('#btnSave').attr('disabled', false);

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error adding / update data');
          $('#btnSave').html('<i class="fa fa-save mr-1"></i> Simpan Perubahan');
          $('#btnSave').attr('disabled', false);
        }
      });
    });

    // Toggle Realisasi Checkbox
    $('#is_realisasi').on('change click', function() {
      enable_cb();
    });

  });

  function enable_cb() {
    var isChecked = $('#is_realisasi').is(':checked');
    if (isChecked) {
      $('#realisasi_status_badge').html('<span class="label label-success" style="font-size: 11px; padding: 3px 8px; border-radius: 3px;"><i class="fa fa-check-circle mr-1"></i> Terlaksana</span>');
      $('#realisasi_desc_text').html('<span class="text-success" style="font-size: 12px; font-weight: 600;"><i class="fa fa-check mr-1"></i> Perkuliahan telah dilaksanakan sesuai target capaian OBE.</span>');
      var currentTgl = $('[name="tanggal_realisasi"]').val();
      if (!currentTgl || currentTgl === '0000-00-00') {
        $('[name="tanggal_realisasi"]').val($('[name="tanggal_rencana"]').val());
      }
      $(".tgl_realisasi").stop(true, true).slideDown(150);
    } else {
      $('#realisasi_status_badge').html('<span class="label label-default" style="font-size: 11px; padding: 3px 8px; background: #95a5a6; border-radius: 3px;"><i class="fa fa-clock-o mr-1"></i> Rencana</span>');
      $('#realisasi_desc_text').html('<span class="text-muted" style="font-size: 12px;"><i class="fa fa-info-circle mr-1"></i> Sesi ini masih berstatus rencana (belum terlaksana). Centang jika sudah selesai.</span>');
      $(".tgl_realisasi").stop(true, true).slideUp(150);
    }
  }

  function renderSkenarioCards(raw) {
    if (!raw || raw.trim() === '') {
      $('#skenario_visual_box').html(
        '<div class="text-muted text-center py-3 border rounded bg-light" style="font-size: 13px;">' +
        'Belum ada skenario pembelajaran. Klik tombol <b>Edit Teks</b> untuk menambahkan.</div>'
      );
      return;
    }

    var clean = raw.trim();
    var hasStages = /Tahap\s+/i.test(clean);

    if (hasStages) {
      var stageBlocks = clean.split(/(?:^|\n)\s*(?:\d+[\.\)]\s*)?\*{0,2}(?=Tahap\s+)/i);
      var html = '';

      for (var s = 0; s < stageBlocks.length; s++) {
        var stageText = stageBlocks[s].trim();
        if (!stageText || /^📋|Skenario\s*Sintaks/i.test(stageText)) continue;

        var lines = stageText.split(/\r?\n/);
        var firstLine = lines[0].trim();
        var restLines = lines.slice(1);

        // Parse title and duration
        var title = firstLine;
        var duration = '';
        var titleMatch = firstLine.match(/^([^\(\:\*]+)(?:\s*\(([^\)]+)\))?/i);
        if (titleMatch) {
          title = titleMatch[1].replace(/[\*\:]/g, '').trim();
          if (titleMatch[2]) {
            duration = titleMatch[2].replace(/[\*\:]/g, '').trim();
          }
        }

        // Collect clean list items into array
        var itemsArr = [];
        for (var j = 0; j < restLines.length; j++) {
          var line = restLines[j].trim();
          if (!line || line === '*' || line === '-' || line === ':') continue;

          var cleanItem = line.replace(/^(?:[\-\*•]|\d+[\.\)])\s*/u, '');
          cleanItem = cleanItem.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>').trim();
          cleanItem = cleanItem.replace(/^\*+\s*/, '').trim();
          if (cleanItem) {
            itemsArr.push(cleanItem);
          }
        }

        if (itemsArr.length === 0) {
          var fallbackLine = restLines.join(' ').replace(/[\*]/g, '').trim();
          if (fallbackLine) {
            itemsArr.push(fallbackLine);
          }
        }

        var stageBodyHtml = '';
        if (itemsArr.length > 1) {
          var listItems = itemsArr.map(function(it) {
            return '<li style="margin-bottom: 6px; line-height: 1.6; color: #333333; padding-left: 4px;">' + it + '</li>';
          }).join('');
          stageBodyHtml = '<ol style="margin: 0; padding-left: 20px; font-size: 13.5px; list-style-type: decimal !important;">' + listItems + '</ol>';
        } else if (itemsArr.length === 1) {
          stageBodyHtml = '<p style="margin: 0; font-size: 13.5px; line-height: 1.6; color: #333333;">' + itemsArr[0] + '</p>';
        } else {
          stageBodyHtml = '<span class="text-muted font-italic">-</span>';
        }

        html += '<div class="card mb-2" style="border: 1px solid #dee2e6; border-radius: 4px; box-shadow: none;">' +
          '<div class="card-header py-2 px-3 d-flex justify-content-between align-items-center" style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">' +
            '<span style="font-weight: 600; font-size: 13.5px; color: #212529;">' + title + '</span>' +
            (duration ? '<span class="badge badge-secondary" style="font-size: 11px; font-weight: 500;">' + duration + '</span>' : '') +
          '</div>' +
          '<div class="card-body p-3" style="background: #ffffff;">' +
            stageBodyHtml +
          '</div>' +
        '</div>';
      }

      if (html !== '') {
        $('#skenario_visual_box').html(html);
        return;
      }
    }

    // Fallback for non-stage markdown
    var fallbackLines = clean.split(/\r?\n/);
    var fallbackArr = [];
    for (var f = 0; f < fallbackLines.length; f++) {
      var fLine = fallbackLines[f].trim();
      if (!fLine || fLine === '*' || fLine === '-') continue;
      var fClean = fLine.replace(/^(?:[\-\*•]|\d+[\.\)])\s*/u, '');
      fClean = fClean.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>').trim();
      fClean = fClean.replace(/^\*+\s*/, '').trim();
      if (fClean) {
        fallbackArr.push(fClean);
      }
    }

    var fallbackHtml = '';
    if (fallbackArr.length > 1) {
      var fbList = fallbackArr.map(function(it) {
        return '<li style="margin-bottom: 6px; line-height: 1.6; color: #333333; padding-left: 4px;">' + it + '</li>';
      }).join('');
      fallbackHtml = '<ol style="margin: 0; padding-left: 20px; font-size: 13.5px; list-style-type: decimal !important;">' + fbList + '</ol>';
    } else if (fallbackArr.length === 1) {
      fallbackHtml = '<p style="margin: 0; font-size: 13.5px; line-height: 1.6; color: #333333;">' + fallbackArr[0] + '</p>';
    }

    $('#skenario_visual_box').html(
      '<div class="card" style="border: 1px solid #dee2e6; border-radius: 4px; box-shadow: none;">' +
        '<div class="card-body p-3" style="background: #ffffff;">' +
          fallbackHtml +
        '</div>' +
      '</div>'
    );
  }

  function renderBapPreview(text) {
    if (!text || text.trim() === '') {
      $('#bap_preview_box').html('<span class="text-muted font-italic">Draf BAP belum tersedia. Klik "Edit Draf" untuk mengisi narasi BAP faktual.</span>');
    } else {
      $('#bap_preview_box').html('<div style="color: #212529; font-size: 13px; line-height: 1.6;">' + $('<div>').text(text).html().replace(/\n/g, '<br>') + '</div>');
    }
  }

  var currentSubcpmkOptions = [];

  function renderSubcpmkChecklist(options, currentValue) {
    currentSubcpmkOptions = options || [];
    var curVal = currentValue ? currentValue.trim() : '';

    if (!options || options.length === 0) {
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
        if (opt.code && curVal.indexOf(opt.code) !== -1) {
          isChecked = true;
        } else if (opt.desc && curVal.indexOf(opt.desc) !== -1) {
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
    'Diskusi Kelompok',
    'Case Method',
    'Project-Based Learning (PjBL)',
    'Problem-Based Learning (PBL)',
    'Praktik / Simulasi',
    'Presentasi Mahasiswa',
    'Belajar Mandiri'
  ];

  function renderSclChips(currentValue) {
    var selectedArr = [];
    if (currentValue) {
      selectedArr = currentValue.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
    }

    var html = '';
    for (var i = 0; i < presetSclMethods.length; i++) {
      var m = presetSclMethods[i];
      var isSelected = false;
      for (var j = 0; j < selectedArr.length; j++) {
        if (selectedArr[j].toLowerCase() === m.toLowerCase()) {
          isSelected = true;
        }
      }

      var activeClass = isSelected ? 'scl-chip-active' : 'scl-chip-inactive';
      var icon = isSelected ? '<i class="fa fa-check mr-1"></i> ' : '<i class="fa fa-plus mr-1" style="opacity: 0.6;"></i> ';

      html += '<button type="button" class="btn btn-xs scl-chip ' + activeClass + '" data-val="' + m + '" onclick="toggleSclChip(this)">' +
        icon + m +
      '</button>';
    }

    // Check if there are custom methods outside presets
    var customArr = [];
    for (var k = 0; k < selectedArr.length; k++) {
      var found = false;
      for (var p = 0; p < presetSclMethods.length; p++) {
        if (selectedArr[k].toLowerCase() === presetSclMethods[p].toLowerCase()) {
          found = true;
          break;
        }
      }
      if (!found) {
        customArr.push(selectedArr[k]);
      }
    }

    for (var c = 0; c < customArr.length; c++) {
      html += '<button type="button" class="btn btn-xs scl-chip scl-chip-active" data-val="' + customArr[c] + '" onclick="toggleSclChip(this)">' +
        '<i class="fa fa-check mr-1"></i> ' + customArr[c] +
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

  function copyBapDraft() {
    var bapText = $('#draf_bap').val();
    if (!bapText || bapText.trim() === '') {
      Swal({
        title: 'Perhatian',
        text: 'Draf BAP masih kosong.',
        type: 'warning'
      });
      return;
    }
    navigator.clipboard.writeText(bapText).then(function() {
      Swal({
        title: 'Tersalin!',
        text: 'Draf BAP berhasil disalin ke clipboard.',
        type: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    }).catch(function() {
      var tempInput = document.createElement("textarea");
      tempInput.value = bapText;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      Swal({
        title: 'Tersalin!',
        text: 'Draf BAP berhasil disalin ke clipboard.',
        type: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    });
  }

  function reload_table() {
    table.ajax.reload(null, false);
  }

  function edit_rencana(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#sub_cpmk_edit_wrap').hide();
    $('#btnToggleEditSubcpmk').html('<i class="fa fa-pencil mr-1 text-warning"></i> Edit Teks');
    $('#berita_acara_rencana_edit_wrap').hide();
    $('#btnToggleEditSkenario').html('<i class="fa fa-pencil mr-1 text-warning"></i> Edit Teks');
    $('#draf_bap_edit_wrap').hide();
    $('#btnToggleEditBap').html('<i class="fa fa-pencil mr-1 text-warning"></i> Edit Draf');

    $.ajax({
      url: "<?php echo site_url('c_rencana_pertemuan/ajax_edit') ?>/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {

        $('[name="uniq"]').val(data.uniq);
        $('[name="tanggal_rencana"]').val(data.tanggal_rencana);
        $('[name="jam_mulai"]').val(data.jam_mulai);
        $('[name="jam_selesai"]').val(data.jam_selesai);
        $('[name="pertemuan"]').val(data.pertemuan);
        $('[name="jam_teori"]').val(data.jam_teori);
        $('[name="jam_praktek"]').val(data.jam_praktek);
        $('[name="kategori_pertemuan"]').val(data.kategori_pertemuan);

        // Target Sub-CPMK, Metode, & Indikator
        $('[name="sub_cpmk"]').val(data.sub_cpmk || '');
        renderSubcpmkChecklist(data.sub_cpmk_options || [], data.sub_cpmk || '');
        $('[name="metode_pembelajaran"]').val(data.metode_pembelajaran || '');
        renderSclChips(data.metode_pembelajaran || '');
        $('[name="indikator_evaluasi"]').val(data.indikator_evaluasi || '');

        // Bahan Kajian / Bacaan Materi
        $('[name="bacaan_materi"]').val(data.bacaan_materi || '');

        // Skenario SCL
        $('[name="berita_acara_rencana"]').val(data.berita_acara_rencana || '');
        renderSkenarioCards(data.berita_acara_rencana);

        // Draf BAP & Catatan Refleksi
        $('[name="draf_bap"]').val(data.draf_bap || '');
        renderBapPreview(data.draf_bap);
        $('[name="catatan_refleksi"]').val(data.catatan_refleksi || '');

        // Realisasi
        $('[name="is_realisasi"]').val(data.is_realisasi);

        if (data.is_realisasi == 1 || data.is_realisasi == '1') {
          $('#is_realisasi').prop("checked", true);
          var tglReal = (data.tanggal_realisasi && data.tanggal_realisasi !== '0000-00-00') ? data.tanggal_realisasi : (data.tanggal_rencana || '');
          $('[name="tanggal_realisasi"]').val(tglReal);
        } else {
          $('#is_realisasi').prop("checked", false);
          $('[name="tanggal_realisasi"]').val((data.tanggal_realisasi && data.tanggal_realisasi !== '0000-00-00') ? data.tanggal_realisasi : '');
        }
        enable_cb();

        $('#modal_form').modal({
          backdrop: 'static',
          keyboard: false
        });

        $('#modal_meet_badge').text('Pertemuan Ke-' + data.pertemuan);
        var subText = (data.nm_kls ? ('[' + data.nm_kls + '] ') : '') + (data.nm_mk || '');
        $('.modal-sub').text(subText || 'Mata Kuliah');

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function format_tanggal(tgl) {
    if (Date.parse(tgl)) {
      var date = new Date(tgl);
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
    } else {
      return '';
    }
  }

  function swalert(method) {
    Swal({
      title: 'Berhasil',
      html: method,
      type: 'success'
    });
  };
</script>

<div class="modal fade" id="modal_form" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" style="max-width: 920px !important;">
    <div class="modal-content" style="border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background: linear-gradient(to right, #1e3c72, #2a5298); color: #ffffff; padding: 14px 18px; border-top-left-radius: 4px; border-top-right-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
        <div style="flex: 1; padding-right: 15px;">
          <h5 class="modal-title font-weight-bold mb-0" style="color: #ffffff; margin: 0; font-size: 16px; display: flex; align-items: center; flex-wrap: wrap;">
            <span>Eksekusi & Realisasi Perkuliahan OBE</span>
            <span class="badge badge-light" id="modal_meet_badge" style="background: #ffffff; color: #1e3c72; font-weight: 700; font-size: 12px; margin-left: 8px; padding: 4px 8px; border-radius: 3px;">Pertemuan</span>
          </h5>
          <div class="modal-sub" style="font-size: 12.5px; color: rgba(255,255,255,0.85); margin-top: 4px; font-weight: 500;"></div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.9; font-size: 26px; font-weight: 300; line-height: 1; background: transparent; border: 0; cursor: pointer; padding: 0; margin: 0; outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-3" style="background: #f4f6f9; padding: 15px;">

        <?php
        $attributes = ['class' => 'form-horizontal', 'id' => 'form'];
        echo form_open('#', $attributes);
        ?>
        <input type="hidden" value="" name="uniq" />
        <input type="hidden" value="" name="pertemuan" />

        <!-- 1. TARGET CAPAIAN & JADWAL OBE -->
        <div class="form-section-box">
          <div class="form-section-title">
            <span>1. Target Capaian & Parameter Pembelajaran (OBE)</span>
          </div>

          <!-- Hidden T/P inputs & SCL field -->
          <input type="hidden" id="jam_teori" name="jam_teori">
          <input type="hidden" id="jam_praktek" name="jam_praktek">
          <input type="hidden" id="metode_pembelajaran" name="metode_pembelajaran">
          
          <!-- Baris 1: Tanggal Rencana & Jam Perkuliahan -->
          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-6 col-sm-6" style="margin-bottom: 6px;">
              <label class="small text-muted font-weight-bold" style="font-size: 11.5px; margin-bottom: 3px; display: block;">Tanggal Rencana</label>
              <input type="text" class="form-control input-sm" id="tanggal_rencana" name="tanggal_rencana" placeholder="YYYY-MM-DD">
            </div>
            <div class="col-md-6 col-sm-6" style="margin-bottom: 6px;">
              <label class="small text-muted font-weight-bold" style="font-size: 11.5px; margin-bottom: 3px; display: block;">Jam Perkuliahan</label>
              <div style="display: flex; align-items: center;">
                <input type="text" class="form-control input-sm text-center" id="jam_mulai" name="jam_mulai" placeholder="13:00" style="margin-right: 6px;">
                <span class="text-muted small" style="padding: 0 4px; font-weight: bold;">sd</span>
                <input type="text" class="form-control input-sm text-center" id="jam_selesai" name="jam_selesai" placeholder="17:00" style="margin-left: 6px;">
              </div>
            </div>
          </div>

          <!-- Baris 2: Bentuk & Metode SCL (Di bawah Tanggal dan Jam) -->
          <div class="row" style="margin-bottom: 12px;">
            <div class="col-md-12">
              <label class="small text-muted font-weight-bold" style="font-size: 11.5px; margin-bottom: 4px; display: block;">
                Bentuk & Metode SCL (Klik untuk memilih/checklist):
              </label>
              <div id="scl_checklist_container" style="display: flex; flex-wrap: wrap; align-items: center; gap: 4px; min-height: 30px;">
                <!-- SCL Chips rendered via JS -->
              </div>
            </div>
          </div>

          <!-- Baris 3: Target Sub-CPMK & Bahan Kajian -->
          <div class="row">
            <div class="col-md-7" style="margin-bottom: 6px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <label class="small text-dark font-weight-bold" style="font-size: 12px; margin-bottom: 0;">
                  🎯 Target Sub-CPMK (Pilih / Checklist):
                </label>
                <button type="button" class="btn btn-xs btn-default" id="btnToggleEditSubcpmk" style="padding: 1px 8px; font-size: 11.5px; border: 1px solid #ced4da; background: #ffffff;">
                  <i class="fa fa-pencil mr-1 text-warning"></i> Edit Teks
                </button>
              </div>
              <div id="subcpmk_checklist_container" class="subcpmk-container">
                <!-- Sub-CPMK checkboxes rendered via JS -->
              </div>
              <div id="sub_cpmk_edit_wrap" style="display: none; margin-top: 6px;">
                <textarea name="sub_cpmk" id="sub_cpmk" rows="3" class="form-control input-sm" placeholder="Rumusan Sub-CPMK yang ditargetkan pada sesi pertemuan ini..."></textarea>
              </div>
            </div>
            <div class="col-md-5" style="margin-bottom: 6px;">
              <label class="small text-dark font-weight-bold" style="font-size: 12px; margin-bottom: 4px; display: block;">
                📚 Bahan Kajian & Referensi Belajar:
              </label>
              <textarea name="bacaan_materi" id="bacaan_materi" rows="6" class="form-control input-sm" placeholder="Topik materi bahasan dan rujukan bab/buku..." style="min-height: 180px;"></textarea>
            </div>
          </div>
        </div>

        <!-- 2. STRATEGI & SKENARIO PEMBELAJARAN SCL -->
        <div class="form-section-box">
          <div class="form-section-title">
            <span>2. Skenario Langkah Pembelajaran (Sintaks SCL di Kelas)</span>
            <button type="button" class="btn btn-xs btn-default" id="btnToggleEditSkenario" style="padding: 2px 8px; font-size: 12px; border: 1px solid #ced4da;">
              <i class="fa fa-pencil mr-1 text-warning"></i> Edit Teks
            </button>
          </div>
          
          <!-- Visual Structured Step Cards -->
          <div id="skenario_visual_box"></div>

          <!-- Collapsible Textarea Editor -->
          <div id="berita_acara_rencana_edit_wrap" style="display: none; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #ced4da;">
            <label class="small text-muted font-weight-bold" style="font-size: 12px; margin-bottom: 4px; display: block;">Editor Teks Skenario:</label>
            <textarea name="berita_acara_rencana" id="berita_acara_rencana" rows="6" class="form-control input-sm" placeholder="Tuliskan skenario langkah pembelajaran..."></textarea>
          </div>
        </div>

        <!-- 3. ASESMEN & KRITERIA EVALUASI -->
        <div class="form-section-box">
          <div class="form-section-title">
            <span>3. Asesmen & Indikator Ketercapaian Pertemuan</span>
          </div>
          <div>
            <textarea name="indikator_evaluasi" id="indikator_evaluasi" rows="2" class="form-control input-sm" placeholder="Bentuk asesmen & kriteria penilaian (misal: Rubrik Partisipasi Diskusi & Ketepatan Analisis Kasus, Bobot: 3%)..."></textarea>
          </div>
        </div>

        <!-- 4. REALISASI PERKULIAHAN, BAP & REFLEKSI DOSEN -->
        <div class="form-section-box mb-0">
          <div class="form-section-title">
            <span>4. Realisasi Perkuliahan, BAP Faktual & Refleksi Kelas</span>
          </div>

          <!-- Realisasi Checkbox & Status Card -->
          <div style="background: #ffffff; border: 1px solid #d2d6de; border-radius: 4px; padding: 12px 15px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="row" style="margin: 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
              
              <!-- Left: Checkbox, Label, Status Badge & Helper -->
              <div style="padding: 4px 0; display: flex; align-items: flex-start;">
                <input type="checkbox" id="is_realisasi" name="is_realisasi" value="1" style="width: 20px; height: 20px; margin: 2px 10px 0 0; cursor: pointer; vertical-align: middle;">
                <div>
                  <div style="display: flex; align-items: center; flex-wrap: wrap;">
                    <label for="is_realisasi" style="margin: 0; font-size: 13.5px; font-weight: 700; color: #2c3e50; cursor: pointer;">
                      Tandai Perkuliahan Sudah Terlaksana (Realisasi)
                    </label>
                    <span id="realisasi_status_badge" style="margin-left: 8px;"></span>
                  </div>
                  <div id="realisasi_desc_text" style="margin-top: 2px;"></div>
                </div>
              </div>
              
              <!-- Right: Tanggal Realisasi Input Group (Toggled) -->
              <div class="tgl_realisasi" style="display: none; padding: 4px 0; min-width: 260px;">
                <div class="input-group input-group-sm">
                  <span class="input-group-addon" style="background: #2c3e50; color: #ffffff; font-weight: 600; font-size: 12px; border-color: #2c3e50;">
                    <i class="fa fa-calendar" style="margin-right: 4px;"></i> Tgl Realisasi
                  </span>
                  <input type="text" class="form-control" name="tanggal_realisasi" id="tanggal_realisasi" placeholder="YYYY-MM-DD" style="font-weight: 600; color: #2c3e50; background: #ffffff; font-size: 12.5px;">
                </div>
              </div>

            </div>
          </div>

          <!-- Draf BAP Faktual -->
          <div style="margin-bottom: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <label style="font-size: 12.5px; font-weight: 700; color: #2c3e50; margin-bottom: 0;">
                📝 Draf Narasi Berita Acara Perkuliahan (BAP Faktual):
              </label>
              <div>
                <button type="button" class="btn btn-xs btn-default" id="btnCopyDrafBap" onclick="copyBapDraft()" title="Salin ke Clipboard" style="padding: 2px 10px; font-size: 12px; margin-right: 4px; border: 1px solid #ced4da; background: #ffffff; font-weight: 600;">
                  <i class="fa fa-copy mr-1 text-primary"></i> Salin Draf
                </button>
                <button type="button" class="btn btn-xs btn-default" id="btnToggleEditBap" style="padding: 2px 10px; font-size: 12px; border: 1px solid #ced4da; background: #ffffff; font-weight: 600;">
                  <i class="fa fa-pencil mr-1 text-warning"></i> Edit Draf
                </button>
              </div>
            </div>
            <div id="bap_preview_box" style="padding: 12px 15px; background: #ffffff; border: 1px solid #d2d6de; border-radius: 4px; min-height: 48px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);"></div>
            <div id="draf_bap_edit_wrap" style="display: none; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ced4da;">
              <textarea name="draf_bap" id="draf_bap" rows="3" class="form-control input-sm" placeholder="Tuliskan narasi draft evaluasi pertemuan..." style="font-size: 13px;"></textarea>
            </div>
          </div>

          <!-- Catatan Refleksi Dosen -->
          <div>
            <label style="font-size: 12.5px; font-weight: 700; color: #2c3e50; margin-bottom: 4px; display: block;">
              💡 Catatan Refleksi Dosen (Evaluasi Pemahaman & Dinamika Kelas):
            </label>
            <textarea name="catatan_refleksi" id="catatan_refleksi" rows="2" class="form-control input-sm" placeholder="Catatan evaluasi kelas, daya serap mahasiswa, kendala teknis, atau materi yang perlu penguatan pada pertemuan berikutnya..." style="font-size: 13px;"></textarea>
            <small style="color: #6c757d; font-size: 11.5px; display: block; margin-top: 4px;">
              Catatan ini bermanfaat untuk evaluasi mutu berkelanjutan (PPEPP/SPMI) dan Penelitian Tindakan Kelas (PTK).
            </small>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="background: #f8f9fa; padding: 10px 15px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight: 600;">
          Tutup
        </button>
        <button type="submit" class="btn btn-primary btn-sm" id="btnSave" style="font-weight: 700; padding: 6px 16px;">
          <i class="fa fa-save mr-1"></i> Simpan Perubahan
        </button>
      </div>

      <?php
      echo form_close();
      ?>
    </div>
  </div>
</div>

