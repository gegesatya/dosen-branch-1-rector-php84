<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_monitoring extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Monitoring_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		// $group = array(2);
		// if (!$this->ion_auth->in_group($group)) {
		// 	redirect('auth/logout', 'refresh');
		// }

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index()
	{
		$output['judul_besar'] = 'Monitoring Perkuliahan ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_monitoring';
		$output['parent_kaprodi'] = 'active open';
		$output['content'] = 'monitoring/monitoring_view';
		$output['after_page'] = 'monitoring/monitoring_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Monitoring_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rg) {
			$jam = substr($rg->jam_mulai, 0, 5) . ' sd ' . substr($rg->jam_selesai, 0, 5);
			$jumlah_mahasiswa = $this->db->where('id_jadwal', $rg->id)->get('siakad_nilai_baru')->num_rows();
			$realisasi = 0;
			$rencana_pertemuan = $this->db->where('id_jadwal', $rg->id)->get('siakad_rencana_pertemuan');
			if ($rencana_pertemuan->num_rows()) {
				$realisasi = $this->db->where(['id_jadwal' => $rg->id, 'is_realisasi' => 1])->get('siakad_rencana_pertemuan')->num_rows();
			}

			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nm_ptk;
			$row[] = $rg->nama_hari . '<br><small>' . $jam . '<br>' . $rg->nama_ruang . '</small>';
			$row[] = $rg->tag_name;
			$row[] = "Jml Mahasiswa : " . $jumlah_mahasiswa . "<br>Realisasi : " . $realisasi . '<br>Rencana : ' . $rencana_pertemuan->num_rows();

			if ($rencana_pertemuan->num_rows() > 0) {
				$lihat_pertemuan = '<a href="C_monitoring_view_pertemuan/index/' . $rg->id . '" 
											class="btn btn-sm btn-default green-stripe" target="_new">
											View</a>';
			} else {
				$lihat_pertemuan = 'Belum ada RPS';
			}

			$row[] = $lihat_pertemuan;
			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Monitoring_model->count_all(),
			"recordsFiltered" => $this->Monitoring_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}
}
