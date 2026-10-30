<?php

defined('BASEPATH') or exit('No direct script access allowed');



class C_eoffice_konfirmasi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Eoffice_konfirmasi_model', 'Eoffice_konfirmasi_model');
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

		$output['judul_besar'] = 'Konfirmasi Permohonan Mahasiswa';
		$output['judul_kecil'] = ' Politeknik WBI';
		$output['menu_aktif'] = 'm_konfirmasi';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'eoffice_konfirmasi/eoffice_konfirmasi_view';
		$output['after_page'] = 'eoffice_konfirmasi/eoffice_konfirmasi_after_page';
		//$output['dd_pelaporan'] 	= $this->Eoffice_konfirmasi_model->f_drop_down('simawa_status_pelaporan', 'id_status', 'status_pelaporan');

		$this->load->view('master_page', $output);
	}

	public function ajax_list($status)
	{
		$list = $this->Eoffice_konfirmasi_model->get_datatables($status);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];
			$st = '';

			if ($rg->id_status == 1) {
				$tgl = $rg->create_date;
				$st = 'Permohonan Mahasiswa <br>(' . $tgl . ')';
			} elseif ($rg->id_status == 2) {
				$tgl = $rg->tgl_dinomori;
				$st = 'Konfirmasi Administrasi <br>(' . $tgl . ')';
			} elseif ($rg->id_status == 3) {
				$tgl = $rg->tgl_submit;
				$st = 'Menunggu Konfirmasi Kaprodi<br>(' . $tgl . ')';
			} elseif ($rg->id_status == 4) {
				$tgl = $rg->tgl_approve;
				$st = 'Selesai <br>(' . $tgl . ')';
			}
			$html_preview = '<a href="' . site_url('C_admin_eoffice_general/cetak_pdf_eoffice') .
				'/' . $rg->id_eoffice . '" target="new" class="btn btn-sm btn-default green-stripe">
						<i class="fa fa-bar-chart-o"></i></a>';

			$krs_html = '<a href="javascript:void(0)" class="btn btn-default btn-sm blue-stripe"
					onclick="show_krs(' . "'" . $rg->nipd . "'" . ')">
					<i class="fa fa-info-circle"></i></a> 
					</a>';
			$row[] = $html_preview;
			$row[] = $krs_html;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $rg->nomor_surat . '<br>' . $rg->tanggal_surat;
			$row[] = $rg->nm_kategori_pengantar;

			$row[] = $st;

			if ($rg->id_status == 3) {
			}



			if ($rg->id_status == 3) {
				$row[] = '<a href="javascript:void(0)" class="btn btn-sm btn-default green-stripe" style="margin-right:0px !important" title="Konfirmasi"
							onclick="view_permohonan(\'' . $rg->id_eoffice . '\')"><i class="fa fa-search"></i> Konfirmasi</a>';
			} else {
				$row[] = '';
			}



			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Eoffice_konfirmasi_model->count_all($status),
			"recordsFiltered" => $this->Eoffice_konfirmasi_model->count_filtered($status),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Eoffice_konfirmasi_model->get_by_id($id);

		$id_reg_pd  = $data->id_reg_pd_feeder;
		$kuliah_mahasiswa = $this->db->where('id_reg_pd', $id_reg_pd)->from('kuliah_mahasiswa')->get()->result();

		$data_akademik = '<ul>';
		foreach ($kuliah_mahasiswa as $key => $value) {
			$data_akademik .= '<li>Semester : ' . $value->id_smt . ' | IPS : ' . $value->ips . ' | IPK : ' . $value->ipk . ' | SKS : ' . $value->sks_total . ' </li>';
		}

		$data_akademik .= '</ul>';
		$data->academic_record = $data_akademik;
		
		echo json_encode($data);
	}

	public function ajax_update_stempel()
	{
		$pengguna = $this->ion_auth->user()->row();
		$data = [
			'is_boleh_download' => 'Y',
			'id_status' => 4,
			'tgl_approve' => date('Y-m-d H:i:s'),
			'approve_2_by' => $pengguna->first_name
		];

		
		$this->Eoffice_konfirmasi_model->update(['id_eoffice' => $this->input->post('id_eoffice')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function submit_cetak($id_eoffice)
	{
		$data = [
			'id_status' => 2,
			'tgl_dicetak' => date('Y-m-d H:i:s'),
			'is_cetak' => 'Y',
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Eoffice_konfirmasi_model->update(['id_eoffice' => $id_eoffice], $data);

		$data = $this->Eoffice_konfirmasi_model->get_by_id($id_eoffice);
		$pekerjaan_ayah = $data->pekerjaan_ayah;
		$pekerjaan_ibu = $data->pekerjaan_ibu;

		if (!empty($pekerjaan_ayah)) {
			$data->nm_pekerjaan = $pekerjaan_ayah;
		} else {
			$data->nm_pekerjaan = $pekerjaan_ibu;
		}

		$output['nipd'] = $data->nipd;
		$output['nm_pd'] = $data->nm_pd;
		$output['nomor_surat'] = $data->nomor_surat;
		$output['tanggal_surat'] = date('d-m-Y', strtotime($data->tanggal_surat));
		$output['tmpt_lahir'] = $data->tmpt_lahir;
		$output['tgl_lahir'] = date('d-m-Y', strtotime($data->tgl_lahir));
		$output['telepon_seluler'] = $data->telepon_seluler;
		$output['jln'] = $data->jln;
		$output['nm_pekerjaan'] = $data->nm_pekerjaan;
		$output['nm_lemb'] = $data->nm_lemb;

		$this->load->view('cetak/cetak_surat_pengantar_pkl', $output);
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
