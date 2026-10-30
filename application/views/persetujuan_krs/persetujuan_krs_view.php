<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            <i class="fa fa-gift font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> </span>
            <span class="caption-helper">TAHUN AJARAN <?= $this->session->userdata('tahun_ajaran'); ?></span>

            <?php if ($this->session->flashdata('msg')) { ?>
              <div class="alert alert-danger margin-top-10"> <?= $this->session->flashdata('msg') ?> </div>
            <?php } ?>

          </div>
          <div class="actions">
              <a href="<?= site_url('C_wall') ?>" class="btn btn-sm" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 8px; font-weight: 600;"><i class="fa fa-arrow-left"></i> Kembali</a>
          </div>
        </div>
        <div class="portlet-body">
          
          <div class="row margin-bottom-20" style="display: flex; flex-wrap: wrap;">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 15px; display: flex;">
              <div class="dashboard-stat blue-soft" style="width: 100%; display: flex; flex-direction: column; justify-content: center; margin-bottom: 0; border-radius: 4px; min-height: 98px;">
                <div class="visual"><i class="fa fa-clock-o"></i></div>
                <div class="details">
                  <div class="number" id="summary_pending"> <?= $summary->pending ?> </div>
                  <div class="desc"> Menunggu Persetujuan </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 15px; display: flex;">
              <div class="dashboard-stat green-soft" style="width: 100%; display: flex; flex-direction: column; justify-content: center; margin-bottom: 0; border-radius: 4px; min-height: 98px;">
                <div class="visual"><i class="fa fa-check-circle"></i></div>
                <div class="details">
                  <div class="number">
                    <span id="summary_approved"><?= $summary->approved ?? 0 ?></span> <span style="font-size: 14px; font-weight: normal; opacity: 0.9;">dari <span id="summary_total_krs"><?= $summary->total_krs ?? (($summary->pending ?? 0) + ($summary->approved ?? 0)) ?></span></span>
                  </div>
                  <div class="desc"> Sudah di-Approve </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 15px; display: flex;">
              <div class="dashboard-stat purple-soft" style="width: 100%; display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0; cursor: pointer; border-radius: 4px; overflow: hidden; min-height: 98px;" onclick="filterByStatusDraft();" title="Klik untuk memfilter mahasiswa berstatus Draft / Belum Submit">
                <div style="position: relative; flex: 1; display: flex; align-items: center; justify-content: space-between;">
                  <div class="visual" style="padding-top: 5px;"><i class="fa fa-pencil-square-o"></i></div>
                  <div class="details">
                    <div class="number" id="summary_draft"> <?= $summary->krs_draft ?> </div>
                    <div class="desc"> KRS Belum Submit (Draft) </div>
                  </div>
                </div>
                <a class="more" href="javascript:void(0)" onclick="filterByStatusDraft(); return false;" style="font-size: 11px; font-weight: 600; padding: 3px 10px; margin-top: auto; display: block; text-transform: none;">
                  Klik untuk filter <i class="m-icon-swapright m-icon-white"></i>
                </a>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 15px; display: flex;">
              <div class="dashboard-stat red-soft" style="width: 100%; display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 0; cursor: pointer; border-radius: 4px; overflow: hidden; min-height: 98px;" onclick="show_modal_belum_krs();" title="Klik untuk melihat daftar mahasiswa belum KRS">
                <div style="position: relative; flex: 1; display: flex; align-items: center; justify-content: space-between;">
                  <div class="visual" style="padding-top: 5px;"><i class="fa fa-user-times"></i></div>
                  <div class="details">
                    <div class="number" id="summary_belum_krs"> <?= $summary->belum_krs ?? 0 ?> </div>
                    <div class="desc"> Belum KRS </div>
                  </div>
                </div>
                <a class="more" href="javascript:void(0)" onclick="show_modal_belum_krs(); return false;" style="font-size: 11px; font-weight: 600; padding: 3px 10px; margin-top: auto; display: block; text-transform: none;">
                  Klik untuk lihat <i class="m-icon-swapright m-icon-white"></i>
                </a>
              </div>
            </div>
          </div>

<style>
/* Modern Action Buttons in DataTable */
.btn-action-approval {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 96px;
  height: 31px;
  padding: 0 10px;
  font-size: 11.5px;
  font-weight: 600;
  color: #ffffff !important;
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  border: 1px solid #047857;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);
  text-decoration: none !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}
.btn-action-approval:hover {
  background: linear-gradient(135deg, #047857 0%, #059669 100%);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.35);
  transform: translateY(-1px);
  color: #ffffff !important;
}
.btn-action-approval:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px rgba(16, 185, 129, 0.2);
}

