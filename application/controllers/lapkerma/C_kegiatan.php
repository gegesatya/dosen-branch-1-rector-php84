<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_kegiatan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Kegiatan_model', 'Kegiatan_model');
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
		$output['content'] = 'lapkerma/kegiatan/kegiatan_view';
		$output['after_page'] = 'lapkerma/kegiatan/kegiatan_after_page';
		$output['judul_besar'] = 'Laporan Kegiatan';
		$output['judul_kecil'] = 'WBI';
		$output['menu_aktif'] 		= 'm_kegiatan';
		$output['parent_lapkerma']	= 'active open';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->data_main();

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Kegiatan_model->count_all(),
				"recordsFiltered" => $this->Kegiatan_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Kegiatan_model->get_datatables();

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {
			$html = '';
			if (empty($b->nomor_dokumen_moa)) {
				$html = '<span class="label label-danger">MOA Tidak Ada</span>';
			} else {
				$html = $b->nomor_dokumen_moa . '<small><br>' . $b->judul_moa . '<br>Status: ' . $b->nm_status_kerjasama  . '</small>';
			}

			if (!empty($b->dokumen_pdf)) {
				$link = "<a href='" . $b->dokumen_pdf . "' title='Download Laporan dari G-Drive' class='btn btn-success btn-sm' target='_new'><i class='fa fa-download'></i></a>";
			} else {
				$link = '';
			}
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = 'Judul Kegiatan : <b>' . $b->judul_kegiatan . '</b><br><br>Tanggal : ' . tgl_indo_short($b->tanggal_awal_kegiatan) . ' - ' . tgl_indo_short($b->tanggal_akhir_kegiatan) . '<br>PIC Kegiatan: ' . $b->pic_kegiatan;
			$row[] = $html;
			//$row[] = $b->nm_instansi . '<small><br>Bdg Kerjasama: ' . $b->nm_bidang_kerjasama . '</small>';
			$row[] = $b->nm_bentuk_kegiatan;
			// $row[] = $link;

			$row[] = $link . ' <a class="btn btn-primary btn-sm" 
						 title="Edit" 
						 onclick="edit_kegiatan(\'' . $b->id_kegiatan . '\')"
						 href="javascript:void(0)" >
						 <i class="fa fa-edit"></i>
					  </a>
					  <a class="btn btn-danger btn-sm" 
						 title="delete" 
						 onclick="delete_kegiatan(\'' . $b->id_kegiatan . '\')"
						 href="javascript:void(0)" >
						 <i class="fa fa-times"></i>
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
			$data = $this->Kegiatan_model->get_by_id($id);
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
					'id_daftar_instansi' => form_error('id_daftar_instansi'),
					'judul_kegiatan' => form_error('judul_kegiatan'),
					'id_bentuk_kegiatan' => form_error('id_bentuk_kegiatan'),
					'lokasi_kegiatan' => form_error('lokasi_kegiatan'),
					'hasil' => form_error('hasil'),
					'laporan_singkat_kegiatan' => form_error('laporan_singkat_kegiatan'),
					'saran' => form_error('saran'),
					'dokumen_pdf' => form_error('dokumen_pdf'),
					'galeri_foto' => form_error('galeri_foto'),
					'pic_kegiatan' => form_error('pic_kegiatan'),
					'handphone_pic' => form_error('handphone_pic'),
					'tanggal_awal_kegiatan' => form_error('tanggal_awal_kegiatan'),
					'tanggal_akhir_kegiatan' => form_error('tanggal_akhir_kegiatan'),
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
					'id_daftar_instansi' => $this->input->post('id_daftar_instansi'),
					'id_moa' => $this->input->post('id_moa'),
					'judul_kegiatan' => $this->input->post('judul_kegiatan'),
					'lokasi_kegiatan' => $this->input->post('lokasi_kegiatan'),
					'id_bentuk_kegiatan' => $this->input->post('id_bentuk_kegiatan'),
					'hasil' => $this->input->post('hasil'),
					'laporan_singkat_kegiatan' => $this->input->post('laporan_singkat_kegiatan'),
					'saran' => $this->input->post('saran'),
					'dokumen_pdf' => $this->input->post('dokumen_pdf'),
					'galeri_foto' => $this->input->post('galeri_foto'),
					'pic_kegiatan' => $this->input->post('pic_kegiatan'),
					'handphone_pic' => $this->input->post('handphone_pic'),
					'tanggal_awal_kegiatan' => $this->input->post('tanggal_awal_kegiatan'),
					'tanggal_akhir_kegiatan' => $this->input->post('tanggal_akhir_kegiatan'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Kegiatan_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Kegiatan_model->delete_by_id($id);
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
					// 'id_moa' => form_error('id_moa'),
					'id_daftar_instansi' => form_error('id_daftar_instansi'),
					'judul_kegiatan' => form_error('judul_kegiatan'),
					'id_bentuk_kegiatan' => form_error('id_bentuk_kegiatan'),
					'lokasi_kegiatan' => form_error('lokasi_kegiatan'),
					'hasil' => form_error('hasil'),
					'laporan_singkat_kegiatan' => form_error('laporan_singkat_kegiatan'),
					'saran' => form_error('saran'),
					'dokumen_pdf' => form_error('dokumen_pdf'),
					'galeri_foto' => form_error('galeri_foto'),
					'pic_kegiatan' => form_error('pic_kegiatan'),
					'handphone_pic' => form_error('handphone_pic'),
					'tanggal_awal_kegiatan' => form_error('tanggal_awal_kegiatan'),
					'tanggal_akhir_kegiatan' => form_error('tanggal_akhir_kegiatan'),
				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'id_daftar_instansi' => $this->input->post('id_daftar_instansi'),
					'id_moa' => $this->input->post('id_moa'),
					'judul_kegiatan' => $this->input->post('judul_kegiatan'),
					'lokasi_kegiatan' => $this->input->post('lokasi_kegiatan'),
					'id_bentuk_kegiatan' => $this->input->post('id_bentuk_kegiatan'),
					'hasil' => $this->input->post('hasil'),
					'laporan_singkat_kegiatan' => $this->input->post('laporan_singkat_kegiatan'),
					'saran' => $this->input->post('saran'),
					'dokumen_pdf' => $this->input->post('dokumen_pdf'),
					'galeri_foto' => $this->input->post('galeri_foto'),
					'pic_kegiatan' => $this->input->post('pic_kegiatan'),
					'handphone_pic' => $this->input->post('handphone_pic'),
					'tanggal_awal_kegiatan' => $this->input->post('tanggal_awal_kegiatan'),
					'tanggal_akhir_kegiatan' => $this->input->post('tanggal_akhir_kegiatan'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Kegiatan_model->update(['id_kegiatan' => $this->input->post('id_kegiatan')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('id_daftar_instansi', 'Instansi/Mitra', 'trim|required');
		$this->form_validation->set_rules('judul_kegiatan', 'Judul Kegiatan', 'trim|required');
		$this->form_validation->set_rules('lokasi_kegiatan', 'Lokasi Kegiatan', 'trim|required');
		$this->form_validation->set_rules('id_bentuk_kegiatan', 'Bentuk Kegiatan', 'trim|required');
		$this->form_validation->set_rules('pic_kegiatan', 'PIC Kegiatan', 'trim|required');
		$this->form_validation->set_rules('hasil', 'Hasil Kegiatan', 'trim|required');
		$this->form_validation->set_rules('laporan_singkat_kegiatan', 'Laporan Singkat', 'trim|required');
		$this->form_validation->set_rules('saran', 'Saran', 'trim|required');
		$this->form_validation->set_rules('tanggal_awal_kegiatan', 'Tanggal Awal', 'trim|required');
		$this->form_validation->set_rules('tanggal_akhir_kegiatan', 'Tanggal Akhir', 'trim|required');

		$this->form_validation->set_message('required', '%s harus diisi');
		$this->form_validation->set_message('numeric', 'Harus bernilai angka');
	}
}
