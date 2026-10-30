<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_bentuk_kegiatan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Bentuk_kegiatan_model');
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
		$output['content'] = 'bentuk_kegiatan/bentuk_kegiatan_view';
		$output['after_page'] = 'bentuk_kegiatan/bentuk_kegiatan_after_page';
		$output['judul'] = 'Bentuk Kegiatan';
		$output['judul_page'] = 'Bentuk Kegiatan';

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
				"recordsTotal" => $this->Bentuk_kegiatan_model->count_all(),
				"recordsFiltered" => $this->Bentuk_kegiatan_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Bentuk_kegiatan_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_bentuk_kegiatan;
			$data[] = $row;
		}

		return $data;
	}
}
