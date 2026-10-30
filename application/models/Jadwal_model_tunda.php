<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal_model_tunda extends CI_Model
{

	public $table = 'siakad_jadwal';
	public $column = ['tag_name', 'nama_hari'];
	public $order = ['h.uniq' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("setting")->id_smt_tunda;

		$this->db->from('siakad_jadwal j')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang')
			->where(['j.id_sdm' => $id_ptk, 'j.id_smt' => $id_smt, 'id_kategori_jadwal' => 1]);
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
		$id_smt = $this->session->userdata("setting")->id_smt_tunda;

		$this->db->from('siakad_jadwal j')
			->where(['j.id_sdm' => $id_ptk, 'j.id_smt' => $id_smt]);

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
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

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}
}
