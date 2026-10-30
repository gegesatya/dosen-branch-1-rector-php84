<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_tracking_kegiatan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Tracking_kegiatan_model');
		date_default_timezone_set("Asia/Jakarta");

		if (!$this->ion_auth->logged_in()) {
			redirect('auth/login', 'refresh');
		}

		$group = [2];
		if (!$this->ion_auth->in_group($group)) {
			return show_error('Forbidden Access');
		}

		/* if (cek_tunggakan() == 1) {
			redirect('C_administrasi_info', 'refresh');
		} */
	}

	public function index()
	{
		$output['judul_besar'] 		= 'Ringkasan Laporan Mingguan';
		$output['judul_kecil'] 		= 'Program Internship ';
		$output['menu_aktif'] 		= 'm_tracking_kegiatan';
		$output['parent_kaprodi']	= 'active open';
		$output['content'] 			= 'tracking_kegiatan/tracking_kegiatan_view';
		$output['after_page'] 		= 'tracking_kegiatan/tracking_kegiatan_after_page';

		$this->load->view('master_page', $output);
	}

	public function ajax_list()
	{
		$list = $this->Tracking_kegiatan_model->get_datatables();
		$data = [];
		$no = $_POST['start'];
		foreach ($list as $l) {
			$no++;
			$row = [];

			$row[] = $no;
			$row[] = $l->nipd;
			$row[] = $l->nm_pd;

			$row[] = '<a class="btn btn-sm btn-default btn-sm" target="_new"
							href="' . site_url("C_tracking_kegiatan/cetak_tracking/$l->nipd") . '">
								<i class="icon-printer"></i>
							</a>';

			$data[] = $row;
		}

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Tracking_kegiatan_model->count_all(),
			"recordsFiltered" => $this->Tracking_kegiatan_model->count_filtered(),
			"data" => $data,
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Tracking_kegiatan_model->get_by_id($id);
		echo json_encode($data);
	}


	public function ajax_update()
	{
		$this->_validate();

		$nipd = $this->session->userdata('nim');
		$week 	= $this->input->post('week');
		$periode_laporan 	= $this->input->post('periode_laporan');
		$kegiatan_hari_1 	= $this->input->post('kegiatan_hari_1');
		$kegiatan_hari_2 	= $this->input->post('kegiatan_hari_2');
		$kegiatan_hari_3 	= $this->input->post('kegiatan_hari_3');
		$kegiatan_hari_4 	= $this->input->post('kegiatan_hari_4');
		$kegiatan_hari_5 	= $this->input->post('kegiatan_hari_5');
		$kegiatan_hari_6 	= $this->input->post('kegiatan_hari_6');
		$pengetahuan_baru 	= $this->input->post('pengetahuan_baru');
		$keterampilan_baru 	= $this->input->post('keterampilan_baru');
		$tujuan_internship 	= $this->input->post('tujuan_internship');
		$catatan_lain 	= $this->input->post('catatan_lain');

		$change_by 			= $this->ion_auth->user()->row()->id;
		$change_date 		= date('Y-m-d h:i:s');


		$data = compact(
			'week',
			'periode_laporan',
			'kegiatan_hari_1',
			'kegiatan_hari_2',
			'kegiatan_hari_3',
			'kegiatan_hari_4',
			'kegiatan_hari_5',
			'kegiatan_hari_6',
			'pengetahuan_baru',
			'keterampilan_baru',
			'tujuan_internship',
			'catatan_lain',
			'change_by',
			'change_date'
		);

		$this->Tracking_kegiatan_model->update(['id_tracking' => $this->input->post('id_tracking')], $data);
		echo json_encode(["status" => TRUE]);
	}



	private function _clean_html_for_mpdf($html)
	{
		if (empty($html)) return '';

		if (str_contains($html, 'pointer-events-auto') || str_contains($html, 'data-turn="assistant"') || str_contains($html, 'data-conversation-screenshot-content')) {
			$warning = '<div style="color: red; font-size: 12px; font-style: italic; font-weight: bold; margin-bottom: 5px;">[Terindikasi copas dari chatgpt]</div>';
			$cleaned = preg_replace('/^.*?data-turn=\\\\?["\']assistant\\\\?["\']\s*>/is', '', $html);
			if ($cleaned === $html) {
				$cleaned = preg_replace('/^.*?pointer-events-auto[^>]*>/is', '', $html);
			}
			if ($cleaned === $html) {
				$cleaned = preg_replace('/^.*?data-conversation-screenshot-content[^>]*>/is', '', $html);
			}
			$cleaned = ltrim($cleaned, " *]:\n\r\t");
			$cleaned = strip_tags($cleaned, '<p><br><br/><ul><li><ol><strong><b><i><em><u><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6>');
			$cleaned = preg_replace('/Peringatan:\s*Laporan hari ini diisi terlambat\s*\(\d+\s*hari setelah kegiatan\)\.?/i', '', $cleaned);
			$html = $warning . $cleaned;
		} else {
			$html = strip_tags($html, '<p><br><br/><ul><li><ol><strong><b><i><em><u><table><thead><tbody><tr><th><td><h1><h2><h3><h4><h5><h6>');
			$html = preg_replace('/Peringatan:\s*Laporan hari ini diisi terlambat\s*\(\d+\s*hari setelah kegiatan\)\.?/i', '', $html);
		}
		
		// 1. Dekode entitas HTML barangkali mahasiswa secara literal menyimpan &lt;i&gt; dsb.
		$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		
		// 2. Gunakan DOMDocument parser native PHP untuk menambal/membuang tag yang terputus (mismatched tags)
		if (!empty(trim($html))) {
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML('<div>' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			$divs = $dom->getElementsByTagName('div');
			if ($divs->length > 0) {
				$wrapper = $divs->item(0);
				$inner_html = '';
				foreach ($wrapper->childNodes as $child) {
					$inner_html .= $dom->saveHTML($child);
				}
				$html = $inner_html;
			}
			libxml_clear_errors();
		}

		// Hapus komentar HTML (termasuk komentar MS Word)
		$html = preg_replace('/<!--(.*?)-->/is', '', $html);
		// Hapus tag o:p bawaan MS Word
		$html = preg_replace('/<\/?o:p[^>]*>/is', '', $html);
		// Hapus tag hr (garis pemisah buatan) yang bisa bikin garis ganda
		$html = preg_replace('/<hr[^>]*>/is', '', $html);
		// Hapus atribut kotor yang sering membuat mPDF error (style, class, lang, dir, align) serta atribut width/height yang memicu shrink_tables_to_fit
		$html = preg_replace('/(?:style|class|lang|dir|align|valign|width|height)="[^"]*"/i', '', $html);
		// Hapus atribut tunggal tanpa tanda kutip (kadang dari Word)
		$html = preg_replace('/(?:style|class|lang|dir|align|valign|width|height)=\'[^\']*\'/i', '', $html);
		
		// Konversi tag penutup p dan div menjadi baris baru
		$html = preg_replace('/<\/(p|div)>/i', '<br/>', $html);
		// Hapus tag pembuka p dan div
		$html = preg_replace('/<(p|div)[^>]*>/i', '', $html);
		
		// 3. Deteksi & konversi tabel lama dari Excel (fallback untuk data yang sudah tersimpan)
		//    Tabel diekstrak ke placeholder SEBELUM strip_tags agar struktur tidak hilang,
		//    kemudian di-restore kembali sesudahnya.
		$excel_tables = [];
		if (stripos($html, '<table') !== false) {
			$html = preg_replace_callback('/<table[^>]*>.*?<\/table>/is', function($m) use (&$excel_tables) {
				$raw = $m[0];
				// Ambil semua baris tabel
				preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $raw, $tr_matches);
				$all_rows = [];
				foreach ($tr_matches[1] as $tr_inner) {
					preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $tr_inner, $cell_matches);
					$cells = [];
					foreach ($cell_matches[1] as $cell_html) {
						$text = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $cell_html)));
						$text = preg_replace('/\s+/', ' ', $text);
						if ($text !== '') $cells[] = $text;
					}
					if (!empty($cells)) $all_rows[] = $cells;
				}
				if (empty($all_rows)) return '';

				// Deteksi baris pertama sebagai header (tidak mengandung pola waktu HH.MM)
				$first_is_header = false;
				if (count($all_rows) > 1 && !preg_match('/\d{1,2}[.:]\d{2}/', implode(' ', $all_rows[0]))) {
					$first_is_header = true;
				}
				$data_rows = $first_is_header ? array_slice($all_rows, 1) : $all_rows;
				if (empty($data_rows)) return '';

				$col_count = max(array_map('count', $data_rows));
				$td_base = 'border: none; vertical-align: top; padding: 5px 8px; font-size: 14px; line-height: 1.5;';

				if ($col_count >= 2) {
					// Tabel multi-kolom: kolom pertama dianggap sebagai "Jam" (lebih sempit)
					$td_time = $td_base . ' width: 80px; white-space: nowrap;';
					$rows_html = '';
					if ($first_is_header) {
						// Tampilkan baris header dengan background biru muda
						$th_style = $td_base . ' background-color: #dce6f1; font-weight: bold;';
						$rows_html .= '<tr>';
						foreach ($all_rows[0] as $cidx => $cell) {
							$s = ($cidx === 0) ? str_replace($td_base, '', $th_style) . $td_base : $th_style;
							$rows_html .= '<th style="' . $th_style . '">' . htmlspecialchars($cell) . '</th>';
						}
						$rows_html .= '</tr>';
					}
					foreach ($data_rows as $row) {
						$rows_html .= '<tr>';
						foreach ($row as $cidx => $cell) {
							$style = ($cidx === 0) ? $td_time : $td_base;
							$rows_html .= '<td style="' . $style . '">' . htmlspecialchars($cell) . '</td>';
						}
						$rows_html .= '</tr>';
					}
					$converted = '<table width="100%" style="border-collapse: collapse; font-size: 14px; line-height: 1.5; margin: 4px 0;">' . $rows_html . '</table>';
				} elseif (count($data_rows) === 1) {
					// 1 baris, 1 kolom: teks langsung
					$converted = htmlspecialchars($data_rows[0][0] ?? '');
				} else {
					// Multi-baris, 1 kolom: tabel a, b, c
					$td_no  = $td_base . ' width: 28px; text-align: center; white-space: nowrap;';
					$rows_html = '';
					foreach ($data_rows as $idx => $row) {
						$rows_html .= '<tr><td style="' . $td_no . '">' . chr(97 + $idx) . '.</td><td style="' . $td_base . '">' . htmlspecialchars($row[0] ?? '') . '</td></tr>';
					}
					$converted = '<table width="100%" style="border-collapse: collapse; font-size: 14px; line-height: 1.5; margin: 4px 0;">' . $rows_html . '</table>';
				}

				$placeholder = '%%EXCELTBL' . count($excel_tables) . '%%';
				$excel_tables[$placeholder] = $converted;
				return $placeholder;
			}, $html);
		}

		// Kita hanya mengizinkan tag enter dan list agar susunan teks tetap rapi
		$html = strip_tags($html, '<br><ul><ol><li>');

		// Bersihkan multiple br yang tumpang tindih
		$html = preg_replace('/(<br\s*\/?>\s*)+/', '<br/>', $html);
		// Hapus br di awal dan akhir string
		$html = preg_replace('/^(<br\s*\/?>\s*)+|(<br\s*\/?>\s*)+$/i', '', trim($html));

		// --- KONVERSI LIST MENJADI TABEL BERNOMOR UNTUK mPDF ---
		// Hapus karakter bullet manual yang mungkin ada
		$html = preg_replace('/(?:&bull;|&middot;|•|·|&#8226;|◦|■|▪|&square;)\s*/ui', '', $html);

		// Ambil semua blok <ul>...</ul> atau <ol>...</ol> dan konversi jadi tabel
		$html = preg_replace_callback('/<(?:ul|ol)[^>]*>(.*?)<\/(?:ul|ol)>/is', function($matches) {
			$inner = $matches[1];
			// Ambil semua <li>
			preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $inner, $items);
			if (empty($items[1])) return '';

			// Jika hanya 1 item, hilangkan bullet-nya
			if (count($items[1]) === 1) {
				$item = strip_tags($items[1][0], '<b><strong><u><br>');
				$item = preg_replace('/(<br\s*\/?>)+$/i', '', trim($item));
				return $item;
			}

			$td_no  = 'border: none; vertical-align: top; padding: 5px 8px; font-size: 14px; line-height: 1.5; width: 28px; text-align: center; white-space: nowrap;';
			$td_txt = 'border: none; vertical-align: top; padding: 5px 8px; font-size: 14px; line-height: 1.5;';
			$rows = '';
			foreach ($items[1] as $idx => $item) {
				$item = strip_tags($item, '<b><strong><u><br>');
				$item = preg_replace('/(<br\s*\/?>)+$/i', '', trim($item));
				$no   = chr(97 + $idx) . '.';
				$rows .= '<tr><td style="' . $td_no . '">' . $no . '</td><td style="' . $td_txt . '">' . $item . '</td></tr>';
			}
			return '<table width="100%" style="border-collapse: collapse; font-size: 14px; line-height: 1.5; margin: 4px 0;">' . $rows . '</table>';
		}, $html);

		// Paksa ukuran font dan ukuran tabel agar tidak menyusut di mPDF
		$html = preg_replace('/<table(?![^>]*style)/i', '<table width="100%" style="border-collapse: collapse; font-size: 14px; line-height: 1.5;" ', $html);
		$html = preg_replace('/<td(?![^>]*style)/i', '<td style="font-size: 14px; line-height: 1.5; padding: 5px;" ', $html);
		$html = preg_replace('/<th(?![^>]*style)/i', '<th style="font-size: 14px; line-height: 1.5; padding: 5px;" ', $html);

		// Restore tabel Excel yang sudah dikonversi dari placeholder
		if (!empty($excel_tables)) {
			foreach ($excel_tables as $placeholder => $tbl_html) {
				$html = str_replace($placeholder, $tbl_html, $html);
			}
		}

		$warning_text = '<div style="color: red; font-size: 12px; font-style: italic; font-weight: bold; margin-bottom: 5px;">[Terindikasi copas dari chatgpt]</div>';
		$has_warning = (str_contains($html, $warning_text));
		if ($has_warning) {
			$html = str_replace($warning_text, '', $html);
		}

		if (stripos($html, '<table') === false && !empty(trim(strip_tags($html)))) {
			$item = trim($html);
			$item = preg_replace('/(<br\s*\/?>\s*)+$/i', '', $item);
			$item = preg_replace('/^(<br\s*\/?>\s*)+/i', '', $item);

			$html = $item;
		}

		if ($has_warning) {
			$html = $warning_text . $html;
		}

		return $html;
	}

	public function cetak_tracking($nipd)
	{
		$data = $this->db->where('a.nipd', $nipd)
			->select('a.*, m.nm_pd')
			->from('simlapkerma_tracking_kegiatan a')
			->join('mahasiswa_pt mp', 'a.nipd=mp.nipd')
			->join('mahasiswa m', 'mp.id_pd=m.id_pd')
			->order_by('a.week', 'asc')
			->get()->result();

		foreach ($data as &$d) {
			for ($i = 1; $i <= 6; $i++) {
				$f = 'kegiatan_hari_' . $i;
				$v = $d->$f;
				if (!empty($v) && trim($v) !== '<p><br></p>') {
					$js = @json_decode($v, true);

                    if (!is_array($js)) {
                        $v_fixed = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $v);
                        $v_fixed = preg_replace("/\\\\(?![\"\\\\\/bfnrtu])/i", "\\\\\\\\", $v_fixed);
                        $js = @json_decode($v_fixed, true);
                    }

                    $extract_teks = function($val, $key) use (&$extract_teks) {
                        $current = is_array($val) ? ($val['teks'] ?? '') : $val;
                        if (is_string($current)) {
                            $current = trim($current);
                            if (str_starts_with($current, '{')) {
                                $inner_js = @json_decode($current, true);
                                if (!is_array($inner_js)) {
                                    $fixed_current = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $current);
                                    $inner_js = @json_decode($fixed_current, true);
                                    if (!is_array($inner_js)) {
                                        $clean_current = preg_replace('/\\\\([^"\\\\\/bfnrtu])/', "$1", $fixed_current);
                                        $clean_current = preg_replace('/[\x00-\x1F\x7F]/', '', $clean_current);
                                        $last_brace = strrpos($clean_current, '}');
                                        if ($last_brace !== false) {
                                            $clean_current = substr($clean_current, 0, $last_brace + 1);
                                        }
                                        $inner_js = @json_decode($clean_current, true);
                                    }
                                }
                                if (is_array($inner_js)) {
                                    if (isset($inner_js[$key]['teks'])) {
                                        return $extract_teks($inner_js[$key]['teks'], $key);
                                    } elseif (isset($inner_js['teks'])) {
                                        return $extract_teks($inner_js['teks'], $key);
                                    }
                                    return '';
                                }
                            }
                        }
                        return $current;
                    };

					$html_day = '';
					if (is_array($js) && (array_key_exists('pagi', $js) || array_key_exists('siang', $js) || array_key_exists('sore', $js) || array_key_exists('tanggal_kegiatan', $js))) {
						if (!empty($js['tanggal_kegiatan'])) {
							$html_day .= '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">' . $js['tanggal_kegiatan'] . ' (Hari ke ' . $i . ')</span></div>';
						}

						$label_block_style = 'padding-top: 8px;';
						$created_style = 'font-size: 11px; color: #777777; font-style: italic; padding-top: 4px; padding-bottom: 8px; ';
						$sep           = '<div style="height: 10px; font-size: 10px; line-height: 10px;">&nbsp;</div>';

						$pagi = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
						$pagi = $extract_teks($pagi, 'pagi');
						$pagi_clean = $this->_clean_html_for_mpdf($pagi);
						if (!empty(trim(strip_tags($pagi_clean)))) {
							$pagi_created = !empty($js['pagi']['created_at']) ? $js['pagi']['created_at'] : null;
							$pagi_updated = !empty($js['pagi']['updated_at']) ? $js['pagi']['updated_at'] : null;
							$html_day .= '<div style="' . $label_block_style . '"><span style="font-weight: bold; font-size: 13px;">Pagi</span></div>';
							$html_day .= '<div style="padding-left: 15px;">';
							$html_day .= $pagi_clean;
							$html_day .= '</div>';
							if ($pagi_created) {
								$ts = $pagi_updated ?? $pagi_created;
								$html_day .= '<div class="text-right" style="' . $created_style . '">Diisi tanggal : ' . date('d M Y H:i', strtotime($ts)) . '</div>';
							}
							$html_day .= $sep;
						}

						$siang = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
						$siang = $extract_teks($siang, 'siang');
						$siang_clean = $this->_clean_html_for_mpdf($siang);
						if (!empty(trim(strip_tags($siang_clean)))) {
							$siang_created = !empty($js['siang']['created_at']) ? $js['siang']['created_at'] : null;
							$siang_updated = !empty($js['siang']['updated_at']) ? $js['siang']['updated_at'] : null;
							$html_day .= '<div style="' . $label_block_style . '"><span style="font-weight: bold; font-size: 13px;">Siang</span></div>';
							$html_day .= '<div style="padding-left: 15px;">';
							$html_day .= $siang_clean;
							$html_day .= '</div>';
							if ($siang_created) {
								$ts = $siang_updated ?? $siang_created;
								$html_day .= '<div class="text-right" style="' . $created_style . '">Diisi tanggal : ' . date('d M Y H:i', strtotime($ts)) . '</div>';
							}
							$html_day .= $sep;
						}

						$sore = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
						$sore = $extract_teks($sore, 'sore');
						$sore_clean = $this->_clean_html_for_mpdf($sore);
						if (!empty(trim(strip_tags($sore_clean)))) {
							$sore_created = !empty($js['sore']['created_at']) ? $js['sore']['created_at'] : null;
							$sore_updated = !empty($js['sore']['updated_at']) ? $js['sore']['updated_at'] : null;
							$html_day .= '<div style="' . $label_block_style . '"><span style="font-weight: bold; font-size: 13px;">Sore</span></div>';
							$html_day .= '<div style="padding-left: 15px;">';
							$html_day .= $sore_clean;
							$html_day .= '</div>';
							if ($sore_created) {
								$ts = $sore_updated ?? $sore_created;
								$html_day .= '<div class="text-right" style="' . $created_style . '">Diisi tanggal : ' . date('d M Y H:i', strtotime($ts)) . '</div>';
							}
							$html_day .= $sep;
						}

						if (empty(trim(strip_tags($html_day)))) {
                            $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                        } elseif (preg_match('/^\s*<div[^>]*><span[^>]*>[^<]*\(Hari ke \d+\)<\/span><\/div>\s*$/i', $html_day)) {
                            $d->$f = $html_day . '<div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                        } else {
    						$d->$f = $html_day;
                        }
					} else {
						$cleaned = $this->_clean_html_for_mpdf($v);
                        if (empty(trim(strip_tags($cleaned)))) {
                            $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                        } else {
    						$d->$f   = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div>'
    						         . '<div style="padding-left: 15px;">' . $cleaned . '</div>';
                        }
					}
				} else {
                    $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                }
			}
			$d->pengetahuan_baru = $this->_clean_html_for_mpdf($d->pengetahuan_baru);
            if (empty(trim(strip_tags($d->pengetahuan_baru)))) $d->pengetahuan_baru = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->keterampilan_baru = $this->_clean_html_for_mpdf($d->keterampilan_baru);
            if (empty(trim(strip_tags($d->keterampilan_baru)))) $d->keterampilan_baru = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->tujuan_internship = $this->_clean_html_for_mpdf($d->tujuan_internship);
            if (empty(trim(strip_tags($d->tujuan_internship)))) $d->tujuan_internship = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->catatan_lain = $this->_clean_html_for_mpdf($d->catatan_lain);
            if (empty(trim(strip_tags($d->catatan_lain)))) $d->catatan_lain = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			$d->review_dpl = $this->_clean_html_for_mpdf($d->review_dpl);
		}

		$output['data'] = $data;

		// $this->load->view('cetak/cetak_tracking', $output);

		$mhs = $this->db->where('nipd', $nipd)->from('mahasiswa_pt mp')->join('mahasiswa m', 'mp.id_pd=m.id_pd')->get()->row();
		$nm_pd = $mhs->nm_pd;
		$nm_lemb = $this->db->where('id_sms', $mhs->id_sms)->get('sms')->row()->nm_lemb;
		$output['nm_pd'] = $nm_pd;
		$output['nipd'] = $nipd;
		$output['nm_lemb'] = $nm_lemb;

		$lokasi = $this->db->where('nipd', $nipd)->get('simlapkerma_lokasi_magang');
		if ($lokasi->num_rows() > 0) {
			$output['lokasi'] = $lokasi->row()->nama_perusahaan;
			$output['tahun_magang'] = $lokasi->row()->tahun_magang;
		} else {
			$output['lokasi'] = 'Nama Perusahaan Belum Diisi';
			$output['tahun_magang'] = 'Tahun Magang Belum Diisi';
		}

		$html = $this->load->view('cetak/cetak_tracking', $output, true);
		$pdfFilePath  = "Ringkasan Lap Mingguan " . $nm_pd . ".pdf";
		// $pdfFilePath  = "r.pdf";

		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-P']);
		$mpdf->SetFooter('Halaman {PAGENO} dari {nbpg}');
		
		$old_err = error_reporting();
		error_reporting($old_err & ~E_NOTICE & ~E_WARNING);
		
		$mpdf->WriteHTML($html);
		$mpdf->Output($pdfFilePath, "I");
	}

	public function cetak_tracking_html($nipd)
	{
		$data = $this->db->where('a.nipd', $nipd)
			->select('a.*, m.nm_pd')
			->from('simlapkerma_tracking_kegiatan a')
			->join('mahasiswa_pt mp', 'a.nipd=mp.nipd')
			->join('mahasiswa m', 'mp.id_pd=m.id_pd')
			->order_by('a.week', 'asc')
			->get()->result();

		foreach ($data as &$d) {
			for ($i = 1; $i <= 6; $i++) {
				$f = 'kegiatan_hari_' . $i;
				$v = $d->$f;
				if (!empty($v) && trim($v) !== '<p><br></p>') {
					$js = @json_decode($v, true);

                    if (!is_array($js)) {
                        $v_fixed = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $v);
                        $v_fixed = preg_replace("/\\\\(?![\"\\\\\/bfnrtu])/i", "\\\\\\\\", $v_fixed);
                        $js = @json_decode($v_fixed, true);
                    }

                    // Jika masih gagal, mungkin JSON terbungkus tag HTML (misal <ul><li> atau <p>) atau HTML entities
                    if (!is_array($js)) {
                        $v_stripped = trim(strip_tags(html_entity_decode($v, ENT_QUOTES, 'UTF-8')));
                        $v_stripped = str_replace('&nbsp;', ' ', $v_stripped);
                        if (str_starts_with(trim($v_stripped), '{')) {
                            $js = @json_decode($v_stripped, true);
                            if (!is_array($js)) {
                                $v_stripped_fixed = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $v_stripped);
                                $v_stripped_fixed = preg_replace('/\\\\(?!["\\\\\/bfnrtu])/i', '\\\\\\\\', $v_stripped_fixed);
                                $js = @json_decode($v_stripped_fixed, true);
                            }
                        }
                    }

                    // Fallback terakhir: Ekstrak paksa menggunakan regex jika JSON benar-benar hancur (misal ada copy-paste XML Word)
                    if (!is_array($js)) {
                        $parsed = [];
                        if (preg_match('/"tanggal_kegiatan"\s*:\s*"([^"]+)"/i', $v, $m)) {
                            $parsed['tanggal_kegiatan'] = $m[1];
                        }
                        foreach (['pagi', 'siang', 'sore'] as $seg) {
                            if (preg_match('/"' . $seg . '"\s*:\s*\{(?:[^}]*)?"teks"\s*:\s*"(.*?)"\s*(?:,"created_at"|\})/is', $v, $m)) {
                                $parsed[$seg] = ['teks' => trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')))];
                            }
                        }
                        if (!empty($parsed['tanggal_kegiatan']) || !empty($parsed['pagi']) || !empty($parsed['siang']) || !empty($parsed['sore'])) {
                            $js = $parsed;
                        }
                    }

                    $extract_teks = function($val, $key) use (&$extract_teks) {
                        $current = is_array($val) ? ($val['teks'] ?? '') : $val;
                        if (is_string($current)) {
                            $current = trim($current);
                            if (str_starts_with($current, '{')) {
                                $inner_js = @json_decode($current, true);
                                if (!is_array($inner_js)) {
                                    $fixed_current = str_replace(["\r", "\n", "\t"], ["\\r", "\\n", "\\t"], $current);
                                    $inner_js = @json_decode($fixed_current, true);
                                    if (!is_array($inner_js)) {
                                        $clean_current = preg_replace('/\\\\([^"\\\\\/bfnrtu])/', "$1", $fixed_current);
                                        $clean_current = preg_replace('/[\x00-\x1F\x7F]/', '', $clean_current);
                                        $last_brace = strrpos($clean_current, '}');
                                        if ($last_brace !== false) {
                                            $clean_current = substr($clean_current, 0, $last_brace + 1);
                                        }
                                        $inner_js = @json_decode($clean_current, true);
                                    }
                                }
                                if (is_array($inner_js)) {
                                    if (isset($inner_js[$key]['teks'])) {
                                        return $extract_teks($inner_js[$key]['teks'], $key);
                                    } elseif (isset($inner_js['teks'])) {
                                        return $extract_teks($inner_js['teks'], $key);
                                    }
                                    return '';
                                }
                            }
                        }
                        return $current;
                    };

					$html_day = '';
					if (is_array($js) && (array_key_exists('pagi', $js) || array_key_exists('siang', $js) || array_key_exists('sore', $js) || array_key_exists('tanggal_kegiatan', $js))) {
						$header = '';
						if (!empty($js['tanggal_kegiatan'])) {
							$header = $js['tanggal_kegiatan'] . ' (Hari ke ' . $i . ')';
						} else {
                            $header = 'Hari ke ' . $i;
                        }

                        $pagi = is_array($js['pagi'] ?? null) ? ($js['pagi']['teks'] ?? '') : ($js['pagi'] ?? '');
						$pagi = $extract_teks($pagi, 'pagi');
						$pagi_clean = $this->_clean_html_for_mpdf($pagi);
                        $pagi_ts = null;
                        if (!empty(trim(strip_tags($pagi_clean)))) {
                            $pagi_created = !empty($js['pagi']['created_at']) ? $js['pagi']['created_at'] : null;
							$pagi_updated = !empty($js['pagi']['updated_at']) ? $js['pagi']['updated_at'] : null;
                            $pagi_ts = $pagi_updated ?? $pagi_created;
                        }

                        $siang = is_array($js['siang'] ?? null) ? ($js['siang']['teks'] ?? '') : ($js['siang'] ?? '');
						$siang = $extract_teks($siang, 'siang');
						$siang_clean = $this->_clean_html_for_mpdf($siang);
                        $siang_ts = null;
                        if (!empty(trim(strip_tags($siang_clean)))) {
                            $siang_created = !empty($js['siang']['created_at']) ? $js['siang']['created_at'] : null;
							$siang_updated = !empty($js['siang']['updated_at']) ? $js['siang']['updated_at'] : null;
                            $siang_ts = $siang_updated ?? $siang_created;
                        }

                        $sore = is_array($js['sore'] ?? null) ? ($js['sore']['teks'] ?? '') : ($js['sore'] ?? '');
						$sore = $extract_teks($sore, 'sore');
						$sore_clean = $this->_clean_html_for_mpdf($sore);
                        $sore_ts = null;
                        if (!empty(trim(strip_tags($sore_clean)))) {
                            $sore_created = !empty($js['sore']['created_at']) ? $js['sore']['created_at'] : null;
							$sore_updated = !empty($js['sore']['updated_at']) ? $js['sore']['updated_at'] : null;
                            $sore_ts = $sore_updated ?? $sore_created;
                        }

                        $has_pagi = !empty(trim(strip_tags($pagi_clean)));
                        $has_siang = !empty(trim(strip_tags($siang_clean)));
                        $has_sore = !empty(trim(strip_tags($sore_clean)));

                        if ($has_pagi || $has_siang || $has_sore) {
                            $html_day .= '<div class="kegiatan-header" style="font-weight: bold; margin-bottom: 10px; font-size: 14.5px; color: #1e293b;">' . $header . '</div>';
                            $html_day .= '<table class="table-kegiatan" style="width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 1px solid #cbd5e1; font-size: 13.5px;">';
                            $html_day .= '<thead><tr>';
                            $html_day .= '<th style="border: 1px solid #cbd5e1; padding: 10px 14px; background-color: #f8fafc; width: 30%; text-align: left; color: #475569; font-weight: 600;">Waktu Aktivitas</th>';
                            $html_day .= '<th style="border: 1px solid #cbd5e1; padding: 10px 14px; background-color: #f8fafc; text-align: left; color: #475569; font-weight: 600;">Kegiatan</th>';
                            $html_day .= '</tr></thead>';
                            $html_day .= '<tbody>';

                            $addRow = function($waktu, $clean_content, $ts) {
                                if (empty(trim(strip_tags($clean_content)))) return '';
                                $dateHtml = '';
                                if ($ts) {
                                    $dateStr = date('d M Y H:i', strtotime($ts));
                                    $dateHtml = '<div style="text-align: right; margin-top: 10px; font-size: 12px; color: #64748b; font-style: italic;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16" style="margin-right: 4px; position: relative; top: -1px;">
                                          <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
                                          <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                        </svg>Diisi tanggal : ' . htmlspecialchars($dateStr) . '</div>';
                                }
                                $tr = '<tr>';
                                $tr .= '<td style="border: 1px solid #cbd5e1; padding: 12px 14px; vertical-align: top; font-weight: 500; color: #334155;">' . $waktu . '</td>';
                                $tr .= '<td style="border: 1px solid #cbd5e1; padding: 12px 14px; vertical-align: top; color: #1e293b;">' . $clean_content . $dateHtml . '</td>';
                                $tr .= '</tr>';
                                return $tr;
                            };

                            $html_day .= $addRow('Pagi<br><span style="font-size: 11.5px; color: #64748b; font-weight: normal;">08.00 sd 11.00</span>', $pagi_clean, $pagi_ts);
                            $html_day .= $addRow('Siang<br><span style="font-size: 11.5px; color: #64748b; font-weight: normal;">11.00 sd 14.00</span>', $siang_clean, $siang_ts);
                            $html_day .= $addRow('Sore<br><span style="font-size: 11.5px; color: #64748b; font-weight: normal;">11.00 sd 17.00</span>', $sore_clean, $sore_ts);

                            $html_day .= '</tbody></table>';
                            $d->$f = $html_day;
                        } else {
                            $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">' . $header . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                        }
					} else {
						$cleaned = $this->_clean_html_for_mpdf($v);
                        if (empty(trim(strip_tags($cleaned)))) {
                            $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                        } else {
    						$d->$f   = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div>'
    						         . '<div style="padding-left: 15px;">' . $cleaned . '</div>';
                        }
					}
				} else {
                    $d->$f = '<div style="margin-bottom: 4px;"><span style="font-weight: bold; font-size: 13px;">Hari ke ' . $i . '</span></div><div style="padding-left: 15px;"><span style="font-style: italic; color: #777777;">Kolom belum diisi</span></div>';
                }
			}
			$d->pengetahuan_baru = $this->_clean_html_for_mpdf($d->pengetahuan_baru);
            if (empty(trim(strip_tags($d->pengetahuan_baru)))) $d->pengetahuan_baru = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->keterampilan_baru = $this->_clean_html_for_mpdf($d->keterampilan_baru);
            if (empty(trim(strip_tags($d->keterampilan_baru)))) $d->keterampilan_baru = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->tujuan_internship = $this->_clean_html_for_mpdf($d->tujuan_internship);
            if (empty(trim(strip_tags($d->tujuan_internship)))) $d->tujuan_internship = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			
			$d->catatan_lain = $this->_clean_html_for_mpdf($d->catatan_lain);
            if (empty(trim(strip_tags($d->catatan_lain)))) $d->catatan_lain = '<span style="font-style: italic; color: #777777;">Kolom belum diisi</span>';
			$d->review_dpl = $this->_clean_html_for_mpdf($d->review_dpl);
		}

		$output['data'] = $data;

		$mhs = $this->db->where('nipd', $nipd)->from('mahasiswa_pt mp')->join('mahasiswa m', 'mp.id_pd=m.id_pd')->get()->row();
		$nm_pd = $mhs->nm_pd;
		$nm_lemb = $this->db->where('id_sms', $mhs->id_sms)->get('sms')->row()->nm_lemb;
		$output['nm_pd'] = $nm_pd;
		$output['nipd'] = $nipd;
		$output['nm_lemb'] = $nm_lemb;

		$lokasi = $this->db->where('nipd', $nipd)->get('simlapkerma_lokasi_magang');
		if ($lokasi->num_rows() > 0) {
			$output['lokasi'] = $lokasi->row()->nama_perusahaan;
			$output['tahun_magang'] = $lokasi->row()->tahun_magang;
		} else {
			$output['lokasi'] = 'Nama Perusahaan Belum Diisi';
			$output['tahun_magang'] = 'Tahun Magang Belum Diisi';
		}

		$this->load->view('cetak/cetak_tracking_html', $output);
	}

	private function _validate()
	{
		$data = [];
		$data['error_string'] = [];
		$data['inputerror'] = [];
		$data['status'] = TRUE;

		if ($this->input->post('week') == '') {
			$data['inputerror'][] = 'week';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}

		if ($this->input->post('periode_laporan') == '') {
			$data['inputerror'][] = 'periode_laporan';
			$data['error_string'][] = 'Harus Diisi.';
			$data['status'] = FALSE;
		}






		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
