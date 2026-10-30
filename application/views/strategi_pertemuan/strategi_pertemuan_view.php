<?php
/**
 * View: strategi_pertemuan_view.php
 * Halaman Utama Manajemen Strategi Pertemuan Perkuliahan & Draft Evaluasi Pertemuan
 * Standard Elite Admin Layout (Clean, Modern, Datatables + Document Viewer)
 */

if (!function_exists('render_scl_scenario_cockpit_html')) {
    function render_scl_scenario_cockpit_html($skenario_raw) {
        if (empty($skenario_raw)) {
            return '<div class="text-muted font-italic small p-2 bg-light rounded"><i class="fa fa-info-circle mr-1 text-info"></i> Skenario langkah pembelajaran SCL belum dirinci.</div>';
        }

        $clean = trim($skenario_raw);
        $clean = preg_replace('/<br\s*[\/]?>/gi', "\n", $clean);
        $lines = preg_split('/\r\n|\r|\n/', $clean);
        $html = '<div class="scl-cockpit-content" style="font-size: 13px; line-height: 1.65; color: #1e293b;">';
        
        $in_list = false;
        foreach ($lines as $line) {
            $tline = trim($line);
            if (empty($tline)) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                continue;
            }

            // Check if stage title (e.g. 1. **Tahap Pendahuluan (15 Menit):** or ### Tahap ...)
            if (preg_match('/^(?:(?:\d+\.|\*|-)?\s*)?\*\*(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)\*\*(?:\s*:\s*)?(.*)$/i', $tline, $sm) ||
                preg_match('/^(\d+\.\s+)(?:\*\*)?(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)(?:\*\*)?(?:\s*:\s*)?(.*)$/i', $tline, $sm)) {
                if ($in_list) {
                    $html .= '</ul>';
                    $in_list = false;
                }
                $stage_num = isset($sm[1]) && is_numeric(trim(str_replace('.', '', $sm[1]))) ? trim($sm[1]) . ' ' : '';
                $stage_title = trim($sm[2] ?? $sm[1]);
                $stage_extra = trim($sm[3] ?? '');
                
                $html .= '<div style="margin-top: 10px; margin-bottom: 5px; font-weight: 700; color: #0f172a; font-size: 13px; display: flex; align-items: baseline;">'
                       . '<span style="color: #2563eb; margin-right: 6px; font-weight: 800;">' . $stage_num . '</span>'
                       . '<span>' . htmlspecialchars($stage_title) . '</span>'
                       . ($stage_extra ? ' <span style="font-weight: 500; color: #475569; margin-left: 4px;">' . htmlspecialchars($stage_extra) . '</span>' : '')
                       . '</div>';
                continue;
            }

            // Check if bullet point (- or * or •)
            if (preg_match('/^(?:[\-\*•]|\d+[\.\)])\s*(.*)$/u', $tline, $bm)) {
                if (!$in_list) {
                    $html .= '<ul style="margin: 0 0 8px 0; padding-left: 20px; list-style-type: disc;">';
                    $in_list = true;
                }
                $bullet_content = trim($bm[1]);
                $bullet_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($bullet_content));
                $html .= '<li style="margin-bottom: 5px; color: #334155; line-height: 1.55;">' . $bullet_content . '</li>';
                continue;
            }

            if ($in_list) {
                $html .= '</ul>';
                $in_list = false;
            }

            $line_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($tline));
            $html .= '<p style="margin-bottom: 6px; color: #334155;">' . $line_content . '</p>';
        }

        if ($in_list) {
            $html .= '</ul>';
        }

        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('render_bap_faktual_cockpit_html')) {
    function render_bap_faktual_cockpit_html($draf_bap) {
        if (empty($draf_bap) || $draf_bap === '-') {
            return '<div class="text-muted font-italic small p-2 bg-light rounded"><i class="fa fa-info-circle mr-1 text-muted"></i> Draft Evaluasi Pertemuan belum diisi.</div>';
        }

        $raw = trim($draf_bap);
        $raw = preg_replace('/<br\s*[\/]?>/i', "\n", $raw);

        $match_materi     = preg_match('/Realisasi Materi\s*:\s*([\s\S]*?)(?=(?:Aktivitas|Capaian|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i', $raw, $mm) ? trim($mm[1]) : '';
        $match_aktivitas  = preg_match('/Aktivitas\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Capaian|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i', $raw, $ma) ? trim($ma[1]) : '';
        $match_capaian    = preg_match('/Capaian\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Kendala|Tindak\s*lanjut|Kesesuaian RPS|$))/i', $raw, $mc) ? trim($mc[1]) : '';
        $match_kendala    = preg_match('/Kendala\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Capaian|Tindak\s*lanjut|Kesesuaian RPS|$))/i', $raw, $mk) ? trim($mk[1]) : '';
        $match_tindak     = preg_match('/Tindak\s*lanjut\s*:\s*([\s\S]*?)(?=(?:Realisasi Materi|Aktivitas|Capaian|Kendala|Kesesuaian RPS|$))/i', $raw, $mt) ? trim($mt[1]) : '';
        $match_kesesuaian = preg_match('/Kesesuaian RPS\s*:\s*([\s\S]*?)$/i', $raw, $ms) ? trim($ms[1]) : '';

        if (!$match_materi && !$match_aktivitas && !$match_capaian && !$match_kendala && !$match_tindak && !$match_kesesuaian) {
            return '<div style="font-size: 12.5px; line-height: 1.6; color: #1e293b;">' . nl2br(htmlspecialchars($raw)) . '</div>';
        }

        $ks_badge = '<span class="badge badge-success px-2 py-1" style="background:#dcfce7; color:#15803d; border:1px solid #86efac;"><i class="fa fa-check-circle mr-1"></i> Sesuai RPS</span>';
        if (stripos($match_kesesuaian, 'sebagian') !== false) {
            $ks_badge = '<span class="badge badge-warning px-2 py-1" style="background:#fef3c7; color:#78350f; border:1px solid #fde68a;"><i class="fa fa-adjust mr-1"></i> ' . htmlspecialchars($match_kesesuaian) . '</span>';
        } elseif (stripos($match_kesesuaian, 'tidak') !== false) {
            $ks_badge = '<span class="badge badge-danger px-2 py-1" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;"><i class="fa fa-times-circle mr-1"></i> ' . htmlspecialchars($match_kesesuaian) . '</span>';
        }

        $html = '<div style="font-size: 12.5px; line-height: 1.6; color: #1e293b; display: flex; flex-direction: column; gap: 8px;">';

        if ($match_materi) {
            $html .= '<div><strong style="color: #0f172a;"><i class="fa fa-book text-primary mr-1"></i> Realisasi Materi:</strong><br><span style="color: #334155;">' . nl2br(htmlspecialchars($match_materi)) . '</span></div>';
        }
        if ($match_aktivitas) {
            $html .= '<div><strong style="color: #0f172a;"><i class="fa fa-clock-o text-info mr-1"></i> Aktivitas (SCL):</strong><br><span style="color: #334155;">' . nl2br(htmlspecialchars($match_aktivitas)) . '</span></div>';
        }
        if ($match_capaian) {
            $html .= '<div><strong style="color: #0f172a;"><i class="fa fa-trophy text-success mr-1"></i> Capaian:</strong><br><span style="color: #334155;">' . nl2br(htmlspecialchars($match_capaian)) . '</span></div>';
        }
        if ($match_kendala) {
            $html .= '<div><strong style="color: #0f172a;"><i class="fa fa-exclamation-triangle text-danger mr-1"></i> Kendala:</strong><br><span style="color: #334155;">' . nl2br(htmlspecialchars($match_kendala)) . '</span></div>';
        }
        if ($match_tindak) {
            $html .= '<div><strong style="color: #0f172a;"><i class="fa fa-arrow-circle-right text-warning mr-1"></i> Tindak Lanjut:</strong><br><span style="color: #334155;">' . nl2br(htmlspecialchars($match_tindak)) . '</span></div>';
        }
        if ($match_kesesuaian) {
            $html .= '<div style="margin-top: 4px; display: flex; align-items: center; gap: 6px;"><strong style="color: #0f172a;">Kesesuaian RPS:</strong> ' . $ks_badge . '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}
?>
<style>
  .sp-card-metric {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    background: #ffffff;
  }
  .sp-card-metric:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }
  .sp-metric-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .nav-tabs-custom {
    border-bottom: 2px solid #e2e8f0;
  }
  .nav-tabs-custom .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #64748b;
    font-weight: 600;
    font-size: 14px;
    padding: 10px 18px;
    border-radius: 0;
    transition: all 0.2s ease;
  }
  .nav-tabs-custom .nav-link:hover {
    color: #1e40af;
    border-bottom-color: #cbd5e1;
  }
  .nav-tabs-custom .nav-link.active {
    color: #1e40af;
    border-bottom-color: #2563eb;
    background: transparent;
  }
  .sp-doc-preview-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
  }
  .sp-meet-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 18px;
    background: #ffffff;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  }
  .sp-meet-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37,99,235,0.08);
  }
  .sp-meet-header {
    background: #f8fafc;
    padding: 12px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .sp-cockpit-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 14px 16px;
    margin-top: 10px;
  }
