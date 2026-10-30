<div class="page-content">
  <!-- STYLES -->
  <style>
    .setting-rps-wrapper {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color: #0f172a;
      padding: 6px 0 30px 0;
    }

    /* Top Breadcrumb Bar */
    .modern-breadcrumb {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 16px;
      padding: 0 2px;
    }
    .breadcrumb-trail {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: #64748b;
      margin: 0;
      padding: 0;
      list-style: none;
    }
    .breadcrumb-trail a {
      color: #0f766e;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.15s;
    }
    .breadcrumb-trail a:hover {
      color: #115e59;
      text-decoration: underline;
    }
    .breadcrumb-trail .separator {
      color: #cbd5e1;
      font-size: 11px;
    }
    .breadcrumb-trail .current-page {
      color: #334155;
      font-weight: 700;
    }

    /* Hero Header Card */
    .setting-hero-card {
      background: linear-gradient(135deg, #042f2e 0%, #0f766e 60%, #14b8a6 100%);
      color: #ffffff;
      border-radius: 14px;
      padding: 24px 28px;
      margin-bottom: 24px;
      box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.25), 0 8px 10px -6px rgba(15, 118, 110, 0.2);
      position: relative;
      overflow: hidden;
    }
    .setting-hero-card::after {
      content: "";
      position: absolute;
      right: -30px;
      bottom: -30px;
      width: 180px;
      height: 180px;
      background: rgba(255, 255, 255, 0.06);
      border-radius: 50%;
      pointer-events: none;
    }
    .hero-title {
      font-size: 22px;
      font-weight: 800;
      margin: 0 0 6px 0;
      display: flex;
      align-items: center;
      gap: 12px;
      letter-spacing: -0.02em;
    }
    .hero-subtitle {
      font-size: 13.5px;
      color: #ccfbf1;
      margin: 0;
      line-height: 1.5;
      max-width: 820px;
    }
    .hero-badge-pill {
      background: rgba(255, 255, 255, 0.16);
      border: 1px solid rgba(255, 255, 255, 0.28);
      color: #ffffff;
      font-size: 12px;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      backdrop-filter: blur(4px);
    }

    /* Section Cards */
    .config-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 24px 28px;
      margin-bottom: 24px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      transition: all 0.2s ease;
    }
    .config-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 18px;
      padding-bottom: 16px;
      border-bottom: 1px solid #f1f5f9;
    }
    .config-card-title {
      font-size: 17px;
      font-weight: 800;
      color: #0f172a;
      margin: 0 0 4px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      letter-spacing: -0.01em;
    }
    .config-card-desc {
      font-size: 13px;
      color: #64748b;
      margin: 0;
      line-height: 1.4;
    }

    /* Mode Grid & Interactive Boxes */
    .mode-selection-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
      gap: 18px;
      margin-bottom: 20px;
    }
    .mode-item-card {
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px;
      cursor: pointer;
      position: relative;
      transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
      background: #f8fafc;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .mode-item-card:hover {
      border-color: #0f766e;
      background: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 10px 20px -5px rgba(15, 118, 110, 0.12);
    }
    .mode-item-card.is-selected {
      border-color: #0f766e;
      background: #f0fdfa;
      box-shadow: 0 0 0 1px #0f766e, 0 8px 18px -4px rgba(15, 118, 110, 0.18);
    }
    .mode-item-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .mode-item-label {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .mode-item-desc {
      font-size: 12.5px;
      color: #475569;
      line-height: 1.55;
      margin: 0;
    }
    .mode-check-circle {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 2px solid #cbd5e1;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      background: #ffffff;
    }
    .mode-item-card.is-selected .mode-check-circle {
      border-color: #0f766e;
      background: #0f766e;
      color: #ffffff;
    }
    .mode-badge-tag {
      font-size: 10px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Maintenance Module Box */
    .maint-row-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 18px 22px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      transition: all 0.2s ease;
      margin-bottom: 14px;
    }
    .maint-row-box.locked-state {
      background: #fffdf5;
      border-color: #fde68a;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.08);
    }
    .maint-module-name {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
    }
    .maint-module-desc {
      font-size: 12.5px;
      color: #64748b;
      margin: 0;
      line-height: 1.45;
    }
    .maint-status-pill {
      font-size: 11px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .maint-status-pill.unlocked {
      background: #dcfce7;
      color: #15803d;
      border: 1px solid #bbf7d0;
    }
    .maint-status-pill.locked {
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
    }

    /* Buttons */
    .btn-action-primary {
      background: #0f766e;
      color: #ffffff;
      border: 1px solid #0f766e;
      font-weight: 700;
      font-size: 13px;
      padding: 9px 22px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
      transition: all 0.2s ease;
    }
    .btn-action-primary:hover {
      background: #115e59;
      border-color: #115e59;
      color: #ffffff;
      transform: translateY(-1px);
    }
    .btn-lock-toggle {
      font-weight: 700;
      font-size: 12.5px;
      padding: 8px 18px;
      border-radius: 8px;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-lock-toggle.lock {
      background: #f59e0b;
      color: #ffffff;
    }
    .btn-lock-toggle.lock:hover {
      background: #d97706;
      color: #ffffff;
    }
    .btn-lock-toggle.unlock {
      background: #10b981;
      color: #ffffff;
    }
    .btn-lock-toggle.unlock:hover {
      background: #059669;
      color: #ffffff;
    }
    .btn-outline-custom {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      color: #334155;
      font-weight: 600;
      font-size: 12px;
      padding: 7px 14px;
      border-radius: 7px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      transition: all 0.15s;
    }
    .btn-outline-custom:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      color: #0f172a;
    }
  </style>

  <div class="setting-rps-wrapper">

    <!-- BREADCRUMB -->
    <div class="modern-breadcrumb">
      <ul class="breadcrumb-trail">
        <li>
          <a href="<?= site_url('Dashboard'); ?>"><i class="fa fa-home"></i> Home</a>
        </li>
        <li class="separator"><i class="fa fa-angle-right"></i></li>
        <li>
          <span>Setting</span>
        </li>
        <li class="separator"><i class="fa fa-angle-right"></i></li>
        <li class="current-page">
          <span>Pengaturan RPS &amp; Maintenance</span>
        </li>
      </ul>
      <div class="hero-badge-pill" style="background:#0f766e; color:#ffffff; border-color:#0f766e; font-size:11.5px; padding:5px 12px;">
        <i class="fa fa-user-shield"></i> User Login: <?= html_escape($user_email); ?>
      </div>
    </div>

    <!-- HERO HEADER CARD -->
    <div class="setting-hero-card">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
          <h2 class="hero-title">
            <i class="fa fa-sliders"></i> Pengaturan Mode RPS &amp; Pemeliharaan Sistem
          </h2>
          <p class="hero-subtitle">
            Pusat kendali administrator untuk beralih antara <strong>Mode Menu RPS Lama vs Standar OBE 2026</strong> serta pengelolaan <strong>Status Maintenance (Kunci Akses)</strong> modul akademik.
          </p>
        </div>
        <div>
          <div class="hero-badge-pill">
            <i class="fa fa-lock"></i> Hak Akses Eksklusif Administrator
          </div>
        </div>
      </div>
    </div>

    <!-- CARD 1: PENGATURAN MODE TAMPILAN MENU RPS -->
    <div class="config-card">
      <div class="config-card-header">
        <div>
          <h3 class="config-card-title">
            <i class="fa fa-th-large text-primary" style="color:#0f766e;"></i>
            1. Mode Tampilan Menu RPS pada Sidebar
          </h3>
          <p class="config-card-desc">
            Pilih menu RPS mana yang akan dirender pada panel navigasi (sidebar) seluruh dosen dan pimpinan prodi.
          </p>
        </div>
        <div>
          <span id="badge_active_mode" class="maint-status-pill <?= ($rps_mode_detail['effective_mode'] === 'OBE_DEV') ? 'unlocked' : 'locked'; ?>" style="font-size:12px; padding:5px 12px;">
            <i class="fa fa-circle" style="font-size:8px;"></i>
            Status Aktif Saat Ini: <strong><?= $rps_mode_detail['effective_mode']; ?></strong> (<?= $rps_mode_detail['configured_mode'] === 'AUTO' ? 'Auto-Detect' : 'Manual Config'; ?>)
          </span>
        </div>
      </div>

      <!-- Mode Selector Grid -->
      <div class="mode-selection-grid">
        <!-- Option 1: AUTO -->
        <div class="mode-item-card <?= ($rps_mode_detail['configured_mode'] === 'AUTO') ? 'is-selected' : ''; ?>" onclick="pilihModeRps('AUTO')" id="box_mode_AUTO">
          <div>
            <div class="mode-item-top">
              <span class="mode-item-label">
                <i class="fa fa-magic text-info" style="color:#0284c7;"></i> 1. Mode Otomatis (AUTO)
              </span>
              <div class="mode-check-circle">
                <i class="fa fa-check" style="<?= ($rps_mode_detail['configured_mode'] === 'AUTO') ? '' : 'display:none;'; ?>"></i>
              </div>
            </div>
            <p class="mode-item-desc">
              <strong>Smart Auto-Detect:</strong> Otomatis menampilkan <em>RPS OBE Standar 2026</em> jika berada di server development / akun pengembang, dan menampilkan <em>RPS Lama</em> di server production.
            </p>
          </div>
          <div style="margin-top:14px;">
            <span class="mode-badge-tag" style="background:#e0f2fe; color:#0369a1;">Rekomendasi Dev &amp; Prod</span>
          </div>
        </div>

        <!-- Option 2: LEGACY -->
        <div class="mode-item-card <?= ($rps_mode_detail['configured_mode'] === 'LEGACY') ? 'is-selected' : ''; ?>" onclick="pilihModeRps('LEGACY')" id="box_mode_LEGACY">
          <div>
            <div class="mode-item-top">
              <span class="mode-item-label">
                <i class="fa fa-archive text-warning" style="color:#d97706;"></i> 2. RPS Lama (LEGACY)
              </span>
              <div class="mode-check-circle">
                <i class="fa fa-check" style="<?= ($rps_mode_detail['configured_mode'] === 'LEGACY') ? '' : 'display:none;'; ?>"></i>
              </div>
            </div>
            <p class="mode-item-desc">
              <strong>Standar Production:</strong> Mengunci tampilan sidebar hanya ke 3 menu format lama (<em>Daftar RPS</em>, <em>Tambah RPS</em>, <em>Daftar RPS Lama</em>). Modul OBE disembunyikan.
            </p>
          </div>
          <div style="margin-top:14px;">
            <span class="mode-badge-tag" style="background:#fef3c7; color:#92400e;">Mode Aman Dosen</span>
          </div>
        </div>

        <!-- Option 3: OBE_DEV -->
        <div class="mode-item-card <?= ($rps_mode_detail['configured_mode'] === 'OBE_DEV') ? 'is-selected' : ''; ?>" onclick="pilihModeRps('OBE_DEV')" id="box_mode_OBE_DEV">
          <div>
            <div class="mode-item-top">
              <span class="mode-item-label">
                <i class="fa fa-star text-success" style="color:#059669;"></i> 3. RPS OBE (Standar 2026)
              </span>
              <div class="mode-check-circle">
                <i class="fa fa-check" style="<?= ($rps_mode_detail['configured_mode'] === 'OBE_DEV') ? '' : 'display:none;'; ?>"></i>
              </div>
            </div>
            <p class="mode-item-desc">
              <strong>Standar OBE 2026:</strong> Menampilkan modul RPS OBE modern (<em>Daftar Kurikulum &amp; Audit CPL</em>, <em>Matriks MK-CPL</em>, <em>Daftar RPS OBE</em>, <em>RPS Generator AI</em>). Menu RPS format lama disembunyikan.
            </p>
          </div>
          <div style="margin-top:14px;">
            <span class="mode-badge-tag" style="background:#dcfce7; color:#15803d;">Full Features OBE</span>
          </div>
        </div>
      </div>

      <!-- Action Footer -->
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding-top:14px; border-top:1px solid #f1f5f9;">
        <div style="font-size:12px; color:#94a3b8;">
          <i class="fa fa-clock-o"></i> Terakhir diubah: <strong><?= html_escape($rps_mode_detail['updated_at']); ?></strong> oleh <strong><?= html_escape($rps_mode_detail['updated_by']); ?></strong>
        </div>
        <div>
          <button type="button" class="btn-action-primary" onclick="simpanModeRps()" id="btn_simpan_mode">
            <i class="fa fa-save"></i> Terapkan Mode Menu RPS
          </button>
        </div>
      </div>
    </div>

    <!-- CARD 2: KONTROL STATUS PEMELIHARAAN (MAINTENANCE CONTROL PANEL) -->
    <div class="config-card">
      <div class="config-card-header">
        <div>
          <h3 class="config-card-title">
            <i class="fa fa-shield text-warning" style="color:#d97706;"></i>
            2. Kontrol Status Pemeliharaan (Maintenance Lock)
          </h3>
          <p class="config-card-desc">
            Saat modul berstatus <strong>Terkunci (Maintenance)</strong>, dosen dan pengguna lain yang mengakses akan dialihkan ke halaman pemeliharaan, sementara akun Anda tetap dapat mengakses untuk pengujian.
          </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <button type="button" class="btn-outline-custom" onclick="bukaModalPesanMaintenance('all')">
            <i class="fa fa-bullhorn text-warning"></i> Kustomisasi Pesan Pengumuman
          </button>
          <?php $is_any_active = (!empty($is_maint_kurikulum) || !empty($is_maint_studio_rps)); ?>
          <button type="button" class="btn-lock-toggle <?= $is_any_active ? 'unlock' : 'lock'; ?>" onclick="toggleMaintenanceSemua(<?= $is_any_active ? 0 : 1; ?>)">
            <i class="fa <?= $is_any_active ? 'fa-unlock' : 'fa-lock'; ?>"></i> <?= $is_any_active ? 'Buka Semua Akses (Normal)' : 'Kunci Semua Modul (Maintenance)'; ?>
          </button>
        </div>
      </div>

      <?php if ($is_any_active): ?>
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:12.5px; color:#92400e; display:flex; align-items:center; gap:10px;">
          <i class="fa fa-exclamation-triangle" style="font-size:18px; color:#d97706;"></i>
          <div><strong>Perhatian:</strong> Mode Maintenance saat ini <strong>AKTIF</strong> pada satu atau lebih modul. Pengguna umum (Dosen &amp; Kaprodi) dialihkan ke notice pemeliharaan.</div>
        </div>
      <?php endif; ?>

      <!-- Module 1: Kurikulum & Audit CPL -->
      <div class="maint-row-box <?= !empty($is_maint_kurikulum) ? 'locked-state' : ''; ?>" id="box_maint_kurikulum">
        <div style="flex:1;">
          <div class="maint-module-name">
            <span>1. Modul Kurikulum &amp; Audit CPL</span>
            <?php if (!empty($is_maint_kurikulum)): ?>
              <span class="maint-status-pill locked" id="badge_kuri_status"><i class="fa fa-lock"></i> Terkunci (Maintenance)</span>
            <?php else: ?>
              <span class="maint-status-pill unlocked" id="badge_kuri_status"><i class="fa fa-check"></i> Terbuka (Normal)</span>
            <?php endif; ?>
          </div>
          <p class="maint-module-desc" id="desc_kuri_status">
            <?= !empty($is_maint_kurikulum) ? 'Akses publik ditutup. Hanya akun administrator yang dapat mengakses modul kurikulum.' : 'Dapat diakses normal oleh seluruh dosen dan pimpinan berwenang.'; ?>
          </p>
          <div style="font-size:11.5px; color:#94a3b8; margin-top:5px;">
            <i class="fa fa-comment-o"></i> <em>Pesan Aktif:</em> "<?= html_escape($maintenance_statuses['kurikulum']->message ?? 'Sedang dalam pemeliharaan.'); ?>"
          </div>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
          <button type="button" class="btn-outline-custom" onclick="bukaModalPesanMaintenance('kurikulum')">
            <i class="fa fa-pencil"></i> Edit Pesan
          </button>
          <?php if (!empty($is_maint_kurikulum)): ?>
            <button type="button" class="btn-lock-toggle unlock" onclick="toggleMaintenanceSingle('kurikulum', 0)">
              <i class="fa fa-unlock"></i> Buka Akses
            </button>
          <?php else: ?>
            <button type="button" class="btn-lock-toggle lock" onclick="toggleMaintenanceSingle('kurikulum', 1)">
              <i class="fa fa-lock"></i> Kunci Maintenance
            </button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Module 2: Studio RPS OBE & Generator -->
      <div class="maint-row-box <?= !empty($is_maint_studio_rps) ? 'locked-state' : ''; ?>" id="box_maint_studio_rps">
        <div style="flex:1;">
          <div class="maint-module-name">
            <span>2. Studio RPS OBE (Generator AI &amp; Master RPS)</span>
            <?php if (!empty($is_maint_studio_rps)): ?>
              <span class="maint-status-pill locked" id="badge_rps_status"><i class="fa fa-lock"></i> Terkunci (Maintenance)</span>
            <?php else: ?>
              <span class="maint-status-pill unlocked" id="badge_rps_status"><i class="fa fa-check"></i> Terbuka (Normal)</span>
            <?php endif; ?>
          </div>
          <p class="maint-module-desc" id="desc_rps_status">
            <?= !empty($is_maint_studio_rps) ? 'Akses generator & daftar RPS OBE ditutup untuk publik.' : 'Dapat diakses normal untuk menyusun dokumen RPS OBE standar 2026.'; ?>
          </p>
          <div style="font-size:11.5px; color:#94a3b8; margin-top:5px;">
            <i class="fa fa-comment-o"></i> <em>Pesan Aktif:</em> "<?= html_escape($maintenance_statuses['studio_rps']->message ?? 'Sedang dalam pemeliharaan.'); ?>"
          </div>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
          <button type="button" class="btn-outline-custom" onclick="bukaModalPesanMaintenance('studio_rps')">
            <i class="fa fa-pencil"></i> Edit Pesan
          </button>
          <?php if (!empty($is_maint_studio_rps)): ?>
            <button type="button" class="btn-lock-toggle unlock" onclick="toggleMaintenanceSingle('studio_rps', 0)">
              <i class="fa fa-unlock"></i> Buka Akses
            </button>
          <?php else: ?>
            <button type="button" class="btn-lock-toggle lock" onclick="toggleMaintenanceSingle('studio_rps', 1)">
              <i class="fa fa-lock"></i> Kunci Maintenance
            </button>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>
</div>

<!-- MODAL: KUSTOMISASI PESAN UNDER MAINTENANCE -->
<div class="modal fade" id="modalPesanMaintenance" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);">
      <div class="modal-header" style="background:#0f172a; color:#ffffff; padding:16px 22px;">
        <button type="button" class="close" data-dismiss="modal" style="color:#ffffff; opacity:0.8;">&times;</button>
        <h4 class="modal-title" style="font-size:16px; font-weight:700;">
          <i class="fa fa-bullhorn text-warning"></i> Kustomisasi Pesan Pengumuman Pemeliharaan
        </h4>
      </div>
      <div class="modal-body" style="padding:22px;">
        <input type="hidden" id="modal_maint_target_key" value="all">

        <div class="form-group" style="margin-bottom:16px;">
          <label style="font-weight:700; font-size:13px; color:#334155;">Target Modul:</label>
          <select id="modal_maint_select_module" class="form-control" style="border-radius:8px; height:38px;">
            <option value="all">Semua Modul (Kurikulum &amp; Studio RPS)</option>
            <option value="kurikulum">Hanya Modul Kurikulum &amp; Audit CPL</option>
            <option value="studio_rps">Hanya Modul Studio RPS OBE</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
          <label style="font-weight:700; font-size:13px; color:#334155;">Isi Pesan Pengumuman untuk Dosen:</label>
          <textarea id="modal_maint_textarea_msg" class="form-control" rows="4" style="border-radius:8px; font-size:13px; line-height:1.5;" placeholder="Ketik pesan yang akan ditampilkan saat dosen mengakses modul yang terkunci..."></textarea>
          <div style="font-size:11.5px; color:#94a3b8; margin-top:5px;">
            Pesan ini akan tampil di layar notice pemeliharaan bagi dosen yang membuka modul terkait.
          </div>
        </div>

        <!-- Quick Template Buttons -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
          <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">
            <i class="fa fa-bolt text-warning"></i> Template Cepat:
          </div>
          <div style="display:flex; flex-direction:column; gap:6px;">
            <button type="button" class="btn btn-default btn-xs text-left" onclick="setTemplatePesan(1)" style="font-size:11.5px; text-align:left; border-radius:6px; padding:6px 10px;">
              <i class="fa fa-wrench text-muted"></i> <strong>Pemeliharaan Rutin:</strong> "Modul sedang dalam pemeliharaan rutin dan peningkatan performa sistem."
            </button>
            <button type="button" class="btn btn-default btn-xs text-left" onclick="setTemplatePesan(2)" style="font-size:11.5px; text-align:left; border-radius:6px; padding:6px 10px;">
              <i class="fa fa-refresh text-muted"></i> <strong>Sinkronisasi Kurikulum:</strong> "Sedang berlangsung sinkronisasi data kurikulum &amp; CPL institusi standar SN-Dikti 2026."
            </button>
            <button type="button" class="btn btn-default btn-xs text-left" onclick="setTemplatePesan(3)" style="font-size:11.5px; text-align:left; border-radius:6px; padding:6px 10px;">
              <i class="fa fa-magic text-muted"></i> <strong>Pembaruan Studio RPS:</strong> "Studio RPS sedang dioptimalkan dengan model AI terbaru untuk penyusunan RPS yang lebih akurat."
            </button>
          </div>
        </div>

      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:14px 22px;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:8px; font-weight:600; padding:8px 16px;">Batal</button>
        <button type="button" class="btn-action-primary" onclick="simpanPesanMaintenanceModal()" style="padding:8px 20px;">
          <i class="fa fa-save"></i> Simpan Pesan
        </button>
      </div>
    </div>
  </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
  var _selectedRpsMode = '<?= $rps_mode_detail['configured_mode']; ?>';
  var _maintStatuses = <?= json_encode($maintenance_statuses ?? []); ?>;
  var _csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
  var _csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

  function pilihModeRps(mode) {
    _selectedRpsMode = mode;
    $('.mode-item-card').removeClass('is-selected');
    $('.mode-check-circle i').hide();

    $('#box_mode_' + mode).addClass('is-selected');
    $('#box_mode_' + mode + ' .mode-check-circle i').show();
  }

  function simpanModeRps() {
    var $btn = $('#btn_simpan_mode');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    var postData = {
      mode: _selectedRpsMode
    };
    postData[_csrfName] = _csrfHash;

    $.ajax({
      url: '<?= site_url("C_setting_rps/ajax_set_rps_mode"); ?>',
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: function(resp) {
        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Terapkan Mode Menu RPS');
        if (resp.status) {
          if (typeof toastr !== 'undefined') {
            toastr.success(resp.message, 'Berhasil Disimpan');
          } else {
            alert(resp.message);
          }
          setTimeout(function() {
            window.location.reload();
          }, 700);
        } else {
          alert(resp.message || 'Gagal mengubah mode RPS.');
        }
      },
      error: function(xhr, status, error) {
        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Terapkan Mode Menu RPS');
        alert('Terjadi kesalahan saat menyimpan mode RPS: ' + (xhr.responseText || error || 'Koneksi terputus.'));
      }
    });
  }

  function toggleMaintenanceSingle(moduleKey, isActive) {
    var actionText = isActive ? 'mengunci modul ke status Maintenance' : 'membuka akses modul secara Normal';
    if (!confirm('Apakah Anda yakin ingin ' + actionText + '?')) {
      return;
    }

    var postData = {
      module_key: moduleKey,
      is_active: isActive
    };
    postData[_csrfName] = _csrfHash;

    $.ajax({
      url: '<?= site_url("C_setting_rps/ajax_toggle_maintenance"); ?>',
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: function(resp) {
        if (resp.status) {
          if (typeof toastr !== 'undefined') {
            toastr.success(resp.message, 'Status Diperbarui');
          }
          setTimeout(function() {
            window.location.reload();
          }, 500);
        } else {
          alert(resp.message || 'Gagal memperbarui status maintenance.');
        }
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan saat memperbarui status: ' + (xhr.responseText || error || 'Jaringan error.'));
      }
    });
  }

  function toggleMaintenanceSemua(isActive) {
    var actionText = isActive ? 'mengunci SEMUA modul OBE ke status Maintenance' : 'membuka SEMUA modul OBE ke status Normal';
    if (!confirm('Apakah Anda yakin ingin ' + actionText + '?')) {
      return;
    }

    var postData = {
      module_key: 'all',
      is_active: isActive
    };
    postData[_csrfName] = _csrfHash;

    $.ajax({
      url: '<?= site_url("C_setting_rps/ajax_toggle_maintenance"); ?>',
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: function(resp) {
        if (resp.status) {
          if (typeof toastr !== 'undefined') {
            toastr.success(resp.message, 'Status Diperbarui');
          }
          setTimeout(function() {
            window.location.reload();
          }, 500);
        } else {
          alert(resp.message || 'Gagal memperbarui status maintenance.');
        }
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan saat memperbarui status: ' + (xhr.responseText || error || 'Jaringan error.'));
      }
    });
  }

  function bukaModalPesanMaintenance(moduleKey) {
    $('#modal_maint_target_key').val(moduleKey || 'all');
    $('#modal_maint_select_module').val(moduleKey || 'all');

    var currentMsg = '';
    if (moduleKey === 'kurikulum' && _maintStatuses['kurikulum']) {
      currentMsg = _maintStatuses['kurikulum'].message || '';
    } else if (moduleKey === 'studio_rps' && _maintStatuses['studio_rps']) {
      currentMsg = _maintStatuses['studio_rps'].message || '';
    } else {
      currentMsg = _maintStatuses['kurikulum']?.message || _maintStatuses['studio_rps']?.message || '';
    }

    $('#modal_maint_textarea_msg').val(currentMsg);
    $('#modalPesanMaintenance').modal('show');
  }

  function setTemplatePesan(idx) {
    var msgs = {
      1: 'Modul sedang dalam pemeliharaan rutin dan peningkatan performa sistem oleh Administrator Akademik.',
      2: 'Sedang berlangsung sinkronisasi data kurikulum & CPL institusi standar SN-Dikti 2026.',
      3: 'Studio RPS sedang dioptimalkan dengan model AI terbaru untuk penyusunan RPS yang lebih akurat.'
    };
    $('#modal_maint_textarea_msg').val(msgs[idx] || '');
  }

  function simpanPesanMaintenanceModal() {
    var targetKey = $('#modal_maint_select_module').val();
    var msg = $('#modal_maint_textarea_msg').val().trim();

    if (!msg) {
      alert('Pesan pengumuman pemeliharaan tidak boleh kosong.');
      return;
    }

    var postData = {
      module_key: targetKey,
      message: msg
    };
    postData[_csrfName] = _csrfHash;

    $.ajax({
      url: '<?= site_url("C_setting_rps/ajax_save_maintenance_message"); ?>',
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: function(resp) {
        if (resp.status) {
          $('#modalPesanMaintenance').modal('hide');
          if (typeof toastr !== 'undefined') {
            toastr.success(resp.message, 'Pesan Disimpan');
          }
          setTimeout(function() {
            window.location.reload();
          }, 500);
        } else {
          alert(resp.message || 'Gagal menyimpan pesan.');
        }
      },
      error: function(xhr, status, error) {
        alert('Terjadi kesalahan saat menyimpan pesan: ' + (xhr.responseText || error || 'Jaringan error.'));
      }
    });
  }
</script>
