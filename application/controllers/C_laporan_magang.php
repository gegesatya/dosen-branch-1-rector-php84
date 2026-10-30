<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_laporan_magang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Laporan_magang_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2, 19];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$id_ptk = $this->session->userdata('id_ptk');
		$default_smt = $this->session->userdata('tahun_ajaran');

		// Ambil semester dari GET parameter jika dipilih, atau dari session magang_selected_smt, default dari session tahun_ajaran
		$get_smt = $this->input->get('id_smt');
		if (!empty($get_smt)) {
			$selected_smt = $get_smt;
			$this->session->set_userdata('magang_selected_smt', $selected_smt);
		} else {
			$saved_smt = $this->session->userdata('magang_selected_smt');
			$selected_smt = !empty($saved_smt) ? $saved_smt : $default_smt;
		}

		// Ambil daftar semester untuk dropdown filter
		$list_semester = $this->Laporan_magang_model->get_list_semester($id_ptk);

		// 1. Get All Students
		$data_mahasiswa = $this->Laporan_magang_model->get_all_mahasiswa_magang($id_ptk, $selected_smt);
		
		$nipds = [];
		foreach ($data_mahasiswa as $m) {
			$nipds[] = $m->nipd;
		}

		// 2. Get Lokasi
		$lokasi_by_nipd = [];
		if (!empty($nipds)) {
			$lokasi_data = $this->db->where_in('nipd', $nipds)->get('simlapkerma_lokasi_magang')->result();
			foreach ($lokasi_data as $lok) {
				$lokasi_by_nipd[$lok->nipd] = $lok;
			}
		}

		// 3. Assemble Data (No Heavy Logs Processing)
		foreach ($data_mahasiswa as &$m) {
			$nipd = $m->nipd;
			$lokasi = $lokasi_by_nipd[$nipd] ?? null;
			
			if ($lokasi) {
				$m->tanggal_mulai        = $lokasi->tanggal_mulai;
				$m->tanggal_selesai      = $lokasi->tanggal_selesai;
				$m->link_drive_proposal  = $lokasi->link_drive_proposal;
				$m->ai_summary_proposal  = $lokasi->ai_summary_proposal ?? null;
			} else {
				$m->tanggal_mulai        = null;
				$m->tanggal_selesai      = null;
				$m->link_drive_proposal  = null;
				$m->ai_summary_proposal  = null;
			}
			$m->jml_total_laporan = 0; // Will be updated via AJAX
			$m->warning_magang = null;
		}
		
		$output['data_mahasiswa'] = $data_mahasiswa;
		$output['list_semester']  = $list_semester;
		$output['selected_smt']   = $selected_smt;
		$output['default_smt']    = $default_smt;

		$output['judul_besar'] = 'Laporan Ringkasan Magang/Internship';
		$output['judul_kecil'] = ' Log Harian/Mingguan';
		$output['menu_aktif'] = 'm_laporan_magang';
		$output['parent_magang'] = 'active open';
		$output['content'] = 'laporan_magang/laporan_magang_view';
		$output['after_page'] = 'laporan_magang/laporan_magang_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_load_profil_magang()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->input->get_post('id_smt') ?: $this->session->userdata('magang_selected_smt') ?: $this->session->userdata('tahun_ajaran');

		// 1. Get All Students
		$data_mahasiswa = $this->Laporan_magang_model->get_all_mahasiswa_magang($id_ptk, $id_smt);
		
		$nipds = [];
		foreach ($data_mahasiswa as $m) {
			$nipds[] = $m->nipd;
		}

		// 2. Get Lokasi
		$lokasi_by_nipd = [];
		if (!empty($nipds)) {
			$lokasi_data = $this->db->where_in('nipd', $nipds)->get('simlapkerma_lokasi_magang')->result();
			foreach ($lokasi_data as $lok) {
				$lokasi_by_nipd[$lok->nipd] = $lok;
			}
		}

		// 3. Assemble Data (No Heavy Logs Processing)
		foreach ($data_mahasiswa as &$m) {
			$nipd = $m->nipd;
			$lokasi = $lokasi_by_nipd[$nipd] ?? null;
			
			if ($lokasi) {
				$m->tanggal_mulai        = $lokasi->tanggal_mulai;
				$m->tanggal_selesai      = $lokasi->tanggal_selesai;
				$m->link_drive_proposal  = $lokasi->link_drive_proposal;
				$m->ai_summary_proposal  = $lokasi->ai_summary_proposal ?? null;
			} else {
				$m->tanggal_mulai        = null;
				$m->tanggal_selesai      = null;
				$m->link_drive_proposal  = null;
				$m->ai_summary_proposal  = null;
			}
			$m->jml_total_laporan = 0; // Will be updated in next phase
			$m->warning_magang = null;
		}

		echo json_encode(['status' => true, 'data' => $data_mahasiswa]);
	}

	public function ajax_load_warning_magang()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->input->get_post('id_smt') ?: $this->session->userdata('magang_selected_smt') ?: $this->session->userdata('tahun_ajaran');

		// 1. Get Students (to know which NIPDs we handle)
		$data_mahasiswa = $this->Laporan_magang_model->get_all_mahasiswa_magang($id_ptk, $id_smt);
		$nipds = [];
		foreach ($data_mahasiswa as $m) {
			$nipds[] = $m->nipd;
		}

		// 2. Get Logs
		$logs_by_nipd = [];
		if (!empty($nipds)) {
			$logs_data = $this->db->select('nipd, week, kegiatan_hari_1, kegiatan_hari_2, kegiatan_hari_3, kegiatan_hari_4, kegiatan_hari_5, kegiatan_hari_6, create_date, change_date, status_laporan, review_dpl')
								  ->from('simlapkerma_tracking_kegiatan')
								  ->where_in('nipd', $nipds)
								  ->order_by('week', 'asc')
								  ->get()->result();
			foreach ($logs_data as $log) {
				$logs_by_nipd[$log->nipd][] = $log;
			}
		}

		// 3. Heavy Process Loop
		$warnings = [];
		foreach ($nipds as $nipd) {
			$logs = $logs_by_nipd[$nipd] ?? [];
			
			$missing_weeks = 0;
			$incomplete_weeks = [];
			$last_filled_date = null;
			$latest_timestamp = 0;
			$jml_total_laporan = count($logs);
			$jml_butuh_review = 0;
			
			if (!empty($logs)) {
				// find the latest update
				$max_week = 0;
				foreach ($logs as $log) {
					if (empty($log->review_dpl)) {
						$jml_butuh_review++;
					}
					
					$ts = strtotime(!empty($log->change_date) ? $log->change_date : $log->create_date);
					if ($ts > $latest_timestamp) {
						$latest_timestamp = $ts;
						$last_filled_date = date('Y-m-d H:i:s', $ts);
					}
					if ((int)$log->week > $max_week) {
						$max_week = (int)$log->week;
					}
				}

				// Calculate missing weeks
				$current_timestamp = time();
				if ($latest_timestamp > 0 && $current_timestamp > $latest_timestamp) {
					$seconds_passed = $current_timestamp - $latest_timestamp;
					if ($seconds_passed > (7 * 24 * 3600)) {
						$missing_weeks = floor($seconds_passed / (7 * 24 * 3600));
					}
				}
				
				foreach ($logs as $log) {
					$empty_days = [];
					for ($i = 1; $i <= 6; $i++) {
						$field = 'kegiatan_hari_' . $i;
						$raw_val = $log->$field ?? '';
						$content = '';
						$is_json = false;
						if (is_string($raw_val) && !empty($raw_val) && ($raw_val[0] === '{' || $raw_val[0] === '[')) {
							$decoded = json_decode($raw_val, true);
							if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
								$is_json = true;
								$kegiatan_pagi = $decoded['pagi']['teks'] ?? '';
								$kegiatan_siang = $decoded['siang']['teks'] ?? '';
								$kegiatan_sore = $decoded['sore']['teks'] ?? '';
								$combined = $kegiatan_pagi . ' ' . $kegiatan_siang . ' ' . $kegiatan_sore;
								$content = trim(strip_tags(str_replace(['&nbsp;', '<p>', '</p>', '<br>', '<br/>', "\r", "\n", "\t"], '', $combined)));
							}
						}
						
						if (!$is_json) {
							$content = trim(strip_tags(str_replace(['&nbsp;', '<p>', '</p>', '<br>', '<br/>', "\r", "\n", "\t"], '', $raw_val)));
						}
						
						if (empty($content)) {
							$empty_days[] = $i;
						}
					}
					if (!empty($empty_days)) {
						$incomplete_weeks[] = ['week' => $log->week, 'empty_days' => $empty_days];
					}
				}
			}
			
			$warning_magang = null;
			if ($missing_weeks > 0 || !empty($incomplete_weeks)) {
				$warning_magang = [
					'last_filled' => $last_filled_date,
					'missing_weeks' => $missing_weeks,
					'incomplete_weeks' => $incomplete_weeks
				];
			}

			$warnings[$nipd] = [
				'jml_total_laporan' => $jml_total_laporan,
				'jml_butuh_review'  => $jml_butuh_review,
				'warning_magang'    => $warning_magang
			];
		}

		echo json_encode(['status' => true, 'warnings' => $warnings]);
	}

	public function ajax_pedoman()
	{
		// Load pedoman view as standalone partial (no layout)
		$this->load->view('laporan_magang/pedoman_review_magang');
	}



	public function ajax_get_nilai($nipd)
	{
		$student = $this->db->select('m.nipd, md.nm_pd, m.nilai_rata_fasilitator')
			->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('mahasiswa md', 'md.id_pd = mp.id_pd')
			->where('m.nipd', $nipd)
			->get()
			->row();

		$id_smt = $this->session->userdata('tahun_ajaran');

		$courses = $this->db->select('nb.id_kls, mk.nm_mk, nb.nilai_angka, nb.nilai_huruf')
			->from('siakad_nilai_baru nb')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = kk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->where('nb.nipd', $nipd)
			->where('kk.id_smt', $id_smt)
			->where('j.id_kategori_jadwal', 2)
			->where("(LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk.nm_mk) NOT LIKE '%kkn%')", NULL, FALSE)
			->where('nb.nilai_angka IS NULL', NULL, FALSE)
			->where('nb.nilai_huruf IS NULL', NULL, FALSE)
			->get()
			->result();

		echo json_encode([
			'student' => $student,
			'courses' => $courses
		]);
	}

	public function ajax_save_nilai()
	{
		$nipd = $this->input->post('nipd');
		$nilai_rata_fasilitator = $this->input->post('nilai_rata_fasilitator');

		if ($nilai_rata_fasilitator === '') {
			$nilai_rata_fasilitator = NULL;
		}

		$this->db->where('nipd', $nipd)
			->update('simlapkerma_lokasi_magang', ['nilai_rata_fasilitator' => $nilai_rata_fasilitator]);

		$course_grades = $this->input->post('course_grades');
		if (!empty($course_grades)) {
			foreach ($course_grades as $id_kls => $grade) {
				$nilai_angka = ($grade['nilai_angka'] === '') ? NULL : $grade['nilai_angka'];
				$nilai_huruf = ($grade['nilai_huruf'] === '') ? NULL : $grade['nilai_huruf'];

				$nilai_indeks = NULL;
				if ($nilai_huruf !== NULL) {
					switch ($nilai_huruf) {
						case 'A': $nilai_indeks = 4.00; break;
						case 'B+': $nilai_indeks = 3.50; break;
						case 'B': $nilai_indeks = 3.00; break;
						case 'C+': $nilai_indeks = 2.50; break;
						case 'C': $nilai_indeks = 2.00; break;
						case 'D': $nilai_indeks = 1.00; break;
						case 'E': $nilai_indeks = 0.00; break;
					}
				}

				$this->db->where(['nipd' => $nipd, 'id_kls' => $id_kls])
					->update('siakad_nilai_baru', [
						'nilai_angka' => $nilai_angka,
						'nilai_huruf' => $nilai_huruf,
						'nilai_indeks' => $nilai_indeks
					]);
			}
		}

		echo json_encode(['status' => TRUE]);
	}

	public function ajax_generate_ai_summary_proposal()
	{
		$nipd = $this->input->post('nipd');
		
		$lokasi = $this->db->select('m.*, md.nm_pd as nama_mahasiswa')
			->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('mahasiswa md', 'md.id_pd = mp.id_pd')
			->where('m.nipd', $nipd)
			->get()
			->row();
		
		if (!$lokasi) {
			echo json_encode(['status' => false, 'msg' => 'Data magang tidak ditemukan.']);
			return;
		}
		
		if (empty($lokasi->link_drive_proposal)) {
			echo json_encode(['status' => false, 'msg' => 'Mahasiswa belum melampirkan link proposal.']);
			return;
		}
		
		if (!empty($lokasi->ai_summary_proposal)) {
			echo json_encode(['status' => true, 'msg' => 'Ringkasan sudah ada.', 'summary' => $lokasi->ai_summary_proposal]);
			return;
		}

		$file_url = $lokasi->link_drive_proposal;
		$temp_file = sys_get_temp_dir() . '/' . uniqid('magang_') . '.tmp';
		
		$is_gdoc = false;
		if (str_contains($file_url, 'docs.google.com/document/d/')) {
			$is_gdoc = true;
		}

		if (str_contains($file_url, 'drive.google.com') || str_contains($file_url, 'docs.google.com')) {
			preg_match('/[-\w]{25,}/', $file_url, $matches);
			if (!empty($matches[0])) {
				$file_id = $matches[0];
				if ($is_gdoc) {
					$file_url = 'https://docs.google.com/document/d/' . $file_id . '/export?format=docx';
				} else {
					$file_url = 'https://drive.google.com/uc?export=download&id=' . $file_id;
				}
			}
		}
		
		$is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || str_contains($_SERVER['HTTP_HOST'], '.test') || str_contains($_SERVER['HTTP_HOST'], '.local'));

		$ch_dl = curl_init();
		curl_setopt($ch_dl, CURLOPT_URL, $file_url);
		curl_setopt($ch_dl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_dl, CURLOPT_FOLLOWLOCATION, 1);
		if ($is_local) {
			curl_setopt($ch_dl, CURLOPT_SSL_VERIFYPEER, false);
		}
		curl_setopt($ch_dl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
		$file_content = curl_exec($ch_dl);
		$dl_http_code = curl_getinfo($ch_dl, CURLINFO_HTTP_CODE);
		curl_close($ch_dl);

		if (!$file_content || $dl_http_code != 200) {
			echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh file dokumen (HTTP ' . $dl_http_code . '). URL: ' . $file_url . ' - Pastikan link tidak salah dan setelan share adalah "Anyone with the link".']);
			return;
		}
		
		file_put_contents($temp_file, $file_content);
		
		if (str_contains(trim(strtolower(substr($file_content, 0, 50))), '<!doctype html') || str_contains(trim(strtolower(substr($file_content, 0, 50))), '<html')) {
			@unlink($temp_file);
			echo json_encode(['status' => false, 'msg' => 'Gagal membaca dokumen. Tautan Google Drive ini dilindungi, membutuhkan konfirmasi, atau mengarah ke halaman web (HTML). Pastikan file tersebut adalah dokumen asli dan sudah di-set "Anyone with the link".']);
			return;
		}

		$extracted_text = '';
		try {
			$parser = new \Smalot\PdfParser\Parser();
			try {
				$pdf = $parser->parseFile($temp_file);
				$extracted_text = $pdf->getText();
			} catch (\Exception $e) {
				try {
					$zip = new ZipArchive;
					if ($zip->open($temp_file) === true) {
						if (($index = $zip->locateName('word/document.xml')) !== false) {
							$data = $zip->getFromIndex($index);
							$zip->close();
							
							$data = str_replace(['</w:p>', '<w:br>', '<w:br/>', '<w:br />'], "\n", $data);
							$data = str_replace(['<w:tab>', '<w:tab/>', '<w:tab />'], "\t", $data);
							$extracted_text = strip_tags($data);
						} else {
							$zip->close();
							throw new \Exception("File bukan dokumen Word (.docx) yang valid.");
						}
					} else {
						throw new \Exception("Gagal membuka file ZIP/DOCX.");
					}
				} catch (\Exception $e2) {
					@unlink($temp_file);
					echo json_encode(['status' => false, 'msg' => 'Gagal membaca isi dokumen: ' . $e2->getMessage()]);
					return;
				}
			}
		} catch (\Exception $e) {
			@unlink($temp_file);
			echo json_encode(['status' => false, 'msg' => 'Gagal memproses file: ' . $e->getMessage()]);
			return;
		}
		
		@unlink($temp_file);
		
		if (empty(trim($extracted_text))) {
			echo json_encode(['status' => false, 'msg' => 'Teks di dalam dokumen kosong atau tidak bisa dibaca (berupa gambar hasil scan).']);
			return;
		}

		$extracted_text = substr($extracted_text, 0, 15000);

		$id_ptk = $this->session->userdata('id_ptk');
		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		if (empty($api_setting) || empty($api_setting->api_key)) {
			echo json_encode(['status' => false, 'msg' => 'API Key belum disetting. Silakan setting API Key di menu Setting API Key terlebih dahulu.']);
			return;
		}

		$api_key = $api_setting->api_key;
		$ai_provider = isset($api_setting->ai_provider) && !empty($api_setting->ai_provider) ? $api_setting->ai_provider : 'gemini';
		
		$nama_mahasiswa_pengusul = $lokasi->nama_mahasiswa ?? 'Tidak diketahui';
		
		// Cek apakah dokumen berformat Proposal lama (Bab 1, Bab 2, dll)
		$is_format_lama = false;
		if (preg_match('/bab\s*(1|i)\b/i', $extracted_text) && preg_match('/bab\s*(2|ii)\b/i', $extracted_text)) {
			$is_format_lama = true;
		}

		if ($is_format_lama) {
			$prompt = "# Konteks & Peran\nKamu adalah seorang asisten akademik AI berpengalaman. Tugasmu adalah melakukan reviu dan merangkum dokumen Proposal Magang/Internship mahasiswa secara mendalam dan komprehensif.\n\n# Instruksi\n1. Bacalah isi teks dokumen yang diberikan dengan cermat.\n2. Ekstrak informasi dan susun ke dalam format JSON yang valid dan baku (RFC 8259). TIDAK BOLEH ADA TEKS LAIN SELAIN JSON.\n3. Jangan gunakan markdown block (jangan gunakan ```json).\n4. Format JSON harus memiliki key/struktur berikut:\n{\n  \"kesesuaian_nama\": \"Periksa apakah nama mahasiswa pengusul magang yaitu '" . $nama_mahasiswa_pengusul . "' tercantum di dalam dokumen proposal. Jelaskan jika sesuai, atau beritahu jika nama yang ada di dokumen berbeda dengan nama pengusul.\",\n  \"profil_dan_latar_belakang\": \"Alasan/tujuan utama mahasiswa memilih tempat magang tersebut.\",\n  \"lokasi_dan_mitra\": \"Nama lengkap perusahaan/instansi, divisi/departemen tempat magang, dan lokasi.\",\n  \"ruang_lingkup\": \"Penjabaran spesifik mengenai tugas, proyek, atau teknologi yang akan dipelajari/dikerjakan selama magang.\",\n  \"waktu_pelaksanaan\": \"Durasi magang, tanggal mulai, dan tanggal selesai.\",\n  \"target_capaian\": \"Apa hasil atau output yang diharapkan dari kegiatan magang ini?\"\n}\n5. JANGAN menyalin (copy-paste) mentah-mentah teks dari dokumen. Susun narasi atau pokok pikiran utama dengan bahasamu sendiri yang profesional dan padat. Bila perlu, gunakan format list HTML (<ul><li>...</li></ul>) untuk menjabarkan poin-poin agar lebih rapi. Jika suatu informasi tidak dibahas, isi dengan 'Tidak ada informasi'.";
		} else {
			$prompt = "# Konteks & Peran\nKamu adalah seorang asisten akademik AI berpengalaman. Tugasmu adalah melakukan reviu dan mengekstrak informasi pokok dari dokumen Formulir Pendaftaran / Usulan Internship mahasiswa secara mendalam.\n\n# Instruksi\n1. Bacalah isi teks dokumen yang diberikan dengan cermat.\n2. Ekstrak informasi dan susun ke dalam format JSON yang valid dan baku (RFC 8259). TIDAK BOLEH ADA TEKS LAIN SELAIN JSON.\n3. Jangan gunakan markdown block (jangan gunakan ```json).\n4. Format JSON harus memiliki key/struktur berikut:\n{\n  \"kesesuaian_nama\": \"Periksa apakah nama mahasiswa pengusul magang yaitu '" . $nama_mahasiswa_pengusul . "' tercantum di dalam dokumen. Jelaskan jika sesuai, atau beritahu jika berbeda.\",\n  \"masalah_mendorong\": \"Masalah yang mendorong mahasiswa mengajukan internship ini.\",\n  \"sumber_ide\": \"Sumber ide internship ini.\",\n  \"kegunaan_manfaat\": \"Kegunaan dan Manfaat internship ini bagi mahasiswa.\",\n  \"hal_ingin_diketahui\": \"Hal yang ingin diketahui mahasiswa dari internship ini.\",\n  \"mengapa_penting\": \"Mengapa hal di atas penting bagi mahasiswa.\",\n  \"tujuan_internship\": \"Tujuan internship ini bagi mahasiswa.\",\n  \"pengetahuan_diperoleh\": \"Pengetahuan yang ingin diperoleh dari internship ini.\",\n  \"keterampilan_diperoleh\": \"Keterampilan yang ingin diperoleh dari internship ini.\",\n  \"catatan\": \"Catatan tambahan (jika ada).\"\n}\n5. JANGAN menyalin (copy-paste) mentah-mentah teks dari dokumen. Susun narasi atau pokok pikiran utama dengan bahasamu sendiri yang profesional dan padat. Bila perlu, gunakan format list HTML (<ul><li>...</li></ul>) untuk menjabarkan poin-poin agar lebih rapi. Jika suatu informasi tidak dibahas, isi dengan 'Tidak ada informasi'.";
		}

		$this->load->library('Ai_reviewer');
		
		$system_prompt = $prompt;
		$user_document = "Berikut adalah isi dokumen Proposal Magang:\n\n" . $extracted_text;

		$result = $this->ai_reviewer->generate_review($ai_provider, $api_key, $system_prompt, $user_document, $is_local, false);

		if (!$result['status']) {
			echo json_encode(['status' => false, 'msg' => $result['msg']]);
			return;
		}

		$ai_summary = $result['review'];
		$ai_summary = preg_replace('/```json\s*/', '', $ai_summary);
		$ai_summary = preg_replace('/```\s*/', '', $ai_summary);
		$ai_summary = trim($ai_summary);

		if (!empty($ai_summary)) {
			$this->db->where('nipd', $nipd);
			$this->db->update('simlapkerma_lokasi_magang', ['ai_summary_proposal' => $ai_summary]);
			
			echo json_encode(['status' => true, 'msg' => 'Ringkasan berhasil digenerate.', 'summary' => $ai_summary]);
		} else {
			echo json_encode(['status' => false, 'msg' => 'Respon AI kosong atau tidak sesuai format.']);
		}
	}

	public function ajax_reset_ai_summary_proposal()
	{
		$nipd = $this->input->post('nipd');

		if (!$nipd) {
			echo json_encode(['status' => false, 'msg' => 'Parameter tidak valid.']);
			return;
		}

		$this->db->where('nipd', $nipd);
		$this->db->update('simlapkerma_lokasi_magang', ['ai_summary_proposal' => NULL]);

		// Hapus juga history chat
		$this->Laporan_magang_model->delete_chat_history($nipd);

		echo json_encode(['status' => true, 'msg' => 'Hasil ringkasan AI berhasil direset.']);
	}

	public function ajax_ask_ai_proposal()
	{
		$nipd = $this->input->post('nipd');
		$question = $this->input->post('question');
		
		if (empty($nipd) || empty($question)) {
			echo json_encode(['status' => false, 'msg' => 'Parameter tidak lengkap.']);
			return;
		}

		// --- Tambahan: Cek apakah user menggunakan keyword @dokumen ---
		$need_document = false;
		$question_clean = $question;

		if (stripos($question, '@dokumen') !== false) {
			$need_document = true;
			// Hilangkan kata @dokumen dari pertanyaan
			$question_clean = trim(str_ireplace('@dokumen', '', $question));
			// Hilangkan tanda baca yang mungkin tersisa di awal, misal ", coba periksa"
			$question_clean = ltrim($question_clean, " ,.:;-");
		}

		// Kalau TIDAK butuh dokumen, cek apakah ini hanya sapaan pendek/basa-basi
		if (!$need_document && preg_match('/^(halo|hai|hi|hello|pagi|siang|sore|malam|assalamualaikum|tes|test|ping|oke|ok|terima kasih|terimakasih|makasih|tq|thanks|thank you|mantap|keren|kamu keren|bagus|sip|siap|baik|gimana sih|gimana|ya|iya|tidak|nggak|enggak)[\s\W]*$/i', $question_clean)) {
			
			$clean_q = strtolower($question_clean);
			if (preg_match('/(terima kasih|terimakasih|makasih|tq|thanks|thank you)/i', $clean_q)) {
				$answer = "Sama-sama! Senang bisa membantu. Ada lagi yang bisa saya bantu?";
			} else if (preg_match('/(oke|ok|sip|siap|baik|mantap|keren|bagus)/i', $clean_q)) {
				$answer = "Baik, silakan beri tahu saya jika ada pertanyaan lain.";
			} else if (preg_match('/(gimana sih|gimana)/i', $clean_q)) {
				$answer = "Mohon diperjelas pertanyaannya.";
			} else {
				$answer = "Halo! Ada yang bisa saya bantu? Jika ingin mengecek dokumen, gunakan kata kunci @dokumen.";
			}

			$this->load->model('Laporan_magang_model');
			$this->Laporan_magang_model->save_chat_message($nipd, 'user', $question);
			$this->Laporan_magang_model->save_chat_message($nipd, 'ai',   $answer);
			echo json_encode(['status' => true, 'answer' => $answer]);
			return;
		}
		// ---------------------------------------------------------------------

		$lokasi = $this->db->select('m.*, md.nm_pd as nama_mahasiswa, s.nm_lemb as prodi')
			->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('mahasiswa md', 'md.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->where('m.nipd', $nipd)
			->get()
			->row();
		if (!$lokasi || empty($lokasi->link_drive_proposal)) {
			echo json_encode(['status' => false, 'msg' => 'Data magang atau link proposal tidak ditemukan.']);
			return;
		}

		$extracted_text = '';
		if ($need_document) {
			$file_url = $lokasi->link_drive_proposal;
			$temp_file = sys_get_temp_dir() . '/' . uniqid('magang_ask_') . '.tmp';
			
			$is_gdoc = false;
			if (str_contains($file_url, 'docs.google.com/document/d/')) {
				$is_gdoc = true;
			}

			if (str_contains($file_url, 'drive.google.com') || str_contains($file_url, 'docs.google.com')) {
				preg_match('/[-\w]{25,}/', $file_url, $matches);
				if (!empty($matches[0])) {
					$file_id = $matches[0];
					if ($is_gdoc) {
						$file_url = 'https://docs.google.com/document/d/' . $file_id . '/export?format=docx';
					} else {
						$file_url = 'https://drive.google.com/uc?export=download&id=' . $file_id;
					}
				}
			}
			
			$is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || str_contains($_SERVER['HTTP_HOST'], '.test') || str_contains($_SERVER['HTTP_HOST'], '.local'));

			$ch_dl = curl_init();
			curl_setopt($ch_dl, CURLOPT_URL, $file_url);
			curl_setopt($ch_dl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch_dl, CURLOPT_FOLLOWLOCATION, 1);
			if ($is_local) curl_setopt($ch_dl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch_dl, CURLOPT_USERAGENT, 'Mozilla/5.0');
			$file_content = curl_exec($ch_dl);
			curl_close($ch_dl);

			if (!$file_content) {
				echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh dokumen.']);
				return;
			}
			file_put_contents($temp_file, $file_content);

			try {
				$parser = new \Smalot\PdfParser\Parser();
				try {
					$pdf = $parser->parseFile($temp_file);
					$extracted_text = $pdf->getText();
				} catch (\Exception $e) {
					try {
						$zip = new ZipArchive;
						if ($zip->open($temp_file) === true) {
							if (($index = $zip->locateName('word/document.xml')) !== false) {
								$data = $zip->getFromIndex($index);
								$data = str_replace(['</w:p>', '<w:br>', '<w:br/>', '<w:br />'], "\n", $data);
								$extracted_text = strip_tags($data);
							}
							$zip->close();
						}
					} catch (\Exception $e2) { }
				}
			} catch (\Exception $e) { }
			@unlink($temp_file);
			
			if (empty(trim($extracted_text))) {
				echo json_encode(['status' => false, 'msg' => 'Gagal membaca teks dokumen.']);
				return;
			}

			$extracted_text = substr($extracted_text, 0, 15000);
		}

		$id_ptk = $this->session->userdata('id_ptk');
		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		if (empty($api_setting) || empty($api_setting->api_key)) {
			echo json_encode(['status' => false, 'msg' => 'API Key belum disetting.']);
			return;
		}

		$nama_mahasiswa = $lokasi->nama_mahasiswa ?? 'Tidak diketahui';
		$prodi_mahasiswa = $lokasi->prodi ?? 'Tidak diketahui';

		if ($need_document) {
			$prompt = "# Konteks\nKamu adalah AI Asisten Akademik. Diberikan isi dokumen Proposal/Pendaftaran Magang dari mahasiswa bernama $nama_mahasiswa (Program Studi: $prodi_mahasiswa).\nJawablah pertanyaan Dosen pembimbing mengenai isi dokumen tersebut dengan ringkas, jelas, dan akurat berdasarkan teks yang diberikan, dan kaitkan relevansinya dengan keilmuan program studi mahasiswa jika memungkinkan.\n\n# Pertanyaan Dosen:\n" . $question_clean . "\n\n# Instruksi Tambahan:\n- Jika jawabannya tidak ada di dokumen, bilang tidak tahu/tidak ditemukan di dokumen.\n- Jika pertanyaan berupa sapaan atau tidak relevan dengan dokumen, balas dengan ramah tanpa perlu membahas isi dokumen.\n- Gunakan format Markdown rapi.\n- Langsung berikan jawaban, jangan berikan salam pembuka jika sedang menjawab seputar dokumen.";
			$user_document = "Berikut adalah isi dokumen:\n\n" . $extracted_text;
		} else {
			$prompt = "# Konteks\nKamu adalah AI Asisten Akademik yang bertugas membantu Dosen terkait pembimbingan mahasiswa magang bernama $nama_mahasiswa (Program Studi: $prodi_mahasiswa).\nJawablah pertanyaan dari Dosen dengan sopan, natural, dan profesional. Jangan membahas isi dokumen karena dosen tidak meminta pengecekan dokumen saat ini. Jika dosen meminta pengecekan dokumen di masa depan, beri tahu mereka untuk menggunakan kata kunci @dokumen di awal pertanyaan.\n\n# Pertanyaan/Pernyataan Dosen:\n" . $question_clean;
			$user_document = "Tolong berikan jawaban langsung tanpa salam pembuka yang berlebihan.";
		}

		$this->load->library('Ai_reviewer');

		$result = $this->ai_reviewer->generate_review(
			$api_setting->ai_provider ?? 'gemini', 
			$api_setting->api_key, 
			$prompt, 
			$user_document, 
			$is_local, 
			false
		);

		if (!$result['status']) {
			echo json_encode(['status' => false, 'msg' => $result['msg']]);
			return;
		}

		$answer = trim($result['review']);

		// Simpan percakapan ke database
		$this->Laporan_magang_model->save_chat_message($nipd, 'user', $question);
		$this->Laporan_magang_model->save_chat_message($nipd, 'ai',   $answer);

		echo json_encode(['status' => true, 'answer' => $answer]);
	}

	public function ajax_load_chat_history()
	{
		$nipd = $this->input->post('nipd');
		if (empty($nipd)) {
			echo json_encode(['status' => false, 'msg' => 'Parameter nipd tidak ada.']);
			return;
		}

		$history = $this->Laporan_magang_model->get_chat_history($nipd);
		$rows    = [];
		foreach ($history as $row) {
			$rows[] = [
				'role'       => $row->role,
				'message'    => $row->message,
				'created_at' => $row->created_at,
			];
		}

		echo json_encode(['status' => true, 'history' => $rows]);
	}

	// ===================================================================
	// SK DPL: Parse PDF SK dari GDrive, kembalikan JSON mahasiswa
	// ===================================================================
	public function ajax_parse_sk_dpl()
	{
		$link_gdrive = $this->input->post('link_gdrive');
		if (empty($link_gdrive)) {
			echo json_encode(['status' => false, 'msg' => 'Link Google Drive tidak boleh kosong.']);
			return;
		}

		// Ambil nama dosen yang sedang login
		$id_ptk = $this->session->userdata('id_ptk');
		$dosen  = $this->db->select('nm_ptk')->where('id_ptk', $id_ptk)->get('siakad_dosen')->row();
		$nm_dosen = $dosen ? $dosen->nm_ptk : '';

		// Konversi link GDrive ke download URL
		$file_url = $link_gdrive;
		$is_gdoc  = false;
		if (str_contains($file_url, 'docs.google.com/document/d/')) {
			$is_gdoc = true;
		}
		if (str_contains($file_url, 'drive.google.com') || str_contains($file_url, 'docs.google.com')) {
			preg_match('/[-\w]{25,}/', $file_url, $matches);
			if (!empty($matches[0])) {
				$file_id = $matches[0];
				if ($is_gdoc) {
					$file_url = 'https://docs.google.com/document/d/' . $file_id . '/export?format=docx';
				} else {
					$file_url = 'https://drive.google.com/uc?export=download&id=' . $file_id;
				}
			}
		}

		$is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1'])
			|| str_contains($_SERVER['HTTP_HOST'], '.test')
			|| str_contains($_SERVER['HTTP_HOST'], '.local'));

		// Download file
		$temp_file = sys_get_temp_dir() . '/' . uniqid('sk_dpl_') . '.tmp';
		$ch_dl = curl_init();
		curl_setopt($ch_dl, CURLOPT_URL, $file_url);
		curl_setopt($ch_dl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_dl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch_dl, CURLOPT_TIMEOUT, 60);
		if ($is_local) curl_setopt($ch_dl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch_dl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
		$file_content = curl_exec($ch_dl);
		$dl_http_code = curl_getinfo($ch_dl, CURLINFO_HTTP_CODE);
		curl_close($ch_dl);

		if (!$file_content || $dl_http_code != 200) {
			echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh file SK (HTTP ' . $dl_http_code . '). Pastikan link sudah disetel "Anyone with the link".']);
			return;
		}

		// Cek kalau yang diunduh HTML (redirect/403)
		if (str_contains(trim(strtolower(substr($file_content, 0, 50))), '<!doctype html')) {
			echo json_encode(['status' => false, 'msg' => 'File tidak dapat diakses langsung. Pastikan file Google Drive disetel "Anyone with the link".']);
			return;
		}

		file_put_contents($temp_file, $file_content);

		// Ekstrak teks dari PDF/DOCX
		$extracted_text = '';
		try {
			$parser = new \Smalot\PdfParser\Parser();
			try {
				$pdf = $parser->parseFile($temp_file);
				$extracted_text = $pdf->getText();
			} catch (\Exception $e) {
				// Coba sebagai DOCX
				try {
					$zip = new ZipArchive;
					if ($zip->open($temp_file) === true) {
						if (($index = $zip->locateName('word/document.xml')) !== false) {
							$data = $zip->getFromIndex($index);
							$data = str_replace(['</w:p>', '<w:br>', '<w:br/>', '<w:br />'], "\n", $data);
							$data = str_replace(['<w:tab>', '<w:tab/>', '<w:tab />'], "\t", $data);
							$extracted_text = strip_tags($data);
						}
						$zip->close();
					}
				} catch (\Exception $e2) { }
			}
		} catch (\Exception $e) { }
		@unlink($temp_file);

		if (empty(trim($extracted_text))) {
			echo json_encode(['status' => false, 'msg' => 'Gagal membaca isi file SK. Pastikan file adalah PDF atau DOCX yang valid dan bukan hasil scan (gambar).']);
			return;
		}

		$extracted_text = substr($extracted_text, 0, 20000);

		// Ambil API key
		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		if (empty($api_setting) || empty($api_setting->api_key)) {
			echo json_encode(['status' => false, 'msg' => 'API Key belum disetting. Silakan setting API Key terlebih dahulu.']);
			return;
		}

		$ai_provider = isset($api_setting->ai_provider) && !empty($api_setting->ai_provider) ? $api_setting->ai_provider : 'gemini';

		// Prompt AI untuk parse SK
		$prompt_system = <<<PROMPT
Kamu adalah sistem ekstraksi data dokumen SK (Surat Keputusan) perguruan tinggi yang sangat teliti.
Tugasmu: ekstrak informasi dari dokumen SK Pengangkatan Dosen Pembimbing Magang/Internship/PKL ke format JSON.

Output HARUS berupa JSON valid saja. TANPA teks apapun di luar JSON, tanpa markdown, tanpa penjelasan.

Struktur JSON:
{
  "no_sk": "...",
  "perihal": "...",
  "instansi_penerbit": "...",
  "tgl_ditetapkan": "YYYY-MM-DD atau string kosong",
  "tgl_berlaku": "YYYY-MM-DD atau string kosong",
  "tgl_berakhir": "YYYY-MM-DD atau string kosong",
  "validitas_legal": "valid atau invalid",
  "catatan_validasi": "penjelasan singkat hasil validasi legal SK",
  "mahasiswa": [
    {
      "no_urut": 1,
      "nim": "NIM tanpa spasi",
      "nama_mahasiswa": "Nama lengkap",
      "nama_perusahaan": "Nama tempat magang/PKL",
      "alamat": "Alamat lengkap tempat magang",
      "nama_dpl": "Nama lengkap dosen pembimbing beserta gelar"
    }
  ]
}

=== ATURAN KRITIS: KOLOM DOSEN PEMBIMBING LAPANGAN (DPL) ===

Dokumen SK memiliki berbagai format kolom DPL. Kamu HARUS menangani semua kemungkinan:

KASUS A - Merged cell (sel gabung):
Nama DPL hanya muncul SEKALI di baris pertama, tetapi mencakup beberapa baris mahasiswa di bawahnya.
Saat teks PDF diekstrak, baris ke-2 dan seterusnya tidak memiliki nama DPL.
ATURAN: Salin nama DPL dari baris pertama ke SEMUA baris berikutnya yang kosong, SAMPAI muncul nama DPL baru.
Contoh teks yang diekstrak:
  Baris 1: "2301010048 | Fenny | RACC Indonesia | ... | Jessica, S.Tr., M.Si"
  Baris 2: "2301010079 | Rennard | Brighton | ... | "   <-- kosong karena merged cell
  Baris 3: "2301010145 | Dewi | Honda | ... | "         <-- kosong karena merged cell
Hasil yang benar: Baris 2 dan 3 menggunakan DPL = "Jessica, S.Tr., M.Si"

KASUS B - Multi-dosen dalam 1 SK (beberapa kelompok):
SK berisi banyak mahasiswa yang dibagi ke beberapa dosen.
Setiap dosen memiliki kelompok mahasiswanya sendiri, dengan merged cell per kelompok.
Contoh: DPL "Jessica" untuk baris 1-4, DPL "Andi" untuk baris 5-8.
ATURAN: Saat ada nama DPL baru muncul, ganti DPL yang sedang aktif. Propagasi terus berlanjut.

KASUS C - Tanpa merged cell (nama DPL ditulis di setiap baris):
Nama DPL ditulis lengkap di setiap baris mahasiswa. Tidak ada masalah, ekstrak biasa.

KASUS D - Tabel berlanjut di halaman berikutnya:
Ketika lampiran SK memiliki banyak mahasiswa, tabel dilanjutkan ke halaman baru.
Di halaman baru, header tabel (No, NIM, Nama, Perusahaan, Alamat, Dosen) muncul lagi.
ATURAN:
  1. Abaikan baris header tabel yang muncul ulang di halaman baru (bukan data mahasiswa).
  2. Lanjutkan ekstraksi data mahasiswa dari nomor urut terakhir.
  3. Propagasi nama DPL tetap berlanjut dari halaman sebelumnya jika baris pertama di halaman baru tidak memiliki DPL.
  4. Nomor urut yang dimulai ulang dari 1 di halaman baru harus kamu sesuaikan menjadi lanjutan nomor sebelumnya.

=== ATURAN UMUM ===
- Jangan lewati satu pun baris mahasiswa, termasuk di halaman kedua, ketiga, dst.
- Jika ada gangguan teks (header/footer halaman, nomor halaman, watermark logo) di antara baris data, abaikan saja.
- NIM biasanya berupa angka 10-13 digit. Bersihkan spasi di NIM jika ada.
- Jika tanggal hanya ada bulan dan tahun, gunakan tanggal 1 (Mei 2026 -> 2026-05-01).
- validitas_legal = "valid" jika SK memiliki: nomor SK, perihal jelas, tanggal penetapan, dan nama pejabat penandatangan.
PROMPT;

		$user_document = "NAMA DOSEN YANG SEDANG LOGIN (gunakan sebagai referensi untuk verifikasi): " . $nm_dosen . "\n\n" .
		                 "Berikut adalah isi teks yang diekstrak dari dokumen SK:\n\n" . $extracted_text;

		$this->load->library('Ai_reviewer');
		$result = $this->ai_reviewer->generate_review($ai_provider, $api_setting->api_key, $prompt_system, $user_document, $is_local, false);

		if (!$result['status']) {
			echo json_encode(['status' => false, 'msg' => 'AI gagal memproses SK: ' . $result['msg']]);
			return;
		}

		// Bersihkan output AI
		$ai_json = $result['review'];
		$ai_json = preg_replace('/```json\s*/', '', $ai_json);
		$ai_json = preg_replace('/```\s*/', '', $ai_json);
		$ai_json = trim($ai_json);

		$parsed = json_decode($ai_json, true);
		if (!$parsed) {
			echo json_encode(['status' => false, 'msg' => 'AI tidak menghasilkan JSON yang valid. Coba lagi.', 'raw' => substr($ai_json, 0, 500)]);
			return;
		}

		// Filter mahasiswa: hanya yang nama DPL-nya cocok dengan dosen login (fuzzy)
		$nm_dosen_lower = mb_strtolower(trim($nm_dosen));
		// Ambil nama pertama (sebelum koma/gelar) untuk toleransi perbedaan gelar
		$nama_pertama_dosen = trim(explode(',', $nm_dosen_lower)[0]);
		// Juga ambil kata pertama saja (lebih toleran)
		$kata_pertama_dosen = trim(explode(' ', $nama_pertama_dosen)[0]);

		// Step 1: Propagasi nama DPL dari baris sebelumnya jika kosong (handle merged cell)
		$last_dpl = '';
		if (!empty($parsed['mahasiswa']) && is_array($parsed['mahasiswa'])) {
			foreach ($parsed['mahasiswa'] as &$mhs) {
				$nama_dpl = trim($mhs['nama_dpl'] ?? '');
				if (!empty($nama_dpl)) {
					$last_dpl = $nama_dpl;
				} elseif (!empty($last_dpl)) {
					// Propagasi dari baris sebelumnya
					$mhs['nama_dpl'] = $last_dpl;
				}
			}
			unset($mhs);
		}

		// Step 2: Filter berdasarkan nama DPL
		$mahasiswa_filtered = [];
		if (!empty($parsed['mahasiswa']) && is_array($parsed['mahasiswa'])) {
			foreach ($parsed['mahasiswa'] as $mhs) {
				$nm_dpl_lower = mb_strtolower(trim($mhs['nama_dpl'] ?? ''));
				$nama_pertama_dpl = trim(explode(',', $nm_dpl_lower)[0]);
				$kata_pertama_dpl = trim(explode(' ', $nama_pertama_dpl)[0]);

				$cocok = false;
				if (!empty($nm_dosen_lower) && !empty($nm_dpl_lower)) {
					// 1. Kata pertama nama (paling toleran terhadap OCR)
					if (!empty($kata_pertama_dosen) && strlen($kata_pertama_dosen) >= 4 &&
						str_contains($nm_dpl_lower, $kata_pertama_dosen)) {
						$cocok = true;
					}
					// 2. Nama pertama (sebelum gelar)
					if (!$cocok && !empty($nama_pertama_dosen) && strlen($nama_pertama_dosen) >= 4 &&
						(str_contains($nm_dpl_lower, $nama_pertama_dosen) ||
						 str_contains($nm_dosen_lower, $nama_pertama_dpl))) {
						$cocok = true;
					}
					// 3. Similar_text fallback
					if (!$cocok) {
						similar_text($nm_dosen_lower, $nm_dpl_lower, $pct);
						if ($pct > 55) $cocok = true;
					}
				}
				if ($cocok) {
					$mahasiswa_filtered[] = $mhs;
				}
			}
		}

		// Cek NIM mana yang sudah ada di DB dan status DPL-nya
		$existing_map = []; // nipd => ['exists'=>true, 'dpl_empty'=>true/false]
		$existing_rows = $this->db->select('nipd, dosen_pembimbing_lapangan')->get('simlapkerma_lokasi_magang')->result();
		foreach ($existing_rows as $row) {
			$existing_map[$row->nipd] = [
				'exists'    => true,
				'dpl_empty' => (empty($row->dosen_pembimbing_lapangan)),
			];
		}

		// Tandai status masing-masing mahasiswa
		foreach ($mahasiswa_filtered as &$mhs) {
			$nim = $mhs['nim'] ?? '';
			if (isset($existing_map[$nim])) {
				if ($existing_map[$nim]['dpl_empty']) {
					// Ada di DB tapi DPL kosong — bisa di-update
					$mhs['db_status'] = 'exists_no_dpl';
				} else {
					// Ada di DB dan DPL sudah terisi — skip
					$mhs['db_status'] = 'exists_complete';
				}
			} else {
				// Belum ada di DB — insert baru
				$mhs['db_status'] = 'new';
			}
		}
		unset($mhs);

		echo json_encode([
			'status'       => true,
			'no_sk'        => $parsed['no_sk'] ?? '-',
			'perihal'      => $parsed['perihal'] ?? '-',
			'tgl_berlaku'  => $parsed['tgl_berlaku'] ?? '',
			'tgl_berakhir' => $parsed['tgl_berakhir'] ?? '',
			'tgl_ditetapkan' => $parsed['tgl_ditetapkan'] ?? '',
			'validitas_legal' => $parsed['validitas_legal'] ?? 'unknown',
			'catatan_validasi' => $parsed['catatan_validasi'] ?? '',
			'nama_dosen_login' => $nm_dosen,
			'mahasiswa'    => $mahasiswa_filtered,
			'total_all'    => count($parsed['mahasiswa'] ?? []),
		]);
	}

	// ===================================================================
	// SK DPL: Simpan mahasiswa terpilih ke simlapkerma_lokasi_magang
	// ===================================================================
	public function ajax_save_sk_mahasiswa()
	{
		$mahasiswa_json = $this->input->post('mahasiswa');
		$no_sk          = $this->input->post('no_sk'); // Ambil no_sk dari frontend

		if (empty($mahasiswa_json)) {
			echo json_encode(['status' => false, 'msg' => 'Tidak ada data mahasiswa yang dikirim.']);
			return;
		}

		$mahasiswa_list = json_decode($mahasiswa_json, true);
		if (!is_array($mahasiswa_list) || empty($mahasiswa_list)) {
			echo json_encode(['status' => false, 'msg' => 'Format data tidak valid.']);
			return;
		}

		$id_ptk  = $this->session->userdata('id_ptk');
		$id_smt  = $this->session->userdata('tahun_ajaran');
		$user_id = $this->session->userdata('user_id');
		$tahun   = date('Y');
		$now     = date('Y-m-d H:i:s');

		$saved   = 0;
		$updated = 0;
		$skipped = 0;

		foreach ($mahasiswa_list as $mhs) {
			$nipd = trim($mhs['nim'] ?? '');
			if (empty($nipd)) {
				$skipped++;
				continue;
			}

			// Cek apakah NIM sudah ada di lokasi_magang
			$existing = $this->db->select('nipd, dosen_pembimbing_lapangan')
				->where('nipd', $nipd)
				->get('simlapkerma_lokasi_magang')
				->row();

			if ($existing) {
				if (empty($existing->dosen_pembimbing_lapangan)) {
					// Ada tapi DPL kosong — update DPL, id_smt, dan nomor_sk
					$this->db->where('nipd', $nipd)
						->update('simlapkerma_lokasi_magang', [
							'dosen_pembimbing_lapangan' => $id_ptk,
							'id_smt'                    => $id_smt,
							'nomor_sk'                  => $no_sk,
						]);
					$updated++;
				} else {
					// DPL sudah terisi — skip
					$skipped++;
				}
				continue;
			}

			// Insert data baru
			$this->db->insert('simlapkerma_lokasi_magang', [
				'nipd'                      => $nipd,
				'nama_perusahaan'           => $mhs['nama_perusahaan'] ?? '',
				'alamat'                    => $mhs['alamat'] ?? '',
				'tahun_magang'              => $tahun,
				'dosen_pembimbing_lapangan' => $id_ptk,
				'id_smt'                    => $id_smt,
				'nomor_sk'                  => $no_sk,
				'create_by'                 => $user_id,
				'create_date'               => $now,
			]);
			$saved++;
		}

		$parts = [];
		if ($saved   > 0) $parts[] = $saved   . ' mahasiswa baru ditambahkan';
		if ($updated > 0) $parts[] = $updated . ' mahasiswa diperbarui DPL-nya';
		if ($skipped > 0) $parts[] = $skipped . ' dilewati (DPL sudah terisi atau NIM kosong)';

		echo json_encode([
			'status'  => true,
			'msg'     => implode(', ', $parts) . '.',
			'saved'   => $saved,
			'updated' => $updated,
			'skipped' => $skipped,
		]);
	}
}
