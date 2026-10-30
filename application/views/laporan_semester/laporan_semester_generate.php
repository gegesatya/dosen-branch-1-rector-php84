<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Kinerja Akademik WBI <?= isset($active_smt) && $active_smt ? '— ' . htmlspecialchars($active_smt->nm_smt) : '' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════════════════════════════════════
   LAPORAN SEMESTER — Print-Friendly Full-Color Design (1-column cards)
   ══════════════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --primary:  #1e3a8a;
  --accent:   #0ea5e9;
  --gold:     #f59e0b;
  --emerald:  #059669;
  --rose:     #e11d48;
  --bg:       #f0f4f8;
  --card-bg:  #ffffff;
  --text:     #1e293b;
  --muted:    #64748b;
  --border:   #e2e8f0;
}

body {
  font-family: 'Inter', 'Segoe UI', sans-serif;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  line-height: 1.5;
  min-height: 100vh;
}

/* ── Toolbar ─────────────────────────────────────────────────────────── */
.toolbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: linear-gradient(135deg, #0f1f4a, #1e3a8a);
  padding: 10px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
}
.toolbar label { color: rgba(255,255,255,0.8); font-size: 14px; font-weight: 600; white-space: nowrap; }
.toolbar select {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.25);
  color: #fff;
  padding: 6px 30px 6px 12px;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  cursor: pointer;
  min-width: 230px;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  flex: 1;
  max-width: 300px;
}
.toolbar select option { background: #1e3a8a; }
.btn-print {
  display: inline-flex; align-items: center; justify-content: center; gap: 7px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff; border: none; padding: 8px 18px; border-radius: 8px;
  font-size: 14px; font-weight: 700; cursor: pointer;
  box-shadow: 0 4px 12px rgba(245,158,11,0.4);
  font-family: inherit; white-space: nowrap;
  transition: transform .15s, box-shadow .15s;
}
.btn-print:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }

/* ── Print area ──────────────────────────────────────────────────────── */
.print-area {
  max-width: 860px;
  margin: 18px auto;
  padding: 0 14px 40px;
}

/* ── Page Section ────────────────────────────────────────────────────── */
.page-section {
  background: var(--card-bg);
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.07);
  margin-bottom: 22px;
  overflow: hidden;
}

