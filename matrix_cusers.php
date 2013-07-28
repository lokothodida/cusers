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
    $mcusers::FILE,           // id
    $mcusers->title,          // name
    $mcusers::VERSION,        // version
    $mcusers::AUTHOR,         // author
    $mcusers::URL,            // url
    $mcusers->desc,           // description
    $mcusers::PAGE,           // page type - on which admin tab to display
    array($mcusers, 'admin')  // administration function
  );

# activate actions/filters
  # front-end
    add_action('error-404', array($mcusers, 'display')); // display for plugin
  # back-end
    add_action($mcusers::PAGE.'-sidebar', 'createSideMenu' , array($mcusers::FILE, $mcusers->sidebarLabel)); // sidebar link
    add_action('search-index',   array($mcusers, 'searchIndex'));
    add_filter('search-item',    array($mcusers, 'searchItem'));
    add_filter('search-display', array($mcusers, 'searchDisplay'));
?>