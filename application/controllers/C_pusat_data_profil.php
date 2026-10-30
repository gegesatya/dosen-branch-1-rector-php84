<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_profil extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_profil_model', 'Pusat_data_profil_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0) {
			return show_error('Forbidden Access.');
		}
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Pusat Data Profil Mahasiswa ';
		$output['judul_kecil'] 		= '';
		$output['menu_aktif'] 		= 'm_pusat_data_profil';
		$output['parent_data_center'] = 'active open';
		$output['content'] 			= 'pusat_data_profil_mahasiswa/pusat_data_profil_mahasiswa_view';
		$output['after_page'] 		= 'pusat_data_profil_mahasiswa/pusat_data_profil_mahasiswa_after_page';
		$output['master_page'] 		= 'ajax_pagination';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Pusat_data_profil_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $student) {
			$html = '<a href="' . site_url("C_pusat_data_profil/download_data_akademik/$student->nipd") . '" class="btn btn-sm btn-primary" target="_new">
				<i class="icon-speedometer"></i> Download</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $student->nipd;
			$row[] = $student->nm_pd;
			$row[] = $student->nm_lemb;
			$row[] = $student->nm_ptk;
			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pusat_data_profil_model->count_all(),
			"recordsFiltered" => $this->Pusat_data_profil_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function download_data_akademik($nipd)
	{
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.*, mp.nipd, mp.id_reg_pd, s.nm_lemb, mp.tgl_masuk_sp, sd.nm_ptk')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->join('sms s', 's.id_sms = mp.id_sms')
			->join('siakad_dosen sd', 'sd.id_ptk = m.dosen_pa', 'LEFT')
			->get()->row();
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
		$this->table->set_heading(['No', 'SMT',  'Kode/Code', 'Mata Kuliah/Subjects', 'Lambang/<br>Mark', 'Mutu/<br>Quality', 'SKS/<br>Credit', 'Credit <br>X<br> Quality']);


		$i = 1;
		$j = 1;

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

		$tmpl2 = [
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

		$this->table->set_template($tmpl2);
		$this->table->set_heading(['No', 'SMT',  'Mata Kuliah/Subjects', 'Dosen', 'Kritik Saran']);

		$data_kritik_saran = $this->db->where('nipd', $nipd)->from('siakad_evaluasi_dosen ed')
			->join('siakad_jadwal j', 'j.id = ed.id_jadwal')
			->join('feeder_kelas_kuliah fk', 'fk.id_kls = ed.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = fk.id_mk')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->get()->result();

		foreach ($data_kritik_saran as $dt) {

			$this->table->add_row([
				['data' => $j++ . '.', 'class' => 'text-right'],
				$dt->id_smt,
				$dt->kode_mk . '<br>' . $dt->nm_mk,
				$dt->nm_ptk,
				$dt->kritik_saran
				/* array('data' => $l_nilai->nilai_huruf, 'class' => 'text-center'),
				array('data' => $l_nilai->nilai_indeks, 'class' => 'text-center'),
				array('data' => (int) $l_nilai->sks_mk, 'class' => 'text-center'),
				array('data' => $hasil, 'class' => 'text-center') */
			]);
		}

		$kritik_saran =  $this->table->generate();

		$output['mahasiswa'] = $mahasiswa;
		$output['tabel'] = $nilai_tabel;
		$output['tbl_kritik_saran'] = $kritik_saran;

		//var_dump($nilai_tabel);

		$html = $this->load->view('cetak/pdf_profil_mahasiswa', $output, true);
		$pdfFilePath  = 'Data Akademik ' . $mahasiswa->nm_pd .  '.pdf';
		$mpdf = new \Mpdf\Mpdf();

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}
}
