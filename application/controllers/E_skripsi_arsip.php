<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_arsip extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_arsip_model');
		$this->load->helper('gg');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus dosen.');
		}
	}

	public function index()
	{
		$this->load->helper('url');

		$id_ptk = $this->session->userdata('id_ptk');

		// 1. Ambil data distribusi nilai mutu TA (A, B+, B, C+, C, dst.) untuk chart donut
		$q_nilai = $this->db->select('ns_lulus.sidang_nilai_mutu, COUNT(*) as jumlah')
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_nilai_sempro ns_lulus',
				'ns_lulus.id_judul_skripsi = s.id_judul_skripsi
				 AND ns_lulus.id_penilai = pj.dosen_pembimbing_1
				 AND ns_lulus.sidang_hasil = \'LULUS\'')
			->where('s.id_penilai', $id_ptk)
			->group_by('ns_lulus.sidang_nilai_mutu')
			->get()
			->result();

		$dist_nilai = ['A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'Lainnya' => 0];
		foreach ($q_nilai as $n) {
			$mutu = trim(strtoupper((string)$n->sidang_nilai_mutu));
			if (array_key_exists($mutu, $dist_nilai)) {
				$dist_nilai[$mutu] = (int)$n->jumlah;
			} else {
				$dist_nilai['Lainnya'] += (int)$n->jumlah;
			}
		}
		$output['donut_data'] = array_values($dist_nilai);
		$output['donut_labels'] = array_keys($dist_nilai);

		// 2. Ambil data ketepatan waktu kelulusan (Tepat Waktu <= 4 tahun / 8 semester sejak masuk) untuk pie chart
		$q_mhs = $this->db->select('mp.tgl_masuk_sp, ns_lulus.sidang_tanggal')
			->from('siakad_skripsi_nilai_sempro s')
			->join('siakad_skripsi_pengajuan_judul pj', 'pj.id_judul_skripsi = s.id_judul_skripsi')
			->join('siakad_skripsi_nilai_sempro ns_lulus',
				'ns_lulus.id_judul_skripsi = s.id_judul_skripsi
				 AND ns_lulus.id_penilai = pj.dosen_pembimbing_1
				 AND ns_lulus.sidang_hasil = \'LULUS\'')
			->join('mahasiswa_pt mp', 'mp.nipd = s.nipd')
			->where('s.id_penilai', $id_ptk)
			->group_by('pj.id_judul_skripsi')
			->get()
			->result();

		$tepat_waktu = 0;
		$tidak_tepat = 0;

		foreach ($q_mhs as $m) {
			$thn_masuk = !empty($m->tgl_masuk_sp) ? date('Y', strtotime($m->tgl_masuk_sp)) : null;
			$thn_lulus = !empty($m->sidang_tanggal) ? date('Y', strtotime($m->sidang_tanggal)) : null;
			if ($thn_masuk && $thn_lulus) {
				$lama_studi = $thn_lulus - $thn_masuk;
				if ($lama_studi <= 4) {
					$tepat_waktu++;
				} else {
					$tidak_tepat++;
				}
			} else {
				// Default fallback jika tanggal kosong
				$tepat_waktu++;
			}
		}

		$output['pie_data'] = [$tepat_waktu, $tidak_tepat];
		$output['pie_labels'] = ['Tepat Waktu (≤ 4 Tahun)', 'Terlambat (> 4 Tahun)'];

		$output['judul_besar'] = 'Arsip Tugas Akhir';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_arsip_ta';
		$output['parent_skripsi'] = 'active open';
		$output['content'] = 'E_skripsi_arsip/E_skripsi_arsip_view';
		$output['after_page'] = 'E_skripsi_arsip/E_skripsi_arsip_after_page';
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$this->output->set_content_type('application/json');

		$list = $this->E_skripsi_arsip_model->get_datatables();
		$data = [];
		$no = isset($_POST['start']) ? (int)$_POST['start'] : 0;

		foreach ($list as $rg) {
			$no++;
			$row = [];
			$row[] = $no;
			$row[] = $rg->nipd .'<br>'.$rg->nm_pd;
			//$row[] = $rg->nm_pd;
			// $row[] = !empty($rg->analisis_ipk) ? $rg->analisis_ipk : '-';
			$row[] = !empty($rg->nilai_mutu) ? $rg->nilai_mutu : '-';
			// Strip sisa karakter kontrol
			$judul_clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $rg->judul_ta);
			$row[] = $judul_clean;
			$dewan = '<ol style="margin:0;padding-left:16px;">';
			foreach ([$rg->penguji_1, $rg->penguji_2, $rg->penguji_3] as $p) {
				if (!empty($p)) $dewan .= '<li>' . $p . '</li>';
			}
			$dewan .= '</ol>';
			$row[] = $dewan;

			$tgl_yudisium = '-';
			if (!empty($rg->tgl_sk_yudisium) && $rg->tgl_sk_yudisium != '0000-00-00') {
				$tgl_yudisium = tgl_indo($rg->tgl_sk_yudisium);
			}
			$row[] = $tgl_yudisium;

			$row[] = '<a class="btn btn-default red-stripe btn-xs" 
						href="' . site_url('E_skripsi_sidang/cetak_pdf/' .  md5($rg->id_judul_skripsi)) . '"  target = "_new"
						title="Berita Acara Sidang" ><i class="fa fa-download"></i> Download
					</a>';

			$data[] = $row;
		}

		$output = [
			"draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
			"recordsTotal" => $this->E_skripsi_arsip_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_arsip_model->count_filtered(),
			"data" => $data,
		];
		echo json_encode($output, JSON_UNESCAPED_UNICODE);
	}
}
