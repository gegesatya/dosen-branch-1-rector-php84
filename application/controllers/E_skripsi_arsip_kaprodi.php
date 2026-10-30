<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_arsip_kaprodi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_arsip_kaprodi_model');
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
		$output['judul_besar'] = 'Arsip TA Kedaluwarsa';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_arsip_ta_kaprodi';
		$output['parent_admin_ta'] = 'active open';
		$output['content'] = 'e_skripsi_arsip_kaprodi/e_skripsi_arsip_kaprodi_view';
		$output['after_page'] = 'e_skripsi_arsip_kaprodi/e_skripsi_arsip_kaprodi_after_page';
		
		$output['list_dosen'] = $this->E_skripsi_arsip_kaprodi_model->get_dosen_list();

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->E_skripsi_arsip_kaprodi_model->get_datatables();
		$data = [];
		$no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
		foreach ($list as $rg) {
			$no++;
			$row = [];
			
			$tgl_sk = date('d-m-Y', strtotime($rg->tanggal_sk));

			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = '<b>' . $rg->nm_pd . '</b><br>' . $rg->nipd;
			$row[] = '<b>' . $rg->nm_pembimbing_1 . '</b><br><small>SK: ' . $rg->nomor_sk . '</small><br><small>Tgl: ' . $tgl_sk . '</small>';
			$row[] = $rg->judul_ditetapkan;
			
			$row[] = '<span class="text-danger"><i class="fa fa-warning"></i> SK Kedaluwarsa</span><br><small>Melewati 1 Tahun</small>';

			// Aksi Perpanjang
			$btn = '<button class="btn btn-sm btn-primary btn-block" onclick="perpanjang_sk(' . $rg->id_judul_skripsi . ',\'' . $rg->dosen_pembimbing_1 . '\')"><i class="fa fa-refresh"></i> Perpanjang SK</button>';
			
			$row[] = $btn;

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'] ?? 0,
			"recordsTotal" => $this->E_skripsi_arsip_kaprodi_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_arsip_kaprodi_model->count_filtered(),
			"data" => $data,
		];
		echo json_encode($output);
	}

	public function ajax_get_achievement($id_judul_skripsi)
	{
		$this->db->select('pj.nipd, m.nm_pd, pj.judul_ditetapkan, ns.seminar_tanggal as sempro_tgl, ns.sempro_hasil, ns.semhas_tanggal as semhas_tgl, ns.semhas_hasil, ns.sidang_tanggal as sidang_tgl, ns.sidang_hasil, 
							dp1.nama_lengkap as penguji_1, dp2.nama_lengkap as penguji_2, dp3.nama_lengkap as penguji_3');
		$this->db->from('siakad_skripsi_pengajuan_judul pj');
		$this->db->join('mahasiswa_pt mpt', 'mpt.nipd = pj.nipd', 'left');
		$this->db->join('mahasiswa m', 'm.id_pd = mpt.id_pd', 'left');
		$this->db->join('siakad_skripsi_nilai_sempro ns', 'ns.id_judul_skripsi = pj.id_judul_skripsi AND ns.id_penilai = pj.dosen_pembimbing_1', 'left');
		$this->db->join('siakad_skripsi_dosen dp1', 'dp1.id_ptk = pj.dosen_penguji_sempro_1', 'left');
		$this->db->join('siakad_skripsi_dosen dp2', 'dp2.id_ptk = pj.dosen_penguji_sempro_2', 'left');
		$this->db->join('siakad_skripsi_dosen dp3', 'dp3.id_ptk = pj.dosen_penguji_sempro_3', 'left');
		$this->db->where('pj.id_judul_skripsi', $id_judul_skripsi);
		$data = $this->db->get()->row();

		echo json_encode(['status' => TRUE, 'data' => $data]);
	}

	public function ajax_perpanjang()
	{
		$id_judul_skripsi = $this->input->post('id_judul_skripsi');
		$dosen_pembimbing_1 = $this->input->post('dosen_pembimbing_1');
		$clear_penguji = $this->input->post('clear_penguji'); // true / false
		$clear_nomor_sk = $this->input->post('clear_nomor_sk'); // true / false

		if (empty($id_judul_skripsi) || empty($dosen_pembimbing_1)) {
			echo json_encode(['status' => FALSE, 'pesan' => 'Data tidak lengkap']);
			return;
		}

		$data_update = [
			'status_sk' => 1,
			'tanggal_sk' => date('Y-m-d'),
			'dosen_pembimbing_1' => $dosen_pembimbing_1
		];

		if ($clear_penguji == 'true') {
			$data_update['dosen_penguji_sempro_1'] = NULL;
			$data_update['dosen_penguji_sempro_2'] = NULL;
			$data_update['dosen_penguji_sempro_3'] = NULL;
			$data_update['no_surat_tugas'] = NULL;
		}

		if ($clear_nomor_sk == 'true') {
			$data_update['nomor_sk'] = NULL;
		}

		$this->db->where('id_judul_skripsi', $id_judul_skripsi);
		$this->db->where('id_sms', $this->session->userdata('prodi'));
		$this->db->update('siakad_skripsi_pengajuan_judul', $data_update);

		echo json_encode(['status' => TRUE]);
	}
}
