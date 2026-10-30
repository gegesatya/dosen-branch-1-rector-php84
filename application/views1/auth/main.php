<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
<meta charset="utf-8"/>
<title>PORTAL WBI</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<?php $this->load->view('_layout/_head_css', '', FALSE); ?>
</head>











<body class="page-header-fixed page-sidebar-closed-hide-logo ">
<div class="page-header navbar navbar-fixed-top">
  
  <div class="page-header-inner">
    
    <div class="page-logo">
      <a href="#">
      <img src="<?=base_url('assets');?>/image_web/logo-white-3.png" width='90px' alt="logo" class="logo-default"/>
      </a>
      <div class="menu-toggler sidebar-toggler">
        
      </div>
    </div>
    
    
    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    </a>
    
    
    <div class="page-top">
      
      <div class="top-menu">
        <ul class="nav navbar-nav pull-right">
          
          
          <?php $user = $this->ion_auth->user()->row(); ?>
          
          <li class="dropdown dropdown-user dropdown-dark">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <span class="username username-hide-on-mobile">
              <?= @$user->first_name.' '.@$user->last_name; ?> 
            </span>
            
            <!-- <img alt="" class="img-circle" src="<?=base_url('assets/theme');?>/assets/admin/layout4/img/avatar9.jpg"/> -->
            </a>
            <ul class="dropdown-menu dropdown-menu-default">
              
              <li>
                <a href="<?= site_url('auth/logout') ?>">
                <i class="icon-key"></i> Log Out </a>
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
  <div class="page-sidebar-wrapper">  
    <div class="page-sidebar navbar-collapse collapse">
      <?php if($this->ion_auth->logged_in()) { ?>
      <?php $this->load->view('_layout/_main_menu', '', FALSE); ?>
      <?php } ?>
      
    </div>
  </div>
  
  
  <div class="page-content-wrapper">
    <?php $this->load->view($view_file, $output, FALSE); ?>
  </div>
  
</div>

<div class="page-footer">
  <div class="page-footer-inner">
    <?=date('Y');?> Bagian Teknologi Informasi Wilmar Bisnis Indonesia
  </div>
  <div class="scroll-to-top">
    <i class="icon-arrow-up"></i>
  </div>
</div>

<?php $this->load->view('_layout/_script', '', FALSE); ?>
</body>

</html>
