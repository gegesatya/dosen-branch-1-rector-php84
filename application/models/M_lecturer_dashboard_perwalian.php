<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_lecturer_dashboard_perwalian extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_kpi_perwalian($id_ptk, $id_smt)
    {
        // 1. Total Mahasiswa Bimbingan Aktif
        $total_bimbingan = $this->db->from('mahasiswa m')
            ->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
            ->where('m.dosen_pa', $id_ptk)
            ->where('mp.id_jns_keluar', NULL)
            ->count_all_results();

        // Ambil list nipd mahasiswa bimbingan untuk batch query
        $nipds = [];
        if ($total_bimbingan > 0) {
            $this->db->select('mp.nipd');
            $this->db->from('mahasiswa m');
            $this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd');
            $this->db->where('m.dosen_pa', $id_ptk);
            $this->db->where('mp.id_jns_keluar', NULL);
            $res = $this->db->get()->result();
            $nipds = array_column($res, 'nipd');
        }

        // Sudah KRS Semester ini & Pending KRS
        $sudah_krs = 0;
        $pending_krs = 0;
        if (!empty($nipds)) {
            $this->db->select('id_status_krs, COUNT(*) as total');
            $this->db->from('siakad_krs');
            $this->db->where_in('nipd', $nipds);
            $this->db->where('id_smt', $id_smt);
            $this->db->group_by('id_status_krs');
            $status_counts = $this->db->get()->result();
            
            foreach ($status_counts as $row) {
                if ($row->id_status_krs != 1) { // Bukan draft
                    $sudah_krs += $row->total;
                }
                if ($row->id_status_krs == 2) { // Menunggu Persetujuan
                    $pending_krs += $row->total;
                }
            }
        }

        // Rata-rata IPK Bimbingan
        $avg_ipk = 0;
        if (!empty($nipds)) {
            $this->db->select('nipd, ipk');
            $this->db->from('kuliah_mahasiswa');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            $res_ipk = $this->db->get()->result();
            
            $latest_ipks = [];
            foreach ($res_ipk as $row) {
                $latest_ipks[$row->nipd] = (float)$row->ipk;
            }
            if (count($latest_ipks) > 0) {
                $avg_ipk = round(array_sum($latest_ipks) / count($latest_ipks), 2);
            }
        }

        return [
            'total_bimbingan' => $total_bimbingan,
            'sudah_krs' => $sudah_krs,
            'belum_krs' => $total_bimbingan - $sudah_krs,
            'pending_krs' => $pending_krs,
            'persentase_krs' => $total_bimbingan > 0 ? round(($sudah_krs / $total_bimbingan) * 100, 1) : 0,
            'avg_ipk' => $avg_ipk
        ];
    }

    public function get_krs_status_by_angkatan($id_ptk, $id_smt)
    {
        $sql = "SELECT 
                    mp.nipd,
                    CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) AS angkatan_2d
                FROM mahasiswa m
                JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
                WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL";
        
        $mhs = $this->db->query($sql, [$id_ptk])->result();
        
        if (empty($mhs)) return [];
        $nipds = array_column($mhs, 'nipd');

        $krs_status = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, id_status_krs');
            $this->db->from('siakad_krs');
            $this->db->where_in('nipd', $nipds);
            $this->db->where('id_smt', $id_smt);
            $res = $this->db->get()->result();
            foreach ($res as $row) {
                $krs_status[$row->nipd] = $row->id_status_krs;
            }
        }

        $result_map = [];
        foreach ($mhs as $row) {
            $angk = $row->angkatan_2d;
            $result_map[$angk] ??= (object) [
                'angkatan_2d' => $angk,
                'total_mhs' => 0,
                'sudah_krs' => 0,
                'belum_krs' => 0
            ];
            $result_map[$angk]->total_mhs++;
            
            $status = $krs_status[$row->nipd] ?? null;
            if ($status !== null && $status != 1) {
                $result_map[$angk]->sudah_krs++;
            } else {
                $result_map[$angk]->belum_krs++;
            }
        }
        
        $result = array_values($result_map);
        usort($result, fn($a, $b) => $b->angkatan_2d <=> $a->angkatan_2d);

        return $result;
    }

    public function get_student_predictions($id_ptk, $id_smt, $params)
    {
        $sql_mhs = "SELECT 
                        mp.nipd, 
                        m.nm_pd, 
                        m.foto,
                        CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) AS angkatan_2d
                    FROM mahasiswa m
                    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
                    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL
                    AND EXISTS (SELECT 1 FROM siakad_krs krs WHERE krs.nipd = mp.nipd AND krs.id_smt = ?)
                    ORDER BY angkatan_2d DESC";
        $students = $this->db->query($sql_mhs, [$id_ptk, $id_smt])->result();

        if (empty($students)) {
            return [
                'tahun_1' => ['aman' => [], 'berisiko' => []],
                'tahun_2' => ['aman' => [], 'berisiko' => []],
                'tahun_3' => ['aman' => [], 'berisiko' => []],
                'tahun_akhir' => ['aman' => [], 'berisiko' => []]
            ];
        }

        $nipds = array_column($students, 'nipd');
        
        $mk_mengulang = [];
        if (!empty($nipds)) {
            $nipds_str = "'" . implode("','", $nipds) . "'";
            $sql = "SELECT sn.nipd, COUNT(*) as total
                    FROM siakad_nilai_baru sn
                    JOIN feeder_kelas_kuliah kk ON kk.id_kls = sn.id_kls
                    WHERE sn.nipd IN ($nipds_str)
                    AND (sn.nilai_huruf = 'D' OR sn.nilai_huruf = 'E')
                    AND NOT EXISTS (
                        SELECT 1 FROM siakad_nilai_baru sn2 
                        JOIN feeder_kelas_kuliah kk2 ON kk2.id_kls = sn2.id_kls
                        WHERE sn2.nipd = sn.nipd 
                        AND kk2.id_mk = kk.id_mk 
                        AND (sn2.nilai_huruf IN ('A', 'B', 'C', 'A-', 'B+', 'B-', 'C+'))
                    )
                    GROUP BY sn.nipd";
            $results = $this->db->query($sql)->result();
            foreach ($results as $row) {
                $mk_mengulang[$row->nipd] = $row->total;
            }
        }

        $judul_ta = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, COUNT(*) as total');
            $this->db->from('siakad_skripsi_pengajuan_judul');
            $this->db->where_in('nipd', $nipds);
            $this->db->group_by('nipd');
            foreach ($this->db->get()->result() as $row) {
                $judul_ta[$row->nipd] = $row->total;
            }
        }

        $ipk_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, id_smt, ipk, sks_total');
            $this->db->from('kuliah_mahasiswa');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $ipk_history_map[$row->nipd][] = $row;
            }
        }

        $current_year_2d = (int) substr($id_smt, 2, 2);

        $krs_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, id_smt, id_status_krs');
            $this->db->from('siakad_krs');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $krs_history_map[$row->nipd][] = $row;
            }
        }

        $result = [
            'tahun_1' => ['aman' => [], 'berisiko' => []],
            'tahun_2' => ['aman' => [], 'berisiko' => []],
            'tahun_3' => ['aman' => [], 'berisiko' => []],
            'tahun_akhir' => ['aman' => [], 'berisiko' => []]
        ];

        foreach ($students as $std) {
            $std->total_mk_mengulang = $mk_mengulang[$std->nipd] ?? 0;
            $std->jml_judul_ta = $judul_ta[$std->nipd] ?? 0;
            
            $history = $ipk_history_map[$std->nipd] ?? [];
            
            $std->ipk_thn1 = isset($history[1]) ? $history[1]->ipk : (isset($history[0]) ? $history[0]->ipk : null);
            $std->ipk_thn2 = isset($history[3]) ? $history[3]->ipk : (isset($history[2]) ? $history[2]->ipk : null);
            $std->ipk_thn3 = isset($history[5]) ? $history[5]->ipk : (isset($history[4]) ? $history[4]->ipk : null);
            
            $last_idx = count($history) - 1;
            $std->ipk = $last_idx >= 0 ? $history[$last_idx]->ipk : 0;
            $std->ipk_terakhir = $std->ipk; 
            $std->sks_total = $last_idx >= 0 ? $history[$last_idx]->sks_total : 0;

            $tahun_kuliah = ($current_year_2d - $std->angkatan_2d) + 1;
            if ($tahun_kuliah < 1) $tahun_kuliah = 1;
            $std->tahun_kuliah = $tahun_kuliah;

            // Deteksi Anomali Data Akademik (KRS Kelang / Nilai Tidak Masuk)
            $std->anomali_akademik = [];
            $krs_hist = $krs_history_map[$std->nipd] ?? [];
            
            // Loop semua KRS yang pernah diajukan
            foreach ($krs_hist as $krs) {
                // Abaikan semester aktif saat ini
                if ($krs->id_smt == $id_smt) continue;
                // Abaikan semester antara/pendek (berakhiran 3)
                if (str_ends_with($krs->id_smt, '3')) continue;

                if ($krs->id_status_krs != 3 && $krs->id_status_krs != 0 && $krs->id_status_krs != 4) {
                    $std->anomali_akademik[] = "KRS Smt " . $krs->id_smt . " berstatus Belum Selesai (Status: " . $krs->id_status_krs . ").";
                } else {
                    $nilai_masuk = array_any($history, fn($hist) => $hist->id_smt == $krs->id_smt);
                    if (!$nilai_masuk) {
                        $std->anomali_akademik[] = "Nilai Smt " . $krs->id_smt . " belum masuk/diproses. KHS mahasiswa tidak ada, silahkan hubungi mahasiswa untuk menghadap ke bagian Akademik.";
                    }
                }
            }

            $tab_key = '';
            $is_berisiko = false;

            if ($tahun_kuliah <= 1) {
                $tab_key = 'tahun_1';
                // Tahun 1 tetap diberi pengecualian > 0 agar maba (mhs baru smt 1) yang belum ujian tidak langsung masuk 'berisiko'
                if ($std->ipk_terakhir > 0 && $std->ipk_terakhir < $params['ipk_tahun_1']) $is_berisiko = true;
            } elseif ($tahun_kuliah == 2) {
                $tab_key = 'tahun_2';
                if ($std->ipk_terakhir < $params['ipk_tahun_2']) $is_berisiko = true;
            } elseif ($tahun_kuliah == 3) {
                $tab_key = 'tahun_3';
                if ($std->ipk_terakhir < $params['ipk_tahun_3']) $is_berisiko = true;
            } else {
                $tab_key = 'tahun_akhir';
                if ($tahun_kuliah > 4 || $std->ipk_terakhir < 2.75) {
                    $is_berisiko = true;
                }
            }

            if ($is_berisiko) {
                $result[$tab_key]['berisiko'][] = $std;
            } else {
                $result[$tab_key]['aman'][] = $std;
            }
        }

        // Urutkan ulang berdasarkan IPK Ascending (Sesuai order awal dari kueri lama)
        foreach ($result as $key => $group) {
            usort($result[$key]['berisiko'], fn($a, $b) => $a->ipk <=> $b->ipk);
            usort($result[$key]['aman'], fn($a, $b) => $a->ipk <=> $b->ipk);
        }

        return $result;
    }

    public function get_prestasi_clusters($id_ptk)
    {
        $sql_mhs = "SELECT 
                        mp.nipd, 
                        m.nm_pd, 
                        m.foto
                    FROM mahasiswa m
                    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
                    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL";
        $students = $this->db->query($sql_mhs, [$id_ptk])->result_array();

        if (empty($students)) return [];
        $nipds = array_column($students, 'nipd');

        $ipk_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, ipk, sks_total');
            $this->db->from('kuliah_mahasiswa');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result_array() as $row) {
                $ipk_history_map[$row['nipd']] = $row; // Timpa dengan data semester terbaru
            }
        }

        $result = [];
        foreach ($students as $std) {
            $last_record = $ipk_history_map[$std['nipd']] ?? ['ipk' => 0, 'sks_total' => 0];
            $std['ipk'] = $last_record['ipk'];
            $std['sks_total'] = $last_record['sks_total'];
            
            if ($std['ipk'] >= 3.50) {
                $std['cluster'] = 'Prestasi Tinggi (Unggul)';
            } elseif ($std['ipk'] >= 2.75) {
                $std['cluster'] = 'Prestasi Menengah (Rata-rata)';
            } else {
                $std['cluster'] = 'Perhatian Khusus (Bawah)';
            }
            $std['x'] = 'IPK: ' . $std['ipk'];
            $result[] = $std;
        }
        
        // Urutkan berdasarkan IPK Descending
        usort($result, fn($a, $b) => $b['ipk'] <=> $a['ipk']);

        return $result;
    }

    public function get_mahasiswa_belum_krs($id_ptk, $id_smt)
    {
        $sql_mhs = "SELECT 
                        mp.nipd, 
                        m.nm_pd, 
                        m.foto
                    FROM mahasiswa m
                    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
                    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL
                    ORDER BY mp.nipd ASC";
        $students = $this->db->query($sql_mhs, [$id_ptk])->result();

        if (empty($students)) return [];
        $nipds = array_column($students, 'nipd');

        $ipk_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, ipk, sks_total, id_smt');
            $this->db->from('kuliah_mahasiswa');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $ipk_history_map[$row->nipd] = $row;
            }
        }

        $krs_history = [];
        $krs_current = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, id_smt, id_status_krs');
            $this->db->from('siakad_krs');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $krs_history[$row->nipd] = $row->id_smt; // Timpa dengan data semester terbaru
                if ($row->id_smt == $id_smt) {
                    $krs_current[$row->nipd] = clone $row;
                }
            }
        }

        $result = [];
        foreach ($students as $std) {
            $last_km = $ipk_history_map[$std->nipd] ?? null;
            $std->ipk = $last_km ? $last_km->ipk : 0;
            $std->sks_total = $last_km ? $last_km->sks_total : 0;
            
            $last_krs_smt = $krs_history[$std->nipd] ?? ($last_km ? $last_km->id_smt : null);
            $std->terakhir_smt = $last_krs_smt;
            
            $current = $krs_current[$std->nipd] ?? null;
            $id_status_krs = $current ? $current->id_status_krs : null;
            
            // Calculate missed semesters
            $std->semester_terlampaui = 1;
            if ($std->terakhir_smt) {
                $last_year = (int)substr($std->terakhir_smt, 0, 4);
                $last_term = (int)substr($std->terakhir_smt, 4, 1);
                $curr_year = (int)substr($id_smt, 0, 4);
                $curr_term = (int)substr($id_smt, 4, 1);
                
                if ($last_term == 3) $last_term = 2;
                if ($curr_term == 3) $curr_term = 2;
                
                $diff = (($curr_year - $last_year) * 2) + ($curr_term - $last_term);
                
                // If the last recorded semester is before the current one, they missed 'diff' semesters.
                // If it's the same, they missed 1 (the current one) because they haven't submitted.
                $std->semester_terlampaui = $diff > 0 ? $diff : 1; 
            } else {
                $angkatan_year = 2000 + (int)substr($std->nipd, 0, 2);
                $curr_year = (int)substr($id_smt, 0, 4);
                $curr_term = (int)substr($id_smt, 4, 1);
                if ($curr_term == 3) $curr_term = 2;
                
                $diff = (($curr_year - $angkatan_year) * 2) + $curr_term; 
                $std->semester_terlampaui = $diff > 0 ? $diff : 1;
            }
            
            if ($id_status_krs === null || $id_status_krs == 1) {
                if (($id_status_krs ?? -1) != 0) {
                    $result[] = $std;
                }
            }
        }
        
        return $result;
    }
    
    public function get_mahasiswa_anomali_krs($id_ptk, $id_smt)
    {
        $sql_mhs = "SELECT 
                        mp.nipd, 
                        m.nm_pd, 
                        m.foto
                    FROM mahasiswa m
                    JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
                    WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL
                    ORDER BY mp.nipd ASC";
        $students = $this->db->query($sql_mhs, [$id_ptk])->result();

        if (empty($students)) return [];
        $nipds = array_column($students, 'nipd');

        $ipk_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, ipk, sks_total, id_smt');
            $this->db->from('kuliah_mahasiswa');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $ipk_history_map[$row->nipd][] = $row;
            }
        }

        $krs_history_map = [];
        if (!empty($nipds)) {
            $this->db->select('nipd, id_smt, id_status_krs');
            $this->db->from('siakad_krs');
            $this->db->where_in('nipd', $nipds);
            $this->db->order_by('id_smt ASC');
            foreach ($this->db->get()->result() as $row) {
                $krs_history_map[$row->nipd][] = $row;
            }
        }

        $result = [];
        foreach ($students as $std) {
            $std->anomali_akademik = [];
            $krs_hist = $krs_history_map[$std->nipd] ?? [];
            $history = $ipk_history_map[$std->nipd] ?? [];
            
            foreach ($krs_hist as $krs) {
                if ($krs->id_smt == $id_smt) continue;
                if (str_ends_with($krs->id_smt, '3')) continue;

                if ($krs->id_status_krs != 3 && $krs->id_status_krs != 0 && $krs->id_status_krs != 4) {
                    $std->anomali_akademik[] = "KRS Smt " . $krs->id_smt . " berstatus Belum Selesai (Status: " . $krs->id_status_krs . ").";
                } else {
                    $nilai_masuk = array_any($history, fn($hist) => $hist->id_smt == $krs->id_smt);
                    if (!$nilai_masuk) {
                        $std->anomali_akademik[] = "Nilai Smt " . $krs->id_smt . " belum masuk/diproses. KHS mahasiswa tidak ada, silahkan hubungi mahasiswa untuk menghadap ke bagian Akademik.";
                    }
                }
            }
            
            if (!empty($std->anomali_akademik)) {
                $result[] = $std;
            }
        }
        
        return $result;
    }
    
    public function get_entrepreneurship_grades($nipd)
    {
        $nipd_safe = $this->db->escape($nipd);
        
        $mk_refs = json_decode(MK_ENTREPRENEURSHIP, true) ?: [];
        $likes = [];
        foreach ($mk_refs as $kode => $keywords) {
            foreach ($keywords as $kw) {
                $likes[] = "mk.nm_mk LIKE '%" . $this->db->escape_like_str($kw) . "%'";
            }
        }
        $like_str = implode(' OR ', $likes);
        
        if (empty($like_str)) {
            $like_str = "1=0"; // Prevent empty query error if constants is missing
        }

        $sql = "SELECT 
                    n.nilai_indeks, 
                    n.nilai_huruf,
                    mk.nm_mk,
                    mk.id_mk
                FROM siakad_nilai_baru n
                JOIN feeder_kelas_kuliah kk ON kk.id_kls = n.id_kls
                JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk
                WHERE n.nipd = $nipd_safe 
                AND ($like_str)
                ORDER BY mk.nm_mk ASC";
                
        $results = $this->db->query($sql)->result_array();
        
        $grades = [];
        foreach ($results as $r) {
            $nm = strtolower($r['nm_mk']);
            $kode_e = '';
            $sort = 99;
            
            $idx = 1;
            foreach ($mk_refs as $kode => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($nm, strtolower($kw))) {
                        $kode_e = $kode;
                        $sort = $idx;
                        break 2;
                    }
                }
                $idx++;
            }
            
            if ($kode_e !== '') {
                if (!isset($grades[$kode_e]) || $r['nilai_indeks'] > $grades[$kode_e]['nilai_indeks']) {
                    $grades[$kode_e] = [
                        'kode_e'       => $kode_e,
                        'nm_mk'        => $r['nm_mk'],
                        'nilai_huruf'  => $r['nilai_huruf'],
                        'nilai_indeks' => (float) $r['nilai_indeks'],
                        'sort'         => $sort
                    ];
                }
            }
        }
        
        usort($grades, fn($a, $b) => $a['sort'] <=> $b['sort']);
        
        return $grades;
    }

    public function get_language_grades($nipd)
    {
        $nipd_safe = $this->db->escape($nipd);
        
        $mk_refs = json_decode(MK_BAHASA_ASING, true) ?: [];
        $likes = [];
        foreach ($mk_refs as $kode => $keywords) {
            foreach ($keywords as $kw) {
                $likes[] = "LOWER(mk.nm_mk) LIKE '%" . $this->db->escape_like_str(strtolower($kw)) . "%'";
            }
        }
        $like_str = implode(' OR ', $likes);
        
        if (empty($like_str)) {
            $like_str = "1=0";
        }

        $sql = "SELECT 
                    n.nilai_indeks, 
                    n.nilai_huruf,
                    mk.nm_mk,
                    mk.id_mk
                FROM siakad_nilai_baru n
                JOIN feeder_kelas_kuliah kk ON kk.id_kls = n.id_kls
                JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk
                WHERE n.nipd = $nipd_safe 
                AND ($like_str)
                ORDER BY n.nilai_indeks DESC";
                
        $results = $this->db->query($sql)->result_array();
        
        $grades = [];
        foreach ($results as $r) {
            $nm = strtolower($r['nm_mk']);
            $kode_lang = '';
            $sort = 99;
            
            $idx = 1;
            foreach ($mk_refs as $kode => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($nm, strtolower($kw))) {
                        $kode_lang = $kode;
                        $sort = $idx;
                        break 2;
                    }
                }
                $idx++;
            }
            
            if ($kode_lang !== '') {
                $id_mk = $r['id_mk'];
                if (!isset($grades[$id_mk]) || $r['nilai_indeks'] > $grades[$id_mk]['nilai_indeks']) {
                    $grades[$id_mk] = [
                        'kode_lang'    => $kode_lang,
                        'nm_mk'        => $r['nm_mk'],
                        'nilai_huruf'  => $r['nilai_huruf'],
                        'nilai_indeks' => (float) $r['nilai_indeks'],
                        'sort'         => $sort
                    ];
                }
            }
        }
        
        usort($grades, fn($a, $b) => $a['sort'] <=> $b['sort']);
        
        return $grades;
    }
}
