<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class krk_model extends CI_Model {

	public $table = 'validasi_krk';
	public $column = ['nama_agenda']; 
	public $order = ['nama_agenda' => 'asc']; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		$this->simkrekema_db = $this->load->database('simkrekema_db', TRUE);
	}

	private function _get_datatables_query($id_reg_pd)
	{	
		
		$setting = $this->db->get('siakad_setting')->row();
		$mahasiswa_pt = $this->db->where('id_reg_pd', $id_reg_pd)->get('mahasiswa_pt')->row();

		$this->simkrekema_db->select('validasi_krk.id_validasi_krk,
									  validasi_krk.nipd,
									  validasi_krk.tanggal_pengajuan,
									  validasi_krk.tanggal_disetujui_wali,
									  validasi_krk.status_persetujuan,
									  agenda_kegiatan.kode_agenda,
									  agenda_kegiatan.nama_agenda, 
									  agenda_kegiatan.kategori, 
									  agenda_kegiatan.keterangan,
									  agenda_kegiatan.tanggal_mulai,
									  agenda_kegiatan.tanggal_selesai,
									  bidang_kegiatan.nm_bidang_kegiatan, 
									  tingkat.nm_tingkat, 
									  agenda_kegiatan.angka_skor')
			 ->from('validasi_krk')
			 ->join('agenda_kegiatan', 'agenda_kegiatan.id_agenda_kegiatan = validasi_krk.id_agenda_kegiatan')
			 ->join('bidang_kegiatan', 'bidang_kegiatan.id_bidang_kegiatan =  agenda_kegiatan.id_bidang_kegiatan')
			 ->join('tingkat', 'tingkat.id_tingkat = agenda_kegiatan.id_tingkat')
			 ->where(['validasi_krk.nipd' => $mahasiswa_pt->nipd, 'agenda_kegiatan.kd_tahun_akademik'=>$setting->tahun_ajaran_aktif]);

		$i = 0;
	
		foreach ($this->column as $item)
		{
			if($_POST['search']['value'])
			{
				
				if($i===0)
				{
					$this->simkrekema_db->group_start();
					$this->simkrekema_db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->simkrekema_db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column) - 1 == $i)
					$this->simkrekema_db->group_end();
			}
			$column[$i] = $item;
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->simkrekema_db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->simkrekema_db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables($id_reg_pd)
	{
		$this->_get_datatables_query($id_reg_pd);
		if($_POST['length'] != -1)
		$this->simkrekema_db->limit($_POST['length'], $_POST['start']);
		$query = $this->simkrekema_db->get();
		return $query->result();
	}

	public function count_filtered($id_reg_pd)
	{
		$this->_get_datatables_query($id_reg_pd);
		$query = $this->simkrekema_db->get();
		return $query->num_rows();
	}

	public function count_all($id_reg_pd)
	{
		$setting = $this->db->get('siakad_setting')->row();
		$mahasiswa_pt = $this->db->where('id_reg_pd', $id_reg_pd)->get('mahasiswa_pt')->row();

		$this->simkrekema_db->select('agenda_kegiatan.nama_agenda, agenda_kegiatan.kategori, bidang_kegiatan.nm_bidang_kegiatan, tingkat.nm_tingkat')->from('validasi_krk')
			 ->join('agenda_kegiatan', 'agenda_kegiatan.id_agenda_kegiatan = validasi_krk.id_agenda_kegiatan')
			 ->join('bidang_kegiatan', 'bidang_kegiatan.id_bidang_kegiatan =  agenda_kegiatan.id_bidang_kegiatan')
			 ->join('tingkat', 'tingkat.id_tingkat = agenda_kegiatan.id_tingkat')
			 ->where(['validasi_krk.nipd' => $mahasiswa_pt->nipd, 'agenda_kegiatan.kd_tahun_akademik'=>$setting->tahun_ajaran_aktif]);

		return $this->simkrekema_db->count_all_results();
	}

	

	public function get_by_id()
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

	public function get_idregpd(){
		$user = $this->ion_auth->user()->row();
		$id_reg_pd = $this->db
					->where('nipd', $user->nim)
					->where('id_sms', $this->session->userdata('prodi'))
					->get('mahasiswa_pt')->row()->id_reg_pd;
		return $id_reg_pd;
	}


}
