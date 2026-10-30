<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_kelola_model extends CI_Model
{

	public $table = 'siakad_absen_detail_baru';
	public $column = ['mk.nm_mk', 's.nm_lemb', 'mk.kode_mk', 's.nm_prodi_english'];
	public $order = ['s.nm_lemb' => 'asc'];
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($id_rps)
	{
		/* $this->db
			->select('mk.*, k.id_kurikulum_sp, s.nm_lemb, s.nm_prodi_english, k.nm_kurikulum_sp, mkk.smt')
			->from('feeder_mata_kuliah mk')
			->join('feeder_mata_kuliah_kurikulum mkk', 'mkk.id_mk = mk.id_mk')
			->join('feeder_kurikulum k', 'k.id_kurikulum_sp = mkk.id_kurikulum_sp')
			->join('sms s', 's.id_sms = mk.id_sms'); */

		/* if ($id_rps <> '') {
			//$this->db->where('mk.id_mk', $id_rps);
		} else {
			$this->db->where('mk.id_mk', 'NIL');
		} */
		$id_smt = $this->session->userdata('id_smt');
		$this->db
			->select('mk.id_mk, kk.nm_kls, mk.kode_mk, mk.nm_mk,  kk.sks_mk,s.nm_lemb, s.nm_prodi_english')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = kk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->order_by('nm_kls')
			->where(['kk.id_smt'=> $id_smt, 'j.id_sdm'=>$this->session->userdata('id_ptk')]);

		if ($id_rps == 'NIL') {
			$this->db->where('mk.id_mk', 'NIL');
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

	public function get_datatables($id_rps)
	{
		$this->_get_datatables_query($id_rps);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($id_rps)
	{
		$this->_get_datatables_query($id_rps);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_rps)
	{
		$id_smt = $this->session->userdata('id_smt');
		$this->db
			->select('mk.id_mk, kk.nm_kls, mk.kode_mk, mk.nm_mk,  kk.sks_mk,s.nm_lemb, s.nm_prodi_english')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->order_by('nm_kls')
			->where('kk.id_smt', $id_smt);

		/* if ($id_jadwal <> NULL) {
			$this->db->where('id_jadwal', $id_jadwal);
		} else {
			$this->db->where('id_jadwal', '');
		} */

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
		$this->db->insert('siakad_rps_mk_prodi', $data);
		return $this->db->insert_id();
	}

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
			for ($i = 1; $i <= 15; $i++) {
				$jumlah_hadir += $value['meet_' . $i] == 'Hadir' ? 1 : 0;
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

	public function update_single($where, $data)
	{
		$this->db->update('siakad_absen_detail_baru', $data, $where);

		$id_jadwal = $this->session->userdata('id_kls_session');
		$rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])
			->get('siakad_rencana_pertemuan');
		$absen_detail = $this->db->where($where)->get('siakad_absen_detail_baru')->row_array();
		$jumlah_hadir = 0;
		for ($i = 1; $i <= 15; $i++) {
			$jumlah_hadir += $absen_detail['meet_' . $i] == 'Hadir' ? 1 : 0;
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
				$jumlah_hadir += $value['meet_' . $i] == 'Hadir' ? 1 : 0;
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
			$jumlah_hadir += $absen_detail['meet_' . $i] == 'Hadir' ? 1 : 0;
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

	public function delete_mk_prodi($id_rps, $id_mk)
	{
		$this->db->where(['id_rps' => $id_rps, 'id_mk' => $id_mk]);
		$this->db->delete('siakad_rps_mk_prodi');
	}

	public function f_dropdown_rps()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$result = $this->db->from('siakad_rps_online o')->join('siakad_rps_online_detail d', 'o.id_rps = d.id_rps')->where(compact('id_ptk'))->get();

		$dd['NIL'] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$dd[$row['id_rps']] = $row['general_mk'];
			}
		}
		return $dd;
	}
}
