<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_instansi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Instansi_model');
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
		$output['content'] = 'instansi/instansi_view';
		$output['after_page'] = 'instansi/instansi_after_page';
		$output['judul'] = 'Instansi Kerja Sama';
		$output['judul_page'] = 'Instansi Kerja Sama';

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
				"recordsTotal" => $this->Instansi_model->count_all(),
				"recordsFiltered" => $this->Instansi_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Instansi_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->kode_instansi;
			$row[] = $b->nm_jenis_instansi;

			$row[] = '<a class="btn btn-dark btn-xs" 
						 title="Edit" 
						 onclick="edit_instansi(' . "'" . $b->id_jenis_instansi . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-edit"></i>
					  </a>
					  <a class="btn btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_instansi(' . "'" . $b->id_jenis_instansi . "'" . ')"
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
			$data = $this->Instansi_model->get_by_id($id);
			echo json_encode($data);
		}
	}
}
