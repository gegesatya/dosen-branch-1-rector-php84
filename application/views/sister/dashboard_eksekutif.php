<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
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
    background: linear-gradient(135deg, #38A169 0%, #2F855A 100%);
    color: white !important;
    border: none;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(56, 161, 105, 0.3);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }
  .btn-sync:hover {
    background: linear-gradient(135deg, #2F855A 0%, #276749 100%);
    box-shadow: 0 6px 15px rgba(56, 161, 105, 0.4);
    transform: translateY(-2px);
  }
  
  /* AI Stat Cards */
  .ai-stat-card {
    border-radius: 16px !important;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    min-height: 120px;
  }
  .ai-stat-card .icon {
    position: absolute;
    right: 15px;
    bottom: 10px;
    font-size: 55px;
    opacity: 0.15;
  }
  .stat-bg-blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
  .stat-bg-red { background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%); }
  .stat-bg-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }

  .ai-stat-value {
    font-size: 32px;
    font-weight: 800;
    margin: 5px 0 0 0;
    font-family: 'Outfit', sans-serif;
  }
  .ai-stat-label {
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    opacity: 0.9;
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
</style>

<div class="page-content" style="padding-top: 0; position: relative; min-height: 80vh;">
  <!-- Loading Overlay with Progress Bar -->
  <div id="sync-loader" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(247,250,252,0.9); z-index: 99999; justify-content: flex-start; padding-top: 150px; align-items: center; flex-direction: column; backdrop-filter: blur(3px); border-radius: 8px;">
    <div class="modern-spinner" style="width: 70px; height: 70px;"></div>
    <h4 class="loader-text" style="margin-top: 25px; margin-bottom: 20px;">SINKRONISASI MASSAL SISTER...</h4>
    
    <div style="width: 50%; background-color: #E2E8F0; border-radius: 999px; height: 12px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
      <div id="sync-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #38A169 0%, #48BB78 100%); transition: width 0.3s ease; border-radius: 999px;"></div>
    </div>
    <div id="sync-progress-text" style="margin-top: 10px; font-weight: 700; color: #4A5568; font-size: 14px;">0%</div>
    <button onclick="cancelSyncEksekutif()" style="margin-top: 15px; background: #E53E3E; color: white; border: none; padding: 5px 15px; border-radius: 5px; font-weight: 700; cursor: pointer;"><i class="fa fa-times"></i> Batal</button>
  </div>

  <div class="sister-wrapper">
    <div class="container-fluid">
      
      <!-- Top Title Bar -->
      <div class="row">
        <div class="col-md-12">
          <div class="timeline-title-section">
            <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h1>Dashboard SISTER Eksekutif</h1>
                <p>Pemantauan Kinerja Tridharma Dosen Politeknik WBI Terintegrasi SISTER</p>
              </div>
              <div>
                <button id="btn-sync-sister" class="btn-sync" onclick="syncSisterEksekutif()">
                  <i class="fa fa-refresh"></i> Tarik Data Massal (Seluruh Dosen)
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Stat Cards -->
      <div class="row">
        <div class="col-md-4">
          <div class="ai-stat-card stat-bg-blue">
            <i class="fa fa-book icon"></i>
            <div class="ai-stat-label">Total SKS Pengajaran</div>
            <div class="ai-stat-value"><?= number_format($total_sks, 0) ?> SKS</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="ai-stat-card stat-bg-red">
            <i class="fa fa-flask icon"></i>
            <div class="ai-stat-label">Total Penelitian Dosen</div>
            <div class="ai-stat-value"><?= number_format($total_penelitian, 0) ?> Kegiatan</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="ai-stat-card stat-bg-green">
            <i class="fa fa-users icon"></i>
            <div class="ai-stat-label">Total Pengabdian Dosen</div>
            <div class="ai-stat-value"><?= number_format($total_pengabdian, 0) ?> Kegiatan</div>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="row">
        <div class="col-md-12">
          <div class="stats-card">
            <div style="font-size: 16px; font-weight: 800; color: #1A365D; margin-bottom: 15px;">
              <i class="fa fa-list" style="color: #4A5568; margin-right: 8px;"></i> Ringkasan Tridharma Seluruh Dosen
            </div>
            
            <div class="table-responsive">
              <table class="modern-table dt-ajax" id="tabelEksekutif" width="100%">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Dosen</th>
                    <th>Total Pengajaran</th>
                    <th>Total Penelitian</th>
                    <th>Total Pengabdian</th>
                    <th>Sync Terakhir</th>
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

<script>
var currentXhr = null;
var progressInterval = null;

function cancelSyncEksekutif() {
  if (currentXhr) {
    currentXhr.abort();
  }
  if (progressInterval) {
    clearInterval(progressInterval);
  }
  document.getElementById('sync-loader').style.display = 'none';
  alert('Sinkronisasi massal dibatalkan oleh pengguna.');
}

function syncSisterEksekutif() {
  var loader = document.getElementById('sync-loader');
  var progressBar = document.getElementById('sync-progress-bar');
  var progressText = document.getElementById('sync-progress-text');
  
  loader.style.display = 'flex';
  progressBar.style.width = '0%';
  progressText.innerText = '0% (Mempersiapkan daftar dosen...)';

  var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
  var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
  
  // Ambil seluruh dosen dari siakad_dosen untuk disinkronkan secara lengkap
  $.ajax({
    url: '<?= site_url("C_sister/ajax_get_all_dosen_for_profil_sync") ?>',
    type: 'POST',
    data: { [csrfName]: csrfHash },
    dataType: 'json',
    success: function(res) {
      if(res.status === 'success' && res.data && res.data.length > 0) {
        var listDosen = res.data;
        var total = listDosen.length;
        var current = 0;
        
        function syncNext() {
          if(current >= total) {
            progressBar.style.width = '100%';
            progressText.innerText = 'Selesai 100% (' + total + '/' + total + ' Dosen)';
            setTimeout(function() {
              alert('Sinkronisasi massal seluruh Dosen (Profil, Tridharma & BKD) berhasil diselesaikan.');
              $('#tabelEksekutif').DataTable().ajax.reload();
              location.reload();
            }, 500);
            return;
          }
          
          var d = listDosen[current];
          var pct = Math.floor((current / total) * 100);
          progressBar.style.width = pct + '%';
          progressText.innerText = pct + '% (' + (current + 1) + '/' + total + ') Menyinkronkan: ' + d.username;
          
          currentXhr = $.ajax({
            url: '<?= site_url("C_sister/ajax_sync_single_dosen") ?>',
            type: 'POST',
            data: {
              [csrfName]: csrfHash,
              username: d.username,
              id_ptk_feeder: d.id_ptk_feeder
            },
            dataType: 'json',
            success: function() {
              current++;
              syncNext();
            },
            error: function() {
              // Jika ada 1 dosen gagal/error, tetap lanjut ke dosen berikutnya
              current++;
              syncNext();
            }
          });
        }
        
        syncNext();
      } else {
        loader.style.display = 'none';
        alert('Tidak ada data dosen yang dapat ditarik.');
      }
    },
    error: function() {
      loader.style.display = 'none';
      alert('Gagal mengambil daftar dosen untuk sinkronisasi.');
    }
  });
}

var initDataTablesEks = setInterval(function() {
  if (window.jQuery && window.jQuery.fn.DataTable) {
    clearInterval(initDataTablesEks);
    
    $('#tabelEksekutif').DataTable({
      "serverSide": true,
      "processing": true,
      "ajax": {
        "url": "<?= site_url('C_sister/ajax_list_eksekutif') ?>",
        "type": "POST",
        "data": function(d) {
          d.<?= $this->security->get_csrf_token_name(); ?> = '<?= $this->security->get_csrf_hash(); ?>';
        }
      },
      "columns": [
        { "data": null, "render": function (data, type, row, meta) { return meta.row + 1 + meta.settings._iDisplayStart; } },
        { "data": null, "render": function (data, type, row) { 
            var name = row.first_name ? row.first_name + ' ' + (row.last_name ? row.last_name : '') : row.username;
            return '<span style="font-weight: 700;">' + name + '</span><br><small style="color: #718096;">' + row.username + '</small>'; 
        } },
        { "data": "total_pengajaran", "render": function(data) { return '<span style="background: #EBF8FF; color: #3182CE; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">'+data+' MK</span>'; } },
        { "data": "total_penelitian", "render": function(data) { return '<span style="background: #FFF5F5; color: #E53E3E; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">'+data+'</span>'; } },
        { "data": "total_pengabdian", "render": function(data) { return '<span style="background: #F0FFF4; color: #38A169; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">'+data+'</span>'; } },
        { "data": "last_sync", "render": function(data) { return data ? '<span style="font-size: 12px; color: #718096;"><i class="fa fa-clock-o"></i> '+data+'</span>' : '-'; } }
      ],
      "language": { "emptyTable": "Belum ada data Dosen tersinkron. Silakan klik 'Tarik Data Massal'." }
    });
  }
}, 100);
</script>
