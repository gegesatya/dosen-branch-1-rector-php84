<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_pendampingan_model extends CI_Model
{
	public $table = 'e_kinerja_pendampingan';
	public $column_order = [null, 'sd.nm_ptk', 's.nm_smt', 'p.kategori', 'p.jumlah', 'p.approve_by_kaprodi', null, null];
	public $column_search = ['sd.nm_ptk', 's.nm_smt', 'p.kategori'];
	public $order = ['p.id_pendampingan' => 'desc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($is_wadir2 = false)
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->select('p.*, s.nm_smt, COALESCE(sd.nm_ptk, "-") as nm_ptk');
		$this->db->from($this->table . ' p');
		$this->db->join('semester s', 's.id_smt = p.id_smt', 'left');
		$this->db->join('siakad_dosen sd', 'sd.id_ptk = p.id_ptk', 'left');

		if (!$is_wadir2) {
			$this->db->where('p.id_ptk', $id_ptk);
		}

		$i = 0;
		foreach ($this->column_search as $item) {
			if (!empty($_POST['search']['value'])) {
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
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables($is_wadir2 = false)
	{
		$this->_get_datatables_query($is_wadir2);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($is_wadir2 = false)
	{
		$this->_get_datatables_query($is_wadir2);
		return $this->db->count_all_results();
	}

	public function count_all($is_wadir2 = false)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$this->db->from($this->table);
		if (!$is_wadir2) {
			$this->db->where('id_ptk', $id_ptk);
		}
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_pendampingan', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_pendampingan', $id);
		$this->db->delete($this->table);
	}
}
