<form method="post">
<?php

if (!empty($_POST['forgotpass'])) {
  $validate = $this->validate('forgot-password');
  if ($validate) {
    // success
  ?>
  <p class="success"><?php echo str_replace('%', $_POST['email'], i18n_r(self::FILE.'/FORGOTPASS_SUCCESS')); ?></p>
  <?php
  }
  else {
    // error
    ?>
    <p class="error"><?php echo i18n_r(self::FILE.'/FORGOTPASS_ERROR'); ?></p>
    <?php
  }
}
else {
  // form
?>

<input type="email" placeholder="name@email.com" name="email" required>
<input type="submit" name="forgotpass" value="<?php echo i18n_r(self::FILE.'/SEND'); ?>">

<?php
}

?>
</form>
