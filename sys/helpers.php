<?php

/**
 * Allow only specified array keys
 * @param array Array of allowed keys
 * @param array Array to be filtered
 * @return array Filered array
 */
function array_allow($arr,$values){
  foreach($arr as $k => $v){
    if(!in_array($k,$values))unset($arr[$k]);
  }
  return $arr;
}

/**
 * Current request uri
 * e.g. if $len is 3, accept only /foo/bar/baz from 
 * /foo/bar/baz/bat/rat/77
 * @param int Path length 
 */
function request_uri($len=0){
  if($len>0)$get = array_slice($_GET,0,$len);
  else $get = $_GET;

  return '/' . implode("/",$get);
}

/**
 * Calculate last page number
 * @param int Total items
 * @param int Items per page
 * @return int Last page number
 */
function lastpage($count,$perpage=10){
  return floor($count/$perpage) + (($count%$perpage>0)?1:0);
}
