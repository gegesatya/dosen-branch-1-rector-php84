<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_bimbingan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('E_skripsi_bimbingan_model');
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

        $output['judul_besar'] = 'Monitoring Bimbingan TA ';
        $output['judul_kecil'] = 'Academic Health Analytics';
        $output['menu_aktif'] = 'm_proposal';
        $output['parent_skripsi'] = 'active open';
        $output['content'] = 'e_skripsi_bimbingan/e_skripsi_bimbingan_view';
        $output['after_page'] = 'e_skripsi_bimbingan/e_skripsi_bimbingan_after_page';

        $this->db->where('dosen_pembimbing_1', $id_ptk);
        $this->db->or_where('dosen_pembimbing_2', $id_ptk);
        $output['dd_pembimbing'] = $this->db->get('siakad_skripsi_pengajuan_judul')->result();

        $this->load->view('master_page', $output);
    }

    public function ajax_load_cards_html()
    {
        $id_ptk = $this->session->userdata('id_ptk');

        $this->db->where('dosen_pembimbing_1', $id_ptk);
        $this->db->or_where('dosen_pembimbing_2', $id_ptk);
        $dd = $this->db->get('siakad_skripsi_pengajuan_judul')->result();

        $last_recalc = $this->session->userdata('last_recalc_health');
        if (!$last_recalc || (time() - $last_recalc) > 3600) { // 1 jam
            if (!empty($dd)) {
                foreach ($dd as $judul) {
                    $this->_recalculate_academic_health($judul->id_judul_skripsi);
                }
            }
            $this->session->set_userdata('last_recalc_health', time());
        }

        $data_mahasiswa = $this->E_skripsi_bimbingan_model->get_all_bimbingan_dosen($id_ptk);

        $pending_count = 0;
        if (!empty($data_mahasiswa)) {
            foreach ($data_mahasiswa as $m) {
                if ($m->jumlah_pending > 0) {
                    $pending_count++;
                }
            }
        }
        $this->session->set_userdata('bimbingan_pending_count', $pending_count);
        $this->session->set_userdata('bimbingan_last_check', time());

        $data['data_mahasiswa'] = $data_mahasiswa;
        $this->load->view('e_skripsi_bimbingan/e_skripsi_bimbingan_cards_partial', $data);
    }

    private function _check_auth($id_judul_skripsi)
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $data_skripsi = $this->db->select('dosen_pembimbing_1, dosen_pembimbing_2')->where('id_judul_skripsi', $id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row();

        // Jika dosen_pembimbing_1 atau dosen_pembimbing_2 cocok dengan id_ptk, maka lolos
        // Perhatikan bahwa untuk admin / superadmin (id_ptk kosong), jika ingin di-bypass silakan tambah logic
        if (!$data_skripsi || ($data_skripsi->dosen_pembimbing_1 !== $id_ptk && $data_skripsi->dosen_pembimbing_2 !== $id_ptk)) {
            $is_ajax = $this->input->is_ajax_request();
            if ($is_ajax) {
                echo json_encode(['status' => false, 'msg' => 'Akses Ditolak. Anda bukan Dosen Pembimbing untuk skripsi/TA ini.']);
                exit;
            } else {
                $output['judul_besar'] = 'Akses Ditolak';
                $output['judul_kecil'] = 'Error 403 Forbidden';
                $output['menu_aktif'] = '';
                $output['error_message'] = 'Anda tidak memiliki izin untuk mengakses riwayat bimbingan mahasiswa ini. Halaman ini diproteksi dan hanya dapat dibuka oleh <strong>Dosen Pembimbing (Utama / Pendamping)</strong> yang telah ditugaskan secara resmi.';
                $output['content'] = 'review_laporan/error_403';
                $this->load->view('master_page', $output);
                $this->output->_display();
                exit;
            }
        }
    }

    public function detail($id_judul_skripsi)
    {
        $this->_check_auth($id_judul_skripsi);

        $id_ptk = $this->session->userdata('id_ptk');

        $data_skripsi = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row();

        if (!$data_skripsi) {
            show_404();
        }

        $nipd = $data_skripsi->nipd;
        $mahasiswa = $this->db->query("SELECT nm_pd, foto FROM mahasiswa m JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd WHERE mp.nipd = ?", [$nipd])->row();
        $nm_pd = $mahasiswa ? $mahasiswa->nm_pd : 'Unknown';
        $foto_mhs = ($mahasiswa && !empty($mahasiswa->foto)) ? $mahasiswa->foto : null;

        $doping_1 = $this->db->where('id_ptk', $data_skripsi->dosen_pembimbing_1)->get('siakad_skripsi_dosen');
        $nm_doping_1 = $doping_1->num_rows() > 0 ? $doping_1->row()->nama_lengkap : '-';

        $doping_2 = $this->db->where('id_ptk', $data_skripsi->dosen_pembimbing_2)->get('siakad_skripsi_dosen');
        $nm_doping_2 = $doping_2->num_rows() > 0 ? $doping_2->row()->nama_lengkap : '-';

        $logs = $this->E_skripsi_bimbingan_model->get_bimbingan_logs($id_judul_skripsi);

        $has_chunk_table = $this->db->table_exists('siakad_skripsi_chunk_files');
        foreach ($logs as $log) {
            $log->has_chunks = $has_chunk_table ? ($this->db->where('id_log', $log->id_log)->count_all_results('siakad_skripsi_chunk_files') > 0) : false;
            if ($log->has_chunks) {
                $total_chunks = $this->db->where('id_log', $log->id_log)->count_all_results('siakad_skripsi_chunk_files');
                $reviewed_chunks = $this->db->where('id_log', $log->id_log)->where('ai_review_result IS NOT NULL', null, false)->count_all_results('siakad_skripsi_chunk_files');
                $log->is_all_reviewed = ($total_chunks > 0 && $total_chunks == $reviewed_chunks);
            } else {
                $log->is_all_reviewed = false;
            }
        }

        // Fetch Dosen Wali / Pembimbing Akademik
        $dosen_pa = $this->db->query("
            SELECT d.nama_lengkap 
            FROM mahasiswa m 
            JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd 
            LEFT JOIN siakad_skripsi_dosen d ON m.dosen_pa = d.id_ptk 
            WHERE mp.nipd = ?
        ", [$nipd])->row();
        $nm_dosen_wali = $dosen_pa ? $dosen_pa->nama_lengkap : '-';

        // Fetch latest KRS
        $krs_terakhir = $this->db->query("
            SELECT k.*, s.nm_status_krs 
            FROM siakad_krs k 
            LEFT JOIN siakad_status_krs s ON k.id_status_krs = s.id_status_krs 
            WHERE k.nipd = ? 
            ORDER BY k.id_smt DESC 
            LIMIT 1
        ", [$nipd])->row();

        $krs_courses = [];
        if ($krs_terakhir) {
            $krs_courses = $this->db->query("
                SELECT kd.id_kls, kk.nm_kls, mk.kode_mk, mk.nm_mk, mk.sks_mk 
                FROM siakad_krs_detail kd 
                JOIN feeder_kelas_kuliah kk ON kd.id_kls = kk.id_kls 
                JOIN feeder_mata_kuliah mk ON kk.id_mk = mk.id_mk
                WHERE kd.id_rencana_studi = ?
            ", [$krs_terakhir->id_rencana_studi])->result();
        }

        // Calculate Title Age (Umur Judul)
        $umur_judul = '-';
        if ($data_skripsi && !empty($data_skripsi->tanggal_disetujui)) {
            $diff = time() - strtotime($data_skripsi->tanggal_disetujui);
            $days = floor($diff / (60 * 60 * 24));
            if ($days < 0) {
                $umur_judul = "Baru disetujui";
            } else {
                $years = floor($days / 365);
                $rem_days = $days % 365;
                $months = floor($rem_days / 30);
                $remaining_days = $rem_days % 30;

                $parts = [];
                if ($years > 0)
                    $parts[] = $years . " Tahun";
                if ($months > 0)
                    $parts[] = $months . " Bulan";
                if (empty($parts) || $remaining_days > 0)
                    $parts[] = $remaining_days . " Hari";

                $umur_judul = implode(' ', $parts);
            }
        }

        // Fetch Seminar History
        $seminar_history = $this->db->query("
            SELECT kategori_pengajuan, tanggal_seminar 
            FROM siakad_skripsi_seminar_sidang 
            WHERE id_judul_skripsi = ? 
            ORDER BY kategori_pengajuan ASC
        ", [$id_judul_skripsi])->result();

        $riwayat_seminar = [];
        $tgl_disetujui = !empty($data_skripsi->tanggal_disetujui) ? strtotime($data_skripsi->tanggal_disetujui) : null;
        $tgl_sempro = null;
        $tgl_semhas = null;
        $tgl_sidang = null;

        foreach ($seminar_history as $sh) {
            if ($sh->kategori_pengajuan == 1 && !empty($sh->tanggal_seminar)) {
                $tgl_sempro = strtotime($sh->tanggal_seminar);
            } elseif ($sh->kategori_pengajuan == 2 && !empty($sh->tanggal_seminar)) {
                $tgl_semhas = strtotime($sh->tanggal_seminar);
            } elseif ($sh->kategori_pengajuan == 3 && !empty($sh->tanggal_seminar)) {
                $tgl_sidang = strtotime($sh->tanggal_seminar);
            }
        }

        $calc_duration = function ($start, $end) {
            if (!$start)
                return null;
            $is_ongoing = false;
            if (!$end) {
                $end = time();
                $is_ongoing = true;
            }
            if ($end < $start)
                return null;

            $days = floor(($end - $start) / (60 * 60 * 24));

            // Flag as abnormal if the process was completed (not ongoing) in less than 21 days
            $is_abnormal = (!$is_ongoing && $days < 21) ? true : false;

            if ($days == 0)
                $res = '0 Hari';
            else {
                $years = floor($days / 365);
                $rem_days = $days % 365;
                $months = floor($rem_days / 30);
                $remaining_days = $rem_days % 30;

                $parts = [];
                if ($years > 0)
                    $parts[] = $years . " thn";
                if ($months > 0)
                    $parts[] = $months . " bln";
                if (empty($parts) || $remaining_days > 0)
                    $parts[] = $remaining_days . " hr";

                $res = implode(' ', $parts);
            }
            return ['text' => $res, 'ongoing' => $is_ongoing, 'abnormal' => $is_abnormal, 'days' => $days];
        };

        $dur_sempro = $calc_duration($tgl_disetujui, $tgl_sempro);
        $riwayat_seminar['sempro'] = [
            'tanggal' => $tgl_sempro ? date('Y-m-d', $tgl_sempro) : null,
            'hasil' => !empty($data_skripsi->hasil_sempro) && $data_skripsi->hasil_sempro != 'belum_ada' ? $data_skripsi->hasil_sempro : null,
            'durasi' => $dur_sempro ? $dur_sempro['text'] : '-',
            'ongoing' => $dur_sempro ? $dur_sempro['ongoing'] : false,
            'abnormal' => $dur_sempro ? $dur_sempro['abnormal'] : false,
            'label_durasi' => 'Dari Penetapan Judul'
        ];

        $dur_semhas = $calc_duration($tgl_sempro, $tgl_semhas);
        $riwayat_seminar['semhas'] = [
            'tanggal' => $tgl_semhas ? date('Y-m-d', $tgl_semhas) : null,
            'hasil' => !empty($data_skripsi->hasil_semhas) && $data_skripsi->hasil_semhas != 'belum_ada' ? $data_skripsi->hasil_semhas : null,
            'durasi' => $dur_semhas ? $dur_semhas['text'] : '-',
            'ongoing' => $dur_semhas ? $dur_semhas['ongoing'] : false,
            'abnormal' => $dur_semhas ? $dur_semhas['abnormal'] : false,
            'label_durasi' => 'Dari Sempro'
        ];

        $dur_sidang = $calc_duration($tgl_semhas, $tgl_sidang);
        $riwayat_seminar['sidang'] = [
            'tanggal' => $tgl_sidang ? date('Y-m-d', $tgl_sidang) : null,
            'hasil' => null,
            'durasi' => $dur_sidang ? $dur_sidang['text'] : '-',
            'ongoing' => $dur_sidang ? $dur_sidang['ongoing'] : false,
            'abnormal' => $dur_sidang ? $dur_sidang['abnormal'] : false,
            'label_durasi' => 'Dari Semhas'
        ];

        $output['riwayat_seminar'] = $riwayat_seminar;

        $output['judul_besar'] = 'Aktifitas Bimbingan';
        $output['judul_kecil'] = 'Bimbingan TA - ' . $nm_pd;
        $output['menu_aktif'] = 'm_proposal';
        $output['parent_skripsi'] = 'active open';

        $output['data_skripsi'] = $data_skripsi;
        $output['nm_pd'] = $nm_pd;
        $output['foto_mhs'] = $foto_mhs;
        $output['nm_doping_1'] = $nm_doping_1;
        $output['nm_doping_2'] = $nm_doping_2;
        $output['nm_dosen_wali'] = $nm_dosen_wali;
        $output['logs'] = $logs;
        $output['krs_terakhir'] = $krs_terakhir;
        $output['krs_courses'] = $krs_courses;
        $output['umur_judul'] = $umur_judul;

        $output['petunjuk_dosen'] = [
            'title' => 'Petunjuk Bimbingan Dosen',
            'desc' => 'Mahasiswa memiliki 4 tahapan proses bimbingan yang akan dibimbing, yaitu:',
            'steps' => [
                'Proposal',
                'Naskah (bab 1 sd bab 3)',
                'Naskah (bab 3 sd bab 6)',
                'Finalisasi pasca sidang'
            ],
            'action_tip' => 'Mohon agar Dosen berfokus pada progres yang dikirim oleh student.'
        ];

        $id_ptk = $this->session->userdata('id_ptk');
        $output['is_utama'] = ($id_ptk == $data_skripsi->dosen_pembimbing_1);
        $output['is_pendamping'] = ($id_ptk == $data_skripsi->dosen_pembimbing_2);

        $this->load->model('Setting_api_key_model');
        $output['has_api_key'] = !empty($this->Setting_api_key_model->get_api_key($id_ptk));

        // Cek apakah mahasiswa sudah LULUS sidang
        $nilai_sidang = $this->db->query("
            SELECT sidang_hasil 
            FROM siakad_skripsi_nilai_sempro 
            WHERE id_judul_skripsi = ? 
            AND UPPER(TRIM(sidang_hasil)) = 'LULUS'
            LIMIT 1
        ", [$id_judul_skripsi])->row();
        $output['is_lulus'] = !empty($nilai_sidang);


        // Create array of new system messages to filter out duplicates in the old archive
        $new_system_messages = [];
        foreach ($logs as $l) {
            if (!empty($l->catatan_mahasiswa))
                $new_system_messages[] = preg_replace('/\s+/', '', strip_tags($l->catatan_mahasiswa));
            if (!empty($l->catatan_dosen))
                $new_system_messages[] = preg_replace('/\s+/', '', strip_tags($l->catatan_dosen));
            if (!empty($l->catatan_pendamping))
                $new_system_messages[] = preg_replace('/\s+/', '', strip_tags($l->catatan_pendamping));
        }

        // Fetch Old Bimbingan Log (siakad_skripsi_kartu_bimbingan_proposal)
        $old_bimbingan = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->get('siakad_skripsi_kartu_bimbingan_proposal')->row();

        $old_bimbingan_formatted_html = '';
        if (!empty($old_bimbingan) && !empty($old_bimbingan->html_respon)) {
            // Regex to extract all panels from html_respon
            preg_match_all('/<div class="panel panel-default">.*?<div class="panel-heading">.*?<a[^>]*>(.*?)<\/a>.*?<span[^>]*>(.*?)<\/span>.*?<\/div>.*?<div class="panel-body">(.*?)<\/div>.*?<\/div>/is', $old_bimbingan->html_respon, $matches, PREG_SET_ORDER);

            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $sender = trim(strip_tags($match[1]));
                    $time = trim(strip_tags($match[2]));
                    $message = trim($match[3]);

                    // Check if this message is already in the new system logs
                    $message_clean = preg_replace('/\s+/', '', strip_tags($message));
                    $is_duplicate = array_any($new_system_messages, fn($new_msg) => !empty($message_clean) && !empty($new_msg) && (str_contains($message_clean, $new_msg) || str_contains($new_msg, $message_clean)));

                    if ($is_duplicate) {
                        continue; // Skip this message, it's from the new system
                    }

                    // Check if sender is student or lecturer
                    $is_student = (strcasecmp($sender, $nm_pd) === 0 || stripos($sender, (string) $nm_pd) !== false || stripos($nm_pd, $sender) !== false);

                    // Flatten existing <a href="...">...</a> tags to raw URLs first to avoid double wrapping/nested links
                    $message = preg_replace('/<a\s[^>]*href=["\'](https?:\/\/[^"\']+)["\'][^>]*>.*?<\/a>/is', '$1', $message);

                    // Convert text URLs to interactive preview buttons calling bukaGdrive
                    $message = preg_replace_callback(
                        '/\b(https?:\/\/[^\s<>"\']+)/i',
                        function ($url_match) {
                            $url = $url_match[1];
                            $display_text = "Preview Dokumen";
                            $icon = "fa-file-text-o";

                            // Check if the URL is a syntactically valid URL
                            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                                return '<span class="badge" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin: 4px 0;"><i class="fa fa-chain-broken"></i> Tautan Rusak (Broken Link)</span>';
                            }

                            // For Google Drive/Docs links, check for minimum path length to ensure it is not a broken partial link
                            if (stripos($url, 'docs.google.com') !== false || stripos($url, 'drive.google.com') !== false) {
                                $path = parse_url($url, PHP_URL_PATH);
                                if (empty($path) || strlen($path) < 10) {
                                    return '<span class="badge" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin: 4px 0;"><i class="fa fa-chain-broken"></i> Tautan Rusak (Broken Link)</span>';
                                }

                                $display_text = "Preview Google Drive";
                                $icon = "fa-google";
                                return '<a href="javascript:void(0)" onclick="bukaGdrive(\'' . $url . '\')" class="btn btn-xs btn-primary font-white" style="margin: 4px 0; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; border-radius: 6px; background: #2563EB; border: none; padding: 4px 8px; color: #ffffff !important;"><i class="fa ' . $icon . '"></i> ' . $display_text . '</a>';
                            } else {
                                return '<a href="javascript:void(0)" onclick="bukaGdrive(\'' . $url . '\')" class="btn btn-xs btn-default" style="margin: 4px 0; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; border-radius: 6px; padding: 4px 8px;"><i class="fa ' . $icon . '"></i> ' . $display_text . '</a>';
                            }
                        },
                        $message
                    );

                    // Bubble styling
                    $bubble_align = $is_student ? 'flex-start' : 'flex-end';
                    $bubble_bg = $is_student ? '#E0F2FE' : '#F3F4F6'; // Light blue for student, light gray for Dosen
                    $bubble_text_color = $is_student ? '#0369A1' : '#374151';
                    $bubble_border = $is_student ? '#BAE6FD' : '#E5E7EB';
                    $bubble_margin = $is_student ? 'margin-right: 50px;' : 'margin-left: 50px;';
                    $sender_color = $is_student ? '#0284C7' : '#4B5563';

                    $old_bimbingan_formatted_html .= '
                    <div style="display: flex; flex-direction: column; align-items: ' . $bubble_align . '; margin-bottom: 16px; width: 100%;">
                        <div style="font-size: 11px; font-weight: 700; color: #6B7280; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                            <span style="color: ' . $sender_color . ';">' . $sender . '</span>
                            <span style="font-weight: 500; color: #9CA3AF;">• ' . $time . '</span>
                        </div>
                        <div style="background: ' . $bubble_bg . '; border: 1px solid ' . $bubble_border . '; color: #1F2937; border-radius: 12px; padding: 12px 16px; font-size: 13px; line-height: 1.6; font-weight: 500; ' . $bubble_margin . ' box-shadow: 0 1px 3px rgba(0,0,0,0.02); position: relative; max-width: 85%; word-break: break-word;">
                            ' . $message . '
                        </div>
                    </div>';
                }
            } else {
                // Fallback: If parsing fails, output original HTML inside styled container
                $old_bimbingan_formatted_html = '<div class="raw-old-bimbingan">' . $old_bimbingan->html_respon . '</div>';
            }
        }

        $output['old_bimbingan'] = $old_bimbingan;
        $output['old_bimbingan_formatted_html'] = $old_bimbingan_formatted_html;

        $output['content'] = 'e_skripsi_bimbingan/e_skripsi_bimbingan_detail_view';
        $output['after_page'] = 'e_skripsi_bimbingan/e_skripsi_bimbingan_detail_after_page';

        $this->load->view('master_page', $output);
    }

    public function sbs_review($id_log)
    {
        $log = $this->db->where('id_log', $id_log)->get('siakad_skripsi_bimbingan_log')->row();
        if (!$log) show_404();
        
        $this->_check_auth($log->id_judul_skripsi);

        // Parse Google Drive link for iframe preview
        $link_preview = $log->file_lampiran_mahasiswa;
        if (stripos($link_preview, 'drive.google.com') !== false || stripos($link_preview, 'docs.google.com') !== false) {
            $link_preview = preg_replace('/\/view\b.*$/i', '/preview', $link_preview);
            $link_preview = preg_replace('/\/edit\b.*$/i', '/preview', $link_preview);
        }

        // Ambil data mahasiswa
        $mhs = $this->db->query("
            SELECT b.nm_pd, b.foto, a.nipd 
            FROM siakad_skripsi_pengajuan_judul a 
            JOIN mahasiswa_pt m ON m.nipd = a.nipd 
            JOIN mahasiswa b ON b.id_pd = m.id_pd 
            WHERE a.id_judul_skripsi = ?
        ", [$log->id_judul_skripsi])->row();

        $data = [
            'log' => $log,
            'id_judul_skripsi' => $log->id_judul_skripsi,
            'link_preview' => $link_preview,
            'mhs_nama' => $mhs ? $mhs->nm_pd : 'Mahasiswa',
            'mhs_nipd' => $mhs ? $mhs->nipd : '',
            'mhs_foto' => ($mhs && $mhs->foto) ? 'https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . rawurlencode($mhs->foto) : base_url('assets/no_profile_photo.png')
        ];

        $this->load->view('e_skripsi_bimbingan/e_skripsi_bimbingan_sbs_view', $data);
    }

    public function ajax_refresh_cards()
    {
        $id_ptk = $this->session->userdata('id_ptk');

        $this->db->where('dosen_pembimbing_1', $id_ptk);
        $this->db->or_where('dosen_pembimbing_2', $id_ptk);
        $dd = $this->db->get('siakad_skripsi_pengajuan_judul')->result();
        if (!empty($dd)) {
            foreach ($dd as $judul) {
                $this->_recalculate_academic_health($judul->id_judul_skripsi);
            }
        }

        $data_mahasiswa = $this->E_skripsi_bimbingan_model->get_all_bimbingan_dosen($id_ptk);

        $cards = [];
        foreach ($data_mahasiswa as $m) {
            $is_unstarted = ($m->total_bimbingan == 0 && $m->kategori_health != 'Risiko Tinggi');
            $cards[] = [
                'id_judul_skripsi' => $m->id_judul_skripsi,
                'nm_pd' => $m->nm_pd,
                'nipd' => $m->nipd,
                'jumlah_pending' => (int) $m->jumlah_pending,
                'tgl_pending' => $m->tgl_pending,
                'total_bimbingan' => (int) $m->total_bimbingan,
                'current_progress' => (int) $m->current_progress,
                'kategori_health' => $m->kategori_health,
                'academic_health_score' => (float) $m->academic_health_score,
                'last_bimbingan_date' => $m->last_bimbingan_date,
                'is_unstarted' => $is_unstarted,
                'tanggal_disetujui' => $m->tanggal_disetujui ?? null,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($cards);
        exit;
    }

    public function ajax_list()
    {

        $list = $this->E_skripsi_bimbingan_model->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $rg) {
            $no++;

            if ($rg->status_sk == 1 and !empty($rg->nomor_sk)) {

                $row = [];
                $id_judul_skripsi = $rg->id_judul_skripsi;
                $row[] = $no;
                $row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;

                // Build Academic Health HTML
                $kategori_color = 'success';
                if ($rg->kategori_health == 'Perlu Perhatian')
                    $kategori_color = 'warning';
                if ($rg->kategori_health == 'Risiko Tinggi')
                    $kategori_color = 'danger';

                $html_health = '
                <div style="margin-bottom:10px;">
                    <span class="badge badge-' . $kategori_color . '">' . $rg->kategori_health . '</span>
                    <strong style="margin-left:10px;">Score: ' . round($rg->academic_health_score) . '</strong>
                </div>
                <div class="progress" style="height: 15px; margin-bottom:5px;">
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' . $rg->current_progress . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $rg->current_progress . '%; line-height: 15px;">
                    <span style="color:#fff; font-size:10px;">' . $rg->current_progress . '% Complete</span>
                  </div>
                </div>
                <small class="text-muted">Last Update: ' . ($rg->last_bimbingan_date ? tgl_indo_short($rg->last_bimbingan_date) : '-') . '</small>';

                $html_theme = '<div class="general-item-list" style="height: 200px; overflow-y: auto;">' . $this->convertURLs($rg->html_respon) . '</div>';


                if ($rg->status_approve == 0 and $rg->dosen_pembimbing == $this->session->userdata('id_ptk')) {
                    $html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
											onclick="balas_pesan(' . "'" . $id_judul_skripsi . "'" . ')">
											<i class="fa fa-mail-reply"></i> <small>Bimbingan</small></a>
											<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
											onclick="get_info(' . "'" . $id_judul_skripsi . "'" . ')">
											<i class="fa fa-info"></i> <small>Info TA</small></a>
											<a href="' . site_url('E_skripsi_bimbingan/cetak_pdf/') . md5($id_judul_skripsi) . '" class="btn btn-default btn-xs green-stripe" target="_new">
											<i class="fa fa-file-pdf-o"></i> <small>Download</small></a>
											<a href="javascript:void(0)" class="btn btn-default btn-xs red-stripe"
											onclick="approve_kegiatan(' . "'" . $id_judul_skripsi . "'" . ')">
											<i class="fa fa-list-ol"></i> <small>Rekap By Mahasiswa</small></a>';
                } else {
                    $html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
											onclick="balas_pesan(' . "'" . $id_judul_skripsi . "'" . ')">
											<i class="fa fa-mail-reply"></i> <small>Bimbingan</small></a>
											<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
											onclick="get_info(' . "'" . $id_judul_skripsi . "'" . ')">
											<i class="fa fa-info"></i> <small>Info TA</small></a>
											<a href="' . site_url('E_skripsi_bimbingan/cetak_pdf/') . md5($id_judul_skripsi) . '" class="btn btn-default btn-xs green-stripe" target="_new">
											<i class="fa fa-file-pdf-o"></i> <small>Download</small></a>';
                }

                $row[] = $html_health;

                if (!empty($rg->html_respon)) {
                    $row[] = $html_theme . '<br>' . $html_komen;
                } else {
                    $row[] = $html_komen;
                }

                $data[] = $row;
            } else {
                $row = [];

                $row[] = $no;
                $row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;
                $row[] = '-';
                $row[] = 'Status SK Pembimbing belum aktif, silahkan hubungi Bagian Akademik untuk periksa kembali Nomor SK dan Tanggal SK';

                $data[] = $row;
            }
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->E_skripsi_bimbingan_model->count_all(),
            "recordsFiltered" => $this->E_skripsi_bimbingan_model->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }


    public function ajax_list_backup()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $list = $this->E_skripsi_bimbingan_model->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $rg) {
            $no++;

            if ($rg->status_sk == 1 and !empty($rg->nomor_sk)) {
                $sudah_boleh_maju_prodi = $rg->sudah_boleh_maju_prodi;
                $approval_sempro_pembimbing_satu = $rg->approval_sempro_pembimbing_satu;
                $approval_sempro_pembimbing_dua = $rg->approval_sempro_pembimbing_dua;

                $a = $b = $c = '';

                if ($approval_sempro_pembimbing_satu == 1) {
                    $tgl_approval_sempro_p1 = date('Y-m-d', strtotime($rg->tgl_approval_sempro_p1));
                    $a = '<br><small><i class="fa fa-bell-o text-success"></i> ' . tgl_indo_short($tgl_approval_sempro_p1) . ': Approval Seminar Proposal Dosen Pembimbing Utama.</small>';
                }

                if ($approval_sempro_pembimbing_dua == 1) {
                    $tgl_approval_sempro_p2 = date('Y-m-d', strtotime($rg->tgl_approval_sempro_p2));
                    $b = '<br><small><i class="fa fa-bell-o text-success"></i> ' . tgl_indo_short($tgl_approval_sempro_p2) . ':  Approval Seminar Proposal Dosen Pembimbing Pendamping.</small>';
                }

                if ($sudah_boleh_maju_prodi == 1) {
                    $tgl_maju_sempro_prodi = date('Y-m-d', strtotime($rg->tgl_maju_sempro_prodi));
                    $c = '<br><small><i class="fa fa-bell-o text-success"></i>  ' . tgl_indo_short($tgl_maju_sempro_prodi) . ': Sudah boleh mengajukan Seminar Proposal dan melengkapi dokumen persyaratan.</small>';
                }

                $html_jadwal = $html_cek_data = $html_surat_tugas = $html_undangan = $html_hasil_sempro = $html_hasil_semhas = '';
                if (!empty($rg->tanggal_sempro) || $rg->tanggal_sempro == '0000-00-00') {
                    if (!empty($rg->jam_sempro)) {
                        $jam = " | " . substr($rg->jam_sempro, 0, 5);
                    } else {
                        $jam = '';
                    }
                    $html_jadwal = '<br><i class="fa fa-calendar font-green"></i> <small>Jadwal Seminar Proposal : ' . tgl_indo($rg->tanggal_sempro) . $jam . '</small>';
                }

                if ($rg->terbit_surat_tugas == 1) {
                    $html_surat_tugas = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Surat Tugas Penguji Seminar Proposal sudah diterbitkan </small><a href="' . site_url('E_skripsi_notifikasi/cetak_surat_tugas') . '/' . md5($rg->id_judul_skripsi) . '" class="btn btn-xs btn-info" target="_new"> <i class="fa fa-download"></i></a>';
                }

                $html_cek_data = $rg->sudah_cek_data_sempro == 0 ? '<i class="fa fa-times font-red"></i> <small>Dokumen persyaratan Seminar Proposal belum diperiksa</small>' : '<i class="fa fa-check-square-o info font-green"></i><small>Dokumen persyaratan SEMPRO sudah di validasi</small>';


                if ($rg->undangan_status == 1) {
                    $html_undangan = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Surat Undangan Seminar Proposal sudah diterbitkan </small><a href="' . site_url('E_skripsi_notifikasi/cetak_undangan') . '/' . md5($rg->id_judul_skripsi) . '" class="btn btn-xs btn-info" target="_new"> <i class="fa fa-download"></i></a>';
                }

                if ($rg->hasil_sempro <> 'belum_ada') {
                    if ($rg->hasil_sempro == 'layak') {
                        $html_hasil_sempro = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Hasil Seminar Proposal: Layak untuk dilanjutkan</small>';
                    } elseif ($rg->hasil_sempro == 'tidak_layak') {
                        $html_hasil_sempro = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Hasil Seminar Proposal: Tidak Layak </small>';
                    }
                }

                if ($rg->hasil_semhas <> 'belum_ada') {
                    if ($rg->hasil_semhas == 'layak') {
                        $html_hasil_semhas = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Hasil Seminar Hasil: Layak untuk dilanjutkan</small>';
                    } elseif ($rg->hasil_semhas == 'tidak_layak') {
                        $html_hasil_semhas = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Hasil Seminar Hasil: Tidak Layak </small>';
                    }
                }

                $row = [];

                $row[] = $no;
                $row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;

                $html_theme = '<div class="general-item-list" style="height: 200px; overflow-y: auto;">' . $this->convertURLs($rg->html_respon) . '</div>';
                if ($rg->hasil_sempro == 'layak') {
                    /* <a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
                                    onclick="get_semhas(' . "'" . $rg->id_judul_skripsi . "'" . ')">
                                    <i class="fa fa-mail-reply"></i> <small>Persetujuan Semhas</small></a> */
                    $html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="balas_pesan(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-mail-reply"></i> <small>Catat Kegiatan</small></a>
									<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="get_info(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-info"></i> <small>Info</small></a>
									<a href="' . site_url('E_skripsi_bimbingan/cetak_pdf/') . md5($rg->id_judul_skripsi) . '" class="btn btn-default btn-xs green-stripe" target="_new"
									onclick="">
									<i class="fa fa-file-pdf-o"></i> <small>Download</small></a>
									';
                } else {
                    $html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="balas_pesan(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-mail-reply"></i> <small>Catat Kegiatan</small></a>
									<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="get_info(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-info"></i> <small>Info</small></a>
									<a href="' . site_url('E_skripsi_bimbingan/cetak_pdf/') . md5($rg->id_judul_skripsi) . '" class="btn btn-default btn-xs green-stripe" target="_new"
									onclick="">
									<i class="fa fa-file-pdf-o"></i> <small>Download</small></a>
									<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="get_sempro(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-mail-reply"></i> <small>Persetujuan Sempro</small></a>';
                }


                if (!empty($rg->html_respon)) {
                    $row[] = $html_theme . '<br>' . $html_komen;
                } else {
                    $row[] = $html_komen;
                }
                $row[] = $a . $b . $c . $html_jadwal . $html_surat_tugas . $html_undangan . $html_hasil_sempro . $html_hasil_semhas;

                $data[] = $row;
            } else {
                $row = [];

                $row[] = $no;
                $row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;

                $row[] = 'Status SK Pembimbing belum aktif, silahkan hubungi Bagian Akademik untuk periksa kembali Nomor SK dan Tanggal SK';
                $row[] = '';
                $row[] = '';


                $data[] = $row;
            }
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->E_skripsi_bimbingan_model->count_all(),
            "recordsFiltered" => $this->E_skripsi_bimbingan_model->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }


    public function ajax_edit($id)
    {
        $data = $this->E_skripsi_bimbingan_model->get_by_id_2($id);

        $id_ptk = $this->session->userdata('id_ptk');
        if ($id_ptk == $data->dosen_pembimbing_1) {
            $data->is_doping_1 = TRUE;
        } else {
            $data->is_doping_1 = FALSE;
        }

        if ($id_ptk == $data->dosen_pembimbing_2) {
            $data->is_doping_2 = TRUE;
        } else {
            $data->is_doping_2 = FALSE;
        }

        echo json_encode($data);
    }

    public function ajax_edit_bimbingan($id)
    {
        $data = $this->E_skripsi_bimbingan_model->get_by_id($id);
        $data->jam_bimbingan = substr($data->jam_bimbingan, 0, 5);
        echo json_encode($data);
    }

    public function ajax_balas_pesan()
    {
        $user = $this->ion_auth->user()->row();
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');

        $id_log_bimbingan = $this->input->post('id_log_bimbingan');
        $id_ptk = $this->session->userdata('id_ptk');

        $skripsi_data = $this->db->get_where('siakad_skripsi_pengajuan_judul', ['id_judul_skripsi' => $id_judul_skripsi])->row();

        $is_pendamping = false;
        $is_utama = false;

        if ($skripsi_data->dosen_pembimbing_1 == $id_ptk)
            $is_utama = true;
        if ($skripsi_data->dosen_pembimbing_2 == $id_ptk)
            $is_pendamping = true;

        $progress_persen = $this->input->post('progress_persen') ?: 0;
        $status_bimbingan = $this->input->post('status_bimbingan') ?: 'Menunggu Review';

        if (!empty($this->input->post('catatan_dosen_wali')) && $id_log_bimbingan) {
            $tgl = date('Y-m-d');
            $jam = date('H:i');

            if ($is_pendamping && !$is_utama) {
                // Update log untuk Pendamping
                $log_data = [
                    'catatan_pendamping' => $this->input->post('catatan_dosen_wali'),
                    'status_pendamping' => $status_bimbingan, // Reuse the same dropdown for status, but it means status_pendamping
                    'reviewed_at_pendamping' => date('Y-m-d H:i:s'),
                    'reviewed_by_pendamping' => $user->id,
                    'id_ptk_pendamping' => $id_ptk
                ];

                if ($status_bimbingan == 'Perlu Revisi') {
                    $log_data['status_bimbingan'] = 'Perlu Revisi';
                } elseif ($status_bimbingan == 'Lanjut ke Utama') {
                    $log_data['status_bimbingan'] = 'Menunggu Review';
                }
            } else {
                // Update log untuk Utama
                $log_data = [
                    'catatan_dosen' => $this->input->post('catatan_dosen_wali'),
                    'progress_persen' => $progress_persen,
                    'status_bimbingan' => $status_bimbingan,
                    'created_at' => date('Y-m-d H:i:s'), // Update created_at for dosen reply time
                    'created_by' => $user->id,
                    'id_ptk_utama' => $id_ptk
                ];

                if ($status_bimbingan == 'Konsultasi ke Pendamping') {
                    $log_data['status_pendamping'] = 'Konsultasi ke Pendamping';
                }
            }
            $this->E_skripsi_bimbingan_model->update_bimbingan_log($id_log_bimbingan, $log_data);

            // AUTO-CLOSE AGENDA DOSEN JIKA UTAMA MENYETUJUI BIMBINGAN
            if ($is_utama && $status_bimbingan == 'Disetujui') {
                $active_agenda = $this->E_skripsi_bimbingan_model->get_agenda_aktif($id_judul_skripsi);
                if ($active_agenda) {
                    $this->E_skripsi_bimbingan_model->close_agenda($active_agenda->id_log, $user->id);
                }
            }

            // Legacy bimbingan table save removed to prevent mixing with old archive

            $this->_recalculate_academic_health($id_judul_skripsi);

            // Clear menu badge session cache to force reload
            $this->session->unset_userdata('bimbingan_last_check');

            echo json_encode(["status" => TRUE]);
        }
    }

    public function ajax_generate_ai_review()
    {
        $id_log = $this->input->post('id_log');
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');

        // 1. Fetch Log
        $log = $this->db->get_where('siakad_skripsi_bimbingan_log', ['id_log' => $id_log, 'id_judul_skripsi' => $id_judul_skripsi])->row();

        if (!$log) {
            echo json_encode(['status' => false, 'msg' => 'Log bimbingan tidak ditemukan.']);
            return;
        }

        if (empty($log->file_lampiran_mahasiswa)) {
            echo json_encode(['status' => false, 'msg' => 'Mahasiswa tidak melampirkan file dokumen.']);
            return;
        }

        // Cek apakah sudah ada hasil review
        if (!empty($log->ai_review_result)) {
            echo json_encode(['status' => true, 'msg' => 'Review sudah ada.', 'review' => $log->ai_review_result]);
            return;
        }

        // 2. Tentukan Path / URL File
        $file_url = $log->file_lampiran_mahasiswa;
        $temp_file = sys_get_temp_dir() . '/' . uniqid('skripsi_') . '.tmp';

        // Ekstrak ID dari Google Drive URL jika memungkinkan
        $is_gdoc = false;
        if (str_contains($file_url, 'docs.google.com/document/d/')) {
            $is_gdoc = true;
        }

        if (str_contains($file_url, 'drive.google.com') || str_contains($file_url, 'docs.google.com')) {
            preg_match('/[-\w]{25,}/', $file_url, $matches);
            if (!empty($matches[0])) {
                $file_id = $matches[0];
                if ($is_gdoc) {
                    $file_url = 'https://docs.google.com/document/d/' . $file_id . '/export?format=docx';
                } else {
                    $file_url = 'https://drive.google.com/uc?export=download&id=' . $file_id;
                }
            }
        }

        // Cek apakah sedang berjalan di local server
        $is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || str_contains($_SERVER['HTTP_HOST'], '.test') || str_contains($_SERVER['HTTP_HOST'], '.local'));

        // Download file sementara (Jika public) menggunakan cURL agar bisa follow redirect
        $ch_dl = curl_init();
        curl_setopt($ch_dl, CURLOPT_URL, $file_url);
        curl_setopt($ch_dl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_dl, CURLOPT_FOLLOWLOCATION, 1);
        if ($is_local) {
            curl_setopt($ch_dl, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch_dl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        $file_content = curl_exec($ch_dl);
        $dl_http_code = curl_getinfo($ch_dl, CURLINFO_HTTP_CODE);
        curl_close($ch_dl);

        if (!$file_content || $dl_http_code != 200) {
            echo json_encode(['status' => false, 'msg' => 'Gagal mengunduh file dokumen (HTTP ' . $dl_http_code . '). URL: ' . $file_url . ' - Pastikan link tidak salah dan setelan share adalah "Anyone with the link".']);
            return;
        }

        file_put_contents($temp_file, $file_content);

        if (str_contains(trim(strtolower(substr($file_content, 0, 50))), '<!doctype html') || str_contains(trim(strtolower(substr($file_content, 0, 50))), '<html')) {
            @unlink($temp_file);
            echo json_encode(['status' => false, 'msg' => 'Gagal membaca dokumen. Tautan Google Drive ini dilindungi, membutuhkan konfirmasi, atau mengarah ke halaman web (HTML). Pastikan file tersebut adalah dokumen asli dan sudah di-set "Anyone with the link".']);
            return;
        }

        // 3. Ekstrak Teks
        $extracted_text = '';
        try {
            // Kita coba deteksi apakah PDF atau Word dari magic bytes atau asumsi error parsing
            // Coba PDF parser dulu
            $parser = new \Smalot\PdfParser\Parser();
            try {
                $pdf = $parser->parseFile($temp_file);
                $extracted_text = $pdf->getText();
            } catch (\Exception $e) {
                // Mungkin bukan PDF, coba ekstrak murni sebagai DOCX ZIP Archive
                try {
                    $zip = new ZipArchive;
                    if ($zip->open($temp_file) === true) {
                        if (($index = $zip->locateName('word/document.xml')) !== false) {
                            $data = $zip->getFromIndex($index);
                            $zip->close();

                            $data = str_replace(['</w:p>', '<w:br>', '<w:br/>', '<w:br />'], "\n", $data);
                            $data = str_replace(['<w:tab>', '<w:tab/>', '<w:tab />'], "\t", $data);
                            $extracted_text = strip_tags($data);
                        } else {
                            $zip->close();
                            throw new \Exception("File bukan dokumen Word (.docx) yang valid.");
                        }
                    } else {
                        throw new \Exception("Gagal membuka file ZIP/DOCX.");
                    }
                } catch (\Exception $e2) {
                    @unlink($temp_file);
                    echo json_encode(['status' => false, 'msg' => 'Gagal membaca isi dokumen: ' . $e2->getMessage()]);
                    return;
                }
            }
        } catch (\Exception $e) {
            @unlink($temp_file);
            echo json_encode(['status' => false, 'msg' => 'Gagal memproses file: ' . $e->getMessage()]);
            return;
        }

        @unlink($temp_file);

        if (empty(trim($extracted_text))) {
            echo json_encode(['status' => false, 'msg' => 'Teks di dalam dokumen kosong atau tidak bisa dibaca (berupa gambar hasil scan).']);
            return;
        }

        // Batasi teks agar tidak melebihi payload API terlalu besar (Ambil 15000 karakter pertama)
        $extracted_text = substr($extracted_text, 0, 15000);

        // 4. Kirim ke API AI Provider
        $id_ptk = $this->session->userdata('id_ptk');
        $this->load->model('Setting_api_key_model');
        $api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);

        if (empty($api_setting) || empty($api_setting->api_key)) {
            echo json_encode(['status' => false, 'msg' => 'API Key belum disetting. Silakan setting API Key di menu Setting API Key terlebih dahulu.']);
            return;
        }

        $api_key = $api_setting->api_key;
        $ai_provider = isset($api_setting->ai_provider) && !empty($api_setting->ai_provider) ? $api_setting->ai_provider : 'gemini';
        $ai_model = isset($api_setting->ai_model) && !empty($api_setting->ai_model) ? $api_setting->ai_model : 'gemini-1.5-flash';

        $skripsi = $this->db->select('a.judul_ditetapkan, b.nm_lemb')
            ->from('siakad_skripsi_pengajuan_judul a')
            ->join('sms b', 'a.id_sms = b.id_sms', 'left')
            ->where('a.id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        $judul_ta = $skripsi ? $skripsi->judul_ditetapkan : "Tidak diketahui";
        $prodi = $skripsi ? $skripsi->nm_lemb : "Tidak diketahui";

        // ------------------------------------------------------------------
        // Deteksi peran dosen yang sedang login (Utama vs. Pendamping)
        // ------------------------------------------------------------------
        $id_ptk_login = $this->session->userdata('id_ptk');
        $skripsi_full = $this->db->select('dosen_pembimbing_1, dosen_pembimbing_2')
            ->from('siakad_skripsi_pengajuan_judul')
            ->where('id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        $is_pembimbing_utama = ($skripsi_full && $skripsi_full->dosen_pembimbing_1 == $id_ptk_login);
        $is_pembimbing_pendamping = ($skripsi_full && $skripsi_full->dosen_pembimbing_2 == $id_ptk_login);

        // Cek apakah prodi ini hanya memiliki 1 dosen pembimbing (dosen_pembimbing_2 kosong)
        $hanya_satu_dosen = ($skripsi_full && (empty($skripsi_full->dosen_pembimbing_2) || $skripsi_full->dosen_pembimbing_2 == '-'));

        if ($is_pembimbing_utama && !$is_pembimbing_pendamping && !$hanya_satu_dosen) {
            $nama_peran = "Dosen Pembimbing Utama";
            $is_akuntansi_perpajakan = (str_contains(strtolower($prodi), 'akuntansi perpajakan'));
            
            if (!$is_akuntansi_perpajakan) {
                $pendamping_sudah_review = !empty($log->catatan_pendamping);
                
                if ($pendamping_sudah_review) {
                    $catatan_pendamping_bersih = strip_tags(str_replace(['<br>', '</p>'], ' ', $log->catatan_pendamping));
                    $instruksi_peran = "Dosen Pendamping telah mereview bagian tata tulis dengan catatan: '{$catatan_pendamping_bersih}'. Tolong BACA catatan tersebut agar Anda TIDAK MENGULANGI temuan yang sama. Fokuslah 100% pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. ABAIKAN masalah tata bahasa (typo, ejaan).";
                } else {
                    $instruksi_peran = "PRIORITASKAN evaluasi pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. ABAIKAN kesalahan tata bahasa minor (seperti typo, ejaan). Porsi koreksi tata bahasa harus dikurangi seminimal mungkin dan diserahkan kepada Dosen Pendamping.";
                }
            } else {
                $instruksi_peran = "PRIORITASKAN evaluasi pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. Evaluasi tata tulis juga dilakukan sebagai pelengkap, namun substansi adalah penilaian utama kamu.";
            }
        } elseif ($is_pembimbing_pendamping && !$is_pembimbing_utama) {
            $nama_peran = "Dosen Pembimbing Pendamping";
            $instruksi_peran = "PRIORITASKAN evaluasi pada tata tulis, ejaan (EYD), pemformatan dokumen (font, margin, penomoran), penggunaan Bahasa Indonesia baku, dan kesesuaian dengan template penulisan TA Politeknik WBI. Pastikan arahan substantif dari Pembimbing Utama sudah tercermin dalam isi dokumen.";
        } else {
            $nama_peran = "Dosen Pembimbing (Utama & Pendamping)";
            $instruksi_peran = "Berikan penilaian komprehensif, mencakup substansi penelitian sekaligus tata tulis, pemformatan, dan kesesuaian template.";
        }

        // ------------------------------------------------------------------
        // Konteks laporan aktivitas mahasiswa pada sesi ini
        // ------------------------------------------------------------------
        $konteks_mahasiswa = "";
        if (!empty($log->catatan_mahasiswa)) {
            $catatan_bersih = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $log->catatan_mahasiswa));
            $catatan_bersih = trim(preg_replace('/\n{3,}/', "\n\n", $catatan_bersih));
            $konteks_mahasiswa = "\n\n## Laporan Aktivitas Mahasiswa pada Sesi Ini\n" . $catatan_bersih;
        }

        // ------------------------------------------------------------------
        // Fokus pemeriksaan berdasarkan tahapan
        // ------------------------------------------------------------------
        $tahapan = strtolower((string)$log->tahapan);
        $jenis_dokumen = "Dokumen Bimbingan";
        $fokus_pemeriksaan = "Lakukan pemeriksaan standar pada struktur dan isi naskah akademik.";

        if (str_contains($tahapan, 'proposal')) {
            $jenis_dokumen = "Proposal Tugas Akhir";
            $fokus_pemeriksaan = "Pastikan dokumen adalah Proposal TA yang lengkap: mencakup Latar Belakang, Rumusan Masalah, Tujuan, Kontribusi, Tinjauan Pustaka, dan Metodologi sesuai template pedoman WBI. Verifikasi bahwa BELUM ada bab hasil penelitian atau kesimpulan akhir — jika ada, beri TEGURAN KRITIS. Periksa apakah kerangka berpikir dan diagram alur metodologi sudah logis.";
        } elseif (str_contains($tahapan, 'bab 1 s.d. bab 3')) {
            $jenis_dokumen = "Naskah Tugas Akhir (Fase Awal: Bab 1–3)";
            $fokus_pemeriksaan = "Buatkan intisari singkat per bab dalam bentuk poin (contoh: 'Bab 1 – Pendahuluan: berisi latar belakang ...'). Evaluasi keterkaitan logis antara Bab 1 (latar belakang/tujuan), Bab 2 (teori), dan Bab 3 (metodologi). Pastikan penulisan sudah beralih ke format Naskah TA, bukan lagi gaya Proposal. Soroti ketidakkonsistenan atau loncat logika antar bab.";
        } elseif (str_contains($tahapan, 'bab 4 s.d. bab 6')) {
            $jenis_dokumen = "Naskah Tugas Akhir (Fase Akhir: Bab 4–6)";
            $fokus_pemeriksaan = "Buatkan intisari singkat per bab (hasil, pengujian, kesimpulan). Evaluasi secara tajam: apakah metodologi Bab 3 benar-benar diterapkan di Bab 4? Apakah kesimpulan di Bab 6 menjawab rumusan masalah di Bab 1? Tinjau kualitas analisis data, keabsahan interpretasi hasil, serta kelengkapan saran. Evaluasi tata bahasa dan EYD tetap dilakukan.";
        } elseif (str_contains($tahapan, 'finalisasi') || str_contains($tahapan, 'revisi')) {
            $jenis_dokumen = "Naskah Tugas Akhir Final/Revisi";
            $fokus_pemeriksaan = "Lakukan pengecekan menyeluruh dan komprehensif dari Bab 1 hingga Bab 6: konsistensi antar bab, kelengkapan daftar pustaka, kesesuaian daftar isi dengan isi dokumen, dan ketelitian typo/EYD. Buatkan intisari singkat keseluruhan naskah, lalu fokus berikan catatan teknis penulisan yang harus diselesaikan sebelum sidang.";
        }

        // ------------------------------------------------------------------
        // Konstruksi Prompt Utama
        // ------------------------------------------------------------------
        // Jika pendamping: gunakan prompt yang lebih spesifik (tata tulis, sitasi, antiplagiasi)
        if ($is_pembimbing_pendamping && !$is_pembimbing_utama) {
            $prompt = "Kamu adalah **{$nama_peran}** di Politeknik WBI. Tugasmu adalah **Proof Reading** naskah TA — BUKAN menilai substansi penelitian.

**Informasi TA:** {$prodi} | {$judul_ta} | {$log->tahapan}

**ABAIKAN:** kode Mendeley/Zotero (ADDIN CSL_CITATION), masalah spasi PDF, hilangnya italics, kesalahan format rumus matematika. Itu artefak sistem, bukan kesalahan mahasiswa.

---

## Instruksi Evaluasi (3 Aspek)

**1. Tata Tulis & Ejaan**
- Kumpulkan SEMUA typo/ejaan salah dalam satu daftar bullet ringkas, format: *[kata salah]* → [seharusnya] (keterangan singkat).
- Periksa konsistensi istilah dan penomoran sub-bab.
- Kaidah kata ulang PUEBI: 'Faktor-Faktor' di awal kalimat/judul, 'faktor-faktor' di tengah kalimat.

**2. Sitasi & Daftar Pustaka**
- Tandai klaim/paragraf tanpa sitasi.
- Cek konsistensi format sitasi (APA?).
- Cek kesesuaian sumber di teks vs Daftar Pustaka.

**3. Risiko Plagiasi**
- Deteksi blok teks copy-paste tanpa parafrase/sitasi.
- Berikan penilaian: Rendah / Sedang / Tinggi.

---

## Format Output WAJIB

### 📋 Ringkasan Dokumen
*2–3 kalimat: isi dokumen dan kesan kualitas tulisan.*

### 🔍 Temuan Evaluasi

**Bahasa & Tata Tulis**
- 🔴/🟡/🟢 [typo/ejaan] (Sub-bab: X) — *[teks asli]* → Saran: [koreksi]
- (kumpulkan semua typo di sini, satu baris per temuan, WAJIB sebut sub-bab)

**Struktur & Formatting**
- 🔴/🟡/🟢 [temuan] (Sub-bab: X) → Saran: [...]

**Sitasi & Daftar Pustaka**
- 🔴/🟡/🟢 [temuan] (Sub-bab: X) → Saran: [...]
- Jika tidak ada masalah: *Tidak ditemukan masalah sitasi signifikan.*

**Risiko Plagiasi**
- 🔴/🟡/🟢 Tingkat Risiko: Rendah/Sedang/Tinggi — [alasan singkat, bagian yang perlu diperhatikan]

### ✅ Kesimpulan & Catatan untuk Mahasiswa
*Poin singkat, maks 5. JANGAN narasi panjang.*
1. ...

---

**Aturan:** Langsung ke Ringkasan Dokumen tanpa basa-basi. Gunakan Markdown. Jaga output ringkas dan padat. Jangan ulangi temuan yang sama.
**PENTING untuk Saran:** Dalam memberikan saran, JANGAN menyebutkan atau mengulangi daftar isi, sub-bab, atau konten dokumen secara panjang. Cukup berikan arahan koreksi singkat (maks 1–2 contoh saja, bukan daftar lengkap).
**PENTING untuk Temuan:** JANGAN menampilkan temuan typo/ejaan yang PERSIS SAMA lebih dari 2 kali. Jika kesalahan yang sama (misal: penulisan kata 'Faktor-Faktor') muncul berulang kali, cukup tuliskan satu kali saja dan tambahkan keterangan '(ditemukan berulang kali di seluruh dokumen)'.
**PENTING untuk Lokasi:** WAJIB sebutkan di sub-bab mana temuan tersebut berada pada setiap poin evaluasi dengan format (Sub-bab: X).";
        } else {
            $prompt = "# Konteks & Peran

Kamu adalah **{$nama_peran}** di Politeknik WBI yang sedang melakukan review digital terhadap draf Tugas Akhir mahasiswa. Sikapmu kritis namun konstruktif — setiap temuan harus disertai saran perbaikan yang konkret dan actionable.

**Panduan Peranmu:** {$instruksi_peran}

---

## Informasi Tugas Akhir

| Atribut | Nilai |
|---|---|
| Program Studi | {$prodi} |
| Judul Tugas Akhir | {$judul_ta} |
| Tahapan Bimbingan | {$log->tahapan} |
| Jenis Dokumen | {$jenis_dokumen} |
{$konteks_mahasiswa}

---

## Instruksi Utama: Fokus Evaluasi Tahapan Ini

{$fokus_pemeriksaan}

---

## Sinkronisasi dengan Laporan Mahasiswa

Lakukan sinkronisasi (cross-check) antara isi naskah yang dikirimkan dengan klaim mahasiswa pada bagian \"Laporan Aktivitas Mahasiswa pada Sesi Ini\" (jika ada).
- Jika mahasiswa dengan jelas menyatakan bagian yang belum selesai (contoh: \"baru mengirim Bab 1 saja\"), sesuaikan ekspektasi evaluasimu dan fokuslah pada bagian tersebut tanpa menganggap hilangnya bab lain sebagai kesalahan kritis.
- Sebaliknya, jika mahasiswa menyatakan mengirimkan dokumen lengkap namun isinya terpotong atau tidak sesuai klaim, berikan peringatan 🔴 **KRITIS**.

---

## Instruksi Evaluasi Umum

Selain fokus utama di atas, evaluasi juga dimensi berikut secara proporsional:

1. **Tata Bahasa, EYD & Typo** — Deteksi ejaan keliru, tanda baca tidak tepat, dan kata tidak baku. Kutip kalimat asli, lalu berikan koreksinya.
2. **Kesinambungan Logika** — Cek koherensi antar paragraf, konsistensi variabel/istilah, dan alur argumen penelitian.
3. **Struktur & Formatting** — Evaluasi kelengkapan struktur dokumen (daftar isi, judul bab, penomoran, margin, font) sesuai jenis dokumen yang diharapkan.
4. **Substansi Penelitian** — Kedalaman analisis, validitas metodologi, relevansi tinjauan pustaka, dan ketercapaian tujuan penelitian.

---

## Sistem Penandaan Tingkat Keparahan

Tandai setiap temuan dengan label berikut:
- 🔴 **KRITIS** — Harus diperbaiki sebelum dapat lanjut ke tahapan berikutnya.
- 🟡 **PERHATIAN** — Perlu diperbaiki, namun proses bimbingan masih dapat dilanjutkan.
- 🟢 **SARAN** — Rekomendasi peningkatan kualitas, tidak wajib namun dianjurkan.

---

## Klausul Dokumen Tidak Relevan

Jika dokumen yang diberikan **jelas bukan** draf Tugas Akhir atau Proposal mahasiswa (misalnya: RPP, makalah mata kuliah lain, slip gaji, dokumen pribadi, atau file yang isinya tidak terbaca), **HENTIKAN analisis** dan sampaikan pesan berikut saja:

> ⚠️ **Dokumen Tidak Sesuai:** Dokumen yang diunggah bukan merupakan naskah/proposal Tugas Akhir. Mohon pastikan mahasiswa mengunggah file dokumen yang benar sesuai tahapan bimbingan.

---

## Format Output Wajib

Gunakan PERSIS struktur berikut — jangan tambah bagian lain di luar ini:

### 📋 Ringkasan Dokumen
*Deskripsikan isi dokumen yang kamu baca dalam 2–4 kalimat singkat. Sebutkan bab atau bagian apa saja yang ada.*

### 🔍 Temuan Evaluasi

**Bahasa & Tata Tulis**
- 🔴/🟡/🟢 [Temuan] — *[Kutip kalimat bermasalah]* → Saran: [koreksi/saran]

**Struktur & Formatting**
- 🔴/🟡/🟢 [Temuan] → Saran: [koreksi/saran]

**Substansi & Logika Penelitian**
- 🔴/🟡/🟢 [Temuan] → Saran: [koreksi/saran]

### ✅ Kesimpulan untuk Catatan Dosen ke Mahasiswa:
*Wajib berupa poin-poin terkelompok. JANGAN gunakan narasi paragraf panjang.*

Dari segi penulisan & tata tulis:
1. ...
2. ...

Dari segi substansi & struktur penelitian:
1. ...
2. ...

*(Sesuaikan kelompok dengan temuan aktual. Hapus kelompok yang tidak relevan.)*

---

## Aturan Tambahan

- JANGAN berikan salam pembuka atau narasi basa-basi. Langsung masuk ke **Ringkasan Dokumen**.
- Gunakan format **Markdown** agar mudah dibaca.
- JANGAN gunakan format LaTeX/Math (seperti \$\\rightarrow\$ atau \$\$). Gunakan tanda biasa seperti -> untuk panah.
- Berikan ulasan yang MENDETAIL dan KOMPREHENSIF namun tetap padat. Hindari pengulangan.

---";
        }

        $this->load->library('Ai_reviewer');

        $system_prompt = $prompt . "\n\nAnda adalah AI Reviewer Akademik. Berikan balasan dalam format Markdown sesuai instruksi. Jaga output tetap ringkas dan padat.";
        $user_document = "Berikut adalah isi dokumen yang harus direview:\n\n" . $extracted_text;

        $result = $this->ai_reviewer->generate_review($ai_provider, $api_key, $system_prompt, $user_document, $is_local, false);

        if (!$result['status']) {
            echo json_encode(['status' => false, 'msg' => $result['msg']]);
            return;
        }

        $ai_review = $result['review'];

        if (!empty($ai_review)) {

            // Simpan ke DB (sesuai peran dosen)
            $update_field = ($is_pembimbing_utama) ? 'ai_review_utama' : 'ai_review_pendamping';

            $this->db->where('id_log', $id_log);
            $this->db->update('siakad_skripsi_bimbingan_log', [$update_field => $ai_review]);

            echo json_encode(['status' => true, 'msg' => 'Review AI berhasil digenerate.', 'review' => $ai_review]);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Respon AI tidak sesuai format.']);
        }
    }

    /**
     * Hapus (reset) hasil AI review pada satu log bimbingan,
     * sehingga dosen dapat meng-generate ulang dengan prompt yang berbeda
     * (misalnya setelah delegasi ke Dosen Pendamping).
     */
    public function ajax_reset_ai_review()
    {
        $id_log = (int) $this->input->post('id_log');
        $id_judul_skripsi = (int) $this->input->post('id_judul_skripsi');

        if (!$id_log || !$id_judul_skripsi) {
            echo json_encode(['status' => false, 'msg' => 'Parameter tidak valid.']);
            return;
        }

        // Pastikan log benar-benar milik skripsi ini (mencegah manipulasi ID)
        $log = $this->db->get_where('siakad_skripsi_bimbingan_log', [
            'id_log' => $id_log,
            'id_judul_skripsi' => $id_judul_skripsi,
        ])->row();

        if (!$log) {
            echo json_encode(['status' => false, 'msg' => 'Log bimbingan tidak ditemukan.']);
            return;
        }

        // Pastikan dosen yang login memang pembimbing dari skripsi ini
        $id_ptk_login = $this->session->userdata('id_ptk');
        $skripsi = $this->db->select('dosen_pembimbing_1, dosen_pembimbing_2')
            ->from('siakad_skripsi_pengajuan_judul')
            ->where('id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        $is_utama = ($skripsi && $skripsi->dosen_pembimbing_1 == $id_ptk_login);
        $is_pendamping = ($skripsi && $skripsi->dosen_pembimbing_2 == $id_ptk_login);

        if (!$is_utama && !$is_pendamping) {
            echo json_encode(['status' => false, 'msg' => 'Anda tidak memiliki hak akses untuk menghapus review ini.']);
            return;
        }

        // Reset field sesuai peran
        $update_field = ($is_utama) ? 'ai_review_utama' : 'ai_review_pendamping';

        $this->db->where('id_log', $id_log);
        $this->db->update('siakad_skripsi_bimbingan_log', [$update_field => null]);

        echo json_encode(['status' => true, 'msg' => 'AI Review berhasil direset.']);
    }

    public function ajax_get_chat($id_judul_skripsi)
    {
        if (empty($id_judul_skripsi)) {
            echo json_encode(['status' => FALSE, 'msg' => 'ID tidak valid.']);
            return;
        }

        $this->db->select('c.*, u.first_name, u.last_name');
        $this->db->from('siakad_skripsi_chat c');
        $this->db->join('users u', 'c.pengirim_id = u.id', 'left');
        $this->db->where('c.id_judul_skripsi', $id_judul_skripsi);
        $this->db->order_by('c.created_at', 'ASC');
        $chats = $this->db->get()->result();

        // Mark as read if the sender was mahasiswa
        $this->db->where('id_judul_skripsi', $id_judul_skripsi)
            ->where('pengirim_type', 'mahasiswa')
            ->where('is_read', 0)
            ->update('siakad_skripsi_chat', ['is_read' => 1]);

        echo json_encode(['status' => TRUE, 'data' => $chats]);
    }

    public function ajax_get_unread_chat($id_judul_skripsi)
    {
        if (empty($id_judul_skripsi)) {
            echo json_encode(['status' => FALSE, 'count' => 0]);
            return;
        }

        $count = $this->db->where('id_judul_skripsi', $id_judul_skripsi)
            ->where('pengirim_type', 'mahasiswa')
            ->where('is_read', 0)
            ->count_all_results('siakad_skripsi_chat');

        echo json_encode(['status' => TRUE, 'count' => $count]);
    }

    public function ajax_send_chat()
    {
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');
        $pesan = $this->input->post('pesan');

        if (empty($pesan)) {
            echo json_encode(['status' => FALSE, 'msg' => 'Pesan tidak boleh kosong']);
            return;
        }

        $user = $this->ion_auth->user()->row();

        $data = [
            'id_judul_skripsi' => $id_judul_skripsi,
            'pengirim_type' => 'dosen',
            'pengirim_id' => $user->id,
            'pesan' => $pesan,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => 0
        ];

        $data = $this->security->xss_clean($data);
        $this->db->insert('siakad_skripsi_chat', $data);

        echo json_encode(['status' => TRUE]);
    }

    public function ajax_save_draft()
    {
        $user = $this->ion_auth->user()->row();
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');
        $lembar_monitoring = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_kartu_bimbingan_proposal');


        if (!empty($this->input->post('catatan_dosen_wali'))) {
            $catatan = str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('catatan_dosen_wali'));

            $data = [
                'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
                'draft_bimbingan' => $catatan,
                'tipe_monitoring' => 1,
            ];

            $this->E_skripsi_bimbingan_model->save_draft($data, $lembar_monitoring, $id_judul_skripsi);
            echo json_encode(["status" => TRUE]);
        }
    }

    public function view_pesan($id_judul_skripsi)
    {
        $data = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_kartu_bimbingan_proposal')->row();

        $data_judul = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

        $nipd = $data_judul->nipd;
        $mahasiswa = $this->db->query("SELECT nm_pd FROM mahasiswa m JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd WHERE mp.nipd = ?", [$nipd])->row();
        $nm_pd = $mahasiswa ? $mahasiswa->nm_pd : 'Unknown';

        $judul_ditetapkan = '<div class="alert alert-info">
							<p>' . $data_judul->judul_ditetapkan . '</p></div>';

        $log_message = $draft_bimbingan = '';
        if (!empty($data->html_respon)) {
            $log_message = $data->html_respon;
        }

        if (!empty($data->draft_bimbingan)) {
            $draft_bimbingan = $data->draft_bimbingan;
        }

        echo json_encode([
            'data_pesan' => $log_message,
            'data_draft_bimbingan' => $draft_bimbingan,
            'nm_pd' => $nm_pd,
            'judul' => $judul_ditetapkan
        ]);
    }

    public function view_info($id_judul_skripsi)
    {
        $data = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

        $nipd = $data->nipd;
        $mhs = $this->db->where('nipd', $nipd)->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
        $nm_pd = !empty($mhs->nm_pd) ? $mhs->nm_pd : '';
        $judul_ditetapkan = $data->judul_ditetapkan;
        $target_selesai = $data->target_selesai;
        $doping_1 = $this->db->where('id_ptk', $data->dosen_pembimbing_1)->get('siakad_skripsi_dosen');
        $doping_2 = $this->db->where('id_ptk', $data->dosen_pembimbing_2)->get('siakad_skripsi_dosen');
        $nm_doping_1 = $nm_doping_2 = '';

        if ($doping_1->num_rows() > 0) {
            $nm_doping_1 = $doping_1->row()->nama_lengkap;
        }

        if ($doping_2->num_rows() > 0) {
            $nm_doping_2 = $doping_2->row()->nama_lengkap;
        }

        echo json_encode([
            'nipd' => $nipd,
            'nm_pd' => $nm_pd,
            'judul_ditetapkan' => $judul_ditetapkan,
            'target_selesai' => $target_selesai,
            'nm_doping_1' => $nm_doping_1,
            'nm_doping_2' => $nm_doping_2,
            'no_handphone' => $data->no_handphone,
            'timeline_data' => $data
        ]);
    }

    public function ajax_save_sempro()
    {
        $judul_lama = $this->input->post('judul_lama');
        $judul_baru = $this->input->post('judul_ditetapkan');

        $tgl_approval_sempro_p1 = $tgl_approval_sempro_p2 = $tgl_maju_sempro_prodi = NULL;

        if ($this->input->post('approval_sempro_pembimbing_satu') == true) {
            if ($this->input->post('is_approval_p1') == 0) {
                $tgl_approval_sempro_p1 = date('Y-m-d H:i:s');
            } else {
                $tgl_approval_sempro_p1 = $this->input->post('tgl_approval_sempro_p1');
            }
        }

        if ($this->input->post('approval_sempro_pembimbing_dua') == true) {
            if ($this->input->post('is_approval_p2') == 0) {
                $tgl_approval_sempro_p2 = date('Y-m-d H:i:s');
            } else {
                $tgl_approval_sempro_p2 = $this->input->post('tgl_approval_sempro_p2');
            }
        }

        if ($this->input->post('sudah_boleh_maju_prodi') == true) {
            if ($this->input->post('is_boleh_maju_prodi') == 0) {
                $tgl_maju_sempro_prodi = date('Y-m-d H:i:s');
            } else {
                $tgl_maju_sempro_prodi = $this->input->post('tgl_maju_sempro_prodi');
            }
        }

        if ($this->input->post('status_revisi_judul') == true) {

            $data = [
                'sudah_boleh_maju_prodi' => $this->input->post('sudah_boleh_maju_prodi') == true ? 1 : 0,
                'approval_sempro_pembimbing_satu' => $this->input->post('approval_sempro_pembimbing_satu') == true ? 1 : 0,
                'approval_sempro_pembimbing_dua' => $this->input->post('approval_sempro_pembimbing_dua') == true ? 1 : 0,
                'tgl_approval_sempro_p1' => $tgl_approval_sempro_p1,
                'tgl_approval_sempro_p2' => $tgl_approval_sempro_p2,
                'tgl_maju_sempro_prodi' => $tgl_maju_sempro_prodi,
                'judul_ditetapkan' => $this->input->post('judul_ditetapkan_2'),
            ];

            $user = $this->ion_auth->user()->row();

            $this->db->insert('siakad_skripsi_judul_lama', [
                'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
                'judul_lama' => $judul_lama,
                'create_by' => $user->first_name,
                'create_date' => date('Y-m-d H:i:s')
            ]);
        } else {
            $data = [
                'status_revisi_judul' => $this->input->post('status_revisi_judul') == true ? 1 : 0,
                'sudah_boleh_maju_prodi' => $this->input->post('sudah_boleh_maju_prodi') == true ? 1 : 0,
                'approval_sempro_pembimbing_satu' => $this->input->post('approval_sempro_pembimbing_satu') == true ? 1 : 0,
                'approval_sempro_pembimbing_dua' => $this->input->post('approval_sempro_pembimbing_dua') == true ? 1 : 0,
                'tgl_approval_sempro_p1' => $tgl_approval_sempro_p1,
                'tgl_approval_sempro_p2' => $tgl_approval_sempro_p2,
                'tgl_maju_sempro_prodi' => $tgl_maju_sempro_prodi
            ];
        }

        $this->E_skripsi_bimbingan_model->update_sempro(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data, $judul_lama);
        echo json_encode(["status" => TRUE]);
    }

    public function ajax_approve_kegiatan()
    {
        $this->_validate_kegiatan();
        $user = $this->ion_auth->user()->row();
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');

        $tgl = $this->input->post('tanggal_bimbingan');
        $jam = substr($this->input->post('jam_bimbingan'), 0, 5);
        $tl_mahasiswa = $this->input->post('tulisan_mahasiswa');
        $progress_persen = $this->input->post('progress_persen') ?: 0;
        $status_bimbingan = $this->input->post('status_bimbingan') ?: 'Menunggu Review';

        // Cek log terakhir untuk mewarisi status pendamping jika sedang direvisi oleh pendamping
        $last_log = $this->db->order_by('id_log', 'DESC')->get_where('siakad_skripsi_bimbingan_log', ['id_judul_skripsi' => $id_judul_skripsi])->row();
        $status_pendamping = null;
        if ($last_log && ($last_log->status_pendamping == 'Perlu Revisi' || $last_log->status_pendamping == 'Konsultasi ke Pendamping')) {
            $status_pendamping = 'Konsultasi ke Pendamping';
            if ($status_bimbingan == 'Menunggu Review' || empty($this->input->post('status_bimbingan'))) {
                $status_bimbingan = 'Konsultasi ke Pendamping';
            }
        }

        $log_data = [
            'id_judul_skripsi' => $id_judul_skripsi,
            'tahapan' => 'Proposal',
            'tanggal_bimbingan' => $tgl,
            'catatan_mahasiswa' => $tl_mahasiswa,
            'progress_persen' => $progress_persen,
            'status_bimbingan' => $status_bimbingan,
            'status_pendamping' => $status_pendamping,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user->id
        ];
        $this->E_skripsi_bimbingan_model->save_bimbingan_log($log_data);

        // AUTO-CLOSE AGENDA DOSEN JIKA UTAMA MENYETUJUI BIMBINGAN
        $id_ptk = $this->session->userdata('id_ptk');
        $skripsi_data = $this->db->get_where('siakad_skripsi_pengajuan_judul', ['id_judul_skripsi' => $id_judul_skripsi])->row();
        $is_utama = ($skripsi_data && $skripsi_data->dosen_pembimbing_1 == $id_ptk);
        if ($is_utama && $status_bimbingan == 'Disetujui') {
            $active_agenda = $this->E_skripsi_bimbingan_model->get_agenda_aktif($id_judul_skripsi);
            if ($active_agenda) {
                $this->E_skripsi_bimbingan_model->close_agenda($active_agenda->id_log, $user->id);
            }
        }

        $lembar_monitoring = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_kartu_bimbingan_proposal');
        if ($lembar_monitoring->num_rows() == 0) {
            $html_respon_awal = '';
            $html_table_awal = '';
        } else {
            $html_respon_awal = $lembar_monitoring->row()->html_respon;
            $html_table_awal = $lembar_monitoring->row()->html_table;
        }

        $catatan = str_ireplace(["\r\n", "\n", "\r"], '<br />', $tl_mahasiswa);
        $html_respon = $html_respon_awal . '<div class="panel panel-default"><div class="panel-heading"><a href="javascript:void(0)" class="item-name primary-link">' . $this->ion_auth->user()->row()->first_name . '</a> <span class="item-label text-right">' . tgl_indo_short($tgl) . ', ' . $jam . '</span></div><div class="panel-body">' . $catatan . '</div></div>';
        $html_table = $html_table_awal . '<tr><td>' . tgl_indo_short($tgl) . '</td><td>' . $tl_mahasiswa . '</td><td>' . $this->ion_auth->user()->row()->first_name . ' *)</td></tr>';

        $data = [
            'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
            'html_respon' => $html_respon,
            'html_table' => $html_table,
            'tipe_monitoring' => 1,
            'status_approve' => 1,
            'tanggal_bimbingan' => NULL,
            'jam_bimbingan' => NULL,
            'dosen_pembimbing' => NULL,
            'tulisan_mahasiswa' => NULL
        ];

        $this->E_skripsi_bimbingan_model->save($data);

        $this->_recalculate_academic_health($id_judul_skripsi);

        // Clear menu badge session cache to force reload
        $this->session->unset_userdata('bimbingan_last_check');

        echo json_encode(["status" => TRUE]);
    }

    private function _validate_kegiatan()
    {
        $data = [];
        $data['error_string'] = [];
        $data['inputerror'] = [];
        $data['status'] = TRUE;

        $tanggal_bimbingan = $this->input->post('tanggal_bimbingan');
        $jam_bimbingan = $this->input->post('jam_bimbingan');
        $tulisan_mahasiswa = $this->input->post('tulisan_mahasiswa');

        if ($tanggal_bimbingan == '') {
            $data['inputerror'][] = 'tanggal_bimbingan';
            $data['error_string'][] = 'Tanggal harus diisi';
            $data['status'] = FALSE;
        }

        if ($jam_bimbingan == '') {
            $data['inputerror'][] = 'jam_bimbingan';
            $data['error_string'][] = 'Jam harus diisi';
            $data['status'] = FALSE;
        }

        if ($tulisan_mahasiswa == '') {
            $data['inputerror'][] = 'tulisan_mahasiswa';
            $data['error_string'][] = 'Resume bimbingan harus diisi';
            $data['status'] = FALSE;
        }

        if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $jam_bimbingan) == FALSE) {
            $data['inputerror'][] = 'jam_bimbingan';
            $data['error_string'][] = 'Format Jam Salah, format yang benar adalah hh:mm';
            $data['status'] = FALSE;
        }



        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }


    public function cetak_pdf($id_judul_skripsi)
    {
        $judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
            ->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
            ->join('mahasiswa m', 'm.id_pd = mp.id_pd')
            ->join('sms s', 's.id_sms = mp.id_sms')
            ->where('md5(j.id_judul_skripsi)', $id_judul_skripsi)->get()->row();
        $data_bimbingan = $this->db->where('md5(id_judul_skripsi)', $id_judul_skripsi)->get('siakad_skripsi_kartu_bimbingan_proposal');

        $doping_1 = $this->db->where('id_ptk', $judul->dosen_pembimbing_1)->get('siakad_skripsi_dosen');
        $doping_2 = $this->db->where('id_ptk', $judul->dosen_pembimbing_2)->get('siakad_skripsi_dosen');
        $nm_doping_1 = $nm_doping_2 = '';



        if ($doping_1->num_rows() > 0) {
            $nm_doping_1 = $doping_1->row()->nama_lengkap;
        }

        if ($doping_2->num_rows() > 0) {
            $nm_doping_2 = $doping_2->row()->nama_lengkap;
        }

        $nm_lemb = $this->db->where('id_sms', $this->session->userdata('prodi'))->get('sms')->row()->nm_lemb;

        if ($judul->tanggal_sk == '' or $judul->nomor_sk == '') {
            echo "Tanggal SK dan Nomor SK belum diterbitkan.";
        } else {
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
            $output['data'] = $judul;
            $output['nm_lemb'] = $nm_lemb;

            $output['nm_doping_1'] = $nm_doping_1;
            $output['nm_doping_2'] = $nm_doping_2;

            if ($data_bimbingan->num_rows() > 0) {
                $output['html_table'] = nl2br($data_bimbingan->row()->html_table);
            } else {
                $output['html_table'] = '';
            }


            $html = $this->load->view('cetak/pdf_bimbingan_proposal', $output, true);
            $pdfFilePath = 'laporan_konsultasi_proposal_' . $judul->nipd . '.pdf';

            $mpdf->SetHTMLHeader('
						<div style="position: absolute; right:40; top: 20;">
						<img src="' . FCPATH . "assets/image_web/kop_wbi.png" . '" width="250px" />
						</div>');
            $mpdf->SetHTMLFooter('
						<div style="font-size: 12px; color: #009999 ;bottom:10;position:absolute">
						<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
						<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
						Kabupaten Deli Serdang – Sumatera Utara<br>
						(061) 42074111; Laman : www.wbi.ac.id<br>
						</p>
						</div>');

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, "I");
        }
    }

    private function _recalculate_academic_health($id_judul_skripsi)
    {
        $all_logs = $this->E_skripsi_bimbingan_model->get_bimbingan_logs($id_judul_skripsi);

        $logs = [];
        foreach ($all_logs as $log) {
            if (!empty($log->catatan_mahasiswa) && $log->initiator_type != 'dosen') {
                $logs[] = $log;
            }
        }

        $frekuensi = count($logs);
        $current_progress = 0;
        $last_bimbingan_date = NULL;

        if ($frekuensi > 0) {
            // progress is the max progress achieved in the logs to prevent new pending submissions from resetting it
            foreach ($logs as $log) {
                if (intval($log->progress_persen) > $current_progress) {
                    $current_progress = intval($log->progress_persen);
                }
            }
            $last_bimbingan_date = $logs[count($logs) - 1]->tanggal_bimbingan;
        }

        $score = 50;

        $freq_boost = min($frekuensi * 5, 25);
        $prog_boost = min($current_progress * 0.25, 25);

        $score += $freq_boost + $prog_boost;

        if ($last_bimbingan_date) {
            $days_diff = (strtotime(date('Y-m-d')) - strtotime($last_bimbingan_date)) / (60 * 60 * 24);
            if ($days_diff > 14) {
                $score -= 20;
            }
        } else {
            $score -= 20;
        }

        $score = max(0, min(100, $score));

        $kategori = 'Aman';
        if ($frekuensi == 0) {
            // Cek jika persetujuan judul sudah lebih dari 6 bulan (180 hari)
            $judul_data = $this->db->select('tanggal_disetujui')
                ->where('id_judul_skripsi', $id_judul_skripsi)
                ->get('siakad_skripsi_pengajuan_judul')
                ->row();

            $is_old_approved = false;
            if ($judul_data && !empty($judul_data->tanggal_disetujui) && $judul_data->tanggal_disetujui != '0000-00-00 00:00:00' && $judul_data->tanggal_disetujui != '0000-00-00') {
                $days_approved = (strtotime(date('Y-m-d')) - strtotime($judul_data->tanggal_disetujui)) / (60 * 60 * 24);
                if ($days_approved > 180) {
                    $is_old_approved = true;
                }
            }

            if ($is_old_approved) {
                $kategori = 'Risiko Tinggi';
                $score = 0;
            } else {
                $kategori = 'Belum Ada Bimbingan';
                $score = 0;
            }
        } else {
            if ($score < 50) {
                $kategori = 'Risiko Tinggi';
            } else if ($score < 70) {
                $kategori = 'Perlu Perhatian';
            }
        }

        $health_data = [
            'current_progress' => $current_progress,
            'academic_health_score' => $score,
            'kategori_health' => $kategori,
            'last_bimbingan_date' => $last_bimbingan_date
        ];
        $this->E_skripsi_bimbingan_model->update_academic_health($id_judul_skripsi, $health_data);
    }

    public function convertURLs($string)
    {
        $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
    }

    // --- Agenda Bimbingan Methods ---

    public function ajax_get_agenda($id_judul)
    {
        $id_ptk = $this->session->userdata('id_ptk');
        if (empty($id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Unauthorized']);
            return;
        }

        $agenda = $this->E_skripsi_bimbingan_model->get_agenda_aktif($id_judul);
        $arsip_agendas = $this->E_skripsi_bimbingan_model->get_agenda_arsip($id_judul);

        // Attach todos to each arsip agenda
        $arsip_with_todos = [];
        foreach ($arsip_agendas as $a) {
            $a->todos = $this->E_skripsi_bimbingan_model->get_todo_by_log($a->id_log);
            $arsip_with_todos[] = $a;
        }

        if ($agenda) {
            $todos = $this->E_skripsi_bimbingan_model->get_todo_by_log($agenda->id_log);
            echo json_encode(['status' => true, 'agenda' => $agenda, 'todos' => $todos, 'arsip' => $arsip_with_todos]);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Tidak ada agenda aktif', 'arsip' => $arsip_with_todos]);
        }
    }

    public function ajax_save_agenda()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');

        $judul = $this->E_skripsi_bimbingan_model->get_by_id_2($id_judul_skripsi);
        if (!$judul || $judul->dosen_pembimbing_1 != $id_ptk) {
            echo json_encode(['status' => false, 'msg' => 'Hanya Dosen Pembimbing Utama yang dapat membuat agenda.']);
            return;
        }

        $data_log = [
            'id_judul_skripsi' => $id_judul_skripsi,
            'initiator_type' => 'dosen',
            'tahapan' => $this->input->post('tahapan') ?: 'Proposal',
            'tanggal_bimbingan' => date('Y-m-d'),
            'catatan_mahasiswa' => $this->input->post('instruksi'),
            'status_bimbingan' => 'Agenda Dosen',
            'created_at' => date('Y-m-d H:i:s'),

            'created_by' => $this->ion_auth->user()->row()->id,
            'agenda_tgl_mulai' => $this->input->post('tgl_mulai'),
            'agenda_tgl_selesai' => $this->input->post('tgl_selesai'),
            'agenda_jam_mulai' => $this->input->post('jam_mulai'),
            'agenda_jam_selesai' => $this->input->post('jam_selesai'),
            'agenda_prioritas' => $this->input->post('prioritas'),
            'agenda_fase_seminar' => $this->input->post('fase_seminar'),
            'agenda_tahapan' => $this->input->post('tahapan') ?: 'Proposal',
            'agenda_konteks' => $this->input->post('konteks'),
            'ai_review_agenda' => $this->input->post('ai_review_agenda')
        ];

        $todos_json = $this->input->post('todos');
        $todos = json_decode($todos_json, true);
        if (!is_array($todos))
            $todos = [];

        $id_log = $this->E_skripsi_bimbingan_model->save_agenda_log($data_log, $todos);

        if ($id_log) {
            echo json_encode(['status' => true, 'msg' => 'Agenda berhasil dibuat.']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal menyimpan agenda.']);
        }
    }

    public function ajax_generate_ai_agenda()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');
        $konteks = $this->input->post('konteks');

        $judul = $this->E_skripsi_bimbingan_model->get_by_id_2($id_judul_skripsi);
        if ($judul->dosen_pembimbing_1 != $id_ptk) {
            echo json_encode(['status' => false, 'msg' => 'Hanya Dosen Utama.']);
            return;
        }

        $all_logs = $this->E_skripsi_bimbingan_model->get_bimbingan_logs($id_judul_skripsi);
        $total_bimbingan = count(array_filter($all_logs, fn($l) => !empty($l->catatan_mahasiswa) && $l->initiator_type != 'dosen'));
        $last_bimbingan = 'Belum pernah';
        if ($total_bimbingan > 0) {
            $last_bimbingan = end($all_logs)->tanggal_bimbingan;
        }

        $doc_text = "Data Mahasiswa:\n";
        $doc_text .= "Nama: {$judul->nm_pd}\n";
        $doc_text .= "Judul TA: {$judul->judul_ditetapkan}\n";
        $doc_text .= "Total Bimbingan: {$total_bimbingan} sesi\n";
        $doc_text .= "Terakhir Bimbingan: {$last_bimbingan}\n";
        $doc_text .= "Seminar: " . ($this->input->post('fase_seminar') ?? 'Belum') . "\n";

        if (is_array($konteks)) {
            $doc_text .= "Konteks Dosen:\n";
            foreach ($konteks as $key => $val) {
                $doc_text .= "- $key: $val\n";
            }
        }

        $prompt = "Kamu adalah Asisten Akademik Dosen Pembimbing TA di Politeknik WBI.\n";
        $prompt .= "Berikan output dalam JSON KETAT sesuai format berikut — TANPA teks di luar JSON.\n\n";
        $prompt .= "OUTPUT FORMAT (JSON):\n";
        $prompt .= "{\n";
        $prompt .= "  \"review_kondisi\": \"2-3 paragraf analisis kondisi mahasiswa dalam Markdown\",\n";
        $prompt .= "  \"strategi_percepatan\": \"3-5 poin strategi untuk dosen dalam Markdown\",\n";
        $prompt .= "  \"todo_list\": [\n";
        $prompt .= "    {\"task\": \"Deskripsi tugas konkret 1\", \"urutan\": 1},\n";
        $prompt .= "    {\"task\": \"Deskripsi tugas konkret 2\", \"urutan\": 2}\n";
        $prompt .= "  ]\n";
        $prompt .= "}\n\n";
        $prompt .= "ATURAN PENTING untuk todo_list:\n";
        $prompt .= "- Setiap item todo_list adalah TUGAS UNTUK MAHASISWA, BUKAN untuk dosen.\n";
        $prompt .= "- DILARANG membuat task seperti: 'Menghubungi mahasiswa', 'Menjadwalkan pertemuan', 'Membuat undangan via WhatsApp', 'Memantau perkembangan', atau tugas apapun yang dilakukan oleh dosen.\n";
        $prompt .= "- Semua tugas harus dimulai dengan kata kerja yang menunjukkan aksi mahasiswa: 'Menyusun...', 'Melengkapi...', 'Membuat...', 'Merevisi...', 'Mengumpulkan...', 'Menganalisis...', dll.\n";
        $prompt .= "- Buat 5-8 item to-do yang SPESIFIK, MEASURABLE, dan bisa dikerjakan mahasiswa secara mandiri dalam waktu dekat.\n";
        $prompt .= "- Contoh tugas BENAR: 'Menyusun draft Bab 1 dengan minimal 2000 kata', 'Mengumpulkan 10 referensi jurnal internasional terkait topik penelitian'\n";
        $prompt .= "- Contoh tugas SALAH: 'Dosen menghubungi mahasiswa via WA', 'Menjadwalkan sesi bimbingan pertama'";

        $this->load->library('Ai_reviewer');
        $this->load->model('Setting_api_key_model');
        $api_key_row = $this->Setting_api_key_model->get_api_key($id_ptk);

        if ($api_key_row && !empty($api_key_row->api_key)) {
            // Gunakan API key milik dosen yang login
            $provider = $api_key_row->ai_provider ?: 'gemini';
            $api_key = $api_key_row->api_key;
        } else {
            // Fallback ke setting global
            $settings = $this->db->get('setting')->row();
            $provider = $settings->ai_provider ?? 'gemini';
            $api_key = $settings->ai_api_key ?? '';
        }

        if (empty($api_key)) {
            echo json_encode(['status' => false, 'msg' => 'API Key belum dikonfigurasi. Silakan atur di menu Settings → AI Configuration.']);
            return;
        }

        $res = $this->ai_reviewer->generate_review($provider, $api_key, $prompt, $doc_text, false, true);

        if ($res['status']) {
            $json_str = $res['review'];
            $json_str = preg_replace('/^```json\s*/i', '', $json_str);
            $json_str = preg_replace('/\s*```$/i', '', $json_str);
            $data = json_decode($json_str, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['todo_list'])) {
                echo json_encode(['status' => true, 'data' => $data]);
            } else {
                echo json_encode(['status' => false, 'msg' => 'AI tidak merespons dalam format JSON yang valid.', 'raw' => $res['review']]);
            }
        } else {
            echo json_encode($res);
        }
    }

    public function ajax_update_todo_dosen()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_todo = $this->input->post('id_todo');
        $verified = $this->input->post('verified');
        $user_id = $this->ion_auth->user()->row()->id;

        $res = $this->E_skripsi_bimbingan_model->update_todo_item($id_todo, [
            'verified_dosen' => $verified,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => $user_id
        ]);

        echo json_encode(['status' => true]);
    }

    public function ajax_edit_todo_list()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        if (empty($id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Unauthorized']);
            return;
        }
        $id_log = $this->input->post('id_log');
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');
        $todos_json = $this->input->post('todos');
        $todos = json_decode($todos_json, true);
        if (!is_array($todos))
            $todos = [];

        // Proteksi: Jangan izinkan edit jika ada To-Do list yang sudah diselesaikan (dicentang) oleh mahasiswa
        $completed_todos = $this->db->where('id_log', $id_log)
            ->where('done_by_mhs', 1)
            ->get('siakad_skripsi_agenda_todo')
            ->num_rows();

        if ($completed_todos > 0) {
            echo json_encode(['status' => false, 'msg' => 'Tidak dapat mengubah To-Do list karena mahasiswa sudah mencentang progres.']);
            return;
        }

        $res = $this->E_skripsi_bimbingan_model->update_todo_list($id_log, $id_judul_skripsi, $todos);
        if ($res) {
            echo json_encode(['status' => true, 'msg' => 'To-Do list berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal memperbarui to-do list.']);
        }
    }

    public function ajax_close_agenda()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $id_log = $this->input->post('id_log');
        $user_id = $this->ion_auth->user()->row()->id;

        $res = $this->E_skripsi_bimbingan_model->close_agenda($id_log, $user_id);

        $log = $this->db->where('id_log', $id_log)->get('siakad_skripsi_bimbingan_log')->row();
        if ($log) {
            $this->_recalculate_academic_health($log->id_judul_skripsi);
        }

        echo json_encode(['status' => true]);
    }

    public function cetak_rekap_bimbingan()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        if (empty($id_ptk)) {
            show_error('Anda harus login sebagai Dosen.', 403);
            return;
        }

        $data_mahasiswa = $this->E_skripsi_bimbingan_model->get_all_bimbingan_dosen($id_ptk);

        $mahasiswa_aktif = [];
        foreach ($data_mahasiswa as $m) {
            // Fetch logs
            $logs = $this->E_skripsi_bimbingan_model->get_bimbingan_logs($m->id_judul_skripsi);

            // Filter: Hanya mahasiswa yang sudah melakukan bimbingan (punya minimal 1 log)
            if (count($logs) > 0) {
                $m->logs = $logs;

                // Fetch Prodi Name
                $prodi_query = $this->db->select('nm_lemb')->where('id_sms', $m->id_sms)->get('sms');
                $m->nama_prodi = ($prodi_query->num_rows() > 0) ? $prodi_query->row()->nm_lemb : '-';

                // Fetch Dosen Names
                $dosen1_query = $this->db->select('nama_lengkap')->where('id_ptk', $m->dosen_pembimbing_1)->get('siakad_skripsi_dosen');
                $m->nama_dosen_1 = ($dosen1_query->num_rows() > 0) ? $dosen1_query->row()->nama_lengkap : '-';

                $m->nama_dosen_2 = null;
                if (!empty($m->dosen_pembimbing_2) && $m->dosen_pembimbing_2 != '-') {
                    $dosen2_query = $this->db->select('nama_lengkap')->where('id_ptk', $m->dosen_pembimbing_2)->get('siakad_skripsi_dosen');
                    if ($dosen2_query->num_rows() > 0) {
                        $m->nama_dosen_2 = $dosen2_query->row()->nama_lengkap;
                    }
                }

                $mahasiswa_aktif[] = $m;
            }
        }

        $data['mahasiswa'] = $mahasiswa_aktif;
        $data['dosen_name'] = $this->ion_auth->user()->row()->first_name;

        $this->load->view('cetak/cetak_rekap_bimbingan_html', $data);
    }
    public function ajax_cek_tunggakan_mhs($nipd)
    {
        $id_ptk = $this->session->userdata('id_ptk');
        if (empty($id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak']);
            return;
        }

        // Cek cache dari session
        $session_key = "tunggakan_" . $nipd;
        if ($this->session->has_userdata($session_key)) {
            $is_menunggak = $this->session->userdata($session_key);
            echo json_encode(['status' => true, 'is_menunggak' => $is_menunggak]);
            return;
        }

        // Panggil fungsi API jika tidak ada di session
        $is_menunggak = cek_tunggakan($nipd);

        // Simpan ke session untuk cache
        $this->session->set_userdata($session_key, $is_menunggak);

        echo json_encode(['status' => true, 'is_menunggak' => $is_menunggak]);
    }
    public function ajax_get_strategic_plan()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $bulan_tahun = date('Y-m');

        $plan = $this->db->where('id_ptk', $id_ptk)
            ->where('bulan_tahun', $bulan_tahun)
            ->get('siakad_skripsi_ai_strategic_plan')
            ->row();

        if ($plan) {
            echo json_encode(['status' => true, 'data' => $plan]);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Belum ada plan bulan ini.']);
        }
    }

    /**
     * Ambil daftar bab (chunks) yang tersedia untuk satu id_judul_skripsi.
     * Mengambil chunk terbaru berdasarkan id_log terbesar.
     */
    public function ajax_get_chunks_bab()
    {
        $id_judul_skripsi = $this->input->post('id_judul_skripsi');
        $id_ptk = $this->session->userdata('id_ptk');

        if (empty($id_judul_skripsi) || empty($id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Parameter tidak valid.']);
            return;
        }

        // Verifikasi dosen adalah pembimbing dari judul ini
        $auth_check = $this->db->get_where('siakad_skripsi_pengajuan_judul', [
            'id_judul_skripsi' => $id_judul_skripsi,
        ])->row();
        if (!$auth_check || ($auth_check->dosen_pembimbing_1 != $id_ptk && $auth_check->dosen_pembimbing_2 != $id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak.']);
            return;
        }

        // Ambil chunk dari id_log terbesar (naskah terbaru yang di-upload mahasiswa)
        $latest_log = $this->db->select('MAX(id_log) as max_id_log')
            ->where('id_judul_skripsi', $id_judul_skripsi)
            ->get('siakad_skripsi_chunk_files')
            ->row();

        if (!$latest_log || empty($latest_log->max_id_log)) {
            echo json_encode(['status' => false, 'msg' => 'Belum ada data chunking naskah untuk mahasiswa ini. Pastikan mahasiswa sudah mengunggah naskah dan sistem chunking sudah berjalan.']);
            return;
        }

        $chunks = $this->db->where('id_judul_skripsi', $id_judul_skripsi)
            ->where('id_log', $latest_log->max_id_log)
            ->order_by('bab_number', 'ASC')
            ->get('siakad_skripsi_chunk_files')
            ->result();

        if (empty($chunks)) {
            echo json_encode(['status' => false, 'msg' => 'Data chunk tidak ditemukan.']);
            return;
        }

        $data = [];
        foreach ($chunks as $c) {
            $data[] = [
                'id'            => $c->id,
                'id_log'        => $c->id_log,
                'bab_number'    => $c->bab_number,
                'bab_label'     => $c->bab_label,
                'bab_title'     => $c->bab_title,
                'char_count'    => $c->char_count,
                'token_estimate'=> $c->token_estimate,
                'has_review'    => !empty($c->ai_review_result),
                'ai_review_result'  => $c->ai_review_result,
                'ai_review_summary' => $c->ai_review_summary,
                'ai_reviewed_at'    => $c->ai_reviewed_at,
            ];
        }

        // Ambil info skripsi untuk konteks
        $skripsi = $this->db->select('a.judul_ditetapkan, b.nm_lemb')
            ->from('siakad_skripsi_pengajuan_judul a')
            ->join('sms b', 'a.id_sms = b.id_sms', 'left')
            ->where('a.id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        // Ambil status share AI review dari tabel bimbingan log
        $bimbingan_log = $this->db->select('is_ai_review_visible')->where('id_log', $latest_log->max_id_log)->get('siakad_skripsi_bimbingan_log')->row();
        $is_ai_review_visible = $bimbingan_log ? $bimbingan_log->is_ai_review_visible : 0;

        echo json_encode([
            'status'  => true,
            'babs'    => $data,
            'id_log'  => $latest_log->max_id_log,
            'judul_ta'=> $skripsi ? $skripsi->judul_ditetapkan : '-',
            'prodi'   => $skripsi ? $skripsi->nm_lemb : '-',
            'is_ai_review_visible' => $is_ai_review_visible,
        ]);
    }

    /**
     * Review satu bab dari hasil chunking secara sequential.
     * Menerima: id_chunk (ID dari siakad_skripsi_chunk_files), id_judul_skripsi,
     *           previous_summaries (JSON array ringkasan bab-bab sebelumnya, untuk chaining)
     */
    public function ajax_review_bab_chunk()
    {
        // Mencegah PHP mati karena timeout default (30 detik) saat memanggil AI yang memakan waktu lama
        set_time_limit(300);

        $id_chunk         = (int) $this->input->post('id_chunk');
        $id_judul_skripsi = (int) $this->input->post('id_judul_skripsi');
        $previous_summaries_json = $this->input->post('previous_summaries'); // JSON string
        $force_regenerate = $this->input->post('force_regenerate') == '1';

        $id_ptk_auth = $this->session->userdata('id_ptk');

        if (!$id_chunk || !$id_judul_skripsi || !$id_ptk_auth) {
            echo json_encode(['status' => false, 'msg' => 'Parameter tidak valid.']);
            return;
        }

        // Verifikasi dosen adalah pembimbing dari judul ini
        $auth_check2 = $this->db->get_where('siakad_skripsi_pengajuan_judul', [
            'id_judul_skripsi' => $id_judul_skripsi,
        ])->row();
        if (!$auth_check2 || ($auth_check2->dosen_pembimbing_1 != $id_ptk_auth && $auth_check2->dosen_pembimbing_2 != $id_ptk_auth)) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak.']);
            return;
        }

        // 1. Ambil data chunk
        $chunk = $this->db->get_where('siakad_skripsi_chunk_files', [
            'id'              => $id_chunk,
            'id_judul_skripsi'=> $id_judul_skripsi,
        ])->row();

        if (!$chunk) {
            echo json_encode(['status' => false, 'msg' => 'Data bab tidak ditemukan.']);
            return;
        }

        // 2. Jika sudah ada review dan tidak force, kembalikan hasil lama
        if (!$force_regenerate && !empty($chunk->ai_review_result)) {
            echo json_encode([
                'status'  => true,
                'msg'     => 'Review sudah tersedia (dari cache).',
                'review'  => $chunk->ai_review_result,
                'summary' => $chunk->ai_review_summary,
                'cached'  => true,
            ]);
            return;
        }

        // 3. Ambil konten bab
        $content = trim((string)$chunk->content);
        
        // --- PRE-PROCESSING / SANITASI KONTEN ---
        // Jika formatnya JSON (dari ekstraksi dokumen sisi student)
        if (str_starts_with($content, '{') || str_starts_with($content, '[')) {
            $parsed_json = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Prioritaskan 'html' agar tabel dan format struktur terbaca oleh AI
                if (isset($parsed_json['html']) && !empty(trim($parsed_json['html']))) {
                    // Hapus tag styling/script, pertahankan struktur penting (terutama tabel)
                    $allowed_tags = '<h1><h2><h3><h4><p><br><b><i><strong><em><ul><ol><li><table><thead><tbody><tr><th><td>';
                    $content = strip_tags($parsed_json['html'], $allowed_tags);
                } elseif (isset($parsed_json['text'])) {
                    $content = $parsed_json['text'];
                } else {
                    $content = print_r($parsed_json, true);
                }
            }
        }
        
        // Cek jika formatnya XML
        if (str_starts_with($content, '<') && str_contains($content, '<?xml')) {
            $xml = @simplexml_load_string($content);
            if ($xml !== false) {
                $content = trim((string)$xml);
            }
        }

        // Hapus kode hidden field dari Mendeley/Zotero agar tidak dibaca AI sebagai typo
        $content = preg_replace('/ADDIN\s+CSL_CITATION/i', '', $content);
        // Hapus atribut HTML berlebih yang memakan token (seperti class, style, width, id, dll)
        $content = preg_replace('/\s(class|style|width|height|id)="[^"]*"/i', '', $content);

        if (empty($content)) {
            echo json_encode(['status' => false, 'msg' => 'Konten bab kosong.']);
            return;
        }

        // Batasi konten agar tidak terlalu besar. Karena formatnya bisa HTML, 
        // batas dinaikkan ke 150000 karakter (aman untuk Gemini 1.5 Flash)
        $content = mb_substr($content, 0, 150000);

        // 4. Ambil API Key
        $id_ptk = $id_ptk_auth; // gunakan variabel yang sudah diset di auth check
        $this->load->model('Setting_api_key_model');
        $api_setting = $this->Setting_api_key_model->get_api_key($id_ptk);

        if (empty($api_setting) || empty($api_setting->api_key)) {
            echo json_encode(['status' => false, 'msg' => 'API Key belum disetting. Silakan setting API Key terlebih dahulu.']);
            return;
        }

        $api_key    = $api_setting->api_key;
        $ai_provider= isset($api_setting->ai_provider) && !empty($api_setting->ai_provider) ? $api_setting->ai_provider : 'gemini';
        $ai_model   = isset($api_setting->ai_model) && !empty($api_setting->ai_model) ? $api_setting->ai_model : 'gemini-1.5-flash';

        // 5. Ambil info skripsi
        $skripsi = $this->db->select('a.judul_ditetapkan, b.nm_lemb, a.nipd')
            ->from('siakad_skripsi_pengajuan_judul a')
            ->join('sms b', 'a.id_sms = b.id_sms', 'left')
            ->where('a.id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        $judul_ta = $skripsi ? $skripsi->judul_ditetapkan : 'Tidak diketahui';
        $prodi    = $skripsi ? $skripsi->nm_lemb : 'Tidak diketahui';
        $nipd     = $skripsi ? $skripsi->nipd : '';

        // Ambil nama mahasiswa
        $mahasiswa = $this->db->query("SELECT nm_pd FROM mahasiswa m JOIN mahasiswa_pt mp ON m.id_pd = mp.id_pd WHERE mp.nipd = ?", [$nipd])->row();
        $nm_pd = $mahasiswa ? $mahasiswa->nm_pd : 'Tidak diketahui';

        // 6. Informasi Peran Dosen
        $skripsi_full = $this->db->select('dosen_pembimbing_1, dosen_pembimbing_2')
            ->from('siakad_skripsi_pengajuan_judul')
            ->where('id_judul_skripsi', $id_judul_skripsi)
            ->get()->row();

        $is_utama     = ($skripsi_full && $skripsi_full->dosen_pembimbing_1 == $id_ptk);
        $is_pendamping= ($skripsi_full && $skripsi_full->dosen_pembimbing_2 == $id_ptk);
        $hanya_satu   = ($skripsi_full && (empty($skripsi_full->dosen_pembimbing_2) || $skripsi_full->dosen_pembimbing_2 == '-'));

        if ($is_utama && !$is_pendamping && !$hanya_satu) {
            $nama_peran    = "Dosen Pembimbing Utama";
            $is_akuntansi_perpajakan = (str_contains(strtolower($prodi), 'akuntansi perpajakan'));
            
            if (!$is_akuntansi_perpajakan) {
                // Ambil log bimbingan terbaru untuk mengecek apakah pendamping sudah mereview
                $latest_log = $this->db->order_by('id_log', 'DESC')->get_where('siakad_skripsi_bimbingan_log', ['id_judul_skripsi' => $id_judul_skripsi])->row();
                $pendamping_sudah_review = $latest_log ? !empty($latest_log->catatan_pendamping) : false;
                
                if ($pendamping_sudah_review) {
                    $catatan_pendamping_bersih = strip_tags(str_replace(['<br>', '</p>'], ' ', $latest_log->catatan_pendamping));
                    $instruksi_peran = "Dosen Pendamping telah mereview bagian tata tulis dengan catatan: '{$catatan_pendamping_bersih}'. Tolong BACA catatan tersebut agar Anda TIDAK MENGULANGI temuan yang sama. Fokuslah 100% pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. ABAIKAN masalah tata bahasa (typo, ejaan).";
                } else {
                    $instruksi_peran = "PRIORITASKAN evaluasi pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. ABAIKAN kesalahan tata bahasa minor (seperti typo, ejaan). Porsi koreksi tata bahasa harus dikurangi seminimal mungkin dan diserahkan kepada Dosen Pendamping.";
                }
            } else {
                $instruksi_peran = "PRIORITASKAN evaluasi pada kualitas dan kedalaman substansi penelitian: validitas metodologi, kekoherensian argumen, relevansi tinjauan pustaka, serta ketercapaian tujuan penelitian. ABAIKAN kesalahan tata bahasa minor (seperti typo, ejaan). Porsi koreksi tata bahasa harus dikurangi seminimal mungkin.";
            }
        } elseif ($is_pendamping && !$is_utama) {
            $nama_peran    = "Dosen Pembimbing Pendamping";
            $instruksi_peran = "PRIORITASKAN evaluasi pada tata tulis, ejaan (EYD), pemformatan dokumen (font, margin, penomoran), penggunaan Bahasa Indonesia baku, dan kesesuaian dengan template penulisan TA Politeknik WBI. Pastikan arahan substantif dari Pembimbing Utama sudah tercermin dalam isi dokumen.";
        } else {
            $nama_peran    = "Dosen Pembimbing (Utama & Pendamping)";
            $instruksi_peran = "Berikan penilaian komprehensif, mencakup substansi penelitian sekaligus tata tulis, pemformatan, dan kesesuaian template.";
        }

        // 7. Bangun konteks bab sebelumnya (chaining)
        $previous_summaries = [];
        if (!empty($previous_summaries_json)) {
            $decoded = json_decode($previous_summaries_json, true);
            if (is_array($decoded)) {
                $previous_summaries = $decoded;
            }
        }

        $context_sebelumnya = '';
        if (!empty($previous_summaries)) {
            $context_sebelumnya = "\n\n## Konteks Review Bab-bab Sebelumnya\n\nBerikut ringkasan temuan review dari bab-bab yang telah dianalisis sebelumnya (gunakan sebagai referensi untuk menilai kesinambungan dan konsistensi antar bab):\n\n";
            foreach ($previous_summaries as $s) {
                $context_sebelumnya .= "**{$s['bab_label']} - {$s['bab_title']}:**\n{$s['summary']}\n\n";
            }
            $context_sebelumnya .= "---\n";
        }

        // 8. Prompt spesifik per bab
        $bab_number = (int) $chunk->bab_number;
        $bab_label  = $chunk->bab_label;
        $bab_title  = $chunk->bab_title;

        $fokus_bab = "Lakukan review standar pada konten bab ini.";
        switch ($bab_number) {
            case 1:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa ketajaman latar belakang (apakah masalah diuraikan dengan data/fakta yang kuat?), kejelasan rumusan masalah, relevansi tujuan penelitian, dan batasan penelitian. Verifikasi apakah bab ini sudah memperkenalkan konteks masalah secara cukup untuk mendukung bab-bab selanjutnya. Tandai jika latar belakang terlalu generik atau tidak ada data pendukung.";
                break;
            case 2:
                $current_year = date('Y');
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa kedalaman dan relevansi tinjauan pustaka terhadap topik penelitian di Bab 1. Evaluasi apakah teori-teori yang dikutip up-to-date (diutamakan 5 tahun terakhir dari tahun saat ini yaitu {$current_year}). Cek konsistensi variabel penelitian antara Bab 1 dan Bab 2. Periksa apakah landasan teori cukup kuat untuk menopang metodologi di Bab 3. " . (!empty($previous_summaries) ? "Bandingkan dengan temuan Bab 1 yang sudah dianalisis sebelumnya untuk memeriksa keselarasan konsep." : "");
                break;
            case 3:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa apakah metodologi yang dipilih sesuai dengan rumusan masalah di Bab 1 dan teori di Bab 2. Evaluasi kejelasan desain penelitian, teknik pengumpulan data, dan teknik analisis data. Cek apakah populasi, sampel, dan instrumen penelitian didefinisikan dengan jelas. Tandai jika ada gap antara metodologi yang diklaim dengan yang benar-benar dijelaskan. " . (!empty($previous_summaries) ? "Gunakan ringkasan bab sebelumnya untuk menilai konsistensi arah penelitian." : "");
                break;
            case 4:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa apakah hasil yang disajikan relevan dan menjawab tujuan penelitian di Bab 1. Evaluasi kualitas deskripsi hasil, kelengkapan data, dan kejelasan tabel/grafik. Cek apakah ada data yang tidak dianalisis atau hasil yang ambigu. " . (!empty($previous_summaries) ? "Bandingkan dengan metodologi Bab 3: apakah metode yang direncanakan benar-benar dieksekusi dalam bab ini?" : "");
                break;
            case 5:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa kualitas analisis dan pembahasan — apakah hasil di Bab 4 diinterpretasikan dengan benar? Cek apakah pembahasan mengacu pada teori di Bab 2 dan menjawab rumusan masalah di Bab 1. Tandai jika interpretasi terlalu dangkal atau tidak didukung data. " . (!empty($previous_summaries) ? "Gunakan ringkasan bab sebelumnya untuk menilai ketercapaian tujuan penelitian secara komprehensif." : "");
                break;
            case 6:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa apakah kesimpulan benar-benar menjawab rumusan masalah di Bab 1 secara one-to-one. Evaluasi apakah saran bersifat konkret dan actionable (bukan generik). Cek kelengkapan Daftar Pustaka dan konsistensinya dengan referensi yang disitasi dalam naskah. " . (!empty($previous_summaries) ? "Ini adalah bab terakhir — nilai keseluruhan konsistensi penelitian dari Bab 1 hingga Bab 6 berdasarkan ringkasan yang tersedia." : "");
                break;
            default:
                $fokus_bab = "Evaluasi **{$bab_label} – {$bab_title}**: Periksa konten, struktur, dan kejelasan penulisan bab ini. Pastikan bab ini berkontribusi pada alur penelitian secara keseluruhan.";
                break;
        }
        $is_local = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || str_contains($_SERVER['HTTP_HOST'], '.test') || str_contains($_SERVER['HTTP_HOST'], '.local'));

        if ($is_pendamping && !$is_utama) {
            // ─── PROMPT DOSEN PENDAMPING: fokus proof reading, sitasi, antiplagiasi ───
            $prompt = "Kamu adalah **{$nama_peran}** di Politeknik WBI. Tugasmu adalah **Proof Reading per Bab** pada naskah TA — BUKAN menilai substansi penelitian.

**TA:** {$prodi} | {$nm_pd} | {$judul_ta} | **{$bab_label} – {$bab_title}**

**ABAIKAN:** kode ADDIN CSL_CITATION, masalah spasi PDF, hilangnya italics, kesalahan format rumus/superscript. Itu artefak sistem, bukan kesalahan mahasiswa.

---

## Instruksi Evaluasi

**1. Tata Tulis & Ejaan**
- Kumpulkan SEMUA typo/ejaan salah dalam satu daftar bullet ringkas: *[salah]* → [benar] (keterangan).
- Periksa konsistensi istilah dan penomoran sub-bab di bab ini.
- Kaidah PUEBI: 'Faktor-Faktor' di awal kalimat/judul, 'faktor-faktor' di tengah kalimat.

**2. Sitasi**
- Tandai paragraf/klaim tanpa sitasi.
- Cek konsistensi format sitasi (APA?).

**3. Risiko Plagiasi**
- Deteksi blok copy-paste (terlalu formal, tanpa parafrase, tanpa sitasi).
- Penilaian: Rendah / Sedang / Tinggi.

---

## Format Output WAJIB

### 📑 Ringkasan {$bab_label}
*2–3 kalimat: isi bab dan kesan kualitas tulisan.*

### 🔍 Temuan Evaluasi

**Bahasa & Tata Tulis**
- 🔴/🟡/🟢 [temuan] (Sub-bab: X) — *[teks asli]* → Saran: [koreksi]
- (kumpulkan semua typo di bagian ini, WAJIB sebut sub-bab)

**Struktur & Formatting**
- 🔴/🟡/🟢 [temuan] (Sub-bab: X) → Saran: [...]

**Sitasi & Daftar Pustaka**
- 🔴/🟡/🟢 [temuan] (Sub-bab: X) → Saran: [...]
- Jika aman: *Tidak ditemukan masalah sitasi signifikan.*

**Risiko Plagiasi**
- 🔴/🟡/🟢 Tingkat: Rendah/Sedang/Tinggi — [alasan singkat]

### ✅ Kesimpulan & Catatan untuk Mahasiswa
*Maks 5 poin singkat.*
1. ...

---
Langsung ke Ringkasan tanpa basa-basi. Markdown only. Output ringkas dan padat.
**PENTING untuk Saran:** JANGAN mengulangi atau menyebutkan daftar sub-bab/konten dokumen secara panjang dalam saran. Berikan arahan koreksi singkat dengan maks 1–2 contoh saja, bukan daftar lengkap.
**PENTING untuk Temuan:** JANGAN menampilkan temuan typo/ejaan yang PERSIS SAMA lebih dari 2 kali. Jika kesalahan yang sama muncul berulang kali, cukup tuliskan satu kali saja dan tambahkan keterangan '(ditemukan berulang kali)'.
**PENTING untuk Lokasi:** WAJIB sebutkan di sub-bab mana temuan tersebut berada pada setiap poin evaluasi dengan format (Sub-bab: X).";

            $system_prompt = $prompt . "\n\nAnda adalah AI Proof Reader. Format Markdown sesuai instruksi. Output ringkas dan padat.";

        } else {
            // ─── PROMPT DOSEN UTAMA / KOMBINASI: fokus substansi + chaining ───
            $prompt = "# Identitas & Peran

Kamu adalah **{$nama_peran}** di Politeknik WBI yang sedang melakukan **Review Naskah Tugas Akhir Per Bab** secara sistematik dan berkesinambungan.

**Panduan Peranmu:** {$instruksi_peran}

PENTING:
1. Abaikan kode-kode aneh hasil ekstraksi teks seperti 'ADDIN CSL_CITATION', tag HTML, atau angka acak tanpa konteks. Itu BUKAN kesalahan ketik mahasiswa, melainkan artefak sistem (misal dari Mendeley/Zotero). JANGAN masukkan hal tersebut ke dalam temuan evaluasi Anda.
2. Saat mengevaluasi referensi, ingatlah bahwa TAHUN SAAT INI ADALAH " . date('Y') . ". Jika kutipan berada di tahun " . date('Y') . " atau sebelumnya, maka referensi tersebut adalah logis dan sah.
3. Abaikan kesalahan format pada penulisan rumus matematika, persamaan statistik, *superscript*, atau *subscript* (misal: Z^2 terbaca Z2, hilangnya pemisah desimal, dsb). Kesalahan ini murni terjadi karena limitasi ekstraksi teks dari naskah asli (Word/PDF) ke sistem, BUKAN kesalahan ketik mahasiswa. Jangan jadikan ini sebagai bahan evaluasi.
4. Abaikan kesalahan minor terkait spasi, seperti spasi berlebih sebelum tanda baca (contoh: 'kata .', 'kalimat ,') atau masalah spasi pada URL. Seringkali spasi yang bergeser atau hilang ini terjadi akibat distorsi saat ekstraksi dari dokumen PDF ke teks, BUKAN kesalahan ketik mahasiswa. Fokuslah pada substansi kalimat dan ejaan kata.
5. Abaikan kurangnya penggunaan huruf miring (*italics*) pada istilah asing, nama aplikasi, nama *brand*, atau jargon modern (contoh: 'binge-shopping', 'blind box', 'e-commerce', 'skincare'). Format *italics* seringkali hilang selama proses ekstraksi teks PDF, sehingga ketiadaannya BUKAN kesalahan penulisan mahasiswa. Jangan jadikan hal ini sebagai temuan tata tulis.
6. Terkait penulisan kata ulang utuh (seperti 'faktor-faktor'), pastikan mengikuti kaidah PUEBI: Jika berada di **awal kalimat** atau di dalam **Judul**, KEDUA unsur kata harus diawali huruf kapital (contoh benar: 'Faktor-Faktor', 'Undang-Undang'). Jika berada di tengah kalimat (bukan nama diri/judul), gunakan huruf kecil semua (contoh: 'faktor-faktor'). Tegur jika mahasiswa menuliskannya 'Faktor-faktor' di awal kalimat/judul.
7. Tabel 'Informasi Tugas Akhir' di bawah ini (yang berisi Judul TA, Nama, Prodi) HANYA berfungsi sebagai **konteks** bagi Anda. JANGAN mengevaluasi atau mencari kesalahan EYD/tata tulis pada teks di dalam tabel metadata tersebut (misalnya, JANGAN mengoreksi penulisan judul TA dari tabel metadata). Fokuslah 100% mengevaluasi isi teks naskah bab yang sedang di-review.

---

## Informasi Tugas Akhir

| Atribut | Nilai |
|---|---|
| Program Studi | {$prodi} |
| Nama Mahasiswa | {$nm_pd} |
| Judul Tugas Akhir | {$judul_ta} |
| Bab yang Direview | **{$bab_label} – {$bab_title}** |
{$context_sebelumnya}
---

## Fokus Review Bab Ini

{$fokus_bab}

---

## Instruksi Evaluasi Umum

Selain fokus utama di atas, evaluasi juga:

1. **Tata Bahasa & EYD** — Deteksi ejaan keliru, tanda baca tidak tepat, dan kata tidak baku. Kutip kalimat bermasalah dan berikan koreksinya.
2. **Kesinambungan Logika** — Cek koherensi antar paragraf, konsistensi variabel/istilah, dan alur argumen.
3. **Struktur & Formatting** — Evaluasi kelengkapan struktur sub-bab, penomoran, dan tata letak.
4. **Substansi Penelitian** — Kedalaman analisis dan relevansi konten dengan topik penelitian.
5. **Lokasi Temuan (Sub-bab)** — WAJIB sebutkan di sub-bab atau bagian mana temuan tersebut berada berdasarkan struktur naskah (contoh: Sub-bab 1.2, atau Latar Belakang). Sertakan posisi ini di setiap temuan dengan format (Bagian: X).

---

## Sistem Penandaan Tingkat Keparahan

- 🔴 **KRITIS** — Harus diperbaiki sebelum dapat lanjut.
- 🟡 **PERHATIAN** — Perlu diperbaiki, namun proses dapat dilanjutkan.
- 🟢 **SARAN** — Rekomendasi peningkatan kualitas.

---

## Format Output Wajib

Gunakan PERSIS struktur berikut:

### 📑 Ringkasan {$bab_label}
*Deskripsikan isi bab ini dalam 2–4 kalimat. Sebutkan poin-poin utama yang dibahas.*

### 🔍 Temuan Evaluasi

**Bahasa & Tata Tulis**
- 🔴/🟡/🟢 [Temuan] — *[Kutip kalimat bermasalah]* (Bagian: X) → Saran: [koreksi/saran]

**Struktur & Formatting**
- 🔴/🟡/🟢 [Temuan] (Bagian: X) → Saran: [koreksi/saran]

**Substansi & Logika Penelitian**
- 🔴/🟡/🟢 [Temuan] (Bagian: X) → Saran: [koreksi/saran]

" . (!empty($previous_summaries) ? "**Kesinambungan dengan Bab Sebelumnya**
- 🔴/🟡/🟢 [Apakah bab ini konsisten dan berkesinambungan dengan bab sebelumnya?]
" : "") . "
### ✅ Kesimpulan Review {$bab_label}
*Berikan poin-poin singkat (maks 5 poin) yang WAJIB ditindaklanjuti mahasiswa untuk bab ini.*
1. ...
2. ...

### 🌟 Sintesis *Researcher* Internasional
*Berperanlah sebagai Researcher Kelas Dunia yang kritis namun suportif. Berikan satu paragraf kesimpulan 'super' (high-level synthesis) yang tajam dalam mengevaluasi benang merah bab ini terhadap desain penelitian keseluruhan. JANGAN terlalu memuji atau bersikap terlalu positif. Soroti secara spesifik potensi celah (blind spots), inkonsistensi, atau kelemahan argumentasi antara Latar Belakang, Tujuan, Referensi, dan Metode Penelitian. Berikan perspektif analitis yang menantang mahasiswa untuk memperkokoh fondasi risetnya.*

### 📌 Ringkasan untuk Chaining Review
*Buat ringkasan objektif 2–3 kalimat tentang isi dan kondisi {$bab_label} ini. Ringkasan ini akan digunakan sebagai konteks untuk review bab berikutnya agar review bersifat berkelanjutan.*

---

## Aturan Tambahan

- JANGAN berikan salam pembuka atau narasi basa-basi. Langsung masuk ke **Ringkasan {$bab_label}**.
- Gunakan format **Markdown**.
- JANGAN gunakan format LaTeX/Math.
- Berikan ulasan yang MENDETAIL dan KOMPREHENSIF, namun PASTIKAN semua bagian format output selesai ditulis termasuk '### 📌 Ringkasan untuk Chaining Review'.
- Jangan menulis lebih dari yang diperlukan — fokus pada temuan yang relevan dan bermakna.

---";

            $system_prompt = $prompt . "\n\nAnda adalah AI Reviewer Akademik. Berikan balasan dalam format Markdown sesuai instruksi. Pastikan seluruh bagian output termasuk '### 📌 Ringkasan untuk Chaining Review' selesai ditulis.";
        }

        $this->load->library('Ai_reviewer');

        $user_document = "Berikut adalah isi **{$bab_label} – {$bab_title}** yang harus direview:\n\n" . $content;

        $result = $this->ai_reviewer->generate_review($ai_provider, $api_key, $system_prompt, $user_document, $is_local, false);

        if (!$result['status']) {
            echo json_encode(['status' => false, 'msg' => $result['msg']]);
            return;
        }

        $ai_review = $result['review'];

        if (empty($ai_review)) {
            echo json_encode(['status' => false, 'msg' => 'Respon AI tidak sesuai format atau kosong.']);
            return;
        }

        // 10. Ekstrak ringkasan untuk chaining dari output AI
        $summary_for_chain = '';
        if (preg_match('/### 📌 Ringkasan untuk Chaining Review\s*\n(.*?)(?=\n---|\n###|$)/si', $ai_review, $m_summary)) {
            $summary_for_chain = trim($m_summary[1]);
        } else {
            // Fallback: ambil 3 kalimat pertama dari ringkasan bab
            if (preg_match('/### 📋 Ringkasan ' . preg_quote($bab_label, '/') . '\s*\n(.*?)(?=\n###|$)/si', $ai_review, $m_sum2)) {
                $sentences = preg_split('/(?<=[.!?])\s+/', trim(strip_tags($m_sum2[1])), 4);
                $summary_for_chain = implode(' ', array_slice($sentences, 0, 3));
            } else {
                $summary_for_chain = "Review {$bab_label} ({$bab_title}) telah selesai.";
            }
        }

        // 11. Simpan ke database
        $this->db->where('id', $id_chunk)
            ->update('siakad_skripsi_chunk_files', [
                'ai_review_result'  => $ai_review,
                'ai_review_summary' => $summary_for_chain,
                'ai_reviewed_at'    => date('Y-m-d H:i:s'),
            ]);

        echo json_encode([
            'status'  => true,
            'msg'     => "Review {$bab_label} berhasil digenerate.",
            'review'  => $ai_review,
            'summary' => $summary_for_chain,
            'cached'  => false,
        ]);
    }

    public function ajax_generate_ai_strategic_plan()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        if (empty($id_ptk)) {
            echo json_encode(['status' => false, 'msg' => 'Unauthorized']);
            return;
        }

        // Get all students where this lecturer is dosen_pembimbing_1
        $data_mahasiswa = $this->E_skripsi_bimbingan_model->get_all_bimbingan_dosen($id_ptk);
        $mhs_utama = [];

        foreach ($data_mahasiswa as $m) {
            if ($m->dosen_pembimbing_1 == $id_ptk && (!isset($m->hasil_sidang) || $m->hasil_sidang != 'LULUS')) {
                $umur_judul = '-';
                $days = 0;
                if (!empty($m->tanggal_disetujui)) {
                    $diff = time() - strtotime($m->tanggal_disetujui);
                    $days = floor($diff / (60 * 60 * 24));
                    if ($days < 0) {
                        $umur_judul = "Baru disetujui";
                    } else {
                        $months = floor($days / 30);
                        $umur_judul = $months . " Bulan";
                    }
                }

                $status_tambahan = [];
                if (isset($m->hasil_sempro) && $m->hasil_sempro != 'belum_ada') {
                    $status_tambahan[] = "Sudah Sempro";
                }
                if (isset($m->hasil_semhas) && $m->hasil_semhas != 'belum_ada') {
                    $status_tambahan[] = "Sudah Semhas";
                }
                $tahap_lanjut = empty($status_tambahan) ? "Belum Sempro/Semhas" : implode(', ', $status_tambahan);

                $angkatan = !empty($m->mulai_smt) ? substr($m->mulai_smt, 0, 4) : '';
                if (empty($angkatan) && !empty($m->nipd)) {
                    $angkatan = '20' . substr($m->nipd, 0, 2);
                }
                if (empty($angkatan))
                    $angkatan = '-';

                $mhs_utama[] = [
                    'nama' => $m->nm_pd,
                    'nim' => $m->nipd,
                    'angkatan' => $angkatan,
                    'umur_ta_hari' => max(0, $days),
                    'umur_ta_label' => $umur_judul,
                    'status_health' => $m->kategori_health,
                    'total_bimbingan' => $m->total_bimbingan,
                    'tahap_lanjut' => $tahap_lanjut
                ];
            }
        }

        if (empty($mhs_utama)) {
            echo json_encode(['status' => false, 'msg' => 'Tidak ada mahasiswa bimbingan aktif yang mana Anda bertindak sebagai Dosen Utama.']);
            return;
        }

        // Prepare document text
        $tahun_sekarang = date('Y');
        $doc_text = "Data Mahasiswa Bimbingan Aktif (Anda sebagai Dosen Utama) di Tahun {$tahun_sekarang}:\n";
        foreach ($mhs_utama as $m) {
            $doc_text .= "- Nama: {$m['nama']} ({$m['nim']}), Angkatan: {$m['angkatan']}, Umur TA: {$m['umur_ta_label']} ({$m['umur_ta_hari']} hari), Status: {$m['status_health']}, Total Bimbingan: {$m['total_bimbingan']}x, Tahapan: {$m['tahap_lanjut']}\n";
        }

        // Check if there is a previous month plan
        $prev_month = date('Y-m', strtotime('-1 month'));
        $prev_plan = $this->db->where('id_ptk', $id_ptk)->where('bulan_tahun', $prev_month)->get('siakad_skripsi_ai_strategic_plan')->row();

        if ($prev_plan) {
            $doc_text .= "\n--- DATA BULAN LALU ---\n";
            $doc_text .= "Kondisi Mahasiswa Bulan Lalu:\n{$prev_plan->review_kondisi}\n";
            $doc_text .= "\nStrategic Plan Bulan Lalu:\n{$prev_plan->strategi_percepatan}\n";
            $doc_text .= "-----------------------\n";
        }

        $prompt = "Kamu adalah Konsultan Akademik AI di Politeknik WBI yang membantu Dosen Utama mengelola mahasiswa bimbingan Tugas Akhir (TA).\n";
        $prompt .= "Tugasmu adalah menganalisis data mahasiswa bimbingan yang diberikan dan menyusun 'Strategic Plan' bulanan.\n";
        $prompt .= "Fokus Utama: Berikan strategi untuk mendorong dan mempercepat proses penyelesaian tugas akhir bagi mahasiswa dengan Umur TA < 6 bulan (kurang dari 180 hari).\n";
        $prompt .= "Fokus Tambahan: Berikan juga strategi khusus untuk mahasiswa dengan Umur TA > 1 tahun (lebih dari 365 hari), terutama bagi mereka yang sudah mencapai tahap Sempro or Semhas agar tidak menunda dan segera mendaftar Sidang.\n\n";
        $prompt .= "ATURAN PENTING: JANGAN gunakan istilah 'Pemanggilan Paksa', gunakan istilah 'Pemanggilan Resmi'. Untuk pemantauan progres, rekomendasikan penggunaan instrumen yang sudah ada (Bimbingan via Siakad atau Kartu Kontrol Manual), JANGAN merekomendasikan kontrol digital pihak ketiga (seperti Google Sheets atau Trello).\n\n";
        $prompt .= "PENTING: Di setiap poin rekomendasi strategi pada 'Strategi Plan' (kolom 2), Anda wajib mencantumkan nama-nama mahasiswa yang relevan dengan kasus tersebut secara spesifik di akhir kalimat tindakan (misalnya: '... dalam waktu maksimal 2 minggu (Mahasiswa: Nama1, Nama2)'). Jangan biarkan rekomendasi bersifat umum tanpa menyebutkan nama mahasiswanya.\n\n";
        $prompt .= "Berikan output dalam JSON KETAT sesuai format berikut — TANPA teks di luar JSON. PASTIKAN SELURUH NEWLINE DI DALAM VALUE JSON DI-ESCAPE DENGAN BENAR (gunakan \\n, jangan gunakan literal newline atau enter langsung di dalam string value).\n\n";
        $prompt .= "OUTPUT FORMAT (JSON):\n";
        $prompt .= "{\n";
        $prompt .= "  \"review_kondisi\": \"Satu blok teks analisis kondisi mahasiswa bimbingan dalam format HTML murni. Gunakan tag HTML seperti <p>, <strong>, dsb. Di awal teks, sebutkan prospek mahasiswa yang diprediksi lulus tepat waktu (cek Tahun Angkatan vs Tahun Saat Ini) dan imbau dosen untuk memprioritaskan mereka. DILARANG menggunakan karakter Markdown seperti *, **, ***, atau #.\",\n";
        $prompt .= "  \"evaluasi_progres\": \"Satu blok teks evaluasi progres dibandingkan bulan lalu atau baseline awal dalam format HTML murni. Gunakan tag HTML seperti <p>, <strong>, <ul>, <li>. DILARANG menggunakan karakter Markdown seperti *, **, ***, atau #.\",\n";
        $prompt .= "  \"strategi_percepatan\": \"Rekomendasi rencana strategis wajib berupa tabel dalam format HTML murni (menggunakan tag <table>, <thead>, <tbody>, <tr>, <th>, <td>). Kolom 1 harus bernama 'Kelompok Mahasiswa' (misalnya: Mahasiswa Baru < 6 Bulan, Mahasiswa Kritis > 1 Tahun, dsb), Kolom 2 harus bernama 'Strategi Plan' (tindakan percepatan konkrit dan instruksi detail). Di dalam kolom 2, gunakan tag HTML seperti <ul>, <li>, <strong> untuk memformat teks. Pastikan mencantumkan nama mahasiswa yang bersangkutan di akhir setiap kalimat tindakan strategi. DILARANG menggunakan karakter Markdown seperti *, **, ***, atau # di seluruh isi tabel.\"\n";
        $prompt .= "}\n";

        $this->load->library('Ai_reviewer');
        $this->load->model('Setting_api_key_model');
        $api_key_row = $this->Setting_api_key_model->get_api_key($id_ptk);

        if ($api_key_row && !empty($api_key_row->api_key)) {
            $provider = $api_key_row->ai_provider ?: 'gemini';
            $api_key = $api_key_row->api_key;
        } else {
            $settings = $this->db->get('setting')->row();
            $provider = $settings->ai_provider ?? 'gemini';
            $api_key = $settings->ai_api_key ?? '';
        }

        if (empty($api_key)) {
            echo json_encode(['status' => false, 'msg' => 'API Key belum dikonfigurasi.']);
            return;
        }

        $res = $this->ai_reviewer->generate_review($provider, $api_key, $prompt, $doc_text, false, true);

        if ($res['status']) {
            $json_str = trim($res['review']);
            // Extract JSON block if wrapped in markdown, using greedy match to avoid stopping at internal markdown code blocks
            if (preg_match('/```json(.*)```/is', $json_str, $matches)) {
                $json_str = trim($matches[1]);
            } elseif (preg_match('/```(.*)```/is', $json_str, $matches)) {
                $json_str = trim($matches[1]);
            }
            $data = json_decode($json_str, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['strategi_percepatan'])) {
                // Save to DB
                $bulan_tahun = date('Y-m');
                $insert_data = [
                    'id_ptk' => $id_ptk,
                    'bulan_tahun' => $bulan_tahun,
                    'review_kondisi' => $data['review_kondisi'],
                    'strategi_percepatan' => $data['strategi_percepatan'],
                    'evaluasi_progres' => $data['evaluasi_progres'],
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->db->where('id_ptk', $id_ptk)->where('bulan_tahun', $bulan_tahun)->delete('siakad_skripsi_ai_strategic_plan');
                $this->db->insert('siakad_skripsi_ai_strategic_plan', $insert_data);

                echo json_encode(['status' => true, 'data' => $insert_data]);
            } else {
                echo json_encode(['status' => false, 'msg' => 'AI tidak merespons dalam format JSON yang valid.', 'raw' => $res['review']]);
            }
        } else {
            echo json_encode($res);
        }
    }

    /**
     * Endpoint untuk toggle (menyimpan status) Share Review AI ke Mahasiswa
     */
    public function ajax_toggle_share_review()
    {
        $id_log = $this->input->post('id_log', true);
        $is_visible = $this->input->post('is_visible', true);

        if (!$id_log) {
            echo json_encode(['status' => false, 'msg' => 'ID Log tidak valid.']);
            return;
        }

        $this->db->where('id_log', $id_log);
        $this->db->update('siakad_skripsi_bimbingan_log', [
            'is_ai_review_visible' => ($is_visible == '1' || $is_visible == 'true') ? 1 : 0
        ]);

        if ($this->db->affected_rows() >= 0) {
            echo json_encode(['status' => true, 'msg' => 'Status share berhasil disimpan.']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal mengubah status di database.']);
        }
    }

    /**
     * Endpoint untuk menyimpan hasil edit teks review AI oleh Dosen
     */
    public function ajax_save_ai_review()
    {
        $id_chunk = (int) $this->input->post('id_chunk');
        $edited_text = $this->input->post('edited_text');
        $id_ptk_auth = $this->session->userdata('id_ptk');

        if (!$id_chunk || !$id_ptk_auth) {
            echo json_encode(['status' => false, 'msg' => 'Akses ditolak atau parameter tidak valid.']);
            return;
        }

        // Verifikasi bahwa chunk ini benar milik dosen ybs
        $chunk = $this->db->get_where('siakad_skripsi_chunk_files', ['id' => $id_chunk])->row();
        if (!$chunk) {
            echo json_encode(['status' => false, 'msg' => 'Data bab tidak ditemukan.']);
            return;
        }

        $auth_check = $this->db->get_where('siakad_skripsi_pengajuan_judul', [
            'id_judul_skripsi' => $chunk->id_judul_skripsi
        ])->row();

        if (!$auth_check || ($auth_check->dosen_pembimbing_1 != $id_ptk_auth && $auth_check->dosen_pembimbing_2 != $id_ptk_auth)) {
            echo json_encode(['status' => false, 'msg' => 'Anda tidak berhak mengedit review untuk mahasiswa ini.']);
            return;
        }

        // Generate ringkasan ulang jika perlu (atau biarkan kosong/lama)
        // Untuk sederhananya, ringkasan tidak di-regenerate saat diedit manual.
        
        $this->db->where('id', $id_chunk);
        $this->db->update('siakad_skripsi_chunk_files', [
            'ai_review_result' => $edited_text
        ]);

        if ($this->db->affected_rows() >= 0) {
            echo json_encode(['status' => true, 'msg' => 'Perubahan review berhasil disimpan.']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Gagal menyimpan ke database.']);
        }
    }

}
