<body>

  <?php
  $tanggal_usulan = !empty($data_rps->tanggal_rps) ? date('d-m-Y', strtotime($data_rps->tanggal_rps)) : '-';
  $tanggal_pengesahan = !empty($data_rps->tanggal_pengesahan) ? date('d-m-Y', strtotime($data_rps->tanggal_pengesahan)) : '-';
  ?>

  <table autosize="1" class="table table-black" style="margin-bottom: 3px !important;">
    <tbody>
      <tr>
        <td colspan="2" class="text-center" style="padding:10px;">
          <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="160px" />
        </td>
        <td class="text-center" style="vertical-align: middle;" colspan=4>
          <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px;margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>
        <!-- <td class="text-center" style="vertical-align: middle; width:100px">
          <h4><strong>KODE REVISI</strong></h4>
          <h1><strong><?= $data_rps->kode_revisi; ?></strong></h1>
        </td> -->
      </tr>
      <tr>
        <td class="text-center" colspan="6">
          <h2><strong>RENCANA PEMBELAJARAN SEMESTER</strong></h2>
        </td>

      </tr>
      <tr>
        <td class="text-center">Jenis Dokumen: <br><br>
          <p style="font-size: 15px;font-weight:bold">FORMULIR<br> SPMI</p>
        </td>
        <td colspan="2" class="text-center">Disusun Oleh:<br>Kordinator <br><br><br><br>(<?= $koordinator_rps->nm_ptk ?>)<br>Tanggal: <?= $tanggal_usulan; ?></td>

        <?php
        if ($kaprodi_rps->num_rows() > 0) {
        ?>
          <td colspan="2" class="text-center">Disetujui Oleh:<br><?= $kaprodi_rps->row()->jabatan; ?><br><br><br><br>(<?= $kaprodi_rps->row()->nm_koordinator; ?>)<br>Tanggal:</td>
        <?php
        } else {
        ?>
          <td colspan="2" class="text-center">Disetujui Oleh:<br><br><br><br>(_________________________________)<br>Tanggal:</td>
        <?php
        }
        ?>
        <td class="text-center">Dosen Pengampu:<br><?= $data_rps->dosen_pengampu; ?></td>

      </tr>
      <tr>
        <td colspan="2">
          Program Studi:<br><?= $nm_lemb; ?>
        </td>
        <!-- <td style="width: 5px !important;">
          Sem:<br><?= $data_rps->semester; ?>

        </td> -->
        <td colspan="2">
          Nama Matakuliah:<br><?= $data_rps->nm_mk; ?>

        </td>
        <td style="width: 150px;">
          Kode:<br><?= $data_rps->kode_mk; ?>

        </td>
        <td style="width: 150px;">
          Bobot (SKS):<br><?= $data_rps->sks_tm . '/' . $data_rps->sks_prak; ?>

        </td>

      </tr>
    </tbody>
  </table>
  <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><b>Capaian Pembelajaran Lulusan yang Dibebankan Pada Mata Kuliah</b></div>
        <div class="divTableCell" style="padding-left: 80px;"><?= clean_style($data_rps->cpl); ?></div>
      </div>
    </div>
  </div>

  <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><b>Tujuan Belajar (CPMK)</b></div>
        <div class="divTableCell" style="padding-left: 80px;"><?= clean_style($data_rps->tujuan_belajar); ?></div>
      </div>
    </div>
  </div>

  <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><b>Deskripsi Mata Kuliah</b></div>
        <div class="divTableCell" style="padding-left: 80px;"><?= clean_style($data_rps->deskripsi_mata_kuliah); ?></div>
      </div>
    </div>
  </div>
  <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><b>Prasyarat Mata Kuliah</b></div>
        <div class="divTableCell" style="padding-left: 80px;"><?= clean_style($data_rps->prasyarat_mata_kuliah); ?></div>
      </div>
    </div>
  </div>
  <div class="divTable" style="width: 100%; border: 1px solid #7a7a7a;">
    <div class="divTableBody">
      <div class="divTableRow">
        <div class="divTableCell"><b>Daftar Referensi</b></div>
        <div class="divTableCell" style="padding-left: 80px;"><?= clean_style($data_rps->referensi); ?></div>
      </div>
    </div>
  </div>




  <!-- <table autosize="1" class="table table-black" style="margin-bottom: 3px !important;">
    <tbody>
      <tr>
        <td colspan="2" style="background-color:rgb(229, 229, 229);"><b>Capaian Pembelajaran Lulusan yang Dibebankan Pada Mata Kuliah</b></td>
        <?php
        if (empty($data_rps->cpl)) {
          echo '<td colspan="4" style="color:grey">Capaian Pembelajaran Lulusan Belum Diisi, Silahkan isi dengan format berikut:<br>[KODE Referensi] Capaian yang diinginkan<br><br>Misal:<br>[S1] Beriman, bertaqwa, berakhlak mulia sesuain dengan nilai-nilai Pancasila</td>';
        ?>
        <?php
        } else {
          echo '<td colspan="4" style="word-wrap: break-word"><div>' . clean_style($data_rps->cpl, "<br><p><ol><li><b>") . '</div></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="2" style="background-color:rgb(229, 229, 229);"><b>Tujuan Belajar (CPMK)</b></td>
        <?php
        if (empty($data_rps->tujuan_belajar)) {
          echo '<td colspan="4" style="color:grey">Tujuan Belajar Belum Diisi, Silahkan isi dengan format berikut:<br>[KODE Referensi] Tujuan Belajar yang diinginkan<br><br>Misal:<br>[TB1] Menguasai konsep teoritis mengenai objek dan topik tertentu</td>';
        ?>
        <?php
        } else {
          echo '<td colspan="4">' . clean_style($data_rps->tujuan_belajar, "<br><p><ol><li><b>") . '</td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="2" style="background-color:rgb(229, 229, 229);"><b>Deskripsi Mata Kuliah</b></td>
        <td colspan="4">
          <?= clean_style($data_rps->deskripsi_mata_kuliah, '<br><p><ol><li><b><ol><li>'); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="background-color:rgb(229, 229, 229);"><b>Prasyarat Mata Kuliah</b></td>
        <td colspan="4">
          <?= clean_style($data_rps->prasyarat_mata_kuliah, '<br><p><ol><li><b><ol><li>'); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="background-color:rgb(229, 229, 229);"><b>Daftar Referensi</b></td>
        <td colspan="4">
          <?= clean_style($data_rps->referensi); ?>
        </td>
      </tr>
    </tbody>
  </table> -->


</body>