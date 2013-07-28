<?php

// check if username already exists
$checkUsername = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE username = "'.$username.'"', 'MULTI', $cache=false);

// check if email already exists
$checkEmail = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE email = "'.$email.'"', 'MULTI', $cache=false);

// check passwords match
if ($password == $confirm) {
  $checkPassword = 1;
}
else $checkPassword = 0;

// final return
if (!empty($checkUsername) || !empty($checkEmail) || $checkPassword == 0 || $checkCaptcha == false) {
  $return = false;
}
else {
  // add user
  $this->matrix->createRecord(self::TABLE_USERS, array('displayname' => $displayname, 'username' => $username, 'password' => $password, 'email' => $email, 'registered' => time(), 'ip' => $this->getIP()));
  
  // send verification email
  $to = $email;
  $subject = $this->matrix->getSiteName().' - '.i18n_r('REGISTER');
  ob_start();
  ?>
  <h2><?php echo $this->matrix->getSiteName(); ?> - <?php echo i18n_r(self::FILE.'/REGISTER'); ?></h2>
  <p><?php echo str_replace('%user%', '<i>'.$username.'</i>', i18n_r(self::FILE.'/REGISTER_EMAILMSG')); ?></p>
  <p><b><?php echo i18n_r(self::FILE.'/USERNAME'); ?></b>: <?php echo $username; ?></p>
  <p><b><?php echo i18n_r(self::FILE.'/PASSWORD'); ?></b>: <?php echo $password; ?></p>
  <?php
  $message = ob_get_contents();
  ob_end_clean();
  $mail = sendmail($to, $subject, $message);
  
  $return = true;
}

?>