</style>

<!-- Header & Hero Section -->
<div class="row page-titles mb-3">
  <div class="col-md-7 align-self-center">
    <div class="d-flex align-items-center">
      <div class="bg-primary text-white rounded p-3 mr-3 shadow-sm" style="font-size: 24px; width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-calendar-check-o"></i>
      </div>
      <div>
        <h4 class="text-dark font-weight-bold mb-1"><?= $judul_besar; ?></h4>
        <p class="text-muted mb-0 small">
          <i class="fa fa-clock-o mr-1 text-primary"></i> <?= $judul_kecil; ?>
          <?php if (!empty($creator)): ?>
            <span class="mx-2">•</span> <i class="fa fa-user mr-1 text-info"></i> <?= $creator; ?>
          <?php endif; ?>
          <?php if (!empty($nm_lemb)): ?>
            <span class="mx-2">•</span> <i class="fa fa-graduation-cap mr-1 text-success"></i> <?= $nm_lemb; ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-5 align-self-center text-right mt-2 mt-md-0">
    <div class="d-inline-flex flex-wrap align-items-center">
      <a href="<?= site_url('C_jadwal'); ?>" class="btn btn-outline-secondary btn-sm rounded shadow-sm mr-2 mb-1">
        <i class="fa fa-arrow-left mr-1"></i> Dashboard
      </a>
      <a href="<?= site_url('C_strategi_pertemuan/dokumen/' . $id_jadwal); ?>" target="_blank" class="btn btn-info btn-sm rounded shadow-sm mb-1 text-white">
        <i class="fa fa-print mr-1"></i> Cetak / Full Dokumen
      </a>
    </div>
  </div>
