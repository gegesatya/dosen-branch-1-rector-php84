<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_wall extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load driver cache dengan prioritas Redis, fallback ke file cache
        $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->load->model('M_wall_model', 'wall_model');
    }

    // =========================================================================
    // MAIN PAGE — Hanya render KPI (ringan), sisa data di-load via AJAX
    // =========================================================================

    public function index()
    {
        $id_ptk    = $this->session->userdata('id_ptk');
        $id_smt    = $this->session->userdata('tahun_ajaran');
        $today     = date('Y-m-d');
        $next_week = date('Y-m-d', strtotime('+7 days'));

        $cache_name = "wall_kpi_{$id_ptk}_{$id_smt}_{$today}";
        $kpi = $this->cache->get($cache_name);

        if ($kpi === FALSE) {
            $krs_counts      = $this->wall_model->get_krs_counts($id_ptk, $id_smt);
            $upcoming_sidang = $this->wall_model->get_upcoming_sidang($id_ptk, $today, $next_week);

            $is_pembimbing_pkl = $this->wall_model->is_pembimbing_pkl($id_ptk);
            $pkl_butuh_review = 0;
            if ($is_pembimbing_pkl) {
                $pkl_butuh_review = $this->wall_model->get_pkl_butuh_review($id_ptk);
            }

            $kpi = [
                'total_sks'    => $this->wall_model->get_total_sks($id_ptk, $id_smt),
                'total_ta'     => $this->wall_model->get_total_ta_aktif($id_ptk),
                'krs_pending'  => $krs_counts->pending,
                'krs_final'    => $krs_counts->final,
                'sidang_count' => count($upcoming_sidang),
                'upcoming'     => $upcoming_sidang,
                'is_pembimbing_pkl' => $is_pembimbing_pkl,
                'pkl_butuh_review'  => $pkl_butuh_review,
            ];
            // KPI cache: 30 menit cukup, data berubah saat ada aktivitas
            $this->cache->save($cache_name, $kpi, 1800);
        }

        $output = array_merge($kpi, [
            'judul_besar' => 'Academic Wall &amp; Dashboard',
            'judul_kecil' => 'Linimasa &amp; Kinerja Mahasiswa Perwalian',
            'menu_aktif'  => 'm_wall',
            'content'     => 'wall/wall_view',
        ]);

        $this->load->view('master_page', $output);
    }

    // =========================================================================
    // AJAX PHASE 1 — Chart & Jadwal Hari Ini (above the fold, ~250ms delay)
    // =========================================================================

    public function ajax_load_panel_top()
    {
        if (!$this->ion_auth->logged_in()) { echo json_encode(['error' => 'Unauthorized']); return; }

        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');
        $today  = date('Y-m-d');

        $cache_key = "wall_panel_top_{$id_ptk}_{$id_smt}_{$today}";
        $cached = $this->cache->get($cache_key);

        if ($cached !== FALSE) {
            header('Content-Type: application/json');
            echo json_encode($cached);
            return;
        }

        $today_schedules = $this->_build_today_schedules($id_ptk, $id_smt, $today);
        $chart_data      = $this->wall_model->get_chart_data($id_smt, $id_ptk);

        $result = [
            'schedules'  => $today_schedules,
            'chart'      => $chart_data,
        ];

        $this->cache->save($cache_key, $result, 3600);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // =========================================================================
    // AJAX PHASE 2 — Early Warning + Progress TA (~500ms delay)
    // =========================================================================

    public function ajax_load_panel_middle()
    {
        if (!$this->ion_auth->logged_in()) { echo json_encode(['error' => 'Unauthorized']); return; }

        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');
        $today  = date('Y-m-d');

        $cache_key = "wall_panel_mid_{$id_ptk}_{$id_smt}_{$today}";
        $cached = $this->cache->get($cache_key);

        if ($cached !== FALSE) {
            header('Content-Type: application/json');
            echo json_encode($cached);
            return;
        }

        // --- Early Warning: Tiered Academic (single DB query, split in PHP) ---
        $ew_raw = $this->wall_model->get_early_warning_akademik($id_ptk, $id_smt);

        $current_year = (int) date('Y');
        // 2-digit angkatan thresholds
        $yr1 = (int) substr(strval($current_year - 1), 2, 2); // e.g. 25
        $yr2 = (int) substr(strval($current_year - 2), 2, 2); // e.g. 24
        $yr3 = (int) substr(strval($current_year - 3), 2, 2); // e.g. 23

        $ew_thn1        = []; // Angkatan thn ini-1, IPK < 2.75
        $ew_thn2        = []; // Angkatan thn ini-2, IPK < 2.75, sem 3-4
        $ew_thn3        = []; // Angkatan thn ini-3, IPK < 2.75, sem 5-6
        $ew_ips_drop    = []; // IPS drop >= 0.2 AND IPK ikut turun (all angkatan, max 6 sem)
        $ew_belum_krs   = []; // Belum isi KRS (id_status_krs IS NULL)

        foreach ($ew_raw as $s) {
            $ang   = (int) $s->angkatan_2d;
            $total = (int) ($s->total_smt ?? 0);
            $ipk   = $s->ipk_curr !== null ? (float) $s->ipk_curr : null;
            $ipk_p = $s->ipk_prev !== null ? (float) $s->ipk_prev : null;
            $ips   = $s->ips_curr !== null ? (float) $s->ips_curr : null;
            $ips_p = $s->ips_prev !== null ? (float) $s->ips_prev : null;

            // --- Tier: Tahun 1 ---
            if ($ang === $yr1 && $ipk !== null && $ipk < 2.75) {
                $ew_thn1[] = $s;
            }

            // --- Tier: Tahun 2 (semester 3-4) ---
            if ($ang === $yr2 && $total >= 3 && $total <= 4 && $ipk !== null && $ipk < 2.75) {
                $ew_thn2[] = $s;
            }

            // --- Tier: Tahun 3 (semester 5-6) ---
            if ($ang === $yr3 && $total >= 5 && $total <= 6 && $ipk !== null && $ipk < 2.75) {
                $ew_thn3[] = $s;
            }

            // --- IPS Drop Ekstrim (semua angkatan <= 6 sem) ---
            // Syarat: selisih IPS >= 0.2 DAN IPK juga turun
            if ($total <= 6 && $ips !== null && $ips_p !== null && $ipk !== null && $ipk_p !== null) {
                $ips_drop = $ips_p - $ips; // positive = turun
                if ($ips_drop >= 0.2 && $ipk < $ipk_p) {
                    $ew_ips_drop[] = $s;
                }
            }

            // --- Belum Daftar KRS ---
            if ($s->id_status_krs === null) {
                $ew_belum_krs[] = $s;
            }
        }

        // --- Early Warning: TA Progress Checkpoints (kept for grouped_progress bar) ---
        $ew_list = $this->wall_model->get_early_warning_ta($id_ptk);
        $no_progress_ta      = [];
        $no_progress_sempro  = [];
        $no_progress_semhas  = [];

        foreach ($ew_list as $s) {
            $lulus        = (int)$s->has_sidang_lulus;
            $semhas_layak = (int)$s->has_semhas_layak;
            $sempro_layak = (int)$s->has_sempro_layak;

            if ($lulus) continue;

            if (!$sempro_layak) {
                $s->tgl_ref = $s->tgl_penetapan;
                $no_progress_ta[] = $s;
            } elseif ($sempro_layak && !$semhas_layak) {
                $s->tgl_ref = $s->tgl_sempro;
                $no_progress_sempro[] = $s;
            } elseif ($semhas_layak) {
                $s->tgl_ref = $s->tgl_semhas;
                $no_progress_semhas[] = $s;
            }
        }

        $this->_enrich_age($no_progress_ta);
        $this->_enrich_age($no_progress_sempro, 'tgl_ref');
        $this->_enrich_age($no_progress_semhas, 'tgl_ref');

        // --- TA Progress Grouped ---
        $grouped_progress = [
            '100' => ['label' => 'Sidang Lulus', 'status' => 'Sidang',    'color' => 'bg-bar-green',  'badge' => 'badge-green',  'students' => []],
            '75'  => ['label' => 'Semhas Layak', 'status' => 'Semhas',    'color' => 'bg-bar-teal',   'badge' => 'badge-teal',   'students' => []],
            '45'  => ['label' => 'Sempro Layak', 'status' => 'Sempro',    'color' => 'bg-bar-orange', 'badge' => 'badge-orange', 'students' => []],
            '20'  => ['label' => 'Belum Sempro', 'status' => 'Penetapan', 'color' => 'bg-bar-red',    'badge' => 'badge-red',    'students' => []],
        ];

        foreach ($this->wall_model->get_ta_progress($id_ptk) as $s) {
            if ((int)$s->has_sidang_lulus)     $prog = 100;
            elseif ((int)$s->has_semhas_layak) $prog = 75;
            elseif ((int)$s->has_sempro_layak) $prog = 45;
            else                               $prog = 20;
            $grouped_progress[(string)$prog]['students'][] = $s;
        }

        $result = [
            // Tiered academic early warnings
            'ew_thn1_list'         => $ew_thn1,
            'ew_thn1_count'        => count($ew_thn1),
            'ew_thn1_year'         => ($current_year - 1),
            'ew_thn2_list'         => $ew_thn2,
            'ew_thn2_count'        => count($ew_thn2),
            'ew_thn2_year'         => ($current_year - 2),
            'ew_thn3_list'         => $ew_thn3,
            'ew_thn3_count'        => count($ew_thn3),
            'ew_thn3_year'         => ($current_year - 3),
            'ew_ips_drop_list'     => $ew_ips_drop,
            'ew_ips_drop_count'    => count($ew_ips_drop),
            'ew_belum_krs_count'   => count($ew_belum_krs),
            'ew_belum_krs_list'    => $ew_belum_krs,
            // TA progress (still needed for grouped_progress bar)
            'no_progress_ta_list'      => $no_progress_ta,
            'no_progress_ta_count'     => count($no_progress_ta),
            'no_progress_sempro_list'  => $no_progress_sempro,
            'no_progress_sempro_count' => count($no_progress_sempro),
            'no_progress_semhas_list'  => $no_progress_semhas,
            'no_progress_semhas_count' => count($no_progress_semhas),
            'grouped_progress'         => $grouped_progress,
        ];

        $this->cache->save($cache_key, $result, 3600);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // =========================================================================
    // AJAX PHASE 3 — Academic Health Insight (~750ms delay)
    // =========================================================================

    public function ajax_load_panel_health()
    {
        if (!$this->ion_auth->logged_in()) { echo json_encode(['error' => 'Unauthorized']); return; }

        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');
        $today  = date('Y-m-d');

        $cache_key = "wall_panel_health_{$id_ptk}_{$id_smt}_{$today}";
        $cached = $this->cache->get($cache_key);

        if ($cached !== FALSE) {
            header('Content-Type: application/json');
            echo json_encode($cached);
            return;
        }

        $health_data = $this->wall_model->get_academic_health($id_ptk, $id_smt);
        $aman_list = []; $perhatian_list = []; $risiko_list = [];

        foreach ($health_data as $h) {

            $curr = (float)$h->ips_curr;
            $prev = (float)$h->ips_prev;

            if ($curr == 0) {
                $aman_list[] = $h;
            } elseif ($curr < 2.0) {
                $risiko_list[] = $h;
            } elseif ($curr < 2.75 || ($prev > 0 && $curr < $prev)) {
                $perhatian_list[] = $h;
            } else {
                $aman_list[] = $h;
            }
        }

        $aman      = count($aman_list);
        $perhatian = count($perhatian_list);
        $risiko    = count($risiko_list);
        $total     = $aman + $perhatian + $risiko;

        $result = [
            'health_pct'     => [
                'aman'      => $total > 0 ? round(($aman / $total) * 100) : 0,
                'perhatian' => $total > 0 ? round(($perhatian / $total) * 100) : 0,
                'risiko'    => $total > 0 ? round(($risiko / $total) * 100) : 0,
            ],
            'health_counts'  => [$aman, $perhatian, $risiko],
            'aman_list'      => $aman_list,
            'perhatian_list' => $perhatian_list,
            'risiko_list'    => $risiko_list,
        ];

        // Health data: cache lebih lama (6 jam), tidak sering berubah
        $this->cache->save($cache_key, $result, 21600);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // =========================================================================
    // AJAX PHASE 4 — Events Feed (real-time, bypass cache)
    // =========================================================================

    public function ajax_load_events()
    {
        $offset    = (int)$this->input->get('offset');
        $last_date = $this->input->get('last_date');
        $id_ptk    = $this->session->userdata('id_ptk');
        $id_smt    = $this->session->userdata('tahun_ajaran');

        // Bypassing cache to ensure real-time status updates
        $events = $this->wall_model->get_events_feed($offset, 10, $id_ptk, $id_smt);
        $html = $this->_render_events_html($events, $last_date);

        echo $html;
    }

    // =========================================================================
    // EXISTING AJAX ENDPOINTS
    // =========================================================================

    public function ajax_detail_mengajar()
    {
        $id_jadwal = $this->input->post('id_jadwal');
        $today     = date('Y-m-d');

        $data = [
            'jadwal'    => $this->wall_model->get_jadwal_detail($id_jadwal),
            'materi'    => $this->wall_model->get_materi_today($id_jadwal, $today),
            'students'  => $this->wall_model->get_students_by_jadwal($id_jadwal),
            'id_jadwal' => $id_jadwal,
        ];

        $this->load->view('wall/ajax_detail_mengajar_view', $data);
    }

    public function ajax_get_chart_data()
    {
        $mode   = $this->input->get('mode');
        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');

        if ($mode === 'last') {
            $year = (int)substr($id_smt, 0, 4);
            $term = substr($id_smt, 4, 1);
            $id_smt = ($term == '2') ? $year . '1' : (($term == '1') ? ($year - 1) . '2' : $year . '2');
        }

        $cache_name = "wall_chart_{$mode}_{$id_ptk}_{$id_smt}";
        $cache_data = $this->cache->get($cache_name);

        if ($cache_data !== FALSE) {
            echo $cache_data;
            return;
        }

        $chart_data = json_encode($this->wall_model->get_chart_data($id_smt, $id_ptk));
        $this->cache->save($cache_name, $chart_data, 7200); // 2 jam
        echo $chart_data;
    }

    public function ajax_load_class_details()
    {
        $id_jadwal = $this->input->get('id_jadwal');
        $offset    = (int)$this->input->get('offset');
        $limit     = 10;

        if (!$id_jadwal) {
            echo json_encode(['status' => false, 'message' => 'Invalid Request']);
            return;
        }

        $today  = date('Y-m-d');
        $rps    = $this->wall_model->get_rps_today($id_jadwal, $today);
        $id_kls = $this->wall_model->get_id_kls_by_jadwal($id_jadwal);

        $students = [];
        $has_more = false;

        if ($id_kls) {
            $result   = $this->wall_model->get_students_by_kls($id_kls, $limit + 1, $offset);
            $has_more = count($result) > $limit;
            if ($has_more) array_pop($result);
            $students = $result;
        }

        echo json_encode([
            'status'   => true,
            'rps'      => $rps,
            'students' => $students,
            'has_more' => $has_more,
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Build today's schedule array (teaching + seminars), sorted by time.
     */
    private function _build_today_schedules($id_ptk, $id_smt, $today)
    {
        $schedules   = [];
        $day_of_week = date('N');

        // Teaching
        $rencana = $this->wall_model->get_rencana_pertemuan_today($id_ptk, $today);
        if (!empty($rencana)) {
            $teaching = $rencana;
        } else {
            $semester = $this->wall_model->get_semester($id_smt);
            $teaching = ($semester && $today >= $semester->tgl_mulai && $today <= $semester->tgl_selesai)
                ? $this->wall_model->get_jadwal_by_hari($id_ptk, $id_smt, $day_of_week)
                : [];
        }

        foreach ($teaching as $t) {
            $schedules[] = (object)[
                'type'        => 'Perkuliahan',
                'title'       => 'Mengajar: ' . $t->tag_name,
                'time'        => substr($t->jam_mulai, 0, 5) . ' - ' . substr($t->jam_selesai, 0, 5),
                'location'    => $t->nama_ruang ?: 'R. Kelas',
                'badge_class' => 'badge-blue',
                'id_jadwal'   => $t->id_jadwal,
            ];
        }

        // Seminars
        foreach ($this->wall_model->get_seminars_today($id_ptk, $today) as $s) {
            $kat_map  = [1 => 'Seminar Proposal', 2 => 'Seminar Hasil', 3 => 'Sidang Akhir'];
            $kat_text    = $kat_map[$s->kategori_pengajuan] ?? 'Seminar Proposal';
            $badge_color = ($s->kategori_pengajuan == 3) ? 'badge-red' : 'badge-orange';

            $schedules[] = (object)[
                'type'         => $kat_text,
                'title'        => $kat_text . ': ' . $s->nm_pd . ' (' . $s->nipd . ')',
                'time'         => substr($s->jam, 0, 5),
                'location'     => $s->tempat ?: 'Ruang Sidang',
                'badge_class'  => $badge_color,
                'link_dokumen' => $s->link_dokumen,
                'nm_pd'        => $s->nm_pd,
                'nipd'         => $s->nipd,
            ];
        }

        usort($schedules, fn($a, $b) => strcmp($a->time, $b->time));
        return $schedules;
    }

    /**
     * Add umur_* (age since reference date) to each item in the list.
     */
    private function _enrich_age(array &$list, $date_field = 'tgl_penetapan')
    {
        $now = new DateTime();
        foreach ($list as $mhs) {
            $ref = $mhs->$date_field ?? null;
            if (!empty($ref) && $ref !== '0000-00-00' && $ref !== '0000-00-00 00:00:00') {
                $diff = (new DateTime($ref))->diff($now);
                $mhs->umur_tahun          = $diff->y;
                $mhs->umur_bulan          = $diff->m;
                $mhs->umur_hari           = $diff->d;
                $mhs->tgl_penetapan_format = date('d M Y', strtotime($ref));
            } else {
                $mhs->umur_tahun          = 0;
                $mhs->umur_bulan          = 0;
                $mhs->umur_hari           = 0;
                $mhs->tgl_penetapan_format = '-';
            }
        }
    }

    /**
     * Render HTML for the events feed.
     */
    private function _render_events_html($events, &$last_date_sent)
    {
        $html = '';
        foreach ($events as $event) {
            $initial = mb_substr(strtoupper((string)$event->nm_pd), 0, 1);
            $fallback_html = "<div class=\'custom-feed-avatar\' style=\'width:40px;height:40px;border-radius:50%!important;background:#f1f5f9;color:#0f172a;font-weight:700;display:flex;align-items:center;justify-content:center;float:left;margin-right:12px;font-size:14px;\'>{$initial}</div>";

            if ($event->foto) {
                $avatar = '<img class="custom-feed-pic" src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_'.$event->foto.'" style="width:40px;height:40px;object-fit:cover;border-radius:50%!important;margin-right:12px;float:left;" onerror="this.onerror=null; this.outerHTML=\''.$fallback_html.'\';">';
            } else {
                $avatar = str_replace("\'", "'", $fallback_html);
            }

            $dot_color  = '#2563eb';
            $status_txt = 'KRS Approved';
            $bg_color   = 'transparent';
            if ($event->event_type === 'sidang') {
                $dot_color  = '#dc2626';
                $status_txt = ($event->extra_val == 2) ? 'Semhas' : (($event->extra_val == 3) ? 'Sidang' : 'Sempro');
            } elseif ($event->event_type === 'lulus') {
                $dot_color  = '#16a34a';
                $status_txt = 'Lulus';
            } elseif ($event->event_type === 'bimbingan_ta') {
                $pending_count = (int)$event->total_mk;
                $total_logs    = (int)$event->total_sks;

                if ($pending_count > 0) {
                    $dot_color  = '#d97706';
                    $status_txt = $pending_count . ' Butuh Review';
                    $bg_color   = 'transparent';
                    $body = '<strong>'.$total_logs.' Log Bimbingan TA</strong> telah dikirim. Terdapat <strong>'.$pending_count.' log</strong> yang menunggu ulasan Anda. <a href="'.site_url('E_skripsi_bimbingan').'" style="color:#b45309; font-weight:bold; text-decoration:underline;">Review Sekarang &rarr;</a>';
                } else {
                    $dot_color  = '#15803d';
                    $status_txt = 'Telah Direview';
                    $body = 'Seluruh <strong>'.$total_logs.' Log Bimbingan TA</strong> dari mahasiswa ini telah Anda review.';
                }
            } elseif ($event->event_type === 'bimbingan_pkl') {
                $pending_count = (int)$event->total_mk;
                $total_logs    = (int)$event->total_sks;

                if ($pending_count > 0) {
                    $dot_color  = '#d97706';
                    $status_txt = $pending_count . ' Butuh Review';
                    $bg_color   = 'transparent';
                    $body = '<strong>'.$total_logs.' Laporan PKL</strong> telah dikirim. Terdapat <strong>'.$pending_count.' laporan</strong> yang menunggu ulasan Anda. <a href="'.site_url('C_review_laporan/index/'.$event->nipd).'" style="color:#b45309; font-weight:bold; text-decoration:underline;">Review Sekarang &rarr;</a>';
                } else {
                    $dot_color  = '#15803d';
                    $status_txt = 'Telah Direview';
                    $body = 'Seluruh <strong>'.$total_logs.' Laporan PKL</strong> dari mahasiswa ini telah Anda review.';
                }
            }

            $time_label = date('d M Y', strtotime($event->event_date));

            if ($event->event_type === 'krs') {
                $body = 'Mahasiswa telah menyelesaikan proses perwalian. Pengajuan KRS (<strong>'.$event->total_mk.' MK / '.((int)$event->total_sks).' SKS</strong>) telah <strong>Disetujui</strong> oleh Dosen Wali.';
            } elseif ($event->event_type === 'sidang') {
                $kat = ['Seminar Proposal', 'Seminar Hasil', 'Sidang Akhir'];
                $kat_name = $kat[($event->extra_val - 1)] ?? 'Seminar Proposal';
                $body = 'Mahasiswa ini telah dijadwalkan dan/atau telah mengikuti <strong>'.$kat_name.'</strong>. Terima kasih atas peran serta Anda.';
            } elseif ($event->event_type === 'lulus') {
                $body = 'Selamat! Mahasiswa telah dinyatakan <strong>LULUS</strong> dalam Sidang Tugas Akhir dengan Nilai Akhir: <strong>'.$event->extra_val.'</strong>.';
            }

            $html .= '<div class="custom-feed-item" style="padding:16px 8px; border-bottom:1px solid #f1f5f9; clear:both; overflow:hidden; text-align:left; background:'.$bg_color.'; border-radius: 8px; margin-bottom: 8px;">';
            $html .= '<div class="custom-feed-head" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;text-align:left;">';
            $html .= '<div class="custom-feed-details" style="display:flex;align-items:center;float:left;text-align:left;">';
            $html .= $avatar;
            $html .= '<div style="display:inline-block;text-align:left;">';
            $html .= '<a href="javascript:;" class="custom-feed-name" style="font-weight:700;font-size:14px;color:#2563eb;text-decoration:none;display:inline-block;margin-right:8px;">'.$event->nm_pd.'</a>';
            $html .= '<span class="custom-feed-time" style="font-size:12px;color:#94a3b8;">'.$time_label.'</span>';
            $html .= '</div></div>';
            $html .= '<span class="custom-feed-status" style="font-size:12px;color:#64748b;font-weight:600;display:flex;align-items:center;gap:6px;float:right;margin-top:8px;">';
            $html .= '<span style="width:8px;height:8px;border-radius:50%;display:inline-block;background-color:'.$dot_color.';"></span> '.$status_txt;
            $html .= '</span></div>';
            $html .= '<div class="custom-feed-body" style="padding-left:52px;font-size:13px;color:#475569;line-height:1.6;clear:both;text-align:left;margin-top:4px;">'.$body.'</div>';
            $html .= '</div>';
        }
        return $html;
    }
}
