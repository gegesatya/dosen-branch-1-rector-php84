<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
  .profil-wrapper {
    font-family: 'Nunito', 'Segoe UI', Roboto, sans-serif;
    color: #2D3748;
    background-color: #F7FAFC;
    padding: 20px 0;
  }
  .profil-title-section {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E2E8F0;
  }
  .profil-title-section h1 {
    font-size: 26px;
    font-weight: 800;
    color: #1A365D;
    margin: 0 0 5px 0;
    letter-spacing: -0.5px;
  }
  .profil-title-section p { color: #718096; margin: 0; font-size: 14px; }
  .search-bar-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .search-bar-wrapper input {
    flex: 1;
    border: 2px solid #E2E8F0;
    border-radius: 8px;
    padding: 9px 14px;
    font-family: 'Nunito', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #2D3748;
    transition: border-color 0.2s;
    outline: none;
  }
  .search-bar-wrapper input:focus { border-color: #3182CE; }
  .search-bar-wrapper input::placeholder { color: #A0AEC0; font-weight: 400; }
  .search-icon { color: #A0AEC0; font-size: 18px; }
  .stats-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-family: 'Nunito', sans-serif;
    font-size: 13px;
    color: #718096;
    font-weight: 600;
  }
  .stats-bar .badge-count {
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }
  .dosen-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    margin-bottom: 30px;
  }
  @media (max-width: 1400px) { .dosen-grid { grid-template-columns: repeat(5, 1fr); } }
  @media (max-width: 1200px) { .dosen-grid { grid-template-columns: repeat(4, 1fr); } }
  @media (max-width: 992px) { .dosen-grid { grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 768px) { .dosen-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 576px) { .dosen-grid { grid-template-columns: 1fr; } }
  
  .dosen-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 22px 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid rgba(224,224,224,0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.25s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .dosen-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3182CE, #63B3ED);
    opacity: 0;
    transition: opacity 0.25s;
  }
  .dosen-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(49,130,206,0.15);
    border-color: #BEE3F8;
  }
  .dosen-card:hover::before { opacity: 1; }
  
  .dosen-avatar {
    width: 86px; height: 86px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #EBF8FF;
    box-shadow: 0 4px 12px rgba(49,130,206,0.2);
    margin-bottom: 12px;
    transition: border-color 0.25s;
    background: #EDF2F7;
  }
  .dosen-card:hover .dosen-avatar { border-color: #90CDF4; }
  .dosen-avatar-fallback {
    width: 86px; height: 86px;
    border-radius: 50%;
    border: 3px solid #EBF8FF;
    box-shadow: 0 4px 12px rgba(49,130,206,0.2);
    margin-bottom: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    color: white;
    font-weight: 800;
    font-family: 'Nunito', sans-serif;
  }
  .dosen-name { font-size: 13px; font-weight: 800; color: #1A365D; line-height: 1.3; margin-bottom: 4px; }
  .dosen-nidn { font-size: 11px; font-weight: 600; color: #A0AEC0; letter-spacing: 0.5px; margin-bottom: 6px; }
  .dosen-prodi { font-size: 10px; font-weight: 700; color: #4A5568; background: #EDF2F7; padding: 2px 8px; border-radius: 4px; margin-bottom: 8px; }
  
  .btn-detail {
    margin-top: 10px; width: 100%; padding: 8px 0;
    background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
    color: white; border: none; border-radius: 8px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    font-family: 'Nunito', sans-serif; transition: all 0.2s; letter-spacing: 0.3px;
    display: flex; align-items: center; justify-content: center; gap: 6px;
  }
  .btn-detail:hover {
    background: linear-gradient(135deg, #2B6CB0 0%, #2c5282 100%);
    box-shadow: 0 4px 12px rgba(49,130,206,0.3);
  }

  /* Skeleton Loading */
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 8px;
  }
  @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
  .skeleton-card {
    background: #fff; border-radius: 16px; padding: 22px 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid rgba(224,224,224,0.5);
    display: flex; flex-direction: column; align-items: center;
  }
  .skeleton-avatar { width: 86px; height: 86px; border-radius: 50%; margin-bottom: 12px; }
  .skeleton-line { height: 12px; width: 80%; margin-bottom: 8px; }
  .skeleton-line.short { width: 50%; }
  .skeleton-line.btn-line { width: 100%; height: 32px; border-radius: 8px; margin-top: 6px; }

  /* Modal SISTER Style */
  .modal-profil .modal-dialog {
    max-width: 1180px;
    width: 95%;
    margin: 20px auto;
    height: calc(100vh - 40px);
    display: flex;
    align-items: center;
  }
  .modal-profil .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    font-family: 'Nunito', sans-serif;
    overflow: hidden;
    height: calc(100vh - 40px);
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    width: 100%;
  }
  #modalBkdContent {
    display: none;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    height: 100%;
    overflow: hidden;
  }
  .modal-header-profil {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 24px;
    border-bottom: 1px solid #E2E8F0;
    background: #FFFFFF;
    border-radius: 16px 16px 0 0;
    flex-shrink: 0;
  }
  .modal-title-profil {
    font-size: 17px; font-weight: 800; color: #1A365D; margin: 0;
    display: flex; align-items: center; gap: 8px;
  }
  .modal-close-btn {
    background: #EDF2F7; border: none; color: #4A5568;
    width: 32px; height: 32px; border-radius: 50%; font-size: 15px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
  }
  .modal-close-btn:hover { background: #E2E8F0; color: #E53E3E; transform: rotate(90deg); }

  .modal-body-profil {
    padding: 16px 20px 20px;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #F8FAFC;
    flex: 1;
    min-height: 0;
    overflow: hidden;
  }

  /* Banner Profil */
  .modal-banner-row {
    background: linear-gradient(135deg, #1A365D 0%, #2B6CB0 100%);
    border-radius: 12px;
    padding: 16px 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 16px rgba(26,54,93,0.18);
    flex-shrink: 0;
  }
  .modal-banner-avatar {
    width: 78px; height: 104px;
    border-radius: 8px;
    border: 3px solid rgba(255,255,255,0.4);
    object-fit: cover;
    box-shadow: 0 4px 14px rgba(0,0,0,0.22);
    background: #EDF2F7;
    flex-shrink: 0;
  }
  .modal-banner-avatar-fallback {
    width: 78px; height: 104px;
    border-radius: 8px;
    border: 3px solid rgba(255,255,255,0.4);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white; font-weight: 800;
    box-shadow: 0 4px 14px rgba(0,0,0,0.22);
    flex-shrink: 0;
  }
  .modal-banner-info { flex: 1; min-width: 0; }
  .modal-banner-nama { font-size: 18px; font-weight: 800; margin: 0 0 4px; line-height: 1.25; color: #FFFFFF; }
  .modal-banner-nidn { font-size: 12px; font-weight: 600; opacity: 0.9; margin-bottom: 8px; }
  .modal-banner-badges { display: flex; flex-wrap: wrap; gap: 6px; }
  .modal-banner-badges span {
    background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35);
    color: white; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
  }

  /* Table Section Inside Modal */
  .modal-table-container {
    background: #FFFFFF;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #E2E8F0;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .modal-table-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 18px;
    background: #EDF2F7;
    border-bottom: 1px solid #E2E8F0;
    flex-shrink: 0;
  }
  .modal-table-scroll-wrap {
    flex: 1;
    min-height: 0;
    overflow-y: auto !important;
    overflow-x: auto;
    padding: 16px;
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 #F7FAFC;
  }
  .modal-table-scroll-wrap::-webkit-scrollbar { width: 8px; height: 8px; }
  .modal-table-scroll-wrap::-webkit-scrollbar-track { background: #F7FAFC; border-radius: 4px; }
  .modal-table-scroll-wrap::-webkit-scrollbar-thumb { background: #CBD5E0; border-radius: 4px; }
  .modal-table-scroll-wrap::-webkit-scrollbar-thumb:hover { background: #A0AEC0; }

  .modal-spinner-wrap { display: flex; flex-direction: column; align-items: center; padding: 60px 0; }
  .modal-spinner {
    width: 40px; height: 40px; border: 4px solid rgba(49,130,206,0.15);
    border-top: 4px solid #3182CE; border-radius: 50%; animation: spin 0.9s linear infinite; margin-bottom: 12px;
  }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="page-content" style="padding-top: 0;">
  <div class="profil-wrapper">
    <div class="container-fluid">
      
      <!-- Title -->
      <div class="row">
        <div class="col-md-12">
          <div class="profil-title-section">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
              <div>
                <h1><i class="fa fa-id-card-o" style="color: #3182CE; margin-right: 10px;"></i>BKD Internal</h1>
                <p>Direktori Beban Kerja Dosen (BKD) internal Politeknik Wilmar Bisnis Indonesia dengan standar rubrik tahunan</p>
              </div>
              <div style="text-align: right; font-size: 12px; color: #38A169; font-family: 'Nunito', sans-serif; font-weight: 700; background: #F0FFF4; padding: 8px 14px; border-radius: 8px; border: 1px solid #C6F6D5;">
                <i class="fa fa-circle" style="margin-right: 4px;"></i> BKD Internal PWBI
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="row">
        <div class="col-md-12">
          <div class="search-bar-wrapper">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="searchDosenBkd" placeholder="Cari nama dosen, NIDN, atau program studi..." oninput="filterDosenBkd(this.value)">
          </div>
        </div>
      </div>

      <!-- Stats Bar & Grid -->
      <div class="row">
        <div class="col-md-12">
          <div class="stats-bar" id="statsBarBkd" style="display:none;">
            Menampilkan <span class="badge-count" id="visibleCountBkd">0</span> dari <span id="totalCountBkd">0</span> dosen
          </div>
          
          <div class="dosen-grid" id="dosenGridBkd">
            <!-- Skeletons -->
            <?php for ($i = 0; $i < 12; $i++): ?>
            <div class="skeleton-card">
              <div class="skeleton skeleton-avatar"></div>
              <div class="skeleton skeleton-line"></div>
              <div class="skeleton skeleton-line short"></div>
              <div class="skeleton skeleton-line short" style="width:40%; height:18px; border-radius:999px;"></div>
              <div class="skeleton skeleton-line btn-line"></div>
            </div>
            <?php endfor; ?>
          </div>

          <div id="noResultBkd" style="display:none; text-align:center; padding: 50px 0; color:#A0AEC0; font-family:'Nunito',sans-serif;">
            <i class="fa fa-database" style="font-size: 38px; margin-bottom: 12px; display:block;"></i>
            <div style="font-weight:700; font-size:15px; margin-bottom:6px; color:#4A5568;">Tidak ada data dosen yang sesuai</div>
            <div style="font-size:13px; max-width:360px; margin:0 auto; line-height:1.6;">Silakan periksa kata kunci pencarian Anda atau muat ulang halaman.</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal BKD Dosen (Style SISTER Modal) -->
<div class="modal fade modal-profil" id="modalBkdDosen" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div id="modalBkdLoadingSpinner" class="modal-spinner-wrap" style="display:none;">
        <div class="modal-spinner"></div>
        <div style="font-size:13px; color:#718096; font-weight:700; font-family:'Nunito',sans-serif;">Memuat rekapitulasi BKD internal dosen...</div>
      </div>

      <div id="modalBkdContent" style="display:none; position:relative;">
        <!-- Header Modal -->
        <div class="modal-header-profil">
          <h5 class="modal-title-profil">
            <i class="fa fa-bar-chart" style="color: #3182CE;"></i> Evaluasi BKD Internal
          </h5>
          <button class="modal-close-btn" onclick="$('#modalBkdDosen').modal('hide')"><i class="fa fa-times"></i></button>
        </div>
        
        <div class="modal-body-profil">
          
          <!-- Banner Profil Ringkas (Foto + Data Utama) -->
          <div class="modal-banner-row">
            <div id="modalBkdAvatarWrap"></div>
            <div class="modal-banner-info">
              <h3 class="modal-banner-nama" id="modalBkdNama"></h3>
              <div class="modal-banner-nidn" id="modalBkdNidn"></div>
              <div class="modal-banner-badges">
                <span id="modalBkdProdi"><i class="fa fa-graduation-cap" style="margin-right:4px;"></i> -</span>
                <span id="modalBkdStatus"><i class="fa fa-briefcase" style="margin-right:4px;"></i> Dosen Tetap</span>
              </div>
            </div>
          </div>

          <!-- Section Tabel & Filter Tahun -->
          <div class="modal-table-container">
            <!-- Filter Bar -->
            <div class="modal-table-header-bar">
              <div style="display: flex; align-items: center; gap: 10px;">
                <label for="selectTahunBkd" style="margin: 0; font-size: 13px; font-weight: 700; color: #4A5568;">
                  <i class="fa fa-calendar" style="color: #3182CE; margin-right: 4px;"></i> Tahun Akademik:
                </label>
                <select id="selectTahunBkd" class="form-control" style="width: 140px; font-weight: 700; font-size: 13px; border-radius: 6px; padding: 4px 10px; height: 34px;" onchange="gantiTahunBkd(this.value)">
                  <?php foreach ($list_years as $y): ?>
                    <option value="<?= $y->year; ?>" <?= ($default_year == $y->year) ? 'selected' : ''; ?>>
                      <?= $y->label; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="font-size: 12px; color: #718096; font-weight: 600;">
                <i class="fa fa-info-circle"></i> Sesuai Standar Rubrik BKD Internal
              </div>
            </div>

            <!-- Content Area for Table -->
            <div class="modal-table-scroll-wrap" id="modalBkdTableContainer">
              <div id="tableSpinner" class="modal-spinner-wrap" style="display:none; padding: 40px 0;">
                <div class="modal-spinner"></div>
                <div style="font-size:12px; color:#718096; font-weight:700;">Menghitung data kinerja...</div>
              </div>
              <div id="tableContentArea"></div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
var allDosenBkd = [];
var filteredDosenBkd = [];
var activeDosen = null;

document.addEventListener('DOMContentLoaded', function() {
  loadDosenBkdList();
});

function loadDosenBkdList() {
  $.ajax({
    url: '<?= site_url("C_kinerja/ajax_daftar_bkd_dosen") ?>',
    type: 'POST',
    data: { <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp) {
      if (resp.status === 'success') {
        allDosenBkd = resp.data;
        filteredDosenBkd = allDosenBkd;
        renderGridBkd(filteredDosenBkd);
        updateStatsBkd();
      } else {
        alert(resp.message || 'Gagal memuat data dosen.');
      }
    },
    error: function() {
      alert('Terjadi kesalahan koneksi saat memuat daftar dosen BKD.');
    }
  });
}

function renderGridBkd(data) {
  var grid = document.getElementById('dosenGridBkd');
  if (data.length === 0) {
    grid.innerHTML = '';
    document.getElementById('noResultBkd').style.display = 'block';
    return;
  }
  document.getElementById('noResultBkd').style.display = 'none';
  var html = '';
  data.forEach(function(d, index) {
    var initials = getInitialsBkd(d.nama);
    var avatarHtml = d.foto_url
      ? '<img src="' + escBkd(d.foto_url) + '" class="dosen-avatar" alt="' + escBkd(d.nama) + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
        + '<div class="dosen-avatar-fallback" style="display:none;">' + initials + '</div>'
      : '<div class="dosen-avatar-fallback">' + initials + '</div>';
    
    var nama = d.nama || '-';
    var nidn = d.nidn || '-';
    var prodi = d.prodi_name || '';

    html += '<div class="dosen-card" onclick="bukaModalBkdByIndex(' + index + ')">'
          + '<div style="display:flex;flex-direction:column;align-items:center;">' + avatarHtml + '</div>'
          + '<div class="dosen-name">' + escBkd(nama) + '</div>'
          + '<div class="dosen-nidn">NIDN: ' + escBkd(nidn) + '</div>'
          + (prodi ? '<div class="dosen-prodi">' + escBkd(prodi) + '</div>' : '')
          + '<button class="btn-detail" onclick="event.stopPropagation(); bukaModalBkdByIndex(' + index + ')">'
          + '<i class="fa fa-bar-chart"></i> Lihat BKD</button>'
          + '</div>';
  });
  grid.innerHTML = html;
}

function updateStatsBkd() {
  document.getElementById('statsBarBkd').style.display = 'flex';
  document.getElementById('visibleCountBkd').textContent = filteredDosenBkd.length;
  document.getElementById('totalCountBkd').textContent = allDosenBkd.length;
}

function filterDosenBkd(kw) {
  kw = kw.toLowerCase().trim();
  filteredDosenBkd = kw ? allDosenBkd.filter(function(d) {
    return (d.nama || '').toLowerCase().indexOf(kw) !== -1
        || (d.nidn || '').toLowerCase().indexOf(kw) !== -1
        || (d.prodi_name || '').toLowerCase().indexOf(kw) !== -1;
  }) : allDosenBkd;
  renderGridBkd(filteredDosenBkd);
  updateStatsBkd();
}

function bukaModalBkdByIndex(index) {
  var d = filteredDosenBkd[index];
  if (!d) return;
  activeDosen = d;
  
  document.getElementById('modalBkdLoadingSpinner').style.display = 'flex';
  document.getElementById('modalBkdContent').style.display = 'none';
  $('#modalBkdDosen').modal('show');

  // Set banner data
  document.getElementById('modalBkdNama').textContent = d.nama;
  document.getElementById('modalBkdNidn').textContent = 'NIDN: ' + (d.nidn || '-');
  document.getElementById('modalBkdProdi').innerHTML = '<i class="fa fa-graduation-cap" style="margin-right:4px;"></i> ' + escBkd(d.prodi_name || 'PWBI');
  
  var initials = getInitialsBkd(d.nama);
  var avatarHtml = d.foto_url
    ? '<img src="' + escBkd(d.foto_url) + '" class="modal-banner-avatar" alt="' + escBkd(d.nama) + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">'
      + '<div class="modal-banner-avatar-fallback" style="display:none;">' + initials + '</div>'
    : '<div class="modal-banner-avatar-fallback">' + initials + '</div>';
  document.getElementById('modalBkdAvatarWrap').innerHTML = avatarHtml;

  var currentYear = document.getElementById('selectTahunBkd').value;
  loadDetailBkd(d.id_ptk, currentYear, d.user_id);
}

function gantiTahunBkd(year) {
  if (!activeDosen) return;
  loadDetailBkd(activeDosen.id_ptk, year, activeDosen.user_id);
}

function loadDetailBkd(id_ptk, year, user_id) {
  document.getElementById('tableSpinner').style.display = 'flex';
  document.getElementById('tableContentArea').style.display = 'none';

  $.ajax({
    url: '<?= site_url("C_kinerja/ajax_modal_bkd_detail") ?>',
    type: 'POST',
    data: {
      id_ptk: id_ptk,
      year: year,
      user_id: user_id,
      <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'
    },
    dataType: 'json',
    success: function(resp) {
      document.getElementById('modalBkdLoadingSpinner').style.display = 'none';
      document.getElementById('modalBkdContent').style.display = 'flex';
      document.getElementById('tableSpinner').style.display = 'none';
      document.getElementById('tableContentArea').style.display = 'block';

      if (resp.status === 'success') {
        document.getElementById('tableContentArea').innerHTML = resp.html;
        if (resp.dosen && resp.dosen.status) {
          document.getElementById('modalBkdStatus').innerHTML = '<i class="fa fa-briefcase" style="margin-right:4px;"></i> ' + escBkd(resp.dosen.status);
        }
      } else {
        document.getElementById('tableContentArea').innerHTML = '<div class="alert alert-danger" style="margin:20px;">' + (resp.message || 'Gagal memuat rincian BKD dosen.') + '</div>';
      }
    },
    error: function(xhr, status, error) {
      document.getElementById('modalBkdLoadingSpinner').style.display = 'none';
      document.getElementById('modalBkdContent').style.display = 'flex';
      document.getElementById('tableSpinner').style.display = 'none';
      document.getElementById('tableContentArea').style.display = 'block';
      var errDetail = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : (xhr.responseText ? xhr.responseText.replace(/<[^>]*>?/gm, ' ').substring(0, 200) : error);
      document.getElementById('tableContentArea').innerHTML = '<div class="alert alert-danger" style="margin:20px;"><strong>Terjadi kesalahan saat memuat data BKD:</strong><br><small>' + escBkd(errDetail) + '</small></div>';
    }
  });
}

function bukaModalMahasiswaBimbingan() {
  $('#modalMahasiswaBimbinganInternal').modal('show');
}

function getInitialsBkd(name) {
  if (!name) return 'D';
  var parts = name.trim().split(/\s+/);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
}

function escBkd(str) {
  if (!str) return '';
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>
