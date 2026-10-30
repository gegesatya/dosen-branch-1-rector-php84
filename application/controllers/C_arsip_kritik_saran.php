<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_arsip_kritik_saran extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Arsip_kritik_saran_model', 'Arsip_kritik_saran_model');
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
		$output['judul_besar'] 		= 'Arsip Kritik/Saran ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_arsip_kritik_saran';
		$output['parent_3']			= 'active open';
		$output['content'] 			= 'arsip_kritik_saran/arsip_kritik_saran_view';
		$output['after_page'] 		= 'arsip_kritik_saran/arsip_kritik_saran_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Arsip_kritik_saran_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_arsip_kritik_saran/cetak_pdf_kritik_saran') . '/' . $n->id_kls . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Kritik/Saran</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->nm_kls;
			$row[] = $n->nm_mk;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Arsip_kritik_saran_model->count_all(),
			"recordsFiltered" => $this->Arsip_kritik_saran_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_kritik_saran($id_kls, $id_smt)
	{
		$data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt')
			->from('arsip_kelas_kuliah sk')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = sk.id_mk')
			->join('siakad_dosen d', 'd.id_ptk = sk.id_ptk')
			->join('sms sms', 'sk.id_sms = sms.id_sms')
			->join('semester sem', 'sk.id_smt = sem.id_smt')
			->where('sk.id_kls', $id_kls)->get()->row();

		$output['nm_ptk'] = $data_kuliah->nm_ptk;
		$output['nm_mk'] = $data_kuliah->nm_mk;
		$output['nm_lemb'] = $data_kuliah->nm_lemb;
		$output['nm_smt'] = $data_kuliah->nm_smt;

		$output['kritik_saran'] = $this->db->where(['id_kls' => $id_kls, 'kritik_saran <>' => ''])->get('siakad_evaluasi_dosen')->result();


		$html = $this->load->view('cetak/pdf_kritik_saran', $output, true);
		$pdfFilePath  = 'Data Absensi Mahasiswa ' . $data_kuliah->nm_mk . ' Semester ' . $data_kuliah->nm_smt . '.pdf';
		$mpdf = new \Mpdf\Mpdf();
		ini_set('memory_limit', '-1');
		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");



		//$this->load->view('cetak/pdf_absen', $output);
	}
}
