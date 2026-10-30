<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_isi_nilai_excel_tunda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->model('absen_model', 'absen_model');
		$this->load->model('Import_model', 'import');
		$this->load->model('Isi_nilai_tunda_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index($id_jadwal = null)
	{
		$output['judul_besar'] 		= 'Form Nilai Tahun Ajaran ';
		$output['judul_kecil'] 		= 'Tahun Ajaran ' . $this->session->userdata('setting')->id_smt_tunda . ' (Tunda/Revisi)';
		$output['menu_aktif'] 		= 'm_isi_nilai_tunda';
		$output['parent_tunda']			= 'active open';
		$output['content'] 			= 'isi_nilai_tunda/isi_nilai_excel_tunda_view';
		$output['after_page'] 		= 'isi_nilai_tunda/isi_nilai_tunda_after_page';
		$output['dd_kelas_kuliah'] = $this->Isi_nilai_tunda_model->f_dropdown();

		$this->load->view('master_page', $output);
	}

	public function save_id_kls_session($id_jadwal)
	{
		$this->session->set_userdata('id_kls_session', $id_jadwal);

		$siakad_jadwal = $this->db->where(['id' => $id_jadwal])->get('siakad_jadwal')->row();
		$bobot_sikap = $siakad_jadwal->sikap;
		$bobot_pengetahuan_quiz = $siakad_jadwal->quiz;
		$bobot_pengetahuan_uts = $siakad_jadwal->uts;
		$bobot_pengetahuan_uas = $siakad_jadwal->uas;
		$bobot_pengetahuan_tugas_terstruktur = $siakad_jadwal->tugas_terstruktur;
		$bobot_keterampilan_unjuk_kinerja = $siakad_jadwal->unjuk_kinerja;
		$bobot_keterampilan_proyek = $siakad_jadwal->proyek;
		$bobot_keterampilan_portofolio = $siakad_jadwal->portofolio;
		$bobot_keterampilan_produk = $siakad_jadwal->produk;
		$bobot_keterampilan_presentasi = $siakad_jadwal->presentasi;

		$data_nilai = $this->ajax_list($id_jadwal);

		$html_tabel = '';
		$html_tabel .= '<table class="table table-bordered table-center-th" style="font-size: 12px !important">
						<thead>
						<tr>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">No</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">NIM</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">NAMA</td>
							<td style="text-align:center;vertical-align:middle;">Sikap</td>
							<td>QUI</td>
							<td>TTS</td>
							<td>UTS</td>
							<td>UAS</td>
							<td>UKN</td>
							<td>PRY</td>
							<td>POR</td>
							<td>PRD</td>
							<td>PRE</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">FINAL</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">HURUF</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">INDEX</td>
							<td rowspan="2" style="text-align:center;vertical-align:middle;">LAST EDITED</td>
						</tr>
						<tr>
							<td>' . $bobot_sikap . ' %</td>
							<td>' . $bobot_pengetahuan_quiz . ' %</td>
							<td>' . $bobot_pengetahuan_tugas_terstruktur . ' %</td>
							<td>' . $bobot_pengetahuan_uts . ' %</td>
							<td>' . $bobot_pengetahuan_uas . ' %</td>
							<td>' . $bobot_keterampilan_unjuk_kinerja . ' %</td>
							<td>' . $bobot_keterampilan_proyek . ' %</td>
							<td>' . $bobot_keterampilan_portofolio . ' %</td>
							<td>' . $bobot_keterampilan_produk . ' %</td>
							<td>' . $bobot_keterampilan_presentasi . ' %</td>
						</tr>
						</thead>
						<tbody>';

		$no = 1;

		foreach ($data_nilai as $k) {
			$html_tabel .= '<tr>' .
				'<td>' . $no++ . '</td>' .
				'<td>' . $k['nipd'] . '</td>' .
				'<td>' . $k['nm_pd'] . '</td>' .
				'<td>' . $k['n_sikap_2'] . '</td>' .
				'<td>' . $k['n_pengetahuan_2_quiz'] . '</td>' .
				'<td>' . $k['n_pengetahuan_2_tugas_terstruktur'] . '</td>' .
				'<td>' . $k['n_pengetahuan_2_uts'] . '</td>' .
				'<td>' . $k['n_pengetahuan_2_uas'] . '</td>' .
				'<td>' . $k['n_keterampilan_2_unjuk_kinerja'] . '</td>' .
				'<td>' . $k['n_keterampilan_2_proyek'] . '</td>' .
				'<td>' . $k['n_keterampilan_2_portofolio'] . '</td>' .
				'<td>' . $k['n_keterampilan_2_produk'] . '</td>' .
				'<td>' . $k['n_keterampilan_2_presentasi'] . '</td>' .
				'<td>' . $k['nilai_angka'] . '</td>' .
				'<td>' . $k['nilai_huruf'] . '</td>' .
				'<td>' . $k['nilai_indeks'] . '</td>' .
				'<td>' . $k['changedate'] . '</td>' .

				'</tr>';
		}

		$html_tabel .= '</tbody></table>';
		echo json_encode(['html_tabel' => $html_tabel]);
	}

	private function ajax_list($id_jadwal = NULL)
	{
		$list = $this->Isi_nilai_tunda_model->get_table_data($id_jadwal);
		return $list;
	}

	public function download_excel()
	{
		$spreadsheet = new Spreadsheet();
		//$this->load->library('excel');
		$id_jadwal = $this->session->userdata('id_kls_session');

		$jadwal = $this->db->where('id', $id_jadwal)
			->from('siakad_jadwal')
			->get()->row();

		$nilai = $this->db->select('*')
			->from('siakad_nilai_baru sn')
			->join('mahasiswa_pt mp', 'mp.nipd = sn.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->where(['sn.id_jadwal' => $id_jadwal, 'sn.is_memo_revisi' => 0])
			->order_by('nm_pd')
			->get()->result();

		$array_nilai = [];
		$no = 1;
		foreach ($nilai as $key => $value) {
			$id_reg_pd = $value->id_reg_pd;
			$nipd = $value->nipd;
			$token = '';

			if (empty($value->token_excel)) {
				$token = openssl_random_pseudo_bytes(16);
				$token = bin2hex($token);

				$this->db->update('siakad_nilai_baru', ['token_excel' => $token], ['id_kls' => $value->id_kls, 'nipd' => $value->nipd]);
			} else {
				$token = $value->token_excel;
			}

			$absen = $this->db
				->where(['nipd' => $value->nipd, 'id_jadwal' => $value->id_jadwal])
				->get('siakad_absen_detail_baru');
			$excel_absen = $absen->num_rows() ? $absen->row()->persen_hadir : 0;

			$array_nilai[] = [
				$no,
				$id_jadwal,
				$value->id_reg_pd,
				$value->nipd,
				$value->nm_pd,
				$excel_absen,
				$value->n_sikap_2,
				$value->n_pengetahuan_2_quiz,
				$value->n_pengetahuan_2_tugas_terstruktur,
				$value->n_pengetahuan_2_uts,
				$value->n_pengetahuan_2_uas,
				$value->n_keterampilan_2_unjuk_kinerja,
				$value->n_keterampilan_2_proyek,
				$value->n_keterampilan_2_portofolio,
				$value->n_keterampilan_2_produk,
				$value->n_keterampilan_2_presentasi,
				$value->nilai_angka,
				$value->nilai_huruf,
				$value->nilai_indeks,
				$token,
				$value->id_kls
			];

			$no++;
		}

		//$this->excel->getActiveSheet()->setTitle($jadwal->tag_name);
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DATA NILAI MAHASISWA ');
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
		$sheet->setCellValue('F6', 'PERSEN_HADIR');
		$sheet->setCellValue('G6', 'SIKAP');
		$sheet->setCellValue('H6', 'QUI');
		$sheet->setCellValue('I6', 'TTS');
		$sheet->setCellValue('J6', 'UTS');
		$sheet->setCellValue('K6', 'UAS');
		$sheet->setCellValue('L6', 'UKN');
		$sheet->setCellValue('M6', 'PRY');
		$sheet->setCellValue('N6', 'POR');
		$sheet->setCellValue('O6', 'PRD');
		$sheet->setCellValue('P6', 'PRE');
		$sheet->setCellValue('Q6', 'NILAI_FINAL');
		$sheet->setCellValue('R6', 'NILAI_HURUF');
		$sheet->setCellValue('S6', 'NILAI_INDEX');
		$sheet->setCellValue('T6', 'TOKEN_EXCEL');
		$sheet->setCellValue('U6', 'ID_KLS');


		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(40);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(50);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(15);
		$sheet->getColumnDimension('J')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(15);
		$sheet->getColumnDimension('L')->setWidth(15);
		$sheet->getColumnDimension('M')->setWidth(15);
		$sheet->getColumnDimension('N')->setWidth(15);
		$sheet->getColumnDimension('O')->setWidth(15);
		$sheet->getColumnDimension('P')->setWidth(15);
		$sheet->getColumnDimension('Q')->setWidth(15);
		$sheet->getColumnDimension('R')->setWidth(15);
		$sheet->getColumnDimension('S')->setWidth(15);
		$sheet->getColumnDimension('T')->setWidth(40);
		$sheet->getColumnDimension('U')->setWidth(40);

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


		$sheet->fromArray($array_nilai, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray($style_horizontal_center); //no
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray($style_horizontal_left); //id_reg_pd
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray($style_horizontal_left); //id_reg_pd
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray($style_horizontal_left); //nim
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray($style_horizontal_left); //nama
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

		$sheet->getColumnDimension('B')->setVisible(false);
		$sheet->getColumnDimension('C')->setVisible(false);
		$sheet->getColumnDimension('T')->setVisible(false);
		$sheet->getColumnDimension('U')->setVisible(false);

		$writer = new Xlsx($spreadsheet);
		$nm_file = substr('Daftar Nilai ' . $jadwal->tag_name, 0, 150);
		$filename =  str_replace(["/", "&"], ["_", "_"], $nm_file) . '.xlsx';
		$writer->save(getcwd() . '/laporan_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'laporan_excel/' . $filename);
		force_download($filename, $data);
	}

	public function cwds()
	{
		echo getcwd() . '<br>';
		echo realpath(__DIR__);
	}

	/* public function save_excel()
	{
		$this->load->library('excel');
		$pesan = '';
		$path = getcwd() . '/laporan_excel/uploads/';

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'xlsx|xls';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ($this->upload->do_upload('userfile')) {
			$data = array('upload_data' => $this->upload->data());
			$import_xls_file = $data['upload_data']['file_name'];
			$a = $this->edit_excel($path . $import_xls_file);
			$b = explode('|', $a);
			echo json_encode(array('status' => TRUE, 'message' => $b[0], 'tipe' => $b[1], 'id_kls' => $this->session->userdata('id_kls_session')));
		} else {
			$data = array();
		}
	} */

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
		try {

			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Slk();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Gnumeric();
			//    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			$reader->setLoadAllSheets();
			$objPHPExcel = $reader->load($file_excel);

			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
			$arrayCount = count($allDataInSheet);
			$flag = 0;
			$createArray = [
				'id_jadwal', 'id_reg_pd', 'NIM', 'NAMA', 'SIKAP', 'QUI', 'TTS',
				'UTS', 'UAS', 'NILAI_PENGETAHUAN', 'UKN', 'PRY', 'POR', 'PRD',
				'PRE', 'NILAI_KETERAMPILAN', 'NILAI_FINAL', 'NILAI_HURUF', 'NILAI_INDEX', 'TOKEN_EXCEL',
				'ID_KLS'
			];

			$makeArray = [
				'id_jadwal' => 'id_jadwal',
				'id_reg_pd' => 'id_reg_pd',
				'NIM' => 'NIM',
				'NAMA' => 'NAMA',
				'SIKAP' => 'SIKAP',
				'QUI' => 'QUI',
				'TTS' => 'TTS',
				'UTS' => 'UTS',
				'UAS' => 'UAS',
				'UKN' => 'UKN',
				'PRY' => 'PRY',
				'POR' => 'POR',
				'PRD' => 'PRD',
				'PRE' => 'PRE',
				'NILAI_FINAL' => 'NILAI_FINAL',
				'NILAI_HURUF' => 'NILAI_HURUF',
				'NILAI_INDEX' => 'NILAI_INDEX',
				'TOKEN_EXCEL' => 'TOKEN_EXCEL',
				'ID_KLS' => 'ID_KLS',
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
					$n_sikap_2 = $SheetDataKey['SIKAP'];
					$n_pengetahuan_2_quiz = $SheetDataKey['QUI'];
					$n_pengetahuan_2_tugas_terstruktur = $SheetDataKey['TTS'];
					$n_pengetahuan_2_uts = $SheetDataKey['UTS'];
					$n_pengetahuan_2_uas = $SheetDataKey['UAS'];
					//$n_pengetahuan_2 = $SheetDataKey['NILAI_PENGETAHUAN'];
					$n_keterampilan_2_unjuk_kinerja = $SheetDataKey['UKN'];
					$n_keterampilan_2_proyek = $SheetDataKey['PRY'];
					$n_keterampilan_2_portofolio = $SheetDataKey['POR'];
					$n_keterampilan_2_produk = $SheetDataKey['PRD'];
					$n_keterampilan_2_presentasi = $SheetDataKey['PRE'];
					//$n_keterampilan_2 = $SheetDataKey['NILAI_KETERAMPILAN'];
					$nilai_angka = $SheetDataKey['NILAI_FINAL'];
					$nilai_huruf = $SheetDataKey['NILAI_HURUF'];
					$nilai_indeks = $SheetDataKey['NILAI_INDEX'];
					$token_excel = $SheetDataKey['TOKEN_EXCEL'];
					$id_kls = $SheetDataKey['ID_KLS'];

					$id_jadwal = filter_var(trim($allDataInSheet[$i][$id_jadwal]), FILTER_SANITIZE_STRING);
					$id_reg_pd = filter_var(trim($allDataInSheet[$i][$id_reg_pd]), FILTER_SANITIZE_STRING);
					$n_sikap_2 = filter_var(trim($allDataInSheet[$i][$n_sikap_2]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_pengetahuan_2_quiz = filter_var(trim($allDataInSheet[$i][$n_pengetahuan_2_quiz]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_pengetahuan_2_tugas_terstruktur = filter_var(trim($allDataInSheet[$i][$n_pengetahuan_2_tugas_terstruktur]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

					$n_pengetahuan_2_uts = filter_var(trim($allDataInSheet[$i][$n_pengetahuan_2_uts]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_pengetahuan_2_uas = filter_var(trim($allDataInSheet[$i][$n_pengetahuan_2_uas]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					//$n_pengetahuan_2 = filter_var(trim($allDataInSheet[$i][$n_pengetahuan_2]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_keterampilan_2_unjuk_kinerja = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2_unjuk_kinerja]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

					$n_keterampilan_2_proyek = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2_proyek]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_keterampilan_2_portofolio = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2_portofolio]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_keterampilan_2_produk = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2_produk]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$n_keterampilan_2_presentasi = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2_presentasi]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

					//$n_keterampilan_2 = filter_var(trim($allDataInSheet[$i][$n_keterampilan_2]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$nilai_angka = filter_var(trim($allDataInSheet[$i][$nilai_angka]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$nilai_huruf = filter_var(trim($allDataInSheet[$i][$nilai_huruf]), FILTER_SANITIZE_STRING);
					$nilai_indeks = filter_var(trim($allDataInSheet[$i][$nilai_indeks]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
					$token_excel = filter_var(trim($allDataInSheet[$i][$token_excel]), FILTER_SANITIZE_STRING);
					//$id_kls = filter_var(trim($allDataInSheet[$i][$id_kls]), FILTER_SANITIZE_STRING);

					$excel_id_jadwal = $id_jadwal;

					$fetchData[] = [
						'id_jadwal' => $id_jadwal,
						'n_sikap_2' => $n_sikap_2,
						'n_pengetahuan_2_quiz' => $n_pengetahuan_2_quiz,
						'n_pengetahuan_2_tugas_terstruktur' => $n_pengetahuan_2_tugas_terstruktur,
						'n_pengetahuan_2_uts' => $n_pengetahuan_2_uts,
						'n_pengetahuan_2_uas' => $n_pengetahuan_2_uas,
						'n_keterampilan_2_unjuk_kinerja' => $n_keterampilan_2_unjuk_kinerja,
						'n_keterampilan_2_proyek' => $n_keterampilan_2_proyek,
						'n_keterampilan_2_portofolio' => $n_keterampilan_2_portofolio,
						'n_keterampilan_2_produk' => $n_keterampilan_2_produk,
						'n_keterampilan_2_presentasi' => $n_keterampilan_2_presentasi,
						'nilai_angka' => $nilai_angka,
						'token_excel' => $token_excel,
						'changeby' => $this->ion_auth->user()->row()->first_name,
						'changedate' => date('Y-m-d H:i:s')
					];
				}

				$session_id_jadwal = $this->session->userdata('id_kls_session');

				if ($session_id_jadwal == $excel_id_jadwal) {
					$this->import->setBatchImport($fetchData);
					$this->import->importData();

					$data_kls = $this->db->where(['id' => $id_jadwal])->from('siakad_jadwal')->get()->row();
					$this->recalculate_nilai();
					$pesan = "Import data Kelas Kuliah " . $data_kls->tag_name . ", Berhasil.";
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



	/* function do_upload(){
		$path = getcwd().'/laporan_excel/uploads/';

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'xlsx|xls';
        $config['encrypt_name'] = TRUE;
         
        $this->load->library('upload',$config);
        if($this->upload->do_upload("userfile")){
            $data = array('upload_data' => $this->upload->data());
 
            //$judul= $this->input->post('judul');
            $image= $data['upload_data']['file_name'];
             
            //$result= $this->m_upload->simpan_upload($judul,$image);
            echo json_decode(['status'=>$image]);
        }
	} */

	public function hitung_absen($id_reg_pd, $id_jadwal)
	{
		$data_absen = $this->db->select('*')->from('siakad_absen_master m')
			->join('siakad_absen_detail d', 'm.uniq = d.id_absen_master')
			->where(['id_kls' => $id_jadwal, 'id_reg_pd' => $id_reg_pd])
			->get();
		$persen_hadir = 0;
		$hadir = 0;
		$total_pertemuan = 0;
		if ($data_absen->num_rows() > 0) {
			$total_pertemuan = $data_absen->num_rows();

			foreach ($data_absen->result() as $key => $value) {
				if ($value->status_kehadiran == 'Hadir' || $value->status_kehadiran == 'Sakit' || $value->status_kehadiran == 'Izin') {
					$hadir = $hadir + 1;
				}
			}
		}

		$persen_hadir = 0;

		if ($total_pertemuan <> 0) {
			$persen_hadir = round(($hadir / $total_pertemuan) * 100, 2);
		}
		return [$persen_hadir, $hadir, $total_pertemuan];
	}

	public function recalculate_nilai($type = 'full')
	{
		$id_jadwal = $this->session->userdata('id_kls_session');
		$siakad_jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

		/* $nilai_baru = $this->db->from('siakad_jadwal j')
						->join('siakad_jadwal_detail d', 'd.id_jadwal = j.id')
						->join('siakad_nilai_baru n', 'n.id_kls = d.id_kls')
						->where('j.id', $id_jadwal)->get(); */

		$nilai_baru = $this->db->from('siakad_nilai_baru')
			->where('id_jadwal', $id_jadwal)->get();

		$b_sikap = $siakad_jadwal->sikap;
		$b_qui = $siakad_jadwal->quiz;
		$b_tts = $siakad_jadwal->tugas_terstruktur;
		$b_uts = $siakad_jadwal->uts;
		$b_uas = $siakad_jadwal->uas;
		$b_ukn = $siakad_jadwal->unjuk_kinerja;
		$b_pro = $siakad_jadwal->proyek;
		$b_por = $siakad_jadwal->portofolio;
		$b_prd = $siakad_jadwal->produk;
		$b_pre = $siakad_jadwal->presentasi;

		$fetchData = [];

		if ($type == 'full') {
			if ($nilai_baru->num_rows() > 0) {
				foreach ($nilai_baru->result_array() as $key => $input) {

					//if($input['persen_kehadiran'] < 80){
					//	$uas = 0;
					//}else{
					$uas = $input['n_pengetahuan_2_uas'];
					//}

					/* $n_pengetahuan_2 = (($b_qui/100)*$input['n_pengetahuan_2_quiz'])
							 + (($b_uts/100)*$input['n_pengetahuan_2_uts'])
							 + (($b_uas/100)*$uas)
							 + (($b_tts/100)*$input['n_pengetahuan_2_tugas_terstruktur']);

					$n_keterampilan_2 = (($b_ukn/100)*$input['n_keterampilan_2_unjuk_kinerja'])
									 + (($b_pro/100)*$input['n_keterampilan_2_proyek'])
									 + (($b_por/100)*$input['n_keterampilan_2_portofolio'])
									 + (($b_prd/100)*$input['n_keterampilan_2_produk'])
									 + (($b_pre/100)*$input['n_keterampilan_2_presentasi']); */

					$nilai_angka =  (($b_sikap / 100) * $input['n_sikap_2']) +
						(($b_qui / 100) * $input['n_pengetahuan_2_quiz']) +
						(($b_uts / 100) * $input['n_pengetahuan_2_uts']) +
						(($b_uas / 100) * $uas) +
						(($b_tts / 100) * $input['n_pengetahuan_2_tugas_terstruktur']) +
						(($b_ukn / 100) * $input['n_keterampilan_2_unjuk_kinerja']) +
						(($b_pro / 100) * $input['n_keterampilan_2_proyek']) +
						(($b_por / 100) * $input['n_keterampilan_2_portofolio']) +
						(($b_prd / 100) * $input['n_keterampilan_2_produk']) +
						(($b_pre / 100) * $input['n_keterampilan_2_presentasi']);

					$nilai_huruf = $this->get_nilai_huruf($nilai_angka);
					$nilai_indeks = $this->db->where('nilai_huruf', $nilai_huruf)->get('siakad_nilai_huruf')->row()->indeks;

					$fetchData[] = [
						//'n_pengetahuan_2' => $n_pengetahuan_2,
						//'n_keterampilan_2' => $n_keterampilan_2,
						'nilai_angka' => $nilai_angka,
						'nilai_huruf' => $nilai_huruf,
						'nilai_indeks' => $nilai_indeks,
						'token_excel' => $input['token_excel'],
						'n_pengetahuan_2_uas_2' => $uas,
						'changeby' => $this->ion_auth->user()->row()->first_name,
						'changedate' => date('Y-m-d H:i:s'),
					];
				}
			}
		} else {
			//if ($tabel_nilai->num_rows() > 0) {
			/*foreach ($tabel_nilai->result_array() as $key => $input) {


					$nilai_angka = $input['nilai_angka'];
					$nilai_huruf = $this->get_nilai_huruf($nilai_angka);
					$nilai_indeks = $this->db->where('nilai_huruf', $nilai_huruf)->get('siakad_nilai_huruf')->row()->indeks;

					$fetchData[] = array(
											'nilai_angka' => $nilai_angka,
											'nilai_huruf' => $nilai_huruf,
											'nilai_indeks' => $nilai_indeks,
											'token_excel' => $input['token_excel'],
											'changeby'=>$this->ion_auth->user()->row()->first_name,
	    									'changedate'=>date('Y-m-d H:i:s'),
										 );
				}*/
			//}
		}

		$this->import->setBatchImport($fetchData);
		$this->import->importData();
		//redirect(site_url('C_isi_nilai_excel'));

	}

	private function get_nilai_huruf($nilai_angka)
	{
		$final = floor($nilai_angka);
		$range = $this->db->get('siakad_nilai_huruf')->result();
		foreach ($range as  $row) {
			if ($final >= $row->range_bawah and $final <= $row->range_atas) {
				return $row->nilai_huruf;
				exit();
			}
		}
	}
}
