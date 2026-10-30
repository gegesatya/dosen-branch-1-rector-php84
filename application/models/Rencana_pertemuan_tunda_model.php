<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rencana_pertemuan_tunda_model extends CI_Model
{

	public $table = 'siakad_rencana_pertemuan';
	public $column = [1, 'tanggal_rencana', 'jam_mulai', 'jam_selesai', 'berita_acara_rencana', 'bacaan_materi'];
	public $order = ['pertemuan' => 'asc', 'tanggal_rencana' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_jadwal = NULL)
	{
		$this->db->from($this->table);

		if ($id_jadwal <> NULL) {
			$this->db->where('id_jadwal', $id_jadwal);
		} else {
			$this->db->where('id_jadwal', '');
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

	public function get_datatables($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_jadwal = NULL)
	{
		$this->db->from($this->table);

		if ($id_jadwal <> NULL) {
			$this->db->where('id_jadwal', $id_jadwal);
		} else {
			$this->db->where('id_jadwal', '');
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

	public function delete_all($id)
	{
		$this->db->where(['id_jadwal' => $id, 'is_realisasi' => 0]);
		$this->db->delete($this->table);
	}

	public function f_dropdown()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("setting")->id_smt_tunda;

		$this->db->where(['id_sdm' => $id_ptk, 'id_smt' => $id_smt, 'is_generate_bap' => 1]);
		$this->db->order_by('tag_name', 'asc');
		$result = $this->db->from('siakad_jadwal')->get();

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row['id']] = $row['tag_name'];
			}
		}
		return $dd;
	}

	public function f_dropdown_backup($nama_tabel, $id, $nm)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("setting")->id_smt_tunda;

		$this->db->where(['id_ptk' => $id_ptk, 'id_smt' => $id_smt]);
		$this->db->order_by($nm, 'asc');
		$result = $this->db->get($nama_tabel);

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}
}
