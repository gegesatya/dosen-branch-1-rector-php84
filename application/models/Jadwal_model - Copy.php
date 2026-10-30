<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal_model extends CI_Model
{

	public $table = 'siakad_jadwal';
	public $column = ['tag_name', 'nama_hari'];
	public $order = ['h.uniq' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->select('j.*, r.nama_ruang, h.nama_hari, a.id_rps, a.id_mk, a.id_kls, a.id_smt')->from('siakad_jadwal j')
			->join('siakad_rps_mk_prodi a', 'a.id_jadwal = j.id', 'LEFT')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang')
			->where(['j.id_sdm' => $id_ptk, 'j.id_smt' => $id_smt, 'id_kategori_jadwal' => 1]);
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
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->select('j.*, r.nama_ruang, h.nama_hari, a.id_rps, a.id_mk, a.id_kls, a.id_smt')->from('siakad_jadwal j')
			->join('siakad_rps_mk_prodi a', 'a.id_jadwal = j.id', 'LEFT')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang')
			->where(['j.id_sdm' => $id_ptk, 'j.id_smt' => $id_smt, 'id_kategori_jadwal' => 1]);

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
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
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function save_rps($data)
	{
		// $this->db->where(['id_kls'=> $id_kls, 'id_mk'=>$id_mk, 'id_ptk'=>$id_ptk])->delete('siakad_rps_mk_prodi');
		// $data = $this->security->xss_clean($data);
		$this->db->insert_batch('siakad_rps_mk_prodi', $data);
		return $this->db->insert_id();
	}

	public function delete_rps($id_jadwal, $id_smt)
	{

		$this->db->trans_begin();

		$this->db->where(['id_jadwal' => $id_jadwal, 'id_smt' => $id_smt])->delete('siakad_rps_mk_prodi');
		$this->db->where(['id_jadwal' => $id_jadwal])->delete('siakad_rencana_pertemuan');
		$this->db->where(['course_id' => $id_jadwal])->delete('lesson');
		$this->db->where(['id' => $id_jadwal])->update('siakad_jadwal', ['is_generate_bap' => 0]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
	}

	public function f_dropdown_rps()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$result = $this->db->from('siakad_new_rps_online o')->join('siakad_new_rps_online_detail d', 'o.id_rps = d.id_rps')
			->where(compact('id_ptk'))->order_by('nm_mk')->get();

		$dd['NIL'] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row['id_rps']] = '[' . $row['kode_mk'] . '] ' . $row['nm_mk'];
			}
		}
		return $dd;
	}
}
