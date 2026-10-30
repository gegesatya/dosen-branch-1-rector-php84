<?php
$group_tidak_tetap = array(15);
$user_login_obj = $this->ion_auth->user()->row();
$user_login_email = $user_login_obj->email ?? '';
$user_login_uname = $user_login_obj->username ?? '';
$user_id_ptk = $this->session->userdata('id_ptk') ?? ($user_login_obj->id_ptk ?? '');

$CI_inst =& get_instance();
if (!isset($CI_inst->Maintenance_model)) {
    $CI_inst->load->model('Maintenance_model');
}
$is_gege_satya = $CI_inst->Maintenance_model->is_user_exempt();
$can_access_kurikulum = (!$this->ion_auth->in_group(15) || $is_gege_satya);

$is_maint_kuri = $CI_inst->Maintenance_model->is_maintenance('kurikulum');
$is_maint_rps  = $CI_inst->Maintenance_model->is_maintenance('studio_rps');
$rps_menu_mode = $CI_inst->Maintenance_model->get_rps_menu_mode();


// 1. Get siakad_dosen mapping (to resolve id_ptk_feeder for SISTER)
$sd = null;
if (!empty($user_id_ptk)) {
    $sd = $this->db->where('id_ptk', $user_id_ptk)->get('siakad_dosen')->row();
}
if (!$sd && !empty($user_login_email)) {
    $sd = $this->db->where('email', $user_login_email)->get('siakad_dosen')->row();
}
if (!$sd && !empty($user_login_uname)) {
    $sd = $this->db->where('username', $user_login_uname)->get('siakad_dosen')->row();
}

$id_ptk_feeder = $sd->id_ptk_feeder ?? '';

// 2. Fetch lecturer profile & photo from SISTER
$sister_dosen = null;
if (!empty($id_ptk_feeder)) {
    $sister_dosen = $this->db->where('id_sdm', $id_ptk_feeder)->get('sister_profile_dosen')->row();
}
if (!$sister_dosen && !empty($user_id_ptk)) {
    $sister_dosen = $this->db->where('id_sdm', $user_id_ptk)->get('sister_profile_dosen')->row();
}
if (!$sister_dosen && !empty($user_login_email)) {
    $sister_dosen = $this->db->where('email', $user_login_email)->get('sister_profile_dosen')->row();
}
if (!$sister_dosen && !empty($user_login_uname)) {
    $sister_dosen = $this->db->where('email', $user_login_uname)->get('sister_profile_dosen')->row();
}

$avatar_url = base_url('assets/theme/assets/admin/layout/img/avatar.png');
if ($sister_dosen && !empty($sister_dosen->photo) && file_exists(FCPATH . $sister_dosen->photo)) {
    $avatar_url = base_url($sister_dosen->photo);
} elseif ($sd && !empty($sd->photo) && file_exists(FCPATH . 'assets/uploads/foto_dosen/' . $sd->photo . '.jpg')) {
    $avatar_url = base_url('assets/uploads/foto_dosen/' . $sd->photo . '.jpg');
}

// 3. Name formatting
$user_full_name = '';
if ($sister_dosen && !empty($sister_dosen->nm_ptk)) {
    $user_full_name = ucwords(strtolower((string)$sister_dosen->nm_ptk));
} elseif ($sd && !empty($sd->nm_ptk)) {
    $user_full_name = ucwords(strtolower((string)$sd->nm_ptk));
} else {
    $user_full_name = trim((@$user_login_obj->first_name ?? '') . ' ' . (@$user_login_obj->last_name ?? ''));
    if (empty($user_full_name)) $user_full_name = $user_login_obj->username ?? 'Dosen';
}

// 3. Role label
$user_role_label = 'Dosen';
if ($this->ion_auth->in_group(array(5))) {
    $user_role_label = 'Dosen &bull; Kaprodi';
} elseif ($this->ion_auth->in_group(array(1))) {
    $user_role_label = 'Administrator';
}

$is_pimpinan_sister = ($this->db->group_start()
    ->where('username', $user_login_email)
    ->or_where('username', $user_login_uname)
    ->group_end()
    ->where_in('roles', array('direktur', 'wadir2'))
    ->get('siakad_akses_data_center')->num_rows() > 0);

