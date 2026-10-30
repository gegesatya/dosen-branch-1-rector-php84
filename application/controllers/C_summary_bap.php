<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_bap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_bap_model', 'Summary_bap_model');
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
		$output['judul_besar'] 		= 'BAP ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_bap';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_bap/summary_bap_view';
		$output['after_page'] 		= 'summary_bap/summary_bap_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Summary_bap_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_summary_bap/cetak_pdf_berita_acara') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> View BAP</a>';
			$html_copy = '<a href="javascript:void(0)" 
						 class="btn btn-sm btn-default green-stripe"
						 onclick="ajax_copy(\'' . $n->id . '\')">
						 <i class="fa fa-file"></i> Copy BAP</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] =  $n->tag_name . ' <br><small>' . $n->nama_hari . ', Jam : ' . substr($n->jam_mulai, 0, 5);
			//$row[] = $n->nm_mk;
			//$row[] = $html_copy;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_bap_model->count_all(),
			"recordsFiltered" => $this->Summary_bap_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_berita_acara($id_jadwal = null, $id_smt = null)
	{
		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)->get()->row();

		$pertemuan = $this->db->select('*')->from('siakad_rencana_pertemuan p')
			->where('id_jadwal', $id_jadwal)->get()->result();

		$total = $this->db->where(compact('id_jadwal'))->get('siakad_absen_detail_baru')->num_rows();

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = $this->db->where('id_sms', $jadwal->id_sms)->get('sms')->row()->nm_lemb;

		$output['nm_smt'] = $jadwal->nm_smt;
		$output['id_smt'] = $jadwal->id_smt;
		$output['list_pertemuan'] = $pertemuan;
		$output['statistik'] = $this->count_statistik($id_jadwal);
		$output['id_smt'] = $id_smt;
		$output['tipe_file'] = 1;
		$output['class_table'] = 'table-black';
		$output['total'] = $total;
		$output['nidn'] = $jadwal->nidn;
		$output['id_jadwal'] = $id_jadwal;

		if ($id_smt <= 20241) {
			$rps_mk_prodi = $this->db->where('id_kls', $jadwal->id_kls)->get('siakad_rps_mk_prodi');
		} else {
			$rps_mk_prodi = $this->db->where('id_kls', $jadwal->id_kls)->get('siakad_new_rps_mk_prodi');
		}

		if ($rps_mk_prodi->num_rows() > 0) {
			$output['id_rps'] = $rps_mk_prodi->row()->id_rps;
		} else {
			$output['id_rps'] = '';
		}

		$html = $this->load->view('cetak/summary_berita_acara_perkuliahan', $output, true);
		$pdfFilePath  = 'BAP ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';

		// $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);

		// $mpdf->WriteHTML($html);
		// $mpdf->Output($pdfFilePath, "I");

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'margin_top' => 42, 'margin_left' => 10, 'margin_right' => 10, 'margin-bottom' => 30]);
		$mpdf->SetHTMLHeader(SELF::header($output['nm_lemb'], $output['nm_mk'], $jadwal->nm_smt));
		$mpdf->SetHTMLFooter(SELF::footer());

		$mpdf->setAutoTopMargin = 'stretch';
		$mpdf->setAutoBottomMargin = 'pad';

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	private function count_statistik($id_kls)
	{
		$dt_kelas = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master')->row();
		$dt_absen = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master');
		$dt_teori = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Teori'])
			->get('arsip_absen_master');
		$dt_praktek = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Praktek'])
			->get('arsip_absen_master');

		$jumlah_mahasiswa = $this->db->where('id_kls', $id_kls)->get('feeder_nilai')->num_rows();
		$jumlah_pertemuan 	= $dt_absen->num_rows();
		$jumlah_teori 		= $dt_teori->num_rows();
		$jumlah_praktek 	= $dt_praktek->num_rows();

		$data = [];
		$data['jumlah_pertemuan'] 	= $jumlah_pertemuan;
		$data['jumlah_teori'] 		= $jumlah_teori;
		$data['jumlah_praktek'] 	= $jumlah_praktek;
		$data['jumlah_mahasiswa'] 	= $jumlah_mahasiswa;
		return $data;
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

	public function header($nm_lemb, $nm_mk, $nm_smt)
	{
		$h = '<div class="text-center">
				<img src="' . FCPATH . "assets/image_web/logo-white-3.png" . '" width="80px" />
				<p><strong>POLITEKNIK WILMAR BISNIS INDONESIA</strong><br>
				PROGRAM STUDI ' . strtoupper($nm_lemb) . '<br>
				BAP ' . strtoupper($nm_mk) . '<br>
				TAHUN AJARAN ' . $nm_smt . '</p>
			</div>';

		return $h;
	}
}
