<?php
require_once('RefIf.php');
class MongoRef implements RefIf{
  protected $db;
  protected $data;

  public function __construct($db=array(),$data=null){
    $this->db = $db;
    $this->data = $data;
  }

  public function fetch(){
    $ref = $this->data['$ref'];
    $id = $this->data['$id'];

    return $this->db->$ref->findOne(array(
      '_id' => $id
    ));
  }
}
