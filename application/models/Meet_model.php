<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meet_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_section($type_by, $id)
    {
        $this->db->order_by("uniq", "asc");
        if ($type_by == 'course') {
            return $this->db->get_where('siakad_rencana_pertemuan', ['uniq' => $id]);
        } elseif ($type_by == 'section') {
            return $this->db->get_where('section', ['id' => $id]);
        }
    }

    public function get_rencana($id_jadwal)
    {

        return $this->db->get_where('siakad_rencana_pertemuan', ['id_jadwal' => $id_jadwal]);
    }

    public function get_lessons($type = "", $id = "")
    {
        $this->db->order_by("order", "asc");
        if ($type == "course") {
            return $this->db->get_where('lesson', ['course_id' => $id]);
        } elseif ($type == "section") {
            return $this->db->get_where('lesson', ['section_id' => $id]);
        } elseif ($type == "lesson") {
            return $this->db->get_where('lesson', ['id' => $id]);
        } else {
            return $this->db->get('lesson');
        }
    }

    public function get_course_by_id($course_id = "")
    {
        return $this->db->get_where('siakad_rencana_pertemuan', ['uniq' => $course_id]);
    }

    public function delete_lesson($lesson_id)
    {
        $this->db->where('id', $lesson_id);
        $this->db->delete('lesson');
    }

    public function edit_lesson($lesson_id)
    {
        $this->load->model('Video_model');
        $previous_data = $this->db->get_where('lesson', ['id' => $lesson_id])->row_array();

        // $data['course_id'] = html_escape($this->input->post('course_id'));
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));

        $lesson_type_array = explode('-', $this->input->post('lesson_type'));

        $lesson_type = $lesson_type_array[0];
        $data['lesson_type'] = $lesson_type;

        $attachment_type = $lesson_type_array[1];
        $data['attachment_type'] = $attachment_type;

        if ($lesson_type == 'video') {
            $lesson_provider = $this->input->post('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->input->post('video_url') == "" || $this->input->post('duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('video_url'));

                $duration_formatter = explode(':', $this->input->post('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->Video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('html5_video_url'));

                $duration_formatter = explode(':', $this->input->post('html5_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'html5';

                if ($_FILES['thumbnail']['name'] != "") {
                    if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                        mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
                    }
                    move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $lesson_id . '.jpg');
                }
            } elseif ($lesson_provider == 'google_drive') {
                if ($this->input->post('google_drive_video_url') == "" || $this->input->post('google_drive_video_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('google_drive_video_url'));
                $duration_formatter = explode(':', $this->input->post('google_drive_video_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'google_drive';
            } else {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }
            $data['attachment'] = "";

            // This portion is for mobile application video lessons
            if ($this->input->post('html5_video_url_for_mobile_application') == "" || $this->input->post('html5_duration_for_mobile_application') == "") {
                $mobile_app_lesson_url = "https://www.html5rocks.com/en/tutorials/video/basics/devstories.webm";
                $mobile_app_lesson_duration = "00:01:10";
            } else {
                $mobile_app_lesson_url = $this->input->post('html5_video_url_for_mobile_application');
                $mobile_app_lesson_duration = $this->input->post('html5_duration_for_mobile_application');
            }
            $duration_for_mobile_application_formatter = explode(':', $mobile_app_lesson_duration);
            $hour = sprintf('%02d', $duration_for_mobile_application_formatter[0]);
            $min  = sprintf('%02d', $duration_for_mobile_application_formatter[1]);
            $sec  = sprintf('%02d', $duration_for_mobile_application_formatter[2]);
            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = 'html5';
            $data['video_url_for_mobile_application'] = $mobile_app_lesson_url;
        } elseif ($lesson_type == "s3") {
            // SET MAXIMUM EXECUTION TIME 600
            ini_set('max_execution_time', '600');

            if (isset($_FILES['video_file_for_amazon_s3']) && !empty($_FILES['video_file_for_amazon_s3']['name'])) {
                $fileName           = $_FILES['video_file_for_amazon_s3']['name'];
                $tmp                = explode('.', $fileName);
                $fileExtension      = strtoupper(end($tmp));

                $video_extensions = ['WEBM', 'MP4'];
                if (!in_array($fileExtension, $video_extensions)) {
                    $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }

                $upload_loaction = get_settings('video_upload_location');
                $access_key = get_settings('amazon_s3_access_key');
                $secret_key = get_settings('amazon_s3_secret_key');
                $bucket = get_settings('amazon_s3_bucket_name');
                $region = get_settings('amazon_s3_region_name');

                $s3config = [
                    'region'  => $region,
                    'version' => 'latest',
                    'credentials' => [
                        'key'    => $access_key, //Put key here
                        'secret' => $secret_key // Put Secret here
                    ]
                ];


                $tmpfile = $_FILES['video_file_for_amazon_s3'];

                $s3 = new Aws\S3\S3Client($s3config);
                $key = str_replace(".", "-" . random_int(1, 9999) . ".", preg_replace('/\s+/', '', $tmpfile['name']));

                $result = $s3->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $key,
                    'SourceFile' => $tmpfile['tmp_name'],
                    'ACL'   => 'public-read'
                ]);

                $data['video_url'] = $result['ObjectURL'];
                $data['video_url_for_mobile_application'] = $result['ObjectURL'];
            }

            $data['video_type'] = 'amazon';
            $data['lesson_type'] = 'video';
            $data['attachment_type'] = 'file';


            if ($this->input->post('amazon_s3_duration') == "") {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_duration'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            $duration_formatter = explode(':', $this->input->post('amazon_s3_duration'));
            $hour = sprintf('%02d', $duration_formatter[0]);
            $min = sprintf('%02d', $duration_formatter[1]);
            $sec = sprintf('%02d', $duration_formatter[2]);
            $data['duration'] = $hour . ':' . $min . ':' . $sec;

            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = "html5";
        } elseif ($lesson_type == "system") {
            // SET MAXIMUM EXECUTION TIME 600
            ini_set('max_execution_time', '600');

            if (isset($_FILES['system_video_file']) && !empty($_FILES['system_video_file']['name'])) {
                //delete previews video
                $previews_video_url = $this->db->get_where('lesson', ['id' => $lesson_id])->row('video_url');
                $video_file = explode('/', $previews_video_url);
                unlink('uploads/lesson_files/videos/' . end($video_file));
                //end delete previews video

                $fileName           = $_FILES['system_video_file']['name'];

                // CHECKING IF THE FILE IS AVAILABLE AND FILE SIZE IS VALID
                if (array_key_exists('system_video_file', $_FILES)) {
                    if ($_FILES['system_video_file']['error'] !== UPLOAD_ERR_OK) {
                        $error_code = $_FILES['system_video_file']['error'];
                        $this->session->set_flashdata('error_message', phpFileUploadErrors($error_code));
                        redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                    }
                } else {
                    $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                };

                $tmp                = explode('.', $fileName);
                $fileExtension      = strtoupper(end($tmp));

                $video_extensions = ['WEBM', 'MP4'];
                if (!in_array($fileExtension, $video_extensions)) {
                    $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }

                // custom random name of the video file
                $uploadable_video_file    =  md5(uniqid(random_int(0, mt_getrandmax()), true)) . '.' . strtolower($fileExtension);


                $tmp_video_file = $_FILES['system_video_file']['tmp_name'];

                if (!file_exists('uploads/lesson_files/videos')) {
                    mkdir('uploads/lesson_files/videos', 0777, true);
                }
                $video_file_path = 'uploads/lesson_files/videos/' . $uploadable_video_file;
                move_uploaded_file($tmp_video_file, $video_file_path);

                $data['video_url'] = site_url($video_file_path);
                $data['video_url_for_mobile_application'] = site_url($video_file_path);
            }

            $data['video_type'] = 'system';
            $data['lesson_type'] = 'video';
            $data['attachment_type'] = 'file';


            if ($this->input->post('system_video_file_duration') == "") {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_duration'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            $duration_formatter = explode(':', $this->input->post('system_video_file_duration'));
            $hour = sprintf('%02d', $duration_formatter[0]);
            $min = sprintf('%02d', $duration_formatter[1]);
            $sec = sprintf('%02d', $duration_formatter[2]);
            $data['duration'] = $hour . ':' . $min . ':' . $sec;

            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = "html5";
        } elseif ($lesson_type == 'text' && $attachment_type == 'description') {
            $data['attachment'] = htmlspecialchars(remove_js($this->input->post('text_description', false)));
        } else {
            if ($attachment_type == 'iframe') {
                if (empty($this->input->post('iframe_source'))) {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_source'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['attachment'] = $this->input->post('iframe_source');
            } elseif ($attachment_type == 'gdrive_document') {
                if (empty($this->input->post('gdrive_source'))) {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_source'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['attachment'] = $this->input->post('gdrive_source');
            } else {
                if ($_FILES['attachment']['name'] != "") {
                    // unlinking previous attachments
                    if ($previous_data['attachment'] != "") {
                        unlink('uploads/lesson_files/' . $previous_data['attachment']);
                    }

                    $fileName           = $_FILES['attachment']['name'];
                    $tmp                = explode('.', $fileName);
                    $fileExtension      = end($tmp);
                    $uploadable_file    =  md5(uniqid(random_int(0, mt_getrandmax()), true)) . '.' . $fileExtension;
                    $data['attachment'] = $uploadable_file;
                    $data['video_type'] = "";
                    $data['duration'] = "";
                    $data['video_url'] = "";
                    $data['duration_for_mobile_application'] = "";
                    $data['video_type_for_mobile_application'] = '';
                    $data['video_url_for_mobile_application'] = "";
                    if (!file_exists('uploads/lesson_files')) {
                        mkdir('uploads/lesson_files', 0777, true);
                    }
                    move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
                }
            }
        }

        $data['last_modified'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = htmlspecialchars(remove_js($this->input->post('summary', false)));
        $data['is_free'] = htmlspecialchars($this->input->post('free_lesson'));

        $this->db->where('id', $lesson_id);
        $this->db->update('lesson', $this->security->xss_clean($data));
    }

    public function add_lesson()
    {
        $this->load->model('Video_model');
        $course_id_post = html_escape($this->input->post('course_id'));
        $id_jadwal = $this->db->where('uniq', $course_id_post)->get('siakad_rencana_pertemuan')->row(1);
        $data['course_id'] = $id_jadwal->id_jadwal;
        $data['title'] = html_escape($this->input->post('title'));
        $data['section_id'] = html_escape($this->input->post('section_id'));

        $lesson_type_array = explode('-', $this->input->post('lesson_type'));

        $lesson_type = $lesson_type_array[0];
        $data['lesson_type'] = $lesson_type;

        $attachment_type = $lesson_type_array[1];
        $data['attachment_type'] = $attachment_type;

        if ($lesson_type == 'video') {
            // This portion is for web application's video lesson
            $lesson_provider = $this->input->post('lesson_provider');
            if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
                if ($this->input->post('video_url') == "" || $this->input->post('duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('video_url'));

                $duration_formatter = explode(':', $this->input->post('duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;

                $video_details = $this->Video_model->getVideoDetails($data['video_url']);
                $data['video_type'] = $video_details['provider'];
            } elseif ($lesson_provider == 'html5') {
                if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('html5_video_url'));
                $duration_formatter = explode(':', $this->input->post('html5_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'html5';
            } elseif ($lesson_provider == 'google_drive') {
                if ($this->input->post('google_drive_video_url') == "" || $this->input->post('google_drive_video_duration') == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_url_and_duration'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['video_url'] = html_escape($this->input->post('google_drive_video_url'));
                $duration_formatter = explode(':', $this->input->post('google_drive_video_duration'));
                $hour = sprintf('%02d', $duration_formatter[0]);
                $min = sprintf('%02d', $duration_formatter[1]);
                $sec = sprintf('%02d', $duration_formatter[2]);
                $data['duration'] = $hour . ':' . $min . ':' . $sec;
                $data['video_type'] = 'google_drive';
            } else {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_provider'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            // This portion is for mobile application video lessons
            if ($this->input->post('html5_video_url_for_mobile_application') == "" || $this->input->post('html5_duration_for_mobile_application') == "") {
                $mobile_app_lesson_url = "https://www.html5rocks.com/en/tutorials/video/basics/devstories.webm";
                $mobile_app_lesson_duration = "00:01:10";
            } else {
                $mobile_app_lesson_url = $this->input->post('html5_video_url_for_mobile_application');
                $mobile_app_lesson_duration = $this->input->post('html5_duration_for_mobile_application');
            }
            $duration_for_mobile_application_formatter = explode(':', $mobile_app_lesson_duration);
            $hour = sprintf('%02d', $duration_for_mobile_application_formatter[0]);
            $min  = sprintf('%02d', $duration_for_mobile_application_formatter[1]);
            $sec  = sprintf('%02d', $duration_for_mobile_application_formatter[2]);
            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = 'html5';
            $data['video_url_for_mobile_application'] = $mobile_app_lesson_url;
        } elseif ($lesson_type == "s3") {
            // SET MAXIMUM EXECUTION TIME 600
            ini_set('max_execution_time', '600');

            $fileName           = $_FILES['video_file_for_amazon_s3']['name'];
            $tmp                = explode('.', $fileName);
            $fileExtension      = strtoupper(end($tmp));

            $video_extensions = ['WEBM', 'MP4'];
            if (!in_array($fileExtension, $video_extensions)) {
                $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            if ($this->input->post('amazon_s3_duration') == "") {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_duration'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            $upload_loaction = get_settings('video_upload_location');
            $access_key = get_settings('amazon_s3_access_key');
            $secret_key = get_settings('amazon_s3_secret_key');
            $bucket = get_settings('amazon_s3_bucket_name');
            $region = get_settings('amazon_s3_region_name');

            $s3config = [
                'region'  => $region,
                'version' => 'latest',
                'credentials' => [
                    'key'    => $access_key, //Put key here
                    'secret' => $secret_key // Put Secret here
                ]
            ];


            $tmpfile = $_FILES['video_file_for_amazon_s3'];

            $s3 = new Aws\S3\S3Client($s3config);
            $key = str_replace(".", "-" . random_int(1, 9999) . ".", $tmpfile['name']);

            $result = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $tmpfile['tmp_name'],
                'ACL'   => 'public-read'
            ]);

            $data['video_url'] = $result['ObjectURL'];
            $data['video_type'] = 'amazon';
            $data['lesson_type'] = 'video';
            $data['attachment_type'] = 'file';

            $duration_formatter = explode(':', $this->input->post('amazon_s3_duration'));
            $hour = sprintf('%02d', $duration_formatter[0]);
            $min = sprintf('%02d', $duration_formatter[1]);
            $sec = sprintf('%02d', $duration_formatter[2]);
            $data['duration'] = $hour . ':' . $min . ':' . $sec;

            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = "html5";
            $data['video_url_for_mobile_application'] = $result['ObjectURL'];
        } elseif ($lesson_type == "system") {
            // SET MAXIMUM EXECUTION TIME 600
            ini_set('max_execution_time', '600');

            $fileName           = $_FILES['system_video_file']['name'];

            // CHECKING IF THE FILE IS AVAILABLE AND FILE SIZE IS VALID
            if (array_key_exists('system_video_file', $_FILES)) {
                if ($_FILES['system_video_file']['error'] !== UPLOAD_ERR_OK) {
                    $error_code = $_FILES['system_video_file']['error'];
                    $this->session->set_flashdata('error_message', phpFileUploadErrors($error_code));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
            } else {
                $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            };

            $tmp                = explode('.', $fileName);
            $fileExtension      = strtoupper(end($tmp));

            $video_extensions = ['WEBM', 'MP4'];

            if (!in_array($fileExtension, $video_extensions)) {
                $this->session->set_flashdata('error_message', get_phrase('please_select_valid_video_file'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }

            // custom random name of the video file
            $uploadable_video_file    =  md5(uniqid(random_int(0, mt_getrandmax()), true)) . '.' . strtolower($fileExtension);

            if ($this->input->post('system_video_file_duration') == "") {
                $this->session->set_flashdata('error_message', get_phrase('invalid_lesson_duration'));
                redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
            }



            $tmp_video_file = $_FILES['system_video_file']['tmp_name'];

            if (!file_exists('uploads/lesson_files/videos')) {
                mkdir('uploads/lesson_files/videos', 0777, true);
            }
            $video_file_path = 'uploads/lesson_files/videos/' . $uploadable_video_file;
            move_uploaded_file($tmp_video_file, $video_file_path);
            $data['video_url'] = site_url($video_file_path);
            $data['video_type'] = 'system';
            $data['lesson_type'] = 'video';
            $data['attachment_type'] = 'file';

            $duration_formatter = explode(':', $this->input->post('system_video_file_duration'));
            $hour = sprintf('%02d', $duration_formatter[0]);
            $min = sprintf('%02d', $duration_formatter[1]);
            $sec = sprintf('%02d', $duration_formatter[2]);
            $data['duration'] = $hour . ':' . $min . ':' . $sec;

            $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
            $data['video_type_for_mobile_application'] = "html5";
            $data['video_url_for_mobile_application'] = site_url($video_file_path);
        } elseif ($lesson_type == 'text' && $attachment_type == 'description') {
            $data['attachment'] = htmlspecialchars(remove_js($this->input->post('text_description', false)));
        } else {
            if ($attachment_type == 'iframe') {
                if (empty($this->input->post('iframe_source'))) {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_source'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['attachment'] = $this->input->post('iframe_source');
            } elseif ($attachment_type == 'gdrive_document') {
                if (empty($this->input->post('gdrive_source'))) {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_source'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                }
                $data['attachment'] = $this->input->post('gdrive_source');
            } else {
                if ($_FILES['attachment']['name'] == "") {
                    $this->session->set_flashdata('error_message', get_phrase('invalid_attachment'));
                    redirect(site_url(strtolower($this->session->userdata('role')) . '/course_form/course_edit/' . $data['course_id']), 'refresh');
                } else {
                    $fileName           = $_FILES['attachment']['name'];
                    $tmp                = explode('.', $fileName);
                    $fileExtension      = end($tmp);
                    $uploadable_file    =  md5(uniqid(random_int(0, mt_getrandmax()), true)) . '.' . $fileExtension;
                    $data['attachment'] = $uploadable_file;

                    if (!file_exists('uploads/lesson_files')) {
                        mkdir('uploads/lesson_files', 0777, true);
                    }
                    move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
                }
            }
        }

        $data['date_added'] = strtotime(date('D, d-M-Y'));
        $data['summary'] = htmlspecialchars(remove_js($this->input->post('summary', false)));
        $data['is_free'] = htmlspecialchars($this->input->post('free_lesson'));


        $this->db->insert('lesson', $this->security->xss_clean($data));
        $inserted_id = $this->db->insert_id();

        if ($_FILES['thumbnail']['name'] != "") {
            if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
                mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
            }
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $inserted_id . '.jpg');
        }
    }

    public function update_watch_history($course_id = "", $lesson_id = "")
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $query = $this->db->get_where('watch_histories', ['course_id' => $course_id, 'student_id' => $user_id]);

        if ($course_id != "" && $lesson_id != "") {
            if ($query->num_rows() > 0) {
                $this->db->where('watch_history_id', $query->row('watch_history_id'));
                $this->db->update('watch_histories', ['watching_lesson_id' => $lesson_id, 'date_updated' => time()]);
            } else {
                $data['course_id'] = $course_id;
                $data['student_id'] = $user_id;
                $data['watching_lesson_id'] = $lesson_id;
                $data['date_added'] = time();
                $this->db->insert('watch_histories', $data);
            }
            return $lesson_id;
        } elseif ($query->num_rows() > 0) {
            return $query->row('watching_lesson_id');
        }
    }

    public function get_lesson_thumbnail_url($lesson_id)
    {

        if (file_exists('uploads/thumbnails/lesson_thumbnails/' . $lesson_id . '.jpg'))
            return base_url() . 'uploads/thumbnails/lesson_thumbnails/' . $lesson_id . '.jpg';
        else
            return base_url() . 'uploads/thumbnails/thumbnail.png';
    }

    public function save_course_progress()
    {
        $lesson_id = $this->input->post('lesson_id');
        $progress = $this->input->post('progress');
        // $user_id   = $this->session->userdata('user_id');
        $user_id = $this->ion_auth->user()->row()->id;
        $user_details  = $this->db->where('id', $user_id)->get('users')->row_array();
        $watch_history = $user_details['watch_history'];
        $watch_history_array = [];
        if ($watch_history == '') {
            array_push($watch_history_array, ['lesson_id' => $lesson_id, 'progress' => $progress]);
        } else {
            $founder = false;
            $watch_history_array = json_decode($watch_history, true);
            for ($i = 0; $i < count($watch_history_array); $i++) {
                $watch_history_for_each_lesson = $watch_history_array[$i];
                if ($watch_history_for_each_lesson['lesson_id'] == $lesson_id) {
                    $watch_history_for_each_lesson['progress'] = $progress;
                    $watch_history_array[$i]['progress'] = $progress;
                    $founder = true;
                }
            }
            if (!$founder) {
                array_push($watch_history_array, ['lesson_id' => $lesson_id, 'progress' => $progress]);
            }
        }

        $data['watch_history'] = json_encode($watch_history_array);
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);


        return $progress;
    }
}
