<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_nilai extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_nilai_model', 'Summary_nilai_model');
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
		$output['judul_besar'] 		= 'Nilai ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_nilai';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_nilai/summary_nilai_view';
		$output['after_page'] 		= 'summary_nilai/summary_nilai_after_page';

		$this->load->view('master_page', $output);
	}



	public function ajax_list()
	{
		$list = $this->Summary_nilai_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_summary_nilai/cetak_pdf_nilai') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Nilai</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] =  $n->tag_name . ' <br><small>' . $n->nama_hari . ', Jam : ' . substr($n->jam_mulai, 0, 5);
			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_nilai_model->count_all(),
			"recordsFiltered" => $this->Summary_nilai_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_nilai($id_jadwal = null, $id_smt = null)
	{
		$jadwal = $this->db->select('j.*, h.nama_hari, sd.nm_ptk, sd.nidn, s.nm_smt')->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari', 'left')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm', 'left')
			->join('semester s', 's.id_smt = j.id_smt', 'left')
			->where('j.id', $id_jadwal)->get()->row();

		$jadwal_detail = $this->db->where('id_jadwal', $jadwal->id)->get('siakad_jadwal_detail')->result();
		$prodi = '';
		foreach ($jadwal_detail as $key => $value) {
			$k = $this->db->where('kk.id_kls', $value->id_kls)
				->select('kk.nm_kls, s.nm_lemb')
				->from('feeder_kelas_kuliah kk')
				->join('sms s', 's.id_sms = kk.id_sms')->get()->row();
			$prodi .= '[' . $k->nm_kls . '] ' . $k->nm_lemb . '<br>';
		}

		$count_nilai_a = $this->hitung_nilai($id_jadwal, 'A');
		$count_nilai_b_plus = $this->hitung_nilai($id_jadwal, 'B+');
		$count_nilai_b = $this->hitung_nilai($id_jadwal, 'B');
		$count_nilai_c_plus = $this->hitung_nilai($id_jadwal, 'C+');
		$count_nilai_c = $this->hitung_nilai($id_jadwal, 'C');
		$count_nilai_d = $this->hitung_nilai($id_jadwal, 'D');
		$count_nilai_e = $this->hitung_nilai($id_jadwal, 'E');

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['prodi'] = $prodi;
		$output['nm_smt'] = $jadwal->nm_smt;
		$output['tanggal_lapor'] = $jadwal->tanggal_lapor_nilai;
		$output['sikap'] = $jadwal->sikap;
		$output['quiz'] = $jadwal->quiz;
		$output['tugas_terstruktur'] = $jadwal->tugas_terstruktur;
		$output['uts'] = $jadwal->uts;
		$output['uas'] = $jadwal->uas;
		$output['unjuk_kinerja'] = $jadwal->unjuk_kinerja;
		$output['proyek'] = $jadwal->proyek;
		$output['portofolio'] = $jadwal->portofolio;
		$output['produk'] = $jadwal->produk;
		$output['presentasi'] = $jadwal->presentasi;
		$output['nidn'] = $jadwal->nidn;
		$output['nilai_a'] = $count_nilai_a;
		$output['nilai_b_plus'] = $count_nilai_b_plus;
		$output['nilai_b'] = $count_nilai_b;
		$output['nilai_c_plus'] = $count_nilai_c_plus;
		$output['nilai_c'] = $count_nilai_c;
		$output['nilai_d'] = $count_nilai_d;
		$output['nilai_e'] = $count_nilai_e;
		$output['jadwal_nya'] = $jadwal->nama_hari . ', Jam ' . substr($jadwal->jam_mulai, 0, 5);


		$data_nilai = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('mahasiswa_pt mp', 'mp.nipd = sn.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('id_jadwal', $id_jadwal)
			->order_by('m.nm_pd')->get()->result();
		$output['data_nilai'] = $data_nilai;

		$html = $this->load->view('cetak/summary_nilai_matakuliah', $output, true);
		$pdfFilePath  = 'Nilai ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'margin_top' => 32, 'margin_left' => 10, 'margin_right' => 10, 'margin-bottom' => 30]);
		$mpdf->SetHTMLHeader(SELF::header($output['nm_lemb'], $output['nm_mk'], $jadwal->nm_smt));
		$mpdf->SetHTMLFooter(SELF::footer());

		$mpdf->setAutoTopMargin = 'stretch';
		$mpdf->setAutoBottomMargin = 'pad';

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

	public function header($nm_lemb, $nm_mk, $nm_smt)
	{
		// $h = '<div class="text-center">
		// 		<img src="' . FCPATH . "assets/image_web/logo-white-3.png" . '" width="80px" />
		// 		<p><strong>POLITEKNIK WILMAR BISNIS INDONESIA</strong><br>
		// 		PROGRAM STUDI ' . strtoupper($nm_lemb) . '<br>
		// 		NILAI ' . strtoupper($nm_mk) . '<br>
		// 		TAHUN AJARAN ' . $nm_smt . '</p>
		// 	</div>';

		$h = '<div class="text-center">
				<img src="' . FCPATH . "assets/image_web/logo-white-3.png" . '" width="80px" />
				<p><strong>POLITEKNIK WILMAR BISNIS INDONESIA</strong><br>
				NILAI ' . strtoupper($nm_mk) . '</p></div>';

		return $h;
	}

	private function hitung_nilai($id_jadwal, $nilai)
	{
		return $this->db->select('count(nilai_huruf) as jumlah')
			->from('siakad_nilai_baru')
			->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => $nilai])->get()->row()->jumlah;
	}
}
