<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kerjasama WBI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            padding: 20px 0;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #edf2f9;
            padding: 15px 20px;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            font-weight: 600;
        }

        .stat-box {
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-box .inner {
            position: relative;
            z-index: 2;
        }

        .stat-box h3 {
            font-size: 38px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .stat-box p {
            font-size: 14px;
            font-weight: 500;
            margin: 0;
            line-height: 1.2;
        }

        .stat-box .icon {
            position: absolute;
            top: -10px;
            right: 10px;
            font-size: 80px;
            color: rgba(0,0,0,0.15);
            z-index: 1;
        }

        .bg-primary-custom { background-color: #4e73df; }
        .bg-success-custom { background-color: #1cc88a; }
        .bg-info-custom { background-color: #36b9cc; }
        .bg-warning-custom { background-color: #f6c23e; }
        .bg-danger-custom { background-color: #e74a3b; }

        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        .table th {
            text-transform: uppercase;
            font-size: 13px;
            color: #6e707e;
            background-color: #f8f9fc;
        }
        
        .wbi-header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="container-fluid px-4">
        
        <!-- HEADER & FILTER -->
        <div class="wbi-header">
            <div>
                <h3 class="m-0 text-dark fw-bold" style="color: #4e73df !important;">Dashboard Kerjasama WBI</h3>
                <p class="text-muted m-0">Menampilkan <strong>seluruh data</strong> historis kerjasama institusi.</p>
            </div>
            <div class="d-flex align-items-center">
                <label for="tahunFilter" class="me-2 fw-bold text-secondary">Tahun:</label>
                <select id="tahunFilter" class="form-select form-select-lg" style="width: auto; min-width: 120px;" onchange="changeYear(this.value)">
                    <option value="All" <?= $selected_year == 'All' ? 'selected' : '' ?>>Keseluruhan</option>
                    <?php if (!empty($years)): ?>
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="row">
            <div class="col-xl col-md-6 mb-4">
                <div class="stat-box bg-primary-custom">
                    <div class="inner">
                        <h3><?= $total_mou; ?></h3>
                        <p>MoU Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-signature"></i></div>
                </div>
            </div>
            <div class="col-xl col-md-6 mb-4">
                <div class="stat-box bg-success-custom">
                    <div class="inner">
                        <h3><?= $total_moa; ?></h3>
                        <p>MoA Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-contract"></i></div>
                </div>
            </div>
            <div class="col-xl col-md-4 mb-4">
                <div class="stat-box bg-info-custom" style="cursor: pointer; transition: transform 0.2s;" onclick="window.open('<?= site_url('C_infografis_kerjasama/cetak_mitra') ?>?tahun=' + selectedYear, '_blank')" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="inner">
                        <h3><?= $total_instansi; ?></h3>
                        <p>Mitra / Instansi Terlibat <i class="fas fa-external-link-alt" style="font-size: 10px; margin-left: 5px;"></i></p>
                    </div>
                    <div class="icon"><i class="fas fa-building"></i></div>
                </div>
            </div>
            <!-- Mitra Baru Card -->
            <div class="col-xl col-md-4 mb-4">
                <div class="stat-box bg-danger-custom">
                    <div class="inner">
                        <h3><?= $total_mitra_baru; ?></h3>
                        <p>Mitra Baru (Tahun <?= $selected_year == 'All' ? date('Y') : $selected_year ?>)</p>
                    </div>
                    <div class="icon"><i class="fas fa-handshake"></i></div>
                </div>
            </div>
            <div class="col-xl col-md-4 mb-4">
                <div class="stat-box bg-warning-custom">
                    <div class="inner">
                        <h3><?= $total_kegiatan; ?></h3>
                        <p>Realisasi Kegiatan</p>
                    </div>
                    <div class="icon"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- CHART BULANAN / TAHUNAN -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">
                            <?php if ($selected_year != 'All'): ?>
                                Pertumbuhan Kesepakatan MoU & MoA (Tahun <?= $selected_year ?>)
                            <?php else: ?>
                                Tren Kesepakatan MoU & MoA per Tahun
                            <?php endif; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JENIS INSTANSI PIE CHART -->
            <div class="col-xl-3 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">Proporsi Jenis Instansi</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="jenisInstansiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BIDANG USAHA PIE CHART -->
            <div class="col-xl-3 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">Proporsi Bidang Usaha</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="bidangUsahaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP INSTANSI TABLE -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Top 10 Instansi / Mitra Aktif Berdasarkan Kegiatan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4" width="5%">#</th>
                                        <th>Nama Instansi / Mitra</th>
                                        <th class="text-center" width="20%">Jumlah Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($top_instansi)): ?>
                                        <?php $no = 1; foreach ($top_instansi as $instansi): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-secondary"><?= $no++; ?></td>
                                            <td>
                                                <i class="fas fa-building text-primary me-2"></i>
                                                <span class="fw-semibold"><?= $instansi->nm_instansi; ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6"><?= $instansi->total_kegiatan; ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data kegiatan untuk pilihan ini.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const baseUrl = '<?= site_url("C_infografis_kerjasama") ?>';
        const selectedYear = '<?= $selected_year ?>';

        function changeYear(year) {
            window.location.href = baseUrl + '/generate?tahun=' + year;
        }

        function resizeIframe() {
            if (window.parent) {
                window.parent.postMessage({ type: 'setHeight', height: document.body.scrollHeight + 50 }, '*');
            }
        }
        window.addEventListener('load', resizeIframe);
        window.addEventListener('resize', resizeIframe);

        Chart.defaults.font.family = "'Segoe UI', Roboto, sans-serif";

        const pieColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'];

        <?php if ($selected_year != 'All'): ?>
        // Load Monthly Trend Chart
        fetch(`${baseUrl}/get_chart_mou_moa_monthly?tahun=${selectedYear}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('trendChart'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'MoU Baru/Aktif',
                                data: data.mou,
                                backgroundColor: '#4e73df',
                                borderRadius: 4
                            },
                            {
                                label: 'MoA Baru/Aktif',
                                data: data.moa,
                                backgroundColor: '#1cc88a',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { precision: 0 } 
                            }
                        },
                        plugins: {
                            tooltip: { mode: 'index', intersect: false }
                        }
                    }
                });
                setTimeout(resizeIframe, 500);
            });
        <?php else: ?>
        // Load Yearly Trend Chart
        fetch(`${baseUrl}/get_chart_mou_moa_yearly`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('trendChart'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'MoU Baru/Aktif',
                                data: data.mou,
                                backgroundColor: '#4e73df',
                                borderRadius: 4
                            },
                            {
                                label: 'MoA Baru/Aktif',
                                data: data.moa,
                                backgroundColor: '#1cc88a',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { precision: 0 } 
                            }
                        },
                        plugins: {
                            tooltip: { mode: 'index', intersect: false }
                        }
                    }
                });
                setTimeout(resizeIframe, 500);
            });
        <?php endif; ?>

        // Load Jenis Instansi (Pie)
        fetch(`${baseUrl}/get_chart_jenis_instansi?tahun=${selectedYear}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('jenisInstansiChart'), {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: pieColors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        }
                    }
                });
            });

        // Load Bidang Usaha (Doughnut)
        fetch(`${baseUrl}/get_chart_bidang_usaha?tahun=${selectedYear}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('bidangUsahaChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: pieColors.slice().reverse()
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        }
                    }
                });
            });
    </script>
</body>
</html>
