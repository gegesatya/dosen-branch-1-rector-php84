<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_seminar_sidang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_seminar_sidang_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] = 'Pengajuan Seminar dan Sidang Tugas Akhir';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_seminar_sidang';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'e_skripsi_seminar_sidang/e_skripsi_seminar_sidang_view';
		$output['after_page'] = 'e_skripsi_seminar_sidang/e_skripsi_seminar_sidang_after_page';

		//$judul = $this->db->from('siakad_skripsi_pengajuan_judul')->where(['nipd' => $this->session->userdata('nim')])->get()->row();
		//$output['judul'] = $judul;
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->E_skripsi_seminar_sidang_model->get_datatables();
		$data = [];
		$no = $_POST['start'];

		foreach ($list as $rg) {
			$judul = $this->db->from('siakad_skripsi_pengajuan_judul')->where(['nipd' => $rg->nipd])->get()->row();
			$z = 'NIL';
			$tgl_1 = $tgl_2 = '';
			$all_approve = 0;

			if ($rg->kategori_pengajuan == 1) {
				$x = 'Seminar Proposal';
			} elseif ($rg->kategori_pengajuan == 2) {
				$x = 'Seminar Hasil';
			} elseif ($rg->kategori_pengajuan == 3) {
				$x = 'Ujian/Sidang Tugas Akhir';
			}

			if ($rg->approval_p1 == 0) {
				$y = 'Belum Disetujui';
				$all_approve = 0;
			} elseif ($rg->approval_p1 == 1) {
				$y = 'Sudah Disetujui';
				$tgl_1 = tgl_indo($rg->tgl_approval_p1);
				$all_approve = 1;
			}

			if (!empty($judul->dosen_pembimbing_2)) {
				$all_approve = 0;
				if ($rg->approval_p2 == 0) {
					$z = 'Belum Disetujui';
					$all_approve = 0;
				} elseif ($rg->approval_p2 == 1) {
					$z = 'Sudah Disetujui';
					$tgl_2 = tgl_indo($rg->tgl_approval_p2);
					$all_approve = 1;
				}
			}

			$html_link_dokumen = '<a href="' . $rg->link_dokumen . '" class="btn btn-sm btn-default blue-stripe" target="_new"> <i class="fa fa-download"></i> Dokumen TA</a>';

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $x;
			$row[] = $y . '<br>' . $tgl_1;
			$row[] = $z . '<br>' . $tgl_2;
			$row[] = $html_link_dokumen; //"<a href='$rg->link_dokumen' target='_new'>" . $rg->link_dokumen;
			if ($rg->is_kaprodi == 0) {
				if ($all_approve == 1) {
					if ($judul->dosen_pembimbing_1 == $this->session->userdata('id_ptk')) {
						$row[] = '<a class="btn green-stripe btn-default btn-xs btn-block margin-bottom-5" 
										href="javascript:void(0)" 
										title="Edit" 
										onclick="edit_pengajuan(' . "'" . $rg->uniq . "'" . ')">
										Persetujuan dan Ganti Judul
									</a>
									<a class="btn green-stripe btn-default btn-xs btn-block" 
										href="javascript:void(0)" 
										title="Edit" 
										onclick="pengajuan_kaprodi(' . "'" . $rg->uniq . "'" . ')">
										Submit Ke Kaprodi
									</a>';
					} else {
						$row[] = '<a class="btn green-stripe btn-default btn-xs btn-block margin-bottom-5" 
										href="javascript:void(0)" 
										title="Edit" 
										onclick="edit_pengajuan(' . "'" . $rg->uniq . "'" . ')">
										Persetujuan dan Ganti Judul
									</a>';
					}
				} else {
					if ($all_approve == 0) {
						$row[] = '<a class="btn green-stripe btn-default btn-xs btn-block margin-bottom-5" 
									href="javascript:void(0)" 
									title="Edit" 
									onclick="edit_pengajuan(' . "'" . $rg->uniq . "'" . ')">
									Persetujuan dan Ganti Judul
								</a>';
					}
				}
				$row[] = 1;
			} else {
				/* if ($rg->terbit_surat_tugas == 1) {
					$html_surat_tugas = '<a href="' . site_url('E_skripsi_notifikasi/cetak_surat_tugas') . '/' . md5($rg->id_judul_skripsi)  . '" class="btn btn-sm btn-default btn-block margin-bottom-5 green-stripe" target="_new"><i class="fa fa-download"></i> Surat Tugas Penguji</a>';
				} else {
					$html_surat_tugas = '';
				}

				if ($rg->status_undangan == 1) {
					if ($rg->kategori_pengajuan == 1) {
						$html_undangan = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan') . '/' . md5($rg->id_judul_skripsi)  . '" class="btn btn-sm btn-default btn-block green-stripe" target="_new"> <i class="fa fa-download"></i> Surat Undangan</a>';
					} elseif ($rg->kategori_pengajuan == 2) {
						$html_undangan = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_semhas') . '/' . md5($rg->id_judul_skripsi)  . '" class="btn btn-sm btn-default btn-block green-stripe" target="_new"> <i class="fa fa-download"></i> Surat Undangan</a>';
					}
				} else {
					$html_undangan = '';
				} */

				$row[] = '-';
				$row[] = 0;
			}


			$data[] = $row;

			/* <a class="btn btn-sm btn-danger btn-xs" 
				         href="javascript:void()" 
				         title="Delete" onclick="delete_pengajuan(' . "'" . $rg->uniq . "'" . ')">
				         <i class="glyphicon glyphicon-trash"></i>
				      </a> */
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_seminar_sidang_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_seminar_sidang_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		/* $data = $this->E_skripsi_seminar_sidang_model->get_by_id($id);
		echo json_encode($data); */

		$data = $this->E_skripsi_seminar_sidang_model->get_by_id_2($id);

		$id_ptk = $this->session->userdata('id_ptk');
		if ($id_ptk == $data->dosen_pembimbing_1) {
			$data->is_doping_1 = TRUE;
		} else {
			$data->is_doping_1 = FALSE;
		}

		if ($id_ptk == $data->dosen_pembimbing_2) {
			$data->is_doping_2 = TRUE;
		} else {
			$data->is_doping_2 = FALSE;
		}

		echo json_encode($data);
	}



	public function ajax_add()
	{
		$this->_validate();
		$data = [
			'kategori_pengajuan' => $this->input->post('kategori_pengajuan'),
			'nipd' => $this->input->post('nipd'),
			'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
			'link_dokumen' => $this->input->post('link_dokumen'),
			'create_by' => $this->ion_auth->user()->row()->id,
			'create_date' => date('Y-m-d H:i:s')
		];
		$insert = $this->E_skripsi_seminar_sidang_model->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update2()
	{
		$this->_validate();
		$data = [
			'kategori_pengajuan' => $this->input->post('kategori_pengajuan'),
			'nipd' => $this->input->post('nipd'),
			'id_judul_skripsi' => $this->input->post('id_judul_skripsi'),
			'link_dokumen' => $this->input->post('link_dokumen'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->E_skripsi_seminar_sidang_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_edit_pengajuan_kaprodi($id)
	{
		$data = $this->E_skripsi_seminar_sidang_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_submit_kaprodi()
	{
		$this->_validate_submit_kaprodi($this->input->post('uniq'));
		$data = [
			'is_kaprodi' => 1,
			'pesan_singkat' => $this->input->post('pesan_singkat'),
			'tgl_is_kaprodi' => date('Y-m-d H:i:s'),
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->E_skripsi_seminar_sidang_model->update(['uniq' => $this->input->post('uniq')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$this->_validate_approval();
		$judul_lama = $this->input->post('judul_lama');
		if (empty($judul_lama)) {
			$current_judul = $this->db->where('id_judul_skripsi', $this->input->post('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();
			if ($current_judul) {
				$judul_lama = $current_judul->judul_ditetapkan;
			}
		}

		$tgl_approval_p1 = $tgl_approval_p2 = $tgl_maju_prodi = NULL;
		$is_ganti_judul = 0;

		if ($this->input->post('approval_p1') == true) {
			if ($this->input->post('is_approval_p1') == 0) {
				$tgl_approval_p1 = date('Y-m-d H:i:s');
			} else {
				$tgl_approval_p1 = $this->input->post('tgl_approval_p1');
			}
		}

		if ($this->input->post('approval_p2') == true) {
			if ($this->input->post('is_approval_p2') == 0) {
				$tgl_approval_p2 = date('Y-m-d H:i:s');
			} else {
				$tgl_approval_p2 = $this->input->post('tgl_approval_p2');
			}
		}

		if ($this->input->post('status_revisi_judul') == true) {
			$is_ganti_judul = 1;
			$data_judul = [
				'status_revisi_judul' => $this->input->post('status_revisi_judul') == true ? 1 : 0,
				'judul_ditetapkan' => $this->input->post('judul_ditetapkan_2'),
			];
		} else {
			$data_judul = [
				'status_revisi_judul' => $this->input->post('status_revisi_judul') == true ? 1 : 0,
			];
		}

		$data_approve = [
			'approval_p1' => $this->input->post('approval_p1') == true ? 1 : 0,
			'approval_p2' => $this->input->post('approval_p2') == true ? 1 : 0,
			'tgl_approval_p1' => $tgl_approval_p1,
			'tgl_approval_p2' => $tgl_approval_p2,
		];

		$uniq = $this->input->post('uniq');
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$this->E_skripsi_seminar_sidang_model->update_sempro(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data_judul, $data_approve, $judul_lama, ['uniq' => $uniq], $is_ganti_judul, $id_judul_skripsi);
		echo json_encode(["status" => TRUE]);
	}


	public function ajax_delete($id)
	{
		$this->E_skripsi_seminar_sidang_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}


	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('link_dokumen') == '') {
			$data['inputerror'][] = 'link_dokumen';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('kategori_pengajuan') == '') {
			$data['inputerror'][] = 'kategori_pengajuan';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->kategori_check($this->input->post('kategori_pengajuan')) == FALSE) {
			$data['inputerror'][] = 'kategori_pengajuan';
			$data['error_string'][] = 'Data sudah ada. Tidak bisa menambahkan pengajuan.';
			$data['status'] = FALSE;
		}

		/* if ($this->p2_approve_check($this->input->post('id_judul_skripsi')) == FALSE) {
			$data['inputerror'][] = 'approval_p1';
			$data['error_string'][] = 'Pembimbing 2 Belum Menyetujui';
			$data['status'] = FALSE;
		} */

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_approval()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('status_revisi_judul') == true) {
			$judul_baru = trim($this->input->post('judul_ditetapkan_2'));
			if ($judul_baru == '') {
				$data['inputerror'][] = 'judul_ditetapkan_2';
				$data['error_string'][] = 'Revisi Topik/Judul harus diisi jika ganti judul dipilih.';
				$data['status'] = FALSE;
			} else {
				$words = preg_split('/\s+/', $judul_baru, -1, PREG_SPLIT_NO_EMPTY);
				if (count($words) < 5) {
					$data['inputerror'][] = 'judul_ditetapkan_2';
					$data['error_string'][] = 'Revisi Topik/Judul minimal harus terdiri dari 5 kata.';
					$data['status'] = FALSE;
				}
			}
		}

		if ($this->p2_approve_check($this->input->post('id_judul_skripsi'), $this->input->post('uniq')) == FALSE) {
			$data['inputerror'][] = 'approval_p1';
			$data['error_string'][] = 'Pembimbing Pendamping Belum Menyetujui';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_submit_kaprodi($uniq)
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		//$uniq = $this->input->post('uniq');
		$sem_sidang = $this->db->where(compact('uniq'))->get('siakad_skripsi_seminar_sidang')->row();
		//$judul = $this->db->where('id_judul_skripsi', $sem_sidang->id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row();

		if ($sem_sidang->approval_p1 == 0) {
			$data['inputerror'][] = 'pesan_singkat';
			$data['error_string'][] = 'Dosen Pembimbing Belum Melakukan Persetujuan Seminar/Sidang. Silahkan klik tombol "Persetujuan dan Ganti Judul", kemudian checklist pada bagian approval dan jangan lupa menekan tombol SAVE.';
			$data['status'] = FALSE;
		}

		/* if ($sem_sidang->approval_p2 == 0) {
			$data['inputerror'][] = 'pesan_singkat';
			$data['error_string'][] = 'Dosen Pendamping Belum Melakukan Persetujuan Seminar/Sidang.';
			$data['status'] = FALSE;
		} */

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

	private function p2_approve_check($id_judul_skripsi, $uniq)
	{
		$judul = $this->db->where(['id_judul_skripsi' => $id_judul_skripsi])->get('siakad_skripsi_pengajuan_judul');
		$id_ptk = $this->session->userdata('id_ptk');
		if (!empty($judul->row()->dosen_pembimbing_2)) {
			if ($judul->row()->dosen_pembimbing_2 == $id_ptk) {
				return true;
			} else {
				$seminar_sidang = $this->db->where(['uniq' => $uniq])->get('siakad_skripsi_seminar_sidang');
				if ($seminar_sidang->row()->approval_p2 == 1) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return true;
		}
	}
}
