<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_download extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Download_model', 'Download_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [17];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['content'] = 'download/download_view';
		$output['after_page'] = 'download/download_after_page';
		$output['judul'] = 'Daftar Download';
		$output['judul_page'] = 'Daftar Download';

		$this->load->view('master_page_elite', $output);
	}

	private static function set_excel_style($style)
	{
		switch ($style) {
			case 'style_horizontal_center':
				return [
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'borders' => [
						'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
					],
				];
				break;
			case 'style_horizontal_left':
				return  [
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'borders' => [
						'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
					],
				];
				break;
			case 'style_outline':
				return  [
					'borders' => [
						'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
					],
				];
				break;
			default:
				return '';
				break;
		}
	}

	public function download_instansi()
	{
		$spreadsheet = new Spreadsheet();


		$instansi = $this->db->select('*')->from('simlapkerma_tbl_daftar_instansi')
			->order_by('nm_instansi')->get()->result();



		$array_data = [];
		$no = 1;
		foreach ($instansi as $key => $value) {
			$nm_bidang_usaha = '';
			if (!empty($value->id_bidang_usaha)) {
				$nm_bidang_usaha = $this->db->where('id_bidang_usaha', $value->id_bidang_usaha)->get('simlapkerma_tbl_bidang_usaha')->row()->nm_bidang_usaha;
			}

			$array_data[] = [
				$no,
				$value->nm_instansi,
				$value->alamat_instansi,
				$value->telepon,
				$value->handphone,
				$value->email,
				$value->pic,
				$value->jabatan,
				$nm_bidang_usaha
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MITRA KERJASAMA');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nama');
		$sheet->setCellValue('C6', 'Alamat');
		$sheet->setCellValue('D6', 'Telepon');
		$sheet->setCellValue('E6', 'Handphone');
		$sheet->setCellValue('F6', 'Email');
		$sheet->setCellValue('G6', 'Person In Charge');
		$sheet->setCellValue('H6', 'Jabatan');
		$sheet->setCellValue('I6', 'Bidang Usaha');


		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(100);
		$sheet->getColumnDimension('C')->setWidth(132);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(50);
		$sheet->getColumnDimension('G')->setWidth(55);
		$sheet->getColumnDimension('H')->setWidth(90);
		$sheet->getColumnDimension('I')->setWidth(40);


		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));


		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));



		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar Instansi Mitra Kerjasama');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_instansi_mou()
	{
		$spreadsheet = new Spreadsheet();
		$instansi = $this->db->select('*')->from('simlapkerma_tbl_daftar_instansi')->order_by('nm_instansi')->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($instansi as $key => $value) {
			$jumlah_mou = 0;
			$jumlah_moa = 0;
			$sub_instansi = '';
			$jenis_instansi = '';
			$nm_sub_instansi = '';
			$x = '';

			$jumlah_mou = $this->db->where('id_daftar_instansi', $value->id_daftar_instansi)->get('simlapkerma_tbl_daftar_mou')->num_rows();
			$jumlah_moa = $this->db->where('id_daftar_instansi', $value->id_daftar_instansi)->get('simlapkerma_tbl_moa')->num_rows();

			if (!empty($value->id_sub_instansi)) {
				$sub_instansi = $this->db->where('id_sub_instansi', $value->id_sub_instansi)->get('simlapkerma_tbl_sub_instansi')->row();
				$nm_sub_instansi = $sub_instansi->nm_sub_instansi;
				$id_instansi = $sub_instansi->id_instansi;
				$jenis_instansi = $this->db->where('id_jenis_instansi', $id_instansi)->get('simlapkerma_tbl_instansi')->row()->nm_jenis_instansi;
				$x = $nm_sub_instansi . ' (' . $jenis_instansi . ')';
			}

			$array_data[] = [
				$no,
				$value->nm_instansi,
				$x,
				$jumlah_mou,
				$jumlah_moa
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOU dan MOA Berdasarkan Instansi Mitra');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nama Instansi');
		$sheet->setCellValue('C6', 'Jenis Instansi');
		$sheet->setCellValue('D6', 'Jumlah MOU');
		$sheet->setCellValue('E6', 'Jumlah MOA');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(100);
		$sheet->getColumnDimension('C')->setWidth(50);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);

		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));

		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_center'));



		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar Jumlah MOU MOA berdasarkan Instansi Mitra');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_mou_aktif()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$mou = $this->db->where('id_status_kerjasama', 1)->select('*')->from('simlapkerma_tbl_daftar_mou mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($mou as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir));

			if (strtotime($value->tanggal_akhir) < now()) {
				$expire = 'Sudah berakhir';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$array_data[] = [
				$no,
				$value->nomor_mou,
				$value->nm_instansi,
				$value->tanggal_awal,
				$value->tanggal_akhir,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->bentuk_kerjasama
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOU AKTIF');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOU');
		$sheet->setCellValue('C6', 'Nama Mitra');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOU');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Bentuk Kerjasama');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);



		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOU Aktif');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_mou_tidak_aktif()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$mou = $this->db->where('id_status_kerjasama', 4)->select('*')->from('simlapkerma_tbl_daftar_mou mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($mou as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir));

			if (strtotime($value->tanggal_akhir) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$array_data[] = [
				$no,
				$value->nomor_mou,
				$value->nm_instansi,
				$value->tanggal_awal,
				$value->tanggal_akhir,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->bentuk_kerjasama
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOU TIDAK AKTIF');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOU');
		$sheet->setCellValue('C6', 'Nama Mitra');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOU');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Bentuk Kerjasama');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);



		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOU Tidak Aktif');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_mou_expired()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$mou = $this->db->where('id_status_kerjasama', 2)->select('*')->from('simlapkerma_tbl_daftar_mou mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($mou as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir));

			if (strtotime($value->tanggal_akhir) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$array_data[] = [
				$no,
				$value->nomor_mou,
				$value->nm_instansi,
				$value->tanggal_awal,
				$value->tanggal_akhir,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->bentuk_kerjasama
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOU EXPIRED');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOU');
		$sheet->setCellValue('C6', 'Nama Mitra');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOU');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Bentuk Kerjasama');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);



		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOU EXPIRED');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_mou_expired_soon()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$mou = $this->db->where(['id_status_kerjasama' => 1, 'will_expire' => 1])->select('*')->from('simlapkerma_tbl_daftar_mou mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($mou as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir));

			if (strtotime($value->tanggal_akhir) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$array_data[] = [
				$no,
				$value->nomor_mou,
				$value->nm_instansi,
				$value->tanggal_awal,
				$value->tanggal_akhir,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->bentuk_kerjasama
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOU EXPIRED');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOU');
		$sheet->setCellValue('C6', 'Nama Mitra');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOU');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Bentuk Kerjasama');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);



		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOU EXPIRED');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_moa_aktif()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$moa = $this->db->where('status_moa', 1)->select('*')->from('simlapkerma_tbl_moa mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($moa as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal_moa);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir_moa));

			if (strtotime($value->tanggal_akhir_moa) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$pemagangan = $value->is_magang == 1 ? 'Ya' : 'Tidak';

			$array_data[] = [
				$no,
				$value->nomor_dokumen_moa,
				$value->nm_instansi,
				$value->tanggal_awal_moa,
				$value->tanggal_akhir_moa,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->deskripsi_moa,
				$pemagangan
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOA AKTIF');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOA');
		$sheet->setCellValue('C6', 'Nama Mitra/Instansi');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOA');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Deskripsi MOA');
		$sheet->setCellValue('I6', 'Pemagangan');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);
		$sheet->getColumnDimension('I')->setWidth(20);




		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->getAlignment()->setWrapText(true);


		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOA Aktif');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_moa_tidak_aktif()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$moa = $this->db->where('status_moa', 4)->select('*')->from('simlapkerma_tbl_moa mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($moa as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal_moa);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir_moa));

			if (strtotime($value->tanggal_akhir_moa) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$pemagangan = $value->is_magang == 1 ? 'Ya' : 'Tidak';

			$array_data[] = [
				$no,
				$value->nomor_dokumen_moa,
				$value->nm_instansi,
				$value->tanggal_awal_moa,
				$value->tanggal_akhir_moa,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->deskripsi_moa,
				$pemagangan
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOA TIDAK AKTIF');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOA');
		$sheet->setCellValue('C6', 'Nama Mitra/Instansi');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOA');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Deskripsi MOA');
		$sheet->setCellValue('I6', 'Pemagangan');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);
		$sheet->getColumnDimension('I')->setWidth(20);




		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->getAlignment()->setWrapText(true);


		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOA TIDAK Aktif');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_moa_expired()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$moa = $this->db->where('status_moa', 2)->select('*')->from('simlapkerma_tbl_moa mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($moa as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal_moa);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir_moa));

			if (strtotime($value->tanggal_akhir_moa) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$pemagangan = $value->is_magang == 1 ? 'Ya' : 'Tidak';

			$array_data[] = [
				$no,
				$value->nomor_dokumen_moa,
				$value->nm_instansi,
				$value->tanggal_awal_moa,
				$value->tanggal_akhir_moa,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->deskripsi_moa,
				$pemagangan
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOA EXPIRED');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOA');
		$sheet->setCellValue('C6', 'Nama Mitra/Instansi');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOA');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Deskripsi MOA');
		$sheet->setCellValue('I6', 'Pemagangan');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);
		$sheet->getColumnDimension('I')->setWidth(20);




		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->getAlignment()->setWrapText(true);


		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOA EXPIRED');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}

	public function download_moa_expired_soon()
	{
		$this->load->helper('date');
		$spreadsheet = new Spreadsheet();
		$moa = $this->db->where(['status_moa' => 1, 'will_expire' => 1])->select('*')->from('simlapkerma_tbl_moa mo')
			->join('simlapkerma_tbl_daftar_instansi i', 'i.id_daftar_instansi = mo.id_daftar_instansi')
			->order_by('nm_instansi')
			->get()->result();

		$array_data = [];
		$no = 1;
		foreach ($moa as $key => $value) {

			$tgl_awal = strtotime($value->tanggal_awal_moa);
			$tgl_akhir = strtotime('+2 day', strtotime($value->tanggal_akhir_moa));

			if (strtotime($value->tanggal_akhir_moa) < now()) {
				$expire = 'Expired';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$pemagangan = $value->is_magang == 1 ? 'Ya' : 'Tidak';

			$array_data[] = [
				$no,
				$value->nomor_dokumen_moa,
				$value->nm_instansi,
				$value->tanggal_awal_moa,
				$value->tanggal_akhir_moa,
				timespan($tgl_awal, $tgl_akhir, 1),
				$expire,
				$value->deskripsi_moa,
				$pemagangan
			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DAFTAR MOA EXPIRED');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'Nomor MOA');
		$sheet->setCellValue('C6', 'Nama Mitra/Instansi');
		$sheet->setCellValue('D6', 'Tgl Mulai');
		$sheet->setCellValue('E6', 'Tgl Selesai');
		$sheet->setCellValue('F6', 'Umur MOA');
		$sheet->setCellValue('G6', 'Berakhir Dalam');
		$sheet->setCellValue('H6', 'Deskripsi MOA');
		$sheet->setCellValue('I6', 'Pemagangan');

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(40);
		$sheet->getColumnDimension('H')->setWidth(150);
		$sheet->getColumnDimension('I')->setWidth(20);




		$sheet->getStyle('A6:V6')->getFont()->setSize(11)->setBold(true);


		$sheet->fromArray($array_data, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_center'));
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H7:H" . $highestRow)->getAlignment()->setWrapText(true);


		$sheet->getStyle("A6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("B6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("C6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("D6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("E6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("F6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("G6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("H6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));
		$sheet->getStyle("I6")->applyFromArray(SELF::set_excel_style('style_horizontal_left'));

		$writer = new Xlsx($spreadsheet);
		$f_name = str_replace('/', '_', 'Daftar MOA EXPIRED');
		$nm_file = substr($f_name, 0, 150);

		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/download_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'download_excel/' . $filename);
		force_download($filename, $data);
	}
}
