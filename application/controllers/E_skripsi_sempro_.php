<?php
defined('BASEPATH') or exit('No direct script access allowed');


class E_skripsi_sempro extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_sempro_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus login sebagai dosen.');
			redirect('auth/logout', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$output['judul_besar'] = 'Penetapan Dosen Penguji Sempro';
		$output['judul_kecil'] = 'dan Penetapan Jadwal Sempro';
		$output['menu_aktif'] = 'm_sempro';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = 'e_skripsi_sempro/e_skripsi_sempro_view';
		$output['after_page'] = 'e_skripsi_sempro/e_skripsi_sempro_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{

		$list = $this->E_skripsi_sempro_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$nm_penguji_sempro_1 = $nm_penguji_sempro_2 = '';
			$dosen_penguji_sempro_1 = $this->db->where('id_ptk', $rg->dosen_penguji_sempro_1)->get('siakad_skripsi_dosen');
			$dosen_penguji_sempro_2 = $this->db->where('id_ptk', $rg->dosen_penguji_sempro_2)->get('siakad_skripsi_dosen');

			if ($dosen_penguji_sempro_1->num_rows() > 0) {
				$nm_penguji_sempro_1 = $dosen_penguji_sempro_1->row()->nama_lengkap;
			}

			if ($dosen_penguji_sempro_2->num_rows() > 0) {
				$nm_penguji_sempro_2 = $dosen_penguji_sempro_2->row()->nama_lengkap;
			}

			$html_jadwal = $html_cek_data = $html_surat_tugas = $html_undangan = '';
			if (!empty($rg->tanggal_sempro) || $rg->tanggal_sempro == '0000-00-00') {
				if (!empty($rg->jam_sempro)) {
					$jam = " | " . substr($rg->jam_sempro, 0, 5);
				} else {
					$jam = '';
				}
				$html_jadwal = '<br><i class="fa fa-calendar font-green"></i> <small>Jadwal Sempro : ' . tgl_indo($rg->tanggal_sempro) . $jam . '</small>';
			}

			if ($rg->terbit_surat_tugas == 1) {
				$html_surat_tugas = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Surat Tugas Penguji Sempro sudah diterbitkan </small><a href="' . site_url('e_skripsi_sempro/cetak_surat_tugas') . '/' .  md5($rg->id_judul_skripsi)  . '" class="btn btn-xs btn-info" target="_new"> <i class="fa fa-download"></i></a>';
			}

			if ($rg->undangan_status == 1) {
				$html_undangan = '<br><i class="fa fa-bullhorn font-blue"></i> <small>Surat Undangan Sempro sudah diterbitkan </small><a href="' . site_url('e_skripsi_sempro/cetak_undangan') . '/' . md5($rg->id_judul_skripsi)  . '" class="btn btn-xs btn-info" target="_new"> <i class="fa fa-download"></i></a>';
			}

			$html_cek_data = $rg->sudah_cek_data_sempro == 0 ? '<br><i class="fa fa-times font-red"></i> <small>Dokumen persyaratan SEMPRO belum diperiksa</small>' : '<br><i class="fa fa-check-square-o info font-green"></i><small>Dokumen persyaratan SEMPRO sudah di validasi</small>';

			$html_info = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
							onclick="get_info(' . "'" . $rg->id_judul_skripsi . "'" . ')">
							<i class="fa fa-info"></i> <small>Info</small></a>';
			$row = [];
			$row[] = $no;
			$row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;
			$row[] = $html_info . $html_cek_data . $html_jadwal . $html_surat_tugas . $html_undangan;
			$row[] = $nm_penguji_sempro_1 . '<br>' . $nm_penguji_sempro_2;


			$html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
							onclick="get_sempro(' . "'" . $rg->id_judul_skripsi . "'" . ')">
							<i class="icon-bell"></i> <small>Dosen Penguji Sempro</small></a>';

			$row[] = $html_komen;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_sempro_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_sempro_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}



	public function ajax_edit($id)
	{
		$data = $this->E_skripsi_sempro_model->get_by_id_2($id);
		$data->fotokopi_ktm = $this->convertURLs($data->fotokopi_ktm);
		$data->fotokopi_krs = $this->convertURLs($data->fotokopi_krs);
		$data->transkrip_nilai = $this->convertURLs($data->transkrip_nilai);
		$data->kartu_partisipasi = $this->convertURLs($data->kartu_partisipasi);
		$data->proposal_tugas_akhir = $this->convertURLs($data->proposal_tugas_akhir);
		echo json_encode($data);
	}


	public function view_info($id_judul_skripsi)
	{
		$data = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$nipd = $data->nipd;
		$mhs = $this->db->where('nipd', $nipd)->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$nm_pd = !empty($mhs->nm_pd) ? $mhs->nm_pd : '';
		$judul_ditetapkan = $data->judul_ditetapkan;
		$target_selesai = $data->target_selesai;
		$doping_1 = $this->db->where('id_ptk', $data->dosen_pembimbing_1)->get('siakad_skripsi_dosen');
		$doping_2 = $this->db->where('id_ptk', $data->dosen_pembimbing_2)->get('siakad_skripsi_dosen');
		$dosen_penguji_sempro_1 = $this->db->where('id_ptk', $data->dosen_penguji_sempro_1)->get('siakad_skripsi_dosen');
		$dosen_penguji_sempro_2 = $this->db->where('id_ptk', $data->dosen_penguji_sempro_2)->get('siakad_skripsi_dosen');
		$nm_doping_1 = $nm_doping_2 = $nm_penguji_sempro_1 = $nm_penguji_sempro_2 = '';

		if ($doping_1->num_rows() > 0) {
			$nm_doping_1 = $doping_1->row()->nama_lengkap;
		}

		if ($doping_2->num_rows() > 0) {
			$nm_doping_2 = $doping_2->row()->nama_lengkap;
		}

		if ($dosen_penguji_sempro_1->num_rows() > 0) {
			$nm_penguji_sempro_1 = $dosen_penguji_sempro_1->row()->nama_lengkap;
		}

		if ($dosen_penguji_sempro_2->num_rows() > 0) {
			$nm_penguji_sempro_2 = $dosen_penguji_sempro_2->row()->nama_lengkap;
		}

		echo json_encode([
			'nipd' => $nipd,
			'nm_pd' => $nm_pd,
			'judul_ditetapkan' => $judul_ditetapkan,
			'target_selesai' => $target_selesai,
			'nm_doping_1' => $nm_doping_1,
			'nm_doping_2' => $nm_doping_2,
			'nm_penguji_sempro_1' => $nm_penguji_sempro_1,
			'nm_penguji_sempro_2' => $nm_penguji_sempro_2,
			'no_handphone' => $data->no_handphone,
		]);
	}

	public function ajax_save_sempro()
	{
		$data = [
			'dosen_penguji_sempro_1' => $this->input->post('dosen_penguji_sempro_1'),
			'dosen_penguji_sempro_2' => $this->input->post('dosen_penguji_sempro_2'),
		];

		$this->E_skripsi_sempro_model->update_sempro(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data);
		echo json_encode(["status" => TRUE]);
	}


	public function cetak_surat_tugas($id_judul_skripsi)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('md5(j.id_judul_skripsi)', $id_judul_skripsi)->get()->row();
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
		$output['judul'] = $judul;
		//$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		//$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_1 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
		$penguji_sempro_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_sempro_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

		$datanya = "Nomor :" . $judul->no_surat_tugas . "\n" .
			"Tanggal : " . $judul->tanggal_surat_tugas . "\n" .
			"Ditandatangani Oleh: \nDr. Jenny Elisabeth \nDirektur";
		/* $output['qr_name'] = $this->create_qr($datanya, $judul->nipd); */

		$qr_name = $this->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = !empty($qr_name) ? (str_starts_with($qr_name, 'data:') ? $qr_name : FCPATH . "assets/qrcode/" . $qr_name) : '';

		$this->load->library('table');

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
		$this->table->set_heading(['NO', 'NIDN', 'NAMA DOSEN']);



		if (!empty($penguji_sempro_1)) {
			$this->table->add_row([
				['data' => '1.', 'class' => 'text-center'],
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

		//$this->load->view('cetak/pdf_sk_pembimbing', $output);
	}

	public function cetak_undangan($id_judul_skripsi)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('md5(j.id_judul_skripsi)', $id_judul_skripsi)->get()->row();
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
		/* $output['qr_name'] = $this->create_qr($datanya, $judul->nipd); */
		$qr_name = $this->create_qr($datanya, $judul->nipd);
		$output['qr_name'] = !empty($qr_name) ? (str_starts_with($qr_name, 'data:') ? $qr_name : FCPATH . "assets/qrcode/" . $qr_name) : '';
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

		//$this->load->view('cetak/pdf_sk_pembimbing', $output);
	}

	private function create_qr($datanya, $nipd = null)
	{
		$this->load->model('Qr_model');
		return $this->Qr_model->create_qr($datanya, $nipd);
	}

	public function convertURLs($string)
	{
		$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
		return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
	}

	public function getDosenPenguji()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_sempro_model->get_dosen_penguji());
	}
}
