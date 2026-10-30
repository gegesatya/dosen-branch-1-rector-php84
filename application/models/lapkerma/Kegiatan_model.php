<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kegiatan_model extends CI_Model
{

	public $table = 'simlapkerma_tbl_kegiatan';
	public $column = ['judul_kegiatan'];
	public $order = ['judul_kegiatan' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->from('simlapkerma_tbl_kegiatan k')
			->join('simlapkerma_tbl_moa a', 'a.id_moa = k.id_moa', 'LEFT')
			->join('simlapkerma_tbl_status_kerjasama sk', 'sk.id_status_kerjasama = a.status_moa', 'LEFT')
			->join('simlapkerma_tbl_daftar_instansi in', 'in.id_daftar_instansi = a.id_daftar_instansi', 'LEFT')
			->join('simlapkerma_tbl_bidang_kerjasama ks', 'ks.id_bidang_kerjasama = a.id_bidang_kerjasama', 'LEFT')
			->join('simlapkerma_tbl_bentuk_kegiatan bk', 'bk.id_bentuk_kegiatan = k.id_bentuk_kegiatan');
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
		$this->db->from('simlapkerma_tbl_kegiatan k')
			->join('simlapkerma_tbl_moa a', 'a.id_moa = k.id_moa', 'LEFT')
			->join('simlapkerma_tbl_status_kerjasama sk', 'sk.id_status_kerjasama = a.status_moa')
			->join('simlapkerma_tbl_daftar_instansi in', 'in.id_daftar_instansi = a.id_daftar_instansi');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_kegiatan', $id);
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
		$this->db->where('id_kegiatan', $id);
		$this->db->delete($this->table);
	}
}
