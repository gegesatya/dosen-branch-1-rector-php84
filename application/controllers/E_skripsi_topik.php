<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_topik extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_topik_model');

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
		$output['judul_besar'] = 'Topik Skripsi';
		$output['judul_kecil'] = $nm_lemb;
		$output['title'] = 'Topik Skripsi';
		$output['menu_aktif'] = 'm_topik_skripsi';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = '_data/view_topik';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all()
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_topik_model->get_all();
	}

	public function getKategori()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_topik_model->get_kategori());
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_topik_model->get_by_id($id));
	}

	public function delete($id)
	{
		$this->db->where('id_topik', $id);
		$this->db->delete('siakad_skripsi_topik');
		echo json_encode(["status" => TRUE]);
	}

	public function add()
	{
		$this->validation_for = 'add';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate();
		$id_sms = $this->session->userdata('prodi');

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'topik_skripsi' 	=> form_error('topik_skripsi'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$insert = [
				'topik_skripsi'	=> $this->input->post('topik_skripsi'),
				'deskripsi'	=> $this->input->post('deskripsi'),
				'id_sms'			=> $id_sms
			];
			$this->db->insert('siakad_skripsi_topik', $insert);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate();

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'topik_skripsi' 	=> form_error('topik_skripsi'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$update = [
				'topik_skripsi'	=> $this->input->post('topik_skripsi'),
				'deskripsi'	=> $this->input->post('deskripsi'),
			];
			$this->db->where('id_topik', $this->input->post('id_topik'));
			$this->db->update('siakad_skripsi_topik', $update);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	private function _validate()
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('topik_skripsi', 'Topik Skripsi', 'trim|required|min_length[5]|max_length[250]');
		/* 	$this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
		$this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric'); */
	}
}
