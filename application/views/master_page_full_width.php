<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->


<head>
  <meta charset="utf-8" />
  <title>Review Laporan PKL</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta name="robots" content="noindex">

  <?php $this->load->view('_layout/_head_css', '', FALSE); ?>
</head>


<body>


  <div class="page-container">

    <div>
      <?php $this->load->view($content, '', FALSE); ?>
    </div>
  </div>
  <?php $this->load->view('_layout/_script', '', FALSE); ?>
</body>

</html>