</div>

<!-- Metric & Summary Cards -->
<div class="row mb-3">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
    <div class="card sp-card-metric p-3 mb-0">
      <div class="d-flex align-items-center">
        <div class="sp-metric-icon bg-light-primary text-primary mr-3">
          <i class="fa fa-calendar"></i>
        </div>
        <div>
          <h6 class="text-muted mb-0 font-weight-normal small">Total Rencana</h6>
          <h4 class="font-weight-bold mb-0 text-dark"><?= $i_pertemuan ?: 16; ?> <small class="text-muted" style="font-size: 12px;">Pertemuan</small></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
    <div class="card sp-card-metric p-3 mb-0">
      <div class="d-flex align-items-center">
        <div class="sp-metric-icon bg-light-success text-success mr-3">
          <i class="fa fa-check-circle"></i>
        </div>
        <div>
          <h6 class="text-muted mb-0 font-weight-normal small">Evaluasi Pertemuan</h6>
          <h4 class="font-weight-bold mb-0 text-dark"><?= $i_realisasi; ?> / <?= $i_pertemuan ?: 16; ?> <small class="text-muted" style="font-size: 12px;">Selesai</small></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
    <div class="card sp-card-metric p-3 mb-0">
      <div class="d-flex align-items-center">
        <div class="sp-metric-icon bg-light-info text-info mr-3">
          <i class="fa fa-users"></i>
        </div>
        <div>
          <h6 class="text-muted mb-0 font-weight-normal small">Mahasiswa Kelas</h6>
          <h4 class="font-weight-bold mb-0 text-dark"><?= $jumlah_mahasiswa; ?> <small class="text-muted" style="font-size: 12px;">Orang</small></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
    <div class="card sp-card-metric p-3 mb-0">
      <div class="d-flex align-items-center">
        <div class="sp-metric-icon bg-light-warning text-warning mr-3">
          <i class="fa fa-certificate"></i>
        </div>
        <div>
          <h6 class="text-muted mb-0 font-weight-normal small">Status Dokumen</h6>
          <h5 class="font-weight-bold mb-0 text-dark" style="font-size: 14px;">
            <?php if (!empty($rps_obe) && !empty($rps_obe->id_rps)): ?>
              <span class="text-success"><i class="fa fa-check-circle"></i> RPS OBE Siap</span>
            <?php else: ?>
              <span class="text-info"><i class="fa fa-info-circle"></i> RPS Reguler</span>
            <?php endif; ?>
          </h5>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Navigation Tabs: Datatables vs Document Preview -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
  <div class="card-header bg-white pb-0 border-0 pt-3">
    <ul class="nav nav-tabs nav-tabs-custom" id="tabStrategiPertemuan" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="tab-tabel-pertemuan" data-toggle="tab" href="#content-tabel-pertemuan" role="tab">
          <i class="fa fa-list-alt mr-1"></i> Data Rencana & Realisasi 16 Pertemuan
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-view-dokumen" data-toggle="tab" href="#content-view-dokumen" role="tab">
          <i class="fa fa-book mr-1"></i> View Strategi Pertemuan & Draft Evaluasi Pertemuan
        </a>
      </li>
    </ul>
  </div>

  <div class="card-body p-4">
    <div class="tab-content">
      
      <!-- TAB 1: DATATABLES 16 PERTEMUAN -->
      <div class="tab-pane fade show active" id="content-tabel-pertemuan" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
          <div>
            <h5 class="font-weight-bold text-dark mb-1"><i class="fa fa-table text-primary mr-1"></i> Daftar 16 Rencana Pertemuan & Skenario SCL</h5>
            <p class="text-muted small mb-0">Klik <strong>Edit / Detail</strong> pada setiap pertemuan untuk melengkapi Sub-CPMK, Skenario SCL, Draft Evaluasi Pertemuan, dan konfirmasi realisasi.</p>
          </div>
          <div class="mt-2 mt-sm-0">
            <button class="btn btn-outline-primary btn-sm rounded shadow-sm" type="button" onclick="reload_table()">
              <i class="fa fa-refresh mr-1"></i> Refresh Tabel
            </button>
          </div>
        </div>

        <div class="table-responsive rounded">
          <table class="table table-bordered table-hover mb-0" id="datatable_ajax" style="width: 100%;">
            <thead class="bg-light">
              <tr role="row" class="heading text-dark font-weight-bold" style="font-size: 13px;">
                <th style="width: 170px;">Meet | Jadwal</th>
                <th>Skenario Strategi Pembelajaran (SCL), Sub-CPMK & Draft Evaluasi Pertemuan</th>
                <th style="width: 130px; text-align: center;">Status Realisasi</th>
                <th style="width: 90px; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody style="font-size: 13px;">
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 2: VIEW STRATEGI PERTEMUAN DOKUMEN & COCKPIT -->
      <div class="tab-pane fade" id="content-view-dokumen" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
          <div>
            <h5 class="font-weight-bold text-dark mb-1"><i class="fa fa-file-text-o text-primary mr-1"></i> Dokumen Rencana Strategi Pertemuan & Draft Evaluasi Pertemuan</h5>
            <p class="text-muted small mb-0">Rincian skenario pembelajaran 16 pertemuan terstruktur berbasis Student-Centered Learning (SCL) & SPMI WBI.</p>
          </div>
          <div class="mt-2 mt-sm-0">
            <a href="<?= site_url('C_strategi_pertemuan/dokumen/' . $id_jadwal); ?>" target="_blank" class="btn btn-primary btn-sm rounded shadow-sm text-white">
              <i class="fa fa-external-link mr-1"></i> Buka Fullscreen / Cetak
            </a>
          </div>
        </div>

        <?php if (!empty($meetings)): ?>
          <div class="row">
            <?php foreach ($meetings as $m_num => $m): ?>
              <?php
                $is_eval = ($m_num == 8) ? 'Evaluasi Tengah Semester (UTS)' : (($m_num == 16) ? 'Evaluasi Akhir Semester (UAS)' : '');
                $card_border = $is_eval ? 'border-warning bg-light-warning' : '';
                $badge_bg = $is_eval ? 'badge-warning' : 'badge-primary';
                $topik_judul = !empty($m['topik_pertemuan']) ? $m['topik_pertemuan'] : (!empty($m['bahan_kajian']) ? $m['bahan_kajian'] : (!empty($m['bacaan_materi']) ? $m['bacaan_materi'] : 'Pertemuan ' . $m_num));
                
                $raw_t = !empty($m['jam_teori']) ? (float)$m['jam_teori'] : 1;
                $raw_p = !empty($m['jam_praktek']) ? (float)$m['jam_praktek'] : 2;
                $menit_t = $raw_t <= 10 ? round($raw_t * 50) : ($raw_t > 300 ? 50 : round($raw_t));
                $menit_p = $raw_p <= 10 ? round($raw_p * 50) : ($raw_p > 600 ? 100 : round($raw_p));
              ?>
              <div class="col-lg-12 mb-4">
                <div class="sp-meet-card <?= $card_border; ?>">
                  
                  <!-- Card Header -->
                  <div class="sp-meet-header">
                    <div class="d-flex align-items-center flex-wrap">
                      <span class="badge <?= $badge_bg; ?> px-2 py-1 font-weight-bold mr-2" style="font-size: 12px;">
                        Pertemuan <?= $m_num; ?>
                      </span>
                      <h5 class="font-weight-bold text-dark mb-0 d-inline-block">
                        📌 PERTEMUAN KE-<?= $m_num; ?>: <?= htmlspecialchars($topik_judul); ?>
                      </h5>
                    </div>
                    <div class="mt-1 mt-sm-0">
                      <span class="badge badge-light border text-dark mr-2" style="font-size: 11px;">
                        <i class="fa fa-clock-o text-primary mr-1"></i> <?= $menit_t; ?> Menit Teori / <?= $menit_p; ?> Menit Praktik
                      </span>
                      <?php if (!empty($m['is_realisasi'])): ?>
                        <span class="badge badge-success px-2 py-1" style="font-size: 10.5px;"><i class="fa fa-check"></i> Terealisasi</span>
                      <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1" style="font-size: 10.5px;"><i class="fa fa-clock-o"></i> Terencana</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Card Body -->
                  <div class="p-4" style="background: #ffffff;">
                    
                    <!-- Sub-CPMK & Metode Tag -->
                    <div class="row mb-3">
                      <?php if (!empty($m['sub_cpmk']) && $m['sub_cpmk'] != '-'): ?>
                        <div class="col-md-7 mb-2 mb-md-0">
                          <div class="p-2 rounded bg-light border" style="font-size: 12px;">
                            <strong class="text-danger"><i class="fa fa-bullseye mr-1"></i> Target Sub-CPMK:</strong>
                            <div class="text-dark mt-1"><?= nl2br(htmlspecialchars($m['sub_cpmk'])); ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="col-md-5">
                        <div class="p-2 rounded bg-light border" style="font-size: 12px;">
                          <strong class="text-info"><i class="fa fa-cogs mr-1"></i> Bentuk & Metode Pembelajaran:</strong>
                          <div class="text-dark mt-1 font-weight-bold"><?= htmlspecialchars(!empty($m['metode']) ? $m['metode'] : (!empty($m['metode_pembelajaran']) ? $m['metode_pembelajaran'] : 'Kuliah Interaktif, Diskusi Kelompok')); ?></div>
                        </div>
                      </div>
                    </div>

                    <!-- Skenario Strategi Pembelajaran (SCL) Cockpit -->
                    <div class="mb-3">
                      <h6 class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">
                        🎯 Skenario Strategi Pembelajaran (SCL):
                      </h6>
                      <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb;">
                        <?= render_scl_scenario_cockpit_html(!empty($m['skenario_scl']) ? $m['skenario_scl'] : $m['berita_acara_rencana']); ?>
                      </div>
                    </div>

                    <!-- Draft Evaluasi Pertemuan -->
                    <?php if (!empty($m['draf_bap']) && $m['draf_bap'] != '-'): ?>
                      <div class="mt-3 pt-3 border-top">
                        <h6 class="font-weight-bold text-success mb-2" style="font-size: 13px;">
                          📝 Draft Evaluasi Pertemuan:
                        </h6>
                        <div class="p-3 rounded bg-light" style="border: 1px solid #e2e8f0; border-left: 4px solid #22c55e;">
                          <?= render_bap_faktual_cockpit_html($m['draf_bap']); ?>
                        </div>
                      </div>
                    <?php endif; ?>

                  </div>

                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fa fa-info-circle fa-3x mb-3 text-info"></i>
            <p>Belum ada rincian strategi pertemuan yang tersimpan pada sistem.</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

