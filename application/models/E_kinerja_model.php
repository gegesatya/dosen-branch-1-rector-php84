<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_kinerja_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get performance summary based on e_kinerja_nilai master table
     * and matching it with lecturer's schedule.
     */
    public function get_kinerja_summary($id_ptk, $id_smt, $user_id = null)
    {
        // 1. Get all rubrics from master e_kinerja_nilai
        $rubrik = $this->db->get('e_kinerja_nilai')->result();

        // 2. Identify the specific "Team Teaching" rubric item
        $team_rubric_index = -1;
        foreach ($rubrik as $key => $r) {
            $name = $r->keterangan ?? ($r->nama ?? '');
            if (stripos($name, 'team teaching') !== false) {
                $team_rubric_index = $key;
                break;
            }
        }

        // Initialize result containers for each rubric
        foreach ($rubrik as $r) {
            $r->schedules = [];
            $r->total_sks = 0;
            $r->display_label = $r->keterangan ?? ($r->nama ?? 'Kategori '.$r->id_kategori_jadwal);
            
            $credit_val = 0;
            if (isset($r->nilai_kredit)) $credit_val = $r->nilai_kredit;
            else if (isset($r->kredit)) $credit_val = $r->kredit;
            else if (isset($r->poin)) $credit_val = $r->poin;
            else if (isset($r->nilai)) $credit_val = $r->nilai;
            $r->nilai_kredit_detected = $credit_val;
        }

        // 3. Fetch ALL schedules for the lecturer in this semester
        $this->db->select('j.id, j.tag_name, j.id_sdm, j.id_sdm_2, j.id_sdm_3, j.id_kategori_jadwal, mk.sks_mk, jd.id_kls');
        $this->db->from('siakad_jadwal j');
        $this->db->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id', 'left');
        $this->db->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls', 'left');
        $this->db->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk', 'left');
        $this->db->group_start();
        $this->db->where('j.id_sdm', $id_ptk);
        $this->db->or_where('j.id_sdm_2', $id_ptk);
        $this->db->or_where('j.id_sdm_3', $id_ptk);
        $this->db->group_end();
        $this->db->where('j.id_smt', $id_smt);
        $this->db->group_by('j.id');
        $all_schedules = $this->db->get()->result();

        // 4. Distribute schedules to rubrics
        $entrepreneurship_tracked = []; 

        foreach ($all_schedules as $s) {
            // Menggunakan id_kategori_jadwal (5 = MK Umum dan Entrepreneurship)
            $is_entrepreneur = ($s->id_kategori_jadwal == 5);
            $is_team = (!empty($s->id_sdm_2) || !empty($s->id_sdm_3));
            
            // Count students for "Kelas Besar" info
            $s->student_count = 0;
            if (!empty($s->id_kls)) {
                $s->student_count = $this->db->where('id_kls', $s->id_kls)->count_all_results('siakad_nilai_baru');
            }

            $divider = 1;
            if ($is_team && !$is_entrepreneur) {
                if (!empty($s->id_sdm_3)) $divider = 3;
                else if (!empty($s->id_sdm_2)) $divider = 2;
            }
            $s->divider = $divider; 
            $effective_sks = $s->sks_mk / $divider;

            // Determine destination rubric
            $target_index = -1;
            if ($is_team && !$is_entrepreneur && $team_rubric_index !== -1) {
                $target_index = $team_rubric_index;
            } else {
                foreach ($rubrik as $key => $r) {
                    if ($r->id_kategori_jadwal == $s->id_kategori_jadwal) {
                        $target_index = $key;
                        break;
                    }
                }
            }

            if ($target_index !== -1) {
                if ($is_entrepreneur) {
                    if (!isset($entrepreneurship_tracked[$target_index])) {
                        $rubrik[$target_index]->total_sks += $effective_sks;
                        $entrepreneurship_tracked[$target_index] = true;
                    }
                } else {
                    $rubrik[$target_index]->total_sks += $effective_sks;
                }
                $rubrik[$target_index]->schedules[] = $s;
            }
        }

        // 5. Special logic for "Perwalian" and "Jabatan Struktural"
        foreach ($rubrik as $r) {
            $name = $r->keterangan ?? ($r->nama ?? '');
            
            // Perwalian logic
            if (stripos($name, 'perwalian') !== false) {
                $approved_students = $this->db->where([
                    'm.dosen_pa' => $id_ptk,
                    'sk.id_smt' => $id_smt,
                    'sk.id_status_krs' => 3
                ])
                ->from('mahasiswa m')
                ->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
                ->join('siakad_krs sk', 'sk.nipd = mp.nipd')
                ->count_all_results();
                
                $r->student_count = $approved_students;
                $r->calculated_poin = ($approved_students / 20) * $r->nilai_kredit_detected * 16;
                $r->perwalian_formula = "($approved_students / 20) * $r->nilai_kredit_detected * 16";
            } 
            // Pendampingan logic (IDs 7, 8, 9, 10)
            else if (in_array($r->id_kinerja, [7, 8, 9, 10])) {
                $kategori_map = [
                    7 => 'Fasilitator Entre',
                    8 => 'KKN',
                    9 => 'PKL',
                    10 => 'Pendamping UKM'
                ];
                $kat = $kategori_map[$r->id_kinerja];
                
                $this->db->select_sum('jumlah');
                $this->db->where([
                    'id_ptk' => $id_ptk,
                    'id_smt' => $id_smt,
                    'kategori' => $kat,
                    'approve_by_kaprodi' => 1
                ]);
                $q = $this->db->get('e_kinerja_pendampingan')->row();
                $jumlah = $q ? (int)$q->jumlah : 0;
                
                $r->student_count = $jumlah;
                $r->total_sks = 0;
                
                if ($jumlah > 0) {
                    if ($r->id_kinerja == 7) {
                        // Fasilitator Entre: 0.35 SKS per minggu per tim per semester
                        $r->total_sks = $jumlah * $r->nilai_kredit_detected;
                        $r->calculated_poin = $jumlah * $r->nilai_kredit_detected * 16;
                    } else if ($r->id_kinerja == 8) {
                        // KKN: 25 mhs = 0.7 SKS per minggu per semester
                        $r->total_sks = ($jumlah / 25) * $r->nilai_kredit_detected;
                        $r->calculated_poin = ($jumlah / 25) * $r->nilai_kredit_detected * 16;
                    } else if ($r->id_kinerja == 9) {
                        // PKL: 6 mhs = 0.7 SKS per minggu per semester
                        $r->total_sks = ($jumlah / 6) * $r->nilai_kredit_detected;
                        $r->calculated_poin = ($jumlah / 6) * $r->nilai_kredit_detected * 16;
                    } else if ($r->id_kinerja == 10) {
                        // Pendamping UKM: 0.35 SKS per semester
                        $r->total_sks = $jumlah * $r->nilai_kredit_detected;
                        $r->calculated_poin = $jumlah * $r->nilai_kredit_detected * 16;
                    }
                } else {
                    $r->calculated_poin = 0;
                }
            }
            // Jabatan Struktural logic
            else if (stripos($name, 'Pejabat Struktural') !== false || stripos($name, 'Jabatan Struktural') !== false) {
                if (empty($user_id)) {
                    $u = $this->db->where('id_ptk', $id_ptk)->get('users')->row();
                    $user_id = $u ? $u->id : $this->session->userdata('user_id');
                }
                $struktural = $this->db->where([
                    'user_id' => $user_id,
                    'id_smt' => $id_smt
                ])->get('e_kinerja_jabatan_struktural')->row();

                if ($struktural) {
                    $r->structural_info = $struktural->jabatan;
                    $r->structural_credit = (float)$struktural->kredit;
                    $r->calculated_poin = $r->structural_credit * 16;
                    $r->total_sks = $r->structural_credit; 
                } else {
                    $r->calculated_poin = 0;
                }
            }
            // Logic for Skripsi/TA (Sempro) - IDs 12, 13, 14
            else if (in_array($r->id_kinerja, [12, 13, 14])) {

                // Get semester date range
                $smt_info = $this->db->get_where('semester', ['id_smt' => $id_smt])->row();
                $tgl_mulai = $smt_info->tgl_mulai ?? '1970-01-01';
                $tgl_selesai = $smt_info->tgl_selesai ?? '2099-12-31';

                $student_query = $this->db->select('ns.nipd, COALESCE(m.nm_pd, ns.nipd) as nm_pd, ns.sidang_tanggal, ns.sidang_nilai_ratarata, ns.sidang_nilai_mutu, ns.sidang_nilai_akhir, pj.tanggal_usulan_kaprodi, mpt2.tgl_keluar as tanggal_keluar', FALSE)
                    ->from('siakad_skripsi_nilai_sempro ns')
                    ->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = ns.id_judul_skripsi')
                    ->join('mahasiswa_pt mpt', 'mpt.nipd = ns.nipd', 'left')
                    ->join('mahasiswa m', 'm.id_pd = mpt.id_pd', 'left')
                    // Join mahasiswa_pt to check graduation date
                    ->join('mahasiswa_pt mpt2', 'mpt2.nipd = ns.nipd AND mpt2.id_jns_keluar = "1"', 'left');
                    
                if ($r->id_kinerja == 12) {
                    // Pembimbing Utama
                    $student_query->where("(
                        (COALESCE(pj.id_sms, '') != 'f28c79a3-18c1-4b40-b3af-b2bedc810e20' AND pj.dosen_penguji_sempro_1 = '$id_ptk')
                        OR 
                        (pj.id_sms = 'f28c79a3-18c1-4b40-b3af-b2bedc810e20' AND pj.dosen_pembimbing_1 = '$id_ptk')
                    )", NULL, FALSE);
                } else if ($r->id_kinerja == 13) {
                    // Pendamping (Hanya untuk prodi selain Akuntansi Perpajakan)
                    $student_query->where("(
                        COALESCE(pj.id_sms, '') != 'f28c79a3-18c1-4b40-b3af-b2bedc810e20' AND pj.dosen_penguji_sempro_2 = '$id_ptk'
                    )", NULL, FALSE);
                } else if ($r->id_kinerja == 14) {
                    // Penguji
                    $student_query->where("(
                        (COALESCE(pj.id_sms, '') != 'f28c79a3-18c1-4b40-b3af-b2bedc810e20' AND pj.dosen_penguji_sempro_3 = '$id_ptk')
                        OR 
                        (pj.id_sms = 'f28c79a3-18c1-4b40-b3af-b2bedc810e20' AND (pj.dosen_penguji_sempro_2 = '$id_ptk' OR pj.dosen_penguji_sempro_3 = '$id_ptk'))
                    )", NULL, FALSE);
                }

                $result_query = $student_query->group_start()
                        // Milestone 1: Sidang Lulus in this semester
                        ->group_start()
                            ->where('ns.sidang_tanggal >=', $tgl_mulai)
                            ->where('ns.sidang_tanggal <=', $tgl_selesai)
                            ->where('UPPER(ns.sidang_hasil)', 'LULUS')
                        ->group_end()
                        // Milestone 2: Semhas Layak in this semester, but NO Sidang Lulus in this or any previous semester
                        ->or_group_start()
                            ->where('ns.semhas_tanggal >=', $tgl_mulai)
                            ->where('ns.semhas_tanggal <=', $tgl_selesai)
                            ->where('UPPER(ns.semhas_hasil)', 'LAYAK')
                            ->group_start()
                                ->where('ns.sidang_tanggal >', $tgl_selesai)
                                ->or_where('ns.sidang_tanggal', NULL)
                                ->or_where('UPPER(ns.sidang_hasil) !=', 'LULUS')
                            ->group_end()
                        ->group_end()
                    ->group_end()
                    ->distinct()
                    ->get();
                
                $students_raw = $result_query->result();
                $students = [];
                $seen_nipds = [];
                foreach ($students_raw as $std) {
                    $has_nilai = (!empty($std->sidang_nilai_ratarata) && $std->sidang_nilai_ratarata > 0) || 
                                 (!empty($std->sidang_nilai_akhir) && $std->sidang_nilai_akhir > 0) || 
                                 (!empty($std->sidang_nilai_mutu) && trim((string)$std->sidang_nilai_mutu) != '');
                    if ($has_nilai && !in_array($std->nipd, $seen_nipds)) {
                        $students[] = $std;
                        $seen_nipds[] = $std->nipd;
                    }
                }
                
                $r->student_count = count($students);
                $r->student_list = $students;
                $r->student_names = implode(', ', array_column($students, 'nm_pd'));
                $r->calculated_poin = $r->student_count * $r->nilai_kredit_detected;
                $r->total_sks = $r->student_count; 
            }
            else {
                $r->calculated_poin = $r->total_sks * $r->nilai_kredit_detected;
            }
        }
        return $rubrik;
    }

    public function get_kinerja_yearly($id_ptk, $academic_year, $user_id = null)
    {
        $id_smt_ganjil = $academic_year . '1';
        $id_smt_genap = $academic_year . '2';

        $ganjil_data = $this->get_kinerja_summary($id_ptk, $id_smt_ganjil, $user_id);
        $genap_data = $this->get_kinerja_summary($id_ptk, $id_smt_genap, $user_id);

        // Cross-semester deduplication for Skripsi/TA (rubric IDs 12, 13, 14)
        foreach ($ganjil_data as $index => $ganjil) {
            if (isset($ganjil->id_kinerja) && in_array($ganjil->id_kinerja, [12, 13, 14])) {
                $genap = $genap_data[$index] ?? null;
                if ($genap && isset($ganjil->student_list) && isset($genap->student_list)) {
                    $genap_nipds = array_column($genap->student_list, 'nipd');
                    
                    // Filter ganjil students
                    $new_ganjil_students = [];
                    foreach ($ganjil->student_list as $std) {
                        if (!in_array($std->nipd, $genap_nipds)) {
                            $new_ganjil_students[] = $std;
                        }
                    }
                    
                    // Update ganjil metrics
                    $ganjil->student_list = $new_ganjil_students;
                    $ganjil->student_count = count($new_ganjil_students);
                    $ganjil->student_names = implode(', ', array_column($new_ganjil_students, 'nm_pd'));
                    $ganjil->total_sks = $ganjil->student_count;
                    $ganjil->calculated_poin = $ganjil->student_count * $ganjil->nilai_kredit_detected;
                }
            }
        }

        $result = [];
        // Use Ganjil data as baseline because rubrics are the same
        foreach ($ganjil_data as $index => $ganjil) {
            $row = new stdClass();
            $row->id_kategori_jadwal = $ganjil->id_kategori_jadwal;
            $row->display_label = $ganjil->display_label;
            $row->nilai_kredit_detected = $ganjil->nilai_kredit_detected;
            
            // Ganjil side
            $row->ganjil = $ganjil;
            
            // Genap side (match by index or find by label if order differs)
            $row->genap = $genap_data[$index] ?? null;
            
            // Yearly Total calculation: Simple sum Ganjil + Genap
            // (Multiplier 16 is handled in the view or already included in specific rubric formulas)
            $ganjil_poin = $ganjil->calculated_poin ?? 0;
            $genap_poin = $row->genap->calculated_poin ?? 0;
            $row->total_poin_yearly = $ganjil_poin + $genap_poin;

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Get the rubric for performance calculation
     */
    public function get_rubrik()
    {
        return $this->db->get('e_kinerja_nilai')->result();
    }

    /**
     * Get total points for a semester
     */
    public function get_total_kinerja($id_ptk, $id_smt)
    {
        $data = $this->get_kinerja_summary($id_ptk, $id_smt);
        $total = 0;
        foreach ($data as $row) {
            $total += $row->calculated_poin;
        }
        return $total;
    }
}
