<?php
defined('BASEPATH') or exit('No direct script access allowed');

class c_bimbingan_krs_dosen extends CI_Controller
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
		$output['halaman'] = 'Bimbingan KRS ';
		$output['sub_halaman'] = 'Semester ' . $nm_semester->nm_smt;
		$output['content'] = 'bimbingan_krs_dosen/bimbingan_krs_dosen_view';
		$output['after_page'] = 'bimbingan_krs_dosen/bimbingan_krs_dosen_after_page';
		$output['menu_aktif'] 	= 'm_bimbingan';
		$output['parent_1']			= 'active open';
		$this->session->set_userdata('address', $this->uri->segment(1));
		$this->load->view('master_page', $output);
	}

	public function get_validasi_krs($id_reg_pd)
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$nm_semester = $this->db->where('id_smt', $this->setting->tahun_ajaran_future)->get('semester')->row();
		$output['halaman'] = 'Pengajuan Rencana Studi ';
		$output['sub_halaman'] = 'Semester ' . $nm_semester->nm_smt;

		$output['content'] = 'registrasi_matakuliah/registrasi_view';

		$mahasiswa = $this->db->select('nm_pd,nipd,tgl_masuk_sp,mp.id_reg_pd')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->where('mp.id_reg_pd', $id_reg_pd)->get()->row();

		$output['id_smt'] = $id_smt;
		$output['id_reg_pd'] = $id_reg_pd;
		$output['mahasiswa'] = !empty($mahasiswa) ? $mahasiswa : '';
		$output['jumlah_sks_pengajuan'] = $this->krs_model->count_sks_pengajuan($id_reg_pd);

		$fase = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $id_smt])->get('siakad_registration_pool')->row()->id_stage;
		$output['fase'] = !empty($fase) ? $fase : '';

		$respon = $this->load->view('registrasi_matakuliah/registrasi_view', $output, TRUE);
		echo $respon;
	}


	public function validasi_ajax_list($id_reg_pd)
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$list = $this->krs_model->get_datatables($id_reg_pd);
		$data = [];
		$no = $_POST['start'];

		$fase = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $id_smt])->get('siakad_registration_pool')->row()->id_stage;
		if ($fase == 3) {
			foreach ($list as $kk) {
				$registration_date = !empty($kk->registration_date) ? date('d-m-Y', strtotime($kk->registration_date)) : '';
				$registration_time = !empty($kk->registration_date) ? date('H:i', strtotime($kk->registration_date)) : '';
				$validation_date = !empty($kk->validation_date) ? date('d-m-Y', strtotime($kk->validation_date)) : '';
				$validation_time = !empty($kk->validation_date) ? date('H:i', strtotime($kk->validation_date)) : '';

				$no++;
				$row = [];

				$hr = $this->db->where('uniq', $kk->hari)->get('siakad_hari');
				$hari = $hr->num_rows() > 0 ? $hr->row()->nama_hari : '';
				$jam = substr($kk->jam_mulai, 0, 5) . ' s/d ' . substr($kk->jam_selesai, 0, 5);
				$row[] = $no;
				$row[] = $kk->kode_mk . '<br/>' . $kk->nm_mk;
				$row[] = $kk->nm_kls . '<br/>' . $kk->nm_ptk;
				$row[] = $kk->sks_mk . ' SKS';
				$row[] = $hari . '<br/>' . $jam;
				$row[] = $kk->validation_status;
				$row[] = $registration_date . '<br>' . $registration_time;
				$row[] = $validation_date . '<br>' . $validation_time;

				$data[] = $row;
			}
		} else {
			foreach ($list as $kk) {
				$registration_date = !empty($kk->registration_date) ? date('d-m-Y', strtotime($kk->registration_date)) : '';
				$registration_time = !empty($kk->registration_date) ? date('H:i', strtotime($kk->registration_date)) : '';
				$validation_date = !empty($kk->validation_date) ? date('d-m-Y', strtotime($kk->validation_date)) : '';
				$validation_time = !empty($kk->validation_date) ? date('H:i', strtotime($kk->validation_date)) : '';

				$no++;
				$row = [];
				$hr = $this->db->where('uniq', $kk->hari)->get('siakad_hari');
				$hari = $hr->num_rows() > 0 ? $hr->row()->nama_hari : '';
				$jam = substr($kk->jam_mulai, 0, 5) . ' s/d ' . substr($kk->jam_selesai, 0, 5);
				$row[] = $no;
				$row[] = $kk->kode_mk . '<br/>' . $kk->nm_mk;
				$row[] = $kk->nm_kls . '<br/>' . $kk->nm_ptk;
				$row[] = $kk->sks_mk . ' SKS';
				$row[] = $hari . '<br/>' . $jam;
				$row[] = $kk->validation_status;
				$row[] = $registration_date . '<br>' . $registration_time;
				$row[] = $validation_date . '<br>' . $validation_time;

				$row[] = '<a class="btn bg-teal waves-effect" href="javascript:void()" onclick="approve_krs(' . "'" . $id_reg_pd . "'" . ', ' . "'" . $kk->id_kls . "'" . ')"><i class="material-icons">done</i></a>
						  <a class="btn bg-pink waves-effect" href="javascript:void()" onclick="not_approve_krs(' . "'" . $id_reg_pd . "'" . ', ' . "'" . $kk->id_kls . "'" . ')"><i class="material-icons">clear</i></a>';

				$data[] = $row;
			}
		}


		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->registrasi_model->count_all(),
			"recordsFiltered" => $this->registrasi_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_list()
	{
		$list = $this->registrasi_model->get_datatables();
		$id_smt = $this->session->userdata("tahun_ajaran");
		$tahun_ajaran_aktif = $this->db->get('siakad_setting')->row()->tahun_ajaran_aktif;

		$setting = $this->db->get('siakad_setting')->row();
		$tanggal_close_krs = $setting->tanggal_close_krs;

		$data = [];
		$no = $_POST['start'];

		if ($id_smt == $tahun_ajaran_aktif) {

			if (strtotime(date('Y-m-d H:i:s')) <= strtotime($tanggal_close_krs)) {
				foreach ($list as $kk) {
					$no++;
					$row = [];
					$total_sks = $this->nilai_model->hitung_totalsks($kk->id_reg_pd);
					$total_nilai = $this->nilai_model->hitung_total_nilai($kk->id_reg_pd);
					$ipk = 0;
					if ($total_sks > 0) {
						$ipk = round($total_nilai / $total_sks, 2);
					}

					$status_pengajuan_krk = '';

					$pengajuan = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where(['v.id_reg_pd' => $kk->id_reg_pd, 'sk.id_smt' => $id_smt])
						->get()
						->num_rows();


					$disetujui = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where([
							'v.id_reg_pd' => $kk->id_reg_pd,
							'sk.id_smt' => $this->setting->tahun_ajaran_aktif,
							'validation_status' => 'Disetujui'
						])
						->get()
						->num_rows();

					$status_pengajuan = '';
					if ($pengajuan == 0) {
						$status_pengajuan = "-";
					} else {
						$status_pengajuan = "Mata Kuliah diajukan : " . $pengajuan . ', disetujui : ' . $disetujui;
					}


					$tahun_masuk = date('Y', strtotime($kk->tgl_masuk_sp));
					$fase = $this->db->where(['id_reg_pd' => $kk->id_reg_pd, 'id_smt' => $id_smt])->get('siakad_registration_pool');
					$status_fase = '';
					$html = '';
					if ($fase->num_rows() > 0) {
						if ($fase->row()->id_stage == 3) {
							$status_fase = "<span><i class='glyphicon glyphicon-ok'></i> Final</span>";
						} elseif ($fase->row()->id_stage == 2) {
							if ($fase->row()->is_perbaikan == 1) {
								$status_fase = "<span><i class='glyphicon glyphicon-hourglass'></i> Perbaikan KRS.</span>";
							} else {
								$status_fase = "";
							}
						}
					}

					$jumlah_pengajuan = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where(['v.id_reg_pd' => $kk->id_reg_pd, 'sk.id_smt' => $id_smt])
						->get()
						->num_rows();

					$row[] = $no;
					$row[] = $tahun_masuk;
					$row[] = '<a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="transkrip_mahasiswa(' . "'" . $kk->id_reg_pd . "'" . ')" ><i class="fa fa-database"></i> ' . $kk->nipd . '</a>';
					$row[] = $kk->nm_pd;
					$row[] = $ipk . ' / ' . $total_sks;

					if ($jumlah_pengajuan > 0) {
						if ($fase->num_rows() > 0) {
							if ($fase->row()->id_stage == 3) {
								$row[] = $status_pengajuan;
								$row[] = $status_fase;
								$row[] = '<a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="bimbingan_krs(' . "'" . $kk->id_reg_pd . "'" . ')" > 
											<i class="fa fa-check-square-o"></i> Lihat KRS</a>';
								$row[] = ' <a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="perbaikan_krs(\'' . $kk->id_reg_pd . '\')"><i class="fa fa-edit"></i> Perbaikan KRS</a>';
							} elseif ($fase->row()->id_stage == 2) {
								$row[] = $status_pengajuan;
								$row[] = $status_fase;
								$row[] = '<a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="bimbingan_krs(' . "'" . $kk->id_reg_pd . "'" . ')" > 
												        	<i class="fa fa-check-square-o"></i> Persetujuan KRS</a>';
								$row[] = '-';
							}
						}
					} else {
						$row[] = "Belum melakukan pendaftaran KRS.";
						$row[] = "-";
						$row[] = "-";
						$row[] = "-";
					}

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
				foreach ($list as $kk) {
					$no++;
					$row = [];
					$total_sks = $this->nilai_model->hitung_totalsks($kk->id_reg_pd);
					$total_nilai = $this->nilai_model->hitung_total_nilai($kk->id_reg_pd);
					$ipk = 0;
					if ($total_sks > 0) {
						$ipk = round($total_nilai / $total_sks, 2);
					}

					$status_pengajuan_krk = '';

					$pengajuan = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where(['v.id_reg_pd' => $kk->id_reg_pd, 'sk.id_smt' => $id_smt])
						->get()
						->num_rows();

					$disetujui = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where([
							'v.id_reg_pd' => $kk->id_reg_pd,
							'sk.id_smt' => $this->setting->tahun_ajaran_aktif,
							'validation_status' => 'Disetujui'
						])
						->get()
						->num_rows();

					$status_pengajuan = '';
					if ($pengajuan == 0) {
						$status_pengajuan = "-";
					} else {
						$status_pengajuan = "MK Diajukan : " . $pengajuan . ', disetujui : ' . $disetujui;
					}


					$tahun_masuk = date('Y', strtotime($kk->tgl_masuk_sp));
					$fase = $this->db->where(['id_reg_pd' => $kk->id_reg_pd, 'id_smt' => $id_smt])->get('siakad_registration_pool');
					$status_fase = '';
					$html = '';
					if ($fase->num_rows() > 0) {
						if ($fase->row()->id_stage == 3) {
							$status_fase = "<span><i class='glyphicon glyphicon-ok'></i> Final</span>";
						} elseif ($fase->row()->id_stage == 2) {
							if ($fase->row()->is_perbaikan == 1) {
								$status_fase = "<span><i class='glyphicon glyphicon-hourglass'></i> Perbaikan KRS.</span>";
							} else {
								$status_fase = "";
							}
						}
					}

					$jumlah_pengajuan = $this->db->select('*')
						->from('siakad_validasi_krs v')
						->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
						->where(['v.id_reg_pd' => $kk->id_reg_pd, 'sk.id_smt' => $id_smt])
						->get()
						->num_rows();

					$row[] = $no;
					$row[] = $tahun_masuk;
					$row[] = '<a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="transkrip_mahasiswa(' . "'" . $kk->id_reg_pd . "'" . ')" ><i class="fa fa-database "></i> ' . $kk->nipd . '</a>';
					$row[] = $kk->nm_pd;
					$row[] = $ipk . ' / ' . $total_sks;

					if ($jumlah_pengajuan > 0) {
						if ($fase->num_rows() > 0) {
							if ($fase->row()->id_stage == 3) {
								$row[] = $status_pengajuan;
								$row[] = $status_fase;
								$row[] = '<a class="btn default green-stripe btn-xs" href="javascript:void(0)" onclick="bimbingan_krs(' . "'" . $kk->id_reg_pd . "'" . ')" > 
											<i class="fa fa-check-square-o"></i> Lihat KRS</a>';
								$row[] = '-';
							} elseif ($fase->row()->id_stage == 2) {
								$row[] = $status_pengajuan;
								$row[] = $status_fase;
								$row[] = 'KRS Pending';
								$row[] = '-';
							}
						} else {
							$row[] = 'Tidak Mengajukan';
							$row[] = '-';
							$row[] = '-';
							$row[] = '-';
						}
					} else {
						$row[] = "Belum melakukan pendaftaran KRS.";
						$row[] = '-';
						$row[] = '-';
						$row[] = '-';
					}





					$data[] = $row;
				}

				$output = [
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->registrasi_model->count_all(),
					"recordsFiltered" => $this->registrasi_model->count_filtered(),
					"data" => $data,
				];


				echo json_encode($output);
			}
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
			$row[] = $konsultasi->problem;
			$row[] = $konsultasi->saran;

			/*<a class="btn btn-xs btn-default btn-xs" href="javascript:void()" onclick="show_edit_tab('."'".$student->id_pd."'".')"><i class="glyphicon glyphicon-pencil"></i></a>*/
			$row[] = '
			    <a class="btn btn-xs btn-danger btn-xs" href="javascript:void()" onclick="delete_konsultasi(' . "'" . $konsultasi->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i></a>
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

		$this->db->select('*');
		$this->db->from('mahasiswa m');
		$this->db->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd');
		$this->db->where('m.dosen_pa', $this->session->userdata('id_ptk'));

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
		//$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility

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
		//$this->_validate();
		$this->db->trans_begin();
		$tanggal = date('Y-m-d', strtotime($this->input->post('tanggal_konsultasi')));
		$data = [
			'id_reg_pd'					=> $this->input->post('id_reg_pd'),
			'problem'				=> $this->input->post('problem'),
			'saran'					=> $this->input->post('saran'),
			'tanggal_konsultasi'	=> $tanggal,
			'create_by' => $this->ion_auth->user()->row()->id,
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

	public function ajax_approve($id_reg_pd, $id_kls)
	{

		$this->db->trans_begin();
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->update(
			'siakad_validasi_krs',
			[
				'validation_status' => 'Disetujui', 'validation_date' => date('Y-m-d H:i:s'),
				'changeby' => $this->ion_auth->user()->row()->first_name,
				'changedate' => date('Y-m-d H:i:s'),
			]
		);
		$data_siakad_nilai = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->get('feeder_nilai')->num_rows();
		$data_evaluasi_dosen = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->get('siakad_evaluasi_dosen')->num_rows();

		if ($data_siakad_nilai == 0) {
			$this->db->insert(
				'feeder_nilai',
				[
					'id_reg_pd' => $id_reg_pd,
					'id_kls' => $id_kls,
					'createby' => $this->ion_auth->user()->row()->first_name,
					'createdate' => date('Y-m-d H:i:s'),
				]
			);
		}

		if ($data_evaluasi_dosen == 0) {
			$this->db->insert(
				'siakad_evaluasi_dosen',
				[
					'id_reg_pd' => $id_reg_pd,
					'id_kls' => $id_kls,
					'createby' => $this->ion_auth->user()->row()->first_name,
					'createdate' => date('Y-m-d H:i:s'),
				]
			);
		}

		$eval_akademik = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $this->setting->tahun_ajaran_future])->get('siakad_evaluasi_akademik')->num_rows();

		if ($eval_akademik == 0) {
			$this->db->insert(
				'siakad_evaluasi_akademik',
				[
					'id_reg_pd' => $id_reg_pd,
					'id_smt' => $this->setting->tahun_ajaran_future,
					'createby' => $this->ion_auth->user()->row()->first_name,
					'createdate' => date('Y-m-d H:i:s'),
				]
			);
		}



		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}

		//$this->student->delete_by_id($id);
		//echo json_encode(array("status" => TRUE));
	}

	public function get_data_approve($id_reg_pd)
	{
		$diajukan = $this->db->select('*')
			->from('siakad_validasi_krs v')
			->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
			->where(['v.id_reg_pd' => $id_reg_pd, 'sk.id_smt' => $this->setting->tahun_ajaran_aktif])
			->get()
			->num_rows();

		$disetujui = $this->db->select('*')
			->from('siakad_validasi_krs v')
			->join('feeder_kelas_kuliah sk', 'sk.id_kls = v.id_kls')
			->where([
				'v.id_reg_pd' => $id_reg_pd,
				'sk.id_smt' => $this->setting->tahun_ajaran_aktif,
				'validation_status' => 'Disetujui'
			])
			->get()
			->num_rows();
		echo json_encode(["diajukan" => $diajukan, "disetujui" => $disetujui]);
	}

	public function ajax_not_approve($id_reg_pd, $id_kls)
	{
		$this->db->trans_begin();
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->update(
			'siakad_validasi_krs',
			[
				'validation_status' => 'Tidak Disetujui',
				'validation_date' => NULL,
				'changeby' => $this->ion_auth->user()->row()->first_name,
				'changedate' => date('Y-m-d H:i:s'),
			]
		);
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->delete('feeder_nilai');
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_kls' => $id_kls])->delete('siakad_evaluasi_dosen');
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $this->setting->tahun_ajaran_future])->delete('siakad_evaluasi_akademik');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function ajax_approve_krk($nipd, $id_validasi_krk)
	{

		$this->simkrekema_db->trans_begin();
		$this->simkrekema_db->where(['nipd' => $nipd, 'id_validasi_krk' => $id_validasi_krk])
			->update('validasi_krk', ['status_persetujuan' => 1, 'tanggal_disetujui_wali' => date('Y-m-d H:i:s')]);


		$this->simkrekema_db->trans_complete();

		if ($this->simkrekema_db->trans_status() === FALSE) {
			$this->simkrekema_db->trans_rollback();
			return FALSE;
		} else {
			$this->simkrekema_db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function ajax_not_approve_krk($nipd, $id_validasi_krk)
	{
		$this->simkrekema_db->trans_begin();
		$this->simkrekema_db->where(['nipd' => $nipd, 'id_validasi_krk' => $id_validasi_krk])
			->delete('validasi_krk');

		$this->simkrekema_db->trans_complete();

		if ($this->simkrekema_db->trans_status() === FALSE) {
			$this->simkrekema_db->trans_rollback();
			return FALSE;
		} else {
			$this->simkrekema_db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}


	/*
	public function ajax_set_fase_3($id_reg_pd){
		$id_smt = $this->session->userdata("tahun_ajaran");
		$this->db->trans_begin();
        $this->db->where(array('id_reg_pd'=>$id_reg_pd, 'id_smt'=>$id_smt))->update('siakad_registration_pool', 
        							array( 
        								'id_stage'=>3,
        								'update_by'=>$this->ion_auth->user()->row()->first_name,
        								'update_date'=>date('Y-m-d H:i:s'),
        							 ));
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
        	return FALSE;
        } else {
            $this->db->trans_commit();
            echo json_encode(array("status" => TRUE));
	    }
	}

	public function ajax_set_fase_2($id_reg_pd){
		$id_smt = $this->session->userdata("tahun_ajaran");
		$this->db->trans_begin();
        $this->db->where(array('id_reg_pd'=>$id_reg_pd, 'id_smt'=>$id_smt))->update('siakad_registration_pool', 
        							array( 
        								'id_stage'=>2,
        								'update_by'=>$this->ion_auth->user()->row()->first_name,
        								'update_date'=>date('Y-m-d H:i:s'),
        							 ));
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {     
            $this->db->trans_rollback();
        	return FALSE;
        } else {
            $this->db->trans_commit();
            echo json_encode(array("status" => TRUE));
	    }
	}
*/

	public function ajax_set_fase_3($id_reg_pd)
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$this->db->trans_begin();
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $id_smt])->update(
			'siakad_registration_pool',
			[
				'id_stage' => 3,
				'is_perbaikan' => 0,
				'update_by' => $this->ion_auth->user()->row()->first_name,
				'update_date' => date('Y-m-d H:i:s'),
			]
		);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$nipd = $this->db->where('id_reg_pd', $id_reg_pd)->get('mahasiswa_pt')->row()->nipd;
			$this->log_library->log_history('approval_krs', '', $nipd);
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function ajax_set_fase_2($id_reg_pd)
	{
		$id_smt = $this->session->userdata("tahun_ajaran");
		$this->db->trans_begin();
		$this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt' => $id_smt])->update(
			'siakad_registration_pool',
			[
				'id_stage' => 2,
				'is_perbaikan' => 1,
				'update_by' => $this->ion_auth->user()->row()->first_name,
				'update_date' => date('Y-m-d H:i:s'),
			]
		);

		/*$this->db->insert('test_perbaikan', array('id_reg_pd'=>$id_reg_pd, 
        											     'alasan_perbaikan' => $this->input->post('inputValue'),
        											     'id_smt' =>$id_smt,
        											     'create_by'=>$this->ion_auth->user()->row()->id,
        												 'create_date'=>date('Y-m-d H:i:s'),
        											     ));*/


		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$nipd = $this->db->where('id_reg_pd', $id_reg_pd)->get('mahasiswa_pt')->row()->nipd;
			$this->log_library->log_history('perbaikan_krs', '', $nipd);
			$this->db->trans_commit();
			echo json_encode(["status" => TRUE]);
		}
	}

	public function cetak_krs()
	{
		$this->load->model('nilai/nilai_model', 'nilai_model');

		$setting = $this->db->get('siakad_setting')->row();
		$id_reg_pd = $this->session->userdata('id_reg_pd');

		$nama_semester = $this->db->where('id_smt', $setting->tahun_ajaran_aktif)->get('semester')->row()->nm_smt;
		$ion_user = $this->ion_auth->user()->row();
		$nipd = $ion_user->nim;
		$mahasiswa_pt = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->row();
		$id_dosen_pa = $this->db->where('id_pd', $mahasiswa_pt->id_pd)->get('siakad_mahasiswa_additional')->row();
		$setting_kaprodi = $this->db->where('id_sms', $mahasiswa_pt->id_sms)
			->where('id_smt', $setting->tahun_ajaran_aktif)->get('siakad_kaprodi')->row();
		$nm_pd = $this->db->select('nm_pd')
			->from('mahasiswa_pt mp')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')->where('nipd', $nipd)->get()->row()->nm_pd;
		$nm_smt = strtoupper($this->db->where('id_smt', $setting->tahun_ajaran_future)->get('semester')->row()->nm_smt);

		$output['photo'] = $id_dosen_pa->foto;
		$output['content'] 				= 'nilai/nilai_view';
		$output['judul_besar'] 			= 'Nilai Mahasiswa ';
		$output['judul_kecil'] 			= '';
		$output['m_mahasiswa'] 			= TRUE;
		$output['m_tahun'] 				= $setting->tahun_ajaran_aktif;
		$output['master_page'] 			= 'detail';
		$output['m_treeview_nilai'] 	= TRUE;
		$output['nm_smt'] = $nm_smt;
		$output['nipd'] = $nipd;
		$output['nm_pd'] = $nm_pd;
		$output['nm_lemb'] = $this->db->where('id_sms', $mahasiswa_pt->id_sms)->get('sms')->row()->nm_lemb;
		$output['dosen_pa'] = $this->db->where('id_ptk', $id_dosen_pa->dosen_pa)->get('siakad_dosen')->row()->nm_ptk;
		$output['tgl_cetak'] = $setting_kaprodi->tgl_cetak_khs;
		$output['nm_kaprodi'] = $setting_kaprodi->nm_kaprodi;
		$output['nidn'] = $setting_kaprodi->nidn;

		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-black">',
			'thead_open'          => '<thead >',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center">',
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
		$this->table->set_heading(['No.', 'Kode MK', 'Mata Kuliah', 'SKS', 'Kelas', 'Dosen', 'Status']);


		$i = 1;

		$rencana_studi = $this->db->select('kk.id_kls, kk.nm_kls, kk.sks_mk, sn.validation_status,
								  kk.sks_tm, kk.sks_prak, mk.nm_mk, mk.kode_mk, 
								  d.nm_ptk, sn.id_reg_pd, kk.hari,kk.jam_mulai, kk.jam_selesai')
			->from('siakad_validasi_krs sn')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('siakad_dosen d', 'd.id_ptk = kk.id_ptk', 'LEFT')
			->where('kk.id_sms', $this->session->userdata('prodi'))
			->where('sn.validation_status', 'Daftar')
			->where('sn.id_reg_pd', $id_reg_pd)
			->order_by('nm_mk')
			->get();

		$sks_mk = 0;
		foreach ($rencana_studi->result() as $krs) {
			$hari = $this->db->where('uniq', $krs->hari)->get('siakad_hari')->row()->nama_hari;
			$jam = substr($krs->jam_mulai, 0, 5) . ' s/d ' . substr($krs->jam_selesai, 0, 5);

			$sks_mk = $krs->sks_mk + $sks_mk;
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				['data' => $krs->kode_mk, 'class' => 'text-center'],
				$krs->nm_mk,
				['data' => $krs->sks_mk, 'class' => 'text-center'],
				['data' => $krs->nm_kls, 'class' => 'text-center'],
				$krs->nm_ptk,
				['data' => $krs->validation_status, 'class' => 'text-center'],


			]);
		}

		$this->table->add_row([
			['data' => 'JUMLAH SKS', 'class' => 'text-center', 'colspan' => 3],
			['data' => $sks_mk, 'class' => 'text-center'], '', '', '',
		]);

		$sks_semester = $this->nilai_model->hitung_sks_semester($id_reg_pd, $setting->tahun_ajaran_aktif);
		$nilai_semester = $this->nilai_model->hitung_nilai_semester($id_reg_pd, $setting->tahun_ajaran_aktif);
		$ips = round($nilai_semester / $sks_semester, 2);
		//$ips = $nilai_semester.'/'.$sks_semester;

		$total_sks = $this->nilai_model->hitung_totalsks($id_reg_pd);
		$total_nilai = $this->nilai_model->hitung_total_nilai($id_reg_pd);
		$ipk = round($total_nilai / $total_sks, 2);

		/*$eval_dosen = '';
		$eval_akademik = '';
		$cek_eval_dosen = $this->db->select('status_eval')
						  ->from('siakad_evaluasi_dosen sev')
						  ->join('feeder_kelas_kuliah sek','sev.id_kls = sek.id_kls')
						  ->where(array('id_reg_pd'=>$id_reg_pd, 'id_smt'=>$setting->tahun_ajaran_aktif))
						  ->get();
		$cek_eval_akademik = $this->db->select('akademik_1')
						  ->from('siakad_evaluasi_akademik')
						  ->where(array('id_reg_pd'=>$id_reg_pd, 'id_smt'=>$setting->tahun_ajaran_aktif))
						  ->get();*/

		$nilai_tabel =  $this->table->generate();
		$output['tabel'] = $nilai_tabel;

		$this->load->view('cetak/cetak_krs', $output);
	}
}
