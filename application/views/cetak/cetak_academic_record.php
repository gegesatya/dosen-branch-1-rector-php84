<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Academic Record - <?= !empty($nim) ? $nim : ''; ?> - <?= !empty($nmpd) ? $nmpd : ''; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <style type="text/css">
    /* Global Styling */
    body {
      background-color: #525659;
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      padding: 0;
      color: #000;
    }

    /* Top Action Bar (Layar Saja) */
    .top-action-bar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #1e293b;
      color: #fff;
      border-bottom: 1px solid #334155;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      padding: 10px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .top-action-bar .bar-title {
      font-size: 14px;
      font-weight: 600;
      color: #f8fafc;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .top-action-bar .bar-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-left: auto;
    }

    /* Sheet Container (Simulasi Kertas A4) */
    .page-sheet {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      margin: 20px auto 40px auto;
      padding: 10mm 14mm 14mm 14mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
      box-sizing: border-box;
      position: relative;
    }

    /* Kop Surat */
    .kop-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2px;
    }

    .kop-logo {
      width: 95px;
      vertical-align: middle;
      text-align: center;
      padding-right: 12px;
    }

    .kop-logo img {
      width: 85px;
      max-height: 75px;
      object-fit: contain;
    }

    .kop-text {
      vertical-align: middle;
      text-align: center;
    }

    .kop-title {
      font-size: 16px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #000;
      line-height: 1.2;
    }

    .kop-prodi {
      font-size: 13.5px;
      font-weight: bold;
      text-transform: uppercase;
      color: #000;
      margin-top: 3px;
      line-height: 1.2;
    }

    .kop-address {
      font-size: 9.5px;
      color: #222;
      margin-top: 3px;
      line-height: 1.25;
    }

    .kop-divider {
      border-top: 2.5px solid #000;
      border-bottom: 1px solid #000;
      height: 3.5px;
      margin: 4px 0 12px 0;
    }

    /* Identitas Mahasiswa */
    .table-mhs-info {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      line-height: 1.35;
      margin-bottom: 12px;
    }

    .table-mhs-info td {
      padding: 3px 0;
      vertical-align: middle;
    }

    /* Ringkasan Header Banner */
    .banner-summary {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 8px 12px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }

    .banner-summary-title {
      font-size: 12px;
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .pill-stat {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 12px;
      line-height: 1.2;
    }

    .pill-stat-success {
      background-color: #ecfdf5;
      color: #059669;
      border: 1px solid #a7f3d0;
    }

    .pill-stat-warning {
      background-color: #fff7ed;
      color: #c2410c;
      border: 1px solid #fed7aa;
    }

    .pill-stat-info {
      background-color: #f0f9ff;
      color: #0284c7;
      border: 1px solid #bae6fd;
    }

    /* Tabel Academic Record (1 Tabel Memanjang ke Bawah) */
    .table-academic-record {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      margin-bottom: 12px;
      font-size: 10.5px;
    }

    .table-academic-record th,
    .table-academic-record td {
      border: 1px solid #666;
      padding: 4px 6px;
      line-height: 1.25;
      vertical-align: middle;
    }

    .table-academic-record thead th {
      background-color: #f1f5f9 !important;
      color: #1e293b;
      font-weight: bold;
      text-align: center;
      vertical-align: middle;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Sub-header Baris Tiap Semester */
    .semester-divider-row td {
      background-color: #f1f5f9 !important;
      font-weight: 700;
      font-size: 11px;
      color: #0f172a;
      padding: 5px 8px !important;
      border-top: 1.8px solid #475569 !important;
      border-bottom: 1.2px solid #475569 !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Badge & Status Elements */
    .badge-kode-mk {
      display: inline-block;
      font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: 9.5px;
      font-weight: 600;
      padding: 1px 3px;
      background-color: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 3px;
      color: #334155;
      letter-spacing: -0.2px;
      white-space: nowrap;
    }

    .badge-status-lulus {
      display: inline-block;
      font-size: 10px;
      font-weight: 600;
      color: #15803d;
    }

    .badge-status-belum {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      font-size: 9.5px;
      font-weight: 700;
      padding: 2px 6px;
      background-color: #ea580c;
      color: #ffffff;
      border-radius: 3px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .badge-status-sudah {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      font-size: 9.5px;
      font-weight: 700;
      padding: 2px 6px;
      background-color: #059669;
      color: #ffffff;
      border-radius: 3px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .badge-status-diulang-lulus {
      display: inline-block;
      font-size: 9.5px;
      font-weight: 600;
      padding: 2px 6px;
      background-color: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      border-radius: 3px;
      line-height: 1.25;
      text-align: center;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .badge-status-diulang-belum {
      display: inline-block;
      font-size: 9.5px;
      font-weight: 600;
      padding: 2px 6px;
      background-color: #fff1f2;
      color: #be123c;
      border: 1px solid #fecdd3;
      border-radius: 3px;
      line-height: 1.25;
      text-align: center;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .badge-sub-smt {
      display: block;
      font-size: 8.5px;
      font-weight: normal;
      color: #64748b;
      margin-top: 1px;
    }

    .badge-status-diganti {
      display: inline-block;
      font-size: 9.5px;
      font-weight: 600;
      padding: 2px 6px;
      background-color: #f1f5f9;
      color: #64748b;
      border: 1px solid #cbd5e1;
      border-radius: 3px;
    }

    .badge-status-disarankan {
      display: inline-block;
      font-size: 9.5px;
      font-weight: 600;
      padding: 2px 6px;
      background-color: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
      border-radius: 3px;
      line-height: 1.25;
      text-align: center;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .semester-summary-row td {
      background-color: #f1f5f9 !important;
      border-top: 1px solid #94a3b8 !important;
      border-bottom: 1.5px solid #475569 !important;
      padding: 4px 6px !important;
      line-height: 1.3;
      vertical-align: middle;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .smt-summary-label {
      text-align: right !important;
      font-weight: 700;
      color: #334155;
      font-size: 10px;
      padding-right: 10px !important;
    }

    .smt-summary-sks {
      text-align: center !important;
      font-weight: 700;
      color: #0f172a;
      font-size: 10.5px;
    }

    .smt-summary-values {
      text-align: left !important;
      padding-left: 10px !important;
    }

    .smt-stat-item {
      display: inline-block;
      margin-right: 14px;
      font-size: 10.5px;
      color: #1e293b;
    }

    .val-ips {
      font-weight: 700;
      color: #0284c7;
      font-size: 11px;
    }

    .val-ipk {
      font-weight: 700;
      color: #059669;
      font-size: 11px;
    }

    .smt-stat-total {
      display: inline-block;
      color: #64748b;
      font-size: 9.5px;
    }

    .row-mengulang-diganti {
      background-color: #fafaf9;
      color: #57534e;
    }

    .rekap-row td {
      background-color: #f8fafc !important;
      font-weight: bold;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Print Formatting */
    @media print {
      body {
        background-color: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      .no-print {
        display: none !important;
      }

      .page-sheet {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
      }

      .banner-summary {
        background-color: #f8fafc !important;
        border: 1px solid #ccc !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .table-academic-record thead th {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .semester-divider-row td {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .semester-summary-row td {
        background-color: #f1f5f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      tr, td, th {
        page-break-inside: avoid !important;
      }

      .signature-section {
        page-break-inside: avoid !important;
      }
    }
  </style>
</head>

<body>
  <!-- Top Action Bar (Layar Saja) -->
  <div class="top-action-bar no-print">
    <div class="bar-title">
      <i class="fa fa-book"></i> <strong>Academic Record Mahasiswa</strong> &mdash; <?= !empty($nim) ? $nim : ''; ?> (<?= !empty($nmpd) ? html_escape(ucwords(strtolower($nmpd))) : ''; ?>)
    </div>
    <div class="bar-actions">
      <button type="button" class="btn btn-default btn-sm" onclick="window.close();">
        <i class="fa fa-times"></i> Tutup
      </button>
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print();" style="font-weight: bold; padding: 5px 18px;">
        <i class="fa fa-print"></i> Cetak Dokumen
      </button>
    </div>
  </div>

  <!-- Page Sheet Container -->
  <div class="page-sheet">
    <!-- Kop Surat -->
    <?php
    $fcpath_dir = defined('FCPATH') ? FCPATH : dirname(dirname(dirname(__DIR__))) . '/';
    $logo_path = $fcpath_dir . 'assets/logo.png';
    if (!file_exists($logo_path)) {
        $logo_path = $fcpath_dir . 'assets/image_web/kop_wbi.png';
    }
    if (file_exists($logo_path)) {
        $logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
    } elseif (function_exists('base_url')) {
        $logo_src = base_url('assets/logo.png');
    } else {
        $logo_src = '/assets/logo.png';
    }
    ?>
    <table class="kop-table">
      <tr>
        <td class="kop-logo">
          <img src="<?= $logo_src; ?>" alt="Logo WBI">
        </td>
        <td class="kop-text">
          <div class="kop-title">POLITEKNIK WILMAR BISNIS INDONESIA</div>
          <div class="kop-prodi">ACADEMIC RECORD &amp; REKAM JEJAK MAHASISWA</div>
          <div class="kop-address">
            Jl. Kapten Batu Sihombing Medan Estate, Percut Sei Tuan, Deli Serdang - Sumatera Utara 20371<br>
            Telp. +62 61 4207 4111 | Website: https://wbi.ac.id
          </div>
        </td>
      </tr>
    </table>

    <div class="kop-divider"></div>

    <!-- Identitas Mahasiswa -->
    <table class="table-mhs-info">
      <tr>
        <td style="width: 14%; white-space: nowrap;">Nama Mahasiswa</td>
        <td style="width: 1.5%; text-align: center;">:</td>
        <td style="width: 31%;"><strong><?= !empty($nmpd) ? html_escape(strtoupper($nmpd)) : ''; ?></strong></td>
        <td style="width: 2%;"></td>
        <td style="width: 12%; white-space: nowrap;">Program Studi</td>
        <td style="width: 1.5%; text-align: center;">:</td>
        <td style="width: 38%; white-space: nowrap;"><strong><?= !empty($prodi) ? html_escape($prodi) : '-'; ?></strong></td>
      </tr>
      <tr>
        <td style="white-space: nowrap;">NIM</td>
        <td style="text-align: center;">:</td>
        <td><strong><?= !empty($nim) ? html_escape($nim) : ''; ?></strong></td>
        <td></td>
        <td style="white-space: nowrap;">Tanggal Masuk</td>
        <td style="text-align: center;">:</td>
        <td style="white-space: nowrap;"><?= !empty($tgl_masuk_sp) ? html_escape($tgl_masuk_sp) : '-'; ?></td>
      </tr>
      <tr>
        <td style="white-space: nowrap;">Tempat, Tgl. Lahir</td>
        <td style="text-align: center;">:</td>
        <td><?= !empty($tlahir) ? html_escape($tlahir) : '-'; ?></td>
        <td></td>
        <td style="white-space: nowrap;">Status Akademik</td>
        <td style="text-align: center;">:</td>
        <td style="white-space: nowrap;"><span style="color: #059669; font-weight: 600;">Aktif</span></td>
      </tr>
    </table>

    <!-- Bar Ringkasan Status & Pemantauan Mengulang -->
    <div class="banner-summary">
      <div class="banner-summary-title">
        <i class="fa fa-history text-muted"></i>
        <span>Pemantauan Mata Kuliah &amp; Rekam Nilai:</span>
      </div>
      <div>
        <span class="pill-stat pill-stat-success">
          <i class="fa fa-check-circle"></i> <?= (int)$count_de_sudah_diulang; ?> Sudah Diulang (Nilai Diakui)
        </span>
        <?php if (!empty($count_d_disarankan)): ?>
        &nbsp;
        <span class="pill-stat" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
          <i class="fa fa-info-circle"></i> <?= (int)$count_d_disarankan; ?> Disarankan Mengulang (Nilai D)
        </span>
        <?php endif; ?>
        <?php if (!empty($count_e_belum_diulang)): ?>
        &nbsp;
        <span class="pill-stat pill-stat-warning">
          <i class="fa fa-exclamation-triangle"></i> <?= (int)$count_e_belum_diulang; ?> Belum Diulang (Nilai E)
        </span>
        <?php endif; ?>
        &nbsp;
        <span class="pill-stat pill-stat-info">
          <i class="fa fa-bookmark"></i> Total <?= (int)$total_mk_diambil; ?> MK Diambil
        </span>
      </div>
    </div>

    <!-- TABEL ACADEMIC RECORD TUNGGAL (1 TABEL MEMANJANG KE BAWAH DARI SEMESTER 1) -->
    <table class="table-academic-record">
      <thead>
        <tr>
          <th style="width: 3%; text-align: center; padding: 4px 2px;">No</th>
          <th style="width: 8.5%; text-align: center; padding: 4px 2px;">Kode MK</th>
          <th style="width: 44%; text-align: left;">Mata Kuliah</th>
          <th style="width: 6%;">SKS (K)</th>
          <th style="width: 6%;">Nilai (NM)</th>
          <th style="width: 6.5%;">Indeks (NA)</th>
          <th style="width: 8%;">Bobot (K&times;NA)</th>
          <th style="width: 18%; text-align: center;">Status / Record</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          $no = 0;
          $current_smt = null;
          $current_smt_label = '';
          $total_rows = !empty($academic_records) ? count($academic_records) : 0;

          if ($total_rows > 0):
            for ($idx = 0; $idx < $total_rows; $idx++):
              $row = $academic_records[$idx];
              $no++;
              
              // Jika berganti semester
              if ($row['id_smt'] !== $current_smt):
                // Jika sebelumnya ada semester yang berjalan, cetak baris ringkasan capaian semester sebelumnya
                if ($current_smt !== null):
                  $smt_info = isset($semester_summary[$current_smt]) ? $semester_summary[$current_smt] : null;
        ?>
                  <tr class="semester-summary-row">
                    <td colspan="3" class="smt-summary-label">
                      <i class="fa fa-line-chart" style="color: #0284c7; margin-right: 3px;"></i>
                      Capaian <?= strtoupper(html_escape($current_smt_label)); ?> (Periode: <?= html_escape($current_smt); ?>) :
                    </td>
                    <td class="smt-summary-sks">
                      <?= !empty($smt_info) ? (int)$smt_info['sks_smt'] : '-'; ?> SKS
                    </td>
                    <td colspan="4" class="smt-summary-values">
                      <span class="smt-stat-item">
                        <strong>IPS :</strong> <span class="val-ips"><?= !empty($smt_info) ? number_format($smt_info['ips'], 2) : '-'; ?></span>
                      </span>
                      <span class="smt-stat-item">
                        <strong>IPK :</strong> <span class="val-ipk"><?= !empty($smt_info) ? number_format($smt_info['ipk'], 2) : '-'; ?></span>
                      </span>
                      <?php if (!empty($smt_info['sks_total'])): ?>
                      <span class="smt-stat-total">
                        (SKS Kumulatif: <strong><?= (int)$smt_info['sks_total']; ?></strong>)
                      </span>
                      <?php endif; ?>
                    </td>
                  </tr>
        <?php 
                endif;

                $current_smt = $row['id_smt'];
                $current_smt_label = $row['smt_label'];
        ?>
                <tr class="semester-divider-row">
                  <td colspan="8">
                    <i class="fa fa-calendar-check-o" style="margin-right: 4px;"></i> 
                    <?= strtoupper(html_escape($row['smt_label'])); ?> 
                    <span style="font-weight: normal; font-size: 10px; color: #475569;">(Periode Semester: <?= html_escape($row['id_smt']); ?>)</span>
                  </td>
                </tr>
        <?php 
              endif;

              $is_diganti = ($row['status_code'] === 'diulang_diganti' || $row['status_code'] === 'diulang_lulus' || $row['status_code'] === 'diulang_belum_lulus');
              $row_class = $is_diganti ? 'row-mengulang-diganti' : '';
              
              $nh_val = strtoupper(trim($row['nilai_huruf']));
              $color_nh = '#0f172a';
              if ($nh_val === 'E') {
                $color_nh = '#dc2626';
              } elseif ($nh_val === 'D') {
                $color_nh = '#d97706';
              }
        ?>
              <tr class="<?= $row_class; ?>">
                <td style="text-align: center; color: #64748b; padding: 4px 2px; font-size: 10px;"><?= $no; ?>.</td>
                <td style="text-align: center; padding: 4px 2px;">
                  <span class="badge-kode-mk"><?= html_escape($row['kode_mk']); ?></span>
                </td>
                <td style="font-weight: <?= $is_diganti ? 'normal' : '600'; ?>; color: <?= $is_diganti ? '#64748b' : '#0f172a'; ?>;">
                  <?= html_escape($row['nm_mk']); ?>
                </td>
                <td style="text-align: center; font-weight: bold;"><?= (int)$row['sks_mk']; ?></td>
                <td style="text-align: center; font-weight: bold; color: <?= $color_nh; ?>;">
                  <?= html_escape($row['nilai_huruf']); ?>
                </td>
                <td style="text-align: center;"><?= $row['nilai_indeks'] + 0; ?></td>
                <td style="text-align: center; font-weight: bold;"><?= number_format($row['bobot'], 1); ?></td>
                <td style="text-align: center;">
                  <?php if ($row['status_code'] === 'lulus'): ?>
                    <span class="badge-status-lulus"><i class="fa fa-check"></i> Lulus</span>
                  <?php elseif ($row['status_code'] === 'disarankan_mengulang'): ?>
                    <span class="badge-status-disarankan" title="Nilai D (Lulus), disarankan mengulang untuk perbaikan indeks prestasi">
                      <i class="fa fa-info-circle"></i> Disarankan mengulang
                    </span>
                  <?php elseif ($row['status_code'] === 'belum_diulang'): ?>
                    <span class="badge-status-belum">
                      <i class="fa fa-exclamation-triangle"></i> Belum Diulang
                    </span>
                  <?php elseif ($row['status_code'] === 'diulang_terbaik'): ?>
                    <span class="badge-status-sudah">
                      <i class="fa fa-check-circle"></i> Nilai Diakui
                    </span>
                  <?php elseif ($row['status_code'] === 'diulang_belum_lulus_terbaik'): ?>
                    <span class="badge-status-belum">
                      <i class="fa fa-exclamation-triangle"></i> <?= html_escape($row['status_label']); ?>
                    </span>
                  <?php elseif ($row['status_code'] === 'diulang_lulus'): ?>
                    <span class="badge-status-diulang-lulus" title="Sudah diulang dan dinyatakan lulus">
                      <i class="fa fa-check-circle"></i> Diulang dan lulus
                      <?php if (!empty($row['smt_target'])): ?>
                        <span class="badge-sub-smt">(smt <?= html_escape($row['smt_target']); ?>)</span>
                      <?php endif; ?>
                    </span>
                  <?php elseif ($row['status_code'] === 'diulang_belum_lulus'): ?>
                    <span class="badge-status-diulang-belum" title="Diulang namun belum lulus">
                      <i class="fa fa-repeat"></i> Diulang belum lulus
                      <?php if (!empty($row['smt_target'])): ?>
                        <span class="badge-sub-smt">(smt <?= html_escape($row['smt_target']); ?>)</span>
                      <?php endif; ?>
                    </span>
                  <?php else: ?>
                    <span class="badge-status-diganti">
                      <?= html_escape($row['status_label']); ?>
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
        <?php 
            endfor;

            // Baris ringkasan capaian untuk semester terakhir
            if ($current_smt !== null):
              $smt_info = isset($semester_summary[$current_smt]) ? $semester_summary[$current_smt] : null;
        ?>
              <tr class="semester-summary-row">
                <td colspan="3" class="smt-summary-label">
                  <i class="fa fa-line-chart" style="color: #0284c7; margin-right: 3px;"></i>
                  Capaian <?= strtoupper(html_escape($current_smt_label)); ?> (Periode: <?= html_escape($current_smt); ?>) :
                </td>
                <td class="smt-summary-sks">
                  <?= !empty($smt_info) ? (int)$smt_info['sks_smt'] : '-'; ?> SKS
                </td>
                <td colspan="4" class="smt-summary-values">
                  <span class="smt-stat-item">
                    <strong>IPS :</strong> <span class="val-ips"><?= !empty($smt_info) ? number_format($smt_info['ips'], 2) : '-'; ?></span>
                  </span>
                  <span class="smt-stat-item">
                    <strong>IPK :</strong> <span class="val-ipk"><?= !empty($smt_info) ? number_format($smt_info['ipk'], 2) : '-'; ?></span>
                  </span>
                  <?php if (!empty($smt_info['sks_total'])): ?>
                  <span class="smt-stat-total">
                    (SKS Kumulatif: <strong><?= (int)$smt_info['sks_total']; ?></strong>)
                  </span>
                  <?php endif; ?>
                </td>
              </tr>
        <?php 
            endif;
          else: 
        ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                Belum ada data nilai akademik untuk mahasiswa ini.
              </td>
            </tr>
        <?php endif; ?>

        <!-- Baris Rekapitulasi Akhir -->
        <tr class="rekap-row">
          <td colspan="3" style="text-align: right; font-weight: bold; padding-right: 12px;">Total SKS Pernah Ditempuh :</td>
          <td style="text-align: center; font-weight: bold; color: #475569;"><?= (int)$total_sks_diambil; ?> SKS</td>
          <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 12px;">Total Mutu Diakui :</td>
          <td style="text-align: center; font-weight: bold;"><?= number_format($total_mutu_diakui, 1); ?></td>
          <td style="text-align: center; font-size: 9.5px; color: #64748b;">(Nilai Terbaik)</td>
        </tr>
        <tr class="rekap-row">
          <td colspan="3" style="text-align: right; font-weight: bold; padding-right: 12px;">Total SKS Riil yang Diakui :</td>
          <td style="text-align: center; font-weight: bold; color: #059669; font-size: 11.5px;"><?= (int)$total_sks_diakui; ?> SKS</td>
          <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 12px; font-size: 11px;">Indeks Prestasi Kumulatif (IPK) :</td>
          <td colspan="2" style="text-align: center; font-weight: bold; font-size: 12px; color: #0284c7;">
            <?= number_format($ipk, 2); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</body>

</html>
