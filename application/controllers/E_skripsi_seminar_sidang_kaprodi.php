<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_sidang_kaprodi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_seminar_sidang_kaprodi_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index($i = '')
	{
		$output['parent_admin_ta'] = 'active open';

		if ($i == '' or $i == 1) {
			$output['menu_aktif'] = 'm_seminar_proposal';
			$output['judul_besar'] = 'Persetujuan Seminar Proposal';
			$output['judul_kecil'] = 'Politeknik WBI';
		} elseif ($i == 2) {
			$output['menu_aktif'] = 'm_seminar_hasil';
			$output['judul_besar'] = 'Persetujuan Seminar Hasil';
			$output['judul_kecil'] = 'Politeknik WBI';
		} elseif ($i == 3) {
			$output['menu_aktif'] = 'm_sidang_tugas_akhir';
			$output['judul_besar'] = 'Persetujuan Sidang Tugas Akhir';
			$output['judul_kecil'] = 'Politeknik WBI';
		}

		$output['index_page'] = $i;
		$output['content'] = 'e_skripsi_seminar_sidang_kaprodi/e_skripsi_seminar_sidang_kaprodi_view';
		$output['after_page'] = 'e_skripsi_seminar_sidang_kaprodi/e_skripsi_seminar_sidang_kaprodi_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list($index_page)
	{
		$list = $this->E_skripsi_seminar_sidang_kaprodi_model->get_datatables($index_page);
		$data = [];
		$no = $_POST['start'];


		foreach ($list as $rg) {
			$judul = $this->db->from('siakad_skripsi_pengajuan_judul')->where(['nipd' => $rg->nipd])->get()->row();
			$z = '-';
			$tgl_1 = $tgl_2 = '-';
			$nm_penguji_1 = $nm_penguji_2 = $nm_penguji_3 = '';

			$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji_3 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_3)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

			if (!empty($doping_1)) {
				$nm_penguji_1 = $doping_1->nama_lengkap;
			}

			if (!empty($doping_2)) {
				$nm_penguji_2 = $doping_2->nama_lengkap;
			}

			if (!empty($penguji_3)) {
				$nm_penguji_3 = $penguji_3->nama_lengkap;
			}

			if ($rg->kategori_pengajuan == 1) {
				$x = 'Seminar Proposal';
			} elseif ($rg->kategori_pengajuan == 2) {
				$x = 'Seminar Hasil';
			} elseif ($rg->kategori_pengajuan == 3) {
				$x = 'Ujian/Sidang Tugas Akhir';
			}

			$persetujuan = '';
			if ($rg->approval_p1 == 0) {
				$y = 'Belum Disetujui';
			} elseif ($rg->approval_p1 == 1) {
				$y = 'Approved';
				$tgl_1 = tgl_indo_short($rg->tgl_approval_p1);

				$persetujuan = '<small>P1: ' . $y . ' (' . $tgl_1 . ')</small>';
			}

			if (!empty($judul->dosen_pembimbing_2)) {
				if ($rg->approval_p2 == 0) {
					$z = 'Belum Disetujui';
				} elseif ($rg->approval_p2 == 1) {
					$z = 'Approved';
					$tgl_2 = tgl_indo_short($rg->tgl_approval_p2);
					$persetujuan .= '<small><br>P2: ' . $z . ' (' . $tgl_2 . ')</small>';
				}
			}

			$nilai = $this->db->from('siakad_skripsi_nilai_sempro')->where(['nipd' => $rg->nipd])->get();

			if ($nilai->num_rows()) {
				if ($rg->kategori_pengajuan == 1) {
					$hasil = $nilai->row()->sempro_hasil;
					$nilai_penguji_1 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_1, $judul->id_judul_skripsi, 1);
					$nilai_penguji_2 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_2, $judul->id_judul_skripsi, 1);
					$nilai_penguji_3 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_3, $judul->id_judul_skripsi, 1);
				} elseif ($rg->kategori_pengajuan == 2) {
					$hasil = $nilai->row()->semhas_hasil;
					$nilai_penguji_1 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_1, $judul->id_judul_skripsi, 2);
					$nilai_penguji_2 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_2, $judul->id_judul_skripsi, 2);
					$nilai_penguji_3 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_3, $judul->id_judul_skripsi, 2);
				} elseif ($rg->kategori_pengajuan == 3) {
					$hasil = $nilai->row()->sidang_hasil;
					$nilai_penguji_1 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_1, $judul->id_judul_skripsi, 3);
					$nilai_penguji_2 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_2, $judul->id_judul_skripsi, 3);
					$nilai_penguji_3 = $this->E_skripsi_seminar_sidang_kaprodi_model->get_nilai_by_penguji($judul->dosen_penguji_sempro_3, $judul->id_judul_skripsi, 3);
				}
			} else {
				$hasil = '-';
				$nilai_penguji_1 = 0;
				$nilai_penguji_2 = 0;
				$nilai_penguji_3 = 0;
			}
			$tgl_seminar = '';
			$tgl = $rg->tanggal_seminar;
			if ($this->validateDate($tgl)) {
				$tgl_seminar = tgl_indo_short(date('Y-m-d', strtotime($tgl)));
			}

			$seminar = 'Tgl : ' . $tgl_seminar . '<br>Jam : ' . $rg->jam . '<br>Hasil : ' . ucfirst($hasil);

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $x . '<br>' . $persetujuan;
			$row[] = '<small>1. ' . $nm_penguji_1 . ' (' . $nilai_penguji_1 . ')' . '<br>2. ' . $nm_penguji_2 . ' (' . $nilai_penguji_2 . ')' . '<br>3. ' . $nm_penguji_3 . ' (' . $nilai_penguji_3 . ')' . '</small>';
			$row[] = $rg->is_akademik == 0 ? 'Kaprodi' : 'Akademik';
			$row[] = $seminar;
			if ($rg->is_akademik == 0 and $rg->is_kaprodi == 1) {
				if ($rg->kategori_pengajuan == 1) {
					// $row[] = '<a class="btn green-stripe btn-default btn-xs btn-block margin-bottom-5" 
					// 				href="javascript:void(0)" 
					// 				title="Edit" 
					// 				onclick="edit_penguji(' . "'" . $rg->uniq . "'" . ')">
					// 				Dosen Penguji
					// 			</a>
					// 			<a class="btn green-stripe btn-default btn-xs btn-block" 
					// 				href="javascript:void(0)" 
					// 				title="Edit" 
					// 				onclick="disposisi_akademik(' . "'" . $rg->uniq . "'" . ')">
					// 				Submit Ke Akademik
					// 			</a>';
					$row[] = "<a class='btn green-stripe btn-default btn-xs btn-block margin-bottom-5'
										href='javascript:void(0)'
										title='Edit'
										onclick=\"edit_penguji('{$rg->uniq}')\">
										Dosen Penguji
									</a>
									<a class='btn green-stripe btn-default btn-xs btn-block'
										href='javascript:void(0)'
										title='Edit'
										onclick=\"disposisi_akademik('{$rg->uniq}')\">
										Submit Ke Akademik
									</a>";
				} elseif ($rg->kategori_pengajuan == 2) {
					$row[] = "<a class='btn green-stripe btn-default btn-xs btn-block'
								href='javascript:void(0)'
								title='Edit'
								onclick=\"disposisi_akademik('{$rg->uniq}')\">
								Submit Ke Akademik
							</a>";
				} elseif ($rg->kategori_pengajuan == 3) {
					$row[] = "<a class='btn green-stripe btn-default btn-xs btn-block'
								href='javascript:void(0)'
								title='Edit'
								onclick=\"disposisi_sidang_ke_akademik('{$rg->uniq} ')\">
								Submit Ke Akademik
							</a>";
				}
				$row[] = 1;
			} else {
				if ($hasil == 'layak' || $hasil == 'LULUS') {
					if ($index_page == 1) {
						$row[] = '<a class="btn btn-default green-stripe btn-xs" 
									href="' . site_url('E_skripsi_nilai_sempro/cetak_pdf/' . md5($rg->id_judul_skripsi)) . '"  target = "_new"
									title="Berita Acara Sempro" ><i class="fa fa-download"></i> Download
								</a>';
					} elseif ($index_page == 2) {
						$row[] = '<a class="btn btn-default green-stripe btn-xs" 
									href="' . site_url('E_skripsi_seminar_hasil/cetak_pdf/' . md5($rg->id_judul_skripsi)) . '"  target = "_new"
									title="Berita Acara Semhas" ><i class="fa fa-download"></i> Download
								</a>';
					} elseif ($index_page == 3) {
						$row[] = '<a class="btn btn-default green-stripe btn-xs" 
									href="' . site_url('E_skripsi_sidang/cetak_pdf/' .  md5($rg->id_judul_skripsi)) . '"  target = "_new"
									title="Berita Acara Sidang" ><i class="fa fa-download"></i> Download
								</a>';
					}
				} else {
					$row[] = '';
				}

				$row[] = 0;
			}


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_seminar_sidang_kaprodi_model->count_all($index_page),
			"recordsFiltered" => $this->E_skripsi_seminar_sidang_kaprodi_model->count_filtered($index_page),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}

	public function ajax_edit($id)
	{
		$nm_pembimbing_utama = $nm_pembimbing_pendamping = $nm_penguji_1 = $nm_penguji_2 = $nm_penguji_3 = '';
		$data = $this->E_skripsi_seminar_sidang_kaprodi_model->get_by_id_2($id);

		$doping_1 = $this->db->where('sd.id_ptk', $data->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$doping_2 = $this->db->where('sd.id_ptk', $data->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		$penguji_1 = $this->db->where('sd.id_ptk', $data->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_2 = $this->db->where('sd.id_ptk', $data->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_3 = $this->db->where('sd.id_ptk', $data->dosen_penguji_sempro_3)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		if (!empty($doping_1)) {
			$nm_pembimbing_utama = $doping_1->nama_lengkap;
			$data->nm_pembimbing_utama = $nm_pembimbing_utama;
		}

		if (!empty($doping_2)) {
			$nm_pembimbing_pendamping = $doping_2->nama_lengkap;
			$data->nm_pembimbing_pendamping = $nm_pembimbing_pendamping;
		}

		if (!empty($penguji_1)) {
			$nm_penguji_1 = $penguji_1->nama_lengkap;
			$data->nm_penguji_1 = $nm_penguji_1;
		}

		if (!empty($penguji_2)) {
			$nm_penguji_2 = $penguji_2->nama_lengkap;
			$data->nm_penguji_2 = $nm_penguji_2;
		}

		if (!empty($penguji_3)) {
			$nm_penguji_3 = $penguji_3->nama_lengkap;
			$data->nm_penguji_3 = $nm_penguji_3;
		}

		echo json_encode($data);
	}

	public function ajax_edit_pengajuan_akademik($id)
	{
		$data = $this->E_skripsi_seminar_sidang_kaprodi_model->get_by_id_2($id);
		echo json_encode($data);
	}

	public function ajax_submit_akademik()
	{
		$this->_validate_cek_penguji($this->input->post('uniq'));
		$data = [
			'is_akademik' => 1,
			'pesan_singkat_kaprodi' => $this->input->post('pesan_singkat_kaprodi'),
			'tgl_is_akademik' => date('Y-m-d H:i:s'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->E_skripsi_seminar_sidang_kaprodi_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_save_penguji()
	{
		$this->_validate();
		$data = [
			'dosen_penguji_sempro_1' => $this->input->post('dosen_penguji_sempro_1'),
			'dosen_penguji_sempro_2' => $this->input->post('dosen_penguji_sempro_2'),
			'dosen_penguji_sempro_3' => $this->input->post('dosen_penguji_sempro_3'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->E_skripsi_seminar_sidang_kaprodi_model->update_penguji(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_submit_sidang_akademik()
	{
		$this->_validate_sidang();
		$data = [
			'is_akademik' => 1,
			'pesan_singkat_kaprodi' => $this->input->post('pesan_singkat_kaprodi'),
			'penguji_sidang_1' => $this->input->post('penguji_sidang_1'),
			'penguji_sidang_2' => $this->input->post('penguji_sidang_2'),
			'penguji_sidang_3' => $this->input->post('penguji_sidang_3'),
			'tgl_is_akademik' => date('Y-m-d H:i:s'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->E_skripsi_seminar_sidang_kaprodi_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('dosen_penguji_sempro_1') == '') {
			$data['inputerror'][] = 'dosen_penguji_sempro_1';
			$data['error_string'][] = 'Harus Diisi';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_penguji_sempro_2') == '') {
			$data['inputerror'][] = 'dosen_penguji_sempro_2';
			$data['error_string'][] = 'Harus Diisi';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_penguji_sempro_3') == '') {
			$data['inputerror'][] = 'dosen_penguji_sempro_3';
			$data['error_string'][] = 'Harus Diisi';
			$data['status'] = FALSE;
		}

		/* if ($this->input->post('dosen_penguji_sempro_2') == '') {
			$data['inputerror'][] = 'dosen_penguji_sempro_2';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} */

		$judul = $this->db->where('id_judul_skripsi', $this->input->post('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		/* if ($this->input->post('dosen_penguji_sempro_1') == $judul->dosen_pembimbing_1) {
			$data['inputerror'][] = 'dosen_penguji_sempro_1';
			$data['error_string'][] = 'Dosen Pembimbing Utama tidak boleh menjadi Penguji';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_penguji_sempro_2') == $judul->dosen_pembimbing_1) {
			$data['inputerror'][] = 'dosen_penguji_sempro_2';
			$data['error_string'][] = 'Dosen Pembimbing Utama tidak boleh menjadi Penguji';
			$data['status'] = FALSE;
		} */

		/* if ($this->input->post('dosen_penguji_sempro_1') == $judul->dosen_pembimbing_2) {
			$data['inputerror'][] = 'dosen_penguji_sempro_1';
			$data['error_string'][] = 'Dosen Pembimbing Pendamping tidak boleh menjadi Penguji';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_penguji_sempro_2') == $judul->dosen_pembimbing_2) {
			$data['inputerror'][] = 'dosen_penguji_sempro_2';
			$data['error_string'][] = 'Dosen Pembimbing Pendamping tidak boleh menjadi Penguji';
			$data['status'] = FALSE;
		} */

		if ($this->input->post('dosen_penguji_sempro_2') == $this->input->post('dosen_penguji_sempro_1')) {
			$data['inputerror'][] = 'dosen_penguji_sempro_2';
			$data['error_string'][] = 'Dosen Penguji 1 dan Penguji 2 harus berbeda';
			$data['status'] = FALSE;
		}


		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_sidang()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('penguji_sidang_1') == '') {
			$data['inputerror'][] = 'penguji_sidang_1';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('penguji_sidang_2') == '') {
			$data['inputerror'][] = 'penguji_sidang_2';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('penguji_sidang_3') == '') {
			$data['inputerror'][] = 'penguji_sidang_3';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_cek_penguji($uniq)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$id_judul_skripsi = $this->db->where('uniq', $uniq)->get('siakad_skripsi_seminar_sidang')->row()->id_judul_skripsi;
		$cek = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row();

		if ($cek->dosen_penguji_sempro_1 == '' or $cek->dosen_penguji_sempro_2 == '' or $cek->dosen_penguji_sempro_3 == '') {
			$data['inputerror'][] = 'pesan_singkat_kaprodi';
			$data['error_string'][] = 'Dosen Penguji Belum Lengkap. Mohon lengkapi terlebih dahulu.';
			$data['status'] = FALSE;
		}



		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function kategori_check($str)
	{
		$nipd = $this->session->userdata('nim');
		$cek_data = $this->db->where(['nipd' => $nipd, 'kategori_pengajuan' => $str])->get('siakad_skripsi_seminar_sidang');
		if ($cek_data->num_rows() == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getDosenPenguji()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_seminar_sidang_kaprodi_model->get_dosen_penguji());
	}
}
