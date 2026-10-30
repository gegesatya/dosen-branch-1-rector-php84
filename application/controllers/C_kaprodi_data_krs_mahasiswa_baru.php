<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_kaprodi_data_krs_mahasiswa_baru extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Kaprodi_krs_mhs_baru_model', 'Kaprodi_krs_mhs_baru_model');
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
		$this->load->helper('url');
		$output['judul_besar'] = 'KRS (Mahasiswa Baru)';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_krs_baru';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'kaprodi_krs_mahasiswa_baru/kaprodi_krs_mahasiswa_baru_view';
		$output['after_page'] = 'kaprodi_krs_mahasiswa_baru/kaprodi_krs_mahasiswa_baru_after_page';
		$output['master_page'] = 'ajax_pagination';
		//$output['dd_agama'] = $this->Kaprodi_krs_mhs_baru_model->f_dropdown('agama', 'id_agama', 'nm_agama');
		$output['tabel'] = $this->stat_by_agama();
		$this->load->view('master_page', $output);
	}

	public function ajax_list($id_agama)
	{
		$list = $this->Kaprodi_krs_mhs_baru_model->get_datatables($id_agama);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$id_status_krs = $l->id_status_krs;

			if ((int) $id_status_krs == 0) {
				$status_krs = 'Belum mengajukan KRS';
			} else {
				$status_krs = $this->db->where('id_status_krs', $id_status_krs)->get('siakad_status_krs')->row()->nm_status_krs;
			}

			$no++;
			$row = [];
			$row[] = $no;
			//$row[] = $l->nipd;
			$row[] = '<a href="javascript:void(0)" class="btn btn-success btn-sm"
						onclick="show_krs(' . "'" . $l->id_rencana_studi . "'" . ', ' . "'" . $l->nipd . "'" . ')">
						<i class="fa fa-cloud-upload "></i> ' . $l->nipd . '
					</a>';
			$row[] = $l->nm_pd;
			$row[] = date('d-m-Y H:i', strtotime($l->tgl_registrasi));
			$row[] = date('d-m-Y H:i', strtotime($l->pengajuan_date));
			$row[] = $status_krs;
			$row[] = !empty($l->approved_date) ? date('d-m-Y H:i', strtotime($l->approved_date)) : '-';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Kaprodi_krs_mhs_baru_model->count_all($id_agama),
			"recordsFiltered" => $this->Kaprodi_krs_mhs_baru_model->count_filtered($id_agama),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function builder($id_agama = '', $id_sms = '')
	{
		if ($id_agama == '' and $id_sms == '') {
			return $this->db->from('mahasiswa m')
				->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
				->get()->num_rows();
		} elseif ($id_agama <> '' and $id_sms == '') {
			return $this->db->from('mahasiswa m')
				->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
				->where(['id_agama' => $id_agama])
				->get()->num_rows();
		} elseif ($id_agama == '' and $id_sms <> '') {
			return $this->db->from('mahasiswa m')
				->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
				->where(['id_sms' => $id_sms])
				->get()->num_rows();
		} elseif ($id_agama <> '' and $id_sms <> '') {
			return $this->db->from('mahasiswa m')
				->join('mahasiswa_pt mp', 'mp.id_pd = m.id_pd')
				->where(['id_agama' => $id_agama, 'id_sms' => $id_sms])
				->get()->num_rows();
		}
	}

	private function stat_by_agama()
	{
		$id_sms = $this->session->userdata('prodi');

		$mahasiswa_all = $this->builder();
		$mahasiswa_all_islam = $this->builder(1);
		$mahasiswa_all_kristen = $this->builder(2);
		$mahasiswa_all_katholik = $this->builder(3);
		$mahasiswa_all_hindu = $this->builder(4);
		$mahasiswa_all_budha = $this->builder(5);
		$mahasiswa_all_konghucu = $this->builder(6);
		$mahasiswa_all_lainnya = $this->builder(99);

		$mahasiswa_prodi_islam = $this->builder(1, $id_sms);
		$mahasiswa_prodi_kristen = $this->builder(2, $id_sms);
		$mahasiswa_prodi_katholik = $this->builder(3, $id_sms);
		$mahasiswa_prodi_hindu = $this->builder(4, $id_sms);
		$mahasiswa_prodi_budha = $this->builder(5, $id_sms);
		$mahasiswa_prodi_konghucu = $this->builder(6, $id_sms);
		$mahasiswa_prodi_lainnya = $this->builder(99, $id_sms);



		$tabel = '<table class="table table-bordered">';
		$tabel .= '<thead>
						<tr>
							<th>Keterangan</th>
							<th class="text-center">Prodi</th>
							<th class="text-center">WBI</th>
						</tr>
					</thead>';
		$tabel .= 	'<tbody>
						<tr>
							<td>Islam</td>
							<td class="text-center">' . $mahasiswa_prodi_islam . '</td>
							<td class="text-center">' . $mahasiswa_all_islam . '</td>
						</tr>
						<tr>
							<td>Kristen</td>
							<td class="text-center">' . $mahasiswa_prodi_kristen . '</td>
							<td class="text-center">' . $mahasiswa_all_kristen . '</td>
						</tr>
						<tr>
							<td>Katholik</td>
							<td class="text-center">' . $mahasiswa_prodi_katholik . '</td>
							<td class="text-center">' . $mahasiswa_all_katholik . '</td>
						</tr>
						<tr>
							<td>Hindu</td>
							<td class="text-center">' . $mahasiswa_prodi_hindu . '</td>
							<td class="text-center">' . $mahasiswa_all_hindu . '</td>
						</tr>
						<tr>
							<td>Budha</td>
							<td class="text-center">' . $mahasiswa_prodi_budha . '</td>
							<td class="text-center">' . $mahasiswa_all_budha . '</td>
						</tr>
						<tr>
							<td>Konghucu</td>
							<td class="text-center">' . $mahasiswa_prodi_konghucu . '</td>
							<td class="text-center">' . $mahasiswa_all_konghucu . '</td>
						</tr>
						<tr>
							<td>Lainnya</td>
							<td class="text-center">' . $mahasiswa_prodi_lainnya . '</td>
							<td class="text-center">' . $mahasiswa_all_lainnya . '</td>
						</tr>
					</tbody>';
		$tabel .= '</table>';

		return $tabel;
	}

	public function view_krs($id_rencana_studi, $nipd)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, m.dosen_pa')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();

		$krs = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap')
			->from('siakad_krs sn')
			->join('siakad_krs_detail kd', 'sn.id_rencana_studi = kd.id_rencana_studi')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = kd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where(['sn.nipd' => $nipd, 'sn.id_rencana_studi' => $id_rencana_studi])->get();

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
		$this->table->set_heading([
			'No',
			'Kode/<br>Code',
			'Mata Kuliah/Subjects',
			'SKS/<br>Credit', 'T', 'P', 'PL'
		]);


		$i = 1;

		$sks_mk = $sks_tm = $sks_prak = $sks_prak_lap = 0;

		foreach ($krs->result() as $l_nilai) {

			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				$l_nilai->kode_mk,
				$l_nilai->nm_mk,
				['data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_tm, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_prak, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_prak_lap, 'class' => 'text-center']
			]);

			$sks_mk += $l_nilai->sks_mk;
			$sks_tm += $l_nilai->sks_tm;
			$sks_prak += $l_nilai->sks_prak;
			$sks_prak_lap += $l_nilai->sks_prak_lap;
		}

		$this->table->add_row([
			['data' => 'Jumlah SKS / <i>Semester Credit Total</i>', 'class' => 'text-center', 'colspan' => 3],
			['data' => $sks_mk, 'class' => 'text-center'],
			['data' => $sks_tm, 'class' => 'text-center'],
			['data' => $sks_prak, 'class' => 'text-center'],
			['data' => $sks_prak_lap, 'class' => 'text-center']
		]);

		$nilai_tabel =  $this->table->generate();

		$output['mahasiswa'] = $mahasiswa;

		$output['tabel'] = $nilai_tabel;
		//if($tipe=="cetak"){
		//	$this->load->view('cetak/cetak_krs', $output);
		//}else{
		$nm_pd = $mahasiswa->nm_pd;
		$nipd = $mahasiswa->nipd;
		$dosen_wali = $this->db->where('id_ptk', $mahasiswa->dosen_pa)->get('siakad_dosen')->row()->nm_ptk;
		$id_smt = $this->session->userdata('tahun_ajaran');
		echo json_encode(['tabel' => $nilai_tabel, 'nm_pd' => $nm_pd, 'nipd' => $nipd, 'id_smt' => $id_smt, 'dosen_wali' => $dosen_wali]);
		//}

	}
}
