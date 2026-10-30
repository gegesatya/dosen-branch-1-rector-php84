<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_nilai_berita_acara_model extends CI_Model
{
	public $table = 'siakad_skripsi_pengajuan_judul';
	public $column_order = [
		null,
		'm.nm_pd',
		'sempro_nilai_rerata',
		'semhas_nilai_rerata',
		'sidang_nilai_rerata'
	];
	public $column_search = [
		'pj.nipd',
		'm.nm_pd',
		'pj.judul_ditetapkan',
		'pj.judul_skripsi_1',
		's.nm_lemb'
	];
	public $order = ['pj.id_judul_skripsi' => 'desc'];

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Build base query for DataTables
	 */
	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->select("
			pj.id_judul_skripsi,
			pj.nipd,
			m.nm_pd,
			s.nm_lemb as nama_prodi,
			s.id_sms,
			COALESCE(NULLIF(pj.judul_ditetapkan, ''), pj.judul_skripsi_1) as judul_ta,
			pj.dosen_pembimbing_1,
			pj.dosen_pembimbing_2,
			pj.dosen_penguji_sempro_1,
			pj.dosen_penguji_sempro_2,
			pj.dosen_penguji_sempro_3,
			MAX(ss_sidang.penguji_sidang_1) as penguji_sidang_1,
			MAX(ss_sidang.penguji_sidang_2) as penguji_sidang_2,
			MAX(ss_sidang.penguji_sidang_3) as penguji_sidang_3,

			-- Peran Dosen login
			CASE 
				WHEN pj.dosen_pembimbing_1 = '{$id_ptk}' THEN 'Pembimbing Utama'
				WHEN pj.dosen_pembimbing_2 = '{$id_ptk}' THEN 'Pembimbing Pendamping'
				WHEN (pj.dosen_penguji_sempro_1 = '{$id_ptk}' OR pj.dosen_penguji_sempro_2 = '{$id_ptk}' OR pj.dosen_penguji_sempro_3 = '{$id_ptk}') THEN 'Penguji Sempro/Semhas'
				WHEN (MAX(ss_sidang.penguji_sidang_1) = '{$id_ptk}' OR MAX(ss_sidang.penguji_sidang_2) = '{$id_ptk}' OR MAX(ss_sidang.penguji_sidang_3) = '{$id_ptk}') THEN 'Penguji Sidang'
				ELSE 'Penilai'
			END as peran_dosen,
			
			-- SEMPRO
			MAX(ss_sempro.status_undangan) as sempro_status_undangan,
			MAX(ss_sempro.tanggal_seminar) as sempro_tanggal_seminar,
			MAX(ns_p1.sempro_hasil) as sempro_hasil_p1,
			AVG(CASE WHEN ns.nilai_akhir > 0 THEN ns.nilai_akhir ELSE NULL END) as sempro_nilai_rerata,
			COUNT(CASE WHEN ns.nilai_akhir > 0 THEN 1 ELSE NULL END) as sempro_jumlah_penilai,
			
			-- SEMHAS
			MAX(ss_semhas.status_undangan) as semhas_status_undangan,
			MAX(ss_semhas.tanggal_seminar) as semhas_tanggal_seminar,
			MAX(ns_p1.semhas_hasil) as semhas_hasil_p1,
			AVG(CASE WHEN ns.semhas_nilai_akhir > 0 THEN ns.semhas_nilai_akhir ELSE NULL END) as semhas_nilai_rerata,
			COUNT(CASE WHEN ns.semhas_nilai_akhir > 0 THEN 1 ELSE NULL END) as semhas_jumlah_penilai,
			
			-- SIDANG
			MAX(ss_sidang.status_undangan) as sidang_status_undangan,
			MAX(ss_sidang.tanggal_seminar) as sidang_tanggal_seminar,
			MAX(ns_p1.sidang_hasil) as sidang_hasil_p1,
			MAX(ns_p1.sidang_nilai_mutu) as sidang_nilai_mutu,
			AVG(CASE WHEN ns.sidang_nilai_akhir > 0 THEN ns.sidang_nilai_akhir ELSE NULL END) as sidang_nilai_rerata,
			COUNT(CASE WHEN ns.sidang_nilai_akhir > 0 THEN 1 ELSE NULL END) as sidang_jumlah_penilai
		", FALSE);

		$this->db->from('siakad_skripsi_pengajuan_judul pj');
		$this->db->join('mahasiswa_pt mp', 'mp.nipd = pj.nipd');
		$this->db->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->join('sms s', 's.id_sms = pj.id_sms');

		// Join Seminar/Sidang schedules
		$this->db->join('siakad_skripsi_seminar_sidang ss_sempro', 'ss_sempro.id_judul_skripsi = pj.id_judul_skripsi AND ss_sempro.kategori_pengajuan = 1', 'left');
		$this->db->join('siakad_skripsi_seminar_sidang ss_semhas', 'ss_semhas.id_judul_skripsi = pj.id_judul_skripsi AND ss_semhas.kategori_pengajuan = 2', 'left');
		$this->db->join('siakad_skripsi_seminar_sidang ss_sidang', 'ss_sidang.id_judul_skripsi = pj.id_judul_skripsi AND ss_sidang.kategori_pengajuan = 3', 'left');

		// Join Pembimbing 1 grade/verdict
		$this->db->join('siakad_skripsi_nilai_sempro ns_p1', 'ns_p1.id_judul_skripsi = pj.id_judul_skripsi AND ns_p1.id_penilai = pj.dosen_pembimbing_1', 'left');

		// Join all grades for average calculation
		$this->db->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = pj.id_judul_skripsi', 'left');

		$this->db->where('pj.status_sk !=', 3);
		// ONLY mahasiswa yang belum lulus (id_jns_keluar IS NULL)
		$this->db->where('mp.id_jns_keluar IS NULL', NULL, FALSE);

		// STRICT LECTURER FILTER: Only students where the logged-in dosen is Pembimbing or Penguji/Penilai
		$this->db->group_start();
		$this->db->where('pj.dosen_pembimbing_1', $id_ptk)
			->or_where('pj.dosen_pembimbing_2', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_1', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_2', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_3', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_1', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_2', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_3', $id_ptk)
			->or_where('ns.id_penilai', $id_ptk);
		$this->db->group_end();

		$this->db->group_by('pj.id_judul_skripsi, pj.nipd, m.nm_pd, s.nm_lemb, s.id_sms, pj.judul_ditetapkan, pj.judul_skripsi_1, pj.dosen_pembimbing_1, pj.dosen_pembimbing_2, pj.dosen_penguji_sempro_1, pj.dosen_penguji_sempro_2, pj.dosen_penguji_sempro_3');

		// Search
		$search_val = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
		if (!empty($search_val)) {
			$this->db->group_start();
			$i = 0;
			foreach ($this->column_search as $item) {
				if ($i === 0) {
					$this->db->like($item, $search_val);
				} else {
					$this->db->or_like($item, $search_val);
				}
				$i++;
			}
			$this->db->group_end();
		}

		// Order
		if (isset($_POST['order'])) {
			$col_idx = (int)$_POST['order']['0']['column'];
			$col_dir = $_POST['order']['0']['dir'];
			if (isset($this->column_order[$col_idx]) && $this->column_order[$col_idx] !== null) {
				$this->db->order_by($this->column_order[$col_idx], $col_dir);
			} else {
				$this->db->order_by('pj.id_judul_skripsi', 'desc');
			}
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if (isset($_POST['length']) && $_POST['length'] != -1) {
			$this->db->limit((int)$_POST['length'], (int)$_POST['start']);
		}
		return $this->db->get()->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->select('pj.id_judul_skripsi')
			->from('siakad_skripsi_pengajuan_judul pj')
			->join('mahasiswa_pt mp', 'mp.nipd = pj.nipd')
			->join('siakad_skripsi_seminar_sidang ss_sidang', 'ss_sidang.id_judul_skripsi = pj.id_judul_skripsi AND ss_sidang.kategori_pengajuan = 3', 'left')
			->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = pj.id_judul_skripsi', 'left')
			->where('pj.status_sk !=', 3)
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->group_start()
			->where('pj.dosen_pembimbing_1', $id_ptk)
			->or_where('pj.dosen_pembimbing_2', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_1', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_2', $id_ptk)
			->or_where('pj.dosen_penguji_sempro_3', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_1', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_2', $id_ptk)
			->or_where('ss_sidang.penguji_sidang_3', $id_ptk)
			->or_where('ns.id_penilai', $id_ptk)
			->group_end()
			->group_by('pj.id_judul_skripsi');

		return $this->db->get()->num_rows();
	}

	public function get_kpi_stats()
	{
		$this->_get_datatables_query();
		$list = $this->db->get()->result();

		$total = count($list);
		$sempro_done = 0;
		$semhas_done = 0;
		$sidang_done = 0;

		foreach ($list as $r) {
			if (!empty($r->sempro_hasil_p1) || (!empty($r->sempro_nilai_rerata) && $r->sempro_nilai_rerata > 0)) {
				$sempro_done++;
			}
			if (!empty($r->semhas_hasil_p1) || (!empty($r->semhas_nilai_rerata) && $r->semhas_nilai_rerata > 0)) {
				$semhas_done++;
			}
			if (!empty($r->sidang_hasil_p1) || (!empty($r->sidang_nilai_rerata) && $r->sidang_nilai_rerata > 0)) {
				$sidang_done++;
			}
		}

		return [
			'total'        => $total,
			'sempro_done'  => $sempro_done,
			'semhas_done'  => $semhas_done,
			'sidang_done'  => $sidang_done
		];
	}
}
