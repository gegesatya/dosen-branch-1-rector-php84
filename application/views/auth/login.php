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
    
    <?php if (!empty($message)): ?>
      <?php 
        $is_success = (isset($message_type) && $message_type === 'success');
        $bg_color = $is_success ? 'rgba(52, 199, 89, 0.25)' : 'rgba(255, 59, 48, 0.25)';
        $border_color = $is_success ? 'rgba(52, 199, 89, 0.4)' : 'rgba(255, 59, 48, 0.4)';
        $icon = $is_success ? 
          '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>' : 
          '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
      ?>
      <div id="infoMessage" style="color: #fff; background: <?= $bg_color ?>; border: 1px solid <?= $border_color ?>; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); padding: 12px 15px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; text-align: center; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <?= $icon ?>
        <span style="margin: 0; padding: 0; line-height: 1.2; letter-spacing: 0.3px;">
          <?php echo strip_tags($message); ?>
        </span>
      </div>
    <?php endif; ?>

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