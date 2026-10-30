<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_notifikasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_notifikasi_model');
		$this->load->model('Qr_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus login sebagai dosen.');
			redirect('auth/logout', 'refresh');
		}

		/* $group = array(5);
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		} */
	}

	public function index()
	{
		/* $id_ptk = $this->session->userdata('id_ptk');
		$this->db->where('id_ptk', $id_ptk)->update('siakad_skripsi_notifikasi', ['is_read' => 1]); */
		$output['judul_besar'] = 'Notifikasi ';
		$output['judul_kecil'] = 'Tugas Akhir WBI';
		$output['menu_aktif'] = 'm_notifikasi';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'e_skripsi_notifikasi/e_skripsi_notifikasi_view';
		$output['after_page'] = 'e_skripsi_notifikasi/e_skripsi_notifikasi_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{

		$list = $this->E_skripsi_notifikasi_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$linknya = $jadwal = '-';
			$kompare_tanggal = 0;
			if (!empty($rg->id_referensi_seminar_sidang)) {
				$link_dokumen = $this->db->where('uniq', $rg->id_referensi_seminar_sidang)->get('siakad_skripsi_seminar_sidang')->row();

				$tanggal_seminar = !empty($link_dokumen->tanggal_seminar) ? tgl_indo($link_dokumen->tanggal_seminar) : '-';
				$jam = !empty($link_dokumen->jam) ? $link_dokumen->jam : '-';
				$tempat = !empty($link_dokumen->tempat) ? $link_dokumen->tempat : '';
				$jadwal = '<small>Tgl : ' . $tanggal_seminar . '<br>Jam : ' . $jam . '<br>Ruang : ' . $tempat;
				$link = !empty($link_dokumen->link_dokumen) ? $link_dokumen->link_dokumen : '-';

				//$linknya = $this->convertURLs($link_dokumen);
				if ($link <> '-') {
					$linknya = '<a href="' . $link_dokumen->link_dokumen . '" class="btn btn-default btn-sm green-stripe" target="_new"> <i class="fa fa-download"></i> <small>Dokumen TA</small></a>';
				} else {
					$linknya = '-';
				}

				$t1 = strtotime($tanggal_seminar);
				$t2 = strtotime(date('Y-m-d H:i:s'));

				if ($t1 > $t2) {
					$kompare_tanggal = 1;
				}
			}

			$no++;
			$row = [];
			$row[] = $no;

			$row[] = $jadwal;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $linknya;
			if ($rg->is_read == 0) {
				$class_icon = 'class="btn btn-default btn-sm red-stripe"><i class="fa fa-envelope-o "></i><small> ' . $rg->jenis_notif . ' (Unread)</small>';
			} else {
				$class_icon = 'class="btn btn-default btn-sm green-stripe"><i class="icon-envelope-open"></i><small> ' . $rg->jenis_notif . '</small>';
			}

			if ($rg->kategori_cetak == 'SK_pembimbing') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_sk/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" 
				' . $class_icon . '</a>';
			} elseif ($rg->kategori_cetak == 'surat_tugas') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_surat_tugas/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" 
				' . $class_icon . '</a>';
			} elseif ($rg->kategori_cetak == 'undangan_sempro') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" 
				' . $class_icon . '</a>';
			} elseif ($rg->kategori_cetak == 'undangan_semhas') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_semhas/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" 
				' . $class_icon . '</a>';
			} elseif ($rg->kategori_cetak == 'undangan_sidang') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_sidang/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" 
				' . $class_icon . '</a>';
			}

			$tgl_notif = tgl_indo(date('Y-m-d', strtotime($rg->tgl_notifikasi)));
			$jam_notif = date('H:i:s', strtotime($rg->tgl_notifikasi));
			$row[] = '<small>' . $tgl_notif . '<br>' . $jam_notif . '</small>';
			$row[] = $kompare_tanggal;
			$row[] = $rg->is_read;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_notifikasi_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_notifikasi_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function update_telah_dibaca($id_notifikasi)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$this->db->where(['id_notifikasi' => $id_notifikasi])->update('siakad_skripsi_notifikasi', ['is_read' => 1]);
	}

	public function cetak_sk($id_judul_skripsi, $id_notifikasi)
	{
		$this->update_telah_dibaca($id_notifikasi);
		$this->load->library('table');
		$judul = $this->Qr_model->judul($id_judul_skripsi);
		$id_sms = $judul->id_sms;

		if ($judul->tanggal_sk == '' or $judul->nomor_sk == '') {
			$pesan =  '<div class="note note-success note-shadow">
						<h4 class="block">Ada Kesalahan Data</h4>
						<p>
							 Tanggal dan Nomor Surat Keputusan belum ada.
						</p>
					</div>';

			$output['pesan'] = $pesan;
			$output['judul_besar'] = 'Notifikasi ';
			$output['judul_kecil'] = 'Tugas Akhir WBI';
			$output['menu_aktif'] = 'm_notifikasi';
			$output['parent_skripsi'] = 'active open';
			$output['content'] = 'e_skripsi_notifikasi/e_skripsi_notifikasi_no_page';
			$output['after_page'] = 'e_skripsi_notifikasi/e_skripsi_notifikasi_after_page';
			$this->load->view('master_page', $output);
		} else {
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
			$output['judul'] = $judul;

			$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
			$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
			$penguji = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
			$penguji_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);

			$tanggal_qr = tgl_short($judul->tanggal_sk);
			$datanya = DIREKTUR . ' (' . $tanggal_qr . ')';

			$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
			$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;

			$tmpl = $this->Qr_model->template_tabel();

			$this->table->set_template($tmpl);
			$this->table->set_heading(['NIDN', 'NAMA DOSEN', 'JABATAN AKADEMIK', 'KETERANGAN']);

			if (!empty($doping_1)) {
				$this->table->add_row([
					['data' => $doping_1->nidn, 'class' => 'text-right'],
					['data' => $doping_1->nama_lengkap, 'class' => 'text-left'],
					['data' => $doping_1->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Pembimbing Utama', 'class' => 'text-left'],
				]);
			}

			if (!empty($doping_2)) {
				$this->table->add_row([
					['data' => $doping_2->nidn, 'class' => 'text-right'],
					['data' => $doping_2->nama_lengkap, 'class' => 'text-left'],
					['data' => $doping_2->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Pembimbing Pendamping', 'class' => 'text-left'],
				]);
			}

			/*if (!empty($penguji)) {
				$this->table->add_row(array(
					array('data' => $penguji->nidn, 'class' => 'text-right'),
					array('data' => $penguji->nama_lengkap, 'class' => 'text-left'),
					array('data' => $penguji->jabatan_akademik, 'class' => 'text-left'),
					array('data' => 'Penguji', 'class' => 'text-left'),
				));
			}

			if (!empty($penguji_2)) {
				$this->table->add_row(array(
					array('data' => $penguji_2->nidn, 'class' => 'text-right'),
					array('data' => $penguji_2->nama_lengkap, 'class' => 'text-left'),
					array('data' => $penguji_2->jabatan_akademik, 'class' => 'text-left'),
					array('data' => 'Penguji', 'class' => 'text-left'),
				));
			} */

			$myTable =  $this->table->generate();
			$output['myTable'] = $myTable;
			$output['id_sms'] = $id_sms;

			$html = $this->load->view('cetak/pdf_sk_pembimbing', $output, true);
			$pdfFilePath  = 'sk_pembimbing_' . $judul->nipd . '.pdf';

			$mpdf->SetHTMLHeader($this->Qr_model->header());
			$mpdf->SetHTMLFooter($this->Qr_model->footer());

			$mpdf->WriteHTML($html);
			$mpdf->Output($pdfFilePath, "I");
		}
	}

	public function cetak_surat_tugas($id_judul_skripsi, $id_notifikasi)
	{
		$this->update_telah_dibaca($id_notifikasi);
		$judul = $this->Qr_model->judul($id_judul_skripsi);
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

		$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
		$penguji_sempro_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);

		$tanggal_qr = tgl_short($judul->tanggal_sk);
		$datanya = DIREKTUR . ' (' . $tanggal_qr . ')';

		$output['qr_name'] = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$this->load->library('table');

		$tmpl = $this->Qr_model->template_tabel();

		$this->table->set_template($tmpl);
		$this->table->set_heading(['NO', 'NIDN', 'NAMA DOSEN']);

		if ($judul->dosen_pembimbing_1 <> '' and $judul->dosen_pembimbing_2 <> '') {
			$penguji_sempro_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);
			if (!empty($penguji_sempro_3)) {
				$this->table->add_row([
					['data' => '1.', 'class' => 'text-center'],
					['data' => $penguji_sempro_3->nidn, 'class' => 'text-center'],
					['data' => $penguji_sempro_3->nama_lengkap, 'class' => 'text-left'],

				]);
			}
		} elseif ($judul->dosen_pembimbing_1 <> '') {
			$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
			$penguji_sempro_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);
			if (!empty($penguji_sempro_2)) {
				$this->table->add_row([
					['data' => "1.", 'class' => 'text-center'],
					['data' => $penguji_sempro_2->nidn, 'class' => 'text-center'],
					['data' => $penguji_sempro_2->nama_lengkap, 'class' => 'text-left'],
				]);
			}

			if (!empty($penguji_sempro_3)) {
				$this->table->add_row([
					['data' => '2.', 'class' => 'text-center'],
					['data' => $penguji_sempro_3->nidn, 'class' => 'text-center'],
					['data' => $penguji_sempro_3->nama_lengkap, 'class' => 'text-left'],

				]);
			}
		}

		$myTable =  $this->table->generate();

		$output['judul'] = $judul;
		$output['myTable'] = $myTable;

		$html = $this->load->view('cetak/pdf_surat_tugas', $output, true);
		$pdfFilePath  = 'surat_tugas_penguji_sempro_' . $judul->nipd . '.pdf';

		$mpdf->SetHTMLHeader($this->Qr_model->header());
		$mpdf->SetHTMLFooter($this->Qr_model->footer());

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_undangan($id_judul_skripsi, $id_notifikasi)
	{
		$this->update_telah_dibaca($id_notifikasi);
		$judul = $this->Qr_model->judul_undangan($id_judul_skripsi, 1);
		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('tahun_ajaran');

		$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt)->nm_pejabat_khs_1;

		//$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
		//$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
		$penguji_sempro_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);

		$tanggal_qr = tgl_short($judul->tanggal_undangan);
		$datanya =  $nama_kaprodi . " (" . $tanggal_qr . ")";

		$output['judul'] = $judul;
		$output['qr_name'] = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;
		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = $output['penguji_sempro_3'] = '';

		/* if (!empty($penguji_sempro_1)) {
			$output['doping_1'] = $penguji_sempro_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['doping_2'] = $penguji_sempro_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		} */

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji)';
		}

		if (!empty($penguji_sempro_3)) {
			$output['penguji_sempro_3'] = $penguji_sempro_3->nama_lengkap . ' (Penguji)';
		}



		$html = $this->load->view('cetak/pdf_cetak_undangan_sempro', $output, true);
		$pdfFilePath  = 'undangan_sempro_' . $judul->nipd . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetHTMLHeader($this->Qr_model->header());
		$mpdf->SetHTMLFooter($this->Qr_model->footer());

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_undangan_semhas($id_judul_skripsi, $id_notifikasi)
	{
		$this->update_telah_dibaca($id_notifikasi);
		$judul = $this->Qr_model->judul_undangan($id_judul_skripsi, 2);
		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt)->nm_pejabat_khs_1;

		//$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
		//$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);
		$penguji_sempro_3 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_3);

		$tanggal_qr = tgl_short($judul->tanggal_undangan);
		$datanya = $nama_kaprodi . " (" . $tanggal_qr . ")";

		$this->load->model('Qr_model');
		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;

		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = $output['penguji_sempro_3'] = '';

		/* if (!empty($doping_1)) {
			$output['doping_1'] = $doping_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($doping_2)) {
			$output['doping_2'] = $doping_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		} */

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji)';
		}

		if (!empty($penguji_sempro_3)) {
			$output['penguji_sempro_3'] = $penguji_sempro_3->nama_lengkap . ' (Penguji)';
		}

		$output['judul'] = $judul;

		$html = $this->load->view('cetak/pdf_cetak_undangan_semhas', $output, true);
		$pdfFilePath  = 'undangan_semhas_' . $judul->nipd . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetHTMLHeader($this->Qr_model->header());
		$mpdf->SetHTMLFooter($this->Qr_model->footer());

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_undangan_sidang($id_judul_skripsi, $id_notifikasi)
	{
		$this->update_telah_dibaca($id_notifikasi);
		$judul = $this->Qr_model->judul_undangan($id_judul_skripsi, 3);
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('tahun_ajaran');

		$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt)->nm_pejabat_khs_1;

		$penguji_sidang_1 = $this->Qr_model->nama_dosen($judul->penguji_sidang_1);
		$penguji_sidang_2 = $this->Qr_model->nama_dosen($judul->penguji_sidang_2);
		$penguji_sidang_3 = $this->Qr_model->nama_dosen($judul->penguji_sidang_3);

		$tanggal_qr = tgl_short($judul->tanggal_undangan);
		$datanya = $nama_kaprodi . " (" . $tanggal_qr . ")";

		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$output['judul'] = $judul;
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;
		$output['nama_kaprodi'] = $nama_kaprodi;
		$output['penguji_sidang_1'] = $output['penguji_sidang_2'] = $output['penguji_sidang_3'] = '';

		if (!empty($penguji_sidang_1)) {
			$output['nm_penguji_sidang_1'] = $penguji_sidang_1->nama_lengkap . ' (Penguji I)';
		}

		if (!empty($penguji_sidang_2)) {
			$output['nm_penguji_sidang_2'] = $penguji_sidang_2->nama_lengkap . ' (Penguji II)';
		}

		if (!empty($penguji_sidang_3)) {
			$output['nm_penguji_sidang_3'] = $penguji_sidang_3->nama_lengkap . ' (Penguji III)';
		}

		$this->load->library('table');
		$tmpl = $this->Qr_model->template_tabel();

		$this->table->set_template($tmpl);
		$this->table->set_heading(['NIDN', 'NAMA DOSEN', 'JABATAN AKADEMIK', 'KETERANGAN']);

		if (!empty($penguji_sidang_1)) {
			$this->table->add_row([
				['data' => $penguji_sidang_1->nidn, 'class' => 'text-right'],
				['data' => $penguji_sidang_1->nama_lengkap, 'class' => 'text-left'],
				['data' => $penguji_sidang_1->jabatan_akademik, 'class' => 'text-left'],
				['data' => 'PENGUJI 1', 'class' => 'text-left'],
			]);
		}

		if (!empty($penguji_sidang_2)) {
			$this->table->add_row([
				['data' => $penguji_sidang_2->nidn, 'class' => 'text-right'],
				['data' => $penguji_sidang_2->nama_lengkap, 'class' => 'text-left'],
				['data' => $penguji_sidang_2->jabatan_akademik, 'class' => 'text-left'],
				['data' => 'PENGUJI 2', 'class' => 'text-left'],
			]);
		}

		if (!empty($penguji_sidang_3)) {
			$this->table->add_row([
				['data' => $penguji_sidang_3->nidn, 'class' => 'text-right'],
				['data' => $penguji_sidang_3->nama_lengkap, 'class' => 'text-left'],
				['data' => $penguji_sidang_3->jabatan_akademik, 'class' => 'text-left'],
				['data' => 'PENGUJI 3', 'class' => 'text-left'],
			]);
		}

		$myTable =  $this->table->generate();
		$output['table_penguji'] = $myTable;

		$html = $this->load->view('cetak/pdf_cetak_undangan_sidang', $output, true);
		$pdfFilePath  = 'undangan_sidang_' . $judul->nipd . '.pdf';

		$mpdf->SetHTMLHeader($this->Qr_model->header());
		$mpdf->SetHTMLFooter($this->Qr_model->footer());

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function convertURLs($string)
	{
		$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
		return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
	}
}
