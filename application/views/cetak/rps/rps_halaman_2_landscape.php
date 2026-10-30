<h3><b><u>DETAIL RENCANA PERTEMUAN MINGGUAN</u></b></h3>
<table class="table table-black" style="width: 100%; border-collapse: collapse;">
   <thead>
      <tr>
         <th rowspan="2" style="width: 25px;" class="text-center">M</th>
         <th rowspan="2" style="width: 25px;" class="text-center">P</th>
         <th rowspan="2">Kemampuan Akhir Tiap <br>Tahapan Belajar</th>
         <th rowspan="2">Bahan Kajian</th>
         <th rowspan="2">Bentuk dan <br>Modalitas<br>Pembelajaran</th>
         <th rowspan="2">Metode Pembelajaran</th>
         <th rowspan="2">Pengalaman Belajar</th>
         <th colspan="2" class="text-center">Alokasi Waktu</th>
         <th colspan="3" class="text-center">Evaluasi/Penilaian</th>
         <th rowspan="2">Sumber Belajar</th>
      </tr>
      <tr>
         <th style="width: 25px;" class="text-center">T</th>
         <th style="width: 25px;" class="text-center">Pr</th>
         <th>Kriteria</th>
         <th>Indikator</th>
         <th style="width: 35px;" class="text-center">Bobot</th>
      </tr>
      <tr>
         <th class="text-center">(1)</th>
         <th class="text-center">(2)</th>
         <th class="text-center">(3)</th>
         <th class="text-center">(4)</th>
         <th class="text-center">(5)</th>
         <th class="text-center">(6)</th>
         <th class="text-center">(7)</th>
         <th class="text-center">(8)</th>
         <th class="text-center">(9)</th>
         <th class="text-center">(10)</th>
         <th class="text-center">(11)</th>
         <th class="text-center">(12)</th>
         <th class="text-center">(13)</th>
      </tr>
   </thead>
   <tbody>
      <?php
      if (!empty($data_rps_detail)) {
         foreach ($data_rps_detail as $key => $value) {
      ?>
         <tr>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_1; ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_2; ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_3); ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_4); ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_5); ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_6); ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_7); ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_8; ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_9; ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_10); ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_11); ?></td>
            <td class="text-center" style="vertical-align: top;"><?= $value->kolom_12; ?></td>
            <td style="vertical-align: top;"><?= format_matrix_cell($value->kolom_13); ?></td>
         </tr>
      <?php
         }
      }
      ?>
   </tbody>
</table>
<p style="font-size: 10px; margin-top: 5px; font-style: italic;">
   <b>Keterangan:</b> M = Minggu, P = Pertemuan, T = Teori, Pr = Praktek
</p>