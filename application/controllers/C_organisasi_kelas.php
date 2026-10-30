<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_organisasi_kelas extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Organisasi_kelas_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function course($slug = "", $course_id = "")
	{
		$page_data['course_id'] = $course_id;
		$page_data['page_name'] = "course_page";
		$page_data['page_title'] = site_phrase('course');

		$this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
	}

	private function authorization($id_jadwal)
	{
		$id_logged_in_user = $this->ion_auth->user()->row();
		$id_ptk_user = $id_logged_in_user->id_ptk;

		$jadwal = $this->db->where('id', $id_jadwal)->get('siakad_jadwal')->row();

		if ($jadwal->id_sdm === $id_ptk_user) {
			return true;
		} else {
			return false;
		}
	}

	public function index($id_jadwal)
	{
		if (SELF::authorization($id_jadwal)) {
			$jadwal = $this->db->where('id', $id_jadwal)->from('siakad_jadwal j')->join('siakad_hari h', 'h.uniq = j.id_hari')->get()->row();
			$meet = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->result();
			$id_rps = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_new_rps_mk_prodi')->row()->id_rps;
			$rps = $this->db->where(compact('id_rps'))->get('siakad_new_rps_online')->row();
			$creator = $this->db->where('id_ptk', $rps->id_ptk)->get('siakad_dosen')->row();
			$kordinator = $this->db->where('id_sms', $jadwal->id_sms)->get('kaprodi')->row();
			$kaprodi = $this->db->where('id_ptk', $kordinator->id_ptk)->get('siakad_dosen')->row();
			$peserta_kelas = $this->db->where('id_jadwal', $id_jadwal)->select('n.nipd, m.nm_pd')->from('siakad_nilai_baru n')->join('mahasiswa_pt mp', 'mp.nipd = n.nipd')->join('mahasiswa m', 'm.id_pd = mp.id_pd')->get();

			$i_realisasi = $this->db->where(['id_jadwal' => $id_jadwal, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();
			$i_pertemuan = $this->db->where(compact('id_jadwal'))->get('siakad_rencana_pertemuan')->num_rows();
			$jumlah_mahasiswa = $this->db->where(compact('id_jadwal'))->get('siakad_nilai_baru')->num_rows();

			$output['judul_besar'] = $jadwal->tag_name;
			$output['judul_kecil'] = $jadwal->nama_hari . ', ' . $jadwal->jam_mulai . ' sd ' . $jadwal->jam_selesai;
			$output['meet'] = $meet;
			$output['content'] = 'organisasi_kelas/organisasi_kelas_view';
			$output['after_page'] = 'organisasi_kelas/organisasi_kelas_after_page';
			$output['judul'] = 'Organisasi Kelas ' . $jadwal->tag_name;
			$output['i_realisasi'] = $i_realisasi;
			$output['i_pertemuan'] = $i_pertemuan;
			$output['jumlah_mahasiswa'] = $jumlah_mahasiswa;
			$output['creator'] = $creator;
			$output['kaprodi'] = $kaprodi;
			$output['rps'] = $rps;
			$output['peserta_kelas'] = $peserta_kelas;
			$output['is_generate_bap'] = $jadwal->is_generate_bap;


			if ($jadwal->is_skema) {
				$output['skema'] = 'Sudah isi';
			} else {
				$output['skema'] = 'Belum isi';
			}

			$output['id_jadwal'] = $id_jadwal;

			$this->load->view('master_page_elite', $output);
		} else {
			echo "Anda tidak bisa mengakses kelas ini.";
		}
	}


	public function get_keterangan()
	{
		header('Content-Type: application/json');
		$arr[] = ["id" => "1", "keterangan" => "Komting"];
		$arr[] = ["id" => "2", "keterangan" => "Wakil Komting 1"];
		$arr[] = ["id" => "3", "keterangan" => "Wakil Komting 2"];
		echo json_encode($arr);
	}

	public function ajax_list($id_kls = NULL)
	{
		// $id_kls = $this->session->userdata('id_kls_session');
		$list = $this->Organisasi_kelas_model->get_datatables($id_kls);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {

			$no++;
			$row = [];
			$row[] = $l->nipd;
			$row[] = $l->nm_pd;
			$row[] = $l->keterangan;

			$row[] = '<a class="btn btn-primary btn-xs" 
						 href="javascript:void(0)" 
						 onclick="edit_organisasi(' . "'" . $l->id_organisasi . "'" . ')">
						 <i class="icon-pencil"></i>
					</a>
					<a class="btn btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_organisasi(' . "'" . $l->id_organisasi . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-times"></i>
					  </a>';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Organisasi_kelas_model->count_all($id_kls),
			"recordsFiltered" => $this->Organisasi_kelas_model->count_filtered($id_kls),
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function ajax_edit($id)
	{
		$data = $this->Organisasi_kelas_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate('add');
		$id_jadwal			= $this->input->post('id_jadwal');

		$nipd 			= $this->input->post('nipd');
		$nm_pd 			= $this->input->post('nm_pd');
		$keterangan 	= $this->input->post('keterangan');
		$create_by 		= $this->ion_auth->user()->row()->id;
		$create_date 	= date('Y-m-d h:i:s');

		$data = compact(
			'id_jadwal',
			'nipd',
			'nm_pd',
			'keterangan',
			'create_by',
			'create_date'
		);

		$insert = $this->Organisasi_kelas_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate('edit');

		$nipd 			= $this->input->post('nipd');
		$nm_pd 			= $this->input->post('nm_pd');
		$keterangan 		= $this->input->post('keterangan');
		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');

		$data = compact(
			'nipd',
			'nm_pd',
			'keterangan',
			'change_by',
			'change_date'
		);

		$data = $this->security->xss_clean($data);

		$this->Organisasi_kelas_model->update(['id_organisasi' => $this->input->post('id_organisasi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->Organisasi_kelas_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function get_mahasiswa($id_jadwal)
	{
		$data = $this->Organisasi_kelas_model->get_mahasiswa_by_nim($id_jadwal);
		echo json_encode($data);
	}

	private function cek($id_jadwal, $keterangan)
	{
		$c = $this->db->where(['id_jadwal' => $id_jadwal, 'keterangan' => $keterangan])->get('siakad_organisasi_kelas');
		return $c->num_rows();
	}

	private function cek_nim($id_jadwal, $nipd)
	{
		$c = $this->db->where(['id_jadwal' => $id_jadwal, 'nipd' => $nipd])->get('siakad_organisasi_kelas');
		return $c->num_rows();
	}

	private function _validate($tipe)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($tipe == 'add') {
			$cekout = $this->cek($this->input->post('id_jadwal'), $this->input->post('keterangan'));
			if ($cekout == 1) {
				$data['inputerror'][] = 'keterangan';
				$data['error_string'][] = 'Jabatan ' . $this->input->post('keterangan') . ' Sudah Ada.';
				$data['status'] = FALSE;
			}

			$cekout_nim = $this->cek_nim($this->input->post('id_jadwal'), $this->input->post('nipd'));
			if ($cekout_nim == 1) {
				$data['inputerror'][] = 'nipd';
				$data['error_string'][] = 'NIM ' . $this->input->post('nipd') . ' Sudah Terdaftar.';
				$data['status'] = FALSE;
			}
		}

		if ($this->input->post('nipd') == '') {
			$data['inputerror'][] = 'nipd';
			$data['error_string'][] = 'NIM harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nm_pd') == '') {
			$data['inputerror'][] = 'nm_pd';
			$data['error_string'][] = 'Nama harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('keterangan') == '') {
			$data['inputerror'][] = 'keterangan';
			$data['error_string'][] = 'Keterangan Organisasi Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
