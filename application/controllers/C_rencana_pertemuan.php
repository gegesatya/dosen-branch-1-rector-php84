<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_rencana_pertemuan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rencana_pertemuan_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function course($slug = "", $course_id = "")
	{
		$page_data['course_id'] = $course_id;
		$page_data['page_name'] = "course_page";
		$page_data['page_title'] = site_phrase('course');

		$this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
	}

	public function index($id_jadwal = NULL)
	{
		if (empty($id_jadwal)) {
			redirect('C_jadwal', 'refresh');
			return;
		}

		if (SELF::authorization($id_jadwal)) {
			//$this->session->set_userdata('id_kls_session', $id_jadwal);
			$jadwal = $this->db->where('id', $id_jadwal)->from('siakad_jadwal j')->join('siakad_hari h', 'h.uniq = j.id_hari')->get()->row();
			$meet = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->result();
			
			// 1. Get class & course info from siakad_jadwal_detail and feeder_kelas_kuliah
			$jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();
			$id_kls = $jadwal_detail ? $jadwal_detail->id_kls : null;
			$kelas_kuliah = $id_kls ? $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row() : null;

			$id_sdm  = $jadwal->id_sdm;
			$nm_mk   = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : trim((string)$jadwal->tag_name);
			$kode_mk = $kelas_kuliah ? trim((string)$kelas_kuliah->kode_mk_1) : '';
			$id_mk   = $kelas_kuliah ? $kelas_kuliah->id_mk : '';

			// 2. Lookup in siakad_rps_obe (RPS OBE) prioritizing current lecturer & course
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

			// 3. Fallback to legacy siakad_new_rps_online if no OBE RPS exists
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

			$i_realisasi = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();
			$i_pertemuan = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->num_rows();
			$jumlah_mahasiswa = $this->db->where(compact('id_jadwal'))->get('siakad_nilai_baru')->num_rows();
			// $next_meet = $this->db->where(['id_jadwal' => $id_jadwal, 'tanggal_rencana >=' => date('Y-m-d')])->order_by('tanggal_rencana')->get('siakad_rencana_pertemuan');

			$output['judul_besar'] = $jadwal->tag_name;
			$output['judul_kecil'] = $jadwal->nama_hari . ', ' . $jadwal->jam_mulai . ' sd ' . $jadwal->jam_selesai;
			$output['meet'] = $meet;
			$output['content'] = 'rencana_pertemuan/rencana_pertemuan_view';
			$output['after_page'] = 'rencana_pertemuan/rencana_pertemuan_after_page';
			$output['judul'] = 'Virtual Class ' . $jadwal->tag_name;
			$output['i_realisasi'] = $i_realisasi;
			$output['i_pertemuan'] = $i_pertemuan;
			$output['jumlah_mahasiswa'] = $jumlah_mahasiswa;
			$output['creator'] = $creator;
			$output['rps'] = $rps;

			$output['is_generate_bap'] = $jadwal->is_generate_bap;
			// if ($next_meet->num_rows()) {
			// 	$output['next_meet'] = $next_meet->first_row()->tanggal_rencana;
			// } else {
			// 	$output['next_meet'] = '-';
			// }

			if ($jadwal->is_skema) {
				$output['skema'] = 'Sudah isi';
			} else {
				$output['skema'] = 'Belum isi';
			}

			$output['id_jadwal'] = $id_jadwal;

			$this->load->view('master_page_elite', $output);
		} else {
			echo "Anda tidak bisa mengakses kelas ini.";
		}
	}

	private function authorization($id_jadwal)
	{
		$id_logged_in_user = $this->ion_auth->user()->row();
		$id_ptk_user = $id_logged_in_user->id_ptk;

		$jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

		if ($jadwal->id_sdm === $id_ptk_user) {
			return true;
		} else {
			return false;
		}
	}

	public function ajax_list($id_kls = NULL)
	{
		$list = $this->Rencana_pertemuan_model->get_datatables($id_kls);
		$data = [];
		$no = $_POST['start'] ?? 0;
		foreach ($list as $rencana) {
			if ($rencana->is_realisasi == 1) {
				$status_realisasi = '<span class="badge badge-success px-2 py-1"><i class="fa fa-check"></i> Sudah</span>';
				$tgl_realisasi = '<div class="small text-muted mt-1">Tgl: ' . tgl_indo(date('Y-m-d', strtotime($rencana->tanggal_realisasi))) . '</div>';
			} else {
				$status_realisasi = '<span class="badge badge-warning px-2 py-1"><i class="fa fa-clock-o"></i> Belum</span>';
				$tgl_realisasi = '';
			}
			$no++;
			$row = [];
			$row[] = '<strong>Meet ' . $rencana->pertemuan . '</strong><br><span class="text-primary font-weight-bold">' . tgl_indo($rencana->tanggal_rencana) . '</span><br><small class="text-muted">' . substr($rencana->jam_mulai, 0, 5) . ' sd ' . substr($rencana->jam_selesai, 0, 5) . '</small>';
			
			$bahasan_display = !empty($rencana->bacaan_materi) ? $rencana->bacaan_materi : $rencana->berita_acara_rencana;
			$row[] = '<div><strong>Bahasan:</strong> ' . $this->getExcerpt(strip_tags(str_replace(['<br />', '<br>', "\n"], ' ', $bahasan_display)), 0, 110) . '</div>';
			$row[] = $status_realisasi . $tgl_realisasi;

			$row[] = '<button type="button" class="btn btn-action-edit btn-sm rounded shadow-sm px-3" 
						 onclick="edit_rencana(' . "'" . $rencana->uniq . "'" . ')" title="Detail & Strategi Pembelajaran">
						 <i class="fa fa-pencil mr-1"></i> Edit / Detail
					</button>';

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

	private function getExcerpt($str, $startPos = 0, $maxLength = 100)
	{
		if (strlen($str) > $maxLength) {
			$excerpt   = substr($str, $startPos, $maxLength - 3);
			$lastSpace = strrpos($excerpt, ' ');
			$excerpt   = substr($excerpt, 0, $lastSpace);
			$excerpt  .= '...';
		} else {
			$excerpt = $str;
		}

		return $excerpt;
	}

	public function get_copy_data()
	{
		$id_kls = $this->session->userdata('id_kls_session');
		$id_ptk = $this->session->userdata('id_ptk');

		$data = $this->db->where('id_ptk', $id_ptk)->get('siakad_rencana_pertemuan')->row();
		$data_kls = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();

		//if($jml_data->num_rows() >= 14){
		$id_mk = $data_kls->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data_kls->nm_mk = $nm_mk;
		echo json_encode($data_kls);
		//}else{
		//	echo json_encode(['status'=>FALSE, 'message'=>'Mohon lengkapi sumber data, jumlah rencana pertemuan harus 15 minggu']);
		//}
	}

	public function ajax_edit($id)
	{
		$data = $this->Rencana_pertemuan_model->get_by_id($id);
		$data->tanggal_rencana = ($data->tanggal_rencana == '0000-00-00') ? '' : $data->tanggal_rencana;
		$data->jam_mulai = substr($data->jam_mulai, 0, 5);
		$data->jam_selesai = substr($data->jam_selesai, 0, 5);

		$data->berita_acara_rencana = str_ireplace(["<br />", "<br>"], "\r\n", $data->berita_acara_rencana ?: '');
		$data->bacaan_materi = str_ireplace(["<br />", "<br>"], "\r\n", $data->bacaan_materi ?: '');
		$data->draf_bap = str_ireplace(["<br />", "<br>"], "\r\n", $data->draf_bap ?: '');
		$data->sub_cpmk = str_ireplace(["<br />", "<br>"], "\r\n", $data->sub_cpmk ?: '');
		$data->metode_pembelajaran = $data->metode_pembelajaran ?: '';
		$data->indikator_evaluasi = str_ireplace(["<br />", "<br>"], "\r\n", $data->indikator_evaluasi ?: '');
		$data->catatan_refleksi = str_ireplace(["<br />", "<br>"], "\r\n", $data->catatan_refleksi ?: '');

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
			// Extract from blueprint assessment table in tujuan_belajar
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

			// Fallback if not in table
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

			// Fallback to CPMK list if no sub-cpmk found
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
					// Auto-fill Sub-CPMK if empty
					if (empty($data->sub_cpmk) && !empty($matchedRow['sub_cpmk'])) {
						$data->sub_cpmk = $matchedRow['sub_cpmk'];
					}
					// Auto-fill Bahan Kajian & Referensi Belajar if empty
					if (empty($data->bacaan_materi)) {
						$bk = !empty($matchedRow['bahan_kajian']) ? $matchedRow['bahan_kajian'] : '';
						$ref = !empty($matchedRow['sumber_belajar']) ? ("\nReferensi: " . $matchedRow['sumber_belajar']) : '';
						$data->bacaan_materi = trim($bk . $ref);
					}
					// Auto-fill Metode Pembelajaran SCL if empty
					if (empty($data->metode_pembelajaran)) {
						$metode = !empty($matchedRow['metode']) ? $matchedRow['metode'] : '';
						if (empty($metode) && !empty($matchedRow['bentuk_pembelajaran'])) {
							$metode = $matchedRow['bentuk_pembelajaran'];
						}
						$data->metode_pembelajaran = $metode;
					}
					// Auto-fill Indikator Evaluasi if empty
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

		// Auto-populate Skenario Sintaks SCL if empty
		if ($rps_obe && !empty($rps_obe->skenario_pembelajaran_markdown) && empty($data->berita_acara_rencana)) {
			$meetNum = (int)$data->pertemuan;
			if (preg_match('/(?:###[^\r\n]*PERTEMUAN[^\r\n]*KE\s*[-–—:]?\s*)' . $meetNum . '\b[^\r\n]*\r?\n(.*?)(?=(?:###[^\r\n]*PERTEMUAN|\Z))/is', $rps_obe->skenario_pembelajaran_markdown, $sMatch)) {
				$data->berita_acara_rencana = trim($sMatch[1]);
			}
		}

		$data->sub_cpmk_options = $sub_cpmk_list;

		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate('add');
		$id_jadwal			= $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan');
		$meeting = $rencana_pertemuan->num_rows() + 1;

		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));

		$tanggal_rencana 	= $tanggal;
		$pertemuan 			= $meeting;
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		$sub_cpmk           = trim($this->input->post('sub_cpmk'));
		$metode_pembelajaran = trim($this->input->post('metode_pembelajaran'));
		$indikator_evaluasi = trim($this->input->post('indikator_evaluasi'));
		$berita_acara_rencana =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('berita_acara_rencana'));
		$bacaan_materi =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));
		$draf_bap =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('draf_bap'));
		$catatan_refleksi =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('catatan_refleksi'));

		$create_by 			= $this->ion_auth->user()->row()->id;
		$create_date 		= date('Y-m-d H:i:s');

		$data = compact(
			'id_jadwal',
			'tanggal_rencana',
			'pertemuan',
			'jam_mulai',
			'jam_selesai',
			'jam_teori',
			'jam_praktek',
			'sub_cpmk',
			'metode_pembelajaran',
			'indikator_evaluasi',
			'berita_acara_rencana',
			'bacaan_materi',
			'draf_bap',
			'catatan_refleksi',
			'create_by',
			'create_date'
		);

		$insert = $this->Rencana_pertemuan_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate('update');
		$rencana_pertemuan = $this->db->where('uniq', $this->input->post('uniq'))->get('siakad_rencana_pertemuan');
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));

		$tanggal_rencana 	= $tanggal;
		$pertemuan 			= $this->input->post('pertemuan');
		$jam_mulai 			= $this->input->post('jam_mulai');
		$jam_selesai 		= $this->input->post('jam_selesai');
		$jam_teori 			= $this->input->post('jam_teori');
		$jam_praktek 		= $this->input->post('jam_praktek');
		$sub_cpmk           = trim($this->input->post('sub_cpmk'));
		$metode_pembelajaran = trim($this->input->post('metode_pembelajaran'));
		$indikator_evaluasi = trim($this->input->post('indikator_evaluasi'));
		$berita_acara_rencana =  str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('berita_acara_rencana'));
		$bacaan_materi		= str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('bacaan_materi'));
		$draf_bap			= str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('draf_bap'));
		$catatan_refleksi	= str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('catatan_refleksi'));
		$checked 			= $this->input->post('is_realisasi');
		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d H:i:s');

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

		if ($rencana_pertemuan->is_realisasi == 0) {
			$this->Rencana_pertemuan_model->delete_by_id($id);
			echo json_encode(["status" => TRUE]);
		} else {
			echo json_encode(["status" => FALSE, "message" => "Tidak bisa dihapus karena sudah realisasi."]);
		}
	}

	public function ajax_delete_all()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$rp_exist = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan')->result();
		$this->Rencana_pertemuan_model->delete_all($id_jadwal);
		echo json_encode(["status" => TRUE]);
	}

	public function get_kelas_kuliah()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$data_kls = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();
		$data_kls->jam_mulai = date('H:i', strtotime($data_kls->jam_mulai));
		$data_kls->jam_selesai = date('H:i', strtotime($data_kls->jam_selesai));
		echo json_encode($data_kls);
	}

	private function _validate($tipe)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('tanggal_rencana') == '') {
			$data['inputerror'][] = 'tanggal_rencana';
			$data['error_string'][] = 'Tanggal Rencana harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_mulai') == '') {
			$data['inputerror'][] = 'jam_mulai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_selesai') == '') {
			$data['inputerror'][] = 'jam_selesai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_teori') == '') {
			$data['inputerror'][] = 'jam_teori';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jam_praktek') == '') {
			$data['inputerror'][] = 'jam_praktek';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if (!strlen(trim($this->input->post('berita_acara_rencana')))) {
			$data['inputerror'][] = 'berita_acara_rencana';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		$dateObj1 = DateTime::createFromFormat('d.m.Y H:i', "10.10.2010 " . $this->input->post('jam_mulai'));
		$dateObj2 = DateTime::createFromFormat('d.m.Y H:i', "10.10.2010 " . $this->input->post('jam_selesai'));
		if ($dateObj1 == false) {
			$data['inputerror'][] = 'jam_mulai';
			$data['error_string'][] = 'Format Jam Salah.';
			$data['status'] = FALSE;
		}

		if ($dateObj2 == false) {
			$data['inputerror'][] = 'jam_selesai';
			$data['error_string'][] = 'Format Jam Salah.';
			$data['status'] = FALSE;
		}

		if ($tipe == 'insert') {
			//cari pertemuan double..
			$tanggal_rencana = date('Y-m-d', strtotime($this->input->post('tanggal_rencana')));
			$rencana_pertemuan = $this->db
				->where([
					'tanggal_rencana' => $tanggal_rencana,
					'jam_mulai' => $this->input->post('jam_mulai'),
					'id_kls' => $this->session->userdata('id_kls_session'),
				])
				->get('siakad_rencana_pertemuan');
			if ($rencana_pertemuan->num_rows() > 0) {
				$data['inputerror'][] = 'tanggal_rencana';
				$data['error_string'][] = 'Sudah ada Rencana Perkuliahan dengan kategori yang sama pada tanggal dan waktu yang diinput.';
				$data['status'] = FALSE;
			}
		}


		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	/**
	 * Helper: Parse teks Markdown RPS OBE menjadi array terstruktur 16 Pertemuan
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

		// Fallback jika belum ada markdown
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
					'sub_cpmk'             => $detail_row->kolom_3 ?? ($is_eval ?: '-'),
					'bahan_kajian'         => $detail_row->kolom_4 ?? ($is_eval ? 'Ujian / Asesmen CPMK' : '-'),
					'bacaan_materi'        => $detail_row->kolom_4 ?? ($is_eval ? 'Ujian / Asesmen CPMK' : '-'),
					'metode'               => $detail_row->kolom_5 ?? ($is_eval ? 'Asesmen Tertulis/Proyek' : 'Kuliah Interaktif & Diskusi'),
					'berita_acara_rencana' => $detail_row->kolom_5 ?? '',
					'skenario_scl'         => $detail_row->kolom_7 ?? ($is_eval ? 'Pelaksanaan Ujian Evaluasi Capaian Pembelajaran' : 'Pendahuluan, Eksplorasi Konsep, Diskusi Kasus, Refleksi & Penutup.'),
					'draf_bap'             => $detail_row->kolom_4 ?? ($is_eval ?: 'Pembahasan materi pembelajaran dan studi kasus terapan.'),
					'evaluasi'             => $detail_row->kolom_10 ?? '',
					'indikator_evaluasi'   => $detail_row->kolom_10 ?? '',
					'jam_teori'            => (float)($detail_row->kolom_8 ?? 1),
					'jam_praktek'          => (float)($detail_row->kolom_9 ?? 2),
					'sumber'               => $detail_row->kolom_13 ?? 'Pustaka Utama RPS'
				];
			}
		}

		ksort($meetings);
		return $meetings;
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

			// Check if stage title
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
}
