<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_bimbingan_model extends CI_Model
{

	public $table = 'siakad_skripsi_kartu_bimbingan_proposal';
	public $column = ['mahasiswa.nm_pd', 'mahasiswa_pt.nipd', 'siakad_skripsi_kartu_bimbingan_proposal.html_respon'];
	public $order = ['mahasiswa_pt.nipd' => 'ASC'];

	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
	}

	private function _get_datatables_query()
	{
		// $id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		// $id_sms = $this->session->userdata('prodi');

		// $lulus_do = $this->db->select('nim')->get('feeder_lulus_do')->result_array();

		$id_ptk = $this->session->userdata('id_ptk');

		// $this->db->group_start();
		// $this->db->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
		// 	->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk]);
		// $this->db->group_end();

		// $this->db->where_not_in('mahasiswa_pt.nipd', $lulus_do, FALSE);

		//$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms])
		$this->db
			->group_start()
			->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
			->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk])
			->group_end()
			->where('mahasiswa_pt.id_jns_keluar IS NULL', NULL, FALSE)
			->where('siakad_skripsi_pengajuan_judul.status_sk !=', 3)
			->select('siakad_skripsi_pengajuan_judul.*, 
					siakad_skripsi_kartu_bimbingan_proposal.html_respon,
					siakad_skripsi_kartu_bimbingan_proposal.html_table,
					siakad_skripsi_kartu_bimbingan_proposal.status_approve,
					siakad_skripsi_kartu_bimbingan_proposal.dosen_pembimbing,
					mahasiswa_pt.nipd, 
					mahasiswa.nm_pd')
			->from('siakad_skripsi_pengajuan_judul')
			->join('siakad_skripsi_kartu_bimbingan_proposal', 'siakad_skripsi_kartu_bimbingan_proposal.id_judul_skripsi = siakad_skripsi_pengajuan_judul.id_judul_skripsi', 'LEFT')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');


		$i = 0;

		foreach ($this->column as $item) {
			if ($_POST['search']['value']) {

				if ($i === 0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column) - 1 == $i)
					$this->db->group_end();
			}
			$column[$i] = $item;
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if ($_POST['length'] != -1)
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
		// $user_logins = $this->ion_auth->user()->row();
		// $id_sms = $this->session->userdata('prodi');

		$id_ptk = $this->session->userdata('id_ptk');
		// $this->db->group_start();
		// $this->db->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
		// 	->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk]);
		// $this->db->group_end();

		//$this->db->where(['siakad_skripsi_pengajuan_judul.id_sms' => $id_sms])
		$this->db
			->group_start()
			->where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_1' => $id_ptk])
			->or_where(['siakad_skripsi_pengajuan_judul.dosen_pembimbing_2' => $id_ptk])
			->group_end()
			->where('mahasiswa_pt.id_jns_keluar IS NULL', NULL, FALSE)
			->where('siakad_skripsi_pengajuan_judul.status_sk !=', 3)
			->select('siakad_skripsi_pengajuan_judul.*, 
					siakad_skripsi_kartu_bimbingan_proposal.html_respon,
					siakad_skripsi_kartu_bimbingan_proposal.html_table,
					siakad_skripsi_kartu_bimbingan_proposal.status_approve,
					siakad_skripsi_kartu_bimbingan_proposal.dosen_pembimbing,
					mahasiswa_pt.nipd, 
					mahasiswa.nm_pd')
			->from('siakad_skripsi_pengajuan_judul')
			->join('siakad_skripsi_kartu_bimbingan_proposal', 'siakad_skripsi_kartu_bimbingan_proposal.id_judul_skripsi = siakad_skripsi_pengajuan_judul.id_judul_skripsi', 'LEFT')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd');

		return $this->db->count_all_results();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_judul_skripsi', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->replace($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function save_draft($data, $lembar_monitoring, $id_judul_skripsi)
	{

		if ($lembar_monitoring->num_rows() == 0) {
			$this->db->insert($this->table, $data);
		} else {
			$this->db->update('siakad_skripsi_kartu_bimbingan_proposal', $data, ['id_judul_skripsi' => $id_judul_skripsi]);
		}

		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('uniq', $id);
		$this->db->delete($this->table);
	}

	public function approve_kegiatan($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function get_by_id_2($id)
	{

		$this->db->where(['id_judul_skripsi' => $id])
			->select('siakad_skripsi_pengajuan_judul.*, mahasiswa_pt.nipd, mahasiswa.nm_pd')
			->from('siakad_skripsi_pengajuan_judul')
			->join('mahasiswa_pt', 'mahasiswa_pt.nipd = siakad_skripsi_pengajuan_judul.nipd', 'left')
			->join('mahasiswa', 'mahasiswa.id_pd = mahasiswa_pt.id_pd', 'left');

		//$this->db->from('siakad_skripsi_pengajuan_judul');
		//$this->db->where('id_judul_skripsi', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function update_sempro($where, $data, $judul_lama = NULL)
	{
		$this->db->update('siakad_skripsi_pengajuan_judul', $data, $where);
		return $this->db->affected_rows();
	}

	public function get_bimbingan_logs($id_judul_skripsi)
	{
		$this->db->select('log.*, u.first_name, u.last_name');
		$this->db->from('siakad_skripsi_bimbingan_log log');
		$this->db->join('users u', 'log.created_by = u.id', 'left');
		$this->db->where('log.id_judul_skripsi', $id_judul_skripsi);
		$this->db->order_by('log.created_at', 'ASC');
		$logs = $this->db->get()->result();

        $has_todo_table = $this->db->table_exists('siakad_skripsi_agenda_todo');
        foreach ($logs as $log) {
            if ($has_todo_table && isset($log->initiator_type) && $log->initiator_type == 'dosen') {
                $log->todos = $this->db->where('id_log', $log->id_log)
                                       ->order_by('urutan', 'ASC')
                                       ->get('siakad_skripsi_agenda_todo')->result();
            } else {
                $log->todos = [];
            }
        }
        return $logs;
	}

	public function save_bimbingan_log($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert('siakad_skripsi_bimbingan_log', $data);
		return $this->db->insert_id();
	}

	public function update_bimbingan_log($id_log, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->where('id_log', $id_log);
		$this->db->update('siakad_skripsi_bimbingan_log', $data);
		return $this->db->affected_rows();
	}

	public function update_academic_health($id_judul_skripsi, $data)
	{
		$this->db->where('id_judul_skripsi', $id_judul_skripsi);
		$this->db->update('siakad_skripsi_pengajuan_judul', $data);
	}

    public function get_all_bimbingan_dosen($id_ptk)
    {
        $this->db->select("a.*, b.nm_pd, b.foto, m.mulai_smt,
            (SELECT id_log FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS id_agenda_aktif,
            (SELECT agenda_prioritas FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS agenda_prioritas_aktif,
            (SELECT agenda_tgl_selesai FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS agenda_tgl_selesai_aktif,
            (SELECT agenda_tgl_mulai FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS agenda_tgl_mulai_aktif,
            (SELECT agenda_jam_mulai FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS agenda_jam_mulai_aktif,
            (SELECT agenda_jam_selesai FROM siakad_skripsi_bimbingan_log
             WHERE id_judul_skripsi = a.id_judul_skripsi
               AND initiator_type = 'dosen'
               AND status_bimbingan = 'Agenda Dosen'
             ORDER BY id_log DESC LIMIT 1) AS agenda_jam_selesai_aktif,

            (SELECT COUNT(*) FROM siakad_skripsi_bimbingan_log last_log
             WHERE last_log.id_judul_skripsi = a.id_judul_skripsi
               AND last_log.catatan_mahasiswa IS NOT NULL
               AND last_log.catatan_mahasiswa != ''
               AND (
                    (a.dosen_pembimbing_1 = '$id_ptk' AND last_log.status_bimbingan = 'Menunggu Review' AND (last_log.status_pendamping IS NULL OR last_log.status_pendamping = '' OR last_log.status_pendamping = 'Lanjut ke Utama'))
                    OR 
                    (a.dosen_pembimbing_2 = '$id_ptk' AND (last_log.status_pendamping = 'Konsultasi ke Pendamping' OR last_log.status_pendamping = 'Perlu Revisi'))
               )
               AND last_log.id_log = (
                   SELECT MAX(id_log) FROM siakad_skripsi_bimbingan_log
                   WHERE id_judul_skripsi = a.id_judul_skripsi
               )
            ) AS jumlah_pending,
            (SELECT last_log2.created_at FROM siakad_skripsi_bimbingan_log last_log2
             WHERE last_log2.id_judul_skripsi = a.id_judul_skripsi
               AND last_log2.id_log = (
                   SELECT MAX(id_log) FROM siakad_skripsi_bimbingan_log
                   WHERE id_judul_skripsi = a.id_judul_skripsi
               )
             LIMIT 1
            ) AS tgl_pending,
            (SELECT u.first_name FROM siakad_skripsi_bimbingan_log last_log3
             LEFT JOIN users u ON last_log3.created_by = u.id
             WHERE last_log3.id_judul_skripsi = a.id_judul_skripsi
               AND last_log3.id_log = (
                   SELECT MAX(id_log) FROM siakad_skripsi_bimbingan_log
                   WHERE id_judul_skripsi = a.id_judul_skripsi
               )
             LIMIT 1
            ) AS last_updater_name,
            (SELECT last_log4.status_pendamping FROM siakad_skripsi_bimbingan_log last_log4
             WHERE last_log4.id_judul_skripsi = a.id_judul_skripsi
               AND last_log4.id_log = (
                   SELECT MAX(id_log) FROM siakad_skripsi_bimbingan_log
                   WHERE id_judul_skripsi = a.id_judul_skripsi
               )
             LIMIT 1
            ) AS last_status_pendamping,
            (SELECT COUNT(*) FROM siakad_skripsi_bimbingan_log 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND catatan_mahasiswa IS NOT NULL 
               AND catatan_mahasiswa != ''
               AND status_bimbingan = 'Konsultasi ke Pendamping'
               AND (a.dosen_pembimbing_2 IS NOT NULL AND a.dosen_pembimbing_2 != '' AND a.dosen_pembimbing_2 != '-')
            ) AS pending_di_pendamping,
            (SELECT created_at FROM siakad_skripsi_bimbingan_log 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND catatan_mahasiswa IS NOT NULL 
               AND catatan_mahasiswa != ''
               AND status_bimbingan != 'Disetujui'
               AND (status_pendamping IS NULL OR status_pendamping = '' OR status_pendamping = 'Menunggu Review')
               AND (a.dosen_pembimbing_2 IS NOT NULL AND a.dosen_pembimbing_2 != '' AND a.dosen_pembimbing_2 != '-')
             ORDER BY created_at DESC LIMIT 1
            ) AS tgl_pending_pendamping,
            (SELECT MAX(created_at) FROM siakad_skripsi_bimbingan_log 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND catatan_dosen IS NOT NULL 
               AND catatan_dosen != ''
            ) AS tgl_terakhir_review,
            (SELECT MAX(created_at) FROM siakad_skripsi_chat 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND pengirim_type = 'dosen'
            ) AS tgl_terakhir_chat,
            (SELECT MAX(created_at) FROM siakad_skripsi_bimbingan_log 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND status_bimbingan = 'Agenda Dosen'
            ) AS tgl_terakhir_agenda,
            (SELECT MAX(verified_at) FROM siakad_skripsi_agenda_todo 
              WHERE id_judul_skripsi = a.id_judul_skripsi 
                AND verified_at IS NOT NULL
             ) AS tgl_terakhir_todo,
            (SELECT COUNT(*) FROM siakad_skripsi_bimbingan_log 
             WHERE id_judul_skripsi = a.id_judul_skripsi 
               AND catatan_mahasiswa IS NOT NULL 
               AND catatan_mahasiswa != '') AS total_bimbingan,
            (SELECT ns.sidang_hasil FROM siakad_skripsi_nilai_sempro ns 
             WHERE ns.id_judul_skripsi = a.id_judul_skripsi 
             AND ns.id_penilai = a.dosen_pembimbing_1 LIMIT 1) AS hasil_sidang");
		$this->db->from('siakad_skripsi_pengajuan_judul a');
		$this->db->join('mahasiswa_pt m', 'a.nipd = m.nipd');
		$this->db->join('mahasiswa b', 'm.id_pd = b.id_pd');
		$this->db->group_start();
		$this->db->where('a.dosen_pembimbing_1', $id_ptk);
		$this->db->or_where('a.dosen_pembimbing_2', $id_ptk);
		$this->db->group_end();
        $this->db->where('m.id_jns_keluar IS NULL', NULL, FALSE);
        $this->db->where('a.status_sk !=', 3);
        $this->db->order_by('m.nipd', 'DESC');
        return $this->db->get()->result();
    }

    // --- Agenda Bimbingan Methods ---

    public function get_agenda_aktif($id_judul_skripsi)
    {
        $this->db->where([
            'id_judul_skripsi' => $id_judul_skripsi,
            'initiator_type' => 'dosen',
            'status_bimbingan' => 'Agenda Dosen'
        ]);
        $this->db->order_by('id_log', 'DESC');
        return $this->db->get('siakad_skripsi_bimbingan_log')->row();
    }

    public function get_todo_by_log($id_log)
    {
        $this->db->where('id_log', $id_log);
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('siakad_skripsi_agenda_todo')->result();
    }

    public function save_agenda_log($data, $todo_items)
    {
        $data = $this->security->xss_clean($data);
        $this->db->trans_start();

        $this->db->insert('siakad_skripsi_bimbingan_log', $data);
        $id_log = $this->db->insert_id();

        if (!empty($todo_items)) {
            foreach ($todo_items as $k => $item) {
                $todo_items[$k]['id_log'] = $id_log;
                $todo_items[$k]['id_judul_skripsi'] = $data['id_judul_skripsi'];
            }
            $this->db->insert_batch('siakad_skripsi_agenda_todo', $todo_items);
        }

        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_log : false;
    }

    public function update_todo_item($id_todo, $data)
    {
        $data = $this->security->xss_clean($data);
        $this->db->where('id_todo', $id_todo);
        $this->db->update('siakad_skripsi_agenda_todo', $data);
        return $this->db->affected_rows();
    }

    public function update_todo_list($id_log, $id_judul_skripsi, $items)
    {
        $this->db->trans_start();
        
        // Remove existing items that are not verified/done by student yet or just remove all and re-insert carefully
        // For simplicity of edit: Delete all unverified items and reinsert the provided list.
        // Actually, if we just replace the whole list:
        $this->db->where('id_log', $id_log);
        $this->db->delete('siakad_skripsi_agenda_todo');

        if (!empty($items)) {
            foreach ($items as &$item) {
                $item['id_log'] = $id_log;
                $item['id_judul_skripsi'] = $id_judul_skripsi;
            }
            $this->db->insert_batch('siakad_skripsi_agenda_todo', $items);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function close_agenda($id_log, $user_id)
    {
        $this->db->where('id_log', $id_log);
        $this->db->update('siakad_skripsi_bimbingan_log', [
            'status_bimbingan' => 'Agenda Selesai',
            'reviewed_by_pendamping' => $user_id, // Reuse this field to store closer ID
            'reviewed_at_pendamping' => date('Y-m-d H:i:s')
        ]);
        return $this->db->affected_rows();
    }

    public function get_agenda_arsip($id_judul_skripsi)
    {
        $this->db->where([
            'id_judul_skripsi' => $id_judul_skripsi,
            'initiator_type'   => 'dosen',
            'status_bimbingan' => 'Agenda Selesai'
        ]);
        $this->db->order_by('id_log', 'DESC');
        return $this->db->get('siakad_skripsi_bimbingan_log')->result();
    }
}
