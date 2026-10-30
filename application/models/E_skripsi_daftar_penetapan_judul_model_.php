<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_penetapan_judul_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $this->datatables->select('j.id_judul_skripsi, j.tanggal_diajukan, j.judul_skripsi_1, j.judul_skripsi_2, 
                                    j.judul_skripsi_3, j.nipd, m.nm_pd, analisis_ipk, analisis_sks_total, 
                                    analisis_sks_lulus, analisis_sks_tidak_lulus,sd.nm_ptk as pembimbing_1, sd1.nm_ptk as pembimbing_2,
                                    judul_ditetapkan, status_penetapan_judul, sd2.nm_ptk as penguji, sd3.nm_ptk as penguji_2');
        $this->datatables->from('siakad_skripsi_pengajuan_judul j')
            ->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
            ->join('siakad_skripsi_analisis a', 'a.nipd = j.nipd', 'left')
            ->join('siakad_dosen sd', 'sd.id_ptk = j.dosen_pembimbing_1', 'LEFT')
            ->join('siakad_dosen sd1', 'sd1.id_ptk = j.dosen_pembimbing_2', 'LEFT')
            ->join('siakad_dosen sd2', 'sd2.id_ptk = j.dosen_penguji', 'LEFT')
            ->join('siakad_dosen sd3', 'sd3.id_ptk = j.dosen_penguji_2', 'LEFT')
            ->where(['j.id_sms' => $id_sms]);

        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="edit_pengajuan_judul($1)" class="btn btn-default btn-xs blue-stripe"><i class="fa fa-edit"></i></button>
            <button type="button" onclick="comment($1)" class="btn btn-default btn-xs green-stripe"><i class="fa fa-comment"></i></button>',
            'id_judul_skripsi'
        );
        return $this->datatables->generate();
    }

    public function get_by_id($id)
    {
        $this->db->from('siakad_skripsi_pengajuan_judul j')
            ->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
            ->join('siakad_skripsi_analisis a', 'a.nipd = j.nipd', 'left')
            ->join('sms s', 's.id_sms = j.id_sms')
            ->where(['j.id_judul_skripsi' => $id]);
        $query = $this->db->get();

        $nipd = $query->row()->nipd;
        $dosen_wali = $this->db->where('nipd', $nipd)->from('mahasiswa_pt mp')->join('mahasiswa m', 'm.id_pd = mp.id_pd')->get()->row()->dosen_pa;
        $nm_ptk = $this->db->where('id_ptk', $dosen_wali)->get('siakad_dosen')->row()->nm_ptk;

        $tabel = "<table class='table table-bordered'>
                    <tbody>
                        <tr><td style='width:200px'><strong>NIM</strong></td><td>" . $query->row()->nipd . "</td></tr>
                        <tr><td><strong>Nama</strong></td><td>" . $query->row()->nm_pd . "</td></tr>
                        <tr><td><strong>Alamat di Medan</strong></td><td>" . $query->row()->alamat_medan . "</td></tr>
                        <tr><td><strong>Alamat Asal</strong></td><td>" . $query->row()->alamat_asal . "</td></tr>
                        <tr><td><strong>No HP</strong></td><td>" . $query->row()->no_handphone . "</td></tr>
                        <tr><td><strong>Email</strong></td><td>" . $query->row()->email_pribadi . "</td></tr>
                        <tr><td><strong>Program Studi</strong></td><td>" . $query->row()->nm_lemb . "</td></tr>
                        <tr><td><strong>Dosen Wali</strong></td><td>" . $nm_ptk . "</td></tr>
                        <tr><td><strong>IP Kumulatif</strong></td><td>" . $query->row()->analisis_ipk . "</td></tr>
                        <tr><td><strong>Total SKS</strong></td><td>" . $query->row()->analisis_sks_total . "</td></tr>
                        <tr><td><strong>SKS Tidak Lulus</strong></td><td>" . $query->row()->analisis_sks_tidak_lulus . "</td></tr>
                        <tr><td><strong>Judul A</strong></td><td><span name='judul_a'>" . $query->row()->judul_skripsi_1 . "</span> <button class='btn btn-xs btn-primary' onclick='copy_a()'> Copy</button></td></tr>
                        <tr><td><strong>Judul B</strong></td><td><span name='judul_b'>" . $query->row()->judul_skripsi_2 . "</span> <button class='btn btn-xs btn-primary' onclick='copy_b()'> Copy</button></td></tr>
                        <tr><td><strong>Judul C</strong></td><td><span name='judul_c'>" . $query->row()->judul_skripsi_3 . "</span> <button class='btn btn-xs btn-primary' onclick='copy_c()'> Copy</button></td></tr>
                        <tr><td><strong>Target Selesai</strong></td><td>" . $query->row()->target_selesai . "</td></tr>
                        <tr><td><strong>Semester Pengajuan</strong></td><td>" . $query->row()->id_smt . "</td></tr>
                        <tr><td><strong>Tgl Pengajuan</strong></td><td>" . $query->row()->tanggal_diajukan . "</td></tr>
                    </tbody>
                </table>";


        return [$query->row(), $nm_ptk, $tabel];
    }

    public function get_dosen()
    {
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->where(['d.id_sms' => $id_sms, 'is_pembimbing' => 1])->order_by('nm_ptk')
            ->select('*')->from('siakad_skripsi_dosen d')
            ->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
        return $result->result();
    }

    public function get_dosen_penguji()
    {
        $id_sms = $this->session->userdata('prodi');
        $result = $this->db->where(['d.id_sms' => $id_sms, 'is_penguji' => 1])->order_by('nm_ptk')
            ->select('*')->from('siakad_skripsi_dosen d')
            ->join('siakad_dosen sd', 'sd.id_ptk = d.id_ptk')->get();
        return $result->result();
    }
}
