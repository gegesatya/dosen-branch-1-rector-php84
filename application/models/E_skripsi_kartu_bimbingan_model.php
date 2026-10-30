<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_kartu_bimbingan_model extends CI_Model
{
    public function get_all($nipd)
    {
        $judul_skripsi = $this->db->where(['nipd' => $nipd, 'nomor_sk <>' => ''])->get('siakad_skripsi_pengajuan_judul')->row()->id_judul_skripsi;

        $this->datatables->select('id_bimbingan, tanggal_bimbingan, materi_bimbingan, html_respon')
            ->from('siakad_skripsi_kartu_bimbingan')
            ->where(['id_judul_skripsi' => $judul_skripsi]);
        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_respon($1)" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button>',
            'id_bimbingan'
        );

        return $this->datatables->generate();
    }



    public function get_by_id($id)
    {
        $this->db->from('siakad_skripsi_kartu_bimbingan');
        $this->db->where('id_bimbingan', $id);
        $query = $this->db->get();

        $result = $this->db->where('id_bimbingan', $id)->get('siakad_skripsi_kartu_bimbingan_detail')->result();
        $item = '';

        foreach ($result as $key => $value) {
            $tgl = date('Y-m-d', strtotime($value->respon_date));
            $jam = date('H:i', strtotime($value->respon_date));
            $item .= "<div class='item' style='color: #000 !important;'>
                        <div class='item-head'>
                            <div class='item-details'>
                                <a href='' class='item-name primary-link'>" . $value->respon_by . "</a>
                                <span class='item-label'>" . tgl_indo_short($tgl) . ", " . $jam . "</span>
                            </div>
                        </div>
                        <div class='item-body'>
                        " . $value->respon_bimbingan . "
                        </div>
                    </div>";
        }
        return [$query->row(), $item];
    }

    public function get_topik()
    {
        $id_sms = $this->session->userdata('mahasiswa_pt')->id_sms;
        $result = $this->db->order_by('topik_skripsi')->where(compact('id_sms'))->get('siakad_skripsi_topik');
        return $result->result();
    }
}
