<?php if (!empty($is_mpdf)): ?>
<htmlpagefooter name="docFooter">
  <div style="border-top: 0.5px solid #cbd5e1; padding-top: 4px; font-size: 8pt; color: #64748b; font-family: Helvetica, sans-serif;">
    <table style="width: 100%; border: none !important; border-collapse: collapse;">
      <tr>
        <td style="width: 65%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: left;">
          Roadmap Pembelajaran - <?= !empty($data_rps->nm_mk) ? htmlspecialchars($data_rps->nm_mk) : ''; ?>
        </td>
        <td style="width: 35%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: right;">
          Halaman {PAGENO} dari {nbpg}
        </td>
      </tr>
    </table>
  </div>
</htmlpagefooter>
<sethtmlpagefooter name="docFooter" value="on" />
<?php endif; ?>

<div class="text-center" style="margin-bottom: 12px; text-align: center;">
   <h3 style="margin: 0 0 3px 0; font-size: 12pt; font-weight: 800; letter-spacing: 0.6px; color: #0f172a;">
      <u>ROADMAP PEMBELAJARAN MATA KULIAH</u>
   </h3>
   <div style="font-size: 8pt; color: #475569; margin-bottom: 4px;">
      Standar Outcome-Based Education (OBE) & Kurikulum Politeknik Wilmar Bisnis Indonesia
   </div>
   <div style="font-size: 9pt; font-weight: 500; color: #1e293b; line-height: 1.5;">
      <span><strong>Mata Kuliah:</strong> <?= htmlspecialchars($data_rps->nm_mk); ?><?= (!empty($data_rps->kode_mk) && stripos($data_rps->nm_mk, $data_rps->kode_mk) === false) ? ' (' . htmlspecialchars($data_rps->kode_mk) . ')' : ''; ?></span>
      <span style="margin: 0 10px; color: #94a3b8;">|</span>
      <span><strong>Kode MK:</strong> <?= htmlspecialchars($data_rps->kode_mk); ?></span>
      <span style="margin: 0 10px; color: #94a3b8;">|</span>
      <span><strong>Bobot:</strong> <?= !empty($data_rps->sks_total_display) ? htmlspecialchars($data_rps->sks_total_display) : ((int)$data_rps->sks_mk . ' SKS'); ?></span>
      <span style="margin: 0 10px; color: #94a3b8;">|</span>
      <span><strong>Program Studi:</strong> <?= htmlspecialchars($nm_lemb); ?></span>
   </div>
</div>

<?php
// Ekstrak roadmap data dari JSON atau markdown
$roadmap_data = null;
if (!empty($data_rps->roadmap_modul_json)) {
    $decoded = json_decode($data_rps->roadmap_modul_json, true);
    if (is_array($decoded) && !empty($decoded['modules'])) {
        $roadmap_data = $decoded;
    }
}

// Fallback jika belum ada di database, coba cari modul dari detail RPS
if (!$roadmap_data && !empty($data_rps_detail)) {
    // Generate modul sederhana dari pertemuan
    $modules_auto = [];
    $current_mod = null;
    $mod_num = 1;
    foreach ($data_rps_detail as $idx => $d) {
        $m_num = (int)$d->kolom_1;
        if ($m_num === 8) {
            $modules_auto[] = [
                'module_name'   => 'Evaluasi Tengah Semester (UTS)',
                'start_meeting' => 8,
                'end_meeting'   => 8,
                'output'        => 'Ujian Tertulis / Praktik UTS',
                'concept'       => 'Evaluasi Capaian CPMK 1 s.d Pertemuan 7',
                'indicators'    => 'Ketepatan menjawab & menyelesaikan soal evaluasi',
                'strategy'      => 'Asesmen UTS'
            ];
            continue;
        }
        if ($m_num === 16) {
            $modules_auto[] = [
                'module_name'   => 'Evaluasi Akhir Semester (UAS / Proyek)',
                'start_meeting' => 16,
                'end_meeting'   => 16,
                'output'        => 'Laporan Akhir & Proyek Aplikasi',
                'concept'       => 'Evaluasi Komprehensif Seluruh CPMK',
                'indicators'    => 'Penguasaan materi & fungsionalitas luaran proyek',
                'strategy'      => 'Asesmen UAS / Presentasi'
            ];
            continue;
        }
    }
}
?>

