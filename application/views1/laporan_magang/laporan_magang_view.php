<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .magang-dashboard {
        --primary: #4F46E5;
        --primary-hover: #4338CA;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --surface: #FFFFFF;
        --text-main: #111827;
        --text-muted: #6B7280;
        --border: #E5E7EB;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-main);
    }
    
    .portlet-title::before,
    .portlet-title::after {
        display: none !important;
    }
    
    .magang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }
    
    .student-magang-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        transition: all 0.25s ease;
        position: relative;
    }
    
    .student-magang-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        border-color: #CBD5E1;
    }
    
    .card-banner {
        height: 75px;
        background: linear-gradient(135deg, #1E293B, #475569);
        position: relative;
    }
    
    .avatar-wrapper {
        text-align: center;
        margin-top: -40px;
        position: relative;
        z-index: 5;
        margin-bottom: 12px;
    }
    
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #FFFFFF;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        display: inline-block;
        background: #F3F4F6;
    }
    
    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .student-info-section {
        padding: 0 20px 15px 20px;
        text-align: center;
        border-bottom: 1px solid #F1F5F9;
    }
    
    .student-name {
        font-size: 16px;
        font-weight: 800;
        color: #1E293B;
        margin: 0 0 4px 0;
        text-transform: uppercase;
    }
    
    .student-nipd {
        font-size: 12px;
        font-weight: 700;
        color: #EF4444;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .company-badge {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F1F5F9;
        padding: 4px 12px;
        border-radius: 20px;
        margin-top: 4px;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .details-section {
        padding: 16px 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        font-size: 13px;
    }
    
    .detail-row {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .detail-label {
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    

    .detail-value {
        color: #334155;
        font-weight: 600;
        line-height: 1.5;
    }
    
    .grades-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        background: #F8FAFC;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        border: 1px solid #E2E8F0;
    }
    
    .grade-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .grade-label {
        font-size: 10px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    
    .grade-num {
        font-size: 13px;
        font-weight: 800;
        color: #1E293B;
    }
    
    .card-actions-wrapper {
        padding: 16px 20px 20px 20px;
        display: flex;
        gap: 8px;
        background: #FAFBFD;
        border-top: 1px solid #F1F5F9;
    }
    
    .action-btn {
        flex: 1;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12.5px;
        padding: 10px;
        text-align: center;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-pdf {
        background: #FFFFFF;
        color: #10B981;
        border: 1px solid #A7F3D0;
    }
    .btn-pdf:hover {
        background: #ECFDF5;
        color: #047857;
        text-decoration: none;
    }
    
    .btn-proposal {
        background: #FFFFFF;
        color: #4F46E5;
        border: 1px solid #C7D2FE;
    }
    .btn-proposal:hover {
        background: #EEF2FF;
        color: #4338CA;
        border-color: #818CF8;
        transform: translateY(-1px);
    }
    
    .btn-review {
        background: linear-gradient(135deg, #4F46E5, #312E81);
        color: #FFFFFF;
        box-shadow: 0 4px 8px rgba(79, 70, 229, 0.15);
    }
    .btn-review:hover {
        background: linear-gradient(135deg, #4338CA, #1E1B4B);
        color: #FFFFFF;
        text-decoration: none;
    }
    
    .btn-nilai {
        background: #FFFFFF;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    .btn-nilai:hover {
        background: #FFFBEB;
        color: #92400E;
        text-decoration: none;
    }
    
    #search-mahasiswa:focus {
        border-color: var(--primary) !important;
        background: #FFFFFF !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    
    /* ----------------------------------------------------
       OVERLAY & PROGRESS STYLES
       ---------------------------------------------------- */
    #init-loading-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: #0f172a;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #f8fafc;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: opacity 0.5s ease, visibility 0.5s;
    }
    .overlay-content-box {
        width: 100%; max-width: 420px;
        text-align: center;
        padding: 0 20px;
    }
    .loading-pulse {
        width: 60px; height: 60px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.2);
        margin: 0 auto 24px auto;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5);
        animation: pulse-ring 2s infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(99, 102, 241, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
    }
    .progress-bar-container {
        width: 100%; height: 6px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        overflow: hidden;
        margin: 20px 0 12px 0;
        position: relative;
    }
    #init-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #d946ef);
        background-size: 200% 100%;
        border-radius: 10px;
        transition: width 0.4s ease-out;
        animation: gradient-shift 2s infinite linear;
    }
    @keyframes gradient-shift {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }
    .init-steps-container {
        display: flex; flex-direction: column; gap: 10px; margin-top: 20px;
    }
    .init-step-item {
        display: flex; align-items: center; gap: 12px;
        text-align: left;
    }
    .init-step-chip {
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid #475569;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s ease;
    }
    .init-step-chip.active {
        border-color: #6366f1; border-top-color: transparent;
        animation: spin 1s linear infinite;
    }
    .init-step-chip.done {
        border-color: #10b981; background: #10b981; animation: none;
    }
    .init-step-chip.done::after {
        content: '✓'; color: #fff; font-size: 10px; font-weight: bold;
    }
    .init-step-label {
        font-size: 13px; font-weight: 600; color: #64748b; transition: color 0.3s ease;
    }
    .init-step-label.active { color: #f8fafc; }
    .init-step-label.done { color: #10b981; }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* SHIMMER */
    .shimmer {
        background: #f6f7f8;
        background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
        background-repeat: no-repeat;
        background-size: 800px 100%;
        animation: shimmer-anim 1.5s infinite linear;
        border-radius: 4px;
    }
    @keyframes shimmer-anim {
        0% { background-position: -400px 0; }
        100% { background-position: 400px 0; }
    }
</style>

<div id="init-loading-overlay">
  <div class="overlay-content-box">
      <div class="loading-pulse">
          <i class="fa fa-file-text-o" style="font-size: 24px; color: #818cf8;"></i>
      </div>
      <h2 style="font-weight: 800; font-size: 20px; margin: 0 0 4px 0; letter-spacing: -0.5px; color: #fff;">Menyiapkan Laporan Magang</h2>
      <div style="font-size: 13px; color: #94a3b8; font-weight: 500;">Mohon tunggu, menyusun rekap harian...</div>
      
      <div class="progress-bar-container">
          <div id="init-progress-bar"></div>
      </div>
      <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: #6366f1;">
          <span id="init-progress-text">0%</span>
          <span style="color: #64748b;">Memuat</span>
      </div>

      <div class="init-steps-container">
          <div class="init-step-item">
              <div id="chip-ui" class="init-step-chip"></div>
              <div id="lbl-ui" class="init-step-label">Inisialisasi Antarmuka</div>
          </div>
          <div class="init-step-item">
              <div id="chip-warning" class="init-step-chip"></div>
              <div id="lbl-warning" class="init-step-label">Menganalisis Peringatan Laporan...</div>
          </div>
      </div>
  </div>
</div>

<div class="page-content magang-dashboard" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title" style="display: flex !important; justify-content: space-between !important; align-items: center !important; border-bottom: 1px solid #E5E7EB !important; padding-bottom: 12px !important; margin-bottom: 20px !important; width: 100% !important; float: none !important;">
          <div class="caption" style="display: flex !important; flex-direction: column !important; gap: 2px !important; text-align: left !important; float: none !important; padding: 0 !important;">
            <span class="caption-subject font-green-sharp bold uppercase" style="font-weight: 800; font-size: 16px; color: #111827;"><?= $judul_besar; ?></span>
            <span class="caption-helper" style="font-size: 12px; color: #6B7280; font-weight: 500;"><?= $judul_kecil; ?></span>
          </div>
        </div>
        <div class="portlet-body">

          <!-- Controls Wrapper (Search & Reload) -->
          <div class="controls-wrapper" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
            <!-- Search input -->
            <div class="search-container" style="position: relative; flex: 1; max-width: 400px; min-width: 280px;">
              <i class="fa fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 14px;"></i>
              <input type="text" id="search-mahasiswa" placeholder="Cari nama atau NIM siswa..." 
                     style="width: 100%; padding: 10px 14px 10px 40px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; font-weight: 500; outline: none; transition: all 0.2s; background: #FAFBFD;"
                     onkeyup="filterMahasiswa()">
              <button id="clear-search" onclick="clearSearch()" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer; display: none; padding: 0;">
                <i class="fa fa-times-circle" style="font-size: 16px;"></i>
              </button>
            </div>
            <!-- Right Buttons -->
            <div style="display: flex; gap: 8px; align-items: center;">
              <!-- SK DPL Button -->
              <button id="btn-sk-dpl" onclick="openModalSkDpl()"
                      style="border-radius: var(--radius-md); font-weight: 700; padding: 10px 18px; background: #FFF7ED; color: #D97706; border: 1px solid #FDE68A; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; margin: 0; height: 42px; cursor: pointer;">
                <i class="fa fa-file-text"></i> Import SK
              </button>
              <!-- Pedoman Button -->
              <button id="btn-pedoman" onclick="openPedoman()"
                      style="border-radius: var(--radius-md); font-weight: 700; padding: 10px 18px; background: #F0FDF4; color: #059669; border: 1px solid #A7F3D0; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; margin: 0; height: 42px; cursor: pointer;">
                <i class="fa fa-book"></i> Pedoman
              </button>
              <!-- Reload button -->
              <button class="btn btn-default btn-sm" onclick="window.location.reload()" 
                      style="border-radius: var(--radius-md); font-weight: 700; padding: 10px 20px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; margin: 0; height: 42px;">
                <i class="fa fa-refresh"></i> Reload
              </button>
            </div>
          </div>

          <!-- Modal Import SK DPL -->
          <div class="modal fade" id="modalSkDpl" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1050px !important; width: 100% !important;">
              <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">

                <!-- Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #92400E, #D97706); border-bottom: none; padding: 16px 24px; display: flex; align-items: center;">
                  <h5 class="modal-title" style="font-weight: 800; color: #FFFFFF; margin: 0; font-size: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-file-text" style="color: #FDE68A;"></i> Import SK Pengangkatan DPL
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: -8px -8px -8px auto; padding: 0; opacity: 0.9; color: white; text-shadow: none;">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <!-- Body -->
                <div class="modal-body" style="padding: 24px; max-height: 80vh; overflow-y: auto;">

                  <!-- Step 1: Input Link -->
                  <div id="sk-step-1">
                    <p style="color: #64748B; font-size: 13px; margin-bottom: 16px; line-height: 1.6;">
                      Masukkan link Google Drive dari file PDF SK Pengangkatan Dosen Pembimbing Lapangan. Sistem akan membaca, memvalidasi secara legal, dan menampilkan daftar mahasiswa yang ada di SK tersebut.
                    </p>
                    <div style="display: flex; gap: 10px; align-items: stretch;">
                      <input type="text" id="sk_gdrive_link" placeholder="https://drive.google.com/file/d/..." 
                             style="flex: 1; padding: 10px 16px; border: 1.5px solid #E2E8F0; border-radius: 10px; font-size: 13px; outline: none; transition: border 0.2s;"
                             onfocus="this.style.borderColor='#D97706'" onblur="this.style.borderColor='#E2E8F0'">
                      <button id="btn-proses-sk" onclick="prosesSKDpl()" 
                              style="background: linear-gradient(135deg, #D97706, #92400E); color: white; border: none; border-radius: 10px; font-weight: 700; padding: 10px 22px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; white-space: nowrap; transition: opacity 0.2s;">
                        <i class="fa fa-cogs"></i> Proses SK
                      </button>
                    </div>
                    <div id="sk-error-msg" style="display:none; margin-top: 10px; padding: 10px 14px; background: #FEF2F2; color: #DC2626; border-radius: 8px; font-size: 13px; font-weight: 600;"></div>
                  </div>

                  <!-- Step 2: Hasil Parsing (ditampilkan setelah proses) -->
                  <div id="sk-step-2" style="display:none; margin-top: 16px;">

                    <!-- Info SK -->
                    <div id="sk-info-box" style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px;">
                      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                        <div>
                          <div style="font-weight: 800; color: #92400E; font-size: 14px; margin-bottom: 4px;" id="sk-no-sk"></div>
                          <div style="color: #B45309; font-size: 12.5px; line-height: 1.5;" id="sk-perihal"></div>
                          <div style="color: #64748B; font-size: 11.5px; margin-top: 6px;" id="sk-berlaku"></div>
                        </div>
                        <div id="sk-validitas-badge"></div>
                      </div>
                      <div id="sk-catatan" style="margin-top: 10px; font-size: 12px; color: #78350F; background: rgba(255,255,255,0.6); padding: 8px 12px; border-radius: 8px;"></div>
                      <div id="sk-summary-filter" style="margin-top: 10px; font-size: 12px; color: #0369A1; background: #E0F2FE; padding: 8px 12px; border-radius: 8px;"></div>
                    </div>

                    <!-- Tabel Mahasiswa -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                      <div style="font-weight: 800; color: #1E293B; font-size: 13px;">
                        <i class="fa fa-users" style="color: #D97706; margin-right: 6px;"></i>
                        Mahasiswa dalam SK <span style="color: #D97706;">(sesuai DPL Anda)</span>
                      </div>
                      <label style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #64748B; cursor: pointer; font-weight: 600;">
                        <input type="checkbox" id="sk-check-all" onchange="skToggleAll(this.checked)" style="width: 15px; height: 15px;"> Pilih Semua
                      </label>
                    </div>

                    <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #E2E8F0;">
                      <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;">
                        <thead>
                          <tr style="background: #1E293B; color: white;">
                            <th style="padding: 10px 12px; text-align: center; width: 40px;"></th>
                            <th style="padding: 10px 12px; text-align: left;">No</th>
                            <th style="padding: 10px 12px; text-align: left;">NIM</th>
                            <th style="padding: 10px 12px; text-align: left;">Nama Mahasiswa</th>
                            <th style="padding: 10px 12px; text-align: left;">Perusahaan / Mitra</th>
                            <th style="padding: 10px 12px; text-align: left;">Status</th>
                          </tr>
                        </thead>
                        <tbody id="sk-mahasiswa-tbody"></tbody>
                      </table>
                    </div>

                    <div id="sk-empty-msg" style="display:none; text-align:center; padding: 32px; color: #64748B; font-size: 13px;">
                      <i class="fa fa-info-circle" style="font-size: 28px; margin-bottom: 10px; display: block; color: #94A3B8;"></i>
                      Tidak ada mahasiswa dengan DPL Anda yang ditemukan dalam SK ini.
                    </div>
                  </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer" style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 12px 24px; display: flex; align-items: center; justify-content: flex-end; width: 100%;">
                  <div id="sk-footer-info" style="font-size: 12px; color: #64748B; margin-right: auto;"></div>
                  <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-sm" data-dismiss="modal" style="background: #F1F5F9; color: #475569; border-radius: 8px; font-weight: 700; padding: 8px 20px; border: 1px solid #E2E8F0;">
                      Tutup
                    </button>
                    <button type="button" id="btn-simpan-sk" onclick="simpanSKDpl()" style="display:none; background: linear-gradient(135deg, #059669, #047857); color: white; border: none; border-radius: 8px; font-weight: 700; padding: 8px 24px; cursor: pointer; align-items: center; gap: 8px;">
                      <i class="fa fa-save"></i> Simpan ke Daftar
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Modal Pedoman -->
          <div class="modal fade" id="modalPedoman" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 760px;">
              <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">
                <!-- Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #1E293B, #334155); border-bottom: none; padding: 16px 24px;">
                  <h5 class="modal-title" style="font-weight: 800; color: #FFFFFF; margin: 0; font-size: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-book" style="color: #34D399;"></i> Pedoman Penggunaan — Laporan Magang/Internship
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0; opacity: 0.7; color: white;">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <!-- Body: AJAX-loaded content -->
                <div class="modal-body" id="pedomanModalBody" style="padding: 24px; max-height: 75vh; overflow-y: auto;">
                  <div id="pedoman-loading" style="text-align: center; padding: 40px 0; color: #64748B;">
                    <i class="fa fa-spinner fa-spin" style="font-size: 28px; margin-bottom: 12px; display: block; color: #4F46E5;"></i>
                    <span style="font-size: 13px; font-weight: 600;">Memuat pedoman...</span>
                  </div>
                  <div id="pedoman-content" style="display: none;"></div>
                </div>
                <!-- Footer -->
                <div class="modal-footer" style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 12px 24px;">
                  <button type="button" class="btn btn-sm" data-dismiss="modal" style="background: #4F46E5; color: white; border-radius: 8px; font-weight: 700; padding: 8px 24px; border: none;">
                    <i class="fa fa-check"></i> Mengerti
                  </button>
                </div>
              </div>
            </div>
          </div>


          <div class="magang-grid">
            <?php if(!empty($data_mahasiswa)): ?>
              <?php foreach($data_mahasiswa as $m): ?>
                <?php 
                $photo_thumb_src = !empty($m->foto) 
                    ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($m->foto) 
                    : base_url('assets/no_profile_photo.png');
                $photo_full_src = !empty($m->foto)
                    ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . rawurlencode($m->foto)
                    : base_url('assets/no_profile_photo.png');
                $perusahaan_esc = htmlspecialchars($m->nama_perusahaan ?? '-', ENT_QUOTES);
                $nm_pd_esc      = htmlspecialchars($m->nm_pd ?? '', ENT_QUOTES);
                $nipd_esc       = htmlspecialchars($m->nipd ?? '', ENT_QUOTES);
                ?>
                <div class="student-magang-card">
                  <div>
                    <!-- Banner -->
                    <div class="card-banner"></div>
                    
                    <!-- Avatar -->
                    <div class="avatar-wrapper">
                      <div class="avatar-circle" style="cursor: pointer;" 
                           onclick="showZoomFotoMagang('<?= $photo_full_src ?>', '<?= $nm_pd_esc ?>', '<?= $nipd_esc ?>', '<?= $perusahaan_esc ?>')"
                           title="Klik untuk memperbesar foto">
                        <img src="<?= $photo_thumb_src ?>" 
                             alt="<?= $m->nm_pd ?>"
                             style="transition: transform 0.2s; width:100%; height:100%; object-fit:cover;"
                             onmouseover="this.style.transform='scale(1.08)'" 
                             onmouseout="this.style.transform='scale(1)'"
                             onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                      </div>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="student-info-section">
                      <h3 class="student-name" title="<?= $m->nm_pd ?>"><?= $m->nm_pd ?></h3>
                      <div class="student-nipd">NIM: <?= $m->nipd ?></div>
                      <div class="company-badge" title="<?= $m->nama_perusahaan ?>">
                        🏢 <?= !empty($m->nama_perusahaan) ? $m->nama_perusahaan : '-' ?>
                      </div>
                      
                      <?php if (!empty($m->tanggal_mulai) && !empty($m->tanggal_selesai) && $m->tanggal_mulai != '0000-00-00'): ?>
                        <div style="font-size: 11px; color: #64748B; margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 6px; font-weight: 600;">
                          <i class="fa fa-calendar" style="color: #475569;"></i> 
                          <?= date('d M Y', strtotime($m->tanggal_mulai)) ?> - <?= date('d M Y', strtotime($m->tanggal_selesai)) ?>
                        </div>
                      <?php else: ?>
                        <div style="margin-top: 6px;">
                          <span style="font-size: 10px; color: #DC2626; font-weight: 700; background: #FEF2F2; display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 12px; border: 1px solid #FECACA;">
                            <i class="fa fa-exclamation-triangle"></i> Mahasiswa belum isi tgl PKL
                          </span>
                        </div>
                      <?php endif; ?>
                    </div>
                    
                    <!-- Details -->
                    <div class="details-section">
                      <div class="detail-row">
                        <div class="detail-label">Mata Kuliah Terkait</div>
                        <div class="detail-value" style="font-size: 12px; color: #475569;">
                          <?= !empty($m->mk_terkait) ? $m->mk_terkait : '<span style="font-style: italic; color: #94A3B8;">-</span>' ?>
                        </div>
                      </div>
                      
                      <!-- Grades (Dihilangkan sementara)
                      <div class="grades-grid">
                        <div class="grade-box" style="border-right: 1px solid #E2E8F0;">
                          <div class="grade-label">Fasilitator</div>
                          <div class="grade-num" style="color: #059669;">
                            <?= ($m->nilai_rata_fasilitator === null || $m->nilai_rata_fasilitator === '') ? '-' : number_format($m->nilai_rata_fasilitator, 2) ?>
                          </div>
                        </div>
                        <div class="grade-box">
                          <div class="grade-label">Nilai DPL</div>
                          <div class="grade-num" style="color: #4F46E5;">
                            <?= !empty($m->nilai_dosen) ? $m->nilai_dosen : '-' ?>
                          </div>
                        </div>
                      </div>
                      -->
                      
                      <div id="warning-wrapper-<?= $m->nipd ?>" style="min-height: 50px;">
                        <!-- Skeleton loader before AJAX load -->
                        <div style="margin-top: 10px;">
                          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 10px 12px; border-radius: 8px;">
                            <div class="shimmer" style="height: 12px; width: 80%; margin-bottom: 8px;"></div>
                            <div class="shimmer" style="height: 12px; width: 60%;"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Actions -->
                  <div class="card-actions-wrapper">
                    <button onclick="showPdf('<?= site_url("C_tracking_kegiatan/cetak_tracking/$m->nipd") ?>')" class="action-btn btn-pdf" title="Cetak PDF Tracking">
                      <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    
                    <?php if (!empty($m->link_drive_proposal)): ?>
                    <button type="button" data-toggle="modal" data-target="#modalProposal<?= $m->nipd ?>" class="action-btn btn-proposal" title="Lihat Usulan">
                      <i class="fa fa-file-text-o"></i> Usulan
                    </button>
                    <?php endif; ?>
                    
                    <a href="javascript:void(0)" onclick="openReview('<?= site_url("C_review_laporan/index/$m->nipd") ?>')" class="action-btn btn-review" title="Review Laporan Kegiatan">
                      <i class="fa fa-pencil"></i> Review
                    </a>
                  </div>
                </div>

                <!-- Modal Proposal -->
                <?php if (!empty($m->link_drive_proposal)): 
                    $embed_link = $m->link_drive_proposal;
                    if (strpos($embed_link, 'drive.google.com') !== false) {
                        if (strpos($embed_link, '/view') !== false) {
                            $embed_link = str_replace('/view', '/preview', $embed_link);
                        } else if (strpos($embed_link, '/edit') !== false) {
                            $embed_link = str_replace('/edit', '/preview', $embed_link);
                        }
                    }
                ?>
                  <div class="modal fade" id="modalProposal<?= $m->nipd ?>" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 85vw; width: 85vw;">
                      <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">

                        <!-- Header -->
                        <div class="modal-header" style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; padding: 12px 20px;">
                          <h5 class="modal-title" style="font-weight: 800; color: #1E293B; margin: 0; font-size: 15px;">
                            <i class="fa fa-file-text-o" style="color: #4F46E5; margin-right: 8px;"></i> Berkas Pendaftaran Internship/Magang - <?= $m->nm_pd ?>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0; opacity: 0.5;">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>

                        <!-- Body: PDF Full Width + Collapsible AI Drawer -->
                        <div style="position: relative; display: flex; flex-direction: column; height: 82vh;">

                          <!-- PDF Viewer (Full Width) -->
                          <div id="pdf_area_<?= $m->nipd ?>" style="flex: 1; overflow: hidden; position: relative;">
                            <iframe id="pdf_iframe_<?= $m->nipd ?>" data-src="<?= htmlspecialchars($embed_link) ?>" src="" style="width: 100%; height: 100%; border: none;" allow="autoplay"></iframe>
                          </div>


                          <!-- Collapsible AI Chat Drawer -->
                          <div id="ai_drawer_<?= $m->nipd ?>" style="height: 0; overflow: hidden; transition: height 0.35s ease; background: #FAFBFD; border-top: 0px solid #E2E8F0; display: flex; flex-direction: column;">

                            <!-- Drawer Content -->
                            <div style="display: flex; height: 100%;">

                              <!-- AI Summary (kiri) -->
                              <div style="flex: 1; overflow-y: auto; padding: 16px 20px; border-right: 1px solid #E2E8F0; font-size: 13px; color: #334155; line-height: 1.6;" id="ai_summary_container_<?= $m->nipd ?>">
                                <!-- Header ringkasan + tombol Reset -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                  <div style="font-weight: 800; color: #1E293B; font-size: 13px;">
                                    <i class="fa fa-magic" style="color: #8B5CF6; margin-right: 6px;"></i> Ringkasan Usulan Pendaftaran Internship/Magang
                                  </div>
                                  <button type="button" id="btn_reset_ai_<?= $m->nipd ?>" onclick="resetAiSummaryMagang('<?= $m->nipd ?>')"
                                    style="background: #FEE2E2; color: #DC2626; border-radius: 6px; border: none; font-weight: 700; padding: 3px 8px; font-size: 11px; <?= empty($m->ai_summary_proposal) ? 'display:none;' : '' ?>">
                                    <i class="fa fa-refresh"></i> Reset
                                  </button>
                                </div>
                                <textarea class="ai-raw-text-magang" style="display:none;"><?= !empty($m->ai_summary_proposal) ? htmlspecialchars($m->ai_summary_proposal) : '' ?></textarea>
                                <div class="ai-rendered-content-magang"></div>
                                <div class="ai-empty-state-magang" style="text-align: center; margin-top: 40px; <?= !empty($m->ai_summary_proposal) ? 'display:none;' : '' ?>">
                                  <div style="background: #EEF2FF; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                                    <i class="fa fa-cogs" style="font-size: 26px; color: #6366F1;"></i>
                                  </div>
                                  <h6 style="font-weight: 700; color: #1E293B; margin-bottom: 6px; font-size: 13px;">Ringkas Otomatis</h6>
                                  <p style="color: #64748B; font-size: 12px; margin-bottom: 16px;">AI akan menarik poin-poin penting dari dokumen ini.</p>
                                  <button type="button" id="btn_generate_ai_<?= $m->nipd ?>" onclick="generateAiSummaryMagang('<?= $m->nipd ?>')"
                                    style="background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; border-radius: 8px; font-weight: 700; padding: 8px 18px; border: none; box-shadow: 0 4px 12px rgba(79,70,229,0.25); width: 100%; transition: all 0.2s;">
                                    <i class="fa fa-magic" style="margin-right: 6px;"></i> Mulai Meringkas
                                  </button>
                                </div>
                              </div>

                              <!-- Chat (kanan) -->
                              <div style="flex: 1; display: flex; flex-direction: column; padding: 16px 20px;" class="ai-chat-container-magang">
                                <div style="margin-bottom: 12px;">
                                  <h6 style="font-weight: 800; color: #1E293B; margin: 0; font-size: 13px;">
                                    <i class="fa fa-comments-o" style="color: #4F46E5; margin-right: 6px;"></i> Tanya Jawab Dokumen
                                  </h6>
                                </div>
                                <div class="chat-messages-magang" id="chat_messages_<?= $m->nipd ?>"
                                  style="flex: 1; overflow-y: auto; margin-bottom: 12px; display: flex; flex-direction: column; gap: 10px; padding-right: 6px;">
                                  <div style="text-align: center; color: #94A3B8; font-size: 12px; font-style: italic; margin-top: 8px;">Ketik pesan Anda. Gunakan <strong>@dokumen</strong> jika ingin AI membaca isi berkas.</div>
                                </div>
                                <div style="display: flex; gap: 8px; position: relative;">
                                    <input type="text" id="chat_input_<?= $m->nipd ?>" class="form-control form-control-sm"
                                      placeholder="Ketik pesan (@dokumen untuk cek berkas)..."
                                      style="border-radius: 20px; font-size: 13px; height: 34px; border: 1px solid #CBD5E1; flex: 1;"
                                      onkeydown="handleChatInputKey(event, '<?= $m->nipd ?>')"
                                      oninput="handleChatInputAutocomplete('<?= $m->nipd ?>')">
                                      
                                    <!-- Autocomplete Dropdown -->
                                    <div id="autocomplete_<?= $m->nipd ?>" style="display: none; position: absolute; bottom: calc(100% + 8px); left: 0; background: white; border: 1px solid #CBD5E1; border-radius: 8px; padding: 6px; box-shadow: 0 -4px 12px rgba(0,0,0,0.1); z-index: 10; width: calc(100% - 50px);">
                                       <div onclick="selectAutocomplete('<?= $m->nipd ?>', '@dokumen ')" style="padding: 8px 12px; cursor: pointer; border-radius: 6px; display: flex; align-items: center; gap: 8px; background: #F8FAFC; color: #4F46E5; font-weight: 700; font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#EEF2FF'" onmouseout="this.style.background='#F8FAFC'">
                                            <i class="fa fa-file-text-o"></i> @dokumen <span style="color: #64748B; font-weight: normal; font-size: 12px; margin-left: 4px;">— AI akan membaca isi dokumen</span>
                                       </div>
                                    </div>

                                    <button type="button" id="btn_send_chat_<?= $m->nipd ?>" onclick="sendChatMessageMagang('<?= $m->nipd ?>')"
                                      style="background: #4F46E5; color: white; border-radius: 20px; padding: 4px 14px; border: none; flex-shrink: 0;">
                                      <i class="fa fa-paper-plane"></i>
                                    </button>
                                  </div>
                              </div>

                            </div>
                          </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer" style="background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
                          <!-- AI Summary Toggle (pindah ke footer) -->
                          <button type="button" id="btn_toggle_ai_<?= $m->nipd ?>" onclick="toggleAiDrawer('<?= $m->nipd ?>')"
                            style="background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; border: none; border-radius: 20px; font-size: 12.5px; font-weight: 700; padding: 7px 18px; cursor: pointer; display: flex; align-items: center; gap: 7px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(99,102,241,0.3);">
                            <i class="fa fa-magic"></i>
                            <span id="btn_toggle_ai_label_<?= $m->nipd ?>">AI Summary</span>
                            <i class="fa fa-chevron-up" id="btn_toggle_ai_icon_<?= $m->nipd ?>" style="font-size: 10px; transition: transform 0.3s;"></i>
                          </button>
                          <button type="button" class="btn btn-sm" data-dismiss="modal" style="background: #4F46E5; color: white; border-radius: 8px; font-weight: 700; padding: 6px 20px;">
                            Tutup
                          </button>
                        </div>

                      </div>
                    </div>
                  </div>

                <?php endif; ?>

              <?php endforeach; ?>
            <?php else: ?>
              <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #64748B; background: #FFFFFF; border-radius: 16px; border: 2px dashed #E2E8F0;">
                <i class="fa fa-users" style="font-size: 54px; margin-bottom: 12px; color: #94A3B8;"></i>
                <p style="font-weight: 700; margin: 0; font-size: 15px;">Belum ada data mahasiswa bimbingan magang untuk Anda.</p>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal Zoom Foto Mahasiswa Magang -->
<div class="modal fade" id="modalZoomFotoMagang" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.85);">
  <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: 5vh; max-width: 440px;">
    <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
      <div class="modal-header" style="border: none; padding: 0 0 10px 0; text-align: right;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                style="color: #fff; opacity: 1; text-shadow: none; font-size: 32px; margin: 0 0 0 auto; outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align: center; padding: 0;">
        <img id="zoomFotoImgMagang" src="" 
             onerror="this.onerror=null; this.src='<?= base_url('assets/no_profile_photo.png') ?>';" 
             alt="Foto Mahasiswa"
             style="width: 100%; height: 420px; max-height: 58vh; border-radius: 14px; object-fit: cover;
                    box-shadow: 0 12px 35px rgba(0,0,0,0.6); background: #fff; padding: 8px;">
        
        <div style="background: #ffffff; border-radius: 14px; padding: 16px 18px; margin-top: 16px;
                    box-shadow: 0 6px 20px rgba(0,0,0,0.2); text-align: left;">
          <div id="zoomFotoNameMagang"
               style="color: #1e293b; font-weight: 800; font-size: 17px; margin-bottom: 4px;
                      text-align: center; text-transform: uppercase;
                      border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 10px;"></div>
          
          <div style="font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">
            <i class="fa fa-id-card-o"></i> NIM
          </div>
          <div id="zoomFotoNipdMagang" style="font-size: 13px; color: #4F46E5; font-weight: 700; margin-bottom: 10px; font-family: monospace; letter-spacing: 0.5px;"></div>
          
          <div style="font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">
            <i class="fa fa-building-o"></i> Tempat Magang / Mitra
          </div>
          <div id="zoomFotoPerusahaanMagang" style="font-size: 14px; color: #334155; font-weight: 600;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    var pedomanLoaded = false;

    // ============================================================
    // PROGRESSIVE LOADING LOGIC
    // ============================================================
    function waitForLibrary(cb) {
        if (window.jQuery) { cb(); }
        else { setTimeout(function () { waitForLibrary(cb); }, 80); }
    }

    waitForLibrary(function () {
        var $ = window.jQuery;

        // Inisialisasi Overlay
        var pct = 0;
        var bar = document.getElementById('init-progress-bar');
        var txt = document.getElementById('init-progress-text');

        function setPct(val) {
            pct = val;
            if(bar) bar.style.width = pct + '%';
            if(txt) txt.innerText   = pct + '%';
        }

        function setStep(id) {
            var elChip = document.getElementById('chip-' + id);
            var elLbl  = document.getElementById('lbl-' + id);
            if(elChip) elChip.className = 'init-step-chip active';
            if(elLbl)  elLbl.className  = 'init-step-label active';
        }

        function doneStep(id) {
            var elChip = document.getElementById('chip-' + id);
            var elLbl  = document.getElementById('lbl-' + id);
            if(elChip) elChip.className = 'init-step-chip done';
            if(elLbl)  elLbl.className  = 'init-step-label done';
        }

        function dismissOverlay() {
            var _ov = document.getElementById('init-loading-overlay');
            if (_ov) {
                _ov.style.opacity = '0';
                setTimeout(function () { _ov.style.display = 'none'; }, 500);
            }
        }

        // --- START PHASES ---
        
        // Phase 1: UI Initialized
        setPct(35);
        setStep('ui');
        setTimeout(function() {
            doneStep('ui');
            
            // Phase 2: Load Warnings
            setStep('warning');
            setPct(65);
            
            $.ajax({
                url: '<?= site_url("C_laporan_magang/ajax_load_warning_magang") ?>',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status && res.warnings) {
                        setPct(90);
                        
                        // Render DOM per siswa
                        // Note: kita perlu mengambil variable jml_belum_review dari attribute/data jika kita punya, 
                        // tapi karena tidak dikirim via AJAX (ada di var PHP awal), kita bisa gunakan JSON di dalam elemen atau kita pass via data attribute.
                        // Di implementasi ini, kita memanipulasi #warning-wrapper-{nipd}.
                        
                        $.each(res.warnings, function(nipd, w) {
                            var wrapper = $('#warning-wrapper-' + nipd);
                            if (wrapper.length) {
                                // Kita juga butuh jml_belum_review yang ada dari backend. Karena gaada di json warning, 
                                // mari kita ambil dari elemen (kita render secara default di PHP sebagai data atr, tapi karena blm ada, 
                                // kita akan merender progress UI sederhana tanpa review counts)
                                
                                var html = '';
                                
                                // 1. Laporan count
                                html += '<div style="margin-top: 10px;">';
                                if (w.jml_total_laporan == 0) {
                                    html += '<div style="background: #F1F5F9; border: 1px dashed #CBD5E1; color: #475569; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px;">';
                                    html += '  <i class="fa fa-info-circle" style="font-size: 14px;"></i> Belum ada laporan dibuat';
                                    html += '</div>';
                                } else {
                                    html += '<div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px;">';
                                    html += '  <i class="fa fa-check-circle" style="font-size: 14px;"></i> Total ' + w.jml_total_laporan + ' laporan dikirim';
                                    html += '</div>';
                                }
                                html += '</div>';
                                
                                // 2. Warning Magang
                                if (w.warning_magang) {
                                    var warn = w.warning_magang;
                                    html += '<div style="margin-top: 10px; background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; padding: 8px 12px; border-radius: 8px; font-size: 12px;">';
                                    html += '  <div style="font-weight: 700; display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">';
                                    html += '    <i class="fa fa-warning" style="font-size: 14px; color: #D97706;"></i> Peringatan Kekosongan Laporan';
                                    html += '  </div>';
                                    
                                    if (warn.last_filled) {
                                        html += '  <div style="margin-bottom: 4px;"><strong>Terakhir Diisi:</strong> ' + warn.last_filled + '</div>';
                                    } else {
                                        html += '  <div style="margin-bottom: 4px;"><strong>Terakhir Diisi:</strong> Belum pernah mengisi</div>';
                                    }
                                    
                                    if (warn.missing_weeks > 0) {
                                        html += '  <div style="margin-bottom: 4px;"><strong>Minggu Belum Dibuat:</strong> ' + warn.missing_weeks + ' Minggu</div>';
                                    }
                                    
                                    if (warn.incomplete_weeks && warn.incomplete_weeks.length > 0) {
                                        html += '  <div style="margin-top: 4px; color: #B45309;"><strong>Terdeteksi ada lap harian tidak lengkap</strong></div>';
                                    }
                                    html += '</div>';
                                }

                                wrapper.html(html);
                            }
                        });
                        
                        doneStep('warning');
                        setPct(100);
                        setTimeout(dismissOverlay, 400);
                    } else {
                        // error fallback
                        setPct(100);
                        dismissOverlay();
                    }
                },
                error: function() {
                    setPct(100);
                    dismissOverlay();
                }
            });
        }, 300);
    });

    // ============================================================
    // Zoom Foto Mahasiswa
    // ============================================================
    function showZoomFotoMagang(src, name, nipd, perusahaan) {
        document.getElementById('zoomFotoImgMagang').src = src;
        document.getElementById('zoomFotoNameMagang').innerText = name;
        document.getElementById('zoomFotoNipdMagang').innerText = nipd;
        document.getElementById('zoomFotoPerusahaanMagang').innerText = perusahaan || '-';
        $('#modalZoomFotoMagang').modal('show');
    }

    // ============================================================
    // SK DPL Functions
    // ============================================================
    var skParsedMahasiswa = [];

    function openModalSkDpl() {
        // Reset modal ke kondisi awal
        $('#sk_gdrive_link').val('');
        $('#sk-error-msg').hide().text('');
        $('#sk-step-2').hide();
        $('#sk-mahasiswa-tbody').html('');
        $('#sk-empty-msg').hide();
        $('#btn-simpan-sk').hide();
        $('#sk-check-all').prop('checked', false);
        $('#btn-proses-sk').html('<i class="fa fa-cogs"></i> Proses SK').prop('disabled', false);
        $('#sk-footer-info').text('');
        skParsedMahasiswa = [];
        $('#modalSkDpl').modal('show');
    }

    function prosesSKDpl() {
        var link = $('#sk_gdrive_link').val().trim();
        if (!link) {
            $('#sk-error-msg').text('Link Google Drive tidak boleh kosong.').show();
            return;
        }
        $('#sk-error-msg').hide();
        $('#btn-proses-sk').html('<i class="fa fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
        $('#sk-step-2').hide();
        $('#btn-simpan-sk').hide();

        $.ajax({
            url: '<?= site_url("C_laporan_magang/ajax_parse_sk_dpl") ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                link_gdrive: link,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                $('#btn-proses-sk').html('<i class="fa fa-cogs"></i> Proses SK').prop('disabled', false);

                if (!res.status) {
                    $('#sk-error-msg').text(res.msg).show();
                    return;
                }

                // Isi info SK
                $('#sk-no-sk').text('No. SK: ' + res.no_sk);
                $('#sk-perihal').text(res.perihal);
                skParsedNoSK = res.no_sk; // Simpan ke global scope

                var berlakuStr = '';
                if (res.tgl_berlaku) berlakuStr += 'Berlaku: ' + res.tgl_berlaku;
                if (res.tgl_berakhir) berlakuStr += ' s/d ' + res.tgl_berakhir;
                $('#sk-berlaku').text(berlakuStr);

                // Badge validitas
                var badgeHtml = '';
                if (res.validitas_legal === 'valid') {
                    badgeHtml = '<span style="background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;"><i class="fa fa-check-circle"></i> SK Valid</span>';
                } else {
                    badgeHtml = '<span style="background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;"><i class="fa fa-exclamation-circle"></i> Perlu Dicek</span>';
                }
                $('#sk-validitas-badge').html(badgeHtml);
                $('#sk-catatan').text(res.catatan_validasi || '');

                // Summary filter
                var filterMsg = '<i class="fa fa-filter" style="margin-right:6px;"></i>';
                filterMsg += 'Menampilkan mahasiswa dengan DPL: <strong>' + (res.nama_dosen_login || '-') + '</strong>';
                filterMsg += ' — Ditemukan <strong>' + (res.mahasiswa ? res.mahasiswa.length : 0) + '</strong> dari total ' + res.total_all + ' mahasiswa dalam SK.';
                $('#sk-summary-filter').html(filterMsg);

                // Render tabel mahasiswa
                skParsedMahasiswa = res.mahasiswa || [];
                var tbody = $('#sk-mahasiswa-tbody');
                tbody.empty();

                if (skParsedMahasiswa.length === 0) {
                    $('#sk-empty-msg').show();
                    $('table').closest('div').hide(); // sembunyikan tabel
                } else {
                    $('#sk-empty-msg').hide();
                    $.each(skParsedMahasiswa, function(i, mhs) {
                        var status = mhs.db_status || 'new'; // 'new', 'exists_no_dpl', 'exists_complete'
                        var rowBg = (i % 2 === 0) ? '#FFFFFF' : '#F8FAFC';
                        
                        var statusBadge = '';
                        var chkDisabled = '';
                        var chkChecked  = 'checked';

                        if (status === 'new') {
                            statusBadge = '<span title="Mahasiswa baru, akan ditambahkan" style="background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:700;">Baru</span>';
                        } else if (status === 'exists_no_dpl') {
                            statusBadge = '<span title="NIM sudah ada tapi DPL masih kosong. DPL akan diisi." style="background:#DBEAFE;color:#1E3A8A;border:1px solid #BFDBFE;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:700;">Update DPL</span>';
                        } else {
                            // exists_complete
                            statusBadge = '<span title="NIM dan DPL sudah lengkap di database" style="background:#F1F5F9;color:#475569;border:1px solid #E2E8F0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:700;">Sudah Ada</span>';
                            chkDisabled = 'disabled';
                            chkChecked  = '';
                        }
                        tbody.append(
                            '<tr style="background:' + rowBg + '; border-bottom: 1px solid #F1F5F9;">' +
                            '  <td style="padding:10px 12px;text-align:center;">' +
                            '    <input type="checkbox" class="sk-mhs-check" data-idx="' + i + '" ' + chkDisabled + ' ' + chkChecked + ' style="width:15px;height:15px;">' +
                            '  </td>' +
                            '  <td style="padding:10px 12px;color:#64748B;font-weight:600;">' + (mhs.no_urut || (i+1)) + '</td>' +
                            '  <td style="padding:10px 12px;font-weight:700;color:#1E293B;font-family:monospace;">' + (mhs.nim || '-') + '</td>' +
                            '  <td style="padding:10px 12px;color:#334155;">' + (mhs.nama_mahasiswa || '-') + '</td>' +
                            '  <td style="padding:10px 12px;color:#475569;font-size:12px;">' + (mhs.nama_perusahaan || '-') + '</td>' +
                            '  <td style="padding:10px 12px;">' + statusBadge + '</td>' +
                            '</tr>'
                        );
                    });
                    $('#btn-simpan-sk').css('display', 'inline-flex').show();
                    $('#sk-footer-info').text('Centang mahasiswa yang ingin ditambahkan ke daftar bimbingan Anda.');
                }

                $('#sk-step-2').show();
            },
            error: function() {
                $('#btn-proses-sk').html('<i class="fa fa-cogs"></i> Proses SK').prop('disabled', false);
                $('#sk-error-msg').text('Terjadi kesalahan sistem. Silakan coba lagi.').show();
            }
        });
    }

    function skToggleAll(checked) {
        $('.sk-mhs-check:not(:disabled)').prop('checked', checked);
    }

    function simpanSKDpl() {
        var selected = [];
        $('.sk-mhs-check:checked:not(:disabled)').each(function() {
            var idx = parseInt($(this).data('idx'));
            if (skParsedMahasiswa[idx]) {
                selected.push(skParsedMahasiswa[idx]);
            }
        });

        if (selected.length === 0) {
            alert('Pilih minimal satu mahasiswa untuk disimpan.');
            return;
        }

        if (!confirm('Simpan ' + selected.length + ' mahasiswa ke daftar bimbingan magang Anda?')) return;

        var $btn = $('#btn-simpan-sk');
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url("C_laporan_magang/ajax_save_sk_mahasiswa") ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                mahasiswa: JSON.stringify(selected),
                no_sk: typeof skParsedNoSK !== 'undefined' ? skParsedNoSK : '',
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                $btn.html('<i class="fa fa-save"></i> Simpan ke Daftar').prop('disabled', false);
                if (res.status) {
                    alert('✅ ' + res.msg);
                    if (res.saved > 0) {
                        // Reload halaman agar daftar mahasiswa terupdate
                        $('#modalSkDpl').modal('hide');
                        setTimeout(function() { window.location.reload(); }, 500);
                    }
                } else {
                    alert('Gagal: ' + res.msg);
                }
            },
            error: function() {
                $btn.html('<i class="fa fa-save"></i> Simpan ke Daftar').prop('disabled', false);
                alert('Terjadi kesalahan sistem saat menyimpan.');
            }
        });
    }
    // ============================================================

    function openPedoman() {
        $('#modalPedoman').modal('show');
        if (pedomanLoaded) return;

        $.ajax({
            url: '<?= site_url("C_laporan_magang/ajax_pedoman") ?>',
            type: 'GET',
            success: function(html) {
                $('#pedoman-content').html(html);
                $('#pedoman-loading').hide();
                $('#pedoman-content').show();
                pedomanLoaded = true;
            },
            error: function() {
                $('#pedoman-loading').html('<i class="fa fa-exclamation-triangle" style="color:#EF4444; font-size:24px; display:block; margin-bottom:8px;"></i><span style="font-size:13px; color:#64748B;">Gagal memuat pedoman. Silakan coba lagi.</span>');
            }
        });
    }

    function renderJsonToHtml(jsonString) {
        try {
            var data = JSON.parse(jsonString);
            var html = '';
            var labels = {
                'kesesuaian_nama': 'Kesesuaian Nama Pengusul',
                // Format lama (Proposal)
                'profil_dan_latar_belakang': 'Profil & Latar Belakang',
                'lokasi_dan_mitra': 'Lokasi & Mitra',
                'ruang_lingkup': 'Ruang Lingkup & Job Deskripsi',
                'waktu_pelaksanaan': 'Waktu Pelaksanaan',
                'target_capaian': 'Target Capaian',
                // Format baru (Formulir Pendaftaran)
                'masalah_mendorong': 'Masalah yang Mendorong Internship',
                'sumber_ide': 'Sumber Ide Internship',
                'kegunaan_manfaat': 'Kegunaan dan Manfaat bagi Mahasiswa',
                'hal_ingin_diketahui': 'Hal yang Ingin Diketahui',
                'mengapa_penting': 'Alasan Pentingnya Hal Tersebut',
                'tujuan_internship': 'Tujuan Internship bagi Mahasiswa',
                'pengetahuan_diperoleh': 'Pengetahuan yang Ingin Diperoleh',
                'keterampilan_diperoleh': 'Keterampilan yang Ingin Diperoleh',
                'catatan': 'Catatan Tambahan'
            };
            var icons = {
                'kesesuaian_nama': 'fa-id-card-o',
                // Format lama
                'profil_dan_latar_belakang': 'fa-bullseye',
                'lokasi_dan_mitra': 'fa-building',
                'ruang_lingkup': 'fa-briefcase',
                'waktu_pelaksanaan': 'fa-calendar',
                'target_capaian': 'fa-line-chart',
                // Format baru
                'masalah_mendorong': 'fa-exclamation-circle',
                'sumber_ide': 'fa-lightbulb-o',
                'kegunaan_manfaat': 'fa-gift',
                'hal_ingin_diketahui': 'fa-question-circle',
                'mengapa_penting': 'fa-exclamation',
                'tujuan_internship': 'fa-bullseye',
                'pengetahuan_diperoleh': 'fa-book',
                'keterampilan_diperoleh': 'fa-wrench',
                'catatan': 'fa-sticky-note-o'
            };

            html += '<div style="margin-bottom: 16px;">';
            
            for (var key in labels) {
                if (data[key]) {
                    html += '<div style="margin-bottom: 16px; padding: 16px; background: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">';
                    html += '<h6 style="margin: 0 0 8px 0; font-weight: 800; color: #4F46E5;"><i class="fa ' + icons[key] + '" style="margin-right: 6px;"></i> ' + labels[key] + '</h6>';
                    html += '<p style="margin: 0; color: #334155; line-height: 1.6; font-size: 13.5px;">' + data[key].replace(/\n/g, '<br>') + '</p>';
                    html += '</div>';
                }
            }
            html += '</div>';
            return html;
        } catch (e) {
            return '<div style="color:#DC2626; padding:12px; background:#FEE2E2; border-radius:8px; font-weight:600; margin-bottom:12px;"><i class="fa fa-exclamation-triangle"></i> Gagal membaca format JSON: ' + e.message + '</div>' + 
                   '<div style="background:#F1F5F9; padding:16px; border-radius:8px; font-size:12px; font-family:monospace; white-space:pre-wrap; border:1px solid #CBD5E1; color:#334155; overflow-x:auto;">' + 
                   String(jsonString).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + 
                   '</div>';
        }
    }

    function generateAiSummaryMagang(nipd) {
        var $btn = $('#btn_generate_ai_' + nipd);
        var $container = $('#ai_summary_container_' + nipd);
        
        $btn.html('<i class="fa fa-spinner fa-spin"></i> AI Sedang Membaca...').attr('disabled', true);
        
        $.ajax({
            url: "<?= site_url('C_laporan_magang/ajax_generate_ai_summary_proposal') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                nipd: nipd,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    var htmlContent = renderJsonToHtml(res.summary);
                    $container.find('.ai-empty-state-magang').hide();
                    $container.find('.ai-rendered-content-magang').html(htmlContent).show();
                    $container.find('.ai-chat-container-magang').show();
                    $container.find('.ai-raw-text-magang').val(res.summary);
                    $('#btn_reset_ai_' + nipd).show();
                } else {
                    $btn.html('<i class="fa fa-magic" style="margin-right: 6px;"></i> Mulai Meringkas').attr('disabled', false);
                    alert('Gagal: ' + res.msg);
                }
            },
            error: function() {
                $btn.html('<i class="fa fa-magic" style="margin-right: 6px;"></i> Mulai Meringkas').attr('disabled', false);
                alert('Terjadi kesalahan sistem saat menghubungi AI.');
            }
        });
    }

    function resetAiSummaryMagang(nipd) {
        if (!confirm('Hapus hasil ringkasan AI ini? Anda bisa men-generatenya lagi nanti.')) return;
        
        var $btnReset = $('#btn_reset_ai_' + nipd);
        $btnReset.html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);
        
        $.ajax({
            url: "<?= site_url('C_laporan_magang/ajax_reset_ai_summary_proposal') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                nipd: nipd,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    var $container = $('#ai_summary_container_' + nipd);
                    $container.find('.ai-rendered-content-magang').empty().hide();
                    $container.find('.ai-chat-container-magang').hide();
                    $container.find('.ai-raw-text-magang').val('');
                    $container.find('.ai-empty-state-magang').show();
                    
                    var $btn = $('#btn_generate_ai_' + nipd);
                    $btn.html('<i class="fa fa-magic" style="margin-right: 6px;"></i> Mulai Meringkas').attr('disabled', false);
                    
                    $btnReset.html('<i class="fa fa-refresh"></i> Reset').hide().attr('disabled', false);

                    // Bersihkan chat UI
                    var $msgs = $('#chat_messages_' + nipd);
                    $msgs.html('<div style="text-align: center; color: #94A3B8; font-size: 12px; font-style: italic; margin-top: 8px;">Ketik pesan Anda. Gunakan <strong>@dokumen</strong> jika ingin AI membaca isi berkas.</div>');
                    chatHistoryLoaded[nipd] = false;
                } else {
                    $btnReset.html('<i class="fa fa-refresh"></i> Reset').attr('disabled', false);
                    alert('Gagal: ' + res.msg);
                }
            },
            error: function() {
                $btnReset.html('<i class="fa fa-refresh"></i> Reset').attr('disabled', false);
                alert('Terjadi kesalahan sistem.');
            }
        });
    }

    // Track apakah history chat sudah di-load untuk setiap nipd
    var chatHistoryLoaded = {};

    function loadChatHistory(nipd) {
        if (chatHistoryLoaded[nipd]) return; // Jangan load 2x
        chatHistoryLoaded[nipd] = true;

        $.ajax({
            url: "<?= site_url('C_laporan_magang/ajax_load_chat_history') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                nipd: nipd,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                if (!res.status || !res.history || res.history.length === 0) return;

                var $msgs = $('#chat_messages_' + nipd);
                // Hapus placeholder
                $msgs.find('div[style*="font-style: italic"]').remove();

                // Label history
                $msgs.append(
                    '<div style="text-align:center; margin: 4px 0 10px; color:#94A3B8; font-size:11px;">'
                    + '<span style="background:#F1F5F9; padding:3px 10px; border-radius:10px; border:1px solid #E2E8F0;">'
                    + '<i class="fa fa-history" style="margin-right:4px;"></i>Riwayat percakapan sebelumnya'
                    + '</span></div>'
                );

                $.each(res.history, function(i, item) {
                    var time = '';
                    if (item.created_at) {
                        var d = new Date(item.created_at.replace(' ', 'T'));
                        time = '<div style="font-size:10px; color:#94A3B8; margin-top:4px; text-align:right;">'
                               + d.toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) + ' '
                               + d.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'})
                               + '</div>';
                    }

                    if (item.role === 'user') {
                        $msgs.append(
                            '<div style="align-self:flex-end; background:#EEF2FF; color:#1E293B; padding:10px 14px; border-radius:14px 14px 0 14px; max-width:85%; font-size:13px; line-height:1.5; border:1px solid #C7D2FE; opacity:0.85;">'
                            + $('<div>').text(item.message).html()
                            + time + '</div>'
                        );
                    } else {
                        $msgs.append(
                            '<div style="align-self:flex-start; background:#F8FAFC; color:#334155; padding:10px 14px; border-radius:14px 14px 14px 0; max-width:90%; font-size:13px; line-height:1.5; border:1px solid #E2E8F0; opacity:0.85;">'
                            + '<div style="font-weight:700; color:#4F46E5; margin-bottom:4px; font-size:11px; text-transform:uppercase;"><i class="fa fa-magic"></i> AI Assistant</div>'
                            + chatMarkdownToHtml(item.message)
                            + time + '</div>'
                        );
                    }
                });

                // Garis pemisah antara history dan chat baru
                $msgs.append(
                    '<div style="text-align:center; margin: 8px 0; color:#94A3B8; font-size:11px;">'
                    + '<span style="background:#F0FDF4; padding:3px 10px; border-radius:10px; border:1px solid #BBF7D0; color:#166534;">'
                    + '<i class="fa fa-comments" style="margin-right:4px;"></i>Sesi baru di bawah ini'
                    + '</span></div>'
                );

                $msgs.scrollTop($msgs[0].scrollHeight);
            }
        });
    }

    function chatMarkdownToHtml(text) {
        var escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var lines = escaped.split('\n');
        var html = '';
        var inList = false;

        lines.forEach(function(line) {
            var trimmed = line.trim();
            if (/^[-*+]\s+(.+)/.test(trimmed)) {
                if (!inList) { html += '<ul style="margin: 4px 0 4px 16px; padding: 0;">'; inList = true; }
                var itemContent = trimmed.replace(/^[-*+]\s+/, '').replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html += '<li style="margin-bottom: 4px;">' + itemContent + '</li>';
            } else if (/^\d+\.\s+(.+)/.test(trimmed)) {
                if (inList) { html += '</ul>'; inList = false; }
                var numContent = trimmed.replace(/^\d+\.\s+/, '').replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html += '<p style="margin: 3px 0;"><strong style="color:#374151;">' + trimmed.match(/^(\d+\.)/)[1] + '</strong> ' + numContent + '</p>';
            } else if (trimmed === '') {
                if (inList) { html += '</ul>'; inList = false; }
                html += '<div style="height: 6px;"></div>';
            } else {
                if (inList) { html += '</ul>'; inList = false; }
                var pContent = trimmed.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html += '<p style="margin: 3px 0;">' + pContent + '</p>';
            }
        });
        if (inList) html += '</ul>';
        return html;
    }

    // Chat history per nipd (like terminal arrow-up behavior)
    var chatHistoryMap = {};
    var chatHistoryCursorMap = {};

    function handleChatInputKey(event, nipd) {
        var $input = $('#chat_input_' + nipd);
        if (!chatHistoryMap[nipd]) chatHistoryMap[nipd] = [];
        if (!(nipd in chatHistoryCursorMap)) chatHistoryCursorMap[nipd] = -1;

        if (event.keyCode === 13) { // Enter
            event.preventDefault();
            sendChatMessageMagang(nipd);
        } else if (event.keyCode === 38) { // ArrowUp
            event.preventDefault();
            var history = chatHistoryMap[nipd];
            if (history.length === 0) return;
            var cursor = chatHistoryCursorMap[nipd];
            cursor = (cursor < history.length - 1) ? cursor + 1 : history.length - 1;
            chatHistoryCursorMap[nipd] = cursor;
            $input.val(history[history.length - 1 - cursor]);
        } else if (event.keyCode === 40) { // ArrowDown
            event.preventDefault();
            var history = chatHistoryMap[nipd];
            var cursor = chatHistoryCursorMap[nipd];
            if (cursor <= 0) {
                chatHistoryCursorMap[nipd] = -1;
                $input.val('');
            } else {
                cursor--;
                chatHistoryCursorMap[nipd] = cursor;
                $input.val(history[history.length - 1 - cursor]);
            }
        } else {
            // Reset cursor saat mengetik manual
            chatHistoryCursorMap[nipd] = -1;
        }
    }

    function handleChatInputAutocomplete(nipd) {
        var input = document.getElementById('chat_input_' + nipd);
        var cursorPosition = input.selectionStart;
        var textBeforeCursor = input.value.substring(0, cursorPosition);
        
        // Temukan spasi terakhir sebelum kursor untuk mendapatkan kata yang sedang diketik
        var lastSpaceIndex = textBeforeCursor.lastIndexOf(' ');
        var currentWord = textBeforeCursor.substring(lastSpaceIndex + 1).toLowerCase();

        var autocompleteBox = $('#autocomplete_' + nipd);
        if (currentWord === '@' || currentWord === '@d' || currentWord === '@do' || currentWord === '@dok' || currentWord === '@doku') {
            autocompleteBox.show();
        } else {
            autocompleteBox.hide();
        }
    }

    function selectAutocomplete(nipd, text) {
        var input = document.getElementById('chat_input_' + nipd);
        var val = input.value;
        var cursorPosition = input.selectionStart;
        var textBeforeCursor = val.substring(0, cursorPosition);
        var textAfterCursor = val.substring(cursorPosition);
        
        var lastSpaceIndex = textBeforeCursor.lastIndexOf(' ');
        
        // Ganti kata yang sedang diketik dengan teks autocomplete
        var newTextBeforeCursor = textBeforeCursor.substring(0, lastSpaceIndex + 1) + text;
        
        input.value = newTextBeforeCursor + textAfterCursor;
        input.selectionStart = input.selectionEnd = newTextBeforeCursor.length;
        input.focus();
        
        $('#autocomplete_' + nipd).hide();
    }

    function sendChatMessageMagang(nipd) {
        var $input = $('#chat_input_' + nipd);
        var question = $input.val().trim();
        if (!question) return;
        
        $('#autocomplete_' + nipd).hide();
        
        var $messages = $('#chat_messages_' + nipd);
        var $btn = $('#btn_send_chat_' + nipd);
        
        if ($messages.find('.fa-spinner').length > 0) return; // already loading
        
        if ($messages.text().indexOf('Silakan ajukan pertanyaan') > -1) {
            $messages.empty();
        }
        
        // User bubble
        var userBubble = '<div style="align-self: flex-end; background: #EEF2FF; color: #1E293B; padding: 10px 14px; border-radius: 14px 14px 0 14px; max-width: 85%; font-size: 13px; line-height: 1.5; border: 1px solid #C7D2FE;">' + 
                         $('<div>').text(question).html() + 
                         '</div>';
        $messages.append(userBubble);
        
        // AI loading bubble
        var loadingBubble = '<div class="ai-loading" style="align-self: flex-start; background: #FFFFFF; color: #64748B; padding: 10px 14px; border-radius: 14px 14px 14px 0; max-width: 85%; font-size: 13px; border: 1px solid #E2E8F0; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' + 
                            '<i class="fa fa-spinner fa-spin" style="color: #4F46E5; margin-right: 6px;"></i> Membaca dokumen...</div>';
        $messages.append(loadingBubble);
        $messages.scrollTop($messages[0].scrollHeight);
        
        // Simpan ke history
        if (!chatHistoryMap[nipd]) chatHistoryMap[nipd] = [];
        chatHistoryMap[nipd].push(question);
        chatHistoryCursorMap[nipd] = -1;

        $input.val('').attr('disabled', true);
        $btn.attr('disabled', true);
        
        $.ajax({
            url: "<?= site_url('C_laporan_magang/ajax_ask_ai_proposal') ?>",
            type: "POST",
            dataType: "JSON",
            data: {
                nipd: nipd,
                question: question,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(res) {
                $messages.find('.ai-loading').remove();
                $input.attr('disabled', false).focus();
                $btn.attr('disabled', false);
                
                if (res.status) {
                    var aiBubble = '<div style="align-self: flex-start; background: #FFFFFF; color: #334155; padding: 10px 14px; border-radius: 14px 14px 14px 0; max-width: 90%; font-size: 13px; line-height: 1.5; border: 1px solid #E2E8F0; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' + 
                                   '<div style="font-weight: 700; color: #4F46E5; margin-bottom: 4px; font-size: 11px; text-transform: uppercase;"><i class="fa fa-magic"></i> AI Assistant</div>' +
                                   chatMarkdownToHtml(res.answer) + 
                                   '</div>';
                    $messages.append(aiBubble);
                } else {
                    var errBubble = '<div style="align-self: flex-start; background: #FEE2E2; color: #DC2626; padding: 10px 14px; border-radius: 14px 14px 14px 0; max-width: 85%; font-size: 13px; border: 1px solid #FECACA;">' + 
                                    $('<div>').text(res.msg).html() + 
                                    '</div>';
                    $messages.append(errBubble);
                }
                $messages.scrollTop($messages[0].scrollHeight);
            },
            error: function() {
                $messages.find('.ai-loading').remove();
                $input.attr('disabled', false).focus();
                $btn.attr('disabled', false);
                
                var errBubble = '<div style="align-self: flex-start; background: #FEE2E2; color: #DC2626; padding: 10px 14px; border-radius: 14px 14px 14px 0; max-width: 85%; font-size: 13px; border: 1px solid #FECACA;"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan koneksi ke server.</div>';
                $messages.append(errBubble);
                $messages.scrollTop($messages[0].scrollHeight);
            }
        });
    }

    // PDF Zoom per nipd
    var pdfZoomLevel = {};

    function zoomPdf(nipd, delta) {
        if (!pdfZoomLevel[nipd]) pdfZoomLevel[nipd] = 1;
        var newZoom = Math.min(3, Math.max(0.4, pdfZoomLevel[nipd] + delta));
        pdfZoomLevel[nipd] = newZoom;
        _applyZoom(nipd);
    }

    function zoomPdfReset(nipd) {
        pdfZoomLevel[nipd] = 1;
        _applyZoom(nipd);
    }

    function _applyZoom(nipd) {
        var zoom   = pdfZoomLevel[nipd] || 1;
        var pct    = Math.round(zoom * 100);
        var $area  = $('#pdf_area_' + nipd);
        var $frame = $('#pdf_iframe_' + nipd);

        // Expand iframe beyond container then let container clip it
        $area.css('overflow', zoom > 1 ? 'auto' : 'hidden');
        $frame.css({
            'width'  : (zoom * 100) + '%',
            'height' : zoom > 1 ? (zoom * 100) + '%' : '100%'
        });
        $('#pdf_zoom_label_' + nipd).text(pct + '%');
    }

    // Track drawer open state per nipd
    var aiDrawerOpen = {};

    function toggleAiDrawer(nipd) {
        var $drawer  = $('#ai_drawer_'  + nipd);
        var $pdfArea = $('#pdf_area_'   + nipd);
        var $icon    = $('#btn_toggle_ai_icon_' + nipd);
        var $label   = $('#btn_toggle_ai_label_' + nipd);

        if (aiDrawerOpen[nipd]) {
            // Close — kembalikan PDF
            $drawer.css({ 'height': '0', 'border-top-width': '0' });
            $pdfArea.css({ 'flex': '1', 'height': '', 'display': '' });
            $icon.css('transform', 'rotate(0deg)');
            $label.text('AI Summary');
            aiDrawerOpen[nipd] = false;
        } else {
            // Open — full tinggi, PDF disembunyikan
            var parentH = $drawer.parent().height();
            $pdfArea.css({ 'flex': 'none', 'height': '0', 'display': 'none' });
            $drawer.css({ 'height': parentH + 'px', 'border-top-width': '0' });
            $icon.css('transform', 'rotate(180deg)');
            $label.text('Lihat Dokumen');
            aiDrawerOpen[nipd] = true;

            // Load history chat dari DB (hanya sekali)
            loadChatHistory(nipd);

            // Scroll chat ke bawah
            var $msgs = $('#chat_messages_' + nipd);
            if ($msgs.length) setTimeout(function() { $msgs.scrollTop($msgs[0].scrollHeight); }, 300);
            // Fokus input chat
            setTimeout(function() { $('#chat_input_' + nipd).focus(); }, 400);
        }
    }

    function filterMahasiswa() {
        var input = document.getElementById('search-mahasiswa');
        var filter = input.value.toLowerCase().trim();
        var cards = document.getElementsByClassName('student-magang-card');
        var clearBtn = document.getElementById('clear-search');
        var grid = document.querySelector('.magang-grid');
        
        // Show/hide clear button
        if (filter.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
        
        var visibleCount = 0;
        
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            var name = card.querySelector('.student-name').textContent.toLowerCase();
            var nipd = card.querySelector('.student-nipd').textContent.toLowerCase();
            
            if (name.includes(filter) || nipd.includes(filter)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        }
        
        // Handle no results message
        var noResults = document.getElementById('no-search-results');
        if (visibleCount === 0 && cards.length > 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.id = 'no-search-results';
                noResults.style.cssText = 'grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #64748B; background: #FFFFFF; border-radius: 16px; border: 2px dashed #E2E8F0; width: 100%;';
                noResults.innerHTML = '<i class="fa fa-search-minus" style="font-size: 54px; margin-bottom: 12px; color: #94A3B8;"></i>' +
                                      '<p style="font-weight: 700; margin: 0; font-size: 15px;">Mahasiswa dengan nama atau NIM tersebut tidak ditemukan.</p>';
                grid.appendChild(noResults);
            } else {
                noResults.style.display = 'block';
            }
        } else {
            if (noResults) {
                noResults.style.display = 'none';
            }
        }
    }
    
    function clearSearch() {
        var input = document.getElementById('search-mahasiswa');
        input.value = '';
        filterMahasiswa();
        input.focus();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Render existing markdown
        $('.ai-raw-text-magang').each(function() {
            var raw = $(this).val();
            if (raw && raw.trim().length > 0) {
                $(this).siblings('.ai-rendered-content-magang').html(renderJsonToHtml(raw));
            }
        });

        $('.modal').on('show.bs.modal', function (e) {
            var iframe = $(this).find('iframe');
            if (iframe.length > 0) {
                var dataSrc = iframe.attr('data-src');
                if (dataSrc && !iframe.attr('src')) {
                    iframe.attr('src', dataSrc);
                }
            }
        });
        $('.modal').on('hidden.bs.modal', function (e) {
            var iframe = $(this).find('iframe');
            if (iframe.length > 0) {
                iframe.attr('src', '');
            }
            // Reset drawer state on close
            var nipd = this.id.replace('modalProposal', '');
            if (nipd) {
                aiDrawerOpen[nipd] = false;
                $('#ai_drawer_'  + nipd).css('height', '0');
                $('#pdf_area_'   + nipd).css({ 'flex': '1', 'height': '', 'display': '' });
                $('#btn_toggle_ai_icon_'  + nipd).css('transform', 'rotate(0deg)');
                $('#btn_toggle_ai_label_' + nipd).text('AI Summary');
            }
        });
    });
</script>