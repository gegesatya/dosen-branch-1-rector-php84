<style>
  .cell-stage {
    padding: 6px 4px;
    min-width: 140px;
  }
  .table > tbody > tr > td {
    vertical-align: middle !important;
  }
  .table thead th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    font-size: 11px !important;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0 !important;
  }

  /* Modal Styling */
  .modal-dialog-custom {
    max-width: 900px;
    width: 92%;
    margin: 30px auto;
  }
  .modal-student-header {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 16px;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  }
  .modal-student-avatar {
    width: 65px;
    height: 65px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0d9488, #0f766e);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 1px;
    flex-shrink: 0;
    box-shadow: 0 3px 6px rgba(13, 148, 136, 0.25);
  }
  .modal-student-header h4 {
    margin: 0 0 6px 0;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: 0.3px;
  }
  .modal-student-header p.title {
    margin: 0;
    font-size: 12px;
    color: #334155;
    line-height: 1.45;
    font-style: italic;
    background: #f1f5f9;
    padding: 6px 10px;
    border-radius: 0 6px 6px 0;
    border-left: 3.5px solid #0D9488;
    white-space: normal;
    word-wrap: break-word;
    word-break: break-word;
  }
  .modal-student-header div:last-child {
    flex: 1;
    min-width: 0;
  }

  /* Modern Tab Menu in Modal */
  .nav-tabs-modern {
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 16px;
  }
  .nav-tabs-modern > li > a {
    border: 1px solid transparent;
    border-bottom: none;
    border-radius: 6px 6px 0 0 !important;
    font-weight: 600;
    font-size: 13px;
    color: #64748b;
    padding: 10px 18px;
    background: #f8fafc;
    transition: all 0.15s;
    margin-right: 4px;
  }
  .nav-tabs-modern > li > a:hover {
    background: #e2e8f0;
    color: #1e293b;
  }
  .nav-tabs-modern > li.active > a,
  .nav-tabs-modern > li.active > a:focus,
  .nav-tabs-modern > li.active > a:hover {
    background: #ffffff !important;
    color: #0d9488 !important;
    border: 2px solid #0d9488 !important;
    border-bottom-color: #ffffff !important;
    font-weight: 700;
  }

  /* Note Cards */
  .sempro-note-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 12px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    overflow: hidden;
  }
  .sempro-note-header {
    background: #f8fafc;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
  }
  .sempro-note-body {
    padding: 12px 14px;
    font-size: 12px;
    color: #334155;
  }
  .sempro-note-item {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
  }
  .sempro-note-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  .sempro-note-label {
    font-weight: 700;
    color: #0d9488;
    font-size: 11.5px;
    margin-bottom: 3px;
  }
</style>

