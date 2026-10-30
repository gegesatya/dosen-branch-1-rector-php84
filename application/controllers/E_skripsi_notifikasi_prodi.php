<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_notifikasi_prodi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_notifikasi_prodi_model');
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
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$output['judul_besar'] = 'Notifikasi ';
		$output['judul_kecil'] = 'Tugas Akhir WBI';
		$output['menu_aktif'] = 'm_notifikasi_prodi';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = 'e_skripsi_notifikasi_prodi/e_skripsi_notifikasi_prodi_view';
		$output['after_page'] = 'e_skripsi_notifikasi_prodi/e_skripsi_notifikasi_prodi_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{

		$list = $this->E_skripsi_notifikasi_prodi_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {

			$tgl_notif = tgl_indo(date('Y-m-d', strtotime($rg->tgl_notifikasi)));
			$jam_notif = date('H:i:s', strtotime($rg->tgl_notifikasi));
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $tgl_notif . '<br>' . $jam_notif;
			$row[] = $rg->nm_ptk;
			$row[] = $rg->jenis_notif;
			$row[] = $rg->keterangan;

			if ($rg->kategori_cetak == 'SK_pembimbing') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_sk/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" class="btn btn-default btn-sm green-stripe">
				<i class="fa fa-download"></i> <small>Download</small></a>';
			} elseif ($rg->kategori_cetak == 'surat_tugas') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_surat_tugas/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" class="btn btn-default btn-sm green-stripe">
				<i class="fa fa-download"></i> <small>Download</small></a>';
			} elseif ($rg->kategori_cetak == 'undangan_sempro') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" class="btn btn-default btn-sm green-stripe">
				<i class="fa fa-download"></i> <small>Download</small></a>';
			} elseif ($rg->kategori_cetak == 'undangan_semhas') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_semhas/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" class="btn btn-default btn-sm green-stripe">
				<i class="fa fa-download"></i> <small>Download</small></a>';
			} elseif ($rg->kategori_cetak == 'undangan_sidang') {
				$row[] = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_sidang/') . md5($rg->id_judul_skripsi) . '/' . $rg->id_notifikasi . '" target="_new" class="btn btn-default btn-sm green-stripe">
				<i class="fa fa-download"></i> <small>Download</small></a>';
			}

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_notifikasi_prodi_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_notifikasi_prodi_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	/* public function cetak_sk($id_judul_skripsi)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('md5(j.id_judul_skripsi)', $id_judul_skripsi)->get()->row();

		$id_sms = $judul->id_sms;

		if ($judul->tanggal_sk == '' or $judul->nomor_sk == '') {
		} else {
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
			$output['judul'] = $judul;
			$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

			$datanya = "Nomor SK :" . $judul->nomor_sk . "\n" .
				"Tanggal SK : " . $judul->tanggal_sk . "\n" .
				"Ditandatangani Oleh: \nDr. Jenny Elisabeth \nDirektur";
			$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
			$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;


			$this->load->library('table');

			$tmpl = array(
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
			);

			$this->table->set_template($tmpl);
			$this->table->set_heading(array('NIDN', 'NAMA DOSEN', 'JABATAN AKADEMIK', 'KETERANGAN'));

			if (!empty($doping_1)) {
				$this->table->add_row(array(
					array('data' => $doping_1->nidn, 'class' => 'text-right'),
					array('data' => $doping_1->nama_lengkap, 'class' => 'text-left'),
					array('data' => $doping_1->jabatan_akademik, 'class' => 'text-left'),
					array('data' => 'Pembimbing Utama', 'class' => 'text-left'),
				));
			}

			if (!empty($doping_2)) {
				$this->table->add_row(array(
					array('data' => $doping_2->nidn, 'class' => 'text-right'),
					array('data' => $doping_2->nama_lengkap, 'class' => 'text-left'),
					array('data' => $doping_2->jabatan_akademik, 'class' => 'text-left'),
					array('data' => 'Pembimbing Pendamping', 'class' => 'text-left'),
				));
			}

			if (!empty($penguji)) {
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
			}

			$myTable =  $this->table->generate();
			$output['myTable'] = $myTable;
			$output['id_sms'] = $id_sms;

			$html = $this->load->view('cetak/pdf_sk_pembimbing', $output, true);
			$pdfFilePath  = 'sk_penetapan_judul_' . $judul->nipd . '.pdf';

			$mpdf->SetHTMLHeader('
		<div style="position: absolute; right:40; top: 20;">
		<img src="' . FCPATH  . "assets/image_web/kop_wbi.png" . '" width="250px" />
		</div>');
			$mpdf->SetHTMLFooter('
		<div style="font-size: 11px; color: #006a6f ;bottom:10;left:140;position:absolute">
		<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
		<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
		Kabupaten Deli Serdang – Sumatera Utara<br>
		(061) 42074111; Laman : www.wbi.ac.id<br>
		</p>
 		 </div>');

			$mpdf->WriteHTML($html);
			$mpdf->Output($pdfFilePath, "I");
		}
	} */

	/* public function cetak_surat_tugas($id_judul_skripsi)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('md5(j.id_judul_skripsi)', $id_judul_skripsi)->get()->row();
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$output['judul'] = $judul;
		$penguji_sempro_1 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		$datanya = "Nomor :" . $judul->no_surat_tugas . "\n" .
			"Tanggal : " . $judul->tanggal_surat_tugas . "\n" .
			"Ditandatangani Oleh: \nDr. Jenny Elisabeth \nDirektur";
		$output['qr_name'] = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$this->load->library('table');

		$tmpl = array(
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
		);

		$this->table->set_template($tmpl);
		$this->table->set_heading(array('NO', 'NIDN', 'NAMA DOSEN'));



		if (!empty($penguji_sempro_1)) {
			$this->table->add_row(array(
				array('data' => '1.', 'class' => 'text-center'),
				array('data' => $penguji_sempro_1->nidn, 'class' => 'text-center'),
				array('data' => $penguji_sempro_1->nama_lengkap, 'class' => 'text-left'),
			));
		}

		if (!empty($penguji_sempro_2)) {
			$this->table->add_row(array(
				array('data' => '2.', 'class' => 'text-center'),
				array('data' => $penguji_sempro_2->nidn, 'class' => 'text-center'),
				array('data' => $penguji_sempro_2->nama_lengkap, 'class' => 'text-left'),

			));
		}

		$myTable =  $this->table->generate();
		$output['myTable'] = $myTable;

		$html = $this->load->view('cetak/pdf_surat_tugas', $output, true);
		$pdfFilePath  = 'surat_tugas_penguji_sempro_' . $judul->nipd . '.pdf';

		$mpdf->SetHTMLHeader('
		<div style="position: absolute; right:40; top: 20;">
		<img src="' . FCPATH  . "assets/image_web/kop_wbi.png" . '" width="250px" />
		</div>');
		$mpdf->SetHTMLFooter('
		<div style="font-size: 12px; color: #009999 ;bottom:10;position:absolute">
		<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
		<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
		Kabupaten Deli Serdang – Sumatera Utara<br>
		(061) 42074111; Laman : www.wbi.ac.id<br>
		</p>
 		 </div>');

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	} */

	/* public function cetak_undangan($uniq)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->where('md5(ss.uniq)', $uniq)->get()->row();
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$output['judul'] = $judul;

		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('tahun_ajaran');
		$nama_kaprodi = $this->db->where(['id_sms' => $id_sms, 'id_smt' => $id_smt])->get('siakad_administrasi_prodi')->row()->nm_pejabat_khs_1;

		$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_1 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		$datanya = "Nomor :" . $judul->undangan_nomor . "\n" .
			"Tanggal : " . $judul->undangan_tanggal . "\n" .
			"Ditandatangani Oleh: \n" . $nama_kaprodi . " \nDirektur";
		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;
		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = '';

		if (!empty($doping_1)) {
			$output['doping_1'] = $doping_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($doping_2)) {
			$output['doping_2'] = $doping_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		}

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Penguji)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Penguji)';
		}



		$html = $this->load->view('cetak/pdf_cetak_undangan_sempro', $output, true);
		$pdfFilePath  = 'undangan_sempro_' . $judul->nipd . '.pdf';

		$mpdf->SetHTMLHeader('
		<div style="position: absolute; right:40; top: 20;">
		<img src="' . FCPATH  . "assets/image_web/kop_wbi.png" . '" width="250px" />
		</div>');
		$mpdf->SetHTMLFooter('
		<div style="font-size: 12px; color: #009999 ;bottom:10;position:absolute">
		<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
		<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
		Kabupaten Deli Serdang – Sumatera Utara<br>
		(061) 42074111; Laman : www.wbi.ac.id<br>
		</p>
 		 </div>');

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");

	} */

	/* public function cetak_undangan_semhas($uniq)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->join('siakad_skripsi_seminar_sidang ss', 'ss.id_judul_skripsi = j.id_judul_skripsi')
			->where('md5(ss.uniq)', $uniq)->get()->row();
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$output['judul'] = $judul;

		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$nama_kaprodi = $this->db->where(['id_sms' => $id_sms, 'id_smt' => $id_smt])->get('siakad_administrasi_prodi')->row()->nm_pejabat_khs_1;

		$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_1 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		$datanya = "Nomor :" . $judul->nomor_undangan . "\n" .
			"Tanggal : " . $judul->tanggal_undangan . "\n" .
			"Ditandatangani Oleh: \n" . $nama_kaprodi . " \KAPRODI";

		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;

		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = '';

		if (!empty($doping_1)) {
			$output['doping_1'] = $doping_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($doping_2)) {
			$output['doping_2'] = $doping_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		}

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Penguji)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Penguji)';
		}



		$html = $this->load->view('cetak/pdf_cetak_undangan_semhas', $output, true);
		$pdfFilePath  = 'undangan_sempro_' . $judul->nipd . '.pdf';

		$mpdf->SetHTMLHeader('
		<div style="position: absolute; right:40; top: 20;">
		<img src="' . FCPATH  . "assets/image_web/kop_wbi.png" . '" width="250px" />
		</div>');
		$mpdf->SetHTMLFooter('
		<div style="font-size: 12px; color: #009999 ;bottom:10;position:absolute">
		<p style="font-weight:bold">P O L I T E K N I K  &#160; &#160;  W I L M A R  &#160; &#160;  B I S N I S	 &#160; &#160; I N D O N E S I A </p>
		<p>Jalan Kapten Batu Sihombing, Desa Medan Estate, Kec. Percut Sei Tuan<br>
		Kabupaten Deli Serdang – Sumatera Utara<br>
		(061) 42074111; Laman : www.wbi.ac.id<br>
		</p>
 		 </div>');

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	} */

	public function cetak_sk($id_judul_skripsi)
	{
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

	public function cetak_surat_tugas($id_judul_skripsi)
	{
		$judul = $this->Qr_model->judul($id_judul_skripsi);
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

		$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);

		$tanggal_qr = tgl_short($judul->tanggal_sk);
		$datanya = DIREKTUR . ' (' . $tanggal_qr . ')';

		$output['qr_name'] = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$this->load->library('table');

		$tmpl = $this->Qr_model->template_tabel();

		$this->table->set_template($tmpl);
		$this->table->set_heading(['NO', 'NIDN', 'NAMA DOSEN']);

		/* if (!empty($doping_2)) {
			$this->table->add_row(array(
				array('data' => '1.', 'class' => 'text-center'),
				array('data' => $doping_2->nidn, 'class' => 'text-center'),
				array('data' => $doping_2->nama_lengkap, 'class' => 'text-left'),
			));
		} */

		if (!empty($penguji_sempro_1)) {
			$no = 1;
			$this->table->add_row([
				['data' => "$no" . ".", 'class' => 'text-center'],
				['data' => $penguji_sempro_1->nidn, 'class' => 'text-center'],
				['data' => $penguji_sempro_1->nama_lengkap, 'class' => 'text-left'],
			]);
		}

		if (!empty($penguji_sempro_2)) {
			$this->table->add_row([
				['data' => '2.', 'class' => 'text-center'],
				['data' => $penguji_sempro_2->nidn, 'class' => 'text-center'],
				['data' => $penguji_sempro_2->nama_lengkap, 'class' => 'text-left'],

			]);
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

	public function cetak_undangan($id_judul_skripsi)
	{
		$judul = $this->Qr_model->judul_undangan($id_judul_skripsi, 1);
		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('tahun_ajaran');

		$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt)->nm_pejabat_khs_1;

		$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
		$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);

		$tanggal_qr = tgl_short($judul->tanggal_undangan);
		$datanya =  $nama_kaprodi . " (" . $tanggal_qr . ")";

		$output['judul'] = $judul;
		$output['qr_name'] = $this->Qr_model->create_qr($datanya, $judul->nipd);

		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;
		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = '';

		if (!empty($doping_1)) {
			$output['doping_1'] = $doping_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($doping_2)) {
			$output['doping_2'] = $doping_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		}

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Penguji)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Penguji)';
		}

		$html = $this->load->view('cetak/pdf_cetak_undangan_sempro', $output, true);
		$pdfFilePath  = 'undangan_sempro_' . $judul->nipd . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$mpdf->SetHTMLHeader($this->Qr_model->header());
		$mpdf->SetHTMLFooter($this->Qr_model->footer());

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_undangan_semhas($id_judul_skripsi)
	{
		$judul = $this->Qr_model->judul_undangan($id_judul_skripsi, 2);
		$id_sms = $judul->id_sms;
		$id_smt = $this->session->userdata('setting')->tahun_ajaran_aktif;
		$nama_kaprodi = $this->Qr_model->nama_kaprodi($id_sms, $id_smt)->nm_pejabat_khs_1;

		$doping_1 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_1);
		$doping_2 = $this->Qr_model->nama_dosen($judul->dosen_pembimbing_2);
		$penguji_sempro_1 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_1);
		$penguji_sempro_2 = $this->Qr_model->nama_dosen($judul->dosen_penguji_sempro_2);

		$tanggal_qr = tgl_short($judul->tanggal_undangan);
		$datanya = $nama_kaprodi . " (" . $tanggal_qr . ")";

		$this->load->model('Qr_model');
		$qr_name = $this->Qr_model->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = FCPATH  . "assets/qrcode/" . $qr_name;

		$output['nama_kaprodi'] = $nama_kaprodi;

		$output['doping_1'] = $output['doping_2'] = $output['penguji_sempro_1'] = $output['penguji_sempro_2'] = '';

		if (!empty($doping_1)) {
			$output['doping_1'] = $doping_1->nama_lengkap . ' (Pembimbing Utama)';
		}

		if (!empty($doping_2)) {
			$output['doping_2'] = $doping_2->nama_lengkap . ' (Pembimbing Pendamping / Penguji) *)';
		}

		if (!empty($penguji_sempro_1)) {
			$output['penguji_sempro_1'] = $penguji_sempro_1->nama_lengkap . ' (Penguji)';
		}

		if (!empty($penguji_sempro_2)) {
			$output['penguji_sempro_2'] = $penguji_sempro_2->nama_lengkap . ' (Penguji)';
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

	public function cetak_undangan_sidang($id_judul_skripsi)
	{
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
