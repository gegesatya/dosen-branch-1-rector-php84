<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  .modern-spinner {
    width: 60px;
    height: 60px;
    border: 5px solid rgba(49, 130, 206, 0.2);
    border-top: 5px solid #3182CE;
    border-radius: 50%;
    animation: spinModern 1s linear infinite;
    margin: 0 auto;
  }
  @keyframes spinModern {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .loader-text {
    font-family: 'Nunito', sans-serif;
    font-weight: 800;
    font-size: 16px;
    color: #3182CE;
    letter-spacing: 2px;
    animation: pulseText 1.5s infinite;
  }
  @keyframes pulseText {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
  .sister-wrapper {
    font-family: 'Nunito', 'Segoe UI', Roboto, sans-serif;
    color: #2D3748;
    background-color: #F7FAFC;
    padding: 20px 0;
  }
  .timeline-title-section {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E2E8F0;
  }
  .timeline-title-section h1 {
    font-size: 26px;
    font-weight: 800;
    color: #1A365D;
    margin: 0 0 5px 0;
    letter-spacing: -0.5px;
  }
  .timeline-title-section p {
    color: #718096;
    margin: 0;
    font-size: 14px;
  }
  .btn-sync {
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white !important;
    border: none;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(49, 130, 206, 0.3);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }
  .btn-sync:hover {
    background: linear-gradient(135deg, #2B6CB0 0%, #2c5282 100%);
    box-shadow: 0 6px 15px rgba(49, 130, 206, 0.4);
    transform: translateY(-2px);
  }
  .btn-sync:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(49, 130, 206, 0.2);
  }
  .stats-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
    border: 1px solid rgba(224, 224, 224, 0.4);
  }
  .modern-table {
    width: 100%;
    border-collapse: collapse;
  }
  .modern-table th {
    background-color: #EDF2F7;
    color: #4A5568;
    font-weight: 700;
    padding: 12px 15px;
    text-align: left;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .modern-table td {
    padding: 15px;
    border-bottom: 1px solid #E2E8F0;
    color: #2D3748;
    font-size: 14px;
  }
  .modern-table tr:last-child td {
    border-bottom: none;
  }
  .modern-table tbody tr:hover {
    background-color: #F7FAFC;
  }
  .nav-tabs-modern {
    border-bottom: 2px solid #E2E8F0;
    margin-bottom: 20px;
    display: flex;
    list-style: none;
    padding: 0;
  }
  .nav-tabs-modern li {
    margin-right: 15px;
  }
  .nav-tabs-modern li a {
    display: block;
    padding: 10px 20px;
    color: #718096;
    font-weight: 700;
    font-size: 15px;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
  }
  .nav-tabs-modern li.active a, .nav-tabs-modern li a:hover {
    color: #3182CE;
    border-bottom-color: #3182CE;
  }
  .tab-content-modern > .tab-pane {
    display: none;
  }
  .tab-content-modern > .tab-pane.active {
    display: block;
    animation: fadeIn 0.4s ease;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<div class="page-content" style="padding-top: 0; position: relative; min-height: 80vh;">
  <!-- Loading Overlay with Progress Bar -->
  <div id="sync-loader" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(247,250,252,0.9); z-index: 99999; justify-content: flex-start; padding-top: 150px; align-items: center; flex-direction: column; backdrop-filter: blur(3px); border-radius: 8px;">
    <div class="modern-spinner" style="width: 70px; height: 70px;"></div>
    <h4 class="loader-text" style="margin-top: 25px; margin-bottom: 20px;">SINKRONISASI DATA SISTER...</h4>
    
    <div style="width: 50%; background-color: #E2E8F0; border-radius: 999px; height: 12px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
      <div id="sync-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3182CE 0%, #63B3ED 100%); transition: width 0.3s ease; border-radius: 999px;"></div>
    </div>
    <div id="sync-progress-text" style="margin-top: 10px; font-weight: 700; color: #4A5568; font-size: 14px;">0%</div>
  </div>

  <div class="sister-wrapper">
    <div class="container-fluid">
      
      <!-- Top Title Bar -->
      <div class="row">
        <div class="col-md-12">
          <div class="timeline-title-section">
            <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h1>Dashboard SISTER Pribadi</h1>
                <p>Pantau dan kelola integrasi data Tridharma Anda langsung dari SISTER</p>
              </div>
              <?php
              $u_login_obj = $this->ion_auth->user()->row();
              $u_login_email = $u_login_obj->email ?? '';
              $u_login_uname = $u_login_obj->username ?? '';
              $is_gege_dash = (stripos($u_login_email, 'gege.satya') !== false || stripos($u_login_uname, 'gege.satya') !== false);
              if ($is_gege_dash):
              ?>
              <div>
                <button id="btn-sync-sister" class="btn-sync" onclick="syncSisterPribadi()">
                  <i class="fa fa-refresh"></i> Tarik Data SISTER
                </button>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-3">
          <div class="stats-card" style="background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(49,130,206,0.2);">
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 8px;">TOTAL MENGAJAR</div>
            <div style="font-size: 28px; font-weight: 800;"><?= $total_sks ?> <span style="font-size: 14px; font-weight: 600; opacity: 0.9;">SKS</span></div>
            <div style="font-size: 13px; font-weight: 600; opacity: 0.9; margin-top: 4px;"><i class="fa fa-book"></i> <?= $total_ajar ?> Mata Kuliah</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card" style="background: linear-gradient(135deg, #E53E3E 0%, #C53030 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(229,62,62,0.2);">
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 8px;">TOTAL PENELITIAN</div>
            <div style="font-size: 28px; font-weight: 800;"><?= $total_lit ?> <span style="font-size: 14px; font-weight: 600; opacity: 0.9;">Kegiatan</span></div>
            <div style="font-size: 13px; font-weight: 600; opacity: 0.9; margin-top: 4px;"><i class="fa fa-flask"></i> Tercatat di SISTER</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card" style="background: linear-gradient(135deg, #38A169 0%, #2F855A 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(56,161,105,0.2);">
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 8px;">TOTAL PENGABDIAN</div>
            <div style="font-size: 28px; font-weight: 800;"><?= $total_abdimas ?> <span style="font-size: 14px; font-weight: 600; opacity: 0.9;">Kegiatan</span></div>
            <div style="font-size: 13px; font-weight: 600; opacity: 0.9; margin-top: 4px;"><i class="fa fa-users"></i> Tercatat di SISTER</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card" style="background: linear-gradient(135deg, #D69E2E 0%, #B7791F 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(214,158,46,0.2);">
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 8px;">ANGKA KREDIT (KUM)</div>
            <div style="font-size: 28px; font-weight: 800;"><?= number_format((float)$kum, 2) ?></div>
            <div style="font-size: 13px; font-weight: 600; opacity: 0.9; margin-top: 4px;"><i class="fa fa-star"></i> Poin Terakhir</div>
          </div>
        </div>
      </div>

      <!-- Main Content Tabs -->
      <div class="row">
        <div class="col-md-12">
          <ul class="nav-tabs-modern" id="sisterTabs">
            <li class="active"><a href="#tab_pengajaran" data-toggle="tab">Pengajaran</a></li>
            <li><a href="#tab_penelitian" data-toggle="tab">Penelitian</a></li>
            <li><a href="#tab_pengabdian" data-toggle="tab">Pengabdian</a></li>
            <li><a href="#tab_inpassing" data-toggle="tab"><i class="fa fa-certificate" style="color:#D69E2E;margin-right:4px;"></i> Inpassing</a></li>
            <li><a href="#tab_publikasi" data-toggle="tab"><i class="fa fa-newspaper-o" style="color:#805AD5;margin-right:4px;"></i> Publikasi</a></li>
            <li><a href="#tab_bkd" data-toggle="tab"><i class="fa fa-check-circle" style="color:#38A169;margin-right:4px;"></i> Riwayat BKD</a></li>
          </ul>

          <div class="tab-content-modern">
            <!-- Pengajaran -->
            <div class="tab-pane active" id="tab_pengajaran">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-book" style="color: #3182CE; margin-right: 8px;"></i> Data Pengajaran</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelPengajaran" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Tahun Ajaran</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Penelitian -->
            <div class="tab-pane" id="tab_penelitian">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-flask" style="color: #E53E3E; margin-right: 8px;"></i> Data Penelitian</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelPenelitian" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Judul Penelitian</th>
                        <th>Peran</th>
                        <th>Tahun</th>
                        <th>Sumber Dana</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Pengabdian -->
            <div class="tab-pane" id="tab_pengabdian">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-users" style="color: #38A169; margin-right: 8px;"></i> Data Pengabdian</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelPengabdian" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Judul Pengabdian</th>
                        <th>Peran</th>
                        <th>Tahun</th>
                        <th>Mitra</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Inpassing -->
            <div class="tab-pane" id="tab_inpassing">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-certificate" style="color: #D69E2E; margin-right: 8px;"></i> Riwayat Inpassing &amp; Pangkat Golongan (KUM)</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelInpassing" width="100%">
                    <thead>
                      <tr>
                        <th width="45">No</th>
                        <th>Pangkat / Golongan</th>
                        <th>Nomor SK</th>
                        <th>Tanggal SK</th>
                        <th>Mulai Berlaku</th>
                        <th>Angka Kredit (KUM)</th>
                        <th>Masa Kerja</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Publikasi -->
            <div class="tab-pane" id="tab_publikasi">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-newspaper-o" style="color: #805AD5; margin-right: 8px;"></i> Riwayat Publikasi Ilmiah (Jurnal, Buku, Prosiding)</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelPublikasi" width="100%">
                    <thead>
                      <tr>
                        <th width="45">No</th>
                        <th>Judul Publikasi &amp; Jurnal</th>
                        <th>Jenis Publikasi</th>
                        <th>Tanggal Terbit</th>
                        <th>Penerbit / Tautan</th>
                        <th>Penulis</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Riwayat BKD -->
            <div class="tab-pane" id="tab_bkd">
              <div class="stats-card">
                <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;"><i class="fa fa-check-circle" style="color: #38A169; margin-right: 8px;"></i> Riwayat Laporan Beban Kerja Dosen (BKD) Seluruh Semester</div>
                <div class="table-responsive">
                  <table class="modern-table dt-ajax" id="tabelBKD" width="100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Semester / Tahun Ajaran</th>
                        <th>Total SKS</th>
                        <th>Ajar</th>
                        <th>Litmas</th>
                        <th>Pengmas</th>
                        <th>Penunjang</th>
                        <th>Status Beban</th>
                        <th>Simpulan Asesor</th>
                        <th>Terakhir Sinkron</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Diisi oleh DataTables via AJAX -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Simple tab logic to replace bootstrap tabs if they conflict
document.addEventListener('DOMContentLoaded', function() {
  const tabs = document.querySelectorAll('#sisterTabs li a');
  const panes = document.querySelectorAll('.tab-content-modern > .tab-pane');
  
  tabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      
      tabs.forEach(t => t.parentElement.classList.remove('active'));
      this.parentElement.classList.add('active');
      
      const target = this.getAttribute('href');
      panes.forEach(pane => pane.classList.remove('active'));
      document.querySelector(target).classList.add('active');
    });
  });
});

function syncSisterPribadi() {
  var loader = document.getElementById('sync-loader');
  var progressBar = document.getElementById('sync-progress-bar');
  var progressText = document.getElementById('sync-progress-text');
  
  loader.style.display = 'flex';
  progressBar.style.width = '0%';
  progressText.innerText = '0%';

  // Animasi progress simulasi (mengingat HTTP request tidak natively beri progress download unless chunked)
  var width = 0;
  var progressInterval = setInterval(function() {
    if (width >= 85) {
      clearInterval(progressInterval);
    } else {
      width += Math.random() * 15; // Naik acak max 15%
      if(width > 85) width = 85;
      progressBar.style.width = width + '%';
      progressText.innerText = Math.round(width) + '%';
    }
  }, 400); // Tiap 400ms naik
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= site_url("C_sister/ajax_sync_pribadi") ?>', true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
  var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
  var params = csrfName + '=' + csrfHash;
  
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      clearInterval(progressInterval);
      if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        
        // Selesaikan progress bar ke 100%
        progressBar.style.width = '100%';
        progressText.innerText = '100%';
        
          setTimeout(function() {
          alert(response.message);
          // Reload datatables
          $('#tabelPengajaran').DataTable().ajax.reload();
          $('#tabelPenelitian').DataTable().ajax.reload();
          $('#tabelPengabdian').DataTable().ajax.reload();
          $('#tabelInpassing').DataTable().ajax.reload();
          $('#tabelPublikasi').DataTable().ajax.reload();
          $('#tabelBKD').DataTable().ajax.reload();
          loader.style.display = 'none';
        }, 300);
      } else {
        alert('Terjadi kesalahan saat menyinkronkan data. Status: ' + xhr.status);
        loader.style.display = 'none';
      }
    }
  };
  xhr.send(params);
}

