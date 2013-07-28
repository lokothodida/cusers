<?php

if ($_SERVER['REQUEST_METHOD']=='POST') {
  $update    = $this->matrix->buildFields(self::TABLE_USERS, $_POST);
  if ($update) $this->matrix->getAdminError('Fields updated successfully', true);
  else         $this->matrix->getAdminError('Fields not updated successfully', false);
} 

$coreFields = array('id', 'displayname', 'username', 'password', 'level', 'registered', 'email', 'ip', 'avatar', 'signature');
$fields = $this->matrix->getSchema(self::TABLE_USERS);
$newfields = $fields['fields'];
foreach ($coreFields as $field) {
  if (isset($newfields[$field])) unset($newfields[$field]);
}



?>

<h3 class="floated"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>&config"><?php echo i18n_r(self::FILE.'/CONFIG'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&fields" class="current"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&refresh"><?php echo i18n_r(self::FILE.'/REFRESH_INDEX'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&add"><?php echo i18n_r(self::FILE.'/USER_ADD'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>&template=profile"><?php echo i18n_r(self::FILE.'/PROFILE'); ?></a>
  <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/USERS'); ?></a>
  <div class="clear"></div>
</div>

<form method="post">
  <table class="edittable highlight">
    <thead>
      <th style="width: 20%;"><?php echo i18n_r(self::FILE.'/NAME'); ?></th>
      <th style="width: 20%;"><?php echo i18n_r(self::FILE.'/LABEL'); ?></th>
      <th style="width: 20%;"><?php echo i18n_r(self::FILE.'/TYPE'); ?></th>
      <th style="width: 20%;"><?php echo i18n_r(self::FILE.'/DEFAULT'); ?></th>
      <th style="width: 19%;"><?php echo i18n_r(self::FILE.'/INDEX'); ?></th>
      <th style="width: 1%;"></th>
    </thead>
    <tbody class="sortable">
    <?php foreach ($newfields as $field) include(GSPLUGINPATH.self::FILE.'/php/admin/fields_form.php'); ?>
    <tr class="addField">
      <td colspan="100%"><a href="">Add Field</a></td>
    </tr>
    </tbody>
  </table>
  <input type="submit" name="submit" class="submit" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
</form>

<style>
  tbody.sortable td div { display: none; }
</style>
<script>
    $('.sortable').sortable({items:':not(.addField td)'});
    $(document).ready(function() {
      // sortable
      $('.sortable').sortable();
      
    <?php
      // load 'add field' content into variable
      $content = '';
      ob_start(); // output buffering
      $field = array();
      $field['name'] = $field['oldname'] = $field['label'] = $field['default'] = $field['options'] = $field['rows'] = '';
      $field['type'] = 'text';
      $field['index'] = 0;
      include(GSPLUGINPATH.self::FILE.'/php/admin/fields_form.php');
      $content = ob_get_contents(); // loads content from buffer
      ob_end_clean(); // ends output buffering
      
      // return the content
    ?>
    
      $('.addField').click(function() {
        $(<?php echo json_encode($content); ?>).insertBefore('table .sortable .addField');
        //$('table .sortable .addField').insertBefore(<?php echo json_encode($content); ?>);
        $('.sortable').sortable('destroy');
        $('.sortable').sortable();
        return false;
      });
      
      $(document).on('click', '.removeField', function(e){
        $(this).closest('tr').remove();
        return false;
      });
      
    }); // ready

    // extra options dependent on field type
    $(document).ready(function() {
      $('.type').each(function() {
        $this = $(this);
        $this.closest('td').find('.showOptions').hide();
        $this.closest('td').find('div.' + $this.val()).stop().show();
      });
    }); // ready
    
    $('body').on('change', '.type', function() {
      $this = $(this);
      $this.closest('td').find('.showOptions').not('.' + $this.val()).stop().slideUp('fast');
      $this.closest('td').find('div.' + $this.val()).slideDown('fast');
    });
  </script>