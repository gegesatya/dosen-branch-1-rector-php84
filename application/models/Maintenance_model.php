<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maintenance_model extends CI_Model
{
    private $table = 'siakad_maintenance_status';

    public function __construct()
    {
        parent::__construct();
        $this->_ensure_table_exists();
    }

    /**
     * Auto create table if not exists with default rows
     */
    private function _ensure_table_exists()
    {
        if (!$this->db->table_exists($this->table)) {
            $query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `module_key` VARCHAR(50) NOT NULL UNIQUE,
                `module_name` VARCHAR(100) NOT NULL,
                `is_maintenance` TINYINT(1) NOT NULL DEFAULT 0,
                `message` TEXT NULL,
                `updated_by` VARCHAR(100) NULL,
                `updated_at` DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($query);

            // Seed default rows
            $this->db->insert_batch($this->table, [
                [
                    'module_key'     => 'kurikulum',
                    'module_name'    => 'Daftar Kurikulum & Audit CPL',
                    'is_maintenance' => 0,
                    'message'        => 'Modul Kurikulum sedang dalam pemeliharaan dan pembaruan sistem oleh Administrator Akademik.',
                    'updated_by'     => 'system',
                    'updated_at'     => date('Y-m-d H:i:s')
                ],
                [
                    'module_key'     => 'studio_rps',
                    'module_name'    => 'Studio RPS OBE (Generator & Master RPS)',
                    'is_maintenance' => 0,
                    'message'        => 'Studio RPS OBE sedang dalam pemeliharaan dan optimalisasi sistem oleh Administrator Akademik.',
                    'updated_by'     => 'system',
                    'updated_at'     => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            // Ensure both module_keys exist
            $keys = ['kurikulum', 'studio_rps'];
            foreach ($keys as $k) {
                $check = $this->db->where('module_key', $k)->get($this->table)->row();
                if (!$check) {
                    $m_name = ($k === 'kurikulum') ? 'Daftar Kurikulum & Audit CPL' : 'Studio RPS OBE (Generator & Master RPS)';
                    $this->db->insert($this->table, [
                        'module_key'     => $k,
                        'module_name'    => $m_name,
                        'is_maintenance' => 0,
                        'message'        => 'Modul ini sedang dalam pemeliharaan sistem.',
                        'updated_by'     => 'system',
                        'updated_at'     => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    /**
     * Check if current user is exempted from maintenance / has admin setting access (gege.satya / administrator)
     */
    public function is_user_exempt()
    {
        $CI =& get_instance();
        if (!isset($CI->ion_auth) || !$CI->ion_auth || !method_exists($CI->ion_auth, 'logged_in') || !$CI->ion_auth->logged_in()) {
            return false;
        }

        $user_obj = $CI->ion_auth->user()->row();
        if (!$user_obj) {
            return false;
        }

        $user_email = strtolower(trim($user_obj->email ?? ''));
        $user_uname = strtolower(trim($user_obj->username ?? ''));
        $user_id    = (int)($user_obj->id ?? 0);
        $user_id_ptk = ($CI->session && method_exists($CI->session, 'userdata')) ? ($CI->session->userdata('id_ptk') ?? ($user_obj->id_ptk ?? '')) : ($user_obj->id_ptk ?? '');

        if (
            stripos($user_email, 'gege.satya') !== false ||
            stripos($user_uname, 'gege.satya') !== false ||
            $user_id === 1 ||
            $user_id === 69 ||
            $user_id_ptk === '58aa0428-1c6d-11f1-8885-341bea469d42'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if a module is under maintenance
     */
    public function is_maintenance($module_key)
    {
        $row = $this->db->where('module_key', $module_key)->get($this->table)->row();
        if ($row) {
            return ((int)$row->is_maintenance === 1);
        }
        return false;
    }

    /**
     * Get maintenance status of a module
     */
    public function get_status($module_key)
    {
        return $this->db->where('module_key', $module_key)->get($this->table)->row();
    }

    /**
     * Get all maintenance statuses
     */
    public function get_all_statuses()
    {
        $result = [];
        $rows = $this->db->get($this->table)->result();
        foreach ($rows as $r) {
            $result[$r->module_key] = $r;
        }
        return $result;
    }

    /**
     * Update maintenance status
     */
    public function set_maintenance($module_key, $is_active, $message = null, $updated_by = null)
    {
        $data = [
            'is_maintenance' => $is_active ? 1 : 0,
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        if ($message !== null) {
            $data['message'] = trim($message);
        }

        if ($updated_by !== null) {
            $data['updated_by'] = trim($updated_by);
        }

        $this->db->where('module_key', $module_key)->update($this->table, $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Get RPS Menu Display Mode: 'LEGACY' (Menu Lama murni) vs 'OBE_DEV' (Menu Lengkap OBE + Lama)
     */
    public function get_rps_menu_mode()
    {
        // 1. Check if explicitly configured in table siakad_maintenance_status
        $row = $this->db->where('module_key', 'rps_menu_mode')->get($this->table)->row();
        if ($row && !empty($row->message)) {
            $mode = strtoupper(trim((string)$row->message));
            if ($mode === 'LEGACY' || $mode === 'OBE_DEV') {
                return $mode;
            }
        }

        // 2. Default Smart Auto-Detect (or if mode == 'AUTO'):
        // If developer/admin or running in dosen_branch_1 -> show OBE_DEV
        // Otherwise -> show LEGACY (safe production default)
        $is_exempt = $this->is_user_exempt();
        $is_dev_folder = (defined('FCPATH') && stripos(FCPATH, 'dosen_branch_1') !== false) || (defined('ENVIRONMENT') && ENVIRONMENT === 'development');

        if ($is_exempt || $is_dev_folder) {
            return 'OBE_DEV';
        }

        return 'LEGACY';
    }

    /**
     * Get Detailed Config & Status for Settings Page
     */
    public function get_rps_menu_mode_detail()
    {
        $row = $this->db->where('module_key', 'rps_menu_mode')->get($this->table)->row();
        $configured_mode = $row ? strtoupper(trim($row->message ?? 'AUTO')) : 'AUTO';
        if (!in_array($configured_mode, ['AUTO', 'LEGACY', 'OBE_DEV'])) {
            $configured_mode = 'AUTO';
        }

        $effective_mode = $this->get_rps_menu_mode();

        return [
            'configured_mode' => $configured_mode,
            'effective_mode'  => $effective_mode,
            'updated_by'      => $row->updated_by ?? '-',
            'updated_at'      => $row->updated_at ?? '-'
        ];
    }

    /**
     * Set RPS Menu Mode explicitly ('LEGACY' or 'OBE_DEV' or 'AUTO')
     */
    public function set_rps_menu_mode($mode, $updated_by = 'system')
    {
        $mode = strtoupper(trim($mode));
        $row = $this->db->where('module_key', 'rps_menu_mode')->get($this->table)->row();
        if ($row) {
            $this->db->where('module_key', 'rps_menu_mode')->update($this->table, [
                'message'    => $mode,
                'updated_by' => $updated_by,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->insert($this->table, [
                'module_key'     => 'rps_menu_mode',
                'module_name'    => 'Mode Tampilan Menu RPS (LEGACY vs OBE_DEV)',
                'is_maintenance' => 0,
                'message'        => $mode,
                'updated_by'     => $updated_by,
                'updated_at'     => date('Y-m-d H:i:s')
            ]);
        }
    }
}
