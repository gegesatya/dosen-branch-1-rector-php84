<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_persetujuan_pendampingan_model extends CI_Model
{
	public $table = 'e_kinerja_pendampingan';
	public $column_order = [null, 'd.nm_ptk', 'smt.nm_smt', 'kp.kategori', 'kp.jumlah', null, 'kp.approve_by_kaprodi', null];
	public $column_search = ['d.nm_ptk', 'kp.kategori', 'smt.nm_smt'];
	public $order = ['kp.id_smt' => 'desc', 'd.nm_ptk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_sms)
	{
		$this->db->select('kp.*, d.nm_ptk, smt.nm_smt AS nama_semester');
		$this->db->from($this->table . ' kp');
		$this->db->join('users u', 'u.id = kp.create_by');
		$this->db->join('siakad_dosen d', 'd.id_ptk = u.id_ptk');
		$this->db->join('semester smt', 'smt.id_smt = kp.id_smt');
		$this->db->where('d.id_sms', $id_sms);

		$i = 0;
		foreach ($this->column_search as $item) {
			if (isset($_POST['search']['value']) && $_POST['search']['value']) {
				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}
				if (count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			foreach ($order as $key => $val) {
				$this->db->order_by($key, $val);
			}
		}
	}

	public function get_datatables($id_sms)
	{
		$this->_get_datatables_query($id_sms);
		if (isset($_POST['length']) && $_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_sms)
	{
		$this->_get_datatables_query($id_sms);
		return $this->db->count_all_results();
	}

	public function count_all($id_sms)
	{
		$this->db->from($this->table . ' kp');
		$this->db->join('users u', 'u.id = kp.create_by');
		$this->db->join('siakad_dosen d', 'd.id_ptk = u.id_ptk');
		$this->db->where('d.id_sms', $id_sms);
		return $this->db->count_all_results();
	}
}
