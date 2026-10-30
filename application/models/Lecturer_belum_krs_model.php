<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_belum_krs_model extends CI_Model
{

	public $table = 'siakad_krs';
	public $column = [null, null, 'mahasiswa_pt.nipd', 'mahasiswa.nm_pd', null, 'km.sks_total', 'km.ipk'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($angkatan = NULL)
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		//$user_logins = $this->ion_auth->user()->row();

		$this->db->select('mahasiswa_pt.nipd, mahasiswa.nm_pd, mahasiswa.foto, mahasiswa.a_penerima_kip, mahasiswa_pt.tgl_masuk_sp, km.ipk, km.sks_total')
			->select("TIMESTAMPDIFF(YEAR, mahasiswa_pt.tgl_masuk_sp, CURDATE()) as thn_studi")
			->select("TIMESTAMPDIFF(MONTH, mahasiswa_pt.tgl_masuk_sp, CURDATE()) % 12 as bln_studi")
			->from('mahasiswa')
			->join('mahasiswa_pt', 'mahasiswa_pt.id_pd = mahasiswa.id_pd')
			->join('(SELECT id_reg_pd, ipk, sks_total FROM kuliah_mahasiswa WHERE (id_reg_pd, id_smt) IN (SELECT id_reg_pd, MAX(id_smt) FROM kuliah_mahasiswa GROUP BY id_reg_pd)) km', 'km.id_reg_pd = mahasiswa_pt.id_reg_pd_feeder', 'left')
			->where(['mahasiswa.dosen_pa' => $this->session->userdata('id_ptk')])
			->where("mahasiswa_pt.nipd NOT IN (SELECT nipd FROM siakad_krs WHERE id_smt = '$id_smt')", NULL, FALSE)
			->where('mahasiswa_pt.id_jns_keluar IS NULL', NULL, FALSE);
		//->where('mahasiswa_pt.nipd NOT IN (SELECT nipd FROM siakad_krs WHERE id_smt = ' . $id_smt . ')', NULL, FALSE);

		if ($angkatan <> 0) {
			$this->db->where_in('year(mahasiswa_pt.tgl_masuk_sp)', $angkatan);
		}

		$i = 0;
		foreach ($this->column as $item) {
			if ($item && $_POST['search']['value']) {
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
			$order_col = $column[$_POST['order']['0']['column']];
			if ($order_col) {
				$this->db->order_by($order_col, $_POST['order']['0']['dir']);
			}
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables($angkatan = NULL)
	{
		$this->_get_datatables_query($angkatan);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($angkatan)
	{
		$this->_get_datatables_query($angkatan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($angkatan)
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;

		$this->db->from('mahasiswa')
			->join('mahasiswa_pt', 'mahasiswa_pt.id_pd = mahasiswa.id_pd')
			->where(['mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'), 'mahasiswa_pt.tgl_keluar' => NULL])
			->where("mahasiswa_pt.nipd NOT IN (SELECT nipd FROM siakad_krs WHERE id_smt = '$id_smt')", NULL, FALSE)
			->where('mahasiswa_pt.id_jns_keluar IS NULL', NULL, FALSE);

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



	public function f_status_krs()
	{
		$result = $this->db->where_in('id_status_krs', [3, 4, 1])
			->get('siakad_status_krs');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($row['id_status_krs'] == 1) {
					$row['nm_status_krs'] = 'Dikembalikan ke status Daftar KRS';
				}
				$dd[$row['id_status_krs']] = $row['nm_status_krs'];
			}
		}
		return $dd;
	}
}
