<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Evaluasi_diri extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		$this->load->model('evaluasi_diri_model', 'evaluasi');

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Forbidden');
		}
	}

	public function index()
	{

		$output['judul_besar'] = 'Evaluasi Diri ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_evaluasi_diri';
		$output['parent_2'] = 'active open';
		$output['content'] = 'evaluasi_diri/evaluasi_diri_view';
		$output['after_page'] = 'evaluasi_diri/evaluasi_diri_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->evaluasi->get_datatables();
		$data = [];
		$no = $_POST['start'];
		$setting = $this->db->get('siakad_setting')->row();

		foreach ($list as $courses) {
			$no++;
			$row = [];
			$row[] = $courses->id_smt;
			$row[] = $courses->tag_name;
			$row[] = $courses->jumlah_peserta;
			$row[] = '<span class="badge bg-green">' . ($courses->cpmk ? number_format($courses->cpmk, 2) : '0.00') . '</span>';

			$row[] = '<a class="btn btn-default btn-sm green-stripe" 
						href="javascript:void()" 
						onclick="form_evaluasi_diri(' . "'" . $courses->id . "'" . ')">
						<i class="glyphicon glyphicon-pencil"></i> Evaluasi Diri</a>
					  <a class="btn btn-default btn-sm green-stripe" href="' . site_url('evaluasi_diri/cetak_pdf/' . $courses->id) . '"><i class="fa fa-file-pdf-o"></i> Download</a>
					 ';


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->evaluasi->count_all(),
			"recordsFiltered" => $this->evaluasi->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id_jadwal)
	{
		$cek = $this->db->where(['id_jadwal' => $id_jadwal])->get('siakad_evaluasi_diri')->num_rows();
		// Optimasi: Hitung distribusi nilai dan rata-rata secara lokal dengan 1 query saja
		$peserta_list = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_nilai_baru')->result();
		$jumlah_peserta = count($peserta_list);
		
		$total_angka = 0;
		$counts = ['A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'kosong' => 0];
		foreach ($peserta_list as $p) {
			$total_angka += (float)$p->nilai_angka;
			$huruf = trim($p->nilai_huruf ?? '');
			if ($huruf === '' || $huruf === null) {
				$counts['kosong']++;
			} else if (array_key_exists($huruf, $counts)) {
				$counts[$huruf]++;
			}
		}

		$nilai_rata_kelas = $jumlah_peserta > 0 ? round($total_angka / $jumlah_peserta, 2) : 0;
		
		$distribusi_nilai_a = ['jumlah' => $counts['A'], 'persen' => $jumlah_peserta > 0 ? round(($counts['A'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_b_plus = ['jumlah' => $counts['B+'], 'persen' => $jumlah_peserta > 0 ? round(($counts['B+'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_b = ['jumlah' => $counts['B'], 'persen' => $jumlah_peserta > 0 ? round(($counts['B'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_c_plus = ['jumlah' => $counts['C+'], 'persen' => $jumlah_peserta > 0 ? round(($counts['C+'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_c = ['jumlah' => $counts['C'], 'persen' => $jumlah_peserta > 0 ? round(($counts['C'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_d = ['jumlah' => $counts['D'], 'persen' => $jumlah_peserta > 0 ? round(($counts['D'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_e = ['jumlah' => $counts['E'], 'persen' => $jumlah_peserta > 0 ? round(($counts['E'] / $jumlah_peserta) * 100, 2) : 0];
		$distribusi_nilai_kosong = ['jumlah' => $counts['kosong'], 'persen' => $jumlah_peserta > 0 ? round(($counts['kosong'] / $jumlah_peserta) * 100, 2) : 0];
		$jadwal = $this->db->where('id', $id_jadwal)->from('siakad_jadwal')->get()->row();

		if ($cek == 0) {
			$insert_data = [
				'id_jadwal' => $id_jadwal,
				'int_jumlah_peserta' => $jumlah_peserta,
				'nilai_rata_kelas' => $nilai_rata_kelas,
				'distribusi_nilai_a' => $distribusi_nilai_a['jumlah'],
				'distribusi_nilai_b' => $distribusi_nilai_b['jumlah'],
				'distribusi_nilai_b_plus' => $distribusi_nilai_b_plus['jumlah'],
				'distribusi_nilai_c_plus' => $distribusi_nilai_c_plus['jumlah'],
				'distribusi_nilai_c' => $distribusi_nilai_c['jumlah'],
				'distribusi_nilai_d' => $distribusi_nilai_d['jumlah'],
				'distribusi_nilai_e' => $distribusi_nilai_e['jumlah'],
				'distribusi_nilai_kosong' => $distribusi_nilai_kosong['jumlah'],
				'persen_nilai_a' => $distribusi_nilai_a['persen'],
				'persen_nilai_b_plus' => $distribusi_nilai_b_plus['persen'],
				'persen_nilai_b' => $distribusi_nilai_b['persen'],
				'persen_nilai_c_plus' => $distribusi_nilai_c_plus['persen'],
				'persen_nilai_c' => $distribusi_nilai_c['persen'],
				'persen_nilai_d' => $distribusi_nilai_d['persen'],
				'persen_nilai_e' => $distribusi_nilai_e['persen'],
				'persen_nilai_kosong' => $distribusi_nilai_kosong['persen'],
				'metode_evaluasi_sikap' => 'Sikap',
				'metode_evaluasi_sikap_bobot' => $jadwal->sikap,
				'metode_evaluasi_pengetahuan' => 'Pengetahuan',
				'metode_evaluasi_pengetahuan_bobot' => '',
				'metode_evaluasi_pengetahuan_quiz' => 'Quiz',
				'metode_evaluasi_pengetahuan_quiz_bobot' => $jadwal->quiz,
				'metode_evaluasi_pengetahuan_uts' => 'Ujian Tengah Semester',
				'metode_evaluasi_pengetahuan_uts_bobot' => $jadwal->uts,
				'metode_evaluasi_pengetahuan_uas' => 'Ujian Akhir Semester',
				'metode_evaluasi_pengetahuan_uas_bobot' => $jadwal->uas,
				'metode_evaluasi_keterampilan' => 'Keterampilan',
				'metode_evaluasi_keterampilan_bobot' => '',
				'metode_evaluasi_keterampilan_tugas' => 'Tugas',
				'metode_evaluasi_keterampilan_tugas_bobot' => $jadwal->tugas_terstruktur,
				'metode_evaluasi_keterampilan_unjuk_kinerja' => 'Unjuk Kinerja',
				'metode_evaluasi_keterampilan_unjuk_kinerja_bobot' => $jadwal->unjuk_kinerja,
				'metode_evaluasi_keterampilan_presentasi' => 'Presentasi',
				'metode_evaluasi_keterampilan_presentasi_bobot' => $jadwal->presentasi,
				'createby' => $this->ion_auth->user()->row()->id,
				'createdate' => date('Y-m-d H:i:s'),
			];

			$this->evaluasi->save($insert_data);
		} else {
			$this->db->where(['id_jadwal' => $id_jadwal])
				->update('siakad_evaluasi_diri', [
					'id_jadwal' => $id_jadwal,
					'int_jumlah_peserta' => $jumlah_peserta,
					'nilai_rata_kelas' => $nilai_rata_kelas,
					'distribusi_nilai_a' => $distribusi_nilai_a['jumlah'],
					'distribusi_nilai_b' => $distribusi_nilai_b['jumlah'],
					'distribusi_nilai_b_plus' => $distribusi_nilai_b_plus['jumlah'],
					'distribusi_nilai_c_plus' => $distribusi_nilai_c_plus['jumlah'],
					'distribusi_nilai_c' => $distribusi_nilai_c['jumlah'],
					'distribusi_nilai_d' => $distribusi_nilai_d['jumlah'],
					'distribusi_nilai_e' => $distribusi_nilai_e['jumlah'],
					'distribusi_nilai_kosong' => $distribusi_nilai_kosong['jumlah'],
					'persen_nilai_a' => $distribusi_nilai_a['persen'],
					'persen_nilai_b_plus' => $distribusi_nilai_b_plus['persen'],
					'persen_nilai_b' => $distribusi_nilai_b['persen'],
					'persen_nilai_c_plus' => $distribusi_nilai_c_plus['persen'],
					'persen_nilai_c' => $distribusi_nilai_c['persen'],
					'persen_nilai_d' => $distribusi_nilai_d['persen'],
					'persen_nilai_e' => $distribusi_nilai_e['persen'],
					'persen_nilai_kosong' => $distribusi_nilai_kosong['persen'],
					'metode_evaluasi_sikap' => 'Sikap',
					'metode_evaluasi_sikap_bobot' => $jadwal->sikap,
					'metode_evaluasi_pengetahuan' => 'Pengetahuan',
					'metode_evaluasi_pengetahuan_bobot' => '',
					'metode_evaluasi_pengetahuan_quiz' => 'Quiz',
					'metode_evaluasi_pengetahuan_quiz_bobot' => $jadwal->quiz,
					'metode_evaluasi_pengetahuan_uts' => 'Ujian Tengah Semester',
					'metode_evaluasi_pengetahuan_uts_bobot' => $jadwal->uts,
					'metode_evaluasi_pengetahuan_uas' => 'Ujian Akhir Semester',
					'metode_evaluasi_pengetahuan_uas_bobot' => $jadwal->uas,
					'metode_evaluasi_keterampilan' => 'Keterampilan',
					'metode_evaluasi_keterampilan_bobot' => '',
					'metode_evaluasi_keterampilan_tugas' => 'Tugas',
					'metode_evaluasi_keterampilan_tugas_bobot' => $jadwal->tugas_terstruktur,
					'metode_evaluasi_keterampilan_unjuk_kinerja' => 'Unjuk Kinerja',
					'metode_evaluasi_keterampilan_unjuk_kinerja_bobot' => $jadwal->unjuk_kinerja,
					'metode_evaluasi_keterampilan_presentasi' => 'Presentasi',
					'metode_evaluasi_keterampilan_presentasi_bobot' => $jadwal->presentasi,
				]);
		}

		$data = $this->evaluasi->get_by_id($id_jadwal);
		$data->nm_smt = $this->db->where('id_smt', $jadwal->id_smt)->get('semester')->row()->nm_smt;
		$data->nm_mk = $jadwal->tag_name;
		$data->nm_ptk = $this->session->userdata('nm_ptk');

		$nm_lemb = $this->db->where('id_sms', $jadwal->id_sms)->get('sms')->row()->nm_lemb;
		$data->nm_lemb = $nm_lemb;

		echo json_encode($data);
	}

	private function get_by_nilai($nilai, $id_jadwal)
	{
		$jumlah = 0;
		if ($nilai == 'f') {
			$jumlah = $this->db->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => null])->get('siakad_nilai_baru')->num_rows();
		} else {
			$jumlah = $this->db->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => $nilai])->get('siakad_nilai_baru')->num_rows();
		}

		$jumlah_total = $this->db->where(['id_jadwal' => $id_jadwal])->get('siakad_nilai_baru')->num_rows();
		$persen = 0;
		if ($jumlah_total != 0) {
			$persen = round(($jumlah / $jumlah_total) * 100, 2);
		}

		$x = ['jumlah' => $jumlah, 'persen' => $persen];
		return $x;
	}

	public function ajax_update()
	{
		//$this->_validate();
		$materi_kuliah_referensi 	= $this->input->post('materi_kuliah_referensi');
		$materi_kuliah_diktat 		= $this->input->post('materi_kuliah_diktat');
		$materi_kuliah_handout 		= $this->input->post('materi_kuliah_handout');
		$materi_kuliah_lain_lain 	= $this->input->post('materi_kuliah_lain_lain');
		$metode_kuliah_lecturing 	= $this->input->post('metode_kuliah_lecturing');
		$metode_kuliah_diskusi 		= $this->input->post('metode_kuliah_diskusi');
		$metode_kuliah_smallgroup 	= $this->input->post('metode_kuliah_smallgroup');
		$metode_kuliah_presentasi_mhs = $this->input->post('metode_kuliah_presentasi_mhs');
		$metode_kuliah_studi_kasus 	= $this->input->post('metode_kuliah_studi_kasus');
		$metode_kuliah_studi_proyek = $this->input->post('metode_kuliah_studi_proyek');
		$metode_kuliah_lain_lain 	= $this->input->post('metode_kuliah_lain_lain');

		$metode_evaluasi_tugas_mandiri = $this->input->post('metode_evaluasi_tugas_mandiri');
		$metode_evaluasi_tugas_mandiri_jumlah = $this->input->post('metode_evaluasi_tugas_mandiri_jumlah');
		$metode_evaluasi_tugas_mandiri_bobot = $this->input->post('metode_evaluasi_tugas_mandiri_bobot');
		$metode_evaluasi_quiz = $this->input->post('metode_evaluasi_quiz');
		$metode_evaluasi_quiz_jumlah = $this->input->post('metode_evaluasi_quiz_jumlah');
		$metode_evaluasi_quiz_bobot = $this->input->post('metode_evaluasi_quiz_bobot');
		$metode_evaluasi_tugas_kelompok = $this->input->post('metode_evaluasi_tugas_kelompok');
		$metode_evaluasi_tugas_kelompok_jumlah = $this->input->post('metode_evaluasi_tugas_kelompok_jumlah');
		$metode_evaluasi_tugas_kelompok_bobot = $this->input->post('metode_evaluasi_tugas_kelompok_bobot');
		$metode_evaluasi_partisipasi = $this->input->post('metode_evaluasi_partisipasi');
		$metode_evaluasi_partisipasi_jumlah = $this->input->post('metode_evaluasi_partisipasi_jumlah');
		$metode_evaluasi_partisipasi_bobot = $this->input->post('metode_evaluasi_partisipasi_bobot');
		$metode_evaluasi_uts = $this->input->post('metode_evaluasi_uts');
		$metode_evaluasi_uts_jumlah = $this->input->post('metode_evaluasi_uts_jumlah');
		$metode_evaluasi_uts_bobot = $this->input->post('metode_evaluasi_uts_bobot');
		$metode_evaluasi_uas = $this->input->post('metode_evaluasi_uas');
		$metode_evaluasi_uas_jumlah = $this->input->post('metode_evaluasi_uas_jumlah');
		$metode_evaluasi_uas_bobot = $this->input->post('metode_evaluasi_uas_bobot');


		$metode_evaluasi_sikap 			= $this->input->post('metode_evaluasi_sikap');
		$metode_evaluasi_sikap_bobot 	= $this->input->post('metode_evaluasi_sikap_bobot');
		$metode_evaluasi_pengetahuan 	= $this->input->post('metode_evaluasi_pengetahuan');
		$metode_evaluasi_pengetahuan_bobot 		= $this->input->post('metode_evaluasi_pengetahuan_bobot');
		$metode_evaluasi_pengetahuan_quiz 		= $this->input->post('metode_evaluasi_pengetahuan_quiz');
		$metode_evaluasi_pengetahuan_quiz_bobot = $this->input->post('metode_evaluasi_pengetahuan_quiz_bobot');
		$metode_evaluasi_pengetahuan_uts 		= $this->input->post('metode_evaluasi_pengetahuan_uts');
		$metode_evaluasi_pengetahuan_uts_bobot 	= $this->input->post('metode_evaluasi_pengetahuan_uts_bobot');
		$metode_evaluasi_pengetahuan_uas 		= $this->input->post('metode_evaluasi_pengetahuan_uas');
		$metode_evaluasi_pengetahuan_uas_bobot = $this->input->post('metode_evaluasi_pengetahuan_uas_bobot');
		$metode_evaluasi_keterampilan = $this->input->post('metode_evaluasi_keterampilan');
		$metode_evaluasi_keterampilan_bobot = $this->input->post('metode_evaluasi_keterampilan_bobot');
		$metode_evaluasi_keterampilan_tugas = $this->input->post('metode_evaluasi_keterampilan_tugas');
		$metode_evaluasi_keterampilan_tugas_bobot = $this->input->post('metode_evaluasi_keterampilan_tugas_bobot');
		$metode_evaluasi_keterampilan_unjuk_kinerja = $this->input->post('metode_evaluasi_keterampilan_unjuk_kinerja');
		$metode_evaluasi_keterampilan_unjuk_kinerja_bobot = $this->input->post('metode_evaluasi_keterampilan_unjuk_kinerja_bobot');
		$metode_evaluasi_keterampilan_presentasi = $this->input->post('metode_evaluasi_keterampilan_presentasi');
		$metode_evaluasi_keterampilan_presentasi_bobot = $this->input->post('metode_evaluasi_keterampilan_presentasi_bobot');


		$jumlah_tatap_muka 			= empty($this->input->post('jumlah_tatap_muka')) ? null : $this->input->post('jumlah_tatap_muka');
		$persen_tatap_muka 			= empty($this->input->post('persen_tatap_muka')) ? null : $this->input->post('persen_tatap_muka');
		$materi_tersampaikan 		= empty($this->input->post('materi_tersampaikan')) ? null : $this->input->post('materi_tersampaikan');
		$cpmk                       = empty($this->input->post('cpmk')) ? null : $this->input->post('cpmk');
		$text_kelemahan_dan_kendala = $this->input->post('text_kelemahan_dan_kendala');
		$text_rekomendasi 			= $this->input->post('text_rekomendasi');




		$data = [
			'materi_kuliah_referensi' => $materi_kuliah_referensi,
			'materi_kuliah_diktat' => $materi_kuliah_diktat,
			'materi_kuliah_handout' => $materi_kuliah_handout,
			'materi_kuliah_lain_lain' => $materi_kuliah_lain_lain,
			'metode_kuliah_lecturing' => $metode_kuliah_lecturing,
			'metode_kuliah_diskusi' => $metode_kuliah_diskusi,
			'metode_kuliah_smallgroup' => $metode_kuliah_smallgroup,
			'metode_kuliah_presentasi_mhs' => $metode_kuliah_presentasi_mhs,
			'metode_kuliah_studi_kasus' => $metode_kuliah_studi_kasus,
			'metode_kuliah_studi_proyek' => $metode_kuliah_studi_proyek,
			'metode_kuliah_lain_lain' => $metode_kuliah_lain_lain,
			'metode_evaluasi_sikap' => $metode_evaluasi_sikap,
			'metode_evaluasi_sikap_bobot' => $metode_evaluasi_sikap_bobot,
			'metode_evaluasi_pengetahuan' => $metode_evaluasi_pengetahuan,
			'metode_evaluasi_pengetahuan_bobot' => $metode_evaluasi_pengetahuan_bobot,
			'metode_evaluasi_pengetahuan_quiz' => $metode_evaluasi_pengetahuan_quiz,
			'metode_evaluasi_pengetahuan_quiz_bobot' => $metode_evaluasi_pengetahuan_quiz_bobot,
			'metode_evaluasi_pengetahuan_uts' => $metode_evaluasi_pengetahuan_uts,
			'metode_evaluasi_pengetahuan_uts_bobot' => $metode_evaluasi_pengetahuan_uts_bobot,
			'metode_evaluasi_pengetahuan_uas' => $metode_evaluasi_pengetahuan_uas,
			'metode_evaluasi_pengetahuan_uas_bobot' => $metode_evaluasi_pengetahuan_uas_bobot,
			'metode_evaluasi_keterampilan' => $metode_evaluasi_keterampilan,
			'metode_evaluasi_keterampilan_bobot' => $metode_evaluasi_keterampilan_bobot,
			'metode_evaluasi_keterampilan_tugas' => $metode_evaluasi_keterampilan_tugas,
			'metode_evaluasi_keterampilan_tugas_bobot' => $metode_evaluasi_keterampilan_tugas_bobot,
			'metode_evaluasi_keterampilan_unjuk_kinerja' => $metode_evaluasi_keterampilan_unjuk_kinerja,
			'metode_evaluasi_keterampilan_unjuk_kinerja_bobot' => $metode_evaluasi_keterampilan_unjuk_kinerja_bobot,
			'metode_evaluasi_keterampilan_presentasi' => $metode_evaluasi_keterampilan_presentasi,
			'metode_evaluasi_keterampilan_presentasi_bobot' => $metode_evaluasi_keterampilan_presentasi_bobot,
			'metode_evaluasi_tugas_mandiri' => $metode_evaluasi_tugas_mandiri,
			'metode_evaluasi_tugas_mandiri_jumlah' => $metode_evaluasi_tugas_mandiri_jumlah,
			'metode_evaluasi_tugas_mandiri_bobot' => $metode_evaluasi_tugas_mandiri_bobot,
			'metode_evaluasi_quiz' => $metode_evaluasi_quiz,
			'metode_evaluasi_quiz_jumlah' => $metode_evaluasi_quiz_jumlah,
			'metode_evaluasi_quiz_bobot' => $metode_evaluasi_quiz_bobot,
			'metode_evaluasi_tugas_kelompok' => $metode_evaluasi_tugas_kelompok,
			'metode_evaluasi_tugas_kelompok_jumlah' => $metode_evaluasi_tugas_kelompok_jumlah,
			'metode_evaluasi_tugas_kelompok_bobot' => $metode_evaluasi_tugas_kelompok_bobot,
			'cpmk' => $cpmk,
			'metode_evaluasi_partisipasi' => $metode_evaluasi_partisipasi,
			'metode_evaluasi_partisipasi_jumlah' => $metode_evaluasi_partisipasi_jumlah,
			'metode_evaluasi_partisipasi_bobot' => $metode_evaluasi_partisipasi_bobot,
			'metode_evaluasi_uts' => $metode_evaluasi_uts,
			'metode_evaluasi_uts_jumlah' => $metode_evaluasi_uts_jumlah,
			'metode_evaluasi_uts_bobot' => $metode_evaluasi_uts_bobot,
			'metode_evaluasi_uas' => $metode_evaluasi_uas,
			'metode_evaluasi_uas_jumlah' => $metode_evaluasi_uas_jumlah,
			'metode_evaluasi_uas_bobot' => $metode_evaluasi_uas_bobot,

			'jumlah_tatap_muka' => $jumlah_tatap_muka,
			'persen_tatap_muka' => $persen_tatap_muka,
			'materi_tersampaikan' => $materi_tersampaikan,
			'text_kelemahan_dan_kendala' => $text_kelemahan_dan_kendala,
			'text_rekomendasi' => $text_rekomendasi,
			'updateby' => $this->ion_auth->user()->row()->id,
			'updatedate' => date('Y-m-d H:i:s'),
		];

		$this->evaluasi->update(['id_jadwal' => $this->input->post('id_jadwal')], $data);
		echo json_encode([
			"status" => TRUE,
			"csrfTokenName" => $this->security->get_csrf_token_name(),
			"csrfHash" => $this->security->get_csrf_hash()
		]);
	}

	public function cetak_pdf($id_jadwal)
	{
		$cek_data = $this->db->where(compact('id_jadwal'))->get('siakad_evaluasi_diri');
		if ($cek_data->num_rows() > 0) {
			$output['evaluasi'] = $this->evaluasi->get_by_id($id_jadwal);
			$jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();
			$dosen = $this->db->where('id_ptk', $jadwal->id_sdm)->get('siakad_dosen')->row();

			$output['jadwal'] = $jadwal;
			$output['dosen'] = $dosen;
			$nm_ptk = $dosen->nm_ptk;
			$nm_mk = $jadwal->tag_name;
			$nm_smt = '';

			$html = $this->load->view('cetak/evaluasi_diri_pdf', $output, true);
			$pdfFilePath  = $nm_ptk . "-" . $nm_mk . "(Evaluasi Diri).pdf";
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

			$mpdf->WriteHTML($html);
			$mpdf->Output($pdfFilePath, "I");
		} else {
			return show_error('Anda harus melakukan input evaluasi diri terlebih dahulu.');
		}
	}
}
