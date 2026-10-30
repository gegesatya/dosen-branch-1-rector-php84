<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rps_obe_model extends CI_Model
{
    public $table = 'siakad_rps_obe';
    public $column_order = [null, 'kode_mk', 'nm_mk', 'sks_mk', 'create_date', null];
    public $column_search = ['kode_mk', 'nm_mk', 'cpl', 'tujuan_belajar'];
    public $order = ['id_rps' => 'desc'];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $this->db->where('id_ptk', $id_ptk);
        if ($this->db->field_exists('is_deleted', $this->table)) {
            $this->db->where('(is_deleted = 0 OR is_deleted IS NULL)');
        }
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $id_ptk = $this->session->userdata('id_ptk');
        $this->db->where('id_ptk', $id_ptk);
        if ($this->db->field_exists('is_deleted', $this->table)) {
            $this->db->where('(is_deleted = 0 OR is_deleted IS NULL)');
        }
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        if (strlen($id) === 32 && !is_numeric($id)) {
            $this->db->where('md5(id_rps)', $id);
        } else {
            $this->db->where('id_rps', $id);
        }
        $query = $this->db->get();
        $row = $query->row();
        if ($row) {
            $prodi = '';
            if (!empty($row->id_mk)) {
                $mk = $this->db->query("SELECT COALESCE(NULLIF(s.nm_lemb, ''), (SELECT s2.nm_lemb FROM feeder_kelas_kuliah kk JOIN sms s2 ON s2.id_sms = kk.id_sms WHERE kk.id_mk = mk.id_mk LIMIT 1), '') as nm_prodi FROM feeder_mata_kuliah mk LEFT JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.id_mk = ? LIMIT 1", [$row->id_mk])->row();
                if ($mk && !empty($mk->nm_prodi)) $prodi = $mk->nm_prodi;
            }
            if (empty($prodi) && !empty($row->kode_mk)) {
                $mk = $this->db->query("SELECT COALESCE(NULLIF(s.nm_lemb, ''), (SELECT s2.nm_lemb FROM feeder_kelas_kuliah kk JOIN sms s2 ON s2.id_sms = kk.id_sms WHERE kk.id_mk = mk.id_mk LIMIT 1), '') as nm_prodi FROM feeder_mata_kuliah mk LEFT JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.kode_mk = ? LIMIT 1", [$row->kode_mk])->row();
                if ($mk && !empty($mk->nm_prodi)) $prodi = $mk->nm_prodi;
            }
            if (empty($prodi) && !empty($row->nm_mk)) {
                $mk = $this->db->query("SELECT COALESCE(NULLIF(s.nm_lemb, ''), (SELECT s2.nm_lemb FROM feeder_kelas_kuliah kk JOIN sms s2 ON s2.id_sms = kk.id_sms WHERE kk.id_mk = mk.id_mk LIMIT 1), '') as nm_prodi FROM feeder_mata_kuliah mk LEFT JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.nm_mk = ? LIMIT 1", [$row->nm_mk])->row();
                if ($mk && !empty($mk->nm_prodi)) $prodi = $mk->nm_prodi;
            }
            $row->nama_prodi = $prodi;
            $row->prodi = $prodi;
        }
        return $row;
    }

    public function get_details($id_rps)
    {
        return $this->db->where('id_rps', $id_rps)
            ->order_by('kolom_1', 'ASC')
            ->get('siakad_rps_obe_detail')
            ->result();
    }

    public function save($data)
    {
        $data = clean_wysiwyg_data($data);
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($where, $data)
    {
        $data = clean_wysiwyg_data($data);
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete_by_id($id, $user_email = null)
    {
        if (strlen($id) === 32 && !is_numeric($id)) {
            $row = $this->get_by_id($id);
            if ($row) $id = $row->id_rps;
        }
        if ($this->db->field_exists('is_deleted', $this->table)) {
            $data = [
                'is_deleted' => 1,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $user_email ?: ($this->ion_auth->user()->row()->email ?? ($this->session->userdata('identity') ?: 'dosen'))
            ];
            $this->db->where('id_rps', $id)->update($this->table, $data);
            return ($this->db->affected_rows() > 0);
        } else {
            $this->db->where('id_rps', $id)->delete('siakad_rps_obe_detail');
            $this->db->where('id_rps', $id)->delete($this->table);
            return ($this->db->affected_rows() > 0);
        }
    }

    public function save_details($id_rps, $details)
    {
        $this->db->where('id_rps', $id_rps)->delete('siakad_rps_obe_detail');
        if (!empty($details) && is_array($details)) {
            $details = clean_wysiwyg_data($details);
            $insert_batch = [];
            foreach ($details as $idx => $d) {
                $insert_batch[] = [
                    'id_rps'             => $id_rps,
                    'kolom_1'            => $d['minggu'] ?? ($idx + 1),
                    'kolom_2'            => $d['pertemuan'] ?? ($idx + 1),
                    'kolom_3'            => $d['sub_cpmk'] ?? ($d['kemampuan_akhir'] ?? ''),
                    'kolom_4'            => $d['bahan_kajian'] ?? '',
                    'kolom_5'            => $d['bentuk_pembelajaran'] ?? '',
                    'kolom_6'            => $d['metode'] ?? ($d['metode_pembelajaran'] ?? ''),
                    'kolom_7'            => $d['pengalaman_belajar'] ?? '',
                    'kolom_8'            => (int)($d['waktu_teori'] ?? 0),
                    'kolom_9'            => (int)($d['waktu_praktek'] ?? 0),
                    'kolom_10'           => $d['kriteria_evaluasi'] ?? ($d['kriteria_penilaian'] ?? ''),
                    'kolom_11'           => $d['indikator_evaluasi'] ?? ($d['indikator_penilaian'] ?? ''),
                    'kolom_12'           => (int)($d['bobot'] ?? ($d['bobot_nilai'] ?? 0)),
                    'kolom_13'           => $d['sumber_belajar'] ?? ''
                ];
            }
            if (!empty($insert_batch)) {
                $this->db->insert_batch('siakad_rps_obe_detail', $insert_batch);
            }
        }
        return true;
    }
}
