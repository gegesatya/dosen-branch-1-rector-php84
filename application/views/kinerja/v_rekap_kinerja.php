<style>
  .rekap-kinerja-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #E2E8F0;
    margin-bottom: 25px;
    overflow: hidden;
  }
  .rekap-kinerja-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #EDF2F7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
  }
  .rekap-kinerja-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #1A365D;
    letter-spacing: -0.3px;
  }
  .rekap-kinerja-subtitle {
    font-size: 13px;
    font-weight: 600;
    color: #718096;
    margin-top: 3px;
  }
  
  /* Filter Bar */
  .filter-wrapper {
    background: #F8FAFC;
    padding: 16px 24px;
    border-bottom: 1px solid #E2E8F0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
  }
  .filter-form-group {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .filter-label {
    font-size: 13px;
    font-weight: 700;
    color: #4A5568;
    margin: 0;
  }
  
  /* Excel-like Table Styling */
  .table-rekap-excel {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    color: #2D3748;
    margin: 0;
  }
  .table-rekap-excel th, 
  .table-rekap-excel td {
    border: 1px solid #CBD5E0 !important;
    padding: 9px 12px;
    vertical-align: middle;
  }
  
  /* Header warna peach / oranye persis Excel */
  .table-rekap-excel thead th {
    background-color: #F8CBAD !important;
    color: #1A202C !important;
    font-weight: 800 !important;
    text-align: center;
  }
  .table-rekap-excel thead th.th-main-group {
    background-color: #F4B084 !important;
    font-size: 14px;
    padding: 10px;
  }
  
  /* Highlight kolom persentase oranye lembut */
  .td-persen-sks {
    background-color: #FCE4D6 !important;
    font-weight: 700;
    text-align: center;
    color: #2D3748;
  }
  .td-persen-lulusan {
    background-color: #FCE4D6 !important;
    font-weight: 700;
    text-align: center;
    color: #2D3748;
  }
  
  .td-prodi {
    background-color: #FFFFFF;
    font-weight: 800;
    font-size: 13.5px;
    text-align: center;
    vertical-align: middle !important;
    color: #1A365D;
  }
  .td-nama-dosen {
    font-weight: 600;
    color: #2D3748;
  }
  .table-rekap-excel tbody tr:hover td {
    background-color: #EBF8FF !important;
  }
  .table-rekap-excel tbody tr:hover .td-persen-sks,
  .table-rekap-excel tbody tr:hover .td-persen-lulusan {
    background-color: #FBD5B5 !important;
  }
  
  /* Search input */
  .search-dosen-input {
    border: 1px solid #CBD5E0;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13px;
    width: 240px;
    outline: none;
    transition: all 0.2s;
  }
  .search-dosen-input:focus {
    border-color: #3182CE;
    box-shadow: 0 0 0 2px rgba(49,130,206,0.2);
  }
  
  @media print {
    .page-sidebar-wrapper, .page-header, .filter-wrapper, .btn-export-group, .page-footer {
      display: none !important;
    }
    .page-content-wrapper, .page-content {
      margin: 0 !important;
      padding: 0 !important;
    }
    .rekap-kinerja-card {
      border: none !important;
      box-shadow: none !important;
    }
    .table-rekap-excel th, .table-rekap-excel td {
      border: 1px solid #000000 !important;
    }
  }
</style>

<div class="page-content" style="padding-top:0">
  <div class="row">
    <div class="col-md-12">
      
      <div class="rekap-kinerja-card">
        <!-- Header -->
        <div class="rekap-kinerja-header">
          <div>
            <h3 class="rekap-kinerja-title">
              <i class="icon-briefcase" style="color:#3182CE;margin-right:8px;"></i>
              <?= $judul_besar; ?>
            </h3>
            <div class="rekap-kinerja-subtitle">
              <?= $judul_kecil; ?> &bull; Data Rekapitulasi Kinerja Seluruh Dosen
              <?php if (!empty($last_rekap_time)): ?>
                &bull; <span style="color:#4A5568;"><i class="fa fa-clock-o"></i> Terakhir direkap: <b><?= date('d M Y H:i', strtotime($last_rekap_time)); ?> WIB</b></span>
              <?php else: ?>
                &bull; <span style="color:#E53E3E;"><i class="fa fa-exclamation-circle"></i> Data belum direkap untuk tahun ini. Silakan klik tombol <b>Rekap Data</b>.</span>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="btn-export-group" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <button type="button" class="btn btn-sm btn-warning" id="btnRekapData" onclick="generateRekapData()" style="border-radius:6px;font-weight:700;background-color:#DD6B20;border-color:#C05621;color:#fff;">
              <i class="fa fa-calculator"></i> Rekap Data
            </button>
            <button type="button" class="btn btn-sm btn-primary" id="btnSyncPKL" onclick="syncDataPKL()" style="border-radius:6px;font-weight:700;background-color:#3182CE;border-color:#2B6CB0;">
              <i class="fa fa-refresh"></i> Sinkron Pendamping PKL
            </button>
            <button type="button" class="btn btn-sm btn-default" onclick="window.print()" style="border-radius:6px;font-weight:700;">
              <i class="fa fa-print"></i> Cetak / PDF
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="exportTableToExcel('tableRekapKinerja', 'Rekap_Kinerja_Dosen_<?= $selected_year ?>')" style="border-radius:6px;font-weight:700;">
              <i class="fa fa-file-excel-o"></i> Export Excel
            </button>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-wrapper">
          <form action="<?= site_url('C_kinerja/rekap'); ?>" method="get" class="form-inline" id="formFilterTahun">
            <div class="filter-form-group">
              <label class="filter-label">Tahun Akademik:</label>
              <select name="year" class="form-control input-sm select2" onchange="document.getElementById('formFilterTahun').submit()" style="min-width:140px;">
                <?php foreach ($list_years as $y) : ?>
                  <option value="<?= $y->year; ?>" <?= ($selected_year == $y->year) ? 'selected' : ''; ?>>
                    <?= $y->label; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </form>

          <div>
            <input type="text" id="inputSearchDosen" class="search-dosen-input" placeholder="Cari nama dosen / prodi..." onkeyup="filterTableDosen()">
          </div>
        </div>

        <!-- Table Body -->
        <div class="table-responsive" style="padding: 0;">
          <table class="table-rekap-excel" id="tableRekapKinerja">
            <thead>
              <tr>
                <th rowspan="2" width="90" style="vertical-align:middle;">Prodi</th>
                <th rowspan="2" width="280" style="vertical-align:middle; text-align:left; padding-left:14px;">Nama Dosen</th>
                <th colspan="6" class="th-main-group">Pendidikan dan Pengajaran</th>
              </tr>
              <tr>
                <th width="150" style="vertical-align:middle;">Jumlah Jam (sks x minggu)</th>
                <th width="130" style="vertical-align:middle;">Standar Jam/Tahun</th>
                <th width="130" style="vertical-align:middle;">% Pencapaian SKS</th>
                <th width="130" style="vertical-align:middle;">Jumlah Poin Lulusan</th>
                <th width="130" style="vertical-align:middle;">Standar Poin/tahun</th>
                <th width="130" style="vertical-align:middle;">% Pencapaian Lulusan</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $total_seluruh_dosen = 0;
              if (!empty($rekap_per_prodi)): 
                foreach ($rekap_per_prodi as $prodi_key => $pdata):
                  $dosen_list = $pdata['dosen'];
                  $rowspan_prodi = count($dosen_list);
                  if ($rowspan_prodi == 0) continue;
                  
                  foreach ($dosen_list as $idx => $d):
                    $total_seluruh_dosen++;
              ?>
                <tr class="row-dosen" data-prodi="<?= strtolower($prodi_key . ' ' . $pdata['prodi_name']); ?>" data-nama="<?= strtolower((string)$d->nm_ptk); ?>">
                  <?php if ($idx === 0): ?>
                    <td rowspan="<?= $rowspan_prodi; ?>" class="td-prodi">
                      <?= $prodi_key; ?>
                    </td>
                  <?php endif; ?>
                  
                  <td class="td-nama-dosen" style="padding-left:14px;">
                    <?= htmlspecialchars($d->nm_ptk); ?>
                  </td>

                  <!-- Jumlah Jam (sks x minggu) -->
                  <td class="text-right" style="font-weight:600; padding-right:16px;">
                    <?= $d->teaching_yearly_total > 0 ? number_format($d->teaching_yearly_total, 2) : '-'; ?>
                  </td>

                  <!-- Standar Jam/Tahun -->
                  <td class="text-center" style="font-weight:600;">
                    <?= $d->standar_jam; ?>
                  </td>

                  <!-- % Pencapaian SKS -->
                  <td class="td-persen-sks">
                    <?= number_format($d->persen_sks, 1); ?>%
                  </td>

                  <!-- Jumlah Poin Lulusan -->
                  <td class="text-center" style="font-weight:600;">
                    <?= $d->skripsi_yearly_sum > 0 ? rtrim(rtrim(number_format($d->skripsi_yearly_sum, 2), '0'), '.') : '0'; ?>
                  </td>

                  <!-- Standar Poin/tahun -->
                  <td class="text-center" style="font-weight:600;">
                    <?= $d->standar_lulusan; ?>
                  </td>

                  <!-- % Pencapaian Lulusan -->
                  <td class="td-persen-lulusan">
                    <?= number_format($d->persen_lulusan, 2); ?>%
                  </td>
                </tr>
              <?php 
                  endforeach;
                endforeach; 
              endif;

              if ($total_seluruh_dosen == 0):
              ?>
                <tr>
                  <td colspan="8" class="text-center" style="padding: 30px; color:#A0AEC0; font-weight:700;">
                    Tidak ada data dosen yang ditemukan untuk Tahun Akademik <?= $selected_year; ?>.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
function generateRekapData() {
  var selectElem = document.querySelector('select[name="year"]');
  var tahun = selectElem ? selectElem.value : '<?= $selected_year ?>';
  var tahunTeks = selectElem ? selectElem.options[selectElem.selectedIndex].text : ('TA ' + tahun + '/' + (parseInt(tahun)+1));

  if (!confirm('Apakah Anda yakin ingin menghitung dan mengupdate ulang data rekapitulasi kinerja dosen untuk ' + tahunTeks + '?')) {
    return;
  }
  
  var btn = document.getElementById('btnRekapData');
  var originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses Update...';
  
  $.ajax({
    url: '<?= site_url("C_kinerja/ajax_generate_rekap") ?>',
    type: 'POST',
    data: {
      year: tahun,
      <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'
    },
    dataType: 'json',
    success: function(resp) {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      if (resp.status) {
        alert(resp.msg || 'Data rekapitulasi berhasil diperbarui!');
        window.location.href = '<?= site_url("C_kinerja/rekap?year=") ?>' + encodeURIComponent(tahun);
      } else {
        alert('Gagal: ' + (resp.msg || 'Terjadi kesalahan saat memproses rekap.'));
      }
    },
    error: function(xhr, status, error) {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      alert('Terjadi kesalahan koneksi saat memproses rekap data: ' + error);
    }
  });
}

function syncDataPKL() {
  if (!confirm('Apakah Anda yakin ingin menyinkronkan data pembimbing PKL dari tabel lokasi magang ke eKinerja pendampingan?')) {
    return;
  }
  
  var btn = document.getElementById('btnSyncPKL');
  var originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyinkronkan...';
  
  $.ajax({
    url: '<?= site_url("C_kinerja/ajax_sync_pkl") ?>',
    type: 'POST',
    data: {
      <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'
    },
    dataType: 'json',
    success: function(resp) {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      if (resp.status) {
        alert(resp.msg || 'Sinkronisasi berhasil!');
        location.reload();
      } else {
        alert('Gagal: ' + (resp.msg || 'Terjadi kesalahan.'));
      }
    },
    error: function() {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      alert('Terjadi kesalahan koneksi ke server.');
    }
  });
}

function filterTableDosen() {
  var input = document.getElementById('inputSearchDosen');
  var filter = input.value.toLowerCase().trim();
  var rows = document.querySelectorAll('.row-dosen');
  
  rows.forEach(function(row) {
    var nama = row.getAttribute('data-nama') || '';
    var prodi = row.getAttribute('data-prodi') || '';
    if (nama.indexOf(filter) !== -1 || prodi.indexOf(filter) !== -1) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function exportTableToExcel(tableID, filename) {
  var downloadLink;
  var dataType = 'application/vnd.ms-excel';
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
  
  filename = filename ? filename + '.xls' : 'rekap_kinerja.xls';
  downloadLink = document.createElement("a");
  document.body.appendChild(downloadLink);
  
  if (navigator.msSaveOrOpenBlob) {
    var blob = new Blob(['\ufeff', tableSelect.outerHTML], {
      type: dataType
    });
    navigator.msSaveOrOpenBlob(blob, filename);
  } else {
    downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    downloadLink.download = filename;
    downloadLink.click();
  }
}
</script>
