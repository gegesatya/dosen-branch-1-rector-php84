<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kehadiran_2_model extends CI_Model
{

	public $table = 'siakad_absen_detail_baru';
	public $column = ['mp.nipd', 'm.nm_pd'];
	public $order = ['m.nm_pd' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_jadwal = NULL)
	{
		$this->db
			->select('mp.nipd, m.nm_pd, a.*')
			->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		if ($id_jadwal <> NULL) {
			$this->db->where('id_jadwal', $id_jadwal);
		} else {
			$this->db->where('id_jadwal', '');
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

	public function get_datatables($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_jadwal = NULL)
	{
		$this->_get_datatables_query($id_jadwal);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_jadwal = NULL)
	{
		$this->db
			->select('mp.nipd, m.nm_pd, a.*')
			->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd');

		if ($id_jadwal <> NULL) {
			$this->db->where('id_jadwal', $id_jadwal);
		} else {
			$this->db->where('id_jadwal', '');
		}

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('uniq', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/* public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	} */

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
			->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->result_array();
		$data = [];

		foreach ($absen_detail as $key => $value) {
			$uniq = $value['uniq'];
			$jumlah_hadir = 0;
			for ($i = 1; $i <= $rencana_pertemuan->num_rows(); $i++) {
				$jumlah_hadir += $value['meet_' . $i] == 'H' ? 1 : 0;
			}

			if ($rencana_pertemuan->num_rows() == 0) {
				$persen_kehadiran = 0;
			} else {
				$persen_kehadiran = round($jumlah_hadir / $rencana_pertemuan->num_rows() * 100, 2);
			}

			//$this->db->update($this->table, ['persen_hadir'=>$persen_kehadiran], ['uniq'=>$uniq]);
			$data[] = ['uniq' => $uniq, 'persen_hadir' => $persen_kehadiran];
		}

		$this->db->update_batch($this->table, $data, 'uniq');
		return $this->db->affected_rows();
	}

	public function update_single($where, $data)
	{
		$this->db->update('siakad_absen_detail_baru', $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
			->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->row_array();
		$jumlah_hadir = 0;
		for ($i = 1; $i <= $rencana_pertemuan->num_rows(); $i++) {
			$jumlah_hadir += $absen_detail['meet_' . $i] == 'H' ? 1 : 0;
		}

		if ($rencana_pertemuan->num_rows() == 0) {
			$persen_kehadiran = 0;
		} else {
			$persen_kehadiran = round($jumlah_hadir / $rencana_pertemuan->num_rows() * 100, 2);
		}

		$this->db->update($this->table, ['persen_hadir' => $persen_kehadiran], $where);

		return $this->db->affected_rows();
	}

	public function update_2($where, $data)
	{
		$this->db->update($this->table, $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
			->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->result_array();
		$data = [];

		foreach ($absen_detail as $key => $value) {
			$uniq = $value['uniq'];
			$jumlah_hadir = 0;
			for ($i = 1; $i <= 30; $i++) {
				$jumlah_hadir += $value['meet_' . $i] == 'H' ? 1 : 0;
			}

			if ($rencana_pertemuan->num_rows() == 0) {
				$persen_kehadiran = 0;
			} else {
				$persen_kehadiran = round($jumlah_hadir / $rencana_pertemuan->num_rows() * 100, 2);
			}

			$data[] = ['uniq' => $uniq, 'persen_hadir' => $persen_kehadiran];
		}

		$this->db->update_batch($this->table, $data, 'uniq');
		return $this->db->affected_rows();
	}

	public function update_single_2($where, $data)
	{
		$this->db->update('siakad_absen_detail_baru', $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
			->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->row_array();
		$jumlah_hadir = 0;
		for ($i = 1; $i <= 30; $i++) {
			$jumlah_hadir += $absen_detail['meet_' . $i] == 'H' ? 1 : 0;
		}

		if ($rencana_pertemuan->num_rows() == 0) {
			$persen_kehadiran = 0;
		} else {
			$persen_kehadiran = round($jumlah_hadir / $rencana_pertemuan->num_rows() * 100, 2);
		}

		$this->db->update($this->table, ['persen_hadir' => $persen_kehadiran], $where);

		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function delete_all($id)
	{
		$this->db->where(['id_jadwal' => $id, 'is_realisasi' => 0]);
		$this->db->delete($this->table);
	}

	public function f_dropdown()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata("tahun_ajaran");


		$this->db->where(['id_sdm' => $id_ptk, 'id_smt' => $id_smt, 'is_generate_bap' => 1]);
		$this->db->order_by('tag_name', 'asc');
		$result = $this->db->from('siakad_jadwal')->get();

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($this->realisasi_16($row['id'])) {
					$dd[$row['id']] = $row['tag_name'];
				}
			}
		}
		return $dd;
	}

	public function f_status_kehadiran()
	{
		$dd[''] = 'Please Select';
		$dd['Hadir'] = 'Hadir';
		$dd['Tidak Hadir'] = 'Tidak Hadir';
		return $dd;
	}

	public function f_pertemuan_1()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();

		$dd[''] = 'Please Select';

		for ($i = 1; $i <= $rencana_pertemuan; $i++) {
			$dd['meet_' . $i] = 'Pertemuan ' . $i;
		}

		return $dd;
	}

	private function realisasi_16($id_jadwal)
	{
		//$id_jadwal = $this->session->userdata('id_kls_session');
		$pertemuan = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_rencana_pertemuan');
		$realisasi = TRUE;

		if ($pertemuan->num_rows() > 0) {
			for ($i = 1; $i <= 16; $i++) {
				$_iterasi =  $this->db->where(['id_jadwal' => $id_jadwal, 'pertemuan' => $i])->get('siakad_rencana_pertemuan');
				if (!empty($_iterasi->row()->is_realisasi)) {
					break;
					if ($_iterasi->row()->is_realisasi == 0) {
						$realisasi = FALSE;
						break;
					}
				}
			}
		}

		return $realisasi;
	}
}
