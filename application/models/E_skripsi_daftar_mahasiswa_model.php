<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_mahasiswa_model extends CI_Model
{

	public $table = 'siakad_skripsi_analisis';
	public $column = ['mp.nipd', 'nm_pd'];
	public $order = ['mp.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');


		$id_ptk = $this->session->userdata('id_ptk');
		/* $this->db->group_start();
		$this->db->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
			->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk]);
		$this->db->group_end(); */

		$this->db->where(['mp.id_sms' => $id_sms])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->select('mp.nipd, nm_pd, a.analisis_sks_total, a.analisis_ipk,
			a.analisis_sks_lulus, a.analisis_sks_tidak_lulus, a.is_boleh_ajukan_judul')
			->from('siakad_skripsi_analisis a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

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
		$user_logins = $this->ion_auth->user()->row();
		$id_sms = $this->session->userdata('prodi');

		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->where(['mp.id_sms' => $id_sms])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->select('mp.nipd, nm_pd, a.analisis_sks_total, a.analisis_ipk,
			a.analisis_sks_lulus, a.analisis_sks_tidak_lulus, a.is_boleh_ajukan_judul')
			->from('siakad_skripsi_analisis a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		return $this->db->count_all_results();
	}

	/* public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_rencana_studi', $id);
		$query = $this->db->get();

		return $query->row();
	} */

	public function save($data)
	{
		$this->db->replace($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}



	public function get_analisis_data($nipd)
	{
		$data = $this->db->where('a.nipd', $nipd)
			->select('a.*, m.nm_pd')
			->from('siakad_skripsi_analisis a')
			->join('mahasiswa_pt mp', 'a.nipd = mp.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->get()->row();

		$mk_tdk_lls =  str_replace('\r\n', '<br />', $data->makul_tidak_lulus);
		return [$data, $mk_tdk_lls];
	}
}
