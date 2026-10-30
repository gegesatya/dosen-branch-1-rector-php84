<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_epom_dosen extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_epom_dosen_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		/* $group = array(5);
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		} */
		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'EPOM By Dosen';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_pusat_data_epom_dosen';
		$output['parent_data_center']	= 'active open';
		$output['content'] 			= 'pusat_data_epom_dosen/pusat_data_epom_dosen_view';
		$output['after_page'] 		= 'pusat_data_epom_dosen/pusat_data_epom_dosen_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Pusat_data_epom_dosen_model->get_datatables();
		$data = [];
		$no = $_POST['start'];

		$id_smt = $this->session->userdata('id_smt');
		$ts = substr($id_smt, 0, 4);

		$ts_2 = $ts - 2;
		$ts_1 = $ts - 1;

		$id_smt_1 = $ts_2 . "1";
		$id_smt_2 = $ts_2 . "2";
		$id_smt_3 = $ts_1 . "1";
		$id_smt_4 = $ts_1 . "2";
		$id_smt_5 = $ts . "1";
		$id_smt_6 = $ts . "2";

		foreach ($list as $n) {
			// $ratarata = $this->db->where('id_ptk', $n->id_ptk)->select('avg(nilai_epom) as nilai_epom')
			// 	->from('siakad_rekap_epom')->group_by('id_ptk')->get();

			$epom_smt_1 = $this->get_rekap_epom($id_smt_1, $n->id_ptk);
			$epom_smt_2 = $this->get_rekap_epom($id_smt_2, $n->id_ptk);
			$epom_smt_3 = $this->get_rekap_epom($id_smt_3, $n->id_ptk);
			$epom_smt_4 = $this->get_rekap_epom($id_smt_4, $n->id_ptk);
			$epom_smt_5 = $this->get_rekap_epom($id_smt_5, $n->id_ptk);
			$epom_smt_6 = $this->get_rekap_epom($id_smt_6, $n->id_ptk);


			//$html = '<a href="' . site_url('C_pusat_data_epom_dosen/cetak_pdf_epom') . '/' . $n->id_ptk . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> Detail</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->nidn;
			$row[] = $n->nm_ptk;
			$row[] = $n->nm_ikatan_kerja;
			$row[] = "Rata-rata : <b>" . $epom_smt_1[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_1[1] . '</b>';
			$row[] = "Rata-rata : <b>" . $epom_smt_2[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_2[1] . '</b>';
			$row[] = "Rata-rata : <b>" . $epom_smt_3[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_3[1] . '</b>';
			$row[] = "Rata-rata : <b>" . $epom_smt_4[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_4[1] . '</b>';
			$row[] = "Rata-rata : <b>" . $epom_smt_5[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_5[1] . '</b>';
			$row[] = "Rata-rata : <b>" . $epom_smt_6[0] . '</b><br>Jml Kelas : <b>' . $epom_smt_6[1] . '</b>';

			// $row[] = $epom_smt_2[0];
			// $row[] = $epom_smt_3[0];
			// $row[] = $epom_smt_4[0];
			// $row[] = $epom_smt_5[0];
			// $row[] = $epom_smt_6[0];

			// if ($ratarata->num_rows() == 0) {
			// 	$row[] = 0;
			// } else {
			// 	$row[] = round($ratarata->row()->nilai_epom, 2);
			// }

			//$row[] = $html;
			// $row[] = '<a class="btn btn-sm btn-default green-stripe" 
			// 					href="javascript:void()" 
			// 					onclick="detail_epom(\'' . $n->id_ptk . '\')">
			// 					<i class="glyphicon glyphicon-pencil"></i> Detail
			// 				</a>';
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pusat_data_epom_dosen_model->count_all(),
			"recordsFiltered" => $this->Pusat_data_epom_dosen_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function get_rekap_epom($id_smt, $id_ptk)
	{
		$data = $this->db->where(['id_smt' => $id_smt, 'id_ptk' => $id_ptk])->get('siakad_rekap_epom');
		if ($data->num_rows() > 0) {
			$nilai_epom = $this->db->where(['id_smt' => $id_smt, 'id_ptk' => $id_ptk])->select('avg(nilai_epom) as r')->from('siakad_rekap_epom')->get()->row()->r;
		} else {
			$nilai_epom = 0;
		}

		return [round($nilai_epom, 2), $data->num_rows()];
	}

	public function ajax_edit($id)
	{
		$data = $this->Pusat_data_epom_dosen_model->get_by_id($id);
		$rekap_epom = $this->Pusat_data_epom_dosen_model->get_rekap_epom($id);
		$avg_epom = $this->Pusat_data_epom_dosen_model->get_avg_semester($id);

		$i = 1;
		$tabel = '<table class="table table-bordered table-hover"><thead><tr><th>#</th><th>SMT</th><th>Nama MK</th><th>Prodi MK</th><th>EPOM</th></tr></thead><tbody>';
		foreach ($rekap_epom as $key => $value) {
			$tabel .= '<tr><td>' . $i++ . '</td>
							<td>' . $value->id_smt . '</td>
							<td>' . $value->nm_mk . '</td>
							<td>' . $value->nm_prodi_english . '</td>
							<td>' . $value->nilai_epom . '</td>
						</tr>';
		}
		$tabel .= '</tbody></table>';

		$j = 1;
		$tabel2 = '<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>SMT</th>
							<th>Rata2</th>
							<th>Rata2 Dosen Tetap WBI</th>
							<th>Rata2 Dosen Tdk Tetap WBI</th>
							<th>Rata2 WBI</th>
						</tr>
					</thead>
				<tbody>';
		foreach ($avg_epom as $key => $value) {
			$tabel2 .= '<tr><td>' . $j++ . '</td>
							<td>' . $value->id_smt . '</td>
							<td>' . round($value->nilai_epom, 2) . '</td>
							<td>' . round($this->get_data_rata_rata($value->id_smt)->rerata_epom, 2) . '</td>
							<td>' . round($this->get_data_rata_rata($value->id_smt)->rerata_epom_tidak_tetap, 2) . '</td>
							<td>' . round($this->get_data_rata_rata($value->id_smt)->rerata_epom, 2) . '</td>
						</tr>';
		}
		$tabel2 .= '</tbody></table>';

		echo json_encode([$data, $tabel, $tabel2]);
	}

	private function get_data_rata_rata($id_smt)
	{
		$data = $this->db->where(['id_smt' => $id_smt, 'id_sms' => 'wbi'])->get('siakad_rekap_epom_pusat_data')->row();
		return $data;
	}
}
