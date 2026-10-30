<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_revisi_nilai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Revisi_nilai_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		// $group = array('admin_akademik', 'operator_baa');
		// if (!$this->ion_auth->in_group($group)) {
		// 	return show_error('Anda harus Administrator atau Operator BAA untuk melihat halaman ini.');
		// }
		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Revisi Nilai ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_revisi_nilai';
		$output['parent_data_center'] = 'active open';
		$output['content'] 			= 'revisi_nilai/revisi_nilai_view';
		$output['after_page'] 		= 'revisi_nilai/revisi_nilai_after_page';
		$output['master_page'] 		= 'ajax_pagination';
		$output['dd_memo_revisi'] 	= $this->Revisi_nilai_model->f_memo_revisi();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Revisi_nilai_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;

			$username = $this->ion_auth->user()->row()->email;
			$data_akses = $this->db->where(['username' => $username, 'roles' => 'direktur'])->get('siakad_akses_data_center');

			if ($data_akses->num_rows() > 0) {
				if ($rg->is_memo_revisi == 1) {
					$aksi = '<a class="btn btn-primary btn-xs btn-block margin-bottom-5" 
								href="' . SOURCEPATH_REVISI_NILAI . $rg->file_kelengkapan . '.pdf' . '" target="_new" 
								title="Download Berkas" >
								<i class="glyphicon glyphicon-download-alt"></i> Download Berkas
							</a>
							<a class="btn btn-primary btn-xs btn-block" 
								href="javascript:void(0)" 
								title="Approve Revisi" 
								onclick="edit(\'' . addslashes($rg->id_kls) . '\',\'' . addslashes($rg->nipd) . '\')">
								<i class="glyphicon glyphicon-pencil"></i> Approve Revisi
							</a>';
				} else {
					$aksi = '<a class="btn btn-primary btn-xs btn-block margin-bottom-5" 
								href="' . SOURCEPATH_REVISI_NILAI . $rg->file_kelengkapan . '.pdf' . '" target="_new" 
								title="Download Berkas" >
								<i class="glyphicon glyphicon-download-alt"></i> Download Berkas
							</a>';
				}
			} else {
				$aksi = '<a class="btn btn-primary btn-xs btn-block margin-bottom-5" 
								href="' . SOURCEPATH_REVISI_NILAI . $rg->file_kelengkapan . '.pdf' . '" target="_new" 
								title="Download Berkas" >
								<i class="glyphicon glyphicon-download-alt"></i> Download Berkas
							</a>';
			}

			$row = [];
			$row[] = $no;
			$row[] = $rg->tanggal_diajukan;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $rg->kode_mk . " | " . $rg->nm_mk;
			$row[] = $rg->nilai_angka;
			$row[] = $rg->nilai_huruf;
			$row[] = $rg->nilai_indeks;

			$row[] = $aksi;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Revisi_nilai_model->count_all(),
			"recordsFiltered" => $this->Revisi_nilai_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id_kls, $nipd)
	{
		$data = $this->Revisi_nilai_model->get_by_id($id_kls, $nipd);
		echo json_encode($data);
	}



	public function ajax_update()
	{
		$this->_validate();

		$data = [
			'nilai_angka' => $this->input->post('nilai_angka_revisi'),
			'nilai_huruf' => $this->input->post('nilai_huruf_revisi'),
			'nilai_indeks' => $this->input->post('nilai_indeks_revisi'),
			'is_memo_revisi' => $this->input->post('is_memo_revisi'),
			'tanggal_approve_memo' => date('Y-m-d H:i:s')
		];
		$this->Revisi_nilai_model->update(['id_kls' => $this->input->post('id_kls'), 'nipd' => $this->input->post('nipd')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('is_memo_revisi') == '') {
			$data['inputerror'][] = 'is_memo_revisi';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
