<?php if (!empty($is_mpdf)): ?>
<htmlpagefooter name="docFooter">
  <div style="border-top: 0.5px solid #cbd5e1; padding-top: 4px; font-size: 8pt; color: #64748b; font-family: Helvetica, sans-serif;">
    <table style="width: 100%; border: none !important; border-collapse: collapse;">
      <tr>
        <td style="width: 65%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: left;">
          RPS - <?= !empty($data_rps->nm_mk) ? htmlspecialchars($data_rps->nm_mk) : ''; ?>
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

<div class="rps-portrait-inner">
  <?php
  // Gunakan tanggal_rps jika ada, fallback ke create_date (tanggal pertama simpan), lalu hari ini
  $tanggal_rps_val = !empty($data_rps->tanggal_rps) ? $data_rps->tanggal_rps
                   : (!empty($data_rps->create_date) ? date('Y-m-d', strtotime($data_rps->create_date))
                   : date('Y-m-d'));
  $tanggal_usulan = date('d-m-Y', strtotime($tanggal_rps_val));
  $tanggal_pengesahan = !empty($data_rps->tanggal_pengesahan) ? date('d-m-Y', strtotime($data_rps->tanggal_pengesahan)) : '-';

  $logo_path = file_exists(FCPATH . 'assets/image_web/kop_wbi.png') 
               ? FCPATH . 'assets/image_web/kop_wbi.png'
               : (file_exists(FCPATH . 'assets/image_web/logo1 (1).png') 
                  ? FCPATH . 'assets/image_web/logo1 (1).png' 
                  : FCPATH . 'assets/image_web/logo-white-3.png');
  $logo_src = file_exists($logo_path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path)) : (function_exists('base_url') ? base_url('assets/image_web/kop_wbi.png') : 'assets/image_web/kop_wbi.png');
  ?>

  <?php
  if (!function_exists('parse_markdown_table')) {
      function parse_markdown_table($text) {
          if (strpos($text, '|') === false) return null;

          $text = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $text);
          $text = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $text);
          $lines = explode("\n", str_replace("\r", "", $text));

          // Kumpulkan semua tabel markdown yang ada di dalam teks
          $all_tables = [];
          $current_table = [];
          $current_before = [];
          $in_table = false;
          $non_table_lines = [];

          foreach ($lines as $line) {
              $trim = trim($line);
              if (strpos($trim, '|') !== false && (substr($trim, 0, 1) === '|' || substr($trim, -1) === '|')) {
                  if (!$in_table) {
                      $in_table = true;
                      $current_table = [];
                  }
                  $current_table[] = $trim;
              } else {
                  if ($in_table) {
                      $in_table = false;
                      if (count($current_table) >= 2) {
                          $all_tables[] = [
                              'lines'  => $current_table,
                              'before' => $current_before
                          ];
                      }
                      $current_table = [];
                      $current_before = [];
                  }
                  $current_before[] = $line;
                  $non_table_lines[] = $line;
              }
          }
          if ($in_table && count($current_table) >= 2) {
              $all_tables[] = [
                  'lines'  => $current_table,
                  'before' => $current_before
              ];
          }

          if (empty($all_tables)) return null;

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

          // Helper parse raw table lines to rows
          $parse_rows_fn = function($tbl_lines) {
              $rows = [];
              foreach ($tbl_lines as $tline) {
                  if (preg_match('/^\|?[\s\-:|]+\|?$/', $tline)) continue;
                  $cells = array_values(array_filter(array_map('trim', explode('|', $tline)), function($v) {
                      return true;
                  }));
                  if (substr($tline, 0, 1) === '|' && isset($cells[0]) && $cells[0] === '') array_shift($cells);
                  if (substr($tline, -1) === '|' && isset($cells[count($cells)-1]) && $cells[count($cells)-1] === '') array_pop($cells);
                  if (!empty($cells)) $rows[] = $cells;
              }
              return $rows;
          };

          $cpmk_table = null;
          $matrix_table = null;
          $matrix_before_str = '';

          foreach ($all_tables as $tbl) {
              $rows = $parse_rows_fn($tbl['lines']);
              if (empty($rows)) continue;
              $header_line = implode(' ', $rows[0]);
              $num_cols = count($rows[0]);
              $full_str = implode("\n", $tbl['lines']);

              $is_matrix = false;
              if (stripos($header_line, 'Sikap') !== false || stripos($header_line, 'Tugas') !== false || stripos($header_line, 'UTS') !== false || stripos($header_line, 'UAS') !== false || stripos($header_line, 'Kuis') !== false || stripos($header_line, 'Bobot') !== false || stripos($full_str, 'TOTAL BOBOT') !== false || $num_cols >= 5) {
                  $is_matrix = true;
              }

              if ($is_matrix && $matrix_table === null) {
                  $matrix_table = [
                      'rows'   => $rows,
                      'before' => $tbl['before']
                  ];
              } elseif (!$is_matrix && $cpmk_table === null) {
                  $cpmk_table = [
                      'rows'   => $rows,
                      'before' => $tbl['before']
                  ];
              }
          }

          // Render CPMK Table HTML (Tabel Rumusan CPMK vs CPL Terkait)
          $cpmk_table_html = '';
          $cpmk_intro_text = '';
          if ($cpmk_table !== null) {
              $cpmk_intro_text = trim(implode("\n", $cpmk_table['before']));
              $h_row = $cpmk_table['rows'][0];
              $b_rows = array_slice($cpmk_table['rows'], 1);
              $n_cols = count($h_row);

              $col_widths = ['12%', '68%', '20%'];
              if ($n_cols === 2) {
                  $col_widths = ['15%', '85%'];
              } elseif ($n_cols === 4) {
                  $col_widths = ['10%', '62%', '16%', '12%'];
              }

              $cpmk_custom_title = 'Matriks Rumusan CPMK & Pemetaan CPL Terkait';
              if (preg_match('/(?:^|\n)(?:#{1,4}\s*|\*{1,2})([^\n]*(?:rumusan|pemetaan|cpmk)[^\n]*?)(\*{1,2})?\s*$/i', $cpmk_intro_text, $c_title)) {
                  $extracted = trim(preg_replace('/\*\*/', '', $c_title[1]));
                  $extracted = preg_replace('/^(?:[#*`\s]*)(?:(?:[A-Za-z]\.?\d+|[A-Za-z0-9]+(?:\.[A-Za-z0-9]+)*[\.\)]|\d+[\.\)]|[A-Za-z][\.\)])\s*)*(?:TABEL\s+)?/i', '', $extracted);
                  $extracted = trim(preg_replace('/[\*#`]+/', '', $extracted));
                  if (!empty($extracted) && (stripos($extracted, 'Matriks') !== false || stripos($extracted, 'Rumusan') !== false || stripos($extracted, 'Pemetaan') !== false || stripos($extracted, 'CPMK') !== false)) {
                      $cpmk_custom_title = $extracted;
                  }
              }

              $cpmk_table_html = '<div style="margin-top:14px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">' . htmlspecialchars($cpmk_custom_title) . '</div>';
              $cpmk_table_html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:4px; margin-bottom:4px; font-size:8.5pt; border:1px solid #7a7a7a; table-layout:fixed;">';
              $cpmk_table_html .= '<colgroup>';
              foreach ($col_widths as $w) {
                  $cpmk_table_html .= '<col style="width:' . $w . ';" />';
              }
              $cpmk_table_html .= '</colgroup>';
              $cpmk_table_html .= '<thead><tr style="background-color:#f8fafc;">';
              foreach ($h_row as $idx => $c) {
                  $align = ($idx === 1) ? 'left' : 'center';
                  $cpmk_table_html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:' . $align . '; vertical-align:middle; font-weight:700; font-size:8.5pt; color:#0f172a;">' . $format_cell($c) . '</th>';
              }
              $cpmk_table_html .= '</tr></thead><tbody>';

              foreach ($b_rows as $cells) {
                  $cpmk_table_html .= '<tr>';
                  foreach ($cells as $c_idx => $c) {
                      $align = ($c_idx === 1) ? 'justify' : 'center';
                      $td_style = 'border:1px solid #7a7a7a; padding:6px 8px; text-align:' . $align . '; vertical-align:top; font-size:8.5pt; line-height:1.45;';
                      if ($c_idx === 0) {
                          $td_style .= ' font-weight:bold; text-align:center;';
                      }
                      if ($c_idx === $n_cols - 1 && $n_cols >= 3) {
                          $td_style .= ' text-align:center; font-weight:600; color:#1e293b;';
                      }
                      $cpmk_table_html .= '<td style="' . $td_style . '">' . $format_cell($c) . '</td>';
                  }
                  $cpmk_table_html .= '</tr>';
              }
              $cpmk_table_html .= '</tbody></table>';
          }

          // Render Matrix Table HTML (Matriks Korelasi CPMK vs Bentuk Penilaian)
          $matrix_table_html = '';
          if ($matrix_table !== null) {
              $m_h_row = $matrix_table['rows'][0];
              $m_b_rows = array_slice($matrix_table['rows'], 1);
              $m_num_cols = count($m_h_row);

              $custom_title = 'Matriks Korelasi CPMK vs Bentuk Penilaian (Blueprint Asesmen OBE)';
              $m_before_str = trim(implode("\n", $matrix_table['before']));
              if (preg_match('/(?:^|\n)(?:#{1,4}\s*|\*{1,2})([^\n]*(?:matriks|blueprint|korelasi)[^\n]*?)(\*{1,2})?\s*$/i', $m_before_str, $m_title)) {
                  $extracted = trim(preg_replace('/\*\*/', '', $m_title[1]));
                  $extracted = preg_replace('/^(?:[#*`\s]*)(?:(?:[A-Za-z]\.?\d+|[A-Za-z0-9]+(?:\.[A-Za-z0-9]+)*[\.\)]|\d+[\.\)]|[A-Za-z][\.\)])\s*)*(?:TABEL\s+)?/i', '', $extracted);
                  $extracted = trim(preg_replace('/[\*#`]+/', '', $extracted));
                  if (!empty($extracted) && (stripos($extracted, 'Matriks') !== false || stripos($extracted, 'Blueprint') !== false || stripos($extracted, 'Korelasi') !== false)) {
                      $custom_title = $extracted;
                  }
              }

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

              $matrix_table_html = '<div style="margin-top:14px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">' . htmlspecialchars($custom_title) . '</div>';
              $matrix_table_html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:4px; margin-bottom:8px; font-size:8pt; border:1px solid #7a7a7a; table-layout:fixed;">';
              $matrix_table_html .= '<colgroup>';
              foreach ($m_col_widths as $cw) {
                  $matrix_table_html .= '<col style="width:' . $cw . ';" />';
              }
              $matrix_table_html .= '</colgroup>';

              $matrix_table_html .= '<thead><tr style="background-color:#f8fafc;">';
              foreach ($m_h_row as $h_idx => $c) {
                  $c_clean = $format_cell($c);
                  $is_last = ($h_idx === $m_num_cols - 1);
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
                          $cpmk_rowspans[$r_idx + $s] = 0; // 0: sel dilewati karena di-merge ke baris atas
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
                              // Sel ini sudah di-merge dengan baris di atasnya
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
          }

          return [
              'cpmk_table_html' => $cpmk_table_html,
              'cpmk_intro_text' => $cpmk_intro_text,
              'matrix_html'     => $matrix_table_html,
              'cpmk_table_rows' => $cpmk_table ? $cpmk_table['rows'] : null,
              'before'          => $cpmk_table ? $cpmk_intro_text : ($matrix_table ? trim(implode("\n", $matrix_table['before'])) : ''),
              'table'           => $matrix_table_html
          ];
      }
  }

  if (!function_exists('extract_cpmk_items')) {
      function extract_cpmk_items($text) {
          $cpmks = [];
          if (empty($text)) return $cpmks;

          $plain = $text;
          if (preg_match('/<[a-z][\s\S]*>/i', $plain)) {
              $plain = preg_replace('/<\/(?:p|div|li|tr|h[1-6])>/i', "\n", $plain);
              $plain = preg_replace('/<br\s*\/?>/i', "\n", $plain);
              $plain = strip_tags($plain);
              $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          }

          $lines = explode("\n", str_replace("\r", "", $plain));
          foreach ($lines as $line) {
              $trim = trim($line);
              if ($trim === '') continue;

              // Abaikan baris heading, intro, atau baris tabel
              if (preg_match('/^(?:#{1,6}\s*|[A-Z]\.\d*\s*|BAGIAN|BAB|SEKSI|Berdasarkan|Matriks|Blueprint|Tabel|\|)/i', $trim)) continue;
              if (preg_match('/^(?:\d+\.\d+|\d+\.)\s*(?:Rumusan|Tabel|Matriks|Capaian|CPMK\b)/i', $trim)) continue;

              if (preg_match('/^(?:[\-\*\d\.\)\s]*)?(?:\*\*)?CPMK[\s\-]*(\d+)[:\*\s\-]+(.*)$/i', $trim, $m)) {
                  $desc = preg_replace('/^[:\*\s\-]+/', '', $m[2]);
                  $desc = preg_replace('/\*\*/', '', $desc);
                  $desc = preg_replace('/\):\s*/', ') ', $desc);
                  $cpmks[] = [
                      'kode' => 'CPMK-' . $m[1],
                      'desc' => trim($desc)
                  ];
              } elseif (preg_match('/^(\d+)[\.\)]\s*(.+)$/', $trim, $m)) {
                  $desc = preg_replace('/^\*\*CPMK\s*\d*[:\-]?\*\*\s*/i', '', $m[2]);
                  $desc = preg_replace('/^CPMK\s*\d*[:\-]?\s*/i', '', $desc);
                  $desc = preg_replace('/\*\*/', '', $desc);
                  $desc = preg_replace('/\):\s*/', ') ', $desc);
                  if (!preg_match('/^(?:Rumusan|Tabel|Matriks|Capaian Pembelajaran|CPMK\b)/i', trim($desc))) {
                      $cpmks[] = [
                          'kode' => 'CPMK-' . (count($cpmks) + 1),
                          'desc' => trim($desc)
                      ];
                  }
              }
          }
          return $cpmks;
      }
  }

  if (!function_exists('generate_cpmk_table_from_items')) {
      function generate_cpmk_table_from_items($cpmks, $cpl_raw_text = '') {
          if (empty($cpmks)) return '';

          // Ekstrak kode-kode CPL unik dari teks CPL jika ada
          $all_cpl_codes = [];
          if (!empty($cpl_raw_text)) {
              if (preg_match_all('/\b([A-Z]{1,3}\d+|[A-Z]{1,2}\-[0-9]+)\b/u', $cpl_raw_text, $m_cpl)) {
                  $filtered_cpls = array_filter($m_cpl[1], function($code) {
                      return !in_array(strtoupper($code), ['RPS', 'OBE', 'CPL', 'CPMK', 'WBI', 'SN', 'DIKTI', 'TA', 'TM', 'SKS']);
                  });
                  $all_cpl_codes = array_values(array_unique($filtered_cpls));
              }
          }
          $cpl_fallback_str = !empty($all_cpl_codes) ? implode(', ', array_slice($all_cpl_codes, 0, 4)) : '-';

          $html = '<table class="table table-black" style="width:100%; border-collapse:collapse; margin-top:4px; margin-bottom:4px; font-size:8.5pt; border:1px solid #7a7a7a; table-layout:fixed;">';
          $html .= '<colgroup>';
          $html .= '<col style="width:12%;" />';
          $html .= '<col style="width:68%;" />';
          $html .= '<col style="width:20%;" />';
          $html .= '</colgroup>';
          $html .= '<thead><tr style="background-color:#f8fafc;">';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:center; vertical-align:middle; font-weight:700; font-size:8.5pt; color:#0f172a;">No</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:left; vertical-align:middle; font-weight:700; font-size:8.5pt; color:#0f172a;">Rumusan CPMK</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:center; vertical-align:middle; font-weight:700; font-size:8.5pt; color:#0f172a;">CPL Terkait</th>';
          $html .= '</tr></thead><tbody>';

          $n_cpl = count($all_cpl_codes);
          $n_cpmk = count($cpmks);

          foreach ($cpmks as $idx => $c) {
              $kode_display = !empty($c['kode']) ? str_replace(' ', '-', trim($c['kode'])) : ('CPMK-' . ($idx + 1));
              $desc = $c['desc'] ?? '';
              $cpl_code = '';

              // Cek jika ada CPL di dalam kurung pada deskripsi
              if (preg_match('/^(?:\((.*?)\)\s*|\[(.*?)\]\s*)(.*)$/s', $desc, $m_paren)) {
                  $possible = trim($m_paren[1] ?: $m_paren[2]);
                  if (preg_match('/[A-Z]{1,3}\d+/i', $possible)) {
                      $cpl_code = $possible;
                      $desc = trim($m_paren[3]);
                  }
              }

              if (empty($cpl_code)) {
                  if ($n_cpl > 0) {
                      // Distribusikan kode CPL secara cerdas ke butir CPMK
                      if ($n_cpl >= $n_cpmk) {
                          $slice = array_slice($all_cpl_codes, $idx % $n_cpl, max(2, (int)ceil($n_cpl / $n_cpmk)));
                          $cpl_code = implode(', ', $slice);
                      } else {
                          $cpl_code = implode(', ', $all_cpl_codes);
                      }
                  } else {
                      $cpl_code = $cpl_fallback_str;
                  }
              }

              $html .= '<tr>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; vertical-align:top; font-weight:bold; font-size:8.5pt;">' . htmlspecialchars($kode_display) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px 8px; text-align:justify; vertical-align:top; font-size:8.5pt; line-height:1.45;">' . htmlspecialchars($desc) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; vertical-align:top; font-weight:600; font-size:8.5pt; color:#1e293b;">' . htmlspecialchars($cpl_code) . '</td>';
              $html .= '</tr>';
          }

          $html .= '</tbody></table>';
          return $html;
      }
  }

  if (!function_exists('generate_cpmk_matrix_html')) {
      function generate_cpmk_matrix_html($cpmks, $data_rps_detail = []) {
          if (empty($cpmks)) return '';

          $bobot_uts = 25;
          $bobot_uas = 25;
          if (!empty($data_rps_detail)) {
              foreach ($data_rps_detail as $d) {
                  $col1 = (int)(is_object($d) ? ($d->kolom_1 ?? 0) : ($d['kolom_1'] ?? 0));
                  $b = (int)(is_object($d) ? ($d->kolom_12 ?? 0) : ($d['kolom_12'] ?? 0));
                  if ($col1 === 8 && $b > 0) $bobot_uts = $b;
                  if ($col1 === 16 && $b > 0) $bobot_uas = $b;
              }
          }

          $bobot_sikap = 10;
          $bobot_tugas = 100 - ($bobot_sikap + $bobot_uts + $bobot_uas);
          if ($bobot_tugas < 0) $bobot_tugas = 30;

          $n = count($cpmks);
          if ($n === 0) return '';

          $html = '<div style="margin-top:12px; margin-bottom:6px;">';
          $html .= '<div style="font-weight:bold; font-size:9.5pt; color:#0f172a; margin-bottom:6px;">Matriks Rencana Asesmen & Pembobotan CPMK (Korelasi Sikap, Tugas, UTS, UAS):</div>';
          $html .= '<table class="table table-black" style="width:100%; border-collapse:collapse; font-size:9pt; border:1px solid #7a7a7a; table-layout:fixed;">';
          $html .= '<colgroup>';
          $html .= '<col style="width:12%;" />';
          $html .= '<col style="width:38%;" />';
          $html .= '<col style="width:10%;" />';
          $html .= '<col style="width:10%;" />';
          $html .= '<col style="width:10%;" />';
          $html .= '<col style="width:10%;" />';
          $html .= '<col style="width:10%;" />';
          $html .= '</colgroup>';
          $html .= '<thead>';
          $html .= '<tr style="background-color:#f8fafc;">';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:center; vertical-align:middle; font-weight:700;">Kode</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 6px; text-align:center; vertical-align:middle; font-weight:700;">Rumusan CPMK</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 4px; text-align:center; vertical-align:middle; font-weight:700;">Sikap / Partisipasi</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 4px; text-align:center; vertical-align:middle; font-weight:700;">Tugas / Kuis</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 4px; text-align:center; vertical-align:middle; font-weight:700;">UTS</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 4px; text-align:center; vertical-align:middle; font-weight:700;">UAS / Proyek</th>';
          $html .= '<th style="border:1px solid #7a7a7a; padding:6px 4px; text-align:center; vertical-align:middle; font-weight:700;">Total Bobot</th>';
          $html .= '</tr>';
          $html .= '</thead><tbody>';

          $sum_sikap = 0;
          $sum_tugas = 0;
          $sum_uts = 0;
          $sum_uas = 0;
          $sum_total = 0;

          foreach ($cpmks as $idx => $c) {
              $i = $idx + 1;
              $sikap_i = ($i === $n) ? ($bobot_sikap - $sum_sikap) : (int)round($bobot_sikap / $n);
              $sum_sikap += $sikap_i;

              $tugas_i = ($i === $n) ? ($bobot_tugas - $sum_tugas) : (int)round($bobot_tugas / $n);
              $sum_tugas += $tugas_i;

              $half = (int)ceil($n / 2);
              if ($i <= $half) {
                  $uts_i = ($i == $half) ? ($bobot_uts - $sum_uts) : (int)round($bobot_uts / $half);
                  $sum_uts += $uts_i;
              } else {
                  $uts_i = 0;
              }

              if ($i >= $half) {
                  $uas_count = $n - $half + 1;
                  $uas_i = ($i == $n) ? ($bobot_uas - $sum_uas) : (int)round($bobot_uas / $uas_count);
                  $sum_uas += $uas_i;
              } else {
                  $uas_i = 0;
              }

              $row_total = $sikap_i + $tugas_i + $uts_i + $uas_i;
              $sum_total += $row_total;

              $html .= '<tr>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; font-weight:600; vertical-align:middle;">' . htmlspecialchars($c['kode']) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px 8px; text-align:left; vertical-align:middle; line-height:1.4;">' . htmlspecialchars($c['desc']) . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $sikap_i . '%</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $tugas_i . '%</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . ($uts_i > 0 ? $uts_i . '%' : '-') . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . ($uas_i > 0 ? $uas_i . '%' : '-') . '</td>';
              $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle; font-weight:700; background-color:#fafafa;">' . $row_total . '%</td>';
              $html .= '</tr>';
          }

          $html .= '<tr style="background-color:#f1f5f9; font-weight:700;">';
          $html .= '<td colspan="2" style="border:1px solid #7a7a7a; padding:6px 8px; text-align:center; vertical-align:middle;">Total Bobot Komponen Penilaian</td>';
          $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $sum_sikap . '%</td>';
          $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $sum_tugas . '%</td>';
          $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $sum_uts . '%</td>';
          $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle;">' . $sum_uas . '%</td>';
          $html .= '<td style="border:1px solid #7a7a7a; padding:6px; text-align:center; vertical-align:middle; font-weight:800; background-color:#e2e8f0;">' . $sum_total . '%</td>';
          $html .= '</tr>';

          $html .= '</tbody></table></div>';
          return $html;
      }
  }

  if (!function_exists('parse_tujuan_belajar_parts')) {
      function parse_tujuan_belajar_parts($text, $details = [], $cpl_raw_text = '') {
          $result = [
              'cpmk_html'   => '',
              'matrix_html' => ''
          ];

          if (empty($text) || trim($text) === '') {
              $result['cpmk_html'] = '<p style="margin:0; color:#94a3b8; font-style:italic;">-</p>';
              return $result;
          }

          // Case 1: Markdown table is present in the text
          $parsed = parse_markdown_table($text);
          if ($parsed !== null) {
              // 1. CPMK HTML (SELALU dikembalikan ke format DAFTAR TEKS BERNOMOR untuk kotak dokumen utama)
              $cpmk_text_raw = !empty($parsed['cpmk_intro_text']) ? $parsed['cpmk_intro_text'] : $parsed['before'];
              $cpmk_text_clean = preg_replace('/^(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?(?:RUMUSAN CPMK|CAPAIAN PEMBELAJARAN MATA KULIAH|CPMK|TUJUAN BELAJAR)(?:\*\*)?[:\s\-]*\n+/i', '', trim($cpmk_text_raw));
              $cpmk_text_clean = preg_replace('/(?:\n+|\A)(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?(?:MATRIKS\s*KORELASI|MATRIKS\s*RENCANA\s*ASESMEN|MATRIKS\s*DISTRIBUSI|MATRIKS\s*ASESMEN|BLUEPRINT\s*ASESMEN|TABEL\s*MATRIKS|MATRIKS|BLUEPRINT)[^\n]*$/i', '', trim($cpmk_text_clean));
              $cpmk_text_clean = preg_replace('/(?:\n+|\A)(?:[#*_`\s]*)[0-9A-Za-z\.\-_]{1,10}(?:[#*_`\s]*)$/i', '', trim($cpmk_text_clean));
              $cpmk_text_clean = preg_replace('/(?:\n+|\A)\s*\|[^\n]+\|[\s\S]*$/i', '', $cpmk_text_clean);

              $cpmks = extract_cpmk_items($cpmk_text_clean);
              if (empty($cpmks) && !empty($parsed['cpmk_table_rows'])) {
                  $b_rows = array_slice($parsed['cpmk_table_rows'], 1);
                  foreach ($b_rows as $r) {
                      $cpmks[] = [
                          'kode' => $r[0] ?? 'CPMK',
                          'desc' => $r[1] ?? ''
                      ];
                  }
              }

              if (!empty($cpmks)) {
                  $list_text = '';
                  foreach ($cpmks as $idx => $item) {
                      $list_text .= ($idx + 1) . ". **" . $item['kode'] . ":** " . $item['desc'] . "\n";
                  }
                  $result['cpmk_html'] = format_rps_list(trim($list_text));
              } elseif (!empty($cpmk_text_clean)) {
                  $result['cpmk_html'] = format_rps_list(trim($cpmk_text_clean));
              } else {
                  $result['cpmk_html'] = '<p style="margin:0; color:#94a3b8; font-style:italic;">-</p>';
              }

              // 2. Matrix HTML (Kedua Matriks diletakkan di BAGIAN BAWAH DOKUMEN)
              $matrix_out = '';
              if (!empty($parsed['cpmk_table_html'])) {
                  $matrix_out .= $parsed['cpmk_table_html'];
              } elseif (!empty($cpmks)) {
                  $cpmk_tbl = generate_cpmk_table_from_items($cpmks, $cpl_raw_text);
                  if (!empty($cpmk_tbl)) {
                      $matrix_out .= '<div style="margin-top:14px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">Matriks Rumusan CPMK &amp; Pemetaan CPL Terkait</div>' . $cpmk_tbl;
                  }
              }

              if (!empty($parsed['matrix_html'])) {
                  $matrix_out .= $parsed['matrix_html'];
              } elseif (!empty($cpmks)) {
                  $matrix_out .= generate_cpmk_matrix_html($cpmks, $details);
              }
              $result['matrix_html'] = $matrix_out;

              return $result;
          }

          // Case 2: List without markdown table -> Format clean list for main table, and generate BOTH tables for the bottom!
          $cpmks = extract_cpmk_items($text);
          if (!empty($cpmks)) {
              $list_text = '';
              foreach ($cpmks as $idx => $item) {
                  $list_text .= ($idx + 1) . ". **" . $item['kode'] . ":** " . $item['desc'] . "\n";
              }
              $result['cpmk_html'] = format_rps_list(trim($list_text));

              $matrix_out = '';
              $cpmk_tbl = generate_cpmk_table_from_items($cpmks, $cpl_raw_text);
              if (!empty($cpmk_tbl)) {
                  $matrix_out .= '<div style="margin-top:14px; margin-bottom:6px; font-weight:bold; font-size:10pt; color:#0f172a;">Matriks Rumusan CPMK &amp; Pemetaan CPL Terkait</div>' . $cpmk_tbl;
              }
              $matrix_out .= generate_cpmk_matrix_html($cpmks, $details);
              $result['matrix_html'] = $matrix_out;
          } else {
              $cpmk_text = preg_replace('/^(?:[#*]{1,6}\s*)?(?:[0-9A-Za-z\.\-_]+\s*)?(?:RUMUSAN CPMK|CAPAIAN PEMBELAJARAN MATA KULIAH|CPMK|TUJUAN BELAJAR)(?:\*\*)?[:\s\-]*\n+/i', '', trim($text));
              $cpmk_text = preg_replace('/(?:\n+|\A)(?:[#*]{1,6}\s*)?(?:[0-9A-Za-z\.\-_]+\s*)?(?:MATRIKS\s*KORELASI|MATRIKS\s*RENCANA\s*ASESMEN|MATRIKS\s*ASESMEN|BLUEPRINT\s*ASESMEN|MATRIKS|BLUEPRINT)[^\n]*$/i', '', trim($cpmk_text));
              $cpmk_text = preg_replace('/(?:\n+|\A)(?:[#*_`\s]*)[0-9A-Za-z\.\-_]{1,10}(?:[#*_`\s]*)$/i', '', trim($cpmk_text));
              $result['cpmk_html'] = format_rps_list(trim($cpmk_text));
          }

          return $result;
      }
  }

  if (!function_exists('render_tujuan_belajar_section')) {
      function render_tujuan_belajar_section($text, $details = []) {
          $parts = parse_tujuan_belajar_parts($text, $details);
          return $parts['cpmk_html'] . $parts['matrix_html'];
      }
  }

  if (!function_exists('clean_rps_html')) {
      function clean_rps_html($html) {
          if (empty($html) || trim($html) === '') return '<p style="margin:0; color:#94a3b8; font-style:italic;">-</p>';

          // Bersihkan code fences markdown (```json, ```markdown, ```)
          $html = preg_replace('/^```[a-zA-Z0-9_\-]*\s*$/m', '', $html);
          $html = str_replace('```', '', $html);

          // Bersihkan seluruh sampah Word HTML & XML terlebih dahulu
          if (function_exists('clean_word_and_html_junk')) {
              $html = clean_word_and_html_junk($html);
          } else {
              $html = preg_replace('/<!--\[if[\s\S]*?<!\[endif\]-->/i', '', $html);
              $html = preg_replace('/<!--[\s\S]*?-->/', '', $html);
              $html = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html);
              $html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
              $html = preg_replace('/<xml\b[^>]*>[\s\S]*?<\/xml>/i', '', $html);
              $html = preg_replace('/<\/?\w+:[^>]*>/i', '', $html);
              $html = preg_replace('/<(?:span|font|o:p)[^>]*>/i', '', $html);
              $html = preg_replace('/<\/(?:span|font|o:p)>/i', '', $html);
              $html = preg_replace('/(?:minor-latin|calibri|times new roman|arial|mso-[a-zA-Z0-9\-]+|font-family|font-size|line-height|margin|tab-stops)[^>\n]*["\'>]/i', '', $html);
              $html = preg_replace('/(?:mso-[a-zA-Z0-9\-]+|font-family|font-size|line-height|margin|text-align):[^;>\n]*;?/i', '', $html);
              $html = preg_replace('/["\']\s*>/', '', $html);
              $html = preg_replace('/^[\s"\'\>]*>/m', '', $html);
              $html = preg_replace('/\s*(?:style|class|lang|align|valign|dir)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
          }

          // Hapus blok kutipan / catatan AI seperti blockquote
          $html = preg_replace('/<blockquote\b[^>]*>.*?<\/blockquote>/is', '', $html);
          
          // Hapus penanda draf [PERLU VALIDASI...]
          $html = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $html);
          $html = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $html);

          // Hapus sisa backticks
          $html = str_replace('`', '', $html);

          // Hapus atribut data-* (misal data-start, data-end dari copy paste ChatGPT)
          $html = preg_replace('/\s+data-[a-z0-9_\-]+="[^"]*"/i', '', $html);
          $html = preg_replace('/\s+data-[a-z0-9_\-]+=\'[^\']*\'/i', '', $html);

          // Standarisasi tag: strong -> b, em -> i
          $html = preg_replace('/<(\/)?strong\b[^>]*>/i', '<$1b>', $html);
          $html = preg_replace('/<(\/)?em\b[^>]*>/i', '<$1i>', $html);

          // Ubah heading h1, h2, h3, h4 menjadi paragraf bold yang rapi
          $html = preg_replace('/<h[1-6]\b[^>]*>(.*?)<\/h[1-6]>/is', '<p style="margin: 6px 0 2px 0;"><b style="font-size: 9.5pt; color: #0f172a;">$1</b></p>', $html);

          // Hilangkan p di dalam li: <li><p>text</p></li> -> <li>text</li>
          $html = preg_replace('/<li\b[^>]*>\s*<p\b[^>]*>(.*?)<\/p>\s*<\/li>/is', '<li>$1</li>', $html);

          // Konversi markdown bold & italic di dalam teks HTML
          $html = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<b><i>$1</i></b>', $html);
          $html = preg_replace('/\*\*_(.*?)_\*\*/s', '<b><i>$1</i></b>', $html);
          $html = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $html);
          $html = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<b>$1</b>', $html);
          $html = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $html);
          $html = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $html);

          // Filter tag yang diizinkan saja
          $allowed_tags = '<p><br><b><i><u><ol><ul><li><table><thead><tbody><tr><th><td>';
          $html = strip_tags($html, $allowed_tags);

          // Bersihkan spasi kosong dan paragraf kosong
          $html = preg_replace('/<p\b[^>]*>\s*(?:&nbsp;|<br\s*\/?>|\s)*<\/p>/i', '', $html);
          
          // Rapikan margin paragraf agar tidak terlalu berjarak di PDF
          $html = preg_replace('/<p\b[^>]*>/i', '<p style="margin: 0 0 4px 0; line-height: 1.5;">', $html);
          
          // Konversi ol / ul / li menjadi table hanging indent jika ada HTML list
          if (preg_match('/<li\b[^>]*>/i', $html)) {
              $li_num = 1;
              $html = preg_replace_callback('/<li\b[^>]*>(.*?)<\/li>/is', function($matches) use (&$li_num) {
                  $content = trim($matches[1]);
                  $content = preg_replace('/^\d+[\.\)]\s*/', '', $content);
                  $out = '<table style="width: 100%; border: none !important; border-collapse: collapse; margin-bottom: 5px;">'
                       . '<tr style="border: none !important;">'
                       . '<td style="width: 22px; vertical-align: top; border: none !important; padding: 0 4px 0 0; font-size: 9pt; font-weight: normal; color: #0f172a;">' . $li_num . '.</td>'
                       . '<td style="vertical-align: top; border: none !important; padding: 0; font-size: 9pt; line-height: 1.55; text-align: justify; color: #0f172a;">' . $content . '</td>'
                       . '</tr></table>';
                  $li_num++;
                  return $out;
              }, $html);
              $html = preg_replace('/<\/?(?:ol|ul)\b[^>]*>/i', '', $html);
          }

          return trim($html);
      }
  }

  if (!function_exists('render_md_bold')) {
      function render_md_bold($text) {
          if (empty($text)) return '';
          $text = preg_replace('/^```[a-zA-Z0-9_\-]*\s*$/m', '', $text);
          $text = str_replace(['```', '`'], '', $text);
          $text = preg_replace('/\s+data-[a-z0-9_\-]+="[^"]*"/i', '', $text);
          $text = preg_replace('/\s+data-[a-z0-9_\-]+=\'[^\']*\'/i', '', $text);
          $text = preg_replace('/\s*style="[^"]*"/i', '', $text);
          $text = preg_replace('/<(\/)?strong\b[^>]*>/i', '<$1b>', $text);
          $text = preg_replace('/<(\/)?em\b[^>]*>/i', '<$1i>', $text);
          $text = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<b><i>$1</i></b>', $text);
          $text = preg_replace('/\*\*_(.*?)_\*\*/s', '<b><i>$1</i></b>', $text);
          $text = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $text);
          $text = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<b>$1</b>', $text);
          $text = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $text);
          $text = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $text);
          $text = preg_replace('/<\/?(?:span|font)\b[^>]*>/i', '', $text);
          return $text;
      }
  }

  if (!function_exists('format_rps_list')) {
      function format_rps_list($text) {
          if (empty($text) || trim($text) === '') {
              return '<p style="margin:0; color:#94a3b8; font-style:italic;">-</p>';
          }

          // Cek jika teks mengandung tag HTML (<p, <div, <span, <strong, <b, <br, <ol, <ul, <li, <table, dll)
          if (preg_match('/<\/?(?:p|div|span|strong|b|em|i|u|ol|ul|li|br|h[1-6]|table|blockquote)\b/i', $text)) {
              return clean_rps_html($text);
          }

          // Bersihkan penanda draf seperti [PERLU VALIDASI DOSEN/PRODI]
          $text = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $text);
          $text = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $text);

          // Format markdown bold & headings
          $text = htmlspecialchars($text);
          $text = preg_replace('/^(?:#{1,6})\s*(.*?)$/m', '<b style="font-size:10pt; color:#0f172a;">\1</b>', $text);
          $text = preg_replace('/#{1,6}\s*/', '', $text);
          $text = preg_replace('/\*\*\*(.*?)\*\*\*/', '<b><i>\1</i></b>', $text);
          $text = preg_replace('/\*\*_(.*?)_\*\*/', '<b><i>\1</i></b>', $text);
          $text = preg_replace('/\*\*(.*?)\*\*/', '<b>\1</b>', $text);
          $text = preg_replace('/(?<!\w)__(.*?)__(?!\w)/', '<b>\1</b>', $text);
          $text = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>\1</i>', $text);
          $text = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>\1</i>', $text);

          $lines = explode("\n", str_replace("\r", "", $text));
          $html = '';
          $item_num = 1;

          foreach ($lines as $line) {
              $trim = trim($line);
              if ($trim === '') {
                  continue;
              }

              // Abaikan horizontal rule / garis pemisah markdown seperti ---, ***, ___
              if (preg_match('/^[\-\*_]{2,}$/', $trim)) {
                  continue;
              }

              // List items: 1. / 1) / - / * / • / &bull; / CPL X / CPMK X / Sub-CPMK X
              if (preg_match('/^(?:\d+[\.\)]|[\-\*•\x{2022}]|&bull;)\s*(.*)$/u', $trim, $m_item)) {
                  $item_content = trim($m_item[1]);
                  // Hapus penomoran ganda jika ada di awal item_content
                  $item_content = preg_replace('/^\d+[\.\)]\s*/', '', $item_content);

                  if ($item_content === '' || preg_match('/^[\-\*_]+$/', $item_content)) {
                      continue;
                  }

                  $html .= '<table style="width: 100%; border: none !important; border-collapse: collapse; margin-bottom: 5px;">'
                        . '<tr style="border: none !important;">'
                        . '<td style="width: 22px; vertical-align: top; border: none !important; padding: 0 4px 0 0; font-size: 9pt; font-weight: normal; color: #0f172a;">' . $item_num . '.</td>'
                        . '<td style="vertical-align: top; border: none !important; padding: 0; font-size: 9pt; line-height: 1.55; text-align: justify; color: #0f172a;">' . $item_content . '</td>'
                        . '</tr></table>';
                  $item_num++;
              }
              // Pola baris yang diawali langsung dengan CPL / CPMK / Sub-CPMK tanpa nomor depan
              elseif (preg_match('/^(?:<b>)?\s*(?:CPL|CPMK|Sub-CPMK)\s*\d+.*$/i', $trim)) {
                  $html .= '<table style="width: 100%; border: none !important; border-collapse: collapse; margin-bottom: 5px;">'
                        . '<tr style="border: none !important;">'
                        . '<td style="width: 22px; vertical-align: top; border: none !important; padding: 0 4px 0 0; font-size: 9pt; font-weight: normal; color: #0f172a;">' . $item_num . '.</td>'
                        . '<td style="vertical-align: top; border: none !important; padding: 0; font-size: 9pt; line-height: 1.55; text-align: justify; color: #0f172a;">' . $trim . '</td>'
                        . '</tr></table>';
                  $item_num++;
              }
              // Normal text line
              else {
                  $html .= '<p style="margin: 0 0 6px 0; font-size: 9pt; line-height: 1.55; text-align: justify; color: #0f172a;">' . $trim . '</p>';
              }
          }

          return $html;
      }
  }

  $tb_parts = parse_tujuan_belajar_parts($data_rps->tujuan_belajar, $data_rps_detail ?? [], $data_rps->cpl ?? '');

  // Resolusi nama Program Studi
  $ci = get_instance();
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
  }
  if (empty($nama_prodi)) {
      $nama_prodi = (!empty($nm_lemb) && $nm_lemb !== 'Politeknik Wilmar Bisnis Indonesia') ? $nm_lemb : '';
  }
  ?>

  <table class="table table-black" style="width: 100%; table-layout: fixed; margin-bottom: 0px !important; border-collapse: collapse !important;">
    <colgroup>
      <col style="width: 24%;" />
      <col style="width: 38%;" />
      <col style="width: 38%;" />
    </colgroup>
    <tbody>
      <tr>
        <td class="text-center" style="width: 24%; padding:10px; vertical-align: middle;">
          <img src="<?= $logo_src; ?>" width="140px" style="max-width: 140px; height: auto;" />
        </td>
        <td class="text-center" style="vertical-align: middle; width: 76%;" colspan="2">
          <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px;margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>
      </tr>
      <tr>
        <td class="text-center" colspan="3" style="padding: 10px 14px; background: #ffffff; text-align: center; vertical-align: middle;">
          <?php if (!empty($nama_prodi)): ?>
            <div style="font-size: 11pt; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; margin-bottom: 3px;">
              PROGRAM STUDI <?= $nama_prodi; ?>
            </div>
          <?php endif; ?>
          <h2 style="font-size: 13.5pt; margin: 0 0 3px 0; font-weight: 800; letter-spacing: 0.6px; color: #0f172a;">
            RENCANA PEMBELAJARAN SEMESTER (RPS)
          </h2>
          <div style="font-size: 10pt; font-weight: 700; color: #1e3a8a; letter-spacing: 0.5px; margin-bottom: 3px;">
            BERBASIS OUTCOME-BASED EDUCATION (OBE)
          </div>
          <div style="font-size: 8pt; font-weight: 500; color: #475569; line-height: 1.35;">
            Standar Penjaminan Mutu Pendidikan Tinggi (Permendiktisaintek No. 39 Thn 2025 jo. No. 10 Thn 2026) & SPMI Politeknik WBI
          </div>
        </td>
      </tr>
      <tr style="font-size: 8.5pt;">
        <td class="text-center" style="width: 24%; padding: 8px 4px; vertical-align: middle; background-color: #fafbfc;">
          <span style="font-size: 7.5pt; color: #475569; text-transform: uppercase; font-weight: 600;">Jenis Dokumen:</span>
          <div style="margin-top: 4px; font-size: 10pt; font-weight: 800; letter-spacing: 0.5px; line-height: 1.25; color: #0f172a;">FORMULIR<br>SPMI WBI</div>
          <div style="margin-top: 5px; font-size: 7pt; color: #065f46; font-weight: 700; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 3px; padding: 2px 4px; display: inline-block;">
            KURIKULUM OBE
          </div>
        </td>
        <?php
        $nama_koordinator = (!empty($koordinator_rps) && is_object($koordinator_rps) && !empty($koordinator_rps->nm_ptk)) ? $koordinator_rps->nm_ptk : ($data_rps->dosen_pengampu ?? '-');
        $nidn_koordinator = (!empty($koordinator_rps) && is_object($koordinator_rps) && !empty($koordinator_rps->nidn)) ? $koordinator_rps->nidn : '';
        if (empty($nidn_koordinator)) {
            $ci = get_instance();
            if (!empty($data_rps->id_ptk)) {
                $dsn = $ci->db->where('id_ptk', $data_rps->id_ptk)->get('siakad_dosen')->row();
                if ($dsn && !empty($dsn->nidn)) $nidn_koordinator = $dsn->nidn;
            }
            if (empty($nidn_koordinator) && !empty($data_rps->dosen_pengampu)) {
                $dsn = $ci->db->where('nm_ptk', trim((string)$data_rps->dosen_pengampu))->get('siakad_dosen')->row();
                if ($dsn && !empty($dsn->nidn)) $nidn_koordinator = $dsn->nidn;
            }
        }
        ?>
        <td class="text-center" style="width: 38%; padding: 6px 8px; vertical-align: top; font-size: 8.5pt; line-height: 1.4;">
          <span style="font-size: 8pt; color: #475569;">Disusun Oleh:</span><br>
          <strong>Koordinator / Pengampu</strong>
          <div style="line-height: 16px; font-size: 9.5pt; margin: 4px 0;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <strong>(<?= $nama_koordinator; ?>)</strong><br>
          <?= !empty($nidn_koordinator) ? '<span style="font-size: 8pt; color: #475569;">NIDN. ' . $nidn_koordinator . '</span><br>' : ''; ?>
          <span style="font-size: 8pt; color: #64748b;">Tanggal: <?= $tanggal_usulan; ?></span>
        </td>

        <?php
        $nama_kaprodi = !empty($kaprodi_info->nama) ? $kaprodi_info->nama : ((!empty($kaprodi_rps) && is_object($kaprodi_rps) && method_exists($kaprodi_rps, 'num_rows') && $kaprodi_rps->num_rows() > 0) ? $kaprodi_rps->row()->nm_koordinator : '');
        $nidn_kaprodi = !empty($kaprodi_info->nidn) ? $kaprodi_info->nidn : '';
        $jabatan_kaprodi = !empty($kaprodi_info->jabatan) ? $kaprodi_info->jabatan : ((!empty($kaprodi_rps) && is_object($kaprodi_rps) && method_exists($kaprodi_rps, 'num_rows') && $kaprodi_rps->num_rows() > 0) ? ($kaprodi_rps->row()->jabatan ?? 'Ketua Program Studi') : 'Ketua Program Studi');

        // Fallback jika belum terisi: cari langsung di database
        if (empty($nama_kaprodi)) {
            $ci =& get_instance();
            $id_sms_kap = null;
            if (!empty($data_rps->id_mk)) {
                $mkr = $ci->db->where('id_mk', $data_rps->id_mk)->get('feeder_mata_kuliah')->row();
                if ($mkr) $id_sms_kap = $mkr->id_sms;
            } elseif (!empty($data_rps->kode_mk)) {
                $mkr = $ci->db->where('kode_mk', $data_rps->kode_mk)->get('feeder_mata_kuliah')->row();
                if ($mkr) $id_sms_kap = $mkr->id_sms;
            }
            if (!empty($id_sms_kap)) {
                $kap_row = $ci->db->query("
                    SELECT k.nama as prodi, d.nm_ptk, d.nidn
                    FROM kaprodi k
                    JOIN siakad_dosen d ON k.id_ptk = d.id_ptk
                    WHERE k.id_sms = ? LIMIT 1
                ", [$id_sms_kap])->row();
                if ($kap_row) {
                    $nama_kaprodi = $kap_row->nm_ptk;
                    $nidn_kaprodi = $kap_row->nidn;
                }
            }
        }
        ?>
        <?php if (!empty($nama_kaprodi)) { ?>
          <td class="text-center" style="width: 38%; padding: 6px 8px; vertical-align: top; font-size: 8.5pt; line-height: 1.4;">
            <span style="font-size: 8pt; color: #475569;">Disetujui Oleh:</span><br>
            <strong><?= $jabatan_kaprodi; ?></strong>
            <div style="line-height: 16px; font-size: 9.5pt; margin: 4px 0;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
            <strong>(<?= $nama_kaprodi; ?>)</strong><br>
            <?= !empty($nidn_kaprodi) ? '<span style="font-size: 8pt; color: #475569;">NIDN. ' . $nidn_kaprodi . '</span><br>' : ''; ?>
            <span style="font-size: 8pt; color: #64748b;">Tanggal: <?= $tanggal_pengesahan; ?></span>
          </td>
        <?php } else { ?>
          <td class="text-center" style="width: 38%; padding: 6px 8px; vertical-align: top; font-size: 8.5pt; line-height: 1.4;">
            <span style="font-size: 8pt; color: #475569;">Disetujui Oleh:</span><br>
            <strong>Ketua Program Studi</strong>
            <div style="line-height: 16px; font-size: 9.5pt; margin: 4px 0;">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
            <strong>(_________________________________)</strong><br>
            <span style="font-size: 8pt; color: #64748b;">Tanggal: <?= $tanggal_pengesahan; ?></span>
          </td>
        <?php } ?>
      </tr>
      <tr style="font-size: 8.5pt;">
        <td colspan="2" style="width: 62%; padding: 6px 8px; vertical-align: top; font-size: 8.5pt; line-height: 1.4;">
          <div>
            <span style="font-size: 8pt; color: #475569;">Nama Matakuliah:</span><br>
            <strong style="font-size: 8.5pt; color: #0f172a;"><?= $data_rps->nm_mk; ?><?= (!empty($data_rps->kode_mk) && stripos($data_rps->nm_mk, $data_rps->kode_mk) === false) ? ' (' . $data_rps->kode_mk . ')' : ''; ?></strong>
          </div>
          <div style="margin: 6px 0 5px 0; border-top: 1.5px dashed #64748b; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</div>
          <div>
            <span style="font-size: 8pt; color: #475569;">Semester:</span><br>
            <strong style="font-size: 8.5pt; color: #0f172a;"><?= !empty($data_rps->semester) ? $data_rps->semester : (!empty($data_rps->smt) ? $data_rps->smt : '-'); ?></strong>
          </div>
        </td>
        <td style="width: 38%; padding: 6px 8px; vertical-align: top; font-size: 8.5pt; line-height: 1.4;">
          <span style="font-size: 8pt; color: #475569;">Bobot (SKS):</span><br>
          <strong style="font-size: 8.5pt; color: #0f172a;"><?= !empty($data_rps->sks_total_display) ? $data_rps->sks_total_display : ((int)$data_rps->sks_mk . ' SKS'); ?></strong>
          <?php if (!empty($data_rps->sks_rincian_str)): ?>
            <br><span style="font-size: 7.5pt; font-weight: normal; color: #475569;"><?= $data_rps->sks_rincian_str; ?></span>
          <?php else: ?>
            <br><span style="font-size: 7.5pt; font-weight: normal; color: #475569;">(TM: <?= (int)$data_rps->sks_tm; ?>, Prak: <?= (int)$data_rps->sks_prak; ?>)</span>
          <?php endif; ?>
        </td>
      </tr>

      <!-- Capaian Pembelajaran Lulusan (CPL) -->
      <tr>
        <td colspan="3" style="font-weight: bold; background-color: #f8fafc; padding: 6px 10px; font-size: 9.5pt;">
          Capaian Pembelajaran Lulusan yang Dibebankan Pada Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="3" style="padding: 8px 12px; font-size: 9pt; line-height: 1.55; word-wrap: break-word;">
          <?php
            $clean_cpl = preg_replace('/^(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?(?:CAPAIAN PEMBELAJARAN LULUSAN|CPL)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n+/i', '', trim($data_rps->cpl ?? ''));
            // Hapus kebocoran judul bab CPMK di bagian akhir teks CPL jika ada
            $clean_cpl = preg_replace('/(?:\n+|\A)(?:[#*`\s]*)(?:(?:BAGIAN|BAB|SEKSI|POIN|BAG)\s*)?(?:[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*[\.\:\)]\s*)?(?:CAPAIAN PEMBELAJARAN MATA KULIAH|CPMK|TUJUAN BELAJAR|MATRIKS\s*KORELASI|MATRIKS\s*DISTRIBUSI|BLUEPRINT\s*ASESMEN|RUMUSAN\s*CPMK)[^\n]*\n+[\s\S]*$/i', '', $clean_cpl);
            // Hapus jika ada baris tabel markdown yang bocor
            $clean_cpl = preg_replace('/(?:\n+|\A)\s*\|[^\n]+\|[\s\S]*$/i', '', $clean_cpl);
          ?>
          <?= format_rps_list(trim($clean_cpl)); ?>
        </td>
      </tr>

      <!-- Capaian Pembelajaran Mata Kuliah (CPMK) -->
      <tr>
        <td colspan="3" style="font-weight: bold; background-color: #f8fafc; padding: 6px 10px; font-size: 9.5pt;">
          Capaian Pembelajaran Mata Kuliah (CPMK)
        </td>
      </tr>
      <tr>
        <td colspan="3" style="padding: 8px 12px; font-size: 9pt; line-height: 1.55; word-wrap: break-word;">
          <?= $tb_parts['cpmk_html']; ?>
        </td>
      </tr>

      <!-- Deskripsi Mata Kuliah -->
      <tr>
        <td colspan="3" style="font-weight: bold; background-color: #f8fafc; padding: 6px 10px; font-size: 9.5pt;">
          Deskripsi Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="3" style="padding: 8px 12px; font-size: 9pt; line-height: 1.55; word-wrap: break-word;">
          <?php 
            $clean_deskripsi = $data_rps->deskripsi_mata_kuliah ?? '';
            // 1. Potong jika ada Prasyarat yang ikut bocor ke dalam teks deskripsi
            $clean_deskripsi = preg_replace('/(?:\n+|\A)\s*(?:\d+[\.\)]\s*)?(?:[\-\*•`\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)[\s\S]*$/i', '', $clean_deskripsi);
            // 2. Hapus awalan heading, nomor urut '1.', bullet, atau label 'Deskripsi Singkat:'
            $clean_deskripsi = preg_replace('/^(?:\s*\d+[\.\)]\s*)?(?:[\-\*•`#\s]*)(?:DESKRIPSI\s*(?:SINGKAT)?(?:\s*MATA\s*KULIAH)?|RINGKASAN\s*MATA\s*KULIAH)(?:\*{1,2})?[:\s\-]*\n*/i', '', trim($clean_deskripsi));
            $clean_deskripsi = preg_replace('/^(?:\s*\d+[\.\)]\s*|\s*[\-\*•]\s*)+/u', '', trim($clean_deskripsi));
            $clean_deskripsi = preg_replace('/(?:\n+|\A)(?:[#*_`\s]*)[0-9A-Za-z\.\-_]{1,10}(?:[#*_`\s]*)$/i', '', trim($clean_deskripsi));
            $clean_deskripsi = trim($clean_deskripsi);
          ?>
          <?php if (empty($clean_deskripsi)): ?>
            <p style="margin:0; color:#94a3b8; font-style:italic;">-</p>
          <?php else: ?>
            <p style="margin:0; font-size:9pt; line-height:1.55; text-align:justify; color:#0f172a;"><?= nl2br(render_md_bold(htmlspecialchars($clean_deskripsi))); ?></p>
          <?php endif; ?>
        </td>
      </tr>

      <!-- Prasyarat Mata Kuliah -->
      <tr>
        <td colspan="3" style="font-weight: bold; background-color: #f8fafc; padding: 6px 10px; font-size: 9.5pt;">
          Prasyarat Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="3" style="padding: 8px 12px; font-size: 9pt; line-height: 1.55; word-wrap: break-word;">
          <?php
            $raw_pra = trim($data_rps->prasyarat_mata_kuliah ?? '');
            // Jika kosong tapi di deskripsi_mata_kuliah asli sempat ada prasyarat
            if (empty($raw_pra) || $raw_pra === 'Tidak ada') {
                if (preg_match('/(?:\n+|\A)\s*(?:\d+[\.\)]\s*)?(?:[\-\*•`\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n*([^\n\r]+)/i', $data_rps->deskripsi_mata_kuliah ?? '', $m_pra_leak)) {
                    $raw_pra = trim($m_pra_leak[1]);
                }
            }
            $clean_pra = preg_replace('/^(?:\s*\d+[\.\)]\s*)?(?:[\-\*•`#\s]*)(?:PRASYARAT|MATA KULIAH PRASYARAT)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n*/i', '', $raw_pra);
            $clean_pra = preg_replace('/^(?:\s*\d+[\.\)]\s*|\s*[\-\*•]\s*)+/u', '', trim($clean_pra));
            $clean_pra = trim($clean_pra);
            $clean_pra_low = strtolower(preg_replace('/[^a-z]/', '', $clean_pra));
            $is_empty_pra = empty($clean_pra) || $clean_pra_low === 'tidakada' || $clean_pra_low === 'none' || $clean_pra_low === '' || $clean_pra === '-';
          ?>
          <?php if ($is_empty_pra): ?>
            <p style="margin:0; font-size:9pt; color:#334155;">Tidak ada</p>
          <?php else: ?>
            <p style="margin:0; font-size:9pt; line-height:1.55; color:#0f172a;"><?= render_md_bold(htmlspecialchars($clean_pra)); ?></p>
          <?php endif; ?>
        </td>
      </tr>

      <!-- Daftar Referensi -->
      <tr>
        <td colspan="3" style="font-weight: bold; background-color: #f8fafc; padding: 6px 10px; font-size: 9.5pt;">
          Daftar Referensi
        </td>
      </tr>
      <tr>
        <td colspan="3" style="padding: 8px 12px; font-size: 9pt; line-height: 1.55; word-wrap: break-word;">
          <?php
            $clean_ref = preg_replace('/^(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DAFTAR REFERENSI|REFERENSI|DAFTAR PUSTAKA|PUSTAKA)(?:\s*\([^\)]*\))?(?:\*{1,2})?[:\s\-]*\n+/i', '', trim($data_rps->referensi ?? ''));
            // Hapus kebocoran DETAIL RENCANA PEMBELAJARAN MINGGUAN / TABEL 16 PERTEMUAN jika ada yang tersimpan di kolom referensi
            $clean_ref = preg_replace('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:DETAIL RENCANA|RENCANA PEMBELAJARAN|TABEL 16 PERTEMUAN|DISTRIBUSI BOBOT|PENILAIAN)[^\n]*\n+[\s\S]*$/i', '', $clean_ref);
            // Hapus juga jika ada baris tabel markdown yang bocor ke referensi
            $clean_ref = preg_replace('/(?:^|\n)\|[^\n]+\|[\s\S]*$/i', '', $clean_ref);
          ?>
          <?= format_rps_list(trim($clean_ref)); ?>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Matriks Korelasi CPMK vs Bentuk Penilaian (Di luar Struktur Tabel Utama agar tidak memicu blank page PDF) -->
  <?php if (!empty($tb_parts['matrix_html'])): ?>
    <div style="margin-top: 14px; page-break-inside: avoid;">
      <?= $tb_parts['matrix_html']; ?>
    </div>
  <?php endif; ?>

</div>
