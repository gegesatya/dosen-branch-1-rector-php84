<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Db_migration_rps_obe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
    }

    public function index()
    {
        echo "=== STARTING MIGRATION FOR RPS OBE ===\n";

        // 1. Create siakad_rps_obe
        if (!$this->db->table_exists('siakad_rps_obe')) {
            $fields = [
                'id_rps' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE
                ],
                'id_ptk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'koordinator_rps' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'kaprodi_rps' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'tanggal_rps' => [
                    'type' => 'DATE',
                    'null' => TRUE
                ],
                'id_mk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'kode_mk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => TRUE
                ],
                'nm_mk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE
                ],
                'sks_mk' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'default'    => 3
                ],
                'sks_tm' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'default'    => 0
                ],
                'sks_prak' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'default'    => 0
                ],
                'semester' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => TRUE
                ],
                'dosen_pengampu' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'deskripsi_mata_kuliah' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'prasyarat_mata_kuliah' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'cpl' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tujuan_belajar' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'referensi' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'image_organisasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE
                ],
                // Kontrak Pembelajaran
                'manfaat_mata_kuliah' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'cp_mata_kuliah' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'analisis_instruksional' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'strategi_perkuliahan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'materi_bahan_bacaan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_tugas' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'kontrak_kriteria_penilaian' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'jadwal_perkuliahan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tata_kelola_perkuliahan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'kontrak_kuliah_markdown' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                // Rancangan Tugas
                'tugas_ke' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'tugas_minggu_ke' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'tujuan_tugas' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_obyek_garapan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_batasan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_metode' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_luaran' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_penilaian_kerapian' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'tugas_ketepatan' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'rancangan_tugas_markdown' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                // Fitur OBE & AI
                'roadmap_modul_json' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                'roadmap_wbi_markdown' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                'matrix_pertemuan_json' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                'ai_provider' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE
                ],
                'ai_model' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => TRUE
                ],
                'raw_ai_output' => [
                    'type' => 'LONGTEXT',
                    'null' => TRUE
                ],
                'id_status_pengajuan_rps' => [
                    'type'       => 'INT',
                    'constraint' => 2,
                    'default'    => 1 // 1: Draft, 2: Diajukan, 3: Disetujui
                ],
                'create_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => TRUE
                ],
                'create_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'change_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => TRUE
                ],
                'change_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ];

            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id_rps', TRUE);
            $this->dbforge->add_key('id_ptk');
            $this->dbforge->add_key('id_mk');
            $this->dbforge->create_table('siakad_rps_obe', TRUE);
            echo "Table 'siakad_rps_obe' created successfully.\n";
        } else {
            echo "Table 'siakad_rps_obe' already exists.\n";
        }

        // 2. Create siakad_rps_obe_detail
        if (!$this->db->table_exists('siakad_rps_obe_detail')) {
            $fields_detail = [
                'id_detail_rps' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE
                ],
                'id_rps' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE
                ],
                'kolom_1' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'null'       => TRUE // Minggu ke- (1-16)
                ],
                'kolom_2' => [
                    'type'       => 'INT',
                    'constraint' => 3,
                    'null'       => TRUE // Pertemuan ke- (1-16)
                ],
                'kolom_3' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Sub-CPMK C4-C6
                ],
                'kolom_4' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Bahan Kajian
                ],
                'kolom_5' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Bentuk Pembelajaran
                ],
                'kolom_6' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Metode Pembelajaran SCL/PjBL/CBL
                ],
                'kolom_7' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Pengalaman Belajar
                ],
                'kolom_8' => [
                    'type'       => 'INT',
                    'constraint' => 6,
                    'default'    => 0 // Waktu Teori (menit)
                ],
                'kolom_9' => [
                    'type'       => 'INT',
                    'constraint' => 6,
                    'default'    => 0 // Waktu Praktek (menit)
                ],
                'kolom_10' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Kriteria Penilaian
                ],
                'kolom_11' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Indikator Penilaian
                ],
                'kolom_12' => [
                    'type'       => 'INT',
                    'constraint' => 4,
                    'default'    => 0 // Bobot Nilai (%)
                ],
                'kolom_13' => [
                    'type' => 'TEXT',
                    'null' => TRUE // Sumber Belajar / Rujukan
                ]
            ];

            $this->dbforge->add_field($fields_detail);
            $this->dbforge->add_key('id_detail_rps', TRUE);
            $this->dbforge->add_key('id_rps');
            $this->dbforge->create_table('siakad_rps_obe_detail', TRUE);
            echo "Table 'siakad_rps_obe_detail' created successfully.\n";
        } else {
            echo "Table 'siakad_rps_obe_detail' already exists.\n";
        }

        // 3. Ensure skenario_pembelajaran_markdown exists in siakad_rps_obe
        if ($this->db->table_exists('siakad_rps_obe')) {
            if (!$this->db->field_exists('skenario_pembelajaran_markdown', 'siakad_rps_obe')) {
                $this->dbforge->add_column('siakad_rps_obe', [
                    'skenario_pembelajaran_markdown' => [
                        'type' => 'LONGTEXT',
                        'null' => TRUE
                    ]
                ]);
                echo "Column 'skenario_pembelajaran_markdown' added to 'siakad_rps_obe'.\n";
            }
        }

        // 4. Ensure draf_bap exists in siakad_rencana_pertemuan
        if ($this->db->table_exists('siakad_rencana_pertemuan')) {
            if (!$this->db->field_exists('draf_bap', 'siakad_rencana_pertemuan')) {
                $this->dbforge->add_column('siakad_rencana_pertemuan', [
                    'draf_bap' => [
                        'type' => 'MEDIUMTEXT',
                        'null' => TRUE
                    ]
                ]);
                echo "Column 'draf_bap' added to 'siakad_rencana_pertemuan'.\n";
            } else {
                echo "Column 'draf_bap' already exists in 'siakad_rencana_pertemuan'.\n";
            }
        }

        echo "=== MIGRATION COMPLETED ===\n";
    }
}
