<?php

defined('BASEPATH') or exit('No direct script access allowed');



class C_konfirmasi_magang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Konfirmasi_magang_model');
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

		$output['judul_besar'] = 'Konfirmasi Pemagangan';
		$output['judul_kecil'] = ' Politeknik WBI';
		$output['menu_aktif'] = 'm_konfirmasi_magang';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'konfirmasi_magang/konfirmasi_magang_view';
		$output['after_page'] = 'konfirmasi_magang/konfirmasi_magang_after_page';
		$output['dd_dpl'] 	= $this->Konfirmasi_magang_model->f_drop_down('siakad_dosen', 'nm_ptk', 'nm_ptk');

		$this->load->view('master_page', $output);
	}

	public function ajax_list($hasil)
	{
		$list = $this->Konfirmasi_magang_model->get_datatables($hasil);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];
			$st = '';

			if ($rg->hasil == 0) {
				$tgl = tgl_indo(date('Y-m-d', strtotime($rg->create_date)));
				$st = 'Pendaftaran Oleh Mahasiswa <br>(Tanggal : ' . $tgl . ', Jam : ' . date('H:i', strtotime($rg->create_date)) . ')';
			} elseif ($rg->hasil == 1) {
				$tgl_approve = $rg->tanggal_approve ? tgl_indo(date('Y-m-d', strtotime($rg->tanggal_approve))) : '';
				$st = 'Permohonan Magang sudah dikonfirmasi <br>Dikonfirmasi Oleh : ' . $rg->approve_by . '<br>Tanggal : ' . $tgl_approve;
			}

			$row[] = $no;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $rg->nm_instansi . '<br>Job Title : ' . $rg->job_title . '<br>Tgl Pendaftaran : ' . tgl_indo_short(date('Y-m-d', strtotime($rg->tanggal_awal_pendaftaran))) . ' s.d. ' . tgl_indo_short(date('Y-m-d', strtotime($rg->tanggal_akhir_pendaftaran)));
			$row[] = $st;

			$row[] = '<a href="' . site_url('C_lihat_cv/download_cv/' . $rg->nipd) . '" target="_blank" class="btn btn-default btn-xs blue-stripe">
					<i class="fa fa-info-circle"></i> Lihat CV</a>';
			if ($rg->hasil == 0) {
				$row[] = '<a href="javascript:void(0)" class="btn btn-xs btn-default btn-block green-stripe" style="margin-bottom:5px !important" 
							title="Konfirmasi" onclick="view_permohonan(\'' . $rg->id_daftar_magang . '\')"><i class="fa fa-search"></i> Konfirmasi</a>
							<a href="javascript:void(0)" class="btn btn-xs btn-default btn-block red-stripe" style="margin-right:0px !important" 
							title="Delete Magang" onclick="delete_konfirmasi_magang(\'' . $rg->id_daftar_magang . '\')"><i class="fa fa-trash-o"></i> Pembatalan</a>';
			} else {
				//$row[] = '';
				$row[] = '<a href="javascript:void(0)" class="btn btn-xs btn-default btn-block red-stripe" style="margin-right:0px !important" 
							title="Delete Magang" onclick="delete_konfirmasi_magang(\'' . $rg->id_daftar_magang . '\')"><i class="fa fa-trash-o"></i> Pembatalan</a>';
			}

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Konfirmasi_magang_model->count_all($hasil),
			"recordsFiltered" => $this->Konfirmasi_magang_model->count_filtered($hasil),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Konfirmasi_magang_model->get_by_id($id);

		$id_sms = $this->db->where('nipd', $data->nipd)->get('mahasiswa_pt')->row()->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;
		$data->nm_lemb = $nm_lemb;
		$kualifikasi_1 = !empty($data->kualifikasi_1) ? '1. ' . $data->kualifikasi_1 : '';
		$kualifikasi_2 = !empty($data->kualifikasi_2) ? '<br>2. ' . $data->kualifikasi_2 : '';
		$kualifikasi_3 = !empty($data->kualifikasi_3) ? '<br>3. ' . $data->kualifikasi_3 : '';
		$kualifikasi_4 = !empty($data->kualifikasi_4) ? '<br>4. ' . $data->kualifikasi_4 : '';
		$kualifikasi_5 = !empty($data->kualifikasi_5) ? '<br>5. ' . $data->kualifikasi_5 : '';

		$kualifikasi = $kualifikasi_1  . $kualifikasi_2  . $kualifikasi_3  . $kualifikasi_4  . $kualifikasi_5;
		$data->kualifikasi = $kualifikasi;

		$fasilitas_1 = !empty($data->fasilitas_1) ? '1. ' . $data->fasilitas_1 : '';
		$fasilitas_2 = !empty($data->fasilitas_2) ? '<br>2. ' . $data->fasilitas_2 : '';
		$fasilitas_3 = !empty($data->fasilitas_3) ? '<br>3. ' . $data->fasilitas_3 : '';

		$fasilitas = $fasilitas_1  . $fasilitas_2  . $fasilitas_3;
		$data->fasilitas = $fasilitas;

		echo json_encode($data);
	}

	public function ajax_delete($id)
	{
		$this->Konfirmasi_magang_model->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update_stempel()
	{
		$this->_validate();
		$pengguna = $this->ion_auth->user()->row();
		$data = [
			'is_boleh_download' => 'Y',
			'hasil' => 1,
			'tanggal_approve' => date('Y-m-d H:i:s'),
			'approve_by' => $pengguna->first_name,
			'dpl' => $this->input->post('dpl'),
			'tgl_awal_magang' => date('Y-m-d', strtotime($this->input->post('tgl_awal_magang'))),
			'tgl_akhir_magang' => date('Y-m-d', strtotime($this->input->post('tgl_akhir_magang'))),
			'tipe_surat_tugas' => $this->input->post('tipe_surat_tugas'),
			'change_by' => $pengguna->id,
			'change_date' => date('Y-m-d H:i:s')
		];


		$this->Konfirmasi_magang_model->update(['id_daftar_magang' => $this->input->post('id_daftar_magang')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('tgl_awal_magang') == '') {
			$data['inputerror'][] = 'tgl_awal_magang';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('tgl_akhir_magang') == '') {
			$data['inputerror'][] = 'tgl_akhir_magang';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

	public function submit_cetak($id_eoffice)
	{
		$data = [
			'id_status' => 1,
			'tgl_dicetak' => date('Y-m-d H:i:s'),
			'is_cetak' => 'Y',
			'change_by' => $this->ion_auth->user()->row()->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Konfirmasi_magang_model->update(['id_eoffice' => $id_eoffice], $data);

		$data = $this->Konfirmasi_magang_model->get_by_id($id_eoffice);
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
