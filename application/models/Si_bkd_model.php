<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Si_bkd_model extends CI_Model {

	public $table = 'si_bkd_online_master';
	public $column = ['id_smt', 'id_triwulan']; 
	public $order = ['id_smt'=>'asc', 'id_triwulan'=>'asc']; 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('bkd.*');
		$this->db->from('si_bkd_online_master bkd');
		$this->db->join('siakad_dosen sd', 'sd.id_ptk = bkd.id_ptk');
		$this->db->where('sd.id_ptk', $this->session->userdata('id_ptk'));
		
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
		$this->db->from($this->table);
		//$this->db->where('id_reg_pd', );
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('si_bkd_online_master');
		$this->db->where('id', $id);
		$query = $this->db->get()->row_array();
		return $query;
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
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

}
