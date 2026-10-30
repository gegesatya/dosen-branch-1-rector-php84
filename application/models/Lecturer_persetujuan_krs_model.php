<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_persetujuan_krs_model extends CI_Model
{

	public $table = 'siakad_krs';
	public $column = ['nm_pd', 'mahasiswa_pt.nipd'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_status_krs = NULL, $angkatan = NULL, $is_counting = FALSE)
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		$id_ptk = $this->session->userdata('id_ptk');

		$where = [
			'siakad_krs.id_smt' => $id_smt,
			'mahasiswa.dosen_pa' => $id_ptk
		];

		if (!empty($id_status_krs) && $id_status_krs != 0) {
			$where['siakad_krs.id_status_krs'] = $id_status_krs;
		}

		if ($is_counting) {
			$this->db->select('siakad_krs.id_rencana_studi');
		} else {
			$this->db->select('
				siakad_krs.id_rencana_studi,
				siakad_krs.nipd,
				siakad_krs.id_smt,
				siakad_krs.id_status_krs,
				siakad_krs.approved_date,
				siakad_krs.pengajuan_date,
				siakad_krs.change_date,
				siakad_krs.catatan_dosen_wali,
				siakad_krs.registrasi_no_hp,
				siakad_krs.ip_kumulatif,
				siakad_krs.ips,
				mahasiswa.nm_pd,
				mahasiswa.foto,
				mahasiswa_pt.tgl_masuk_sp,
				siakad_status_krs.nm_status_krs,
				mahasiswa_kip.skema as kip_skema
			');
		}

		$this->db->from('siakad_krs')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_krs.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->join('siakad_status_krs', 'siakad_status_krs.id_status_krs = siakad_krs.id_status_krs')
			->join('mahasiswa_kip', 'mahasiswa_kip.nipd = siakad_krs.nipd', 'left')
			->where($where)
			->where('mahasiswa_pt.id_jns_keluar IS NULL');

		if (!empty($angkatan) && $angkatan != '0') {
			$angkatan_clean = $this->db->escape_str($angkatan);
			$angkatan_2d = strlen((string)$angkatan) == 4 ? substr((string)$angkatan, 2, 2) : (string)$angkatan;
			$angkatan_2d_clean = $this->db->escape_str($angkatan_2d);

			$this->db->where("(YEAR(mahasiswa_pt.tgl_masuk_sp) = '{$angkatan_clean}' OR SUBSTR(siakad_krs.nipd, 1, 2) = '{$angkatan_2d_clean}')", NULL, FALSE);
		}

		$i = 0;
		$search_value = $this->input->post('search')['value'] ?? '';
		foreach ($this->column as $item) {
			if ($search_value) {
				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $search_value);
				} else {
					$this->db->or_like($item, $search_value);
				}
				if (count($this->column) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if (!$is_counting) {
			$order = $this->input->post('order');
			if (isset($order[0]['column'])) {
				$order_col = $order[0]['column'];
				if (isset($this->column[$order_col])) {
					$this->db->order_by($this->column[$order_col], $order[0]['dir']);
				}
			} else if (isset($this->order)) {
				$order_default = $this->order;
				$this->db->order_by(key($order_default), $order_default[key($order_default)]);
			}
		}
	}

	public function get_datatables($id_status_krs = NULL, $angkatan = NULL)
	{
		$this->_get_datatables_query($id_status_krs, $angkatan, FALSE);
		$length = $this->input->post('length') !== NULL ? (int)$this->input->post('length') : 10;
		$start = $this->input->post('start') !== NULL ? (int)$this->input->post('start') : 0;
		if ($length != -1) {
			$this->db->limit($length, $start);
		}
		$query = $this->db->get();
		$list = $query->result();

		if (!empty($list)) {
			$this->_hydrate_datatables_extra($list);
		}

		return $list;
	}

	/**
	 * Batch hydrates total_sks_detail, total_sks_nilai, and low_grade_count
	 * for only the paginated rows to eliminate heavy correlated subqueries.
	 */
	private function _hydrate_datatables_extra(&$list)
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		$rencana_ids = [];
		$nipds = [];

		foreach ($list as $row) {
			if (!empty($row->id_rencana_studi)) {
				$rencana_ids[] = $row->id_rencana_studi;
			}
			if (!empty($row->nipd)) {
				$nipds[] = $row->nipd;
			}
		}

		$rencana_ids = array_values(array_unique($rencana_ids));
		$nipds = array_values(array_unique($nipds));

		// 1. Batch total_sks_detail (from siakad_krs_detail)
		$sks_detail_map = [];
		if (!empty($rencana_ids)) {
			$res_detail = $this->db->select('kd.id_rencana_studi, SUM(kk.sks_mk) as total_sks')
				->from('siakad_krs_detail kd')
				->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
				->where_in('kd.id_rencana_studi', $rencana_ids)
				->group_by('kd.id_rencana_studi')
				->get()->result();

			foreach ($res_detail as $d) {
				$sks_detail_map[$d->id_rencana_studi] = (int)$d->total_sks;
			}
		}

		// 2. Batch total_sks_nilai (from siakad_nilai_baru for active semester)
		$sks_nilai_map = [];
		if (!empty($nipds)) {
			$res_nilai = $this->db->select('sn.nipd, SUM(kk.sks_mk) as total_sks')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->where_in('sn.nipd', $nipds)
				->where('kk.id_smt', $id_smt)
				->group_by('sn.nipd')
				->get()->result();

			foreach ($res_nilai as $n) {
				$sks_nilai_map[$n->nipd] = (int)$n->total_sks;
			}
		}

		// 3. Batch low_grade_count (subjects with D or E in previous semesters not yet passed with A, B, or C)
		$low_grade_map = [];
		if (!empty($nipds)) {
			$res_grades = $this->db->select('sn.nipd, mk.kode_mk, sn.nilai_huruf')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where_in('sn.nipd', $nipds)
				->where('kk.id_smt <', $id_smt)
				->get()->result();

			$student_courses = [];
			foreach ($res_grades as $g) {
				$student_courses[$g->nipd][$g->kode_mk][] = strtoupper(trim((string)($g->nilai_huruf ?? '')));
			}

			foreach ($student_courses as $nipd => $courses) {
				$unresolved_fail_count = 0;
				foreach ($courses as $kode_mk => $grades) {
					$has_pass = false;
					$has_fail = false;
					foreach ($grades as $grade) {
						if (in_array($grade, ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])) {
							$has_pass = true;
						}
						if (in_array($grade, ['D', 'E'])) {
							$has_fail = true;
						}
					}
					if ($has_fail && !$has_pass) {
						$unresolved_fail_count++;
					}
				}
				$low_grade_map[$nipd] = $unresolved_fail_count;
			}
		}

		// 4. Batch last_ipk (from kuliah_mahasiswa)
		$ipk_map = [];
		if (!empty($nipds)) {
			$res_km = $this->db->select('nipd, ipk, id_smt')
				->from('kuliah_mahasiswa')
				->where_in('nipd', $nipds)
				->order_by('id_smt ASC')
				->get()->result();

			foreach ($res_km as $km) {
				$ipk_map[$km->nipd] = (float)$km->ipk;
			}
		}

		// Hydrate back to objects
		foreach ($list as &$item) {
			$item->total_sks_detail = $sks_detail_map[$item->id_rencana_studi] ?? 0;
			$item->total_sks_nilai = $sks_nilai_map[$item->nipd] ?? 0;
			$item->low_grade_count = $low_grade_map[$item->nipd] ?? 0;
			$item->last_ipk = $ipk_map[$item->nipd] ?? 0.0;
		}
	}

	public function count_filtered($id_status_krs = NULL, $angkatan = NULL)
	{
		$this->_get_datatables_query($id_status_krs, $angkatan, TRUE);
		return $this->db->count_all_results();
	}

	public function count_all($id_status_krs = NULL, $angkatan = NULL)
	{
		$this->_get_datatables_query($id_status_krs, $angkatan, TRUE);
		return $this->db->count_all_results();
	}

	public function get_list_angkatan($id_ptk)
	{
		$sql = "SELECT DISTINCT 
					CASE 
						WHEN mp.tgl_masuk_sp IS NOT NULL AND mp.tgl_masuk_sp > '1900-01-01' 
						THEN YEAR(mp.tgl_masuk_sp)
						ELSE CAST(CONCAT('20', SUBSTR(mp.nipd, 1, 2)) AS UNSIGNED)
					END AS angkatan
				FROM mahasiswa m
				JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
				WHERE m.dosen_pa = ? AND mp.id_jns_keluar IS NULL
				ORDER BY angkatan DESC";
		$query = $this->db->query($sql, [$id_ptk]);
		$result = $query->result();

		$options = ['0' => 'All (Semua Angkatan)'];
		if (!empty($result)) {
			foreach ($result as $row) {
				if (!empty($row->angkatan) && $row->angkatan > 2000) {
					$options[$row->angkatan] = (string)$row->angkatan;
				}
			}
		}

		if (count($options) <= 1) {
			$curr_year = (int) date('Y');
			for ($y = $curr_year; $y >= $curr_year - 6; $y--) {
				$options[$y] = (string) $y;
			}
		}

		return $options;
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_rencana_studi', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function f_status_krs()
	{
		$result = $this->db->where_in('id_status_krs', [3, 4, 1])
			->get('siakad_status_krs');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($row['id_status_krs'] == 1) {
					$row['nm_status_krs'] = 'Berikan kesempatan untuk edit KRS';
				} elseif ($row['id_status_krs'] == 3) {
					$row['nm_status_krs'] = 'Final (Disetujui)';
				}
				$dd[$row['id_status_krs']] = $row['nm_status_krs'];
			}
		}
		return $dd;
	}

	public function get_summary_stats($id_ptk)
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		
		// Single query with conditional aggregation for KRS data
		$row = $this->db->select("
			COUNT(CASE WHEN sk.id_status_krs = 2 THEN 1 END) as pending,
			COUNT(CASE WHEN sk.id_status_krs = 3 THEN 1 END) as approved,
			COUNT(CASE WHEN sk.id_status_krs = 1 THEN 1 END) as krs_draft,
			COUNT(sk.id_rencana_studi) as total_krs
		", FALSE)
		->from('siakad_krs sk')
		->join('mahasiswa_pt mp', 'mp.nipd = sk.nipd')
		->join('mahasiswa m', 'm.id_pd = mp.id_pd')
		->where(['m.dosen_pa' => $id_ptk, 'sk.id_smt' => $id_smt])
		->where('mp.id_jns_keluar IS NULL')
		->get()->row();

		// Determine active study period / cohorts for current semester
		$current_year = !empty($id_smt) ? (int)substr($id_smt, 0, 4) : (int)date('Y');
		$smt_digit = !empty($id_smt) ? (int)substr($id_smt, -1) : 1;
		$prev_smt = ($smt_digit == 1) ? ($current_year - 1) . '2' : $current_year . '1';
		$min_angkatan_2d = ($current_year - 3) % 100;
		$new_angkatan_2d = !empty($id_smt) ? (int)substr($id_smt, 2, 2) : 26;

		// Count active advisees who haven't created KRS in this active semester
		$row_belum = $this->db->select("COUNT(DISTINCT mp.nipd) as belum_krs")
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
			->where('m.dosen_pa', $id_ptk)
			->where('mp.id_jns_keluar IS NULL')
			->where('mp.tgl_keluar IS NULL')
			->where("CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) >= {$min_angkatan_2d}")
			->where("(
				mp.nipd IN (SELECT nipd FROM siakad_krs WHERE id_smt = '{$prev_smt}')
				OR mp.nipd IN (SELECT nipd FROM kuliah_mahasiswa WHERE id_smt = '{$prev_smt}')
				OR CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) >= {$new_angkatan_2d}
			)", NULL, FALSE)
			->where("mp.nipd NOT IN (SELECT nipd FROM siakad_krs WHERE id_smt = " . $this->db->escape($id_smt) . ")", NULL, FALSE)
			->get()->row();

		return (object)[
			'pending' => (int)($row->pending ?? 0),
			'approved' => (int)($row->approved ?? 0),
			'total_krs' => (int)($row->total_krs ?? 0),
			'krs_draft' => (int)($row->krs_draft ?? 0),
			'belum_krs' => (int)($row_belum->belum_krs ?? 0)
		];
	}

	public function get_list_belum_krs($id_ptk)
	{
		$id_smt = $this->session->userdata('tahun_ajaran');

		$current_year = !empty($id_smt) ? (int)substr($id_smt, 0, 4) : (int)date('Y');
		$smt_digit = !empty($id_smt) ? (int)substr($id_smt, -1) : 1;
		$prev_smt = ($smt_digit == 1) ? ($current_year - 1) . '2' : $current_year . '1';
		$min_angkatan_2d = ($current_year - 3) % 100;
		$new_angkatan_2d = !empty($id_smt) ? (int)substr($id_smt, 2, 2) : 26;

		$sql = "SELECT 
					mp.nipd, 
					m.nm_pd, 
					m.foto,
					m.telepon_seluler,
					mp.tgl_masuk_sp,
					mkip.skema as kip_skema,
					(SELECT registrasi_no_hp FROM siakad_krs WHERE nipd = mp.nipd ORDER BY id_rencana_studi DESC LIMIT 1) as no_hp_krs
				FROM mahasiswa m
				JOIN mahasiswa_pt mp ON mp.id_pd = m.id_pd
				LEFT JOIN mahasiswa_kip mkip ON mkip.nipd = mp.nipd
				WHERE m.dosen_pa = ? 
				  AND mp.id_jns_keluar IS NULL
				  AND mp.tgl_keluar IS NULL
				  AND CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) >= ?
				  AND (
					mp.nipd IN (SELECT nipd FROM siakad_krs WHERE id_smt = ?)
					OR mp.nipd IN (SELECT nipd FROM kuliah_mahasiswa WHERE id_smt = ?)
					OR CAST(SUBSTR(mp.nipd, 1, 2) AS UNSIGNED) >= ?
				  )
				  AND mp.nipd NOT IN (SELECT nipd FROM siakad_krs WHERE id_smt = ?)
				ORDER BY mp.nipd ASC";

		$query = $this->db->query($sql, [$id_ptk, $min_angkatan_2d, $prev_smt, $prev_smt, $new_angkatan_2d, $id_smt]);
		$list = $query->result();

		// Hydrate latest IPK & last semester from kuliah_mahasiswa
		$nipds = array_column($list, 'nipd');
		$ipk_map = [];
		if (!empty($nipds)) {
			$res_km = $this->db->select('nipd, ipk, id_smt, sks_total')
				->from('kuliah_mahasiswa')
				->where_in('nipd', $nipds)
				->order_by('id_smt ASC')
				->get()->result();

			foreach ($res_km as $km) {
				$ipk_map[$km->nipd] = $km;
			}
		}

		foreach ($list as &$std) {
			$last_km = $ipk_map[$std->nipd] ?? null;
			$std->last_ipk = $last_km ? (float)$last_km->ipk : 0.0;
			$std->last_smt = $last_km ? $last_km->id_smt : '-';
			$std->sks_total = $last_km ? (int)$last_km->sks_total : 0;
			
			// Best contact number
			$std->no_hp = !empty($std->telepon_seluler) ? $std->telepon_seluler : ($std->no_hp_krs ?? '');
		}

		return $list;
	}

	public function check_kip($nipd)
	{
		return $this->db->where('nipd', $nipd)->get('mahasiswa_kip')->row();
	}
}
