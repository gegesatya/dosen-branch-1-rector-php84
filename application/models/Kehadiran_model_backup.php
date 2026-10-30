<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_model extends CI_Model {

	public $table = 'siakad_absen_detail_baru';
	public $column = [1,'nm_pd', 'nipd'];
	public $order = ['nm_pd' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
	}

	private function _get_datatables_query($id_jadwal = NULL)
	{
		
		$this->db->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd = a.id_reg_pd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		if($id_jadwal <> NULL){
			$this->db->where('id_jadwal', $id_jadwal);
		}else{
			$this->db->where('id_jadwal', '');
		}
		
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

	public function get_table_data($id_jadwal = NULL){
		$this->db
			->select('mp.nipd, m.nm_pd, a.*')
			->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		if($id_jadwal <> NULL){
			$this->db->where('id_jadwal', $id_jadwal);
		}else{
			$this->db->where('id_jadwal', '');
		}

		return $this->db->order_by('nm_pd')->get()->result_array();
	}

	public function get_datatables($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_jadwal = NULL)
	{
		$this->db->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.id_reg_pd = a.id_reg_pd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		if($id_jadwal <> NULL){
			$this->db->where('id_jadwal', $id_jadwal);
		}else{
			$this->db->where('id_jadwal', '');
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq',$id);
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

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal'=> $id_jadwal, 'is_realisasi'=>1])
								->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->result_array();

		foreach ($absen_detail as $key => $value) {
			$uniq = $value['uniq'];
			$jumlah_hadir=0;
			for($i=1;$i<=30;$i++){
				$jumlah_hadir += $value['meet_'.$i] == 'Hadir' ? 1 : 0;
			}

			if($rencana_pertemuan->num_rows() == 0){
				$persen_kehadiran = 0;
			}else{
				$persen_kehadiran = round($jumlah_hadir/$rencana_pertemuan->num_rows()*100, 2);
			}
			
			$this->db->update($this->table, ['persen_hadir'=>$persen_kehadiran], ['uniq'=>$uniq]);
		}
		

		return $this->db->affected_rows();
	}

	public function update_single($where, $data)
	{
		$this->db->update($this->table, $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal'=> $id_jadwal, 'is_realisasi'=>1])
								->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->row_array();
		$jumlah_hadir=0;
		for($i=1;$i<=30;$i++){
			$jumlah_hadir += $absen_detail['meet_'.$i] == 'Hadir' ? 1 : 0;
		}

		if($rencana_pertemuan->num_rows() == 0){
			$persen_kehadiran = 0;
		}else{
			$persen_kehadiran = round($jumlah_hadir/$rencana_pertemuan->num_rows()*100, 2);
		}
		
		$this->db->update($this->table, ['persen_hadir'=>$persen_kehadiran], $where);

		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function f_dropdown()
    {
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->where(['id_sdm'=>$id_ptk, 'id_smt'=>$id_smt]);
        $this->db->order_by('tag_name', 'asc');
		$result = $this->db->from('siakad_jadwal')->get();
      
        $dd[''] = 'Please Select';
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $dd[$row['id']] = $row['tag_name'];
            }
        }
        return $dd;
	}

	/* function f_dropdown($nama_tabel, $id, $nm)
    {
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->where(['id_ptk'=>$id_ptk, 'id_smt'=>$id_smt]);
        $this->db->order_by('nm_mk', 'asc');
        $result = $this->db->from('feeder_kelas_kuliah sk')->join('feeder_mata_kuliah mk', 'sk.id_mk = mk.id_mk')->get();
      
        $dd[''] = 'Please Select';
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $dd[$row[$id]] = '['.$row[$nm].'] '. $row['nm_mk'];
            }
        }
        return $dd;
	} */

	public function f_status_kehadiran()
    {
		$dd[''] = 'Please Select';
		$dd['Hadir'] = 'Hadir';
		$dd['Tidak Hadir'] = 'Tidak Hadir';
        return $dd;
	}

	public function f_pertemuan_1()
    {
		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal'=> $id_jadwal, 'is_realisasi'=>1])->get('siakad_rencana_pertemuan')->num_rows();
		
		$dd[''] = 'Please Select';

		for($i=1;$i<=$rencana_pertemuan;$i++){
			$dd['meet_'.$i] = 'Pertemuan '.$i;
		}
		/* $dd['meet_1'] = 'Pertemuan 1';
		$dd['meet_2'] = 'Pertemuan 2';
		$dd['meet_3'] = 'Pertemuan 3';
		$dd['meet_4'] = 'Pertemuan 4';
		$dd['meet_5'] = 'Pertemuan 5';
		$dd['meet_6'] = 'Pertemuan 6';
		$dd['meet_7'] = 'Pertemuan 7';
		$dd['meet_8'] = 'Pertemuan 8';
		$dd['meet_9'] = 'Pertemuan 9';
		$dd['meet_10'] = 'Pertemuan 10';
		$dd['meet_11'] = 'Pertemuan 11';
		$dd['meet_12'] = 'Pertemuan 12';
		$dd['meet_13'] = 'Pertemuan 13';
		$dd['meet_14'] = 'Pertemuan 14';
		$dd['meet_15'] = 'Pertemuan 15'; */
		
        return $dd;
	}

	public function f_pertemuan_2()
    {
		$dd[''] = 'Please Select';
		$dd['meet_16'] = 'Pertemuan 16';
		$dd['meet_17'] = 'Pertemuan 17';
		$dd['meet_18'] = 'Pertemuan 18';
		$dd['meet_19'] = 'Pertemuan 19';
		$dd['meet_20'] = 'Pertemuan 20';
		$dd['meet_21'] = 'Pertemuan 21';
		$dd['meet_22'] = 'Pertemuan 22';
		$dd['meet_23'] = 'Pertemuan 23';
		$dd['meet_24'] = 'Pertemuan 24';
		$dd['meet_25'] = 'Pertemuan 25';
		$dd['meet_26'] = 'Pertemuan 26';
		$dd['meet_27'] = 'Pertemuan 27';
		$dd['meet_28'] = 'Pertemuan 28';
		$dd['meet_29'] = 'Pertemuan 29';
		$dd['meet_30'] = 'Pertemuan 30';
		
        return $dd;
	}
	
	public function f_dropdown_backup($nama_tabel, $id, $nm)
    {
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->where(['id_ptk'=>$id_ptk, 'id_smt'=>$id_smt]);
        $this->db->order_by($nm, 'asc');
        $result = $this->db->get($nama_tabel);
      
        $dd[''] = 'Please Select';
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $dd[$row[$id]] = $row[$nm];
            }
        }
        return $dd;
    }


}
