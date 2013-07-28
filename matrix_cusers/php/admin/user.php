<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update = $this->matrix->updateRecord(self::TABLE_USERS, $_GET['user'], $_POST);
    
    // success message
    if ($update) {
      $this->matrix->getAdminError(i18n_r(self::FILE.'/USER_UPDATESUCCESS'), true);
    }
    
    // error message
    else {
      $this->matrix->getAdminError(i18n_r(self::FILE.'/USER_UPDATEERROR'), false);
    }
    
    // refresh the index to reflect the changes
    $this->matrix->refreshIndex();
  }
  
  $user = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE id = '.$_GET['user'], 'SINGLE');
?>

<!--heading-->
<h3 class="floated"><?php echo i18n_r(self::FILE.'/MANAGE_USER'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
  <a href="#" id="metadata_toggle"><?php echo i18n_r(self::FILE.'/OPTIONS'); ?></a>
  <div class="clear"></div>
</div>

<!--form-->
  <form method="post">
    <p><?php $this->matrix->displayField(self::TABLE_USERS, 'displayname', $user['displayname']); ?></p>
    
    <div class="leftsec">
      <p><input type="text" class="text" readonly value="<?php echo $user['username']; ?>"/></p>
      <p><input type="text" class="text" readonly value="<?php echo $user['ip']; ?>"/></p>
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'level', $user['level']); ?></p>
    </div>
    
    <div class="rightsec">
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'avatar', $user['avatar']); ?></p>
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'email', $user['email']); ?></p>
    </div>
    
    <div class="clear"></div>

    <!--custom fields-->
    <div id="metadata_window">
      <?php 
      
        $fields = $this->matrix->getSchema(self::TABLE_USERS);
        $newfields = $fields['fields'];
        foreach ($this->coreFields as $field) {
          if (isset($newfields[$field])) unset($newfields[$field]);
        }
        $total = ceil(count($newfields));
        $mid = $total/2;
        $first = array_slice($newfields, 0, $mid);
        $second = array_slice($newfields, $mid, $total);
        
      
      ?>
      <div class="leftopt">
        <?php
          foreach ($first as $field) {
            if (!isset($user[$field['name']])) $user[$field['name']] = $field['default'];
            if (!empty($field['label'])) echo '<label>'.$field['label'].' : </label>';
            echo '<p>';
            $this->matrix->displayField(self::TABLE_USERS, $field['name'], $user[$field['name']]);
            echo '</p>';
          }
        ?>
      </div>
      <div class="rightopt">
        <?php
          foreach ($second as $field) {
            if (!isset($user[$field['name']])) $user[$field['name']] = $field['default'];
            if (!empty($field['label'])) echo '<label>'.$field['label'].' : </label>';
            echo '<p>';
            $this->matrix->displayField(self::TABLE_USERS, $field['name'], $user[$field['name']]);
            echo '</p>';
          }
        ?>
      </div>
      <div class="clear"></div>
    </div>
    
    <p><?php $this->matrix->displayField(self::TABLE_USERS, 'signature', $user['signature']); ?></p>
    
    <input type="submit" class="submit" name="save" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
  </form>

<!--scripts-->
  <script>
    $(document).ready(function(){
      $('#metadata_window').hide();
    }); // ready
  </script>