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

<div class="text-center" style="margin-bottom: 12px; text-align: center;">
   <h3 style="margin: 0 0 3px 0; font-size: 12pt; font-weight: 800; letter-spacing: 0.6px; color: #0f172a;">
      <u>DETAIL RENCANA PEMBELAJARAN MINGGUAN (OBE 16 PERTEMUAN)</u>
   </h3>
   <div style="font-size: 8pt; color: #475569; margin-bottom: 4px;">
      Standar SN-Dikti / Permendiktisaintek No. 39 Thn 2025 jo. No. 10 Thn 2026 &bull; SPMI Politeknik Wilmar Bisnis Indonesia
   </div>
   <div style="font-size: 9pt; font-weight: 500; color: #1e293b; line-height: 1.5;">
      <span><strong>Mata Kuliah:</strong> <?= htmlspecialchars($data_rps->nm_mk); ?><?= (!empty($data_rps->kode_mk) && stripos($data_rps->nm_mk, $data_rps->kode_mk) === false) ? ' (' . htmlspecialchars($data_rps->kode_mk) . ')' : ''; ?></span>
      <span style="margin: 0 10px; color: #94a3b8;">|</span>
      <span><strong>Kode MK:</strong> <?= htmlspecialchars($data_rps->kode_mk); ?></span>
      <span style="margin: 0 10px; color: #94a3b8;">|</span>
      <span><strong>Bobot:</strong> <?= !empty($data_rps->sks_total_display) ? htmlspecialchars($data_rps->sks_total_display) : ((int)$data_rps->sks_mk . ' SKS'); ?>
      <?php if (!empty($data_rps->sks_rincian_str)): ?>
         <span style="color: #475569; font-weight: normal;">(<?= htmlspecialchars($data_rps->sks_rincian_str); ?>)</span>
      <?php elseif (!empty($data_rps->sks_tm) || !empty($data_rps->sks_prak)): ?>
         <span style="color: #475569; font-weight: normal;">(TM: <?= (int)$data_rps->sks_tm; ?>, Prak: <?= (int)$data_rps->sks_prak; ?>)</span>
      <?php endif; ?>
      </span>
   </div>
</div>

<?php
// Ekstrak dan mapping referensi dari $data_rps->referensi
$ref_map = [];
$ref_short_map = [];

if (!empty($data_rps->referensi)) {
    $ref_text = $data_rps->referensi;
    if (preg_match('/<[a-z]/i', $ref_text)) {
        $ref_text = preg_replace('/<\/(?:p|li|tr|div)>/i', "\n", $ref_text);
        $ref_text = strip_tags($ref_text);
    }
    $r_lines = explode("\n", str_replace("\r", "", $ref_text));
    $r_idx = 1;
    foreach ($r_lines as $r_line) {
        $r_trim = trim($r_line);
        if ($r_trim === '' || preg_match('/^(?:[#*]{1,4}\s*)?(?:DAFTAR REFERENSI|REFERENSI|PUSTAKA)/i', $r_trim)) continue;
        
        $num = $r_idx;
        $content = $r_trim;
        if (preg_match('/^(\d+)[\.\)]\s*(.*)$/', $r_trim, $m_r)) {
            $num = (int)$m_r[1];
            $content = trim($m_r[2]);
        } elseif (preg_match('/^[\-\*•\x{2022}]\s*(.*)$/u', $r_trim, $m_r)) {
            $content = trim($m_r[1]);
            $content = preg_replace('/^\d+[\.\)]\s*/', '', $content);
        }

        if (!empty($content)) {
            $ref_map[$num] = $content;
            
            $clean_c = preg_replace('/^[\d\.\)\-\*•\s]+/', '', $content);
            $clean_c = preg_replace('/[*_]/', '', $clean_c);
            
            // Pola 1: Standar APA dengan Tahun dalam kurung: Author (Year). Title...
            if (preg_match('/^(.*?)\s*\((\d{4}[a-z]?)\)\s*[\.:,]\s*(.*)$/u', $clean_c, $m_apa)) {
                $raw_auth = trim($m_apa[1]);
                $year = $m_apa[2];
                $rest = trim($m_apa[3]);
                
                // Ambil nama utama penulis
                $auth_display = '';
                if (preg_match('/^([A-Za-z\x{00C0}-\x{017F}\-\'\s]+?)(?:,|\.|\s*&)/u', $raw_auth, $m_sur)) {
                    $auth_display = trim($m_sur[1]);
                    if (stripos($raw_auth, '&') !== false || stripos($raw_auth, 'and') !== false || stripos($raw_auth, 'et al') !== false) {
                        $auth_display .= ' et al.';
                    }
                } else {
                    $auth_display = $raw_auth;
                }
                
                // Ambil judul buku (bagian sebelum titik berikutnya jika ada penerbit)
                $rest_parts = explode('.', $rest);
                $raw_title = trim($rest_parts[0]);
                if (empty($raw_title) && isset($rest_parts[1])) {
                    $raw_title = trim($rest_parts[1]);
                }
                if (empty($raw_title)) $raw_title = $rest;
                
                if (mb_strlen($raw_title) > 42) {
                    $raw_title = mb_substr($raw_title, 0, 40) . '...';
                }
                $stitle = '<strong>' . $auth_display . ' (' . $year . ')</strong>: <i>' . $raw_title . '</i>';
            }
            // Pola 2: Judul diapit tanda kutip "Title"
            elseif (preg_match('/^(.*?)\s*["“‘]([^"”’]+)["”’](.*)$/u', $clean_c, $m_q)) {
                $raw_auth = trim($m_q[1]);
                $raw_title = trim($m_q[2]);
                if (mb_strlen($raw_title) > 42) {
                    $raw_title = mb_substr($raw_title, 0, 40) . '...';
                }
                $auth_display = !empty($raw_auth) ? '<strong>' . trim($raw_auth, ' .,:') . '</strong>: ' : '';
                $stitle = $auth_display . '<i>' . $raw_title . '</i>';
            }
            // Pola 3: Dokumentasi / Web / Modul umum
            else {
                $first_part = explode('.', $clean_c)[0];
                if (mb_strlen($first_part) > 45) {
                    $first_part = mb_substr($first_part, 0, 43) . '...';
                }
                $stitle = '<i>' . $first_part . '</i>';
            }
            
            $ref_short_map[$num] = $stitle;
            $r_idx++;
        }
    }
}

