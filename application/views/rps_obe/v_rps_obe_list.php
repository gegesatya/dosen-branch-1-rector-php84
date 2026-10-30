<style>
  /* Dashboard & Table Styling (WBI Corporate Green Theme) */
  .rps-dashboard-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
  }
  .rps-header-banner {
    background: #1b4332;
    color: #ffffff;
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    border-top-left-radius: 7px;
    border-top-right-radius: 7px;
    border-bottom: 1px solid #143527;
  }
  .rps-header-title h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .rps-header-title p {
    margin: 0;
    font-size: 13px;
    color: #a7f3d0;
  }
  .rps-header-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
  }
  .btn-rps-primary {
    background-color: #2d6a4f !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 13px;
    border: 1px solid #40916c !important;
    border-radius: 4px;
    padding: 7px 15px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: background-color 0.15s ease;
  }
  .btn-rps-primary:hover {
    background-color: #40916c !important;
    color: #ffffff !important;
  }
  .btn-rps-outline {
    background-color: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    font-weight: 500;
    font-size: 13px;
    border-radius: 4px;
    padding: 7px 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: background-color 0.15s ease;
  }
  .btn-rps-outline:hover {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
  }

  /* Info Strip */
  .rps-stats-bar {
    background: #f0fdf4;
    border-bottom: 1px solid #dcfce7;
    padding: 10px 24px;
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    align-items: center;
  }
  .rps-stat-item {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    color: #1b4332;
  }

  /* Table Customization */
  .table-container {
    padding: 16px 20px 20px 20px;
  }
  #datatable_rps_obe {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100% !important;
    margin: 0 !important;
  }
  #datatable_rps_obe thead th {
    background: #f8fafc !important;
    color: #1b4332;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 11px 12px !important;
    vertical-align: middle;
  }
  #datatable_rps_obe tbody td {
    padding: 12px 12px !important;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0 !important;
    font-size: 13px;
    background: #ffffff;
  }
  #datatable_rps_obe tbody tr:hover td {
    background-color: #f8fafc !important;
  }

  /* Robust Action Buttons (Immune to theme overrides) */
  .btn-action-view {
    background-color: #1b4332 !important;
    color: #ffffff !important;
    border: 1px solid #143527 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    text-shadow: none !important;
  }
  .btn-action-view:hover {
    background-color: #2d6a4f !important;
    color: #ffffff !important;
  }
  .btn-action-studio {
    background-color: #2d6a4f !important;
    color: #ffffff !important;
    border: 1px solid #1b4332 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    text-decoration: none !important;
    cursor: pointer !important;
    text-shadow: none !important;
  }
  .btn-action-studio:hover {
    background-color: #40916c !important;
    color: #ffffff !important;
  }
  .btn-action-delete {
    background-color: #fff1f2 !important;
    color: #be123c !important;
    border: 1px solid #fda4af !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 5px 10px !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    cursor: pointer !important;
    text-shadow: none !important;
  }
  .btn-action-delete:hover {
    background-color: #fee2e2 !important;
    color: #9f1239 !important;
    border-color: #f87171 !important;
  }

  .dataTables_wrapper {
    width: 100%;
  }
  .dataTables_filter {
    margin-bottom: 12px;
  }
  .dataTables_filter input {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 10px;
    outline: none;
    font-size: 13px;
  }
  .dataTables_filter input:focus {
    border-color: #1b4332;
    box-shadow: 0 0 0 2px rgba(27, 67, 50, 0.2);
  }
  .dataTables_length {
    margin-bottom: 12px;
  }
  .dataTables_length select {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 13px;
    outline: none;
  }
</style>

