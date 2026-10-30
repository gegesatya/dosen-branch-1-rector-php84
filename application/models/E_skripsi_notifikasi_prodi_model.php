<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_notifikasi_prodi_model extends CI_Model
{

	public $table = 'siakad_skripsi_kartu_bimbingan_proposal';
	public $column = ['tgl_notifikasi', 'jenis_notif', 'keterangan'];
	public $order = ['tgl_notifikasi' => 'DESC'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');


		$id_ptk = $this->session->userdata('id_ptk');
		/* $this->db->group_start();
		$this->db->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
			->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk]);
		$this->db->group_end(); */

		$this->db->where(['j.id_sms' => $id_sms])
			->from('siakad_skripsi_notifikasi n')
			->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = n.id_judul_skripsi')
			->join('siakad_dosen d', 'd.id_ptk = n.id_ptk');

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
		$user_logins = $this->ion_auth->user()->row();
		$id_sms = $this->session->userdata('prodi');

		$id_ptk = $this->session->userdata('id_ptk');

		$this->db->where(['j.id_sms' => $id_sms])
			->from('siakad_skripsi_notifikasi n')->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = n.id_judul_skripsi');

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
		$data = $this->security->xss_clean($data);
		$this->db->replace($this->table, $data);
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
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function get_by_id_2($id)
	{
		$this->db->from('siakad_skripsi_pengajuan_judul');
		$this->db->where('id_judul_skripsi', $id);
		$query = $this->db->get();

		return $query->row();
	}
	public function update_sempro($where, $data)
	{
		$this->db->update('siakad_skripsi_pengajuan_judul', $data, $where);
		return $this->db->affected_rows();
	}
}
