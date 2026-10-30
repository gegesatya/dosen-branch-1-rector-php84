<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_summary_epom extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Summary_epom_model', 'Summary_epom_model');
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
		$output['judul_besar'] 		= 'EPOM ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_summary_epom';
		$output['parent_summary']	= 'active open';
		$output['content'] 			= 'summary_epom/summary_epom_view';
		$output['after_page'] 		= 'summary_epom/summary_epom_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Summary_epom_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_summary_epom/cetak_pdf_epom') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Data EPOM</a>';
			$html2 = '<a href="' . site_url('C_summary_kritik_saran/cetak_pdf_kritik_saran') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Kritik/Saran</a>';
			$html3 = '<a href="' . site_url('C_summary_epom/cetak_partisipan') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Partisipan</a>';

			// $jumlah_peserta_edom = $this->db->where('id_jadwal', $n->id)->get('siakad_evaluasi_dosen')->num_rows();

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] =  $n->tag_name . ' <br><small>' . $n->nama_hari . ', Jam : ' . substr($n->jam_mulai, 0, 5) . '</small>';
			$row[] = $html;
			$row[] = $html2;
			$row[] = $html3;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Summary_epom_model->count_all(),
			"recordsFiltered" => $this->Summary_epom_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_epom($id_jadwal)
	{

		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)->get()->row();

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = '';
		$output['nm_smt'] = $jadwal->nm_smt;
		$output['id_jadwal'] = $jadwal->id;
		$output['jadwal_nya'] = $jadwal->nama_hari . ', Jam : ' . substr($jadwal->jam_mulai, 0, 5);

		$siakad_evaluasi_dosen = $this->db
			->select(
				'round(avg(perencanaan_pembelajaran_1),2) as perencanaan_pembelajaran_1,
									  			 round(avg(perencanaan_pembelajaran_2),2) as perencanaan_pembelajaran_2,
									  			 round(avg(perencanaan_pembelajaran_3),2) as perencanaan_pembelajaran_3,
									  			 round(avg(perencanaan_pembelajaran_4),2) as perencanaan_pembelajaran_4,
									  			 round(avg(perencanaan_pembelajaran_5),2) as perencanaan_pembelajaran_5,
												round(avg(pelaksanaan_pembelajaran_1),2) as pelaksanaan_pembelajaran_1,
												round(avg(pelaksanaan_pembelajaran_2),2) as pelaksanaan_pembelajaran_2,
												round(avg(pelaksanaan_pembelajaran_3),2) as pelaksanaan_pembelajaran_3,
												round(avg(pelaksanaan_pembelajaran_4),2) as pelaksanaan_pembelajaran_4,
												round(avg(pelaksanaan_pembelajaran_5),2) as pelaksanaan_pembelajaran_5,
												round(avg(pelaksanaan_pembelajaran_6),2) as pelaksanaan_pembelajaran_6,
												round(avg(pelaksanaan_pembelajaran_7),2) as pelaksanaan_pembelajaran_7,
												round(avg(pelaksanaan_pembelajaran_8),2) as pelaksanaan_pembelajaran_8,
												round(avg(pelaksanaan_pembelajaran_9),2) as pelaksanaan_pembelajaran_9,
												round(avg(sarana_1),2) as sarana_1,
												round(avg(sarana_2),2) as sarana_2'
			)
			->from('siakad_evaluasi_dosen')
			->where(['id_jadwal' => $id_jadwal, 'status_eval' => 1])
			->get()->row();

		// $t_perencanaan_pembelajaran = $siakad_evaluasi_dosen->perencanaan_pembelajaran_1 +
		// 	$siakad_evaluasi_dosen->perencanaan_pembelajaran_2 +
		// 	$siakad_evaluasi_dosen->perencanaan_pembelajaran_3 +
		// 	$siakad_evaluasi_dosen->perencanaan_pembelajaran_4 +
		// 	$siakad_evaluasi_dosen->perencanaan_pembelajaran_5;

		// $t_pelaksanaan_pembelajaran = $siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8 +
		// 	$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9;

		// $t_sarana = $siakad_evaluasi_dosen->sarana_1 + $siakad_evaluasi_dosen->sarana_2;


		$t_perencanaan_kali_bobot = $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_1, $this->get_soal_epom('perencanaan_pembelajaran_1')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_2, $this->get_soal_epom('perencanaan_pembelajaran_2')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_3, $this->get_soal_epom('perencanaan_pembelajaran_3')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_4, $this->get_soal_epom('perencanaan_pembelajaran_4')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_5, $this->get_soal_epom('perencanaan_pembelajaran_5')->bobot_soal);

		$t_bobot_perencanaan = $this->db->select('sum(bobot_soal) as bobot_soal')
			->where('kategori', 'Perencanaan Pembelajaran')->get('siakad_master_soal_epom')->row()->bobot_soal;

		$t_pelaksanaan_kali_bobot = $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1, $this->get_soal_epom('pelaksanaan_pembelajaran_1')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2, $this->get_soal_epom('pelaksanaan_pembelajaran_2')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3, $this->get_soal_epom('pelaksanaan_pembelajaran_3')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4, $this->get_soal_epom('pelaksanaan_pembelajaran_4')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5, $this->get_soal_epom('pelaksanaan_pembelajaran_5')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6, $this->get_soal_epom('pelaksanaan_pembelajaran_6')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7, $this->get_soal_epom('pelaksanaan_pembelajaran_7')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8, $this->get_soal_epom('pelaksanaan_pembelajaran_8')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9, $this->get_soal_epom('pelaksanaan_pembelajaran_9')->bobot_soal);

		$t_bobot_pelaksanaan = $this->db->select('sum(bobot_soal) as bobot_soal')
			->where('kategori', 'Pelaksanaan Pembelajaran')->get('siakad_master_soal_epom')->row()->bobot_soal;

		// $t_sarana_kali_bobot = 	$this->hitung_score_kali_bobot($siakad_evaluasi_dosen->sarana_1, $this->get_soal_epom('sarana_1')->bobot_soal) +
		// 	$this->hitung_score_kali_bobot($siakad_evaluasi_dosen->sarana_2, $this->get_soal_epom('sarana_2')->bobot_soal);

		// $t_bobot_sarana = $this->db->select('sum(bobot_soal) as bobot_soal')
		// 	->where('kategori', 'Sarana dan Prasarana')->get('siakad_master_soal_epom')->row()->bobot_soal;

		$tot_bobot_kali_nilai = $t_perencanaan_kali_bobot + $t_pelaksanaan_kali_bobot;
		$tot_bobot = $t_bobot_perencanaan + $t_bobot_pelaksanaan;
		$score_akhir = round($tot_bobot_kali_nilai / $tot_bobot, 2);

		$jumlah_peserta_edom = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_evaluasi_dosen')->num_rows();
		$jumlah_sudah_edom = $this->db->where('id_jadwal', $id_jadwal)->where('status_eval', 1)->get('siakad_evaluasi_dosen')->num_rows();
		// $partisipasi = $jumlah_sudah_edom . ' / ' . $jumlah_peserta_edom;

		$tabel_html = '';

		$tabel_html .= "<table class='table table-black'>
						<thead><tr><th class='text-right'>No.</th><th>Aspek yang Dinilai</th><th class='text-center'>Score</th><th class='text-center'>Bobot</th><th class='text-center'>Score*Bobot</th></tr></thead>
						<tbody>";

		$tabel_html .= "<tr><td colspan='5'><b>A. PERENCANAAN PEMBELAJARAN</b></td></tr>";
		$tabel_html .= "<tr><td class='text-right'>1.</td><td>" . $this->get_soal_epom('perencanaan_pembelajaran_1')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->perencanaan_pembelajaran_1</td><td class='text-center'>" . $this->get_soal_epom('perencanaan_pembelajaran_1')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_1, $this->get_soal_epom('perencanaan_pembelajaran_1')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>2.</td><td>" . $this->get_soal_epom('perencanaan_pembelajaran_2')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->perencanaan_pembelajaran_2</td><td class='text-center'>" . $this->get_soal_epom('perencanaan_pembelajaran_2')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_2, $this->get_soal_epom('perencanaan_pembelajaran_2')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>3.</td><td>" . $this->get_soal_epom('perencanaan_pembelajaran_3')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->perencanaan_pembelajaran_3</td><td class='text-center'>" . $this->get_soal_epom('perencanaan_pembelajaran_3')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_3, $this->get_soal_epom('perencanaan_pembelajaran_3')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>4.</td><td>" . $this->get_soal_epom('perencanaan_pembelajaran_4')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->perencanaan_pembelajaran_4</td><td class='text-center'>" . $this->get_soal_epom('perencanaan_pembelajaran_4')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_4, $this->get_soal_epom('perencanaan_pembelajaran_4')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>5.</td><td>" . $this->get_soal_epom('perencanaan_pembelajaran_5')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->perencanaan_pembelajaran_5</td><td class='text-center'>" . $this->get_soal_epom('perencanaan_pembelajaran_5')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_5, $this->get_soal_epom('perencanaan_pembelajaran_5')->bobot_soal) . "</td></tr>";

		$tabel_html .= "<tr><td colspan='5'><b>B. PELAKSANAAN PEMBELAJARAN</b></td></tr>";
		$tabel_html .= "<tr><td class='text-right'>1.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_1')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_1')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1, $this->get_soal_epom('pelaksanaan_pembelajaran_1')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>2.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_2')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_2')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2, $this->get_soal_epom('pelaksanaan_pembelajaran_2')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>3.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_3')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_3')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3, $this->get_soal_epom('pelaksanaan_pembelajaran_3')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>4.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_4')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_4')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4, $this->get_soal_epom('pelaksanaan_pembelajaran_4')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>5.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_5')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_5')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5, $this->get_soal_epom('pelaksanaan_pembelajaran_5')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>6.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_6')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_6')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6, $this->get_soal_epom('pelaksanaan_pembelajaran_6')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>7.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_7')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_7')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7, $this->get_soal_epom('pelaksanaan_pembelajaran_7')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>8.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_8')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_8')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8, $this->get_soal_epom('pelaksanaan_pembelajaran_8')->bobot_soal) . "</td></tr>";
		$tabel_html .= "<tr><td class='text-right'>9.</td><td>" . $this->get_soal_epom('pelaksanaan_pembelajaran_9')->soal . "</td><td class='text-center'>$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9</td><td class='text-center'>" . $this->get_soal_epom('pelaksanaan_pembelajaran_9')->bobot_soal . "</td><td class='text-center'>" . $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9, $this->get_soal_epom('pelaksanaan_pembelajaran_9')->bobot_soal) . "</td></tr>";

		$tabel_html .= "<tr><td colspan='2' class='text-right'><b>TOTAL</b></td><td></td><td class='text-center'>$tot_bobot</td><td class='text-center'>$tot_bobot_kali_nilai</td></tr>";
		$tabel_html .= "<tr><td colspan='2' class='text-right'><b>NILAI EPOM</b></td><td colspan='3' class='text-center'>$score_akhir</td></tr>";

		$tabel_html .= "</tbody></table>";


		$output['partisipasi'] = $jumlah_sudah_edom . ' / ' . $jumlah_peserta_edom;
		$output['tabel_html'] = $tabel_html;

		$html = $this->load->view('cetak/summary_epom', $output, true);
		$pdfFilePath  = 'EPOM ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_partisipan($id_jadwal)
	{
		$output['id_jadwal'] = $id_jadwal;

		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)->get()->row();

		$id_sms = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->result();

		$data_nm_lemb = '';

		foreach ($id_sms as $key => $value) {
			$data_nm_lemb .= $this->db->where('id_sms', $value->id_sms)->get('sms')->row()->nm_lemb . '<br>';
		}

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = $data_nm_lemb;
		$output['nm_smt'] = $jadwal->nm_smt;

		$berpartisipasi = $this->db->select('ed.nipd , m.nm_pd')
			->where(['id_jadwal' => $id_jadwal, 'status_eval' => 1])
			->from('siakad_evaluasi_dosen ed')
			->join('mahasiswa_pt mp', 'mp.nipd = ed.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->order_by('RAND()')
			->get();
		$tidak_berpartisipasi = $this->db->select('ed.nipd , m.nm_pd')
			->where(['id_jadwal' => $id_jadwal, 'status_eval' => 0])
			->from('siakad_evaluasi_dosen ed')->join('mahasiswa_pt mp', 'mp.nipd = ed.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->order_by('RAND()')
			->get();
		$output['berpartisipasi'] = $berpartisipasi;
		$output['tidak_berpartisipasi'] = $tidak_berpartisipasi;



		$html = $this->load->view('cetak/partisipan_epom', $output, true);
		$pdfFilePath  = 'EPOM ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function get_soal_epom($kode_soal)
	{
		return $this->db->where('kode_soal', $kode_soal)->get('siakad_master_soal_epom')->row();
	}

	public function hitung_score_kali_bobot($ratarata, $bobot)
	{
		return round($ratarata * $bobot, 2);
	}
}
