<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_kinerja extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        date_default_timezone_set("Asia/Jakarta");

        // Simple auth check similar to C_history_ajar
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // Restrict to logged in users and valid groups (Lecturer, Admin, Kaprodi)
        $group = [2, 3, 5, 15];
        if (!$this->ion_auth->in_group($group)) {
            redirect('auth/logout', 'refresh');
        }
        

        $this->load->model('E_kinerja_model');
    }

    public function index()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        
        // Handle year selection
        $current_smt = $this->session->userdata('tahun_ajaran');
        $default_year = substr($current_smt, 0, 4);
        $selected_year = $this->input->get('year') ?: $default_year;

        $data_kinerja = $this->E_kinerja_model->get_kinerja_yearly($id_ptk, $selected_year);

        $output['judul_besar'] = 'Kinerja Dosen (Tahunan)';
        $output['judul_kecil'] = 'Tahun Akademik ' . $selected_year . '/' . ($selected_year + 1);
        $output['menu_aktif'] = 'm_kinerja';
        $output['parent_kinerja'] = 'active open';
        
        $output['data_kinerja'] = $data_kinerja;
        $output['selected_year'] = $selected_year;
        
        // Academic years list
        $output['list_years'] = [
            (object)['year' => '2024', 'label' => '2024/2025'],
            (object)['year' => '2025', 'label' => '2025/2026'],
            (object)['year' => '2026', 'label' => '2026/2027']
        ];

        $output['content'] = 'kinerja/v_kinerja_yearly';
        $this->load->view('master_page', $output);
    }

    /**
     * AJAX function to get performance details
     */
    public function get_performance_json()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->input->post('id_smt');
        
        $data = $this->E_kinerja_model->get_kinerja_summary($id_ptk, $id_smt);
        $total = $this->E_kinerja_model->get_total_kinerja($id_ptk, $id_smt);
        
        echo json_encode([
            'status' => true,
            'data' => $data,
            'total' => $total
        ]);
    }

    /**
     * Sinkronisasi data pembimbing PKL dari simlapkerma_lokasi_magang ke e_kinerja_pendampingan
     */
    public function ajax_sync_pkl()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak. Fitur ini khusus untuk Direktur dan Wakil Direktur 2.']);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $now = date('Y-m-d H:i:s');

        // Ambil semua data bimbingan PKL dari simlapkerma_lokasi_magang beserta deteksi semester dari kelas perkuliahan magang
        $sql = "SELECT lm.*, sd.nm_ptk, COALESCE(m.nm_pd, '-') as nm_pd,
                       COALESCE(NULLIF(lm.id_smt, ''), (
                           SELECT kk.id_smt 
                           FROM siakad_nilai_baru nb
                           JOIN feeder_kelas_kuliah kk ON kk.id_kls = nb.id_kls
                           JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk
                           JOIN siakad_jadwal_detail jd ON jd.id_kls = kk.id_kls
                           JOIN siakad_jadwal j ON j.id = jd.id_jadwal
                           WHERE nb.nipd = lm.nipd AND j.id_kategori_jadwal = 2 
                             AND (LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk.nm_mk) NOT LIKE '%kkn%')
                           ORDER BY kk.id_smt DESC
                           LIMIT 1
                       ), CONCAT(lm.tahun_magang - 1, '2')) as resolved_id_smt
                FROM simlapkerma_lokasi_magang lm
                JOIN siakad_dosen sd ON sd.id_ptk = lm.dosen_pembimbing_lapangan
                LEFT JOIN mahasiswa_pt mp ON mp.nipd = lm.nipd
                LEFT JOIN mahasiswa m ON m.id_pd = mp.id_pd
                WHERE lm.dosen_pembimbing_lapangan IS NOT NULL 
                  AND lm.dosen_pembimbing_lapangan != ''
                ORDER BY resolved_id_smt, sd.nm_ptk";
        $rows = $this->db->query($sql)->result();

        if (empty($rows)) {
            echo json_encode(['status' => false, 'msg' => 'Tidak ada data bimbingan PKL di simlapkerma_lokasi_magang.']);
            return;
        }

        // Kelompokkan per dosen (id_ptk) dan semester (id_smt)
        $grouped = [];
        foreach ($rows as $r) {
            $id_smt = !empty($r->resolved_id_smt) ? $r->resolved_id_smt : ($r->tahun_magang == 2025 ? '20242' : ($r->tahun_magang == 2026 ? '20252' : '20242'));
            $id_ptk = $r->dosen_pembimbing_lapangan;
            $key = $id_ptk . '_' . $id_smt;

            $grouped[$key] ??= [
                'id_ptk' => $id_ptk,
                'id_smt' => $id_smt,
                'mahasiswa' => []
            ];

            $nama_mhs = ($r->nm_pd !== '-' && !empty($r->nm_pd)) ? $r->nm_pd : $r->nipd;
            $nim_key = trim((string)$r->nipd);

            if (!empty($nim_key)) {
                $grouped[$key]['mahasiswa'][$nim_key] = [
                    'nim' => $r->nipd,
                    'nipd' => $r->nipd,
                    'nama' => $nama_mhs,
                    'nm_pd' => $nama_mhs,
                    'perusahaan' => $r->nama_perusahaan,
                    'alamat' => $r->alamat,
                    'nomor_sk' => $r->nomor_sk
                ];
            }
        }

        // Hapus data kategori PKL lama di e_kinerja_pendampingan agar tidak ada duplikasi data semester
        $this->db->where('kategori', 'PKL')->delete('e_kinerja_pendampingan');

        $inserted = 0;

        foreach ($grouped as $g) {
            $id_ptk = $g['id_ptk'];
            $id_smt = $g['id_smt'];
            $mhs_list = array_values($g['mahasiswa']);
            $jumlah = count($mhs_list);
            $mhs_json = json_encode($mhs_list);

            $this->db->insert('e_kinerja_pendampingan', [
                'id_ptk' => $id_ptk,
                'id_smt' => $id_smt,
                'kategori' => 'PKL',
                'jumlah' => $jumlah,
                'approve_by_kaprodi' => 1,
                'mahasiswa_json' => $mhs_json,
                'create_by' => $user_id,
                'create_date' => $now
            ]);
            $inserted++;
        }

        echo json_encode([
            'status' => true,
            'msg' => "Sinkronisasi berhasil! Sebanyak $inserted entri pembimbing PKL berhasil disinkronkan."
        ]);
    }

    /**
     * Hitung dan simpan seluruh rekapitulasi kinerja ke tabel e_kinerja_rekapitulasi
     */
    public function ajax_generate_rekap()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak. Fitur ini khusus untuk Direktur dan Wakil Direktur 2.']);
            return;
        }

        $selected_year = $this->input->post('year') ?: substr($this->session->userdata('tahun_ajaran'), 0, 4);
        $user_id = $this->session->userdata('user_id');
        $now = date('Y-m-d H:i:s');

        $prodi_order = [
            'Manajemen Pemasaran Internasional' => 'MPI',
            'Akuntansi Perpajakan' => 'AP',
            'Agribisnis Hortikultura' => 'AH',
            'Pengelolaan Konvensi dan Acara' => 'PKA',
            'Teknologi Rekayasa Perangkat Lunak' => 'TRPL'
        ];

        $dosen_raw = $this->db->select('sd.id_ptk, sd.nm_ptk, sd.nidn, sd.id_sms, s.nm_lemb as prodi_name, u.id as user_id')
            ->from('siakad_dosen sd')
            ->join('sms s', 's.id_sms = sd.id_sms', 'inner')
            ->join('users u', 'u.id_ptk = sd.id_ptk', 'left')
            ->where('sd.id_ikatan_kerja', 'A')
            ->order_by('FIELD(s.nm_lemb, "Manajemen Pemasaran Internasional", "Akuntansi Perpajakan", "Agribisnis Hortikultura", "Pengelolaan Konvensi dan Acara", "Teknologi Rekayasa Perangkat Lunak"), sd.nm_ptk ASC')
            ->get()->result();

        if (empty($dosen_raw)) {
            echo json_encode(['status' => false, 'msg' => 'Tidak ada data dosen tetap (ikatan kerja A) ditemukan.']);
            return;
        }

        // Hapus rekap lama untuk tahun terkait
        $this->db->where('academic_year', $selected_year)->delete('e_kinerja_rekapitulasi');

        $batch_data = [];
        foreach ($dosen_raw as $d) {
            $prodi_key = $prodi_order[$d->prodi_name] ?? $d->prodi_name;
            $kinerja = $this->E_kinerja_model->get_kinerja_yearly($d->id_ptk, $selected_year, $d->user_id);
            
            $grand_total_ganjil = 0;
            $grand_total_genap = 0;
            $grand_total_kinerja = 0;
            $skripsi_yearly_sum = 0;
            
            $main_kinerja = [];
            $skripsi_items = [];
            foreach ($kinerja as $row) {
                if (in_array($row->ganjil->id_kinerja, [12, 13, 14])) {
                    $skripsi_items[] = $row;
                    $skripsi_yearly_sum += $row->total_poin_yearly;
                } else {
                    $main_kinerja[] = $row;
                }
            }

            $idx = 0;
            foreach ($main_kinerja as $row) {
                $idx++;
                if ($idx <= 5) {
                    $grand_total_ganjil += $row->ganjil->calculated_poin;
                    $grand_total_genap += ($row->genap->calculated_poin ?? 0);
                } else {
                    $grand_total_kinerja += $row->total_poin_yearly;
                }
            }
            
            $teaching_yearly_total = ($grand_total_ganjil + $grand_total_genap) * 16;
            $grand_total_kinerja += $teaching_yearly_total;
            
            $standar_jam = 568;
            $standar_lulusan = 12;
            $persen_sks = $standar_jam > 0 ? ($grand_total_kinerja / $standar_jam) * 100 : 0;
            $persen_lulusan = $standar_lulusan > 0 ? ($skripsi_yearly_sum / $standar_lulusan) * 100 : 0;

            $batch_data[] = [
                'academic_year' => $selected_year,
                'id_ptk' => $d->id_ptk,
                'nm_ptk' => $d->nm_ptk,
                'nidn' => $d->nidn,
                'id_sms' => $d->id_sms,
                'prodi_code' => $prodi_key,
                'prodi_name' => $d->prodi_name,
                'jumlah_jam' => round($grand_total_kinerja, 2),
                'standar_jam' => $standar_jam,
                'persen_sks' => round($persen_sks, 2),
                'jumlah_poin_lulusan' => round($skripsi_yearly_sum, 2),
                'standar_poin_lulusan' => $standar_lulusan,
                'persen_lulusan' => round($persen_lulusan, 2),
                'calculated_at' => $now,
                'calculated_by' => $user_id
            ];
        }

        if (!empty($batch_data)) {
            $this->db->insert_batch('e_kinerja_rekapitulasi', $batch_data);
        }

        echo json_encode([
            'status' => true,
            'msg' => "Rekap data kinerja TA $selected_year/" . ($selected_year + 1) . " berhasil dihitung dan disimpan! Total " . count($batch_data) . " dosen diproses."
        ]);
    }

    public function rekap()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            show_error('Anda tidak memiliki akses ke halaman ini. Halaman ini khusus untuk Direktur dan Wakil Direktur 2.', 403, 'Akses Ditolak');
            return;
        }

        $current_smt = $this->session->userdata('tahun_ajaran');
        $default_year = substr($current_smt, 0, 4);
        $selected_year = $this->input->get('year') ?: $default_year;

        // Ambil data langsung dari tabel precomputed e_kinerja_rekapitulasi (sangat cepat & ringan)
        $rekap_data = $this->db->where('academic_year', $selected_year)
            ->order_by('FIELD(prodi_code, "MPI", "AP", "AH", "PKA", "TRPL"), nm_ptk ASC')
            ->get('e_kinerja_rekapitulasi')->result();

        // Cari info kapan terakhir direkap
        $last_calc = $this->db->select('calculated_at')
            ->where('academic_year', $selected_year)
            ->order_by('id_rekapitulasi', 'desc')
            ->limit(1)
            ->get('e_kinerja_rekapitulasi')->row();
        $last_rekap_time = $last_calc ? $last_calc->calculated_at : null;

        // Kelompokkan per prodi
        $rekap_per_prodi = [];
        foreach ($rekap_data as $d) {
            $prodi_key = $d->prodi_code;
            $rekap_per_prodi[$prodi_key] ??= [
                'prodi_code' => $prodi_key,
                'prodi_name' => $d->prodi_name,
                'dosen' => []
            ];
            // Alias properti agar match dengan view
            $d->teaching_yearly_total = (float)$d->jumlah_jam;
            $d->skripsi_yearly_sum = (float)$d->jumlah_poin_lulusan;
            $d->persen_sks = (float)$d->persen_sks;
            $d->persen_lulusan = (float)$d->persen_lulusan;
            $d->standar_jam = (int)$d->standar_jam;
            $d->standar_lulusan = (int)$d->standar_poin_lulusan;

            $rekap_per_prodi[$prodi_key]['dosen'][] = $d;
        }

        $output['judul_besar'] = 'REKAPITULASI KINERJA DOSEN PWBI';
        $output['judul_kecil'] = 'TA ' . $selected_year . ' / ' . ($selected_year + 1);
        $output['menu_aktif'] = 'm_rekap_kinerja';
        $output['parent_kinerja'] = 'active open';
        
        $output['rekap_per_prodi'] = $rekap_per_prodi;
        $output['selected_year'] = $selected_year;
        $output['last_rekap_time'] = $last_rekap_time;
        $output['has_data'] = !empty($rekap_data);
        $output['list_years'] = [
            (object)['year' => '2023', 'label' => '2023 / 2024'],
            (object)['year' => '2024', 'label' => '2024 / 2025'],
            (object)['year' => '2025', 'label' => '2025 / 2026'],
            (object)['year' => '2026', 'label' => '2026 / 2027']
        ];

        $output['content'] = 'kinerja/v_rekap_kinerja';
        $this->load->view('master_page', $output);
    }

    /**
     * Halaman Direktori BKD Dosen (Tampilan seperti Profil Dosen di SISTER)
     */
    public function bkd_dosen()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            show_error('Anda tidak memiliki akses ke halaman ini. Halaman ini khusus untuk Direktur dan Wakil Direktur 2.', 403, 'Akses Ditolak');
            return;
        }

        $current_smt = $this->session->userdata('tahun_ajaran');
        $default_year = substr($current_smt, 0, 4);
        if (empty($default_year)) {
            $default_year = '2025';
        }

        $output['judul_besar'] = 'BKD Internal';
        $output['judul_kecil'] = 'Evaluasi Beban Kerja Dosen';
        $output['menu_aktif'] = 'm_bkd_dosen';
        $output['parent_kinerja'] = 'active open';
        $output['default_year'] = $default_year;
        $output['list_years'] = [
            (object)['year' => '2023', 'label' => '2023/2024'],
            (object)['year' => '2024', 'label' => '2024/2025'],
            (object)['year' => '2025', 'label' => '2025/2026'],
            (object)['year' => '2026', 'label' => '2026/2027']
        ];

        $output['content'] = 'kinerja/v_bkd_dosen';
        $this->load->view('master_page', $output);
    }

    /**
     * AJAX: Daftar Dosen untuk Grid BKD (Format Profil SISTER)
     */
    public function ajax_daftar_bkd_dosen()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $sql = "
            SELECT 
                sd.id_ptk,
                sd.nm_ptk,
                sd.nidn as sd_nidn,
                sd.nuptk as sd_nuptk,
                sd.photo,
                s.nm_lemb as prodi_name,
                u.id as user_id,
                u.username,
                spd.nm_ptk as sister_nama,
                spd.nidn as sister_nidn,
                spd.photo as sister_foto_url
            FROM siakad_dosen sd
            LEFT JOIN users u ON sd.id_ptk = u.id_ptk
            LEFT JOIN sms s ON s.id_sms = sd.id_sms
            LEFT JOIN sister_profile_dosen spd ON spd.id_sdm COLLATE utf8mb4_unicode_ci = sd.id_ptk_feeder COLLATE utf8mb4_unicode_ci
            WHERE sd.id_ikatan_kerja = 'A'
            ORDER BY COALESCE(spd.nm_ptk, sd.nm_ptk) ASC
        ";
        $raw = $this->db->query($sql)->result_array();

        $dosen_list = [];
        foreach ($raw as $d) {
            $nama = !empty($d['sister_nama']) ? $d['sister_nama'] : $d['nm_ptk'];
            $nidn = !empty($d['sister_nidn']) ? $d['sister_nidn'] : (!empty($d['sd_nidn']) ? $d['sd_nidn'] : (!empty($d['sd_nuptk']) ? $d['sd_nuptk'] : '-'));
            
            $foto_url = null;
            if (!empty($d['sister_foto_url'])) {
                $foto_url = base_url($d['sister_foto_url']);
            } elseif (!empty($d['photo'])) {
                if (str_starts_with($d['photo'], 'assets/') || str_starts_with($d['photo'], 'http')) {
                    $foto_url = base_url($d['photo']);
                } else {
                    $foto_url = base_url('assets/uploads/foto_dosen/' . $d['photo']);
                }
            }

            $dosen_list[] = [
                'id_ptk'         => $d['id_ptk'],
                'user_id'        => $d['user_id'],
                'username'       => $d['username'] ?? '',
                'nama'           => $nama,
                'nidn'           => $nidn,
                'prodi_name'     => $d['prodi_name'] ?? 'Politeknik Wilmar Bisnis Indonesia',
                'foto_url'       => $foto_url
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $dosen_list, 'total' => count($dosen_list)]);
    }

    /**
     * AJAX: Ambil Rincian Tabel BKD Internal Dosen untuk Modal
     */
    public function ajax_modal_bkd_detail()
    {
        if (!$this->_is_direktur_or_wadir2()) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $id_ptk = $this->input->post('id_ptk');
        $year = $this->input->post('year') ?: substr($this->session->userdata('tahun_ajaran'), 0, 4);
        if (empty($year)) $year = '2025';
        $user_id = $this->input->post('user_id');

        if (!$id_ptk) {
            echo json_encode(['status' => 'error', 'message' => 'ID Dosen tidak valid.']);
            return;
        }

        try {
            // Ambil info profil dosen dengan query langsung agar COLLATE tidak di-escape salah oleh query builder
            $sql_dosen = "
                SELECT 
                    sd.id_ptk, sd.nm_ptk, sd.nidn, sd.photo, 
                    s.nm_lemb as prodi_name, 
                    spd.photo as sister_photo, spd.nm_ptk as sister_nama, spd.nidn as sister_nidn, 
                    spd.status_kepegawaian, spd.ikatan_kerja_dosen, spd.gelar_akademik
                FROM siakad_dosen sd
                LEFT JOIN sms s ON s.id_sms = sd.id_sms
                LEFT JOIN sister_profile_dosen spd ON spd.id_sdm COLLATE utf8mb4_unicode_ci = sd.id_ptk_feeder COLLATE utf8mb4_unicode_ci
                WHERE sd.id_ptk = ?
                LIMIT 1
            ";
            $dosen_info = $this->db->query($sql_dosen, [$id_ptk])->row();

            $nama = !empty($dosen_info->sister_nama) ? $dosen_info->sister_nama : ($dosen_info->nm_ptk ?? 'Dosen');
            $nidn = !empty($dosen_info->sister_nidn) ? $dosen_info->sister_nidn : ($dosen_info->nidn ?? '-');
            $foto_url = null;
            if (!empty($dosen_info->sister_photo)) {
                $foto_url = base_url($dosen_info->sister_photo);
            } elseif (!empty($dosen_info->photo)) {
                $foto_url = base_url($dosen_info->photo);
            }

            // Ambil data kinerja tahunan dari model
            $data_kinerja = $this->E_kinerja_model->get_kinerja_yearly($id_ptk, $year, $user_id);

            $data_view = [
                'data_kinerja' => $data_kinerja,
                'selected_year' => $year,
                'dosen' => (object)[
                    'id_ptk' => $id_ptk,
                    'user_id' => $user_id,
                    'nama' => $nama,
                    'nidn' => $nidn,
                    'prodi' => $dosen_info->prodi_name ?? 'Politeknik Wilmar Bisnis Indonesia',
                    'status' => $dosen_info->status_kepegawaian ?? ($dosen_info->ikatan_kerja_dosen ?? 'Dosen Tetap'),
                    'foto_url' => $foto_url
                ]
            ];

            $html_table = $this->load->view('kinerja/partial_bkd_table', $data_view, true);

            echo json_encode([
                'status' => 'success',
                'dosen' => $data_view['dosen'],
                'year' => $year,
                'html' => $html_table
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal memproses data BKD: ' . $e->getMessage()
            ]);
        }
    }

    private function _is_direktur_or_wadir2()
    {
        $user = $this->ion_auth->user()->row();
        if (!$user) return false;
        $user_login_email = $user->email ?? '';
        $user_login_uname = $user->username ?? '';
        
        return ($user_login_email === 'ferawati.zhang@wbi.ac.id'
            || $user_login_uname === 'ferawati.zhang@wbi.ac.id'
            || $user_login_email === 'jenny.elisabeth@wbi.ac.id'
            || $user_login_uname === 'jenny.elisabeth@wbi.ac.id'
            || $this->ion_auth->in_group([6])
            || $this->db->group_start()
                ->where('username', $user_login_email)
                ->or_where('username', $user_login_uname)
                ->group_end()
                ->where_in('roles', ['direktur', 'wadir2'])
                ->get('siakad_akses_data_center')->num_rows() > 0);
    }
}
