<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_kartu_bimbingan extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_kartu_bimbingan_model');

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus dosen.');
		}
	}

	public function index($nipd, $nm_pd)
	{
		$id_sms = $this->session->userdata('prodi');
		if (!empty($id_sms)) {
			$nm_lemb = $this->db->where(compact('id_sms'))->get('sms')->row()->nm_lemb;
		}

		$id_ptk = $this->session->userdata('id_ptk');
		/*$data_menu_bimbingan = $this->db->where('dosen_pembimbing_1', $id_ptk)
			->or_where('dosen_pembimbing_2', $id_ptk)
			->from('siakad_skripsi_pengajuan_judul pj')
			->join('mahasiswa_pt mp', 'mp.nipd = pj.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->order_by('nm_pd')->get()->result(); */

		$data_judul = $this->db->where('nipd', $nipd)
			->from('siakad_skripsi_pengajuan_judul')
			->get()->row();
		$output['judul_besar'] = 'Lembar Monitoring Tugas Akhir';
		$output['title'] = 'Lembar Monitoring TA ' . urldecode($nm_pd);
		$output['judul_skripsi'] = $data_judul->judul_ditetapkan;
		$output['judul_kecil'] = $nipd . ' | ' . urldecode($nm_pd);
		$output['menu_aktif'] = $nipd;
		$output['parent_bimbingan'] = 'active open';
		$output['nipd'] = $nipd;
		//$output['data_menu_bimbingan'] = $data_menu_bimbingan;
		$output['content'] = '_data/view_skripsi_kartu_bimbingan';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all($nipd)
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_kartu_bimbingan_model->get_all($nipd);
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_kartu_bimbingan_model->get_by_id($id));
	}

	public function edit_respon($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_kartu_bimbingan_model->get_by_id($id));
	}


	/* public function delete($id)
	{
		$this->db->where('id_judul_skripsi', $id);
		$this->db->delete('siakad_skripsi_pengajuan_judul');
		echo json_encode(array("status" => TRUE));
	} */

	public function cek_data()
	{
		$nipd = $this->session->userdata('mahasiswa_pt')->nipd;
		$cek = $this->db->where(compact('nipd'))->get('siakad_skripsi_pengajuan_judul')->num_rows();
		if ($cek == 0) {
			echo json_encode(["status" => TRUE]);
		} else {
			echo json_encode(["status" => FALSE]);
		}
	}

	public function add_respon()
	{
		$this->validation_for = 'add';

		$data = [];
		$data['status'] = TRUE;

		$this->_validate('update');

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'respon_bimbingan' 			=> form_error('respon_bimbingan'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			//get_respon_data
			$respon = $this->db->where('id_bimbingan', $this->input->post('id_bimbingan'))->order_by('respon_date')->get('siakad_skripsi_kartu_bimbingan_detail');
			$html_respon = "<div class='general-item-list' name='table-my-items' id='table-my-items' style='height: 200px; overflow-y: scroll;'>";

			//</div></div>";

			if ($respon->num_rows() > 0) {
				foreach ($respon->result() as $key => $value) {
					$tgl = date('Y-m-d', strtotime($value->respon_date));
					$jam = date('H:i', strtotime($value->respon_date));
					$html_respon .= "<div class='item'>
										<div class='item-head'>
											<div class='item-details'>
												<a href='' class='item-name primary-link'>" . $value->respon_by . "</a>
												<span class='item-label'>" . tgl_indo_short($tgl) . ", " . $jam . "</span>
											</div>
										</div>
										<div class='item-body'>
										" . $value->respon_bimbingan . "
										</div>
									</div>";
				}
			}

			$_tgl = date('Y-m-d');
			$_jam = date('H:i');
			$html_respon .= "<div class='item'>
								<div class='item-head'>
									<div class='item-details'>
										<a href='' class='item-name primary-link'>" . $this->ion_auth->user()->row()->first_name . "</a>
										<span class='item-label'>" . tgl_indo_short($_tgl) . ", " . $_jam . "</span>
									</div>
								</div>
								<div class='item-body'>
								" . $this->input->post('respon_bimbingan') . "
								</div>
								</div>
								</div>
							</div>";

			$insert = [
				'id_bimbingan' => $this->input->post('id_bimbingan'),
				'respon_bimbingan' => $this->input->post('respon_bimbingan'),
				'respon_by' => $this->ion_auth->user()->row()->first_name,
				'respon_date' => date('Y-m-d H:i:s'),
			];
			$this->db->insert('siakad_skripsi_kartu_bimbingan_detail', $insert);
			$this->db->where('id_bimbingan', $this->input->post('id_bimbingan'))->update('siakad_skripsi_kartu_bimbingan', ['html_respon' => $html_respon]);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate('update');
		$nipd = $this->session->userdata('mahasiswa_pt')->nipd;
		$id_sms = $this->session->userdata('mahasiswa_pt')->id_sms;

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'id_topik' 				=> form_error('id_topik'),
				'judul_skripsi_1' 		=> form_error('judul_skripsi_1'),
				'judul_skripsi_2' 		=> form_error('judul_skripsi_2'),
				'judul_skripsi_3' 		=> form_error('judul_skripsi_3'),
				'target_selesai' 		=> form_error('target_selesai'),
				'tanggal_diajukan' 		=> form_error('tanggal_diajukan'),
				'alamat_medan' 		=> form_error('alamat_medan'),
				'alamat_asal' 		=> form_error('alamat_asal'),
				'email_pribadi' 		=> form_error('email_pribadi'),
				'no_handphone' 		=> form_error('no_handphone'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$update = [
				'id_topik'	=> $this->input->post('id_topik'),
				'nipd'		=> $nipd,
				'id_sms'		=> $id_sms,
				'tanggal_diajukan'	=> $this->input->post('tanggal_diajukan'),
				'judul_skripsi_1'	=> $this->input->post('judul_skripsi_1'),
				'judul_skripsi_2'	=> $this->input->post('judul_skripsi_2'),
				'judul_skripsi_3'	=> $this->input->post('judul_skripsi_3'),
				'target_selesai'	=> $this->input->post('target_selesai'),
				'alamat_medan' => $this->input->post('alamat_medan'),
				'alamat_asal' => $this->input->post('alamat_asal'),
				'email_pribadi' => $this->input->post('email_pribadi'),
				'no_handphone' => $this->input->post('no_handphone'),
				'change_by' => $this->ion_auth->user()->row()->id,
				'change_date' => date('Y-m-d H:i:s')
			];
			$this->db->where('id_judul_skripsi', $this->input->post('id_judul_skripsi'));
			$this->db->update('siakad_skripsi_pengajuan_judul', $update);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('materi_bimbingan', 'Materi Bimbingan', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('respon_bimbingan', 'Message', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
		}
	}

	/* public function cetak_pdf($id_judul_skripsi, $enkrip)
	{
		try {
			$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
				->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
				->join('mahasiswa m', 'm.id_pd = mp.id_pd')
				->join('sms s', 's.id_sms = mp.id_sms')
				->where('md5(j.id_judul_skripsi)', $enkrip)->get()->row();
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
			$output['judul'] = $judul;
			$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji = $this->db->where('sd.id_ptk', $judul->dosen_penguji)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

			$datanya = "Nomor SK :" . $judul->nomor_sk . "\n" .
				"Tanggal SK : " . $judul->tanggal_sk . "\n" .
				"Ditandatangani Oleh: \nDr. Jenny Elisabeth \nDirektur";
			$output['qr_name'] = $this->create_qr($datanya, $judul->nipd);



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
					array('data' => 'Pembimbing', 'class' => 'text-left'),
				));
			}

			if (!empty($doping_2)) {
				$this->table->add_row(array(
					array('data' => $doping_2->nidn, 'class' => 'text-right'),
					array('data' => $doping_2->nama_lengkap, 'class' => 'text-left'),
					array('data' => $doping_2->jabatan_akademik, 'class' => 'text-left'),
					array('data' => 'Pembimbing', 'class' => 'text-left'),
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

			$html = $this->load->view('cetak/pdf_sk_pembimbing', $output, true);
			$pdfFilePath  = 'sk_penetapan_judul_' . $judul->nipd . '.pdf';

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
		} catch (\Throwable $th) {
			echo "Maaf, data tidak ada";
		}
	} */
}
