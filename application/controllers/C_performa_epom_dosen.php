<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_performa_epom_dosen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $username   = $this->ion_auth->user()->row()->email;
        $data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');
        $is_kaprodi = $this->ion_auth->in_group([5]);

        if ($data_akses->num_rows() == 0 && !$is_kaprodi) {
            $this->output->set_status_header(403);

            $output['judul_besar']       = 'Akses Ditolak';
            $output['judul_kecil']       = '';
            $output['isi']               = 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses halaman ini.';
            $output['menu_aktif']        = 'm_performa_epom_dosen';
            $output['parent_infografis'] = 'active open';
            $output['content']           = 'forbidden_page/forbidden_page_view';
            $output['after_page']        = 'forbidden_page/forbidden_page_after_page';

            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }

        $this->load->driver('cache', ['adapter' => 'file']);
    }

    public function index()
    {
        $output['judul_besar']       = 'Performa EPOM Dosen';
        $output['judul_kecil']       = 'KPI Rata-Rata EPOM 3 Semester Terakhir';
        $output['menu_aktif']        = 'm_performa_epom_dosen';
        $output['parent_infografis'] = 'active open';
        $output['content']           = 'performa_epom_dosen/performa_epom_dosen_view';
        $output['after_page']        = 'performa_epom_dosen/performa_epom_dosen_after_page';

        $this->load->view('master_page', $output);
    }

    public function generate()
    {
        $id_smt = $this->input->get('id_smt');

        // Load Semester list
        $sql_smt = "SELECT id_smt, nm_smt 
                   FROM semester 
                   WHERE id_smt >= 20221 
                   ORDER BY id_smt DESC";
        $smt_list = $this->db->query($sql_smt)->result();

        $active_smt = null;
        if (!empty($id_smt)) {
            foreach ($smt_list as $s) {
                if ($s->id_smt == $id_smt) {
                    $active_smt = $s->id_smt;
                    break;
                }
            }
        }
        if (!$active_smt && !empty($smt_list)) {
            $active_smt = $smt_list[0]->id_smt;
        }

        if (!$active_smt) {
            $data['smt_list']   = $smt_list;
            $data['active_smt'] = null;
            $this->load->view('performa_epom_dosen/performa_epom_dosen_generate', $data);
            return;
        }

        // Find the 4 most recent semesters up to this Semester
        $sql_smts = "SELECT id_smt, nm_smt 
                     FROM semester 
                     WHERE id_smt <= " . $this->db->escape($active_smt) . "
                     ORDER BY id_smt DESC 
                     LIMIT 4";
        $recent_smts = $this->db->query($sql_smts)->result();
        $recent_smts = array_reverse($recent_smts);
        
        $smt_ids = [];
        $smt_names = [];
        foreach ($recent_smts as $s) {
            $smt_ids[] = (int)$s->id_smt;
            $smt_names[] = $s->nm_smt;
        }
        
        // Load all prodi
        $prodi_list = $this->db->query("SELECT id_sms, nm_lemb FROM sms ORDER BY nm_lemb")->result();
        $prodi_map = [];
        foreach ($prodi_list as $p) {
            $prodi_map[$p->id_sms] = [
                'nm_lemb' => $p->nm_lemb,
                'dosen' => []
            ];
        }

        if (!empty($smt_ids)) {
            $smt_ids_str = implode(',', $smt_ids);
            // Get EPOM performance
            $sql_kpi = "
                SELECT
                    fk.id_sms,
                    p.id_ptk,
                    p.nm_ptk,
                    p.id_ikatan_kerja,
                    fk.id_smt,
                    AVG(re.nilai_epom) as kpi_epom,
                    COUNT(re.id_kls) as jml_kls_mengajar
                FROM siakad_rekap_epom re
                JOIN feeder_kelas_kuliah fk ON fk.id_kls = re.id_kls
                JOIN siakad_dosen p ON p.id_ptk = re.id_ptk
                WHERE fk.id_smt IN ($smt_ids_str)
                  AND re.nilai_epom > 0
                GROUP BY fk.id_sms, p.id_ptk, p.nm_ptk, p.id_ikatan_kerja, fk.id_smt
            ";
            
            $dosen_kpi = $this->db->query($sql_kpi)->result();
            
            // Group by dosen and prodi
            $dosen_grouped = [];
            foreach ($dosen_kpi as $d) {
                $id_dosen = $d->id_sms . '_' . $d->id_ptk;
                if (!isset($dosen_grouped[$id_dosen])) {
                    $dosen_grouped[$id_dosen] = (object) [
                        'id_sms' => $d->id_sms,
                        'id_ptk' => $d->id_ptk,
                        'nm_ptk' => $d->nm_ptk,
                        'id_ikatan_kerja' => $d->id_ikatan_kerja,
                        'is_tetap' => ($d->id_ikatan_kerja == 'A') ? 1 : 0,
                        'jml_smt_mengajar' => 0,
                        'jml_kls_mengajar' => 0,
                        'total_epom' => 0,
                        'kpi_epom' => 0,
                        'smt_data' => []
                    ];
                    foreach ($smt_ids as $sid) {
                        $dosen_grouped[$id_dosen]->smt_data[$sid] = 0;
                    }
                }
                
                $dosen_grouped[$id_dosen]->smt_data[$d->id_smt] = (float)$d->kpi_epom;
                $dosen_grouped[$id_dosen]->jml_smt_mengajar++;
                $dosen_grouped[$id_dosen]->jml_kls_mengajar += $d->jml_kls_mengajar;
                $dosen_grouped[$id_dosen]->total_epom += $d->kpi_epom;
            }
            
            // calculate avg
            foreach ($dosen_grouped as &$d_grp) {
                if ($d_grp->jml_smt_mengajar > 0) {
                    $d_grp->kpi_epom = $d_grp->total_epom / $d_grp->jml_smt_mengajar;
                }
            }
            unset($d_grp);

            foreach ($dosen_grouped as $d_grp) {
                if (isset($prodi_map[$d_grp->id_sms])) {
                    $prodi_map[$d_grp->id_sms]['dosen'][] = $d_grp;
                }
            }
            
            // sort and calculate prodi stats
            foreach ($prodi_map as &$p) {
                usort($p['dosen'], fn($a, $b) => $b->kpi_epom <=> $a->kpi_epom);
                
                $n = count($p['dosen']);
                $sum = 0;
                foreach ($p['dosen'] as $d) {
                    $sum += $d->kpi_epom;
                }
                $avg = $n > 0 ? $sum / $n : 0;
                
                $variance_sum = 0;
                foreach ($p['dosen'] as $d) {
                    $variance_sum += ($d->kpi_epom - $avg) ** 2;
                }
                $variance = $n > 0 ? $variance_sum / $n : 0;
                $sd = sqrt($variance);
                
                $p['avg_epom'] = $avg;
                $p['sd_epom'] = $sd;
            }
            unset($p);
        }

        $data = [
            'smt_list' => $smt_list,
            'active_smt' => $active_smt,
            'recent_smts' => $recent_smts,
            'prodi_map' => $prodi_map
        ];

        $this->load->view('performa_epom_dosen/performa_epom_dosen_generate', $data);
    }
}
