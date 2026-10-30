<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_output_kegiatan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Output_kegiatan_model');
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
		$output['content'] = 'output_kegiatan/output_kegiatan_view';
		$output['after_page'] = 'output_kegiatan/output_kegiatan_after_page';
		$output['judul'] = 'Data Kegiatan';
		$output['judul_page'] = 'Data Kegiatan';

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
				"recordsTotal" => $this->Output_kegiatan_model->count_all(),
				"recordsFiltered" => $this->Output_kegiatan_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Output_kegiatan_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$html = '';
			/* if (empty($b->nomor_dokumen_moa)) {
				$html = '<span class="label label-danger">MOA Tidak Ada</span>';
			} else {
				$html = $b->nomor_dokumen_moa . '<small><br>' . $b->judul_moa . '<br>Status: ' . $b->nm_status_kerjasama  . '</small>';
			} */
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $b->nm_kategori_output;
			/* $row[] = $html;

			$row[] = ''; */

			$row[] = '<a class="btn waves-effect waves-light btn-dark btn-xs" 
						 title="Edit" 
						 onclick="edit_data_kegiatan(\'' . $b->id_output_kegiatan . '\')"
						 href="javascript:void()" >
						 <i class="fas fa-edit"></i>
					  </a>
					  <a class="btn waves-effect waves-light btn-danger btn-xs" 
						 title="delete" 
						 onclick="delete_data_kegiatan(\'' . $b->id_output_kegiatan . '\')"
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
			$data = $this->Output_kegiatan_model->get_by_id($id);
			echo json_encode($data);
		}
	}

	public function ajax_add()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'add';
			$this->_validate();

			if ($this->form_validation->run() == FALSE) {
				$errors = [
					'id_kegiatan' => form_error('id_kegiatan'),
					'id_kategori_output' => form_error('id_kategori_output'),
					'dokumen_pdf' => form_error('dokumen_pdf'),
					'judul_penelitian_dosen' => form_error('judul_penelitian_dosen'),
					'judul_penelitian_mahasiswa' => form_error('judul_penelitian_mahasiswa'),
					'lingkup_pkl_mahasiswa' => form_error('lingkup_pkl_mahasiswa'),
					'kontribusi_dosen_wbi' => form_error('kontribusi_dosen_wbi'),
					'dosen_tamu_nama' => form_error('dosen_tamu_nama'),
					'dosen_tamu_topik' => form_error('dosen_tamu_topik'),
					'dosen_undang_nama' => form_error('dosen_undang_nama'),
					'dosen_undang_topik' => form_error('dosen_undang_topik'),
					'kurikulum_prodi' => form_error('kurikulum_prodi'),
					'kurikulum_ruang_lingkup' => form_error('kurikulum_ruang_lingkup'),
					'penyusunan_modul_prodi' => form_error('penyusunan_modul_prodi'),
					'penyusunan_modul_mk' => form_error('penyusunan_modul_mk'),
					'penyusunan_modul_ruang_lingkup' => form_error('penyusunan_modul_ruang_lingkup'),
					'beasiswa_jumlah' => form_error('beasiswa_jumlah'),
					'beasiswa_dana' => form_error('beasiswa_dana'),
					'investor_startup_jumlah' => form_error('investor_startup_jumlah'),
					'investor_startup_nama_bisnis' => form_error('investor_startup_nama_bisnis'),
					'investor_startup_jumlah_dana' => form_error('investor_startup_jumlah_dana'),
					'mbkm_mk' => form_error('mbkm_mk'),
					'mbkm_pic' => form_error('mbkm_pic'),
					'kerjasama_bisnis_jenis' => form_error('kerjasama_bisnis_jenis'),
					'lowongan_pekerjaan_jumlah_mahasiswa' => form_error('lowongan_pekerjaan_jumlah_mahasiswa'),
					'lowongan_pekerjaan_prodi' => form_error('lowongan_pekerjaan_prodi'),
					'lowongan_pekerjaan_angkatan' => form_error('lowongan_pekerjaan_angkatan')
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();

				/* if ($this->input->post('id_bentuk_kegiatan') == '') {
					$id_bentuk_kegiatan = 0;
				} else {
					$id_bentuk_kegiatan = $this->input->post('id_bentuk_kegiatan');
				} */

				$data = [
					'id_kegiatan' => $this->input->post('id_kegiatan'),
					'id_kategori_output' => $this->input->post('id_kategori_output'),
					'dokumen_pdf' => $this->input->post('dokumen_pdf'),
					'judul_penelitian_dosen' => $this->input->post('judul_penelitian_dosen'),
					'judul_penelitian_mahasiswa' => $this->input->post('judul_penelitian_mahasiswa'),
					'lingkup_pkl_mahasiswa' => $this->input->post('lingkup_pkl_mahasiswa'),
					'kontribusi_dosen_wbi' => $this->input->post('kontribusi_dosen_wbi'),
					'dosen_tamu_nama' => $this->input->post('dosen_tamu_nama'),
					'dosen_tamu_topik' => $this->input->post('dosen_tamu_topik'),
					'dosen_undang_nama' => $this->input->post('dosen_undang_nama'),
					'dosen_undang_topik' => $this->input->post('dosen_undang_topik'),
					'kurikulum_prodi' => $this->input->post('kurikulum_prodi'),
					'kurikulum_ruang_lingkup' => $this->input->post('kurikulum_ruang_lingkup'),
					'penyusunan_modul_prodi' => $this->input->post('penyusunan_modul_prodi'),
					'penyusunan_modul_mk' => $this->input->post('penyusunan_modul_mk'),
					'penyusunan_modul_ruang_lingkup' => $this->input->post('penyusunan_modul_ruang_lingkup'),
					'beasiswa_jumlah' => $this->input->post('beasiswa_jumlah'),
					'beasiswa_dana' => $this->input->post('beasiswa_dana'),
					'investor_startup_jumlah' => $this->input->post('investor_startup_jumlah'),
					'investor_startup_nama_bisnis' => $this->input->post('investor_startup_nama_bisnis'),
					'investor_startup_jumlah_dana' => $this->input->post('investor_startup_jumlah_dana'),
					'mbkm_mk' => $this->input->post('mbkm_mk'),
					'mbkm_pic' => $this->input->post('mbkm_pic'),
					'kerjasama_bisnis_jenis' => $this->input->post('kerjasama_bisnis_jenis'),
					'lowongan_pekerjaan_jumlah_mahasiswa' => $this->input->post('lowongan_pekerjaan_jumlah_mahasiswa'),
					'lowongan_pekerjaan_prodi' => $this->input->post('lowongan_pekerjaan_prodi'),
					'lowongan_pekerjaan_angkatan' => $this->input->post('lowongan_pekerjaan_angkatan'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s')
				];

				$insert = $this->Output_kegiatan_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Output_kegiatan_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$this->validation_for = 'update';
			$this->_validate();

			if ($this->form_validation->run() == FALSE) {
				$errors = [
					'id_kegiatan' => form_error('id_kegiatan'),
					'id_kategori_output' => form_error('id_kategori_output'),
					'dokumen_pdf' => form_error('dokumen_pdf'),
					'judul_penelitian_dosen' => form_error('judul_penelitian_dosen'),
					'judul_penelitian_mahasiswa' => form_error('judul_penelitian_mahasiswa'),
					'lingkup_pkl_mahasiswa' => form_error('lingkup_pkl_mahasiswa'),
					'kontribusi_dosen_wbi' => form_error('kontribusi_dosen_wbi'),
					'dosen_tamu_nama' => form_error('dosen_tamu_nama'),
					'dosen_tamu_topik' => form_error('dosen_tamu_topik'),
					'dosen_undang_nama' => form_error('dosen_undang_nama'),
					'dosen_undang_topik' => form_error('dosen_undang_topik'),
					'kurikulum_prodi' => form_error('kurikulum_prodi'),
					'kurikulum_ruang_lingkup' => form_error('kurikulum_ruang_lingkup'),
					'penyusunan_modul_prodi' => form_error('penyusunan_modul_prodi'),
					'penyusunan_modul_mk' => form_error('penyusunan_modul_mk'),
					'penyusunan_modul_ruang_lingkup' => form_error('penyusunan_modul_ruang_lingkup'),
					'beasiswa_jumlah' => form_error('beasiswa_jumlah'),
					'beasiswa_dana' => form_error('beasiswa_dana'),
					'investor_startup_jumlah' => form_error('investor_startup_jumlah'),
					'investor_startup_nama_bisnis' => form_error('investor_startup_nama_bisnis'),
					'investor_startup_jumlah_dana' => form_error('investor_startup_jumlah_dana'),
					'mbkm_mk' => form_error('mbkm_mk'),
					'mbkm_pic' => form_error('mbkm_pic'),
					'kerjasama_bisnis_jenis' => form_error('kerjasama_bisnis_jenis'),
					'lowongan_pekerjaan_jumlah_mahasiswa' => form_error('lowongan_pekerjaan_jumlah_mahasiswa'),
					'lowongan_pekerjaan_prodi' => form_error('lowongan_pekerjaan_prodi'),
					'lowongan_pekerjaan_angkatan' => form_error('lowongan_pekerjaan_angkatan')
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'id_kegiatan' => $this->input->post('id_kegiatan'),
					'id_kategori_output' => $this->input->post('id_kategori_output'),
					'dokumen_pdf' => $this->input->post('dokumen_pdf'),
					'judul_penelitian_dosen' => $this->input->post('judul_penelitian_dosen'),
					'judul_penelitian_mahasiswa' => $this->input->post('judul_penelitian_mahasiswa'),
					'lingkup_pkl_mahasiswa' => $this->input->post('lingkup_pkl_mahasiswa'),
					'kontribusi_dosen_wbi' => $this->input->post('kontribusi_dosen_wbi'),
					'dosen_tamu_nama' => $this->input->post('dosen_tamu_nama'),
					'dosen_tamu_topik' => $this->input->post('dosen_tamu_topik'),
					'dosen_undang_nama' => $this->input->post('dosen_undang_nama'),
					'dosen_undang_topik' => $this->input->post('dosen_undang_topik'),
					'kurikulum_prodi' => $this->input->post('kurikulum_prodi'),
					'kurikulum_ruang_lingkup' => $this->input->post('kurikulum_ruang_lingkup'),
					'penyusunan_modul_prodi' => $this->input->post('penyusunan_modul_prodi'),
					'penyusunan_modul_mk' => $this->input->post('penyusunan_modul_mk'),
					'penyusunan_modul_ruang_lingkup' => $this->input->post('penyusunan_modul_ruang_lingkup'),
					'beasiswa_jumlah' => $this->input->post('beasiswa_jumlah'),
					'beasiswa_dana' => $this->input->post('beasiswa_dana'),
					'investor_startup_jumlah' => $this->input->post('investor_startup_jumlah'),
					'investor_startup_nama_bisnis' => $this->input->post('investor_startup_nama_bisnis'),
					'investor_startup_jumlah_dana' => $this->input->post('investor_startup_jumlah_dana'),
					'mbkm_mk' => $this->input->post('mbkm_mk'),
					'mbkm_pic' => $this->input->post('mbkm_pic'),
					'kerjasama_bisnis_jenis' => $this->input->post('kerjasama_bisnis_jenis'),
					'lowongan_pekerjaan_jumlah_mahasiswa' => $this->input->post('lowongan_pekerjaan_jumlah_mahasiswa'),
					'lowongan_pekerjaan_prodi' => $this->input->post('lowongan_pekerjaan_prodi'),
					'lowongan_pekerjaan_angkatan' => $this->input->post('lowongan_pekerjaan_angkatan'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Output_kegiatan_model->update(['id_output_kegiatan' => $this->input->post('id_output_kegiatan')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate()
	{
		$this->form_validation->set_error_delimiters('', '');
		//$this->form_validation->set_rules('id_kegiatan', 'Judul Kegiatan', 'trim|required');
		$this->form_validation->set_rules('id_kategori_output', 'Kategori Data Kegiatan', 'trim|required');
		//$this->form_validation->set_rules('pic_kegiatan', 'PIC Kegiatan', 'trim|required');

		//$this->form_validation->set_message('required', '%s harus diisi');
		//$this->form_validation->set_message('numeric', 'Harus bernilai angka');


	}
}