<?php if ($roadmap_data && !empty($roadmap_data['modules'])): ?>
  <?php
    $palette = ['#dbeafe', '#dcfce7', '#ffedd5', '#f3e8ff', '#cffafe', '#ffe4e6', '#e0e7ff'];
    $color_uts_uas = '#fef08a';
    $learning_outcome = $roadmap_data['learning_outcome'] ?? ('ROADMAP PEMBELAJARAN ' . strtoupper((string)$data_rps->nm_mk));
    $modules = array_values($roadmap_data['modules']);

    // Normalisasi rentang pertemuan agar modul terakhir otomatis mencakup s.d. pertemuan 16
    if (!empty($modules)) {
        $last_idx = count($modules) - 1;
        if ((int)($modules[$last_idx]['end_meeting'] ?? 0) < 16) {
            $modules[$last_idx]['end_meeting'] = 16;
        }
    }
  ?>

  <table class="table table-black" style="width: 100%; border-collapse: collapse; font-size: 8.5pt; table-layout: fixed; border: 1px solid #7a7a7a; margin-bottom: 12px;">
    <!-- Baris 1: Learning Outcome Header -->
    <tr>
      <th style="width: 110px; background: #e2e8f0; border: 1px solid #7a7a7a; padding: 6px 8px; font-weight: bold; text-align: left; vertical-align: middle;">
        LEARNING OUTCOME
      </th>
      <td colspan="16" style="background: #f1f5f9; border: 1px solid #7a7a7a; padding: 8px 12px; font-weight: bold; color: #0f172a; text-align: center; vertical-align: middle; font-size: 9pt;">
        <?= htmlspecialchars($learning_outcome); ?>
      </td>
      <td rowspan="7" style="width: 28px; background: #e2e8f0; border: 1px solid #7a7a7a; padding: 4px; text-align: center; vertical-align: middle; writing-mode: vertical-rl; transform: rotate(180deg); font-weight: bold; font-size: 8.5pt; color: #334155;">
        <?= htmlspecialchars($data_rps->nm_mk); ?>
      </td>
    </tr>

    <?php
    $categories = [
      ['key' => 'module_name', 'label' => 'MODULE', 'default' => 'Modul', 'bold' => true],
      ['key' => 'output', 'label' => 'OUTPUT', 'default' => '-', 'bold' => false],
      ['key' => 'concept', 'label' => 'CONCEPT', 'default' => '-', 'bold' => false],
      ['key' => 'indicators', 'label' => 'INDICATORS', 'default' => '-', 'bold' => false],
      ['key' => 'strategy', 'label' => 'LEARNING STRATEGY', 'default' => '-', 'bold' => false]
    ];

    foreach ($categories as $cat):
    ?>
      <tr>
        <th style="background: #e2e8f0; border: 1px solid #7a7a7a; padding: 5px 6px; font-weight: bold; text-align: left; vertical-align: top; font-size: 8pt;">
          <?= $cat['label']; ?>
        </th>
        <?php
        $color_idx = 0;
        foreach ($modules as $mod):
            $start_m = max(1, min(16, (int)($mod['start_meeting'] ?? 1)));
            $end_m = max(1, min(16, (int)($mod['end_meeting'] ?? $start_m)));
            if ($end_m < $start_m) $end_m = $start_m;
            $span = ($end_m - $start_m + 1);

            $mod_name = $mod['module_name'] ?? '';
            $is_uts_uas = ($start_m === 8 && $end_m === 8) || ($start_m === 16 && $end_m === 16) || (stripos($mod_name, 'UTS') !== false || stripos($mod_name, 'UAS') !== false);
            $bg_color = $is_uts_uas ? $color_uts_uas : ($palette[$color_idx % count($palette)]);
            if (!$is_uts_uas) $color_idx++;

            $cell_val = $mod[$cat['key']] ?? $cat['default'];
            $td_style = 'background:' . $bg_color . '; border:1px solid #7a7a7a; padding:5px 6px; vertical-align:top; font-size:8pt; line-height:1.35;';
            if ($cat['bold']) {
                $td_style .= ' font-weight:bold; text-align:center;';
            }
        ?>
          <td colspan="<?= $span; ?>" style="<?= $td_style; ?>">
            <?= nl2br(htmlspecialchars($cell_val)); ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>

    <!-- Baris Terakhir: Meeting 1 s.d 16 -->
    <tr>
      <th style="background: #e2e8f0; border: 1px solid #7a7a7a; padding: 5px 6px; font-weight: bold; text-align: left; vertical-align: middle; font-size: 8pt;">
        MEETING
      </th>
      <?php for ($m = 1; $m <= 16; $m++): 
          $lbl = $m;
          $bg = '#f8fafc';
          if ($m === 8) { $lbl = '8 (UTS)'; $bg = $color_uts_uas; }
          if ($m === 16) { $lbl = '16 (UAS)'; $bg = $color_uts_uas; }
      ?>
        <td style="background: <?= $bg; ?>; border: 1px solid #7a7a7a; padding: 5px 2px; text-align: center; font-weight: bold; font-size: 8pt;">
          <?= $lbl; ?>
        </td>
      <?php endfor; ?>
    </tr>
  </table>

<?php elseif (!empty($data_rps->roadmap_wbi_markdown)): ?>
  <div style="border: 1px solid #cbd5e1; padding: 14px; border-radius: 4px; background: #ffffff; font-size: 9pt; line-height: 1.55;">
    <?= format_rps_list($data_rps->roadmap_wbi_markdown); ?>
  </div>
<?php else: ?>
  <div style="border: 1px dashed #cbd5e1; padding: 24px; border-radius: 6px; text-align: center; background: #f8fafc; color: #64748b;">
    <i class="fa-solid fa-sitemap" style="font-size: 28px; color: #94a3b8; margin-bottom: 8px;"></i>
    <div style="font-weight: 700; font-size: 11pt; color: #334155; margin-bottom: 4px;">Data Roadmap Modul Belum Dibuat</div>
    <p style="margin: 0; font-size: 9pt;">Silakan buka <strong>Studio RPS</strong> untuk menyusun roadmap modul pembelajaran.</p>
  </div>
<?php endif; ?>
