<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Eoffice_general_model extends CI_Model
{
	public $tbl = 'e_office';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function _cetak_pdf_eoffice($id_eoffice)
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
		} elseif ($data_eoffice->id_kategori_pengantar == 4) {
			$output['top'] = 680;
			$output['left'] = 500;
		}

		$output['data_eoffice'] = $data_eoffice;

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

	public function _view_krs($nipd, $tipe = "cetak")
	{
		$id_smt = $this->session->userdata('tahun_ajaran');
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd')->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();
		$krs = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap')
			->from('siakad_nilai_baru sn')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where([
				'sn.nipd' => $nipd,
				'kk.id_smt' => $id_smt
			])->get();

		$this->load->library('table');
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
		if ($tipe == "cetak") {
			$this->load->view('cetak/cetak_krs', $output);
		} else {
			$nm_pd = $mahasiswa->nm_pd;
			$nipd = $mahasiswa->nipd;
			$id_smt = $this->session->userdata('tahun_ajaran');

			echo json_encode(['tabel' => $nilai_tabel, 'nm_pd' => $nm_pd, 'nipd' => $nipd, 'id_smt' => $id_smt]);
		}
	}

	public function save($data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->insert($this->tbl, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$data = $this->security->xss_clean($data);
		$this->db->update($this->tbl, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_simawa', $id);
		$this->db->delete($this->tbl);
	}
}
