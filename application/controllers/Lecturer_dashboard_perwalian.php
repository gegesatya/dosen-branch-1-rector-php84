<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_dashboard_perwalian extends CI_Controller
{
    const TARGET_IPK_TAHUN_1 = 2.50;
    const TARGET_IPK_TAHUN_2 = 2.75;
    const TARGET_IPK_TAHUN_3 = 2.75;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_lecturer_dashboard_perwalian', 'dashboard_model');
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $group = ['dosen'];
        if (!$this->ion_auth->in_group($group)) {
            redirect('auth/logout', 'refresh');
        }
    }

    public function index()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');

        $prediksi_params = [
            'ipk_tahun_1' => self::TARGET_IPK_TAHUN_1,
            'ipk_tahun_2' => self::TARGET_IPK_TAHUN_2,
            'ipk_tahun_3' => self::TARGET_IPK_TAHUN_3,
        ];

        $data['kpi']        = $this->dashboard_model->get_kpi_perwalian($id_ptk, $id_smt);
        // Data lain akan diload via AJAX (step-by-step) untuk mempercepat initial load
        $data['krs_status'] = [];
        $data['clustering'] = [];
        $data['belum_krs']  = [];
        $data['anomali_akademik'] = [];

        $output['judul_besar'] = 'Dashboard Perwalian';
        $output['judul_kecil'] = 'Analitik & Prediksi Mahasiswa Bimbingan';
        $output['menu_aktif']  = 'm_dashboard_perwalian';
        $output['parent_1']    = 'active open';
        $output['content']     = 'perwalian/dashboard_perwalian_view';
        $output['data']        = $data;

        $this->load->view('master_page', $output);
    }

    public function ajax_get_prediksi_risiko()
    {
        ob_start();

        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');

        if (!$id_ptk) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid']);
            return;
        }

        $tab = $this->input->get('tab');

        $prediksi_params = [
            'ipk_tahun_1' => self::TARGET_IPK_TAHUN_1,
            'ipk_tahun_2' => self::TARGET_IPK_TAHUN_2,
            'ipk_tahun_3' => self::TARGET_IPK_TAHUN_3,
        ];

        $prediksi = $this->dashboard_model->get_student_predictions($id_ptk, $id_smt, $prediksi_params);

        if ($tab && isset($prediksi[$tab])) {
            $data = $prediksi[$tab];
        } else {
            $data = $prediksi;
        }

        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data'   => $data,
            'tab'    => $tab
        ]);
    }

    public function ajax_load_panel_atas()
    {
        ob_start();
        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');

        if (!$id_ptk) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid']);
            return;
        }

        $this->load->driver('cache', ['adapter' => 'file', 'backup' => 'default']);
        $cache_key = 'dash_perwalian_atas_' . $id_ptk . '_' . $id_smt;
        $cached_response = $this->cache->get($cache_key);

        if ($cached_response !== FALSE) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo $cached_response;
            return;
        }

        $krs_status = $this->dashboard_model->get_krs_status_by_angkatan($id_ptk, $id_smt);
        $clustering = $this->dashboard_model->get_prestasi_clusters($id_ptk);
        
        $data['clustering'] = $clustering;
        $html_clustering_list = $this->load->view('perwalian/partial_clustering_list', $data, true);
        $html_clustering_modal = $this->load->view('perwalian/partial_clustering_modal', $data, true);

        $response_json = json_encode([
            'status' => 'success',
            'krs_status' => $krs_status,
            'clustering' => $clustering,
            'html_clustering_list' => $html_clustering_list,
            'html_clustering_modal' => $html_clustering_modal
        ]);

        // Simpan ke cache selama 3600 detik (1 jam)
        $this->cache->save($cache_key, $response_json, 3600);

        ob_end_clean();
        header('Content-Type: application/json');
        echo $response_json;
    }

    public function ajax_load_panel_bawah()
    {
        ob_start();
        $id_ptk = $this->session->userdata('id_ptk');
        $id_smt = $this->session->userdata('tahun_ajaran');

        if (!$id_ptk) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid']);
            return;
        }

        $this->load->driver('cache', ['adapter' => 'file', 'backup' => 'default']);
        $cache_key = 'dash_perwalian_bawah_' . $id_ptk . '_' . $id_smt;
        $cached_response = $this->cache->get($cache_key);

        if ($cached_response !== FALSE) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo $cached_response;
            return;
        }

        $data['belum_krs'] = $this->dashboard_model->get_mahasiswa_belum_krs($id_ptk, $id_smt);
        $data['anomali_akademik'] = $this->dashboard_model->get_mahasiswa_anomali_krs($id_ptk, $id_smt);

        $html_anomali = $this->load->view('perwalian/partial_anomali_akademik', $data, true);
        $html_belum_krs = $this->load->view('perwalian/partial_belum_krs', $data, true);

        $response_json = json_encode([
            'status' => 'success',
            'html_anomali' => $html_anomali,
            'html_belum_krs' => $html_belum_krs,
            'count_belum_krs' => count($data['belum_krs'])
        ]);

        // Simpan ke cache selama 3600 detik (1 jam)
        $this->cache->save($cache_key, $response_json, 3600);

        ob_end_clean();
        header('Content-Type: application/json');
        echo $response_json;
    }

    public function ajax_get_entrepreneurship_mindset()
    {
        ob_start();

        $nipd = $this->input->post('nipd');
        if (!$nipd) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'NIPD tidak valid']);
            return;
        }

        $skill_data = $this->_get_skill_data($nipd);

        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data'   => $skill_data
        ]);
    }

    private function _get_skill_data($nipd)
    {
        $grades = $this->dashboard_model->get_entrepreneurship_grades($nipd);

        $conclusion = "Belum ada data nilai mata kuliah Entrepreneurship (E1-E5) yang diselesaikan oleh mahasiswa ini.";

        if (count($grades) > 0) {
            // Hanya hitung tren dari MK yang sudah ada nilainya (indeks > 0)
            $trend          = [];
            $mk_tidak_lulus = [];

            foreach ($grades as $g) {
                if ($g['nilai_indeks'] > 0) {
                    $trend[] = $g['nilai_indeks'];
                }
                // Deteksi nilai tidak lulus: D atau E
                $huruf = strtoupper(trim($g['nilai_huruf'] ?? ''));
                if (in_array($huruf, ['D', 'E'])) {
                    $mk_tidak_lulus[] = $g['kode_e'] . ' (' . $g['nm_mk'] . ')';
                }
            }

            $jumlah_terisi = count($trend);

            if ($jumlah_terisi === 0) {
                $conclusion = "<span style='color:#4e73df'><i class='fa fa-info-circle'></i> <strong>Data Belum Tersedia:</strong> Mahasiswa sudah terdaftar di mata kuliah entrepreneurship, namun nilai belum diinput.</span>";
            } elseif ($jumlah_terisi === 1) {
                $conclusion = "<span style='color:#4e73df'><i class='fa fa-info-circle'></i> <strong>Data Awal:</strong> Baru 1 mata kuliah entrepreneurship yang memiliki nilai, sehingga tren belum dapat dibandingkan.</span>";
            } else {
                $is_improving = true;
                $is_declining = true;

                for ($i = 1; $i < $jumlah_terisi; $i++) {
                    if ($trend[$i] < $trend[$i - 1]) $is_improving = false;
                    if ($trend[$i] > $trend[$i - 1]) $is_declining = false;
                }

                if ($is_improving) {
                    $conclusion = "<span style='color:#4e73df'><i class='fa fa-arrow-up'></i> <strong>Tren Positif:</strong> Berdasarkan {$jumlah_terisi} nilai yang tersedia, terjadi peningkatan atau kestabilan performa yang konsisten. Mindset entrepreneurship berkembang dengan baik.</span>";
                } elseif ($is_declining) {
                    $conclusion = "<span style='color:#4e73df'><i class='fa fa-arrow-down'></i> <strong>Tren Menurun:</strong> Berdasarkan {$jumlah_terisi} nilai yang tersedia, terdapat penurunan performa pada tahapan entrepreneurship lanjutan. Perlu perhatian dan pendampingan lebih.</span>";
                } else {
                    $conclusion = "<span style='color:#4e73df'><i class='fa fa-minus'></i> <strong>Tren Fluktuatif:</strong> Berdasarkan {$jumlah_terisi} nilai yang tersedia, performa mahasiswa naik-turun. Pemahaman belum sepenuhnya konsisten di setiap tahapan.</span>";
                }
            }

            // Tambahkan rekomendasi kuat jika ada nilai tidak lulus (D/E)
            if (!empty($mk_tidak_lulus)) {
                $mk_list     = implode(', ', $mk_tidak_lulus);
                $conclusion .= '<div style="margin-top:1.25rem;padding:1.25rem 1.5rem;background:#fff5f5;border-left:6px solid #e74a3b;border-radius:0.75rem;">'
                             . '<p style="margin:0 0 0.5rem;font-size:1.2rem;font-weight:800;color:#c0392b;">'
                             . '<i class="fa fa-exclamation-triangle"></i>&nbsp; Rekomendasi Tindakan Segera</p>'
                             . '<p style="margin:0;color:#c0392b;font-size:1.1rem;line-height:1.8;">'
                             . 'Mahasiswa memiliki nilai <strong>tidak lulus (D/E)</strong> pada: <strong>' . $mk_list . '</strong>. '
                             . 'Dosen PA disarankan untuk <strong>segera melakukan perwalian khusus</strong> dan mendorong mahasiswa mengulang mata kuliah tersebut pada semester berikutnya.'
                             . '</p></div>';
            }
        }
        
        // --- Analisis Kemampuan Bahasa ---
        $lang_grades = $this->dashboard_model->get_language_grades($nipd);
        $lang_conclusion = "Belum ada data nilai mata kuliah Bahasa (Indonesia/Inggris) yang diselesaikan.";
        
        if (count($lang_grades) > 0) {
            $lang_trend = [];
            $lang_tidak_lulus = [];
            
            foreach ($lang_grades as $g) {
                if ($g['nilai_indeks'] > 0) {
                    $lang_trend[] = $g['nilai_indeks'];
                }
                $huruf = strtoupper(trim($g['nilai_huruf'] ?? ''));
                if (in_array($huruf, ['D', 'E'])) {
                    $lang_tidak_lulus[] = $g['kode_lang'] . ' (' . $g['nm_mk'] . ')';
                }
            }
            
            $jml_lang = count($lang_trend);
            
            if ($jml_lang === 0) {
                $lang_conclusion = "<span style='color:#4e73df'><i class='fa fa-info-circle'></i> <strong>Data Belum Tersedia:</strong> Mahasiswa sudah terdaftar di mata kuliah bahasa, namun nilai belum diinput.</span>";
            } else {
                $avg_lang = array_sum($lang_trend) / $jml_lang;
                if ($avg_lang >= 3.5) {
                    $lang_conclusion = "<span style='color:#4e73df'><i class='fa fa-check-circle'></i> <strong>Sangat Baik:</strong> Rata-rata indeks prestasi bahasa mahasiswa sangat baik (" . number_format($avg_lang, 2) . "). Kemampuan komunikasi dan literasi bahasa sangat memadai.</span>";
                } elseif ($avg_lang >= 2.5) {
                    $lang_conclusion = "<span style='color:#4e73df'><i class='fa fa-check'></i> <strong>Cukup Baik:</strong> Kemampuan bahasa mahasiswa cukup memadai dengan rata-rata indeks " . number_format($avg_lang, 2) . ".</span>";
                } else {
                    $lang_conclusion = "<span style='color:#4e73df'><i class='fa fa-warning'></i> <strong>Kurang:</strong> Performa bahasa mahasiswa di bawah standar (rata-rata " . number_format($avg_lang, 2) . "). Diperlukan peningkatan kemampuan literasi.</span>";
                }
            }
            
            // Rekomendasi bahasa
            if (!empty($lang_tidak_lulus)) {
                $lang_list = implode(', ', $lang_tidak_lulus);
                $lang_conclusion .= '<div style="margin-top:1.25rem;padding:1.25rem 1.5rem;background:#fff5f5;border-left:6px solid #e74a3b;border-radius:0.75rem;">'
                                  . '<p style="margin:0 0 0.5rem;font-size:1.2rem;font-weight:800;color:#c0392b;">'
                                  . '<i class="fa fa-exclamation-triangle"></i>&nbsp; Perhatian pada Kemampuan Bahasa</p>'
                                  . '<p style="margin:0;color:#c0392b;font-size:1.1rem;line-height:1.8;">'
                                  . 'Mahasiswa memiliki nilai <strong>tidak lulus (D/E)</strong> pada: <strong>' . $lang_list . '</strong>. '
                                  . 'Mengingat pentingnya kemampuan komunikasi, mohon disarankan untuk segera mengulang.'
                                  . '</p></div>';
            }
        }

        return [
            'grades'          => $grades,
            'conclusion'      => $conclusion,
            'lang_grades'     => $lang_grades,
            'lang_conclusion' => $lang_conclusion
        ];
    }

    public function ajax_get_student_detail()
    {
        $nipd = $this->input->post('nipd');
        if (!$nipd) {
            echo json_encode(['status' => 'error', 'message' => 'NIM tidak ditemukan.']);
            return;
        }

        // 1. Basic Info
        $mahasiswa = $this->db->where('mp.nipd', $nipd)
            ->select('m.nm_pd, mp.nipd, foto, mp.tgl_masuk_sp, mp.id_sms, ps.nm_lemb as nm_prodi')
            ->from('mahasiswa m')
            ->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
            ->join('sms ps', 'ps.id_sms = mp.id_sms', 'left')
            ->get()->row();

        if (!$mahasiswa) {
            echo json_encode(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan.']);
            return;
        }

        $fallback_url = "https://ui-avatars.com/api/?name=".urlencode($mahasiswa->nm_pd)."&background=random&color=fff";
        $pic = $mahasiswa->foto ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $mahasiswa->foto : $fallback_url;

        // 2. KIP Status
        $this->load->model('Lecturer_persetujuan_krs_model');
        $kip_data = $this->Lecturer_persetujuan_krs_model->check_kip($nipd);
        $kip_status = "<span style='color: #858796; font-weight: 500;'>Bukan Peserta KIP</span>";
        if ($kip_data) {
            $skema_raw = !empty($kip_data->skema) ? trim((string)$kip_data->skema) : '';
            $skema_lbl = !empty($skema_raw) ? 'KIP ' . $skema_raw : 'Peserta KIP';
            $kip_status = "<span class='badge' style='background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 600; padding: 2.5px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;'><i class='fa fa-id-card-o' style='color: #2563eb; font-size: 11px;'></i> " . htmlspecialchars($skema_lbl) . "</span>";
        }

        // 3. Masa Studi
        $masa_studi_text = "-";
        if (!empty($mahasiswa->tgl_masuk_sp)) {
            $tgl_masuk = new DateTime($mahasiswa->tgl_masuk_sp);
            $tgl_sekarang = new DateTime();
            $diff = $tgl_masuk->diff($tgl_sekarang);
            $masa_studi_text = $diff->y . " Tahun " . $diff->m . " Bulan";
        }

        // 4. IPK / IPS Trend
        $data_kuliah = $this->db->where('nipd', $nipd)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
        $ips_labels = []; $ips_values = []; $ipk_values = []; $last_ipk = 0;
        $semester_tempuh = count($data_kuliah);

        foreach ($data_kuliah as $dk) {
            $ips_labels[] = $dk->id_smt;
            $ips_values[] = (float)$dk->ips;
            $ipk_values[] = (float)$dk->ipk;
            $last_ipk = (float)$dk->ipk;
        }

        // 5. Mata Kuliah D dan E (Failed)
        $query = "SELECT sn.*, mk.nm_mk, mk.kode_mk, mk.sks_mk,
                         (SELECT MIN(smt) FROM feeder_mata_kuliah_kurikulum WHERE id_mk = mk.id_mk) as smt
                  FROM siakad_nilai_baru sn 
                  JOIN feeder_kelas_kuliah kk ON kk.id_kls = sn.id_kls 
                  JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk 
                  WHERE sn.nipd = ? 
                  AND (sn.nilai_huruf = 'D' OR sn.nilai_huruf = 'E') 
                  AND NOT EXISTS (
                      SELECT 1 FROM siakad_nilai_baru sn2 
                      JOIN feeder_kelas_kuliah kk2 ON kk2.id_kls = sn2.id_kls
                      WHERE sn2.nipd = sn.nipd 
                      AND kk2.id_mk = kk.id_mk 
                      AND (sn2.nilai_huruf IN ('A', 'B', 'C', 'A-', 'B+', 'B-', 'C+'))
                  )
                  ORDER BY kk.id_smt ASC";
        $failed_courses = $this->db->query($query, [$nipd])->result();
        
        $failed_html = '';
        if (count($failed_courses) > 0) {
            $failed_html .= '<div class="bs5-table-wrapper" style="margin-top: 1rem;"><table class="bs5-table">';
            $failed_html .= '<thead><tr><th>No</th><th>Smt</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th></tr></thead><tbody>';
            $no = 1;
            foreach ($failed_courses as $fc) {
                $failed_html .= '<tr>';
                $failed_html .= '<td>' . $no++ . '</td>';
                $failed_html .= '<td>' . ($fc->smt ?? '-') . '</td>';
                $failed_html .= '<td>' . $fc->kode_mk . '</td>';
                $failed_html .= '<td>' . $fc->nm_mk . '</td>';
                $failed_html .= '<td>' . $fc->sks_mk . '</td>';
                $failed_html .= '<td><span class="bs5-badge bs5-badge-danger">' . $fc->nilai_huruf . '</span></td>';
                $failed_html .= '</tr>';
            }
            $failed_html .= '</tbody></table></div>';
        } else {
            $failed_html = '<div style="text-align:center; padding: 2rem; color: #858796;"><i class="fa fa-check-circle" style="font-size:3rem; color:#1cc88a; margin-bottom:1rem;"></i><br>Tidak ada mata kuliah dengan nilai D atau E.</div>';
        }

        // 6. Skill (Entrepreneurship & Language)
        // Reuse the logic directly instead of invoking the controller method
        $skill_data = $this->_get_skill_data($nipd);

        $has_e = false;
        foreach ($failed_courses as $fc) {
            if ($fc->nilai_huruf == 'E') {
                $has_e = true;
                break;
            }
        }
        
        $jml_judul_ta = $this->db->where('nipd', $nipd)->count_all_results('siakad_skripsi_pengajuan_judul');
        
        $academic_summary = $this->_generate_academic_narrative($mahasiswa, $ips_values, count($failed_courses), $has_e, $kip_data, $last_ipk, $jml_judul_ta, $semester_tempuh);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'nm_pd' => $mahasiswa->nm_pd,
                'nipd' => $mahasiswa->nipd,
                'pic' => $pic,
                'last_ipk' => number_format($last_ipk, 2),
                'kip_status' => $kip_status,
                'semester_tempuh' => $semester_tempuh,
                'masa_studi_text' => $masa_studi_text,
                'ipk_values' => $ipk_values,
                'ips_values' => $ips_values,
                'ips_labels' => $ips_labels,
                'failed_courses' => count($failed_courses),
                'failed_html' => $failed_html,
                'failed_html' => $failed_html,
                'skill_data' => $skill_data,
                'academic_summary' => $academic_summary
            ]
        ]);
    }

    private function _generate_academic_narrative($mahasiswa, $ips_values, $attention_count, $has_e, $kip_data, $last_ipk, $jml_judul_ta = 0, $semester_tempuh = 0)
    {
        $nm_pd = $mahasiswa->nm_pd;
        $prodi = $mahasiswa->nm_prodi ?? 'Program Studi';
        $angkatan = !empty($mahasiswa->tgl_masuk_sp) ? date('Y', strtotime($mahasiswa->tgl_masuk_sp)) : '-';

        $last_ips = end($ips_values) ?: 0;
        $is_maba = ($semester_tempuh == 0 || count($ips_values) == 0);

        // 1. Perkembangan Akademik & Tren IPS
        $intro = "Mahasiswa atas nama <strong>$nm_pd</strong>, Program Studi $prodi angkatan $angkatan, ";
        if ($is_maba) {
            $intro .= "merupakan <strong>Mahasiswa Baru</strong> yang sedang memulai perkuliahan perdana (Semester 1). ";
            $trend = "Diharapkan dapat segera beradaptasi dengan atmosfer dan metode pembelajaran di perguruan tinggi secara optimal.";
        } elseif (count($ips_values) > 1) {
            $first_ips = $ips_values[0];
            if ($last_ips >= $first_ips) {
                $trend = "menunjukkan perkembangan akademik yang cukup baik selama masa studi berjalan. Berdasarkan evaluasi capaian semester, mahasiswa mengalami peningkatan atau stabilitas IPS secara bertahap.";
            } else {
                $trend = "menunjukkan performa akademik yang fluktuatif. Diperlukan perhatian khusus untuk menjaga konsistensi capaian semester agar tidak mengalami penurunan berkelanjutan.";
            }
        } else {
            $trend = "saat ini sedang menempuh semester awal dengan capaian yang perlu terus dipantau dan dikembangkan.";
        }

        // 2. Status IPK (Emphasis on min IPK)
        if ($is_maba) {
            $ipk_text = "Sebagai mahasiswa baru, belum terdapat riwayat Indeks Prestasi Kumulatif (IPK). Mahasiswa diarahkan untuk membangun rekam jejak akademik yang unggul sejak semester pertama.";
        } else {
            $min_ipk = ($semester_tempuh < 2) ? 2.50 : 2.75;
            $ipk_val = number_format($last_ipk, 2);
            $ipk_stress = ($last_ipk < $min_ipk) ? "style='color: #e74c3c; font-weight: bold; font-size: 1.1em;'" : "";
            
            $potensi_text = "";
            if ($semester_tempuh >= 6 && $jml_judul_ta > 0) {
                $potensi_text = ", serta <strong>berpotensi lulus tepat waktu</strong>";
            }
            
            if ($last_ipk >= 3.5) {
                $ipk_status = "berada pada kategori <strong>Sangat Memuaskan</strong> <span style='color:#1cc88a;'><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i></span> dengan nilai <span $ipk_stress>$ipk_val</span>. Capaian tersebut menunjukkan kemampuan akademik yang sangat tinggi dan konsisten{$potensi_text}.";
            } elseif ($last_ipk >= 3.0) {
                $ipk_status = "berada pada kategori <strong>Baik</strong> dengan nilai <span $ipk_stress>$ipk_val</span>. Capaian tersebut menunjukkan kemampuan akademik yang stabil terhadap beban studi yang ditempuh.";
            } else {
                $cat = ($last_ipk < $min_ipk) ? "<strong>Kurang (Perlu Perhatian Khusus)</strong>" : "Cukup";
                $ipk_status = "saat ini berada pada kategori $cat dengan nilai <span $ipk_stress>$ipk_val</span>. " . 
                              (($last_ipk < $min_ipk) ? "Capaian ini berada di bawah standar minimal " . number_format($min_ipk, 2) . ", sehingga diperlukan bimbingan akademik yang lebih intensif dari dosen wali." : 
                              "Diperlukan strategi belajar yang lebih fokus untuk meningkatkan index prestasi pada semester-semester mendatang.");
            }
            $ipk_text = "Secara keseluruhan, IPK mahasiswa saat ini $ipk_status";
        }

        // 3. Analisis Pengambilan KRS
        $krs_text = "Berdasarkan rekam jejak akademik sejauh ini, sebagian besar mata kuliah telah diselesaikan sesuai dengan kurikulum.";

        // 4. Warning & Mata Kuliah Perhatian (E grades retake suggestion)
        $warning_count = $attention_count;
        
        $warning_text = "";
        if ($is_maba) {
            $warning_text = " Seluruh mata kuliah merupakan paket semester 1 tanpa adanya riwayat mata kuliah mengulang.";
        } elseif ($warning_count > 0) {
            $warning_text = " Namun demikian, tercatat masih terdapat <strong>$warning_count mata kuliah (D/E)</strong> yang memerlukan perhatian. ";
            if ($has_e) {
                $warning_text .= "Secara khusus, terdapat mata kuliah dengan <strong>Nilai E (Belum Lulus)</strong> yang sangat disarankan untuk segera diprogramkan kembali (mengulang) guna menghindari hambatan pada progres kelulusan.";
            } else {
                $warning_text .= "Disarankan untuk mempertimbangkan pengulangan mata kuliah dengan nilai D tersebut di masa mendatang agar dapat memperbaiki IPK akumulatif.";
            }
        } else {
            $warning_text = " Sejauh ini, rekam jejak pengambilan mata kuliah menunjukkan kepatuhan yang baik terhadap kurikulum tanpa adanya tunggakan nilai kritis.";
        }

        // 5. Status KIP
        $kip_text = "";
        if ($kip_data) {
            $kip_text = "Sebagai penerima program <strong>KIP</strong>, mahasiswa diharapkan menjaga keaktifan dan performa akademik agar pembiayaan beasiswa tetap berlanjut.";
        }

        // 6. Target Kedepan
        if ($is_maba) {
            $target_ips = "3.25";
            $recommendation = "Diharapkan pada semester pertama ini, mahasiswa dapat mencapai target <strong>IPS minimal $target_ips</strong> sebagai langkah awal meraih predikat kelulusan terbaik.";
        } else {
            $target_ips = ($last_ipk < 3.0) ? "3.20" : "3.50";
            $recommendation = "Diharapkan mahasiswa dapat mencapai target <strong>IPS minimal $target_ips</strong> untuk terus memperbaiki atau mempertahankan rekam akademik yang kompetitif.";
        }

        return "<p>$intro $trend</p><p>$ipk_text</p><p>$krs_text $warning_text</p><p>$kip_text $recommendation</p>";
    }
}