if (!function_exists('render_sumber_belajar')) {
    function render_sumber_belajar($raw_text, $ref_short_map = []) {
        if (empty($raw_text)) return '<span style="color:#94a3b8;">-</span>';
        
        $clean = trim($raw_text);

        // Jika hanya berisi nomor rujukan referensi murni seperti "[1]", "[1, 2]", "[Ref 1]", "Ref 1; Ref 2"
        // (Bukan teks nama buku atau tahun seperti "Silvers (2008)")
        if (preg_match('/^(?:\[?\s*(?:Ref(?:erensi)?|Pustaka)?\s*\d{1,2}\s*\]?[\s,;–\-\/]*)+$/i', $clean)) {
            if (preg_match_all('/\b(\d{1,2})\b/', $clean, $matches)) {
                $nums = array_unique($matches[1]);
                if (!empty($nums)) {
                    $html = '<div style="font-size: 8pt; line-height: 1.35;">';
                    $items = [];
                    foreach ($nums as $n) {
                        $n_int = (int)$n;
                        if (isset($ref_short_map[$n_int])) {
                            $items[] = '<div style="margin-bottom: 3px; padding-bottom: 2px;">'
                                     . '<span style="color: #1e3a8a; font-weight: bold; font-size: 7.5pt;">[Ref ' . $n_int . ']</span> '
                                     . '<span style="color: #0f172a;">' . $ref_short_map[$n_int] . '</span>'
                                     . '</div>';
                        } else {
                            $items[] = '<div style="margin-bottom: 2px;">'
                                     . '<span style="color: #1e3a8a; font-weight: bold; font-size: 7.5pt;">[Ref ' . $n_int . ']</span>'
                                     . '</div>';
                        }
                    }
                    $html .= implode('', $items);
                    $html .= '</div>';
                    return $html;
                }
            }
        }

        // Jika berisi teks kutipan buku/penulis lengkap (misal: "Silvers (2008); Bowdin et al. (2011)")
        $clean_formatted = render_md_bold($clean);
        // Ubah pemisah ";" antar referensi menjadi baris baru yang rapi
        $parts = preg_split('/;\s*/', $clean_formatted);
        if (count($parts) > 1) {
            $formatted_items = [];
            foreach ($parts as $p) {
                $p_trim = trim($p);
                if (!empty($p_trim)) {
                    $formatted_items[] = '<div style="margin-bottom: 3px;">• ' . $p_trim . '</div>';
                }
            }
            return '<div style="font-size: 8pt; line-height: 1.35;">' . implode('', $formatted_items) . '</div>';
        }

        return '<div style="font-size: 8pt; line-height: 1.35;">' . $clean_formatted . '</div>';
    }
}

