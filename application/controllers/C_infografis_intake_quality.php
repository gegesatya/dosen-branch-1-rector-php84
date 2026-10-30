<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_infografis_intake_quality extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $username     = $this->ion_auth->user()->row()->email;
        $data_akses   = $this->db->where('username', $username)->get('siakad_akses_data_center');
        $is_kaprodi   = $this->ion_auth->in_group([5]);

        if ($data_akses->num_rows() == 0 && !$is_kaprodi) {
            $this->output->set_status_header(403);

            $output['judul_besar']       = 'Akses Ditolak';
            $output['judul_kecil']       = '';
            $output['isi']               = 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses halaman ini.';
            $output['menu_aktif']        = 'm_intake_quality';
            $output['parent_infografis'] = 'active open';
            $output['content']           = 'forbidden_page/forbidden_page_view';
            $output['after_page']        = 'forbidden_page/forbidden_page_after_page';

            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }

        // Load Cache driver
        $this->load->driver('cache', ['adapter' => 'file']);
    }

    public function index()
    {
        $output['judul_besar']       = 'Intake Quality';
        $output['judul_kecil']       = 'Asal Sekolah Mahasiswa WBI';
        $output['menu_aktif']        = 'm_intake_quality';
        $output['parent_infografis'] = 'active open';
        $output['content']           = 'infografis_intake_quality/infografis_intake_quality_view';
        $output['after_page']        = 'infografis_intake_quality/infografis_intake_quality_after_page';

        $this->load->view('master_page', $output);
    }

    public function generate()
    {
        $tahun_filter = $this->input->get('tahun');

        // ── FIX: Range tahun dinamis dari database (tidak hardcoded) ─────────
        $sql_data_years = "SELECT DISTINCT YEAR(tgl_masuk_sp) as tahun_masuk
            FROM mahasiswa_pt
            WHERE tgl_masuk_sp IS NOT NULL
            ORDER BY tahun_masuk DESC";
        $res_years = $this->db->query($sql_data_years);
        $data_years = ($res_years !== false)
            ? array_column($res_years->result_array(), 'tahun_masuk')
            : [];

        // Build biennial range dinamis dari data yang tersedia
        $angkatan_list = [];
        if (!empty($data_years)) {
            $year_max = (int) max($data_years);
            $year_min = (int) min($data_years);
            for ($y = $year_max; $y >= $year_min; $y -= 2) {
                $angkatan_list[] = (object)[
                    'angkatan' => (string)$y,
                    'has_data' => in_array($y, $data_years),
                ];
            }
        }

        $data['angkatan_list'] = $angkatan_list;
        $data['tahun_filter']  = $tahun_filter;

        $this->load->view('infografis_intake_quality/infografis_intake_quality_generate', $data);
    }

    // ── AJAX Endpoints ────────────────────────────────────────────────────────

    private function get_filter_conditions()
    {
        $tahun_filter = $this->input->get('tahun');
        $join  = "";
        $where = "";

        if (!empty($tahun_filter)) {
            $t     = $this->db->escape($tahun_filter);
            $join  = " JOIN mahasiswa_pt pt ON m.id_pd = pt.id_pd ";
            $where = " AND pt.tgl_masuk_sp IS NOT NULL AND YEAR(pt.tgl_masuk_sp) = {$t} ";
        }
        return ['join' => $join, 'where' => $where];
    }

    // ── FIX: Helper untuk response JSON error ─────────────────────────────────
    private function json_error($message = 'Query gagal')
    {
        echo json_encode(['status' => 'error', 'message' => $message]);
    }

    public function ajax_get_kpi()
    {
        // FIX: Set Content-Type JSON
        $this->output->set_content_type('application/json');

        $tahun_filter = $this->input->get('tahun');
        $cache_key    = 'intake_kpi_' . ($tahun_filter ?: 'all');
        if ($cached = $this->cache->get($cache_key)) {
            echo $cached;
            return;
        }

        $filters = $this->get_filter_conditions();
        $join    = $filters['join'];
        $where   = $filters['where'];

        $sql_status = "SELECT d.status, COUNT(DISTINCT d.npsn) as total
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              AND d.status IS NOT NULL AND d.status != ''
              {$where}
            GROUP BY d.status";
        // FIX: Cek hasil query sebelum diproses
        $res_status = $this->db->query($sql_status);
        if ($res_status === false) {
            $this->json_error('Query status gagal');
            return;
        }

        $negeri = 0;
        $swasta = 0;
        foreach ($res_status->result() as $r) {
            $st = strtoupper(trim((string)$r->status));
            if ($st === 'NEGERI' || $st === 'N') $negeri += (int)$r->total;
            if ($st === 'SWASTA' || $st === 'S') $swasta += (int)$r->total;
        }

        $sql_kategori = "SELECT d.bentuk, COUNT(DISTINCT d.npsn) as total
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              AND d.bentuk IS NOT NULL AND d.bentuk != ''
              {$where}
            GROUP BY d.bentuk ORDER BY total DESC";
        // FIX: Cek hasil query sebelum diproses
        $res_kategori = $this->db->query($sql_kategori);
        if ($res_kategori === false) {
            $this->json_error('Query kategori gagal');
            return;
        }

        $sma_ma = 0;
        $smk    = 0;
        foreach ($res_kategori->result() as $r) {
            $k = strtoupper(trim((string)$r->bentuk));
            if ($k === 'SMK') {
                $smk += (int)$r->total;
            } else {
                $sma_ma += (int)$r->total;
            }
        }

        $output = json_encode([
            'status' => 'success',
            'data'   => [
                'negeri' => $negeri,
                'swasta' => $swasta,
                'sma_ma' => $sma_ma,
                'smk'    => $smk
            ]
        ]);
        // FIX: Hanya cache jika berhasil
        $this->cache->save($cache_key, $output, 43200);
        echo $output;
    }

    public function ajax_get_top_schools()
    {
        // FIX: Set Content-Type JSON
        $this->output->set_content_type('application/json');

        $tahun_filter = $this->input->get('tahun');
        $cache_key    = 'intake_top_' . ($tahun_filter ?: 'all');
        if ($cached = $this->cache->get($cache_key)) {
            echo $cached;
            return;
        }

        $filters = $this->get_filter_conditions();
        $join    = $filters['join'];
        $where   = $filters['where'];

        $sql_top_schools = "SELECT d.sekolah as nama_sekolah, COUNT(DISTINCT m.id_pd) as total
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              {$where}
            GROUP BY d.sekolah ORDER BY total DESC LIMIT 5";
        // FIX: Cek hasil query sebelum diproses
        $res_top_schools = $this->db->query($sql_top_schools);
        if ($res_top_schools === false) {
            $this->json_error('Query top sekolah gagal');
            return;
        }

        $output = json_encode([
            'status' => 'success',
            'data'   => $res_top_schools->result()
        ]);
        // FIX: Hanya cache jika berhasil
        $this->cache->save($cache_key, $output, 43200);
        echo $output;
    }

    public function ajax_get_sebaran_provinsi()
    {
        // FIX: Set Content-Type JSON
        $this->output->set_content_type('application/json');

        $tahun_filter = $this->input->get('tahun');
        $cache_key    = 'intake_sebaran_' . ($tahun_filter ?: 'all');
        if ($cached = $this->cache->get($cache_key)) {
            echo $cached;
            return;
        }

        $filters = $this->get_filter_conditions();
        $join    = $filters['join'];
        $where   = $filters['where'];

        $sql_provinsi = "SELECT
                d.propinsi,
                AVG(CAST(d.lintang AS DECIMAL(10,6))) as lat,
                AVG(CAST(d.bujur AS DECIMAL(10,6))) as lng,
                COUNT(DISTINCT m.id_pd) as jumlah_mahasiswa,
                COUNT(DISTINCT d.npsn) as jumlah_sekolah
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              AND d.lintang IS NOT NULL AND d.lintang != '0' AND d.lintang != ''
              AND d.bujur IS NOT NULL AND d.bujur != '0' AND d.bujur != ''
              AND d.propinsi IS NOT NULL AND d.propinsi != ''
              {$where}
            GROUP BY d.propinsi
            ORDER BY jumlah_mahasiswa DESC";
        // FIX: Cek hasil query sebelum diproses
        $res_provinsi = $this->db->query($sql_provinsi);
        if ($res_provinsi === false) {
            $this->json_error('Query sebaran provinsi gagal');
            return;
        }

        $output = json_encode([
            'status' => 'success',
            'data'   => $res_provinsi->result()
        ]);
        // FIX: Hanya cache jika berhasil
        $this->cache->save($cache_key, $output, 43200);
        echo $output;
    }

    public function ajax_get_semua_sekolah()
    {
        // FIX: Set Content-Type JSON
        $this->output->set_content_type('application/json');

        $tahun_filter = $this->input->get('tahun');
        $cache_key    = 'intake_semua_' . ($tahun_filter ?: 'all');
        if ($cached = $this->cache->get($cache_key)) {
            echo $cached;
            return;
        }

        $filters = $this->get_filter_conditions();
        $join    = $filters['join'];
        $where   = $filters['where'];

        $sql_semua_sekolah = "SELECT
                MIN(d.npsn) as npsn,
                d.sekolah,
                d.kabupaten_kota,
                d.propinsi,
                COUNT(DISTINCT m.id_pd) as total
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              {$where}
            GROUP BY d.sekolah, d.kabupaten_kota, d.propinsi
            ORDER BY total DESC";
        // FIX: Cek hasil query sebelum diproses
        $res_semua_sekolah = $this->db->query($sql_semua_sekolah);
        if ($res_semua_sekolah === false) {
            $this->json_error('Query semua sekolah gagal');
            return;
        }

        $output = json_encode([
            'status' => 'success',
            'data'   => $res_semua_sekolah->result()
        ]);
        // FIX: Hanya cache jika berhasil
        $this->cache->save($cache_key, $output, 43200);
        echo $output;
    }

    // ── AJAX Endpoint: Detail Sekolah per Provinsi ────────────────────────
    public function get_sekolah_by_provinsi()
    {
        // FIX: Set Content-Type JSON
        $this->output->set_content_type('application/json');

        $propinsi     = $this->input->get('propinsi');
        $tahun_filter = $this->input->get('tahun');

        if (empty($propinsi)) {
            $this->json_error('Propinsi diperlukan');
            return;
        }

        $p = $this->db->escape($propinsi);
        // Using md5 to sanitize propinsi for cache key
        $cache_key = 'intake_detail_' . ($tahun_filter ?: 'all') . '_' . md5($propinsi);
        if ($cached = $this->cache->get($cache_key)) {
            echo $cached;
            return;
        }

        $filters = $this->get_filter_conditions();
        $join    = $filters['join'];
        $where   = $filters['where'];

        $sql = "SELECT
                d.sekolah,
                d.bentuk,
                AVG(CAST(d.lintang AS DECIMAL(10,6))) as lat,
                AVG(CAST(d.bujur AS DECIMAL(10,6))) as lng,
                COUNT(DISTINCT m.id_pd) as jumlah_mahasiswa
            FROM mahasiswa m
            {$join}
            JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
            WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
              AND d.lintang IS NOT NULL AND d.lintang != '0' AND d.lintang != ''
              AND d.bujur IS NOT NULL AND d.bujur != '0' AND d.bujur != ''
              AND d.propinsi = {$p}
              {$where}
            GROUP BY d.sekolah, d.bentuk
            ORDER BY jumlah_mahasiswa DESC";

        // FIX: Cek hasil query sebelum diproses
        $result = $this->db->query($sql);
        if ($result === false) {
            $this->json_error('Query detail provinsi gagal');
            return;
        }

        $output = json_encode([
            'status' => 'success',
            'data'   => $result->result()
        ]);
        // FIX: Hanya cache jika berhasil
        $this->cache->save($cache_key, $output, 43200);
        echo $output;
    }

    // ── Halaman Cetak Mahasiswa ───────────────────────────────────────────────
    public function print_mahasiswa()
    {
        $tahun_filter = $this->input->get('tahun');
        $npsn         = $this->input->get('npsn');

        $where_tahun = "";
        $tahun_label = "Semua Angkatan";

        // FIX: Gunakan LEFT JOIN agar mahasiswa tanpa data mahasiswa_pt tetap muncul
        if (!empty($tahun_filter)) {
            $t           = $this->db->escape($tahun_filter);
            $join        = " JOIN mahasiswa_pt pt ON pt.id_pd = m.id_pd ";
            $where_tahun = " AND pt.tgl_masuk_sp IS NOT NULL AND YEAR(pt.tgl_masuk_sp) = {$t} ";
            $tahun_label = "Angkatan " . htmlspecialchars($tahun_filter);
        } else {
            // LEFT JOIN agar mahasiswa tanpa record mahasiswa_pt tetap tampil
            $join = " LEFT JOIN mahasiswa_pt pt ON pt.id_pd = m.id_pd ";
        }

        $where_npsn   = "";
        $nama_sekolah = "Semua Sekolah";
        if (!empty($npsn)) {
            $n          = $this->db->escape($npsn);
            $where_npsn = " AND m.npsn_sekolah_asal = {$n} ";

            // Cari nama sekolah
            $sch = $this->db->query("SELECT sekolah FROM dapo_sekolah WHERE npsn = {$n} LIMIT 1")->row();
            if ($sch) {
                $nama_sekolah = $sch->sekolah;
            }
        }

        $sql = "SELECT
                    pt.nipd as nim,
                    m.nm_pd as nama,
                    pt.ipk,
                    d.sekolah as nama_sekolah
                FROM mahasiswa m
                {$join}
                JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
                WHERE m.npsn_sekolah_asal IS NOT NULL
                  AND m.npsn_sekolah_asal != ''
                  {$where_tahun}
                  {$where_npsn}
                ORDER BY pt.nipd ASC";

        $data['mahasiswa']    = $this->db->query($sql)->result();
        $data['tahun_label']  = $tahun_label;
        $data['nama_sekolah'] = $nama_sekolah;

        $this->load->view('infografis_intake_quality/infografis_intake_quality_print', $data);
    }
}
