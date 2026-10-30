<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_daftar_mou extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lapkerma/Daftar_mou_model', 'Daftar_mou_model');
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
		$output['content'] = 'lapkerma/daftar_mou/daftar_mou_view';
		$output['after_page'] = 'lapkerma/daftar_mou/daftar_mou_after_page';
		$output['judul_besar'] = 'Daftar MOU';
		$output['judul_kecil'] = 'Aktif WBI';
		$output['menu_aktif'] 		= 'm_daftar_mou';
		$output['parent_lapkerma']	= 'active open';

		$this->load->view('master_page', $output);
	}

	public function edit_excel_mou()
	{
		$mou = $this->db->get('simlapkerma_tbl_daftar_mou')->result();

		foreach ($mou as $key => $value) {
			$nm_mitra = $value->nama_mitra;
			$id_daftar_instansi = $this->db->where('nm_instansi', $nm_mitra)->get('simlapkerma_tbl_daftar_instansi')->row()->id_daftar_instansi;
			$id_mou = $value->id_mou;

			$this->db->where(compact('id_mou'))->update('simlapkerma_tbl_daftar_mou', ['id_daftar_instansi' => $id_daftar_instansi]);
		}
	}

	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		} else {
			$data = $this->data_main();

			$output = [
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->Daftar_mou_model->count_all(),
				"recordsFiltered" => $this->Daftar_mou_model->count_filtered(),
				"data" => $data
			];

			echo json_encode($output);
		}
	}

	private function data_main()
	{
		$list = $this->Daftar_mou_model->get_datatables();
		$this->load->helper('date');

		$data = [];
		$no = $_POST['start'];

		foreach ($list as $b) {

			$tgl_awal = strtotime($b->tanggal_awal);
			$tgl_akhir = strtotime('+2 day', strtotime($b->tanggal_akhir));
			if (strtotime($b->tanggal_akhir) < now()) {
				$expire = 'Sudah berakhir';
			} else {
				$expire =  timespan(now(), $tgl_akhir, 3);
			}

			$no++;
			$row = [];
			$row[] = $no;
			if (!empty($b->dokumen_mou)) {
				$row[] = '<small>' . $b->nomor_mou . '<br>Penandatangan MOU : ' . $b->penandatangan_wbi . '</small><br>' . $b->judul_kerjasama . '<br><a href="' . $b->dokumen_mou . '" target="_new" class="btn btn-xs btn-success">Buka Dokumen</a>';
			} else {
				$row[] = '<small>' . $b->nomor_mou . '<br>Penandatangan MOU : ' . $b->penandatangan_wbi . '</small><br>' . $b->judul_kerjasama;
			}
			$row[] = $b->nm_instansi;
			$row[] = $b->nm_status_kerjasama;
			$row[] = '<small>' . tgl_indo_short($b->tanggal_awal) . ' - ' . tgl_indo_short($b->tanggal_akhir) . '<br>' . timespan($tgl_awal, $tgl_akhir, 1) . '<br>Expire in: ' . $expire . '</small>';

			$row[] = '<a class="btn btn-primary btn-sm" 
						 title="Edit" 
						 onclick="edit_kerjasama(' . "'" . $b->id_mou . "'" . ')"
						 href="javascript:void()" >
						 <i class="fa fa-edit"></i> Lihat MOU
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
			$data = $this->Daftar_mou_model->get_by_id($id);
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
					//'id_dokumen_kerjasama' 	=> form_error('id_dokumen_kerjasama'),
					'nomor_mou' 		=> form_error('nomor_mou'),
					'judul_kerjasama' 		=> form_error('judul_kerjasama'),
					'deskripsi_kerjasama' 	=> form_error('deskripsi_kerjasama'),
					'id_status_kerjasama' 		=> form_error('id_status_kerjasama'),
					'tanggal_awal' 			=> form_error('tanggal_awal'),
					'tanggal_akhir' 		=> form_error('tanggal_akhir'),
					'id_daftar_instansi' 		=> form_error('id_daftar_instansi'),
					'penandatangan_mitra' 	=> form_error('penandatangan_mitra'),
					'jabatan_mitra' 		=> form_error('jabatan_mitra'),
					'penandatangan_wbi' 	=> form_error('penandatangan_wbi'),
					'jabatan_wbi' 		=> form_error('jabatan_wbi'),
					//'alamat_mitra' 			=> form_error('alamat_mitra'),
					//'id_sub_instansi' 		=> form_error('id_sub_instansi'),
					//'id_bidang_usaha' 		=> form_error('id_bidang_usaha'),
					//'magang_mpi' 		=> form_error('magang_mpi'),
					//'magang_ap' 		=> form_error('magang_ap'),
					//'magang_ah' 		=> form_error('magang_ah'),
					//'magang_pka' 		=> form_error('magang_pka'),
					//'magang_trpl' 		=> form_error('magang_trpl'),
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
					'nomor_mou' => $this->input->post('nomor_mou'),
					'judul_kerjasama' => $this->input->post('judul_kerjasama'),
					'deskripsi_kerjasama' => $this->input->post('deskripsi_kerjasama'),
					'id_status_kerjasama' => $this->input->post('id_status_kerjasama'),
					'tanggal_awal' => $this->input->post('tanggal_awal'),
					'tanggal_akhir' => $this->input->post('tanggal_akhir'),
					'id_daftar_instansi' => $this->input->post('id_daftar_instansi'),
					'penandatangan_mitra' => $this->input->post('penandatangan_mitra'),
					'jabatan_mitra' => $this->input->post('jabatan_mitra'),
					'penandatangan_wbi' => $this->input->post('penandatangan_wbi'),
					'jabatan_wbi' => $this->input->post('jabatan_wbi'),
					'bentuk_kerjasama' => $this->input->post('bentuk_kerjasama'),
					//'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'dokumen_mou' => $this->input->post('dokumen_mou'),
					'create_by' => $user->first_name,
					'create_date' => date('Y-m-d H:i:s'),
				];

				$insert = $this->Daftar_mou_model->save($data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	public function ajax_delete($id)
	{
		$this->Daftar_mou_model->delete_by_id($id);
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
					'nomor_mou' 		=> form_error('nomor_mou'),
					'judul_kerjasama' 		=> form_error('judul_kerjasama'),
					'deskripsi_kerjasama' 	=> form_error('deskripsi_kerjasama'),
					'id_status_kerjasama' 		=> form_error('id_status_kerjasama'),
					'tanggal_awal' 			=> form_error('tanggal_awal'),
					'tanggal_akhir' 		=> form_error('tanggal_akhir'),
					'id_daftar_instansi' 		=> form_error('id_daftar_instansi'),
					'penandatangan_mitra' 	=> form_error('penandatangan_mitra'),
					'jabatan_mitra' 		=> form_error('jabatan_mitra'),
					'penandatangan_wbi' 	=> form_error('penandatangan_wbi'),
					'jabatan_wbi' 		=> form_error('jabatan_wbi'),
					//'id_bidang_usaha' 		=> form_error('id_bidang_usaha'),

				];
				$data = [
					'status' 		=> FALSE,
					'errors' 		=> $errors
				];
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			} else {
				$user = $this->ion_auth->user()->row();
				$data = [
					'nomor_mou' => $this->input->post('nomor_mou'),
					'judul_kerjasama' => $this->input->post('judul_kerjasama'),
					'deskripsi_kerjasama' => $this->input->post('deskripsi_kerjasama'),
					'id_status_kerjasama' => $this->input->post('id_status_kerjasama'),
					'tanggal_awal' => $this->input->post('tanggal_awal'),
					'tanggal_akhir' => $this->input->post('tanggal_akhir'),
					'id_daftar_instansi' => $this->input->post('id_daftar_instansi'),
					'penandatangan_mitra' => $this->input->post('penandatangan_mitra'),
					'jabatan_mitra' => $this->input->post('jabatan_mitra'),
					'penandatangan_wbi' => $this->input->post('penandatangan_wbi'),
					'jabatan_wbi' => $this->input->post('jabatan_wbi'),
					'bentuk_kerjasama' => $this->input->post('bentuk_kerjasama'),
					//'id_bidang_usaha' => $this->input->post('id_bidang_usaha'),
					'dokumen_mou' => $this->input->post('dokumen_mou'),
					'change_by' => $user->first_name,
					'change_date' => date('Y-m-d H:i:s'),
				];

				$this->Daftar_mou_model->update(['id_mou' => $this->input->post('id_mou')], $data);
				$this->output->set_content_type('application/json')->set_output(json_encode(['status' => TRUE]));
			}
		}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('nomor_mou', 'Nomor MOU', 'trim|required');
			$this->form_validation->set_rules('judul_kerjasama', 'Judul Kerjasama', 'trim|required');
			$this->form_validation->set_rules('deskripsi_kerjasama', 'Deskripsi Kerjasama', 'trim|required');
			$this->form_validation->set_rules('id_status_kerjasama', 'Status Kerjasama', 'trim|required');
			$this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'trim|required');
			$this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('id_daftar_instansi', 'Instansi Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_mitra', 'Penandatangan Mitra', 'trim|required');
			$this->form_validation->set_rules('jabatan_mitra', 'Jabatan Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_wbi', 'Penandatangan WBI', 'trim|required');
			$this->form_validation->set_rules('jabatan_wbi', 'Jabatan WBI', 'trim|required');
			//$this->form_validation->set_rules('id_bidang_usaha', 'Bidang Usaha', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
			$this->form_validation->set_message('numeric', 'Harus bernilai angka');
		} else {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('nomor_mou', 'Nomor MOU', 'trim|required');
			$this->form_validation->set_rules('judul_kerjasama', 'Judul Kerjasama', 'trim|required');
			$this->form_validation->set_rules('deskripsi_kerjasama', 'Deskripsi Kerjasama', 'trim|required');
			$this->form_validation->set_rules('id_status_kerjasama', 'Status Kerjasama', 'trim|required');
			$this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'trim|required');
			$this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'trim|required');
			$this->form_validation->set_rules('id_daftar_instansi', 'Instansi Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_mitra', 'Penandatangan Mitra', 'trim|required');
			$this->form_validation->set_rules('jabatan_mitra', 'Jabatan Mitra', 'trim|required');
			$this->form_validation->set_rules('penandatangan_wbi', 'Penandatangan WBI', 'trim|required');
			$this->form_validation->set_rules('jabatan_wbi', 'Jabatan WBI', 'trim|required');
			//$this->form_validation->set_rules('id_bidang_usaha', 'Bidang Usaha', 'trim|required');
			$this->form_validation->set_message('required', '%s harus diisi');
			$this->form_validation->set_message('numeric', 'Harus bernilai angka');
		}
	}
}
