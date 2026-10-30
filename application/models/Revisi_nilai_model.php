<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Revisi_nilai_model extends CI_Model
{

	public $table = 'siakad_nilai_baru';
	public $column = ['mp.nipd', 'm.nm_pd'];
	public $order = ['mk.nm_mk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('n.id_kls,mp.nipd, m.nm_pd, n.nilai_angka, n.nilai_huruf, n.nilai_indeks, kk.nm_kls,
						 mk.id_mk, mk.kode_mk, mk.nm_mk, is_memo_revisi,file_kelengkapan,
						 n.nilai_angka_revisi, n.nilai_huruf_revisi, n.nilai_indeks_revisi, n.alasan_revisi, n.dosen_pemberi_nilai, n.nilai_sebelumnya, n.tanggal_diajukan')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('mahasiswa_pt mp', 'mp.nipd = n.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['n.is_memo_revisi' => 1])->or_where(['n.is_memo_revisi' => 2]);
		// ->where('mp.nipd NOT IN (SELECT `nim` FROM `feeder_lulus_do`)', NULL, FALSE);
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
		$this->db->select('n.id_kls,mp.nipd, m.nm_pd, n.nilai_angka, n.nilai_huruf, n.nilai_indeks, kk.nm_kls, mk.id_mk, mk.kode_mk, mk.nm_mk')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('mahasiswa_pt mp', 'mp.nipd = n.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['n.is_memo_revisi' => 1]);
		// ->where('mp.nipd NOT IN (SELECT `nim` FROM `feeder_lulus_do`)', NULL, FALSE);


		return $this->db->count_all_results();
	}

	public function get_by_id($id_kls, $nipd)
	{
		$this->db->select('n.id_kls,mp.nipd, m.nm_pd, mk.kode_mk, mk.nm_mk, n.nilai_angka, n.nilai_huruf, n.nilai_indeks,
		n.nilai_angka_revisi, n.nilai_huruf_revisi, n.nilai_indeks_revisi, n.alasan_revisi, n.dosen_pemberi_nilai, n.nilai_sebelumnya')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('mahasiswa_pt mp', 'mp.nipd = n.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->where(['n.id_kls' => $id_kls, 'n.nipd' => $nipd]);
		$query = $this->db->get();

		return $query->row();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function f_memo_revisi()
	{


		$dd[''] = 'Please Select';
		$dd['2'] = 'Disetujui';

		return $dd;
	}
}
