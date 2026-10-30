<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bentuk_kegiatan_model extends CI_Model
{

	public $table = 'simlapkerma_tbl_bentuk_kegiatan';
	public $column = ['nm_bentuk_kegiatan'];
	public $order = ['nm_bentuk_kegiatan' => 'asc'];
	public $pk = 'id_bentuk_kegiatan';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->from($this->table);
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
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
}
