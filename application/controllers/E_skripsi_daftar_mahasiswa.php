<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_daftar_mahasiswa extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_daftar_mahasiswa_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
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
		$output['judul_besar'] = 'Evaluasi ';
		$output['judul_kecil'] = 'Mahasiswa';
		$output['menu_aktif'] = 'm_daftar_mahasiswa';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = 'e_skripsi_daftar_mahasiswa/e_skripsi_daftar_mahasiswa_view';
		$output['after_page'] = 'e_skripsi_daftar_mahasiswa/e_skripsi_daftar_mahasiswa_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{

		$list = $this->E_skripsi_daftar_mahasiswa_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nipd;
			$row[] = $rg->nm_pd;
			$row[] = $rg->analisis_ipk;
			$row[] = $rg->analisis_sks_lulus;
			$row[] = $rg->analisis_sks_tidak_lulus;
			$row[] = $rg->is_boleh_ajukan_judul == 'Y' ? 'Bisa ajukan Judul' : 'Tidak Bisa ajukan judul';


			$row[] = "<a href='javascript:void(0)' class='btn btn-default btn-sm green-stripe' onclick='view_mahasiswa(\"" . $rg->nipd . "\")'>Tabel Nilai</a>";
			$row[] = "<a href='javascript:void(0)' class='btn btn-default btn-sm green-stripe' onclick='view_makul(\"" . $rg->nipd . "\")'>Edit Evaluasi</a>";


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->E_skripsi_daftar_mahasiswa_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_daftar_mahasiswa_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function view($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->get_by_id($id));
	}

	public function view_makul($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->E_skripsi_daftar_mahasiswa_model->get_analisis_data($id));
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = [];
		$data['status'] = TRUE;

		$this->_validate('update');

		$update = [
			'is_boleh_ajukan_judul'	=> $this->input->post('is_boleh_ajukan_judul') == TRUE ? 'Y' : 'N',
			'keterangan' => $this->input->post('keterangan')
		];
		$this->db->where('nipd', $this->input->post('nipd'));
		$this->db->update('siakad_skripsi_analisis', $update);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
		//}
	}

	private function _validate($tipe)
	{
		if ($tipe == 'add') {
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
		} else {
			$this->form_validation->set_error_delimiters('', '');
		}

		/* 	$this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
		$this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric'); */
	}

	public function get_by_id($nipd)
	{
		$this->load->library('table');
		$id_smt = $this->session->userdata("tahun_ajaran");

		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, mp.id_reg_pd, mp.id_reg_pd_feeder')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$id_reg_pd = $mahasiswa->id_reg_pd;
		$id_reg_pd_feeder = $mahasiswa->id_reg_pd_feeder;

		$nilai_transfer =  $this->db->where(['id_reg_pd' => $id_reg_pd_feeder])
			->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk')
			->from('nilai_transfer n')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = n.id_mk')
			->get();

		$nilai_mahasiswa_1 = $this->db->where(['id_reg_pd' => $id_reg_pd, 'id_smt <=' => $id_smt])
			->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('arsip_nilai n')
			->join('arsip_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('arsip_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();

		$nilai_mahasiswa_2 = $this->db->where(['nipd' => $nipd, 'id_smt <=' => $id_smt])
			->select('mk.kode_mk, n.*, mk.nm_mk, mk.sks_mk, kk.id_smt')
			->from('siakad_nilai_baru n')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = n.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->order_by('id_smt')
			->get();


		$table = "";
		$tmpl = [
			'table_open'          => '<table class="table table-bordered">',
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

		$jumlah = $jumlah_sks_mk = $jumlah_sks_lulus = $jumlah_sks_tidak_lulus = 0;
		$makul_tidak_lulus = [];

		foreach ($nilai_transfer->result() as $l_nilai) {
			$hasil = $l_nilai->sks_diakui * $l_nilai->nilai_angka_diakui;
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				'Transfer',
				$l_nilai->kode_mk,
				$l_nilai->nm_mk,
				['data' => $l_nilai->nilai_huruf_diakui, 'class' => 'text-center'],
				['data' => $l_nilai->nilai_angka_diakui, 'class' => 'text-center'],
				['data' => (int) $l_nilai->sks_diakui, 'class' => 'text-center'],
				['data' => $hasil, 'class' => 'text-center']
			]);

			$jumlah += $hasil;
			if (!empty($l_nilai->nilai_huruf_diakui)) {
				$jumlah_sks_mk += (int) $l_nilai->sks_diakui;
			}

			if ($l_nilai->nilai_huruf_diakui == 'E') {
				$jumlah_sks_tidak_lulus += (int) $l_nilai->sks_diakui;
				$makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
			}
		}

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

			if ($l_nilai->nilai_huruf == 'E') {
				$jumlah_sks_tidak_lulus += (int) $l_nilai->sks_mk;
				$makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
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

			if ($l_nilai->nilai_huruf == 'E') {
				$jumlah_sks_tidak_lulus += (int) $l_nilai->sks_mk;
				$makul_tidak_lulus[] = $l_nilai->kode_mk . ' | ' . $l_nilai->nm_mk;
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
		$data = [
			'query' => $mahasiswa,
			'nilai_tabel' => $nilai_tabel,
			'analisis_ipk' => $ips,
			'analisis_sks_total' => $jumlah_sks_mk,
			'analisis_sks_tidak_lulus' => $jumlah_sks_tidak_lulus,
			'analisis_sks_lulus' => $jumlah_sks_mk - $jumlah_sks_tidak_lulus,
			'makul_tidak_lulus' => $makul_tidak_lulus
		];
		return $data;
	}
}
