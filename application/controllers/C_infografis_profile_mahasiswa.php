<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_infografis_profile_mahasiswa extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Infografis_profile_mahasiswa_model');
		date_default_timezone_set("Asia/Jakarta");

		$this->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}
		$username = $this->ion_auth->user()->row()->email;
		$data_akses = $this->db->where('username', $username)->get('siakad_akses_data_center');

		if ($data_akses->num_rows() == 0 && !$this->ion_auth->in_group([5])) {
			$this->output->set_status_header(403);
			
			$output['judul_besar'] 		= 'Akses Ditolak';
			$output['judul_kecil'] 		= '';
			$output['isi'] 				= 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses Infografis Profile Mahasiswa.';
			$output['menu_aktif'] 		= 'm_infografis_profil_mahasiswa';
			$output['parent_infografis'] = 'active open';
			$output['content'] 			= 'forbidden_page/forbidden_page_view';
			$output['after_page'] 		= 'forbidden_page/forbidden_page_after_page';
			
			$this->load->view('master_page', $output);
			echo $this->output->get_output();
			exit;
		}

	}

	public function index()
	{
		$output['judul_besar'] 		= 'Infografis Profile Mahasiswa ';
		$output['judul_kecil'] 		= 'Politeknik WBI';
		$output['menu_aktif'] 		= 'm_infografis_profil_mahasiswa';
		$output['parent_infografis']	= 'active open';
		$output['content'] 			= 'infografis_profile_mahasiswa/infografis_profile_mahasiswa_view';
		$output['after_page'] 		= 'infografis_profile_mahasiswa/infografis_profile_mahasiswa_after_page';

		$this->load->view('master_page', $output);
	}

	public function generate()
	{
		$cache_name = "info_profil_generate";
		$html = $this->cache->get($cache_name);

		if ($html === FALSE) {
			$this->load->model('Infografis_profile_mahasiswa_model', 'infografis_model');

			$mhs_stats = $this->infografis_model->get_global_mhs_stats();
			$pt_stats = $this->infografis_model->get_global_pt_stats();
			$alumni_stats = $this->infografis_model->get_alumni_stats();
			$ipk_stats = $this->infografis_model->get_ipk_semester_stats();

			$data['mhs_total'] = $pt_stats->mhs_total;
			$data['mhs_total_2'] = $mhs_stats->total_mhs;
			$data['mhs_aktif'] = $pt_stats->mhs_total - $alumni_stats->mhs_do_lulus;
			$data['mhs_total_laki'] = $mhs_stats->total_laki;
			$data['mhs_total_perempuan'] = $mhs_stats->total_perempuan;
			$data['mhs_baru'] = $pt_stats->mhs_baru;

			$data['mhs_kip'] = $this->db->get('mahasiswa_kip')->num_rows();

			$data['rerata_ipk_ganjil'] = round($ipk_stats->rerata_ipk_ganjil ?? 0, 2);
			$data['rerata_ipk_genap'] = round($ipk_stats->rerata_ipk_genap ?? 0, 2);
			$data['ipk_lulus'] = round($alumni_stats->ipk_lulus ?? 0, 2);
			$data['ipk_max'] = round($alumni_stats->ipk_max ?? 0, 2);
			$data['ipk_min'] = round($alumni_stats->ipk_min ?? 0, 2);
			$data['jumlah_lulusan'] = $alumni_stats->jumlah_lulusan;

			$prodi_demographics = $this->infografis_model->get_prodi_demographics();
			$prodi_alumni = $this->infografis_model->get_prodi_alumni();

			// Mapping Prodi
			$prodis = [
				'_mpi' => MPI,
				'_ap' => AP,
				'_ah' => AH,
				'_pka' => PKA,
				'_trpl' => TRPL
			];

			foreach ($prodis as $key => $id_sms) {
				$demo = $prodi_demographics[$id_sms] ?? (object)['total'=>0, 'laki'=>0, 'perempuan'=>0];
				$data[$key] = $demo->total;
				$data[$key.'_laki'] = $demo->laki;
				$data[$key.'_perempuan'] = $demo->perempuan;
				$data[$key.'_alumni'] = $prodi_alumni[$id_sms] ?? 0;
			}

			$data['pekerjaan_ayah'] = $this->infografis_model->get_pekerjaan_ayah_stats();
			$data['pekerjaan_ibu'] = $this->infografis_model->get_pekerjaan_ibu_stats();
			$data['orang_tua_meninggal'] = $this->infografis_model->get_status_orang_tua_meninggal_stats();

			$data['penghasilan_ayah'] = $this->infografis_model->get_penghasilan_ayah_stats();
			$data['penghasilan_ibu'] = $this->infografis_model->get_penghasilan_ibu_stats();

			$data['mhs_angkatan_prodi'] = $this->infografis_model->get_mhs_angkatan_prodi_stats();

			$html = $this->load->view('cetak/infografis_profile_mahasiswa', $data, TRUE);
			$this->cache->save($cache_name, $html, 7200); // 2 Jam
		}

		echo $html;
	}

	public function get_chart_data()
	{
		$cache_name = "info_profil_chart_data";
		$json = $this->cache->get($cache_name);
		if ($json === FALSE) {
			$data = [
				'labels' => ['2018', '2019','2020', '2021', '2022', '2023', '2024', '2025'],
				'values' => [
					$this->get_jumlah_mahasiswa(2018),
					$this->get_jumlah_mahasiswa(2019),
					$this->get_jumlah_mahasiswa(2020),
					$this->get_jumlah_mahasiswa(2021),
					$this->get_jumlah_mahasiswa(2022),
					$this->get_jumlah_mahasiswa(2023),
					$this->get_jumlah_mahasiswa(2024),
					$this->get_jumlah_mahasiswa(2025)
				]
			];
			$json = json_encode($data);
			$this->cache->save($cache_name, $json, 7200);
		}

		echo $json;
	}

	public function get_chart_data_aktif()
	{
		$cache_name = "info_profil_chart_aktif";
		$json = $this->cache->get($cache_name);
		if ($json === FALSE) {
			$data = [
				'labels' => ['2018', '2019','2020', '2021', '2022', '2023', '2024', '2025'],
				'values' => [
					$this->get_jumlah_mahasiswa_aktif(2018),
					$this->get_jumlah_mahasiswa_aktif(2019),
					$this->get_jumlah_mahasiswa_aktif(2020),
					$this->get_jumlah_mahasiswa_aktif(2021),
					$this->get_jumlah_mahasiswa_aktif(2022),
					$this->get_jumlah_mahasiswa_aktif(2023),
					$this->get_jumlah_mahasiswa_aktif(2024),
					$this->get_jumlah_mahasiswa_aktif(2025)
				]
			];
			$json = json_encode($data);
			$this->cache->save($cache_name, $json, 7200);
		}

		echo $json;
	}

	public function get_chart_data_by_prodi()
	{
		$cache_name = "info_profil_chart_prodi";
		$json = $this->cache->get($cache_name);
		if ($json === FALSE) {
			$data = [
				'labels' => ['MPI', 'AP', 'AH', 'PKA', 'TRPL'],
				'values' => [
					$this->get_jumlah_mahasiswa('', MPI),
					$this->get_jumlah_mahasiswa('', AP),
					$this->get_jumlah_mahasiswa('', AH),
					$this->get_jumlah_mahasiswa('', PKA),
					$this->get_jumlah_mahasiswa('', TRPL)
				]
			];
			$json = json_encode($data);
			$this->cache->save($cache_name, $json, 7200);
		}

		echo $json;
	}

	public function get_chart_asal_mahasiswa()
	{
		$cache_name = "info_profil_chart_asal";
		$json = $this->cache->get($cache_name);
		if ($json === FALSE) {
			$query = $this->db->select('count(nm_wil) as hasil, nm_wil')->from('mahasiswa m')->join('wilayah w', 'm.id_kabupaten = w.id_wil')
				->group_by('nm_wil')->order_by('hasil', 'DESC')->limit(10)->get();

			$labels = [];
			$values = [];

			foreach ($query->result() as $row) {
				$labels[] = substr($row->nm_wil, 4);
				$values[] = (int)$row->hasil;
			}

			$json = json_encode([
				'labels' => $labels,
				'values' => $values
			]);
			$this->cache->save($cache_name, $json, 7200);
		}

		ob_clean();
		header('Content-Type: application/json');
		echo $json;
	}

	public function get_data_by_kota_per_year($id_wil = 076000)
	{
		$cache_name = "info_profil_kota_year_{$id_wil}";
		$json = $this->cache->get($cache_name);

		if ($json === FALSE) {
			$ids_wil = ['076000', '070100', '123'];
			$years = [2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025];
			$all_data = [];

			foreach ($ids_wil as $id) {
				if ($id ==  '123') {
					$nama_wil = 'Kab/Kota Lainnya';

					$this->db->select("YEAR(tgl_masuk_sp) as tahun, COUNT(*) as hasil");
					$this->db->from("mahasiswa m");
					$this->db->join("mahasiswa_pt mp", "mp.id_pd = m.id_pd", "inner");
					$this->db->join("wilayah w", "m.id_kabupaten = w.id_wil");
					$this->db->where_not_in("w.id_wil", ['076000', '070100']);
					$this->db->where("YEAR(tgl_masuk_sp) >=", 2015);
					$this->db->group_by("YEAR(tgl_masuk_sp)");
					$query = $this->db->get()->result_array();

					// Map hasil query ke tahun yang sesuai agar tidak berantakan
					$data_per_tahun = [];
					foreach ($years as $y) {
						$found = array_search($y, array_column($query, 'tahun'));
						$data_per_tahun[] = ($found !== false) ? (int)$query[$found]['hasil'] : 0;
					}
				} else {
					$nama_wil = $this->db->get_where('wilayah', ['id_wil' => $id])->row()->nm_wil;

					$this->db->select("YEAR(tgl_masuk_sp) as tahun, COUNT(*) as hasil");
					$this->db->from("mahasiswa m");
					$this->db->join("mahasiswa_pt mp", "mp.id_pd = m.id_pd", "inner");
					$this->db->join("wilayah w", "m.id_kabupaten = w.id_wil");
					$this->db->where("w.id_wil", $id);
					$this->db->where("YEAR(tgl_masuk_sp) >=", 2015);
					$this->db->group_by("YEAR(tgl_masuk_sp)");
					$query = $this->db->get()->result_array();

					// Map hasil query ke tahun yang sesuai agar tidak berantakan
					$data_per_tahun = [];
					foreach ($years as $y) {
						$found = array_search($y, array_column($query, 'tahun'));
						$data_per_tahun[] = ($found !== false) ? (int)$query[$found]['hasil'] : 0;
					}
				}

				$all_data[] = [
					'label' => $nama_wil,
					'data' => $data_per_tahun
				];
			}

			$json = json_encode([
				'labels' => $years,
				'datasets' => $all_data
			]);
			$this->cache->save($cache_name, $json, 7200);
		}

		ob_clean();

		return $this->output
			->set_content_type('application/json')
			->set_output($json);
	}

	private function get_jumlah_mahasiswa($tahun = '', $prodi = '')
	{
		if ($prodi == '') {
			$jumlah  = $this->db->where('year(tgl_masuk_sp)', $tahun)->get('mahasiswa_pt')->num_rows();
		} else {
			$jumlah  = $this->db->where(['id_sms' => $prodi])->get('mahasiswa_pt')->num_rows();
		}
		return $jumlah;
	}

	private function get_jumlah_mahasiswa_aktif($tahun = '', $prodi = '')
	{
		if ($prodi == '') {
			$jumlah  = $this->db->where(['year(tgl_masuk_sp)'=> $tahun])
								->where_not_in('nipd','SELECT nim FROM feeder_lulus_do', FALSE)
								->get('mahasiswa_pt')->num_rows();
		} else {
			$jumlah  = $this->db->where(['id_sms' => $prodi])->get('mahasiswa_pt')->num_rows();
		}
		return $jumlah;
	}

	public function print_alumni()
	{
		$data['alumni_data'] = $this->Infografis_profile_mahasiswa_model->get_alumni_data();
		$this->load->view('infografis_profile_mahasiswa/print_alumni_view', $data);
	}

	public function print_asal_mahasiswa()
	{
		$data['asal_mahasiswa'] = $this->Infografis_profile_mahasiswa_model->get_all_asal_mahasiswa();
		$this->load->view('infografis_profile_mahasiswa/print_asal_mahasiswa_view', $data);
	}

	public function print_asal_mahasiswa_tahun()
	{
		$data['asal_mahasiswa_tahun'] = $this->Infografis_profile_mahasiswa_model->get_asal_mahasiswa_per_tahun();
		$this->load->view('infografis_profile_mahasiswa/print_asal_mahasiswa_tahun_view', $data);
	}

	// ── AJAX: Top 10 Kabupaten/Kota asal sekolah mahasiswa ───────────────────
	public function ajax_get_top_kabupaten()
	{
		$this->output->set_content_type('application/json');

		$cache_key = 'profil_top_kabupaten_30';
		if ($cached = $this->cache->get($cache_key)) {
			echo $cached;
			return;
		}

		$sql = "SELECT d.kabupaten_kota, COUNT(DISTINCT m.id_pd) as total_mahasiswa
			FROM mahasiswa m
			JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
			WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
			  AND d.kabupaten_kota IS NOT NULL AND d.kabupaten_kota != ''
			GROUP BY d.kabupaten_kota
			ORDER BY total_mahasiswa DESC
			LIMIT 30";

		$result = $this->db->query($sql);
		if ($result === false) {
			echo json_encode(['status' => 'error', 'message' => 'Query gagal']);
			return;
		}

		$output = json_encode(['status' => 'success', 'data' => $result->result()]);
		$this->cache->save($cache_key, $output, 7200);
		echo $output;
	}

	// ── AJAX: Daftar sekolah berdasarkan kabupaten/kota ──────────────────────
	public function ajax_get_sekolah_by_kabupaten()
	{
		$this->output->set_content_type('application/json');

		$kabupaten = $this->input->get('kabupaten');
		if (empty($kabupaten)) {
			echo json_encode(['status' => 'error', 'message' => 'Kabupaten diperlukan']);
			return;
		}

		$k = $this->db->escape($kabupaten);
		$cache_key = 'profil_sekolah_kab_' . md5($kabupaten);
		if ($cached = $this->cache->get($cache_key)) {
			echo $cached;
			return;
		}

		$sql = "SELECT d.sekolah, d.bentuk, COUNT(DISTINCT m.id_pd) as total_mahasiswa
			FROM mahasiswa m
			JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
			WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
			  AND d.kabupaten_kota = {$k}
			GROUP BY d.sekolah, d.bentuk
			ORDER BY total_mahasiswa DESC";

		$result = $this->db->query($sql);
		if ($result === false) {
			echo json_encode(['status' => 'error', 'message' => 'Query gagal']);
			return;
		}

		$output = json_encode(['status' => 'success', 'data' => $result->result()]);
		$this->cache->save($cache_key, $output, 7200);
		echo $output;
	}

	// ── AJAX: Sekolah dari Kab/Kota di luar top 30 ───────────────────────────
	public function ajax_get_sekolah_lainnya()
	{
		$this->output->set_content_type('application/json');

		$cache_key = 'profil_sekolah_lainnya';
		if ($cached = $this->cache->get($cache_key)) {
			echo $cached;
			return;
		}

		$sql = "SELECT d.sekolah, d.bentuk, d.kabupaten_kota,
				COUNT(DISTINCT m.id_pd) as total_mahasiswa
			FROM mahasiswa m
			JOIN dapo_sekolah d ON d.npsn = m.npsn_sekolah_asal
			WHERE m.npsn_sekolah_asal IS NOT NULL AND m.npsn_sekolah_asal != ''
			  AND d.kabupaten_kota IS NOT NULL AND d.kabupaten_kota != ''
			  AND d.kabupaten_kota NOT IN (
			    SELECT kabupaten_kota FROM (
			        SELECT d2.kabupaten_kota
			        FROM mahasiswa m2
			        JOIN dapo_sekolah d2 ON d2.npsn = m2.npsn_sekolah_asal
			        WHERE m2.npsn_sekolah_asal IS NOT NULL AND m2.npsn_sekolah_asal != ''
			          AND d2.kabupaten_kota IS NOT NULL AND d2.kabupaten_kota != ''
			        GROUP BY d2.kabupaten_kota
			        ORDER BY COUNT(DISTINCT m2.id_pd) DESC
			        LIMIT 30
			    ) AS top30
			  )
			GROUP BY d.sekolah, d.bentuk, d.kabupaten_kota
			ORDER BY total_mahasiswa DESC";

		$result = $this->db->query($sql);
		if ($result === false) {
			echo json_encode(['status' => 'error', 'message' => 'Query gagal']);
			return;
		}

		$output = json_encode(['status' => 'success', 'data' => $result->result()]);
		$this->cache->save($cache_key, $output, 7200);
		echo $output;
	}
}
