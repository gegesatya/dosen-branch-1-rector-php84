<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_penetapan_judul extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_daftar_penetapan_judul_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus login sebagai dosen.');
			redirect('auth/logout', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');
		$output['judul_besar'] = 'Penetapan Judul Tugas Akhir ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_daftar_penetapan_judul';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'e_skripsi_penetapan_judul/e_skripsi_penetapan_judul_view';
		$output['after_page'] = 'e_skripsi_penetapan_judul/e_skripsi_penetapan_judul_after_page';
		$output['dd_status_penetapan_judul'] 	= $this->E_skripsi_daftar_penetapan_judul_model->f_status_penetapan_judul();

		$this->load->view('master_page', $output);
	}

	public function ajax_list($angkatan)
	{

		$list = $this->E_skripsi_daftar_penetapan_judul_model->get_datatables($angkatan, 2);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];

			$row[] = '<strong>' . $rg->nipd . '</strong>' . '<br>' . $rg->nm_pd;
			$row[] = $rg->judul_ditetapkan;
			$html_komen = '<a href="javascript:void(0)" class="btn btn-default btn-xs green-stripe"
							onclick="balas_pesan(' . "'" . $rg->id_judul_skripsi . "'" . ')">
							<i class="fa fa-mail-reply"></i> <small>Kirim Pesan</small></a>';

			if (!empty($rg->komentar)) {
				$row[] = '<small>' . $rg->komentar . '<br>' . $html_komen . '</small>';
			} else {
				$row[] = $html_komen;
			}

			$status = $this->db->where(['status_penetapan_judul' => $rg->status_penetapan_judul])->get('siakad_skripsi_status_penetapan_judul')->row()->nm_status_penetapan_judul;
			$row[] = $status;

			$html_action = '';
			if ($rg->status_penetapan_judul == 2) {
				$html_action = '<a href="javascript:void(0)" class="btn btn-success btn-sm"
					onclick="approve_judul(' . "'" . $rg->id_judul_skripsi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ')">
					<i class="fa fa-cloud-upload "></i> Approval</a>';
			}
			$row[] = $html_action;



			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_daftar_penetapan_judul_model->count_all($angkatan, 2),
			"recordsFiltered" => $this->E_skripsi_daftar_penetapan_judul_model->count_filtered($angkatan, 2),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function view_data_pengajuan($id_judul_skripsi)
	{
		$judul_skripsi = $this->db->where('j.id_judul_skripsi', $id_judul_skripsi)
			->select('j.*, m.nm_pd, a.analisis_sks_lulus, a.analisis_sks_tidak_lulus, a.analisis_sks_total, a.analisis_ipk')
			->from('siakad_skripsi_pengajuan_judul j')
			->join('mahasiswa_pt mp', 'j.nipd = mp.nipd', 'left')
			->join('mahasiswa m', 'mp.id_pd = m.id_pd', 'left')
			->join('siakad_skripsi_analisis a', 'a.nipd = j.nipd', 'left')
			->get()->row();


		echo json_encode([
			'nm_pd' => !empty($judul_skripsi->nm_pd) ? $judul_skripsi->nm_pd : '',
			'nipd' => !empty($judul_skripsi->nipd) ? $judul_skripsi->nipd : '',
			'id_smt' => !empty($judul_skripsi->id_smt) ? $judul_skripsi->id_smt : '',
			'komentar' => !empty($judul_skripsi->komentar) ? $judul_skripsi->komentar : '',
			'id_judul_skripsi' => !empty($judul_skripsi->id_judul_skripsi) ? $judul_skripsi->id_judul_skripsi : '',
			'judul_skripsi_1' => !empty($judul_skripsi->judul_skripsi_1) ? $judul_skripsi->judul_skripsi_1 : '',
			'judul_skripsi_2' => !empty($judul_skripsi->judul_skripsi_2) ? $judul_skripsi->judul_skripsi_2 : '',
			'judul_skripsi_3' => !empty($judul_skripsi->judul_skripsi_3) ? $judul_skripsi->judul_skripsi_3 : '',
			'target_selesai' => !empty($judul_skripsi->target_selesai) ? $judul_skripsi->target_selesai : '',
			'tanggal_diajukan' => !empty($judul_skripsi->tanggal_diajukan) ? $judul_skripsi->tanggal_diajukan : '',
			'analisis_sks_lulus' => !empty($judul_skripsi->analisis_sks_lulus) ? $judul_skripsi->analisis_sks_lulus : '',
			'analisis_sks_tidak_lulus' => !empty($judul_skripsi->analisis_sks_tidak_lulus) ? $judul_skripsi->analisis_sks_tidak_lulus : '',
			'analisis_sks_total' => !empty($judul_skripsi->analisis_sks_total) ? $judul_skripsi->analisis_sks_total : '',
			'analisis_ipk' => !empty($judul_skripsi->analisis_ipk) ? $judul_skripsi->analisis_ipk : '',
			'judul_ditetapkan' => !empty($judul_skripsi->judul_ditetapkan) ? $judul_skripsi->judul_ditetapkan : '',
			'tanggal_usulan_kaprodi' => !empty($judul_skripsi->tanggal_usulan_kaprodi) ? $judul_skripsi->tanggal_usulan_kaprodi : '',
			'dosen_pembimbing_1' => !empty($judul_skripsi->dosen_pembimbing_1) ? $judul_skripsi->dosen_pembimbing_1 : '',
			'dosen_pembimbing_2' => !empty($judul_skripsi->dosen_pembimbing_2) ? $judul_skripsi->dosen_pembimbing_2 : '',
			//'dosen_penguji' => $judul_skripsi->dosen_penguji,
			//'dosen_penguji_2' => $judul_skripsi->dosen_penguji_2,
			'status_penetapan_judul' => !empty($judul_skripsi->status_penetapan_judul) ? $judul_skripsi->status_penetapan_judul : ''

		]);
	}

	public function ajax_edit($id)
	{
		$data = $this->E_skripsi_daftar_penetapan_judul_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('judul_ditetapkan') == '') {
			$data['inputerror'][] = 'judul_ditetapkan';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('status_penetapan_judul') == '') {
			$data['inputerror'][] = 'status_penetapan_judul';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('dosen_pembimbing_1') == '') {
			$data['inputerror'][] = 'dosen_pembimbing_1';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		/* if ($this->input->post('dosen_penguji') == '') {
			$data['inputerror'][] = 'dosen_penguji';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		} */

		if ($this->input->post('tanggal_usulan_kaprodi') == '') {
			$data['inputerror'][] = 'tanggal_usulan_kaprodi';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}


	public function persetujuan_judul()
	{
		$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$data_judul_skripsi = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul');
		$user = $this->ion_auth->user()->row();
		$this->db->trans_start();

		if ($this->input->post('status_penetapan_judul') == 3) {

			$this->db->where(compact('id_judul_skripsi'))->update(
				'siakad_skripsi_pengajuan_judul',
				[
					'judul_ditetapkan' => $this->input->post('judul_ditetapkan'),
					'tanggal_usulan_kaprodi' => $this->input->post('tanggal_usulan_kaprodi'),
					'dosen_pembimbing_1' => $this->input->post('dosen_pembimbing_1'),
					'dosen_pembimbing_2' => $this->input->post('dosen_pembimbing_2'),
					//'dosen_penguji' => $this->input->post('dosen_penguji'),
					//'dosen_penguji_2' => $this->input->post('dosen_penguji_2'),
					'status_penetapan_judul' => $this->input->post('status_penetapan_judul'),
					'judul_ditetapkan' => $this->input->post('judul_ditetapkan'),
					'tanggal_disetujui' => date('Y-m-d H:i:s'),
				]
			);
		} else {
			$this->db->where(compact('id_judul_skripsi'))->update(
				'siakad_skripsi_pengajuan_judul',
				[
					'status_penetapan_judul' => $this->input->post('status_penetapan_judul'),
					'change_by' => $user->id,
					'change_date' => date('Y-m-d H:i:s')
				]
			);
		}


		$this->db->trans_complete();
		echo json_encode(["status" => TRUE]);
	}



	public function ajax_balas_pesan()
	{
		$user = $this->ion_auth->user()->row();
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$data_pengajuan_judul = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$catatan_dosen_wali_awal = $data_pengajuan_judul->komentar;
		$catatan_dosen_wali = '';

		/* if (!empty($this->input->post('catatan_dosen_wali'))) {
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			} else {
				$catatan_dosen_wali = '<div class="general-item-list"><div class="item"><div class="item-head"><div class="item-details"><a href="" class="item-name primary-link">' . $user->first_name . '</a><span>' . tgl_indo_short(date('Y-m-d')) . '</span></div></div><div>' . html_escape($this->input->post('catatan_dosen_wali')) . '</div></div></div>';
			}
		} */

		if (!empty($this->input->post('catatan_dosen_wali'))) {
			$tgl = tgl_indo_short(date('Y-m-d'));
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . '<br><b>' . ucwords(strtolower((string)$user->first_name)) . '</b> <small> On ' . $tgl . '</small>: <br>' . $this->input->post('catatan_dosen_wali');
			} else {
				$catatan_dosen_wali = '<b>' . ucwords(strtolower((string)$user->first_name))  . '</b> <small> On ' . $tgl . '</small>: <br>' . $this->input->post('catatan_dosen_wali');
			}
		}

		$data = [
			'komentar' => $catatan_dosen_wali
		];

		$this->E_skripsi_daftar_penetapan_judul_model->update(['id_judul_skripsi' => $this->input->post('id_judul_skripsi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function view_pesan($id_judul_skripsi)
	{
		$data_rencana_studi = $this->db->where(compact('id_judul_skripsi'))->get('siakad_skripsi_pengajuan_judul')->row();

		$log_message = '';
		if (!empty($data_rencana_studi->komentar)) {
			$log_message =  $data_rencana_studi->komentar;
		}

		echo json_encode([
			'status_penetapan_judul' => $data_rencana_studi->status_penetapan_judul,
			'data_pesan' => $log_message
		]);
	}

	public function getDosen()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_penetapan_judul_model->get_dosen());
	}

	public function getDosenPenguji()
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_penetapan_judul_model->get_dosen_penguji());
	}
}