<div class="page-content" style="padding-left: 20px;">
  <div class="row">
    <div class="col-md-12">
      <div class="rps-dashboard-card">
        
        <!-- HEADER (WBI CORPORATE GREEN) -->
        <div class="rps-header-banner">
          <div class="rps-header-title">
            <h3><i class="fa fa-graduation-cap" style="color: #6ee7b7;"></i> <?= $judul_besar; ?></h3>
            <p>Standar Outcome-Based Education (SN-Dikti 2026 & SPMI WBI)</p>
          </div>
          <div class="rps-header-actions">
            <a href="<?= site_url('C_rps_generator'); ?>" class="btn-rps-primary">
              <i class="fa fa-magic"></i> Susun RPS Baru
            </a>
            <button class="btn-rps-outline" onclick="reload_table()" title="Muat Ulang Tabel">
              <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="javascript:void(0)" class="btn-rps-outline" data-toggle="modal" data-target="#apiKeyModal" title="Pengaturan Kunci AI">
              <i class="fa fa-key"></i> Kunci AI
            </a>
          </div>
        </div>

        <!-- INFO STRIP -->
        <div class="rps-stats-bar">
          <div class="rps-stat-item">
            <i class="fa fa-cube" style="color: #1b4332;"></i> <span><strong>Taksonomi Bloom:</strong> Level Analisis (C4) s.d. Kreasi (C6)</span>
          </div>
          <div class="rps-stat-item">
            <i class="fa fa-file-text-o" style="color: #2d6a4f;"></i> <span><strong>Dokumen Terintegrasi:</strong> RPS OBE, Kontrak Kuliah, & Rancangan Tugas</span>
          </div>
          <div class="rps-stat-item">
            <i class="fa fa-map-signs" style="color: #b45309;"></i> <span><strong>Roadmap:</strong> 16 Minggu Pembelajaran & Evaluasi</span>
          </div>
        </div>

        <!-- TABLE CONTAINER -->
        <div class="table-container">
          <table id="datatable_rps_obe" class="table table-hover" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">No</th>
                <th style="min-width: 260px;">Mata Kuliah & Info Kurikulum</th>
                <th style="min-width: 270px;">Sasaran & Capaian Belajar (CPMK)</th>
                <th style="width: 270px; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL RPS VIEW (HUB DOKUMEN PEMBELAJARAN) -->
