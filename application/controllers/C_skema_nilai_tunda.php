<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_skema_nilai_tunda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Skema_nilai_tunda_model');
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

		$output['judul_besar'] = 'Skema Nilai Tunda ';
		$output['judul_kecil'] = 'Tahun Ajaran ' . $this->session->userdata('setting')->id_smt_tunda . ' (Tunda/Revisi)';
		$output['menu_aktif'] = 'm_skema_nilai_tunda';
		$output['parent_tunda'] = 'active open';
		$output['content'] = 'skema_nilai_tunda/skema_nilai_tunda_view';
		$output['after_page'] = 'skema_nilai_tunda/skema_nilai_tunda_after_page';
		$output['dd_kelas_kuliah'] = $this->Skema_nilai_tunda_model->f_dropdown('feeder_kelas_kuliah', 'id_kls', 'nm_kls');
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Skema_nilai_tunda_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$html = '
					<a href="javascript:void()" class="btn btn-sm btn-primary"
						onclick="edit_skema_nilai(' . "'" . $rg->id . "'" . ')">
						<i class="icon-pencil"></i> Edit
					</a>
					';

			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);
			$detail = $this->db->where('id_jadwal', $rg->id)->get('siakad_jadwal_detail');
			if ($detail->num_rows() == 0) {
				$kk = '-';
			} else {
				$kk = '';
				foreach ($detail->result() as $key => $value) {
					$kk .= '[' . $value->nm_kls . '] ' . $value->nm_mk . '<br>';
				}
			}

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->tag_name;
			$row[] = $kk;

			$row[] = $rg->sikap;
			$row[] = $rg->quiz;
			$row[] = $rg->tugas_terstruktur;
			$row[] = $rg->uts;
			$row[] = $rg->uas;
			$row[] = $rg->unjuk_kinerja;
			$row[] = $rg->proyek;
			$row[] = $rg->portofolio;
			$row[] = $rg->produk;
			$row[] = $rg->presentasi;

			$row[] = $html;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Skema_nilai_tunda_model->count_all(),
			"recordsFiltered" => $this->Skema_nilai_tunda_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Skema_nilai_tunda_model->get_by_id($id);
		$data->jam_mulai = substr($data->jam_mulai, 0, 5);
		$data->jam_selesai = substr($data->jam_selesai, 0, 5);

		echo json_encode($data);
	}

	public function ajax_skema_edit($id_kls)
	{
		$data = $this->db->where('id_kls', $id_kls)->get('feeder_kelas_kuliah')->row();
		$id_mk = $data->id_mk;
		$nm_mk = $this->db->where('id_mk', $id_mk)->get('feeder_mata_kuliah')->row()->nm_mk;
		$data->nm_mk = $nm_mk;
		echo json_encode($data);
	}

	public function ajax_update_skema_nilai()
	{
		$this->_validate_skema_nilai();
		$user = $this->ion_auth->user()->row();
		$data = [
			'sikap' => $this->input->post('sikap'),
			'quiz' => $this->input->post('quiz'),
			'tugas_terstruktur' => $this->input->post('tugas_terstruktur'),
			'uts' => $this->input->post('uts'),
			'uas' => $this->input->post('uas'),
			'unjuk_kinerja' => $this->input->post('unjuk_kinerja'),
			'portofolio' => $this->input->post('portofolio'),
			'proyek' => $this->input->post('proyek'),
			'produk' => $this->input->post('produk'),
			'presentasi' => $this->input->post('presentasi'),
			'is_skema' => 1,
			'change_skema_by' => $user->first_name,
			'change_skema_date' =>	date('Y-m-d H:i:s')
		];

		$this->Skema_nilai_tunda_model->update(['id' => $this->input->post('id')], $data);
		echo json_encode(["status" => TRUE]);
	}

	private function _validate_skema_nilai()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		$sikap = (int)$this->input->post('sikap');
		$quiz = (int)$this->input->post('quiz');
		$tugas_terstruktur = (int)$this->input->post('tugas_terstruktur');
		$uts = (int)$this->input->post('uts');
		$uas = (int)$this->input->post('uas');
		$unjuk_kinerja = (int)$this->input->post('unjuk_kinerja');
		$produk = (int)$this->input->post('produk');
		$portofolio = (int)$this->input->post('portofolio');
		$proyek = (int)$this->input->post('proyek');
		$presentasi = (int)$this->input->post('presentasi');

		$total = $sikap + $quiz + $tugas_terstruktur + $uts + $uas + $unjuk_kinerja + $produk + $portofolio + $proyek + $presentasi;

		if ($sikap == 0) {
			$data['inputerror'][] = 'sikap';
			$data['error_string'][] = 'WAJIB diisi.';
			$data['status'] = FALSE;
		}

		/*if ($quiz == 0) {
			$data['inputerror'][] = 'quiz';
			$data['error_string'][] = 'Nilai harus diisi.';
			$data['status'] = FALSE;
		}*/

		if ($tugas_terstruktur == 0) {
			$data['inputerror'][] = 'tugas_terstruktur';
			$data['error_string'][] = 'WAJIB diisi.';
			$data['status'] = FALSE;
		}

		if ($uts == 0) {
			$data['inputerror'][] = 'uts';
			$data['error_string'][] = 'WAJIB diisi.';
			$data['status'] = FALSE;
		}

		if ($uas == 0) {
			$data['inputerror'][] = 'uas';
			$data['error_string'][] = 'WAJIB diisi.';
			$data['status'] = FALSE;
		}

		/*if ($unjuk_kinerja == 0) {
			$data['inputerror'][] = 'unjuk_kinerja';
			$data['error_string'][] = 'Nilai harus diisi.';
			$data['status'] = FALSE;
		}*/

		if ($total <> 100) {
			$data['inputerror'][] = 'jumlah_total';
			$data['error_string'][] = 'Perbaiki input data, jumlah total harus = 100%.';
			$data['status'] = FALSE;
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
