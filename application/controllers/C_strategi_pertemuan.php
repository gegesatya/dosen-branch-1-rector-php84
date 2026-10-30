<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller: C_strategi_pertemuan
 * Pengelola Dokumen & Manajemen Strategi Pertemuan Perkuliahan 16 Minggu & Draf BAP SCL
 * Mengadopsi & menyempurnakan proses dari C_rencana_pertemuan dengan tampilan clean document
 */
class C_strategi_pertemuan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rencana_pertemuan_model');
		$this->load->model('Rps_obe_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	/**
	 * Halaman Utama: Strategi Pertemuan & Draf BAP 16 Pertemuan (Elite Admin DataTables & Document Layout)
	 * Mendukung ID Jadwal (integer) maupun MD5 ID RPS (hash)
	 */
	public function index($id_param = NULL)
	{
		if (empty($id_param)) {
			redirect('C_jadwal', 'refresh');
			return;
		}

		// Jika parameter merupakan MD5 hash (32 karakter hex) -> buka berdasarkan RPS OBE
		if (is_string($id_param) && strlen($id_param) === 32 && !is_numeric($id_param)) {
			$this->view_rps($id_param);
			return;
		}

		$id_jadwal = (int)$id_param;
		if (!$this->authorization($id_jadwal)) {
			show_error("Anda tidak memiliki hak akses untuk membuka jadwal perkuliahan ini.", 403, "Akses Ditolak");
			return;
		}

		// 1. Ambil Data Jadwal, Hari, Ruang, Kelas & Prodi
		$jadwal = $this->db
			->select('j.*, h.nama_hari, r.nama_ruang')
			->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari', 'left')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang', 'left')
			->where('j.id', $id_jadwal)
			->get()
			->row();

		if (!$jadwal) {
			show_404("Jadwal perkuliahan tidak ditemukan.");
			return;
		}

		$meet = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->result();
		$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();
		$id_kls = $jadwal_detail ? $jadwal_detail->id_kls : null;
		$kelas_kuliah = $id_kls ? $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row() : null;

		$id_sdm  = $jadwal->id_sdm;
		$nm_mk   = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : trim((string)$jadwal->tag_name);
		$kode_mk = $kelas_kuliah ? trim((string)$kelas_kuliah->kode_mk_1) : '';
		$id_mk   = $kelas_kuliah ? $kelas_kuliah->id_mk : '';
		$nm_kls  = $jadwal_detail ? ($jadwal_detail->nm_kls ?? '') : '';

		// Ambil Nama Program Studi
		$nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
		if ($id_kls) {
			$kls_sms = $this->db->query("SELECT s.nm_lemb FROM feeder_kelas_kuliah kk JOIN sms s ON s.id_sms = kk.id_sms WHERE kk.id_kls = ? LIMIT 1", [$id_kls])->row();
			if ($kls_sms && !empty($kls_sms->nm_lemb)) $nm_lemb = $kls_sms->nm_lemb;
		}
		if ($nm_lemb === 'Politeknik Wilmar Bisnis Indonesia' && !empty($id_mk)) {
			$mk_sms = $this->db->query("SELECT s.nm_lemb FROM feeder_mata_kuliah mk JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.id_mk = ? LIMIT 1", [$id_mk])->row();
			if ($mk_sms && !empty($mk_sms->nm_lemb)) $nm_lemb = $mk_sms->nm_lemb;
		}

		// 2. Lookup Dokumen RPS OBE
		$rps_obe = null;
		if (!empty($kode_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($id_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['id_mk' => $id_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($kode_mk)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}

		// Fallback ke legacy jika tidak ada OBE RPS
		$rps = null;
		if ($rps_obe) {
			$rps = $rps_obe;
			$creator = (!empty($rps->id_ptk)) ? ($this->db->where('id_ptk', $rps->id_ptk)->get('siakad_dosen')->row()->nm_ptk ?? '') : '';
		} else {
			$rps_prodi = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_new_rps_mk_prodi')->row();
			$id_rps = $rps_prodi ? $rps_prodi->id_rps : null;
			$rps = $id_rps ? $this->db->where(compact('id_rps'))->get('siakad_new_rps_online')->row() : null;
			$creator = ($rps && !empty($rps->id_ptk)) ? ($this->db->where('id_ptk', $rps->id_ptk)->get('siakad_dosen')->row()->nm_ptk ?? '') : '';
		}

		if (!$rps_obe) {
			$rps_obe = (object)[
				'id_rps'                        => 0,
				'id_ptk'                        => $id_sdm,
				'kode_mk'                       => $kode_mk ?: 'MK',
				'nm_mk'                         => $nm_mk ?: 'Mata Kuliah',
				'sks_mk'                        => $kelas_kuliah->sks_mk ?? 3,
				'semester'                      => 'Semester Aktif',
				'dosen_pengampu'                => $this->session->userdata('user_name') ?: 'Dosen Pengampu',
				'skenario_pembelajaran_markdown'=> '',
				'raw_ai_output'                 => '',
				'tujuan_belajar'                => '',
				'prodi'                         => $nm_lemb
			];
		}

		$data_rps_detail = !empty($rps_obe->id_rps) ? $this->Rps_obe_model->get_details($rps_obe->id_rps) : [];
		$total_sks = (int)($rps_obe->sks_mk ?: ($kelas_kuliah->sks_mk ?? 3));
		$rps_obe->sks_total_display = $total_sks . ' SKS';
		$rps_obe->nm_kls_display    = $nm_kls ? "Kelas {$nm_kls}" : '';
		$rps_obe->jadwal_info       = ($jadwal->nama_hari ?: '') . ($jadwal->jam_mulai ? (', ' . substr($jadwal->jam_mulai,0,5) . ' - ' . substr($jadwal->jam_selesai,0,5) . ' WIB') : '') . ($jadwal->nama_ruang ? (' • ' . $jadwal->nama_ruang) : '');

		// 3. Ambil 16 Rencana Pertemuan dari Database Jadwal
		$rencana_db = $this->db->where('id_jadwal', $id_jadwal)->order_by('pertemuan', 'ASC')->get('siakad_rencana_pertemuan')->result();
		$meetings = [];

		if (!empty($rencana_db)) {
			foreach ($rencana_db as $r) {
				$meet_no = (int)$r->pertemuan;
				$meetings[$meet_no] = [
					'pertemuan'            => $meet_no,
					'minggu'               => (int)($r->minggu ?: $meet_no),
					'tanggal_rencana'      => $r->tanggal_rencana,
					'jam_mulai'            => $r->jam_mulai,
					'jam_selesai'          => $r->jam_selesai,
					'sub_cpmk'             => $r->sub_cpmk,
					'bahan_kajian'         => $r->bacaan_materi ?: $r->berita_acara_rencana,
					'bacaan_materi'        => $r->bacaan_materi ?: $r->berita_acara_rencana,
					'metode'               => $r->metode_pembelajaran ?: 'Kuliah Interaktif & Diskusi Kelompok',
					'berita_acara_rencana' => $r->metode_pembelajaran,
					'skenario_scl'         => $r->berita_acara_rencana,
					'draf_bap'             => $r->draf_bap ?: $r->berita_acara_rencana,
					'evaluasi'             => $r->indikator_evaluasi,
					'indikator_evaluasi'   => $r->indikator_evaluasi,
					'jam_teori'            => (float)($r->jam_teori ?: 1),
					'jam_praktek'          => (float)($r->jam_praktek ?: 2),
					'is_realisasi'         => (int)$r->is_realisasi,
					'tanggal_realisasi'    => $r->tanggal_realisasi
				];
			}
		}

		// Parse skenario lengkap dari RPS OBE & sinkronkan detail SCL
		$skenario_md = $rps_obe->skenario_pembelajaran_markdown ?: $rps_obe->raw_ai_output;
		$parsed = $this->_parse_skenario_pertemuan($skenario_md, $data_rps_detail);

		foreach ($meetings as $meet_no => &$m) {
			if (!empty($parsed[$meet_no]['skenario_scl'])) {
				if (empty($m['skenario_scl']) || strlen($m['skenario_scl']) < strlen($parsed[$meet_no]['skenario_scl'])) {
					$m['skenario_scl'] = $parsed[$meet_no]['skenario_scl'];
				}
			}
			if (!empty($parsed[$meet_no]['draf_bap'])) {
				if (empty($m['draf_bap']) || strlen($m['draf_bap']) < strlen($parsed[$meet_no]['draf_bap'])) {
					$m['draf_bap'] = $parsed[$meet_no]['draf_bap'];
				}
			}
			if (!empty($parsed[$meet_no]['topik_pertemuan'])) {
				$m['topik_pertemuan'] = $parsed[$meet_no]['topik_pertemuan'];
			}
		}
		unset($m);

		for ($i = 1; $i <= 16; $i++) {
			if (!isset($meetings[$i]) && isset($parsed[$i])) {
				$meetings[$i] = $parsed[$i];
			}
		}

		ksort($meetings);

		$i_realisasi      = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();
		$i_pertemuan      = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->num_rows();
		$jumlah_mahasiswa = $this->db->where(compact('id_jadwal'))->get('siakad_nilai_baru')->num_rows();

		$kaprodi_info = $this->_resolve_kaprodi($rps_obe, null, $nm_lemb);

		$output['judul_besar']      = $jadwal->tag_name;
		$output['judul_kecil']      = ($jadwal->nama_hari ? $jadwal->nama_hari . ', ' : '') . substr($jadwal->jam_mulai,0,5) . ' sd ' . substr($jadwal->jam_selesai,0,5) . ($jadwal->nama_ruang ? ' (' . $jadwal->nama_ruang . ')' : '');
		$output['meet']             = $meet;
		$output['content']          = 'strategi_pertemuan/strategi_pertemuan_view';
		$output['after_page']       = 'strategi_pertemuan/strategi_pertemuan_after_page';
		$output['judul']            = 'Strategi Pertemuan ' . $jadwal->tag_name;
		$output['i_realisasi']      = $i_realisasi;
		$output['i_pertemuan']      = $i_pertemuan;
		$output['jumlah_mahasiswa'] = $jumlah_mahasiswa;
		$output['creator']          = $creator;
		$output['rps_obe']          = $rps_obe;
		$output['data_rps']         = $rps_obe;
		$output['data_rps_detail']  = $data_rps_detail;
		$output['meetings']         = $meetings;
		$output['kaprodi_info']     = $kaprodi_info;
		$output['nm_lemb']          = $nm_lemb;
		$output['id_jadwal']        = $id_jadwal;
		$output['jadwal']           = $jadwal;
		$output['is_generate_bap']  = $jadwal->is_generate_bap;

		$this->load->view('master_page_elite', $output);
	}

	/**
	 * Halaman Cetak / Dokumen Fullscreen Strategi Pertemuan & Draf BAP 16 Pertemuan
	 */
	public function dokumen($id_jadwal = NULL)
	{
		$this->cetak($id_jadwal);
	}

	public function cetak($id_jadwal = NULL)
	{
		if (empty($id_jadwal)) {
			redirect('C_jadwal', 'refresh');
			return;
		}

		if (!$this->authorization($id_jadwal)) {
			show_error("Anda tidak memiliki hak akses untuk membuka dokumen ini.", 403, "Akses Ditolak");
			return;
		}

		$jadwal = $this->db
			->select('j.*, h.nama_hari, r.nama_ruang')
			->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari', 'left')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang', 'left')
			->where('j.id', $id_jadwal)
			->get()
			->row();

		if (!$jadwal) {
			show_404("Jadwal perkuliahan tidak ditemukan.");
			return;
		}

		$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();
		$id_kls = $jadwal_detail ? $jadwal_detail->id_kls : null;
		$kelas_kuliah = $id_kls ? $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row() : null;

		$id_sdm  = $jadwal->id_sdm;
		$nm_mk   = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : trim((string)$jadwal->tag_name);
		$kode_mk = $kelas_kuliah ? trim((string)$kelas_kuliah->kode_mk_1) : '';
		$id_mk   = $kelas_kuliah ? $kelas_kuliah->id_mk : '';
		$nm_kls  = $jadwal_detail ? ($jadwal_detail->nm_kls ?? '') : '';

		$nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
		if ($id_kls) {
			$kls_sms = $this->db->query("SELECT s.nm_lemb FROM feeder_kelas_kuliah kk JOIN sms s ON s.id_sms = kk.id_sms WHERE kk.id_kls = ? LIMIT 1", [$id_kls])->row();
			if ($kls_sms && !empty($kls_sms->nm_lemb)) $nm_lemb = $kls_sms->nm_lemb;
		}
		if ($nm_lemb === 'Politeknik Wilmar Bisnis Indonesia' && !empty($id_mk)) {
			$mk_sms = $this->db->query("SELECT s.nm_lemb FROM feeder_mata_kuliah mk JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.id_mk = ? LIMIT 1", [$id_mk])->row();
			if ($mk_sms && !empty($mk_sms->nm_lemb)) $nm_lemb = $mk_sms->nm_lemb;
		}

		$rps_obe = null;
		if (!empty($kode_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($id_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['id_mk' => $id_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk) && !empty($id_sdm)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk, 'id_ptk' => $id_sdm])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($kode_mk)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}

		if (!$rps_obe) {
			$rps_obe = (object)[
				'id_rps'                        => 0,
				'id_ptk'                        => $id_sdm,
				'kode_mk'                       => $kode_mk ?: 'MK',
				'nm_mk'                         => $nm_mk ?: 'Mata Kuliah',
				'sks_mk'                        => $kelas_kuliah->sks_mk ?? 3,
				'semester'                      => 'Semester Aktif',
				'dosen_pengampu'                => $this->session->userdata('user_name') ?: 'Dosen Pengampu',
				'skenario_pembelajaran_markdown'=> '',
				'raw_ai_output'                 => '',
				'tujuan_belajar'                => '',
				'prodi'                         => $nm_lemb
			];
		}

		$data_rps_detail = !empty($rps_obe->id_rps) ? $this->Rps_obe_model->get_details($rps_obe->id_rps) : [];
		$total_sks = (int)($rps_obe->sks_mk ?: ($kelas_kuliah->sks_mk ?? 3));
		$rps_obe->sks_total_display = $total_sks . ' SKS';
		$rps_obe->nm_kls_display    = $nm_kls ? "Kelas {$nm_kls}" : '';
		$rps_obe->jadwal_info       = ($jadwal->nama_hari ?: '') . ($jadwal->jam_mulai ? (', ' . substr($jadwal->jam_mulai,0,5) . ' - ' . substr($jadwal->jam_selesai,0,5) . ' WIB') : '') . ($jadwal->nama_ruang ? (' • ' . $jadwal->nama_ruang) : '');

		$rencana_db = $this->db->where('id_jadwal', $id_jadwal)->order_by('pertemuan', 'ASC')->get('siakad_rencana_pertemuan')->result();
		$meetings = [];

		if (!empty($rencana_db)) {
			foreach ($rencana_db as $r) {
				$meet_no = (int)$r->pertemuan;
				$meetings[$meet_no] = [
					'pertemuan'            => $meet_no,
					'minggu'               => (int)($r->minggu ?: $meet_no),
					'tanggal_rencana'      => $r->tanggal_rencana,
					'jam_mulai'            => $r->jam_mulai,
					'jam_selesai'          => $r->jam_selesai,
					'sub_cpmk'             => $r->sub_cpmk,
					'bahan_kajian'         => $r->bacaan_materi ?: $r->berita_acara_rencana,
					'bacaan_materi'        => $r->bacaan_materi ?: $r->berita_acara_rencana,
					'metode'               => $r->metode_pembelajaran ?: 'Kuliah Interaktif & Diskusi Kelompok',
					'berita_acara_rencana' => $r->metode_pembelajaran,
					'skenario_scl'         => $r->berita_acara_rencana,
					'draf_bap'             => $r->draf_bap ?: $r->berita_acara_rencana,
					'evaluasi'             => $r->indikator_evaluasi,
					'indikator_evaluasi'   => $r->indikator_evaluasi,
					'jam_teori'            => (float)($r->jam_teori ?: 1),
					'jam_praktek'          => (float)($r->jam_praktek ?: 2),
					'is_realisasi'         => (int)$r->is_realisasi,
					'tanggal_realisasi'    => $r->tanggal_realisasi
				];
			}
		}

		if (count($meetings) < 16) {
			$skenario_md = $rps_obe->skenario_pembelajaran_markdown ?: $rps_obe->raw_ai_output;
			$parsed = $this->_parse_skenario_pertemuan($skenario_md, $data_rps_detail);
			foreach ($parsed as $p_num => $p) {
				$meetings[$p_num] ??= $p;
			}
		}

		ksort($meetings);

		$kaprodi_info = $this->_resolve_kaprodi($rps_obe, null, $nm_lemb);

		$output['data_rps']        = $rps_obe;
		$output['data_rps_detail'] = $data_rps_detail;
		$output['nm_lemb']         = $nm_lemb;
		$output['kaprodi_info']    = $kaprodi_info;
		$output['meetings']        = $meetings;
		$output['id_jadwal']       = $id_jadwal;
		$output['jadwal']          = $jadwal;
		$output['is_mpdf']         = false;

		if ($this->input->get('pdf') == '1') {
			$this->_output_pdf($output, $rps_obe->nm_mk);
			return;
		}

		$this->load->view('cetak/rps_obe/rps_html_print_skenario', $output);
	}

	/**
	 * Preview Strategi Pertemuan berdasarkan MD5 ID RPS OBE
	 */
	public function view_rps($id_md5 = null)
	{
		$data_rps = $this->db->where('md5(id_rps)', $id_md5)->get('siakad_rps_obe')->row();
		if (!$data_rps) {
			show_404('Dokumen RPS OBE tidak ditemukan.');
			return;
		}

		$details = $this->Rps_obe_model->get_details($data_rps->id_rps);

		$id_mk = $data_rps->id_mk;
		$nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
		$mk_row = null;
		if (!empty($id_mk)) {
			$mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
			if ($mk_row && !empty($mk_row->id_sms)) {
				$sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
				if ($sms_row && !empty($sms_row->nm_lemb)) $nm_lemb = $sms_row->nm_lemb;
			}
		}

		$total_sks = (int)($data_rps->sks_mk ?: ($mk_row->sks_mk ?? 3));
		$data_rps->sks_total_display = $total_sks . ' SKS';

		$kaprodi_info = $this->_resolve_kaprodi($data_rps, $mk_row, $nm_lemb);

		$skenario_md = $data_rps->skenario_pembelajaran_markdown ?: $data_rps->raw_ai_output;
		$meetings = $this->_parse_skenario_pertemuan($skenario_md, $details);

		$output['data_rps']        = $data_rps;
		$output['data_rps_detail'] = $details;
		$output['nm_lemb']         = $nm_lemb;
		$output['kaprodi_info']    = $kaprodi_info;
		$output['meetings']        = $meetings;
		$output['id_md5']          = $id_md5;
		$output['is_mpdf']         = false;

		if ($this->input->get('pdf') == '1') {
			$this->_output_pdf($output, $data_rps->nm_mk);
			return;
		}

		$this->load->view('cetak/rps_obe/rps_html_print_skenario', $output);
	}

	/**
	 * Helper: Export PDF menggunakan mPDF
	 */
	private function _output_pdf($output, $nm_mk = '')
	{
		$output['is_mpdf'] = true;
		$style = $this->load->view('cetak/rps_obe/_pdf_rps_style', $output, true);
		$html  = $this->load->view('cetak/rps_obe/rps_html_print_skenario', $output, true);

		$doc_title    = 'Strategi Pertemuan & Draf BAP ' . $nm_mk;
		$pdfFilePath  = 'Strategi_Pertemuan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nm_mk) . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetTitle($doc_title);
		$mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
		$mpdf->SetSubject($doc_title);

		$clean_style = preg_replace('/<\/?style[^>]*>/i', '', $style);
		$mpdf->WriteHTML($clean_style, \Mpdf\HTMLParserMode::HEADER_CSS);
		$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		$mpdf->Output($pdfFilePath, "I");
	}

	/**
	 * Helper: Parse 16 Pertemuan dari Markdown / Matriks RPS Detail
	 */
	private function _parse_skenario_pertemuan($md, $details = [])
	{
		$meetings = [];

		if (!empty($md)) {
			$parts = preg_split('/(?=###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*\d+)/i', $md);

			foreach ($parts as $part) {
				if (!preg_match('/###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*(\d+)(?:\s*[:\-]\s*([^\n]+))?/i', $part, $m_num)) {
					continue;
				}
				$meet_no = (int)$m_num[1];
				if ($meet_no < 1 || $meet_no > 16) continue;

				$topik_pertemuan = !empty($m_num[2]) ? trim($m_num[2]) : '';

				$sub_cpmk = '';
				if (preg_match('/(?:\*|\-)?\s*\*\*Kemampuan Akhir[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
					$sub_cpmk = trim($m[1]);
				}

				$bahan_kajian = '';
				if (preg_match('/(?:\*|\-)?\s*\*\*Bahan Kajian\*\*:\s*([^\n]+)/i', $part, $m)) {
					$bahan_kajian = trim($m[1]);
				}
				if (empty($bahan_kajian) && !empty($topik_pertemuan)) {
					$bahan_kajian = $topik_pertemuan;
				}

				$metode = '';
				if (preg_match('/(?:\*|\-)?\s*\*\*Bentuk & Metode[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
					$metode = trim($m[1]);
				}

				$sumber = '';
				if (preg_match('/(?:\*|\-)?\s*\*\*Sumber & Media Belajar\*\*:\s*([^\n]+)/i', $part, $m)) {
					$sumber = trim($m[1]);
				}

				$evaluasi = '';
				if (preg_match('/(?:\*|\-)?\s*\*\*Kriteria & Indikator[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
					$evaluasi = trim($m[1]);
				}

				$jam_t = 1; $jam_p = 2;
				if (preg_match('/(\d+)\s*Menit Teori\s*\/\s*(\d+)\s*Menit Praktek/i', $part, $m)) {
					$jam_t = round(((int)$m[1]) / 50, 1);
					$jam_p = round(((int)$m[2]) / 50, 1);
				}

				$skenario_scl = '';
				if (preg_match('/####\s*(?:🎯\s*)?(?:Skenario Langkah Pembelajaran|Skenario Strategi Pembelajaran|Skenario Strategi Pembelajaran \(SCL\)|Sintaks SCL|Skenario Langkah Pelaksanaan)[^\n]*\n([\s\S]*?)(?=(?:####|---|```text|\Z))/i', $part, $m)) {
					$skenario_scl = trim($m[1]);
				}

				$draf_bap = '';
				if (preg_match('/####\s*(?:📝\s*)?[^\n]*(?:Draft Evaluasi|Draf Evaluasi|Draf Realisasi Berita Acara|Draf BAP|BAP Faktual)[^\n]*\n(?:```(?:text|markdown)?\s*)?([\s\S]*?)(?:```|---|###|\Z)/i', $part, $m)) {
					$draf_bap = trim(str_replace(['```text', '```'], '', $m[1]));
				}

				$meetings[$meet_no] = [
					'pertemuan'            => $meet_no,
					'minggu'               => $meet_no,
					'topik_pertemuan'      => $topik_pertemuan,
					'sub_cpmk'             => $sub_cpmk,
					'bahan_kajian'         => $bahan_kajian,
					'bacaan_materi'        => $bahan_kajian,
					'metode'               => $metode,
					'berita_acara_rencana' => $metode,
					'sumber'               => $sumber,
					'evaluasi'             => $evaluasi,
					'indikator_evaluasi'   => $evaluasi,
					'jam_teori'            => $jam_t,
					'jam_praktek'          => $jam_p,
					'skenario_scl'         => $skenario_scl,
					'draf_bap'             => $draf_bap
				];
			}
		}

		// Fallback & Pengayaan matriks 16 pertemuan dari detail RPS OBE
		for ($i = 1; $i <= 16; $i++) {
			$detail_row = null;
			if (!empty($details)) {
				foreach ($details as $d) {
					$minggu_val = (int)($d->kolom_1 ?? $d->minggu ?? $d->minggu_ke ?? 0);
					if ($minggu_val === $i) {
						$detail_row = $d;
						break;
					}
				}
			}

			if (!isset($meetings[$i])) {
				$is_eval = ($i == 8) ? 'Evaluasi Tengah Semester (UTS)' : (($i == 16) ? 'Evaluasi Akhir Semester (UAS)' : '');
				$meetings[$i] = [
					'pertemuan'            => $i,
					'minggu'               => $i,
					'topik_pertemuan'      => $detail_row->kolom_4 ?? ($is_eval ?: 'Pertemuan ' . $i),
					'sub_cpmk'             => $detail_row->kolom_3 ?? ($is_eval ?: '-'),
					'bahan_kajian'         => $detail_row->kolom_4 ?? ($is_eval ? 'Ujian / Asesmen CPMK' : '-'),
					'bacaan_materi'        => $detail_row->kolom_4 ?? ($is_eval ? 'Ujian / Asesmen CPMK' : '-'),
					'metode'               => trim(($detail_row->kolom_6 ?? '') ?: ($detail_row->kolom_5 ?? ($is_eval ? 'Asesmen Tertulis/Proyek' : 'Kuliah Interaktif & Diskusi'))),
					'berita_acara_rencana' => trim(($detail_row->kolom_6 ?? '') ?: ($detail_row->kolom_5 ?? '')),
					'skenario_scl'         => $detail_row->kolom_7 ?? ($is_eval ? 'Pelaksanaan Ujian Evaluasi Capaian Pembelajaran' : 'Pendahuluan, Eksplorasi Konsep, Diskusi Kasus, Refleksi & Penutup.'),
					'draf_bap'             => $detail_row->kolom_4 ?? ($is_eval ?: 'Pembahasan materi pembelajaran dan studi kasus terapan.'),
					'evaluasi'             => $detail_row->kolom_10 ?? '',
					'indikator_evaluasi'   => $detail_row->kolom_10 ?? '',
					'jam_teori'            => (float)($detail_row->kolom_8 ?? 1),
					'jam_praktek'          => (float)($detail_row->kolom_9 ?? 2),
					'sumber'               => $detail_row->kolom_13 ?? 'Pustaka Utama RPS'
				];
			} else {
				// Pengayaan jika data pertemuan dari markdown belum memuat Sub-CPMK / Metode / Bahan Kajian lengkap
				if ($detail_row) {
					if (empty($meetings[$i]['sub_cpmk']) && !empty($detail_row->kolom_3)) {
						$meetings[$i]['sub_cpmk'] = $detail_row->kolom_3;
					}
					if ((empty($meetings[$i]['bahan_kajian']) || $meetings[$i]['bahan_kajian'] === $meetings[$i]['topik_pertemuan']) && !empty($detail_row->kolom_4)) {
						$meetings[$i]['bahan_kajian'] = $detail_row->kolom_4;
						$meetings[$i]['bacaan_materi'] = $detail_row->kolom_4;
					}
					if (empty($meetings[$i]['metode'])) {
						$metode_val = trim(($detail_row->kolom_6 ?? '') ?: ($detail_row->kolom_5 ?? ''));
						$meetings[$i]['metode'] = $metode_val;
						$meetings[$i]['berita_acara_rencana'] = $metode_val;
					}
					if (empty($meetings[$i]['evaluasi']) && !empty($detail_row->kolom_10)) {
						$eval_val = $detail_row->kolom_10 . (!empty($detail_row->kolom_11) ? ' - ' . $detail_row->kolom_11 : '');
						$meetings[$i]['evaluasi'] = $eval_val;
						$meetings[$i]['indikator_evaluasi'] = $eval_val;
					}
					if (empty($meetings[$i]['sumber']) && !empty($detail_row->kolom_13)) {
						$meetings[$i]['sumber'] = $detail_row->kolom_13;
					}
				}
			}
		}

		ksort($meetings);
		return $meetings;
	}

	/**
	 * Helper: Authorization Check
	 */
	private function authorization($id_jadwal)
	{
		$id_logged_in_user = $this->ion_auth->user()->row();
		$id_ptk_user = $id_logged_in_user->id_ptk ?? '';

		$jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();
		if (!$jadwal) return false;

		return ($jadwal->id_sdm === $id_ptk_user || $jadwal->id_sdm_2 === $id_ptk_user || $jadwal->id_sdm_3 === $id_ptk_user);
	}

	/**
	 * Helper: Resolve Kaprodi Data
	 */
	private function _resolve_kaprodi($data_rps, $mk_row = null, $nm_lemb = '')
	{
		$kaprodi_data = [
			'nama'    => '',
			'nidn'    => '',
			'nip'     => '',
			'id_ptk'  => '',
			'jabatan' => 'Ketua Program Studi'
		];

		if (!empty($data_rps->kaprodi_rps)) {
			$dsn = $this->db->where('id_ptk', $data_rps->kaprodi_rps)->get('siakad_dosen')->row();
			if ($dsn) {
				$kaprodi_data['nama']   = $dsn->nm_ptk;
				$kaprodi_data['nidn']   = $dsn->nidn;
				$kaprodi_data['nip']    = $dsn->nip;
				$kaprodi_data['id_ptk'] = $dsn->id_ptk;
				return (object)$kaprodi_data;
			}
		}

		return (object)$kaprodi_data;
	}

	// =========================================================================
	// CRUD & AJAX PROCESSES INHERITED FROM C_rencana_pertemuan
	// =========================================================================

	public function ajax_list($id_kls = NULL)
	{
		$list = $this->Rencana_pertemuan_model->get_datatables($id_kls);
		
		// Preload RPS OBE & parse skenario SCL untuk dirender langsung di cell datatables
		$parsed_skenario = [];
		if (!empty($list) && !empty($list[0]->id_jadwal)) {
			$id_jadwal = (int)$list[0]->id_jadwal;
			$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();
			$nm_mk = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : '';
			$kode_mk = '';
			if ($jadwal_detail && !empty($jadwal_detail->id_kls)) {
				$kelas = $this->db->where('id_kls', $jadwal_detail->id_kls)->get('feeder_kelas_kuliah')->row();
				if ($kelas) {
					$kode_mk = $kelas->kode_mk_1 ?? '';
					if (!$nm_mk && !empty($kelas->nm_mk_1)) $nm_mk = $kelas->nm_mk_1;
				}
			}
			$rps_obe = null;
			if (!empty($kode_mk)) {
				$rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
			}
			if (!$rps_obe && !empty($nm_mk)) {
				$rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
			}
			if ($rps_obe) {
				$details = $this->Rps_obe_model->get_details($rps_obe->id_rps);
				$skenario_md = $rps_obe->skenario_pembelajaran_markdown ?: $rps_obe->raw_ai_output;
				$parsed_skenario = $this->_parse_skenario_pertemuan($skenario_md, $details);
			}
		}

		$data = [];
		$no = $_POST['start'] ?? 0;
		foreach ($list as $rencana) {
			$meet_no = (int)$rencana->pertemuan;

			if ($rencana->is_realisasi == 1) {
				$status_realisasi = '<span class="badge badge-success px-2 py-1"><i class="fa fa-check"></i> Sudah</span>';
				$tgl_realisasi = '<div class="small text-muted mt-1">Tgl: ' . tgl_indo(date('Y-m-d', strtotime($rencana->tanggal_realisasi))) . '</div>';
			} else {
				$status_realisasi = '<span class="badge badge-warning px-2 py-1"><i class="fa fa-clock-o"></i> Belum</span>';
				$tgl_realisasi = '';
			}
			$no++;

			$raw_t = (float)($rencana->jam_teori ?: 1);
			$raw_p = (float)($rencana->jam_praktek ?: 2);
			$menit_t = $raw_t <= 10 ? round($raw_t * 50) : ($raw_t > 300 ? 50 : round($raw_t));
			$menit_p = $raw_p <= 10 ? round($raw_p * 50) : ($raw_p > 600 ? 100 : round($raw_p));

			// Kolom 0: Info Pertemuan, Jadwal & Durasi
			$col_jadwal = '<div style="min-width: 150px;">'
				. '<div class="font-weight-bold" style="font-size: 15px; color: #1e40af;">Meet ' . $meet_no . '</div>'
				. '<div class="text-dark font-weight-bold mt-1" style="font-size: 12.5px;"><i class="fa fa-calendar mr-1 text-primary"></i>' . tgl_indo($rencana->tanggal_rencana) . '</div>'
				. '<div class="text-muted small mt-1"><i class="fa fa-clock-o mr-1"></i>' . substr($rencana->jam_mulai, 0, 5) . ' - ' . substr($rencana->jam_selesai, 0, 5) . '</div>'
				. '<div class="mt-2"><span class="badge badge-light border text-dark" style="font-size: 11px;"><i class="fa fa-hourglass-half text-info mr-1"></i>' . $menit_t . 'm Teori / ' . $menit_p . 'm Praktik</span></div>'
				. '</div>';

			// Resolve Topik, Sub-CPMK, Metode, SCL & BAP
			$topik = !empty($parsed_skenario[$meet_no]['topik_pertemuan']) ? $parsed_skenario[$meet_no]['topik_pertemuan'] : (!empty($rencana->bacaan_materi) ? $rencana->bacaan_materi : $rencana->berita_acara_rencana);
			$topik_clean = strip_tags(str_replace(['<br />', '<br>', "\n"], ' ', $topik));

			$sub_cpmk = !empty($rencana->sub_cpmk) ? $rencana->sub_cpmk : ($parsed_skenario[$meet_no]['sub_cpmk'] ?? '');
			$metode = !empty($rencana->metode_pembelajaran) ? $rencana->metode_pembelajaran : ($parsed_skenario[$meet_no]['metode'] ?? 'Kuliah Interaktif, Diskusi Kelompok');
			$skenario_scl = !empty($parsed_skenario[$meet_no]['skenario_scl']) ? $parsed_skenario[$meet_no]['skenario_scl'] : (!empty($rencana->berita_acara_rencana) ? $rencana->berita_acara_rencana : '');
			$draf_bap = !empty($rencana->draf_bap) ? $rencana->draf_bap : ($parsed_skenario[$meet_no]['draf_bap'] ?? '');

			// Kolom 1: Ringkasan Pertemuan (Topik + Sub-CPMK + Metode)
			// Skenario SCL lengkap & Draf BAP sudah tampil di dalam Modal Edit
			$has_skenario = !empty($skenario_scl) && $skenario_scl != '-';
			$has_bap      = !empty($draf_bap)     && $draf_bap != '-';

			$col_cockpit = '<div style="min-width: 250px;">';

			// Topik
			$col_cockpit .= '<div class="mb-2">'
				. '<div style="font-size: 14px; font-weight: 700; color: #0f172a; line-height: 1.35;">'
				. '📌 ' . htmlspecialchars($topik_clean)
				. '</div>'
				. '</div>';

			// Sub-CPMK (excerpt 120 char)
			if (!empty($sub_cpmk) && $sub_cpmk != '-') {
				$sub_excerpt = mb_strlen($sub_cpmk) > 120 ? mb_substr($sub_cpmk, 0, 117) . '...' : $sub_cpmk;
				$col_cockpit .= '<div class="mb-1">'
					. '<span style="font-size: 11px; font-weight: 700; color: #dc2626;"><i class="fa fa-bullseye mr-1"></i> Sub-CPMK:</span>'
					. '<div style="font-size: 11.5px; color: #334155; margin-top: 2px;">' . htmlspecialchars($sub_excerpt) . '</div>'
					. '</div>';
			}

			// Metode (chips)
			if (!empty($metode)) {
				$col_cockpit .= '<div class="mt-1">'
					. '<span style="font-size: 11px; font-weight: 700; color: #0284c7;"><i class="fa fa-cogs mr-1"></i> Metode:</span>'
					. '<span class="badge badge-light border text-info ml-1" style="font-size: 11px; font-weight: 600;">' . htmlspecialchars($metode) . '</span>'
					. '</div>';
			}

			// Badge status skenario
			$col_cockpit .= '<div class="mt-2 d-flex align-items-center" style="gap: 5px; flex-wrap: wrap;">';
			if ($has_skenario) {
				$col_cockpit .= '<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:10.5px; padding:3px 7px;"><i class="fa fa-tasks mr-1"></i> SCL Tersedia</span>';
			} else {
				$col_cockpit .= '<span class="badge" style="background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; font-size:10.5px; padding:3px 7px;"><i class="fa fa-tasks mr-1"></i> SCL Belum Diisi</span>';
			}
			$col_cockpit .= '</div>';

			$col_cockpit .= '</div>';

			// Kolom 3: Aksi Edit
			$col_aksi = '<button type="button" class="btn btn-action-edit btn-sm rounded shadow-sm px-3" '
				. 'onclick="edit_rencana(\'' . $rencana->uniq . '\')" title="Edit & Lihat Skenario SCL, Evaluasi & Sub-CPMK">'
				. '<i class="fa fa-pencil mr-1"></i> Edit / Lihat'
				. '</button>';

			$row = [];
			$row[] = $col_jadwal;
			$row[] = $col_cockpit;
			$row[] = $status_realisasi . $tgl_realisasi;
			$row[] = $col_aksi;

			$data[] = $row;
		}

		$output = [
			"draw"            => $_POST['draw'] ?? 1,
			"recordsTotal"    => $this->Rencana_pertemuan_model->count_all($id_kls),
			"recordsFiltered" => $this->Rencana_pertemuan_model->count_filtered($id_kls),
			"data"            => $data,
		];

		echo json_encode($output);
	}

	/**
	 * AJAX: Ambil data lengkap satu pertemuan untuk ditampilkan pada Modal Cockpit Skenario SCL
	 */
	public function ajax_view_cockpit($id)
	{
		$data = $this->Rencana_pertemuan_model->get_by_id($id);
		if (!$data) {
			echo json_encode(['status' => false, 'message' => 'Data pertemuan tidak ditemukan']);
			return;
		}

		$id_jadwal = $data->id_jadwal;
		$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();
		$nm_mk = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : '';
		$kode_mk = '';
		if ($jadwal_detail && !empty($jadwal_detail->id_kls)) {
			$kelas = $this->db->where('id_kls', $jadwal_detail->id_kls)->get('feeder_kelas_kuliah')->row();
			if ($kelas) {
				$kode_mk = $kelas->kode_mk_1 ?? '';
				if (!$nm_mk && !empty($kelas->nm_mk_1)) $nm_mk = $kelas->nm_mk_1;
			}
		}

		// Lookup RPS OBE
		$rps_obe = null;
		if (!empty($kode_mk)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}

		$details = $rps_obe ? $this->Rps_obe_model->get_details($rps_obe->id_rps) : [];
		$skenario_md = $rps_obe ? ($rps_obe->skenario_pembelajaran_markdown ?: $rps_obe->raw_ai_output) : '';
		$parsed = $this->_parse_skenario_pertemuan($skenario_md, $details);

		$meet_no = (int)$data->pertemuan;
		$topik = !empty($parsed[$meet_no]['topik_pertemuan']) ? $parsed[$meet_no]['topik_pertemuan'] : (!empty($data->bacaan_materi) ? $data->bacaan_materi : $data->berita_acara_rencana);
		$topik = strip_tags(str_replace(['<br />', '<br>', "\n"], ' ', $topik));

		$sub_cpmk = !empty($data->sub_cpmk) ? $data->sub_cpmk : ($parsed[$meet_no]['sub_cpmk'] ?? '-');
		$metode = !empty($data->metode_pembelajaran) ? $data->metode_pembelajaran : ($parsed[$meet_no]['metode'] ?? 'Kuliah Interaktif & Diskusi Kelompok');
		
		$skenario_raw = !empty($parsed[$meet_no]['skenario_scl']) ? $parsed[$meet_no]['skenario_scl'] : (!empty($data->berita_acara_rencana) ? $data->berita_acara_rencana : '');
		$draf_bap = !empty($data->draf_bap) ? $data->draf_bap : ($parsed[$meet_no]['draf_bap'] ?? '');

		$raw_t = (float)($data->jam_teori ?: 1);
		$raw_p = (float)($data->jam_praktek ?: 2);
		$menit_t = $raw_t <= 10 ? round($raw_t * 50) : ($raw_t > 300 ? 50 : round($raw_t));
		$menit_p = $raw_p <= 10 ? round($raw_p * 50) : ($raw_p > 600 ? 100 : round($raw_p));

		$res = [
			'status'            => true,
			'uniq'              => $data->uniq,
			'pertemuan'         => $meet_no,
			'topik'             => $topik,
			'sub_cpmk'          => $sub_cpmk,
			'metode'            => $metode,
			'jam_teori_menit'   => $menit_t,
			'jam_praktek_menit' => $menit_p,
			'tanggal_rencana'   => tgl_indo($data->tanggal_rencana),
			'jam_mulai'         => substr($data->jam_mulai, 0, 5),
			'jam_selesai'       => substr($data->jam_selesai, 0, 5),
			'is_realisasi'      => (int)$data->is_realisasi,
			'tanggal_realisasi' => $data->is_realisasi ? tgl_indo(date('Y-m-d', strtotime($data->tanggal_realisasi))) : '',
			'skenario_raw'      => $skenario_raw,
			'skenario_html'     => $this->_render_scl_cockpit_html($skenario_raw),
			'draf_bap'          => $draf_bap
		];

		echo json_encode($res);
	}

	/**
	 * Helper: Render skenario SCL ke format HTML Cockpit (Tahap 1, 2, 3 + bullet points)
	 */
	private function _render_scl_cockpit_html($skenario_raw)
	{
		if (empty($skenario_raw)) {
			return '<div class="text-muted font-italic small p-2 bg-light rounded"><i class="fa fa-info-circle mr-1 text-info"></i> Skenario langkah pembelajaran SCL belum dirinci.</div>';
		}

		$clean = trim($skenario_raw);
		$clean = preg_replace('/<br\s*[\/]?>/i', "\n", $clean);
		$lines = preg_split('/\r\n|\r|\n/', $clean);
		$html = '<div class="scl-cockpit-content" style="font-size: 13px; line-height: 1.65; color: #1e293b;">';
		
		$in_list = false;
		foreach ($lines as $line) {
			$tline = trim($line);
			if (empty($tline)) {
				if ($in_list) {
					$html .= '</ul>';
					$in_list = false;
				}
				continue;
			}

			// Check if stage title (e.g. 1. **Tahap Pendahuluan (15 Menit):** or 1. Tahap Pendahuluan (15 Menit): )
			if (preg_match('/^(\d+\.\s*)?\*\*(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)\*\*(?:\s*:\s*)?(.*)$/i', $tline, $sm) ||
				preg_match('/^(\d+\.\s*)(?:\*\*)?(Tahap\s+[^:\*]+|Pendahuluan|Kegiatan Inti|Eksplorasi Konsep|Studi Kasus|Diskusi Kelompok|Refleksi & Penutup|Penutup|Sintaks\s*\d+[^:\*]*)(?:\*\*)?(?:\s*:\s*)?(.*)$/i', $tline, $sm)) {
				if ($in_list) {
					$html .= '</ul>';
					$in_list = false;
				}
				$stage_num = !empty($sm[1]) ? trim($sm[1]) . ' ' : '';
				$stage_title = trim($sm[2]);
				$stage_extra = trim($sm[3] ?? '');
				
				$html .= '<div style="margin-top: 14px; margin-bottom: 6px; font-weight: 700; color: #0f172a; font-size: 13.5px;">'
					   . '<span style="color: #0f172a; font-weight: 700;">' . htmlspecialchars($stage_num . $stage_title) . '</span>'
					   . ($stage_extra ? ' <span style="font-weight: 600; color: #334155;">' . htmlspecialchars($stage_extra) . '</span>' : '')
					   . '</div>';
				continue;
			}

			// Check if bullet point (- or * or •)
			if (preg_match('/^(?:[\-\*•]|\d+[\.\)])\s*(.*)$/u', $tline, $bm)) {
				if (!$in_list) {
					$html .= '<ul style="margin: 0 0 10px 0; padding-left: 22px; list-style-type: disc;">';
					$in_list = true;
				}
				$bullet_content = trim($bm[1]);
				$bullet_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($bullet_content));
				$html .= '<li style="margin-bottom: 6px; color: #334155; line-height: 1.6;">' . $bullet_content . '</li>';
				continue;
			}

			if ($in_list) {
				$html .= '</ul>';
				$in_list = false;
			}

			$line_content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', htmlspecialchars($tline));
			$html .= '<p style="margin-bottom: 8px; color: #334155;">' . $line_content . '</p>';
		}

		if ($in_list) {
			$html .= '</ul>';
		}

		$html .= '</div>';
		return $html;
	}

	/**
	 * Helper: Normalize a DB field value that may have been serialized/array-ified by xss_clean, back to a clean string.
	 */
	private function _normalize_text_field($val)
	{
		if (is_array($val)) {
			// Flatten: join all leaf string values
			return implode('\n', array_filter(array_map(fn($v) => is_string($v) ? trim($v) : '', array_values($val))));
		}
		if (is_string($val) && strlen($val) > 2 && ($val[0] === 'a' || $val[0] === 's') && str_contains($val, ':')) {
			$unserialized = @unserialize($val);
			if ($unserialized !== false) {
				if (is_array($unserialized)) {
					// Flatten array to readable text
					$parts = [];
					array_walk_recursive($unserialized, function($item, $key) use (&$parts) {
						if (is_string($item) && trim($item) !== '') {
							$parts[] = $item;
						} elseif (is_string($key) && trim($key) !== '' && !is_numeric($key)) {
							$parts[] = $key . ': ' . (is_string($item) ? $item : '');
						}
					});
					return implode('\n', $parts);
				}
				return is_string($unserialized) ? $unserialized : $val;
			}
		}
		return (string)$val;
	}

	public function ajax_edit($id)
	{
		$data = $this->Rencana_pertemuan_model->get_by_id($id);
		if (!$data) {
			echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
			return;
		}
		$data->tanggal_rencana = ($data->tanggal_rencana == '0000-00-00') ? '' : $data->tanggal_rencana;
		$data->jam_mulai       = substr($data->jam_mulai, 0, 5);
		$data->jam_selesai     = substr($data->jam_selesai, 0, 5);

		// Normalize: bisa jadi tersimpan sebagai serialized array oleh xss_clean
		$data->berita_acara_rencana = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->berita_acara_rencana));
		$data->bacaan_materi        = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->bacaan_materi));
		$data->draf_bap             = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->draf_bap));
		$data->sub_cpmk             = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->sub_cpmk));
		$data->metode_pembelajaran  = $this->_normalize_text_field($data->metode_pembelajaran);
		$data->indikator_evaluasi   = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->indikator_evaluasi));
		$data->catatan_refleksi     = str_ireplace(["<br />", "<br>"], "\r\n", $this->_normalize_text_field($data->catatan_refleksi));

		// Get Kelas and Mata Kuliah info safely
		$jadwal_detail = $this->db->where('id_jadwal', $data->id_jadwal)->get('siakad_jadwal_detail')->row();
		$id_mk = null;
		$kode_mk = null;
		$nm_mk = null;

		if ($jadwal_detail) {
			$data->nm_kls = $jadwal_detail->nm_kls ?? '';
			$data->nm_mk  = $jadwal_detail->nm_mk ?? '';
			$nm_mk        = $data->nm_mk;

			if (!empty($jadwal_detail->id_kls)) {
				$kelas = $this->db->where('id_kls', $jadwal_detail->id_kls)->get('feeder_kelas_kuliah')->row();
				if ($kelas) {
					$id_mk   = $kelas->id_mk ?? null;
					$kode_mk = $kelas->kode_mk_1 ?? null;
					if (!$nm_mk && isset($kelas->nm_mk_1)) {
						$nm_mk = $kelas->nm_mk_1;
					}
				}
			}
		} else {
			$jadwal = $this->db->where('id', $data->id_jadwal)->get('siakad_jadwal')->row();
			$data->nm_kls = '';
			$data->nm_mk  = ($jadwal && isset($jadwal->tag_name)) ? $jadwal->tag_name : '';
			$nm_mk        = $data->nm_mk;
		}

		// Fetch Sub-CPMK options from RPS OBE
		$rps_obe = null;
		if (!empty($id_mk)) {
			$rps_obe = $this->db->where(['id_mk' => $id_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($kode_mk)) {
			$rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}
		if (!$rps_obe && !empty($nm_mk)) {
			$rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
		}

		$sub_cpmk_list = [];
		if ($rps_obe && !empty($rps_obe->tujuan_belajar)) {
			if (preg_match_all('/\|\s*([^\|]+?)\s*\|\s*(Sub-CPMK\s*[\d\.]+)\s*\|\s*([^\|]+?)\s*\|/i', $rps_obe->tujuan_belajar, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $m) {
					$cpmk_label = trim($m[1]);
					$sub_label  = trim($m[2]);
					$desc       = trim($m[3]);
					if (stripos($cpmk_label, 'CPMK') !== false && stripos($sub_label, 'Sub-CPMK') !== false && stripos($desc, 'Bahan Kajian') === false) {
						$sub_cpmk_list[] = [
							'code' => $sub_label,
							'cpmk' => $cpmk_label,
							'desc' => $desc,
							'full' => $sub_label . ': ' . $desc
						];
					}
				}
			}

			if (empty($sub_cpmk_list)) {
				if (preg_match_all('/(Sub-CPMK\s*[\d\.]+)\s*[:\-\)]\s*([^\r\n]+)/i', $rps_obe->tujuan_belajar, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $m) {
						$sub_cpmk_list[] = [
							'code' => trim($m[1]),
							'cpmk' => '',
							'desc' => trim($m[2]),
							'full' => trim($m[1]) . ': ' . trim($m[2])
						];
					}
				}
			}

			if (empty($sub_cpmk_list)) {
				if (preg_match_all('/(CPMK-\d+)\s*[:\-\)]\s*([^\r\n]+)/i', $rps_obe->tujuan_belajar, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $m) {
						$sub_cpmk_list[] = [
							'code' => trim($m[1]),
							'cpmk' => trim($m[1]),
							'desc' => trim($m[2]),
							'full' => trim($m[1]) . ': ' . trim($m[2])
						];
					}
				}
			}
		}

		// Auto-populate from RPS OBE 16 Pertemuan Matrix if fields are currently empty
		if ($rps_obe && !empty($rps_obe->matrix_pertemuan_json)) {
			$matrix = json_decode($rps_obe->matrix_pertemuan_json, true);
			if (is_array($matrix)) {
				$meetNum = (int)$data->pertemuan;
				$matchedRow = null;
				foreach ($matrix as $row) {
					$rowNum = isset($row['pertemuan']) ? (int)$row['pertemuan'] : (isset($row['minggu']) ? (int)$row['minggu'] : 0);
					if ($rowNum === $meetNum) {
						$matchedRow = $row;
						break;
					}
				}

				if ($matchedRow) {
					if (empty($data->sub_cpmk) && !empty($matchedRow['sub_cpmk'])) {
						$data->sub_cpmk = $matchedRow['sub_cpmk'];
					}
					if (empty($data->bacaan_materi)) {
						$bk = !empty($matchedRow['bahan_kajian']) ? $matchedRow['bahan_kajian'] : '';
						$ref = !empty($matchedRow['sumber_belajar']) ? ("\nReferensi: " . $matchedRow['sumber_belajar']) : '';
						$data->bacaan_materi = trim($bk . $ref);
					}
					if (empty($data->metode_pembelajaran)) {
						$metode = !empty($matchedRow['metode']) ? $matchedRow['metode'] : '';
						if (empty($metode) && !empty($matchedRow['bentuk_pembelajaran'])) {
							$metode = $matchedRow['bentuk_pembelajaran'];
						}
						$data->metode_pembelajaran = $metode;
					}
					if (empty($data->indikator_evaluasi)) {
						$evalParts = [];
						if (!empty($matchedRow['indikator_evaluasi'])) $evalParts[] = $matchedRow['indikator_evaluasi'];
						if (!empty($matchedRow['kriteria_evaluasi'])) $evalParts[] = '(' . $matchedRow['kriteria_evaluasi'] . ')';
						if (!empty($matchedRow['bobot'])) $evalParts[] = 'Bobot: ' . $matchedRow['bobot'] . '%';
						$data->indikator_evaluasi = implode(' ', $evalParts);
					}
				}
			}
		}

		// Ambil data lengkap dari RPS OBE via _parse_skenario_pertemuan & details (PRIORITAS OTOMATIS)
		$skenario_scl_for_display = $data->berita_acara_rencana; // fallback: isi DB
		if ($rps_obe) {
			$details     = $this->Rps_obe_model->get_details($rps_obe->id_rps);
			$skenario_md = $rps_obe->skenario_pembelajaran_markdown ?: ($rps_obe->raw_ai_output ?? '');
			$parsed      = $this->_parse_skenario_pertemuan($skenario_md, $details);
			$meetNum     = (int)$data->pertemuan;

			// Auto-fill dari RPS OBE jika field di DB masih kosong / default
			if (isset($parsed[$meetNum])) {
				$pRow = $parsed[$meetNum];
				if (empty($data->sub_cpmk) && !empty($pRow['sub_cpmk']) && $pRow['sub_cpmk'] !== '-') {
					$data->sub_cpmk = $pRow['sub_cpmk'];
				}
				if (empty($data->metode_pembelajaran) && !empty($pRow['metode'])) {
					$data->metode_pembelajaran = $pRow['metode'];
				}
				if (empty($data->bacaan_materi) && !empty($pRow['bahan_kajian']) && $pRow['bahan_kajian'] !== '-') {
					$data->bacaan_materi = $pRow['bahan_kajian'];
				}
				if (empty($data->indikator_evaluasi) && !empty($pRow['evaluasi'])) {
					$data->indikator_evaluasi = $pRow['evaluasi'];
				}
				if (empty($data->draf_bap) && !empty($pRow['draf_bap'])) {
					$data->draf_bap = $pRow['draf_bap'];
				}
				if (!empty($pRow['skenario_scl'])) {
					$skenario_scl_for_display = $pRow['skenario_scl'];
				}
			}

			// Jika list butir Sub-CPMK belum terisi dari regex tujuan_belajar, buat dari rincian RPS per pertemuan
			if (empty($sub_cpmk_list) && !empty($parsed)) {
				$seen_sub = [];
				foreach ($parsed as $pItem) {
					$sc = trim($pItem['sub_cpmk'] ?? '');
					if ($sc && $sc !== '-' && !isset($seen_sub[$sc])) {
						$seen_sub[$sc] = true;
						$code_label = 'Meet ' . ($pItem['pertemuan'] ?? '');
						if (preg_match('/(Sub-CPMK\s*[\d\.]+)/i', $sc, $m_sc)) {
							$code_label = $m_sc[1];
						}
						$sub_cpmk_list[] = [
							'code' => $code_label,
							'cpmk' => '',
							'desc' => $sc,
							'full' => $sc
						];
					}
				}
			}
		}

		// skenario_scl: untuk visual box modal (bersih, dari RPS OBE)
		// berita_acara_rencana: raw DB, untuk textarea edit saja
		$data->skenario_scl = $skenario_scl_for_display;
		$data->sub_cpmk_options = $sub_cpmk_list;

		echo json_encode($data);
	}

	public function ajax_update()
	{
		$this->_validate('update');
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));

		$tanggal_rencana      = $tanggal;
		$pertemuan            = $this->input->post('pertemuan');
		$jam_mulai            = $this->input->post('jam_mulai');
		$jam_selesai          = $this->input->post('jam_selesai');
		$raw_jt               = (float)$this->input->post('jam_teori');
		$raw_jp               = (float)$this->input->post('jam_praktek');
		$jam_teori            = $raw_jt >= 10 ? round($raw_jt / 50, 2) : ($raw_jt > 0 ? $raw_jt : 1.00);
		$jam_praktek          = $raw_jp >= 10 ? round($raw_jp / 50, 2) : ($raw_jp > 0 ? $raw_jp : 2.00);
		$sub_cpmk             = trim($this->input->post('sub_cpmk'));
		$metode_pembelajaran  = trim($this->input->post('metode_pembelajaran'));
		$indikator_evaluasi   = trim($this->input->post('indikator_evaluasi'));
		$berita_acara_rencana = str_ireplace(["\r\n", "\n", "\r"], '<br />', (string)$this->input->post('berita_acara_rencana'));
		$bacaan_materi        = str_ireplace(["\r\n", "\n", "\r"], '<br />', (string)$this->input->post('bacaan_materi'));
		$draf_bap             = str_ireplace(["\r\n", "\n", "\r"], '<br />', (string)$this->input->post('draf_bap'));
		$catatan_refleksi     = str_ireplace(["\r\n", "\n", "\r"], '<br />', (string)$this->input->post('catatan_refleksi'));
		$checked              = $this->input->post('is_realisasi');
		$change_by            = $this->ion_auth->user()->row()->id;
		$change_date          = date('Y-m-d H:i:s');

		if (isset($checked)) {
			$is_realisasi = 1;
			$tanggal_realisasi = $this->input->post('tanggal_realisasi');
		} else {
			$is_realisasi = 0;
			$tanggal_realisasi = NULL;
		}

		$data = compact(
			'tanggal_rencana',
			'pertemuan',
			'jam_mulai',
			'jam_selesai',
			'jam_teori',
			'jam_praktek',
			'sub_cpmk',
			'metode_pembelajaran',
			'indikator_evaluasi',
			'is_realisasi',
			'tanggal_realisasi',
			'berita_acara_rencana',
			'bacaan_materi',
			'draf_bap',
			'catatan_refleksi',
			'change_by',
			'change_date'
		);

		$data = $this->security->xss_clean($data);
		$this->Rencana_pertemuan_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$rencana_pertemuan = $this->db->where('uniq', $id)->get('siakad_rencana_pertemuan')->row();

		if ($rencana_pertemuan && $rencana_pertemuan->is_realisasi == 0) {
			$this->Rencana_pertemuan_model->delete_by_id($id);
			echo json_encode(["status" => TRUE]);
		} else {
			echo json_encode(["status" => FALSE, "message" => "Tidak bisa dihapus karena sudah realisasi."]);
		}
	}

	private function _validate($tipe)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror']   = [];
		$data['status']       = TRUE;

		if ($this->input->post('tanggal_rencana') == '') {
			$data['inputerror'][]   = 'tanggal_rencana';
			$data['error_string'][] = 'Tanggal Rencana harus diisi.';
			$data['status']         = FALSE;
		}

		if ($this->input->post('jam_mulai') == '') {
			$data['inputerror'][]   = 'jam_mulai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status']         = FALSE;
		}

		if ($this->input->post('jam_selesai') == '') {
			$data['inputerror'][]   = 'jam_selesai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status']         = FALSE;
		}

		if (!strlen(trim($this->input->post('berita_acara_rencana')))) {
			$data['inputerror'][]   = 'berita_acara_rencana';
			$data['error_string'][] = 'Harus diisi.';
			$data['status']         = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