if ($this->ion_auth->in_group($group_tidak_tetap)) {
?>
  <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <!-- User Profile Card in Sidebar -->
    <li class="sidebar-profile-item" style="list-style: none; margin: 0; padding: 0;">
      <div class="sidebar-profile-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin: 4px 10px 14px 10px; background: rgba(255, 255, 255, 0.1); border-radius: 12px; overflow: hidden;">
        <img src="<?= $avatar_url; ?>" alt="Avatar" class="profile-avatar" width="44" height="44" style="width: 44px !important; height: 44px !important; min-width: 44px !important; max-width: 44px !important; min-height: 44px !important; max-height: 44px !important; border-radius: 50% !important; object-fit: cover !important; display: block !important; flex-shrink: 0 !important; border: 2px solid rgba(255, 255, 255, 0.85);" />
        <div class="profile-info" style="overflow: hidden; display: flex; flex-direction: column;">
          <div class="profile-name" style="color: #ffffff; font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= html_escape($user_full_name); ?>"><?= html_escape($user_full_name); ?></div>
          <div class="profile-role" style="color: #ccfbf1; font-size: 11px; font-weight: 600; margin-top: 2px; display: flex; align-items: center; gap: 4px;"><i class="fa fa-circle" style="font-size: 7px; color: #34d399;"></i> <?= $user_role_label; ?></div>
        </div>
      </div>
    </li>

    <li class="sidebar-heading-modern">
      <i class="fa fa-graduation-cap"></i> MENU DOSEN
    </li>


    <li class="<?= isset($parent_2) ? $parent_2 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-screen-desktop"></i>
        <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->tahun_ajaran_aktif;; ?></span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_jadwal' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_jadwal'); ?>">
            <i class="icon-calendar"></i>
            <span class="title">Jadwal Dosen</span>
          </a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_skema_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_skema_nilai'); ?>">
            <i class="icon-list"></i>
            Skema Nilai</a>
        </li> -->

        <li class="<?= $menu_aktif == 'm_lapor_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_lapor_nilai'); ?>">
            <i class="icon-cloud-upload"></i>
            Publish Nilai</a>
        </li>

        <li class="<?= $menu_aktif == 'm_evaluasi_diri' ? 'active' : ''; ?>">
          <a href="<?= site_url('Evaluasi_diri'); ?>">
            <i class="icon-user-following"></i>
            Evaluasi Diri</a>
        </li>
      </ul>
    </li>
    <?php
    if ($this->session->userdata('setting')->pengisian_tunda == 1) {
    ?>
      <li class="<?= isset($parent_tunda) ? $parent_tunda : ''; ?>">
        <a href="javascript:;">
          <i class="icon-screen-desktop"></i>
          <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->id_smt_tunda; ?></span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_jadwal_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_jadwal_tunda'); ?>">
              <i class="icon-speedometer"></i>
              <span class="title">Dashboard</span>
            </a>
          </li>

          <!-- <li class="<?= $menu_aktif == 'm_skema_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_skema_nilai_tunda'); ?>">
              <i class="icon-list"></i>
              Skema Nilai</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_isi_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_tunda'); ?>">
              <i class="icon-check"></i>
              Nilai</a>
          </li>
          <!--  <li class="<?= $menu_aktif == 'm_lapor_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_lapor_nilai_tunda'); ?>">
              <i class="icon-cloud-upload"></i>
              Submit Nilai</a>
          </li> -->
        </ul>
      </li>
    <?php
    }
    ?>
    <?php if ($rps_menu_mode === 'LEGACY'): ?>
      <!-- MENU RPS LAMA (STANDAR PRODUCTION) -->
      <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
        <a href="javascript:;">
          <i class="icon-book-open"></i>
          <span class="title">RPS</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_rps_new' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_new'); ?>">
              <i class="icon-docs"></i>
              Daftar RPS</a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_tambah' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_new/tambah'); ?>">
              <i class="icon-plus"></i>
              Tambah RPS</a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_lama' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_daftar_rps_lama'); ?>">
              <i class="icon-folder"></i>
              Daftar RPS Lama</a>
          </li>
        </ul>
      </li>
    <?php else: ?>
      <!-- MENU RPS OBE (STANDAR 2026) -->
      <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
        <a href="javascript:;">
          <i class="icon-book-open"></i>
          <span class="title">RPS (OBE)</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <?php if ($can_access_kurikulum): ?>
            <li class="<?= $menu_aktif == 'm_kurikulum_list' ? 'active' : ''; ?>">
              <a href="<?= site_url('C_kurikulum'); ?>">
                <i class="icon-layers"></i>
                Daftar Kurikulum &amp; Audit <span class="badge badge-warning" style="font-size:9px;padding:2px 4px;">CPL</span></a>
            </li>
            <li class="<?= $menu_aktif == 'm_matriks_mk_cpl' ? 'active' : ''; ?>">
              <a href="<?= site_url('C_matriks_mk_cpl'); ?>">
                <i class="icon-grid"></i>
                Matriks MK-CPL <span class="badge badge-info" style="font-size:9px;padding:2px 4px;">OBE</span></a>
            </li>
          <?php endif; ?>
          <li class="<?= $menu_aktif == 'm_rps_obe_list' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_obe'); ?>">
              <i class="icon-badge"></i>
              Daftar RPS OBE <span class="badge badge-success" style="font-size:9px;padding:2px 4px;">Aktif</span></a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_generator' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_generator'); ?>">
              <i class="icon-magic-wand"></i>
              RPS Generator AI <span class="badge badge-info" style="font-size:9px;padding:2px 4px;">OBE</span></a>
          </li>
        </ul>
      </li>
    <?php endif; ?>
    <!-- <li class="<?= isset($parent_sister) ? $parent_sister : ''; ?>">
      <a href="javascript:;">
        <i class="icon-graduation"></i>
        <span class="title">SISTER</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_sister_dashboard' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/dashboard'); ?>">
            <i class="icon-speedometer"></i>
            Dashboard Saya</a>
        </li>
        <li class="<?= $menu_aktif == 'm_sister_profil_pribadi' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/profil_pribadi'); ?>">
            <i class="icon-user"></i>
            Profil Saya</a>
        </li>
        <?php if($is_pimpinan_sister): ?>
        <li class="<?= $menu_aktif == 'm_sister_profil_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/profil_dosen'); ?>">
            <i class="icon-users"></i>
            Profil Dosen</a>
        </li>
        <?php endif; ?>
        
        <?php if($is_pimpinan_sister): ?>
        <li class="<?= $menu_aktif == 'm_sister_eksekutif' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/eksekutif'); ?>">
            <i class="icon-pie-chart"></i>
            Dashboard Eksekutif</a>
        </li>
        <?php endif; ?>
      </ul>
    </li> -->
    <?php if(!empty($is_gege_satya)): ?>
    <?php
    $is_admin_menu_active_1 = in_array($menu_aktif ?? '', ['m_sister_eksekutif', 'm_monitoring_sk_lama', 'm_setting_rps_mode']);
    $parent_admin_class_1 = ($is_admin_menu_active_1 || !empty($parent_admin)) ? 'active open' : '';
    ?>
    <li class="<?= $parent_admin_class_1; ?>">
      <a href="javascript:;">
        <i class="icon-shield"></i>
        <span class="title">Admin</span>
        <span class="arrow <?= $is_admin_menu_active_1 ? 'open' : ''; ?>"></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_sister_eksekutif' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/eksekutif'); ?>"><i class="icon-pie-chart"></i> Dashboard Eksekutif</a>
        </li>
        <li class="<?= $menu_aktif == 'm_monitoring_sk_lama' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_monitoring_sk_lama'); ?>"><i class="icon-clock"></i> Monitoring SK Lama</a>
        </li>
        <li class="<?= $menu_aktif == 'm_setting_rps_mode' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_setting_rps'); ?>"><i class="icon-wrench"></i> Pengaturan RPS &amp; Maintenance</a>
        </li>
      </ul>
    </li>
    <?php endif; ?>
    <li class="<?= isset($parent_4) ? $parent_4 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-settings"></i>
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
          <a href="<?= site_url('Auth/change_password'); ?>"><i class="icon-lock"></i> Change Password</a>
        </li>
        <li class="">
          <a href="<?= site_url('Auth/logout'); ?>"><i class="icon-logout"></i> Logout</a>
        </li>
      </ul>
    </li>
  </ul>
