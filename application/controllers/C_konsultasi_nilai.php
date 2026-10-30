<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_konsultasi_nilai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Konsultasi_nilai_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2, 15];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$output['judul_besar'] = 'Konsultasi Nilai';
		$output['judul_kecil'] = 'Mahasiswa Tidak Lulus';
		$output['menu_aktif'] = 'm_konsultasi_nilai';
		$output['parent_1'] = 'active open';
		$output['content'] = 'konsultasi_nilai/konsultasi_nilai_view';
		$output['after_page'] = 'konsultasi_nilai/konsultasi_nilai_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Konsultasi_nilai_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$html = '
					<a href="javascript:void(0)" class="btn btn-sm btn-primary"
						onclick="edit_catatan(\'' . addslashes($rg->nipd) . '\')">
						<i class="icon-pencil"></i> Catatan
					</a>';

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nipd . '<br>' . $rg->nm_pd;
			$row[] = $rg->jml;
			$row[] = '<small>' . $rg->konsultasi_nilai . '</small>';

			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Konsultasi_nilai_model->count_all(),
			"recordsFiltered" => $this->Konsultasi_nilai_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit()
	{
		// $id_kls = $this->input->get('id_kls');
		$nipd  = $this->input->get('nipd');

		$data = $this->Konsultasi_nilai_model->get_by_id($nipd);

		$mhs = $this->db->where('nipd', $nipd)->select('*')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$krs = $this->db->where(['nipd' => $nipd, 'id_status_krs' => 3])->from('siakad_krs')->order_by('id_smt', 'desc')->limit(1)->get()->row();
		$nm_smt = $this->db->where('id_smt', $krs->id_smt)->get('semester')->row()->nm_smt;

		$log_message = '';
		if (!empty($mhs->konsultasi_nilai)) {
			$log_message =  $mhs->konsultasi_nilai;
		}

		$btn = '';

		$tabel = '<table class="table table-bordered table-sm">
					<thead><tr><th>No</th><th>Kode MK | Nama MK</th><th>UTS|UAS</th><th>Nilai</th></tr></thead>
					<tbody>';
		$i = 1;
		foreach ($data as $key => $value) {
			$nm_smt = $this->db->where('id_smt', $value->id_smt)->get('semester')->row()->nm_smt;
			$tabel .= '<tr>
							<td><small>' . $i . '</small></td>
							<td><small>' . $value->kode_mk . '<br>' . $value->nm_mk . '</small></td>
							<td><small><b>UTS : </b>' . $value->n_pengetahuan_2_uts . ' , <b>UAS : </b>' . $value->n_pengetahuan_2_uas . '<br>Tahun Ajaran : ' . $nm_smt . '</small></td>
							<td><small>Angka : ' . $value->nilai_angka . '<br>huruf : ' . $value->nilai_huruf . '</small></td>
						</tr>';
			$i++;
		}

		$tabel .= '</tbody></table>';

		echo json_encode(['nipd' => $nipd, 'nm_pd' => $mhs->nm_pd, 'tabel_nilai' => $tabel, 'data_pesan' => $log_message, 'krs_terakhir' => $nm_smt]);
	}

	public function ajax_update()
	{
		$this->_validate();
		$user = $this->ion_auth->user()->row();

		// $id_kls = $this->input->post('id_kls');
		$nipd  = $this->input->post('nipd');

		$dt_awal = $this->db->where('nipd', $nipd)->select('*')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$konsultasi_nilai_awal = $dt_awal->konsultasi_nilai;
		$konsultasi_nilai = '';
		if (!empty($this->input->post('konsultasi_nilai'))) {
			if (!empty($konsultasi_nilai_awal)) {
				$konsultasi_nilai = $konsultasi_nilai_awal . '<br><b>' . $user->first_name . '</b> (' . date('d-m-Y') . ') : <br>' . $this->input->post('konsultasi_nilai');
			} else {
				$konsultasi_nilai = '<b>' . $user->first_name . '</b> (' . date('d-m-Y') . ') : <br>' . $this->input->post('konsultasi_nilai');
			}
		}

		$data = [
			'konsultasi_nilai' => $konsultasi_nilai
		];

		$data = $this->security->xss_clean($data);
		$this->Konsultasi_nilai_model->update(['id_pd' => $dt_awal->id_pd], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$konsultasi_nilai = $this->input->post('konsultasi_nilai');

		if ($konsultasi_nilai == '') {
			$data['inputerror'][] = 'konsultasi_nilai';
			$data['error_string'][] = 'Harus diisi.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
