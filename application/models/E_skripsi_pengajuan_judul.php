<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_penetapan_judul_model extends CI_Model
{

	public $table = 'siakad_skripsi_pengajuan_judul';
	public $column = ['mahasiswa.nm_pd', 'mahasiswa_pt.nipd'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query($angkatan, $id_status_krs = NULL)
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_sms = $this->session->userdata('prodi');

		/* if ($id_status_krs == 1) {
			$where = [
				'siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'),
				'id_status_krs' => $id_status_krs
			];
		} else {
			$where = [
				'siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'),
				'id_status_krs >=' => $id_status_krs
			];
		} */


		$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms])->from('siakad_skripsi_pengajuan_judul')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd', 'left')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd', 'left');

		if ($angkatan <> 0) {
			$this->db->where('status_penetapan_judul', $angkatan);
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

	public function get_datatables($angkatan = NULL, $id_status_krs = NULL)
	{
		$this->_get_datatables_query($angkatan, $id_status_krs);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($angkatan, $id_status_krs = NULL)
	{
		$this->_get_datatables_query($angkatan, $id_status_krs);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($angkatan, $id_status_krs)
	{
		$user_logins = $this->ion_auth->user()->row();
		$id_sms = $this->session->userdata('prodi');
		//$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		//$where = ['siakad_krs.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk'), 'id_status_krs' => $id_status_krs];
		$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms])->from('siakad_skripsi_pengajuan_judul')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd', 'left')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd', 'left');

		if ($angkatan <> 0) {
			$this->db->where('status_penetapan_judul', $angkatan);
		}

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
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function f_status_penetapan_judul()
	{
		$result = $this->db->where_in('status_penetapan_judul', [3,  1])
			->get('siakad_skripsi_status_penetapan_judul');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($row['status_penetapan_judul'] == 1) {
					$row['nm_status_penetapan_judul'] = 'Berikan kesempatan untuk edit Judul';
				}
				$dd[$row['status_penetapan_judul']] = $row['nm_status_penetapan_judul'];
			}
		}
		return $dd;
	}

	public function get_dosen()
	{
		$id_sms = $this->session->userdata('prodi');
		$result = $this->db->where(['d.id_sms' => $id_sms, 'is_pembimbing' => 1])->order_by('nm_ptk')
			->select('*')->from('siakad_skripsi_dosen d')
			->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
		return $result->result();
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
