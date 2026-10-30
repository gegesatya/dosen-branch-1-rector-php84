<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_kriteria_mitra extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Kriteria_mitra_model');
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
		$output['content'] = 'kriteria_mitra/kriteria_mitra_view';
		$output['after_page'] = 'kriteria_mitra/kriteria_mitra_after_page';
		$output['judul'] = 'Kriteria Mitra';
		$output['judul_page'] = 'Kriteria Mitra';

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
				"recordsTotal" => $this->Kriteria_mitra_model->count_all(),
				"recordsFiltered" => $this->Kriteria_mitra_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Kriteria_mitra_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_kriteria_mitra;
			$row[] = $b->deskripsi_kriteria;
			$data[] = $row;
		}

		return $data;
	}
}
