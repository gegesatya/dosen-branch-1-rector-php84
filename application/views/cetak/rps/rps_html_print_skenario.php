<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Strategi Pertemuan & Draf BAP - <?= htmlspecialchars($data_rps->kode_mk . ' ' . $data_rps->nm_mk); ?> | Politeknik WBI</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/favicon.png'); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
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
      font-size: 9.5pt;
      line-height: 1.55;
      color: #0f172a;
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
      background: linear-gradient(135deg, #4338ca, #6366f1);
      color: #e0e7ff;
      border: 1px solid rgba(199, 210, 254, 0.4);
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
      font-size: 13.5px;
      font-weight: 700;
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
      background: #4f46e5;
      color: #fff;
      border: 1px solid #6366f1;
      box-shadow: 0 2px 6px rgba(79, 70, 229, 0.4);
    }
    .btn-tb-primary:hover {
      background: #4338ca;
      border-color: #818cf8;
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

    /* DOCUMENT CONTAINER */
    .doc-container {
      margin-top: 80px;
      padding-bottom: 60px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
    }

    .doc-sheet {
      background: #ffffff;
      width: 215mm;
      min-height: 297mm;
      padding: 20mm 20mm 20mm 20mm;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04);
      border-radius: 4px;
      position: relative;
    }

    /* KOP & HEADER */
    .kop-wrapper {
      display: flex;
      align-items: center;
      gap: 16px;
      border-bottom: 2.5px solid #0f172a;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .kop-logo {
      width: 68px;
      height: 68px;
      object-fit: contain;
    }
    .kop-text {
      flex: 1;
      text-align: center;
    }
    .kop-inst {
      font-size: 14pt;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: 0.5px;
      margin: 0;
      text-transform: uppercase;
    }
    .kop-prodi {
      font-size: 11pt;
      font-weight: 700;
      color: #1e293b;
      margin: 2px 0 0 0;
      text-transform: uppercase;
    }
    .kop-addr {
      font-size: 8pt;
      color: #64748b;
      margin: 2px 0 0 0;
    }

    .doc-header-title {
      text-align: center;
      margin-bottom: 18px;
    }
    .doc-header-title h1 {
      font-size: 12.5pt;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
      margin: 0;
      letter-spacing: 0.5px;
    }
    .doc-header-title .doc-subtitle {
      font-size: 9.5pt;
      font-weight: 600;
      color: #4f46e5;
      margin-top: 3px;
    }

    /* INFO TABLE */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 9pt;
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      border-radius: 6px;
      overflow: hidden;
    }
    .info-table td {
      padding: 6px 10px;
      border: 1px solid #e2e8f0;
      vertical-align: middle;
    }
    .info-table .info-lbl {
      width: 22%;
      font-weight: 700;
      color: #334155;
      background: #f1f5f9;
    }
    .info-table .info-val {
      width: 28%;
      color: #0f172a;
      font-weight: 600;
    }

    /* MEETING CARD */
    .meeting-card {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      margin-bottom: 16px;
      overflow: hidden;
      page-break-inside: avoid;
      transition: all 0.2s ease;
    }
    .meeting-card.highlight-uts-uas {
      border-color: #fde68a;
      background: #fffdf5;
    }
    .meeting-header {
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      padding: 8px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
    }
    .meeting-badge-num {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #1e293b;
      color: #ffffff;
      font-weight: 800;
      font-size: 9.5pt;
      padding: 3px 10px;
      border-radius: 5px;
      letter-spacing: 0.3px;
    }
    .meeting-badge-uts-uas {
      background: #b45309;
      color: #ffffff;
    }
    .meeting-duration {
      font-size: 8.5pt;
      font-weight: 600;
      color: #475569;
      background: #ffffff;
      padding: 2.5px 8px;
      border-radius: 4px;
      border: 1px solid #cbd5e1;
    }

    .meeting-body {
      padding: 12px 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .row-field {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .field-lbl {
      font-size: 8pt;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .field-val {
      font-size: 9pt;
      color: #0f172a;
      line-height: 1.45;
    }

    .sub-cpmk-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 5px;
      padding: 6px 10px;
      font-size: 8.8pt;
      color: #166534;
      font-weight: 600;
    }
    .sub-cpmk-badge {
      display: inline-block;
      background: #15803d;
      color: #ffffff;
      font-size: 7.5pt;
      font-weight: 800;
      padding: 1.5px 6px;
      border-radius: 3px;
      margin-right: 4px;
    }

    .bap-draft-box {
      background: #f8fafc;
      border-left: 3.5px solid #4f46e5;
      border-radius: 0 5px 5px 0;
      padding: 8px 12px;
      margin-top: 4px;
      border-top: 1px solid #e2e8f0;
      border-right: 1px solid #e2e8f0;
      border-bottom: 1px solid #e2e8f0;
    }
    .bap-draft-title {
      font-size: 8pt;
      font-weight: 700;
      color: #4338ca;
      text-transform: uppercase;
      margin-bottom: 3px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .bap-draft-content {
      font-size: 8.5pt;
      color: #334155;
      line-height: 1.45;
      font-style: italic;
    }

    /* SIGNATURE BLOCK */
    .sign-container {
      margin-top: 26px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      page-break-inside: avoid;
    }
    .sign-box {
      text-align: center;
      font-size: 8.5pt;
      color: #334155;
    }
    .sign-title {
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 50px;
    }
    .sign-name {
      font-weight: 700;
      color: #0f172a;
      border-top: 1px solid #0f172a;
      display: inline-block;
      min-width: 140px;
      padding-top: 4px;
    }
    .sign-nidn {
      font-size: 7.5pt;
      color: #64748b;
    }

    /* FOOTER */
    .doc-footer {
      margin-top: 24px;
      padding-top: 8px;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 7.5pt;
      color: #94a3b8;
    }

    /* PRINT RULES */
    @media print {
      .print-toolbar {
        display: none !important;
      }
      html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      .doc-container {
        margin: 0 !important;
        padding: 0 !important;
        gap: 0 !important;
      }
      .doc-sheet {
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 12mm 15mm 12mm 15mm !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      .meeting-card {
        page-break-inside: avoid;
        border-color: #cbd5e1 !important;
      }
    }
  </style>
</head>
<body>

  <!-- Floating Navigation & Print Toolbar -->
  <header class="print-toolbar no-print">
    <div class="toolbar-title">
      <span class="toolbar-badge"><i class="fa fa-calendar-check-o"></i> STRATEGI PERTEMUAN &amp; BAP</span>
      <div class="toolbar-text">
        <span class="toolbar-text-main">[<?= htmlspecialchars($data_rps->kode_mk ?: 'MK'); ?>] <?= htmlspecialchars($data_rps->nm_mk ?: 'Mata Kuliah'); ?></span>
        <span class="toolbar-text-sub"><?= htmlspecialchars($nm_lemb); ?> &bull; <?= htmlspecialchars($data_rps->sks_total_display ?? '3 SKS'); ?></span>
      </div>
    </div>
    <div class="toolbar-actions">
      <button type="button" onclick="window.print()" class="btn-tb btn-tb-primary" title="Cetak Dokumen atau Simpan ke PDF">
        <i class="fa fa-print"></i> Cetak / Simpan PDF
      </button>
      <a href="<?= site_url('C_rps_obe/preview_rps/' . md5($data_rps->id_rps)); ?>" target="_blank" class="btn-tb btn-tb-secondary" title="Buka Dokumen RPS Full">
        <i class="fa fa-book"></i> RPS Full
      </a>
      <a href="<?= site_url('C_rps_obe/preview_roadmap/' . md5($data_rps->id_rps)); ?>" target="_blank" class="btn-tb btn-tb-secondary" title="Buka Roadmap Pembelajaran">
        <i class="fa fa-sitemap"></i> Roadmap
      </a>
      <button type="button" onclick="window.close()" class="btn-tb btn-tb-close" title="Tutup Halaman">
        <i class="fa fa-times"></i> Tutup
      </button>
    </div>
  </header>

  <!-- Main Document Container -->
  <main class="doc-container">
    <article class="doc-sheet">
      
      <!-- Kop Dokumen -->
      <div class="kop-wrapper">
        <img src="<?= base_url('assets/wbi_logo.png'); ?>" alt="Logo WBI" class="kop-logo" onerror="this.style.display='none'">
        <div class="kop-text">
          <h2 class="kop-inst">POLITEKNIK WILMAR BISNIS INDONESIA</h2>
          <h3 class="kop-prodi"><?= strtoupper(htmlspecialchars($nm_lemb)); ?></h3>
          <p class="kop-addr">Jalan Laut Dendang, Percut Sei Tuan, Deli Serdang, Sumatera Utara 20371 &bull; SPMI WBI</p>
        </div>
      </div>

      <!-- Judul Dokumen -->
      <div class="doc-header-title">
        <h1>SKENARIO STRATEGI PEMBELAJARAN SCL &amp; DRAF BAP 16 MINGGU</h1>
        <div class="doc-subtitle">Berstandar Outcome-Based Education (OBE SN-Dikti 2026 &amp; SPMI WBI)</div>
      </div>

      <!-- Tabel Identitas Mata Kuliah -->
      <table class="info-table">
        <tr>
          <td class="info-lbl">Mata Kuliah</td>
          <td class="info-val"><?= htmlspecialchars($data_rps->nm_mk ?: '-'); ?></td>
          <td class="info-lbl">Kode Mata Kuliah</td>
          <td class="info-val" style="font-family: 'JetBrains Mono', monospace;"><?= htmlspecialchars($data_rps->kode_mk ?: '-'); ?></td>
        </tr>
        <tr>
          <td class="info-lbl">Bobot SKS</td>
          <td class="info-val"><?= htmlspecialchars($data_rps->sks_total_display ?? '3 SKS'); ?></td>
          <td class="info-lbl">Semester / Tahun</td>
          <td class="info-val"><?= htmlspecialchars($data_rps->semester ?: 'Semester Aktif'); ?></td>
        </tr>
        <tr>
          <td class="info-lbl">Dosen Pengampu</td>
          <td class="info-val"><?= htmlspecialchars($data_rps->dosen_pengampu ?: '-'); ?></td>
          <td class="info-lbl">Program Studi</td>
          <td class="info-val"><?= htmlspecialchars($nm_lemb); ?></td>
        </tr>
      </table>

      <!-- 16 Meeting Cards / Skenario Pertemuan -->
      <section style="display: flex; flex-direction: column;">
        <?php if (!empty($meetings)) : ?>
          <?php foreach ($meetings as $m_num => $m) : 
            $is_exam = ($m_num == 8 || $m_num == 16);
            $badge_cls = $is_exam ? 'meeting-badge-uts-uas' : '';
            $minggu_ke = $m['minggu'] ?? $m_num;
          ?>
            <div class="meeting-card <?= $is_exam ? 'highlight-uts-uas' : ''; ?>">
              
              <!-- Header Pertemuan -->
              <div class="meeting-header">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span class="meeting-badge-num <?= $badge_cls; ?>">
                    <?php if ($m_num == 8) : ?>
                      <i class="fa fa-pencil-square-o"></i> PERTEMUAN 8 &bull; UTS (Evaluasi Tengah Semester)
                    <?php elseif ($m_num == 16) : ?>
                      <i class="fa fa-trophy"></i> PERTEMUAN 16 &bull; UAS (Evaluasi Akhir Semester)
                    <?php else : ?>
                      <i class="fa fa-bookmark"></i> PERTEMUAN <?= $m_num; ?> (Minggu <?= $minggu_ke; ?>)
                    <?php endif; ?>
                  </span>
                </div>
                <div class="meeting-duration">
                  <i class="fa fa-clock-o" style="color:#4f46e5;"></i> 
                  <?= round(($m['jam_teori'] ?? 1) * 50); ?> Menit TM / Teori &bull; <?= round(($m['jam_praktek'] ?? 2) * 50); ?> Menit Praktek
                </div>
              </div>

              <!-- Isi Detail Pertemuan -->
              <div class="meeting-body">
                
                <!-- Sub-CPMK -->
                <?php if (!empty($m['sub_cpmk'])) : ?>
                  <div class="sub-cpmk-box">
                    <span class="sub-cpmk-badge">TARGET SUB-CPMK</span>
                    <?= htmlspecialchars($m['sub_cpmk']); ?>
                  </div>
                <?php endif; ?>

                <!-- Bahan Kajian & Topik -->
                <div class="row-field">
                  <span class="field-lbl"><i class="fa fa-book"></i> Bahan Kajian &amp; Pokok Bahasan:</span>
                  <div class="field-val">
                    <strong><?= htmlspecialchars(!empty($m['bahan_kajian']) ? $m['bahan_kajian'] : (!empty($m['bacaan_materi']) ? $m['bacaan_materi'] : 'Sesuai silabus pertemuan')); ?></strong>
                  </div>
                </div>

                <!-- Bentuk & Metode Pembelajaran SCL -->
                <?php if (!empty($m['metode']) || !empty($m['berita_acara_rencana'])) : ?>
                  <div class="row-field">
                    <span class="field-lbl"><i class="fa fa-cogs"></i> Model / Metode Pembelajaran SCL:</span>
                    <div class="field-val">
                      <?= htmlspecialchars(!empty($m['metode']) ? $m['metode'] : (!empty($m['berita_acara_rencana']) ? $m['berita_acara_rencana'] : 'Kuliah Interaktif, Diskusi Kelompok, Studi Kasus / Problem-Based Learning')); ?>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Skenario Pelaksanaan Pembelajaran SCL (Jika ada rincian sintaks) -->
                <?php if (!empty($m['skenario_scl'])) : ?>
                  <div class="row-field" style="margin-top: 2px;">
                    <span class="field-lbl"><i class="fa fa-list-ol"></i> Skenario Pelaksanaan SCL:</span>
                    <div class="field-val" style="background: #fafafa; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px 10px; font-size: 8.5pt;">
                      <?= nl2br(htmlspecialchars($m['skenario_scl'])); ?>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Draft Evaluasi Pertemuan -->
                <?php if (!empty($m['draf_bap'])) : ?>
                  <div class="bap-draft-box">
                    <div class="bap-draft-title">
                      <i class="fa fa-file-text-o"></i> Draft Evaluasi Pertemuan:
                    </div>
                    <div class="bap-draft-content">
                      "<?= htmlspecialchars($m['draf_bap']); ?>"
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Indikator & Kriteria Penilaian -->
                <?php if (!empty($m['indikator_evaluasi']) || !empty($m['evaluasi'])) : ?>
                  <div class="row-field" style="margin-top: 2px;">
                    <span class="field-lbl"><i class="fa fa-check-circle-o"></i> Kriteria &amp; Bentuk Penilaian:</span>
                    <div class="field-val" style="font-size: 8.5pt; color: #475569;">
                      <?= htmlspecialchars(!empty($m['indikator_evaluasi']) ? $m['indikator_evaluasi'] : $m['evaluasi']); ?>
                    </div>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b;">
            <i class="fa fa-info-circle fa-2x" style="margin-bottom: 8px; color: #94a3b8; display: block;"></i>
            Belum ada rincian skenario pertemuan yang tersimpan untuk dokumen RPS ini.
          </div>
        <?php endif; ?>
      </section>

      <!-- Tanda Tangan Pengesahan -->
      <section class="sign-container">
        <div class="sign-box">
          <div class="sign-title">Dosen Pengampu / Pengembang RPS</div>
          <div class="sign-name"><?= htmlspecialchars($data_rps->dosen_pengampu ?: 'Dosen Pengampu'); ?></div>
          <div class="sign-nidn">NIDN / NIP. <?= htmlspecialchars($data_rps->id_ptk ?: '-'); ?></div>
        </div>
        <div class="sign-box">
          <div class="sign-title">Koordinator Kelompok Bidang Keahlian</div>
          <div class="sign-name"><?= htmlspecialchars(!empty($data_rps->koordinator_rps_nama) ? $data_rps->koordinator_rps_nama : (!empty($data_rps->dosen_pengampu) ? $data_rps->dosen_pengampu : 'Koordinator RPS')); ?></div>
          <div class="sign-nidn">NIDN / NIP. <?= htmlspecialchars(!empty($data_rps->koordinator_rps) ? $data_rps->koordinator_rps : '-'); ?></div>
        </div>
        <div class="sign-box">
          <div class="sign-title">Ketua Program Studi <?= htmlspecialchars($nm_lemb); ?></div>
          <div class="sign-name"><?= htmlspecialchars(!empty($data_rps->kaprodi_nama) ? $data_rps->kaprodi_nama : ($kaprodi_info->nama ?? ($kaprodi_info->nm_ptk ?? 'Ketua Program Studi'))); ?></div>
          <div class="sign-nidn">NIDN / NIP. <?= htmlspecialchars(!empty($data_rps->kaprodi_rps) ? $data_rps->kaprodi_rps : ($kaprodi_info->nidn ?? ($kaprodi_info->nip ?? '-'))); ?></div>
        </div>
      </section>

      <!-- Footer Dokumen -->
      <footer class="doc-footer">
        <span>Dokumen Pembelajaran SPMI &bull; Politeknik Wilmar Bisnis Indonesia</span>
        <span>Dicetak melalui SIAKAD Dosen &bull; <?= date('d/m/Y H:i'); ?> WIB</span>
      </footer>

    </article>
  </main>

</body>
</html>