// Ekstrak dan mapping Sub-CPMK rumusan lengkap dari $data_rps->tujuan_belajar / raw_ai_output
$sub_cpmk_map = [];
$source_cpmk_text = (!empty($data_rps->tujuan_belajar) ? $data_rps->tujuan_belajar : '') . "\n" . (!empty($data_rps->raw_ai_output) ? $data_rps->raw_ai_output : '');

if (!empty($source_cpmk_text)) {
    // Pola 1: Baris tabel markdown: | CPMK-1 | Sub-CPMK 1.1 | Rumusan Kemampuan... |
    if (preg_match_all('/\|\s*(?:CPMK[-_\s]*\d+)?\s*\|\s*(Sub[-_\s]*CPMK\s*[\d\.]+)\s*\|\s*([^\|]+)\|/i', $source_cpmk_text, $m_sub_tbl, PREG_SET_ORDER)) {
        foreach ($m_sub_tbl as $st) {
            $k = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $st[1]));
            $desc = trim($st[2]);
            if (!empty($desc) && stripos($desc, 'Bahan Kajian') === false && stripos($desc, 'Kemampuan Akhir') === false && stripos($desc, '---') === false) {
                $sub_cpmk_map[$k] = $desc;
            }
        }
    }

    // Pola 2: Daftar teks: Sub-CPMK 1.1: Rumusan...
    if (preg_match_all('/(?:^|\n)\s*[\-\*•]?\s*(Sub[-_\s]*CPMK\s*[\d\.]+)\s*[:\-]\s*([^\n\r]+)/i', $source_cpmk_text, $m_sub_txt, PREG_SET_ORDER)) {
        foreach ($m_sub_txt as $st) {
            $k = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $st[1]));
            $desc = trim($st[2]);
            if (!empty($desc) && empty($sub_cpmk_map[$k])) {
                $sub_cpmk_map[$k] = $desc;
            }
        }
    }
}

if (!function_exists('render_kemampuan_akhir')) {
    function render_kemampuan_akhir($value, $sub_cpmk_map = []) {
        $raw = trim($value->kolom_3 ?? '');
        $minggu = (int)($value->kolom_1 ?? 0);

        // Pertemuan 8 (UTS)
        if ($minggu === 8 || stripos($raw, 'UTS') !== false || stripos($raw, 'Tengah Semester') !== false) {
            if (mb_strlen($raw) < 25 || stripos($raw, 'Evaluasi') === false) {
                return '<strong>Evaluasi Tengah Semester (UTS):</strong> Mengukur ketercapaian Sub-CPMK Minggu 1 s.d. 7';
            }
            return render_md_bold($raw);
        }

        // Pertemuan 16 (UAS)
        if ($minggu === 16 || stripos($raw, 'UAS') !== false || stripos($raw, 'Akhir Semester') !== false) {
            if (mb_strlen($raw) < 25 || stripos($raw, 'Evaluasi') === false) {
                return '<strong>Evaluasi Akhir Semester (UAS):</strong> Mengukur ketercapaian seluruh CPMK (Komprehensif)';
            }
            return render_md_bold($raw);
        }

        if (empty($raw)) return '<span style="color:#94a3b8;">-</span>';

        // Bersihkan raw
        $clean = preg_replace('/[*_`]/', '', $raw);

        // Cek apakah raw hanya berisi kode singkat seperti "Sub-CPMK 1.1" atau "Sub-CPMK 2.1"
        $norm_key = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $clean));
        if (isset($sub_cpmk_map[$norm_key]) && (mb_strlen($clean) <= 22 || preg_match('/^Sub[-_\s]*CPMK\s*[\d\.]+$/i', trim($clean)))) {
            $code_display = trim($raw);
            if (!preg_match('/[:\-]$/', $code_display)) $code_display .= ':';
            return '<strong>' . htmlspecialchars($code_display) . '</strong> ' . render_md_bold($sub_cpmk_map[$norm_key]);
        }

        // Cek variasi angka saja seperti "1.1"
        if (preg_match('/^(\d+\.\d+)$/', trim($clean), $m_num)) {
            $num_key = 'SUBCPMK' . str_replace('.', '', $m_num[1]);
            if (isset($sub_cpmk_map[$num_key])) {
                return '<strong>Sub-CPMK ' . $m_num[1] . ':</strong> ' . render_md_bold($sub_cpmk_map[$num_key]);
            }
        }

        return render_md_bold($raw);
    }
}

