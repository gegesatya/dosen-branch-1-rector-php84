<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Krs_report_model extends CI_Model
{

	public $table = 'siakad_krs';
	public $column = ['nm_pd', 'mahasiswa_pt.nipd'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($angkatan, $id_status_krs = NULL)
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;

		if ($id_status_krs == 0) {
			$where = [
				'siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk')
			];
		} else {
			$where = [
				'siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'),
				'id_status_krs' => $id_status_krs
			];
		}


		$this->db->from('siakad_krs')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_krs.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where($where);

		if ($angkatan <> 0) {
			$this->db->where_in('year(mahasiswa_pt.tgl_masuk_sp)', $angkatan);
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

	public function get_datatables($angkatan = NULL, $id_status_krs = NULL)
	{
		$this->_get_datatables_query($angkatan, $id_status_krs);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($angkatan, $id_status_krs = NULL)
	{
		$this->_get_datatables_query($angkatan, $id_status_krs);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($angkatan, $id_status_krs)
	{
		$user_logins = $this->ion_auth->user()->row();
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$where = ['siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'), 'id_status_krs' => $id_status_krs];
		$this->db->from('siakad_krs')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_krs.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where($where);

		if ($angkatan <> 0) {
			$this->db->where_in('year(mahasiswa_pt.tgl_masuk_sp)', $angkatan);
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_rencana_studi', $id);
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
