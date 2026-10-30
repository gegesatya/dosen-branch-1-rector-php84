<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lecturer_persetujuan_krs extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lecturer_persetujuan_krs_model', 'Lecturer_persetujuan_krs_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = ['dosen'];
		if (!$this->ion_auth->in_group($group)) {
			//return show_error('Anda harus login sebagai dosen.');
			redirect('auth/logout', 'refresh');
		}
	}

	public function index()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');

		$summary = $this->Lecturer_persetujuan_krs_model->get_summary_stats($id_ptk);
		if ($summary->pending > 0) {
			$this->session->set_flashdata('jumlah', $summary->pending);
		}

		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_krs = $setting->tgl_akhir_krs ?? '';

		if (!empty($tgl_akhir_krs) && strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s'))) {
			$this->session->set_flashdata('msg', 'Periode pendaftaran KRS sudah berakhir. Approval dan perbaikan KRS sudah ditutup.');
		}

		$output['judul_besar'] = 'Persetujuan KRS';
		$output['judul_kecil'] = 'Politeknik WBI';
		$output['menu_aktif'] = 'm_persetujuan_krs';
		$output['parent_1'] = 'active open';
		$output['content'] = 'persetujuan_krs/persetujuan_krs_view';
		$output['after_page'] = 'persetujuan_krs/persetujuan_krs_after_page';
		$output['dd_status_krs'] = $this->Lecturer_persetujuan_krs_model->f_status_krs();
		$output['list_angkatan'] = $this->Lecturer_persetujuan_krs_model->get_list_angkatan($id_ptk);
		$output['summary'] = $summary;

		$this->load->view('master_page', $output);
	}

	public function ajax_list($status = 0, $angkatan = 0)
	{
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_krs = $setting->tgl_akhir_krs ?? '';

		$list = $this->Lecturer_persetujuan_krs_model->get_datatables($status, $angkatan);
		$data = [];
		$no = (int)($this->input->post('start') ?? 0);
		foreach ($list as $rg) {
			$no++;
			$row = [];

			// Total SKS logic (Use status check instead of ID)
			$total_sks = ($rg->id_status_krs == 3) ? (int)$rg->total_sks_nilai : (int)$rg->total_sks_detail;

			// Deteksi apakah baru di-approve dalam 1 jam terakhir (3600 detik)
			$is_recently_approved = false;
			$time_badge_text = '';
			if ($rg->id_status_krs == 3 && !empty($rg->approved_date) && $rg->approved_date != '0000-00-00 00:00:00') {
				$diff_seconds = time() - strtotime($rg->approved_date);
				if ($diff_seconds >= 0 && $diff_seconds <= 3600) {
					$is_recently_approved = true;
					$diff_minutes = max(1, (int)round($diff_seconds / 60));
					$time_badge_text = ($diff_minutes <= 1) ? 'Baru saja' : $diff_minutes . ' mnt lalu';
				}
			}

			$is_daftar = ($rg->id_status_krs == 1 || $rg->id_status_krs == 0 || strtolower((string)($rg->nm_status_krs ?? '')) == 'daftar');

			if ($is_daftar) {
				$row['DT_RowClass'] = 'row-status-draft';
			} elseif ($is_recently_approved) {
				$row['DT_RowClass'] = 'row-recently-approved';
			}

			// Status info
			if ($is_daftar) {
				$status1 = '<b>SKS : </b>' . $total_sks . '<br><b>Status : </b><span class="label" style="background-color: #ea580c; color: #fff; font-weight: 700; padding: 2px 7px; border-radius: 3px; font-size: 10px; letter-spacing: 0.3px;">Daftar</span>';
				$status1 .= '<div style="margin-top: 5px;"><span class="badge badge-draft-warning" style="background: #fff7ed; color: #c2410c; border: 1px solid #fdba74; font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;" title="Mahasiswa belum menekan tombol Submit KRS di portal mahasiswa"><i class="fa fa-exclamation-circle" style="color: #ea580c; font-size: 10px;"></i> Belum Submit KRS</span></div>';
			} else {
				$nm_status = ($rg->id_status_krs == 2) ? 'Diajukan' : $rg->nm_status_krs;
				$status1 = '<b>SKS : </b>' . $total_sks . '<br><b>Status : </b>' . $nm_status;
				if ($is_recently_approved) {
					$status1 .= '<div style="margin-top: 4px;"><span class="badge badge-recently-approved" title="Baru disetujui ' . $time_badge_text . ' (penanda aktif 1 jam)"><i class="fa fa-check-circle"></i> Baru Di-Approve (' . $time_badge_text . ')</span></div>';
				}
			}

			// Tgl Diajukan (Hanya dari pengajuan_date)
			if ($is_daftar) {
				$tgl_diajukan = '<span class="label" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;" title="Mahasiswa belum mengajukan / submit KRS"><i class="fa fa-clock-o"></i> Belum Submit</span>';
			} else {
				$tgl_diajukan = '<span class="text-muted" style="color: #999; font-size: 11px;">-</span>';
				if (!empty($rg->pengajuan_date) && $rg->pengajuan_date != '0000-00-00 00:00:00') {
					$tgl_diajukan = '<div style="font-size: 10.5px; line-height: 1.25; white-space: nowrap;"><i class="fa fa-calendar" style="color: #3498db; font-size: 10px;"></i> ' . date('d-m-Y', strtotime($rg->pengajuan_date)) . '<br><i class="fa fa-clock-o" style="color: #3498db; font-size: 10px;"></i> ' . date('H:i', strtotime($rg->pengajuan_date)) . ' WIB</div>';
				}
			}

			// Tgl Direspon (Hanya dari approved_date atau change_date jika status Disetujui/Ditolak)
			if ($is_daftar) {
				$tgl_direspon = '<span class="text-muted" style="color: #94a3b8; font-size: 10.5px; font-style: italic;">Menunggu Submit</span>';
			} else {
				$tgl_direspon = '<span class="text-muted" style="color: #999; font-size: 10.5px;">Belum direspon</span>';
				$resp_date = (!empty($rg->approved_date) && $rg->approved_date != '0000-00-00 00:00:00') ? $rg->approved_date : ((!empty($rg->change_date) && $rg->change_date != '0000-00-00 00:00:00' && $rg->id_status_krs == 4) ? $rg->change_date : '');

				if (!empty($resp_date)) {
					if ($rg->id_status_krs == 3) {
						if ($is_recently_approved) {
							$tgl_direspon = '<div style="font-size: 10.5px; line-height: 1.25; white-space: nowrap;"><i class="fa fa-check-circle" style="color: #16a34a; font-size: 11px;"></i> <span style="font-weight: 700; color: #15803d;">' . date('d-m-Y', strtotime($resp_date)) . '</span><br><span style="color: #16a34a; font-weight: 600;"><i class="fa fa-clock-o" style="font-size: 10px;"></i> ' . date('H:i', strtotime($resp_date)) . ' WIB</span></div>';
						} else {
							$tgl_direspon = '<div style="font-size: 10.5px; line-height: 1.25; white-space: nowrap;"><i class="fa fa-calendar" style="color: #27ae60; font-size: 10px;"></i> ' . date('d-m-Y', strtotime($resp_date)) . '<br><i class="fa fa-clock-o" style="color: #27ae60; font-size: 10px;"></i> ' . date('H:i', strtotime($resp_date)) . ' WIB</div>';
						}
					} elseif ($rg->id_status_krs == 4) {
						$tgl_direspon = '<div style="font-size: 10.5px; line-height: 1.25; white-space: nowrap;"><i class="fa fa-times-circle" style="color: #dc2626; font-size: 11px;"></i> <span style="font-weight: 600; color: #b91c1c;">' . date('d-m-Y', strtotime($resp_date)) . '</span><br><span style="color: #dc2626; font-size: 10px;"><i class="fa fa-clock-o"></i> ' . date('H:i', strtotime($resp_date)) . ' WIB</span></div>';
					}
				}
			}
			// Action Dropdown Menu
			$is_deadline_passed = (!empty($tgl_akhir_krs) && strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s')));
			$nm_pd_safe = addslashes($rg->nm_pd);
			$url_pdf = site_url("Lecturer_persetujuan_krs/preview_pdf/{$rg->id_rencana_studi}");
			$url_ar = site_url("Lecturer_data_perwalian/academic_record/{$rg->nipd}");
			$item_ar = '<li><a href="' . $url_ar . '" target="_blank"><i class="fa fa-graduation-cap" style="color: #10b981;"></i> Academic Record</a></li>';

			$is_daftar = ($rg->id_status_krs == 1 || $rg->id_status_krs == 0 || strtolower((string)($rg->nm_status_krs ?? '')) == 'daftar');

			if ($is_daftar) {
				$item_detail = '<li><a href="javascript:void(0)" onclick="show_krs(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ', ' . "'detail'" . ')"><i class="fa fa-search" style="color: #3b82f6;"></i> Monitor Pendaftaran</a></li>';
				$item_wa_reminder = '';
				if (!empty($rg->registrasi_no_hp)) {
					$clean_hp = preg_replace('/[^0-9]/', '', $rg->registrasi_no_hp);
					if (str_starts_with($clean_hp, '0')) $clean_hp = '62' . substr($clean_hp, 1);
					$wa_msg = rawurlencode("Halo " . $rg->nm_pd . " (" . $rg->nipd . "), kami melihat rencana studi Anda sudah diisi (" . $total_sks . " SKS) tetapi belum di-Submit (masih status Draft). Mohon segera login ke Portal Mahasiswa dan klik tombol 'Submit KRS' agar dapat kami verifikasi dan setujui. Terima kasih.");
					$item_wa_reminder = '<li><a href="https://wa.me/' . $clean_hp . '?text=' . $wa_msg . '" target="_blank"><i class="fa fa-whatsapp" style="color: #10b981;"></i> Ingatkan Submit (WA)</a></li>';
				}
				$menu_items = $item_detail . $item_wa_reminder . $item_ar;
			} else {
				$item_detail = '<li><a href="javascript:void(0)" onclick="show_krs(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ', ' . "'detail'" . ')"><i class="fa fa-search" style="color: #3b82f6;"></i> Detail KRS</a></li>';
				$item_pdf = '<li><a href="' . $url_pdf . '" target="_new"><i class="fa fa-print" style="color: #0284c7;"></i> Cetak / Lihat KRS</a></li>';
				$item_perbaikan = '<li><a href="javascript:void(0)" onclick="perbaikan_krs(' . "'" . $rg->id_rencana_studi . "', '" . $nm_pd_safe . "', '" . $rg->nipd . "'" . ')"><i class="fa fa-undo" style="color: #f59e0b;"></i> Buka Perbaikan</a></li>';

				$menu_items = $item_detail . $item_pdf . $item_ar;
				if (!$is_deadline_passed) {
					$menu_items .= '<li class="divider" style="margin: 4px 0;"></li>' . $item_perbaikan;
				}
			}

			if ($rg->id_status_krs == 2) {
				// Status 2: Menunggu Persetujuan (Tombol langsung Approval, bukan dropdown)
				if (!$is_deadline_passed && $total_sks <= 24) {
					$html = '<a href="javascript:void(0)" class="btn-action-approval" onclick="show_krs(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ', ' . "'approval'" . ')"><i class="fa fa-check-circle"></i> Approval</a>';
				} else {
					$html = '<a href="javascript:void(0)" class="btn-action-detail" onclick="show_krs(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . ', ' . "'detail'" . ')"><i class="fa fa-search"></i> Detail</a>';
				}
			} elseif ($rg->id_status_krs == 4) {
				$html = '<span class="label-action-rejected">Ditolak</span>';
			} elseif ($is_daftar) {
				// Status 0 / 1 (Draft) -> Dropdown Aksi bernuansa Draft
				$html = '<div class="btn-group">
					<button type="button" class="btn-action-dropdown btn-action-draft dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="KRS Status Draft (Belum Submit)">
						<i class="fa fa-pencil-square-o" style="font-size: 11px; color: #ea580c;"></i> Aksi <i class="fa fa-angle-down" style="font-size: 10px; margin-left: 1px; opacity: 0.7;"></i>
					</button>
					<ul class="dropdown-menu custom-action-menu" role="menu">
						' . $menu_items . '
					</ul>
				</div>';
			} else {
				// Status 3 (Disetujui) -> Dropdown Aksi
				$html = '<div class="btn-group">
					<button type="button" class="btn-action-dropdown dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-cog" style="font-size: 11px; opacity: 0.8;"></i> Aksi <i class="fa fa-angle-down" style="font-size: 10px; margin-left: 1px; opacity: 0.7;"></i>
					</button>
					<ul class="dropdown-menu custom-action-menu" role="menu">
						' . $menu_items . '
					</ul>
				</div>';
			}

			$row[] = $html;

			// Warnings & KIP
			$warning = '';
			if ($rg->low_grade_count > 0) {
				$warning = '<div style="margin-top: 4px;"><span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-exclamation-triangle" style="color: #d97706; font-size: 10px;"></i> Butuh Perhatian Nilai</span></div>';
			}
			if (!empty($rg->tgl_masuk_sp)) {
				$tahun_masuk = date('Y', strtotime($rg->tgl_masuk_sp));
				if ((date('Y') - $tahun_masuk) > 4) {
					$warning .= '<div style="margin-top: 4px;"><span class="badge" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-clock-o" style="color: #ef4444; font-size: 10px;"></i> Masa Studi > 4 Thn</span></div>';
				}
			}
			$kip_status = "";
			if ($rg->kip_skema !== null) {
				$skema_raw = trim((string)$rg->kip_skema);
				$skema_lbl = !empty($skema_raw) ? 'KIP ' . $skema_raw : 'Peserta KIP';
				$kip_status = '<div style="margin-top: 4px;"><span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 9.5px; font-weight: 600; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-id-card-o" style="color: #2563eb; font-size: 10px;"></i> ' . htmlspecialchars($skema_lbl) . '</span></div>';
			}

			$hp_info = '<span class="text-muted" style="color: #aaa; font-size: 11px;">-</span>';
			if (!empty($rg->registrasi_no_hp)) {
				$clean_hp = preg_replace('/[^0-9]/', '', $rg->registrasi_no_hp);
				if (str_starts_with($clean_hp, '0')) $clean_hp = '62' . substr($clean_hp, 1);
				$wa_link = ' <a href="https://wa.me/' . $clean_hp . '" target="_blank" class="btn btn-xs" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 4px; padding: 1px 6px; font-size: 10px; font-weight: 600; text-decoration: none; line-height: 1.2;"><i class="fa fa-whatsapp" style="color: #10b981;"></i> WA</a>';
				$hp_info = '<div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #334155;"><i class="fa fa-phone text-muted" style="font-size: 11px; color: #64748b;"></i><span style="font-weight: 500; letter-spacing: 0.3px;">' . $rg->registrasi_no_hp . '</span>' . $wa_link . '</div>';
			}

			$col_info = $hp_info . $kip_status . $warning;

			// Academic Tier Star Rating (Sesuai Star System Dashboard Perwalian)
			$last_ipk = isset($rg->last_ipk) ? (float)$rg->last_ipk : 0.0;
			$stars_html = $this->_get_star_rating_html($last_ipk, true);

			$row[] = '<div style="overflow: hidden; line-height: 1.4;"><span style="font-weight: 700; color: #1e293b;">' . $rg->nipd . '</span>' . $stars_html . '<br><span style="color: #334155;">' . $rg->nm_pd . '</span></div>';
			$row[] = $col_info;
			$row[] = $status1;
			$row[] = $tgl_diajukan;
			$row[] = $tgl_direspon;
			
			// Messages Column (Bubble Chat Button)
			$has_msg = !empty($rg->catatan_dosen_wali);
			if ($has_msg) {
				$btn_pesan = '<a href="javascript:void(0)" class="btn-msg-active" onclick="balas_pesan(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . '); return false;" title="Buka Pesan (Ada Catatan)"><i class="fa fa-comments"></i><span class="msg-dot"></span></a>';
			} else {
				$btn_pesan = '<a href="javascript:void(0)" class="btn-msg-empty" onclick="balas_pesan(' . "'" . $rg->id_rencana_studi . "'" . ', ' . "'" . $rg->nipd . "'" . ', ' . "'" . $rg->id_smt . "'" . '); return false;" title="Kirim Pesan"><i class="fa fa-comment-o"></i></a>';
			}
			$row[] = '<div class="text-center">' . $btn_pesan . '</div>';

			$data[] = $row;
		}

		$draw = $this->input->post('draw') !== NULL ? (int)$this->input->post('draw') : 1;
		$output = [
			"draw" => $draw,
			"recordsTotal" => $this->Lecturer_persetujuan_krs_model->count_all($status, $angkatan),
			"recordsFiltered" => $this->Lecturer_persetujuan_krs_model->count_filtered($status, $angkatan),
			"data" => $data,
		];

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($output));
	}

	public function ajax_list_belum_krs()
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$list = $this->Lecturer_persetujuan_krs_model->get_list_belum_krs($id_ptk);

		$html = '';
		if (empty($list)) {
			$html = '<tr><td colspan="6" class="text-center" style="padding: 25px; color: #27ae60;"><i class="fa fa-check-circle" style="font-size: 28px;"></i><br><b style="font-size: 14px; margin-top: 6px; display: block;">Semua Mahasiswa Perwalian Sudah Mengisi KRS</b></td></tr>';
		} else {
			$no = 1;
			foreach ($list as $m) {
				$clean_hp = preg_replace('/[^0-9]/', '', $m->no_hp ?? '');
				if (str_starts_with($clean_hp, '0')) $clean_hp = '62' . substr($clean_hp, 1);
				
				$wa_btn = '<span class="text-muted" style="color: #999;">-</span>';
				if (!empty($clean_hp)) {
					$pesan_wa = urlencode("Halo {$m->nm_pd}, jangan lupa segera mengisi dan mengajukan KRS untuk semester ini ya. Terima kasih.");
					$wa_btn = '<a href="https://wa.me/' . $clean_hp . '?text=' . $pesan_wa . '" target="_blank" class="btn btn-xs" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 4px; font-size: 11px; padding: 2px 8px; margin-top: 3px; text-decoration: none;"><i class="fa fa-whatsapp" style="color: #10b981;"></i> Chat WA</a>';
				}

				$kip = '';
				if (!empty($m->kip_skema)) {
					$kip = ' <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 9.5px; padding: 1px 4px; border-radius: 3px;">KIP ' . $m->kip_skema . '</span>';
				}

				// Academic Tier Star Rating (Sesuai Star System Dashboard Perwalian)
				$stars_html = $this->_get_star_rating_html($m->last_ipk, true);

				$angkatan = !empty($m->tgl_masuk_sp) ? date('Y', strtotime($m->tgl_masuk_sp)) : ('20' . substr($m->nipd, 0, 2));

				$html .= '<tr>';
				$html .= '<td class="text-center" style="vertical-align: middle;">' . $no++ . '</td>';
				$html .= '<td style="vertical-align: middle;"><div style="overflow: hidden; line-height: 1.4;"><span style="font-weight: 700; color: #1e293b;">' . $m->nipd . '</span>' . $stars_html . '<br><span style="color: #334155;">' . $m->nm_pd . '</span>' . $kip . '</div></td>';
				$html .= '<td class="text-center" style="vertical-align: middle;">' . $angkatan . '</td>';
				$html .= '<td class="text-center" style="vertical-align: middle;">' . ($m->last_ipk > 0 ? number_format($m->last_ipk, 2) : '-') . '</td>';
				$html .= '<td class="text-center" style="vertical-align: middle;">' . ($m->sks_total > 0 ? $m->sks_total . ' SKS' : '-') . '</td>';
				$html .= '<td class="text-center" style="vertical-align: middle;">' . (!empty($m->no_hp) ? '<small style="color: #555;"><i class="fa fa-phone"></i> ' . $m->no_hp . '</small><br>' : '') . $wa_btn . '</td>';
				$html .= '</tr>';
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'total' => count($list),
				'html' => $html
			]));
	}

	public function view_krs($id_rencana_studi, $nipd, $id_smt)
	{
		$user = $this->ion_auth->user()->row();
		$user_first_name = $user->first_name;

		// 1. Basic Student Info
		$mahasiswa = $this->db->where('mp.nipd', $nipd)
			->select('m.nm_pd, mp.nipd, foto, mp.tgl_masuk_sp, mp.id_sms, ps.nm_lemb as nm_prodi')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->join('sms ps', 'ps.id_sms = mp.id_sms', 'left')
			->get()->row();

		if ($mahasiswa->foto) {
			$pic = '<img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $mahasiswa->foto . '" alt="foto_profil" height="220" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 1px solid #ddd;">';
		} else {
			$pic = '<img src="' . base_url('assets/images.png') . '" alt="foto_profil" height="150" style="border-radius: 8px; border: 1px solid #ddd;">';
		}

		// 2. KRS Items & Analysis
		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();
		if ($data_rencana_studi->id_status_krs == 3) {
			$krs_query = $this->db->select('mk.nm_mk, mk.kode_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap, kk.nm_kls, COALESCE(mkk.a_wajib, 1) as a_wajib')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->join('(SELECT id_mk, MAX(a_wajib) as a_wajib FROM feeder_mata_kuliah_kurikulum GROUP BY id_mk) mkk', 'mkk.id_mk = mk.id_mk', 'left')
				->where(['sn.nipd' => $nipd, 'kk.id_smt' => $id_smt])
				->order_by('nm_mk')
				->get();
		} else {
			$krs_query = $this->db->where(compact('id_rencana_studi'))
				->select('mk.*, kk.nm_kls, COALESCE(mkk.a_wajib, 1) as a_wajib')->from('siakad_krs_detail kd')
				->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->join('(SELECT id_mk, MAX(a_wajib) as a_wajib FROM feeder_mata_kuliah_kurikulum GROUP BY id_mk) mkk', 'mkk.id_mk = mk.id_mk', 'left')
				->get();
		}
		$krs_items = $krs_query->result();

		// 3. Curriculum & Semester Analysis
		$actual_recorded_smts = $this->db->where('nipd', $nipd)->order_by('id_smt ASC')->get('kuliah_mahasiswa')->result();
		$semester_expected = count($actual_recorded_smts) + 1;

		// Deteksi Timeline Kalender Angkatan & Gap Semester (Cuti / Tidak Aktif)
		$start_year = !empty($mahasiswa->tgl_masuk_sp) ? date('Y', strtotime($mahasiswa->tgl_masuk_sp)) : ('20' . substr($nipd, 0, 2));
		$start_smt = $start_year . '1';

		$expected_timeline = [];
		$curr_iter = (int)$start_smt;
		$target_iter = (int)$id_smt;
		while ($curr_iter < $target_iter) {
			$expected_timeline[] = (string)$curr_iter;
			$y_t = (int)substr((string)$curr_iter, 0, 4);
			$t_t = (int)substr((string)$curr_iter, 4, 1);
			if ($t_t == 1) $curr_iter = ($y_t * 10) + 2;
			else $curr_iter = (($y_t + 1) * 10) + 1;
		}

		$actual_smt_keys = array_map(fn($r) => (string)$r->id_smt, $actual_recorded_smts);
		$missing_smts = array_diff($expected_timeline, $actual_smt_keys);
		$cohort_semester = count($expected_timeline) + 1;

		$missing_labels = [];
		foreach ($missing_smts as $ms) {
			$y_ms = substr($ms, 0, 4);
			$t_ms = substr($ms, 4, 1);
			$next_y = (int)$y_ms + 1;
			$type_str = ($t_ms == '1') ? 'Ganjil' : 'Genap';
			$missing_labels[] = "Semester $type_str $y_ms/$next_y ($ms)";
		}

		$target_check_sem = (!empty($missing_smts) && $cohort_semester > $semester_expected) ? $cohort_semester : $semester_expected;

		$offered_this_sem = $this->db->select('mk.kode_mk, mk.nm_mk, mk.sks_mk, COALESCE(mkk.a_wajib, 0) as a_wajib')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'kk.id_mk = mk.id_mk')
			->join('(SELECT id_mk, MAX(a_wajib) as a_wajib FROM feeder_mata_kuliah_kurikulum GROUP BY id_mk) mkk', 'mkk.id_mk = mk.id_mk', 'left')
			->where(['kk.id_smt' => $id_smt, 'kk.id_sms' => ($mahasiswa->id_sms ?? '')])
			->where('SUBSTR(kk.nm_kls, 2, 1) =', (string)$target_check_sem)
			->group_by('mk.kode_mk, mk.nm_mk, mk.sks_mk, mkk.a_wajib')
			->get()->result();

		$is_taking_internship = false;
		$is_taking_regular = false;
		foreach ($krs_items as $item) {
			if (stripos($item->nm_mk, 'internship') !== false) {
				$is_taking_internship = true;
			} else {
				$is_taking_regular = true;
			}
		}

		$already_passed_names = [];
		$not_taken_names = [];
		$pilihan_offered_names = [];
		$pilihan_offered_list = [];
		$current_krs_codes = array_map(fn($i) => $i->kode_mk, $krs_items);

		$pilihan_taken_names = [];
		foreach ($krs_items as $item) {
			if (isset($item->a_wajib) && (int)$item->a_wajib === 0) {
				$pilihan_taken_names[] = $item->nm_mk;
			}
		}

		foreach ($offered_this_sem as $offered) {
			// Jika MK Pilihan, kumpulkan ke daftar penawaran MK pilihan semester ini
			if ((int)$offered->a_wajib !== 1) {
				$pilihan_offered_names[] = $offered->nm_mk;
				$pilihan_offered_list[] = [
					'kode_mk' => $offered->kode_mk,
					'nm_mk'   => $offered->nm_mk,
					'sks_mk'  => (int)$offered->sks_mk
				];
				continue;
			}

			if (in_array($offered->kode_mk, $current_krs_codes)) continue;

			$is_offered_internship = (stripos($offered->nm_mk, 'internship') !== false);
			// If student chooses regular track, skip internship options
			if ($is_taking_regular && $is_offered_internship) continue;
			// If student chooses internship track, skip regular options
			if ($is_taking_internship && !$is_offered_internship) continue;

			$passed = $this->db->select('sn.nilai_angka')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where(['sn.nipd' => $nipd, 'mk.kode_mk' => $offered->kode_mk, 'sn.nilai_angka >=' => 56])
				->where('kk.id_smt <', $id_smt)->limit(1)->get()->row();
			
			if ($passed) $already_passed_names[] = $offered->nm_mk;
			else $not_taken_names[] = $offered->nm_mk;
		}

		// Riwayat seluruh MK yang pernah diambil mahasiswa di semester lampau
		$prior_taken_query = $this->db->select('mk.kode_mk, mk.id_mk, sn.nilai_huruf, sn.nilai_angka, kk.id_smt, kk.nm_kls')
			->from('siakad_nilai_baru sn')
			->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->where('sn.nipd', $nipd)
			->where('kk.id_smt <', $id_smt)
			->get()->result();

		$prior_taken_codes = [];
		$prior_taken_id_mks = [];
		foreach ($prior_taken_query as $ptq) {
			$clean = trim((string)$ptq->kode_mk);
			if (!empty($clean)) $prior_taken_codes[$clean] = $ptq;
			if (!empty($ptq->id_mk)) $prior_taken_id_mks[$ptq->id_mk] = $ptq;
		}

		$mk_current = $mk_repeat = $mk_susulan = $mk_advance = 0;
		// Detect Double / Duplicate MK (berdasarkan kode_mk)
		$code_counts = [];
		$mk_by_code = [];
		foreach ($krs_items as $item) {
			$clean_code = trim((string)$item->kode_mk);
			if (!isset($code_counts[$clean_code])) {
				$code_counts[$clean_code] = 0;
				$mk_by_code[$clean_code] = [
					'kode_mk' => $clean_code,
					'nm_mk' => $item->nm_mk,
					'sks_mk' => $item->sks_mk,
					'classes' => []
				];
			}
			$code_counts[$clean_code]++;
			$mk_by_code[$clean_code]['classes'][] = $item->nm_kls;

			$mk_sem = (int) substr($item->nm_kls, 1, 1);
			$has_prior = isset($prior_taken_codes[$clean_code]) || (!empty($item->id_mk) && isset($prior_taken_id_mks[$item->id_mk]));

			$is_matching_sem = ($mk_sem == $semester_expected || $mk_sem == $cohort_semester);

			if ($mk_sem > 0 && $is_matching_sem) {
				if ($has_prior) $mk_repeat++;
				else $mk_current++;
			} elseif ($mk_sem > 0 && $mk_sem < min($semester_expected, $cohort_semester)) {
				if ($has_prior) $mk_repeat++;
				else $mk_susulan++;
			} else {
				$mk_current++;
			}
		}

		$duplicate_mks = [];
		foreach ($code_counts as $code => $cnt) {
			if ($cnt > 1) {
				$duplicate_mks[] = [
					'kode_mk' => $code,
					'nm_mk' => $mk_by_code[$code]['nm_mk'],
					'count' => $cnt,
					'classes' => $mk_by_code[$code]['classes']
				];
			}
		}
		$has_duplicate_mk = (count($duplicate_mks) > 0);

		// Generate KRS Table with Matching Column
		$this->load->library('table');
		$tmpl = [
			'table_open'          => '<table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">',
			'heading_cell_start'  => '<th class="text-center" style="vertical-align:middle; background: #f8fafc; font-weight: 700; color: #334155; font-size: 12px;">',
			'table_close'         => '</table>'
		];
		$this->table->set_template($tmpl);
		$this->table->set_heading(['No', 'Kelas', 'Smt', 'Kode', 'Mata Kuliah', 'Sifat', 'SKS', 'Kesesuaian Paket']);

		$i = 1;
		$total_sks = 0;
		foreach ($krs_items as $item) {
			$clean_code = trim((string)$item->kode_mk);
			$is_duplicate = ($code_counts[$clean_code] > 1);

			$mk_sem = (int) substr($item->nm_kls, 1, 1);
			$has_prior = isset($prior_taken_codes[$clean_code]) || (!empty($item->id_mk) && isset($prior_taken_id_mks[$item->id_mk]));

			if ($is_duplicate) {
				$badge_match = '<span class="label" style="background: #dc2626; color: #ffffff; font-size: 10px; font-weight: 700; padding: 3px 7px; border-radius: 3px; display: inline-block;"><i class="fa fa-exclamation-triangle"></i> Double MK (' . $code_counts[$clean_code] . 'x)</span>';
			} elseif ($mk_sem > 0 && ($mk_sem == $semester_expected || $mk_sem == $cohort_semester)) {
				if ($has_prior) {
					$badge_match = '<span class="label label-warning" style="font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 3px; display: inline-block;"><i class="fa fa-repeat"></i> Mengulang Smt ' . $mk_sem . '</span>';
				} else {
					$badge_match = '<span class="label label-success" style="font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 3px; display: inline-block;"><i class="fa fa-check"></i> Sesuai Smt ' . $mk_sem . '</span>';
				}
			} elseif ($mk_sem > 0 && $mk_sem < min($semester_expected, $cohort_semester)) {
				if ($has_prior) {
					$badge_match = '<span class="label label-warning" style="font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 3px; display: inline-block;"><i class="fa fa-repeat"></i> Mengulang Smt ' . $mk_sem . '</span>';
				} else {
					$badge_match = '<span class="label" style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 3px; display: inline-block;"><i class="fa fa-clock-o"></i> Susulan Smt ' . $mk_sem . '</span>';
				}
			} else {
				$badge_match = '<span class="label" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 3px; display: inline-block;">Smt ' . $mk_sem . '</span>';
			}

			$sifat_badge = (isset($item->a_wajib) && $item->a_wajib == 1) 
				? '<span class="label" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">Wajib</span>'
				: '<span class="label" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">Pilihan</span>';

			$row_bg = $is_duplicate ? 'background-color: #fff1f2;' : '';
			$mk_name_display = $item->nm_mk;
			if ($is_duplicate) {
				$mk_name_display = '<span style="color: #be123c; font-weight: 700;">' . htmlspecialchars($item->nm_mk) . '</span> <span class="badge" style="background: #e11d48; color: #fff; font-size: 9px; padding: 2px 5px; vertical-align: middle;"><i class="fa fa-clone"></i> Double (' . $item->kode_mk . ')</span>';
			}

			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right', 'style' => 'vertical-align: middle; ' . $row_bg],
				['data' => '<b>' . $item->nm_kls . '</b>', 'class' => 'text-center', 'style' => 'vertical-align: middle; ' . $row_bg],
				['data' => substr($item->nm_kls, 1, 1), 'class' => 'text-center', 'style' => 'vertical-align: middle; ' . $row_bg],
				['data' => ($is_duplicate ? '<strong style="color: #e11d48;">' . $item->kode_mk . '</strong>' : $item->kode_mk), 'style' => 'vertical-align: middle; ' . $row_bg],
				['data' => $mk_name_display, 'style' => 'vertical-align: middle; font-weight: 500; ' . $row_bg],
				['data' => $sifat_badge, 'class' => 'text-center', 'style' => 'vertical-align: middle; ' . $row_bg],
				['data' => (int) $item->sks_mk, 'class' => 'text-center', 'style' => 'vertical-align: middle; font-weight: 700; ' . $row_bg],
				['data' => $badge_match, 'class' => 'text-center', 'style' => 'vertical-align: middle; ' . $row_bg]
			]);
			$total_sks += $item->sks_mk;
		}

		$this->table->add_row([
			['data' => '<b>Jumlah SKS</b>', 'class' => 'text-center', 'colspan' => 6],
			['data' => '<b>' . $total_sks . '</b>', 'class' => 'text-center'],
			['data' => '', 'class' => 'text-center']
		]);
		$tabel_krs_raw = $this->table->generate();
		$this->table->clear();

		// Matching Summary Header Bar (Modern SaaS Card)
		$badge_sum = '';
		if ($mk_current == count($krs_items) && count($krs_items) > 0 && !$has_duplicate_mk) {
			$sem_paket_lbl = (!empty($missing_smts) && $cohort_semester != $semester_expected) ? "Smt $cohort_semester" : "Smt $semester_expected";
			$badge_sum = '<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 14px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 2px rgba(16,185,129,0.08);"><i class="fa fa-check-circle" style="color: #10b981;"></i> ' . $mk_current . ' MK Sesuai Paket ' . $sem_paket_lbl . '</span>';
		} else {
			$badges = [];
			if ($has_duplicate_mk) $badges[] = '<span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-exclamation-triangle" style="color: #dc2626;"></i> ' . count($duplicate_mks) . ' Double MK</span>';
			if ($mk_current > 0) $badges[] = '<span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-check" style="color: #3b82f6;"></i> ' . $mk_current . ' MK Paket</span>';
			if ($mk_repeat > 0) $badges[] = '<span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-repeat" style="color: #f97316;"></i> ' . $mk_repeat . ' Mengulang</span>';
			if ($mk_susulan > 0) $badges[] = '<span style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-clock-o" style="color: #0284c7;"></i> ' . $mk_susulan . ' Susulan</span>';
			$badge_sum = '<div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap;">' . implode('', $badges) . '</div>';
		}

		$sem_display_title = !empty($missing_smts) 
			? "Semester $semester_expected (Riil) / Smt $cohort_semester (Angkatan $start_year)"
			: "Semester $semester_expected";

		$banner_match = '<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 14px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
			<div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
				<span style="background: #e0e7ff; color: #4338ca; width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; border: 1px solid #c7d2fe;"><i class="fa fa-graduation-cap"></i></span>
				<strong style="color: #0f172a; font-size: 13px;">' . $sem_display_title . '</strong>
				<span style="color: #cbd5e1; margin: 0 4px;">/</span>
				<span style="color: #475569; font-size: 12px; font-weight: 500;"><i class="fa fa-university" style="color: #059669; font-size: 10.5px; margin-right: 3px;"></i> ' . htmlspecialchars($mahasiswa->nm_prodi ?? '-') . '</span>
			</div>
			<div>' . $badge_sum . '</div>
		</div>';

		$alert_gap = '';
		if (!empty($missing_smts)) {
			$alert_gap = '<div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin-top: 12px; margin-bottom: 8px; font-size: 12px; color: #92400e; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
				<div style="display: flex; align-items: flex-start; gap: 9px;">
					<i class="fa fa-exclamation-triangle" style="color: #d97706; font-size: 15px; margin-top: 2px;"></i>
					<div style="line-height: 1.5;">
						<strong style="color: #b45309; font-size: 12.5px;">Catatan Riwayat Keaktifan Perkuliahan:</strong><br>
						Mahasiswa terdeteksi tidak mengikuti perkuliahan pada <strong>' . implode(', ', $missing_labels) . '</strong> <em>(kemungkinan berstatus cuti akademik atau tidak aktif)</em>.<br>
						<span style="color: #78350f;">Masa studi tempuh riil saat ini tercatat <strong>' . count($actual_recorded_smts) . ' semester</strong>, dan mata kuliah yang diprogramkan pada semester ini mengikuti paket <strong>Semester ' . $cohort_semester . '</strong> bersama rekan seangkatannya (Angkatan ' . $start_year . ').</span>
					</div>
				</div>
			</div>';
		}

		$alert_not_taken = '';
		if (!empty($not_taken_names) && $total_sks < 22) {
			$alert_not_taken = '<div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 8px 12px; margin-top: 12px; margin-bottom: 8px; font-size: 12px; color: #92400e; display: flex; align-items: center; gap: 6px;">
				<i class="fa fa-info-circle" style="color: #d97706; font-size: 13px;"></i>
				<span><strong>Catatan MK Wajib:</strong> MK Wajib paket semester ' . $target_check_sem . ' yang belum diprogramkan: <strong>' . implode(', ', array_map('html_escape', $not_taken_names)) . '</strong>.</span>
			</div>';
		}

		$alert_pilihan = '';
		if (!empty($pilihan_offered_names)) {
			$cnt_pilihan_offered = count($pilihan_offered_names);
			$cnt_pilihan_taken = count($pilihan_taken_names);

			$taken_info = '';
			if ($cnt_pilihan_taken > 0) {
				$taken_info = ' Mahasiswa telah memprogramkan <strong style="color: #1d4ed8;">' . $cnt_pilihan_taken . ' MK Pilihan</strong>: <span style="color: #334155; font-style: italic;">' . implode(', ', array_map('html_escape', $pilihan_taken_names)) . '</span>.';
			} else {
				$taken_info = ' Mahasiswa <strong style="color: #64748b;">belum memprogramkan</strong> MK Pilihan pada semester ini (bersifat opsional sesuai peminatan/kuota SKS).';
			}

			// Urutkan MK Pilihan alfabetis (A-Z) agar mudah dibaca dosen
			usort($pilihan_offered_list, fn($a, $b) => strcasecmp($a['nm_mk'], $b['nm_mk']));

			$items_markup = '';
			$no_pilihan = 1;
			foreach ($pilihan_offered_list as $pol) {
				$is_taken = in_array($pol['kode_mk'], $current_krs_codes);
				$row_bg = $is_taken ? '#ecfdf5' : '#ffffff';
				$row_border = $is_taken ? '#a7f3d0' : '#e2e8f0';
				$status_badge = $is_taken 
					? '<span class="label label-success" style="font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 3px; white-space: nowrap;"><i class="fa fa-check"></i> Diambil</span>'
					: '<span style="color: #64748b; font-size: 10px; font-weight: 600; background: #f1f5f9; padding: 1px 5px; border-radius: 3px; border: 1px solid #e2e8f0;">' . $pol['sks_mk'] . ' SKS</span>';

				$items_markup .= '<div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 5px 8px; background: ' . $row_bg . '; border: 1px solid ' . $row_border . '; border-radius: 4px; font-size: 11px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
				$items_markup .= '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">';
				$items_markup .= '<span style="color: #64748b; font-weight: 600; font-size: 10.5px; margin-right: 4px;">' . $no_pilihan++ . '.</span>';
				$items_markup .= '<span style="color: #0f172a; font-weight: ' . ($is_taken ? '700' : '500') . ';" title="' . htmlspecialchars($pol['nm_mk']) . '">' . htmlspecialchars($pol['nm_mk']) . '</span>';
				$items_markup .= '<span style="color: #64748b; font-size: 10px; margin-left: 3px;">(' . htmlspecialchars($pol['kode_mk']) . ')</span>';
				$items_markup .= '</div>';
				$items_markup .= '<div>' . $status_badge . '</div>';
				$items_markup .= '</div>';
			}

			$details_html = '<details style="margin-top: 6px;">
				<summary style="cursor: pointer; color: #2563eb; font-weight: 600; font-size: 11.5px; outline: none; user-select: none;">
					<i class="fa fa-list-ul"></i> Lihat daftar ' . $cnt_pilihan_offered . ' MK Pilihan yang ditawarkan semester ini
				</summary>
				<div style="margin-top: 8px; padding: 8px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; max-height: 230px; overflow-y: auto;">
					<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 5px;">
						' . $items_markup . '
					</div>
				</div>
			</details>';

			$alert_pilihan = '<div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 12px; margin-top: 8px; margin-bottom: 10px; font-size: 12px; color: #1e40af;">
				<div style="display: flex; align-items: flex-start; gap: 7px;">
					<i class="fa fa-info-circle" style="color: #3b82f6; font-size: 14px; margin-top: 2px;"></i>
					<div style="flex: 1;">
						<span><strong>Catatan MK Pilihan:</strong> Semester ' . $semester_expected . ' menawarkan <strong>' . $cnt_pilihan_offered . ' MK Pilihan</strong>.' . $taken_info . '</span>
						' . $details_html . '
					</div>
				</div>
			</div>';
		}

		$krs_review = [
			'total_mk' => count($krs_items),
			'total_sks' => $total_sks,
			'expected_semester' => $semester_expected,
			'cohort_semester' => $cohort_semester,
			'missing_smts' => $missing_smts,
			'missing_labels' => $missing_labels,
			'mk_current' => $mk_current,
			'mk_repeat' => $mk_repeat,
			'mk_susulan' => $mk_susulan,
			'mk_advance' => $mk_advance,
			'has_duplicate_mk' => $has_duplicate_mk,
			'duplicate_count' => count($duplicate_mks),
			'duplicate_mks' => $duplicate_mks,
			'total_offered_this_sem' => count($offered_this_sem),
			'already_passed_names' => $already_passed_names,
			'not_taken_names' => $not_taken_names,
			'pilihan_offered_names' => $pilihan_offered_names,
			'pilihan_taken_names' => $pilihan_taken_names
		];

		// 4. Academic Performance
		$data_kuliah = $this->db->where('nipd', $nipd)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
		$ips_labels = []; $ips_values = []; $ipk_values = []; $last_ipk = 0;
		
		$this->table->set_template($tmpl);
		$this->table->set_heading(['No', 'Semester', 'IPS', 'SKS', 'IPK', 'SKS Total']);
		$i = 1;
		foreach ($data_kuliah as $dk) {
			$ips_labels[] = $dk->id_smt;
			$ips_values[] = (float)$dk->ips;
			$ipk_values[] = (float)$dk->ipk;
			$last_ipk = (float)$dk->ipk;
			$this->table->add_row([
				['data' => $i++ . '.', 'class' => 'text-right'],
				['data' => $dk->id_smt, 'class' => 'text-center'],
				['data' => $dk->ips, 'class' => 'text-center'],
				['data' => $dk->sks_smt, 'class' => 'text-center'],
				['data' => $dk->ipk, 'class' => 'text-center'],
				['data' => $dk->sks_total, 'class' => 'text-center']
			]);
		}
		$tabel_akademik = $this->table->generate();
		$this->table->clear();

		// 5. KIP & Risk
		$kip_data = $this->Lecturer_persetujuan_krs_model->check_kip($nipd);
		$kip_status = "<span class='text-muted' style='font-size: 11.5px;'>Bukan Peserta KIP</span>";
		if ($kip_data) {
			$skema_raw = !empty($kip_data->skema) ? trim((string)$kip_data->skema) : '';
			$skema_lbl = !empty($skema_raw) ? 'KIP ' . $skema_raw : 'Peserta KIP';
			$kip_status = "<span class='badge' style='background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 600; padding: 2.5px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;'><i class='fa fa-id-card-o' style='color: #2563eb; font-size: 11px;'></i> " . htmlspecialchars($skema_lbl) . "</span>";
		}

		$masa_studi_text = "-";
		$risk_status = "<span class='label label-success'>Normal</span>";
		if (!empty($mahasiswa->tgl_masuk_sp)) {
			$tgl_masuk = new DateTime($mahasiswa->tgl_masuk_sp);
			$tgl_sekarang = new DateTime();
			$diff = $tgl_masuk->diff($tgl_sekarang);
			$masa_studi_text = $diff->y . " Tahun " . $diff->m . " Bulan";
			if ($diff->y > 4) $risk_status = "<span class='label label-danger'>Kritis (Masa Studi > 4 Tahun)</span>";
		}

		// 6. Chat History (Refactored)
		$formatted_log = $this->_format_chat_history($data_rencana_studi->catatan_dosen_wali, $user_first_name);

		// 7. Attention & Retake Tracking (Riwayat Nilai D/E dan Status Pengulangan)
		// Ambil seluruh mata kuliah yang pernah bernilai D atau E pada semester sebelum semester aktif
		$query_de = "SELECT sn.*, mk.nm_mk, mk.kode_mk, mk.sks_mk, kk.id_smt, kk.nm_kls 
                  FROM siakad_nilai_baru sn 
                  JOIN feeder_kelas_kuliah kk ON kk.id_kls = sn.id_kls 
                  JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk 
                  WHERE sn.nipd = ? 
                  AND kk.id_smt < ? 
                  AND (sn.nilai_huruf = 'D' OR sn.nilai_huruf = 'E') 
                  ORDER BY mk.nm_mk ASC, kk.id_smt DESC";
		$raw_de_records = $this->db->query($query_de, [$nipd, $id_smt])->result();

		// Ambil seluruh riwayat nilai mahasiswa untuk mengecek apakah sudah pernah diulang & lulus (hanya jika ada riwayat D/E)
		$grades_by_code = [];
		$grades_by_id_mk = [];
		if (!empty($raw_de_records)) {
			$all_grades = $this->db->select('sn.nilai_huruf, sn.nilai_angka, sn.nilai_indeks, kk.id_smt, kk.nm_kls, mk.kode_mk, mk.id_mk')
				->from('siakad_nilai_baru sn')
				->join('feeder_kelas_kuliah kk', 'kk.id_kls = sn.id_kls')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where('sn.nipd', $nipd)
				->order_by('kk.id_smt ASC')
				->get()->result();

			foreach ($all_grades as $ag) {
				$code = trim((string)($ag->kode_mk ?? ''));
				$grades_by_code[$code][] = $ag;
				if (!empty($ag->id_mk)) {
					$grades_by_id_mk[$ag->id_mk][] = $ag;
				}
			}
		}

		$krs_kode_mks = [];
		foreach ($krs_items as $item) {
			$krs_kode_mks[] = trim((string)($item->kode_mk ?? ''));
		}

		$passing_grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'];

		// Kelompokkan MK yang pernah D/E secara unik per kode_mk
		$de_courses = [];
		foreach ($raw_de_records as $row) {
			$code = trim((string)($row->kode_mk ?? ''));
			$de_courses[$code] ??= [
					'kode_mk' => $code,
					'id_mk' => $row->id_mk ?? '',
					'nm_mk' => $row->nm_mk,
					'sks_mk' => (int)$row->sks_mk,
					'initial_grades' => []
				];
			$de_courses[$code]['initial_grades'][] = [
				'id_smt' => $row->id_smt,
				'nm_kls' => $row->nm_kls,
				'nilai_huruf' => $row->nilai_huruf,
				'nilai_angka' => $row->nilai_angka,
				'nilai_indeks' => $row->nilai_indeks
			];
		}

		$processed_de = [];
		$e_count = 0;
		$d_count = 0;
		$passed_count = 0;
		$retaking_count = 0;
		$unresolved_count = 0;
		$retake_candidates = [];
		$has_unresolved_e = false;

		foreach ($de_courses as $code => $c) {
			$history_code = $grades_by_code[$code] ?? [];
			$history_id = (!empty($c['id_mk']) && isset($grades_by_id_mk[$c['id_mk']])) ? $grades_by_id_mk[$c['id_mk']] : [];
			$history = !empty($history_id) ? array_unique(array_merge($history_code, $history_id), SORT_REGULAR) : $history_code;
			$is_passed = false;
			$passed_record = null;
			$repeat_count = 0;

			foreach ($history as $h) {
				$letter = strtoupper(trim((string)$h->nilai_huruf));
				$indeks = (float)$h->nilai_indeks;
				$angka = (float)$h->nilai_angka;

				if (in_array($letter, $passing_grades) || $indeks >= 2.0 || $angka >= 56.0) {
					$is_passed = true;
					if (!$passed_record || $indeks > (float)$passed_record->nilai_indeks || ($indeks == (float)$passed_record->nilai_indeks && $h->id_smt > $passed_record->id_smt)) {
						$passed_record = $h;
					}
				}
				if (!empty($h->nilai_huruf)) {
					$repeat_count++;
				}
			}

			$is_currently_in_krs = in_array($code, $krs_kode_mks);

			// Render badge nilai asal D/E
			$initial_badges = [];
			$worst_fail_letter = 'D';
			foreach ($c['initial_grades'] as $ig) {
				$h_val = strtoupper(trim((string)$ig['nilai_huruf']));
				if ($h_val == 'E') {
					$worst_fail_letter = 'E';
					$e_count++;
				} elseif ($h_val == 'D') {
					$d_count++;
				}
				$badge_style = ($h_val == 'E') 
					? 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;' 
					: 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;';
				$initial_badges[] = '<span style="' . $badge_style . ' font-weight: 700; font-size: 10.5px; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;">Nilai ' . $h_val . '</span> <small style="color: #64748b; font-weight: 500;">(Smt ' . $ig['id_smt'] . ')</small>';
			}

			if ($is_passed) {
				$passed_count++;
				$status_html = '<span class="label" style="background: #10b981; color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-check-circle"></i> Sudah Diulang</span>';
				$nilai_lulus_html = '<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 800; font-size: 11.5px; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-check"></i> ' . $passed_record->nilai_huruf . '</span> <small style="color: #047857; font-weight: 600;">(Smt ' . $passed_record->id_smt . ')</small>';
			} elseif ($is_currently_in_krs) {
				$retaking_count++;
				$status_html = '<span class="label" style="background: #0284c7; color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-refresh"></i> Sedang Diulang</span>';
				$nilai_lulus_html = '<span style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-clock-o"></i> KRS Smt Ini</span>';
			} else {
				$unresolved_count++;
				if ($worst_fail_letter == 'E') {
					$has_unresolved_e = true;
				}
				$status_lbl = ($repeat_count > 1) ? 'Pernah Diulang (Belum Lulus)' : 'Belum Diulang';
				$bg_color = ($worst_fail_letter == 'E') ? '#dc2626' : '#ea580c';
				$status_html = '<span class="label" style="background: ' . $bg_color . '; color: #fff; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa fa-exclamation-triangle"></i> ' . $status_lbl . '</span>';
				$nilai_lulus_html = '<span class="text-muted" style="color: #94a3b8; font-size: 12px; font-weight: 600;">-</span>';
				
				// Hanya MK yang belum lulus dan belum diambil pada KRS ini yang menjadi kandidat rekomendasi mengulang
				$retake_candidates[] = [
					'kode_mk' => $code,
					'nm_mk' => $c['nm_mk'],
					'sks_mk' => $c['sks_mk'],
					'nilai_huruf' => $worst_fail_letter
				];
			}

			$processed_de[$code] = [
				'kode_mk' => $code,
				'nm_mk' => $c['nm_mk'],
				'sks_mk' => $c['sks_mk'],
				'initial_html' => implode('<br>', $initial_badges),
				'status_html' => $status_html,
				'nilai_lulus_html' => $nilai_lulus_html,
				'is_passed' => $is_passed,
				'is_currently_in_krs' => $is_currently_in_krs,
				'passed_record' => $passed_record
			];
		}

		// Bangun tampilan tabel Tab Nilai D/E
		$attention_html = "";
		if (!empty($processed_de)) {
			$attention_html = '<div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px;">';
			$attention_html .= '<div style="display: flex; align-items: center; gap: 6px; font-weight: 700; color: #1e293b; font-size: 12px;">';
			$attention_html .= '<i class="fa fa-history" style="color: #6366f1;"></i> Riwayat Mata Kuliah Nilai D / E (' . count($processed_de) . ' MK)';
			$attention_html .= '</div>';
			$attention_html .= '<div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">';
			if ($passed_count > 0) {
				$attention_html .= '<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-check-circle" style="color: #10b981;"></i> ' . $passed_count . ' Sudah Lulus Diulang</span>';
			}
			if ($retaking_count > 0) {
				$attention_html .= '<span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-refresh" style="color: #3b82f6;"></i> ' . $retaking_count . ' Sedang Diulang</span>';
			}
			if ($unresolved_count > 0) {
				$attention_html .= '<span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-exclamation-triangle" style="color: #f97316;"></i> ' . $unresolved_count . ' Belum Diulang</span>';
			}
			$attention_html .= '</div>';
			$attention_html .= '</div>';

			$attention_html .= '<div class="table-responsive" style="background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden;">';
			$attention_html .= '<table class="table table-bordered table-hover" style="margin-bottom: 0; font-size: 12px;">';
			$attention_html .= '<thead><tr style="background: #f8fafc; color: #334155; font-size: 11.5px; font-weight: 700;">';
			$attention_html .= '<th width="35" class="text-right" style="vertical-align: middle; padding: 8px 10px;">No</th>';
			$attention_html .= '<th width="95" class="text-center" style="vertical-align: middle; padding: 8px 10px;">Kode MK</th>';
			$attention_html .= '<th style="vertical-align: middle; padding: 8px 10px;">Mata Kuliah</th>';
			$attention_html .= '<th width="45" class="text-center" style="vertical-align: middle; padding: 8px 10px;">SKS</th>';
			$attention_html .= '<th width="125" class="text-center" style="vertical-align: middle; padding: 8px 10px;">Nilai Asal (D/E)</th>';
			$attention_html .= '<th width="140" class="text-center" style="vertical-align: middle; padding: 8px 10px;">Status</th>';
			$attention_html .= '<th width="140" class="text-center" style="vertical-align: middle; padding: 8px 10px;">Nilai Lulus / Akhir</th>';
			$attention_html .= '</tr></thead><tbody>';

			$idx = 1;
			foreach ($processed_de as $v) {
				$row_bg = $v['is_passed'] ? 'background-color: #fcfdfd;' : ($v['is_currently_in_krs'] ? 'background-color: #f8fafc;' : 'background-color: #fffbfa;');
				$attention_html .= '<tr style="' . $row_bg . '">';
				$attention_html .= '<td class="text-right" style="vertical-align: middle; padding: 8px 10px; color: #64748b;">' . $idx++ . '.</td>';
				$attention_html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px;"><span style="background: #f1f5f9; color: #334155; font-family: monospace; font-weight: 600; font-size: 11px; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">' . htmlspecialchars($v['kode_mk']) . '</span></td>';
				$attention_html .= '<td style="vertical-align: middle; padding: 8px 10px; font-weight: 600; color: #0f172a;">' . htmlspecialchars($v['nm_mk']) . '</td>';
				$attention_html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px; font-weight: 700; color: #0f172a;">' . (int)$v['sks_mk'] . '</td>';
				$attention_html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px;">' . $v['initial_html'] . '</td>';
				$attention_html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px;">' . $v['status_html'] . '</td>';
				$attention_html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px;">' . $v['nilai_lulus_html'] . '</td>';
				$attention_html .= '</tr>';
			}
			$attention_html .= '</tbody></table></div>';
		} else {
			$attention_html = "<div class='alert alert-success' style='padding: 12px; margin-bottom: 0; border-radius: 6px;'><i class='fa fa-check-circle'></i> Tidak ada riwayat Mata Kuliah dengan nilai D atau E.</div>";
		}

		// Check if any unresolved D/E subjects are currently offered in this active semester and NOT yet taken in current KRS
		$retake_offered_not_taken = [];
		if (!empty($retake_candidates)) {
			$all_offered_classes = $this->db->select('mk.kode_mk, mk.nm_mk, kk.nm_kls')
				->from('feeder_kelas_kuliah kk')
				->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
				->where('kk.id_smt', $id_smt)
				->get()->result();

			$offered_map = [];
			foreach ($all_offered_classes as $oc) {
				$code = trim((string)($oc->kode_mk ?? ''));
				$offered_map[$code] ??= [];
				$offered_map[$code][] = $oc->nm_kls;
			}

			foreach ($retake_candidates as $rc) {
				$code = trim($rc['kode_mk']);
				if (isset($offered_map[$code])) {
					$retake_offered_not_taken[] = [
						'kode_mk' => $rc['kode_mk'],
						'nm_mk' => $rc['nm_mk'],
						'sks_mk' => (int)$rc['sks_mk'],
						'nilai_huruf' => $rc['nilai_huruf'],
						'classes' => array_unique($offered_map[$code])
					];
				}
			}
		}

		$alert_retake = '';
		if (!empty($retake_offered_not_taken)) {
			$alert_retake = '<div style="margin-top: 15px; margin-bottom: 8px; background: #ffffff; border: 1px solid #fed7aa; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(249, 115, 22, 0.05);">';
			$alert_retake .= '<div style="background: linear-gradient(to right, #fff7ed, #ffedd5); border-bottom: 1px solid #fed7aa; padding: 8px 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">';
			$alert_retake .= '<div style="display: flex; align-items: center; gap: 8px;">';
			$alert_retake .= '<span style="background: #ea580c; color: #fff; width: 22px; height: 22px; border-radius: 5px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; box-shadow: 0 1px 3px rgba(234, 88, 12, 0.3);"><i class="fa fa-bell-o"></i></span>';
			$alert_retake .= '<span style="font-weight: 700; color: #9a3412; font-size: 12.5px;">Rekomendasi MK Mengulang (Nilai D / E)</span>';
			$alert_retake .= '</div>';
			$alert_retake .= '<span style="background: #ffffff; color: #c2410c; border: 1px solid #fed7aa; font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);"><i class="fa fa-calendar-check-o" style="color: #ea580c;"></i> ' . count($retake_offered_not_taken) . ' MK Tersedia di Semester Ini</span>';
			$alert_retake .= '</div>';
			
			$alert_retake .= '<div class="table-responsive" style="margin-bottom: 0;">';
			$alert_retake .= '<table class="table table-condensed table-hover" style="margin-bottom: 0; font-size: 12px;">';
			$alert_retake .= '<thead><tr style="background: #fafaf9; color: #57534e; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px;">';
			$alert_retake .= '<th width="35" class="text-right" style="padding: 7px 10px;">No</th>';
			$alert_retake .= '<th width="90" style="padding: 7px 10px;">Kode MK</th>';
			$alert_retake .= '<th style="padding: 7px 10px;">Mata Kuliah</th>';
			$alert_retake .= '<th width="50" class="text-center" style="padding: 7px 10px;">SKS</th>';
			$alert_retake .= '<th width="85" class="text-center" style="padding: 7px 10px;">Nilai</th>';
			$alert_retake .= '<th style="padding: 7px 10px;">Kelas Dibuka</th>';
			$alert_retake .= '</tr></thead><tbody>';
			
			$r_no = 1;
			foreach ($retake_offered_not_taken as $ro) {
				$badge_nilai = ($ro['nilai_huruf'] == 'E') 
					? '<span style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-weight: 700; font-size: 10.5px; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-times-circle" style="color: #dc2626; font-size: 10px;"></i> Nilai E</span>'
					: '<span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 700; font-size: 10.5px; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-exclamation-circle" style="color: #d97706; font-size: 10px;"></i> Nilai D</span>';
				
				$cls_badges = '';
				foreach ($ro['classes'] as $cl) {
					$cls_badges .= '<span style="background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; font-weight: 700; font-size: 10.5px; padding: 2px 7px; border-radius: 4px; margin-right: 4px; display: inline-flex; align-items: center; gap: 3px;"><i class="fa fa-users" style="font-size: 9px;"></i> ' . htmlspecialchars($cl) . '</span>';
				}

				$alert_retake .= '<tr>';
				$alert_retake .= '<td class="text-right" style="vertical-align: middle; padding: 7px 10px; color: #78716c;">' . $r_no++ . '.</td>';
				$alert_retake .= '<td style="vertical-align: middle; padding: 7px 10px;"><span style="background: #f1f5f9; color: #334155; font-family: monospace; font-weight: 600; font-size: 10.5px; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">' . htmlspecialchars($ro['kode_mk']) . '</span></td>';
				$alert_retake .= '<td style="vertical-align: middle; padding: 7px 10px; font-weight: 600; color: #0f172a;">' . htmlspecialchars($ro['nm_mk']) . '</td>';
				$alert_retake .= '<td class="text-center" style="vertical-align: middle; padding: 7px 10px; font-weight: 700; color: #0f172a;">' . (int)$ro['sks_mk'] . '</td>';
				$alert_retake .= '<td class="text-center" style="vertical-align: middle; padding: 7px 10px;">' . $badge_nilai . '</td>';
				$alert_retake .= '<td style="vertical-align: middle; padding: 7px 10px;">' . $cls_badges . '</td>';
				$alert_retake .= '</tr>';
			}
			$alert_retake .= '</tbody></table></div>';
			$alert_retake .= '</div>';
		}

		$is_draft_mode = ($data_rencana_studi->id_status_krs == 1 || $data_rencana_studi->id_status_krs == 0 || strtolower($data_rencana_studi->nm_status_krs ?? '') == 'daftar');
		$alert_draft = '';
		if ($is_draft_mode) {
			$wa_draft_btn = '';
			if (!empty($data_rencana_studi->registrasi_no_hp)) {
				$clean_hp = preg_replace('/[^0-9]/', '', $data_rencana_studi->registrasi_no_hp);
				if (str_starts_with($clean_hp, '0')) $clean_hp = '62' . substr($clean_hp, 1);
				$wa_msg = rawurlencode("Halo " . $mahasiswa->nm_pd . " (" . $nipd . "), kami melihat rencana studi Anda sudah diisi (" . $total_sks . " SKS) tetapi belum di-Submit (masih status Draft). Mohon segera login ke Portal Mahasiswa dan klik tombol 'Submit KRS' agar dapat kami verifikasi dan setujui. Terima kasih.");
				$wa_draft_btn = '<div style="margin-top: 8px;"><a href="https://wa.me/' . $clean_hp . '?text=' . $wa_msg . '" target="_blank" class="btn btn-xs" style="background: #10b981; color: #fff; font-weight: 600; padding: 4px 10px; border-radius: 4px; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;"><i class="fa fa-whatsapp"></i> Ingatkan Mahasiswa via WhatsApp</a></div>';
			}
			$alert_draft = '<div style="background: #fff7ed; border: 1px solid #fdba74; border-radius: 8px; padding: 12px 14px; margin-bottom: 14px; display: flex; align-items: flex-start; gap: 10px; box-shadow: 0 1px 3px rgba(234, 88, 12, 0.08);">
				<span style="background: #ea580c; color: #fff; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(234,88,12,0.25);"><i class="fa fa-exclamation-triangle"></i></span>
				<div style="flex: 1;">
					<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
						<strong style="color: #9a3412; font-size: 13px;">Perhatian: Rencana Studi Masih Berstatus DRAFT (Belum Di-Submit)</strong>
						<span class="label" style="background-color: #ea580c; color: #fff; font-weight: 700; padding: 2px 7px; border-radius: 3px; font-size: 10px;">BELUM SUBMIT</span>
					</div>
					<div style="color: #7c2d12; font-size: 11.5px; line-height: 1.45; margin-top: 4px;">
						Mahasiswa telah memilih <strong>' . count($krs_items) . ' mata kuliah (' . $total_sks . ' SKS)</strong>, namun <b>belum menekan tombol Submit KRS</b> di portal mahasiswa. Pengajuan ini belum masuk antrean persetujuan resmi. Mohon ingatkan mahasiswa agar segera melakukan Submit.
					</div>
					' . $wa_draft_btn . '
				</div>
			</div>';
		}

		$tabel_krs = $alert_draft . $banner_match . $tabel_krs_raw . $alert_gap . $alert_not_taken . $alert_pilihan . $alert_retake;

		// 8. Academic Narrative Summary
		$jml_judul_ta = $this->db->where('nipd', $nipd)->count_all_results('siakad_skripsi_pengajuan_judul');
		$semester_tempuh = count($data_kuliah);
		$de_summary = [
			'total_count' => count($processed_de),
			'unresolved_count' => $unresolved_count,
			'passed_count' => $passed_count,
			'retaking_count' => $retaking_count,
			'has_e' => $has_unresolved_e
		];
		$gap_summary = [
			'missing_smts' => $missing_smts,
			'missing_labels' => $missing_labels,
			'riil_count' => count($actual_recorded_smts),
			'cohort_semester' => $cohort_semester,
			'start_year' => $start_year
		];
		$academic_summary = $this->_generate_academic_narrative($mahasiswa, $data_kuliah, $ips_values, $de_summary, $kip_data, $total_sks, count($krs_items), $jml_judul_ta, $semester_tempuh, $gap_summary);

		// 9. Deadline Check
		$setting = $this->db->get('siakad_setting')->row();
		$tgl_akhir_krs = $setting->tgl_akhir_krs;
		$is_deadline_passed = (strtotime($tgl_akhir_krs) < strtotime(date('Y-m-d H:i:s')));

		echo json_encode([
			'tabel_krs' => $tabel_krs,
			'tabel_akademik_record' => $tabel_akademik,
			'nm_pd' => $mahasiswa->nm_pd,
			'nipd' => $nipd,
			'id_smt' => $id_smt,
			'catatan_dosen_wali' => $data_rencana_studi->catatan_dosen_wali,
			'id_status_krs' => $data_rencana_studi->id_status_krs,
			'log_message' => $formatted_log,
			'pic' => $pic,
			'attention' => $attention_html,
			'ips_labels' => $ips_labels,
			'ips_values' => $ips_values,
			'ipk_values' => $ipk_values,
			'kip_status' => $kip_status,
			'risk_status' => $risk_status,
			'semester_tempuh' => count($data_kuliah),
			'masa_studi_text' => $masa_studi_text,
			'last_ipk' => number_format($last_ipk, 2),
			'star_rating_html' => $this->_get_star_rating_html($last_ipk, false),
			'has_perwalian' => (stripos($data_rencana_studi->catatan_dosen_wali, '[PERWALIAN]') !== false),
			'failed_count' => count($processed_de),
			'total_de_count' => count($processed_de),
			'unresolved_de_count' => $unresolved_count,
			'passed_de_count' => $passed_count,
			'retaking_de_count' => $retaking_count,
			'e_count' => $e_count,
			'd_count' => $d_count,
			'krs_review' => $krs_review,
			'academic_summary' => $academic_summary,
			'is_deadline_passed' => $is_deadline_passed,
			'is_draft' => $is_draft_mode
		]);
	}

	private function _generate_academic_narrative($mahasiswa, $data_kuliah, $ips_values, $attention, $kip_data, $total_sks, $total_mk, $jml_judul_ta = 0, $semester_tempuh = 0, $gap_summary = [])
	{
		$nm_pd = $mahasiswa->nm_pd;
		$prodi = $mahasiswa->nm_prodi ?? 'Program Studi';
		$angkatan = !empty($mahasiswa->tgl_masuk_sp) ? date('Y', strtotime($mahasiswa->tgl_masuk_sp)) : '-';

		$last_ips = end($ips_values) ?: 0;
		$last_ipk = !empty($data_kuliah) ? end($data_kuliah)->ipk : 0;
		$is_maba = ($semester_tempuh == 0 || empty($data_kuliah) || count($ips_values) == 0);

		// 1. Perkembangan Akademik & Tren IPS
		$intro = "Mahasiswa atas nama <strong>$nm_pd</strong>, Program Studi $prodi angkatan $angkatan, ";
		if ($is_maba) {
			$intro .= "merupakan <strong>Mahasiswa Baru</strong> yang sedang memulai perkuliahan perdana (Semester 1). ";
			$trend = "Diharapkan dapat segera beradaptasi dengan atmosfer dan metode pembelajaran di perguruan tinggi secara optimal.";
		} elseif (count($ips_values) > 1) {
			$first_ips = $ips_values[0];
			if ($last_ips >= $first_ips) {
				$trend = "menunjukkan perkembangan akademik yang cukup baik selama masa studi berjalan. Berdasarkan evaluasi capaian semester, mahasiswa mengalami peningkatan atau stabilitas IPS secara bertahap.";
			} else {
				$trend = "menunjukkan performa akademik yang fluktuatif. Diperlukan perhatian khusus untuk menjaga konsistensi capaian semester agar tidak mengalami penurunan berkelanjutan.";
			}
		} else {
			$trend = "saat ini sedang menempuh semester awal dengan capaian yang perlu terus dipantau dan dikembangkan.";
		}

		// 2. Status IPK (Emphasis on min IPK)
		if ($is_maba) {
			$ipk_text = "Sebagai mahasiswa baru, belum terdapat riwayat Indeks Prestasi Kumulatif (IPK). Mahasiswa diarahkan untuk membangun rekam jejak akademik yang unggul sejak semester pertama.";
		} else {
			$min_ipk = ($semester_tempuh < 2) ? 2.50 : 2.75;
			$ipk_val = number_format($last_ipk, 2);
			$ipk_stress = ($last_ipk < $min_ipk) ? "style='color: #e74c3c; font-weight: bold; font-size: 1.1em;'" : "";
			
			$potensi_text = "";
			if ($semester_tempuh >= 6 && $jml_judul_ta > 0) {
				$potensi_text = ", serta <strong>berpotensi lulus tepat waktu</strong>";
			}
			
			if ($last_ipk >= 3.5) {
				$ipk_status = "berada pada kategori <strong>Sangat Memuaskan</strong> <span style='color:#1cc88a;'><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i><i class='fa fa-star'></i></span> dengan nilai <span $ipk_stress>$ipk_val</span>. Capaian tersebut menunjukkan kemampuan akademik yang sangat tinggi dan konsisten{$potensi_text}.";
			} elseif ($last_ipk >= 3.0) {
				$ipk_status = "berada pada kategori <strong>Baik</strong> dengan nilai <span $ipk_stress>$ipk_val</span>. Capaian tersebut menunjukkan kemampuan akademik yang stabil terhadap beban studi yang ditempuh.";
			} else {
				$cat = ($last_ipk < $min_ipk) ? "<strong>Kurang (Perlu Perhatian Khusus)</strong>" : "Cukup";
				$ipk_status = "saat ini berada pada kategori $cat dengan nilai <span $ipk_stress>$ipk_val</span>. " . 
							  (($last_ipk < $min_ipk) ? "Capaian ini berada di bawah standar minimal " . number_format($min_ipk, 2) . ", sehingga diperlukan bimbingan akademik yang lebih intensif dari dosen wali." : 
							  "Diperlukan strategi belajar yang lebih fokus untuk meningkatkan index prestasi pada semester-semester mendatang.");
			}
			$ipk_text = "Secara keseluruhan, IPK mahasiswa saat ini $ipk_status";
		}

		// 3. Analisis Pengambilan KRS
		$krs_text = "Pada semester ini, mahasiswa mengajukan sebanyak <strong>$total_mk Mata Kuliah</strong> dengan total beban <strong>$total_sks SKS</strong>. Seluruh mata kuliah telah sesuai dengan struktur kurikulum Program Studi.";

		// 4. Warning & Mata Kuliah Perhatian (E grades retake suggestion)
		if (is_array($attention)) {
			$unresolved_count = $attention['unresolved_count'] ?? 0;
			$passed_count = $attention['passed_count'] ?? 0;
			$retaking_count = $attention['retaking_count'] ?? 0;
			$total_de_count = $attention['total_count'] ?? 0;
			$has_e = !empty($attention['has_e']);
		} else {
			$unresolved_count = is_object($attention) ? $attention->num_rows() : 0;
			$passed_count = 0;
			$retaking_count = 0;
			$total_de_count = $unresolved_count;
			$has_e = false;
			if (is_object($attention)) {
				foreach ($attention->result() as $v) { if ($v->nilai_huruf == 'E') $has_e = true; }
			}
		}
		
		$warning_text = "";
		if ($is_maba) {
			$warning_text = " Seluruh mata kuliah merupakan paket semester 1 tanpa adanya riwayat mata kuliah mengulang.";
		} elseif ($unresolved_count > 0) {
			$warning_text = " Namun demikian, tercatat masih terdapat <strong>$unresolved_count mata kuliah (D/E)</strong> yang belum lulus dan memerlukan perhatian. ";
			if ($has_e) {
				$warning_text .= "Secara khusus, terdapat mata kuliah dengan <strong>Nilai E (Belum Lulus)</strong> yang sangat disarankan untuk segera diprogramkan kembali (mengulang) guna menghindari hambatan pada progres kelulusan.";
			} else {
				$warning_text .= "Disarankan untuk mempertimbangkan pengulangan mata kuliah dengan nilai D tersebut di masa mendatang agar dapat memperbaiki IPK akumulatif.";
			}
			if ($passed_count > 0) {
				$warning_text .= " Sebanyak $passed_count mata kuliah yang pernah bernilai D/E telah berhasil diperbaiki dan lulus.";
			}
			if ($retaking_count > 0) {
				$warning_text .= " Sebanyak $retaking_count mata kuliah saat ini sedang ditempuh ulang pada KRS semester ini.";
			}
		} elseif ($total_de_count > 0 && $unresolved_count == 0) {
			$msg_detail = [];
			if ($passed_count > 0) $msg_detail[] = "$passed_count MK telah berhasil lulus diulang";
			if ($retaking_count > 0) $msg_detail[] = "$retaking_count MK sedang ditempuh ulang pada semester ini";
			$warning_text = " Seluruh riwayat mata kuliah yang pernah bernilai D/E (" . implode(', ', $msg_detail) . ") telah tertangani dengan baik tanpa adanya tunggakan nilai kritis.";
		} else {
			$warning_text = " Sejauh ini, rekam jejak pengambilan mata kuliah menunjukkan kepatuhan yang baik terhadap kurikulum tanpa adanya tunggakan nilai kritis.";
		}

		// 5. Catatan Riwayat Perkuliahan / Semester Terlewat (Tidak Aktif / Cuti)
		$gap_text = "";
		if (!empty($gap_summary['missing_labels'])) {
			$missing_str = implode(', ', $gap_summary['missing_labels']);
			$gap_text = "<p style='background: #fffbeb; border-left: 4px solid #f59e0b; padding: 9px 13px; border-radius: 4px; color: #92400e; margin: 12px 0; line-height: 1.6;'>"
				. "<strong><i class='fa fa-info-circle' style='color: #d97706;'></i> Catatan Riwayat Perkuliahan:</strong> "
				. "Mahasiswa terdeteksi tidak mengikuti perkuliahan pada <strong>$missing_str</strong> <em>(kemungkinan berstatus tidak aktif atau cuti akademik)</em>. "
				. "Masa studi tempuh riil saat ini tercatat <strong>{$gap_summary['riil_count']} semester</strong>, dan mata kuliah yang diambil pada semester ini telah disesuaikan mengikuti paket <strong>Semester {$gap_summary['cohort_semester']}</strong> bersama rekan seangkatannya (Angkatan {$gap_summary['start_year']})."
				. "</p>";
		}

		// 6. Status KIP
		$kip_text = "";
		if ($kip_data) {
			$kip_text = "Sebagai penerima program <strong>KIP</strong>, mahasiswa diharapkan menjaga keaktifan dan performa akademik agar pembiayaan beasiswa tetap berlanjut.";
		}

		// 7. Target Kedepan
		if ($is_maba) {
			$target_ips = "3.25";
			$recommendation = "Diharapkan pada semester pertama ini, mahasiswa dapat mencapai target <strong>IPS minimal $target_ips</strong> sebagai langkah awal meraih predikat kelulusan terbaik.";
		} else {
			$target_ips = ($last_ipk < 3.0) ? "3.20" : "3.50";
			$recommendation = "Diharapkan pada semester ini, mahasiswa dapat mencapai target <strong>IPS minimal $target_ips</strong> untuk terus memperbaiki atau mempertahankan rekam akademik yang kompetitif.";
		}

		return "<p>$intro $trend</p><p>$ipk_text</p><p>$krs_text $warning_text</p>$gap_text<p>$kip_text $recommendation</p>";
	}


	public function ajax_view_foto($nipd)
	{
		$data = $this->db->where('mp.nipd', $nipd)
			->select('m.nm_pd, mp.nipd, foto')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->get()->row();
		echo json_encode($data);
	}


	public function persetujuan_krs()
	{
		//$this->_validate();
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$this->db->trans_start();
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();
		$id_reg_pd = $this->db->where(['nipd' => $data_krs->nipd])->get('mahasiswa_pt')->row()->nipd;

		//$this->db->trans_begin();
		//$this->db->where('id_rencana_studi', $id_rencana_studi)->delete('siakad_krs_detail');

		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';

		$id_status_krs = (int)$this->input->post('id_status_krs');
		$pesan_input = $this->input->post('catatan_dosen_wali', TRUE);
		$pesan_input = trim($pesan_input ?? '');

		if ($id_status_krs == 3) {
			// 1. Status Final (Disetujui) -> Wajib minimal 5 kata
			$words = !empty($pesan_input) ? preg_split('/\s+/', $pesan_input, -1, PREG_SPLIT_NO_EMPTY) : [];
			if (count($words) < 5) {
				$count_text = (count($words) === 0) ? 'masih kosong' : count($words) . ' kata terdeteksi';
				echo json_encode([
					'status' => FALSE,
					'message' => 'Catatan perwalian pada status Final (Disetujui) wajib diisi minimal 5 kata (' . $count_text . '). Mohon berikan arahan atau klik tombol Saran Perwalian.'
				]);
				return;
			}
		} else if ($id_status_krs == 1 || $id_status_krs == 4) {
			// 2. Status Ditolak (4) atau Kesempatan Edit (1) -> Wajib diisi (tidak boleh kosong)
			if (empty($pesan_input)) {
				$label = ($id_status_krs == 4) ? 'Alasan penolakan pengajuan KRS' : 'Catatan arahan perbaikan KRS';
				echo json_encode([
					'status' => FALSE,
					'message' => $label . ' wajib diisi untuk mahasiswa.'
				]);
				return;
			}
		}

		$safe_pesan = nl2br(html_escape($pesan_input));
		$new_message = '<div class="chat-bubble chat-right"><span class="chat-name">Me (' . $user->first_name . ')</span>' . $safe_pesan . '</div>';
		if (!empty($catatan_dosen_wali_awal)) {
			$catatan_dosen_wali = $catatan_dosen_wali_awal . $new_message;
		} else {
			$catatan_dosen_wali = $new_message;
		}

		// Automated Status Narratives
		$id_status_krs = $this->input->post('id_status_krs');
		$auto_narasi = "";
		$now = date('d/m/Y H:i');

		if ($id_status_krs == 3) {
			$auto_narasi = "Setelah melakukan perwalian dan review terhadap mata kuliah yang diambil mahasiswa, dengan ini pengajuan KRS disetujui pada tanggal " . $now . " WIB.";
		} else if ($id_status_krs == 1) {
			$auto_narasi = "Berdasarkan hasil perwalian dan review, pengajuan KRS Anda dikembalikan (Kesempatan Edit) untuk dilakukan perbaikan sesuai catatan yang diberikan.";
		} else if ($id_status_krs == 4) {
			$auto_narasi = "Berdasarkan hasil review perwalian, pengajuan KRS Anda ditolak. Silakan hubungi dosen wali untuk informasi lebih lanjut.";
		}

		if (!empty($auto_narasi)) {
			$system_bubble = '<div class="chat-bubble chat-left" style="background-color: #f0f2f5; border: 1px solid #dcdfe6; color: #555;"><span class="chat-name" style="color: #888;">System Auto-Notification</span>' . $auto_narasi . '</div>';
			$catatan_dosen_wali .= $system_bubble;
		}


		if ($this->input->post('id_status_krs') == 3) {

			$this->db->where('id_rencana_studi', $id_rencana_studi)->update(
				'siakad_krs',
				[
					'id_status_krs' => $this->input->post('id_status_krs'),
					'catatan_dosen_wali' => $catatan_dosen_wali,
					'approved_by' => $user->id,
					'approved_date' => date('Y-m-d H:i:s'),
					'change_by' => $user->id,
					'change_date' => date('Y-m-d H:i:s')
				]
			);

			$krs_detail = $this->db->where(['id_rencana_studi' => $id_rencana_studi])->get('siakad_krs_detail')->result();
			$data_nilai = [];

			foreach ($krs_detail as $key => $value) {
				$data_nilai = $this->db->where(['nipd' => $data_krs->nipd, 'id_kls' => $value->id_kls])->get('siakad_nilai_baru')->num_rows();

				if ($data_nilai == 0) {
					$this->db->insert('siakad_nilai_baru', [
						'nipd' => $data_krs->nipd,
						'id_kls' => $value->id_kls,
						'id_reg_pd' => $id_reg_pd,
						'createby' => $user->id,
						'createdate' => date('Y-m-d H:i:s')
					]);
				}
			}
		} else {
			$update_data = [
				'id_status_krs' => $this->input->post('id_status_krs'),
				'catatan_dosen_wali' => $catatan_dosen_wali,
				'change_by' => $user->id,
				'change_date' => date('Y-m-d H:i:s')
			];
			if ($this->input->post('id_status_krs') == 4) {
				$update_data['approved_by'] = $user->id;
				$update_data['approved_date'] = date('Y-m-d H:i:s');
			}
			$this->db->where('id_rencana_studi', $id_rencana_studi)->update('siakad_krs', $update_data);
		}

		$this->db->trans_complete();
		

		$id_ptk = $this->session->userdata('id_ptk');
		$summary = $this->Lecturer_persetujuan_krs_model->get_summary_stats($id_ptk);
		
		echo json_encode(["status" => TRUE, "summary" => $summary]);
	}

	public function ajax_perbaikan_krs($id_rencana_studi)
	{
		$user = $this->ion_auth->user()->row();
		$alasan = $this->input->post('alasan', TRUE);
		$now = date('d/m/Y H:i');

		// 1. Get current data for log appending
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();
		$catatan_awal = $data_krs->catatan_dosen_wali;

		// 2. Prepare Auto Notification
		$notif_text = "<b>[SYSTEM] Buka Perbaikan KRS</b><br>";
		$notif_text .= "Tanggal: $now<br>";
		$notif_text .= "Oleh: " . $user->first_name . "<br>";
		$notif_text .= "Alasan: " . html_escape($alasan);

		$new_log = '<div class="chat-bubble chat-left" style="background-color: #fff1f0; border: 1px solid #ffa39e;">' . $notif_text . '</div>';

		if (!empty($catatan_awal)) {
			$catatan_dosen_wali = $catatan_awal . $new_log;
		} else {
			$catatan_dosen_wali = $new_log;
		}

		// 3. Update Status and Log
		$data = [
			'id_status_krs' => 1,
			'catatan_dosen_wali' => $catatan_dosen_wali,
			'change_by' => $user->id,
			'change_date' => date('Y-m-d H:i:s')
		];

		$this->Lecturer_persetujuan_krs_model->update(['id_rencana_studi' => $id_rencana_studi], $data);
		

		$id_ptk = $this->session->userdata('id_ptk');
		$summary = $this->Lecturer_persetujuan_krs_model->get_summary_stats($id_ptk);
		
		echo json_encode(["status" => TRUE, "summary" => $summary]);
	}

	public function preview_pdf($id_rencana_studi)
	{
		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();
		$data_rencana_studi_detail = $this->db->where(compact('id_rencana_studi'))
			->select('mk.*, kk.nm_kls')->from('siakad_krs_detail kd')
			->join('feeder_kelas_kuliah kk', 'kd.id_kls = kk.id_kls')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->get()->result();

		$data_mahasiswa = $this->db->where('nipd', $data_rencana_studi->nipd)->select('*')->from('mahasiswa m')->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')->get()->row();

		$where_km = "nipd = " . $this->db->escape($data_mahasiswa->nipd);
		if (!empty($data_mahasiswa->id_reg_pd_feeder)) {
			$where_km .= " OR id_reg_pd = " . $this->db->escape($data_mahasiswa->id_reg_pd_feeder);
		}
		$data_kuliah_mahasiswa = $this->db->where($where_km, NULL, FALSE)->order_by('id_smt', 'ASC')->get('kuliah_mahasiswa')->result();

		// Update logic to only show failed subjects from PAST semesters that haven't been passed yet
		$id_smt_aktif = $data_rencana_studi->id_smt;
		$attention_query = "
			SELECT sn.*, mk.nm_mk, mk.kode_mk, mk.sks_mk 
			FROM siakad_nilai_baru sn
			JOIN feeder_kelas_kuliah kk ON kk.id_kls = sn.id_kls
			JOIN feeder_mata_kuliah mk ON mk.id_mk = kk.id_mk
			WHERE sn.nipd = ? 
			AND kk.id_smt < ?
			AND sn.nilai_angka < 56
			AND NOT EXISTS (
				SELECT 1 
				FROM siakad_nilai_baru sn2
				JOIN feeder_kelas_kuliah kk2 ON kk2.id_kls = sn2.id_kls
				JOIN feeder_mata_kuliah mk2 ON mk2.id_mk = kk2.id_mk
				WHERE sn2.nipd = sn.nipd 
				AND mk2.kode_mk = mk.kode_mk 
				AND sn2.nilai_angka >= 56
			)
			ORDER BY mk.nm_mk ASC
		";
		$attention = $this->db->query($attention_query, [$data_mahasiswa->nipd, $id_smt_aktif]);

		$tabel = "<table class='table table-bordered'>
					<thead>
						<tr>
							<th>No</th>
							<th>Kode MK</th>
							<th>Mata Kuliah</th>
							<th>SKS</th>
							<th>Nilai</th>
						</tr>
					</thead>
					<tbody>";
		$tampil = 0;
		if ($attention->num_rows()) {
			$tampil = 1;
			$i = 1;
			foreach ($attention->result() as $value) {
				$color = ($value->nilai_huruf == 'E') ? 'red' : '#d35400';
				$tabel .= "<tr>
							<td>$i</td>
							<td>$value->kode_mk</td>
							<td>$value->nm_mk</td>
							<td>$value->sks_mk</td>
							<td><span style='color: $color; font-weight: bold;'>$value->nilai_huruf</span></td>
						</tr>";
				$i++;
			}
		}

		$tabel .= "</tbody></table>";

		$id_sms = $data_mahasiswa->id_sms;
		$nm_lemb = $this->db->where('id_sms', $id_sms)->get('sms')->row()->nm_lemb;
		$id_smt = $data_rencana_studi->id_smt;
		$nm_semester = $this->db->where(compact('id_smt'))->get('semester')->row()->nm_smt;

		$m_dosen_wali = $data_mahasiswa->dosen_pa;
		$dosen_wali = $this->db->where(['id_ptk' => $m_dosen_wali])->get('siakad_dosen');
		$dsn_row = $dosen_wali ? $dosen_wali->row() : null;
		$output['nm_sdm'] = !empty($dsn_row->nm_ptk) ? $dsn_row->nm_ptk : '';
		$output['nidn_sdm'] = !empty($dsn_row->nidn) ? $dsn_row->nidn : (!empty($dsn_row->nip) ? $dsn_row->nip : '-');
		$output['tipe_file'] = 1;
		$output['data_rencana_studi'] = $data_rencana_studi;
		$output['data_rencana_studi_detail'] = $data_rencana_studi_detail;
		$output['data_mahasiswa'] = $data_mahasiswa;
		$output['data_kuliah_mahasiswa'] = $data_kuliah_mahasiswa;
		$output['nm_lemb'] = $nm_lemb;
		$output['nm_semester'] = $nm_semester;
		$output['nilai_perhatian'] = $tabel;
		$output['tampil'] = $tampil;
		$output['foto'] = $data_mahasiswa->foto;
		$this->load->view('pdf/pdf_pengajuan_krs', $output);
	}

	public function ajax_balas_pesan()
	{
		$user = $this->ion_auth->user()->row();
		$id_rencana_studi = $this->input->post('id_rencana_studi');
		$data_krs = $this->db->where('id_rencana_studi', $id_rencana_studi)->get('siakad_krs')->row();

		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';


		$catatan_dosen_wali_awal = $data_krs->catatan_dosen_wali;
		$catatan_dosen_wali = '';

		$pesan_input = $this->input->post('catatan_dosen_wali', TRUE);
		$pesan_input = trim($pesan_input);

		if (!empty($pesan_input)) {
			// Double protection: xss_clean (above) + html_escape (below) + nl2br for list support
			$safe_pesan = nl2br(html_escape($pesan_input));
			$new_message = '<div class="chat-bubble chat-right"><span class="chat-name">Me (' . $user->first_name . ')</span>' . $safe_pesan . '</div>';
			if (!empty($catatan_dosen_wali_awal)) {
				$catatan_dosen_wali = $catatan_dosen_wali_awal . $new_message;
			} else {
				$catatan_dosen_wali = $new_message;
			}
		} else {
			$catatan_dosen_wali = $catatan_dosen_wali_awal;
		}

		$data = [
			'catatan_dosen_wali' => $catatan_dosen_wali
		];

		$this->Lecturer_persetujuan_krs_model->update(['id_rencana_studi' => $this->input->post('id_rencana_studi')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function view_pesan($id_rencana_studi, $nipd, $id_smt)
	{
		$user = $this->ion_auth->user()->row();
		$user_first_name = $user->first_name;

		// 1. Basic Student Info
		$mahasiswa = $this->db->where('nipd', $nipd)
			->select('m.nm_pd, mp.nipd, foto, mp.tgl_masuk_sp, mp.id_sms')
			->from('mahasiswa m')
			->join('mahasiswa_pt mp', 'm.id_pd = mp.id_pd')
			->get()->row();

		if ($mahasiswa->foto) {
			$pic = '<img src="https://files.wbi.ac.id/siakad_file_mahasiswa/thumbs/thumb_' . $mahasiswa->foto . '" alt="foto_profil" height="150" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 1px solid #ddd;">';
		} else {
			$pic = '<img src="' . base_url('assets/images.png') . '" alt="foto_profil" height="120" style="border-radius: 8px; border: 1px solid #ddd;">';
		}

		$data_rencana_studi = $this->db->where(compact('id_rencana_studi'))->get('siakad_krs')->row();

		// 2. Academic Summary Info
		$data_kuliah = $this->db->where('nipd', $nipd)->order_by('id_smt')->get('kuliah_mahasiswa')->result();
		$last_ipk = !empty($data_kuliah) ? (float)end($data_kuliah)->ipk : 0;

		// 3. KIP & Risk
		$kip_data = $this->Lecturer_persetujuan_krs_model->check_kip($nipd);
		$kip_status = "<span class='text-muted' style='font-size: 11.5px;'>-</span>";
		if ($kip_data) {
			$skema_raw = !empty($kip_data->skema) ? trim((string)$kip_data->skema) : '';
			$skema_lbl = !empty($skema_raw) ? 'KIP ' . $skema_raw : 'Peserta KIP';
			$kip_status = "<span class='badge' style='background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 600; padding: 2.5px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;'><i class='fa fa-id-card-o' style='color: #2563eb; font-size: 11px;'></i> " . htmlspecialchars($skema_lbl) . "</span>";
		}

		$masa_studi_text = "-";
		if (!empty($mahasiswa->tgl_masuk_sp)) {
			$tgl_masuk = new DateTime($mahasiswa->tgl_masuk_sp);
			$tgl_sekarang = new DateTime();
			$diff = $tgl_masuk->diff($tgl_sekarang);
			$masa_studi_text = $diff->y . " Tahun " . $diff->m . " Bulan";
		}

		// 4. Chat History (Refactored)
		$formatted_log = $this->_format_chat_history($data_rencana_studi->catatan_dosen_wali, $user_first_name);

		echo json_encode([
			'id_status_krs' => $data_rencana_studi->id_status_krs,
			'data_pesan' => $formatted_log,
			'nm_pd' => $mahasiswa->nm_pd,
			'nipd' => $nipd,
			'pic' => $pic,
			'last_ipk' => number_format($last_ipk, 2),
			'semester_tempuh' => count($data_kuliah),
			'masa_studi_text' => $masa_studi_text,
			'kip_status' => $kip_status
		]);
	}

	private function _format_chat_history($raw_log, $user_first_name)
	{
		$formatted_log = '';
		if (empty($raw_log)) return '';

		$bubbles = [];
		$temp_log = preg_replace_callback('/<div class="chat-bubble.*?<\/div>/is', function ($m) use (&$bubbles) {
			$bubbles[] = $m[0];
			return '[[BUBBLE_' . (count($bubbles) - 1) . ']]';
		}, $raw_log);

		$segments = preg_split('/(\[\[BUBBLE_\d+\]\])/i', $temp_log, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($segments as $seg) {
			$seg = trim($seg);
			if (empty($seg)) continue;
			if (preg_match('/\[\[BUBBLE_\d+\]\]/i', $seg)) {
				$formatted_log .= $seg;
				continue;
			}
			$old_messages = preg_split('/(<br>|<br\s*\/>|(?=<b>))/i', $seg, -1, PREG_SPLIT_NO_EMPTY);
			foreach ($old_messages as $msg) {
				$msg = trim($msg);
				if (empty($msg) || $msg == '<br>' || $msg == '<br/>') continue;
				if (preg_match('/<b>(.*?)<\/b>\s*:\s*(.*)/is', $msg, $matches)) {
					$name = strip_tags($matches[1]);
					$is_me = (stripos($name, (string) $user_first_name) !== false);
					$class = $is_me ? 'chat-right' : 'chat-left';
					$formatted_log .= '<div class="chat-bubble ' . $class . '"><span class="chat-name">' . ($is_me ? 'Me (' . $name . ')' : $name) . '</span>' . $matches[2] . '</div>';
				} else {
					$formatted_log .= '<div class="chat-bubble chat-left">' . $msg . '</div>';
				}
			}
		}
		foreach ($bubbles as $i => $b) {
			$formatted_log = str_replace('[[BUBBLE_' . $i . ']]', $b, $formatted_log);
		}
		return $formatted_log;
	}

	public function show_makul_prodi($id_sms = null)
	{
		$id_ptk = $this->session->userdata('id_ptk');
		$id_smt = $this->session->userdata('tahun_ajaran');

		// 1. Get all available prodis offering courses in active semester
		$prodis = $this->db->select('DISTINCT(kk.id_sms), ps.nm_lemb')
			->from('feeder_kelas_kuliah kk')
			->join('sms ps', 'ps.id_sms = kk.id_sms')
			->where('kk.id_smt', $id_smt)
			->order_by('ps.nm_lemb', 'ASC')
			->get()->result();

		// 2. Default prodi if not specified: based on lecturer's advisees
		if (empty($id_sms)) {
			$default_prodi = $this->db->select('mp.id_sms')
				->from('mahasiswa_pt mp')
				->join('mahasiswa m', 'm.id_pd = mp.id_pd')
				->where('m.dosen_pa', $id_ptk)
				->where('mp.id_jns_keluar IS NULL')
				->limit(1)->get()->row();
			$id_sms = $default_prodi ? $default_prodi->id_sms : ($prodis[0]->id_sms ?? '');
		}

		// 3. Get classes for selected prodi with kurikulum wajib status
		$classes = $this->db->select('kk.id_kls, kk.nm_kls, SUBSTR(kk.nm_kls, 2, 1) as smt, mk.kode_mk, mk.nm_mk, mk.sks_mk, mk.sks_tm, mk.sks_prak, mk.sks_prak_lap, COALESCE(mkk.a_wajib, 1) as a_wajib')
			->from('feeder_kelas_kuliah kk')
			->join('feeder_mata_kuliah mk', 'mk.id_mk = kk.id_mk')
			->join('(SELECT id_mk, MAX(a_wajib) as a_wajib FROM feeder_mata_kuliah_kurikulum GROUP BY id_mk) mkk', 'mkk.id_mk = mk.id_mk', 'left')
			->where('kk.id_smt', $id_smt)
			->where('kk.id_sms', $id_sms)
			->order_by('smt ASC, mk.nm_mk ASC, kk.nm_kls ASC')
			->get()->result();

		$current_prodi_name = '';
		foreach ($prodis as $p) {
			if ($p->id_sms == $id_sms) {
				$current_prodi_name = $p->nm_lemb;
				break;
			}
		}

		// Group by semester and track
		$grouped = [];
		foreach ($classes as $c) {
			$smt = !empty($c->smt) && is_numeric($c->smt) ? (int)$c->smt : 'Lainnya';
			$is_internship = (stripos($c->nm_mk, 'internship') !== false);
			$track = $is_internship ? 'Jalur Magang / Internship (MBKM)' : 'Jalur Reguler';

			$grouped[$smt][$track] ??= [];
			$grouped[$smt][$track][$c->kode_mk] ??= [
					'kode_mk' => $c->kode_mk,
					'nm_mk' => $c->nm_mk,
					'sks_mk' => (int)$c->sks_mk,
					'sks_tm' => (int)$c->sks_tm,
					'sks_prak' => (int)$c->sks_prak,
					'a_wajib' => (int)$c->a_wajib,
					'classes' => []
				];
			$grouped[$smt][$track][$c->kode_mk]['classes'][] = $c->nm_kls;
		}
		ksort($grouped);

		// Render HTML
		$html = '<div style="margin-bottom: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">';
		$html .= '<div><span style="font-size: 11.5px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; display: block; margin-bottom: 3px;">Pilih Program Studi</span>';
		$html .= '<select id="select_prodi_makul" class="form-control" onchange="load_makul_prodi(this.value)" style="border-radius: 6px; font-weight: 600; color: #1e293b; min-width: 260px; height: 36px;">';
		foreach ($prodis as $p) {
			$sel = ($p->id_sms == $id_sms) ? 'selected' : '';
			$html .= '<option value="' . $p->id_sms . '" ' . $sel . '>' . $p->nm_lemb . '</option>';
		}
		$html .= '</select></div>';
		$html .= '<div><span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 6px;"><i class="fa fa-calendar-check-o"></i> Tahun Ajaran ' . $id_smt . '</span></div>';
		$html .= '</div>';

		if (empty($grouped)) {
			$html .= '<div class="alert alert-warning" style="border-radius: 6px; padding: 15px;"><i class="fa fa-info-circle"></i> Belum ada kelas kuliah yang ditawarkan untuk program studi ini pada semester aktif.</div>';
		} else {
			foreach ($grouped as $smt => $tracks) {
				$has_multi_tracks = (count($tracks) > 1);

				foreach ($tracks as $track_name => $mks) {
					$total_sks_smt = 0;
					$total_mk_smt = count($mks);
					foreach ($mks as $m) {
						$total_sks_smt += $m['sks_mk'];
					}

					$smt_title = is_numeric($smt) ? 'Paket Semester ' . $smt : 'Mata Kuliah Pilihan / Non-Semester';
					if ($has_multi_tracks) {
						$track_badge_color = (stripos($track_name, 'internship') !== false) ? '#d97706' : '#2563eb';
						$smt_title .= ' &bull; <span style="color: ' . $track_badge_color . '; font-weight: 700;">' . $track_name . '</span>';
					}

					$html .= '<div style="margin-bottom: 22px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">';
					$html .= '<div style="background: #f1f5f9; padding: 10px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">';
					$html .= '<div><span style="font-weight: 700; font-size: 13.5px; color: #1e293b;"><i class="fa fa-bookmark" style="color: #3b82f6; margin-right: 6px;"></i> ' . $smt_title . '</span></div>';
					$html .= '<div><span class="badge" style="background: #ffffff; color: #334155; border: 1px solid #cbd5e1; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px; margin-right: 4px;">' . $total_mk_smt . ' MK</span> <span class="badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">' . $total_sks_smt . ' SKS Total</span></div>';
					$html .= '</div>';

					$html .= '<div class="table-responsive"><table class="table table-hover table-striped" style="margin-bottom: 0; font-size: 12px;">';
					$html .= '<thead><tr style="background: #fafafa; color: #475569; font-weight: 700; font-size: 11.5px;">';
					$html .= '<th width="35" class="text-right" style="padding: 8px 10px;">No</th>';
					$html .= '<th width="95" style="padding: 8px 10px;">Kode MK</th>';
					$html .= '<th style="padding: 8px 10px;">Mata Kuliah</th>';
					$html .= '<th width="75" class="text-center" style="padding: 8px 10px;">Sifat</th>';
					$html .= '<th width="50" class="text-center" style="padding: 8px 10px;">SKS</th>';
					$html .= '<th width="45" class="text-center" style="padding: 8px 10px;" title="Teori">T</th>';
					$html .= '<th width="45" class="text-center" style="padding: 8px 10px;" title="Praktikum">P</th>';
					$html .= '<th style="padding: 8px 10px;">Kelas Dibuka</th>';
					$html .= '</tr></thead><tbody>';

					$no = 1;
					foreach ($mks as $mk) {
						$classes_badges = '';
						foreach ($mk['classes'] as $cls) {
							$classes_badges .= '<span class="label label-default" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: 600; font-size: 11px; margin-right: 4px; display: inline-block; padding: 2px 6px; border-radius: 3px;">' . $cls . '</span>';
						}

						$sifat_badge = ($mk['a_wajib'] == 1) 
							? '<span class="label" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">Wajib</span>'
							: '<span class="label" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">Pilihan</span>';

						$html .= '<tr>';
						$html .= '<td class="text-right" style="vertical-align: middle; padding: 8px 10px; color: #64748b;">' . $no++ . '.</td>';
						$html .= '<td style="vertical-align: middle; padding: 8px 10px; font-weight: 600; color: #334155;">' . $mk['kode_mk'] . '</td>';
						$html .= '<td style="vertical-align: middle; padding: 8px 10px; font-weight: 600; color: #0f172a;">' . $mk['nm_mk'] . '</td>';
						$html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px;">' . $sifat_badge . '</td>';
						$html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px; font-weight: 700; color: #2563eb;">' . $mk['sks_mk'] . '</td>';
						$html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px; color: #64748b;">' . $mk['sks_tm'] . '</td>';
						$html .= '<td class="text-center" style="vertical-align: middle; padding: 8px 10px; color: #64748b;">' . $mk['sks_prak'] . '</td>';
						$html .= '<td style="vertical-align: middle; padding: 8px 10px;">' . $classes_badges . '</td>';
						$html .= '</tr>';
					}

					$html .= '</tbody></table></div></div>';
				}
			}
		}

		if ($this->input->is_ajax_request()) {
			echo json_encode(['status' => TRUE, 'id_sms' => $id_sms, 'prodi_name' => $current_prodi_name, 'html' => $html]);
		} else {
			$output['judul_besar'] = 'Daftar Kelas Kuliah Ditawarkan Prodi';
			$output['judul_kecil'] = 'Tahun Ajaran ' . $id_smt;
			$output['menu_aktif'] = 'm_persetujuan_krs';
			$output['parent_1'] = 'active open';
			$output['content_html'] = $html;
			$output['content'] = 'persetujuan_krs/makul_prodi_page';
			$this->load->view('master_page', $output);
		}
	}

	private function _get_star_rating_html($ipk, $float_right = true)
	{
		$val = (float)($ipk ?? 0);
		if ($val <= 0) return '';

		$float_css = $float_right ? 'float: right;' : 'display: inline-block; vertical-align: middle; margin-left: 6px;';

		if ($val >= 3.50) {
			$stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
			$color = '#1cc88a'; // hijau (5 bintang)
			$bg = '#ecfdf5';
			$border = '#a7f3d0';
			$label = 'Sangat Memuaskan';
		} elseif ($val >= 3.00) {
			$stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
			$color = '#1cc88a'; // hijau (4 bintang)
			$bg = '#ecfdf5';
			$border = '#a7f3d0';
			$label = 'Unggul';
		} elseif ($val >= 2.50) {
			$stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
			$color = '#f6c23e'; // kuning/emas (3 bintang)
			$bg = '#fffbeb';
			$border = '#fef3c7';
			$label = 'Cukup';
		} else {
			$stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i>';
			$color = '#e74a3b'; // merah (2 bintang)
			$bg = '#fef2f2';
			$border = '#fecaca';
			$label = 'Perlu Perhatian';
		}

		return '<span title="Rating IPK: ' . number_format($val, 2) . ' (' . $label . ')" style="color: ' . $color . '; font-size: 10px; letter-spacing: 1.5px; ' . $float_css . ' background: ' . $bg . '; border: 1px solid ' . $border . '; border-radius: 12px; padding: 1px 6px; white-space: nowrap;">' . $stars . '</span>';
	}

	public function academic_record($nipd)
	{
		redirect("Lecturer_data_perwalian/academic_record/$nipd");
	}
}
