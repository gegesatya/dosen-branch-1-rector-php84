<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_infografis_lulusan extends CI_Controller
{
    const BATAS_TEPAT_WAKTU = 4;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Infografis_lulusan_model');
		date_default_timezone_set("Asia/Jakarta");

		$this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0 && !$this->ion_auth->in_group([5])) {
			$this->output->set_status_header(403);
			
			$output['judul_besar'] 		= 'Akses Ditolak';
			$output['judul_kecil'] 		= '';
			$output['isi'] 				= 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses Infografis Lulusan.';
			$output['menu_aktif'] 		= 'm_infografis_lulusan';
			$output['parent_infografis'] = 'active open';
			$output['content'] 			= 'forbidden_page/forbidden_page_view';
			$output['after_page'] 		= 'forbidden_page/forbidden_page_after_page';
			
			$this->load->view('master_page', $output);
			echo $this->output->get_output();
			exit;
		}

	}

	public function index()
	{
		$output['judul_besar'] 		= 'Infografis Lulusan ';
		$output['judul_kecil'] 		= 'Politeknik WBI';	
		$output['menu_aktif'] 		= 'm_infografis_lulusan';
		$output['parent_infografis']	= 'active open';
		$output['content'] 			= 'infografis_lulusan/infografis_lulusan_view';
		$output['after_page'] 		= 'infografis_lulusan/infografis_lulusan_after_page';
		$this->load->view('master_page', $output);
	}

	public function generate()
	{
		$id_sms = $this->input->get('id_sms');
		
		$cache_name = "info_lulusan_generate_{$id_sms}";
		$html = $this->cache->get($cache_name);

		if ($html === FALSE) {
			$data['list_prodi'] = $this->Infografis_lulusan_model->get_prodi();
			$data['selected_prodi'] = $id_sms;

			$data['kpi'] = $this->Infografis_lulusan_model->get_kpi_stats($id_sms, self::BATAS_TEPAT_WAKTU);
			$data['demografi'] = $this->Infografis_lulusan_model->get_chart_demografi($id_sms);
			$data['ipk'] = $this->Infografis_lulusan_model->get_chart_ipk($id_sms);
			$data['sorotan'] = $this->Infografis_lulusan_model->get_sorotan_prodi(self::BATAS_TEPAT_WAKTU);
			$data['trend'] = $this->Infografis_lulusan_model->get_trend_kpi($id_sms, self::BATAS_TEPAT_WAKTU);
			$data['mahasiswa_berisiko'] = $this->Infografis_lulusan_model->get_mahasiswa_berisiko($id_sms);
			$data['potensi_yudisium'] = $this->Infografis_lulusan_model->get_potensi_yudisium($id_sms);

			$html = $this->load->view('cetak/infografis_lulusan', $data, TRUE);
			$this->cache->save($cache_name, $html, 7200); // 2 Jam
		}

		echo $html;
	}

	public function cetak_laporan()
	{
		$cache_name = "info_lulusan_cetak_all";
		$html = $this->cache->get($cache_name);

		if ($html === FALSE) {
			// 1. Get Overall Stats (WBI)
			$data['overall'] = $this->get_full_stats(null);

			// 2. Get All Prodis
			$prodis = $this->Infografis_lulusan_model->get_prodi();
			$data['prodi_details'] = [];

			foreach ($prodis as $p) {
				$data['prodi_details'][] = [
					'info' => $p,
					'stats' => $this->get_full_stats($p->id_sms)
				];
			}

			$html = $this->load->view('cetak/laporan_lulusan_print', $data, TRUE);
			$this->cache->save($cache_name, $html, 7200); // 2 Jam
		}

		echo $html;
	}

	private function get_full_stats($id_sms) {
		return (object)[
			'kpi' => $this->Infografis_lulusan_model->get_kpi_stats($id_sms, self::BATAS_TEPAT_WAKTU),
			'trend' => $this->Infografis_lulusan_model->get_trend_kpi($id_sms, self::BATAS_TEPAT_WAKTU),
			'ipk' => $this->Infografis_lulusan_model->get_chart_ipk($id_sms),
			'mahasiswa_berisiko' => $this->Infografis_lulusan_model->get_mahasiswa_berisiko($id_sms),
			'potensi_yudisium' => $this->Infografis_lulusan_model->get_potensi_yudisium($id_sms)
		];
	}
}
