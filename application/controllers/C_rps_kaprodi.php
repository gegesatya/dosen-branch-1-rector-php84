<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class C_rps_kaprodi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rps_kaprodi_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;

		$output['judul_besar'] = 'Kelas dan Dosen Pengampu ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_rps_kaprodi';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'rps_kaprodi/rps_kaprodi_view';
		$output['after_page'] = 'rps_kaprodi/rps_kaprodi_after_page';


		$this->load->view('master_page', $output);
	}

	public function ajax_list($angkatan)
	{
		$list = $this->Rps_kaprodi_model->get_datatables($angkatan);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$tersedia_rps = $html_preview = $html_kontrak_kuliah = $html_rancangan_tugas = "";
			$html = '<a class="btn btn-default blue-stripe btn-xs btn-block" 
						href="javascript:void(0)" 
						onclick="edit_evaluasi(' . "'" . $l->id . "'" . ')">
						<i class="glyphicon glyphicon-pencil"></i> Evaluasi
					</a>';

			if ($l->id_smt < 20242) {
				$cek_rps = $this->db->where('id_jadwal', $l->id)->get('siakad_rps_mk_prodi');
				if ($cek_rps->num_rows() > 0) {
					$html_preview = '<a href="' . site_url('C_daftar_rps_lama/preview_rps/' . md5($cek_rps->row()->id_rps)) . '" class="btn btn-default btn-sm btn-block margin-bottom-5" target="_new"  title="Preview RPS">
					Lihat RPS </a>';
				}
			} else {
				$cek_rps = $this->db->where('id_jadwal', $l->id)->get('siakad_new_rps_mk_prodi');

				if ($cek_rps->num_rows() > 0) {
					$html_preview = '<a href="' . site_url('C_rps_obsolete/preview_rps_baru/' . md5($cek_rps->row()->id_rps)) . '" class="btn btn-default btn-sm btn-block margin-bottom-5" target="_new"  title="Preview RPS">
					RPS</a>';
					$html_rancangan_tugas = '<a href="' . site_url('C_rps_obsolete/preview_rps_rancangan_tugas/' . md5($cek_rps->row()->id_rps)) . '" class="btn btn-default btn-sm btn-block margin-bottom-5" target="_new"  title="Preview RPS">
					Rancangan Tugas</a>';

					$html_kontrak_kuliah = '<a href="' . site_url('C_rps_obsolete/preview_kontrak/' . md5($cek_rps->row()->id_rps)) . '" class="btn btn-default btn-sm btn-block margin-bottom-5" target="_new"  title="Preview RPS">
					Kontrak Pembelajaran</a>';
				}
			}

			$html_pdf_nilai = "<a href='" . site_url() . "C_summary_nilai/cetak_pdf_nilai/" . $l->id . "/" . $l->id_smt . "' target='new' class='btn btn-sm btn-default green-stripe btn-block margin-bottom-5'><i class='fa fa-download'></i> Download Nilai</a>";
			$html_pdf_absen = "<a href='" . site_url() . "C_summary_kehadiran/cetak_pdf_absen/" . $l->id . "/" . $l->id_smt . "' target='new' class='btn btn-sm btn-default green-stripe btn-block'><i class='fa fa-download'></i> Download Absen</a>";

			if ($cek_rps->num_rows() > 0) {
				$tersedia_rps = "<span class='label label-success'>RPS untuk kelas kuliah ini sudah tersedia.</span>";
			} else {
				$tersedia_rps = "<span class='label label-danger'>Belum menerapkan RPS pada kelas ini.</span>";
			}

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $l->nm_smt;
			$row[] = $l->nidn . '<br>' . $l->nm_ptk;
			$row[] = '<b>' . $l->tag_name . '</b><br><small><b>Hari : </b>' . $l->nama_hari . '</small>';
			$row[] = $tersedia_rps;
			if ($l->id_smt < 20242) {
				$row[] = $html_preview;
			} else {
				$row[] = $html_preview . $html_kontrak_kuliah . $html_rancangan_tugas;
			}

			$row[] = $html_pdf_nilai . $html_pdf_absen;


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Rps_kaprodi_model->count_all($angkatan),
			"recordsFiltered" => $this->Rps_kaprodi_model->count_filtered($angkatan),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function export_nilai()
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		$mhs = $this->db->select('mp.*, m.nm_pd, s.nm_lemb, mk.nm_mk, mk.sks_mk, mk.kode_mk, kk.nm_kls,
					coalesce(n.nilai_angka, 0) as nilai_angka, coalesce(n.nilai_indeks,0) as nilai_indeks, coalesce(n.nilai_huruf,"") as nilai_huruf')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
			->join('nilai n', 'n.nipd = mp.nipd')
			->join('siakad_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where_not_in('mp.nipd', 'SELECT nim FROM feeder_lulus_do', FALSE)
			->where(['n.id_smt' => $id_smt])
			->order_by('mk.kode_mk', 'ASC')
			->get()->result();


		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$style_col = [
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$style_row_tengah = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$sheet->setCellValue('A1', "Data Nilai/KRS PER Periode TA " . $this->session->userdata('tahun_ajaran'));
		$sheet->setCellValue('A2', "Program Studi : Semua Prodi");
		//$sheet->mergeCells('A1:E1');
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A2')->getFont()->setBold(true);

		$sheet->setCellValue('A3', "NO");
		$sheet->setCellValue('B3', "NIM");
		$sheet->setCellValue('C3', "NAMA");
		$sheet->setCellValue('D3', "PROGRAM STUDI");
		$sheet->setCellValue('E3', "KODE MK");
		$sheet->setCellValue('F3', "NAMA MK");
		$sheet->setCellValue('G3', "Bobot MK (SKS)");
		$sheet->setCellValue('H3', "N Angka");
		$sheet->setCellValue('I3', "N Huruf");
		$sheet->setCellValue('J3', "N Indeks");
		$sheet->setCellValue('K3', "Nama Kelas");


		$sheet->getStyle('A3')->applyFromArray($style_col);
		$sheet->getStyle('B3')->applyFromArray($style_col);
		$sheet->getStyle('C3')->applyFromArray($style_col);
		$sheet->getStyle('D3')->applyFromArray($style_col);
		$sheet->getStyle('E3')->applyFromArray($style_col);
		$sheet->getStyle('F3')->applyFromArray($style_col);
		$sheet->getStyle('G3')->applyFromArray($style_col);
		$sheet->getStyle('H3')->applyFromArray($style_col);
		$sheet->getStyle('I3')->applyFromArray($style_col);
		$sheet->getStyle('J3')->applyFromArray($style_col);
		$sheet->getStyle('K3')->applyFromArray($style_col);



		$no = 1;
		$numrow = 4;
		foreach ($mhs as $data) {
			$sheet->setCellValue('A' . $numrow, $no);
			$sheet->setCellValueExplicit('B' . $numrow, $data->nipd, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('C' . $numrow, $data->nm_pd, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('D' . $numrow, $data->nm_lemb, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('E' . $numrow, $data->kode_mk, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('F' . $numrow, $data->nm_mk, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('G' . $numrow, $data->sks_mk, DataType::TYPE_NUMERIC);
			$sheet->setCellValueExplicit('H' . $numrow, $data->nilai_angka, DataType::TYPE_NUMERIC);
			$sheet->setCellValueExplicit('I' . $numrow, $data->nilai_huruf, DataType::TYPE_STRING);
			$sheet->setCellValueExplicit('J' . $numrow, $data->nilai_indeks, DataType::TYPE_NUMERIC);
			$sheet->setCellValueExplicit('K' . $numrow, $data->nm_kls, DataType::TYPE_STRING);


			$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('F' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('G' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('H' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('I' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('J' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('K' . $numrow)->applyFromArray($style_row);


			$no++;
			$numrow++;
		}

		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(45);
		$sheet->getColumnDimension('D')->setWidth(45);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(45);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(15);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(15);



		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->setTitle("Pengabdian");

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="DATA NILAI_KRS PER PERIODE TA ' . $id_smt . '.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}
