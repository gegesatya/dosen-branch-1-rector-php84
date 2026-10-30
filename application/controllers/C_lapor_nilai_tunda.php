<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_lapor_nilai_tunda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lapor_nilai_tunda_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$output['judul_besar'] = 'Laporan Sudah Mengisi Nilai Di Portal Tunda ';
		$output['judul_kecil'] = 'Tahun Ajaran ' . $this->session->userdata('setting')->id_smt_tunda;
		$output['menu_aktif'] = 'm_lapor_nilai_tunda';
		$output['parent_tunda'] = 'active open';
		$output['content'] = 'lapor_nilai_tunda/lapor_nilai_tunda_view';
		$output['after_page'] = 'lapor_nilai_tunda/lapor_nilai_tunda_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Lapor_nilai_tunda_model->get_datatables();
		$data = [];
		$no = $_POST['start'];

		// Batch fetch untuk mengeliminasi N+1 Query
		$detail_map = [];
		$nilai_map  = [];

		if (!empty($list)) {
			$jadwal_ids = [];
			foreach ($list as $item) {
				if (!empty($item->id)) {
					$jadwal_ids[] = $item->id;
				}
			}

			if (!empty($jadwal_ids)) {
				// 1. Batch fetch detail kelas & mata kuliah dalam 1 query
				$details = $this->db->select('id_jadwal, nm_kls, nm_mk')
					->where_in('id_jadwal', $jadwal_ids)
					->get('siakad_jadwal_detail')
					->result();

				foreach ($details as $d) {
					$detail_map[$d->id_jadwal][] = '[' . $d->nm_kls . '] ' . $d->nm_mk;
				}

				// 2. Batch fetch seluruh hitungan nilai huruf (A, B+, B, C+, C, D, E) dalam 1 query agregasi
				$nilai_stats = $this->db->select("
					id_jadwal,
					SUM(CASE WHEN nilai_huruf = 'A' THEN 1 ELSE 0 END) AS count_a,
					SUM(CASE WHEN nilai_huruf = 'B+' THEN 1 ELSE 0 END) AS count_b_plus,
					SUM(CASE WHEN nilai_huruf = 'B' THEN 1 ELSE 0 END) AS count_b,
					SUM(CASE WHEN nilai_huruf = 'C+' THEN 1 ELSE 0 END) AS count_c_plus,
					SUM(CASE WHEN nilai_huruf = 'C' THEN 1 ELSE 0 END) AS count_c,
					SUM(CASE WHEN nilai_huruf = 'D' THEN 1 ELSE 0 END) AS count_d,
					SUM(CASE WHEN nilai_huruf = 'E' THEN 1 ELSE 0 END) AS count_e
				", FALSE)
				->where_in('id_jadwal', $jadwal_ids)
				->group_by('id_jadwal')
				->get('siakad_nilai_baru')
				->result();

				foreach ($nilai_stats as $ns) {
					$nilai_map[$ns->id_jadwal] = $ns;
				}
			}
		}

		foreach ($list as $rg) {
			$html = '
					<a href="javascript:void()" class="btn btn-sm btn-primary"
						onclick="edit_skema_nilai(' . "'" . $rg->id . "'" . ')">
						<i class="icon-pencil"></i> Edit
					</a>
					';

			// Ambil detail jadwal dari map in-memory
			if (!empty($detail_map[$rg->id])) {
				$kk = implode('<br>', $detail_map[$rg->id]);
			} else {
				$kk = '-';
			}

			// Ambil hitungan nilai dari map in-memory
			$ns = $nilai_map[$rg->id] ?? null;
			$count_nilai_a      = $ns ? (int)$ns->count_a : 0;
			$count_nilai_b_plus = $ns ? (int)$ns->count_b_plus : 0;
			$count_nilai_b      = $ns ? (int)$ns->count_b : 0;
			$count_nilai_c_plus = $ns ? (int)$ns->count_c_plus : 0;
			$count_nilai_c      = $ns ? (int)$ns->count_c : 0;
			$count_nilai_d      = $ns ? (int)$ns->count_d : 0;
			$count_nilai_e      = $ns ? (int)$ns->count_e : 0;

			// Format Waktu, Hari & Kategori Jadwal
			$hari = !empty($rg->nama_hari) ? $rg->nama_hari : '-';
			$jam  = substr($rg->jam_mulai, 0, 5) . ' - ' . substr($rg->jam_selesai, 0, 5);
			$kategori = !empty($rg->nm_kategori_jadwal) ? $rg->nm_kategori_jadwal : 'Perkuliahan Reguler';

			// Style badge kategori dengan kontras tinggi dan mudah dibaca
			$badge_style = 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;'; // Default Reguler (Blue)
			if (stripos($kategori, 'PKL') !== false || stripos($kategori, 'MBKM') !== false || stripos($kategori, 'Magang') !== false) {
				$badge_style = 'background: #fefce8; color: #854d0e; border: 1px solid #fef08a;'; // PKL/MBKM (Amber)
			} elseif (stripos($kategori, 'Paralel') !== false) {
				$badge_style = 'background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff;'; // Paralel (Purple)
			} elseif (stripos($kategori, 'Skripsi') !== false || stripos($kategori, 'Akhir') !== false) {
				$badge_style = 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;'; // Skripsi (Green)
			}

			$col_jadwal = '<div style="font-weight: 700; color: #0f172a; font-size: 13.5px; line-height: 1.35;">' . htmlspecialchars($rg->tag_name) . '</div>'
				. '<div style="margin-top: 4px; color: #475569; font-size: 12px; line-height: 1.5;">'
				. '<i class="fa fa-clock-o text-primary" style="margin-right: 4px;"></i>' . $hari . ', ' . $jam
				. '</div>'
				. '<div style="margin-top: 5px;">'
				. '<span style="' . $badge_style . ' font-size: 11.5px; font-weight: 600; padding: 2.5px 8px; border-radius: 4px; display: inline-block; letter-spacing: 0.2px;">'
				. '<i class="fa fa-tag" style="margin-right: 4px; font-size: 10.5px; opacity: 0.9;"></i>' . htmlspecialchars($kategori)
				. '</span>'
				. '</div>';

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $col_jadwal;
			$row[] = $kk;

			$row[] = $count_nilai_a;
			$row[] = $count_nilai_b_plus;
			$row[] = $count_nilai_b;
			$row[] = $count_nilai_c_plus;
			$row[] = $count_nilai_c;
			$row[] = $count_nilai_d;
			$row[] = $count_nilai_e;
			$row[] = $rg->is_lapor_nilai == 0 ? 'Belum Lapor Nilai dan Belum Publish' : 'Sudah Lapor Nilai dan Sudah Publish';

			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lapor_nilai_tunda_model->count_all(),
			"recordsFiltered" => $this->Lapor_nilai_tunda_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	private function hitung_nilai($id_jadwal, $nilai)
	{
		return $this->db->select('count(nilai_huruf) as jumlah')
			->from('siakad_nilai_baru')
			->where(['id_jadwal' => $id_jadwal, 'nilai_huruf' => $nilai])->get()->row()->jumlah;
	}

	public function ajax_edit($id)
	{
		$data = $this->Lapor_nilai_tunda_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_skema_edit($id_kls)
	{
		$data = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
		$id_mk = $data->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;
		echo json_encode($data);
	}

	public function ajax_update()
	{
		$this->_validate_lapor_nilai();
		$user = $this->ion_auth->user()->row();
		$data = [
			'is_lapor_nilai' => $this->input->post('is_lapor_nilai'),
			'tanggal_lapor_nilai' =>	date('Y-m-d H:i:s')
		];

		$data = $this->security->xss_clean($data);

		$this->Lapor_nilai_tunda_model->update(['id' => $this->input->post('id')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate_lapor_nilai()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$is_lapor_nilai = $this->input->post('is_lapor_nilai');

		if ($is_lapor_nilai == '') {
			$data['inputerror'][] = 'is_lapor_nilai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
