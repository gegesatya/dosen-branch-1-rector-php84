<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_penjadwalan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_penjadwalan_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$output['judul_besar'] = 'Diskusi Jadwal Seminar/Sidang ';
		$output['judul_kecil'] = 'Tugas Akhir WBI';
		$output['menu_aktif'] = 'm_jadwal_seminar_sidang';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'e_skripsi_penjadwalan/e_skripsi_penjadwalan_view';
		$output['after_page'] = 'e_skripsi_penjadwalan/e_skripsi_penjadwalan_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{

		$list = $this->E_skripsi_penjadwalan_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];

			$kompare_tanggal = 0;
			if ($rg->tanggal_seminar != '0000-00-00') {
				$tgl = tgl_indo($rg->tanggal_seminar) . ' <br> ' . $rg->jam;
				$t1 = strtotime($rg->tanggal_seminar);
				$t2 = strtotime(date('Y-m-d H:i:s'));

				if ($t1 > $t2) {
					$kompare_tanggal = 1;
				}
			} else {
				$tgl = '';
			}

			if ($rg->kategori_pengajuan == 1) {
				$teks = "Jadwal Seminar Proposal";
			} elseif ($rg->kategori_pengajuan == 2) {
				$teks = "Jadwal Seminar Hasil";
			} elseif ($rg->kategori_pengajuan == 3) {
				$teks = "Jadwal Sidang Tugas Akhir";
			}

			$html_link_dokumen = '<a href="' . $rg->link_dokumen . '" class="btn btn-xs btn-default blue-stripe" target="_new"> <i class="fa fa-download"></i> <small>Download</small></a>';

			$row[] = $no;
			$row[] =  $tgl;
			$row[] = '<strong>' . $rg->nipd . '</strong>' . ' <br> ' . $rg->nm_pd;
			$row[] =  $teks;
			$row[] =  $rg->tempat;
			$row[] =  $html_link_dokumen; //$this->convertURLs($rg->link_dokumen);


			$html_theme = '<div class="general-item-list" style="height: 100px; overflow-y: auto;">' . $this->convertURLs($rg->html_respon) . '</div>';
			/* if ($rg->hasil_sempro == 'layak') {
					$html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="balas_pesan(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-mail-reply"></i> <small>Diskusi Jadwal</small></a>
									<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
									onclick="get_info(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-info"></i> <small>Info</small></a>
									<a href="' . site_url('E_skripsi_bimbingan/cetak_pdf/') . md5($rg->id_judul_skripsi) . '" class="btn btn-default btn-xs green-stripe" target="_new"
									onclick="">
									<i class="fa fa-file-pdf-o"></i> <small>Download</small></a>
									';
				} else { */
			$html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe" onclick="balas_pesan(' . "'" . $rg->uniq . "'" . ')">
									<i class="fa fa-mail-reply"></i> <small>Diskusi Jadwal</small></a>
						  <a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe" onclick="get_info(' . "'" . $rg->id_judul_skripsi . "'" . ')">
									<i class="fa fa-info"></i> <small>Info</small></a>';
			//}


			/* 
				if (!empty($rg->html_respon)) {
					$row[] =   $html_theme  . '<br>' . $html_komen;
				} else {
					$row[] = $html_komen;
				}
 			*/
			$row[] = $html_komen;
			$row[] = $kompare_tanggal;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_penjadwalan_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_penjadwalan_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->E_skripsi_penjadwalan_model->get_by_id_2($id);

		$id_ptk = $this->session->userdata('id_ptk');
		if ($id_ptk == $data->dosen_pembimbing_1) {
			$data->is_doping_1 = TRUE;
		} else {
			$data->is_doping_1 = FALSE;
		}

		if ($id_ptk == $data->dosen_pembimbing_2) {
			$data->is_doping_2 = TRUE;
		} else {
			$data->is_doping_2 = FALSE;
		}

		echo json_encode($data);
	}

	public function ajax_balas_pesan()
	{
		$user = $this->ion_auth->user()->row();
		$uniq = $this->input->post('uniq');
		$lembar_monitoring = $this->db->where(compact('uniq'))->get('siakad_skripsi_seminar_sidang');

		if ($lembar_monitoring->num_rows() == 0) {
			$html_respon_awal = '';
			$html_table_awal = '';
		} else {
			$html_respon_awal = $lembar_monitoring->row()->html_respon;
			$html_table_awal = $lembar_monitoring->row()->html_table;
		}
		$html_respon = '';
		$html_table = '';


		if (!empty($this->input->post('catatan_dosen_wali'))) {
			$tgl = date('Y-m-d');
			$jam = date('H:i');

			$catatan = str_ireplace(["\r\n", "\n", "\r"], '<br />', $this->input->post('catatan_dosen_wali'));

			$html_respon = $html_respon_awal . '<div class="panel panel-default"><div class="panel-heading"><a href="javascript:void(0)" class="item-name primary-link">' . $this->ion_auth->user()->row()->first_name . '</a> <span class="item-label text-right">' . tgl_indo_short($tgl) . ', ' . $jam . '</span></div><div class="panel-body">' . $catatan . '</div></div>';

			//$html_respon = $html_respon_awal . '<div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $this->ion_auth->user()->row()->first_name . '</a><span class="item-label">' . tgl_indo_short($tgl) . ', ' . $jam . '</span></div></div><div class="item-body">' . $this->input->post('catatan_dosen_wali') . '</div></div>';

			//$html_table = $html_table_awal . '<tr><td>' . tgl_indo_short($tgl) . '</td><td>' . $this->input->post('catatan_dosen_wali') . '</td><td>' . $this->ion_auth->user()->row()->first_name . '</td></tr>';

			$data = ['html_respon' => $html_respon, 'html_table' => $html_table];

			$this->E_skripsi_penjadwalan_model->update(['uniq' => $uniq], $data);
			echo json_encode(["status" => TRUE]);
		}
	}

	public function view_pesan($uniq)
	{
		$data = $this->db->where(compact('uniq'))->get('siakad_skripsi_seminar_sidang')->row();

		$log_message = '';
		if (!empty($data->html_respon)) {
			$log_message =  $data->html_respon;
		}

		echo json_encode([
			'data_pesan' => $log_message
		]);
	}

	public function view_info($id_judul_skripsi)
	{
		$data = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$nipd = $data->nipd;
		$mhs = $this->db->where('nipd', $nipd)->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$nm_pd = !empty($mhs->nm_pd) ? $mhs->nm_pd : '';
		$judul_ditetapkan = $data->judul_ditetapkan;

		echo json_encode([
			'nipd' => $nipd,
			'nm_pd' => $nm_pd,
			'judul_ditetapkan' => $judul_ditetapkan,
			'no_handphone' => $data->no_handphone
		]);
	}




	public function convertURLs($string)
	{
		$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
		return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
	}
}
