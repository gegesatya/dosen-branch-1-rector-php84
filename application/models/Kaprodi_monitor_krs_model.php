<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kaprodi_monitor_krs_model extends CI_Model {

	public $table = 'siakad_dosen';
	public $column = [1,'nidn','nm_ptk'];
	public $order = ['nm_ptk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}

	private function _get_datatables_query()
	{
		$this->db->where('id_sms', $this->session->userdata('prodi'));
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
		$this->db->where('id_sms', $this->session->userdata('prodi'));
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
}
