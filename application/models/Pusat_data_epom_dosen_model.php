<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pusat_data_epom_dosen_model extends CI_Model
{

	public $table = 'siakad_dosen';
	public $table_join = 'ikatan_kerja_dosen';
	public $column = [1, 'nm_ptk'];
	public $order = ['nm_ptk' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->where('siakad_dosen.id_ikatan_kerja <>', 'X')->from($this->table)
			->join($this->table_join, 'siakad_dosen.id_ikatan_kerja = ikatan_kerja_dosen.id_ikatan_kerja');
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
		$this->db->where('id_ikatan_kerja <>', 'X')->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_dosen sd')->join('ikatan_kerja_dosen ikd', 'sd.id_ikatan_kerja = ikd.id_ikatan_kerja');
		$this->db->where('id_ptk', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_rekap_epom($id)
	{
		$this->db->from('siakad_rekap_epom re')
			->join('feeder_kelas_kuliah fk', 're.id_kls = fk.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->join('sms s', 's.id_sms = fk.id_sms');
		$this->db->where('id_ptk', $id)->order_by('re.id_smt', 'mk.nm_mk');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_avg_semester($id_ptk)
	{
		$this->db->select('id_smt, avg(nilai_epom) as nilai_epom')->from('siakad_rekap_epom re');
		$this->db->where('id_ptk', $id_ptk)->group_by('id_smt');
		$query = $this->db->get();

		return $query->result();
	}

	public function save($data)
	{
		$this->db->insert('siakad_rekap_epom_pusat_data', $data);
		return $this->db->insert_id();
	}
}
