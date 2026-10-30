<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_kaprodi_sebaran_nilai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Sebaran_nilai_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2];
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
		$this->load->helper('url');

		$output['judul_besar'] = 'Sebaran Nilai';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_sebaran_nilai';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'sebaran_nilai/sebaran_nilai_view';
		$output['after_page'] = 'sebaran_nilai/sebaran_nilai_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Sebaran_nilai_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$count_nilai_a = $this->hitung_nilai($rg->id_kls, 'A');
			$count_nilai_b_plus = $this->hitung_nilai($rg->id_kls, 'B+');
			$count_nilai_b = $this->hitung_nilai($rg->id_kls, 'B');
			$count_nilai_c_plus = $this->hitung_nilai($rg->id_kls, 'C+');
			$count_nilai_c = $this->hitung_nilai($rg->id_kls, 'C');
			$count_nilai_d = $this->hitung_nilai($rg->id_kls, 'D');
			$count_nilai_e = $this->hitung_nilai($rg->id_kls, 'E');

			$dosen = '-';

			if ($this->get_dosen($rg->id_kls)->num_rows() > 0) {
				$dosen = $this->get_dosen($rg->id_kls)->row()->nm_ptk;
			}

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nm_kls;
			$row[] = $rg->nm_mk;
			$row[] = $dosen;
			$row[] = $count_nilai_a;
			$row[] = $count_nilai_b_plus;
			$row[] = $count_nilai_b;
			$row[] = $count_nilai_c_plus;
			$row[] = $count_nilai_c;
			$row[] = $count_nilai_d;
			$row[] = $count_nilai_e;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Sebaran_nilai_model->count_all(),
			"recordsFiltered" => $this->Sebaran_nilai_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function hitung_nilai($id_kls, $nilai)
	{
		return $this->db->select('count(nilai_huruf) as jumlah')
			->from('siakad_nilai_baru')
			->where(['id_kls' => $id_kls, 'nilai_huruf' => $nilai])->get()->row()->jumlah;
	}

	private function get_dosen($id_kls)
	{
		$a = $this->db->where('k.id_kls', $id_kls)->from('siakad_jadwal j')
			->join('siakad_jadwal_detail jd', 'jd.id_jadwal = j.id')
			->join('feeder_kelas_kuliah k', 'jd.id_kls = k.id_kls')
			->join('siakad_dosen d', 'd.id_ptk = j.id_sdm')
			->get();

		return $a;
	}
}
