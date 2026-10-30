<?php

defined('BASEPATH') or exit('No direct script access allowed');


class C_admin_eoffice_general extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		/* $group = array(13);
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Anda harus admin Student Center');
		} */
	}

	public function ajax_update_download($id_eoffice)
	{
		$this->load->model('Eoffice_general_model', 'Eoffice_general_model');

		$data_eoffice = $this->db->where('id_eoffice', $id_eoffice)->get('e_office')->row();
		$is_boleh_download = $data_eoffice->is_boleh_download;

		if ($is_boleh_download == 'N') {
			$data = [
				'is_boleh_download' => 'Y',
				'change_by' => $this->ion_auth->user()->row()->id,
				'change_date' => date('Y-m-d H:i:s')
			];
		} else {
			$data = [
				'is_boleh_download' => 'N',
				'change_by' => $this->ion_auth->user()->row()->id,
				'change_date' => date('Y-m-d H:i:s')
			];
		}

		$this->Eoffice_general_model->update(['id_eoffice' => $id_eoffice], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_submit_direktur($id_eoffice)
	{
		$this->load->model('Eoffice_general_model', 'Eoffice_general_model');

		$pengguna = $this->ion_auth->user()->row();
		$data = [
			'id_status' => 3,
			'tgl_submit' => date('Y-m-d H:i:s'),
			'approve_1_by' => $pengguna->first_name
		];

		$this->Eoffice_general_model->update(['id_eoffice' => $id_eoffice], $data);
		echo json_encode(["status" => TRUE]);
	}



	public function view_krs($nipd, $tipe = "cetak")
	{
		$this->load->model('Eoffice_general_model', 'Eoffice_general_model');
		$this->Eoffice_general_model->_view_krs($nipd, $tipe);
	}

	public function cetak_pdf_eoffice($id_eoffice)
	{
		$this->load->model('Eoffice_general_model', 'Eoffice_general_model');
		$this->Eoffice_general_model->_cetak_pdf_eoffice($id_eoffice);
	}
}
