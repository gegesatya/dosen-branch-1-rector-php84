<script type="text/javascript">
  var save_method;
  var table;

  var TableAjax = function() {
    var handleRecords = function() {
      table = $('#datatable_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "pagingType": "full_numbers",
        "order": [],
        "ajax": {
          "url": "<?php echo site_url('C_skema_nilai/ajax_list') ?>",
          "type": "POST",
          data: function(dtRequest) {
            dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
            return dtRequest;
          },
        },

        "columnDefs": [{
          "targets": [-1, 0, 1, 2, 3, 4],
          "orderable": false,
        }, ],

      });
    }

    return {
      init: function() {
        handleRecords();
      }

    };

  }();

  function reload_table() {
    table.ajax.reload(null, false);
  var currentObeBlueprint = null;

  $(function() {
    $(".form-control").change(function() {
      $(this).parent().parent().removeClass('has-error');
      if ($(this).next().hasClass("help-block")) {
        $(this).next().empty();
      } else {
        $(this).parent().next().empty();
      }
    });

    $(".komponen-reguler").on('input change', function(e) {
      hitungTotalSkemaNilai();
    });
  });

  function updateSlotActiveStates() {
    var keys = ['sikap', 'quiz', 'tugas_terstruktur', 'uts', 'uas', 'proyek', 'unjuk_kinerja', 'portofolio', 'produk', 'presentasi'];
    keys.forEach(function(k) {
      var val = Number($('[name="' + k + '"]').val()) || 0;
      var $card = $('#slot_card_' + k);
      if (val > 0) {
        $card.addClass('has-value');
      } else {
        $card.removeClass('has-value');
      }
    });
  }

  function hitungTotalSkemaNilai() {
    var s_sikap = Number($('[name="sikap"]').val()) || 0;
    var s_quiz = Number($('[name="quiz"]').val()) || 0;
    var s_tugas = Number($('[name="tugas_terstruktur"]').val()) || 0;
    var s_uts = Number($('[name="uts"]').val()) || 0;
    var s_uas = Number($('[name="uas"]').val()) || 0;
    var s_kinerja = Number($('[name="unjuk_kinerja"]').val()) || 0;
    var s_proyek = Number($('[name="proyek"]').val()) || 0;
    var s_porto = Number($('[name="portofolio"]').val()) || 0;
    var s_produk = Number($('[name="produk"]').val()) || 0;
    var s_pres = Number($('[name="presentasi"]').val()) || 0;

    var total = s_sikap + s_quiz + s_tugas + s_uts + s_uas + s_kinerja + s_proyek + s_porto + s_produk + s_pres;

    var clampedProgress = Math.min(total, 100);
    $('#skema_progress_bar').css('width', clampedProgress + '%');
    $('#skema_total_text').text(total + '%');

    var $box = $('#jumlah_total');
    if (total === 100) {
      $('#skema_progress_bar').css('background-color', '#10b981');
      $box.removeClass('alert-info alert-warning alert-danger').addClass('alert-success')
        .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#166534;"><i class="fa fa-check-circle"></i> Total Bobot: 100%</strong> — <span style="font-size:12px; color:#15803d;">Sesuai Standar SPMI &amp; Siap Disimpan</span></div><span class="badge" style="background:#16a34a; font-size:11px; padding:4px 8px;"><i class="fa fa-lock"></i> Valid 100%</span></div>');
      $('#btnSave').prop('disabled', false).css({'opacity': '1', 'cursor': 'pointer'});
    } else if (total > 100) {
      var over = total - 100;
      $('#skema_progress_bar').css('background-color', '#ef4444');
      $box.removeClass('alert-info alert-success alert-warning').addClass('alert-danger')
        .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#991b1b;"><i class="fa fa-times-circle"></i> Total Bobot: ' + total + '%</strong> — <span style="font-size:12px; color:#b91c1c;">Melebihi 100% (Kurangi ' + over + '%)</span></div><span class="badge" style="background:#dc2626; font-size:11px; padding:4px 8px;">Over +' + over + '%</span></div>');
      $('#btnSave').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
    } else {
      var under = 100 - total;
      $('#skema_progress_bar').css('background-color', '#f59e0b');
      $box.removeClass('alert-info alert-success alert-danger').addClass('alert-warning')
        .html('<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;"><div><strong style="font-size:13px; color:#92400e;"><i class="fa fa-exclamation-triangle"></i> Total Bobot: ' + total + '%</strong> — <span style="font-size:12px; color:#b45309;">Belum mencapai 100% (Kurang ' + under + '% lagi)</span></div><span class="badge" style="background:#d97706; font-size:11px; padding:4px 8px;">Kurang -' + under + '%</span></div>');
      $('#btnSave').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
    }

    updateSlotActiveStates();
  }

  /* Drag & Drop HTML5 Handlers */
  function handleObeDragStart(ev, cardId, percent, suggestedTarget) {
    var dataPayload = JSON.stringify({
      cardId: cardId,
      percent: percent,
      suggestedTarget: suggestedTarget
    });
    ev.dataTransfer.setData("text/plain", dataPayload);
    ev.dataTransfer.effectAllowed = "copy";
    $(ev.currentTarget).addClass('dragging');
  }

  function handleObeDragEnd(ev) {
    $(ev.currentTarget).removeClass('dragging');
  }

  function handleObeDragOver(ev) {
    ev.preventDefault();
    ev.dataTransfer.dropEffect = "copy";
    $(ev.currentTarget).addClass('slot-drag-hover');
  }

  function handleObeDragLeave(ev) {
    $(ev.currentTarget).removeClass('slot-drag-hover');
  }

  function handleObeDrop(ev, targetSlotKey) {
    ev.preventDefault();
    $(ev.currentTarget).removeClass('slot-drag-hover');
    var raw = ev.dataTransfer.getData("text/plain");
    if (!raw) return;
    try {
      var payload = JSON.parse(raw);
      $('[name="' + targetSlotKey + '"]').val(payload.percent).trigger('input');
      
      var $slot = $('#slot_card_' + targetSlotKey);
      $slot.addClass('slot-pulse');
      setTimeout(function() { $slot.removeClass('slot-pulse'); }, 600);
    } catch(e) {
      console.error("Drop error:", e);
    }
  }

  function quickAssignObeCard(targetSlotKey, percent) {
    $('[name="' + targetSlotKey + '"]').val(percent).trigger('input');
    var $slot = $('#slot_card_' + targetSlotKey);
    $slot.addClass('slot-pulse');
    setTimeout(function() { $slot.removeClass('slot-pulse'); }, 600);
  }

  function clearSlot(targetSlotKey) {
    $('[name="' + targetSlotKey + '"]').val('').trigger('input');
  }

  function autoMapObeToSiakad() {
    if (!currentObeBlueprint || !currentObeBlueprint.auto_map_values) {
      alert('Data matriks asesmen OBE tidak tersedia.');
      return;
    }
    var autoMap = currentObeBlueprint.auto_map_values;
    for (var key in autoMap) {
      if (autoMap.hasOwnProperty(key)) {
        $('[name="' + key + '"]').val(autoMap[key] > 0 ? autoMap[key] : '');
      }
    }
    hitungTotalSkemaNilai();

    $('.siakad-slot-card').addClass('slot-pulse');
    setTimeout(function() {
      $('.siakad-slot-card').removeClass('slot-pulse');
    }, 600);
  }

  function resetSiakadSlots() {
    var keys = ['sikap', 'quiz', 'tugas_terstruktur', 'uts', 'uas', 'unjuk_kinerja', 'proyek', 'portofolio', 'produk', 'presentasi'];
    keys.forEach(function(k) {
      $('[name="' + k + '"]').val('');
    });
    hitungTotalSkemaNilai();
  }

  function edit_skema_nilai(id) {
    save_method = 'update_skema_nilai';
    $('#form_skema_nilai')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#btnSave').html('<i class="fa fa-check"></i> Simpan Skema Nilai');
    $('#btnSave').attr('disabled', false);

    $.ajax({
      url: "<?php echo site_url('C_skema_nilai/ajax_edit/') ?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        if (data.status === false || data.need_rps) {
          if (typeof swal === 'function') {
            swal({
              title: 'RPS Belum Diterapkan',
              text: data.msg || 'Jadwal perkuliahan ini belum memiliki RPS / rencana pertemuan aktif. Silakan buat atau terapkan RPS OBE terlebih dahulu di Studio RPS.',
              type: 'warning',
              confirmButtonText: 'Buka Studio RPS',
              showCancelButton: true,
              cancelButtonText: 'Tutup'
            }).then(function(result) {
              if (result && result.value) {
                window.open('<?php echo site_url("C_rps_generator"); ?>', '_blank');
              }
            });
          } else {
            alert(data.msg || 'Harap buat atau terapkan RPS OBE terlebih dahulu.');
          }
          return;
        }

        $('[name="id"]').val(data.id);
        $('[name="tag_name"]').val(data.tag_name);
        $('[name="sikap"]').val(data.sikap || '');
        $('[name="quiz"]').val(data.quiz || '');
        $('[name="tugas_terstruktur"]').val(data.tugas_terstruktur || '');
        $('[name="uts"]').val(data.uts || '');
        $('[name="uas"]').val(data.uas || '');
        $('[name="unjuk_kinerja"]').val(data.unjuk_kinerja || '');
        $('[name="proyek"]').val(data.proyek || '');
        $('[name="portofolio"]').val(data.portofolio || '');
        $('[name="produk"]').val(data.produk || '');
        $('[name="presentasi"]').val(data.presentasi || '');

        currentObeBlueprint = data.obe_blueprint || null;

        // Render Kartu Komponen RPS OBE
        var $cardsContainer = $('#obe_cards_container');
        $cardsContainer.empty();

        if (currentObeBlueprint && currentObeBlueprint.cards && currentObeBlueprint.cards.length > 0) {
          currentObeBlueprint.cards.forEach(function(c) {
            var cTitle = c.title || c.name || 'Komponen';
            var cWeight = (c.weight !== undefined) ? c.weight : (c.percent || 0);
            var cTarget = c.target_key || c.suggested_target || 'tugas_terstruktur';
            var cTargetLabel = c.target_label || c.suggested_target_label || 'Tugas Terstruktur';
            var cBreakdown = c.breakdown ? '<div style="font-size:10px; color:#64748b; margin-top:3px; line-height:1.2; font-style:italic;"><i class="fa fa-info-circle"></i> ' + c.breakdown + '</div>' : '';

            var cardHtml = 
              '<div class="obe-drag-card" draggable="true" ' +
              'ondragstart="handleObeDragStart(event, \'' + c.id + '\', ' + cWeight + ', \'' + cTarget + '\')" ' +
              'ondragend="handleObeDragEnd(event)">' +
                '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                  '<div style="font-weight:700; font-size:12.5px; color:#0f172a; display:flex; align-items:center; gap:6px;">' +
                    '<i class="fa fa-arrows" style="color:#94a3b8; font-size:11px; cursor:grab;" title="Seret kartu ini"></i> ' +
                    '<i class="' + c.icon + '" style="color:' + c.color + ';"></i> ' +
                    '<span>' + cTitle + '</span>' +
                  '</div>' +
                  '<span class="badge" style="background:' + c.color + '; font-size:12px; font-weight:800; padding:3px 8px;">' + cWeight + '%</span>' +
                '</div>' +
                cBreakdown +
                '<div style="font-size:10.5px; color:#475569; margin-top:6px; display:flex; justify-content:space-between; align-items:center;">' +
                  '<span><i class="fa fa-bullseye" style="color:' + c.color + ';"></i> Saran: <strong>' + cTargetLabel + '</strong></span>' +
                  '<button type="button" class="btn btn-default btn-xs" onclick="quickAssignObeCard(\'' + cTarget + '\', ' + cWeight + ')" title="Terapkan langsung ke ' + cTargetLabel + '" style="padding:2px 8px; font-size:10.5px; border-radius:4px; background:' + c.bg + '; color:' + c.color + '; border-color:' + c.color + '; font-weight:700;">' +
                    'Petakan &rarr;' +
                  '</button>' +
                '</div>' +
              '</div>';
            $cardsContainer.append(cardHtml);
          });

          $('#obe_blueprint_total_badge').text('Total ' + (currentObeBlueprint.total_sum || 100) + '%');
          $('#obe_blueprint_table_wrap').html(currentObeBlueprint.table_html || '<p class="text-muted">Tidak ada tabel.</p>');
        } else {
          $cardsContainer.html(
            '<div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:16px; text-align:center; color:#64748b; font-size:12px;">' +
              '<i class="fa fa-info-circle" style="font-size:20px; color:#94a3b8; margin-bottom:6px; display:block;"></i>' +
              'Belum ada matriks asesmen terdeteksi pada RPS mata kuliah ini.<br>Anda dapat mengisi bobot secara manual pada 10 slot di sebelah kanan.' +
            '</div>'
          );
          $('#obe_blueprint_total_badge').text('Default SPMI');
          $('#obe_blueprint_table_wrap').html('<p class="text-muted">Matriks asesmen RPS tidak tersedia.</p>');
        }

        if (currentObeBlueprint && currentObeBlueprint.has_obe) {
          $('#obe_status_tag').html('<span class="badge" style="background:#16a34a; font-size:11px; padding:5px 10px; border-radius:20px;"><i class="fa fa-check-circle"></i> RPS OBE Terhubung (' + (currentObeBlueprint.total_sum || 100) + '%)</span>');
        } else {
          $('#obe_status_tag').html('<span class="badge" style="background:#f59e0b; font-size:11px; padding:5px 10px; border-radius:20px;"><i class="fa fa-info-circle"></i> Standar SPMI</span>');
        }

        hitungTotalSkemaNilai();

        $('#modal_form_skema_nilai').modal({
          backdrop: 'static',
          keyboard: false
        });
        $('#skema_modal_title').html('<i class="fa fa-sliders" style="color:#38bdf8; margin-right:8px;"></i> Studio Pemetaan Skema Nilai: <strong>' + (data.tag_name || '') + '</strong>');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  function save() {
    $('#btnSave').text('Menyimpan...');
    $('#btnSave').attr('disabled', true);
    var url;
    var data;

    url = "<?php echo site_url('C_skema_nilai/ajax_update_skema_nilai') ?>";
    data = $('#form_skema_nilai').serializeArray()

    $.ajax({
      url: url,
      type: "POST",
      data: data,
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $('#modal_form_skema_nilai').modal('hide');
          reload_table();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
            $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
          }

          $('#btnSave').html('<i class="fa fa-check"></i> Simpan Skema Nilai');
          $('#btnSave').attr('disabled', false);
        }

      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error adding / update data');
        $('#btnSave').html('<i class="fa fa-check"></i> Simpan Skema Nilai');
        $('#btnSave').attr('disabled', false);
      }
    });
  }
