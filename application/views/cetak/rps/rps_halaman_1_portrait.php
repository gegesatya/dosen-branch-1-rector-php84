<body>

  <?php
  $tanggal_usulan = !empty($data_rps->tanggal_rps) ? date('d-m-Y', strtotime($data_rps->tanggal_rps)) : '-';
  $tanggal_pengesahan = !empty($data_rps->tanggal_pengesahan) ? date('d-m-Y', strtotime($data_rps->tanggal_pengesahan)) : '-';
  ?>

  <table autosize="1" class="table table-black" style="margin-bottom: 0px !important;">
    <tbody>
      <tr>
        <td colspan="2" class="text-center" style="padding:10px; width: 22%;">
          <img src="<?= FCPATH . 'assets/image_web/logo-white-3.png'; ?>" width="160px" />
        </td>
        <td class="text-center" style="vertical-align: middle; width: 78%;" colspan="4">
          <h4 style="font-size: 14px; margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px; margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px; margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>
      </tr>
      <tr>
        <td class="text-center" colspan="6" style="padding: 6px;">
          <h3 style="margin: 0; font-size: 13px; font-weight: bold;">RENCANA PEMBELAJARAN SEMESTER</h3>
        </td>
      </tr>
      <tr>
        <td class="text-center" style="width: 18%;">
          Jenis Dokumen: <br><br>
          <p style="font-size: 13px; font-weight:bold;">FORMULIR<br>SPMI</p>
        </td>
        <td colspan="2" class="text-center" style="width: 27%;">
          Disusun Oleh:<br>Kordinator <br><br><br><br>
          (<?= $koordinator_rps->nm_ptk ?>)<br>Tanggal: <?= $tanggal_usulan; ?>
        </td>
        <?php if ($kaprodi_rps->num_rows() > 0) { ?>
          <td colspan="2" class="text-center" style="width: 27%;">
            Disetujui Oleh:<br><?= $kaprodi_rps->row()->jabatan; ?><br><br><br><br>
            (<?= $kaprodi_rps->row()->nm_koordinator; ?>)<br>Tanggal:
          </td>
        <?php } else { ?>
          <td colspan="2" class="text-center" style="width: 27%;">
            Disetujui Oleh:<br><br><br><br>
            (_________________________________)<br>Tanggal:
          </td>
        <?php } ?>
        <td class="text-center" style="width: 28%;">
          Dosen Pengampu:<br><br><br><br>
          <?= resolve_dosen_nama_lengkap($data_rps->dosen_pengampu, $data_rps->nm_ptk); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="padding: 6px 8px;">
          <strong>Program Studi:</strong><br><?= $nm_lemb; ?>
        </td>
        <td colspan="2" style="padding: 6px 8px;">
          <strong>Nama Matakuliah:</strong><br><?= $data_rps->nm_mk; ?>
        </td>
        <td style="width: 130px; padding: 6px 8px;">
          <strong>Kode:</strong><br><?= $data_rps->kode_mk; ?>
        </td>
        <td style="width: 140px; padding: 6px 8px; font-size: 11px; line-height: 1.35;">
          <strong>Bobot (SKS):</strong><br>
          SKS MK : <?= (int)($data_rps->sks_mk ?: ($data_rps->sks_tm + $data_rps->sks_prak)); ?><br>
          SKS TM: <?= (int)$data_rps->sks_tm; ?><br>
          SKS Prak: <?= (int)$data_rps->sks_prak; ?>
        </td>
      </tr>

      <!-- CAPAIAN PEMBELAJARAN LULUSAN -->
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="background-color: #f2f2f2; font-weight: bold; padding: 6px 8px;">
          Capaian Pembelajaran Lulusan yang Dibebankan Pada Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="6" style="padding: 8px 12px; font-size: 11px; line-height: 1.45;">
          <?= format_rps_content_list($data_rps->cpl); ?>
        </td>
      </tr>

      <!-- TUJUAN BELAJAR (CPMK) -->
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="background-color: #f2f2f2; font-weight: bold; padding: 6px 8px;">
          Tujuan Belajar (CPMK)
        </td>
      </tr>
      <tr>
        <td colspan="6" style="padding: 8px 12px; font-size: 11px; line-height: 1.45;">
          <?= format_rps_content_list($data_rps->tujuan_belajar); ?>
        </td>
      </tr>

      <!-- DESKRIPSI MATA KULIAH -->
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="background-color: #f2f2f2; font-weight: bold; padding: 6px 8px;">
          Deskripsi Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="6" style="padding: 8px 12px; font-size: 11px; line-height: 1.45;">
          <?= format_rps_content_list($data_rps->deskripsi_mata_kuliah, true); ?>
        </td>
      </tr>

      <!-- PRASYARAT MATA KULIAH -->
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="background-color: #f2f2f2; font-weight: bold; padding: 6px 8px;">
          Prasyarat Mata Kuliah
        </td>
      </tr>
      <tr>
        <td colspan="6" style="padding: 8px 12px; font-size: 11px; line-height: 1.45;">
          <?= format_rps_content_list($data_rps->prasyarat_mata_kuliah, true); ?>
        </td>
      </tr>

      <!-- DAFTAR REFERENSI -->
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="background-color: #f2f2f2; font-weight: bold; padding: 6px 8px;">
          Daftar Referensi
        </td>
      </tr>
      <tr style="page-break-inside: avoid;">
        <td colspan="6" style="padding: 8px 12px; font-size: 11px; line-height: 1.45;">
          <?= format_rps_content_list($data_rps->referensi); ?>
        </td>
      </tr>
    </tbody>
  </table>

</body>