if (!function_exists('render_md_bold')) {
   function render_md_bold($text) {
      if (empty($text)) return '';
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
?>
<table class="table table-black" style="width: 100%; table-layout: fixed;">
   <colgroup>
      <col style="width: 3.5%;" /> <!-- Minggu -->
      <col style="width: 3.5%;" /> <!-- Pertemuan -->
      <col style="width: 14.5%;" /><!-- Sub-CPMK -->
      <col style="width: 11.5%;" /><!-- Bahan Kajian -->
      <col style="width: 7.5%;" /> <!-- Bentuk & Modalitas -->
      <col style="width: 7.5%;" /> <!-- Metode -->
      <col style="width: 5.5%;" /> <!-- Pengalaman Belajar -->
      <col style="width: 3.5%;" /> <!-- T -->
      <col style="width: 3.5%;" /> <!-- P -->
      <col style="width: 7.5%;" /> <!-- Kriteria -->
      <col style="width: 11%;" />  <!-- Indikator -->
      <col style="width: 3.5%;" /> <!-- Bobot -->
      <col style="width: 17.5%;" /><!-- Sumber Belajar -->
   </colgroup>
   <thead>
      <tr>
         <th rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">M</th>
         <th rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">P</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Kemampuan Akhir Tiap <br>Tahapan Belajar</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Bahan Kajian</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Bentuk dan <br>Modalitas<br>Pembelajaran</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Metode<br>Pembelajaran</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Pengalaman<br>Belajar</th>
         <th colspan="2" style="text-align: center; vertical-align: middle;">Alokasi Waktu</th>
         <th colspan="3" style="text-align: center; vertical-align: middle;">Evaluasi/Penilaian</th>
         <th rowspan="2" style="text-align: center; vertical-align: middle;">Sumber<br>Belajar</th>
      </tr>
      <tr>
         <th style="text-align: center; vertical-align: middle;">T</th>
         <th style="text-align: center; vertical-align: middle;">Pr</th>
         <th style="text-align: center; vertical-align: middle;">Kriteria</th>
         <th style="text-align: center; vertical-align: middle;">Indikator</th>
         <th style="text-align: center; vertical-align: middle;">Bobot</th>
      </tr>
      <tr>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(1)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(2)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(3)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(4)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(5)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(6)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(7)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(8)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(9)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(10)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(11)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(12)</th>
         <th class="text-center" style="font-size: 8.5pt; font-weight: normal; color: #475569;">(13)</th>
      </tr>
   </thead>
   <tbody>
      <?php
      foreach ($data_rps_detail as $key => $value) {
      ?>
         <tr>
            <td class="text-center" style="white-space: nowrap; font-weight: 600; vertical-align: top;"><?= !empty($value->kolom_1) ? $value->kolom_1 : ($key + 1); ?></td>
            <td class="text-center" style="white-space: nowrap; font-weight: 600; vertical-align: top;"><?= !empty($value->kolom_2) ? $value->kolom_2 : ($key + 1); ?></td>
            <td><?= render_kemampuan_akhir($value, $sub_cpmk_map); ?></td>
            <td><?= render_md_bold($value->kolom_4); ?></td>
            <td><?= render_md_bold($value->kolom_5); ?></td>
            <td><?= render_md_bold($value->kolom_6); ?></td>
            <td><?= render_md_bold($value->kolom_7); ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_8; ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_9; ?></td>
            <td><?= render_md_bold($value->kolom_10); ?></td>
            <td><?= render_md_bold($value->kolom_11); ?></td>
            <td class="text-center" style="vertical-align: top; font-weight: 600;"><?= $value->kolom_12; ?></td>
            <td><?= render_sumber_belajar($value->kolom_13, $ref_short_map); ?></td>
         </tr>
      <?php
      }
      ?>
   </tbody>
</table>
<div style="font-size: 8pt; color: #475569; margin-top: 6px; font-style: italic;">
   <strong>Keterangan:</strong> M = Minggu, P = Pertemuan, T = Teori (dalam menit), Pr = Praktek (dalam menit)
</div>