</script>

<div class="modal fade" id="modal_form_skema_nilai" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1180px; margin: 25px auto;">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 20px 45px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); color: #ffffff; padding: 18px 24px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#ffffff; opacity:0.85; font-size: 24px; text-shadow: none;"><span aria-hidden="true">&times;</span></button>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding-right:20px;">
          <div>
            <h4 class="modal-title" id="skema_modal_title" style="font-weight: 700; color: #ffffff; margin: 0; font-size: 16.5px; letter-spacing: -0.2px;">
              <i class="fa fa-sliders" style="color:#38bdf8; margin-right:8px;"></i> Studio Pemetaan Skema Nilai OBE ke SIAKAD
            </h4>
            <div id="skema_modal_subtitle" style="font-size: 12px; color: #94a3b8; margin-top: 3px;">
              Matriks Asesmen CPMK/Sub-CPMK RPS OBE &rarr; Komponen Evaluasi SIAKAD Politeknik WBI
            </div>
          </div>
          <div id="obe_status_tag" style="display:flex; align-items:center; gap:6px;">
            <!-- dynamic badge -->
          </div>
        </div>
      </div>

      <?php
      $attributes = ['class' => 'form-horizontal', 'id' => 'form_skema_nilai', 'style' => 'margin: 0;'];
      echo form_open('#', $attributes);
      ?>
      <div class="modal-body" style="background: #f1f5f9 !important; padding: 20px 24px !important; max-height: calc(100vh - 210px); overflow-y: auto;">
        <input type="hidden" value="" name="id" />
        <input type="hidden" name="tag_name" />

        <!-- Panduan & Quick Action Bar -->
        <div style="background: #ffffff; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 12px 18px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <div style="font-size: 12.5px; color: #334155;">
            <i class="fa fa-info-circle" style="color: #3b82f6; font-size: 15px; margin-right: 6px;"></i>
            <strong>Panduan Pemetaan:</strong> Seret (<em>drag & drop</em>) kartu komponen OBE di sebelah kiri ke slot SIAKAD di kanan, atau gunakan tombol <strong>"⚡ Terapkan Bobot OBE"</strong> untuk pemetaan instan (Total wajib <strong>100%</strong>).
          </div>
          <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-xs btn-primary" onclick="autoMapObeToSiakad()" style="border-radius:6px; font-weight:700; padding:6px 12px; background:#2563eb; border-color:#1d4ed8; box-shadow:0 2px 4px rgba(37,99,235,0.2);">
              <i class="fa fa-bolt"></i> ⚡ Terapkan Bobot OBE (100%)
            </button>
            <button type="button" class="btn btn-xs btn-default" onclick="resetSiakadSlots()" style="border-radius:6px; font-weight:600; padding:6px 12px; color:#64748b;">
              <i class="fa fa-refresh"></i> Reset (0%)
            </button>
          </div>
        </div>

        <div class="row" style="margin-left: -8px !important; margin-right: -8px !important;">
          
          <!-- PANEL KIRI (col-md-5): SUMBER MATRIKS ASESMEN OBE -->
          <div class="col-md-5 col-xs-12" style="padding-left: 8px; padding-right: 8px; margin-bottom: 16px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); height: 100%; display: flex; flex-direction: column;">
              
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <div>
                  <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; display:flex; align-items:center; gap:6px;">
                    <i class="fa fa-th-large" style="color:#6366f1;"></i> Kartu Komponen RPS OBE
                  </div>
                  <div style="font-size:11px; color:#64748b; margin-top:2px;">
                    Sumber: Matriks Asesmen CPMK/Sub-CPMK
                  </div>
                </div>
                <span id="obe_blueprint_total_badge" class="badge" style="background:#4f46e5; font-size:11.5px; padding:4px 9px;">Total 100%</span>
              </div>

              <div style="font-size: 11.5px; color: #64748b; margin-bottom: 10px; line-height: 1.4;">
                <i class="fa fa-hand-pointer-o" style="color:#6366f1;"></i> <em>Seret kartu di bawah ini ke kotak SIAKAD tujuan di sebelah kanan:</em>
              </div>

              <!-- List Kartu Draggable -->
              <div id="obe_cards_container" style="display:flex; flex-direction:column; gap:8px; margin-bottom: 14px; min-height: 180px;">
                <!-- Rendered dynamically by JS -->
              </div>

              <!-- Collapsible Preview Tabel Blueprint Lengkap -->
              <div style="margin-top:auto; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                <button type="button" class="btn btn-default btn-xs btn-block" data-toggle="collapse" data-target="#collapseBlueprintTable" style="border-radius:6px; font-weight:600; color:#475569; background:#f8fafc; padding:6px 10px; text-align:left; display:flex; justify-content:space-between; align-items:center;">
                  <span><i class="fa fa-table" style="color:#0ea5e9; margin-right:5px;"></i> Preview Matriks Asesmen Lengkap</span>
                  <i class="fa fa-chevron-down" style="font-size:10px;"></i>
                </button>
                <div class="collapse" id="collapseBlueprintTable" style="margin-top:8px;">
                  <div id="obe_blueprint_table_wrap" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:8px; font-size:11px; max-height:220px; overflow-y:auto; overflow-x:auto;">
                    <!-- Table HTML rendered here -->
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- PANEL KANAN (col-md-7): 10 TARGET SLOT PENILAIAN SIAKAD -->
          <div class="col-md-7 col-xs-12" style="padding-left: 8px; padding-right: 8px; margin-bottom: 16px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
              
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <div>
                  <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; display:flex; align-items:center; gap:6px;">
                    <i class="fa fa-check-square-o" style="color:#059669;"></i> Slot Komponen Penilaian SIAKAD
                  </div>
                  <div style="font-size:11px; color:#64748b; margin-top:2px;">
                    Drop kartu ke slot terkait atau ketik angka persentase secara manual
                  </div>
                </div>
                <div style="text-align:right;">
                  <span style="font-size:11px; font-weight:600; color:#64748b;">Target Total: </span>
                  <span style="font-size:12px; font-weight:700; color:#0f172a;">100%</span>
                </div>
              </div>

              <!-- Group 1: Evaluasi Teoretis & Kognitif (5 Komponen) -->
              <div style="margin-bottom: 14px;">
                <div style="font-size: 11.5px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display:flex; align-items:center; gap:6px;">
                  <i class="fa fa-book text-primary"></i> 1. Evaluasi Formatif, Sikap &amp; Kognitif
                </div>
                
                <div class="row" style="margin-left:-4px !important; margin-right:-4px !important;">
                  <!-- Sikap -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_sikap" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'sikap')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-heartbeat text-danger"></i> Sikap / Partisipasi</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('sikap')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="sikap" id="slot_input_sikap" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Quiz -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_quiz" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'quiz')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-question-circle text-warning"></i> Quiz / Kuis</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('quiz')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="quiz" id="slot_input_quiz" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tugas Terstruktur -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_tugas_terstruktur" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'tugas_terstruktur')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-tasks text-info"></i> Tugas Terstruktur</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('tugas_terstruktur')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="tugas_terstruktur" id="slot_input_tugas_terstruktur" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- UTS -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_uts" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'uts')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-file-text-o text-primary"></i> UTS</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('uts')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="uts" id="slot_input_uts" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- UAS -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_uas" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'uas')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-graduation-cap text-danger"></i> UAS</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('uas')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="uas" id="slot_input_uas" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <!-- Group 2: Evaluasi Kinerja, Proyek & Portofolio SCL (5 Komponen) -->
              <div>
                <div style="font-size: 11.5px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display:flex; align-items:center; gap:6px;">
                  <i class="fa fa-cubes text-success"></i> 2. Evaluasi Kinerja, Proyek &amp; Luaran (SCL)
                </div>
                
                <div class="row" style="margin-left:-4px !important; margin-right:-4px !important;">
                  <!-- Proyek -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_proyek" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'proyek')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-briefcase text-success"></i> Proyek / Case Method</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('proyek')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="proyek" id="slot_input_proyek" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Unjuk Kinerja -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_unjuk_kinerja" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'unjuk_kinerja')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-cogs" style="color:#0284c7;"></i> Unjuk Kinerja / Praktik</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('unjuk_kinerja')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="unjuk_kinerja" id="slot_input_unjuk_kinerja" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Portofolio -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_portofolio" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'portofolio')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-folder-open-o" style="color:#8b5cf6;"></i> Portofolio</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('portofolio')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="portofolio" id="slot_input_portofolio" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Produk -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_produk" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'produk')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-archive" style="color:#d97706;"></i> Produk / Luaran</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('produk')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="produk" id="slot_input_produk" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Presentasi -->
                  <div class="col-xs-12 col-sm-6" style="padding:4px !important;">
                    <div class="siakad-slot-card" id="slot_card_presentasi" ondragover="handleObeDragOver(event)" ondragleave="handleObeDragLeave(event)" ondrop="handleObeDrop(event, 'presentasi')">
                      <div class="slot-header">
                        <span class="slot-title"><i class="fa fa-television" style="color:#ec4899;"></i> Presentasi</span>
                        <button type="button" class="btn-slot-clear" onclick="clearSlot('presentasi')" title="Kosongkan"><i class="fa fa-times"></i></button>
                      </div>
                      <div class="slot-input-wrap">
                        <div class="input-group input-group-sm">
                          <input name="presentasi" id="slot_input_presentasi" class="form-control komponen-reguler text-center font-bold" type="number" min="0" max="100" placeholder="0">
                          <span class="input-group-addon font-bold">%</span>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>

        <!-- PROGRESS BAR & VALIDATION NOTIFICATION -->
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; margin-top:4px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <span style="font-size:12px; font-weight:700; color:#334155;">
              <i class="fa fa-bar-chart" style="color:#4f46e5;"></i> Akumulasi Total Bobot Penilaian
            </span>
            <span id="skema_total_text" style="font-size:14px; font-weight:800; color:#0f172a;">0%</span>
          </div>
          <div class="progress" style="height: 10px; margin-bottom: 10px; border-radius: 5px; background: #e2e8f0; overflow: hidden;">
            <div id="skema_progress_bar" class="progress-bar" role="progressbar" style="width: 0%; background-color: #f59e0b; transition: width 0.3s ease, background-color 0.3s ease;"></div>
          </div>
          <div class="alert text-center" id="jumlah_total" style="border-radius: 8px; margin-bottom: 0; padding: 10px 14px; font-size: 12.5px;"></div>
        </div>

      </div>
      <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px 28px; text-align: right; display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:11.5px; color:#64748b; text-align:left;">
          <i class="fa fa-shield text-success"></i> Validasi Sistem: Tombol simpan hanya aktif saat total tepat 100%.
        </div>
        <div>
          <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600; padding: 8px 20px; margin-right: 8px; color: #475569;">Batal</button>
          <button type="button" id="btnSave" onclick="save()" class="btn btn-primary" style="background-color: #1e293b !important; border: 1px solid #0f172a !important; color: #ffffff !important; border-radius: 6px; font-weight: 700; padding: 8px 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
            <i class="fa fa-check"></i> Simpan Skema Nilai
          </button>
        </div>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<style>
  /* Drag and Drop Studio Styles */
  .obe-drag-card {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 12px;
    cursor: grab;
    transition: all 0.2s ease;
    user-select: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  .obe-drag-card:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99,102,241,0.18);
    transform: translateY(-2px);
  }
  .obe-drag-card:active {
    cursor: grabbing;
  }
  .obe-drag-card.dragging {
    opacity: 0.45;
    border-style: dashed;
    border-color: #6366f1;
  }

  .siakad-slot-card {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 8px;
    padding: 8px 10px;
    transition: all 0.2s ease;
    position: relative;
  }
  .siakad-slot-card.has-value {
    background: #ffffff;
    border: 1.5px solid #94a3b8;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  }
  .siakad-slot-card.slot-drag-hover {
    background: #eff6ff !important;
    border: 2px dashed #2563eb !important;
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(37,99,235,0.2);
  }
  .siakad-slot-card.slot-pulse {
    animation: slotPulseAnim 0.6s ease;
  }
  @keyframes slotPulseAnim {
    0% { transform: scale(1); background: #dcfce7; }
    50% { transform: scale(1.04); background: #bbf7d0; border-color: #16a34a; }
    100% { transform: scale(1); }
  }

  .siakad-slot-card .slot-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
  }
  .siakad-slot-card .slot-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .siakad-slot-card .btn-slot-clear {
    background: none;
    border: none;
    color: #94a3b8;
    padding: 0 4px;
    font-size: 11px;
    cursor: pointer;
    line-height: 1;
  }
  .siakad-slot-card .btn-slot-clear:hover {
    color: #ef4444;
  }
  .siakad-slot-card input.form-control {
    font-weight: 700;
    color: #0f172a;
    font-size: 13px;
    border-radius: 4px 0 0 4px !important;
  }
  .siakad-slot-card .input-group-addon {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 11px;
    border-radius: 0 4px 4px 0 !important;
  }
</style>