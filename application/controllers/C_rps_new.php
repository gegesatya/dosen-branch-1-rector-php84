<?php
defined('BASEPATH') or exit('No direct script access allowed');

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class C_rps_new extends CI_Controller
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
		$output['menu_aktif'] = 'm_rps_new';
		$output['parent_ws'] = 'active open';
		$output['content'] = 'rps_new/rps_new_online_view';
		$output['after_page'] = 'rps_new/rps_new_online_after_page';
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
											<li><a href="' . site_url('C_rps_new/preview_rps_baru/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> RPS (16 Minggu)</a></li>
											<li><a href="' . site_url('C_rps_new/preview_kontrak/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> Kontrak Pembelajaran</a></li>
											<li><a href="' . site_url('C_rps_new/preview_rps_rancangan_tugas/' . md5($l->id_rps)) . '" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i> Rancangan Tugas</a></li>
											<li class="divider" style="margin:4px 0;"></li>
											<li class="dropdown-header" style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; padding:4px 14px;">Ekspor Matriks</li>
											<li><a href="' . site_url('C_rps_new/convert/' . $l->id_rps) . '"><i class="fa fa-file-excel-o text-success"></i> Detail Pertemuan (.xls)</a></li>
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
		if ($data && !empty($data->id_mk) && (empty($data->nm_mk) || empty($data->kode_mk))) {
			$mk_info = $this->db->where('id_mk', $data->id_mk)->get('feeder_mata_kuliah')->row();
			if ($mk_info) {
				if (empty($data->nm_mk)) $data->nm_mk = $mk_info->nm_mk;
				if (empty($data->kode_mk)) $data->kode_mk = $mk_info->kode_mk;
			}
		}

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
			echo json_encode(["status" => TRUE, "redirect" => site_url('C_rps_new')]);
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

	public function ajax_delete($id = null)
	{
		$this->output->set_content_type('application/json');

		if (empty($id)) {
			$id = $this->input->post('id') ?: $this->input->get('id');
		}
		if (empty($id)) {
			$total_segments = $this->uri->total_segments();
			for ($s = $total_segments; $s >= 1; $s--) {
				$seg = $this->uri->segment($s);
				if (!empty($seg) && is_numeric($seg)) {
					$id = $seg;
					break;
				}
			}
		}

		$id = (int)$id;
		if (empty($id)) {
			echo json_encode(["status" => FALSE, "pesan" => "ID RPS tidak valid."]);
			return;
		}

		$rps = $this->Rps_new_online_model->get_by_id($id);
		if (!$rps) {
			echo json_encode(["status" => FALSE, "pesan" => "Data RPS tidak ditemukan atau sudah dihapus."]);
			return;
		}

		$user_email = $this->ion_auth->user()->row()->email ?? ($this->session->userdata('identity') ?: 'dosen');
		$deleted = $this->Rps_new_online_model->delete_by_id($id, $user_email);

		if ($deleted) {
			$mk_name = (!empty($rps->kode_mk) ? '[' . $rps->kode_mk . '] ' : '') . ($rps->nm_mk ?: 'Mata Kuliah');
			echo json_encode([
				"status" => TRUE, 
				"pesan"  => "Dokumen RPS untuk {$mk_name} berhasil dihapus (soft delete)."
			]);
		} else {
			echo json_encode(["status" => FALSE, "pesan" => "Gagal menghapus dokumen RPS."]);
		}
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

// 		$this->load->view('cetak/pdf_rps_online', $output);
		//$mpdf->shrink_tables_to_fit = 1;


		$style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
		$html = $this->load->view('cetak/rps/rps_halaman_1_portrait', $output, true);
		$html2 = $this->load->view('cetak/rps/rps_halaman_2_landscape', $output, true);
		$pdfFilePath  = 'RPS_' . $data_rps->nm_mk . '.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Folio-P']);


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

		//$this->load->view('cetak/pdf_rps_online', $output);
		//$mpdf->shrink_tables_to_fit = 1;
		$style = $this->load->view('cetak/rps/_pdf_rps_style', $output, true);
		$html = $this->load->view('cetak/rps/rps_rancangan_tugas', $output, true);

		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

		// $mpdf->WriteHTML($style, 1);
		// $mpdf->WriteHTML($html, 2);

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

		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

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

	/**
	 * AJAX: Summary RPS OBE & Evaluasi Matriks 16 Pertemuan
	 * Membaca data RPS, mengevaluasi total bobot (target 100%), sebaran UTS/UAS, Bloom CPMK, dan keselarasan CPL Kurikulum 2026
	 */
	/*
	public function ajax_summary_rps($id_rps = NULL)
	{
		if (empty($id_rps)) {
			$id_rps = $this->input->post('id_rps');
		}

		$id_rps = (int)$id_rps;
		if (empty($id_rps)) {
			echo json_encode(['status' => false, 'message' => 'ID RPS tidak valid.']);
			return;
		}

		// 1. Ambil data RPS utama
		$rps = $this->db->where('id_rps', $id_rps)->get('siakad_new_rps_online')->row();
		if (!$rps) {
			$rps = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online')->row();
		}
		if (!$rps) {
			$rps = $this->db->where('id_rps', $id_rps)->get('siakad_rps_obe')->row();
		}

		if (!$rps) {
			echo json_encode(['status' => false, 'message' => 'Dokumen RPS tidak ditemukan dalam database.']);
			return;
		}

		// 2. Ambil Dosen, Koordinator & Kaprodi
		$koordinator_nama = resolve_dosen_nama_lengkap($rps->koordinator_rps ?? '');
		$kaprodi_nama = resolve_dosen_nama_lengkap($rps->kaprodi_rps ?? '');
		$pengampu_nama = resolve_dosen_nama_lengkap($rps->dosen_pengampu ?? '');

		// 3. Ambil Rincian 16 Pertemuan
		$details = $this->db->where('id_rps', $id_rps)->order_by('kolom_1', 'ASC')->get('siakad_new_rps_online_detail')->result();
		if (empty($details)) {
			$details = $this->db->where('id_rps', $id_rps)->order_by('kolom_1', 'ASC')->get('siakad_rps_online_detail')->result();
		}
		if (empty($details)) {
			$details = $this->db->where('id_rps', $id_rps)->order_by('kolom_1', 'ASC')->get('siakad_rps_obe_detail')->result();
		}

		$count_pertemuan = count($details);
		$total_bobot = 0;
		$bobot_uts = 0;
		$bobot_uas = 0;
		$bobot_formatif = 0;
		$bobot_project = 0;
		$rows_with_bobot = 0;
		$rows_missing_indicator = 0;
		$total_teori_minutes = 0;
		$total_praktek_minutes = 0;
		$matrix_evaluation = [];

		if (!empty($details)) {
			foreach ($details as $d) {
				$m = (int)($d->kolom_1 ?? $d->minggu ?? 0);
				$p = (int)($d->kolom_2 ?? $d->pertemuan ?? 0);
				$b = (float)($d->kolom_12 ?? $d->bobot ?? 0);
				$total_bobot += $b;

				$t = (float)($d->kolom_8 ?? $d->waktu_teori ?? 0);
				$pr = (float)($d->kolom_9 ?? $d->waktu_praktek ?? 0);
				$total_teori_minutes += ($t <= 10 ? $t * 50 : $t);
				$total_praktek_minutes += ($pr <= 10 ? $pr * 50 : $pr);
				$all_row_text = ($d->kolom_4 ?? '') . ' ' . ($d->kolom_3 ?? '') . ' ' . ($d->kolom_5 ?? '') . ' ' . ($d->kolom_6 ?? '');
				$is_uts = ($m === 8 || $p === 8);
				$is_uas = ($m === 16 || $p === 16);
				$is_proj = (preg_match('/\b(project|proyek|kasus|case method|pbl)\b/i', $all_row_text));

				if ($is_uts) {
					$bobot_uts += $b;
				} elseif ($is_uas) {
					$bobot_uas += $b;
				} else {
					$bobot_formatif += $b;
					if ($is_proj) $bobot_project += $b;
				}

				$has_ind = !empty(trim(strip_tags($d->kolom_11 ?? '')));
				$has_krit = !empty(trim(strip_tags($d->kolom_10 ?? '')));

				if ($b > 0) {
					$rows_with_bobot++;
					if (!$has_ind && !$has_krit) {
						$rows_missing_indicator++;
					}
				}

				$matrix_evaluation[] = [
					'minggu'       => $m,
					'pertemuan'    => $p,
					'kemampuan'    => strip_tags($d->kolom_3 ?? ''),
					'bahan_kajian' => strip_tags($d->kolom_4 ?? ''),
					'metode'       => strip_tags(($d->kolom_6 ?? '') ?: ($d->kolom_5 ?? '')),
					'bobot'        => $b,
					'has_eval'     => ($has_ind || $has_krit)
				];
			}
		}

		// Status Evaluasi Bobot
		if ($count_pertemuan === 0) {
			$bobot_status = 'EMPTY';
			$bobot_badge = 'danger';
			$bobot_msg = 'Rincian matriks 16 pertemuan belum diupload.';
			$bobot_score = 0;
		} elseif ($total_bobot == 100) {
			$bobot_status = 'VALID';
			$bobot_badge = 'success';
			$bobot_msg = 'Total bobot asesmen tepat 100% (Sempurna & Sesuai Standar OBE).';
			$bobot_score = 100;
		} elseif ($total_bobot > 100) {
			$bobot_status = 'OVER';
			$bobot_badge = 'danger';
			$bobot_msg = "Total bobot {$total_bobot}%, melebihi 100% sebesar " . ($total_bobot - 100) . "%. Harap sesuaikan kembali file rincian.";
			$bobot_score = max(30, 100 - ($total_bobot - 100));
		} elseif ($total_bobot > 0) {
			$bobot_status = 'UNDER';
			$bobot_badge = 'warning';
			$bobot_msg = "Total bobot baru {$total_bobot}%, masih kurang " . (100 - $total_bobot) . "% dari target 100%.";
			$bobot_score = round(($total_bobot / 100) * 80);
		} else {
			$bobot_status = 'ZERO';
			$bobot_badge = 'danger';
			$bobot_msg = 'Bobot penilaian masih 0% di semua pertemuan.';
			$bobot_score = 10;
		}

		// 4. Analisis Taksonomi Bloom pada Sub-CPMK 16 Pertemuan & CPMK
		$cpmk_raw = $rps->cpmk ?? ($rps->capaian_pembelajaran_mata_kuliah ?? ($rps->cp_mata_kuliah ?? ''));
		$all_bloom_text = strip_tags($cpmk_raw);
		if (!empty($details)) {
			foreach ($details as $d) {
				$sub_text = ($d->kolom_3 ?? '') . ' ' . ($d->kolom_2 ?? '') . ' ' . ($d->kolom_5 ?? '') . ' ' . ($d->kolom_6 ?? '');
				$all_bloom_text .= ' ' . strip_tags($sub_text);
			}
		}

		$bloom = [
			'C2' => ['label' => 'C2 - Memahami', 'count' => 0, 'color' => '#3b82f6'],
			'C3' => ['label' => 'C3 - Menerapkan', 'count' => 0, 'color' => '#10b981'],
			'C4' => ['label' => 'C4 - Menganalisis', 'count' => 0, 'color' => '#f59e0b'],
			'C5' => ['label' => 'C5 - Mengevaluasi', 'count' => 0, 'color' => '#8b5cf6'],
			'C6' => ['label' => 'C6 - Mencipta', 'count' => 0, 'color' => '#ec4899']
		];
		$bloom_keywords = [
			'C2' => ['memahami', 'menjelaskan', 'mengidentifikasi', 'menguraikan', 'menerangkan', 'mendeskripsikan', 'merangkum', 'mengenali', 'mengklasifikasi'],
			'C3' => ['menerapkan', 'menggunakan', 'menghitung', 'melaksanakan', 'mempraktikkan', 'mengoperasikan', 'menyelesaikan', 'mengukur', 'mengimplementasikan', 'menyimulasikan', 'menentukan'],
			'C4' => ['menganalisis', 'menelaah', 'membedakan', 'mengorelasikan', 'membandingkan', 'menguji', 'mendiagnosis', 'menginterpretasi', 'mengkaji', 'memecahkan', 'memetakan', 'valuasi'],
			'C5' => ['mengevaluasi', 'menilai', 'mengaudit', 'mengkritisi', 'menyimpulkan', 'memvalidasi', 'mempertimbangkan'],
			'C6' => ['merancang', 'menyusun', 'membuat', 'mengembangkan', 'mendesain', 'merumuskan', 'memproduksi', 'mengonstruksi', 'merencanakan', 'membangun']
		];

		$bloom_text_lower = strtolower($all_bloom_text);
		$total_bloom_hits = 0;
		foreach ($bloom_keywords as $lvl => $kws) {
			foreach ($kws as $kw) {
				$hits = substr_count($bloom_text_lower, $kw);
				$bloom[$lvl]['count'] += $hits;
				$total_bloom_hits += $hits;
			}
		}

		// 5. Cross-Check Keselarasan CPL Kurikulum 2026 (Studio CPL)
		$kode_mk = trim($rps->kode_mk ?? '');
		$nm_mk   = trim($rps->nm_mk ?? ($rps->general_mk ?? ''));
		$id_mk   = trim($rps->id_mk ?? '');

		// Cari Prodi dari pustaka kurikulum
		$pustaka = null;
		if (strlen($kode_mk) >= 3) {
			$prefix = strtoupper(substr($kode_mk, 0, 3));
			$prodi_map = [
				'TRP' => 'Teknologi Rekayasa Perangkat Lunak',
				'TPL' => 'Teknologi Rekayasa Perangkat Lunak',
				'AGH' => 'Agribisnis Hortikultura',
				'AGB' => 'Agribisnis Hortikultura',
				'PKA' => 'Pengelolaan Konvensi dan Acara',
				'MPI' => 'Manajemen Pemasaran Internasional',
				'AKP' => 'Akuntansi Perpajakan',
				'AKT' => 'Akuntansi Perpajakan'
			];
			if (isset($prodi_map[$prefix])) {
				$pustaka = $this->db->like('nama_prodi', $prodi_map[$prefix])->get('siakad_pustaka_kurikulum')->row();
			}
		}

		if (!$pustaka && !empty($id_mk)) {
			$mk_sms = $this->db->query("SELECT s.nm_lemb FROM feeder_mata_kuliah mk JOIN sms s ON s.id_sms = mk.id_sms WHERE mk.id_mk = ? LIMIT 1", [$id_mk])->row();
			if ($mk_sms && !empty($mk_sms->nm_lemb)) {
				$pustaka = $this->db->like('nama_prodi', $mk_sms->nm_lemb)->get('siakad_pustaka_kurikulum')->row();
			}
		}

		if (!$pustaka) {
			$pustaka = $this->db->order_by('id', 'DESC')->get('siakad_pustaka_kurikulum')->row();
		}

		$curriculum_cpl_list = [];
		$cpl_match_count = 0;
		$cpl_suggested_for_mk = [];
		$matched_cpl_codes = [];
		$total_cpl_prodi = 0;
		$total_cpl_mk = 0;
		$cpl_rps_text = strip_tags($rps->cpl ?? ($rps->cpl_prodi ?? ($rps->capaian_sikap . ' ' . $rps->capaian_pengetahuan . ' ' . $rps->capaian_keterampilan_khusus . ' ' . $rps->capaian_keterampilan_umum)));
		$clean_rps_text = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($cpl_rps_text));

		if ($pustaka && !empty($pustaka->normalized_json)) {
			$cur_data = json_decode($pustaka->normalized_json, true);
			$raw_cpl_list = $cur_data['official_cpl'] ?? ($cur_data['cpl_list'] ?? []);
			$raw_mk_list = $cur_data['mata_kuliah'] ?? ($cur_data['mata_kuliah_list'] ?? []);
			$total_cpl_prodi = $cur_data['official_cpl_count'] ?? count($raw_cpl_list);

			// Cari mata kuliah terkait di dokumen kurikulum
			$found_mk = null;
			if (!empty($raw_mk_list)) {
				foreach ($raw_mk_list as $mk_cur) {
					$cur_kdmk = trim($mk_cur['kode_mk'] ?? '');
					$cur_nmmk = trim($mk_cur['nama_mk'] ?? '');
					if ((!empty($kode_mk) && stripos($cur_kdmk, $kode_mk) !== false) || 
					    (!empty($nm_mk) && stripos($cur_nmmk, $nm_mk) !== false) ||
					    (!empty($cur_nmmk) && !empty($nm_mk) && similar_text(strtolower($cur_nmmk), strtolower($nm_mk)) > (strlen($nm_mk) * 0.7))) {
						$found_mk = $mk_cur;
						break;
					}
				}
			}

			if ($found_mk) {
				$cpl_suggested_for_mk = $found_mk['effective_cpl_codes'] ?? ($found_mk['direct_cpl_codes'] ?? ($found_mk['cpl_mapped'] ?? ($found_mk['cpl_terkait'] ?? [])));
			}
			$total_cpl_mk = count($cpl_suggested_for_mk);

			// Hitung CPL yang terakomodasi di dokumen RPS
			$target_cpls = ($total_cpl_mk > 0) ? $cpl_suggested_for_mk : array_map(function($c) { return $c['code'] ?? ($c['kode'] ?? ''); }, $raw_cpl_list);
			foreach ($target_cpls as $c_kode) {
				$c_clean = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($c_kode));
				if (!empty($c_clean) && strpos($clean_rps_text, $c_clean) !== false) {
					$cpl_match_count++;
					$matched_cpl_codes[] = $c_kode;
				}
			}
		}

		$base_count = ($total_cpl_mk > 0) ? $total_cpl_mk : ($total_cpl_prodi > 0 ? $total_cpl_prodi : 0);
		$cpl_alignment_pct = $base_count > 0 ? min(100, round(($cpl_match_count / $base_count) * 100)) : 100;

		// 6. Evaluasi Diferensiasi CPL vs CPMK (Anti Duplikasi/Copy-Paste)
		$cpmk_clean_text = preg_replace('/[^a-zA-Z0-9]/', '', strtolower(strip_tags($cpmk_raw)));
		$cpl_cpmk_similarity = 0;
		if (!empty($clean_rps_text) && !empty($cpmk_clean_text)) {
			similar_text($clean_rps_text, $cpmk_clean_text, $cpl_cpmk_similarity);
		}
		$is_cpl_cpmk_duplicate = ($clean_rps_text === $cpmk_clean_text || $cpl_cpmk_similarity >= 85);

		$tgl_pembuatan = (!empty($rps->tanggal_rps) && $rps->tanggal_rps != '0000-00-00') ? tgl_indo($rps->tanggal_rps) : ((!empty($rps->create_date) && $rps->create_date != '0000-00-00 00:00:00') ? tgl_indo(date('Y-m-d', strtotime($rps->create_date))) : '-');

		$response = [
			'status'                  => true,
			'id_rps'                  => $id_rps,
			'kode_mk'                 => $kode_mk,
			'nm_mk'                   => $nm_mk,
			'sks_mk'                  => $rps->sks_mk ?? 3,
			'sks_tm'                  => $rps->sks_tm ?? 2,
			'sks_prak'                => $rps->sks_prak ?? 1,
			'tgl_pembuatan'           => $tgl_pembuatan,
			'koordinator'             => $koordinator_nama,
			'kaprodi'                 => $kaprodi_nama,
			'pengampu'                => $pengampu_nama,
			'count_pertemuan'         => $count_pertemuan,
			'total_bobot'             => $total_bobot,
			'bobot_status'            => $bobot_status,
			'bobot_badge'             => $bobot_badge,
			'bobot_msg'               => $bobot_msg,
			'bobot_score'             => $bobot_score,
			'bobot_uts'               => $bobot_uts,
			'bobot_uas'               => $bobot_uas,
			'bobot_formatif'          => $bobot_formatif,
			'bobot_project'           => $bobot_project,
			'rows_with_bobot'         => $rows_with_bobot,
			'rows_missing_indicator'  => $rows_missing_indicator,
			'total_teori_minutes'     => $total_teori_minutes,
			'total_praktek_minutes'   => $total_praktek_minutes,
			'bloom'                   => $bloom,
			'total_bloom_hits'        => $total_bloom_hits,
			'prodi_nama'              => $pustaka ? $pustaka->nama_prodi : 'Politeknik WBI',
			'tahun_kurikulum'         => ($pustaka && !empty($pustaka->tahun_kurikulum)) ? 'Kurikulum ' . $pustaka->tahun_kurikulum : 'Pustaka Kurikulum',
			'nama_dokumen'            => $pustaka ? ($pustaka->file_name ?: $pustaka->judul_dokumen) : 'Buku Kurikulum Prodi',
			'tahun_dokumen'           => $pustaka ? $pustaka->tahun_kurikulum : '2026',
			'total_cpl_prodi'         => $total_cpl_prodi,
			'total_cpl_mk'            => $total_cpl_mk,
			'cpl_match_count'         => $cpl_match_count,
			'cpl_alignment_pct'       => $cpl_alignment_pct,
			'cpl_cpmk_similarity'     => round($cpl_cpmk_similarity),
			'is_cpl_cpmk_duplicate'   => $is_cpl_cpmk_duplicate,
			'matched_cpl_codes'       => $matched_cpl_codes,
			'cpl_suggested_for_mk'    => $cpl_suggested_for_mk,
			'matrix_evaluation'       => $matrix_evaluation
		];

		$this->output->set_content_type('application/json');
		echo json_encode($response);
	}
	*/
}

