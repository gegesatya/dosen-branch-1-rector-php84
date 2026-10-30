<div class="text-center" style="margin-bottom: 12px; text-align: center;">
   <h3 style="margin: 0 0 4px 0; font-size: 11.5pt; font-weight: bold; letter-spacing: 0.5px;">
      <u>DETAIL RENCANA PEMBELAJARAN MINGGUAN</u>
   </h3>
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
if (!function_exists('render_md_bold')) {
   function render_md_bold($text) {
      if (empty($text)) return '';
      // Clean attributes like data-start, data-end, inline styles
      $text = preg_replace('/\s+data-[a-z0-9_\-]+="[^"]*"/i', '', $text);
      $text = preg_replace('/\s+data-[a-z0-9_\-]+=\'[^\']*\'/i', '', $text);
      $text = preg_replace('/\s*style="[^"]*"/i', '', $text);
      // Convert strong to b, em to i
      $text = preg_replace('/<(\/)?strong\b[^>]*>/i', '<$1b>', $text);
      $text = preg_replace('/<(\/)?em\b[^>]*>/i', '<$1i>', $text);
      // Convert triple asterisks ***text*** or **_text_** to <b><i>text</i></b>
      $text = preg_replace('/\*\*\*(.*?)\*\*\*/s', '<b><i>$1</i></b>', $text);
      $text = preg_replace('/\*\*_(.*?)_\*\*/s', '<b><i>$1</i></b>', $text);
      // Convert markdown **text** to <b>text</b>
      $text = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $text);
      // Convert __text__ to <b>text</b>
      $text = preg_replace('/(?<!\w)__(.*?)__(?!\w)/s', '<b>$1</b>', $text);
      // Convert single asterisk *text* to <i>text</i>
      $text = preg_replace('/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/', '<i>$1</i>', $text);
      // Convert single underscore _text_ to <i>text</i>
      $text = preg_replace('/(?<![a-zA-Z0-9_])_(?!\s)([^_\n]+?)(?<!\s)_(?![a-zA-Z0-9_])/', '<i>$1</i>', $text);
      // Remove span, font tags
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
            <td><?= render_md_bold($value->kolom_3); ?></td>
            <td><?= render_md_bold($value->kolom_4); ?></td>
            <td><?= render_md_bold($value->kolom_5); ?></td>
            <td><?= render_md_bold($value->kolom_6); ?></td>
            <td><?= render_md_bold($value->kolom_7); ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_8; ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_9; ?></td>
            <td><?= render_md_bold($value->kolom_10); ?></td>
            <td><?= render_md_bold($value->kolom_11); ?></td>
            <td class="text-center" style="vertical-align: top; font-weight: 600;"><?= $value->kolom_12; ?></td>
            <td><?= render_md_bold($value->kolom_13); ?></td>
         </tr>
      <?php
      }
      ?>
   </tbody>
</table>
<div style="font-size: 8pt; color: #475569; margin-top: 6px; font-style: italic;">
   <strong>Keterangan:</strong> M = Minggu, P = Pertemuan, T = Teori (dalam menit), Pr = Praktek (dalam menit)
</div>