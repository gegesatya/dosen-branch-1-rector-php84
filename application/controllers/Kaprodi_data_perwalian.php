<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Lecturer_data_perwalian extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lecturer_data_perwalian_model', 'Lecturer_data_perwalian_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus Dosen untuk melihat halaman ini.');
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Evaluasi Keberhasilan Studi';
		$output['judul_kecil'] 		= '';
		$output['menu_aktif'] 		= 'm_lecturer_data_perwalian';
		$output['parent_1']			= 'active open';
		$output['content'] 			= 'data_perwalian/data_perwalian_view';
		$output['after_page'] 		= 'data_perwalian/data_perwalian_after_page';
		$output['master_page'] 		= 'ajax_pagination';

		//$output['dd_semester'] = $this->Lecturer_data_perwalian_model->f_dropdown('sms', 'id_sms', 'nm_lemb');*/

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Lecturer_data_perwalian_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $student) {
			/* $html = '<a href="' . site_url("Lecturer_data_perwalian/download_data_akademik_excel/$student->nipd") . '" class="btn btn-xs btn-default blue-stripe" target="_new">
				1 Thn</a>
				<a href="' . site_url("Lecturer_data_perwalian/download_data_akademik_excel/$student->nipd") . '" class="btn btn-xs btn-default blue-stripe" target="_new">
				2 Thn</a>'; */

			$html = '<a class="btn btn-default blue-stripe btn-xs btn-block" 
						href="javascript:void(0)" 
						onclick="edit_evaluasi(' . "'" . $student->nipd . "'" . ')">
						<i class="glyphicon glyphicon-pencil"></i> Evaluasi
					</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $student->nipd . ' | ' . $student->nm_pd;
			$row[] = $student->ips1;
			$row[] = $student->ipk1;
			$row[] = $student->ips2;
			$row[] = $student->ipk2;
			$row[] = $student->ips3;
			$row[] = $student->ipk3;
			$row[] = $student->ips4;
			$row[] = $student->ipk4;
			$row[] = $student->ips5;
			$row[] = $student->ipk5;
			$row[] = $student->ips6;
			$row[] = $student->ipk6;
			$row[] = $html;
			$row[] = $student->semester_tempuh;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lecturer_data_perwalian_model->count_all(),
			"recordsFiltered" => $this->Lecturer_data_perwalian_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function sinkron_data()
	{
		$mhs_bimbingan = $this->db->select('*')->from('mahasiswa m')
			->join('siakad_dosen d', 'd.id_ptk = m.dosen_pa')
			->join('mahasiswa_pt fmp', 'fmp.id_pd = m.id_pd')
			->where(['m.dosen_pa' => $this->session->userdata('id_ptk')])
			->where_not_in('fmp.id_reg_pd_feeder', 'SELECT id_reg_pd from feeder_lulus_do', FALSE)
			->get()->result();


		foreach ($mhs_bimbingan as $key => $value) {
			$cek_data = $this->db->where('nipd', $value->nipd)->get('siakad_evaluasi_keberhasilan_studi');
			$data_kuliah_mahasiswa = $this->db->where('id_reg_pd', $value->id_reg_pd_feeder)
				->limit(6, 0)->order_by('id_smt')->get('kuliah_mahasiswa');


			if ($data_kuliah_mahasiswa->num_rows() > 0) {
				$data_input = '';

				if ($data_kuliah_mahasiswa->num_rows() == 1) {
					$data_input = [
						'nipd' => $value->nipd,
						'nm_pd' => $value->nm_pd,
						'ips1' => $data_kuliah_mahasiswa->row()->ips,
						'ipk1' => $data_kuliah_mahasiswa->row()->ips,
						'sks_1' => $data_kuliah_mahasiswa->row()->sks_smt,
						'sks_kum_1' => $data_kuliah_mahasiswa->row()->sks_total,
						'status1' => $data_kuliah_mahasiswa->row()->id_stat_mhs,
						'id_ptk' => $this->session->userdata('id_ptk'),
						'semester_tempuh' => 2
					];
				} elseif ($data_kuliah_mahasiswa->num_rows() == 2) {

					$ips1 = $data_kuliah_mahasiswa->result()[0]->ips;
					$ipk1 = $data_kuliah_mahasiswa->result()[0]->ipk;
					$status1 = $data_kuliah_mahasiswa->result()[0]->id_stat_mhs;
					$sks_1 = $data_kuliah_mahasiswa->result()[0]->sks_smt;
					$sks_kum_1 = $data_kuliah_mahasiswa->result()[0]->sks_total;

					$ips2 = $data_kuliah_mahasiswa->result()[1]->ips;
					$ipk2 = $data_kuliah_mahasiswa->result()[1]->ipk;
					$status2 = $data_kuliah_mahasiswa->result()[1]->id_stat_mhs;
					$sks_2 = $data_kuliah_mahasiswa->result()[1]->sks_smt;
					$sks_kum_2 = $data_kuliah_mahasiswa->result()[1]->sks_total;


					$data_input = [
						'nipd' 		=> $value->nipd,
						'nm_pd' 	=> $value->nm_pd,
						'ips1' 		=> $ips1,
						'ipk1' 		=> $ipk1,
						'status1' 	=> $status1,
						'ips2' 		=> $ips2,
						'ipk2' 		=> $ipk2,
						'status2' 	=> $status2,
						'sks_1'		=> $sks_1,
						'sks_2'		=> $sks_2,
						'sks_kum_1'		=> $sks_kum_1,
						'sks_kum_2'		=> $sks_kum_2,
						'semester_tempuh' => 2,
						'id_ptk' => $this->session->userdata('id_ptk')

					];
				} elseif ($data_kuliah_mahasiswa->num_rows() == 3) {

					$ips1 = $data_kuliah_mahasiswa->result()[0]->ips;
					$ipk1 = $data_kuliah_mahasiswa->result()[0]->ipk;
					$status1 = $data_kuliah_mahasiswa->result()[0]->id_stat_mhs;
					$sks_1 = $data_kuliah_mahasiswa->result()[0]->sks_smt;
					$sks_kum_1 = $data_kuliah_mahasiswa->result()[0]->sks_total;

					$ips2 = $data_kuliah_mahasiswa->result()[1]->ips;
					$ipk2 = $data_kuliah_mahasiswa->result()[1]->ipk;
					$status2 = $data_kuliah_mahasiswa->result()[1]->id_stat_mhs;
					$sks_2 = $data_kuliah_mahasiswa->result()[1]->sks_smt;
					$sks_kum_2 = $data_kuliah_mahasiswa->result()[1]->sks_total;

					$ips3 = $data_kuliah_mahasiswa->result()[2]->ips;
					$ipk3 = $data_kuliah_mahasiswa->result()[2]->ipk;
					$status3 = $data_kuliah_mahasiswa->result()[2]->id_stat_mhs;
					$sks_3 = $data_kuliah_mahasiswa->result()[2]->sks_smt;
					$sks_kum_3 = $data_kuliah_mahasiswa->result()[2]->sks_total;


					$data_input = [
						'nipd' 		=> $value->nipd,
						'nm_pd' 	=> $value->nm_pd,
						'ips1' 		=> $ips1,
						'ipk1' 		=> $ipk1,
						'status1' 	=> $status1,
						'ips2' 		=> $ips2,
						'ipk2' 		=> $ipk2,
						'status2' 	=> $status2,
						'ips3' 		=> $ips3,
						'ipk3' 		=> $ipk3,
						'status3' 	=> $status3,
						'sks_1'		=> $sks_1,
						'sks_2'		=> $sks_2,
						'sks_3'		=> $sks_3,
						'sks_kum_1'	=> $sks_kum_1,
						'sks_kum_2'	=> $sks_kum_2,
						'sks_kum_3'	=> $sks_kum_3,
						'id_ptk' => $this->session->userdata('id_ptk'),
						'semester_tempuh' => 3
					];
				} elseif ($data_kuliah_mahasiswa->num_rows() == 4) {

					$ips1 = $data_kuliah_mahasiswa->result()[0]->ips;
					$ipk1 = $data_kuliah_mahasiswa->result()[0]->ipk;
					$status1 = $data_kuliah_mahasiswa->result()[0]->id_stat_mhs;
					$sks_1 = $data_kuliah_mahasiswa->result()[0]->sks_smt;
					$sks_kum_1 = $data_kuliah_mahasiswa->result()[0]->sks_total;

					$ips2 = $data_kuliah_mahasiswa->result()[1]->ips;
					$ipk2 = $data_kuliah_mahasiswa->result()[1]->ipk;
					$status2 = $data_kuliah_mahasiswa->result()[1]->id_stat_mhs;
					$sks_2 = $data_kuliah_mahasiswa->result()[1]->sks_smt;
					$sks_kum_2 = $data_kuliah_mahasiswa->result()[1]->sks_total;

					$ips3 = $data_kuliah_mahasiswa->result()[2]->ips;
					$ipk3 = $data_kuliah_mahasiswa->result()[2]->ipk;
					$status3 = $data_kuliah_mahasiswa->result()[2]->id_stat_mhs;
					$sks_3 = $data_kuliah_mahasiswa->result()[2]->sks_smt;
					$sks_kum_3 = $data_kuliah_mahasiswa->result()[2]->sks_total;

					$ips4 = $data_kuliah_mahasiswa->result()[3]->ips;
					$ipk4 = $data_kuliah_mahasiswa->result()[3]->ipk;
					$status4 = $data_kuliah_mahasiswa->result()[3]->id_stat_mhs;
					$sks_4 = $data_kuliah_mahasiswa->result()[3]->sks_smt;
					$sks_kum_4 = $data_kuliah_mahasiswa->result()[3]->sks_total;


					$data_input = [
						'nipd' 		=> $value->nipd,
						'nm_pd' 	=> $value->nm_pd,
						'ips1' 		=> $ips1,
						'ipk1' 		=> $ipk1,
						'status1' 	=> $status1,
						'ips2' 		=> $ips2,
						'ipk2' 		=> $ipk2,
						'status2' 	=> $status2,
						'ips3' 		=> $ips3,
						'ipk3' 		=> $ipk3,
						'status3' 	=> $status3,
						'ips4' 		=> $ips4,
						'ipk4' 		=> $ipk4,
						'status4' 	=> $status4,
						'sks_1'		=> $sks_1,
						'sks_2'		=> $sks_2,
						'sks_3'		=> $sks_3,
						'sks_4'		=> $sks_4,
						'sks_kum_1'	=> $sks_kum_1,
						'sks_kum_2'	=> $sks_kum_2,
						'sks_kum_3'	=> $sks_kum_3,
						'sks_kum_4'	=> $sks_kum_4,
						'id_ptk' => $this->session->userdata('id_ptk'),
						'semester_tempuh' => 4
					];
				} elseif ($data_kuliah_mahasiswa->num_rows() == 5) {

					$ips1 = $data_kuliah_mahasiswa->result()[0]->ips;
					$ipk1 = $data_kuliah_mahasiswa->result()[0]->ipk;
					$status1 = $data_kuliah_mahasiswa->result()[0]->id_stat_mhs;
					$sks_1 = $data_kuliah_mahasiswa->result()[0]->sks_smt;
					$sks_kum_1 = $data_kuliah_mahasiswa->result()[0]->sks_total;

					$ips2 = $data_kuliah_mahasiswa->result()[1]->ips;
					$ipk2 = $data_kuliah_mahasiswa->result()[1]->ipk;
					$status2 = $data_kuliah_mahasiswa->result()[1]->id_stat_mhs;
					$sks_2 = $data_kuliah_mahasiswa->result()[1]->sks_smt;
					$sks_kum_2 = $data_kuliah_mahasiswa->result()[1]->sks_total;

					$ips3 = $data_kuliah_mahasiswa->result()[2]->ips;
					$ipk3 = $data_kuliah_mahasiswa->result()[2]->ipk;
					$status3 = $data_kuliah_mahasiswa->result()[2]->id_stat_mhs;
					$sks_3 = $data_kuliah_mahasiswa->result()[2]->sks_smt;
					$sks_kum_3 = $data_kuliah_mahasiswa->result()[2]->sks_total;

					$ips4 = $data_kuliah_mahasiswa->result()[3]->ips;
					$ipk4 = $data_kuliah_mahasiswa->result()[3]->ipk;
					$status4 = $data_kuliah_mahasiswa->result()[3]->id_stat_mhs;
					$sks_4 = $data_kuliah_mahasiswa->result()[3]->sks_smt;
					$sks_kum_4 = $data_kuliah_mahasiswa->result()[3]->sks_total;

					$ips5 = $data_kuliah_mahasiswa->result()[4]->ips;
					$ipk5 = $data_kuliah_mahasiswa->result()[4]->ipk;
					$status5 = $data_kuliah_mahasiswa->result()[4]->id_stat_mhs;
					$sks_5 = $data_kuliah_mahasiswa->result()[4]->sks_smt;
					$sks_kum_5 = $data_kuliah_mahasiswa->result()[4]->sks_total;

					$data_input = [
						'nipd' 		=> $value->nipd,
						'nm_pd' 	=> $value->nm_pd,
						'ips1' 		=> $ips1,
						'ips2' 		=> $ips2,
						'ips3' 		=> $ips3,
						'ips4' 		=> $ips4,
						'ips5' 		=> $ips5,
						'ipk1' 		=> $ipk1,
						'ipk2' 		=> $ipk2,
						'ipk3' 		=> $ipk3,
						'ipk4' 		=> $ipk4,
						'ipk5' 		=> $ipk5,
						'status1' 	=> $status1,
						'status2' 	=> $status2,
						'status3' 	=> $status3,
						'status4' 	=> $status4,
						'status5' 	=> $status5,
						'sks_1'		=> $sks_1,
						'sks_2'		=> $sks_2,
						'sks_3'		=> $sks_3,
						'sks_4'		=> $sks_4,
						'sks_5'		=> $sks_5,
						'sks_kum_1'	=> $sks_kum_1,
						'sks_kum_2'	=> $sks_kum_2,
						'sks_kum_3'	=> $sks_kum_3,
						'sks_kum_4'	=> $sks_kum_4,
						'sks_kum_5'	=> $sks_kum_5,
						'id_ptk' => $this->session->userdata('id_ptk'),
						'semester_tempuh' => 5
					];
				} elseif ($data_kuliah_mahasiswa->num_rows() == 6) {

					$ips1 = $data_kuliah_mahasiswa->result()[0]->ips;
					$ipk1 = $data_kuliah_mahasiswa->result()[0]->ipk;
					$status1 = $data_kuliah_mahasiswa->result()[0]->id_stat_mhs;
					$sks_1 = $data_kuliah_mahasiswa->result()[0]->sks_smt;
					$sks_kum_1 = $data_kuliah_mahasiswa->result()[0]->sks_total;

					$ips2 = $data_kuliah_mahasiswa->result()[1]->ips;
					$ipk2 = $data_kuliah_mahasiswa->result()[1]->ipk;
					$status2 = $data_kuliah_mahasiswa->result()[1]->id_stat_mhs;
					$sks_2 = $data_kuliah_mahasiswa->result()[1]->sks_smt;
					$sks_kum_2 = $data_kuliah_mahasiswa->result()[1]->sks_total;

					$ips3 = $data_kuliah_mahasiswa->result()[2]->ips;
					$ipk3 = $data_kuliah_mahasiswa->result()[2]->ipk;
					$status3 = $data_kuliah_mahasiswa->result()[2]->id_stat_mhs;
					$sks_3 = $data_kuliah_mahasiswa->result()[2]->sks_smt;
					$sks_kum_3 = $data_kuliah_mahasiswa->result()[2]->sks_total;

					$ips4 = $data_kuliah_mahasiswa->result()[3]->ips;
					$ipk4 = $data_kuliah_mahasiswa->result()[3]->ipk;
					$status4 = $data_kuliah_mahasiswa->result()[3]->id_stat_mhs;
					$sks_4 = $data_kuliah_mahasiswa->result()[3]->sks_smt;
					$sks_kum_4 = $data_kuliah_mahasiswa->result()[3]->sks_total;

					$ips5 = $data_kuliah_mahasiswa->result()[4]->ips;
					$ipk5 = $data_kuliah_mahasiswa->result()[4]->ipk;
					$status5 = $data_kuliah_mahasiswa->result()[4]->id_stat_mhs;
					$sks_5 = $data_kuliah_mahasiswa->result()[4]->sks_smt;
					$sks_kum_5 = $data_kuliah_mahasiswa->result()[4]->sks_total;

					$ips6 = $data_kuliah_mahasiswa->result()[5]->ips;
					$ipk6 = $data_kuliah_mahasiswa->result()[5]->ipk;
					$status6 = $data_kuliah_mahasiswa->result()[5]->id_stat_mhs;
					$sks_6 = $data_kuliah_mahasiswa->result()[5]->sks_smt;
					$sks_kum_6 = $data_kuliah_mahasiswa->result()[5]->sks_total;


					$data_input = [
						'nipd' 		=> $value->nipd,
						'nm_pd' 	=> $value->nm_pd,
						'ips1' 		=> $ips1,
						'ips2' 		=> $ips2,
						'ips3' 		=> $ips3,
						'ips4' 		=> $ips4,
						'ips5' 		=> $ips5,
						'ips6' 		=> $ips6,
						'ipk1' 		=> $ipk1,
						'ipk2' 		=> $ipk2,
						'ipk3' 		=> $ipk3,
						'ipk4' 		=> $ipk4,
						'ipk5' 		=> $ipk5,
						'ipk6' 		=> $ipk6,
						'status1' 	=> $status1,
						'status2' 	=> $status2,
						'status3' 	=> $status3,
						'status4' 	=> $status4,
						'status5' 	=> $status5,
						'status6' 	=> $status6,
						'sks_1'		=> $sks_1,
						'sks_2'		=> $sks_2,
						'sks_3'		=> $sks_3,
						'sks_4'		=> $sks_4,
						'sks_5'		=> $sks_5,
						'sks_6'		=> $sks_6,
						'sks_kum_1'	=> $sks_kum_1,
						'sks_kum_2'	=> $sks_kum_2,
						'sks_kum_3'	=> $sks_kum_3,
						'sks_kum_4'	=> $sks_kum_4,
						'sks_kum_5'	=> $sks_kum_5,
						'sks_kum_6'	=> $sks_kum_6,
						'id_ptk' => $this->session->userdata('id_ptk'),
						'semester_tempuh' => 6
					];
				}



				if ($cek_data->num_rows() == 0) {
					$this->db->insert('siakad_evaluasi_keberhasilan_studi', $data_input);
				} else {
					$this->db->update('siakad_evaluasi_keberhasilan_studi', $data_input, ['nipd' => $value->nipd]);
				}
			}
		}

		header("Refresh:0; url=" . site_url('Lecturer_data_perwalian'));
	}

	public function ajax_edit($id)
	{
		$data = $this->Lecturer_data_perwalian_model->get_by_id($id);
		$tr_evaluasi_1 = $tr_evaluasi_2 = $tr_evaluasi_3 = '';

		if ($data->semester_tempuh >= 2) {
			if ($data->ipk2 < 2) {
				$tr_evaluasi_1 = '<tr class="info"><td colspan=6>IPK tahun pertama di bawah 2,00. Mohon dilakukan pengecekan apakah data nilai sudah masuk. Selanjutnya konfirmasi ke Bagian Akademik untuk memeriksa status keaktifan mahasiswa.</td></tr>';
			} else {
				$tr_evaluasi_1 = '<tr class="info"><td colspan=6>IPK tahun pertama di atas 2,00.</td></tr>';
			}
		}

		if ($data->semester_tempuh >= 4) {
			if ($data->ipk4 < 2) {
				$tr_evaluasi_2 = '<tr class="info"><td colspan=6>IPK tahun kedua di bawah 2,00. Mohon dilakukan pengecekan apakah data nilai sudah masuk. Selanjutnya konfirmasi ke Bagian Akademik untuk memeriksa status keaktifan mahasiswa.</td></tr>';
			} else {
				$tr_evaluasi_2 = '<tr class="info"><td colspan=6>IPK tahun kedua di atas 2,00.</td></tr>';
			}
		}

		if ($data->semester_tempuh >= 6) {
			if ($data->ipk6 < 2) {
				$tr_evaluasi_3 = '<tr class="info"><td colspan=6>IPK tahun ketiga di bawah 2,00. Mohon dilakukan pengecekan apakah data nilai sudah masuk. Selanjutnya konfirmasi ke Bagian Akademik untuk memeriksa status keaktifan mahasiswa.</td></tr>';
			} else {
				$tr_evaluasi_3 = '<tr class="info"><td colspan=6>IPK tahun ketiga di atas 2,00.</td></tr>';
			}
		}

		$tbl_nilai = '<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center">Semester</th>
								<th class="text-center">IPS</th>
								<th class="text-center">IPK</th>
								<th class="text-center">SKS</th>
								<th class="text-center">SKS KUM</th>
								<th class="text-center">Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-center">1 (Satu)</td>
								<td class="text-center">' . $data->ips1 . '</td>
								<td class="text-center">' . $data->ipk1 . '</td>
								<td class="text-center">' . $data->sks_1 . '</td>
								<td class="text-center">' . $data->sks_kum_1 . '</td>
								<td class="text-center">' . $this->cek_status($data->status1) . '</td>
							</tr>
							<tr>
								<td class="text-center">2 (Dua)</td>
								<td class="text-center">' . $data->ips2 . '</td>
								<td class="text-center">' . $data->ipk2 . '</td>
								<td class="text-center">' . $data->sks_2 . '</td>
								<td class="text-center">' . $data->sks_kum_2 . '</td>
								<td class="text-center">' . $this->cek_status($data->status2) . '</td>
							</tr>
							' . $tr_evaluasi_1 . '
							<tr>
								<td class="text-center">3 (Tiga)</td>
								<td class="text-center">' . $data->ips3 . '</td>
								<td class="text-center">' . $data->ipk3 . '</td>
								<td class="text-center">' . $data->sks_3 . '</td>
								<td class="text-center">' . $data->sks_kum_3 . '</td>
								<td class="text-center">' . $this->cek_status($data->status3) . '</td>
							</tr>
							<tr>
								<td class="text-center">4 (Empat)</td>
								<td class="text-center">' . $data->ips4 . '</td>
								<td class="text-center">' . $data->ipk4 . '</td>
								<td class="text-center">' . $data->sks_4 . '</td>
								<td class="text-center">' . $data->sks_kum_4 . '</td>
								<td class="text-center">' . $this->cek_status($data->status4) . '</td>
							</tr>
							' . $tr_evaluasi_2 . '
							<tr>
								<td class="text-center">5 (Lima)</td>
								<td class="text-center">' . $data->ips5 . '</td>
								<td class="text-center">' . $data->ipk5 . '</td>
								<td class="text-center">' . $data->sks_5 . '</td>
								<td class="text-center">' . $data->sks_kum_5 . '</td>
								<td class="text-center">' . $this->cek_status($data->status5) . '</td>
							</tr>
							<tr>
								<td class="text-center">6 (Enam)</td>
								<td class="text-center">' . $data->ips6 . '</td>
								<td class="text-center">' . $data->ipk6 . '</td>
								<td class="text-center">' . $data->sks_6 . '</td>
								<td class="text-center">' . $data->sks_kum_6 . '</td>
								<td class="text-center">' . $this->cek_status($data->status6) . '</td>
							</tr>
							' . $tr_evaluasi_3 . '
						</tbody>';

		$tbl_nilai .= '</table>';

		$data->tbl_nilai = $tbl_nilai;
		echo json_encode($data);
	}

	public function ajax_update()
	{
		//$this->_validate();
		$data = [
			'evaluasi_1' => $this->input->post('evaluasi_1'),
			'evaluasi_2' => $this->input->post('evaluasi_2'),
			'evaluasi_3' => $this->input->post('evaluasi_3'),
			'rekomendasi_1' => $this->input->post('rekomendasi_1'),
			'rekomendasi_2' => $this->input->post('rekomendasi_2'),
			'rekomendasi_3' => $this->input->post('rekomendasi_3'),
		];

		$this->Lecturer_data_perwalian_model->update(['nipd' => $this->input->post('nipd_hidden')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function cek_status($id_status)
	{
		if ($id_status == 'A') {
			return 'AKTIF';
		} elseif ($id_status == 'C') {
			return 'CUTI';
		} elseif ($id_status == 'D') {
			return 'DROP-OUT/PUTUS STUDI';
		} elseif ($id_status == 'G') {
			return 'SEDANG DOUBLE DEGREE';
		} elseif ($id_status == 'K') {
			return 'KELUAR';
		} elseif ($id_status == 'L') {
			return 'LULUS';
		} elseif ($id_status == 'N') {
			return 'NON-AKTIF';
		} elseif ($id_status == 'X' || $id_status == '') {
			return 'UNKNOWN';
		}
	}
















	public function download_data_akademik($nipd)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, mp.id_reg_pd')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$id_reg_pd = $mahasiswa->id_reg_pd;


		$nilai_mahasiswa_1 = $this->db->where(['id_reg_pd' => $id_reg_pd])->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('arsip_nilai n')
			->join('siakad_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();

		$nilai_mahasiswa_2 = $this->db->where(['nipd' => $nipd])->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();




		$this->load->library('table');
		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-black">',
			'thead_open'          => '<thead>',
			'thead_close'         => '</thead>',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle">',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		];

		$this->table->set_template($tmpl);
		$this->table->set_heading(['No', 'SMT', 'Kode/<br>Code', 'Mata Kuliah/Subjects', 'Lambang/<br>Mark', 'Mutu/<br>Quality', 'SKS/<br>Credit', 'Credit <br>X<br> Quality']);


		$i = 1;

		$jumlah = $jumlah_sks_mk = 0;

		foreach ($nilai_mahasiswa_1->result() as $l_nilai) {
			$hasil = $l_nilai->sks_mk * $l_nilai->nilai_indeks;
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				$l_nilai->id_smt,
				$l_nilai->kode_mk,
				$l_nilai->nm_mk,
				['data' => $l_nilai->nilai_huruf, 'class' => 'text-center'],
				['data' => $l_nilai->nilai_indeks, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
				['data' => $hasil, 'class' => 'text-center']
			]);

			$jumlah += $hasil;
			if (!empty($l_nilai->nilai_huruf)) {
				$jumlah_sks_mk += (int) $l_nilai->sks_mk;
			}
		}

		foreach ($nilai_mahasiswa_2->result() as $l_nilai) {
			$hasil = $l_nilai->sks_mk * $l_nilai->nilai_indeks;
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				$l_nilai->id_smt,
				$l_nilai->kode_mk,
				$l_nilai->nm_mk,
				['data' => $l_nilai->nilai_huruf, 'class' => 'text-center'],
				['data' => $l_nilai->nilai_indeks, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
				['data' => $hasil, 'class' => 'text-center']
			]);

			$jumlah += $hasil;
			if (!empty($l_nilai->nilai_huruf)) {
				$jumlah_sks_mk += (int) $l_nilai->sks_mk;
			}
		}

		if ($jumlah_sks_mk == 0) {
			$ips = 0;
		} else {
			$ips = round($jumlah / $jumlah_sks_mk, 2);
		}

		$this->table->add_row([
			['data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 6],
			['data' => $jumlah_sks_mk, 'class' => 'text-center'],
			['data' => $jumlah, 'class' => 'text-center']
		]);

		$this->table->add_row([
			['data' => 'Indeks Prestasi / <i>Grade Point</i>', 'class' => 'text-center', 'colspan' => 6],
			['data' => $ips, 'class' => 'text-center', 'colspan' => 2]
		]);

		$nilai_tabel =  $this->table->generate();

		$output['mahasiswa'] = $mahasiswa;
		$output['tabel'] = $nilai_tabel;

		//var_dump($nilai_tabel);

		$html = $this->load->view('cetak/pdf_data_akademik', $output, true);
		$pdfFilePath  = 'Data Akademik ' . $mahasiswa->nm_pd .  '.pdf';
		$mpdf = new \Mpdf\Mpdf();

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function download_data_akademik_excel($nipd)
	{
		$spreadsheet = new Spreadsheet();
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, mp.id_reg_pd, mp.id_reg_pd_feeder')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$id_reg_pd = $mahasiswa->id_reg_pd;
		$id_reg_pd_feeder = $mahasiswa->id_reg_pd_feeder;

		$nilai_mahasiswa_1 = $this->db->where(['id_reg_pd' => $id_reg_pd])->select('mk.id_mk, mk.kode_mk, n.nilai_huruf, n.nilai_angka, n.nilai_indeks, mk.id_mk, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('arsip_nilai n')
			->join('arsip_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();

		$nilai_mahasiswa_2 = $this->db->where(['nipd' => $nipd])->select('mk.id_mk, mk.kode_mk, n.nilai_huruf, n.nilai_angka, n.nilai_indeks, mk.id_mk, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();

		$gabung = array_merge($nilai_mahasiswa_1->result_array(), $nilai_mahasiswa_2->result_array());

		$array_nilai = [];
		$no = 1;

		$total_sks = $total_sks_lulus = $total_sks_tidak_lulus = $total_sks_mk = $ipk = 0;

		foreach ($gabung as $key => $value) {
			$array_nilai[] = [
				$no,
				$value['id_smt'],
				$value['kode_mk'],
				$value['nm_mk'],
				$value['nilai_huruf'],
				$value['nilai_indeks'],
				$value['sks_mk'],
				$value['sks_mk'] * $value['nilai_indeks'] * 1
			];

			if ($value['nilai_huruf'] <> '') {
				$total_sks += $value['sks_mk'];
				$total_sks_mk += $value['sks_mk'] * $value['nilai_indeks'] * 1;
				if ($value['nilai_huruf'] == 'E') {
					$total_sks_tidak_lulus += $value['sks_mk'];
				} else {
					$total_sks_lulus += $value['sks_mk'];
				}
			}

			$no++;
		}

		$smt = 1;
		$kuliah_mahasiswa = $this->db->where('id_reg_pd', $id_reg_pd_feeder)->get('kuliah_mahasiswa')->result_array();
		$array_kuliah_mahasiswa = [];
		foreach ($kuliah_mahasiswa as $key => $value) {
			$sks_lulus_semester = $sks_tidak_lulus_semester = 0;

			$nilai_lama = $this->db->where(['id_reg_pd' => $id_reg_pd, 'kk.id_smt' => $value['id_smt']])->select('mk.kode_mk, n.nilai_huruf, n.nilai_angka, n.nilai_indeks, mk.id_mk, mk.nm_mk, mk.sks_mk, kk.id_smt')
				->from('arsip_nilai n')
				->join('arsip_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
				->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->order_by('id_smt')
				->get();

			$nilai_baru = $this->db->where(['nipd' => $nipd, 'kk.id_smt' => $value['id_smt']])->select('mk.kode_mk, n.nilai_huruf, n.nilai_angka, n.nilai_indeks, mk.id_mk, mk.nm_mk, mk.sks_mk, kk.id_smt')
				->from('siakad_nilai_baru n')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->order_by('id_smt')
				->get();

			$nilai_gabung = array_merge($nilai_lama->result_array(), $nilai_baru->result_array());

			foreach ($nilai_gabung as $key => $x) {
				if ($x['nilai_huruf'] <> '') {
					if ($x['nilai_huruf'] == 'E') {
						$sks_tidak_lulus_semester += $x['sks_mk'];
					} else {
						$sks_lulus_semester += $x['sks_mk'];
					}
				}
			}

			$array_kuliah_mahasiswa[] = [
				$smt,
				$value['id_smt'],
				$value['ips'],
				$value['sks_smt'],
				$sks_lulus_semester,
				$sks_tidak_lulus_semester,
				$value['ipk'],
				$value['sks_total']
			];

			$smt++;
		}


		$ipk = round($total_sks_mk / $total_sks, 2);

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'Data Akademik Mahasiswa');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A4', 'Nama ');
		$sheet->getStyle('A4')->getFont()->setSize(12)->setBold(true);

		$sheet->setCellValue('C4', ': ' . $mahasiswa->nm_pd);
		$sheet->getStyle('C4')->getFont()->setSize(12)->setBold(true);

		$sheet->setCellValue('A5', 'NIM ');
		$sheet->getStyle('A5')->getFont()->setSize(12)->setBold(true);

		$sheet->setCellValue('C5', ': ' . $mahasiswa->nipd);
		$sheet->getStyle('C5')->getFont()->setSize(12)->setBold(true);

		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(12);
		$sheet->getColumnDimension('C')->setWidth(12);
		$sheet->getColumnDimension('D')->setWidth(50);
		$sheet->getColumnDimension('E')->setWidth(12);
		$sheet->getColumnDimension('F')->setWidth(12);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(12);


		$sheet->setCellValue('A7', 'No.');
		$sheet->setCellValue('B7', 'SMT');
		$sheet->setCellValue('C7', 'Kode');
		$sheet->setCellValue('D7', 'Mata Kuliah');
		$sheet->setCellValue('E7', 'Lambang');
		$sheet->setCellValue('F7', 'Mutu');
		$sheet->setCellValue('G7', 'SKS');
		$sheet->setCellValue('H7', 'SKS*Mutu');

		$sheet->setCellValue('J7', 'Semester');
		$sheet->setCellValue('K7', 'TA');
		$sheet->setCellValue('L7', 'IPS');
		$sheet->setCellValue('M7', 'SKS');
		$sheet->setCellValue('N7', 'SKS Lulus');
		$sheet->setCellValue('O7', 'SKS Tdk Lulus');
		$sheet->setCellValue('P7', 'IPK');
		$sheet->setCellValue('Q7', 'SKS KUM');


		$sheet->getStyle('A7:H7')->getFont()->setSize(11)->setBold(true);

		$style_horizontal_center = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$style_horizontal_left = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allborders' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$style_outline = [
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$sheet->fromArray($array_nilai, NULL, "A8");
		$sheet->fromArray($array_kuliah_mahasiswa, NULL, "J8");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);
		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray($style_horizontal_center);


		$next_row = $highestRow + 1;
		$sheet->setCellValue('D' . $next_row, 'TOTAL');
		$sheet->setCellValue('G' . $next_row, $total_sks);
		$sheet->setCellValue('H' . $next_row, $total_sks_mk);
		$next_row += 1;

		$sheet->setCellValue('D' . $next_row, 'TOTAL SKS LULUS');
		$sheet->setCellValue('G' . $next_row, $total_sks_lulus);

		$next_row += 1;

		$sheet->setCellValue('D' . $next_row, 'TOTAL SKS TIDAK LULUS');
		$sheet->setCellValue('G' . $next_row, $total_sks_tidak_lulus);

		$next_row += 1;

		$sheet->setCellValue('D' . $next_row, 'Indeks Prestasi Kumulatif');
		$sheet->setCellValue('G' . $next_row, $ipk);

		$sheet->getStyle("G7:G" . $next_row)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("H7:H" . $next_row)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("A7:H" . $next_row)->applyFromArray($style_outline);
		//$sheet->getStyle("J7:Q" . $next_row)->applyFromArray($style_outline);

		$writer = new Xlsx($spreadsheet);
		$filename = 'Data akademik ' . $mahasiswa->nipd . ' ' . $mahasiswa->nm_pd . '.xlsx';
		$writer->save(getcwd() . '/laporan_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'laporan_excel/' . $filename);
		force_download($filename, $data);
	}

	public function convert()
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$style_col = [
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			],
		];

		$style_row_tengah = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$sheet->setCellValue('A1', "DATA PERWALIAN " . strtoupper($this->session->userdata('nm_ptk')));
		$sheet->mergeCells('A1:E1');
		$sheet->getStyle('A1')->getFont()->setBold(true);

		$sheet->mergeCells('H2:I2');

		$sheet->setCellValue('A4', "NIM");
		$sheet->setCellValue('B4', "NAMA");
		$sheet->setCellValue('C4', "Tgl Masuk");
		$sheet->setCellValue('D4', "Telepon");
		$sheet->setCellValue('E4', "Email");


		$sheet->getStyle('A4')->applyFromArray($style_col);
		$sheet->getStyle('B4')->applyFromArray($style_col);
		$sheet->getStyle('C4')->applyFromArray($style_col);
		$sheet->getStyle('D4')->applyFromArray($style_col);
		$sheet->getStyle('E4')->applyFromArray($style_col);



		//$data_rps_master = $this->db->where(compact('id_rps'))->get('siakad_rps_online')->row();
		$jabfung = $this->Lecturer_data_perwalian_model->get_excel_data();

		$no = 1;
		$numrow = 5;
		foreach ($jabfung as $data) {
			$sheet->setCellValue('A' . $numrow, $data->nipd);
			$sheet->setCellValue('B' . $numrow, $data->nm_pd);
			$sheet->setCellValue('C' . $numrow, $data->tgl_masuk_sp);
			$sheet->setCellValue('D' . $numrow, $data->telepon_seluler);
			$sheet->setCellValue('E' . $numrow, $data->email_wbi);

			$sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
			$sheet->getStyle('E' . $numrow)->applyFromArray($style_row);



			$no++;
			$numrow++;
		}

		$sheet->getColumnDimension('A')->setWidth(13);
		$sheet->getColumnDimension('B')->setWidth(45);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(45);

		$sheet->getDefaultRowDimension()->setRowHeight(-1);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->setTitle("Pengabdian");

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename= Data Perwalian ' . $this->session->userdata('nm_ptk') . '".xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	private function has_dupes($array)
	{
		$dupe_array = [];
		foreach ($array as $val) {
			if (++$dupe_array[$val] > 1) {
				return true;
			}
		}
		return false;
	}
}
