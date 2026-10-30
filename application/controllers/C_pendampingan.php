<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pendampingan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_pendampingan_model', 'pendampingan');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [1, 2, 5, 12, 15]; 
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Pendampingan';
		$output['judul_kecil'] 		= 'eKinerja';
		$output['menu_aktif'] 		= 'm_pendampingan';
		$output['parent_kinerja']	= 'active open';
		$output['content'] 			= 'pendampingan/v_pendampingan';
		$output['after_page'] 		= 'pendampingan/v_pendampingan_after_page';
		$output['user_email'] 		= $this->ion_auth->user()->row()->email;
		$output['is_wadir2'] 		= $this->_is_wadir2();
		
		// Load semesters for dropdown
		$this->db->order_by('id_smt', 'desc');
		$output['dd_semester'] = $this->db->get('semester')->result();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$is_wadir2 = $this->_is_wadir2();
		$list = $this->pendampingan->get_datatables($is_wadir2);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $p) {
			$no++;
			$row = [];
			$row[] = $no;
			if ($is_wadir2) {
				$row[] = '<b>' . htmlspecialchars($p->nm_ptk ?? '-', ENT_QUOTES, 'UTF-8') . '</b>';
			}
			$row[] = $p->nm_smt;
			
			$kategori_label = $p->kategori;
			if ($kategori_label == 'KKN') $kategori_label = 'Kuliah Kerja Nyata (KKN)';
			if ($kategori_label == 'PKL') $kategori_label = 'Praktek Kerja Lapangan/Internship/Magang';
			$row[] = $kategori_label;
			$jumlah_text = $p->jumlah;
			if (!empty($p->mahasiswa_json)) {
				$json_safe = htmlspecialchars($p->mahasiswa_json, ENT_QUOTES, 'UTF-8');
				$jumlah_text .= ' <button type="button" class="btn btn-xs btn-info" data-mhs="' . $json_safe . '" onclick="show_mahasiswa_from_btn(this)"><i class="fa fa-users"></i> Lihat (' . $p->jumlah . ' Mhs)</button>';
			}
			$row[] = $jumlah_text;
			
			$status = '<span class="badge badge-warning">Menunggu</span>';
			if ($p->approve_by_kaprodi == 1) {
				$status = '<span class="badge badge-success">Disetujui</span>';
			} elseif ($p->approve_by_kaprodi == 2) {
				$status = '<span class="badge badge-danger">Ditolak</span>';
			}
			$row[] = $status;
			
			$file_link = '-';
			if (!empty($p->pdf_surat_keterangan)) {
				$file_link = '<a href="'.$p->pdf_surat_keterangan.'" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-external-link"></i> Buka Link</a>';
			}
			$row[] = $file_link;

			if ($is_wadir2) {
				$btn_delete = '<a class="btn btn-sm btn-danger btn-xs" href="javascript:void(0)" onclick="delete_data(' . "'" . $p->id_pendampingan . "'" . ')"><i class="icon-trash"></i></a>';
				$row[] = $btn_delete;
			}

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->pendampingan->count_all($is_wadir2),
			"recordsFiltered" => $this->pendampingan->count_filtered($is_wadir2),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->pendampingan->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		if (!$this->_is_wadir2()) {
			echo json_encode(["status" => FALSE, "msg" => "Akses ditolak. Tambah data hanya untuk Wakil Direktur 2."]);
			return;
		}

		$this->_validate();

		$id_smt = $this->input->post('id_smt');
		$kategori = $this->input->post('kategori');
		$pdf_surat_keterangan = $this->input->post('pdf_surat_keterangan');
		$dosen_list_json = $this->input->post('dosen_list_json');

		$user_id = $this->ion_auth->user()->row()->id;
		$now = date('Y-m-d H:i:s');

		if (!empty($dosen_list_json)) {
			$dosen_list = json_decode($dosen_list_json, true);
			if (is_array($dosen_list) && count($dosen_list) > 0) {
				$saved_count = 0;
				foreach ($dosen_list as $d) {
					$target_id_ptk = !empty($d['id_ptk']) ? $d['id_ptk'] : null;
					$mhs_raw = $d['mahasiswa'] ?? [];
					$unique_mhs = [];
					foreach ($mhs_raw as $m) {
						$nim_key = !empty($m['nim']) ? $m['nim'] : trim($m['nama'] ?? '');
						if (!empty($nim_key) && !isset($unique_mhs[$nim_key])) {
							$unique_mhs[$nim_key] = $m;
						}
					}
					$mhs_list = array_values($unique_mhs);
					$jumlah = count($mhs_list);
					$mhs_json = json_encode($mhs_list);

					// Cek apakah sudah ada data dosen ini di semester & kategori ini
					$this->db->where('id_ptk', $target_id_ptk);
					$this->db->where('id_smt', $id_smt);
					$this->db->where('kategori', $kategori);
					$existing = $this->db->get('e_kinerja_pendampingan')->row();

					$data_save = [
						'id_smt' => $id_smt,
						'kategori' => $kategori,
						'jumlah' => $jumlah,
						'mahasiswa_json' => $mhs_json,
						'pdf_surat_keterangan' => $pdf_surat_keterangan,
						'approve_by_kaprodi' => 1,
						'id_ptk' => $target_id_ptk
					];

					if ($existing) {
						$data_save['change_by'] = $user_id;
						$data_save['change_date'] = $now;
						$this->pendampingan->update(['id_pendampingan' => $existing->id_pendampingan], $data_save);
					} else {
						$data_save['create_by'] = $user_id;
						$data_save['create_date'] = $now;
						$this->pendampingan->save($data_save);
					}
					$saved_count++;
				}
				echo json_encode(["status" => TRUE, "msg" => "Berhasil menyimpan data pendampingan untuk $saved_count dosen."]);
				return;
			}
		}

		$data = [
			'id_smt' => $id_smt,
			'kategori' => $kategori,
			'jumlah' => $this->input->post('jumlah'),
			'mahasiswa_json' => $this->input->post('mahasiswa_json'),
			'create_by' => $user_id,
			'create_date' => $now,
			'approve_by_kaprodi' => 1,
			'id_ptk' => $this->session->userdata('id_ptk'),
			'pdf_surat_keterangan' => $pdf_surat_keterangan
		];

		$this->pendampingan->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		if (!$this->_is_wadir2()) {
			echo json_encode(["status" => FALSE, "msg" => "Akses ditolak. Ubah data hanya untuk Wakil Direktur 2."]);
			return;
		}

		$id_pendampingan = $this->input->post('id_pendampingan');
		$row = $this->pendampingan->get_by_id($id_pendampingan);

		$this->_validate();

		$data = [
			'id_smt' => $this->input->post('id_smt'),
			'kategori' => $this->input->post('kategori'),
			'jumlah' => $this->input->post('jumlah'),
			'mahasiswa_json' => $this->input->post('mahasiswa_json'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s'),
			'approve_by_kaprodi' => 1,
			'id_ptk' => $this->session->userdata('id_ptk')
		];

		$data['pdf_surat_keterangan'] = $this->input->post('pdf_surat_keterangan');

		$this->pendampingan->update(['id_pendampingan' => $this->input->post('id_pendampingan')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		if (!$this->_is_wadir2()) {
			echo json_encode(["status" => FALSE, "msg" => "Akses ditolak. Hapus data hanya untuk Wakil Direktur 2."]);
			return;
		}

		$row = $this->pendampingan->get_by_id($id);
		// file di gdrive tidak perlu di-unlink dari server
		$this->pendampingan->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$id_smt = $this->input->post('id_smt');
		$kategori = $this->input->post('kategori');
		$id_pendampingan = $this->input->post('id_pendampingan');
		$dosen_list_json = $this->input->post('dosen_list_json');

		if ($id_smt == '') {
			$data['inputerror'][] = 'id_smt';
			$data['error_string'][] = 'Semester harus diisi';
			$data['status'] = FALSE;
		}

		if ($kategori == '') {
			$data['inputerror'][] = 'kategori';
			$data['error_string'][] = 'Kategori harus diisi';
			$data['status'] = FALSE;
		}

		if (empty($dosen_list_json) && $this->input->post('jumlah') == '') {
			$data['inputerror'][] = 'jumlah';
			$data['error_string'][] = 'Jumlah harus diisi';
			$data['status'] = FALSE;
		}

		$mahasiswa_json = $this->input->post('mahasiswa_json');
		if (!empty($mahasiswa_json)) {
			$mhs_array = json_decode($mahasiswa_json, true);
			if (is_array($mhs_array)) {
				foreach ($mhs_array as $k => $m) {
					$nim = trim($m['nim'] ?? '');
					
					if (!empty($nim)) {
						$this->db->select('m.nm_pd');
						$this->db->from('mahasiswa_pt pt');
						$this->db->join('mahasiswa m', 'pt.id_pd = m.id_pd');
						$this->db->where('pt.nipd', $nim);
						$cek = $this->db->get()->row();
						
						if (!$cek) {
							$mhs_array[$k]['nim'] = null;
						}
					}
				}
				// Update $_POST supaya data tersimpan dengan nim = null jika tidak ketemu
				$_POST['mahasiswa_json'] = json_encode($mhs_array);
			}
		}

		$surat_keterangan = $this->input->post('pdf_surat_keterangan');
		if (empty($surat_keterangan)) {
			$data['inputerror'][] = 'pdf_surat_keterangan';
			$data['error_string'][] = 'Link Surat Keterangan harus diisi';
			$data['status'] = FALSE;
		} else {
			$val = $this->_validate_gdrive_url($surat_keterangan);
			if (!$val['status']) {
				$data['inputerror'][] = 'pdf_surat_keterangan';
				$data['error_string'][] = $val['msg'];
				$data['status'] = FALSE;
			}
		}

		// Check for duplicate kategori in the same semester for this user
		if ($id_smt != '' && $kategori != '') {
			$user_id = $this->ion_auth->user()->row()->id;
			$this->db->where('create_by', $user_id);
			$this->db->where('id_smt', $id_smt);
			$this->db->where('kategori', $kategori);
			if (!empty($id_pendampingan)) {
				$this->db->where('id_pendampingan !=', $id_pendampingan);
			}
			$exist = $this->db->get('e_kinerja_pendampingan')->row();

			if ($exist) {
				$data['inputerror'][] = 'kategori';
				$data['error_string'][] = 'Kategori ini sudah Anda isi untuk semester yang dipilih.';
				$data['status'] = FALSE;
			}
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_gdrive_url($url)
	{
		if (empty($url)) {
			return ['status' => false, 'msg' => 'URL Kosong.'];
		}
		if (!str_contains($url, 'drive.google.com') && !str_contains($url, 'docs.google.com')) {
			return ['status' => false, 'msg' => 'Link harus dari Google Drive atau Google Docs'];
		}
		if (str_contains($url, '/folders/') || str_contains($url, '/drive/folders/')) {
			return ['status' => false, 'msg' => 'Link harus berupa file tunggal, bukan folder'];
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$html = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$final_url = $info['url'];
		
		if (str_contains($final_url, 'ServiceLogin') || str_contains($final_url, 'accounts.google.com')) {
			return ['status' => false, 'msg' => 'Akses ditolak. Pastikan akses link diatur menjadi "Anyone with the link can view".'];
		} else if (str_contains($html, '<title>Halaman Tidak Ditemukan</title>') || $info['http_code'] == 404) {
			return ['status' => false, 'msg' => 'Link Google Drive tidak valid atau file tidak ditemukan.'];
		} else {
			preg_match('/<title>(.*?)<\/title>/i', $html, $matches);
			$title = isset($matches[1]) ? strtolower($matches[1]) : '';
			
			$is_doc = (str_contains($url, 'docs.google.com/document/'));
			$has_valid_ext = (str_contains($title, '.pdf') || str_contains($title, '.doc') || str_contains($title, '.docx'));
			$is_image = (str_contains($title, '.png') || str_contains($title, '.jpg') || str_contains($title, '.jpeg'));

			if ($is_image) {
				return ['status' => false, 'msg' => 'File tidak boleh berupa gambar. Harus berupa PDF/DOC/DOCX.'];
			} else if (!$is_doc && !$has_valid_ext) {
				return ['status' => false, 'msg' => 'File harus berupa PDF, DOC, atau DOCX.'];
			}
		}

		return ['status' => true];
	}

	public function ajax_proses_sk_ai()
	{
		if (!$this->_is_wadir2()) {
			echo json_encode(['status' => false, 'msg' => 'Akses ditolak. Fitur ini hanya untuk Wakil Direktur 2.']);
			return;
		}

		$url_gdrive = $this->input->post('url_gdrive');
		$val = $this->_validate_gdrive_url($url_gdrive);
		if (!$val['status']) {
			echo json_encode($val);
			return;
		}

		// Convert GDrive link to direct download
		$file_id = '';
		if (preg_match('/(?:drive\.google\.com\/.*[?&]id=|drive\.google\.com\/file\/d\/)([\w-]{25,})/', $url_gdrive, $matches)) {
			$file_id = $matches[1];
		} else if (preg_match('/docs\.google\.com\/document\/d\/([\w-]{25,})/', $url_gdrive, $matches)) {
			$file_id = $matches[1];
		}

		if (empty($file_id)) {
			echo json_encode(['status' => false, 'msg' => 'Format URL Google Drive tidak dikenali.']);
			return;
		}

		// GDrive export URL
		if (str_contains($url_gdrive, 'docs.google.com/document')) {
			$download_url = "https://docs.google.com/document/d/{$file_id}/export?format=pdf";
		} else {
			$download_url = "https://drive.google.com/uc?export=download&id={$file_id}";
		}

		// Download file
		$ch = curl_init($download_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$pdf_content = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code != 200 || empty($pdf_content) || str_contains($pdf_content, '<html')) {
			echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh PDF. Pastikan akses file adalah Public/Anyone with the link.']);
			return;
		}

		$user_id = $this->ion_auth->user()->row()->id;
		$id_ptk = $this->session->userdata('id_ptk');
		
		$dosen = $this->db->get_where('siakad_dosen', ['id_ptk' => $id_ptk])->row();
		if ($dosen) {
			$nama_dosen = $dosen->nm_ptk;
		} else {
			$user = $this->ion_auth->user()->row();
			$nama_dosen = trim($user->first_name . ' ' . $user->last_name);
		}

		// Extract Text
		$parser = new \Smalot\PdfParser\Parser();
		try {
			$pdf = $parser->parseContent($pdf_content);
			$text = $pdf->getText();
		} catch (Exception $e) {
			$text = '';
		}

		// Get Settings
		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		$provider = $api_setting->ai_provider ?? 'gemini';
		$api_key = $api_setting->api_key ?? '';

		$prompt = "Tugas: Analisis Dokumen Surat Keputusan (SK) Pendampingan / Kuliah Kerja Nyata (KKN) / Praktek Kerja Lapangan (PKL) / Magang.\n\n";
		$prompt .= "Instruksi Ekstraksi:\n";
		$prompt .= "1. Identifikasi Kategori kegiatan dari SK ini: Pilih SATU yang paling cocok: 'KKN' atau 'PKL'. Jika Kuliah Kerja Nyata, pilih 'KKN'. Jika Praktek Kerja Lapangan / Magang / Internship, pilih 'PKL'.\n";
		$prompt .= "2. Ekstrak keterangan Semester dan Tahun Akademik yang tertera pada dokumen. Kembalikan dalam format ID Semester (misal: '20251' untuk Ganjil 2025/2026, '20252' untuk Genap 2025/2026). Jika tidak yakin, kembalikan teks semesternya apa adanya.\n";
		$prompt .= "3. Periksa apakah dokumen ini sah dan memiliki penetapan/tanda tangan Direktur Politeknik Wilmar Bisnis Indonesia (atau bagian penetapan/keputusan pimpinan di akhir SK). Set is_valid ke true jika valid, false jika bukan SK resmi/tidak ada penetapan.\n";
		$prompt .= "4. Identifikasi SELURUH Dosen Pembimbing yang tercantum di dalam SK (baik di batang tubuh SK maupun pada tabel/lampiran SK). PENTING: JANGAN HANYA MENGAMBIL SATU DOSEN, EKSTRAK SEMUA DOSEN PEMBIMBING YANG ADA DI DALAM SK BESERTA MAHASISWA BIMBINGANNYA MASING-MASING!\n";
		$prompt .= "5. Untuk SETIAP Dosen Pembimbing yang ditemukan, ekstrak:\n";
		$prompt .= "   - nama_dosen: Nama lengkap dosen beserta gelar seperti yang tertulis pada SK/lampiran.\n";
		$prompt .= "   - mahasiswa: Daftar seluruh mahasiswa (NIM dan Nama) yang dibimbing oleh dosen tersebut. PENTING: NIM mahasiswa selalu berupa digit angka (kurang lebih 10 digit). Jika terdapat kesalahan OCR (seperti huruf 'O' atau 'l'), koreksi menjadi angka yang sesuai ('0', '1', dsb).\n";
		$prompt .= "   - jumlah_mahasiswa: Total jumlah mahasiswa bimbingan dosen tersebut.\n";
		$prompt .= "6. Berikan 'reason' singkat mengenai hasil validasi dokumen dan ringkasan dosen yang ditemukan.\n\n";
		$prompt .= "Output WAJIB berupa format JSON murni berikut:\n";
		$prompt .= "{\n";
		$prompt .= "  \"kategori\": \"KKN\",\n";
		$prompt .= "  \"semester_teks\": \"Semester Genap T.A. 2025/2026\",\n";
		$prompt .= "  \"id_smt\": \"20252\",\n";
		$prompt .= "  \"is_valid\": true,\n";
		$prompt .= "  \"reason\": \"Dokumen SK KKN disahkan oleh Direktur, ditemukan 4 Dosen Pembimbing.\",\n";
		$prompt .= "  \"dosen_list\": [\n";
		$prompt .= "    {\n";
		$prompt .= "      \"nama_dosen\": \"Nama Dosen Lengkap Beserta Gelar\",\n";
		$prompt .= "      \"jumlah_mahasiswa\": 12,\n";
		$prompt .= "      \"mahasiswa\": [\n";
		$prompt .= "        {\"nim\": \"2201010010\", \"nama\": \"NAMA MAHASISWA 1\"},\n";
		$prompt .= "        {\"nim\": \"2201010032\", \"nama\": \"NAMA MAHASISWA 2\"}\n";
		$prompt .= "      ]\n";
		$prompt .= "    }\n";
		$prompt .= "  ]\n";
		$prompt .= "}";

		if (strlen(trim($text)) < 50) {
			// This is likely a scanned PDF
			if ($provider !== 'gemini') {
				echo json_encode(['status' => false, 'msg' => 'PDF terdeteksi sebagai hasil scan atau gambar murni. Provider ' . strtoupper($provider) . ' tidak mendukung pembacaan dokumen scan tanpa teks. Harap ganti AI Provider ke Google Gemini pada menu Pengaturan, lalu coba lagi.']);
				return;
			}
			
			// Send to Gemini using inlineData
			$payload = [
				'contents' => [
					['parts' => [
						['text' => $prompt],
						['inlineData' => [
							'mimeType' => 'application/pdf',
							'data' => base64_encode($pdf_content)
						]]
					]]
				],
				'generationConfig' => [
					'temperature' => 0.2,
					'responseMimeType' => 'application/json'
				]
			];
			
			$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $api_key;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 90);
			$response = curl_exec($ch);
			curl_close($ch);
			
			$result = json_decode($response, true);
			$ai_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
			
		} else {
			// PDF is text-based, use Ai_reviewer
			$this->load->library('Ai_reviewer');
			$ai_result = $this->ai_reviewer->generate_review($provider, $api_key, $prompt, "DOKUMEN PDF:\n" . substr($text, 0, 60000), false, true);
			
			if (!$ai_result['status']) {
				echo json_encode(['status' => false, 'msg' => $ai_result['msg']]);
				return;
			}
			$ai_text = $ai_result['review'];
		}
		
		if (empty($ai_text)) {
			$error_detail = '';
			if (isset($result) && isset($result['error']['message'])) {
				$error_detail = ' (API Error: ' . $result['error']['message'] . ')';
			} else if (isset($ai_result) && !$ai_result['status']) {
				$error_detail = ' (' . $ai_result['msg'] . ')';
			}
			echo json_encode(['status' => false, 'msg' => 'Gagal mendapatkan response dari AI.' . $error_detail]);
			return;
		}

		$ai_text = trim($ai_text);
		$ai_text = preg_replace('/```json\s*/', '', $ai_text);
		$ai_text = preg_replace('/```\s*/', '', $ai_text);
		
		$start_pos = strpos($ai_text, '{');
		$end_pos = strrpos($ai_text, '}');
		if ($start_pos !== false && $end_pos !== false) {
			$ai_text = substr($ai_text, $start_pos, $end_pos - $start_pos + 1);
		}
		
		$parsed = json_decode($ai_text, true);
		if (!$parsed) {
			echo json_encode(['status' => false, 'msg' => 'Gagal parsing JSON dari AI.']);
			return;
		}

		// Normalize dosen_list structure
		$dosen_list = [];
		if (isset($parsed['dosen_list']) && is_array($parsed['dosen_list']) && count($parsed['dosen_list']) > 0) {
			$dosen_list = $parsed['dosen_list'];
		} else if (isset($parsed['mahasiswa']) && is_array($parsed['mahasiswa'])) {
			// Single dosen fallback
			$dosen_list[] = [
				'nama_dosen' => $nama_dosen,
				'jumlah_mahasiswa' => count($parsed['mahasiswa']),
				'mahasiswa' => $parsed['mahasiswa']
			];
		}

		// Load all lecturers for matching
		$this->db->select('sd.id_ptk, sd.nm_ptk, u.id as user_id');
		$this->db->from('siakad_dosen sd');
		$this->db->join('users u', 'u.id_ptk = sd.id_ptk', 'left');
		$this->db->where('sd.nm_ptk !=', '');
		$all_dosen_db = $this->db->get()->result();

		$processed_dosen = [];
		$total_mahasiswa_all = 0;

		foreach ($dosen_list as $d) {
			$raw_nama = trim($d['nama_dosen'] ?? '');
			$matched_dosen = $this->_match_dosen($raw_nama, $all_dosen_db);

			$target_id_ptk = $matched_dosen ? $matched_dosen->id_ptk : null;
			$target_nm_ptk = $matched_dosen ? $matched_dosen->nm_ptk : $raw_nama;
			$target_user_id = $matched_dosen ? $matched_dosen->user_id : null;

			$mhs_list = isset($d['mahasiswa']) && is_array($d['mahasiswa']) ? $d['mahasiswa'] : [];

			// AUTO-CORRECTION OCR / AI HALLUCINATION for students
			foreach ($mhs_list as &$m) {
				$nim = preg_replace('/[^0-9]/', '', $m['nim'] ?? '');
				$nama = trim($m['nama'] ?? '');
				
				$this->db->select('pt.nipd, m.nm_pd');
				$this->db->from('mahasiswa_pt pt');
				$this->db->join('mahasiswa m', 'pt.id_pd = m.id_pd');
				$this->db->where('pt.nipd', $nim);
				$cek = $this->db->get()->row();
				
				if (!$cek && !empty($nama)) {
					// Fallback: search by exact name
					$this->db->select('pt.nipd, m.nm_pd');
					$this->db->from('mahasiswa_pt pt');
					$this->db->join('mahasiswa m', 'pt.id_pd = m.id_pd');
					$this->db->where('m.nm_pd', $nama);
					$cek_nama = $this->db->get()->row();
					if ($cek_nama) {
						$m['nim'] = $cek_nama->nipd;
						$m['nama'] = $cek_nama->nm_pd;
					} else {
						// Fallback: search by LIKE
						$this->db->select('pt.nipd, m.nm_pd');
						$this->db->from('mahasiswa_pt pt');
						$this->db->join('mahasiswa m', 'pt.id_pd = m.id_pd');
						$this->db->like('m.nm_pd', $nama);
						$cek_like = $this->db->get()->row();
						if ($cek_like) {
							$m['nim'] = $cek_like->nipd;
							$m['nama'] = $cek_like->nm_pd;
						} else {
							$words = explode(' ', $nama);
							if (count($words) >= 2) {
								$this->db->select('pt.nipd, m.nm_pd');
								$this->db->from('mahasiswa_pt pt');
								$this->db->join('mahasiswa m', 'pt.id_pd = m.id_pd');
								$this->db->like('m.nm_pd', $words[0] . ' ' . $words[1]);
								$cek_like2 = $this->db->get()->row();
								if ($cek_like2) {
									$m['nim'] = $cek_like2->nipd;
									$m['nama'] = $cek_like2->nm_pd;
								}
							}
						}
					}
				} else if ($cek) {
					$m['nim'] = $cek->nipd;
					$m['nama'] = $cek->nm_pd;
				}
			}
			unset($m);

			// Deduplicate students by NIM / Nama per lecturer
			$unique_mhs = [];
			foreach ($mhs_list as $m) {
				$nim_key = !empty($m['nim']) ? $m['nim'] : trim($m['nama'] ?? '');
				if (!empty($nim_key) && !isset($unique_mhs[$nim_key])) {
					$unique_mhs[$nim_key] = $m;
				}
			}
			$mhs_list = array_values($unique_mhs);

			$jml_mhs = count($mhs_list);
			$total_mahasiswa_all += $jml_mhs;

			$processed_dosen[] = [
				'nama_dosen' => $raw_nama,
				'id_ptk' => $target_id_ptk,
				'nm_ptk' => $target_nm_ptk,
				'user_id' => $target_user_id,
				'matched' => !empty($target_id_ptk),
				'jumlah_mahasiswa' => $jml_mhs,
				'mahasiswa' => $mhs_list
			];
		}

		$semesters = $this->db->get('semester')->result();
		$matched_smt = '';
		$id_smt_ai = $parsed['id_smt'] ?? '';
		foreach ($semesters as $s) {
			if ($s->id_smt == $id_smt_ai) {
				$matched_smt = $s->id_smt;
				break;
			}
		}

		if (empty($matched_smt) && isset($parsed['semester_teks'])) {
			$txt = strtolower($parsed['semester_teks']);
			foreach ($semesters as $s) {
				$db_smt = strtolower((string)$s->nm_smt);
				if (str_contains($txt, $db_smt) || str_contains($db_smt, $txt)) {
					$matched_smt = $s->id_smt;
					break;
				}
				
				// Smart match if wording is reversed (e.g. "Genap 2024/2025" vs "2024/2025 Genap")
				$year_match = false;
				if (preg_match('/\d{4}\/\d{4}/', $db_smt, $m)) {
					if (str_contains($txt, $m[0])) $year_match = true;
				}
				
				$type_match = false;
				if (str_contains($db_smt, 'ganjil') && str_contains($txt, 'ganjil')) $type_match = true;
				if (str_contains($db_smt, 'genap') && str_contains($txt, 'genap')) $type_match = true;
				if (str_contains($db_smt, 'pendek') && str_contains($txt, 'pendek')) $type_match = true;
				
				if ($year_match && $type_match) {
					$matched_smt = $s->id_smt;
					break;
				}
			}
		}
		$kategori_ai = $parsed['kategori'] ?? '';

		echo json_encode([
			'status' => true,
			'id_smt' => $matched_smt,
			'semester_teks' => $parsed['semester_teks'] ?? '',
			'kategori' => $kategori_ai,
			'total_dosen' => count($processed_dosen),
			'total_mahasiswa' => $total_mahasiswa_all,
			'is_valid' => $parsed['is_valid'] ?? false,
			'reason' => $parsed['reason'] ?? '',
			'dosen_list' => $processed_dosen
		]);
	}

	private function _clean_name_tokens($name)
	{
		$name = str_replace(['.', ',', '-', '/', '(', ')', ':'], ' ', $name);
		$titles = [
			'dr', 'dra', 'drs', 'ir', 'prof', 'se', 'msi', 'mkom', 'skom', 'mm', 'sp', 'meng', 'phd', 'mpd', 'spd',
			'mti', 'sti', 'mt', 'st', 'msn', 'ssn', 'msc', 'ssc', 'ba', 'ma', 'mba', 'ms', 'mma', 'ak', 'ca', 's', 'm', 'kom', 'si', 't', 'pd', 'eng', 'sn', 'sc', 'dsn'
		];
		$words = explode(' ', strtolower($name));
		$filtered = [];
		foreach ($words as $w) {
			$w = trim(preg_replace('/[^a-z]/', '', $w));
			if (!empty($w) && !in_array($w, $titles) && strlen($w) > 2) {
				$filtered[] = $w;
			}
		}
		return $filtered;
	}

	private function _match_dosen($nama_sk, $all_dosen)
	{
		$tokens_sk = $this->_clean_name_tokens($nama_sk);
		$str_sk = implode(' ', $tokens_sk);

		if (empty($tokens_sk)) return null;

		$best_match = null;
		$max_score = 0;

		foreach ($all_dosen as $d) {
			$tokens_db = $this->_clean_name_tokens($d->nm_ptk);
			$str_db = implode(' ', $tokens_db);

			if ($str_sk === $str_db && !empty($str_sk)) {
				return $d;
			}

			$intersect = array_intersect($tokens_sk, $tokens_db);
			$score = count($intersect);

			if ($score >= 2 && $score > $max_score) {
				$max_score = $score;
				$best_match = $d;
			} elseif ($score === 1 && $max_score < 1) {
				$matched_token = reset($intersect);
				if (strlen($matched_token) >= 4) {
					$max_score = 1;
					$best_match = $d;
				}
			}
		}

		return $best_match;
	}

	private function _is_wadir2()
	{
		$user = $this->ion_auth->user()->row();
		if (!$user) return false;
		$user_login_email = $user->email ?? '';
		$user_login_uname = $user->username ?? '';
		
		return ($user_login_email === 'ferawati.zhang@wbi.ac.id'
			|| $user_login_uname === 'ferawati.zhang@wbi.ac.id'
			|| $this->db->group_start()
				->where('username', $user_login_email)
				->or_where('username', $user_login_uname)
				->group_end()
				->where('roles', 'wadir2')
				->get('siakad_akses_data_center')->num_rows() > 0);
	}

}


