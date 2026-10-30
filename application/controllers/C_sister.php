<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_sister extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('Auth/login', 'refresh');
        }
    }

    /**
     * Cek apakah user adalah Direktur atau Wakil Direktur 2.
     * Hanya role 'direktur' atau 'wadir2' di siakad_akses_data_center yang diizinkan.
     */
    private function _is_direktur_or_wadir2() {
        $user = $this->ion_auth->user()->row();
        if (!$user) return false;
        $email    = strtolower(trim($user->email ?? ''));
        $username = strtolower(trim($user->username ?? ''));

        // Akun gege.satya selalu diizinkan akses eksekutif / sinkronisasi massal
        if (stripos($email, 'gege.satya') !== false || stripos($username, 'gege.satya') !== false) {
            return true;
        }

        $akses = $this->db->group_start()
            ->where('username', $email)
            ->or_where('username', $username)
            ->group_end()
            ->where_in('roles', ['direktur', 'wadir2'])
            ->get('siakad_akses_data_center');
        return $akses->num_rows() > 0;
    }

    private function _is_gege_satya() {
        $user = $this->ion_auth->user()->row();
        if (!$user) return false;
        $email    = strtolower(trim($user->email ?? ''));
        $username = strtolower(trim($user->username ?? ''));
        $user_id  = (int)($user->id ?? 0);
        return (stripos($email, 'gege.satya') !== false || stripos($username, 'gege.satya') !== false || $user_id === 69 || $user_id === 1);
    }

    private function _cek_akses_direktur_wadir2() {
        if (!$this->_is_direktur_or_wadir2()) {
            $this->output->set_status_header(403);
            $output['judul_besar']   = 'Akses Ditolak';
            $output['judul_kecil']   = '';
            $output['isi']           = 'Halaman ini hanya dapat diakses oleh Direktur dan Wakil Direktur 2.';
            $output['menu_aktif']    = 'm_sister_dashboard';
            $output['parent_sister'] = 'active open';
            $output['content']       = 'forbidden_page/forbidden_page_view';
            $output['after_page']    = 'forbidden_page/forbidden_page_after_page';
            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }
    }

    public function dashboard() {
        $data['menu_aktif'] = 'm_sister_dashboard';
        $data['parent_sister'] = 'active open';
        // Ambil data user yang sedang login
        $user = $this->ion_auth->user()->row();
        
        // Asumsi relasi user dengan dosen
        // Ambil id_sdm dari siakad_dosen menggunakan id_ptk user
        $sd = $this->db->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_dosen = $sd->id_ptk_feeder ?? null; 
        
        $this->db->where('id_dosen', $id_dosen);
        $data['pengajaran'] = $this->db->get('sister_pengajaran')->result();
        
        $this->db->where('id_dosen', $id_dosen);
        $data['penelitian'] = $this->db->get('sister_penelitian')->result();
        
        $this->db->where('id_dosen', $id_dosen);
        $data['pengabdian'] = $this->db->get('sister_pengabdian')->result();

        $data['total_sks'] = array_sum(array_column($data['pengajaran'], 'sks'));
        $data['total_ajar'] = count($data['pengajaran']);
        $data['total_lit'] = count($data['penelitian']);
        $data['total_abdimas'] = count($data['pengabdian']);
        
        $inpassing = $this->db->where('id_sdm', $id_dosen)->order_by('tanggal_sk', 'DESC')->get('sister_inpassing_dosen')->row();
        $data['kum'] = $inpassing ? $inpassing->angka_kredit : 0;

        $data['judul_besar'] = 'Dashboard SISTER';
        $data['judul_kecil'] = 'Pribadi';
        $data['content'] = 'sister/dashboard_pribadi';

        $this->load->view('master_page', $data);
    }

    public function profil_dosen() {
        $this->_cek_akses_direktur_wadir2();
        $data['menu_aktif'] = 'm_sister_profil_dosen';
        $data['parent_sister'] = 'active open';
        $data['judul_besar'] = 'SISTER';
        $data['judul_kecil'] = 'Profil Dosen';
        $data['content'] = 'sister/profil_dosen';
        $this->load->view('master_page', $data);
    }

    /**
     * Profil Pribadi: hanya menampilkan profil dosen yang sedang login.
     * Bisa diakses oleh semua dosen yang login (tidak perlu akses data center).
     */
    public function profil_pribadi() {
        $data['menu_aktif'] = 'm_sister_profil_pribadi';
        $data['parent_sister'] = 'active open';
        $data['judul_besar'] = 'SISTER';
        $data['judul_kecil'] = 'Profil Saya';
        $data['content'] = 'sister/profil_pribadi';
        $this->load->view('master_page', $data);
    }

    public function ajax_daftar_profil_dosen() {
        if (!$this->input->is_ajax_request()) show_404();
        if (!$this->_is_direktur_or_wadir2()) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        // Hanya ambil dosen yang BENAR-BENAR ada di SISTER:
        // yaitu yang punya minimal 1 record di sister_pengajaran, penelitian, atau pengabdian.
        // Dosen dengan id_ptk_feeder tapi tidak ada datanya di SISTER tidak ditampilkan.
        $sql = "
            SELECT DISTINCT
                u.username,
                u.email,
                sd.id_ptk_feeder,
                spd.nm_ptk,
                spd.nidn as sister_nidn,
                spd.nuptk as sister_nuptk,
                sd.nidn as sd_nidn,
                sd.nuptk as sd_nuptk,
                sd.photo,
                spd.photo as sister_foto_url,
                spd.nm_ptk as sister_nama,
                spd.nm_ptk AS nama_display,
                (SELECT COUNT(*) FROM sister_pengajaran sp WHERE sp.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) AS jml_pengajaran,
                (SELECT COUNT(*) FROM sister_penelitian sp WHERE sp.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) AS jml_penelitian,
                (SELECT COUNT(*) FROM sister_pengabdian sp WHERE sp.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) AS jml_pengabdian,
                (SELECT angka_kredit FROM sister_inpassing_dosen si WHERE si.id_sdm COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci ORDER BY tanggal_sk DESC LIMIT 1) AS kum_inpassing
            FROM sister_profile_dosen spd
            JOIN siakad_dosen sd ON spd.id_sdm COLLATE utf8mb4_unicode_ci = sd.id_ptk_feeder COLLATE utf8mb4_unicode_ci
            JOIN users u ON sd.id_ptk COLLATE utf8mb4_unicode_ci = u.id_ptk COLLATE utf8mb4_unicode_ci
            WHERE spd.nm_ptk IS NOT NULL AND spd.nm_ptk != ''
            ORDER BY spd.nm_ptk ASC
        ";
        $raw = $this->db->query($sql)->result_array();

        $dosen_list = [];
        foreach ($raw as $d) {
            $nama = !empty($d['sister_nama']) ? $d['sister_nama'] : (!empty($d['nm_ptk']) ? $d['nm_ptk'] : trim($d['nama_display'] ?? $d['username']));
            
            // Prioritaskan SISTER NIDN, fallback ke Siakad NIDN, fallback ke NUPTK
            $nidn = !empty($d['sister_nidn']) ? $d['sister_nidn'] : (!empty($d['sd_nidn']) ? $d['sd_nidn'] : (!empty($d['sister_nuptk']) ? $d['sister_nuptk'] : (!empty($d['sd_nuptk']) ? $d['sd_nuptk'] : '-')));
            
            // URL foto: prioritas SISTER API lokal, fallback ke photo lokal siakad_dosen
            $foto_url = null;
            if (!empty($d['sister_foto_url'])) {
                $foto_url = base_url($d['sister_foto_url']);
            } elseif (!empty($d['photo'])) {
                $foto_url = base_url($d['photo']);
            }
            
            $dosen_list[] = [
                'username'       => $d['username'],
                'nama'           => $nama,
                'nama_lengkap'   => $nama,
                'nidn'           => $nidn,
                'email'          => $d['email'] ?? '',
                'jabatan_fungsional' => '',
                'jabatan'        => '',
                'kum'            => !empty($d['kum_inpassing']) ? $d['kum_inpassing'] : null,
                'gelar_akademik' => '',
                'status_aktif'   => '1',
                'jml_pengajaran' => (int)($d['jml_pengajaran'] ?? 0),
                'jml_penelitian' => (int)($d['jml_penelitian'] ?? 0),
                'jml_pengabdian' => (int)($d['jml_pengabdian'] ?? 0),
                'foto_url'       => $foto_url,
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $dosen_list, 'total' => count($dosen_list)]);
    }

    public function ajax_detail_profil_dosen() {
        if (!$this->input->is_ajax_request()) show_404();

        $username = $this->input->post('username');
        if (!$username) {
            echo json_encode(['status' => 'error', 'message' => 'Username tidak valid.']);
            return;
        }

        // Ambil data dosen dari DB lokal
        $this->db->select('u.username, u.first_name, u.last_name, u.email, sd.id_ptk_feeder, sd.nm_ptk, sd.nidn, sd.nuptk, sd.photo, sd.nm_ibu_kandung as siakad_nm_ibu_kandung, spd.photo as sister_foto_url, spd.nm_ptk as sister_nama, spd.jk as jenis_kelamin, spd.tmpt_lahir as tempat_lahir, spd.tgl_lahir as tanggal_lahir, spd.nm_ibu_kandung as sister_nm_ibu_kandung, spd.nik, spd.id_agama, spd.kewarganegaraan, spd.stat_kawin, spd.nm_suami_istri, spd.nip_suami_istri, spd.jln, spd.rt, spd.rw, spd.nm_dsn, spd.ds_kel, spd.id_kabupaten, spd.kode_pos, spd.no_tel_rmh, spd.no_hp, spd.email as sister_email, spd.nip, spd.nuptk as sister_nuptk, spd.sk_cpns, spd.tgl_sk_cpns, spd.id_sumber_gaji, spd.npwp, spd.gelar_akademik, spd.pendidikan_terakhir, spd.status_kepegawaian, spd.ikatan_kerja_dosen, spd.unit_kerja, spd.perguruan_tinggi, spd.tanggal_mulai_penugasan');
        $this->db->from('users u');
        $this->db->join('siakad_dosen sd', 'u.id_ptk = sd.id_ptk', 'left');
        $this->db->join('sister_profile_dosen spd', 'sd.id_ptk_feeder = spd.id_sdm', 'left');
        $this->db->where('u.username', $username);
        $dosen = $this->db->get()->row_array();

        if (!$dosen) {
            echo json_encode(['status' => 'error', 'message' => 'Data dosen tidak ditemukan.']);
            return;
        }

        $nama = !empty($dosen['sister_nama']) ? $dosen['sister_nama'] : (!empty($dosen['nm_ptk']) ? $dosen['nm_ptk'] : trim(($dosen['first_name'] ?? '') . ' ' . ($dosen['last_name'] ?? '')));
        $nidn = !empty($dosen['nidn']) ? $dosen['nidn'] : (!empty($dosen['nuptk']) ? $dosen['nuptk'] : '-');
        $nm_ibu = !empty($dosen['sister_nm_ibu_kandung']) ? $dosen['sister_nm_ibu_kandung'] : ($dosen['siakad_nm_ibu_kandung'] ?? '');
        
        $foto_url = null;
        if (!empty($dosen['sister_foto_url'])) {
            $foto_url = base_url($dosen['sister_foto_url']);
        } elseif (!empty($dosen['photo'])) {
            $foto_url = base_url($dosen['photo']);
        }
        
        $detail = [
            'username'           => $dosen['username'],
            'nama'               => $nama,
            'nama_lengkap'       => $nama,
            'nidn'               => $nidn,
            'email'              => $dosen['email'] ?? '',
            'jenis_kelamin'      => $dosen['jenis_kelamin'] ?? '',
            'tempat_lahir'       => $dosen['tempat_lahir'] ?? '',
            'tanggal_lahir'      => $dosen['tanggal_lahir'] ?? '',
            'nama_ibu_kandung'   => $nm_ibu,
            'nik'                => $dosen['nik'] ?? '',
            'agama'              => $this->_get_agama($dosen['id_agama'] ?? ''),
            'kewarganegaraan'    => $dosen['kewarganegaraan'] ?? '',
            'stat_kawin'         => $this->_get_stat_kawin($dosen['stat_kawin'] ?? ''),
            'nm_suami_istri'     => $dosen['nm_suami_istri'] ?? '',
            'nip_suami_istri'    => $dosen['nip_suami_istri'] ?? '',
            'alamat'             => $this->_format_alamat($dosen),
            'no_hp'              => $dosen['no_hp'] ?? '',
            'no_tel_rmh'         => $dosen['no_tel_rmh'] ?? '',
            'sister_email'       => $dosen['sister_email'] ?? '',
            'jabatan_fungsional' => '',
            'jabatan'            => '',
            'gelar_akademik'     => $dosen['gelar_akademik'] ?? '',
            'pendidikan_terakhir'=> $dosen['pendidikan_terakhir'] ?? '',
            'bidang_ilmu'        => '',
            'nip'                => $dosen['nip'] ?? '',
            'nuptk'              => !empty($dosen['sister_nuptk']) ? $dosen['sister_nuptk'] : ($dosen['nuptk'] ?? ''),
            'sk_cpns'            => $dosen['sk_cpns'] ?? '',
            'tgl_sk_cpns'        => $dosen['tgl_sk_cpns'] ?? '',
            'id_sumber_gaji'     => $dosen['id_sumber_gaji'] ?? '',
            'npwp'               => $dosen['npwp'] ?? '',
            'status_kepegawaian' => $dosen['status_kepegawaian'] ?? '',
            'ikatan_kerja_dosen' => $dosen['ikatan_kerja_dosen'] ?? '',
            'unit_kerja'         => $dosen['unit_kerja'] ?? '',
            'perguruan_tinggi'   => $dosen['perguruan_tinggi'] ?? '',
            'tanggal_mulai_penugasan' => $dosen['tanggal_mulai_penugasan'] ?? '',
            'list_bidang_ilmu'   => $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('urutan', 'ASC')->get('sister_bidang_ilmu_dosen')->result_array(),
            'list_jabatan_fungsional' => $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('tanggal_mulai', 'DESC')->get('sister_jabatan_fungsional_dosen')->result_array(),
            'list_inpassing'     => $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('tanggal_sk', 'DESC')->get('sister_inpassing_dosen')->result_array(),
            'list_pendidikan_formal' => [],
            'list_riwayat_pekerjaan' => $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('mulai_bekerja', 'DESC')->get('sister_riwayat_pekerjaan_dosen')->result_array(),
            'status_aktif'       => '1',
            'foto_url'           => $foto_url,
            // Tridharma dari cache lokal
            'pengajaran'         => $this->db->where('id_dosen', $dosen['id_ptk_feeder'])->order_by('tahun_ajaran', 'DESC')->get('sister_pengajaran')->result_array(),
            'penelitian'         => $this->db->where('id_dosen', $dosen['id_ptk_feeder'])->order_by('tahun', 'DESC')->get('sister_penelitian')->result_array(),
            'pengabdian'         => $this->db->where('id_dosen', $dosen['id_ptk_feeder'])->order_by('tahun', 'DESC')->get('sister_pengabdian')->result_array(),
            'publikasi'          => $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('tanggal', 'DESC')->get('sister_publikasi_dosen')->result_array(),
        ];

        // Set the latest Jabatan Fungsional as the main one
        if (!empty($detail['list_jabatan_fungsional'])) {
            $detail['jabatan_fungsional'] = $detail['list_jabatan_fungsional'][0]['jabatan_fungsional'];
        }

        // Deduplikasi list pendidikan formal (prefer yang ada bidang studi)
        $raw_pendidikan = $this->db->where('id_sdm', $dosen['id_ptk_feeder'])->order_by('tahun_lulus', 'DESC')->get('sister_pendidikan_dosen')->result_array();
        $dedup_pend = [];
        foreach ($raw_pendidikan as $p) {
            $key = $p['jenjang_pendidikan'] . '|' . $p['nama_perguruan_tinggi'] . '|' . $p['tahun_lulus'];
            if (!isset($dedup_pend[$key])) {
                $dedup_pend[$key] = $p;
            } else {
                // If existing has empty or * bidang_studi, and current has valid, swap it
                $existing_bs = trim($dedup_pend[$key]['bidang_studi']);
                $current_bs = trim($p['bidang_studi']);
                if (($existing_bs === '' || $existing_bs === '*') && ($current_bs !== '' && $current_bs !== '*')) {
                    $dedup_pend[$key] = $p;
                }
            }
        }
        $detail['list_pendidikan_formal'] = array_values($dedup_pend);

        // BKD Dosen (Laporan & Kegiatan per Semester)
        $id_sdm = $dosen['id_ptk_feeder'];
        $laporan_db = $this->db->where('id_sdm', $id_sdm)->order_by('id_smt', 'DESC')->get('sister_bkd_laporan')->result_array();
        $smt_label = function($id_smt) {
            if (!$id_smt) return '-';
            $tahun = substr((string)$id_smt, 0, 4);
            $sem   = substr((string)$id_smt, 4, 1);
            return $tahun . '/' . ((int)$tahun + 1) . ' ' . ($sem == '1' ? 'Ganjil' : 'Genap');
        };

        $laporan_mapped = array_map(fn($l) => [
            'id_smt'          => $l['id_smt'],
            'label_smt'       => $smt_label($l['id_smt']),
            'sks_ajar'        => floatval($l['sks_ajar']),
            'sks_didik'       => floatval($l['sks_didik']),
            'sks_lit'         => floatval($l['sks_lit']),
            'sks_pengmas'     => floatval($l['sks_pengmas']),
            'sks_penunjang'   => floatval($l['sks_penunjang']),
            'sks_total'       => floatval($l['sks_total']),
            'stat_kewajiban'  => $l['stat_kewajiban'],
            'simpulan_asesor' => $l['simpulan_asesor'],
        ], $laporan_db);

        $kegiatan_all = $this->db->where('id_sdm', $id_sdm)->order_by('id', 'ASC')->get('sister_bkd_kegiatan')->result_array();
        $kegiatan_by_smt = [];
        foreach ($kegiatan_all as $k) {
            $smt = (string)$k['id_smt'];
            $kegiatan_by_smt[$smt] ??= [
                'ajar'       => [],
                'penelitian' => [],
                'pengmas'    => [],
                'tunjang'    => [],
                'pendidikan' => [],
            ];
            $unsur = $k['unsur'];
            $item = [
                'judul_keg'  => $k['judul_keg'],
                'id_katgiat' => $k['id_katgiat'],
                'nm_kat'     => $k['nm_kat'],
                'beban_sks'  => floatval($k['beban_sks']),
                'nilai'      => floatval($k['nilai']),
            ];
            if (isset($kegiatan_by_smt[$smt][$unsur])) {
                $kegiatan_by_smt[$smt][$unsur][] = $item;
            }
        }

        $detail['bkd'] = [
            'laporan'         => $laporan_mapped,
            'kegiatan_by_smt' => $kegiatan_by_smt,
        ];

        echo json_encode(['status' => 'success', 'data' => $detail]);
    }

    /**
     * Ambil data BKD dosen yang sedang login secara real-time dari SISTER API.
     * Menggunakan id_sdm + id_smt sebagai parameter.
     */
    public function ajax_bkd_pribadi() {
        if (!$this->input->is_ajax_request()) show_404();

        $user      = $this->ion_auth->user()->row();
        $username  = $user->username;

        // Dapatkan id_ptk_feeder (id_sdm) dari siakad_dosen
        $dosen = $this->db->select('id_ptk_feeder')
            ->join('siakad_dosen', 'users.id_ptk = siakad_dosen.id_ptk', 'left')
            ->where('username', $username)
            ->get('users')->row();

        if (!$dosen || empty($dosen->id_ptk_feeder)) {
            echo json_encode(['status' => 'error', 'message' => 'ID SDM tidak ditemukan.']);
            return;
        }
        $id_sdm = $dosen->id_ptk_feeder;

        // 1. Ambil laporan akhir dari database lokal
        $laporan_db = $this->db->where('id_sdm', $id_sdm)->order_by('id_smt', 'DESC')->get('sister_bkd_laporan')->result_array();

        // Konversi id_smt ke label readable
        $smt_label = function($id_smt) {
            if (!$id_smt) return '-';
            $tahun = substr((string)$id_smt, 0, 4);
            $sem   = substr((string)$id_smt, 4, 1);
            return $tahun . '/' . ((int)$tahun + 1) . ' ' . ($sem == '1' ? 'Ganjil' : 'Genap');
        };

        $laporan_mapped = array_map(fn($l) => [
            'id_smt'          => $l['id_smt'],
            'label_smt'       => $smt_label($l['id_smt']),
            'sks_ajar'        => floatval($l['sks_ajar']),
            'sks_didik'       => floatval($l['sks_didik']),
            'sks_lit'         => floatval($l['sks_lit']),
            'sks_pengmas'     => floatval($l['sks_pengmas']),
            'sks_penunjang'   => floatval($l['sks_penunjang']),
            'sks_total'       => floatval($l['sks_total']),
            'stat_kewajiban'  => $l['stat_kewajiban'],
            'simpulan_asesor' => $l['simpulan_asesor'],
        ], $laporan_db);

        // 2. Ambil seluruh rincian kegiatan BKD untuk SEMUA semester dosen ini
        $kegiatan_all = $this->db->where('id_sdm', $id_sdm)->order_by('id', 'ASC')->get('sister_bkd_kegiatan')->result_array();
        $kegiatan_by_smt = [];
        foreach ($kegiatan_all as $k) {
            $smt = (string)$k['id_smt'];
            $kegiatan_by_smt[$smt] ??= [
                'ajar'       => [],
                'penelitian' => [],
                'pengmas'    => [],
                'tunjang'    => [],
                'pendidikan' => [],
            ];
            $unsur = $k['unsur'];
            $item = [
                'judul_keg'  => $k['judul_keg'],
                'id_katgiat' => $k['id_katgiat'],
                'nm_kat'     => $k['nm_kat'],
                'beban_sks'  => floatval($k['beban_sks']),
                'nilai'      => floatval($k['nilai']),
            ];
            if (isset($kegiatan_by_smt[$smt][$unsur])) {
                $kegiatan_by_smt[$smt][$unsur][] = $item;
            }
        }

        echo json_encode([
            'status'          => 'success',
            'laporan'         => $laporan_mapped,
            'kegiatan_by_smt' => $kegiatan_by_smt,
        ]);
    }


    /**
     * Bangun URL foto dari SISTER.
     * Foto di-render langsung di browser menggunakan URL SISTER (tidak di-download).
     * Sesuaikan endpoint sesuai dokumentasi SISTER API yang aktual.
     */
    private function _build_foto_url($id_ptk_feeder, $token) {
        return 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/data_pribadi/foto/' . urlencode($id_ptk_feeder) . '?token=' . urlencode($token);
    }

    // ==========================================
    // SMART SYNC HELPERS
    // ==========================================

    /**
     * Cek apakah last_sync masih dalam threshold (masih 'fresh').
     * Jika fresh, tidak perlu sync ulang ke API SISTER.
     *
     * @param string|null $last_sync  Datetime string dari DB (Y-m-d H:i:s)
     * @param int         $days       Threshold dalam hari (default: 180)
     * @return bool  TRUE jika masih fresh (skip), FALSE jika sudah expired (perlu sync)
     */
    private function _is_sync_fresh($last_sync, $days = 180) {
        if (empty($last_sync)) return false;
        $last = strtotime($last_sync);
        if ($last === false) return false;
        return (time() - $last) < ($days * 86400);
    }

    /**
     * Buat MD5 fingerprint dari array data untuk membandingkan isi sebelum upsert.
     *
     * @param array  $data  Associative array data
     * @param array  $keys  Key yang ingin di-hash (kosong = semua key)
     * @return string  MD5 hash
     */
    private function _data_fingerprint(array $data, array $keys = []) {
        if (!empty($keys)) {
            $subset = [];
            foreach ($keys as $k) {
                $subset[$k] = $data[$k] ?? null;
            }
            return md5(serialize($subset));
        }
        return md5(serialize($data));
    }

    /**
     * Jalankan beberapa cURL request secara PARALEL menggunakan curl_multi.
     * Mengurangi waktu tunggu dari N*latency menjadi ~1*latency.
     *
     * @param string $token   Bearer token SISTER
     * @param array  $urls    Associative array [key => url]
     * @return array          Associative array [key => decoded_json_or_null]
     */
    private function _sister_curl_parallel($token, array $urls) {
        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ];

        $mh      = curl_multi_init();
        $handles = [];

        foreach ($urls as $key => $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_multi_add_handle($mh, $ch);
            $handles[$key] = $ch;
        }

        // Eksekusi paralel
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        $results = [];
        foreach ($handles as $key => $ch) {
            $res  = curl_multi_getcontent($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if ($code === 200 && $res) {
                $decoded = json_decode($res);
                $results[$key] = $decoded ?? null;
            } else {
                $results[$key] = null;
            }
        }

        curl_multi_close($mh);
        return $results;
    }

    /**
     * Ambil dan simpan data BKD dosen ke sister_bkd_laporan & sister_bkd_kegiatan.
     * Smart Sync:
     *  - Cek threshold 30 hari. Jika masih fresh dan bukan force, skip.
     *  - Hanya tarik detail kegiatan untuk semester yang SKS-nya berubah atau belum ada.
     *  - Semester lama (> 2 tahun) tidak di-refresh kecuali force = true.
     *
     * @param string $token
     * @param string $id_sdm
     * @param bool   $force  TRUE = bypass threshold & selalu tarik ulang
     */
    private function _fetch_and_save_bkd($token, $id_sdm, $force = false) {
        // --- Threshold check: BKD cek setiap 30 hari ---
        if (!$force) {
            $latest_lap = $this->db
                ->select('last_sync')
                ->where('id_sdm', $id_sdm)
                ->order_by('last_sync', 'DESC')
                ->limit(1)
                ->get('sister_bkd_laporan')
                ->row();
            $last_sync = $latest_lap ? ($latest_lap->last_sync ?? null) : null;
            if ($this->_is_sync_fresh($last_sync, 30)) {
                log_message('info', "SISTER BKD SKIP (fresh 30d): {$id_sdm}");
                return;
            }
        }

        $base    = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/';
        $headers = ['Accept: application/json', 'Authorization: Bearer ' . $token];

        $fetch_arr = function($url) use ($headers) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $res  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code === 200 && $res) {
                $data = json_decode($res, true);
                return is_array($data) ? $data : [];
            }
            return [];
        };

        $stat_kewajiban = function($v) {
            if ($v == '1' || $v === 'M' || strtolower((string)$v) === 'memenuhi') return 'Memenuhi';
            return 'Tidak'; // Sesuai kolom varchar(10) di MySQL
        };
        $simpulan_label = function($v) {
            $map = ['M' => 'Memenuhi', 'TM' => 'Tidak', 'BM' => 'Belum'];
            return $map[$v] ?? substr((string)$v, 0, 10);
        };

        // 1. Ambil ringkasan laporan akhir BKD (1 request)
        $laporan = $fetch_arr($base . 'bkd/laporan_akhir_bkd?id_sdm=' . urlencode($id_sdm));
        if (empty($laporan) || !is_array($laporan)) {
            log_message('info', "SISTER BKD: tidak ada laporan untuk id_sdm={$id_sdm}");
            return;
        }

        // Ambil data laporan lokal yang sudah ada untuk perbandingan cepat
        $laporan_lokal_raw = $this->db
            ->select('id_smt, sks_total, simpulan_asesor, last_sync')
            ->where('id_sdm', $id_sdm)
            ->get('sister_bkd_laporan')
            ->result_array();
        $laporan_lokal = []; // id_smt => row
        foreach ($laporan_lokal_raw as $row) {
            $laporan_lokal[$row['id_smt']] = $row;
        }

        $batas_semester_lama = date('Ym', strtotime('-2 years')); // 2 tahun lalu

        foreach ($laporan as $l) {
            $l_arr   = is_array($l) ? $l : (array)$l;
            $id_smt  = $l_arr['id_smt'] ?? '';
            if (!$id_smt) continue;

            $sks_total_api       = floatval($l_arr['sks_kinerja'] ?? 0);
            $simpulan_api        = $simpulan_label($l_arr['simpulan_asesor'] ?? '');
            $stat_kewajiban_api  = $stat_kewajiban($l_arr['stat_kewajiban'] ?? '');

            $lap_data = [
                'id_sdm'         => $id_sdm,
                'id_smt'         => $id_smt,
                'sks_ajar'       => floatval($l_arr['sks_kinerja_ajar']      ?? 0),
                'sks_didik'      => floatval($l_arr['sks_kinerja_didik']     ?? 0),
                'sks_lit'        => floatval($l_arr['sks_kinerja_lit']       ?? 0),
                'sks_pengmas'    => floatval($l_arr['sks_kinerja_pengmas']   ?? 0),
                'sks_penunjang'  => floatval($l_arr['sks_kinerja_penunjang'] ?? 0),
                'sks_total'      => $sks_total_api,
                'stat_kewajiban' => $stat_kewajiban_api,
                'simpulan_asesor'=> $simpulan_api,
                'last_sync'      => date('Y-m-d H:i:s'),
            ];

            $existing = $laporan_lokal[$id_smt] ?? null;

            // --- Smart check: apakah detail kegiatan perlu di-refresh? ---
            $data_sama     = $existing
                && (string)floatval($existing['sks_total']) === (string)$sks_total_api
                && $existing['simpulan_asesor']   === $simpulan_api;
            $semester_lama = ($id_smt < $batas_semester_lama);

            if ($existing) {
                $this->db->where(['id_sdm' => $id_sdm, 'id_smt' => $id_smt])->update('sister_bkd_laporan', $lap_data);
            } else {
                $this->db->insert('sister_bkd_laporan', $lap_data);
            }

            // Skip tarik kegiatan detail jika data sama & semester lama (dan bukan force)
            if (!$force && $data_sama && $semester_lama) {
                log_message('info', "SISTER BKD KEGIATAN SKIP (same+old): {$id_sdm} smt={$id_smt}");
                continue;
            }

            // 3. Tarik detail kegiatan per unsur untuk semester ini secara PARALEL
            $params     = '?id_sdm=' . urlencode($id_sdm) . '&id_smt=' . urlencode($id_smt);
            $unsur_list = ['ajar', 'penelitian', 'pengmas', 'tunjang', 'pendidikan'];

            $urls_kegiatan = [];
            foreach ($unsur_list as $unsur) {
                $urls_kegiatan[$unsur] = $base . 'bkd/' . $unsur . $params;
            }

            $kegiatan_hasil = $this->_sister_curl_parallel($token, $urls_kegiatan);

            // Hapus kegiatan lama semester ini, lalu insert batch baru
            $this->db->where(['id_sdm' => $id_sdm, 'id_smt' => $id_smt])->delete('sister_bkd_kegiatan');

            $batch_keg = [];
            foreach ($unsur_list as $unsur) {
                $detail_raw = $kegiatan_hasil[$unsur] ?? null;
                $detail     = is_array($detail_raw) ? $detail_raw : [];
                foreach ($detail as $item) {
                    $judul      = is_object($item) ? ($item->judul_keg ?? null) : ($item['judul_keg'] ?? null);
                    $id_katgiat = is_object($item) ? ($item->id_katgiat ?? null) : ($item['id_katgiat'] ?? null);
                    $nm_kat     = is_object($item) ? ($item->nm_kat ?? null) : ($item['nm_kat'] ?? null);
                    $beban_sks  = is_object($item) ? ($item->beban_sks ?? 0) : ($item['beban_sks'] ?? 0);
                    $nilai      = is_object($item) ? ($item->nilai ?? 0) : ($item['nilai'] ?? 0);

                    $batch_keg[] = [
                        'id_sdm'    => $id_sdm,
                        'id_smt'    => $id_smt,
                        'unsur'     => $unsur,
                        'judul_keg' => $judul,
                        'id_katgiat'=> $id_katgiat ? intval($id_katgiat) : null,
                        'nm_kat'    => $nm_kat,
                        'beban_sks' => floatval($beban_sks),
                        'nilai'     => floatval($nilai),
                        'last_sync' => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($batch_keg)) {
                $this->db->insert_batch('sister_bkd_kegiatan', $batch_keg);
            }

            log_message('info', "SISTER BKD KEGIATAN SYNCED: {$id_sdm} smt={$id_smt} total_kegiatan=" . count($batch_keg));
        }
    }


    public function eksekutif() {
        if (!$this->_is_gege_satya()) {
            show_error('Akses Terbatas: Halaman Dashboard Eksekutif hanya dapat diakses oleh Administrator Sistem (gege.satya@wbi.ac.id).', 403, '403 Forbidden Access');
            exit;
        }

        $data['menu_aktif'] = 'm_sister_eksekutif';
        $data['parent_admin'] = 'active open';
        
        // Analitik agregat untuk HRD (KPI)
        
        // 1. Total SKS Pengajaran
        $this->db->select_sum('sks');
        $pengajaran_query = $this->db->get('sister_pengajaran')->row();
        $data['total_sks'] = $pengajaran_query->sks ?? 0;
        
        // 2. Total Penelitian
        $data['total_penelitian'] = $this->db->count_all('sister_penelitian');
        
        // 3. Total Pengabdian
        $data['total_pengabdian'] = $this->db->count_all('sister_pengabdian');

        // Untuk detail tabel
        $data['semua_pengajaran'] = $this->db->get('sister_pengajaran')->result();
        $data['semua_penelitian'] = $this->db->get('sister_penelitian')->result();
        $data['semua_pengabdian'] = $this->db->get('sister_pengabdian')->result();

        $data['judul_besar'] = 'Dashboard SISTER';
        $data['judul_kecil'] = 'Eksekutif';
        $data['content'] = 'sister/dashboard_eksekutif';

        $this->load->view('master_page', $data);
    }
    public function ajax_sync_pribadi() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $user = $this->ion_auth->user()->row();
        $id_dosen = $user->username;
        
        // Ambil id_ptk_feeder dari tabel siakad_dosen
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';

        // 1. Ambil setting SISTER
        $setting = $this->db->get('sister_settings')->row();
        if (!$setting) {
            echo json_encode(['status' => 'error', 'message' => 'Pengaturan kredensial SISTER belum diisi di database.']);
            return;
        }

        // 2. Dapatkan Token
        $token = $this->_get_sister_token_api($setting);
        if (!$token) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mendapatkan token dari SISTER API. Cek kredensial Anda.']);
            return;
        }

        // 3. Tarik data riil
        if (empty($id_sdm)) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyinkronkan: ID SDM / ID PTK Anda belum terdaftar.']);
            return;
        }

        // Sync pribadi selalu force = true agar dosen mendapatkan data terbaru saat klik manual
        $this->_fetch_and_save_profil($token, $id_dosen, $id_sdm, true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengajaran', true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'penelitian', true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengabdian', true);
        $this->_fetch_and_save_publikasi($token, $id_sdm, true);
        $this->_fetch_and_save_bkd($token, $id_sdm, true);
        
        echo json_encode(['status' => 'success', 'message' => 'Data SISTER Anda berhasil disinkronkan dari server Pusat.']);
    }

    public function ajax_sync_eksekutif() {
        if (!$this->input->is_ajax_request() || !$this->_is_gege_satya()) show_404();
        set_time_limit(0);
        $setting = $this->db->get('sister_settings')->row();
        if (!$setting) { echo json_encode(['status' => 'error', 'message' => 'Pengaturan kredensial SISTER belum diisi.']); return; }
        $token = $this->_get_sister_token_api($setting);
        if (!$token) { echo json_encode(['status' => 'error', 'message' => 'Gagal mendapatkan token SISTER API.']); return; }
        
        $this->db->select('users.username, siakad_dosen.id_ptk_feeder');
        $this->db->from('users');
        $this->db->join('siakad_dosen', 'users.id_ptk = siakad_dosen.id_ptk');
        $this->db->where('siakad_dosen.id_ptk_feeder !=', '');
        $this->db->where('siakad_dosen.id_ptk_feeder IS NOT NULL');
        $this->db->where('users.username IS NOT NULL');
        $this->db->where('users.username !=', '');
        $dosen_list = $this->db->get()->result();
        
        $synced  = 0;
        $skipped = 0;

        // Sync massal: TIDAK force (gunakan threshold agar tidak spam API)
        // Dosen yang last_sync masih fresh akan di-skip otomatis
        foreach ($dosen_list as $dosen) {
            $id_dosen = $dosen->username;
            $id_sdm   = $dosen->id_ptk_feeder;

            // Cek apakah profil masih fresh (180 hari)
            $profil_row = $this->db->select('last_sync')->where('id_sdm', $id_sdm)->get('sister_profile_dosen')->row();
            $last_sync  = $profil_row ? ($profil_row->last_sync ?? null) : null;

            if ($this->_is_sync_fresh($last_sync, 180)) {
                $skipped++;
                log_message('info', "SISTER SYNC SKIP (fresh): {$id_dosen}");
                continue;
            }

            $this->_fetch_and_save_profil($token, $id_dosen, $id_sdm, false);
            $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengajaran', false);
            $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'penelitian', false);
            $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengabdian', false);
            $this->_fetch_and_save_publikasi($token, $id_sdm, false);
            $this->_fetch_and_save_bkd($token, $id_sdm, false);
            $synced++;
        }
        
        echo json_encode([
            'status'  => 'success',
            'message' => "Sinkronisasi massal selesai. Diproses: {$synced} dosen, Di-skip (masih fresh): {$skipped} dosen.",
            'synced'  => $synced,
            'skipped' => $skipped,
        ]);
    }

    public function ajax_get_all_dosen_for_profil_sync() {
        if (!$this->input->is_ajax_request() || !$this->_is_direktur_or_wadir2()) show_404();
        $this->db->select('users.username, siakad_dosen.id_ptk_feeder');
        $this->db->from('users');
        $this->db->join('siakad_dosen', 'users.id_ptk = siakad_dosen.id_ptk');
        $this->db->where('siakad_dosen.id_ptk_feeder !=', '');
        $this->db->where('siakad_dosen.id_ptk_feeder IS NOT NULL');
        $this->db->where('users.username IS NOT NULL');
        $this->db->where('users.username !=', '');
        $dosen_list = $this->db->get()->result_array();
        echo json_encode(['status' => 'success', 'data' => $dosen_list]);
    }

    public function ajax_sync_profil_single_dosen() {
        if (!$this->input->is_ajax_request() || !$this->_is_direktur_or_wadir2()) show_404();
        
        $id_dosen = $this->input->post('username');
        $id_sdm   = $this->input->post('id_ptk_feeder');

        if (!$id_dosen || !$id_sdm) { echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']); return; }

        $setting = $this->db->get('sister_settings')->row();
        if (!$setting) { echo json_encode(['status' => 'error', 'message' => 'Setting SISTER kosong']); return; }

        $token = $this->_get_sister_token_api($setting);
        if (!$token) { echo json_encode(['status' => 'error', 'message' => 'Gagal mendapat token']); return; }

        // Admin sync single dosen profil = force agar data langsung diperbarui
        $this->_fetch_and_save_profil($token, $id_dosen, $id_sdm, true);

        echo json_encode(['status' => 'success']);
    }

    public function ajax_get_dosen_sync_list() {
        if (!$this->input->is_ajax_request() || !$this->_is_gege_satya()) show_404();
        $this->db->select('users.username, siakad_dosen.id_ptk_feeder');
        $this->db->from('users');
        $this->db->join('siakad_dosen', 'users.id_ptk = siakad_dosen.id_ptk');
        $this->db->join('sister_profile_dosen', 'siakad_dosen.id_ptk_feeder = sister_profile_dosen.id_sdm', 'inner'); // HANYA YANG PROFILNYA ADA
        $this->db->where('siakad_dosen.id_ptk_feeder !=', '');
        $this->db->where('siakad_dosen.id_ptk_feeder IS NOT NULL');
        $this->db->where('users.username IS NOT NULL');
        $this->db->where('users.username !=', '');
        $dosen_list = $this->db->get()->result_array();
        echo json_encode(['status' => 'success', 'data' => $dosen_list]);
    }

    public function ajax_sync_single_dosen() {
        if (!$this->input->is_ajax_request() || !$this->_is_gege_satya()) show_404();
        
        $id_dosen = $this->input->post('username');
        $id_sdm   = $this->input->post('id_ptk_feeder');

        if (!$id_dosen || !$id_sdm) {
            echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
            return;
        }

        $setting = $this->db->get('sister_settings')->row();
        if (!$setting) { echo json_encode(['status' => 'error', 'message' => 'Setting SISTER kosong']); return; }

        $token = $this->_get_sister_token_api($setting);
        if (!$token) { echo json_encode(['status' => 'error', 'message' => 'Gagal mendapat token']); return; }

        // Admin sync single dosen = force agar data langsung diperbarui
        $this->_fetch_and_save_profil($token, $id_dosen, $id_sdm, true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengajaran', true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'penelitian', true);
        $this->_fetch_and_save_data($token, $id_dosen, $id_sdm, 'pengabdian', true);
        $this->_fetch_and_save_publikasi($token, $id_sdm, true);
        $this->_fetch_and_save_bkd($token, $id_sdm, true);

        echo json_encode(['status' => 'success']);
    }


    private function _get_sister_token_api($setting) {
        $url = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/authorize'; // Endpoint Otorisasi
        
        $payload = json_encode([
            'username' => $setting->username,
            'password' => $setting->password,
            'id_pengguna' => $setting->id_pengguna
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Hilangkan jika production memiliki SSL valid
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200 && $response) {
            $data = json_decode($response);
            return $data->token ?? false;
        }
        return false;
    }

    /**
     * Ambil dan simpan profil dosen dari SISTER API.
     * Smart Sync:
     *  - Cek threshold 180 hari. Jika masih fresh dan bukan force, skip.
     *  - Gunakan parallel cURL untuk semua sub-endpoint sekaligus.
     *  - Fingerprint check sebelum update profil utama.
     *  - Foto hanya di-download jika file lokal belum ada atau sudah > 180 hari.
     *
     * @param string $token
     * @param string $id_dosen  Username
     * @param string $id_sdm    id_ptk_feeder
     * @param bool   $force     TRUE = bypass threshold
     */
    private function _fetch_and_save_profil($token, $id_dosen, $id_sdm, $force = false) {
        // --- Threshold check: Profil cek setiap 180 hari ---
        if (!$force) {
            $profil_lokal = $this->db->select('last_sync')->where('id_sdm', $id_sdm)->get('sister_profile_dosen')->row();
            $last_sync    = $profil_lokal ? ($profil_lokal->last_sync ?? null) : null;
            if ($this->_is_sync_fresh($last_sync, 180)) {
                log_message('info', "SISTER PROFIL SKIP (fresh 180d): {$id_dosen}");
                return false;
            }
        }

        $base = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/';

        // --- FASE 1: Fetch profil utama dulu (perlu nama untuk validasi) ---
        $ch_profil = curl_init($base . 'data_pribadi/profil/' . urlencode($id_sdm));
        curl_setopt($ch_profil, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_profil, CURLOPT_HTTPGET, true);
        curl_setopt($ch_profil, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
        curl_setopt($ch_profil, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_profil, CURLOPT_TIMEOUT, 30);
        $resp_profil = curl_exec($ch_profil);
        $code_profil = curl_getinfo($ch_profil, CURLINFO_HTTP_CODE);
        curl_close($ch_profil);

        if ($code_profil != 200 || !$resp_profil) {
            log_message('error', "SISTER PROFIL ERROR: HTTP {$code_profil} id_sdm={$id_sdm}");
            return false;
        }
        $data_profil = json_decode($resp_profil);
        $nama = $data_profil->nama ?? null;
        $jk   = $data_profil->jenis_kelamin ?? null;
        $tmpt = $data_profil->tempat_lahir ?? null;
        $tgl  = $data_profil->tanggal_lahir ?? null;

        // Jika nama kosong, tidak valid — hentikan
        if (empty(trim((string)$nama))) {
            log_message('info', "SISTER PROFIL SKIP (nama kosong): {$id_sdm}");
            return false;
        }

        // --- FASE 2: Fetch semua sub-endpoint PARALEL ---
        $parallel_urls = [
            'kependudukan'      => $base . 'data_pribadi/kependudukan/' . urlencode($id_sdm),
            'keluarga'          => $base . 'data_pribadi/keluarga/'     . urlencode($id_sdm),
            'alamat'            => $base . 'data_pribadi/alamat/'        . urlencode($id_sdm),
            'kepegawaian'       => $base . 'data_pribadi/kepegawaian/'  . urlencode($id_sdm),
            'lain'              => $base . 'data_pribadi/lain/'          . urlencode($id_sdm),
            'bidang_ilmu'       => $base . 'data_pribadi/bidang_ilmu/'  . urlencode($id_sdm),
            'jabatan_fungsional'=> $base . 'jabatan_fungsional?id_sdm=' . urlencode($id_sdm),
            'pendidikan_formal' => $base . 'pendidikan_formal?id_sdm='  . urlencode($id_sdm),
            'riwayat_pekerjaan' => $base . 'riwayat_pekerjaan?id_sdm='  . urlencode($id_sdm),
            'penugasan'         => $base . 'penugasan?id_sdm='           . urlencode($id_sdm),
            'inpassing'         => $base . 'inpassing?id_sdm='           . urlencode($id_sdm),
        ];

        $parallel_results = $this->_sister_curl_parallel($token, $parallel_urls);

        $kep  = $parallel_results['kependudukan']      ?? null;
        $kel  = $parallel_results['keluarga']           ?? null;
        $alm  = $parallel_results['alamat']             ?? null;
        $peg  = $parallel_results['kepegawaian']        ?? null;
        $lan  = $parallel_results['lain']               ?? null;
        $bid  = $parallel_results['bidang_ilmu']        ?? null;
        $jab  = $parallel_results['jabatan_fungsional'] ?? null;
        $pend = $parallel_results['pendidikan_formal']  ?? null;
        $pek  = $parallel_results['riwayat_pekerjaan']  ?? null;
        $arr_penugasan = $parallel_results['penugasan'] ?? null;
        $arr_inp       = $parallel_results['inpassing'] ?? null;

        // --- Proses Penugasan ---
        $status_kepegawaian = null; $ikatan_kerja_dosen = null;
        $unit_kerja = null; $perguruan_tinggi = null; $tanggal_mulai_penugasan = null;
        if (is_array($arr_penugasan) && count($arr_penugasan) > 0) {
            $active_penugasan = $arr_penugasan[0];
            foreach ($arr_penugasan as $p) {
                if (isset($p->apakah_penugasan_homebase) && strtolower(trim((string)$p->apakah_penugasan_homebase)) == 'ya') {
                    $active_penugasan = $p;
                    break;
                }
            }
            $status_kepegawaian      = $active_penugasan->status_kepegawaian ?? null;
            $ikatan_kerja_dosen      = $active_penugasan->ikatan_kerja ?? null;
            $unit_kerja              = $active_penugasan->unit_kerja ?? null;
            $perguruan_tinggi        = $active_penugasan->perguruan_tinggi ?? null;
            $tanggal_mulai_penugasan = $active_penugasan->tanggal_mulai ?? null;
        }

        // --- Proses Inpassing: buat detail request untuk setiap id_riwayat (mungkin hanya 1-3 item) ---
        if (is_array($arr_inp) && !empty($arr_inp)) {
            $inp_detail_urls = [];
            foreach ($arr_inp as $inp) {
                $id_riwayat = $inp->id ?? '';
                if ($id_riwayat) {
                    $inp_detail_urls[$id_riwayat] = $base . 'inpassing/' . urlencode($id_riwayat);
                }
            }

            $inp_detail_results = !empty($inp_detail_urls)
                ? $this->_sister_curl_parallel($token, $inp_detail_urls)
                : [];

            $this->db->where('id_sdm', $id_sdm)->delete('sister_inpassing_dosen');
            $batch_inp = [];
            foreach ($arr_inp as $inp) {
                $id_riwayat     = $inp->id ?? '';
                $detail_inp     = $inp_detail_results[$id_riwayat] ?? null;
                $angka_kredit   = $detail_inp->angka_kredit   ?? null;
                $masa_kerja_tahun = $detail_inp->masa_kerja_tahun ?? null;
                $masa_kerja_bulan = $detail_inp->masa_kerja_bulan ?? null;

                $batch_inp[] = [
                    'id_sdm'          => $id_sdm,
                    'id_riwayat'      => $id_riwayat,
                    'pangkat_golongan'=> $inp->pangkat_golongan ?? null,
                    'sk'              => $inp->sk ?? null,
                    'tanggal_sk'      => $inp->tanggal_sk ?? null,
                    'tanggal_mulai'   => $inp->tanggal_mulai ?? null,
                    'angka_kredit'    => $angka_kredit,
                    'masa_kerja_tahun'=> $masa_kerja_tahun,
                    'masa_kerja_bulan'=> $masa_kerja_bulan,
                    'last_sync'       => date('Y-m-d H:i:s'),
                ];
            }
            if (!empty($batch_inp)) {
                $this->db->insert_batch('sister_inpassing_dosen', $batch_inp);
            }
        }

        // --- Proses Riwayat Pekerjaan ---
        if (is_array($pek)) {
            $this->db->where('id_sdm', $id_sdm)->delete('sister_riwayat_pekerjaan_dosen');
            $batch_pek = [];
            foreach ($pek as $pk) {
                $batch_pek[] = [
                    'id_sdm'          => $id_sdm,
                    'id_riwayat'      => $pk->id ?? null,
                    'jenis_pekerjaan' => $pk->jenis_pekerjaan ?? null,
                    'nama_jabatan'    => $pk->nama_jabatan ?? null,
                    'instansi'        => $pk->instansi ?? null,
                    'divisi'          => $pk->divisi ?? null,
                    'mulai_bekerja'   => !empty($pk->mulai_bekerja) ? $pk->mulai_bekerja : null,
                    'selesai_bekerja' => !empty($pk->selesai_bekerja) ? $pk->selesai_bekerja : null,
                    'luar_negeri'     => $pk->luar_negeri ?? null,
                    'bidang_usaha'    => $pk->bidang_usaha ?? null,
                    'last_sync'       => date('Y-m-d H:i:s'),
                ];
            }
            if (!empty($batch_pek)) {
                $this->db->insert_batch('sister_riwayat_pekerjaan_dosen', $batch_pek);
            }
        }

        // --- Proses Pendidikan Formal ---
        $gelar_akademik = null;
        $pendidikan_terakhir = null;
        if (is_array($pend)) {
            $arr_gelar = [];
            $jenjang_tertinggi = '';
            $tahun_tertinggi = 0;
            foreach ($pend as $p) {
                if (!empty($p->gelar_akademik)) $arr_gelar[] = trim((string)$p->gelar_akademik);
                elseif (!empty($p->singkatan_gelar)) $arr_gelar[] = trim((string)$p->singkatan_gelar);

                $tahun = intval($p->tahun_lulus ?? 0);
                if ($tahun >= $tahun_tertinggi) {
                    $tahun_tertinggi   = $tahun;
                    $jenjang_tertinggi = $p->jenjang_pendidikan ?? ($p->nama_jenjang_pendidikan ?? '');
                }
            }
            $gelar_akademik      = implode(', ', array_unique($arr_gelar));
            $pendidikan_terakhir = $jenjang_tertinggi;

            // Fingerprint check: hanya update tabel pendidikan jika ada perubahan
            $pend_db = $this->db->select('id_riwayat, jenjang_pendidikan, gelar_akademik, tahun_lulus')
                ->where('id_sdm', $id_sdm)->get('sister_pendidikan_dosen')->result_array();
            $fp_api  = md5(serialize(array_map(fn($p) => [(string)($p->id ?? ''), (string)($p->jenjang_pendidikan ?? ''), (string)($p->tahun_lulus ?? '')], $pend)));
            $fp_db   = md5(serialize(array_map(fn($r) => [(string)($r['id_riwayat'] ?? ''), (string)($r['jenjang_pendidikan'] ?? ''), (string)($r['tahun_lulus'] ?? '')], $pend_db)));

            if ($force || $fp_api !== $fp_db) {
                $this->db->where('id_sdm', $id_sdm)->delete('sister_pendidikan_dosen');
                $batch_pend = [];
                foreach ($pend as $p) {
                    $gelar_p  = $p->gelar_akademik ?? ($p->singkatan_gelar ?? null);
                    $bidang_p = $p->bidang_studi ?? null;
                    if (empty(trim((string)$gelar_p)) && ($bidang_p === '*' || empty(trim((string)$bidang_p)))) {
                        continue;
                    }
                    $batch_pend[] = [
                    'id_sdm'               => $id_sdm,
                    'id_riwayat'           => $p->id ?? null,
                    'jenjang_pendidikan'   => $p->jenjang_pendidikan ?? ($p->nama_jenjang_pendidikan ?? null),
                    'gelar_akademik'       => $gelar_p,
                    'bidang_studi'         => $bidang_p,
                    'nama_perguruan_tinggi'=> $p->nama_perguruan_tinggi ?? null,
                    'tahun_lulus'          => $p->tahun_lulus ?? null,
                    'last_sync'            => date('Y-m-d H:i:s'),
                ];
                }
                if (!empty($batch_pend)) {
                    $this->db->insert_batch('sister_pendidikan_dosen', $batch_pend);
                }
            } // end if fingerprint changed
        } // end if is_array(pend)

        // --- Foto: hanya download jika file lokal belum ada atau force ---
        $foto_path     = 'assets/uploads/foto_dosen/' . $id_sdm . '.jpg';
        $foto_fullpath = FCPATH . $foto_path;
        $local_foto_url = null;

        $perlu_download_foto = $force
            || !file_exists($foto_fullpath)
            || (time() - filemtime($foto_fullpath)) > (180 * 86400);

        if ($perlu_download_foto) {
            $ch_foto = curl_init($base . 'data_pribadi/foto/' . urlencode($id_sdm));
            curl_setopt($ch_foto, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_foto, CURLOPT_HTTPGET, true);
            curl_setopt($ch_foto, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer ' . $token]);
            curl_setopt($ch_foto, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_foto, CURLOPT_TIMEOUT, 20);
            $resp_foto  = curl_exec($ch_foto);
            $code_foto  = curl_getinfo($ch_foto, CURLINFO_HTTP_CODE);
            curl_close($ch_foto);

            if ($code_foto == 200 && strlen($resp_foto) > 0) {
                file_put_contents($foto_fullpath, $resp_foto);
                $local_foto_url = $foto_path;
            }
        } else {
            $local_foto_url = file_exists($foto_fullpath) ? $foto_path : null;
        }

        // --- Susun data profil utama ---
        $data_db = [
            'id_sdm'                  => $id_sdm,
            'nm_ptk'                  => $nama,
            'jk'                      => $jk,
            'tmpt_lahir'              => $tmpt,
            'tgl_lahir'               => $tgl,
            'photo'                   => $local_foto_url,
            'status_kepegawaian'      => $status_kepegawaian,
            'ikatan_kerja_dosen'      => $ikatan_kerja_dosen,
            'unit_kerja'              => $unit_kerja,
            'perguruan_tinggi'        => $perguruan_tinggi,
            'tanggal_mulai_penugasan' => $tanggal_mulai_penugasan,
            // Kependudukan
            'nik'                     => isset($kep->nik)          ? trim((string)$kep->nik)          : null,
            'id_agama'                => $kep->id_agama            ?? null,
            'kewarganegaraan'         => isset($kep->kode_negara)  ? trim((string)$kep->kode_negara)  : null,
            // Keluarga
            'stat_kawin'              => $kel->id_status_kawin     ?? null,
            'nm_suami_istri'          => isset($kel->nama_pasangan) ? trim((string)$kel->nama_pasangan) : null,
            'nip_suami_istri'         => isset($kel->nip_pasangan)  ? trim((string)$kel->nip_pasangan)  : null,
            'id_pekerjaan_suami_istri'=> $kel->id_pekerjaan_pasangan ?? null,
            // Alamat
            'jln'                     => isset($alm->alamat)          ? trim((string)$alm->alamat)          : null,
            'rt'                      => $alm->rt                     ?? null,
            'rw'                      => $alm->rw                     ?? null,
            'nm_dsn'                  => isset($alm->dusun)            ? trim((string)$alm->dusun)            : null,
            'ds_kel'                  => isset($alm->kelurahan)        ? trim((string)$alm->kelurahan)        : null,
            'id_kabupaten'            => isset($alm->id_kota_kabupaten)? trim((string)$alm->id_kota_kabupaten): null,
            'kode_pos'                => isset($alm->kode_pos)         ? trim((string)$alm->kode_pos)         : null,
            'no_tel_rmh'              => isset($alm->telepon_rumah)    ? trim((string)$alm->telepon_rumah)    : null,
            'no_hp'                   => isset($alm->telepon_hp)       ? trim((string)$alm->telepon_hp)       : null,
            'email'                   => isset($alm->email)            ? trim((string)$alm->email)            : null,
            // Kepegawaian & Lainnya
            'nip'                     => isset($peg->nip)       ? trim((string)$peg->nip)       : null,
            'sk_cpns'                 => isset($peg->sk_cpns)   ? trim((string)$peg->sk_cpns)   : null,
            'tgl_sk_cpns'             => $peg->tanggal_sk_cpns  ?? null,
            'id_sumber_gaji'          => $peg->id_sumber_gaji   ?? null,
            'npwp'                    => isset($lan->npwp)       ? trim((string)$lan->npwp)      : null,
            'gelar_akademik'          => $gelar_akademik,
            'pendidikan_terakhir'     => $pendidikan_terakhir,
            'last_sync'               => date('Y-m-d H:i:s'),
        ];

        // Ambil nama ibu kandung dari siakad_dosen jika ada
        $sd_row = $this->db->select('nm_ibu_kandung')->where('id_ptk_feeder', $id_sdm)->get('siakad_dosen')->row();
        if ($sd_row && !empty($sd_row->nm_ibu_kandung)) {
            $data_db['nm_ibu_kandung'] = $sd_row->nm_ibu_kandung;
        }

        // --- Fingerprint check profil utama sebelum update ---
        $exist = $this->db->where('id_sdm', $id_sdm)->get('sister_profile_dosen')->row();
        if ($exist) {
            // Kunci-kunci yang sering berubah untuk fingerprint
            $fp_keys = ['nm_ptk','jk','tmpt_lahir','tgl_lahir','nik','stat_kawin','nip','status_kepegawaian'];
            $fp_new  = $this->_data_fingerprint($data_db, $fp_keys);
            $exist_arr = (array)$exist;
            $fp_old  = $this->_data_fingerprint($exist_arr, $fp_keys);

            if (!$force && $fp_new === $fp_old) {
                // Data identik — hanya perbarui last_sync
                $this->db->where('id_sdm', $id_sdm)->update('sister_profile_dosen', ['last_sync' => date('Y-m-d H:i:s')]);
                log_message('info', "SISTER PROFIL IDENTIK (fingerprint sama): {$id_dosen}");
            } else {
                if (!$local_foto_url) unset($data_db['photo']); // jangan timpa foto lama jika gagal download
                $this->db->where('id_sdm', $id_sdm)->update('sister_profile_dosen', $data_db);
                log_message('info', "SISTER PROFIL UPDATED: {$id_dosen}");
            }
        } else {
            $this->db->insert('sister_profile_dosen', $data_db);
            log_message('info', "SISTER PROFIL INSERTED: {$id_dosen}");
        }

        // --- Sync Bidang Ilmu (fingerprint check) ---
        if (is_array($bid)) {
            $bid_db  = $this->db->select('id_kelompok_bidang, kelompok_bidang, urutan')->where('id_sdm', $id_sdm)->get('sister_bidang_ilmu_dosen')->result_array();
            $fp_bid_api = md5(serialize(array_map(fn($b) => [(string)($b->id_kelompok_bidang ?? ''), (string)($b->urutan ?? '')], $bid)));
            $fp_bid_db  = md5(serialize(array_map(fn($r) => [(string)($r['id_kelompok_bidang'] ?? ''), (string)($r['urutan'] ?? '')], $bid_db)));

            if ($force || $fp_bid_api !== $fp_bid_db) {
                $this->db->where('id_sdm', $id_sdm)->delete('sister_bidang_ilmu_dosen');
                $batch_bidang = [];
                foreach ($bid as $b) {
                    $batch_bidang[] = [
                        'id_sdm'             => $id_sdm,
                        'urutan'             => $b->urutan             ?? null,
                        'id_kelompok_bidang' => $b->id_kelompok_bidang ?? null,
                        'kelompok_bidang'    => $b->kelompok_bidang    ?? null,
                        'last_sync'          => date('Y-m-d H:i:s'),
                    ];
                }
                if (!empty($batch_bidang)) {
                    $this->db->insert_batch('sister_bidang_ilmu_dosen', $batch_bidang);
                }
            }
        }

        // --- Sync Jabatan Fungsional (fingerprint check) ---
        if (is_array($jab)) {
            $jab_db   = $this->db->select('id_riwayat, jabatan_fungsional, tanggal_mulai')->where('id_sdm', $id_sdm)->get('sister_jabatan_fungsional_dosen')->result_array();
            $fp_jab_api = md5(serialize(array_map(fn($j) => [(string)($j->id ?? ''), (string)($j->jabatan_fungsional ?? '')], $jab)));
            $fp_jab_db  = md5(serialize(array_map(fn($r) => [(string)($r['id_riwayat'] ?? ''), (string)($r['jabatan_fungsional'] ?? '')], $jab_db)));

            if ($force || $fp_jab_api !== $fp_jab_db) {
                $this->db->where('id_sdm', $id_sdm)->delete('sister_jabatan_fungsional_dosen');
                $batch_jab = [];
                foreach ($jab as $j) {
                    $batch_jab[] = [
                        'id_sdm'            => $id_sdm,
                        'id_riwayat'        => $j->id              ?? null,
                        'jabatan_fungsional'=> $j->jabatan_fungsional ?? null,
                        'sk'                => $j->sk              ?? null,
                        'tanggal_mulai'     => $j->tanggal_mulai   ?? null,
                        'last_sync'         => date('Y-m-d H:i:s'),
                    ];
                }
                if (!empty($batch_jab)) {
                    $this->db->insert_batch('sister_jabatan_fungsional_dosen', $batch_jab);
                }
            }
        }
    }

    /**
     * Ambil dan simpan data Tridharma (pengajaran/penelitian/pengabdian).
     * Smart Sync:
     *  - Cek threshold 180 hari. Jika masih fresh dan bukan force, skip.
     *  - Fingerprint check: bandingkan jumlah + hash judul+tahun sebelum delete+insert.
     *
     * @param string $token
     * @param string $id_dosen  Username
     * @param string $id_sdm    id_ptk_feeder
     * @param string $jenis     pengajaran | penelitian | pengabdian
     * @param bool   $force     TRUE = bypass threshold
     */
    private function _fetch_and_save_data($token, $id_dosen, $id_sdm, $jenis, $force = false) {
        $tabel = 'sister_' . $jenis;

        // --- Threshold check: Tridharma cek setiap 180 hari ---
        if (!$force) {
            $latest = $this->db
                ->select('last_sync')
                ->where('id_dosen', $id_sdm)
                ->order_by('last_sync', 'DESC')
                ->limit(1)
                ->get($tabel)
                ->row();
            $last_sync = $latest ? ($latest->last_sync ?? null) : null;
            if ($this->_is_sync_fresh($last_sync, 180)) {
                log_message('info', "SISTER {$jenis} SKIP (fresh 180d): {$id_dosen}");
                return;
            }
        }

        $url = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/' . $jenis . '?id_sdm=' . urlencode($id_sdm);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            log_message('error', "SISTER API Error: HTTP {$http_code} URL: {$url}");
            return;
        }

        if (!$response) return;

        $data = json_decode($response);
        if (empty($data) || !is_array($data)) return;

        // --- Fingerprint check: bandingkan data API vs DB sebelum delete+insert ---
        $data_lokal = $this->db->select('nama_mata_kuliah, judul_penelitian, judul_pengabdian, tahun, tahun_ajaran, semester')
            ->where('id_dosen', $id_sdm)
            ->order_by('id', 'ASC')
            ->get($tabel)
            ->result_array();

        // Buat fingerprint dari data API (judul + tahun + count)
        if ($jenis === 'pengajaran') {
            $fp_api = md5(count($data) . '|' . implode('|', array_map(fn($i) => ($i->mata_kuliah ?? '') . ($i->semester ?? ''), $data)));
            $fp_db  = md5(count($data_lokal) . '|' . implode('|', array_map(fn($r) => ($r['nama_mata_kuliah'] ?? '') . ($r['semester'] ?? ''), $data_lokal)));
        } elseif ($jenis === 'penelitian') {
            $fp_api = md5(count($data) . '|' . implode('|', array_map(fn($i) => ($i->judul ?? '') . ($i->tahun_pelaksanaan ?? ''), $data)));
            $fp_db  = md5(count($data_lokal) . '|' . implode('|', array_map(fn($r) => ($r['judul_penelitian'] ?? '') . ($r['tahun'] ?? ''), $data_lokal)));
        } else { // pengabdian
            $fp_api = md5(count($data) . '|' . implode('|', array_map(fn($i) => ($i->judul ?? '') . ($i->tahun_pelaksanaan ?? ''), $data)));
            $fp_db  = md5(count($data_lokal) . '|' . implode('|', array_map(fn($r) => ($r['judul_pengabdian'] ?? '') . ($r['tahun'] ?? ''), $data_lokal)));
        }

        if (!$force && $fp_api === $fp_db) {
            // Data identik — hanya perbarui last_sync record pertama
            $this->db->where('id_dosen', $id_sdm)->set('last_sync', date('Y-m-d H:i:s'))->update($tabel);
            log_message('info', "SISTER {$jenis} IDENTIK (fingerprint sama): {$id_dosen}");
            return;
        }

        // Data berbeda — delete + insert batch
        $this->db->where('id_dosen', $id_sdm)->delete($tabel);
        $batch = [];

        if ($jenis === 'pengajaran') {
            foreach ($data as $item) {
                $smt = is_object($item) ? ($item->semester ?? '') : ($item['semester'] ?? '');
                $mk  = is_object($item) ? ($item->mata_kuliah ?? '') : ($item['mata_kuliah'] ?? '');
                $sks = is_object($item) ? ($item->sks ?? 0) : ($item['sks'] ?? 0);
                $batch[] = [
                    'id_dosen'         => $id_sdm,
                    'nama_mata_kuliah' => $mk,
                    'sks'              => intval($sks),
                    'semester'         => $smt,
                    'tahun_ajaran'     => substr((string)$smt, 0, 4) . '/' . (intval(substr((string)$smt, 0, 4)) + 1),
                    'last_sync'        => date('Y-m-d H:i:s'),
                ];
            }
        } elseif ($jenis === 'penelitian') {
            foreach ($data as $item) {
                $judul = is_object($item) ? ($item->judul ?? '') : ($item['judul'] ?? '');
                $thn   = is_object($item) ? ($item->tahun_pelaksanaan ?? null) : ($item['tahun_pelaksanaan'] ?? null);
                $batch[] = [
                    'id_dosen'         => $id_sdm,
                    'judul_penelitian' => $judul,
                    'tahun'            => $thn ? intval($thn) : null,
                    'peran'            => '-', // Tidak tersedia di response summary GET /penelitian
                    'sumber_dana'      => '-', // Tidak tersedia di response summary GET /penelitian
                    'last_sync'        => date('Y-m-d H:i:s'),
                ];
            }
        } elseif ($jenis === 'pengabdian') {
            foreach ($data as $item) {
                $judul = is_object($item) ? ($item->judul ?? '') : ($item['judul'] ?? '');
                $thn   = is_object($item) ? ($item->tahun_pelaksanaan ?? null) : ($item['tahun_pelaksanaan'] ?? null);
                $batch[] = [
                    'id_dosen'          => $id_sdm,
                    'judul_pengabdian'  => $judul,
                    'tahun'             => $thn ? intval($thn) : null,
                    'peran'             => '-', // Tidak tersedia di response summary GET /pengabdian
                    'mitra'             => '-', // Tidak tersedia di response summary GET /pengabdian
                    'last_sync'         => date('Y-m-d H:i:s'),
                ];
            }
        }

        if (!empty($batch)) {
            $this->db->insert_batch($tabel, $batch);
            log_message('info', "SISTER {$jenis} UPDATED: {$id_dosen} total=" . count($batch));
        }
    }

    /**
     * Ambil dan simpan data Publikasi Ilmiah dosen dari SISTER API.
     * Endpoint: GET publikasi?id_sdm={id_sdm}
     * Detail:   GET publikasi/{id} (paralel)
     *
     * @param string $token
     * @param string $id_sdm
     * @param bool   $force
     */
    private function _fetch_and_save_publikasi($token, $id_sdm, $force = false) {
        $base = 'https://sister-api.kemdiktisaintek.go.id/ws-sandbox.php/1.0/';
        $url  = $base . 'publikasi?id_sdm=' . urlencode($id_sdm);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200 || !$response) {
            log_message('error', "SISTER Publikasi API Error: HTTP {$http_code} URL: {$url}");
            return;
        }

        $list = json_decode($response);
        if (!is_array($list)) return;

        // Ambil detail publikasi secara paralel untuk kelengkapan jurnal, penerbit, doi, issn, penulis
        $detail_urls = [];
        foreach ($list as $p) {
            $id_pub = is_object($p) ? ($p->id ?? '') : ($p['id'] ?? '');
            if ($id_pub) {
                $detail_urls[$id_pub] = $base . 'publikasi/' . urlencode($id_pub);
            }
        }

        $detail_results = !empty($detail_urls)
            ? $this->_sister_curl_parallel($token, $detail_urls)
            : [];

        $this->db->where('id_sdm', $id_sdm)->delete('sister_publikasi_dosen');
        $batch = [];
        foreach ($list as $p) {
            $id_pub = is_object($p) ? ($p->id ?? '') : ($p['id'] ?? '');
            $detail = $detail_results[$id_pub] ?? null;

            $judul       = $detail->judul ?? ($p->judul ?? null);
            $nama_jurnal = $detail->nama_jurnal ?? null;
            $jenis       = $detail->jenis_publikasi ?? ($p->jenis_publikasi ?? null);
            $kategori    = $detail->kategori_kegiatan ?? ($p->kategori_kegiatan ?? null);
            $tanggal     = !empty($detail->tanggal) ? $detail->tanggal : (!empty($p->tanggal) ? $p->tanggal : null);
            $penerbit    = $detail->penerbit ?? null;
            $doi         = $detail->doi ?? null;
            $issn        = $detail->issn ?? ($detail->e_issn ?? null);
            $tautan      = $detail->tautan ?? null;
            $asal_data   = $detail->asal_data ?? ($p->asal_data ?? null);
            $quartile    = $detail->quartile ?? ($p->quartile ?? null);

            // Ringkasan Penulis
            $arr_penulis = [];
            if (isset($detail->penulis) && is_array($detail->penulis)) {
                foreach ($detail->penulis as $pen) {
                    if (!empty($pen->nama)) {
                        $arr_penulis[] = $pen->nama . ($pen->corresponding_author ? ' (Korespondensi)' : '');
                    }
                }
            }
            $penulis_list = !empty($arr_penulis) ? implode(', ', $arr_penulis) : null;

            $batch[] = [
                'id_sdm'            => $id_sdm,
                'id_publikasi'      => $id_pub,
                'judul'             => $judul,
                'nama_jurnal'       => $nama_jurnal,
                'jenis_publikasi'   => $jenis,
                'kategori_kegiatan' => $kategori,
                'tanggal'           => $tanggal,
                'penerbit'          => $penerbit,
                'doi'               => $doi,
                'issn'              => $issn,
                'tautan'            => $tautan,
                'asal_data'         => $asal_data,
                'quartile'          => $quartile ? (string)$quartile : null,
                'penulis_list'      => $penulis_list,
                'last_sync'         => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($batch)) {
            $this->db->insert_batch('sister_publikasi_dosen', $batch);
            log_message('info', "SISTER Publikasi UPDATED: {$id_sdm} total=" . count($batch));
        }
    }

    // ==========================================
    // ENDPOINTS UNTUK AJAX DATATABLES (SERVER-SIDE)
    // ==========================================
    
    private function _get_datatables_ssp($table, $column_search, $column_order, $where = null) {
        $start = $this->input->post('start') ?: 0;
        $length = $this->input->post('length') ?: 10;
        $search = $this->input->post('search')['value'] ?? '';
        
        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }

        // Search
        $i = 0;
        foreach ($column_search as $item) {
            if ($search) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search);
                } else {
                    $this->db->or_like($item, $search);
                }
                if (count($column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        // Order
        if (isset($_POST['order'])) {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id', 'DESC'); // Default order
        }
        
        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    private function _count_all_ssp($table, $where = null) {
        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    private function _count_filtered_ssp($table, $column_search, $where = null) {
        $search = $this->input->post('search')['value'] ?? '';
        $this->db->from($table);
        if ($where) {
            $this->db->where($where);
        }
        
        $i = 0;
        foreach ($column_search as $item) {
            if ($search) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search);
                } else {
                    $this->db->or_like($item, $search);
                }
                if (count($column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }
        return $this->db->count_all_results();
    }

    public function ajax_list_pengajaran() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_pengajaran';
        $column_order = [null, 'nama_mata_kuliah', 'sks', 'semester', 'tahun_ajaran', 'last_sync'];
        $column_search = ['nama_mata_kuliah', 'semester', 'tahun_ajaran'];
        $where = ['id_dosen' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_penelitian() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_penelitian';
        $column_order = [null, 'judul_penelitian', 'peran', 'tahun', 'sumber_dana', 'last_sync'];
        $column_search = ['judul_penelitian', 'tahun', 'sumber_dana'];
        $where = ['id_dosen' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_pengabdian() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_pengabdian';
        $column_order = [null, 'judul_pengabdian', 'peran', 'tahun', 'mitra', 'last_sync'];
        $column_search = ['judul_pengabdian', 'tahun', 'mitra'];
        $where = ['id_dosen' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_bkd() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_bkd_laporan';
        $column_order = [null, 'id_smt', 'sks_total', 'sks_ajar', 'sks_lit', 'sks_pengmas', 'sks_penunjang', 'stat_kewajiban', 'simpulan_asesor', 'last_sync'];
        $column_search = ['id_smt', 'stat_kewajiban', 'simpulan_asesor'];
        $where = ['id_sdm' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $smt_label = function($id_smt) {
            if (!$id_smt) return '-';
            $tahun = substr((string)$id_smt, 0, 4);
            $sem   = substr((string)$id_smt, 4, 1);
            return $tahun . '/' . ((int)$tahun + 1) . ' ' . ($sem == '1' ? 'Ganjil' : 'Genap');
        };

        foreach ($list as $item) {
            $item->label_smt = $smt_label($item->id_smt);
        }

        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_inpassing() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_inpassing_dosen';
        $column_order = [null, 'pangkat_golongan', 'sk', 'tanggal_sk', 'tanggal_mulai', 'angka_kredit', 'masa_kerja_tahun', 'last_sync'];
        $column_search = ['pangkat_golongan', 'sk', 'tanggal_sk'];
        $where = ['id_sdm' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_publikasi() {
        if (!$this->input->is_ajax_request()) show_404();
        $user = $this->ion_auth->user()->row();
        $dosen_data = $this->db->select('id_ptk_feeder')->where('id_ptk', $user->id_ptk)->get('siakad_dosen')->row();
        $id_sdm = $dosen_data ? $dosen_data->id_ptk_feeder : '';
        
        $table = 'sister_publikasi_dosen';
        $column_order = [null, 'judul', 'jenis_publikasi', 'nama_jurnal', 'tanggal', 'penerbit', 'last_sync'];
        $column_search = ['judul', 'nama_jurnal', 'jenis_publikasi', 'penerbit', 'doi', 'issn', 'penulis_list'];
        $where = ['id_sdm' => $id_sdm];

        $list = $this->_get_datatables_ssp($table, $column_search, $column_order, $where);
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->_count_all_ssp($table, $where),
            "recordsFiltered" => $this->_count_filtered_ssp($table, $column_search, $where),
            "data" => $list,
        ];
        echo json_encode($output);
    }

    public function ajax_list_eksekutif() {
        if (!$this->input->is_ajax_request() || !$this->_is_gege_satya()) show_404();
        
        $search = $this->input->post('search')['value'] ?? '';
        $start = $this->input->post('start') ?: 0;
        $length = $this->input->post('length') ?: 10;
        
        // Base Query
        $base_sql = "
            FROM sister_profile_dosen spd
            JOIN siakad_dosen sd ON spd.id_sdm COLLATE utf8mb4_unicode_ci = sd.id_ptk_feeder COLLATE utf8mb4_unicode_ci
            JOIN users u ON sd.id_ptk COLLATE utf8mb4_unicode_ci = u.id_ptk COLLATE utf8mb4_unicode_ci
            WHERE 1=1
        ";
        
        if (!empty($search)) {
            $base_sql .= " AND (spd.nm_ptk LIKE '%".$this->db->escape_like_str($search)."%' ESCAPE '!' OR u.username LIKE '%".$this->db->escape_like_str($search)."%' ESCAPE '!')";
        }
        
        // Sorting eksekutif manual
        $order_col = 'u.username';
        $order_dir = 'ASC';
        if (isset($_POST['order'])) {
            $cols = ['u.username', 'spd.nm_ptk', 'total_pengajaran', 'total_penelitian', 'total_pengabdian', 'last_sync'];
            $idx = $_POST['order']['0']['column'];
            $order_col = $cols[$idx] ?? 'u.username';
            $order_dir = $_POST['order']['0']['dir'];
        }
        
        $sql = "
            SELECT u.username, spd.nm_ptk as first_name, '' as last_name, 
            (SELECT COUNT(*) FROM sister_pengajaran p WHERE p.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) as total_pengajaran,
            (SELECT COUNT(*) FROM sister_penelitian pen WHERE pen.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) as total_penelitian,
            (SELECT COUNT(*) FROM sister_pengabdian peng WHERE peng.id_dosen COLLATE utf8mb4_unicode_ci = spd.id_sdm COLLATE utf8mb4_unicode_ci) as total_pengabdian,
            spd.last_sync as last_sync
            " . $base_sql . "
            ORDER BY " . $order_col . " " . $order_dir . "
            LIMIT " . $start . ", " . $length;
            
        $data = $this->db->query($sql)->result();
        
        $total_records = $this->db->query("SELECT COUNT(spd.id_sdm) as count FROM sister_profile_dosen spd JOIN siakad_dosen sd ON spd.id_sdm COLLATE utf8mb4_unicode_ci = sd.id_ptk_feeder COLLATE utf8mb4_unicode_ci JOIN users u ON sd.id_ptk COLLATE utf8mb4_unicode_ci = u.id_ptk COLLATE utf8mb4_unicode_ci")->row()->count;
        $total_filtered = $this->db->query("SELECT COUNT(spd.id_sdm) as count " . $base_sql)->row()->count;
        
        $output = [
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_filtered,
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function _get_agama($id_agama) {
        $agama_map = [
            '1' => 'Islam', '2' => 'Kristen', '3' => 'Katholik', '4' => 'Hindu', '5' => 'Budha', '6' => 'Konghucu', '99' => 'Lainnya'
        ];
        return $agama_map[$id_agama] ?? $id_agama;
    }

    private function _get_stat_kawin($id) {
        $stat = ['1' => 'Kawin', '2' => 'Belum Kawin', '3' => 'Janda/Duda', '4' => 'Cerai'];
        return $stat[$id] ?? $id;
    }

    private function _format_alamat($dosen) {
        $alamat = [];
        if (!empty($dosen['jln'])) $alamat[] = $dosen['jln'];
        
        $rt_rw = '';
        if (!empty($dosen['rt']) || !empty($dosen['rw'])) {
            $rt = !empty($dosen['rt']) ? 'RT ' . $dosen['rt'] : '';
            $rw = !empty($dosen['rw']) ? 'RW ' . $dosen['rw'] : '';
            $rt_rw = trim($rt . ' ' . $rw);
            if ($rt_rw) $alamat[] = $rt_rw;
        }

        if (!empty($dosen['nm_dsn'])) $alamat[] = $dosen['nm_dsn'];
        if (!empty($dosen['ds_kel'])) $alamat[] = 'Kel. ' . $dosen['ds_kel'];
        
        // Remove trailing space for id_kabupaten that SISTER adds
        if (!empty($dosen['id_kabupaten'])) $alamat[] = trim($dosen['id_kabupaten']);
        if (!empty($dosen['kode_pos'])) $alamat[] = trim($dosen['kode_pos']);

        return empty($alamat) ? '-' : implode(', ', $alamat);
    }
}
