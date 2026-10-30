<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_linimasa_ta extends CI_Controller
{
    const BATAS_TEPAT_WAKTU = 4;
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        // Load driver cache dengan prioritas Redis, fallback ke file cache
        $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $username = $this->ion_auth->user()->row()->email;
        $data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

        if ($data_akses->num_rows() == 0 && !$this->ion_auth->in_group([5])) {
            $this->output->set_status_header(403);
            
            $output['judul_besar']      = 'Akses Ditolak';
            $output['judul_kecil']      = '';
            $output['isi']              = 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses Linimasa TA.';
            $output['menu_aktif']       = 'm_linimasa_ta';
            $output['parent_infografis'] = 'active open';
            $output['content']          = 'forbidden_page/forbidden_page_view';
            $output['after_page']       = 'forbidden_page/forbidden_page_after_page';
            
            $this->load->view('master_page', $output);
            echo $this->output->get_output();
            exit;
        }
    }

    public function index()
    {
        $output['judul_besar']      = 'Linimasa Tugas Akhir';
        $output['judul_kecil']      = 'Politeknik WBI';	
        $output['menu_aktif']       = 'm_linimasa_ta';
        $output['parent_infografis'] = 'active open';
        
        $output['stats']            = $this->_get_stats();
        
        $output['content']          = 'linimasa_ta/linimasa_ta_view';
        $output['after_page']       = 'linimasa_ta/linimasa_ta_after_page';
        
        $this->load->view('master_page', $output);
    }

    private function _get_stats()
    {
        $cache_name = 'linimasa_ta_stats';
        $cache_data = $this->cache->get($cache_name);
        if ($cache_data !== FALSE) {
            return $cache_data;
        }

        $setting = $this->db->get('siakad_setting')->row();
        $tahun_ajaran_aktif = $setting ? $setting->tahun_ajaran_aktif : '';

        // Pre-fetch all KRS max semester for TA to optimize
        $krs_data_query = "
            SELECT k.nipd, MAX(k.id_smt) as max_smt
            FROM siakad_krs_detail kd
            JOIN siakad_krs k ON k.id_rencana_studi = kd.id_rencana_studi
            JOIN feeder_kelas_kuliah kk ON kk.id_kls = kd.id_kls
            JOIN feeder_mata_kuliah fmk ON fmk.id_mk = kk.id_mk
            WHERE (fmk.nm_mk LIKE '%tugas akhir%' OR fmk.nm_mk LIKE '%skripsi%')
            GROUP BY k.nipd
        ";
        $krs_results = $this->db->query($krs_data_query)->result();
        $student_krs_map = [];
        foreach ($krs_results as $kr) {
            $student_krs_map[$kr->nipd] = $kr->max_smt;
        }

        $total_mhs_query = "
            SELECT s.nm_lemb as prodi_name, LEFT(mp.nipd, 2) as angkatan_str, COUNT(*) as total_mhs
            FROM mahasiswa_pt mp
            JOIN sms s ON s.id_sms = mp.id_sms
            WHERE mp.id_jns_keluar IS NULL OR mp.id_jns_keluar != '1'
            GROUP BY s.nm_lemb, LEFT(mp.nipd, 2)
        ";
        $total_mhs_results = $this->db->query($total_mhs_query)->result();
        $angkatan_population = [];
        foreach ($total_mhs_results as $row) {
            $year = 2000 + (int)$row->angkatan_str;
            $angkatan_population[$row->prodi_name] ??= [];
            $angkatan_population[$row->prodi_name][$year] = $row->total_mhs;
        }

        $query_str = "
            SELECT 
                j.id_judul_skripsi,
                j.nipd,
                j.tanggal_disetujui,
                j.tanggal_sk,
                COALESCE(j.tanggal_disetujui, j.tanggal_sk, j.tanggal_diajukan) as tgl_penetapan,
                SUM(CASE WHEN UPPER(ns.sempro_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as sempro_layak,
                SUM(CASE WHEN UPPER(ns.semhas_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as semhas_layak,
                SUM(CASE WHEN UPPER(ns.sidang_hasil) = 'LULUS' THEN 1 ELSE 0 END) as sidang_lulus,
                MAX(ns.seminar_tanggal) as sempro_tanggal,
                MAX(ns.semhas_tanggal) as semhas_tanggal,
                MAX(ns.sidang_tanggal) as sidang_tanggal,
                s.nm_lemb as prodi_name,
                sd1.nm_ptk as pembimbing_name,
                sd2.nm_ptk as pembimbing2_name,
                sd1.id_ikatan_kerja as pembimbing_status,
                sd2.id_ikatan_kerja as pembimbing2_status,
                mp.id_jns_keluar,
                mp.mulai_smt
            FROM siakad_skripsi_pengajuan_judul j
            JOIN mahasiswa_pt mp ON mp.nipd = j.nipd
            JOIN sms s ON s.id_sms = j.id_sms
            LEFT JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = j.id_judul_skripsi
            LEFT JOIN siakad_dosen sd1 ON sd1.id_ptk = j.dosen_pembimbing_1
            LEFT JOIN siakad_dosen sd2 ON sd2.id_ptk = j.dosen_pembimbing_2
            WHERE j.status_sk != 3
            GROUP BY j.id_judul_skripsi, j.nipd, j.tanggal_disetujui, j.tanggal_sk, j.tanggal_diajukan, s.nm_lemb, sd1.nm_ptk, sd2.nm_ptk, sd1.id_ikatan_kerja, sd2.id_ikatan_kerja, mp.id_jns_keluar, mp.mulai_smt
        ";
        
        $students = $this->db->query($query_str)->result();
        
        $total = 0;
        $potensi_tinggi = 0;
        $potensi_sedang = 0;
        $potensi_rendah = 0;
        
        $total_days = 0;
        $counted_days = 0;
        
        $prodi_details = [];
        
        foreach ($students as $stu) {
            $is_alumni = (isset($stu->id_jns_keluar) && $stu->id_jns_keluar == '1');
            
            // Batasi pengecekan alumni hanya 2 tahun terakhir (sejak E-Tugas Akhir efektif)
            if ($is_alumni) {
                if (empty($stu->sidang_tanggal) || strtotime($stu->sidang_tanggal) < strtotime('-2 years')) {
                    continue;
                }
            }
            
            $pname = $stu->prodi_name;
            $prodi_details[$pname] ??= [
                'total' => 0,
                'tinggi' => 0,
                'sedang' => 0,
                'rendah' => 0,
                'waktu_kritis' => 0, // TA > 1 tahun
                'waktu_kritis_belum_progres' => 0,
                'waktu_kritis_ada_progres' => 0,
                'waktu_menengah' => 0, // 6-12 bulan
                'waktu_baru' => 0, // < 6 bulan
                'angkatan_baru_list' => [], // ['2020' => count]
                'dosen_kritis' => [], // ['Nama Dosen' => count]
                'dosen_beban' => [], // ['Nama Dosen' => count]
                'krs_aktif' => 0,
                'krs_menghilang' => 0,
                
                // Arrays to track current stuck duration of active students
                'stuck_judul_sempro' => [],
                'stuck_sempro_semhas' => [],
                'stuck_semhas_sidang' => [],

                'lulus_tepat_waktu' => 0,
                'lulus_terlambat' => 0,
                'tahap_sidang' => 0,
                'tahap_semhas' => 0,
                'tahap_sempro' => 0,
                'tahap_belum' => 0,
                'sum_judul_sempro' => 0,
                'count_judul_sempro' => 0,
                'sum_sempro_semhas' => 0,
                'count_sempro_semhas' => 0,
                'sum_semhas_sidang' => 0,
                'count_semhas_sidang' => 0,
                'anomali_kilat' => 0,
                'dosen_nonaktif_list' => [],
                'belum_disetujui' => 0,
                'sum_judul_sempro_ideal' => 0,
                'count_judul_sempro_ideal' => 0,
                'sum_sempro_semhas_ideal' => 0,
                'count_sempro_semhas_ideal' => 0,
                'sum_semhas_sidang_ideal' => 0,
                'count_semhas_sidang_ideal' => 0
            ];
            
            $is_alumni = (isset($stu->id_jns_keluar) && $stu->id_jns_keluar == '1');
            
            if (!$is_alumni) {
                $total++;
                $prodi_details[$pname]['total']++;
                
                $max_smt = $student_krs_map[$stu->nipd] ?? null;
                if ($max_smt && $max_smt >= $tahun_ajaran_aktif) {
                    $prodi_details[$pname]['krs_aktif']++;
                } else {
                    $prodi_details[$pname]['krs_menghilang']++;
                }
                
                // Deteksi judul belum disetujui
                if (empty($stu->tanggal_disetujui) && empty($stu->tanggal_sk)) {
                    $prodi_details[$pname]['belum_disetujui']++;
                }
                
                // Milestone check
                // Milestone check for Potential
                if ($stu->sidang_lulus > 0 || $stu->semhas_layak > 0) {
                    $potensi_tinggi++;
                    $prodi_details[$pname]['tinggi']++;
                } elseif ($stu->sempro_layak > 0) {
                    $potensi_sedang++;
                    $prodi_details[$pname]['sedang']++;
                } else {
                    $potensi_rendah++;
                    $prodi_details[$pname]['rendah']++;
                }
                
                // Mutually Exclusive Progress check
                if ($stu->sidang_lulus > 0) {
                    $prodi_details[$pname]['tahap_sidang']++;
                } elseif ($stu->semhas_layak > 0) {
                    $prodi_details[$pname]['tahap_semhas']++;
                } elseif ($stu->sempro_layak > 0) {
                    $prodi_details[$pname]['tahap_sempro']++;
                } else {
                    $prodi_details[$pname]['tahap_belum']++;
                }
                // Beban Dosen (Mahasiswa Aktif belum lulus)
                if ($stu->sidang_lulus == 0) {
                    $dosen = !empty($stu->pembimbing_name) ? trim((string)$stu->pembimbing_name) : 'Belum Ditentukan';
                    $prodi_details[$pname]['dosen_beban'][$dosen] ??= 0;
                    $prodi_details[$pname]['dosen_beban'][$dosen]++;
                    
                    // Deteksi pembimbing utama atau pendamping tidak aktif
                    $dosen1_x = (isset($stu->pembimbing_status) && trim((string)$stu->pembimbing_status) === 'X');
                    $dosen2_x = (isset($stu->pembimbing2_status) && trim((string)$stu->pembimbing2_status) === 'X');
                    
                    if ($dosen1_x) {
                        $d_name = !empty($stu->pembimbing_name) ? trim((string)$stu->pembimbing_name) : 'Tidak Diketahui (Utama)';
                        $prodi_details[$pname]['dosen_nonaktif_list'][$d_name] ??= 0;
                        $prodi_details[$pname]['dosen_nonaktif_list'][$d_name]++;
                    }
                    
                    if ($dosen2_x) {
                        $d_name = !empty($stu->pembimbing2_name) ? trim((string)$stu->pembimbing2_name) : 'Tidak Diketahui (Pendamping)';
                        $prodi_details[$pname]['dosen_nonaktif_list'][$d_name] ??= 0;
                        $prodi_details[$pname]['dosen_nonaktif_list'][$d_name]++;
                    }
                }
            }
            
            // Age in days
            $is_lulus_tepat_waktu = false;
            $days = 0;
            if (!empty($stu->tgl_penetapan)) {
                $tgl = strtotime($stu->tgl_penetapan);
                if ($tgl > 0) {
                    // Check if passed sidang to stop age counter
                    if ($stu->sidang_lulus > 0 && !empty($stu->sidang_tanggal)) {
                        $diff = strtotime($stu->sidang_tanggal) - $tgl;
                        if ($diff < 0) $diff = 0;
                        $days = floor($diff / (60 * 60 * 24));
                        
                        // Hitung masa studi
                        if (!empty($stu->mulai_smt)) {
                            $mulai_thn = floor($stu->mulai_smt / 10);
                        } elseif (!empty($stu->nipd)) {
                            $angkatan_str = substr(trim((string)$stu->nipd), 0, 2);
                            $mulai_thn = 2000 + (int)$angkatan_str;
                        } else {
                            $mulai_thn = date('Y', $tgl) - 4;
                        }
                        
                        $lulus_thn = date('Y', strtotime($stu->sidang_tanggal));
                        $masa_studi = $lulus_thn - $mulai_thn;
                        
                        // Relax limit to 360 days (1 year TA) as long as study period is 4 years
                        if ($days <= 360 && $masa_studi <= 4) {
                            if (!$is_alumni) $prodi_details[$pname]['lulus_tepat_waktu']++;
                            $is_lulus_tepat_waktu = true;
                        } else {
                            if (!$is_alumni) $prodi_details[$pname]['lulus_terlambat']++;
                        }
                    } else {
                        $diff = time() - $tgl;
                        if ($diff < 0) $diff = 0;
                        $days = floor($diff / (60 * 60 * 24));
                    }
                    
                    if ($days >= 0 && !$is_alumni) {
                        $total_days += $days;
                        $counted_days++;
                        
                        if ($days > 365 && $stu->sidang_lulus == 0) {
                            $prodi_details[$pname]['waktu_kritis']++;
                            
                            if ($stu->sempro_layak == 0) {
                                $prodi_details[$pname]['waktu_kritis_belum_progres']++;
                            } else {
                                $prodi_details[$pname]['waktu_kritis_ada_progres']++;
                            }
                            
                            $dosen_k = !empty($stu->pembimbing_name) ? trim((string)$stu->pembimbing_name) : 'Belum Ditentukan';
                            $prodi_details[$pname]['dosen_kritis'][$dosen_k] ??= 0;
                            $prodi_details[$pname]['dosen_kritis'][$dosen_k]++;
                        } elseif ($days > 180) {
                            $prodi_details[$pname]['waktu_menengah']++;
                        } else {
                            $prodi_details[$pname]['waktu_baru']++;
                            if (!empty($stu->nipd)) {
                                $angkatan_str = substr(trim((string)$stu->nipd), 0, 2);
                                $angkatan_year = 2000 + (int)$angkatan_str;
                                $prodi_details[$pname]['angkatan_baru_list'][$angkatan_year] ??= 0;
                                $prodi_details[$pname]['angkatan_baru_list'][$angkatan_year]++;
                            }
                        }
                    }
                }
            } else {
                // If no date, consider it baru (new submission)
                if (!$is_alumni) {
                    $prodi_details[$pname]['waktu_baru']++;
                    if (!empty($stu->nipd)) {
                        $angkatan_str = substr(trim((string)$stu->nipd), 0, 2);
                        $angkatan_year = 2000 + (int)$angkatan_str;
                        $prodi_details[$pname]['angkatan_baru_list'][$angkatan_year] ??= 0;
                        $prodi_details[$pname]['angkatan_baru_list'][$angkatan_year]++;
                    }
                }
            }
            
            // Waktu Antar-Tahapan dan Anomali Kilat
            if (!empty($stu->tgl_penetapan) && !empty($stu->sempro_tanggal) && $stu->sempro_layak > 0) {
                $d = strtotime($stu->sempro_tanggal) - strtotime($stu->tgl_penetapan);
                if ($d >= 0) {
                    if (!$is_alumni && $days <= 360 && $masa_studi <= 4) {
                        $prodi_details[$pname]['sum_judul_sempro'] += $d;
                        $prodi_details[$pname]['count_judul_sempro']++;
                    }
                    if ($is_lulus_tepat_waktu) {
                        $prodi_details[$pname]['sum_judul_sempro_ideal'] += $d;
                        $prodi_details[$pname]['count_judul_sempro_ideal']++;
                    }
                    if ($d < (7 * 86400)) { // di bawah 7 hari
                        $prodi_details[$pname]['anomali_kilat']++;
                    }
                }
            }
            
            if (!empty($stu->sempro_tanggal) && !empty($stu->semhas_tanggal) && $stu->semhas_layak > 0) {
                $d = strtotime($stu->semhas_tanggal) - strtotime($stu->sempro_tanggal);
                if ($d >= 0) {
                    if (!$is_alumni && $days <= 360 && $masa_studi <= 4) {
                        $prodi_details[$pname]['sum_sempro_semhas'] += $d;
                        $prodi_details[$pname]['count_sempro_semhas']++;
                    }
                    if ($is_lulus_tepat_waktu) {
                        $prodi_details[$pname]['sum_sempro_semhas_ideal'] += $d;
                        $prodi_details[$pname]['count_sempro_semhas_ideal']++;
                    }
                }
            }
            
            if (!empty($stu->semhas_tanggal) && !empty($stu->sidang_tanggal) && $stu->sidang_lulus > 0) {
                $d = strtotime($stu->sidang_tanggal) - strtotime($stu->semhas_tanggal);
                if ($d >= 0) {
                    if (!$is_alumni && $days <= 360 && $masa_studi <= 4) {
                        $prodi_details[$pname]['sum_semhas_sidang'] += $d;
                        $prodi_details[$pname]['count_semhas_sidang']++;
                    }
                    if ($is_lulus_tepat_waktu) {
                        $prodi_details[$pname]['sum_semhas_sidang_ideal'] += $d;
                        $prodi_details[$pname]['count_semhas_sidang_ideal']++;
                    }
                }
            }
            
            // EARLY WARNING SYSTEM: Track stuck durations for active students
            if (!$is_alumni) {
                if (!empty($stu->tgl_penetapan) && $stu->sempro_layak == 0) {
                    $d = time() - strtotime($stu->tgl_penetapan);
                    if ($d > 0) $prodi_details[$pname]['stuck_judul_sempro'][] = $d;
                }
                if ($stu->sempro_layak > 0 && $stu->semhas_layak == 0 && !empty($stu->sempro_tanggal)) {
                    $d = time() - strtotime($stu->sempro_tanggal);
                    if ($d > 0) $prodi_details[$pname]['stuck_sempro_semhas'][] = $d;
                }
                if ($stu->semhas_layak > 0 && $stu->sidang_lulus == 0 && !empty($stu->semhas_tanggal)) {
                    $d = time() - strtotime($stu->semhas_tanggal);
                    if ($d > 0) $prodi_details[$pname]['stuck_semhas_sidang'][] = $d;
                }
            }
        }
        
        $avg_days = $counted_days > 0 ? round($total_days / $counted_days) : 0;
        
        // Find overall strong point and weak point
        $strong_prodi = "";
        $strong_rate = -1;
        $weak_prodi = "";
        $weak_rate = 101; 
        
        foreach ($prodi_details as $pname => &$data) {
            arsort($data['dosen_kritis']); // Sort dosen by highest critical student count
            
            $total_p = $data['total'];
            $potensi_rate_tinggi = ($total_p > 0) ? ($data['tinggi'] / $total_p) * 100 : 0;
            $potensi_rate_sedang = ($total_p > 0) ? ($data['sedang'] / $total_p) * 100 : 0;
            $rate = $potensi_rate_tinggi + $potensi_rate_sedang;
            
            $data['progress_rate'] = $rate;
            
            if ($rate > $strong_rate) {
                $strong_rate = $rate;
                $strong_prodi = $pname;
            }
            if ($rate < $weak_rate) {
                $weak_rate = $rate;
                $weak_prodi = $pname;
            }

            // Generate per-prodi analysis for the modal
            $kritis_rate = ($total_p > 0) ? round(($data['waktu_kritis'] / $total_p) * 100, 1) : 0;
            $progress_rate = round($rate, 1);
            
            $data['strong_point'] = "Sebanyak <strong>{$progress_rate}%</strong> mahasiswa telah berhasil melewati Seminar Proposal (Potensi Kelulusan Tinggi/Sedang).";
            
            if ($data['waktu_kritis'] > 0) {
                $data['weak_point'] = "Terdapat <strong>{$data['waktu_kritis']} mahasiswa ({$kritis_rate}%)</strong> yang berisiko kritis (lama pengerjaan TA > 1 tahun). Mahasiswa ini memerlukan monitoring intensif.";
            } else {
                $data['weak_point'] = "Tidak ada mahasiswa yang berada pada risiko kritis (umur TA > 1 tahun). Performa waktu cukup baik.";
            }

            // Generate Narrative
            $peluang_lulus_rate = ($total_p > 0) ? round((($data['tahap_sidang'] + $data['tahap_semhas']) / $total_p) * 100, 1) : 0;
            $tahap_akhir_count = $data['tahap_sidang'] + $data['tahap_semhas'];
            
            $lulus_total = $data['lulus_tepat_waktu'] + $data['lulus_terlambat'];
            $lulus_tepat_waktu_rate = ($lulus_total > 0) ? round(($data['lulus_tepat_waktu'] / $lulus_total) * 100, 1) : 0;
            
            $waktu_baru_count = $data['waktu_baru'];
            $waktu_baru_rate = ($total_p > 0) ? round(($waktu_baru_count / $total_p) * 100, 1) : 0;
            
            // Cari mayoritas angkatan
            $mayoritas_angkatan = 'tidak diketahui';
            $mayoritas_count = 0;
            $populasi_angkatan = 0;
            if (!empty($data['angkatan_baru_list'])) {
                arsort($data['angkatan_baru_list']);
                $mayoritas_angkatan = array_key_first($data['angkatan_baru_list']);
                $mayoritas_count = $data['angkatan_baru_list'][$mayoritas_angkatan];
                
                if (isset($angkatan_population[$pname][$mayoritas_angkatan])) {
                    $populasi_angkatan = $angkatan_population[$pname][$mayoritas_angkatan];
                }
            }
            
            $narasi = "Program Studi <strong>{$pname}</strong> memiliki total <strong>{$total_p}</strong> rekam data pengajuan Tugas Akhir (TA). ";
            
            if ($peluang_lulus_rate > 0) {
                $narasi .= "Dari keseluruhan jumlah tersebut, sebanyak <strong>{$tahap_akhir_count} orang ({$peluang_lulus_rate}%)</strong> mahasiswa sudah berada pada tahapan penyelesaian akhir (telah mencapai Seminar Hasil atau Lulus Sidang). ";
            } else {
                $narasi .= "Saat ini, belum ada mahasiswa yang berhasil mencapai tahapan penyelesaian akhir (Seminar Hasil maupun Sidang). ";
            }
            
            if ($waktu_baru_count > 0) {
                $narasi .= "Kemudian sebanyak <strong>{$waktu_baru_count} orang ({$waktu_baru_rate}%)</strong> masih memiliki umur TA &lt; 6 bulan. ";
                
                if ($mayoritas_angkatan != 'tidak diketahui' && $populasi_angkatan > 0) {
                    $narasi .= "Sebagian besar berasal dari angkatan <strong>{$mayoritas_angkatan}</strong>. Dari total <strong>{$populasi_angkatan} mahasiswa</strong> angkatan {$mayoritas_angkatan} ini, sebanyak <strong>{$mayoritas_count} orang</strong> telah mengajukan Tugas Akhir. ";
                } else {
                    $narasi .= "Sebagian besar berasal dari angkatan <strong>{$mayoritas_angkatan}</strong>. ";
                }
                
                $narasi .= "Mahasiswa yang memiliki umur penetapan judul Tugas Akhir kurang dari 6 bulan ini berpotensi untuk lulus tepat waktu. ";
            }
            
            $waktu_kritis_count = $data['waktu_kritis'];
            if ($waktu_kritis_count > 0) {
                $kritis_belum = $data['waktu_kritis_belum_progres'];
                $kritis_ada = $data['waktu_kritis_ada_progres'];
                $narasi .= "Selain itu perlu di highlight juga untuk <strong>{$waktu_kritis_count} mahasiswa</strong> yang memiliki umur Tugas Akhir &gt; 1 tahun. Dari {$waktu_kritis_count} mahasiswa ini, ada <strong>{$kritis_belum} orang</strong> yang belum ada progres sejak judul ditetapkan, sedangkan <strong>{$kritis_ada} mahasiswa</strong> sisanya sudah melakukan Sempro atau Semhas. ";
            }
            
            $narasi .= "<br><br>";
            
            if ($lulus_total > 0) {
                if ($lulus_tepat_waktu_rate > 0) {
                    $narasi .= "Dari <strong>{$lulus_total}</strong> mahasiswa yang sudah lulus sidang, terdapat <strong>{$lulus_tepat_waktu_rate}%</strong> yang lulus tepat waktu.";
                } else {
                    $narasi .= "Dari <strong>{$lulus_total}</strong> mahasiswa yang sudah lulus sidang, tidak ada yang lulus tepat waktu.";
                }
            } else {
                $narasi .= "Belum ada data riwayat kelulusan sidang untuk menghitung performa ketepatan waktu.";
            }
            
            if (!empty($data['dosen_beban'])) {
                $beban_real = $data['dosen_beban'];
                if (isset($beban_real['Belum Ditentukan'])) {
                    unset($beban_real['Belum Ditentukan']);
                }
                
                if (!empty($beban_real)) {
                    arsort($beban_real);
                    $top_dosen = array_key_first($beban_real);
                    $top_beban = $beban_real[$top_dosen];
                    $narasi .= " Dari sisi pendampingan, beban bimbingan tertinggi saat ini dipegang oleh <strong>{$top_dosen}</strong> dengan total <strong>{$top_beban} mahasiswa aktif</strong> (belum lulus sidang).";
                } else {
                    $narasi .= " Saat ini, belum ada penugasan Dosen Pembimbing yang ditetapkan pada mahasiswa aktif.";
                }
            }
            
            if (!empty($data['dosen_nonaktif_list'])) {
                $total_affected = array_sum($data['dosen_nonaktif_list']);
                $dosen_str_parts = [];
                foreach ($data['dosen_nonaktif_list'] as $d_name => $d_count) {
                    $dosen_str_parts[] = "{$d_name} ({$d_count} bimbingan)";
                }
                $dosen_str = implode(", ", $dosen_str_parts);
                
                $narasi .= " <br><strong style=\"color: #E53E3E;\"><i class=\"fa fa-warning\"></i> PERHATIAN:</strong> Terdapat <strong>{$total_affected} kasus mahasiswa aktif</strong> yang tercatat dibimbing oleh Dosen dengan status ikatan kerja tidak aktif (X). Rincian dosen: <strong>{$dosen_str}</strong>. Mohon segera diproses untuk pergantian pembimbing.";
            }

            $krs_menghilang = $data['krs_menghilang'];
            if ($krs_menghilang > 0) {
                $narasi .= " Di luar itu, perlu diwaspadai adanya <strong>{$krs_menghilang} mahasiswa</strong> yang tercatat tidak aktif mengisi KRS pada semester ini (menghilang).";
            } else {
                $narasi .= " Seluruh mahasiswa tercatat tertib secara administrasi dan berstatus aktif mengisi KRS pada semester ini.";
            }

            $narasi .= "<br><br><strong>Analisis Waktu Antar-Tahapan:</strong>";
            
            $avg_judul_sempro = 0;
            $avg_sempro_semhas = 0;
            $avg_semhas_sidang = 0;
            
            $has_data = false;
            
            if ($data['count_judul_sempro'] > 0 || $data['count_sempro_semhas'] > 0 || $data['count_semhas_sidang'] > 0 ||
                $data['count_judul_sempro_ideal'] > 0 || $data['count_sempro_semhas_ideal'] > 0 || $data['count_semhas_sidang_ideal'] > 0) {
                $has_data = true;
            }
            
            if ($has_data) {
                if ($data['count_judul_sempro'] > 0) $avg_judul_sempro = round($data['sum_judul_sempro'] / $data['count_judul_sempro'] / 86400);
                if ($data['count_sempro_semhas'] > 0) $avg_sempro_semhas = round($data['sum_sempro_semhas'] / $data['count_sempro_semhas'] / 86400);
                if ($data['count_semhas_sidang'] > 0) $avg_semhas_sidang = round($data['sum_semhas_sidang'] / $data['count_semhas_sidang'] / 86400);
                
                $total_avg_current = $avg_judul_sempro + $avg_sempro_semhas + $avg_semhas_sidang;
                
                $avg_js_ideal = $data['count_judul_sempro_ideal'] > 0 ? round($data['sum_judul_sempro_ideal'] / $data['count_judul_sempro_ideal'] / 86400) : 0;
                $avg_ss_ideal = $data['count_sempro_semhas_ideal'] > 0 ? round($data['sum_sempro_semhas_ideal'] / $data['count_sempro_semhas_ideal'] / 86400) : 0;
                $avg_hs_ideal = $data['count_semhas_sidang_ideal'] > 0 ? round($data['sum_semhas_sidang_ideal'] / $data['count_semhas_sidang_ideal'] / 86400) : 0;
                
                $total_avg_ideal = $avg_js_ideal + $avg_ss_ideal + $avg_hs_ideal;
                $std_ideal = $total_avg_ideal > 0 ? $total_avg_ideal : 360;
                
                $n_sample = max($data['count_judul_sempro_ideal'], $data['count_sempro_semhas_ideal'], $data['count_semhas_sidang_ideal']);
                $n_text = $n_sample > 0 ? "<br><small style=\"font-weight: normal; font-size: 0.85em; color: #6c757d;\">(Sampel: {$n_sample} mhs)</small>" : "";
                
                $n_current_sample = max($data['count_judul_sempro'], $data['count_sempro_semhas'], $data['count_semhas_sidang']);
                $n_current_text = $n_current_sample > 0 ? "<br><small style=\"font-weight: normal; font-size: 0.85em; color: #6c757d;\">(Sampel: {$n_current_sample} mhs aktif)</small>" : "";
                
                // Build HTML Table
                $narasi .= "
                <div class=\"table-responsive mt-2 mb-2\">
                    <table class=\"table table-bordered table-sm text-center\" style=\"font-size: 0.9em; background-color: white;\">
                        <thead style=\"background-color: #f8f9fa;\">
                            <tr>
                                <th class=\"text-left align-middle\">Tahapan</th>
                                <th class=\"align-middle\">Rata-Rata Saat Ini{$n_current_text}</th>
                                <th class=\"align-middle\">Standar Ideal Tepat Waktu{$n_text}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class=\"text-left\">Penetapan Judul &rarr; Sempro</td>
                                <td>" . ($avg_judul_sempro > 0 ? "{$avg_judul_sempro} hari" : "-") . "</td>
                                <td>" . ($avg_js_ideal > 0 ? "{$avg_js_ideal} hari" : "-") . "</td>
                            </tr>
                            <tr>
                                <td class=\"text-left\">Sempro &rarr; Semhas</td>
                                <td>" . ($avg_sempro_semhas > 0 ? "{$avg_sempro_semhas} hari" : "-") . "</td>
                                <td>" . ($avg_ss_ideal > 0 ? "{$avg_ss_ideal} hari" : "-") . "</td>
                            </tr>
                            <tr>
                                <td class=\"text-left\">Semhas &rarr; Sidang</td>
                                <td>" . ($avg_semhas_sidang > 0 ? "{$avg_semhas_sidang} hari" : "-") . "</td>
                                <td>" . ($avg_hs_ideal > 0 ? "{$avg_hs_ideal} hari" : "-") . "</td>
                            </tr>
                        </tbody>
                        <tfoot style=\"background-color: #e9ecef; font-weight: bold;\">
                            <tr>
                                <td class=\"text-left\">Total Akumulasi Waktu</td>
                                <td>{$total_avg_current} hari</td>
                                <td>{$std_ideal} hari</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>";
                
                // Prediction logic text
                $prediksi = "";
                if ($total_avg_current > $std_ideal) {
                    $prediksi = "Berdasarkan komparasi pada tabel di atas, standar ideal waktu penyelesaian adalah sekitar <b>{$std_ideal} hari</b>. Namun melihat akumulasi waktu rata-rata prodi saat ini (<b>{$total_avg_current} hari</b>), diprediksi mahasiswa aktif akan <span class=\"text-danger fw-bold\">kesulitan mencapai target lulus tepat waktu</span> jika jarak antar-tahapan tidak dipersingkat.";
                } else {
                    if ($total_avg_current == 0) {
                        if ($data['belum_disetujui'] > 0) {
                            $prediksi = "<span class=\"text-danger fw-bold\">Saat ini belum ada mahasiswa aktif yang mencapai tahapan lanjut. Terdapat potensi besar mahasiswa tidak akan lulus tepat waktu jika usulan judul belum disetujui oleh Kaprodi dan penetapan Dosen Pembimbing terus tertunda.</span>";
                        } else {
                            $prediksi = "Saat ini belum ada mahasiswa yang menyelesaikan tahapan awal, sehingga prediksi ketepatan waktu belum dapat dikalkulasi secara akurat.";
                        }
                    } else {
                        $prediksi = "Berdasarkan komparasi pada tabel di atas, diprediksi mahasiswa aktif saat ini masih memiliki <span class=\"text-success fw-bold\">peluang besar untuk lulus tepat waktu</span>.";
                    }
                }
                $narasi .= $prediksi;
                
                // Construct Early Warning System alerts
                $stuck_js_count = 0;
                if ($avg_js_ideal > 0) {
                    foreach ($data['stuck_judul_sempro'] as $d) {
                        if (floor($d / 86400) > $avg_js_ideal) $stuck_js_count++;
                    }
                }
                
                $stuck_ss_count = 0;
                if ($avg_ss_ideal > 0) {
                    foreach ($data['stuck_sempro_semhas'] as $d) {
                        if (floor($d / 86400) > $avg_ss_ideal) $stuck_ss_count++;
                    }
                }
                
                $stuck_hs_count = 0;
                if ($avg_hs_ideal > 0) {
                    foreach ($data['stuck_semhas_sidang'] as $d) {
                        if (floor($d / 86400) > $avg_hs_ideal) $stuck_hs_count++;
                    }
                }
                
                $warnings = [];
                if ($stuck_js_count > 0) {
                    $warnings[] = "&bull; Terdapat <b>{$stuck_js_count} mahasiswa</b> yang terancam mandek karena belum melaksanakan Sempro, melampaui batas waktu ideal ({$avg_js_ideal} hari).";
                }
                if ($stuck_ss_count > 0) {
                    $warnings[] = "&bull; Terdapat <b>{$stuck_ss_count} mahasiswa</b> yang terancam mandek karena belum melaksanakan Semhas, melampaui batas waktu ideal ({$avg_ss_ideal} hari).";
                }
                if ($stuck_hs_count > 0) {
                    $warnings[] = "&bull; Terdapat <b>{$stuck_hs_count} mahasiswa</b> yang terancam mandek karena belum mendaftar Sidang, melampaui batas waktu ideal ({$avg_hs_ideal} hari).";
                }
                
                if (!empty($warnings)) {
                    $narasi .= "
                    <div class=\"alert alert-danger mt-3 mb-1\" style=\"font-size: 0.9em; padding: 12px; border-left: 4px solid #dc3545; background-color: #f8d7da; color: #721c24;\">
                        <strong><i class=\"fa fa-exclamation-triangle mr-1\"></i> EARLY WARNING SYSTEM (Potensi Terlambat):</strong><br>
                        " . implode("<br>", $warnings) . "
                    </div>";
                }
                
            } else {
                $narasi .= " Belum ada riwayat rentang waktu antar-tahapan yang valid untuk dianalisis.";
            }
            
            if ($data['anomali_kilat'] > 0) {
                $narasi .= " Ditemukan juga anomali pada <strong>{$data['anomali_kilat']} mahasiswa</strong> di mana jarak dari penetapan judul hingga Sempro terlampau singkat (di bawah 1 minggu), yang mungkin memerlukan verifikasi kelengkapan administratif tanggal.";
            }
            
            if ($data['belum_disetujui'] > 0) {
                $narasi .= " <br><strong style=\"color: #D69E2E;\"><i class=\"fa fa-exclamation-circle\"></i> INFO:</strong> Terdapat <strong>{$data['belum_disetujui']} mahasiswa</strong> yang sudah mengajukan judul, tetapi belum diproses/disetujui oleh Kaprodi (menunggu penetapan). Mohon untuk segera ditindaklanjuti.";
            }

            $data['narasi'] = $narasi;
        }
        
        // Rounding overall rates
        $strong_rate = round($strong_rate, 1);
        $weak_rate = round($weak_rate, 1);
        $weak_stagnant_rate = 100 - $weak_rate; 
        
        $strong_point = !empty($strong_prodi) ? "Program Studi <strong>{$strong_prodi}</strong> menunjukkan performa terbaik dengan tingkat kemajuan Tugas Akhir mencapai <strong>{$strong_rate}%</strong> (mahasiswa yang telah melewati tahapan Seminar Proposal)." : "Belum ada data kemajuan prodi.";
        
        $weak_point = !empty($weak_prodi) ? "Program Studi <strong>{$weak_prodi}</strong> memerlukan perhatian khusus karena memiliki tingkat stagnansi tertinggi sebesar <strong>{$weak_stagnant_rate}%</strong> (mahasiswa yang masih berada pada tahap penetapan judul dan belum Seminar Proposal)." : "Belum ada data kendala prodi.";
        
        $result = (object)[
            'tahun_ajaran_aktif' => $tahun_ajaran_aktif,
            'total' => $total,
            'potensi_tinggi' => $potensi_tinggi,
            'potensi_sedang' => $potensi_sedang,
            'potensi_rendah' => $potensi_rendah,
            'avg_days' => $avg_days,
            'prodi_details' => $prodi_details,
            'strong_point' => $strong_point,
            'weak_point' => $weak_point
        ];

        // Simpan ke cache selama 2 jam (7200 detik)
        $this->cache->save($cache_name, $result, 7200);

        return $result;
    }

    public function get_timeline_feed()
    {
        $limit = (int)$this->input->get('limit');
        $offset = (int)$this->input->get('offset');
        if ($limit <= 0) $limit = 10;
        if ($offset < 0) $offset = 0;
        
        $prodi = $this->input->get('prodi');
        $statuses = $this->input->get('statuses');
        if (!is_array($statuses)) {
            $statuses = [];
        }

        // Tentukan nama cache berdasarkan parameter filter
        $cache_key = md5(json_encode(['limit' => $limit, 'offset' => $offset, 'prodi' => $prodi, 'statuses' => $statuses]));
        $cache_name = 'linimasa_feed_' . $cache_key;
        
        $cache_data = $this->cache->get($cache_name);
        if ($cache_data !== FALSE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output($cache_data);
            return;
        }

        $setting = $this->db->get('siakad_setting')->row();
        $tahun_ajaran_aktif = $setting ? $setting->tahun_ajaran_aktif : '';

        $where_clauses = ["(mp.id_jns_keluar IS NULL OR mp.id_jns_keluar != '1')", "j.status_sk != 3"];
        
        if (!empty($prodi) && $prodi !== 'WBI') {
            $where_clauses[] = "s.nm_lemb = " . $this->db->escape($prodi);
        }

        // Handle Status filters
        if (in_array('kritis', $statuses)) {
            $where_clauses[] = "DATEDIFF(CURDATE(), COALESCE(j.tanggal_disetujui, j.tanggal_sk, j.tanggal_diajukan)) > 365";
        }
        


        $having_clauses = [];
        if (in_array('judul_saja', $statuses)) {
            $having_clauses[] = "sempro_layak = 0 AND semhas_layak = 0 AND sidang_lulus = 0";
        }
        if (in_array('sempro', $statuses)) {
            $having_clauses[] = "sempro_layak > 0 AND semhas_layak = 0 AND sidang_lulus = 0";
        }
        if (in_array('semhas', $statuses)) {
            $having_clauses[] = "semhas_layak > 0 AND sidang_lulus = 0";
        }
        if (in_array('sidang', $statuses)) {
            $having_clauses[] = "sidang_lulus > 0";
        }
        if (in_array('tepat_waktu', $statuses)) {
            $having_clauses[] = "sidang_lulus > 0 AND DATEDIFF(MAX(ns.sidang_tanggal), MAX(COALESCE(j.tanggal_disetujui, j.tanggal_sk, j.tanggal_diajukan))) <= 360 AND (YEAR(MAX(ns.sidang_tanggal)) - COALESCE(FLOOR(MAX(mp.mulai_smt)/10), 2000 + CAST(SUBSTRING(MAX(j.nipd), 1, 2) AS UNSIGNED))) <= " . self::BATAS_TEPAT_WAKTU;
        }

        $where_sql = implode(' AND ', $where_clauses);
        $having_sql = !empty($having_clauses) ? "HAVING " . implode(' AND ', $having_clauses) : "";

        // Query to get Total Count
        $count_query_str = "
            SELECT COUNT(*) as total_count FROM (
                SELECT j.id_judul_skripsi,
                       SUM(CASE WHEN UPPER(ns.sempro_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as sempro_layak,
                       SUM(CASE WHEN UPPER(ns.semhas_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as semhas_layak,
                       SUM(CASE WHEN UPPER(ns.sidang_hasil) = 'LULUS' THEN 1 ELSE 0 END) as sidang_lulus
                FROM siakad_skripsi_pengajuan_judul j
                JOIN mahasiswa_pt mp ON mp.nipd = j.nipd
                JOIN mahasiswa m ON m.id_pd = mp.id_pd
                JOIN sms s ON s.id_sms = j.id_sms
                LEFT JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = j.id_judul_skripsi
                WHERE $where_sql
                GROUP BY j.id_judul_skripsi
                $having_sql
            ) AS count_table
        ";
        $total_count = $this->db->query($count_query_str)->row()->total_count;

        $query_str = "
            SELECT 
                j.id_judul_skripsi,
                j.nipd,
                j.judul_ditetapkan,
                COALESCE(j.tanggal_disetujui, j.tanggal_sk, j.tanggal_diajukan) as tgl_penetapan,
                m.nm_pd,
                m.foto,
                s.nm_lemb as prodi,
                sd1.nm_ptk as pembimbing_1_name,
                sd2.nm_ptk as pembimbing_2_name,
                sdw.nm_ptk as dosen_wali_name,
                SUM(CASE WHEN UPPER(ns.sempro_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as sempro_layak,
                SUM(CASE WHEN UPPER(ns.semhas_hasil) = 'LAYAK' THEN 1 ELSE 0 END) as semhas_layak,
                SUM(CASE WHEN UPPER(ns.sidang_hasil) = 'LULUS' THEN 1 ELSE 0 END) as sidang_lulus,
                MAX(ns.seminar_tanggal) as sempro_tanggal,
                MAX(ns.semhas_tanggal) as semhas_tanggal,
                MAX(ns.sidang_tanggal) as sidang_tanggal,
                MAX(ns.sidang_nilai_mutu) as sidang_nilai_mutu,
                mp.mulai_smt
            FROM siakad_skripsi_pengajuan_judul j
            JOIN mahasiswa_pt mp ON mp.nipd = j.nipd
            JOIN mahasiswa m ON m.id_pd = mp.id_pd
            JOIN sms s ON s.id_sms = j.id_sms
            LEFT JOIN siakad_dosen sd1 ON sd1.id_ptk = j.dosen_pembimbing_1
            LEFT JOIN siakad_dosen sd2 ON sd2.id_ptk = j.dosen_pembimbing_2
            LEFT JOIN siakad_dosen sdw ON sdw.id_ptk = m.dosen_pa
            LEFT JOIN siakad_skripsi_nilai_sempro ns ON ns.id_judul_skripsi = j.id_judul_skripsi
            WHERE $where_sql
            GROUP BY 
                j.id_judul_skripsi, 
                j.nipd, 
                j.judul_ditetapkan, 
                j.tanggal_disetujui, 
                j.tanggal_sk, 
                j.tanggal_diajukan, 
                m.nm_pd, 
                m.foto, 
                s.nm_lemb, 
                sd1.nm_ptk, 
                sd2.nm_ptk,
                sdw.nm_ptk,
                mp.mulai_smt
            $having_sql
            ORDER BY tgl_penetapan ASC
            LIMIT ? OFFSET ?
        ";
        
        $result = $this->db->query($query_str, [$limit, $offset])->result();
        
        // Optimasi: Bulk fetch KRS info to prevent N+1 query issue
        $nipds = [];
        foreach ($result as $r) {
            $nipds[] = $r->nipd;
        }
        $krs_map = [];
        if (!empty($nipds)) {
            $in_nipds = "'" . implode("','", $nipds) . "'";
            $krs_bulk_query = "
                SELECT k.nipd, k.id_smt, COALESCE(k.approved_date, k.create_date) as krs_date
                FROM siakad_krs_detail kd
                JOIN siakad_krs k ON k.id_rencana_studi = kd.id_rencana_studi
                JOIN feeder_kelas_kuliah kk ON kk.id_kls = kd.id_kls
                JOIN feeder_mata_kuliah fmk ON fmk.id_mk = kk.id_mk
                WHERE k.nipd IN ($in_nipds) AND (fmk.nm_mk LIKE '%tugas akhir%' OR fmk.nm_mk LIKE '%skripsi%')
                ORDER BY k.id_smt DESC
            ";
            $krs_bulk_result = $this->db->query($krs_bulk_query)->result();
            foreach ($krs_bulk_result as $kr) {
                $krs_map[$kr->nipd] ??= $kr;
            }
        }
        
        foreach ($result as &$row) {
            $row->umur_hari = 0;
            $row->tanggal_formatted = '-';
            if (!empty($row->tgl_penetapan)) {
                $tgl = strtotime($row->tgl_penetapan);
                if ($tgl > 0) {
                    if ($row->sidang_lulus > 0 && !empty($row->sidang_tanggal)) {
                        $diff = strtotime($row->sidang_tanggal) - $tgl;
                    } else {
                        $diff = time() - $tgl;
                    }
                    if ($diff < 0) $diff = 0;
                    
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                    
                    $total_days = floor($diff / (60*60*24));
                    $row->umur_hari = $total_days;
                    
                    $umur_text = [];
                    if ($years > 0) $umur_text[] = "$years thn";
                    if ($months > 0) $umur_text[] = "$months bln";
                    if ($days > 0) $umur_text[] = "$days hari";
                    
                    $row->umur_hari_text = !empty($umur_text) ? implode(' ', $umur_text) : '0 hari';
                    $row->tanggal_formatted = date('d M Y', $tgl);
                }
            }
            
            // Hitung flag is_lulus_tepat_waktu yang sebenarnya
            $row->is_lulus_tepat_waktu = false;
            if ($row->sidang_lulus > 0 && !empty($row->sidang_tanggal)) {
                if (!empty($row->mulai_smt)) {
                    $mulai_thn = floor($row->mulai_smt / 10);
                } elseif (!empty($row->nipd)) {
                    $angkatan_str = substr(trim((string)$row->nipd), 0, 2);
                    $mulai_thn = 2000 + (int)$angkatan_str;
                } else {
                    $tgl_p = !empty($row->tgl_penetapan) ? strtotime($row->tgl_penetapan) : time();
                    $mulai_thn = date('Y', $tgl_p) - self::BATAS_TEPAT_WAKTU;
                }
                
                $lulus_thn = date('Y', strtotime($row->sidang_tanggal));
                $masa_studi = $lulus_thn - $mulai_thn;
                
                if ($row->umur_hari <= 360 && $masa_studi <= self::BATAS_TEPAT_WAKTU) {
                    $row->is_lulus_tepat_waktu = true;
                }
            }

            if (empty($row->judul_ditetapkan)) {
                $row->judul_ditetapkan = '(Judul belum ditetapkan / sedang diajukan)';
            }
            
            // Format milestone dates
            $row->sempro_tanggal_fmt = !empty($row->sempro_tanggal) && $row->sempro_layak > 0 ? date('d M Y', strtotime($row->sempro_tanggal)) : '';
            $row->semhas_tanggal_fmt = !empty($row->semhas_tanggal) && $row->semhas_layak > 0 ? date('d M Y', strtotime($row->semhas_tanggal)) : '';
            $row->sidang_tanggal_fmt = !empty($row->sidang_tanggal) && $row->sidang_lulus > 0 ? date('d M Y', strtotime($row->sidang_tanggal)) : '';
            
            $row->avatar_url = !empty($row->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $row->foto : '';
            
            // Fetch KRS Info from bulk fetched map (O(1) lookup, no N+1 query)
            if (isset($krs_map[$row->nipd])) {
                $krs_row = $krs_map[$row->nipd];
                $row->krs_smt = $krs_row->id_smt;
                $row->krs_date_fmt = !empty($krs_row->krs_date) ? date('d M Y', strtotime($krs_row->krs_date)) : '-';
            } else {
                $row->krs_smt = null;
                $row->krs_date_fmt = null;
            }
        }
        
        $json_output = json_encode([
            'status' => true,
            'total_count' => $total_count,
            'data' => $result
        ]);

        // Simpan ke cache selama 30 menit (1800 detik)
        $this->cache->save($cache_name, $json_output, 1800);

        $this->output
            ->set_content_type('application/json')
            ->set_output($json_output);
    }
}
