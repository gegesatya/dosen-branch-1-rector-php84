<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Performa EPOM Dosen</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
  --primary: #1e3a8a;
  --accent: #2563eb;
  --bg: #f8fafc;
  --text: #1e293b;
  --muted: #64748b;
  --border: #e2e8f0;
}
* { box-sizing: border-box; }
body {
  margin: 0; padding: 20px;
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text);
  -webkit-font-smoothing: antialiased;
}

/* Toolbar */
.toolbar {
  display: flex; align-items: center; gap: 15px;
  background: #fff; padding: 15px 25px;
  border-radius: 12px; margin-bottom: 25px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
  border: 1px solid var(--border);
}
.toolbar label { font-weight: 700; color: var(--primary); font-size: 14px; }
.toolbar select {
  padding: 8px 16px; border-radius: 8px; border: 2px solid #cbd5e1;
  font-family: inherit; font-size: 14px; font-weight: 600; color: var(--text);
  outline: none; transition: border-color 0.2s;
  min-width: 200px;
}
.toolbar select:focus { border-color: var(--accent); }
.btn-print {
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff; border: none; padding: 10px 20px;
  border-radius: 8px; font-family: inherit; font-size: 14px; font-weight: 700;
  cursor: pointer; transition: transform 0.1s, box-shadow 0.2s;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}
.btn-print:active { transform: translateY(1px); }
.btn-print:hover { filter: brightness(1.1); }

