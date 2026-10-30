<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_bimbingan_krs extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_bimbingan_krs_model');
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
		$output['judul_besar'] 		= 'Bimbingan KRS ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_bimbingan_krs';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_bimbingan_krs/summary_bimbingan_krs_view';
		$output['after_page'] 		= 'summary_bimbingan_krs/summary_bimbingan_krs_after_page';

		$this->load->view('master_page', $output);
	}



	public function ajax_list()
	{
		$list = $this->Summary_bimbingan_krs_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$keterangan = $this->db->where('id_smt', $n->id_smt)->get('semester')->row()->nm_smt;
			$html = '<a href="' . site_url('C_summary_bimbingan_krs/cetak_pdf_bimbingan_krs')  . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Konsultasi KRS</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = 'Data Bimbingan KRS Tahun Ajaran ' . $keterangan;
			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_bimbingan_krs_model->count_all(),
			"recordsFiltered" => $this->Summary_bimbingan_krs_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_bimbingan_krs($id_smt = null)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$jadwal = $this->db->select('*')->from('siakad_krs k')
			->join('mahasiswa_pt mp', 'mp.nipd = k.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['m.dosen_pa' => $id_ptk, 'k.id_smt' => $id_smt])
			->order_by('k.nipd')
			->get()->result();

		$nm_ptk = $this->db->where('id_ptk', $id_ptk)->get('siakad_dosen')->row()->nm_ptk;
		$nm_smt = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;
		$output['nm_ptk'] = $nm_ptk;
		$output['nm_smt'] = $nm_smt;
		$output['krs'] = $jadwal;



		$html = $this->load->view('cetak/summary_bimbingan_krs', $output, true);
		$pdfFilePath  = 'Lembar Konsultasi ' . $nm_ptk . ' ' . $nm_smt . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}
}
