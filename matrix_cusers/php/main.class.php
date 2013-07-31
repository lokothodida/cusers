<?php

class MatrixCUsers {
  /* constants */
  const FILE         = 'matrix_cusers';
  const ID           = 'cusers';
  const VERSION      =  '1.01';
  const AUTHOR       = 'Lawrence Okoth-Odida';
  const PAGE         = 'plugins';
  const URL          = 'http://lokida.co.uk/';
  const TABLE_USERS  = 'cusers';
  const TABLE_CONFIG = 'cusers-config';
  const SEARCHID     = 'cuser:';
  
  /* properties */
  public  $users;
  public  $parser;
  private $uri;
  private $slug;
  private $user;
  private $template;
  private $config;
  private $usersSchema;
  private $configSchema;
  private $levels;
  private $matrix;
  private $imgExtensions;
  private $pluginPath;
  private $paths;
  
  /* methods */
  # constructor
  public function __construct() {
    // plugin details
    $this->plugin = array();
    $this->plugin['id']          = self::FILE;
    $this->plugin['name']        = i18n_r(self::FILE.'/PLUGIN_TITLE');
    $this->plugin['version']     = self::VERSION;
    $this->plugin['author']      = self::AUTHOR;
    $this->plugin['url']         = self::URL;
    $this->plugin['description'] = i18n_r(self::FILE.'/PLUGIN_DESC');
    $this->plugin['page']        = self::PAGE;
    $this->plugin['sidebar']     = i18n_r(self::FILE.'/PLUGIN_SIDEBAR');
    
    // check for dependencies
    if ($this->checkDependencies()) {
      $this->matrix = new TheMatrix;
      $this->parser = new TheMatrixParser;
      $this->imgExtensions = array('png', 'jpg', 'jpeg', 'bmp', 'gif');
      
      // directories
      $this->directories = array();
      $this->directories['plugins']['core']    = array('dir' => GSPLUGINPATH.self::FILE.'/');
      $this->directories['plugins']['php']     = array('dir' => GSPLUGINPATH.self::FILE.'/php/');
      $this->directories['plugins']['img']     = array('dir' => GSPLUGINPATH.self::FILE.'/img/');
      $this->directories['plugins']['captcha'] = array('dir' => GSPLUGINPATH.self::FILE.'/img/captcha/');
      $this->directories['plugins']['css']     = array('dir' => GSPLUGINPATH.self::FILE.'/css/');
      $this->directories['plugins']['js']      = array('dir' => GSPLUGINPATH.self::FILE.'/js/');
      $this->directories['plugins']['font']    = array('dir' => GSPLUGINPATH.self::FILE.'/font/');
      $this->directories['data']['core']       = array('dir' => GSDATAOTHERPATH.self::ID.'/');
      $this->directories['data']['smilies']    = array('dir' => GSDATAOTHERPATH.self::ID.'/smilies/', 'htaccess' => 'allow', 'zip' => $this->directories['plugins']['img']['dir'].'smilies.zip');
      $this->directories['data']['captcha']    = array('dir' => GSDATAOTHERPATH.self::ID.'/captcha/', 'htaccess' => 'allow');
      $this->directories['data']['templates']  = array('dir' => GSDATAOTHERPATH.self::ID.'/templates/');
      $this->mkdir($this->directories['data']);
      
      // smilies file
      if (!file_exists($this->directories['data']['core']['dir'].'smilies.xml')) $this->createSmilies();
      
      // user levels
      $this->levels = array(
        i18n_r(self::FILE.'/MEMBER'),
        i18n_r(self::FILE.'/MODERATOR'),
        i18n_r(self::FILE.'/ADMINISTRATOR'),
        i18n_r(self::FILE.'/BANNED'),
        i18n_r(self::FILE.'/INACTIVE'),
      );
      
      // users
      $this->users = $this->getUsers();
      
      // template
      #$this->template = GSDATAOTHERPATH.self::FILE.'_profile_template.xml';
      $this->template = $this->directories['data']['templates']['dir'].'profiles.xml';
      
      // create tables
      $this->createTables();
      
      // initialize configuration
      $config = $this->matrix->query('SELECT * FROM '.self::TABLE_CONFIG, 'SINGLE');
      
      $this->usersSchema  = $this->matrix->getSchema(self::TABLE_USERS);
      $this->configSchema = $this->matrix->getSchema(self::TABLE_CONFIG);
      $this->coreFields = array('id', 'displayname', 'username', 'password', 'level', 'registered', 'email', 'ip', 'avatar', 'signature');
      $this->config = array();
      $this->config['profiles-slug'] = 'profiles';
      $this->config['edit-profile-slug'] = 'edit';
      $this->config['levels'] = $this->matrix->explodeTrim("\n", $config['levels']);
      $this->config['date-format'] = $config['date-format'];
      $this->config['ban-list'] = $this->matrix->explodeTrim("\n", $config['ban-list']);
      $this->config['header-css'] = $config['header-css'];
      $this->config['salt'] = $config['salt'];
      $this->config['captcha'] = (bool) $config['captcha'];
      $this->config['captcha-config'] = $this->matrix->explodeTrim("\n", $config['captcha-config']);
      $this->matrix->salt($config['salt']);
      
      // uri
      
      
      // url structures
      if ($this->matrix->getPrettyURLs()) {
        $this->config['profiles-url']      = $this->matrix->getSiteURL().$this->config['profiles-slug'].'/';
        $this->config['profile-url']       = $this->matrix->getSiteURL().$this->config['profiles-slug'].'/$user/';
        $this->config['edit-profile-url']  = $this->matrix->getSiteURL().$this->config['profiles-slug'].'/$user/edit/';
      }
      else {
        $this->config['profiles-url']      = $this->matrix->getSiteURL().'index.php?id='.$this->config['profiles-slug'];
        $this->config['profile-url']       = $this->matrix->getSiteURL().'index.php?id='.$this->config['profiles-slug'].'&user=$user';
        $this->config['edit-profile-url']  = $this->matrix->getSiteURL().'index.php?id='.$this->config['profiles-slug'].'&user=$user&edit=edit';
      }
      
      // ip ban
      $this->ipBan($this->getIP());
    }
  }
  
