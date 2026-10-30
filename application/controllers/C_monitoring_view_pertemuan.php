<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_monitoring_view_pertemuan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Monitoring_pertemuan_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus untuk KAPRODI.');
		}
	}

	public function index($id_jadwal)
	{
		$output['judul_besar'] = 'Monitoring Pertemuan ';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_monitoring';
		$output['parent_2'] = 'active open';
		$output['content'] = 'monitoring_pertemuan/monitoring_pertemuan_view';
		$output['after_page'] = 'monitoring_pertemuan/monitoring_pertemuan_after_page';

		$jadwal = $this->db->where(['id' => $id_jadwal])->from('siakad_jadwal j')
			->select('j.*, r.nama_ruang, h.nama_hari, sd.nm_ptk')
			->join('siakad_dosen sd', 'sd.id_ptk = j.id_sdm')
			->join('siakad_ruang r', 'r.uniq = j.id_ruang')
			->join('siakad_hari h', 'h.uniq = j.id_hari')
			->get()->row();
		$output['jadwal'] = $jadwal;

		$this->load->view('master_page_2', $output);
	}

	public function ajax_list($id_jadwal = NULL)
	{

		$list = $this->Monitoring_pertemuan_model->get_datatables($id_jadwal);
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $rencana) {
			$i = $no + 1;
			$jumlah_mahasiswa = $this->db->where(compact('id_jadwal'))->get('siakad_nilai_baru')->num_rows();
			$jumlah_hadir = $this->db->where(['id_jadwal' => $id_jadwal, 'meet_' . $i => 'H'])->get('siakad_absen_detail_baru')->num_rows();
			$id_rps = $this->db->where('id_jadwal', $id_jadwal)->get('siakad_new_rps_mk_prodi')->row(1)->id_rps;
			$konten_rps = $this->db->where(['id_rps' => $id_rps, 'kolom_2' => $i])->get('siakad_rps_online_detail')->row();

			if ($rencana->is_realisasi == 1) {
				$status_realisasi = 'Sudah';
				$tgl_realisasi = 'Tgl : ' . tgl_indo(date('Y-m-d', strtotime($rencana->tanggal_realisasi)));
			} else {
				$status_realisasi = 'Belum';
				$tgl_realisasi = '';
			}
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = '<strong>Meet : </strong>' . $rencana->pertemuan . '<br>' . tgl_indo($rencana->tanggal_rencana) . '<br><small>' . substr($rencana->jam_mulai, 0, 5) . ' sd ' . substr($rencana->jam_selesai, 0, 5) . '</small>';;
			$row[] = '<strong>Di Kelas : </strong><br>' . $this->getExcerpt($rencana->berita_acara_rencana, 0, 200) . '<br><strong>Di RPS :</strong> <br>' . $konten_rps->kolom_7 . '<br><strong>Kemampuan akhir yang dirumuskan RPS:</strong><br>' . $konten_rps->kolom_6;
			$row[] = 'Kehadiran : ' . $jumlah_hadir . '/' . $jumlah_mahasiswa;
			$row[] = $status_realisasi . '<br>' . $tgl_realisasi;



			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Monitoring_pertemuan_model->count_all($id_jadwal),
			"recordsFiltered" => $this->Monitoring_pertemuan_model->count_filtered($id_jadwal),
			"data" => $data,
		];

		echo json_encode($output);
	}

	private function getExcerpt($str, $startPos = 0, $maxLength = 100)
	{
		if (strlen($str) > $maxLength) {
			$excerpt   = substr($str, $startPos, $maxLength - 3);
			$lastSpace = strrpos($excerpt, ' ');
			$excerpt   = substr($excerpt, 0, $lastSpace);
			$excerpt  .= '...';
		} else {
			$excerpt = $str;
		}

		return $excerpt;
	}
}
