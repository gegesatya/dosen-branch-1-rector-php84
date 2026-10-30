<?php if (!empty($is_mpdf)): ?>
  <htmlpagefooter name="docFooter">
    <div style="border-top: 0.5px solid #cbd5e1; padding-top: 4px; font-size: 8pt; color: #64748b; font-family: Helvetica, sans-serif;">
      <table style="width: 100%; border: none !important; border-collapse: collapse;">
        <tr>
          <td style="width: 65%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: left;">
            Kontrak Pembelajaran - <?= clean_style($data_rps->nm_mk ?? ''); ?>
          </td>
          <td style="width: 35%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: right;">
            Halaman {PAGENO} dari {nbpg}
          </td>
        </tr>
      </table>
    </div>
  </htmlpagefooter>
  <sethtmlpagefooter name="docFooter" value="on" />
<?php endif; ?>

  <?php
  $tanggal_usulan = !empty($data_rps->tanggal_rps) ? date('d-m-Y', strtotime($data_rps->tanggal_rps)) : (!empty($data_rps->create_date) ? date('d-m-Y', strtotime($data_rps->create_date)) : date('d-m-Y'));
  $tanggal_pengesahan = !empty($data_rps->tanggal_pengesahan) ? date('d-m-Y', strtotime($data_rps->tanggal_pengesahan)) : '-';

  $logo_path = FCPATH . 'assets/image_web/logo-white-3.png';
  $logo_src = file_exists($logo_path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path)) : (function_exists('base_url') ? base_url('assets/image_web/logo-white-3.png') : 'assets/image_web/logo-white-3.png');

  // Ambil data dosen pengampu dan NIDN secara presisi
  $ci =& get_instance();
  $nama_dosen = !empty($data_rps->dosen_pengampu) ? $data_rps->dosen_pengampu : '-';
  $nidn_dosen = '';
  if (!empty($data_rps->id_ptk)) {
      $dsn = $ci->db->where('id_ptk', $data_rps->id_ptk)->get('siakad_dosen')->row();
      if ($dsn && !empty($dsn->nidn)) $nidn_dosen = $dsn->nidn;
  }
  if (empty($nidn_dosen) && !empty($data_rps->dosen_pengampu)) {
      $dsn = $ci->db->where('nm_ptk', trim((string)$data_rps->dosen_pengampu))->get('siakad_dosen')->row();
      if ($dsn && !empty($dsn->nidn)) $nidn_dosen = $dsn->nidn;
  }
  if (empty($nidn_dosen) && !empty($data_rps->dosen_pengampu)) {
      $dparts = explode(',', $data_rps->dosen_pengampu);
      $dsn = $ci->db->like('nm_ptk', trim($dparts[0]))->get('siakad_dosen')->row();
      if ($dsn && !empty($dsn->nidn)) $nidn_dosen = $dsn->nidn;
  }

  // Resolusi nama Program Studi yang akurat
  $nama_prodi = (!empty($nm_lemb) && $nm_lemb !== 'Politeknik Wilmar Bisnis Indonesia') ? $nm_lemb : '';
  if (empty($nama_prodi)) {
      if (!empty($data_rps->id_mk)) {
          $mkr = $ci->db->where('id_mk', $data_rps->id_mk)->get('feeder_mata_kuliah')->row();
          if ($mkr && !empty($mkr->id_sms)) {
              $sms_r = $ci->db->where('id_sms', $mkr->id_sms)->get('sms')->row();
              if ($sms_r && !empty($sms_r->nm_lemb)) $nama_prodi = $sms_r->nm_lemb;
          }
      }
      if (empty($nama_prodi) && !empty($data_rps->kode_mk)) {
          $mkr = $ci->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
          if ($mkr && !empty($mkr->id_sms)) {
              $sms_r = $ci->db->where('id_sms', $mkr->id_sms)->get('sms')->row();
              if ($sms_r && !empty($sms_r->nm_lemb)) $nama_prodi = $sms_r->nm_lemb;
          }
      }
      if (empty($nama_prodi) && !empty($data_rps->kaprodi_rps)) {
          $kap_r = $ci->db->where('id_ptk', $data_rps->kaprodi_rps)->get('kaprodi')->row();
          if ($kap_r && !empty($kap_r->nama)) $nama_prodi = $kap_r->nama;
      }
      if (empty($nama_prodi) && !empty($data_rps->id_ptk)) {
          $dsn_sms = $ci->db->query("SELECT s.nm_lemb FROM siakad_dosen d JOIN sms s ON d.id_sms = s.id_sms WHERE d.id_ptk = ? LIMIT 1", [$data_rps->id_ptk])->row();
          if ($dsn_sms && !empty($dsn_sms->nm_lemb)) $nama_prodi = $dsn_sms->nm_lemb;
      }
      if (empty($nama_prodi)) {
          $nama_prodi = !empty($nm_lemb) ? $nm_lemb : 'Politeknik Wilmar Bisnis Indonesia';
      }
  }

  $nama_kaprodi = !empty($kaprodi_info->nama) ? $kaprodi_info->nama : ((!empty($kaprodi_rps) && is_object($kaprodi_rps) && method_exists($kaprodi_rps, 'num_rows') && $kaprodi_rps->num_rows() > 0) ? $kaprodi_rps->row()->nm_koordinator : '');
  $nidn_kaprodi = !empty($kaprodi_info->nidn) ? $kaprodi_info->nidn : '';

  if (!function_exists('clean_kontrak_markdown_string')) {
      function clean_kontrak_markdown_string($str) {
          if (empty($str)) return '';

          // Bersihkan code fences markdown (```json, ```markdown, ```)
          $str = preg_replace('/^```[a-zA-Z0-9_\-]*\s*$/m', '', $str);
          $str = str_replace('```', '', $str);

          if (function_exists('clean_word_and_html_junk')) {
              $str = clean_word_and_html_junk($str);
          } else {
              // 1. Hapus conditional comment Microsoft Word & HTML comments
              $str = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $str);
              $str = preg_replace('/<!--[\s\S]*?-->/', '', $str);

              // 2. Hapus blok style, script, xml
              $str = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $str);
              $str = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $str);
              $str = preg_replace('/<xml\b[^>]*>[\s\S]*?<\/xml>/i', '', $str);
              $str = preg_replace('/<\/?\w+:[^>]*>/i', '', $str);

              // 3. Bersihkan tag span/p/font
              $str = preg_replace('/<(?:span|font|o:p)[^>]*>/i', '', $str);
              $str = preg_replace('/<\/(?:span|font|o:p)>/i', '', $str);

              // 4. Bersihkan potongan teks style Word yang bocor
              $str = preg_replace('/(?:minor-latin|calibri|times new roman|arial|mso-[a-zA-Z0-9\-]+|font-family|font-size|line-height|margin|tab-stops)[^>\n]*["\'>]/i', '', $str);
              $str = preg_replace('/(?:mso-[a-zA-Z0-9\-]+|font-family|font-size|line-height|margin|text-align):[^;>\n]*;?/i', '', $str);
              $str = preg_replace('/["\']\s*>/', '', $str);
              $str = preg_replace('/^[\s"\'\>]*>/m', '', $str);

              // 5. Bersihkan atribut style, class, lang
              $str = preg_replace('/\s*(?:style|class|lang|align|valign|dir)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $str);
          }

          // Hapus penanda draf/validasi
          $str = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $str);
          $str = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $str);

          // Hapus sisa backticks
          $str = str_replace('`', '', $str);

          // Hapus baris horizontal divider markdown (---, --, ***, ___)
          $str = preg_replace('/(?:^|\n)\s*[-*_]{2,}\s*(?=\n|\Z)/', "\n", $str);
          $str = preg_replace('/[-*_]{2,}\s*$/', '', $str);

          // Jika teks adalah HTML dari rich text editor lama (bukan tabel markdown),
          // rapikan tag HTML blok ke baris teratur
          if (preg_match('/<[a-z0-9]+/i', $str) && strpos($str, '|') === false) {
              $str = preg_replace('/<\/(?:p|div|tr|h[1-6])>/i', "\n", $str);
              $str = preg_replace('/<br\s*\/?>/i', "\n", $str);
              $str = preg_replace('/<li[^>]*>/i', "• ", $str);
              $str = preg_replace('/<\/li>/i', "\n", $str);
              $str = strip_tags($str, '<b><strong><i><em><u><table><tr><td><th>');
              $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
              $str = str_replace("\xc2\xa0", ' ', $str);
          }

          return trim($str);
      }
  }

  if (!function_exists('render_kontrak_skala_nilai_wbi')) {
      function render_kontrak_skala_nilai_wbi() {
          $html = '<div style="margin-top:14px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">Skala Nilai Huruf Politeknik Wilmar Bisnis Indonesia</div>';
          $html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:4px; margin-bottom:8px; font-size:8.5pt; border:1px solid #7a7a7a; table-layout:fixed;">';
          $html .= '<colgroup>';
          $html .= '<col style="width:25%;" />';
          $html .= '<col style="width:25%;" />';
          $html .= '<col style="width:25%;" />';
          $html .= '<col style="width:25%;" />';
          $html .= '</colgroup>';
          $html .= '<thead><tr style="background-color:#f8fafc;">';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; font-weight:700; font-size:8.5pt; color:#0f172a;">Nilai Angka</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; font-weight:700; font-size:8.5pt; color:#0f172a;">Nilai Huruf</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; font-weight:700; font-size:8.5pt; color:#0f172a;">Bobot</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 8px; text-align:left; font-weight:700; font-size:8.5pt; color:#0f172a;">Kategori</th>';
          $html .= '</tr></thead><tbody>';

          $skala = [
              ['>= 85', 'A', '4.00', 'Istimewa'],
              ['75 - 84', 'B+', '3.50', 'Sangat Baik'],
              ['70 - 74', 'B', '3.00', 'Baik'],
              ['65 - 69', 'C+', '2.50', 'Cukup Baik'],
              ['55 - 64', 'C', '2.00', 'Cukup'],
              ['45 - 54', 'D', '1.00', 'Kurang'],
              ['< 45', 'E', '0.00', 'Gagal']
          ];

          foreach ($skala as $s) {
              $html .= '<tr>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:5px 8px; text-align:center; font-size:8.5pt;">' . htmlspecialchars($s[0]) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:5px 8px; text-align:center; font-weight:bold; font-size:8.5pt;">' . htmlspecialchars($s[1]) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:5px 8px; text-align:center; font-size:8.5pt;">' . htmlspecialchars($s[2]) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:5px 8px; text-align:left; font-size:8.5pt;">' . htmlspecialchars($s[3]) . '</td>';
              $html .= '</tr>';
          }
          $html .= '</tbody></table>';
          return $html;
      }
  }

  if (!function_exists('render_kontrak_matrix_blueprint_html')) {
      function render_kontrak_matrix_blueprint_html($m_h_row, $m_b_rows, $custom_title = '') {
          if (empty($m_h_row) || empty($m_b_rows)) return '';

          $m_num_cols = count($m_h_row);
          if (empty($custom_title)) {
              $custom_title = 'Matriks Distribusi Bobot Asesmen terhadap Sub-CPMK & CPMK (Blueprint Asesmen OBE)';
          }

          $format_cell = function($c) {
              $c_clean = htmlspecialchars($c);
              $c_clean = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><i>$1</i></strong>', $c_clean);
              $c_clean = preg_replace('/\*\*_(.*?)_\*\*/s', '<strong><i>$1</i></strong>', $c_clean);
              $c_clean = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $c_clean);
              $c_clean = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<strong>$1</strong>', $c_clean);
              $c_clean = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $c_clean);
              $c_clean = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $c_clean);
              return $c_clean;
          };

          $m_col_widths = [];
          $has_sub_cpmk = ($m_num_cols >= 9 && stripos(implode(' ', array_slice($m_h_row, 0, 3)), 'sub') !== false);

          if ($has_sub_cpmk) {
              $w_cpmk = 8;
              $w_sub = 10;
              $w_materi = ($m_num_cols >= 11) ? 26 : 28;
              $w_bobot_mk = 9;
              $w_bobot_cpmk = 9;
              $num_middle = $m_num_cols - 5;
              $remain = 100 - ($w_cpmk + $w_sub + $w_materi + $w_bobot_mk + $w_bobot_cpmk);
              $w_mid = ($num_middle > 0) ? round($remain / $num_middle, 2) : 6;

              $m_col_widths[0] = $w_cpmk . '%';
              $m_col_widths[1] = $w_sub . '%';
              $m_col_widths[2] = $w_materi . '%';
              for ($i = 3; $i < $m_num_cols - 2; $i++) {
                  $m_col_widths[$i] = $w_mid . '%';
              }
              $m_col_widths[$m_num_cols - 2] = $w_bobot_mk . '%';
              $m_col_widths[$m_num_cols - 1] = $w_bobot_cpmk . '%';
          } elseif ($m_num_cols <= 2) {
              $w = round(100 / $m_num_cols, 1);
              $m_col_widths = array_fill(0, $m_num_cols, $w . '%');
          } elseif ($m_num_cols === 3) {
              $m_col_widths = ['15%', '65%', '20%'];
          } else {
              $w_kode = ($m_num_cols >= 8) ? 9 : 11;
              $w_last = ($m_num_cols >= 8) ? 13 : 13;
              $w_cpmk = ($m_num_cols >= 9) ? 26 : (($m_num_cols >= 8) ? 28 : (($m_num_cols >= 7) ? 32 : 36));
              $num_middle = $m_num_cols - 3;
              $remain = 100 - ($w_kode + $w_cpmk + $w_last);
              $w_mid = round($remain / $num_middle, 2);

              $m_col_widths[0] = $w_kode . '%';
              $m_col_widths[1] = $w_cpmk . '%';
              for ($i = 2; $i < $m_num_cols - 1; $i++) {
                  $m_col_widths[$i] = $w_mid . '%';
              }
              $m_col_widths[$m_num_cols - 1] = $w_last . '%';
          }

          $matrix_table_html = '<div style="margin-top:6px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">' . htmlspecialchars($custom_title) . '</div>';
          $matrix_table_html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:4px; margin-bottom:8px; font-size:8pt; border:1px solid #7a7a7a; table-layout:fixed;">';
          $matrix_table_html .= '<colgroup>';
          foreach ($m_col_widths as $cw) {
              $matrix_table_html .= '<col style="width:' . $cw . ';" />';
          }
          $matrix_table_html .= '</colgroup>';

          $matrix_table_html .= '<thead><tr style="background-color:#f8fafc;">';
          foreach ($m_h_row as $h_idx => $c) {
              $c_clean = $format_cell($c);
              $h_style = 'border:1px solid #7a7a7a; padding:6px 3px; text-align:center; vertical-align:middle; font-weight:700; line-height:1.2; font-size:7.5pt;';
              $matrix_table_html .= '<th style="' . $h_style . '">' . $c_clean . '</th>';
          }
          $matrix_table_html .= '</tr></thead><tbody>';

          // Auto-Kalkulasi Matematika Tepat untuk "Bobot thd CPMK" (dan Bobot thd MK)
          if ($has_sub_cpmk && $m_num_cols >= 9) {
              // 1. Kumpulkan total bobot MK per grup CPMK
              $cpmk_mk_totals = [];
              $active_cpmk_grp = '';
              foreach ($m_b_rows as $r_cells) {
                  $is_tot = (stripos(implode(' ', $r_cells), 'total') !== false || stripos(implode(' ', $r_cells), 'akumulasi') !== false);
                  if ($is_tot) continue;
                  
                  $cpmk_cell = trim(preg_replace('/[\*#`]+/', '', $r_cells[0] ?? ''));
                  if (!empty($cpmk_cell)) {
                      $active_cpmk_grp = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cpmk_cell));
                  }
                  if (empty($active_cpmk_grp)) continue;

                  $mk_val_raw = $r_cells[$m_num_cols - 2] ?? '0';
                  $mk_val = 0;
                  if (preg_match('/([\d\.]+)/', $mk_val_raw, $m_num)) {
                      $mk_val = (float)$m_num[1];
                  }
                  if (!isset($cpmk_mk_totals[$active_cpmk_grp])) {
                      $cpmk_mk_totals[$active_cpmk_grp] = 0;
                  }
                  $cpmk_mk_totals[$active_cpmk_grp] += $mk_val;
              }

              // 2. Koreksi nilai kolom "Bobot thd CPMK" di setiap baris data
              $current_cpmk_grp = '';
              foreach ($m_b_rows as &$r_cells) {
                  $is_tot = (stripos(implode(' ', $r_cells), 'total') !== false || stripos(implode(' ', $r_cells), 'akumulasi') !== false);
                  if ($is_tot) {
                      $r_cells[$m_num_cols - 1] = '-';
                      continue;
                  }

                  $row_cpmk = trim(preg_replace('/[\*#`]+/', '', $r_cells[0] ?? ''));
                  if (!empty($row_cpmk)) {
                      $current_cpmk_grp = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $row_cpmk));
                  }

                  if (!empty($current_cpmk_grp) && isset($cpmk_mk_totals[$current_cpmk_grp])) {
                      $grp_total = $cpmk_mk_totals[$current_cpmk_grp];
                      $mk_val_raw = $r_cells[$m_num_cols - 2] ?? '0';
                      $mk_val = 0;
                      if (preg_match('/([\d\.]+)/', $mk_val_raw, $m_num)) {
                          $mk_val = (float)$m_num[1];
                      }

                      if ($grp_total > 0) {
                          $exact_pct = round(($mk_val / $grp_total) * 100, 2);
                          if (abs($exact_pct - round($exact_pct)) < 0.01) {
                              $r_cells[$m_num_cols - 1] = intval(round($exact_pct)) . '%';
                          } else {
                              $r_cells[$m_num_cols - 1] = number_format($exact_pct, 1, '.', '') . '%';
                          }
                      }
                  }
              }
              unset($r_cells);
          }

          // Hitung rowspan untuk merge sel CPMK (Kolom 0) yang bernilai sama
          $cpmk_rowspans = [];
          if ($has_sub_cpmk) {
              $r_idx = 0;
              $total_rows_count = count($m_b_rows);
              while ($r_idx < $total_rows_count) {
                  $cells = $m_b_rows[$r_idx];
                  $is_total = (stripos(implode(' ', $cells), 'total') !== false || stripos(implode(' ', $cells), 'akumulasi') !== false);
                  if ($is_total) {
                      $cpmk_rowspans[$r_idx] = 1;
                      $r_idx++;
                      continue;
                  }

                  $current_cpmk = trim(preg_replace('/[\*#`]+/', '', $cells[0] ?? ''));
                  $span = 1;
                  for ($next = $r_idx + 1; $next < $total_rows_count; $next++) {
                      $next_cells = $m_b_rows[$next];
                      $next_is_total = (stripos(implode(' ', $next_cells), 'total') !== false || stripos(implode(' ', $next_cells), 'akumulasi') !== false);
                      if ($next_is_total) break;

                      $next_cpmk = trim(preg_replace('/[\*#`]+/', '', $next_cells[0] ?? ''));
                      if (!empty($current_cpmk) && $current_cpmk === $next_cpmk) {
                          $span++;
                      } else {
                          break;
                      }
                  }

                  $cpmk_rowspans[$r_idx] = $span;
                  for ($s = 1; $s < $span; $s++) {
                      $cpmk_rowspans[$r_idx + $s] = 0;
                  }
                  $r_idx += $span;
              }
          }

          foreach ($m_b_rows as $row_idx => $cells) {
              $is_total = (stripos(implode(' ', $cells), 'total') !== false || stripos(implode(' ', $cells), 'akumulasi') !== false);
              $bg = $is_total ? 'background-color:#f1f5f9; font-weight:bold;' : '';
              $matrix_table_html .= '<tr style="' . $bg . '">';
              foreach ($cells as $c_idx => $c) {
                  if ($has_sub_cpmk && $c_idx === 0 && !$is_total) {
                      $span = $cpmk_rowspans[$row_idx] ?? 1;
                      if ($span === 0) {
                          continue;
                      }
                      $td_rowspan_attr = ($span > 1) ? ' rowspan="' . $span . '"' : '';
                      $c_clean = $format_cell($c);
                      $td_style = 'border:1px solid #7a7a7a; padding:5px 4px; text-align:center; vertical-align:middle; line-height:1.3; font-size:8pt; font-weight:bold; background-color:#ffffff;';
                      $matrix_table_html .= '<td' . $td_rowspan_attr . ' style="' . $td_style . '">' . $c_clean . '</td>';
                      continue;
                  }

                  $align = 'center';
                  if (!$is_total) {
                      if ($has_sub_cpmk && $c_idx === 2) {
                          $align = 'left';
                      } elseif (!$has_sub_cpmk && $c_idx === 1) {
                          $align = 'left';
                      }
                  }
                  $c_clean = $format_cell($c);
                  $td_style = 'border:1px solid #7a7a7a; padding:5px 4px; text-align:' . $align . '; vertical-align:middle; line-height:1.3; font-size:8pt;';
                  if ($has_sub_cpmk) {
                      if ($c_idx === 0) {
                          $td_style .= ' font-weight:bold;';
                      } elseif ($c_idx >= $m_num_cols - 2) {
                          $td_style .= ' font-weight:bold; background-color:' . ($is_total ? '#e2e8f0' : '#fafafa') . ';';
                      }
                  } else {
                      if ($c_idx === 0) {
                          $td_style .= ' font-weight:bold;';
                      } elseif ($is_total && $c_idx === $m_num_cols - 1) {
                          $td_style .= ' background-color:#e2e8f0; font-weight:800;';
                      }
                  }
                  $matrix_table_html .= '<td style="' . $td_style . '">' . $c_clean . '</td>';
              }
              $matrix_table_html .= '</tr>';
          }
          $matrix_table_html .= '</tbody></table>';

          return $matrix_table_html;
      }
  }

  if (!function_exists('render_kontrak_markdown_table')) {
      function render_kontrak_markdown_table($text) {
          if (strpos($text, '|') === false) return null;

          $text = clean_kontrak_markdown_string($text);
          $lines = explode("\n", str_replace("\r", "", $text));

          $format_cell = function($c) {
              $c_clean = htmlspecialchars($c);
              $c_clean = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><i>$1</i></strong>', $c_clean);
              $c_clean = preg_replace('/\*\*_(.*?)_\*\*/s', '<strong><i>$1</i></strong>', $c_clean);
              $c_clean = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $c_clean);
              $c_clean = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<strong>$1</strong>', $c_clean);
              $c_clean = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $c_clean);
              $c_clean = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $c_clean);
              return $c_clean;
          };

          // Pisahkan teks menjadi blok-blok bergantian: teks vs tabel
          $blocks = [];
          $current_type = 'text';
          $current_lines = [];

          foreach ($lines as $line) {
              $trim = trim($line);
              $is_tbl_line = (strpos($trim, '|') !== false && (substr($trim, 0, 1) === '|' || substr($trim, -1) === '|'));

              if ($is_tbl_line) {
                  if ($current_type === 'text') {
                      if (!empty($current_lines)) {
                          $blocks[] = ['type' => 'text', 'lines' => $current_lines];
                          $current_lines = [];
                      }
                      $current_type = 'table';
                  }
                  $current_lines[] = $trim;
              } else {
                  if ($current_type === 'table') {
                      if (!empty($current_lines)) {
                          $blocks[] = ['type' => 'table', 'lines' => $current_lines];
                          $current_lines = [];
                      }
                      $current_type = 'text';
                  }
                  $current_lines[] = $line;
              }
          }
          if (!empty($current_lines)) {
              $blocks[] = ['type' => $current_type, 'lines' => $current_lines];
          }

          $html = '';
          $found_table = false;

          foreach ($blocks as $block) {
              if ($block['type'] === 'text') {
                  $txt = trim(implode("\n", $block['lines']));
                  if (!empty($txt)) {
                      $html .= render_kontrak_text($txt);
                  }
              } elseif ($block['type'] === 'table') {
                  $rows = [];
                  foreach ($block['lines'] as $tline) {
                      if (preg_match('/^\|?[\s\-:|]+\|?$/', $tline)) continue;
                      $cells = array_values(array_filter(array_map('trim', explode('|', $tline)), function($v) { return true; }));
                      if (substr($tline, 0, 1) === '|' && isset($cells[0]) && $cells[0] === '') array_shift($cells);
                      if (substr($tline, -1) === '|' && isset($cells[count($cells)-1]) && $cells[count($cells)-1] === '') array_pop($cells);
                      if (!empty($cells)) $rows[] = $cells;
                  }

                  if (count($rows) < 2) continue;
                  $found_table = true;

                  $header_row = $rows[0];
                  $body_rows = array_slice($rows, 1);
                  $num_cols = count($header_row);

                  $header_line = implode(' ', $header_row);
                  $is_matrix = ($num_cols >= 5 || stripos($header_line, 'Sub') !== false || stripos($header_line, 'UTS') !== false || stripos($header_line, 'UAS') !== false || stripos($header_line, 'ETS') !== false || stripos($header_line, 'EAS') !== false || stripos($header_line, 'Bobot') !== false);

                  if ($is_matrix) {
                      $html .= render_kontrak_matrix_blueprint_html($header_row, $body_rows);
                  } else {
                      $html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:6px; margin-bottom:8px; font-size:9.5pt; border:1px solid #7a7a7a;">';
                      $html .= '<thead><tr style="background-color:#f1f5f9;">';
                      foreach ($header_row as $hcell) {
                          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; font-weight:bold; font-size:9.5pt;">' . $format_cell($hcell) . '</th>';
                      }
                      $html .= '</tr></thead><tbody>';

                      foreach ($body_rows as $brow) {
                          $is_total = (isset($brow[0]) && stripos($brow[0], 'Total') !== false) || (isset($brow[1]) && stripos($brow[1], 'Total') !== false);
                          $row_bg = $is_total ? 'background-color:#f8fafc; font-weight:bold;' : '';
                          $html .= '<tr style="' . $row_bg . '">';
                          foreach ($brow as $c_idx => $bcell) {
                              $align = ($c_idx === 0 || $c_idx >= ($num_cols - 1)) ? 'center' : 'left';
                              if ($is_total && $c_idx === 0) $align = 'center';
                              $html .= '<td style="border:1px solid #7a7a7a; padding:5px 8px; vertical-align:top; text-align:' . $align . '; font-size:9.5pt;">' . $format_cell($bcell) . '</td>';
                          }
                          $html .= '</tr>';
                      }
                      $html .= '</tbody></table>';
                  }
              }
          }

          return $found_table ? $html : null;
      }
  }

  if (!function_exists('render_kontrak_kriteria_penilaian_section')) {
      function render_kontrak_kriteria_penilaian_section($data_rps, $data_rps_detail = []) {
          $ci =& get_instance();

          // 1. Kumpulkan seluruh sumber teks kandidat
          $candidates = [];
          if (!empty($data_rps->kontrak_kriteria_penilaian)) {
              $candidates[] = $data_rps->kontrak_kriteria_penilaian;
          }
          if (!empty($data_rps->tujuan_belajar) && strpos($data_rps->tujuan_belajar, '|') !== false) {
              $candidates[] = $data_rps->tujuan_belajar;
          }
          if (!empty($data_rps->raw_ai_output)) {
              $candidates[] = $data_rps->raw_ai_output;
          }

          // 2. Cari tabel matriks blueprint dari teks kandidat
          $matrix_table = null;
          $intro_text = '';

          $parse_all_tables_fn = function($text) {
              $lines = explode("\n", str_replace("\r", "", $text));
              $tables = [];
              $curr_tbl = [];
              $curr_before = [];
              $in_tbl = false;

              foreach ($lines as $line) {
                  $trim = trim($line);
                  $is_row = (strpos($trim, '|') !== false && (substr($trim, 0, 1) === '|' || substr($trim, -1) === '|'));
                  if ($is_row) {
                      $in_tbl = true;
                      $curr_tbl[] = $trim;
                  } else {
                      if ($in_tbl) {
                          $tables[] = ['lines' => $curr_tbl, 'before' => $curr_before];
                          $curr_tbl = [];
                          $curr_before = [];
                          $in_tbl = false;
                      }
                      $curr_before[] = $line;
                  }
              }
              if (!empty($curr_tbl)) {
                  $tables[] = ['lines' => $curr_tbl, 'before' => $curr_before];
              }
              return $tables;
          };

          $parse_rows_fn = function($tbl_lines) {
              $rows = [];
              foreach ($tbl_lines as $tline) {
                  if (preg_match('/^\|?[\s\-:|]+\|?$/', $tline)) continue;
                  $cells = array_values(array_filter(array_map('trim', explode('|', $tline)), function($v) { return true; }));
                  if (substr($tline, 0, 1) === '|' && isset($cells[0]) && $cells[0] === '') array_shift($cells);
                  if (substr($tline, -1) === '|' && isset($cells[count($cells)-1]) && $cells[count($cells)-1] === '') array_pop($cells);
                  if (!empty($cells)) $rows[] = $cells;
              }
              return $rows;
          };

          foreach ($candidates as $cand) {
              $tbls = $parse_all_tables_fn($cand);
              foreach ($tbls as $t) {
                  $rows = $parse_rows_fn($t['lines']);
                  if (empty($rows)) continue;
                  $header_line = implode(' ', $rows[0]);
                  $num_cols = count($rows[0]);
                  $full_str = implode("\n", $t['lines']);

                  $is_matrix = (stripos($header_line, 'Sikap') !== false || stripos($header_line, 'Tugas') !== false || stripos($header_line, 'UTS') !== false || stripos($header_line, 'UAS') !== false || stripos($header_line, 'Kuis') !== false || stripos($header_line, 'Bobot') !== false || stripos($full_str, 'TOTAL BOBOT') !== false || $num_cols >= 5);

                  // Hindari tabel skala nilai (4 kolom: Nilai Angka, Nilai Huruf, Bobot, Kategori)
                  if ($num_cols === 4 && stripos($header_line, 'Huruf') !== false && stripos($header_line, 'Kategori') !== false) {
                      continue;
                  }
                  // Hindari tabel rumusan CPMK vs CPL 3 kolom
                  if ($num_cols === 3 && stripos($header_line, 'Rumusan CPMK') !== false) {
                      continue;
                  }

                  if ($is_matrix && $matrix_table === null) {
                      $matrix_table = $rows;
                      $filtered_intro = [];
                      foreach ($t['before'] as $l) {
                          $l_trim = trim($l);
                          if (empty($l_trim)) continue;
                          // Abaikan jika baris adalah heading atau judul tabel matriks/asesmen/kriteria
                          if (preg_match('/^(?:#{1,6}\s*)*(?:(?:[0-9A-Za-z]+[\.\)]|[a-z]\.?\d*)\s*)*(?:KRITERIA|BOBOT|PENILAIAN|TUJUAN BELAJAR|MATRIKS|BLUEPRINT|TABEL|SKALA NILAI)/i', $l_trim)) {
                              continue;
                          }
                          if (stripos($l_trim, 'Matriks Distribusi') !== false || stripos($l_trim, 'Blueprint Asesmen') !== false || stripos($l_trim, 'Bobot Asesmen') !== false) {
                              continue;
                          }
                          $filtered_intro[] = $l_trim;
                      }
                      if (!empty($filtered_intro)) {
                          $intro_text = implode("\n", $filtered_intro);
                      }
                      break 2;
                  }
              }
          }

          $out_html = '';

          // A. Paragraf Pengantar Singkat (hanya jika ada narasi penjelasan yang bukan judul)
          if (!empty($intro_text)) {
              $clean_intro = trim(render_kontrak_text($intro_text));
              if (!empty($clean_intro)) {
                  $out_html .= $clean_intro;
              }
          }

          // B. TABEL 1: Matriks Distribusi Bobot Asesmen terhadap Sub-CPMK & CPMK (Blueprint Asesmen OBE)
          if ($matrix_table !== null && count($matrix_table) >= 2) {
              $h_row = $matrix_table[0];
              $b_rows = array_slice($matrix_table, 1);
              $out_html .= render_kontrak_matrix_blueprint_html($h_row, $b_rows, 'Matriks Distribusi Bobot Asesmen terhadap Sub-CPMK & CPMK (Blueprint Asesmen OBE)');
          } else {
              // Fallback jika tidak ada matriks markdown: ekstrak CPMK dan susun tabel matriks dinamis
              $cpmks = [];
              $cp_text = !empty($data_rps->cp_mata_kuliah) ? $data_rps->cp_mata_kuliah : (!empty($data_rps->tujuan_belajar) ? $data_rps->tujuan_belajar : '');
              if (function_exists('extract_cpmk_items')) {
                  $cpmks = extract_cpmk_items($cp_text);
              }
              if (empty($cpmks)) {
                  $cpmks = [
                      ['kode' => 'CPMK-1', 'desc' => 'Menguasai konsep dan dasar mata kuliah secara komprehensif.'],
                      ['kode' => 'CPMK-2', 'desc' => 'Mampu menganalisis dan memecahkan permasalahan kontekstual.'],
                      ['kode' => 'CPMK-3', 'desc' => 'Mampu mengimplementasikan solusi kreatif dan menyusun laporan evaluasi.']
                  ];
              }
              if (function_exists('generate_cpmk_matrix_html')) {
                  $out_html .= generate_cpmk_matrix_html($cpmks, $data_rps_detail);
              }
          }

          // C. TABEL 2: Skala Nilai Huruf Politeknik Wilmar Bisnis Indonesia
          $out_html .= render_kontrak_skala_nilai_wbi();

          return $out_html;
      }
  }

  if (!function_exists('render_kontrak_text')) {
      function render_kontrak_text($str) {
          if (empty($str)) return '';
          $str = clean_kontrak_markdown_string($str);

          $format_md = function($txt) {
              $t = htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
              $t = preg_replace('/&amp;([a-zA-Z0-9#]+;)/', '&$1', $t); // restore safe entities
              $t = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><i>$1</i></strong>', $t);
              $t = preg_replace('/\*\*_(.*?)_\*\*/s', '<strong><i>$1</i></strong>', $t);
              $t = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $t);
              $t = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<strong>$1</strong>', $t);
              $t = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $t);
              $t = preg_replace('/#{1,6}\s*/', '', $t);
              $t = str_replace(['***', '**'], '', $t);
              return $t;
          };

          $raw_lines = explode("\n", str_replace("\r", "", $str));

          $is_intro_line = function($text) {
              $t = strtolower(trim($text));
              return (
                  strpos($t, 'sebagai berikut') !== false ||
                  strpos($t, 'antara lain') !== false ||
                  strpos($t, 'meliputi') !== false ||
                  strpos($t, 'adalah:') !== false ||
                  strpos($t, 'berikut ini:') !== false ||
                  strpos($t, 'ketentuan berikut:') !== false ||
                  strpos($t, 'tata tertib berikut:') !== false ||
                  strpos($t, 'aturan berikut:') !== false
              );
          };

          $is_subheading = function($text) use ($is_intro_line) {
              $trim = trim($text);
              if (empty($trim)) return false;
              if ($is_intro_line($trim)) return false;

              // Bersihkan bullet/nomor awal dan markdown
              $clean = preg_replace('/^(?:[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}\x{2013}\x{2014}\-\*\+\•]|\d+[\.\)]|[a-zA-Z][\.\)])\s*/u', '', $trim);
              $clean = trim($clean, "*#_ `\t\n\r\0\x0B");

              // Cek jika berakhiran titik dua dan merupakan judul singkat (3-60 karakter)
              if (preg_match('/^[A-Za-z0-9\s&\/\-–—\(\)\.,]{3,60}:$/u', $clean)) {
                  return $clean;
              }
              return false;
          };

          $merged_blocks = [];
          $current_heading = null;
          $current_heading_body = [];

          $flush_heading = function() use (&$current_heading, &$current_heading_body, &$merged_blocks, $format_md) {
              if ($current_heading !== null) {
                  $head_text = $format_md(rtrim($current_heading, ':'));
                  if (count($current_heading_body) > 1) {
                      // Sub-list bertingkat jika memiliki lebih dari 1 butir poin: a., b., c., dst.
                      $sub_html = '<strong>' . $head_text . ':</strong>';
                      $sub_html .= '<table style="width:100%; border:none !important; border-collapse:collapse; margin:2px 0 2px 0;"><tbody>';
                      $letters = range('a', 'z');
                      foreach ($current_heading_body as $idx => $b_line) {
                          $lbl = isset($letters[$idx]) ? $letters[$idx] . '.' : ($idx + 1) . '.';
                          $sub_html .= '<tr>';
                          $sub_html .= '<td style="width:18px; min-width:18px; vertical-align:top; border:none !important; padding:1px 3px 2px 0; font-size:10pt; line-height:1.55; text-align:left;">' . $lbl . '</td>';
                          $sub_html .= '<td style="vertical-align:top; border:none !important; padding:1px 0 2px 0; font-size:10pt; line-height:1.55; text-align:justify;">' . $format_md($b_line) . '</td>';
                          $sub_html .= '</tr>';
                      }
                      $sub_html .= '</tbody></table>';
                      $merged_blocks[] = [
                          'type' => 'list_item_html',
                          'text' => $sub_html
                      ];
                  } elseif (count($current_heading_body) === 1) {
                      $merged_blocks[] = [
                          'type' => 'list_item_html',
                          'text' => '<strong>' . $head_text . ':</strong> ' . $format_md($current_heading_body[0])
                      ];
                  } else {
                      $merged_blocks[] = [
                          'type' => 'list_item_html',
                          'text' => '<strong>' . $head_text . ':</strong>'
                      ];
                  }
                  $current_heading = null;
                  $current_heading_body = [];
              }
          };

          foreach ($raw_lines as $line) {
              $trim = trim($line);
              if (empty($trim)) {
                  $flush_heading();
                  continue;
              }

              // Bersihkan awalan header markdown
              $trim = preg_replace('/^#{1,6}\s*/', '', $trim);

              // Cek jika baris adalah kalimat pengantar
              if ($is_intro_line($trim)) {
                  $flush_heading();
                  $merged_blocks[] = [
                      'type' => 'paragraph_html',
                      'text' => $format_md($trim)
                  ];
                  continue;
              }

              // Cek apakah baris ini sub-heading
              $sub_title = $is_subheading($trim);
              if ($sub_title !== false) {
                  $flush_heading();
                  $current_heading = $sub_title;
                  continue;
              }

              // Jika sedang mengumpulkan body dari sub-heading
              if ($current_heading !== null) {
                  $clean_body_line = preg_replace('/^(?:[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}\x{2013}\x{2014}\-\*\+\•]|\d+[\.\)]|[a-zA-Z][\.\)])\s+/u', '', $trim);
                  $current_heading_body[] = $clean_body_line;
                  continue;
              }

              // Cek list item biasa atau CPMK
              if (preg_match('/^(?:[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}\x{2013}\x{2014}\-\*\+\•]|\d+[\.\)]|[a-zA-Z][\.\)])\s+(.*)$/u', $trim, $m)) {
                  $item_text = $m[1];
                  if (preg_match('/^([A-Za-z0-9\s&\/\-–—\(\)]{2,50}:)\s*(.*)$/u', $item_text, $jm)) {
                      $item_text = '<strong>' . $format_md(trim($jm[1])) . '</strong> ' . $format_md($jm[2]);
                  } else {
                      $item_text = $format_md($item_text);
                  }
                  $merged_blocks[] = [
                      'type' => 'list_item_html',
                      'text' => $item_text
                  ];
              } elseif (preg_match('/^(?:CPMK\s*\d+[:\.\-])\s*(.*)$/i', $trim, $m)) {
                  $colon_pos = strpos($trim, ':') !== false ? strpos($trim, ':') + 1 : 7;
                  $head_cpmk = substr($trim, 0, $colon_pos);
                  $body_cpmk = substr($trim, $colon_pos);
                  $merged_blocks[] = [
                      'type' => 'list_item_html',
                      'text' => '<strong>' . $format_md(trim($head_cpmk)) . '</strong> ' . $format_md(trim($body_cpmk))
                  ];
              } else {
                  $merged_blocks[] = [
                      'type' => 'paragraph_html',
                      'text' => $format_md($trim)
                  ];
              }
          }
          $flush_heading();

          $out = '';
          $list_items = [];

          $flush_list = function(&$items) {
              if (empty($items)) return '';
              $html = '<table style="width:100%; border:none !important; border-collapse:collapse; margin:4px 0 6px 0;"><tbody>';
              $num = 1;
              foreach ($items as $it) {
                  $html .= '<tr>';
                  $html .= '<td style="width:24px; min-width:24px; vertical-align:top; border:none !important; padding:2px 4px 4px 0; font-size:10pt; line-height:1.55; text-align:left;">' . $num . '.</td>';
                  $html .= '<td style="vertical-align:top; border:none !important; padding:2px 0 4px 0; font-size:10pt; line-height:1.55; text-align:justify;">' . $it . '</td>';
                  $html .= '</tr>';
                  $num++;
              }
              $html .= '</tbody></table>';
              $items = [];
              return $html;
          };

          foreach ($merged_blocks as $block) {
              if ($block['type'] === 'list_item_html') {
                  $list_items[] = $block['text'];
              } else {
                  $out .= $flush_list($list_items);
                  $out .= '<p style="margin:4px 0 6px 0; line-height:1.6; font-size:10pt; text-align:justify;">' . $block['text'] . '</p>';
              }
          }
          $out .= $flush_list($list_items);

          return $out;
      }
  }

  if (!function_exists('render_kontrak_content')) {
      function render_kontrak_content($content, $fallback_html = '') {
          if (empty($content) || trim($content) === '') return $fallback_html;
          $content = clean_kontrak_markdown_string($content);
          if (empty($content)) return $fallback_html;

          if (strpos($content, '<table') !== false) {
              return clean_style($content);
          }
          $parsed_table = render_kontrak_markdown_table($content);
          if ($parsed_table !== null) {
              return $parsed_table;
          }
          return render_kontrak_text($content);
      }
  }
  ?>

  <!-- HEADER KOP INSTITUSI -->
  <table class="table table-black" style="width: 100%; border-collapse: collapse; margin-bottom: 0 !important;">
    <tbody>
      <tr>
        <td style="width: 25%; text-align: center; padding: 8px 10px; border-bottom: none;">
          <img src="<?= $logo_src; ?>" width="128px" />
        </td>
        <td style="width: 75%; text-align: center; vertical-align: middle; border-bottom: none;">
          <h4 style="font-size: 14px; margin: 0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px; margin: 0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px; margin: 0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- TABEL IDENTITAS & PENGESAHAN KONTRAK (3 KOLOM PRESISI: 33.33% / 33.33% / 33.33%) -->
  <table class="table table-black" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 0 !important;">
    <tbody>
      <tr>
        <td class="text-center" colspan="3" style="padding: 10px 10px 9px 10px;">
          <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-bottom: 6px; letter-spacing: 0.3px;">
            PROGRAM STUDI <?= preg_replace('/^Program\s+Studi\s+/i', '', $nama_prodi); ?>
          </div>
          <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-bottom: 10px; letter-spacing: 0.3px;">
            KONTRAK PEMBELAJARAN
          </div>
          <div style="font-size: 8.5pt; font-weight: bold; color: #475569; letter-spacing: 0.5px;">
            FORMULIR SPMI (SISTEM PENJAMINAN MUTU INTERNAL)
          </div>
        </td>
      </tr>
      <tr>
        <td class="text-center" style="width: 33.333%; height: 22px; font-weight: bold; font-size: 9.5pt;">Pihak I</td>
        <td class="text-center" style="width: 33.333%; font-weight: bold; font-size: 9.5pt;">Pihak II</td>
        <td class="text-center" style="width: 33.334%; font-weight: bold; font-size: 9.5pt;">Disetujui</td>
      </tr>
      <tr>
        <td class="text-center" style="width: 33.333%; vertical-align: bottom; height: 85px; padding: 6px 8px;">
          <span style="font-size: 9.5pt;">Dosen Pengampu</span>
          <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <span style="font-size: 9.5pt; white-space: nowrap;"><u><?= clean_style($nama_dosen, ''); ?></u></span>
          <?= !empty($nidn_dosen) ? '<br><span style="font-size: 9pt; color: #334155;">NIDN. ' . $nidn_dosen . '</span>' : ''; ?>
        </td>
        <td class="text-center" style="width: 33.333%; vertical-align: bottom; height: 85px; padding: 6px 8px;">
          <span style="font-size: 9.5pt;">Perwakilan Mahasiswa</span>
          <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <span style="white-space: nowrap; font-size: 9.5pt;">( ........................ )</span><br>
          <span style="font-size: 9pt; color: #334155;">Komting Kelas / NIM. -</span>
        </td>
        <td class="text-center" style="width: 33.334%; vertical-align: bottom; height: 85px; padding: 6px 8px;">
          <span style="font-size: 9.5pt;">Ketua Program Studi</span>
          <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <span style="white-space: nowrap; font-size: 9.5pt;"><?= (!empty($nama_kaprodi)) ? '<u>' . clean_style($nama_kaprodi, '') . '</u>' : '( ........................ )'; ?></span>
          <?= !empty($nidn_kaprodi) ? '<br><span style="font-size: 9pt; color: #334155;">NIDN. ' . $nidn_kaprodi . '</span>' : ''; ?>
        </td>
      </tr>
      <tr>
        <td style="width: 33.333%; height: 20px; font-size: 9pt; padding: 4px 8px;">Tanggal : <?= $tanggal_usulan; ?></td>
        <td style="width: 33.333%; font-size: 9pt; padding: 4px 8px;">Tanggal : <?= $tanggal_usulan; ?></td>
        <td style="width: 33.334%; font-size: 9pt; padding: 4px 8px;">Tanggal : <?= $tanggal_usulan; ?></td>
      </tr>
    </tbody>
  </table>

  <!-- TABEL INFORMASI MATA KULIAH (2 KOLOM PRESISI 50% / 50% DEAD-CENTER) -->
  <table class="table table-black" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 3px !important;">
    <tbody>
      <tr>
        <td colspan="2" class="text-center" style="padding: 8px 10px;">
          <div style="font-size: 9pt; color: #475569; margin-bottom: 2px;">Mata Kuliah:</div>
          <strong style="font-size: 11.5pt; color: #0f172a; line-height: 1.35;"><?= $data_rps->nm_mk; ?><?= (!empty($data_rps->kode_mk) && stripos($data_rps->nm_mk, $data_rps->kode_mk) === false) ? ' (' . $data_rps->kode_mk . ')' : ''; ?></strong>
        </td>
      </tr>
      <tr>
        <td style="width: 50%; font-size: 9.5pt; padding: 6px 8px;">
          Semester:<br>
          <strong><?= $data_rps->semester; ?></strong>
        </td>
        <td style="width: 50%; font-size: 9.5pt; padding: 6px 8px;">
          Bobot (SKS):<br>
          <b><?= !empty($data_rps->sks_total_display) ? $data_rps->sks_total_display : ((int)$data_rps->sks_mk . ' SKS'); ?></b>
          <?php if (!empty($data_rps->sks_rincian_str)): ?>
            <span style="font-size: 8.5pt; font-weight: normal; color: #334155;">(<?= $data_rps->sks_rincian_str; ?>)</span>
          <?php else: ?>
            <span style="font-size: 8.5pt; font-weight: normal; color: #334155;">(TM: <?= (int)$data_rps->sks_tm; ?>, Prak: <?= (int)$data_rps->sks_prak; ?>)</span>
          <?php endif; ?>
        </td>
      </tr>
    </tbody>
  </table>
  <!-- SEKSI 1 SAMPAI 10 KONTRAK PERKULIAHAN (FLOWING DOCUMENT TANPA PEMBUNGKUS TABEL UTAMA) -->
  <div style="margin-top: 10px;">

    <!-- 1. DESKRIPSI MATA KULIAH -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      1. DESKRIPSI MATA KULIAH
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_deskripsi = preg_replace('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z\.\-_]+\s*)?(?:PRASYARAT|MATA KULIAH PRASYARAT)[^\n]*\n+[\s\S]*$/i', '', $data_rps->deskripsi_mata_kuliah ?? '');
      $clean_deskripsi = preg_replace('/^(?:[#*]{1,6}\s*)?(?:[0-9A-Za-z\.\-_]+\s*)?(?:DESKRIPSI SINGKAT MATA KULIAH|DESKRIPSI SINGKAT|DESKRIPSI MATA KULIAH|RINGKASAN MATA KULIAH|DESKRIPSI)(?:\*\*)?[:\s\-]*\n+/i', '', trim($clean_deskripsi));
      $clean_deskripsi = preg_replace('/(?:\n+|\A)(?:[#*_`\s]*)[0-9A-Za-z\.\-_]{1,10}(?:[#*_`\s]*)$/i', '', trim($clean_deskripsi));
      echo render_kontrak_content(trim($clean_deskripsi), '<p>Data belum diisi</p>');
      ?>
    </div>

    <!-- 2. MANFAAT MATA KULIAH -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      2. MANFAAT MATA KULIAH
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_manfaat = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:MANFAAT MATA KULIAH)(?:\*\*)?[:\s\-]*\n+/i', '', trim($data_rps->manfaat_mata_kuliah ?? ''));
      echo render_kontrak_content($clean_manfaat, '<p>Mata kuliah ini membekali mahasiswa dengan penguasaan kompetensi analitis dan praktis yang esensial, mendukung profil lulusan Program Studi, serta membentuk kesiapan profesional di dunia industri dan bisnis.</p>');
      ?>
    </div>

    <!-- 3. CP MATA KULIAH -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      3. CP MATA KULIAH
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $cp_raw = !empty($data_rps->cp_mata_kuliah) ? $data_rps->cp_mata_kuliah : (!empty($data_rps->tujuan_belajar) ? $data_rps->tujuan_belajar : '');
      $clean_cp = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:TUJUAN BELAJAR|CP MATA KULIAH|CPMK)(?:\*\*)?[:\s\-]*\n+/i', '', trim($cp_raw));
      echo render_kontrak_content($clean_cp, '<p>Data belum diisi</p>');
      ?>
    </div>

    <!-- 4. ANALISIS INSTRUKSIONAL/ORGANISASI MATERI -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      4. ANALISIS INSTRUKSIONAL/ORGANISASI MATERI
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_analisis = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:ANALISIS INSTRUKSIONAL|ORGANISASI MATERI)(?:\*\*)?[:\s\-]*\n+/i', '', trim($data_rps->analisis_instruksional ?? ''));
      echo render_kontrak_content($clean_analisis, '<p>Struktur materi tersusun secara berkesinambungan dari capaian fondasional hingga integrasi tingkat lanjut berbasis Capaian Pembelajaran Lulusan (CPL).</p>');
      ?>
      <?php if (!empty($data_rps->image_organisasi)): ?>
        <br><img src="<?= SOURCEPATH_RPS . $data_rps->image_organisasi; ?>" style="max-width: 100%; height: auto;" />
      <?php endif; ?>
    </div>

    <!-- 5. STRATEGI PERKULIAHAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      5. STRATEGI PERKULIAHAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_strat = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:STRATEGI PERKULIAHAN|METODE PERKULIAHAN)(?:\*\*)?[:\s\-]*\n+/i', '', trim($data_rps->strategi_perkuliahan ?? ''));
      echo render_kontrak_content($clean_strat, '<p>Perkuliahan diselenggarakan dengan pendekatan Student-Centered Learning (SCL) yang mengintegrasikan metode Case Method, Project-Based Learning, diskusi interaktif, dan simulasi praktikum.</p>');
      ?>
    </div>

    <!-- 6. MATERI/BAHAN BACAAN PERKULIAHAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      6. MATERI/BAHAN BACAAN PERKULIAHAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $materi_raw = !empty($data_rps->materi_bahan_bacaan) ? $data_rps->materi_bahan_bacaan : (!empty($data_rps->referensi) ? $data_rps->referensi : '');
      $clean_mat = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:MATERI BACAAN|BAHAN BACAAN|REFERENSI|DAFTAR PUSTAKA)(?:\*\*)?[:\s\-]*\n+/i', '', trim($materi_raw));
      $clean_mat = preg_replace('/(?:\n+|\A)(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DETAIL RENCANA|RENCANA PEMBELAJARAN|TABEL\s*(?:16\s*)?PERTEMUAN|DISTRIBUSI BOBOT|PENILAIAN|DESKRIPSI SINGKAT|MATRIKS|BLUEPRINT)[\s\S]*$/i', '', $clean_mat);
      $clean_mat = preg_replace('/(?:\n+|\A)\s*\|{1,2}(?:\s*Minggu|\s*Temu|\s*No|\s*1\s*\||\s*Sub-CPMK|\s*CPMK|\s*Bentuk|\s*Kriteria|\s*ETS|\s*UTS|\s*EAS|\s*UAS)[\s\S]*$/i', '', $clean_mat);
      $clean_mat = trim($clean_mat);
      echo render_kontrak_content($clean_mat, '<p>Daftar pustaka mengacu pada referensi utama dan pendukung yang tercantum pada Dokumen RPS OBE.</p>');
      ?>
    </div>

    <!-- 7. TUGAS-TUGAS -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      7. TUGAS-TUGAS
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_tugas = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:TUGAS-TUGAS|TUGAS MAHASISWA)(?:\*\*)?[:\s\-]*\n+/i', '', trim($data_rps->tugas_tugas ?? ''));
      echo render_kontrak_content($clean_tugas, '<p>Tugas terstruktur terdiri dari analisis studi kasus industri, tugas kelompok mini proyek, kuis berkala, dan evaluasi hasil belajar mandiri.</p>');
      ?>
    </div>

    <!-- 8. KRITERIA PENILAIAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      8. KRITERIA PENILAIAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      echo render_kontrak_kriteria_penilaian_section($data_rps, $data_rps_detail ?? []);
      ?>
    </div>

    <!-- 9. JADWAL PERKULIAHAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      9. JADWAL PERKULIAHAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $clean_jadwal = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:JADWAL KULIAH|JADWAL PERKULIAHAN|RENCANA 16 MINGGU)(?:\*\*)?[:\s\-]*\n+/i', '', trim($data_rps->jadwal_perkuliahan ?? ''));
      echo render_kontrak_content($clean_jadwal, '<p>Jadwal perkuliahan berlangsung selama 16 minggu (Minggu 8: UTS, Minggu 16: UAS) selaras dengan Rencana Pembelajaran Mingguan pada RPS.</p>');
      ?>
    </div>

    <!-- 10. TATA KELOLA PERKULIAHAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 5px;">
      10. TATA KELOLA PERKULIAHAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 26px; padding-right: 4px; margin-bottom: 10px;">
      <?php
      $raw_tata_kelola = $data_rps->tata_kelola_perkuliahan ?? '';
      // Hapus jika ada teks pengesahan yang terduplikasi di dalam tata kelola
      $clean_tata = preg_replace('/(?:^|\n)(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?PENGESAHAN KONTRAK[\s\S]*$/i', '', $raw_tata_kelola);
      $clean_tata = preg_replace('/^(?:[#*]{1,6}\s*)?(?:\d+[\.\)]\s*)?(?:TATA TERTIB|TATA KELOLA PERKULIAHAN)(?:\*\*)?[:\s\-]*\n+/i', '', trim($clean_tata));
      $fallback_tata = "1. Kehadiran minimal mahasiswa adalah 75% dari total tatap muka untuk berhak mengikuti Ujian Akhir Semester (UAS).\n2. Toleransi keterlambatan maksimal 15 menit.\n3. Berpakaian rapi, sopan, dan mematuhi etika akademik Politeknik Wilmar Bisnis Indonesia.\n4. Plagiarisme atau kecurangan dalam ujian/tugas berakibat sanksi nilai E.";
      echo render_kontrak_content($clean_tata, render_kontrak_text($fallback_tata));
      ?>
    </div>

  </div>

  <!-- Bagian Pengesahan Resmi Kontrak Pembelajaran (SPMI WBI) -->
  <div style="margin-top: 16px; page-break-inside: avoid;">
    <p style="font-size: 10pt; line-height: 1.6; margin-bottom: 10px; color: #1e293b; text-align: justify;">
      Kontrak Pembelajaran ini disepakati bersama antara Dosen Pengampu dan Perwakilan Mahasiswa sebagai acuan operasional perkuliahan yang transparan, terstruktur, serta berorientasi pada pencapaian Capaian Pembelajaran Lulusan (CPL).
    </p>
    <table class="table table-black" style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 9.5pt; text-align: center;">
      <tbody>
        <tr style="background-color: #f1f5f9;">
          <td style="width: 35%; padding: 7px 8px; font-weight: bold; border: 1px solid #7a7a7a; font-size: 9.5pt;">Pihak I<br>(Dosen Pengampu)</td>
          <td style="width: 30%; padding: 7px 8px; font-weight: bold; border: 1px solid #7a7a7a; font-size: 9.5pt;">Pihak II<br>(Perwakilan Mahasiswa)</td>
          <td style="width: 35%; padding: 7px 8px; font-weight: bold; border: 1px solid #7a7a7a; font-size: 9.5pt;">Mengetahui & Menyetujui<br>(Ketua Program Studi)</td>
        </tr>
        <tr>
          <td style="border: 1px solid #7a7a7a; vertical-align: bottom; height: 90px; padding: 6px 8px;">
            <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
            <span style="font-size: 9.5pt; white-space: nowrap;"><u><?= clean_style($nama_dosen, ''); ?></u></span><br>
            <span style="font-size: 9pt; color: #334155;"><?= !empty($nidn_dosen) ? 'NIDN. ' . $nidn_dosen : 'NIDN. -'; ?></span>
          </td>
          <td style="border: 1px solid #7a7a7a; vertical-align: bottom; height: 90px; padding: 6px 8px;">
            <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
            <span style="white-space: nowrap; font-size: 9.5pt;">( ........................ )</span><br>
            <span style="font-size: 9pt; color: #334155;">Komting Kelas / NIM. -</span>
          </td>
          <td style="border: 1px solid #7a7a7a; vertical-align: bottom; height: 90px; padding: 6px 8px;">
            <div style="height: 48px; line-height: 16px; font-size: 9.5pt;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
            <span style="white-space: nowrap; font-size: 9.5pt;"><?= (!empty($nama_kaprodi)) ? '<u>' . clean_style($nama_kaprodi, '') . '</u>' : '( ........................ )'; ?></span><br>
            <span style="font-size: 9pt; color: #334155;"><?= !empty($nidn_kaprodi) ? 'NIDN. ' . $nidn_kaprodi : 'NIDN. -'; ?></span>
          </td>
        </tr>
        <tr>
          <td style="border: 1px solid #7a7a7a; padding: 5px; font-size: 9pt; color: #334155;">Tanggal : <?= $tanggal_usulan; ?></td>
          <td style="border: 1px solid #7a7a7a; padding: 5px; font-size: 9pt; color: #334155;">Tanggal : <?= $tanggal_usulan; ?></td>
          <td style="border: 1px solid #7a7a7a; padding: 5px; font-size: 9pt; color: #334155;">Tanggal : <?= $tanggal_usulan; ?></td>
        </tr>
      </tbody>
    </table>
  </div>