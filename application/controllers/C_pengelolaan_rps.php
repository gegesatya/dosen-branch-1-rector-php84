<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pengelolaan_rps extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pengelolaan_rps_model', 'Pengelolaan_rps_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		/* $group = array('admin_akademik', 'operator_baa');
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Anda harus Administrator atau Operator BAA untuk melihat halaman ini.');
		} */
	}

	public function index()
	{


		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			$output['judul_besar'] = 'Anda tidak berhak mengakses halaman ini.';
			$output['judul_kecil'] = '';
			$output['isi'] = 'Halaman ini hanya bisa diakses oleh level KAPRODI.';
			$output['menu_aktif'] = 'm_pengelolaan_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'forbidden_page/forbidden_page_view';
			$output['after_page'] = 'forbidden_page/forbidden_page_after_page';
		} else {
			$output['judul_besar'] = 'Mata Kuliah Kurikulum';
			$output['judul_kecil'] = 'Politeknik WBI';
			$output['menu_aktif'] = 'm_pengelolaan_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'pengelolaan_rps/pengelolaan_rps_view';
			$output['after_page'] = 'pengelolaan_rps/pengelolaan_rps_after_page';
			$output['master_page'] = 'ajax_pagination';
			$output['dd_kurikulum'] = $this->Pengelolaan_rps_model->f_dropdown('feeder_kurikulum', 'id_kurikulum_sp', 'nm_kurikulum_sp');
			$output['dd_rps'] = $this->Pengelolaan_rps_model->f_dropdown_2('siakad_rps_online', 'id_rps', 'nm_mk');
		}

		$this->load->view('master_page', $output);
	}

	public function ajax_list($id_kurikulum)
	{
		$list = $this->Pengelolaan_rps_model->get_datatables($id_kurikulum);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $mk) {
			$id_rps = $this->db->where(['id_mk' => $mk->id_mk, 'id_kurikulum_sp' => $mk->id_kurikulum_sp])
				->get('siakad_rps_online_pengelolaan');

			$c = $id_rps->num_rows();

			$html = '<a class="btn btn-sm btn-primary btn-sm" 
						href="javascript:void()" 
						title="Edit" 
						onclick="edit_matakuliah(\'' . $mk->id_kurikulum_sp . '\',\'' . $mk->id_mk . '\')">
						<i class="glyphicon glyphicon-pencil"></i>
					</a>';

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $mk->nm_kurikulum_sp;
			$row[] = '[' . $mk->kode_mk . '] ' . $mk->nm_mk;
			$row[] = $mk->sks_mk * 1;
			$row[] = $mk->smt;
			$row[] = $mk->a_wajib == 1 ? 'Y' : 'N';

			if ($c > 0 && !empty($id_rps->row()->id_rps)) {
				$siakad_rps = $this->db->select('o.*, nm_ptk')
					->from('siakad_rps_online o')
					->join('siakad_dosen sd', 'o.id_ptk = sd.id_ptk')
					->where('id_rps', $id_rps->row()->id_rps)
					->get();

				$dt_rps = $siakad_rps->row()->kode_revisi . ' | ' . $siakad_rps->row()->nomor_dokumen . ' | ' . ucfirst(strtolower($siakad_rps->row()->nm_ptk));


				$html_preview = '<a href="' . site_url('C_rps_online/preview_rps/' . $id_rps->row()->id_rps) . '" class="btn btn-default btn-sm green-stripe" target="_new"  title="Preview RPS">
					' . $dt_rps . ' </a>';

				$row[] = $html_preview;
				//$row[] = $c;
			} else {
				$row[] = '-';
			}

			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pengelolaan_rps_model->count_all($id_kurikulum),
			"recordsFiltered" => $this->Pengelolaan_rps_model->count_filtered($id_kurikulum),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id_kurikulum_sp, $id_mk)
	{
		$data = $this->Pengelolaan_rps_model->get_by_id($id_kurikulum_sp, $id_mk);
		//$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; 
		echo json_encode($data);
	}

	public function ajax_update()
	{
		//$this->_validate();
		$data = [
			'id_rps' => $this->input->post('id_rps'),

		];

		$data = $this->security->xss_clean($data);
		$this->Pengelolaan_rps_model->update(['id_mk' => $this->input->post('id_mk')], $data);
		echo json_encode(["status" => TRUE]);
	}



	public function ajax_delete($id)
	{
		$this->Pengelolaan_rps_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}
}
