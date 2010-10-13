<?php

require_once('conf.php');
require_once('sys/Data.php');
require_once('sys/Controller.php');

session_start();

/* Start requested controller
 * If not $_GET[1] not set then use Default controller
 * Deafault controller is first controller in conf.php
 * Pretty url style 
 */
$_GET = parseurl();

if(isset($_GET[1])){
  $file = $controllers[$_GET[1]];
  if(file_exists($file)){
    include_once($file);
    $classname = ucfirst($_GET[1]);
    new $classname($database);
  }else{
    $base = new Controller();
    $base->error404();
  
  }
}else{
  $key = key($controllers);
  $file = $controllers[$key];
  if(file_exists($file)){
    include_once($file);
    $classname = ucfirst($key);
    new $classname($database);
  }else{
    $base = new Controller();
    $base->error404();
  }
}

/********************************************************
 * Some global helper functions
 *******************************************************/

/** 
 * Url helper for templates.
 * @return string Correct url path
 */
function url($url){
  return PATH . $url;
}

/** 
 * Parses pretty URLS 
 * example: url is http://example.com/foo/bar/1[/] (last slash is optional)
 * function returns array('foo','bar','bas',1)
 * @return array Array with pretty url params
 */
function parseurl(){
  $request = substr($_SERVER['REQUEST_URI'],strlen(PATH)-1);
  $tokens = explode("/",$request);
  return array_filter($tokens,function($elem){
    return $elem != '';
  });
}