/* Main layout */
.print-area { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
.page-section { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; border: 1px solid var(--border); }
.section-hdr { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 16px; }
.section-hdr .ico { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.section-hdr .ico.blue { background: #eff6ff; color: #2563eb; }
.section-hdr h2 { margin: 0 0 4px; font-size: 20px; font-weight: 800; color: var(--text); }
.section-hdr p { margin: 0; font-size: 14px; color: var(--muted); }

/* Prodi Header */
.prodi-section { margin-bottom: 28px; }
.prodi-section-hdr {
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  color: #fff; padding: 12px 20px;
  display: flex; align-items: center; gap: 12px;
  border-radius: 12px; margin-bottom: 15px;
}
.prodi-section-hdr .prodi-ico {
  width: 34px; height: 34px;
  background: rgba(255,255,255,0.15); border-radius: 9px;
  display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.prodi-section-hdr .prodi-title { font-size: 15px; font-weight: 700; }
.prodi-section-hdr .prodi-sub   { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 1px; }

/* Cards Grid */
.dosen-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}

.dosen-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
}
.dosen-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.dosen-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.dosen-name {
  font-weight: 800;
  color: var(--text);
  font-size: 15px;
  line-height: 1.3;
  margin-bottom: 4px;
}
.dosen-stats {
  font-size: 12px;
  color: var(--muted);
  font-weight: 600;
}
.status-badge {
  padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-block;
  white-space: nowrap;
}
.status-tetap { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.status-tidak { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.epom-avg-box {
  background: #f8faff;
  border-radius: 8px;
  padding: 8px 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid #e2e8f0;
}
.epom-avg-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--muted);
}
.epom-avg-val {
  font-size: 18px;
  font-weight: 900;
}

.chart-container {
  position: relative;
  height: 140px;
  width: 100%;
}

/* Print styles */
@media print {
  @page { size: A4; margin: 12mm 10mm 12mm 10mm; }
  body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; color-adjust: exact; }
  .toolbar { display: none !important; }
  .print-area { max-width: 100%; margin: 0; padding: 0; }
  .page-section { box-shadow: none; border-radius: 0; border: none; margin-bottom: 0; }
  .section-hdr h2 { font-size: 24px; }
  
  .prodi-section-hdr { background: #f8faff !important; padding: 12px 20px !important; border-bottom: 2px solid #e2e8f0 !important; color: #1e293b !important; }
  .prodi-section-hdr .prodi-title { color: #1e293b !important; }
  .prodi-section-hdr .prodi-sub { color: #64748b !important; }
  .prodi-section-hdr .prodi-ico { background: #e2e8f0 !important; font-size: 24px !important; width: 48px !important; height: 48px !important; }
  
  .dosen-card { break-inside: avoid; border: 1px solid #cbd5e1; box-shadow: none; }
  
  /* Fix Chart overflow in print */
  .dosen-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 15px !important;
  }
  .chart-container {
    width: 100% !important;
    max-width: 100% !important;
  }
  .chart-container canvas {
    width: 100% !important;
    max-width: 100% !important;
    height: 140px !important;
    object-fit: contain;
  }
}
</style>
</head>
<body>

<div class="toolbar">
  <label for="smt-select"><i class="fa fa-calendar" style="margin-right: 5px;"></i> Semester:</label>
  <select id="smt-select" onchange="changeSmt(this.value, this.options[this.selectedIndex].text)">
    <?php foreach ($smt_list as $s): ?>
      <option value="<?= $s->id_smt ?>"
        <?= (isset($active_smt) && $active_smt == $s->id_smt) ? 'selected' : '' ?>>
        <?= htmlspecialchars($s->nm_smt) ?>
      </option>
    <?php endforeach; ?>
  </select>
  
  <div style="display:flex; gap:8px; margin-left:auto;">
    <button class="btn-print" onclick="window.print()">
      <i class="fa fa-print" style="margin-right: 5px;"></i> Cetak / PDF
    </button>
  </div>
</div>

<?php if (!isset($active_smt) || !$active_smt): ?>
<div class="print-area">
  <div class="page-section" style="padding: 40px; text-align: center;">
    <div style="font-size:40px; margin-bottom:10px;"><i class="fa fa-list-alt" style="color: #cbd5e1;"></i></div>
    <div style="color:var(--muted); font-size:15px;">Pilih semester dari dropdown untuk menampilkan performa EPOM.</div>
  </div>
</div>
<?php else: ?>

<?php
  // Helper: EPOM color
  function epom_color($v) {
    if ($v >= 3.5) return '#059669';
    if ($v >= 3.0) return '#0ea5e9';
    if ($v >= 2.5) return '#d97706';
    if ($v >= 2.0) return '#be123c';
    return '#64748b';
  }
  
  $smt_labels = [];
  foreach ($recent_smts as $rs) {
      $smt_labels[] = $rs->id_smt;
  }
?>

<div class="print-area">
  
  <div class="page-section" style="margin-bottom: 20px;">
    <div class="section-hdr">
      <div class="ico blue"><i class="fa fa-users"></i></div>
      <div>
        <h2>Kinerja EPOM Dosen WBI</h2>
        <p>
          Rata-Rata 4 Semester Terakhir dari Semester <strong><?= htmlspecialchars($active_smt) ?></strong> 
          (<?= implode(', ', array_column($recent_smts, 'nm_smt')) ?>)
        </p>
      </div>
    </div>
  </div>

  <?php if (empty($prodi_map)): ?>
    <div class="page-section" style="padding: 40px; text-align: center; color: var(--muted);">Belum ada data EPOM untuk periode ini.</div>
  <?php else: ?>
    
    <?php $chart_data = []; ?>
    <?php foreach ($prodi_map as $id_sms => $pdata): 
        if (empty($pdata['dosen'])) continue;
    ?>
    <div class="prodi-section">
      <div class="prodi-section-hdr" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="prodi-ico"><i class="fa fa-university"></i></div>
          <div>
            <div class="prodi-title"><?= htmlspecialchars($pdata['nm_lemb']) ?></div>
            <div class="prodi-sub"><?= count($pdata['dosen']) ?> Dosen</div>
          </div>
        </div>
        <div style="text-align: right;">
          <div style="font-size: 15px; font-weight: 700;">Rata-rata Prodi: <?= number_format($pdata['avg_epom'], 2) ?></div>
          <div style="font-size: 12px; color: rgba(255,255,255,0.8);">Deviasi Standar (SD): <?= number_format($pdata['sd_epom'], 2) ?></div>
        </div>
      </div>

      <?php
        $sd = $pdata['sd_epom'];
        if ($sd < 0.25) {
            $narasi = "Kualitas mengajar sangat merata (deviasi sangat kecil). Hampir semua dosen memiliki performa serupa.";
            $bg_narasi = "#dcfce7"; $color_narasi = "#166534"; $icon = "<i class='fa fa-check-circle' style='margin-right: 5px;'></i>";
        } elseif ($sd <= 0.45) {
            $narasi = "Kualitas mengajar cukup merata, namun ada sedikit variasi nilai antar dosen.";
            $bg_narasi = "#fef9c3"; $color_narasi = "#854d0e"; $icon = "<i class='fa fa-balance-scale' style='margin-right: 5px;'></i>";
        } else {
            $narasi = "Distribusi nilai berfluktuasi tinggi. Terdapat ketimpangan performa (gap) antar dosen yang signifikan.";
            $bg_narasi = "#fee2e2"; $color_narasi = "#991b1b"; $icon = "<i class='fa fa-exclamation-triangle' style='margin-right: 5px;'></i>";
        }
      ?>
      <div style="background: <?= $bg_narasi ?>; color: <?= $color_narasi ?>; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid <?= $color_narasi ?>40;">
        <?= $icon ?> <strong>Analisis Distribusi:</strong> <?= $narasi ?>
      </div>
      
      <div class="dosen-grid">
        <?php foreach ($pdata['dosen'] as $d): 
            $chart_id = 'chart_' . md5($d->id_sms . '_' . $d->id_ptk);
            $chart_values = [];
            $active_vals = [];
            foreach ($recent_smts as $rs) {
                $val = isset($d->smt_data[$rs->id_smt]) ? $d->smt_data[$rs->id_smt] : 0;
                $chart_values[] = $val;
                if ($val > 0) $active_vals[] = $val;
            }
            
            // Logic Narasi Per Dosen
            if ($d->kpi_epom >= 3.6) {
                $kat = "sangat memuaskan";
                $n_color = "#166534"; $n_bg = "#dcfce7"; $n_icon = "fa-star";
            } elseif ($d->kpi_epom >= 3.0) {
                $kat = "baik";
                $n_color = "#0369a1"; $n_bg = "#e0f2fe"; $n_icon = "fa-thumbs-up";
            } elseif ($d->kpi_epom >= 2.5) {
                $kat = "cukup";
                $n_color = "#854d0e"; $n_bg = "#fef9c3"; $n_icon = "fa-minus-circle";
            } else {
                $kat = "perlu evaluasi";
                $n_color = "#991b1b"; $n_bg = "#fee2e2"; $n_icon = "fa-exclamation-circle";
            }

            if (count($active_vals) >= 2) {
                $first = $active_vals[0];
                $last = end($active_vals);
                $diff = $last - $first;
                
                $is_inc = true; $is_dec = true;
                for($i=1; $i<count($active_vals); $i++){
                    if($active_vals[$i] < $active_vals[$i-1]) $is_inc = false;
                    if($active_vals[$i] > $active_vals[$i-1]) $is_dec = false;
                }
                
                if ($is_inc && count($active_vals) >= 3) $trend = "dengan tren yang terus naik konsisten";
                elseif ($is_dec && count($active_vals) >= 3) $trend = "namun trennya terus menurun beruntun";
                elseif ($diff > 0.15) $trend = "dengan peningkatan nilai di semester akhir";
                elseif ($diff < -0.15) $trend = "namun mengalami penurunan di semester akhir";
                else $trend = "dan tergolong cukup stabil";
                
                $narasi_dosen = "Kinerja secara umum <strong>{$kat}</strong> {$trend}.";
            } else {
                $narasi_dosen = "Kinerja <strong>{$kat}</strong> (baru aktif 1 semester).";
            }
            
            $chart_data[] = [
                'id' => $chart_id,
                'data' => $chart_values
            ];
        ?>
        <div class="dosen-card">
          <div class="dosen-card-header">
            <div>
              <div class="dosen-name"><?= htmlspecialchars($d->nm_ptk ?: 'Tanpa Nama') ?></div>
              <div class="dosen-stats"><?= $d->jml_smt_mengajar ?> smt • <?= $d->jml_kls_mengajar ?> kls</div>
            </div>
            <div>
              <?php if ($d->is_tetap): ?>
                <span class="status-badge status-tetap">Tetap</span>
              <?php else: ?>
                <span class="status-badge status-tidak">Tdk Tetap</span>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="chart-container">
            <canvas id="<?= $chart_id ?>"></canvas>
          </div>
          
          <div class="epom-avg-box">
            <span class="epom-avg-label">Rata-rata <?= $d->jml_smt_mengajar ?> Smt</span>
            <span class="epom-avg-val" style="color:<?= epom_color($d->kpi_epom) ?>;">
              <?= number_format($d->kpi_epom, 2) ?>
            </span>
          </div>
          
          <div style="font-size: 11px; background: <?= $n_bg ?>; color: <?= $n_color ?>; padding: 8px 10px; border-radius: 6px; line-height: 1.4; margin-top: auto;">
            <i class="fa <?= $n_icon ?>" style="margin-right: 4px;"></i> <?= $narasi_dosen ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

  <?php endif; ?>
</div>

<?php endif; ?>

<script>
function changeSmt(val, text) {
  window.parent.postMessage({ type: 'filterChange', id_smt: val, nm_smt: text.trim() }, '*');
}

// Post height for dynamic iframe resize
function reportHeight() {
  const h = document.documentElement.scrollHeight || document.body.scrollHeight;
  window.parent.postMessage({ type: 'setHeight', height: h + 40 }, '*');
}
window.addEventListener('load', reportHeight);
const observer = new ResizeObserver(() => reportHeight());
observer.observe(document.body);

// Render Charts
document.addEventListener("DOMContentLoaded", function() {
    <?php if (isset($chart_data) && !empty($chart_data)): ?>
    const labels = <?= json_encode($smt_labels) ?>;
    const chartDataList = <?= json_encode($chart_data) ?>;
    
    chartDataList.forEach(item => {
        const ctx = document.getElementById(item.id);
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai EPOM',
                        data: item.data,
                        backgroundColor: item.data.map(val => {
                            if (val >= 3.5) return 'rgba(5, 150, 105, 0.8)'; // green
                            if (val >= 3.0) return 'rgba(14, 165, 233, 0.8)'; // blue
                            if (val >= 2.5) return 'rgba(217, 119, 6, 0.8)'; // orange
                            if (val > 0) return 'rgba(190, 18, 60, 0.8)'; // red
                            return 'rgba(100, 116, 139, 0.3)'; // gray
                        }),
                        borderRadius: 4,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y > 0 ? context.parsed.y.toFixed(2) : 'Tidak ada data';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4.0,
                            ticks: { font: { size: 10 }, stepSize: 1 },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    },
                    animation: { duration: 0 } // Disable animation for better iframe rendering
                }
            });
        }
    });
    <?php endif; ?>
});
</script>

</body>
</html>
