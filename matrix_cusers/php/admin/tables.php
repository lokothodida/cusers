<?php
  $css = <<<EOF
<style>
  div.table {
    background:#fff;
	  border-bottom:1px solid #c8c8c8;
	  border-left:1px solid #e4e4e4;
	  border-right:1px solid #c8c8c8;
	  -moz-box-shadow: 2px 1px 10px rgba(0,0,0, .07);
	  -webkit-box-shadow: 2px 1px 10px rgba(0,0,0, .07);
	  box-shadow: 2px 1px 10px rgba(0,0,0, .07);
	  margin: 0 0 10px 0;
  }
  div.table table {
    width: 100%;
    border-collapse:separate;
    border-spacing: 1px;
  }
  div.table table .th1 {
    background: #6B94B4;
	  background: -moz-linear-gradient(top, #6B94B4 0%, #316594 100%);
	  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#6B94B4), color-stop(100%,#316594)); 
	  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6B94B4', endColorstr='#316594',GradientType=0 );
	  padding: 5px;
	  border-bottom:#2B5780 1px solid;
	  color: #fff;
  }
	div.table table .th1 a {
		text-decoration: none !important;
		color: #fff;
	}
  div.table table .th2 {
  }
  div.table table .td1 {
    padding: 5px;
    background: #FBFBFB;
  }
  div.table table .td2 {
    padding: 5px;
    background: #F1F1F1;
  }
  .page_navigation {
    margin: 0 0 10px 0;
  }
  .page_navigation span, .page_navigation a {
    padding: 5px;
    background: #FBFBFB;
    border-bottom:1px solid #c8c8c8;
    border-left:1px solid #e4e4e4;
    border-right:1px solid #c8c8c8;
    -moz-box-shadow: 2px 1px 10px rgba(0,0,0, .07);
    -webkit-box-shadow: 2px 1px 10px rgba(0,0,0, .07);
    box-shadow: 2px 1px 10px rgba(0,0,0, .07);
    margin: 0 0 10px 0;
  }
  .page_navigation .current, .page_navigation a:hover {
    background: #F1F1F1;
  }
  .page_navigation .current:hover {
    background: #FBFBFB;
  }
</style>
EOF;


  // main cusers table
    $tables[self::TABLE_USERS]['name'] = self::TABLE_USERS;
    $tables[self::TABLE_USERS]['oldname'] = 'CU_users';
    $levels = $this->levels;
    foreach ($levels as $key => $level) {
      $levels[$key] = '['.$key.']['.$level.']';
    }
    
    $tables[self::TABLE_USERS]['fields'] = array(
      array(
        'oldname' => 'user_display',
        'name' => 'displayname',
        'label' => i18n_r(self::FILE.'/DISPLAYNAME'),
        'required' => 'required',
        'placeholder' => i18n_r(self::FILE.'/DISPLAYNAME'),
        'type' => 'textlong',
        'index' => 1,
      ),
      array(
        'oldname' => 'user_name',
        'name' => 'username',
        'label' => i18n_r(self::FILE.'/USERNAME'),
        'type' => 'slug',
        'required' => 'required',
        'placeholder' => i18n_r(self::FILE.'/USERNAME'),
        'index' => 1,
        'class' => 'leftopt',
      ),
      array(
        'oldname' => 'user_pass',
        'name' => 'password',
        'label' => i18n_r(self::FILE.'/PASSWORD'),
        'type' => 'password',
        'required' => 'required',
        'tableview' => 0,
        'placeholder' => i18n_r(self::FILE.'/PASSWORD'),
        'class' => 'leftopt',
      ),
      array(
        'oldname' => 'user_level',
        'name' => 'level',
        'label' => i18n_r(self::FILE.'/LEVEL'),
        'type' => 'dropdowncustomkey',
        'options' => implode("\n", $levels),
        'default' => 0,
        'class' => 'leftopt',
      ),
      array(
        'oldname' => 'user_email',
        'name' => 'email',
        'label' => i18n_r(self::FILE.'/EMAIL'),
        'type' => 'email',
        'placeholder' => 'name@domain.com',
        'required' => 'required',
        'index' => 1,
        'class' => 'rightopt',
      ),
      array(
        'oldname' => 'user_avatar',
        'name' => 'avatar',
        'label' => i18n_r(self::FILE.'/AVATAR'),
        'type' => 'url',
        'placeholder' => 'http://',
        'class' => 'leftopt',
      ),
      array(
        'oldname' => 'user_date',
        'name' => 'registered',
        'label' => i18n_r(self::FILE.'/REGISTERED'),
        'type' => 'datetimelocal',
        'readonly' => 'readonly',
        'class' => 'rightopt',
      ),
      array(
        'oldname' => 'user_ip',
        'name' => 'ip',
        'label' => i18n_r(self::FILE.'/IP'),
        'type' => 'text',
        'readonly' => 'readonly',
        'class' => 'rightopt',
      ),
      array(
        'oldname' => 'user_signature',
        'name' => 'signature',
        'label' => i18n_r(self::FILE.'/SIGNATURE'),
        'type' => 'bbcodeeditor',
        'index' => 1,
      ),
    );
    $tables[self::TABLE_USERS]['maxrecords'] = 0;
    $tables[self::TABLE_USERS]['id'] = 0;
    $tables[self::TABLE_USERS]['records'] = array();
    $tables[self::TABLE_USERS]['records'][] = array(
      'displayname' => 'Administrator',
      'username' => 'cuadmin',
      'password' => 'password',
      'email' => 'admin@domain.com',
      'registered' => time(),
      'ip' => $this->getIP(),
      'level' => 2,
    );
    
    // cusers settings
    $tables[self::TABLE_CONFIG]['name'] = self::TABLE_CONFIG;
    $tables[self::TABLE_CONFIG]['oldname'] = 'CU_settings';
    $tables[self::TABLE_CONFIG]['fields'] = array(
      array(
        'oldname' => 'admin_email',
        'name' => 'admin-email',
        'label' => i18n_r(self::FILE.'/EMAIL'),
        'type' => 'email',
        'default' => 'name@domain.com',
        'placeholder' => 'name@domain.com',
        'required' => 'required',
        'class' => 'leftsec',
      ),
      array(
        'name' => 'date-format',
        'label' => i18n_r(self::FILE.'/DATE_FORMAT'),
        'type' => 'text',
        'default' => 'r',
        'placeholder' => i18n_r(self::FILE.'/DATE_FORMAT'),
        'required' => 'required',
        'class' => 'leftsec',
      ),
      array(
        'oldname' => 'ban_list',
        'name' => 'ban-list',
        'label' => i18n_r(self::FILE.'/BAN_LIST'),
        'type' => 'textarea',
        'class' => 'leftsec',
      ),
      array(
        'name' => 'salt',
        'label' => i18n_r(self::FILE.'/SALT'),
        'type' => 'text',
        'class' => 'leftsec',
      ),
      array(
        'oldname' => 'censor_list',
        'name' => 'censor-list',
        'label' => i18n_r(self::FILE.'/CENSOR_LIST'),
        'type' => 'textarea',
        'class' => 'rightsec',
      ),
      array(
        'name' => 'captcha',
        'label' => i18n_r(self::FILE.'/CAPTCHA'),
        'type' => 'dropdowncustomkey',
        'default' => 1,
        'options' => implode("\n", array(i18n_r(self::FILE.'/NO'), i18n_r(self::FILE.'/YES'))),
        'class' => 'rightsec',
      ),
      array(
        'name' => 'captcha-config',
        'type' => 'intmulti',
        'default' => implode("\n", array(5, 30, -10, 10, 40)),
        'labels' => implode("\n", array(i18n_r(self::FILE.'/LENGTH'), i18n_r(self::FILE.'/FONT_SIZE'), i18n_r(self::FILE.'/ANGLE'), i18n_r(self::FILE.'/X_AXIS'), i18n_r(self::FILE.'/Y_AXIS'))),
        'rows' => 5,
        'class' => 'rightsec',
      ),
      array(
        'name' => 'levels',
        'label' => i18n_r(self::FILE.'/LEVELS'),
        'type' => 'textarea',
        'default' => implode("\n", $levels),
        'class' => 'rightsec',
      ),
      array(
        'name' => 'header-css',
        'label' => i18n_r(self::FILE.'/CSS'),
        'type' => 'codeeditor',
        'default' => $css,
      ),
    );
    $tables[self::TABLE_CONFIG]['maxrecords'] = 1;
    $tables[self::TABLE_CONFIG]['id'] = 0;
    $tables[self::TABLE_CONFIG]['records'] = array();
    $tables[self::TABLE_CONFIG]['records'][] = array(
    );
    
?>