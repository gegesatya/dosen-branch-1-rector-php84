<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_magang_model extends CI_Model
{

	public $table = 'simutu_pelaksanaan_ami';
	public $column = ['m.nipd', 'md.nm_pd', 'm.nama_perusahaan'];
	public $order = ['1' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$this->db->select("m.nipd, m.nama_perusahaan, m.nilai_rata_fasilitator, md.nm_pd, 
			GROUP_CONCAT(DISTINCT mk.nm_mk ORDER BY mk.nm_mk SEPARATOR '<br>') AS mk_terkait,
			GROUP_CONCAT(IF(nb.nilai_angka IS NOT NULL, CONCAT(nb.nilai_angka, ' (', nb.nilai_huruf, ')'), '-') ORDER BY mk.nm_mk SEPARATOR '<br>') AS nilai_dosen", FALSE);
		$this->db->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('mahasiswa md', 'md.id_pd = mp.id_pd')
			->join('siakad_nilai_baru nb', 'nb.nipd = m.nipd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = kk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->where('m.dosen_pembimbing_lapangan', $id_ptk)
			->where('kk.id_smt', $id_smt)
			->where('j.id_kategori_jadwal', 2)
			->where("(LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk.nm_mk) NOT LIKE '%kkn%')", NULL, FALSE)
			->where('nb.nilai_angka IS NULL', NULL, FALSE)
			->where('nb.nilai_huruf IS NULL', NULL, FALSE)
			->where("EXISTS (
				SELECT 1 FROM siakad_nilai_baru nb2
				JOIN feeder_kelas_kuliah kk2 ON kk2.id_kls = nb2.id_kls
				JOIN feeder_mata_kuliah mk2 ON mk2.id_mk = kk2.id_mk
				JOIN siakad_jadwal_detail jd2 ON jd2.id_kls = kk2.id_kls
				JOIN siakad_jadwal j2 ON j2.id = jd2.id_jadwal
				WHERE nb2.nipd = m.nipd
				  AND kk2.id_smt = $id_smt
				  AND j2.id_kategori_jadwal = 2
				  AND LOWER(mk2.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk2.nm_mk) NOT LIKE '%kkn%'
				  AND nb2.nilai_angka IS NULL
				  AND nb2.nilai_huruf IS NULL
			)", NULL, FALSE)
			->group_by(['m.nipd', 'm.nama_perusahaan', 'm.nilai_rata_fasilitator', 'md.nm_pd']);

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
		$id_smt = $this->session->userdata('tahun_ajaran');

		$this->db->select('m.nipd');
		$this->db->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('siakad_nilai_baru nb', 'nb.nipd = m.nipd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = kk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->where('m.dosen_pembimbing_lapangan', $id_ptk)
			->where('kk.id_smt', $id_smt)
			->where('j.id_kategori_jadwal', 2)
			->where("(LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk.nm_mk) NOT LIKE '%kkn%')", NULL, FALSE)
			->where('nb.nilai_angka IS NULL', NULL, FALSE)
			->where('nb.nilai_huruf IS NULL', NULL, FALSE)
			->where("EXISTS (
				SELECT 1 FROM siakad_nilai_baru nb2
				JOIN feeder_kelas_kuliah kk2 ON kk2.id_kls = nb2.id_kls
				JOIN feeder_mata_kuliah mk2 ON mk2.id_mk = kk2.id_mk
				JOIN siakad_jadwal_detail jd2 ON jd2.id_kls = kk2.id_kls
				JOIN siakad_jadwal j2 ON j2.id = jd2.id_jadwal
				WHERE nb2.nipd = m.nipd
				  AND kk2.id_smt = $id_smt
				  AND j2.id_kategori_jadwal = 2
				  AND LOWER(mk2.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk2.nm_mk) NOT LIKE '%kkn%'
				  AND nb2.nilai_angka IS NULL
				  AND nb2.nilai_huruf IS NULL
			)", NULL, FALSE)
			->group_by('m.nipd');

		$query = $this->db->get();
		return $query->num_rows();
	}

	public function get_list_semester($id_ptk)
	{
		$this->db->select('DISTINCT(s.id_smt), s.nm_smt', FALSE)
			->from('semester s')
			->where('s.a_periode_aktif', 1)
			->or_where("s.id_smt IN (
				SELECT DISTINCT kk.id_smt
				FROM simlapkerma_lokasi_magang m
				JOIN siakad_nilai_baru nb ON nb.nipd = m.nipd
				JOIN feeder_kelas_kuliah kk ON kk.id_kls = nb.id_kls
				WHERE m.dosen_pembimbing_lapangan = " . $this->db->escape($id_ptk) . "
			)", NULL, FALSE)
			->order_by('s.id_smt', 'desc');

		return $this->db->get()->result();
	}

	public function get_all_mahasiswa_magang($id_ptk, $id_smt)
	{
		$this->db->select("m.nipd, m.nama_perusahaan, m.nilai_rata_fasilitator, md.nm_pd, md.foto, 
			GROUP_CONCAT(DISTINCT mk.nm_mk ORDER BY mk.nm_mk SEPARATOR '<br>') AS mk_terkait,
			GROUP_CONCAT(
				DISTINCT CONCAT(
					mk.nm_mk, 
					IF(nb.nilai_angka IS NOT NULL, 
					   CONCAT(' : <span style=\"font-weight:700; color:#059669;\">', nb.nilai_angka, ' (', nb.nilai_huruf, ')</span>'), 
					   ' : <span style=\"color:#94a3b8; font-style:italic;\">Belum dinilai</span>')
				) ORDER BY mk.nm_mk SEPARATOR '<br>'
			) AS detail_mk_nilai,
			GROUP_CONCAT(IF(nb.nilai_angka IS NOT NULL, CONCAT(nb.nilai_angka, ' (', nb.nilai_huruf, ')'), '-') ORDER BY mk.nm_mk SEPARATOR '<br>') AS nilai_dosen,
			SUM(CASE WHEN nb.nilai_angka IS NOT NULL THEN 1 ELSE 0 END) AS jml_sudah_nilai,
			COUNT(DISTINCT nb.id_kls) AS total_mk,
			(SELECT COUNT(*) FROM simlapkerma_tracking_kegiatan tl WHERE tl.nipd = m.nipd AND (tl.review_dpl IS NULL OR tl.review_dpl = '')) AS jml_belum_review,
			(SELECT MAX(change_date) FROM simlapkerma_tracking_kegiatan tl WHERE tl.nipd = m.nipd AND (tl.review_dpl IS NOT NULL AND tl.review_dpl != '')) AS tgl_terakhir_review", FALSE);
		$this->db->from('simlapkerma_lokasi_magang m')
			->join('mahasiswa_pt mp', 'mp.nipd = m.nipd')
			->join('mahasiswa md', 'md.id_pd = mp.id_pd')
			->join('siakad_nilai_baru nb', 'nb.nipd = m.nipd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = nb.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('siakad_jadwal_detail jd', 'jd.id_kls = kk.id_kls')
			->join('siakad_jadwal j', 'j.id = jd.id_jadwal')
			->where('m.dosen_pembimbing_lapangan', $id_ptk);

		if (!empty($id_smt) && $id_smt !== 'all') {
			$this->db->where('kk.id_smt', $id_smt);
		}

		$this->db->where('j.id_kategori_jadwal', 2)
			->where("(LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' AND LOWER(mk.nm_mk) NOT LIKE '%kkn%')", NULL, FALSE)
			->group_by(['m.nipd', 'm.nama_perusahaan', 'm.nilai_rata_fasilitator', 'md.nm_pd', 'md.foto'])
			->order_by('md.nm_pd', 'asc');

		return $this->db->get()->result();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_pelaksanaan_ami', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_nilai_mk($id_jadwal)
	{
		$data_nilai_e = $this->db->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => 'E'])
			->from('siakad_nilai_baru nb')
			->join('mahasiswa_pt mp', 'mp.nipd = nb.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')->order_by('nm_pd')->get();
		return $data_nilai_e;
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

    // =========================================================
    // AI Chat Proposal History
    // =========================================================

    public function get_chat_history($nipd)
    {
        return $this->db
            ->where('nipd', $nipd)
            ->order_by('created_at', 'asc')
            ->get('simlapkerma_ai_chat_proposal_history')
            ->result();
    }

    public function save_chat_message($nipd, $role, $message)
    {
        $data = [
            'nipd'    => $nipd,
            'role'    => $role,
            'message' => $message,
        ];
        $this->db->insert('simlapkerma_ai_chat_proposal_history', $data);
        return $this->db->insert_id();
    }

    public function delete_chat_history($nipd)
    {
        $this->db->where('nipd', $nipd);
        $this->db->delete('simlapkerma_ai_chat_proposal_history');
        return $this->db->affected_rows();
    }
}
