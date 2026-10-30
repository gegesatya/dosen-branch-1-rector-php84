<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_arsip_model extends CI_Model
{
	public $table = 'siakad_skripsi_pengajuan_judul';
	public $column = ['s.nipd', 'm.nm_pd', 'pj.judul_ditetapkan'];
	public $order = ['s.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Build the base DataTable query.
	 * Key design choices for performance:
	 *  - Drive off siakad_skripsi_nilai_sempro (s) filtered by id_penilai = $id_ptk first.
	 *    This shrinks the working set instantly to only rows belonging to this lecturer.
	 *  - INNER JOIN ns_lulus directly instead of a correlated EXISTS — MySQL can use
	 *    a compound index on (id_judul_skripsi, id_penilai, sidang_hasil) if available.
	 *  - ns_lulus.sidang_nilai_mutu is fetched from the JOIN row, not a per-row subquery.
	 */
	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db
			->select('pj.id_judul_skripsi, s.nipd, m.nm_pd, a.analisis_ipk,
					  TRIM(REPLACE(REPLACE(COALESCE(pj.judul_ditetapkan, pj.judul_skripsi_1), CHAR(13), \' \'), CHAR(10), \' \')) AS judul_ta,
					  MAX(ns_lulus.sidang_nilai_mutu) AS nilai_mutu,
					  mp.tgl_sk_yudisium,
					  pg.penguji_1, pg.penguji_2, pg.penguji_3', FALSE)
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_nilai_sempro ns_lulus',
				'ns_lulus.id_judul_skripsi = s.id_judul_skripsi
				 AND ns_lulus.id_penilai = pj.dosen_pembimbing_1
				 AND ns_lulus.sidang_hasil = \'LULUS\'')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_analisis a', 'a.nipd = s.nipd', 'left')
			// Subquery pivot: ambil semua penguji per id_judul (independen dari filter dosen login)
			->join('(SELECT
						  id_judul_skripsi,
						  MAX(CASE WHEN urutan = 1 THEN nm_ptk END) AS penguji_1,
						  MAX(CASE WHEN urutan = 2 THEN nm_ptk END) AS penguji_2,
						  MAX(CASE WHEN urutan = 3 THEN nm_ptk END) AS penguji_3
					  FROM (
						  SELECT sn.id_judul_skripsi, d.nm_ptk,
							  ROW_NUMBER() OVER (PARTITION BY sn.id_judul_skripsi ORDER BY sn.id_penilai) AS urutan
						  FROM siakad_skripsi_nilai_sempro sn
						  INNER JOIN siakad_dosen d ON d.id_ptk = sn.id_penilai
					  ) ranked
					  GROUP BY id_judul_skripsi) pg',
				'pg.id_judul_skripsi = pj.id_judul_skripsi', 'left')
			->where('s.id_penilai', $id_ptk)
			->group_by('pj.id_judul_skripsi');

		$i = 0;
		foreach ($this->column as $item) {
			if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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
			$this->db->limit((int)$_POST['length'], (int)$_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	/**
	 * Count total records for this lecturer.
	 * Cached in session per-lecturer to avoid re-running the join on every pagination click.
	 * Cache is invalidated when the lecturer session changes (id_ptk differs).
	 */
	public function count_all()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$cache_key = 'arsip_ta_count_' . md5($id_ptk);
		$cached = $this->session->userdata($cache_key);

		if ($cached !== FALSE && $cached !== NULL) {
			return $cached;
		}

		$this->db
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_nilai_sempro ns_lulus',
				'ns_lulus.id_judul_skripsi = s.id_judul_skripsi
				 AND ns_lulus.id_penilai = pj.dosen_pembimbing_1
				 AND ns_lulus.sidang_hasil = \'LULUS\'')
			->where('s.id_penilai', $id_ptk);

		$count = $this->db->count_all_results();
		$this->session->set_userdata($cache_key, $count);
		return $count;
	}
	
}
