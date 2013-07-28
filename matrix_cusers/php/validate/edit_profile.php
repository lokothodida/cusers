<?php

$user = $this->matrix->query('SELECT id FROM '.self::TABLE_USERS.' WHERE username = "'.$username.'"', 'SINGLE', $cache=false);

// check passwords match
if ($password == $confirm) {
  $checkPassword = 1;
}
else $checkPassword = 0;

// check if username/display already exists
$displaynameParsed = trim(strtolower($displayname));
$checkUsername = $this->matrix->query('SELECT id, displayname FROM '.self::TABLE_USERS.' WHERE LOWER(displayname) = "'.$displaynameParsed.'"', 'SINGLE', $cache=false);
if ($checkUsername && $checkUsername['id'] == $user['id']) $checkUsername = false;

$checkDisplay  = $this->matrix->query('SELECT id, username FROM '.self::TABLE_USERS.' WHERE LOWER(username) = "'.$displaynameParsed.'"', 'SINGLE', $cache=false);
if ($checkDisplay && $checkDisplay['id'] == $user['id']) $checkDisplay = false;

if ($checkPassword == 0 || $checkUsername || $checkDisplay) {
  $return = false;
}
else {
  $user = $this->matrix->query('SELECT id FROM '.self::TABLE_USERS.' WHERE username = "'.$username.'"', 'SINGLE', $cache=false);
  
  // password validation
  if (empty($password)) unset($query['password']);
  
  $this->matrix->updateRecord(self::TABLE_USERS, $user['id'], $query);
  $return = true;
}

?>