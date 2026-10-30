<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_epom extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_epom_model', 'Pusat_data_epom_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		/* $group = array(5);
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		} */
		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Pusat Data EPOM ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_pusat_data_epom';
		$output['parent_data_center']	= 'active open';
		$output['content'] 			= 'pusat_data_epom/pusat_data_epom_view';
		$output['after_page'] 		= 'pusat_data_epom/pusat_data_epom_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Pusat_data_epom_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_pusat_data_epom/cetak_pdf_epom') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Data EPOM</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->tag_name;
			$row[] = $n->nm_ptk;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pusat_data_epom_model->count_all(),
			"recordsFiltered" => $this->Pusat_data_epom_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_epom($id_jadwal)
	{

		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)->get()->row();

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = '';
		$output['nm_smt'] = $jadwal->nm_smt;

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

		$siakad_kritik_saran = $this->db
			->select('ed.kritik_saran, mp.nipd, m.nm_pd')
			->from('siakad_evaluasi_dosen ed')
			->join('mahasiswa_pt mp', 'mp.nipd = ed.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['id_jadwal' => $id_jadwal, 'status_eval' => 1])
			->get()->result();

		$t_perencanaan_pembelajaran = $siakad_evaluasi_dosen->perencanaan_pembelajaran_1 +
			$siakad_evaluasi_dosen->perencanaan_pembelajaran_2 +
			$siakad_evaluasi_dosen->perencanaan_pembelajaran_3 +
			$siakad_evaluasi_dosen->perencanaan_pembelajaran_4 +
			$siakad_evaluasi_dosen->perencanaan_pembelajaran_5;

		$t_pelaksanaan_pembelajaran = $siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8 +
			$siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9;

		$t_sarana = $siakad_evaluasi_dosen->sarana_1 + $siakad_evaluasi_dosen->sarana_2;


		$t_perencanaan_kali_bobot = $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_1, $this->get_soal_epom('perencanaan_pembelajaran_1')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_2, $this->get_soal_epom('perencanaan_pembelajaran_2')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_3, $this->get_soal_epom('perencanaan_pembelajaran_3')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_4, $this->get_soal_epom('perencanaan_pembelajaran_4')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->perencanaan_pembelajaran_5, $this->get_soal_epom('perencanaan_pembelajaran_5')->bobot_soal);

		$t_bobot_perencanaan = $this->db->select('sum(bobot_soal) as bobot_soal')
			->where('kategori', 'Perencanaan Pembelajaran')->get('siakad_master_soal_epom')->row()->bobot_soal;

		$t_pelaksanaan_kali_bobot = $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_1, $this->get_soal_epom('pelaksanaan_pembelajaran_1')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_2, $this->get_soal_epom('pelaksanaan_pembelajaran_2')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_3, $this->get_soal_epom('pelaksanaan_pembelajaran_3')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_4, $this->get_soal_epom('pelaksanaan_pembelajaran_4')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_5, $this->get_soal_epom('pelaksanaan_pembelajaran_5')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_6, $this->get_soal_epom('pelaksanaan_pembelajaran_6')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_7, $this->get_soal_epom('pelaksanaan_pembelajaran_7')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_8, $this->get_soal_epom('pelaksanaan_pembelajaran_8')->bobot_soal) + $this->hitung_score_kali_bobot($siakad_evaluasi_dosen->pelaksanaan_pembelajaran_9, $this->get_soal_epom('pelaksanaan_pembelajaran_9')->bobot_soal);

		$t_bobot_pelaksanaan = $this->db->select('sum(bobot_soal) as bobot_soal')
			->where('kategori', 'Pelaksanaan Pembelajaran')->get('siakad_master_soal_epom')->row()->bobot_soal;

		$t_sarana_kali_bobot = 	$this->hitung_score_kali_bobot($siakad_evaluasi_dosen->sarana_1, $this->get_soal_epom('sarana_1')->bobot_soal) +
			$this->hitung_score_kali_bobot($siakad_evaluasi_dosen->sarana_2, $this->get_soal_epom('sarana_2')->bobot_soal);

		$t_bobot_sarana = $this->db->select('sum(bobot_soal) as bobot_soal')
			->where('kategori', 'Sarana dan Prasarana')->get('siakad_master_soal_epom')->row()->bobot_soal;

		$tot_bobot_kali_nilai = $t_perencanaan_kali_bobot + $t_pelaksanaan_kali_bobot;
		$tot_bobot = $t_bobot_perencanaan + $t_bobot_pelaksanaan;
		$score_akhir = round($tot_bobot_kali_nilai / $tot_bobot, 2);

		$jumlah_peserta_edom = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_evaluasi_dosen')->num_rows();
		$jumlah_sudah_edom = $this->db->where('id_jadwal', $id_jadwal)->where('status_eval', 1)->get('siakad_evaluasi_dosen')->num_rows();
		$partisipasi = $jumlah_sudah_edom . ' / ' . $jumlah_peserta_edom;

		$tabel_html = $tabel_kritik = '';

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

		$tabel_kritik = "<table class='table table-black'>
						<thead>
							<tr>
								<th class='text-right'>No.</th>
								<th>NIM</th>
								<th>Nama</th>
								<th>Kritik Saran</th>
							</tr>
						</thead>
						<tbody>";

		$j = 1;
		foreach ($siakad_kritik_saran as $key => $value) {
			$tabel_kritik .= "<tr><td>$j</td><td>$value->nipd</td><td>$value->nm_pd</td><td>$value->kritik_saran</td></tr>";
			$j++;
		}

		$tabel_kritik .= "</tbody></table>";


		$output['partisipasi'] = $jumlah_sudah_edom . ' / ' . $jumlah_peserta_edom;
		$output['tabel_html'] = $tabel_html;
		$output['tabel_kritik'] = $tabel_kritik;

		$html = $this->load->view('cetak/pdf_pusat_data_epom', $output, true);
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
