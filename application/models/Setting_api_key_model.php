<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_api_key_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        try {
            // Load driver cache dengan prioritas Redis, fallback ke file cache
            $this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
        } catch (\Throwable $e) {
            log_message('error', 'Cache driver init error: ' . $e->getMessage());
        }
    }

    public function get_api_key($id_ptk, $ai_provider = null) {
        if (empty($id_ptk)) {
            return null;
        }

        // Tentukan nama cache key terisolasi per id_ptk
        $cache_name = 'api_key_default_' . $id_ptk;
        if ($ai_provider) {
            $cache_name = 'api_key_' . $ai_provider . '_' . $id_ptk;
        }

        // 1. Coba ambil dari Cache
        try {
            if (isset($this->cache) && ($this->cache->is_supported('redis') || isset($this->cache))) {
                $cache_data = $this->cache->get($cache_name);
                if ($cache_data !== FALSE && is_object($cache_data) && !empty($cache_data->id_ptk) && $cache_data->id_ptk === $id_ptk) {
                    return $cache_data;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Redis/Cache get error: ' . $e->getMessage());
        }

        // 2. Jika tabel belum ada di DB server, kembalikan null
        if (!$this->db->table_exists('siakad_api_key')) {
            return null;
        }

        // 3. Query database strictly per id_ptk milik user yang login
        $this->db->where('id_ptk', $id_ptk);
        $this->db->where_not_in('ai_provider', ['anthropic', 'claude']);
        if ($ai_provider) {
            $this->db->where('ai_provider', $ai_provider);
        } else {
            if ($this->db->field_exists('is_active', 'siakad_api_key')) {
                $this->db->order_by('is_active', 'DESC');
            }
        }
        $this->db->order_by('updated_at', 'DESC');
        $query = $this->db->get('siakad_api_key');
        
        if ($query && $query->num_rows() > 0) {
            $row = $query->row();
            // Simpan ke cache selama 30 menit
            try {
                if (isset($this->cache)) {
                    $this->cache->save($cache_name, $row, 1800);
                }
            } catch (\Throwable $e) {
                // Ignore cache write error
            }
            return $row;
        }
        
        return null;
    }

    public function get_all_api_keys($id_ptk) {
        if (empty($id_ptk) || !$this->db->table_exists('siakad_api_key')) {
            return [];
        }
        $this->db->where('id_ptk', $id_ptk);
        $this->db->where_not_in('ai_provider', ['anthropic', 'claude']);
        $query = $this->db->get('siakad_api_key');
        $keys = [];
        if ($query) {
            foreach ($query->result() as $row) {
                $keys[$row->ai_provider] = $row->api_key;
            }
        }
        return $keys;
    }

    public function save_api_key($id_ptk, $api_key, $ai_provider = 'gemini', $ai_model = 'gemini-3.5-flash') {
        if (empty($id_ptk) || !$this->db->table_exists('siakad_api_key')) {
            return false;
        }
        // Hapus/Invalidate cache lama agar perubahan langsung berefek
        try {
            if (isset($this->cache)) {
                $this->cache->delete('api_key_default_' . $id_ptk);
                $this->cache->delete('api_key_gemini_' . $id_ptk);
                $this->cache->delete('api_key_groq_' . $id_ptk);
                $this->cache->delete('api_key_openai_' . $id_ptk);
                $this->cache->delete('api_key_deepseek_' . $id_ptk);
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        // Setel provider lain milik PTK ini menjadi non-aktif jika kolom is_active ada
        if ($this->db->field_exists('is_active', 'siakad_api_key')) {
            $this->db->where('id_ptk', $id_ptk);
            $this->db->update('siakad_api_key', ['is_active' => 0]);
        }

        $this->db->where('id_ptk', $id_ptk);
        $this->db->where('ai_provider', $ai_provider);
        $query = $this->db->get('siakad_api_key');
        
        $data = [
            'id_ptk' => $id_ptk,
            'api_key' => $api_key,
            'ai_provider' => $ai_provider,
            'ai_model' => $ai_model,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($this->db->field_exists('is_active', 'siakad_api_key')) {
            $data['is_active'] = 1;
        }

        if ($query && $query->num_rows() > 0) {
            $this->db->where('id_ptk', $id_ptk);
            $this->db->where('ai_provider', $ai_provider);
            return $this->db->update('siakad_api_key', $data);
        } else {
            return $this->db->insert('siakad_api_key', $data);
        }
    }
}
