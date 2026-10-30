<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Arsip_bap_model extends CI_Model
{

	public $table = 'arsip_absen_master';
	public $column = [1, 'nm_mk', 'id_smt'];
	public $order = ['id_smt' => 'desc'];
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();

	}

	private function _get_datatables_query()
	{

		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->from('arsip_kelas_kuliah kk')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where('kk.id_ptk', $id_ptk);

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
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->from('feeder_nilai sn')
			->join('arsip_kelas_kuliah kk', 'sn.id_kls = kk.id_kls')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where('kk.id_ptk', $id_ptk)->get();

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function f_dropdown($nama_tabel, $id, $nm, $orderby = NULL, $where = NULL)
	{
		if (!empty($orderby)) {
			$this->db->order_by($orderby, 'asc');
		} else {
			$this->db->order_by($nm, 'asc');
		}
		if (!empty($where)) {
			$result = $this->db->where('id_sdm', $where)->get($nama_tabel);
		} else {
			$result = $this->db->get($nama_tabel);
		}

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}

	/* public function f_dropdown_jadwal($id_ptk){

	} */
}
