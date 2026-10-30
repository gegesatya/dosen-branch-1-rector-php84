<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_mahasiswa_model extends CI_Model
{
    public function get_all()
    {
        $id_sms = $this->session->userdata('prodi');
        $id_smt = $this->session->userdata('setting')->id_smt_tunda;

        $this->datatables->select('mp.nipd, nm_pd, s.analisis_sks_total, s.analisis_ipk,
                                    s.analisis_sks_lulus, s.analisis_sks_tidak_lulus, s.is_boleh_ajukan_judul');
        $this->datatables->from('mahasiswa m')
            ->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
            ->join('kuliah_mahasiswa an', 'an.id_reg_pd = mp.id_reg_pd_feeder')
            ->join('siakad_skripsi_analisis s', 's.nipd = mp.nipd')
            ->where([
                'mp.id_sms' => $id_sms,
                'id_smt' => $id_smt,
                'sks_total >=' => 120
            ]);

        $this->datatables->add_column(
            'view',
            '<button type="button" onclick="view_mahasiswa($1)" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button>',
            'nipd'
        );

        $this->datatables->add_column(
            'view_makul',
            '<a onclick="view_makul($1)" class="btn btn-primary btn-xs"><i class="fa fa-graduation-cap"></i></a>',
            'nipd, analisis_sks_tidak_lulus'
        );

        return $this->datatables->generate();
    }

    public function get_by_id($nipd)
    {
        $id_smt = $this->session->userdata("tahun_ajaran");

        $mahasiswa = $this->db->where('nipd', $nipd)
            ->select('m.nm_pd, mp.nipd, mp.id_reg_pd, mp.id_reg_pd_feeder')
            ->from('mahasiswa m')
            ->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
        $id_reg_pd = $mahasiswa->id_reg_pd;
        $id_reg_pd_feeder = $mahasiswa->id_reg_pd_feeder;

        $nilai_transfer =  $this->db->where(['id_reg_pd' => $id_reg_pd_feeder])
            ->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk')
            ->from('nilai_transfer n')
            ->join('feeder_mata_kuliah mk', 'mk.id_mk = n.id_mk')
            ->get();

        $nilai_mahasiswa_1 = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt <=' => $id_smt])
            ->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
            ->from('arsip_nilai n')
            ->join('arsip_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
            ->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
            ->order_by('id_smt')
            ->get();

        $nilai_mahasiswa_2 = $this->db->where(['nipd' => $nipd, 'id_smt <=' => $id_smt])
            ->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
            ->from('siakad_nilai_baru n')
            ->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
            ->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
            ->order_by('id_smt')
            ->get();

        $this->load->library('table');
        $table = "";
        $tmpl = [
            'table_open'          => '<table class="table table-bordered">',
            'thead_open'          => '<thead>',
            'thead_close'         => '</thead>',
            'heading_row_start'   => '<tr>',
            'heading_row_end'     => '</tr>',
            'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
            'heading_cell_end'    => '</th>',
            'row_start'           => '<tr>',
            'row_end'             => '</tr>',
            'cell_start'          => '<td>',
            'cell_end'            => '</td>',
            'row_alt_start'       => '<tr>',
            'row_alt_end'         => '</tr>',
            'cell_alt_start'      => '<td>',
            'cell_alt_end'        => '</td>',
            'table_close'         => '</table>'
        ];

        $this->table->set_template($tmpl);
        $this->table->set_heading(['No', 'SMT', 'Kode/<br>Code', 'Mata Kuliah/Subjects', 'Lambang/<br>Mark', 'Mutu/<br>Quality', 'SKS/<br>Credit', 'Credit <br>X<br> Quality']);

        $i = 1;

        $jumlah = $jumlah_sks_mk = $jumlah_sks_lulus = $jumlah_sks_tidak_lulus = 0;
        $makul_tidak_lulus = [];

        foreach ($nilai_transfer->result() as $l_nilai) {
            $hasil = $l_nilai->sks_diakui * $l_nilai->nilai_angka_diakui;
            $this->table->add_row([
                ['data' => $i++ . '.', 'class' => 'text-right'],
                'Transfer',
                $l_nilai->kode_mk,
                $l_nilai->nm_mk,
                ['data' => $l_nilai->nilai_huruf_diakui, 'class' => 'text-center'],
                ['data' => $l_nilai->nilai_angka_diakui, 'class' => 'text-center'],
                ['data' => (int) $l_nilai->sks_diakui, 'class' => 'text-center'],
                ['data' => $hasil, 'class' => 'text-center']
            ]);

            $jumlah += $hasil;
            if (!empty($l_nilai->nilai_huruf_diakui)) {
                $jumlah_sks_mk += (int) $l_nilai->sks_diakui;
            }

            if ($l_nilai->nilai_huruf_diakui == 'E') {
                $jumlah_sks_tidak_lulus += (int) $l_nilai->sks_diakui;
                $makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
            }
        }

        foreach ($nilai_mahasiswa_1->result() as $l_nilai) {
            $hasil = $l_nilai->sks_mk * $l_nilai->nilai_indeks;
            $this->table->add_row([
                ['data' => $i++ . '.', 'class' => 'text-right'],
                $l_nilai->id_smt,
                $l_nilai->kode_mk,
                $l_nilai->nm_mk,
                ['data' => $l_nilai->nilai_huruf, 'class' => 'text-center'],
                ['data' => $l_nilai->nilai_indeks, 'class' => 'text-center'],
                ['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
                ['data' => $hasil, 'class' => 'text-center']
            ]);

            $jumlah += $hasil;
            if (!empty($l_nilai->nilai_huruf)) {
                $jumlah_sks_mk += (int) $l_nilai->sks_mk;
            }

            if ($l_nilai->nilai_huruf == 'E') {
                $jumlah_sks_tidak_lulus += (int) $l_nilai->sks_mk;
                $makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
            }
        }

        foreach ($nilai_mahasiswa_2->result() as $l_nilai) {
            $hasil = $l_nilai->sks_mk * $l_nilai->nilai_indeks;
            $this->table->add_row([
                ['data' => $i++ . '.', 'class' => 'text-right'],
                $l_nilai->id_smt,
                $l_nilai->kode_mk,
                $l_nilai->nm_mk,
                ['data' => $l_nilai->nilai_huruf, 'class' => 'text-center'],
                ['data' => $l_nilai->nilai_indeks, 'class' => 'text-center'],
                ['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
                ['data' => $hasil, 'class' => 'text-center']
            ]);

            $jumlah += $hasil;
            if (!empty($l_nilai->nilai_huruf)) {
                $jumlah_sks_mk += (int) $l_nilai->sks_mk;
            }

            if ($l_nilai->nilai_huruf == 'E') {
                $jumlah_sks_tidak_lulus += (int) $l_nilai->sks_mk;
                $makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
            }
        }

        if ($jumlah_sks_mk == 0) {
            $ips = 0;
        } else {
            $ips = round($jumlah / $jumlah_sks_mk, 2);
        }

        $this->table->add_row([
            ['data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 6],
            ['data' => $jumlah_sks_mk, 'class' => 'text-center'],
            ['data' => $jumlah, 'class' => 'text-center']
        ]);

        $this->table->add_row([
            ['data' => 'Indeks Prestasi / <i>Grade Point</i>', 'class' => 'text-center', 'colspan' => 6],
            ['data' => $ips, 'class' => 'text-center', 'colspan' => 2]
        ]);

        $nilai_tabel =  $this->table->generate();
        $data = [
            'query' => $mahasiswa,
            'nilai_tabel' => $nilai_tabel,
            'analisis_ipk' => $ips,
            'analisis_sks_total' => $jumlah_sks_mk,
            'analisis_sks_tidak_lulus' => $jumlah_sks_tidak_lulus,
            'analisis_sks_lulus' => $jumlah_sks_mk - $jumlah_sks_tidak_lulus,
            'makul_tidak_lulus' => $makul_tidak_lulus
        ];
        return $data;
    }

    public function get_analisis_data($nipd)
    {
        $data = $this->db->where('a.nipd', $nipd)
            ->select('a.*, m.nm_pd')
            ->from('siakad_skripsi_analisis a')
            ->join('mahasiswa_pt mp', 'a.nipd = mp.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->get()->row();

        $mk_tdk_lls =  str_replace('\r\n', '<br />', $data->makul_tidak_lulus);
        return [$data, $mk_tdk_lls];
    }
}
