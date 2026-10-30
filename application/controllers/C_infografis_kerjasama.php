<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_infografis_kerjasama extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Infografis_kerjasama_model');
        date_default_timezone_set("Asia/Jakarta");

        $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $username = $this->ion_auth->user()->row()->email;
        $data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

        if ($data_akses->num_rows() == 0 && !$this->ion_auth->in_group([5])) {
            $this->output->set_status_header(403);
            
            $output['judul_besar']      = 'Akses Ditolak';
            $output['judul_kecil']      = '';
            $output['isi']              = 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses Infografis Kerjasama WBI.';
            $output['menu_aktif']       = 'm_infografis_kerjasama';
            $output['parent_infografis']= 'active open';
            $output['content']          = 'forbidden_page/forbidden_page_view';
            $output['after_page']       = 'forbidden_page/forbidden_page_after_page';
            
            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }
    }

    public function index()
    {
        $output['judul_besar']      = 'Infografis Kerjasama WBI ';
        $output['judul_kecil']      = 'Politeknik WBI';
        $output['menu_aktif']       = 'm_infografis_kerjasama';
        $output['parent_infografis']= 'active open';
        $output['content']          = 'infografis_kerjasama/infografis_kerjasama_view';
        $output['after_page']       = 'infografis_kerjasama/infografis_kerjasama_after_page';

        $this->load->view('master_page', $output);
    }

    public function generate()
    {
        $tahun = $this->input->get('tahun') ?: 'All';
        $cache_name = "info_kerjasama_gen_v9_" . $tahun;
        $html = $this->cache->get($cache_name);

        if ($html === FALSE) {
            $data['years'] = $this->Infografis_kerjasama_model->get_available_years();
            $data['selected_year'] = $tahun;

            $global_stats = $this->Infografis_kerjasama_model->get_global_stats($tahun);
            $data['total_mou'] = $global_stats->total_mou;
            $data['total_moa'] = $global_stats->total_moa;
            $data['total_instansi'] = $global_stats->total_instansi;
            $data['total_kegiatan'] = $global_stats->total_kegiatan;

            $data['total_mitra_baru'] = $this->Infografis_kerjasama_model->get_mitra_baru($tahun);

            $data['top_instansi'] = $this->Infografis_kerjasama_model->get_top_instansi_kegiatan($tahun);

            $html = $this->load->view('cetak/infografis_kerjasama', $data, TRUE);
            $this->cache->save($cache_name, $html, 7200);
        }

        echo $html;
    }

    public function get_chart_bidang_usaha()
    {
        $tahun = $this->input->get('tahun') ?: 'All';
        $cache_name = "info_krj_chart_bu_v7_" . $tahun;
        $json = $this->cache->get($cache_name);
        if ($json === FALSE) {
            $data = $this->Infografis_kerjasama_model->get_bidang_usaha_mitra($tahun);
            $labels = [];
            $values = [];
            foreach ($data as $row) {
                $labels[] = $row->nama ?: 'Lainnya';
                $values[] = $row->total;
            }
            $json = json_encode([
                'labels' => $labels,
                'values' => $values
            ]);
            $this->cache->save($cache_name, $json, 7200);
        }
        ob_clean();
        return $this->output->set_content_type('application/json')->set_output($json);
    }

    public function get_chart_jenis_instansi()
    {
        $tahun = $this->input->get('tahun') ?: 'All';
        $cache_name = "info_krj_chart_ji_v7_" . $tahun;
        $json = $this->cache->get($cache_name);
        if ($json === FALSE) {
            $data = $this->Infografis_kerjasama_model->get_jenis_instansi_mitra($tahun);
            $labels = [];
            $values = [];
            foreach ($data as $row) {
                $labels[] = $row->nama ?: 'Lainnya';
                $values[] = $row->total;
            }
            $json = json_encode([
                'labels' => $labels,
                'values' => $values
            ]);
            $this->cache->save($cache_name, $json, 7200);
        }
        ob_clean();
        return $this->output->set_content_type('application/json')->set_output($json);
    }

    public function get_chart_mou_moa_monthly()
    {
        $tahun = $this->input->get('tahun') ?: 'All';
        // Only show monthly trend if a specific year is selected
        if ($tahun == 'All') {
            $tahun = date('Y');
        }

        $cache_name = "info_krj_chart_moa_mou_v7_" . $tahun;
        $json = $this->cache->get($cache_name);
        if ($json === FALSE) {
            $data = $this->Infografis_kerjasama_model->get_mou_moa_trend_monthly($tahun);
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            
            $json = json_encode([
                'labels' => $labels,
                'mou' => $data['mou'],
                'moa' => $data['moa']
            ]);
            $this->cache->save($cache_name, $json, 7200);
        }
        ob_clean();
        return $this->output->set_content_type('application/json')->set_output($json);
    }

    public function get_chart_mou_moa_yearly()
    {
        $cache_name = "info_krj_chart_moa_mou_yearly_v7";
        $json = $this->cache->get($cache_name);
        if ($json === FALSE) {
            $data = $this->Infografis_kerjasama_model->get_mou_moa_trend_yearly();
            
            $json = json_encode([
                'labels' => $data['labels'],
                'mou' => $data['mou'],
                'moa' => $data['moa']
            ]);
            $this->cache->save($cache_name, $json, 7200);
        }
        ob_clean();
        return $this->output->set_content_type('application/json')->set_output($json);
    }

    public function cetak_mitra()
    {
        $tahun = $this->input->get('tahun') ?: 'All';
        
        $data['tahun'] = $tahun;
        $data['mitra_list'] = $this->Infografis_kerjasama_model->get_mitra_details($tahun);
        
        $this->load->view('cetak/daftar_mitra_print', $data);
    }
}
