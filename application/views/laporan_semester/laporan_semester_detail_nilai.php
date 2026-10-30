<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Nilai - <?= htmlspecialchars($kelas->nm_mk) ?></title>
  <style>
    :root {
      --primary: #1e3a8a;
      --border: #e2e8f0;
      --text: #334155;
    }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--text);
      line-height: 1.5;
      padding: 30px 40px;
      margin: 0;
      background: #f8fafc;
    }
    .print-area {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    /* ── Header ────────────────────────────────────── */
    .header {
      border-bottom: 2px solid var(--primary);
      padding-bottom: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .header-left h1 {
      margin: 0 0 8px 0;
      font-size: 24px;
      color: var(--primary);
    }
    .header-left p {
      margin: 0 0 4px 0;
      font-size: 14px;
      color: #64748b;
    }
    .header-right {
      text-align: right;
    }
    .info-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      display: inline-block;
      text-align: left;
      min-width: 250px;
    }
    .info-box strong { color: #1e40af; }
    
    /* ── Stats ─────────────────────────────────────── */
    .stats-row {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
    }
    .stat-card {
      flex: 1;
      background: #fff;
      border: 1px solid var(--border);
      padding: 16px;
      border-radius: 8px;
      text-align: center;
    }
    .stat-card .lbl { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-card .val { font-size: 20px; font-weight: 800; color: var(--primary); }

    /* ── Table ─────────────────────────────────────── */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }
    th, td {
      border: 1px solid var(--border);
      padding: 8px 10px;
      text-align: left;
    }
    th {
      background: #f1f5f9;
      font-weight: 700;
      color: #0f172a;
      text-align: center;
      text-transform: uppercase;
      font-size: 11px;
    }
    td.num { text-align: right; font-weight: 600; }
    td.center { text-align: center; }
    
    .z-score-pos { color: #10b981; font-weight: 700; }
    .z-score-neg { color: #ef4444; font-weight: 700; }
    .z-score-neu { color: #64748b; font-weight: 700; }

    /* ── Kritik Saran ─────────────────────────────────────── */
    .feedback-section {
      margin-top: 40px;
      border-top: 2px dashed var(--border);
      padding-top: 30px;
    }
    .feedback-title {
      font-size: 18px;
      color: var(--primary);
      font-weight: 800;
      margin-bottom: 16px;
    }
    .feedback-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
    }
    .feedback-card {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      padding: 16px;
      border-radius: 8px;
      font-size: 13px;
      color: #334155;
      font-style: italic;
      position: relative;
    }
    .feedback-card::before {
      content: '"';
      font-size: 40px;
      color: #e2e8f0;
      position: absolute;
      top: 0px;
      left: 10px;
      font-family: serif;
      line-height: 1;
    }

    /* Print specific */
    @media print {
      body { background: #fff; padding: 0; }
      .print-area { padding: 0; box-shadow: none; max-width: 100%; }
      .no-print { display: none !important; }
      th { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .info-box { background: #fff !important; }
    }
  </style>
</head>
<body>
  
  <div class="print-area">
    <div style="text-align: right; margin-bottom: 20px;">
      <button class="no-print" onclick="window.print()" style="background: var(--primary); color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">🖨️ Cetak / PDF</button>
    </div>

    <div class="header">
      <div class="header-left">
        <h1>Daftar Nilai Mahasiswa</h1>
        <p><strong>Program Studi:</strong> <?= htmlspecialchars($kelas->prodi) ?></p>
        <p><strong>Mata Kuliah:</strong> <?= htmlspecialchars($kelas->nm_mk) ?> (<?= htmlspecialchars($kelas->kode_mk) ?>)</p>
        <p><strong>Kelas:</strong> <?= htmlspecialchars($kelas->nm_kls) ?></p>
        <p><strong>Dosen Pengampu:</strong> <?= htmlspecialchars($kelas->dosen ?: 'Belum diset') ?></p>
      </div>
      <div class="header-right">
        <div class="info-box">
          <div style="font-weight:800; font-size:14px; margin-bottom:6px; border-bottom:1px solid #bfdbfe; padding-bottom:4px;">Jadwal Perkuliahan</div>
          <?php if (!empty($jadwal)): ?>
            <?php foreach ($jadwal as $j): ?>
              <div style="margin-top:4px;">🗓️ <strong><?= htmlspecialchars($j->nama_hari) ?></strong>, <?= $j->jam_mulai ?> - <?= $j->jam_selesai ?></div>
              <div style="margin-top:2px;">📍 Ruang: <?= htmlspecialchars($j->nama_ruang) ?></div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="color:#64748b; font-style:italic;">Jadwal belum tersedia</div>
          <?php endif; ?>
        </div>

        <div class="info-box" style="margin-top: 12px; padding-bottom: 8px;">
          <div style="font-weight:800; font-size:13px; margin-bottom:8px; text-transform:uppercase; color:#1e3a8a; text-align:center;">Sebaran Nilai (Total: <?= count($nilai) ?>)</div>
          <div style="display:flex; gap:8px; justify-content:center;">
             <?php 
             $max_sebaran = !empty($sebaran) ? max($sebaran) : 0;
             foreach (['A','B+','B','C+','C','D','E'] as $gl): 
                 $gcount = isset($sebaran[$gl]) ? $sebaran[$gl] : 0;
                 $gcolor = '#94a3b8';
                 if ($gl == 'A') $gcolor = '#10b981';
                 elseif ($gl == 'B+') $gcolor = '#0ea5e9';
                 elseif ($gl == 'B') $gcolor = '#3b82f6';
                 elseif ($gl == 'C+') $gcolor = '#8b5cf6';
                 elseif ($gl == 'C') $gcolor = '#f59e0b';
                 elseif ($gl == 'D') $gcolor = '#ef4444';
                 elseif ($gl == 'E') $gcolor = '#9ca3af';
             ?>
             <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                 <div style="font-size:10px; font-weight:700; color:#475569;"><?= $gcount ?></div>
                 <div style="height:35px; width:12px; background:#e2e8f0; border-radius:2px; position:relative; display:flex; align-items:flex-end; justify-content:center;">
                     <div style="width:100%; background:<?= $gcolor ?>; border-radius:2px; height:<?= $max_sebaran > 0 ? ($gcount/$max_sebaran)*100 : 0 ?>%;"></div>
                 </div>
                 <div style="font-size:11px; font-weight:900; color:<?= $gcolor ?>;"><?= $gl ?></div>
             </div>
             <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="stats-row">
      <div class="stat-card">
        <div class="lbl">Total Mahasiswa</div>
        <div class="val"><?= count($nilai) ?></div>
      </div>
      <div class="stat-card">
        <div class="lbl">Rata-Rata Kelas (&mu;)</div>
        <div class="val"><?= number_format($mean, 2) ?></div>
      </div>
      <div class="stat-card">
        <div class="lbl">Standar Deviasi (&sigma;)</div>
        <div class="val"><?= number_format($std_dev, 2) ?></div>
      </div>
      <div class="stat-card" style="border-color: #f59e0b; background: #fffbeb;">
        <div class="lbl" style="color: #d97706;">Nilai EPOM</div>
        <div class="val" style="color: #b45309;"><?= $kelas->nilai_epom > 0 ? number_format($kelas->nilai_epom, 2) : '—' ?></div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width: 30px;">No</th>
          <th style="width: 100px;">NIM</th>
          <th>Nama Mahasiswa</th>
          <th style="width: 70px;">Nilai<br>Angka</th>
          <th style="width: 70px;">Nilai<br>Mutu</th>
          <th style="width: 70px;">Indeks</th>
          <th style="width: 90px;">Z-Score<br>(Deviasi)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($nilai)): ?>
          <tr><td colspan="7" class="center" style="padding: 20px; color: #64748b; font-style: italic;">Belum ada data nilai mahasiswa.</td></tr>
        <?php else: ?>
          <?php $no = 1; foreach ($nilai as $n): 
            $zs = (float)$n->z_score;
            $z_class = 'z-score-neu';
            $z_prefix = '';
            if ($zs > 0.05) { $z_class = 'z-score-pos'; $z_prefix = '+'; }
            elseif ($zs < -0.05) { $z_class = 'z-score-neg'; }
          ?>
          <tr>
            <td class="center"><?= $no++ ?></td>
            <td class="center" style="font-family: monospace; font-size: 14px;"><?= htmlspecialchars($n->nipd) ?></td>
            <td style="font-weight: 600;"><?= htmlspecialchars($n->nm_pd) ?></td>
            <td class="num"><?= number_format($n->nilai_angka, 2) ?></td>
            <td class="center" style="font-weight: 800; color: var(--primary);"><?= htmlspecialchars($n->nilai_huruf) ?></td>
            <td class="num"><?= number_format($n->nilai_indeks, 2) ?></td>
            <td class="center <?= $z_class ?>"><?= $z_prefix . number_format($zs, 2) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    
    <div style="margin-top: 16px; font-size: 11px; color: #64748b;">
      * <strong>Z-Score (Deviasi Standard)</strong> menunjukkan seberapa jauh nilai mahasiswa menyimpang dari rata-rata kelas. 
      Nilai positif (+) berarti di atas rata-rata kelas, negatif (-) berarti di bawah rata-rata.
    </div>

    <!-- Kritik & Saran -->
    <?php if (!empty($kritik_saran)): ?>
    <div class="feedback-section">
      <div class="feedback-title">💬 Kritik & Saran Mahasiswa (Anonim & Acak)</div>
      <div class="feedback-grid">
        <?php foreach ($kritik_saran as $ks): ?>
          <div class="feedback-card">
            <div style="position:relative; z-index:1;"><?= nl2br(htmlspecialchars(trim((string)$ks->kritik_saran))) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>

</body>
</html>
