<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kaprodi_krs_mhs_model extends CI_Model
{

	public $table = 'mahasiswa';
	public $column = ['nm_pd', 'fmp.nipd'];
	public $order = ['nm_pd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_agama)
	{
		$this->db->select('*, krs.create_date as tgl_registrasi')
			->from('mahasiswa fm')
			->join('mahasiswa_pt fmp', 'fm.id_pd = fmp.id_pd')
			->join('siakad_krs krs', 'krs.nipd = fmp.nipd');
		//if($id_agama == -1){
		$this->db->where([
			'fmp.id_sms' => $this->session->userdata('prodi'),
			'krs.id_smt' => $this->session->userdata('tahun_ajaran')
		]);
		/* }else{
			$this->db->where(['fm.id_agama' => $id_agama,
							  'fmp.id_sms'=>$this->session->userdata('prodi'),
							  'krs.id_smt'=>$this->session->userdata('tahun_ajaran')]);
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

	public function get_datatables($id_agama)
	{
		$this->_get_datatables_query($id_agama);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_agama)
	{
		$this->_get_datatables_query($id_agama);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_agama)
	{
		$this->db->select('*')
			->from('mahasiswa fm')
			->join('mahasiswa_pt fmp', 'fm.id_pd = fmp.id_pd')
			->join('siakad_krs krs', 'krs.nipd = fmp.nipd');
		//if($id_agama == -1){
		$this->db->where([
			'fmp.id_sms' => $this->session->userdata('prodi'),
			'krs.id_smt' => $this->session->userdata('tahun_ajaran')
		]);
		/* }else{
			$this->db->where(['fm.id_agama' => $id_agama,
							  'fmp.id_sms'=>$this->session->userdata('prodi'),
							  'krs.id_smt'=>$this->session->userdata('tahun_ajaran')]);
		} */
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_mk', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function f_dropdown($nama_tabel, $id, $nm)
	{
		$this->db->order_by($nm, 'asc');
		$result = $this->db->get($nama_tabel);

		$dd[-1] = 'All';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}
}
