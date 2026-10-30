<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_matriks_mk_cpl extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $user_obj = $this->ion_auth->user()->row();
        $user_email = strtolower(trim($user_obj->email ?? ''));
        $user_uname = strtolower(trim($user_obj->username ?? ''));
        $is_gege = ($user_email === 'gege.satya@wbi.ac.id' || $user_uname === 'gege.satya@wbi.ac.id');

        // Untuk user dengan status group = 15, hanya dibuka untuk akun gege.satya@wbi.ac.id
        if ($this->ion_auth->in_group(15) && !$is_gege) {
            redirect('auth/logout', 'refresh');
        }

        $this->load->model('Maintenance_model');

        // Cek mode maintenance (terhubung ke maintenance Kurikulum)
        if ($this->Maintenance_model->is_maintenance('kurikulum') && !$this->Maintenance_model->is_user_exempt()) {
            if ($this->input->is_ajax_request()) {
                $status = $this->Maintenance_model->get_status('kurikulum');
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'status' => 'error',
                    'maintenance' => true,
                    'msg' => $status->message ?? 'Modul Matriks MK-CPL sedang dalam pemeliharaan sistem.'
                ]);
                exit;
            } else {
                $status = $this->Maintenance_model->get_status('kurikulum');
                $output['judul_besar'] = 'Pemeliharaan Sistem';
                $output['judul_kecil'] = 'Under Maintenance';
                $output['menu_aktif'] = 'm_matriks_mk_cpl';
                $output['parent_ws'] = 'active open';
                $output['module_title'] = 'Matriks Mata Kuliah & CPL (OBE)';
                $output['maintenance_message'] = $status->message ?? 'Modul Matriks MK-CPL sedang dalam sinkronisasi kurikulum oleh Administrator Akademik.';
                $output['content'] = 'maintenance/v_maintenance_notice';
                $this->load->view('master_page', $output);
                $this->output->_display();
                exit;
            }
        }
    }

    public function index($selected_id = null)
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $user_obj = $this->ion_auth->user()->row();
        $user_email = strtolower(trim($user_obj->email ?? ''));
        $user_uname = strtolower(trim($user_obj->username ?? ''));
        $is_gege = ($user_email === 'gege.satya@wbi.ac.id' || $user_uname === 'gege.satya@wbi.ac.id');

        // Ambil seluruh pustaka kurikulum & auto-heal dari disk jika perlu
        $raw_rows = $this->db->order_by('nama_prodi', 'ASC')->get('siakad_pustaka_kurikulum')->result();
        $kurikulum_rows = [];
        foreach ($raw_rows as $raw_item) {
            $healed = $this->_get_pustaka_row_with_autoheal($raw_item->id);
            $kurikulum_rows[] = $healed ?: $raw_item;
        }

        // Tentukan ID yang aktif (prioritaskan yang VERIFIED & memiliki normalized_json)
        $active_id = null;
        $active_row = null;

        if (!empty($selected_id)) {
            $active_id = (int)$selected_id;
            foreach ($kurikulum_rows as $kr) {
                if ((int)$kr->id === $active_id) {
                    $active_row = $kr;
                    break;
                }
            }
        }

        if (!$active_row && !empty($kurikulum_rows)) {
            // 1. Cari prodi yang VERIFIED dan memiliki normalized_json
            foreach ($kurikulum_rows as $kr) {
                if ($kr->normalization_status === 'VERIFIED' && !empty($kr->normalized_json)) {
                    $active_id = (int)$kr->id;
                    $active_row = $kr;
                    break;
                }
            }
            // 2. Jika tidak ada yang verified+json, cari yang status VERIFIED
            if (!$active_row) {
                foreach ($kurikulum_rows as $kr) {
                    if ($kr->normalization_status === 'VERIFIED') {
                        $active_id = (int)$kr->id;
                        $active_row = $kr;
                        break;
                    }
                }
            }
            // 3. Fallback ke record pertama
            if (!$active_row && isset($kurikulum_rows[0])) {
                $active_id = (int)$kurikulum_rows[0]->id;
                $active_row = $kurikulum_rows[0];
            }
        }

        $parsed_data = null;
        $is_verified = false;

        if ($active_row) {
            $is_verified = ($active_row->normalization_status === 'VERIFIED');
            if ($is_verified && !empty($active_row->normalized_json)) {
                $parsed_data = json_decode($active_row->normalized_json, true);
                if (!empty($parsed_data) && is_array($parsed_data)) {
                    $parsed_data = $this->_enrich_course_descriptions($parsed_data, $active_row->nama_prodi);
                    $parsed_data = $this->_calculate_cpl_bk_alignment_scores($parsed_data);
                }
            }
        }

        $output['judul_besar'] = 'Matriks MK-CPL';
        $output['judul_kecil'] = 'Pemetaan Capaian Pembelajaran Lulusan & Mata Kuliah (SN-Dikti 2026)';
        $output['menu_aktif']  = 'm_matriks_mk_cpl';
        $output['parent_ws']   = 'active open';
        $output['content']     = 'matriks_mk_cpl/v_matriks_mk_cpl';
        $output['after_page']  = 'matriks_mk_cpl/v_matriks_mk_cpl_after_page';

        $output['kurikulum_rows'] = $kurikulum_rows;
        $output['active_id']      = $active_id;
        $output['active_row']     = $active_row;
        $output['parsed_data']    = $parsed_data;
        $output['is_verified']    = $is_verified;
        $output['is_gege']        = $is_gege;

        $this->load->view('master_page', $output);
    }

    /**
     * Helper to get siakad_pustaka_kurikulum row with automatic file self-healing and persistent JSON restore
     */
    protected function _get_pustaka_row_with_autoheal($id)
    {
        $row = $this->db->where('id', (int)$id)->get('siakad_pustaka_kurikulum')->row();
        if (!$row) {
            return null;
        }

        $needs_update = false;
        $updates = [];

        $safe_slug = preg_replace('/[^a-zA-Z0-9_-]/', '_', $row->nama_prodi);
        $upload_dir = FCPATH . 'uploads/pustaka_kurikulum/';
        $config_dir = APPPATH . 'config/kurikulum_payloads/';

        if (!is_dir($config_dir)) {
            @mkdir($config_dir, 0777, true);
        }
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        // Normalize any absolute paths (e.g., C:/laragon/... or /var/www/...) to clean relative path
        if (!empty($row->file_path_doc)) {
            $cleaned_doc = str_replace('\\', '/', $row->file_path_doc);
            if (preg_match('#(uploads/pustaka_kurikulum/[^/]+)$#i', $cleaned_doc, $m_doc)) {
                if ($row->file_path_doc !== $m_doc[1]) {
                    $row->file_path_doc = $m_doc[1];
                    $updates['file_path_doc'] = $m_doc[1];
                    $needs_update = true;
                }
            }
        }
        if (!empty($row->file_path_content)) {
            $cleaned_content = str_replace('\\', '/', $row->file_path_content);
            if (preg_match('#(uploads/pustaka_kurikulum/[^/]+)$#i', $cleaned_content, $m_cnt)) {
                if ($row->file_path_content !== $m_cnt[1]) {
                    $row->file_path_content = $m_cnt[1];
                    $updates['file_path_content'] = $m_cnt[1];
                    $needs_update = true;
                }
            }
        }

        $doc_exists = !empty($row->file_path_doc) && file_exists(FCPATH . $row->file_path_doc);
        $txt_exists = !empty($row->file_path_content) && file_exists(FCPATH . $row->file_path_content);

        if (!$doc_exists) {
            $doc_files = glob($upload_dir . 'doc_' . $safe_slug . '_*.*');
            if (!empty($doc_files)) {
                usort($doc_files, fn($a, $b) => filemtime($b) - filemtime($a));
                $latest_doc = 'uploads/pustaka_kurikulum/' . basename($doc_files[0]);
                $row->file_path_doc = $latest_doc;
                $updates['file_path_doc'] = $latest_doc;
                $doc_exists = true;
                $needs_update = true;
            }
        }

        if (!$txt_exists) {
            $txt_files = glob($upload_dir . 'content_' . $safe_slug . '_*.md');
            if (!empty($txt_files)) {
                usort($txt_files, fn($a, $b) => filemtime($b) - filemtime($a));
                $latest_txt = 'uploads/pustaka_kurikulum/' . basename($txt_files[0]);
                $row->file_path_content = $latest_txt;
                $updates['file_path_content'] = $latest_txt;
                $row->total_karakter = filesize($txt_files[0]);
                $updates['total_karakter'] = $row->total_karakter;
                $txt_exists = true;
                $needs_update = true;
            }
        }

        $cfg_json = $config_dir . 'normalized_' . $safe_slug . '.json';
        $upl_json = $upload_dir . 'normalized_' . $safe_slug . '.json';

        $has_db_norm = !empty($row->normalized_json) && !empty($row->normalization_status) && $row->normalization_status !== 'NOT_NORMALIZED';

        if (!$has_db_norm) {
            $json_disk_file = file_exists($cfg_json) ? $cfg_json : (file_exists($upl_json) ? $upl_json : null);

            if ($json_disk_file && file_exists($json_disk_file)) {
                $raw_json = file_get_contents($json_disk_file);
                $decoded = json_decode($raw_json, true);
                if (!empty($decoded) && is_array($decoded)) {
                    $norm_status = $decoded['curriculum']['verification_status'] ?? ($decoded['normalization_status'] ?? 'VERIFIED');
                    if (empty($norm_status) || $norm_status === 'NOT_NORMALIZED') {
                        $norm_status = 'VERIFIED';
                    }
                    $source_hash = $decoded['curriculum']['source_hash'] ?? ($decoded['source_hash'] ?? '');

                    $updates['normalized_json'] = $raw_json;
                    $updates['normalization_status'] = $norm_status;
                    $updates['schema_version'] = $decoded['schema_version'] ?? '1.0';
                    $updates['parser_version'] = $decoded['metadata']['parser_version'] ?? 'Phase-1.7';
                    $updates['source_hash'] = $source_hash;
                    $updates['normalized_at'] = $decoded['curriculum']['verified_at'] ?? date('Y-m-d H:i:s');

                    $row->normalized_json = $raw_json;
                    $row->normalization_status = $norm_status;
                    $row->schema_version = $updates['schema_version'];
                    $row->parser_version = $updates['parser_version'];
                    $row->source_hash = $source_hash;
                    $row->normalized_at = $updates['normalized_at'];
                    $needs_update = true;

                    if (!file_exists($cfg_json))
                        @file_put_contents($cfg_json, $raw_json);
                    if (!file_exists($upl_json))
                        @file_put_contents($upl_json, $raw_json);
                }
            }
        } else {
            if (!file_exists($cfg_json)) {
                @file_put_contents($cfg_json, $row->normalized_json);
            }
            if (!file_exists($upl_json)) {
                @file_put_contents($upl_json, $row->normalized_json);
            }
        }

        if ($needs_update) {
            $this->db->where('id', $row->id)->update('siakad_pustaka_kurikulum', $updates);
        }

        return $row;
    }

    /**
     * Helper to clean leaked running header/footer and page text artifacts from documents
     */
    protected function _sanitize_text($text)
    {
        if (empty($text)) return $text;
        $text = preg_replace('/POLITEKNIK\s+WBI\s+\d{4}\s+\d+\s*\|\s*P\s*a\s*g\s*e\s*(Aspek\s+Kode\s+D[ie]skripsi\s+Capaian\s+Pembelajaran\s+Lulusan(\s+Berdasarkan\s+KKNI\s+[A-Za-z\s]+)?)?/i', '', $text);
        $text = preg_replace('/\b\d+\s*\|\s*P\s*a\s*g\s*e\b/i', '', $text);
        $text = preg_replace('/\bPage\s+\d+\b/i', '', $text);
        $text = preg_replace('/Aspek\s+Kode\s+D[ie]skripsi\s+Capaian\s+Pembelajaran\s+Lulusan(\s+Berdasarkan\s+KKNI\s+[A-Za-z\s]+)?/i', '', $text);
        $text = preg_replace('/\b\d+\s+[a-z]\.\s+Aspek\s+(Keterampilan\s+Umum|Keterampilan\s+Khusus|Pengetahuan|Sikap)/i', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Helper to auto-enrich course descriptions from RPS OBE and RPS online
     */
    protected function _enrich_course_descriptions($payload, $nama_prodi = '')
    {
        if (empty($payload)) {
            return $payload;
        }

        // Sanitize CPL descriptions
        if (!empty($payload['official_cpl']) && is_array($payload['official_cpl'])) {
            foreach ($payload['official_cpl'] as &$c) {
                if (!empty($c['description'])) {
                    $c['description'] = $this->_sanitize_text($c['description']);
                }
            }
            unset($c);
        }

        // Sanitize BK descriptions
        if (!empty($payload['bahan_kajian']) && is_array($payload['bahan_kajian'])) {
            foreach ($payload['bahan_kajian'] as &$b) {
                if (!empty($b['description'])) {
                    $b['description'] = $this->_sanitize_text($b['description']);
                }
                if (!empty($b['name'])) {
                    $b['name'] = $this->_sanitize_text($b['name']);
                }
            }
            unset($b);
        }

        if (empty($payload['mata_kuliah'])) {
            return $payload;
        }

        $desc_lookup = [];

        // 1. Dari siakad_rps_obe
        $obe_rows = $this->db->select('nm_mk, kode_mk, deskripsi_mata_kuliah')
            ->where('deskripsi_mata_kuliah IS NOT NULL')
            ->where("TRIM(deskripsi_mata_kuliah) != ''")
            ->get('siakad_rps_obe')->result();
        foreach ($obe_rows as $r) {
            $key_name = strtolower(preg_replace('/[^a-z0-9]/i', '', $r->nm_mk));
            $key_code = strtolower(preg_replace('/[^a-z0-9]/i', '', $r->kode_mk));
            if (!empty($key_name) && !isset($desc_lookup[$key_name])) {
                $desc_lookup[$key_name] = trim(strip_tags($r->deskripsi_mata_kuliah));
            }
            if (!empty($key_code) && !isset($desc_lookup[$key_code])) {
                $desc_lookup[$key_code] = trim(strip_tags($r->deskripsi_mata_kuliah));
            }
        }

        // 2. Dari siakad_new_rps_online
        $online_rows = $this->db->select('nm_mk, kode_mk, deskripsi_mata_kuliah')
            ->where('deskripsi_mata_kuliah IS NOT NULL')
            ->where("TRIM(deskripsi_mata_kuliah) != ''")
            ->get('siakad_new_rps_online')->result();
        foreach ($online_rows as $r) {
            $key_name = strtolower(preg_replace('/[^a-z0-9]/i', '', $r->nm_mk));
            $key_code = strtolower(preg_replace('/[^a-z0-9]/i', '', $r->kode_mk));
            if (!empty($key_name) && !isset($desc_lookup[$key_name])) {
                $desc_lookup[$key_name] = trim(strip_tags($r->deskripsi_mata_kuliah));
            }
            if (!empty($key_code) && !isset($desc_lookup[$key_code])) {
                $desc_lookup[$key_code] = trim(strip_tags($r->deskripsi_mata_kuliah));
            }
        }

        // Inject into each mata_kuliah item
        foreach ($payload['mata_kuliah'] as &$m) {
            if (empty($m['deskripsi_mk'])) {
                $k_name = strtolower(preg_replace('/[^a-z0-9]/i', '', $m['nama_mk'] ?? ''));
                $k_code = strtolower(preg_replace('/[^a-z0-9]/i', '', $m['kode_mk'] ?? ''));
                if (!empty($k_name) && isset($desc_lookup[$k_name])) {
                    $m['deskripsi_mk'] = $desc_lookup[$k_name];
                } elseif (!empty($k_code) && isset($desc_lookup[$k_code])) {
                    $m['deskripsi_mk'] = $desc_lookup[$k_code];
                } else {
                    $m['deskripsi_mk'] = 'Mata kuliah ' . ($m['nama_mk'] ?? '') . ' membekali mahasiswa dengan penguasaan capaian pembelajaran lulusan (CPL) dan kompetensi inti program studi sesuai kurikulum SN-Dikti 2026.';
                }
            }
        }
        unset($m);

        return $payload;
    }

    /**
     * Helper to compute Phase 1.7 Curriculum Alignment scoring (Direct 65 pt, BK Path 35 pt)
     */
    protected function _calculate_cpl_bk_alignment_scores($payload)
    {
        if (empty($payload) || empty($payload['mata_kuliah'])) {
            return $payload;
        }

        $cpls = $payload['official_cpl'] ?? [];
        $bks = $payload['bahan_kajian'] ?? [];
        $cpl_bk_pairs = $payload['cpl_bk_mapping']['pairs'] ?? ($payload['cpl_bk_mapping'] ?? []);
        $has_bk_source = !empty($payload['cpl_bk_mapping']['pairs']) || !empty($payload['bahan_kajian']);

        // Check reconciliation lists
        $recon = $payload['matrix_reconciliation'] ?? [];
        $no_cpl_names = [];
        if (!empty($recon['matrix_courses_without_cpl_list']) && is_array($recon['matrix_courses_without_cpl_list'])) {
            foreach ($recon['matrix_courses_without_cpl_list'] as $item) {
                $no_cpl_names[strtolower(trim($item['mk_name'] ?? ''))] = true;
            }
        }
        $outside_names = [];
        if (!empty($recon['active_courses_outside_matrix_list']) && is_array($recon['active_courses_outside_matrix_list'])) {
            foreach ($recon['active_courses_outside_matrix_list'] as $item) {
                $outside_names[strtolower(trim($item['mk_name'] ?? ''))] = true;
            }
        }

        // Index BKs
        $bk_dict = [];
        foreach ($bks as $b) {
            $code = $b['code'] ?? $b['kode_bk'] ?? '';
            if ($code) {
                $bk_dict[$code] = [
                    'code' => $code,
                    'name' => $b['name'] ?? $b['nama_bk'] ?? $code,
                    'description' => $b['description'] ?? $b['deskripsi'] ?? ''
                ];
            }
        }

        // Index CPLs
        $cpl_dict = [];
        foreach ($cpls as $c) {
            if (!empty($c['code'])) {
                $cpl_dict[$c['code']] = $c;
            }
        }

        // Build CPL -> BK Map
        $cpl_to_bks = [];
        if (is_array($cpl_bk_pairs)) {
            foreach ($cpl_bk_pairs as $p) {
                $c_code = $p['cpl_code'] ?? '';
                $b_code = $p['bk_code'] ?? '';
                if ($c_code && $b_code && isset($bk_dict[$b_code])) {
                    $cpl_to_bks[$c_code][$b_code] = $bk_dict[$b_code];
                }
            }
        }

        // Phase 1.7: Pure source-first authoritative CPL-BK matrix mapping (Zero AI/Semantic guessing fallback)

        // Process each Course
        $total_mk_score = 0;
        $count_mk = 0;

        $is_pending_audit = (isset($payload['metadata']['manual_validation_status']) && $payload['metadata']['manual_validation_status'] === 'PENDING') || 
                            (isset($payload['manual_validation_status']) && $payload['manual_validation_status'] === 'PENDING');

        if ($is_pending_audit) {
            $payload['manual_validation_status'] = 'PENDING';
            $payload['curriculum_alignment_status'] = 'NOT_YET_VALIDATED';
            $payload['avg_alignment_score'] = null;

            foreach ($payload['mata_kuliah'] as &$m) {
                $m['manual_validation_status'] = 'PENDING';
                $m['curriculum_alignment_score'] = null;
                $m['curriculum_alignment_status'] = 'NOT_YET_VALIDATED';
                $m['alignment_score'] = null;
                $m['alignment_verdict'] = 'Belum Diaudit Manual';
                $m['alignment_badge'] = 'default';
                $m['badge_label'] = 'Belum diaudit manual';
                $m['alignment_evidence'] = [
                    'direct' => [
                        'available' => false,
                        'score' => null,
                        'mapping_type' => 'NOT_YET_VALIDATED',
                        'cpl_codes' => []
                    ],
                    'bk_path' => [
                        'available' => false,
                        'score' => null,
                        'supported_cpl_codes' => [],
                        'unsupported_cpl_codes' => [],
                        'evidence_paths' => []
                    ]
                ];
                $m['cpl_justifications'] = [];
                $m['supported_bks'] = [];
            }
            unset($m);
            return $payload;
        }

        foreach ($payload['mata_kuliah'] as &$m) {
            $nama_mk_clean = strtolower(trim($m['nama_mk'] ?? ''));
            $direct_cpls = $m['direct_cpl_codes'] ?? $m['mapped_cpls'] ?? $m['cpls'] ?? [];
            $total_direct_cpls = count($direct_cpls);

            // Special handling: Aggregate matrix track (e.g. MPI internship/concentration modules)
            $is_aggregate = (str_contains($nama_mk_clean, '(internship)') || 
                             str_contains($nama_mk_clean, 'magang') ||
                             (!empty($m['mapping_type']) && $m['mapping_type'] === 'COVERED_BY_AGGREGATE_TRACK') ||
                             (!empty($m['is_aggregate_track'])));

            if ($is_aggregate) {
                $m['curriculum_alignment_score'] = null;
                $m['curriculum_alignment_status'] = 'COVERED_BY_AGGREGATE_TRACK';
                $m['alignment_score'] = null;
                $m['alignment_verdict'] = 'Terhubung melalui Aggregate Track';
                $m['alignment_badge'] = 'info';
                $m['badge_label'] = 'Aggregate Track';
                $m['alignment_evidence'] = [
                    'direct' => [
                        'available' => true,
                        'score' => null,
                        'mapping_type' => 'COVERED_BY_AGGREGATE_MATRIX_TRACK',
                        'cpl_codes' => []
                    ],
                    'bk_path' => [
                        'available' => false,
                        'score' => null,
                        'supported_cpl_codes' => [],
                        'unsupported_cpl_codes' => [],
                        'evidence_paths' => []
                    ]
                ];
                $m['cpl_justifications'] = [];
                $m['supported_bks'] = [];
                continue;
            }

            // Direct Mapping Score (Max 65)
            $direct_score = 0;
            $mapping_type = 'NO_OFFICIAL_CPL_MAPPING';
            if ($total_direct_cpls > 0) {
                $direct_score = 65;
                $mapping_type = 'EXPLICIT_MATRIX';
            } elseif (isset($no_cpl_names[$nama_mk_clean])) {
                $direct_score = 0;
                $mapping_type = 'IN_MATRIX_NO_CPL_MARKED';
            } elseif (isset($outside_names[$nama_mk_clean])) {
                $direct_score = 0;
                $mapping_type = 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX';
            }

            // BK Path Score (Max 35)
            $bk_score = 0;
            $supported_cpl_codes = [];
            $unsupported_cpl_codes = [];
            $justifications = [];
            $supported_bks = [];

            if ($total_direct_cpls > 0) {
                foreach ($direct_cpls as $cp) {
                    $c_info = $cpl_dict[$cp] ?? [];
                    $cat = strtoupper($c_info['category'] ?? '');
                    $sec = strtoupper($c_info['source_section'] ?? '');
                    $sup_bks = array_values($cpl_to_bks[$cp] ?? []);

                    $is_sikap = ($cat === 'S' || str_contains($sec, 'SIKAP') || str_starts_with($cp, 'S-') || str_starts_with($cp, 'SIKAP'));

                    if ($is_sikap) {
                        $supported_cpl_codes[] = $cp;
                        $justifications[$cp] = [
                            'cpl_code' => $cp,
                            'category' => 'S',
                            'status' => 'DIRECT_VALUE',
                            'status_label' => 'Internalisasi Sikap & Tata Nilai',
                            'is_supported_bk' => true,
                            'supporting_bks' => []
                        ];
                    } elseif (count($sup_bks) > 0) {
                        $supported_cpl_codes[] = $cp;
                        $justifications[$cp] = [
                            'cpl_code' => $cp,
                            'category' => $cat ?: 'P',
                            'status' => 'JUSTIFIED_BY_BK',
                            'status_label' => 'Didukung ' . count($sup_bks) . ' Bahan Kajian',
                            'is_supported_bk' => true,
                            'supporting_bks' => $sup_bks
                        ];
                        foreach ($sup_bks as $sb) {
                            $supported_bks[$sb['code']] = $sb;
                        }
                    } else {
                        $unsupported_cpl_codes[] = $cp;
                        $justifications[$cp] = [
                            'cpl_code' => $cp,
                            'category' => $cat ?: 'KU',
                            'status' => 'DIRECT_MAPPED_ONLY',
                            'status_label' => 'Pemetaan Matriks Langsung',
                            'is_supported_bk' => false,
                            'supporting_bks' => []
                        ];
                    }
                }

                $supported_count = count($supported_cpl_codes);
                $bk_score = round(($supported_count / $total_direct_cpls) * 35, 2);
            } else {
                $bk_score = 0;
            }

            // Total Curriculum Alignment Score
            $total_score = round($direct_score + $bk_score, 2);

            // Interpretation & Status
            $status = 'EVIDENCE_INCOMPLETE';
            $verdict = '';
            $badge_class = 'default';
            $badge_label = '';

            if ($total_score >= 85) {
                $status = 'VERY_STRONG_CURRICULUM_EVIDENCE';
                $verdict = 'Very Strong Curriculum Evidence';
                $badge_class = 'success';
                $badge_label = $total_score . '% Very Strong';
            } elseif ($total_score >= 65) {
                $status = 'STRONG_DIRECT_CURRICULUM_EVIDENCE';
                $verdict = 'Strong Direct Curriculum Evidence';
                $badge_class = 'info';
                $badge_label = $total_score . '% Direct Matrix Only';
            } elseif ($total_score >= 35) {
                $status = 'PARTIAL_DERIVED_CURRICULUM_EVIDENCE';
                $verdict = 'Partial / Derived Curriculum Evidence';
                $badge_class = 'warning';
                $badge_label = $total_score . '% Partial Evidence';
            } elseif ($total_score > 0) {
                $status = 'WEAK_CURRICULUM_EVIDENCE';
                $verdict = 'Weak Curriculum Evidence';
                $badge_class = 'danger';
                $badge_label = $total_score . '% Weak Evidence';
            } else {
                $status = 'NO_CONFIRMED_OFFICIAL_ALIGNMENT';
                $badge_class = 'danger';
                if ($mapping_type === 'IN_MATRIX_NO_CPL_MARKED') {
                    $verdict = 'Ada di matriks resmi, tetapi tidak diberi CPL';
                    $badge_label = '0% Ada di Matriks, Tanpa CPL';
                } elseif ($mapping_type === 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX') {
                    $verdict = 'MK aktif, tidak tercantum pada matriks CPL-MK resmi';
                    $badge_label = '0% Di Luar Matriks Resmi';
                } else {
                    $verdict = 'No confirmed official CPL-MK alignment';
                    $badge_label = '0% No Official CPL Mapping';
                }
            }

            $m['curriculum_alignment_score'] = $total_score;
            $m['curriculum_alignment_status'] = $status;
            $m['alignment_score'] = $total_score;
            $m['alignment_verdict'] = $verdict;
            $m['alignment_badge'] = $badge_class;
            $m['badge_label'] = $badge_label;

            $m['alignment_evidence'] = [
                'direct' => [
                    'available' => ($direct_score > 0 || in_array($mapping_type, ['IN_MATRIX_NO_CPL_MARKED', 'ACTIVE_COURSE_OUTSIDE_CPL_MATRIX'])),
                    'score' => $direct_score,
                    'mapping_type' => $mapping_type,
                    'cpl_codes' => $direct_cpls
                ],
                'bk_path' => [
                    'available' => $has_bk_source,
                    'score' => $bk_score,
                    'supported_cpl_codes' => $supported_cpl_codes,
                    'unsupported_cpl_codes' => $unsupported_cpl_codes,
                    'evidence_paths' => array_values($supported_bks)
                ]
            ];
            $m['cpl_justifications'] = $justifications;
            $m['supported_bks'] = array_values($supported_bks);

            if ($total_score !== null) {
                $total_mk_score += $total_score;
                $count_mk++;
            }
        }
        unset($m);

        $payload['avg_alignment_score'] = $count_mk > 0 ? round($total_mk_score / $count_mk, 2) : 0;
        return $payload;
    }

    /**
     * AJAX: Get Matrix Data for Selected Curriculum ID
     */
    public function ajax_get_matrix()
    {
        $this->output->set_content_type('application/json');
        $id = (int)$this->input->post('id');

        $row = $this->_get_pustaka_row_with_autoheal($id);
        if (!$row) {
            echo json_encode(['status' => false, 'msg' => 'Data kurikulum prodi tidak ditemukan.']);
            return;
        }

        $is_verified = ($row->normalization_status === 'VERIFIED');
        if (!$is_verified) {
            echo json_encode([
                'status' => true,
                'is_verified' => false,
                'prodi' => $row->nama_prodi,
                'tahun' => $row->tahun_kurikulum ?: '-',
                'normalization_status' => $row->normalization_status ?: 'NOT_NORMALIZED',
                'msg' => 'Kurikulum Program Studi ' . $row->nama_prodi . ' belum berstatus VERIFIED. Matriks MK-CPL resmi hanya ditampilkan setelah dokumen kurikulum diverifikasi oleh Administrator Kurikulum.'
            ]);
            return;
        }

        if (empty($row->normalized_json)) {
            echo json_encode([
                'status' => true,
                'is_verified' => false,
                'prodi' => $row->nama_prodi,
                'tahun' => $row->tahun_kurikulum ?: '-',
                'normalization_status' => 'EMPTY_JSON',
                'msg' => 'Payload normalisasi kurikulum belum tersedia.'
            ]);
            return;
        }

        $payload = json_decode($row->normalized_json, true);
        $payload = $this->_enrich_course_descriptions($payload, $row->nama_prodi);
        $payload = $this->_calculate_cpl_bk_alignment_scores($payload);

        echo json_encode([
            'status' => true,
            'is_verified' => true,
            'id' => $row->id,
            'prodi' => $row->nama_prodi,
            'tahun' => $row->tahun_kurikulum ?: '-',
            'normalization_status' => $row->normalization_status,
            'payload' => $payload
        ]);
    }
}
