<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Politeknik Wilmar Bisnis Indonesia</title>

  <link href="<?= base_url('assets/glass_login/style.css'); ?>" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?= base_url('assets/favicon.png'); ?>">
</head>

<body>
  <div class="wrapper">
    <?php
    $attributes = array('class' => 'text-start', 'id' => 'myform', 'role' => 'form');
    echo form_open('Auth/login', $attributes);
    ?>
    <h2>Portal Dosen WBI</h2>
    <div class="input-field">
      <input type="text" required id="identity" name="identity" autocomplete="off">
      <label>Enter your email</label>
    </div>
    <div class="input-field">
      <input type="password" required id="password" name="password" autocomplete="new-password">
      <label>Enter your password</label>
    </div>

    <button type="submit">Log In</button>
    <?php echo form_close(); ?>

    <a href="https://dosen.wbi.ac.id/auth/connect_with_google" class="btn-google">
      <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo">
      Sign in with Google
    </a>

  </div>

</body>

</html>