<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_moa_expired extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Moa_expired_model', 'Moa_expired_model');
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

		$output['content'] = 'lapkerma/moa_expired/moa_expired_view';
		$output['after_page'] = 'lapkerma/moa_expired/moa_expired_after_page';
		$output['judul_besar'] = 'Daftar MOA';
		$output['judul_kecil'] = 'Expired';
		$output['menu_aktif'] 		= 'm_moa_expired';
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
				"recordsTotal" => $this->Moa_expired_model->count_all(),
				"recordsFiltered" => $this->Moa_expired_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Moa_expired_model->get_datatables();
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


			$row[] = '<small>' . $b->nm_instansi . '</small>';
			$row[] = '<small>' . $is_magang . '</small>';
			$row[] = $b->nm_status_kerjasama;
			$row[] = '<small>' . tgl_indo_short($b->tanggal_awal_moa) . ' - ' . tgl_indo_short($b->tanggal_akhir_moa) . '<br>' . $lama_periode . '<br>Expire in: ' . $expire . '</small>';

			$row[] = '<a class="btn btn-primary btn_sm" 
						 title="Edit" 
						 onclick="edit_moa(\'' . $b->id_moa . '\')"
						 href="javascript:void()" >
						 <i class="fa fa-edit"></i> Lihat MOU
					  </a>';
			$data[] = $row;



			// 	<a class="btn waves-effect waves-light btn-danger btn-xs" 
			// 	title="delete" 
			// 	onclick="delete_moa(\'' . $b->id_moa . '\')"
			// 	href="javascript:void()" >
			// 	<i class="fas fa-times"></i>
			//  </a>
		}

		return $data;
	}

	public function ajax_edit($id)
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->Moa_expired_model->get_by_id($id);
			echo json_encode($data);
		}
	}
}
