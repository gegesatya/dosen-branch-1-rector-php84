<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_kehadiran extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_kehadiran_model', 'Summary_kehadiran_model');
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
		$output['judul_besar'] 		= 'Kehadiran ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_kehadiran';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_kehadiran/summary_kehadiran_view';
		$output['after_page'] 		= 'summary_kehadiran/summary_kehadiran_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Summary_kehadiran_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_summary_kehadiran/cetak_pdf_absen') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Data Kehadiran</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] =  $n->tag_name . ' <br><small>' . $n->nama_hari . ', Jam : ' . substr($n->jam_mulai, 0, 5);
			//$row[] = $n->nm_mk;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_kehadiran_model->count_all(),
			"recordsFiltered" => $this->Summary_kehadiran_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_absen($id_jadwal, $id_smt)
	{
		/* $data_kuliah = $this->db->select('mk.nm_mk, d.nm_ptk, sms.nm_lemb, sem.nm_smt')
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
			->where(array('id_kls' => $id_kls, 'kategori_pertemuan' => 'Teori'))
			->order_by('minggu_ke')
			->get('arsip_absen_master');
		$output['absen_detail'] = $this->db->select('nipd,nm_pd, sn.id_reg_pd')->from('arsip_nilai sn')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd = sn.id_reg_pd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('arsip_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->where(array('sn.id_kls' => $id_kls, 'id_smt' => $id_smt))
			->order_by('left(nipd,4)')
			->order_by('right(nipd,4)')
			->get()->result();

		$output['absen_master_praktek'] = $this->db
			->where(array('id_kls' => $id_kls, 'kategori_pertemuan' => 'Praktek'))
			->order_by('minggu_ke')
			->get('arsip_absen_master');

		$total_pertemuan = $output['absen_master']->num_rows() + $output['absen_master_praktek']->num_rows(); */

		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('semester s', 's.id_smt = j.id_smt')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->where('j.id', $id_jadwal)->get()->row();

		$absen_master = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan');

		$siakad_absen = $this->db->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('id_jadwal', $id_jadwal)
			->order_by('nm_pd')
			->get()->result();

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['id_smt'] = $id_smt;
		$output['nm_lemb'] = '';
		$output['nm_smt'] = $jadwal->nm_smt;
		$output['tipe_file'] = 1;
		$output['class_table'] = 'table-black';
		$output['absen_master'] = $absen_master;
		$output['siakad_absen'] = $siakad_absen;
		$output['style'] = '';
		$output['jadwal_nya'] = $jadwal->nama_hari . ', Jam : ' . substr($jadwal->jam_mulai, 0, 5);

		$html = $this->load->view('cetak/summary_absen', $output, true);
		$pdfFilePath  = 'Data Absensi Mahasiswa ' . $jadwal->nm_mk . ' Semester ' . $jadwal->nm_smt . '.pdf';

		// $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 30, 'margin_left' => 10, 'margin_right' => 10, 'margin-bottom' => 30]);

		$mpdf->SetHTMLHeader(SELF::header($jadwal->nm_smt));
		$mpdf->SetHTMLFooter(SELF::footer());

		ini_set('memory_limit', '-1');
		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function footer()
	{
		$f = '<div style="font-size: 11px; color: #006a6f ;bottom:15;left:75;position:absolute">
				<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
				<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
					Kabupaten Deli Serdang – Sumatera Utara<br>
					(061) 42074111; Laman : www.wbi.ac.id<br>
				</p>
 		 	</div>';
		return $f;
	}

	public function header($nm_smt)
	{
		$h = '<div class="text-center">
				<img src="' . FCPATH . "assets/image_web/logo-white-3.png" . '" width="80px" />
				<p><strong>POLITEKNIK WILMAR BISNIS INDONESIA</strong><br>
				TAHUN AJARAN ' . $nm_smt . '</p>
			</div>';

		return $h;
	}
}
