<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_meet extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meet_model');
        date_default_timezone_set("Asia/Jakarta");

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $group = [2, 15];
        if (!$this->ion_auth->in_group($group)) {
            redirect('auth/logout', 'refresh');
        }
        
        header("Content-Security-Policy: frame-ancestors https://wbi.ac.id");

    }

    public function index($uniq)
    {
        $curr_meet = $this->db->where(compact('uniq'))->get('siakad_rencana_pertemuan')->row();
        if (!$curr_meet) {
            show_404("Pertemuan tidak ditemukan.");
            return;
        }

        $id_jadwal = $curr_meet->id_jadwal;

        if (SELF::authorization($id_jadwal)) {
            $jadwal = $this->db->where('id', $id_jadwal)->from('siakad_jadwal j')->join('siakad_hari h', 'h.uniq = j.id_hari', 'left')->get()->row();
            $meet = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->result();
            $jadwal_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_jadwal_detail')->row();

            $nm_mk = $jadwal_detail ? trim((string)$jadwal_detail->nm_mk) : trim((string)$jadwal->tag_name);
            $kode_mk = '';
            if ($jadwal_detail && !empty($jadwal_detail->id_kls)) {
                $kelas = $this->db->where('id_kls', $jadwal_detail->id_kls)->get('feeder_kelas_kuliah')->row();
                if ($kelas) {
                    $kode_mk = $kelas->kode_mk_1 ?? '';
                    if (!$nm_mk && !empty($kelas->nm_mk_1)) $nm_mk = $kelas->nm_mk_1;
                }
            }

            // Lookup RPS OBE
            $this->load->model('Rps_obe_model');
            $rps_obe = null;
            if (!empty($kode_mk)) {
                $rps_obe = $this->db->where(['kode_mk' => $kode_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
            }
            if (!$rps_obe && !empty($nm_mk)) {
                $rps_obe = $this->db->where(['nm_mk' => $nm_mk])->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
            }

            $details = $rps_obe ? $this->Rps_obe_model->get_details($rps_obe->id_rps) : [];
            $skenario_md = $rps_obe ? ($rps_obe->skenario_pembelajaran_markdown ?: $rps_obe->raw_ai_output) : '';
            
            // Parse Skenario
            $parsed = [];
            if (!empty($skenario_md)) {
                $parts = preg_split('/(?=###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*\d+)/i', $skenario_md);
                foreach ($parts as $part) {
                    if (preg_match('/###\s*(?:📌\s*)?PERTEMUAN\s+(?:MINGGU\s+KE-|KE-)?\s*(\d+)(?:\s*[:\-]\s*([^\n]+))?/i', $part, $m_num)) {
                        $num = (int)$m_num[1];
                        $topik = !empty($m_num[2]) ? trim($m_num[2]) : '';
                        $sub_cpmk = '';
                        if (preg_match('/(?:\*|\-)?\s*\*\*Kemampuan Akhir[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) $sub_cpmk = trim($m[1]);
                        $metode = '';
                        if (preg_match('/(?:\*|\-)?\s*\*\*Bentuk & Metode[^\*]*\*\*:\s*([^\n]+)/i', $part, $m)) $metode = trim($m[1]);
                        $skenario_scl = '';
                        if (preg_match('/####\s*(?:🎯\s*)?(?:Skenario Langkah Pembelajaran|Skenario Strategi Pembelajaran|Skenario Strategi Pembelajaran \(SCL\)|Sintaks SCL|Skenario Langkah Pelaksanaan)[^\n]*\n([\s\S]*?)(?=(?:####|---|```text|\Z))/i', $part, $m)) {
                            $skenario_scl = trim($m[1]);
                        }
                        $draf_bap = '';
                        if (preg_match('/####\s*(?:📝\s*)?[^\n]*(?:Draft Evaluasi|Draf Evaluasi|Draf Realisasi Berita Acara|Draf BAP|BAP Faktual)[^\n]*\n(?:```(?:text|markdown)?\s*)?([\s\S]*?)(?:```|---|###|\Z)/i', $part, $m)) {
                            $draf_bap = trim(str_replace(['```text', '```'], '', $m[1]));
                        }
                        $parsed[$num] = [
                            'topik'        => $topik,
                            'sub_cpmk'     => $sub_cpmk,
                            'metode'       => $metode,
                            'skenario_scl' => $skenario_scl,
                            'draf_bap'     => $draf_bap
                        ];
                    }
                }
            }

            $meet_no = (int)$curr_meet->pertemuan;
            $topik_pertemuan = !empty($parsed[$meet_no]['topik']) ? $parsed[$meet_no]['topik'] : (!empty($curr_meet->bacaan_materi) ? $curr_meet->bacaan_materi : $curr_meet->berita_acara_rencana);
            $topik_pertemuan = strip_tags(str_replace(['<br />', '<br>', "\n"], ' ', $topik_pertemuan));

            $sub_cpmk = !empty($curr_meet->sub_cpmk) ? $curr_meet->sub_cpmk : ($parsed[$meet_no]['sub_cpmk'] ?? '-');
            $metode_pembelajaran = !empty($curr_meet->metode_pembelajaran) ? $curr_meet->metode_pembelajaran : ($parsed[$meet_no]['metode'] ?? 'Kuliah Interaktif & Diskusi Kelompok');
            $skenario_scl = !empty($parsed[$meet_no]['skenario_scl']) ? $parsed[$meet_no]['skenario_scl'] : (!empty($curr_meet->berita_acara_rencana) ? $curr_meet->berita_acara_rencana : '');
            $draf_bap = !empty($curr_meet->draf_bap) ? $curr_meet->draf_bap : ($parsed[$meet_no]['draf_bap'] ?? '');

            $menit_t = round(((float)($curr_meet->jam_teori ?: 1)) * 50);
            $menit_p = round(((float)($curr_meet->jam_praktek ?: 2)) * 50);

            $output['judul_besar']         = $jadwal->tag_name;
            $output['judul_kecil']         = ($jadwal->nama_hari ? $jadwal->nama_hari . ', ' : '') . substr($jadwal->jam_mulai,0,5) . ' sd ' . substr($jadwal->jam_selesai,0,5);
            $output['meet']                = $meet;
            $output['curr_meet']           = $curr_meet;
            $output['topik_pertemuan']     = $topik_pertemuan;
            $output['sub_cpmk']            = $sub_cpmk;
            $output['metode_pembelajaran'] = $metode_pembelajaran;
            $output['skenario_scl']        = $skenario_scl;
            $output['draf_bap']            = $draf_bap;
            $output['menit_t']             = $menit_t;
            $output['menit_p']             = $menit_p;
            $output['id_jadwal']           = $id_jadwal;
            $output['content']             = 'meet/meet_view';
            $output['after_page']          = 'meet/meet_after_page';
            $output['judul']               = 'Pertemuan ' . $curr_meet->pertemuan . ' - ' . $jadwal->tag_name;
            $output['course_id']           = $uniq;
            $output['is_generate_bap']     = $jadwal->is_generate_bap;

            $this->load->view('master_page_elite', $output);
        } else {
            echo "Anda tidak berhak mengakses kelas ini.";
        }
    }

    private function authorization($id_jadwal)
    {
        $id_logged_in_user = $this->ion_auth->user()->row();
        $id_ptk_user = $id_logged_in_user->id_ptk;

        $jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

        if ($jadwal->id_sdm === $id_ptk_user) {
            return true;
        } else {
            return false;
        }
    }

    // Ajax Portion
    public function ajax_get_video_details()
    {
        $this->load->model('Video_model');
        $video_details = $this->Video_model->getVideoDetails($_POST['video_url']);
        // $video_details = $this->Video_model->getVideoDetails('https://youtu.be/Kl6xuXKXZlw');
        echo $video_details['duration'];
    }

    public function lessons($course_id = "", $param1 = "", $param2 = "")
    {



        // CHECK ACCESS PERMISSION
        // check_permission('course');

        // if ($this->session->userdata('admin_login') != true) {
        //     redirect(site_url('login'), 'refresh');
        // }
        if ($param1 == 'add') {
            $this->Meet_model->add_lesson();
            $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_added_successfully'));
            redirect('C_meet/index/' . $course_id);
        } elseif ($param1 == 'edit') {
            $this->Meet_model->edit_lesson($param2);
            $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_updated_successfully'));
            redirect('C_meet/index/' . $course_id);
        } elseif ($param1 == 'delete') {
            $this->Meet_model->delete_lesson($param2);
            $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_deleted_successfully'));
            redirect('C_meet/index/' . $course_id);
        } elseif ($param1 == 'filter') {
            redirect('admin/lessons/' . $this->input->post('course_id'));
        }
        // $page_data['page_name'] = 'lessons';
        // $page_data['lessons'] = $this->Meet_model->get_lessons('course', $course_id);
        // $page_data['course_id'] = $course_id;
        // $page_data['page_title'] = get_phrase('lessons');
        // $this->load->view('backend/index', $page_data);
    }

    public function preview($course_id = '')
    {
        //$this->is_drafted_course($course_id);
        if ($course_id > 0) {
            $courses = $this->Meet_model->get_course_by_id($course_id);
            if ($courses->num_rows() > 0) {
                $course_details = $courses->row_array();
                redirect(site_url('C_meet/lesson/' . rawurlencode(slugify($course_details['berita_acara_rencana'])) . '/' . $course_details['uniq']), 'refresh');
            }
        }
        //redirect(site_url('admin/courses'), 'refresh');
    }

    public function course($slug = "", $course_id = "")
    {

        //$this->access_denied_courses($course_id);
        $page_data['course_id'] = $course_id;
        $page_data['page_name'] = "course_page";
        $page_data['page_title'] = site_phrase('course');


        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    private function is_drafted_course($course_id)
    {
        $course_details = $this->Meet_model->get_course_by_id($course_id)->row_array();
        if ($course_details['status'] == 'draft') {
            $this->session->set_flashdata('error_message', get_phrase('you_do_not_have_right_to_access_this_course'));
            redirect(site_url('admin/courses'), 'refresh');
        }
    }

    private function access_denied_courses($course_id)
    {
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        if ($course_details['status'] == 'draft' && $course_details['user_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error_message', site_phrase('you_do_not_have_permission_to_access_this_course'));
            redirect(site_url('home'), 'refresh');
        } elseif ($course_details['status'] == 'pending') {
            if ($course_details['user_id'] != $this->session->userdata('user_id') && $this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error_message', site_phrase('you_do_not_have_permission_to_access_this_course'));
                redirect(site_url('home'), 'refresh');
            }
        }
    }

    public function lesson($slug = "", $course_id = "", $lesson_id = "")
    {

        $course_details = $this->Meet_model->get_course_by_id($course_id)->row_array();
        $id_jadwal = $course_details['id_jadwal'];

        //this function saved current lesson id and return previous lesson id if $lesson_id param is empty
        $lesson_id = $this->Meet_model->update_watch_history($course_id, $lesson_id);
        // $id_jadwal = $this->session->userdata('id_kls_session');

        // if ($course_details['course_type'] == 'general') {

        $sections = $this->Meet_model->get_rencana($id_jadwal);


        if ($sections->num_rows() > 0) {
            $page_data['sections'] = $sections->result_array();
            if ($lesson_id == "") {
                $default_section = $sections->row_array();
                $page_data['section_id'] = $default_section['uniq'];
                $lessons = $this->Meet_model->get_lessons('section', $default_section['uniq']);
                if ($lessons->num_rows() > 0) {
                    $default_lesson = $lessons->row_array();
                    $lesson_id = $default_lesson['id'];
                    $page_data['lesson_id']  = $default_lesson['id'];
                }
            } else {
                $page_data['lesson_id']  = $lesson_id;
                $section_id = $this->db->get_where('lesson', ['id' => $lesson_id])->row()->section_id;
                $page_data['section_id'] = $section_id;
            }
        } else {
            $page_data['sections'] = [];
        }
        // } else if ($course_details['course_type'] == 'scorm') {
        //     $this->load->model('addons/scorm_model');
        //     $scorm_course_data = $this->scorm_model->get_scorm_curriculum_by_course_id($course_id);
        //     $page_data['scorm_curriculum'] = $scorm_course_data->row_array();
        // }

        // Check if the lesson contained course is purchased by the user
        // if (isset($page_data['lesson_id']) && $page_data['lesson_id'] > 0 && $course_details['course_type'] == 'general') {
        //     if ($this->session->userdata('role_id') != 1 && $course_details['user_id'] != $this->session->userdata('user_id')) {
        //         if (!is_purchased($course_id)) {
        //             redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
        //         }
        //     }
        // } else if ($course_details['course_type'] == 'scorm' && $scorm_course_data->num_rows() > 0) {
        //     if ($this->session->userdata('role_id') != 1 && $course_details['user_id'] != $this->session->userdata('user_id')) {
        //         if (!is_purchased($course_id)) {
        //             redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
        //         }
        //     }
        // } else {
        //     if (!is_purchased($course_id)) {
        //         redirect(site_url('home/course/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
        //     }
        // }


        $page_data['course_details']  = $course_details;
        $page_data['course_id']  = $course_id;
        $page_data['page_name']  = 'lessons';
        $page_data['page_title'] = $course_details['berita_acara_rencana'];
        $this->load->view('lessons/index', $page_data);
    }

    public function save_course_progress()
    {
        $response = $this->Meet_model->save_course_progress();
        echo $response;
    }
}
