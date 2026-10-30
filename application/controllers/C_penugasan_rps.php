<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_penugasan_rps extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Penugasan_rps_model', 'Penugasan_rps_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
	}

	public function index()
	{
		$group = [12];
		if (!$this->ion_auth->in_group($group)) {

			$output['judul_besar'] = 'Anda tidak berhak mengakses halaman ini.';
			$output['judul_kecil'] = '';
			$output['menu_aktif'] = 'm_persetujuan_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'forbidden_page/forbidden_page_view';
			$output['isi'] = 'Halaman ini hanya bisa diakses oleh pengguna dengan level Wadir 1.';
			$output['after_page'] = 'forbidden_page/forbidden_page_after_page';
		} else {
			$output['judul_besar'] = 'Persetujuan RPS';
			$output['judul_kecil'] = 'Politeknik WBI';
			$output['menu_aktif'] = 'm_penugasan_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'penugasan_rps/penugasan_rps_view';
			$output['after_page'] = 'penugasan_rps/penugasan_rps_after_page';
			$output['dd_kode_revisi'] = $this->Penugasan_rps_model->f_dropdown();
			$output['dd_status_pengesahan'] = $this->Penugasan_rps_model->f_dropdown_pengesahan();

			//$this->load->view('master_page', $output);
		}

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Penugasan_rps_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$no++;
			$row = [];

			$html_preview = '<a href="' . site_url('C_rps_online/preview_rps/' . $l->id_rps) . '" class="btn btn-default btn-sm green-stripe" target="_new"  title="Preview RPS">
								' . $l->nm_mk . ' </a>';

			$html_action = '<a class="btn btn-sm btn-primary btn-sm" 
						 href="javascript:void()" 
						 title="Edit" 
						 onclick="edit_rps(\'' . $l->id_rps . '\')">
						 <i class="glyphicon glyphicon-pencil"></i>
					  </a>';

			$row[] = $no;
			$row[] = $html_preview;
			$row[] = ucfirst(strtolower((string)$l->nm_ptk));
			$row[] = $l->nm_cakupan;
			$row[] = $l->kode_revisi;
			$row[] = $l->nomor_dokumen;
			$row[] = $l->tanggal_pengesahan;
			$row[] = $l->nm_status_pengajuan_rps;
			$row[] = $html_action;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Penugasan_rps_model->count_all(),
			"recordsFiltered" => $this->Penugasan_rps_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Penugasan_rps_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_update()
	{
		//$this->_validate();
		$data = [
			//'id_ptk' => $this->session->userdata('id_ptk'),
			'tanggal_pengesahan' => $this->input->post('tanggal_pengesahan'),
			'nomor_dokumen' => $this->input->post('nomor_dokumen'),
			'kode_revisi' => $this->input->post('kode_revisi'),
			'id_status_pengajuan_rps' => $this->input->post('id_status_pengajuan_rps'),

			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$data = $this->security->xss_clean($data);
		$this->Penugasan_rps_model->update(['id_rps' => $this->input->post('id_rps')], $data);
		echo json_encode(["status" => TRUE]);
	}



	public function preview_rps($id_rps = null)
	{
		$data_rps = $this->db->where('id_rps', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();

		$data_rps_detail = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online_detail')->result();

		$output['data_rps'] = $data_rps;
		$output['data_rps_detail'] = $data_rps_detail;

		$html = $this->load->view('cetak/pdf_rps_online', $output, true);
		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function download_template()
	{
		$this->load->helper('download');
		force_download('rps_online_excel/template_rps_detail.xlsx', NULL);
	}
}
