<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kaprodi_monitor_kontrak_kuliah_model extends CI_Model {

	public $table = 'feeder_kelas_kuliah';
	public $column = [1,'nm_ptk', 'nm_mk'];
	public $order = ['nm_ptk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}

	private function _get_datatables_query()
	{
		$where = ['feeder_kelas_kuliah.id_sms'=>$this->session->userdata('prodi'), 'feeder_kelas_kuliah.id_smt'=>$this->session->userdata('tahun_ajaran')];
		$this->db->where($where);
		$this->db->select('feeder_kelas_kuliah.*, d.nm_ptk, mk.nm_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = feeder_kelas_kuliah.id_ptk');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = feeder_kelas_kuliah.id_mk');
		$this->db->from($this->table);

		$i = 0;
	
		foreach ($this->column as $item)
		{
			if($_POST['search']['value'])
			{
				
				if($i===0)
				{
					$this->db->group_start(); 
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i)
					$this->db->group_end();
			}
			$column[$i] = $item;
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
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
		//$this->db->where('id_sms', $this->session->userdata('prodi'));
		//$this->db->from($this->table);

		$where = ['feeder_kelas_kuliah.id_sms'=>$this->session->userdata('prodi'), 'feeder_kelas_kuliah.id_smt'=>$this->session->userdata('tahun_ajaran')];
		$this->db->where($where);
		$this->db->select('feeder_kelas_kuliah.*, d.nm_ptk, mk.nm_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = feeder_kelas_kuliah.id_ptk');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = feeder_kelas_kuliah.id_mk');
		$this->db->from($this->table);

		return $this->db->count_all_results();
	}
}
