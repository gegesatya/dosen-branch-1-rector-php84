<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kaprodi_data_nilai_model extends CI_Model
{

	public $table = 'mahasiswa m';
	public $column = ['m.nm_pd', 'mp.nipd', 'sd.nm_ptk'];
	public $order = ['mp.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->join('siakad_dosen sd', 'sd.id_ptk = m.dosen_pa')
			->where(['mp.id_sms' => $this->session->userdata('prodi')]);

		$i = 0;

		foreach ($this->column as $item) {
			if ($_POST['search']['value']) {

				if ($i === 0) // first loop
				{
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
		$this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->where(['mp.id_sms' => $this->session->userdata('prodi')]);

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'LEFT');
		$this->db->where('m.id_pd', $id);
		$query = $this->db->get()->row();
		return $query;
	}
}
