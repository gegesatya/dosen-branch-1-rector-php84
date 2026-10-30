<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_rps_kelola extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rps_kelola_model', 'Rps_kelola_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Kelola RPS ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_rps_kelola';
		$output['parent_ws']			= 'active open';
		$output['content'] 			= 'rps_kelola/rps_kelola_view';
		$output['after_page'] 		= 'rps_kelola/rps_kelola_after_page';
		$output['dd_rps'] 		= $this->Rps_kelola_model->f_dropdown_rps();

		$this->load->view('master_page', $output);
	}

	public function save_id_kls_session($id_kls)
	{
		$this->session->set_userdata('id_rps', $id_kls);
		//echo json_encode($this->Rps_kelola_model->f_pertemuan_1());
	}

	public function ajax_list($id_rps = NULL)
	{
		$list = $this->Rps_kelola_model->get_datatables($id_rps);
		$id_ptk = $this->session->userdata('id_ptk');

		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $l->nm_prodi_english;
			$row[] = $l->nm_kls;
			$row[] = '[' . $l->kode_mk . '] ' . $l->nm_mk;
			$row[] = substr($l->nm_kls, 1, 1);
			$row[] = $l->sks_mk * 1;

			/* $mk_prodi = $this->db->where(['id_mk' => $l->id_mk, 'id_kurikulum_sp' => $l->id_kurikulum_sp])
				->get('siakad_rps_mk_prodi'); */

			$mk_prodi = $this->db->where(['id_mk' => $l->id_mk])->get('siakad_rps_mk_prodi');

			if ($mk_prodi->num_rows() > 0) {
				if ($mk_prodi->row()->id_ptk == $id_ptk) {
					$row[] = "<a href='javascript:void(0)' onclick='edit_mk_prodi(\"delete\", \"" . $id_rps . "\",\"" . $l->id_mk . "\")'>" . $this->convert_icon(1) . "</a>";
				} else {
					$row[] = '-';
				}
			} else {
				if ($this->get_mk_prodi($id_rps, $l->id_mk)) {
					$row[] = "<a href='javascript:void(0)' onclick='edit_mk_prodi(\"delete\", \"" . $id_rps . "\",\"" . $l->id_mk . "\")'>" . $this->convert_icon(1) . "</a>";
				} else {
					$row[] = "<a href='javascript:void(0)' onclick='edit_mk_prodi(\"insert\", \"" . $id_rps . "\",\"" . $l->id_mk . "\")'>" . $this->convert_icon(0) . "</a>";
				}
			}

			if ($mk_prodi->num_rows() == 0) {
				$row[] = '';
			} else {
				if (!empty($mk_prodi->row()->id_ptk)) {
					$a = $this->db->where(['id_ptk' => $mk_prodi->row()->id_ptk])->get('siakad_dosen')->row()->nm_ptk;
					$row[] = $a;
				} else {
					$row[] = '';
				}
			}


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Rps_kelola_model->count_all($id_rps),
			"recordsFiltered" => $this->Rps_kelola_model->count_filtered($id_rps),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function edit_mk_prodi($tipe, $id_rps, $id_mk)
	{
		if ($tipe == 'insert') {
			$data = [
				'id_rps' => $id_rps, 
				'id_mk' => $id_mk,
				'id_ptk' => $this->session->userdata('id_ptk')
			];
			$this->Rps_kelola_model->save($data);
		} else {
			//$data = [$pertemuan => 'Hadir'];
			$this->Rps_kelola_model->delete_mk_prodi($id_rps, $id_mk);
		}
	}

	private function get_mk_prodi($id_rps, $id_mk)
	{
		$id_rps = $this->session->userdata('id_rps');
		$mk_prodi = $this->db->where(['id_rps' => $id_rps, 'id_mk' => $id_mk])
			->get('siakad_rps_mk_prodi');
		if ($mk_prodi->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	private function convert_icon($status)
	{
		if ($status == 0) {
			return "<i class='glyphicon glyphicon-remove'></i>";
		} else {
			return "<i class='glyphicon glyphicon-ok'></i>";
		}
	}
}
