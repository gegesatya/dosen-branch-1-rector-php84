<?php 
if (!function_exists('time_ago_indo')) {
    function time_ago_indo($datetime) {
        if (empty($datetime)) return '';
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit yang lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam yang lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari yang lalu';
        if ($diff < 2592000) return floor($diff / 604800) . ' minggu yang lalu';
        return date('d/m/Y', $time);
    }
}

            $count_all = count($data_mahasiswa);
            $count_safe = 0; $count_warning = 0; $count_danger = 0; $count_unstarted = 0;
            foreach($data_mahasiswa as $m) {
                                $is_belum_mulai = ($m->total_bimbingan == 0 || empty($m->last_bimbingan_date));
                if ($is_belum_mulai) {
                    $count_unstarted++;
                }
                
                                if ($m->kategori_health == 'Risiko Tinggi') {
                    $count_danger++;
                } else if ($m->kategori_health == 'Perlu Perhatian') {
                    $count_warning++;
                } else if ($m->kategori_health == 'Aman') {
                    $count_safe++;
                }
            }
        ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin:0; font-size: 1.5rem; color: #111827; font-weight: 800;">Dashboard Bimbingan</h2>
            <a href="<?= base_url('e_skripsi_bimbingan/cetak_rekap_bimbingan') ?>" target="_blank" class="btn btn-primary" style="background: #0D9488; border-color: #0F766E; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);">
                <i class="fa fa-print" style="margin-right: 6px;"></i> Cetak Rekap Bimbingan
            </a>
        </div>

        
        <div class="summary-cards">
            <div class="summary-card">
                <div class="icon all"><i class="fa fa-users"></i></div>
                <div class="info">
                    <span>Total Bimbingan</span>
                    <strong><?= $count_all ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon" style="background: #F3F4F6; color: #6B7280;"><i class="fa fa-history"></i></div>
                <div class="info">
                    <span>Belum Mulai</span>
                    <strong><?= $count_unstarted ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon safe"><i class="fa fa-check-circle"></i></div>
                <div class="info">
                    <span>Status Aman</span>
                    <strong><?= $count_safe ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon warning"><i class="fa fa-exclamation-triangle"></i></div>
                <div class="info">
                    <span>Perlu Perhatian</span>
                    <strong><?= $count_warning ?></strong>
                </div>
            </div>
            <div class="summary-card">
                <div class="icon danger"><i class="fa fa-ban"></i></div>
                <div class="info">
                    <span>Risiko Tinggi</span>
                    <strong><?= $count_danger ?></strong>
                </div>
            </div>
        </div>

        <?php 
            $history_mhs = [];
            foreach($data_mahasiswa as $m) {
                $time_review = !empty($m->tgl_terakhir_review) ? strtotime($m->tgl_terakhir_review) : 0;
                $time_chat = !empty($m->tgl_terakhir_chat) ? strtotime($m->tgl_terakhir_chat) : 0;
                $time_agenda = !empty($m->tgl_terakhir_agenda) ? strtotime($m->tgl_terakhir_agenda) : 0;
                $time_todo = !empty($m->tgl_terakhir_todo) ? strtotime($m->tgl_terakhir_todo) : 0;
                
                $max_time = max($time_review, $time_chat, $time_agenda, $time_todo);
                
                $ten_days_ago = time() - (10 * 86400); // 10 hari terakhir
                
                if ($max_time > 0 && $max_time >= $ten_days_ago) {
                    if ($max_time == $time_review) {
                        $m->tgl_terakhir_activity = $m->tgl_terakhir_review;
                        $m->activity_type = 'review';
                    } elseif ($max_time == $time_chat) {
                        $m->tgl_terakhir_activity = $m->tgl_terakhir_chat;
                        $m->activity_type = 'chat';
                    } elseif ($max_time == $time_todo) {
                        $m->tgl_terakhir_activity = $m->tgl_terakhir_todo;
                        $m->activity_type = 'todo';
                    } else {
                        $m->tgl_terakhir_activity = $m->tgl_terakhir_agenda;
                        $m->activity_type = 'agenda';
                    }
                    $history_mhs[] = $m;
                }
            }
            usort($history_mhs, function($a, $b) {
                $dateA = strtotime($a->tgl_terakhir_activity);
                $dateB = strtotime($b->tgl_terakhir_activity);
                return $dateB <=> $dateA; // Descending
            });
            $history_mhs = array_slice($history_mhs, 0, 15); // Batasi 15 item
        ?>

        <?php if (count($history_mhs) > 0): ?>
        <style>
            .stories-container {
                display: flex;
                gap: 16px;
                overflow-x: auto;
                padding: 10px 5px 15px 5px;
                margin-bottom: 5px;
                scrollbar-width: thin; /* Firefox */
                scrollbar-color: #D1D5DB transparent;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
            }
            .stories-container::-webkit-scrollbar {
                height: 6px;
            }
            .stories-container::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 10px;
            }
            .stories-container::-webkit-scrollbar-thumb {
                background: #D1D5DB;
                border-radius: 10px;
            }
            .stories-container::-webkit-scrollbar-thumb:hover {
                background: #9CA3AF;
            }
            .story-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none !important;
                min-width: 76px;
                cursor: pointer;
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .story-item:hover {
                transform: scale(1.05);
            }
            .story-ring {
                width: 72px;
                height: 72px;
                border-radius: 50%;
                background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
                padding: 3px;
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 6px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.08);
                position: relative;
            }
            .story-photo {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                border: 2.5px solid #FFFFFF;
                background-color: #FFFFFF;
                object-fit: cover;
            }
            .story-name {
                font-size: 11px;
                font-weight: 700;
                color: #374151;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 76px;
            }
            .story-time {
                font-size: 9.5px;
                color: #9CA3AF;
                font-weight: 600;
                margin-top: 1px;
            }
            .story-badge {
                position: absolute;
                bottom: -4px;
                background: #EF4444;
                color: #FFFFFF;
                font-size: 8px;
                font-weight: 800;
                padding: 2px 6px;
                border-radius: 8px;
                border: 2px solid #FFFFFF;
                letter-spacing: 0.5px;
            }
        </style>
        <div style="margin-bottom: 24px; margin-top: 24px;">
            <h3 style="font-size: 14px; font-weight: 800; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-history" style="color: #bc1888;"></i> Aktivitas Terakhir (Review, Chat, Todo & Agenda)
            </h3>
            <div class="stories-container">
                <?php foreach($history_mhs as $idx => $hm): 
                    $photo_thumb = !empty($hm->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($hm->foto) : base_url('assets/no_profile_photo.png');
                    $nama_parts = explode(' ', trim((string)$hm->nm_pd));
                    $nama_pendek = strtolower($nama_parts[0]);
                    $time_str = time_ago_indo($hm->tgl_terakhir_activity);
                    $time_short = str_replace([' yang lalu', 'menit', 'hari', 'minggu', 'Baru saja'], ['', 'mnt', 'hr', 'mgg', 'Barusan'], $time_str);
                    
                    $icon_html = '';
                    if ($hm->activity_type == 'chat') {
                        $icon_html = '<i class="fa fa-commenting" style="color:#06B6D4; font-size: 10px; margin-right: 2px;"></i> ';
                    } elseif ($hm->activity_type == 'agenda') {
                        $icon_html = '<i class="fa fa-calendar-check-o" style="color:#8B5CF6; font-size: 10px; margin-right: 2px;"></i> ';
                    } elseif ($hm->activity_type == 'todo') {
                        $icon_html = '<i class="fa fa-check-square-o" style="color:#10B981; font-size: 10px; margin-right: 2px;"></i> ';
                    }
                    $act_title = 'Baru di-review';
                    if ($hm->activity_type == 'chat') $act_title = 'Baru di-chat';
                    if ($hm->activity_type == 'agenda') $act_title = 'Agenda dibuat';
                    if ($hm->activity_type == 'todo') $act_title = 'Mereview To-Do list';
                ?>
                <a href="<?= base_url('E_skripsi_bimbingan/detail/'.$hm->id_judul_skripsi) ?>" class="story-item" title="<?= $hm->nm_pd ?> (<?= $hm->nipd ?>) - <?= $act_title ?>">
                    <div class="story-ring" <?= ($idx > 4) ? 'style="background: #D1D5DB;"' : '' ?>>
                        <img src="<?= $photo_thumb ?>" class="story-photo" onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';">
                        <?php if ($idx === 0 && $time_short == 'Barusan'): ?>
                            <div class="story-badge">NEW</div>
                        <?php endif; ?>
                    </div>
                    <span class="story-name"><?= htmlspecialchars($nama_pendek) ?></span>
                    <span class="story-time"><?= $icon_html ?><?= $time_short ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Real-time Search Bar -->
        <div class="search-wrapper" style="margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div class="search-container" style="position: relative; flex: 1; max-width: 400px; min-width: 280px;">
                <i class="fa fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 14px;"></i>
                <input type="text" id="search-bimbingan" placeholder="Cari nama atau NIM mahasiswa..." 
                       style="width: 100%; padding: 10px 14px 10px 40px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; font-weight: 500; outline: none; transition: all 0.2s; background: #FAFBFD;"
                       onkeyup="filterBimbingan()">
                <button id="clear-bimbingan-search" onclick="clearBimbinganSearch()" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94A3B8; cursor: pointer; display: none; padding: 0;">
                    <i class="fa fa-times-circle" style="font-size: 16px;"></i>
                </button>
            </div>
        </div>

        <div>
            <?php 
                $pending_mhs = [];
                $other_mhs = [];
                $lulus_mhs = [];
                
                foreach($data_mahasiswa as $m) {
                    $is_pending_pendamping = ($m->jumlah_pending == 0 && isset($m->pending_di_pendamping) && $m->pending_di_pendamping > 0);
                    
                    if (isset($m->hasil_sidang) && $m->hasil_sidang == 'LULUS') {
                        $lulus_mhs[] = $m;
                    } elseif ($m->jumlah_pending > 0) {
                        $pending_mhs[] = $m;
                    } else {
                        $other_mhs[] = $m;
                    }
                }

                // Sort pending_mhs by NIM DESC
                usort($pending_mhs, function($a, $b) {
                    return strcmp($b->nipd, $a->nipd);
                });

                // Sort other_mhs by NIM DESC
                usort($other_mhs, function($a, $b) {
                    return strcmp($b->nipd, $a->nipd);
                });

                // Sort lulus_mhs by NIM DESC
                usort($lulus_mhs, function($a, $b) {
                    return strcmp($b->nipd, $a->nipd);
                });

                $groups = [];
                if (count($pending_mhs) > 0) {
                    $groups[] = [
                        'title' => '<i class="fa fa-bell" style="color: #F59E0B; margin-right: 6px;"></i> Prioritas: Butuh Respon',
                        'data' => $pending_mhs,
                        'grid_class' => 'student-grid-5',
                        'card_class' => 'student-card-small'
                    ];
                }
                
                if (count($other_mhs) > 0) {
                    $groups[] = [
                        'title' => (count($pending_mhs) > 0) ? '<i class="fa fa-users" style="color: #6B7280; margin-right: 6px;"></i> Mahasiswa Lainnya' : '',
                        'data' => $other_mhs,
                        'grid_class' => 'student-grid-5',
                        'card_class' => 'student-card-small'
                    ];
                }

                if (count($lulus_mhs) > 0) {
                    $groups[] = [
                        'title' => '<i class="fa fa-graduation-cap" style="color: #10B981; margin-right: 6px;"></i> Sudah Lulus Sidang',
                        'data' => $lulus_mhs,
                        'grid_class' => 'student-grid-6',
                        'card_class' => 'student-card-xsmall'
                    ];
                }
            ?>
            
            <?php foreach($groups as $group): ?>
                <?php if (!empty($group['title'])): ?>
                    <h4 style="font-weight: 800; color: #374151; margin-top: <?= ($group === $groups[0]) ? '0' : '30px' ?>; margin-bottom: 18px; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px; font-size: 16px;">
                        <?= $group['title'] ?> (<?= count($group['data']) ?>)
                    </h4>
                <?php endif; ?>
                
                <div class="<?= $group['grid_class'] ?>">
                    <?php foreach($group['data'] as $m): ?>
                    <?php 
                        $has_never_bimbingan = ($m->total_bimbingan == 0 || empty($m->last_bimbingan_date));
                        $is_unstarted = $has_never_bimbingan && ((floatval($m->academic_health_score) == 0 && intval($m->current_progress) == 0) || $m->kategori_health == 'Belum Ada Bimbingan' || empty($m->kategori_health)) && $m->kategori_health != 'Risiko Tinggi';
                        
                        $alert = null;
                        $now = time();
                        $is_lulus = (isset($m->hasil_sidang) && $m->hasil_sidang == 'LULUS');
                        
                        if ($has_never_bimbingan && !empty($m->tanggal_disetujui) && !$is_lulus) {
                            $waktu_setuju = strtotime($m->tanggal_disetujui);
                            $diff_days = floor(($now - $waktu_setuju) / (60 * 60 * 24));
                            $tgl_str = date('d M Y', $waktu_setuju);
                            
                            if ($diff_days >= 365) {
                                $alert = ['icon' => '<i class="fa fa-calendar-times-o"></i>', 'text' => "Sudah lebih dari 1 tahun sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FFE4E6', 'text_c' => '#E11D48', 'border' => '#FDA4AF'];
                            } elseif ($diff_days >= 180) {
                                $alert = ['icon' => '<i class="fa fa-calendar-times-o"></i>', 'text' => "Sudah lebih dari 6 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FEE2E2', 'text_c' => '#B91C1C', 'border' => '#F87171'];
                            } elseif ($diff_days >= 90) {
                                $alert = ['icon' => '<i class="fa fa-calendar-minus-o"></i>', 'text' => "Sudah lebih dari 3 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FFEDD5', 'text_c' => '#C2410C', 'border' => '#FDBA74'];
                            } elseif ($diff_days >= 30) {
                                $alert = ['icon' => '<i class="fa fa-calendar-times-o"></i>', 'text' => "Sudah lebih dari 1 bulan sejak judul disetujui ($tgl_str), mahasiswa belum mulai.", 'bg' => '#FEF9C3', 'text_c' => '#A16207', 'border' => '#FDE047'];
                            }
                        } elseif (!$has_never_bimbingan && !empty($m->last_bimbingan_date)) {
                            $waktu_terakhir = strtotime($m->last_bimbingan_date);
                            $diff_days = floor(($now - $waktu_terakhir) / (60 * 60 * 24));
                            $tgl_str = tgl_indo_short($m->last_bimbingan_date);
                            
                            if ($diff_days >= 180) {
                                $alert = ['icon' => '<i class="fa fa-clock-o"></i>', 'text' => "Bimbingan terhenti lebih dari 6 bulan (terakhir $tgl_str).", 'bg' => '#FFE4E6', 'text_c' => '#E11D48', 'border' => '#FDA4AF'];
                            } elseif ($diff_days >= 90) {
                                $alert = ['icon' => '<i class="fa fa-clock-o"></i>', 'text' => "Bimbingan terhenti lebih dari 3 bulan (terakhir $tgl_str).", 'bg' => '#FEE2E2', 'text_c' => '#B91C1C', 'border' => '#F87171'];
                            } elseif ($diff_days >= 60) {
                                $alert = ['icon' => '<i class="fa fa-clock-o"></i>', 'text' => "Bimbingan terhenti lebih dari 2 bulan (terakhir $tgl_str).", 'bg' => '#FFEDD5', 'text_c' => '#C2410C', 'border' => '#FDBA74'];
                            } elseif ($diff_days >= 21) {
                                $alert = ['icon' => '<i class="fa fa-clock-o"></i>', 'text' => "Sudah lebih dari 21 hari sejak bimbingan terakhir ($tgl_str).", 'bg' => '#FEF9C3', 'text_c' => '#A16207', 'border' => '#FDE047'];
                            } else {
                                $alert = ['icon' => '<i class="fa fa-fire"></i>', 'text' => "Mahasiswa aktif berkonsultasi sebanyak " . $m->total_bimbingan . "x.", 'bg' => '#E6FBF0', 'text_c' => '#087f46', 'border' => '#A7F3D0'];
                            }
                        }
                        
                        if ($is_unstarted) {
                            $badge_class = 'neutral'; $badge_text = '<i class="fa fa-clock-o"></i> Belum Mulai'; $bar_color = '#2a93e2';
                            $status_color = '#2a93e2'; 
                        } else if ($m->kategori_health == 'Perlu Perhatian') {
                            $badge_class = 'warning'; $badge_text = '<i class="fa fa-exclamation-triangle"></i> Perhatian'; $bar_color = '#c78c1a';
                            $status_color = '#c78c1a'; 
                        } else if ($m->kategori_health == 'Risiko Tinggi') {
                            $badge_class = 'danger'; $badge_text = '<i class="fa fa-ban"></i> Risiko Tinggi'; $bar_color = '#c51f42';
                            $status_color = '#c51f42'; 
                        } else {
                            $badge_class = 'safe'; $badge_text = '<i class="fa fa-check-circle"></i> Aman'; $bar_color = '#087f46';
                            $status_color = '#087f46'; 
                        }
                        
                        $is_pending_pendamping = ($m->jumlah_pending == 0 && isset($m->pending_di_pendamping) && $m->pending_di_pendamping > 0);
                        
                        if ($is_lulus) {
                            $status_color = '#1E3A8A';
                            $bar_color = '#1E3A8A';
                        }

                        $nm_pd_esc = htmlspecialchars(addslashes($m->nm_pd));
                        $nipd_esc = htmlspecialchars(addslashes($m->nipd));
                        $photo_full_src = !empty($m->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/' . rawurlencode($m->foto) : base_url('assets/no_profile_photo.png');
                        $judul_ta = !empty($m->judul_ditetapkan) ? $m->judul_ditetapkan : (!empty($m->judul_skripsi_1) ? $m->judul_skripsi_1 : 'Belum Ditetapkan');
                        $judul_ta_esc = htmlspecialchars(addslashes($judul_ta));
                        $is_lulus_js = $is_lulus ? 'true' : 'false';
                    ?>
                    <div class="student-card <?= $group['card_class'] ?> <?= ($m->jumlah_pending > 0 || $is_pending_pendamping || !empty($m->id_agenda_aktif)) ? 'has-pending' : '' ?>" id="card-<?= $m->id_judul_skripsi ?>" <?= ($m->jumlah_pending > 0 || $is_pending_pendamping || !empty($m->id_agenda_aktif)) ? '' : ($m->kategori_health == 'Risiko Tinggi' ? 'style="border-color: #FECACA;"' : '') ?>>
                        <?php if ($is_lulus): ?>
                            <div class="card-header-band" style="background: #FFFFFF; position: relative; padding: 15px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; box-sizing: border-box; border-radius: 12px 12px 0 0; border-bottom: 1px solid #E5E7EB;">
                                <div style="text-align: center; width: 100%;">
                                    <h3 style="font-size: 13px; font-weight: 800; color: #111827; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">
                                        <?= $m->nm_pd ?>
                                    </h3>
                                    <?php 
                                        $role_dosen = 'Utama';
                                        $role_bg = '#EEF2FF';
                                        $role_color = '#4F46E5';
                                        $role_border = '#C7D2FE';
                                        if (!empty($m->dosen_pembimbing_2) && $m->dosen_pembimbing_2 == $this->session->userdata('id_ptk') && $m->dosen_pembimbing_1 != $this->session->userdata('id_ptk')) {
                                            $role_dosen = 'Pendamping';
                                            $role_bg = '#F3F4F6';
                                            $role_color = '#4B5563';
                                            $role_border = '#D1D5DB';
                                        }
                                    ?>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap;">
                                        <span style="font-size: 11px; color: #6B7280; font-weight: 600;"><?= $m->nipd ?></span>
                                        <span style="background: <?= $role_bg ?>; color: <?= $role_color ?>; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; border: 1px solid <?= $role_border ?>;"><?= $role_dosen ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card-header-band" style="background: #FFFFFF; height: 95px; position: relative; padding: 15px; display: flex; align-items: center; gap: 12px; box-sizing: border-box; border-radius: 12px 12px 0 0; border-bottom: 1px solid #E5E7EB;">
                                
                                <div style="flex: 0 0 clamp(40px, 25%, 56px); aspect-ratio: 1/1; border-radius: 50%; overflow: hidden; border: 2px solid <?= $status_color ?>; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: #F3F4F6; padding: 2px;">
                                    <div style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden;">
                                        <?php if (!empty($m->foto)): ?>
                                            <img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_<?= rawurlencode($m->foto) ?>" 
                                                 onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';" 
                                                 onclick="showZoomFotoBimbingan('<?= $photo_full_src ?>', '<?= $nm_pd_esc ?>', '<?= $nipd_esc ?>', '<?= $judul_ta_esc ?>', <?= $is_lulus_js ?>)"
                                                 style="cursor:pointer; width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/no_profile_photo.png') ?>" alt="No Photo" onclick="showZoomFotoBimbingan('<?= $photo_full_src ?>', '<?= $nm_pd_esc ?>', '<?= $nipd_esc ?>', '<?= $judul_ta_esc ?>', <?= $is_lulus_js ?>)" style="cursor:pointer; width: 100%; height: 100%; object-fit: cover;">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; text-align: left; padding-right: 5px;">
                                    <h3 style="font-size: 13px; font-weight: 800; color: #111827; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= $m->nm_pd ?>
                                    </h3>
                                    
                                    <?php 
                                        $role_dosen = 'Utama';
                                        $role_bg = '#EEF2FF';
                                        $role_color = '#4F46E5';
                                        $role_border = '#C7D2FE';
                                        if (!empty($m->dosen_pembimbing_2) && $m->dosen_pembimbing_2 == $this->session->userdata('id_ptk') && $m->dosen_pembimbing_1 != $this->session->userdata('id_ptk')) {
                                            $role_dosen = 'Pendamping';
                                            $role_bg = '#F3F4F6';
                                            $role_color = '#4B5563';
                                            $role_border = '#D1D5DB';
                                        }
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                        <span style="font-size: 11px; color: #6B7280; font-weight: 600;"><?= $m->nipd ?></span>
                                        <span style="background: <?= $role_bg ?>; color: <?= $role_color ?>; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; border: 1px solid <?= $role_border ?>;"><?= $role_dosen ?></span>
                                    </div>
                                    <?php 
                                        $time_rev = !empty($m->tgl_terakhir_review) ? strtotime($m->tgl_terakhir_review) : 0;
                                        $time_cht = !empty($m->tgl_terakhir_chat) ? strtotime($m->tgl_terakhir_chat) : 0;
                                        $time_agd = !empty($m->tgl_terakhir_agenda) ? strtotime($m->tgl_terakhir_agenda) : 0;
                                        $max_t = max($time_rev, $time_cht, $time_agd);
                                        if ($max_t > 0):
                                            $act_text = 'Baru di-review';
                                            if ($max_t == $time_cht) $act_text = 'Baru di-chat';
                                            if ($max_t == $time_agd) $act_text = 'Agenda dibuat';
                                    ?>
                                    <div style="font-size: 10px; color: #8B5CF6; margin-top: 6px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                        <i class="fa fa-clock-o"></i> <?= $act_text ?> <?= time_ago_indo(date('Y-m-d H:i:s', $max_t)) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="card-student-body" style="padding: 15px 20px 20px 20px; text-align: center; flex: 1; display: flex; flex-direction: column;">
                            
                            <?php if ($is_lulus): ?>
                                <div style="display: flex; justify-content: center; margin-top: 5px; margin-bottom: 25px;">
                                    <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; border: 3px solid <?= $status_color ?>; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #F3F4F6; padding: 2px;">
                                        <div style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden;">
                                            <?php if (!empty($m->foto)): ?>
                                                <img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_<?= rawurlencode($m->foto) ?>" 
                                                     onerror="this.onerror=null;this.src='<?= base_url('assets/no_profile_photo.png') ?>';" 
                                                     onclick="showZoomFotoBimbingan('<?= $photo_full_src ?>', '<?= $nm_pd_esc ?>', '<?= $nipd_esc ?>', '<?= $judul_ta_esc ?>', <?= $is_lulus_js ?>)"
                                                     style="cursor:pointer; width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/no_profile_photo.png') ?>" alt="No Photo" onclick="showZoomFotoBimbingan('<?= $photo_full_src ?>', '<?= $nm_pd_esc ?>', '<?= $nipd_esc ?>', '<?= $judul_ta_esc ?>', <?= $is_lulus_js ?>)" style="cursor:pointer; width: 100%; height: 100%; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 15px; padding-bottom: 10px; margin-bottom: 15px;">
                                    <div class="badge <?= $badge_class ?>" style="font-weight: 700; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-size: 11px; padding: 6px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; line-height: 1;"><?= str_replace('</i> ', '</i>', $badge_text) ?></div>
                                    
                                    <?php if ($m->jumlah_pending > 0): ?>
                                        <span class="badge pending-badge" style="font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 0; display: inline-flex; align-items: center; gap: 4px; font-size: 11px; padding: 6px 12px;">
                                            <span class="pending-icon" style="font-size: 11px;"><i class="fa fa-reply"></i></span> Butuh Respon
                                        </span>
                                    <?php elseif ($is_pending_pendamping): ?>
                                        <span class="badge pending-badge" style="font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 0; display: inline-flex; align-items: center; gap: 4px; background: #6366F1; animation: pulse-glow-indigo 2s infinite; font-size: 11px; padding: 6px 12px;">
                                            <span class="pending-icon" style="font-size: 11px;"><i class="fa fa-comments"></i></span> Review Pendamping
                                        </span>
                                    <?php elseif (!empty($m->id_agenda_aktif)): ?>
                                        <span class="badge pending-badge" style="font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 0; display: inline-flex; align-items: center; gap: 4px; background: #4F46E5; animation: pulse-glow-indigo 2s infinite; font-size: 11px; padding: 6px 12px;">
                                            <span class="pending-icon" style="font-size: 11px;"><i class="fa fa-calendar-check-o"></i></span> Bimbingan Terjadwal
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!$has_never_bimbingan): ?>
                            <div style="margin-bottom: 20px; display: flex; justify-content: center; align-items: center; height: 21px;">
                                <span class="badge" style="background:#F3F4F6; color:#4B5563; border:1px solid #D1D5DB; font-weight:700; font-size: 11.5px; padding: 6px 14px; border-radius: 20px; cursor:pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'" onclick="showScoreModal('<?= htmlspecialchars(addslashes($m->nm_pd)) ?>', <?= round($m->academic_health_score) ?>, <?= $m->total_bimbingan ?>, <?= $m->current_progress ?>, '<?= $m->last_bimbingan_date ?>', '<?= isset($m->tanggal_disetujui) ? $m->tanggal_disetujui : '' ?>')">
                                    Score: <?= round($m->academic_health_score) ?> <i class="fa fa-info-circle" style="color:#6B7280; font-size: 13px;"></i>
                                </span>
                            </div>
                            <?php endif; ?>

                            <?php 
                                $show_snippet = ($m->jumlah_pending > 0 && !empty($m->tgl_pending)) || ($is_pending_pendamping && !empty($m->tgl_pending_pendamping));
                                $snippet_date = !empty($m->tgl_pending) ? $m->tgl_pending : (!empty($m->tgl_pending_pendamping) ? $m->tgl_pending_pendamping : $m->last_bimbingan_date);
                                
                                $action_text = "Diposting";
                                $poster_name = $m->nm_pd;
                                
                                if (!empty($m->last_updater_name)) {
                                    $first_name_student = explode(' ', (string)($m->nm_pd ?? ''))[0];
                                    if (stripos($m->last_updater_name, $first_name_student) === false) {
                                        $action_text = "Dioper";
                                        $poster_name = "Dosen Utama (" . $m->last_updater_name . ")";
                                    }
                                }
                            ?>
                            <?php if (!empty($m->id_agenda_aktif)): ?>
                                <?php 
                                    $prioritas_color = '#4F46E5'; $prioritas_bg = '#E0E7FF';
                                    if ($m->agenda_prioritas_aktif == 'Tinggi') { $prioritas_color = '#B91C1C'; $prioritas_bg = '#FEE2E2'; }
                                    elseif ($m->agenda_prioritas_aktif == 'Rendah') { $prioritas_color = '#374151'; $prioritas_bg = '#F3F4F6'; }
                                    
                                    $tgl_mulai_str = !empty($m->agenda_tgl_mulai_aktif) ? tgl_indo_short($m->agenda_tgl_mulai_aktif) : '-';
                                    $jam_mulai_str = !empty($m->agenda_jam_mulai_aktif) ? date('H:i', strtotime($m->agenda_jam_mulai_aktif)) : '-';
                                    $jam_selesai_str = !empty($m->agenda_jam_selesai_aktif) ? date('H:i', strtotime($m->agenda_jam_selesai_aktif)) : '-';
                                    
                                    $deadline_info = '';
                                    if (!empty($m->agenda_tgl_mulai_aktif)) {
                                        $sisa_hari = (strtotime($m->agenda_tgl_mulai_aktif) - strtotime(date('Y-m-d'))) / 86400;
                                        $sisa_label = round($sisa_hari);
                                        if ($sisa_hari < 0) {
                                            $deadline_info = ' <i class="fa fa-exclamation-triangle"></i> <span style="color:#DC2626; font-weight:800;">Lewat deadline!</span>';
                                        } elseif ($sisa_hari == 0) {
                                            $deadline_info = ' <i class="fa fa-clock-o"></i> <span style="color:#D97706; font-weight:800;">Hari ini</span>';
                                        } elseif ($sisa_hari <= 3) {
                                            $deadline_info = ' <i class="fa fa-clock-o"></i> <span style="color:#D97706; font-weight:800;">' . $sisa_label . 'h lagi</span>';
                                        }
                                    }
                                ?>
                                <div style="font-size: 11.5px; color: #3730A3; background: #E0E7FF; padding: 8px 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 6px; border: 1px solid #818CF8; text-align: left;">
                                    <span><i class="fa fa-calendar"></i></span>
                                    <span>Bimbingan Terjadwal Tgl <?= $tgl_mulai_str ?> antara jam <?= $jam_mulai_str ?> dan <?= $jam_selesai_str ?> &bull; <span style="background:<?= $prioritas_bg ?>; color:<?= $prioritas_color ?>; padding:1px 6px; border-radius:4px; font-size:10px; font-weight:800;"><?= $m->agenda_prioritas_aktif ?: 'Sedang' ?></span><?= $deadline_info ?></span>
                                </div>
                            <?php elseif ($show_snippet): ?>
                                <div style="font-size: 11.5px; color: #4F46E5; background: #EEF2FF; padding: 8px 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 6px; border: 1px solid #C7D2FE; text-align: left;">
                                    <span><i class="fa fa-history"></i></span>
                                    <span><?= $action_text ?> oleh <strong><?= $poster_name ?></strong> <?= time_ago($snippet_date) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($alert)): ?>
                                <div style="font-size: 11.5px; color: <?= $alert['text_c'] ?>; background: <?= $alert['bg'] ?>; padding: 12px 14px; border-radius: 8px; margin-top: 10px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: flex-start; gap: 8px; border: 1px solid <?= $alert['border'] ?>; text-align: left; line-height: 1.5; box-shadow: 0 2px 4px <?= $alert['bg'] ?>80;">
                                    <span style="font-size: 16px; margin-top: -1px;"><?= $alert['icon'] ?></span>
                                    <span><?= $alert['text'] ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="progress-section" style="margin-top: auto; margin-bottom: 20px; text-align: left;">
                                <div class="progress-header" style="font-size: 12px; font-weight: 700;">
                                    <span>Progress Keseluruhan</span>
                                    <span style="color: <?= $status_color ?>;"><?= $m->current_progress ?>%</span>
                                </div>
                                <div class="progress-bar-bg" style="margin-bottom: 10px;">
                                    <div class="progress-bar-fill" style="width: <?= $m->current_progress ?>%; background: <?= $bar_color ?>;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); font-weight: 600; flex-wrap: wrap; gap: 8px;">
                                    <span><i class="fa fa-hashtag"></i> Total: <strong><?= $m->total_bimbingan ?>x</strong></span>
                                    <?php if (!$is_lulus): ?>
                                        <?php if ($m->total_bimbingan > 0 && !empty($m->last_bimbingan_date)): ?>
                                            <span><i class="fa fa-bolt"></i> Aktif: <strong><?= tgl_indo_short($m->last_bimbingan_date) ?></strong></span>
                                        <?php else: ?>
                                            <span><i class="fa fa-bed"></i> Belum Aktif</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-actions">
                                <a href="<?= site_url('E_skripsi_bimbingan/detail/'.$m->id_judul_skripsi) ?>" 
                                   class="btn-action primary"
                                   style="background: <?= $status_color ?>; border-radius: 8px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px <?= $status_color ?>30;"
                                   onclick="saveScrollPos(<?= $m->id_judul_skripsi ?>)">
                                    <?= $is_lulus ? 'View Data' : 'Lihat Aktifitas Bimbingan' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <?php if(empty($data_mahasiswa)): ?>
                <div style="grid-column: 1 / -1; text-align:center; padding: 40px; color:#6B7280;">
                    Belum ada mahasiswa bimbingan yang aktif.
                </div>
            <?php endif; ?>

        </div>
