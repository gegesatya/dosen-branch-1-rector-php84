<style>
  .swal2-container {
    z-index: 9999999 !important;
  }
  .sweet-alert, .sweet-overlay {
    z-index: 9999999 !important;
  }
  .kuri-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    background: #f8fafc;
    min-height: calc(100vh - 120px);
    padding: 20px 24px;
    margin: -15px -20px -10px -20px;
  }

  .kuri-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    background: #ffffff;
    padding: 18px 24px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    margin-bottom: 20px;
  }

  .kuri-title-wrap h3 {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .kuri-title-wrap p {
    font-size: 12.5px;
    color: #64748b;
    margin: 0;
  }

  .kuri-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
  }

  .kuri-stat-card {
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
  }

  .kuri-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .kuri-stat-icon.blue { background: #eff6ff; color: #2563eb; }
  .kuri-stat-icon.green { background: #f0fdf4; color: #16a34a; }
  .kuri-stat-icon.amber { background: #fffbeb; color: #d97706; }

  .kuri-stat-num {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
  }

  .kuri-stat-label {
    font-size: 11.5px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-top: 2px;
  }

  .kuri-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: visible;
  }

  .kuri-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
  }

  .kuri-table-wrapper {
    overflow: visible;
  }

  .kuri-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }

  .kuri-table {
    width: 100%;
    margin-bottom: 0;
  }

  .kuri-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
  }

  .kuri-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: #334155;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
  }

  .kuri-table tr:hover td {
    background: #fbfdff;
  }

  .btn-upload-kuri {
    background: #0f766e;
    color: #ffffff;
    border: none;
    padding: 9px 18px;
    font-weight: 700;
    font-size: 13px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
    transition: all 0.15s ease;
  }
  .btn-upload-kuri:hover {
    background: #0d9488;
    color: #ffffff;
    transform: translateY(-1px);
  }

  .btn-audit-kuri {
    background: #4f46e5;
    color: #ffffff;
    border: none;
    padding: 6px 12px;
    font-weight: 600;
    font-size: 12px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
  }
  .btn-audit-kuri:hover {
    background: #4338ca;
    color: #ffffff;
  }

  .kuri-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
  }
  .kuri-badge.success { background: #dcfce7; color: #166534; }
  .kuri-badge.info { background: #e0f2fe; color: #0369a1; }
  .kuri-badge.purple { background: #f3e8ff; color: #6b21a8; }

  /* Dropzone Modal */
  .kuri-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
    padding: 24px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .kuri-dropzone:hover {
    border-color: #0f766e;
    background: #f0fdfa;
  }
  .kuri-dropzone input[type="file"] {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  /* Modal Audit Markdown Styling */
  .audit-markdown-view {
    font-size: 13.5px;
    line-height: 1.7;
    color: #1e293b;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }
  .audit-markdown-view h1 {
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 10px;
    margin-top: 0;
    margin-bottom: 16px;
    letter-spacing: -0.01em;
  }
  .audit-markdown-view h2 {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 24px;
    margin-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 4px;
  }
  .audit-markdown-view h3 {
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-top: 16px;
    margin-bottom: 8px;
  }
  .audit-markdown-view ul, .audit-markdown-view ol {
    padding-left: 22px;
    margin-bottom: 14px;
  }
  .audit-markdown-view li {
    margin-bottom: 6px;
    color: #334155;
  }
  .audit-markdown-view table {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
    font-size: 12.5px;
    background: #ffffff;
    border-radius: 6px;
    overflow: hidden;
  }
  .audit-markdown-view th, .audit-markdown-view td {
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    vertical-align: top;
    line-height: 1.5;
  }
  .audit-markdown-view th {
    background: #f8fafc;
    color: #1e293b;
    font-weight: 700;
    border-bottom: 2px solid #cbd5e1;
  }
  .audit-markdown-view tr:nth-child(even) td {
    background: #fafbfc;
  }

  /* Maintenance Panel Styles */
  .maint-control-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    position: relative;
    overflow: hidden;
  }
  .maint-control-card.active-alert {
    border-color: #f59e0b;
    background: linear-gradient(180deg, #fffdfa 0%, #ffffff 100%);
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.12);
  }
  .maint-control-card.active-alert::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #f59e0b, #d97706, #b45309);
  }
  .maint-module-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
  }
  .maint-module-box.is-locked {
    background: #fffbeb;
    border-color: #fde68a;
  }
  .btn-toggle-maint {
    font-weight: 700;
    font-size: 11.5px;
    border-radius: 6px;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid transparent;
  }
  .btn-toggle-maint.lock {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
  }
  .btn-toggle-maint.lock:hover {
    background: #fee2e2;
    color: #991b1b;
  }
  .btn-toggle-maint.unlock {
    background: #f0fdf4;
    color: #15803d;
    border-color: #bbf7d0;
  }
  .btn-toggle-maint.unlock:hover {
    background: #dcfce7;
    color: #166534;
  }
</style>

<div class="page-content kuri-wrapper">
  
  <!-- Header Bar -->
  <div class="kuri-header">
    <div class="kuri-title-wrap">
      <h3>
        <i class="fa fa-book text-primary"></i>
        Daftar Kurikulum Program Studi
        <span class="kuri-badge success">SN-Dikti 2026</span>
      </h3>
      <p>
        Pusat repositori naskah Kurikulum &amp; CPL Program Studi. Dokumen yang tersimpan di sini otomatis terhubung ke <strong>Studio RPS Generator</strong>.
      </p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
      <?php if (!empty($can_upload)): ?>
        <button type="button" class="btn-upload-kuri" onclick="bukaModalUpload()">
          <i class="fa fa-cloud-upload"></i> Unggah Kurikulum Baru
        </button>
      <?php else: ?>
        <span class="kuri-badge info" style="font-size:12px; padding:7px 12px; border:1px solid #bae6fd;">
          <i class="fa fa-eye"></i> Akses Dosen: Lihat &amp; Unduh PDF
        </span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="kuri-stats-grid">
    <div class="kuri-stat-card">
      <div class="kuri-stat-icon blue">
        <i class="fa fa-graduation-cap"></i>
      </div>
      <div>
        <div class="kuri-stat-num"><?= count($kurikulum_list); ?></div>
        <div class="kuri-stat-label">Dokumen Kurikulum Prodi</div>
      </div>
    </div>
    <div class="kuri-stat-card">
      <div class="kuri-stat-icon green">
        <i class="fa fa-check-circle"></i>
      </div>
      <div>
        <div class="kuri-stat-num"><?= count($prodi_list); ?></div>
        <div class="kuri-stat-label">Total Program Studi</div>
      </div>
    </div>
    <div class="kuri-stat-card">
      <div class="kuri-stat-icon amber">
        <i class="fa fa-check-square-o"></i>
      </div>
      <div>
        <div class="kuri-stat-num">Faktual</div>
        <div class="kuri-stat-label">Grounding RPS Terintegrasi</div>
      </div>
    </div>
  </div>

  <!-- Main Table Card -->
  <div class="kuri-card">
    <div class="kuri-card-header">
      <h4 class="kuri-card-title">
        <i class="fa fa-book text-muted"></i> Repositori Dokumen Kurikulum Terverifikasi
      </h4>
      <span style="font-size:12px; color:#64748b;">
        <i class="fa fa-info-circle text-primary"></i> RPS Generator akan langsung memakai CPL &amp; Bahan Kajian dari data terstruktur terverifikasi
      </span>
    </div>

    <div class="kuri-table-wrapper">
      <table class="kuri-table">
        <thead>
          <tr>
            <th style="width:40px; text-align:center;">No</th>
            <th style="width:200px;">Program Studi</th>
            <th>Dokumen Kurikulum &amp; Status Data Terstruktur</th>
            <th style="width:1%; white-space:nowrap; text-align:right; padding-right:18px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($kurikulum_list)): ?>
            <?php $no = 1; foreach ($kurikulum_list as $k): ?>
              <?php
                $norm_status = !empty($k->normalization_status) ? $k->normalization_status : 'NOT_NORMALIZED';
              ?>
              <tr id="row_kuri_<?= $k->id; ?>">
                <td style="text-align:center; font-weight:600; color:#94a3b8;"><?= $no++; ?></td>
                <td>
                  <strong style="color:#0f172a; font-size:13.5px;"><?= htmlspecialchars($k->nama_prodi); ?></strong><br>
                  <span class="kuri-badge info" style="margin-top:4px;">
                    <i class="fa fa-link" style="margin-right:3px;"></i> Terhubung ke Studio RPS
                  </span>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
                    <span style="font-weight:700; color:#1e293b; font-size:13.5px;"><?= htmlspecialchars($k->judul_dokumen); ?></span>
                    <?php if (!empty($k->tahun_kurikulum)): ?>
                      <span class="kuri-badge purple" style="font-size:11px; padding:2px 7px;">Tahun <?= htmlspecialchars($k->tahun_kurikulum); ?></span>
                    <?php endif; ?>
                    
                    <!-- Status Normalisasi Badge -->
                    <span id="badge_status_<?= $k->id; ?>">
                      <?php if ($norm_status === 'VERIFIED'): ?>
                        <span class="kuri-badge success" style="font-weight:700; font-size:11.5px; padding:3px 8px;" title="Dokumen telah diverifikasi resmi oleh Admin">
                          <i class="fa fa-check-circle" style="margin-right:4px;"></i> VERIFIED
                        </span>
                      <?php elseif ($norm_status === 'NORMALIZED'): ?>
                        <span class="kuri-badge info" style="font-weight:700; font-size:11.5px; padding:3px 8px; background:#e0f2fe; color:#0369a1;" title="Telah dinormalisasi, menunggu verifikasi eksplisit admin">
                          <i class="fa fa-cogs" style="margin-right:4px;"></i> NORMALIZED
                        </span>
                      <?php elseif ($norm_status === 'STALE'): ?>
                        <span class="kuri-badge" style="font-weight:700; font-size:11.5px; padding:3px 8px; background:#fef3c7; color:#b45309;" title="Source file berubah sejak normalisasi terakhir">
                          <i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i> STALE
                        </span>
                      <?php else: ?>
                        <span class="kuri-badge" style="font-weight:600; font-size:11px; padding:3px 8px; background:#f1f5f9; color:#64748b;" title="Belum dilakukan normalisasi terstruktur">
                          <i class="fa fa-hourglass-start" style="margin-right:4px;"></i> Belum Dinormalisasi
                        </span>
                      <?php endif; ?>
                    </span>
                  </div>

                  <div style="font-size:12px; color:#64748b; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span><i class="fa fa-file-pdf-o text-danger"></i> <?= htmlspecialchars($k->file_name ?: 'Berkas Kurikulum'); ?></span>
                    <span style="color:#cbd5e1;">•</span>
                    <span><i class="fa fa-clock-o text-muted"></i> <?= date('d M Y, H:i', strtotime($k->updated_at ?: $k->created_at)); ?> WIB</span>
                    <?php if (!empty($k->source_hash)): ?>
                      <span style="color:#cbd5e1;">•</span>
                      <span title="SHA-256: <?= htmlspecialchars($k->source_hash); ?>" style="font-family:monospace; font-size:11px; background:#f1f5f9; padding:1px 6px; border-radius:4px; color:#475569;">
                        hash:<?= substr($k->source_hash, 0, 8); ?>...
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td style="text-align:right; white-space:nowrap; padding-right:18px;">
                  <div style="display:inline-flex; align-items:center; justify-content:flex-end; gap:5px; flex-wrap:wrap;">
                    
                    <!-- 1. Download PDF -->
                    <?php if (!empty($k->file_path_doc) && file_exists(FCPATH . $k->file_path_doc)): ?>
                      <a href="<?= site_url('C_kurikulum/download/' . $k->id); ?>" class="btn btn-primary btn-xs" title="Unduh Berkas PDF Kurikulum" style="font-weight:600; font-size:11.5px; border-radius:6px; padding:5px 9px; display:inline-flex; align-items:center; gap:4px; background:#2563eb; border-color:#1d4ed8; color:#ffffff;">
                        <i class="fa fa-download"></i> PDF
                      </a>
                    <?php endif; ?>

                    <!-- 2. Lihat Readiness Kurikulum (Modal Data Terstruktur) -->
                    <button type="button" class="btn btn-info btn-xs" onclick="lihatHasilNormalisasi(<?= $k->id; ?>)" title="Lihat Ringkasan Readiness Kurikulum Terstruktur (CPL, BK, Matriks)" style="font-weight:600; font-size:11.5px; border-radius:6px; padding:5px 9px; display:inline-flex; align-items:center; gap:4px; background:#0284c7; border-color:#0369a1; color:#ffffff;">
                      <i class="fa fa-sitemap"></i> Readiness Kurikulum
                    </button>

                    <!-- 3. Teks Markdown Raw -->
                    <button type="button" class="btn btn-default btn-xs" onclick="lihatTeksKurikulum(<?= $k->id; ?>)" title="Lihat Naskah Teks Mentah" style="font-weight:600; font-size:11.5px; border-radius:6px; padding:5px 8px; display:inline-flex; align-items:center; gap:3px; border-color:#cbd5e1;">
                      <i class="fa fa-file-text-o text-muted"></i> Teks
                    </button>

                    <!-- 4. Admin Actions (Khusus gege.satya@wbi.ac.id) -->
                    <?php if (!empty($is_gege)): ?>
                      <div class="btn-group" style="display:inline-block;">
                        <button type="button" class="btn btn-warning btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight:700; font-size:11.5px; border-radius:6px; padding:5px 8px; background:#f59e0b; border-color:#d97706; color:#ffffff;" title="Menu Aksi Administrator">
                          <i class="fa fa-cog"></i> Admin <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" style="font-size:12.5px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:6px 0; border:1px solid #e2e8f0; min-width:240px; z-index:9999; right:0; left:auto;">
                          
                          <!-- Normalisasi Kurikulum -->
                          <li>
                            <a href="javascript:;" onclick="normalisasiKurikulum(<?= $k->id; ?>)" style="padding:7px 16px; color:#0f766e; font-weight:600;">
                              <i class="fa fa-cogs fa-fw" style="color:#0f766e;"></i> 1. Normalisasi Kurikulum
                            </a>
                          </li>

                          <!-- Verifikasi Pustaka (Eksplisit) -->
                          <li>
                            <a href="javascript:;" onclick="verifikasiKurikulum(<?= $k->id; ?>)" style="padding:7px 16px; color:#16a34a; font-weight:600;">
                              <i class="fa fa-check-square-o fa-fw" style="color:#16a34a;"></i> 2. Verifikasi Pustaka
                            </a>
                          </li>

                          <!-- Bangun Ulang Data Terstruktur -->
                          <li>
                            <a href="javascript:;" onclick="rebuildKurikulum(<?= $k->id; ?>)" style="padding:7px 16px; color:#d97706; font-weight:600;">
                              <i class="fa fa-refresh fa-fw" style="color:#d97706;"></i> 3. Bangun Ulang Data Terstruktur
                            </a>
                          </li>

                          <li role="separator" class="divider" style="margin:4px 0;"></li>

                          <!-- Hapus Dokumen -->
                          <li>
                            <a href="javascript:;" onclick="hapusKurikulum(<?= $k->id; ?>, '<?= htmlspecialchars(addslashes($k->nama_prodi)); ?>')" style="padding:7px 16px; color:#dc2626; font-weight:600;">
                              <i class="fa fa-trash fa-fw" style="color:#dc2626;"></i> Hapus Dokumen
                            </a>
                          </li>
                        </ul>
                      </div>
                    <?php endif; ?>

                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" style="text-align:center; padding:40px 20px; color:#94a3b8;">
                <i class="fa fa-folder-open-o" style="font-size:40px; margin-bottom:10px; display:block;"></i>
                Belum ada dokumen kurikulum yang diunggah.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: LIHAT READINESS KURIKULUM (SCHEMA V1.0)                            -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalNormalizedSummary" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:92%; max-width:1150px;">
    <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background:#0f172a; color:#ffffff; padding:18px 24px; border-bottom:1px solid #334155;">
        <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8; font-size:24px;">&times;</button>
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
          <h4 class="modal-title" style="font-weight:800; font-size:17px; margin:0; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-sitemap text-info"></i> Ringkasan Readiness Kurikulum Terstruktur
          </h4>
          <span id="norm_header_badge" class="kuri-badge" style="font-size:11.5px; padding:3px 9px;"></span>
        </div>
        <div id="norm_header_meta" style="font-size:12px; color:#94a3b8; margin-top:6px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
          <span>Program Studi: <strong id="norm_header_prodi" style="color:#f8fafc;">-</strong></span>
          <span>•</span>
          <span>Tahun Kurikulum: <strong id="norm_header_tahun" style="color:#f8fafc;">-</strong></span>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="modal-body" style="padding:0; background:#f8fafc; max-height:calc(85vh - 140px); overflow-y:auto;">
        
        <!-- Loading State -->
        <div id="norm_loading" style="padding:60px 20px; text-align:center;">
          <div style="font-size:36px; color:#0284c7; margin-bottom:14px;">
            <i class="fa fa-circle-o-notch fa-spin"></i>
          </div>
          <h4 style="font-weight:700; color:#0f172a; margin:0 0 6px 0;">Membaca Payload Terstruktur...</h4>
          <p style="color:#64748b; font-size:13px; margin:0;">Memvalidasi integritas CPL, Bahan Kajian, dan Pemetaan Matriks.</p>
        </div>

        <!-- Empty / Not Normalized State -->
        <div id="norm_empty_state" style="display:none; padding:50px 30px; text-align:center;">
          <div style="width:64px; height:64px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:inline-flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:16px;">
            <i class="fa fa-database"></i>
          </div>
          <h4 style="font-weight:800; color:#0f172a; margin-bottom:8px;">Dokumen Belum Dinormalisasi</h4>
          <p style="color:#64748b; font-size:13px; max-width:550px; margin:0 auto 20px auto;">
            Dokumen ini belum memiliki data terstruktur (Schema v1.0). Jalankan normalisasi kurikulum untuk mengekstrak official CPL, Bahan Kajian, dan matriks pemetaan secara deterministik.
          </p>
          <?php if (!empty($is_gege)): ?>
            <button type="button" class="btn btn-primary" id="btn_modal_empty_normalize" style="font-weight:700; border-radius:8px; padding:9px 20px; background:#0f766e; border-color:#0f766e;">
              <i class="fa fa-cogs"></i> Jalankan Normalisasi Sekarang
            </button>
          <?php endif; ?>
        </div>

        <!-- Loaded Content State -->
        <div id="norm_content_view" style="display:none;">
          
          <!-- Validation Alert Banner (Hidden by request) -->
          <div id="norm_validation_banner" style="display:none; margin:16px 24px 0 24px; padding:12px 18px; border-radius:8px;"></div>

          <!-- Key Metrics Grid -->
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; padding:16px 24px;">
            
            <div style="background:#ffffff; padding:14px 16px; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">Official CPL</div>
              <div id="stat_official_cpl" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:2px;">0</div>
              <div id="stat_cpl_breakdown" style="font-size:11px; color:#64748b; margin-top:2px;">S:0 · P:0 · KU:0 · KK:0</div>
            </div>

            <div style="background:#ffffff; padding:14px 16px; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">Bahan Kajian (BK)</div>
              <div id="stat_bk_count" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:2px;">0</div>
              <div id="stat_bk_subtext" style="font-size:11px; color:#64748b; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Body of Knowledge</div>
            </div>

            <div style="background:#ffffff; padding:14px 16px; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">Pemetaan CPL↔BK</div>
              <div id="stat_cpl_bk_status" style="font-size:16px; font-weight:800; color:#0f172a; margin-top:6px;">-</div>
              <div id="stat_cpl_bk_count" style="font-size:11px; color:#64748b; margin-top:2px;">-</div>
            </div>

            <div style="background:#ffffff; padding:14px 16px; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">Pemetaan CPL↔MK</div>
              <div id="stat_cpl_mk_pairs" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:2px;">0</div>
              <div style="font-size:11px; color:#64748b; margin-top:2px;">Pasangan Matriks Langsung</div>
            </div>

            <div style="background:#ffffff; padding:14px 16px; border-radius:10px; border:1px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
              <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">Jumlah Mata Kuliah</div>
              <div id="stat_mk_count" style="font-size:24px; font-weight:800; color:#0f172a; margin-top:2px;">0</div>
              <div id="stat_sks_summary" style="font-size:11px; color:#64748b; margin-top:2px;">- SKS Total</div>
            </div>

          </div>

          <!-- Navigation Tabs -->
          <div style="padding:0 24px; background:#ffffff; border-bottom:1px solid #e2e8f0;">
            <ul class="nav nav-tabs" style="border-bottom:none; font-weight:700; font-size:13px;">
              <li class="active">
                <a href="#tab_cpl" data-toggle="tab" style="padding:12px 18px; border-radius:8px 8px 0 0;">
                  <i class="fa fa-list-alt text-primary"></i> 1. Official CPL (<span id="tab_badge_cpl">0</span>)
                </a>
              </li>
              <li>
                <a href="#tab_bk" data-toggle="tab" style="padding:12px 18px; border-radius:8px 8px 0 0;">
                  <i class="fa fa-tags text-success"></i> 2. Bahan Kajian (<span id="tab_badge_bk">0</span>)
                </a>
              </li>
              <li>
                <a href="#tab_cpl_bk" data-toggle="tab" style="padding:12px 18px; border-radius:8px 8px 0 0;">
                  <i class="fa fa-exchange text-warning"></i> 3. Mapping CPL-BK (<span id="tab_badge_cpl_bk">0</span>)
                </a>
              </li>
              <li>
                <a href="#tab_cpl_mk" data-toggle="tab" style="padding:12px 18px; border-radius:8px 8px 0 0;">
                  <i class="fa fa-table text-info"></i> 4. Mapping CPL-MK (<span id="tab_badge_mk">0</span>)
                </a>
              </li>
              <li class="pull-right" style="margin-top:4px;">
                <a href="<?= site_url('C_matriks_mk_cpl'); ?>" class="btn btn-xs btn-default" style="font-weight:700; color:#0f766e; border-color:#0f766e; background:#f0fdfa; border-radius:6px; padding:6px 12px;">
                  <i class="fa fa-th-large"></i> Buka Menu Matriks MK-CPL &rarr;
                </a>
              </li>
              <!-- Tab 6: Validasi & Raw JSON (Hidden by request) -->
              <li style="display:none;">
                <a href="#tab_validation" data-toggle="tab" style="padding:12px 18px; border-radius:8px 8px 0 0;">
                  <i class="fa fa-shield text-danger"></i> 6. Validasi &amp; Raw JSON
                </a>
              </li>
            </ul>
          </div>

          <!-- Tab Content Panes -->
          <div class="tab-content" style="padding:20px 24px; background:#f8fafc;">
            
            <!-- TAB 1: OFFICIAL CPL -->
            <div class="tab-pane active" id="tab_cpl">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; gap:6px; flex-wrap:wrap;" id="cpl_filter_pills">
                  <button type="button" class="btn btn-xs btn-primary active" onclick="filterCplCategory('ALL', this)" style="border-radius:6px; font-weight:700; padding:4px 10px;">Semua</button>
                  <button type="button" class="btn btn-xs btn-default" onclick="filterCplCategory('S', this)" style="border-radius:6px; font-weight:600; padding:4px 10px;">Sikap (S)</button>
                  <button type="button" class="btn btn-xs btn-default" onclick="filterCplCategory('P', this)" style="border-radius:6px; font-weight:600; padding:4px 10px;">Pengetahuan (P)</button>
                  <button type="button" class="btn btn-xs btn-default" onclick="filterCplCategory('KU', this)" style="border-radius:6px; font-weight:600; padding:4px 10px;">Keterampilan Umum (KU)</button>
                  <button type="button" class="btn btn-xs btn-default" onclick="filterCplCategory('KK', this)" style="border-radius:6px; font-weight:600; padding:4px 10px;">Keterampilan Khusus (KK)</button>
                </div>
                <div>
                  <input type="text" id="cpl_search_input" onkeyup="searchCplTable()" placeholder="Cari kode atau rumusan CPL..." class="form-control input-sm" style="width:240px; border-radius:6px;">
                </div>
              </div>
              <div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0;">
                <table class="table table-hover" id="table_official_cpl" style="margin-bottom:0; font-size:12.5px;">
                  <thead>
                    <tr style="background:#f8fafc; color:#475569;">
                      <th style="width:60px; text-align:center;">No</th>
                      <th style="width:110px;">Kode CPL</th>
                      <th style="width:130px;">Kategori</th>
                      <th>Rumusan Capaian Pembelajaran Lulusan (CPL)</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_official_cpl">
                    <!-- Dynamic Rows -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 2: BAHAN KAJIAN (BK) -->
            <div class="tab-pane" id="tab_bk">
              <div id="bk_diagnostic_alert" style="margin-bottom:14px;"></div>
              <div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0;">
                <table class="table table-hover" id="table_bk" style="margin-bottom:0; font-size:12.5px;">
                  <thead>
                    <tr style="background:#f8fafc; color:#475569;">
                      <th style="width:50px; text-align:center;">No</th>
                      <th style="width:100px;">Kode BK</th>
                      <th style="width:100px;">Kode Sumber</th>
                      <th style="width:260px;">Nama Bahan Kajian</th>
                      <th style="width:160px; text-align:center;">Fidelity &amp; Status</th>
                      <th>Deskripsi / Ruang Lingkup</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_bk">
                    <!-- Dynamic Rows -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 3: PEMETAAN CPL-BK -->
            <div class="tab-pane" id="tab_cpl_bk">
              <div id="cpl_bk_info_alert" style="margin-bottom:14px;"></div>
              <div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0;">
                <table class="table table-hover" id="table_cpl_bk" style="margin-bottom:0; font-size:12.5px;">
                  <thead>
                    <tr style="background:#f8fafc; color:#475569;">
                      <th style="width:60px; text-align:center;">No</th>
                      <th style="width:140px;">Kode CPL</th>
                      <th style="width:140px;">Kode BK Terhubung</th>
                      <th>Detail Deskripsi Pasangan</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_cpl_bk">
                    <!-- Dynamic Rows -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 4: PEMETAAN CPL-MK -->
            <div class="tab-pane" id="tab_cpl_mk">
              <div style="margin-bottom:12px; font-size:12.5px; color:#64748b; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <span>Pemetaan CPL ke Mata Kuliah (Effective Grounding &amp; Provenance):</span>
                <span id="mk_total_badge" class="kuri-badge info">0 Pasangan Terpetakan</span>
              </div>
              <div class="table-responsive" style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0;">
                <table class="table table-hover" id="table_mk" style="margin-bottom:0; font-size:12.5px;">
                  <thead>
                    <tr style="background:#f8fafc; color:#475569;">
                      <th style="width:40px; text-align:center;">No</th>
                      <th style="width:90px;">Kode MK</th>
                      <th style="width:200px;">Nama Mata Kuliah</th>
                      <th style="width:70px; text-align:center;">Semester</th>
                      <th style="width:90px; text-align:center;">SKS (T-P-L)</th>
                      <th style="width:160px;">CPL Terpetakan</th>
                      <th style="width:130px; text-align:center;">Domain Aggregate</th>
                      <th style="width:140px; text-align:center;">Sumber Pemetaan</th>
                      <th>Jalur Pembuktian / Evidence</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_mk">
                    <!-- Dynamic Rows -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 5: VALIDASI & RAW JSON -->
            <div class="tab-pane" id="tab_validation">
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                
                <!-- Validation Report Card -->
                <div style="background:#ffffff; padding:18px 20px; border-radius:10px; border:1px solid #e2e8f0;">
                  <h4 style="font-weight:800; font-size:14px; color:#0f172a; margin:0 0 12px 0;">
                    <i class="fa fa-check-square-o text-success"></i> Status Validasi Integritas
                  </h4>
                  <div id="validation_checklist" style="font-size:12.5px; line-height:1.7;"></div>
                </div>

                <!-- Raw JSON & Actions -->
                <div style="background:#ffffff; padding:18px 20px; border-radius:10px; border:1px solid #e2e8f0;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <h4 style="font-weight:800; font-size:14px; color:#0f172a; margin:0;">
                      <i class="fa fa-code text-info"></i> Payload JSON Terstruktur
                    </h4>
                    <button type="button" class="btn btn-default btn-xs" onclick="copyNormalizedJson()" style="font-weight:600; border-radius:6px;">
                      <i class="fa fa-copy"></i> Salin JSON
                    </button>
                  </div>
                  <pre id="norm_raw_json" style="max-height:380px; overflow-y:auto; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; font-size:11px; font-family:monospace; color:#334155;"></pre>
                </div>

              </div>
            </div>

          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div id="modal_footer_info" style="font-size:12px; color:#64748b;">
          <i class="fa fa-shield text-success"></i> Standar Grounding OBE SN-Dikti 2026
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
          <?php if (!empty($is_gege)): ?>
            <!-- Bangun Ulang -->
            <button type="button" class="btn btn-warning btn-sm" id="btn_modal_rebuild" onclick="modalRebuildClick()" style="font-weight:700; border-radius:6px; background:#d97706; border-color:#b45309; color:#ffffff;">
              <i class="fa fa-refresh"></i> Bangun Ulang Data
            </button>

            <!-- Verifikasi Eksplisit -->
            <button type="button" class="btn btn-success btn-sm" id="btn_modal_verify" onclick="modalVerifyClick()" style="font-weight:700; border-radius:6px; background:#16a34a; border-color:#15803d; color:#ffffff;">
              <i class="fa fa-check-circle"></i> Verifikasi Pustaka Ini (VERIFIED)
            </button>
          <?php endif; ?>
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Tutup</button>
        </div>
      </div>

    </div>
  </div>
</div>


<!-- ========================================================================= -->
<!-- MODAL: UPLOAD KURIKULUM BARU -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalUploadKurikulum" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius:12px; overflow:hidden;">
      <div class="modal-header" style="background:#0f766e; color:#ffffff; padding:16px 20px;">
        <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
        <h4 class="modal-title" style="font-weight:700; font-size:16px;">
          <i class="fa fa-cloud-upload"></i> Unggah / Perbarui Dokumen Kurikulum Prodi
        </h4>
      </div>
      <form id="formUploadKurikulum" enctype="multipart/form-data">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="modal-body" style="padding:20px 24px;">
          <div class="form-group">
            <label style="font-weight:700; color:#0f172a; font-size:12.5px;">Program Studi <span class="text-danger">*</span></label>
            <select name="nama_prodi" id="upload_nama_prodi" class="form-control" required style="border-radius:6px; font-weight:600;">
              <option value="">— Pilih Program Studi —</option>
              <?php foreach ($prodi_list as $pr): ?>
                <option value="<?= htmlspecialchars($pr); ?>"><?= htmlspecialchars($pr); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label style="font-weight:700; color:#0f172a; font-size:12.5px;">Judul Dokumen</label>
            <input type="text" name="judul_dokumen" id="upload_judul" class="form-control" placeholder="Contoh: Dokumen Kurikulum & CPL TRPL" style="border-radius:6px;">
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
              <i class="fa fa-magic text-primary"></i> <em>Tahun kurikulum akan diekstrak otomatis oleh sistem dari berkas dokumen atau nama file.</em>
            </div>
          </div>

          <div class="form-group">
            <label style="font-weight:700; color:#0f172a; font-size:12.5px;">Berkas Dokumen (PDF / DOCX / MD / TXT) <span class="text-danger">*</span></label>
            <div class="kuri-dropzone" id="dropzone_upload">
              <input type="file" name="doc_file" id="upload_file_input" accept=".pdf,.docx,.txt,.md" required onchange="previewSelectedFile(this)">
              <i class="fa fa-file-text-o" id="dropzone_icon" style="font-size:32px; color:#0f766e; margin-bottom:8px; display:block;"></i>
              <div id="dropzone_text" style="font-weight:600; color:#334155; font-size:13px;">
                Klik atau seret file PDF / DOCX Kurikulum ke sini
              </div>
              <div style="font-size:11.5px; color:#94a3b8; margin-top:4px;">
                Maksimal 25 MB (Teks akan diekstrak otomatis untuk CPL &amp; RPS)
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 20px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Batal</button>
          <button type="submit" class="btn btn-primary" id="btn_submit_upload" style="background:#0f766e; border-color:#0f766e; font-weight:700; border-radius:6px; padding:8px 18px;">
            <i class="fa fa-save"></i> Unggah &amp; Ekstrak Teks
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: LIHAT TEKS KURIKULUM -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewTeks" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:90%; max-width:950px;">
    <div class="modal-content" style="border-radius:12px; overflow:hidden;">
      <div class="modal-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:14px 20px;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="view_teks_title" style="font-weight:700; font-size:15px; color:#0f172a;">
          <i class="fa fa-file-text-o text-primary"></i> Isi Naskah Dokumen Kurikulum
        </h4>
      </div>
      <div class="modal-body" style="padding:16px 20px; max-height:calc(80vh - 120px); overflow-y:auto; background:#ffffff;">
        <pre id="view_teks_content" style="white-space:pre-wrap; word-wrap:break-word; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; font-size:12px; line-height:1.6; max-height:550px; overflow-y:auto;"></pre>
      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:10px 20px; display:flex; justify-content:space-between; align-items:center;">
        <span id="view_teks_meta" style="font-size:12px; color:#64748b;">Total: 0 karakter</span>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: LAPORAN AUDIT KURIKULUM AI OBE -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAuditAi" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width:90%; max-width:1050px;">
    <div class="modal-content" style="border-radius:12px; overflow:hidden;">
      <div class="modal-header" style="background:#4f46e5; color:#ffffff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h4 class="modal-title" style="font-weight:800; font-size:16px; color:#ffffff; margin:0;">
            <i class="fa fa-magic"></i> Hasil Audit Kurikulum OBE AI
          </h4>
          <span id="audit_modal_subtitle" style="font-size:12px; color:#c7d2fe;">Standar SN-Dikti Permendiktisaintek No. 39/2025 jo No. 10/2026</span>
        </div>
        <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.9;">&times;</button>
      </div>
      <div class="modal-body" style="padding:20px 24px; max-height:calc(85vh - 130px); overflow-y:auto; background:#ffffff;">
        <!-- Loading State -->
        <div id="audit_loading" style="text-align:center; padding:50px 20px;">
          <div style="font-size:36px; color:#4f46e5; margin-bottom:16px;">
            <i class="fa fa-circle-o-notch fa-spin"></i>
          </div>
          <h4 style="font-weight:700; color:#0f172a; margin-bottom:6px;" id="audit_loading_title">
            AI Sedang Menganalisis Kepatuhan Kurikulum...
          </h4>
          <p style="color:#64748b; font-size:13px; max-width:500px; margin:0 auto;">
            Mengevaluasi Profil Lulusan, Taksonomi Bloom C4–C6 rumusan CPL, Bahan Kajian, dan Distribusi SKS sesuai SN-Dikti 2026.
          </p>
        </div>

        <!-- Rendered Output -->
        <div id="audit_output" style="display:none;" class="audit-markdown-view">
          <div id="audit_rendered_content"></div>
        </div>
      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 20px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:12px; color:#64748b;">
          <i class="fa fa-shield text-success"></i> Evaluasi Penjaminan Mutu Internal (PPEPP)
        </span>
        <div style="display:flex; gap:8px;">
          <button type="button" class="btn btn-default btn-sm" id="btn_salin_audit" onclick="salinHasilAudit()" style="font-weight:600;">
            <i class="fa fa-copy"></i> Salin Naskah
          </button>
          <button type="button" class="btn btn-default btn-sm" id="btn_cetak_audit" onclick="cetakHasilAudit()" style="font-weight:600;">
            <i class="fa fa-print"></i> Cetak Laporan
          </button>
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600;">Tutup</button>
        </div>
      </div>
    </div>
  </div>
</div>

