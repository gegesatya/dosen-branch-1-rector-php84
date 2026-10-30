<ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
  <li class="<?= isset($parent_2) ? $parent_2 : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Perkuliahan</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_jadwal' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_jadwal'); ?>">
          <i class="icon-badge"></i>
          <span class="title">Jadwal</span>
        </a>
      </li>
      <li class="<?= $menu_aktif == 'm_skema_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_skema_nilai'); ?>">
          <i class="icon-badge"></i>
          Skema Nilai</a>
      </li>
      <li class="<?= $menu_aktif == 'm_rencana_pertemuan' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rencana_pertemuan'); ?>">
          <i class="icon-badge"></i>
          Berita Acara Perkuliahan</a>
      </li>

      <li class="<?= $menu_aktif == 'm_kehadiran' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kehadiran'); ?>">
          <i class="icon-badge"></i>
          Presensi 1</a>
      </li>
      <li class="<?= $menu_aktif == 'm_kehadiran_2' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kehadiran_2'); ?>">
          <i class="icon-badge"></i>
          Presensi 2</a>
      </li>

      <li class="<?= $menu_aktif == 'm_isi_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_isi_nilai_excel'); ?>">
          <i class="icon-badge"></i>
          Nilai</a>
      </li>

      <li class="<?= $menu_aktif == 'm_lapor_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_lapor_nilai'); ?>">
          <i class="icon-badge"></i>
          Lapor Sudah Isi Nilai</a>
      </li>
    </ul>
  </li>
  <li class="<?= isset($parent_1) ? $parent_1 : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Bimbingan KRS</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_persetujuan_krs' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_persetujuan_krs'); ?>">
          <i class="icon-badge"></i>
          Approval KRS
          <?php if ($this->session->flashdata('jumlah')) { ?>
            <span class="badge badge-danger"><?= $this->session->flashdata('jumlah'); ?></span>
          <?php } ?>
        </a>
      </li>
      <li class="<?= $menu_aktif == 'm_daftar_krs' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_daftar_krs'); ?>">
          <i class="icon-badge"></i>
          Sedang Mendaftar KRS</a>
      </li>
      <li class="<?= $menu_aktif == 'm_belum_krs' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_belum_krs'); ?>">
          <i class="icon-badge"></i>
          Belum KRS</a>
      </li>
      <!-- <li class="<?= $menu_aktif == 'm_perbaikan_krs' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_perbaikan_krs'); ?>">
          <i class="icon-badge"></i>
          Perbaikan KRS</a>
      </li> -->
      <li class="<?= $menu_aktif == 'm_lecturer_data_perwalian' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_data_perwalian'); ?>">
          <i class="icon-badge"></i>
          Perwalian</a>
      </li>
      <!-- <li class="<?= $menu_aktif == 'm_lecturer_konsultasi_akademik' ? 'active' : ''; ?>">
        <a href="<?= site_url('Lecturer_konsultasi_akademik'); ?>">
          <i class="icon-badge"></i>
          Konsultasi Akademik</a>
      </li> -->
    </ul>
  </li>

  <li class="<?= isset($parent_skripsi) ? $parent_skripsi : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">E-TUGAS AKHIR</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_notifikasi' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_notifikasi'); ?>">Notifikasi</a>
      </li>
      <li class="<?= $menu_aktif == 'm_proposal' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_bimbingan'); ?>">Bimbingan</a>
      </li>
      <li class="<?= $menu_aktif == 'm_seminar_sidang' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_seminar_sidang'); ?>">Persetujuan Seminar/Sidang</a>
      </li>
      <li class="<?= $menu_aktif == 'm_jadwal_seminar_sidang' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_penjadwalan'); ?>">Jadwal Seminar/Sidang</a>
      </li>
      <li class="<?= $menu_aktif == 'm_nilai_sempro' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_nilai_sempro'); ?>">Seminar Proposal</a>
      </li>
      <li class="<?= $menu_aktif == 'm_seminar_hasil' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_seminar_hasil'); ?>">Seminar Hasil</a>
      </li>
      <li class="<?= $menu_aktif == 'm_sidang' ? 'active' : ''; ?>">
        <a href="<?= site_url('e_skripsi_sidang'); ?>">Sidang TA</a>
      </li>
    </ul>
  </li>
  <?php
  $group = array(5);
  if ($this->ion_auth->in_group($group)) {
  ?>
    <li class="<?= isset($parent_admin_ta) ? $parent_admin_ta : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Admin Tugas Akhir</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">

        <li class="<?= $menu_aktif == 'm_notifikasi_prodi' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_notifikasi_prodi'); ?>">Notifikasi Prodi</a>
        </li>
        <li class="<?= $menu_aktif == 'm_daftar_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_daftar_dosen'); ?>">Daftar Dosen</a>
        </li>
        <li class="<?= $menu_aktif == 'm_daftar_mahasiswa' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_daftar_mahasiswa'); ?>">Evaluasi Mahasiswa</a>
        </li>
        <li class="<?= $menu_aktif == 'm_daftar_penetapan_judul' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_daftar_penetapan_judul'); ?>">Penetapan Judul
            <span class="badge badge-danger"><?= cekUnreadLog($this->session->userdata('prodi')); ?></span></a>
        </li>
        <!--  <li class="<?= $menu_aktif == 'm_sempro' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_sempro'); ?>"><i class="icon-graduation "></i> Penguji Sempro</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_seminar_sidang_kaprodi' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_seminar_sidang_kaprodi'); ?>">Persetujuan Seminar/Sidang Kaprodi</a>
        </li>


      </ul>
    </li>
  <?php
  }
  ?>

  <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">RPS Online</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_rps_online' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rps_online'); ?>">
          <i class="icon-badge"></i>
          RPS Saya</a>
      </li>
      <li class="<?= $menu_aktif == 'm_rps_tambah' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rps_online/tambah'); ?>">
          <i class="icon-badge"></i>
          Tambah RPS</a>
      </li>
      <li class="<?= $menu_aktif == 'm_rps_kelola' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rps_kelola'); ?>">
          <i class="icon-badge"></i>
          MK PRODI</a>
      </li>
      <li class="<?= $menu_aktif == 'm_rps_wbi_list' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rps_wbi_list'); ?>">
          <i class="icon-badge"></i>
          List RPS WBI</a>
      </li>
      <li class="<?= $menu_aktif == 'm_rps_kelas_kuliah' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_rps_kelas_kuliah'); ?>">
          <i class="icon-badge"></i>
          Kelas Perkuliahan</a>
      </li>
    </ul>
  </li>
  <li class="<?= isset($parent_summary) ? $parent_summary : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Rekap Semester</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_summary_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_summary_nilai'); ?>">
          <i class="icon-badge"></i>
          Nilai</a>
      </li>
      <li class="<?= $menu_aktif == 'm_summary_bap' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_summary_bap'); ?>">
          <i class="icon-badge"></i>
          BAP</a>
      </li>
      <li class="<?= $menu_aktif == 'm_summary_kehadiran' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_summary_kehadiran'); ?>">
          <i class="icon-badge"></i>
          Kehadiran</a>
      </li>
      <li class="<?= $menu_aktif == 'm_summary_epom' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_summary_epom'); ?>">
          <i class="icon-badge"></i>
          EPOM</a>
      </li>
      <li class="<?= $menu_aktif == 'm_summary_kritik_saran' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_summary_kritik_saran'); ?>">
          <i class="icon-badge"></i>
          Saran/Kritik Mahasiswa</a>
      </li>
    </ul>
  </li>
  <li class="<?= isset($parent_3) ? $parent_3 : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Arsip (Data < TA 2019)</span>
          <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_arsip_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('c_arsip_nilai'); ?>">
          <i class="icon-badge"></i>
          Nilai</a>
      </li>
      <li class="<?= $menu_aktif == 'm_arsip_bap' ? 'active' : ''; ?>">
        <a href="<?= site_url('c_arsip_bap'); ?>">
          <i class="icon-badge"></i>
          BAP</a>
      </li>
      <li class="<?= $menu_aktif == 'm_arsip_kehadiran' ? 'active' : ''; ?>">
        <a href="<?= site_url('c_arsip_kehadiran'); ?>">
          <i class="icon-badge"></i>
          Kehadiran</a>
      </li>
      <li class="<?= $menu_aktif == 'm_arsip_epom' ? 'active' : ''; ?>">
        <a href="<?= site_url('c_arsip_epom'); ?>">
          <i class="icon-badge"></i>
          EPOM</a>
      </li>
      <li class="<?= $menu_aktif == 'm_arsip_kritik_saran' ? 'active' : ''; ?>">
        <a href="<?= site_url('c_arsip_kritik_saran'); ?>">
          <i class="icon-badge"></i>
          Saran/Kritik Mahasiswa</a>
      </li>
    </ul>
  </li>

  <li class="<?= isset($parent_kaprodi) ? $parent_kaprodi : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">KAPRODI</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_sebaran_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_sebaran_nilai'); ?>">
          <i class="icon-badge"></i>
          Sebaran Nilai</a>
      </li>
      <li class="<?= $menu_aktif == 'm_krs' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_data_krs_mahasiswa'); ?>">
          <i class="icon-badge"></i>
          KRS</a>
      </li>
      <li class="<?= $menu_aktif == 'm_krs_baru' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_data_krs_mahasiswa_baru'); ?>">
          <i class="icon-badge"></i>
          KRS Mahasiswa Baru</a>
      </li>
      <li class="<?= $menu_aktif == 'm_kaprodi_data_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_data_nilai'); ?>">
          <i class="icon-badge"></i>
          Nilai Mahasiswa</a>
      </li>
      <li class="<?= $menu_aktif == 'm_kaprodi_bap' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_bap'); ?>">
          <i class="icon-badge"></i>
          BAP KAPRODI</a>
      </li>
      <li class="<?= $menu_aktif == 'm_kaprodi_epom' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_kaprodi_epom'); ?>">
          <i class="icon-badge"></i>
          EPOM KAPRODI</a>
      </li>
      <li class="<?= $menu_aktif == 'm_konfirmasi' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_eoffice_konfirmasi'); ?>">
          <i class="icon-badge"></i>
          Permohonan</a>
      </li>
    </ul>
  </li>

  <li class="<?= isset($parent_data_center) ? $parent_data_center : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Pusat Data</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_informasi_statistik' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_informasi_statistik'); ?>">
          <i class="icon-badge"></i>
          Informasi dan Statistik</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_nilai' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_nilai'); ?>">
          <i class="icon-badge"></i>
          Data Nilai</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_bap' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_bap'); ?>">
          <i class="icon-badge"></i>
          Data BAP</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_epom' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_epom'); ?>">
          <i class="icon-badge"></i>
          Data EPOM</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_analisa_epom' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_analisa_epom'); ?>">
          <i class="icon-badge"></i>
          Analisa EPOM</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_epom_dosen' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_epom_dosen'); ?>">
          <i class="icon-badge"></i>
          EPOM Dosen</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_rps' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_rps'); ?>">
          <i class="icon-badge"></i>
          Data RPS</a>
      </li>
      <li class="<?= $menu_aktif == 'm_pusat_data_profil' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_pusat_data_profil'); ?>">
          <i class="icon-badge"></i>
          Profil Mahasiswa</a>
      </li>
      <li class="<?= $menu_aktif == 'm_eoffice_handling_time' ? 'active' : ''; ?>">
        <a href="<?= site_url('C_eoffice_handling_time'); ?>">
          <i class="icon-badge"></i>
          Eoffice Monev</a>
      </li>
    </ul>
  </li>
  <li class="<?= isset($parent_4) ? $parent_4 : ''; ?>">
    <a href="javascript:;">
      <i class="icon-diamond"></i>
      <span class="title">Setting</span>
      <span class="arrow "></span>
    </a>
    <ul class="sub-menu">
      <li class="<?= $menu_aktif == 'm_change_password' ? 'active' : ''; ?>">
        <a href="<?= site_url('Auth/change_password'); ?>"><i class="icon-badge"></i> Change Password</a>
      </li>
      <li class="">
        <a href="<?= site_url('Auth/logout'); ?>"><i class="icon-badge"></i> Logout</a>
      </li>
    </ul>
  </li>
</ul>