<style>
  /* Elevated Modern Theme for Jadwal Table */
  .jadwal-table-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
    padding: 20px 22px 18px 22px;
    margin-top: 18px;
    margin-bottom: 24px;
  }
  .jadwal-table-card .dataTables_wrapper {
    margin: 0 !important;
    padding: 0 !important;
  }
  .jadwal-table-card .dataTables_wrapper > .row:first-child {
    margin-bottom: 16px !important;
    padding-bottom: 2px !important;
  }
  .jadwal-table-card .dataTables_wrapper > .row:last-child {
    margin-top: 16px !important;
    padding-top: 4px !important;
  }
  .jadwal-table-card .dataTables_length,
  .jadwal-table-card .dataTables_filter {
    margin-bottom: 14px !important;
  }
  .jadwal-table-card .dataTables_length label,
  .jadwal-table-card .dataTables_filter label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin-bottom: 0 !important;
  }
  .jadwal-table-card .dataTables_length select {
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 4px 8px !important;
    font-size: 12px !important;
    color: #1e293b !important;
    background: #f8fafc !important;
    height: 32px !important;
    outline: none !important;
  }
  .jadwal-table-card .dataTables_filter input {
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 4px 10px !important;
    font-size: 12px !important;
    color: #1e293b !important;
    background: #ffffff !important;
    height: 32px !important;
    outline: none !important;
    transition: all 0.2s ease !important;
  }
  .jadwal-table-card .dataTables_filter input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
  }
  .jadwal-table-card .table-scrollable {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    margin: 8px 0 14px 0 !important;
  }
  #datatable_ajax {
    border-collapse: separate !important;
    border-spacing: 0;
    width: 100% !important;
    margin: 0 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: #ffffff !important;
  }
  #datatable_ajax thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 11.5px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    border-top: none !important;
    border-bottom: 2px solid #cbd5e1 !important;
    border-left: none !important;
    border-right: 1px solid #e2e8f0 !important;
    padding: 13px 12px !important;
    vertical-align: middle !important;
  }
  #datatable_ajax thead th:last-child {
    border-right: none !important;
  }
  #datatable_ajax tbody td {
    vertical-align: middle !important;
    padding: 14px 12px !important;
    border-top: none !important;
    border-bottom: 1px solid #e2e8f0 !important;
    border-right: 1px solid #f1f5f9 !important;
    border-left: none !important;
    font-size: 12.5px;
    background: #ffffff;
    transition: background 0.15s ease-in-out;
  }
  #datatable_ajax tbody td:last-child {
    border-right: none !important;
  }
  #datatable_ajax tbody tr:hover td {
    background-color: #f8fafc !important;
  }
  #datatable_ajax tbody tr:last-child td {
    border-bottom: none !important;
  }

  /* Dokumen Jadwal Button */
  .btn-doc-jadwal {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    padding: 6px 12px !important;
    background: #f0fdf4 !important;
    color: #166534 !important;
    border: 1px solid #86efac !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
  }
  .btn-doc-jadwal:hover {
    background: #dcfce7 !important;
    border-color: #4ade80 !important;
    color: #14532d !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(22, 101, 52, 0.15);
    text-decoration: none !important;
  }

  .btn-create-rps-obe {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    padding: 6px 10px !important;
    background: #f0f9ff !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
  }
  .btn-create-rps-obe:hover {
    background: #e0f2fe !important;
    border-color: #7dd3fc !important;
    color: #0c4a6e !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(3, 105, 161, 0.15);
    text-decoration: none !important;
  }

  /* Reset RPS Jadwal Button */
  .btn-reset-jadwal {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    width: 100% !important;
    padding: 3px 8px !important;
    background: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca !important;
    border-radius: 4px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    text-decoration: none !important;
    line-height: 1.3 !important;
  }
  .btn-reset-jadwal:hover {
    background: #fee2e2 !important;
    border-color: #fca5a5 !important;
    color: #b91c1c !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
    text-decoration: none !important;
  }

  /* Nilai Pill Buttons */
  .btn-pill-skema {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding: 4px 8px !important;
    background: #f8fafc !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 5px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .btn-pill-skema:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
  }
  .badge-skema-done {
    background: #dcfce7;
    color: #15803d;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 4px;
    border: 1px solid #bbf7d0;
  }

  .btn-pill-atur-skema {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding: 6px 9px !important;
    background: #fffbeb !important;
    color: #92400e !important;
    border: 1px solid #fde68a !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
  }
  .btn-pill-atur-skema:hover {
    background: #fef3c7 !important;
    border-color: #fcd34d !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.15);
  }

  .btn-pill-isi-nilai {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding: 4px 8px !important;
    background: #f0fdf4 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
    border-radius: 5px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
  }
  .btn-pill-isi-nilai:hover {
    background: #dcfce7 !important;
    border-color: #86efac !important;
    color: #14532d !important;
    text-decoration: none !important;
  }

  .btn-pill-publish {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding: 4px 8px !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe !important;
    border-radius: 5px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
  }
  .btn-pill-publish:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
    text-decoration: none !important;
  }

  /* Virtual Class Buttons */
  .btn-vc-green {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    padding: 6px 10px !important;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border: 1px solid #059669 !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
    transition: all 0.2s ease !important;
  }
  .btn-vc-green:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35) !important;
    text-decoration: none !important;
  }

  .btn-vc-gray {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    padding: 6px 10px !important;
    background: #f8fafc !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
  }
  .btn-vc-gray:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #94a3b8 !important;
    transform: translateY(-1px);
    text-decoration: none !important;
  }

  .btn-studio-rps {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background-color: #1b4332 !important;
    background-image: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%) !important;
    color: #ffffff !important;
    border: 1px solid #143527 !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    padding: 7px 14px !important;
    box-shadow: 0 2px 6px rgba(27, 67, 50, 0.25) !important;
    text-decoration: none !important;
    transition: all 0.2s ease-in-out !important;
  }
  .btn-studio-rps:hover, .btn-studio-rps:focus, .btn-studio-rps:active {
    background-color: #143527 !important;
    background-image: none !important;
    color: #ffffff !important;
    text-decoration: none !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(27, 67, 50, 0.35) !important;
  }
  .btn-studio-rps i {
    color: #6ee7b7 !important;
  }

  .btn-reload-table {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background-color: #f8fafc !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 7px 12px !important;
    text-decoration: none !important;
    transition: all 0.2s ease-in-out !important;
    cursor: pointer !important;
  }
  .btn-reload-table:hover {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
    border-color: #94a3b8 !important;
  }
