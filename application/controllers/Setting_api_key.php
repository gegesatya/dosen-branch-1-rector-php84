<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_api_key extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['ion_auth']);
        
        // Check if user is logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        
        // Only allow group_id 2 (Dosen Tetap)
        $group = [2];
        if (!$this->ion_auth->in_group($group)) {
            redirect('auth/logout', 'refresh');
        }

        $this->load->model('Setting_api_key_model');
    }

    public function index() {
        $id_ptk = $this->session->userdata('id_ptk');
        
        // Handle form submission
        if ($this->input->post('submit')) {
            $api_key = trim($this->input->post('api_key', true));
            $ai_provider = $this->input->post('ai_provider', true) ?: 'gemini';

            if ($ai_provider === 'groq') {
                $ai_model = 'openai/gpt-oss-20b';
            } elseif ($ai_provider === 'openai') {
                $ai_model = 'gpt-4o-mini';
            } elseif ($ai_provider === 'deepseek') {
                $ai_model = 'deepseek-chat';
            } else {
                $ai_provider = 'gemini';
                $ai_model = 'gemini-3.5-flash';
            }

            // Jika api_key tidak diketik ulang, gunakan key yang sudah tersimpan sebelumnya milik user login ini
            if (empty($api_key)) {
                $existing_keys = $this->Setting_api_key_model->get_all_api_keys($id_ptk);
                if (!empty($existing_keys[$ai_provider])) {
                    $api_key = $existing_keys[$ai_provider];
                }
            }

            if (!empty($api_key)) {
                $this->Setting_api_key_model->save_api_key($id_ptk, $api_key, $ai_provider, $ai_model);
                $this->session->set_flashdata('success', 'Konfigurasi AI Provider & API Key berhasil disimpan.');
                redirect('setting_api_key');
            } else {
                $this->session->set_flashdata('error', 'API Key tidak boleh kosong.');
            }
        }

        $data['current_key'] = $this->Setting_api_key_model->get_api_key($id_ptk); // Fetches the last updated (Active) provider
        $data['all_keys'] = $this->Setting_api_key_model->get_all_api_keys($id_ptk);
        $data['content'] = "setting/setting_api_key_view";
        $data['menu_aktif'] = "m_setting_api_key";
        $data['parent_4'] = "active open";
        $data['halaman'] = "Setting API Key";
        $data['sub_halaman'] = "Konfigurasi AI";
        
        $this->load->view('master_page', $data);
    }

    public function ajax_save() {
        $id_ptk = $this->session->userdata('id_ptk');
        
        $api_key = trim($this->input->post('api_key', true));
        $ai_provider = $this->input->post('ai_provider', true) ?: 'gemini';

        if ($ai_provider === 'groq') {
            $ai_model = 'openai/gpt-oss-20b';
        } elseif ($ai_provider === 'openai') {
            $ai_model = 'gpt-4o-mini';
        } elseif ($ai_provider === 'deepseek') {
            $ai_model = 'deepseek-chat';
        } else {
            $ai_provider = 'gemini';
            $ai_model = 'gemini-3.5-flash';
        }

        // Jika api_key tidak diketik ulang, ambil dari key yang sudah tersimpan sebelumnya milik user login ini
        if (empty($api_key)) {
            $existing_keys = $this->Setting_api_key_model->get_all_api_keys($id_ptk);
            if (!empty($existing_keys[$ai_provider])) {
                $api_key = $existing_keys[$ai_provider];
            }
        }

        if (!empty($api_key)) {
            $this->Setting_api_key_model->save_api_key($id_ptk, $api_key, $ai_provider, $ai_model);
            
            // Regenerate CSRF token
            $csrf_name = $this->security->get_csrf_token_name();
            $csrf_hash = $this->security->get_csrf_hash();
            
            echo json_encode(['status' => 'success', 'message' => 'AI Provider (' . strtoupper($ai_provider) . ') berhasil diaktifkan!', 'csrf_name' => $csrf_name, 'csrf_hash' => $csrf_hash]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'API Key untuk ' . strtoupper($ai_provider) . ' belum dimasukkan.']);
        }
    }

    public function ajax_get() {
        $id_ptk = $this->session->userdata('id_ptk');
        $current_key = $this->Setting_api_key_model->get_api_key($id_ptk);
        $all_keys = $this->Setting_api_key_model->get_all_api_keys($id_ptk);
        
        echo json_encode([
            'status' => 'success',
            'ai_provider' => $current_key ? $current_key->ai_provider : 'gemini',
            'api_key' => $current_key ? $current_key->api_key : '',
            'all_keys' => $all_keys
        ]);
    }
}
