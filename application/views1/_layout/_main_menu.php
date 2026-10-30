<?php
$group_tidak_tetap = array(15);
if ($this->ion_auth->in_group($group_tidak_tetap)) {
?>
  <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">


    <li class="<?= isset($parent_2) ? $parent_2 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->tahun_ajaran_aktif;; ?></span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_jadwal' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_jadwal'); ?>">
            <i class="icon-badge"></i>
            <span class="title">Jadwal Dosen</span>
          </a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_skema_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_skema_nilai'); ?>">
            <i class="icon-badge"></i>
            Skema Nilai</a>
        </li> -->

        <li class="<?= $menu_aktif == 'm_lapor_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_lapor_nilai'); ?>">
            <i class="icon-badge"></i>
            Publish Nilai</a>
        </li>

        <li class="<?= $menu_aktif == 'm_evaluasi_diri' ? 'active' : ''; ?>">
          <a href="<?= site_url('Evaluasi_diri'); ?>">
            <i class="icon-badge"></i>
            Evaluasi Diri</a>
        </li>
      </ul>
    </li>
    <?php
    if ($this->session->userdata('setting')->pengisian_tunda == 1) {
    ?>
      <li class="<?= isset($parent_tunda) ? $parent_tunda : ''; ?>">
        <a href="javascript:;">
          <i class="icon-diamond"></i>
          <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->id_smt_tunda; ?></span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_jadwal_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_jadwal_tunda'); ?>">
              <i class="icon-badge"></i>
              <span class="title">Dashboard</span>
            </a>
          </li>

          <!-- <li class="<?= $menu_aktif == 'm_skema_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_skema_nilai_tunda'); ?>">
              <i class="icon-badge"></i>
              Skema Nilai</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_isi_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_tunda'); ?>">
              <i class="icon-badge"></i>
              Nilai</a>
          </li>
          <!--  <li class="<?= $menu_aktif == 'm_lapor_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_lapor_nilai_tunda'); ?>">
              <i class="icon-badge"></i>
              Submit Nilai</a>
          </li> -->
        </ul>
      </li>
    <?php
    }
    ?>
    <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">RPS</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_rps_new' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_rps_new'); ?>">
            <i class="icon-badge"></i>
            RPS (NEW)</a>
        </li>
        <li class="<?= $menu_aktif == 'm_rps_tambah' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_rps_new/tambah'); ?>">
            <i class="icon-badge"></i>
            Tambah RPS</a>
        </li>
        <li class="<?= $menu_aktif == 'm_rps_lama' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_daftar_rps_lama'); ?>">
            <i class="icon-badge"></i>
            Daftar RPS Lama</a>
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
        <?php if($this->ion_auth->in_group(array(2))): ?>
        <li class="<?= $menu_aktif == 'm_setting_api_key' ? 'active' : ''; ?>">
          <a href="<?= site_url('Setting_api_key'); ?>"><i class="icon-key"></i> Setting API Key</a>
        </li>
        <?php endif; ?>
        <li class="<?= $menu_aktif == 'm_change_password' ? 'active' : ''; ?>">
          <a href="<?= site_url('Auth/change_password'); ?>"><i class="icon-badge"></i> Change Password</a>
        </li>
        <li class="">
          <a href="<?= site_url('Auth/logout'); ?>"><i class="icon-badge"></i> Logout</a>
        </li>
      </ul>
    </li>
  </ul>
<?php
} else {
?>
  <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">

    <?php
    $username_dc = $this->ion_auth->user()->row()->email;
    $akses_dc = $this->db->where('username', $username_dc)->get('siakad_akses_data_center')->num_rows() > 0;
    if ($akses_dc) {
    ?>
    <li class="<?= isset($parent_infografis) ? $parent_infografis : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Infografis WBI</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_infografis_profil_mahasiswa' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_profile_mahasiswa'); ?>">
            <i class="icon-badge"></i>
            Profil Mahasiswa 2025</a>
        </li>
        <li class="<?= $menu_aktif == 'm_infografis_lulusan' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_lulusan'); ?>">
            <i class="icon-badge"></i>
            Profil Lulusan WBI</a>
        </li>
        <li class="<?= $menu_aktif == 'm_academic_intelligence' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_academic_intelligence'); ?>">
            <i class="icon-badge"></i>
            Academic Intelligence</a>
        </li>
        <li class="<?= $menu_aktif == 'm_linimasa_ta' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_linimasa_ta'); ?>">
            <i class="icon-badge"></i>
            Linimasa TA</a>
        </li>
      </ul>
    </li>
    <?php
    }
    ?>

    <li class="<?= $menu_aktif == 'm_wall' ? 'active' : ''; ?>">
      <a href="<?= site_url('C_wall'); ?>">
        <i class="icon-feed"></i>
        <span class="title">Academic Wall</span>
      </a>
    </li>


    <li class="<?= isset($parent_2) ? $parent_2 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->tahun_ajaran_aktif;; ?></span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_jadwal' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_jadwal'); ?>">
            <i class="icon-badge"></i>
            <span class="title">Jadwal Dosen</span>
          </a>
        </li>
        <li class="<?= $menu_aktif == 'm_isi_nilai_mbkm' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_isi_nilai_excel_mbkm'); ?>">
            <i class="icon-badge"></i>
            Nilai MBKM/KKN/PKL</a>
        </li>

        <li class="<?= $menu_aktif == 'm_lapor_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_lapor_nilai'); ?>">
            <i class="icon-badge"></i>
            Publish Nilai</a>
        </li>

        <li class="<?= $menu_aktif == 'm_evaluasi_diri' ? 'active' : ''; ?>">
          <a href="<?= site_url('Evaluasi_diri'); ?>">
            <i class="icon-badge"></i>
            Evaluasi Diri</a>
        </li>


      </ul>
    </li>

    <?php
    if ($this->session->userdata('setting')->pengisian_tunda == 1) {
    ?>
      <li class="<?= isset($parent_tunda) ? $parent_tunda : ''; ?>">
        <a href="javascript:;">
          <i class="icon-diamond"></i>
          <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->id_smt_tunda; ?></span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_jadwal_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_jadwal_tunda'); ?>">
              <i class="icon-badge"></i>
              <span class="title">Dashboard</span>
            </a>
          </li>
          <li class="<?= $menu_aktif == 'm_rencana_pertemuan_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rencana_pertemuan_tunda'); ?>">
              <i class="icon-badge"></i>
              Berita Acara Perkuliahan</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kehadiran_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kehadiran_tunda'); ?>">
              <i class="icon-badge"></i>
              Kehadiran</a>
          </li>
          <!-- <li class="<?= $menu_aktif == 'm_kehadiran_2_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kehadiran_2_tunda'); ?>">
              <i class="icon-badge"></i>
              Kehadiran 2</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_skema_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_skema_nilai_tunda'); ?>">
              <i class="icon-badge"></i>
              Skema Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_isi_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_tunda'); ?>">
              <i class="icon-badge"></i>
              Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_isi_nilai_mbkm_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_mbkm_tunda'); ?>">
              <i class="icon-badge"></i>
              Nilai MBKM/KKN/PKL</a>
          </li>

          <li class="<?= $menu_aktif == 'm_lapor_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_lapor_nilai_tunda'); ?>">
              <i class="icon-badge"></i>
              Submit Nilai</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>

    <li class="<?= isset($parent_history) ? $parent_history : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">History</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">

        <li class="<?= $menu_aktif == 'm_history_ajar' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_history_ajar'); ?>">
            <i class="icon-badge"></i>
            History Ajar</a>
        </li>
        <li class="<?= $menu_aktif == 'm_history_ta' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_history_ta'); ?>">
            <i class="icon-badge"></i>
            History TA</a>
        </li>

      </ul>
    </li>

    <li class="<?= isset($parent_1) ? $parent_1 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Perwalian</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_persetujuan_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_persetujuan_krs'); ?>">
            <i class="icon-badge"></i>
            Approval KRS
            <span class="badge badge-danger" id="approval_krs_badge" <?= (!$this->session->flashdata('jumlah') ? 'style="display:none;"' : ''); ?>><?= $this->session->flashdata('jumlah'); ?></span>
          </a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_daftar_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_daftar_krs'); ?>">
            <i class="icon-badge"></i>
            Sedang Mendaftar KRS</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_belum_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_belum_krs'); ?>">
            <i class="icon-badge"></i>
            Belum KRS</a>
        </li>
        <li class="<?= $menu_aktif == 'm_lecturer_data_perwalian' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_data_perwalian'); ?>">
            <i class="icon-badge"></i>
            Keberhasilan Studi</a>
        </li>
        <li class="<?= $menu_aktif == 'm_konsultasi_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_konsultasi_nilai'); ?>">
            <i class="icon-badge"></i>
            Konsultasi Nilai</a>
        </li>
      </ul>
    </li>
    <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">RPS</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_rps_new' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_rps_new'); ?>">
            <i class="icon-badge"></i>
            RPS (NEW)</a>
        </li>
        <li class="<?= $menu_aktif == 'm_rps_tambah' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_rps_new/tambah'); ?>">
            <i class="icon-badge"></i>
            Tambah RPS</a>
        </li>
        <li class="<?= $menu_aktif == 'm_rps_lama' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_daftar_rps_lama'); ?>">
            <i class="icon-badge"></i>
            Daftar RPS Lama</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_rps_kelola' ? 'active' : ''; ?>">
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
        <li class="<?= $menu_aktif == 'm_nilai_berita_acara' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_nilai_berita_acara'); ?>">Nilai dan Berita Acara</a>
        </li>
        <li class="<?= $menu_aktif == 'm_notifikasi' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_notifikasi'); ?>">Notifikasi
            <span class="badge badge-danger"><?= cekNotifikasi($this->session->userdata('id_ptk')); ?></span></a>
        </li>
        <li class="<?= $menu_aktif == 'm_proposal' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_bimbingan'); ?>">Bimbingan TA
            <?php 
              $CI =& get_instance();
              $id_ptk_session = $CI->session->userdata('id_ptk');
              $pending_count = 0;
              if (!empty($id_ptk_session)) {
                  $now_time = time();
                  $last_check = $CI->session->userdata('bimbingan_last_check');
                  $cached_count = $CI->session->userdata('bimbingan_pending_count');
                  
                                    if ($last_check === NULL || ($now_time - $last_check) > 300 || $cached_count === NULL) {
                      $sql_pending = "
                          SELECT COUNT(DISTINCT l.id_judul_skripsi) AS total
                          FROM siakad_skripsi_bimbingan_log l
                          JOIN siakad_skripsi_pengajuan_judul a ON l.id_judul_skripsi = a.id_judul_skripsi
                          JOIN mahasiswa_pt m ON a.nipd = m.nipd
                          WHERE (a.dosen_pembimbing_1 = ? OR a.dosen_pembimbing_2 = ?)
                            AND m.id_jns_keluar IS NULL
                            AND l.catatan_mahasiswa IS NOT NULL
                            AND l.catatan_mahasiswa != ''
                            AND (
                                (a.dosen_pembimbing_1 = ? AND l.status_bimbingan = 'Menunggu Review' AND (l.status_pendamping IS NULL OR l.status_pendamping = '' OR l.status_pendamping = 'Lanjut ke Utama'))
                                OR
                                (a.dosen_pembimbing_2 = ? AND (l.status_pendamping = 'Konsultasi ke Pendamping' OR l.status_pendamping = 'Perlu Revisi'))
                            )
                            AND l.id_log = (
                                SELECT MAX(id_log) 
                                FROM siakad_skripsi_bimbingan_log
                                WHERE id_judul_skripsi = l.id_judul_skripsi
                            )
                      ";
                      $query_pending = $CI->db->query($sql_pending, array($id_ptk_session, $id_ptk_session, $id_ptk_session, $id_ptk_session));
                      $pending_count = $query_pending->row() ? (int)$query_pending->row()->total : 0;
                      
                                            $CI->session->set_userdata('bimbingan_pending_count', $pending_count);
                      $CI->session->set_userdata('bimbingan_last_check', $now_time);
                  } else {
                                            $pending_count = (int)$cached_count;
                  }
              }
            ?>
            <span class="badge badge-danger" id="bimbingan_pending_badge" <?= ($pending_count == 0 ? 'style="display:none;"' : ''); ?>><?= $pending_count; ?></span>
          </a>
        </li>
        <li class="<?= $menu_aktif == 'm_seminar_sidang' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_seminar_sidang'); ?>">Approval Seminar/Sidang
            <span class="badge badge-danger"><?= cekApproval($this->session->userdata('id_ptk')); ?></span></a>
        </li>
        <li class="<?= $menu_aktif == 'm_jadwal_seminar_sidang' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_penjadwalan'); ?>">Jadwal Seminar/Sidang</a>
        </li>
        <li class="<?= $menu_aktif == 'm_nilai_sempro' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_nilai_sempro'); ?>">Nilai SEMPRO</a>
        </li>
        <li class="<?= $menu_aktif == 'm_seminar_hasil' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_seminar_hasil'); ?>">Nilai SEMHAS</a>
        </li>
        <li class="<?= $menu_aktif == 'm_sidang' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_sidang'); ?>">Nilai Sidang</a>
        </li>
        <li class="<?= $menu_aktif == 'm_arsip_ta' ? 'active' : ''; ?>">
          <a href="<?= site_url('e_skripsi_arsip'); ?>">Arsip TA</a>
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
          <li class="<?= $menu_aktif == 'm_seminar_proposal' ? 'active' : ''; ?>">
            <a href="<?= site_url('e_skripsi_seminar_sidang_kaprodi/index/1'); ?>">Approval SEMPRO
              <span class="badge badge-danger"><?= cekApprovalKaprodi(1); ?></span>
            </a>
          </li>
          <li class="<?= $menu_aktif == 'm_seminar_hasil' ? 'active' : ''; ?>">
            <a href="<?= site_url('e_skripsi_seminar_sidang_kaprodi/index/2'); ?>">Approval SEMHAS
              <span class="badge badge-danger"><?= cekApprovalKaprodi(2); ?></span></a>
          </li>
          <li class="<?= $menu_aktif == 'm_sidang_tugas_akhir' ? 'active' : ''; ?>">
            <a href="<?= site_url('e_skripsi_seminar_sidang_kaprodi/index/3'); ?>">Approval Sidang
              <span class="badge badge-danger"><?= cekApprovalKaprodi(3); ?></span></a>
          </li>
          <li class="<?= $menu_aktif == 'm_rekapitulasi' ? 'active' : ''; ?>">
            <a href="<?= site_url('e_skripsi_rekapitulasi'); ?>">Kinerja Bimbingan TA</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>

 <li class="<?= isset($parent_kinerja) ? $parent_kinerja : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">E-KINERJA</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_kinerja' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_kinerja'); ?>">eKinerja Dosen</a>
        </li>
        <li class="<?= $menu_aktif == 'm_pendampingan' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pendampingan'); ?>">Pendampingan</a>
        </li>
        <?php if ($this->ion_auth->in_group(array(5))) : ?>
        <li class="<?= $menu_aktif == 'm_persetujuan_pendampingan' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_persetujuan_pendampingan'); ?>">Persetujuan Pendampingan</a>
        </li>
        <?php endif; ?>
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

        <li class="<?= $menu_aktif == 'm_summary_bimbingan_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_bimbingan_krs'); ?>">
            <i class="icon-badge"></i>
            Konsultasi KRS</a>
        </li>
      </ul>
    </li>

    <li class="<?= isset($parent_magang) ? $parent_magang : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Internship/Magang</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_laporan_magang' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_laporan_magang'); ?>">
            <i class="icon-badge"></i>
            Laporan Magang</a>
        </li>
        <?php if ($this->ion_auth->in_group(array(5))) : ?>
        <li class="<?= $menu_aktif == 'm_pantauan_pkl' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pantauan_pkl'); ?>">
            <i class="icon-badge"></i>
            Pantauan PKL</a>
        </li>
        <?php endif; ?>
      </ul>
    </li>
    <!--<li class="<?= isset($parent_3) ? $parent_3 : ''; ?>">
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
    </li>-->
    <?php
    $group = array(5);
    if ($this->ion_auth->in_group($group)) {
    ?>
      <li class="<?= isset($parent_kaprodi) ? $parent_kaprodi : ''; ?>">
        <a href="javascript:;">
          <i class="icon-diamond"></i>
          <span class="title">KAPRODI</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_monitoring' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_monitoring'); ?>">
              <i class="icon-badge"></i>
              Monitoring BAP</a>
          </li>
          <!-- <li class="<?= $menu_aktif == 'm_rps_all' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_all'); ?>">
              <i class="icon-badge"></i>
              Data RPS Prodi</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_rps_kaprodi' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_kaprodi'); ?>">
              <i class="icon-badge"></i>
              RPS PRODI</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kaprodi_evaluasi' ? 'active' : ''; ?>">
            <a href="<?= site_url('Kaprodi_evaluasi'); ?>">
              <i class="icon-badge"></i>
              Evaluasi Mahasiswa</a>
          </li>
          <li class="<?= $menu_aktif == 'm_informasi_statistik_kaprodi' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_informasi_statistik_kaprodi'); ?>">
              <i class="icon-badge"></i>
              Informasi dan Statistik</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kaprodi_belum_isi_nilai' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_belum_isi_nilai'); ?>">
              <i class="icon-badge"></i>
              Belum Lapor Nilai</a>
          </li>
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
          <!-- <li class="<?= $menu_aktif == 'm_kaprodi_bap' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_bap'); ?>">
              <i class="icon-badge"></i>
              BAP KAPRODI</a>
          </li> -->
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
          <li class="<?= $menu_aktif == 'm_konfirmasi_magang' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_konfirmasi_magang'); ?>">
              <i class="icon-badge"></i>
              Konfirmasi Magang</a>
          </li>
          <li class="<?= $menu_aktif == 'm_tracking_kegiatan' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_tracking_kegiatan'); ?>">
              <i class="icon-badge"></i>
              Laporan Mingguan</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>
    <?php
    $group = array(17);
    if ($this->ion_auth->in_group($group)) {
    ?>
      <li class="<?= isset($parent_lapkerma) ? $parent_lapkerma : ''; ?>">
        <a href="javascript:;">
          <i class="icon-diamond"></i>
          <span class="title">LAPKERMA</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_daftar_instansi' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_instansi'); ?>">
              <i class="icon-badge"></i>
              Instans/Mitra</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou'); ?>">
              <i class="icon-badge"></i>
              MOU Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou_expired' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou_expired'); ?>">
              <i class="icon-badge"></i>
              MOU Expired</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou_tidak_aktif' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou_tidak_aktif'); ?>">
              <i class="icon-badge"></i>
              MOU Tidak Aktif</a>
          </li>
          <li class="<?= $menu_aktif == 'm_moa' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa'); ?>">
              <i class="icon-badge"></i>
              MOA Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_moa_expired' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa_expired'); ?>">
              <i class="icon-badge"></i>
              MOA Expired</a>
          </li>

          <li class="<?= $menu_aktif == 'm_moa_tidak_aktif' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa_tidak_aktif'); ?>">
              <i class="icon-badge"></i>
              MOA Tidak Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_download' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_lap_jumlah_kerjasama'); ?>">
              <i class="icon-badge"></i>
              Download Data</a>
          </li>

          <li class="<?= $menu_aktif == 'm_kegiatan' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_kegiatan'); ?>">
              <i class="icon-badge"></i>
              Laporan Kegiatan</a>
          </li>

        </ul>
      </li>
    <?php
    }
    ?>

    <?php
    $group = array(19);
    if ($this->ion_auth->in_group($group)) {
    ?>
      <li class="<?= isset($parent_ami) ? $parent_ami : ''; ?>">
        <a href="javascript:;">
          <i class="icon-diamond"></i>
          <span class="title">Audit Mutu</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_audit_mutu' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_audit_mutu'); ?>">
              <i class="icon-badge"></i>
              Pelaksanaan AMI</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>

    <?php
    if ($akses_dc) {
    ?>
    <li class="<?= isset($parent_data_center) ? $parent_data_center : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Pusat Data</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_revisi_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_revisi_nilai'); ?>">
            <i class="icon-badge"></i>
            Memo Revisi Nilai</a>
        </li>
        <li class="<?= $menu_aktif == 'm_pusat_data_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_nilai'); ?>">
            <i class="icon-badge"></i>
            Data Nilai</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_bap' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_bap'); ?>">
            <i class="icon-badge"></i>
            Data BAP</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_pusat_data_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_epom'); ?>">
            <i class="icon-badge"></i>
            Data EPOM</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_analisa_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_analisa_epom'); ?>">
            <i class="icon-badge"></i>
            Analisa EPOM</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_pusat_data_epom_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_epom_dosen'); ?>">
            <i class="icon-badge"></i>
            EPOM Dosen</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_rps' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_rps'); ?>">
            <i class="icon-badge"></i>
            Data RPS</a>
        </li> -->
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_profil' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_profil'); ?>">
            <i class="icon-badge"></i>
            Profil Mahasiswa</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_eoffice_handling_time' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_eoffice_handling_time'); ?>">
            <i class="icon-badge"></i>
            Eoffice Monev</a>
        </li>
      </ul>
    </li>
    <?php
    }
    ?>
    <li class="<?= isset($parent_4) ? $parent_4 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-diamond"></i>
        <span class="title">Setting</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <?php if($this->ion_auth->in_group(array(2))): ?>
        <li class="<?= $menu_aktif == 'm_setting_api_key' ? 'active' : ''; ?>">
          <a href="<?= site_url('Setting_api_key'); ?>"><i class="icon-key"></i> Setting API Key</a>
        </li>
        <?php endif; ?>
        <li class="<?= $menu_aktif == 'm_change_password' ? 'active' : ''; ?>">
          <a href="<?= site_url('Auth/change_password'); ?>"><i class="icon-badge"></i> Change Password</a>
        </li>
        <li class="">
          <a href="<?= site_url('Auth/logout'); ?>"><i class="icon-badge"></i> Logout</a>
        </li>
      </ul>
    </li>
  </ul>
<?php
}
?>