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

/**
 * Calculate current page number
 * @param int requested page number
 * @param int known last page
 * @return int page number within correct bounds
 */
function currentpage($page, $lastpage){
  $page = (int) $page;
  if($page>0){
    if($page>$lastpage)$page=$lastpage;
  }else{
    $page = 1;
  }
  return $page;
}

/**
 * Return only seconds from mongo date format
 * @param string Mongo timestamp
 * @return int Unix timestamp in seconds
 */
function from_mongo_date($timestamp){
  return next(sscanf($timestamp,"%d %d"));
}
