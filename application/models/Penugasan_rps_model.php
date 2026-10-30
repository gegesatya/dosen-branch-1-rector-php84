<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penugasan_rps_model extends CI_Model
{

	public $table = 'siakad_rps_online';
	public $column = ['nm_mk', 'nm_ptk', 'nm_cakupan', 'kode_revisi', 'nomor_dokumen'];
	public $order = ['nm_mk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->select('siakad_rps_online.*, nm_status_pengajuan_rps, nm_cakupan, nm_ptk')->from($this->table)
			->join('siakad_rps_online_cakupan', 'siakad_rps_online_cakupan.id_cakupan = siakad_rps_online.id_cakupan_rps')
			->join('siakad_rps_online_status_pengajuan', 'siakad_rps_online_status_pengajuan.id_status_pengajuan_rps = siakad_rps_online.id_status_pengajuan_rps')
			->join('siakad_dosen', 'siakad_dosen.id_ptk = siakad_rps_online.id_ptk');
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
		$this->db->select('siakad_rps_online.*, nm_status_pengajuan_rps, nm_cakupan, nm_ptk')->from($this->table)
			->join('siakad_rps_online_cakupan', 'siakad_rps_online_cakupan.id_cakupan = siakad_rps_online.id_cakupan_rps')
			->join('siakad_rps_online_status_pengajuan', 'siakad_rps_online_status_pengajuan.id_status_pengajuan_rps = siakad_rps_online.id_status_pengajuan_rps')
			->join('siakad_dosen', 'siakad_dosen.id_ptk = siakad_rps_online.id_ptk');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_rps', $id);
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
		$this->db->where('id_rps', $id);
		$this->db->delete($this->table);
	}

	/* function f_dropdown()
	{
		$result = $this->db->from('siakad_rps_online_cakupan')->get();

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row['id_cakupan']] = $row['nm_cakupan'];
			}
		}
		return $dd;
	} */

	public function f_dropdown()
	{
		$dd[''] = 'Please Select';
		$dd['R00'] = 'R00';
		$dd['R01'] = 'R01';
		$dd['R02'] = 'R02';
		$dd['R03'] = 'R03';
		$dd['R04'] = 'R04';
		$dd['R05'] = 'R05';
		/* $dd['7'] = 'R06';
		$dd['8'] = 'R07';
		$dd['9'] = 'R08';
		$dd['10'] = 'R09';
		$dd['11'] = 'R10'; */
		return $dd;
	}

	public function f_dropdown_pengesahan()
	{
		$dd[''] = 'Please Select';
		$dd['1'] = 'Kembalikan Ke Status Daftar';
		$dd['2'] = 'Diajukan oleh Dosen';
		$dd['3'] = 'Disahkan';
		$dd['4'] = 'Ditolak';

		return $dd;
	}
}
