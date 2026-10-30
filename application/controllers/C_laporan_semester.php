<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_laporan_semester extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $username   = $this->ion_auth->user()->row()->email;
        $data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');
        $is_kaprodi = $this->ion_auth->in_group([5]);

        if ($data_akses->num_rows() == 0 && !$is_kaprodi) {
            $this->output->set_status_header(403);

            $output['judul_besar']       = 'Akses Ditolak';
            $output['judul_kecil']       = '';
            $output['isi']               = 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses halaman ini.';
            $output['menu_aktif']        = 'm_laporan_semester';
            $output['parent_infografis'] = 'active open';
            $output['content']           = 'forbidden_page/forbidden_page_view';
            $output['after_page']        = 'forbidden_page/forbidden_page_after_page';

            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }

        $this->load->driver('cache', ['adapter' => 'file']);
    }

    public function index()
    {
        $output['judul_besar']       = 'Laporan Semester';
        $output['judul_kecil']       = 'Laporan Kinerja Akademik WBI';
        $output['menu_aktif']        = 'm_laporan_semester';
        $output['parent_infografis'] = 'active open';
        $output['content']           = 'laporan_semester/laporan_semester_view';
        $output['after_page']        = 'laporan_semester/laporan_semester_after_page';

        $this->load->view('master_page', $output);
    }

    // ── Generate Report (iframe target) ──────────────────────────────────────
    public function generate()
    {
        $id_smt = $this->input->get('id_smt');

        // Load semester list
        $sql_smt = "SELECT id_smt, nm_smt, smt, id_thn_ajaran
                    FROM semester
                    WHERE id_smt >= 20231
                    ORDER BY id_smt DESC";
        $smt_list = $this->db->query($sql_smt)->result();

        // Determine active semester
        $active_smt = null;
        if (!empty($id_smt)) {
            foreach ($smt_list as $s) {
                if ($s->id_smt == $id_smt) {
                    $active_smt = $s;
                    break;
                }
            }
        }
        if (!$active_smt && !empty($smt_list)) {
            $active_smt = $smt_list[0];
            $id_smt = $active_smt->id_smt;
        }

        // If no semester selected, pass empty data
        if (!$active_smt) {
            $data['smt_list']   = $smt_list;
            $data['active_smt'] = null;
            $data['id_smt']     = null;
            $this->load->view('laporan_semester/laporan_semester_generate', $data);
            return;
        }

        // ── Load all prodi ────────────────────────────────────────────────────
        $prodi_list = $this->db->query("SELECT id_sms, nm_lemb FROM sms ORDER BY nm_lemb")->result();

        // ── EPOM data per kelas for this semester ─────────────────────────────
        $id_smt_safe = (int)$id_smt;
        $sql_epom = "
            SELECT
                re.id_kls,
                re.nilai_epom,
                re.id_ptk,
                fk.nm_kls,
                fk.id_sms,
                IFNULL(mk.kode_mk, fk.kode_mk_1) as kode_mk,
                IFNULL(mk.nm_mk, fk.nm_mk_1)     as nm_mk,
                fk.sks_mk,
                sm.nm_lemb   as prodi,
                d.nm_ptk     as dosen,
                d.id_ikatan_kerja
            FROM siakad_rekap_epom re
            JOIN feeder_kelas_kuliah fk ON fk.id_kls = re.id_kls
            JOIN sms sm ON sm.id_sms = fk.id_sms
            LEFT JOIN siakad_dosen d ON d.id_ptk = re.id_ptk
            LEFT JOIN feeder_mata_kuliah mk ON mk.id_mk = fk.id_mk
            WHERE re.id_smt = {$id_smt_safe}
              AND IFNULL(mk.nm_mk, fk.nm_mk_1) NOT LIKE '%KKN%'
              AND IFNULL(mk.nm_mk, fk.nm_mk_1) NOT LIKE '%Magang%'
              AND IFNULL(mk.nm_mk, fk.nm_mk_1) NOT LIKE '%PKL%'
              AND IFNULL(mk.nm_mk, fk.nm_mk_1) NOT LIKE '%Tugas Akhir%'
            ORDER BY sm.nm_lemb, fk.nm_kls
        ";
        $epom_rows = $this->db->query($sql_epom)->result();
        
        // Remove classes where EPOM score is 0 or empty
        $filtered_epom = [];
        foreach ($epom_rows as $row) {
            if ((float)$row->nilai_epom > 0) {
                $filtered_epom[] = $row;
            }
        }
        $epom_rows = $filtered_epom;

        // ── Grades and Students per kelas ─────────────────────────────────────
        $kls_ids = array_column($epom_rows, 'id_kls');
        $grade_map = [];
        $mhs_map = [];

        if (!empty($kls_ids)) {
            $placeholders = implode(',', array_fill(0, count($kls_ids), '?'));
            $sql_data = "
                SELECT id_kls, id_reg_pd, nilai_huruf
                FROM siakad_nilai_baru
                WHERE id_kls IN ({$placeholders})
                  AND nilai_huruf IS NOT NULL
                  AND nilai_huruf != ''
            ";
            $data_rows = $this->db->query($sql_data, $kls_ids)->result();
            foreach ($data_rows as $row) {
                // Map Students
                $mhs_map[$row->id_kls] ??= [];
                $mhs_map[$row->id_kls][] = $row->id_reg_pd;
                
                // Map Grades
                $grade_map[$row->id_kls] ??= [];
                $grade_map[$row->id_kls][$row->nilai_huruf] ??= 0;
                $grade_map[$row->id_kls][$row->nilai_huruf]++;
            }
        }

        // ── Jadwal Kuliah ────────────────────────────────────────────────────
        $jadwal_map = [];
        if (!empty($kls_ids)) {
            $sql_jadwal = "
                SELECT jd.id_kls, h.nama_hari, TIME_FORMAT(j.jam_mulai, '%H:%i') as jam_mulai, TIME_FORMAT(j.jam_selesai, '%H:%i') as jam_selesai, r.nama_ruang
                FROM siakad_jadwal_detail jd
                JOIN siakad_jadwal j ON j.id = jd.id_jadwal
                LEFT JOIN siakad_hari h ON h.uniq = j.id_hari
                LEFT JOIN siakad_ruang r ON r.uniq = j.id_ruang
                WHERE jd.id_kls IN ({$placeholders})
                ORDER BY j.id_hari, j.jam_mulai
            ";
            $jadwal_rows = $this->db->query($sql_jadwal, $kls_ids)->result();
            foreach ($jadwal_rows as $jr) {
                $jadwal_map[$jr->id_kls] ??= [];
                $jadwal_map[$jr->id_kls][] = $jr;
            }
        }

        // ── Build per-prodi structure ─────────────────────────────────────────
        $prodi_data = [];
        foreach ($epom_rows as $row) {
            $pid = $row->id_sms;
            $prodi_data[$pid] ??= [
                'prodi'   => $row->prodi,
                'id_sms'  => $pid,
                'kelas'   => [],
                'epom_vals' => [],
            ];
            $grades = $grade_map[$row->id_kls] ?? [];
            $jadwals = $jadwal_map[$row->id_kls] ?? [];
            $students = $mhs_map[$row->id_kls] ?? [];
            $prodi_data[$pid]['kelas'][] = [
                'id_kls'    => $row->id_kls,
                'nm_kls'    => $row->nm_kls,
                'kode_mk'   => $row->kode_mk,
                'nm_mk'     => $row->nm_mk,
                'dosen'     => $row->dosen,
                'nilai_epom'=> $row->nilai_epom,
                'grades'    => $grades,
                'students'  => $students,
                'jadwals'   => $jadwals,
                'is_tetap'  => ($row->id_ikatan_kerja == 'A') ? 1 : 0,
            ];
            if ($row->nilai_epom > 0) {
                $prodi_data[$pid]['epom_vals'][] = (float)$row->nilai_epom;
            }
        }

        // Compute overall statistics
        $all_epom = [];
        foreach ($prodi_data as &$pd) {
            if (!empty($pd['epom_vals'])) {
                $pd['avg_epom'] = round(array_sum($pd['epom_vals']) / count($pd['epom_vals']), 2);
                $pd['min_epom'] = round(min($pd['epom_vals']), 2);
                $pd['max_epom'] = round(max($pd['epom_vals']), 2);
            } else {
                $pd['avg_epom'] = 0;
                $pd['min_epom'] = 0;
                $pd['max_epom'] = 0;
            }
            $all_epom = array_merge($all_epom, $pd['epom_vals']);
        }
        unset($pd);

        $overall_avg = !empty($all_epom) ? round(array_sum($all_epom) / count($all_epom), 2) : 0;
        $overall_min = !empty($all_epom) ? round(min($all_epom), 2) : 0;
        $overall_max = !empty($all_epom) ? round(max($all_epom), 2) : 0;
        $total_kelas = count($epom_rows);

        // ── Dosen yang mengajar di semester ini ──────────────────────────────
        // Determine which semester numbers exist (from nm_kls first char)
        $smt_type = (int)$active_smt->smt; // 1=Ganjil, 2=Genap
        // Ganjil: sem 1, 3, 5, 7; Genap: sem 2, 4, 6, 8
        $expected_smt_nums = ($smt_type == 1) ? [1, 3, 5, 7] : [2, 4, 6, 8];

        // Get unique dosen per kelas for this semester (by id_ptk in epom)
        $dosen_map = []; // id_sms => array of unique dosen
        foreach ($epom_rows as $row) {
            if (empty($row->dosen)) continue;
            $pid = $row->id_sms;
            $dosen_map[$pid] ??= [];
            $key = $row->id_ptk;
            if (!isset($dosen_map[$pid][$key])) {
                $status_dosen = ($row->id_ikatan_kerja == 'A') ? 'Tetap' : (($row->id_ikatan_kerja == 'G') ? 'Tdk Tetap' : 'Lainnya');
                $dosen_map[$pid][$key] = [
                    'nm_ptk'       => $row->dosen,
                    'kelas_count'  => 0,
                    'status_dosen' => $status_dosen,
                    'total_sks'    => 0,
                    'epom_vals'    => [],
                    'is_tetap'     => ($row->id_ikatan_kerja == 'A') ? 1 : 0,
                    'total_e'      => 0
                ];
            }
            $dosen_map[$pid][$key]['kelas_count']++;
            $dosen_map[$pid][$key]['total_sks'] += (float)$row->sks_mk;
            if ((float)$row->nilai_epom > 0) {
                $dosen_map[$pid][$key]['epom_vals'][] = (float)$row->nilai_epom;
            }
            if (isset($grade_map[$row->id_kls]['E'])) {
                $dosen_map[$pid][$key]['total_e'] += $grade_map[$row->id_kls]['E'];
            }
        }

        // Calculate average EPOM per dosen and sort
        foreach ($dosen_map as $pid => &$dosens) {
            foreach ($dosens as &$di) {
                $di['avg_epom'] = !empty($di['epom_vals']) ? round(array_sum($di['epom_vals'])/count($di['epom_vals']), 2) : 0;
            }
            unset($di);
            
            // Sort by is_tetap DESC, then nm_ptk ASC
            uasort($dosens, function($a, $b) {
                if ($a['is_tetap'] != $b['is_tetap']) {
                    return $b['is_tetap'] <=> $a['is_tetap']; // 1 (Tetap) before 0
                }
                return strcmp($a['nm_ptk'], $b['nm_ptk']); // A-Z
            });
        }
        unset($dosens);

        $data['smt_list']       = $smt_list;
        $data['active_smt']     = $active_smt;
        $data['id_smt']         = $id_smt;
        $data['prodi_data']     = $prodi_data;
        $data['dosen_map']      = $dosen_map;
        $data['overall_avg']    = $overall_avg;
        $data['overall_min']    = $overall_min;
        $data['overall_max']    = $overall_max;
        $data['total_kelas']    = $total_kelas;
        $data['total_prodi']    = count($prodi_data);
        $data['expected_smt_nums'] = $expected_smt_nums;

        $this->load->view('laporan_semester/laporan_semester_generate', $data);
    }

    // ── Student Detail Nilai (Popup/New Tab) ──────────────────────────────
    public function detail_nilai($id_kls)
    {
        if (empty($id_kls)) {
            show_404();
        }

        // Get class info
        $sql_kls = "
            SELECT k.nm_kls, mk.nm_mk, mk.kode_mk, sm.nm_lemb as prodi, d.nm_ptk as dosen, re.nilai_epom
            FROM feeder_kelas_kuliah k
            JOIN feeder_mata_kuliah mk ON mk.id_mk = k.id_mk
            JOIN sms sm ON sm.id_sms = k.id_sms
            LEFT JOIN siakad_rekap_epom re ON re.id_kls = k.id_kls
            LEFT JOIN siakad_dosen d ON d.id_ptk = re.id_ptk
            WHERE k.id_kls = ?
            LIMIT 1
        ";
        $kelas = $this->db->query($sql_kls, [$id_kls])->row();
        
        if (!$kelas) {
            show_404();
        }

        // Get Schedule
        $sql_jadwal = "
            SELECT h.nama_hari, TIME_FORMAT(j.jam_mulai, '%H:%i') as jam_mulai, TIME_FORMAT(j.jam_selesai, '%H:%i') as jam_selesai, r.nama_ruang
            FROM siakad_jadwal_detail jd
            JOIN siakad_jadwal j ON j.id = jd.id_jadwal
            LEFT JOIN siakad_hari h ON h.uniq = j.id_hari
            LEFT JOIN siakad_ruang r ON r.uniq = j.id_ruang
            WHERE jd.id_kls = ?
        ";
        $jadwal = $this->db->query($sql_jadwal, [$id_kls])->result();

        // Get Students & Grades
        $sql_nilai = "
            SELECT n.id_reg_pd, mp.nipd, m.nm_pd, n.nilai_angka, n.nilai_huruf, n.nilai_indeks
            FROM siakad_nilai_baru n
            JOIN mahasiswa_pt mp ON mp.nipd = n.nipd
            JOIN mahasiswa m ON m.id_pd = mp.id_pd
            WHERE n.id_kls = ?
              AND n.nilai_huruf IS NOT NULL
              AND n.nilai_huruf != ''
            GROUP BY mp.nipd
            ORDER BY m.nm_pd ASC
        ";
        $nilai = $this->db->query($sql_nilai, [$id_kls])->result();

        // Calculate Standard Deviation & Mean
        $mean = 0;
        $std_dev = 0;
        $count = count($nilai);

        if ($count > 0) {
            $sum = 0;
            foreach ($nilai as $n) {
                $sum += (float)$n->nilai_angka;
            }
            $mean = $sum / $count;

            $variance_sum = 0;
            foreach ($nilai as $n) {
                $diff = (float)$n->nilai_angka - $mean;
                $variance_sum += ($diff * $diff);
            }
            // Population standard deviation
            $std_dev = sqrt($variance_sum / $count);
        }

        // Attach Z-Score and Calculate Sebaran
        $sebaran = ['A'=>0, 'B+'=>0, 'B'=>0, 'C+'=>0, 'C'=>0, 'D'=>0, 'E'=>0];
        foreach ($nilai as &$n) {
            if ($std_dev > 0) {
                $n->z_score = ((float)$n->nilai_angka - $mean) / $std_dev;
            } else {
                $n->z_score = 0;
            }
            $huruf = strtoupper(trim((string)$n->nilai_huruf));
            if (isset($sebaran[$huruf])) {
                $sebaran[$huruf]++;
            }
        }
        unset($n);

        // Get Kritik & Saran
        $where_ks = "id_kls = " . $this->db->escape($id_kls) . " AND CHAR_LENGTH(kritik_saran) > 3";
        $kritik_saran = $this->db->select('kritik_saran')->where($where_ks)->order_by('RAND()')->get('siakad_evaluasi_dosen')->result();

        $data = [
            'kelas' => $kelas,
            'jadwal' => $jadwal,
            'nilai' => $nilai,
            'mean' => $mean,
            'std_dev' => $std_dev,
            'sebaran' => $sebaran,
            'kritik_saran' => $kritik_saran
        ];

        $this->load->view('laporan_semester/laporan_semester_detail_nilai', $data);
    }
}
