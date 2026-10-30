<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Intake Quality - WBI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: #F0F4F8;
      color: #1A202C;
      padding: 24px;
    }

    /* ── Shimmer Animation ── */
    @keyframes shimmer {
      0% { background-position: -468px 0; }
      100% { background-position: 468px 0; }
    }
    .shimmer {
      animation: shimmer 1.5s linear infinite;
      background: #f6f7f8;
      background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
      background-size: 800px 104px;
      border-radius: 4px;
      color: transparent !important;
      overflow: hidden;
      display: inline-block;
    }
    .shimmer-block { height: 16px; width: 100%; border-radius: 4px; }
    .shimmer-circle { border-radius: 50%; }

    /* ── Main CSS ── */
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .header-title h1 { font-size: 24px; font-weight: 800; color: #1A202C; line-height: 1.2; }
    .header-title p { color: #718096; font-size: 13px; margin-top: 4px; }
    .filter-bar { display: flex; align-items: center; gap: 10px; }
    .filter-bar label { font-size: 13px; font-weight: 600; color: #4A5568; }
    .filter-bar select { border: 1.5px solid #CBD5E0; border-radius: 10px; padding: 8px 16px 8px 12px; font-size: 13px; font-weight: 600; color: #2D3748; background: #fff; cursor: pointer; outline: none; }
    .filter-bar select:focus { border-color: #4F46E5; }

    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .kpi-card { background: #fff; border-radius: 16px; padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 16px; transition: transform 0.2s; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }
    .kpi-card-header { display: flex; align-items: center; justify-content: space-between; }
    .kpi-card-title { font-size: 12px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.8px; }
    .kpi-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .kpi-chart-row { display: flex; align-items: center; gap: 16px; }
    .kpi-donut-wrap { width: 90px; height: 90px; position: relative; flex-shrink: 0; }
    .kpi-donut-label { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .kpi-donut-label span:first-child { font-size: 18px; font-weight: 800; color: #1A202C; }
    .kpi-donut-label span:last-child { font-size: 10px; color: #A0AEC0; font-weight: 600; }
    .kpi-legend { display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .kpi-legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; }
    .kpi-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .kpi-legend-label { color: #4A5568; font-weight: 500; flex: 1; }
    .kpi-legend-val { font-weight: 800; color: #1A202C; }
    .kpi-legend-pct { color: #A0AEC0; font-size: 12px; margin-left: 2px; }
    .kpi-bar-wrap { width: 100%; position: relative; }
    .kpi-bar-wrap canvas { max-height: 100px; }

    .bottom-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 16px; padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); height: 650px; }
    .card-title { font-size: 15px; font-weight: 700; color: #2D3748; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-title .badge { background: #EBF4FF; color: #3182CE; border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; }
    #intake-map { height: 530px; border-radius: 12px; z-index: 1; }
    #btnResetMap { position: absolute; top: 10px; right: 10px; z-index: 1000; background: #FFF; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; padding: 8px 16px; font-weight: 600; font-size: 13px; color: #4F46E5; cursor: pointer; display: none; }
    
    .province-table { width: 100%; border-collapse: collapse; }
    .province-table thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #A0AEC0; padding: 8px 10px; border-bottom: 2px solid #EDF2F7; text-align: left; }
    .province-table tbody td { padding: 9px 10px; font-size: 13px; color: #2D3748; border-bottom: 1px solid #EDF2F7; }
    .province-table-wrap { max-height: 530px; overflow-y: auto; }
    .schools-table-wrap { max-height: 400px; overflow-y: auto; margin-top: 24px; }

    .rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; font-size: 11px; font-weight: 800; margin-right: 6px; }
    .mhs-bar-bg { background: #EDF2F7; border-radius: 4px; height: 6px; margin-top: 3px; }
    .mhs-bar-fill { background: linear-gradient(90deg, #4F46E5, #7C3AED); border-radius: 4px; height: 6px; }

    @media (max-width: 900px) { .kpi-grid, .bottom-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<!-- ── Header ── -->
<div class="header">
  <div class="header-title">
    <h1>🎓 Intake Quality</h1>
    <p>Analisis asal sekolah mahasiswa WBI &mdash; berdasarkan data NPSN &amp; Dapo Sekolah</p>
  </div>
  <div class="filter-bar">
    <label for="tahunSelect">Filter Angkatan:</label>
    <select id="tahunSelect" onchange="applyFilter()">
      <option value="" <?= empty($tahun_filter) ? 'selected' : '' ?>>Semua Angkatan</option>
      <?php foreach ($angkatan_list as $a): ?>
        <option value="<?= $a->angkatan ?>"
          <?= $tahun_filter == $a->angkatan ? 'selected' : '' ?>
          <?= !$a->has_data ? 'style="color:#A0AEC0;"' : '' ?>>
          Angkatan <?= $a->angkatan ?><?= !$a->has_data ? ' (belum ada data)' : '' ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<!-- ── KPI Cards ── -->
<div class="kpi-grid">

  <!-- KPI 1: Negeri vs Swasta -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-card-title">Status Sekolah</span>
      <div class="kpi-icon" style="background:#EBF4FF;">🏛️</div>
    </div>
    <div class="kpi-chart-row">
      <div class="kpi-donut-wrap shimmer shimmer-circle" id="shimmer-chart1">
        <canvas id="chartNegeriSwasta" style="opacity:0;"></canvas>
        <div class="kpi-donut-label">
          <span id="val_total_all" class="shimmer">0000</span>
          <span>Total</span>
        </div>
      </div>
      <div class="kpi-legend">
        <div class="kpi-legend-item">
          <div class="kpi-legend-dot" style="background:#3B82F6;"></div>
          <span class="kpi-legend-label">Negeri</span>
          <span class="kpi-legend-val shimmer" id="val_negeri">000</span>
          <span class="kpi-legend-pct shimmer" id="pct_negeri">(0%)</span>
        </div>
        <div class="kpi-legend-item">
          <div class="kpi-legend-dot" style="background:#F97316;"></div>
          <span class="kpi-legend-label">Swasta</span>
          <span class="kpi-legend-val shimmer" id="val_swasta">000</span>
          <span class="kpi-legend-pct shimmer" id="pct_swasta">(0%)</span>
        </div>
      </div>
    </div>
  </div>

  <!-- KPI 2: SMA/MA vs SMK -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-card-title">Jenis Sekolah</span>
      <div class="kpi-icon" style="background:#F0FFF4;">📚</div>
    </div>
    <div class="kpi-chart-row">
      <div class="kpi-donut-wrap shimmer shimmer-circle" id="shimmer-chart2">
        <canvas id="chartJenis" style="opacity:0;"></canvas>
        <div class="kpi-donut-label">
          <span id="val_total_jenis" class="shimmer">0000</span>
          <span>Total</span>
        </div>
      </div>
      <div class="kpi-legend">
        <div class="kpi-legend-item">
          <div class="kpi-legend-dot" style="background:#10B981;"></div>
          <span class="kpi-legend-label">Selain SMK</span>
          <span class="kpi-legend-val shimmer" id="val_sma_ma">000</span>
          <span class="kpi-legend-pct shimmer" id="pct_sma_ma">(0%)</span>
        </div>
        <div class="kpi-legend-item">
          <div class="kpi-legend-dot" style="background:#8B5CF6;"></div>
          <span class="kpi-legend-label">SMK</span>
          <span class="kpi-legend-val shimmer" id="val_smk">000</span>
          <span class="kpi-legend-pct shimmer" id="pct_smk">(0%)</span>
        </div>
      </div>
    </div>
  </div>

  <!-- KPI 3: Top 5 Sekolah Asal -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-card-title">Top 5 Sekolah Asal</span>
      <div class="kpi-icon" style="background:#FFF5F5;">🏆</div>
    </div>
    <div class="kpi-bar-wrap">
      <div class="shimmer shimmer-block" style="height:90px;" id="shimmer-chart3"></div>
      <canvas id="chartTopSchools" style="display:none;"></canvas>
    </div>
  </div>
</div>

<!-- ── Map + Table ── -->
<div class="bottom-grid">

  <!-- Peta -->
  <div class="card">
    <div class="card-title">
      🗺️ Sebaran Asal Sekolah Mahasiswa
      <span class="badge shimmer" id="badge_provinsi_count">0 Provinsi</span>
    </div>
    <div style="position: relative;">
      <button id="btnResetMap" onclick="resetMap()">⬅️ Kembali ke Peta Nasional</button>
      <div class="shimmer" style="width:100%; height:530px; border-radius:12px;" id="shimmer-map"></div>
      <div id="intake-map" style="visibility:hidden; position:absolute; top:0; width:100%;"></div>
    </div>
  </div>

  <!-- Tabel Provinsi -->
  <div class="card">
    <div class="card-title">🏆 Ranking per Provinsi</div>
    <div class="province-table-wrap">
      <table class="province-table">
        <thead>
          <tr><th>#</th><th>Provinsi</th><th>Sekolah</th><th style="text-align:center;">Mhs</th></tr>
        </thead>
        <tbody id="tbody_provinsi">
          <tr><td colspan="4"><div class="shimmer shimmer-block"></div></td></tr>
          <tr><td colspan="4"><div class="shimmer shimmer-block"></div></td></tr>
          <tr><td colspan="4"><div class="shimmer shimmer-block"></div></td></tr>
          <tr><td colspan="4"><div class="shimmer shimmer-block"></div></td></tr>
          <tr><td colspan="4"><div class="shimmer shimmer-block"></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ── Tabel Seluruh Sekolah ── -->
<div class="card" style="height: auto;">
  <div class="card-title">🏫 Data Seluruh Sekolah Asal Mahasiswa</div>
  <div class="schools-table-wrap">
    <table class="province-table">
      <thead>
        <tr>
          <th style="width: 50px; text-align:center;">No</th>
          <th>Nama Sekolah</th>
          <th>Kabupaten / Kota</th>
          <th>Provinsi</th>
          <th style="text-align:center;">Jumlah Mahasiswa</th>
        </tr>
      </thead>
      <tbody id="tbody_semua_sekolah">
        <tr><td colspan="5"><div class="shimmer shimmer-block"></div></td></tr>
        <tr><td colspan="5"><div class="shimmer shimmer-block"></div></td></tr>
        <tr><td colspan="5"><div class="shimmer shimmer-block"></div></td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
// --- Globals ---
var map = null;
var provinsiLayer = null;
var sekolahLayer = null;
var chartNegeriSwastaObj = null;
var chartJenisObj = null;
var chartTopSchoolsObj = null;
var baseUrl = '<?= site_url("C_infografis_intake_quality") ?>';
var filterTahun = document.getElementById('tahunSelect').value;
var qStr = filterTahun ? ('?tahun=' + filterTahun) : '';

// Function to update progress overlay via postMessage
function updateProgress(step, pct, msg) {
    window.parent.postMessage({
        type: 'progressUpdate',
        step: step,
        pct: pct,
        msg: msg
    }, '*');
}

// Function to hide shimmer from an element
function removeShimmer(id) {
    var el = document.getElementById(id);
    if(el) {
        el.classList.remove('shimmer');
        // also remove from children with shimmer
        var children = el.querySelectorAll('.shimmer');
        children.forEach(c => c.classList.remove('shimmer'));
    }
}

function renderKPI(data) {
    let tot_all = data.negeri + data.swasta;
    let tot_jenis = data.sma_ma + data.smk;

    document.getElementById('val_total_all').innerText = tot_all;
    document.getElementById('val_negeri').innerText = data.negeri;
    document.getElementById('pct_negeri').innerText = '(' + (tot_all > 0 ? (data.negeri/tot_all*100).toFixed(1) : 0) + '%)';
    document.getElementById('val_swasta').innerText = data.swasta;
    document.getElementById('pct_swasta').innerText = '(' + (tot_all > 0 ? (data.swasta/tot_all*100).toFixed(1) : 0) + '%)';
    
    document.getElementById('val_total_jenis').innerText = tot_jenis;
    document.getElementById('val_sma_ma').innerText = data.sma_ma;
    document.getElementById('pct_sma_ma').innerText = '(' + (tot_jenis > 0 ? (data.sma_ma/tot_jenis*100).toFixed(1) : 0) + '%)';
    document.getElementById('val_smk').innerText = data.smk;
    document.getElementById('pct_smk').innerText = '(' + (tot_jenis > 0 ? (data.smk/tot_jenis*100).toFixed(1) : 0) + '%)';

    removeShimmer('shimmer-chart1');
    removeShimmer('shimmer-chart2');
    removeShimmer('val_total_all');
    removeShimmer('val_total_jenis');
    
    // Remove shimmer from legends
    removeShimmer('val_negeri');
    removeShimmer('pct_negeri');
    removeShimmer('val_swasta');
    removeShimmer('pct_swasta');
    removeShimmer('val_sma_ma');
    removeShimmer('pct_sma_ma');
    removeShimmer('val_smk');
    removeShimmer('pct_smk');

    var ctx1 = document.getElementById('chartNegeriSwasta');
    ctx1.style.opacity = 1;
    chartNegeriSwastaObj = new Chart(ctx1, {
        type: 'doughnut',
        data: { labels: ['Negeri', 'Swasta'], datasets: [{ data: [data.negeri, data.swasta], backgroundColor: ['#3B82F6', '#F97316'], borderWidth: 0, hoverOffset: 4 }] },
        options: { responsive: true, maintainAspectRatio: true, cutout: '72%', plugins: { legend: { display: false } } }
    });

    var ctx2 = document.getElementById('chartJenis');
    ctx2.style.opacity = 1;
    chartJenisObj = new Chart(ctx2, {
        type: 'doughnut',
        data: { labels: ['Selain SMK', 'SMK'], datasets: [{ data: [data.sma_ma, data.smk], backgroundColor: ['#10B981', '#8B5CF6'], borderWidth: 0, hoverOffset: 4 }] },
        options: { responsive: true, maintainAspectRatio: true, cutout: '72%', plugins: { legend: { display: false } } }
    });
}

function renderTopSchools(data) {
    let labels = data.map(s => s.nama_sekolah.length > 20 ? s.nama_sekolah.substring(0,20)+'...' : s.nama_sekolah);
    let values = data.map(s => parseInt(s.total));

    document.getElementById('shimmer-chart3').style.display = 'none';
    var ctx3 = document.getElementById('chartTopSchools');
    ctx3.style.display = 'block';

    chartTopSchoolsObj = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Mahasiswa', data: values, borderRadius: 4,
                backgroundColor: function(ctx) { const g = ctx.chart.ctx.createLinearGradient(0,0,0,100); g.addColorStop(0,'#F59E0B'); g.addColorStop(1,'#D97706'); return g; }
            }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: true }, y: { display: true, grid: { display: false }, ticks: { font: { size: 10 } } } } }
    });
}

function renderMap(data) {
    document.getElementById('shimmer-map').style.display = 'none';
    document.getElementById('intake-map').style.visibility = 'visible';
    
    document.getElementById('badge_provinsi_count').innerText = data.length + ' Provinsi';
    removeShimmer('badge_provinsi_count');

    map = L.map('intake-map').setView([-2.5, 118.0], 4);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
        maxZoom: 16
    }).addTo(map);

    provinsiLayer = L.featureGroup().addTo(map);
    sekolahLayer = L.featureGroup();

    if(data.length > 0) {
        var maxMhs = Math.max.apply(Math, data.map(m => m.jumlah_mahasiswa));
        var tbodyHtml = '';
        data.forEach(function(m, i) {
            // Marker
            var radius = Math.max(10, Math.sqrt(m.jumlah_mahasiswa / maxMhs) * 50);
            L.circleMarker([m.lat, m.lng], { radius: radius, fillColor: '#4F46E5', color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.75 })
            .bindPopup(
                '<div style="font-family:Inter,sans-serif; min-width:160px;">' +
                '<div style="font-weight:800; font-size:14px; margin-bottom:6px;">' + m.propinsi + '</div>' +
                '<div style="color:#718096; font-size:12px;">🏫 ' + m.jumlah_sekolah + ' sekolah</div>' +
                '<div style="color:#4F46E5; font-weight:700; font-size:16px; margin-top:6px; margin-bottom:12px;">👨‍🎓 ' + m.jumlah_mahasiswa + ' mahasiswa</div>' +
                '<button onclick="loadSekolahDetail(\'' + m.propinsi + '\')" style="width:100%; padding:6px; background:#4F46E5; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px; font-weight:600;">🔍 Lihat Detail Sekolah</button>' +
                '</div>'
            ).addTo(provinsiLayer);

            // Table Row
            var rank = i + 1;
            var rankClass = rank <= 3 ? 'rank-'+rank : 'rank-n';
            var widthPct = Math.round((m.jumlah_mahasiswa / maxMhs) * 100);
            tbodyHtml += `<tr>
                <td><span class="rank-badge ${rankClass}">${rank}</span></td>
                <td>
                    <div style="font-weight:600; font-size:12.5px;">${m.propinsi}</div>
                    <div class="mhs-bar-bg"><div class="mhs-bar-fill" style="width:${widthPct}%;"></div></div>
                </td>
                <td style="color:#718096; font-size:12px;">${m.jumlah_sekolah}</td>
                <td style="text-align:center;"><strong>${m.jumlah_mahasiswa}</strong></td>
            </tr>`;
        });
        document.getElementById('tbody_provinsi').innerHTML = tbodyHtml;
    } else {
        document.getElementById('tbody_provinsi').innerHTML = '<tr><td colspan="4" style="text-align:center; color:#A0AEC0;">Tidak ada data.</td></tr>';
    }
}

function renderSemuaSekolah(data) {
    if(data.length > 0) {
        var tbodyHtml = '';
        data.forEach(function(s, i) {
            tbodyHtml += `<tr>
                <td style="text-align: center; color: #718096; font-size: 13px;">${i+1}</td>
                <td style="font-weight: 600; color: #2D3748; font-size: 13px;">${s.sekolah}</td>
                <td style="color: #4A5568; font-size: 12.5px;">${s.kabupaten_kota}</td>
                <td style="color: #4A5568; font-size: 12.5px;">${s.propinsi}</td>
                <td style="text-align: center;">
                    <strong>${s.total}</strong>
                    <button onclick="printMahasiswa('${s.npsn}')" style="margin-left:8px; padding:4px 8px; font-size:11px; background:#10B981; color:white; border:none; border-radius:4px; cursor:pointer;" title="Cetak Daftar Mahasiswa">🖨️ Cetak</button>
                </td>
            </tr>`;
        });
        document.getElementById('tbody_semua_sekolah').innerHTML = tbodyHtml;
    } else {
        document.getElementById('tbody_semua_sekolah').innerHTML = '<tr><td colspan="5" style="text-align:center; color:#A0AEC0;">Tidak ada data sekolah.</td></tr>';
    }
}

function loadData() {
    // Sequentially run promises to allow UI progress bar updates
    fetch(baseUrl + '/ajax_get_kpi' + qStr)
    .then(res => res.json())
    .then(res => {
        renderKPI(res.data);
        updateProgress(1, 25, 'Memuat Top 5 Sekolah...');
        return fetch(baseUrl + '/ajax_get_top_schools' + qStr);
    })
    .then(res => res.json())
    .then(res => {
        renderTopSchools(res.data);
        updateProgress(2, 50, 'Memuat Peta Sebaran...');
        return fetch(baseUrl + '/ajax_get_sebaran_provinsi' + qStr);
    })
    .then(res => res.json())
    .then(res => {
        renderMap(res.data);
        updateProgress(3, 75, 'Memuat Detail Semua Sekolah...');
        return fetch(baseUrl + '/ajax_get_semua_sekolah' + qStr);
    })
    .then(res => res.json())
    .then(res => {
        renderSemuaSekolah(res.data);
        updateProgress(4, 100, 'Selesai!');
        // Tell parent loading is complete
        window.parent.postMessage({ type: 'loadingComplete' }, '*');
        setTimeout(() => {
            sendHeight(); // adjust iframe height
        }, 500);
    })
    .catch(err => {
        console.error(err);
        document.getElementById('loading-msg').innerText = 'Gagal memuat data.';
    });
}

window.addEventListener('DOMContentLoaded', () => {
    loadData();
});

// ── Peta Drill-down Logic ────────────────────────────────────
function loadSekolahDetail(propinsi) {
  map.closePopup();
  var url = baseUrl + '/get_sekolah_by_provinsi?propinsi=' + encodeURIComponent(propinsi) + (filterTahun ? '&tahun=' + filterTahun : '');
  
  fetch(url).then(response => response.json()).then(res => {
      if(res.status === 'success') {
        map.removeLayer(provinsiLayer);
        sekolahLayer.clearLayers();
        if (res.data.length > 0) {
          var maxMhsSekolah = Math.max.apply(Math, res.data.map(s => parseInt(s.jumlah_mahasiswa)));
          res.data.forEach(function(s) {
            var mhs = parseInt(s.jumlah_mahasiswa);
            var r = Math.max(5, Math.sqrt(mhs / maxMhsSekolah) * 20);
            var color = s.bentuk === 'SMK' ? '#8B5CF6' : '#10B981';
            
            L.circleMarker([s.lat, s.lng], { radius: r, fillColor: color, color: '#fff', weight: 1, opacity: 1, fillOpacity: 0.8 })
            .bindPopup(
              '<div style="font-family:Inter,sans-serif;">' +
              '<div style="font-weight:700; font-size:13px; margin-bottom:4px;">' + s.sekolah + '</div>' +
              '<div style="color:#718096; font-size:11px;">Bentuk: ' + s.bentuk + '</div>' +
              '<div style="color:#4F46E5; font-weight:700; font-size:13px; margin-top:4px;">👨‍🎓 ' + mhs + ' mahasiswa</div>' +
              '</div>'
            ).addTo(sekolahLayer);
          });
        }
        sekolahLayer.addTo(map);
        document.getElementById('btnResetMap').style.display = 'block';
        if (sekolahLayer.getBounds().isValid()) {
          map.flyToBounds(sekolahLayer.getBounds(), { padding: [30, 30], duration: 1 });
        }
      }
  }).catch(err => alert('Gagal memuat detail sekolah.'));
}

function resetMap() {
  map.removeLayer(sekolahLayer);
  provinsiLayer.addTo(map);
  document.getElementById('btnResetMap').style.display = 'none';
  if (provinsiLayer.getBounds().isValid()) {
    map.flyToBounds(provinsiLayer.getBounds(), { padding: [20, 20], duration: 1 });
  } else {
    map.flyTo([-2.5, 118.0], 4, {duration: 1});
  }
}

// ── Interactivity ───────────────────────────────────────────
function applyFilter() {
  var tahun = document.getElementById('tahunSelect').value;
  window.parent.postMessage({ type: 'filterChange', tahun: tahun }, '*');
}

function printMahasiswa(npsn) {
  var url = baseUrl + '/print_mahasiswa?npsn=' + encodeURIComponent(npsn);
  if (filterTahun) url += '&tahun=' + encodeURIComponent(filterTahun);
  window.open(url, '_blank');
}

function sendHeight() {
  window.parent.postMessage({ type: 'setHeight', height: document.body.scrollHeight + 40 }, '*');
}
window.addEventListener('resize', sendHeight);
</script>
</body>
</html>
