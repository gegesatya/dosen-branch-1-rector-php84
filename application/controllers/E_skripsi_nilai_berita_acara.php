<?php
defined('BASEPATH') or exit('No direct script access allowed');

class E_skripsi_nilai_berita_acara extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('E_skripsi_nilai_berita_acara_model');
		$this->load->helper(['url', 'gg']);
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Halaman khusus Dosen.');
		}
	}

	public function index()
	{
		$output['judul_besar'] = 'Nilai dan Berita Acara';
		$output['judul_kecil'] = 'E-Tugas Akhir';
		$output['menu_aktif'] = 'm_nilai_berita_acara';
		$output['parent_skripsi'] = 'active open';

		$output['kpi'] = $this->E_skripsi_nilai_berita_acara_model->get_kpi_stats();

		$output['content'] = 'e_skripsi_nilai_berita_acara/e_skripsi_nilai_berita_acara_view';
		$output['after_page'] = 'e_skripsi_nilai_berita_acara/e_skripsi_nilai_berita_acara_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->E_skripsi_nilai_berita_acara_model->get_datatables();
		$data = [];
		$no   = isset($_POST['start']) ? (int)$_POST['start'] : 0;

		foreach ($list as $rg) {
			$no++;
			$row = [];
			$id_judul = (int)$rg->id_judul_skripsi;
			$hash_id  = md5($rg->id_judul_skripsi);

			// 1. No
			$row[] = '<div class="text-center font-weight-bold" style="color: #475569; font-size: 12px;">' . $no . '</div>';

			// 2. Mahasiswa & Judul TA (NIM, Nama, Status/Peran Dosen, Judul TA)
			$nama_html = '<div style="text-align: left;">';
			
			// Baris 1: NIM Badge & Nama Mahasiswa
			$nama_html .= '<div style="margin-bottom: 5px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">';
			$nama_html .= '<span class="label label-primary label-sm" style="font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600; letter-spacing: 0.5px;">' . htmlspecialchars($rg->nipd) . '</span>';
			$nama_html .= '<strong style="font-size: 13.5px; color: #1e293b;">' . htmlspecialchars($rg->nm_pd) . '</strong>';
			$nama_html .= '</div>';

			// Baris 2: Status / Peran Dosen
			if (!empty($rg->peran_dosen)) {
				$badge_bg = '#0284c7';
				if (stripos($rg->peran_dosen, 'Utama') !== false) {
					$badge_bg = '#0284c7'; // Blue
				} elseif (stripos($rg->peran_dosen, 'Pendamping') !== false) {
					$badge_bg = '#7c3aed'; // Purple
				} elseif (stripos($rg->peran_dosen, 'Sidang') !== false) {
					$badge_bg = '#dc2626'; // Red
				} elseif (stripos($rg->peran_dosen, 'Penguji') !== false) {
					$badge_bg = '#d97706'; // Amber
				}
				$nama_html .= '<div style="margin-bottom: 5px;">';
				$nama_html .= '<span style="background-color: ' . $badge_bg . '; color: #ffffff; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-user"></i> ' . htmlspecialchars($rg->peran_dosen) . '</span>';
				$nama_html .= '</div>';
			}

			// Baris 3: Judul TA
			if (!empty($rg->judul_ta)) {
				$judul_clean = htmlspecialchars(trim(strip_tags($rg->judul_ta)));
				$nama_html .= '<div style="font-size: 11.5px; color: #475569; line-height: 1.4; font-style: italic;" title="' . $judul_clean . '">';
				$nama_html .= '<i class="fa fa-book text-muted" style="margin-right: 4px;"></i> ' . (mb_strlen($judul_clean) > 120 ? mb_substr($judul_clean, 0, 120) . '...' : $judul_clean);
				$nama_html .= '</div>';
			}

			$nama_html .= '</div>';
			$row[] = $nama_html;

			// 4. SEMPRO (Status, Tanggal, Rata-rata Nilai, Button Download Berita Acara, Button Detail Nilai)
			$sempro_html = '<div class="cell-stage text-center">';
			$sempro_has_ba = false;
			$sempro_tgl = (!empty($rg->sempro_tanggal_seminar) && $rg->sempro_tanggal_seminar != '0000-00-00') ? date('d/m/Y', strtotime($rg->sempro_tanggal_seminar)) : '';

			// Status
			if (!empty($rg->sempro_hasil_p1)) {
				$sempro_has_ba = true;
				$status_txt = ucwords(strtolower(str_replace('_', ' ', $rg->sempro_hasil_p1)));
				$is_layak   = (stripos($status_txt, 'layak') !== false && stripos($status_txt, 'tidak') === false);
				$bg_color   = $is_layak ? '#10b981' : '#ef4444';
				$icon_cls   = $is_layak ? 'fa-check-circle' : 'fa-times-circle';
				$sempro_html .= '<span style="background-color: ' . $bg_color . '; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa ' . $icon_cls . '"></i> ' . $status_txt . '</span>';
			} elseif (!empty($rg->sempro_nilai_rerata) && $rg->sempro_nilai_rerata > 0) {
				$sempro_has_ba = true;
				$sempro_html .= '<span style="background-color: #f59e0b; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-clock-o"></i> Sudah Dinilai</span>';
			} elseif ($rg->sempro_status_undangan == 1) {
				$sempro_html .= '<span style="background-color: #3b82f6; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-calendar"></i> Terjadwal</span>';
			} else {
				$sempro_html .= '<span style="background-color: #e2e8f0; color: #475569; font-weight: 500; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-minus"></i> Belum Sempro</span>';
			}

			// Tanggal Sempro
			if (!empty($sempro_tgl)) {
				$sempro_html .= '<div style="font-size: 11px; color: #334155; font-weight: 600; margin-bottom: 3px;"><i class="fa fa-calendar text-primary" style="font-size: 10px;"></i> ' . $sempro_tgl . '</div>';
			} else {
				$sempro_html .= '<div style="font-size: 11px; color: #94a3b8; margin-bottom: 3px;"><i class="fa fa-calendar-o text-muted" style="font-size: 10px;"></i> -</div>';
			}

			// Rata-rata Nilai
			if (!empty($rg->sempro_nilai_rerata) && $rg->sempro_nilai_rerata > 0) {
				$nilai_format = number_format($rg->sempro_nilai_rerata, 2);
				$sempro_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #334155;">Rata-rata: <strong style="font-size: 13px; color: #0f766e; font-weight: 700;">' . $nilai_format . '</strong></div>';
			} else {
				$sempro_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #94a3b8;">Rata-rata: <span style="color: #cbd5e1;">-</span></div>';
			}

			// Buttons (Download Berita Acara & Detail Nilai)
			$sempro_html .= '<div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">';
			if ($sempro_has_ba) {
				$sempro_html .= '<a href="' . site_url('E_skripsi_nilai_sempro/cetak_pdf/' . $hash_id) . '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); width: 115px; justify-content: center;" title="Unduh Berita Acara SEMPRO">';
				$sempro_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</a>';

				$sempro_html .= '<button type="button" class="btn btn-xs btn-default" onclick="open_modal_detail(' . $id_judul . ', \'sempro\')" style="font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; border: 1px solid #cbd5e1; color: #1e293b; display: inline-flex; align-items: center; gap: 4px; width: 115px; justify-content: center;" title="Lihat Rincian Nilai & Catatan Sempro">';
				$sempro_html .= '<i class="fa fa-list-alt text-primary"></i> Rincian Nilai</button>';
			} else {
				$sempro_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Berita Acara belum tersedia">';
				$sempro_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</span>';

				$sempro_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 10.5px; font-weight: 500; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Rincian Nilai belum tersedia">';
				$sempro_html .= '<i class="fa fa-list-alt text-muted"></i> Rincian Nilai</span>';
			}
			$sempro_html .= '</div>';

			$sempro_html .= '</div>';
			$row[] = $sempro_html;

			// 5. SEMHAS (Status, Tanggal, Rata-rata Nilai, Button Download Berita Acara, Button Detail Nilai)
			$semhas_html = '<div class="cell-stage text-center">';
			$semhas_has_ba = false;
			$semhas_tgl = (!empty($rg->semhas_tanggal_seminar) && $rg->semhas_tanggal_seminar != '0000-00-00') ? date('d/m/Y', strtotime($rg->semhas_tanggal_seminar)) : '';

			// Status
			if (!empty($rg->semhas_hasil_p1)) {
				$semhas_has_ba = true;
				$status_txt = ucwords(strtolower(str_replace('_', ' ', $rg->semhas_hasil_p1)));
				$is_layak   = (stripos($status_txt, 'layak') !== false && stripos($status_txt, 'tidak') === false);
				$bg_color   = $is_layak ? '#10b981' : '#ef4444';
				$icon_cls   = $is_layak ? 'fa-check-circle' : 'fa-times-circle';
				$semhas_html .= '<span style="background-color: ' . $bg_color . '; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa ' . $icon_cls . '"></i> ' . $status_txt . '</span>';
			} elseif (!empty($rg->semhas_nilai_rerata) && $rg->semhas_nilai_rerata > 0) {
				$semhas_has_ba = true;
				$semhas_html .= '<span style="background-color: #f59e0b; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-clock-o"></i> Sudah Dinilai</span>';
			} elseif ($rg->semhas_status_undangan == 1) {
				$semhas_html .= '<span style="background-color: #3b82f6; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-calendar"></i> Terjadwal</span>';
			} else {
				$semhas_html .= '<span style="background-color: #e2e8f0; color: #475569; font-weight: 500; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-minus"></i> Belum Semhas</span>';
			}

			// Tanggal Semhas
			if (!empty($semhas_tgl)) {
				$semhas_html .= '<div style="font-size: 11px; color: #334155; font-weight: 600; margin-bottom: 3px;"><i class="fa fa-calendar text-primary" style="font-size: 10px;"></i> ' . $semhas_tgl . '</div>';
			} else {
				$semhas_html .= '<div style="font-size: 11px; color: #94a3b8; margin-bottom: 3px;"><i class="fa fa-calendar-o text-muted" style="font-size: 10px;"></i> -</div>';
			}

			// Rata-rata Nilai
			if (!empty($rg->semhas_nilai_rerata) && $rg->semhas_nilai_rerata > 0) {
				$nilai_format = number_format($rg->semhas_nilai_rerata, 2);
				$semhas_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #334155;">Rata-rata: <strong style="font-size: 13px; color: #0f766e; font-weight: 700;">' . $nilai_format . '</strong></div>';
			} else {
				$semhas_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #94a3b8;">Rata-rata: <span style="color: #cbd5e1;">-</span></div>';
			}

			// Buttons (Download Berita Acara & Detail Nilai)
			$semhas_html .= '<div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">';
			if ($semhas_has_ba) {
				$semhas_html .= '<a href="' . site_url('E_skripsi_seminar_hasil/cetak_pdf/' . $hash_id) . '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); width: 115px; justify-content: center;" title="Unduh Berita Acara SEMHAS">';
				$semhas_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</a>';

				$semhas_html .= '<button type="button" class="btn btn-xs btn-default" onclick="open_modal_detail(' . $id_judul . ', \'semhas\')" style="font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; border: 1px solid #cbd5e1; color: #1e293b; display: inline-flex; align-items: center; gap: 4px; width: 115px; justify-content: center;" title="Lihat Rincian Nilai & Catatan Semhas">';
				$semhas_html .= '<i class="fa fa-list-alt text-primary"></i> Rincian Nilai</button>';
			} else {
				$semhas_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Berita Acara belum tersedia">';
				$semhas_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</span>';

				$semhas_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 10.5px; font-weight: 500; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Rincian Nilai belum tersedia">';
				$semhas_html .= '<i class="fa fa-list-alt text-muted"></i> Rincian Nilai</span>';
			}
			$semhas_html .= '</div>';

			$semhas_html .= '</div>';
			$row[] = $semhas_html;

			// 6. SIDANG (Status, Tanggal, Rata-rata Nilai, Button Download Berita Acara, Button Detail Nilai)
			$sidang_html = '<div class="cell-stage text-center">';
			$sidang_has_ba = false;
			$sidang_tgl = (!empty($rg->sidang_tanggal_seminar) && $rg->sidang_tanggal_seminar != '0000-00-00') ? date('d/m/Y', strtotime($rg->sidang_tanggal_seminar)) : '';

			// Status
			if (!empty($rg->sidang_hasil_p1)) {
				$sidang_has_ba = true;
				$status_txt = strtoupper((string)$rg->sidang_hasil_p1);
				$is_lulus   = ($status_txt == 'LULUS');
				$bg_color   = $is_lulus ? '#10b981' : '#ef4444';
				$mutu_str   = !empty($rg->sidang_nilai_mutu) ? ' [' . $rg->sidang_nilai_mutu . ']' : '';
				$sidang_html .= '<span style="background-color: ' . $bg_color . '; color: #ffffff; font-weight: 700; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-graduation-cap"></i> ' . $status_txt . $mutu_str . '</span>';
			} elseif (!empty($rg->sidang_nilai_rerata) && $rg->sidang_nilai_rerata > 0) {
				$sidang_has_ba = true;
				$sidang_html .= '<span style="background-color: #f59e0b; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-clock-o"></i> Sudah Dinilai</span>';
			} elseif ($rg->sidang_status_undangan == 1) {
				$sidang_html .= '<span style="background-color: #3b82f6; color: #ffffff; font-weight: 600; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-calendar"></i> Terjadwal</span>';
			} else {
				$sidang_html .= '<span style="background-color: #e2e8f0; color: #475569; font-weight: 500; font-size: 11px; padding: 3px 9px; border-radius: 12px; display: inline-block; margin-bottom: 4px;"><i class="fa fa-minus"></i> Belum Sidang</span>';
			}

			// Tanggal Sidang
			if (!empty($sidang_tgl)) {
				$sidang_html .= '<div style="font-size: 11px; color: #334155; font-weight: 600; margin-bottom: 3px;"><i class="fa fa-calendar text-primary" style="font-size: 10px;"></i> ' . $sidang_tgl . '</div>';
			} else {
				$sidang_html .= '<div style="font-size: 11px; color: #94a3b8; margin-bottom: 3px;"><i class="fa fa-calendar-o text-muted" style="font-size: 10px;"></i> -</div>';
			}

			// Rata-rata Nilai
			if (!empty($rg->sidang_nilai_rerata) && $rg->sidang_nilai_rerata > 0) {
				$nilai_format = number_format($rg->sidang_nilai_rerata, 2);
				$sidang_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #334155;">Rata-rata: <strong style="font-size: 13px; color: #0f766e; font-weight: 700;">' . $nilai_format . '</strong></div>';
			} else {
				$sidang_html .= '<div style="margin-bottom: 6px; font-size: 12px; color: #94a3b8;">Rata-rata: <span style="color: #cbd5e1;">-</span></div>';
			}

			// Buttons (Download Berita Acara & Detail Nilai)
			$sidang_html .= '<div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">';
			if ($sidang_has_ba) {
				$sidang_html .= '<a href="' . site_url('E_skripsi_sidang/cetak_pdf/' . $hash_id) . '" target="_blank" class="btn btn-xs" style="background-color: #e11d48; color: #ffffff !important; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.15); width: 115px; justify-content: center;" title="Unduh Berita Acara Sidang TA">';
				$sidang_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</a>';

				$sidang_html .= '<button type="button" class="btn btn-xs btn-default" onclick="open_modal_detail(' . $id_judul . ', \'sidang\')" style="font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; border: 1px solid #cbd5e1; color: #1e293b; display: inline-flex; align-items: center; gap: 4px; width: 115px; justify-content: center;" title="Lihat Rincian Nilai & Catatan Sidang">';
				$sidang_html .= '<i class="fa fa-list-alt text-primary"></i> Rincian Nilai</button>';
			} else {
				$sidang_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Berita Acara belum tersedia">';
				$sidang_html .= '<i class="fa fa-file-pdf-o"></i> Berita Acara</span>';

				$sidang_html .= '<span class="btn btn-xs disabled" style="background-color: #f1f5f9; color: #94a3b8 !important; border: 1px solid #cbd5e1; font-size: 10.5px; font-weight: 500; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; opacity: 0.8; cursor: not-allowed; width: 115px; justify-content: center;" title="Rincian Nilai belum tersedia">';
				$sidang_html .= '<i class="fa fa-list-alt text-muted"></i> Rincian Nilai</span>';
			}
			$sidang_html .= '</div>';

			$sidang_html .= '</div>';
			$row[] = $sidang_html;

			$data[] = $row;
		}

		$output = [
			"draw"            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
			"recordsTotal"    => $this->E_skripsi_nilai_berita_acara_model->count_all(),
			"recordsFiltered" => $this->E_skripsi_nilai_berita_acara_model->count_filtered(),
			"data"            => $data,
		];

		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}

	/**
	 * AJAX endpoint to fetch complete breakdown (Sempro, Semhas, Sidang) for modal dialog
	 */
	public function ajax_detail($id_judul_skripsi)
	{
		$id_ptk = $this->session->userdata('id_ptk');

		// 1. Data Mahasiswa & Judul
		$this->db->select("
			pj.*,
			m.nm_pd,
			m.foto,
			s.nm_lemb as prodi_name,
			COALESCE(NULLIF(pj.judul_ditetapkan, ''), pj.judul_skripsi_1) as judul_ta
		");
		$this->db->from('siakad_skripsi_pengajuan_judul pj');
		$this->db->join('mahasiswa_pt mp', 'mp.nipd = pj.nipd');
		$this->db->join('mahasiswa m', 'm.id_pd = mp.id_pd');
		$this->db->join('sms s', 's.id_sms = pj.id_sms');
		$this->db->where('pj.id_judul_skripsi', $id_judul_skripsi);
		$pj = $this->db->get()->row();

		if (!$pj) {
			$this->output->set_status_header(404)->set_output(json_encode(['status' => false, 'message' => 'Data tidak ditemukan']));
			return;
		}

		// Initials for avatar fallback
		$words = explode(' ', trim((string)$pj->nm_pd));
		$initials = '';
		foreach ($words as $w) {
			if (!empty($w)) {
				$initials .= strtoupper(mb_substr($w, 0, 1));
				if (strlen($initials) >= 2) break;
			}
		}

		// 2. All grades from siakad_skripsi_nilai_sempro (prioritaskan id_sms mahasiswa jika dosen lintas prodi)
		$prodi_sms = $this->db->escape($pj->id_sms);
		$this->db->select("ns.*, (SELECT nama_lengkap FROM siakad_skripsi_dosen WHERE id_ptk = ns.id_penilai ORDER BY (CASE WHEN id_sms = {$prodi_sms} THEN 1 ELSE 2 END) LIMIT 1) as nama_lengkap", FALSE);
		$this->db->from('siakad_skripsi_nilai_sempro ns');
		$this->db->where('ns.id_judul_skripsi', $id_judul_skripsi);
		$this->db->order_by('ns.id_nilai_sempro', 'ASC');
		$grades = $this->db->get()->result();

		// 3. Seminar / Sidang schedules
		$this->db->where('id_judul_skripsi', $id_judul_skripsi);
		$sched_rows = $this->db->get('siakad_skripsi_seminar_sidang')->result();
		$schedules = [];
		foreach ($sched_rows as $sc) {
			$schedules[$sc->kategori_pengajuan] = $sc;
		}

		// Helper to resolve role
		$get_role = function($id_penilai, $stage = 1) use ($pj, $schedules) {
			if ($id_penilai == $pj->dosen_pembimbing_1) {
				return 'Pembimbing Utama';
			} elseif ($id_penilai == $pj->dosen_pembimbing_2) {
				return 'Pembimbing Pendamping';
			}
			if ($stage == 3 && isset($schedules[3])) {
				$sc3 = $schedules[3];
				if ($id_penilai == $sc3->penguji_sidang_1) return 'Ketua Penguji / Penguji 1';
				if ($id_penilai == $sc3->penguji_sidang_2) return 'Penguji 2';
				if ($id_penilai == $sc3->penguji_sidang_3) return 'Penguji 3';
			}
			if ($id_penilai == $pj->dosen_penguji_sempro_1) return 'Penguji 1';
			if ($id_penilai == $pj->dosen_penguji_sempro_2) return 'Penguji 2';
			if ($id_penilai == $pj->dosen_penguji_sempro_3) return 'Penguji 3';
			return 'Penguji';
		};

		// ─── SEMPRO DATA ───
		$sempro_penguji = [];
		$sempro_total = 0;
		$sempro_count = 0;
		$sempro_hasil = '-';
		foreach ($grades as $g) {
			$role = $get_role($g->id_penilai, 1);
			if ($g->id_penilai == $pj->dosen_pembimbing_1 && !empty($g->sempro_hasil)) {
				$sempro_hasil = ucwords(strtolower(str_replace('_', ' ', $g->sempro_hasil)));
			}
			$nilai_akhir = $g->nilai_akhir * 1;
			if ($nilai_akhir > 0) {
				$sempro_total += $nilai_akhir;
				$sempro_count++;
			}
			$sempro_penguji[] = [
				'id_penilai'   => $g->id_penilai,
				'nama_lengkap' => $g->nama_lengkap,
				'role_dosen'   => $role,
				'nilai_a'      => $g->nilai_komponen_a * 1,
				'nilai_b'      => $g->nilai_komponen_b * 1,
				'nilai_c'      => $g->nilai_komponen_c * 1,
				'nilai_d'      => $g->nilai_komponen_d * 1,
				'nilai_e'      => $g->nilai_komponen_e * 1,
				'nilai_akhir'  => $nilai_akhir,
				'catatan_judul'             => $g->catatan_judul,
				'catatan_latar_belakang'    => $g->catatan_latar_belakang,
				'catatan_rumusan_masalah'   => $g->catatan_rumusan_masalah,
				'catatan_tujuan_penelitian' => $g->catatan_tujuan_penelitian,
				'catatan_tinjauan_pustaka'  => $g->catatan_tinjauan_pustaka,
				'catatan_metode_penelitian' => $g->catatan_metode_penelitian,
				'catatan_daftar_pustaka'    => $g->catatan_daftar_pustaka,
				'catatan_format_penulisan'  => $g->catatan_format_penulisan,
			];
		}
		$sempro_sc = $schedules[1] ?? null;

		// ─── SEMHAS DATA ───
		$semhas_penguji = [];
		$semhas_total = 0;
		$semhas_count = 0;
		$semhas_hasil = '-';
		foreach ($grades as $g) {
			$role = $get_role($g->id_penilai, 2);
			if ($g->id_penilai == $pj->dosen_pembimbing_1 && !empty($g->semhas_hasil)) {
				$semhas_hasil = ucwords(strtolower(str_replace('_', ' ', $g->semhas_hasil)));
			}
			$nilai_akhir = $g->semhas_nilai_akhir * 1;
			if ($nilai_akhir > 0) {
				$semhas_total += $nilai_akhir;
				$semhas_count++;
			}
			$semhas_penguji[] = [
				'id_penilai'   => $g->id_penilai,
				'nama_lengkap' => $g->nama_lengkap,
				'role_dosen'   => $role,
				'nilai_a'      => $g->semhas_nilai_a * 1,
				'nilai_b'      => $g->semhas_nilai_b * 1,
				'nilai_c'      => $g->semhas_nilai_c * 1,
				'nilai_d'      => $g->semhas_nilai_d * 1,
				'nilai_e'      => $g->semhas_nilai_e * 1,
				'nilai_f'      => $g->semhas_nilai_f * 1,
				'nilai_akhir'  => $nilai_akhir,
				'catatan_judul'             => $g->semhas_catatan_judul,
				'catatan_latar_belakang'    => $g->semhas_catatan_latar_belakang,
				'catatan_rumusan_masalah'   => $g->semhas_catatan_rumusan_masalah,
				'catatan_tujuan_penelitian' => $g->semhas_catatan_tujuan_penelitian,
				'catatan_tinjauan_pustaka'  => $g->semhas_catatan_tinjauan_pustaka,
				'catatan_metode_penelitian' => $g->semhas_catatan_metode_penelitian,
				'catatan_daftar_pustaka'    => $g->semhas_catatan_daftar_pustaka,
				'catatan_format_penulisan'  => $g->semhas_catatan_format_penulisan,
			];
		}
		$semhas_sc = $schedules[2] ?? null;

		// ─── SIDANG DATA ───
		$sidang_penguji = [];
		$sidang_total = 0;
		$sidang_count = 0;
		$sidang_hasil = '-';
		$sidang_mutu = '-';
		foreach ($grades as $g) {
			$role = $get_role($g->id_penilai, 3);
			if ($g->id_penilai == $pj->dosen_pembimbing_1) {
				if (!empty($g->sidang_hasil)) $sidang_hasil = strtoupper((string)$g->sidang_hasil);
				if (!empty($g->sidang_nilai_mutu)) $sidang_mutu = $g->sidang_nilai_mutu;
			}
			$nilai_akhir = $g->sidang_nilai_akhir * 1;
			if ($nilai_akhir > 0) {
				$sidang_total += $nilai_akhir;
				$sidang_count++;
			}
			$sidang_penguji[] = [
				'id_penilai'   => $g->id_penilai,
				'nama_lengkap' => $g->nama_lengkap,
				'role_dosen'   => $role,
				'nilai_a'      => $g->sidang_nilai_a * 1,
				'nilai_b'      => $g->sidang_nilai_b * 1,
				'nilai_c'      => $g->sidang_nilai_c * 1,
				'nilai_d'      => $g->sidang_nilai_d * 1,
				'nilai_e'      => $g->sidang_nilai_e * 1,
				'nilai_akhir'  => $nilai_akhir,
				'uraian_revisi' => $g->sidang_uraian_revisi,
			];
		}
		$sidang_sc = $schedules[3] ?? null;

		$response = [
			'status' => true,
			'student' => [
				'id_judul_skripsi' => $pj->id_judul_skripsi,
				'hash_id'          => md5($pj->id_judul_skripsi),
				'nipd'             => $pj->nipd,
				'nm_pd'            => $pj->nm_pd,
				'initials'         => $initials,
				'prodi_name'       => $pj->prodi_name,
				'judul_ta'         => $pj->judul_ta,
				'foto'             => $pj->foto
			],
			'sempro' => [
				'rerata'       => $sempro_count > 0 ? round($sempro_total / $sempro_count, 2) : 0,
				'hasil'        => $sempro_hasil,
				'tanggal'      => $sempro_sc ? $sempro_sc->tanggal_seminar : null,
				'jam'          => $sempro_sc ? substr($sempro_sc->jam, 0, 5) : null,
				'tempat'       => $sempro_sc ? $sempro_sc->tempat : '-',
				'link_dokumen' => $sempro_sc ? $sempro_sc->link_dokumen : null,
				'penguji_data' => $sempro_penguji
			],
			'semhas' => [
				'rerata'       => $semhas_count > 0 ? round($semhas_total / $semhas_count, 2) : 0,
				'hasil'        => $semhas_hasil,
				'tanggal'      => $semhas_sc ? $semhas_sc->tanggal_seminar : null,
				'jam'          => $semhas_sc ? substr($semhas_sc->jam, 0, 5) : null,
				'tempat'       => $semhas_sc ? $semhas_sc->tempat : '-',
				'link_dokumen' => $semhas_sc ? $semhas_sc->link_dokumen : null,
				'penguji_data' => $semhas_penguji
			],
			'sidang' => [
				'rerata'       => $sidang_count > 0 ? round($sidang_total / $sidang_count, 2) : 0,
				'hasil'        => $sidang_hasil,
				'nilai_mutu'   => $sidang_mutu,
				'tanggal'      => $sidang_sc ? $sidang_sc->tanggal_seminar : null,
				'jam'          => $sidang_sc ? substr($sidang_sc->jam, 0, 5) : null,
				'tempat'       => $sidang_sc ? $sidang_sc->tempat : '-',
				'link_dokumen' => $sidang_sc ? $sidang_sc->link_dokumen : null,
				'penguji_data' => $sidang_penguji
			]
		];

		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
}
