<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_sempro_model extends CI_Model
{

	public $table = 'siakad_skripsi_kartu_bimbingan_proposal';
	public $column = ['mahasiswa.nm_pd', 'mahasiswa_pt.nipd', 'siakad_skripsi_kartu_bimbingan_proposal.html_respon'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

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

		$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms, 'sudah_boleh_maju_prodi' => 1])
			->where('mahasiswa_pt.id_jns_keluar IS NULL', NULL, FALSE)
			->select('siakad_skripsi_pengajuan_judul.*, siakad_skripsi_kartu_bimbingan_proposal.html_respon, mahasiswa_pt.nipd, mahasiswa.nm_pd')
			->from('siakad_skripsi_pengajuan_judul')
			->join('siakad_skripsi_kartu_bimbingan_proposal', 'siakad_skripsi_kartu_bimbingan_proposal.id_judul_skripsi = siakad_skripsi_pengajuan_judul.id_judul_skripsi', 'LEFT')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');



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
		// $this->db->group_start();
		// $this->db->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
		// 	->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk]);
		// $this->db->group_end();

		$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms])
			->where('mahasiswa_pt.nipd NOT IN (SELECT `nim` FROM `feeder_lulus_do`)', NULL, FALSE)
			->select('siakad_skripsi_pengajuan_judul.*, siakad_skripsi_kartu_bimbingan_proposal.html_respon, mahasiswa_pt.nipd, mahasiswa.nm_pd')
			->from('siakad_skripsi_pengajuan_judul')
			->join('siakad_skripsi_kartu_bimbingan_proposal', 'siakad_skripsi_kartu_bimbingan_proposal.id_judul_skripsi = siakad_skripsi_pengajuan_judul.id_judul_skripsi', 'LEFT')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');

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

	public function get_dosen_penguji()
	{
		$id_sms = $this->session->userdata('prodi');
		$result = $this->db->where(['d.id_sms' => $id_sms, 'is_penguji' => 1])->order_by('nm_ptk')
			->select('*')->from('siakad_skripsi_dosen d')
			->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
		return $result->result();
	}
}
