<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_daftar_kerjasama extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Daftar_kerjasama_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [17];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['content'] = 'daftar_kerjasama/daftar_kerjasama_view';
		$output['after_page'] = 'daftar_kerjasama/daftar_kerjasama_after_page';
		$output['judul'] = 'Daftar Kerja Sama';
		$output['judul_page'] = 'Daftar Kerja Sama';

		$this->load->view('master_page_elite', $output);
	}

	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->data_main();

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Daftar_kerjasama_model->count_all(),
				"recordsFiltered" => $this->Daftar_kerjasama_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Daftar_kerjasama_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = '<small>' . $b->nomor_dokumen . '</small><br>' . $b->judul_kerjasama . '<br><span class="label label-inverse">' . $b->nm_dokumen . '</span>';
			$row[] = $b->instansi_mitra;
			$row[] = $b->nm_status_kerjasama;
			$row[] = '<small>' . tgl_indo($b->tanggal_awal) . ' - ' . tgl_indo($b->tanggal_akhir) . '</small>';

			$row[] = '<a class="btn btn-dark btn-xs" 
						 title="Edit" 
						 onclick="edit_kerjasama(' . "'" . $b->id_kerjasama . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-edit"></i>
					  </a>
					  <a class="btn btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_kerjasama(' . "'" . $b->id_kerjasama . "'" . ')"
						 href="javascript:void()" >
						 <i class="fas fa-times"></i>
					  </a>';
			$data[] = $row;
		}

		return $data;
	}

	public function ajax_edit($id)
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->Daftar_kerjasama_model->get_by_id($id);
			echo json_encode($data);
		}
	}

	public function ajax_add()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'add';
			$this->_validate('add');

			if ($this->form_validation->run() == FALSE) {
				$errors = [
					'id_dokumen_kerjasama' 	=> form_error('id_dokumen_kerjasama'),
					'nomor_dokumen' 		=> form_error('nomor_dokumen'),
					'judul_kerjasama' 		=> form_error('judul_kerjasama'),
					'deskripsi_kerjasama' 	=> form_error('deskripsi_kerjasama'),
					'status_kerjasama' 		=> form_error('status_kerjasama'),
					'tanggal_awal' 			=> form_error('tanggal_awal'),
					'tanggal_akhir' 		=> form_error('tanggal_akhir'),
					'instansi_mitra' 		=> form_error('instansi_mitra'),
					'alamat_mitra' 			=> form_error('alamat_mitra'),
					'penandatangan_mitra' 	=> form_error('penandatangan_mitra'),
					'jabatan_mitra' 		=> form_error('jabatan_mitra'),
					'id_sub_instansi' 		=> form_error('id_sub_instansi'),
					'id_bidang_kerjasama' 	=> form_error('id_bidang_kerjasama'),
					'id_bidang_usaha' 		=> form_error('id_bidang_usaha'),
					'magang_mpi' 		=> form_error('magang_mpi'),
					'magang_ap' 		=> form_error('magang_ap'),
					'magang_ah' 		=> form_error('magang_ah'),
					'magang_pka' 		=> form_error('magang_pka'),
					'magang_trpl' 		=> form_error('magang_trpl'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();

				if ($this->input->post('id_bentuk_kegiatan') == '') {
					$id_bentuk_kegiatan = 0;
				} else {
					$id_bentuk_kegiatan = $this->input->post('id_bentuk_kegiatan');
				}

				$data = [
					'id_dokumen_kerjasama' => $this->input->post('id_dokumen_kerjasama'),
					'nomor_dokumen' => $this->input->post('nomor_dokumen'),
					'judul_kerjasama' => $this->input->post('judul_kerjasama'),
					'deskripsi_kerjasama' => $this->input->post('deskripsi_kerjasama'),
					'status_kerjasama' => $this->input->post('status_kerjasama'),
					'tanggal_awal' => $this->input->post('tanggal_awal'),
					'tanggal_akhir' => $this->input->post('tanggal_akhir'),
					'instansi_mitra' => $this->input->post('instansi_mitra'),
					'alamat_mitra' => $this->input->post('alamat_mitra'),
					'penandatangan_mitra' => $this->input->post('penandatangan_mitra'),
					'jabatan_mitra' => $this->input->post('jabatan_mitra'),
					'id_sub_instansi' => $this->input->post('id_sub_instansi'),
					'id_bidang_kerjasama' => $this->input->post('id_bidang_kerjasama'),
					'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'id_bentuk_kegiatan' => $id_bentuk_kegiatan,
					'magang_mpi' => $this->input->post('magang_mpi'),
					'magang_ah' => $this->input->post('magang_ah'),
					'magang_ap' => $this->input->post('magang_ap'),
					'magang_pka' => $this->input->post('magang_pka'),
					'magang_trpl' => $this->input->post('magang_trpl'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Daftar_kerjasama_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Daftar_kerjasama_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'update';
			$this->_validate('update');


			if ($this->form_validation->run() == FALSE) {
				$errors = [
					'id_dokumen_kerjasama' 	=> form_error('id_dokumen_kerjasama'),
					'nomor_dokumen' 		=> form_error('nomor_dokumen'),
					'judul_kerjasama' 		=> form_error('judul_kerjasama'),
					'deskripsi_kerjasama' 	=> form_error('deskripsi_kerjasama'),
					'status_kerjasama' 		=> form_error('status_kerjasama'),
					'tanggal_awal' 			=> form_error('tanggal_awal'),
					'tanggal_akhir' 		=> form_error('tanggal_akhir'),
					'instansi_mitra' 		=> form_error('instansi_mitra'),
					'alamat_mitra' 			=> form_error('alamat_mitra'),
					'penandatangan_mitra' 	=> form_error('penandatangan_mitra'),
					'jabatan_mitra' 		=> form_error('jabatan_mitra'),
					'id_sub_instansi' 		=> form_error('id_sub_instansi'),
					'id_bidang_kerjasama' 	=> form_error('id_bidang_kerjasama'),
					'id_bidang_usaha' 		=> form_error('id_bidang_usaha'),
					'magang_mpi' 		=> form_error('magang_mpi'),
					'magang_ap' 		=> form_error('magang_ap'),
					'magang_ah' 		=> form_error('magang_ah'),
					'magang_pka' 		=> form_error('magang_pka'),
					'magang_trpl' 		=> form_error('magang_trpl'),

				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'id_dokumen_kerjasama' => $this->input->post('id_dokumen_kerjasama'),
					'nomor_dokumen' => $this->input->post('nomor_dokumen'),
					'judul_kerjasama' => $this->input->post('judul_kerjasama'),
					'deskripsi_kerjasama' => $this->input->post('deskripsi_kerjasama'),
					'status_kerjasama' => $this->input->post('status_kerjasama'),
					'tanggal_awal' => $this->input->post('tanggal_awal'),
					'tanggal_akhir' => $this->input->post('tanggal_akhir'),
					'instansi_mitra' => $this->input->post('instansi_mitra'),
					'alamat_mitra' => $this->input->post('alamat_mitra'),
					'penandatangan_mitra' => $this->input->post('penandatangan_mitra'),
					'jabatan_mitra' => $this->input->post('jabatan_mitra'),
					'id_sub_instansi' => $this->input->post('id_sub_instansi'),
					'id_bidang_kerjasama' => $this->input->post('id_bidang_kerjasama'),
					'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'id_bentuk_kegiatan' => $this->input->post('id_bentuk_kegiatan'),
					'magang_mpi' => $this->input->post('magang_mpi'),
					'magang_ah' => $this->input->post('magang_ah'),
					'magang_ap' => $this->input->post('magang_ap'),
					'magang_pka' => $this->input->post('magang_pka'),
					'magang_trpl' => $this->input->post('magang_trpl'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Daftar_kerjasama_model->update(['id_kerjasama' => $this->input->post('id_kerjasama')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_dokumen_kerjasama', 'Dokumen Kerjasama', 'trim|required');
			$this->form_validation->set_rules('nomor_dokumen', 'Nomor Dokumen', 'trim|required');
			$this->form_validation->set_rules('judul_kerjasama', 'Judul Kerjasama', 'trim|required');
			$this->form_validation->set_rules('deskripsi_kerjasama', 'Deskripsi Kerjasama', 'trim|required');
			$this->form_validation->set_rules('status_kerjasama', 'Status Kerjasama', 'trim|required');
			$this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'trim|required');
			$this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('instansi_mitra', 'Instansi Mitra', 'trim|required');
			$this->form_validation->set_rules('alamat_mitra', 'Alamat Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_mitra', 'Penandatangan', 'trim|required');
			$this->form_validation->set_rules('jabatan_mitra', 'Jabatan', 'trim|required');
			$this->form_validation->set_rules('id_sub_instansi', 'Instansi', 'trim|required');
			$this->form_validation->set_rules('id_bidang_kerjasama', 'Bidang Kerjasama', 'trim|required');
			$this->form_validation->set_rules('id_bidang_usaha', 'Bidang Usaha', 'trim|required');
			$this->form_validation->set_rules('magang_mpi', 'Magang MPI', 'trim|numeric');
			$this->form_validation->set_rules('magang_ap', 'Magang AP', 'trim|numeric');
			$this->form_validation->set_rules('magang_ah', 'Magang AH', 'trim|numeric');
			$this->form_validation->set_rules('magang_pka', 'Magang PKA', 'trim|numeric');
			$this->form_validation->set_rules('magang_trpl', 'Magang TRPL', 'trim|numeric');
			$this->form_validation->set_message('required', '%s harus diisi');
			$this->form_validation->set_message('numeric', 'Harus bernilai angka');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('id_dokumen_kerjasama', 'Dokumen Kerjasama', 'trim|required');
			$this->form_validation->set_rules('nomor_dokumen', 'Nomor Dokumen', 'trim|required');
			$this->form_validation->set_rules('judul_kerjasama', 'Judul Kerjasama', 'trim|required');
			$this->form_validation->set_rules('deskripsi_kerjasama', 'Deskripsi Kerjasama', 'trim|required');
			$this->form_validation->set_rules('status_kerjasama', 'Status Kerjasama', 'trim|required');
			$this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'trim|required');
			$this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('instansi_mitra', 'Instansi Mitra', 'trim|required');
			$this->form_validation->set_rules('alamat_mitra', 'Alamat Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_mitra', 'Penandatangan', 'trim|required');
			$this->form_validation->set_rules('jabatan_mitra', 'Jabatan', 'trim|required');
			$this->form_validation->set_rules('id_sub_instansi', 'Instansi', 'trim|required');
			$this->form_validation->set_rules('id_bidang_kerjasama', 'Bidang Kerjasama', 'trim|required');
			$this->form_validation->set_rules('id_bidang_usaha', 'Bidang Usaha', 'trim|required');
			$this->form_validation->set_rules('magang_mpi', 'Magang MPI', 'trim|numeric');
			$this->form_validation->set_rules('magang_ap', 'Magang AP', 'trim|numeric');
			$this->form_validation->set_rules('magang_ah', 'Magang AH', 'trim|numeric');
			$this->form_validation->set_rules('magang_pka', 'Magang PKA', 'trim|numeric');
			$this->form_validation->set_rules('magang_trpl', 'Magang TRPL', 'trim|numeric');
			$this->form_validation->set_message('required', '%s harus diisi');
			$this->form_validation->set_message('numeric', 'Harus bernilai angka');
		}
	}
}
