<?php

  error_reporting(0);

  /**
   * Paths
   */
  define('WWW_ROOT',$_SERVER['DOCUMENT_ROOT']);

  /** 
   * forum base dir
   * example: /forum/ OR "/"
   */
  $fpath = substr(getcwd(),strlen(WWW_ROOT),strlen(getcwd()));
  define('FORUM_PATH',$fpath . "/");
  define('FORUM_ROOT',WWW_ROOT . FORUM_PATH);
  define('VIEW_DIR', FORUM_ROOT. '/view/');
  define('VIEW_CACHE_DIR', FORUM_ROOT. '/view/cache/');
  /* Define layout/design file */
  define('LAYOUT_FILE', VIEW_DIR .'layout.haml');
  define('ERROR_404_PAGE','error404.haml');
  define('EMAIL_FROM','KNK Foorum <info@keilanoortekeskus.ee>');
  
  require_once 'database/DAO/UserDAO.php';
  session_start();

  //query helper
  function query(){
    $q = '';
    foreach($_GET as $k => $v){
      switch($k){
        case 'c':
        case 'a':
        case 'f':
        case 't': $q .= "$k=$v".'&'; break;
      }
    }
    return '?'.$q;
  }

  //url helper
  function url($url){
    return FORUM_PATH . $url;
  }
     
  /* c - controller */
  if(isset($_GET['c'])){
    $controller = $_GET['c'];
  }else{
    $controller = '';
  }
  
  switch($controller){
    case 'user':
      require_once 'controllers/User.php';
      new User();
    break;
    case 'install':
      require_once 'controllers/Install.php';
      new Install();
    break;

    default:
      require_once 'controllers/Forum.php';
      new Forum();
    break;
  }
