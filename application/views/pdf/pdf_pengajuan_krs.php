<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 1. Persiapan Data Mahasiswa & Prodi
$nama_prodi = !empty($nm_lemb) ? trim($nm_lemb) : '';
$clean_prodi = trim(preg_replace('/\s+D4$/i', '', $nama_prodi));
$jenjang = (stripos($nama_prodi, 'D4') === FALSE) ? ' D4' : '';
$nama_prodi_id = $clean_prodi . $jenjang;

// 2. Persiapan Semester & Tahun Akademik (Hanya Bahasa Indonesia)
$smt_raw = !empty($nm_semester) ? $nm_semester : '';
$id_smt_raw = !empty($data_rencana_studi->id_smt) ? (string)$data_rencana_studi->id_smt : '';

$smt_type = '';
if (stripos($smt_raw, 'Genap') !== FALSE || (strlen($id_smt_raw) >= 5 && substr($id_smt_raw, 4, 1) == '2')) {
    $smt_type = 'Genap';
} elseif (stripos($smt_raw, 'Ganjil') !== FALSE || (strlen($id_smt_raw) >= 5 && substr($id_smt_raw, 4, 1) == '1')) {
    $smt_type = 'Ganjil';
} elseif (stripos($smt_raw, 'Pendek') !== FALSE || stripos($smt_raw, 'Antara') !== FALSE || (strlen($id_smt_raw) >= 5 && substr($id_smt_raw, 4, 1) == '3')) {
    $smt_type = 'Antara';
} else {
    $smt_type = !empty($smt_raw) ? $smt_raw : '-';
}

$thn_akademik = '';
if (preg_match('/(\d{4}\/\d{4})/', $smt_raw, $m_thn)) {
    $thn_akademik = $m_thn[1];
} elseif (strlen($id_smt_raw) >= 4) {
    $thn_start = (int)substr($id_smt_raw, 0, 4);
    $thn_akademik = $thn_start . '/' . ($thn_start + 1);
} else {
    $thn_akademik = '-';
}

// 3. Persiapan Dosen Wali & NIDN
$nama_dosen = !empty($nm_sdm) ? $nm_sdm : '';
$nidn_dosen = isset($nidn_sdm) ? $nidn_sdm : '';
if (empty($nama_dosen) || empty($nidn_dosen)) {
    if (!empty($data_mahasiswa->dosen_pa)) {
        $dsn_query = $this->db->where('id_ptk', $data_mahasiswa->dosen_pa)->get('siakad_dosen')->row();
        if ($dsn_query) {
            if (empty($nama_dosen)) $nama_dosen = !empty($dsn_query->nm_ptk) ? $dsn_query->nm_ptk : '';
            if (empty($nidn_dosen)) $nidn_dosen = !empty($dsn_query->nidn) ? $dsn_query->nidn : (!empty($dsn_query->nip) ? $dsn_query->nip : '-');
        }
    }
}
if (empty($nidn_dosen)) $nidn_dosen = '-';

// 4. Persiapan Logo WBI (Mengacu pada controller C_transkrip_sementara / cetak_academic_record)
$fcpath_dir = defined('FCPATH') ? FCPATH : dirname(dirname(dirname(__DIR__))) . '/';
$logo_path = $fcpath_dir . 'assets/logo.png';
if (!file_exists($logo_path)) {
    $logo_path = $fcpath_dir . 'assets/image_web/kop_wbi.png';
}

