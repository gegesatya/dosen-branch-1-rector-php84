<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal_2 extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Jadwal_model', 'Jadwal_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2,15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$output['judul_besar'] = 'Jadwal Kuliah ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_jadwal';
		$output['parent_2'] = 'active open';
		$output['content'] = 'jadwal_2/jadwal_2_view';
		$output['after_page'] = 'jadwal_2/jadwal_2_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Jadwal_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);
			$detail = $this->db->where('id_jadwal', $rg->id)->get('siakad_jadwal_detail');
			if ($detail->num_rows() == 0) {
				$kk = '-';
			} else {
				$kk = '';
				foreach ($detail->result() as $key => $value) {
					$kk .= $value->id_jadwal_detail . ' [' . $value->nm_kls . '] ' . $value->nm_mk . '<br>';
				}
			}

			$reset_bap = '<a href="javascript:void(0)" 
							class="btn btn-sm btn-default green-stripe"
							onclick="ajax_reset_bap(\'' . $rg->id . '\')">
							Reset BAP</a>';

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nama_hari . '<br><small>' . $jam . '</small>';
			$row[] = $rg->nama_ruang;
			$row[] = $rg->tag_name;
			$row[] = $kk;
			$row[] = $rg->id.' '.$reset_bap;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Jadwal_model->count_all(),
			"recordsFiltered" => $this->Jadwal_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_skema_edit($id_kls)
	{
		$data = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
		$id_mk = $data->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;
		echo json_encode($data);
	}

	public function ajax_reset_bap($id_jadwal)
	{
		$this->db->where('id', $id_jadwal)->update('siakad_jadwal', ['is_generate_bap'=>0]);
		$this->db->where('id_jadwal', $id_jadwal)->delete('siakad_rencana_pertemuan');
		echo json_encode(["status" => TRUE]);
	}

	public function get_copy_data($id_kls)
	{
		//$id_kls = $this->session->userdata('id_kls_session');
		$data = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();


		if ($data->status_isi_komponen_nilai == 1) {
			$id_mk = $data->id_mk;
			$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
			$data->nm_mk = $nm_mk;
			//print_r($data);
			echo json_encode($data);
		} else {
			echo json_encode(['status' => FALSE, 'message' => 'Sumber data belum isi Skema Nilai.']);
		}
	}

	public function ajax_skema_update()
	{
		$id_kls = $this->security->xss_clean($this->input->post('id_kls'));

		$this->_validate_4($id_kls);
		$data = [
			'bobot_sikap' => $this->security->xss_clean((int)$this->input->post('bobot_sikap')),
			'bobot_pengetahuan' => $this->security->xss_clean((int)$this->input->post('bobot_pengetahuan')),
			'bobot_pengetahuan_quiz' => $this->security->xss_clean((int)$this->input->post('bobot_pengetahuan_quiz')),
			'bobot_pengetahuan_uts' => $this->security->xss_clean((int)$this->input->post('bobot_pengetahuan_uts')),
			'bobot_pengetahuan_uas' => $this->security->xss_clean((int)$this->input->post('bobot_pengetahuan_uas')),
			'bobot_pengetahuan_tugas_terstruktur' => $this->security->xss_clean((int)$this->input->post('bobot_pengetahuan_tugas_terstruktur')),
			'bobot_keterampilan' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan')),
			'bobot_keterampilan_unjuk_kinerja' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan_unjuk_kinerja')),
			'bobot_keterampilan_proyek' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan_proyek')),
			'bobot_keterampilan_portofolio' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan_portofolio')),
			'bobot_keterampilan_produk' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan_produk')),
			'bobot_keterampilan_presentasi' => $this->security->xss_clean((int)$this->input->post('bobot_keterampilan_presentasi')),
			'status_isi_komponen_nilai' => 1,
			'changeby' => $this->ion_auth->user()->row()->first_name,
			'changedate' => date('Y-m-d H:i:s'),
		];


		$this->db->trans_begin();

		$this->db->where('id_kls', $id_kls)->update('feeder_kelas_kuliah', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(["status" => FALSE]);
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function ajax_skema_copy_data()
	{
		$id_kls = $this->security->xss_clean($this->input->post('id_kls'));
		$id_kls_2 = $this->security->xss_clean($this->input->post('id_kls_2'));
		$kelas_kuliah = $this->db->where(['id_kls' => $id_kls])
			->get('feeder_kelas_kuliah')->row();

		$bobot_sikap = $kelas_kuliah->bobot_sikap;
		$bobot_pengetahuan = $kelas_kuliah->bobot_pengetahuan;
		$bobot_pengetahuan_quiz = $kelas_kuliah->bobot_pengetahuan_quiz;
		$bobot_pengetahuan_uts = $kelas_kuliah->bobot_pengetahuan_uts;
		$bobot_pengetahuan_uas = $kelas_kuliah->bobot_pengetahuan_uas;
		$bobot_pengetahuan_tugas_terstruktur = $kelas_kuliah->bobot_pengetahuan_tugas_terstruktur;
		$bobot_keterampilan = $kelas_kuliah->bobot_keterampilan;
		$bobot_keterampilan_unjuk_kinerja = $kelas_kuliah->bobot_keterampilan_unjuk_kinerja;
		$bobot_keterampilan_proyek = $kelas_kuliah->bobot_keterampilan_proyek;
		$bobot_keterampilan_portofolio = $kelas_kuliah->bobot_keterampilan_portofolio;
		$bobot_keterampilan_produk = $kelas_kuliah->bobot_keterampilan_produk;
		$bobot_keterampilan_presentasi = $kelas_kuliah->bobot_keterampilan_presentasi;


		//$this->_validate_4($id_kls);
		$data = [
			'bobot_sikap' => $bobot_sikap,
			'bobot_pengetahuan' => $bobot_pengetahuan,
			'bobot_pengetahuan_quiz' => $bobot_pengetahuan_quiz,
			'bobot_pengetahuan_uts' => $bobot_pengetahuan_uts,
			'bobot_pengetahuan_uas' => $bobot_pengetahuan_uas,
			'bobot_pengetahuan_tugas_terstruktur' => $bobot_pengetahuan_tugas_terstruktur,
			'bobot_keterampilan' => $bobot_keterampilan,
			'bobot_keterampilan_unjuk_kinerja' => $bobot_keterampilan_unjuk_kinerja,
			'bobot_keterampilan_proyek' => $bobot_keterampilan_proyek,
			'bobot_keterampilan_portofolio' => $bobot_keterampilan_portofolio,
			'bobot_keterampilan_produk' => $bobot_keterampilan_produk,
			'bobot_keterampilan_presentasi' => $bobot_keterampilan_presentasi,
			'status_isi_komponen_nilai' => 1,
			'changeby' => $this->ion_auth->user()->row()->first_name,
			'changedate' => date('Y-m-d H:i:s'),
		];

		$this->db->where('id_kls', $id_kls_2)->update('feeder_kelas_kuliah', $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate_4($id_kls)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$sikap = (int)$this->input->post('bobot_sikap');
		$pengetahuan = (int)$this->input->post('bobot_pengetahuan');
		$keterampilan = (int)$this->input->post('bobot_keterampilan');

		$jumlah_a = $sikap + $pengetahuan + $keterampilan;

		$pengetahuan_kuis = (int)$this->input->post('bobot_pengetahuan_quiz');
		$pengetahuan_uts = (int)$this->input->post('bobot_pengetahuan_uts');
		$pengetahuan_uas = (int)$this->input->post('bobot_pengetahuan_uas');
		$pengetahuan_tugas_terstruktur = (int)$this->input->post('bobot_pengetahuan_tugas_terstruktur');

		$jumlah_b = $pengetahuan_kuis + $pengetahuan_uts + $pengetahuan_uas + $pengetahuan_tugas_terstruktur;

		$keterampilan_unjuk_kinerja = (int)$this->input->post('bobot_keterampilan_unjuk_kinerja');
		$keterampilan_proyek = (int)$this->input->post('bobot_keterampilan_proyek');
		$keterampilan_portofolio = (int)$this->input->post('bobot_keterampilan_portofolio');
		$keterampilan_produk = (int)$this->input->post('bobot_keterampilan_produk');
		$keterampilan_presentasi = (int)$this->input->post('bobot_keterampilan_presentasi');

		$jumlah_c = $keterampilan_unjuk_kinerja + $keterampilan_proyek + $keterampilan_portofolio + $keterampilan_produk + $keterampilan_presentasi;

		/* if($pengetahuan_kuis < 10 || $pengetahuan_kuis > 20)
		{
			$data['inputerror'][] = 'quiz_error';
			$data['error_string'][] = 'Persen Quiz harus di antara 10 % (min) dan 20% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'quiz_error';
			$data['error_string'][] = '';
		}

		if($pengetahuan_tugas_terstruktur < 20 || $pengetahuan_tugas_terstruktur > 50)
		{
			$data['inputerror'][] = 'tugas_error';
			$data['error_string'][] = 'Persen Tugas Terstruktur harus di antara 20 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'tugas_error';
			$data['error_string'][] = '';
		}

		if($pengetahuan_uts < 20 || $pengetahuan_uts > 50)
		{
			$data['inputerror'][] = 'uts_error';
			$data['error_string'][] = 'Persen UTS harus di antara 20 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'uts_error';
			$data['error_string'][] = '';
		}

		if($pengetahuan_uas < 20 || $pengetahuan_uas > 50)
		{
			$data['inputerror'][] = 'uas_error';
			$data['error_string'][] = 'Persen UAS harus di antara 20 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'uas_error';
			$data['error_string'][] = '';
		}

		if($keterampilan_unjuk_kinerja < 20 || $keterampilan_unjuk_kinerja > 50)
		{
			$data['inputerror'][] = 'ukn_error';
			$data['error_string'][] = 'Persen Unjuk Kinerja harus di antara 20 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'ukn_error';
			$data['error_string'][] = '';
		}

		if($keterampilan_proyek < 0 || $keterampilan_proyek > 50)
		{
			$data['inputerror'][] = 'pry_error';
			$data['error_string'][] = 'Persen Proyek harus di antara 0 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'pry_error';
			$data['error_string'][] = '';
		}

		if($keterampilan_portofolio < 0 || $keterampilan_portofolio > 50)
		{
			$data['inputerror'][] = 'por_error';
			$data['error_string'][] = 'Persen Portofolio harus di antara 0 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'por_error';
			$data['error_string'][] = '';
		}

		if($keterampilan_produk < 0 || $keterampilan_produk > 50)
		{
			$data['inputerror'][] = 'prd_error';
			$data['error_string'][] = 'Persen Produk harus di antara 0 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'prd_error';
			$data['error_string'][] = '';
		}

		if($keterampilan_presentasi < 0 || $keterampilan_presentasi > 50)
		{
			$data['inputerror'][] = 'pre_error';
			$data['error_string'][] = 'Persen Presentasi harus di antara 0 % (min) dan 50% (max).';
			$data['status'] = FALSE;
		}else{
			$data['inputerror'][] = 'pre_error';
			$data['error_string'][] = '';
		} */


		if ($jumlah_a <> 100) {
			$data['inputerror'][] = 'induk_error';
			$data['error_string'][] = 'Jumlah SIKAP + PENGETAHUAN + KETERAMPILAN harus 100%.';
			$data['status'] = FALSE;
		} else {
			$data['inputerror'][] = 'induk_error';
			$data['error_string'][] = '';
		}

		if ($jumlah_b <> 100) {
			$data['inputerror'][] = 'sub_pengetahuan_error';
			$data['error_string'][] = 'Jumlah Quiz + UTS + UAS + TUGAS TERSTRUKTUR harus 100%.';
			$data['status'] = FALSE;
		} else {
			$data['inputerror'][] = 'sub_pengetahuan_error';
			$data['error_string'][] = '';
		}

		if ($jumlah_c <> 100) {
			$data['inputerror'][] = 'sub_keterampilan_error';
			$data['error_string'][] = 'Jumlah UNJUK KINERJA + PROYEK + PORTOFOLIO + PRODUK + PRESENTASI harus 100%.';
			$data['status'] = FALSE;
		} else {
			$data['inputerror'][] = 'sub_keterampilan_error';
			$data['error_string'][] = '';
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function ajax_edit($id)
	{
		$data = $this->Jadwal_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = [
			'kode_ruang' => $this->input->post('kode_ruang'),
			'nama_ruang' => $this->input->post('nama_ruang'),
			'lantai' => $this->input->post('lantai'),
			'kapasitas' => $this->input->post('kapasitas'),
			'create_by' => $this->ion_auth->user()->row()->id,
			'create_date' => date('Y-m-d H:i:s')
		];
		$insert = $this->Jadwal_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = [
			'kode_ruang' => $this->input->post('kode_ruang'),
			'nama_ruang' => $this->input->post('nama_ruang'),
			'lantai' => $this->input->post('lantai'),
			'kapasitas' => $this->input->post('kapasitas'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];
		$this->Jadwal_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->Jadwal_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}


	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('kode_ruang') == '') {
			$data['inputerror'][] = 'kode_ruang';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nama_ruang') == '') {
			$data['inputerror'][] = 'nama_ruang';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if (!is_numeric($this->input->post('kapasitas'))) {
			$data['inputerror'][] = 'kapasitas';
			$data['error_string'][] = 'Nilai bukan angka.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('kapasitas') < 0) {
			$data['inputerror'][] = 'kapasitas';
			$data['error_string'][] = 'Nilai tidak boleh negatif.';
			$data['status'] = FALSE;
		} else {
			$data['inputerror'][] = 'kapasitas';
			$data['error_string'][] = '';
		}



		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function convert_email_wbi()
	{
		$data_gmail = $this->db->get('data_gmail')->result();
		$hitung = 0;

		foreach ($data_gmail as $key => $value) {
			$mhs = $this->db->where('trim(nm_pd)', trim((string)$value->nama))->get('mahasiswa');
			if ($mhs->num_rows() > 0) {
				$this->db->where('trim(nm_pd)', trim((string)$value->nama))->update('mahasiswa', ['email_wbi' => $value->email_wbi]);
			}
		}
	}
}