  # get plugin info
  public function pluginInfo($info) {
    if (isset($this->plugin[$info])) {
      return $this->plugin[$info];
    }
    else return null;
  }
  
  # check dependencies
  private function checkDependencies() {
    if (
      (class_exists('TheMatrix') && TheMatrix::VERSION >= '1.02') &&
      function_exists('i18n_init') && 
      function_exists('get_i18n_search_results')
    ) return true;
    else return false;
  }
  
  # missing dependencies
  private function missingDependencies() {
    $dependencies = array();
  
    if (!class_exists('TheMatrix') || (class_exists('TheMatrix') && TheMatrix::VERSION < '1.02')) {
      $dependencies[] = array('name' => 'The Matrix (1.02+)', 'url' => 'https://github.com/n00dles/DM_matrix/');
    }
    if (!function_exists('i18n_init')) {
      $dependencies[] = array('name' => 'I18N (3.2.3+)', 'url' => 'http://get-simple.info/extend/plugin/i18n/69/');
    }
    if (!function_exists('get_i18n_search_results')) {
      $dependencies[] = array('name' => 'I18N Search (2.11+)', 'url' => 'http://get-simple.info/extend/plugin/i18n-search/82/');
    }
   
    return $dependencies;
  }
  
  # get config
  public function getConfig($key = null) {
    if (isset($this->config[$key])) return $this->config[$key];
    else return $this->config;
  }
  
  # get users
  public function getUsers($key=false) {
    $array = array();
    $users = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS, 'MULTI', $cache=false, $key);
    foreach ($users as $user) {
      unset($user['password']);
      $array[$user['id']] = $user;
    }
    $array['-1'] = array(
      'id' => -1,
      'username' => strtolower(i18n_r(self::FILE.'/GUEST')),
      'displayname' => i18n_r(self::FILE.'/GUEST'),
      'avatar' => '',
    );
    