<div class="page-content" style="padding-top: 0;">

  <!-- KPI Summary Cards -->
  <div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <div class="dashboard-stat2" style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 15px;">
        <div class="display" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
          <div class="number">
            <h3 class="font-blue-sharp" style="margin: 0; font-size: 28px; font-weight: 800; color: #0284c7;">
              <span><?= isset($kpi['total']) ? (int)$kpi['total'] : 0; ?></span>
            </h3>
            <small style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Mahasiswa</small>
          </div>
          <div class="icon" style="width: 48px; height: 48px; border-radius: 10px; background: #e0f2fe; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-users" style="font-size: 22px; color: #0284c7;"></i>
          </div>
        </div>
        <div class="progress-info" style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
          Mahasiswa aktif bimbingan / ujian
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <div class="dashboard-stat2" style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 15px;">
        <div class="display" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
          <div class="number">
            <h3 class="font-green-sharp" style="margin: 0; font-size: 28px; font-weight: 800; color: #10b981;">
              <span><?= isset($kpi['sempro_done']) ? (int)$kpi['sempro_done'] : 0; ?></span>
            </h3>
            <small style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Sudah SEMPRO</small>
          </div>
          <div class="icon" style="width: 48px; height: 48px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-check-circle" style="font-size: 22px; color: #10b981;"></i>
          </div>
        </div>
        <div class="progress-info" style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
          Telah dinilai / selesai Sempro
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <div class="dashboard-stat2" style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 15px;">
        <div class="display" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
          <div class="number">
            <h3 class="font-yellow-gold" style="margin: 0; font-size: 28px; font-weight: 800; color: #f59e0b;">
              <span><?= isset($kpi['semhas_done']) ? (int)$kpi['semhas_done'] : 0; ?></span>
            </h3>
            <small style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Sudah SEMHAS</small>
          </div>
          <div class="icon" style="width: 48px; height: 48px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-file-text-o" style="font-size: 22px; color: #f59e0b;"></i>
          </div>
        </div>
        <div class="progress-info" style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
          Telah dinilai / selesai Semhas
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <div class="dashboard-stat2" style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 15px;">
        <div class="display" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
          <div class="number">
            <h3 class="font-purple-sharp" style="margin: 0; font-size: 28px; font-weight: 800; color: #7c3aed;">
              <span><?= isset($kpi['sidang_done']) ? (int)$kpi['sidang_done'] : 0; ?></span>
            </h3>
            <small style="color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Sudah SIDANG</small>
          </div>
          <div class="icon" style="width: 48px; height: 48px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-graduation-cap" style="font-size: 22px; color: #7c3aed;"></i>
          </div>
        </div>
        <div class="progress-info" style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
          Telah dinilai / lulus Sidang Skripsi
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Table Section -->
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light bordered" style="border-radius: 12px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-list-alt font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase"><?= $judul_besar; ?></span>
            <span class="caption-helper"><?= $judul_kecil; ?></span>
          </div>
          <div class="actions">
            <button class="btn btn-default btn-sm blue-stripe" onclick="reload_table()">
              <i class="fa fa-refresh"></i> Refresh Data
            </button>
          </div>
        </div>

        <div class="portlet-body">
          <!-- DataTable -->
          <div class="table-container table-responsive">
            <table id="tbl_nilai_berita_acara" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width: 40px; text-align: center;">No</th>
                  <th style="width: 110px; text-align: center;">NIM</th>
                  <th style="min-width: 250px;">Nama Mahasiswa & Judul TA</th>
                  <th style="width: 170px; text-align: center;">SEMPRO</th>
                  <th style="width: 170px; text-align: center;">SEMHAS</th>
                  <th style="width: 170px; text-align: center;">SIDANG</th>
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
</div>

