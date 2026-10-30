<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_kritik_saran extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_kritik_saran_model', 'Summary_kritik_saran_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Kritik/Saran ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_kritik_saran';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_kritik_saran/summary_kritik_saran_view';
		$output['after_page'] 		= 'summary_kritik_saran/summary_kritik_saran_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Summary_kritik_saran_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_summary_kritik_saran/cetak_pdf_kritik_saran') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Kritik/Saran</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->tag_name;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_kritik_saran_model->count_all(),
			"recordsFiltered" => $this->Summary_kritik_saran_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_kritik_saran($id_jadwal, $id_smt)
	{
		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)
			->get()->row();

		$id_sms = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->result();

		$data_nm_lemb = '';

		foreach ($id_sms as $key => $value) {
			$data_nm_lemb .= $this->db->where('id_sms', $value->id_sms)->get('sms')->row()->nm_lemb . '<br>';
		}

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = $data_nm_lemb;
		$output['nm_smt'] = $jadwal->nm_smt;

		$where = "id_jadwal = $id_jadwal AND CHAR_LENGTH(kritik_saran) > 10
  					AND (LENGTH(kritik_saran) - LENGTH(REPLACE(kritik_saran, ' ', '')) + 1) > 3";

		$output['kritik_saran'] = $this->db->where($where)->order_by('RAND()')->get('siakad_evaluasi_dosen')->result();


		$html = $this->load->view('cetak/pdf_kritik_saran', $output, true);
		$pdfFilePath  = 'Data Kritik Saran ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);
		ini_set('memory_limit', '-1');
		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");



		//$this->load->view('cetak/pdf_absen', $output);
	}
}
