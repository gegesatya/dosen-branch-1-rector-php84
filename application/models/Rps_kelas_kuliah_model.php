<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_kelas_kuliah_model extends CI_Model
{

	public $table = 'siakad_absen_detail_baru';
	public $column = ['mk.nm_mk', 's.nm_lemb', 'mk.kode_mk', 's.nm_prodi_english', 'd.nm_ptk'];
	public $order = ['s.nm_lemb' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_sms)
	{
		$id_smt = $this->session->userdata('id_smt');
		$this->db->distinct()
			->select('mk.id_mk, kk.nm_kls, mk.kode_mk, mk.nm_mk,  kk.sks_mk,s.nm_lemb, s.nm_prodi_english, d.nm_ptk')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->join('siakad_rps_mk_prodi m1', 'm1.id_mk = mk.id_mk', 'LEFT')
			->join('siakad_dosen d', 'd.id_ptk = m1.id_ptk', 'LEFT')
			->order_by('nm_kls')
			->where('kk.id_smt', $id_smt);

		if ($id_sms == 'NIL') {
			$this->db->where('kk.id_sms', 'NIL');
		} else {
			$this->db->where('kk.id_sms', $id_sms);
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

	public function get_datatables($id_rps)
	{
		$this->_get_datatables_query($id_rps);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_rps)
	{
		$this->_get_datatables_query($id_rps);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_sms)
	{
		$id_smt = $this->session->userdata('id_smt');
		$this->db->distinct()
			->select('mk.id_mk, kk.nm_kls, mk.kode_mk, mk.nm_mk,  kk.sks_mk,s.nm_lemb, s.nm_prodi_english, d.nm_ptk')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->join('siakad_rps_mk_prodi m1', 'm1.id_mk = mk.id_mk', 'LEFT')
			->join('siakad_dosen d', 'd.id_ptk = m1.id_ptk', 'LEFT')
			->order_by('nm_kls')
			->where('kk.id_smt', $id_smt);

		if ($id_sms == 'NIL') {
			$this->db->where('kk.id_sms', 'NIL');
		} else {
			$this->db->where('kk.id_sms', $id_sms);
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function f_dropdown_rps()
	{
		//$id_ptk = $this->session->userdata('id_ptk');
		//$result = $this->db->get_where('siakad_rps_online', compact('id_ptk'));
		$result = $this->db->from('sms')->get();

		$dd['NIL'] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row['id_sms']] = $row['nm_lemb'];
			}
		}
		return $dd;
	}
}
