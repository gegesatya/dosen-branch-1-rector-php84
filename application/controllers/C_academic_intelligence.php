<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_academic_intelligence extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Academic_intelligence_model');
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
			$output['isi'] 				= 'Username Anda tidak terdaftar sebagai pengguna yang berhak mengakses Academic Intelligence.';
			$output['menu_aktif'] 		= 'm_academic_intelligence';
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
		$output['judul_besar'] 		= 'Academic Intelligence ';
		$output['judul_kecil'] 		= 'Politeknik WBI';	
		$output['menu_aktif'] 		= 'm_academic_intelligence';
		$output['parent_infografis']	= 'active open';
		
		// All users can choose any Program Study from the dropdown filter
		$selected_prodi = $this->input->get('prodi');
		$output['is_restricted'] = false;
		$output['selected_prodi'] = $selected_prodi;
		
		$cache_name = "academic_intel_" . ($selected_prodi ?: "all");
		$cached_output = $this->cache->get($cache_name);

		if ($cached_output !== FALSE && is_array($cached_output)) {
			$output = array_merge($output, $cached_output);
		} else {
			$cached_output = [];
			// Fetch prodis list for dropdown filter
			$cached_output['list_prodi'] = $this->db->select('id_sms, nm_lemb')->get('sms')->result();
			
			$prodi_label = "WBI";
			if (!empty($selected_prodi)) {
				$prodi_row = $this->db->where('id_sms', $selected_prodi)->get('sms')->row();
				if ($prodi_row) {
					$prodi_label = $prodi_row->nm_lemb;
				}
			}
			$cached_output['prodi_label'] = $prodi_label;
			
			// Fetch Real-time Analytics from database filtered by selected prodi
			$cached_output['stats'] = $this->Academic_intelligence_model->get_stats($selected_prodi);
			$cached_output['high_risk_courses'] = $this->Academic_intelligence_model->get_high_risk_courses(15, $selected_prodi);
			$cached_output['cohort_analysis'] = $this->Academic_intelligence_model->get_cohort_analysis($selected_prodi);
			$cached_output['trend_per_prodi'] = $this->Academic_intelligence_model->get_trend_per_prodi($selected_prodi);
			$cached_output['health_distribution'] = $this->Academic_intelligence_model->get_academic_health_distribution($selected_prodi);

			
			// Generate Dynamic AI Narrative
			$cached_output['ai_insights'] = $this->generate_ai_insights($cached_output['stats'], $cached_output['high_risk_courses'], $selected_prodi);

			$this->cache->save($cache_name, $cached_output, 7200); // 2 jam
			$output = array_merge($output, $cached_output);
		}

		$output['content'] 			= 'academic_intelligence/dashboard_view';
		$output['after_page'] 		= 'academic_intelligence/dashboard_after_page';
		
		$this->load->view('master_page', $output);
	}

	private function generate_ai_insights($stats, $high_risk_courses, $selected_prodi)
	{
		$insights = [];

		// Determine the label name of the active scope
		$prodi_name = "seluruh Program Studi di WBI";
		if (!empty($selected_prodi)) {
			$prodi_row = $this->db->where('id_sms', $selected_prodi)->get('sms')->row();
			if ($prodi_row) {
				$prodi_name = "Program Studi <strong>" . $prodi_row->nm_lemb . "</strong>";
			}
		}

		// Insight 1: Performa Akademik
		$insights[] = (object)[
			'badge' => 'Academic Insight',
			'text' => "Rata-rata IPK untuk {$prodi_name} saat ini berada di angka <strong>{$stats->avg_ipk}</strong>. Mahasiswa terdeteksi mengalami penyesuaian beban belajar yang optimal pada semester berjalan.",
			'class' => 'primary'
		];

		// Insight 2: Mata Kuliah Kegagalan Tinggi
		if (!empty($high_risk_courses)) {
			$worst_mk = $high_risk_courses[0];
			$insights[] = (object)[
				'badge' => 'Course Analytics',
				'text' => "Pada {$prodi_name}, mata kuliah <strong>{$worst_mk->nm_mk}</strong> terdeteksi memiliki tingkat ketidaklulusan tertinggi yaitu sebesar <strong>{$worst_mk->failure_rate}%</strong> (sebanyak {$worst_mk->total_gagal} dari {$worst_mk->total_peserta} mahasiswa gagal). Disarankan peninjauan khusus oleh tim pengajar.",
				'class' => 'warning'
			];
		} else {
			$insights[] = (object)[
				'badge' => 'Course Analytics',
				'text' => "Seluruh mata kuliah pada {$prodi_name} memiliki tingkat kelulusan yang baik. Tidak ada mata kuliah reguler dengan tingkat ketidaklulusan kritis (>30%).",
				'class' => 'success'
			];
		}

		// Insight 3: Early Warning Alert
		$insights[] = (object)[
			'badge' => 'Early Warning',
			'text' => "Teridentifikasi sebanyak <strong>{$stats->total_risk} mahasiswa</strong> aktif pada {$prodi_name} dengan status <em>High Risk</em> (IPS di bawah 2.0). Dosen Pembimbing Akademik disarankan segera menjadwalkan konseling terarah.",
			'class' => 'danger'
		];

		return $insights;
	}

	public function print_cohort_students()
	{
		$selected_prodi = $this->input->get('prodi');
		
		$prodi_label = "WBI";
		if (!empty($selected_prodi)) {
			$prodi_row = $this->db->where('id_sms', $selected_prodi)->get('sms')->row();
			if ($prodi_row) {
				$prodi_label = $prodi_row->nm_lemb;
			}
		}

		$data['prodi_label'] = $prodi_label;
		$data['students'] = $this->Academic_intelligence_model->get_cohort_students_data($selected_prodi);
		
		$this->load->view('academic_intelligence/print_cohort_students_view', $data);
	}
}
