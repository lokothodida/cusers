<?php

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $array = array('channel' => array('smiley' => array()));
    foreach($_POST['code'] as $key => $smiley) {
      $array['channel']['smiley'][] = array('code' => $smiley, 'url' => $_POST['url'][$key]);
    }
    $xml = Array2XML::createXML('channel', $array['channel']);
    
    if ($xml->save($this->paths['data'].'smilies.xml')) {
    $this->matrix->getAdminError(i18n_r(self::FILE.'/UPDATE_SUCCESS'), true);
    }
    else {
      $this->matrix->getAdminError(i18n_r(self::FILE.'/UPDATE_ERROR'), false);
    }
  }
  $smilies = XML2Array::createArray(file_get_contents($this->directories['data']['core']['dir'].'smilies.xml'));

?>

<!--heading-->
<h3 class="floated"><?php echo i18n_r(self::FILE.'/SMILIES'); ?></h3>
<div class="edit-nav">
  <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
  <a href="#" class="add"><?php echo i18n_r(self::FILE.'/ADD'); ?></a>
  <div class="clear"></div>
</div>

<form method="post">
  <table class="highlight edittable">
    <thead>
      <tr>
        <th style="width: 1%;"><?php echo i18n_r(self::FILE.'/CODE'); ?></th>
        <th><?php echo i18n_r(self::FILE.'/URL'); ?></th>
        <th style="width: 10%;"></th>
        <th style="width: 5%;"></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($smilies['channel']['smiley'] as $smiley) { ?>
      <tr>
        <td><input type="text" class="text" name="code[]" style="width: 20px;" value="<?php echo $smiley['code']; ?>"></td>
        <td><input type="text" class="text" name="url[]" style="width: 100%;" value="<?php echo $smiley['url']; ?>"></td>
        <td style="text-align: center;"><img src="<?php echo $smiley['url']; ?>" /></td>
        <td style="text-align: right;"><a href="#" class="cancel delete">&times;</a></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <input type="submit" class="submit" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
</form>

<script>
  // add
  $('tbody').sortable();
  $(document).on('click', '.add', function(e){
    $('tbody').append(
      '<tr>' + 
        '<td><input type="text"" class="text" name="code[]" style="width: 20px;"></td>' + 
        '<td><input type="text"" class="text" name="url[]" style="width: 100%;"></td>' + 
        '<td style="text-align: center;"></td>' + 
        '<td style="text-align: right;"><a href="#" class="cancel delete">&times;</a></td>' + 
       '</tr>'
    );
    $('.tbody').sortable('destroy');
    $('.tbody').sortable();
    return false;
  });
  
  // delete
  $(document).on('click', '.delete', function(e){
    $(this).closest('tr').remove();
    return false;
  });
</script>
