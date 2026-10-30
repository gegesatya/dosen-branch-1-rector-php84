<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class E_skripsi_rekapitulasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_rekapitulasi_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$output['judul_besar'] = 'Kinerja Bimbingan TA ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_rekapitulasi';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = 'e_skripsi_rekapitulasi/e_skripsi_rekapitulasi_view';
		$output['after_page'] = 'e_skripsi_rekapitulasi/e_skripsi_rekapitulasi_after_page';
		$output['dd_status_penetapan_judul'] 	= $this->E_skripsi_rekapitulasi_model->f_status_penetapan_judul();

		$this->load->view('master_page', $output);
	}

	public function ajax_list($angkatan = '')
	{
		$id_sms = $this->session->userdata('prodi');
		$list = $this->E_skripsi_rekapitulasi_model->get_datatables($angkatan, 2);
		
		// Cek prodi apakah Akuntansi Perpajakan (id_sms: f28c79a3-18c1-4b40-b3af-b2bedc810e20)
		$is_akuntansi_perpajakan = ($id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20');

		$data = [];
		$no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
		foreach ($list as $rg) {
			$id_ptk = $rg->id_ptk;

			// 1. Pembimbing Utama (dosen_pembimbing_1)
			$pembimbing = $this->db->where(['dosen_pembimbing_1' => $id_ptk, 'id_sms' => $id_sms])
				->from('siakad_skripsi_pengajuan_judul')
				->get()->result();
			$pembimbing_aktif = 0;
			foreach ($pembimbing as $value) {
				$d = $this->db->where(['id_judul_skripsi' => $value->id_judul_skripsi, 'id_penilai' => $value->dosen_pembimbing_1, 'is_arsip' => 0])->get('siakad_skripsi_nilai_sempro');
				if ($d->num_rows()) {
					$pembimbing_aktif++;
				}
			}

			if ($is_akuntansi_perpajakan) {
				// Prodi Akuntansi Perpajakan: Pembimbing Utama, Penguji (penguji_1 / pembahas_1), Penguji 2 (penguji_2 / pembahas_2)

				// Penguji 1 (dosen_penguji_sempro_2 atau dosen_pembimbing_1 / disesuaikan penilai di siakad_skripsi_nilai_sempro)
				// Di prodi Akuntansi Perpajakan, pembimbing utama berperan ganda atau ada penguji khusus.
				// Mari hitung jumlah sebagai Penguji 1 (dosen_penguji_sempro_2)
				$penguji_1 = $this->db->where(['dosen_penguji_sempro_2' => $id_ptk, 'id_sms' => $id_sms])
					->from('siakad_skripsi_pengajuan_judul')
					->get()->result();
				$penguji_1_aktif = 0;
				foreach ($penguji_1 as $value) {
					$d = $this->db->where(['id_judul_skripsi' => $value->id_judul_skripsi, 'id_penilai' => $value->dosen_penguji_sempro_2, 'is_arsip' => 0])->get('siakad_skripsi_nilai_sempro');
					if ($d->num_rows()) {
						$penguji_1_aktif++;
					}
				}

				// Penguji 2 (dosen_penguji_sempro_3)
				$penguji_2 = $this->db->where(['dosen_penguji_sempro_3' => $id_ptk, 'id_sms' => $id_sms])
					->from('siakad_skripsi_pengajuan_judul')
					->get()->result();
				$penguji_2_aktif = 0;
				foreach ($penguji_2 as $value) {
					$d = $this->db->where(['id_judul_skripsi' => $value->id_judul_skripsi, 'id_penilai' => $value->dosen_penguji_sempro_3, 'is_arsip' => 0])->get('siakad_skripsi_nilai_sempro');
					if ($d->num_rows()) {
						$penguji_2_aktif++;
					}
				}

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $rg->nama_lengkap;
				$row[] = $pembimbing_aktif;   // Pembimbing Utama
				$row[] = $penguji_1_aktif;     // Penguji
				$row[] = $penguji_2_aktif;     // Penguji 2
				$row[] = $pembimbing_aktif + $penguji_1_aktif + $penguji_2_aktif; // Total

			} else {
				// Prodi Lainnya: Pembimbing Utama, Pembimbing Pendamping, Penguji

				// Pembimbing Pendamping (dosen_pembimbing_2)
				$pendamping = $this->db->where(['dosen_pembimbing_2' => $id_ptk, 'id_sms' => $id_sms])
					->from('siakad_skripsi_pengajuan_judul')
					->get()->result();
				$pendamping_aktif = 0;
				foreach ($pendamping as $value) {
					$d = $this->db->where(['id_judul_skripsi' => $value->id_judul_skripsi, 'id_penilai' => $value->dosen_pembimbing_2, 'is_arsip' => 0])->get('siakad_skripsi_nilai_sempro');
					if ($d->num_rows()) {
						$pendamping_aktif++;
					}
				}

				// Penguji (dosen_penguji_sempro_3)
				$penguji = $this->db->where(['dosen_penguji_sempro_3' => $id_ptk, 'id_sms' => $id_sms])
					->from('siakad_skripsi_pengajuan_judul')
					->get()->result();
				$penguji_aktif = 0;
				foreach ($penguji as $value) {
					$d = $this->db->where(['id_judul_skripsi' => $value->id_judul_skripsi, 'id_penilai' => $value->dosen_penguji_sempro_3, 'is_arsip' => 0])->get('siakad_skripsi_nilai_sempro');
					if ($d->num_rows()) {
						$penguji_aktif++;
					}
				}

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $rg->nama_lengkap;
				$row[] = $pembimbing_aktif;   // Pembimbing Utama
				$row[] = $pendamping_aktif;   // Pembimbing Pendamping
				$row[] = $penguji_aktif;       // Penguji
				$row[] = $pembimbing_aktif + $pendamping_aktif + $penguji_aktif; // Total
			}

			$data[] = $row;
		}

		$output = [
			"draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
			"recordsTotal" => $this->E_skripsi_rekapitulasi_model->count_all($angkatan, 2),
			"recordsFiltered" => $this->E_skripsi_rekapitulasi_model->count_filtered($angkatan, 2),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function view_data_pengajuan($id_judul_skripsi)
	{
		$judul_skripsi = $this->db->where('j.id_judul_skripsi', $id_judul_skripsi)
			->select('j.*, m.nm_pd, a.analisis_sks_lulus, a.analisis_sks_tidak_lulus, a.analisis_sks_total, a.analisis_ipk')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'j.nipd = mp.nipd', 'left')
			->join('mahasiswa m', 'mp.id_pd = m.id_pd', 'left')
			->join('siakad_skripsi_analisis a', 'a.nipd = j.nipd', 'left')
			->get()->row();


		echo json_encode([
			'nm_pd' => !empty($judul_skripsi->nm_pd) ? $judul_skripsi->nm_pd : '',
			'nipd' => !empty($judul_skripsi->nipd) ? $judul_skripsi->nipd : '',
			'id_smt' => !empty($judul_skripsi->id_smt) ? $judul_skripsi->id_smt : '',
			'komentar' => !empty($judul_skripsi->komentar) ? $judul_skripsi->komentar : '',
			'id_judul_skripsi' => !empty($judul_skripsi->id_judul_skripsi) ? $judul_skripsi->id_judul_skripsi : '',
			'judul_skripsi_1' => !empty($judul_skripsi->judul_skripsi_1) ? $judul_skripsi->judul_skripsi_1 : '',
			'judul_skripsi_2' => !empty($judul_skripsi->judul_skripsi_2) ? $judul_skripsi->judul_skripsi_2 : '',
			'judul_skripsi_3' => !empty($judul_skripsi->judul_skripsi_3) ? $judul_skripsi->judul_skripsi_3 : '',
			'target_selesai' => !empty($judul_skripsi->target_selesai) ? $judul_skripsi->target_selesai : '',
			'tanggal_diajukan' => !empty($judul_skripsi->tanggal_diajukan) ? $judul_skripsi->tanggal_diajukan : '',
			'analisis_sks_lulus' => !empty($judul_skripsi->analisis_sks_lulus) ? $judul_skripsi->analisis_sks_lulus : '',
			'analisis_sks_tidak_lulus' => !empty($judul_skripsi->analisis_sks_tidak_lulus) ? $judul_skripsi->analisis_sks_tidak_lulus : '',
			'analisis_sks_total' => !empty($judul_skripsi->analisis_sks_total) ? $judul_skripsi->analisis_sks_total : '',
			'analisis_ipk' => !empty($judul_skripsi->analisis_ipk) ? $judul_skripsi->analisis_ipk : '',
			'judul_ditetapkan' => !empty($judul_skripsi->judul_ditetapkan) ? $judul_skripsi->judul_ditetapkan : '',
			'tanggal_usulan_kaprodi' => !empty($judul_skripsi->tanggal_usulan_kaprodi) ? $judul_skripsi->tanggal_usulan_kaprodi : '',
			'dosen_pembimbing_1' => !empty($judul_skripsi->dosen_pembimbing_1) ? $judul_skripsi->dosen_pembimbing_1 : '',
			'dosen_pembimbing_2' => !empty($judul_skripsi->dosen_pembimbing_2) ? $judul_skripsi->dosen_pembimbing_2 : '',
			//'dosen_penguji' => $judul_skripsi->dosen_penguji,
			//'dosen_penguji_2' => $judul_skripsi->dosen_penguji_2,
			'status_penetapan_judul' => !empty($judul_skripsi->status_penetapan_judul) ? $judul_skripsi->status_penetapan_judul : ''

		]);
	}

	public function ajax_edit($id)
	{
		$data = $this->E_skripsi_rekapitulasi_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('judul_ditetapkan') == '') {
			$data['inputerror'][] = 'judul_ditetapkan';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('status_penetapan_judul') == '') {
			$data['inputerror'][] = 'status_penetapan_judul';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_pembimbing_1') == '') {
			$data['inputerror'][] = 'dosen_pembimbing_1';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		/* if ($this->input->post('dosen_penguji') == '') {
			$data['inputerror'][] = 'dosen_penguji';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} */

		if ($this->input->post('tanggal_usulan_kaprodi') == '') {
			$data['inputerror'][] = 'tanggal_usulan_kaprodi';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}


	public function persetujuan_judul()
	{
		$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$data_judul_skripsi = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul');
		$user = $this->ion_auth->user()->row();
		$this->db->trans_start();

		if ($this->input->post('status_penetapan_judul') == 3) {

			$this->db->where(compact('id_judul_skripsi'))->update(
				'siakad_skripsi_pengajuan_judul',
				[
					'judul_ditetapkan' => $this->input->post('judul_ditetapkan'),
					'tanggal_usulan_kaprodi' => $this->input->post('tanggal_usulan_kaprodi'),
					'dosen_pembimbing_1' => $this->input->post('dosen_pembimbing_1'),
					'dosen_pembimbing_2' => $this->input->post('dosen_pembimbing_2'),
					//'dosen_penguji' => $this->input->post('dosen_penguji'),
					//'dosen_penguji_2' => $this->input->post('dosen_penguji_2'),
					'status_penetapan_judul' => $this->input->post('status_penetapan_judul'),
					'judul_ditetapkan' => $this->input->post('judul_ditetapkan'),
					'tanggal_disetujui' => date('Y-m-d H:i:s'),
				]
			);
		} else {
			$this->db->where(compact('id_judul_skripsi'))->update(
				'siakad_skripsi_pengajuan_judul',
				[
					'status_penetapan_judul' => $this->input->post('status_penetapan_judul'),
					'change_by' => $user->id,
					'change_date' => date('Y-m-d H:i:s')
				]
			);
		}


		$this->db->trans_complete();
		echo json_encode(["status" => TRUE]);
	}



	public function ajax_balas_pesan()
	{
		$user = $this->ion_auth->user()->row();
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$data_pengajuan_judul = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$catatan_dosen_wali_awal = $data_pengajuan_judul->komentar;
		$catatan_dosen_wali = '';

		/* if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			} else {
				$catatan_dosen_wali = '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			}
		} */

		if (!empty($this->input->post('catatan_dosen_wali'))) {
			$tgl = tgl_indo_short(date('Y-m-d'));
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<br><b>' . ucwords(strtolower((string)$user->first_name)) . '</b> <small> On ' . $tgl . '</small>: <br>' . $this->input->post('catatan_dosen_wali');
			} else {
				$catatan_dosen_wali = '<b>' . ucwords(strtolower((string)$user->first_name))  . '</b> <small> On ' . $tgl . '</small>: <br>' . $this->input->post('catatan_dosen_wali');
			}
		}

		$data = [
			'komentar' => $catatan_dosen_wali
		];

		$this->E_skripsi_rekapitulasi_model->update(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function view_pesan($id_judul_skripsi)
	{
		$data_rencana_studi = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$log_message = '';
		if (!empty($data_rencana_studi->komentar)) {
			$log_message =  $data_rencana_studi->komentar;
		}

		echo json_encode([
			'status_penetapan_judul' => $data_rencana_studi->status_penetapan_judul,
			'data_pesan' => $log_message
		]);
	}

	public function getDosen()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_rekapitulasi_model->get_dosen());
	}

	public function getDosenPenguji()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_rekapitulasi_model->get_dosen_penguji());
	}

	public function download_excel()
	{
		$spreadsheet = new Spreadsheet();
		$judul = $this->db->where(['j.id_sms' => $this->session->userdata('prodi')])
			->select('j.*, m.nm_pd')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd', 'left')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd', 'left')
			->get()->result();

		$array_nilai = [];
		$no = 1;
		foreach ($judul as $key => $value) {
			$nm_doping_1 = $nm_doping_2 = $nm_penguji_1 = $nm_penguji_2 = '-';
			if (!empty($value->dosen_pembimbing_1)) {
				$doping_1 = $this->db->where('sd.id_ptk', $value->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
				$nm_doping_1 = $doping_1->nama_lengkap;
			}
			if (!empty($value->dosen_pembimbing_2)) {
				$doping_2 = $this->db->where('sd.id_ptk', $value->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
				$nm_doping_2 = $doping_2->nama_lengkap;
			}
			if (!empty($value->dosen_penguji_sempro_1)) {
				$penguji_sempro_1 = $this->db->where('sd.id_ptk', $value->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
				$nm_penguji_1 = $penguji_sempro_1->nama_lengkap;
			}

			if (!empty($value->dosen_penguji_sempro_2)) {
				$penguji_sempro_2 = $this->db->where('sd.id_ptk', $value->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
				$nm_penguji_2 = $penguji_sempro_2->nama_lengkap;
			}

			$array_judul[] = [
				$no,
				$value->nipd,
				$value->nm_pd,
				$value->tanggal_diajukan,
				$value->tanggal_usulan_kaprodi,
				$value->judul_skripsi_1,
				$value->judul_skripsi_2,
				$value->judul_skripsi_3,
				$value->target_selesai,
				$value->judul_ditetapkan,
				$nm_doping_1,
				$nm_doping_2,
				$nm_penguji_1,
				$nm_penguji_2
			];

			$no++;
		}

		//$this->excel->getActiveSheet()->setTitle($jadwal->tag_name);
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DATA PENGAJUAN JUDUL TUGAS AKHIR ');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A3', '');
		$sheet->getStyle('A3')->getFont()->setSize(13)->setBold(true);

		/* $sheet->setCellValue('F1', 'PENTING :');
		$sheet->getStyle('F1')->getFont()->setSize(16)->setBold(true);


		$sheet->setCellValue('F2', 'a. Dilarang merubah text pada kolom yang di hidden');
		$sheet->getStyle('F2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('F3', 'b. Dilarang merubah posisi row dan kolom');
		$sheet->getStyle('F3')->getFont()->setSize(13)->setBold(true); */

		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'nim');
		$sheet->setCellValue('C6', 'nama');
		$sheet->setCellValue('D6', 'tanggal_diajukan');
		$sheet->setCellValue('E6', 'tanggal_usulan_kaprodi');
		$sheet->setCellValue('F6', 'judul_skripsi_1');
		$sheet->setCellValue('G6', 'judul_skripsi_2');
		$sheet->setCellValue('H6', 'judul_skripsi_3');
		$sheet->setCellValue('I6', 'target_selesai');
		$sheet->setCellValue('J6', 'judul_ditetapkan');
		$sheet->setCellValue('K6', 'dosen_pembimbing_1');
		$sheet->setCellValue('L6', 'dosen_pembimbing_2');
		$sheet->setCellValue('M6', 'dosen_penguji_1');
		$sheet->setCellValue('N6', 'dosen_penguji_2');
		/* $sheet->setCellValue('O6', 'PRD');
		$sheet->setCellValue('P6', 'PRE');
		$sheet->setCellValue('Q6', 'NILAI_FINAL');
		$sheet->setCellValue('R6', 'NILAI_HURUF');
		$sheet->setCellValue('S6', 'NILAI_INDEX');
		$sheet->setCellValue('T6', 'TOKEN_EXCEL');
		$sheet->setCellValue('U6', 'ID_KLS'); */


		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(40);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(55);
		$sheet->getColumnDimension('G')->setWidth(55);
		$sheet->getColumnDimension('H')->setWidth(55);
		$sheet->getColumnDimension('I')->setWidth(15);
		$sheet->getColumnDimension('J')->setWidth(55);
		$sheet->getColumnDimension('K')->setWidth(40);
		$sheet->getColumnDimension('L')->setWidth(40);
		$sheet->getColumnDimension('M')->setWidth(40);
		$sheet->getColumnDimension('N')->setWidth(40);
		/* $sheet->getColumnDimension('O')->setWidth(15);
		$sheet->getColumnDimension('P')->setWidth(15);
		$sheet->getColumnDimension('Q')->setWidth(15);
		$sheet->getColumnDimension('R')->setWidth(15);
		$sheet->getColumnDimension('S')->setWidth(15);
		$sheet->getColumnDimension('T')->setWidth(40);
		$sheet->getColumnDimension('U')->setWidth(40); */

		$sheet->getStyle('A6:U6')->getFont()->setSize(11)->setBold(true);

		$style_horizontal_center = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$style_horizontal_left = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$style_outline = [
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];


		$sheet->fromArray($array_judul, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray($style_horizontal_center); //no
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray($style_horizontal_left); //id_reg_pd
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray($style_horizontal_left); //id_reg_pd
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray($style_horizontal_left); //nim
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray($style_horizontal_left); //nama
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("J7:J" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("K7:K" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("L7:L" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("M7:M" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("N7:N" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("O7:O" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("P7:P" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("Q7:Q" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("R7:R" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("S7:S" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("T7:T" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("U7:U" . $highestRow)->applyFromArray($style_horizontal_left);
		//$sheet->getStyle("V7:V".$highestRow)->applyFromArray($style_horizontal_left);

		$sheet->getStyle("A6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("B6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("C6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("D6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("E6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("F6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("G6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("H6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("I6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("J6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("K6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("L6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("M6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("N6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("O6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("P6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("Q6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("R6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("S6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("T6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("U6")->applyFromArray($style_horizontal_center);
		//$sheet->getStyle("V6")->applyFromArray($style_horizontal_center);

		/* $sheet->getColumnDimension('B')->setVisible(false);
		$sheet->getColumnDimension('C')->setVisible(false);
		$sheet->getColumnDimension('T')->setVisible(false);
		$sheet->getColumnDimension('U')->setVisible(false); */

		$writer = new Xlsx($spreadsheet);
		$nm_file = substr('Daftar Pengajuan Judul Tugas Akhir', 0, 150);
		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/laporan_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'laporan_excel/' . $filename);
		force_download($filename, $data);
	}
}
