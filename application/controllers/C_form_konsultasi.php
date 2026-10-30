<?php
defined('BASEPATH') or exit('No direct script access allowed');

class c_form_konsultasi extends CI_Controller
{
	public $setting;

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['ion_auth', 'form_validation']);
		$this->load->model('registrasi_matakuliah_model/registrasi_matakuliah_model', 'registrasi_model');
		$this->load->model('registrasi_matakuliah_model/krs_model', 'krs_model');
		$this->load->model('nilai_model', 'nilai_model');
		$this->load->model('konsultasi_model', 'konsultasi_model');
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

	public function index()
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$nm_semester = $this->db->where('id_smt', $id_smt)->get('semester')->row();
		$output['halaman'] = 'Form Konsultasi Online ';
		$output['sub_halaman'] = 'Semester ' . $nm_semester->nm_smt;
		$output['content'] = 'form_konsultasi/form_konsultasi_view';
		$output['after_page'] = 'form_konsultasi/form_konsultasi_after_page';
		$output['tipe_konsultasi'] = ['KRS', 'Kegiatan Kemahasiswaan', 'Entrepreneurship', 'Magang', 'PKL', 'Tugas Akhir', 'Lainnya'];
		$output['menu_aktif'] = "m_konsultasi";
		$output['parent_1']			= 'active open';
		$this->session->set_userdata('address', $this->uri->segment(1));

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->registrasi_model->get_datatables();
		$id_smt = $this->session->userdata("tahun_ajaran");
		$tahun_ajaran_aktif = $this->db->get('siakad_setting')->row()->tahun_ajaran_aktif;

		$data = [];
		$no = $_POST['start'];

		if ($id_smt == $tahun_ajaran_aktif) {
			foreach ($list as $kk) {
				$no++;
				$row = [];
				$total_sks = $this->nilai_model->hitung_totalsks($kk->id_reg_pd);
				$total_nilai = $this->nilai_model->hitung_total_nilai($kk->id_reg_pd);
				$ipk = 0;
				if ($total_sks > 0) {
					$ipk = round($total_nilai / $total_sks, 2);
				}

				$data_konsul_terakhir = $this->db->where('id_reg_pd', $kk->id_reg_pd)->order_by('id', 'DESC')->limit(1)->get('form_konsultasi');

				if ($data_konsul_terakhir->num_rows() > 0) {
					$catatan_dosen = $data_konsul_terakhir->row()->catatan_dosen;
					$konsultasi_akademik = $data_konsul_terakhir->row()->konsultasi_akademik;
					$feedback_mahasiswa = $data_konsul_terakhir->row()->feedback_mahasiswa;
				} else {
					$catatan_dosen = '-';
					$konsultasi_akademik = '-';
					$feedback_mahasiswa = '-';
				}

				$row[] = $no;
				$row[] = '<a class=" waves-effect waves-block" href="javascript:void(0)" onclick="transkrip_mahasiswa(' . "'" . $kk->nipd . "'" . ')" ><i class="fa fa-database"></i> ' . $kk->nipd . '</a>' . '<br>' . $kk->nm_pd;
				$row[] = $ipk . ' / ' . $total_sks;
				$row[] = $catatan_dosen;
				$row[] = $konsultasi_akademik;
				$row[] = $feedback_mahasiswa;
				$row[] = '<a class="btn btn-sm btn-success btn-xs" href="javascript:void()" onclick="konsultasi(' . "'" . $kk->id_reg_pd . "'" . ')"><i class="glyphicon glyphicon-search"></i></a>';

				$data[] = $row;
			}

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->registrasi_model->count_all(),
				"recordsFiltered" => $this->registrasi_model->count_filtered(),
				"data" => $data,
			];


			echo json_encode($output);
		} else {
			$output = [
				"draw" => '',
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => '',
			];


			echo json_encode($output);
		}
	}

	public function ajax_konsultasi_list($id_reg_pd)
	{
		$list = $this->konsultasi_model->get_datatables($id_reg_pd);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $konsultasi) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = date('d M Y', strtotime($konsultasi->tanggal_konsultasi));
			$row[] = $konsultasi->tipe_konsultasi;
			$row[] = $konsultasi->catatan_dosen;
			$row[] = $konsultasi->konsultasi_akademik;
			$row[] = $konsultasi->feedback_mahasiswa;

			$row[] = '
			    <a class="btn btn-sm btn-danger btn-xs" href="javascript:void()" onclick="delete_konsultasi(' . "'" . $konsultasi->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i></a>
				';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->konsultasi_model->count_all($id_reg_pd),
			"recordsFiltered" => $this->konsultasi_model->count_filtered($id_reg_pd),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function get_mahasiswa_mengajukan_krs()
	{
		$id_smt = $this->session->userdata("tahun_ajaran");

		$this->db->select('m.nm_pd, mp.nipd, mp.id_reg_pd, mp.tgl_masuk_sp');
		$this->db->from('siakad_mahasiswa_additional sma');
		$this->db->join('mahasiswa m', 'sma.id_pd = m.id_pd');
		$this->db->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd');
		$this->db->where('dosen_pa', $this->session->userdata('id_ptk'));

		$list = $this->db->get()->result();
		$jumlah_mahasiswa_mengajukan_krs = 0;
		$jumlah_krs_selesai = 0;

		foreach ($list as $key => $value) {
			$pengajuan = $this->db->select('*')
				->from('siakad_validasi_krs v')
				->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
				->where(['v.id_reg_pd' => $value->id_reg_pd, 'sk.id_smt' => $this->setting->tahun_ajaran_aktif])
				->get()
				->num_rows();

			$fase = $this->db->where(['id_reg_pd' => $value->id_reg_pd, 'id_smt' => $id_smt])->get('siakad_registration_pool');
			if ($fase->num_rows() > 0) {
				if ($fase->row()->id_stage == 3) {
					$jumlah_krs_selesai += 1;
				}
			}

			$status_pengajuan = '';
			if ($pengajuan == 0) {
			} else {
				$jumlah_mahasiswa_mengajukan_krs += 1;
			}
		}

		$data = [
			'jumlah_mahasiswa_mengajukan_krs'	=> $jumlah_mahasiswa_mengajukan_krs,
			'jumlah_krs_selesai' 				=> $jumlah_krs_selesai
		];

		echo json_encode($data);
	}

	public function ajax_konsultasi_by_id($id_reg_pd)
	{
		$data = $this->konsultasi_model->get_by_id($id_reg_pd);

		echo json_encode($data);
	}

	public function ajax_konsultasi_delete($id)
	{

		$this->db->trans_begin();
		$this->db->where('id', $id)->delete('form_konsultasi');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function ajax_konsultasi_add()
	{
		$this->_validate();
		$this->db->trans_begin();
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_konsultasi')));
		$data = [
			'id_reg_pd'					=> $this->input->post('id_reg_pd'),
			'id_smt'					=> $id_smt = $this->session->userdata("tahun_ajaran"),
			'catatan_dosen'				=> $this->input->post('catatan_dosen'),
			'konsultasi_akademik'		=> $this->input->post('konsultasi_akademik'),
			'tipe_konsultasi'		=> $this->input->post('tipe_konsultasi'),
			'tanggal_konsultasi'	=> $tanggal,
			'create_by' => $this->session->userdata('nm_ptk'),
			'create_date' => date('Y-m-d h:i:s')
		];

		$data = $this->security->xss_clean($data);
		$this->db->insert('form_konsultasi', $data);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('tanggal_konsultasi') == '') {
			$data['inputerror'][] = 'tanggal_konsultasi';
			$data['error_string'][] = 'Tanggal Konsultasi harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('catatan_dosen') == '') {
			$data['inputerror'][] = 'catatan_dosen';
			$data['error_string'][] = 'Catatan Dosen harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('konsultasi_akademik') == '') {
			$data['inputerror'][] = 'konsultasi_akademik';
			$data['error_string'][] = 'Kategori Pertemuan harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('tipe_konsultasi') == '') {
			$data['inputerror'][] = 'tipe_konsultasi';
			$data['error_string'][] = 'Tipe Konsultasi harus diisi.';
			$data['status'] = FALSE;
		}

		/*
		if (!strlen(trim($this->input->post('berita_acara_rencana'))))
		{
			$data['inputerror'][] = 'berita_acara_rencana';
			$data['error_string'][] = 'Rencana Berita Acara harus diisi.';
			$data['status'] = FALSE;
		}
		*/


		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