.btn-action-dropdown {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  width: 96px;
  height: 31px;
  padding: 0 10px;
  font-size: 11.5px;
  font-weight: 600;
  color: #334155 !important;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none !important;
  cursor: pointer;
}
.btn-action-dropdown:hover, .btn-group.open .btn-action-dropdown {
  background: #f8fafc;
  border-color: #94a3b8;
  color: #0f172a !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}

.btn-action-detail {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  width: 96px;
  height: 31px;
  padding: 0 10px;
  font-size: 11.5px;
  font-weight: 600;
  color: #0284c7 !important;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 6px;
  text-decoration: none !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}
.btn-action-detail:hover {
  background: #e0f2fe;
  border-color: #7dd3fc;
  color: #0369a1 !important;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(2, 132, 199, 0.15);
}

.label-action-rejected {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 96px;
  height: 31px;
  border-radius: 6px;
  font-size: 11.5px;
  font-weight: 600;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* Fix Dropdown visibility, Z-index & Dropup in DataTables */
.table-scrollable,
.table-responsive,
.table-responsive-2,
.table-container,
.dataTables_wrapper,
.portlet-body {
  overflow: visible !important;
}

#datatable_ajax tbody tr .btn-group {
  position: relative;
}

#datatable_ajax .btn-group.open {
  z-index: 1050 !important;
}

.custom-action-menu {
  z-index: 9999 !important;
  border-radius: 8px !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
  padding: 6px 0 !important;
  min-width: 165px !important;
  margin-top: 4px !important;
}

/* Dropup (membuka ke atas jika berada di baris bawah) */
.btn-group.dropup .custom-action-menu {
  top: auto !important;
  bottom: 100% !important;
  margin-bottom: 4px !important;
  margin-top: 0 !important;
  box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.15), 0 -8px 10px -6px rgba(0, 0, 0, 0.1) !important;
}

.custom-action-menu > li > a {
  padding: 8px 16px !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #334155 !important;
  transition: all 0.15s ease !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.custom-action-menu > li > a:hover {
  background: #f8fafc !important;
  color: #0f172a !important;
  padding-left: 18px !important;
}

/* Chat / Message Button Styles */
.btn-msg-active {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #2563eb !important;
  transition: all 0.2s ease;
  text-decoration: none !important;
  cursor: pointer;
}
.btn-msg-active:hover {
  background: #dbeafe;
  border-color: #93c5fd;
  color: #1d4ed8 !important;
  transform: translateY(-1px);
  box-shadow: 0 3px 6px rgba(37, 99, 235, 0.15);
}
.btn-msg-active .msg-dot {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 8px;
  height: 8px;
  background: #ef4444;
  border: 2px solid #ffffff;
  border-radius: 50%;
}

.btn-msg-empty {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  color: #94a3b8 !important;
  transition: all 0.2s ease;
  text-decoration: none !important;
  cursor: pointer;
}
.btn-msg-empty:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
  color: #475569 !important;
  transform: translateY(-1px);
}

/* Top Action Buttons */
.btn-modern-top {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.2s ease;
  text-decoration: none !important;
  cursor: pointer;
}
.btn-modern-top-reload {
  background: #f8fafc;
  color: #334155;
  border: 1px solid #cbd5e1;
}
.btn-modern-top-reload:hover {
  background: #f1f5f9;
  color: #0f172a;
  border-color: #94a3b8;
}
.btn-modern-top-prodi {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
}
.btn-modern-top-prodi:hover {
  background: #dcfce7;
  color: #14532d;
  border-color: #86efac;
}
#datatable_ajax td {
  vertical-align: middle !important;
}

/* Highlight Row untuk KRS yang baru di-approve (< 1 Jam) */
tr.row-recently-approved {
  background-color: #f0fdf4 !important;
}
tr.row-recently-approved > td:first-child {
  border-left: 4px solid #16a34a !important;
}
tr.row-recently-approved:hover {
  background-color: #dcfce7 !important;
}

/* Highlight Row untuk Mahasiswa yang masih status Draft (Belum Submit) */
tr.row-status-draft {
  background-color: #fffaf0 !important;
}
tr.row-status-draft > td:first-child {
  border-left: 4px solid #f97316 !important;
}
tr.row-status-draft:hover {
  background-color: #fff4e5 !important;
}
.btn-action-draft {
  background: #fff7ed !important;
  border-color: #fdba74 !important;
  color: #c2410c !important;
}
.btn-action-draft:hover, .btn-group.open .btn-action-draft {
  background: #ffedd5 !important;
  border-color: #fb923c !important;
  color: #9a3412 !important;
}
.badge-draft-warning {
  animation: pulseDraftOrange 2.5s infinite !important;
}
@keyframes pulseDraftOrange {
  0% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.45); }
  70% { box-shadow: 0 0 0 6px rgba(234, 88, 12, 0); }
  100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
}
.badge-recently-approved {
  background: #16a34a !important;
  color: #ffffff !important;
  border: 1px solid #15803d !important;
  font-size: 9.5px !important;
  font-weight: 700 !important;
  padding: 2px 7px !important;
  border-radius: 12px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  box-shadow: 0 1px 3px rgba(22, 163, 74, 0.3) !important;
  animation: pulseApprovalGreen 2.5s infinite !important;
}
@keyframes pulseApprovalGreen {
  0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.45); }
  70% { box-shadow: 0 0 0 6px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}
