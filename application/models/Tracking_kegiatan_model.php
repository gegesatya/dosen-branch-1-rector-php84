<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_kegiatan_model extends CI_Model
{

	public $table = 'simlapkerma_tracking_kegiatan';
	public $column = ['tk.create_date'];
	public $order = ['tk.create_date' => 'desc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_sms = $this->session->userdata('prodi');
		$this->db->select('distinct(tk.nipd) as nipd, nm_pd, tk.create_date')->from('simlapkerma_tracking_kegiatan tk')
			->join('simlapkerma_pendaftaran_magang pm', 'pm.id_daftar_magang = tk.id_daftar_magang', 'left')
			->join('mahasiswa_pt mp', 'mp.nipd = tk.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->where(['tk.id_sms' => $id_sms]);

		// $this->db->from($this->table);

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
		$id_sms = $this->session->userdata('prodi');
		$this->db->from('simlapkerma_tracking_kegiatan tk')
			->join('simlapkerma_pendaftaran_magang pm', 'pm.id_daftar_magang = tk.id_daftar_magang', 'left');
		$this->db->where(['tk.id_sms' => $id_sms]);

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('simlapkerma_tracking_kegiatan');
		$this->db->where('id_tracking', $id);
		$query = $this->db->get();

		return $query->row();
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
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_tracking', $id);
		$this->db->delete($this->table);
	}
}
