<?php

// save changes
if ($_SERVER['REQUEST_METHOD']=='POST') {

  // update the record
  $update = $this->matrix->updateRecord(self::TABLE_CONFIG, 0, $_POST);
  
  // fix passwords
  if ($update && $update['old']['salt'] != $update['new']['salt']) {
    $this->newSalt($update['new']['salt']);
  }
  
  
  // success message
  if ($update) {
    $undo = 'load.php?id='.self::FILE.'&config&undo';
    $this->matrix->getAdminError(i18n_r(self::FILE.'/CONFIG_UPDATESUCCESS'), true, true, $undo);
    
    // fix users schema
    $this->usersSchema['fields']['level']['options'] = $update['new']['levels'];
    $this->usersSchema['fields']['password']['salt'] = $update['new']['salt'];
    
    // modify the schemas
    $this->matrix->modSchema(self::TABLE_USERS, $this->usersSchema);
  }
  // error message
  else {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/PAGES_UPDATEERROR'), false);
    
    // fix users schema
    $this->usersSchema['fields']['level']['options'] = $update['new']['levels'];
    $this->usersSchema['fields']['password']['salt'] = $update['new']['salt'];
    
    // modify the schemas
    $this->matrix->modSchema(self::TABLE_USERS, $this->usersSchema);
  }
}
// undo changes
elseif (isset($_GET['undo'])) {
  // undo the record update
  $undo = $this->matrix->undoRecord(self::TABLE_CONFIG, 0);
  
  // success message
  if ($undo) {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/CONFIG_UNDOSUCCESS'), true);

    // fix users schema
    $record = $this->matrix->recordExists(self::TABLE_CONFIG, 0);
    $this->usersSchema['fields']['level']['options'] = $record['levels'];
    $this->usersSchema['fields']['password']['salt'] = $record['salt'];
    
    // modify the schemas
    $this->matrix->modSchema(self::TABLE_USERS, $this->usersSchema);
  }
  // error message
  else {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/CONFIG_UNDOERROR'), false);
  }
  
  
  // refresh the index to reflect the changes
  $this->matrix->refreshIndex();
}

?>

<h3 class="floated"><?php echo i18n_r(self::FILE.'/CONFIG'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>&config" class="current"><?php echo i18n_r(self::FILE.'/CONFIG'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&add"><?php echo i18n_r(self::FILE.'/USER_ADD'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&template=profile"><?php echo i18n_r(self::FILE.'/PROFILE'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/USERS'); ?></a>
  <div class="clear"></div>
</div>

<form method="post" action="load.php?id=<?php echo self::FILE; ?>&config">
  <?php $this->matrix->displayForm(self::TABLE_CONFIG, 0); ?>
  <input type="submit" class="submit" name="save" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
</form>