<?php
$prioritas_mhs = array();
$selesai_mhs = array();
$other_mhs = array();
$notifications = array();

$id_ptk = $this->session->userdata('id_ptk');
$today = date('Y-m-d');

foreach ($data_mahasiswa as $m) {
    if ($m->is_locked) {
        continue; // Skip locked students entirely (completed next steps)
    }

    $has_schedule = (!empty($m->tanggal_sempro) && $m->tanggal_sempro != '0000-00-00');
    $is_graded = (!empty($m->nilai_akhir) && $m->nilai_akhir > 0);
    $has_no_result = empty($m->hasil_sempro) || $m->hasil_sempro == '-' || strtolower((string)$m->hasil_sempro) == 'belum_ada';
    
    if ($has_schedule && !$is_graded) {
        // Ungraded but scheduled -> Prioritas
        $prioritas_mhs[] = $m;
    } elseif ($is_graded && $has_no_result) {
        // Graded but BA not filled -> Prioritas
        $prioritas_mhs[] = $m;
    } elseif ($is_graded && !$has_no_result) {
        // Graded and has result -> Selesai
        $selesai_mhs[] = $m;
    } else {
        // Unscheduled -> Other
        $other_mhs[] = $m;
    }

    // Smart notifications logic for finished seminars
    if ($has_schedule && $m->tanggal_sempro < $today) {
        $is_pembimbing_utama = ($m->status_dosen == 'Pembimbing Utama');
        $other_examiners = $m->total_penguji - 1;
        $others_graded = $m->hitung_dinilai - ($is_graded ? 1 : 0);
        $others_graded_complete = ($others_graded >= $other_examiners);
        $all_graded = ($m->hitung_dinilai >= $m->total_penguji);

        if ($is_pembimbing_utama) {
            if ($has_no_result) {
                if (!$is_graded && $others_graded_complete) {
                    $notifications[] = array(
                        'type' => 'warning',
                        'message' => 'Sempro <strong>' . htmlspecialchars($m->nm_pd) . '</strong> sudah selesai, semua penguji selain anda sudah mengisi nilai, silahkan isi dan selanjutnya isi Berita Acara',
                        'actions' => array(
                            array('label' => 'Isi Nilai', 'onclick' => "edit_nilai_sempro('" . $m->id_nilai_sempro . "')", 'class' => 'green'),
                            array('label' => 'Usulan Revisi', 'onclick' => "edit_catatan('" . $m->id_nilai_sempro . "')", 'class' => 'blue')
                        )
                    );
                } elseif (!$is_graded && !$others_graded_complete) {
                    $notifications[] = array(
                        'type' => 'danger',
                        'message' => 'Sempro <strong>' . htmlspecialchars($m->nm_pd) . '</strong> sudah selesai, Anda belum mengisi nilai',
                        'actions' => array(
                            array('label' => 'Isi Nilai', 'onclick' => "edit_nilai_sempro('" . $m->id_nilai_sempro . "')", 'class' => 'green'),
                            array('label' => 'Usulan Revisi', 'onclick' => "edit_catatan('" . $m->id_nilai_sempro . "')", 'class' => 'blue')
                        )
                    );
                } elseif ($is_graded && $all_graded) {
                    $notifications[] = array(
                        'type' => 'success',
                        'message' => 'Sempro <strong>' . htmlspecialchars($m->nm_pd) . '</strong> sudah selesai, semua penguji sudah mengisi nilai, silahkan isi Berita Acara',
                        'actions' => array(
                            array('label' => 'Isi Berita Acara', 'onclick' => "edit_berita_acara('" . $m->id_nilai_sempro . "')", 'class' => 'dark')
                        )
                    );
                } elseif ($is_graded && !$all_graded) {
                    $notifications[] = array(
                        'type' => 'info',
                        'message' => 'Sempro <strong>' . htmlspecialchars($m->nm_pd) . '</strong> sudah selesai, Anda sudah mengisi nilai. Menunggu penguji lain mengisi nilai untuk mengisi Berita Acara.',
                        'actions' => array()
                    );
                }
            }
        } else {
            if (!$is_graded) {
                $notifications[] = array(
                    'type' => 'danger',
                    'message' => 'Sempro <strong>' . htmlspecialchars($m->nm_pd) . '</strong> sudah selesai, Anda belum mengisi nilai',
                    'actions' => array(
                        array('label' => 'Isi Nilai', 'onclick' => "edit_nilai_sempro('" . $m->id_nilai_sempro . "')", 'class' => 'green'),
                        array('label' => 'Usulan Revisi', 'onclick' => "edit_catatan('" . $m->id_nilai_sempro . "')", 'class' => 'blue')
                    )
                );
            }
        }
    }
}

