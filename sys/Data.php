<?php
class Data{
  private $data;

  public function __construct($data){
    $this->data = $data;
  }

  public function __get($field){
    $data = $this->data;
    if(isset($data[$field]))return $data[$field];
    else return false;
  
  }

}
