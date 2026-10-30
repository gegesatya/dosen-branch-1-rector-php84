<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_review_laporan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Review_laporan_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			if ($this->input->is_ajax_request()) {
				header('HTTP/1.1 401 Unauthorized');
				echo json_encode(['status' => false, 'msg' => 'Sesi telah habis. Silakan login kembali.']);
				exit;
			}
			redirect('auth/login', 'refresh');
		}
		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	private function _check_auth($nipd)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		// Jika Admin/Superadmin (Group 1), bisa di-bypass (opsional). 
		// Karena controller ini memang dibatasi untuk dosen (Group 2), kita asumsikan yang login adalah dosen.
		$dpl = $this->db->select('dosen_pembimbing_lapangan')
			->where('nipd', $nipd)
			->get('simlapkerma_lokasi_magang')
			->row();

		if (!$dpl || $dpl->dosen_pembimbing_lapangan !== $id_ptk) {
			$is_ajax = $this->input->is_ajax_request();
			if ($is_ajax) {
				echo json_encode(['status' => false, 'msg' => 'Akses Ditolak. Anda bukan DPL mahasiswa ini.']);
				exit;
			} else {
				$output['judul_besar'] = 'Akses Ditolak';
				$output['judul_kecil'] = 'Error 403 Forbidden';
				$output['menu_aktif'] = '';
				$output['content'] = 'review_laporan/error_403';
				$this->load->view('master_page', $output);
				$this->output->_display();
				exit;
			}
		}
	}

	public function index($nipd)
	{
		$this->_check_auth($nipd);
		$output['judul_besar'] = 'Review Laporan';
		$output['judul_kecil'] = ' Oleh Dosen Pembimbing Lapangan';
		$output['menu_aktif'] = 'm_laporan_magang';
		$output['parent_magang'] = 'active open';
		$output['content'] = 'review_laporan/review_laporan_view';
		$output['after_page'] = 'review_laporan/review_laporan_after_page';
		$output['nipd'] = $nipd;
		
		$mahasiswa = $this->db->select('m.nm_pd, m.foto, ps.nm_lemb as nm_prodi, lm.nama_perusahaan, d.nama_lengkap as nm_dpl, lm.tanggal_mulai, lm.tanggal_selesai')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
			->join('sms ps', 'ps.id_sms = mp.id_sms', 'left')
			->join('simlapkerma_lokasi_magang lm', 'lm.nipd = mp.nipd', 'left')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = lm.dosen_pembimbing_lapangan', 'left')
			->where('mp.nipd', $nipd)->get()->row();
			
		$output['nm_pd'] = $mahasiswa ? $mahasiswa->nm_pd : '';
		$output['foto_mhs'] = ($mahasiswa && !empty($mahasiswa->foto)) ? $mahasiswa->foto : null;
		$output['nm_prodi'] = $mahasiswa ? $mahasiswa->nm_prodi : '';
		$output['nama_perusahaan'] = $mahasiswa ? $mahasiswa->nama_perusahaan : '';
		$output['nm_dpl'] = $mahasiswa ? $mahasiswa->nm_dpl : '';
		$output['tanggal_mulai'] = $mahasiswa ? $mahasiswa->tanggal_mulai : '';
		$output['tanggal_selesai'] = $mahasiswa ? $mahasiswa->tanggal_selesai : '';

		$logs = $this->db->select('tl.*')
			->from('simlapkerma_lokasi_magang m')
			->join('simlapkerma_tracking_kegiatan tl', 'tl.nipd = m.nipd')
			->where('tl.nipd', $nipd)
			->order_by('tl.week', 'asc')
			->get()->result();

		$missing_weeks = 0;
		$last_filled_date = null;
		$latest_timestamp = 0;

		$bulan_indo = [
			'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
			'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
			'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
			'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
		];

		// Hitung berapa hari di awal minggu pertama yang boleh kosong
		// karena mahasiswa memang belum mulai magang pada hari tersebut.
		// DAYOFWEEK MySQL: 1=Minggu, 2=Senin, ..., 7=Sabtu
		// Sistem kita hari_1=Senin ... hari_6=Sabtu
		$skipped_start_days = 0;
		if ($mahasiswa && !empty($mahasiswa->tanggal_mulai)) {
			$start_dow = (int)date('N', strtotime($mahasiswa->tanggal_mulai)); // 1=Mon..7=Sun (ISO)
			// Hari 1..6 = Senin..Sabtu. Jika mulai di Rabu (dow=3), maka hari 1 (Sen) & 2 (Sel) boleh kosong.
			$skipped_start_days = ($start_dow >= 1 && $start_dow <= 6) ? ($start_dow - 1) : 0;
		}

		if (!empty($logs)) {
			$max_week = 0;
			foreach ($logs as $log) {
				$ts = strtotime(!empty($log->change_date) ? $log->change_date : $log->create_date);
				if ($ts > $latest_timestamp) {
					$latest_timestamp = $ts;
					$last_filled_date = date('Y-m-d H:i:s', $ts);
				}
				if ((int)$log->week > $max_week) {
					$max_week = (int)$log->week;
				}
			}

			$current_timestamp = time();
			if ($latest_timestamp > 0 && $current_timestamp > $latest_timestamp) {
				$seconds_passed = $current_timestamp - $latest_timestamp;
				if ($seconds_passed > (7 * 24 * 3600)) {
					$missing_weeks = floor($seconds_passed / (7 * 24 * 3600));
				}
			}
			
			$alasan_edits = $this->db->select('*')
				->from('simlapkerma_alasan_edit_laporan')
				->where('nipd', $nipd)
				->order_by('created_at', 'desc')
				->get()->result();

			$alasan_map = [];
			foreach ($alasan_edits as $al) {
				$alasan_map[$al->id_tracking] ??= $al;
			}

			foreach ($logs as &$log) {
				$log->alasan_edit = $alasan_map[$log->id_tracking] ?? null;

				$empty_days = [];
				$filled_contents = [];
				$is_week_one = ((int)$log->week === 1);
				for ($i = 1; $i <= 6; $i++) {
					// Pada week 1, hari sebelum mahasiswa mulai magang tidak wajib diisi
					if ($is_week_one && $skipped_start_days > 0 && $i <= $skipped_start_days) {
						continue;
					}
					$field = 'kegiatan_hari_' . $i;
					$raw_val = $log->$field ?? '';
					$js = @json_decode($raw_val, true);
					if (is_array($js) && (array_key_exists('pagi', $js) || array_key_exists('siang', $js) || array_key_exists('sore', $js) || array_key_exists('tanggal_kegiatan', $js))) {
						$pagi_teks = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
						$siang_teks = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
						$sore_teks = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
						$content_raw = $pagi_teks . ' ' . $siang_teks . ' ' . $sore_teks;
					} else {
						$content_raw = $raw_val;
					}
					$content = trim(strip_tags(str_replace(['&nbsp;', '<p>', '</p>', '<br>', '<br/>', "\r", "\n", "\t"], '', $content_raw)));
					if (empty($content)) {
						$empty_days[] = $i;
					} else {
						$normalized = preg_replace('/\s+/', ' ', strtolower($content));
						
						$is_exempt = false;
						$exempt_keywords = ['libur', 'merah', 'tutup', 'sakit', 'izin', 'ijin', 'melanjutkan', 'lanjutan'];
						foreach ($exempt_keywords as $ek) {
							if (str_contains($normalized, $ek)) {
								$is_exempt = true;
								break;
							}
						}
						
						if (strlen($normalized) > 5 && !$is_exempt) {
							$filled_contents[] = $normalized;
						}
					}
				}

				$log->is_incomplete = !empty($empty_days);
				$log->skipped_start_days = $is_week_one ? $skipped_start_days : 0; // info untuk view
				$log->empty_days = $empty_days;
				
				$log->is_copas = false;
				if (count($filled_contents) >= 2) {
					$unique_contents = array_unique($filled_contents);
					if (count($unique_contents) == 1) {
						$log->is_copas = true;
					}
				}
				
				$log->is_late = false;
				$log->late_days = 0;
				$parts = explode(' - ', (string)($log->periode_laporan ?? ''));
				if (count($parts) == 2) {
					$end_date_str = trim($parts[1]);
					$end_date_eng = strtr($end_date_str, $bulan_indo);
					$end_timestamp = strtotime($end_date_eng);
					if ($end_timestamp) {
						$submit_ts = strtotime(!empty($log->change_date) ? $log->change_date : $log->create_date);
						if ($submit_ts > ($end_timestamp + 86400)) {
							$log->is_late = true;
							$log->late_days = floor(($submit_ts - $end_timestamp) / 86400);
						}
					}
				}
			}
			unset($log);
		}
		
		
		$progress_pengisian = 0;
		$progress_review = 0;

		if ($mahasiswa && !empty($mahasiswa->tanggal_mulai) && !empty($mahasiswa->tanggal_selesai)) {
			$start = strtotime($mahasiswa->tanggal_mulai);
			$end = strtotime($mahasiswa->tanggal_selesai);
			$total_days = floor(($end - $start) / 86400) + 1;
			$total_weeks_overall = ceil($total_days / 7);

			$total_filled = count($logs);
			$total_reviewed = 0;

			foreach ($logs as $log) {
				if (!empty($log->review_dpl)) {
					$total_reviewed++;
				}
			}

			if ($total_weeks_overall > 0) {
				$progress_pengisian = ($total_filled / $total_weeks_overall) * 100;
				if ($progress_pengisian > 100) $progress_pengisian = 100;
			}

			if ($total_filled > 0) {
				$progress_review = ($total_reviewed / $total_filled) * 100;
			}
		}

		$output['progress_pengisian'] = round($progress_pengisian);
		$output['progress_review'] = round($progress_review);
		
		$output['missing_weeks'] = $missing_weeks;
		$output['logs'] = $logs;

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$nipd = $this->input->post('nipd');
		if ($nipd) {
			$this->_check_auth($nipd);
		}
		
		$list = $this->Review_laporan_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {

			$html = '<a href="javascript:void()" class="btn btn-sm btn-primary"
						onclick="review_laporan(\'' . $rg->id_tracking . '\')">
						<i class="icon-pencil"></i> Review Laporan</a>';

			$no++;
			$row = [];
			$row[] = $no;
			$is_edited_after = (!empty($rg->review_dpl) && $rg->change_by == $rg->create_by);
			$badge_edited = $is_edited_after ? '<br><span class="label label-warning" style="margin-top: 5px; display: inline-block;">Diedit Mahasiswa Setelah Review</span>' : '';
			$row[] = 'Week : ' . $rg->week . '<br>Periode : ' . $rg->periode_laporan . $badge_edited;
			$row[] =  $rg->review_dpl;


			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Review_laporan_model->count_all(),
			"recordsFiltered" => $this->Review_laporan_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Review_laporan_model->get_by_id($id);
		if ($data) {
			$this->_check_auth($data->nipd);
		}
		echo json_encode($data);
	}

	public function ajax_update()
	{
		$id_tracking = $this->input->post('id_tracking');
		$log = $this->Review_laporan_model->get_by_id($id_tracking);
		if ($log) {
			$this->_check_auth($log->nipd);
		}

		// $this->_validate_lapor_nilai();
		$user = $this->ion_auth->user()->row();
		$data = [
			'review_dpl' =>	$this->input->post('review_dpl'),
			'skor_dpl' => (!empty($this->input->post('skor_dpl')) ? $this->input->post('skor_dpl') : null),
			'status_laporan' => 1,
			'change_by' => $user->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$data = $this->security->xss_clean($data);
		$this->Review_laporan_model->update(['id_tracking' => $this->input->post('id_tracking')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate_lapor_nilai()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$review_dpl = $this->input->post('review_dpl');

		if ($review_dpl == '') {
			$data['inputerror'][] = 'review_dpl';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function detail($id_tracking)
	{
		$log = $this->Review_laporan_model->get_by_id($id_tracking);
		if (!$log) {
			show_404();
		}
		$this->_check_auth($log->nipd);

		// Hitung kelengkapan hari
		$empty_days = [];
		$skipped_start_days = 0;
		if ($log->week == 1) {
			$mahasiswa = $this->db->select('tanggal_mulai')
				->from('simlapkerma_lokasi_magang')
				->where('nipd', $log->nipd)->get()->row();
			if ($mahasiswa && !empty($mahasiswa->tanggal_mulai)) {
				$start_dow = (int)date('N', strtotime($mahasiswa->tanggal_mulai));
				$skipped_start_days = ($start_dow >= 1 && $start_dow <= 6) ? ($start_dow - 1) : 0;
			}
		}

		for ($i = 1; $i <= 6; $i++) {
			if ($log->week == 1 && $skipped_start_days > 0 && $i <= $skipped_start_days) {
				continue;
			}
			$field = 'kegiatan_hari_' . $i;
			$raw_val = $log->$field ?? '';
			$js = @json_decode($raw_val, true);
			if (is_array($js) && (isset($js['pagi']) || isset($js['siang']) || isset($js['sore']))) {
				$pagi_teks = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
				$siang_teks = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
				$sore_teks = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
				$content_raw = $pagi_teks . ' ' . $siang_teks . ' ' . $sore_teks;
			} else {
				$content_raw = $raw_val;
			}
			$content = trim(strip_tags(str_replace(['&nbsp;', '<p>', '</p>', '<br>', '<br/>', "\r", "\n", "\t"], '', $content_raw)));
			if (empty($content)) {
				$empty_days[] = $i;
			}
		}
		$log->is_incomplete = !empty($empty_days);
		$log->skipped_start_days = $skipped_start_days;

		$output['judul_besar'] = 'Review Laporan';
		$output['judul_kecil'] = ' Detail Mingguan Mahasiswa';
		$output['alasan_edit'] = $this->Review_laporan_model->get_alasan_edit($id_tracking);
		$output['menu_aktif'] = 'm_laporan_magang';
		$output['parent_magang'] = 'active open';
		$output['content'] = 'review_laporan/review_laporan_detail_view';
		$output['after_page'] = 'review_laporan/review_laporan_detail_after_page';
		
		$output['log'] = $log;
		$output['id_tracking'] = $id_tracking;
		$output['nipd'] = $log->nipd;
		$output['nm_pd'] = $log->nm_pd;
		$output['foto_mhs'] = ($log && !empty($log->foto)) ? $log->foto : null;
		$output['nm_prodi'] = $log->nm_prodi;
		$output['nama_perusahaan'] = $log->nama_perusahaan;

		// Fetch DPL Name
		$dpl = $this->db->select('d.nama_lengkap as nm_dpl')
			->from('simlapkerma_lokasi_magang lm')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = lm.dosen_pembimbing_lapangan', 'left')
			->where('lm.nipd', $log->nipd)->get()->row();
		$output['nm_dpl'] = $dpl ? $dpl->nm_dpl : '';

		$this->load->view('master_page', $output);
	}

	public function save_review()
	{
		$id_tracking = $this->input->post('id_tracking');
		$nipd = $this->input->post('nipd');
		$review_dpl = $this->input->post('review_dpl');
		
		$skor_dpl = $this->input->post('skor_dpl');
		
		$this->_check_auth($nipd);
		
		$user = $this->ion_auth->user()->row();
		$data = [
			'review_dpl' => $review_dpl,
			'skor_dpl' => (!empty($skor_dpl) ? $skor_dpl : null),
			'status_laporan' => 1,
			'change_by' => $user->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$data = $this->security->xss_clean($data);
		$this->Review_laporan_model->update(['id_tracking' => $id_tracking], $data);
		
		redirect('C_review_laporan/index/' . $nipd);
	}

	public function ajax_ai_review()
	{
		$id_tracking = $this->input->post('id_tracking');
		if (empty($id_tracking)) {
			echo json_encode(['status' => false, 'msg' => 'ID Tracking tidak ditemukan.']);
			return;
		}

		$log = $this->Review_laporan_model->get_by_id($id_tracking);
		if (!$log) {
			echo json_encode(['status' => false, 'msg' => 'Data laporan tidak ditemukan.']);
			return;
		}
		$this->_check_auth($log->nipd);
		
		// Jika DPL sudah memberi review, AI tidak perlu diproses ulang / tidak boleh ditimpa
		if (!empty($log->review_dpl)) {
			echo json_encode(['status' => false, 'msg' => 'Laporan sudah di-review oleh DPL. AI Review tidak lagi tersedia untuk laporan ini.']);
			return;
		}

		// Jika AI Review sudah pernah dijalankan dan disimpan, langsung kembalikan hasilnya
		if (!empty($log->ai_review)) {
			$data = json_decode($log->ai_review, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				echo json_encode(['status' => true, 'data' => $data, 'cached' => true]);
				return;
			}
		}

		$chatgpt_indicators = ['pointer-events-auto', 'data-turn="assistant"', 'data-turn-id', 'data-conversation-screenshot-content'];
		$is_chatgpt = false;

		$check_chatgpt = function($val) use ($chatgpt_indicators) {
			if (!is_string($val)) return false;
            return array_any($chatgpt_indicators, fn($ind) => str_contains($val, $ind));
		};

		// Compile the user document / log details
		$doc_text = "MAHASISWA:\n";
		$doc_text .= "Nama: " . ($log->nm_pd ?? '-') . "\n";
		$doc_text .= "NIM: " . ($log->nipd ?? '-') . "\n";
		$doc_text .= "Program Studi: " . ($log->nm_prodi ?? '-') . "\n";
		$doc_text .= "Minggu Ke: " . ($log->week ?? '-') . "\n";
		$doc_text .= "Periode: " . ($log->periode_laporan ?? '-') . "\n\n";
		$doc_text .= "KEGIATAN HARIAN:\n";
		for ($i = 1; $i <= 6; $i++) {
			$f = 'kegiatan_hari_' . $i;
			$raw_val = $log->$f ?? '';
			if ($check_chatgpt($raw_val)) $is_chatgpt = true;
			
			$js = @json_decode($raw_val, true);
			if (is_array($js) && (isset($js['pagi']) || isset($js['siang']) || isset($js['sore']))) {
				$pagi_teks = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
				$siang_teks = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
				$sore_teks = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
				$txt = "Pagi: " . strip_tags($pagi_teks ?: '-') . " | Siang: " . strip_tags($siang_teks ?: '-') . " | Sore: " . strip_tags($sore_teks ?: '-');
			} else {
				$txt = strip_tags($raw_val ?? '-');
			}
			$doc_text .= "Hari $i: " . $txt . "\n";
		}
		
		if ($check_chatgpt($log->pengetahuan_baru ?? '')) $is_chatgpt = true;
		if ($check_chatgpt($log->keterampilan_baru ?? '')) $is_chatgpt = true;
		if ($check_chatgpt($log->tujuan_internship ?? '')) $is_chatgpt = true;
		if ($check_chatgpt($log->catatan_lain ?? '')) $is_chatgpt = true;

		$doc_text .= "\nREFLEKSI:\n";
		$doc_text .= "Pengetahuan baru yang diperoleh: " . strip_tags($log->pengetahuan_baru ?? '-') . "\n";
		$doc_text .= "Keterampilan baru yang diperoleh: " . strip_tags($log->keterampilan_baru ?? '-') . "\n";
		$doc_text .= "Tujuan internship yang dicapai: " . strip_tags($log->tujuan_internship ?? '-') . "\n";
		$doc_text .= "Catatan lain: " . strip_tags($log->catatan_lain ?? '-') . "\n";
		
		// Ambil data minggu sebelumnya untuk mengevaluasi kesinambungan
		$prev_log = $this->db->where('nipd', $log->nipd)
							 ->where('week <', $log->week)
							 // Hanya ambil jika minggu sebelumnya sudah direview (runtun),
							 // jika mau mengambil pokoknya minggu sebelumnya ada, komentar baris berikut:
							 ->where('review_dpl IS NOT NULL') 
							 ->order_by('week', 'desc')
							 ->limit(1)
							 ->get('simlapkerma_tracking_kegiatan')
							 ->row();
							 
		if ($prev_log) {
			$doc_text .= "\nDATA MINGGU SEBELUMNYA (Minggu Ke-" . $prev_log->week . "):\n";
			$doc_text .= "Pengetahuan: " . strip_tags($prev_log->pengetahuan_baru ?? '-') . "\n";
			$doc_text .= "Keterampilan: " . strip_tags($prev_log->keterampilan_baru ?? '-') . "\n";
			$doc_text .= "Tujuan: " . strip_tags($prev_log->tujuan_internship ?? '-') . "\n";
		}

		if ($is_chatgpt) {
			$doc_text .= "\n[PERINGATAN SISTEM]: Sistem mendeteksi adanya indikator copy-paste mentah dari ChatGPT (seperti tag HTML milik web ChatGPT) pada teks yang dikumpulkan mahasiswa ini.\n";
		}

		// Construct prompt
		$prompt = "Anda adalah Dosen Pembimbing Lapangan (DPL) Magang/Internship yang bertugas memberikan review logbook mingguan mahasiswa secara konstruktif dan akademis.\n";
		$prompt .= "Evaluasilah isi logbook mingguan mahasiswa di atas. Berikan feedback dalam format JSON dengan struktur yang SANGAT KETAT berikut:\n";
		$prompt .= "{\n";
		$prompt .= "  \"skor_kelengkapan\": <angka 0-100 menandakan tingkat kelengkapan laporan>,\n";
		$prompt .= "  \"evaluasi_analisis\": \"<berisi analisis singkat tentang kesesuaian kegiatan dengan kompetensi program studi dan kejelasan penulisan>\",\n";
		$prompt .= "  \"feedback_html\": \"<berisi bullet point poin positif dan hal yang perlu ditingkatkan dari kegiatan mahasiswa, diformat dengan tag HTML sederhana seperti <strong> dan <ul>/<li>>\",\n";
		$prompt .= "  \"progres_kompetensi_html\": \"<WAJIB gunakan format list HTML (<ul><li><strong>Pengetahuan Baru:</strong> [Analisis kritis apakah pengetahuan minggu ini merupakan hal baru atau sekadar mengulang minggu lalu. JANGAN sekadar menyalin/mengulang tulisan mahasiswa]</li><li><strong>Keterampilan Baru:</strong> [Analisis peningkatan keterampilan dari minggu sebelumnya]</li><li><strong>Tujuan Internship:</strong> [Analisis progres ketercapaian tujuan berdasarkan kesinambungan]</li></ul>). Jika ada 'DATA MINGGU SEBELUMNYA', WAJIB bandingkan secara eksplisit. Jika tidak ada, evaluasi kedalaman refleksinya>\",\n";
		$prompt .= "  \"rekomendasi_review\": \"<berisi draf review resmi DPL berdurasi 2-4 kalimat. Gunakan gaya bahasa yang resmi, akademis, namun tetap hangat dan akrab. JANGAN gunakan sapaan kasual seperti 'Halo [Nama]', melainkan langsung buka dengan apresiasi atau sapaan profesional yang siap disalin oleh dosen>\"\n";
		$prompt .= "}\n\n";
		$prompt .= "Ketentuan feedback:\n";
		$prompt .= "- Gunakan Bahasa Indonesia yang baik, sopan, dan formal.\n";
		$prompt .= "- Fokus pada aspek pengembangan mahasiswa dan relevansi akademis/praktis dari apa yang mereka kerjakan.\n";
		$prompt .= "- PERHATIAN KHUSUS: Deteksi apakah mahasiswa mengisi kegiatan yang sama persis (copy-paste) atau sangat repetitif dari hari ke hari. PENGECUALIAN: JANGAN anggap repetitif/copas jika kegiatannya adalah ketidakhadiran (seperti: 'libur', 'hari libur', 'hari merah', 'perusahaan tutup', 'sakit', 'izin') atau jika terdapat kata yang menunjukkan keberlanjutan proses (seperti: 'melanjutkan', 'lanjutan'). Jika benar-benar repetitif di luar pengecualian tersebut, berikan hukuman pengurangan skor kelengkapan yang signifikan, serta tegur secara tegas pada poin 'hal yang perlu ditingkatkan' dan pada 'rekomendasi_review'.\n";
		$prompt .= "- EVALUASI KESINAMBUNGAN: Anda WAJIB membandingkan bagian 'REFLEKSI' minggu ini dengan 'DATA MINGGU SEBELUMNYA'. Anda dilarang keras sekadar mengulangi kalimat mahasiswa. Anda harus memberikan komentar analitis/kritis apakah mahasiswa benar-benar mempelajari hal baru, atau sekadar menulis ulang capaian dari minggu lalu. Jika stagnan/repetitif, berikan teguran.\n";
		if ($is_chatgpt) {
			$prompt .= "- PERHATIAN KHUSUS (PLAGIARISME AI): Sistem mendeteksi mahasiswa ini melakukan copy-paste mentah dari ChatGPT. Anda WAJIB memberikan pengurangan skor kelengkapan yang signifikan (maksimal skor yang diberikan adalah 60). Berikan soft warning / teguran halus namun tegas pada bagian 'rekomendasi_review' dan 'feedback_html' yang mengingatkan mahasiswa bahwa penggunaan AI diperbolehkan sebagai alat bantu, namun sangat dilarang melakukan copy-paste mentah tanpa mengedit atau menyaring bahasanya sendiri.\n";
		}
		$prompt .= "- DILARANG menggunakan kata sapaan santai seperti 'Halo', 'Hai'. Gunakan gaya komunikasi dosen ke mahasiswa yang elegan.\n";
		$prompt .= "- PASTIKAN OUTPUT HANYA JSON MURNI YANG VALID. Dilarang menambahkan markdown seperti ```json atau teks awalan/akhiran di luar objek JSON tersebut.";

		$this->load->library('Ai_reviewer');
		$this->load->model('Setting_api_key_model');
		$id_ptk = $this->session->userdata('id_ptk');
		$api_key_row = $this->Setting_api_key_model->get_api_key($id_ptk);

		if ($api_key_row && !empty($api_key_row->api_key)) {
			$provider = $api_key_row->ai_provider ?: 'gemini';
			$api_key  = $api_key_row->api_key;
		} else {
			$settings = $this->db->get('setting')->row();
			$provider = $settings->ai_provider ?? 'gemini';
			$api_key  = $settings->ai_api_key ?? '';
		}

		if (empty($api_key)) {
			echo json_encode(['status' => false, 'msg' => 'API Key belum dikonfigurasi. Silakan atur di menu Settings → AI Configuration.']);
			return;
		}

		$is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || str_contains($_SERVER['HTTP_HOST'], 'localhost'));
		$res = $this->ai_reviewer->generate_review($provider, $api_key, $prompt, $doc_text, $is_local, true);

		if ($res['status']) {
			$json_str = $res['review'];
			
			// Ekstrak hanya bagian JSON (dari { pertama hingga } terakhir)
			if (preg_match('/\{[\s\S]*\}/', $json_str, $matches)) {
				$json_str = $matches[0];
			}
			
			$data = json_decode($json_str, true);
			if (json_last_error() === JSON_ERROR_NONE && isset($data['rekomendasi_review'])) {
				// Simpan ke DB agar tidak diregenerate ulang
				$this->db->where('id_tracking', $id_tracking)->update('simlapkerma_tracking_kegiatan', ['ai_review' => $json_str]);
				
				echo json_encode(['status' => true, 'data' => $data]);
			} else {
				echo json_encode(['status' => false, 'msg' => 'Gagal memproses respons AI ke format JSON yang valid.', 'raw' => $res['review']]);
			}
		} else {
			echo json_encode($res);
		}
	}
}
