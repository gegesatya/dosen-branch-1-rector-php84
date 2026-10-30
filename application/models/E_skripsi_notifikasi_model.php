<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_notifikasi_model extends CI_Model
{

	public $table = 'siakad_skripsi_kartu_bimbingan_proposal';
	public $column = ['tgl_notifikasi', 'jenis_notif', 'not.nipd', 'nm_pd'];
	public $order = ['tgl_notifikasi' => 'DESC'];

	public function __construct()
	{
		parent::__construct();
	}

	private function _get_datatables_query()
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');


		$id_ptk = $this->session->userdata('id_ptk');
		/* $this->db->group_start();
		$this->db->where(['j.dosen_pembimbing_1' => $id_ptk])
			->or_where(['j.dosen_pembimbing_2' => $id_ptk])
			->or_where(['j.dosen_penguji_sempro_1' => $id_ptk])
			->or_where(['j.dosen_penguji_sempro_2' => $id_ptk]);
		$this->db->group_end(); */

		// $this->db->where(['id_ptk' => $id_ptk])
		// 	->where('mp.nipd NOT IN (SELECT `nim` FROM `feeder_lulus_do`)', NULL, FALSE)
		// 	->from('siakad_skripsi_notifikasi not')->join('mahasiswa_pt mp', 'mp.nipd = not.nipd')->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		$this->db->where(['id_ptk' => $id_ptk])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->from('siakad_skripsi_notifikasi not')->join('mahasiswa_pt mp', 'mp.nipd = not.nipd')->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		/* $this->db->select('*')->from('siakad_skripsi_seminar_sidang ss')
			->join('siakad_skripsi_pengajuan_judul j', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->join('siakad_skripsi_notifikasi sn', 'sn.id_judul_skripsi = j.id_judul_skripsi'); */
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

		/* $this->db->where(['id_ptk' => $id_ptk])
			->from('siakad_skripsi_notifikasi sn')->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = sn.id_judul_skripsi'); */
		/* $this->db->select('*')->from('siakad_skripsi_seminar_sidang ss')
			->join('siakad_skripsi_pengajuan_judul j', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['is_kaprodi' => 1, 'is_akademik' => 1]); */

		$this->db->where(['id_ptk' => $id_ptk])
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->from('siakad_skripsi_notifikasi not')->join('mahasiswa_pt mp', 'mp.nipd = not.nipd')->join('mahasiswa m', 'm.id_pd = mp.id_pd');


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
