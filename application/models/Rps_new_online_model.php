<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_new_online_model extends CI_Model
{

	public $table = 'siakad_new_rps_online';
	public $column = ['', ''];
	public $order = ['' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		// $this->db->where(['id_ptk' => $this->session->userdata('id_ptk')])
		// 	->select('*')->from('siakad_new_rps_online r')
		// 	->join('siakad_rps_koordinator k', 'k.koordinator_rps = r.kaprodi_rps', 'LEFT');

		$this->db->where(['id_ptk' => $this->session->userdata('id_ptk')])
			->where('(is_deleted = 0 OR is_deleted IS NULL)')
			->select('*')->from('siakad_new_rps_online');
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
		$this->db->where(['id_ptk' => $this->session->userdata('id_ptk')])
			->where('(is_deleted = 0 OR is_deleted IS NULL)')
			->select('*')->from('siakad_new_rps_online');
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
		$data = clean_wysiwyg_data($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = clean_wysiwyg_data($data);
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id, $user_email = null)
	{
		$data = [
			'is_deleted' => 1,
			'deleted_at' => date('Y-m-d H:i:s'),
			'deleted_by' => $user_email ?: ($this->ion_auth->user()->row()->email ?? ($this->session->userdata('identity') ?: 'dosen'))
		];
		$this->db->where('id_rps', $id);
		$this->db->update($this->table, $data);
		return ($this->db->affected_rows() > 0);
	}

	public function f_dropdown($nama_tabel, $id, $nm, $orderby = NULL, $where = NULL)
	{
		if (!empty($orderby)) {
			$this->db->order_by($orderby, 'asc');
		} else {
			$this->db->order_by($nm, 'asc');
		}
		if (!empty($where)) {
			$result = $this->db->where('id_sdm', $where)->get($nama_tabel);
		} else {
			$result = $this->db->get($nama_tabel);
		}

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}

	public function f_dropdown_mk()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$id_sms = $this->session->userdata('prodi');

		$jadwal = $this->db->where(['sj.id_smt' => $id_smt, 'id_sdm' => $id_ptk, 'id_kategori_jadwal' => 1])
			->from('siakad_jadwal sj')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = sj.id')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')->get();

		$dd[''] = 'Please Select';
		if ($jadwal->num_rows() > 0) {
			foreach ($jadwal->result_array() as $row) {
				$dd[$row['id_mk']] = '[' . $row['kode_mk'] . '] ' . $row['nm_mk'];
			}
		}

		// Sertakan juga MK yang sudah pernah dibuatkan RPS oleh dosen tersebut agar tidak hilang saat edit
		if (!empty($id_ptk)) {
			$existing_rps = $this->db->select('id_mk, kode_mk, nm_mk')
				->where('id_ptk', $id_ptk)
				->where('(is_deleted = 0 OR is_deleted IS NULL)')
				->where("id_mk IS NOT NULL AND id_mk != ''")
				->get('siakad_new_rps_online')->result_array();
			foreach ($existing_rps as $r) {
				if (!empty($r['id_mk']) && !isset($dd[$r['id_mk']])) {
					$label = (!empty($r['kode_mk']) ? '[' . $r['kode_mk'] . '] ' : '') . ($r['nm_mk'] ?: 'Mata Kuliah');
					$dd[$r['id_mk']] = $label;
				}
			}
		}

		return $dd;
	}

	public function f_dd_koordinator()
	{
		$id_sms = $this->session->userdata('prodi');
		$dosen_prodi = $this->db->where(['id_sms' => $id_sms, 'id_ikatan_kerja <>' => 'X'])->get('siakad_dosen');

		$dd[''] = 'Please Select';
		if ($dosen_prodi->num_rows() > 0) {
			foreach ($dosen_prodi->result_array() as $row) {
				$dd[$row['id_ptk']] = '[' . $row['nidn'] . '] ' . $row['nm_ptk'];
			}
		}
		return $dd;
	}


	public function f_dropdown2($nama_tabel, $id, $nm, $orderby = NULL, $where = NULL)
	{
		if (!empty($orderby)) {
			$this->db->order_by($orderby, 'asc');
		} else {
			$this->db->order_by($nm, 'asc');
		}
		if (!empty($where)) {
			$result = $this->db->where($where)->get($nama_tabel);
		} else {
			$result = $this->db->get($nama_tabel);
		}

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}

	public function get_excel_data($id_rps)
	{
		return $this->db->where('id_rps', $id_rps)->from('siakad_new_rps_online_detail')->get()->result();
	}
}
