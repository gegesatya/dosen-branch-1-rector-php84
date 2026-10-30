<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konsultasi_nilai_model extends CI_Model
{

	public $table = 'siakad_nilai_baru';
	public $column = ['nb.nipd', 'nm_pd', 'nilai_angka'];
	public $order = ['nb.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->select('nb.nipd, m.nm_pd, nb.nilai_angka, nb.nilai_huruf, nb.id_kls, count(nb.nipd) as jml, m.konsultasi_nilai')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
			->join('siakad_nilai_baru nb', 'nb.nipd = mp.nipd')
			->group_by('nb.nipd')
			->where(['m.dosen_pa' => $id_ptk, 'nb.nilai_huruf' => 'E'])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE);

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

		$this->db->select('nb.nipd, m.nm_pd, nb.nilai_angka, nb.nilai_huruf')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
			->join('siakad_nilai_baru nb', 'nb.nipd = mp.nipd')
			->group_by('nb.nipd')
			->where(['m.dosen_pa' => $id_ptk, 'nb.nilai_huruf' => 'E'])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE);
		return $this->db->count_all_results();
	}

	public function get_by_id($nipd)
	{
		$this->db->select('nb.nilai_angka, nb.nilai_huruf, fk.id_smt ,mk.nm_mk, mk.kode_mk, nb.n_pengetahuan_2_uts, nb.n_pengetahuan_2_uas, fk.id_smt');
		$this->db->from('siakad_nilai_baru nb')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk');
		$this->db->where(['nb.nipd' => $nipd, 'nb.nilai_huruf' => 'E']);
		$this->db->order_by('fk.id_smt');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_nilai_mk($id_jadwal)
	{
		$data_nilai_e = $this->db->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => 'E'])
			->from('siakad_nilai_baru nb')
			->join('mahasiswa_pt mp', 'mp.nipd = nb.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')->order_by('nm_pd')->get();
		return $data_nilai_e->result();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update('mahasiswa', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function f_dropdown($nama_tabel, $id, $nm)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->where(['id_sdm' => $id_ptk, 'sk.id_smt' => $id_smt]);
		$this->db->order_by('mk.nm_mk', 'asc');
		$result = $this->db->from('feeder_kelas_kuliah sk')
			->join('feeder_mata_kuliah mk', 'sk.id_mk = mk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = sk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->get();

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = '[' . $row[$nm] . '] ' . $row['nm_mk'];
			}
		}
		return $dd;
	}
}