var initDataTables = setInterval(function() {
  if (window.jQuery && window.jQuery.fn.DataTable) {
    clearInterval(initDataTables);
    
    $('#tabelPengajaran').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_pengajaran') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": "nama_mata_kuliah", "render": function (data) { return '<span style="font-weight: 700;">'+data+'</span>'; } },
        { "data": "sks", "render": function(data) { return '<span style="background: #EBF8FF; color: #2B6CB0; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">'+data+' SKS</span>'; } },
        { "data": "semester" },
        { "data": "tahun_ajaran" },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data pengajaran. Silakan klik 'Tarik Data SISTER'." }
    });

    $('#tabelPenelitian').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_penelitian') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": "judul_penelitian", "render": function (data) { return '<span style="font-weight: 700;">'+data+'</span>'; } },
        { "data": "peran" },
        { "data": "tahun" },
        { "data": "sumber_dana" },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data penelitian. Silakan klik 'Tarik Data SISTER'." }
    });

    $('#tabelPengabdian').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_pengabdian') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": "judul_pengabdian", "render": function (data) { return '<span style="font-weight: 700;">'+data+'</span>'; } },
        { "data": "peran" },
        { "data": "tahun" },
        { "data": "mitra" },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data pengabdian. Silakan klik 'Tarik Data SISTER'." }
    });

    $('#tabelInpassing').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_inpassing') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": "pangkat_golongan", "render": function(data) { return '<span style="font-weight: 800; color: #1A365D;">'+(data || '-')+'</span>'; } },
        { "data": "sk", "render": function(data) { return '<span style="font-weight: 700; color: #2D3748;">'+(data || '-')+'</span>'; } },
        { "data": "tanggal_sk", "render": function(data) { return data || '-'; } },
        { "data": "tanggal_mulai", "render": function(data) { return data || '-'; } },
        { "data": "angka_kredit", "render": function(data) { return '<span style="background: #FAF5FF; color: #6B46C1; border: 1px solid rgba(107,70,193,0.3); padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 800;">KUM '+(data || '0')+'</span>'; } },
        { "data": null, "render": function(data, type, row) { 
            var thn = row.masa_kerja_tahun ? row.masa_kerja_tahun + ' Thn' : '';
            var bln = row.masa_kerja_bulan ? row.masa_kerja_bulan + ' Bln' : '';
            return (thn + ' ' + bln).trim() || '-';
        } },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data inpassing / KUM. Silakan klik 'Tarik Data SISTER'." }
    });

    $('#tabelPublikasi').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_publikasi') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": null, "render": function(data, type, row) { 
            var html = '<div style="font-weight: 800; color: #1A365D; font-size: 14px; line-height: 1.4;">'+(row.judul || '-')+'</div>';
            if (row.nama_jurnal) {
              html += '<div style="font-size: 12.5px; color: #4A5568; margin-top: 4px;"><i class="fa fa-book text-muted"></i> <strong>'+row.nama_jurnal+'</strong></div>';
            }
            return html;
        } },
        { "data": "jenis_publikasi", "render": function(data) { 
            return '<span style="background: #EBF8FF; color: #2B6CB0; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-block;">'+(data || '-')+'</span>'; 
        } },
        { "data": "tanggal", "render": function(data) { return data || '-'; } },
        { "data": null, "render": function(data, type, row) {
            var parts = [];
            if (row.penerbit) parts.push('<span style="color:#718096;">'+row.penerbit+'</span>');
            if (row.doi) parts.push('<a href="'+(row.doi.startsWith('http')?row.doi:'https://doi.org/'+row.doi)+'" target="_blank" style="color:#3182CE; font-weight:700;"><i class="fa fa-external-link"></i> DOI</a>');
            if (row.tautan && row.tautan !== row.doi) parts.push('<a href="'+row.tautan+'" target="_blank" style="color:#38A169; font-weight:700;"><i class="fa fa-link"></i> URL</a>');
            return parts.length ? parts.join('<br>') : '-';
        } },
        { "data": "penulis_list", "render": function(data) { 
            return '<span style="font-size: 12.5px; color: #2D3748;">'+(data || '-')+'</span>'; 
        } },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data publikasi ilmiah. Silakan klik 'Tarik Data SISTER'." }
    });

    $('#tabelBKD').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_bkd') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": "label_smt", "render": function(data) { return '<span style="font-weight: 700; color: #1A365D;">'+data+'</span>'; } },
        { "data": "sks_total", "render": function(data) { return '<span style="background: #EBF8FF; color: #2B6CB0; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 800;">'+parseFloat(data).toFixed(2)+' SKS</span>'; } },
        { "data": "sks_ajar", "render": function(data) { return parseFloat(data).toFixed(2); } },
        { "data": "sks_lit", "render": function(data) { return parseFloat(data).toFixed(2); } },
        { "data": "sks_pengmas", "render": function(data) { return parseFloat(data).toFixed(2); } },
        { "data": "sks_penunjang", "render": function(data) { return parseFloat(data).toFixed(2); } },
        { "data": "stat_kewajiban", "render": function(data) {
            var isM = (data === 'Memenuhi');
            return isM ? '<span style="background: #F0FFF4; color: #38A169; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 800;"><i class="fa fa-check"></i> Memenuhi</span>' : '<span style="background: #FFF5F5; color: #E53E3E; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 800;"><i class="fa fa-times"></i> Tidak</span>';
        } },
        { "data": "simpulan_asesor", "render": function(data) { return '<span style="font-weight: 700;">'+(data || '-')+'</span>'; } },
        { "data": "last_sync", "render": function(data) { return '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>'; } }
      ],
      "language": { "emptyTable": "Belum ada data riwayat BKD. Silakan klik 'Tarik Data SISTER'." }
    });
  }
}, 100);
</script>
