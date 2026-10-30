<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->


<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="robots" content="noindex">
  
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/elite-admin'); ?>/assets/images/favicon.png">
  <title><?= $judul; ?></title>
  
  <link href="<?= base_url('assets'); ?>/frontend/default-new/css/all.style.min.css" rel="stylesheet" type="text/css">
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css" rel="stylesheet">
  <link href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/toast-master/css/jquery.toast.css" rel="stylesheet">
  
  <link href="<?= base_url('assets/elite-admin/hospital'); ?>/dist/css/style.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/elite-admin/hospital'); ?>/dist/css/pages/file-upload.css" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/summernote/dist/summernote-bs4.css">
  <link href="<?= base_url('assets'); ?>/frontend/default-new/css/custom.css" rel="stylesheet" type="text/css">
  
  
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body class="skin-megna fixed-layout">
  
  
  
  <div class="preloader">
    <div class="loader">
      <div class="loader__figure"></div>
      <p class="loader__label">WBI</p>
    </div>
  </div>
  
  
  
  <div id="main-wrapper">
    
    
    
    <header class="topbar">
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        
        
        
        <div class="navbar-header">
          <a class="navbar-brand" href="javascript:void(0)">
            <b>
              
              
              <img src="<?= base_url('assets/elite-admin'); ?>/assets/images/logo-icon.png" alt="homepage" class="dark-logo" width="30px" />
              
              <img src="<?= base_url('assets/elite-admin'); ?>/assets/images/logo-light-icon.png" alt="homepage" class="light-logo" width="30px" />
            </b>
            
            <span class="hidden-xs"><span class="font-bold">My</span> Virtual Class</span>
          </a>
        </div>
        
        
        
        <div class="navbar-collapse">
          
          
          
          <ul class="navbar-nav mr-auto">
            
            <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
            <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
          </ul>
        </div>
      </nav>
    </header>
    
    
    
    
    
    
    <aside class="left-sidebar">
      
      <div class="scroll-sidebar">
        
        <nav class="sidebar-nav">
          <ul id="sidebarnav">
            <li> <a class="waves-effect waves-dark" href="<?= site_url('C_jadwal'); ?>"><i class="icon-speedometer"></i><span class="hide-menu">Back To Dashboard</span></a></li>
            <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">Overview</span></a>
              <ul aria-expanded="false" class="collapse">
                <?php
                if ($is_generate_bap == 0) {
                ?>
                  <li><a href="<?= site_url('C_isi_nilai_excel/index/' . $id_jadwal); ?>">Nilai</a></li>

                <?php
                } else {
                ?>
                  <li><a href="<?= site_url('C_strategi_pertemuan/index/' . $id_jadwal); ?>">Strategi Pertemuan</a></li>
                  <li><a href="<?= site_url('C_kehadiran/index/' . $id_jadwal); ?>">Kehadiran</a></li>
                  <li><a href="<?= site_url('C_isi_nilai_excel/index/' . $id_jadwal); ?>">Nilai</a></li>
                  <?php /* Menu Organisasi Kelas dinonaktifkan sementara
                  <li><a href="<?= site_url('C_organisasi_kelas/index/' . $id_jadwal); ?>">Organisasi Kelas</a></li>
                  */ ?>
                <?php
                }
                ?>

              </ul>
            </li>

            <?php /* Menu Meeting dinonaktifkan sementara
            <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">Meeting</span></a>
              <ul aria-expanded="false" class="collapse">
                <?php
                $i = 1;
                if (!empty($meet) && is_iterable($meet)) {
                  foreach ($meet as $key => $value) {
                ?>
                  <li><a href="<?= site_url('C_meet/index/' . ($value->uniq ?? '')); ?>">Chapter <?= $i; ?></a></li>
                <?php
                    $i++;
                  }
                }
                ?>

              </ul>
            </li>
            */ ?>

            <li>
              <a class="waves-effect waves-dark" href="<?= site_url('auth/logout'); ?>" aria-expanded="false">
                <i class="fa fa-power-off"></i>
                <span class="hide-menu">Log Out</span>
              </a>
            </li>
          </ul>
        </nav>
        
      </div>
      
    </aside>
    
    
    
    
    
    
    <div class="page-wrapper">
      
      
      
      <div class="container-fluid">
        <?php $this->load->view($content); ?>
      </div>
      
      
      
    </div>
  </div>
  
  
  
  
  
  
  <footer class="footer">
    © Bagian Sistem Informasi WBI
  </footer>
  
  
  
  
  
  
  
  
  
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
  
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/popper/popper.min.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  
  <script src="<?= base_url('assets/elite-admin/hospital'); ?>/dist/js/perfect-scrollbar.jquery.min.js"></script>
  
  <script src="<?= base_url('assets/elite-admin/hospital'); ?>/dist/js/waves.js"></script>
  
  <script src="<?= base_url('assets/elite-admin/hospital'); ?>/dist/js/sidebarmenu.js"></script>
  
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
  
  <script src="<?= base_url('assets/elite-admin/hospital'); ?>/dist/js/custom.min.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/moment/moment.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/select2/dist/js/select2.full.min.js" type="text/javascript"></script>
  <script src="<?= base_url('assets/elite-admin/hospital'); ?>/dist/js/pages/jasny-bootstrap.js"></script>
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/toast-master/js/jquery.toast.js"></script>
  
  <script src="<?= base_url('assets/elite-admin'); ?>/assets/node_modules/sweetalert/sweetalert.min.js"></script>
  <script src="<?= base_url('assets/elite-admin/assets/node_modules/summernote/dist/summernote-bs4.min.js'); ?>"></script>
  <script src="<?= base_url('assets/elite-admin/assets/node_modules/datatables/datatables.min.js'); ?>"></script>
  
  <?php
  if (isset($after_page)) {
    $this->load->view($after_page);
  }
  ?>

</body>

</html>