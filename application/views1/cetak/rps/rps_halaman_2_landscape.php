<h3><b><u>DETAIL RENCANA PERTEMUAN MINGGUAN</u></b></h3>
<table class="table table-black">
   <thead>
      <tr>
         <th rowspan="2">M</th>
         <th rowspan="2">P</th>
         <th rowspan="2">Kemampuan Akhir Tiap <br>Tahapan Belajar</th>
         <th rowspan="2">Bahan Kajian</th>
         <th rowspan="2">Bentuk dan <br>Modalitas<br>Pembelajaran</th>
         <th rowspan="2">Metode Pembelajaran</th>
         <th rowspan="2">Pengalaman Belajar</th>
         <th colspan="2">Alokasi Waktu</th>
         <th colspan="3">Evaluasi/Penilaian</th>
         <th rowspan="2">Sumber Belajar</th>
      </tr>
      <tr>
         <th>T</th>
         <th>Pr</th>
         <th>Kriteria</th>
         <th>Indikator</th>
         <th>Bobot</th>
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
      foreach ($data_rps_detail as $key => $value) {
      ?>
         <tr>
            <td class="text-center"><?= !empty($value->kolom_1) ? $value->kolom_1 : ($key + 1); ?></td>
            <td class="text-center"><?= !empty($value->kolom_2) ? $value->kolom_2 : ($key + 1); ?></td>
            <td><?= $value->kolom_3; ?></td>
            <td><?= $value->kolom_4; ?></td>
            <td><?= $value->kolom_5; ?></td>
            <td><?= $value->kolom_6; ?></td>
            <td><?= $value->kolom_7; ?></td>
            <td class="text-center"><?= $value->kolom_8; ?></td>
            <td class="text-center"><?= $value->kolom_9; ?></td>
            <td><?= $value->kolom_10; ?></td>
            <td><?= $value->kolom_11; ?></td>
            <td class="text-center"><?= $value->kolom_12; ?></td>
            <td><?= $value->kolom_13; ?></td>
         </tr>
      <?php
      }
      ?>
   </tbody>
</table>
<div style="font-size: 8pt; color: #475569; margin-top: 6px; font-style: italic;">
   <strong>Keterangan:</strong> M = Minggu, P = Pertemuan, T = Teori (dalam menit), Pr = Praktek (dalam menit)
</div>