<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_topik_dosen_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $this->datatables->select('id_dosen_topik, nm_ptk, topik_skripsi');
        $this->datatables->from('siakad_skripsi_topik_dosen')
            ->join('siakad_dosen', 'siakad_dosen.id_ptk = siakad_skripsi_topik_dosen.id_ptk')
            ->join('siakad_skripsi_topik', 'siakad_skripsi_topik.id_topik = siakad_skripsi_topik_dosen.id_topik')
            ->where(['siakad_skripsi_topik_dosen.id_sms' => $id_sms]);

        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_topik_dosen($1)" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>  
            <button onclick="hapus_topik_dosen($1)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>',
            'id_dosen_topik'
        );
        return $this->datatables->generate();
    }

    public function get_by_id($id)
    {
        $this->db->from('siakad_skripsi_topik_dosen');
        $this->db->where('id_dosen_topik', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_dosen()
    {
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->where('d.id_sms', $id_sms)->order_by('nm_ptk')->select('*')->from('siakad_skripsi_dosen d')->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
        return $result->result();
    }

    public function get_topik()
    {
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->order_by('topik_skripsi')->where('id_sms', $id_sms)->get('siakad_skripsi_topik');
        return $result->result();
    }
}
