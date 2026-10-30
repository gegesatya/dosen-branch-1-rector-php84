<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_kuliah_model extends CI_Model {

	public $table = 'feeder_kelas_kuliah';
	public $column = ['feeder_kelas_kuliah.nm_kls','feeder_kelas_kuliah.nm_mk']; 
	public $order = ['feeder_kelas_kuliah.nm_kls' => 'asc']; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}

	private function _get_datatables_query()
	{
		$user = $this->ion_auth->user()->row();
		$this->db->select('feeder_kelas_kuliah.id_kls, feeder_kelas_kuliah.nm_kls, feeder_kelas_kuliah.sks_mk, feeder_kelas_kuliah.sks_tm, feeder_kelas_kuliah.sks_prak, mk.nm_mk, mk.kode_mk, d.nm_ptk');
		$this->db->from($this->table);
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = feeder_kelas_kuliah.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = feeder_kelas_kuliah.id_ptk', 'LEFT');
		$this->db->where('feeder_kelas_kuliah.id_smt', $this->session->userdata('tahun_ajaran'));
		//$this->db->where('feeder_kelas_kuliah.id_sms', $this->session->userdata('prodi'));
		$this->db->where('feeder_kelas_kuliah.id_ptk', $user->id_ptk);

		$i = 0;
	
		foreach ($this->column as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{

				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
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
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_kls',$id);
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
		$this->db->where('id_kls', $id);
		$this->db->delete($this->table);
	}

	public function close_by_id($id){
		$this->db->where('uniq', $id)->update('konfirmasimarketing', ['is_closed'=>'Yes']);
		return $this->db->affected_rows();
	}

	public function get_sekolah(){
		return $this->db->order_by('nama_Sekolah')->get('tblsekolah')->result();
	}


}
