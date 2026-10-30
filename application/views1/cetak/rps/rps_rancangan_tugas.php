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
        <td colspan="5" class="text-center" style="vertical-align: middle;" colspan=4>
          <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px;margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>

      </tr>
      <tr>
        <td class="text-center" colspan="6">
          <h2><strong>RANCANGAN TUGAS</strong></h2>
        </td>

      </tr>
      <tr>
        <td style="width: 150px;" class="text-center">Jenis Dokumen: <br><br>
          <p style="font-size: 20px;font-weight:bold">FORMULIR SPMI</p>
        </td>
        <td colspan="3" class="text-center">Disusun Oleh:<br>Kordinator <br><br><br><br>(<?= $koordinator_rps->nm_ptk ?>)<br>Tanggal: <?= $tanggal_usulan; ?></td>

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


      </tr>
      <tr>
        <td colspan="2">
          Program Studi:<br><?= $nm_lemb; ?>
        </td>

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
      <tr>
        <td colspan="2">Dosen Pengampu:<br><?= $data_rps->dosen_pengampu; ?></td>
        <td colspan="2">Minggu Ke:<br><?= $data_rps->tugas_minggu_ke; ?></td>
        <td colspan="2">Tugas Ke:<br><?= $data_rps->tugas_ke; ?></td>
      </tr>
      <tr>
        <td class="text-center" style="border-bottom:none"><b>1</b></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>TUJUAN TUGAS</b></td>
      </tr>
      <tr>
        <td style="border-top:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tujuan_tugas); ?></td>
      </tr>

      <tr>
        <td class="text-center" style="border-bottom:none"><b>2</b></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>URAIAN TUGAS</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>a. Obyek Garapan</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_obyek_garapan); ?></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>b. Yang harus dikerjakan dan batasan-batasan</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_batasan); ?></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>c. Metode/Cara Pengerjaan, acuan yang digunakan</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_metode); ?></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>d. Deskripsi luaran tugas yang dihasilkan/dikerjakan</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_luaran); ?></td>
      </tr>


      <tr>
        <td class="text-center" style="border-bottom:none"><b>3</b></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>KRITERIA PENILAIAN</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>a. Kerapian</b></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_penilaian_kerapian); ?></td>
      </tr>
      <tr>
        <td style="border-top:none;border-bottom:none"></td>
        <td colspan="5" style="background-color:rgb(229, 229, 229);"><b>b. Ketepatan/Kebenaran</b></td>
      </tr>
      <tr>
        <td style="border-top:none;"></td>
        <td colspan="5" style="padding-left:30px"><?= clean_style($data_rps->tugas_ketepatan); ?></td>
      </tr>

    </tbody>
  </table>

</body>