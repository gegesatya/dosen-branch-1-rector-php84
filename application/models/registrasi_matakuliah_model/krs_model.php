<?php
defined('BASEPATH') or exit('No direct script access allowed');

class krs_model extends CI_Model
{

	public $table = 'feeder_nilai';
	public $column = ['kk.nm_kls', 'kk.nm_mk'];
	public $order = ['kk.nm_kls' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();

	}

	private function _get_datatables_query($id_reg_pd)
	{
		//$setting = $this->db->where('id_reg_pd', $id_reg_pd)->get('siakad_registration_pool')->row();
		$setting = $this->db->get('siakad_setting')->row();
		$mahasiswa_pt = $this->db->where('id_reg_pd', $id_reg_pd)->get('mahasiswa_pt')->row();

		$this->db->select('kk.id_kls, 
								  kk.nm_kls, kk.sks_mk, kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, 
								  d.nm_ptk, sv.id_reg_pd, kk.hari,kk.jam_mulai, kk.jam_selesai, 
								  sv.validation_status, sv.validation_date, sv.registration_date');
		$this->db->from('siakad_validasi_krs sv');
		$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sv.id_kls');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		$this->db->where('kk.id_sms', $mahasiswa_pt->id_sms);
		$this->db->where('kk.id_smt', $setting->tahun_ajaran_aktif);
		$this->db->where('sv.id_reg_pd', $id_reg_pd);

		$i = 0;

		foreach ($this->column as $item) // loop column 
		{
			if ($_POST['search']['value']) // if datatable send POST for search
			{

				if ($i === 0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND. 
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$column[$i] = $item; // set column array variable to order processing
			$i++;
		}

		if (isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables($id_reg_pd)
	{
		$this->_get_datatables_query($id_reg_pd);
		if ($_POST['length'] != -1)
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
		$id_reg_pd = $this->session->userdata('id_reg_pd');
		$setting = $this->db->get('siakad_setting')->row();

		$this->db->select('kk.id_kls, kk.nm_kls, kk.sks_mk, kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, d.nm_ptk, sv.id_reg_pd, kk.hari,kk.jam_mulai, kk.jam_selesai, sv.validation_status');
		$this->db->from('siakad_validasi_krs sv');
		$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sv.id_kls');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		$this->db->where('kk.id_sms', $this->session->userdata('prodi'));
		$this->db->where('kk.id_smt', $setting->tahun_ajaran_aktif);
		$this->db->where('sv.id_reg_pd', $id_reg_pd);

		return $this->db->count_all_results();
	}

	public function count_sks_pengajuan($id_reg_pd)
	{
		$setting = $this->db->get('siakad_setting')->row();

		$this->db->select('sum(kk.sks_mk) as sks_mk', FALSE);
		$this->db->from('siakad_validasi_krs sv');
		$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sv.id_kls');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		$this->db->where('kk.id_smt', $setting->tahun_ajaran_aktif);
		$this->db->where('sv.id_reg_pd', $id_reg_pd);

		return $this->db->get()->row()->sks_mk;
	}

	public function get_by_id()
	{
		$this->db->from($this->table);
		$this->db->where('id_kls', $id);
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

	public function close_by_id($id)
	{
		$this->db->where('uniq', $id)->update('konfirmasimarketing', ['is_closed' => 'Yes']);
		return $this->db->affected_rows();
	}

	public function get_sekolah()
	{
		return $this->db->order_by('nama_Sekolah')->get('tblsekolah')->result();
	}

	public function get_idregpd()
	{
		$user = $this->ion_auth->user()->row();
		$id_reg_pd = $this->db
			->where('nipd', $user->nim)
			->where('id_sms', $this->session->userdata('prodi'))
			->get('mahasiswa_pt')->row()->id_reg_pd;
		return $id_reg_pd;
	}
}
