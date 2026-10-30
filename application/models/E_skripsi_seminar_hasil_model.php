<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_hasil_model extends CI_Model
{
	public $table = 'siakad_skripsi_nilai_sempro';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Ambil satu record nilai sempro berdasarkan ID,
	 * termasuk data mahasiswa dan pengajuan judul.
	 */
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

	public function update_berita_acara_sempro($where, $data)
	{
		$this->db->trans_begin();
		$this->db->update($this->table, $data, $where);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}
}
