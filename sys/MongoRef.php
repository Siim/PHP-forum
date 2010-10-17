<?php
require_once('RefIf.php');
class MongoRef implements RefIf{
  protected $db;
  protected $data;

  public function __construct($db=array(),$data=null){
    $this->db = $db;
    $this->data = $data;
  }

  public function __get($field){
    $data = $this->fetch();
    if(isset($data[$field]))return $data[$field];
    else return null;
  }

  public function fetch(){
    $ref = $this->data['$ref'];
    $id = $this->data['$id'];

    return $this->db->$ref->findOne(array(
      '_id' => $id
    ));
  }
}
