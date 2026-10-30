<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kaprodi_epom_model extends CI_Model
{

	public $table = 'arsip_nilai';
	public $column = [1, 'sj.tag_name', 'fk.id_smt', 'sd.nm_ptk'];
	public $order = ['fk.id_smt' => 'desc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->select('sj.*, sd.nm_ptk,sd.id_ptk, fk.id_kls, nm_mk')->from('siakad_jadwal sj')
			->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = sj.id')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = jd.id_kls')
			->where('fk.id_sms', $this->session->userdata('prodi'));

		/* $this->db->from('siakad_rekap_epom rek')
			->join('siakad_dosen sd', 'sd.id_ptk = rek.id_ptk')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = rek.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->where('fk.id_sms', $this->session->userdata('prodi'));
 */


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
		$this->db->from('siakad_jadwal sj')
			->join('siakad_dosen sd', 'sd.id_ptk = sj.id_sdm')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = sj.id')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = jd.id_kls')
			->where('fk.id_sms', $this->session->userdata('prodi'));

		/* 	$this->db->from('siakad_rekap_epom rek')
			->join('siakad_dosen sd', 'sd.id_ptk = rek.id_ptk')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = rek.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->where('fk.id_sms', $this->session->userdata('prodi')); */

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
		$query = $this->db->get();

		return $query->row();
	}
}
