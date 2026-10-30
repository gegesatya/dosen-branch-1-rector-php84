<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
  .wbi-maint-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    background: #f8fafc;
    min-height: calc(100vh - 120px);
    padding: 24px 28px 40px 28px;
    margin: -15px -20px -10px -20px;
  }

  .wbi-maint-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f766e 100%);
    border-radius: 14px;
    padding: 28px 32px;
    color: #ffffff;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.15);
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
  }

  .wbi-maint-hero::after {
    content: "";
    position: absolute;
    right: -40px;
    top: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(20, 184, 166, 0.18) 0%, rgba(20, 184, 166, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .wbi-maint-breadcrumb {
    font-size: 12px;
    color: #94a3b8;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .wbi-maint-breadcrumb a {
    color: #cbd5e1;
    text-decoration: none;
  }

  .wbi-maint-hero h2 {
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 8px 0;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .wbi-maint-badge-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 9999px;
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.4);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .wbi-maint-hero p {
    font-size: 13.5px;
    color: #cbd5e1;
    margin: 0;
    max-width: 780px;
    line-height: 1.5;
  }

  .wbi-maint-panel {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    padding: 24px 28px;
    margin-bottom: 24px;
  }

  .wbi-maint-status-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 20px;
  }

  .wbi-pulse-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    color: #b45309;
  }

  .wbi-pulse-dot {
    width: 10px;
    height: 10px;
    background: #f59e0b;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
    animation: wbiPulse 1.8s infinite;
  }

  @keyframes wbiPulse {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
    }
    70% {
      transform: scale(1);
      box-shadow: 0 0 0 8px rgba(245, 158, 11, 0);
    }
    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
    }
  }

  .wbi-maint-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 22px;
  }

  .wbi-maint-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
  }

  .wbi-maint-box-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  .wbi-maint-box-icon.amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
  .wbi-maint-box-icon.teal  { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
  .wbi-maint-box-icon.blue  { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

  .wbi-maint-box-title {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .wbi-maint-box-desc {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.4;
    margin: 0;
  }

  .wbi-maint-notice-banner {
    background: #f8fafc;
    border-left: 4px solid #0f766e;
    border-top: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 0 10px 10px 0;
    padding: 16px 20px;
    margin-bottom: 24px;
  }

  .wbi-maint-notice-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f766e;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .wbi-maint-notice-text {
    font-size: 13.5px;
    color: #334155;
    line-height: 1.55;
    margin: 0;
  }

  .wbi-quick-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
  }

  .wbi-nav-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 14px 16px;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #1e293b;
    transition: all 0.2s ease;
  }

  .wbi-nav-card:hover {
    border-color: #0f766e;
    background: #f0fdfa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.08);
  }

  .wbi-nav-card i {
    font-size: 18px;
    color: #0f766e;
  }

  .wbi-nav-card-text strong {
    display: block;
    font-size: 13px;
    color: #0f172a;
    font-weight: 700;
  }

  .wbi-nav-card-text span {
    font-size: 11.5px;
    color: #64748b;
  }
</style>

<div class="page-content wbi-maint-wrapper">

  <!-- Top Hero Header Bar -->
  <div class="wbi-maint-hero">
    <div class="wbi-maint-breadcrumb">
      <a href="<?= site_url('C_wall'); ?>"><i class="fa fa-home"></i> Beranda</a>
      <span>/</span>
      <span>Pusat Kurikulum &amp; RPS OBE</span>
      <span>/</span>
      <span style="color:#5eead4;">Status Pemeliharaan Sistem</span>
    </div>
    <h2>
      <i class="fa fa-cogs" style="color:#5eead4;"></i>
      Pemutakhiran Layanan Akademik
      <span class="wbi-maint-badge-pill">
        <i class="fa fa-clock-o"></i> Maintenance Window
      </span>
    </h2>
    <p>
      Layanan penyusunan dan pengelolaan <strong><?= htmlspecialchars($module_title ?? 'Kurikulum & RPS OBE'); ?></strong> saat ini sedang menjalani proses sinkronisasi dan optimalisasi sistem oleh Tim Akademik Politeknik Wilmar Bisnis Indonesia.
    </p>
  </div>

  <!-- Main Advisory Panel -->
  <div class="wbi-maint-panel">
    
    <!-- Top Status Bar -->
    <div class="wbi-maint-status-bar">
      <div class="wbi-pulse-indicator">
        <span class="wbi-pulse-dot"></span>
        <span>Status: Pemeliharaan Aktif — Akses Publik Ditutup Sementara</span>
      </div>
      <div style="font-size:12px; color:#64748b;">
        <i class="fa fa-university text-muted"></i> Standar Mutu Akademik SN-Dikti 2026
      </div>
    </div>

    <!-- Structured 3-Column Advisory Grid -->
    <div class="wbi-maint-grid">
      
      <div class="wbi-maint-box">
        <div class="wbi-maint-box-icon amber">
          <i class="fa fa-folder-open-o"></i>
        </div>
        <div>
          <div class="wbi-maint-box-title">Modul Terdampak</div>
          <p class="wbi-maint-box-desc">
            <?= htmlspecialchars($module_title ?? 'Kurikulum & Studio RPS OBE'); ?>
          </p>
        </div>
      </div>

      <div class="wbi-maint-box">
        <div class="wbi-maint-box-icon teal">
          <i class="fa fa-database"></i>
        </div>
        <div>
          <div class="wbi-maint-box-title">Agenda Pemutakhiran</div>
          <p class="wbi-maint-box-desc">
            Sinkronisasi CPL Prodi &amp; Grounding Faktual Matriks OBE
          </p>
        </div>
      </div>

      <div class="wbi-maint-box">
        <div class="wbi-maint-box-icon blue">
          <i class="fa fa-shield"></i>
        </div>
        <div>
          <div class="wbi-maint-box-title">Keamanan Data</div>
          <p class="wbi-maint-box-desc">
            Seluruh berkas &amp; draf tersimpan aman dan tidak terpengaruh
          </p>
        </div>
      </div>

    </div>

    <!-- Official Advisory Note -->
    <div class="wbi-maint-notice-banner">
      <div class="wbi-maint-notice-title">
        <i class="fa fa-bullhorn"></i> Informasi Resmi Administrator Akademik
      </div>
      <p class="wbi-maint-notice-text">
        <?= nl2br(htmlspecialchars($maintenance_message ?? 'Modul sedang dalam pemeliharaan rutin dan pembaruan sistem oleh Administrator Akademik. Layanan akan otomatis dibuka kembali setelah tahapan verifikasi selesai.')); ?>
      </p>
    </div>

    <!-- Helpful Quick Navigation Links -->
    <div style="margin-top: 10px;">
      <h4 style="font-size:13px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px; margin:0 0 12px 0;">
        <i class="fa fa-compass text-primary"></i> Navigasi Layanan Akademik Lainnya:
      </h4>
      <div class="wbi-quick-nav-grid">
        
        <a href="<?= site_url('C_wall'); ?>" class="wbi-nav-card">
          <i class="fa fa-dashboard"></i>
          <div class="wbi-nav-card-text">
            <strong>Dashboard Dosen</strong>
            <span>Kembali ke menu beranda utama</span>
          </div>
        </a>

        <a href="<?= site_url('C_jadwal'); ?>" class="wbi-nav-card">
          <i class="fa fa-calendar"></i>
          <div class="wbi-nav-card-text">
            <strong>Jadwal Perkuliahan</strong>
            <span>Presensi &amp; kelas mengajar</span>
          </div>
        </a>

        <a href="<?= site_url('Lecturer_dashboard_perwalian'); ?>" class="wbi-nav-card">
          <i class="fa fa-users"></i>
          <div class="wbi-nav-card-text">
            <strong>Bimbingan &amp; KRS</strong>
            <span>Persetujuan studi mahasiswa</span>
          </div>
        </a>

        <a href="<?= site_url('C_informasi_statistik'); ?>" class="wbi-nav-card">
          <i class="fa fa-bar-chart"></i>
          <div class="wbi-nav-card-text">
            <strong>Informasi &amp; Statistik</strong>
            <span>Rekapitulasi data akademik</span>
          </div>
        </a>

      </div>
    </div>

  </div>

  <!-- Footer Institutional Credit -->
  <div style="text-align:center; font-size:12px; color:#94a3b8; margin-top:10px;">
    Bagian Akademik &amp; Teknologi Informasi &bull; Politeknik Wilmar Bisnis Indonesia
  </div>

</div>
