<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_status_kerjasama extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Status_kerjasama_model');
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
		$output['content'] = 'status_kerjasama/status_kerjasama_view';
		$output['after_page'] = 'status_kerjasama/status_kerjasama_after_page';
		$output['judul'] = 'Status Kerjasama';
		$output['judul_page'] = 'Status Kerjasama';

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
				"recordsTotal" => $this->Status_kerjasama_model->count_all(),
				"recordsFiltered" => $this->Status_kerjasama_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Status_kerjasama_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_status_kerjasama;
			$row[] = $b->deskripsi;
			$data[] = $row;
		}

		return $data;
	}
}
