<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_bidang_usaha extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Bidang_usaha_model');
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
		$output['content'] = 'bidang_usaha/bidang_usaha_view';
		$output['after_page'] = 'bidang_usaha/bidang_usaha_after_page';
		$output['judul'] = 'Bidang Usaha';
		$output['judul_page'] = 'Bidang Usaha';

		$this->load->view('master_page_elite', $output);
	}

	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->data_main();

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Bidang_usaha_model->count_all(),
				"recordsFiltered" => $this->Bidang_usaha_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Bidang_usaha_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->kode_bidang_usaha;
			$row[] = $b->nm_bidang_usaha;

			$row[] = '<a class="btn btn-dark btn-xs" 
						 title="Edit" 
						 onclick="edit_bidang_usaha(' . "'" . $b->id_bidang_usaha . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-edit"></i>
					  </a>
					  <a class="btn btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_bidang_usaha(' . "'" . $b->id_bidang_usaha . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-times"></i>
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
			$data = $this->Bidang_usaha_model->get_by_id($id);
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
					'kode_bidang_usaha' 	=> form_error('kode_bidang_usaha'),
					'nm_bidang_usaha' 	=> form_error('nm_bidang_usaha'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'kode_bidang_usaha' => $this->input->post('kode_bidang_usaha'),
					'nm_bidang_usaha' => $this->input->post('nm_bidang_usaha'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Bidang_usaha_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Bidang_usaha_model->delete_by_id($id);
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
					'kode_bidang_usaha' 	=> form_error('kode_bidang_usaha'),
					'nm_bidang_usaha' 	=> form_error('nm_bidang_usaha'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'kode_bidang_usaha' => $this->input->post('kode_bidang_usaha'),
					'nm_bidang_usaha' => $this->input->post('nm_bidang_usaha'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Bidang_usaha_model->update(['id_bidang_usaha' => $this->input->post('id_bidang_usaha')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('kode_bidang_usaha', 'Kode Bidang usaha', 'trim|required');
			$this->form_validation->set_rules('nm_bidang_usaha', 'Nama Bidang usaha', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('kode_bidang_usaha', 'Kode Bidang usaha', 'trim|required');
			$this->form_validation->set_rules('nm_bidang_usaha', 'Nama Bidang usaha', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		}
	}
}
