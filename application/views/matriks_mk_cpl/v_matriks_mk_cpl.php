<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
  .matrix-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    background: #f8fafc;
    min-height: calc(100vh - 120px);
    padding: 20px 24px;
    margin: -15px -20px -10px -20px;
  }

  .matrix-header {
    background: #ffffff;
    padding: 20px 24px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
  }

  .matrix-title-wrap h3 {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .matrix-title-wrap p {
    font-size: 12.5px;
    color: #64748b;
    margin: 0;
  }

  /* Prodi Selector Tabs */
  .matrix-prodi-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .btn-prodi-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
  }

  .btn-prodi-pill:hover {
    border-color: #0f766e;
    background: #f0fdfa;
    color: #0f766e;
  }

  .btn-prodi-pill.active {
    background: #0f766e;
    border-color: #0f766e;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
  }

  .btn-prodi-pill .pill-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 700;
  }

  .btn-prodi-pill.active .pill-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }

  .btn-prodi-pill:not(.active) .pill-badge.verified {
    background: #dcfce7;
    color: #15803d;
  }

  .btn-prodi-pill:not(.active) .pill-badge.unverified {
    background: #f1f5f9;
    color: #64748b;
  }

  /* Stats Grid */
  .matrix-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
  }

  .matrix-stat-card {
    background: #ffffff;
    padding: 16px 18px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
  }

  .matrix-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
  }

  .matrix-stat-icon.teal { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
  .matrix-stat-icon.blue { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
  .matrix-stat-icon.purple { background: #f5f3ff; color: #7c3aed; border: 1px solid #ede9fe; }
  .matrix-stat-icon.green { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }

  .matrix-stat-num {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
  }

  .matrix-stat-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-top: 2px;
  }

  /* Main Card */
  .matrix-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 22px 24px;
    margin-bottom: 20px;
  }

  .cpl-tag-pill {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 700;
    margin-right: 3px;
    margin-bottom: 3px;
    padding: 2px 7px;
    border-radius: 4px;
    font-family: monospace;
    cursor: default;
    transition: transform 0.1s ease;
  }

  .cpl-tag-pill:hover {
    transform: translateY(-1px);
  }

  .cpl-tag-pill.cat-s  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .cpl-tag-pill.cat-p  { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
  .cpl-tag-pill.cat-ku { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
  .cpl-tag-pill.cat-kk { background: #fce7f3; color: #9d174d; border: 1px solid #fbcfe8; }
</style>

<div class="page-content matrix-wrapper">

  <!-- Header Bar -->
  <div class="matrix-header">
    <div class="matrix-title-wrap">
      <h3>
        <i class="fa fa-th-large text-primary" style="color:#0f766e;"></i>
        Matriks Mata Kuliah &amp; CPL
        <span class="label" style="background:#0f766e; color:#ffffff; font-size:11px; padding:3px 8px; border-radius:4px;">SN-Dikti 2026</span>
      </h3>
      <p>
        Pusat repositori pemetaan Capaian Pembelajaran Lulusan (CPL) ke seluruh Mata Kuliah per semester berdasarkan dokumen Kurikulum Terverifikasi.
      </p>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <a href="<?= site_url('C_kurikulum'); ?>" class="btn btn-default btn-sm" style="font-weight:600; border-radius:6px; font-size:12px;">
        <i class="fa fa-book"></i> Daftar Kurikulum
      </a>
      <a href="<?= site_url('C_rps_generator'); ?>" class="btn btn-primary btn-sm" style="background:#0f766e; border-color:#0f766e; font-weight:700; border-radius:6px; font-size:12px;">
        <i class="fa fa-magic"></i> Studio RPS Generator
      </a>
    </div>
  </div>

  <!-- Prodi Selector Pills -->
  <div class="matrix-prodi-pills">
    <?php foreach ($kurikulum_rows as $kr): ?>
      <?php
        $is_curr_active = ((int)$kr->id === (int)$active_id);
        $is_curr_verified = ($kr->normalization_status === 'VERIFIED');
      ?>
      <a href="javascript:void(0)" onclick="pilihProdiMatrix(<?= $kr->id; ?>, this)" class="btn-prodi-pill <?= $is_curr_active ? 'active' : ''; ?>" data-id="<?= $kr->id; ?>">
        <i class="fa fa-graduation-cap"></i>
        <span><?= htmlspecialchars($kr->nama_prodi); ?></span>
        <?php if ($is_curr_verified): ?>
          <span class="pill-badge verified"><i class="fa fa-check"></i> VERIFIED</span>
        <?php else: ?>
          <span class="pill-badge unverified">Draft / Pending</span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Container Dynamic Render -->
  <div id="matrix_content_container">
    
    <?php if (!empty($active_row) && !empty($is_verified) && !empty($parsed_data)): ?>
      <?php
        $p = $parsed_data;
        $mks = $p['mata_kuliah'] ?? [];
        $cpls = $p['official_cpl'] ?? [];
        $effective_pairs = $p['effective_cpl_mk_pair_count'] ?? (count($p['effective_cpl_mk_pairs'] ?? []));
        
        $total_sks = 0;
        foreach ($mks as $m) {
          $total_sks += (int)($m['sks_total'] ?? ($m['sks'] ?? 0));
        }
      ?>

      <!-- Stats Grid -->
      <div class="matrix-stats-grid">
        <div class="matrix-stat-card">
          <div class="matrix-stat-icon teal">
            <i class="fa fa-book"></i>
          </div>
          <div>
            <div class="matrix-stat-num" id="stat_total_mk"><?= count($mks); ?></div>
            <div class="matrix-stat-label">Total Mata Kuliah</div>
          </div>
        </div>

        <div class="matrix-stat-card">
          <div class="matrix-stat-icon blue">
            <i class="fa fa-calculator"></i>
          </div>
          <div>
            <div class="matrix-stat-num" id="stat_total_sks"><?= $total_sks; ?> SKS</div>
            <div class="matrix-stat-label">Beban Studi Terdistribusi</div>
          </div>
        </div>

        <div class="matrix-stat-card">
          <div class="matrix-stat-icon purple">
            <i class="fa fa-tags"></i>
          </div>
          <div>
            <div class="matrix-stat-num" id="stat_total_pairs"><?= $effective_pairs; ?></div>
            <div class="matrix-stat-label">Pemetaan Pasangan CPL-MK</div>
          </div>
        </div>

        <div class="matrix-stat-card">
          <div class="matrix-stat-icon green">
            <i class="fa fa-shield"></i>
          </div>
          <div>
            <div class="matrix-stat-num" style="font-size:15px; color:#15803d;">
              <i class="fa fa-check-circle"></i> VERIFIED
            </div>
            <div class="matrix-stat-label">Dokumen Kurikulum <?= htmlspecialchars($active_row->tahun_kurikulum ?: '-'); ?></div>
          </div>
        </div>
      </div>

      <!-- Main Card: Table per Semester -->
      <div class="matrix-card">
        
        <!-- Filter & Search Bar -->
        <div style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">
          <div style="display:flex; gap:6px; flex-wrap:wrap;" id="mk_sem_filter_pills">
            <!-- Dynamic semester filter buttons -->
          </div>
          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <button type="button" class="btn btn-default btn-sm" id="btn_toggle_desc_view" onclick="toggleModeRinciDeskripsi()" style="font-weight:700; font-size:12px; border-radius:6px; color:#334155; border-color:#cbd5e1; display:inline-flex; align-items:center; gap:6px;">
              <i class="fa fa-align-left text-primary"></i> <span id="lbl_toggle_desc">Tampilkan Deskripsi MK</span>
            </button>
            <input type="text" id="mk_search_input" onkeyup="searchMkMatrixTable()" placeholder="Cari kode atau nama mata kuliah..." class="form-control input-sm" style="width:230px; border-radius:6px;">
          </div>
        </div>

        <!-- Matrix Groups List -->
        <div id="mk_matrix_list_container">
          <!-- Dynamic semester groups rendered by JS -->
        </div>

      </div>

    <?php else: ?>
      
      <!-- Unverified / No Data Notice -->
      <div class="matrix-card" style="text-align:center; padding:50px 24px;">
        <div style="width:68px; height:68px; border-radius:50%; background:#fffbeb; border:1px solid #fde68a; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
          <i class="fa fa-clock-o" style="font-size:32px; color:#d97706;"></i>
        </div>
        <h4 style="font-weight:800; font-size:17px; color:#0f172a; margin:0 0 8px 0;">
          Kurikulum <?= htmlspecialchars($active_row->nama_prodi ?? 'Program Studi'); ?> Belum Terverifikasi Resmi
        </h4>
        <p style="font-size:13.5px; color:#64748b; max-width:580px; margin:0 auto 20px auto; line-height:1.55;">
          Data Matriks MK-CPL hanya disajikan untuk dokumen kurikulum yang telah melalui proses normalisasi terstruktur dan berstatus <strong>VERIFIED</strong>.
        </p>
        <?php if (!empty($is_gege)): ?>
          <a href="<?= site_url('C_kurikulum'); ?>" class="btn btn-warning btn-sm" style="font-weight:700; border-radius:6px; background:#d97706; border-color:#b45309; color:#ffffff;">
            <i class="fa fa-book"></i> Buka Kurikulum &amp; Verifikasi Dokumen
          </a>
        <?php endif; ?>
      </div>

    <?php endif; ?>

  </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL: DETAIL INFORMASI MATA KULIAH, DESKRIPSI & PEMETAAN CPL -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDetailMkCpl" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="max-width:850px; width:90%;">
    <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color:#ffffff; padding:18px 24px; display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <span class="label" id="modal_mk_kode" style="background:#0f766e; color:#ffffff; font-size:12px; font-weight:700; padding:3px 8px; border-radius:4px; font-family:monospace;">-</span>
            <span class="label" id="modal_mk_semester" style="background:rgba(255,255,255,0.15); color:#f8fafc; font-size:11px; padding:3px 8px; border-radius:4px;">-</span>
            <span class="label" id="modal_mk_sks" style="background:rgba(255,255,255,0.15); color:#f8fafc; font-size:11px; padding:3px 8px; border-radius:4px;">-</span>
          </div>
          <h4 class="modal-title" id="modal_mk_nama" style="font-weight:800; font-size:18px; color:#ffffff; margin:0;">
            Nama Mata Kuliah
          </h4>
        </div>
        <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8; font-size:24px;">&times;</button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body" style="padding:22px 24px; background:#f8fafc; max-height:calc(85vh - 140px); overflow-y:auto;">
        
        <!-- Section 1: Skor Keselarasan Kurikulum & Evidence Breakdown (Phase 1.7) -->
        <div style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; padding:18px 20px; margin-bottom:18px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            <h5 style="font-weight:800; font-size:13px; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; margin:0; display:flex; align-items:center; gap:8px;">
              <i class="fa fa-balance-scale text-primary" style="color:#0f766e;"></i> Audit Keselarasan Kurikulum (Curriculum Alignment Evidence)
            </h5>
            <div id="modal_mk_verdict_badge" style="display:inline-flex; align-items:center; gap:6px;">
              <span class="label label-success" style="font-size:12px; font-weight:700; padding:4px 10px; border-radius:6px;">Very Strong (100%)</span>
            </div>
          </div>

          <!-- Progress Bar Meter -->
          <div style="background:#f1f5f9; border-radius:8px; height:12px; overflow:hidden; margin-bottom:14px; border:1px solid #e2e8f0;">
            <div id="modal_mk_score_bar" style="width:100%; height:100%; background:linear-gradient(90deg, #0f766e 0%, #16a34a 100%); transition:width 0.4s ease;"></div>
          </div>

          <!-- Dual Evidence Cards: Direct Matrix (65 pts) & BK Path (35 pts) -->
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:12px; margin-bottom:14px;">
            <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; border:1px solid #e2e8f0;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <span style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;"><i class="fa fa-th text-primary"></i> 1. Direct CPL-MK Matrix</span>
                <span id="modal_ev_direct_score" style="font-size:11.5px; font-weight:800; color:#0f766e; background:#f0fdfa; padding:2px 8px; border-radius:4px; border:1px solid #ccfbf1;">65 / 65</span>
              </div>
              <div id="modal_ev_direct_status" style="font-size:11.5px; color:#334155; font-weight:600;">✓ Official Direct Matrix</div>
            </div>

            <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; border:1px solid #e2e8f0;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <span style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;"><i class="fa fa-cubes text-info"></i> 2. CPL-BK-MK Path</span>
                <span id="modal_ev_bk_score" style="font-size:11.5px; font-weight:800; color:#0369a1; background:#f0f9ff; padding:2px 8px; border-radius:4px; border:1px solid #e0f2fe;">35 / 35</span>
              </div>
              <div id="modal_ev_bk_status" style="font-size:11.5px; color:#334155; font-weight:600;">✓ All CPL Supported via BK</div>
            </div>
          </div>

          <p id="modal_mk_score_desc" style="font-size:12px; color:#475569; margin:0 0 12px 0; line-height:1.5;">
            Seluruh CPL yang dibebankan pada mata kuliah ini terjustifikasi selaras dengan ranah pembelajaran, modul keilmuan, dan internalisasi tata nilai kurikulum.
          </p>

          <!-- List Bahan Kajian Pendukung -->
          <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; border:1px solid #e2e8f0;">
            <div style="font-size:11.5px; font-weight:700; color:#334155; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
              <i class="fa fa-cubes text-info"></i> Bahan Kajian (Body of Knowledge) Terkait:
            </div>
            <div id="modal_mk_bks_container" style="display:flex; flex-wrap:wrap; gap:6px;">
              <span style="font-size:11px; color:#94a3b8; font-style:italic;">Memuat bahan kajian...</span>
            </div>
          </div>
        </div>

        <!-- Section 2: Deskripsi Mata Kuliah -->
        <div style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; padding:18px 20px; margin-bottom:18px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
          <h5 style="font-weight:800; font-size:13px; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; margin:0 0 8px 0; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-file-text-o text-primary" style="color:#0f766e;"></i> Deskripsi Mata Kuliah (Silabus &amp; Ruang Lingkup)
          </h5>
          <p id="modal_mk_deskripsi" style="font-size:13px; color:#334155; line-height:1.65; margin:0; background:#f8fafc; border-radius:8px; padding:12px 14px; border:1px solid #f1f5f9;">
            -
          </p>
        </div>

        <!-- Section 3: Pemetaan CPL Resmi & Status Keselarasan -->
        <div style="background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; padding:18px 20px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
            <h5 style="font-weight:800; font-size:13px; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; margin:0; display:flex; align-items:center; gap:8px;">
              <i class="fa fa-tags text-success" style="color:#16a34a;"></i> Pemetaan Capaian Pembelajaran Lulusan (CPL) &amp; Status Bukti
            </h5>
            <span class="badge" id="modal_mk_cpl_count" style="background:#f1f5f9; color:#475569; font-weight:700; font-size:11px; padding:4px 8px; border:1px solid #cbd5e1;">0 CPL Terpetakan</span>
          </div>

          <div class="table-responsive" style="border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
            <table class="table table-hover" style="margin-bottom:0; font-size:12.5px;">
              <thead>
                <tr style="background:#f8fafc; color:#475569; border-bottom:2px solid #cbd5e1;">
                  <th style="width:90px;">Kode CPL</th>
                  <th style="width:130px;">Kategori</th>
                  <th>Rumusan Capaian Pembelajaran Lulusan (CPL)</th>
                  <th style="width:190px;">Dukungan Bahan Kajian</th>
                </tr>
              </thead>
              <tbody id="modal_mk_cpl_tbody">
                <!-- Dynamic CPL Rows -->
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="background:#ffffff; border-top:1px solid #e2e8f0; padding:14px 24px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:12px; color:#64748b;">
          <i class="fa fa-shield text-success"></i> Standar Kurikulum SN-Dikti 2026
        </span>
        <div style="display:flex; gap:8px;">
          <a href="<?= site_url('C_rps_generator'); ?>" target="_blank" class="btn btn-primary btn-sm" style="background:#0f766e; border-color:#0f766e; font-weight:700; border-radius:6px;">
            <i class="fa fa-magic"></i> Susun di Studio RPS
          </a>
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="font-weight:600; border-radius:6px;">Tutup</button>
        </div>
      </div>

    </div>
  </div>
</div>

