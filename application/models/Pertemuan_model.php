<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertemuan_model extends CI_Model {

	public $table = 'feeder_nilai';
	public $column = ['mp.nipd','m.nm_pd']; 
	public $order = ['urutan' => 'asc'];
	//->order_by('right(nipd,4)')
    //->order_by('left(nipd,4)')

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$mahasiswa = $this->db
					 ->select('dt.id_reg_pd, m.nm_pd, mp.nipd, ms.*, dt.uniq,right(mp.nipd,4) as urutan, dt.status_kehadiran')
					 ->from('siakad_absen_master ms')
					 ->join('siakad_absen_detail dt', 'ms.uniq = dt.id_absen_master')
					 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = dt.id_reg_pd')
					 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
					 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = ms.id_kls')
					 ->where(['ms.id_rencana_pertemuan'=>$this->session->userdata('id_rencana_pertemuan')]);	

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
		
		$mahasiswa = $this->db
					 ->select('dt.id_reg_pd, m.nm_pd, mp.nipd, ms.*, dt.uniq,right(mp.nipd,4) as urutan, dt.status_kehadiran')
					 ->from('siakad_absen_master ms')
					 ->join('siakad_absen_detail dt', 'ms.uniq = dt.id_absen_master')
					 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = dt.id_reg_pd')
					 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
					 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = ms.id_kls')
					 ->where(['ms.id_rencana_pertemuan'=>$this->session->userdata('id_rencana_pertemuan')]);	
		return $mahasiswa->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$query = $this->db->get();

		return $query->row();
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
		$this->db->where('id_mk', $id);
		$this->db->delete($this->table);
	}


}
