<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_fasilitator extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_fasilitator_model', 'fasilitator');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		// Only Wadir 2 can access
		if (!$this->_is_wadir2()) {
			redirect('auth/logout', 'refresh');
		}
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

	public function index()
	{
		$output['judul_besar'] 		= 'Data Fasilitator';
		$output['judul_kecil'] 		= 'Manajemen Fasilitator Entre';
		$output['menu_aktif'] 		= 'm_fasilitator';
		$output['parent_kinerja']	= 'active open';
		$output['content'] 			= 'fasilitator/v_fasilitator';
		$output['after_page'] 		= 'fasilitator/v_fasilitator_after_page';
		
		// Load semesters for dropdown
		$this->db->order_by('id_smt', 'desc');
		$output['dd_semester'] = $this->db->get('semester')->result();

        // Load active lecturers for manual add
        $this->db->where('id_stat_aktif', 'A');
        $this->db->order_by('nm_ptk', 'asc');
        $output['dd_dosen'] = $this->db->get('siakad_dosen')->result();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->fasilitator->get_datatables();
		$data = [];
		$no = $_POST['start'] ?? 0;
		foreach ($list as $p) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $p->nm_ptk;
			$row[] = $p->nm_smt;
			$row[] = $p->jumlah;
			
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

			$btn_edit = '<a class="btn btn-sm btn-primary btn-xs" href="javascript:void(0)" onclick="edit_data(' . "'" . $p->id_pendampingan . "'" . ')"><i class="icon-pencil"></i></a>';
			$btn_delete = '<a class="btn btn-sm btn-danger btn-xs" href="javascript:void(0)" onclick="delete_data(' . "'" . $p->id_pendampingan . "'" . ')"><i class="icon-trash"></i></a>';
			
			$row[] = $btn_edit . ' ' . $btn_delete;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'] ?? 0,
			"recordsTotal" => $this->fasilitator->count_all(),
			"recordsFiltered" => $this->fasilitator->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->fasilitator->get_by_id($id);
		echo json_encode($data);
	}

    private function _validate_manual()
    {
        $data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

        if ($this->input->post('id_smt') == '') {
			$data['inputerror'][] = 'id_smt';
			$data['error_string'][] = 'Semester harus diisi';
			$data['status'] = FALSE;
		}

        if ($this->input->post('id_ptk') == '') {
			$data['inputerror'][] = 'id_ptk';
			$data['error_string'][] = 'Dosen harus dipilih';
			$data['status'] = FALSE;
		}

        if ($this->input->post('jumlah') == '') {
			$data['inputerror'][] = 'jumlah';
			$data['error_string'][] = 'Jumlah Tim harus diisi';
			$data['status'] = FALSE;
		}

        $surat_keterangan = $this->input->post('pdf_surat_keterangan');
        if (empty($surat_keterangan)) {
            $data['inputerror'][] = 'pdf_surat_keterangan';
            $data['error_string'][] = 'Link G-Drive harus diisi';
            $data['status'] = FALSE;
        } else {
            $val = $this->_validate_gdrive_url($surat_keterangan);
            if (!$val['status']) {
                $data['inputerror'][] = 'pdf_surat_keterangan';
                $data['error_string'][] = $val['msg'];
                $data['status'] = FALSE;
            }
        }

        if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
    }

	public function ajax_add()
	{
		$this->_validate_manual();
        $id_smt = $this->input->post('id_smt');
        $id_ptk = $this->input->post('id_ptk');

        // Check exist
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_ptk', $id_ptk);
        $this->db->where('kategori', 'Fasilitator Entre');
        $exist = $this->db->get('e_kinerja_pendampingan')->row();

        if ($exist) {
            echo json_encode(['status' => false, 'error_string' => ['Dosen ini sudah ditambahkan sebagai Fasilitator pada semester yang dipilih.'], 'inputerror' => ['id_ptk']]);
            exit();
        }

		$data = [
			'id_smt' => $id_smt,
			'kategori' => 'Fasilitator Entre',
			'jumlah' => $this->input->post('jumlah'),
			'id_ptk' => $id_ptk,
            'approve_by_kaprodi' => 1,
			'pdf_surat_keterangan' => $this->input->post('pdf_surat_keterangan'),
			'create_by' => $this->ion_auth->user()->row()->id,
			'create_date' => date('Y-m-d H:i:s'),
            'mahasiswa_json' => ''
		];

		$this->fasilitator->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
        // Only update jumlah
        $data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

        if ($this->input->post('jumlah') == '') {
			$data['inputerror'][] = 'jumlah';
			$data['error_string'][] = 'Jumlah Tim harus diisi';
			$data['status'] = FALSE;
		}

        if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}

		$data_update = [
			'jumlah' => $this->input->post('jumlah')
		];

		$this->fasilitator->update(['id_pendampingan' => $this->input->post('id_pendampingan')], $data_update);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->fasilitator->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
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

    public function ajax_proses_fasilitator_ai()
	{
		$url_gdrive = $this->input->post('url_gdrive');
		$val = $this->_validate_gdrive_url($url_gdrive);
		if (!$val['status']) {
			echo json_encode($val);
			return;
		}

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

		if (str_contains($url_gdrive, 'docs.google.com/document')) {
			$download_url = "https://docs.google.com/document/d/{$file_id}/export?format=pdf";
		} else {
			$download_url = "https://drive.google.com/uc?export=download&id={$file_id}";
		}

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

		$id_ptk = $this->session->userdata('id_ptk');
		
		$parser = new \Smalot\PdfParser\Parser();
		try {
			$pdf = $parser->parseContent($pdf_content);
			$text = $pdf->getText();
		} catch (Exception $e) {
			$text = '';
		}

		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		$provider = $api_setting->ai_provider ?? 'gemini';
		$api_key = $api_setting->api_key ?? '';

		$prompt = "Tugas: Analisis SK Fasilitator.\n";
		$prompt .= "Instruksi Ekstraksi:\n";
		$prompt .= "1. Ekstrak keterangan Semester dan Tahun Akademik yang tertera pada dokumen. Kembalikan dalam format ID Semester (misal: '20251' untuk Ganjil 2025/2026, '20252' untuk Genap 2025/2026). Jika tidak yakin, kembalikan teks semesternya apa adanya.\n";
		$prompt .= "2. Ekstrak daftar nama dosen yang ditugaskan sebagai fasilitator dari tabel di dalam SK.\n\n";
		$prompt .= "Output WAJIB berupa JSON dengan format berikut:\n";
		$prompt .= "{\n";
		$prompt .= "  \"semester_teks\": \"Semester Genap T.A. 2025/2026\",\n";
		$prompt .= "  \"id_smt\": \"20252\",\n";
		$prompt .= "  \"dosen\": [\"Nama Dosen 1\", \"Nama Dosen 2\"]\n";
		$prompt .= "}";

		if (strlen(trim($text)) < 50) {
			if ($provider !== 'gemini') {
				echo json_encode(['status' => false, 'msg' => 'PDF terdeteksi sebagai hasil scan atau gambar murni. Provider ' . strtoupper($provider) . ' tidak mendukung pembacaan dokumen scan tanpa teks.']);
				return;
			}
			
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
			$this->load->library('Ai_reviewer');
			$ai_result = $this->ai_reviewer->generate_review($provider, $api_key, $prompt, "DOKUMEN PDF:\n" . substr($text, 0, 50000), false, true);
			if (!$ai_result['status']) {
				echo json_encode(['status' => false, 'msg' => $ai_result['msg']]);
				return;
			}
			$ai_text = $ai_result['review'];
		}
		
		if (empty($ai_text)) {
			echo json_encode(['status' => false, 'msg' => 'Gagal mendapatkan response dari AI.']);
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

		if (empty($matched_smt)) {
			echo json_encode(['status' => false, 'msg' => 'Gagal menemukan semester yang cocok.']);
			return;
		}

		$dosen_list = $parsed['dosen'] ?? [];
		if (empty($dosen_list)) {
			echo json_encode(['status' => false, 'msg' => 'Tidak ada nama dosen yang ditemukan.']);
			return;
		}

		$inserted = 0;
		$user_id = $this->ion_auth->user()->row()->id;

		foreach ($dosen_list as $nama) {
			// clean name from gelar
			$nama_bersih = preg_replace('/\,\s*[a-zA-Z\.\s]+$/', '', $nama);
            $nama_bersih = preg_replace('/^[a-zA-Z\.\s]+\.\s*/', '', $nama_bersih);
			$parts = explode(',', $nama_bersih);
			$nama_bersih = trim($parts[0]);
			
            // Use LIKE with wildcards to find similar names
			$nama_words = explode(' ', $nama_bersih);
			$like_condition = [];
			foreach ($nama_words as $word) {
				if (strlen($word) > 2) {
					$like_condition[] = "nm_ptk LIKE '%" . $this->db->escape_like_str($word) . "%'";
				}
			}
			
			if (empty($like_condition)) {
				$like_condition[] = "nm_ptk LIKE '%" . $this->db->escape_like_str($nama_bersih) . "%'";
			}

			$this->db->where(implode(' AND ', $like_condition));
            $ptk = $this->db->get('siakad_dosen')->row();

            if ($ptk) {
                // Check duplicate
                $this->db->where('id_smt', $matched_smt);
                $this->db->where('id_ptk', $ptk->id_ptk);
                $this->db->where('kategori', 'Fasilitator Entre');
                $exist = $this->db->get('e_kinerja_pendampingan')->row();

                if (!$exist) {
                    $data = [
                        'id_smt' => $matched_smt,
                        'kategori' => 'Fasilitator Entre',
                        'jumlah' => 0,
                        'approve_by_kaprodi' => 1,
                        'id_ptk' => $ptk->id_ptk,
                        'pdf_surat_keterangan' => $url_gdrive,
                        'create_by' => $user_id,
                        'create_date' => date('Y-m-d H:i:s'),
                        'mahasiswa_json' => ''
                    ];
                    $this->db->insert('e_kinerja_pendampingan', $data);
                    $inserted++;
                }
            }
		}

		echo json_encode([
			'status' => true,
			'msg' => "Berhasil memproses SK. $inserted dosen ditambahkan sebagai Fasilitator Entre."
		]);
	}

	public function ajax_import_excel()
	{
		if (!$this->_is_wadir2()) {
			echo json_encode(['status' => false, 'msg' => 'Akses ditolak. Fitur ini hanya untuk Wakil Direktur 2.']);
			return;
		}

		if (empty($_FILES['file_excel']['name'])) {
			echo json_encode(['status' => false, 'msg' => 'Silakan pilih file Excel (.xlsx / .xls / .csv) terlebih dahulu.']);
			return;
		}

		$file_tmp = $_FILES['file_excel']['tmp_name'];
		$file_ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));

		if (!in_array($file_ext, ['xlsx', 'xls', 'csv'])) {
			echo json_encode(['status' => false, 'msg' => 'Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv.']);
			return;
		}

		try {
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray(null, true, true, true);
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'msg' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
			return;
		}

		if (empty($rows) || count($rows) < 2) {
			echo json_encode(['status' => false, 'msg' => 'File Excel kosong atau tidak memiliki data baris.']);
			return;
		}

		$fallback_id_smt = $this->input->post('id_smt_fallback');
		$user_id = $this->ion_auth->user()->row()->id;
		$now = date('Y-m-d H:i:s');

		// Load all lecturers from siakad_dosen
		$all_dosen = $this->db->select('id_ptk, nm_ptk')->where('nm_ptk !=', '')->get('siakad_dosen')->result();

		$success_count = 0;
		$updated_count = 0;
		$failed_count = 0;
		$details = [];

		// Default column mapping (Column A = nm_ptk, Column B = total, Column C = id_smt)
		$col_nama = 'A';
		$col_total = 'B';
		$col_smt = 'C';

		$start_row = 1;
		// Check row 1 for header mapping
		if (!empty($rows[1])) {
			$header_row = $rows[1];
			$has_header = false;
			foreach ($header_row as $col_letter => $header_val) {
				$val = strtolower(trim($header_val ?? ''));
				if (empty($val)) continue;

				if (str_contains($val, 'nm_ptk') || str_contains($val, 'nama') || str_contains($val, 'dosen') || str_contains($val, 'fasilitator') || str_contains($val, 'ptk')) {
					$col_nama = $col_letter;
					$has_header = true;
				} elseif (str_contains($val, 'total') || str_contains($val, 'jumlah') || str_contains($val, 'tim')) {
					$col_total = $col_letter;
					$has_header = true;
				} elseif (str_contains($val, 'id_smt') || str_contains($val, 'smt') || str_contains($val, 'semester')) {
					$col_smt = $col_letter;
					$has_header = true;
				}
			}

			if ($has_header) {
				$start_row = 2; // skip row 1
			}
		}

		$row_index = 0;
		foreach ($rows as $r_num => $row) {
			$row_index++;
			if ($row_index < $start_row) {
				continue;
			}

			$nama_fasilitator = trim($row[$col_nama] ?? '');
			if (empty($nama_fasilitator)) {
				continue;
			}

			$total_raw = trim($row[$col_total] ?? '0');
			$total = is_numeric($total_raw) ? (int)$total_raw : 0;

			$id_smt = trim($row[$col_smt] ?? '');
			if (empty($id_smt)) {
				$id_smt = $fallback_id_smt;
			}

			if (empty($id_smt)) {
				$failed_count++;
				$details[] = [
					'nama' => $nama_fasilitator,
					'status' => 'failed',
					'msg' => 'ID Semester tidak ditemukan pada baris atau form.'
				];
				continue;
			}

			$matched = $this->_match_dosen($nama_fasilitator, $all_dosen);
			if (!$matched) {
				$failed_count++;
				$details[] = [
					'nama' => $nama_fasilitator,
					'status' => 'failed',
					'msg' => 'Nama dosen tidak cocok di database siakad_dosen.'
				];
				continue;
			}

			$id_ptk = $matched->id_ptk;
			$nm_ptk = $matched->nm_ptk;

			// Check existing
			$this->db->where('id_ptk', $id_ptk);
			$this->db->where('id_smt', $id_smt);
			$this->db->where('kategori', 'Fasilitator Entre');
			$exist = $this->db->get('e_kinerja_pendampingan')->row();

			if ($exist) {
				$this->db->where('id_pendampingan', $exist->id_pendampingan)->update('e_kinerja_pendampingan', [
					'jumlah' => $total,
					'approve_by_kaprodi' => 1,
					'change_by' => $user_id,
					'change_date' => $now
				]);
				$updated_count++;
				$details[] = [
					'nama' => $nama_fasilitator,
					'matched_to' => $nm_ptk,
					'id_smt' => $id_smt,
					'jumlah' => $total,
					'status' => 'updated',
					'msg' => 'Data diperbarui (' . $exist->jumlah . ' -> ' . $total . ' Tim).'
				];
			} else {
				$this->db->insert('e_kinerja_pendampingan', [
					'id_smt' => $id_smt,
					'kategori' => 'Fasilitator Entre',
					'jumlah' => $total,
					'id_ptk' => $id_ptk,
					'approve_by_kaprodi' => 1,
					'pdf_surat_keterangan' => '',
					'mahasiswa_json' => '',
					'create_by' => $user_id,
					'create_date' => $now
				]);
				$success_count++;
				$details[] = [
					'nama' => $nama_fasilitator,
					'matched_to' => $nm_ptk,
					'id_smt' => $id_smt,
					'jumlah' => $total,
					'status' => 'inserted',
					'msg' => 'Data baru berhasil disimpan (' . $total . ' Tim).'
				];
			}
		}

		$total_processed = $success_count + $updated_count;
		echo json_encode([
			'status' => true,
			'total_processed' => $total_processed,
			'success_count' => $success_count,
			'updated_count' => $updated_count,
			'failed_count' => $failed_count,
			'details' => $details,
			'msg' => "Proses impor selesai! $success_count data baru ditambahkan, $updated_count data diperbarui, $failed_count data tidak cocok."
		]);
	}

	private function _clean_name_tokens($name)
	{
		$name = str_replace(['.', ',', '-', '/', '(', ')', ':'], ' ', $name);
		$titles = [
			'dr', 'dra', 'drs', 'ir', 'prof', 'se', 'msi', 'mkom', 'skom', 'mm', 'sp', 'meng', 'phd', 'mpd', 'spd',
			'mti', 'sti', 'mt', 'st', 'msn', 'ssn', 'msc', 'ssc', 'ba', 'ma', 'mba', 'ms', 'mma', 'ak', 'ca', 's', 'm',
			'kom', 'si', 't', 'pd', 'eng', 'sn', 'sc', 'dsn', 'str', 'strm', 'sst', 'msm', 'sos', 'hp', 'psi', 'hum'
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
		$nama_sk_clean = trim($nama_sk);
		if (empty($nama_sk_clean)) return null;

		// 1. Direct exact match
		foreach ($all_dosen as $d) {
			if (strcasecmp(trim((string)$d->nm_ptk), $nama_sk_clean) === 0) {
				return $d;
			}
		}

		// 2. Direct match without degree (first segment before comma)
		$base_sk = trim(explode(',', $nama_sk_clean)[0]);
		if (!empty($base_sk)) {
			foreach ($all_dosen as $d) {
				$base_db = trim(explode(',', $d->nm_ptk)[0]);
				if (strcasecmp($base_sk, $base_db) === 0) {
					return $d;
				}
			}
		}

		// 3. Token-based matching
		$tokens_sk = $this->_clean_name_tokens($nama_sk_clean);
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
}
