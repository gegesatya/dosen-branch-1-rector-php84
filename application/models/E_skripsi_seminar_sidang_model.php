<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_sidang_model extends CI_Model
{

	public $table = 'siakad_skripsi_seminar_sidang';
	public $column = ['s.nipd', 'm.nm_pd'];
	public $order = ['s.create_date' => 'desc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$this->db->group_start()
			->where(['dosen_pembimbing_1' => $id_ptk])
			->or_where(['dosen_pembimbing_2' => $id_ptk])
			->group_end()
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE);
		$this->db->select('s.*, m.nm_pd,j.*')
			->from('siakad_skripsi_seminar_sidang s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')->order_by('is_akademik', 'asc');
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

		$this->db->group_start()
			->where(['dosen_pembimbing_1' => $id_ptk])
			->or_where(['dosen_pembimbing_2' => $id_ptk])
			->group_end()
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE);

		$this->db->select('s.*, m.nm_pd,j.*')
			->from('siakad_skripsi_seminar_sidang s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')->order_by('is_akademik', 'asc');
		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from('siakad_skripsi_seminar_sidang s')->join('siakad_skripsi_pengajuan_judul j', 'j.id_judul_skripsi = s.id_judul_skripsi');
		$this->db->where('uniq', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_id_2($id)
	{
		$this->db->where(['siakad_skripsi_seminar_sidang.uniq' => $id])
			->select('siakad_skripsi_pengajuan_judul.*, mahasiswa_pt.nipd, mahasiswa.nm_pd, 
					siakad_skripsi_seminar_sidang.*')
			->from('siakad_skripsi_pengajuan_judul')
			->join('siakad_skripsi_seminar_sidang', 'siakad_skripsi_seminar_sidang.id_judul_skripsi = siakad_skripsi_pengajuan_judul.id_judul_skripsi')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd', 'left')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd', 'left');
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

	public function update_sempro($where, $data_judul, $data_approve, $judul_lama, $where2, $is_ganti_judul, $id_judul_skripsi)
	{
		//$this->db->update('siakad_skripsi_pengajuan_judul', $data, $where);
		//return $this->db->affected_rows();
		$user = $this->ion_auth->user()->row();

		$this->db->trans_begin();

		//update data di tabel siakad_skripsi_pengajuan_judul
		$this->db->update('siakad_skripsi_pengajuan_judul', $data_judul, $where);

		//update data approve
		$this->db->update('siakad_skripsi_seminar_sidang', $data_approve, $where2);

		//insert data ke tabel judul lama
		if ($is_ganti_judul) {
			$this->db->insert('siakad_skripsi_judul_lama', [
				'id_judul_skripsi' => $id_judul_skripsi,
				'judul_lama' => $judul_lama,
				'create_by' => $user->first_name,
				'create_date' => date('Y-m-d H:i:s')
			]);
		}


		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		} else {
			$this->db->trans_commit();
		}
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}
}
