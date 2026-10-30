<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_browse_rps extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Browse_rps_model', 'Browse_rps_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			$output['judul_besar'] = 'Anda tidak berhak mengakses halaman ini.';
			$output['judul_kecil'] = '';
			$output['isi'] = 'Halaman ini hanya bisa diakses oleh level KAPRODI.';
			$output['menu_aktif'] = 'm_browse_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'forbidden_page/forbidden_page_view';
			$output['after_page'] = 'forbidden_page/forbidden_page_after_page';
		} else {
			/* $output['judul_besar'] = 'Mata Kuliah Kurikulum';
			$output['judul_kecil'] = 'Politeknik WBI';
			$output['menu_aktif'] = 'm_pengelolaan_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'pengelolaan_rps/pengelolaan_rps_view';
			$output['after_page'] = 'pengelolaan_rps/pengelolaan_rps_after_page';
			$output['master_page'] = 'ajax_pagination';
			$output['dd_kurikulum'] = $this->Pengelolaan_rps_model->f_dropdown('feeder_kurikulum', 'id_kurikulum_sp', 'nm_kurikulum_sp');
			$output['dd_rps'] = $this->Pengelolaan_rps_model->f_dropdown_2('siakad_rps_online', 'id_rps', 'nm_mk'); */
			$output['judul_besar'] = 'Daftar RPS Prodi';
			$output['judul_kecil'] = 'Politeknik WBI';
			$output['menu_aktif'] = 'm_browse_rps';
			$output['parent_ws'] = 'active open';
			$output['content'] = 'browse_rps/browse_rps_view';
			$output['after_page'] = 'browse_rps/browse_rps_after_page';
			$output['dd_cakupan_rps'] = $this->Browse_rps_model->f_dropdown();
			$output['dd_tipe_usulan'] = $this->Browse_rps_model->f_dropdown_tipe_usulan();
		}



		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Browse_rps_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = '<a href="' . site_url('C_rps_online/preview_rps/' . $l->id_rps) . '" class="btn btn-sm btn-default green-stripe" target="_new"  title="Preview RPS">
			 ' . $l->nm_mk . '</a>';
			$row[] = $l->nm_cakupan;
			$row[] = $l->kode_revisi;
			$row[] = $l->nomor_dokumen;
			$row[] = $l->nm_ptk;

			/* $html_preview = '<a href="' . site_url('C_rps_online/preview_rps/' . $l->id_rps) . '" class="btn btn-sm green-meadow" target="_new"  title="Preview RPS">
								<i class="fa fa-laptop"></i> </a>'; */

			/* if ($l->id_status_pengajuan_rps == 1) {
				$html = '<a href="javascript:void(0)" class="btn btn-sm green-meadow"  title="Upload Detail RPS"
						onclick="form_upload(\'' . $l->id_rps . '\')">
						<i class="fa fa-upload"></i> </a>';



				$html_submit = '<a class="btn btn-sm btn-primary btn-sm" 
									href="javascript:void()" 
									title="Submit RPS" 
									onclick="submit_rps(\'' . $l->id_rps . '\')">
									<i class="fa fa-send-o"></i>
								</a>';

				$row[] = $html;
				$row[] = $html_preview;
				$row[] = $html_submit;

				$row[] = '<a class="btn btn-sm btn-primary btn-sm" 
						 href="javascript:void()" 
						 title="Edit" 
						 onclick="edit_rps(\'' . $l->id_rps . '\')">
						 <i class="glyphicon glyphicon-pencil"></i>
					  </a>
				      <a class="btn btn-sm btn-danger btn-sm" 
				         href="javascript:void()" 
				         title="Delete" onclick="delete_rps(\'' . $l->id_rps . '\')">
				         <i class="glyphicon glyphicon-trash"></i>
				      </a>';
			} else {
				$row[] = '-';
				$row[] = $html_preview;
				$row[] = '-';
				$row[] = '-';
			} */

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Browse_rps_model->count_all(),
			"recordsFiltered" => $this->Browse_rps_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Browse_rps_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_delete($id)
	{
		$this->db->where('id_rps', $id)->delete('siakad_rps_online_detail');
		$this->db->where('id_rps', $id)->delete('siakad_rps_online');
		echo json_encode(["status" => TRUE, "pesan" => "Data berhasil dihapus!"]);
	}

	public function preview_rps($id_rps = null)
	{
		$data_rps = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online')->row();

		$data_rps_detail = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online_detail')->result();

		$output['data_rps'] = $data_rps;
		$output['data_rps_detail'] = $data_rps_detail;

		$html = $this->load->view('cetak/pdf_rps_online', $output, true);
		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function download_template()
	{
		$this->load->helper('download');
		force_download('rps_online_excel/template_rps_detail.xlsx', NULL);
	}
}
