<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_arsip_kaprodi_model extends CI_Model
{
    public $table = 'siakad_skripsi_pengajuan_judul';
    public $column = ['mpt.nipd', 'm.nm_pd', 'd1.nama_lengkap', 'pj.judul_ditetapkan'];
    public $order = ['pj.tanggal_sk' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $id_sms = $this->session->userdata('prodi');

        $this->db->select('pj.*, m.nm_pd, d1.nama_lengkap as nm_pembimbing_1')
                 ->from('siakad_skripsi_pengajuan_judul pj')
                 ->join('mahasiswa_pt mpt', 'mpt.nipd = pj.nipd', 'left')
                 ->join('mahasiswa m', 'm.id_pd = mpt.id_pd', 'left')
                 ->join('siakad_skripsi_dosen d1', 'd1.id_ptk = pj.dosen_pembimbing_1', 'left')
                 ->where(['pj.status_sk' => 3, 'pj.id_sms' => $id_sms]);

        $i = 0;
        foreach ($this->column as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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
        if (isset($_POST['length']) && $_POST['length'] != -1)
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
        $id_sms = $this->session->userdata('prodi');
        $this->db->where(['pj.status_sk' => 3, 'pj.id_sms' => $id_sms])
                 ->from('siakad_skripsi_pengajuan_judul pj');
        return $this->db->count_all_results();
    }

    public function get_dosen_list()
    {
        // Ambil semua dosen yang aktif untuk dropdown
        $this->db->select('id_ptk, nama_lengkap')
                 ->from('siakad_skripsi_dosen')
                 ->order_by('nama_lengkap', 'ASC');
        return $this->db->get()->result();
    }
}