// Sort prioritas_mhs by tanggal_sempro DESC (latest first)
usort($prioritas_mhs, function($a, $b) {
    $timeA = !empty($a->tanggal_sempro) ? strtotime($a->tanggal_sempro) : 0;
    $timeB = !empty($b->tanggal_sempro) ? strtotime($b->tanggal_sempro) : 0;
    if ($timeA == $timeB) {
        return strcmp($a->nipd, $b->nipd);
    }
    return $timeB - $timeA;
});

// Sort selesai_mhs by tanggal_sempro DESC (latest first)
usort($selesai_mhs, function($a, $b) {
    $timeA = !empty($a->tanggal_sempro) ? strtotime($a->tanggal_sempro) : 0;
    $timeB = !empty($b->tanggal_sempro) ? strtotime($b->tanggal_sempro) : 0;
    if ($timeA == $timeB) {
        return strcmp($a->nipd, $b->nipd);
    }
    return $timeB - $timeA;
});

// Sort other_mhs by NIM (nipd asc)
usort($other_mhs, function($a, $b) {
    return strcmp($a->nipd, $b->nipd);
});

$groups = array();
if (count($prioritas_mhs) > 0) {
    $groups[] = array(
        'title' => '<i class="fa fa-bell" style="color: #F59E0B; margin-right: 6px;"></i> Prioritas (Belum Dinilai / BA Belum Diisi)',
        'data' => $prioritas_mhs,
        'is_priority' => true
    );
}
if (count($selesai_mhs) > 0) {
    $groups[] = array(
        'title' => '<i class="fa fa-check-circle" style="color: #10B981; margin-right: 6px;"></i> Sudah Selesai Dinilai',
        'data' => $selesai_mhs,
        'is_priority' => false
    );
}
if (count($other_mhs) > 0) {
    $groups[] = array(
        'title' => '<i class="fa fa-calendar-o" style="color: #4F46E5; margin-right: 6px;"></i> Mahasiswa Lainnya (Belum Terjadwal)',
        'data' => $other_mhs,
        'is_priority' => false
    );
}
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .sempro-dashboard {
        --primary: #4F46E5;
        --primary-hover: #4338CA;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
        --background: #F3F4F6;
        --surface: #FFFFFF;
        --text-main: #111827;
        --text-muted: #6B7280;
        --border: #E5E7EB;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-main);
    }

    .sempro-dashboard * { box-sizing: border-box; }

    /* Flex Header */
    .sempro-dashboard.portlet .portlet-title {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        border-bottom: 1px solid #E5E7EB !important;
        padding-bottom: 12px !important;
        margin-bottom: 20px !important;
        width: 100% !important;
        float: none !important;
        min-height: auto !important;
        height: auto !important;
    }
    .sempro-dashboard.portlet .portlet-title::before,
    .sempro-dashboard.portlet .portlet-title::after {
        display: none !important;
    }
    .sempro-dashboard.portlet .portlet-title .caption {
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
        text-align: left !important;
        float: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .sempro-dashboard.portlet .portlet-title .actions {
        margin: 0 !important;
        padding: 0 !important;
        float: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px;
    }

    /* Grid layout */
    .student-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    @media (max-width: 1600px) {
        .student-grid-5 { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 1200px) {
        .student-grid-5 { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 992px) {
        .student-grid-5 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .student-grid-5 { grid-template-columns: 1fr; }
    }

    /* Card styling */
    .student-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 0;
        overflow: hidden;
        border: 1px solid var(--border);
        position: relative;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
    }
    .student-card:hover { border-color: #D1D5DB; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08); }

    .card-header-band {
        height: 80px;
        position: relative;
    }

    .badge-top {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 10;
        font-weight: 700;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10px;
        text-transform: uppercase;
        color: #FFFFFF;
    }
    
    .badge-top.priority { background: #FEF3C7; color: #92400E; }
    .badge-top.upcoming { background: #E0E7FF; color: #3730A3; }
    .badge-top.other { background: #D1FAE5; color: #065F46; }
    .badge-top.locked { background: #F3F4F6; color: #4B5563; border: 1px solid #D1D5DB; }
    .badge-top.unscheduled { background: #E5E7EB; color: #374151; border: 1px solid #D1D5DB; }

    .card-avatar-wrapper {
        text-align: center;
        margin-top: -40px;
        position: relative;
        z-index: 5;
        margin-bottom: 8px;
    }
    .card-avatar-wrapper div {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #FFFFFF;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        display: inline-block;
        background: #F3F4F6;
    }

    .card-student-body {
        padding: 0 15px 15px 15px;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .student-name {
        font-size: 14px;
        font-weight: 800;
        margin: 0 0 2px 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #111827;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .student-nim {
        font-size: 11px;
        color: #6B7280;
        margin: 0 0 10px 0;
        font-weight: 600;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        font-size: 10px;
        font-weight: 700;
        margin: 0 auto 12px auto;
        text-transform: uppercase;
    }
    .role-badge.utama { background: #EEF2FF; color: #4F46E5; }
    .role-badge.pendamping { background: #E0F2FE; color: #0284C7; }
    .role-badge.penguji { background: #F1F5F9; color: #475569; }

    /* Details block */
    .details-list {
        text-align: left;
        background: #F9FAFB;
        border-radius: var(--radius-md);
        padding: 10px 12px;
        font-size: 11.5px;
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
    }
    .details-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed #E5E7EB;
        padding-bottom: 4px;
    }
    .details-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .details-label {
        color: var(--text-muted);
        font-weight: 600;
    }
    .details-val {
        font-weight: 700;
        color: var(--text-main);
        text-align: right;
    }
    .details-val.warning { color: #D97706; }
    .details-val.success { color: #059669; }

    .card-actions {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: auto;
    }
    .btn-action {
        width: 100%;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        border: none;
        text-decoration: none !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
    }
    .btn-action.green { background: #10B981; color: #FFFFFF; }
    .btn-action.green:hover { background: #059669; }
    .btn-action.blue { background: #3B82F6; color: #FFFFFF; }
    .btn-action.blue:hover { background: #2563EB; }
    .btn-action.gray { background: #6B7280; color: #FFFFFF; }
    .btn-action.gray:hover { background: #4B5563; }
    .btn-action.dark { background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #FFFFFF; border: none; }
    .btn-action.dark:hover { background: linear-gradient(135deg, #334155 0%, #1E293B 100%); box-shadow: 0 4px 10px rgba(15, 23, 42, 0.3); }
    .btn-action.download { background: #EF4444; color: #FFFFFF; }
    .btn-action.download:hover { background: #DC2626; }
    .btn-gdrive:hover {
        background: #F9FAFB !important;
        border-color: #D1D5DB !important;
        color: #111827 !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08) !important;
    }

    /* Smart Notifications Styles */
    .smart-notifications-box {
        background: #F9FAFB;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .smart-notifications-title {
        font-size: 14px;
        font-weight: 800;
        color: #1F2937;
        margin-top: 0;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .smart-notifications-title i {
        font-size: 16px;
        animation: smart-pulse 2s infinite;
    }
    @keyframes smart-pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .smart-notification-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border-radius: var(--radius-md);
        margin-bottom: 10px;
        gap: 16px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .smart-notification-item:last-child {
        margin-bottom: 0;
    }
    .smart-notification-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.03);
    }
    .smart-notification-item.type-danger {
        background: #FEF2F2;
        border-color: #FCA5A5;
        color: #991B1B;
    }
    .smart-notification-item.type-warning {
        background: #FFFBEB;
        border-color: #FCD34D;
        color: #92400E;
    }
    .smart-notification-item.type-success {
        background: #ECFDF5;
        border-color: #6EE7B7;
        color: #065F46;
    }
    .smart-notification-item.type-info {
        background: #F0F9FF;
        border-color: #7DD3FC;
        color: #075985;
    }
    .smart-notification-content {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
    }
    .smart-notification-content i {
        flex-shrink: 0;
    }
    .smart-notification-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }
    .smart-btn {
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 700;
        border-radius: var(--radius-sm);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
        text-decoration: none !important;
    }
    .smart-btn.green { background: #10B981; color: #FFFFFF; }
    .smart-btn.green:hover { background: #059669; }
    .smart-btn.blue { background: #3B82F6; color: #FFFFFF; }
    .smart-btn.blue:hover { background: #2563EB; }
    .smart-btn.gray { background: #6B7280; color: #FFFFFF; }
    .smart-btn.gray:hover { background: #4B5563; }
    .smart-btn.dark { background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #FFFFFF; border: none; }
    .smart-btn.dark:hover { background: linear-gradient(135deg, #334155 0%, #1E293B 100%); box-shadow: 0 4px 10px rgba(15, 23, 42, 0.3); }
</style>

<div class="page-content" style="padding-top: 0;">
  <div class="row">
    <div class="col-md-12">
      <div class="portlet light sempro-dashboard">
        <div class="portlet-title">
          <div class="caption">
            <span class="caption-subject font-green-sharp bold uppercase" style="font-weight: 800; font-size: 16px; color: #111827;"><?= $judul_besar; ?></span>
            <span class="caption-helper" style="font-size: 12px; color: #6B7280; font-weight: 500;"><?= $judul_kecil; ?></span>
          </div>
          <div class="actions">
            <button class="btn btn-default btn-sm" onclick="reload_table()" style="border-radius: 8px; font-weight: 700; padding: 6px 14px; background: #EEF2FF; color: #4F46E5; border: 1px solid #C7D2FE;"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            <button class="btn btn-default btn-sm" onclick="view_rubrik()" style="border-radius: 8px; font-weight: 700; padding: 6px 14px; background: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0;"><i class="glyphicon glyphicon-search"></i> Rubrik Penilaian</button>
          </div>
        </div>
        <div class="portlet-body">

          <?php if (!empty($notifications)): ?>
            <div class="smart-notifications-box">
              <div class="smart-notifications-title">
                <i class="fa fa-bell" style="color: #F59E0B;"></i> Pemberitahuan Penting Seminar Proposal
              </div>
              <?php foreach ($notifications as $notif): ?>
                <?php
                  $icon = 'fa-info-circle';
                  if ($notif['type'] == 'danger') $icon = 'fa-exclamation-circle';
                  elseif ($notif['type'] == 'warning') $icon = 'fa-exclamation-triangle';
                  elseif ($notif['type'] == 'success') $icon = 'fa-check-circle';
                ?>
                <div class="smart-notification-item type-<?= $notif['type'] ?>">
                  <div class="smart-notification-content">
                    <i class="fa <?= $icon ?>" style="font-size: 14px;"></i>
                    <span><?= $notif['message'] ?></span>
                  </div>
                  <div class="smart-notification-actions">
                    <?php foreach ($notif['actions'] as $act): ?>
                      <button class="smart-btn <?= $act['class'] ?>" onclick="<?= $act['onclick'] ?>">
                        <?= $act['label'] ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php foreach ($groups as $group): ?>
            <?php if (!empty($group['title'])): ?>
              <h4 style="font-weight: 800; color: #374151; margin-top: <?= ($group === $groups[0]) ? '0' : '30px' ?>; margin-bottom: 18px; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px; font-size: 15px;">
                <?= $group['title'] ?> (<?= count($group['data']) ?>)
              </h4>
            <?php endif; ?>

            <div class="student-grid-5">
              <?php foreach ($group['data'] as $m): ?>
                <?php
                  $has_schedule = (!empty($m->tanggal_sempro) && $m->tanggal_sempro != '0000-00-00');
                  $is_graded = (!empty($m->nilai_akhir) && $m->nilai_akhir > 0);

                  // Determine Dosen Role Badge CSS
                  $role_class = 'penguji';
                  if ($m->status_dosen == 'Pembimbing Utama') {
                      $role_class = 'utama';
                  } elseif ($m->status_dosen == 'Pembimbing Pendamping/Penguji') {
                      $role_class = 'pendamping';
                  }

                  // Determine Dynamic Badge Text & Color
                  if ($m->is_locked) {
                      $band_bg = 'linear-gradient(135deg, #9CA3AF 0%, #4B5563 100%)';
                      $badge_text = '🔒 Terkunci';
                      $badge_class = 'locked';
                  } else {
                      $has_no_result = empty($m->hasil_sempro) || $m->hasil_sempro == '-' || strtolower((string)$m->hasil_sempro) == 'belum_ada';
                      
                      if ($is_graded && $has_no_result) {
                          $band_bg = 'linear-gradient(135deg, #FBBF24 0%, #D97706 100%)';
                          $badge_text = 'BA Belum Diisi';
                          $badge_class = 'priority';
                      } elseif ($has_schedule && !$is_graded) {
                          if ($m->tanggal_sempro >= $today) {
                              $band_bg = 'linear-gradient(135deg, #818CF8 0%, #4F46E5 100%)';
                              $badge_text = '📅 Upcoming';
                              $badge_class = 'upcoming';
                          } else {
                              $band_bg = 'linear-gradient(135deg, #FBBF24 0%, #D97706 100%)';
                              $badge_text = '⏳ Belum Dinilai';
                              $badge_class = 'priority';
                          }
                      } elseif (!$has_schedule) {
                          $band_bg = 'linear-gradient(135deg, #E5E7EB 0%, #9CA3AF 100%)';
                          $badge_text = '⚪ Belum Terjadwal';
                          $badge_class = 'unscheduled';
                      } else {
                          $band_bg = 'linear-gradient(135deg, #34D399 0%, #059669 100%)';
                          $badge_text = '✅ Selesai';
                          $badge_class = 'other';
                      }
                  }
                ?>
                <div class="student-card" id="card-<?= $m->id_nilai_sempro ?>">
                  <div class="card-header-band" style="background: <?= $band_bg ?>;">
                    <div class="badge-top <?= $badge_class ?>"><?= $badge_text ?></div>
                  </div>

                  <div class="card-avatar-wrapper">
                    <div>
                      <?php if (!empty($m->foto)): ?>
                        <img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_<?= rawurlencode($m->foto) ?>" 
                             onerror="this.onerror=function(){this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($m->nm_pd) ?>&background=0D9488&color=fff';}; this.src='https://files.wbi.ac.id/siakad_file_mahasiswa/<?= rawurlencode($m->foto) ?>';" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                      <?php else: ?>
                        <img src="<?= base_url('assets/no_profile_photo.png') ?>" alt="No Photo" style="width: 100%; height: 100%; object-fit: cover;">
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="card-student-body">
                    <h3 class="student-name" title="<?= $m->nm_pd ?>"><?= $m->nm_pd ?></h3>
                    <p class="student-nim"><?= $m->nipd ?></p>

                    <div class="role-badge <?= $role_class ?>"><?= $m->status_dosen ?></div>

                    <?php if (!empty($m->link_dokumen)): ?>
                      <a href="<?= $m->link_dokumen ?>" target="_blank" class="btn-gdrive" style="margin-bottom: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 12px; font-size: 11px; font-weight: 700; border-radius: 8px; border: 1px solid #E5E7EB; background: #FFFFFF; color: #374151; transition: all 0.2s; text-decoration: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: fit-content; margin-left: auto; margin-right: auto;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg" alt="G-Drive" style="width: 14px; height: 14px;"> Buka Berkas
                      </a>
                    <?php else: ?>
                      <span class="btn-gdrive-disabled" style="margin-bottom: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 12px; font-size: 11px; font-weight: 700; border-radius: 8px; border: 1px solid #E5E7EB; background: #F3F4F6; color: #9CA3AF; width: fit-content; margin-left: auto; margin-right: auto; cursor: not-allowed; user-select: none;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg" alt="G-Drive" style="width: 14px; height: 14px; filter: grayscale(100%); opacity: 0.5;"> Berkas Belum Diunggah
                      </span>
                    <?php endif; ?>

                    <div class="details-list">
                      <div class="details-item">
                        <span class="details-label">📅 Jadwal</span>
                        <span class="details-val">
                          <?= $has_schedule ? tgl_indo_short($m->tanggal_sempro) . ' | ' . substr($m->jam_sempro, 0, 5) : 'Belum Terjadwal' ?>
                        </span>
                      </div>
                      <div class="details-item">
                        <span class="details-label">📍 Tempat</span>
                        <span class="details-val" title="<?= $m->tempat_sempro ?>"><?= !empty($m->tempat_sempro) ? $m->tempat_sempro : '-' ?></span>
                      </div>
                      <div class="details-item">
                        <span class="details-label">✏️ Nilai Saya</span>
                        <span class="details-val <?= $is_graded ? 'success' : 'warning' ?>">
                          <?= $is_graded ? ($m->nilai_akhir * 1) : 'Belum Dinilai' ?>
                        </span>
                      </div>
                      <div class="details-item">
                        <span class="details-label">📊 Rata-rata</span>
                        <span class="details-val"><?= ($m->nilai_rerata * 1) ?> <small class="text-muted">(<?= $m->hitung_dinilai ?> dosen)</small></span>
                      </div>
                      <div class="details-item">
                        <span class="details-label">📝 Hasil</span>
                        <span class="details-val"><?= $m->hasil_sempro ?></span>
                      </div>
                    </div>

                    <div class="card-actions">
                      <?php if (!$m->is_locked): ?>
                        
                        <?php 
                          $is_future = $has_schedule && $m->tanggal_sempro > $today;
                          $is_editable = $has_schedule && !$is_future;
                        ?>

                        <?php if ($has_schedule && !$is_graded && $m->tanggal_sempro >= $today): ?>
                          <?php
                              $dt_today = date_create($today);
                              $dt_sempro = date_create($m->tanggal_sempro);
                              $interval = date_diff($dt_today, $dt_sempro);
                              $days = (int)$interval->format('%a');
                              if ($days == 0) {
                                  $time_text = "hari ini";
                              } elseif ($days == 1) {
                                  $time_text = "besok";
                              } else {
                                  $weeks = floor($days / 7);
                                  $rem_days = $days % 7;
                                  $time_text = "dalam ";
                                  if ($weeks > 0) $time_text .= $weeks . " minggu ";
                                  if ($rem_days > 0) $time_text .= $rem_days . " hari";
                                  $time_text = trim($time_text);
                              }
                          ?>
                          <span style="font-size: 11px; color: #4F46E5; display: block; margin-bottom: 6px; font-weight: 600; text-align: center;">
                            ⏳ Dijadwalkan <?= $time_text ?>
                          </span>
                        <?php endif; ?>

                        <?php 
                          $other_examiners = $m->total_penguji - 1;
                          $others_graded = $m->hitung_dinilai - ($is_graded ? 1 : 0);
                        ?>

                        <?php if ($m->status_dosen == 'Pembimbing Utama' && $has_no_result && $has_schedule && $m->tanggal_sempro < $today): ?>
                          <?php if ($others_graded >= $other_examiners): ?>
                            <span style="font-size: 10px; color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 6px 8px; border-radius: 6px; display: block; margin-bottom: 8px; font-weight: 600; line-height: 1.4; text-align: center;">
                              Semua dosen penguji sudah memberi nilai, namun BA belum bisa di download karena status Layak belum diberikan.
                            </span>
                          <?php else: ?>
                            <span style="font-size: 10px; color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; padding: 6px 8px; border-radius: 6px; display: block; margin-bottom: 8px; font-weight: 600; line-height: 1.4; text-align: center;">
                              Nilai belum lengkap, belum bisa membuat BA.
                            </span>
                          <?php endif; ?>
                        <?php endif; ?>

                        <?php 
                          $btn_badge_nilai = ($has_schedule && !$is_graded && $m->tanggal_sempro < $today) ? ' <span style="background:#EF4444; color:white; border-radius:10px; padding:2px 6px; font-size:9px; margin-left:4px; font-weight:bold; box-shadow: 0 0 0 1px rgba(239,68,68,0.5); vertical-align: middle;">BELUM DIISI</span>' : ''; 
                          
                          $is_catatan_filled = !empty($m->catatan_judul) || !empty($m->catatan_latar_belakang) || !empty($m->catatan_rumusan_masalah) || !empty($m->catatan_tujuan_penelitian) || !empty($m->catatan_tinjauan_pustaka) || !empty($m->catatan_metode_penelitian) || !empty($m->catatan_daftar_pustaka) || !empty($m->catatan_format_penulisan);
                          $btn_badge_catatan = ($has_schedule && !$is_catatan_filled && $m->tanggal_sempro < $today) ? ' <span style="background:#EF4444; color:white; border-radius:10px; padding:2px 6px; font-size:9px; margin-left:4px; font-weight:bold; box-shadow: 0 0 0 1px rgba(239,68,68,0.5); vertical-align: middle;">BELUM DIISI</span>' : ''; 
                        ?>
                        <?php if ($is_editable): ?>
                          <button class="btn-action green" onclick="edit_nilai_sempro('<?= $m->id_nilai_sempro ?>')">
                            <i class="glyphicon glyphicon-pencil"></i> Nilai SEMPRO<?= $btn_badge_nilai ?>
                          </button>
                          <button class="btn-action blue" onclick="edit_catatan('<?= $m->id_nilai_sempro ?>')">
                            <i class="glyphicon glyphicon-list"></i> Usulan Revisi<?= $btn_badge_catatan ?>
                          </button>
                        <?php endif; ?>

                        <?php if (($has_schedule && $m->tanggal_sempro >= $today) || !empty($m->has_ai_record)): ?>
                          <a class="btn-action" href="<?= site_url('E_skripsi_nilai_sempro/ai_assistant/' . $m->id_judul_skripsi) ?>" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); color: white; border: none;">
                            <i class="fa fa-magic"></i> Examiner Assistant (AI)
                          </a>
                        <?php endif; ?>

                        <?php if ($m->status_dosen == 'Pembimbing Utama' && $others_graded >= $other_examiners): ?>
                          <button class="btn-action dark" onclick="edit_berita_acara('<?= $m->id_nilai_sempro ?>')">
                            <i class="glyphicon glyphicon-pencil"></i> Isi Berita Acara
                          </button>
                        <?php endif; ?>

                      <?php else: ?>
                        <span style="font-weight: 800; color: #DC2626; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; margin: 5px 0; display: block;">
                          NILAI SUDAH DIKUNCI
                        </span>
                      <?php endif; ?>

                      <?php if (!$has_no_result): ?>
                        <a class="btn-action download" href="<?= site_url('E_skripsi_nilai_sempro/cetak_pdf/' . md5($m->id_judul_skripsi)) ?>" target="_blank">
                          <i class="fa fa-download"></i> Download Berita Acara
                        </a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>

          <?php if (empty($data_mahasiswa)): ?>
            <div style="text-align: center; padding: 40px; color: #6B7280; font-weight: 600;">
              Belum ada mahasiswa Seminar Proposal yang ditugaskan.
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>