<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Daftar_instansi_model extends CI_Model
{

	public $table = 'simlapkerma_tbl_daftar_instansi';
	public $column = ['nm_instansi'];
	public $order = ['nm_instansi' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->from('simlapkerma_tbl_daftar_instansi i')
			->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi', 'LEFT')
			->join('simlapkerma_tbl_instansi ji', 'ji.id_jenis_instansi = si.id_instansi', 'LEFT')
			->join('simlapkerma_tbl_bidang_usaha bu', 'i.id_bidang_usaha = bu.id_bidang_usaha', 'LEFT');
		$i = 0;

		foreach ($this->column as $item) {
			if (isset($_POST['search']['value'])) {
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

		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from('simlapkerma_tbl_daftar_instansi i')
			->join('simlapkerma_tbl_sub_instansi si', 'i.id_sub_instansi = si.id_sub_instansi', 'LEFT')
			->join('simlapkerma_tbl_instansi ji', 'ji.id_jenis_instansi = si.id_instansi', 'LEFT')
			->join('simlapkerma_tbl_bidang_usaha bu', 'i.id_bidang_usaha = bu.id_bidang_usaha', 'LEFT');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_daftar_instansi', $id);
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
		$this->db->where('id_daftar_instansi', $id);
		$this->db->delete($this->table);
	}
}
