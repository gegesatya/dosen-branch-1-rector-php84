<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo_model extends CI_Model {

	public $table = 'siakad_rencana_pertemuan';
	public $table_absen_master = 'siakad_absen_master';
	public $column = [1,'nipd','nm_pd'];
	public $order = ['nipd'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}

	private function _get_datatables_query($id_rencana_pertemuan = NULL)
	{
		
		$mahasiswa = $this->db
					 ->select('dt.id_reg_pd, m.nm_pd, mp.nipd, m.id_pd,ms.*, dt.uniq,right(mp.nipd,4) as urutan, dt.status_kehadiran')
					 ->from('siakad_absen_master ms')
					 ->join('siakad_absen_detail dt', 'ms.uniq = dt.id_absen_master')
					 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = dt.id_reg_pd')
					 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
					 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = ms.id_kls')
					 ->where(['ms.id_rencana_pertemuan'=>$id_rencana_pertemuan]);	

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

	public function get_datatables($id_rencana_pertemuan = NULL)
	{
		$this->_get_datatables_query($id_rencana_pertemuan);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_rencana_pertemuan = NULL)
	{
		$this->_get_datatables_query($id_rencana_pertemuan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_rencana_pertemuan = NULL)
	{
		$this->db
			 ->select('dt.id_reg_pd, m.nm_pd, mp.nipd, ms.*, dt.uniq,right(mp.nipd,4) as urutan, dt.status_kehadiran')
			 ->from('siakad_absen_master ms')
			 ->join('siakad_absen_detail dt', 'ms.uniq = dt.id_absen_master')
			 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = dt.id_reg_pd')
			 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = ms.id_kls')
			 ->where(['ms.id_rencana_pertemuan'=>$id_rencana_pertemuan]);	
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_berita_acara_by_id($id){
		$this->db->from($this->table_absen_master);
		$this->db->where('id_rencana_pertemuan',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save_backup($data)
	{
		$this->db->insert($this->table_absen_master, $data);
		return $this->db->insert_id();
	}

	public function save($data)
	{
		$id_rencana_pertemuan = $data['id_rencana_pertemuan'];
		$row_rencana_pertemuan = $this->db
									  ->where('uniq', $id_rencana_pertemuan)
									  ->get('siakad_rencana_pertemuan')
									  ->row();
		$id_kls = $row_rencana_pertemuan->id_kls;
		$mahasiswa_absen = $this->db->select('mp.id_reg_pd,m.nm_pd, mp.nipd, right(mp.nipd,4) as urutan')
								 ->from('feeder_nilai n')
								 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = n.id_reg_pd')
								 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
								 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
								 ->where(['n.id_kls'=>$id_kls])->get();
		
		$user = $this->ion_auth->user()->row()->id;

		$master_data = ['id_kls'=>$id_kls,
							'tanggal_rencana_perkuliahan'=>$this->return_format_date($row_rencana_pertemuan->tanggal_rencana),
							'id_rencana_pertemuan'=>$id_rencana_pertemuan,
							'minggu_ke'=>$row_rencana_pertemuan->pertemuan,
							'kategori_pertemuan'=>$row_rencana_pertemuan->kategori_pertemuan,
							'isi_berita_acara'=>$row_rencana_pertemuan->berita_acara_rencana,
							'create_by'=>$user,
							'create_date'=>date('Y-m-d H:i:s')];

		$this->db->trans_begin();
        
		$this->db->insert($this->table_absen_master, $data);
		$id_absen_master = $this->db->insert_id();

		$total = 0;
		$hadir = 0;
		$tidak_hadir = 0;

		$data_detail = [];
		foreach ($mahasiswa_absen->result() as $key => $value) {
			$data_detail[] = ['id_absen_master'=>$id_absen_master, 
						  'id_reg_pd'=>$value->id_reg_pd, 
						  'status_kehadiran'=>'Hadir',
						  'create_by'=>$user,
						  'create_date'=>date('Y-m-d H:i:s')];
		}

		$this->db->insert_batch('siakad_absen_detail', $data_detail);
        
        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
        	return FALSE;
        } else {
            $this->db->trans_commit();
	    }
	}

	public function return_format_date($strDate){
		if(!empty($strDate)){
			return date('Y-m-d', strtotime($strDate));
		}else{
			return null;
		}

	}

	public function add_pertemuan_first($id_rencana_pertemuan){
		$row_rencana_pertemuan = $this->db->where('uniq', $id_rencana_pertemuan)->get('siakad_rencana_pertemuan')->row();
		$id_kls = $row_rencana_pertemuan->id_kls;
		$mahasiswa_absen = $this->db->select('mp.id_reg_pd,m.nm_pd, mp.nipd, right(mp.nipd,4) as urutan')
								 ->from('feeder_nilai n')
								 ->join('mahasiswa_pt mp', 'mp.id_reg_pd = n.id_reg_pd')
								 ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
								 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
								 ->where(['n.id_kls'=>$id_kls])->get();

		$data = [];
		$user = $this->ion_auth->user()->row()->id;

		$master_data = ['id_kls'=>$id_kls,
							'tanggal_rencana_perkuliahan'=>$this->return_format_date($row_rencana_pertemuan->tanggal_rencana),
							'id_rencana_pertemuan'=>$id_rencana_pertemuan,
							'minggu_ke'=>$row_rencana_pertemuan->pertemuan,
							'kategori_pertemuan'=>$row_rencana_pertemuan->kategori_pertemuan,
							'isi_berita_acara'=>$row_rencana_pertemuan->berita_acara_rencana,
							'create_by'=>$user,
							'create_date'=>date('Y-m-d H:i:s')];

		$this->db->trans_begin();
        
        $this->db->insert('siakad_absen_master', $master_data);

		$k = $this->db->insert_id();

		$total = 0;
		$hadir = 0;
		$tidak_hadir = 0;

		foreach ($mahasiswa_absen->result() as $key => $value) {
			$data = ['id_absen_master'=>$k, 
						  'id_reg_pd'=>$value->id_reg_pd, 
						  'status_kehadiran'=>'Hadir',
						  'create_by'=>$user,
						  'create_date'=>date('Y-m-d H:i:s')];
			$this->db->insert('siakad_absen_detail', $data);
			$total++;
			//if($value=='Hadir'){
				$hadir++;
			//}
		}

		$tidak_hadir = $total-$hadir;

		$this->db->where('uniq', $k)
		->update('siakad_absen_master', ['jumlah_hadir'=>$hadir,'jumlah_tidak_hadir'=>$tidak_hadir]);

        
        
        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
        	return FALSE;
        } else {
            $this->db->trans_commit();
            //echo json_encode(array("status" => TRUE));
	    }
		
		
	}

	public function update($where, $data)
	{
		$this->db->update($this->table_absen_master, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

}
