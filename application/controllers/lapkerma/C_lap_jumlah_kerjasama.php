<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_lap_jumlah_kerjasama extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Lap_jumlah_kerjasama_model', 'Lap_jumlah_kerjasama_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [17];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['content'] = 'lapkerma/lap_jumlah_kerjasama/lap_jumlah_kerjasama_view';
		$output['after_page'] = 'lapkerma/lap_jumlah_kerjasama/lap_jumlah_kerjasama_after_page';
		$output['judul_besar'] = 'Jumlah Kerjasama';
		$output['judul_kecil'] = 'dan Download Data';
		$output['menu_aktif'] 		= 'm_download';
		$output['parent_lapkerma']	= 'active open';
		$output['get_data'] = $this->get_data();

		$this->load->view('master_page', $output);
	}

	private function get_data()
	{
		$jumlah_mou = $this->db->get('simlapkerma_tbl_daftar_mou')->num_rows();
		$jumlah_mou_aktif = $this->db->where('id_status_kerjasama', 1)->get('simlapkerma_tbl_daftar_mou')->num_rows();
		$jumlah_mou_expired = $this->db->where('id_status_kerjasama', 2)->get('simlapkerma_tbl_daftar_mou')->num_rows();
		$jumlah_mou_expired_soon = $this->db->where(['id_status_kerjasama' => 1, 'will_expire' => 1])->get('simlapkerma_tbl_daftar_mou')->num_rows();
		$jumlah_mou_not_aktif = $this->db->where('id_status_kerjasama', 4)->get('simlapkerma_tbl_daftar_mou')->num_rows();

		$jumlah_moa = $this->db->get('simlapkerma_tbl_moa')->num_rows();
		$jumlah_moa_aktif = $this->db->where('status_moa', 1)->get('simlapkerma_tbl_moa')->num_rows();
		$jumlah_moa_expired = $this->db->where('status_moa', 2)->get('simlapkerma_tbl_moa')->num_rows();
		$jumlah_moa_expired_soon = $this->db->where(['status_moa' => 1, 'will_expire' => 1])->get('simlapkerma_tbl_moa')->num_rows();
		$jumlah_moa_not_aktif = $this->db->where('status_moa', 4)->get('simlapkerma_tbl_moa')->num_rows();

		$jumlah_instansi_pemerintahan = $this->db->where('si.id_instansi', 1)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_instansi_pendidikan = $this->db->where('si.id_instansi', 2)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_instansi_perusahaan = $this->db->where('si.id_instansi', 3)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_instansi_lainnya = $this->db->where('si.id_instansi', 7)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_instansi_perpus_negeri = $this->db->where('si.id_instansi', 8)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_instansi_perpus_swasta = $this->db->where('si.id_instansi', 9)->from('simlapkerma_tbl_daftar_instansi i')->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi')->get()->num_rows();
		$jumlah_kegiatan = $this->db->get('simlapkerma_tbl_kegiatan')->num_rows();

		return [
			$jumlah_mou_aktif,
			$jumlah_mou_not_aktif,
			$jumlah_mou_expired,
			$jumlah_mou,
			$jumlah_moa_aktif,
			$jumlah_moa_not_aktif,
			$jumlah_moa_expired,
			$jumlah_moa,
			$jumlah_instansi_pemerintahan,
			$jumlah_instansi_pendidikan,
			$jumlah_instansi_perusahaan,
			$jumlah_instansi_lainnya,
			$jumlah_instansi_perpus_negeri,
			$jumlah_instansi_perpus_swasta,
			$jumlah_mou_expired_soon,
			$jumlah_moa_expired_soon
		];
	}

	public function sinkron()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {

			$mou = $this->db->where(['tanggal_akhir <' => date('Y-m-d'), 'id_status_kerjasama' => 1, 'id_status_kerjasama <>' => 4])->update('simlapkerma_tbl_daftar_mou', ['id_status_kerjasama' => 2, 'change_date' => date('Y-m-d H:i:s')]);
			$moa = $this->db->where(['tanggal_akhir_moa <' => date('Y-m-d'), 'status_moa' => 1, 'status_moa <>' => 4])->update('simlapkerma_tbl_moa', ['status_moa' => 2, 'change_date' => date('Y-m-d H:i:s')]);

			$moa_exp_in_3_months = $this->db->query("select id_moa, tanggal_akhir_moa, CURRENT_DATE,
												PERIOD_DIFF (EXTRACT(YEAR_MONTH FROM tanggal_akhir_moa),EXTRACT(YEAR_MONTH FROM CURRENT_DATE)) AS months_diff
												from simlapkerma_tbl_moa where status_moa = 1")->result();

			foreach ($moa_exp_in_3_months as $key => $value) {
				if ($value->months_diff > 0 and $value->months_diff <= 6) {
					$this->db->where('id_moa', $value->id_moa)->update('simlapkerma_tbl_moa', ['will_expire' => 1]);
				}
			}

			$mou_exp_in_3_months = $this->db->query("select id_mou, tanggal_akhir, CURRENT_DATE,
												PERIOD_DIFF (EXTRACT(YEAR_MONTH FROM tanggal_akhir),EXTRACT(YEAR_MONTH FROM CURRENT_DATE)) AS months_diff
												from simlapkerma_tbl_daftar_mou where id_status_kerjasama = 1")->result();

			foreach ($mou_exp_in_3_months as $key => $value) {
				if ($value->months_diff > 0 and $value->months_diff <= 6) {
					$this->db->where('id_mou', $value->id_mou)->update('simlapkerma_tbl_daftar_mou', ['will_expire' => 1]);
				}
			}


			$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
		}
	}

	public function tes()
	{
		$moa_exp_in_3_months = $this->db->query("select tanggal_akhir_moa, CURRENT_DATE,
												PERIOD_DIFF (EXTRACT(YEAR_MONTH FROM tanggal_akhir_moa),EXTRACT(YEAR_MONTH FROM CURRENT_DATE)) AS months_diff
												from simlapkerma_tbl_moa where status_moa = 1")->result();

		var_dump($moa_exp_in_3_months);
	}
}
