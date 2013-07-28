<!--header-->
  <h3 class="floated"><?php echo i18n_r(self::FILE.'/PROFILE'); ?></h3>
  <div class="edit-nav">
    <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
    <div class="clear"></div>
  </div>
  
<!--comment template-->
  <form method="post">
    <textarea name="edit-template" class="codeeditor DM_codeeditor text" id="post-edit-template"><?php echo $template; ?></textarea>
    <?php
      // get codemirror script
      $matrix->initialiseCodeMirror();
      $matrix->instantiateCodeMirror('edit-template');
    ?>
    <input type="submit" class="submit" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>"/>
  </form>