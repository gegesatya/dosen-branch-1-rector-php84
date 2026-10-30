<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_nilai_sempro extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_nilai_sempro_model');
		$this->load->model('Qr_model');

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus dosen.');
		}
	}

	// ─── Tolak request non-AJAX ke endpoint AJAX ──────────────────────────
	private function _require_ajax()
	{
		if (!$this->input->is_ajax_request()) {
			show_error('Forbidden', 403);
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$id_ptk = $this->session->userdata('id_ptk');
		$list = $this->E_skripsi_nilai_sempro_model->get_all_nilai_sempro($id_ptk);
		$data_mahasiswa = [];

		$ids = [];
		foreach ($list as $rg) {
			if (!in_array($rg->id_judul_skripsi, $ids)) {
				$ids[] = $rg->id_judul_skripsi;
			}
		}

		$map_rerata = [];
		$map_hitung = [];
		$map_next_seminar = [];
		$map_sempro_schedule = [];
		$map_nilai = [];
		$map_ai_record = [];

		if (!empty($ids)) {
			// 1. Average & Count
			$rerata_hitung_data = $this->db->where_in('id_judul_skripsi', $ids)
				->group_by('id_judul_skripsi')
				->select('id_judul_skripsi, avg(nilai_akhir) as nilai_rerata, count(nilai_akhir) as hitung, count(*) as total_penguji')
				->get('siakad_skripsi_nilai_sempro')->result();
			foreach ($rerata_hitung_data as $row) {
				$map_rerata[$row->id_judul_skripsi] = $row->nilai_rerata;
				$map_hitung[$row->id_judul_skripsi] = $row->hitung;
				$map_total[$row->id_judul_skripsi] = $row->total_penguji;
			}

			// 2. Next seminar (is_locked)
			$next_seminar_data = $this->db->where_in('id_judul_skripsi', $ids)
				->where(['kategori_pengajuan' => 2, 'status_undangan' => 1])
				->get('siakad_skripsi_seminar_sidang')->result();
			foreach ($next_seminar_data as $row) {
				$map_next_seminar[$row->id_judul_skripsi] = true;
			}

			// 3. Sempro Schedule
			$sempro_schedule_data = $this->db->where_in('id_judul_skripsi', $ids)
				->where(['kategori_pengajuan' => 1])
				->get('siakad_skripsi_seminar_sidang')->result();
			foreach ($sempro_schedule_data as $row) {
				$map_sempro_schedule[$row->id_judul_skripsi] = $row;
			}

			// 4. Nilai (Hasil sempro from pembimbing 1)
			$nilai_data = $this->db->where_in('id_judul_skripsi', $ids)
				->get('siakad_skripsi_nilai_sempro')->result();
			foreach ($nilai_data as $row) {
				$map_nilai[$row->id_judul_skripsi] ??= [];
				$map_nilai[$row->id_judul_skripsi][$row->id_penilai] = $row;
			}

			// 5. AI Records
			$ai_records = $this->db->where_in('id_judul_skripsi', $ids)
				->where('id_ptk', $id_ptk)
				->get('siakad_skripsi_sempro_ai')->result();
			foreach ($ai_records as $row) {
				$map_ai_record[$row->id_judul_skripsi] = true;
			}
		}

		foreach ($list as $rg) {
			$id_judul_skripsi = $rg->id_judul_skripsi;

			$status_dosen = '';
			if ($id_ptk == $rg->dosen_pembimbing_1) {
				$status_dosen = 'Pembimbing Utama';
			} elseif ($id_ptk == $rg->dosen_pembimbing_2) {
				$status_dosen = 'Pembimbing Pendamping/Penguji';
			} elseif ($id_ptk == $rg->dosen_penguji_sempro_1) {
				$status_dosen = 'Penguji';
			} elseif ($id_ptk == $rg->dosen_penguji_sempro_2) {
				$status_dosen = 'Penguji';
			} elseif ($id_ptk == $rg->dosen_penguji_sempro_3) {
				$status_dosen = 'Penguji';
			}

			$nilai = $map_nilai[$id_judul_skripsi][$rg->dosen_pembimbing_1] ?? null;
			$hasil = (!empty($nilai) && !empty($nilai->sempro_hasil)) ? ucfirst((string)$nilai->sempro_hasil) : '-';

			$rg->nilai_rerata = isset($map_rerata[$id_judul_skripsi]) ? round($map_rerata[$id_judul_skripsi], 2) : 0;
			$rg->hitung_dinilai = $map_hitung[$id_judul_skripsi] ?? 0;
			$is_akuntansi = $this->db->select('id_sms')->where('id_judul_skripsi', $id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row()->id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';
			$rg->total_penguji = $is_akuntansi ? 2 : ($map_total[$id_judul_skripsi] ?? 0);
			$rg->is_locked = isset($map_next_seminar[$id_judul_skripsi]);
			$rg->status_dosen = $status_dosen;
			$rg->hasil_sempro = $hasil;
			
			$sempro_schedule = $map_sempro_schedule[$id_judul_skripsi] ?? null;
			$rg->tanggal_sempro = $sempro_schedule ? $sempro_schedule->tanggal_seminar : null;
			$rg->jam_sempro = $sempro_schedule ? $sempro_schedule->jam : null;
			$rg->tempat_sempro = $sempro_schedule ? $sempro_schedule->tempat : null;
			$rg->link_dokumen = $sempro_schedule ? $sempro_schedule->link_dokumen : null;
			$rg->has_ai_record = isset($map_ai_record[$id_judul_skripsi]);

			$data_mahasiswa[] = $rg;
		}

		$output['judul_besar'] = 'Seminar Proposal';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_nilai_sempro';
		$output['parent_skripsi'] = 'active open';
		$output['data_mahasiswa'] = $data_mahasiswa;
		$output['content'] = 'E_skripsi_nilai_sempro/E_skripsi_nilai_sempro_view';
		$output['after_page'] = 'E_skripsi_nilai_sempro/E_skripsi_nilai_sempro_after_page';
		$this->load->view('master_page', $output);
	}


	public function ajax_edit($id)
	{
		$this->_require_ajax();

		$id_ptk = $this->session->userdata('id_ptk');
		$id_sms = $this->session->userdata('prodi');
		$data   = $this->E_skripsi_nilai_sempro_model->get_by_id($id);

		// Pastikan record milik dosen yang login
		if (!$data || $data->id_penilai != $id_ptk) {
			echo json_encode(['error' => 'Forbidden']);
			return;
		}

		$data->seminar_jam_mulai   = substr($data->seminar_jam_mulai  ?? '', 0, 5);
		$data->seminar_jam_selesai = substr($data->seminar_jam_selesai ?? '', 0, 5);

		// Tabel nilai seluruh dosen dalam prodi
		$nilai_all = $this->db
			->select('d.nama_lengkap, n.nilai_akhir')
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = n.id_penilai')
			->where(['n.id_judul_skripsi' => $data->id_judul_skripsi, 'd.id_sms' => $id_sms])
			->get()->result();

		$tbl_nilai = '<table class="table table-bordered table-condensed" style="font-size:13px;">
		             <thead><tr><th>Dosen</th><th>Nilai Akhir</th></tr></thead><tbody>';
		$total_nilai = 0;
		$jumlah_penguji = 0;
		foreach ($nilai_all as $v) {
			$tbl_nilai .= '<tr><td>' . htmlspecialchars($v->nama_lengkap) . '</td><td>' . ($v->nilai_akhir * 1) . '</td></tr>';
			$total_nilai += $v->nilai_akhir;
			$jumlah_penguji++;
		}
		$tbl_nilai .= '</tbody></table>';
		$data->tbl_nilai = $tbl_nilai;
		$data->rerata_nilai = $jumlah_penguji > 0 ? $total_nilai / $jumlah_penguji : 0;

		// Cek apakah Berita Acara sudah diisi (sempro_hasil != kosong/belum_ada pada row pembimbing 1)
		$pembimbing1 = $this->db->select('dosen_pembimbing_1')
			->where('id_judul_skripsi', $data->id_judul_skripsi)
			->get('siakad_skripsi_pengajuan_judul')->row()->dosen_pembimbing_1 ?? null;

		$ba_sudah_diisi = false;
		if ($pembimbing1) {
			$row_p1 = $this->db->select('sempro_hasil')
				->where('id_judul_skripsi', $data->id_judul_skripsi)
				->where('id_penilai', $pembimbing1)
				->get('siakad_skripsi_nilai_sempro')->row();
			if ($row_p1 && !empty($row_p1->sempro_hasil) && $row_p1->sempro_hasil != '-' && strtolower((string)$row_p1->sempro_hasil) != 'belum_ada') {
				$ba_sudah_diisi = true;
			}
		}
		$data->ba_sudah_diisi = $ba_sudah_diisi;

		echo json_encode($data);
	}


	public function ajax_update()
	{
		$this->_require_ajax();
		$this->_validate();

		$id_ptk   = $this->session->userdata('id_ptk');
		$id_nilai = (int) $this->input->post('id_nilai_sempro');

		// Kepemilikan: pastikan record milik dosen yang login
		$existing = $this->db
			->select('id_nilai_sempro')
			->where(['id_nilai_sempro' => $id_nilai, 'id_penilai' => $id_ptk])
			->get('siakad_skripsi_nilai_sempro')->row();
		if (!$existing) {
			echo json_encode(['status' => FALSE, 'error' => 'Forbidden']);
			return;
		}

		$a = (float) $this->input->post('nilai_komponen_a');
		$b = (float) $this->input->post('nilai_komponen_b');
		$c = (float) $this->input->post('nilai_komponen_c');
		$d = (float) $this->input->post('nilai_komponen_d');
		$e = (float) $this->input->post('nilai_komponen_e');
		$nilai_akhir = round(0.25*$a + 0.20*$b + 0.25*$c + 0.15*$d + 0.15*$e, 4);

		$this->E_skripsi_nilai_sempro_model->update(
			['id_nilai_sempro' => $id_nilai],
			[
				'nilai_komponen_a' => $a,
				'nilai_komponen_b' => $b,
				'nilai_komponen_c' => $c,
				'nilai_komponen_d' => $d,
				'nilai_komponen_e' => $e,
				'nilai_akhir'      => $nilai_akhir,
				'change_by'        => $this->ion_auth->user()->row()->email,
				'change_date'      => date('Y-m-d H:i:s'),
			]
		);
		echo json_encode(['status' => TRUE]);
	}

	public function ajax_update_catatan()
	{
		$this->_require_ajax();

		$id_ptk   = $this->session->userdata('id_ptk');
		$id_nilai = (int) $this->input->post('id_nilai_sempro');

		$existing = $this->db
			->select('id_nilai_sempro')
			->where(['id_nilai_sempro' => $id_nilai, 'id_penilai' => $id_ptk])
			->get('siakad_skripsi_nilai_sempro')->row();
		if (!$existing) {
			echo json_encode(['status' => FALSE, 'error' => 'Forbidden']);
			return;
		}

		$this->E_skripsi_nilai_sempro_model->update(
			['id_nilai_sempro' => $id_nilai],
			[
				'catatan_judul'             => $this->input->post('catatan_judul'),
				'catatan_latar_belakang'    => $this->input->post('catatan_latar_belakang'),
				'catatan_rumusan_masalah'   => $this->input->post('catatan_rumusan_masalah'),
				'catatan_tujuan_penelitian' => $this->input->post('catatan_tujuan_penelitian'),
				'catatan_tinjauan_pustaka'  => $this->input->post('catatan_tinjauan_pustaka'),
				'catatan_metode_penelitian' => $this->input->post('catatan_metode_penelitian'),
				'catatan_daftar_pustaka'    => $this->input->post('catatan_daftar_pustaka'),
				'catatan_format_penulisan'  => $this->input->post('catatan_format_penulisan'),
				'change_by'                 => $this->ion_auth->user()->row()->email,
				'change_date'               => date('Y-m-d H:i:s'),
			]
		);
		echo json_encode(['status' => TRUE]);
	}

	public function ajax_update_berita_acara()
	{
		$this->_require_ajax();
		$this->_validate_berita_acara();

		$judul_lama = $this->input->post('judul_ditetapkan_1');
		$judul_baru = $this->input->post('judul_ditetapkan_2');
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		if ($this->input->post('status_revisi_judul') == true) {

			$user = $this->ion_auth->user()->row();

			$this->db->insert('siakad_skripsi_judul_lama', [
				'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
				'judul_lama' => $judul_lama,
				'create_by' => $user->first_name,
				'create_date' => date('Y-m-d H:i:s')
			]);

			$this->db->update('siakad_skripsi_pengajuan_judul', ['judul_ditetapkan' => $judul_baru, 'status_revisi_judul' => 1], ['id_judul_skripsi' => $id_judul_skripsi]);
		}

		$hari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		$tgl_post   = $this->input->post('seminar_tanggal');
		$hari_indo  = $tgl_post ? $hari_array[date('w', strtotime($tgl_post))] : '';

		$data = [
			'seminar_hari' => $hari_indo,
			'seminar_tanggal' => $this->input->post('seminar_tanggal'),
			'seminar_moderator' => $this->input->post('seminar_moderator'),
			'seminar_nim_moderator' => $this->input->post('seminar_nim_moderator'),
			'seminar_pembahas_1' => $this->input->post('seminar_pembahas_1'),
			'seminar_nim_pembahas_1' => $this->input->post('seminar_nim_pembahas_1'),
			'seminar_pembahas_2' => $this->input->post('seminar_pembahas_2'),
			'seminar_nim_pembahas_2' => $this->input->post('seminar_nim_pembahas_2'),
			'seminar_jumlah_mahasiswa' => $this->input->post('seminar_jumlah_mahasiswa'),
			'seminar_tempat' => $this->input->post('seminar_tempat'),
			'seminar_jam_mulai' => $this->input->post('seminar_jam_mulai'),
			'seminar_jam_selesai' => $this->input->post('seminar_jam_selesai'),
			'sempro_hasil' => $this->input->post('sempro_hasil'),
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		//if ($this->input->post('hasil_sempro') == 'layak') {
		//$data2 = ['hasil_sempro' => $this->input->post('hasil_sempro')];
		/* } elseif ($this->input->post('hasil_sempro') == 'tidak_layak') {
			$data2 = [
				'hasil_sempro' => $this->input->post('hasil_sempro'),
				'sudah_boleh_maju_prodi' => 0,
				'tanggal_sempro' => NULL,
				'jam_sempro' => NULL,
				'tempat_sempro' => NULL,
				'undangan_nomor' => NULL,
				'undangan_tanggal' => NULL,
				'undangan_status' => 0,
				'approval_sempro_pembimbing_satu' => 0,
				'approval_sempro_pembimbing_dua' => 0,
				'tgl_approval_sempro_p1' => NULL,
				'tgl_approval_sempro_p2' => NULL,
				'tgl_maju_sempro_prodi' => NULL
			];
		} */

		if ($this->E_skripsi_nilai_sempro_model->update_berita_acara_sempro(['id_nilai_sempro' => $this->input->post('id_nilai_sempro')], $data)) {
			echo json_encode(["status" => TRUE]);
		} else {
			echo json_encode(["status" => FALSE]);
		}
	}



	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('nilai_komponen_a') == '') {
			$data['inputerror'][] = 'nilai_komponen_a';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_a') < 1) {
			$data['inputerror'][] = 'nilai_komponen_a';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_a') > 7) {
			$data['inputerror'][] = 'nilai_komponen_a';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_a') == 4) {
			$data['inputerror'][] = 'nilai_komponen_a';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nilai_komponen_b') == '') {
			$data['inputerror'][] = 'nilai_komponen_b';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_b') < 1) {
			$data['inputerror'][] = 'nilai_komponen_b';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_b') > 7) {
			$data['inputerror'][] = 'nilai_komponen_b';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_b') == 4) {
			$data['inputerror'][] = 'nilai_komponen_b';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nilai_komponen_c') == '') {
			$data['inputerror'][] = 'nilai_komponen_c';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_c') < 1) {
			$data['inputerror'][] = 'nilai_komponen_c';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_c') > 7) {
			$data['inputerror'][] = 'nilai_komponen_c';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_c') == 4) {
			$data['inputerror'][] = 'nilai_komponen_c';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nilai_komponen_d') == '') {
			$data['inputerror'][] = 'nilai_komponen_d';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_d') < 1) {
			$data['inputerror'][] = 'nilai_komponen_d';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_d') > 7) {
			$data['inputerror'][] = 'nilai_komponen_d';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_d') == 4) {
			$data['inputerror'][] = 'nilai_komponen_d';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nilai_komponen_e') == '') {
			$data['inputerror'][] = 'nilai_komponen_e';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_e') < 1) {
			$data['inputerror'][] = 'nilai_komponen_e';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_e') > 7) {
			$data['inputerror'][] = 'nilai_komponen_e';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('nilai_komponen_e') == 4) {
			$data['inputerror'][] = 'nilai_komponen_e';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_berita_acara()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$seminar_nim_moderator = $this->input->post('seminar_nim_moderator');
		$seminar_nim_pembahas_1 = $this->input->post('seminar_nim_pembahas_1');
		$seminar_nim_pembahas_2 = $this->input->post('seminar_nim_pembahas_2');
		$seminar_jam_mulai = $this->input->post('seminar_jam_mulai');
		$seminar_jam_selesai = $this->input->post('seminar_jam_selesai');

		if ($this->input->post('seminar_tanggal') == '') {
			$data['inputerror'][] = 'seminar_tanggal';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('seminar_jam_mulai') == '') {
			$data['inputerror'][] = 'seminar_jam_mulai';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('seminar_jam_selesai') == '') {
			$data['inputerror'][] = 'seminar_jam_selesai';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if (!is_numeric($seminar_nim_moderator)) {
			$data['inputerror'][] = 'seminar_nim_moderator';
			$data['error_string'][] = 'Harus diisi dengan angka';
			$data['status'] = FALSE;
		}

		if ($this->nim_check($seminar_nim_moderator) == FALSE) {
			$data['inputerror'][] = 'seminar_nim_moderator';
			$data['error_string'][] = 'NIM tidak ada';
			$data['status'] = FALSE;
		}

		if (!is_numeric($seminar_nim_pembahas_1)) {
			$data['inputerror'][] = 'seminar_nim_pembahas_1';
			$data['error_string'][] = 'Harus diisi dengan angka';
			$data['status'] = FALSE;
		}

		if ($this->nim_check($seminar_nim_pembahas_1) == FALSE) {
			$data['inputerror'][] = 'seminar_nim_pembahas_1';
			$data['error_string'][] = 'NIM tidak ada';
			$data['status'] = FALSE;
		}

		if (!is_numeric($seminar_nim_pembahas_2)) {
			$data['inputerror'][] = 'seminar_nim_pembahas_2';
			$data['error_string'][] = 'Harus diisi dengan angka';
			$data['status'] = FALSE;
		}

		if ($this->nim_check($seminar_nim_pembahas_2) == FALSE) {
			$data['inputerror'][] = 'seminar_nim_pembahas_2';
			$data['error_string'][] = 'NIM tidak ada';
			$data['status'] = FALSE;
		}

		if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $seminar_jam_mulai) == FALSE) {
			$data['inputerror'][] = 'seminar_jam_mulai';
			$data['error_string'][] = 'Format Jam Salah, format yang benar adalah hh:mm';
			$data['status'] = FALSE;
		}

		if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $seminar_jam_selesai) == FALSE) {
			$data['inputerror'][] = 'seminar_jam_selesai';
			$data['error_string'][] = 'Format Jam Salah, format yang benar adalah hh:mm';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function nim_check($nipd)
	{
		$is_check = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->num_rows();
		if ($is_check == 0) {
			$this->form_validation->set_message('nim_check', '{field} ' . $nipd . ' tidak ada atau tidak valid');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function ai_assistant($id_judul_skripsi)
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->load->model('Setting_api_key_model');
		$api_key = $this->Setting_api_key_model->get_api_key($id_ptk);
		if (empty($api_key)) {
			$this->session->set_flashdata('error', 'Anda harus mengatur API Key terlebih dahulu sebelum dapat menggunakan fitur AI Assistant.');
			redirect('setting_api_key');
		}

		$data['id_ptk'] = $this->session->userdata('id_ptk');
		$data['data_ptk'] = $this->session->userdata('data_ptk');
		$data['judul_besar'] = 'AI Examiner Assistant (SEMPRO)';

		$data['skripsi'] = $this->db->select('a.*, b.nm_lemb')
			->from('siakad_skripsi_pengajuan_judul a')
			->join('sms b', 'a.id_sms = b.id_sms', 'left')
			->where('a.id_judul_skripsi', $id_judul_skripsi)
			->get()->row();

		if (!$data['skripsi']) {
			show_404();
		}

		$data['mahasiswa'] = $this->db->select('mahasiswa_pt.nipd, mahasiswa.nm_pd, mahasiswa.foto')
			->from('mahasiswa_pt')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where('mahasiswa_pt.nipd', $data['skripsi']->nipd)
			->get()->row();
		$data['jadwal'] = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->where('kategori_pengajuan', 1)->get('siakad_skripsi_seminar_sidang')->row();

		$data['nm_penguji_1'] = '-';
		$data['nm_penguji_2'] = '-';
		$data['nm_penguji_3'] = '-';

		if ($data['skripsi']) {
			$p1 = $this->db->where('id_ptk', $data['skripsi']->dosen_penguji_sempro_1)->get('siakad_skripsi_dosen')->row();
			$p2 = $this->db->where('id_ptk', $data['skripsi']->dosen_penguji_sempro_2)->get('siakad_skripsi_dosen')->row();
			$p3 = $this->db->where('id_ptk', $data['skripsi']->dosen_penguji_sempro_3)->get('siakad_skripsi_dosen')->row();
			
			if ($p1) $data['nm_penguji_1'] = $p1->nama_lengkap;
			if ($p2) $data['nm_penguji_2'] = $p2->nama_lengkap;
			if ($p3) $data['nm_penguji_3'] = $p3->nama_lengkap;
		}

		$data['locked_roles'] = [];
		$other_ai_reviews = $this->db->select('roles_selected')
			->where('id_judul_skripsi', $id_judul_skripsi)
			->where('id_ptk !=', $id_ptk)
			->get('siakad_skripsi_sempro_ai')->result();
		foreach ($other_ai_reviews as $rev) {
			if (!empty($rev->roles_selected)) {
				$roles = explode(',', $rev->roles_selected);
				foreach ($roles as $r) {
					$data['locked_roles'][] = trim($r);
				}
			}
		}
		$data['locked_roles'] = array_unique($data['locked_roles']);

		// Fetch current user's existing AI review
		$my_ai_review = $this->db->where('id_judul_skripsi', $id_judul_skripsi)
			->where('id_ptk', $id_ptk)
			->get('siakad_skripsi_sempro_ai')->row();
		
		$data['my_roles'] = [];
		$data['my_ai_result'] = null;
		$data['my_file_url'] = '';
		$data['my_questions_evaluation'] = null;

		if ($my_ai_review) {
			if (!empty($my_ai_review->roles_selected)) {
				$data['my_roles'] = array_map('trim', explode(',', $my_ai_review->roles_selected));
			}
			$data['my_ai_result'] = json_decode($my_ai_review->ai_result, true);
			$data['my_file_url'] = $my_ai_review->file_url;
			$data['my_questions_evaluation'] = json_decode($my_ai_review->questions_evaluation, true);
		}


		$data['menu_aktif'] = 'm_nilai_sempro';
		$data['parent_skripsi'] = 'active open';
		$data['content'] = 'E_skripsi_nilai_sempro/e_skripsi_nilai_sempro_ai_detail_view';
		$data['after_page'] = 'E_skripsi_nilai_sempro/e_skripsi_nilai_sempro_ai_detail_after_page';

		// ── Penilaian milik dosen yang sedang login ───────────────────────────
		$penilaian = $this->db
			->select('n.id_nilai_sempro, n.nilai_akhir, n.catatan_judul, n.catatan_latar_belakang,
				n.catatan_rumusan_masalah, n.catatan_tujuan_penelitian, n.catatan_tinjauan_pustaka,
				n.catatan_metode_penelitian, n.catatan_daftar_pustaka, n.catatan_format_penulisan,
				n.sempro_hasil')
			->from('siakad_skripsi_nilai_sempro n')
			->where(['n.id_judul_skripsi' => $id_judul_skripsi, 'n.id_penilai' => $id_ptk])
			->get()->row();

		$data['penilaian']       = $penilaian;
		$data['id_nilai_sempro'] = $penilaian ? $penilaian->id_nilai_sempro : null;

		// ── Ambil semua baris nilai_sempro skripsi ini (1 query) ──────────────
		$semua_nilai = $this->db
			->select('id_penilai, nilai_akhir, sempro_hasil')
			->where('id_judul_skripsi', $id_judul_skripsi)
			->get('siakad_skripsi_nilai_sempro')->result();

		$total_penguji       = count($semua_nilai);
		$sudah_nilai         = 0;
		$sempro_hasil_p1     = '';	// hanya diisi Pembimbing Utama lewat form BA
		$is_pembimbing_utama = ($data['skripsi']->dosen_pembimbing_1 == $id_ptk);

		foreach ($semua_nilai as $row) {
			if (!empty($row->nilai_akhir) && $row->nilai_akhir > 0) {
				$sudah_nilai++;
			}
			// Ambil sempro_hasil dari row milik Pembimbing Utama
			if ($row->id_penilai == $data['skripsi']->dosen_pembimbing_1) {
				$sempro_hasil_p1 = $row->sempro_hasil ?? '';
			}
		}

		// "Semua penguji LAIN sudah menilai" → Pembimbing Utama boleh isi BA
		$my_nilai_akhir = $penilaian ? (float) $penilaian->nilai_akhir : 0;
		$sudah_lain     = $sudah_nilai - ($my_nilai_akhir > 0 ? 1 : 0);
		
		$is_akuntansi = $data['skripsi']->id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';
		$effective_total_penguji = $is_akuntansi ? 2 : $total_penguji;
		$target_lain    = $effective_total_penguji - 1;
		
		// Edge case: jika total penguji hanya 1, langsung boleh isi BA
		$other_examiners_graded = ($target_lain <= 0) || ($sudah_lain >= $target_lain);

		// Status BA diambil dari row Pembimbing Utama (bukan dari dosen yang login)
		$ba_sudah_diisi = !empty($sempro_hasil_p1)
			&& $sempro_hasil_p1 != '-'
			&& strtolower($sempro_hasil_p1) != 'belum_ada';

		// ── Kondisi tampil tombol berdasarkan tanggal seminar ─────────────────
		// Sama persis dengan logika halaman utama:
		//   $is_editable = (...) && !$is_future  →  tombol muncul saat hari ini atau lewat
		$today_ai       = date('Y-m-d');
		$tgl_seminar    = !empty($data['jadwal']->tanggal_seminar) ? $data['jadwal']->tanggal_seminar : '';
		$has_schedule   = !empty($tgl_seminar) && $tgl_seminar !== '0000-00-00';
		$is_future      = $has_schedule && $tgl_seminar > $today_ai;   // besok atau lebih
		$is_past        = $has_schedule && $tgl_seminar < $today_ai;   // kemarin atau lebih lama
		$is_today       = $has_schedule && $tgl_seminar === $today_ai; // tepat hari ini

		// Tombol Nilai & Catatan: muncul mulai hari-H seminar (hari ini atau sudah lewat)
		// $show_form_buttons  = $has_schedule && !$is_future;
		$show_form_buttons = true; // TESTING MODE: Selalu buka
        
		// Tombol BA (di halaman utama): tanggal_sempro < $today  → baru lewat hari-H
		// Di sini kita samakan: sudah lewat hari-H
		// $show_ba_button     = $is_past;
		$show_ba_button = true; // TESTING MODE: Selalu buka

		$data['is_pembimbing_utama']    = $is_pembimbing_utama;
		$data['other_examiners_graded'] = $other_examiners_graded;
		$data['ba_sudah_diisi']         = $ba_sudah_diisi;
		$data['sisa_belum_nilai']       = max(0, $target_lain - $sudah_lain);
		$data['show_form_buttons']      = $show_form_buttons;
		$data['show_ba_button']         = $show_ba_button;
		$data['is_past_seminar']        = $is_past;
		$data['is_today_seminar']       = $is_today;
		$data['tgl_seminar']            = $tgl_seminar;
		$data['has_schedule']           = $has_schedule;

		$this->load->view('master_page', $data);
	}



	public function ajax_check_and_lock_roles()
	{
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$file_url = $this->input->post('file_url');
		$role_penguji = $this->input->post('role_penguji');
		$id_ptk = $this->session->userdata('id_ptk');

		if (empty($id_judul_skripsi) || empty($role_penguji)) {
			echo json_encode(['status' => false, 'msg' => 'Data tidak lengkap.']);
			return;
		}

		if (!is_array($role_penguji)) {
			$role_penguji = [$role_penguji];
		}

		// Check if any role is already taken by ANOTHER examiner
		$other_reviews = $this->db->select('roles_selected')
			->where('id_judul_skripsi', $id_judul_skripsi)
			->where('id_ptk !=', $id_ptk)
			->get('siakad_skripsi_sempro_ai')->result();
		
		$locked_roles = [];
		foreach ($other_reviews as $rev) {
			if (!empty($rev->roles_selected)) {
				$roles = explode(',', $rev->roles_selected);
				foreach ($roles as $r) {
					$locked_roles[] = trim($r);
				}
			}
		}

		// Map English key to readable Indonesian name for error message
		$role_names = [
			'problem_reviewer' => 'Problem Reviewer',
			'literature_reviewer' => 'Literature Reviewer',
			'method_reviewer' => 'Method Reviewer',
			'statistical_reviewer' => 'Statistical Reviewer',
			'technical_reviewer' => 'Technical Reviewer',
			'industry_reviewer' => 'Industry Reviewer',
			'ethics_reviewer' => 'Ethics & Feasibility Reviewer'
		];

		$taken = array_intersect($role_penguji, $locked_roles);
		if (!empty($taken)) {
			$first_taken = reset($taken);
			$taken_name = $role_names[$first_taken] ?? $first_taken;
			echo json_encode(['status' => false, 'msg' => 'Peran "' . $taken_name . '" sudah diambil oleh penguji lain. Silakan pilih peran lain atau muat ulang halaman.']);
			return;
		}

		// Proceed to save/lock the roles
		$existing = $this->db->where('id_judul_skripsi', $id_judul_skripsi)
			->where('id_ptk', $id_ptk)
			->get('siakad_skripsi_sempro_ai')->row();

		if ($existing) {
			$this->db->where('id_judul_skripsi', $id_judul_skripsi)
				->where('id_ptk', $id_ptk)
				->update('siakad_skripsi_sempro_ai', [
					'file_url' => $file_url,
					'roles_selected' => implode(',', $role_penguji)
				]);
		} else {
			$this->db->insert('siakad_skripsi_sempro_ai', [
				'id_judul_skripsi' => $id_judul_skripsi,
				'id_ptk'           => $id_ptk,
				'file_url'         => $file_url,
				'roles_selected'   => implode(',', $role_penguji),
				'created_at'       => date('Y-m-d H:i:s')
			]);
		}

		echo json_encode(['status' => true]);
	}

	public function ajax_save_evaluation()
	{
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$id_ptk = $this->session->userdata('id_ptk');
		$evaluations = $this->input->post('evaluations');

		if (empty($id_judul_skripsi)) {
			echo json_encode(['status' => false, 'msg' => 'Data tidak lengkap.']);
			return;
		}

		$this->db->where('id_judul_skripsi', $id_judul_skripsi)
			->where('id_ptk', $id_ptk)
			->update('siakad_skripsi_sempro_ai', [
				'questions_evaluation' => $evaluations
			]);

		echo json_encode(['status' => true]);
	}

	public function ajax_generate_ai_review()
	{
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$file_url = $this->input->post('file_url');
		$role_penguji = $this->input->post('role_penguji');
		
		if (empty($id_judul_skripsi) || empty($file_url) || empty($role_penguji)) {
			echo json_encode(['status' => false, 'msg' => 'Data tidak lengkap.']);
			return;
		}

		$skripsi = $this->db->select('a.judul_ditetapkan, b.nm_lemb')
							->from('siakad_skripsi_pengajuan_judul a')
							->join('sms b', 'a.id_sms = b.id_sms', 'left')
							->where('a.id_judul_skripsi', $id_judul_skripsi)
							->get()->row();
		if (!$skripsi) {
			echo json_encode(['status' => false, 'msg' => 'Data skripsi tidak ditemukan.']);
			return;
		}

		$judul_ta = $skripsi->judul_ditetapkan;
		$prodi = $skripsi->nm_lemb;

		$is_gdoc = str_contains($file_url, 'docs.google.com/document');
		$temp_file = sys_get_temp_dir() . '/ai_sempro_' . md5($file_url . time()) . ($is_gdoc ? '.docx' : '.pdf');

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
		$dl_http_code = curl_getinfo($ch_dl, CURLINFO_HTTP_CODE);
		curl_close($ch_dl);

		if (!$file_content || $dl_http_code != 200) {
			echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh file dokumen dari Google Drive. Pastikan link dapat diakses publik.']);
			return;
		}
		
		file_put_contents($temp_file, $file_content);

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
							throw new \Exception("File bukan docx valid.");
						}
					} else {
						throw new \Exception("Gagal buka ZIP/DOCX.");
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
			echo json_encode(['status' => false, 'msg' => 'Teks dokumen kosong atau file hasil scan.']);
			return;
		}
		$extracted_text = substr($extracted_text, 0, 15000);

		$id_ptk = $this->session->userdata('id_ptk');
		$this->load->model('Setting_api_key_model');
		$api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);
		
		if (empty($api_setting) || empty($api_setting->api_key)) {
			echo json_encode(['status' => false, 'msg' => 'API Key belum disetting. Silakan setting API Key di menu Pengaturan terlebih dahulu.']);
			return;
		}
		
		$api_key = $api_setting->api_key;
        $ai_provider = isset($api_setting->ai_provider) && !empty($api_setting->ai_provider) ? $api_setting->ai_provider : 'gemini';
        $ai_model = isset($api_setting->ai_model) && !empty($api_setting->ai_model) ? $api_setting->ai_model : 'gemini-1.5-flash';

		$prodi_clean = strtolower(trim($prodi));
		
		$role_penguji_arr = $this->input->post('role_penguji');
		if (!is_array($role_penguji_arr)) {
			$role_penguji_arr = [$role_penguji_arr];
		}

		$role_fokus_list = [];
		$instruksi_prodi_list = [];

		foreach ($role_penguji_arr as $role) {
			if ($role == 'problem_reviewer') {
				$role_fokus_list[] = "Problem Reviewer (Fokus: Latar belakang masalah, urgensi penelitian, dan ketajaman rumusan masalah)";
			} elseif ($role == 'literature_reviewer') {
				$role_fokus_list[] = "Literature Reviewer (Fokus: Kedalaman kajian pustaka, identifikasi gap penelitian (state of the art), dan kerangka teori)";
			} elseif ($role == 'method_reviewer') {
				$role_fokus_list[] = "Method Reviewer (Fokus: Ketepatan desain penelitian, definisi operasional variabel, populasi, sampel, dan instrumen)";
			} elseif ($role == 'statistical_reviewer') {
				$role_fokus_list[] = "Statistical Reviewer (Fokus: Kesesuaian teknik analisis data dan ketepatan rumus statistik yang diajukan)";
			} elseif ($role == 'technical_reviewer') {
				$role_fokus_list[] = "Technical Reviewer (Fokus: Teknis rancang bangun sistem/aplikasi, arsitektur sistem, algoritma)";
			} elseif ($role == 'industry_reviewer') {
				$role_fokus_list[] = "Industry Reviewer (Fokus: Relevansi topik terhadap kebutuhan bisnis/industri saat ini dan manfaat praktis)";
			} elseif ($role == 'ethics_reviewer') {
				$role_fokus_list[] = "Ethics & Feasibility Reviewer (Fokus: Kelayakan jadwal penelitian, etika, potensi risiko, dan aksesibilitas data)";
			}

			if (str_contains($prodi_clean, 'trpl') || str_contains($prodi_clean, 'perangkat lunak')) {
				if ($role == 'problem_reviewer') $instruksi_prodi_list[] = "Prodi TRPL (Problem): Evaluasi kualitas identifikasi masalah dan analisis kebutuhan sistem, serta urgensi pengembangan perangkat lunak.";
				elseif ($role == 'technical_reviewer') $instruksi_prodi_list[] = "Prodi TRPL (Technical): Evaluasi arsitektur sistem, teknologi yang digunakan, serta perancangan database/UML.";
			} elseif (str_contains($prodi_clean, 'pemasaran')) {
				if ($role == 'problem_reviewer') $instruksi_prodi_list[] = "Prodi Pemasaran (Problem): Fokus pada relevansi fenomena pemasaran dan kontribusi terhadap ilmu pemasaran.";
				elseif ($role == 'method_reviewer') $instruksi_prodi_list[] = "Prodi Pemasaran (Method): Fokus pada hubungan antar variabel pemasaran dan indikator kuesioner.";
			}
		}

		$role_fokus = implode("\n- ", $role_fokus_list);
		if (!empty($role_fokus)) $role_fokus = "- " . $role_fokus;
		
		$instruksi_prodi = implode("\n- ", $instruksi_prodi_list);
		if (!empty($instruksi_prodi)) $instruksi_prodi = "- " . $instruksi_prodi;
		
		$interest_penguji = $this->input->post('interest_penguji');
		$interest_instruction = "";
		if (!empty($interest_penguji) && is_array($interest_penguji)) {
		    $interest_str = implode(', ', array_map('trim', $interest_penguji));
		    $interest_instruction = "\n\nKonteks Kepakaran Penguji:\n- Penguji memiliki ketertarikan/kepakaran khusus pada bidang/topik: **" . $interest_str . "**.\n- Hasilkan evaluasi, arahkan pertanyaan teknis untuk sidang, dan berikan saran perbaikan NYATA yang sangat tajam MENGGUNAKAN SUDUT PANDANG KEPAKARAN TERSEBUT.";
		}

		$base_prompt_path = APPPATH . 'AIAcademicExamine.txt';
		$base_prompt = file_exists($base_prompt_path) ? file_get_contents($base_prompt_path) : "";
		
		$prompt = str_replace(
			['{{prodi}}', '{{judul_ta}}', '{{role_penguji}}', '{{instruksi_role}}'],
			[$prodi, $judul_ta, count($role_penguji_arr) . " Peran Spesifik sekaligus:\n" . $role_fokus, "Instruksi Khusus Prodi:\n" . $instruksi_prodi . $interest_instruction],
			$base_prompt
		);

        $this->load->library('Ai_reviewer');
        
        $system_prompt = $prompt . "\n\nAnda adalah AI Reviewer Akademik yang memberikan balasan HARUS dalam format JSON yang valid. Berikan ulasan yang PANJANG, SANGAT MENDETAIL, dan KOMPREHENSIF. Jangan menyingkat penjelasan.";
        $user_document = "Ini adalah isi dokumen mahasiswa yang harus direview:\n\n" . $extracted_text;
        
        $result = $this->ai_reviewer->generate_review($ai_provider, $api_key, $system_prompt, $user_document, $is_local, true);

        if (!$result['status']) {
            echo json_encode(['status' => false, 'msg' => $result['msg']]);
            return;
        }

        $gemini_text = $result['review'];
		
		if (!$gemini_text) {
			echo json_encode(['status' => false, 'msg' => 'Respons dari AI kosong atau tidak dikenali.']);
			return;
		}

		$gemini_text = trim($gemini_text);
		$start_pos = strpos($gemini_text, '{');
		$end_pos = strrpos($gemini_text, '}');
		if ($start_pos !== false && $end_pos !== false) {
			$gemini_text = substr($gemini_text, $start_pos, $end_pos - $start_pos + 1);
		}
		
		// Clean common JSON errors
		// 1. Remove control characters (except newline and carriage return which might be used)
		$gemini_text = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $gemini_text);
		// 2. Remove trailing commas
		$gemini_text = preg_replace('/,\s*([\]}])/m', '$1', $gemini_text);

		$ai_result = json_decode($gemini_text, true);
		if (!$ai_result) {
			$err_msg = json_last_error_msg();
			file_put_contents(FCPATH . 'error_gemini.json', $gemini_text);
			$snippet = substr($gemini_text, 0, 200);
			echo json_encode(['status' => false, 'msg' => 'Gagal mem-parsing hasil JSON dari Gemini. Error: ' . $err_msg . '. Lihat log error_gemini.json. Data: ' . $snippet]);
			return;
		}

		// Update ai_result in the locked row
		$this->db->where('id_judul_skripsi', $id_judul_skripsi)
				 ->where('id_ptk', $id_ptk)
				 ->update('siakad_skripsi_sempro_ai', [
			'ai_result' => json_encode($ai_result)
		]);

		echo json_encode(['status' => true, 'data' => $ai_result]);
	}

	public function cetak_pdf($enkrip)
	{
		try {
			$judul = $this->Qr_model->judul_berita_acara($enkrip);
			$id_sms = $judul->id_sms;

			$usulan_sempro = $this->Qr_model->usulan($judul, $id_sms);

			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8', 'format' => 'A4-P',
				'margin_top' => 30,
				'margin_left' => 30,
				'margin_right' => 15,
				'margin-bottom' => 35
			]);

			$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
			$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
			$penguji = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
			$penguji_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
			$penguji_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);

			$nilai_penguji_1 = $this->Qr_model->nilai_sempro_by_dosen($judul, $judul->dosen_penguji_sempro_1);
			$nilai_penguji_2 = $this->Qr_model->nilai_sempro_by_dosen($judul, $judul->dosen_penguji_sempro_2);
			$nilai_penguji_3 = $this->Qr_model->nilai_sempro_by_dosen($judul, $judul->dosen_penguji_sempro_3);

			$nilai_akhir_penguji = $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_1, $judul->id_judul_skripsi)->nilai_akhir;
			$nilai_akhir_penguji_2 = $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_2, $judul->id_judul_skripsi)->nilai_akhir;
			$nilai_akhir_penguji_3 = $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_3, $judul->id_judul_skripsi)->nilai_akhir;

			$data_nilai = $this->db->select('*')->from('siakad_skripsi_nilai_sempro')
				->where(['id_judul_skripsi' => $judul->id_judul_skripsi, 'id_penilai' => $judul->dosen_pembimbing_1])->get();

			if ($data_nilai->num_rows() > 0) {
				$hasil_sempro = $data_nilai->row()->sempro_hasil;
			} else {
				$hasil_sempro = '';
			}

			$tanggal_qr = tgl_short($judul->seminar_tanggal);
			$datanya = $doping_1->nama_lengkap . ' (' . $tanggal_qr . ')';

			$qr_doping_1 = $this->Qr_model->create_qr($datanya, $doping_1->nidn);

			$output['judul'] = $judul;
			$output['hasil_sempro'] = $hasil_sempro;
			$output['dosen_nama'] = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);

			$output['myTable'] = $this->tabel_sempro($nilai_penguji_1, $nilai_penguji_2, $nilai_penguji_3, $enkrip);
			$output['id_sms'] = $id_sms;
			$output['usulan_sempro'] = $usulan_sempro;
			$output['doping_1'] = $doping_1;
			$output['doping_2'] = $doping_2;
			$output['penguji'] = $penguji;
			$output['penguji_2'] = $penguji_2;
			$output['penguji_3'] = $penguji_3;

			$output['nilai_akhir_penguji'] = !empty($nilai_akhir_penguji) ? $nilai_akhir_penguji : 0;
			$output['nilai_akhir_penguji_2'] = !empty($nilai_akhir_penguji_2) ? $nilai_akhir_penguji_2 : 0;
			$output['nilai_akhir_penguji_3'] = !empty($nilai_akhir_penguji_3) ? $nilai_akhir_penguji_3 : 0;
			$output['qr_doping_1'] = !empty($qr_doping_1) ? (str_starts_with($qr_doping_1, 'data:') ? $qr_doping_1 : FCPATH  . "assets/qrcode/" . $qr_doping_1) : '';
			$output['berita_acara'] = $this->Qr_model->berita_acara($judul);
			$html = $this->load->view('cetak/pdf_nilai_sempro', $output, true);
			$pdfFilePath  = 'nilai_dan_usulan_sempro ' . $judul->nipd . '.pdf';

			$mpdf->SetHTMLHeader($this->Qr_model->header());
			$mpdf->SetHTMLFooter($this->Qr_model->footer());

			$mpdf->setAutoTopMargin = 'stretch';
			$mpdf->setAutoBottomMargin = 'pad';


			$mpdf->WriteHTML($html);
			$mpdf->Output($pdfFilePath, "I");
		} catch (\Throwable $th) {
			echo "Maaf, data tidak ada";
		}
	}

	private function tabel_sempro($nilai_penguji_1, $nilai_penguji_2, $nilai_penguji_3, $enkrip)
	{
		$this->load->library('table');

		$rerata = $this->db->where(['md5(id_judul_skripsi)' => $enkrip])->group_by('id_judul_skripsi')->select('avg(nilai_akhir) as nilai_rerata')->get('siakad_skripsi_nilai_sempro')->row();
		$rerata_x = round($rerata->nilai_rerata, 2) * 1;

		$nilai_a =  0.25 * $nilai_penguji_1->nilai_komponen_a;
		$nilai_b = 0.2 * $nilai_penguji_1->nilai_komponen_b;
		$nilai_c = 0.25 * $nilai_penguji_1->nilai_komponen_c;
		$nilai_d = 0.15 * $nilai_penguji_1->nilai_komponen_d;
		$nilai_e = 0.15 * $nilai_penguji_1->nilai_komponen_e;
		$total_penguji_1 = $nilai_a + $nilai_b + $nilai_c + $nilai_d + $nilai_e;

		$nilai_a2 =  0.25 * $nilai_penguji_2->nilai_komponen_a;
		$nilai_b2 = 0.2 * $nilai_penguji_2->nilai_komponen_b;
		$nilai_c2 = 0.25 * $nilai_penguji_2->nilai_komponen_c;
		$nilai_d2 = 0.15 * $nilai_penguji_2->nilai_komponen_d;
		$nilai_e2 = 0.15 * $nilai_penguji_2->nilai_komponen_e;
		$total_penguji_2 = $nilai_a2 + $nilai_b2 + $nilai_c2 + $nilai_d2 + $nilai_e2;

		$nilai_a3 =  0.25 * $nilai_penguji_3->nilai_komponen_a;
		$nilai_b3 = 0.2 * $nilai_penguji_3->nilai_komponen_b;
		$nilai_c3 = 0.25 * $nilai_penguji_3->nilai_komponen_c;
		$nilai_d3 = 0.15 * $nilai_penguji_3->nilai_komponen_d;
		$nilai_e3 = 0.15 * $nilai_penguji_3->nilai_komponen_e;
		$total_penguji_3 = $nilai_a3 + $nilai_b3 + $nilai_c3 + $nilai_d3 + $nilai_e3;

		$tmpl = $this->Qr_model->template_tabel();

		$this->table->set_template($tmpl);
		$this->table->set_heading(['NO', 'Kriteria Penilaian', 'Bobot <br>(%)',  'Penguji <br>1', 'Penguji<br>2', 'Penguji<br>3']);

		$this->table->add_row(
			'1.',
			'Latar Belakang dan Perumusan masalah:<br>
										a. Ketajaman latar belakang dan perumusan masalah penelitian.<br>
										b. Tujuan Penelitian',
			['data' => '25', 'class' => 'text-center'],
			['data' => round($nilai_a, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_a2, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_a3, 2) * 1, 'class' => 'text-center']
		);

		$this->table->add_row(
			'2.',
			'Tinjauan Pustaka:<br>
										a. Relevansi<br>
										b. Kemutakhiran<br>
										c. Penyusunan Daftar Pustaka',
			['data' => '20', 'class' => 'text-center'],
			['data' => round($nilai_b, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_b2, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_b3, 2) * 1, 'class' => 'text-center']
		);

		$this->table->add_row(
			'3.',
			'Kerangka Konseptual Permasalahan Penelitian',
			['data' => '25', 'class' => 'text-center'],
			['data' => round($nilai_c, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_c2, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_c3, 2) * 1, 'class' => 'text-center']
		);

		$this->table->add_row(
			'4.',
			'Metode Penelitian (Ketepatan dan Kesesuaian Model Yang digunakan)',
			['data' => '15', 'class' => 'text-center'],
			['data' => round($nilai_d, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_d2, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_d3, 2) * 1, 'class' => 'text-center']
		);

		$this->table->add_row(
			'5.',
			'Kemampuan Menjelaskan dan Menjawab Pertanyaan-pertanyaan Terkait Penelitian',
			['data' => '15', 'class' => 'text-center'],
			['data' => round($nilai_e, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_e2, 2) * 1, 'class' => 'text-center'],
			['data' => round($nilai_e3, 2) * 1, 'class' => 'text-center']
		);
		$this->table->add_row(
			'',
			['data' => 'Total'],
			'',
			['data' => round($total_penguji_1, 2) * 1, 'class' => 'text-center'],
			['data' => round($total_penguji_2, 2) * 1, 'class' => 'text-center'],
			['data' => round($total_penguji_3, 2) * 1, 'class' => 'text-center']
		);


		$this->table->add_row(
			'',
			'Rata-rata',
			'',
			['data' => $rerata_x, 'class' => 'text-center', 'colspan' => 3]

		);

		$myTable =  $this->table->generate();
		return $myTable;
	}

	public function search()
	{
		$nim = $this->input->post('query');
		$data_nim = $this->db->like('nipd', $nim, 'AFTER')->limit(10)->get('mahasiswa_pt');

		$output = '';
		$output = '<ul>';


		if ($data_nim->num_rows() > 0) {
			foreach ($data_nim->result() as $key => $value) {
				$output .= '<li onClick="selectNIM(' . $value->nipd . ')">' . $value->nipd . '</li>';
			}
		}
		$output .= '</ul>';
		echo $output;
	}

	public function get_mahasiswa()
	{
		$this->_require_ajax();
		$nim = $this->input->post('nim');
		if (!$nim || !is_numeric($nim)) {
			echo json_encode(null);
			return;
		}
		$row = $this->db
			->select('mahasiswa.nm_pd')
			->from('mahasiswa_pt')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where('mahasiswa_pt.nipd', $nim)
			->get()->row();
		echo json_encode($row);
	}
}
