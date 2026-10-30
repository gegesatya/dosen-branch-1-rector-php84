<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * M_wall_model
 * Handles all database queries for the Academic Wall dashboard (C_wall controller).
 */
class M_wall_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================================
    // KPI METRICS
    // =========================================================================

    /**
     * Get total SKS mengajar in active semester.
     */
    public function get_total_sks($id_ptk, $id_smt)
    {
        $sql = "SELECT SUM(sk.sks_mk) as total_sks
                FROM feeder_kelas_kuliah sk
                WHERE sk.id_kls IN (
                    SELECT DISTINCT jd.id_kls
                    FROM siakad_jadwal_detail jd
                    JOIN siakad_jadwal j ON j.id = jd.id_jadwal
                    WHERE j.id_sdm = ? AND j.id_smt = ?
                      AND j.id_kategori_jadwal IN (1, 5, 6, 7, 8)
                )";
        $row = $this->db->query($sql, [$id_ptk, $id_smt])->row();
        return !empty($row->total_sks) ? (int)$row->total_sks : 12;
    }

    /**
     * Get count of active TA bimbingan (Pembimbing 1, not graduated, status 3).
     */
    public function get_total_ta_aktif($id_ptk)
    {
        $sql = "SELECT COUNT(pj.id_judul_skripsi) as total
                FROM siakad_skripsi_pengajuan_judul pj
                JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                WHERE pj.dosen_pembimbing_1 = ?
                  AND pj.status_penetapan_judul = 3
                  AND mp.id_jns_keluar IS NULL
                  AND pj.nipd NOT IN (
                      SELECT nipd FROM siakad_skripsi_nilai_sempro WHERE sidang_hasil = 'LULUS'
                  )";
        $row = $this->db->query($sql, [$id_ptk])->row();
        return !empty($row->total) ? (int)$row->total : 0;
    }

    /**
     * Get KRS pending and final counts in one query.
     * Returns object with ->pending and ->final properties.
     */
    public function get_krs_counts($id_ptk, $id_smt)
    {
        $sql = "SELECT
                    SUM(CASE WHEN k.id_status_krs IN (1, 2) THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN k.id_status_krs IN (0, 3) THEN 1 ELSE 0 END) as final_count
                FROM siakad_krs k
                JOIN mahasiswa_pt mp ON mp.nipd = k.nipd
                JOIN mahasiswa m ON m.id_pd = mp.id_pd
                WHERE m.dosen_pa = ? AND k.id_smt = ? AND mp.id_jns_keluar IS NULL";
        $row = $this->db->query($sql, [$id_ptk, $id_smt])->row();
        return (object)[
            'pending' => !empty($row->pending) ? (int)$row->pending : 0,
            'final'   => !empty($row->final_count) ? (int)$row->final_count : 0,
        ];
    }

    /**
     * Get count of PKL students that need review.
     */
    public function get_pkl_butuh_review($id_ptk)
    {
        $sql = "SELECT COUNT(DISTINCT tk.nipd) as total_butuh_review
                FROM simlapkerma_tracking_kegiatan tk
                JOIN simlapkerma_lokasi_magang lm ON lm.nipd = tk.nipd
                JOIN mahasiswa_pt mp ON mp.nipd = tk.nipd
                WHERE lm.dosen_pembimbing_lapangan = ? AND mp.id_jns_keluar IS NULL
                AND (tk.review_dpl IS NULL OR tk.review_dpl = '')";
        $row = $this->db->query($sql, [$id_ptk])->row();
        return !empty($row->total_butuh_review) ? (int)$row->total_butuh_review : 0;
    }

    /**
     * Check if the dosen is a PKL supervisor.
     */
    public function is_pembimbing_pkl($id_ptk)
    {
        $sql = "SELECT COUNT(DISTINCT lm.nipd) as total_pkl
                FROM simlapkerma_lokasi_magang lm
                JOIN mahasiswa_pt mp ON mp.nipd = lm.nipd
                WHERE lm.dosen_pembimbing_lapangan = ? AND mp.id_jns_keluar IS NULL";
        $row = $this->db->query($sql, [$id_ptk])->row();
        return (!empty($row->total_pkl) && (int)$row->total_pkl > 0);
    }

    /**
     * Get upcoming sidang/seminar in the next 7 days.
     */
    public function get_upcoming_sidang($id_ptk, $today, $next_week)
    {
        $sql = "SELECT ss.uniq, pj.nipd, m.nm_pd, ss.tanggal_seminar, ss.jam,
                       ss.tempat, m.foto, ss.kategori_pengajuan, ss.link_dokumen
                FROM siakad_skripsi_seminar_sidang ss
                JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = ss.id_judul_skripsi
                JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                JOIN mahasiswa m ON m.id_pd = mp.id_pd
                WHERE (
                    m.dosen_pa = ? OR pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?
                    OR pj.dosen_penguji_sempro_1 = ? OR pj.dosen_penguji_sempro_2 = ?
                    OR pj.dosen_penguji_sempro_3 = ?
                    OR ss.penguji_sidang_1 = ? OR ss.penguji_sidang_2 = ? OR ss.penguji_sidang_3 = ?
                )
                AND ss.tanggal_seminar BETWEEN ? AND ?
                AND mp.id_jns_keluar IS NULL
                AND pj.nipd NOT IN (
                    SELECT nipd FROM siakad_skripsi_nilai_sempro WHERE sidang_hasil = 'LULUS'
                )
                GROUP BY ss.uniq
                ORDER BY ss.tanggal_seminar ASC";

        $p = $id_ptk;
        return $this->db->query($sql, [$p,$p,$p,$p,$p,$p,$p,$p,$p, $today, $next_week])->result();
    }

    // =========================================================================
    // EARLY WARNINGS (IPS)
    // =========================================================================

    public function get_academic_health($id_ptk, $id_smt)
    {
        $current_year    = (int) substr($id_smt, 0, 4);
        // Minimum angkatan = current year - 3 (e.g. 2025 -> angkatan 2022 -> '22')
        $min_angkatan_2d = (int) substr(strval($current_year - 3), 2, 2);

        $sql = "SELECT mp.nipd, m.nm_pd, m.foto, sub.ips_curr, sub.ips_prev, sub.smt_curr, sub.smt_prev, sub.ipk_curr, sub.sks_curr, k.id_status_krs
                FROM mahasiswa m
                JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd
                LEFT JOIN (
                    SELECT
                        km.nipd,
                        MAX(CASE WHEN km.rnk = 1 THEN km.ips END)    AS ips_curr,
                        MAX(CASE WHEN km.rnk = 2 THEN km.ips END)    AS ips_prev,
                        MAX(CASE WHEN km.rnk = 1 THEN km.id_smt END) AS smt_curr,
                        MAX(CASE WHEN km.rnk = 2 THEN km.id_smt END) AS smt_prev,
                        MAX(CASE WHEN km.rnk = 1 THEN km.ipk END)    AS ipk_curr,
                        MAX(CASE WHEN km.rnk = 1 THEN km.sks_total END) AS sks_curr
                    FROM (
                        SELECT nipd, ips, ipk, sks_total, id_smt,
                               ROW_NUMBER() OVER (PARTITION BY nipd ORDER BY id_smt DESC) AS rnk
                        FROM kuliah_mahasiswa
                    ) km
                    WHERE km.rnk <= 2
                    GROUP BY km.nipd
                ) sub ON sub.nipd = mp.nipd
                LEFT JOIN siakad_krs k ON k.nipd = mp.nipd AND k.id_smt = ?
                WHERE m.dosen_pa = ?
                  AND mp.id_jns_keluar IS NULL
                  AND CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) >= ?
                ORDER BY mp.nipd ASC";
        
        return $this->db->query($sql, [$id_smt, $id_ptk, $min_angkatan_2d])->result();
    }

    /**
     * Get all active students (max 6 semesters) for tiered early warning.
     * Excludes the current active semester (id_smt) because it has no grades yet.
     * rank 1 = most recent COMPLETED semester, rank 2 = one before that.
     * Uses ROW_NUMBER() window function (requires MySQL 8.0+).
     */
    public function get_early_warning_akademik($id_ptk, $id_smt)
    {
        $sql = "SELECT
                    mp.nipd,
                    m.nm_pd,
                    m.foto,
                    CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) AS angkatan_2d,
                    sub.total_smt,
                    sub.ipk_1  AS ipk_curr,
                    sub.ipk_2  AS ipk_prev,
                    sub.ips_1  AS ips_curr,
                    sub.ips_2  AS ips_prev,
                    sub.sks_1  AS sks_curr,
                    sub.id_smt_1 AS smt_curr,
                    sub.id_smt_2 AS smt_prev,
                    k.id_status_krs
                FROM mahasiswa m
                JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd
                LEFT JOIN (
                    SELECT
                        nipd,
                        MAX(total_smt)                                 AS total_smt,
                        MAX(CASE WHEN rnk = 1 THEN ipk  END)           AS ipk_1,
                        MAX(CASE WHEN rnk = 2 THEN ipk  END)           AS ipk_2,
                        MAX(CASE WHEN rnk = 1 THEN ips  END)           AS ips_1,
                        MAX(CASE WHEN rnk = 2 THEN ips  END)           AS ips_2,
                        MAX(CASE WHEN rnk = 1 THEN sks_smt END)        AS sks_1,
                        MAX(CASE WHEN rnk = 1 THEN id_smt END)         AS id_smt_1,
                        MAX(CASE WHEN rnk = 2 THEN id_smt END)         AS id_smt_2
                    FROM (
                        SELECT nipd, ipk, ips, sks_smt, id_smt,
                               ROW_NUMBER() OVER (PARTITION BY nipd ORDER BY id_smt DESC) AS rnk,
                               COUNT(*) OVER (PARTITION BY nipd) AS total_smt
                        FROM kuliah_mahasiswa
                        WHERE id_smt < ?
                    ) ranked
                    WHERE rnk <= 2
                    GROUP BY nipd
                ) sub ON sub.nipd = mp.nipd
                LEFT JOIN siakad_krs k ON k.nipd = mp.nipd AND k.id_smt = ?
                WHERE m.dosen_pa = ?
                  AND mp.id_jns_keluar IS NULL
                ORDER BY mp.nipd ASC";

        return $this->db->query($sql, [$id_smt, $id_smt, $id_ptk])->result();
    }

    // =========================================================================
    // EARLY WARNINGS (TA)
    // =========================================================================

    /**
     * Get all TA bimbingan with checkpoint status in ONE query.
     * Returns students with flags: belum_sempro, sempro_belum_semhas, semhas_belum_sidang.
     * The caller groups them into the 3 early warning categories.
     */
    public function get_early_warning_ta($id_ptk)
    {
        $sql = "SELECT
                    pj.id_judul_skripsi,
                    pj.nipd,
                    m.nm_pd,
                    m.foto,
                    pj.judul_ditetapkan,
                    COALESCE(pj.tanggal_disetujui, pj.tanggal_sk, pj.tanggal_diajukan) AS tgl_penetapan,
                    (SELECT MAX(ss.tanggal_seminar) FROM siakad_skripsi_seminar_sidang ss
                     WHERE ss.id_judul_skripsi = pj.id_judul_skripsi AND ss.kategori_pengajuan = 1
                    ) AS tgl_sempro,
                    (SELECT MAX(ss.tanggal_seminar) FROM siakad_skripsi_seminar_sidang ss
                     WHERE ss.id_judul_skripsi = pj.id_judul_skripsi AND ss.kategori_pengajuan = 2
                    ) AS tgl_semhas,
                    MAX(CASE WHEN ns.sempro_hasil = 'layak' THEN 1 ELSE 0 END) AS has_sempro_layak,
                    MAX(CASE WHEN ns.semhas_hasil = 'layak' THEN 1 ELSE 0 END) AS has_semhas_layak,
                    MAX(CASE WHEN ns.sidang_hasil = 'LULUS'  THEN 1 ELSE 0 END) AS has_sidang_lulus
                FROM siakad_skripsi_pengajuan_judul pj
                JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                JOIN mahasiswa m     ON m.id_pd  = mp.id_pd
                LEFT JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = pj.id_judul_skripsi
                WHERE (pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?)
                  AND pj.status_penetapan_judul = 3
                  AND mp.id_jns_keluar IS NULL
                GROUP BY pj.id_judul_skripsi
                ORDER BY pj.nipd ASC";

        return $this->db->query($sql, [$id_ptk, $id_ptk])->result();
    }

    // =========================================================================
    // TA PROGRESS GROUPED
    // =========================================================================

    /**
     * Get all active TA students with aggregated progress checkpoint flags.
     */
    public function get_ta_progress($id_ptk)
    {
        $sql = "SELECT
                    pj.id_judul_skripsi,
                    pj.nipd,
                    m.nm_pd,
                    pj.judul_ditetapkan,
                    m.foto,
                    MAX(CASE WHEN ns.sempro_hasil = 'layak' THEN 1 ELSE 0 END) AS has_sempro_layak,
                    MAX(CASE WHEN ns.semhas_hasil = 'layak' THEN 1 ELSE 0 END) AS has_semhas_layak,
                    MAX(CASE WHEN ns.sidang_hasil = 'LULUS'  THEN 1 ELSE 0 END) AS has_sidang_lulus,
                    AVG(ns.nilai_akhir)         AS nilai_sempro,
                    AVG(ns.semhas_nilai_akhir)  AS nilai_semhas,
                    AVG(ns.sidang_nilai_akhir)  AS nilai_sidang
                FROM siakad_skripsi_pengajuan_judul pj
                JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                JOIN mahasiswa m     ON m.id_pd  = mp.id_pd
                LEFT JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = pj.id_judul_skripsi
                WHERE (pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?)
                  AND pj.status_penetapan_judul = 3
                  AND mp.id_jns_keluar IS NULL
                GROUP BY pj.id_judul_skripsi";

        return $this->db->query($sql, [$id_ptk, $id_ptk])->result();
    }

    // =========================================================================
    // TODAY'S SCHEDULE
    // =========================================================================

    /**
     * Get today's teaching schedule from siakad_rencana_pertemuan (actual meetings first).
     */
    public function get_rencana_pertemuan_today($id_ptk, $today)
    {
        $sql = "SELECT rp.jam_mulai, rp.jam_selesai, j.tag_name, r.nama_ruang, j.id AS id_jadwal
                FROM siakad_rencana_pertemuan rp
                JOIN siakad_jadwal j ON j.id = rp.id_jadwal
                LEFT JOIN siakad_ruang r ON r.uniq = j.id_ruang
                WHERE rp.tanggal_rencana = ? AND j.id_sdm = ?";
        return $this->db->query($sql, [$today, $id_ptk])->result();
    }

    /**
     * Get teaching schedule from siakad_jadwal by day of week (fallback).
     */
    public function get_jadwal_by_hari($id_ptk, $id_smt, $hari)
    {
        $sql = "SELECT j.id AS id_jadwal, j.jam_mulai, j.jam_selesai, j.tag_name, r.nama_ruang
                FROM siakad_jadwal j
                LEFT JOIN siakad_ruang r ON r.uniq = j.id_ruang
                WHERE j.id_sdm = ? AND j.id_smt = ? AND j.id_hari = ?
                ORDER BY j.jam_mulai ASC";
        return $this->db->query($sql, [$id_ptk, $id_smt, $hari])->result();
    }

    /**
     * Get semester date boundaries.
     */
    public function get_semester($id_smt)
    {
        return $this->db->get_where('semester', ['id_smt' => $id_smt])->row();
    }

    /**
     * Get today's seminars/sidang.
     */
    public function get_seminars_today($id_ptk, $today)
    {
        $sql = "SELECT ss.uniq, pj.nipd, m.nm_pd, ss.tanggal_seminar, ss.jam,
                       ss.tempat, ss.kategori_pengajuan, ss.link_dokumen
                FROM siakad_skripsi_seminar_sidang ss
                JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = ss.id_judul_skripsi
                JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                JOIN mahasiswa m     ON m.id_pd  = mp.id_pd
                WHERE (
                    m.dosen_pa = ? OR pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?
                    OR pj.dosen_penguji_sempro_1 = ? OR pj.dosen_penguji_sempro_2 = ?
                    OR pj.dosen_penguji_sempro_3 = ?
                    OR ss.penguji_sidang_1 = ? OR ss.penguji_sidang_2 = ? OR ss.penguji_sidang_3 = ?
                )
                AND ss.tanggal_seminar = ?
                GROUP BY ss.uniq
                ORDER BY ss.jam ASC";

        $p = $id_ptk;
        return $this->db->query($sql, [$p,$p,$p,$p,$p,$p,$p,$p,$p, $today])->result();
    }

    // =========================================================================
    // EVENTS FEED (AJAX)
    // =========================================================================

    /**
     * Get paginated events feed (UNION: KRS approved, sidang, lulus).
     */
    public function get_events_feed($offset, $limit, $id_ptk, $id_smt)
    {
        $today = date('Y-m-d');
        $sql = "
            (SELECT k.id_rencana_studi AS id, k.nipd, m.nm_pd, k.approved_date AS event_date,
                    m.foto, 'krs' AS event_type, stats.total_sks, stats.total_mk, NULL AS extra_val
             FROM siakad_krs k
             JOIN mahasiswa_pt mp ON mp.nipd = k.nipd
             JOIN mahasiswa m     ON m.id_pd = mp.id_pd
             LEFT JOIN (
                 SELECT kd.id_rencana_studi,
                        SUM(kk.sks_mk)                       AS total_sks,
                        COUNT(kd.id_rencana_studi_detail)    AS total_mk
                 FROM siakad_krs_detail kd
                 JOIN feeder_kelas_kuliah kk ON kd.id_kls = kk.id_kls
                 GROUP BY kd.id_rencana_studi
             ) stats ON stats.id_rencana_studi = k.id_rencana_studi
             WHERE k.id_status_krs = 3 AND k.id_smt = ? AND m.dosen_pa = ?)

            UNION ALL

            (SELECT ns.id_nilai_sempro AS id, ns.nipd, m.nm_pd, ns.sidang_tanggal AS event_date,
                    m.foto, 'lulus' AS event_type, NULL, NULL, ns.sidang_nilai_akhir AS extra_val
             FROM siakad_skripsi_nilai_sempro ns
             JOIN mahasiswa_pt mp ON mp.nipd = ns.nipd
             JOIN mahasiswa m     ON m.id_pd = mp.id_pd
             WHERE ns.sidang_hasil = 'LULUS' AND m.dosen_pa = ?
             GROUP BY ns.nipd)

            UNION ALL

            (SELECT ss.uniq AS id, pj.nipd, m.nm_pd, ss.tanggal_seminar AS event_date,
                    m.foto, 'sidang' AS event_type, NULL, NULL, ss.kategori_pengajuan AS extra_val
             FROM siakad_skripsi_seminar_sidang ss
             JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = ss.id_judul_skripsi
             JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
             JOIN mahasiswa m     ON m.id_pd = mp.id_pd
             WHERE (
                 m.dosen_pa = ? OR pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?
                 OR pj.dosen_penguji_sempro_1 = ? OR pj.dosen_penguji_sempro_2 = ?
                 OR pj.dosen_penguji_sempro_3 = ?
                 OR ss.penguji_sidang_1 = ? OR ss.penguji_sidang_2 = ? OR ss.penguji_sidang_3 = ?
             ) AND ss.tanggal_seminar <= ?)

            UNION ALL

            (SELECT MAX(log.id_log) AS id, pj.nipd, m.nm_pd, MAX(log.tanggal_bimbingan) AS event_date,
                    m.foto, 'bimbingan_ta' AS event_type, 
                    COUNT(log.id_log) AS total_sks, 
                    SUM(CASE WHEN log.status_bimbingan = 'Menunggu Review' THEN 1 ELSE 0 END) AS total_mk,
                    NULL AS extra_val
             FROM siakad_skripsi_bimbingan_log log
             JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = log.id_judul_skripsi
             JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
             JOIN mahasiswa m ON m.id_pd = mp.id_pd
             WHERE pj.dosen_pembimbing_1 = ? AND mp.id_jns_keluar IS NULL
             GROUP BY pj.nipd
             HAVING total_mk > 0)

            UNION ALL

            (SELECT MAX(tk.id_tracking) AS id, tk.nipd, m.nm_pd, MAX(tk.create_date) AS event_date,
                    m.foto, 'bimbingan_pkl' AS event_type, 
                    COUNT(tk.id_tracking) AS total_sks, 
                    SUM(CASE WHEN tk.review_dpl IS NULL OR tk.review_dpl = '' THEN 1 ELSE 0 END) AS total_mk,
                    NULL AS extra_val
             FROM simlapkerma_tracking_kegiatan tk
             JOIN simlapkerma_lokasi_magang lm ON lm.nipd = tk.nipd
             JOIN mahasiswa_pt mp ON mp.nipd = tk.nipd
             JOIN mahasiswa m ON m.id_pd = mp.id_pd
             WHERE lm.dosen_pembimbing_lapangan = ? AND mp.id_jns_keluar IS NULL
             GROUP BY tk.nipd
             HAVING total_mk > 0)

            ORDER BY event_date DESC
            LIMIT ? OFFSET ?
        ";

        $p = $id_ptk;
        return $this->db->query($sql, [
            $id_smt, $p,
            $p,
            $p,$p,$p,$p,$p,$p,$p,$p,$p, $today,
            $p, // for TA
            $p, // for PKL
            $limit, $offset
        ])->result();
    }



    // =========================================================================
    // CHART DATA
    // =========================================================================

    /**
     * Get chart data for M1-M16 weekly activity.
     */
    public function get_chart_data($id_smt, $id_ptk)
    {
        $semester = $this->get_semester($id_smt);
        if (!$semester) {
            return ['labels' => [], 'bimbingan' => [], 'mengajar' => [], 'sidang' => []];
        }

        $tgl_mulai   = $semester->tgl_mulai;
        $tgl_selesai = $semester->tgl_selesai;

        $start_ts = strtotime($tgl_mulai);
        $end_ts   = strtotime($tgl_selesai);

        $total_weeks = ceil(($end_ts - $start_ts) / (7 * 86400));
        if ($total_weeks < 16) $total_weeks = 16;
        if ($total_weeks > 26) $total_weeks = 26;

        $bimbingan_data = array_fill(0, $total_weeks, 0);
        $mengajar_data  = array_fill(0, $total_weeks, 0);
        $sidang_data    = array_fill(0, $total_weeks, 0);

        // 1a. Bimbingan TA (new log)
        $sql_bimb_new = "SELECT log.created_at
                         FROM siakad_skripsi_bimbingan_log log
                         JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = log.id_judul_skripsi
                         WHERE (pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?)
                           AND log.created_at BETWEEN ? AND ?";
        $bimb_new = $this->db->query($sql_bimb_new, [
            $id_ptk, $id_ptk,
            $tgl_mulai . ' 00:00:00',
            $tgl_selesai . ' 23:59:59'
        ])->result();

        // 1b. Bimbingan TA (old log – parse HTML dates)
        $sql_bimb_old = "SELECT log_old.tanggal_bimbingan, log_old.html_table, log_old.html_respon
                         FROM siakad_skripsi_kartu_bimbingan_proposal log_old
                         JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = log_old.id_judul_skripsi
                         WHERE (pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?)";
        $old_logs_raw = $this->db->query($sql_bimb_old, [$id_ptk, $id_ptk])->result();

        $months_indo = [
            'Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','Mei'=>'05','Jun'=>'06',
            'Jul'=>'07','Agt'=>'08','Sep'=>'09','Okt'=>'10','Nov'=>'11','Des'=>'12'
        ];

        $bimb_old = [];
        foreach ($old_logs_raw as $raw) {
            if (!empty($raw->tanggal_bimbingan) &&
                $raw->tanggal_bimbingan >= $tgl_mulai &&
                $raw->tanggal_bimbingan <= $tgl_selesai) {
                $bimb_old[] = (object)['created_at' => $raw->tanggal_bimbingan . ' 00:00:00'];
            }
            $html = $raw->html_table . ' ' . $raw->html_respon;
            if (preg_match_all('/<tr><td>(\d{2} [A-Z][a-z]{2} \d{4})<\/td>/i', $html, $m)) {
                foreach ($m[1] as $ds) {
                    $parts = explode(' ', $ds);
                    if (count($parts) == 3 && isset($months_indo[$parts[1]])) {
                        $fd = $parts[2].'-'.$months_indo[$parts[1]].'-'.$parts[0].' 00:00:00';
                        if ($fd >= $tgl_mulai.' 00:00:00' && $fd <= $tgl_selesai.' 23:59:59') {
                            $bimb_old[] = (object)['created_at' => $fd];
                        }
                    }
                }
            }
        }

        $all_bimb = array_merge($bimb_new, $bimb_old);

        // 2. Mengajar (realisasi)
        $sql_mengajar = "SELECT rp.tanggal_realisasi AS created_at
                         FROM siakad_rencana_pertemuan rp
                         JOIN siakad_jadwal j ON j.id = rp.id_jadwal
                         WHERE j.id_smt = ? AND j.id_sdm = ? AND rp.is_realisasi = 1
                           AND rp.tanggal_realisasi BETWEEN ? AND ?";
        $mengajar_logs = $this->db->query($sql_mengajar, [
            $id_smt, $id_ptk, $tgl_mulai, $tgl_selesai
        ])->result();

        // 3. Sidang dalam range semester
        $sql_sidang = "SELECT ss.tanggal_seminar AS created_at
                       FROM siakad_skripsi_seminar_sidang ss
                       JOIN siakad_skripsi_pengajuan_judul pj ON pj.id_judul_skripsi = ss.id_judul_skripsi
                       JOIN mahasiswa_pt mp ON mp.nipd = pj.nipd
                       JOIN mahasiswa m ON m.id_pd = mp.id_pd
                       WHERE ss.tanggal_seminar BETWEEN ? AND ?
                         AND (
                             m.dosen_pa = ? OR pj.dosen_pembimbing_1 = ? OR pj.dosen_pembimbing_2 = ?
                             OR pj.dosen_penguji_sempro_1 = ? OR pj.dosen_penguji_sempro_2 = ?
                             OR pj.dosen_penguji_sempro_3 = ?
                             OR ss.penguji_sidang_1 = ? OR ss.penguji_sidang_2 = ? OR ss.penguji_sidang_3 = ?
                         )";
        $p = $id_ptk;
        $sidang_logs = $this->db->query($sql_sidang, [
            $tgl_mulai, $tgl_selesai,
            $p,$p,$p,$p,$p,$p,$p,$p,$p
        ])->result();

        // Bucket into weeks
        $this->_bucket_to_weeks($all_bimb,   $bimbingan_data, $start_ts, $total_weeks);
        $this->_bucket_to_weeks($mengajar_logs, $mengajar_data, $start_ts, $total_weeks);
        $this->_bucket_to_weeks($sidang_logs,   $sidang_data,  $start_ts, $total_weeks);

        // Labels
        $labels = [];
        for ($i = 0; $i < $total_weeks; $i++) {
            $wts    = strtotime("+$i weeks", $start_ts);
            $labels[] = ['M' . ($i + 1), date('m/y', $wts)];
        }

        return [
            'labels'    => $labels,
            'bimbingan' => $bimbingan_data,
            'mengajar'  => $mengajar_data,
            'sidang'    => $sidang_data,
        ];
    }

    private function _bucket_to_weeks(array $logs, array &$bucket, $start_ts, $total_weeks)
    {
        foreach ($logs as $log) {
            $diff = floor((strtotime($log->created_at) - $start_ts) / (7 * 86400));
            if ($diff >= 0 && $diff < $total_weeks) {
                $bucket[$diff]++;
            }
        }
    }

    // =========================================================================
    // AJAX – DETAIL MENGAJAR
    // =========================================================================

    public function get_jadwal_detail($id_jadwal)
    {
        $sql = "SELECT j.tag_name, j.jam_mulai, j.jam_selesai, r.nama_ruang, h.nama_hari
                FROM siakad_jadwal j
                LEFT JOIN siakad_ruang r ON r.uniq = j.id_ruang
                LEFT JOIN siakad_hari h  ON h.uniq = j.id_hari
                WHERE j.id = ?";
        return $this->db->query($sql, [$id_jadwal])->row();
    }

    public function get_materi_today($id_jadwal, $today)
    {
        $sql = "SELECT berita_acara_rencana, jam_mulai, jam_selesai, pertemuan
                FROM siakad_rencana_pertemuan
                WHERE id_jadwal = ? AND tanggal_rencana = ?";
        return $this->db->query($sql, [$id_jadwal, $today])->row();
    }

    public function get_students_by_jadwal($id_jadwal)
    {
        $sql = "SELECT m.nm_pd, mp.nipd, m.foto
                FROM siakad_nilai_baru n
                JOIN mahasiswa_pt mp ON mp.nipd = n.nipd
                JOIN mahasiswa m     ON m.id_pd  = mp.id_pd
                WHERE n.id_jadwal = ?
                ORDER BY m.nm_pd ASC";
        return $this->db->query($sql, [$id_jadwal])->result();
    }

    // =========================================================================
    // AJAX – CLASS DETAILS (load more students)
    // =========================================================================

    public function get_rps_today($id_jadwal, $today)
    {
        $sql = "SELECT pertemuan, berita_acara_rencana, tanggal_rencana
                FROM siakad_rencana_pertemuan
                WHERE id_jadwal = ? AND tanggal_rencana = ?";
        return $this->db->query($sql, [$id_jadwal, $today])->row();
    }

    public function get_id_kls_by_jadwal($id_jadwal)
    {
        $sql = "SELECT id_kls FROM siakad_jadwal_detail WHERE id_jadwal = ?";
        $row = $this->db->query($sql, [$id_jadwal])->row();
        return $row ? $row->id_kls : null;
    }

    public function get_students_by_kls($id_kls, $limit, $offset)
    {
        $sql = "SELECT mp.nipd, m.nm_pd, m.foto
                FROM siakad_krs_detail kd
                JOIN siakad_krs k    ON k.id_rencana_studi = kd.id_rencana_studi
                JOIN mahasiswa_pt mp ON mp.nipd = k.nipd
                JOIN mahasiswa m     ON m.id_pd = mp.id_pd
                WHERE kd.id_kls = ?
                ORDER BY m.nm_pd ASC
                LIMIT ? OFFSET ?";
        return $this->db->query($sql, [$id_kls, $limit, $offset])->result();
    }
}
