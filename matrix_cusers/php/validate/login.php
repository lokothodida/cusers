<?php

// check if user exists
$checkUser = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE username = "'.$username.'"', 'SINGLE');

if ($checkUser && !$this->isBanned($checkUser) && $checkUser['password'] == $this->saltSha1($password) && $checkCaptcha) {
  // refresh IP address
  $this->matrix->updateRecord(self::TABLE_USERS, $checkUser['id'], array('ip' => $this->getIP()));
  $this->login($checkUser);
  $return = true;
}
else $return = false;


?>