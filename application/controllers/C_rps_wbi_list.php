<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class C_rps_wbi_list extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rps_wbi_list_model', 'Rps_wbi_list_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
			//return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$output['judul_besar'] = 'List RPS WBI';
		$output['judul_kecil'] = '';
		$output['menu_aktif'] = 'm_rps_wbi_list';
		$output['parent_ws'] = 'active open';
		$output['content'] = 'rps_wbi_list/rps_wbi_list_view';
		$output['after_page'] = 'rps_wbi_list/rps_wbi_list_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Rps_wbi_list_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		$id_ptk = $this->session->userdata('id_ptk');


		foreach ($list as $l) {
			$no++;
			$koordinator_rps = $l->koordinator_rps;
			$row = [];
			if ($id_ptk == $koordinator_rps) {
				$html_preview = '<a href="' . site_url('C_rps_online/preview_rps/' . $l->id_rps) . '" class="btn btn-default btn-sm green-stripe" target="_new"  title="Preview RPS">
								' . $l->general_mk . ' </a>';
			} else {
				$html_preview =  $l->general_mk;
			}



			$mk_prodi = $this->db->where(['id_rps' => $l->id_rps])
				->select('*')->from('siakad_rps_mk_prodi mkp')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = mkp.id_mk')
				->join('feeder_mata_kuliah_kurikulum mkk', 'mkk.id_mk = mk.id_mk')
				->join('sms s', 's.id_sms = mk.id_sms')
				->group_by('mkp.id_mk')
				->get();

			$html_mk_prodi = '';
			$prodi = $kode_mk = $nm_mk = $smt = $sks = '';
			if ($mk_prodi->num_rows() > 0) {
				foreach ($mk_prodi->result() as $key => $value) {
					$prodi .= $value->nm_prodi_english . '<br>';
					$nm_mk .= '[' . $value->kode_mk . '] ' . $value->nm_mk . '<br>';
					$smt .= $value->smt . '<br>';
					$sks .= $value->sks_mk * 1 . '<br>';
				}
			}

			$row[] = $no;
			$row[] = $html_preview;
			$row[] = $l->nm_ptk . '<br>' . tgl_indo($l->tanggal_rps);
			$row[] = $l->nm_koordinator;
			$row[] = $prodi;
			$row[] = $nm_mk;
			$row[] = $smt;
			$row[] = $sks;



			if ($id_ptk == $koordinator_rps) {
				$row[] = "<a href='javascript:void(0)' class='btn btn-primary' onclick='edit_gembok(\"" . $l->id_rps . "\")'>" . $this->convert_icon($l->is_lock) . "</a>";
			} else {
				$row[] = "";
			}
			//$group = array(5, 12);
			/* if ($this->ion_auth->in_group($group)) {
				$row[] = "<a href='javascript:void(0)' class='btn btn-primary' onclick='edit_gembok(\"" . $l->id_rps . "\")'>" . $this->convert_icon($l->is_lock) . "</a>";
			} else {
				$row[] = '';
			}
 			*/
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Rps_wbi_list_model->count_all(),
			"recordsFiltered" => $this->Rps_wbi_list_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function preview_rps($id_rps = null)
	{
		$data_rps = $this->db->where('id_rps', $id_rps)
			->select('so.*, nm_ptk')
			->from('siakad_rps_online so')
			->join('siakad_dosen sd', 'so.id_ptk = sd.id_ptk')
			->get()->row();

		$data_rps_detail = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online_detail')->result();
		$mk_prodi = $this->db->where(['id_rps' => $id_rps])
			->select('*')->from('siakad_rps_mk_prodi mkp')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = mkp.id_mk')
			->join('feeder_mata_kuliah_kurikulum mkk', 'mkk.id_mk = mk.id_mk')
			->join('sms s', 's.id_sms = mk.id_sms')
			->get()->result();


		$wadir = $this->db->select('*')->from('users u')
			->join('users_groups ug', 'u.id = ug.user_id')
			->where('ug.group_id', 12)->get();

		$output['data_rps'] = $data_rps;
		$output['data_rps_detail'] = $data_rps_detail;
		$output['wadir_akademik'] = $wadir->row()->first_name;
		$output['rps_kelola'] =  'NIL';
		$output['mk_prodi'] =  $mk_prodi;

		$html = $this->load->view('cetak/pdf_rps_online', $output, true);
		$pdfFilePath  = 'RPS.pdf';
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	private function convert_icon($status)
	{
		if ($status == "N") {
			return "<i class='fa fa-unlock'></i> Boleh Edit Data";
		} else {
			return "<i class='fa fa-lock'></i> Tidak Boleh Edit Data";
		}
	}

	public function edit_gembok($id_rps)
	{
		$rps = $this->Rps_wbi_list_model->get_by_id($id_rps);
		$gembok = $rps->is_lock;

		if ($gembok == 'N') {
			$data = ['is_lock' => 'Y'];
		} else {
			$data = ['is_lock' => 'N'];
		}

		$this->Rps_wbi_list_model->update(['id_rps' => $id_rps], $data);
		echo json_encode(["status" => TRUE]);
	}
}
