<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_persetujuan_krs_2 extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lecturer_persetujuan_krs_model_2', 'Lecturer_persetujuan_krs_model_2');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus login sebagai dosen.');
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		//$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$siakad_krs = $this->db->where(['id_dosen_siakad' => $id_ptk, 'id_status_krs' => 2, 'id_smt' => $id_smt])->get('siakad_krs');
		if ($siakad_krs->num_rows() > 0) {
			$this->session->set_flashdata('jumlah', $siakad_krs->num_rows());
		}

		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_krs = $setting->tgl_akhir_krs;

		if (strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s'))) {
			$this->session->set_flashdata('msg', 'Periode pendaftaran KRS sudah berakhir. Approval dan perbaikan KRS sudah ditutup.');
		}

		$output['judul_besar'] = 'Persetujuan KRS';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_persetujuan_krs';
		$output['parent_1'] = 'active open';
		$output['content'] = 'persetujuan_krs_2/persetujuan_krs_view_2';
		$output['after_page'] = 'persetujuan_krs_2/persetujuan_krs_after_page_2';
		$output['dd_status_krs'] 	= $this->Lecturer_persetujuan_krs_model_2->f_status_krs();

		$this->load->view('master_page', $output);
	}

	public function ajax_list($status)
	{
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_krs = $setting->tgl_akhir_krs;

		$list = $this->Lecturer_persetujuan_krs_model_2->get_datatables($status);
		$data = [];
		$no = $_POST['start'];

		foreach ($list as $rg) {
			$no++;
			$row = [];

			if (!empty($rg->id_status_krs)) {
				$status_1 = '<strong>' . $this->db->where('id_status_krs', $rg->id_status_krs)->get('siakad_status_krs')->row()->nm_status_krs . '</strong>';
				if ($rg->id_status_krs == 3) {
					$tgl_approve = date('d-m-Y', strtotime($rg->approved_date));
					$jam_approve = date('H:i', strtotime($rg->approved_date));
					$status_1 .= '<br><small>Tgl : ' . $tgl_approve . '<br>Jam : ' . $jam_approve . '</small>';
				} elseif ($rg->id_status_krs == 2) {
					$tgl_submit = date('d-m-Y', strtotime($rg->pengajuan_date));
					$jam_submit = date('H:i', strtotime($rg->pengajuan_date));
					$status_1 .= '<br><small>Tgl : ' . $tgl_submit . '<br>Jam : ' . $jam_submit . '</small>';
				} 
			}

			$data_rencana_studi = $this->db->where(['id_rencana_studi' => $rg->id_rencana_studi])->get('siakad_krs')->row();

			if ($data_rencana_studi->id_rencana_studi == 3) {
				$total_sks = $this->db->where(['sn.nipd' => $rg->nipd, 'kk.id_smt' => $rg->id_smt])
					->select('sum(kk.sks_mk) as total_sks', FALSE)
					->from('siakad_nilai_baru sn')
					->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
					->get()->row()->total_sks;
			} else {
				$total_sks = $this->db->where(['id_rencana_studi' => $rg->id_rencana_studi])
					->select('sum(kk.sks_mk) as total_sks', FALSE)->from('siakad_krs_detail kd')
					->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
					->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
					->get()->row()->total_sks;
			}


			if ($rg->id_status_krs == 2) {
				if (strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s'))) {
					$html = '-';
				} else {
					if ($total_sks <= 24) {
						$html = '<a href="javascript:void(0)" class="btn btn-success btn-sm"
						onclick="show_krs(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ')">
						<i class="fa fa-cloud-upload "></i> Approval</a>';
					} else {
						$html = '<a href="' . site_url("Lecturer_persetujuan_krs/preview_pdf/$rg->id_rencana_studi") .
							'" class="btn btn-xs btn-default blue-stripe" target="_new"></i> Lihat KRS</a>
						<a href="javascript:void(0)" onclick="perbaikan_krs(' . "'" . $rg->id_rencana_studi . "'" . ')" 
						class="btn btn-xs btn-default red-stripe"></i> Jumlah SKS > 24<br>Perbaikan</a>';
					}
				}
			} else {
				$html = '<a href="' . site_url("Lecturer_persetujuan_krs/preview_pdf/$rg->id_rencana_studi") . '" class="btn btn-xs btn-primary" target="_new"><i class="icon-speedometer"></i> Preview';
			}

			if ($rg->id_status_krs == 3) {
				if (strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s'))) {
					$row[] = '<a href="' . site_url("Lecturer_persetujuan_krs/preview_pdf/$rg->id_rencana_studi") .
						'" class="btn btn-xs btn-default blue-stripe" target="_new"></i> Lihat KRS</a>';
				} else {
					$row[] = '<a href="' . site_url("Lecturer_persetujuan_krs/preview_pdf/$rg->id_rencana_studi") .
						'" class="btn btn-xs btn-default blue-stripe" target="_new"></i> Lihat KRS</a>
						<a href="javascript:void(0)" onclick="perbaikan_krs(' . "'" . $rg->id_rencana_studi . "'" . ')" 
						class="btn btn-xs btn-default red-stripe"></i> Perbaikan</a>';
				}
			} elseif ($rg->id_status_krs == 4) {
				$row[] = '<span class="label label-danger">Ditolak</span>';
			} else {
				$row[] = $html;
			}

			$row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd . '<br>' . '<i class="fa fa-phone"></i> ' . $rg->registrasi_no_hp;
			$row[] = (int) $total_sks;
			$html = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
							onclick="balas_pesan(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ')">
							<i class="fa fa-mail-reply"></i> <small>Kirim Pesan</small></a>';

			if (!empty($rg->catatan_dosen_wali)) {
				$row[] = '<small>' . $rg->catatan_dosen_wali . '<br>' . $html . '</small>';
			} else {
				$row[] = $html;
			}
			$row[] = $status_1;

			$data[] = $row;
		}


		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lecturer_persetujuan_krs_model_2->count_all($status),
			"recordsFiltered" => $this->Lecturer_persetujuan_krs_model_2->count_filtered($status),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function view_krs($id_rencana_studi, $nipd, $id_smt)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();

		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();

		if ($data_rencana_studi->id_rencana_studi == 3) {
			$krs = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap, kk.nm_kls')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where([
					'sn.nipd' => $nipd,
					'kk.id_smt' => $id_smt
				])
				->order_by('nm_mk')
				->get();
		} else {
			$krs = $this->db->where(compact('id_rencana_studi'))
				->select('mk.*, kk.nm_kls')->from('siakad_krs_detail kd')
				->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->get();
		}

		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-bordered">',
			'thead_open'          => '<thead>',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];

		$this->table->set_template($tmpl);
		$this->table->set_heading([
			'No',
			'Kelas',
			'Semester<br>Tempuh',
			'Kode/<br>Code',
			'Mata Kuliah/Subjects',
			'SKS/<br>Credit', 'T', 'P', 'PL'
		]);


		$i = 1;

		$sks_mk = $sks_tm = $sks_prak = $sks_prak_lap = 0;

		foreach ($krs->result() as $l_nilai) {
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				['data' => $l_nilai->nm_kls, 'class' => 'text-center'],
				['data' => substr($l_nilai->nm_kls, 1, 1), 'class' => 'text-center'],

				$l_nilai->kode_mk,
				$l_nilai->nm_mk,
				['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_tm, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_prak, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_prak_lap, 'class' => 'text-center']
			]);

			$sks_mk += $l_nilai->sks_mk;
			$sks_tm += $l_nilai->sks_tm;
			$sks_prak += $l_nilai->sks_prak;
			$sks_prak_lap += $l_nilai->sks_prak_lap;
		}

		$this->table->add_row([
			['data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 5],
			['data' => $sks_mk, 'class' => 'text-center'],
			['data' => $sks_tm, 'class' => 'text-center'],
			['data' => $sks_prak, 'class' => 'text-center'],
			['data' => $sks_prak_lap, 'class' => 'text-center']
		]);

		$nilai_tabel =  $this->table->generate();
		$output['mahasiswa'] = $mahasiswa;
		$output['tabel'] = $nilai_tabel;

		$nm_pd = $mahasiswa->nm_pd;
		$nipd = $mahasiswa->nipd;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$akademik_record = $this->view_akademik($nipd);

		$log_message = '';
		if (!empty($data_rencana_studi->catatan_dosen_wali)) {
			$log_message =  $data_rencana_studi->catatan_dosen_wali;
		}


		echo json_encode([
			'tabel_krs' => $nilai_tabel, 'tabel_akademik_record' => $akademik_record,
			'nm_pd' => $nm_pd, 'nipd' => $nipd, 'id_smt' => $id_smt,
			'catatan_dosen_wali' => $data_rencana_studi->catatan_dosen_wali,
			'id_status_krs' => $data_rencana_studi->id_status_krs,
			'log_message' => $log_message
		]);
	}

	public function view_akademik($nipd)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, id_reg_pd_feeder')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get();

		// if ($mahasiswa->num_rows() > 0) {
		// 	$data_kuliah_mahasiswa = $this->db->where('id_reg_pd', $mahasiswa->row()->id_reg_pd_feeder)->order_by('id_smt')->get('kuliah_mahasiswa');
		// } else {
		$data_kuliah_mahasiswa = $this->db->where('nipd', $nipd)->order_by('id_smt')->get('kuliah_mahasiswa');
		// }



		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-bordered">',
			'thead_open'          => '<thead>',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];

		$this->table->set_template($tmpl);
		$this->table->set_heading([
			'No',
			'Semester',
			'IPS',
			'SKS',
			'IPK',
			'SKS Total'
		]);

		$i = 1;
		if ($mahasiswa->num_rows() > 0) {
			foreach ($data_kuliah_mahasiswa->result() as $l_nilai) {
				$this->table->add_row([
					['data' => $i++ . '.', 'class' => 'text-right'],
					['data' => $l_nilai->id_smt, 'class' => 'text-center'],
					['data' => $l_nilai->ips, 'class' => 'text-center'],
					['data' => $l_nilai->sks_smt, 'class' => 'text-center'],
					['data' => $l_nilai->ipk, 'class' => 'text-center'],
					['data' => $l_nilai->sks_total, 'class' => 'text-center']
				]);
			}
		}

		$nilai_tabel =  $this->table->generate();
		return $nilai_tabel;
	}

	public function show_makul_prodi()
	{
		$id_smt = $this->session->userdata('id_smt');
		$id_sms = $this->session->userdata('prodi');

		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-black">',
			'thead_open'          => '<thead>',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];

		$this->table->set_template($tmpl);
		$this->table->set_heading([
			'No',
			'NM KLS',
			'SMT',
			'Kode/<br>Code',
			'Mata Kuliah/Subjects',
			'SKS/<br>Credit', 'T', 'P', 'PL'
		]);

		$i = 1;
		$kelas_kuliah = $this->db->where(['id_smt' => $id_smt, 'kk.id_sms' => $id_sms])
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'kk.id_mk = mk.id_mk')
			->order_by('nm_kls')
			->get();

		foreach ($kelas_kuliah->result() as $list) {
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				$list->nm_kls,
				substr($list->nm_kls, 1, 1),
				$list->kode_mk,
				$list->nm_mk,
				['data' => (int) $list->sks_mk, 'class' => 'text-center'],
				['data' => (int) $list->sks_tm, 'class' => 'text-center'],
				['data' => (int) $list->sks_prak, 'class' => 'text-center'],
				['data' => (int) $list->sks_prak_lap, 'class' => 'text-center']
			]);
		}

		$nilai_tabel =  $this->table->generate();
		$output['tabel'] = $nilai_tabel;
		$output['nm_smt'] = $this->db->where('id_smt', $id_smt)->get('semester')->row()->nm_smt;
		$output['nm_lemb'] = $this->db->where(compact('id_sms'))->get('sms')->row()->nm_lemb;
		$output['tipe_file'] = 1;

		$this->load->view('pdf/pdf_makul_prodi', $output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Lecturer_persetujuan_krs_model_2->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_get_krs($id)
	{
		$data = $this->db->select('m.nm_pd,krs.*, d.nm_ptk, sum(kk.sks_mk) as total_sks', FALSE)->set('cek_val = "TRUE"')->from('siakad_krs krs')
			->join('mahasiswa_pt mp', 'mp.nipd = krs.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('siakad_krs_detail kd', 'kd.id_rencana_studi = krs.id_rencana_studi')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = kd.id_kls')
			->where('krs.id_rencana_studi', $id)->get()->row();

		$cek_val = $this->validasi_krs($data);
		$data->cek_val = $cek_val;

		echo json_encode($data);
	}

	private function validasi_krs($data)
	{
		$id_rencana_studi = $data->id_rencana_studi;
		$nm_sdm = $data->nm_ptk;
		$total_sks = $data->total_sks;
		$registrasi_alamat = $data->registrasi_alamat;
		$registrasi_no_hp = $data->registrasi_no_hp;
		$registrasi_no_hp_ortu = $data->registrasi_no_hp_ortu;
		$registrasi_nominal_spp = $data->registrasi_nominal_spp;
		$total_sks = $data->total_sks;
		$img_slip_spp = $data->img_slip_spp;

		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($img_slip_spp == '') {
			$data['inputerror'][] = 'img_slip_spp';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($total_sks == '') {
			$data['inputerror'][] = 'total_sks';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($registrasi_nominal_spp == '') {
			$data['inputerror'][] = 'registrasi_nominal_spp';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($registrasi_no_hp_ortu == '') {
			$data['inputerror'][] = 'registrasi_no_hp_ortu';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($id_rencana_studi == '') {
			$data['inputerror'][] = 'id_rencana_studi';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($nm_sdm == '') {
			$data['inputerror'][] = 'nm_sdm';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		/*if($nm_paket_krs == '')
		{
			$data['inputerror'][] = 'nm_paket_krs';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}*/

		if ($total_sks == '') {
			$data['inputerror'][] = 'total_sks';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($registrasi_alamat == '') {
			$data['inputerror'][] = 'registrasi_alamat';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($registrasi_no_hp == '') {
			$data['inputerror'][] = 'registrasi_no_hp';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			//echo json_encode($data);
			return $data['inputerror'];
			//exit();
		} else {
			return TRUE;
		}
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('id_status_krs') == '') {
			$data['inputerror'][] = 'id_status_krs';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	/* public function persetujuan_krs()
	{
		//$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();
		$id_reg_pd = $this->db->where(['nipd' => $data_krs->nipd])->get('mahasiswa_pt')->row()->nipd;

		$this->db->trans_begin();
		//$this->db->where('id_rencana_studi', $id_rencana_studi)->delete('siakad_krs_detail');
		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';

		if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . $this->input->post('catatan_dosen_wali') . '</div></div></div>';
			} else {
				$catatan_dosen_wali = '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . $this->input->post('catatan_dosen_wali') . '</div></div></div>';
			}
		}

		if ($this->input->post('id_status_krs') == 3) {

			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
					'catatan_dosen_wali' => $catatan_dosen_wali,
					'approved_by' => $user->id, 'approved_date' => date('Y-m-d H:i:s')
				]
			);

			$krs_detail = $this->db->where(['id_rencana_studi' => $id_rencana_studi])->get('siakad_krs_detail')->result();

			foreach ($krs_detail as $key => $value) {
				$data_nilai = $this->db->where(['nipd' => $data_krs->nipd, 'id_kls' => $value->id_kls])->get('siakad_nilai_baru')->num_rows();

				if ($data_nilai == 0) {
					$this->db->insert('siakad_nilai_baru', [
						'nipd' => $data_krs->nipd,
						'id_kls' => $value->id_kls,
						'id_reg_pd' => $id_reg_pd,
						'createby' => $user->id,
						'createdate' => date('Y-m-d H:i:s')
					]);
				}
			}
		} else {
			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
					'catatan_dosen_wali' => $catatan_dosen_wali
				]
			);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$error = "Transaksi data ERROR, coba ulangi lagi.";
			$this->session->set_flashdata('error', $error);
			redirect($_SERVER["HTTP_REFERER"]);
		} else {
			$this->db->trans_commit();
			$error = "Transaksi data berhasil.";
			$this->session->set_flashdata('error', $error);
			redirect($_SERVER["HTTP_REFERER"]);
		}
	} */

	public function persetujuan_krs()
	{
		//$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$this->db->trans_start();
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();
		$id_reg_pd = $this->db->where(['nipd' => $data_krs->nipd])->get('mahasiswa_pt')->row()->nipd;

		//$this->db->trans_begin();
		//$this->db->where('id_rencana_studi', $id_rencana_studi)->delete('siakad_krs_detail');

		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';

		if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<br><b>' .   $user->first_name . '</b> : '  . $this->input->post('catatan_dosen_wali');
			} else {
				$catatan_dosen_wali = '<b>' .  $user->first_name . '</b> : ' . $this->input->post('catatan_dosen_wali');
			}
		}


		if ($this->input->post('id_status_krs') == 3) {

			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
					'catatan_dosen_wali' => $catatan_dosen_wali,
					'approved_by' => $user->id, 'approved_date' => date('Y-m-d H:i:s')
				]
			);

			$krs_detail = $this->db->where(['id_rencana_studi' => $id_rencana_studi])->get('siakad_krs_detail')->result();
			$data_nilai = [];

			foreach ($krs_detail as $key => $value) {
				$data_nilai = $this->db->where(['nipd' => $data_krs->nipd, 'id_kls' => $value->id_kls])->get('siakad_nilai_baru')->num_rows();

				if ($data_nilai == 0) {
					$this->db->insert('siakad_nilai_baru', [
						'nipd' => $data_krs->nipd,
						'id_kls' => $value->id_kls,
						'id_reg_pd' => $id_reg_pd,
						'createby' => $user->id,
						'createdate' => date('Y-m-d H:i:s')
					]);
					/* $data_nilai =  [
						'nipd' => $data_krs->nipd,
						'id_kls' => $value->id_kls,
						'id_reg_pd' => $id_reg_pd,
						'createby' => $user->id,
						'createdate' => date('Y-m-d H:i:s')
					]; */
				}
			}

			//$this->db->insert_batch('siakad_nilai_baru', $data_nilai);
		} else {
			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
					'catatan_dosen_wali' => $catatan_dosen_wali
				]
			);
		}

		/* if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$error = "Transaksi data ERROR, coba ulangi lagi.";
			//$this->session->set_flashdata('error', $error);
			//redirect($_SERVER["HTTP_REFERER"]);
		} else {
			$this->db->trans_commit();
			$error = "Transaksi data berhasil.";
			//$this->session->set_flashdata('error', $error);
			//redirect($_SERVER["HTTP_REFERER"]);
		} */
		$this->db->trans_complete();
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_perbaikan_krs($id_rencana_studi)
	{
		$data = [
			'id_status_krs' => 1,
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Lecturer_persetujuan_krs_model_2->update(['id_rencana_studi' => $id_rencana_studi], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function preview_pdf($id_rencana_studi)
	{
		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();
		$data_rencana_studi_detail = $this->db->where(compact('id_rencana_studi'))
			->select('mk.*, kk.nm_kls')->from('siakad_krs_detail kd')
			->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get()->result();
		$data_mahasiswa = $this->db->where('nipd', $data_rencana_studi->nipd)->select('*')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		if (!empty($data_mahasiswa->id_reg_pd_feeder)) {
			$data_kuliah_mahasiswa = $this->db->where('id_reg_pd', $data_mahasiswa->id_reg_pd_feeder)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
		} else {
			$data_kuliah_mahasiswa = $this->db->where('nipd', $data_mahasiswa->nipd)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
		}
		$id_sms = $data_mahasiswa->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;
		$id_smt = $data_rencana_studi->id_smt;
		$nm_semester = $this->db->where(compact('id_smt'))->get('semester')->row()->nm_smt;

		$m_dosen_wali = $data_mahasiswa->dosen_pa;
		$dosen_wali = $this->db->where(['id_ptk' => $m_dosen_wali])->get('siakad_dosen');

		$output['nm_sdm'] = !empty($dosen_wali->row()->nm_ptk) ? $dosen_wali->row()->nm_ptk : '';
		$output['tipe_file'] = 1;
		$output['data_rencana_studi'] = $data_rencana_studi;
		$output['data_rencana_studi_detail'] = $data_rencana_studi_detail;
		$output['data_mahasiswa'] = $data_mahasiswa;
		$output['data_kuliah_mahasiswa'] = $data_kuliah_mahasiswa;
		$output['nm_lemb'] = $nm_lemb;
		$output['nm_semester'] = $nm_semester;
		$this->load->view('pdf/pdf_pengajuan_krs', $output);
	}

	public function ajax_balas_pesan()
	{
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();

		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';

		/* if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			} else {
				$catatan_dosen_wali = '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			}
		} */

		if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<br><b>' . $user->first_name . '</b> : ' . $this->input->post('catatan_dosen_wali');
			} else {
				$catatan_dosen_wali = '<b>' . $user->first_name . '</b> : ' . $this->input->post('catatan_dosen_wali');
			}
		}

		$data = [
			'catatan_dosen_wali' => $catatan_dosen_wali
		];

		$this->Lecturer_persetujuan_krs_model_2->update(['id_rencana_studi' => $this->input->post('id_rencana_studi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function view_pesan($id_rencana_studi, $nipd, $id_smt)
	{
		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();

		$log_message = '';
		if (!empty($data_rencana_studi->catatan_dosen_wali)) {
			$log_message =  $data_rencana_studi->catatan_dosen_wali;
		}

		echo json_encode([
			'id_status_krs' => $data_rencana_studi->id_status_krs,
			'data_pesan' => $log_message
		]);
	}
}
