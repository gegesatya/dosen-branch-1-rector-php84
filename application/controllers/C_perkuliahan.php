<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_perkuliahan extends CI_Controller
{
	public $setting;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->model('registrasi_matakuliah_model/registrasi_matakuliah_model', 'registrasi_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}


		$this->setting = $this->db->get('siakad_setting')->row();
	}

	public function index_perkuliahan($id_kls)
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$nm_semester = $this->db->where('id_smt', $id_smt)->get('semester')->row();

		$output['content'] = 'perkuliahan/perkuliahan_view';
		$output['after_page'] = 'perkuliahan/perkuliahan_after_page';
		$output['m_kelas'] = $id_kls;
		$output['menu_aktif'] = $id_kls;
		$output['parent_2']			= 'active open';
		$this->session->set_userdata('address', $this->uri->segment(1));
		$this->property_perkuliahan($id_kls);

		/*Rencana pertemuan tab*/
		$output['absen_master'] = $this->db->where('id_kls', $id_kls)->get('siakad_absen_master');
		$setting = $this->db->get('siakad_setting')->row();
		$tanggal_close_rps = $setting->tanggal_close_rps;

		$output['tanggal_close'] = $tanggal_close_rps;

		$this->load->view('master_page', $output);
	}

	private function property_perkuliahan($id_kls)
	{
		$kelas = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah');

		if ($kelas->num_rows() > 0) {
			$id_mk = $kelas->row()->id_mk;
			$nm_kls = $kelas->row()->nm_kls;
			$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
			$this->session->set_userdata(
				[
					'id_kls'		=> $id_kls,
					'id_mk'			=> $id_mk,
					'nm_mk'			=> $nm_mk,
					'nm_kls'		=> $nm_kls
				]
			);
		}
	}

	public function ajax_skema_edit($id_kls)
	{
		$data = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
		$id_mk = $data->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;
		echo json_encode($data);
	}

	public function ajax_update_skema_nilai()
	{
		$id_kls = $this->session->userdata('id_kls');

		$this->_validate_4();
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


		$data = $this->security->xss_clean($data);
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

	private function _validate_4()
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

	/* RENCANA PERTEMUAN */
	public function index_rencana_pertemuan()
	{
		$id_kls = $this->session->userdata('id_kls');

		$kelas = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah');

		if ($kelas->num_rows() > 0) {
			$id_mk = $kelas->row()->id_mk;
			$nm_kls = $kelas->row()->nm_kls;
			$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
			$this->session->set_userdata(
				[
					'id_kls'		=> $id_kls,
					'id_mk'			=> $id_mk,
					'title_nm_mk'	=> $nm_mk,
					'bc_nm_kls'		=>  $nm_kls,
				]
			);
		}


		$output['halaman'] = "RENCANA PERTEMUAN";
		$output['sub_halaman'] = " Tahun Ajaran " . $this->session->userdata('nm_smt');
		$output['menu_aktif'] = "m_rencana";
		$output['m_kelas'] = !empty($id_kls) ? $id_kls : '';
		$output['v_tahun'] = $this->db->where('a_periode_aktif', 1)->get('semester')->result();

		$output['output'] = 'rencana_pertemuan/rencana_pertemuan_view';
		$output['after_page'] = 'rencana_pertemuan/rencana_pertemuan_after_page';

		$output['absen_master'] = $this->db->where('id_kls', $id_kls)->get('siakad_absen_master');

		$setting = $this->db->get('siakad_setting')->row();
		$tanggal_close_rps = $setting->tanggal_close_rps;

		$output['tanggal_close'] = $tanggal_close_rps;



		//$this->log_library->log_history('akses_menu', $id_kls->id_kls);
		$this->session->set_userdata('address', $this->uri->segment(1));
		$this->load->view('master_page', $output);
	}
}