<?php
} else {
?>
  <ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    <!-- User Profile Card in Sidebar -->
    <li class="sidebar-profile-item" style="list-style: none; margin: 0; padding: 0;">
      <div class="sidebar-profile-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin: 4px 10px 14px 10px; background: rgba(255, 255, 255, 0.1); border-radius: 12px; overflow: hidden;">
        <img src="<?= $avatar_url; ?>" alt="Avatar" class="profile-avatar" width="44" height="44" style="width: 44px !important; height: 44px !important; min-width: 44px !important; max-width: 44px !important; min-height: 44px !important; max-height: 44px !important; border-radius: 50% !important; object-fit: cover !important; display: block !important; flex-shrink: 0 !important; border: 2px solid rgba(255, 255, 255, 0.85);" />
        <div class="profile-info" style="overflow: hidden; display: flex; flex-direction: column;">
          <div class="profile-name" style="color: #ffffff; font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= html_escape($user_full_name); ?>"><?= html_escape($user_full_name); ?></div>
          <div class="profile-role" style="color: #ccfbf1; font-size: 11px; font-weight: 600; margin-top: 2px; display: flex; align-items: center; gap: 4px;"><i class="fa fa-circle" style="font-size: 7px; color: #34d399;"></i> <?= $user_role_label; ?></div>
        </div>
      </div>
    </li>

    <li class="sidebar-heading-modern">
      <i class="fa fa-graduation-cap"></i> MENU DOSEN
    </li>

    <?php
    $username_dc = $this->ion_auth->user()->row()->email;
    $akses_dc = $this->db->where('username', $username_dc)->get('siakad_akses_data_center')->num_rows() > 0;
    $is_kaprodi = $this->ion_auth->in_group(array(5));
    if ($akses_dc || $is_kaprodi) {
    ?>
    <li class="<?= isset($parent_infografis) ? $parent_infografis : ''; ?>">
      <a href="javascript:;">
        <i class="icon-pie-chart"></i>
        <span class="title">Infografis WBI</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_infografis_kerjasama' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_kerjasama'); ?>">
            <i class="icon-briefcase"></i>
            Kerjasama WBI</a>
        </li>
        <li class="<?= $menu_aktif == 'm_infografis_profil_mahasiswa' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_profile_mahasiswa'); ?>">
            <i class="icon-users"></i>
            Profil Mahasiswa 2025</a>
        </li>
        <li class="<?= $menu_aktif == 'm_infografis_lulusan' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_lulusan'); ?>">
            <i class="icon-graduation"></i>
            Profil Lulusan WBI</a>
        </li>
        <li class="<?= $menu_aktif == 'm_intake_quality' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_infografis_intake_quality'); ?>">
            <i class="icon-home"></i>
            Intake Quality</a>
        </li>
        <li class="<?= $menu_aktif == 'm_academic_intelligence' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_academic_intelligence'); ?>">
            <i class="icon-bulb"></i>
            Academic Intelligence</a>
        </li>
        <li class="<?= $menu_aktif == 'm_linimasa_ta' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_linimasa_ta'); ?>">
            <i class="icon-action-redo"></i>
            Linimasa TA</a>
        </li>
        <li class="<?= $menu_aktif == 'm_laporan_semester' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_laporan_semester'); ?>">
            <i class="icon-docs"></i>
            Laporan Semester</a>
        </li>
        <li class="<?= $menu_aktif == 'm_performa_epom_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_performa_epom_dosen'); ?>">
            <i class="icon-graduation"></i>
            Performa EPOM Dosen</a>
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
        <i class="icon-screen-desktop"></i>
        <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->tahun_ajaran_aktif;; ?></span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_jadwal' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_jadwal'); ?>">
            <i class="icon-calendar"></i>
            <span class="title">Jadwal Dosen</span>
          </a>
        </li>
        <li class="<?= $menu_aktif == 'm_isi_nilai_mbkm' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_isi_nilai_excel_mbkm'); ?>">
            <i class="icon-star"></i>
            Nilai MBKM/KKN/PKL</a>
        </li>

        <li class="<?= $menu_aktif == 'm_lapor_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_lapor_nilai'); ?>">
            <i class="icon-cloud-upload"></i>
            Publish Nilai</a>
        </li>

        <li class="<?= $menu_aktif == 'm_evaluasi_diri' ? 'active' : ''; ?>">
          <a href="<?= site_url('Evaluasi_diri'); ?>">
            <i class="icon-user-following"></i>
            Evaluasi Diri</a>
        </li>


      </ul>
    </li>

    <?php
    if ($this->session->userdata('setting')->pengisian_tunda == 1) {
    ?>
      <li class="<?= isset($parent_tunda) ? $parent_tunda : ''; ?>">
        <a href="javascript:;">
          <i class="icon-screen-desktop"></i>
          <span class="title">Perkuliahan TA <?= $this->session->userdata('setting')->id_smt_tunda; ?></span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_jadwal_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_jadwal_tunda'); ?>">
              <i class="icon-speedometer"></i>
              <span class="title">Dashboard</span>
            </a>
          </li>
          <li class="<?= $menu_aktif == 'm_rencana_pertemuan_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rencana_pertemuan_tunda'); ?>">
              <i class="icon-pencil"></i>
              Berita Acara Perkuliahan</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kehadiran_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kehadiran_tunda'); ?>">
              <i class="icon-user-follow"></i>
              Kehadiran</a>
          </li>
          <!-- <li class="<?= $menu_aktif == 'm_kehadiran_2_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kehadiran_2_tunda'); ?>">
              <i class="icon-user-follow"></i>
              Kehadiran 2</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_skema_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_skema_nilai_tunda'); ?>">
              <i class="icon-list"></i>
              Skema Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_isi_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_tunda'); ?>">
              <i class="icon-check"></i>
              Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_isi_nilai_mbkm_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_isi_nilai_excel_mbkm_tunda'); ?>">
              <i class="icon-star"></i>
              Nilai MBKM/KKN/PKL</a>
          </li>

          <li class="<?= $menu_aktif == 'm_lapor_nilai_tunda' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_lapor_nilai_tunda'); ?>">
              <i class="icon-cloud-upload"></i>
              Submit Nilai</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>

    <li class="<?= isset($parent_history) ? $parent_history : ''; ?>">
      <a href="javascript:;">
        <i class="icon-clock"></i>
        <span class="title">History</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">

        <li class="<?= $menu_aktif == 'm_history_ajar' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_history_ajar'); ?>">
            <i class="icon-clock"></i>
            History Ajar</a>
        </li>
        <li class="<?= $menu_aktif == 'm_history_ta' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_history_ta'); ?>">
            <i class="icon-clock"></i>
            History TA</a>
        </li>

      </ul>
    </li>

    <li class="<?= isset($parent_1) ? $parent_1 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-users"></i>
        <span class="title">Perwalian</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_dashboard_perwalian' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_dashboard_perwalian'); ?>">
            <i class="icon-speedometer"></i>
            Dashboard Perwalian
          </a>
        </li>
        <li class="<?= $menu_aktif == 'm_persetujuan_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_persetujuan_krs'); ?>">
            <i class="icon-check"></i>
            Approval KRS
            <span class="badge badge-danger" id="approval_krs_badge" <?= (!$this->session->flashdata('jumlah') ? 'style="display:none;"' : ''); ?>><?= $this->session->flashdata('jumlah'); ?></span>
          </a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_daftar_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_daftar_krs'); ?>">
            <i class="icon-note"></i>
            Sedang Mendaftar KRS</a>
        </li> -->
        <!-- <li class="<?= $menu_aktif == 'm_belum_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_belum_krs'); ?>">
            <i class="icon-close"></i>
            Belum KRS</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_lecturer_data_perwalian' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_data_perwalian'); ?>">
            <i class="icon-graph"></i>
            Keberhasilan Studi</a>
        </li>
        <li class="<?= $menu_aktif == 'm_konsultasi_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_konsultasi_nilai'); ?>">
            <i class="icon-bubbles"></i>
            Konsultasi Nilai</a>
        </li>
      </ul>
    </li>
    <?php if ($rps_menu_mode === 'LEGACY'): ?>
      <!-- MENU RPS LAMA (STANDAR PRODUCTION) -->
      <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
        <a href="javascript:;">
          <i class="icon-book-open"></i>
          <span class="title">RPS</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_rps_new' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_new'); ?>">
              <i class="icon-docs"></i>
              Daftar RPS</a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_tambah' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_new/tambah'); ?>">
              <i class="icon-plus"></i>
              Tambah RPS</a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_lama' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_daftar_rps_lama'); ?>">
              <i class="icon-folder"></i>
              Daftar RPS Lama</a>
          </li>
        </ul>
      </li>
    <?php else: ?>
      <!-- MENU RPS OBE (STANDAR 2026) -->
      <li class="<?= isset($parent_ws) ? $parent_ws : ''; ?>">
        <a href="javascript:;">
          <i class="icon-book-open"></i>
          <span class="title">RPS (OBE)</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <?php if ($can_access_kurikulum): ?>
            <li class="<?= $menu_aktif == 'm_kurikulum_list' ? 'active' : ''; ?>">
              <a href="<?= site_url('C_kurikulum'); ?>">
                <i class="icon-layers"></i>
                Daftar Kurikulum &amp; Audit <span class="badge badge-warning" style="font-size:9px;padding:2px 4px;">CPL</span></a>
            </li>
            <li class="<?= $menu_aktif == 'm_matriks_mk_cpl' ? 'active' : ''; ?>">
              <a href="<?= site_url('C_matriks_mk_cpl'); ?>">
                <i class="icon-grid"></i>
                Matriks MK-CPL <span class="badge badge-info" style="font-size:9px;padding:2px 4px;">OBE</span></a>
            </li>
          <?php endif; ?>
          <li class="<?= $menu_aktif == 'm_rps_obe_list' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_obe'); ?>">
              <i class="icon-badge"></i>
              Daftar RPS OBE <span class="badge badge-success" style="font-size:9px;padding:2px 4px;">Aktif</span></a>
          </li>
          <li class="<?= $menu_aktif == 'm_rps_generator' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_generator'); ?>">
              <i class="icon-magic-wand"></i>
              RPS Generator AI <span class="badge badge-info" style="font-size:9px;padding:2px 4px;">OBE</span></a>
          </li>
        </ul>
      </li>
    <?php endif; ?>

    <li class="<?= isset($parent_skripsi) ? $parent_skripsi : ''; ?>">
      <a href="javascript:;">
        <i class="icon-graduation"></i>
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
          <i class="icon-user"></i>
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
          <li class="<?= $menu_aktif == 'm_arsip_ta_kaprodi' ? 'active' : ''; ?>">
            <a href="<?= site_url('e_skripsi_arsip_kaprodi'); ?>">Arsip TA Kedaluwarsa</a>
          </li>
        </ul>
      </li>
    <?php
    }
    ?>



  <li class="<?= isset($parent_kinerja) ? $parent_kinerja : ''; ?>">
      <a href="javascript:;">
        <i class="icon-briefcase"></i>
        <span class="title">E-KINERJA</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_kinerja' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_kinerja'); ?>">
            <i class="icon-user"></i>
            eKinerja Dosen</a>
        </li>
        <?php 
        $u_email = $this->ion_auth->user()->row()->email ?? '';
        $u_uname = $this->ion_auth->user()->row()->username ?? '';
        $is_rekap_access = ($u_email === 'ferawati.zhang@wbi.ac.id'
                             || $u_uname === 'ferawati.zhang@wbi.ac.id'
                             || $u_email === 'jenny.elisabeth@wbi.ac.id'
                             || $u_uname === 'jenny.elisabeth@wbi.ac.id'
                             || $this->ion_auth->in_group(array(6))
                             || ($this->db->group_start()->where('username', $u_email)->or_where('username', $u_uname)->group_end()->where_in('roles', array('direktur', 'wadir2'))->get('siakad_akses_data_center')->num_rows() > 0));
        if ($is_rekap_access):
        ?>
        <li class="<?= $menu_aktif == 'm_rekap_kinerja' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_kinerja/rekap'); ?>">
            <i class="icon-bar-chart"></i>
            Rekap Kinerja</a>
        </li>
        <li class="<?= $menu_aktif == 'm_bkd_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_kinerja/bkd_dosen'); ?>">
            <i class="icon-badge"></i>
            BKD Internal</a>
        </li>
        <?php endif; ?>
        <li class="<?= $menu_aktif == 'm_pendampingan' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pendampingan'); ?>">
            <i class="icon-users"></i>
            Pendampingan</a>
        </li>
        <?php
        $is_wadir2 = ($u_email === 'ferawati.zhang@wbi.ac.id'
                      || $u_uname === 'ferawati.zhang@wbi.ac.id'
                      || ($this->db->group_start()->where('username', $u_email)->or_where('username', $u_uname)->group_end()->where('roles', 'wadir2')->get('siakad_akses_data_center')->num_rows() > 0));
        if ($is_wadir2):
        ?>
        <li class="<?= $menu_aktif == 'm_fasilitator' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_fasilitator'); ?>">
            <i class="icon-user-following"></i>
            Data Fasilitator</a>
        </li>
        <?php endif; ?>
      </ul>
    </li>

    <li class="<?= isset($parent_summary) ? $parent_summary : ''; ?>">
      <a href="javascript:;">
        <i class="icon-folder"></i>
        <span class="title">Rekap Semester</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_summary_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_nilai'); ?>">
            <i class="icon-check"></i>
            Nilai</a>
        </li>
        <li class="<?= $menu_aktif == 'm_summary_bap' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_bap'); ?>">
            <i class="icon-arrow-right"></i>
            BAP</a>
        </li>
        <li class="<?= $menu_aktif == 'm_summary_kehadiran' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_kehadiran'); ?>">
            <i class="icon-user-follow"></i>
            Kehadiran</a>
        </li>
        <li class="<?= $menu_aktif == 'm_summary_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_epom'); ?>">
            <i class="icon-arrow-right"></i>
            EPOM</a>
        </li>

        <li class="<?= $menu_aktif == 'm_summary_bimbingan_krs' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_summary_bimbingan_krs'); ?>">
            <i class="icon-arrow-right"></i>
            Konsultasi KRS</a>
        </li>
      </ul>
    </li>

    <li class="<?= isset($parent_magang) ? $parent_magang : ''; ?>">
      <a href="javascript:;">
        <i class="icon-home"></i>
        <span class="title">Internship/Magang</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_laporan_magang' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_laporan_magang'); ?>">
            <i class="icon-arrow-right"></i>
            Laporan Magang</a>
        </li>
        <?php if ($this->ion_auth->in_group(array(5))) : ?>
        <li class="<?= $menu_aktif == 'm_pantauan_pkl' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pantauan_pkl'); ?>">
            <i class="icon-arrow-right"></i>
            Pantauan PKL</a>
        </li>
        <?php endif; ?>
      </ul>
    </li>
    <!--<li class="<?= isset($parent_3) ? $parent_3 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-folder"></i>
        <span class="title">Arsip (Data < TA 2019)</span>
            <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_arsip_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_arsip_nilai'); ?>">
            <i class="icon-check"></i>
            Nilai</a>
        </li>
        <li class="<?= $menu_aktif == 'm_arsip_bap' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_arsip_bap'); ?>">
            <i class="icon-arrow-right"></i>
            BAP</a>
        </li>
        <li class="<?= $menu_aktif == 'm_arsip_kehadiran' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_arsip_kehadiran'); ?>">
            <i class="icon-user-follow"></i>
            Kehadiran</a>
        </li>
        <li class="<?= $menu_aktif == 'm_arsip_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_arsip_epom'); ?>">
            <i class="icon-arrow-right"></i>
            EPOM</a>
        </li>
        <li class="<?= $menu_aktif == 'm_arsip_kritik_saran' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_arsip_kritik_saran'); ?>">
            <i class="icon-arrow-right"></i>
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
          <i class="icon-arrow-right"></i>
          <span class="title">KAPRODI</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_monitoring' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_monitoring'); ?>">
              <i class="icon-arrow-right"></i>
              Monitoring BAP</a>
          </li>
          <!-- <li class="<?= $menu_aktif == 'm_rps_all' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_all'); ?>">
              <i class="icon-arrow-right"></i>
              Data RPS Prodi</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_rps_kaprodi' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_rps_kaprodi'); ?>">
              <i class="icon-book-open"></i>
              RPS PRODI</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kaprodi_evaluasi' ? 'active' : ''; ?>">
            <a href="<?= site_url('Kaprodi_evaluasi'); ?>">
              <i class="icon-arrow-right"></i>
              Evaluasi Mahasiswa</a>
          </li>
          <li class="<?= $menu_aktif == 'm_informasi_statistik_kaprodi' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_informasi_statistik_kaprodi'); ?>">
              <i class="icon-arrow-right"></i>
              Informasi dan Statistik</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kaprodi_belum_isi_nilai' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_belum_isi_nilai'); ?>">
              <i class="icon-arrow-right"></i>
              Belum Lapor Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_sebaran_nilai' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_sebaran_nilai'); ?>">
              <i class="icon-arrow-right"></i>
              Sebaran Nilai</a>
          </li>
          <li class="<?= $menu_aktif == 'm_krs' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_data_krs_mahasiswa'); ?>">
              <i class="icon-arrow-right"></i>
              KRS</a>
          </li>
          <li class="<?= $menu_aktif == 'm_krs_baru' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_data_krs_mahasiswa_baru'); ?>">
              <i class="icon-arrow-right"></i>
              KRS Mahasiswa Baru</a>
          </li>
          <li class="<?= $menu_aktif == 'm_kaprodi_data_nilai' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_data_nilai'); ?>">
              <i class="icon-check"></i>
              Nilai Mahasiswa</a>
          </li>
          <!-- <li class="<?= $menu_aktif == 'm_kaprodi_bap' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_bap'); ?>">
              <i class="icon-arrow-right"></i>
              BAP KAPRODI</a>
          </li> -->
          <li class="<?= $menu_aktif == 'm_kaprodi_epom' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_kaprodi_epom'); ?>">
              <i class="icon-arrow-right"></i>
              EPOM KAPRODI</a>
          </li>
          <li class="<?= $menu_aktif == 'm_konfirmasi' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_eoffice_konfirmasi'); ?>">
              <i class="icon-arrow-right"></i>
              Permohonan</a>
          </li>
          <li class="<?= $menu_aktif == 'm_konfirmasi_magang' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_konfirmasi_magang'); ?>">
              <i class="icon-arrow-right"></i>
              Konfirmasi Magang</a>
          </li>
          <li class="<?= $menu_aktif == 'm_tracking_kegiatan' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_tracking_kegiatan'); ?>">
              <i class="icon-arrow-right"></i>
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
          <i class="icon-link"></i>
          <span class="title">LAPKERMA</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_daftar_instansi' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_instansi'); ?>">
              <i class="icon-arrow-right"></i>
              Instans/Mitra</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou'); ?>">
              <i class="icon-arrow-right"></i>
              MOU Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou_expired' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou_expired'); ?>">
              <i class="icon-arrow-right"></i>
              MOU Expired</a>
          </li>

          <li class="<?= $menu_aktif == 'm_daftar_mou_tidak_aktif' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_daftar_mou_tidak_aktif'); ?>">
              <i class="icon-arrow-right"></i>
              MOU Tidak Aktif</a>
          </li>
          <li class="<?= $menu_aktif == 'm_moa' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa'); ?>">
              <i class="icon-arrow-right"></i>
              MOA Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_moa_expired' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa_expired'); ?>">
              <i class="icon-arrow-right"></i>
              MOA Expired</a>
          </li>

          <li class="<?= $menu_aktif == 'm_moa_tidak_aktif' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_moa_tidak_aktif'); ?>">
              <i class="icon-arrow-right"></i>
              MOA Tidak Aktif</a>
          </li>

          <li class="<?= $menu_aktif == 'm_download' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_lap_jumlah_kerjasama'); ?>">
              <i class="icon-arrow-right"></i>
              Download Data</a>
          </li>

          <li class="<?= $menu_aktif == 'm_kegiatan' ? 'active' : ''; ?>">
            <a href="<?= site_url('lapkerma/C_kegiatan'); ?>">
              <i class="icon-arrow-right"></i>
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
          <i class="icon-magnifier"></i>
          <span class="title">Audit Mutu</span>
          <span class="arrow "></span>
        </a>
        <ul class="sub-menu">
          <li class="<?= $menu_aktif == 'm_audit_mutu' ? 'active' : ''; ?>">
            <a href="<?= site_url('C_audit_mutu'); ?>">
              <i class="icon-arrow-right"></i>
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
        <i class="icon-folder"></i>
        <span class="title">Pusat Data</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_revisi_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_revisi_nilai'); ?>">
            <i class="icon-arrow-right"></i>
            Memo Revisi Nilai</a>
        </li>
        <li class="<?= $menu_aktif == 'm_pusat_data_nilai' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_nilai'); ?>">
            <i class="icon-arrow-right"></i>
            Data Nilai</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_bap' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_bap'); ?>">
            <i class="icon-arrow-right"></i>
            Data BAP</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_pusat_data_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_epom'); ?>">
            <i class="icon-arrow-right"></i>
            Data EPOM</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_analisa_epom' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_analisa_epom'); ?>">
            <i class="icon-arrow-right"></i>
            Analisa EPOM</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_pusat_data_epom_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_epom_dosen'); ?>">
            <i class="icon-arrow-right"></i>
            EPOM Dosen</a>
        </li>
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_rps' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_rps'); ?>">
            <i class="icon-arrow-right"></i>
            Data RPS</a>
        </li> -->
        <!-- <li class="<?= $menu_aktif == 'm_pusat_data_profil' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_pusat_data_profil'); ?>">
            <i class="icon-arrow-right"></i>
            Profil Mahasiswa</a>
        </li> -->
        <li class="<?= $menu_aktif == 'm_eoffice_handling_time' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_eoffice_handling_time'); ?>">
            <i class="icon-arrow-right"></i>
            Eoffice Monev</a>
        </li>
      </ul>
    </li>
    <?php
    }
    ?>
    <li class="<?= isset($parent_sister) ? $parent_sister : ''; ?>">
      <a href="javascript:;">
        <i class="icon-graduation"></i>
        <span class="title">SISTER</span>
        <span class="arrow "></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_sister_dashboard' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/dashboard'); ?>">
            <i class="icon-speedometer"></i>
            Dashboard Saya</a>
        </li>
         <li class="<?= $menu_aktif == 'm_sister_profil_pribadi' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/profil_pribadi'); ?>">
            <i class="icon-user"></i>
            Profil Saya</a>
        </li>
        <?php if($is_pimpinan_sister || !empty($is_gege_satya)): ?>
        <li class="<?= $menu_aktif == 'm_sister_profil_dosen' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/profil_dosen'); ?>">
            <i class="icon-users"></i>
            Profil Dosen</a>
        </li>
        <?php endif; ?>
      </ul>
    </li>
    <?php if(!empty($is_gege_satya)): ?>
    <?php
    $is_admin_menu_active = in_array($menu_aktif ?? '', ['m_sister_eksekutif', 'm_monitoring_sk_lama', 'm_setting_rps_mode']);
    $parent_admin_class = ($is_admin_menu_active || !empty($parent_admin)) ? 'active open' : '';
    ?>
    <li class="<?= $parent_admin_class; ?>">
      <a href="javascript:;">
        <i class="icon-shield"></i>
        <span class="title">Admin</span>
        <span class="arrow <?= $is_admin_menu_active ? 'open' : ''; ?>"></span>
      </a>
      <ul class="sub-menu">
        <li class="<?= $menu_aktif == 'm_sister_eksekutif' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_sister/eksekutif'); ?>">
            <i class="icon-pie-chart"></i>
            Dashboard Eksekutif</a>
        </li>
        <li class="<?= $menu_aktif == 'm_monitoring_sk_lama' ? 'active' : ''; ?>">
          <a href="<?= site_url('c_monitoring_sk_lama'); ?>">
            <i class="icon-clock"></i>
            Monitoring SK Lama</a>
        </li>
        <li class="<?= $menu_aktif == 'm_setting_rps_mode' ? 'active' : ''; ?>">
          <a href="<?= site_url('C_setting_rps'); ?>">
            <i class="icon-wrench"></i>
            Pengaturan RPS &amp; Maintenance</a>
        </li>
      </ul>
    </li>
    <?php endif; ?>
    <li class="<?= isset($parent_4) ? $parent_4 : ''; ?>">
      <a href="javascript:;">
        <i class="icon-settings"></i>
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
          <a href="<?= site_url('Auth/change_password'); ?>"><i class="icon-lock"></i> Change Password</a>
        </li>
        <li class="">
          <a href="<?= site_url('Auth/logout'); ?>"><i class="icon-logout"></i> Logout</a>
        </li>
      </ul>
    </li>
  </ul>
<?php
}
?>
