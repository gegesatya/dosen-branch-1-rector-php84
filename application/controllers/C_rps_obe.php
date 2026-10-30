<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_rps_obe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rps_obe_model');
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $group = [2, 15];
        if (!$this->ion_auth->in_group($group)) {
            redirect('auth/logout', 'refresh');
        }

        $this->load->model('Maintenance_model');

        // Cek mode maintenance untuk Studio RPS
        if ($this->Maintenance_model->is_maintenance('studio_rps') && !$this->Maintenance_model->is_user_exempt()) {
            if ($this->input->is_ajax_request()) {
                $status = $this->Maintenance_model->get_status('studio_rps');
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'status' => 'error',
                    'maintenance' => true,
                    'msg' => $status->message ?? 'Daftar RPS OBE sedang dalam pemeliharaan sistem.'
                ]);
                exit;
            } else {
                $status = $this->Maintenance_model->get_status('studio_rps');
                $output['judul_besar'] = 'Pemeliharaan Sistem';
                $output['judul_kecil'] = 'Under Maintenance';
                $output['menu_aktif']  = 'm_rps_obe_list';
                $output['parent_ws']   = 'active open';
                $output['module_title'] = 'Daftar RPS OBE (Outcome-Based Education)';
                $output['maintenance_message'] = $status->message ?? 'Modul RPS OBE sedang dalam pemeliharaan dan sinkronisasi data kurikulum oleh Administrator Akademik.';
                $output['content']     = 'maintenance/v_maintenance_notice';
                $this->load->view('master_page', $output);
                $this->output->_display();
                exit;
            }
        }
    }

    public function index()
    {
        $output['judul_besar'] = 'Daftar RPS OBE';
        $output['judul_kecil'] = 'Outcome-Based Education (SN-Dikti 2026)';
        $output['menu_aktif']  = 'm_rps_obe_list';
        $output['parent_ws']   = 'active open';
        $output['content']     = 'rps_obe/v_rps_obe_list';
        $output['after_page']  = 'rps_obe/v_rps_obe_list_after_page';

        $this->load->view('master_page', $output);
    }

    public function ajax_list()
    {
        $list = $this->Rps_obe_model->get_datatables();
        $data = [];
        $no = $_POST['start'] ?? 0;

        foreach ($list as $l) {
            $no++;
            $row = [];

            $id_md5 = md5($l->id_rps);

            // 1. Column 1: No Badge
            $col_no = '<div style="display:flex; align-items:center; justify-content:center;">'
                    . '  <span style="background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 700; width: 28px; height: 28px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1;">' . $no . '</span>'
                    . '</div>';

            // 2. Column 2: Mata Kuliah & Info Kurikulum
            $badge_status = '<span class="label" style="background-color: #fef3c7; color: #92400e; font-weight: 700; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #fde68a; display:inline-block;"><i class="fa fa-pencil-square-o"></i> Draft</span>';
            if ($l->id_status_pengajuan_rps == 2) {
                $badge_status = '<span class="label" style="background-color: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #bae6fd; display:inline-block;"><i class="fa fa-clock-o"></i> Diajukan</span>';
            } elseif ($l->id_status_pengajuan_rps == 3) {
                $badge_status = '<span class="label" style="background-color: #dcfce7; color: #15803d; font-weight: 700; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #bbf7d0; display:inline-block;"><i class="fa fa-check-circle"></i> Disetujui</span>';
            }

            $sem_text = trim($l->semester ?: '-');
            if ($sem_text !== '-' && stripos($sem_text, 'semester') === false) {
                $sem_text = 'Semester ' . $sem_text;
            }

            $badge_obe = '<span class="label" style="background-color: #f1f5f9; color: #334155; font-weight: 700; font-size: 11px; padding: 2px 7px; border-radius: 4px; display: inline-block; border: 1px solid #cbd5e1;"><i class="fa fa-cube" style="color: #0284c7;"></i> OBE C4-C6</span>';

            $col_mk = '<div style="display: flex; flex-direction: column; gap: 4px;">'
                    . '  <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">'
                    . '    <span style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #bfdbfe;">[' . ($l->kode_mk ?: 'MK') . ']</span>'
                    . '    <a href="' . site_url('C_rps_obe/preview_rps/' . $id_md5) . '" target="_blank" style="color:#0f172a; font-size:14px; font-weight:700; line-height:1.3; text-decoration:none;" title="Pratinjau Dokumen RPS">' . htmlspecialchars($l->nm_mk) . '</a>'
                    . '  </div>'
                    . '  <div style="font-size: 12px; color: #475569; display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">'
                    . '    <span><i class="fa fa-graduation-cap" style="color:#2563eb;"></i> <strong>' . ($l->sks_mk ?: 3) . ' SKS</strong></span>'
                    . '    <span style="color:#cbd5e1;">•</span>'
                    . '    <span><i class="fa fa-clone" style="color:#64748b;"></i> ' . $sem_text . '</span>'
                    . '    <span style="color:#cbd5e1;">•</span>'
                    . '    <span><i class="fa fa-calendar-check-o" style="color:#64748b;"></i> ' . date('d M Y, H:i', strtotime($l->create_date ?: $l->change_date ?: date('Y-m-d H:i'))) . '</span>'
                    . '  </div>'
                    . '  <div style="display:flex; gap:5px; align-items:center; margin-top:2px;">' . $badge_status . ' ' . $badge_obe . '</div>'
                    . '</div>';

            // 3. Column 3: Capaian Pembelajaran (CPMK) - Clean full border (No side stripes)
            $cpmk_text = trim($l->tujuan_belajar ?: '');
            if ((empty($cpmk_text) || strlen($cpmk_text) < 35 || !str_contains($cpmk_text, '|')) && !empty($l->raw_ai_output)) {
                if (preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:\d+[\.\)]\s*)?(?:TUJUAN BELAJAR|CPMK\b|CAPAIAN PEMBELAJARAN MATA KULIAH)[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:\d+[\.\)]\s*)?(?:DESKRIPSI|RINGKASAN MATA KULIAH|REFERENSI|DAFTAR PUSTAKA|DETAIL RENCANA))|\Z)/i', $l->raw_ai_output, $m)) {
                    $cpmk_text = trim($m[1]);
                    $this->db->where('id_rps', $l->id_rps)->update('siakad_rps_obe', ['tujuan_belajar' => $cpmk_text]);
                }
            }

            $clean_cpmk = preg_replace('/^#+\s*/m', '', $cpmk_text);
            $clean_cpmk = preg_replace('/\*\*\*(.*?)\*\*\*/', '$1', $clean_cpmk);
            $clean_cpmk = preg_replace('/\*\*(.*?)\*\*/', '$1', $clean_cpmk);
            $clean_cpmk = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '$1', $clean_cpmk);
            $clean_cpmk = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '$1', $clean_cpmk);
            $clean_cpmk = trim($clean_cpmk);

            $display_cpmk = $clean_cpmk ?: 'Capaian pembelajaran RPS belum dirumuskan.';
            if (strlen($display_cpmk) > 230) {
                $display_cpmk = substr($display_cpmk, 0, 230) . '...';
            }

            $col_cpmk = '<div style="font-size: 12px; color: #1e293b; line-height: 1.5; background: #f8fafc; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">'
                      . '  <div style="font-weight: 700; font-size: 11px; color: #0369a1; margin-bottom: 4px; display: flex; align-items: center; gap: 5px;">'
                      . '    <i class="fa fa-bullseye"></i> Sasaran & Capaian (CPMK)'
                      . '  </div>'
                      . '  <div>' . nl2br(htmlspecialchars($display_cpmk)) . '</div>'
                      . '</div>';

            // 4. Column 4: 3 Aksi Utama (High Contrast, Indestructible Colors)
            $modal_data = [
                'id_rps'       => $l->id_rps,
                'id_md5'       => $id_md5,
                'kode_mk'      => $l->kode_mk ?: 'MK',
                'nm_mk'        => $l->nm_mk,
                'sks_mk'       => $l->sks_mk ?: 3,
                'semester'     => $sem_text,
                'status_badge' => $badge_status,
                'obe_badge'    => $badge_obe,
                'deskripsi'    => !empty($display_cpmk) ? $display_cpmk : 'Belum ada ringkasan sasaran capaian pembelajaran.',
                'has_roadmap'  => !empty($l->roadmap_modul_json),
                'url_rps'      => site_url('C_rps_obe/preview_rps/' . $id_md5),
                'url_kontrak'  => site_url('C_rps_obe/preview_kontrak/' . $id_md5),
                'url_tugas'    => site_url('C_rps_obe/preview_rancangan_tugas/' . $id_md5),
                'url_skenario' => site_url('C_strategi_pertemuan/index/' . $id_md5),
                'url_excel'    => site_url('C_rps_obe/export_excel/' . $l->id_rps),
                'url_roadmap'  => site_url('C_rps_obe/export_roadmap_excel/' . $l->id_rps),
                'url_roadmap_preview' => site_url('C_rps_obe/preview_roadmap/' . $id_md5),
                'url_studio'   => site_url('C_rps_generator?load_rps_obe=' . $l->id_rps),
            ];
            $json_attr = htmlspecialchars(json_encode($modal_data), ENT_QUOTES, 'UTF-8');

            $col_action = '<div style="display:flex; gap:6px; align-items:center; justify-content:center; flex-wrap:nowrap;">'
                        . '  <button type="button" class="btn btn-sm btn-action-view" data-rps=\'' . $json_attr . '\' onclick="open_rps_view_modal(this)" style="background-color:#1b4332 !important; color:#ffffff !important; border:1px solid #143527 !important; font-weight:600; font-size:12px; padding:6px 12px; border-radius:4px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;" title="Buka Pratinjau & Dokumen RPS">'
                        . '    <i class="fa fa-eye" style="color:#ffffff !important;"></i> <span style="color:#ffffff !important; font-weight:600;">RPS View</span>'
                        . '  </button>'
                        . '  <a href="' . site_url('C_strategi_pertemuan/index/' . $id_md5) . '" target="_blank" class="btn btn-sm" style="background-color:#7c3aed !important; color:#ffffff !important; border:1px solid #6d28d9 !important; font-weight:600; font-size:12px; padding:6px 12px; border-radius:4px; text-decoration:none !important; display:inline-flex; align-items:center; gap:5px;" title="Buka Strategi Pertemuan 16 Minggu & BAP">'
                        . '    <i class="fa fa-calendar-check-o" style="color:#ffffff !important;"></i> <span style="color:#ffffff !important; font-weight:600;">Strategi Pertemuan</span>'
                        . '  </a>'
                        . '  <a href="' . site_url('C_rps_generator?load_rps_obe=' . $l->id_rps) . '" class="btn btn-sm btn-action-studio" style="background-color:#2d6a4f !important; color:#ffffff !important; border:1px solid #1b4332 !important; font-weight:600; font-size:12px; padding:6px 12px; border-radius:4px; text-decoration:none !important; display:inline-flex; align-items:center; gap:5px;" title="Edit di Studio RPS">'
                        . '    <i class="fa fa-paint-brush" style="color:#ffffff !important;"></i> <span style="color:#ffffff !important; font-weight:600;">Studio RPS</span>'
                        . '  </a>'
                        . '  <button type="button" data-id="' . $l->id_rps . '" data-name="' . htmlspecialchars($l->nm_mk, ENT_QUOTES, 'UTF-8') . '" onclick="delete_rps_obe_btn(this)" class="btn btn-sm btn-action-delete" style="background-color:#fff1f2 !important; color:#be123c !important; border:1px solid #fda4af !important; font-weight:600; font-size:12px; padding:6px 10px; border-radius:4px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Hapus RPS">'
                        . '    <i class="fa fa-trash-o" style="color:#be123c !important;"></i> <span style="color:#be123c !important; font-weight:600;">Delete</span>'
                        . '  </button>'
                        . '</div>';

            $row[] = $col_no;
            $row[] = $col_mk;
            $row[] = $col_cpmk;
            $row[] = $col_action;

            $data[] = $row;
        }

        $output = [
            "draw"            => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
            "recordsTotal"    => $this->Rps_obe_model->count_all(),
            "recordsFiltered" => $this->Rps_obe_model->count_filtered(),
            "data"            => $data,
        ];

        echo json_encode($output);
    }

    public function ajax_delete($id = null)
    {
        $this->output->set_content_type('application/json');

        if (empty($id) || (!is_numeric($id) && strlen($id) !== 32)) {
            $id = $this->input->post('id');
        }
        if (empty($id)) {
            $id = $this->input->get('id');
        }
        if (empty($id)) {
            $total_segments = $this->uri->total_segments();
            for ($s = $total_segments; $s >= 1; $s--) {
                $seg = $this->uri->segment($s);
                if (!empty($seg) && (is_numeric($seg) || strlen($seg) === 32)) {
                    $id = $seg;
                    break;
                }
            }
        }

        if (empty($id)) {
            echo json_encode(['status' => false, 'pesan' => 'ID RPS tidak valid.']);
            return;
        }

        $rps = $this->Rps_obe_model->get_by_id($id);

        if (!$rps) {
            echo json_encode(['status' => false, 'pesan' => 'Data RPS OBE tidak ditemukan atau sudah dihapus.']);
            return;
        }

        $deleted = $this->Rps_obe_model->delete_by_id($id);
        if ($deleted) {
            echo json_encode(['status' => true, 'pesan' => 'Data RPS OBE untuk mata kuliah ' . ($rps->nm_mk ?? '') . ' berhasil dihapus.']);
        } else {
            echo json_encode(['status' => false, 'pesan' => 'Gagal menghapus data RPS dari database.']);
        }
    }

    public function preview_rps($id_md5 = null)
    {
        $data_rps = $this->db->where('md5(id_rps)', $id_md5)
            ->get('siakad_rps_obe')->row();

        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        // Fetch feeder MK info if id_mk is empty
        $id_mk = $data_rps->id_mk;
        $mk_row = null;
        if (!empty($id_mk)) {
            $mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
        } elseif (!empty($data_rps->kode_mk)) {
            $mk_row = $this->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
            if ($mk_row && empty($data_rps->id_mk)) {
                $data_rps->id_mk = $mk_row->id_mk;
                $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', ['id_mk' => $mk_row->id_mk]);
                $id_mk = $mk_row->id_mk;
            }
        }

        // Resolusi Nama Prodi / Lembaga
        $nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
        if ($mk_row && !empty($mk_row->id_sms)) {
            $sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
            if ($sms_row && !empty($sms_row->nm_lemb)) {
                $nm_lemb = $sms_row->nm_lemb;
            }
        }

        $data_rps_detail = $this->Rps_obe_model->get_details($data_rps->id_rps);
        $sks_target = (int)($data_rps->sks_mk ?: 3);
        $needs_reparse = empty($data_rps_detail) || count($data_rps_detail) < 14;
        if (!$needs_reparse && !empty($data_rps_detail)) {
            $sample = $data_rps_detail[0];
            $total_menit = (int)$sample->kolom_8 + (int)$sample->kolom_9;
            // Reparse jika total waktu != total menit SKS, atau T > 200, atau P == 0, atau kolom_7 kosong/angka 0, atau kolom_10 salah isi referensi
            if ($total_menit != ($sks_target * 50) || (int)$sample->kolom_8 > 200 || (int)$sample->kolom_9 == 0 || empty($sample->kolom_7) || $sample->kolom_7 === '0' || preg_match('/kotler|chaffey|buku|jurnal|press|wbi/i', $sample->kolom_10)) {
                $needs_reparse = true;
            }
        }

        if ($needs_reparse && !empty($data_rps->raw_ai_output)) {
            $parsed_details = $this->_parse_markdown_table_to_matrix($data_rps->raw_ai_output, (int)$data_rps->sks_mk, (int)$data_rps->sks_tm, (int)$data_rps->sks_prak);
            if (!empty($parsed_details)) {
                $this->Rps_obe_model->save_details($data_rps->id_rps, $parsed_details);
                $data_rps_detail = $this->Rps_obe_model->get_details($data_rps->id_rps);
            }
        }

        $dosen_row = $this->db->where('id_ptk', $data_rps->id_ptk)->get('siakad_dosen')->row();
        $koordinator = !empty($data_rps->koordinator_rps) ? $this->db->where(['id_ptk' => $data_rps->koordinator_rps])->get('siakad_dosen')->row() : $dosen_row;
        if (!$koordinator) {
            $koordinator = (object)['nm_ptk' => $data_rps->dosen_pengampu ?: '-'];
        }

        $kaprodi_info = $this->_resolve_kaprodi($data_rps, $mk_row, $nm_lemb);
        $kaprodi = null;
        if (!empty($data_rps->kaprodi_rps)) {
            $kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->get('siakad_rps_koordinator');
        }
        if (!$kaprodi || (is_object($kaprodi) && method_exists($kaprodi, 'num_rows') && $kaprodi->num_rows() == 0)) {
            $kaprodi = $this->db->where('1=0')->get('siakad_rps_koordinator');
        }

        // Resolusi Penjabaran SKS (Total SKS & Rincian TM / Prak Lap / Prak / Sim)
        $total_sks = (int)($data_rps->sks_mk ?: ($mk_row->sks_mk ?? 3));
        $rincian_parts = [];
        if (!empty($mk_row)) {
            if ((float)$mk_row->sks_tm > 0) $rincian_parts[] = 'TM: ' . (int)$mk_row->sks_tm;
            if ((float)$mk_row->sks_prak > 0) $rincian_parts[] = 'Prak: ' . (int)$mk_row->sks_prak;
            if ((float)$mk_row->sks_prak_lap > 0) $rincian_parts[] = 'Prak Lap: ' . (int)$mk_row->sks_prak_lap;
            if ((float)$mk_row->sks_sim > 0) $rincian_parts[] = 'Sim: ' . (int)$mk_row->sks_sim;
        }
        if (empty($rincian_parts)) {
            if ((int)$data_rps->sks_tm > 0) $rincian_parts[] = 'TM: ' . (int)$data_rps->sks_tm;
            if ((int)$data_rps->sks_prak > 0) $rincian_parts[] = 'Prak: ' . (int)$data_rps->sks_prak;
        }
        $data_rps->sks_total_display = $total_sks . ' SKS';
        $data_rps->sks_rincian_str   = !empty($rincian_parts) ? '(' . implode(', ', $rincian_parts) . ')' : '';

        // Fallback auto-extract from raw_ai_output if fields are empty or corrupted
        if (!empty($data_rps->raw_ai_output)) {
            $raw = $data_rps->raw_ai_output;

            // Pola regex awalan section yang universal (mendukung: BAGIAN 3:, BAB 3:, ## 3., 3., dll.)
            $sec_prefix = '(?:^|\n)(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?';
            $lookahead_prefix = '\n+(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?';

            // 1. CPL
            $cpl_is_corrupt = (!empty($data_rps->cpl) && (stripos($data_rps->cpl, 'BAGIAN 3') !== false || stripos($data_rps->cpl, 'Rumusan CPMK') !== false || stripos($data_rps->cpl, 'Tabel Matriks') !== false || str_contains($data_rps->cpl, '|') || stripos($data_rps->cpl, 'CPMK-') !== false));
            if (empty($data_rps->cpl) || trim((string)$data_rps->cpl) === '' || $cpl_is_corrupt) {
                if (preg_match('/' . $sec_prefix . '(?:CAPAIAN PEMBELAJARAN LULUSAN|CPL\b)[^\n]*\n+([\s\S]*?)(?=' . $lookahead_prefix . '(?:TUJUAN BELAJAR|CPMK|CAPAIAN PEMBELAJARAN MATA KULIAH|DESKRIPSI|REFERENSI|DETAIL RENCANA)|\Z)/i', $raw, $m)) {
                    $clean_cpl_extracted = trim($m[1]);
                    $clean_cpl_extracted = preg_replace('/(?:\n+|\A)(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?(?:CAPAIAN PEMBELAJARAN MATA KULIAH|CPMK|TUJUAN BELAJAR|MATRIKS\s*KORELASI|MATRIKS\s*DISTRIBUSI|BLUEPRINT\s*ASESMEN|RUMUSAN\s*CPMK)[^\n]*\n+[\s\S]*$/i', '', $clean_cpl_extracted);
                    $clean_cpl_extracted = preg_replace('/(?:\n+|\A)\s*\|[^\n]+\|[\s\S]*$/i', '', $clean_cpl_extracted);
                    $data_rps->cpl = trim($clean_cpl_extracted);
                }
            }

            // 2. CPMK / Tujuan Belajar & Matriks Asesmen
            $has_matrix = (!empty($data_rps->tujuan_belajar) && str_contains($data_rps->tujuan_belajar, '|'));
            if ((empty($data_rps->tujuan_belajar) || trim((string)$data_rps->tujuan_belajar) === '' || !$has_matrix) && !empty($raw)) {
                if (preg_match('/' . $sec_prefix . '(?:TUJUAN BELAJAR|CPMK\b|CAPAIAN PEMBELAJARAN MATA KULIAH|MATRIKS KORELASI CPMK)[^\n]*\n+([\s\S]*?)(?=' . $lookahead_prefix . '(?:DESKRIPSI|RINGKASAN MATA KULIAH|REFERENSI|DAFTAR PUSTAKA|DETAIL RENCANA)|\Z)/i', $raw, $m)) {
                    $data_rps->tujuan_belajar = trim($m[1]);
                }
            }

            // 3. Deskripsi Singkat & Prasyarat
            $desk_is_corrupt = (!empty($data_rps->deskripsi_mata_kuliah) && (stripos($data_rps->deskripsi_mata_kuliah, 'Prasyarat') !== false || preg_match('/^\s*\d+[\.\)]\s*(?:Deskripsi|\*)/i', $data_rps->deskripsi_mata_kuliah)));
            if (empty($data_rps->deskripsi_mata_kuliah) || trim((string)$data_rps->deskripsi_mata_kuliah) === '' || $desk_is_corrupt) {
                if (preg_match('/' . $sec_prefix . '(?:DESKRIPSI SINGKAT MATA KULIAH & PRASYARAT|DESKRIPSI SINGKAT|DESKRIPSI MATA KULIAH|DESKRIPSI|RINGKASAN MATA KULIAH)[^\n]*\n+([\s\S]*?)(?=' . $lookahead_prefix . '(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA|DETAIL RENCANA|TABEL|MATRIKS)|\Z)/i', $raw, $m)) {
                    $desk_full = trim($m[1]);
                    if (empty($data_rps->prasyarat_mata_kuliah) || $data_rps->prasyarat_mata_kuliah === 'Tidak ada' || preg_match('/^\s*[\d\.\*\-]*\s*tidak ada/i', $data_rps->prasyarat_mata_kuliah)) {
                        if (preg_match('/(?:^|\n)\s*(?:\d+[\.\)]\s*)?(?:[\-\*•`\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n*([^\n\r]+)/i', $desk_full, $m_pra)) {
                            $data_rps->prasyarat_mata_kuliah = trim($m_pra[1]);
                        }
                    }
                    $desk_full = preg_replace('/(?:\n+|\A)\s*(?:\d+[\.\)]\s*)?(?:[\-\*•`\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)[\s\S]*$/i', '', $desk_full);
                    $desk_full = preg_replace('/^(?:\s*\d+[\.\)]\s*)?(?:[\-\*•`#\s]*)(?:DESKRIPSI\s*(?:SINGKAT)?(?:\s*MATA\s*KULIAH)?|RINGKASAN\s*MATA\s*KULIAH)(?:\*{1,2})?[:\s\-]*\n*/i', '', trim($desk_full));
                    $desk_full = preg_replace('/^(?:\s*\d+[\.\)]\s*|\s*[\-\*•]\s*)+/u', '', trim($desk_full));
                    $desk_full = preg_replace('/(?:\n+|\A)(?:[#*_`\s]*)[0-9A-Za-z\.\-_]{1,10}(?:[#*_`\s]*)$/i', '', trim($desk_full));
                    $data_rps->deskripsi_mata_kuliah = trim($desk_full);
                } else {
                    // Bersihkan yang sudah ada
                    $clean_d = preg_replace('/(?:\n+|\A)\s*(?:\d+[\.\)]\s*)?(?:[\-\*•`\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)[\s\S]*$/i', '', $data_rps->deskripsi_mata_kuliah);
                    $clean_d = preg_replace('/^(?:\s*\d+[\.\)]\s*)?(?:[\-\*•`#\s]*)(?:DESKRIPSI\s*(?:SINGKAT)?(?:\s*MATA\s*KULIAH)?|RINGKASAN\s*MATA\s*KULIAH)(?:\*{1,2})?[:\s\-]*\n*/i', '', trim($clean_d));
                    $clean_d = preg_replace('/^(?:\s*\d+[\.\)]\s*|\s*[\-\*•]\s*)+/u', '', trim($clean_d));
                    $data_rps->deskripsi_mata_kuliah = trim($clean_d);
                }
            }

            // 4. Prasyarat Mata Kuliah (jika terpisah atau perlu pembersihan)
            if (empty($data_rps->prasyarat_mata_kuliah) || trim((string)$data_rps->prasyarat_mata_kuliah) === '') {
                if (preg_match('/' . $sec_prefix . '(?:PRASYARAT|MATA KULIAH PRASYARAT)[^\n]*\n+([\s\S]*?)(?=' . $lookahead_prefix . '(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA|DETAIL RENCANA)|\Z)/i', $raw, $m)) {
                    $data_rps->prasyarat_mata_kuliah = trim($m[1]);
                }
            }
            $clean_pra = preg_replace('/^(?:\s*\d+[\.\)]\s*)?(?:[\-\*•`#\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n*/i', '', trim($data_rps->prasyarat_mata_kuliah ?? ''));
            $clean_pra = preg_replace('/^(?:\s*\d+[\.\)]\s*|\s*[\-\*•]\s*)+/u', '', trim($clean_pra));
            $clean_pra = trim($clean_pra);
            $clean_pra_low = strtolower(preg_replace('/[^a-z]/', '', $clean_pra));
            if (empty($clean_pra) || $clean_pra_low === 'tidakada' || $clean_pra_low === 'none' || $clean_pra_low === '' || $clean_pra === '-') {
                $data_rps->prasyarat_mata_kuliah = 'Tidak ada';
            } else {
                $data_rps->prasyarat_mata_kuliah = $clean_pra;
            }

            // 5. Referensi / Daftar Pustaka
            if (empty($data_rps->referensi) || trim((string)$data_rps->referensi) === '') {
                if (preg_match('/' . $sec_prefix . '(?:DAFTAR REFERENSI|REFERENSI|DAFTAR PUSTAKA|PUSTAKA)[^\n]*\n+([\s\S]*?)(?=' . $lookahead_prefix . '(?:DETAIL RENCANA|TABEL|MATRIKS|DISTRIBUSI BOBOT|PENILAIAN)|\Z)/i', $raw, $m)) {
                    $data_rps->referensi = trim($m[1]);
                }
            }

            // Simpan sinkronisasi ke tabel siakad_rps_obe
            $update_payload = [];
            if (!empty($data_rps->cpl)) $update_payload['cpl'] = $data_rps->cpl;
            if (!empty($data_rps->tujuan_belajar)) $update_payload['tujuan_belajar'] = $data_rps->tujuan_belajar;
            if (!empty($data_rps->deskripsi_mata_kuliah)) $update_payload['deskripsi_mata_kuliah'] = $data_rps->deskripsi_mata_kuliah;
            if (!empty($data_rps->prasyarat_mata_kuliah)) $update_payload['prasyarat_mata_kuliah'] = $data_rps->prasyarat_mata_kuliah;
            if (!empty($data_rps->referensi)) $update_payload['referensi'] = $data_rps->referensi;
            if (!empty($update_payload)) {
                $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', $update_payload);
            }
        }

        // Bersihkan duplikasi bagian Prasyarat jika masih menempel di dalam deskripsi_mata_kuliah
        if (!empty($data_rps->deskripsi_mata_kuliah) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:PRASYARAT|MATA KULIAH PRASYARAT)[^\n]*\n+/i', $data_rps->deskripsi_mata_kuliah)) {
            if (preg_match('/^(.*?)(?=(?:\n+[#*]{1,4}\s*)?(?:PRASYARAT|MATA KULIAH PRASYARAT)[^\n]*\n)/is', $data_rps->deskripsi_mata_kuliah, $m_clean_desk)) {
                $data_rps->deskripsi_mata_kuliah = trim($m_clean_desk[1]);
                $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', ['deskripsi_mata_kuliah' => $data_rps->deskripsi_mata_kuliah]);
            }
        }

        // Bersihkan flag draft/validasi agar dokumen resmi dan siap saji
        $clean_validation_fn = function($str) {
            if (empty($str)) return $str;
            $str = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $str);
            $str = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $str);
            return trim($str);
        };

        $fields_to_clean = ['cpl', 'tujuan_belajar', 'deskripsi_mata_kuliah', 'prasyarat_mata_kuliah', 'referensi'];
        $clean_update = [];
        foreach ($fields_to_clean as $f) {
            if (!empty($data_rps->$f)) {
                $cleaned = $clean_validation_fn($data_rps->$f);
                if ($cleaned !== $data_rps->$f) {
                    $data_rps->$f = $cleaned;
                    $clean_update[$f] = $cleaned;
                }
            }
        }
        if (!empty($clean_update)) {
            $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', $clean_update);
        }

        // Auto-isi tanggal_rps jika masih kosong (backfill untuk record lama)
        if (empty($data_rps->tanggal_rps)) {
            $fallback_tgl = !empty($data_rps->create_date)
                ? date('Y-m-d', strtotime($data_rps->create_date))
                : date('Y-m-d');
            $data_rps->tanggal_rps = $fallback_tgl;
            $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', ['tanggal_rps' => $fallback_tgl]);
        }

        $output['data_rps']        = $data_rps;
        $output['data_rps_detail'] = $data_rps_detail;
        $output['koordinator_rps'] = $koordinator;
        $output['kaprodi_rps']     = $kaprodi;
        $output['kaprodi_info']    = $kaprodi_info;
        $output['rps_kelola']      = 'NIL';
        $output['nm_lemb']         = $nm_lemb;

        // Cek jika user meminta format mPDF (?pdf=1)
        if ($this->input->get('pdf') == '1') {
            $output['is_mpdf'] = true;
            $style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
            $html  = $this->load->view('cetak/rps/rps_halaman_1_portrait', $output, true);
            $html2 = $this->load->view('cetak/rps/rps_halaman_2_landscape', $output, true);

            $doc_title    = 'RPS ' . ($data_rps->nm_mk ?? '');
            $pdfFilePath  = 'RPS_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk) . '.pdf';
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Folio-P']);
            $mpdf->SetTitle($doc_title);
            $mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
            $mpdf->SetSubject($doc_title);

            $clean_style = preg_replace('/<\/?style[^>]*>/i', '', $style);
            $mpdf->WriteHTML($clean_style, \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            $mpdf->AddPage('Folio-L');
            $mpdf->WriteHTML($html2, \Mpdf\HTMLParserMode::HTML_BODY);

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $mpdf->Output($pdfFilePath, "I");
            return;
        }

        // Tampilkan HTML Media Print Preview yang super tajam, modern & profesional (WBI Theme)
        $output['is_mpdf'] = false;
        $this->load->view('cetak/rps/rps_html_print', $output);
    }

    public function preview_kontrak($id_md5 = null)
    {
        $data_rps = $this->db->where('md5(id_rps)', $id_md5)->get('siakad_rps_obe')->row();
        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        $id_mk = $data_rps->id_mk;
        $nm_lemb = '';
        $mk_row = null;
        if (!empty($id_mk)) {
            $mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
        }
        if (!$mk_row && !empty($data_rps->kode_mk)) {
            $mk_row = $this->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
        }
        if (!$mk_row && !empty($data_rps->nm_mk)) {
            $mk_row = $this->db->where('nm_mk', $data_rps->nm_mk)->get('feeder_mata_kuliah')->row();
        }

        if ($mk_row && !empty($mk_row->id_sms)) {
            $sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
            if ($sms_row && !empty($sms_row->nm_lemb)) $nm_lemb = $sms_row->nm_lemb;
        }

        if (empty($nm_lemb) && !empty($data_rps->kaprodi_rps)) {
            $kap_r = $this->db->where('id_ptk', $data_rps->kaprodi_rps)->get('kaprodi')->row();
            if ($kap_r && !empty($kap_r->nama)) $nm_lemb = $kap_r->nama;
        }
        if (empty($nm_lemb) && !empty($data_rps->id_ptk)) {
            $dsn_sms = $this->db->query("SELECT s.nm_lemb FROM siakad_dosen d JOIN sms s ON d.id_sms = s.id_sms WHERE d.id_ptk = ? LIMIT 1", [$data_rps->id_ptk])->row();
            if ($dsn_sms && !empty($dsn_sms->nm_lemb)) $nm_lemb = $dsn_sms->nm_lemb;
        }
        if (empty($nm_lemb)) {
            $nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
        }

        $dosen_row = $this->db->where('id_ptk', $data_rps->id_ptk)->get('siakad_dosen')->row();
        $koordinator = !empty($data_rps->koordinator_rps) ? $this->db->where(['id_ptk' => $data_rps->koordinator_rps])->get('siakad_dosen')->row() : $dosen_row;
        if (!$koordinator) {
            $koordinator = (object)['nm_ptk' => $data_rps->dosen_pengampu ?: '-'];
        }

        $kaprodi_info = $this->_resolve_kaprodi($data_rps, $mk_row ?? null, $nm_lemb);
        $kaprodi = null;
        if (!empty($data_rps->kaprodi_rps)) {
            $kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->get('siakad_rps_koordinator');
        }
        if (!$kaprodi || (is_object($kaprodi) && method_exists($kaprodi, 'num_rows') && $kaprodi->num_rows() == 0)) {
            $kaprodi = $this->db->where('1=0')->get('siakad_rps_koordinator');
        }

        $output['data_rps']        = $data_rps;
        $output['koordinator_rps'] = $koordinator;
        $output['kaprodi_rps']     = $kaprodi;
        $output['kaprodi_info']    = $kaprodi_info;
        $output['rps_kelola']      = 'NIL';
        $output['nm_lemb']         = $nm_lemb;
        $output['is_mpdf']          = true;

        $style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
        $html  = $this->load->view('cetak/rps/rps_kontrak_kuliah', $output, true);

        $doc_title    = 'Kontrak Pembelajaran ' . ($data_rps->nm_mk ?? '');
        $pdfFilePath  = 'Kontrak_Pembelajaran_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk) . '.pdf';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
        $mpdf->SetTitle($doc_title);
        $mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
        $mpdf->SetSubject($doc_title);

        $clean_style = preg_replace('/<\/?style[^>]*>/i', '', $style);
        $mpdf->WriteHTML($clean_style, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf->Output($pdfFilePath, "I");
    }

    public function preview_rancangan_tugas($id_md5 = null)
    {
        $data_rps = $this->db->where('md5(id_rps)', $id_md5)->get('siakad_rps_obe')->row();
        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        $id_mk = $data_rps->id_mk;
        $nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
        if (!empty($id_mk)) {
            $mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
            if ($mk_row && !empty($mk_row->id_sms)) {
                $sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
                if ($sms_row) $nm_lemb = $sms_row->nm_lemb;
            }
        }

        $dosen_row = $this->db->where('id_ptk', $data_rps->id_ptk)->get('siakad_dosen')->row();
        $koordinator = !empty($data_rps->koordinator_rps) ? $this->db->where(['id_ptk' => $data_rps->koordinator_rps])->get('siakad_dosen')->row() : $dosen_row;
        if (!$koordinator) {
            $koordinator = (object)['nm_ptk' => $data_rps->dosen_pengampu ?: '-'];
        }

        $kaprodi_info = $this->_resolve_kaprodi($data_rps, $mk_row ?? null, $nm_lemb);
        $kaprodi = null;
        if (!empty($data_rps->kaprodi_rps)) {
            $kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->get('siakad_rps_koordinator');
        }
        if (!$kaprodi || (is_object($kaprodi) && method_exists($kaprodi, 'num_rows') && $kaprodi->num_rows() == 0)) {
            $kaprodi = $this->db->where('1=0')->get('siakad_rps_koordinator');
        }

        $output['data_rps']        = $data_rps;
        $output['koordinator_rps'] = $koordinator;
        $output['kaprodi_rps']     = $kaprodi;
        $output['kaprodi_info']    = $kaprodi_info;
        $output['rps_kelola']      = 'NIL';
        $output['nm_lemb']         = $nm_lemb;
        $output['is_mpdf']          = true;

        $style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
        $html  = $this->load->view('cetak/rps/rps_rancangan_tugas', $output, true);

        $doc_title    = 'Rancangan Tugas ' . ($data_rps->nm_mk ?? '');
        $pdfFilePath  = 'Rancangan_Tugas_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk) . '.pdf';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
        $mpdf->SetTitle($doc_title);
        $mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
        $mpdf->SetSubject($doc_title);

        $clean_style = preg_replace('/<\/?style[^>]*>/i', '', $style);
        $mpdf->WriteHTML($clean_style, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf->Output($pdfFilePath, "I");
    }

    public function export_excel($id_rps)
    {
        $data_rps = $this->Rps_obe_model->get_by_id($id_rps);
        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        $details = $this->Rps_obe_model->get_details($id_rps);

        // Ekstrak dan mapping referensi dari data RPS
        [$ref_map, $ref_short_map] = $this->parse_reference_map_for_excel($data_rps->referensi ?? '');

        $clean_cell_text = function($val) {
            if ($val === null || $val === '') return '';
            $val = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</li>'], "\n", (string)$val);
            $val = strip_tags($val);
            $val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
            $val = preg_replace('/\*\*(.*?)\*\*/s', '$1', $val);
            $val = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '$1', $val);
            $val = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/s', '$1', $val);
            $val = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '$1', $val);
            return trim($val);
        };

        $template_path = APPPATH . 'libraries/ai_skills/akademik-dosen-obe-fiq/templates/template_rps_wbi_rincian_pertemuan.xlsx';
        if (!file_exists($template_path)) {
            $template_path = APPPATH . 'third_party/rps_obe/template_rps_wbi_rincian_pertemuan.xlsx';
        }

        if (file_exists($template_path)) {
            $spreadsheet = IOFactory::load($template_path);
            $sheet = $spreadsheet->getActiveSheet();

            $start_row = 6;
            foreach ($details as $index => $r) {
                $row_num = $start_row + $index;
                if ($row_num > 21) break;

                $sumber_clean = $this->format_sumber_belajar_excel($r->kolom_13, $ref_map, $ref_short_map);

                $sheet->setCellValue('A' . $row_num, !empty($r->kolom_1) ? (int)$r->kolom_1 : ($index + 1));
                $sheet->setCellValue('B' . $row_num, !empty($r->kolom_2) ? (int)$r->kolom_2 : ($index + 1));
                $sheet->setCellValue('C' . $row_num, $clean_cell_text($r->kolom_3));
                $sheet->setCellValue('D' . $row_num, $clean_cell_text($r->kolom_4));
                $sheet->setCellValue('E' . $row_num, $clean_cell_text($r->kolom_5));
                $sheet->setCellValue('F' . $row_num, $clean_cell_text($r->kolom_6));
                $sheet->setCellValue('G' . $row_num, $clean_cell_text($r->kolom_7));
                $sheet->setCellValue('H' . $row_num, (int)$r->kolom_8);
                $sheet->setCellValue('I' . $row_num, (int)$r->kolom_9);
                $sheet->setCellValue('J' . $row_num, $clean_cell_text($r->kolom_10));
                $sheet->setCellValue('K' . $row_num, $clean_cell_text($r->kolom_11));
                $sheet->setCellValue('L' . $row_num, (int)$r->kolom_12);
                $sheet->setCellValue('M' . $row_num, $sumber_clean);
            }

            // Rapikan alignment dan wrap text untuk cell data
            $sheet->getStyle('A6:M21')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            $sheet->getStyle('C6:M21')->getAlignment()->setWrapText(true);
            $sheet->setCellValue('A23', 'Keterangan: M = Minggu, P = Pertemuan, T = Teori (dalam menit), Pr = Praktek (dalam menit)');
            $sheet->getStyle('A23')->getFont()->setItalic(true)->setSize(9);
        } else {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'M');
            $sheet->setCellValue('B1', 'P');
            $sheet->setCellValue('C1', 'Kemampuan Akhir Tiap Tahapan Belajar (Sub-CPMK)');
            $sheet->setCellValue('D1', 'Bahan Kajian');
            $sheet->setCellValue('E1', 'Bentuk dan Modalitas Pembelajaran');
            $sheet->setCellValue('F1', 'Metode Pembelajaran');
            $sheet->setCellValue('G1', 'Pengalaman Belajar');
            $sheet->setCellValue('H1', 'Alokasi Waktu (Teori)');
            $sheet->setCellValue('I1', 'Alokasi Waktu (Praktek)');
            $sheet->setCellValue('J1', 'Kriteria Penilaian');
            $sheet->setCellValue('K1', 'Indikator Penilaian');
            $sheet->setCellValue('L1', 'Bobot (%)');
            $sheet->setCellValue('M1', 'Sumber Belajar');

            $row_num = 2;
            foreach ($details as $index => $r) {
                $sumber_clean = $this->format_sumber_belajar_excel($r->kolom_13, $ref_map, $ref_short_map);

                $sheet->setCellValue('A' . $row_num, !empty($r->kolom_1) ? (int)$r->kolom_1 : ($index + 1));
                $sheet->setCellValue('B' . $row_num, !empty($r->kolom_2) ? (int)$r->kolom_2 : ($index + 1));
                $sheet->setCellValue('C' . $row_num, $clean_cell_text($r->kolom_3));
                $sheet->setCellValue('D' . $row_num, $clean_cell_text($r->kolom_4));
                $sheet->setCellValue('E' . $row_num, $clean_cell_text($r->kolom_5));
                $sheet->setCellValue('F' . $row_num, $clean_cell_text($r->kolom_6));
                $sheet->setCellValue('G' . $row_num, $clean_cell_text($r->kolom_7));
                $sheet->setCellValue('H' . $row_num, (int)$r->kolom_8);
                $sheet->setCellValue('I' . $row_num, (int)$r->kolom_9);
                $sheet->setCellValue('J' . $row_num, $clean_cell_text($r->kolom_10));
                $sheet->setCellValue('K' . $row_num, $clean_cell_text($r->kolom_11));
                $sheet->setCellValue('L' . $row_num, (int)$r->kolom_12);
                $sheet->setCellValue('M' . $row_num, $sumber_clean);
                $row_num++;
            }
            $sheet->setCellValue('A' . ($row_num + 1), 'Keterangan: M = Minggu, P = Pertemuan, T = Teori (dalam menit), Pr = Praktek (dalam menit)');
            $sheet->getStyle('A' . ($row_num + 1))->getFont()->setItalic(true)->setSize(9);
        }

        $kode_mk = preg_replace('/[^A-Za-z0-9_\-]/', '', $data_rps->kode_mk ?: 'MK');
        $nama_mk = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk ?: 'RPS_OBE');
        $filename = "RPS_OBE_{$kode_mk}_{$nama_mk}_RincianPertemuan_WBI.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Helper: Parse teks referensi RPS menjadi array mapping nomor referensi
     */
    private function parse_reference_map_for_excel($ref_text)
    {
        $ref_map = [];
        $ref_short_map = [];
        if (empty($ref_text)) return [$ref_map, $ref_short_map];

        if (preg_match('/<[a-z]/i', $ref_text)) {
            $ref_text = preg_replace('/<\/(?:p|li|tr|div)>/i', "\n", $ref_text);
            $ref_text = strip_tags($ref_text);
        }
        $r_lines = explode("\n", str_replace("\r", "", $ref_text));
        $r_idx = 1;
        foreach ($r_lines as $r_line) {
            $r_trim = trim($r_line);
            if ($r_trim === '' || preg_match('/^(?:[#*]{1,4}\s*)?(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA)/i', $r_trim)) continue;

            $num = $r_idx;
            $content = $r_trim;
            if (preg_match('/^(\d+)[\.\)]\s*(.*)$/', $r_trim, $m_r)) {
                $num = (int)$m_r[1];
                $content = trim($m_r[2]);
            } elseif (preg_match('/^[\-\*•\x{2022}]\s*(.*)$/u', $r_trim, $m_r)) {
                $content = trim($m_r[1]);
                $content = preg_replace('/^\d+[\.\)]\s*/', '', $content);
            }

            if (!empty($content)) {
                $clean_c = preg_replace('/^[\d\.\)\-\*•\s]+/', '', $content);
                $clean_c = preg_replace('/[*_]/', '', $clean_c);
                $clean_c = trim($clean_c);
                $ref_map[$num] = $clean_c;

                // Format short representation: Author (Year): Title
                if (preg_match('/^(.*?)\s*\((\d{4}[a-z]?)\)\s*[\.:,]\s*(.*)$/u', $clean_c, $m_apa)) {
                    $raw_auth = trim($m_apa[1]);
                    $year = $m_apa[2];
                    $rest = trim($m_apa[3]);

                    $auth_display = '';
                    if (preg_match('/^([A-Za-z\x{00C0}-\x{017F}\-\'\s]+?)(?:,|\.|\s*&)/u', $raw_auth, $m_sur)) {
                        $auth_display = trim($m_sur[1]);
                        if (stripos($raw_auth, '&') !== false || stripos($raw_auth, 'and') !== false || stripos($raw_auth, 'et al') !== false) {
                            $auth_display .= ' et al.';
                        }
                    } else {
                        $auth_display = $raw_auth;
                    }

                    $rest_parts = explode('.', $rest);
                    $raw_title = trim($rest_parts[0]);
                    if (empty($raw_title) && isset($rest_parts[1])) {
                        $raw_title = trim($rest_parts[1]);
                    }
                    if (empty($raw_title)) $raw_title = $rest;

                    if (mb_strlen($raw_title) > 45) {
                        $raw_title = mb_substr($raw_title, 0, 43) . '...';
                    }
                    $stitle = $auth_display . ' (' . $year . '): ' . $raw_title;
                } elseif (preg_match('/^(.*?)\s*["“‘]([^"”’]+)["”’](.*)$/u', $clean_c, $m_q)) {
                    $raw_auth = trim($m_q[1]);
                    $raw_title = trim($m_q[2]);
                    if (mb_strlen($raw_title) > 45) {
                        $raw_title = mb_substr($raw_title, 0, 43) . '...';
                    }
                    $auth_display = !empty($raw_auth) ? trim($raw_auth, ' .,:') . ': ' : '';
                    $stitle = $auth_display . $raw_title;
                } else {
                    $first_part = explode('.', $clean_c)[0];
                    if (mb_strlen($first_part) > 45) {
                        $first_part = mb_substr($first_part, 0, 43) . '...';
                    }
                    $stitle = $first_part;
                }

                $ref_short_map[$num] = $stitle;
                $r_idx++;
            }
        }
        return [$ref_map, $ref_short_map];
    }

    /**
     * Helper: Format kolom Sumber Belajar untuk Excel (resolusi [Ref 1, Ref 2] menjadi referensi lengkap/ringkas)
     */
    private function format_sumber_belajar_excel($raw_text, $ref_map = [], $ref_short_map = [])
    {
        if (empty($raw_text)) return '';
        $clean = trim(strip_tags((string)$raw_text));
        $clean = html_entity_decode($clean, ENT_QUOTES, 'UTF-8');
        $clean = preg_replace('/\*\*(.*?)\*\*/', '$1', $clean);
        $clean = preg_replace('/[*_]/', '', $clean);

        // Jika hanya berisi nomor rujukan murni seperti "[1]", "[1, 2]", "[Ref 1]", "[Ref 1, Ref 2]", "Ref 1; Ref 2"
        if (preg_match('/^(?:\[?\s*(?:Ref(?:erensi)?|Pustaka)?\s*\d{1,2}\s*\]?[\s,;–\-\/]*)+$/i', $clean)) {
            if (preg_match_all('/\b(\d{1,2})\b/', $clean, $matches)) {
                $nums = array_unique($matches[1]);
                if (!empty($nums)) {
                    $items = [];
                    foreach ($nums as $n) {
                        $n_int = (int)$n;
                        if (isset($ref_short_map[$n_int])) {
                            $items[] = "[Ref {$n_int}] " . $ref_short_map[$n_int];
                        } elseif (isset($ref_map[$n_int])) {
                            $items[] = "[Ref {$n_int}] " . $ref_map[$n_int];
                        } else {
                            $items[] = "[Ref {$n_int}]";
                        }
                    }
                    return implode("\n", $items);
                }
            }
        }

        return $clean;
    }

    public function preview_roadmap($id_md5 = null)
    {
        $data_rps = $this->db->where('md5(id_rps)', $id_md5)->get('siakad_rps_obe')->row();
        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        $id_mk = $data_rps->id_mk;
        $nm_lemb = '';
        $mk_row = null;
        if (!empty($id_mk)) {
            $mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
        }
        if (!$mk_row && !empty($data_rps->kode_mk)) {
            $mk_row = $this->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
        }
        if ($mk_row && !empty($mk_row->id_sms)) {
            $sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
            if ($sms_row && !empty($sms_row->nm_lemb)) $nm_lemb = $sms_row->nm_lemb;
        }
        if (empty($nm_lemb)) {
            $nm_lemb = 'Politeknik Wilmar Bisnis Indonesia';
        }

        $data_rps_detail = $this->Rps_obe_model->get_details($data_rps->id_rps);
        $total_sks = (int)($data_rps->sks_mk ?: ($mk_row->sks_mk ?? 3));
        $data_rps->sks_total_display = $total_sks . ' SKS';

        $output['data_rps']        = $data_rps;
        $output['data_rps_detail'] = $data_rps_detail;
        $output['nm_lemb']         = $nm_lemb;
        $output['is_mpdf']         = false;

        $this->load->view('cetak/rps_obe/rps_html_print_roadmap', $output);
    }

    public function preview_skenario($id_md5 = null)
    {
        $data_rps = $this->db->where('md5(id_rps)', $id_md5)->get('siakad_rps_obe')->row();
        if (!$data_rps) {
            show_404('RPS OBE tidak ditemukan.');
            return;
        }

        $id_mk = $data_rps->id_mk;
        $nm_lemb = '';
        $mk_row = null;
        if (!empty($id_mk)) {
            $mk_row = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row();
        }
        if (!$mk_row && !empty($data_rps->kode_mk)) {
            $mk_row = $this->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
        }
        if ($mk_row && !empty($mk_row->id_sms)) {
            $sms_row = $this->db->where('id_sms', $mk_row->id_sms)->get('sms')->row();
            if ($sms_row && !empty($sms_row->nm_lemb)) $nm_lemb = $sms_row->nm_lemb;
        }
        if (empty($nm_lemb)) {
            $nm_lemb = $data_rps->prodi ?: 'Politeknik Wilmar Bisnis Indonesia';
        }

        $data_rps_detail = $this->Rps_obe_model->get_details($data_rps->id_rps);
        $total_sks = (int)($data_rps->sks_mk ?: ($mk_row->sks_mk ?? 3));
        $data_rps->sks_total_display = $total_sks . ' SKS';

        $kaprodi_info = $this->_resolve_kaprodi($data_rps, $mk_row ?? null, $nm_lemb);

        $skenario_md = $data_rps->skenario_pembelajaran_markdown ?: $data_rps->raw_ai_output;
        $meetings = $this->_parse_skenario_pertemuan($skenario_md);

        // Fallback to matriks 16 kolom if less than 16 meetings parsed
        if (count($meetings) < 16 && !empty($data_rps_detail)) {
            foreach ($data_rps_detail as $d) {
                $p_num = (int)$d->kolom_2;
                if ($p_num > 0 && !isset($meetings[$p_num])) {
                    $meetings[$p_num] = [
                        'pertemuan'            => $p_num,
                        'minggu'               => (int)($d->kolom_1 ?: $p_num),
                        'sub_cpmk'             => $d->kolom_3,
                        'bahan_kajian'         => $d->kolom_4,
                        'metode'               => ($d->kolom_5 ?: 'Kuliah Interaktif') . ($d->kolom_6 ? ' (' . $d->kolom_6 . ')' : ''),
                        'sumber'               => $d->kolom_13 ?: '',
                        'evaluasi'             => $d->kolom_12 ?: ($d->kolom_11 ?: ''),
                        'indikator_evaluasi'   => $d->kolom_10 ?: '',
                        'jam_teori'            => (float)($d->kolom_8 ?: 1),
                        'jam_praktek'          => (float)($d->kolom_9 ?: 2),
                        'skenario_scl'         => ($d->kolom_7 ? "Pengalaman Belajar: " . $d->kolom_7 : ""),
                        'draf_bap'             => "Perkuliahan tatap muka mengenai " . $d->kolom_4 . " dengan metode " . ($d->kolom_6 ?: 'pembelajaran aktif') . ". Mahasiswa aktif menganalisis materi dan berdiskusi kelompok. Sesi ditutup dengan evaluasi capaian dan arahan tugas."
                    ];
                }
            }
        }

        ksort($meetings);

        $output['data_rps']        = $data_rps;
        $output['data_rps_detail'] = $data_rps_detail;
        $output['nm_lemb']         = $nm_lemb;
        $output['kaprodi_info']    = $kaprodi_info;
        $output['meetings']        = $meetings;
        $output['is_mpdf']         = false;

        $this->load->view('cetak/rps_obe/rps_html_print_skenario', $output);
    }

    private function _parse_skenario_pertemuan($md)
    {
        $meetings = [];
        if (empty($md)) return $meetings;

        $parts = preg_split('/(?=###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*\d+)/i', $md);

        foreach ($parts as $part) {
            if (!preg_match('/###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*(\d+)/i', $part, $m_num)) {
                continue;
            }
            $meet_no = (int)$m_num[1];
            if ($meet_no < 1 || $meet_no > 16) continue;

            $sub_cpmk = '';
            if (preg_match('/(?:\*|\-)?\s*\*\*Kemampuan Akhir[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
                $sub_cpmk = trim($m[1]);
            }

            $bahan_kajian = '';
            if (preg_match('/(?:\*|\-)?\s*\*\*Bahan Kajian\*\*:\s*([^\n]+)/i', $part, $m)) {
                $bahan_kajian = trim($m[1]);
            }

            $metode = '';
            if (preg_match('/(?:\*|\-)?\s*\*\*Bentuk & Metode[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
                $metode = trim($m[1]);
            }

            $sumber = '';
            if (preg_match('/(?:\*|\-)?\s*\*\*Sumber & Media Belajar\*\*:\s*([^\n]+)/i', $part, $m)) {
                $sumber = trim($m[1]);
            }

            $evaluasi = '';
            if (preg_match('/(?:\*|\-)?\s*\*\*Kriteria & Indikator[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) {
                $evaluasi = trim($m[1]);
            }

            $jam_t = 1; $jam_p = 2;
            if (preg_match('/(\d+)\s*Menit Teori\s*\/\s*(\d+)\s*Menit Praktek/i', $part, $m)) {
                $jam_t = round(((int)$m[1]) / 50, 1);
                $jam_p = round(((int)$m[2]) / 50, 1);
            }

            $skenario_scl = '';
            if (preg_match('/####\s*(?:Skenario Langkah Pembelajaran|Sintaks SCL|Skenario Langkah Pelaksanaan)[^\n]*\n([\s\S]*?)(?=(?:####|---|```text|\Z))/i', $part, $m)) {
                $skenario_scl = trim($m[1]);
            }

            $draf_bap = '';
            if (preg_match('/####\s*[^\n]*(?:Draft Evaluasi|Draf Evaluasi|Draf Realisasi Berita Acara|Draf BAP|BAP Faktual)[^\n]*\n(?:```(?:text|markdown)?\s*)?([\s\S]*?)(?:```|---|###|\Z)/i', $part, $m)) {
                $draf_bap = trim(str_replace(['```text', '```'], '', $m[1]));
            }

            $meetings[$meet_no] = [
                'pertemuan'            => $meet_no,
                'minggu'               => $meet_no,
                'sub_cpmk'             => $sub_cpmk,
                'bahan_kajian'         => $bahan_kajian,
                'bacaan_materi'        => $bahan_kajian,
                'metode'               => $metode,
                'berita_acara_rencana' => $metode,
                'sumber'               => $sumber,
                'evaluasi'             => $evaluasi,
                'indikator_evaluasi'   => $evaluasi,
                'jam_teori'            => $jam_t,
                'jam_praktek'          => $jam_p,
                'skenario_scl'         => $skenario_scl,
                'draf_bap'             => $draf_bap
            ];
        }

        return $meetings;
    }

    public function export_roadmap_excel($id_rps)
    {
        $data_rps = $this->Rps_obe_model->get_by_id($id_rps);
        if (!$data_rps || empty($data_rps->roadmap_modul_json)) {
            show_404('Data Roadmap Modul tidak ditemukan pada RPS ini.');
            return;
        }

        $data = json_decode($data_rps->roadmap_modul_json, true);
        if (!is_array($data) || empty($data['modules'])) {
            show_error('Format data roadmap tidak valid.');
            return;
        }

        $kode_mk = preg_replace('/[^A-Za-z0-9_\-]/', '', $data_rps->kode_mk ?: 'MK');
        $nama_mk = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk ?: 'Roadmap');

        // Panggil generator roadmap yang sudah ada di C_rps_generator
        $this->load->controller = 'C_rps_generator';
        // Reuse export logic with Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Roadmap WBI');
        $sheet->setShowGridLines(true);

        $palette = ['DBEAFE', 'DCFCE7', 'FFEDD5', 'F3E8FF', 'CFFAFE', 'FFE4E6', 'E0E7FF'];
        $color_uts_uas = 'FEF08A';

        $learning_outcome = strtoupper($data['learning_outcome'] ?? ('ROADMAP PEMBELAJARAN ' . str_replace('_', ' ', $nama_mk)));
        $modules = array_values($data['modules']);

        // Normalisasi rentang pertemuan agar modul terakhir otomatis mencakup s.d. pertemuan 16
        if (!empty($modules)) {
            $last_idx = count($modules) - 1;
            if ((int)($modules[$last_idx]['end_meeting'] ?? 0) < 16) {
                $modules[$last_idx]['end_meeting'] = 16;
            }
        }

        $sheet->setCellValue('A1', 'LEARNING OUTCOME');
        $sheet->mergeCells('A1:A2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');

        $sheet->setCellValue('B1', $learning_outcome);
        $sheet->mergeCells('B1:Q2');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B1:Q2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F1F5F9');

        $sheet->setCellValue('R1', str_replace('_', ' ', $nama_mk));
        $sheet->mergeCells('R1:R8');
        $sheet->getStyle('R1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('R1')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('R1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('R1:R8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');

        $row_labels = [
            3 => 'Module',
            4 => 'Output',
            5 => 'Concept',
            6 => 'Indicators',
            7 => 'Learning Strategy',
            8 => 'Meeting'
        ];

        foreach ($row_labels as $r_idx => $lbl) {
            $sheet->setCellValue('A' . $r_idx, strtoupper($lbl));
            $sheet->getStyle('A' . $r_idx)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle('A' . $r_idx)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $r_idx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');
        }

        for ($m_num = 1; $m_num <= 16; $m_num++) {
            $col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($m_num + 1);
            $label_m = $m_num;
            if ($m_num == 8) $label_m = "8 (UTS)";
            if ($m_num == 16) $label_m = "16 (UAS)";

            $sheet->setCellValue($col_letter . '8', $label_m);
            $sheet->getStyle($col_letter . '8')->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($col_letter . '8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col_letter . '8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $mod_color_idx = 0;
        foreach ($modules as $mod) {
            $start_m = max(1, min(16, (int)($mod['start_meeting'] ?? 1)));
            $end_m   = max(1, min(16, (int)($mod['end_meeting'] ?? $start_m)));
            if ($end_m < $start_m) $end_m = $start_m;

            $start_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_m + 1);
            $end_col   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($end_m + 1);

            $is_uts_uas = ($start_m == 8 && $end_m == 8) || ($start_m == 16 && $end_m == 16) || stripos($mod['module_name'] ?? '', 'UTS') !== false || stripos($mod['module_name'] ?? '', 'UAS') !== false;

            $bg_color = $is_uts_uas ? $color_uts_uas : ($palette[$mod_color_idx % count($palette)]);
            if (!$is_uts_uas) $mod_color_idx++;

            $sheet->setCellValue($start_col . '3', $mod['module_name'] ?? 'Modul');
            if ($start_col !== $end_col) $sheet->mergeCells("{$start_col}3:{$end_col}3");
            $sheet->getStyle("{$start_col}3")->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle("{$start_col}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue($start_col . '4', $mod['output'] ?? '');
            if ($start_col !== $end_col) $sheet->mergeCells("{$start_col}4:{$end_col}4");

            $sheet->setCellValue($start_col . '5', $mod['concept'] ?? '');
            if ($start_col !== $end_col) $sheet->mergeCells("{$start_col}5:{$end_col}5");

            $sheet->setCellValue($start_col . '6', $mod['indicators'] ?? '');
            if ($start_col !== $end_col) $sheet->mergeCells("{$start_col}6:{$end_col}6");

            $sheet->setCellValue($start_col . '7', $mod['strategy'] ?? '');
            if ($start_col !== $end_col) $sheet->mergeCells("{$start_col}7:{$end_col}7");

            $range = "{$start_col}3:{$end_col}7";
            $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($bg_color);
            $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            $sheet->getStyle($range)->getAlignment()->setWrapText(true);
        }

        $border_style = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '334155'],
                ],
            ],
        ];
        $sheet->getStyle('A1:R8')->applyFromArray($border_style);

        $filename = "Roadmap_OBE_WBI_{$kode_mk}_{$nama_mk}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function _parse_markdown_table_to_matrix($markdown_text, $sks_mk = 3, $sks_tm = 2, $sks_prak = 1)
    {
        $rows = [];
        if (empty($markdown_text)) return $rows;

        $lines = explode("\n", str_replace("\r", "", $markdown_text));
        $tables = [];
        $current_table = null;
        $in_table = false;
        $found_divider = false;

        foreach ($lines as $line) {
            $trim_line = trim($line);
            if (str_starts_with($trim_line, '|') || str_ends_with($trim_line, '|')) {
                if (!$in_table) {
                    $in_table = true;
                    $found_divider = false;
                    $current_table = [
                        'headers' => [],
                        'rows'    => []
                    ];
                }

                if (preg_match('/^\|?[\s\-:|]+\|?$/', $trim_line) && str_contains($trim_line, '-')) {
                    $found_divider = true;
                    continue;
                }

                $cells = array_map('trim', explode('|', trim($trim_line, '|')));
                if (count($cells) >= 3) {
                    if (!$found_divider) {
                        $current_table['headers'] = $cells;
                    } else {
                        $current_table['rows'][] = $cells;
                    }
                }
            } else {
                if ($in_table) {
                    if (!empty($current_table['rows'])) {
                        $tables[] = $current_table;
                    }
                    $in_table = false;
                    $found_divider = false;
                    $current_table = null;
                }
            }
        }
        if ($in_table && !empty($current_table['rows'])) {
            $tables[] = $current_table;
        }

        if (empty($tables)) return $rows;

        // Score each table to find the weekly meetings table (16 meetings)
        $best_table = null;
        $best_score = -999;

        foreach ($tables as $t) {
            $header_str = strtolower(implode(' ', $t['headers']));
            $score = 0;

            if (preg_match('/minggu|temu|pertemuan/i', $header_str)) $score += 30;
            if (preg_match('/kemampuan akhir|sub.?cpmk|sasaran belajar/i', $header_str)) $score += 25;
            if (preg_match('/bahan kajian|materi|pokok bahasan/i', $header_str)) $score += 20;
            if (preg_match('/bentuk.*modalitas|bentuk.*pembelajaran/i', $header_str)) $score += 15;
            if (preg_match('/pengalaman.*belajar/i', $header_str)) $score += 15;
            if (preg_match('/alokasi.*waktu|waktu/i', $header_str)) $score += 10;
            if (preg_match('/kriteria|indikator|asesmen|evaluasi/i', $header_str)) $score += 10;

            $row_count = count($t['rows']);
            if ($row_count >= 14) $score += 50;
            elseif ($row_count >= 10) $score += 20;
            elseif ($row_count < 10) $score -= 20;

            if (preg_match('/kode cpmk|total bobot cpmk|akumulasi bobot/i', $header_str) && !preg_match('/minggu|pertemuan/i', $header_str)) {
                $score -= 50;
            }

            if ($score > $best_score) {
                $best_score = $score;
                $best_table = $t;
            }
        }

        if (!$best_table || empty($best_table['rows'])) return $rows;

        $header_cells = $best_table['headers'];
        $data_rows = $best_table['rows'];

        // Map column indices from header
        $col_map = [
            'minggu'      => -1,
            'sub_cpmk'    => -1,
            'bahan_kajian'=> -1,
            'bentuk'      => -1,
            'metode'      => -1,
            'pengalaman'  => -1,
            'waktu'       => -1,
            'waktu_t'     => -1,
            'waktu_p'     => -1,
            'evaluasi'    => -1,
            'kriteria'    => -1,
            'indikator'   => -1,
            'bobot'       => -1,
            'sumber'      => -1,
        ];

        foreach ($header_cells as $idx => $h) {
            $h_low = strtolower($h);
            if (preg_match('/minggu|temu|pertemuan/i', $h_low)) $col_map['minggu'] = $idx;
            elseif (preg_match('/sub.?cpmk|kemampuan akhir|sasaran belajar/i', $h_low)) $col_map['sub_cpmk'] = $idx;
            elseif (preg_match('/bahan kajian|materi|pokok bahasan/i', $h_low)) $col_map['bahan_kajian'] = $idx;
            elseif (preg_match('/bentuk.*modalitas|bentuk.*pembelajaran/i', $h_low)) $col_map['bentuk'] = $idx;
            elseif (preg_match('/metode/i', $h_low)) $col_map['metode'] = $idx;
            elseif (preg_match('/pengalaman/i', $h_low)) $col_map['pengalaman'] = $idx;
            elseif (preg_match('/waktu.*t\b|teori/i', $h_low)) $col_map['waktu_t'] = $idx;
            elseif (preg_match('/waktu.*p\b|praktek|praktikum/i', $h_low)) $col_map['waktu_p'] = $idx;
            elseif (preg_match('/alokasi|waktu|durasi/i', $h_low)) $col_map['waktu'] = $idx;
            elseif (preg_match('/evaluasi|penilaian|asesmen/i', $h_low)) $col_map['evaluasi'] = $idx;
            elseif (preg_match('/kriteria/i', $h_low)) $col_map['kriteria'] = $idx;
            elseif (preg_match('/indikator/i', $h_low)) $col_map['indikator'] = $idx;
            elseif (preg_match('/^\s*(?:bobot|persen|%)\b/i', $h_low)) $col_map['bobot'] = $idx;
            elseif (preg_match('/sumber|referensi|pustaka|rujukan/i', $h_low)) $col_map['sumber'] = $idx;
        }

        // Hitung pembagian SKS Teori dan Praktek
        $sks_mk = (int)$sks_mk > 0 ? (int)$sks_mk : 3;
        $sks_tm = (int)$sks_tm;
        $sks_prak = (int)$sks_prak;

        if ($sks_tm > 0 && $sks_prak > 0 && ($sks_tm + $sks_prak) == $sks_mk) {
            $calc_sks_tm = $sks_tm;
            $calc_sks_prak = $sks_prak;
        } elseif ($sks_mk == 3) {
            if ($sks_tm == 1 && $sks_prak == 2) {
                $calc_sks_tm = 1;
                $calc_sks_prak = 2;
            } else {
                $calc_sks_tm = 2;
                $calc_sks_prak = 1;
            }
        } elseif ($sks_mk == 2) {
            $calc_sks_tm = 2;
            $calc_sks_prak = 0;
        } elseif ($sks_mk == 4) {
            $calc_sks_tm = 2;
            $calc_sks_prak = 2;
        } else {
            $calc_sks_tm = $sks_mk;
            $calc_sks_prak = 0;
        }

        foreach ($data_rows as $idx => $cells) {
            $temu_raw = ($col_map['minggu'] >= 0 && isset($cells[$col_map['minggu']])) ? $cells[$col_map['minggu']] : ($cells[0] ?? ($idx + 1));
            $minggu = $idx + 1;
            $pertemuan = $idx + 1;
            if (preg_match('/(\d+)/', $temu_raw, $tm)) {
                $pertemuan = (int)$tm[1];
                $minggu = $pertemuan;
            }

            $sub_cpmk = ($col_map['sub_cpmk'] >= 0 && isset($cells[$col_map['sub_cpmk']])) ? $cells[$col_map['sub_cpmk']] : ($cells[1] ?? '');
            $bahan_kajian = ($col_map['bahan_kajian'] >= 0 && isset($cells[$col_map['bahan_kajian']])) ? $cells[$col_map['bahan_kajian']] : ($cells[2] ?? '');
            
            $bentuk = ($col_map['bentuk'] >= 0 && isset($cells[$col_map['bentuk']])) ? $cells[$col_map['bentuk']] : 'Kuliah';
            $metode = ($col_map['metode'] >= 0 && isset($cells[$col_map['metode']])) ? $cells[$col_map['metode']] : 'Diskusi interaktif';
            
            if ($col_map['bentuk'] < 0 && $col_map['metode'] < 0 && isset($cells[3])) {
                $bentuk = 'Kuliah';
                $metode = $cells[3];
            }

            $pengalaman = ($col_map['pengalaman'] >= 0 && isset($cells[$col_map['pengalaman']])) ? $cells[$col_map['pengalaman']] : ($cells[4] ?? 'Diskusi dan penugasan');

            // Parse Waktu Teori (T) dan Praktek (P) dalam menit
            $waktu_t = $calc_sks_tm * 50;
            $waktu_p = $calc_sks_prak * 50;

            if ($col_map['waktu_t'] >= 0 && isset($cells[$col_map['waktu_t']])) {
                $raw_t = $cells[$col_map['waktu_t']];
                if (preg_match('/(\d+)\s*[xX*]\s*(\d+)/', $raw_t, $m_mult)) {
                    $waktu_t = (int)$m_mult[1] * (int)$m_mult[2];
                } elseif (preg_match('/(\d+)\s*[\/:]\s*(\d+)/', $raw_t, $w_pair)) {
                    $waktu_t = (int)$w_pair[1];
                    $waktu_p = (int)$w_pair[2];
                } elseif (preg_match('/^(\d{1,3})/', $raw_t, $m_num)) {
                    $w_num = (int)$m_num[1];
                    if ($w_num >= 50 && $w_num <= 240) $waktu_t = $w_num;
                }
            }

            if ($col_map['waktu_p'] >= 0 && isset($cells[$col_map['waktu_p']])) {
                $raw_p = $cells[$col_map['waktu_p']];
                if (preg_match('/(\d+)\s*[xX*]\s*(\d+)/', $raw_p, $m_mult)) {
                    $waktu_p = (int)$m_mult[1] * (int)$m_mult[2];
                } elseif (preg_match('/^(\d{1,3})/', $raw_p, $m_num)) {
                    $w_num = (int)$m_num[1];
                    if ($w_num <= 240) $waktu_p = $w_num;
                }
            }

            if ($col_map['waktu'] >= 0 && isset($cells[$col_map['waktu']])) {
                $w_raw = $cells[$col_map['waktu']];
                if (preg_match('/T\s*[:=]?\s*(\d+)/i', $w_raw, $wt)) {
                    $waktu_t = (int)$wt[1];
                }
                if (preg_match('/P\s*[:=]?\s*(\d+)/i', $w_raw, $wp)) {
                    $waktu_p = (int)$wp[1];
                }
                if (preg_match('/(\d+)\s*[\/:]\s*(\d+)/', $w_raw, $w_pair)) {
                    $waktu_t = (int)$w_pair[1];
                    $waktu_p = (int)$w_pair[2];
                }
            }

            // Pastikan total menit per pertemuan sama dengan SKS (misal 3 SKS = 150 menit)
            if (($waktu_t + $waktu_p) != ($sks_mk * 50)) {
                $waktu_t = $calc_sks_tm * 50;
                $waktu_p = $calc_sks_prak * 50;
            }

            // Kriteria & Indikator & Bobot
            $eval_cell = ($col_map['evaluasi'] >= 0 && isset($cells[$col_map['evaluasi']])) ? $cells[$col_map['evaluasi']] : '';
            $kriteria = 'Rubrik Asesmen';
            $indikator = 'Ketepatan pemahaman';
            $bobot = null;

            if ($col_map['kriteria'] >= 0 && isset($cells[$col_map['kriteria']])) {
                $kriteria = trim(strip_tags($cells[$col_map['kriteria']]));
            }
            if ($col_map['indikator'] >= 0 && isset($cells[$col_map['indikator']])) {
                $indikator = trim(strip_tags($cells[$col_map['indikator']]));
            }
            if ($col_map['bobot'] >= 0 && isset($cells[$col_map['bobot']])) {
                if (preg_match('/(\d{1,3})\s*%/i', $cells[$col_map['bobot']], $mb_pure)) {
                    $bobot = (int)$mb_pure[1];
                } else {
                    $b_val = (int)preg_replace('/[^0-9]/', '', $cells[$col_map['bobot']]);
                    if ($b_val > 0 && $b_val <= 100) $bobot = $b_val;
                }
            }

            // Parsing jika evaluasi digabung (Kriteria, Indikator, Bobot dalam 1 cell)
            if (!empty($eval_cell)) {
                if ($bobot === null) {
                    if (preg_match('/(?:bobot|persen|%)[^:\d]*[:\s=]\s*(\d{1,3})\s*%/i', $eval_cell, $mbob)) {
                        $bobot = (int)$mbob[1];
                    } elseif (preg_match('/(\d{1,3})\s*%/i', $eval_cell, $mbob)) {
                        $bobot = (int)$mbob[1];
                    }
                }
                if ($col_map['kriteria'] < 0 && preg_match('/(?:kriteria|rubrik)[^:]*:\s*([^<\n]+)/i', $eval_cell, $mkrit)) {
                    $kriteria = trim(strip_tags($mkrit[1]));
                }
                if ($col_map['indikator'] < 0 && preg_match('/(?:indikator)[^:]*:\s*([^<\n]+)/i', $eval_cell, $mind)) {
                    $indikator = trim(strip_tags($mind[1]));
                }
            }

            // Bersihkan format markdown asterisks
            $kriteria = trim($kriteria, "* \t\n\r\0\x0B");
            $indikator = trim($indikator, "* \t\n\r\0\x0B");

            // Bobot fallback jika tidak terdeteksi
            if ($bobot === null) {
                if ($pertemuan == 8) {
                    $bobot = 25; // Evaluasi Tengah Semester (UTS)
                } elseif ($pertemuan == 16) {
                    $bobot = 25; // Evaluasi Akhir Semester (UAS)
                } elseif ($pertemuan == 7 || $pertemuan == 14) {
                    $bobot = 10; // Tugas Terstruktur / Proyek
                } else {
                    $bobot = 3;  // Formatif Mingguan (Kuis/Partisipasi)
                }
            }

            // Sumber Belajar
            $sumber = ($col_map['sumber'] >= 0 && isset($cells[$col_map['sumber']])) ? $cells[$col_map['sumber']] : (end($cells) ?: '');

            $to_bold = function($str) {
                if (empty($str)) return '';
                $str = preg_replace('/\s+data-[a-z0-9_\-]+="[^"]*"/i', '', $str);
                $str = preg_replace('/\s+data-[a-z0-9_\-]+=\'[^\']*\'/i', '', $str);
                $str = preg_replace('/\s*style="[^"]*"/i', '', $str);
                $str = preg_replace('/<(\/)?strong\b[^>]*>/i', '<$1b>', $str);
                $str = preg_replace('/<(\/)?em\b[^>]*>/i', '<$1i>', $str);
                $str = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<b><i>$1</i></b>', $str);
                $str = preg_replace('/\*\*_(.*?)_\*\*/s', '<b><i>$1</i></b>', $str);
                $str = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $str);
                $str = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<b>$1</b>', $str);
                $str = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $str);
                $str = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $str);
                $str = preg_replace('/<\/?(?:span|font)\b[^>]*>/i', '', $str);
                return trim($str);
            };

            $rows[] = [
                'minggu'              => $minggu,
                'pertemuan'           => $pertemuan,
                'sub_cpmk'            => $to_bold($sub_cpmk),
                'bahan_kajian'        => $to_bold($bahan_kajian),
                'bentuk_pembelajaran' => $to_bold($bentuk),
                'metode'              => $to_bold($metode),
                'pengalaman_belajar'  => $to_bold($pengalaman),
                'waktu_teori'         => $waktu_t,
                'waktu_praktek'       => $waktu_p,
                'kriteria_evaluasi'   => $to_bold($kriteria),
                'indikator_evaluasi'  => $to_bold($indikator),
                'bobot'               => $bobot,
                'sumber_belajar'      => $to_bold($sumber)
            ];
        }

        return $rows;
    }

    /**
     * Resolusi Data Ketua Program Studi (Kaprodi) beserta NIDN dari Database SIAKAD
     */
    private function _resolve_kaprodi($data_rps, $mk_row = null, $nm_lemb = null)
    {
        $kaprodi_data = [
            'nama'    => '',
            'nidn'    => '',
            'nip'     => '',
            'jabatan' => 'Ketua Program Studi',
            'id_ptk'  => ''
        ];

        // 1. Jika data_rps->kaprodi_rps sudah ada id_ptk
        if (!empty($data_rps->kaprodi_rps)) {
            $dosen = $this->db->where('id_ptk', $data_rps->kaprodi_rps)->get('siakad_dosen')->row();
            if ($dosen) {
                $kaprodi_data['nama']   = $dosen->nm_ptk;
                $kaprodi_data['nidn']   = $dosen->nidn;
                $kaprodi_data['nip']    = $dosen->nip;
                $kaprodi_data['id_ptk'] = $dosen->id_ptk;
                return (object)$kaprodi_data;
            }
        }

        // 2. Ambil id_sms dari mk_row atau cari dari feeder_mata_kuliah
        $id_sms = $mk_row->id_sms ?? null;
        if (empty($id_sms) && !empty($data_rps->id_mk)) {
            $mk = $this->db->where('id_mk', $data_rps->id_mk)->get('feeder_mata_kuliah')->row();
            if ($mk) $id_sms = $mk->id_sms;
        }
        if (empty($id_sms) && !empty($data_rps->kode_mk)) {
            $mk = $this->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
            if ($mk) $id_sms = $mk->id_sms;
        }

        // 3. Cari dari tabel `kaprodi` berdasarkan id_sms
        if (!empty($id_sms)) {
            $kap = $this->db->query("
                SELECT k.id_sms, k.nama as prodi, d.id_ptk, d.nm_ptk, d.nidn, d.nip
                FROM kaprodi k
                JOIN siakad_dosen d ON k.id_ptk = d.id_ptk
                WHERE k.id_sms = ?
                LIMIT 1
            ", [$id_sms])->row();

            if ($kap) {
                $kaprodi_data['nama']   = $kap->nm_ptk;
                $kaprodi_data['nidn']   = $kap->nidn;
                $kaprodi_data['nip']    = $kap->nip;
                $kaprodi_data['id_ptk'] = $kap->id_ptk;

                // Simpan sinkronisasi ke siakad_rps_obe
                if (empty($data_rps->kaprodi_rps) && !empty($data_rps->id_rps)) {
                    $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', ['kaprodi_rps' => $kap->id_ptk]);
                    $data_rps->kaprodi_rps = $kap->id_ptk;
                }
                return (object)$kaprodi_data;
            }
        }

        // 4. Cari dari tabel `kaprodi` berdasarkan nama prodi / nm_lemb
        if (!empty($nm_lemb) && $nm_lemb !== 'Politeknik Wilmar Bisnis Indonesia') {
            $kap = $this->db->query("
                SELECT k.id_sms, k.nama as prodi, d.id_ptk, d.nm_ptk, d.nidn, d.nip
                FROM kaprodi k
                JOIN siakad_dosen d ON k.id_ptk = d.id_ptk
                WHERE k.nama LIKE ?
                LIMIT 1
            ", ['%' . $nm_lemb . '%'])->row();

            if ($kap) {
                $kaprodi_data['nama']   = $kap->nm_ptk;
                $kaprodi_data['nidn']   = $kap->nidn;
                $kaprodi_data['nip']    = $kap->nip;
                $kaprodi_data['id_ptk'] = $kap->id_ptk;

                if (empty($data_rps->kaprodi_rps) && !empty($data_rps->id_rps)) {
                    $this->db->where('id_rps', $data_rps->id_rps)->update('siakad_rps_obe', ['kaprodi_rps' => $kap->id_ptk]);
                    $data_rps->kaprodi_rps = $kap->id_ptk;
                }
                return (object)$kaprodi_data;
            }
        }

        // 5. Fallback ke siakad_rps_koordinator jika ada
        if (!empty($id_sms)) {
            $rk = $this->db->query("
                SELECT rk.id_sms, rk.nm_koordinator as nm_ptk, rk.jabatan, d.nidn, d.nip, d.id_ptk
                FROM siakad_rps_koordinator rk
                LEFT JOIN siakad_dosen d ON rk.koordinator_rps = d.id_ptk
                WHERE rk.id_sms = ? AND rk.aktif = 1 AND rk.jabatan LIKE '%Kaprodi%'
                LIMIT 1
            ", [$id_sms])->row();

            if ($rk) {
                $kaprodi_data['nama']    = $rk->nm_ptk;
                $kaprodi_data['nidn']    = $rk->nidn ?: '';
                $kaprodi_data['nip']     = $rk->nip ?: '';
                $kaprodi_data['jabatan'] = $rk->jabatan;
                $kaprodi_data['id_ptk']  = $rk->id_ptk ?: '';
                return (object)$kaprodi_data;
            }
        }

        return (object)$kaprodi_data;
    }
}
