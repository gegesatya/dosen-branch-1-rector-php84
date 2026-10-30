<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_belum_krs extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lecturer_belum_krs_model', 'Lecturer_belum_krs_model');
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
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;

		// Check KRS Period (siakad_setting)
		$setting = $this->db->get('siakad_setting')->row();
		$is_krs_period = ($setting && !empty($setting->tgl_akhir_krs) && date('Y-m-d H:i:s') <= $setting->tgl_akhir_krs);

		$output['is_krs_period'] = $is_krs_period;
		$output['tgl_akhir_krs'] = ($setting) ? $setting->tgl_akhir_krs : null;

		$output['judul_besar'] = 'Mahasiswa Belum KRS';
		$output['judul_kecil'] = '';
		$output['menu_aktif'] = 'm_belum_krs';
		$output['parent_1'] = 'active open';
		$output['content'] = 'belum_krs/belum_krs_view';
		$output['after_page'] = 'belum_krs/belum_krs_after_page';
		$output['dd_status_krs'] 	= $this->Lecturer_belum_krs_model->f_status_krs();

		$this->load->view('master_page', $output);
	}

	public function ajax_list($angkatan)
	{
		$list = $this->Lecturer_belum_krs_model->get_datatables($angkatan);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;

			// 1. Photo with Modal Trigger
			if ($rg->foto) {
				$photo = '<a href="javascript:void(0)" onclick="view_foto(\''.$rg->nipd.'\'); return false;">
							<img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_'.$rg->foto.'" width="40" height="50" style="object-fit:cover; border-radius:4px; border:1px solid #ddd">
						  </a>';
			} else {
				$photo = '<a href="javascript:void(0)" onclick="view_foto(\''.$rg->nipd.'\'); return false;">
							<img src="'.base_url('assets/images.png').'" width="40" height="50" style="object-fit:cover; opacity:0.5">
						  </a>';
			}

			// 2. KIP Status
			$kip = ($rg->a_penerima_kip == 1) ? '<div style="margin-top: 6px; font-size: 11px;">Status Pembayaran : <span class="label label-primary">KIP</span></div>' : '<div style="margin-top: 6px; font-size: 11px;">Status Pembayaran : <span class="label label-info">Reguler</span></div>';

			// 3. Masa Studi from Query
			$masa_studi = $rg->thn_studi . " Thn " . $rg->bln_studi . " Bln";

			$row = [];
			$row[] = $no;
			$row[] = $photo;
			$row[] = '<b>'.$rg->nipd.'</b>';
			$row[] = $rg->nm_pd . $kip;
			$row[] = $masa_studi;
			$row[] = '<span class="text-primary fw-bold">' . ($rg->sks_total ?? 0) . '</span>';
			$row[] = '<span class="text-success fw-bold">' . number_format($rg->ipk ?? 0, 2) . '</span>';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lecturer_belum_krs_model->count_all($angkatan),
			"recordsFiltered" => $this->Lecturer_belum_krs_model->count_filtered($angkatan),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function view_krs($id_rencana_studi, $nipd, $id_smt)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();

		/* $krs = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap')
				->from('siakad_krs sn')
				->join('siakad_krs_detail kd', 'sn.id_rencana_studi = kd.id_rencana_studi')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = kd.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where(['sn.nipd'=> $nipd, 'sn.id_rencana_studi'=> $id_rencana_studi])->get(); */

		$krs = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap')
			->from('siakad_nilai_baru sn')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where([
				'sn.nipd' => $nipd,
				'kk.id_smt' => $id_smt
			])
			->order_by('nm_mk')
			->get();

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
			'Kode/<br>Code',
			'Mata Kuliah/Subjects',
			'SKS/<br>Credit', 'T', 'P', 'PL'
		]);


		$i = 1;

		$sks_mk = $sks_tm = $sks_prak = $sks_prak_lap = 0;

		foreach ($krs->result() as $l_nilai) {
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
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
			['data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 3],
			['data' => $sks_mk, 'class' => 'text-center'],
			['data' => $sks_tm, 'class' => 'text-center'],
			['data' => $sks_prak, 'class' => 'text-center'],
			['data' => $sks_prak_lap, 'class' => 'text-center']
		]);

		$nilai_tabel =  $this->table->generate();

		$output['mahasiswa'] = $mahasiswa;

		$output['tabel'] = $nilai_tabel;
		//if($tipe=="cetak"){
		//	$this->load->view('cetak/cetak_krs', $output);
		//}else{
		$nm_pd = $mahasiswa->nm_pd;
		$nipd = $mahasiswa->nipd;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		echo json_encode(['tabel' => $nilai_tabel, 'nm_pd' => $nm_pd, 'nipd' => $nipd, 'id_smt' => $id_smt]);
		//}

	}

	public function ajax_edit($id)
	{
		$data = $this->Lecturer_belum_krs_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_get_krs($id)
	{
		/*$data = $this->db->select('krs.*, d.nm_sdm, p.nm_paket_krs, sum(sks_mk) as total_sks', FALSE)->set('cek_val = "TRUE"')->from('siakad_krs krs')
				->join('siakad_dosen_wali dw', 'dw.siakad_user_id = krs.id_dosen_siakad')
				->join('dosen d', 'd.id_sdm = dw.id_sdm')
				->join('siakad_paket_krs p', 'p.id = krs.id_paket_krs')
				->join('siakad_paket_krs_detail pd', 'p.id = pd.id_paket')
				->where('id_rencana_studi', $id)->get()->row();*/

		$data = $this->db->select('krs.*, d.nm_ptk, sum(kk.sks_mk) as total_sks', FALSE)->set('cek_val = "TRUE"')->from('siakad_krs krs')
			//->join('siakad_dosen_wali dw', 'dw.siakad_user_id = krs.id_dosen_siakad')
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
		//$nm_paket_krs = $data->nm_paket_krs;
		$total_sks = $data->total_sks;
		$registrasi_alamat = $data->registrasi_alamat;
		$registrasi_no_hp = $data->registrasi_no_hp;
		$registrasi_no_hp_ortu = $data->registrasi_no_hp_ortu;
		$registrasi_nominal_spp = $data->registrasi_nominal_spp;
		$total_sks = $data->total_sks;
		$img_slip_spp = $data->img_slip_spp;
		//$img_khs = $data->img_khs;

		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		/* if($img_khs == '')
		{
			$data['inputerror'][] = 'img_khs';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} */

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

	public function persetujuan_krs()
	{
		//$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();
		$id_reg_pd = $this->db->where(['nipd' => $data_krs->nipd])->get('mahasiswa_pt')->row()->nipd;
		$id_paket_krs = $data_krs->id_paket_krs;

		$this->db->trans_begin();
		//$this->db->where('id_rencana_studi', $id_rencana_studi)->delete('siakad_krs_detail');

		if ($this->input->post('id_status_krs') == 3) {
			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
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
					'id_status_krs' => $this->input->post('id_status_krs')
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
	}

	public function preview_pdf($id_rencana_studi)
	{
		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();
		$data_rencana_studi_detail = $this->db->where(compact('id_rencana_studi'))
			->select('mk.*')->from('siakad_krs_detail kd')
			->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get()->result();
		$data_mahasiswa = $this->db->where('nipd', $data_rencana_studi->nipd)->select('*')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$data_kuliah_mahasiswa = $this->db->where('id_reg_pd', $data_mahasiswa->id_reg_pd_feeder)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
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

		/*$html=$this->load->view('pdf/pdf_pengajuan_krs', $output,true);	
	    $pdfFilePath  = 'Pengajuan KRS Mahasiswa.pdf';
		$this->load->library('m_pdf_portrait');

		$this->m_pdf_portrait->pdf->WriteHTML($html);
		$this->m_pdf_portrait->pdf->Output($pdfFilePath, "I");*/
	}
	public function ajax_view_foto($nipd)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, foto, m.telepon_seluler')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();

		echo json_encode($mahasiswa);
	}
}
