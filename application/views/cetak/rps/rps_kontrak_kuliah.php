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
        <td colspan="6" class="text-center" style="vertical-align: middle;" colspan=4>
          <h4 style="font-size: 14px;margin:0 0 5px !important; font-weight: bold;">POLITEKNIK WILMAR BISNIS INDONESIA</h4>
          <p style="font-size: 12px;margin:0 0 5px !important">Jl. Kapten Batu Sihombing, Desa Medan Estate</p>
          <p style="font-size: 12px;margin:0 0 5px !important">Deli Serdang, Sumatera Utara</p>
        </td>

      </tr>
      <tr>
        <td class="text-center" colspan="6">
          <h2><strong>KONTRAK PEMBELAJARAN</strong></h2>
        </td>

      </tr>
      <tr>
        <td style="width: 130px;" class="text-center" rowspan="3">Jenis Dokumen: <br><br>
          <p style="font-size: 18px;font-weight:bold">FORMULIR SPMI</p>
        </td>
        <td colspan="2" class="text-center" style="height: 20px;">Pihak I</td>
        <td colspan="1" class="text-center">Pihak II</td>
        <td colspan="2" class="text-center">Disetujui</td>
      </tr>
      <tr>
        <td colspan="2" class="text-center">Dosen Pengampu<br><br><br>
          <p style="display: block !important;">(<?= resolve_dosen_nama_lengkap($data_rps->dosen_pengampu, $data_rps->nm_ptk ?? ''); ?>)</p>
        </td>
        <td colspan="1" class="text-center"></td>
        <td colspan="2" class="text-center">Ketua Program Studi<br><br><br>(<?= $kaprodi_rps->row()->nm_koordinator; ?>)</td>
      </tr>
      <tr>
        <td colspan="2" style="height: 20px;">Tanggal :</td>
        <td colspan="1">Tanggal :</td>
        <td colspan="2">Tanggal :</td>
      </tr>
      <tr>
        <td colspan="2">
          Program Studi:<br><?= $nm_lemb; ?>
        </td>

        <td colspan="2">
          Nama Matakuliah:<br><?= $data_rps->nm_mk; ?>
        </td>
        <td colspan="2">
          Kode:<br><?= $data_rps->kode_mk; ?>
        </td>


      </tr>
      <tr>
        <td colspan="2">
          Semester:<br><?= $data_rps->semester; ?>
        </td>
        <td colspan="2">
          Bobot (SKS):<br>
          SKS MK : <?= (int)($data_rps->sks_mk ?: ($data_rps->sks_tm + $data_rps->sks_prak)); ?><br>
          SKS TM: <?= (int)$data_rps->sks_tm; ?><br>
          SKS Prak: <?= (int)$data_rps->sks_prak; ?>
        </td>
        <td colspan="2">Dosen Pengampu:<br><?= resolve_dosen_nama_lengkap($data_rps->dosen_pengampu, $data_rps->nm_ptk ?? ''); ?></td>

      </tr>
    </tbody>
  </table>
  <table class="table table-black2" style="margin-bottom: 3px !important;">
    <tbody>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>1. DESKRIPSI MATA KULIAH</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->deskripsi_mata_kuliah)) {
          echo '<td colspan="5">' . clean_style($data_rps->deskripsi_mata_kuliah) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Yang dimaksud dengan deskripsi mata kuliah adalah pernyataan yang mengandung ruang materi dan garis-garis besar atau pokok bahasan yang terdapat dalam mata kuliah tersebut.</p></td>';
        }
        ?>

      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>2. MANFAAT MATA KULIAH</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->manfaat_mata_kuliah)) {
          echo '<td colspan="5">' . clean_style($data_rps->manfaat_mata_kuliah) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini menjelaskan mengapa mahasiswa perlu mengambil mata kuliah ini, apa kegunaan mata kuliah ini untuk pengembangan pribadi, bagaimana hubungan mata kuliah ini dengan tujuan Program Studi.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>3. CP MATA KULIAH</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->cp_mata_kuliah)) {
          echo '<td colspan="5">' . clean_style($data_rps->cp_mata_kuliah) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini menjelaskan tentang hasil belajar yang akan dicapai pada akhir perkuliahan. Mahasiswa perlu tahu CP apa yang akan dapat dia ketahui dan lakukan setelah mengambil suatu mata kuliah, apakah tujuan mata kuliah berupa kemampuan pemecahan masalah, meningkatkan kemampuan berkomunikasi atau merancang suatu program. Perumusan CP mata kuliah secara jelas akan membantu mahasiswa dan dosen untuk mengevaluasi sejauh mana tujuan tersebut telah tercapai. Perumusan CP dilakukan dalam bentuk kompetensi untuk berbagai level menurut taksonomi Bloom atau taksonomi lain.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>4. ANALISIS INSTRUKSIONAL/ORGANISASI MATERI</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->analisis_instruksional)) {
          echo '<td colspan="5">' . clean_style($data_rps->analisis_instruksional) . '<br>';
          if (!empty($data_rps->image_organisasi)) {
            echo '<br>';
            echo '<img src="' . SOURCEPATH_RPS . $data_rps->image_organisasi . '" />';
          }
          echo '</td>';
        } else {
          echo '<td colspan="5"></td>';
        }


        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>5. STRATEGI PERKULIAHAN</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->strategi_perkuliahan)) {
          echo '<td colspan="5">' . clean_style($data_rps->strategi_perkuliahan) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini menjelaskan prosedur kerja atau kegiatan yang akan dilakukan dalam perkuliahan. Kombinasi berbagai metode belajar mengajar dapat digunakan untuk mencapai hasil belajar yang maksimal. Sebagai contoh metode ceramah singkat dikombinasikan dengan metode diskusi. Ceramah singkat dimaksudkan untuk menyampaikan atau memberi kerangka berpikir, sedangkan diskusi digunakan untuk melibatkan mahasiswa secara aktif dalam proses belajar dan mencapai kesepakatan atau kesimpulan kelompok.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>6. MATERI/BAHAN BACAAN PERKULIAHAN</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->materi_bahan_bacaan)) {
          echo '<td colspan="5">' . clean_style($data_rps->materi_bahan_bacaan) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Pada awal perkuliahan mahasiswa perlu mengetahui buku teks, artikel atau bahan bacaan lain yang menjadi bahan acuan dan bacaan wajib. Di samping itu untuk tugas-tugas individual, seperti menulis paper dan kritik, mahasiswa juga dianjurkan untuk menggunakan sumber belajar lain yang relevan.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>7. TUGAS-TUGAS</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->tugas_tugas)) {
          echo '<td colspan="5">' . clean_style($data_rps->tugas_tugas) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Diisi dengan kesepakatan tentang pengumpulan tugas yang sudah ada di Rancangan Tugas.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>8. KRITERIA PENILAIAN</b></td>
      </tr>
      <tr>
        <td></td>
        <?php
        if (!empty($data_rps->kontrak_kriteria_penilaian)) {

                    echo '<td colspan="5">' . clean_style($data_rps->kontrak_kriteria_penilaian)  . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini menjelaskan tentang kriteria penilaian prestasi belajar mahasiswa, untuk menunjukkan dia dianggap berhasil atau tidak berhasil. Di samping itu, kalau perlu dijelaskan pula tentang pembobotan tugas, evaluasi dan cara penilaian lainnya.</p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>9. JADWAL PERKULIAHAN</b></td>
      </tr>
      <tr>
        <td></td>

        <?php
        if (!empty($data_rps->jadwal_perkuliahan)) {
                    echo '<td colspan="5">' . clean_style($data_rps->jadwal_perkuliahan) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini memuat jadwal perkuliahan berupa tanggal, topik yang akan dibahas, dan bahan bacaan yang relevan dengan setiap pokok bahasan. </p></td>';
        }
        ?>
      </tr>
      <tr>
        <td colspan="6" style="background-color:rgb(229, 229, 229);"><b>10. TATA KELOLA PERKULIAHAN</b></td>
      </tr>
      <tr>
        <td></td>

        <?php
        if (!empty($data_rps->tata_kelola_perkuliahan)) {
          echo '<td colspan="5">' . clean_style($data_rps->tata_kelola_perkuliahan) . '</td>';
        } else {
          echo '<td colspan="5"><p>Data belum diisi</p><p>Penjelasan : Bagian ini memuat kesepakatan tentang pelaksanaan perkuliahan, pengumpulan tugas terstruktur, pelaksanaan diskusi, aturan kedisiplinan atau hal-hal lain yang perlu disepakati dengan mahasiswa.</p></td>';
        }
        ?>
      </tr>



    </tbody>
  </table>

</body>