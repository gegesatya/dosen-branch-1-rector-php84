<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_kehadiran extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Kehadiran_model', 'Kehadiran_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index($id_jadwal)
	{
		if (SELF::authorization($id_jadwal)) {
			$jadwal = $this->db->where('id', $id_jadwal)->from('siakad_jadwal j')->join('siakad_hari h', 'h.uniq = j.id_hari')->get()->row();
			$meet = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->result();

			$output['judul_besar'] = $jadwal->tag_name;
			$output['judul_kecil'] = $jadwal->nama_hari . ', ' . $jadwal->jam_mulai . ' sd ' . $jadwal->jam_selesai;
			$output['meet'] = $meet;
			$output['id_jadwal'] = $id_jadwal;
			$output['content'] = 'kehadiran/kehadiran_view';
			$output['after_page'] = 'kehadiran/kehadiran_after_page';
			$output['judul'] = 'Kehadiran ' . $jadwal->tag_name;
			$output['is_generate_bap'] = $jadwal->is_generate_bap;
			$this->load->view('master_page_elite', $output);
		} else {
			echo "Anda tidak bisa mengakses kelas ini.";
		}
	}

	private function authorization($id_jadwal)
	{
		$id_logged_in_user = $this->ion_auth->user()->row();
		$id_ptk_user = $id_logged_in_user->id_ptk;

		$jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

		if ($jadwal->id_sdm === $id_ptk_user) {
			return true;
		} else {
			return false;
		}
	}

	private function generate_html($i, $meet, $uniq)
	{
		//$realisasi = $this->get_realisasi($i);
		$realisasi = 1;
		//if ((int)$this->get_realisasi($i) === 1) {
		return  $this->convert_icon($meet, $realisasi);
		// return "<a href='javascript:void(0)' onclick='edit_absen(\"" . $meet . "\", \"meet_$i\",\"" . $uniq . "\")'>" . $this->convert_icon($meet, $realisasi) . "</a>";
		//} else {
		//	return "";
		//}
	}

	public function ajax_list($id_kls = NULL)
	{
		// $id_kls = $this->session->userdata('id_kls_session');
		$list = $this->Kehadiran_model->get_datatables($id_kls);

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
				$l->meet_12, $l->meet_13, $l->meet_14, $l->meet_15, $l->meet_16
			];


			for ($i = 1; $i <= 16; $i++) {
				$j = $i - 1;
				$row[] = $this->generate_html($i, $_meets[$j], $l->uniq);
			}

			$row[] = $l->persen_hadir;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Kehadiran_model->count_all($id_kls),
			"recordsFiltered" => $this->Kehadiran_model->count_filtered($id_kls),
			"data" => $data,
		];

		echo json_encode($output);
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
					return '<i class="fa fa-solid fa-check text-info"></i>';
				} elseif ($status == 'A') {
					return '<i class="fas fa-times text-danger"></i>';
				}
			} else {
				return '?';
			}
		} else {
			return '';
		}
	}

	public function sinkronisasi($id_jadwal)
	{
		// $id_jadwal = $this->session->userdata('id_kls_session');
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

	public function ajax_update($id_jadwal)
	{
		// $this->_validate();

		$id_jadwal 			= $this->session->userdata('id_kls_session');
		$status_hadir 		= $this->input->post('status_hadir');
		$meet 				= $this->input->post('pertemuan');

		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');


		$data = [$meet => $status_hadir, 'change_by' => $change_by, 'change_date' => $change_date];
		$data = $this->security->xss_clean($data);

		$this->Kehadiran_model->update(['id_jadwal' => $id_jadwal], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function edit_status_kehadiran($tipe, $pertemuan, $uniq)
	{
		if ($tipe == 0) {
			$data = [$pertemuan => 'A'];
			$this->Kehadiran_model->update_single(['uniq' => $uniq], $data);
		} else {
			$data = [$pertemuan => 'H'];
			$this->Kehadiran_model->update_single(['uniq' => $uniq], $data);
		}
	}

	public function download_excel($id_jadwal)
	{
		$spreadsheet = new Spreadsheet();
		// $id_jadwal = $this->session->userdata('id_kls_session');

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
				$value->meet_1,
				$value->meet_2,
				$value->meet_3,
				$value->meet_4,
				$value->meet_5,
				$value->meet_6,
				$value->meet_7,
				$value->meet_8,
				$value->meet_9,
				$value->meet_10,
				$value->meet_11,
				$value->meet_12,
				$value->meet_13,
				$value->meet_14,
				$value->meet_15,
				$value->meet_16,
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
		$sheet->setCellValue('F6', 'P1');
		$sheet->setCellValue('G6', 'P2');
		$sheet->setCellValue('H6', 'P3');
		$sheet->setCellValue('I6', 'P4');
		$sheet->setCellValue('J6', 'P5');
		$sheet->setCellValue('K6', 'P6');
		$sheet->setCellValue('L6', 'P7');
		$sheet->setCellValue('M6', 'P8');
		$sheet->setCellValue('N6', 'P9');
		$sheet->setCellValue('O6', 'P10');
		$sheet->setCellValue('P6', 'P11');
		$sheet->setCellValue('Q6', 'P12');
		$sheet->setCellValue('R6', 'P13');
		$sheet->setCellValue('S6', 'P14');
		$sheet->setCellValue('T6', 'P15');
		$sheet->setCellValue('U6', 'P16');
		$sheet->setCellValue('V6', 'UNIQ');

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
		$sheet->getColumnDimension('U')->setWidth(5);
		$sheet->getColumnDimension('V')->setWidth(5);

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
		$sheet->getStyle("T7:T" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("U7:U" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("V7:V" . $highestRow)->applyFromArray($style_horizontal_left);

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
		$sheet->getStyle("V6")->applyFromArray($style_horizontal_center);

		$sheet->getColumnDimension('B')->setVisible(false);
		$sheet->getColumnDimension('C')->setVisible(false);
		$sheet->getColumnDimension('V')->setVisible(false);

		$writer = new Xlsx($spreadsheet);

		$hari = $this->db->where('uniq', $jadwal->id_hari)->get('siakad_hari')->row()->nama_hari;
		$jam = substr($jadwal->jam_mulai, 0, 2);

		$f_name = str_replace('/', '_', $jadwal->tag_name . '_' . $hari . '_jam_' . $jam);
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
				'P1',
				'P2',
				'P3',
				'P4',
				'P5',
				'P6',
				'P7',
				'P8',
				'P9',
				'P10',
				'P11',
				'P12',
				'P13',
				'P14',
				'P15',
				'P16',
				'UNIQ'
			];

			$makeArray = [
				'id_jadwal' => 'id_jadwal',
				'id_reg_pd' => 'id_reg_pd',
				'NIM' => 'NIM',
				'NAMA' => 'NAMA',
				'P1' => 'P1',
				'P2' => 'P2',
				'P3' => 'P3',
				'P4' => 'P4',
				'P5' => 'P5',
				'P6' => 'P6',
				'P7' => 'P7',
				'P8' => 'P8',
				'P9' => 'P9',
				'P10' => 'P10',
				'P11' => 'P11',
				'P12' => 'P12',
				'P13' => 'P13',
				'P14' => 'P14',
				'P15' => 'P15',
				'P16' => 'P16',
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
					$meet_1 = $SheetDataKey['P1'];
					$meet_2 = $SheetDataKey['P2'];
					$meet_3 = $SheetDataKey['P3'];
					$meet_4 = $SheetDataKey['P4'];
					$meet_5 = $SheetDataKey['P5'];
					$meet_6 = $SheetDataKey['P6'];
					$meet_7 = $SheetDataKey['P7'];
					$meet_8 = $SheetDataKey['P8'];
					$meet_9 = $SheetDataKey['P9'];
					$meet_10 = $SheetDataKey['P10'];
					$meet_11 = $SheetDataKey['P11'];
					$meet_12 = $SheetDataKey['P12'];
					$meet_13 = $SheetDataKey['P13'];
					$meet_14 = $SheetDataKey['P14'];
					$meet_15 = $SheetDataKey['P15'];
					$meet_16 = $SheetDataKey['P16'];
					$uniq = $SheetDataKey['UNIQ'];

					$id_jadwal = filter_var(trim($allDataInSheet[$i][$id_jadwal]), FILTER_SANITIZE_STRING);
					$id_reg_pd = filter_var(trim($allDataInSheet[$i][$id_reg_pd]), FILTER_SANITIZE_STRING);
					$nipd = filter_var(trim($allDataInSheet[$i][$nipd]), FILTER_SANITIZE_NUMBER_INT);
					$meet_1 = filter_var(trim($allDataInSheet[$i][$meet_1]), FILTER_SANITIZE_STRING);
					$meet_2 = filter_var(trim($allDataInSheet[$i][$meet_2]), FILTER_SANITIZE_STRING);
					$meet_3 = filter_var(trim($allDataInSheet[$i][$meet_3]), FILTER_SANITIZE_STRING);
					$meet_4 = filter_var(trim($allDataInSheet[$i][$meet_4]), FILTER_SANITIZE_STRING);
					$meet_5 = filter_var(trim($allDataInSheet[$i][$meet_5]), FILTER_SANITIZE_STRING);
					$meet_6 = filter_var(trim($allDataInSheet[$i][$meet_6]), FILTER_SANITIZE_STRING);
					$meet_7 = filter_var(trim($allDataInSheet[$i][$meet_7]), FILTER_SANITIZE_STRING);
					$meet_8 = filter_var(trim($allDataInSheet[$i][$meet_8]), FILTER_SANITIZE_STRING);
					$meet_9 = filter_var(trim($allDataInSheet[$i][$meet_9]), FILTER_SANITIZE_STRING);
					$meet_10 = filter_var(trim($allDataInSheet[$i][$meet_10]), FILTER_SANITIZE_STRING);
					$meet_11 = filter_var(trim($allDataInSheet[$i][$meet_11]), FILTER_SANITIZE_STRING);
					$meet_12 = filter_var(trim($allDataInSheet[$i][$meet_12]), FILTER_SANITIZE_STRING);
					$meet_13 = filter_var(trim($allDataInSheet[$i][$meet_13]), FILTER_SANITIZE_STRING);
					$meet_14 = filter_var(trim($allDataInSheet[$i][$meet_14]), FILTER_SANITIZE_STRING);
					$meet_15 = filter_var(trim($allDataInSheet[$i][$meet_15]), FILTER_SANITIZE_STRING);
					$meet_16 = filter_var(trim($allDataInSheet[$i][$meet_16]), FILTER_SANITIZE_STRING);
					$uniq = filter_var(trim($allDataInSheet[$i][$uniq]), FILTER_SANITIZE_NUMBER_INT);

					$excel_id_jadwal = $id_jadwal;

					$fetchData[] = [
						'id_jadwal' => $id_jadwal,
						'id_reg_pd' => $id_reg_pd,
						'nipd' => $nipd,
						'meet_1' => strtoupper($meet_1),
						'meet_2' => strtoupper($meet_2),
						'meet_3' => strtoupper($meet_3),
						'meet_4' => strtoupper($meet_4),
						'meet_5' => strtoupper($meet_5),
						'meet_6' => strtoupper($meet_6),
						'meet_7' => strtoupper($meet_7),
						'meet_8' => strtoupper($meet_8),
						'meet_9' => strtoupper($meet_9),
						'meet_10' => strtoupper($meet_10),
						'meet_11' => strtoupper($meet_11),
						'meet_12' => strtoupper($meet_12),
						'meet_13' => strtoupper($meet_13),
						'meet_14' => strtoupper($meet_14),
						'meet_15' => strtoupper($meet_15),
						'meet_16' => strtoupper($meet_16),
						'uniq' => $uniq,
						'change_by' => $this->ion_auth->user()->row()->first_name,
						'change_date' => date('Y-m-d H:i:s')
					];
				}

				$session_id_jadwal = $id_jadwal;

				if ($session_id_jadwal == $excel_id_jadwal) {
					$this->import->setBatchImport($fetchData);
					$this->import->importDataAbsen('', $id_jadwal);

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
