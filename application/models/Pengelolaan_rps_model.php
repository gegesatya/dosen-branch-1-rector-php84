<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengelolaan_rps_model extends CI_Model
{

	public $table = 'feeder_mata_kuliah_kurikulum';
	public $column = ['nm_mk', 'smt'];
	public $order = ['nm_mk' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_kurikulum)
	{
		$this->db->select('*')
			->from('feeder_mata_kuliah_kurikulum mkm')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = mkm.id_mk')
			->join('feeder_kurikulum k', 'k.id_kurikulum_sp = mkm.id_kurikulum_sp');
		if ($id_kurikulum == -1) {
			$this->db->where(['k.id_sms' => $this->session->userdata('prodi')]);
		} else {
			$this->db->where(['k.id_kurikulum_sp' => $id_kurikulum, 'k.id_sms' => $this->session->userdata('prodi')]);
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

	public function get_datatables($id_kurikulum)
	{
		$this->_get_datatables_query($id_kurikulum);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_kurikulum)
	{
		$this->_get_datatables_query($id_kurikulum);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_kurikulum)
	{
		$this->db->select('*')
			->from('feeder_mata_kuliah_kurikulum mkm')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = mkm.id_mk')
			->join('feeder_kurikulum k', 'k.id_kurikulum_sp = mkm.id_kurikulum_sp');

		if ($id_kurikulum == -1) {
			$this->db->where(['k.id_sms' => $this->session->userdata('prodi')]);
		} else {
			$this->db->where(['k.id_kurikulum_sp' => $id_kurikulum, 'k.id_sms' => $this->session->userdata('prodi')]);
		}
		return $this->db->count_all_results();
	}

	public function get_by_id($id_kurikulum_sp, $id_mk)
	{
		$cek_data = $this->db->where(['id_kurikulum_sp' => $id_kurikulum_sp, 'id_mk' => $id_mk])->get('siakad_rps_online_pengelolaan');

		if ($cek_data->num_rows() == 0) {
			$this->db->insert('siakad_rps_online_pengelolaan', ['id_kurikulum_sp' => $id_kurikulum_sp, 'id_mk' => $id_mk]);
		}

		//$this->db->from('siakad_rps_online_pengelolaan');
		$this->db->select('*')
			->from('feeder_mata_kuliah_kurikulum mkm')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = mkm.id_mk')
			->join('feeder_kurikulum k', 'k.id_kurikulum_sp = mkm.id_kurikulum_sp');

		$this->db->where(['mkm.id_kurikulum_sp' => $id_kurikulum_sp, 'mkm.id_mk' => $id_mk]);
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
		$this->db->update('siakad_rps_online_pengelolaan', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_mk', $id);
		$this->db->delete($this->table);
	}

	public function f_dropdown($nama_tabel, $id, $nm)
	{
		$this->db->order_by($nm, 'asc');

		$result = $this->db->where('id_sms', $this->session->userdata('prodi'))->get($nama_tabel);

		$dd[-1] = 'All';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row[$nm];
			}
		}
		return $dd;
	}

	public function f_dropdown_2($nama_tabel, $id, $nm)
	{
		$this->db->order_by($nm, 'asc');
		//where('id_sms', $this->session->userdata('prodi'))
		$result = $this->db->select('*')->from('siakad_rps_online r')
			->join('siakad_rps_online_cakupan c', 'r.id_cakupan_rps = c.id_cakupan')
			->join('siakad_dosen d', 'd.id_ptk = r.id_ptk')
			->where(['c.id_sms' => $this->session->userdata('prodi')])
			->or_where(['c.id_sms' => 'institusi'])
			->get();

		$dd[-1] = 'Silahkan Pilih';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = $row['kode_revisi'] . ' | ' . $row[$nm] . ' | By :' . ucwords(strtolower($row['nm_ptk']));
			}
		}
		return $dd;
	}
}
