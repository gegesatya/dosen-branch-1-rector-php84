<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .pp-left {
    background: linear-gradient(160deg, #1A365D 0%, #2B6CB0 100%);
    border-radius: 12px;
    padding: 28px 20px;
    display: flex; flex-direction: column; align-items: center;
    font-family: 'Nunito', sans-serif;
    position: sticky; top: 20px;
  }
  .pp-avatar-wrap {
    width: 120px; height: 180px; border-radius: 8px; overflow: hidden;
    border: 3px solid rgba(255,255,255,0.35);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    margin-bottom: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white; font-weight: 800;
  }
  .pp-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }
  .pp-nama { font-size: 14px; font-weight: 800; color: #fff; text-align: center; margin-bottom: 4px; line-height: 1.4; }
  .pp-nidn { font-size: 11px; color: rgba(255,255,255,0.6); text-align: center; margin-bottom: 12px; }
  .pp-badges { display: flex; flex-wrap: wrap; gap: 5px; justify-content: center; margin-bottom: 18px; }
  .pp-badges span {
    font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); color: #fff;
  }
  .pp-divider { border-top: 1px solid rgba(255,255,255,0.15); width: 100%; margin-bottom: 14px; }
  .pp-info-label { font-size: 9px; font-weight: 800; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
  .pp-info-row { display: flex; align-items: flex-start; margin-bottom: 8px; }
  .pp-info-row .k { font-size: 10px; color: rgba(255,255,255,0.5); font-weight: 700; width: 90px; flex-shrink: 0; }
  .pp-info-row .v { font-size: 11px; color: #fff; font-weight: 700; }

  /* Right */
  .pp-right { background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(49,130,206,0.07); min-height: 600px; font-family: 'Nunito', sans-serif; }
  .pp-tab-bar { display: flex; gap: 0; border-bottom: 2px solid #EBF8FF; padding: 0 20px; flex-wrap: wrap; }
  .pp-tab {
    padding: 14px 20px; font-size: 14px; font-weight: 700; color: #718096; cursor: pointer;
    border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.15s;
  }
  .pp-tab:hover { color: #3182CE; }
  .pp-tab.active { color: #3182CE; border-bottom-color: #3182CE; font-weight: 800; }
  .pp-pane { display: none; padding: 22px 26px; }
  .pp-pane.active { display: block; }
  .pp-section-title {
    font-size: 13.5px; font-weight: 800; color: #4A5568; text-transform: uppercase;
    letter-spacing: 0.6px; margin: 0 0 14px 0; padding-bottom: 8px;
    border-bottom: 2px solid #EBF8FF; display: flex; align-items: center; gap: 8px;
  }
  .pp-section-title + .pp-section-title, .mt-section { margin-top: 26px; }
  .pp-row { display: flex; align-items: flex-start; margin-bottom: 10px; }
  .pp-row .lbl { width: 180px; flex-shrink: 0; font-size: 13.5px; font-weight: 700; color: #718096; }
  .pp-row .val { font-size: 14px; font-weight: 700; color: #2D3748; }
  .pp-row .val.empty { color: #CBD5E0; }

  /* Tridharma tabs */
  .tri-tabs { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
  .tri-tab {
    padding: 7px 18px; font-size: 13px; font-weight: 700; color: #718096;
    border-radius: 8px; cursor: pointer; background: #F7FAFC;
    border: 1px solid #E2E8F0; transition: all 0.15s; font-family: 'Nunito', sans-serif;
  }
  .tri-tab.active { background: #EBF8FF; color: #2B6CB0; border-color: #BEE3F8; font-weight: 800; }
  .tri-pane { display: none; }
  .tri-pane.active { display: block; }
  .tri-empty { text-align: center; color: #A0AEC0; padding: 28px 0; font-size: 14px; font-weight: 700; }

  /* Table */
  .pp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; font-family: 'Nunito', sans-serif; }
  .pp-table th { background: #EDF2F7; color: #4A5568; font-weight: 800; padding: 11px 14px; text-align: left; font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; }
  .pp-table td { padding: 12px 14px; border-bottom: 1px solid #F7FAFC; color: #2D3748; font-weight: 600; vertical-align: top; line-height: 1.45; }
  .pp-table tr:hover td { background: #F7FAFC; }
  .pp-table-wrap { max-height: 340px; overflow-y: auto; border-radius: 8px; border: 1px solid #E2E8F0; }

  /* KUM badge */
  .kum-badge { font-size: 12px; font-weight: 700; color: #6B46C1; background: #FAF5FF; padding: 3px 10px; border-radius: 999px; }

  /* BKD */
  .bkd-status-card {
    border-radius: 12px; padding: 16px 20px; margin-bottom: 18px;
    display: flex; align-items: center; gap: 18px;
  }
  .bkd-status-card.hijau { background: #F0FFF4; border: 1px solid #C6F6D5; }
  .bkd-status-card.merah { background: #FFF5F5; border: 1px solid #FED7D7; }
  .bkd-status-card.abu   { background: #F7FAFC; border: 1px solid #E2E8F0; }
  .bkd-dot { width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; }
  .bkd-dot.hijau { background: #38A169; }
  .bkd-dot.merah { background: #E53E3E; }
  .bkd-dot.abu   { background: #A0AEC0; }
  .bkd-progress-bar-wrap { background: #E2E8F0; border-radius: 999px; height: 8px; overflow: hidden; margin-top: 6px; }
  .bkd-progress-bar { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
  .bkd-smt-selector { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
  .bkd-smt-btn {
    padding: 5px 14px; font-size: 12px; font-weight: 700; border-radius: 6px; cursor: pointer;
    border: 1px solid #E2E8F0; background: #F7FAFC; color: #718096; font-family: 'Nunito', sans-serif;
    transition: all 0.15s;
  }
  .bkd-smt-btn:hover, .bkd-smt-btn.active { background: #EBF8FF; color: #2B6CB0; border-color: #BEE3F8; }
  .bkd-loading { text-align: center; padding: 40px 0; color: #A0AEC0; font-size: 14px; }

  /* Spinner */
  .pp-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 0; gap: 14px; }
  .pp-spinner { width: 46px; height: 46px; border: 4px solid rgba(49,130,206,0.15); border-top: 4px solid #3182CE; border-radius: 50%; animation: ppspin 0.9s linear infinite; }
  @keyframes ppspin { to { transform: rotate(360deg); } }
</style>

<div class="page-content" style="padding-top: 0;">
  <div class="container-fluid" style="font-family:'Nunito',sans-serif;">

    <?php
    $u_login_obj = $this->ion_auth->user()->row();
    $u_login_email = $u_login_obj->email ?? '';
    $u_login_uname = $u_login_obj->username ?? '';
    $is_gege_pribadi = (stripos($u_login_email, 'gege.satya') !== false || stripos($u_login_uname, 'gege.satya') !== false);
    ?>
    <!-- Title Bar -->
    <div class="row" style="margin-bottom: 20px; padding-top: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 15px;">
      <div class="<?= $is_gege_pribadi ? 'col-md-8' : 'col-md-12'; ?>">
        <h1 style="font-size:22px;font-weight:800;color:#1A365D;margin:0 0 4px 0;">
          <i class="fa fa-user-circle" style="color:#3182CE;margin-right:8px;"></i>Profil Saya
        </h1>
        <p style="color:#718096;font-size:13px;margin:0;">Data profil dosen terintegrasi SISTER Kemdiktisaintek</p>
      </div>
      <?php if ($is_gege_pribadi): ?>
      <div class="col-md-4 text-right" style="display:flex;align-items:center;justify-content:flex-end;">
        <button onclick="syncPribadi()" style="background:linear-gradient(135deg,#38A169,#2F855A);color:#fff;font-weight:700;border:none;border-radius:8px;padding:8px 18px;font-size:13px;cursor:pointer;font-family:'Nunito',sans-serif;">
          <i class="fa fa-refresh" id="pp-sync-icon"></i> Perbarui Data Saya
        </button>
      </div>
      <?php endif; ?>
    </div>

    <!-- Loading -->
    <div id="pp-loading" class="pp-loading">
      <div class="pp-spinner"></div>
      <div style="font-size:13px;font-weight:700;color:#718096;">Memuat profil Anda dari SISTER...</div>
    </div>

    <!-- Error -->
    <div id="pp-error" style="display:none;text-align:center;padding:60px 0;">
      <i class="fa fa-exclamation-circle" style="font-size:40px;color:#FC8181;display:block;margin-bottom:12px;"></i>
      <div style="font-weight:800;font-size:15px;color:#4A5568;margin-bottom:6px;">Profil belum tersinkronisasi</div>
      <div style="font-size:13px;color:#718096;max-width:380px;margin:0 auto;">Data profil belum tersedia dari SISTER.</div>
    </div>

    <!-- Content -->
    <div id="pp-content" style="display:none;">
      <div class="row">
        <!-- KOLOM KIRI -->
        <div class="col-md-3">
          <div class="pp-left">
            <div class="pp-avatar-wrap" id="ppAvatar"></div>
            <div class="pp-nama" id="ppNama">-</div>
            <div class="pp-nidn" id="ppNidn">NIDN: -</div>
            <div class="pp-badges" id="ppBadges"></div>
            <div class="pp-divider"></div>
            <div class="pp-info-label">Info Dasar</div>
            <div id="ppInfoSisi"></div>
          </div>
        </div>

        <!-- KOLOM KANAN -->
        <div class="col-md-9">
          <div class="pp-right">
            <div class="pp-tab-bar">
              <div class="pp-tab active" onclick="switchPPTab('kepegawaian',this)"><i class="fa fa-briefcase"></i> Kepegawaian</div>
              <div class="pp-tab" onclick="switchPPTab('kependudukan',this)"><i class="fa fa-id-card"></i> Kependudukan &amp; Keluarga</div>
              <div class="pp-tab" onclick="switchPPTab('tridharma',this)"><i class="fa fa-bar-chart"></i> Tridharma</div>
              <div class="pp-tab" onclick="switchPPTab('bkd',this)"><i class="fa fa-check-circle"></i> BKD</div>
            </div>

            <!-- TAB KEPEGAWAIAN -->
            <div id="pptab-kepegawaian" class="pp-pane active">
              <div class="pp-section-title"><i class="fa fa-user"></i> Identitas Dosen</div>
              <div id="ppIdentitasKepegawaian"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-building"></i> Tempat Bekerja (Penugasan)</div>
              <div id="ppPenugasan"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-flask"></i> Bidang Ilmu</div>
              <div id="ppBidangIlmu"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-briefcase"></i> Riwayat Jabatan Fungsional</div>
              <div id="ppJabFung"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-certificate"></i> Riwayat Inpassing (KUM)</div>
              <div id="ppInpassing"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-university"></i> Riwayat Pendidikan Formal</div>
              <div id="ppPendidikan"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-history"></i> Riwayat Pekerjaan / Pengalaman Kerja</div>
              <div id="ppRiwayatPekerjaan"></div>
            </div>

            <!-- TAB KEPENDUDUKAN -->
            <div id="pptab-kependudukan" class="pp-pane">
              <div class="pp-section-title"><i class="fa fa-id-card"></i> Data Kependudukan</div>
              <div id="ppKependudukan"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-users"></i> Data Keluarga</div>
              <div id="ppKeluarga"></div>
              <div class="pp-section-title mt-section"><i class="fa fa-address-book"></i> Kontak &amp; Alamat</div>
              <div id="ppKontak"></div>
            </div>

            <!-- TAB TRIDHARMA -->
            <div id="pptab-tridharma" class="pp-pane">
              <div class="tri-tabs">
                <div class="tri-tab active" onclick="switchPPTri('pengajaran',this)"><i class="fa fa-book"></i> Pengajaran</div>
                <div class="tri-tab" onclick="switchPPTri('penelitian',this)"><i class="fa fa-flask"></i> Penelitian</div>
                <div class="tri-tab" onclick="switchPPTri('pengabdian',this)"><i class="fa fa-users"></i> Pengabdian</div>
              </div>
              <div id="pptri-pengajaran" class="tri-pane active"></div>
              <div id="pptri-penelitian" class="tri-pane"></div>
              <div id="pptri-pengabdian" class="tri-pane"></div>
            </div>

            <!-- TAB BKD -->
            <div id="pptab-bkd" class="pp-pane">
              <div id="bkd-container">
                <div class="bkd-loading"><i class="fa fa-spinner fa-spin" style="font-size:22px;"></i><br><br>Memuat data BKD dari SISTER...</div>
              </div>
            </div>

          </div><!-- /pp-right -->
      </div>
    </div>

  </div>
</div>

<script>
var ppUsername = '<?= $this->ion_auth->user()->row()->username ?>';
var bkdLoaded = false;
var bkdData = null;

function esc(s){ return s ? String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : ''; }
function getInitials(n){ if(!n) return '?'; return n.split(' ').slice(0,2).map(function(w){return w.charAt(0).toUpperCase();}).join(''); }
function ppRow(lbl, val){
  var v = val && String(val).trim() !== '' && val !== 'null' ? val : null;
  return '<div class="pp-row"><div class="lbl">'+lbl+'</div><div class="val'+(v?'':' empty')+'">'+esc(v||'-')+'</div></div>';
}

function formatTglIndo(tglStr){
  if(!tglStr || tglStr==='null' || tglStr==='0000-00-00') return '-';
  var parts = String(tglStr).split('-');
  if(parts.length < 3) return tglStr;
  var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  var bIdx = parseInt(parts[1],10) - 1;
  return parseInt(parts[2],10) + ' ' + (bulan[bIdx] || parts[1]) + ' ' + parts[0];
}

function switchPPTab(name, el){
  document.querySelectorAll('.pp-tab').forEach(function(t){ t.classList.remove('active'); });
  document.querySelectorAll('.pp-pane').forEach(function(p){ p.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('pptab-'+name).classList.add('active');
  if(name === 'bkd' && !bkdLoaded){ loadBKD(); }
}
function switchPPTri(name, el){
  document.querySelectorAll('.tri-tab').forEach(function(t){ t.classList.remove('active'); });
  document.querySelectorAll('.tri-pane').forEach(function(p){ p.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('pptri-'+name).classList.add('active');
}

function loadPPProfil(){
  document.getElementById('pp-loading').style.display = 'flex';
  document.getElementById('pp-content').style.display = 'none';
  document.getElementById('pp-error').style.display = 'none';
  $.ajax({
    url: '<?= site_url("C_sister/ajax_detail_profil_dosen") ?>',
    type: 'POST',
    data: { username: ppUsername, <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp){
      document.getElementById('pp-loading').style.display = 'none';
      if(resp.status === 'success' && resp.data){
        renderPP(resp.data);
        document.getElementById('pp-content').style.display = 'block';
      } else {
        document.getElementById('pp-error').style.display = 'block';
      }
    },
    error: function(){
      document.getElementById('pp-loading').style.display = 'none';
      document.getElementById('pp-error').style.display = 'block';
    }
  });
}

function renderPP(d){
  var nama = d.nama_lengkap || d.nama || '-';
  var initials = getInitials(nama);

  // Avatar
  var av = d.foto_url
    ? '<img src="'+esc(d.foto_url)+'" onerror="this.parentNode.innerHTML=\'<div style=\\\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:28px;color:white;font-weight:800;\\\'>'+initials+'</div>\'">'
    : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:28px;color:white;font-weight:800;">'+initials+'</div>';
  document.getElementById('ppAvatar').innerHTML = av;
  document.getElementById('ppNama').textContent = nama;
  document.getElementById('ppNidn').textContent = 'NIDN: '+(d.nidn || d.username || '-');

  // Badges
  var b = '';
  if(d.jabatan_fungsional) b += '<span>'+esc(d.jabatan_fungsional)+'</span>';
  var kum = '';
  if(d.list_inpassing && d.list_inpassing.length > 0){
    for(var i=0;i<d.list_inpassing.length;i++){ if(d.list_inpassing[i].angka_kredit){ kum=d.list_inpassing[i].angka_kredit; break; } }
  }
  if(kum) b += '<span>KUM '+esc(kum)+'</span>';
  var aktif = d.status_aktif=='1' || d.status_aktif==='Aktif';
  b += '<span style="background:'+(aktif?'rgba(56,161,105,0.35)':'rgba(229,62,62,0.35)')+';">'+(aktif?'Aktif':'Non-Aktif')+'</span>';
  document.getElementById('ppBadges').innerHTML = b;

  // Info Sisi Kiri
  var sf = [['Email',d.email],['No HP',d.no_hp],['Gelar',d.gelar_akademik],['Pend. Terakhir',d.pendidikan_terakhir],['Tgl Lahir',formatTglIndo(d.tanggal_lahir)]];
  var sHtml = '';
  sf.forEach(function(f){
    var v = f[1] && String(f[1]).trim()!=='' && f[1]!=='null' ? f[1] : null;
    sHtml += '<div class="pp-info-row"><div class="k">'+f[0]+'</div><div class="v">'+esc(v||'-')+'</div></div>';
  });
  document.getElementById('ppInfoSisi').innerHTML = sHtml;

  // 1. Identitas Dosen (Tab Kepegawaian)
  var jkText = d.jenis_kelamin === 'L' ? 'Laki-laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : (d.jenis_kelamin || '-'));
  var ttlText = (d.tempat_lahir ? d.tempat_lahir : '') + (d.tempat_lahir && d.tanggal_lahir ? ', ' : '') + formatTglIndo(d.tanggal_lahir);
  document.getElementById('ppIdentitasKepegawaian').innerHTML =
    ppRow('Nama Lengkap', d.nama_lengkap || d.nama) +
    ppRow('Gelar Akademik', d.gelar_akademik) +
    ppRow('Jenis Kelamin', jkText) +
    ppRow('Tempat, Tgl Lahir', ttlText) +
    ppRow('Nama Ibu Kandung', d.nama_ibu_kandung) +
    ppRow('Agama', d.agama) +
    ppRow('NIDN / NUPTK', (d.nidn || '-') + (d.nuptk ? ' / ' + d.nuptk : '')) +
    ppRow('NIP', d.nip) +
    ppRow('Status Perkawinan', d.stat_kawin);

  // 2. Penugasan
  document.getElementById('ppPenugasan').innerHTML =
    ppRow('Perguruan Tinggi', d.perguruan_tinggi) +
    ppRow('Prodi / Unit Kerja', d.unit_kerja) +
    ppRow('Status Kepegawaian', d.status_kepegawaian) +
    ppRow('Ikatan Kerja', d.ikatan_kerja_dosen) +
    ppRow('Tanggal Mulai', formatTglIndo(d.tanggal_mulai_penugasan));

  // 3. Bidang Ilmu
  var bsH = '';
  if(d.list_bidang_ilmu && d.list_bidang_ilmu.length > 0){
    bsH = '<ul style="margin:0;padding-left:18px;color:#2D3748;font-size:13.5px;font-weight:700;">';
    d.list_bidang_ilmu.forEach(function(bi){ bsH += '<li style="margin-bottom:6px;">'+esc(bi.kelompok_bidang)+'</li>'; });
    bsH += '</ul>';
  } else { bsH = '<span style="color:#CBD5E0;font-weight:700;">-</span>'; }
  document.getElementById('ppBidangIlmu').innerHTML = bsH;

  // 4. Jabatan Fungsional
  var jfH = '';
  if(d.list_jabatan_fungsional && d.list_jabatan_fungsional.length > 0){
    d.list_jabatan_fungsional.forEach(function(j){
      jfH += '<div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #F7FAFC;">'
           + '<div style="font-weight:800;color:#1A365D;font-size:14px;">'+esc(j.jabatan_fungsional)+'</div>'
           + '<div style="font-size:12.5px;color:#718096;margin-top:4px;">SK: <strong style="color:#2D3748;">'+esc(j.sk||'-')+'</strong> &nbsp;|&nbsp; Mulai: <strong style="color:#2D3748;">'+formatTglIndo(j.tanggal_mulai)+'</strong></div>'
           + '</div>';
    });
  } else { jfH = '<span style="color:#CBD5E0;font-weight:700;">-</span>'; }
  document.getElementById('ppJabFung').innerHTML = jfH;

  // 5. Inpassing
  var inpH = '';
  if(d.list_inpassing && d.list_inpassing.length > 0){
    d.list_inpassing.forEach(function(i){
      inpH += '<div style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #F7FAFC;">'
            + '<div style="font-weight:800;color:#1A365D;font-size:14px;">'+esc(i.pangkat_golongan||'-')+' <span class="kum-badge">KUM '+esc(i.angka_kredit||'0')+'</span></div>'
            + '<div style="font-size:12.5px;color:#718096;margin-top:4px;">SK: <strong style="color:#2D3748;">'+esc(i.sk||'-')+'</strong> &nbsp;|&nbsp; Tgl SK: <strong style="color:#2D3748;">'+formatTglIndo(i.tanggal_sk)+'</strong> &nbsp;|&nbsp; Mulai: <strong style="color:#2D3748;">'+formatTglIndo(i.tanggal_mulai)+'</strong></div>'
            + '</div>';
    });
  } else { inpH = '<span style="color:#CBD5E0;font-weight:700;">-</span>'; }
  document.getElementById('ppInpassing').innerHTML = inpH;

  // 6. Pendidikan Formal
  var pendH = '';
  if(d.list_pendidikan_formal && d.list_pendidikan_formal.length > 0){
    pendH += '<div class="pp-table-wrap" style="max-height:260px;margin-bottom:8px;"><table class="pp-table"><thead><tr><th width="45">#</th><th>Jenjang &amp; Perguruan Tinggi</th><th>Gelar Akademik</th><th>Bidang Studi</th><th width="100">Tahun Lulus</th></tr></thead><tbody>';
    d.list_pendidikan_formal.forEach(function(p, i){
      pendH += '<tr>'
             + '<td style="color:#718096;font-weight:700;">'+(i+1)+'</td>'
             + '<td style="font-weight:700;color:#1A365D;font-size:14px;">'+esc(p.jenjang_pendidikan||'-')+'<br><span style="font-size:12px;color:#718096;font-weight:600;">'+esc(p.nama_perguruan_tinggi||'-')+'</span></td>'
             + '<td style="font-weight:700;color:#2D3748;">'+esc(p.gelar_akademik||'-')+'</td>'
             + '<td style="font-size:13px;color:#4A5568;">'+esc(p.bidang_studi||'-')+'</td>'
             + '<td style="font-weight:800;color:#2B6CB0;">'+esc(p.tahun_lulus||'-')+'</td>'
             + '</tr>';
    });
    pendH += '</tbody></table></div>';
  } else { pendH = '<div style="font-size:13px;color:#A0AEC0;font-weight:600;padding:12px 16px;background:#F7FAFC;border-radius:8px;border:1px dashed #E2E8F0;text-align:center;">Belum ada data pendidikan formal.</div>'; }
  document.getElementById('ppPendidikan').innerHTML = pendH;

  // 7. Riwayat Pekerjaan (Di Bawah Pendidikan Formal)
  var pekH = '';
  if (d.list_riwayat_pekerjaan && d.list_riwayat_pekerjaan.length > 0) {
    pekH = '<div class="pp-table-wrap" style="max-height:280px;"><table class="pp-table"><thead><tr><th width="45">#</th><th>Instansi / Perusahaan</th><th>Jabatan / Posisi</th><th>Divisi / Unit</th><th>Jenis Pekerjaan</th><th>Bidang Usaha</th><th width="150">Periode Kerja</th></tr></thead><tbody>';
    d.list_riwayat_pekerjaan.forEach(function(p, i) {
      var mulai = formatTglIndo(p.mulai_bekerja);
      var selesai = p.selesai_bekerja ? formatTglIndo(p.selesai_bekerja) : '<span style="color:#38A169;font-weight:800;">Sekarang</span>';
      var periode = mulai + ' s/d ' + selesai;
      pekH += '<tr>'
           + '<td style="color:#718096;font-weight:700;">' + (i + 1) + '</td>'
           + '<td style="font-weight:700;color:#1A365D;font-size:14px;">' + esc(p.instansi || '-') + '</td>'
           + '<td style="font-weight:700;color:#2D3748;">' + esc(p.nama_jabatan || '-') + '</td>'
           + '<td style="font-size:13px;color:#4A5568;">' + esc(p.divisi || '-') + '</td>'
           + '<td style="font-size:13px;color:#4A5568;">' + esc(p.jenis_pekerjaan || '-') + '</td>'
           + '<td style="font-size:12px;color:#718096;">' + esc(p.bidang_usaha || '-') + '</td>'
           + '<td style="font-size:12.5px;font-weight:700;color:#2B6CB0;">' + periode + '</td>'
           + '</tr>';
    });
    pekH += '</tbody></table></div>';
  } else {
    pekH = '<div style="font-size:13px;color:#A0AEC0;font-weight:600;padding:14px 18px;background:#F7FAFC;border-radius:8px;border:1px dashed #E2E8F0;text-align:center;"><i class="fa fa-info-circle" style="margin-right:6px;"></i>Belum ada data riwayat pekerjaan yang tercatat di SISTER.</div>';
  }
  document.getElementById('ppRiwayatPekerjaan').innerHTML = pekH;

  // 8. Kependudukan & Keluarga
  document.getElementById('ppKependudukan').innerHTML =
    ppRow('NIK', d.nik) + ppRow('Agama', d.agama) + ppRow('Kewarganegaraan', d.kewarganegaraan === 'ID' ? 'Indonesia' : d.kewarganegaraan);
  document.getElementById('ppKeluarga').innerHTML =
    ppRow('Status Kawin', d.stat_kawin) + ppRow('Nama Pasangan', d.nm_suami_istri);
  document.getElementById('ppKontak').innerHTML =
    ppRow('Email', d.sister_email || d.email) + ppRow('No HP', d.no_hp) + ppRow('No Telp Rumah', d.no_tel_rmh) + ppRow('Alamat', d.alamat);

  // 9. Tridharma - Tabel
  renderPPTriTable('pengajaran', d.pengajaran || []);
  renderPPTriTable('penelitian', d.penelitian || []);
  renderPPTriTable('pengabdian', d.pengabdian || []);
}

function renderPPTriTable(type, items){
  var el = document.getElementById('pptri-'+type);
  if(!items || items.length === 0){
    el.innerHTML = '<div class="tri-empty"><i class="fa fa-inbox"></i> Belum ada data '+type+'.</div>';
    return;
  }
  var html = '<div class="pp-table-wrap"><table class="pp-table">';
  if(type === 'pengajaran'){
    html += '<thead><tr><th>#</th><th>Mata Kuliah</th><th>SKS</th><th>Semester</th><th>Tahun Ajaran</th></tr></thead><tbody>';
    items.forEach(function(item, i){
      html += '<tr><td>'+(i+1)+'</td><td>'+esc(item.nama_mata_kuliah||'-')+'</td><td>'+esc(item.sks||'-')+'</td><td>'+esc(item.semester||'-')+'</td><td>'+esc(item.tahun_ajaran||'-')+'</td></tr>';
    });
  } else if(type === 'penelitian'){
    html += '<thead><tr><th>#</th><th>Judul Penelitian</th><th>Tahun</th><th>Peran</th><th>Sumber Dana</th></tr></thead><tbody>';
    items.forEach(function(item, i){
      html += '<tr><td>'+(i+1)+'</td><td>'+esc(item.judul_penelitian||'-')+'</td><td>'+esc(item.tahun||'-')+'</td><td>'+esc(item.peran||'-')+'</td><td>'+esc(item.sumber_dana||'-')+'</td></tr>';
    });
  } else {
    html += '<thead><tr><th>#</th><th>Judul Pengabdian</th><th>Tahun</th><th>Peran</th><th>Mitra</th></tr></thead><tbody>';
    items.forEach(function(item, i){
      html += '<tr><td>'+(i+1)+'</td><td>'+esc(item.judul_pengabdian||'-')+'</td><td>'+esc(item.tahun||'-')+'</td><td>'+esc(item.peran||'-')+'</td><td>'+esc(item.mitra||'-')+'</td></tr>';
    });
  }
  html += '</tbody></table></div>';
  el.innerHTML = html;
}

function syncPribadi(){
  var icon = document.getElementById('pp-sync-icon');
  icon.className = 'fa fa-spinner fa-spin';
  $.ajax({
    url: '<?= site_url("C_sister/ajax_sync_pribadi") ?>',
    type: 'POST',
    data: { <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp){
      icon.className = 'fa fa-refresh';
      if(resp.status === 'success'){ loadPPProfil(); }
      else { alert(resp.message || 'Gagal sinkronisasi.'); }
    },
    error: function(){ icon.className = 'fa fa-refresh'; alert('Terjadi kesalahan.'); }
  });
}

document.addEventListener('DOMContentLoaded', function(){ loadPPProfil(); });

function loadBKD(){
  document.getElementById('bkd-container').innerHTML = '<div class="bkd-loading"><i class="fa fa-spinner fa-spin" style="font-size:22px;"></i><br><br>Memuat data BKD dari SISTER...</div>';
  $.ajax({
    url: '<?= site_url("C_sister/ajax_bkd_pribadi") ?>',
    type: 'POST',
    data: { <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>' },
    dataType: 'json',
    success: function(resp){
      bkdLoaded = true;
      if(resp.status === 'success'){
        bkdData = resp;
        renderBKD(resp);
      } else {
        document.getElementById('bkd-container').innerHTML = '<div class="bkd-loading" style="color:#FC8181;"><i class="fa fa-exclamation-circle"></i><br><br>'+esc(resp.message||'Gagal memuat BKD.')+'</div>';
      }
    },
    error: function(){
      bkdLoaded = false;
      document.getElementById('bkd-container').innerHTML = '<div class="bkd-loading" style="color:#FC8181;"><i class="fa fa-exclamation-circle"></i><br><br>Terjadi kesalahan saat memuat BKD.</div>';
    }
  });
}

function renderBKD(resp){
  var laporan = resp.laporan || [];
  var kegiatan_by_smt = resp.kegiatan_by_smt || {};
  var html    = '';

  if(!laporan.length){ 
    document.getElementById('bkd-container').innerHTML = '<div class="bkd-loading" style="color:#A0AEC0; font-size:15px; font-weight:700;"><i class="fa fa-info-circle" style="font-size:28px; margin-bottom:10px; color:#CBD5E0;"></i><br>Belum ada data BKD yang tersinkronisasi.</div>'; 
    return; 
  }

  // --- Card Grafik Performa BKD (Tren SKS Akhir) ---
  html += '<div style="background:#FFFFFF; border:1px solid #E2E8F0; border-radius:12px; padding:18px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.02);">';
  html += '  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #EDF2F7;">';
  html += '    <div>';
  html += '      <div style="font-size:13px; font-weight:800; color:#1A365D; text-transform:uppercase; letter-spacing:0.6px;">';
  html += '        <i class="fa fa-line-chart" style="color:#3182CE; margin-right:8px; font-size:15px;"></i>GRAFIK TREN PERFORMA BKD (SKS TOTAL)';
  html += '      </div>';
  html += '      <div style="font-size:12px; color:#718096; font-weight:600; margin-top:2px;">';
  html += '        Riwayat capaian total beban SKS per semester (Standar Beban: 12 - 16 SKS)';
  html += '      </div>';
  html += '    </div>';
  html += '    <div style="display:flex; gap:14px; align-items:center; font-size:11.5px; font-weight:700;">';
  html += '      <span style="display:inline-flex; align-items:center; gap:5px; color:#276749;"><span style="width:10px; height:10px; border-radius:2px; background:#38A169; display:inline-block;"></span> Memenuhi (&ge;12 SKS)</span>';
  html += '      <span style="display:inline-flex; align-items:center; gap:5px; color:#9B2C2C;"><span style="width:10px; height:10px; border-radius:2px; background:#E53E3E; display:inline-block;"></span> Belum Memenuhi (&lt;12 SKS)</span>';
  html += '    </div>';
  html += '  </div>';
  html += '  <div style="position:relative; height:200px; width:100%;">';
  html += '    <canvas id="bkdPerformanceChart"></canvas>';
  html += '  </div>';
  html += '</div>';

  // --- Selector semester Card ---
  html += '<div style="background:#F7FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:18px 22px; margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">';
  html += '  <div>';
  html += '    <div style="font-size:12px; font-weight:800; color:#4A5568; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:4px;"><i class="fa fa-calendar" style="color:#3182CE; margin-right:6px; font-size:14px;"></i>PILIH TAHUN AJARAN / SEMESTER</div>';
  html += '    <div style="font-size:13.5px; color:#718096; font-weight:600;">Pilih semester untuk melihat rincian pemenuhan beban kerja &amp; rincian kegiatan dosen</div>';
  html += '  </div>';
  html += '  <div style="min-width:280px;">';
  html += '    <select class="form-control" style="width:100%; font-size:14.5px; font-weight:800; color:#1A365D; border-radius:8px; border:2px solid #CBD5E0; padding:6px 14px; height:44px; background:#fff; cursor:pointer;" onchange="showBKDSmt(this.value)" id="bkdSmtSelector">';
  laporan.forEach(function(l, i){
    html += '      <option value="'+i+'">'+esc(l.label_smt)+' ('+esc(l.stat_kewajiban)+')</option>';
  });
  html += '    </select>';
  html += '  </div>';
  html += '</div>';

  // --- Container Detail Kegiatan & KPI per Semester ---
  html += '<div id="bkd-detail-wrap">';
  var initRecap = laporan[0];
  var initDetail = (kegiatan_by_smt[initRecap.id_smt]) ? kegiatan_by_smt[initRecap.id_smt] : { ajar:[], penelitian:[], pengmas:[], tunjang:[], pendidikan:[] };
  html += renderBKDDetail(initDetail, initRecap);
  html += '</div>';

  document.getElementById('bkd-container').innerHTML = html;

  // Inisialisasi Chart
  setTimeout(function(){
    initBKDChart(laporan);
  }, 50);
}

function renderBKDDetail(detail, recap){
  var h = '';
  var sks = parseFloat(recap.sks_total || 0);
  var pct = Math.min((sks / 16) * 100, 100).toFixed(0);
  var isMemenuhi = (recap.stat_kewajiban === 'Memenuhi' || sks >= 12);
  var color = isMemenuhi ? '#38A169' : (sks >= 6 ? '#D69E2E' : '#E53E3E');
  var statusCls = isMemenuhi ? 'hijau' : (sks >= 6 ? 'abu' : 'merah');

  h += '<div style="font-size:14.5px;font-weight:800;color:#1A365D;text-transform:uppercase;letter-spacing:0.6px;padding-bottom:10px;border-bottom:2px solid #EBF8FF;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">';
  h += '  <span><i class="fa fa-bullseye" style="color:#3182CE;margin-right:8px;font-size:16px;"></i>Rekapitulasi Kinerja — '+esc(recap.label_smt)+'</span>';
  h += '  <span style="font-size:13px;font-weight:700;color:#718096;text-transform:none;">Status: <strong style="color:'+color+';font-size:14px;">'+esc(recap.stat_kewajiban)+'</strong> (Asesor: <strong style="color:#2D3748;">'+esc(recap.simpulan_asesor||'-')+'</strong>)</span>';
  h += '</div>';

  // Status Box Utama
  h += '<div class="bkd-status-card '+statusCls+'" style="padding:12px 18px; margin-bottom:16px;">';
  h += '  <div class="bkd-dot '+statusCls+'" style="width:14px; height:14px;"></div>';
  h += '  <div style="flex:1;">';
  h += '    <div style="font-weight:800;font-size:15px;color:#2D3748;">'+sks.toFixed(2)+' SKS <span style="font-size:12px;font-weight:600;color:#718096;">/ Standar Beban (12 - 16 SKS)</span></div>';
  h += '    <div class="bkd-progress-bar-wrap" style="height:8px; margin-top:5px;"><div class="bkd-progress-bar" style="width:'+pct+'%;background:'+color+';"></div></div>';
  h += '  </div>';
  h += '  <div style="text-align:right;padding-left:14px;">';
  h += '    <div style="font-weight:800;font-size:14px;color:'+color+';"><i class="fa '+(isMemenuhi?'fa-check-circle':'fa-exclamation-circle')+'"></i> '+esc(recap.stat_kewajiban)+'</div>';
  h += '    <div style="font-size:11.5px;color:#718096;font-weight:600;margin-top:1px;">Simpulan Asesor: <strong style="color:#2D3748;">'+esc(recap.simpulan_asesor||'-')+'</strong></div>';
  h += '  </div>';
  h += '</div>';

  // Per-unsur KPI Cards
  var unsur = [
    {label:'PENGAJARAN (AJAR)', val: parseFloat(recap.sks_ajar||0), count: (detail.ajar||[]).length, color:'#3182CE', icon:'fa-book', bg:'#EBF8FF'},
    {label:'PENELITIAN (LITMAS)', val: parseFloat(recap.sks_lit||0), count: (detail.penelitian||[]).length, color:'#E53E3E', icon:'fa-flask', bg:'#FFF5F5'},
    {label:'PENGABDIAN (PENGMAS)', val: parseFloat(recap.sks_pengmas||0), count: (detail.pengmas||[]).length, color:'#38A169', icon:'fa-users', bg:'#F0FFF4'},
    {label:'PENUNJANG',  val: parseFloat(recap.sks_penunjang||0), count: (detail.tunjang||[]).length, color:'#D69E2E', icon:'fa-star', bg:'#FFFFF0'},
  ];
  if(parseFloat(recap.sks_didik||0) > 0 || (detail.pendidikan||[]).length > 0){
    unsur.push({label:'PENDIDIKAN', val: parseFloat(recap.sks_didik||0), count: (detail.pendidikan||[]).length, color:'#805AD5', icon:'fa-graduation-cap', bg:'#FAF5FF'});
  }

  h += '<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px;">';
  unsur.forEach(function(u){
    var up = Math.min((u.val/16)*100, 100).toFixed(0);
    h += '<div style="flex:1;min-width:130px;background:'+u.bg+';border-radius:10px;padding:10px 14px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 4px rgba(0,0,0,0.02);">';
    h += '  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;gap:6px;">';
    h += '    <span style="font-size:10.5px;font-weight:800;color:'+u.color+';letter-spacing:0.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fa '+u.icon+'"></i> '+u.label+'</span>';
    h += '    <span style="font-size:10.5px;font-weight:700;color:#718096;white-space:nowrap;flex-shrink:0;">'+u.count+' Keg.</span>';
    h += '  </div>';
    h += '  <div style="font-size:18px;font-weight:800;color:#2D3748;margin-bottom:4px;">'+u.val.toFixed(2)+' <span style="font-size:11.5px;font-weight:700;color:#718096;">SKS</span></div>';
    h += '  <div class="bkd-progress-bar-wrap" style="height:6px;margin-top:2px;"><div class="bkd-progress-bar" style="width:'+up+'%;background:'+u.color+';"></div></div>';
    h += '</div>';
  });
  h += '</div>';

  // Rincian kegiatan per unsur
  var sections = [
    {title:'1. Pelaksanaan Perkuliahan & Pengajaran (Ajar)', icon:'fa-book', data: detail.ajar||[], color:'#3182CE'},
    {title:'2. Pelaksanaan Penelitian & Publikasi (Litmas)', icon:'fa-flask', data: detail.penelitian||[], color:'#E53E3E'},
    {title:'3. Pelaksanaan Pengabdian Kepada Masyarakat (Pengmas)', icon:'fa-users', data: detail.pengmas||[], color:'#38A169'},
    {title:'4. Pelaksanaan Penunjang Tridharma (Tunjang)', icon:'fa-star', data: detail.tunjang||[], color:'#D69E2E'},
    {title:'5. Pendidikan & Pengembangan Diri', icon:'fa-graduation-cap', data: detail.pendidikan||[], color:'#805AD5'},
  ];

  sections.forEach(function(s){
    h += '<div style="margin-bottom:24px;">';
    h += '  <div style="font-size:14px;font-weight:800;color:#2D3748;text-transform:uppercase;letter-spacing:0.5px;padding:6px 0 10px;border-bottom:2px solid #EBF8FF;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">';
    h += '    <span><i class="fa '+s.icon+'" style="color:'+s.color+';margin-right:8px;font-size:15px;"></i>'+s.title+'</span>';
    h += '    <span style="font-size:12.5px;font-weight:800;color:'+(s.data.length ? s.color : '#CBD5E0')+';">'+s.data.length+' ITEM</span>';
    h += '  </div>';
    
    if(!s.data.length){
      h += '  <div style="font-size:13.5px;color:#A0AEC0;font-weight:600;padding:14px 18px;background:#F7FAFC;border-radius:8px;border:1px dashed #E2E8F0;text-align:center;"><i class="fa fa-info-circle" style="margin-right:6px;"></i>Tidak ada kegiatan tercatat pada unsur ini di semester '+esc(recap.label_smt)+'.</div>';
    } else {
      h += '  <div class="pp-table-wrap" style="max-height:none; overflow:visible;"><table class="pp-table"><thead><tr><th width="45">#</th><th>Nama Kegiatan</th><th>Kategori Kegiatan</th><th width="120" style="text-align:right;">Beban SKS</th><th width="90" style="text-align:right;">Nilai</th></tr></thead><tbody>';
      s.data.forEach(function(item, i){
        h += '    <tr>'
           + '      <td style="color:#718096;font-weight:700;">'+(i+1)+'</td>'
           + '      <td style="font-weight:700;color:#1A365D;font-size:14px;line-height:1.5;">'+esc(item.judul_keg||'-')+'</td>'
           + '      <td style="font-size:13px;color:#4A5568;line-height:1.45;">'+esc(item.nm_kat||'-')+'</td>'
           + '      <td style="font-weight:800;color:#2D3748;text-align:right;font-size:14px;">'+parseFloat(item.beban_sks||0).toFixed(2)+'</td>'
           + '      <td style="text-align:right;color:#718096;font-weight:700;font-size:13.5px;">'+parseFloat(item.nilai||0).toFixed(2)+'</td>'
           + '    </tr>';
      });
      h += '  </tbody></table></div>';
    }
    h += '</div>';
  });

  return h;
}

function showBKDSmt(idx){
  if(bkdData && bkdData.laporan){
    var laporan = bkdData.laporan;
    var kegiatan_by_smt = bkdData.kegiatan_by_smt || {};
    var recap = laporan[idx];
    if(recap){
      var detail = (kegiatan_by_smt[recap.id_smt]) ? kegiatan_by_smt[recap.id_smt] : { ajar:[], penelitian:[], pengmas:[], tunjang:[], pendidikan:[] };
      var wrap = document.getElementById('bkd-detail-wrap');
      if(wrap){ 
        wrap.innerHTML = renderBKDDetail(detail, recap); 
      }
    }
  }
}

var bkdChartInstance = null;

function initBKDChart(laporan){
  if (!window.Chart || !laporan || !laporan.length) return;
  var canvas = document.getElementById('bkdPerformanceChart');
  if (!canvas) return;

  if (bkdChartInstance) {
    bkdChartInstance.destroy();
    bkdChartInstance = null;
  }

  // Ambil maksimal 10 semester (5 tahun terakhir: 1 tahun = 2 semester)
  var last5Years = laporan.slice(0, 10);
  // Urutkan kronologis: dari semester terlama ke semester terbaru (kiri -> kanan)
  var chronoLaporan = last5Years.slice().reverse();

  // Format label sumbu X: kode semester seperti '20211', '20212', dst.
  var labels = chronoLaporan.map(function(l){ 
    return l.id_smt ? String(l.id_smt) : l.label_smt; 
  });
  var dataSks = chronoLaporan.map(function(l){ 
    return parseFloat(l.sks_total || 0); 
  });
  var bgColors = chronoLaporan.map(function(l){
    var s = parseFloat(l.sks_total || 0);
    return (l.stat_kewajiban === 'Memenuhi' || s >= 12) ? 'rgba(56, 161, 105, 0.85)' : 'rgba(229, 62, 62, 0.85)';
  });
  var borderColors = chronoLaporan.map(function(l){
    var s = parseFloat(l.sks_total || 0);
    return (l.stat_kewajiban === 'Memenuhi' || s >= 12) ? '#276749' : '#9B2C2C';
  });

  bkdChartInstance = new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Total SKS Akhir',
          data: dataSks,
          backgroundColor: bgColors,
          borderColor: borderColors,
          borderWidth: 1.5,
          borderRadius: 6,
          borderSkipped: false,
          barPercentage: 0.45
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(26, 54, 93, 0.95)',
          titleFont: { size: 12.5, weight: 'bold', family: 'Nunito' },
          bodyFont: { size: 11.5, family: 'Nunito' },
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            title: function(items){
              var idx = items[0].dataIndex;
              var lap = chronoLaporan[idx];
              return lap.label_smt + ' (' + (lap.id_smt || '-') + ')';
            },
            label: function(item){
              var idx = item.dataIndex;
              var lap = chronoLaporan[idx];
              return [
                ' Total Beban SKS: ' + parseFloat(lap.sks_total||0).toFixed(2) + ' SKS',
                ' Status Kewajiban: ' + lap.stat_kewajiban + ' (Asesor: ' + (lap.simpulan_asesor||'-') + ')'
              ];
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          suggestedMax: 20,
          grid: {
            color: 'rgba(226, 232, 240, 0.8)',
            borderDash: [4, 4]
          },
          ticks: {
            font: { size: 11, weight: '700', family: 'Nunito' },
            color: '#718096',
            callback: function(v){ return v + ' SKS'; }
          }
        },
        x: {
          grid: { display: false },
          ticks: {
            font: { size: 11.5, weight: '800', family: 'Nunito' },
            color: '#2D3748'
          }
        }
      },
      onClick: function(e, elements){
        if (elements && elements.length > 0) {
          var clickedChronoIdx = elements[0].index;
          var clickedItem = chronoLaporan[clickedChronoIdx];
          var originalIdx = laporan.findIndex(function(l){ return l.id_smt === clickedItem.id_smt; });
          if (originalIdx !== -1) {
            var sel = document.getElementById('bkdSmtSelector');
            if (sel) {
              sel.value = originalIdx;
              showBKDSmt(originalIdx);
            }
          }
        }
      }
    }
  });
}

</script>
