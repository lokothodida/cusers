<form method="post">
  <?php
  // logged out
  if (!empty($_POST['logout'])) {
    $this->logout();
  ?>
    <p><?php echo i18n_r(self::FILE.'/LOGOUT_SUCCESS'); ?></p>
    <?php if (!empty($_POST['logout'])) { ?>
      <script>
        setTimeout(function () {
          window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
        }, 1000); // set timeout
      </script>
      <?php } ?>
  <?php
  }
  
  // logged in
  elseif (!empty($_POST['login']) || $this->loggedIn()) {
    $validate = false;
    if (!empty($_POST['login']) ) {
      $validate = $this->validate('login');
    }
    if ($validate || $this->loggedIn()) {
    ?>
      <p class="success"><?php echo str_replace('%s', $_SESSION['cuser']['displayname'], i18n_r(self::FILE.'/LOGIN_SUCCESS')); ?></p>
      <input type="submit" name="logout" value="<?php echo i18n_r(self::FILE.'/LOGOUT'); ?>">
      <?php if (!empty($_POST['login'])) { ?>
      <script>
        setTimeout(function () {
          window.location.replace("<?php echo $_SERVER['REQUEST_URI']; ?>");
        }, 1000); // set timeout
      </script>
      <?php } ?>
    <?php
    }
    else {
    ?>
      <p class="error"><?php echo i18n_r(self::FILE.'/LOGIN_FAIL'); ?></p>
    <?php
    }
  }
  

  
  // login
  else {
    ?>
    <p><input type="text" name="username" placeholder="<?php echo i18n_r(self::FILE.'/USERNAME'); ?>"></p>
    <p><input type="password" name="password" placeholder="<?php echo i18n_r(self::FILE.'/PASSWORD'); ?>"></p>
    <?php if ($this->config['captcha']) $this->displayCaptchaForm(true); ?>
    <input type="submit" name="login" value="<?php echo i18n_r(self::FILE.'/LOGIN'); ?>">
    <?php
  }
  
  
  ?>
</form>