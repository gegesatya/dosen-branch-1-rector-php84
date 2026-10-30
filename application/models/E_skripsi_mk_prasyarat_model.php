<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_mk_prasyarat_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $this->datatables->select('id_mk_prasyarat, mk.kode_mk, mk.nm_mk, mk.sks_mk');
        $this->datatables->from('siakad_skripsi_mk_prasyarat mkp')
            ->join('feeder_mata_kuliah mk', 'mk.id_mk = mkp.id_mk')
            ->where(['mk.id_sms' => $id_sms]);

        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_daftar_mk_prasyarat($1)" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>  
            <button onclick="hapus_mk_prasyarat($1)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>',
            'id_mk_prasyarat, mk.kode_mk, mk.nm_mk, mk.sks_mk'
        );
        return $this->datatables->generate();
    }

    public function get_by_id($id)
    {
        $this->db->from('siakad_skripsi_dosen');
        $this->db->where('id_daftar_dosen', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_mk()
    {
        $id_smt_now = $this->session->userdata('tahun_ajaran');
        $id_smt_skripsi = substr($id_smt_now, 0, 4) - 3;
        $id_smt_skripsi = $id_smt_skripsi . '1';
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->distinct()->select('mk.id_mk, kode_mk, nm_mk')
            ->from('feeder_mata_kuliah mk')
            ->join('feeder_kelas_kuliah kk', 'kk.id_mk = mk.id_mk')
            ->order_by('kode_mk')
            ->where(['kk.id_smt >' => $id_smt_skripsi, 'kk.id_sms' => $id_sms]);
        return $result->get()->result();
    }
}
