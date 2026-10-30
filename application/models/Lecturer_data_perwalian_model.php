<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_data_perwalian_model extends CI_Model
{

	public $table = 'siakad_evaluasi_keberhasilan_studi';
	public $column = ['ev.nipd', 'nm_pd'];
	public $order = ['ev.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($angkatan = 0)
	{
		$this->db->where(['ev.id_ptk' => $this->session->userdata('id_ptk')])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->from('siakad_evaluasi_keberhasilan_studi ev')
			->join('mahasiswa_pt mp', 'mp.nipd = ev.nipd');

		if (!empty($angkatan)) {
			$this->db->where_in('year(mp.tgl_masuk_sp)', $angkatan);
		}


		$i = 0;

		foreach ($this->column as $item) {
			if (!empty($_POST['search']['value'])) {

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

	public function get_datatables($angkatan = 0)
	{
		$this->_get_datatables_query($angkatan);
		if (isset($_POST['length']) && $_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start'] ?? 0);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($angkatan = 0)
	{
		$this->_get_datatables_query($angkatan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($angkatan = 0)
	{
		$this->db->where(['ev.id_ptk' => $this->session->userdata('id_ptk')])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->from('siakad_evaluasi_keberhasilan_studi ev')
			->join('mahasiswa_pt mp', 'mp.nipd = ev.nipd');

		if (!empty($angkatan)) {
			$this->db->where_in('year(mp.tgl_masuk_sp)', $angkatan);
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

	public function get_excel_data()
	{
		$q = $this->db->select('m.nm_pd, fmp.nipd, ipk, tgl_masuk_sp,telepon_seluler, email_wbi')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd')
			->order_by('fmp.nipd')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where('fmp.id_jns_keluar IS NULL', NULL, FALSE);
		return $q->get()->result();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}
}
