<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_sub_instansi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sub_instansi_model');
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
		$output['content'] = 'sub_instansi/sub_instansi_view';
		$output['after_page'] = 'sub_instansi/sub_instansi_after_page';
		$output['judul'] = 'Sub Instansi Kerja Sama';
		$output['judul_page'] = 'Sub Instansi Kerja Sama';

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
				"recordsTotal" => $this->Sub_instansi_model->count_all(),
				"recordsFiltered" => $this->Sub_instansi_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Sub_instansi_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_jenis_instansi;
			$row[] = $b->kode_sub_instansi;
			$row[] = $b->nm_sub_instansi;

			$row[] = '<a class="btn btn-dark btn-xs" 
						 title="Edit" 
						 onclick="edit_sub_instansi(' . "'" . $b->id_sub_instansi . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-edit"></i>
					  </a>
					  <a class="btn btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_sub_instansi(' . "'" . $b->id_sub_instansi . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-times"></i>
					  </a>';



			$data[] = $row;
		}

		return $data;
	}

	public function get_instansi()
	{
		header('Content-Type: application/json');
		echo json_encode($this->Sub_instansi_model->get_instansi());
	}

	public function ajax_edit($id)
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->Sub_instansi_model->get_by_id($id);
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
					'kode_sub_instansi' 	=> form_error('kode_sub_instansi'),
					'nm_sub_instansi' 	=> form_error('nm_sub_instansi'),
					'id_instansi' 	=> form_error('id_instansi'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'id_instansi' => $this->input->post('id_instansi'),
					'kode_sub_instansi' => $this->input->post('kode_sub_instansi'),
					'nm_sub_instansi' => $this->input->post('nm_sub_instansi'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Sub_instansi_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Sub_instansi_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'update';
			$this->_validate('update');


			if ($this->form_validation->run() == FALSE) {
				$errors = [
					'kode_sub_instansi' 	=> form_error('kode_sub_instansi'),
					'nm_sub_instansi' 	=> form_error('nm_sub_instansi'),
					'id_instansi' 	=> form_error('id_instansi'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'kode_sub_instansi' => $this->input->post('kode_sub_instansi'),
					'nm_sub_instansi' => $this->input->post('nm_sub_instansi'),
					'id_instansi' => $this->input->post('id_instansi'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Sub_instansi_model->update(['id_sub_instansi' => $this->input->post('id_sub_instansi')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_instansi', 'Instansi', 'trim|required');
			$this->form_validation->set_rules('kode_sub_instansi', 'Kode Sub Instansi', 'trim|required');
			$this->form_validation->set_rules('nm_sub_instansi', 'Nama Sub Instansi', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_instansi', 'Instansi', 'trim|required');
			$this->form_validation->set_rules('kode_sub_instansi', 'Kode Sub Instansi', 'trim|required');
			$this->form_validation->set_rules('nm_sub_instansi', 'Nama Sub Instansi', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		}
	}
}
