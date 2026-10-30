<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nilai_prodi_model extends CI_Model
{

	public $table = 'feeder_nilai';
	public $column = ['m.nm_pd', 'mp.nipd', 'mp.id_reg_pd'];
	public $order = ['mp.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();

	}

	private function _get_datatables_query()
	{
		//$id_reg_pd = $this->session->userdata('id_reg_pd');
		//$setting = $this->db->get('siakad_setting')->row();
		//$this->db->select('kk.id_kls, kk.nm_kls, kk.sks_mk, kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, d.nm_ptk, sv.id_reg_pd, kk.hari,kk.jam_mulai, kk.jam_selesai, sv.validation_status');
		//$this->db->from('siakad_validasi_krs sv');
		//$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sv.id_kls');
		//$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		//$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		//$this->db->where('kk.id_sms', $this->session->userdata('prodi'));
		//$this->db->where('kk.id_smt', $setting->tahun_ajaran_future);
		//$this->db->where('sv.id_reg_pd', $id_reg_pd);

		$this->db->select('m.nm_pd, mp.nipd, mp.id_reg_pd, mp.tgl_masuk_sp');
		$this->db->from('siakad_mahasiswa_additional sma');
		$this->db->join('mahasiswa m', 'sma.id_pd = m.id_pd');
		$this->db->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd');
		$this->db->where('id_sms', $this->session->userdata('prodi'));

		$i = 0;

		foreach ($this->column as $item) {
			if ($_POST['search']['value']) {

				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column) - 1 == $i)
					$this->db->group_end();
			}
			$column[$i] = $item;
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
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
		//$id_reg_pd = $this->get_idregpd();
		$id_reg_pd = $this->session->userdata('id_reg_pd');
		$setting = $this->db->get('siakad_setting')->row();

		/*$this->db->select('kk.id_kls, kk.nm_kls, kk.sks_mk, kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, d.nm_ptk, sn.id_reg_pd');
		$this->db->from('feeder_nilai sn');
		$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		$this->db->where('kk.id_sms', $this->session->userdata('prodi'));
		$this->db->where('kk.id_smt', $setting->tahun_ajaran_future);
		$this->db->where('sn.id_reg_pd', $id_reg_pd);*/

		$this->db->select('kk.id_kls, kk.nm_kls, kk.sks_mk, kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, d.nm_ptk, sv.id_reg_pd, kk.hari,kk.jam_mulai, kk.jam_selesai, sv.validation_status');
		$this->db->from('siakad_validasi_krs sv');
		$this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = sv.id_kls');
		$this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk');
		$this->db->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT');
		$this->db->where('kk.id_sms', $this->session->userdata('prodi'));
		$this->db->where('kk.id_smt', $setting->tahun_ajaran_future);
		$this->db->where('sv.id_reg_pd', $id_reg_pd);

		return $this->db->count_all_results();
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
