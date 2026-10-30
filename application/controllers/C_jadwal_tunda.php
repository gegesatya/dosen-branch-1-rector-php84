<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal_tunda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Jadwal_model_tunda');
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
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('setting')->id_smt_tunda;
		$siakad_krs = $this->db->where(['id_dosen_siakad' => $id_ptk, 'id_status_krs' => 2, 'id_smt' => $id_smt])->get('siakad_krs');
		if ($siakad_krs->num_rows() > 0) {
			$this->session->set_flashdata('msg_krs', 'Periksa kembali menu KRS, ada mahasiswa membutuhkan persetujuan KRS.');
			$this->session->set_flashdata('jumlah', $siakad_krs->num_rows());
		}

		$output['judul_besar'] = 'Jadwal Kuliah ';
		$output['judul_kecil'] = 'Tahun Ajaran ' . $id_smt . ' (Tunda/Revisi)';
		$output['menu_aktif'] = 'm_jadwal_tunda';
		$output['parent_tunda'] = 'active open';
		$output['content'] = 'jadwal_tunda/jadwal_tunda_view';
		$output['after_page'] = 'jadwal_tunda/jadwal_tunda_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Jadwal_model_tunda->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);
			$detail = $this->db->where('id_jadwal', $rg->id)
				->select('kk.nm_kls, mk.nm_mk,  mk.kode_mk')
				->from('siakad_jadwal_detail jd')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->order_by('nm_kls')
				->get();

			if ($detail->num_rows() == 0) {
				$nmkls = $kk = $kuri = '-';
			} else {
				$nmkls = $kk = $kuri = '';
				foreach ($detail->result() as $key => $value) {
					$kk .= '[' . $value->kode_mk . '] ' . $value->nm_mk . '<br>';
					$nmkls .=  $value->nm_kls . '<br>';
				}
			}

			$jadwal_detail = $this->db->where(['id_jadwal' => $rg->id])
				->from('siakad_jadwal_detail a')
				->join('feeder_kelas_kuliah b', 'a.id_kls = b.id_kls')
				->join('feeder_mata_kuliah c', 'c.id_mk = b.id_mk')
				->get();
			$data_rps = '';

			if ($jadwal_detail->num_rows() > 0) {
				$details = $jadwal_detail->result();
				foreach ($details as $key => $value) {
					if (!empty($value->id_mk)) {
						$data_rps = $this->db->where('id_mk', $value->id_mk)->get('siakad_rps_mk_prodi');
					}
				}
			}

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nama_hari . '<br><small>' . $jam . '</small>';
			$row[] = $rg->nama_ruang;
			$row[] = $rg->tag_name;
			$row[] = $nmkls;
			$row[] = $kk;

			if ($data_rps <> '') {
				if ($data_rps->num_rows() > 0) {
					$html_preview = '<a href="' . site_url('C_rps_online/preview_rps/' . $data_rps->row()->id_rps) .
						'" class="btn btn-default btn-sm green-stripe" target="_new"  title="Preview RPS"> View </a>';

					$html_copy = '<a href="javascript:void(0)" 
						 class="btn btn-sm btn-default green-stripe"
						 onclick="ajax_copy(\'' . $data_rps->row()->id_rps . '\',\'' . $rg->id . '\')">
						 Generate BAP</a>';

					if ($rg->is_generate_bap == 0) {
						$row[] = $html_preview;
						$row[] = $html_copy;
					} else {
						$row[] = $html_preview;
						$row[] = 'Sudah generate BAP';
					}
				} else {
					$row[] = '-';
					$row[] = '-';
				}
			} else {
				$row[] = '-';
				$row[] = '-';
			}


			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Jadwal_model_tunda->count_all(),
			"recordsFiltered" => $this->Jadwal_model_tunda->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}


	public function get_copy_data($id_rps)
	{
		$data = $this->db->where('id_rps', $id_rps)->get('siakad_rps_online')->row();
		$nm_ptk = $this->db->where('id_ptk', $data->id_ptk)->get('siakad_dosen')->row();
		$data->nm_ptk = $nm_ptk->nm_ptk;
		echo json_encode($data);
	}

	public function ajax_generate_bap()
	{
		//$this->_validate();
		$id_rps = $this->security->xss_clean($this->input->post('id_rps')); //sumber
		$id_jadwal = $this->security->xss_clean($this->input->post('id_jadwal')); //tujuan
		$start_date = strtotime($this->input->post('tanggal_mulai'));

		$rps = $this->db->where('o.id_rps', $id_rps)->from('siakad_rps_online o')
			->join('siakad_rps_online_detail d', 'o.id_rps = d.id_rps')->get()->result();
		$jadwal_master = $this->db->where('id', $id_jadwal)->get('siakad_jadwal');

		$data = [];

		$minggu_a = 'NIL';

		foreach ($rps as $key => $value) {

			/* $rp_exist = $this->db->where(['id_jadwal' => $id_jadwal_tujuan, 'pertemuan' => $value->pertemuan])
				->get('siakad_rencana_pertemuan'); */

			//if ($rp_exist->num_rows() == 0) {
			$id_jadwal 				= $id_jadwal;
			//$tanggal_rencana 		= $start_date;
			$pertemuan 				= $value->kolom_2;
			$minggu 				= $value->kolom_1;
			$jam_mulai 				= $jadwal_master->row()->jam_mulai;
			$jam_selesai 			= $jadwal_master->row()->jam_selesai;
			$jam_teori 				= $value->kolom_3;
			$jam_praktek 			= $value->kolom_4;
			$berita_acara_rencana	= $value->kolom_7;
			$bacaan_materi			= $value->kolom_5;
			$create_by 				= $this->ion_auth->user()->row()->id;
			$create_date 			= date('Y-m-d h:i:s');

			//}

			if ($minggu_a <> $minggu) {
				if ($minggu <> 1) {
					$start_date = strtotime("+7 day", $start_date);
				}
			}

			$tanggal_rencana 		= date('Y-m-d', $start_date);
			$data[] = compact(
				'id_jadwal',
				'tanggal_rencana',
				'pertemuan',
				'minggu',
				'jam_mulai',
				'jam_selesai',
				'jam_teori',
				'jam_praktek',
				'berita_acara_rencana',
				'bacaan_materi',
				'create_by',
				'create_date'
			);

			$minggu_a = $value->kolom_1;
		}

		if ($data) {
			$this->db->insert_batch('siakad_rencana_pertemuan', $data);
			$this->db->where(['id' => $id_jadwal])->update('siakad_jadwal', ['is_generate_bap' => 1]);
		}

		echo json_encode(["status" => TRUE]);
	}
}
