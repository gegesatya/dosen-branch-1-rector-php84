<?php if (!empty($is_mpdf)): ?>

  <htmlpagefooter name="docFooter">
    <div style="border-top: 0.5px solid #cbd5e1; padding-top: 4px; font-size: 8pt; color: #64748b; font-family: Helvetica, sans-serif;">
      <table style="width: 100%; border: none !important; border-collapse: collapse;">
        <tr>
          <td style="width: 65%; border: none !important; padding: 0; font-size: 8pt; color: #64748b; text-align: left;">
            Rancangan Tugas - <?= !empty($data_rps->nm_mk) ? htmlspecialchars($data_rps->nm_mk) : ''; ?>
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

  // Ekstrak dari rancangan_tugas_markdown jika ada dan kolom spesifik masih kosong
  $tugas_md = !empty($data_rps->rancangan_tugas_markdown) ? $data_rps->rancangan_tugas_markdown : '';
  
  $val_tujuan = !empty($data_rps->tujuan_tugas) ? $data_rps->tujuan_tugas : '';
  $val_obyek  = !empty($data_rps->tugas_obyek_garapan) ? $data_rps->tugas_obyek_garapan : '';
  $val_batas  = !empty($data_rps->tugas_batasan) ? $data_rps->tugas_batasan : '';
  $val_metode = !empty($data_rps->tugas_metode) ? $data_rps->tugas_metode : '';
  $val_luaran = !empty($data_rps->tugas_luaran) ? $data_rps->tugas_luaran : '';
  $val_rapi   = !empty($data_rps->tugas_penilaian_kerapian) ? $data_rps->tugas_penilaian_kerapian : '';
  $val_tepat  = !empty($data_rps->tugas_ketepatan) ? $data_rps->tugas_ketepatan : '';
  $val_ke     = !empty($data_rps->tugas_ke) ? $data_rps->tugas_ke : '1';
  $val_minggu = !empty($data_rps->tugas_minggu_ke) ? $data_rps->tugas_minggu_ke : '4 s.d. 7';

  if (!empty($tugas_md)) {
      if (empty($val_tujuan) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:\d+[\.\)]\s*)?TUJUAN TUGAS[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:\d+[\.\)]\s*)?(?:URAIAN|OBYEK|METODE|KRITERIA|JADWAL))|\Z)/i', $tugas_md, $tm)) {
          $val_tujuan = trim($tm[1]);
      }
      if (empty($val_obyek) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[a-d][\.\)]\s*)?Obyek Garapan[^\n]*[:\-]?\s*([^\n\r]+)/i', $tugas_md, $tm)) {
          $val_obyek = trim($tm[1]);
      }
      if (empty($val_batas) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[a-d][\.\)]\s*)?Yang Harus Dikerjakan[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[a-d][\.\)]\s*)?(?:Metode|Luaran|Kriteria))|\Z)/i', $tugas_md, $tm)) {
          $val_batas = trim($tm[1]);
      }
      if (empty($val_metode) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[a-d][\.\)]\s*)?Metode[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:[a-d][\.\)]\s*)?(?:Luaran|Kriteria))|\Z)/i', $tugas_md, $tm)) {
          $val_metode = trim($tm[1]);
      }
      if (empty($val_luaran) && preg_match('/(?:^|\n)(?:[#*]{1,4}\s*)?(?:[a-d][\.\)]\s*)?Deskripsi Luaran[^\n]*\n+([\s\S]*?)(?=(?:\n+[#*]{1,4}\s*(?:\d+[\.\)]\s*)?(?:KRITERIA|RUBRIK|JADWAL))|\Z)/i', $tugas_md, $tm)) {
          $val_luaran = trim($tm[1]);
      }
  }

  // Fallback standar SPMI berbasis data RPS jika data masih belum diisi
  if (empty($val_tujuan)) {
      $val_tujuan = !empty($data_rps->tujuan_belajar) ? $data_rps->tujuan_belajar : ("Mahasiswa mampu menganalisis studi kasus nyata, memecahkan permasalahan kontekstual berbasis data, dan menyusun laporan terstruktur sesuai Capaian Pembelajaran Mata Kuliah (CPMK) " . ($data_rps->nm_mk ?? '') . ".");
  }
  if (empty($val_obyek)) {
      $val_obyek = "Studi kasus industri dan proyek implementasi praktis terkait topik materi perkuliahan " . ($data_rps->nm_mk ?? '') . ".";
  }
  if (empty($val_batas)) {
      $val_batas = "1. Menganalisis permasalahan riil berdasarkan data dan literatur terkini.<br>2. Menyusun alternatif solusi dan rekomendasi strategis secara sistematis.<br>3. Format dokumen laporan standar SPMI WBI (Font Times New Roman 12pt / Calibri 11pt, spasi 1.5, margin 4-3-3-3).";
  }
  if (empty($val_metode)) {
      $val_metode = "Pengerjaan berbasis pendekatan Student-Centered Learning (Case Method / Project-Based Learning) dengan tahapan: observasi/pengumpulan data, analisis kelompok, penyusunan laporan, asistensi berkala dengan dosen pengampu, dan presentasi hasil.";
  }
  if (empty($val_luaran)) {
      $val_luaran = "Laporan Analisis Komprehensif (Format PDF), Ringkasan Eksekutif, dan Slide Presentasi Tugas.";
  }
  if (empty($val_rapi)) {
      $val_rapi = "Kerapian tata letak (layout), sistematika penulisan laporan, kesesuaian format standar SPMI WBI, dan ketepatan waktu pengumpulan (deadline).";
  }
  if (empty($val_tepat)) {
      $val_tepat = "Kedalaman analisis masalah, ketepatan metodologi yang diterapkan, validitas sumber rujukan/data, dan kelayakan solusi/rekomendasi.";
  }

  if (!function_exists('format_tugas_html')) {
      function format_tugas_html($str) {
          if (empty($str)) return '-';

          // Bersihkan code fences markdown (```json, ```markdown, ```)
          $str = preg_replace('/^```[a-zA-Z0-9_\-]*\s*$/m', '', $str);
          $str = str_replace('```', '', $str);

          // Bersihkan penanda validasi & draf
          $str = preg_replace('/`?\s*\[(?:PERLU|BUTUH)\s+VALIDASI[^\]]*\]\s*`?/i', '', $str);
          $str = preg_replace('/`?\s*\[ASUMSI\s+DEFAULT[^\]]*\]\s*`?/i', '', $str);

          // Hapus sisa backticks
          $str = str_replace('`', '', $str);

          // Hapus kebocoran sub-heading judul bab markdown di awal teks (misal: #### D.1 Deskripsi Mata Kuliah, ### 1. TUJUAN TUGAS)
          $str = preg_replace('/^(?:[#*]{1,6}\s*)?(?:[0-9A-Za-z]+[\.\)]\s*)?(?:TUJUAN TUGAS|URAIAN TUGAS|OBYEK GARAPAN|DESKRIPSI MATA KULIAH|DESKRIPSI SINGKAT|DESKRIPSI)[^\n]*\n+/i', '', trim($str));

          // Bersihkan semua hash markdown heading (#, ##, ###, ####, #####, ######)
          $str = preg_replace('/(?:^|\n)#{1,6}\s*([^\n]+)/', "\n$1", $str);
          $str = preg_replace('/#{1,6}\s*/', '', $str);

          // Hapus garis horizontal markdown (---, ***, ___)
          $str = preg_replace('/(?:^|\n)\s*[-*_]{3,}\s*(?=\n|\Z)/', "\n", $str);

          if (strpos($str, '<table') !== false || strpos($str, '<p>') !== false || strpos($str, '<ol>') !== false || strpos($str, '<ul>') !== false) {
              return clean_style($str);
          }

          $str = htmlspecialchars(trim($str));
          $str = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<strong><i>$1</i></strong>', $str);
          $str = preg_replace('/\*\*_(.*?)_\*\*/s', '<strong><i>$1</i></strong>', $str);
          $str = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $str);
          $str = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<strong>$1</strong>', $str);
          $str = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $str);
          $str = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $str);
          $str = nl2br($str);
          return $str;
      }
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
        <td class="text-center" style="width: 24%; padding: 10px 8px; vertical-align: middle;">
          <img src="<?= $logo_src; ?>" width="130px" style="max-width: 130px; height: auto;" />
        </td>
        <td class="text-center" style="vertical-align: middle; width: 76%;" colspan="2">
          <h4 style="font-size: 14px; margin: 0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px; margin: 0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px; margin: 0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>
      </tr>
      <tr>
        <td class="text-center" colspan="3" style="padding: 10px 14px; background: #ffffff; text-align: center; vertical-align: middle;">
          <?php if (!empty($nm_lemb)): ?>
            <div style="font-size: 11pt; font-weight: 800; text-transform: uppercase; color: #0f172a; letter-spacing: 0.5px; margin-bottom: 3px;">
              PROGRAM STUDI <?= preg_replace('/^Program\s+Studi\s+/i', '', $nm_lemb); ?>
            </div>
          <?php endif; ?>
          <h2 style="font-size: 13.5pt; margin: 0 0 3px 0; font-weight: 800; letter-spacing: 0.6px; color: #0f172a;">
            RANCANGAN TUGAS
          </h2>
          <div style="font-size: 8.5pt; font-weight: bold; color: #475569; letter-spacing: 0.5px;">
            FORMULIR SPMI (SISTEM PENJAMINAN MUTU INTERNAL)
          </div>
        </td>
      </tr>
      <tr style="font-size: 8.5pt;">
        <td class="text-center" style="width: 24%; padding: 8px 6px; vertical-align: middle; background-color: #fafbfc;">
          <span style="font-size: 7.5pt; color: #475569; text-transform: uppercase; font-weight: 600;">Jenis Dokumen:</span>
          <div style="margin-top: 4px; font-size: 10pt; font-weight: 800; letter-spacing: 0.5px; line-height: 1.25; color: #0f172a;">FORMULIR<br>SPMI WBI</div>
        </td>
        <?php
        $nama_koordinator = (!empty($koordinator_rps) && is_object($koordinator_rps) && !empty($koordinator_rps->nm_ptk)) ? $koordinator_rps->nm_ptk : ($data_rps->dosen_pengampu ?? '-');
        $nidn_koordinator = (!empty($koordinator_rps) && is_object($koordinator_rps) && !empty($koordinator_rps->nidn)) ? $koordinator_rps->nidn : '';
        if (empty($nidn_koordinator)) {
            $ci =& get_instance();
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
        <td class="text-center" style="width: 38%; padding: 6px 8px; vertical-align: bottom; height: 85px;">
          <span style="font-size: 8.5pt;">Disusun Oleh:<br>Koordinator / Pengampu</span>
          <div style="height: 40px; line-height: 14px;">&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <span style="font-size: 9pt; font-weight: bold; white-space: nowrap;"><u>(<?= $nama_koordinator; ?>)</u></span><br>
          <span style="font-size: 8pt; color: #475569;"><?= !empty($nidn_koordinator) ? 'NIDN. ' . $nidn_koordinator . '<br>' : ''; ?>Tanggal: <?= $tanggal_usulan; ?></span>
        </td>

        <?php
        $nama_kaprodi = !empty($kaprodi_info->nama) ? $kaprodi_info->nama : ((!empty($kaprodi_rps) && is_object($kaprodi_rps) && method_exists($kaprodi_rps, 'num_rows') && $kaprodi_rps->num_rows() > 0) ? $kaprodi_rps->row()->nm_koordinator : '');
        $nidn_kaprodi = !empty($kaprodi_info->nidn) ? $kaprodi_info->nidn : '';
        $jabatan_kaprodi = !empty($kaprodi_info->jabatan) ? $kaprodi_info->jabatan : ((!empty($kaprodi_rps) && is_object($kaprodi_rps) && method_exists($kaprodi_rps, 'num_rows') && $kaprodi_rps->num_rows() > 0) ? ($kaprodi_rps->row()->jabatan ?? 'Ketua Program Studi') : 'Ketua Program Studi');
        ?>
        <td class="text-center" style="width: 38%; padding: 6px 8px; vertical-align: bottom; height: 85px;">
          <span style="font-size: 8.5pt;">Disetujui Oleh:<br><?= $jabatan_kaprodi; ?></span>
          <div style="height: 40px; line-height: 14px;">&nbsp;<br>&nbsp;<br>&nbsp;</div>
          <span style="font-size: 9pt; font-weight: bold; white-space: nowrap;"><?= !empty($nama_kaprodi) ? '<u>(' . $nama_kaprodi . ')</u>' : '(___________________)'; ?></span><br>
          <span style="font-size: 8pt; color: #475569;"><?= !empty($nidn_kaprodi) ? 'NIDN. ' . $nidn_kaprodi . '<br>' : ''; ?>Tanggal: <?= $tanggal_pengesahan ?? '-'; ?></span>
        </td>
      </tr>
      <tr>
        <td style="width: 24%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Kode Mata Kuliah:<br>
          <strong><?= !empty($data_rps->kode_mk) ? $data_rps->kode_mk : '-'; ?></strong>
        </td>
        <td style="width: 38%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Nama Matakuliah:<br>
          <strong><?= $data_rps->nm_mk; ?><?= (!empty($data_rps->kode_mk) && stripos($data_rps->nm_mk, $data_rps->kode_mk) === false) ? ' (' . $data_rps->kode_mk . ')' : ''; ?></strong>
        </td>
        <td style="width: 38%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Bobot (SKS):<br>
          <b><?= !empty($data_rps->sks_total_display) ? $data_rps->sks_total_display : ((int)$data_rps->sks_mk . ' SKS'); ?></b>
          <?php if (!empty($data_rps->sks_rincian_str)): ?>
            <br><span style="font-size: 8pt; font-weight: normal; color: #334155;"><?= $data_rps->sks_rincian_str; ?></span>
          <?php else: ?>
            <br><span style="font-size: 8pt; font-weight: normal; color: #334155;">(TM: <?= (int)$data_rps->sks_tm; ?>, Prak: <?= (int)$data_rps->sks_prak; ?>)</span>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td style="width: 24%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Dosen Pengampu:<br><strong><?= $data_rps->dosen_pengampu; ?></strong>
        </td>
        <td style="width: 38%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Minggu Ke:<br><strong><?= $val_minggu; ?></strong>
        </td>
        <td style="width: 38%; padding: 6px 8px; font-size: 9pt; vertical-align: top;">
          Tugas Ke:<br><strong><?= $val_ke; ?></strong>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- SEKSI KONTEN RANCANGAN TUGAS (FLOWING DOCUMENT) -->
  <div style="margin-top: 10px;">

    <!-- 1. TUJUAN TUGAS -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 6px;">
      1. TUJUAN TUGAS
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 20px; padding-right: 4px; margin-bottom: 12px;">
      <?= format_tugas_html($val_tujuan); ?>
    </div>

    <!-- 2. URAIAN TUGAS -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 6px;">
      2. URAIAN TUGAS
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 10px; padding-right: 4px; margin-bottom: 12px;">
      
      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">a. Obyek Garapan:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_obyek); ?>
      </div>

      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">b. Yang Harus Dikerjakan dan Batasan-batasan:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_batas); ?>
      </div>

      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">c. Metode / Cara Pengerjaan, Acuan yang Digunakan:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_metode); ?>
      </div>

      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">d. Deskripsi Luaran Tugas yang Dihasilkan / Dikerjakan:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_luaran); ?>
      </div>

    </div>

    <!-- 3. KRITERIA PENILAIAN -->
    <div style="background-color: #f1f5f9; border: 1px solid #7a7a7a; padding: 5px 8px; font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 6px;">
      3. KRITERIA PENILAIAN
    </div>
    <div style="font-size: 10pt; line-height: 1.6; text-align: justify; padding-left: 10px; padding-right: 4px; margin-bottom: 12px;">

      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">a. Kerapian:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_rapi); ?>
      </div>

      <div style="font-weight: bold; margin-top: 6px; margin-bottom: 4px; color: #0f172a;">b. Ketepatan / Kebenaran:</div>
      <div style="padding-left: 16px; margin-bottom: 8px;">
        <?= format_tugas_html($val_tepat); ?>
      </div>

    </div>

  </div>