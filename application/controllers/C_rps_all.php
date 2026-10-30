<?php
defined('BASEPATH') or exit('No direct script access allowed');

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class C_rps_all extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rps_new_online_model');
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

		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_rps = $setting->tgl_akhir_rps;

		if (strtotime($tgl_akhir_rps) < strtotime(date('Y-m-d H:i:s'))) {
			$this->session->set_flashdata('msg_rps', 'Periode pengisian RPS sudah berakhir.');
		}

		$output['judul_besar'] = 'RPS Online';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_rps_all';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'rps_kaprodi/rps_kaprodi_view';
		$output['after_page'] = 'rps_kaprodi/rps_kaprodi_after_page';
		$output['dd_koordinator'] = $this->Rps_new_online_model->f_dd_koordinator();

		$output['dd_kaprodi'] = $this->Rps_new_online_model->f_dropdown2('siakad_rps_koordinator', 'koordinator_rps', 'nm_koordinator', 'nm_koordinator', ['aktif' => 1]);
		$output['dd_mata_kuliah'] = $this->Rps_new_online_model->f_dropdown_mk();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_rps = $setting->tgl_akhir_rps;

		$list = $this->Rps_new_online_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$no++;
			$row = [];

			$koordinator_rps = $this->db->where('id_ptk', $l->koordinator_rps)->get('siakad_dosen')->row()->nm_ptk ?? $l->koordinator_rps;
			$kaprodi_rps = $this->db->where('id_ptk', $l->kaprodi_rps)->get('siakad_dosen')->row()->nm_ptk ?? $l->kaprodi_rps;

			$tgl_pembuatan = (!empty($l->tanggal_rps) && $l->tanggal_rps != '0000-00-00') ? tgl_indo($l->tanggal_rps) : ((!empty($l->create_date) && $l->create_date != '0000-00-00 00:00:00') ? tgl_indo(date('Y-m-d', strtotime($l->create_date))) : '-');

			// Sembunyikan tombol Summary RPS sementara
			/*
			$html_summary = '<button type="button" class="btn btn-warning btn-sm btn-block margin-bottom-5" style="background:#f59e0b; border-color:#d97706; color:#fff; font-weight:600; text-align:left; padding:5px 8px; border-radius:4px; font-size:12px; white-space:nowrap;" onclick="bukaSummaryRps(\'' . $l->id_rps . '\')" title="Executive Summary RPS OBE & Evaluasi 16 Pertemuan">
								<i class="fa fa-pie-chart" style="margin-right:4px;"></i> Summary RPS</button>';
			*/

			$html_dokumen_dropdown = '<div class="btn-group btn-block">
										<button type="button" class="btn btn-default btn-sm dropdown-toggle btn-block" data-toggle="dropdown" aria-expanded="false" style="font-weight:600; text-align:left; padding:5px 8px; border-radius:4px; border:1px solid #cbd5e1; background:#fff; color:#334155; font-size:12px; white-space:nowrap;">
											<i class="fa fa-file-text-o text-primary" style="margin-right:4px;"></i> Dokumen <span class="caret" style="float:right; margin-top:6px;"></span>
										</button>
										<ul class="dropdown-menu dropdown-menu-right pull-right" role="menu" style="min-width: 195px;">
											<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Preview Dokumen (PDF)</li>
											<li><a href="' . site_url('C_rps_obsolete/preview_rps_baru/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> RPS (16 Minggu)</a></li>
											<li><a href="' . site_url('C_rps_obsolete/preview_kontrak/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> Kontrak Pembelajaran</a></li>
											<li><a href="' . site_url('C_rps_obsolete/preview_rps_rancangan_tugas/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> Rancangan Tugas</a></li>
											<li class="divider" style="margin:4px 0;"></li>
											<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Ekspor Matriks</li>
											<li><a href="' . site_url('C_rps_obsolete/convert/' . $l->id_rps) . '"><i class="fa fa-file-excel-o text-success"></i> Detail Pertemuan (.xls)</a></li>
										</ul>
									</div>';

			$row[] = $no;
			$row[] = '<div style="font-weight: 600; font-size: 13px; color: #1e293b;">[' . $l->kode_mk . '] ' . $l->nm_mk . '</div><div style="margin-top: 5px;"><span class="text-muted" style="font-size: 11.5px;"><i class="fa fa-calendar text-primary" style="margin-right: 4px;"></i><b>Tgl Pembuatan :</b> ' . $tgl_pembuatan . '</span></div>';
			$row[] = 'SKS Mata Kuliah : ' . $l->sks_mk . '<br>SKS Tatap Muka : ' . $l->sks_tm . '<br>SKS Praktek : ' . $l->sks_prak;
			$row[] = '<b>Ketua PS / Wadir 1 : </b>' . $kaprodi_rps . '<br><b>Kordinator RPS : </b>' . $koordinator_rps . '<br><b>Pengampu : </b>' . $l->dosen_pengampu;

			$row[] =  $html_dokumen_dropdown;

			$is_closed = (!empty($tgl_akhir_rps) && strtotime($tgl_akhir_rps) < strtotime(date('Y-m-d H:i:s')));
			if ($is_closed) {
				$row[] = '<span class="text-muted" style="font-size:11.5px;"><i class="fa fa-lock"></i> Periode Ditutup</span>';
			} else {
				$html_action_dropdown = '<div class="btn-group btn-block">
											<button type="button" class="btn btn-primary btn-sm dropdown-toggle btn-block" data-toggle="dropdown" aria-expanded="false" style="font-weight:600; text-align:left; padding:5px 8px; border-radius:4px; background:#2563eb; border-color:#1d4ed8; color:#fff; font-size:12px; white-space:nowrap;">
												<i class="fa fa-pencil" style="margin-right:4px;"></i> Edit &amp; Kelola <span class="caret" style="float:right; margin-top:6px;"></span>
											</button>
											<ul class="dropdown-menu dropdown-menu-right pull-right" role="menu" style="min-width: 215px;">
												<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Edit Perangkat</li>
												<li><a href="javascript:void(0)" onclick="edit_rps(\'' . $l->id_rps . '\')"><i class="fa fa-pencil text-primary"></i> Edit RPS Utama</a></li>
												<li><a href="javascript:void(0)" onclick="edit_kontrak(\'' . $l->id_rps . '\')"><i class="fa fa-pencil text-info"></i> Edit Kontrak Pembelajaran</a></li>
												<li><a href="javascript:void(0)" onclick="edit_rancangan_tugas(\'' . $l->id_rps . '\')"><i class="fa fa-pencil text-purple" style="color:#9333ea;"></i> Edit Rancangan Tugas</a></li>
												<li class="divider" style="margin:4px 0;"></li>
												<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Upload Berkas</li>
												<li><a href="javascript:void(0)" onclick="form_upload(\'' . $l->id_rps . '\')"><i class="fa fa-upload text-success"></i> Upload Rincian Pertemuan</a></li>
												<li><a href="javascript:void(0)" onclick="form_upload_gambar(\'' . $l->id_rps . '\')"><i class="fa fa-image text-warning"></i> Upload Organisasi Materi</a></li>
												<li class="divider" style="margin:4px 0;"></li>
												<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#ef4444; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Tindakan</li>
												<li><a href="javascript:void(0)" onclick="delete_rps(\'' . $l->id_rps . '\')" style="color:#dc2626;"><i class="fa fa-trash-o text-danger"></i> Hapus Dokumen RPS</a></li>
											</ul>
										</div>';

				$row[] = $html_action_dropdown;
			}

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Rps_new_online_model->count_all(),
			"recordsFiltered" => $this->Rps_new_online_model->count_filtered(),
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
		$output['content'] = 'rps_new/rps_new_online_tambah_view';
		$output['after_page'] = 'rps_new/rps_new_online_tambah_after_page';
		$output['dd_kaprodi'] = $this->Rps_new_online_model->f_dropdown2('siakad_rps_koordinator', 'koordinator_rps', 'nm_koordinator', 'nm_koordinator', ['aktif' => 1]);
		$output['dd_mata_kuliah'] = $this->Rps_new_online_model->f_dropdown_mk();
		$output['dd_koordinator'] = $this->Rps_new_online_model->f_dd_koordinator();


		$this->load->view('master_page', $output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Rps_new_online_model->get_by_id($id);

		if (!empty($data->image_organisasi)) {
			$data->image_organisasi = SOURCEPATH_RPS . $data->image_organisasi;
		}

		echo json_encode($data);
	}

	public function ajax_add()
	{
		//$this->_validate();
		$mk = $this->db->where('id_mk', $this->input->post('id_mk'))->get('feeder_mata_kuliah')->row();
		$matkul_kurikulum = $this->db->where(['id_mk' => $mk->id_mk])->from('feeder_mata_kuliah_kurikulum')->get()->row();

		$kode_mk = $mk->kode_mk;
		$nm_mk = $mk->nm_mk;
		$sks_mk = $mk->sks_mk;
		$sks_tm = $mk->sks_tm;
		$sks_prak = $mk->sks_prak + $mk->sks_prak_lap;

		$data = [
			'id_ptk' => $this->session->userdata('id_ptk'),
			'koordinator_rps' => $this->input->post('koordinator_rps'),
			'kaprodi_rps' => $this->input->post('kaprodi_rps'),
			'tanggal_rps' => $this->input->post('tanggal_rps'),
			'id_mk' => $this->input->post('id_mk'),
			'kode_mk' => $kode_mk,
			'nm_mk' => $nm_mk,
			'sks_mk' => $sks_mk,
			'sks_tm' => $sks_tm,
			'sks_prak' => $sks_prak,
			'semester' => $matkul_kurikulum->smt,
			'deskripsi_mata_kuliah' => $this->input->post('deskripsi_mata_kuliah'),
			'prasyarat_mata_kuliah' => $this->input->post('prasyarat_mata_kuliah'),
			'tujuan_tugas' => $this->input->post('tujuan_tugas'),
			'tugas_obyek_garapan' => $this->input->post('tugas_obyek_garapan'),
			'tugas_batasan' => $this->input->post('tugas_batasan'),
			'tugas_metode' => $this->input->post('tugas_metode'),
			'tugas_luaran' => $this->input->post('tugas_luaran'),
			'tugas_penilaian_kerapian' => $this->input->post('tugas_penilaian_kerapian'),
			'tugas_ketepatan' => $this->input->post('tugas_ketepatan'),
			'cpl' => $this->input->post('cpl'),
			'tujuan_belajar' => $this->input->post('tujuan_belajar'),
			'referensi' => $this->input->post('referensi'),
			'id_status_pengajuan_rps' => 1,
			'dosen_pengampu' => $this->input->post('dosen_pengampu'),
			'create_by' => $this->ion_auth->user()->row()->email,
			'create_date' => date('Y-m-d H:i:s')
		];
		$insert = $this->Rps_new_online_model->save($data);

		if ($insert) {
			echo json_encode(["status" => TRUE, "redirect" => site_url('C_rps_obsolete')]);
		}
	}

	public function ajax_update()
	{
		//$this->_validate();

		$mk = $this->db->where('id_mk', $this->input->post('id_mk'))->get('feeder_mata_kuliah')->row();
		$matkul_kurikulum = $this->db->where(['id_mk' => $mk->id_mk])->from('feeder_mata_kuliah_kurikulum')->get()->row();

		$kode_mk = $mk->kode_mk;
		$nm_mk = $mk->nm_mk;
		$sks_mk = $mk->sks_mk;
		$sks_tm = $mk->sks_tm;
		$sks_prak = $mk->sks_prak + $mk->sks_prak_lap;

		$data = [
			'id_ptk' => $this->session->userdata('id_ptk'),
			'koordinator_rps' => $this->input->post('koordinator_rps'),
			'kaprodi_rps' => $this->input->post('kaprodi_rps'),
			'tanggal_rps' => $this->input->post('tanggal_rps'),
			'id_mk' => $this->input->post('id_mk'),
			'kode_mk' => $kode_mk,
			'nm_mk' => $nm_mk,
			'sks_mk' => $sks_mk,
			'sks_tm' => $sks_tm,
			'sks_prak' => $sks_prak,
			'semester' => $matkul_kurikulum->smt,
			'deskripsi_mata_kuliah' => $this->input->post('deskripsi_mata_kuliah'),
			'prasyarat_mata_kuliah' => $this->input->post('prasyarat_mata_kuliah'),
			'cpl' => $this->input->post('cpl'),
			'tujuan_belajar' => $this->input->post('tujuan_belajar'),
			'referensi' => $this->input->post('referensi'),
			'id_status_pengajuan_rps' => 1,
			'dosen_pengampu' => $this->input->post('dosen_pengampu'),
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Rps_new_online_model->update(['id_rps' => $this->input->post('id_rps')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update_rancangan_tugas()
	{
		//$this->_validate();
		$data = [
			'tugas_ke' => $this->input->post('tugas_ke'),
			'tugas_minggu_ke' => $this->input->post('tugas_minggu_ke'),
			'tujuan_tugas' => $this->input->post('tujuan_tugas'),
			'tugas_obyek_garapan' => $this->input->post('tugas_obyek_garapan'),
			'tugas_batasan' => $this->input->post('tugas_batasan'),
			'tugas_metode' => $this->input->post('tugas_metode'),
			'tugas_luaran' => $this->input->post('tugas_luaran'),
			'tugas_penilaian_kerapian' => $this->input->post('tugas_penilaian_kerapian'),
			'tugas_ketepatan' => $this->input->post('tugas_ketepatan'),
			'id_status_pengajuan_rps' => 1,
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Rps_new_online_model->update(['id_rps' => $this->input->post('id_rps')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update_kontrak()
	{
		//$this->_validate();
		$data = [

			'manfaat_mata_kuliah' => $this->input->post('manfaat_mata_kuliah'),
			'cp_mata_kuliah' => $this->input->post('cp_mata_kuliah'),
			'analisis_instruksional' => $this->input->post('analisis_instruksional'),
			'strategi_perkuliahan' => $this->input->post('strategi_perkuliahan'),
			'materi_bahan_bacaan' => $this->input->post('materi_bahan_bacaan'),
			'tugas_tugas' => $this->input->post('tugas_tugas'),
			'kontrak_kriteria_penilaian' => $this->input->post('kontrak_kriteria_penilaian'),
			'jadwal_perkuliahan' => $this->input->post('jadwal_perkuliahan'),
			'tata_kelola_perkuliahan' => $this->input->post('tata_kelola_perkuliahan'),
			'id_status_pengajuan_rps' => 1,
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Rps_new_online_model->update(['id_rps' => $this->input->post('id_rps')], $data);
		echo json_encode(["status" => TRUE]);
	}


	public function ajax_submit_rps($id_rps)
	{
		$data = [
			'id_status_pengajuan_rps' => 2,
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Rps_new_online_model->update(['id_rps' => $id_rps], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$user_email = $this->ion_auth->user()->row()->email ?? ($this->session->userdata('identity') ?: 'dosen');
		$this->Rps_new_online_model->delete_by_id($id, $user_email);
		echo json_encode(["status" => TRUE, "pesan" => "Data berhasil dihapus (soft delete)!"]);
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

			$reader->setLoadAllSheets();
			$objPHPExcel = $reader->load($file_excel);

			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

			$arrayCount = count($allDataInSheet);
			$flag = 0;
			$createArray = [
				'KOLOM_1',
				'KOLOM_2',
				'KOLOM_3',
				'KOLOM_4',
				'KOLOM_5',
				'KOLOM_6',
				'KOLOM_7',
				'KOLOM_8',
				'KOLOM_9',
				'KOLOM_10',
				'KOLOM_11',
				'KOLOM_12',
				'KOLOM_13'
			];

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
				'KOLOM_12' => 'KOLOM_12',
				'KOLOM_13' => 'KOLOM_13'
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
					$kolom_13 	= $SheetDataKey['KOLOM_13'];

					$kolom_1 = filter_var(trim($allDataInSheet[$i][$kolom_1]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_2 = filter_var(trim($allDataInSheet[$i][$kolom_2]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_3 = filter_var(trim($allDataInSheet[$i][$kolom_3]), FILTER_SANITIZE_STRING);
					$kolom_4 = filter_var(trim($allDataInSheet[$i][$kolom_4]), FILTER_SANITIZE_STRING);
					$kolom_5 = filter_var(trim($allDataInSheet[$i][$kolom_5]), FILTER_SANITIZE_STRING);
					$kolom_6 = filter_var(trim($allDataInSheet[$i][$kolom_6]), FILTER_SANITIZE_STRING);
					$kolom_7 = filter_var(trim($allDataInSheet[$i][$kolom_7]), FILTER_SANITIZE_STRING);
					$kolom_8 = filter_var(trim($allDataInSheet[$i][$kolom_8]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_9 = filter_var(trim($allDataInSheet[$i][$kolom_9]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_10 = filter_var(trim($allDataInSheet[$i][$kolom_10]), FILTER_SANITIZE_STRING);
					$kolom_11 = filter_var(trim($allDataInSheet[$i][$kolom_11]), FILTER_SANITIZE_STRING);
					$kolom_12 = filter_var(trim($allDataInSheet[$i][$kolom_12]), FILTER_SANITIZE_NUMBER_INT);
					$kolom_13 = filter_var(trim($allDataInSheet[$i][$kolom_13]), FILTER_SANITIZE_STRING);


					if ($kolom_1 != '' and $kolom_2 != '') {
						$fetchData[] = [
							'id_rps' => $id_rps,
							'kolom_1' => $kolom_1,
							'kolom_2' => $kolom_2,
							'kolom_3' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_3),
							'kolom_4' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_4),
							'kolom_5' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_5),
							'kolom_6' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_6),
							'kolom_7' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_7),
							'kolom_8' => $kolom_8,
							'kolom_9' => $kolom_9,
							'kolom_10' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_10),
							'kolom_11' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_11),
							'kolom_12' => $kolom_12,
							'kolom_13' => str_ireplace(["\r\n", "\n", "\r"], '<br />', $kolom_13)
						];
					}
				}

				//$this->add_data($fetchData);
				$this->db->where('id_rps', $id_rps)->delete('siakad_new_rps_online_detail');
				$this->db->insert_batch('siakad_new_rps_online_detail', $fetchData);
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

	public function preview_rps_baru($id_rps = null)
	{
		$data_rps = $this->db->where('md5(id_rps)', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_new_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();

		$data_rps_detail = $this->db->where('md5(id_rps)', $id_rps)->get('siakad_new_rps_online_detail')->result();

		$id_mk = $data_rps->id_mk;
		$id_sms = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;

		$koordinator =  $this->db->where(['id_ptk' => $data_rps->koordinator_rps, 'id_ikatan_kerja <>' => 'X'])->get('siakad_dosen')->row();
		$kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->select('*')->from('siakad_rps_koordinator')->get();


		$output['data_rps'] = $data_rps;
		$output['data_rps_detail'] = $data_rps_detail;
		$output['koordinator_rps'] = $koordinator;
		$output['kaprodi_rps'] = $kaprodi;
		$output['rps_kelola'] =  'NIL';
		$output['nm_lemb'] =  $nm_lemb;

		//$this->load->view('cetak/pdf_rps_online', $output);
		//$mpdf->shrink_tables_to_fit = 1;
		$style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
		$html = $this->load->view('cetak/rps/rps_halaman_1_portrait', $output, true);
		$html2 = $this->load->view('cetak/rps/rps_halaman_2_landscape', $output, true);
		$doc_title = 'RPS ' . ($data_rps->nm_mk ?? '');
		$pdfFilePath = 'RPS_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk ?? '') . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Folio-P']);
		$mpdf->SetTitle($doc_title);
		$mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
		$mpdf->SetSubject($doc_title);

		$mpdf->WriteHTML($style, \Mpdf\HTMLParserMode::HEADER_CSS);
		$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

		$mpdf->AddPage('Folio-L');
		$mpdf->WriteHTML($html2, \Mpdf\HTMLParserMode::HTML_BODY);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function preview_rps_rancangan_tugas($id_rps = null)
	{
		$data_rps = $this->db->where('md5(id_rps)', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_new_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();


		$id_mk = $data_rps->id_mk;
		$id_sms = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;

		$koordinator =  $this->db->where(['id_ptk' => $data_rps->koordinator_rps, 'id_ikatan_kerja <>' => 'X'])->get('siakad_dosen')->row();
		$kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->select('*')->from('siakad_rps_koordinator')->get();


		$output['data_rps'] = $data_rps;
		$output['koordinator_rps'] = $koordinator;
		$output['kaprodi_rps'] = $kaprodi;
		$output['rps_kelola'] =  'NIL';
		$output['nm_lemb'] =  $nm_lemb;

		$style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
		$html = $this->load->view('cetak/rps/rps_rancangan_tugas', $output, true);

		$doc_title = 'Rancangan Tugas ' . ($data_rps->nm_mk ?? '');
		$pdfFilePath = 'Rancangan_Tugas_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk ?? '') . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetTitle($doc_title);
		$mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
		$mpdf->SetSubject($doc_title);

		$mpdf->WriteHTML($style, \Mpdf\HTMLParserMode::HEADER_CSS);
		$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

		$mpdf->Output($pdfFilePath, "I");
	}

	public function preview_kontrak($id_rps = null)
	{
		$data_rps = $this->db->where('md5(id_rps)', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_new_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();


		$id_mk = $data_rps->id_mk;
		$id_sms = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;

		$koordinator =  $this->db->where(['id_ptk' => $data_rps->koordinator_rps, 'id_ikatan_kerja <>' => 'X'])->get('siakad_dosen')->row();
		$kaprodi = $this->db->where('koordinator_rps', $data_rps->kaprodi_rps)->select('*')->from('siakad_rps_koordinator')->get();

		$output['data_rps'] = $data_rps;
		$output['koordinator_rps'] = $koordinator;
		$output['kaprodi_rps'] = $kaprodi;
		$output['rps_kelola'] =  'NIL';
		$output['nm_lemb'] =  $nm_lemb;

		$style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
		$html = $this->load->view('cetak/rps/rps_kontrak_kuliah', $output, true);

		$doc_title = 'Kontrak Pembelajaran ' . ($data_rps->nm_mk ?? '');
		$pdfFilePath = 'Kontrak_Pembelajaran_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data_rps->nm_mk ?? '') . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetTitle($doc_title);
		$mpdf->SetAuthor('Politeknik Wilmar Bisnis Indonesia');
		$mpdf->SetSubject($doc_title);

		$mpdf->WriteHTML($style, \Mpdf\HTMLParserMode::HEADER_CSS);
		$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

		$mpdf->Output($pdfFilePath, "I");
		// echo $html;
	}


	public function download_template()
	{
		$this->load->helper('download');
		force_download('rps_online_excel/Rincian_pertemuan_RPS.xlsx', NULL);
	}

	public function convert($id_rps)
	{
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
			],
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

		/* $sheet->setCellValue('A1', "DATA PENGABDIAN");
		$sheet->mergeCells('A1:E1');
		$sheet->getStyle('A1')->getFont()->setBold(true); */

		$sheet->mergeCells('H2:I2');

		$sheet->setCellValue('A1', "KOLOM_1");
		$sheet->setCellValue('B1', "KOLOM_2");
		$sheet->setCellValue('C1', "KOLOM_3");
		$sheet->setCellValue('D1', "KOLOM_4");
		$sheet->setCellValue('E1', "KOLOM_5");
		$sheet->setCellValue('F1', "KOLOM_6");
		$sheet->setCellValue('G1', "KOLOM_7");
		$sheet->setCellValue('H1', "KOLOM_8");
		$sheet->setCellValue('I1', "KOLOM_9");
		$sheet->setCellValue('J1', "KOLOM_10");
		$sheet->setCellValue('K1', "KOLOM_11");
		$sheet->setCellValue('L1', "KOLOM_12");

		$sheet->setCellValue('A2', "Minggu-Ke");
		$sheet->setCellValue('B2', "Pertemuan");
		$sheet->setCellValue('C2', "TEORI");
		$sheet->setCellValue('D2', "PRAKTEK");
		$sheet->setCellValue('E2', "Referensi");
		$sheet->setCellValue('F2', "Kemampuan Akhir Tiap Tahapan Belajar");
		$sheet->setCellValue('G2', "Bahan Kajian (Materi dan Pustaka)");
		$sheet->setCellValue('H2', "Metode Pembelajaran (Bentuk dan WAktu)");
		$sheet->setCellValue('J2', "Pengalaman Belajar (Evaluasi)");
		$sheet->setCellValue('K2', "Kriteria/Indikator Penilaian");
		$sheet->setCellValue('L2', "Bobot Penilaian");

		$sheet->setCellValue('H3', "Luring");
		$sheet->setCellValue('I3', "Daring");

		$sheet->setCellValue('C3', "(Menit)");
		$sheet->setCellValue('D3', "(Menit)");

		$sheet->setCellValue('A4', "(1)");
		$sheet->setCellValue('B4', "(2)");
		$sheet->setCellValue('C4', "(3)");
		$sheet->setCellValue('D4', "(4)");
		$sheet->setCellValue('E4', "(5)");
		$sheet->setCellValue('F4', "(6)");
		$sheet->setCellValue('G4', "(7)");
		$sheet->setCellValue('H4', "(8)");
		$sheet->setCellValue('I4', "(9)");
		$sheet->setCellValue('J4', "(10)");
		$sheet->setCellValue('K4', "(11)");
		$sheet->setCellValue('L4', "(12)");

		$sheet->setCellValue('A5', "Integer");
		$sheet->setCellValue('B5', "Integer");
		$sheet->setCellValue('C5', "Text");
		$sheet->setCellValue('D5', "Text");
		$sheet->setCellValue('E5', "Text");
		$sheet->setCellValue('F5', "Text");
		$sheet->setCellValue('G5', "Text");
		$sheet->setCellValue('H5', "Text");
		$sheet->setCellValue('I5', "Text");
		$sheet->setCellValue('J5', "Text");
		$sheet->setCellValue('K5', "Text");
		$sheet->setCellValue('L5', "Integer");

		$sheet->getStyle('A1:A5')->applyFromArray($style_col);
		$sheet->getStyle('B1:B5')->applyFromArray($style_col);
		$sheet->getStyle('C1:C5')->applyFromArray($style_col);
		$sheet->getStyle('D1:D5')->applyFromArray($style_col);
		$sheet->getStyle('E1:E5')->applyFromArray($style_col);
		$sheet->getStyle('F1:F5')->applyFromArray($style_col);
		$sheet->getStyle('G1:G5')->applyFromArray($style_col);
		$sheet->getStyle('H1:H5')->applyFromArray($style_col);
		$sheet->getStyle('I1:I5')->applyFromArray($style_col);
		$sheet->getStyle('J1:J5')->applyFromArray($style_col);
		$sheet->getStyle('K1:K5')->applyFromArray($style_col);
		$sheet->getStyle('L1:L5')->applyFromArray($style_col);


		$data_rps_master = $this->db->where(compact('id_rps'))->get('siakad_new_rps_online')->row();
		$jabfung = $this->Rps_new_online_model->get_excel_data($id_rps);

		$no = 1;
		$numrow = 6;
		foreach ($jabfung as $data) {
			$sheet->setCellValue('A' . $numrow, $no);
			//$clean = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/', '', $data->judul_penelitian_pengabdian);
			$sheet->setCellValue('A' . $numrow, $data->kolom_1);
			$sheet->setCellValue('B' . $numrow, $data->kolom_2);
			$sheet->setCellValue('C' . $numrow, $data->kolom_3);
			$sheet->setCellValue('D' . $numrow, $data->kolom_4);
			$sheet->setCellValue('E' . $numrow, $data->kolom_5);
			$sheet->setCellValue('F' . $numrow, $data->kolom_6);
			$sheet->setCellValue('G' . $numrow, $data->kolom_7);
			$sheet->setCellValue('H' . $numrow, $data->kolom_8);
			$sheet->setCellValue('I' . $numrow, $data->kolom_9);
			$sheet->setCellValue('J' . $numrow, $data->kolom_10);
			$sheet->setCellValue('K' . $numrow, $data->kolom_11);
			$sheet->setCellValue('L' . $numrow, $data->kolom_12);

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
			$sheet->getStyle('L' . $numrow)->applyFromArray($style_row);

			$sheet->getStyle('E' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('F' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('G' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('H' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('I' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('J' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('K' . $numrow)->getAlignment()->setWrapText(true);
			$sheet->getStyle('L' . $numrow)->getAlignment()->setWrapText(true);

			$no++;
			$numrow++;
		}

		$sheet->getColumnDimension('A')->setWidth(13);
		$sheet->getColumnDimension('B')->setWidth(13);
		$sheet->getColumnDimension('C')->setWidth(13);
		$sheet->getColumnDimension('D')->setWidth(13);
		$sheet->getColumnDimension('E')->setWidth(45);
		$sheet->getColumnDimension('F')->setWidth(45);
		$sheet->getColumnDimension('G')->setWidth(45);
		$sheet->getColumnDimension('H')->setWidth(45);
		$sheet->getColumnDimension('I')->setWidth(45);
		$sheet->getColumnDimension('J')->setWidth(45);
		$sheet->getColumnDimension('K')->setWidth(45);
		$sheet->getColumnDimension('L')->setWidth(45);

		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->setTitle("Pengabdian");

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename= ' . $data_rps_master->nm_mk . '.xlsx'); // Set nama file excel nya
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function upload_organisasi_materi()
	{
		$this->load->library('upload');

		$id = $this->input->post('id_rps');
		$data_existing = $this->db->where('id_rps', $id)->get('siakad_new_rps_online')->row();
		$f = $data_existing->image_organisasi;
		// $nm_files = 'Draft_' . $data_existing->no_dokumen . '_' . $data_existing->nama_standard;
		// $nm_files = str_replace(["/", "&", " ", "."], ["_", "_", "_", "_"], $nm_files) . '.pdf';

		$path = FILEPATH_RPS;

		if (!empty($f)) {
			if (file_exists($path . $f)) {
				unlink($path . $f);
			}
		}

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']      = 2048;
		$config['max_width']     = 1920;
		$config['max_height']    = 1080;
		$config['encrypt_name'] = TRUE;
		$config['overwrite'] = FALSE;

		$this->upload->initialize($config);
		if ($_FILES['file_upload']['size'] > 0) {

			if ($this->upload->do_upload('file_upload')) {

				$gbr = $this->upload->data();

				$nm_file_upload = $this->upload->file_name;
				$this->db->update('siakad_new_rps_online', ['image_organisasi' => $nm_file_upload], ['id_rps' => $id]);
				$this->session->set_flashdata('sukses', 'File berhasil di upload');
				redirect($_SERVER["HTTP_REFERER"]);
			} else {
				$error = $this->upload->display_errors();
				$this->session->set_flashdata('error', $error);
				redirect($_SERVER["HTTP_REFERER"]);
			}
		} else {
			echo $_FILES['file_upload']['size'];
		}
	}
}
