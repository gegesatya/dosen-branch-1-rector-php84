<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_daftar_instansi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Daftar_instansi_model', 'Daftar_instansi_model');
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
		$this->load->helper('url');
		$output['content'] 			= 'lapkerma/daftar_instansi/daftar_instansi_view';
		$output['after_page'] 		= 'lapkerma/daftar_instansi/daftar_instansi_after_page';
		$output['judul_besar'] 			= 'Daftar Mitra';
		$output['judul_kecil'] 		= 'WBI';
		$output['menu_aktif'] 		= 'm_daftar_instansi';
		$output['parent_lapkerma']	= 'active open';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->data_main();

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Daftar_instansi_model->count_all(),
				"recordsFiltered" => $this->Daftar_instansi_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Daftar_instansi_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_instansi;
			$row[] = $b->nm_jenis_instansi . ' - ' . $b->nm_sub_instansi;
			$row[] = $b->nm_bidang_usaha;

			$row[] = '<a class="btn btn-primary btn-sm" 
						 title="Edit" 
						 onclick="edit_instansi(\'' . $b->id_daftar_instansi . '\')"
						 href="javascript:void()" >
						 <i class="fa fa-edit"></i> Lihat Data
					  </a>';
			$data[] = $row;
		}

		return $data;
	}

	public function ajax_edit($id)
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->Daftar_instansi_model->get_by_id($id);
			echo json_encode($data);
		}
	}

	public function ajax_add()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'add';
			$this->_validate('add');

			if ($this->form_validation->run() == FALSE) {
				$errors = [

					'nm_instansi' 	=> form_error('nm_instansi'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'nm_instansi' => $this->input->post('nm_instansi'),
					'alamat_instansi' => $this->input->post('alamat_instansi'),
					'telepon' => $this->input->post('telepon'),
					'handphone' => $this->input->post('handphone'),
					'email' => $this->input->post('email'),
					'pic' => $this->input->post('pic'),
					'jabatan' => $this->input->post('jabatan'),
					'id_sub_instansi' => $this->input->post('id_sub_instansi'),
					'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Daftar_instansi_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Daftar_instansi_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		//$this->_validate('edit');

		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'update';
			$this->_validate('update');


			if ($this->form_validation->run() == FALSE) {
				$errors = [

					'nm_instansi' 	=> form_error('nm_instansi'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'nm_instansi' => $this->input->post('nm_instansi'),
					'alamat_instansi' => $this->input->post('alamat_instansi'),
					'telepon' => $this->input->post('telepon'),
					'handphone' => $this->input->post('handphone'),
					'email' => $this->input->post('email'),
					'pic' => $this->input->post('pic'),
					'jabatan' => $this->input->post('jabatan'),
					'id_sub_instansi' => $this->input->post('id_sub_instansi'),
					'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Daftar_instansi_model->update(['id_daftar_instansi' => $this->input->post('id_daftar_instansi')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');

			$this->form_validation->set_rules('nm_instansi', 'Nama Instansi', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		} else {
			$this->form_validation->set_error_delimiters('', '');

			$this->form_validation->set_rules('nm_instansi', 'Nama Instansi', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		}
	}
}
