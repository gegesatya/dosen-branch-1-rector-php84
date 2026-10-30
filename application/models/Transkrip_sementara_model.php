<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transkrip_sementara_model extends CI_Model
{
	public $table = 'transkrip_sementara_master_2020';
	public $column = ['mp.nipd', 'm.nm_pd', 's.nm_lemb', 'ts.jumlah_sks', 'ts.indeks_prestasi_kumulatif'];
	public $order = ['mp.nipd' => 'asc'];

	private static $_kurikulum_map = null;
	private static $_kurikulum_global_map = null;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Preload seluruh semester kurikulum ke dalam memory PHP
	 * Hanya dieksekusi 1 kali per request untuk menghilangkan N+1 query
	 */
	public function init_kurikulum_cache()
	{
		if (self::$_kurikulum_map !== null) {
			return;
		}

		self::$_kurikulum_map = [];
		self::$_kurikulum_global_map = [];

		$rows = $this->db->query("
			SELECT fmk.kode_mk, fk.id_sms, MIN(fmkk.smt) as smt
			FROM feeder_mata_kuliah_kurikulum fmkk
			JOIN feeder_kurikulum fk ON fk.id_kurikulum_sp = fmkk.id_kurikulum_sp
			JOIN feeder_mata_kuliah fmk ON fmk.id_mk = fmkk.id_mk
			GROUP BY fmk.kode_mk, fk.id_sms
		")->result();

		foreach ($rows as $r) {
			$kode = trim(strtoupper((string)($r->kode_mk ?? '')));
			$smt = (int)$r->smt;
			self::$_kurikulum_map[$kode . '|' . $r->id_sms] = $smt;

			if (!isset(self::$_kurikulum_global_map[$kode]) || $smt < self::$_kurikulum_global_map[$kode]) {
				self::$_kurikulum_global_map[$kode] = $smt;
			}
		}
	}

	private function _get_datatables_query()
	{
		// Gabungkan seluruh prodi (tanpa filter session prodi)
		$this->db->select('mp.nipd, m.nm_pd, s.nm_lemb as nm_prodi, ts.jumlah_sks, ts.indeks_prestasi_kumulatif')
			->from('mahasiswa_pt mp')
			->join('mahasiswa m', 'mp.id_pd = m.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms', 'LEFT')
			->join('transkrip_sementara_master_2020 ts', 'ts.nipd = mp.nipd', 'LEFT')
			->where('mp.id_jns_keluar IS NULL', null, false);

		$i = 0;

		foreach ($this->column as $item) {
			if (!empty($_POST['search']['value'])) {

				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column) - 1 == $i)
					$this->db->group_end();
			}
			$column[$i] = $item;
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if (isset($_POST['length']) && $_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	public function count_all()
	{
		// Seluruh mahasiswa aktif dari semua program studi
		return $this->db->where('id_jns_keluar IS NULL', null, false)
			->count_all_results('mahasiswa_pt');
	}

	public function get_by_id($id)
	{
		$this->db->select('*')->from('transkrip_sementara_master_2020 tr')
			->join('mahasiswa_pt mp', 'mp.nipd = tr.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('id_transkrip', $id);
		$query = $this->db->get();

		return $query->row();
	}

	/**
	 * Hitung dan simpan transkrip sementara satu mahasiswa
	 * Optimal, tanpa N+1 query, tanpa query duplikat
	 */
	public function hitung_dan_simpan($nipd, $user_id = null)
	{
		if (!$user_id) {
			$user_id = $this->ion_auth->user()->row()->id;
		}

		// Pastikan cache kurikulum sudah dimuat ke memory
		$this->init_kurikulum_cache();

		// 1. Ambil data mahasiswa
		$mhs = $this->db->select('mp.id_reg_pd, mp.id_reg_pd_feeder, mp.id_sms, m.nm_pd')
			->from('mahasiswa_pt mp')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('mp.nipd', $nipd)
			->get()->row();

		if (!$mhs) {
			return false;
		}

		$id_reg_pd = $mhs->id_reg_pd;
		$id_reg_pd_feeder = $mhs->id_reg_pd_feeder;
		$id_sms = $mhs->id_sms;

		// 2. Nilai transfer
		$nilai_transfer = [];
		if (!empty($id_reg_pd_feeder)) {
			$nilai_transfer = $this->db->where('nt.id_reg_pd', $id_reg_pd_feeder)
				->where('nt.nilai_huruf_diakui IS NOT NULL', null, false)
				->where("nt.nilai_huruf_diakui != ''", null, false)
				->select('mk.kode_mk, mk.nm_mk, nt.nilai_huruf_diakui, nt.nilai_angka_diakui, nt.sks_diakui')
				->from('nilai_transfer nt')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = nt.id_mk')
				->get()->result();
		}

		// 3. Arsip nilai
		$arsip_nilai = [];
		if (!empty($id_reg_pd)) {
			$arsip_nilai = $this->db->select('mk.kode_mk, mk.nm_mk, an.nilai_huruf, an.nilai_indeks, sk.sks_mk, sk.id_smt')
				->from('arsip_nilai an')
				->join('siakad_kelas_kuliah sk', 'sk.id_kls = an.id_kls')
				->join('mata_kuliah mk', 'mk.id_mk = sk.id_mk')
				->where('an.id_reg_pd', $id_reg_pd)
				->where('an.nilai_huruf IS NOT NULL', null, false)
				->where("an.nilai_huruf != ''", null, false)
				->order_by('sk.id_smt')
				->get()->result();
		}

		// 4. Nilai baru
		$nilai_baru = $this->db->where('nb.nipd', $nipd)
			->where('nb.nilai_huruf IS NOT NULL', null, false)
			->where("nb.nilai_huruf != ''", null, false)
			->select('mk.kode_mk, mk.nm_mk, nb.nilai_huruf, nb.nilai_indeks, fk.sks_mk, fk.id_smt')
			->from('siakad_nilai_baru nb')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->order_by('fk.id_smt')
			->get()->result();

		$data_raw = [];
		foreach ($nilai_transfer as $value) {
			$data_raw[] = [
				'kode_mk' => $value->kode_mk,
				'nm_mk' => $value->nm_mk,
				'sks_mk' => (int)$value->sks_diakui,
				'nilai_huruf' => $value->nilai_huruf_diakui,
				'nilai_indeks' => (float)$value->nilai_angka_diakui,
				'id_smt' => NULL
			];
		}

		foreach ($arsip_nilai as $value) {
			$data_raw[] = [
				'kode_mk' => $value->kode_mk,
				'nm_mk' => $value->nm_mk,
				'sks_mk' => (int)$value->sks_mk,
				'nilai_huruf' => $value->nilai_huruf,
				'nilai_indeks' => (float)$value->nilai_indeks,
				'id_smt' => $value->id_smt
			];
		}

		foreach ($nilai_baru as $value) {
			$data_raw[] = [
				'kode_mk' => $value->kode_mk,
				'nm_mk' => $value->nm_mk,
				'sks_mk' => (int)$value->sks_mk,
				'nilai_huruf' => $value->nilai_huruf,
				'nilai_indeks' => (float)$value->nilai_indeks,
				'id_smt' => $value->id_smt
			];
		}

		// 5. Deduplikasi mata kuliah mengulang: ambil nilai tertinggi (nilai_indeks terbesar)
		$data_dedup = [];
		foreach ($data_raw as $item) {
			$key = trim(strtoupper($item['kode_mk']));
			if (empty($key)) {
				$key = trim(strtolower($item['nm_mk']));
			}

			if (!isset($data_dedup[$key])) {
				$data_dedup[$key] = $item;
			} else {
				$current_indeks = (float)$item['nilai_indeks'];
				$existing_indeks = (float)$data_dedup[$key]['nilai_indeks'];

				if ($current_indeks > $existing_indeks) {
					$data_dedup[$key] = $item;
				} elseif ($current_indeks == $existing_indeks) {
					if ($item['id_smt'] > $data_dedup[$key]['id_smt']) {
						$data_dedup[$key] = $item;
					}
				}
			}
		}
		$data_raw = array_values($data_dedup);

		// 6. Tentukan semester kurikulum menggunakan in-memory cache (O(1) tanpa database query)
		foreach ($data_raw as &$item) {
			$kode = trim(strtoupper($item['kode_mk']));
			$key_prodi = $kode . '|' . $id_sms;

			if (isset(self::$_kurikulum_map[$key_prodi])) {
				$item['semester'] = self::$_kurikulum_map[$key_prodi];
			} elseif (isset(self::$_kurikulum_global_map[$kode])) {
				$item['semester'] = self::$_kurikulum_global_map[$kode];
			} else {
				$item['semester'] = NULL;
			}
		}
		unset($item);

		// 7. Urutkan berdasarkan semester kurikulum (1 s/d 8), lalu kode_mk
		usort($data_raw, function($a, $b) {
			$smt_a = !empty($a['semester']) ? (int)$a['semester'] : 99;
			$smt_b = !empty($b['semester']) ? (int)$b['semester'] : 99;
			if ($smt_a == $smt_b) {
				return strcmp($a['kode_mk'], $b['kode_mk']);
			}
			return ($smt_a < $smt_b) ? -1 : 1;
		});

		// 8. Hitung SKS, Mutu, IPK, dan siapkan data detail
		$data_insert = [];
		$total_sks = 0;
		$total_sks_indeks = 0;
		$no_urut = 1;

		foreach ($data_raw as $val) {
			$total_sks += (int)$val['sks_mk'];
			$total_sks_indeks += ((int)$val['sks_mk'] * (float)$val['nilai_indeks']);

			$data_insert[] = [
				'no_urut' => $no_urut++,
				'nipd' => $nipd,
				'kode_mk' => $val['kode_mk'],
				'nm_mk' => $val['nm_mk'],
				'sks_mk' => (int)$val['sks_mk'],
				'nilai_huruf' => $val['nilai_huruf'],
				'nilai_indeks' => (float)$val['nilai_indeks'],
				'semester' => $val['semester'],
				'id_smt' => $val['id_smt']
			];
		}

		$ipk = ($total_sks > 0) ? round($total_sks_indeks / $total_sks, 2) : 0;

		// 9. Simpan ke database (Master & Detail)
		$cek = $this->db->where('nipd', $nipd)->get($this->table);
		if ($cek->num_rows() > 0) {
			$data_master = [
				'id_sms' => $id_sms,
				'jumlah_sks' => $total_sks,
				'jumlah_sks_indeks' => $total_sks_indeks,
				'indeks_prestasi_kumulatif' => $ipk,
				'change_by' => $user_id,
				'change_date' => date('Y-m-d H:i:s')
			];
			$this->db->where('nipd', $nipd)->update($this->table, $data_master);
		} else {
			$data_master = [
				'nipd' => $nipd,
				'id_sms' => $id_sms,
				'jumlah_sks' => $total_sks,
				'jumlah_sks_indeks' => $total_sks_indeks,
				'indeks_prestasi_kumulatif' => $ipk,
				'tanggal_transkrip' => '',
				'create_by' => $user_id,
				'create_date' => date('Y-m-d H:i:s')
			];
			$this->db->insert($this->table, $data_master);
		}

		$this->db->where('nipd', $nipd)->delete('transkrip_sementara_detail_2020');
		if (!empty($data_insert)) {
			$this->db->insert_batch('transkrip_sementara_detail_2020', $data_insert);
		}

		return true;
	}

	public function save($data, $nipd)
	{
		return $this->hitung_dan_simpan($nipd);
	}

	public function f_koreksi()
	{
		$dd[''] = 'Please Select';
		$dd['0'] = 'Tidak';
		$dd['1'] = 'Ya';

		return $dd;
	}

	/**
	 * Mengambil seluruh data Academic Record mahasiswa:
	 * 1. Transkrip lengkap (1 tabel berurutan semester kurikulum)
	 * 2. Pemantauan riwayat mata kuliah yang bernilai D atau E (status sudah diulang / belum diulang)
	 */
	public function get_academic_record_data($nipd)
	{
		$this->init_kurikulum_cache();

		// 1. Data Mahasiswa
		$mhs = $this->db->select('mp.id_reg_pd, mp.id_reg_pd_feeder, mp.id_sms, mp.nipd, mp.tgl_masuk_sp, mp.angkatan, m.nm_pd, m.tmpt_lahir, m.tgl_lahir, m.custom_nama, m.custom_tgl_lahir, s.nm_lemb as nm_prodi')
			->from('mahasiswa_pt mp')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms', 'left')
			->where('mp.nipd', $nipd)
			->get()->row();

		if (!$mhs) {
			return false;
		}

		$id_reg_pd = $mhs->id_reg_pd;
		$id_reg_pd_feeder = $mhs->id_reg_pd_feeder;
		$id_sms = $mhs->id_sms;

		// 2. Kumpulkan seluruh record nilai (Transfer, Arsip, Baru)
		$raw_all = [];

		if (!empty($id_reg_pd_feeder)) {
			$q_tf = $this->db->where('nt.id_reg_pd', $id_reg_pd_feeder)
				->where('nt.nilai_huruf_diakui IS NOT NULL', null, false)
				->where("nt.nilai_huruf_diakui != ''", null, false)
				->select('mk.kode_mk, mk.nm_mk, nt.nilai_huruf_diakui, nt.nilai_angka_diakui, nt.sks_diakui')
				->from('nilai_transfer nt')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = nt.id_mk')
				->get()->result();

			foreach ($q_tf as $val) {
				$raw_all[] = [
					'kode_mk'      => $val->kode_mk,
					'nm_mk'        => $val->nm_mk,
					'sks_mk'       => (int)$val->sks_diakui,
					'nilai_huruf'  => trim((string)($val->nilai_huruf_diakui ?? '')),
					'nilai_indeks' => (float)$val->nilai_angka_diakui,
					'id_smt'       => '0',
					'sumber'       => 'Transfer'
				];
			}
		}

		if (!empty($id_reg_pd)) {
			$q_an = $this->db->select('mk.kode_mk, mk.nm_mk, an.nilai_huruf, an.nilai_indeks, sk.sks_mk, sk.id_smt')
				->from('arsip_nilai an')
				->join('siakad_kelas_kuliah sk', 'sk.id_kls = an.id_kls')
				->join('mata_kuliah mk', 'mk.id_mk = sk.id_mk')
				->where('an.id_reg_pd', $id_reg_pd)
				->where('an.nilai_huruf IS NOT NULL', null, false)
				->where("an.nilai_huruf != ''", null, false)
				->order_by('sk.id_smt', 'ASC')
				->get()->result();

			foreach ($q_an as $val) {
				$raw_all[] = [
					'kode_mk'      => $val->kode_mk,
					'nm_mk'        => $val->nm_mk,
					'sks_mk'       => (int)$val->sks_mk,
					'nilai_huruf'  => trim((string)($val->nilai_huruf ?? '')),
					'nilai_indeks' => (float)$val->nilai_indeks,
					'id_smt'       => (string)$val->id_smt,
					'sumber'       => 'Arsip'
				];
			}
		}

		$q_nb = $this->db->where('nb.nipd', $nipd)
			->where('nb.nilai_huruf IS NOT NULL', null, false)
			->where("nb.nilai_huruf != ''", null, false)
			->select('mk.kode_mk, mk.nm_mk, nb.nilai_huruf, nb.nilai_indeks, fk.sks_mk, fk.id_smt')
			->from('siakad_nilai_baru nb')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->order_by('fk.id_smt', 'ASC')
			->get()->result();

		foreach ($q_nb as $val) {
			$raw_all[] = [
				'kode_mk'      => $val->kode_mk,
				'nm_mk'        => $val->nm_mk,
				'sks_mk'       => (int)$val->sks_mk,
				'nilai_huruf'  => trim((string)($val->nilai_huruf ?? '')),
				'nilai_indeks' => (float)$val->nilai_indeks,
				'id_smt'       => (string)$val->id_smt,
				'sumber'       => 'Baru'
			];
		}

		// 3. Cari seluruh id_smt unik yang terurut untuk memetakan Semester 1, Semester 2, dst.
		$unique_smts = [];
		foreach ($raw_all as $r) {
			if (!empty($r['id_smt']) && $r['id_smt'] !== '0') {
				$unique_smts[$r['id_smt']] = true;
			}
		}
		ksort($unique_smts);
		$smt_keys = array_keys($unique_smts);
		$smt_ke_map = [];
		foreach ($smt_keys as $idx => $smt) {
			$smt_ke_map[$smt] = $idx + 1;
		}

		// 4. Kelompokkan per MK untuk mendeteksi mata kuliah mengulang & nilai tertinggi
		$by_mk = [];
		foreach ($raw_all as $idx => $item) {
			$key = trim(strtoupper($item['kode_mk']));
			if (empty($key)) {
				$key = trim(strtolower($item['nm_mk']));
			}
			$by_mk[$key] ??= [];
			$by_mk[$key][] = [
				'idx'          => $idx,
				'nilai_indeks' => (float)$item['nilai_indeks'],
				'id_smt'       => $item['id_smt']
			];
		}

		$best_idx = [];
		$repeated_keys = [];
		$retake_smt_map = [];
		$best_pass_map = [];

		foreach ($by_mk as $k => $entries) {
			if (count($entries) > 1) {
				$repeated_keys[$k] = true;

				// Urutkan kronologis semester ASC untuk mapping pengulangan
				$chronological = $entries;
				usort($chronological, fn($a, $b) => strcmp($a['id_smt'], $b['id_smt']));

				// Cari indeks nilai terbaik
				$best_entry = $entries[0];
				foreach ($entries as $e) {
					if ((float)$e['nilai_indeks'] > (float)$best_entry['nilai_indeks']) {
						$best_entry = $e;
					} elseif ((float)$e['nilai_indeks'] == (float)$best_entry['nilai_indeks']) {
						if ($e['id_smt'] > $best_entry['id_smt']) {
							$best_entry = $e;
						}
					}
				}
				$best_idx[$best_entry['idx']] = true;

				// Cek apakah nilai terbaik untuk MK ini lulus (nilai selain E, karena nilai D sudah dianggap lulus)
				$best_nh = strtoupper(trim($raw_all[$best_entry['idx']]['nilai_huruf']));
				$best_indeks = (float)$raw_all[$best_entry['idx']]['nilai_indeks'];
				$is_best_passed = ($best_nh !== 'E' && $best_indeks > 0);
				$best_pass_map[$k] = $is_best_passed;

				// Untuk setiap attempt yang bukan terbaik, tentukan semester saat diulang
				for ($i = 0; $i < count($chronological); $i++) {
					$curr_idx = $chronological[$i]['idx'];
					if ($curr_idx !== $best_entry['idx']) {
						if (isset($chronological[$i + 1])) {
							$retake_smt_map[$curr_idx] = $chronological[$i + 1]['id_smt'];
						} else {
							$retake_smt_map[$curr_idx] = $best_entry['id_smt'];
						}
					}
				}
			} else {
				$best_idx[$entries[0]['idx']] = true;
			}
		}

		// 5. Susun seluruh baris rekam akademik (1 tabel utuh)
		$academic_records = [];
		$total_sks_diambil = 0;
		$total_sks_diakui = 0;
		$total_mutu_diakui = 0;
		$count_e_belum_diulang = 0;
		$count_d_disarankan = 0;
		$count_de_sudah_diulang = 0;

		foreach ($raw_all as $idx => $item) {
			$key = trim(strtoupper($item['kode_mk']));
			if (empty($key)) {
				$key = trim(strtolower($item['nm_mk']));
			}

			$is_ulang = isset($repeated_keys[$key]);
			$is_best = isset($best_idx[$idx]);
			$nh = strtoupper(trim($item['nilai_huruf']));
			$indeks = (float)$item['nilai_indeks'];
			$sks = (int)$item['sks_mk'];
			$bobot = $sks * $indeks;
			$smt_ke = $smt_ke_map[$item['id_smt']] ?? 0;

			$total_sks_diambil += $sks;
			if ($is_best) {
				$total_sks_diakui += $sks;
				$total_mutu_diakui += $bobot;
			}

			// Tentukan status baris
			$smt_target = $retake_smt_map[$idx] ?? '';
			if (!$is_ulang) {
				if ($nh === 'E') {
					$status_code = 'belum_diulang';
					$status_label = 'Belum Diulang';
					$count_e_belum_diulang++;
				} elseif ($nh === 'D') {
					$status_code = 'disarankan_mengulang';
					$status_label = 'Disarankan mengulang';
					$count_d_disarankan++;
				} else {
					$status_code = 'lulus';
					$status_label = 'Lulus';
				}
			} else {
				if ($is_best) {
					if ($nh === 'E') {
						$status_code = 'diulang_belum_lulus_terbaik';
						$status_label = 'Belum Lulus (Nilai E)';
						$count_e_belum_diulang++;
					} elseif ($nh === 'D') {
						$status_code = 'disarankan_mengulang';
						$status_label = 'Disarankan mengulang';
						$count_d_disarankan++;
					} else {
						$status_code = 'diulang_terbaik';
						$status_label = 'Diulang (Nilai Diakui)';
						$count_de_sudah_diulang++;
					}
				} else {
					$is_passed = $best_pass_map[$key] ?? false;
					if ($is_passed) {
						$status_code = 'diulang_lulus';
						$status_label = 'Diulang dan lulus';
					} else {
						$status_code = 'diulang_belum_lulus';
						$status_label = 'Diulang belum lulus';
					}
				}
			}

			$academic_records[] = [
				'id_smt'       => $item['id_smt'],
				'smt_ke'       => $smt_ke,
				'smt_label'    => $smt_ke > 0 ? "Semester $smt_ke" : "Transfer",
				'kode_mk'      => $item['kode_mk'],
				'nm_mk'        => $item['nm_mk'],
				'sks_mk'       => $sks,
				'nilai_huruf'  => $item['nilai_huruf'],
				'nilai_indeks' => $indeks,
				'bobot'        => $bobot,
				'is_ulang'     => $is_ulang,
				'is_best'      => $is_best,
				'status_code'  => $status_code,
				'status_label' => $status_label,
				'smt_target'   => $smt_target
			];
		}

		// Urutkan berdasarkan semester yang diambil (id_smt ASC mulai semester 1), lalu kode_mk ASC
		usort($academic_records, function($a, $b) {
			if ($a['id_smt'] == $b['id_smt']) {
				return strcmp($a['kode_mk'], $b['kode_mk']);
			}
			return strcmp($a['id_smt'], $b['id_smt']);
		});

		// 6. Ambil data dari tabel kuliah_mahasiswa untuk mencocokkan IPS & IPK tiap semester
		$km_map = [];
		$this->db->select('id_smt, ips, sks_smt, ipk, sks_total, id_stat_mhs')
			->from('kuliah_mahasiswa')
			->group_start()
				->where('nipd', $nipd);
		if (!empty($id_reg_pd)) {
			$this->db->or_where('id_reg_pd', $id_reg_pd);
		}
		if (!empty($id_reg_pd_feeder)) {
			$this->db->or_where('id_reg_pd', $id_reg_pd_feeder);
		}
		$this->db->group_end()
			->order_by('id_smt', 'ASC');
		$q_km = $this->db->get()->result();

		foreach ($q_km as $km) {
			$smt_key = trim((string)($km->id_smt ?? ''));
			$km_map[$smt_key] = [
				'ips'       => (float)$km->ips,
				'sks_smt'   => (int)$km->sks_smt,
				'ipk'       => (float)$km->ipk,
				'sks_total' => (int)$km->sks_total
			];
		}

		// Hitung ringkasan per semester dengan pencocokan record kuliah_mahasiswa (dan fallback kalkulasi)
		$smt_calc = [];
		$kum_sks = 0;
		$kum_mutu = 0;

		foreach ($academic_records as $ar) {
			$s = $ar['id_smt'];
			$smt_calc[$s] ??= [
					'sks'   => 0,
					'bobot' => 0
				];
			$smt_calc[$s]['sks'] += (int)$ar['sks_mk'];
			$smt_calc[$s]['bobot'] += (float)$ar['bobot'];
		}

		$semester_summary = [];
		foreach ($smt_calc as $s => $calc) {
			$kum_sks += $calc['sks'];
			$kum_mutu += $calc['bobot'];
			$ips_fallback = ($calc['sks'] > 0) ? round($calc['bobot'] / $calc['sks'], 2) : 0;
			$ipk_fallback = ($kum_sks > 0) ? round($kum_mutu / $kum_sks, 2) : 0;

			if (isset($km_map[$s])) {
				$semester_summary[$s] = [
					'ips'       => (float)$km_map[$s]['ips'],
					'ipk'       => (float)$km_map[$s]['ipk'],
					'sks_smt'   => ($km_map[$s]['sks_smt'] > 0) ? (int)$km_map[$s]['sks_smt'] : $calc['sks'],
					'sks_total' => (int)$km_map[$s]['sks_total'],
					'source'    => 'kuliah_mahasiswa'
				];
			} else {
				$semester_summary[$s] = [
					'ips'       => $ips_fallback,
					'ipk'       => $ipk_fallback,
					'sks_smt'   => $calc['sks'],
					'sks_total' => $kum_sks,
					'source'    => 'calculated'
				];
			}
		}

		$ipk = ($total_sks_diakui > 0) ? round($total_mutu_diakui / $total_sks_diakui, 2) : 0;

		return [
			'mhs'                    => $mhs,
			'academic_records'       => $academic_records,
			'semester_summary'       => $semester_summary,
			'total_sks_diambil'      => $total_sks_diambil,
			'total_sks_diakui'       => $total_sks_diakui,
			'total_mutu_diakui'      => $total_mutu_diakui,
			'ipk'                    => $ipk,
			'total_mk_diambil'       => count($academic_records),
			'total_mk_mengulang'     => count($repeated_keys),
			'count_de_belum_diulang' => ($count_e_belum_diulang + $count_d_disarankan),
			'count_e_belum_diulang'  => $count_e_belum_diulang,
			'count_d_disarankan'     => $count_d_disarankan,
			'count_de_sudah_diulang' => $count_de_sudah_diulang
		];
	}
}
