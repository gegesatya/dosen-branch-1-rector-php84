<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_topik_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $this->datatables->select('id_topik, topik_skripsi, deskripsi');
        $this->datatables->from('siakad_skripsi_topik')->where(compact('id_sms'));
        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_topik($1)" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>  
            <button onclick="hapus_topik($1)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>',
            'id_topik'
        );
        return $this->datatables->generate();
    }

    public function get_by_id($id)
    {
        $this->db->from('siakad_skripsi_topik');
        $this->db->where('id_topik', $id);
        $query = $this->db->get();
        return $query->row();
    }
}
