<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konfirmasi_magang_model extends CI_Model
{

	public $table = 'simlapkerma_pendaftaran_magang';
	public $column = ['mp.nipd, mp.nm_pd', 'mdi.nm_instansi', 'pm.job_title', 'pm.tanggal_awal_pendaftaran', 'pm.tanggal_akhir_pendaftaran'];
	public $order = ['pm.create_date' => 'desc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($hasil = 'NIL')
	{
		// $id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');


		$this->db->select('mp.id_sms, mg.*, pm.id_daftar_instansi, pm.job_title, pm.ipk_minimal, m.nm_pd, mdi.nm_instansi, pm.tanggal_awal_pendaftaran, pm.tanggal_akhir_pendaftaran, approve_by')->from('simlapkerma_pendaftaran_magang mg')
			->join('simlapkerma_pengumuman_magang pm', 'pm.id_pengumuman_magang = mg.id_pengumuman_magang')
			->join('simlapkerma_tbl_daftar_instansi mdi', 'mdi.id_daftar_instansi = pm.id_daftar_instansi')
			->join('mahasiswa_pt mp', 'mp.nipd = mg.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		// $this->db->where_in('e_office.id_status', array(3, 4));
		// $this->db->where_in('e_office.id_kategori_pengantar', array(2, 3));
		$this->db->where(['mp.id_sms' => $id_sms]);

		if ($hasil <> 'NIL') {
			$this->db->where('mg.hasil', $hasil);
		}

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

	public function get_datatables($hasil = 'NIL')
	{
		$this->_get_datatables_query($hasil);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($hasil = 'NIL')
	{
		$this->_get_datatables_query($hasil);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($hasil = 'NIL')
	{
		$id_sms = $this->session->userdata('prodi');


		$this->db->from('simlapkerma_pendaftaran_magang mg')
			->join('simlapkerma_pengumuman_magang pm', 'pm.id_pengumuman_magang = mg.id_pengumuman_magang')
			->join('mahasiswa_pt mp', 'mp.nipd = mg.nipd')
			->join('mahasiswa m', 'mp.id_pd = m.id_pd');
		$this->db->where(['mp.id_sms' => $id_sms]);

		if ($hasil <> 'NIL') {
			$this->db->where('mg.hasil', $hasil);
		}
		return $this->db->count_all_results();
	}

	public function get_by_id($id_daftar_magang)
	{
		// $this->db->select('e_office.*, 
		// 					sms.nm_lemb,
		// 					mahasiswa.nm_pd, 
		// 					mahasiswa.jln, 
		// 					mahasiswa.telepon_seluler, 
		// 					mahasiswa.tmpt_lahir, 
		// 					mahasiswa.tgl_lahir,
		// 					mahasiswa_pt.id_reg_pd_feeder,
		// 					nm_kategori_pengantar');
		// $this->db->from('e_office')
		// 	->join('mahasiswa_pt', 'mahasiswa_pt.nipd = e_office.nipd')
		// 	->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
		// 	->join('sms', 'sms.id_sms = mahasiswa_pt.id_sms')
		// 	->join('e_office_kategori_pengantar', 'e_office_kategori_pengantar.id_kategori_pengantar = e_office.id_kategori_pengantar');
		$this->db->from('simlapkerma_pendaftaran_magang mg')
			->join('simlapkerma_cv cv', 'cv.nipd = mg.nipd')
			->join('simlapkerma_pengumuman_magang pm', 'pm.id_pengumuman_magang = mg.id_pengumuman_magang')
			->join('simlapkerma_tbl_daftar_instansi mdi', 'mdi.id_daftar_instansi = pm.id_daftar_instansi');

		$this->db->where('id_daftar_magang', $id_daftar_magang);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_komen_by_id($id)
	{
		$this->db->select('*')->from('simawa_komen')->where('id_simawa', $id);
		$query = $this->db->get();
		return $query->result();
	}

	public function save_komen($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('simawa_komen', $data);
		return $this->db->insert_id();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}



	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update('simlapkerma_pendaftaran_magang', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_daftar_magang', $id);
		$this->db->delete($this->table);
	}

	public function f_drop_down($nama_tabel, $id, $nama)
	{
		if ($nama_tabel == 'siakad_dosen') {
			$this->db->where('id_sms', $this->session->userdata('prodi'));
		}

		$this->db->order_by($id, 'asc');
		$result = $this->db->get($nama_tabel);


		$id_ptk = $this->session->userdata('id_ptk');
		$nm_ptk = $this->db->where('id_ptk', $id_ptk)
			->select('nm_ptk')
			->get('siakad_dosen')->row()->nm_ptk;

		$dd[$id_ptk] = $nm_ptk;

		foreach ($result->result_array() as $row) {
			$dd[$row[$id]] = $row[$nama];
		}

		return $dd;
	}
}