<!-- ================= MODAL DETAIL NILAI & CATATAN ================= -->
<div class="modal fade" id="modal_detail_nilai" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-custom" role="document">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: 2px;">&times;</button>
        <h4 class="modal-title" id="modal_detail_title" style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">
          <i class="fa fa-list-alt text-primary"></i> Riwayat & Rincian Nilai Ujian
        </h4>
      </div>
      <div class="modal-body" style="padding: 18px 20px; background: #ffffff;">
        
        <!-- Student Header Card -->
        <div class="modal-student-header">
          <div class="modal-student-avatar" id="modal_student_avatar">
            YS
          </div>
          <div>
            <h4>[<span id="modal_student_nim"></span>] <span id="modal_student_name"></span></h4>
            <p class="title" id="modal_student_title">Judul Skripsi Mahasiswa</p>
          </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs nav-tabs-modern" id="modal_detail_tabs">
          <li class="active">
            <a data-toggle="tab" href="#tab_detail_sempro" id="tab_btn_sempro">
              Record Seminar Proposal <span id="badge_tab_sempro" class="badge" style="background-color: #0284c7; font-size: 10.5px; margin-left: 4px;">-</span>
            </a>
          </li>
          <li>
            <a data-toggle="tab" href="#tab_detail_semhas" id="tab_btn_semhas">
              Record Seminar Hasil <span id="badge_tab_semhas" class="badge" style="background-color: #0284c7; font-size: 10.5px; margin-left: 4px;">-</span>
            </a>
          </li>
          <li>
            <a data-toggle="tab" href="#tab_detail_sidang" id="tab_btn_sidang">
              Record Sidang TA <span id="badge_tab_sidang" class="badge" style="background-color: #0284c7; font-size: 10.5px; margin-left: 4px;">-</span>
            </a>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" style="border: none; padding: 0; background: transparent;">
          
          <!-- ================= 1. TAB SEMPRO ================= -->
          <div id="tab_detail_sempro" class="tab-pane fade in active">
            <!-- Info Ringkasan -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
              <div class="row">
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Rata-rata Nilai Penguji:</div>
                  <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    <span id="sempro_stat_rerata">-</span> <small style="font-size: 12px; font-weight: normal; color: #64748b;">/ 7</small>
                  </div>
                </div>
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Hasil Sempro:</div>
                  <div id="sempro_stat_hasil" style="margin-top: 5px;">-</div>
                </div>
                <div class="col-sm-4" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Jadwal & Tempat:</div>
                  <div id="sempro_stat_jadwal" style="font-weight: 600; color: #334155; margin-top: 3px; font-size: 12px;">-</div>
                </div>
                <div class="col-sm-2 text-center" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                  <div id="sempro_stat_dokumen"></div>
                  <div id="sempro_stat_ba"></div>
                </div>
              </div>
            </div>

            <!-- Tabel Nilai Sempro -->
            <div style="margin-bottom: 20px;">
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px;">
                Rincian Nilai Penguji Seminar Proposal
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 4px;">
                  <thead>
                    <tr style="background: #f1f5f9; color: #334155;">
                      <th style="width: 35px; text-align: center;">No</th>
                      <th>Dosen Penguji</th>
                      <th style="text-align: center; width: 65px;" title="Latar Belakang (25%)">A (25%)</th>
                      <th style="text-align: center; width: 65px;" title="Perumusan Masalah (20%)">B (20%)</th>
                      <th style="text-align: center; width: 65px;" title="Tinjauan Pustaka (25%)">C (25%)</th>
                      <th style="text-align: center; width: 65px;" title="Kerangka Konsep (15%)">D (15%)</th>
                      <th style="text-align: center; width: 65px;" title="Metode Penelitian (15%)">E (15%)</th>
                      <th style="text-align: center; width: 85px;">Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody id="sempro_table_body">
                  </tbody>
                  <tfoot>
                    <tr style="background: #f8fafc; font-weight: bold;">
                      <td colspan="7" style="text-align: right; padding-right: 12px;">Rata-rata Nilai Penguji:</td>
                      <td style="text-align: center; font-size: 13px; color: #0f766e;" id="sempro_table_rerata_val">0</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <small class="text-muted" style="display: block; font-size: 11px; margin-top: 4px;">
                * Bobot: <b>A</b> (Latar Belakang 25%), <b>B</b> (Perumusan Masalah 20%), <b>C</b> (Tinjauan Pustaka 25%), <b>D</b> (Kerangka Konsep 15%), <b>E</b> (Metode Penelitian 15%).
              </small>
            </div>

            <!-- Catatan Usulan Revisi Sempro -->
            <div>
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 10px;">
                Catatan Usulan Revisi Sempro dari Penguji
              </div>
              <div id="sempro_catatan_container">
              </div>
            </div>
          </div>

          <!-- ================= 2. TAB SEMHAS ================= -->
          <div id="tab_detail_semhas" class="tab-pane fade">
            <!-- Info Ringkasan -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
              <div class="row">
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Rata-rata Nilai Penguji:</div>
                  <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    <span id="semhas_stat_rerata">-</span> <small style="font-size: 12px; font-weight: normal; color: #64748b;">/ 7</small>
                  </div>
                </div>
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Hasil Semhas:</div>
                  <div id="semhas_stat_hasil" style="margin-top: 5px;">-</div>
                </div>
                <div class="col-sm-4" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Jadwal & Tempat:</div>
                  <div id="semhas_stat_jadwal" style="font-weight: 600; color: #334155; margin-top: 3px; font-size: 12px;">-</div>
                </div>
                <div class="col-sm-2 text-center" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                  <div id="semhas_stat_dokumen"></div>
                  <div id="semhas_stat_ba"></div>
                </div>
              </div>
            </div>

            <!-- Tabel Nilai Semhas -->
            <div style="margin-bottom: 20px;">
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px;">
                Rincian Nilai Penguji Seminar Hasil
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 4px;">
                  <thead>
                    <tr style="background: #f1f5f9; color: #334155;">
                      <th style="width: 35px; text-align: center;">No</th>
                      <th>Dosen Penguji</th>
                      <th style="text-align: center; width: 60px;" title="Latar Belakang & Perumusan Masalah (20%)">A (20%)</th>
                      <th style="text-align: center; width: 60px;" title="Tinjauan Pustaka (10%)">B (10%)</th>
                      <th style="text-align: center; width: 60px;" title="Kerangka Konseptual (15%)">C (15%)</th>
                      <th style="text-align: center; width: 60px;" title="Metode Penelitian (15%)">D (15%)</th>
                      <th style="text-align: center; width: 60px;" title="Hasil & Pembahasan (30%)">E (30%)</th>
                      <th style="text-align: center; width: 60px;" title="Kemampuan Menjelaskan (10%)">F (10%)</th>
                      <th style="text-align: center; width: 85px;">Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody id="semhas_table_body">
                  </tbody>
                  <tfoot>
                    <tr style="background: #f8fafc; font-weight: bold;">
                      <td colspan="8" style="text-align: right; padding-right: 12px;">Rata-rata Nilai Penguji:</td>
                      <td style="text-align: center; font-size: 13px; color: #0f766e;" id="semhas_table_rerata_val">0</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <small class="text-muted" style="display: block; font-size: 11px; margin-top: 4px;">
                * Bobot: <b>A</b> (Latar Belakang & Perumusan Masalah 20%), <b>B</b> (Tinjauan Pustaka 10%), <b>C</b> (Kerangka Konseptual 15%), <b>D</b> (Metode Penelitian 15%), <b>E</b> (Hasil & Pembahasan 30%), <b>F</b> (Kemampuan Menjelaskan 10%).
              </small>
            </div>

            <!-- Catatan Usulan Revisi Semhas -->
            <div>
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 10px;">
                Catatan Usulan Revisi Semhas dari Penguji
              </div>
              <div id="semhas_catatan_container">
              </div>
            </div>
          </div>

          <!-- ================= 3. TAB SIDANG ================= -->
          <div id="tab_detail_sidang" class="tab-pane fade">
            <!-- Info Ringkasan -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
              <div class="row">
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Rata-rata Nilai Penguji:</div>
                  <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    <span id="sidang_stat_rerata">-</span> <small style="font-size: 12px; font-weight: normal; color: #64748b;">/ 7</small>
                  </div>
                </div>
                <div class="col-sm-3" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Hasil Sidang TA:</div>
                  <div id="sidang_stat_hasil" style="margin-top: 5px;">-</div>
                </div>
                <div class="col-sm-4" style="border-right: 1px solid #e2e8f0;">
                  <div style="color: #64748b; font-size: 11px;">Jadwal & Tempat:</div>
                  <div id="sidang_stat_jadwal" style="font-weight: 600; color: #334155; margin-top: 3px; font-size: 12px;">-</div>
                </div>
                <div class="col-sm-2 text-center" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                  <div id="sidang_stat_dokumen"></div>
                  <div id="sidang_stat_ba"></div>
                </div>
              </div>
            </div>

            <!-- Tabel Nilai Sidang -->
            <div style="margin-bottom: 20px;">
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px;">
                Rincian Nilai Penguji Sidang Tugas Akhir
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 4px;">
                  <thead>
                    <tr style="background: #f1f5f9; color: #334155;">
                      <th style="width: 35px; text-align: center;">No</th>
                      <th>Dosen Penguji</th>
                      <th style="text-align: center; width: 65px;" title="Latar Belakang (25%)">A (25%)</th>
                      <th style="text-align: center; width: 65px;" title="Tinjauan Pustaka (15%)">B (15%)</th>
                      <th style="text-align: center; width: 65px;" title="Metodologi Penelitian (25%)">C (25%)</th>
                      <th style="text-align: center; width: 65px;" title="Hasil Penelitian & Pembahasan (25%)">D (25%)</th>
                      <th style="text-align: center; width: 65px;" title="Format & Sikap (10%)">E (10%)</th>
                      <th style="text-align: center; width: 85px;">Nilai Akhir</th>
                    </tr>
                  </thead>
                  <tbody id="sidang_table_body">
                  </tbody>
                  <tfoot>
                    <tr style="background: #f8fafc; font-weight: bold;">
                      <td colspan="7" style="text-align: right; padding-right: 12px;">Rata-rata Nilai Penguji:</td>
                      <td style="text-align: center; font-size: 13px; color: #0f766e;" id="sidang_table_rerata_val">0</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <small class="text-muted" style="display: block; font-size: 11px; margin-top: 4px;">
                * Bobot: <b>A</b> (Latar Belakang 25%), <b>B</b> (Tinjauan Pustaka 15%), <b>C</b> (Metodologi Penelitian 25%), <b>D</b> (Hasil Penelitian & Pembahasan 25%), <b>E</b> (Format & Sikap 10%).
              </small>
            </div>

            <!-- Catatan Usulan Revisi Sidang -->
            <div>
              <div style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 10px;">
                Catatan Usulan Revisi Sidang dari Penguji
              </div>
              <div id="sidang_catatan_container">
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600; padding: 6px 18px; border-radius: 6px;">Tutup</button>
      </div>
    </div>
  </div>
</div>
