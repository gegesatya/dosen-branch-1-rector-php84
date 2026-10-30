<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_dosen_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $this->datatables->select('id_daftar_dosen, nm_ptk, is_pembimbing, is_penguji, nama_lengkap, jabatan_akademik');
        $this->datatables->from('siakad_skripsi_dosen')
            ->join('siakad_dosen', 'siakad_dosen.id_ptk=siakad_skripsi_dosen.id_ptk')
            ->where(['siakad_skripsi_dosen.id_sms' => $id_sms]);

        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_daftar_dosen($1)" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>  
            <button onclick="hapus_daftar_dosen($1)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>',
            'id_daftar_dosen'
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

    public function get_dosen()
    {
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->order_by('nm_ptk')->get('siakad_dosen');
        return $result->result();
    }
}
