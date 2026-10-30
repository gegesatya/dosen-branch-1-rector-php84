<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_sidang_kaprodi_model extends CI_Model
{

	public $table = 'siakad_skripsi_seminar_sidang';
	public $column = ['s.nipd', 'm.nm_pd'];
	public $order = ['s.nipd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query($index_page)
	{
		$id_sms = $this->session->userdata('prodi');

		$this->db->select('s.*, m.nm_pd')
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->where(['j.id_sms' => $id_sms, 'is_kaprodi' => 1, 'kategori_pengajuan' => $index_page])
			->from('siakad_skripsi_seminar_sidang s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')
			->order_by('is_akademik', 'asc');

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

	public function get_datatables($index_page)
	{
		$this->_get_datatables_query($index_page);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($index_page)
	{
		$this->_get_datatables_query($index_page);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($index_page)
	{
		$id_sms = $this->session->userdata('prodi');
		$this->db->select('s.*, m.nm_pd')
			->where('mp.id_jns_keluar IS NULL', NULL, FALSE)
			->where(['j.id_sms' => $id_sms, 'is_kaprodi' => 1, 'kategori_pengajuan' => $index_page])
			->from('siakad_skripsi_seminar_sidang s')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_skripsi_pengajuan_judul j', 'j.nipd = s.nipd')
			->order_by('is_akademik', 'asc');
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

	public function update_penguji($where, $data)
	{
		$this->db->update('siakad_skripsi_pengajuan_judul', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function get_dosen_penguji()
	{
		$id_sms = $this->session->userdata('prodi');
		$result = $this->db->where(['d.id_sms' => $id_sms])->order_by('nm_ptk')
			->select('*')->from('siakad_skripsi_dosen d')
			->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
		return $result->result();
	}

	public function get_nilai_by_penguji($id_ptk, $id_judul_skripsi, $tipe)
	{
		$nilai = $this->db->where(['id_penilai' => $id_ptk, 'id_judul_skripsi' => $id_judul_skripsi])->get('siakad_skripsi_nilai_sempro');

		if ($nilai->num_rows()) {
			if ($tipe == 1) {
				return $nilai->row()->nilai_akhir;
			} elseif ($tipe == 2) {
				return $nilai->row()->semhas_nilai_akhir;
			} elseif ($tipe == 3) {
				return $nilai->row()->sidang_nilai_akhir;
			}
		} else {
			return '0';
		}
	}
}
