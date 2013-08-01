<?php
/* Centralized Users */

# thisfile
  $thisfile = basename(__FILE__, ".php");
 
# language
  i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US');
 
# requires
  require_once(GSPLUGINPATH.$thisfile.'/php/captcha.class.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/main.class.php');
  
# class instantiation
  $mcusers = new MatrixCUsers; // instantiate class

# register plugin
  register_plugin(
    $mcusers->pluginInfo('id'),
    $mcusers->pluginInfo('name'),
    $mcusers->pluginInfo('version'),
    $mcusers->pluginInfo('author'),
    $mcusers->pluginInfo('url'),
    $mcusers->pluginInfo('description'),
    $mcusers->pluginInfo('page'),
    array($mcusers, 'admin')
  );

# activate actions/filters
  # front-end
    add_action('error-404',    array($mcusers, 'display'));
    add_filter('content',      array($mcusers, 'content'));
    add_action('theme-header', array($mcusers, 'init'), array($thisfile));
  # back-end
    add_action($mcusers::PAGE.'-sidebar', 'createSideMenu' , array($mcusers->pluginInfo('id'), $mcusers->pluginInfo('sidebar'))); // sidebar link
    add_action('search-index',   array($mcusers, 'searchIndex'));
    add_filter('search-item',    array($mcusers, 'searchItem'));
    add_filter('search-display', array($mcusers, 'searchDisplay'));
    
# functions
  # output form (login/register/forgot password)
  function cusers_form($type='login') {
    global $mcusers;
    echo $mcusers->displayForm($type);
  }
  # check if viewer is logged in
  function cusers_logged_in() {
    global $mcusers;
    return $mcusers->loggedIn();
  }
?>