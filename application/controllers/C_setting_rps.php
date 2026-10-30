<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller C_setting_rps
 * Khusus konfigurasi Mode Tampilan RPS & Maintenance Control Panel (Exclusive for gege.satya@wbi.ac.id)
 */
class C_setting_rps extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['ion_auth', 'session']);
        $this->load->model('Maintenance_model');

        // 1. Cek Login
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        // 2. Proteksi Eksklusif: Hanya gege.satya@wbi.ac.id yang berhak mengakses halaman ini
        if (!$this->Maintenance_model->is_user_exempt()) {
            show_error('Akses Terbatas: Halaman Pengaturan RPS & Maintenance hanya dapat diakses oleh Administrator Sistem (gege.satya@wbi.ac.id).', 403, '403 Forbidden Access');
            exit;
        }
    }

    /**
     * Halaman Utama Pengaturan RPS & Maintenance
     */
    public function index()
    {
        $user_obj = $this->ion_auth->user()->row();
        $user_email = $user_obj->email ?? '';

        $data['halaman']              = 'Pengaturan RPS & Maintenance';
        $data['sub_halaman']          = 'Mode Tampilan Menu & Pemeliharaan Sistem';
        $data['menu_aktif']           = 'm_setting_rps_mode';
        $data['parent_admin']         = 'active open';
        $data['user_email']           = $user_email;

        // Data Mode RPS Menu
        $data['rps_mode_detail']      = $this->Maintenance_model->get_rps_menu_mode_detail();

        // Data Maintenance Status
        $data['maintenance_statuses'] = $this->Maintenance_model->get_all_statuses();
        $data['is_maint_kurikulum']   = $this->Maintenance_model->is_maintenance('kurikulum');
        $data['is_maint_studio_rps']  = $this->Maintenance_model->is_maintenance('studio_rps');

        $data['content']              = 'setting/v_setting_rps_mode';

        $this->load->view('master_page', $data);
    }

    /**
     * AJAX: Set RPS Menu Display Mode ('AUTO', 'LEGACY', 'OBE_DEV')
     */
    public function ajax_set_rps_mode()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $mode = strtoupper(trim($this->input->post('mode', true)));
        if (!in_array($mode, ['AUTO', 'LEGACY', 'OBE_DEV'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'Mode tidak valid. Pilihan yang diizinkan: AUTO, LEGACY, OBE_DEV.'
            ]);
            return;
        }

        $user_obj = $this->ion_auth->user()->row();
        $user_email = $user_obj->email ?? 'gege.satya@wbi.ac.id';

        $this->Maintenance_model->set_rps_menu_mode($mode, $user_email);
        $detail = $this->Maintenance_model->get_rps_menu_mode_detail();

        $mode_labels = [
            'AUTO'    => 'Otomatis (Smart Auto-Detect)',
            'LEGACY'  => 'RPS Lama Murni (Standar Production)',
            'OBE_DEV' => 'RPS OBE Standar 2026 & Arsip (Full Suite)'
        ];

        echo json_encode([
            'status'     => true,
            'message'    => 'Mode tampilan menu RPS berhasil diubah ke: ' . ($mode_labels[$mode] ?? $mode),
            'detail'     => $detail
        ]);
    }

    /**
     * AJAX: Toggle Maintenance Status untuk Modul OBE
     */
    public function ajax_toggle_maintenance()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $module_key = trim($this->input->post('module_key', true));
        $is_active  = (int)$this->input->post('is_active', true);
        $message    = $this->input->post('message', true);

        $user_obj = $this->ion_auth->user()->row();
        $user_email = $user_obj->email ?? 'gege.satya@wbi.ac.id';

        if ($module_key === 'all') {
            $this->Maintenance_model->set_maintenance('kurikulum', $is_active, $message, $user_email);
            $this->Maintenance_model->set_maintenance('studio_rps', $is_active, $message, $user_email);
        } else {
            if (!in_array($module_key, ['kurikulum', 'studio_rps'])) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Modul tidak dikenal.'
                ]);
                return;
            }
            $this->Maintenance_model->set_maintenance($module_key, $is_active, $message, $user_email);
        }

        $statuses = $this->Maintenance_model->get_all_statuses();

        echo json_encode([
            'status'   => true,
            'message'  => 'Status pemeliharaan modul berhasil diperbarui.',
            'statuses' => $statuses
        ]);
    }

    /**
     * AJAX: Simpan Kustom Pesan Pengumuman Pemeliharaan
     */
    public function ajax_save_maintenance_message()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $module_key = trim($this->input->post('module_key', true));
        $message    = trim($this->input->post('message', true));

        if (empty($message)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Pesan pengumuman pemeliharaan tidak boleh kosong.'
            ]);
            return;
        }

        $user_obj = $this->ion_auth->user()->row();
        $user_email = $user_obj->email ?? 'gege.satya@wbi.ac.id';

        if ($module_key === 'all') {
            $cur_kuri = $this->Maintenance_model->is_maintenance('kurikulum');
            $cur_rps  = $this->Maintenance_model->is_maintenance('studio_rps');
            $this->Maintenance_model->set_maintenance('kurikulum', $cur_kuri, $message, $user_email);
            $this->Maintenance_model->set_maintenance('studio_rps', $cur_rps, $message, $user_email);
        } else {
            if (!in_array($module_key, ['kurikulum', 'studio_rps'])) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Modul tidak dikenal.'
                ]);
                return;
            }
            $cur = $this->Maintenance_model->is_maintenance($module_key);
            $this->Maintenance_model->set_maintenance($module_key, $cur, $message, $user_email);
        }

        echo json_encode([
            'status'   => true,
            'message'  => 'Pesan pengumuman pemeliharaan berhasil disimpan.',
            'statuses' => $this->Maintenance_model->get_all_statuses()
        ]);
    }

    /**
     * AJAX: Get Status Lengkap (Mode & Maintenance)
     */
    public function ajax_get_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        echo json_encode([
            'status'       => true,
            'rps_mode'     => $this->Maintenance_model->get_rps_menu_mode_detail(),
            'maintenance'  => $this->Maintenance_model->get_all_statuses()
        ]);
    }
}
