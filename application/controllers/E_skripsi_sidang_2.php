<?php


defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_sidang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_sidang_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus dosen.');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$output['judul_besar'] = 'Sidang Tugas Akhir';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_sidang';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'E_skripsi_sidang/E_skripsi_sidang_view';
		$output['after_page'] = 'E_skripsi_sidang/E_skripsi_sidang_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->E_skripsi_sidang_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		$id_ptk = $this->session->userdata('id_ptk');

		foreach ($list as $rg) {

			$id_judul_skripsi = $rg->id_judul_skripsi;
			
			$status_dosen = '';
			if ($id_ptk == $rg->dosen_penguji_sempro_1) {
				$status_dosen = 'Penguji 1';
			} elseif ($id_ptk == $rg->dosen_penguji_sempro_2) {
				$status_dosen = 'Penguji 2';
			} elseif ($id_ptk == $rg->dosen_penguji_sempro_3) {
				$status_dosen = 'Penguji 3';
			}

			$hasil = !empty($rg->hasil_pembimbing) ? $rg->hasil_pembimbing : '-';
			$no++;
			$row = [];
			$ratarata = round($rg->nilai_rerata, 2)  * 1;
			$row[] = $no;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = 'Status : ' . $status_dosen . '<br>Nilai Saya : <b>' . ($rg->sidang_nilai_akhir * 1) . '</b>';
			// $row[] = $rg->sidang_nilai_akhir;
			$row[] = '<span class="badge badge-primary">' . $ratarata . '</span> <br>(Dinilai : ' . $rg->hitung . ' dosen)<br><b>Hasil : ' . $hasil . '</b>';
			// $row[] = !empty($rg->hasil_pembimbing) ? $rg->hasil_pembimbing : '-';

			$is_akuntansi = $this->db->select('id_sms')->where('id_judul_skripsi', $rg->id_judul_skripsi)->get('siakad_skripsi_pengajuan_judul')->row()->id_sms == 'f28c79a3-18c1-4b40-b3af-b2bedc810e20';
			$req_penguji = $is_akuntansi ? 2 : ((!empty($rg->dosen_penguji_sempro_3) && $rg->dosen_penguji_sempro_3 != '0') ? 3 : 2);

			if ($rg->is_arsip == 0) {
				$row[] = '<a class="btn btn-default blue-stripe btn-xs btn-block margin-bottom-5" 
								href="javascript:void(0)" 
								title="Edit Nilai" 
								onclick="edit_nilai_sidang(' . "'" . $rg->id_nilai_sempro . "'" . ')">
								<i class="glyphicon glyphicon-pencil"></i> Nilai Sidang
							</a>
							<a class="btn btn-default green-stripe btn-xs btn-block" 
								href="javascript:void(0)" 
								title="Edit Usulan Revisi" 
								onclick="edit_catatan(' . "'" . $rg->id_nilai_sempro . "'" . ')">
								<i class="glyphicon glyphicon-list"></i> Usulan Revisi
							</a>';

				if ($id_ptk == $rg->dosen_pembimbing_1) {
					if ($rg->hitung >= $req_penguji) {
						$row[] = '<a class="btn btn-default blue-stripe btn-xs btn-block" 
									href="javascript:void(0)" 
									title="Berita Acara" 
									onclick="edit_berita_acara(' . "'" . $rg->id_nilai_sempro . "'" . ')">
									<i class="glyphicon glyphicon-pencil"></i> Berita Acara
									</a>';
					} else {
						$row[] = "<small>Berita acara belum bisa dibuat. <br>Belum semua Dosen mengisi nilai.</small>";
					}
				} else {
					$row[] = 'Diisi Pembimbing Utama';
				}
			} else {
				$row[] = 'NILAI SUDAH DIKUNCI';
				$row[] = '';
			}


			if ($rg->hitung >= $req_penguji) {
				$row[] = '<a class="btn btn-default red-stripe btn-xs" 
							href="' . site_url('E_skripsi_sidang/cetak_pdf/' .  md5($rg->id_judul_skripsi)) . '"  target = "_new"
							title="Berita Acara Sidang" ><i class="fa fa-download"></i> 
						</a>';
			} else {
				$row[] = '';
			}

			$row[] = $rg->sidang_nilai_akhir;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_sidang_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_sidang_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->E_skripsi_sidang_model->get_by_id($id);

		$id_judul_skripsi = $data->id_judul_skripsi;
		$nilai_all = $this->db->where(['id_judul_skripsi' => $id_judul_skripsi, 'id_sms' => $data->id_sms])
			->select('d.nama_lengkap, n.sidang_nilai_akhir')
			->from('siakad_skripsi_nilai_sempro n')
			->join('siakad_skripsi_dosen d', 'd.id_ptk = n.id_penilai')->get()
			->result();

		$nilai_rata_query = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(sidang_nilai_akhir) as sidang_nilai_akhir')->from('siakad_skripsi_nilai_sempro')->get()->row();
		$nilai_rata = round($nilai_rata_query->sidang_nilai_akhir, 2);

		$nilai_sempro = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(nilai_akhir) as nilai_sempro')->from('siakad_skripsi_nilai_sempro')->get()->row();
		$round_nilai_sempro = round($nilai_sempro->nilai_sempro, 2);

		$nilai_semhas = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(semhas_nilai_akhir) as nilai_semhas')->from('siakad_skripsi_nilai_sempro')->get()->row();
		$round_nilai_semhas = round($nilai_semhas->nilai_semhas, 2);

		$is_lulus = (isset($data->sidang_hasil) && $data->sidang_hasil == 'LULUS');

		if ($is_lulus) {
			$jumlah_total = ($round_nilai_sempro * 1) + ($round_nilai_semhas * 1) + ($nilai_rata * 4 * 1);
			$hasil_sidang = 'LULUS';
		} else {
			$jumlah_total = ($round_nilai_sempro * 0.1) + ($round_nilai_semhas * 0.1) + ($nilai_rata * 0.8);
			$nilai_skala_100 = ($jumlah_total / 7) * 100;
			
			if ($nilai_rata <= 3) {
				$hasil_sidang = 'TIDAK LULUS';
			} else {
				$hasil_sidang = 'LULUS';
			}
		}

		$tbl_nilai = '<table class="table table-bordered"><thead><tr><th>Dosen</th><th>Nilai Akhir</th></tr></thead><tbody>';
		foreach ($nilai_all as $key => $value) {
			$tbl_nilai .= '<tr><td>' . $value->nama_lengkap . '</td><td>' . $value->sidang_nilai_akhir . '</td></tr>';
		}
		$tbl_nilai .= '<tr><td>Rata-rata</td><td>' . $nilai_rata . '</td></tr>';
		$tbl_nilai .= '</table>';
		
		$tbl_nilai .= '<br>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Nilai Rata-rata</th>
									<th class="text-center">Nilai (N)</th>';
		if ($is_lulus) {
			$tbl_nilai .= '
									<th class="text-center">SKS (K)</th>
									<th class="text-center">N*K</th>
								</tr>
							</thead>
						<tbody>
							<tr>
								<td>Seminar Proposal</td><td class="text-center">' . $round_nilai_sempro . '</td><td class="text-center">1</td><td class="text-center">' . ($round_nilai_sempro * 1) . '</td>
							</tr>
							<tr>
								<td>Seminar Hasil</td><td class="text-center">' . $round_nilai_semhas . '</td><td class="text-center">1</td><td class="text-center">' . ($round_nilai_semhas * 1) . '</td>
							</tr>
							<tr>
								<td>Ujian/Sidang Tugas Akhir</td><td class="text-center">' . $nilai_rata . '</td><td class="text-center">4</td><td class="text-center">' . ($nilai_rata * 4 * 1) . '</td>
							</tr>
							<tr><td colspan="2">Total</td><td class="text-center">6</td><td class="text-center">' . $jumlah_total . '</td></tr>
							<tr><td colspan="2">&#931; (N*K)/&#931; K</td><td colspan="2" class="text-center">' . round($jumlah_total / 6, 2) . '</td></tr>
							<tr><td colspan="2">Nilai Ujian/Sidang Tugas Akhir</td><td colspan="2" class="text-center">' . $this->nilai_mutu_lama(round($jumlah_total / 6, 2)) . '</td></tr>
						</tbody>
						</table>';
			$data->nilai_mutu = $this->nilai_mutu_lama(round($jumlah_total / 6, 2));
		} else {
			$tbl_nilai .= '
									<th class="text-center">Bobot (%)</th>
									<th class="text-center">N * Bobot</th>
								</tr>
							</thead>
						<tbody>
							<tr>
								<td>Seminar Proposal</td><td class="text-center">' . $round_nilai_sempro . '</td><td class="text-center">10%</td><td class="text-center">' . ($round_nilai_sempro * 0.1) . '</td>
							</tr>
							<tr>
								<td>Seminar Hasil</td><td class="text-center">' . $round_nilai_semhas . '</td><td class="text-center">10%</td><td class="text-center">' . ($round_nilai_semhas * 0.1) . '</td>
							</tr>
							<tr>
								<td>Ujian/Sidang Tugas Akhir</td><td class="text-center">' . $nilai_rata . '</td><td class="text-center">80%</td><td class="text-center">' . ($nilai_rata * 0.8) . '</td>
							</tr>
							<tr><td colspan="2">Total (Skala 7)</td><td class="text-center">100%</td><td class="text-center">' . round($jumlah_total, 2) . '</td></tr>
							<tr><td colspan="2">Nilai Skala 100 ((Total/7)*100)</td><td colspan="2" class="text-center">' . round($nilai_skala_100, 2) . '</td></tr>
							<tr><td colspan="2">Nilai Ujian/Sidang Tugas Akhir</td><td colspan="2" class="text-center">' . $this->nilai_mutu($nilai_skala_100) . '</td></tr>
						</tbody>
						</table>';
			$data->nilai_mutu = $this->nilai_mutu($nilai_skala_100);
		}
		
		$data->tbl_nilai = $tbl_nilai;

		$legend_html = '<tr><td colspan="2">KETERANGAN NILAI :</td></tr>';
		if ($is_lulus) {
			$legend_html .= '<tr><td>> 6,51 = A</td><td>4,51 – 5,50 = C+</td></tr>';
			$legend_html .= '<tr><td>6,01 – 6,50 = B+</td><td>3,51 – 4,50 = C</td></tr>';
			$legend_html .= '<tr><td>5,51 – 6,00 = B</td><td><=3,50 = Tidak lulus</td></tr>';
		} else {
			$keterangan_nilai_raw = $this->db->order_by('range_atas', 'DESC')->get('siakad_nilai_huruf')->result();
			$keterangan_nilai = [];
			$tidak_lulus_added = false;
			foreach ($keterangan_nilai_raw as $k) {
				if (in_array(trim((string)$k->nilai_huruf), ['D', 'E'])) {
					if (!$tidak_lulus_added) {
						$k->range_bawah = '0.0';
						$k->range_atas = '55.9';
						$k->nilai_huruf = 'Tidak Lulus';
						$keterangan_nilai[] = $k;
						$tidak_lulus_added = true;
					}
				} else {
					$keterangan_nilai[] = $k;
				}
			}
			$half = ceil(count($keterangan_nilai) / 2);
			$chunks = array_chunk($keterangan_nilai, $half > 0 ? $half : 1);
			$left = $chunks[0] ?? [];
			$right = $chunks[1] ?? [];
			$max_count = max(count($left), count($right));
			for ($i = 0; $i < $max_count; $i++) {
				$l_text = isset($left[$i]) ? ($left[$i]->range_bawah . ' - ' . $left[$i]->range_atas . ' = ' . $left[$i]->nilai_huruf) : '';
				$r_text = isset($right[$i]) ? ($right[$i]->range_bawah . ' - ' . $right[$i]->range_atas . ' = ' . $right[$i]->nilai_huruf) : '';
				$legend_html .= "<tr><td>{$l_text}</td><td>{$r_text}</td></tr>";
			}
		}
		$data->legend_html = $legend_html;

		$data->nilai_rata = $nilai_rata;
		$data->hasil_sidang = $hasil_sidang;

		echo json_encode($data);
	}


	public function ajax_update()
	{
		$this->_validate();
		$sidang_nilai_akhir = 0.25 * $this->input->post('sidang_nilai_a') +
			0.15 * $this->input->post('sidang_nilai_b') +
			0.25 * $this->input->post('sidang_nilai_c') +
			0.25 * $this->input->post('sidang_nilai_d') +
			0.1 * $this->input->post('sidang_nilai_e');
		$data = [
			'sidang_nilai_a' => $this->input->post('sidang_nilai_a'),
			'sidang_nilai_b' => $this->input->post('sidang_nilai_b'),
			'sidang_nilai_c' => $this->input->post('sidang_nilai_c'),
			'sidang_nilai_d' => $this->input->post('sidang_nilai_d'),
			'sidang_nilai_e' => $this->input->post('sidang_nilai_e'),
			'sidang_nilai_akhir' => $sidang_nilai_akhir,
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];
		$this->E_skripsi_sidang_model->update(['id_nilai_sempro' => $this->input->post('id_nilai_sempro')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update_catatan()
	{
		//$this->_validate();

		$data = [
			'sidang_uraian_revisi' => $this->input->post('sidang_uraian_revisi'),
			'change_by' => $this->ion_auth->user()->row()->email,
			'change_date' => date('Y-m-d H:i:s')
		];
		$this->E_skripsi_sidang_model->update(['id_nilai_sempro' => $this->input->post('id_nilai_sempro')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update_berita_acara()
	{
		$this->_validate_berita_acara();
		$data = '';
		if ($this->input->post('sidang_hasil') == 'LULUS') {
			$data = [
				'sidang_hari' => $this->input->post('sidang_hari'),
				'sidang_tanggal' => $this->input->post('sidang_tanggal'),
				'sidang_hasil' => $this->input->post('sidang_hasil'),
				'sidang_predikat' => $this->input->post('sidang_predikat'),
				'sidang_nilai_mutu' => $this->input->post('sidang_nilai_mutu'),
				'sidang_kembali_mengulang' => NULL,
				'sidang_tanggal_mengulang' => NULL,
				'change_by' => $this->ion_auth->user()->row()->email,
				'change_date' => date('Y-m-d H:i:s')
			];
		} else {
			$data = [
				'sidang_hari' => $this->input->post('sidang_hari'),
				'sidang_tanggal' => $this->input->post('sidang_tanggal'),
				'sidang_hasil' => $this->input->post('sidang_hasil'),
				'sidang_predikat' => NULL,
				'sidang_nilai_mutu' => $this->input->post('sidang_nilai_mutu'),
				'sidang_kembali_mengulang' => $this->input->post('sidang_kembali_mengulang'),
				//'sidang_tanggal_mengulang' => $this->input->post('sidang_tanggal_mengulang'),
				'change_by' => $this->ion_auth->user()->row()->email,
				'change_date' => date('Y-m-d H:i:s')
			];
		}

		$this->E_skripsi_sidang_model->update(['id_nilai_sempro' => $this->input->post('id_nilai_sempro')], $data);
		echo json_encode(["status" => TRUE]);
	}



	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('sidang_nilai_a') == '') {
			$data['inputerror'][] = 'sidang_nilai_a';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_a') < 1) {
			$data['inputerror'][] = 'sidang_nilai_a';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_a') > 7) {
			$data['inputerror'][] = 'sidang_nilai_a';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_a') == 4) {
			$data['inputerror'][] = 'sidang_nilai_a';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('sidang_nilai_b') == '') {
			$data['inputerror'][] = 'sidang_nilai_b';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_b') < 1) {
			$data['inputerror'][] = 'sidang_nilai_b';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_b') > 7) {
			$data['inputerror'][] = 'sidang_nilai_b';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_b') == 4) {
			$data['inputerror'][] = 'sidang_nilai_b';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('sidang_nilai_c') == '') {
			$data['inputerror'][] = 'sidang_nilai_c';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_c') < 1) {
			$data['inputerror'][] = 'sidang_nilai_c';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_c') > 7) {
			$data['inputerror'][] = 'sidang_nilai_c';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_c') == 4) {
			$data['inputerror'][] = 'sidang_nilai_c';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('sidang_nilai_d') == '') {
			$data['inputerror'][] = 'sidang_nilai_d';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_d') < 1) {
			$data['inputerror'][] = 'sidang_nilai_d';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_d') > 7) {
			$data['inputerror'][] = 'sidang_nilai_d';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_d') == 4) {
			$data['inputerror'][] = 'sidang_nilai_d';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('sidang_nilai_e') == '') {
			$data['inputerror'][] = 'sidang_nilai_e';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_e') < 1) {
			$data['inputerror'][] = 'sidang_nilai_e';
			$data['error_string'][] = 'Nilai tidak boleh di bawah 1.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_e') > 7) {
			$data['inputerror'][] = 'sidang_nilai_e';
			$data['error_string'][] = 'Nilai tidak boleh di atas 7.';
			$data['status'] = FALSE;
		} elseif ($this->input->post('sidang_nilai_e') == 4) {
			$data['inputerror'][] = 'sidang_nilai_e';
			$data['error_string'][] = 'Nilai 4 tidak bisa dipilih.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_berita_acara()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('sidang_hari') == '') {
			$data['inputerror'][] = 'sidang_hari';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}


		if ($this->input->post('sidang_tanggal') == '') {
			$data['inputerror'][] = 'sidang_tanggal';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function nim_check($nipd)
	{
		$is_check = $this->db->where('nipd', $nipd)->get('mahasiswa_pt')->num_rows();
		if ($is_check == 0) {
			$this->form_validation->set_message('nim_check', '{field} ' . $nipd . ' tidak ada atau tidak valid');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function cetak_pdf($enkrip)
	{
		// Disable error reporting so that warnings/notices do not corrupt the PDF file signature
		error_reporting(0);
		ini_set('display_errors', 0);

		$this->load->model('Qr_model');
		try {
			$judul = $this->Qr_model->judul_berita_acara($enkrip);
			$id_sms = $judul->id_sms;
			$id_smt = $this->session->userdata('tahun_ajaran');
			$nilai_sidang = $this->Qr_model->nilai_sidang($judul);
			$data_sidang = $this->Qr_model->data_sidang($judul);
			$penguji_sidang_1 = $data_sidang->penguji_sidang_1;
			$penguji_sidang_2 = $data_sidang->penguji_sidang_2;
			$penguji_sidang_3 = $data_sidang->penguji_sidang_3;

			$penguji_1 = $this->Qr_model->penguji_sidang($penguji_sidang_1);
			$penguji_2 = $this->Qr_model->penguji_sidang($penguji_sidang_2);
			$penguji_3 = (!empty($penguji_sidang_3) && $penguji_sidang_3 != '0') ? $this->Qr_model->penguji_sidang($penguji_sidang_3) : null;

			$nilai_penguji_1 = $this->Qr_model->nilai_penguji($penguji_sidang_1, $judul);
			$nilai_penguji_2 = $this->Qr_model->nilai_penguji($penguji_sidang_2, $judul);
			$nilai_penguji_3 = (!empty($penguji_sidang_3) && $penguji_sidang_3 != '0') ? $this->Qr_model->nilai_penguji($penguji_sidang_3, $judul) : null;

			// Cek apakah ada dosen pengganti yang menilai tapi tidak terupdate di siakad_skripsi_seminar_sidang
			$semua_nilai = $this->db->where('id_judul_skripsi', $judul->id_judul_skripsi)->get('siakad_skripsi_nilai_sempro')->result();
			$penilai_aktual = [];
			foreach($semua_nilai as $n) {
				if(floatval($n->sidang_nilai_akhir) > 0) {
					$penilai_aktual[] = $n;
				}
			}
			
			$req_penguji = (!empty($penguji_sidang_3) && $penguji_sidang_3 != '0') ? 3 : 2;
			
			if (count($penilai_aktual) < $req_penguji) {
				$msg = "Gagal cetak PDF: Semua dosen penguji ($req_penguji) harus mengisi nilai sidang terlebih dahulu! (Baru " . count($penilai_aktual) . " yang mengisi)";
				echo "<script>alert('$msg'); window.close();</script>";
				return;
			}

			// Jika penguji 3 ditugaskan, tapi nilainya kosong (karena ID beda/pengganti), kita ambil dari data aktual
			if ($req_penguji == 3 && empty($nilai_penguji_3)) {
				// Cari penilai aktual yang bukan penguji 1 dan bukan penguji 2
				foreach($penilai_aktual as $n) {
					if ($n->id_penilai != $penguji_sidang_1 && $n->id_penilai != $penguji_sidang_2) {
						$penguji_sidang_3 = $n->id_penilai;
						$penguji_3 = $this->Qr_model->penguji_sidang($penguji_sidang_3);
						$nilai_penguji_3 = $this->Qr_model->nilai_penguji($penguji_sidang_3, $judul);
						break;
					}
				}
			}

			$usulan_semhas = $this->Qr_model->usulan($judul, $id_sms);
			$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt);
			$tanggal_qr = tgl_short($judul->sidang_tanggal);
			$data_penguji_1 = $penguji_1->nama_lengkap . ' (' . $tanggal_qr . ')';
			$qr_penguji_1 = $this->Qr_model->create_qr($data_penguji_1, $penguji_1->nidn);

			$data_penguji_2 = $penguji_2->nama_lengkap . ' (' . $tanggal_qr . ')';
			$qr_penguji_2 = $this->Qr_model->create_qr($data_penguji_2, $penguji_2->nidn);

			$qr_penguji_3 = '';
			if (!empty($penguji_3)) {
				$data_penguji_3 = $penguji_3->nama_lengkap . ' (' . $tanggal_qr . ')';
				$qr_penguji_3 = $this->Qr_model->create_qr($data_penguji_3, $penguji_3->nidn);
			}
			$data_kaprodi = $nama_kaprodi->nm_pejabat_khs_1;
			$qr_kaprodi = $this->Qr_model->create_qr($data_kaprodi, $nama_kaprodi->nidn_pejabat_khs_1);



			$id_judul_skripsi = $judul->id_judul_skripsi;
			$nilai_rata_query = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(sidang_nilai_akhir) as sidang_nilai_akhir')->from('siakad_skripsi_nilai_sempro')->get()->row();
			$nilai_rata = round($nilai_rata_query->sidang_nilai_akhir, 2);

			$nilai_sempro = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(nilai_akhir) as nilai_sempro')->from('siakad_skripsi_nilai_sempro')->get()->row();
			$round_nilai_sempro = round($nilai_sempro->nilai_sempro, 2);

			$nilai_semhas = $this->db->where('id_judul_skripsi', $id_judul_skripsi)->select('avg(semhas_nilai_akhir) as nilai_semhas')->from('siakad_skripsi_nilai_sempro')->get()->row();
			$round_nilai_semhas = round($nilai_semhas->nilai_semhas, 2);

			$jumlah_total = ($round_nilai_sempro * 1) + ($round_nilai_semhas * 1) + ($nilai_rata * 4 * 1);

			$output['judul'] = $judul;
			$output['dosen_nama'] = $this->Qr_model->nama_dosen_pembimbing($judul);
			$output['nilai_penguji_1'] = $nilai_penguji_1;
			$output['nilai_penguji_2'] = $nilai_penguji_2;
			$output['nilai_penguji_3'] = $nilai_penguji_3;
			$output['penguji_1'] = $penguji_1;
			$output['penguji_2'] = $penguji_2;
			$output['penguji_3'] = $penguji_3;
			$output['id_sms'] = $id_sms;
			$output['usulan_semhas'] = $usulan_semhas;
			$output['nama_kaprodi'] = $nama_kaprodi;
			$output['qr_penguji_1'] = !empty($qr_penguji_1) ? (str_starts_with($qr_penguji_1, 'data:') ? $qr_penguji_1 : FCPATH  . "assets/qrcode/" . $qr_penguji_1) : '';
			$output['qr_penguji_2'] = !empty($qr_penguji_2) ? (str_starts_with($qr_penguji_2, 'data:') ? $qr_penguji_2 : FCPATH  . "assets/qrcode/" . $qr_penguji_2) : '';
			$output['qr_penguji_3'] = !empty($qr_penguji_3) ? (str_starts_with($qr_penguji_3, 'data:') ? $qr_penguji_3 : FCPATH  . "assets/qrcode/" . $qr_penguji_3) : '';
			$output['qr_kaprodi'] = !empty($qr_kaprodi) ? (str_starts_with($qr_kaprodi, 'data:') ? $qr_kaprodi : FCPATH  . "assets/qrcode/" . $qr_kaprodi) : '';
			$output['rata_rata'] = round($nilai_rata, 2) * 1;
			$output['jumlah_total'] = round($jumlah_total, 2) * 1;
			$output['nilai_sempro'] = $round_nilai_sempro * 1;
			$output['nilai_semhas'] = $round_nilai_semhas * 1;
			$output['nilai_mutu'] = $this->nilai_mutu(round($jumlah_total / 6, 2));
			$output['berita_acara'] = $this->Qr_model->berita_acara($judul);

			$html = $this->load->view('cetak/pdf_sidang', $output, true);
			
			$pdfFilePath  = 'Sidang_tugas_akhir_' . $judul->nipd . '.pdf';

			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'margin_top' => 30, 'margin_left' => 30, 'margin_right' => 15, 'margin_bottom' => 35]);
			$mpdf->SetHTMLHeader($this->Qr_model->header());
			$mpdf->SetHTMLFooter($this->Qr_model->footer());

			$mpdf->setAutoTopMargin = 'stretch';
			$mpdf->setAutoBottomMargin = 'pad';

			$mpdf->WriteHTML($html);
			
			// Clear any PHP warnings, notices, or trailing spaces from output buffer
			// before sending the PDF stream to prevent 'Failed to load PDF document' errors.
			if (ob_get_length()) {
				file_put_contents(FCPATH . 'ob_log.txt', ob_get_contents());
				ob_clean();
			}
			
			$mpdf->Output($pdfFilePath, "I");
		} catch (\Throwable $th) {
			file_put_contents(FCPATH . 'mpdf_error.txt', $th->__toString());
			echo "Terjadi Error mPDF. Silakan cek mpdf_error.txt";
		}
	}


	private function nilai_mutu($nilai_skala_100)
	{
		$query = $this->db->where('range_bawah <=', $nilai_skala_100)
						  ->where('range_atas >=', $nilai_skala_100)
						  ->get('siakad_nilai_huruf');
		if ($query->num_rows() > 0) {
			$huruf = trim($query->row()->nilai_huruf);
			if (in_array($huruf, ['D', 'E'])) {
				return "Tidak Lulus";
			}
			return $huruf;
		}
		return "Tidak Lulus"; // Atau nilai default jika diluar range
	}

	private function nilai_mutu_lama($ratarata)
	{
		if ($ratarata >= 6.51) {
			return "A";
		} elseif ($ratarata >= 6.01 and $ratarata <= 6.5) {
			return "B+";
		} elseif ($ratarata >= 5.51 and $ratarata <= 6) {
			return "B";
		} elseif ($ratarata >= 4.51 and $ratarata <= 5.5) {
			return "C+";
		} elseif ($ratarata >= 3.51 and $ratarata <= 4.5) {
			return "C";
		} elseif ($ratarata <= 3.5) {
			return "Tidak Lulus";
		}
	}

	public function search()
	{
		$nim = $this->input->post('query');
		$data_nim = $this->db->like('nipd', $nim, 'AFTER')->limit(10)->get('mahasiswa_pt');

		$output = '';
		$output = '<ul>';

		if ($data_nim->num_rows() > 0) {
			foreach ($data_nim->result() as $key => $value) {
				$output .= '<li onClick="selectNIM(' . $value->nipd . ')">' . $value->nipd . '</li>';
			}
		}
		$output .= '</ul>';
		echo $output;
	}
}
