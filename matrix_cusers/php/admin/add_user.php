<!--heading-->
<h3 class="floated"><?php echo i18n_r(self::FILE.'/USER_ADD'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
  <a href="#" id="metadata_toggle"><?php echo i18n_r(self::FILE.'/OPTIONS'); ?></a>
  <div class="clear"></div>
</div>

<!--form-->
  <form method="post" action="load.php?id=<?php echo self::FILE;?>">
    <p><?php $this->matrix->displayField(self::TABLE_USERS, 'displayname'); ?></p>
    
    <div class="leftsec">
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'username'); ?></p>
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'password'); ?></p>
      <p><input type="password" class="text" name="confirm" placeholder="<?php echo i18n_r(self::FILE.'/CONFIRM'); ?>" required></p>
    </div>
    
    <div class="rightsec">
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'avatar'); ?></p>
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'email'); ?></p>
      <p><?php $this->matrix->displayField(self::TABLE_USERS, 'level'); ?></p>
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
            if (!empty($field['label'])) echo '<label>'.$field['label'].' : </label>';
            echo '<p>';
            $this->matrix->displayField(self::TABLE_USERS, $field['name']);
            echo '</p>';
          }
        ?>
      </div>
      <div class="rightopt">
        <?php
          foreach ($second as $field) {
            if (!empty($field['label'])) echo '<label>'.$field['label'].' : </label>';
            echo '<p>';
            $this->matrix->displayField(self::TABLE_USERS, $field['name']);
            echo '</p>';
          }
        ?>
      </div>
      <div class="clear"></div>
    </div>
    
    <p><?php $this->matrix->displayField(self::TABLE_USERS, 'signature'); ?></p>
    
    <input type="submit" class="submit" name="add" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
  </form>

<!--scripts-->
  <script>
    $(document).ready(function(){
      $('#metadata_window').hide();
    }); // ready
  </script>