<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_skema_nilai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Skema_nilai_model', 'Skema_nilai_model');
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

		$output['judul_besar'] = 'Skema Nilai';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_skema_nilai';
		$output['parent_2'] = 'active open';
		$output['content'] = 'skema_nilai/skema_nilai_view';
		$output['after_page'] = 'skema_nilai/skema_nilai_after_page';
		$output['dd_kelas_kuliah'] = $this->Skema_nilai_model->f_dropdown('feeder_kelas_kuliah', 'id_kls', 'nm_kls');
		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Skema_nilai_model->get_datatables();
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
			"recordsTotal" => $this->Skema_nilai_model->count_all(),
			"recordsFiltered" => $this->Skema_nilai_model->count_filtered(),
			"data" => $data,
		];
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Skema_nilai_model->get_by_id($id);
		if (!$data) {
			echo json_encode(['status' => false, 'msg' => 'Data jadwal tidak ditemukan']);
			return;
		}

		// Validasi: Pastikan jadwal sudah memiliki rencana pertemuan / RPS aktif
		$total_rencana = $this->db->where('id_jadwal', $id)->count_all_results('siakad_rencana_pertemuan');
		if ($total_rencana == 0) {
			echo json_encode([
				'status'   => false,
				'need_rps' => true,
				'msg'      => 'Jadwal perkuliahan ini belum memiliki rencana pertemuan (RPS). Silakan buat atau terapkan RPS OBE terlebih dahulu di Studio RPS.'
			]);
			return;
		}

		$data->jam_mulai = substr($data->jam_mulai, 0, 5);
		$data->jam_selesai = substr($data->jam_selesai, 0, 5);

		// Cari informasi mata kuliah & RPS OBE terkait
		$detail = $this->db
			->select('kk.id_mk, kk.nm_kls, mk.kode_mk, mk.nm_mk, mk.sks_mk, s.nm_lemb as nm_prodi')
			->from('siakad_jadwal_detail jd')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = jd.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('sms s', 's.id_sms = kk.id_sms', 'left')
			->where('jd.id_jadwal', $id)
			->get()->row();

		$data->kode_mk  = $detail->kode_mk ?? '';
		$data->nm_mk    = $detail->nm_mk ?? '';
		$data->nm_kls   = $detail->nm_kls ?? '';
		$data->nm_prodi = $detail->nm_prodi ?? '';
		$data->sks_mk   = $detail->sks_mk ?? 3;

		// Cari Master RPS OBE
		$rps_obe = null;
		$id_ptk = $this->session->userdata('id_ptk');
		if (!empty($data->kode_mk)) {
			if (!empty($id_ptk)) {
				$rps_obe = $this->db->where('kode_mk', $data->kode_mk)->where('id_ptk', $id_ptk)->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
			}
			if (!$rps_obe) {
				$rps_obe = $this->db->where('kode_mk', $data->kode_mk)->order_by('id_rps', 'DESC')->get('siakad_rps_obe')->row();
			}
		}

		$parsed_blueprint = null;
		if ($rps_obe) {
			$cand_texts = [
				$rps_obe->kontrak_kriteria_penilaian ?? '',
				$rps_obe->kontrak_kuliah_markdown ?? '',
				$rps_obe->tujuan_belajar ?? '',
				$rps_obe->raw_ai_output ?? ''
			];
			foreach ($cand_texts as $txt) {
				if (!empty($txt)) {
					$parsed_blueprint = $this->_parse_obe_blueprint_table($txt);
					if ($parsed_blueprint && !empty($parsed_blueprint['cards'])) {
						break;
					}
				}
			}
		}

		// Fallback jika belum ada tabel RPS OBE tersimpan
		if (!$parsed_blueprint || empty($parsed_blueprint['cards'])) {
			$parsed_blueprint = $this->_get_default_obe_blueprint($data->nm_mk, $data->sks_mk);
		}

		$data->obe_blueprint = $parsed_blueprint;

		echo json_encode($data);
	}

	private function _parse_obe_blueprint_table($markdown_text)
	{
		if (empty($markdown_text)) return null;

		if (!preg_match('/(?:Matriks\s+Distribusi\s+Bobot\s+Asesmen|Blueprint\s+Asesmen)[\s\S]*?\n(\|[\s\S]*?\|\s*\n\s*(?:\|?\s*[\*\*_]*Total[\s\S]*?\n|\Z))/i', $markdown_text, $match)) {
			if (!preg_match('/(\|(?:\s*CPMK|\s*Sub-CPMK|\s*Bahan Kajian)[\s\S]*?\n\|[\s\-:|]+\n[\s\S]*?\|\s*\n\s*(?:\|?\s*[\*\*_]*Total[\s\S]*?\n|\Z))/i', $markdown_text, $match)) {
				return null;
			}
		}

		$raw_table = $match[1];
		$lines = array_values(array_filter(array_map('trim', explode("\n", $raw_table)), fn($l) => !empty($l)));
		if (count($lines) < 3) return null;

		$parse_line = function($line) {
			$cells = explode('|', $line);
			if (str_starts_with($line, '|')) array_shift($cells);
			if (str_ends_with($line, '|')) array_pop($cells);
			return array_map('trim', $cells);
		};

		$header_cells = $parse_line($lines[0]);

		// Map column names to SIAKAD keys
		$map_header_to_key = function($header) {
			$h = strtolower(trim(strip_tags(str_replace(['*', '_'], '', $header))));
			if (str_contains($h, 'tugas')) return ['key' => 'tugas_terstruktur', 'label' => 'Tugas Terstruktur', 'icon' => 'fa-tasks', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'];
			if (str_contains($h, 'kuis') || str_contains($h, 'quiz')) return ['key' => 'quiz', 'label' => 'Quiz', 'icon' => 'fa-question-circle', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'];
			if (str_contains($h, 'kasus') || str_contains($h, 'proyek') || str_contains($h, 'pjbl')) return ['key' => 'proyek', 'label' => 'Proyek / Kasus', 'icon' => 'fa-cubes', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'];
			if (str_contains($h, 'ets') || str_contains($h, 'uts')) return ['key' => 'uts', 'label' => 'UTS', 'icon' => 'fa-file-text-o', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'];
			if (str_contains($h, 'eas') || str_contains($h, 'uas')) return ['key' => 'uas', 'label' => 'UAS', 'icon' => 'fa-trophy', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca'];
			if (str_contains($h, 'sikap') || str_contains($h, 'partisipasi')) return ['key' => 'sikap', 'label' => 'Sikap / Partisipatif', 'icon' => 'fa-user-check', 'color' => '#0891b2', 'bg' => '#ecfeff', 'border' => '#a5f3fc'];
			if (str_contains($h, 'kinerja')) return ['key' => 'unjuk_kinerja', 'label' => 'Unjuk Kinerja', 'icon' => 'fa-bolt', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'];
			if (str_contains($h, 'portofolio')) return ['key' => 'portofolio', 'label' => 'Portofolio', 'icon' => 'fa-folder-open', 'color' => '#475569', 'bg' => '#f8fafc', 'border' => '#cbd5e1'];
			if (str_contains($h, 'produk')) return ['key' => 'produk', 'label' => 'Produk', 'icon' => 'fa-gift', 'color' => '#b45309', 'bg' => '#fef3c7', 'border' => '#fde68a'];
			if (str_contains($h, 'presentasi')) return ['key' => 'presentasi', 'label' => 'Presentasi', 'icon' => 'fa-slideshare', 'color' => '#4338ca', 'bg' => '#eef2ff', 'border' => '#c7d2fe'];
			return null;
		};

		$col_mappings = [];
		foreach ($header_cells as $idx => $hc) {
			$mapped = $map_header_to_key($hc);
			if ($mapped) {
				$col_mappings[$idx] = $mapped;
				$col_mappings[$idx]['original_header'] = $hc;
			}
		}

		$rows = [];
		$total_cells = [];
		for ($i = 2; $i < count($lines); $i++) {
			$cells = $parse_line($lines[$i]);
			if (empty($cells)) continue;
			
			$is_total = (stripos(implode(' ', $cells), 'total') !== false);
			if ($is_total) {
				$total_cells = $cells;
			} else {
				$rows[] = $cells;
			}
		}

		$auto_map_values = [
			'sikap' => 0,
			'quiz' => 0,
			'tugas_terstruktur' => 0,
			'uts' => 0,
			'uas' => 0,
			'unjuk_kinerja' => 0,
			'proyek' => 0,
			'portofolio' => 0,
			'produk' => 0,
			'presentasi' => 0,
		];

		$comp_cards = [];
		foreach ($col_mappings as $idx => $m) {
			$total_weight = 0;
			$sub_breakdowns = [];

			if (!empty($total_cells) && isset($total_cells[$idx])) {
				$val_str = preg_replace('/[^0-9.]/', '', $total_cells[$idx]);
				if (is_numeric($val_str)) {
					$total_weight = (float)$val_str;
				}
			}

			foreach ($rows as $r_idx => $r_cells) {
				if (isset($r_cells[$idx])) {
					$cell_val = trim(str_replace(['*', '_'], '', $r_cells[$idx]));
					$cell_num = (float)preg_replace('/[^0-9.]/', '', $cell_val);
					if ($cell_num > 0) {
						$sub_cpmk_name = $r_cells[1] ?? ($r_cells[0] ?? "Baris " . ($r_idx+1));
						$sub_breakdowns[] = "{$sub_cpmk_name} ({$cell_val})";
					}
				}
			}

			if ($total_weight == 0) {
				foreach ($rows as $r_cells) {
					if (isset($r_cells[$idx])) {
						$num = (float)preg_replace('/[^0-9.]/', '', $r_cells[$idx]);
						$total_weight += $num;
					}
				}
			}

			$auto_map_values[$m['key']] = round($total_weight);

			$comp_cards[] = [
				'id'             => 'obe_comp_' . $m['key'],
				'title'          => $m['original_header'],
				'weight'         => round($total_weight),
				'target_key'     => $m['key'],
				'target_label'   => $m['label'],
				'breakdown'      => !empty($sub_breakdowns) ? implode(', ', $sub_breakdowns) : '',
				'icon'           => $m['icon'],
				'color'          => $m['color'],
				'bg'             => $m['bg'],
				'border'         => $m['border']
			];
		}

		// Build Table HTML
		$table_html = '<div style="overflow-x:auto; border-radius:8px; border:1px solid #cbd5e1; box-shadow:0 1px 3px rgba(0,0,0,0.05);">'
			. '<table class="table table-bordered table-condensed" style="margin-bottom:0; font-size:11.5px; background:#ffffff;">'
			. '<thead><tr style="background:#f1f5f9; color:#1e293b; font-weight:700;">';
		foreach ($header_cells as $h) {
			$table_html .= '<th style="text-align:center; vertical-align:middle; padding:6px 8px; border:1px solid #cbd5e1;">' . htmlspecialchars($h) . '</th>';
		}
		$table_html .= '</tr></thead><tbody>';

		foreach ($rows as $r) {
			$table_html .= '<tr>';
			foreach ($r as $c_idx => $c) {
				$align = ($c_idx >= 3) ? 'center' : 'left';
				$bg = isset($col_mappings[$c_idx]) ? 'style="text-align:' . $align . '; padding:5px 8px; border:1px solid #cbd5e1; background:#fafafa;"' : 'style="text-align:' . $align . '; padding:5px 8px; border:1px solid #cbd5e1;"';
				$table_html .= '<td ' . $bg . '>' . htmlspecialchars($c) . '</td>';
			}
			$table_html .= '</tr>';
		}

		if (!empty($total_cells)) {
			$table_html .= '<tr style="background:#ecfdf5; font-weight:700; color:#065f46;">';
			foreach ($total_cells as $c_idx => $c) {
				$align = ($c_idx >= 3) ? 'center' : 'left';
				$table_html .= '<td style="text-align:' . $align . '; padding:6px 8px; border:1px solid #a7f3d0;">' . htmlspecialchars($c) . '</td>';
			}
			$table_html .= '</tr>';
		}

		$table_html .= '</tbody></table></div>';

		return [
			'has_blueprint'    => true,
			'headers'          => $header_cells,
			'table_html'       => $table_html,
			'cards'            => $comp_cards,
			'auto_map_values'  => $auto_map_values,
			'total_sum'        => array_sum($auto_map_values)
		];
	}

	private function _get_default_obe_blueprint($nm_mk = '', $sks = 3)
	{
		$auto_map_values = [
			'sikap'             => 5,
			'quiz'              => 10,
			'tugas_terstruktur' => 20,
			'uts'               => 20,
			'uas'               => 20,
			'proyek'            => 25,
			'unjuk_kinerja'     => 0,
			'portofolio'        => 0,
			'produk'            => 0,
			'presentasi'        => 0,
		];

		$comp_cards = [
			[
				'id'           => 'obe_comp_proyek',
				'title'        => 'Kasus / Proyek',
				'weight'       => 25,
				'target_key'   => 'proyek',
				'target_label' => 'Proyek / Kasus',
				'breakdown'    => 'PjBL & Studi Kasus Industri WBI',
				'icon'         => 'fa-cubes',
				'color'        => '#059669',
				'bg'           => '#ecfdf5',
				'border'       => '#a7f3d0'
			],
			[
				'id'           => 'obe_comp_tugas',
				'title'        => 'Tugas Terstruktur',
				'weight'       => 20,
				'target_key'   => 'tugas_terstruktur',
				'target_label' => 'Tugas Terstruktur',
				'breakdown'    => 'Latihan Mandiri & Analisis Terstruktur',
				'icon'         => 'fa-tasks',
				'color'        => '#2563eb',
				'bg'           => '#eff6ff',
				'border'       => '#bfdbfe'
			],
			[
				'id'           => 'obe_comp_uts',
				'title'        => 'UTS',
				'weight'       => 20,
				'target_key'   => 'uts',
				'target_label' => 'UTS',
				'breakdown'    => 'Evaluasi Tengah Semester (Minggu 1-7)',
				'icon'         => 'fa-file-text-o',
				'color'        => '#7c3aed',
				'bg'           => '#f5f3ff',
				'border'       => '#ddd6fe'
			],
			[
				'id'           => 'obe_comp_uas',
				'title'        => 'UAS',
				'weight'       => 20,
				'target_key'   => 'uas',
				'target_label' => 'UAS',
				'breakdown'    => 'Evaluasi Akhir Semester (Komprehensif)',
				'icon'         => 'fa-trophy',
				'color'        => '#dc2626',
				'bg'           => '#fef2f2',
				'border'       => '#fecaca'
			],
			[
				'id'           => 'obe_comp_quiz',
				'title'        => 'Kuis',
				'weight'       => 10,
				'target_key'   => 'quiz',
				'target_label' => 'Quiz',
				'breakdown'    => 'Tes Formatif Berkala',
				'icon'         => 'fa-question-circle',
				'color'        => '#d97706',
				'bg'           => '#fffbeb',
				'border'       => '#fde68a'
			],
			[
				'id'           => 'obe_comp_sikap',
				'title'        => 'Sikap / Partisipasi',
				'weight'       => 5,
				'target_key'   => 'sikap',
				'target_label' => 'Sikap / Partisipatif',
				'breakdown'    => 'Keaktifan Diskusi & Etika Akademik',
				'icon'         => 'fa-user-check',
				'color'        => '#0891b2',
				'bg'           => '#ecfeff',
				'border'       => '#a5f3fc'
			],
		];

		return [
			'has_blueprint'   => false,
			'headers'         => [],
			'table_html'      => '',
			'cards'           => $comp_cards,
			'auto_map_values' => $auto_map_values,
			'total_sum'       => 100
		];
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

		$this->Skema_nilai_model->update(['id' => $this->input->post('id')], $data);
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
