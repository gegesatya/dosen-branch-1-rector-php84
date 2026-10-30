<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_arsip_kehadiran extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Arsip_kehadiran_model', 'Arsip_kehadiran_model');
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
		$output['judul_besar'] 		= 'Arsip Kehadiran ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_arsip_kehadiran';
		$output['parent_3']			= 'active open';
		$output['content'] 			= 'arsip_kehadiran/arsip_kehadiran_view';
		$output['after_page'] 		= 'arsip_kehadiran/arsip_kehadiran_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Arsip_kehadiran_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_arsip_kehadiran/cetak_pdf_absen') . '/' . $n->id_kls . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Data Kehadiran</a>';
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
			"recordsTotal" => $this->Arsip_kehadiran_model->count_all(),
			"recordsFiltered" => $this->Arsip_kehadiran_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_absen($id_kls, $id_smt)
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
		$output['tipe_file'] = 1;
		$output['class_table'] = 'table-black';
		$output['style'] = '';
		$output['id_kls'] = $id_kls;

		$output['absen_master'] = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Teori'])
			->order_by('minggu_ke')
			->get('arsip_absen_master');
		$output['absen_detail'] = $this->db->select('mp.nipd,nm_pd, sn.id_reg_pd')->from('arsip_nilai sn')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd = sn.id_reg_pd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('arsip_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->where(['sn.id_kls' => $id_kls, 'id_smt' => $id_smt])
			->order_by('left(mp.nipd,4)')
			->order_by('right(mp.nipd,4)')
			->get()->result();

		$output['absen_master_praktek'] = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Praktek'])
			->order_by('minggu_ke')
			->get('arsip_absen_master');

		$total_pertemuan = $output['absen_master']->num_rows() + $output['absen_master_praktek']->num_rows();
		$output['total_pertemuan'] = $total_pertemuan;
		//$this->load->view('cetak/pdf_absen', $output);
		$html = $this->load->view('cetak/pdf_absen', $output, true);
		$pdfFilePath  = 'Data Absensi Mahasiswa ' . $data_kuliah->nm_mk . ' Semester ' . $data_kuliah->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf();
		ini_set('memory_limit', '-1');
		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}
}
