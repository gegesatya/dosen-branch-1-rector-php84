<?php

defined('BASEPATH') or exit('No direct script access allowed');



class C_eoffice_handling_time extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Eoffice_handling_time_model', 'Eoffice_handling_time_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{

		$output['judul_besar'] = 'E-Office Handling Time';
		$output['judul_kecil'] = ' Monitoring dan Evaluasi';
		$output['menu_aktif'] = 'm_eoffice_handling_time';
		$output['parent_data_center'] = 'active open';
		$output['content'] = 'eoffice_handling_time/eoffice_handling_time_view';
		$output['after_page'] = 'eoffice_handling_time/eoffice_handling_time_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Eoffice_handling_time_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];

			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $rg->nomor_surat . '<br>' . $rg->tanggal_surat;
			$row[] = $rg->nm_kategori_pengantar;
			$row[] = '<small><i>' . tgl_indo_short(date('Y-m-d', strtotime($rg->create_date))) . '<br>' . date('H:i', strtotime($rg->create_date)) . '</i></small>';
			if (!empty($rg->tgl_submit)) {
				$row[] = '<small><i>' . tgl_indo_short(date('Y-m-d', strtotime($rg->tgl_submit))) . '<br>' . date('H:i', strtotime($rg->tgl_submit)) . '<br>By: ' . $rg->approve_1_by . '</i></small>';
			} else {
				$row[] = '';
			}
			if (!empty($rg->tgl_approve)) {
				$row[] = '<small><i>' . tgl_indo_short(date('Y-m-d', strtotime($rg->tgl_approve))) . '<br>' . date('H:i', strtotime($rg->tgl_approve)) . '<br>By: ' . $rg->approve_2_by . '</i></small>';
			} else {
				$row[] = '';
			}
			if (!empty($rg->tgl_submit)) {
				$row[] = '<small><i>' . $this->hitung_waktu($rg->create_date, $rg->tgl_submit) . '</i></small>';
			} else {
				$row[] = '';
			}
			if (!empty($rg->tgl_approve) && !empty($rg->tgl_submit)) {
				$row[] = '<small><i>' . $this->hitung_waktu($rg->tgl_submit, $rg->tgl_approve) . '</i></small>';
			} else {
				$row[] = '';
			}

			if (!empty($rg->tgl_approve) && !empty($rg->tgl_submit)) {
				$row[] = '<small><i>' . $this->hitung_waktu($rg->create_date, $rg->tgl_approve) . '</i></small>';
			} else {
				$row[] = '';
			}

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Eoffice_handling_time_model->count_all(),
			"recordsFiltered" => $this->Eoffice_handling_time_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	private function hitung_waktu($tanggal, $tanggal2)
	{
		$date1 = new DateTime(date($tanggal));
		$date2  = new DateTime(date($tanggal2));
		$diff = $date2->diff($date1);
		if ($diff->format('%d') == 0) {
			if ($diff->format('%h') == 0) {
				$a = $diff->format('%i min');
			} else {
				$a = $diff->format('%h hrs, %i min');
			}
		} else {
			$a = $diff->format('%d day, %h hrs, %i min');
		}

		return $a;
	}




	//gege
	public function cetak_pdf_eoffice($id_eoffice)
	{
		$data_eoffice = $this->db->where('id_eoffice', $id_eoffice)
			->select('mp.*,m.*, e.*, s.nm_lemb')
			->from('e_office e')
			->join('mahasiswa_pt mp', 'mp.nipd = e.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->get()->row();

		if ($data_eoffice->id_kategori_pengantar == 1) {
			$output['top'] = 560;
			$output['left'] = 500;
		} elseif ($data_eoffice->id_kategori_pengantar == 2) {
			$output['top'] = 700;
			$output['left'] = 480;
		} elseif ($data_eoffice->id_kategori_pengantar == 3) {
			$output['top'] = 600;
			$output['left'] = 480;
		}

		$output['data_eoffice'] = $data_eoffice;
		//$this->load->view('cetak/pdf_eoffice', $output);

		$html = $this->load->view('cetak/pdf_eoffice', $output, true);
		$pdfFilePath  = '1.pdf';

		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 'format' => 'Letter-P', 'margin_top' => 8,
			'margin_left' => 24,
			'margin_right' => 16,
		]);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}
}
