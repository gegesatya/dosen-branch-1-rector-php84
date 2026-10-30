<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Kinerja Lulusan - Politeknik WBI</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    body {
      font-family: 'Inter', sans-serif;
      color: #334155;
      background: #fff;
      line-height: 1.5;
    }

    .report-header {
      border-bottom: 3px solid #0f172a;
      margin-bottom: 30px;
      padding-bottom: 10px;
    }

    .section-title {
      background: #f1f5f9;
      padding: 10px 15px;
      border-left: 5px solid #2563eb;
      font-weight: 700;
      margin-top: 40px;
      margin-bottom: 20px;
      page-break-after: avoid;
    }

    .kpi-table th {
      background: #f8fafc;
      font-weight: 600;
    }

    .chart-container {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }

    @media print {
      .no-print { display: none; }
      .page-break { page-break-before: always; }
      body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; margin: 0; }
      .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
      @page { size: A4; margin: 1.5cm; }
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.85rem;
    }
    
    .status-good { background: #dcfce7; color: #166534; }
    .status-warning { background: #fef9c3; color: #854d0e; }
    .status-danger { background: #fee2e2; color: #991b1b; }
  </style>
</head>
<body>

<div class="container my-5">
  <div class="no-print text-end mb-4">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak Laporan</button>
  </div>

  
  <div class="report-header d-flex justify-content-between align-items-center">
    <div>
      <h1 class="fw-bold mb-1">Laporan Kinerja Lulusan</h1>
      <p class="text-muted mb-0">Politeknik Wilmar Bisnis Indonesia | Periode Laporan: <?= date('d F Y') ?></p>
    </div>
    <div class="text-end">
      <img src="<?= base_url('assets/image_web/logo-white-3.png') ?>" alt="Logo WBI" style="height: 65px; object-fit: contain;">
    </div>
  </div>

  <?php 
    function render_section($title, $stats, $is_overall = false) {
      $total = $stats->kpi->total_lulusan;
      $ptw = $total > 0 ? round(($stats->kpi->lulus_tepat_waktu / $total) * 100) : 0;
      $cumlaude = $total > 0 ? round(($stats->kpi->total_cumlaude / $total) * 100) : 0;
      $masa_studi = number_format($stats->kpi->rata_masa_studi, 1);
  ?>
    <div class="section-title h4"><?= $title ?></div>
    
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered kpi-table mb-3">
          <thead>
            <tr>
              <th style="width: 70%;">Indikator Utama</th>
              <th class="text-center">Nilai</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Total Lulusan Terdaftar</td>
              <td class="text-center fw-bold"><?= $total ?> Mhs</td>
            </tr>
            <tr>
              <td>Rasio Kelulusan Tepat Waktu (PTW)</td>
              <td class="text-center fw-bold">
                <?= $ptw ?>% 
                <span class="status-badge <?= $ptw >= 50 ? 'status-good' : ($ptw >= 40 ? 'status-warning' : 'status-danger') ?>">
                  <?= $ptw >= 50 ? 'Unggul' : ($ptw >= 40 ? 'Baik' : 'Perlu Perbaikan') ?>
                </span>
              </td>
            </tr>
            <tr>
              <td>Rasio Lulusan "Dengan Pujian"</td>
              <td class="text-center fw-bold"><?= $cumlaude ?>%</td>
            </tr>
            <tr>
              <td>Rata-rata Masa Studi</td>
              <td class="text-center fw-bold"><?= $masa_studi ?> Tahun</td>
            </tr>
          </tbody>
        </table>

        
        <div class="p-3 bg-light rounded-3 mb-4 border" style="line-height: 1.6; font-size: 14px; text-align: justify;">
          <strong>Analisis Kinerja:</strong> 
          Pada bagian <?= $title ?>, tercatat total kontribusi lulusan sebanyak <strong><?= $total ?> jiwa</strong>. 
          Kualitas akademik menunjukkan tren yang positif dengan <strong><?= $cumlaude ?>%</strong> lulusan meraih predikat <strong>Dengan Pujian</strong>. 
          Rata-rata mahasiswa menyelesaikan studi dalam waktu <strong><?= $masa_studi ?> tahun</strong>.
          Kinerja ketepatan waktu kelulusan (PTW) mencapai <strong><?= $ptw ?>%</strong>, yang secara administratif dikategorikan 
          <span class="fw-bold <?= $ptw >= 50 ? 'text-success' : ($ptw >= 40 ? 'text-primary' : 'text-danger') ?>">
            <?= $ptw >= 50 ? 'Sangat Memuaskan (Memenuhi Skor Unggul)' : ($ptw >= 40 ? 'Memadai' : 'Perlu Evaluasi Mendalam') ?>
          </span>.
        </div>

        <?php if($stats->mahasiswa_berisiko > 0): ?>
          <div class="alert alert-danger py-2 px-3 d-flex align-items-center mb-4" style="font-size: 0.95rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span><strong>Peringatan Khusus:</strong> Terdapat <?= $stats->mahasiswa_berisiko ?> mahasiswa aktif yang berisiko lulus terlambat karena masa studi telah melebihi 4 tahun.</span>
          </div>
        <?php endif; ?>
      </div>

      
      <div class="col-12 mb-4">
        <div class="chart-container" style="min-height: 280px;">
          <div class="fw-bold mb-3 small text-uppercase text-muted border-bottom pb-2">Volume Lulusan (6 Tahun)</div>
          <div id="chart_total_<?= md5($title) ?>"></div>
        </div>
      </div>
      <div class="col-12">
        <div class="chart-container" style="min-height: 280px;">
          <div class="fw-bold mb-3 small text-uppercase text-muted border-bottom pb-2">Tren Ketepatan Waktu</div>
          <div id="chart_ptw_<?= md5($title) ?>"></div>
        </div>
      </div>
    </div>

    <script>
      (function() {
        // Chart 1: Total Volume
        var optTotal = {
          series: [{ name: 'Total Lulusan', data: <?= json_encode($stats->trend->lulusan) ?> }],
          chart: { type: 'bar', height: 220, toolbar: {show: false} },
          colors: ['#0f172a'],
          plotOptions: { bar: { columnWidth: '50%', borderRadius: 4, dataLabels: { position: 'top' } } },
          xaxis: { categories: <?= json_encode($stats->trend->categories) ?> },
          dataLabels: { enabled: true, offsetBottom: 10, style: { fontSize: '10px', colors: ['#fff'] } },
          grid: { borderColor: '#f1f5f9' }
        };
        new ApexCharts(document.querySelector("#chart_total_<?= md5($title) ?>"), optTotal).render();

        // Chart 2: Stacked PTW
        var optPTW = {
          series: [{
            name: 'Tepat Waktu',
            data: <?= json_encode($stats->trend->tepat_waktu) ?>
          }, {
            name: 'Terlambat',
            data: <?= json_encode($stats->trend->tidak_tepat_waktu) ?>
          }],
          chart: { type: 'bar', height: 220, stacked: true, toolbar: {show: false} },
          colors: ['#2563eb', '#e2e8f0'],
          plotOptions: { bar: { columnWidth: '50%', borderRadius: 4 } },
          xaxis: { categories: <?= json_encode($stats->trend->categories) ?> },
          legend: { position: 'bottom', fontSize: '11px' },
          dataLabels: { enabled: false },
          grid: { borderColor: '#f1f5f9' }
        };
        new ApexCharts(document.querySelector("#chart_ptw_<?= md5($title) ?>"), optPTW).render();
      })();
    </script>
  <?php } ?>

  
  <?php render_section('Ringkasan Institusi (WBI)', $overall, true); ?>

  
  <?php foreach($prodi_details as $p): ?>
    <div class="page-break"></div>
    <?php render_section('Rincian Prodi: ' . $p['info']->nm_lemb, $p['stats']); ?>
  <?php endforeach; ?>

  <div class="mt-5 pt-5 text-center text-muted small border-top">
    Dicetak melalui Sistem Informasi Akademik Politeknik WBI pada <?= date('d/m/Y H:i') ?>
  </div>
</div>

</body>
</html>