</style>

          <?php if (!empty($summary->krs_draft) && $summary->krs_draft > 0): ?>
            <div class="alert alert-warning" id="alert_draft_krs" style="background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; box-shadow: 0 1px 3px rgba(234, 88, 12, 0.06);">
              <div style="display: flex; align-items: center; gap: 12px;">
                <span style="background: #ea580c; color: #fff; width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(234, 88, 12, 0.25);">
                  <i class="fa fa-exclamation"></i>
                </span>
                <div>
                  <div style="font-size: 13.5px; font-weight: 700; color: #9a3412;">
                    Perhatian: Terdapat <span id="alert_draft_count"><?= $summary->krs_draft ?></span> Mahasiswa Masih Berstatus Draft (Belum Submit KRS)
                  </div>
                  <div style="font-size: 12px; color: #c2410c; margin-top: 2px;">
                    Mahasiswa sudah memilih mata kuliah namun <b>belum menekan tombol Submit KRS</b> di portal student. Segera ingatkan mahasiswa agar pengajuan dapat disetujui.
                  </div>
                </div>
              </div>
              <div>
                <button type="button" class="btn btn-sm" onclick="filterByStatusDraft();" style="background: #ea580c; color: #fff; font-weight: 600; border-radius: 6px; border: none; padding: 7px 14px; box-shadow: 0 2px 5px rgba(234, 88, 12, 0.25); cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa fa-filter"></i> Filter Mahasiswa Draft
                </button>
              </div>
            </div>
          <?php endif; ?>

          <div class="table-container table-responsive-2">
            <div style="margin-bottom: 20px; display: flex; gap: 8px; flex-wrap: wrap;">
              <button class="btn-modern-top btn-modern-top-reload" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
              <button type="button" class="btn-modern-top btn-modern-top-prodi" onclick="show_modal_makul_prodi();">
                <i class="fa fa-life-ring"></i> Daftar Kelas Kuliah Prodi</button>
            </div>
            
            <div class="row" style="margin-bottom: 20px;">
              <div class="col-md-5 col-sm-6">
                <div class="form-group" style="margin-bottom: 10px;">
                  <label class="control-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Tampilkan Berdasar Status</label>
                  <select id='ddstatus' name='ddstatus' class="form-control" style="border-radius: 4px; height: 38px;">
                    <option value='0'>All</option>
                    <option value='1'>Draft (Belum Submit KRS)</option>
                    <option value='2'>Membutuhkan Persetujuan/Approval</option>
                    <option value='3'>Final/Selesai</option>
                  </select>
                </div>
              </div>
              <div class="col-md-5 col-sm-6">
                <div class="form-group" style="margin-bottom: 10px;">
                  <label class="control-label" style="font-weight: 600; margin-bottom: 6px; display: block;">Tampilkan Angkatan</label>
                  <select id='ddAngkatan' name='ddAngkatan' class="form-control" style="border-radius: 4px; height: 38px;">
                    <?php if (!empty($list_angkatan)): ?>
                      <?php foreach ($list_angkatan as $val => $lbl): ?>
                        <option value="<?= $val ?>"><?= $lbl ?></option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value='0'>All (Semua Angkatan)</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
            </div>
            <table id="datatable_ajax" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th width="100">Action</th>
                  <th>NIM | Nama</th>
                  <th>Kontak & Keterangan</th>
                  <th>SKS dan Status</th>
                  <th width="95">Diajukan</th>
                  <th width="95">Direspon</th>
                  <th width="75" class="text-center">Pesan</th>
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

<!-- Modal Mahasiswa Belum KRS -->
<div class="modal fade in" id="modal_belum_krs" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 90%; max-width: 950px;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #e7505a; color: #fff; padding: 12px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9; font-size: 22px;">&times;</button>
        <h4 class="modal-title" style="font-weight: 700; margin: 0; color: #fff;">
          <i class="fa fa-user-times"></i> Daftar Mahasiswa Perwalian Belum Mengisi KRS
        </h4>
      </div>
      <div class="modal-body" style="padding: 15px 20px; max-height: calc(85vh - 120px); overflow-y: auto;">
        <div class="alert alert-info" style="margin-bottom: 15px; padding: 10px 15px; border-radius: 6px; font-size: 13px;">
          <i class="fa fa-info-circle"></i> Menampilkan mahasiswa bimbingan akademik Anda yang <b>belum membuat / belum mengisi KRS</b> pada semester aktif ini. Anda dapat langsung mengingatkan mahasiswa melalui tombol <b>Chat WA</b>.
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover" style="margin-bottom: 0;">
            <thead>
              <tr style="background: #f4f6f7;">
                <th width="40" class="text-center">No</th>
                <th>NIM | Nama Mahasiswa</th>
                <th width="90" class="text-center">Angkatan</th>
                <th width="95" class="text-center">IPK Terakhir</th>
                <th width="90" class="text-center">SKS Lulus</th>
                <th width="150" class="text-center">Kontak & Aksi</th>
              </tr>
            </thead>
            <tbody id="tbody_belum_krs">
              <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">
                  <i class="fa fa-spinner fa-spin" style="font-size: 20px;"></i> Memuat data mahasiswa...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="padding: 10px 20px; background: #fafafa;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 4px;">Tutup</button>
      </div>
    </div>
  </div>
</div>