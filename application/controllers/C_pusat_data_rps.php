<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_rps extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_rps_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$output['judul_besar'] = 'RPS Online';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_pusat_data_rps';
		$output['parent_data_center'] = 'active open';
		$output['content'] = 'pusat_data_rps/pusat_data_rps_view';
		$output['after_page'] = 'pusat_data_rps/pusat_data_rps_after_page';
		$output['dd_koordinator'] = $this->Pusat_data_rps_model->f_dropdown('siakad_rps_koordinator', 'koordinator_rps', 'nm_koordinator');

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_rps = $setting->tgl_akhir_rps;

		$list = $this->Pusat_data_rps_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$no++;

			$row = [];

			$tgl_rps = tgl_indo_short(date('Y-m-d', strtotime($l->tanggal_rps)));
			$html_preview = 'Tgl RPS : ' . $tgl_rps . '<br><br><a href="' . site_url('C_rps_online/preview_rps/' . $l->id_rps) . '" class="btn btn-default btn-xs green-stripe" target="_new"  title="Preview RPS">
								<i class="fa fa-folder-open-o"></i> ' . $l->general_mk . ' </a>';

			$mk_prodi = $this->db->where(['id_rps' => $l->id_rps])
				->select('*')->from('siakad_rps_mk_prodi mkp')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = mkp.id_mk')
				->join('feeder_kelas_kuliah kk', 'kk.id_mk = mk.id_mk')
				->join('sms s', 's.id_sms = mk.id_sms')
				->group_by('mkp.id_mk')
				->get();


			$prodi = $kode_mk = $nm_mk = $smt = $sks = '';
			if ($mk_prodi->num_rows() > 0) {
				foreach ($mk_prodi->result() as $key => $value) {
					$sks = $value->sks_mk * 1;
					$prodi .= $value->nm_prodi_english . '<br>';
					$nm_mk .= '[' . $value->kode_mk . '] ' . $value->nm_mk . ' | ' . substr($value->nm_kls, 1, 1) . ' | ' . $sks . '<br>';
					//$smt .= substr($value->nm_kls, 1, 1) . '<br>';
					//$sks .= $value->sks_mk * 1 . '<br>';
				}
			}

			$row[] = $no;
			$row[] = $html_preview;
			$row[] = '<small><strong>Penyusun :</strong><br>' . $l->nm_ptk . '<br><strong>Pengampu :</strong><br>' . $l->dosen_pengampu . '<strong><br>Kordinator :</strong><br>' . $l->nm_koordinator . '</small>';
			$row[] = '<small><strong>' . $prodi . '</strong></small>';
			$row[] = '<small>' . $nm_mk . '</small>';
			/* $row[] = $smt;
			$row[] = $sks; */
			$tgl_buat = date('Y-m-d', strtotime($l->create_date));
			$tgl_ubah = !empty($l->change_date) ? tgl_indo_short(date('Y-m-d', strtotime($l->change_date))) : '-';
			$row[] = '<small>Create : ' . tgl_indo_short($tgl_buat) . '<br>Update : ' . $tgl_ubah . '</small>';
			//$row[] = tgl_indo_short($tgl_ubah);

			/* $html = '<a href="javascript:void(0)" class="btn btn-sm green-meadow"  title="Upload Detail RPS"
				onclick="form_upload(\'' . $l->id_rps . '\')">
				<i class="fa fa-upload"></i> </a>'; */

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pusat_data_rps_model->count_all(),
			"recordsFiltered" => $this->Pusat_data_rps_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function tambah()
	{
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_rps = $setting->tgl_akhir_rps;

		if (strtotime($tgl_akhir_rps) < strtotime(date('Y-m-d H:i:s'))) {
			$this->session->set_flashdata('msg_rps', 'Periode pengisian RPS sudah berakhir.');
		}

		$output['judul_besar'] = 'Tambah RPS';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_rps_tambah';
		$output['parent_ws'] = 'active open';
		$output['content'] = 'rps_online/rps_online_tambah_view';
		$output['after_page'] = 'rps_online/rps_online_tambah_after_page';
		$output['dd_koordinator'] = $this->Pusat_data_rps_model->f_dropdown('siakad_rps_koordinator', 'koordinator_rps', 'nm_koordinator');

		$this->load->view('master_page', $output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Pusat_data_rps_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		//$this->_validate();
		$data = [
			'id_ptk' => $this->session->userdata('id_ptk'),
			'koordinator_rps' => $this->input->post('koordinator_rps'),
			'tanggal_rps' => $this->input->post('tanggal_rps'),
			'general_mk' => $this->input->post('general_mk'),
			'deskripsi_mata_kuliah' => $this->input->post('deskripsi_mata_kuliah'),
			'capaian_pembelajaran_mata_kuliah' => $this->input->post('capaian_pembelajaran_mata_kuliah'),
			'referensi' => $this->input->post('referensi'),
			'id_status_pengajuan_rps' => 1,
			'capaian_sikap' => $this->input->post('capaian_sikap'),
			'capaian_pengetahuan' => $this->input->post('capaian_pengetahuan'),
			'capaian_keterampilan_umum' => $this->input->post('capaian_keterampilan_umum'),
			'capaian_keterampilan_khusus' => $this->input->post('capaian_keterampilan_khusus'),
			'tugas_mahasiswa' => $this->input->post('tugas_mahasiswa'),
			'create_by' => $this->ion_auth->user()->row()->email,
			'create_date' => date('Y-m-d H:i:s')
		];
		$insert = $this->Pusat_data_rps_model->save($data);

		if ($insert) {
			echo json_encode(["status" => TRUE, "redirect" => site_url('C_rps_online')]);
		}
	}

	public function ajax_update()
	{
		//$this->_validate();
		$data = [
			'id_ptk' => $this->session->userdata('id_ptk'),
			'koordinator_rps' => $this->input->post('koordinator_rps'),
			'tanggal_rps' => $this->input->post('tanggal_rps'),
			'general_mk' => $this->input->post('general_mk'),
			'deskripsi_mata_kuliah' => $this->input->post('deskripsi_mata_kuliah'),
			'capaian_pembelajaran_mata_kuliah' => $this->input->post('capaian_pembelajaran_mata_kuliah'),
			'referensi' => $this->input->post('referensi'),
			'dosen_pengampu' => $this->input->post('dosen_pengampu'),
			'capaian_sikap' => $this->input->post('capaian_sikap'),
			'capaian_pengetahuan' => $this->input->post('capaian_pengetahuan'),
			'capaian_keterampilan_umum' => $this->input->post('capaian_keterampilan_umum'),
			'capaian_keterampilan_khusus' => $this->input->post('capaian_keterampilan_khusus'),
			'tugas_mahasiswa' => $this->input->post('tugas_mahasiswa'),
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Pusat_data_rps_model->update(['id_rps' => $this->input->post('id_rps')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_submit_rps($id_rps)
	{
		$data = [
			'id_status_pengajuan_rps' => 2,
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Pusat_data_rps_model->update(['id_rps' => $id_rps], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->db->where('id_rps', $id)->delete('siakad_rps_mk_prodi');
		$this->db->where('id_rps', $id)->delete('siakad_rps_online_detail');
		$this->Pusat_data_rps_model->delete_by_id($id);
		echo json_encode(["status" => TRUE, "pesan" => "Data berhasil dihapus!"]);
	}


	public function save_excel()
	{
		$path = getcwd() . '/rps_online_excel/';

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'xlsx|xls';
		$config['remove_spaces'] = TRUE;
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ($this->upload->do_upload('userfile')) {
			$data = ['upload_data' => $this->upload->data()];
			$import_xls_file = $data['upload_data']['file_name'];
			$a = $this->edit_excel($path . $import_xls_file, $this->input->post('id_rps'));
			$b = explode('|', $a);
			//echo json_encode(array('status' => TRUE, 'message' => $b[0], 'tipe' => $b[1]));
			$this->session->set_flashdata('sukses', $b[0]);
			redirect($_SERVER["HTTP_REFERER"]);
		} else {
			//$data = array();
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('error', $error);
			redirect($_SERVER["HTTP_REFERER"]);
		}
	}

	public function edit_excel($file_excel = '', $id_rps = null)
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
			$createArray = ['KOLOM_1', 'KOLOM_2', 'KOLOM_3', 'KOLOM_4', 'KOLOM_5', 'KOLOM_6', 'KOLOM_7', 'KOLOM_8', 'KOLOM_9', 'KOLOM_10', 'KOLOM_11', 'KOLOM_12'];

			$makeArray = [
				'KOLOM_1' => 'KOLOM_1',
				'KOLOM_2' => 'KOLOM_2',
				'KOLOM_3' => 'KOLOM_3',
				'KOLOM_4' => 'KOLOM_4',
				'KOLOM_5' => 'KOLOM_5',
				'KOLOM_6' => 'KOLOM_6',
				'KOLOM_7' => 'KOLOM_7',
				'KOLOM_8' => 'KOLOM_8',
				'KOLOM_9' => 'KOLOM_9',
				'KOLOM_10' => 'KOLOM_10',
				'KOLOM_11' => 'KOLOM_11',
				'KOLOM_12' => 'KOLOM_12'
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

			if (empty($data_diffs)) {
				$flag = 1;
			}

			if ($flag == 1) {
				for ($i = 6; $i <= $arrayCount; $i++) {
					$kolom_1 	= $SheetDataKey['KOLOM_1'];
					$kolom_2 	= $SheetDataKey['KOLOM_2'];
					$kolom_3 	= $SheetDataKey['KOLOM_3'];
					$kolom_4 	= $SheetDataKey['KOLOM_4'];
					$kolom_5 	= $SheetDataKey['KOLOM_5'];
					$kolom_6 	= $SheetDataKey['KOLOM_6'];
					$kolom_7 	= $SheetDataKey['KOLOM_7'];
					$kolom_8 	= $SheetDataKey['KOLOM_8'];
					$kolom_9 	= $SheetDataKey['KOLOM_9'];
					$kolom_10 	= $SheetDataKey['KOLOM_10'];
					$kolom_11 	= $SheetDataKey['KOLOM_11'];
					$kolom_12 	= $SheetDataKey['KOLOM_12'];

					$kolom_1 = filter_var(trim($allDataInSheet[$i][$kolom_1]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_2 = filter_var(trim($allDataInSheet[$i][$kolom_2]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_3 = filter_var(trim($allDataInSheet[$i][$kolom_3]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_4 = filter_var(trim($allDataInSheet[$i][$kolom_4]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_5 = filter_var(trim($allDataInSheet[$i][$kolom_5]), FILTER_SANITIZE_STRING);
					$kolom_6 = filter_var(trim($allDataInSheet[$i][$kolom_6]), FILTER_SANITIZE_STRING);
					$kolom_7 = filter_var(trim($allDataInSheet[$i][$kolom_7]), FILTER_SANITIZE_STRING);
					$kolom_8 = filter_var(trim($allDataInSheet[$i][$kolom_8]), FILTER_SANITIZE_STRING);
					$kolom_9 = filter_var(trim($allDataInSheet[$i][$kolom_9]), FILTER_SANITIZE_STRING);
					$kolom_10 = filter_var(trim($allDataInSheet[$i][$kolom_10]), FILTER_SANITIZE_STRING);
					$kolom_11 = filter_var(trim($allDataInSheet[$i][$kolom_11]), FILTER_SANITIZE_STRING);
					$kolom_12 = filter_var(trim($allDataInSheet[$i][$kolom_12]), FILTER_SANITIZE_NUMBER_INT);


					if ($kolom_1 != '' and $kolom_2 != '') {
						$fetchData[] = [
							'id_rps' => $id_rps,
							'kolom_1' => $kolom_1,
							'kolom_2' => $kolom_2,
							'kolom_3' => $kolom_3,
							'kolom_4' => $kolom_4,
							'kolom_5' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_5),
							'kolom_6' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_6),
							'kolom_7' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_7),
							'kolom_8' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_8),
							'kolom_9' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_9),
							'kolom_10' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_10),
							'kolom_11' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_11),
							'kolom_12' => $kolom_12
						];
					}
				}

				//$this->add_data($fetchData);
				$this->db->where('id_rps', $id_rps)->delete('siakad_rps_online_detail');
				$this->db->insert_batch('siakad_rps_online_detail', $fetchData);
				unlink($file_excel);
				$pesan = "Import Data Berhasil.";
				$tipe = 0;
			} else {
				$pesan = "Import data gagal, kolom tidak sesuai.";
				$tipe = 1;
			}

			return $pesan . '|' . $tipe;
		} catch (Exception $e) {
			return 'Error';
		}
	}

	public function preview_rps($id_rps = null)
	{
		$data_rps = $this->db->where('id_rps', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();

		$data_rps_detail = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online_detail')->result();
		$mk_prodi = $this->db->where(['id_rps' => $id_rps])
			->select('*')->from('siakad_rps_mk_prodi mkp')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = mkp.id_mk')
			->join('feeder_kelas_kuliah kk', 'kk.id_mk = mk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->group_by('mkp.id_mk')
			->get()->result();


		$koordinator = $this->db->where('koordinator_rps', $data_rps->koordinator_rps)->select('*')->from('siakad_rps_koordinator')->get();


		$output['data_rps'] = $data_rps;
		$output['data_rps_detail'] = $data_rps_detail;
		$output['koordinator_rps'] = $koordinator;
		$output['rps_kelola'] =  'NIL';
		$output['mk_prodi'] =  $mk_prodi;

		//$this->load->view('cetak/pdf_rps_online', $output);
		//$mpdf->shrink_tables_to_fit = 1;
		$html = $this->load->view('cetak/pdf_rps_online', $output, true);
		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
		//$mpdf->AddPage('L', '', '', '', '', 15, 10, 10, 10, 10, 10);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function download_template()
	{
		$this->load->helper('download');
		force_download('rps_online_excel/template_rps_detail.xlsx', NULL);
	}
}