/* ── COVER ───────────────────────────────────────────────────────────── */
.cover {
  background: #ffffff;
  padding: 55px 40px;
  min-height: 350px;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  text-align: center; position: relative; overflow: hidden;
  border-bottom: 1px solid var(--border);
}
.cover-logo { width: 90px; margin-bottom: 24px; position: relative; }
.cover-badge {
  display: inline-block;
  background: #f8faff; border: 1px solid #e2e8f0;
  color: var(--primary); padding: 4px 16px; border-radius: 50px;
  font-size: 12px; font-weight: 700; letter-spacing: 1.5px;
  text-transform: uppercase; margin-bottom: 16px; position: relative;
}
.cover-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 32px; font-weight: 800; color: #1e293b;
  margin-bottom: 8px; line-height: 1.25; position: relative;
}
.cover-subtitle { font-size: 18px; font-weight: 600; color: #64748b; position: relative; margin-bottom: 28px; }
.cover-divider { width: 40px; height: 3px; background: var(--primary); border-radius: 2px; margin: 0 auto 28px; position: relative; }
.cover-meta { display: flex; gap: 48px; position: relative; flex-wrap: wrap; justify-content: center; }
.cover-meta-item .val { display: block; font-size: 24px; font-weight: 900; color: var(--primary); }
.cover-meta-item .lbl { display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

/* ── Section Header ──────────────────────────────────────────────────── */
.section-hdr {
  padding: 16px 22px 12px;
  border-bottom: 2px solid var(--border);
  display: flex; align-items: center; gap: 12px;
  background: linear-gradient(135deg, #f8faff 0%, #fff 100%);
}
.section-hdr .ico {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 19px; flex-shrink: 0;
}
.section-hdr .ico.blue  { background: linear-gradient(135deg, #1e3a8a, #0ea5e9); }
.section-hdr .ico.gold  { background: linear-gradient(135deg, #d97706, #f59e0b); }
.section-hdr h2 { font-size: 16px; font-weight: 700; color: var(--primary); margin-bottom: 2px; }
.section-hdr p  { font-size: 13px; color: var(--muted); }

/* ── KPI Grid ────────────────────────────────────────────────────────── */
.kpi-grid {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 1px; background: var(--border);
}
.kpi-card {
  background: var(--card-bg); padding: 18px 14px;
  text-align: center; position: relative; overflow: hidden;
}
.kpi-card::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
}
.kpi-card.blue::after  { background: linear-gradient(90deg, #1e3a8a, #0ea5e9); }
.kpi-card.gold::after  { background: linear-gradient(90deg, #d97706, #f59e0b); }
.kpi-card.green::after { background: linear-gradient(90deg, #047857, #10b981); }
.kpi-card.rose::after  { background: linear-gradient(90deg, #be123c, #e11d48); }
.kpi-value { font-size: 28px; font-weight: 800; display: block; margin-bottom: 4px; }
.kpi-card.blue  .kpi-value { color: var(--primary); }
.kpi-card.gold  .kpi-value { color: #d97706; }
.kpi-card.green .kpi-value { color: #047857; }
.kpi-card.rose  .kpi-value { color: #be123c; }
.kpi-label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; }

/* ── Section Body ────────────────────────────────────────────────────── */
.section-body { padding: 18px 22px; }

/* ── Overall EPOM bar per prodi ──────────────────────────────────────── */
.prodi-epom-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.prodi-epom-row .prodi-name {
  width: 210px; font-size: 13px; font-weight: 600; color: var(--text);
  flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.prodi-epom-bar-track {
  flex: 1; height: 22px; background: #f1f5f9; border-radius: 6px; overflow: hidden;
}
.prodi-epom-bar-fill {
  height: 100%; border-radius: 6px; display: flex; align-items: center;
  padding-left: 10px; font-size: 13px; font-weight: 700; color: #fff;
  min-width: 40px;
}
.prodi-epom-val { width: 45px; text-align: right; font-size: 14px; font-weight: 700; color: var(--primary); flex-shrink: 0; }

/* ── Dosen table ─────────────────────────────────────────────────────── */
.dosen-prodi-hdr {
  background: linear-gradient(135deg, #1e3a8a, #1e40af);
  color: #fff; padding: 9px 14px; font-size: 14px; font-weight: 700;
  display: flex; align-items: center; gap: 8px;
  border-radius: 8px 8px 0 0;
}
.dosen-prodi-hdr .bdg {
  background: rgba(255,255,255,0.2); border-radius: 4px;
  padding: 2px 8px; font-size: 12px;
}
.dosen-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.dosen-table th {
  background: #f8faff; color: var(--primary); font-weight: 700;
  padding: 7px 10px; text-align: left; border-bottom: 2px solid var(--border);
  font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
}
.dosen-table td { padding: 7px 10px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.dosen-table tr:last-child td { border-bottom: none; }
.dosen-table tr:nth-child(even) td { background: #f8faff; }

/* ══════════════════════════════════════════════════════════════════════
   PRODI SECTION & SEMESTER CHAPTERS
   ══════════════════════════════════════════════════════════════════════ */
.prodi-section { margin-bottom: 28px; }

.prodi-section-hdr {
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  color: #fff; padding: 12px 20px;
  display: flex; align-items: center; gap: 12px;
  border-radius: 12px 12px 0 0;
}
.prodi-section-hdr .prodi-ico {
  width: 34px; height: 34px;
  background: rgba(255,255,255,0.15); border-radius: 9px;
  display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.prodi-section-hdr .prodi-title { font-size: 15px; font-weight: 700; }
.prodi-section-hdr .prodi-sub   { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 1px; }
.prodi-section-hdr .prodi-avg   {
  margin-left: auto; background: rgba(255,255,255,0.15);
  padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 700; white-space: nowrap;
}

/* Semester chapter header */
.smt-chapter-hdr {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 16px 8px;
  background: linear-gradient(90deg, #f0f6ff, #f8faff);
  border-left: 4px solid var(--accent);
  margin: 0 0 2px;
}
.smt-chapter-hdr .smt-badge {
  display: inline-flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, #0ea5e9, #1e3a8a);
  color: #fff; font-size: 16px; font-weight: 900;
  flex-shrink: 0; box-shadow: 0 2px 8px rgba(14,165,233,0.35);
}
.smt-chapter-hdr .smt-label {
  font-size: 14px; font-weight: 700; color: var(--primary);
}
.smt-chapter-hdr .smt-count {
  font-size: 12px; color: var(--muted); margin-top: 1px;
}
.smt-chapter-hdr .smt-avg-pill {
  margin-left: auto;
  background: #eff6ff; color: #1e3a8a;
  padding: 3px 12px; border-radius: 20px;
  font-size: 13px; font-weight: 700; white-space: nowrap;
  border: 1px solid #bfdbfe;
}

/* Kelas list wrapper */
.kelas-list {
  padding: 10px 12px 12px;
  background: #fafbff;
  border: 1px solid var(--border);
  border-top: none;
}
.kelas-list:last-child { border-radius: 0 0 12px 12px; }

/* ── Kelas Card (1 column horizontal, fixed height) ─────────────────── */
.kelas-card {
  display: flex;
  align-items: stretch;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 10px;
  margin-bottom: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  overflow: hidden;
  transition: box-shadow .2s, transform .2s;
  min-height: 120px; /* fixed minimum height to accommodate 7 grades */
}
.kelas-card:last-child { margin-bottom: 0; }
.kelas-card:hover { box-shadow: 0 5px 18px rgba(0,0,0,0.1); transform: translateY(-1px); }

/* Rank / EPOM column */
.kelas-rank {
  flex-shrink: 0;
  width: 70px;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 10px 8px;
  background: linear-gradient(160deg, #f8faff 0%, #eff6ff 100%);
  border-right: 1px solid var(--border);
}
.kelas-rank .rank-no {
  font-size: 12px; font-weight: 600; color: var(--muted);
  margin-bottom: 3px;
}
.kelas-rank .epom-score {
  font-size: 22px; font-weight: 900; line-height: 1;
}
.kelas-rank .epom-lbl {
  font-size: 10px; font-weight: 600; color: var(--muted);
  text-transform: uppercase; letter-spacing: 0.4px; margin-top: 2px;
}

/* Kelas info column */
.kelas-info {
  flex: 1;
  padding: 10px 14px;
  min-width: 0;
  display: flex; flex-direction: column; justify-content: center;
  gap: 3px;
}
.kelas-nm-mk {
  display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;
}
.kelas-nm {
  font-size: 19px; font-weight: 900; color: var(--primary); line-height: 1;
}
.kelas-kode { font-size: 11px; font-weight: 700; color: var(--muted); background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
.kelas-mk-nm { font-size: 13px; font-weight: 600; color: var(--text); line-height: 1.3; margin-top: 2px; margin-bottom: 2px; }
.kelas-dosen {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: var(--muted); margin-top: 2px;
}
.kelas-dosen .nm { font-weight: 600; color: #334155; }

/* EPOM mini bar */
.kelas-epom-bar {
  margin-top: 5px;
  height: 3px; background: #f1f5f9; border-radius: 2px; overflow: hidden; width: 100%;
}
.kelas-epom-bar-fill { height: 100%; border-radius: 2px; }

/* ── Grade: Horizontal bars (fixed 7 grades) ─────────────────────────── */
.kelas-grades {
  flex-shrink: 0;
  width: 220px;
  padding: 10px 12px;
  border-left: 1px solid var(--border);
  display: flex; flex-direction: column; justify-content: center;
  gap: 3px;
}
.grade-top-lbl {
  font-size: 10px; font-weight: 700; color: var(--muted);
  text-transform: uppercase; letter-spacing: 0.5px;
  margin-bottom: 4px; flex-shrink: 0;
}
.grade-row { display: flex; align-items: center; gap: 5px; }
.grade-letter {
  width: 22px; height: 16px; border-radius: 3px;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.grade-letter.A  { background: #059669; }
.grade-letter.AB { background: #0891b2; }
.grade-letter.B  { background: #0ea5e9; }
.grade-letter.BC { background: #7c3aed; }
.grade-letter.C  { background: #d97706; }
.grade-letter.D  { background: #dc2626; }
.grade-letter.E  { background: #9f1239; }
.grade-letter.other { background: #94a3b8; }

.grade-bar-wrap { flex: 1; height: 8px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
.grade-bar-fill { height: 100%; border-radius: 3px; }
.grade-count { width: 20px; text-align: right; font-size: 12px; font-weight: 700; color: var(--text); }
.grade-pct   { width: 24px; text-align: right; font-size: 10px; color: var(--muted); }
/* No data */
.no-grade {
  font-size: 11px; color: var(--muted); font-style: italic;
  display: flex; align-items: center; justify-content: center;
  height: 100%;
}

/* ── No data ─────────────────────────────────────────────────────────── */
.no-data { padding: 30px; text-align: center; color: var(--muted); font-size: 15px; }
.no-data .ico-big { font-size: 36px; display: block; margin-bottom: 8px; }

/* ══════════════════════════════════════════════════════════════════════
   PRINT
   ══════════════════════════════════════════════════════════════════════ */
@media print {
  @page { size: A4; margin: 12mm 10mm 12mm 10mm; }
  body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; color-adjust: exact; }
  .toolbar { display: none !important; }
  .print-area { max-width: 100%; margin: 0; padding: 0; }
  .page-section { box-shadow: none; margin-bottom: 0; border-radius: 0; }
  .cover { min-height: 200mm; page-break-after: always; }
  .overall-section { page-break-after: always; }
  .prodi-section { page-break-inside: avoid; }
  .kelas-card { page-break-inside: avoid; break-inside: avoid; }
  .kelas-card:hover { box-shadow: none; transform: none; }
  a { color: inherit; text-decoration: none; }
  
  /* Print modes */
  body.print-stats-only .sect-prodi { display: none !important; }
  body.print-kpi-only .kelas-list { display: none !important; }
  .no-print { display: none !important; }
  
  /* Printer-friendly Prodi Header */
  .prodi-section-hdr { background: #f8faff !important; padding: 12px 20px !important; border-bottom: 2px solid #e2e8f0 !important; }
  .prodi-section-hdr .prodi-title { color: #1e293b !important; }
  .prodi-section-hdr .prodi-sub { color: #64748b !important; }
  .prodi-section-hdr .prodi-ico { background: #e2e8f0 !important; font-size: 24px !important; width: 48px !important; height: 48px !important; }
  .prodi-section-hdr .prodi-avg { background: #fff !important; color: #0f172a !important; border: 1px solid #cbd5e1 !important; }
}
</style>
</head>
<body>

<!-- ── TOOLBAR ──────────────────────────────────────────────────────── -->
<div class="toolbar">
  <label for="smt-select">📅 Semester:</label>
  <select id="smt-select" onchange="changeSmt(this.value, this.options[this.selectedIndex].text)">
    <?php foreach ($smt_list as $s): ?>
      <option value="<?= $s->id_smt ?>"
        <?= (isset($active_smt) && $active_smt && $active_smt->id_smt == $s->id_smt) ? 'selected' : '' ?>>
        <?= htmlspecialchars($s->nm_smt) ?>
      </option>
    <?php endforeach; ?>
  </select>
  </select>
  <div style="display:flex; gap:8px; margin-left:auto;">
    <button class="btn-print" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);" onclick="printMode('print-stats-only')">
      📊 Cetak Performa WBI
    </button>
    <button class="btn-print" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);" onclick="printMode('print-kpi-only')">
      📑 S/d KPI Prodi
    </button>
    <button class="btn-print" onclick="window.print()">
      🖨️ Cetak Full
    </button>
  </div>
</div>

<?php if (!isset($active_smt) || !$active_smt): ?>
<div class="print-area">
  <div class="page-section">
    <div class="no-data"><span class="ico-big">📋</span>Pilih semester dari dropdown untuk menampilkan laporan.</div>
  </div>
</div>
<?php else: ?>

<?php
  $nm_smt   = htmlspecialchars($active_smt->nm_smt);
  $smt_type = (int)$active_smt->smt; // 1=Ganjil 2=Genap

  // Helper: EPOM color
  function epom_color($v) {
    if ($v >= 3.5) return '#059669';
    if ($v >= 3.0) return '#0ea5e9';
    if ($v >= 2.5) return '#d97706';
    return '#dc2626';
  }
  // Helper: grade CSS class
  function grade_class($g) {
    $map = ['A'=>'A','A-'=>'A','B+'=>'AB','B'=>'B','B-'=>'B','C+'=>'BC','C'=>'C','C-'=>'C','D+'=>'D','D'=>'D','D-'=>'D','E'=>'E','T'=>'T','K'=>'T'];
    return isset($map[$g]) ? $map[$g] : 'other';
  }
  // Helper: grade fill color
  function grade_color($g) {
    $map = ['A'=>'#059669','A-'=>'#059669','B+'=>'#0891b2','B'=>'#0ea5e9','B-'=>'#0ea5e9','C+'=>'#7c3aed','C'=>'#d97706','C-'=>'#d97706','D+'=>'#dc2626','D'=>'#dc2626','E'=>'#9f1239','T'=>'#64748b','K'=>'#64748b'];
    return isset($map[$g]) ? $map[$g] : '#94a3b8';
  }
?>

<div class="print-area">

<!-- ══════════════════════════════════════════════════════════════════
     1. COVER
     ══════════════════════════════════════════════════════════════════ -->
<div class="page-section">
  <div class="cover">
    <img src="<?= base_url('assets/image_web/logo-white-3.png') ?>" alt="Logo WBI" class="cover-logo">
    <div class="cover-badge">Politeknik Wilmar Bisnis Indonesia</div>
    <h1 class="cover-title">Laporan Kinerja Akademik WBI</h1>
    <div class="cover-subtitle"><?= $nm_smt ?></div>
    <div class="cover-divider"></div>
    <div class="cover-meta">
      <div class="cover-meta-item">
        <span class="val"><?= $total_kelas ?></span>
        <span class="lbl">Total Kelas</span>
      </div>
      <div class="cover-meta-item">
        <span class="val"><?= $total_prodi ?></span>
        <span class="lbl">Program Studi</span>
      </div>
      <div class="cover-meta-item">
        <span class="val"><?= number_format($overall_avg, 2) ?></span>
        <span class="lbl">Avg EPOM</span>
      </div>
      <div class="cover-meta-item">
        <span class="val"><?= $smt_type == 1 ? '1,3,5,7' : '2,4,6,8' ?></span>
        <span class="lbl">Semester Aktif</span>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     2. KINERJA KESELURUHAN
     ══════════════════════════════════════════════════════════════════ -->
<div class="page-section overall-section">
  <div class="section-hdr">
    <div class="ico blue">📊</div>
    <div>
      <h2>Kinerja Akademik Keseluruhan — WBI</h2>
      <p>Ringkasan kinerja seluruh program studi pada <?= $nm_smt ?></p>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="kpi-grid">
    <div class="kpi-card blue">
      <span class="kpi-value"><?= number_format($overall_avg, 2) ?></span>
      <span class="kpi-label">Rata-rata EPOM</span>
    </div>
    <div class="kpi-card gold">
      <span class="kpi-value"><?= $total_kelas ?></span>
      <span class="kpi-label">Total Kelas</span>
    </div>
    <div class="kpi-card green">
      <span class="kpi-value"><?= number_format($overall_max, 2) ?></span>
      <span class="kpi-label">EPOM Tertinggi</span>
    </div>
    <div class="kpi-card rose">
      <span class="kpi-value"><?= number_format($overall_min, 2) ?></span>
      <span class="kpi-label">EPOM Terendah</span>
    </div>
  </div>

  <!-- EPOM bar per prodi -->
  <div class="section-body">
    <div style="font-size:14px;font-weight:700;color:var(--primary);margin-bottom:12px;">
      📈 Rata-rata EPOM per Program Studi
    </div>
    <?php if (!empty($prodi_data)):
      $sorted_prodi = $prodi_data;
      usort($sorted_prodi, fn($a,$b) => $b['avg_epom'] <=> $a['avg_epom']);
    ?>
      <?php foreach ($sorted_prodi as $pd): ?>
        <?php $bar_pct = ($pd['avg_epom'] / 4.0) * 100; ?>
        <div class="prodi-epom-row">
          <div class="prodi-epom-row prodi-name" style="width:200px;font-size:13px;font-weight:600;display:block;" title="<?= htmlspecialchars($pd['prodi']) ?>">
            <?= htmlspecialchars($pd['prodi']) ?>
          </div>
          <div class="prodi-epom-bar-track" style="flex:1;height:22px;background:#f1f5f9;border-radius:6px;overflow:hidden;">
            <div class="prodi-epom-bar-fill" style="width:<?= min(100,$bar_pct) ?>%;background:<?= epom_color($pd['avg_epom']) ?>cc;height:100%;display:flex;align-items:center;padding-left:10px;font-size:13px;font-weight:700;color:#fff;">
              &nbsp;
            </div>
          </div>
          <div style="width:45px;text-align:right;font-size:14px;font-weight:700;color:<?= epom_color($pd['avg_epom']) ?>">
            <?= number_format($pd['avg_epom'],2) ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-data"><span class="ico-big">📭</span>Belum ada data EPOM.</div>
    <?php endif; ?>

    <!-- Dosen per prodi -->
    <div class="sect-dosen">
    <?php if (!empty($dosen_map)): ?>
    <div style="margin-top:32px; border-top:2px dashed #e2e8f0; padding-top:24px;">
      <div style="font-size:16px;font-weight:800;color:var(--primary);margin-bottom:16px;">
        👨‍🏫 Dosen yang Mengajar — Semester <?= $smt_type == 1 ? 'Ganjil (1, 3, 5, 7)' : 'Genap (2, 4, 6, 8)' ?>
      </div>
      <?php foreach ($dosen_map as $pid => $dosens):
        $pnm = '';
        $ppd_data = null;
        foreach ($prodi_data as $ppd) { 
            if ($ppd['id_sms'] == $pid) { 
                $pnm = $ppd['prodi']; 
                $ppd_data = $ppd;
                break; 
            } 
        }
        
        $tetap_epom_vals = [];
        $tidak_tetap_epom_vals = [];
        $total_e_tetap = 0;
        $total_e_tidak_tetap = 0;
        foreach ($dosens as $di) {
            if ($di['is_tetap']) {
                $tetap_epom_vals = array_merge($tetap_epom_vals, $di['epom_vals']);
                $total_e_tetap += isset($di['total_e']) ? $di['total_e'] : 0;
            } else {
                $tidak_tetap_epom_vals = array_merge($tidak_tetap_epom_vals, $di['epom_vals']);
                $total_e_tidak_tetap += isset($di['total_e']) ? $di['total_e'] : 0;
            }
        }
        $avg_tetap = !empty($tetap_epom_vals) ? round(array_sum($tetap_epom_vals)/count($tetap_epom_vals), 2) : 0;
        $avg_tidak_tetap = !empty($tidak_tetap_epom_vals) ? round(array_sum($tidak_tetap_epom_vals)/count($tidak_tetap_epom_vals), 2) : 0;
        
        $total_mhs_prodi = 0;
        if ($ppd_data && isset($ppd_data['kelas'])) {
            foreach ($ppd_data['kelas'] as $kls) {
                $total_mhs_prodi += array_sum($kls['grades']);
            }
        }
        $avg_epom_prodi = $ppd_data ? $ppd_data['avg_epom'] : 0;
        $max_epom_prodi = $ppd_data ? $ppd_data['max_epom'] : 0;
        $min_epom_prodi = $ppd_data ? $ppd_data['min_epom'] : 0;
      ?>
        <div class="dosen-prodi-hdr" style="margin-bottom:0;">
          🏫 <?= htmlspecialchars($pnm) ?>
          <span class="bdg"><?= count($dosens) ?> dosen</span>
        </div>
        
        <div style="background:#f8faff; border:1px solid var(--border); border-top:none; border-bottom:none; padding:18px; display:grid; grid-template-columns: repeat(2, 1fr); gap:16px; margin-bottom:0;">
            <!-- Dosen Tetap -->
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px; box-shadow:0 2px 4px rgba(0,0,0,0.02); display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="background:#dcfce7; color:#166534; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; border:1px solid #bbf7d0;">Tetap</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                    <div>
                        <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Avg EPOM</div>
                        <div style="font-weight:800; font-size:20px; color:<?= epom_color($avg_tetap) ?>; line-height:1; margin-top:2px;"><?= $avg_tetap > 0 ? number_format($avg_tetap, 2) : '—' ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Mhs Tdk Lulus (E)</div>
                        <div style="font-weight:800; font-size:20px; color:#e11d48; line-height:1; margin-top:2px;"><?= $total_e_tetap ?> <span style="font-size:12px; font-weight:600; color:var(--muted);">mhs</span></div>
                    </div>
                </div>
            </div>
            <!-- Dosen Tidak Tetap -->
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px; box-shadow:0 2px 4px rgba(0,0,0,0.02); display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="background:#f3f4f6; color:#4b5563; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:700; border:1px solid #e5e7eb;">Tdk Tetap</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                    <div>
                        <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Avg EPOM</div>
                        <div style="font-weight:800; font-size:20px; color:<?= epom_color($avg_tidak_tetap) ?>; line-height:1; margin-top:2px;"><?= $avg_tidak_tetap > 0 ? number_format($avg_tidak_tetap, 2) : '—' ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Mhs Tdk Lulus (E)</div>
                        <div style="font-weight:800; font-size:20px; color:#e11d48; line-height:1; margin-top:2px;"><?= $total_e_tidak_tetap ?> <span style="font-size:12px; font-weight:600; color:var(--muted);">mhs</span></div>
                    </div>
                </div>
            </div>
        </div>
        <table class="dosen-table" style="border:1px solid var(--border);border-top:none;margin-bottom:0;">
          <thead>
            <tr>
              <th style="width:28px;text-align:center;">#</th>
              <th>Nama Dosen</th>
              <th>Status Dosen</th>
              <th style="text-align:center;">Jml Kelas</th>
              <th style="text-align:center;">Total SKS</th>
              <th style="text-align:center;">Avg EPOM</th>
            </tr>
          </thead>
          <tbody>
            <?php $dn = 1; foreach ($dosens as $did => $di): ?>
            <tr>
              <td style="text-align:center;"><?= $dn++ ?></td>
              <td style="font-weight:600;"><?= htmlspecialchars($di['nm_ptk']) ?></td>
              <td>
                <?php if ($di['is_tetap']): ?>
                  <span style="display:inline-block;background:#dcfce7;color:#166534;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;border:1px solid #bbf7d0;">
                    <?= htmlspecialchars($di['status_dosen']) ?>
                  </span>
                <?php else: ?>
                  <span style="display:inline-block;background:#f3f4f6;color:#4b5563;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;border:1px solid #e5e7eb;">
                    <?= htmlspecialchars($di['status_dosen']) ?>
                  </span>
                <?php endif; ?>
              </td>
              <td style="text-align:center;">
                <span style="display:inline-block;background:#eff6ff;color:#1e3a8a;padding:2px 10px;border-radius:20px;font-weight:700;font-size:12px;">
                  <?= $di['kelas_count'] ?> kls
                </span>
              </td>
              <td style="text-align:center;font-weight:700;color:var(--primary);">
                <?= $di['total_sks'] ?> SKS
              </td>
              <td style="text-align:center;font-weight:700;color:<?= epom_color($di['avg_epom']) ?>;">
                <?= $di['avg_epom'] > 0 ? number_format($di['avg_epom'], 2) : '—' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <div style="margin-bottom: 24px;"></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    </div><!-- .sect-dosen -->
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     3. KINERJA PER PRODI
     ══════════════════════════════════════════════════════════════════ -->
<div class="page-section sect-prodi" style="background: transparent; box-shadow: none; padding: 0;">
  <div class="section-hdr" style="background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; margin-bottom: 24px;">
    <div class="ico gold">🎓</div>
    <div>
      <h2>Kinerja Akademik per Program Studi</h2>
      <p>Kelas dikelompokkan per semester, diurutkan EPOM tertinggi ke terendah</p>
    </div>
  </div>
  <div class="section-body" style="padding:14px 16px;">

  <?php if (!empty($prodi_data)): ?>
    <?php foreach ($prodi_data as $pid => $pd): ?>
    <div class="prodi-section" style="page-break-before: always; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 32px; overflow: hidden;">

      <!-- Prodi cover header -->
      <div class="prodi-section-hdr" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 16px 24px; border-radius: 0; margin-bottom: 0;">
        <div class="prodi-ico" style="background: rgba(255,255,255,0.15); width: 48px; height: 48px; font-size: 24px;">🏫</div>
        <div>
          <div class="prodi-title" style="color: #fff; font-size: 18px; font-weight: 800;"><?= htmlspecialchars($pd['prodi']) ?></div>
          <div class="prodi-sub" style="color: #bfdbfe; font-size: 13px; margin-top: 2px;"><?= count($pd['kelas']) ?> kelas mengajar</div>
        </div>
        <div class="prodi-avg" style="background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.4); font-size: 16px; padding: 6px 16px; border-radius: 30px;">⭐ <?= number_format($pd['avg_epom'],2) ?></div>
      </div>
      
      <div style="padding: 24px;">

      <?php
        // ── Group kelas by semester (2nd char of nm_kls) ────────────────
        $smt_groups = [];

        foreach ($pd['kelas'] as $kls) {
          $nm = $kls['nm_kls'];
          // Extract semester digit: 2nd character (index 1)
          $smt_digit = (strlen($nm) >= 2 && is_numeric($nm[1])) ? (int)$nm[1] : 0;
          if (!isset($smt_groups[$smt_digit])) {
              $smt_groups[$smt_digit] = [];
          }
          $smt_groups[$smt_digit][] = $kls;
        }
        // Sort smt_groups by semester number ascending
        ksort($smt_groups);

        // Within each group: sort by nilai_epom DESC
        foreach ($smt_groups as $sdig => &$klist) {
          usort($klist, fn($a,$b) => $b['nilai_epom'] <=> $a['nilai_epom']);
        }
        unset($klist);
      ?>

      <?php $is_last_group = false; $gi = 0; $total_groups = count($smt_groups); ?>
      <?php foreach ($smt_groups as $smt_digit => $klist):
        $gi++;
        $is_last_group = ($gi === $total_groups);
        $group_epom_vals = array_filter(array_column($klist, 'nilai_epom'), fn($v) => $v > 0);
        $group_avg = !empty($group_epom_vals) ? round(array_sum($group_epom_vals)/count($group_epom_vals),2) : 0;
        
        $smt_epom_tetap = [];
        $smt_epom_tidak_tetap = [];
        $smt_e_tetap = 0;
        $smt_e_tidak_tetap = 0;
        foreach ($klist as $kls) {
            $epom = (float)$kls['nilai_epom'];
            $e_count = isset($kls['grades']['E']) ? $kls['grades']['E'] : 0;
            if ($kls['is_tetap']) {
                if ($epom > 0) $smt_epom_tetap[] = $epom;
                $smt_e_tetap += $e_count;
            } else {
                if ($epom > 0) $smt_epom_tidak_tetap[] = $epom;
                $smt_e_tidak_tetap += $e_count;
            }
        }
        $smt_avg_tetap = !empty($smt_epom_tetap) ? round(array_sum($smt_epom_tetap)/count($smt_epom_tetap), 2) : 0;
        $smt_avg_tidak_tetap = !empty($smt_epom_tidak_tetap) ? round(array_sum($smt_epom_tidak_tetap)/count($smt_epom_tidak_tetap), 2) : 0;
        
        $smt_max_epom = !empty($group_epom_vals) ? round(max($group_epom_vals), 2) : 0;
        $smt_min_epom = !empty($group_epom_vals) ? round(min($group_epom_vals), 2) : 0;
        
        $smt_total_nilai = 0;
        $smt_unique_students = array();
        $smt_sebaran = ['A'=>0, 'B+'=>0, 'B'=>0, 'C+'=>0, 'C'=>0, 'D'=>0, 'E'=>0];
        foreach ($klist as $kls) {
            $smt_total_nilai += array_sum($kls['grades']);
            if (isset($kls['students'])) {
                foreach ($kls['students'] as $stu) {
                    $smt_unique_students[$stu] = 1;
                }
            }
            foreach ($smt_sebaran as $g => $v) {
                if (isset($kls['grades'][$g])) {
                    $smt_sebaran[$g] += $kls['grades'][$g];
                }
            }
        }
        $smt_total_mhs = count($smt_unique_students);
      ?>

      <!-- Semester chapter header -->
      <div class="smt-chapter-hdr" style="<?= $gi === 1 ? '' : 'page-break-before: always; break-before: page; margin-top:0;' ?>">
        <div class="smt-badge"><?= $smt_digit ?></div>
        <div>
          <div class="smt-label">Semester <?= $smt_digit ?></div>
          <div class="smt-count"><?= count($klist) ?> kelas</div>
        </div>
        
        <div style="display:flex; align-items:center; gap:12px; margin-left: 20px;">
           <div style="display:flex; align-items:center; gap:6px;">
              <span style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700; border:1px solid #bbf7d0;">Tetap</span>
              <span style="font-weight:800; font-size:13px; color:<?= epom_color($smt_avg_tetap) ?>;"><?= $smt_avg_tetap > 0 ? number_format($smt_avg_tetap, 2) : '—' ?></span>
              <?php if ($smt_e_tetap > 0): ?>
              <span style="font-size:10px; color:#e11d48; font-weight:600;">(<?= $smt_e_tetap ?> mhs E)</span>
              <?php endif; ?>
           </div>
           <div style="display:flex; align-items:center; gap:6px;">
              <span style="background:#f3f4f6; color:#4b5563; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:600; border:1px solid #e5e7eb;">Tdk Tetap</span>
              <span style="font-weight:800; font-size:13px; color:<?= epom_color($smt_avg_tidak_tetap) ?>;"><?= $smt_avg_tidak_tetap > 0 ? number_format($smt_avg_tidak_tetap, 2) : '—' ?></span>
              <?php if ($smt_e_tidak_tetap > 0): ?>
              <span style="font-size:10px; color:#e11d48; font-weight:600;">(<?= $smt_e_tidak_tetap ?> mhs E)</span>
              <?php endif; ?>
           </div>
        </div>

        <?php if ($group_avg > 0): ?>
        <div class="smt-avg-pill">⭐ Avg <?= number_format($group_avg,2) ?></div>
        <?php endif; ?>
      </div>
      
      <div style="background:#fff; border:1px solid var(--border); border-top:none; border-bottom:1px solid var(--border); padding:16px; display:flex; gap:24px; align-items:center;">
         <div style="flex: 1; display:grid; grid-template-columns: repeat(2, 1fr); gap:12px;">
             <div style="background:#f8faff; border:1px solid #e2e8f0; border-radius:6px; padding:12px; display:flex; flex-direction:column; justify-content:center;">
                 <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Avg EPOM Total</div>
                 <div style="font-weight:900; font-size:22px; color:<?= epom_color($group_avg) ?>; line-height:1; margin-top:4px;"><?= $group_avg > 0 ? number_format($group_avg, 2) : '—' ?></div>
             </div>
             <div style="background:#f8faff; border:1px solid #e2e8f0; border-radius:6px; padding:12px; display:flex; flex-direction:column; justify-content:center;">
                 <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">EPOM Tertinggi & Terendah</div>
                 <div style="font-size:13px; font-weight:700; color:#333; margin-top:6px; line-height:1.4;">
                     Max: <span style="color:var(--primary);"><?= $smt_max_epom > 0 ? number_format($smt_max_epom, 2) : '-' ?></span> <br>
                     Min: <span style="color:#ef4444;"><?= $smt_min_epom > 0 ? number_format($smt_min_epom, 2) : '-' ?></span>
                 </div>
             </div>
             <div style="background:#f8faff; border:1px solid #e2e8f0; border-radius:6px; padding:12px; display:flex; flex-direction:column; justify-content:center;">
                 <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Mahasiswa Aktif</div>
                 <div style="font-weight:900; font-size:22px; color:#2563eb; line-height:1; margin-top:4px;"><?= $smt_total_mhs ?> <span style="font-size:12px; font-weight:600; color:var(--muted);">mhs</span></div>
             </div>
             <div style="background:#f8faff; border:1px solid #e2e8f0; border-radius:6px; padding:12px; display:flex; flex-direction:column; justify-content:center;">
                 <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700;">Total Penilaian</div>
                 <div style="font-weight:900; font-size:22px; color:#64748b; line-height:1; margin-top:4px;"><?= $smt_total_nilai ?> <span style="font-size:12px; font-weight:600; color:var(--muted);">nilai</span></div>
             </div>
         </div>
         
         <div style="flex: 1; border-left:1px solid var(--border); padding-left:24px;">
            <div style="font-size:10px; color:var(--muted); text-transform:uppercase; font-weight:700; margin-bottom:12px; text-align:center;">Sebaran Nilai Semester <?= $smt_digit ?></div>
            <div style="display:flex; gap:12px; justify-content:center;">
               <?php 
               $max_sebaran = !empty($smt_sebaran) ? max($smt_sebaran) : 0;
               foreach (['A','B+','B','C+','C','D','E'] as $gl): 
                   $gcount = $smt_sebaran[$gl];
                   $gcolor = '#94a3b8';
                   if ($gl == 'A') $gcolor = '#10b981';
                   elseif ($gl == 'B+') $gcolor = '#0ea5e9';
                   elseif ($gl == 'B') $gcolor = '#3b82f6';
                   elseif ($gl == 'C+') $gcolor = '#8b5cf6';
                   elseif ($gl == 'C') $gcolor = '#f59e0b';
                   elseif ($gl == 'D') $gcolor = '#ef4444';
                   elseif ($gl == 'E') $gcolor = '#9ca3af';
               ?>
               <div style="display:flex; flex-direction:column; align-items:center; gap:4px; flex:1;">
                   <div style="font-size:11px; font-weight:700; color:#475569;"><?= $gcount ?></div>
                   <div style="height:45px; width:14px; background:#f1f5f9; border-radius:3px; position:relative; display:flex; align-items:flex-end; justify-content:center;">
                       <div style="width:100%; background:<?= $gcolor ?>; border-radius:3px; height:<?= $max_sebaran > 0 ? ($gcount/$max_sebaran)*100 : 0 ?>%;"></div>
                   </div>
                   <div style="font-size:12px; font-weight:900; color:<?= $gcolor ?>;"><?= $gl ?></div>
               </div>
               <?php endforeach; ?>
            </div>
         </div>
      </div>

      <!-- Kelas cards (1 column) -->
      <div class="kelas-list" style="<?= $is_last_group ? 'border-radius:0 0 12px 12px;' : 'border-radius:0;' ?>">
        <?php if (empty($klist)): ?>
          <div style="padding:15px; font-size:14px; color:var(--muted); font-style:italic; text-align:center;">
            Tidak ada kelas pada semester ini (atau kelas tidak masuk kriteria).
          </div>
        <?php else: ?>
          <?php $rank = 1; foreach ($klist as $kls):
            $epom     = (float)$kls['nilai_epom'];
            $epom_pct = ($epom / 4.0) * 100;
            $ec       = epom_color($epom);
            $grades   = $kls['grades'];
            $total_mhs = array_sum($grades);
            // Sort grades by count desc for display
            arsort($grades);
          ?>
          <div class="kelas-card">

            <!-- Rank + EPOM -->
            <div class="kelas-rank">
              <div class="rank-no">#<?= $rank ?></div>
              <div class="epom-score" style="color:<?= $ec ?>"><?= $epom > 0 ? number_format($epom,2) : '—' ?></div>
              <div class="epom-lbl">EPOM</div>
            </div>

            <!-- Info -->
            <div class="kelas-info">
              <div class="kelas-nm-mk">
                <span class="kelas-nm"><?= htmlspecialchars($kls['nm_kls']) ?></span>
                <?php if ($kls['kode_mk']): ?>
                  <span class="kelas-kode"><?= htmlspecialchars($kls['kode_mk']) ?></span>
                <?php endif; ?>
              </div>
              <div class="kelas-mk-nm" title="<?= htmlspecialchars($kls['nm_mk'] ?: '—') ?>">
                <?= htmlspecialchars($kls['nm_mk'] ?: '—') ?>
              </div>
              <div class="kelas-dosen">
                <span>👤</span>
                <span class="nm"><?= htmlspecialchars($kls['dosen'] ?: '—') ?></span>
                <?php if ($kls['is_tetap']): ?>
                  <span style="background:#dcfce7; color:#166534; padding:1px 6px; border-radius:4px; font-size:10px; font-weight:700; border:1px solid #bbf7d0; margin-left:4px;">Tetap</span>
                <?php else: ?>
                  <span style="background:#f3f4f6; color:#4b5563; padding:1px 6px; border-radius:4px; font-size:10px; font-weight:600; border:1px solid #e5e7eb; margin-left:4px;">Tdk Tetap</span>
                <?php endif; ?>
              </div>
              
              <div class="kelas-jadwal-wrapper" style="margin-top:5px; font-size:11px; color:var(--muted); display:flex; flex-direction:column; gap:2px;">
                <?php if (!empty($kls['jadwals'])): ?>
                  <?php foreach ($kls['jadwals'] as $jdwl): ?>
                    <div class="kelas-jadwal-item" style="display:flex; align-items:center; gap:4px;">
                      <span>🗓️</span>
                      <span style="font-weight:600; color:#475569;">
                        <?= htmlspecialchars($jdwl->nama_hari) ?>, <?= $jdwl->jam_mulai ?> - <?= $jdwl->jam_selesai ?>
                      </span>
                      <span style="background:#f1f5f9; padding:1px 4px; border-radius:3px; margin-left:2px; font-size:10px;">
                        R. <?= htmlspecialchars($jdwl->nama_ruang) ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="kelas-jadwal-item"><span style="font-style:italic;">Belum ada jadwal</span></div>
                <?php endif; ?>
                <div class="kelas-mhs-count" style="display:flex; align-items:center; gap:4px; margin-top:1px;">
                  <span>👥</span>
                  <span style="font-weight:600; color:#2563eb;"><?= $total_mhs ?> Mahasiswa</span>
                  <?php if ($total_mhs > 0): ?>
                    <a href="<?= base_url('C_laporan_semester/detail_nilai/' . $kls['id_kls']) ?>" target="_blank" class="no-print" style="margin-left: 6px; font-size:10px; padding:2px 8px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:4px; cursor:pointer; font-weight:700; text-decoration:none; transition: all 0.15s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'" title="Lihat Detail Nilai Mahasiswa">🔍 Detail Nilai</a>
                  <?php endif; ?>
                </div>
              </div>

              <!-- EPOM mini bar -->
              <div class="kelas-epom-bar">
                <div class="kelas-epom-bar-fill" style="width:<?= min(100,$epom_pct) ?>%;background:<?= $ec ?>;height:100%;border-radius:2px;"></div>
              </div>
            </div>

            <!-- Grade distribution: fixed horizontal bars -->
            <div class="kelas-grades">
              <?php if ($total_mhs > 0):
                // Tampilkan 7 kriteria standar (A sampai E)
                $std_grades = ['A', 'B+', 'B', 'C+', 'C', 'D', 'E'];
              ?>
                <div class="grade-top-lbl">Sebaran Nilai — <?= $total_mhs ?> mhs</div>
                <?php foreach ($std_grades as $huruf):
                  $jml   = isset($grades[$huruf]) ? $grades[$huruf] : 0;
                  $gc    = grade_class($huruf);
                  $gclr  = grade_color($huruf);
                  $gbar  = ($jml / $total_mhs) * 100;
                  $gpct  = round($gbar);
                ?>
                <div class="grade-row">
                  <span class="grade-letter <?= $gc ?>" style="<?= $jml == 0 ? 'background:#cbd5e1;' : '' ?>"><?= htmlspecialchars($huruf) ?></span>
                  <div class="grade-bar-wrap">
                    <div class="grade-bar-fill" style="width:<?= $gbar ?>%;background:<?= $gclr ?>;height:100%;border-radius:3px;"></div>
                  </div>
                  <span class="grade-count" style="<?= $jml == 0 ? 'color:#94a3b8;' : '' ?>"><?= $jml ?></span>
                  <span class="grade-pct" style="<?= $jml == 0 ? 'color:#cbd5e1;' : '' ?>"><?= $gpct ?>%</span>
                </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="no-grade">Belum ada data nilai</div>
              <?php endif; ?>
            </div>

          </div><!-- .kelas-card -->
          <?php $rank++; endforeach; ?>
        <?php endif; ?>
      </div><!-- .kelas-list -->

      <?php endforeach; /* smt_groups */ ?>
      
      </div><!-- padding 24px -->
    </div><!-- .prodi-section -->
    <?php endforeach; /* prodi_data */ ?>

  <?php else: ?>
    <div class="no-data"><span class="ico-big">📭</span>Belum ada data untuk semester ini.</div>
  <?php endif; ?>

  </div>
</div><!-- page-section -->

</div><!-- .print-area -->
<?php endif; ?>

<script>
function printMode(mode) {
  document.body.classList.add(mode);
  window.print();
  setTimeout(function() { document.body.classList.remove(mode); }, 1000);
}

function notifyHeight() {
  window.parent.postMessage({ type: 'setHeight', height: document.body.scrollHeight }, '*');
}
window.addEventListener('DOMContentLoaded', function() {
  notifyHeight();
  window.parent.postMessage({ type: 'loadingComplete' }, '*');
});
window.addEventListener('load', notifyHeight);
window.addEventListener('resize', notifyHeight);
setTimeout(notifyHeight, 400);
setTimeout(notifyHeight, 1200);

function changeSmt(id_smt, nm_smt) {
  window.parent.postMessage({ type: 'filterChange', id_smt: id_smt, nm_smt: nm_smt }, '*');
}
</script>
</body>
</html>
