<?php
if (empty($_POST['register'])) {
?>
  <form method="post">
    <p><input type="text" name="username" placeholder="<?php echo i18n_r(self::FILE.'/USERNAME'); ?>" required></p>
    <p><input type="email" name="email" placeholder="<?php echo i18n_r(self::FILE.'/EMAIL'); ?>" required></p>
    <p><input type="password" name="password" placeholder="<?php echo i18n_r(self::FILE.'/PASSWORD'); ?>" required></p>
    <p><input type="password" name="confirm" placeholder="<?php echo i18n_r(self::FILE.'/CONFIRM'); ?>" required></p>
    <?php if ($this->config['captcha']) $this->displayCaptchaForm(true); ?>
    <input type="submit" name="register" value="<?php echo i18n_r(self::FILE.'/REGISTER'); ?>">
  </form>
<?php
}
else {
  $validate = $this->validate('register');
  if ($validate) {
?>
  <p class="success"><?php echo i18n_r(self::FILE.'/REGISTER_SUCCESS'); ?></p>
<?php
  }
  else {
?>
  <p class="error"><?php echo i18n_r(self::FILE.'/REGISTER_FAIL'); ?></p>
<?php
  }
} ?>
