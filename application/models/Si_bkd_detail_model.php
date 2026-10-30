<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Si_bkd_detail_model extends CI_Model {

	public $table = 'si_bkd_online_detail';
	public $column = ['jenis_kegiatan', 'a1_mata_kuliah', 'a1_bukti_penugasan', 'a1_bukti_dokumen']; 
	public $order = ['id'=>'asc']; 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_master, $kode_kinerja)
	{
		$field_kegiatan = '';
		if($kode_kinerja=='a1'){
			$field_kegiatan = 'a1_jenis_kegiatan';
		}if($kode_kinerja=='a2'){
			$field_kegiatan = 'a2_jenis_kegiatan';
		}if($kode_kinerja=='a3'){
			$field_kegiatan = 'a3_jenis_kegiatan';
		}if($kode_kinerja=='a4'){
			$field_kegiatan = 'a4_jenis_kegiatan';
		}

		$this->db->select('si_bkd_online_detail.*, si_bkd_online_jenis_kegiatan.jenis_kegiatan');
		$this->db->from('si_bkd_online_detail');
		$this->db->join('si_bkd_online_jenis_kegiatan', 'si_bkd_online_detail.'.$field_kegiatan.' = si_bkd_online_jenis_kegiatan.id');
		$this->db->where(['si_bkd_online_detail.id_master' => $id_master, 'si_bkd_online_detail.kode_kinerja' => $kode_kinerja]);
		
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

	public function get_datatables($id_master, $kode_kinerja)
	{
		$this->_get_datatables_query($id_master, $kode_kinerja);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_master, $kode_kinerja)
	{
		$this->_get_datatables_query($id_master, $kode_kinerja);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_master, $kode_kinerja)
	{
		$this->db->from($this->table);
		$this->db->where(['kode_kinerja' => $kode_kinerja, 'id_master' => $id_master]);
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
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
