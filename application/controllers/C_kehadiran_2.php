<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xls\ErrorCode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_kehadiran_2 extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Kehadiran_2_model', 'Kehadiran_2_model');
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
		/* if ($this->realisasi_16()) { */
		$output['judul_besar'] 		= 'Presensi 2 (Pertemuan 17 sd 30) ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_kehadiran_2';
		$output['parent_2']			= 'active open';
		$output['content'] 			= 'kehadiran_2/kehadiran_2_view';
		$output['after_page'] 		= 'kehadiran_2/kehadiran_2_after_page';
		$output['dd_kelas_kuliah'] 		= $this->Kehadiran_2_model->f_dropdown();
		$this->load->view('master_page', $output);
		/* } else {
			echo "Tidak bisa mengakses menu ini, silahkan mengisi data absen dari pertemuan 1 sd 16";
		} */
	}



	public function save_id_kls_session($id_kls)
	{
		$this->session->set_userdata('id_kls_session', $id_kls);
		echo json_encode($this->Kehadiran_2_model->f_pertemuan_1());
	}

	private function generate_html($i, $meet, $uniq)
	{
		$realisasi = $this->get_realisasi($i);
		//if ((int)$this->get_realisasi($i) === 1) {
		return "<a href='javascript:void(0)' onclick='edit_absen(\"" . $meet . "\", \"meet_$i\",\"" . $uniq . "\")'>" . $this->convert_icon($meet, $realisasi) . "</a>";
		//} else {
		//	return "";
		//}
	}

	public function ajax_list($id_kls = NULL)
	{
		/* if ($this->realisasi_16($id_kls)) { */
		$list = $this->Kehadiran_2_model->get_datatables($id_kls);
		$data = [];
		$no = $_POST['start'];

		foreach ($list as $l) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $l->nipd . '<br>' . $l->nm_pd;
			$_meets = [
				$l->meet_1, $l->meet_2, $l->meet_3, $l->meet_4, $l->meet_5,
				$l->meet_6, $l->meet_7, $l->meet_8, $l->meet_9, $l->meet_10, $l->meet_11,
				$l->meet_12, $l->meet_13, $l->meet_14, $l->meet_15, $l->meet_16,
				$l->meet_17, $l->meet_18, $l->meet_19, $l->meet_20, $l->meet_21,
				$l->meet_22, $l->meet_23, $l->meet_24, $l->meet_25, $l->meet_26, $l->meet_27,
				$l->meet_28, $l->meet_29, $l->meet_30
			];

			for ($i = 17; $i <= 30; $i++) {
				$j = $i - 1;
				$row[] = $this->generate_html($i, $_meets[$j], $l->uniq);
			}

			$row[] = $l->persen_hadir;
			//$row[] = $this->realisasi_16($id_kls);

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Kehadiran_2_model->count_all($id_kls),
			"recordsFiltered" => $this->Kehadiran_2_model->count_filtered($id_kls),
			"data" => $data,
		];

		echo json_encode($output);
		/* } else {
		} */
	}

	private function get_realisasi($pertemuan)
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$realisasi = $this->db->where(['id_jadwal' => $id_jadwal, 'pertemuan' => $pertemuan])
			->get('siakad_rencana_pertemuan');
		if ($realisasi->num_rows() > 0) {
			return $realisasi->row()->is_realisasi;
		} else {
			return 0;
		}
	}

	private function convert_icon($status, $realisasi)
	{
		if ($realisasi == 1) {
			if (!empty($status)) {
				if ($status == 'H') {
					return "<i class='glyphicon glyphicon-ok'></i>";
				} elseif ($status == 'A') {
					return "<i class='glyphicon glyphicon-remove text-danger'></i>";
				}
			} else {
				return '?';
			}
		} else {
			return '';
		}
	}

	public function sinkronisasi()
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$nipd_nilai = $this->db->where('sn.id_jadwal', $id_jadwal)
			->from('siakad_nilai_baru sn')
			->get();

		$nipd_absen_detail = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_absen_detail_baru');

		$arr_nilai = [];
		$arr_absen = [];

		foreach ($nipd_nilai->result() as $key => $value) {
			$arr_nilai[] = $value->nipd;
		}

		foreach ($nipd_absen_detail->result() as $key => $value) {
			$arr_absen[] = $value->nipd;
		}

		$a = array_diff($arr_nilai, $arr_absen);

		$dataku = [];

		if ($nipd_nilai->num_rows() > $nipd_absen_detail->num_rows()) {
			foreach ($a as $key => $value) {
				$dataku[] = ['id_jadwal' => $id_jadwal, 'nipd' => $value];
			}

			$this->db->insert_batch('siakad_absen_detail_baru', $dataku);
		}

		echo json_encode(['status' => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate();

		$id_jadwal 			= $this->session->userdata('id_kls_session');
		$status_hadir 		= $this->input->post('status_hadir');
		$meet 				= $this->input->post('pertemuan');

		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');


		$data = [$meet => $status_hadir, 'change_by' => $change_by, 'change_date' => $change_date];

		$this->Kehadiran_2_model->update(['id_jadwal' => $id_jadwal], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function edit_status_kehadiran($tipe, $pertemuan, $uniq)
	{
		if ($tipe == 0) {
			$data = [$pertemuan => 'A'];
			$this->Kehadiran_2_model->update_single(['uniq' => $uniq], $data);
		} else {
			$data = [$pertemuan => 'H'];
			$this->Kehadiran_2_model->update_single(['uniq' => $uniq], $data);
		}
	}

	public function download_excel()
	{
		$spreadsheet = new Spreadsheet();
		$id_jadwal = $this->session->userdata('id_kls_session');

		$jadwal = $this->db->where('id', $id_jadwal)
			->from('siakad_jadwal')
			->get()->row();

		$data_absen = $this->db->select('*')
			->from('siakad_absen_detail_baru a')
			->join('mahasiswa_pt mp', 'mp.nipd = a.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where('a.id_jadwal', $id_jadwal)
			->order_by('nm_pd')
			->get()->result();

		$array_nilai = [];
		$no = 1;
		foreach ($data_absen as $key => $value) {
			$array_nilai[] = [
				$no,
				$id_jadwal,
				$value->id_reg_pd,
				$value->nipd,
				$value->nm_pd,
				$value->meet_17,
				$value->meet_18,
				$value->meet_19,
				$value->meet_20,
				$value->meet_21,
				$value->meet_22,
				$value->meet_23,
				$value->meet_24,
				$value->meet_25,
				$value->meet_26,
				$value->meet_27,
				$value->meet_28,
				$value->meet_29,
				$value->meet_30,
				$value->uniq
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DATA ABSEN MAHASISWA ');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A3', $jadwal->tag_name);
		$sheet->getStyle('A3')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('F1', 'PENTING :');
		$sheet->getStyle('F1')->getFont()->setSize(16)->setBold(true);


		$sheet->setCellValue('F2', 'a. Dilarang merubah text pada kolom yang di hidden');
		$sheet->getStyle('F2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('F3', 'b. Dilarang merubah posisi row dan kolom');
		$sheet->getStyle('F3')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'id_jadwal');
		$sheet->setCellValue('C6', 'id_reg_pd');
		$sheet->setCellValue('D6', 'NIM');
		$sheet->setCellValue('E6', 'NAMA');
		$sheet->setCellValue('F6', 'P17');
		$sheet->setCellValue('G6', 'P18');
		$sheet->setCellValue('H6', 'P19');
		$sheet->setCellValue('I6', 'P20');
		$sheet->setCellValue('J6', 'P21');
		$sheet->setCellValue('K6', 'P22');
		$sheet->setCellValue('L6', 'P23');
		$sheet->setCellValue('M6', 'P24');
		$sheet->setCellValue('N6', 'P25');
		$sheet->setCellValue('O6', 'P26');
		$sheet->setCellValue('P6', 'P27');
		$sheet->setCellValue('Q6', 'P28');
		$sheet->setCellValue('R6', 'P29');
		$sheet->setCellValue('S6', 'P30');
		$sheet->setCellValue('T6', 'UNIQ');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(40);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(50);
		$sheet->getColumnDimension('F')->setWidth(5);
		$sheet->getColumnDimension('G')->setWidth(5);
		$sheet->getColumnDimension('H')->setWidth(5);
		$sheet->getColumnDimension('I')->setWidth(5);
		$sheet->getColumnDimension('J')->setWidth(5);
		$sheet->getColumnDimension('K')->setWidth(5);
		$sheet->getColumnDimension('L')->setWidth(5);
		$sheet->getColumnDimension('M')->setWidth(5);
		$sheet->getColumnDimension('N')->setWidth(5);
		$sheet->getColumnDimension('O')->setWidth(5);
		$sheet->getColumnDimension('P')->setWidth(5);
		$sheet->getColumnDimension('Q')->setWidth(5);
		$sheet->getColumnDimension('R')->setWidth(5);
		$sheet->getColumnDimension('S')->setWidth(5);
		$sheet->getColumnDimension('T')->setWidth(5);

		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);

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


		$sheet->fromArray($array_nilai, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("J7:J" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("K7:K" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("L7:L" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("M7:M" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("N7:N" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("O7:O" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("P7:P" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("Q7:Q" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("R7:R" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("S7:S" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("T7:T" . $highestRow)->applyFromArray($style_horizontal_left);

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


		$sheet->getColumnDimension('B')->setVisible(false);
		$sheet->getColumnDimension('C')->setVisible(false);
		$sheet->getColumnDimension('T')->setVisible(false);

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', $jadwal->tag_name);
		$nm_file = substr('Absen ' . $f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/laporan_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'laporan_excel/' . $filename);
		force_download($filename, $data);
	}

	public function save_excel()
	{
		$path = getcwd() . '/laporan_excel/uploads/';
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'xlsx|xls';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ($this->upload->do_upload('userfile')) {
			$data = ['upload_data' => $this->upload->data()];
			$import_xls_file = $data['upload_data']['file_name'];
			$a = $this->edit_excel($path . $import_xls_file);
			$b = explode('|', $a);
			echo json_encode([
				'status' => TRUE,
				'message' => $b[0],
				'tipe' => $b[1],
				'id_kls' => $this->session->userdata('id_kls_session')
			]);
		} else {
			$data = [];
		}
	}

	private function edit_excel($file_excel)
	{

		$pesan = $tipe = '';
		$this->load->model('Import_model', 'import');
		try {

			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

			$reader->setLoadAllSheets();
			$objPHPExcel = $reader->load($file_excel);

			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
			$arrayCount = count($allDataInSheet);
			$flag = 0;
			$createArray = [
				'id_jadwal',
				'id_reg_pd',
				'NIM',
				'NAMA',
				'P17',
				'P18',
				'P19',
				'P20',
				'P21',
				'P22',
				'P23',
				'P24',
				'P25',
				'P26',
				'P27',
				'P28',
				'P29',
				'P30',
				'UNIQ'
			];

			$makeArray = [
				'id_jadwal' => 'id_jadwal',
				'id_reg_pd' => 'id_reg_pd',
				'NIM' => 'NIM',
				'NAMA' => 'NAMA',
				'P17' => 'P17',
				'P18' => 'P18',
				'P19' => 'P19',
				'P20' => 'P20',
				'P21' => 'P21',
				'P22' => 'P22',
				'P23' => 'P23',
				'P24' => 'P24',
				'P25' => 'P25',
				'P26' => 'P26',
				'P27' => 'P27',
				'P28' => 'P28',
				'P29' => 'P29',
				'P30' => 'P30',
				'UNIQ' => 'UNIQ'
			];

			$SheetDataKey = [];
			foreach ($allDataInSheet as $dataInSheet) {
				foreach ($dataInSheet as $key => $value) {
					if (in_array(trim($value), $createArray)) {
						$value = preg_replace('/\s+/', '', $value);
						$SheetDataKey[trim($value)] = $key;
					} else {
					}
				}
			}

			$data_diffs = array_diff_key($makeArray, $SheetDataKey);

			$id_jadwal_error = "";
			if (empty($data_diffs)) {
				$flag = 1;
			}

			$excel_id_jadwal = '';

			if ($flag == 1) {
				for ($i = 7; $i <= $arrayCount; $i++) {
					$id_jadwal = $SheetDataKey['id_jadwal'];
					$id_reg_pd = $SheetDataKey['id_reg_pd'];
					$nipd = $SheetDataKey['NIM'];
					$nm_pd = $SheetDataKey['NAMA'];
					$meet_17 = $SheetDataKey['P17'];
					$meet_18 = $SheetDataKey['P18'];
					$meet_19 = $SheetDataKey['P19'];
					$meet_20 = $SheetDataKey['P20'];
					$meet_21 = $SheetDataKey['P21'];
					$meet_22 = $SheetDataKey['P22'];
					$meet_23 = $SheetDataKey['P23'];
					$meet_24 = $SheetDataKey['P24'];
					$meet_25 = $SheetDataKey['P25'];
					$meet_26 = $SheetDataKey['P26'];
					$meet_27 = $SheetDataKey['P27'];
					$meet_28 = $SheetDataKey['P28'];
					$meet_29 = $SheetDataKey['P29'];
					$meet_30 = $SheetDataKey['P30'];
					$uniq = $SheetDataKey['UNIQ'];

					$id_jadwal = filter_var(trim($allDataInSheet[$i][$id_jadwal]), FILTER_SANITIZE_STRING);
					$id_reg_pd = filter_var(trim($allDataInSheet[$i][$id_reg_pd]), FILTER_SANITIZE_STRING);
					$nipd = filter_var(trim($allDataInSheet[$i][$nipd]), FILTER_SANITIZE_NUMBER_INT);
					$meet_17 = filter_var(trim($allDataInSheet[$i][$meet_17]), FILTER_SANITIZE_STRING);
					$meet_18 = filter_var(trim($allDataInSheet[$i][$meet_18]), FILTER_SANITIZE_STRING);
					$meet_19 = filter_var(trim($allDataInSheet[$i][$meet_19]), FILTER_SANITIZE_STRING);
					$meet_20 = filter_var(trim($allDataInSheet[$i][$meet_20]), FILTER_SANITIZE_STRING);
					$meet_21 = filter_var(trim($allDataInSheet[$i][$meet_21]), FILTER_SANITIZE_STRING);
					$meet_22 = filter_var(trim($allDataInSheet[$i][$meet_22]), FILTER_SANITIZE_STRING);
					$meet_23 = filter_var(trim($allDataInSheet[$i][$meet_23]), FILTER_SANITIZE_STRING);
					$meet_24 = filter_var(trim($allDataInSheet[$i][$meet_24]), FILTER_SANITIZE_STRING);
					$meet_25 = filter_var(trim($allDataInSheet[$i][$meet_25]), FILTER_SANITIZE_STRING);
					$meet_26 = filter_var(trim($allDataInSheet[$i][$meet_26]), FILTER_SANITIZE_STRING);
					$meet_27 = filter_var(trim($allDataInSheet[$i][$meet_27]), FILTER_SANITIZE_STRING);
					$meet_28 = filter_var(trim($allDataInSheet[$i][$meet_28]), FILTER_SANITIZE_STRING);
					$meet_29 = filter_var(trim($allDataInSheet[$i][$meet_29]), FILTER_SANITIZE_STRING);
					$meet_30 = filter_var(trim($allDataInSheet[$i][$meet_30]), FILTER_SANITIZE_STRING);
					$uniq = filter_var(trim($allDataInSheet[$i][$uniq]), FILTER_SANITIZE_NUMBER_INT);

					$excel_id_jadwal = $id_jadwal;

					$fetchData[] = [
						'id_jadwal' => $id_jadwal,
						'id_reg_pd' => $id_reg_pd,
						'nipd' => $nipd,
						'meet_17' => strtoupper($meet_17),
						'meet_18' => strtoupper($meet_18),
						'meet_19' => strtoupper($meet_19),
						'meet_20' => strtoupper($meet_20),
						'meet_21' => strtoupper($meet_21),
						'meet_22' => strtoupper($meet_22),
						'meet_23' => strtoupper($meet_23),
						'meet_24' => strtoupper($meet_24),
						'meet_25' => strtoupper($meet_25),
						'meet_26' => strtoupper($meet_26),
						'meet_27' => strtoupper($meet_27),
						'meet_28' => strtoupper($meet_28),
						'meet_29' => strtoupper($meet_29),
						'meet_30' => strtoupper($meet_30),
						'uniq' => $uniq,
						'change_by' => $this->ion_auth->user()->row()->first_name,
						'change_date' => date('Y-m-d H:i:s')
					];
				}

				$session_id_jadwal = $this->session->userdata('id_kls_session');

				if ($session_id_jadwal == $excel_id_jadwal) {
					$this->import->setBatchImport($fetchData);
					$this->import->importDataAbsen();

					$data_kls = $this->db->where(['id' => $id_jadwal])->from('siakad_jadwal')->get()->row();
					$pesan = "Import data Absen Jadwal " . $data_kls->tag_name . ", Berhasil.";
					$tipe = 0;
				} else {
					$pesan = "Import data gagal, silahkan pilih file yang berisi data kelas kuliah yang sesuai.";
					$tipe = 1;
				}
			} else {
				$pesan = "Import data gagal, kolom tidak sesuai.";
				$tipe = 1;
			}

			return $pesan . '|' . $tipe;
		} catch (Exception $e) {
			return 'Error';
		}
	}
}
