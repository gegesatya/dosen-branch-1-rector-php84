<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Eoffice_handling_time_model extends CI_Model
{

	public $table = 'e_office';
	public $column = ['e_office.nipd', 'mahasiswa.nm_pd', 'e_office.create_date', 'nomor_surat', 'tanggal_surat'];
	public $order = ['e_office.create_date' => 'desc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');

		$this->db->select('e_office.*, mahasiswa.nm_pd, nm_kategori_pengantar');
		$this->db->from('e_office')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = e_office.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->join('e_office_kategori_pengantar', 'e_office.id_kategori_pengantar = e_office_kategori_pengantar.id_kategori_pengantar');
		//$this->db->where_in('e_office.id_status', array(3, 4));
		//$this->db->where_in('e_office.id_kategori_pengantar', array(2, 3));
		//$this->db->where(['e_office.id_smt' => $id_smt, 'e_office.id_sms' => $id_sms]);

		/* if ($status_pelaporan <> 'NIL') {
			$this->db->where('e_office.id_status', $status_pelaporan);
		} */

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
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');

		$this->db->select('e_office.*, mahasiswa.nm_pd, nm_kategori_pengantar');
		$this->db->from('e_office')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = e_office.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->join('e_office_kategori_pengantar', 'e_office.id_kategori_pengantar = e_office_kategori_pengantar.id_kategori_pengantar');
		//$this->db->where_in('e_office.id_status', array(3, 4));
		//$this->db->where_in('e_office.id_kategori_pengantar', array(2, 3));
		//$this->db->where(['e_office.id_smt' => $id_smt, 'e_office.id_sms' => $id_sms]);


		/* if ($status_pelaporan <> 'NIL') {
			$this->db->where('e_office.id_status', $status_pelaporan);
		} */
		return $this->db->count_all_results();
	}

	public function get_by_id($id_eoffice)
	{
		$this->db->select('e_office.*, 
							sms.nm_lemb,
							mahasiswa.nm_pd, 
							mahasiswa.jln, 
							mahasiswa.telepon_seluler, 
							mahasiswa.tmpt_lahir, 
							mahasiswa.tgl_lahir,
							mahasiswa_pt.id_reg_pd_feeder,
							nm_kategori_pengantar');
		$this->db->from('e_office')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = e_office.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->join('sms', 'sms.id_sms = mahasiswa_pt.id_sms')
			->join('e_office_kategori_pengantar', 'e_office_kategori_pengantar.id_kategori_pengantar = e_office.id_kategori_pengantar');

		$this->db->where('id_eoffice', $id_eoffice);
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
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_simawa', $id);
		$this->db->delete($this->table);
	}

	public function f_drop_down($nama_tabel, $id, $nama)
	{
		if ($nama_tabel == 'tahun_ajaran') {
			$this->db->where('id_thn_ajaran >', 2015);
		}

		if ($nama_tabel == 'simawa_status_pelaporan') {
			$this->db->where_in('id_status_pelaporan', [1, 3]);
		}
		$this->db->order_by($id, 'asc');
		$result = $this->db->get($nama_tabel);

		$dd[''] = 'Please Select';

		foreach ($result->result_array() as $row) {
			$dd[$row[$id]] = $row[$nama];
		}

		return $dd;
	}
}
