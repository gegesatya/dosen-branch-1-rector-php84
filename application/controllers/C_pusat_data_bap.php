<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pusat_data_bap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Pusat_data_bap_model', 'Pusat_data_bap_model');
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
		$output['judul_besar'] 		= 'Pusat Data BAP ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_pusat_data_bap';
		$output['parent_data_center']	= 'active open';
		$output['content'] 			= 'pusat_data_bap/pusat_data_bap_view';
		$output['after_page'] 		= 'pusat_data_bap/pusat_data_bap_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Pusat_data_bap_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $n) {
			$html = '<a href="' . site_url('C_pusat_data_bap/cetak_pdf_berita_acara') . '/' . $n->id . '/' . $n->id_smt . '" target="new" class="btn btn-sm btn-default green-stripe"><i class="fa fa-bar-chart-o"></i> View BAP</a>';
			$html_copy = '<a href="javascript:void(0)" 
						 class="btn btn-sm btn-default green-stripe"
						 onclick="ajax_copy(\'' . $n->id . '\')">
						 <i class="fa fa-file"></i> Copy BAP</a>';
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $n->id_smt;
			$row[] = $n->tag_name;
			$row[] = $n->nm_ptk;
			//$row[] = $html_copy;
			$row[] = $html;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Pusat_data_bap_model->count_all(),
			"recordsFiltered" => $this->Pusat_data_bap_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function cetak_pdf_berita_acara($id_jadwal = null, $id_smt = null)
	{
		$jadwal = $this->db->select('*')->from('siakad_nilai_baru sn')
			->join('siakad_jadwal j', 'j.id = sn.id_jadwal')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('semester s', 's.id_smt = j.id_smt')
			->where('j.id', $id_jadwal)->get()->row();

		$pertemuan = $this->db->select('*')->from('siakad_rencana_pertemuan')
			->where('id_jadwal', $id_jadwal)->get()->result();

		$output['nm_ptk'] = $jadwal->nm_ptk;
		$output['nm_mk'] = $jadwal->tag_name;
		$output['nm_lemb'] = '';
		$output['nm_smt'] = $jadwal->nm_smt;
		$output['id_smt'] = $jadwal->id_smt;
		$output['list_pertemuan'] = $pertemuan;
		$output['statistik'] = $this->count_statistik($id_jadwal);
		$output['id_smt'] = $id_smt;
		$output['tipe_file'] = 1;
		$output['class_table'] = 'table-black';

		$html = $this->load->view('cetak/pdf_pusat_data_bap', $output, true);
		$pdfFilePath  = 'BAP ' . $jadwal->tag_name . ' Semester ' . $jadwal->nm_smt . '.pdf';

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);

		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	private function count_statistik($id_kls)
	{
		$dt_kelas = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master')->row();
		$dt_absen = $this->db->where('id_kls', $id_kls)->get('arsip_absen_master');
		$dt_teori = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Teori'])
			->get('arsip_absen_master');
		$dt_praktek = $this->db
			->where(['id_kls' => $id_kls, 'kategori_pertemuan' => 'Praktek'])
			->get('arsip_absen_master');

		$jumlah_mahasiswa = $this->db->where('id_kls', $id_kls)->get('feeder_nilai')->num_rows();
		$jumlah_pertemuan 	= $dt_absen->num_rows();
		$jumlah_teori 		= $dt_teori->num_rows();
		$jumlah_praktek 	= $dt_praktek->num_rows();

		$data = [];
		$data['jumlah_pertemuan'] 	= $jumlah_pertemuan;
		$data['jumlah_teori'] 		= $jumlah_teori;
		$data['jumlah_praktek'] 	= $jumlah_praktek;
		$data['jumlah_mahasiswa'] 	= $jumlah_mahasiswa;
		return $data;
	}
}
