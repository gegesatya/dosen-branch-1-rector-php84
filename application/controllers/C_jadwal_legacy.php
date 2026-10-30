<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal_legacy
{
	public function __get($var)
	{
		return get_instance()->$var;
	}

	public function __construct()
	{
		$this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
		$this->load->model('Jadwal_legacy_model', 'Jadwal_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');

		$cache_key = "jadwal_krs_stats_{$id_ptk}_{$id_smt}";
		$krs_cache = $this->cache->get($cache_key);

		if ($krs_cache === FALSE) {
			// 0. Check KRS Period (siakad_setting)
			$setting        = $this->db->get('siakad_setting')->row();
			$is_krs_period  = ($setting && !empty($setting->tgl_akhir_krs) && date('Y-m-d H:i:s') <= $setting->tgl_akhir_krs);

			$list_approval = [];
			$list_draft    = [];
			$list_belum    = [];

			if ($is_krs_period) {
				// 1. Mahasiswa Butuh Approval (Status 2)
				$list_approval = $this->db
					->select('mp.nipd, m.nm_pd, m.foto')
					->from('siakad_krs k')
					->join('mahasiswa_pt mp', 'mp.nipd = k.nipd')
					->join('mahasiswa m', 'm.id_pd = mp.id_pd')
					->where(['m.dosen_pa' => $id_ptk, 'k.id_status_krs' => 2, 'k.id_smt' => $id_smt])
					->group_by(['mp.nipd', 'm.nm_pd', 'm.foto'])
					->get()->result();

				// 2. Mahasiswa Sedang Draft (Status 1)
				$list_draft = $this->db
					->select('mp.nipd, m.nm_pd, m.foto')
					->from('siakad_krs k')
					->join('mahasiswa_pt mp', 'mp.nipd = k.nipd')
					->join('mahasiswa m', 'm.id_pd = mp.id_pd')
					->where(['m.dosen_pa' => $id_ptk, 'k.id_status_krs' => 1, 'k.id_smt' => $id_smt])
					->group_by(['mp.nipd', 'm.nm_pd', 'm.foto'])
					->get()->result();

				// 3. Mahasiswa Belum KRS — gunakan NOT EXISTS agar tidak perlu tarik semua NIM ke PHP
				$list_belum = $this->db
					->select('mp.nipd, m.nm_pd, m.foto')
					->from('mahasiswa m')
					->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
					->join('feeder_lulus_do f', 'f.nim = mp.nipd', 'left')
					->where([
						'm.dosen_pa'    => $id_ptk,
						'mp.tgl_keluar' => NULL,
					])
					->where('f.nim IS NULL')
					->where("NOT EXISTS (
						SELECT 1 FROM siakad_krs krs
						WHERE krs.nipd = mp.nipd
						  AND krs.id_smt = " . $this->db->escape($id_smt) . "
					)")
					->get()->result();
			}

			$krs_cache = [
				'need_approval' => $list_approval,
				'draft'         => $list_draft,
				'belum_krs'     => $list_belum,
				'msg_krs'       => (count($list_approval) > 0 || count($list_draft) > 0) ? 'Ada mahasiswa membutuhkan persetujuan KRS atau sedang mengisi KRS.' : '',
				'jumlah'        => count($list_approval)
			];

			$this->cache->save($cache_key, $krs_cache, 1800); // 30 mins
		}

		if (!empty($krs_cache['msg_krs'])) {
			$this->session->set_flashdata('msg_krs', $krs_cache['msg_krs']);
			$this->session->set_flashdata('jumlah', $krs_cache['jumlah']);
		}

		$output['krs_stats'] = [
			'need_approval' => $krs_cache['need_approval'],
			'draft'         => $krs_cache['draft'],
			'belum_krs'     => $krs_cache['belum_krs'],
		];

		$output['judul_besar'] = 'Jadwal Dosen ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif']  = 'm_jadwal';
		$output['parent_2']    = 'active open';
		$output['content']     = 'jadwal/jadwal_view_legacy';
		$output['after_page']  = 'jadwal/jadwal_after_page_legacy';
		$output['dd_rps']      = $this->Jadwal_model->f_dropdown_rps();

		$this->load->view('master_page', $output);
	}

	/**
	 * ajax_list — DataTables server-side handler.
	 */
	public function ajax_list()
	{
		// ── Sanitize DataTables POST params ──────────────────────────────────
		$draw   = (int) $this->input->post('draw');
		$start  = (int) $this->input->post('start');

		$list = $this->Jadwal_model->get_datatables();

		if (empty($list)) {
			echo json_encode([
				'draw'            => $draw,
				'recordsTotal'    => $this->Jadwal_model->count_all(),
				'recordsFiltered' => $this->Jadwal_model->count_filtered(),
				'data'            => [],
			]);
			return;
		}

		// ── Kumpulkan semua id_jadwal ─────────────────────────────────────────
		$ids = array_unique(array_column(
			array_map(fn($r) => (array) $r, $list),
			'id'
		));

		// ── Batch Query 1: Detail kelas + mata kuliah (WHERE IN) ──────────────
		$all_details = [];
		$details_query = $this->db
			->select('jd.id_jadwal, kk.nm_kls, mk.nm_mk, mk.kode_mk, mk.id_mk, mk.sks_mk')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where_in('jd.id_jadwal', $ids)
			->order_by('jd.id_jadwal, kk.nm_kls')
			->get()->result();

		foreach ($details_query as $d) {
			$all_details[$d->id_jadwal][] = $d;
		}

		// ── Batch Query 2: RPS per jadwal (WHERE IN) ──────────────────────────
		$all_rps = [];
		$rps_query = $this->db
			->select('id_jadwal, id_rps')
			->where_in('id_jadwal', $ids)
			->get('siakad_new_rps_mk_prodi')->result();

		foreach ($rps_query as $r) {
			$all_rps[$r->id_jadwal] = $r;
		}

		// ── Rakit output ──────────────────────────────────────────────────────
		$data = [];
		$no   = $start;

		foreach ($list as $rg) {
			$no++;
			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);

			$skema = $html_publish = $html_nilai = $html_download_nilai = $dinilai = '';

			// Detail kelas dari batch (sudah di-map)
			$details = $all_details[$rg->id] ?? [];

			if (empty($details)) {
				$nmkls = $kk = $kuri = '-';
			} else {
				$nmkls = $kk = $kuri = '';
				foreach ($details as $value) {
					$kk    .= '[' . $value->kode_mk . '] ' . $value->nm_mk . ' (' . ((int) $value->sks_mk) . ' SKS)<br>';
					$nmkls .= $value->nm_kls . '<br>';
				}
			}

			// Nilai sudah di-aggregate di model — langsung pakai
			$jumlah_siswa = (int) $rg->jumlah_siswa;
			$sudah_nilai  = (int) $rg->sudah_nilai;
			$rencana      = (int) $rg->rencana;
			$realisasi    = (int) $rg->realisasi;
			$belum_nilai  = $jumlah_siswa - $sudah_nilai;

			$publish = $rg->is_lapor_nilai == 1
				? '<span class="text-success">Sudah Publish</span>'
				: '<span class="text-danger">Belum Publish</span>';

			if ($rg->is_skema == 1) {
				$skema = '<small>Skema Nilai :<p>'
					. '<b>Sikap = </b>' . (int) $rg->sikap
					. ' %, <b>Quiz =</b> ' . (int) $rg->quiz
					. '%, <b>Tugas =</b> ' . (int) $rg->tugas_terstruktur
					. ' %, <b>UTS = </b>' . (int) $rg->uts
					. ' %, <b>UAS = </b>' . (int) $rg->uas . ' %</p>'
					. '<p><b>Unjuk Kinerja = </b>' . (int) $rg->unjuk_kinerja
					. ' %, <b>Proyek = </b>' . (int) $rg->proyek
					. ' %, <b>Portofolio = </b>' . (int) $rg->portofolio
					. ' %, <b>Produk = </b>' . (int) $rg->produk
					. ' %, <b>Presentasi = </b>' . (int) $rg->presentasi
					. '%</p></small>';

				$html_nilai = '<a href="' . site_url('C_isi_nilai_excel/index/' . (int) $rg->id) . '"
					class="btn btn-sm btn-default green-stripe btn-block margin-top-10">
					Isi Nilai</a>';

				$html_publish = '<a href="' . site_url('C_lapor_nilai') . '"
					class="btn btn-sm btn-default blue-stripe btn-block margin-top-10">
					Publish</a>';

				$html_download_nilai = '<a href="' . site_url('C_summary_nilai/cetak_pdf_nilai/' . (int) $rg->id) . '" target="new"
					class="btn btn-sm btn-default blue-stripe margin-top-10">
					PDF Nilai</a>';

				$dinilai = '<b>Sudah Dinilai : ' . $sudah_nilai . ', Belum Dinilai : ' . $belum_nilai . '</b>';
			}

			// RPS dari batch
			$data_rps_row = $all_rps[$rg->id] ?? null;
			$data_rps_id  = $data_rps_row ? $data_rps_row->id_rps : null;

			// Tombol RPS
			$html_copy       = '';
			$html_dialog_rps = '<a href="javascript:void(0)"
				class="btn btn-sm btn-default green-stripe btn-block"
				onclick="ajax_pilih_rps(\'' . (int) $rg->id . '\')">
				Pilih RPS</a>';

			$html_ganti_rps = '<a href="javascript:void(0)"
				class="btn btn-sm btn-default red-stripe btn-block"
				onclick="delete_rps(\'' . (int) $rg->id . '\')">
				Reset RPS</a>';

			$html_skema_nilai = '<a href="javascript:void(0)"
				class="btn btn-sm btn-default green-stripe btn-block"
				onclick="edit_skema_nilai(\'' . (int) $rg->id . '\')">
				Skema Nilai</a>';

			if ($data_rps_id) {
				$html_preview = '<a href="' . site_url('C_rps_new/preview_rps_baru/' . md5($data_rps_id)) . '"
					class="btn btn-default btn-sm green-stripe btn-block margin-bottom-5" target="_new" title="Preview RPS"> View RPS</a>';
				$html_kontrak = '<a href="' . site_url('C_rps_new/preview_kontrak/' . md5($data_rps_id)) . '"
					class="btn btn-default btn-sm green-stripe btn-block margin-bottom-5" target="_new" title="Preview Kontrak"> View Kontrak</a>';
				$html_tugas   = '<a href="' . site_url('C_rps_new/preview_rps_rancangan_tugas/' . md5($data_rps_id)) . '"
					class="btn btn-default btn-sm green-stripe btn-block margin-bottom-5" target="_new" title="Preview Rancangan Tugas"> View Rancangan Tugas</a>';
				$html_copy    = '<a href="javascript:void(0)"
					class="btn btn-sm btn-default green-stripe btn-block"
					onclick="ajax_copy(\'' . $data_rps_id . '\',\'' . (int) $rg->id . '\')">
					Generate Meeting</a>';

				$col_rps = $html_preview . $html_kontrak . $html_tugas . $html_ganti_rps;
			} else {
				$col_rps = $html_dialog_rps;
			}

			if ($rg->is_generate_bap == 0) {
				$col_vc = $html_copy;
			} else {
				$col_vc = '<a href="' . site_url('C_rencana_pertemuan/index/' . (int) $rg->id) . '"
					class="btn btn-default btn-sm green-stripe" title="Virtual Class"> Virtual Class</a>';
			}

			$row   = [];
			$row[] = $no;
			$row[] = $rg->nama_hari . '<br><small>' . $jam . '<br>' . $rg->nama_ruang . '</small>';
			$row[] = $kk . '<br>' . $skema . '<br>' . $dinilai . '<br>' . $html_download_nilai;
			$row[] = 'Jumlah Siswa : ' . $jumlah_siswa
				. '<br>Realisasi : ' . $realisasi
				. '<br>Rencana : ' . $rencana
				. '<br>Status Publish : ' . $publish;
			$row[] = $col_rps;
			$row[] = $html_skema_nilai . $html_nilai . $html_publish;
			$row[] = $col_vc;

			$data[] = $row;
		}

		echo json_encode([
			'draw'            => $draw,
			'recordsTotal'    => $this->Jadwal_model->count_all(),
			'recordsFiltered' => $this->Jadwal_model->count_filtered(),
			'data'            => $data,
		]);
	}

	public function get_copy_data($id_rps)
	{
		$id_rps = $this->security->xss_clean($id_rps);

		$data = $this->db
			->select('a.*, b.nm_mk as general_mk, b.koordinator_rps, b.tanggal_rps, b.kode_mk, b.nm_mk, b.dosen_pengampu, sd.nm_ptk,
				(SELECT COUNT(*) FROM siakad_new_rps_online_detail d WHERE d.id_rps = a.id_rps) AS jumlah_detail_pertemuan')
			->from('siakad_new_rps_mk_prodi a')
			->join('siakad_new_rps_online b', 'b.id_rps = a.id_rps')
			->join('siakad_dosen sd', 'sd.id_ptk = b.koordinator_rps', 'left')
			->where('a.id_rps', $id_rps)
			->get()->row();

		echo json_encode($data);
	}

	public function get_rps($id_jadwal)
	{
		$id_jadwal = (int) $id_jadwal;

		$data = $this->db->where('j.id', $id_jadwal)
			->select('j.*, h.nama_hari')
			->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->get()->row();

		if (!$data) {
			echo json_encode(null);
			return;
		}

		$data->jam = substr($data->jam_mulai, 0, 5) . ' sd ' . substr($data->jam_selesai, 0, 5);

		$detail = $this->db->where('id_jadwal', $id_jadwal)
			->select('kk.nm_kls, mk.nm_mk, mk.kode_mk, mk.id_mk')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('nm_kls')
			->get();

		$kk = '';
		if ($detail->num_rows() > 0) {
			foreach ($detail->result() as $value) {
				$kk .= '[' . $value->nm_kls . '] ' . $value->kode_mk . ' - ' . $value->nm_mk . '<br>';
			}
		} else {
			$kk = '-';
		}

		$data->matkul = $kk;

		echo json_encode($data);
	}

	public function ajax_generate_bap()
	{
		$id_rps     = $this->security->xss_clean($this->input->post('id_rps'));
		$id_jadwal  = (int) $this->input->post('id_jadwal');
		$start_date = strtotime($this->input->post('tanggal_mulai'));

		if (empty($id_rps) || empty($id_jadwal) || !$start_date) {
			echo json_encode(['status' => FALSE, 'msg' => 'Data tidak lengkap.']);
			return;
		}

		$rps = $this->db
			->where('o.id_rps', $id_rps)
			->from('siakad_new_rps_online o')
			->join('siakad_new_rps_online_detail d', 'o.id_rps = d.id_rps')
			->get()->result();

		$jadwal_master = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

		if (!$jadwal_master) {
			echo json_encode(['status' => FALSE, 'msg' => 'Jadwal tidak ditemukan.']);
			return;
		}

		$data      = [];
		$minggu_a  = 'NIL';
		$create_by = $this->ion_auth->user()->row()->id;

		$jam_mulai   = $jadwal_master->jam_mulai;
		$jam_selesai = $jadwal_master->jam_selesai;

		foreach ($rps as $value) {
			$pertemuan            = $value->kolom_2;
			$minggu               = $value->kolom_1;

			if ($minggu_a != $minggu) {
				if ($minggu != 1) {
					$start_date = strtotime('+7 day', $start_date);
				}
			}

			$data[] = [
				'id_jadwal'            => $id_jadwal,
				'tanggal_rencana'      => date('Y-m-d', $start_date),
				'pertemuan'            => $pertemuan,
				'minggu'               => $minggu,
				'jam_mulai'            => $jam_mulai,
				'jam_selesai'          => $jam_selesai,
				'jam_teori'            => $value->kolom_8,
				'jam_praktek'          => $value->kolom_9,
				'berita_acara_rencana' => $value->kolom_4,
				'bacaan_materi'        => $value->kolom_13,
				'create_by'            => $create_by,
				'create_date'          => date('Y-m-d H:i:s'),
			];

			$minggu_a = $minggu;
		}

		if ($data) {
			$data = $this->security->xss_clean($data);
			$this->db->insert_batch('siakad_rencana_pertemuan', $data);
			$this->db->where(['id' => $id_jadwal])->update('siakad_jadwal', ['is_generate_bap' => 1]);
		}

		echo json_encode(['status' => TRUE]);
	}

	public function ajax_save_rps()
	{
		$this->_validate();

		$id_jadwal = (int) $this->input->post('id_jadwal');
		$id_rps    = $this->security->xss_clean($this->input->post('ddRps'));
		$id_smt    = $this->session->userdata('tahun_ajaran');
		$id_ptk    = $this->session->userdata('id_ptk');

		$jadwal_detail = $this->db
			->select('jd.id_kls, kk.id_mk')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'jd.id_kls = kk.id_kls')
			->where('jd.id_jadwal', $id_jadwal)
			->get();

		$data = [];
		if ($jadwal_detail->num_rows() > 0) {
			foreach ($jadwal_detail->result() as $value) {
				$data[] = [
					'id_rps'    => $id_rps,
					'id_jadwal' => $id_jadwal,
					'id_mk'     => $value->id_mk,
					'id_kls'    => $value->id_kls,
					'id_smt'    => $id_smt,
					'id_ptk'    => $id_ptk,
				];
			}
		}

		$data = $this->security->xss_clean($data);
		$this->Jadwal_model->save_rps($data);
		echo json_encode(['status' => TRUE]);
	}

	public function ajax_delete_rps($id_jadwal)
	{
		$id_jadwal = (int) $id_jadwal;
		$id_smt    = $this->session->userdata('tahun_ajaran');
		$this->Jadwal_model->delete_rps($id_jadwal, $id_smt);
		echo json_encode(['status' => TRUE]);
	}

	// ── Private helpers ───────────────────────────────────────────────────────

	private function _validate()
	{
		$data = [
			'error_string' => [],
			'inputerror'   => [],
			'status'       => TRUE,
		];

		$ddRps = $this->input->post('ddRps');
		if (empty($ddRps) || $ddRps == 0 || $ddRps === 'NIL') {
			$data['inputerror'][]   = 'ddRps';
			$data['error_string'][] = 'RPS belum dipilih.';
			$data['status']         = FALSE;
		} else {
			$data['inputerror'][]   = 'ddRps';
			$data['error_string'][] = '';
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
