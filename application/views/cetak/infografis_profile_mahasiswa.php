<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard WBI 2025 - Full Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --wbi-green-dark: #1a3c34;
            --wbi-green-mid: #557169;
            --wbi-green-light: #dae5e2;
            --wbi-gold: #b58931;
            --bg-body: #f0f4f3;
        }

        body {
            background: var(--bg-body);
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 40px 0;
            color: var(--wbi-green-dark);
        }

        .card-custom {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 30px;
            padding: 35px;
            overflow: hidden;
            position: relative;
        }

        /* CARD 1: PROFIL */
        .wbi-logo-top {
            position: absolute;
            top: 30px;
            right: 35px;
            width: 70px;
            height: auto;
            z-index: 2;
        }

        .card-title-lg {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.2;
            color: var(--wbi-green-dark);
        }

        .card-wbi-main:before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: rgba(47, 128, 237, 0.06);
            border-radius: 50%;
        }

        /* BARS MINI */
        .chart-box-mini {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            height: 80px;
            gap: 8px;
        }

        .bar {
            width: 15px;
            border-radius: 4px;
            background: linear-gradient(180deg, #8da9a2, var(--wbi-green-dark));
        }

        /* CARD 2: RINGKASAN */
        .stat-item {
            background: var(--wbi-green-light);
            border-radius: 16px;
            padding: 22px;
            text-align: center;
            transition: 0.3s;
            height: 100%;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: #cedbd7;
        }

        .icon-box {
            font-size: 28px;
            margin-bottom: 10px;
            color: var(--wbi-green-dark);
        }

        /* CARD 3 & 4: CHARTS */
        .chart-container {
            position: relative;
            height: 450px;
            width: 100%;
        }

        /* LIST PRODI */
        .prodi-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .prodi-item:last-child {
            border-bottom: none;
        }

        .prodi-icon {
            width: 40px;
            height: 40px;
            background: var(--wbi-green-light);
            color: var(--wbi-green-dark);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .prodi-info {
            flex-grow: 1;
        }

        .prodi-name {
            font-weight: 600;
            font-size: 14px;
            margin: 0;
        }

        .prodi-count {
            font-weight: 700;
            color: var(--wbi-green-dark);
        }

        .btn-wbi {
            background: var(--wbi-green-dark);
            color: white !important;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }

        /* SIDEBAR KIRI (TOTAL) */
        .total-box {
            background: var(--wbi-green-light);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .total-box i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .total-value {
            font-size: 24px;
            font-weight: 800;
            display: block;
        }

        .total-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #557169;
        }

        /* SISI KANAN (PRODI DETAIL) */
        .prodi-gender-card {
            background: #fbfcfc;
            /* border-radius: 12px; */
            padding: 15px 20px;
            margin-bottom: 12px;
            border-left: 5px solid var(--wbi-green-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }

        .prodi-gender-card:hover {
            background: #f0f4f3;
            transform: translateX(5px);
        }

        .prodi-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .gender-detail-text {
            font-size: 12px;
            color: #6b827b;
        }

        .gender-detail-text i {
            margin-right: 5px;
        }

        .prodi-total-count {
            background: var(--wbi-green-dark);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
        }

        /* TABEL PROSES CUSTOM CSS */
        .custom-step-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            text-align: center;
        }

        .custom-step-table th {
            color: white;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 16px;
        }

        .custom-step-table th:nth-child(1) { background-color: #7A7A7A; text-align: left; padding-left: 20px; }
        .custom-step-table th:nth-child(2) { background-color: #008080; }
        .custom-step-table th:nth-child(3) { background-color: #A4B72F; }
        .custom-step-table th:nth-child(4) { background-color: #FF9900; }
        .custom-step-table th:nth-child(5) { background-color: #FF8000; }
        .custom-step-table th:nth-child(6) { background-color: #1976D2; }

        .custom-step-table td {
            padding: 12px;
            background-color: #F2F2F2;
            vertical-align: middle;
        }

        .custom-step-table tr:nth-child(even) td {
            background-color: #FFFFFF;
        }

        .custom-step-table td.desc-col {
            text-align: left;
            padding-left: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #555;
            font-size: 15px;
        }

        .diamond {
            width: 14px;
            height: 14px;
            background-color: #C4C4C4;
            transform: rotate(45deg);
            display: inline-block;
        }

        .custom-step-table td:nth-child(2) .diamond.active { background-color: #008080; }
        .custom-step-table td:nth-child(3) .diamond.active { background-color: #A4B72F; }
        .custom-step-table td:nth-child(4) .diamond.active { background-color: #FF9900; }
        .custom-step-table td:nth-child(5) .diamond.active { background-color: #FF8000; }
        .custom-step-table td:nth-child(6) .diamond.active { background-color: #1976D2; }
    </style>
</head>

<body>

    <div class="container">

        <div class="card card-custom card-wbi-main">
            <img src="<?= base_url('assets/favicon.png'); ?>" alt="Logo WBI" class="wbi-logo-top">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="card-title-lg">Profil Mahasiswa<br>WBI 2025</h1>
                    <p class="text-muted">Ringkasan Data Mahasiswa dan Laporan Tahun 2025</p>
                </div>
                <div class="col-md-5 text-center">
                    <div class="icon-blue mb-2" style="font-size: 50px;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="chart-box-mini">
                        <div style="height: 20%" class="bar"></div>
                        <div style="height: 40%" class="bar"></div>
                        <div style="height: 60%" class="bar"></div>
                        <div style="height: 85%" class="bar"></div>
                        <div style="height: 100%" class="bar"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom card-summary">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Ringkasan Eksekutif</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        <i class="fas fa-users mb-2" style="font-size: 24px;"></i>
                        <span class="d-block fw-bold" style="font-size: 24px;"><?= $mhs_aktif; ?></span>
                        <span class="small text-muted">Mahasiswa Aktif</span>
                    </div>

                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        <i class="fas fa-chart-line mb-2" style="font-size: 24px;"></i>
                        <span class="d-block fw-bold" style="font-size: 24px;"><?= $mhs_baru; ?></span>
                        <span class="small text-muted">Mahasiswa Baru</span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        <i class="fas fa-user-graduate mb-2" style="font-size: 24px;"></i>
                        <span class="d-block fw-bold" style="font-size: 24px;"><?= $mhs_kip; ?></span>
                        <span class="small text-muted">Penerima KIP</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        TA 2024
                        <div class="row">
                            <div class="col-md-6">
                                <span class="d-block fw-bold" style="font-size: 24px;"><?= $rerata_ipk_ganjil; ?></span>
                                <span class="small text-muted">Rerata IPK Ganjil</span>
                            </div>
                            <div class="col-md-6">
                                <span class="d-block fw-bold" style="font-size: 24px;"><?= $rerata_ipk_genap; ?></span>
                                <span class="small text-muted">Rerata IPK Genap</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        <i class="fas  fa-laptop-code mb-2" style="font-size: 24px;"></i>
                        <span class="d-block fw-bold" style="font-size: 24px;"><?= round($ipk_lulus, 2); ?></span>
                        <span class="small text-muted">Rerata IPK Kelulusan TA 2024</span>
                        <span class="d-block fw-bold" style="font-size: 14px;">IPK Max : <?= round($ipk_max, 2); ?>, IPK Min : <?= round($ipk_min, 2); ?></span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item">
                        <i class="fas fa-medal mb-2" style="font-size: 24px;"></i>
                        <span class="d-block fw-bold" style="font-size: 24px;"><?= $jumlah_lulusan; ?></span>
                        <span class="small text-muted">Jumlah Lulusan TA 2024</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom card-trend">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Mahasiswa di tahun Akademik Berjalan</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="trendChartAktif"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Detail Mhs Terdaftar VS Mhs Aktif (Per Prodi)</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" style="font-size: 14px;">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" class="align-middle">Angkatan</th>
                            <th colspan="2" style="background-color: #00897b;">PSMPI</th>
                            <th colspan="2" style="background-color: #8bc34a; color: #000;">PSAP</th>
                            <th colspan="2" style="background-color: #7b1fa2;">PSAH</th>
                            <th colspan="2" style="background-color: #00838f;">PSPKA</th>
                            <th colspan="2" style="background-color: #1976d2;">PSTRPL</th>
                            <th colspan="2" style="background-color: #424242; color: white;">TOTAL WBI</th>
                        </tr>
                        <tr>
                            <th style="background-color: #00796b; color: white;">Terdaftar</th>
                            <th style="background-color: #00695c; color: white;">Aktif</th>
                            <th style="background-color: #7cb342; color: #000;">Terdaftar</th>
                            <th style="background-color: #689f38; color: #000;">Aktif</th>
                            <th style="background-color: #6a1b9a; color: white;">Terdaftar</th>
                            <th style="background-color: #4a148c; color: white;">Aktif</th>
                            <th style="background-color: #00695c; color: white;">Terdaftar</th>
                            <th style="background-color: #006064; color: white;">Aktif</th>
                            <th style="background-color: #1565c0; color: white;">Terdaftar</th>
                            <th style="background-color: #0d47a1; color: white;">Aktif</th>
                            <th style="background-color: #616161; color: white;">Terdaftar</th>
                            <th style="background-color: #424242; color: white;">Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_mpi_terdaftar = $grand_mpi_aktif = 0;
                        $grand_ap_terdaftar = $grand_ap_aktif = 0;
                        $grand_ah_terdaftar = $grand_ah_aktif = 0;
                        $grand_pka_terdaftar = $grand_pka_aktif = 0;
                        $grand_trpl_terdaftar = $grand_trpl_aktif = 0;
                        $grand_total_terdaftar = $grand_total_aktif = 0;
                        ?>
                        <?php if (!empty($mhs_angkatan_prodi)): ?>
                            <?php foreach($mhs_angkatan_prodi as $stat): ?>
                                <?php
                                $grand_mpi_terdaftar += $stat->mpi_terdaftar; $grand_mpi_aktif += $stat->mpi_aktif;
                                $grand_ap_terdaftar += $stat->ap_terdaftar; $grand_ap_aktif += $stat->ap_aktif;
                                $grand_ah_terdaftar += $stat->ah_terdaftar; $grand_ah_aktif += $stat->ah_aktif;
                                $grand_pka_terdaftar += $stat->pka_terdaftar; $grand_pka_aktif += $stat->pka_aktif;
                                $grand_trpl_terdaftar += $stat->trpl_terdaftar; $grand_trpl_aktif += $stat->trpl_aktif;
                                $grand_total_terdaftar += $stat->total_terdaftar; $grand_total_aktif += $stat->total_aktif;
                                ?>
                                <tr>
                                    <td class="fw-bold"><?= $stat->angkatan ?></td>
                                    <td><?= $stat->mpi_terdaftar ?></td>
                                    <td><?= $stat->mpi_aktif ?></td>
                                    <td><?= $stat->ap_terdaftar ?></td>
                                    <td><?= $stat->ap_aktif ?></td>
                                    <td><?= $stat->ah_terdaftar ?></td>
                                    <td><?= $stat->ah_aktif ?></td>
                                    <td><?= $stat->pka_terdaftar ?></td>
                                    <td><?= $stat->pka_aktif ?></td>
                                    <td><?= $stat->trpl_terdaftar ?></td>
                                    <td><?= $stat->trpl_aktif ?></td>
                                    <td class="fw-bold bg-light"><?= $stat->total_terdaftar ?></td>
                                    <td class="fw-bold bg-light text-success"><?= $stat->total_aktif ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #263238; color: white; font-weight: bold;">
                            <td>TOTAL</td>
                            <td><?= $grand_mpi_terdaftar ?></td>
                            <td><?= $grand_mpi_aktif ?></td>
                            <td><?= $grand_ap_terdaftar ?></td>
                            <td><?= $grand_ap_aktif ?></td>
                            <td><?= $grand_ah_terdaftar ?></td>
                            <td><?= $grand_ah_aktif ?></td>
                            <td><?= $grand_pka_terdaftar ?></td>
                            <td><?= $grand_pka_aktif ?></td>
                            <td><?= $grand_trpl_terdaftar ?></td>
                            <td><?= $grand_trpl_aktif ?></td>
                            <td style="background-color: #ffffffff;"><?= $grand_total_terdaftar ?></td>
                            <td style="background-color: #ffffffff;"><?= $grand_total_aktif ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Distribusi Mahasiswa Per Prodi</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="chart-container">
                        <canvas id="prodiChart"></canvas>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="prodi-list">
                        <div class="prodi-item">
                            <div class="prodi-icon"><i class="fas fa-globe"></i></div>
                            <div class="prodi-info">
                                <p class="prodi-name">Manajemen Pemasaran Internasional</p>
                                <div class="gender-detail-text">
                                    Alumni : <?= $_mpi_alumni; ?>
                                </div>
                            </div>
                            <div class="prodi-count"><?= $_mpi; ?></div>
                        </div>
                        <div class="prodi-item">
                            <div class="prodi-icon"><i class="fas fa-calculator"></i></div>
                            <div class="prodi-info">
                                <p class="prodi-name">Akuntansi Perpajakan</p>
                                <div class="gender-detail-text">
                                    Alumni : <?= $_ap_alumni; ?>
                                </div>
                            </div>
                            <div class="prodi-count"><?= $_ap; ?></div>
                        </div>
                        <div class="prodi-item">
                            <div class="prodi-icon"><i class="fas fa-leaf"></i></div>
                            <div class="prodi-info">
                                <p class="prodi-name">Agribisnis Hortikultura</p>
                                <div class="gender-detail-text">
                                    Alumni : <?= $_ah_alumni; ?>
                                </div>
                            </div>
                            <div class="prodi-count"><?= $_ah; ?></div>
                        </div>
                        <div class="prodi-item">
                            <div class="prodi-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div class="prodi-info">
                                <p class="prodi-name">Pengelolaan Konvensi Acara</p>
                                <div class="gender-detail-text">
                                    Alumni : <?= $_pka_alumni; ?>
                                </div>
                            </div>
                            <div class="prodi-count"><?= $_pka; ?></div>
                        </div>
                        <div class="prodi-item">
                            <div class="prodi-icon"><i class="fas fa-code"></i></div>
                            <div class="prodi-info">
                                <p class="prodi-name">Teknologi Rekayasa Perangkat Lunak</p>
                                <div class="gender-detail-text">
                                    Alumni : <?= $_trpl_alumni; ?>
                                </div>
                            </div>
                            <div class="prodi-count"><?= $_trpl; ?></div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <a href="<?= site_url('C_infografis_profile_mahasiswa/print_alumni') ?>" target="_blank" class="btn btn-sm text-white" style="background-color: #4e73df; border-radius: 20px; padding: 8px 20px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);">
                            <i class="fas fa-print me-1"></i> Cetak Data Alumni
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Sebaran Gender Mahasiswa</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="total-box">
                        <i class="fas fa-users text-dark"></i>
                        <span class="total-value"><?= $mhs_total_2; ?></span>
                        <span class="total-label">Total Mahasiswa<br><small>(Aktif + Lulus + DO)</small></span>
                    </div>
                    <div class="total-box" style="background: #e3f2fd; border-color: #bbdefb;">
                        <i class="fas fa-mars text-primary"></i>
                        <span class="total-value"><?= $mhs_total_laki; ?></span>
                        <span class="total-label">Laki-Laki</span>
                    </div>
                    <div class="total-box" style="background: #fce4ec; border-color: #f8bbd0;">
                        <i class="fas fa-venus text-danger"></i>
                        <span class="total-value"><?= $mhs_total_perempuan; ?></span>
                        <span class="total-label">Perempuan</span>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="ps-md-3">
                        <h5 class="fw-bold mb-3"><i class="fas fa-list-ul me-2"></i> Rincian Per Program Studi</h5>



                        <div class="prodi-gender-card">
                            <div>
                                <div class="prodi-name">Manajemen Pemasaran Internasional</div>
                                <div class="gender-detail-text">
                                    <i class="fas fa-mars text-primary"></i> <?= $_mpi_laki; ?> Laki-laki &nbsp; | &nbsp;
                                    <i class="fas fa-venus text-danger"></i> <?= $_mpi_perempuan; ?> Perempuan
                                </div>
                            </div>
                            <div class="prodi-total-count"><?= $_mpi; ?></div>
                        </div>

                        <div class="prodi-gender-card">
                            <div>
                                <div class="prodi-name">Akuntansi Perpajakan</div>
                                <div class="gender-detail-text">
                                    <i class="fas fa-mars text-primary"></i> <?= $_ap_laki; ?> Laki-laki &nbsp; | &nbsp;
                                    <i class="fas fa-venus text-danger"></i> <?= $_ap_perempuan; ?> Perempuan
                                </div>
                            </div>
                            <div class="prodi-total-count"><?= $_ap; ?></div>
                        </div>

                        <div class="prodi-gender-card">
                            <div>
                                <div class="prodi-name">Agribisnis Hortikultura</div>
                                <div class="gender-detail-text">
                                    <i class="fas fa-mars text-primary"></i> <?= $_ah_laki; ?> Laki-laki &nbsp; | &nbsp;
                                    <i class="fas fa-venus text-danger"></i> <?= $_ah_perempuan; ?> Perempuan
                                </div>
                            </div>
                            <div class="prodi-total-count"><?= $_ah; ?></div>
                        </div>

                        <div class="prodi-gender-card">
                            <div>
                                <div class="prodi-name">Pengelolaan Konvensi Acara</div>
                                <div class="gender-detail-text">
                                    <i class="fas fa-mars text-primary"></i> <?= $_pka_laki; ?> Laki-laki &nbsp; | &nbsp;
                                    <i class="fas fa-venus text-danger"></i> <?= $_pka_perempuan; ?> Perempuan
                                </div>
                            </div>
                            <div class="prodi-total-count"><?= $_pka; ?></div>
                        </div>

                        <div class="prodi-gender-card">
                            <div>
                                <div class="prodi-name">Teknologi Rekayasa Perangkat Lunak</div>
                                <div class="gender-detail-text">
                                    <i class="fas fa-mars text-primary"></i> <?= $_trpl_laki; ?> Laki-laki &nbsp; | &nbsp;
                                    <i class="fas fa-venus text-danger"></i> <?= $_trpl_perempuan; ?> Perempuan
                                </div>
                            </div>
                            <div class="prodi-total-count"><?= $_trpl; ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Top 10 Kota/Kabupaten Asal Mahasiswa</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="chart-container">
                        <canvas id="asalMahasiswaChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="<?= site_url('C_infografis_profile_mahasiswa/print_asal_mahasiswa') ?>" target="_blank" class="btn btn-sm text-white" style="background-color: #4e73df; border-radius: 20px; padding: 8px 20px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);">
                    <i class="fas fa-print me-1"></i> Cetak Seluruh Data
                </a>
            </div>
        </div>

        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Asal Mahasiswa Per Tahun</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="chart-container">
                        <canvas id="asalMahasiswaChartPerYear"></canvas>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="<?= site_url('C_infografis_profile_mahasiswa/print_asal_mahasiswa_tahun') ?>" target="_blank" class="btn btn-sm text-white" style="background-color: #4e73df; border-radius: 20px; padding: 8px 20px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);">
                    <i class="fas fa-print me-1"></i> Cetak Seluruh Data
                </a>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- CARD: SEKOLAH ASAL PER KABUPATEN/KOTA                       -->
        <!-- ============================================================ -->
        <div class="card card-custom" id="card-sekolah-per-kabupaten">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Sekolah Asal per Kabupaten/Kota</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>

            <!-- Baris Dropdown + Summary -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="fw-semibold d-block mb-2" style="color:#1a3c34;font-size:0.82rem;text-transform:uppercase;letter-spacing:0.06em;">
                        <i class="fas fa-map-marker-alt me-1"></i> Pilih Kabupaten/Kota (Top 30)
                    </label>
                    <select id="dropdown-kabupaten"
                            class="form-select"
                            style="border:2px solid #dae5e2;border-radius:10px;padding:10px 16px;font-size:0.9rem;color:#1a3c34;background:#f8fffe;cursor:pointer;transition:border-color 0.2s;"
                            onchange="loadSekolahByKabupaten(this.value)">
                        <option value="">&#8212; Memuat data&#8230; &#8212;</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div id="kabupaten-summary"
                         class="p-3 rounded-3 w-100"
                         style="background:#f8f9fa;display:none;">
                        <div class="fw-semibold" style="color:#1a3c34;font-size:0.82rem;">Total Mahasiswa dari Kabupaten ini</div>
                        <div id="kabupaten-total" class="fw-bold" style="font-size:1.6rem;color:#b58931;">&#8212;</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Sekolah (muncul setelah pilih dropdown) -->
            <div id="sekolah-table-wrapper" style="display:none;">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div style="max-height:525px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px;">
                            <table class="table table-bordered table-sm mb-0" id="tabel-sekolah-kabupaten">
                                <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                                    <tr>
                                        <th style="width:36px;white-space:nowrap;">#</th>
                                        <th>Nama Sekolah</th>
                                        <th style="width:60px;white-space:nowrap;">Jenis</th>
                                        <th style="width:120px;white-space:nowrap;">Jumlah Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody id="sekolah-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placeholder sebelum ada pilihan -->
            <div id="sekolah-placeholder" class="text-center py-5" style="color:#8da9a2;">
                <i class="fas fa-school" style="font-size:2.5rem;opacity:0.35;"></i>
                <p class="mt-3 mb-0" style="font-size:0.88rem;">Pilih kabupaten/kota untuk menampilkan daftar sekolah asal</p>
            </div>
        </div>

        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Pekerjaan Ayah</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="table-responsive">
                <table class="custom-step-table">
                    <thead>
                        <tr>
                            <th>Nama Pekerjaan</th>
                            <th>PSMPI</th>
                            <th>PSAP</th>
                            <th>PSAH</th>
                            <th>PSPKA</th>
                            <th>PSTRPL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pekerjaan_ayah)): ?>
                            <?php foreach ($pekerjaan_ayah as $index => $row): ?>
                                <tr class="<?= $index >= 5 ? 'd-none more-ayah-row' : '' ?>">
                                    <td class="desc-col">
                                        <i class="fas fa-briefcase text-secondary fs-4"></i> 
                                        <?= htmlspecialchars($row->nama_pekerjaan ?: 'Lainnya'); ?>
                                        <span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->total ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->mpi ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ap ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ah ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->pka ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->trpl ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pekerjaan ayah</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if(!empty($pekerjaan_ayah) && count($pekerjaan_ayah) > 5): ?>
                    <div class="text-center mt-3 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-ayah" onclick="toggleRows('ayah')">Tampilkan Semua Pekerjaan Ayah</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Pekerjaan Ibu</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="table-responsive">
                <table class="custom-step-table">
                    <thead>
                        <tr>
                            <th>Nama Pekerjaan</th>
                            <th>PSMPI</th>
                            <th>PSAP</th>
                            <th>PSAH</th>
                            <th>PSPKA</th>
                            <th>PSTRPL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pekerjaan_ibu)): ?>
                            <?php foreach ($pekerjaan_ibu as $index => $row): ?>
                                <tr class="<?= $index >= 5 ? 'd-none more-ibu-row' : '' ?>">
                                    <td class="desc-col">
                                        <i class="fas fa-briefcase text-secondary fs-4"></i> 
                                        <?= htmlspecialchars($row->nama_pekerjaan ?: 'Lainnya'); ?>
                                        <span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->total ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->mpi ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ap ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ah ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->pka ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->trpl ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pekerjaan ibu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if(!empty($pekerjaan_ibu) && count($pekerjaan_ibu) > 5): ?>
                    <div class="text-center mt-3 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-ibu" onclick="toggleRows('ibu')">Tampilkan Semua Pekerjaan Ibu</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0">Status Orang Tua Meninggal</h3>
                <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
            </div>
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="orangTuaMeninggalChart"></canvas>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="table-responsive">
                        <table class="custom-step-table">
                            <thead>
                                <tr>
                                    <th>Status Orang Tua</th>
                                    <th>PSMPI</th>
                                    <th>PSAP</th>
                                    <th>PSAH</th>
                                    <th>PSPKA</th>
                                    <th>PSTRPL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orang_tua_meninggal)): ?>
                                    <?php foreach ($orang_tua_meninggal as $row): ?>
                                        <tr>
                                            <td class="desc-col">
                                                <i class="fas fa-user-times text-secondary fs-4"></i> 
                                                <?= htmlspecialchars($row->nama_pekerjaan); ?>
                                                <span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->total ?></span>
                                            </td>
                                            <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->mpi ?></span></td>
                                            <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ap ?></span></td>
                                            <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ah ?></span></td>
                                            <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->pka ?></span></td>
                                            <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->trpl ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold m-0">Penghasilan Ayah</h3>
                        <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
                    </div>
                    <div class="table-responsive">
                        <table class="custom-step-table">
                    <thead>
                        <tr>
                            <th>Penghasilan</th>
                            <th>PSMPI</th>
                            <th>PSAP</th>
                            <th>PSAH</th>
                            <th>PSPKA</th>
                            <th>PSTRPL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penghasilan_ayah)): ?>
                            <?php foreach ($penghasilan_ayah as $index => $row): ?>
                                <tr>
                                    <td class="desc-col">
                                        <i class="fas fa-money-bill-wave text-secondary fs-4"></i> 
                                        <?= htmlspecialchars($row->nama_penghasilan); ?>
                                        <span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->total ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->mpi ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ap ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ah ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->pka ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->trpl ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data penghasilan ayah</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                    </div>
                </div>
 <div class="card card-custom h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold m-0">Penghasilan Ibu</h3>
                        <img src="<?= base_url('assets/favicon.png'); ?>" alt="WBI" style="width: 40px; opacity: 0.7;">
                    </div>
                    <div class="table-responsive">
                        <table class="custom-step-table">
                    <thead>
                        <tr>
                            <th>Penghasilan</th>
                            <th>PSMPI</th>
                            <th>PSAP</th>
                            <th>PSAH</th>
                            <th>PSPKA</th>
                            <th>PSTRPL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penghasilan_ibu)): ?>
                            <?php foreach ($penghasilan_ibu as $index => $row): ?>
                                <tr>
                                    <td class="desc-col">
                                        <i class="fas fa-money-bill-wave text-secondary fs-4"></i> 
                                        <?= htmlspecialchars($row->nama_penghasilan); ?>
                                        <span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->total ?></span>
                                    </td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->mpi ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ap ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->ah ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->pka ?></span></td>
                                    <td><span class="badge bg-secondary ms-auto" style="font-size: 12px;"><?= $row->trpl ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data penghasilan ibu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                    </div>
                </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function toggleRows(type) {
            const rows = document.querySelectorAll('.more-' + type + '-row');
            const btn = document.querySelector('.btn-toggle-' + type);
            let isHidden = false;
            
            rows.forEach(row => {
                if(row.classList.contains('d-none')) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                    isHidden = true;
                }
            });
            
            if(isHidden) {
                let label = '';
                if(type === 'ayah') label = 'Pekerjaan Ayah';
                else if(type === 'ibu') label = 'Pekerjaan Ibu';
                else if(type === 'penghasilan_ayah') label = 'Penghasilan Ayah';
                else if(type === 'penghasilan_ibu') label = 'Penghasilan Ibu';
                btn.textContent = 'Tampilkan Semua ' + label;
            } else {
                btn.textContent = 'Tampilkan Lebih Sedikit';
            }
        }

        // ============================================================
        // KONSTANTA & UTILITAS BERSAMA
        // ============================================================

        /** Palet warna tema WBI */
        const WBI_COLORS = {
            solid: ['#1a3c34', '#557169', '#8da9a2', '#b58931', '#dae5e2'],
            rgba: [
                'rgba(26, 60, 52, 1)',
                'rgba(181, 137, 49, 1)',
                'rgba(217, 83, 79, 1)',
                'rgba(85, 113, 105, 1)',
                'rgba(141, 169, 162, 1)',
            ],
        };

        /**
         * Membuat array warna solid yang berulang sesuai jumlah data.
         * Menghindari masalah saat data > 5 item.
         */
        function buildColors(count, palette = WBI_COLORS.solid) {
            return Array.from({
                length: count
            }, (_, i) => palette[i % palette.length]);
        }

        /**
         * Membuat gradient transparan vertikal untuk fill area chart.
         * @param {CanvasRenderingContext2D} ctx - Context canvas
         * @param {string} rgbaColor - Warna dalam format 'rgba(r, g, b, 1)'
         * @param {number} height - Tinggi canvas (default 300)
         */
        function createAreaGradient(ctx, rgbaColor, height = 300) {
            const gradient = ctx.createLinearGradient(0, 0, 0, height);
            gradient.addColorStop(0, rgbaColor.replace('1)', '0.4)'));
            gradient.addColorStop(0.5, rgbaColor.replace('1)', '0.1)'));
            gradient.addColorStop(1, rgbaColor.replace('1)', '0)'));
            return gradient;
        }

        /**
         * Fetch JSON dari URL dengan penanganan error terpusat.
         * @param {string} url
         * @returns {Promise<any>}
         */
        async function fetchJSON(url) {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        }

        // ============================================================
        // CHART 1: TREN PENERIMAAN MAHASISWA BARU
        // ============================================================
        // CHART 1 & 1B: TREN PENERIMAAN MAHASISWA BARU & AKTIF
        // ============================================================
        async function renderTrendCharts() {
            try {
                const [dataBaru, dataAktif] = await Promise.all([
                    fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/get_chart_data") ?>'),
                    fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/get_chart_data_aktif") ?>')
                ]);

                const maxBaru = Math.max(...dataBaru.values);
                const maxAktif = Math.max(...dataAktif.values);
                const globalMax = Math.max(maxBaru, maxAktif);
                const yMax = Math.ceil(globalMax * 1.1);

                // --- Chart Mahasiswa Baru ---
                const ctxBaru = document.getElementById('trendChart').getContext('2d');
                const gradientBaru = createAreaGradient(ctxBaru, 'rgba(26, 60, 52, 1)', 300);

                new Chart(ctxBaru, {
                    type: 'line',
                    data: {
                        labels: dataBaru.labels,
                        datasets: [{
                            label: 'Jumlah Mahasiswa Terdaftar',
                            data: dataBaru.values,
                            borderColor: '#1a3c34',
                            borderWidth: 3,
                            backgroundColor: gradientBaru,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#66867f',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: yMax,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                        },
                    },
                });

                // --- Chart Mahasiswa Aktif ---
                const ctxAktif = document.getElementById('trendChartAktif').getContext('2d');
                const gradientAktif = createAreaGradient(ctxAktif, 'rgba(143, 151, 32, 1)', 300);

                new Chart(ctxAktif, {
                    type: 'line',
                    data: {
                        labels: dataAktif.labels,
                        datasets: [{
                            label: 'Jumlah Mahasiswa Aktif',
                            data: dataAktif.values,
                            borderColor: '#729433ff',
                            borderWidth: 3,
                            backgroundColor: gradientAktif,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#729433ff',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: yMax,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                        },
                    },
                });

            } catch (error) {
                console.error('Gagal memuat Tren Charts:', error);
            }
        }

        // ============================================================
        // CHART 2: DISTRIBUSI PER PRODI (DOUGHNUT)
        // ============================================================
        async function renderProdiChart() {
            try {
                const data = await fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/get_chart_data_by_prodi") ?>');
                const ctx = document.getElementById('prodiChart').getContext('2d');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['MPI', 'AP', 'AH', 'PKA', 'TRPL'],
                        datasets: [{
                            data: data.values,
                            backgroundColor: buildColors(data.values.length),
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                    },
                });
            } catch (error) {
                console.error('Gagal memuat Prodi Chart:', error);
            }
        }

        // ============================================================
        // CHART 3: TOP 10 KOTA ASAL MAHASISWA (BAR HORIZONTAL)
        // ============================================================
        async function renderAsalMahasiswaChart() {
            try {
                const data = await fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/get_chart_asal_mahasiswa") ?>');
                const ctx = document.getElementById('asalMahasiswaChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Jumlah Mahasiswa',
                            data: data.values,
                            backgroundColor: buildColors(data.values.length),
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                ticks: {
                                    align: 'center',
                                    crossAlign: 'far',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    display: false
                                },
                            },
                            x: {
                                beginAtZero: true
                            },
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                displayColors: false
                            },
                        },
                    },
                });
            } catch (error) {
                console.error('Gagal memuat Asal Mahasiswa Chart:', error);
            }
        }

        // ============================================================
        // CHART 4: ASAL MAHASISWA PER TAHUN (LINE MULTI-DATASET)
        // ============================================================
        async function renderComparisonChart() {
            try {
                const data = await fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/get_data_by_kota_per_year") ?>');
                const canvas = document.getElementById('asalMahasiswaChartPerYear');
                const ctx = canvas.getContext('2d');
                const chartHeight = canvas.offsetHeight || 300;

                if (data.datasets) {
                    data.datasets.forEach((dataset, index) => {
                        const color = WBI_COLORS.rgba[index % WBI_COLORS.rgba.length];
                        Object.assign(dataset, {
                            borderColor: color,
                            pointBackgroundColor: color,
                            pointBorderColor: '#fff',
                            pointRadius: 5,
                            borderWidth: 3,
                            pointHoverRadius: 8,
                            fill: true,
                            backgroundColor: createAreaGradient(ctx, color, chartHeight),
                            tension: 0.3,
                        });
                    });
                }

                // Hitung nilai max dari semua dataset, lalu tambah margin 10%
                const allValues = (data.datasets || []).flatMap(ds => ds.data ?? []);
                const yMax = allValues.length ?
                    Math.ceil(Math.max(...allValues) * 1.1) :
                    undefined;

                if (window.myComparisonChart instanceof Chart) {
                    window.myComparisonChart.destroy();
                }

                window.myComparisonChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                },
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: '#ddd',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: true,
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: yMax,
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(200, 200, 200, 0.1)'
                                },
                                ticks: {
                                    precision: 0,
                                    padding: 10
                                },
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10
                                },
                            },
                        },
                    },
                });
            } catch (error) {
                console.error('Gagal memuat Comparison Chart:', error);
            }
        }

        // ============================================================
        // CHART 5: STATUS ORANG TUA MENINGGAL (STACKED BAR)
        // ============================================================
        function renderOrangTuaMeninggalChart() {
            const rawData = <?= json_encode($orang_tua_meninggal) ?>;
            if (!rawData || rawData.length === 0) return;

            const labels = ['PSMPI', 'PSAP', 'PSAH', 'PSPKA', 'PSTRPL'];
            const datasets = [];
            
            const categoryColors = {
                'Ayah Meninggal': WBI_COLORS.rgba[0],
                'Ibu Meninggal': WBI_COLORS.rgba[1],
                'Keduanya Meninggal': WBI_COLORS.rgba[2],
            };

            rawData.forEach((row, index) => {
                const color = categoryColors[row.nama_pekerjaan] || WBI_COLORS.rgba[index % WBI_COLORS.rgba.length];
                datasets.push({
                    label: row.nama_pekerjaan,
                    data: [row.mpi, row.ap, row.ah, row.pka, row.trpl],
                    backgroundColor: color,
                    borderWidth: 1
                });
            });

            const canvas = document.getElementById('orangTuaMeninggalChart');
            if(!canvas) return;
            const ctx = canvas.getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20 }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        // ============================================================
        // KABUPATEN DROPDOWN & TABEL SEKOLAH
        // ============================================================
        async function initKabupatenDropdown() {
            const sel = document.getElementById('dropdown-kabupaten');
            try {
                const res = await fetchJSON('<?= base_url("C_infografis_profile_mahasiswa/ajax_get_top_kabupaten") ?>');
                if (res.status !== 'success' || !res.data.length) {
                    sel.innerHTML = '<option value="">Tidak ada data</option>';
                    return;
                }
                sel.innerHTML = '<option value="">&#8212; Pilih Kabupaten/Kota &#8212;</option>';
                res.data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.kabupaten_kota;
                    opt.textContent = item.kabupaten_kota + ' (' + parseInt(item.total_mahasiswa).toLocaleString('id-ID') + ' mhs)';
                    opt.dataset.total = item.total_mahasiswa;
                    sel.appendChild(opt);
                });
                // Tambahkan separator + opsi Lainnya
                const sep = document.createElement('option');
                sep.disabled = true;
                sep.textContent = '──────────────────';
                sel.appendChild(sep);
                const lainnya = document.createElement('option');
                lainnya.value = '__lainnya__';
                lainnya.textContent = 'Kabupaten/Kota Lainnya (di luar Top 30)';
                sel.appendChild(lainnya);
            } catch (err) {
                sel.innerHTML = '<option value="">Gagal memuat data</option>';
                console.error('Dropdown kabupaten:', err);
            }
        }

        async function loadSekolahByKabupaten(kabupaten) {
            const wrapper     = document.getElementById('sekolah-table-wrapper');
            const placeholder = document.getElementById('sekolah-placeholder');
            const tbody       = document.getElementById('sekolah-tbody');
            const summary     = document.getElementById('kabupaten-summary');
            const totalEl     = document.getElementById('kabupaten-total');
            const sel         = document.getElementById('dropdown-kabupaten');

            if (!kabupaten) {
                wrapper.style.display     = 'none';
                placeholder.style.display = 'block';
                summary.style.display     = 'none';
                return;
            }

            const isLainnya = kabupaten === '__lainnya__';

            // Tampilkan summary total (hanya untuk kab/kota spesifik)
            if (!isLainnya) {
                const opt = sel.options[sel.selectedIndex];
                if (opt && opt.dataset.total) {
                    totalEl.textContent   = parseInt(opt.dataset.total).toLocaleString('id-ID') + ' mahasiswa';
                    summary.style.display = 'block';
                }
            } else {
                summary.style.display = 'none';
            }

            // Tampilkan wrapper dengan loading state
            wrapper.style.display     = 'block';
            placeholder.style.display = 'none';
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <div class="spinner-border spinner-border-sm" style="color:#1a3c34;" role="status"></div>
                            <span style="color:#557169;">Memuat data sekolah...</span>
                        </div>
                    </td>
                </tr>`;

            try {
                const url = isLainnya
                    ? '<?= base_url("C_infografis_profile_mahasiswa/ajax_get_sekolah_lainnya") ?>'
                    : '<?= base_url("C_infografis_profile_mahasiswa/ajax_get_sekolah_by_kabupaten") ?>?kabupaten=' + encodeURIComponent(kabupaten);
                const res = await fetchJSON(url);

                if (res.status !== 'success' || !res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4" style="color:#8da9a2;">Tidak ada data sekolah.</td></tr>';
                    return;
                }

                tbody.innerHTML = res.data.map((row, idx) => {
                    const namaCell = isLainnya
                        ? `${row.sekolah}<br><small class="text-muted">${row.kabupaten_kota}</small>`
                        : row.sekolah;
                    return `
                        <tr>
                            <td><strong>${idx + 1}</strong></td>
                            <td>${namaCell}</td>
                            <td>${row.bentuk || '-'}</td>
                            <td>${parseInt(row.total_mahasiswa).toLocaleString('id-ID')}</td>
                        </tr>`;
                }).join('');

            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Gagal memuat data sekolah.</td></tr>';
                console.error('Load sekolah kabupaten:', err);
            }
        }

        // ============================================================
        // INISIALISASI — Jalankan semua chart saat halaman siap
        // ============================================================
        renderTrendCharts();
        renderProdiChart();
        renderAsalMahasiswaChart();
        renderComparisonChart();
        renderOrangTuaMeninggalChart();
        initKabupatenDropdown();
    </script>

    
    <script>
      function sendHeight() {
        const height = document.body.scrollHeight;
        window.parent.postMessage({ type: 'setHeight', height: height }, '*');
      }
      
      window.addEventListener('load', sendHeight);
      
      // Also resend on any dynamic changes (like dynamic content or charts)
      const resizeObserver = new ResizeObserver(entries => {
        sendHeight();
      });
      resizeObserver.observe(document.body);
    </script>

</body>

</html>