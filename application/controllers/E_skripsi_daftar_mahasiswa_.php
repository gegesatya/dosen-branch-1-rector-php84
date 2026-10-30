<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_mahasiswa extends CI_Controller
{

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');
		$this->load->model('E_skripsi_daftar_mahasiswa_model');

		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [5];
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
		$output['judul_besar'] = 'Evaluasi Mahasiswa SKS Tempuh > 120 SKS ';
		$output['judul_kecil'] = $nm_lemb;
		$output['title'] = 'Evaluasi Mahasiswa';
		$output['menu_aktif'] = 'm_daftar_mahasiswa';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = '_data/view_skripsi_daftar_mahasiswa';

		$this->load->view('master_page_datatables', $output);
	}

	public function get_all()
	{
		header('Content-Type: application/json');
		echo $this->E_skripsi_daftar_mahasiswa_model->get_all();
	}

	public function view($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_mahasiswa_model->get_by_id($id));
	}

	public function view_makul($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_mahasiswa_model->get_analisis_data($id));
	}

	public function generate_analisis()
	{
		$nipd = $this->input->post('nipd');
		$analisis_ipk = $this->input->post('analisis_ipk');
		$analisis_sks_total = $this->input->post('analisis_sks_total');
		$analisis_sks_lulus = $this->input->post('analisis_sks_lulus');
		$analisis_sks_tidak_lulus = $this->input->post('analisis_sks_tidak_lulus');
		$makul_tidak_lulus = $this->input->post('makul_tidak_lulus');

		$data = [
			'nipd' => $nipd,
			'analisis_ipk'  => $analisis_ipk,
			'analisis_sks_total'  => $analisis_sks_total,
			'analisis_sks_lulus' => $analisis_sks_lulus,
			'analisis_sks_tidak_lulus' => $analisis_sks_tidak_lulus,
			'makul_tidak_lulus' => $makul_tidak_lulus
		];

		$this->db->replace('siakad_skripsi_analisis', $data);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate('update');

		$update = [
			'is_boleh_ajukan_judul'	=> $this->input->post('is_boleh_ajukan_judul') == TRUE ? 'Y' : 'N',
			'keterangan' => $this->input->post('keterangan')
		];
		$this->db->where('nipd', $this->input->post('nipd'));
		$this->db->update('siakad_skripsi_analisis', $update);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
		//}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
		} else {
			$this->form_validation->set_error_delimiters('', '');
		}

		/* 	$this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
		$this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric'); */
	}
}
