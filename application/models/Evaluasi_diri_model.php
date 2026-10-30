<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Evaluasi_diri_model extends CI_Model
{

	public $table = 'feeder_kelas_kuliah';
	public $column = ['tag_name'];
	public $column2 = ['nm_pd'];
	public $order = ['id_smt' => 'asc'];
	public $order2 = ['nm_pd' => 'asc'];

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['ion_auth']);
	}

	public function tahun_ajaran_aktif()
	{
		return $this->db->where('uniq', 1)->get('siakad_setting')->row()->tahun_ajaran_aktif;
	}

	private function _get_datatables_query()
	{
		$user = $this->ion_auth->user()->row();
		$this->db->select('siakad_jadwal.*, (SELECT COUNT(*) FROM siakad_nilai_baru WHERE siakad_nilai_baru.id_jadwal = siakad_jadwal.id) as jumlah_peserta, (SELECT cpmk FROM siakad_evaluasi_diri WHERE siakad_evaluasi_diri.id_jadwal = siakad_jadwal.id LIMIT 1) as cpmk');
		$this->db->from('siakad_jadwal');
		$this->db->where(['id_smt' => $this->session->userdata('tahun_ajaran'), 'id_sdm' => $user->id_ptk]);
		$this->db->where_not_in('id_kategori_jadwal', [2, 3, 4]);


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
		$user = $this->ion_auth->user()->row();
		$this->db->from('siakad_jadwal');
		$this->db->where(['id_smt' => $this->session->userdata('tahun_ajaran'), 'id_sdm' => $user->id_ptk]);
		$this->db->where_not_in('id_kategori_jadwal', [2, 3, 4]);
		return $this->db->count_all_results();
	}

	public function get_by_id($id_jadwal)
	{
		$user = $this->ion_auth->user()->row();
		$this->db->from('siakad_evaluasi_diri');
		$this->db->where('id_jadwal', $id_jadwal);


		/* $this->db->from('siakad_evaluasi_diri');
		$this->db->join('feeder_kelas_kuliah', 'feeder_kelas_kuliah.id_kls = siakad_evaluasi_diri.id_kls');
		$this->db->join('feeder_mata_kuliah', 'feeder_mata_kuliah.id_mk = feeder_kelas_kuliah.id_mk');
		$this->db->join('siakad_dosen', 'siakad_dosen.id_ptk = feeder_kelas_kuliah.id_ptk');
		$this->db->join('sms', 'sms.id_sms = feeder_kelas_kuliah.id_sms');
		$this->db->where(array('siakad_evaluasi_diri.id_kls' => $id_kls, 'siakad_evaluasi_diri.id_smt' => $id_smt)); */
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('siakad_evaluasi_diri', $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update('siakad_evaluasi_diri', $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function close_by_id($id)
	{
		$this->db->where('uniq', $id)->update('konfirmasimarketing', ['is_closed' => 'Yes']);
		return $this->db->affected_rows();
	}

	public function get_sekolah()
	{
		return $this->db->order_by('nama_Sekolah')->get('tblsekolah')->result();
	}
}
