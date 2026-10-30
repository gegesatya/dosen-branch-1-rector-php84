<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_kaprodi_model extends CI_Model
{

	public $table = 'siakad_jadwal';
	public $column = ['j.tag_name', 'sd.nm_ptk'];
	public $order = ['sd.nm_ptk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query($id_smt)
	{
		$id_sms = $this->session->userdata('prodi');

		$this->db->where(['j.id_sms' => $id_sms, 'id_kategori_jadwal' => 1, 'j.id_smt >=' => 20241])
			->select('j.id, j.tag_name, sd.nm_ptk, h.nama_hari, j.jam_mulai, j.jam_selesai, sd.nm_ptk,j.id_smt, sd.nidn, sd.id_ptk, se.nm_smt')
			->from('siakad_jadwal j')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->join('semester se', 'se.id_smt = j.id_smt');


		if ($id_smt <> 0) {
			$this->db->where('j.id_smt', $id_smt);
		}


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

	public function get_datatables($id_smt = NULL)
	{
		$this->_get_datatables_query($id_smt);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_smt)
	{
		$this->_get_datatables_query($id_smt);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_smt)
	{
		$id_sms = $this->session->userdata('prodi');

		$this->db->where(['j.id_sms' => $id_sms, 'id_kategori_jadwal' => 1, 'j.id_smt >=' => 20241])
			->from('siakad_jadwal j')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('siakad_hari h', 'h.uniq = j.id_hari');

		if ($id_smt <> 0) {
			$this->db->where('j.id_smt', $id_smt);
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_evaluasi_keberhasilan_studi');

		$this->db->where('nipd', $id);
		$query = $this->db->get()->row();
		return $query;
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

	public function f_status_krs()
	{
		$result = $this->db->where_in('id_status_krs', [3, 4, 1])
			->get('siakad_status_krs');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($row['id_status_krs'] == 1) {
					$row['nm_status_krs'] = 'Berikan kesempatan untuk edit KRS';
				}
				$dd[$row['id_status_krs']] = $row['nm_status_krs'];
			}
		}
		return $dd;
	}
}
