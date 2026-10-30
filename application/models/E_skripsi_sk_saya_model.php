<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_sk_saya_model extends CI_Model
{
    public function get_all()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $this->datatables->select('id_judul_skripsi, mahasiswa_pt.nipd, nm_pd,tanggal_diajukan, judul_ditetapkan, tanggal_sk, nomor_sk');
        $this->datatables->from('siakad_skripsi_pengajuan_judul')
            ->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd')
            ->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd')
            ->where(['dosen_pembimbing_1' => $id_ptk, 'status_sk' => 1])
            ->or_where('dosen_pembimbing_2', $id_ptk);
        $this->datatables->add_column(
            'view',
            '<a href="' . site_url('E_skripsi_sk_saya/cetak_pdf/$1') . '" target="new" class="btn btn-xs btn-default green-stripe">
						<i class="fa fa-print"></i></a>',
            'id_judul_skripsi'
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
