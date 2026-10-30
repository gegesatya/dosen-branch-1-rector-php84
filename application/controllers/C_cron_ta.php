<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_cron_ta extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function expire_old_ta()
    {
        $log_msgs = [];
        $log_msgs[] = "=== CRON TA EXPIRED: " . date('Y-m-d H:i:s') . " ===";

        // 1. Auto-Cleansing (Dihapus sesuai permintaan: jika lulus tidak perlu update status_sk)
        $log_msgs[] = "Auto-Lulus (status->2): Disabled.";

        // 2. Auto-Expire (Set status 3 untuk yang > 1 tahun dan belum LULUS)
        $sql_expire = "
            UPDATE siakad_skripsi_pengajuan_judul pj
            LEFT JOIN siakad_skripsi_nilai_sempro ns 
                ON pj.id_judul_skripsi = ns.id_judul_skripsi 
                AND pj.dosen_pembimbing_1 = ns.id_penilai
            SET pj.status_sk = 3 
            WHERE pj.status_sk = 1 
              AND pj.tanggal_sk <= DATE_SUB(NOW(), INTERVAL 1 YEAR)
              AND (ns.sidang_hasil IS NULL OR ns.sidang_hasil != 'LULUS')
        ";
        $this->db->query($sql_expire);
        $affected_expire = $this->db->affected_rows();
        $log_msgs[] = "Auto-Expire (status->3): $affected_expire rows updated.";

        $output = implode("\n", $log_msgs);
        echo $output;
    }
}
