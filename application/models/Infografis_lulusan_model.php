<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Infografis_lulusan_model extends CI_Model
{
    public function get_prodi()
    {
        $this->db->select('id_sms, nm_lemb');
        $this->db->order_by('nm_lemb', 'ASC');
        return $this->db->get('sms')->result();
    }

    public function get_kpi_stats($id_sms = null, $batas_waktu = 4)
    {
        $this->db->select("
            COUNT(*) as total_lulusan,
            SUM(CASE WHEN (YEAR(f.tanggal_keluar) - CAST(f.angkatan AS UNSIGNED)) <= " . (int)$batas_waktu . " THEN 1 ELSE 0 END) as lulus_tepat_waktu,
            SUM(CASE WHEN t.predikat = 'Dengan Pujian' THEN 1 ELSE 0 END) as total_cumlaude,
            AVG(YEAR(f.tanggal_keluar) - CAST(f.angkatan AS UNSIGNED)) as rata_masa_studi
        ");
        $this->db->from('feeder_lulus_do f');
        $this->db->join('transkrip_master_2020 t', 'f.nim = t.nipd', 'left');
        $this->db->where('f.id_jns_keluar', 1);
        if (!empty($id_sms)) {
            $this->db->where('f.id_sms', $id_sms);
        }
        return $this->db->get()->row();
    }

    public function get_chart_demografi($id_sms = null)
    {
        $this->db->select("s.nm_lemb, COUNT(f.id_pd) as jumlah");
        $this->db->from('feeder_lulus_do f');
        $this->db->join('sms s', 'f.id_sms = s.id_sms', 'left');
        $this->db->where('f.id_jns_keluar', 1);
        if (!empty($id_sms)) {
            $this->db->where('f.id_sms', $id_sms);
        }
        $this->db->group_by('s.nm_lemb');
        return $this->db->get()->result();
    }

    public function get_chart_ipk($id_sms = null)
    {
        $this->db->select("
            SUM(CASE WHEN t.predikat = 'Dengan Pujian' THEN 1 ELSE 0 END) as cumlaude,
            SUM(CASE WHEN t.predikat = 'Sangat Memuaskan' THEN 1 ELSE 0 END) as sangat_memuaskan,
            SUM(CASE WHEN t.predikat = 'Memuaskan' THEN 1 ELSE 0 END) as memuaskan,
            SUM(CASE WHEN t.predikat NOT IN ('Dengan Pujian', 'Sangat Memuaskan', 'Memuaskan') OR t.predikat IS NULL THEN 1 ELSE 0 END) as kurang
        ");
        $this->db->from('feeder_lulus_do f');
        $this->db->join('transkrip_master_2020 t', 'f.nim = t.nipd', 'left');
        $this->db->where('f.id_jns_keluar', 1);
        if (!empty($id_sms)) {
            $this->db->where('f.id_sms', $id_sms);
        }
        return $this->db->get()->row();
    }

    public function get_sorotan_prodi($batas_waktu = 4)
    {
        $this->db->select("
            s.nm_lemb,
            COUNT(*) as total,
            SUM(CASE WHEN (YEAR(f.tanggal_keluar) - CAST(f.angkatan AS UNSIGNED)) <= " . (int)$batas_waktu . " THEN 1 ELSE 0 END) as tepat_waktu
        ");
        $this->db->from('feeder_lulus_do f');
        $this->db->join('sms s', 'f.id_sms = s.id_sms', 'left');
        $this->db->where('f.id_jns_keluar', 1);
        $this->db->group_by('f.id_sms, s.nm_lemb');
        $this->db->having('MIN(YEAR(f.tanggal_keluar)) <=', 2023);
        return $this->db->get()->result();
    }

    public function get_trend_kpi($id_sms = null, $batas_waktu = 4)
    {
        $this->db->select("
            YEAR(tanggal_keluar) as tahun,
            COUNT(*) as total_lulusan,
            AVG(YEAR(tanggal_keluar) - CAST(angkatan AS UNSIGNED)) as rata_masa_studi,
            SUM(CASE WHEN (YEAR(tanggal_keluar) - CAST(angkatan AS UNSIGNED)) <= " . (int)$batas_waktu . " THEN 1 ELSE 0 END) as tepat_waktu,
            SUM(CASE WHEN (YEAR(tanggal_keluar) - CAST(angkatan AS UNSIGNED)) > " . (int)$batas_waktu . " THEN 1 ELSE 0 END) as tidak_tepat_waktu
        ");
        $this->db->from('feeder_lulus_do');
        $this->db->where('id_jns_keluar', 1);
        if (!empty($id_sms)) {
            $this->db->where('id_sms', $id_sms);
        }
        $this->db->where('YEAR(tanggal_keluar) >=', date('Y') - 5);
        $this->db->group_by('YEAR(tanggal_keluar)');
        $this->db->order_by('tahun', 'ASC');
        
        $result = $this->db->get()->result();
        
        $current_year = date('Y');
        $years = [];
        for ($i = 5; $i >= 0; $i--) {
            $years[] = $current_year - $i;
        }
        
        $trend_lulusan = array_fill_keys($years, 0);
        $trend_studi = array_fill_keys($years, 0);
        $trend_tepat = array_fill_keys($years, 0);
        $trend_tidak_tepat = array_fill_keys($years, 0);
        
        foreach ($result as $row) {
            if (in_array($row->tahun, $years)) {
                $trend_lulusan[$row->tahun] = (int)$row->total_lulusan;
                $trend_studi[$row->tahun] = round((float)$row->rata_masa_studi, 1);
                $trend_tepat[$row->tahun] = (int)$row->tepat_waktu;
                $trend_tidak_tepat[$row->tahun] = (int)$row->tidak_tepat_waktu;
            }
        }
        
        return (object)[
            'categories' => array_values($years),
            'lulusan' => array_values($trend_lulusan),
            'masa_studi' => array_values($trend_studi),
            'tepat_waktu' => array_values($trend_tepat),
            'tidak_tepat_waktu' => array_values($trend_tidak_tepat)
        ];
    }

    public function get_mahasiswa_berisiko($id_sms = null)
    {
        $this->db->select("COUNT(mp.id_pd) as total_berisiko");
        $this->db->from('mahasiswa_pt mp');
        $this->db->where('mp.id_jns_keluar IS NULL');
        $this->db->where('(YEAR(CURDATE()) - YEAR(mp.tgl_masuk_sp)) >', 4);
        if (!empty($id_sms)) {
            $this->db->where('mp.id_sms', $id_sms);
        }
        $row = $this->db->get()->row();
        return $row ? (int)$row->total_berisiko : 0;
    }

    public function get_potensi_yudisium($id_sms = null)
    {
        $this->db->select('sn.nipd as nim, m.nm_pd, s.nm_lemb as prodi, m.foto, sn.sidang_nilai_akhir, sn.sidang_tanggal');
        $this->db->from('siakad_skripsi_nilai_sempro sn');
        $this->db->join('mahasiswa_pt mp', 'mp.nipd = sn.nipd', 'left');
        $this->db->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left');
        $this->db->join('sms s', 's.id_sms = mp.id_sms', 'left');
        $this->db->where('sn.sidang_hasil', 'lulus');
        $this->db->where("mp.id_jns_keluar IS NULL", NULL, FALSE);
        if (!empty($id_sms)) {
            $this->db->where('mp.id_sms', $id_sms);
        }
        $this->db->group_by('sn.nipd');
        return $this->db->get()->result();
    }
}
