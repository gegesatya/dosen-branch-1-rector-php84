<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_topik_dosen extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_topik_dosen_model');

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_sms = $this->session->userdata('prodi');
		if (!empty($id_sms)) {
			$nm_lemb = $this->db->where(compact('id_sms'))->get('sms')->row()->nm_lemb;
		}
		$output['judul_besar'] = 'Daftar Topik dan Dosen';
		$output['judul_kecil'] = $nm_lemb;
		$output['title'] = 'Daftar Topik dan Dosen';
		$output['menu_aktif'] = 'm_topik_dosen';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = '_data/view_skripsi_topik_dosen';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all()
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_topik_dosen_model->get_all();
	}

	public function getDosen()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_topik_dosen_model->get_dosen());
	}

	public function getTopik()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_topik_dosen_model->get_topik());
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_topik_dosen_model->get_by_id($id));
	}

	public function delete($id)
	{
		$this->db->where('id_dosen_topik', $id);
		$this->db->delete('siakad_skripsi_topik_dosen');
		echo json_encode(["status" => TRUE]);
	}

	public function add()
	{
		$this->validation_for = 'add';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate('add');
		$id_sms = $this->session->userdata('prodi');

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'id_ptk' 	=> form_error('id_ptk'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$insert = [
				'id_ptk'	=> $this->input->post('id_ptk'),
				'id_topik'	=> $this->input->post('id_topik'),
				'id_sms'	=> $id_sms
			];
			$this->db->insert('siakad_skripsi_topik_dosen', $insert);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate('update');

		//if ($this->form_validation->run() == FALSE) {
		/* $errors = array(
				'id_ptk' 	=> form_error('id_ptk'),
			);
			$data = array(
				'status' 		=> FALSE,
				'errors' 		=> $errors
			);
			$this->output->set_content_type('application/json')->set_output(json_encode($data)); */
		//} else {
		$update = [
			'id_ptk'	=> $this->input->post('id_ptk'),
			'id_topik'	=> $this->input->post('id_topik')
		];
		$this->db->where('id_dosen_topik', $this->input->post('id_dosen_topik'));
		$this->db->update('siakad_skripsi_topik_dosen', $update);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
		//}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_ptk', 'Dosen', 'trim|required');
			$this->form_validation->set_rules('id_topik', 'Topik', 'trim|required');
			//$this->form_validation->set_rules('id_ptk', 'Dosen', 'required|callback_check_id_ptk');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_ptk', 'Dosen', 'trim|required');
			$this->form_validation->set_rules('id_topik', 'Topik', 'trim|required');
		}

		/* 	$this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
		$this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric'); */
	}

	public function check_id_ptk($id_ptk)
	{
		$id_sms = $this->session->userdata('prodi');

		$cek = $this->db->where(['id_sms' => $id_sms, 'id_ptk' => $id_ptk])->get('siakad_skripsi_topik_dosen')->num_rows();
		if ($cek) {
			$this->form_validation->set_message('check_id_ptk', 'Dosen hanya boleh ada 1 untuk tiap Prodi');
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
