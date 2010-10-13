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
