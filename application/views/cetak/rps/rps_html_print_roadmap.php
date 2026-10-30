<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roadmap Pembelajaran - <?= htmlspecialchars($data_rps->kode_mk . ' ' . $data_rps->nm_mk); ?> | Politeknik WBI</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/favicon.png'); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      font-size: 9pt;
      line-height: 1.5;
      color: #1e293b;
      background-color: #f1f5f9;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* FLOATING TOOLBAR */
    .print-toolbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      background: rgba(15, 23, 42, 0.94);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      z-index: 99999;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .toolbar-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .toolbar-badge {
      background: linear-gradient(135deg, #1b4332, #2d6a4f);
      color: #d8f3dc;
      border: 1px solid rgba(116, 198, 157, 0.4);
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 6px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .toolbar-text {
      display: flex;
      flex-direction: column;
    }
    .toolbar-text-main {
      font-size: 13px;
      font-weight: 600;
      color: #fff;
    }
    .toolbar-text-sub {
      font-size: 11px;
      color: #94a3b8;
    }
    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-tb {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 6px;
      border: 1px solid transparent;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.15s ease-in-out;
    }
    .btn-tb-primary {
      background: #2d6a4f;
      color: #fff;
      border: 1px solid #40916c;
      box-shadow: 0 2px 6px rgba(45, 106, 79, 0.4);
    }
    .btn-tb-primary:hover {
      background: #1b4332;
      border-color: #52b788;
      color: #fff;
    }
    .btn-tb-excel {
      background: #b45309;
      color: #fff;
      border: 1px solid #d97706;
    }
    .btn-tb-excel:hover {
      background: #92400e;
      color: #fff;
    }
    .btn-tb-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: #e2e8f0;
      border-color: rgba(255, 255, 255, 0.15);
    }
    .btn-tb-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
    }
    .btn-tb-close {
      background: rgba(255, 255, 255, 0.08);
      color: #cbd5e1;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .btn-tb-close:hover {
      background: #ef4444;
      color: #fff;
    }

    /* DOCUMENT CANVAS & SHEETS */
    .document-container {
      margin-top: 80px;
      padding-bottom: 60px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
    }
    .sheet-label {
      font-size: 12px;
      font-weight: 600;
      color: #64748b;
      margin-bottom: -15px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .sheet {
      background: #ffffff;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08), 0 1px 4px rgba(0, 0, 0, 0.04);
      border-radius: 3px;
      position: relative;
    }
    .sheet-landscape {
      width: 330mm;
      min-height: 215mm;
      padding: 15mm 15mm 15mm 15mm;
    }
    .sheet-footer {
      margin-top: 20px;
      padding-top: 5px;
      border-top: 0.5px solid #cbd5e1;
      font-size: 8pt;
      color: #64748b;
    }

    .table-black {
      border-collapse: collapse !important;
      width: 100% !important;
      border: 1px solid #7a7a7a !important;
      margin-bottom: 0px !important;
      font-size: 8.5pt !important;
    }
    .table-black td,
    .table-black th {
      border: 1px solid #7a7a7a !important;
      padding: 5px 6px !important;
      color: #1e293b;
    }
    .text-center {
      text-align: center !important;
    }

    /* PRINT MEDIA CSS */
    @media print {
      .no-print, .print-toolbar, .sheet-label {
        display: none !important;
      }
      html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      .document-container {
        margin: 0 !important;
        padding: 0 !important;
      }
      .sheet {
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      .sheet-landscape {
        page: landscape-sheet;
      }
    }

    @page landscape-sheet {
      size: folio landscape;
      margin: 12mm 15mm 12mm 15mm;
    }
  </style>
</head>
<body>

  <!-- FLOATING TOOLBAR -->
  <header class="print-toolbar no-print">
    <div class="toolbar-title">
      <span class="toolbar-badge"><i class="fa-solid fa-sitemap"></i> ROADMAP WBI</span>
      <div class="toolbar-text">
        <span class="toolbar-text-main"><?= htmlspecialchars($data_rps->kode_mk . ' - ' . $data_rps->nm_mk); ?></span>
        <span class="toolbar-text-sub"><?= htmlspecialchars($nm_lemb); ?> | <?= !empty($data_rps->sks_total_display) ? $data_rps->sks_total_display : ((int)$data_rps->sks_mk . ' SKS'); ?></span>
      </div>
    </div>

    <div class="toolbar-actions">
      <button type="button" class="btn-tb btn-tb-primary" onclick="window.print()" title="Cetak Dokumen Roadmap atau Simpan PDF">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
      </button>
      <a href="<?= site_url('C_rps_obe/export_roadmap_excel/' . $data_rps->id_rps); ?>" class="btn-tb btn-tb-excel" title="Unduh File Excel Roadmap">
        <i class="fa-solid fa-file-excel"></i> Unduh Excel Roadmap
      </a>
      <a href="<?= site_url('C_rps_obe/preview_rps/' . md5($data_rps->id_rps)); ?>" class="btn-tb btn-tb-secondary" title="Buka Dokumen RPS Full">
        <i class="fa-regular fa-file-lines"></i> RPS Full
      </a>
      <button type="button" class="btn-tb btn-tb-close" onclick="window.close()" title="Tutup Jendela Ini">
        <i class="fa-solid fa-xmark"></i> Tutup
      </button>
    </div>
  </header>

  <!-- DOCUMENT CANVAS -->
  <main class="document-container">

    <div class="sheet-label no-print">
      <i class="fa-solid fa-sitemap"></i> Dokumen Roadmap Pembelajaran Modul (Landscape)
    </div>

    <article class="sheet sheet-landscape">
      <?php include __DIR__ . '/rps_halaman_roadmap_landscape.php'; ?>
      
      <footer class="sheet-footer">
        <table style="width: 100%; border: none !important; border-collapse: collapse;">
          <tr>
            <td style="width: 65%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: left;">
              Roadmap Pembelajaran - <?= htmlspecialchars($data_rps->nm_mk ?? ''); ?>
            </td>
            <td style="width: 35%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: right;">
              Politeknik Wilmar Bisnis Indonesia
            </td>
          </tr>
        </table>
      </footer>
    </article>

  </main>

</body>
</html>
