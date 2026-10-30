<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_monitoring_sk_lama extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		if ($this->ion_auth->user()->row()->email != 'gege.satya@wbi.ac.id') {
			show_404();
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}


	}

	public function index()
	{
		$output['judul_besar'] = 'Monitoring SK Tugas Akhir';
		$output['judul_kecil'] = 'Lebih dari 1 Tahun';
		$output['menu_aktif'] = 'm_monitoring_sk_lama';
		$output['parent_admin'] = 'active open';
		
		$query = "
			SELECT 
				m.nm_pd, 
				mpt.nipd, 
				ta.tanggal_sk, 
				ta.status_sk, 
				IFNULL((SELECT ipk FROM kuliah_mahasiswa WHERE nipd = ta.nipd ORDER BY id_smt DESC LIMIT 1), '0') as ipk,
				(SELECT sempro_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_sempro,
				(SELECT semhas_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_semhas,
				(SELECT sidang_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_sidang,
				IFNULL(d1.nm_ptk, '-') as pembimbing_1,
				IFNULL(d2.nm_ptk, '-') as penguji_2,
				IFNULL(d3.nm_ptk, '-') as penguji_3
			FROM siakad_skripsi_pengajuan_judul ta 
			JOIN mahasiswa_pt mpt ON ta.nipd = mpt.nipd 
			JOIN mahasiswa m ON mpt.id_pd = m.id_pd 
			LEFT JOIN siakad_dosen d1 ON ta.dosen_pembimbing_1 = d1.id_ptk
			LEFT JOIN siakad_dosen d2 ON ta.dosen_penguji_sempro_2 = d2.id_ptk
			LEFT JOIN siakad_dosen d3 ON ta.dosen_penguji_sempro_3 = d3.id_ptk
			WHERE ta.status_sk IN (1, 2, 3) 
			  AND ta.tanggal_sk < DATE_SUB(NOW(), INTERVAL 1 YEAR) 
			  AND mpt.id_jns_keluar IS NULL
			  AND NOT EXISTS (
			      SELECT 1 FROM siakad_skripsi_nilai_sempro 
			      WHERE id_judul_skripsi = ta.id_judul_skripsi 
			      AND id_penilai = ta.dosen_pembimbing_1
			      AND (sidang_hasil = 'LULUS' OR sidang_hasil = 'lulus' OR sidang_hasil = 'Lulus')
			  )
			ORDER BY pembimbing_1 ASC, ta.tanggal_sk ASC
		";
		$output['result'] = $this->db->query($query)->result();

		$output['content'] = 'monitoring_sk_lama/monitoring_sk_lama_view';

		$this->load->view('master_page', $output);
	}

	public function download_excel()
	{
		$id_sms = $this->session->userdata('prodi');
		$nm_prodi = $this->session->userdata('nm_prodi') ?: '';
        
		$query = "
			SELECT 
				m.nm_pd, 
				mpt.nipd, 
				ta.tanggal_sk, 
				ta.status_sk, 
				IFNULL((SELECT ipk FROM kuliah_mahasiswa WHERE nipd = ta.nipd ORDER BY id_smt DESC LIMIT 1), '0') as ipk,
				(SELECT sempro_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_sempro,
				(SELECT semhas_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_semhas,
				(SELECT sidang_hasil FROM siakad_skripsi_nilai_sempro WHERE id_judul_skripsi = ta.id_judul_skripsi AND id_penilai = ta.dosen_pembimbing_1 LIMIT 1) as status_sidang,
				IFNULL(d1.nm_ptk, '-') as pembimbing_1,
				IFNULL(d2.nm_ptk, '-') as penguji_2,
				IFNULL(d3.nm_ptk, '-') as penguji_3
			FROM siakad_skripsi_pengajuan_judul ta 
			JOIN mahasiswa_pt mpt ON ta.nipd = mpt.nipd 
			JOIN mahasiswa m ON mpt.id_pd = m.id_pd 
			LEFT JOIN siakad_dosen d1 ON ta.dosen_pembimbing_1 = d1.id_ptk
			LEFT JOIN siakad_dosen d2 ON ta.dosen_penguji_sempro_2 = d2.id_ptk
			LEFT JOIN siakad_dosen d3 ON ta.dosen_penguji_sempro_3 = d3.id_ptk
			WHERE ta.status_sk IN (1, 2, 3) 
			  AND ta.tanggal_sk < DATE_SUB(NOW(), INTERVAL 1 YEAR) 
			  AND mpt.id_jns_keluar IS NULL
			  AND NOT EXISTS (
			      SELECT 1 FROM siakad_skripsi_nilai_sempro 
			      WHERE id_judul_skripsi = ta.id_judul_skripsi 
			      AND id_penilai = ta.dosen_pembimbing_1
			      AND (sidang_hasil = 'LULUS' OR sidang_hasil = 'lulus' OR sidang_hasil = 'Lulus')
			  )
			ORDER BY pembimbing_1 ASC, ta.tanggal_sk ASC
		";

		$result = $this->db->query($query)->result();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'LAPORAN MAHASISWA SK TUGAS AKHIR > 1 TAHUN');
		$sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->setCellValue('A2', 'Program Studi: Semua Program Studi');

		$sheet->setCellValue('A4', 'No.');
		$sheet->setCellValue('B4', 'Nama Mahasiswa');
		$sheet->setCellValue('C4', 'NIM');
		$sheet->setCellValue('D4', 'Tanggal SK');
		$sheet->setCellValue('E4', 'Umur SK');
		$sheet->setCellValue('F4', 'IPK');
		$sheet->setCellValue('G4', 'Status Sempro');
		$sheet->setCellValue('H4', 'Status Semhas');
		$sheet->setCellValue('I4', 'Status Sidang');
		$sheet->setCellValue('J4', 'Pembimbing 1');
		$sheet->setCellValue('K4', 'Penguji 2');
		$sheet->setCellValue('L4', 'Penguji 3');

		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(10);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(35);
		$sheet->getColumnDimension('J')->setWidth(35);
		$sheet->getColumnDimension('K')->setWidth(35);

		$sheet->getStyle('A4:K4')->getFont()->setBold(true);

		$style_borders = [
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];
		$sheet->getStyle('A4:K4')->applyFromArray($style_borders);

		$no = 1;
		$row_idx = 5;
		foreach ($result as $row) {
			$sempro = empty($row->status_sempro) ? '-' : strtoupper((string)$row->status_sempro);
			$semhas = empty($row->status_semhas) ? '-' : strtoupper((string)$row->status_semhas);
			$sidang = empty($row->status_sidang) ? '-' : strtoupper((string)$row->status_sidang);

			$tgl_sk = new DateTime($row->tanggal_sk);
			$sekarang = new DateTime();
			$diff = $tgl_sk->diff($sekarang);
			$umur_sk = $diff->y . ' Thn ' . $diff->m . ' Bln';

			$sheet->setCellValue('A' . $row_idx, $no++);
			$sheet->setCellValue('B' . $row_idx, $row->nm_pd);
			$sheet->setCellValue('C' . $row_idx, $row->nipd);
			$sheet->setCellValue('D' . $row_idx, $row->tanggal_sk);
			$sheet->setCellValue('E' . $row_idx, $umur_sk);
			$sheet->setCellValue('F' . $row_idx, $row->ipk);
			$sheet->setCellValue('G' . $row_idx, $sempro);
			$sheet->setCellValue('H' . $row_idx, $semhas);
			$sheet->setCellValue('I' . $row_idx, $sidang);
			$sheet->setCellValue('J' . $row_idx, $row->pembimbing_1);
			$sheet->setCellValue('K' . $row_idx, $row->penguji_2);
			$sheet->setCellValue('L' . $row_idx, $row->penguji_3);
			
			$sheet->getStyle("A{$row_idx}:L{$row_idx}")->applyFromArray($style_borders);
			$row_idx++;
		}

		$writer = new Xlsx($spreadsheet);
		$filename = 'Laporan_SK_TA_Lebih_Dari_1_Tahun.xlsx';
		
		// Bersihkan output buffer jika ada sebelumnya (penting untuk download Excel)
		if (ob_get_length()) {
			ob_end_clean();
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output');
		exit();
	}
}
