<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Infografis_kerjasama_model extends CI_Model
{
    public function get_available_years()
    {
        $this->db->select('MIN(YEAR(tanggal_awal)) as min_year, MAX(YEAR(tanggal_awal)) as max_year');
        $this->db->where('tanggal_awal >', '1000-01-01');
        $this->db->where('tanggal_awal IS NOT NULL');
        $query1 = $this->db->get('simlapkerma_tbl_daftar_mou')->row();

        $this->db->select('MIN(YEAR(tanggal_awal_moa)) as min_year, MAX(YEAR(tanggal_awal_moa)) as max_year');
        $this->db->where('tanggal_awal_moa >', '1000-01-01');
        $this->db->where('tanggal_awal_moa IS NOT NULL');
        $query2 = $this->db->get('simlapkerma_tbl_moa')->row();

        $min_year = min((int)$query1->min_year ?: date('Y'), (int)$query2->min_year ?: date('Y'));
        $max_year = max((int)$query1->max_year ?: date('Y'), (int)$query2->max_year ?: date('Y'));
        
        if ($min_year == 0) $min_year = date('Y');
        if ($max_year == 0) $max_year = date('Y');

        $years = [];
        for ($y = $max_year; $y >= $min_year; $y--) {
            $years[] = $y;
        }
        return $years;
    }

    public function get_global_stats($tahun = 'All')
    {
        // Total MOU
        $this->db->from('simlapkerma_tbl_daftar_mou');
        if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal)', $tahun);
        $total_mou = $this->db->count_all_results();
        
        // Total MOA
        $this->db->from('simlapkerma_tbl_moa');
        if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal_moa)', $tahun);
        $total_moa = $this->db->count_all_results();

        // Total Instansi (dari tabel master instansi)
        $this->db->from('simlapkerma_tbl_daftar_instansi');
        if ($tahun != 'All') $this->db->where('YEAR(create_date)', $tahun);
        $total_instansi = $this->db->count_all_results();

        // Total Kegiatan
        $this->db->from('simlapkerma_tbl_kegiatan');
        if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal_kegiatan)', $tahun);
        $total_kegiatan = $this->db->count_all_results();

        return (object)[
            'total_mou' => $total_mou,
            'total_moa' => $total_moa,
            'total_instansi' => $total_instansi,
            'total_kegiatan' => $total_kegiatan
        ];
    }

    public function get_mitra_baru($tahun)
    {
        if ($tahun == 'All') {
            $tahun = date('Y');
        }
        $query = $this->db->query("
            SELECT COUNT(*) as total_mitra_baru
            FROM (
                SELECT id_daftar_instansi, MIN(tahun_awal) as tahun_pertama
                FROM (
                    SELECT id_daftar_instansi, YEAR(tanggal_awal) as tahun_awal 
                    FROM simlapkerma_tbl_daftar_mou 
                    WHERE tanggal_awal IS NOT NULL AND tanggal_awal > '1000-01-01'
                    UNION ALL
                    SELECT id_daftar_instansi, YEAR(tanggal_awal_moa) as tahun_awal 
                    FROM simlapkerma_tbl_moa 
                    WHERE tanggal_awal_moa IS NOT NULL AND tanggal_awal_moa > '1000-01-01'
                ) AS combined
                GROUP BY id_daftar_instansi
            ) AS first_years
            WHERE tahun_pertama = ?
        ", [$tahun]);

        return $query->row()->total_mitra_baru ?? 0;
    }

    public function get_bidang_usaha_mitra($tahun = 'All')
    {
        $this->db->select('bu.nm_bidang_usaha as nama, COUNT(DISTINCT mou.id_daftar_instansi) as total');
        $this->db->from('simlapkerma_tbl_daftar_mou mou');
        $this->db->join('simlapkerma_tbl_daftar_instansi i', 'mou.id_daftar_instansi = i.id_daftar_instansi', 'inner');
        $this->db->join('simlapkerma_tbl_bidang_usaha bu', 'i.id_bidang_usaha = bu.id_bidang_usaha', 'inner');
        if ($tahun != 'All') {
            $this->db->where('YEAR(mou.tanggal_awal)', $tahun);
        }
        $this->db->group_by('bu.id_bidang_usaha');
        $this->db->order_by('total', 'DESC');
        return $this->db->get()->result();
    }

    public function get_jenis_instansi_mitra($tahun = 'All')
    {
        $this->db->select('j.nm_jenis_instansi as nama, COUNT(DISTINCT mou.id_daftar_instansi) as total');
        $this->db->from('simlapkerma_tbl_daftar_mou mou');
        $this->db->join('simlapkerma_tbl_daftar_instansi i', 'mou.id_daftar_instansi = i.id_daftar_instansi', 'inner');
        $this->db->join('simlapkerma_tbl_sub_instansi sub', 'i.id_sub_instansi = sub.id_sub_instansi', 'left');
        $this->db->join('simlapkerma_tbl_instansi j', 'sub.id_instansi = j.id_jenis_instansi', 'left');
        
        if ($tahun != 'All') {
            $this->db->where('YEAR(mou.tanggal_awal)', $tahun);
        }
        $this->db->group_by('j.id_jenis_instansi');
        $this->db->order_by('total', 'DESC');
        
        $results = $this->db->get()->result();
        // Fallback for NULL values
        foreach ($results as &$row) {
            if (empty($row->nama)) {
                $row->nama = 'Tidak Diketahui / Lainnya';
            }
        }
        return $results;
    }

    public function get_mou_moa_trend_monthly($tahun)
    {
        if ($tahun == 'All') $tahun = date('Y');

        $mou_data = array_fill(1, 12, 0);
        $moa_data = array_fill(1, 12, 0);

        // MOU
        $this->db->select('MONTH(tanggal_awal) as bulan, COUNT(*) as total');
        $this->db->from('simlapkerma_tbl_daftar_mou');
        $this->db->where('YEAR(tanggal_awal)', $tahun);
        $this->db->group_by('MONTH(tanggal_awal)');
        $mou_query = $this->db->get()->result();
        foreach ($mou_query as $row) {
            $mou_data[(int)$row->bulan] = (int)$row->total;
        }

        // MOA
        $this->db->select('MONTH(tanggal_awal_moa) as bulan, COUNT(*) as total');
        $this->db->from('simlapkerma_tbl_moa');
        $this->db->where('YEAR(tanggal_awal_moa)', $tahun);
        $this->db->group_by('MONTH(tanggal_awal_moa)');
        $moa_query = $this->db->get()->result();
        foreach ($moa_query as $row) {
            $moa_data[(int)$row->bulan] = (int)$row->total;
        }

        return [
            'mou' => array_values($mou_data),
            'moa' => array_values($moa_data)
        ];
    }

    public function get_mou_moa_trend_yearly()
    {
        $years = $this->get_available_years();
        sort($years);
        
        $mou_data = [];
        $moa_data = [];
        $labels = [];

        foreach ($years as $year) {
            $labels[] = $year;
            $mou_data[$year] = 0;
            $moa_data[$year] = 0;
        }

        // MOU
        $this->db->select('YEAR(tanggal_awal) as tahun, COUNT(*) as total');
        $this->db->from('simlapkerma_tbl_daftar_mou');
        $this->db->where('tanggal_awal >', '1000-01-01');
        $this->db->group_by('YEAR(tanggal_awal)');
        $mou_query = $this->db->get()->result();
        foreach ($mou_query as $row) {
            if (isset($mou_data[(int)$row->tahun])) {
                $mou_data[(int)$row->tahun] = (int)$row->total;
            }
        }

        // MOA
        $this->db->select('YEAR(tanggal_awal_moa) as tahun, COUNT(*) as total');
        $this->db->from('simlapkerma_tbl_moa');
        $this->db->where('tanggal_awal_moa >', '1000-01-01');
        $this->db->group_by('YEAR(tanggal_awal_moa)');
        $moa_query = $this->db->get()->result();
        foreach ($moa_query as $row) {
            if (isset($moa_data[(int)$row->tahun])) {
                $moa_data[(int)$row->tahun] = (int)$row->total;
            }
        }

        return [
            'labels' => $labels,
            'mou' => array_values($mou_data),
            'moa' => array_values($moa_data)
        ];
    }

    public function get_top_instansi_kegiatan($tahun = 'All')
    {
        $this->db->select('i.nm_instansi, COUNT(k.id_kegiatan) as total_kegiatan');
        $this->db->from('simlapkerma_tbl_kegiatan k');
        $this->db->join('simlapkerma_tbl_daftar_instansi i', 'k.id_daftar_instansi = i.id_daftar_instansi', 'inner');
        if ($tahun != 'All') {
            $this->db->where('YEAR(k.tanggal_awal_kegiatan)', $tahun);
        }
        $this->db->group_by('i.id_daftar_instansi');
        $this->db->order_by('total_kegiatan', 'DESC');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    public function get_mitra_details($tahun = 'All')
    {
        $this->db->select("
            i.id_daftar_instansi,
            i.nm_instansi,
            j.nm_jenis_instansi,
            bu.nm_bidang_usaha
        ");
        $this->db->from('simlapkerma_tbl_daftar_instansi i');
        $this->db->join('simlapkerma_tbl_sub_instansi sub', 'i.id_sub_instansi = sub.id_sub_instansi', 'left');
        $this->db->join('simlapkerma_tbl_instansi j', 'sub.id_instansi = j.id_jenis_instansi', 'left');
        $this->db->join('simlapkerma_tbl_bidang_usaha bu', 'i.id_bidang_usaha = bu.id_bidang_usaha', 'left');
        
        if ($tahun != 'All') {
            $this->db->where('YEAR(i.create_date)', $tahun);
        }
        
        $this->db->order_by('i.nm_instansi', 'ASC');
        $instansi_list = $this->db->get()->result();

        // Calculate counts individually to respect year filters on child tables if year is 'All' or not
        foreach ($instansi_list as &$mitra) {
            $this->db->where('id_daftar_instansi', $mitra->id_daftar_instansi);
            if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal)', $tahun);
            $mitra->jml_mou = $this->db->count_all_results('simlapkerma_tbl_daftar_mou');

            $this->db->where('id_daftar_instansi', $mitra->id_daftar_instansi);
            if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal_moa)', $tahun);
            $mitra->jml_moa = $this->db->count_all_results('simlapkerma_tbl_moa');

            $this->db->where('id_daftar_instansi', $mitra->id_daftar_instansi);
            if ($tahun != 'All') $this->db->where('YEAR(tanggal_awal_kegiatan)', $tahun);
            $mitra->jml_kegiatan = $this->db->count_all_results('simlapkerma_tbl_kegiatan');
        }

        return $instansi_list;
    }
}