    // return
    return $array;
  }
  
  # return the main users array
  public function returnUsers() {
    return $this->users;
  }
  
  # parse out the uri
  public function parseURI() {
    // load essential globals for changing the 404 error messages
    global $id, $uri, $data_index;
    
    // parse uri
    $tmpuri = trim(str_replace('index.php', '', $_SERVER['REQUEST_URI']), '/#');
    $tmpuri = str_replace('?id=', '', $tmpuri);
    $tmpuri = preg_split('#(&|\?|\/&|\/\?)#', $tmpuri);
    $tmpuri = reset($tmpuri);
    $tmpuri = explode('/', $tmpuri);
    $slug = end($tmpuri);
    $this->slug = $slug;
    
    // fix slug for pretty urls
    if (!$this->matrix->getPrettyURLS()) {
      end($_GET);
      if (key($_GET) == 'page') prev($_GET);
      $this->slug = current($_GET);
      foreach ($_GET as $get) {
        if (!in_array($get, $tmpuri)) $tmpuri[] = $get;
      }
    }
    
    $this->uri = $tmpuri;
    return $tmpuri;
  }
  
  # salt and sha1
  public function saltSha1($string) {
    return $this->config['salt'].sha1($string);
  }
  
  # change salted passwords
  public function newSalt($salt) {
    $success = array();
    
    // loop through each user and take the last 40 characters and prepend new salt
    $users = glob(GSDATAOTHERPATH.'matrix/'.self::TABLE_USERS.'/*.xml');
    foreach ($users as $user) {
      $array = XML2Array::createArray(file_get_contents($user));
      if (isset($array['channel']['item']['password']['@cdata'])) {
        $array['channel']['item']['password']['@cdata'] = $salt.substr($array['channel']['item']['password']['@cdata'], -40, 40);
      }
      else {
        $array['channel']['item']['password'] = $salt.substr($array['channel']['item']['password'], -40, 40);
      }
      $xml = Array2XML::createXML('channel', $array['channel']);
      $success[] = $xml->save($user);
    }
    
    if (!in_array(false, $success)) return true;
    else return false;
  }
  
  # page type
  public function pageType() {
    $return = null;
    // main profiles page
    if ($this->slug == $this->config['profiles-slug']) {
      $return = 'profiles';
    }
    // user's profile
    elseif (
      in_array($this->config['profiles-slug'], $this->uri) ||
      isset($_GET[$this->config['profiles-slug']])
    ) {
      $this->user = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE username = "'.$this->slug.'"', 'SINGLE');
      if (empty($this->user)) {
        $slug = array_slice($this->uri, -2, 1);
        $slug = current($slug);
        $this->user = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE username = "'.$slug.'"', 'SINGLE');
      }
      if ($this->user) {
        if (in_array($this->config['edit-profile-slug'], $this->uri)) {
          $return = 'edit-profile';
        }
        else $return = 'profile';
      }
    }
    return $return;
  }
  
  # get IP address of user
  public function getIP() {
    if (isset($_SERVER['HTTP_CLIENT_IP']))            return trim($_SERVER['HTTP_CLIENT_IP']);
    elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))   return trim($_SERVER['HTTP_X_FORWARDED_FOR']);
    elseif(isset($_SERVER['HTTP_X_FORWARDED']))       return trim($_SERVER['HTTP_X_FORWARDED']);
    elseif(isset($_SERVER['HTTP_FORWARDED_FOR']))     return trim($_SERVER['HTTP_FORWARDED_FOR']);
    elseif(isset($_SERVER['HTTP_FORWARDED']))         return trim($_SERVER['HTTP_FORWARDED']);
    elseif(isset($_SERVER['REMOTE_ADDR']))            return trim($_SERVER['REMOTE_ADDR']);
    else                                              return 'n/a';
  }
  
  # build data directories
  public function mkdir($dirs=array()) {
    if (is_array($dirs)) {
      $return = array();
      foreach ($dirs as $dir) {
        // default permissions
        if (!isset($dir['perm'])) $dir['perm'] = '0755';
        
        // make directories
        if (!file_exists($dir['dir'])) {
          $return[] = mkdir($dir['dir'], $dir['perm']);
        
          // htaccess files
          if (isset($dir['htaccess'])) {
            if     ($dir['htaccess'] == 'allow') $return[] = file_put_contents($dir['dir'].'.htaccess', 'Allow from all');
            elseif ($dir['htaccess'] == 'deny')  $return[] = file_put_contents($dir['dir'].'.htaccess', 'Deny from all');
          }
          
          // unzip files
          if (isset($dir['zip']) && file_exists($dir['zip'])) {
            $zip = new ZipArchive;
            if ($zip->open($dir['zip']) === TRUE) {
              echo 'true';
              $return[] = $zip->extractTo($dir['dir']);
              $zip->close();
            }
            else $return[] = false;
          }
        }
      }
      if (!in_array(false, $return)) return true;
      else return false;
    }
    else return false;
  }
  
  # create multiple tables (with backwards-compatibility)
  public function buildSchema($tables=array()) {
    foreach ($tables as $name => $table) {
      $table['name'] = trim($name);
      if (!$this->matrix->tableExists($table['name'])) {
        // reconfigure existing tables (compatibility)
        if (isset($table['oldname']) && $this->matrix->tableExists($table['oldname'])) {
          $this->matrix->renameTable($table['oldname'], $table['name']);
        }

        // create table
        $this->matrix->createTable($table['name'], $table['fields'], $table['maxrecords'], $table['id']);
      }
      
      // fixing existing fields
      $schema = $this->matrix->getSchema($table['name']);
      foreach ($table['fields'] as $field) {
        if (isset($field['oldname']) && isset($schema['fields'][$field['oldname']])) {
          $this->matrix->renameField($table['name'], $field['oldname'], $field['name']);
          $schema['fields'][$field['name']] = $field;
          if (isset($schema['fields'][$field['oldname']])) {
            unset($schema['fields'][$field['oldname']]);
          }
          $this->matrix->modSchema($table['name'], $schema);
        }
      }
      
      // missing fields
      foreach ($table['fields'] as $field) {
        if (!$this->matrix->fieldExists($table['name'], $field['name'])) {
          $this->matrix->createField($table['name'], $field);
        }
      }
	  
      // default records
      if (!empty($table['records']) && !$this->matrix->recordExists($table['name'], 0) && $this->matrix->getNextRecord($table['name']) == 0) {
        foreach ($table['records'] as $record) {
          $this->matrix->createRecord($table['name'], $record);
        }
      }
    }
  }
  
  # create tables
  public function createTables() {
    $tables = array(self::TABLE_USERS => array(), self::TABLE_CONFIG =>array());
    
    include($this->directories['plugins']['php']['dir'].'admin/tables.php');
    
    foreach ($tables as $table) {
      if (!$this->matrix->tableExists($table['name'])) {
        // reconfigure existing tables (compatibility)
        if ($this->matrix->tableExists($table['oldname'])) $this->matrix->renameTable($table['oldname'], $table['name']);

        // create table
        $this->matrix->createTable($table['name'], $table['fields'], $table['maxrecords'], $table['id']);
      }
      
      // fixing existing fields
      $schema = $this->matrix->getSchema($table['name']);
      foreach ($table['fields'] as $field) {
        if (isset($field['oldname']) && isset($schema['fields'][$field['oldname']])) {
          $this->matrix->renameField($table['name'], $field['oldname'], $field['name']);
          $schema['fields'][$field['name']] = $field;
          if (isset($schema['fields'][$field['oldname']])) {
            unset($schema['fields'][$field['oldname']]);
          }
          $this->matrix->modSchema($table['name'], $schema);
        }
      }
      
      // missing fields
      foreach ($table['fields'] as $field) {
        if (!$this->matrix->fieldExists($table['name'], $field['name'])) {
          $this->matrix->createField($table['name'], $field);
        }
      }
	  
      // default records
      if (!empty($table['records']) && !$this->matrix->recordExists($table['name'], 0) && $this->matrix->getNextRecord($table['name']) == 0) {
        foreach ($table['records'] as $record) {
          $this->matrix->createRecord($table['name'], $record);
        }
      }
    }
    
    // special config options
    $config = $this->matrix->recordExists(self::TABLE_CONFIG, 0);
    if ($config) {
      if (count($config) != (count($tables[self::TABLE_CONFIG]['fields']) + 1)) {
        $this->matrix->updateRecord(self::TABLE_CONFIG, 0, $config);
      }
    }
    
    // create template files
    if (!file_exists($this->template)) {
      $templates = array();
      foreach (glob(GSPLUGINPATH.self::FILE.'/php/display/*.php') as $template) {
        $name = explode('/', $template);
        $name = end($name);
        $name = trim(str_replace('.php', '', $name));
        $templates[$name]['@cdata'] = file_get_contents($template);
      }
      $xml = Array2XML::createXML('channel', $templates);
      $xml->save($this->template);
    }
    
    // fix compatibility issues with existing cusers tables
    
  }
  
  # random string (for random passwords, etc...)
  public function randomString($length = 10) {
    // thanks to Pr07o7yp3 on StackOverflow @ http://stackoverflow.com/questions/4356289/php-random-string-generator
    
    // character base
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    // new string
    $string = '';
    for ($i = 0; $i < $length; $i++) {
      $string .= $chars;
      $string = str_shuffle($string);
    }
    
    // return finally shuffled random string
    return substr($string, 0, $length);
  }
  
  # include template
  public function includeTemplate($template, $vars=array()) {
    $file = XML2Array::createArray(file_get_contents($this->template));
    if (isset($file['channel'][$template]['@cdata'])) {
      foreach ($vars as $key => $var) {
        ${$key} = $var;
      }
      eval("?>".$file['channel'][$template]['@cdata']);
    }
  }
  
  # validation
  public function validate($type) {
    $return = null; 
    $query = array();
    if (!empty($_POST)) {
      foreach ($_POST as $key => $post) {
        $key = str_replace('post-', '', $key);
        ${$key} = $post;
        $query[$key] = $post;
      }
      
      // captcha
      $checkCaptcha = true;
      if (isset($captcha)) {
        if ($captcha != $_SESSION['captcha']) $checkCaptcha = false;
      }
      
      // slugify username
      if (!isset($displayname) && isset($username)) $displayname = $username;
      if (isset($username)) $username = $this->matrix->str2slug($username);
    }
    if ($type=='register') {
      include(GSPLUGINPATH.self::FILE.'/php/validate/register.php');
    }
    elseif ($type=='login') {
      include(GSPLUGINPATH.self::FILE.'/php/validate/login.php');
    }
    elseif ($type=='edit-profile') {
      include(GSPLUGINPATH.self::FILE.'/php/validate/edit_profile.php');
    }
    elseif ($type=='forgot-password') {
      include(GSPLUGINPATH.self::FILE.'/php/validate/forgot_password.php');
    }
    
    return $return;
  }
  
  # login
  public function login($id) {
    if (is_numeric($id)) {
      $query = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE id = '.$id, 'SINGLE');
    }
    else $query = $id;
    
    // log in
    $_SESSION['cuser'] = array(
      'id' => $query['id'],
      'username' => $query['username'],
      'displayname' => $query['displayname'],
      'level' => $query['level'],
    );
  }
  
  # check if user is mod
  public function isMod($user) {
    if ($user['level'] == 2 || $user['level'] == 1) {
      return true;
    }
    else return false;
  }
  
  # check if user is admin
  public function isAdmin($user) {
    if ($user['level'] == 2) {
      return true;
    }
    else return false;
  }
  
  # check if a user is banned or inactive
  public function isBanned($user) {
    if ($user['level'] == 3 || $user['level'] == 4 || in_array($user['ip'], $this->config['ban-list'])) {
      return true;
    }
    else return false;
  }
  
  # ban
  public function ipBan($ip) {
    if (in_array($ip, $this->config['ban-list']) && $this->loggedIn()) {
      $this->logout();
    }
  }
  
  # logout
  public function logout() {
    if (isset($_SESSION['cuser'])) {
      unset($_SESSION['cuser']);
    }
  }
  
  # logged in
  public function loggedIn() {
    if (isset($_SESSION['cuser'])) {
      return true;
    }
    else return false;
  }
  
  # edit permissions
  public function editPermissions($user) {
    i18n_init();
    if (
      $this->loggedIn() && 
      ($_SESSION['cuser']['id'] == $user['id'] || $_SESSION['cuser']['level'] == 2 || $_SESSION['cuser']['level'] == 1)
    ) return true;
    else return false;
  }
  
  # forms
  public function displayForm($type='login') {
    ob_start();
    if ($type=='register') {
      include(GSPLUGINPATH.self::FILE.'/php/display/register.php');
    }
    elseif ($type=='login') {
      include(GSPLUGINPATH.self::FILE.'/php/display/login.php');
    }
    elseif ($type=='forgot-password') {
      include(GSPLUGINPATH.self::FILE.'/php/display/forgot_password.php');
    }
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
  
  # create smilies
  public function createSmilies() {
    $path = str_replace(GSROOTPATH, $this->matrix->getSiteURL(), $this->directories['data']['smilies']['dir']);
    $smilies = array();
    $smilies[':-)'] = $path.'smile.png';
    $smilies['>:D'] = $path.'grin.png';
    $smilies[':-D'] = $path.'happy.png';
    $smilies['XD']  = $path.'evilgrin.png';
    $smilies[':-('] = $path.'unhappy.png';
    $smilies[':-P'] = $path.'tongue.png';
    $smilies[':-3'] = $path.'waii.png';
    $smilies[';-)'] = $path.'wink.png';
    $smilies[':-o'] = $path.'surprised.png';
    $array = array();
    foreach ($smilies as $code => $url) {
      $array['smiley'][] = array('code' => $code, 'url' => $url);
    }
    $xml = Array2XML::createXML('channel', $array);
    return $xml->save($this->directories['data']['core']['dir'].'smilies.xml');
  }
  
  # get smilies
  public function getSmilies() {
    $smilies = XML2Array::createArray(file_get_contents($this->directories['data']['core']['dir'].'smilies.xml'));
    $array = array('codes' => array(), 'urls' => array());
    foreach ($smilies['channel']['smiley'] as $smiley) {
      $array['codes'][] = $smiley['code'];
      $array['urls'][] = '<img class="smiley" src="'.$smiley['url'].'"/>';
    }
    return $array;
  }
  
  # get profile url
  public function getProfileURL($username='') {
    if ($username == '') {
      return $this->config['profiles-url'];
    }
    else {
      return str_replace('$user', $username, $this->config['profile-url']);
    }
  }
  
  # get edit profile url
  public function getEditProfileURL($username) {
    return str_replace('$user', $username, $this->config['edit-profile-url']);
  }
  
  # get gravatar url (taken from gravatar site)
  public function getGravatar($email, $s = 80, $d = 'mm', $r = 'g', $img=false, $atts=array()) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
      $url = '<img src="' . $url . '"';
      foreach ( $atts as $key => $val )
        $url .= ' ' . $key . '="' . $val . '"';
      $url .= ' />';
    }
    return $url;
  }
  
  # display avatar (or gravatar)
  public function displayAvatar($user, $size = 100) {
    $output = null;
    if (is_array($user) && isset($user['email']) && isset($user['avatar'])) {
      $user['avatar'] = trim($user['avatar']);
      $ext = explode('.', $user['avatar']);
      $ext = end($ext);
      if (!empty($user['avatar']) && in_array(strtolower($ext), $this->imgExtensions)) {
        $output = '<img src="'.$user['avatar'].'" class="avatar" style="height: auto; width: auto; max-width: '.$size.'px; max-height: '.$size.'px;"/>';
      }
      else {
        $output = '<img src="'.$this->getGravatar($user['email'], $size).'" class="avatar" style="height: auto; width: auto; max-width: '.$size.'px; max-height: '.$size.'px;"/>';
      }
    }
    return $output;
  }
  
  # display captcha form
  public function displayCaptchaForm($clear=true) {
    $captcha = new SimpleCaptcha(
      $this->config['captcha-config'][0], 
      $fontConfig=array($this->config['captcha-config'][1], $this->config['captcha-config'][2], $this->config['captcha-config'][3], $this->config['captcha-config'][4], '#000'), 
      $this->directories['plugins']['font']['dir'], 
      $this->directories['data']['captcha']['dir'], 
      $this->directories['plugins']['captcha']['dir'],
      str_replace(GSROOTPATH, $this->matrix->getSiteURL(), $this->directories['data']['captcha']['dir'])
    );
    
    if ($clear) $captcha->clear();
    ?>
    <p><img src="<?php echo $captcha->image(); ?>" class="captcha"></p>
    <p><input type="text" name="captcha" required></p>
    <?php
  }
  
  # display user profile
  public function displayUserProfile() {
    global $data_index;
    
    // metadata
    $data_index->title    = $this->user['displayname'].' '.i18n_r(self::FILE.'/PROFILE');
    $data_index->date     = time();
    $data_index->metak    = '';
    $data_index->meta     = '';
    $data_index->url      = $this->slug;
    $data_index->parent   = '';
    $data_index->template = 'template.php';
    $data_index->private  = '';
    
    $user = $this->user;
    $user['avatar-display'] = $this->displayAvatar($user);
    $user['signature'] = $this->bbcode($user['signature']);
    
    // content
    ob_start();
    echo $this->config['header-css'];
    $this->includeTemplate('profile', array('user' => $user));
    ?>
    <div class="edit-profile">
      <?php if ($this->editPermissions($this->user)) { ?>
      <a href="<?php echo $this->getEditProfileURL($this->user['username']); ?>"><?php echo i18n_r(self::FILE.'/MANAGE_USER'); ?></a> | 
      <?php } ?>
      <a href="<?php echo $this->getProfileURL(); ?>"><?php echo i18n_r(self::FILE.'/PROFILES'); ?></a>
      
    </div>
    <?php
    $data_index->content = ob_get_contents();
    ob_end_clean();
  }
  
  # display user list
  public function displayUserList() {
    global $data_index;
    
    // load users
    $users = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS);
    
    // metadata
    $data_index->title    = i18n_r(self::FILE.'/USERS');
    $data_index->date     = time();
    $data_index->metak    = '';
    $data_index->meta     = '';
    $data_index->url      = $this->slug;
    $data_index->parent   = '';
    $data_index->template = 'template.php';
    $data_index->private  = '';
    
    // content
    ob_start();
    ?>
    <?php echo $this->config['header-css']; ?>
    <div class="pajinate">
      <div class="page_navigation"></div>
      <div class="tableWrap">
        <table>
          <thead>
            <tr>
              <th class="sort head1" data-sort="displayname"><?php echo i18n_r(self::FILE.'/DISPLAYNAME'); ?></th>
              <th class="sort head1" data-sort="username"><?php echo i18n_r(self::FILE.'/USERNAME'); ?></th>
              <th class="sort head1" data-sort="email"><?php echo i18n_r(self::FILE.'/EMAIL'); ?></th>
              <th class="sort head1" data-sort="email"><?php echo i18n_r(self::FILE.'/AVATAR'); ?></th>
            </tr>
          </thead>
          <tbody class="content">
            <?php foreach ($users as $user) { ?>
            <tr data-displayname="<?php echo $user['displayname']; ?>" data-username="<?php echo $user['username']; ?>" data-email="<?php echo $user['email']; ?>">  
              <td class="row1"><a href="<?php echo $this->getProfileURL($user['username']); ?>"><?php echo $user['displayname']; ?></a></td>
              <td class="row2"><?php echo $user['username']; ?></td>
              <td class="row1"><a href="malto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></td>
              <td class="row2" style="text-align: center;"><?php echo $this->displayAvatar($user, 25); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div style="overflow: hidden;">
        <div class="page_navigation" style="overflow: hidden; float: left;"></div>
        <select class="maxUsers" style="float: right;">
          <option value="1">--</option>
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="20">20</option>
        </select>
      </div>
    </div>
    <script>
    $(document).ready(function() {
      // pajination settings
      var pajinateSettings = {
        'items_per_page'  : 10,
        'nav_label_first' : '|&lt;&lt;', 
        'nav_label_prev'  : '&lt;', 
        'nav_label_next'  : '&gt;', 
        'nav_label_last'  : '&gt;&gt;|', 
      };
      
      // pajination
      $('.pajinate').pajinate(pajinateSettings);
      
      // change max number of records
      $('.maxUsers').change(function(){
        pajinateSettings['items_per_page'] = $(this).val();
        $('table').pajinate(pajinateSettings);
      }); // change
      
      // table sorting
      $('table thead .sort').toggle(
        function() {
          $('table').pajinate({'items_per_page': 9999});
          $('table tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'asc'});
          $('table').pajinate(pajinateSettings);
        },
        function () {
          $('table').pajinate({'items_per_page': 9999});
          $('table tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'desc'});
          $('table').pajinate(pajinateSettings);
        }
      ); // toggle
    }); // ready
  </script>
    <?php
    $data_index->content = ob_get_contents();
    ob_end_clean();
  }
  
  # display edit profile page
  public function displayEditProfile($user) {
    global $data_index;
    
    // content
    if ($this->editPermissions($user)) {
      ob_start();
      
      if (!empty($_POST['save'])) {
        $_POST['username'] = $user['username'];
        $update = $this->validate('edit-profile');
        
        // success message
        if ($update) {
          $msg['status'] = 'success';
          $msg['msg'] = i18n_r(self::FILE.'/USER_UPDATESUCCESS');
        }
        
        // error message
        else {
          $msg['status'] = 'error';
          $msg['msg'] = i18n_r(self::FILE.'/USER_UPDATEERROR');
        }
        
        // refresh the index to reflect the changes
        $this->matrix->refreshIndex();
        ?>
        <div class="<?php echo $msg['status']?>"><?php echo $msg['msg']?></div>
        <?php
      }
      
      $user = $this->matrix->query('SELECT * FROM '.self::TABLE_USERS.' WHERE username = "'.$user['username'].'"', 'SINGLE');
      
      
      // metadata
      $data_index->title    = i18n_r(self::FILE.'/MANAGE_USER').' ('.$user['displayname'].')';
      $data_index->date     = time();
      $data_index->metak    = '';
      $data_index->meta     = '';
      $data_index->url      = $this->slug;
      $data_index->parent   = '';
      $data_index->template = 'template.php';
      $data_index->private  = '';
      
      
      ?>
      <?php echo $this->config['header-css']; ?>
      <form method="post">
      <div class="tableWrap"> 
        <table>
          <thead>
            <tr>
              <th class="head1" colspan="100%"><?php echo i18n_r(self::FILE.'/CORE'); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/USERNAME'); ?></th>
              <td class="row1" style="width: 80%;"><input type="text" class="text" readonly value="<?php echo $user['username']; ?>"/></td>
            </tr>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/EMAIL'); ?></th>
              <td class="row1" style="width: 80%;"><?php $this->matrix->displayField(self::TABLE_USERS, 'email', $user['email']); ?></td>
            </tr>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/PASSWORD'); ?></th>
              <td class="row1" style="width: 80%;"><input type="password" class="text" name="password" placeholder="<?php echo i18n_r(self::FILE.'/PASSWORD'); ?>"></td>
            </tr>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/CONFIRM'); ?></th>
              <td class="row1" style="width: 80%;"><input type="password" class="text" name="confirm" placeholder="<?php echo i18n_r(self::FILE.'/CONFIRM'); ?>"></td>
            </tr>
          <?php
            if ($user['id'] != 0) {
              if ($_SESSION['cuser']['level'] == 2) {
          ?>
          
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/LEVEL'); ?></th>
              <td class="row1" style="width: 80%;"><p><?php $this->matrix->displayField(self::TABLE_USERS, 'level', $user['level']); ?></p></td>
            </tr>
          
          
          <?php
              }
              if ($_SESSION['cuser']['level'] == 1 && $user['level'] != 2 && $user['level'] != 1) {
                $levels = $this->config['levels'];
                unset($levels[1]);
                unset($levels[2]);
          ?>
          
          <tr>
            <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/LEVEL'); ?></th>
            <td class="row1" style="width: 80%;">
              <select name="level">
                <?php foreach ($levels as $key => $level) { ?>
                <option value="<?php echo $key; ?>" <?php if ($user['level'] == $key) echo 'selected="selected"'; ?>><?php echo $level; ?></option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <?php
              }
            }
          ?>
          </tbody>
          <thead>
            <tr>
              <th class="head1" colspan="100%"><?php echo i18n_r(self::FILE.'/DISPLAY'); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/DISPLAYNAME'); ?></th>
              <td class="row1" style="width: 80%;"><?php $this->matrix->displayField(self::TABLE_USERS, 'displayname', $user['displayname']); ?></td>
            </tr>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/AVATAR'); ?></th>
              <td class="row1" style="width: 80%;"><?php $this->matrix->displayField(self::TABLE_USERS, 'avatar', $user['avatar']); ?></td>
            </tr>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo i18n_r(self::FILE.'/SIGNATURE'); ?></th>
              <td class="row1" style="width: 80%;"><?php $this->matrix->displayField(self::TABLE_USERS, 'signature', $user['signature']); ?></td>
            </tr>
          </tbody>
          <?php
            $fields = $this->matrix->getSchema(self::TABLE_USERS);
            $newfields = $fields['fields'];
            foreach ($this->coreFields as $field) {
              if (isset($newfields[$field])) unset($newfields[$field]);
            }
            if (!empty($newfields)) {
          ?>
          <thead>
            <tr>
              <th class="head1" colspan="100%"><?php echo i18n_r(self::FILE.'/CUSTOM'); ?></th>
            </tr>
          </thead>
          <?php
            foreach ($newfields as $field) {
              if (!isset($user[$field['name']])) $user[$field['name']] = $field['default'];
          ?>
          <tbody>
            <tr>
              <th class="row2" style="width: 20%;"><?php echo $field['label']; ?></th>
              <td class="row1" style="width: 80%;"><?php $this->matrix->displayField(self::TABLE_USERS, $field['name'], $user[$field['name']]); ?></td>
            </tr>
          </tbody>
          <?php }
            }  
          ?>
        </table>
      </div>
        <input type="submit" class="submit" name="save" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>">
      </form>    
      <?php
      $data_index->content = ob_get_contents();
      ob_end_clean();
    }
  }
  
  # bbcode parser (to include smileys)
  public function bbcode($content) {
    return $this->parser->bbcode($content, $this->getSmilies());
  }
  
  # display
  public function display() {
    if ($this->checkDependencies()) {
      global $data_index;
      
      // initialize i18n
      i18n_init();
      
      // parse uri
      $this->parseURI();
        
      // get correct page type
      $type = $this->pageType();
      
      if ($type) {
        // change output depending on type
        if ($type == 'profiles') {
          $this->displayUserList();
        }
        elseif ($type == 'profile') {
          $this->displayUserProfile();
        }
        elseif ($type == 'edit-profile') {
          $this->displayEditProfile($this->user);
        }
      }
    }
  }
  
  # content (placeholders)
  public function content() {
    global $content;
    
    $placeholders = $replacements = array();
    
    $placeholders[] = '(% cusers_form_login %)';
    $placeholders[] = '(% cusers_form_register %)';
    $placeholders[] = '(% cusers_form_forgot %)';
    
    $replacements[] = $this->displayForm('login');
    $replacements[] = $this->displayForm('register');
    $replacements[] = $this->displayForm('forgot-password');
    
    return str_replace($placeholders, $replacements, $content);
  }
  
  # admin
  public function admin() {
    if ($_GET['id'] == self::FILE) {
      if ($this->checkDependencies()) {
        // edit user
        if (isset($_GET['user']) && is_numeric($_GET['user'])) {
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/user.php');
        }
        // add user
        elseif (isset($_GET['add'])) {
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/add_user.php');
        }
        // custom fields
        elseif (isset($_GET['fields'])) {
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/fields.php');
        }
        // edit profile template
        elseif (isset($_GET['template']) && $_GET['template'] == 'profile') {
          // load template
          $template = XML2Array::createArray(file_get_contents($this->template));
          
          // save changes
          if ($_SERVER['REQUEST_METHOD']=='POST') {
            // update the template
            $template['channel'][$_GET['template']]['@cdata'] = $_POST['edit-template'];
            $xml = Array2XML::createXML('channel', $template['channel']);
            $xml->save($this->template);

            // success message
            if ($xml) {
              $this->matrix->getAdminError(i18n_r(self::FILE.'/TEMPLATE_UPDATESUCCESS'), true);
            }
            // error message
            else {
              $this->matrix->getAdminError(i18n_r(self::FILE.'/TEMPLATE_UPDATEERROR'), false);
            }
          }
          $template = $template['channel'][$_GET['template']]['@cdata'];

          ?>
          
        <!--header-->
          <h3 class="floated"><?php echo i18n_r(self::FILE.'/'.strtoupper($_GET['template'])); ?> (<?php echo i18n_r(self::FILE.'/TEMPLATE'); ?>)</h3>
          <div class="edit-nav">
            <a href="load.php?id=<?php echo self::FILE; ?>"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
            <div class="clear"></div>
          </div>
          
          
          <!--template-->
          <form method="post">
            <textarea name="edit-template" class="codeeditor DM_codeeditor text" id="post-edit-template"><?php echo $template; ?></textarea>
            <?php
              // get codemirror script
              $this->matrix->initialiseCodeMirror();
              $this->matrix->instantiateCodeMirror('edit-template');
            ?>
            <input type="submit" class="submit" value="<?php echo i18n_r('BTN_SAVECHANGES'); ?>"/>
          </form>
          
          
          <?php
        }
        // config
        elseif (isset($_GET['config'])) {
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/config.php');
        }
        // config
        elseif (isset($_GET['smilies'])) {
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/smilies.php');
        }
        // view all users
        else {
          if (isset($_GET['refresh'])) {
            $refresh = $this->matrix->refreshIndex();
            // success message
            if ($refresh) {
              $this->matrix->getAdminError(i18n_r(self::FILE.'/REFRESHINDEX_SUCCESS'), true);
            }
            
            // error message
            else {
              $this->matrix->getAdminError(i18n_r(self::FILE.'/REFRESHINDEX_ERROR'), false);
            }
          }
          include_once(GSPLUGINPATH.self::FILE.'/php/admin/users.php');
        }
      }
      else {
        $dependencies = $this->missingDependencies();
        include_once(GSPLUGINPATH.self::FILE.'/php/admin/dependencies.php');
      }
    }
  }
  
  // date
  public function date($timestamp) {
    if (is_numeric($timestamp)) {
      return date($this->config['date-format'], $timestamp);
    }
  }
  
  // search index
  public function searchIndex() {
    // for each item call i18n_search_index_item($id, $language, $creDate, $pubDate, $tags, $title, $content)
    var_dump($this->users);
    foreach ($this->users as $item) {
      // ensures guest user isn't indexed
      if ($item['id'] != -1) {

        // id is prefixed with the constant defined earlier (to make it unique)
        $id = self::SEARCHID.$item['id'];
      
        // format date correctly (the two stages MUST be done - you cannot just use the raw UNIX stamp as-is
        $date = date('j F Y', $item['registered']);
        $date = strtotime($date);
        
        // explode tags list and add default tags to the array
        $tags   = array('_user', '_users', '_cuser', '_cusers');
        
        // virtual tags for credate and pubdate and ensuring our item is in MatrixBlog
        $tags[] = '_cre_'.date('Y',  $item['registered']);
        $tags[] = '_cre_'.date('Ym', $item['registered']);
        $tags[] = '_pub_'.date('Y',  $item['registered']);
        $tags[] = '_pub_'.date('Ym', $item['registered']);
   
        // content (other user fields)
        $fieldsSchema = $this->usersSchema['fields'];
        $content = array();
        foreach ($item as $field => $value) {
          if ($field != 'password' && $fieldsSchema[$field]['index'] == 1) {
            $content[] = $value;
            
            // add to tags
            if (strlen($value) < 50) {
              $tags[] = '_'.$field.'_'.$value;
            }
          }
        }
        $content = implode("\n", $content);
        
        // format tags correctly for i18n search
        $tags = $this->matrix->formatTags($tags);
        
        // finally index the item
        i18n_search_index_item($id, $lang=null, $date, $date, $tags, $item['displayname'], $content);
      }
    }
  }
  
  // search item
  public function searchItem($id, $language, $creDate, $pubDate, $score) {
    if (substr($id, 0, strlen(self::SEARCHID)) == self::SEARCHID) {
      // load data
      $data = $this->users[substr($id, strlen(self::SEARCHID))];
      
      // get key for items of this plugin
      $key = self::SEARCHID;
      
      // translate search result keys into the relevant content
      $transkey = array('title'=>'displayname', 'description'=>'content', 'content'=>'signature', 'link'=>'username');
      return new TheMatrixSearchResultItem($data, $key, $id, $transkey, $language, $creDate, $pubDate, $score);
    }
    // item is not from our plugin - maybe from another plugin
    else return null; 
  }
  
  // search display
  public function searchDisplay($item, $showLanguage, $showDate, $dateFormat, $numWords) {
    if (substr($item->id, 0, strlen(self::SEARCHID)) == self::SEARCHID) {
      // convert i18n search object to array
      $entry = array();
      foreach ($this->usersSchema['fields'] as $field) {
        if ($field['name'] != 'password') $entry[$field['name']] = $item->{$field['name']};
      }
      $entry['id'] = substr($item->id, strlen(self::SEARCHID));
      
      ?>
      <h2><a href="<?php echo $this->getProfileURL($entry['username']); ?>"><?php echo $entry['username']; ?></a></h2>
      
      <?php
      return true;
    }
    return false;
  }

  // variable dumping function (just for easier displaying of variables when debugging)
  public function varDump($var) {
    echo '<pre><code>';
    var_dump($var);
    echo '</code></pre>';
  }
}

?>
