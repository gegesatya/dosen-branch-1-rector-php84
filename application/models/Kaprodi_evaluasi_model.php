<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kaprodi_evaluasi_model extends CI_Model
{

	public $table = 'siakad_evaluasi_keberhasilan_studi';
	public $column = ['mp.nipd', 'nm_pd', 'nm_ptk'];
	public $order = ['mp.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query($angkatan)
	{
		/* $this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd_feeder')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where_not_in('fmp.id_reg_pd', 'SELECT id_reg_pd from feeder_lulus_do', FALSE); */

		$id_sms = $this->session->userdata('prodi');

		$this->db->where(['mp.id_sms' => $id_sms])
			->from('siakad_evaluasi_keberhasilan_studi ev')
			->join('mahasiswa_pt mp', 'mp.nipd = ev.nipd')
			->join('siakad_dosen sd', 'sd.id_ptk = ev.id_ptk');

		if ($angkatan <> 0) {
			$this->db->where_in('year(mp.tgl_masuk_sp)', $angkatan);
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
		$id_sms = $this->session->userdata('prodi');
		$this->db->where(['mp.id_sms' => $id_sms])
			->from('siakad_evaluasi_keberhasilan_studi ev')
			->join('mahasiswa_pt mp', 'mp.nipd = ev.nipd')
			->join('siakad_dosen sd', 'sd.id_ptk = ev.id_ptk');

		if ($angkatan <> 0) {
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
