
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Infografis Profil Lulusan</title>

  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>
    :root {
      --bg-color: #f8fafc;
      --card-bg: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --primary: #2563eb;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --radius: 20px;
    }

    body {
      background: var(--bg-color);
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--text-main);
    }

    .dashboard-title {
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.5px;
    }

    .card-info {
      background: var(--card-bg);
      border: 1px solid rgba(0,0,0,0.03);
      border-radius: var(--radius);
      box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
      position: relative;
      overflow: hidden;
    }

    /* Executive Insight Box */
    .insight-hero {
      background: linear-gradient(135deg, #0f4c81, #1e3a8a);
      color: white;
      border-radius: var(--radius);
      padding: 30px;
      position: relative;
      overflow: hidden;
    }
    
    .insight-hero::after {
      content: '\F4B4'; /* bootstrap icon sparkle */
      font-family: 'Bootstrap Icons';
      position: absolute;
      right: -20px;
      top: -20px;
      font-size: 150px;
      opacity: 0.05;
    }

    .highlight-text {
      color: #38bdf8;
      font-weight: 700;
    }

    /* KPI Cards */
    .kpi-box {
      padding: 24px;
      border-radius: var(--radius);
      color: white;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      min-height: 160px;
      transition: transform 0.2s ease;
    }

    .kpi-box:hover {
      transform: translateY(-5px);
    }

    .kpi-icon {
      position: absolute;
      right: -10px;
      bottom: -10px;
      font-size: 100px;
      opacity: 0.15;
      line-height: 1;
    }

    .kpi-title {
      font-size: 15px;
      font-weight: 600;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .kpi-value {
      font-size: 42px;
      font-weight: 800;
      line-height: 1.1;
      margin-top: 10px;
    }

    .kpi-trend {
      font-size: 14px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: rgba(255,255,255,0.2);
      padding: 4px 10px;
      border-radius: 30px;
      margin-top: auto;
      backdrop-filter: blur(4px);
    }

    .bg-grad-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .bg-grad-success { background: linear-gradient(135deg, #10b981, #047857); }
    .bg-grad-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .bg-grad-danger { background: linear-gradient(135deg, #ef4444, #b91c1c); }
    .bg-grad-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

    /* Performance List */
    .perf-item {
      display: flex;
      align-items: center;
      padding: 16px;
      border-radius: 12px;
      background: #f8fafc;
      margin-bottom: 12px;
      transition: background 0.2s;
    }
    
    .perf-item:hover {
      background: #f1f5f9;
    }

    .perf-rank {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      margin-right: 15px;
      font-size: 18px;
    }

    .rank-1 { background: #fef08a; color: #a16207; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #fed7aa; color: #9a3412; }
    
    .rank-bad-1 { background: #fee2e2; color: #991b1b; }
    
    .status-badge {
      padding: 6px 12px;
      border-radius: 30px;
      font-size: 12px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .status-good { background: #dcfce7; color: #166534; }
    .status-alert { background: #fef3c7; color: #92400e; }
    .status-danger { background: #fee2e2; color: #991b1b; }

    .section-title {
      font-weight: 800;
      font-size: 20px;
      color: var(--text-main);
      margin-bottom: 5px;
    }
    
    .section-subtitle {
      color: var(--text-muted);
      font-size: 14px;
      margin-bottom: 20px;
    }

    /* Minimal Select */
    .filter-select {
      background-color: white;
      border: 1px solid #e2e8f0;
      border-radius: 30px;
      padding: 10px 20px;
      font-weight: 600;
      color: #334155;
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
      cursor: pointer;
    }

    /* Print Styles */
    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      
      body {
        background: #ffffff !important;
      }

      /* Hide controls during printing */
      .print-hide {
        display: none !important;
      }

      /* Ensure cards don't break across pages if possible */
      .card-info, .kpi-box, .insight-hero {
        break-inside: avoid;
        page-break-inside: avoid;
      }

      /* Reduce shadows for print */
      .card-info, .insight-hero, .kpi-box {
        box-shadow: none !important;
        border: 1px solid #e2e8f0;
      }

      /* Force background gradients to render */
      .bg-grad-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; }
      .bg-grad-success { background: linear-gradient(135deg, #10b981, #047857) !important; }
      .bg-grad-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
      .bg-grad-danger { background: linear-gradient(135deg, #ef4444, #b91c1c) !important; }
      .bg-grad-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9) !important; }
      .insight-hero { background: linear-gradient(135deg, #0f4c81, #1e3a8a) !important; }
    }

    /* Potensi Yudisium Styles */
    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .student-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .student-item:last-child {
        border-bottom: none;
    }
  </style>
</head>
<body>

<div class="container-fluid py-4 px-lg-5">
  <?php
    $total_lulusan = ($kpi && isset($kpi->total_lulusan)) ? $kpi->total_lulusan : 0;
    $persen_tepat_waktu = $total_lulusan > 0 ? round(($kpi->lulus_tepat_waktu / $total_lulusan) * 100) : 0;
    $persen_dengan_pujian = $total_lulusan > 0 ? round(($kpi->total_cumlaude / $total_lulusan) * 100) : 0;
    $rata_masa_studi = ($kpi && $kpi->rata_masa_studi) ? number_format($kpi->rata_masa_studi, 1) : 0;

        if (!function_exists('abbreviate_prodi')) {
        function abbreviate_prodi($string) {
            $words = explode(' ', $string);
            $acronym = '';
            foreach ($words as $w) {
                $w = trim($w);
                if (empty($w)) continue;
                if (strtolower($w) != 'dan' && strtolower($w) != 'studi' && strtolower($w) != 'program') {
                    $acronym .= strtoupper($w[0]);
                }
            }
            return $acronym;
        }
    }

        $nama_prodi_terpilih = "Semua Program Studi";
    if (!empty($selected_prodi)) {
        foreach ($list_prodi as $prodi) {
            if ($prodi->id_sms == $selected_prodi) {
                $nama_prodi_terpilih = $prodi->nm_lemb;
                break;
            }
        }
    }
  ?>

  
  <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 gap-3">
    <div>
      <?php 
        $title_display = "Profil Lulusan WBI";
        if (!empty($selected_prodi)) {
            $title_display = "Profil Lulusan " . abbreviate_prodi($nama_prodi_terpilih);
        }
      ?>
      <h1 class="dashboard-title mb-1"><i class="bi bi-mortarboard-fill text-primary me-2"></i><?= $title_display ?></h1>
      <div class="text-muted fw-medium">
        Infografis Kinerja Akademik Lulusan
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2 print-hide">
      <form action="<?= site_url('C_infografis_lulusan/generate') ?>" method="get" id="filterForm" class="d-flex gap-2 m-0">
        <select class="form-select filter-select w-auto" name="id_sms" onchange="this.form.submit()">
          <option value="">Semua Program Studi</option>
          <?php foreach ($list_prodi as $prodi): ?>
            <option value="<?= $prodi->id_sms ?>" <?= ($selected_prodi == $prodi->id_sms) ? 'selected' : '' ?>>
              <?= $prodi->nm_lemb ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
      <button class="btn btn-dark rounded-pill px-4 fw-bold" onclick="window.open('<?= site_url('C_infografis_lulusan/cetak_laporan') ?>', '_blank')">
        <i class="bi bi-printer me-2"></i>Cetak Laporan
      </button>
    </div>
  </div>

  <?php
      ?>

  
  <div class="row mb-4">
    <div class="col-12">
      <div class="insight-hero shadow-sm">
        <div class="d-flex align-items-center mb-3">
          <i class="bi bi-bar-chart-line-fill text-warning fs-4 me-2"></i>
          <h5 class="mb-0 fw-bold text-white">Executive Insight</h5>
        </div>
        <div class="row align-items-center">
          <?php
                        if ($persen_tepat_waktu >= 50) {
                $status_kinerja = "Sangat Baik (Skor Maksimal BAN-PT)";
                $highlight_color = "#38bdf8";                 $badge_class = "status-good";
                $badge_icon = "bi-check-circle-fill";
                $badge_text = "UNGGUL";
            } elseif ($persen_tepat_waktu >= 40) {
                $status_kinerja = "Baik (Memenuhi Standar Minimum)";
                $highlight_color = "#fcd34d";                 $badge_class = "bg-warning bg-opacity-25 text-warning";
                $badge_icon = "bi-check-circle";
                $badge_text = "MEMADAI";
            } else {
                $status_kinerja = "Perlu Peningkatan";
                $highlight_color = "#fca5a5";                 $badge_class = "bg-danger bg-opacity-25 text-danger";
                $badge_icon = "bi-exclamation-triangle-fill";
                $badge_text = "DI BAWAH TARGET";
            }
          ?>
          <div class="col-lg-8 fs-5 fw-medium" style="line-height: 1.6;">
            Kinerja kelulusan institusi dalam kondisi <span class="fw-bold" style="color: <?= $highlight_color ?>;"><?= $status_kinerja ?></span>. 
            Total lulusan saat ini adalah <span class="highlight-text"><?= $total_lulusan ?> mahasiswa</span>, dengan <span class="highlight-text"><?= $persen_tepat_waktu ?>% mahasiswa lulus tepat waktu</span>.
            
            <?php if(isset($mahasiswa_berisiko) && $mahasiswa_berisiko > 0): ?>
            <div class="mt-3 p-3 bg-danger bg-opacity-25 border border-danger border-opacity-50 rounded-3 fs-6 d-flex align-items-start">
              <i class="bi bi-exclamation-triangle-fill text-danger me-3 fs-4 mt-1"></i>
              <div>
                <strong>Perhatian Khusus:</strong> Terdapat <strong><?= $mahasiswa_berisiko ?> mahasiswa aktif</strong> yang berisiko lulus tidak tepat waktu (masa studi telah melebihi 4 tahun namun belum dinyatakan lulus).
              </div>
            </div>
            <?php endif; ?>
          </div>
          <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-block bg-white bg-opacity-10 rounded-4 p-3 text-center">
              <div class="fs-6 text-white-50 mb-1">Status Kinerja Keseluruhan</div>
              <div class="status-badge <?= $badge_class ?> fs-5 px-4 py-2">
                <i class="bi <?= $badge_icon ?>"></i> <?= $badge_text ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="kpi-box bg-grad-primary shadow-sm">
        <i class="bi bi-people-fill kpi-icon"></i>
        <div class="kpi-title">Total Lulusan</div>
        <div class="kpi-value"><?= $total_lulusan ?> <span class="fs-4 fw-normal">org</span></div>
        <div class="mt-2" id="spark1"></div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-box bg-grad-success shadow-sm">
        <i class="bi bi-stopwatch kpi-icon"></i>
        <div class="kpi-title">Tren Ketepatan Waktu</div>
        <div class="kpi-value"><?= $persen_tepat_waktu ?><span class="fs-4">%</span></div>
        <div class="mt-2" id="spark_tepat_waktu"></div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-box bg-grad-purple shadow-sm">
        <i class="bi bi-award-fill kpi-icon"></i>
        <div class="kpi-title">Rasio Dengan Pujian</div>
        <div class="kpi-value"><?= $persen_dengan_pujian ?><span class="fs-4">%</span></div>
        <div class="mt-2">
          <div class="progress" style="height: 6px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" style="width: <?= $persen_dengan_pujian ?>%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="kpi-box bg-grad-warning shadow-sm">
        <i class="bi bi-hourglass-split kpi-icon"></i>
        <div class="kpi-title">Rata-rata Masa Studi</div>
        <div class="kpi-value"><?= $rata_masa_studi ?> <span class="fs-4 fw-normal">Thn</span></div>
        <div class="mt-2" id="spark2"></div>
        <?php if ($kpi && $kpi->rata_masa_studi <= 4.5): ?>
          <div class="kpi-trend mt-2"><i class="bi bi-shield-check"></i> Aman (Target ≤ 4.5 Thn)</div>
        <?php else: ?>
          <div class="kpi-trend mt-2 bg-danger bg-opacity-25 text-white border border-danger"><i class="bi bi-exclamation-triangle"></i> Melebihi Target (≤ 4.5 Thn)</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  
  <div class="row mb-4">
    <div class="col-12">
      <div class="card-info p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="section-title">Potensi Mahasiswa Yudisium</h4>
            <div class="section-subtitle">Mahasiswa yang telah lulus Sidang namun belum masuk data kelulusan</div>
          </div>
          <div class="badge bg-primary rounded-pill px-3 py-2">
            <?= count($potensi_yudisium) ?> Mahasiswa
          </div>
        </div>
        
        <div class="row g-4">
          <div class="col-12">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-hover align-middle mb-0">
                <thead class="sticky-top bg-white">
                  <tr class="text-muted small text-uppercase fw-bold border-bottom">
                    <th style="width: 50px;">Mhs</th>
                    <th>NIM & Nama Mahasiswa</th>
                    <th>Program Studi</th>
                    <th class="text-center">Nilai Sidang</th>
                    <th class="text-center">Tgl Sidang</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($potensi_yudisium)): ?>
                    <?php foreach($potensi_yudisium as $mhs): ?>
                      <tr>
                        <td>
                          <?php 
                            $fallback = "https://ui-avatars.com/api/?name=".urlencode($mhs->nm_pd)."&background=random";
                            $foto = !empty($mhs->foto) ? "https://wbi.siakadcloud.com/file/lihat/".$mhs->foto : $fallback;
                          ?>
                          <img src="<?= $foto ?>" class="avatar-circle" alt="Avatar" onerror="this.onerror=null;this.src='<?= $fallback ?>';">
                        </td>
                        <td>
                          <div class="fw-bold text-dark"><?= $mhs->nm_pd ?></div>
                          <div class="small text-muted"><?= $mhs->nim ?></div>
                        </td>
                        <td>
                          <div class="small fw-medium"><?= $mhs->prodi ?></div>
                        </td>
                        <td class="text-center">
                          <div class="fw-bold text-primary fs-5"><?= $mhs->sidang_nilai_akhir ?></div>
                        </td>
                        <td class="text-center">
                          <div class="small"><?= !empty($mhs->sidang_tanggal) ? date('d/m/Y', strtotime($mhs->sidang_tanggal)) : '-' ?></div>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">
                            Lulus Sidang
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-person-check fs-1 opacity-25 d-block mb-2"></i>
                        Tidak ada mahasiswa yang menunggu yudisium
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div class="row g-4 mb-4">
    
    
    <div class="col-xl-4">
      <div class="card-info p-4 h-100">
        <h4 class="section-title">Kontribusi Lulusan</h4>
        <div class="section-subtitle">Distribusi jumlah lulusan per program studi</div>
        <div id="donutChart" class="mt-4"></div>
      </div>
    </div>

    
    <div class="col-xl-4">
      <div class="card-info p-4 h-100">
        <h4 class="section-title">Kualitas Akademik</h4>
        <div class="section-subtitle">Sebaran predikat IPK kelulusan</div>
        <div id="ipkChart" class="mt-4"></div>
      </div>
    </div>

    
    <div class="col-xl-4">
      <div class="card-info p-4 h-100 d-flex flex-column">
        <?php if(empty($selected_prodi)): ?>
          <h4 class="section-title">Sorotan Prodi</h4>
          <div class="section-subtitle">Ketepatan waktu penyelesaian studi</div>

          <div class="flex-grow-1" style="max-height: 350px; overflow-y: auto;">
            <?php if(!empty($sorotan)): ?>
              <?php 
                usort($sorotan, function($a, $b) {
                    $pct_a = $a->total > 0 ? ($a->tepat_waktu / $a->total) : 0;
                    $pct_b = $b->total > 0 ? ($b->tepat_waktu / $b->total) : 0;
                    return $pct_b <=> $pct_a;                 });
                $rank = 1;
              ?>
              <?php foreach($sorotan as $item): ?>
                <?php 
                  $pct = $item->total > 0 ? round(($item->tepat_waktu / $item->total) * 100) : 0;
                  $is_good = $pct >= 75;
                ?>
                <div class="perf-item <?= !$is_good ? 'border border-danger border-opacity-25' : '' ?>">
                  <div class="perf-rank <?= $is_good ? ($rank <= 3 ? 'rank-'.$rank : 'bg-light text-dark') : 'rank-bad-1' ?>"><?= $rank ?></div>
                  <div class="flex-grow-1">
                    <div class="fw-bold"><?= $item->nm_lemb ?></div>
                    <div class="small text-muted"><?= $pct ?>% Tepat Waktu</div>
                  </div>
                  <?php if($is_good): ?>
                    <div class="status-badge status-good">Bagus</div>
                  <?php else: ?>
                    <div class="status-badge status-danger">Perlu Perhatian</div>
                  <?php endif; ?>
                </div>
                <?php $rank++; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-muted text-center py-4">Data belum tersedia</div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <h4 class="section-title">Detail Kinerja</h4>
          <div class="section-subtitle">Analisis prodi <?= abbreviate_prodi($nama_prodi_terpilih) ?></div>
          <div class="flex-grow-1 d-flex flex-column justify-content-center">
            <div class="p-3 bg-light rounded-3 mb-3 border border-light" style="line-height: 1.6; font-size: 15px;">
              Program studi <strong><?= $nama_prodi_terpilih ?></strong> telah berkontribusi mencetak <strong><?= $total_lulusan ?> lulusan</strong>.
              Rata-rata masa studi mahasiswa berada di angka <strong><?= $rata_masa_studi ?> tahun</strong>. 
              Dari segi kualitas akademik, sebanyak <strong><?= $persen_dengan_pujian ?>%</strong> mahasiswa berhasil menyandang predikat <strong>Dengan Pujian</strong>.
              <br><br>
              Tingkat kelulusan tepat waktu mencapai <strong><?= $persen_tepat_waktu ?>%</strong>, yang menandakan performa ketepatan kelulusan di program studi ini 
              <?php if($persen_tepat_waktu >= 50): ?>
                 berjalan <strong class="text-success">sangat memuaskan</strong> dan setara dengan skor unggul BAN-PT.
              <?php elseif($persen_tepat_waktu >= 40): ?>
                 berjalan <strong class="text-primary">memadai</strong> dan sesuai target batas minimal.
              <?php else: ?>
                 <strong class="text-danger">memerlukan perbaikan dan evaluasi lebih lanjut</strong>.
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    // Common ApexCharts Options
    const commonFont = "'Plus Jakarta Sans', sans-serif";

    // Sparkline 1 (Trend Lulusan di KPI Box)
    var spark1Options = {
      chart: { type: 'bar', width: '100%', height: 100, toolbar: { show: false } },
      plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
      series: [{ name: 'Lulusan', data: <?= json_encode($trend->lulusan ?? []) ?> }],
      colors: ['#ffffff'],
      dataLabels: { enabled: false },
      xaxis: { 
        categories: <?= json_encode($trend->categories ?? []) ?>, 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } },
        axisBorder: { show: false }, 
        axisTicks: { show: false } 
      },
      yaxis: { 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } } 
      },
      grid: { borderColor: 'rgba(255,255,255,0.1)' },
      tooltip: { theme: 'dark' }
    }
    new ApexCharts(document.querySelector("#spark1"), spark1Options).render();

    // Sparkline Tepat Waktu (Komparatif)
    var sparkTepatWaktuOptions = {
      chart: { type: 'bar', width: '100%', height: 100, toolbar: { show: false }, stacked: true },
      plotOptions: { bar: { borderRadius: 2, columnWidth: '60%' } },
      series: [
        { name: 'Tepat Waktu', data: <?= json_encode($trend->tepat_waktu ?? []) ?> },
        { name: 'Tidak Tepat', data: <?= json_encode($trend->tidak_tepat_waktu ?? []) ?> }
      ],
      colors: ['#ffffff', '#fca5a5'],
      dataLabels: { enabled: false },
      xaxis: { 
        categories: <?= json_encode($trend->categories ?? []) ?>, 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } },
        axisBorder: { show: false }, 
        axisTicks: { show: false } 
      },
      yaxis: { 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } } 
      },
      grid: { borderColor: 'rgba(255,255,255,0.1)' },
      legend: { show: false },
      tooltip: { theme: 'dark' }
    }
    new ApexCharts(document.querySelector("#spark_tepat_waktu"), sparkTepatWaktuOptions).render();

    // Sparkline 2 (Masa Studi di KPI Box)
    var spark2Options = {
      chart: { type: 'bar', width: '100%', height: 100, toolbar: { show: false } },
      plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
      series: [{ name: 'Masa Studi (Thn)', data: <?= json_encode($trend->masa_studi ?? []) ?> }],
      colors: ['#ffffff'],
      dataLabels: { enabled: false },
      xaxis: { 
        categories: <?= json_encode($trend->categories ?? []) ?>, 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } },
        axisBorder: { show: false }, 
        axisTicks: { show: false } 
      },
      yaxis: { 
        labels: { style: { colors: 'rgba(255,255,255,0.7)', fontSize: '10px' } } 
      },
      grid: { borderColor: 'rgba(255,255,255,0.1)' },
      tooltip: { theme: 'dark' }
    }
    new ApexCharts(document.querySelector("#spark2"), spark2Options).render();

    <?php
      $donut_labels = [];
      $donut_series = [];
      $mayoritas_nama = 'N/A';
      $mayoritas_jumlah = 0;
      if (!empty($demografi)) {
          foreach ($demografi as $d) {
              $singkatan = abbreviate_prodi($d->nm_lemb);
              $donut_labels[] = $singkatan;
              $donut_series[] = (int)$d->jumlah;
              if ((int)$d->jumlah > $mayoritas_jumlah) {
                  $mayoritas_jumlah = (int)$d->jumlah;
                  $mayoritas_nama = $singkatan;
              }
          }
      }

      $ipk_dengan_pujian = isset($ipk->cumlaude) ? (int)$ipk->cumlaude : 0;
      $ipk_sangat_memuaskan = isset($ipk->sangat_memuaskan) ? (int)$ipk->sangat_memuaskan : 0;
      $ipk_memuaskan = isset($ipk->memuaskan) ? (int)$ipk->memuaskan : 0;
      $ipk_kurang = isset($ipk->kurang) ? (int)$ipk->kurang : 0;

      $ipk_total = $ipk_dengan_pujian + $ipk_sangat_memuaskan + $ipk_memuaskan + $ipk_kurang;
      $pct_dengan_pujian = $ipk_total > 0 ? round(($ipk_dengan_pujian / $ipk_total) * 100) : 0;
      $pct_sangat_memuaskan = $ipk_total > 0 ? round(($ipk_sangat_memuaskan / $ipk_total) * 100) : 0;
      $pct_memuaskan = $ipk_total > 0 ? round((($ipk_memuaskan + $ipk_kurang) / $ipk_total) * 100) : 0;
    ?>

    // Donut Chart - Distribusi Prodi
    var donutOptions = {
      chart: { type: 'donut', height: 280, fontFamily: commonFont },
      labels: <?= json_encode($donut_labels) ?>,
      series: <?= json_encode($donut_series) ?>,
      colors: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#14b8a6', '#f97316'],
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: { fontSize: '14px', color: '#64748b' },
              value: { fontSize: '24px', fontWeight: 700, color: '#0f172a' },
              total: { show: true, label: 'Mayoritas', color: '#64748b', formatter: function (w) { return "<?= $mayoritas_nama ?>" } }
            }
          }
        }
      },
      dataLabels: { enabled: false },
      legend: { position: 'bottom', fontSize: '13px', markers: { radius: 12 } },
      stroke: { show: false }
    };
    new ApexCharts(document.querySelector('#donutChart'), donutOptions).render();

    // Radial Bar - Kualitas Akademik (IPK)
    var ipkOptions = {
      chart: { type: 'radialBar', height: 320, fontFamily: commonFont },
      series: [<?= $pct_dengan_pujian ?>, <?= $pct_sangat_memuaskan ?>, <?= $pct_memuaskan ?>],
      labels: ['Dengan Pujian', 'Sgt Memuaskan', 'Memuaskan'],
      colors: ['#2563eb', '#10b981', '#f59e0b'],
      plotOptions: {
        radialBar: {
          hollow: { size: '40%' },
          track: { background: '#f1f5f9', margin: 10 },
          dataLabels: {
            name: { fontSize: '14px', color: '#64748b' },
            value: { fontSize: '20px', fontWeight: 700, color: '#0f172a' },
            total: { show: true, label: 'Dengan Pujian', fontSize: '14px', fontWeight: 600, formatter: function (w) { return "<?= $pct_dengan_pujian ?>%" } }
          }
        }
      },
      legend: { show: true, position: 'bottom', fontSize: '13px', markers: { radius: 12 } },
      stroke: { lineCap: 'round' }
    };
    new ApexCharts(document.querySelector('#ipkChart'), ipkOptions).render();

  });
</script>


<script>
  function sendHeight() {
    const height = document.body.scrollHeight;
    window.parent.postMessage({ type: 'setHeight', height: height }, '*');
  }
  
  window.addEventListener('load', sendHeight);
  
  // Also resend on any dynamic changes (like Alpine or Charts)
  const resizeObserver = new ResizeObserver(entries => {
    sendHeight();
  });
  resizeObserver.observe(document.body);
</script>

</body>
</html>
