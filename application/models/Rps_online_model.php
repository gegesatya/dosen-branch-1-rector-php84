<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_online_model extends CI_Model
{

	public $table = 'siakad_rps_online';
	public $column = ['general_mk'];
	public $order = ['general_mk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->where(['id_ptk' => $this->session->userdata('id_ptk')])
			->select('*')->from('siakad_rps_online r')
			->join('siakad_rps_koordinator k', 'k.koordinator_rps = r.koordinator_rps', 'LEFT');
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
		$this->db->where('id_ptk', $this->session->userdata('id_ptk'))
			->select('*')->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_rps', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$data = clean_wysiwyg_data($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = clean_wysiwyg_data($data);
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_rps', $id);
		$this->db->delete($this->table);
		$this->db->where('id_rps', $id);
		$this->db->delete('siakad_rps_mk_prodi');
		$this->db->where('id_rps', $id);
		$this->db->delete('siakad_rps_online_detail');
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


	public function f_dropdown2($nama_tabel, $id, $nm, $orderby = NULL, $where = NULL)
	{
		if (!empty($orderby)) {
			$this->db->order_by($orderby, 'asc');
		} else {
			$this->db->order_by($nm, 'asc');
		}
		if (!empty($where)) {
			$result = $this->db->where($where)->get($nama_tabel);
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

	public function get_excel_data($id_rps)
	{
		return $this->db->where('id_rps', $id_rps)->from('siakad_rps_online_detail')->get()->result();
	}
}
