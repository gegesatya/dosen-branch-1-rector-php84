<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_pantauan_pkl extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

		// Load Redis Cache Driver
		$this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		// Hanya Kaprodi (group 5) yang boleh mengakses ini
		$group = [5];
		if (!$this->ion_auth->in_group($group)) {
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$id_sms = $this->session->userdata('prodi');
		
		$output['judul_besar'] = 'Pantauan PKL';
		$output['judul_kecil'] = 'Dashboard Kaprodi';
		$output['menu_aktif'] = 'm_pantauan_pkl';
		$output['parent_magang'] = 'active open';
		$output['content'] = 'pantauan_pkl/pantauan_pkl_view';
		$output['after_page'] = 'pantauan_pkl/pantauan_pkl_after_page';
		
		$id_smt = $this->session->userdata('tahun_ajaran');
		$cache_key = 'kaprodi_pkl_v4_' . $id_sms . '_' . $id_smt;
		
		if (!$students_data = $this->cache->get($cache_key)) {
			// Query seluruh mahasiswa PKL di prodi bersangkutan
			$this->db->select("
				lm.nipd, 
				m.nm_pd, 
				m.foto, 
				lm.nama_perusahaan, 
				d.nama_lengkap as nm_dpl, 
				lm.tanggal_mulai, 
				lm.tanggal_selesai, 
				COUNT(tl.id_tracking) as total_filled, 
				SUM(CASE WHEN tl.review_dpl IS NOT NULL AND tl.review_dpl != '' THEN 1 ELSE 0 END) as total_reviewed
			");
			$this->db->from('simlapkerma_lokasi_magang lm');
			$this->db->join('mahasiswa_pt mp', 'mp.nipd = lm.nipd');
			$this->db->join('mahasiswa m', 'm.id_pd = mp.id_pd');
			$this->db->join('siakad_skripsi_dosen d', 'd.id_ptk = lm.dosen_pembimbing_lapangan', 'left');
			$this->db->join('simlapkerma_tracking_kegiatan tl', 'tl.nipd = lm.nipd', 'left');
			$this->db->where('mp.id_sms', $id_sms);
			
			// FILTER: Hanya ambil mahasiswa yang:
			// 1. Punya mata kuliah KRS dengan id_kategori_jadwal = 2 (PKL/Internship)
			// 2. Bukan mata kuliah KKN
			// 3. Belum dinilai (angka & huruf NULL)
			$this->db->where("EXISTS (
				SELECT 1 FROM siakad_nilai_baru nb
				JOIN feeder_kelas_kuliah kk ON kk.id_kls = nb.id_kls
				JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk
				JOIN siakad_jadwal_detail jd ON jd.id_kls = kk.id_kls
				JOIN siakad_jadwal j ON j.id = jd.id_jadwal
				WHERE nb.nipd = lm.nipd
				  AND kk.id_smt = $id_smt
				  AND j.id_kategori_jadwal = 2
				  AND LOWER(mk.nm_mk) NOT LIKE '%kuliah kerja nyata%' 
				  AND LOWER(mk.nm_mk) NOT LIKE '%kkn%'
				  AND (nb.nilai_angka IS NULL OR nb.nilai_angka = 0)
				  AND (nb.nilai_huruf IS NULL OR nb.nilai_huruf = '')
			)", NULL, FALSE);
			
			$this->db->group_by('lm.nipd');
			$this->db->order_by('m.nm_pd', 'ASC');
			
			$students = $this->db->get()->result();
			
			$total_pkl = count($students);
			$baru_mulai = 0;
			$segera_berakhir = 0;
			$sudah_dpl = 0;
			$belum_dpl = 0;
			$now = time();
			
			// Kalkulasi progres dan statistik
			foreach ($students as &$s) {
				$progress_pengisian = 0;
				$progress_review = 0;
				$total_weeks_overall = null;
				
				if (!empty($s->tanggal_mulai) && !empty($s->tanggal_selesai)) {
					$start = strtotime($s->tanggal_mulai);
					$end = strtotime($s->tanggal_selesai);
					$total_days = floor(($end - $start) / 86400) + 1;
					$total_weeks_overall = ceil($total_days / 7);
					
					if ($total_weeks_overall > 0) {
						$progress_pengisian = ($s->total_filled / $total_weeks_overall) * 100;
						if ($progress_pengisian > 100) $progress_pengisian = 100;
					}
					
					// Hitung Baru Mulai (dalam 14 hari terakhir)
					if ($start <= $now && ($now - $start) <= (14 * 86400)) {
						$baru_mulai++;
					}
					
					// Hitung Segera Berakhir (dalam 30 hari kedepan)
					if ($end >= $now && ($end - $now) <= (30 * 86400)) {
						$segera_berakhir++;
					}
					
				} else {
					$progress_pengisian = 'N/A'; // Kasus tanggal kosong
				}
				
				if ($s->total_filled > 0) {
					$progress_review = ($s->total_reviewed / $s->total_filled) * 100;
				}
				
				if (!empty($s->nm_dpl)) {
					$sudah_dpl++;
				} else {
					$belum_dpl++;
				}
				
				$s->progress_pengisian = $progress_pengisian === 'N/A' ? 'N/A' : round($progress_pengisian);
				$s->progress_review = round($progress_review);
				$s->total_weeks = $total_weeks_overall;
			}
			
			$students_data = [
				'list' => $students,
				'stats' => [
					'total_pkl' => $total_pkl,
					'baru_mulai' => $baru_mulai,
					'segera_berakhir' => $segera_berakhir,
					'sudah_dpl' => $sudah_dpl,
					'belum_dpl' => $belum_dpl
				]
			];
			
			// Simpan ke Redis selama 3 menit (180 detik)
			$this->cache->save($cache_key, $students_data, 180);
		}
		
		$output['students'] = $students_data['list'];
		$output['stats'] = $students_data['stats'];

		$this->load->view('master_page', $output);
	}
}
