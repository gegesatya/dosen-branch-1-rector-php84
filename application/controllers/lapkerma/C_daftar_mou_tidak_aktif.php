<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_daftar_mou_tidak_aktif extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Daftar_mou_tidak_aktif_model', 'Daftar_mou_tidak_aktif_model');
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

		$output['content'] = 'lapkerma/daftar_mou_tidak_aktif/daftar_mou_tidak_aktif_view';
		$output['after_page'] = 'lapkerma/daftar_mou_tidak_aktif/daftar_mou_tidak_aktif_after_page';
		$output['judul_besar'] = 'Daftar MOU';
		$output['judul_kecil'] = 'Tidak Aktif';
		$output['menu_aktif'] 		= 'm_daftar_mou_tidak_aktif';
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
				"recordsTotal" => $this->Daftar_mou_tidak_aktif_model->count_all(),
				"recordsFiltered" => $this->Daftar_mou_tidak_aktif_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Daftar_mou_tidak_aktif_model->get_datatables();
		$this->load->helper('date');

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {

			$tgl_awal = strtotime($b->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($b->tanggal_akhir));
			if (strtotime($b->tanggal_akhir) < now()) {
				$expire = '<span class="label label-danger">Sudah berakhir</span>';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$no++;
			$row = [];
			$row[] = $no;

			if (!empty($b->dokumen_mou)) {
				$row[] = '<small>' . $b->nomor_mou . '<br>Penandatangan MOU : ' . $b->penandatangan_wbi . '</small><br>' . $b->judul_kerjasama . '<br><a href="' . $b->dokumen_mou . '" target="_new" class="btn btn-xs btn-success">Buka Dokumen</a>';
			} else {
				$row[] = '<small>' . $b->nomor_mou . '<br>Penandatangan MOU : ' . $b->penandatangan_wbi . '</small><br>' . $b->judul_kerjasama;
			}
			$row[] = $b->nm_instansi;
			$row[] = $b->nm_status_kerjasama;
			$row[] = '<small>' . tgl_indo_short($b->tanggal_awal) . ' - ' . tgl_indo_short($b->tanggal_akhir) . '<br>' . timespan($tgl_awal, $tgl_akhir, 1) . '<br>Expire in: ' . $expire . '</small>';
			$row[] = '<a class="btn btn-primary btn-sm" 
						 title="Edit" 
						 onclick="edit_kerjasama(' . "'" . $b->id_mou . "'" . ')"
						 href="javascript:void()" >
						 <i class="fa fa-edit"></i> Lihat MOU
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
			$data = $this->Daftar_mou_tidak_aktif_model->get_by_id($id);
			echo json_encode($data);
		}
	}
}
