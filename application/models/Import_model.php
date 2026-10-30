<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Import Model
 *
 * @author TechArise Team
 *
 * @email  info@techarise.com
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Import_model extends CI_Model
{

    private $_batchImport;

    public function setBatchImport($batchImport)
    {
        $this->_batchImport = $batchImport;
    }

    // save data
    public function importData($tipe = NULL)
    {

        $data = $this->_batchImport;

        if ($tipe == 'insert') {
            $this->db->insert_batch('siakad_rencana_pertemuan', $data);
        } else {
            $this->db->update_batch('siakad_nilai_baru', $data, 'token_excel');
        }
    }


    // get employee list
    public function nilai_list()
    {
        $id_kls = $this->session->userdata('id_kls');
        $nilai = $this->db->select('*')
            ->from('siakad_nilai_baru sn')
            ->join('mahasiswa_pt mp', 'mp.id_reg_pd = sn.id_reg_pd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->where('id_kls', $id_kls)
            ->order_by('right(nipd,4)')
            ->get()
            ->result();
        return $nilai;
    }

    public function importDataAbsen($tipe = NULL, $id_jadwal = null)
    {

        $data = $this->_batchImport;

        if ($tipe == 'insert') {
            //
        } else {
            $this->db->update_batch('siakad_absen_detail_baru', $data, 'uniq');


            $rencana_pertemuan = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan');
            $absen_detail = $this->db->where(['id_jadwal' => $id_jadwal])->get('siakad_absen_detail_baru')->result_array();
            $data = [];

            foreach ($absen_detail as $key => $value) {
                $uniq = $value['uniq'];
                $jumlah_hadir = 0;
                for ($i = 1; $i <= $rencana_pertemuan->num_rows(); $i++) {
                    $jumlah_hadir += $value['meet_' . $i] == 'H' ? 1 : 0;
                }

                if ($rencana_pertemuan->num_rows() == 0) {
                    $persen_kehadiran = 0;
                } else {
                    $persen_kehadiran = round($jumlah_hadir / $rencana_pertemuan->num_rows() * 100, 2);
                }

                $data[] = ['uniq' => $uniq, 'persen_hadir' => $persen_kehadiran];
            }

            $data = $this->security->xss_clean($data);
            $this->db->update_batch('siakad_absen_detail_baru', $data, 'uniq');
        }
    }
}
