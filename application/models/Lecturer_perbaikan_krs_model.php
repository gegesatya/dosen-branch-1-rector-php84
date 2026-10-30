<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_perbaikan_krs_model extends CI_Model
{
	public $table = 'siakad_krs';
	public $column = ['nm_pd', 'siakad_krs_perbaikan.nipd'];
	public $order = ['nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query($angkatan = NULL)
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		//$user_logins = $this->ion_auth->user()->row();

		$this->db->from('siakad_krs_perbaikan')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_krs_perbaikan.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where(['siakad_krs_perbaikan.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk')]);

		if ($angkatan <> 0) {
			$this->db->where_in('year(mahasiswa_pt.tgl_masuk_sp)', $angkatan);
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

	public function get_datatables($angkatan = NULL)
	{
		$this->_get_datatables_query($angkatan);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered($angkatan)
	{
		$this->_get_datatables_query($angkatan);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($angkatan)
	{
		$user_logins = $this->ion_auth->user()->row();
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;

		$this->db->from('siakad_krs_perbaikan')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_krs_perbaikan.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
			->where(['siakad_krs_perbaikan.id_smt' => $id_smt, 'mahasiswa.dosen_pa' => $this->session->userdata('id_ptk')]);

		if ($angkatan <> 0) {
			$this->db->where_in('year(mahasiswa_pt.tgl_masuk_sp)', $angkatan);
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

	public function f_status_krs()
	{
		$result = $this->db->where_in('id_status_krs', [3, 4, 1])
			->get('siakad_status_krs');

		$dd[''] = 'Please Select';
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				if ($row['id_status_krs'] == 1) {
					$row['nm_status_krs'] = 'Dikembalikan ke status Daftar Perbaikan KRS';
				}
				$dd[$row['id_status_krs']] = $row['nm_status_krs'];
			}
		}
		return $dd;
	}

	/*function get_paket_krs(){
    	$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
    	$id_sms = $this->session->userdata('attribut_mahasiswa')->id_sms;

    	$result = $this->db->where(['id_smt'=>$id_smt, 'id_sms'=>$id_sms])
    					->select('*')->from('siakad_paket_krs')
    					->order_by('nm_paket_krs')->get();

        $dd[''] = 'Please Select';
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $dd[$row['id']] = $row['nm_paket_krs'];
            }
        }
        return $dd;
    }*/
}