</style>

<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light" style="border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); padding: 20px 24px;">
        <div class="portlet-title" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
          <div class="caption">
            <i class="fa fa-calendar-check-o font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold uppercase"><?= $judul_besar; ?> <small style="color:#64748b; font-weight:normal;"><?= $judul_kecil; ?></small></span>
            <?php if ($this->session->flashdata('msg_krs')) { ?>
              <div class="alert alert-danger margin-top-10" style="display: flex; justify-content: space-between; align-items: center; border-radius:8px;"> 
                <span><?= $this->session->flashdata('msg_krs') ?></span>
                <button class="btn btn-xs btn-danger" style="margin-left: 10px; border-radius:4px;" onclick="$('#modal_krs_notification').modal('show')"><i class="fa fa-eye"></i> Tampilkan</button>
              </div>
            <?php } ?>
          </div>
          <div class="actions">
              <a href="<?= site_url('C_wall') ?>" class="btn btn-sm" style="background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE; border-radius: 8px; font-weight: 600;"><i class="fa fa-arrow-left"></i> Kembali</a>
          </div>
        </div>
        <div class="portlet-body">
          <div class="table-container">
            <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
              <button type="button" class="btn-reload-table" onclick="reload_table_ajax()"><i class="glyphicon glyphicon-refresh" style="color: #64748b;"></i> Reload Data</button>
              <a href="<?= site_url('C_rps_generator'); ?>" target="_blank" class="btn-studio-rps"><i class="fa fa-cubes"></i> Buka Studio RPS OBE</a>
            </div>
            <div class="jadwal-table-card">
              <table id="datatable_ajax" class="table" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th style="width:38px; text-align:center;">No</th>
                    <th style="width:150px;">Hari / Jam / Ruang</th>
                    <th>Mata Kuliah &amp; Kelas</th>
                    <th style="width:120px; text-align:center;">Status RPS</th>
                    <th style="width:140px; text-align:center;">Dokumen Jadwal</th>
                    <th style="width:115px; text-align:center;">Skema Nilai</th>
                    <th style="width:120px; text-align:center;">Nilai</th>
                    <th style="width:105px; text-align:center;">Reset RPS</th>
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

</div>