<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_hasil extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_seminar_hasil_model');
		date_default_timezone_set('Asia/Jakarta');

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		if (!$this->ion_auth->in_group([2])) {
			return show_error('Halaman khusus dosen.');
		}
	}

	// ─── Helper: tolak request non-AJAX ke AJAX endpoint ───────────────────
	private function _require_ajax()
	{
		if (!$this->input->is_ajax_request()) {
			show_error('Forbidden', 403);
		}
	}

	// ─── Helper: validasi nilai komponen (1-7, bukan 4) ────────────────────
	private function _validate_komponen(string $field, array &$data)
	{
		$v = $this->input->post($field);
		if ($v === '' || $v === null) {
			$data['inputerror'][] = $field;
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ((int) $v < 1) {
			$data['inputerror'][] = $field;
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ((int) $v > 7) {
			$data['inputerror'][] = $field;
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ((int) $v === 4) {
			$data['inputerror'][] = $field;
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}
	}

	// ═══════════════════════════════════════════════════════════════════════
	// INDEX — card-based view (N+1 free: 4 batch queries total)
	// ═══════════════════════════════════════════════════════════════════════
	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		// Query 1: daftar mahasiswa semhas milik dosen ini
		$list = $this->db
			->select('s.id_nilai_sempro, s.id_judul_skripsi, s.nipd,
			          s.semhas_nilai_akhir, s.nilai_akhir AS sempro_nilai_saya,
			          s.semhas_catatan_judul, s.semhas_catatan_latar_belakang,
			          s.semhas_catatan_rumusan_masalah, s.semhas_catatan_tujuan_penelitian,
			          s.semhas_catatan_tinjauan_pustaka, s.semhas_catatan_metode_penelitian,
			          s.semhas_catatan_daftar_pustaka, s.semhas_catatan_format_penulisan,
			          pj.judul_ditetapkan, pj.dosen_pembimbing_1, pj.dosen_pembimbing_2,
			          pj.dosen_penguji_sempro_1, pj.dosen_penguji_sempro_2, pj.dosen_penguji_sempro_3,
			          mp.nipd, m.nm_pd, m.foto,
			          ss.tanggal_seminar AS tanggal_semhas,
			          ss.jam AS jam_semhas,
			          ss.tempat AS tempat_semhas,
			          ss.link_dokumen')
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_seminar_sidang ss',
			       'ss.id_judul_skripsi = pj.id_judul_skripsi AND ss.kategori_pengajuan = 2', 'left')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['s.id_penilai' => $id_ptk, 'is_create_form_nilai_semhas' => 1])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->order_by('s.semhas_nilai_akhir', 'asc')
			->get()->result();

		// Kumpulkan id_judul_skripsi unik
		$ids = array_unique(array_column($list, 'id_judul_skripsi'));

		$map_rerata        = [];
		$map_hitung        = [];
		$map_total         = [];
		$map_next_sidang   = [];
		$map_nilai_hasil   = [];
		$map_sempro_rerata = [];
		$map_sempro_hitung = [];
		$map_sempro_detail = [];

		if (!empty($ids)) {
			// Query 2: rata-rata & jumlah penilai semhas dan sempro
			$rows = $this->db
				->select('id_judul_skripsi,
				          AVG(semhas_nilai_akhir) AS nilai_rerata,
				          SUM(CASE WHEN semhas_nilai_akhir > 0 THEN 1 ELSE 0 END) AS hitung,
				          AVG(nilai_akhir) AS sempro_nilai_rerata,
				          SUM(CASE WHEN nilai_akhir > 0 THEN 1 ELSE 0 END) AS sempro_hitung,
				          COUNT(*) AS total_penguji')
				->where_in('id_judul_skripsi', $ids)
				->group_by('id_judul_skripsi')
				->get('siakad_skripsi_nilai_sempro')->result();
			foreach ($rows as $r) {
				$map_rerata[$r->id_judul_skripsi]        = $r->nilai_rerata;
				$map_hitung[$r->id_judul_skripsi]        = (int) $r->hitung;
				$map_total[$r->id_judul_skripsi]         = (int) $r->total_penguji;
				$map_sempro_rerata[$r->id_judul_skripsi] = $r->sempro_nilai_rerata;
				$map_sempro_hitung[$r->id_judul_skripsi] = (int) $r->sempro_hitung;
			}

			// Query 2b: detail nilai sempro per penguji
			$sempro_penguji_rows = $this->db
				->select('n.id_judul_skripsi, n.id_penilai, n.nilai_akhir, d.nama_lengkap')
				->from('siakad_skripsi_nilai_sempro n')
				->join('siakad_skripsi_dosen d', 'd.id_ptk = n.id_penilai', 'left')
				->where_in('n.id_judul_skripsi', $ids)
				->get()->result();
			foreach ($sempro_penguji_rows as $sp) {
				$map_sempro_detail[$sp->id_judul_skripsi][] = $sp;
			}

			// Query 3: cek apakah sudah ada sidang TA (semhas dikunci)
			$locks = $this->db
				->select('id_judul_skripsi')
				->where_in('id_judul_skripsi', $ids)
				->where(['kategori_pengajuan' => 3, 'status_undangan' => 1])
				->get('siakad_skripsi_seminar_sidang')->result();
			foreach ($locks as $r) {
				$map_next_sidang[$r->id_judul_skripsi] = true;
			}

			// Query 4: semua nilai semhas (untuk cek hasil dari PB1)
			$all_nilai = $this->db
				->select('id_judul_skripsi, id_penilai, semhas_hasil, sempro_hasil')
				->where_in('id_judul_skripsi', $ids)
				->get('siakad_skripsi_nilai_sempro')->result();
			foreach ($all_nilai as $r) {
				$map_nilai_hasil[$r->id_judul_skripsi][$r->id_penilai] = $r;
			}
		}

		$data_mahasiswa = [];
		foreach ($list as $rg) {
			$idk = $rg->id_judul_skripsi;

			// Status dosen
			if ($id_ptk == $rg->dosen_pembimbing_1) {
				$rg->status_dosen = 'Pembimbing Utama';
			} elseif ($id_ptk == $rg->dosen_pembimbing_2) {
				$rg->status_dosen = 'Pembimbing Pendamping/Penguji';
			} else {
				$rg->status_dosen = 'Penguji';
			}

			// Hasil dari PB1
			$pb1_row = $map_nilai_hasil[$idk][$rg->dosen_pembimbing_1] ?? null;
			$rg->hasil_semhas  = ($pb1_row && !empty($pb1_row->semhas_hasil))
			                      ? ucfirst((string)$pb1_row->semhas_hasil) : '-';
			$rg->hasil_sempro  = ($pb1_row && !empty($pb1_row->sempro_hasil))
			                      ? ucfirst((string)$pb1_row->sempro_hasil) : '-';

			$rg->nilai_rerata         = isset($map_rerata[$idk]) ? round($map_rerata[$idk], 2) : 0;
			$rg->hitung_dinilai       = $map_hitung[$idk] ?? 0;
			$rg->sempro_nilai_rerata  = isset($map_sempro_rerata[$idk]) ? round($map_sempro_rerata[$idk], 2) : 0;
			$rg->sempro_hitung        = $map_sempro_hitung[$idk] ?? 0;
			$rg->sempro_penguji_list  = $map_sempro_detail[$idk] ?? [];
			$is_akuntansi = $this->db->select('id_sms')->where('id_judul_skripsi', $idk)->get('siakad_skripsi_pengajuan_judul')->row()->id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';
			$rg->total_penguji        = $is_akuntansi ? 2 : ($map_total[$idk] ?? 0);
			$rg->is_locked            = isset($map_next_sidang[$idk]);

			$data_mahasiswa[] = $rg;
		}

		$output = [
			'judul_besar'     => 'Seminar Hasil',
			'judul_kecil'     => 'Politeknik WBI',
			'menu_aktif'      => 'm_seminar_hasil',
			'parent_skripsi'  => 'active open',
			'data_mahasiswa'  => $data_mahasiswa,
			'content'         => 'E_skripsi_seminar_hasil/E_skripsi_seminar_hasil_view',
			'after_page'      => 'E_skripsi_seminar_hasil/E_skripsi_seminar_hasil_after_page',
		];
		$this->load->view('master_page', $output);
	}

	// ═══════════════════════════════════════════════════════════════════════
	// AJAX: ambil data untuk modal (nilai & catatan & record sempro)
	// ═══════════════════════════════════════════════════════════════════════
	public function ajax_edit($id)
	{
		$this->_require_ajax();

		// Hanya boleh mengakses data milik sendiri
		$id_ptk = $this->session->userdata('id_ptk');
		$data   = $this->E_skripsi_seminar_hasil_model->get_by_id($id);
		if (!$data || $data->id_penilai != $id_ptk) {
			echo json_encode(['error' => 'Forbidden']);
			return;
		}

		// Trim jam agar hanya hh:mm
		$data->semhas_jam_mulai   = substr($data->semhas_jam_mulai  ?? '', 0, 5);
		$data->semhas_jam_selesai = substr($data->semhas_jam_selesai ?? '', 0, 5);

		// Tabel nilai Semhas seluruh dosen (1 query)
		$id_sms    = $this->session->userdata('prodi');
		$nilai_all = $this->db
			->select('d.nama_lengkap, n.semhas_nilai_akhir')
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = n.id_penilai')
			->where(['n.id_judul_skripsi' => $data->id_judul_skripsi, 'd.id_sms' => $id_sms])
			->get()->result();

		$tbl = '<table class="table table-bordered table-condensed" style="font-size:13px;">
		         <thead><tr><th>Dosen</th><th>Nilai Akhir</th></tr></thead><tbody>';
		$total_nilai = 0;
		$jumlah_penguji = 0;
		foreach ($nilai_all as $v) {
			$tbl .= '<tr><td>' . htmlspecialchars($v->nama_lengkap) . '</td><td>'
			      . ($v->semhas_nilai_akhir * 1) . '</td></tr>';
			$total_nilai += $v->semhas_nilai_akhir;
			$jumlah_penguji++;
		}
		$tbl .= '</tbody></table>';
		$data->tbl_nilai = $tbl;
		$data->rerata_nilai = $jumlah_penguji > 0 ? $total_nilai / $jumlah_penguji : 0;

		// ─── AMBIL RECORD LENGKAP SEMINAR PROPOSAL (SEMPRO) ───────────────
		$sempro_all = $this->db
			->select('n.*, d.nama_lengkap')
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = n.id_penilai')
			->where(['n.id_judul_skripsi' => $data->id_judul_skripsi, 'd.id_sms' => $id_sms])
			->get()->result();

		// Jadwal Sempro dari tabel seminar_sidang (kategori_pengajuan = 1)
		$sempro_schedule = $this->db
			->where(['id_judul_skripsi' => $data->id_judul_skripsi, 'kategori_pengajuan' => 1])
			->get('siakad_skripsi_seminar_sidang')->row();

		$data->sempro_tanggal      = $sempro_schedule ? $sempro_schedule->tanggal_seminar : ($data->seminar_tanggal ?? null);
		$data->sempro_jam          = $sempro_schedule ? substr($sempro_schedule->jam, 0, 5) : ($data->seminar_jam_mulai ?? null);
		$data->sempro_tempat       = $sempro_schedule ? $sempro_schedule->tempat : ($data->seminar_tempat ?? '-');
		$data->sempro_link_dokumen = $sempro_schedule ? $sempro_schedule->link_dokumen : null;

		$total_sempro = 0;
		$jml_penguji_sempro = 0;
		$sempro_penguji_data = [];

		foreach ($sempro_all as $sp) {
			// Peran penguji di sempro
			if ($sp->id_penilai == $data->dosen_pembimbing_1) {
				$sp->role_dosen = 'Pembimbing Utama';
				$data->sempro_hasil_akhir = !empty($sp->sempro_hasil) ? ucfirst((string)$sp->sempro_hasil) : '-';
			} elseif ($sp->id_penilai == $data->dosen_pembimbing_2) {
				$sp->role_dosen = 'Pembimbing Pendamping';
			} else {
				$sp->role_dosen = 'Penguji';
			}

			if ($sp->nilai_akhir > 0) {
				$total_sempro += $sp->nilai_akhir;
				$jml_penguji_sempro++;
			}

			$sempro_penguji_data[] = [
				'nama_lengkap'              => $sp->nama_lengkap,
				'role_dosen'                => $sp->role_dosen,
				'nilai_a'                   => $sp->nilai_komponen_a * 1,
				'nilai_b'                   => $sp->nilai_komponen_b * 1,
				'nilai_c'                   => $sp->nilai_komponen_c * 1,
				'nilai_d'                   => $sp->nilai_komponen_d * 1,
				'nilai_e'                   => $sp->nilai_komponen_e * 1,
				'nilai_akhir'               => $sp->nilai_akhir * 1,
				'catatan_judul'             => $sp->catatan_judul,
				'catatan_latar_belakang'    => $sp->catatan_latar_belakang,
				'catatan_rumusan_masalah'   => $sp->catatan_rumusan_masalah,
				'catatan_tujuan_penelitian' => $sp->catatan_tujuan_penelitian,
				'catatan_tinjauan_pustaka'  => $sp->catatan_tinjauan_pustaka,
				'catatan_metode_penelitian' => $sp->catatan_metode_penelitian,
				'catatan_daftar_pustaka'    => $sp->catatan_daftar_pustaka,
				'catatan_format_penulisan'  => $sp->catatan_format_penulisan,
			];
		}

		$data->sempro_rerata = $jml_penguji_sempro > 0 ? round($total_sempro / $jml_penguji_sempro, 2) : 0;
		$data->sempro_penguji_data = $sempro_penguji_data;
		if (empty($data->sempro_hasil_akhir)) {
			$data->sempro_hasil_akhir = '-';
		}

		echo json_encode($data);
	}

	// ═══════════════════════════════════════════════════════════════════════
	// AJAX: simpan nilai semhas
	// ═══════════════════════════════════════════════════════════════════════
	public function ajax_update()
	{
		$this->_require_ajax();
		$this->_validate();

		$id_ptk       = $this->session->userdata('id_ptk');
		$id_nilai     = (int) $this->input->post('id_nilai_sempro');

		// Pastikan record milik dosen yang login
		$existing = $this->db
			->select('id_nilai_sempro')
			->where(['id_nilai_sempro' => $id_nilai, 'id_penilai' => $id_ptk])
			->get('siakad_skripsi_nilai_sempro')->row();
		if (!$existing) {
			echo json_encode(['status' => FALSE, 'error' => 'Forbidden']);
			return;
		}

		$a = (float) $this->input->post('semhas_nilai_a');
		$b = (float) $this->input->post('semhas_nilai_b');
		$c = (float) $this->input->post('semhas_nilai_c');
		$d = (float) $this->input->post('semhas_nilai_d');
		$e = (float) $this->input->post('semhas_nilai_e');
		$f = (float) $this->input->post('semhas_nilai_f');
		$akhir = round(0.20*$a + 0.10*$b + 0.15*$c + 0.15*$d + 0.30*$e + 0.10*$f, 4);

		$this->E_skripsi_seminar_hasil_model->update(
			['id_nilai_sempro' => $id_nilai],
			[
				'semhas_nilai_a'     => $a,
				'semhas_nilai_b'     => $b,
				'semhas_nilai_c'     => $c,
				'semhas_nilai_d'     => $d,
				'semhas_nilai_e'     => $e,
				'semhas_nilai_f'     => $f,
				'semhas_nilai_akhir' => $akhir,
				'change_by'          => $this->ion_auth->user()->row()->email,
				'change_date'        => date('Y-m-d H:i:s'),
			]
		);
		echo json_encode(['status' => TRUE]);
	}

	// ═══════════════════════════════════════════════════════════════════════
	// AJAX: simpan catatan / usulan revisi
	// ═══════════════════════════════════════════════════════════════════════
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

		$this->E_skripsi_seminar_hasil_model->update(
			['id_nilai_sempro' => $id_nilai],
			[
				'semhas_catatan_judul'              => $this->input->post('semhas_catatan_judul'),
				'semhas_catatan_latar_belakang'     => $this->input->post('semhas_catatan_latar_belakang'),
				'semhas_catatan_rumusan_masalah'    => $this->input->post('semhas_catatan_rumusan_masalah'),
				'semhas_catatan_tujuan_penelitian'  => $this->input->post('semhas_catatan_tujuan_penelitian'),
				'semhas_catatan_tinjauan_pustaka'   => $this->input->post('semhas_catatan_tinjauan_pustaka'),
				'semhas_catatan_metode_penelitian'  => $this->input->post('semhas_catatan_metode_penelitian'),
				'semhas_catatan_daftar_pustaka'     => $this->input->post('semhas_catatan_daftar_pustaka'),
				'semhas_catatan_format_penulisan'   => $this->input->post('semhas_catatan_format_penulisan'),
				'semhas_desain'                     => $this->input->post('semhas_desain'),
				'semhas_pengujian'                  => $this->input->post('semhas_pengujian'),
				'semhas_kesimpulan'                 => $this->input->post('semhas_kesimpulan'),
				'change_by'                         => $this->ion_auth->user()->row()->email,
				'change_date'                       => date('Y-m-d H:i:s'),
			]
		);
		echo json_encode(['status' => TRUE]);
	}

	// ═══════════════════════════════════════════════════════════════════════
	// AJAX: simpan berita acara
	// ═══════════════════════════════════════════════════════════════════════
	public function ajax_update_berita_acara()
	{
		$this->_require_ajax();
		$this->_validate_berita_acara();

		$id_ptk      = $this->session->userdata('id_ptk');
		$id_nilai    = (int) $this->input->post('id_nilai_sempro');
		$id_judul    = (int) $this->input->post('id_judul_skripsi');

		// Hanya Pembimbing Utama yang boleh isi BA
		$penguji_row = $this->db
			->select('dosen_pembimbing_1')
			->where('id_judul_skripsi', $id_judul)
			->get('siakad_skripsi_pengajuan_judul')->row();
		if (!$penguji_row || $penguji_row->dosen_pembimbing_1 != $id_ptk) {
			echo json_encode(['status' => FALSE, 'error' => 'Hanya Pembimbing Utama yang dapat mengisi Berita Acara.']);
			return;
		}

		// Ganti judul jika diminta
		if ($this->input->post('status_revisi_judul')) {
			$judul_lama = $this->input->post('judul_ditetapkan_1');
			$judul_baru = $this->input->post('judul_ditetapkan_2');
			$user       = $this->ion_auth->user()->row();

			$this->db->insert('siakad_skripsi_judul_lama', [
				'id_judul_skripsi' => $id_judul,
				'judul_lama'       => $judul_lama,
				'create_by'        => $user->first_name,
				'create_date'      => date('Y-m-d H:i:s'),
			]);
			$this->db->update(
				'siakad_skripsi_pengajuan_judul',
				['judul_ditetapkan' => $judul_baru, 'status_revisi_judul' => 1],
				['id_judul_skripsi' => $id_judul]
			);
		}

		$hari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		$tgl_post   = $this->input->post('semhas_tanggal');
		$hari_indo  = $tgl_post ? $hari_array[date('w', strtotime($tgl_post))] : '';

		$ok = $this->E_skripsi_seminar_hasil_model->update_berita_acara_sempro(
			['id_nilai_sempro' => $id_nilai],
			[
				'semhas_hari'           => $hari_indo,
				'semhas_tanggal'        => $this->input->post('semhas_tanggal'),
				'semhas_moderator'      => $this->input->post('semhas_moderator'),
				'semhas_nim_moderator'  => $this->input->post('semhas_nim_moderator'),
				'semhas_pembahas_1'     => $this->input->post('semhas_pembahas_1'),
				'semhas_nim_pembahas_1' => $this->input->post('semhas_nim_pembahas_1'),
				'semhas_pembahas_2'     => $this->input->post('semhas_pembahas_2'),
				'semhas_nim_pembahas_2' => $this->input->post('semhas_nim_pembahas_2'),
				'semhas_jumlah_mahasiswa' => $this->input->post('semhas_jumlah_mahasiswa'),
				'semhas_tempat'         => $this->input->post('semhas_tempat'),
				'semhas_jam_mulai'      => $this->input->post('semhas_jam_mulai'),
				'semhas_jam_selesai'    => $this->input->post('semhas_jam_selesai'),
				'semhas_hasil'          => $this->input->post('semhas_hasil'),
				'change_by'             => $this->ion_auth->user()->row()->email,
				'change_date'           => date('Y-m-d H:i:s'),
			]
		);
		echo json_encode(['status' => (bool) $ok]);
	}

	// ═══════════════════════════════════════════════════════════════════════
	// AJAX: lookup NIM untuk auto-fill nama moderator / pembahas
	// ═══════════════════════════════════════════════════════════════════════
	public function get_mahasiswa()
	{
		$this->_require_ajax();
		$nim  = $this->input->post('nim');
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

	// ═══════════════════════════════════════════════════════════════════════
	// CETAK PDF Berita Acara
	// ═══════════════════════════════════════════════════════════════════════
	public function cetak_pdf($enkrip)
	{
		$this->load->model('Qr_model');
		try {
			$judul       = $this->Qr_model->judul_berita_acara($enkrip);
			$id_sms      = $judul->id_sms;
			$usulan      = $this->Qr_model->usulan($judul, $id_sms);

			$mpdf = new \Mpdf\Mpdf([
				'mode'          => 'utf-8',
				'format'        => 'A4-P',
				'margin_top'    => 30,
				'margin_left'   => 30,
				'margin_right'  => 15,
				'margin-bottom' => 35,
			]);

			$doping_1  = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
			$doping_2  = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
			$penguji   = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
			$penguji_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
			$penguji_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);

			$nv1 = $this->Qr_model->nilai_semhas_by_dosen($judul, $judul->dosen_penguji_sempro_1);
			$nv2 = $this->Qr_model->nilai_semhas_by_dosen($judul, $judul->dosen_penguji_sempro_2);
			$nv3 = $this->Qr_model->nilai_semhas_by_dosen($judul, $judul->dosen_penguji_sempro_3);

			$tanggal_qr = tgl_short($judul->semhas_tanggal);
			$qr_doping  = $this->Qr_model->create_qr(
				$doping_1->nama_lengkap . ' (' . $tanggal_qr . ')',
				$doping_1->nidn
			);
			$rata_rata  = $this->Qr_model->nilai_rata_rata_akhir_semhas($judul);

			// Ambil hasil semhas (1 query)
			$hasil_row = $this->db
				->select('semhas_hasil')
				->where(['id_judul_skripsi' => $judul->id_judul_skripsi,
				         'id_penilai'       => $judul->dosen_pembimbing_1])
				->get('siakad_skripsi_nilai_sempro')->row();
			$hasil_semhas = $hasil_row ? $hasil_row->semhas_hasil : '';

			$output = [
				'judul'          => $judul,
				'hasil_semhas'   => $hasil_semhas,
				'dosen_nama'     => $doping_1,
				'myTable'        => $this->tabel_semhas($nv1, $nv2, $nv3, $enkrip),
				'id_sms'         => $id_sms,
				'usulan_semhas'  => $usulan,
				'doping_1'       => $doping_1,
				'doping_2'       => $doping_2,
				'penguji'        => $penguji,
				'penguji_2'      => $penguji_2,
				'penguji_3'      => $penguji_3,
				'nilai_semhas_p1' => $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_1, $judul->id_judul_skripsi),
				'nilai_semhas_p2' => $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_2, $judul->id_judul_skripsi),
				'nilai_semhas_p3' => $this->Qr_model->nilai_akhir_by_dosen($judul->dosen_penguji_sempro_3, $judul->id_judul_skripsi),
				'qr_doping_1'    => !empty($qr_doping) ? (str_starts_with($qr_doping, 'data:') ? $qr_doping : FCPATH . 'assets/qrcode/' . $qr_doping) : '',
				'rata_rata'      => $rata_rata,
				'berita_acara'   => $this->Qr_model->berita_acara($judul),
			];

			$html = $this->load->view('cetak/pdf_semhas', $output, true);
			$mpdf->SetHTMLHeader($this->Qr_model->header());
			$mpdf->SetHTMLFooter($this->Qr_model->footer());
			$mpdf->setAutoTopMargin    = 'stretch';
			$mpdf->setAutoBottomMargin = 'pad';
			$mpdf->WriteHTML($html);
			$mpdf->Output('Seminar_hasil_tugas_akhir_' . $judul->nipd . '.pdf', 'I');
		} catch (\Throwable $th) {
			echo 'Maaf, data tidak ada.';
		}
	}

	// ═══════════════════════════════════════════════════════════════════════
	// PRIVATE: generate tabel nilai PDF
	// ═══════════════════════════════════════════════════════════════════════
	private function tabel_semhas($p1, $p2, $p3, $enkrip)
	{
		$this->load->library('table');
		$rerata_row = $this->db
			->select('AVG(semhas_nilai_akhir) AS nilai_rerata')
			->where('MD5(id_judul_skripsi)', $enkrip)
			->group_by('id_judul_skripsi')
			->get('siakad_skripsi_nilai_sempro')->row();
		$rerata_x = $rerata_row ? $rerata_row->nilai_rerata : 0;

		$tmpl = $this->Qr_model->template_tabel();
		$this->table->set_template($tmpl);
		$this->table->set_heading(['NO', 'Kriteria Penilaian', 'Bobot (%)', 'Penguji 1', 'Penguji 2', 'Penguji 3']);

		$calc = function($row, $weights) {
			if (!$row) return array_fill(0, count($weights), 0);
			return [
				round(0.20 * $row->semhas_nilai_a, 2),
				round(0.10 * $row->semhas_nilai_b, 2),
				round(0.15 * $row->semhas_nilai_c, 2),
				round(0.15 * $row->semhas_nilai_d, 2),
				round(0.30 * $row->semhas_nilai_e, 2),
				round(0.10 * $row->semhas_nilai_f, 2),
			];
		};
		[$a1,$b1,$c1,$d1,$e1,$f1] = $calc($p1, []);
		[$a2,$b2,$c2,$d2,$e2,$f2] = $calc($p2, []);
		[$a3,$b3,$c3,$d3,$e3,$f3] = $calc($p3, []);

		$kriteria = [
			['no'=>'1.','label'=>'Latar Belakang & Perumusan Masalah','bobot'=>20,'v'=>[$a1,$a2,$a3]],
			['no'=>'2.','label'=>'Tinjauan Pustaka','bobot'=>10,'v'=>[$b1,$b2,$b3]],
			['no'=>'3.','label'=>'Kerangka Konseptual','bobot'=>15,'v'=>[$c1,$c2,$c3]],
			['no'=>'4.','label'=>'Metode Penelitian','bobot'=>15,'v'=>[$d1,$d2,$d3]],
			['no'=>'5.','label'=>'Hasil & Pembahasan','bobot'=>30,'v'=>[$e1,$e2,$e3]],
			['no'=>'6.','label'=>'Kemampuan Menjelaskan','bobot'=>10,'v'=>[$f1,$f2,$f3]],
		];
		foreach ($kriteria as $k) {
			$this->table->add_row(
				$k['no'], $k['label'],
				['data' => $k['bobot'], 'class' => 'text-center'],
				['data' => $k['v'][0] * 1, 'class' => 'text-center'],
				['data' => $k['v'][1] * 1, 'class' => 'text-center'],
				['data' => $k['v'][2] * 1, 'class' => 'text-center']
			);
		}
		$t1 = $a1+$b1+$c1+$d1+$e1+$f1;
		$t2 = $a2+$b2+$c2+$d2+$e2+$f2;
		$t3 = $a3+$b3+$c3+$d3+$e3+$f3;
		$this->table->add_row('','Total','',
			['data'=>round($t1,2)*1,'class'=>'text-center'],
			['data'=>round($t2,2)*1,'class'=>'text-center'],
			['data'=>round($t3,2)*1,'class'=>'text-center']
		);
		$this->table->add_row('','Rata-rata','',
			['data'=>round($rerata_x,2)*1,'class'=>'text-center','colspan'=>'3']
		);
		return $this->table->generate();
	}

	// ═══════════════════════════════════════════════════════════════════════
	// PRIVATE: validasi input nilai komponen
	// ═══════════════════════════════════════════════════════════════════════
	private function _validate()
	{
		$data = ['error_string' => [], 'inputerror' => [], 'status' => TRUE];
		foreach (['semhas_nilai_a','semhas_nilai_b','semhas_nilai_c',
		          'semhas_nilai_d','semhas_nilai_e','semhas_nilai_f'] as $f) {
			$this->_validate_komponen($f, $data);
		}
		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	// ═══════════════════════════════════════════════════════════════════════
	// PRIVATE: validasi input berita acara
	// ═══════════════════════════════════════════════════════════════════════
	private function _validate_berita_acara()
	{
		$data = ['error_string' => [], 'inputerror' => [], 'status' => TRUE];

		$required_fields = [
			'semhas_tanggal'    => 'Tanggal',
			'semhas_jam_mulai'  => 'Jam Mulai',
			'semhas_jam_selesai'=> 'Jam Selesai',
		];
		foreach ($required_fields as $field => $label) {
			if ($this->input->post($field) === '') {
				$data['inputerror'][]  = $field;
				$data['error_string'][]= $label . ' harus diisi.';
				$data['status'] = FALSE;
			}
		}

		foreach (['semhas_nim_moderator','semhas_nim_pembahas_1','semhas_nim_pembahas_2'] as $nim_field) {
			$nim = $this->input->post($nim_field);
			if (!is_numeric($nim)) {
				$data['inputerror'][]  = $nim_field;
				$data['error_string'][]= 'Harus diisi dengan angka.';
				$data['status'] = FALSE;
			} elseif (!$this->nim_check($nim)) {
				$data['inputerror'][]  = $nim_field;
				$data['error_string'][]= 'NIM tidak ditemukan.';
				$data['status'] = FALSE;
			}
		}

		$jam_re = '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/';
		foreach (['semhas_jam_mulai','semhas_jam_selesai'] as $jf) {
			if (!preg_match($jam_re, $this->input->post($jf))) {
				$data['inputerror'][]  = $jf;
				$data['error_string'][]= 'Format jam salah, gunakan hh:mm.';
				$data['status'] = FALSE;
			}
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	// ═══════════════════════════════════════════════════════════════════════
	// PRIVATE: cek apakah NIM ada di database
	// ═══════════════════════════════════════════════════════════════════════
	public function nim_check($nipd)
	{
		return $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->num_rows() > 0;
	}
}
