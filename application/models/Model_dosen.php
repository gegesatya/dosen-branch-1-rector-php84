<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_dosen extends CI_Model {

	public $table = 'siakad_dosen d';
	public $column = ['m.nm_ptk','mp.id_ptk']; //set column field database for order and search
	public $order = ['mp.nm_ptk' => 'asc']; // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('m.*, mp.nipd, mp.id_reg_pd, mp.tgl_masuk_sp,d.nm_ptk');
		$this->db->from($this->table);
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'LEFT');
		$this->db->join('siakad_mahasiswa_additional s', 's.id_pd = m.id_pd', 'LEFT');
		$this->db->join('siakad_dosen d', 's.dosen_pa = d.id_ptk', 'LEFT');
		$this->db->where('mp.id_sms', $this->session->userdata('prodi'));
		
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
		$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd');
		$this->db->join('siakad_mahasiswa_additional s', 's.id_pd = m.id_pd', 'LEFT');
		$this->db->join('siakad_dosen d', 's.dosen_pa = d.id_ptk', 'LEFT');
		$this->db->where('mp.id_sms', $this->session->userdata('prodi'));
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_dosen ');
		//$this->db->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd', 'LEFT');
		//$this->db->join('siakad_mahasiswa_additional s', 's.id_pd = m.id_pd', 'LEFT');
		$this->db->where('id_ptk',$id);
		$query = $this->db->get()->row_array();
		//$kecamatan = $this->kecamatan_by_id($query['id_wil']);
		//$all_set = array_merge($kecamatan, $query);

		return $query;
	}

	public function kecamatan_by_id($id){
		$searchTerm = $id;

		$k = $this->db->select('id_wil, nm_wil, id_induk_wilayah')
			->where('id_level_wil',3)
			->where('id_wil', $searchTerm)
			->get('wilayah');

		if($k->num_rows() > 0){
			$induk_wilayah = $this->db->where('id_wil', $k->row()->id_induk_wilayah)->get('wilayah')->row()->nm_wil;
			return ['kecamatan'=>$k->row()->id_wil .' - '. trim($k->row()->nm_wil) . ' - ' . trim( $induk_wilayah )];			
		}else{
			return ['kecamatan'=> ''];	
		}	
		
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('mahasiswa', $data);
		return $this->db->insert_id();
	}

	public function save2($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('mahasiswa_pt', $data);
		return $this->db->insert_id();
	}

	public function save3($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('siakad_mahasiswa_additional', $data);
		return $this->db->insert_id();
	}

	public function update($where, $data, $data2=null, $data3=null)
	{	
		$data = $this->security->xss_clean($data);
		$this->db->trans_begin();


		$this->db->update('siakad_dosen', $data, $where);

		/*if(!empty($data2)){
			$this->db->update('mahasiswa_pt', $data2, $where);
		}*/

        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
        	return FALSE;
        } else {
            $this->db->trans_commit();
            //echo json_encode(array("status" => TRUE));
			return $this->db->affected_rows();
	    }


		//return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_pd', $id);
		$this->db->delete('mahasiswa');
	}

	public function get_detail_mahasiswa($id_pd){
      return $this->db->where('id_pd', $id_pd)->get('mahasiswa')->row();
    }

    public function get_mahasiswa_pt($id_pd){
       $m_pt = $this->db->where('id_pd', $id_pd)->get('mahasiswa_pt');
       return $m_pt;
    }

    /*function get_pekerjaan($id_pekerjaan){
        $i_record = $this->db->where('id_pekerjaan', $id_pekerjaan)->get('pekerjaan');

        if($i_record->num_rows()==0){
            return '-';
        }else{
            return $i_record->row()->nm_pekerjaan;
        }
    }*/

    /*function get_jenjang_pendidikan($id_jenj_pendidikan){
        $i_record = $this->db->where('id_jenj_didik', $id_jenj_pendidikan)->get('jenjang_pendidikan');

        if($i_record->num_rows()==0){
            return '-';
        }else{
            return $i_record->row()->nm_jenj_didik;
        }
    }*/

    /*function get_penghasilan($id_penghasilan){
        $i_record = $this->db->where('id_penghasilan', $id_penghasilan)->get('penghasilan');

        if($i_record->num_rows()==0){
            return '-';
        }else{
            return $i_record->row()->nm_penghasilan;
        }
    }*/

     public function get_dosen_pa($id_pd){
      $additional_data = $this->db->where('id_pd', $id_pd)->get('siakad_mahasiswa_additional');

      if($additional_data->num_rows() >0 ){
        $dosen_pa = !empty($additional_data->row()->dosen_pa)?$additional_data->row()->dosen_pa:'';
        if($dosen_pa == ''){
          return '';
        }else{
          $nm_ptk = $this->db->where('id_ptk', $dosen_pa)->get('siakad_dosen')->row()->nm_ptk;
          return $nm_ptk;
        }
       
      }
    }

    public function get_foto($id_pd){
      $additional_data = $this->db->where('id_pd', $id_pd)->get('siakad_mahasiswa_additional');

      if($additional_data->num_rows() >0 ){
        $foto = !empty($additional_data->row()->foto)?$additional_data->row()->foto:'';
        
        return $foto;
      }
    }

    public function nilai($id_pd, $sem = NULL){
      $id_reg_pd = $this->get_mahasiswa_pt($id_pd);

      if($id_reg_pd->num_rows() > 0){
        $nilai = $this->db->select('n.*, mk.nm_mk, mk.id_mk, mk.kode_mk, kk.sks_mk,kk.id_smt, s.nm_smt, kk.nm_kls')
                 ->from('feeder_nilai n')
                 ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
                 ->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
                 ->join('semester s', 's.id_smt = kk.id_smt')
                 ->where('id_reg_pd', $id_reg_pd->row()->id_reg_pd)
                 ->where('kk.id_smt', $sem)
                 //->where('kk.id_sms', $this->session->userdata('prodi'))
                 ->order_by('nm_mk', 'ASC')
                 ->get()->result();  
         return $nilai; 
      }
    }

    public function nilai_transfer($id_pd){
      $id_reg_pd = $this->get_mahasiswa_pt($id_pd);
      if($id_reg_pd->num_rows() > 0){
        $nilai_transfer = $this->db->select('nt.*,mk.nm_mk')
                          ->from('nilai_transfer nt')
                          ->join('feeder_mata_kuliah mk', 'mk.id_mk = nt.id_mk')
                          ->where('id_reg_pd', $id_reg_pd->row()->id_reg_pd)
                          ->get()->result();
        return $nilai_transfer;
      }
    }



    public function hitung_totalsks($id_pd){
       $id_reg_pd = $this->get_mahasiswa_pt($id_pd)->row()->id_reg_pd;

       $sks_nilai_transfer = $this->db->where('id_reg_pd', $id_reg_pd)->select_sum('sks_diakui')->get('nilai_transfer')->row()->sks_diakui;
       $sks_nilai = $this->db->select('sum(kk.sks_mk) as sks_mk')
                                    ->from('feeder_nilai n')
                                    ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
                                    ->where('id_reg_pd', $id_reg_pd)
                                    ->where('n.nilai_huruf !=', '')
                                    ->get()->row()->sks_mk;

       $total = $sks_nilai + $sks_nilai_transfer;

       return $total;
    }

    public function hitung_total_nilai($id_pd){
       $id_reg_pd = $this->get_mahasiswa_pt($id_pd)->row()->id_reg_pd;

       $total_nilai_transfer = $this->db->where('id_reg_pd', $id_reg_pd)->select('sum(sks_diakui*nilai_angka_diakui) as hasil')->from('nilai_transfer')->get()->row()->hasil;
       $total_nilai = $this->db->select('sum(kk.sks_mk*n.nilai_indeks) as hasil')
                                    ->from('feeder_nilai n')
                                    ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
                                    ->where('id_reg_pd', $id_reg_pd)
                                    ->where('n.nilai_huruf !=', '')
                                    ->get()->row()->hasil;

       $total = $total_nilai + $total_nilai_transfer;

       return $total;
    }

    public function kuliah_mahasiswa($id_pd){
      $id_reg_pd = $this->get_mahasiswa_pt($id_pd);
      if($id_reg_pd->num_rows() > 0){
        $kuliah_mahasiswa = $this->db->select('km.*, s.nm_smt')
                          ->from('kuliah_mahasiswa km')
                          ->join('semester s', 'km.id_smt = s.id_smt')
                          ->where('km.id_reg_pd', $id_reg_pd->row()->id_reg_pd)
                          ->get()->result();
        return $kuliah_mahasiswa;
      }
    }

    public function get_tabel($tabel_data, $id_field, $nm_field){
    	$query = $this->db->select($id_field.','. $nm_field)->from($tabel_data)->get()->result();
    	return $query;
    }

    public function get_negara(){
    	return $this->db->get('negara')->result();
    }

    public function get_agama(){
		$this->db->select('id_agama, nm_agama');
		$query = $this ->portal_db-> get('agama')->result();
		return $query;		 
	}

	public function get_jenis_tinggal(){
		$this->db->select('id_jns_tinggal, nm_jns_tinggal');
		$query = $this ->portal_db-> get('jenis_tinggal')->result();
		return $query;
	}
 
 	public function get_jenjang_pendidikan(){
		$this->db->select('id_jenj_didik, nm_jenj_didik');
		$query = $this ->portal_db-> get('jenjang_pendidikan')->result();
		return $query;
	}

	public function get_pekerjaan(){
		$this->db->select('id_pekerjaan, nm_pekerjaan');
		$query = $this ->portal_db-> get('pekerjaan')->result();
		return $query;
	}

	public function get_penghasilan(){
		$this->db->select('id_penghasilan, nm_penghasilan');
		$query = $this ->portal_db-> get('penghasilan')->result();
		return $query;
	}

	public function get_jenis_pendaftaran(){
		$this->db->select('id_jns_daftar, nm_jns_daftar')->where('u_daftar_sekolah', 1);
		$query = $this ->portal_db-> get('jenis_pendaftaran')->result();
		return $query;
	}

	public function get_sms(){
		$this->db->where('id_sms', $this->session->userdata('prodi'));
		$this->db->select('id_sms, nm_lemb');
		$query = $this ->portal_db-> get('sms')->result();
		return $query;
	}

	public function get_tahun_ajaran(){
		$this->db->select('id_thn_ajaran, nm_thn_ajaran');
		$query = $this ->portal_db-> get('tahun_ajaran')->result();
		return $query;
	}

	public function get_propinsi(){
		$this->db->select('id_wil, nm_wil');
		$query = $this ->portal_db -> where('id_level_wil', 1)->order_by('nm_wil', 'ASC')-> get('wilayah')->result();
		return $query;
	}

	public function get_alat_transport(){
		$this->db->select('id_alat_transport, nm_alat_transport');
		$query = $this ->portal_db -> get('alat_transport')->result();
		return $query;
	}

	public function get_semester(){
		$this->db->select('id_smt, nm_smt');
		$query = $this ->portal_db ->where('a_periode_aktif', 1)-> get('semester')->result();
		return $query;
	}

	public function get_status_mahasiswa(){
		$this->db->select('id_stat_mhs, nm_stat_mhs');
		$query = $this ->portal_db -> get('status_mahasiswa')->result();
		return $query;
	}

	public function get_dosen(){
		$this->db->select('id_ptk, nm_ptk');
		$query = $this ->portal_db->order_by('nm_ptk') -> get('siakad_dosen')->result();
		return $query;
	}

}
