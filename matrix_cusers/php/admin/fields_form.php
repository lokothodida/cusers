<tr>
  <td>
    <input class="hidden" name="oldname[]" value="<?php echo $field['name']; ?>">
    <input type="text" class="text" style="width: 80px; padding: 2px;" name="name[]" value="<?php echo $field['name']; ?>" required>
  </td>
  <td>
    <input type="text" class="text" style="width: 80px; padding: 2px;" name="label[]" value="<?php echo $field['label']; ?>" required>
  </td>
  <td>
    <select class="text type" style="width: 160px; padding: 2px;" name="type[]">
    <?php 
      $fieldTypes = $this->matrix->getFieldTypes();
      $noTypes = array('imageuploadadmin');
      foreach ($noTypes as $noType) {
        if (isset($fieldTypes[$noType])) unset($fieldTypes[$noType]);
      }
      foreach ($fieldTypes as $type => $properties) { ?>
       <option value="<?php echo $type; ?>" <?php if ($field['type'] == $type) echo 'selected="selected"'; ?>><?php echo $type; ?></option>
    <?php } ?>
    </select>
    
    <div class="showOptions dropdowncustom checkbox dropdownhierarchy imageuploadadmin">
      <p>
        <textarea class="text" style="width: 150px; height: 50px; padding: 2px; display: inline;" name="options[]"><?php echo $field['options']; ?></textarea>
      </p>
    </div>
    
    <div class="showOptions textmulti intmulti">
      <p><input class="text rows" style="width: 80px; padding: 2px;" name="rows[]" placeholder="" value="<?php echo $field['rows']; ?>"/></p>
    </div>
  </td>
  <td>
    <textarea class="text" style="width: 150px; height: 50px; padding: 2px; display: inline;" name="default[]"><?php echo $field['default']; ?></textarea>
  </td>
  <td>
    <select class="text" style="width: 80px; padding: 2px;" name="index[]" required>
      <option value="1" <?php if ($field['index']==1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES'); ?></option>
      <option value="0" <?php if ($field['index']==0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO'); ?></option>
    </select>
  </td>
  <td><a href="#" class="cancel removeField">&times;</a></td>
</tr>