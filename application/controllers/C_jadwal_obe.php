<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal_obe
{
	public function __get($var)
	{
		return get_instance()->$var;
	}

	public function __construct()
	{
		$this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
		$this->load->model('Jadwal_model', 'Jadwal_model');
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
		$output['content']     = 'jadwal/jadwal_view_obe';
		$output['after_page']  = 'jadwal/jadwal_after_page_obe';
		$output['dd_rps']      = $this->Jadwal_model->f_dropdown_rps();

		$this->load->view('master_page', $output);
	}

	/**
	 * ajax_list — DataTables server-side handler.
	 *
	 * Nilai jumlah_siswa, sudah_nilai, rencana, realisasi sudah di-aggregate
	 * di Jadwal_model menggunakan correlated subquery sehingga tidak perlu
	 * query tambahan per baris di sini.
	 *
	 * Satu-satunya batch query tambahan adalah untuk detail kelas+MK dan RPS,
	 * keduanya diambil sekaligus dengan WHERE IN.
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
		$all_details  = [];
		$kode_mk_list = [];
		$id_mk_list   = [];

		if (!empty($ids)) {
			$details_query = $this->db
				->select('jd.id_jadwal, kk.nm_kls, mk.nm_mk, mk.kode_mk, mk.id_mk, mk.sks_mk, s.nm_lemb as nm_prodi, s.id_sms')
				->from('siakad_jadwal_detail jd')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->join('sms s', 's.id_sms = kk.id_sms', 'left')
				->where_in('jd.id_jadwal', $ids)
				->order_by('jd.id_jadwal, kk.nm_kls')
				->get()->result();

			foreach ($details_query as $d) {
				$all_details[$d->id_jadwal][] = $d;
				if (!empty($d->kode_mk)) {
					$kode_mk_list[] = trim((string)$d->kode_mk);
				}
				if (!empty($d->id_mk)) {
					$id_mk_list[] = trim((string)$d->id_mk);
				}
			}
		}

		// ── Batch Query 2: Aggregated Nilai (WHERE IN) ────────────────────────
		$all_nilai = [];
		if (!empty($ids)) {
			$nilai_rows = $this->db
				->select('id_jadwal, COUNT(*) as jumlah_siswa, COUNT(CASE WHEN nilai_huruf IS NOT NULL AND nilai_huruf != "" THEN 1 END) as sudah_nilai')
				->from('siakad_nilai_baru')
				->where_in('id_jadwal', $ids)
				->group_by('id_jadwal')
				->get()->result();

			foreach ($nilai_rows as $nr) {
				$all_nilai[$nr->id_jadwal] = $nr;
			}
		}

		// ── Batch Query 3: Aggregated Rencana & Realisasi Pertemuan (WHERE IN) ─
		$all_rencana = [];
		if (!empty($ids)) {
			$rencana_rows = $this->db
				->select('id_jadwal, COUNT(*) as rencana, COUNT(CASE WHEN is_realisasi = 1 THEN 1 END) as realisasi')
				->from('siakad_rencana_pertemuan')
				->where_in('id_jadwal', $ids)
				->group_by('id_jadwal')
				->get()->result();

			foreach ($rencana_rows as $rr) {
				$all_rencana[$rr->id_jadwal] = $rr;
			}
		}

		// ── Batch Query 4: Targeted RPS OBE per MK / Dosen ────────────────────
		$all_rps_obe   = [];
		$kode_mk_list  = array_unique(array_filter($kode_mk_list));
		$id_mk_list    = array_unique(array_filter($id_mk_list));
		$id_ptk_login  = $this->session->userdata('id_ptk');

		$this->db->select('id_rps, id_ptk, kode_mk, id_mk, nm_mk, sks_mk, semester, id_status_pengajuan_rps, cp_mata_kuliah, deskripsi_mata_kuliah')
			->from('siakad_rps_obe');

		$this->db->group_start();
		$has_filter = false;
		if (!empty($id_ptk_login)) {
			$this->db->where('id_ptk', $id_ptk_login);
			$has_filter = true;
		}
		if (!empty($kode_mk_list)) {
			if ($has_filter) {
				$this->db->or_where_in('kode_mk', $kode_mk_list);
			} else {
				$this->db->where_in('kode_mk', $kode_mk_list);
				$has_filter = true;
			}
		}
		if (!empty($id_mk_list)) {
			if ($has_filter) {
				$this->db->or_where_in('id_mk', $id_mk_list);
			} else {
				$this->db->where_in('id_mk', $id_mk_list);
				$has_filter = true;
			}
		}
		$this->db->group_end();
		$this->db->order_by('id_rps', 'DESC');
		$rps_obe_rows = $this->db->get()->result();

		foreach ($rps_obe_rows as $r) {
			if (!empty($r->kode_mk)) {
				$key_ptk = trim((string)$r->kode_mk) . '_' . trim((string)($r->id_ptk ?? ''));
				$all_rps_obe[$key_ptk] ??= $r;
				$key_mk = trim((string)$r->kode_mk);
				$all_rps_obe[$key_mk] ??= $r;
			}
			if (!empty($r->id_mk)) {
				$key_idmk = trim((string)$r->id_mk);
				$all_rps_obe[$key_idmk] ??= $r;
			}
		}

		// ── Batch Query 5: Legacy fallback if any ─────────────────────────────
		$all_legacy_rps = [];
		if (!empty($ids)) {
			$legacy_query = $this->db
				->select('id_jadwal, id_rps')
				->where_in('id_jadwal', $ids)
				->get('siakad_new_rps_mk_prodi')->result();
			foreach ($legacy_query as $lr) {
				$all_legacy_rps[$lr->id_jadwal] = $lr;
			}
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

			$matched_rps_obe = null;
			$mk_html_list = [];
			if (empty($details)) {
				$mk_html_list[] = '<span class="text-muted">-</span>';
			} else {
				$total_classes = count($details);
				$first_item    = $details[0];
				$sks_val       = (int) $first_item->sks_mk;
				
				// Gunakan tag_name dari siakad_jadwal jika ada, fallback ke nm_mk
				$group_title   = !empty(trim((string)($rg->tag_name ?? ''))) ? trim((string)$rg->tag_name) : trim((string)($first_item->nm_mk ?? ''));

				$prodi_abbrev_map = [
					'Manajemen Pemasaran Internasional'   => 'MPI',
					'Akuntansi Perpajakan'               => 'AKP',
					'Agribisnis Hortikultura'            => 'AGH',
					'Pengelolaan Konvensi dan Acara'     => 'PKA',
					'Teknologi Rekayasa Perangkat Lunak' => 'TRPL',
					'Teknologi Rekayasa Multimedia'      => 'TRM',
					'Kewirausahaan'                      => 'KWU',
					'Keuangan dan Perbankan'             => 'KPB',
				];

				if ($total_classes > 1) {
					// ── KELAS GABUNGAN (Seluruh kelas pada jadwal ini dimasukkan ke dalam 1 panel) ──
					$classes_html = [];
					foreach ($details as $item) {
						$nm_prodi_raw = trim($item->nm_prodi ?? '');
						$prodi_short  = $prodi_abbrev_map[$nm_prodi_raw] ?? (!empty($nm_prodi_raw) ? $nm_prodi_raw : '-');

						$classes_html[] = '<div style="display:flex; align-items:center; justify-content:space-between; background:#ffffff; border:1px solid #e2e8f0; border-radius:6px; padding:4.5px 8px; gap:8px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">'
							. '<div style="display:flex; align-items:center; gap:6px; min-width:0; overflow:hidden; flex-wrap:wrap;">'
							. '<span style="background:#ede9fe; color:#5b21b6; font-weight:700; font-size:10.5px; padding:1.5px 6px; border-radius:4px; border:1px solid #ddd6fe; white-space:nowrap;"><i class="fa fa-users" style="font-size:9.5px;"></i> Kelas ' . htmlspecialchars($item->nm_kls) . '</span>'
							. '<span style="background:#ecfdf5; color:#065f46; font-weight:700; font-size:10.5px; padding:1.5px 6px; border-radius:4px; border:1px solid #a7f3d0; white-space:nowrap;" title="' . htmlspecialchars($nm_prodi_raw) . '"><i class="fa fa-university" style="font-size:9px; color:#059669;"></i> ' . htmlspecialchars($prodi_short) . '</span>'
							. '<span style="font-weight:600; color:#1e293b; font-size:11.5px;">' . htmlspecialchars($item->nm_mk) . '</span>'
							. '</div>'
							. '<span style="background:#f1f5f9; color:#475569; font-family:monospace; font-weight:600; font-size:10px; padding:1.5px 6px; border-radius:4px; border:1px solid #cbd5e1; white-space:nowrap; margin-left:auto;">' . htmlspecialchars($item->kode_mk) . '</span>'
							. '</div>';
					}

					$mk_html_list[] = '<div style="display:flex; flex-direction:column; gap:5px; margin-bottom:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:7px 9px;">'
						. '<div style="display:flex; align-items:center; justify-content:space-between; gap:6px; flex-wrap:wrap;">'
						. '<div style="font-weight:700; color:#0f172a; font-size:13.5px; line-height:1.35;">' . htmlspecialchars($group_title) . '</div>'
						. '<div style="display:flex; align-items:center; gap:4px;">'
						. '<span style="background:#fef3c7; color:#92400e; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:10px; border:1px solid #fde68a;">' . $sks_val . ' SKS</span>'
						. '<span style="background:#eef2ff; color:#4338ca; font-weight:700; font-size:10.5px; padding:2px 8px; border-radius:10px; border:1px solid #c7d2fe; display:inline-flex; align-items:center; gap:3px;"><i class="fa fa-cubes"></i> ' . $total_classes . ' Kelas Gabungan</span>'
						. '</div>'
						. '</div>'
						. '<div style="display:flex; flex-direction:column; gap:3px; margin-top:3px;">'
						. implode('', $classes_html)
						. '</div>'
						. '</div>';

				} else {
					// ── SINGLE KELAS (Normal) ──
					$single = $first_item;
					$display_name = !empty(trim((string)($rg->tag_name ?? ''))) ? trim((string)$rg->tag_name) : trim((string)($single->nm_mk ?? ''));
					$nm_prodi_raw = trim($single->nm_prodi ?? '');
					$prodi_short  = $prodi_abbrev_map[$nm_prodi_raw] ?? (!empty($nm_prodi_raw) ? $nm_prodi_raw : '');

					$prodi_row = !empty($prodi_short)
						? '<div style="margin-top:2px; display:flex; align-items:center; gap:4px;">'
							. '<span style="background:#ecfdf5; color:#065f46; font-weight:700; font-size:10.5px; padding:1.5px 7px; border-radius:4px; border:1px solid #a7f3d0; display:inline-flex; align-items:center; gap:4px;" title="' . htmlspecialchars($nm_prodi_raw) . '">'
							. '<i class="fa fa-university" style="font-size:9.5px; color:#059669;"></i> ' . htmlspecialchars($prodi_short)
							. '</span>'
							. '</div>'
						: '';

					$mk_html_list[] = '<div style="display:flex; flex-direction:column; gap:2px; margin-bottom:6px;">'
						. '<div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap;">'
						. '<span style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-weight:700; font-size:11px; padding:2px 7px; border-radius:4px; border:1px solid #cbd5e1;">' . htmlspecialchars($single->kode_mk) . '</span>'
						. '<span style="background:#fef3c7; color:#92400e; font-weight:700; font-size:10.5px; padding:2px 7px; border-radius:10px; border:1px solid #fde68a;">' . $sks_val . ' SKS</span>'
						. '<span style="background:#ede9fe; color:#5b21b6; font-weight:600; font-size:11px; padding:2px 7px; border-radius:4px; border:1px solid #ddd6fe;"><i class="fa fa-users" style="font-size:9.5px;"></i> Kelas ' . htmlspecialchars($single->nm_kls) . '</span>'
						. '</div>'
						. '<div style="font-weight:700; color:#0f172a; font-size:13.5px; line-height:1.35; margin-top:3px;">'
						. htmlspecialchars($display_name)
						. '</div>'
						. $prodi_row
						. '</div>';
				}

				// Cari RPS OBE yang cocok
				if (!$matched_rps_obe) {
					$id_sdm_row = $rg->id_sdm ?: $id_ptk_login;
					foreach ($details as $item) {
						$k1 = trim((string)($item->kode_mk ?? '')) . '_' . trim((string)($id_sdm_row ?? ''));
						$k2 = trim((string)($item->kode_mk ?? ''));
						$k3 = trim((string)($item->id_mk ?? ''));
						$matched_rps_obe = $all_rps_obe[$k1] ?? $all_rps_obe[$k2] ?? $all_rps_obe[$k3] ?? null;
						if ($matched_rps_obe) break;
					}
				}
			}

			// Nilai diambil dari batch aggregate
			$n_info = $all_nilai[$rg->id] ?? null;
			$r_info = $all_rencana[$rg->id] ?? null;

			$jumlah_siswa = (int) ($n_info ? $n_info->jumlah_siswa : 0);
			$sudah_nilai  = (int) ($n_info ? $n_info->sudah_nilai : 0);
			$rencana      = (int) ($r_info ? $r_info->rencana : 0);
			$realisasi    = (int) ($r_info ? $r_info->realisasi : 0);
			$belum_nilai  = $jumlah_siswa - $sudah_nilai;

			$publish_badge = $rg->is_lapor_nilai == 1
				? '<span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; font-size:10.5px; font-weight:700; color:#15803d; background:#dcfce7; border:1px solid #bbf7d0; border-radius:12px;"><i class="fa fa-check-circle" style="color:#16a34a;"></i> Sudah Publish</span>'
				: '<span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; font-size:10.5px; font-weight:700; color:#b91c1c; background:#fee2e2; border:1px solid #fecaca; border-radius:12px;"><i class="fa fa-clock-o" style="color:#dc2626;"></i> Belum Publish</span>';

			// Format rincian skema nilai untuk modal hub dokumen
			$skema_items = [];
			if ($rg->is_skema == 1) {
				if ($rg->sikap > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:11.5px; color:#334155;">Sikap: <strong style="color:#0f172a;">' . (int)$rg->sikap . '%</strong></span>';
				if ($rg->quiz > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:11.5px; color:#334155;">Quiz: <strong style="color:#0f172a;">' . (int)$rg->quiz . '%</strong></span>';
				if ($rg->tugas_terstruktur > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:11.5px; color:#334155;">Tugas: <strong style="color:#0f172a;">' . (int)$rg->tugas_terstruktur . '%</strong></span>';
				if ($rg->uts > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:11.5px; color:#334155;">UTS: <strong style="color:#0f172a;">' . (int)$rg->uts . '%</strong></span>';
				if ($rg->uas > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:11.5px; color:#334155;">UAS: <strong style="color:#0f172a;">' . (int)$rg->uas . '%</strong></span>';
				if ($rg->unjuk_kinerja > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; font-size:11.5px; color:#166534;">Unjuk Kinerja: <strong style="color:#14532d;">' . (int)$rg->unjuk_kinerja . '%</strong></span>';
				if ($rg->proyek > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; font-size:11.5px; color:#166534;">Proyek: <strong style="color:#14532d;">' . (int)$rg->proyek . '%</strong></span>';
				if ($rg->portofolio > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; font-size:11.5px; color:#166534;">Portofolio: <strong style="color:#14532d;">' . (int)$rg->portofolio . '%</strong></span>';
				if ($rg->produk > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; font-size:11.5px; color:#166534;">Produk: <strong style="color:#14532d;">' . (int)$rg->produk . '%</strong></span>';
				if ($rg->presentasi > 0) $skema_items[] = '<span style="display:inline-block; margin:2px 3px; padding:3px 8px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; font-size:11.5px; color:#166534;">Presentasi: <strong style="color:#14532d;">' . (int)$rg->presentasi . '%</strong></span>';
			}
			$skema_html = !empty($skema_items) ? implode('', $skema_items) : '';

			// ── 1. Render Kolom Hari / Waktu / Ruang (Sleek Compact Schedule Card) ──
			$jam_formatted = substr($rg->jam_mulai, 0, 5) . ' - ' . substr($rg->jam_selesai, 0, 5);
			$col_jadwal = '<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:7px 9px; display:flex; flex-direction:column; gap:3px;">'
				. '<div>'
				. '<span style="display:inline-block; background:#eef2ff; color:#4338ca; font-weight:700; font-size:11px; padding:2px 8px; border-radius:4px; border:1px solid #c7d2fe;">' . htmlspecialchars($rg->nama_hari) . '</span>'
				. '</div>'
				. '<div style="color:#0f172a; font-weight:600; font-size:11.5px; white-space:nowrap; display:flex; align-items:center; gap:4px;">'
				. '<i class="fa fa-clock-o" style="color:#6366f1; font-size:12px;"></i> ' . $jam_formatted . ' WIB'
				. '</div>'
				. '<div style="color:#64748b; font-size:11px; white-space:nowrap; display:flex; align-items:center; gap:4px;">'
				. '<i class="fa fa-map-marker" style="color:#ef4444; font-size:12px;"></i> ' . htmlspecialchars($rg->nama_ruang ?: 'Ruang Kuliah')
				. '</div>'
				. '</div>';

			// ── 2. Render Kolom Mata Kuliah & Kelas ────────────────────────────────
			$col_mk = implode('', $mk_html_list);

			// ── 3. Render Kolom Dokumen Jadwal (Hub Dokumen Pembelajaran & Nilai) ──
			if ($rencana > 0) {
				// Jadwal sudah memiliki pertemuan ter-generate (Aktif)
				$btn_reset_html = '<button type="button" class="btn-reset-jadwal" onclick="handleResetRpsJadwalDirect(' . (int) $rg->id . ')" title="Reset / bersihkan ' . $rencana . ' rencana pertemuan jadwal ini"><i class="fa fa-trash-o" style="color:#dc2626;"></i> Reset RPS (' . $rencana . ')</button>';

				if ($matched_rps_obe) {
					$id_md5 = md5($matched_rps_obe->id_rps);
					$status_id = (int)$matched_rps_obe->id_status_pengajuan_rps;
					if ($status_id === 3) {
						$badge_status_txt = '<span style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:2px 8px; border-radius:4px; font-weight:700; font-size:11px;"><i class="fa fa-check-circle"></i> Disetujui</span>';
					} elseif ($status_id === 2) {
						$badge_status_txt = '<span style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; padding:2px 8px; border-radius:4px; font-weight:700; font-size:11px;"><i class="fa fa-paper-plane"></i> Diajukan</span>';
					} else {
						$badge_status_txt = '<span style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:2px 8px; border-radius:4px; font-weight:700; font-size:11px;"><i class="fa fa-pencil-square-o"></i> Draft</span>';
					}
					$badge_obe_txt = '<span style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; padding:2px 8px; border-radius:4px; font-weight:700; font-size:11px;"><i class="fa fa-cubes"></i> OBE C4-C6</span>';

					$cpmk_desc = trim(strip_tags($matched_rps_obe->cp_mata_kuliah ?: $matched_rps_obe->deskripsi_mata_kuliah ?: ''));
					if (empty($cpmk_desc)) {
						$cpmk_desc = 'Capaian pembelajaran mata kuliah berstandar Outcome-Based Education (OBE SN-Dikti 2026 & SPMI WBI).';
					}

					$sem_clean = $matched_rps_obe->semester ? (stripos(trim((string)$matched_rps_obe->semester), 'semester') !== false ? trim((string)$matched_rps_obe->semester) : 'Semester ' . trim((string)$matched_rps_obe->semester)) : 'Semester Aktif';

					$modal_data = [
						'id_jadwal'           => (int) $rg->id,
						'id_rps'              => $matched_rps_obe->id_rps,
						'id_md5'              => $id_md5,
						'kode_mk'             => $matched_rps_obe->kode_mk ?: ($details[0]->kode_mk ?? 'MK'),
						'nm_mk'               => $matched_rps_obe->nm_mk ?: ($details[0]->nm_mk ?? 'Mata Kuliah'),
						'sks_mk'              => $matched_rps_obe->sks_mk ?: ($details[0]->sks_mk ?? 3),
						'nm_kls'              => count($details) > 1 
							? count($details) . ' Kelas Gabungan (' . implode(', ', array_map(fn($d) => $d->nm_kls, $details)) . ')' 
							: ($details[0]->nm_kls ?? ''),
						'nm_prodi'            => count($details) > 1 
							? implode(' &bull; ', array_unique(array_filter(array_map(fn($d) => $d->nm_prodi, $details)))) 
							: ($details[0]->nm_prodi ?? ''),
						'semester'            => $sem_clean,
						'status_badge'        => $badge_status_txt,
						'obe_badge'           => $badge_obe_txt,
						'deskripsi'           => $cpmk_desc,
						'is_skema'            => (int) $rg->is_skema,
						'skema_html'          => $skema_html,
						'sudah_nilai'         => $sudah_nilai,
						'belum_nilai'         => $belum_nilai,
						'jumlah_siswa'        => $jumlah_siswa,
						'url_rps'             => site_url('C_rps_obe/preview_rps/' . $id_md5),
						'url_kontrak'         => site_url('C_rps_obe/preview_kontrak/' . $id_md5),
						'url_tugas'           => site_url('C_rps_obe/preview_rancangan_tugas/' . $id_md5),
						'url_skenario'        => site_url('C_strategi_pertemuan/index/' . (int)$rg->id),
						'url_excel'           => site_url('C_rps_obe/export_excel/' . $matched_rps_obe->id_rps),
						'url_roadmap_preview' => site_url('C_rps_obe/preview_roadmap/' . $id_md5),
						'url_pdf_nilai'       => site_url('C_summary_nilai/cetak_pdf_nilai/' . (int) $rg->id),
						'url_studio'          => site_url('C_rps_generator?load_rps_obe=' . $matched_rps_obe->id_rps),
					];
					$json_attr = htmlspecialchars(json_encode($modal_data), ENT_QUOTES, 'UTF-8');
					$col_status_rps = '<div style="text-align:center;"><span style="display:inline-flex; align-items:center; gap:4px; padding:3px 9px; font-size:11px; font-weight:700; color:#15803d; background:#dcfce7; border:1px solid #86efac; border-radius:20px; white-space:nowrap;"><i class="fa fa-check-circle" style="color:#16a34a;"></i> RPS OBE Siap</span></div>';
					
					$col_dokumen    = '<div style="display:flex; flex-direction:column; gap:4px; align-items:stretch; min-width:145px;">'
						. '<button type="button" class="btn-doc-jadwal" data-rps=\'' . $json_attr . '\' onclick="open_rps_view_modal(this)" title="Buka Dokumen Jadwal Perkuliahan"><i class="fa fa-folder-open-o"></i> Dokumen Jadwal</button>'
						. '<a href="' . site_url('C_strategi_pertemuan/index/' . (int)$rg->id) . '" target="_blank" class="btn btn-sm" style="background-color:#7c3aed !important; color:#ffffff !important; border:1px solid #6d28d9 !important; font-weight:600; font-size:11.5px; padding:4px 8px; border-radius:4px; text-decoration:none !important; display:inline-flex; align-items:center; justify-content:center; gap:4px;" title="Buka Strategi Pertemuan 16 Minggu & BAP"><i class="fa fa-calendar-check-o"></i> Strategi Pertemuan</a>'
						. '</div>';
				} else {
					$legacy_row = $all_legacy_rps[$rg->id] ?? null;
					if ($legacy_row && !empty($legacy_row->id_rps)) {
						$col_status_rps = '<div style="text-align:center;"><span style="display:inline-flex; align-items:center; gap:4px; padding:3px 9px; font-size:11px; font-weight:600; color:#0369a1; background:#e0f2fe; border:1px solid #bae6fd; border-radius:20px; white-space:nowrap;"><i class="fa fa-file-text-o"></i> RPS Legacy</span></div>';
						$col_dokumen    = '<div style="display:flex; flex-direction:column; gap:4px; align-items:stretch; min-width:145px;">'
							. '<a href="' . site_url('C_rps_generator') . '" class="btn-create-rps-obe" target="_blank" title="Upgrade ke RPS OBE di Studio RPS"><i class="fa fa-magic"></i> Upgrade RPS</a>'
							. '<a href="' . site_url('C_strategi_pertemuan/index/' . (int)$rg->id) . '" target="_blank" class="btn btn-sm" style="background-color:#7c3aed !important; color:#ffffff !important; border:1px solid #6d28d9 !important; font-weight:600; font-size:11.5px; padding:4px 8px; border-radius:4px; text-decoration:none !important; display:inline-flex; align-items:center; justify-content:center; gap:4px;" title="Buka Strategi Pertemuan 16 Minggu & BAP"><i class="fa fa-calendar-check-o"></i> Strategi Pertemuan</a>'
							. '</div>';
					} else {
						$col_status_rps = '<div style="text-align:center;"><span style="display:inline-flex; align-items:center; gap:4px; padding:3px 9px; font-size:11px; font-weight:600; color:#065f46; background:#d1fae5; border:1px solid #a7f3d0; border-radius:20px; white-space:nowrap;"><i class="fa fa-check"></i> ' . $rencana . ' Pertemuan</span></div>';
						$col_dokumen    = '<div style="display:flex; flex-direction:column; gap:4px; align-items:stretch; min-width:145px;">'
							. '<a href="' . site_url('C_strategi_pertemuan/index/' . (int)$rg->id) . '" target="_blank" class="btn btn-sm" style="background-color:#7c3aed !important; color:#ffffff !important; border:1px solid #6d28d9 !important; font-weight:600; font-size:11.5px; padding:4px 8px; border-radius:4px; text-decoration:none !important; display:inline-flex; align-items:center; justify-content:center; gap:4px;" title="Buka Strategi Pertemuan 16 Minggu & BAP"><i class="fa fa-calendar-check-o"></i> Strategi Pertemuan</a>'
							. '</div>';
					}
				}
			} else {
				// Jadwal belum memiliki pertemuan / sudah direset (Full Clean)
				$col_status_rps = '<div style="text-align:center;"><span style="display:inline-flex; align-items:center; gap:4px; padding:3px 9px; font-size:11px; font-weight:600; color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:20px; white-space:nowrap;"><i class="fa fa-circle-o" style="color:#94a3b8;"></i> Belum Ada RPS</span></div>';

				$url_generator = $matched_rps_obe 
					? site_url('C_rps_generator?load_rps_obe=' . $matched_rps_obe->id_rps)
					: site_url('C_rps_generator');

				$col_dokumen    = '<div style="display:flex; flex-direction:column; gap:4px; align-items:stretch; min-width:145px;">'
					. '<a href="' . $url_generator . '" class="btn-create-rps-obe" target="_blank" title="Buka Studio RPS untuk membuat RPS dan menerapkan jadwal perkuliahan"><i class="fa fa-magic"></i> Buat RPS di Studio</a>'
					. '</div>';
			}

			// ── 4. Render Kolom Skema Nilai ───────────────────────────────────────
			if ($rencana > 0) {
				if ($rg->is_skema == 1) {
					$col_skema = '<div style="text-align:center;">'
						. '<button type="button" class="btn-pill-skema" onclick="edit_skema_nilai(\'' . (int) $rg->id . '\')" title="Ubah proporsi skema nilai">'
						. '<span><i class="fa fa-sliders" style="color:#64748b; margin-right:4px;"></i> Skema</span>'
						. '<span class="badge-skema-done"><i class="fa fa-check"></i> 100%</span></button>'
						. '</div>';
				} else {
					$col_skema = '<div style="text-align:center;">'
						. '<button type="button" class="btn-pill-atur-skema" onclick="edit_skema_nilai(\'' . (int) $rg->id . '\')" title="Klik untuk menetapkan skema penilaian">'
						. '<span><i class="fa fa-sliders" style="color:#d97706; margin-right:4px;"></i> Atur Skema</span>'
						. '<i class="fa fa-exclamation-circle" style="color:#d97706; font-size:11px;"></i></button>'
						. '</div>';
				}
			} else {
				// Belum ada RPS -> Skema Nilai dikunci
				$col_skema = '<div style="text-align:center;">'
					. '<button type="button" class="btn-pill-atur-skema" style="background:#f8fafc; border:1px dashed #cbd5e1; color:#94a3b8; cursor:pointer;" onclick="notify_need_rps()" title="Harap buat/terapkan RPS terlebih dahulu">'
					. '<span><i class="fa fa-lock" style="color:#94a3b8; margin-right:4px;"></i> Butuh RPS</span>'
					. '</button>'
					. '</div>';
			}

			// ── 5. Render Kolom Nilai (Isi Nilai & Publish) ────────────────────────
			if ($rencana > 0 && $rg->is_skema == 1) {
				$btn_isi_nilai = '<a href="' . site_url('C_isi_nilai_excel/index/' . (int) $rg->id) . '" class="btn-pill-isi-nilai" title="Input nilai mahasiswa">'
					. '<span><i class="fa fa-pencil-square-o" style="color:#16a34a; margin-right:4px;"></i> Isi Nilai</span>'
					. '<i class="fa fa-chevron-right" style="font-size:9px; opacity:0.6;"></i></a>';

				$btn_publish = '<a href="' . site_url('C_lapor_nilai') . '" class="btn-pill-publish" title="Publish nilai ke akademik">'
					. '<span><i class="fa fa-paper-plane-o" style="color:#2563eb; margin-right:4px;"></i> Publish</span>'
					. '<i class="fa fa-chevron-right" style="font-size:9px; opacity:0.6;"></i></a>';

				$col_nilai = '<div style="display:flex; flex-direction:column; gap:3px;">' . $btn_isi_nilai . $btn_publish . '</div>';
			} else if ($rencana > 0) {
				$col_nilai = '<div style="text-align:center;"><span style="font-size:11px; color:#94a3b8; font-style:italic;"><i class="fa fa-lock" style="margin-right:3px;"></i> Perlu Skema</span></div>';
			} else {
				$col_nilai = '<div style="text-align:center;"><span style="font-size:11px; color:#94a3b8; font-style:italic;"><i class="fa fa-lock" style="margin-right:3px;"></i> Butuh RPS</span></div>';
			}

			// ── 6. Render Kolom Aksi Reset RPS (Paling Kanan) ────────────────────
			if ($rencana > 0) {
				$col_reset = '<div style="text-align:center;">'
					. '<button type="button" class="btn-reset-jadwal" onclick="handleResetRpsJadwalDirect(' . (int) $rg->id . ')" title="Reset / bersihkan ' . $rencana . ' rencana pertemuan jadwal ini">'
					. '<i class="fa fa-trash-o" style="color:#dc2626;"></i> Reset RPS (' . $rencana . ')'
					. '</button>'
					. '</div>';
			} else {
				$col_reset = '<div style="text-align:center;"><span style="color:#cbd5e1; font-size:12px;">-</span></div>';
			}

			$row   = [];
			$row[] = '<div style="display:flex; justify-content:center;"><span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:#f1f5f9; color:#475569; font-weight:700; font-size:11.5px; border:1px solid #e2e8f0;">' . $no . '</span></div>';
			$row[] = $col_jadwal;
			$row[] = $col_mk;
			$row[] = $col_status_rps;
			$row[] = $col_dokumen;
			$row[] = $col_skema;
			$row[] = $col_nilai;
			$row[] = $col_reset;

			$data[] = $row;
		}

		echo json_encode([
			'draw'            => $draw,
			'recordsTotal'    => $this->Jadwal_model->count_all(),
			'recordsFiltered' => $this->Jadwal_model->count_filtered(),
			'data'            => $data,
		]);
	}

	public function get_copy_data($id_rps, $id_jadwal_current = null)
	{
		// Sanitize
		$id_rps = $this->security->xss_clean($id_rps);
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');

		// Ambil daftar jadwal aktif dosen untuk opsi multi-sesi (2 pertemuan per minggu)
		$schedules = [];
		if (!empty($id_ptk)) {
			$raw_schedules = $this->db
				->select('j.id, j.id as id_jadwal, j.tag_name, j.jam_mulai, j.jam_selesai, j.id_hari, h.nama_hari,
				          GROUP_CONCAT(DISTINCT kk.nm_kls SEPARATOR ", ") as nm_kls,
				          GROUP_CONCAT(DISTINCT s.nm_lemb SEPARATOR ", ") as nm_prodi')
				->from('siakad_jadwal j')
				->join('siakad_hari h', 'h.uniq = j.id_hari', 'left')
				->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id', 'left')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls', 'left')
				->join('sms s', 's.id_sms = kk.id_sms', 'left')
				->where('j.id_sdm', $id_ptk)
				->where('j.id_smt', $id_smt)
				->group_by('j.id')
				->order_by('j.id_hari, j.jam_mulai')
				->get()->result();

			foreach ($raw_schedules as $s) {
				$timeRange = substr($s->jam_mulai, 0, 5) . ' - ' . substr($s->jam_selesai, 0, 5);
				$klsInfo   = !empty($s->nm_kls) ? ' - Kls ' . $s->nm_kls : '';
				$prodiInfo = !empty($s->nm_prodi) ? ' (' . $s->nm_prodi . ')' : '';
				$s->label_formatted = ($s->tag_name ?: 'Jadwal') . $klsInfo . $prodiInfo . ' [' . ($s->nama_hari ?: '') . ', ' . $timeRange . ']';
				$schedules[] = $s;
			}
		}

		// 1. Prioritaskan Master RPS OBE (siakad_rps_obe)
		$data_obe = $this->db
			->select('r.id_rps, r.nm_mk as general_mk, r.koordinator_rps, r.tanggal_rps, r.kode_mk, r.nm_mk, r.dosen_pengampu, sd.nm_ptk,
				(SELECT COUNT(*) FROM siakad_rps_obe_detail d WHERE d.id_rps = r.id_rps) AS jumlah_detail_pertemuan')
			->from('siakad_rps_obe r')
			->join('siakad_dosen sd', 'sd.id_ptk = r.id_ptk', 'left')
			->where('r.id_rps', $id_rps)
			->get()->row();

		if ($data_obe) {
			$data_obe->schedules = $schedules;
			echo json_encode($data_obe);
			return;
		}

		// 2. Fallback ke legacy siakad_new_rps_mk_prodi jika ada
		$data = $this->db
			->select('a.*, b.nm_mk as general_mk, b.koordinator_rps, b.tanggal_rps, b.kode_mk, b.nm_mk, b.dosen_pengampu, sd.nm_ptk,
				(SELECT COUNT(*) FROM siakad_new_rps_online_detail d WHERE d.id_rps = a.id_rps) AS jumlah_detail_pertemuan')
			->from('siakad_new_rps_mk_prodi a')
			->join('siakad_new_rps_online b', 'b.id_rps = a.id_rps')
			->join('siakad_dosen sd', 'sd.id_ptk = b.koordinator_rps', 'left')
			->where('a.id_rps', $id_rps)
			->get()->row();

		if ($data) {
			$data->schedules = $schedules;
		}

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
			->select('kk.nm_kls, mk.nm_mk, mk.kode_mk, mk.id_mk, s.nm_lemb as nm_prodi')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = kk.id_sms', 'left')
			->order_by('nm_kls')
			->get();

		$kk = '';
		if ($detail->num_rows() > 0) {
			foreach ($detail->result() as $value) {
				$prodi_str = !empty($value->nm_prodi) ? ' (' . $value->nm_prodi . ')' : '';
				$kk .= '[' . $value->nm_kls . '] ' . $value->kode_mk . ' - ' . $value->nm_mk . $prodi_str . '<br>';
			}
		} else {
			$kk = '-';
		}

		$data->matkul = $kk;

		echo json_encode($data);
	}

	public function ajax_generate_bap()
	{
		$id_rps            = $this->security->xss_clean($this->input->post('id_rps'));
		$id_jadwal         = (int) $this->input->post('id_jadwal');
		$start_date_input  = $this->input->post('tanggal_mulai');
		$start_date        = strtotime($start_date_input);
		$sync_freq         = (int) ($this->input->post('sync_freq') ?: 1);
		$id_jadwal_2       = (int) $this->input->post('id_jadwal_2');
		$sync_distribution = $this->input->post('sync_distribution') ?: 'alternating';

		if (empty($id_rps) || empty($id_jadwal) || !$start_date) {
			echo json_encode(['status' => FALSE, 'msg' => 'Data tidak lengkap.']);
			return;
		}

		$jadwal_master = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();
		if (!$jadwal_master) {
			echo json_encode(['status' => FALSE, 'msg' => 'Jadwal 1 tidak ditemukan.']);
			return;
		}

		$jadwal_2 = null;
		if ($sync_freq === 2 && !empty($id_jadwal_2)) {
			$jadwal_2 = $this->db->where('id', $id_jadwal_2)->get('siakad_jadwal')->row();
			if (!$jadwal_2) {
				echo json_encode(['status' => FALSE, 'msg' => 'Jadwal Sesi 2 tidak ditemukan.']);
				return;
			}
		}

		// Cek tabel siakad_rps_obe (Master RPS OBE)
		$rps_header = $this->db->where('id_rps', $id_rps)->get('siakad_rps_obe')->row();
		$rps = [];
		if ($rps_header) {
			$rps = $this->db->where('id_rps', $id_rps)->order_by('kolom_2', 'ASC')->get('siakad_rps_obe_detail')->result();
		} else {
			$rps = $this->db
				->where('o.id_rps', $id_rps)
				->from('siakad_new_rps_online o')
				->join('siakad_new_rps_online_detail d', 'o.id_rps = d.id_rps')
				->order_by('d.kolom_2', 'ASC')
				->get()->result();
		}

		if (empty($rps)) {
			echo json_encode(['status' => FALSE, 'msg' => 'Rincian 16 pertemuan pada RPS ini belum lengkap.']);
			return;
		}

		$user_id = $this->ion_auth->user()->row()->id ?? 1;
		$now_str = date('Y-m-d H:i:s');

		// Helper tanggal awal per hari jadwal
		$calc_first_date = function($start_timestamp, $day_of_week) {
			$ts = $start_timestamp;
			if ($day_of_week > 0) {
				$start_w = (int)date('N', $ts);
				$diff_days = ($day_of_week - $start_w);
				if ($diff_days < 0) {
					$diff_days += 7;
				}
				$ts = strtotime("+{$diff_days} day", $ts);
			}
			return $ts;
		};

		// ── Kasus 1: Multi-Sesi 2x Seminggu (Ganjil di Sesi 1, Genap di Sesi 2) ──
		if ($sync_freq === 2 && $jadwal_2 && $sync_distribution === 'alternating') {
			$first_ts_1 = $calc_first_date($start_date, (int)$jadwal_master->id_hari);
			$first_ts_2 = $calc_first_date($start_date, (int)$jadwal_2->id_hari);

			$data_1 = [];
			$data_2 = [];

			foreach ($rps as $value) {
				$meet_no = (int)($value->kolom_2 ?: 1);
				$week_idx = (int)ceil($meet_no / 2); // 1..8
				$week_offset_days = ($week_idx - 1) * 7;

				$rencana_scl = "🎯 Target: " . ($value->kolom_3 ?? '') . "\n📋 Strategi SCL: " . ($value->kolom_5 ?? 'Kuliah Interaktif') . " (" . ($value->kolom_6 ?? 'Diskusi') . ").\nPengalaman Belajar: " . ($value->kolom_7 ?? '');
				$bacaan_full = ($value->kolom_4 ? "Bahan Kajian: " . $value->kolom_4 . "\n" : "") . ($value->kolom_13 ? "Sumber Belajar: " . $value->kolom_13 : "");
				$draf_bap    = "Perkuliahan tatap muka mengenai " . ($value->kolom_4 ?? '') . " dengan metode " . ($value->kolom_6 ?? 'pembelajaran aktif') . ". Mahasiswa aktif menganalisis materi dan berdiskusi. Sesi diakhiri dengan evaluasi capaian dan arahan tugas.";

				if ($meet_no % 2 !== 0) {
					// Sesi 1 (Ganjil)
					$meet_ts = strtotime("+{$week_offset_days} day", $first_ts_1);
					$data_1[] = [
						'id_jadwal'            => $jadwal_master->id,
						'tanggal_rencana'      => date('Y-m-d', $meet_ts),
						'pertemuan'            => $meet_no,
						'minggu'               => $week_idx,
						'jam_mulai'            => $jadwal_master->jam_mulai,
						'jam_selesai'          => $jadwal_master->jam_selesai,
						'jam_teori'            => $value->kolom_8 ?: 1,
						'jam_praktek'          => $value->kolom_9 ?: 2,
						'sub_cpmk'             => $value->kolom_3 ?? '',
						'metode_pembelajaran'  => ($value->kolom_5 ?? '') . ' (' . ($value->kolom_6 ?? '') . ')',
						'berita_acara_rencana' => $rencana_scl,
						'bacaan_materi'        => $bacaan_full,
						'draf_bap'             => $draf_bap,
						'create_by'            => $user_id,
						'create_date'          => $now_str,
					];
				} else {
					// Sesi 2 (Genap)
					$meet_ts = strtotime("+{$week_offset_days} day", $first_ts_2);
					$data_2[] = [
						'id_jadwal'            => $jadwal_2->id,
						'tanggal_rencana'      => date('Y-m-d', $meet_ts),
						'pertemuan'            => $meet_no,
						'minggu'               => $week_idx,
						'jam_mulai'            => $jadwal_2->jam_mulai,
						'jam_selesai'          => $jadwal_2->jam_selesai,
						'jam_teori'            => $value->kolom_8 ?: 1,
						'jam_praktek'          => $value->kolom_9 ?: 2,
						'sub_cpmk'             => $value->kolom_3 ?? '',
						'metode_pembelajaran'  => ($value->kolom_5 ?? '') . ' (' . ($value->kolom_6 ?? '') . ')',
						'berita_acara_rencana' => $rencana_scl,
						'bacaan_materi'        => $bacaan_full,
						'draf_bap'             => $draf_bap,
						'create_by'            => $user_id,
						'create_date'          => $now_str,
					];
				}
			}

			if (!empty($data_1)) {
				$this->db->where('id_jadwal', $jadwal_master->id)->delete('siakad_rencana_pertemuan');
				$this->db->insert_batch('siakad_rencana_pertemuan', $data_1);
				$this->db->where('id', $jadwal_master->id)->update('siakad_jadwal', ['is_generate_bap' => 1]);
			}
			if (!empty($data_2)) {
				$this->db->where('id_jadwal', $jadwal_2->id)->delete('siakad_rencana_pertemuan');
				$this->db->insert_batch('siakad_rencana_pertemuan', $data_2);
				$this->db->where('id', $jadwal_2->id)->update('siakad_jadwal', ['is_generate_bap' => 1]);
			}

			echo json_encode([
				'status' => TRUE,
				'msg'    => 'Pertemuan perkuliahan berhasil dibuat untuk 2 sesi per minggu (Sesi 1 Ganjil & Sesi 2 Genap).'
			]);
			return;
		}

		// ── Kasus 2: Standar 1 Sesi per Minggu (16 Minggu) ──
		$first_ts = $calc_first_date($start_date, (int)$jadwal_master->id_hari);
		$meeting_date_ts = $first_ts;
		$minggu_a  = 'NIL';
		$data      = [];

		foreach ($rps as $value) {
			$pertemuan = (int)$value->kolom_2;
			$minggu    = (int)$value->kolom_1;

			if ($minggu_a != $minggu) {
				if ($minggu != 1) {
					$meeting_date_ts = strtotime('+7 day', $meeting_date_ts);
				}
			}

			$rencana_scl = "🎯 Target: " . ($value->kolom_3 ?? '') . "\n📋 Strategi SCL: " . ($value->kolom_5 ?? 'Kuliah Interaktif') . " (" . ($value->kolom_6 ?? 'Diskusi') . ").\nPengalaman Belajar: " . ($value->kolom_7 ?? '');
			$bacaan_full = ($value->kolom_4 ? "Bahan Kajian: " . $value->kolom_4 . "\n" : "") . ($value->kolom_13 ? "Sumber Belajar: " . $value->kolom_13 : "");
			$draf_bap    = "Perkuliahan tatap muka mengenai " . ($value->kolom_4 ?? '') . " dengan metode " . ($value->kolom_6 ?? 'pembelajaran aktif') . ". Mahasiswa aktif menganalisis materi dan berdiskusi. Sesi diakhiri dengan evaluasi capaian dan arahan tugas.";

			$data[] = [
				'id_jadwal'            => $id_jadwal,
				'tanggal_rencana'      => date('Y-m-d', $meeting_date_ts),
				'pertemuan'            => $pertemuan,
				'minggu'               => $minggu,
				'jam_mulai'            => $jadwal_master->jam_mulai,
				'jam_selesai'          => $jadwal_master->jam_selesai,
				'jam_teori'            => $value->kolom_8 ?: 1,
				'jam_praktek'          => $value->kolom_9 ?: 2,
				'sub_cpmk'             => $value->kolom_3 ?? '',
				'metode_pembelajaran'  => ($value->kolom_5 ?? '') . ' (' . ($value->kolom_6 ?? '') . ')',
				'berita_acara_rencana' => $rencana_scl,
				'bacaan_materi'        => $bacaan_full,
				'draf_bap'             => $draf_bap,
				'create_by'            => $user_id,
				'create_date'          => $now_str,
			];

			$minggu_a = $minggu;
		}

		if (!empty($data)) {
			$this->db->where('id_jadwal', $id_jadwal)->delete('siakad_rencana_pertemuan');
			$this->db->insert_batch('siakad_rencana_pertemuan', $data);
			$this->db->where(['id' => $id_jadwal])->update('siakad_jadwal', ['is_generate_bap' => 1]);
		}

		echo json_encode([
			'status' => TRUE,
			'msg'    => '16 Rencana Pertemuan dan Draf BAP berhasil digenerate ke Virtual Class.'
		]);
	}

	public function ajax_save_rps()
	{
		$this->_validate();

		$id_jadwal = (int) $this->input->post('id_jadwal');
		$id_rps    = $this->security->xss_clean($this->input->post('ddRps'));
		$id_smt    = $this->session->userdata('tahun_ajaran');
		$id_ptk    = $this->session->userdata('id_ptk');

		// SELECT hanya kolom yang dibutuhkan, bukan SELECT *
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

	/**
	 * AJAX: Check status of meetings & realizations before resetting RPS for a schedule
	 */
	public function ajax_check_reset_rps($id_jadwal)
	{
		$this->output->set_content_type('application/json');
		$id_jadwal = (int) $id_jadwal;
		$id_ptk    = $this->session->userdata('id_ptk');

		$jadwal = $this->db->where('id', $id_jadwal)
			->group_start()
				->where('id_sdm', $id_ptk)
				->or_where('id_sdm_2', $id_ptk)
				->or_where('id_sdm_3', $id_ptk)
			->group_end()
			->get('siakad_jadwal')->row();

		if (!$jadwal) {
			echo json_encode(['status' => FALSE, 'message' => 'Jadwal tidak ditemukan atau Anda tidak memiliki akses.']);
			return;
		}

		$total_rencana   = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan')->num_rows();
		$total_realisasi = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();

		// Ambil info nama MK dan Kelas
		$details = $this->db->select('mk.nm_mk, kk.nm_kls')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'jd.id_kls = kk.id_kls')
			->join('feeder_mata_kuliah mk', 'kk.id_mk = mk.id_mk')
			->where('jd.id_jadwal', $id_jadwal)
			->get()->row();

		$tag_name = $details ? ($details->nm_mk . ' (Kelas ' . $details->nm_kls . ')') : ($jadwal->tag_name ?: 'Jadwal Perkuliahan');

		$realized_meets = [];
		if ($total_realisasi > 0) {
			$realized_rows = $this->db->select('pertemuan, tanggal_realisasi, jam_mulai_realisasi, jam_selesai_realisasi')
				->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
				->order_by('pertemuan', 'ASC')
				->get('siakad_rencana_pertemuan')->result();
			foreach ($realized_rows as $rr) {
				$realized_meets[] = 'Pertemuan ke-' . $rr->pertemuan . ' (' . ($rr->tanggal_realisasi ?: 'Terlaksana') . ')';
			}
		}

		echo json_encode([
			'status'          => TRUE,
			'id_jadwal'       => $id_jadwal,
			'tag_name'        => $tag_name,
			'total_rencana'   => $total_rencana,
			'total_realisasi' => $total_realisasi,
			'has_realisasi'   => ($total_realisasi > 0),
			'realized_meets'  => $realized_meets,
			'message'         => ($total_realisasi > 0)
				? "Jadwal ini telah memiliki {$total_realisasi} pertemuan yang terealisasi (BAP/Presensi terisi). Reset total akan menghapus data tersebut!"
				: "Jadwal ini memiliki {$total_rencana} rencana pertemuan dan belum ada pertemuan yang berjalan. Aman untuk direset."
		]);
	}

	/**
	 * AJAX: Execute Reset RPS for a schedule
	 */
	public function ajax_execute_reset_rps()
	{
		$this->output->set_content_type('application/json');
		$id_jadwal   = (int) $this->input->post('id_jadwal');
		$reset_mode  = $this->input->post('reset_mode') ?: 'full_reset'; // 'full_reset' or 'unrealized_only'
		$id_ptk      = $this->session->userdata('id_ptk');
		$id_smt      = $this->session->userdata('tahun_ajaran');

		$jadwal = $this->db->where('id', $id_jadwal)
			->group_start()
				->where('id_sdm', $id_ptk)
				->or_where('id_sdm_2', $id_ptk)
				->or_where('id_sdm_3', $id_ptk)
			->group_end()
			->get('siakad_jadwal')->row();

		if (!$jadwal) {
			echo json_encode(['status' => FALSE, 'message' => 'Jadwal tidak valid atau Anda tidak memiliki akses.']);
			return;
		}

		$total_realisasi = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();

		if ($reset_mode === 'unrealized_only') {
			// Hanya hapus pertemuan yang belum realisasi
			$this->db->where('id_jadwal', $id_jadwal)->where('is_realisasi', 0)->delete('siakad_rencana_pertemuan');
			$deleted_count = $this->db->affected_rows();

			echo json_encode([
				'status'  => TRUE,
				'mode'    => 'unrealized_only',
				'message' => "Sebanyak {$deleted_count} rencana pertemuan yang belum terlaksana berhasil dibersihkan. Pertemuan yang sudah realisasi ({$total_realisasi}) tetap aman tersimpan."
			]);
			return;
		}

		// Full Reset
		$this->db->trans_start();

		// 1. Hapus seluruh rencana pertemuan pada jadwal ini
		$this->db->where('id_jadwal', $id_jadwal)->delete('siakad_rencana_pertemuan');
		$deleted_count = $this->db->affected_rows();

		// 2. Set is_generate_bap = 0 pada jadwal
		$this->db->where('id', $id_jadwal)->update('siakad_jadwal', ['is_generate_bap' => 0]);

		// 3. Hapus relasi legacy RPS jika ada
		$this->db->where('id_jadwal', $id_jadwal)->delete('siakad_new_rps_mk_prodi');

		// 4. Hapus lesson jika ada
		$this->db->where('course_id', $id_jadwal)->delete('lesson');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => FALSE, 'message' => 'Gagal mereset data jadwal di database.']);
			return;
		}

		echo json_encode([
			'status'  => TRUE,
			'mode'    => 'full_reset',
			'message' => "Seluruh {$deleted_count} rencana pertemuan pada jadwal ini berhasil direset. Status jadwal kini bersih dan siap disinkronkan kembali dari Studio RPS."
		]);
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
