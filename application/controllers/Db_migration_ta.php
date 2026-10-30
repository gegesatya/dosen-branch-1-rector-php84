<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Db_migration_ta extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            echo "Only available via CLI";
            exit;
        }
    }

    public function index()
    {
        $this->load->database();
        $this->load->dbforge();

        echo "Starting Migration...\n";

        // Create siakad_skripsi_bimbingan_log
        if (!$this->db->table_exists('siakad_skripsi_bimbingan_log')) {
            $fields = [
                'id_log' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'id_judul_skripsi' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'tahapan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'Proposal',
                ],
                'tanggal_bimbingan' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'catatan_mahasiswa' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'file_lampiran_mahasiswa' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE,
                ],
                'catatan_dosen' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'file_lampiran_dosen' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE,
                ],
                'progress_persen' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0,
                ],
                'status_bimbingan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'Menunggu Review',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'created_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE,
                ]
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id_log', TRUE);
            $this->dbforge->create_table('siakad_skripsi_bimbingan_log');
            echo "Table siakad_skripsi_bimbingan_log created.\n";
        } else {
            echo "Table siakad_skripsi_bimbingan_log already exists.\n";
        }

        // Add columns to siakad_skripsi_pengajuan_judul
        $add_fields = [];
        
        if (!$this->db->field_exists('current_progress', 'siakad_skripsi_pengajuan_judul')) {
            $add_fields['current_progress'] = ['type' => 'INT', 'constraint' => 3, 'default' => 0];
        }
        if (!$this->db->field_exists('academic_health_score', 'siakad_skripsi_pengajuan_judul')) {
            $add_fields['academic_health_score'] = ['type' => 'FLOAT', 'default' => 0];
        }
        if (!$this->db->field_exists('kategori_health', 'siakad_skripsi_pengajuan_judul')) {
            $add_fields['kategori_health'] = ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Aman'];
        }
        if (!$this->db->field_exists('last_bimbingan_date', 'siakad_skripsi_pengajuan_judul')) {
            $add_fields['last_bimbingan_date'] = ['type' => 'DATE', 'null' => TRUE];
        }

        if (!empty($add_fields)) {
            $this->dbforge->add_column('siakad_skripsi_pengajuan_judul', $add_fields);
            echo "Columns added to siakad_skripsi_pengajuan_judul.\n";
        } else {
            echo "Columns already exist in siakad_skripsi_pengajuan_judul.\n";
        }

        echo "Migration Completed.\n";
    }
}