if (file_exists($logo_path)) {
    $logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
} elseif (function_exists('base_url')) {
    $logo_src = base_url('assets/logo.png');
} else {
    $logo_src = '/assets/logo.png';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KRS - <?= htmlspecialchars($data_mahasiswa->nipd); ?> - <?= htmlspecialchars($data_mahasiswa->nm_pd); ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style type="text/css">
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    body {
      background-color: #525659;
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      padding: 0;
      color: #000;
      line-height: 1.35;
    }

    /* Top Action Bar (Layar Saja) */
    .top-action-bar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #1e293b;
      color: #fff;
      border-bottom: 1px solid #334155;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      padding: 10px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .top-action-bar .bar-title {
      font-size: 14px;
      font-weight: 600;
      color: #f8fafc;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .top-action-bar .bar-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-left: auto;
    }

    .btn-action-print {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 6px 16px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.2s;
    }

    .btn-action-print:hover {
      background: #1d4ed8;
    }

    .btn-action-close {
      background: #475569;
      color: #fff;
      border: none;
      padding: 6px 14px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.2s;
    }

    .btn-action-close:hover {
      background: #334155;
    }

    /* Page Sheet Container (Simulasi Kertas A4 - Mengacu C_transkrip_sementara) */
    .page-sheet {
      background: #ffffff;
      width: 210mm;
      min-height: 297mm;
      margin: 20px auto 40px auto;
      padding: 10mm 14mm 14mm 18mm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
      box-sizing: border-box;
      position: relative;
    }

    /* Kop Surat (Persis Style cetak_academic_record) */
    .kop-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2px;
    }

    .kop-logo {
      width: 95px;
      vertical-align: middle;
      text-align: center;
      padding-right: 12px;
    }

    .kop-logo img {
      width: 85px;
      max-height: 75px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }

    .kop-text {
      vertical-align: middle;
      text-align: center;
    }

    .kop-title {
      font-size: 16.5px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #000;
      line-height: 1.2;
    }

    .kop-prodi {
      font-size: 14.5px;
      font-weight: bold;
      text-transform: uppercase;
      color: #000;
      margin-top: 3px;
      line-height: 1.2;
    }

    .kop-address {
      font-size: 10px;
      color: #222;
      margin-top: 3px;
      line-height: 1.25;
    }

    .kop-divider {
      border-top: 2.5px solid #000;
      border-bottom: 1px solid #000;
      height: 3.5px;
      margin: 4px 0 12px 0;
    }

    /* Table Informasi Mahasiswa */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 18px;
      font-size: 12.5px;
    }

    .info-table td {
      padding: 3px 0;
      vertical-align: top;
      border: none !important;
    }

    .info-table .lbl {
      width: 210px;
      color: #000;
    }

    .info-table .sep {
      width: 16px;
      text-align: center;
    }

    .info-table .val {
      color: #000;
    }

    /* Tabel KRS Mata Kuliah */
    .krs-table {
      width: 100%;
      border-collapse: collapse;
      border: 1.5px solid #000;
      font-size: 12px;
      margin-top: 6px;
    }

    .krs-table th,
    .krs-table td {
      border: 1px solid #000;
      padding: 6px 7px;
      line-height: 1.35;
    }

    .krs-table th {
      background-color: #fff;
      text-align: center;
      vertical-align: middle;
      font-weight: 700;
    }

    .krs-table td.text-center {
      text-align: center;
    }

    .krs-table td.text-left {
      text-align: left;
    }

    .krs-table .total-row td {
      font-weight: 700;
    }

    /* Area Tanda Tangan */
    .signature-table {
      width: 100%;
      margin-top: 40px;
      border-collapse: collapse;
      border: none;
      page-break-inside: avoid;
    }

    .signature-table td {
      border: none !important;
      text-align: center;
      vertical-align: top;
      width: 50%;
      padding: 0 10px;
    }

    .sig-title {
      font-size: 12.5px;
      font-weight: 700;
    }

    .sig-space {
      height: 70px;
    }

    .sig-name {
      font-size: 12.5px;
      font-weight: 600;
      margin-bottom: 2px;
    }

    .sig-id {
      font-size: 11.5px;
    }

    /* Aturan Khusus Cetak (@media print) */
    @media print {
      @page {
        size: A4 portrait;
        margin: 12mm 14mm 14mm 18mm;
      }

      body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      .no-print {
        display: none !important;
      }

      .page-sheet {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
      }

      .kop-divider {
        border-top: 2.5px solid #000 !important;
        border-bottom: 1px solid #000 !important;
      }

      .krs-table {
        border: 1.5px solid #000 !important;
      }

      .krs-table th,
      .krs-table td {
        border: 1px solid #000 !important;
      }

      .signature-table {
        page-break-inside: avoid !important;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Bar Khusus Tampilan Layar (Tidak Tercetak) -->
  <div class="top-action-bar no-print">
    <div class="bar-title">
      <i class="fa fa-file-text-o"></i> <strong>Kartu Rencana Studi (KRS)</strong> &mdash; <?= htmlspecialchars($data_mahasiswa->nipd); ?> (<?= htmlspecialchars(strtoupper((string)$data_mahasiswa->nm_pd)); ?>)
    </div>
    <div class="bar-actions">
      <button type="button" onclick="window.close()" class="btn-action-close">
        <i class="fa fa-times"></i> Tutup
      </button>
      <button type="button" onclick="window.print()" class="btn-action-print">
        <i class="fa fa-print"></i> Cetak Dokumen
      </button>
    </div>
  </div>

  <!-- Page Sheet Container -->
  <div class="page-sheet">

    <!-- Kop Surat Sesuai C_transkrip_sementara (cetak_academic_record) -->
    <table class="kop-table">
      <tr>
        <td class="kop-logo">
          <img src="<?= $logo_src; ?>" alt="Logo WBI">
        </td>
        <td class="kop-text">
          <div class="kop-title">POLITEKNIK WILMAR BISNIS INDONESIA</div>
          <div class="kop-prodi">KARTU RENCANA STUDI</div>
          <div class="kop-address">
            Jl. Kapten Batu Sihombing Medan Estate, Percut Sei Tuan, Deli Serdang - Sumatera Utara 20371<br>
            Telp. +62 61 4207 4111 | Website: https://wbi.ac.id
          </div>
        </td>
      </tr>
    </table>

    <div class="kop-divider"></div>

    <!-- Informasi Mahasiswa -->
    <table class="info-table">
      <tbody>
        <tr>
          <td class="lbl">Nama Mahasiswa</td>
          <td class="sep">:</td>
          <td class="val"><?= strtoupper(htmlspecialchars($data_mahasiswa->nm_pd)); ?></td>
        </tr>
        <tr>
          <td class="lbl">Nomor Induk Mahasiswa</td>
          <td class="sep">:</td>
          <td class="val"><?= htmlspecialchars($data_mahasiswa->nipd); ?></td>
        </tr>
        <tr>
          <td class="lbl">Program Studi</td>
          <td class="sep">:</td>
          <td class="val"><?= htmlspecialchars($nama_prodi_id); ?></td>
        </tr>
        <tr>
          <td class="lbl">Semester</td>
          <td class="sep">:</td>
          <td class="val"><?= htmlspecialchars($smt_type); ?></td>
        </tr>
        <tr>
          <td class="lbl">Tahun Akademik</td>
          <td class="sep">:</td>
          <td class="val"><?= htmlspecialchars($thn_akademik); ?></td>
        </tr>
      </tbody>
    </table>

    <!-- Tabel Mata Kuliah -->
    <table class="krs-table">
      <thead>
        <tr>
          <th style="width: 38px;">No</th>
          <th style="width: 105px;">Kode</th>
          <th>Mata Kuliah</th>
          <th style="width: 75px;">SKS</th>
          <th style="width: 55px;">T</th>
          <th style="width: 55px;">P</th>
          <th style="width: 55px;">PL</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $total_sks = 0;
        $total_tm = 0;
        $total_prak = 0;
        $total_prak_lap = 0;

        if (!empty($data_rencana_studi_detail)) {
            foreach ($data_rencana_studi_detail as $item) {
                $sks = (float)$item->sks_mk;
                $tm = (float)$item->sks_tm;
                $prak = (float)$item->sks_prak;
                $prak_lap = (float)$item->sks_prak_lap;

                $total_sks += $sks;
                $total_tm += $tm;
                $total_prak += $prak;
                $total_prak_lap += $prak_lap;
        ?>
          <tr>
            <td class="text-center"><?= $no++; ?>.</td>
            <td class="text-center"><?= htmlspecialchars($item->kode_mk); ?></td>
            <td class="text-left"><?= htmlspecialchars($item->nm_mk); ?></td>
            <td class="text-center"><?= (int)$sks; ?></td>
            <td class="text-center"><?= number_format($tm, 2); ?></td>
            <td class="text-center"><?= number_format($prak, 2); ?></td>
            <td class="text-center"><?= number_format($prak_lap, 2); ?></td>
          </tr>
        <?php
            }
        } else {
        ?>
          <tr>
            <td colspan="7" class="text-center" style="padding: 16px; color: #666; font-style: italic;">
              Belum ada mata kuliah yang diambil dalam rencana studi ini.
            </td>
          </tr>
        <?php } ?>
        <tr class="total-row">
          <td colspan="3" class="text-center"><b>Jumlah SKS</b></td>
          <td class="text-center"><b><?= (int)$total_sks; ?></b></td>
          <td class="text-center"><b><?= (int)$total_tm; ?></b></td>
          <td class="text-center"><b><?= (int)$total_prak; ?></b></td>
          <td class="text-center"><b><?= (int)$total_prak_lap; ?></b></td>
        </tr>
      </tbody>
    </table>

    <?php
    $is_approved = (
        (isset($data_rencana_studi->id_status_krs) && $data_rencana_studi->id_status_krs == 3) ||
        !empty($data_rencana_studi->approved_date)
    );
    $tgl_approval = !empty($data_rencana_studi->approved_date) ? $data_rencana_studi->approved_date : (!empty($data_rencana_studi->change_date) ? $data_rencana_studi->change_date : '');

    if ($is_approved && !empty($tgl_approval)):
        $hari_indo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $bulan_indo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $ts_app = strtotime($tgl_approval);
        $hari_app = isset($hari_indo[date('l', $ts_app)]) ? $hari_indo[date('l', $ts_app)] : date('l', $ts_app);
        $tgl_app = date('j', $ts_app);
        $bln_app = isset($bulan_indo[(int)date('n', $ts_app)]) ? $bulan_indo[(int)date('n', $ts_app)] : date('F', $ts_app);
        $thn_app = date('Y', $ts_app);
        $jam_app = date('H:i', $ts_app);
        $approved_text = "$hari_app, $tgl_app $bln_app $thn_app pukul $jam_app WIB";
    ?>
      <div style="margin-top: 8px; font-size: 12px; color: #1e293b; font-style: italic;">
        Disetujui oleh Dosen Wali pada : <?= $approved_text; ?>
      </div>
    <?php endif; ?>

    <!-- Tanda Tangan Sesuai Format Resmi -->
    <table class="signature-table">
      <tbody>
        <tr>
          <td>
            <div class="sig-title">Dosen Wali</div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= htmlspecialchars($nama_dosen); ?></div>
            <div class="sig-id">NIDN : <?= htmlspecialchars($nidn_dosen); ?></div>
          </td>
          <td>
            <div class="sig-title">Mahasiswa</div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= strtoupper(htmlspecialchars($data_mahasiswa->nm_pd)); ?></div>
            <div class="sig-id">NIM : <?= htmlspecialchars($data_mahasiswa->nipd); ?></div>
          </td>
        </tr>
      </tbody>
    </table>

  </div>

</body>
</html>