<div class="modal fade" id="modalRpsView" tabindex="-1" role="dialog" aria-labelledby="modalRpsViewTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 820px; margin: 30px auto;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);">
      
      <!-- Modal Header (WBI GREEN) -->
      <div class="modal-header" style="background: #1b4332; color: #ffffff; padding: 16px 20px; border-bottom: 1px solid #143527;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8; font-size: 22px; text-shadow: none;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalRpsViewTitle" style="font-weight: 700; font-size: 16px; display: flex; align-items: center; gap: 8px; color: #ffffff; margin: 0;">
          <i class="fa fa-graduation-cap" style="color: #6ee7b7;"></i> Dokumen Pembelajaran & Pratinjau RPS
        </h4>
        <p style="margin: 3px 0 0 0; font-size: 12px; color: #a7f3d0;">
          Standar Outcome-Based Education (SN-Dikti 2026 & SPMI WBI)
        </p>
      </div>

      <!-- Modal Body -->
      <div class="modal-body" style="padding: 20px; background: #f8fafc;">
        
        <!-- Course Info Box -->
        <div style="background: #ffffff; border-radius: 6px; padding: 16px 18px; border: 1px solid #e2e8f0; margin-bottom: 18px;">
          <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
              <span id="modalViewKodeMk" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 12px; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe;">[MK]</span>
              <h4 id="modalViewNmMk" style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.3;">Nama Mata Kuliah</h4>
            </div>
            <div style="font-size: 12px; color: #475569; display: flex; gap: 8px; align-items: center; margin-top: 5px; flex-wrap: wrap;">
              <span id="modalViewSks"><i class="fa fa-graduation-cap" style="color:#2563eb;"></i> <strong>3 SKS</strong></span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewSemester"><i class="fa fa-clone" style="color:#475569;"></i> Semester -</span>
              <span style="color:#cbd5e1;">•</span>
              <span id="modalViewStatusBadge"></span>
            </div>
          </div>

          <!-- Deskripsi / CPMK -->
          <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
            <div style="font-size: 11px; font-weight: 700; color: #0369a1; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 5px;">
              <i class="fa fa-bullseye"></i> Deskripsi & Sasaran Pembelajaran (CPMK)
            </div>
            <div id="modalViewDeskripsi" style="font-size: 12.5px; color: #1e293b; line-height: 1.5; max-height: 120px; overflow-y: auto; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border: 1px solid #e2e8f0;">
              Deskripsi mata kuliah...
            </div>
          </div>
        </div>

        <!-- Section Title -->
        <div style="font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
          <i class="fa fa-folder-open-o text-primary"></i> Pilih Dokumen untuk Dibuka / Diunduh:
        </div>

        <!-- Document Grid (5 Cards) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px;">
          
          <!-- Doc 1: RPS Full -->
          <a id="btnDocRpsFull" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #bae6fd; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #e0f2fe; color: #0284c7; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-pdf-o"></i>
                  </span>
                  <div>
                    <strong style="color: #0369a1; font-size: 13.5px; display: block;">RPS Full (Lengkap)</strong>
                    <span style="font-size: 11px; color: #64748b;">Standar SN-Dikti 2026</span>
                  </div>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Dokumen RPS lengkap: identitas, CPL, CPMK, matriks 16 minggu, evaluasi & pustaka.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 12px; border-radius: 4px; background-color: #0284c7; color: #ffffff !important; padding: 6px 10px; border: 1px solid #0369a1;">
                <i class="fa fa-external-link"></i> Buka RPS Full
              </span>
            </div>
          </a>

          <!-- Doc 2: Kontrak Perkuliahan -->
          <a id="btnDocKontrak" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #e0e7ff; color: #4338ca; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-text-o"></i>
                  </span>
                  <div>
                    <strong style="color: #3730a3; font-size: 13.5px; display: block;">Kontrak Kuliah</strong>
                    <span style="font-size: 11px; color: #64748b;">Standar SPMI WBI</span>
                  </div>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Pedoman hak & kewajiban mahasiswa, tata tertib, jadwal, dan skema evaluasi nilai.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 12px; border-radius: 4px; background-color: #4338ca; color: #ffffff !important; padding: 6px 10px; border: 1px solid #3730a3;">
                <i class="fa fa-external-link"></i> Buka Kontrak
              </span>
            </div>
          </a>

          <!-- Doc 3: Rancangan Tugas -->
          <a id="btnDocTugas" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #99f6e4; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #ccfbf1; color: #0f766e; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-tasks"></i>
                  </span>
                  <div>
                    <strong style="color: #0f766e; font-size: 13.5px; display: block;">Rancangan Tugas</strong>
                    <span style="font-size: 11px; color: #64748b;">SCL / PjBL / Kasus</span>
                  </div>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Instruksi tugas mandiri & kelompok, batasan pengerjaan, dan rubrik penilaian.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 12px; border-radius: 4px; background-color: #0f766e; color: #ffffff !important; padding: 6px 10px; border: 1px solid #115e59;">
                <i class="fa fa-external-link"></i> Buka Rancangan Tugas
              </span>
            </div>
          </a>

          <!-- Doc 5: Roadmap Belajar -->
          <a id="btnDocRoadmapPreview" href="#" target="_blank" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #fde68a; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #fef3c7; color: #b45309; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-sitemap"></i>
                  </span>
                  <div>
                    <strong style="color: #92400e; font-size: 13.5px; display: block;">Roadmap Belajar</strong>
                    <span style="font-size: 11px; color: #64748b;">Peta Jalan 16 Minggu</span>
                  </div>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Peta jalan modul, durasi waktu TM/Praktek, serta alur pencapaian sub-CPMK.
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 12px; border-radius: 4px; background-color: #b45309; color: #ffffff !important; padding: 6px 10px; border: 1px solid #92400e;">
                <i class="fa fa-external-link"></i> Buka Roadmap
              </span>
            </div>
          </a>

          <!-- Doc 5: Ekspor Excel (13 Kolom) -->
          <a id="btnDocExcel" href="#" style="text-decoration: none; display: flex;">
            <div style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px; width: 100%; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                  <span style="background: #dcfce7; color: #15803d; width: 30px; height: 30px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="fa fa-file-excel-o"></i>
                  </span>
                  <div>
                    <strong style="color: #166534; font-size: 13.5px; display: block;">Matriks Excel 13 Kolom</strong>
                    <span style="font-size: 11px; color: #64748b;">Format Arsip SPMI</span>
                  </div>
                </div>
                <p style="font-size: 11.5px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                  Ekspor data matriks 13 kolom RPS lengkap ke file spreadsheet Excel (.xlsx).
                </p>
              </div>
              <span style="display: block; text-align: center; font-weight: 600; font-size: 12px; border-radius: 4px; background-color: #15803d; color: #ffffff !important; padding: 6px 10px; border: 1px solid #166534;">
                <i class="fa fa-download"></i> Unduh Excel RPS
              </span>
            </div>
          </a>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center;">
        <a id="btnModalStudioRps" href="#" class="btn btn-default" style="font-weight: 600; font-size: 12.5px; color: #0f766e; border: 1px solid #99f6e4; background: #f0fdfa;">
          <i class="fa fa-paint-brush"></i> Buka di Studio RPS
        </a>
        <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600; font-size: 12.5px; padding: 6px 18px;">
          Tutup
        </button>
      </div>

    </div>
  </div>
</div>
