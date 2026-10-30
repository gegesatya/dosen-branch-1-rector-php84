<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_sidang_model extends CI_Model
{

	public $table = 'siakad_skripsi_nilai_sempro';
	public $column = ['mp.nipd', 'm.nm_pd'];
	public $order = ['mp.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');

//         $this->db->where(['id_penilai' => $id_ptk, 'is_create_form_nilai_sidang' => 1, 'ss.kategori_pengajuan' => 3])
// 			->where('mp.nipd NOT IN (SELECT `nim` FROM `feeder_lulus_do`)', NULL, FALSE)
// 			->select('s.id_judul_skripsi, s.nipd, id_nilai_sempro, m.nm_pd, sidang_nilai_a, sidang_nilai_b, sidang_nilai_c, 
// 						sidang_nilai_d, sidang_nilai_e, sidang_nilai_akhir, dosen_penguji_sempro_1, 
// 						dosen_penguji_sempro_2, dosen_penguji_sempro_3, dosen_pembimbing_1')
// 			->from('siakad_skripsi_nilai_sempro s')
// 			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
// 			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
// 			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
// 			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = pj.id_judul_skripsi');

       	$this->db->where(['id_penilai' => $id_ptk, 'is_create_form_nilai_sidang' => 1, 'ss.kategori_pengajuan' => 3])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->select('s.id_judul_skripsi, s.nipd, id_nilai_sempro, m.nm_pd, sidang_nilai_a, sidang_nilai_b, sidang_nilai_c, 
						sidang_nilai_d, sidang_nilai_e, sidang_nilai_akhir, dosen_penguji_sempro_1, 
						dosen_penguji_sempro_2, dosen_penguji_sempro_3, dosen_pembimbing_1, is_arsip,
						(SELECT AVG(ns2.sidang_nilai_akhir) FROM siakad_skripsi_nilai_sempro ns2 WHERE ns2.id_judul_skripsi = s.id_judul_skripsi) as nilai_rerata,
						(SELECT COUNT(ns3.sidang_nilai_akhir) FROM siakad_skripsi_nilai_sempro ns3 WHERE ns3.id_judul_skripsi = s.id_judul_skripsi AND ns3.sidang_nilai_akhir <> 0) as hitung,
						(SELECT ns4.sidang_hasil FROM siakad_skripsi_nilai_sempro ns4 WHERE ns4.id_judul_skripsi = s.id_judul_skripsi AND ns4.id_penilai = pj.dosen_pembimbing_1 LIMIT 1) as hasil_pembimbing,
						(SELECT ns5.sidang_nilai_mutu FROM siakad_skripsi_nilai_sempro ns5 WHERE ns5.id_judul_skripsi = s.id_judul_skripsi AND ns5.id_penilai = pj.dosen_pembimbing_1 LIMIT 1) as nilai_mutu_pembimbing')
			->from('siakad_skripsi_nilai_sempro s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = pj.id_judul_skripsi');

		$i = 0;

		foreach ($this->column as $item) {
			if ($_POST['search']['value']) {

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
		if ($_POST['length'] != -1)
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
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->where(['id_penilai' => $id_ptk, 'is_create_form_nilai_sidang' => 1, 'ss.kategori_pengajuan' => 3])
			->where('NOT EXISTS (SELECT 1 FROM feeder_lulus_do fld WHERE fld.nim = mp.nipd)', NULL, FALSE)
			->select('s.id_judul_skripsi, s.nipd, id_nilai_sempro, m.nm_pd, sidang_nilai_a, sidang_nilai_b, sidang_nilai_c, 
						sidang_nilai_d, sidang_nilai_e, sidang_nilai_akhir, dosen_penguji_sempro_1, 
						dosen_penguji_sempro_2, dosen_penguji_sempro_3, dosen_pembimbing_1, is_arsip')
			->from('siakad_skripsi_nilai_sempro s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = pj.id_judul_skripsi');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->where('id_nilai_sempro', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update($this->table, $data, $where);

		return $this->db->affected_rows();
	}

	public function update_berita_acara_sempro($where, $data, $data2, $id_judul_skripsi)
	{
		$this->db->trans_begin();

		$this->db->update($this->table, $data, $where);
		$this->db->update('siakad_skripsi_pengajuan_judul', $data2, ['id_judul_skripsi' => $id_judul_skripsi]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}

		//return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}
}
