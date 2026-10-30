<?php
defined('BASEPATH') or exit('No direct script access allowed');


class E_skripsi_sk_saya extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_sk_saya_model');

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_sms = $this->session->userdata('prodi');
		if (!empty($id_sms)) {
			$nm_lemb = $this->db->where(compact('id_sms'))->get('sms')->row()->nm_lemb;
		}
		$output['judul_besar'] = 'SK Pembimbing Tugas Akhir';
		$output['judul_kecil'] = $nm_lemb;
		$output['title'] = 'SK Pembimbing Tugas Akhir';
		$output['menu_aktif'] = 'm_sk_saya';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = '_data/view_skripsi_sk_saya';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all()
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_sk_saya_model->get_all();
	}

	public function getKategori()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_sk_saya_model->get_kategori());
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_sk_saya_model->get_by_id($id));
	}

	public function delete($id)
	{
		$this->db->where('id_topik', $id);
		$this->db->delete('siakad_skripsi_topik');
		echo json_encode(["status" => TRUE]);
	}

	public function add()
	{
		$this->validation_for = 'add';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate();
		$id_sms = $this->session->userdata('prodi');

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'topik_skripsi' 	=> form_error('topik_skripsi'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$insert = [
				'topik_skripsi'	=> $this->input->post('topik_skripsi'),
				'deskripsi'	=> $this->input->post('deskripsi'),
				'id_sms'			=> $id_sms
			];
			$this->db->insert('siakad_skripsi_topik', $insert);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate();

		if ($this->form_validation->run() == FALSE) {
			$errors = [
				'topik_skripsi' 	=> form_error('topik_skripsi'),
			];
			$data = [
				'status' 		=> FALSE,
				'errors' 		=> $errors
			];
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		} else {
			$update = [
				'topik_skripsi'	=> $this->input->post('topik_skripsi'),
				'deskripsi'	=> $this->input->post('deskripsi'),
			];
			$this->db->where('id_topik', $this->input->post('id_topik'));
			$this->db->update('siakad_skripsi_topik', $update);
			$data['status'] = TRUE;
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	private function _validate()
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('topik_skripsi', 'Topik Skripsi', 'trim|required|min_length[5]|max_length[250]');
		/* 	$this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
		$this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric'); */
	}

	public function cetak_pdf($id_judul_skripsi)
	{
		$judul = $this->db->select('*')->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'mp.nipd = j.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->where('j.id_judul_skripsi', $id_judul_skripsi)->get()->row();

		if ($judul->tanggal_sk == '' or $judul->nomor_sk == '') {
		} else {
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
			$output['judul'] = $judul;
			$doping_1 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_1)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$doping_2 = $this->db->where('sd.id_ptk', $judul->dosen_pembimbing_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji = $this->db->where('sd.id_ptk', $judul->dosen_penguji)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();
			$penguji_2 = $this->db->where('sd.id_ptk', $judul->dosen_penguji_2)->from('siakad_dosen sd')->join('siakad_skripsi_dosen ssd', 'sd.id_ptk = ssd.id_ptk')->get()->row();

			$datanya = "Nomor SK :" . $judul->nomor_sk . "\n" .
				"Tanggal SK : " . $judul->tanggal_sk . "\n" .
				"Ditandatangani Oleh: \nDr. Jenny Elisabeth \nDirektur";
			//$output['qr_name'] = $this->create_qr($datanya, $judul->nipd);
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
			$this->table->set_heading(['NIDN', 'NAMA DOSEN', 'JABATAN AKADEMIK', 'KETERANGAN']);

			if (!empty($doping_1)) {
				$this->table->add_row([
					['data' => $doping_1->nidn, 'class' => 'text-right'],
					['data' => $doping_1->nama_lengkap, 'class' => 'text-left'],
					['data' => $doping_1->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Pembimbing', 'class' => 'text-left'],
				]);
			}

			if (!empty($doping_2)) {
				$this->table->add_row([
					['data' => $doping_2->nidn, 'class' => 'text-right'],
					['data' => $doping_2->nama_lengkap, 'class' => 'text-left'],
					['data' => $doping_2->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Pembimbing', 'class' => 'text-left'],
				]);
			}

			if (!empty($penguji)) {
				$this->table->add_row([
					['data' => $penguji->nidn, 'class' => 'text-right'],
					['data' => $penguji->nama_lengkap, 'class' => 'text-left'],
					['data' => $penguji->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Penguji', 'class' => 'text-left'],
				]);
			}

			if (!empty($penguji_2)) {
				$this->table->add_row([
					['data' => $penguji_2->nidn, 'class' => 'text-right'],
					['data' => $penguji_2->nama_lengkap, 'class' => 'text-left'],
					['data' => $penguji_2->jabatan_akademik, 'class' => 'text-left'],
					['data' => 'Penguji', 'class' => 'text-left'],
				]);
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
		}
	}

	public function create_qr($datanya, $nipd = null)
	{
		$this->load->model('Qr_model');
		return $this->Qr_model->create_qr($datanya, $nipd);
	}
}
