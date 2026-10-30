<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Beta_notification_model extends CI_Model
{
    private $table = 'sys_beta_notifications';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_check_table();
    }

    /**
     * Auto-create table if not exists (Safe for Beta)
     */
    private function _check_table()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->load->dbforge();
            $fields = [
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'user_id' => ['type' => 'VARCHAR', 'constraint' => 50],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                'message' => ['type' => 'TEXT'],
                'type' => ['type' => 'ENUM("info", "success", "warning", "danger")', 'default' => 'info'],
                'link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'is_read' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'DATETIME'],
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('user_id');
            $this->dbforge->create_table($this->table);
        }
    }

    public function add_notification($user_id, $title, $message, $type = 'info', $link = NULL)
    {
        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert($this->table, $data);
    }

    public function get_unread($user_id)
    {
        return $this->db->where(['user_id' => $user_id, 'is_read' => 0])
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function mark_read($id)
    {
        return $this->db->where('id', $id)->update($this->table, ['is_read' => 1]);
    }

    public function count_unread($user_id)
    {
        return $this->db->where(['user_id' => $user_id, 'is_read' => 0])
            ->count_all_results($this->table);
    }
}
