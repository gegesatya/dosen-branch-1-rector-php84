<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Review_laporan_model extends CI_Model
{

	public $table = 'simutu_pelaksanaan_ami';
	public $column = ['daftar_tilik'];
	public $order = ['1' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		// $id_ptk = $this->session->userdata('id_ptk');
		$this->db->from('simlapkerma_lokasi_magang m')
			->join('simlapkerma_tracking_kegiatan tl', 'tl.nipd = m.nipd')
			->where(['tl.nipd' => $_POST['nipd']]);

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

		$this->db->from('simlapkerma_lokasi_magang')
			->where(['dosen_pembimbing_lapangan' => $id_ptk]);

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->select('tk.*, mp.nipd, m.nm_pd, m.foto, ps.nm_lemb as nm_prodi, lm.nama_perusahaan')
			->from('simlapkerma_tracking_kegiatan tk')
			->join('mahasiswa_pt mp', 'mp.nipd = tk.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('simlapkerma_lokasi_magang lm', 'lm.nipd = tk.nipd')
			->join('sms ps', 'ps.id_sms = mp.id_sms', 'left')
			->where('tk.id_tracking', $id);
		// $this->db->where('id_tracking', $id);
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
		$this->db->update('simlapkerma_tracking_kegiatan', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function f_dropdown($nama_tabel, $id, $nm)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->where(['id_sdm' => $id_ptk, 'sk.id_smt' => $id_smt]);
		$this->db->order_by('mk.nm_mk', 'asc');
		$result = $this->db->from('feeder_kelas_kuliah sk')
			->join('feeder_mata_kuliah mk', 'sk.id_mk = mk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = sk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->get();

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row[$id]] = '[' . $row[$nm] . '] ' . $row['nm_mk'];
			}
		}
		return $dd;
	}

	public function get_alasan_edit($id_tracking)
	{
		$this->db->where('id_tracking', $id_tracking);
		$this->db->order_by('created_at', 'desc');
		return $this->db->get('simlapkerma_alasan_edit_laporan')->result();
	}
}
