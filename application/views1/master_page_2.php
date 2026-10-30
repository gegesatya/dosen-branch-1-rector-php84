<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->


<head>
  <meta charset="utf-8" />
  <title>Akademik Dosen WBI</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <meta name="robots" content="noindex">
  <?php $this->load->view('_layout/_head_css', '', FALSE); ?>
</head>












<body class="page-header-fixed page-sidebar-closed-hide-logo ">
  <div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner">
      <div class="page-logo">
        <a href="#">
          <img src="<?= base_url('assets'); ?>/image_web/logo-white-3.png" alt="logo" width="150px" class="logo-default" />
        </a>
        <div class="menu-toggler sidebar-toggler">
        </div>
      </div>
      <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
      </a>
      <div class="page-top">
        <div class="top-menu">
          <ul class="nav navbar-nav pull-right">
            <li class="separator hide">
            </li>
            
            
            <li class="dropdown dropdown-extended dropdown-notification dropdown-dark" id="header_notification_bar">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <i class="icon-bell"></i>
                <span class="badge badge-success">
                  <?= cekNotifikasi($this->session->userdata('id_ptk')); ?> </span>
              </a>
              <ul class="dropdown-menu">
                <li class="external">
                  <h3>Notifikasi Tugas Akhir</h3>
                  <a href="<?= site_url('e_skripsi_notifikasi'); ?>">view all</a>
                </li>
                <li>
                  <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                    <?php
                    if (cekNotifikasi($this->session->userdata('id_ptk')) > 0) {
                      $get_notif = notifikasi_data($this->session->userdata('id_ptk'));
                      $url_download = '';
                      foreach ($get_notif as $key => $value) {
                        if ($value->kategori_cetak == 'SK_pembimbing') {
                          $url_download = '<a href="' . site_url('E_skripsi_notifikasi/cetak_sk/') . md5($value->id_judul_skripsi) . '/' . $value->id_judul_skripsi . '" target="_new">
                          <span class="details">' . $value->keterangan . '</span></a>';
                        } elseif ($value->kategori_cetak == 'surat_tugas') {
                          $url_download = '<a href="' . site_url('E_skripsi_notifikasi/cetak_surat_tugas/') . md5($value->id_judul_skripsi) . '/' . $value->id_judul_skripsi . '" target="_new">
                          <span class="details">' . $value->keterangan . '</span></a>';
                        } elseif ($value->kategori_cetak == 'undangan_sempro') {
                          $url_download = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan/') . md5($value->id_judul_skripsi) . '/' . $value->id_judul_skripsi . '" target="_new">
                          <span class="details">' . $value->keterangan . '</span></a>';
                        } elseif ($value->kategori_cetak == 'undangan_semhas') {
                          $url_download = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_semhas/') . md5($value->id_judul_skripsi) . '/' . $value->id_judul_skripsi . '" target="_new">
                          <span class="details">' . $value->keterangan . '</span></a>';
                        } elseif ($value->kategori_cetak == 'undangan_sidang') {
                          $url_download = '<a href="' . site_url('E_skripsi_notifikasi/cetak_undangan_sidang/') . md5($value->id_judul_skripsi) . '/' . $value->id_judul_skripsi . '" target="_new">
                          <span class="details">' . $value->keterangan . '</span></a>';
                        }
                    ?>
                        <li>
                          <?= $url_download; ?>
                        </li>
                    <?php
                      }
                    }
                    ?>
                  </ul>
                </li>
              </ul>
            </li>
            
            <li class="separator hide">
            </li>
            <li class="dropdown dropdown-user dropdown-dark">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <span class="username username-hide-on-mobile">
                  <?= @$user->first_name . ' ' . @$user->last_name; ?> </span>
                
                
              </a>
              <ul class="dropdown-menu dropdown-menu-default">
                <li>
                  <a href="<?= site_url('auth/logout') ?>"><i class="icon-key"></i> Log Out </a>
                </li>
              </ul>
            </li>
            
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix">
  </div>
  <div class="page-container">
    <div class="col-md-8 col-md-offset-2">
      <?php $this->load->view($content, '', FALSE); ?>
    </div>

  </div>
  <div class="page-footer">
    <div class="page-footer-inner col-md-8 col-md-offset-2">
      <?= date('Y'); ?> Bagian Teknologi Informasi Wilmar Bisnis Indonesia
    </div>
    <div class="scroll-to-top">
      <i class="icon-arrow-up"></i>
    </div>
  </div>
  <?php $this->load->view('_layout/_script', '', FALSE); ?>
</body>

</html>