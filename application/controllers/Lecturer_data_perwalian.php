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

	public function ajax_list($angkatan = 0)
	{
		$list = $this->Lecturer_data_perwalian_model->get_datatables($angkatan);
		$data = [];
		$no = $_POST['start'] ?? 0;
		foreach ($list as $student) {
			$html = '<a class="btn btn-default blue-stripe btn-xs btn-block" 
						href="javascript:void(0)" 
						onclick="edit_evaluasi(' . "'" . $student->nipd . "'" . ')">
						<i class="glyphicon glyphicon-pencil"></i> Evaluasi
					</a>';

			$html2 = '<a href="' . site_url("Lecturer_data_perwalian/academic_record/$student->nipd") . '" class="btn btn-xs btn-default blue-stripe" target="_blank">
				Academic Record</a>';
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
			$row[] = $html2;
			//$row[] = $student->semester_tempuh;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lecturer_data_perwalian_model->count_all($angkatan),
			"recordsFiltered" => $this->Lecturer_data_perwalian_model->count_filtered($angkatan),
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
			->where_not_in('fmp.nipd', 'SELECT nim from feeder_lulus_do', FALSE)
			->get()->result();


		foreach ($mhs_bimbingan as $key => $value) {
			$cek_data = $this->db->where('nipd', $value->nipd)->get('siakad_evaluasi_keberhasilan_studi');
			$data_kuliah_mahasiswa = $this->db->where('nipd', $value->nipd)
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


	public function download_excel()
	{
		$spreadsheet = new Spreadsheet();

		//$id_sms = $this->session->userdata('prodi');
		//$prodi = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;

		$id_ptk = $this->session->userdata('id_ptk');
		$nm_dosen = $this->db->where(compact('id_ptk'))->get('siakad_dosen')->row()->nm_ptk;

		$data_evaluasi = $this->db->where(['ev.id_ptk' => $id_ptk])
			->select('ev.*, m.nm_pd, m.jln,m.telepon_rumah, m.telepon_seluler, sd.nm_ptk')
			->from('siakad_evaluasi_keberhasilan_studi ev')
			->join('mahasiswa_pt mp', 'mp.nipd = ev.nipd')
			->join('mahasiswa m', 'm.id_pd = mp.id_pd')
			->join('siakad_dosen sd', 'sd.id_ptk = ev.id_ptk')
			->order_by('nipd')
			->get()->result();

		$no = 1;
		foreach ($data_evaluasi as $key => $value) {


			$array_judul[] = [
				$no,
				$value->nipd,
				$value->nm_pd,
				$value->jln,
				$value->telepon_rumah,
				$value->telepon_seluler,
				$value->nm_ptk,
				$value->ips1,
				$value->ipk1,
				$value->ips2,
				$value->ipk2,
				$value->ips3,
				$value->ipk3,
				$value->ips4,
				$value->ipk4,
				$value->ips5,
				$value->ipk5,
				$value->ips6,
				$value->ipk6,
				$value->semester_tempuh,
				$value->evaluasi_1,
				$value->rekomendasi_1,
				$value->evaluasi_2,
				$value->rekomendasi_2,
				$value->evaluasi_3,
				$value->rekomendasi_3,
				$value->evaluasi_kaprodi,
				$value->rekomendasi_kaprodi,

			];

			$no++;
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Wilmar Bisnis Indonesia');
		$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

		$sheet->setCellValue('A2', 'DATA EVALUASI KEBERHASILAN STUDI ');
		$sheet->getStyle('A2')->getFont()->setSize(13)->setBold(true);

		$sheet->setCellValue('A3', $nm_dosen);
		$sheet->getStyle('A3')->getFont()->setSize(13)->setBold(true);



		$sheet->setCellValue('A6', 'No.');
		$sheet->setCellValue('B6', 'nim');
		$sheet->setCellValue('C6', 'nama');
		$sheet->setCellValue('D6', 'jln');
		$sheet->setCellValue('E6', 'telepon_rumah');
		$sheet->setCellValue('F6', 'telepon_seluler');
		$sheet->setCellValue('G6', 'nm_ptk');
		$sheet->setCellValue('H6', 'ips1');
		$sheet->setCellValue('I6', 'ipk1');
		$sheet->setCellValue('J6', 'ips2');
		$sheet->setCellValue('K6', 'ipk2');
		$sheet->setCellValue('L6', 'ips3');
		$sheet->setCellValue('M6', 'ipk3');
		$sheet->setCellValue('N6', 'ips4');
		$sheet->setCellValue('O6', 'ipk4');
		$sheet->setCellValue('P6', 'ips5');
		$sheet->setCellValue('Q6', 'ipk5');
		$sheet->setCellValue('R6', 'ips6');
		$sheet->setCellValue('S6', 'ipk6');
		$sheet->setCellValue('T6', 'semester_tempuh');
		$sheet->setCellValue('U6', 'evaluasi_1');
		$sheet->setCellValue('V6', 'rekomendasi_1');
		$sheet->setCellValue('W6', 'evaluasi_2');
		$sheet->setCellValue('X6', 'rekomendasi_2');
		$sheet->setCellValue('Y6', 'evaluasi_3');
		$sheet->setCellValue('Z6', 'rekomendasi_3');
		$sheet->setCellValue('AA6', 'evaluasi_kaprodi');
		$sheet->setCellValue('AB6', 'rekomendasi_kaprodi');


		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(40);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(20);
		$sheet->getColumnDimension('K')->setWidth(20);
		$sheet->getColumnDimension('L')->setWidth(20);
		$sheet->getColumnDimension('M')->setWidth(20);
		$sheet->getColumnDimension('N')->setWidth(20);
		$sheet->getColumnDimension('O')->setWidth(20);
		$sheet->getColumnDimension('P')->setWidth(20);
		$sheet->getColumnDimension('Q')->setWidth(20);
		$sheet->getColumnDimension('R')->setWidth(20);
		$sheet->getColumnDimension('S')->setWidth(20);
		$sheet->getColumnDimension('T')->setWidth(20);
		$sheet->getColumnDimension('U')->setWidth(40);
		$sheet->getColumnDimension('V')->setWidth(40);
		$sheet->getColumnDimension('W')->setWidth(40);
		$sheet->getColumnDimension('X')->setWidth(40);
		$sheet->getColumnDimension('Y')->setWidth(40);
		$sheet->getColumnDimension('Z')->setWidth(40);
		$sheet->getColumnDimension('AA')->setWidth(40);
		$sheet->getColumnDimension('AB')->setWidth(40);

		$sheet->getStyle('A6:AB6')->getFont()->setSize(11)->setBold(true);

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
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];

		$style_outline = [
			'borders' => [
				'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,]
			],
		];


		$sheet->fromArray($array_judul, NULL, "A7");
		$highestRow = $sheet->getHighestRow();

		$sheet->getStyle('A7:V' . $highestRow)->getFont()->setSize(11);

		$sheet->getStyle("A7:A" . $highestRow)->applyFromArray($style_horizontal_center);
		$sheet->getStyle("B7:B" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("C7:C" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("D7:D" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("E7:E" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("F7:F" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("G7:G" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("H7:H" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("I7:I" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("J7:J" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("K7:K" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("L7:L" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("M7:M" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("N7:N" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("O7:O" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("P7:P" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("Q7:Q" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("R7:R" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("S7:S" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("T7:T" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("U7:U" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("V7:V" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("W7:W" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("X7:X" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("Y7:Y" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("Z7:Z" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("AA7:AA" . $highestRow)->applyFromArray($style_horizontal_left);
		$sheet->getStyle("AB7:AB" . $highestRow)->applyFromArray($style_horizontal_left);

		$sheet->getStyle("A6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("B6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("C6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("D6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("E6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("F6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("G6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("H6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("I6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("J6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("K6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("L6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("M6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("N6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("O6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("P6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("Q6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("R6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("S6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("T6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("U6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("V6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("W6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("X6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("Y6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("Z6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("AA6")->applyFromArray($style_horizontal_center);
		$sheet->getStyle("AB6")->applyFromArray($style_horizontal_center);


		$writer = new Xlsx($spreadsheet);
		$nm_file = substr('Evaluasi studi ' . $nm_dosen, 0, 150);
		$filename = $nm_file . '.xlsx';
		$writer->save(getcwd() . '/laporan_excel/' . $filename);

		$this->load->helper('download');
		$data = file_get_contents(FCPATH . 'laporan_excel/' . $filename);
		force_download($filename, $data);
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

	public function academic_record($nipd)
	{
		$this->load->model('Transkrip_sementara_model');
		$data_ar = $this->Transkrip_sementara_model->get_academic_record_data($nipd);
		if (!$data_ar) {
			return show_error('Mahasiswa dengan NIM ' . html_escape($nipd) . ' tidak ditemukan.');
		}

		$mhs = $data_ar['mhs'];
		$output = $data_ar;
		$output['nim'] = $nipd;
		$output['nmpd'] = $mhs->nm_pd;
		$output['prodi'] = $mhs->nm_prodi;

		$tgl_lahir_indo = $this->tanggal_indo(date("Y-m-d", strtotime($mhs->tgl_lahir)), $mhs->custom_tgl_lahir);
		$output['tlahir'] = rtrim((string)$mhs->tmpt_lahir) . ", " . $tgl_lahir_indo;
		$output['tgl_masuk_sp'] = (!empty($mhs->tgl_masuk_sp) && $mhs->tgl_masuk_sp != '0000-00-00') ? $this->tanggal_indo(date("Y-m-d", strtotime($mhs->tgl_masuk_sp))) : (!empty($mhs->angkatan) ? $mhs->angkatan : '-');

		$layout = $this->db->where('id_sms', $mhs->id_sms)->get('transkrip_layout')->row();
		$dosen = $this->db->like('nm_ptk', 'Aldon')->get('siakad_dosen')->row();
		if ($dosen && !empty($dosen->nidn)) {
			$output['nidn_wadir1'] = $dosen->nidn;
			if ($layout) {
				$layout->nidn_direktur = $dosen->nidn;
			}
		} else {
			$output['nidn_wadir1'] = ($layout && !empty($layout->nidn_direktur)) ? $layout->nidn_direktur : '';
		}
		$output['layout'] = $layout;

		$this->load->view('cetak/cetak_academic_record', $output);
	}

	private function tanggal_indo($tanggal, $custom_tgl_lahir = 0)
	{
		$bulan = [
			1 => 'Januari',
			2 => 'Februari',
			3 => 'Maret',
			4 => 'April',
			5 => 'Mei',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'Agustus',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Desember'
		];
		$var = explode('-', $tanggal);
		if (count($var) < 3) return $tanggal;
		return $var[2] . ' ' . $bulan[(int)$var[1]] . ' ' . $var[0];
	}
}
