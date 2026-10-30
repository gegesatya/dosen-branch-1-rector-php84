<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_moa extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Moa_model', 'Moa_model');
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

		$output['content'] = 'lapkerma/moa/moa_view';
		$output['after_page'] = 'lapkerma/moa/moa_after_page';
		$output['judul_besar'] = 'Daftar MOA';
		$output['judul_kecil'] = 'Aktif';
		$output['menu_aktif'] 		= 'm_moa';
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
				"recordsTotal" => $this->Moa_model->count_all(),
				"recordsFiltered" => $this->Moa_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Moa_model->get_datatables();
		$this->load->helper('date');
		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$tgl_awal = strtotime($b->tanggal_awal_moa);
			$tgl_akhir = strtotime('+2 day', strtotime($b->tanggal_akhir_moa));
			if (strtotime($b->tanggal_akhir_moa) < now()) {
				$expire = 'Sudah berakhir';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$lama_periode = timespan($tgl_awal, $tgl_akhir, 1);
			$is_magang = $b->is_magang == 0 ? 'N' : 'Y';
			$no++;
			$row = [];
			$row[] = $no;
			if (!empty($b->dokumen_moa)) {
				$row[] = '<small>' . $b->nomor_dokumen_moa . '<br>Penandatangan WBI : ' . $b->penandatangan_moa_wbi . '<br>' . $b->judul_moa . '</small><br><a href="' . $b->dokumen_moa . '" target="_new" class="btn btn-xs btn-success">Buka Dokumen</a>';
			} else {
				$row[] = '<small>' . $b->nomor_dokumen_moa . '<br>Penandatangan WBI : ' . $b->penandatangan_moa_wbi . '<br>' . $b->judul_moa . '</small>';
			}

			$row[] =  $b->nm_instansi;
			$row[] = $is_magang;
			$row[] = $b->nm_status_kerjasama;
			$row[] = '<small>' . tgl_indo_short($b->tanggal_awal_moa) . ' - ' . tgl_indo_short($b->tanggal_akhir_moa) . '<br>' . $lama_periode . '<br>Expire in: ' . $expire . '</small>';

			$row[] = '<a class="btn btn-primary btn-sm" 
						 title="Edit" 
						 onclick="edit_moa(\'' . $b->id_moa . '\')"
						 href="javascript:void()" >
						 <i class="fa fa-edit"></i> Lihat MOA
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
			$data = $this->Moa_model->get_by_id($id);
			echo json_encode($data);
		}
	}
}
