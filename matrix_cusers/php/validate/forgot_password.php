<?php

// check if user exists
$checkUser = $this->matrix->query('SELECT id, username, displayname, email FROM '.self::TABLE_USERS.' WHERE email = "'.$email.'"', 'SINGLE');

if ($checkUser) {
  // send email
  $to = $email;
  $subject = $this->matrix->getSiteName().' - '.i18n_r('FORGOT_PASSWORD');
  $newPassword = $this->randomString();
  ob_start();
  ?>
  <h2><?php echo $this->matrix->getSiteName(); ?> - <?php echo i18n_r(self::FILE.'/FORGOT_PASSWORD'); ?></h2>
  <p><?php echo str_replace('%user%', '<i>'.$checkUser['displayname'].'</i>', i18n_r(self::FILE.'/FORGOTPASS_EMAILMSG')); ?></p>
  <p><b><?php echo i18n_r(self::FILE.'/USERNAME'); ?></b>: <?php echo $checkUser['username']; ?></p>
  <p><b><?php echo i18n_r(self::FILE.'/PASSWORD'); ?></b>: <?php echo $newPassword; ?></p>
  <?php
  $message = ob_get_contents();
  ob_end_clean();
  $mail = sendmail($to, $subject, $message);
  if ($mail) {
   // only change the password if the email is sent
   $changePass = $this->matrix->updateRecord(self::TABLE_USERS, $checkUser['id'], array('password' => $newPassword));  
   if ($changePass) $return = true;
   else $return = false;
  }
  else $return = false;
}
else $return = false;

?>
