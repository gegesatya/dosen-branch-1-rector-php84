<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_data_perwalian_model extends CI_Model
{

	public $table = 'siakad_evaluasi_keberhasilan_studi';
	public $column = ['nipd', 'nm_pd'];
	public $order = ['nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		/* $this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd_feeder')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where_not_in('fmp.id_reg_pd', 'SELECT id_reg_pd from feeder_lulus_do', FALSE); */

		$this->db->where(['id_ptk' => $this->session->userdata('id_ptk')])->from('siakad_evaluasi_keberhasilan_studi');

		$i = 0;

		foreach ($this->column as $item) {
			if ($_POST['search']['value']) {

				if ($i === 0) // first loop
				{
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
		/* $this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd_feeder')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where_not_in('fmp.id_reg_pd', 'SELECT id_reg_pd from feeder_lulus_do', FALSE); */
		$this->db->from('siakad_evaluasi_keberhasilan_studi');

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_evaluasi_keberhasilan_studi');

		$this->db->where('nipd', $id);
		$query = $this->db->get()->row();
		return $query;
	}

	public function get_excel_data()
	{
		$q = $this->db->select('m.nm_pd, fmp.nipd, ipk, tgl_masuk_sp,telepon_seluler, email_wbi')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd')
			->order_by('fmp.nipd')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where_not_in('fmp.id_reg_pd', 'SELECT id_reg_pd from feeder_lulus_do', FALSE);
		return $q->get()->result();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}
}
