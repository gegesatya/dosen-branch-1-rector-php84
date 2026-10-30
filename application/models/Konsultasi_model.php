<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konsultasi_model extends CI_Model {

	public $table = 'form_konsultasi k';
	public $column = ['tanggal_konsultasi', 'catatan_dosen', 'konsultasi_akademik']; 
	public $order = ['tanggal_konsultasi'=>'desc']; 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_reg_pd)
	{
		$this->db->select('m.*, k.*');
		$this->db->from('form_konsultasi k');
		$this->db->join('mahasiswa_pt mp', 'mp.id_reg_pd = k.id_reg_pd', 'LEFT');
		$this->db->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'LEFT');
		$this->db->where('k.id_reg_pd', $id_reg_pd);
		
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

	public function get_datatables($id_reg_pd)
	{
		$this->_get_datatables_query($id_reg_pd);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_reg_pd)
	{
		$this->_get_datatables_query($id_reg_pd);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_reg_pd)
	{
		$this->db->from($this->table);
		$this->db->where('id_reg_pd', $id_reg_pd);
		return $this->db->count_all_results();
	}

	public function get_by_id($id_reg_pd)
	{
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'LEFT');
		$this->db->where('mp.id_reg_pd',$id_reg_pd);
		$query = $this->db->get()->row_array();
		return $query;
	}


	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('mahasiswa', $data);
		return $this->db->insert_id();
	}


	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update('mahasiswa', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_pd', $id);
		$this->db->delete('mahasiswa');
	}

}
