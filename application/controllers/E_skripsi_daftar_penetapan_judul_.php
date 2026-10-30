<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_penetapan_judul extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_daftar_penetapan_judul_model');

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
		$output['judul_besar'] = 'Penetapan Judul dan Dosen Pembimbing';
		$output['judul_kecil'] = $nm_lemb;
		$output['title'] = 'Penetapan Judul';
		$output['menu_aktif'] = 'm_daftar_penetapan_judul';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = '_data/view_skripsi_daftar_penetapan_judul';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all()
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_daftar_penetapan_judul_model->get_all();
	}

	public function getDosen()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_penetapan_judul_model->get_dosen());
	}

	public function getDosenPenguji()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_penetapan_judul_model->get_dosen_penguji());
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_penetapan_judul_model->get_by_id($id));
	}

	public function delete($id)
	{
		$this->db->where('id_daftar_dosen', $id);
		$this->db->delete('siakad_skripsi_dosen');
		echo json_encode(["status" => TRUE]);
	}

	/* public function add()
	{
		$this->validation_for = 'add';
		$data = array();
		$data['status'] = TRUE;

		$this->_validate('add');
		$id_sms = $this->session->userdata('prodi');

		if ($this->form_validation->run() == FALSE) {
			$errors = array(
				'id_ptk' 	=> form_error('id_ptk'),
			);
			$data = array(
				'status' 		=> FALSE,
				'errors' 		=> $errors
			);
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$insert = array(
				'id_ptk'	=> $this->input->post('id_ptk'),
				'is_pembimbing'	=> $this->input->post('is_pembimbing') == TRUE ? 'Y' : 'N',
				'is_penguji'	=> $this->input->post('is_penguji') == TRUE ? 'Y' : 'N',
				'id_sms'			=> $id_sms
			);
			$this->db->insert('siakad_skripsi_dosen', $insert);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	} */

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate();
		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'judul_ditetapkan' 	=> form_error('judul_ditetapkan'),
				//'dosen_pembimbing_1' 	=> form_error('dosen_pembimbing_1'),
				//'dosen_penguji' 	=> form_error('dosen_penguji'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$update = [
				'judul_ditetapkan'	=> $this->input->post('judul_ditetapkan'),
				//'dosen_pembimbing_1' => $this->input->post('dosen_pembimbing_1'),
				//'dosen_pembimbing_2' => $this->input->post('dosen_pembimbing_2'),
				//'dosen_penguji' => $this->input->post('dosen_penguji'),
				//'dosen_penguji_2' => $this->input->post('dosen_penguji_2'),
				'status_penetapan_judul' => 1,
				'change_by' => $user->id,
				'change_date' => date('Y-m-d H:i:s')
			];
			$this->db->where('id_judul_skripsi', $this->input->post('id_judul_skripsi'));
			$this->db->update('siakad_skripsi_pengajuan_judul', $update);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	private function _validate($tipe)
	{
		//if ($tipe == 'add') {
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('judul_ditetapkan', 'Judul yang ditetapkan', 'required|trim');
		//$this->form_validation->set_rules('dosen_pembimbing_1', 'Dosen Pembimbing 1', 'required|trim');
		//$this->form_validation->set_rules('dosen_penguji', 'Dosen Penguji', 'required|trim');

		$this->form_validation->set_message('required', '%s harus diisi');
		//} else {
		//	$this->form_validation->set_error_delimiters('', '');
		//}


	}

	public function check_id_ptk($id_ptk)
	{
		$id_sms = $this->session->userdata('prodi');

		$cek = $this->db->where(['id_sms' => $id_sms, 'id_ptk' => $id_ptk])->get('siakad_skripsi_dosen')->num_rows();
		if ($cek) {
			$this->form_validation->set_message('check_id_ptk', 'Dosen hanya boleh ada 1 untuk tiap Prodi');
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
