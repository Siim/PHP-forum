<?php

/**
 * Data array wrapper
 */

require_once('RefIf.php');
class MongoData{
  private $data;
  private $db;
  private $refclass = 'MongoRef';

  /**
   * Construct data object. If $db is given then referenced 
   * data can be easily fetched: e.g. $data->field->fetch()
   * or even $data->field[i]->fetch()
   * @param array Data
   * @param Mongo Database
   */
  public function __construct($data,$db=null){
    $this->db = $db;
    $this->data = $data;
  }

  public function __get($field){
    $data = $this->data;
    $refclass = $this->refclass;
    if(isset($data[$field])){
      $df = $data[$field];

      if(is_array($df)&&$this->db!=null && (new $refclass() instanceof RefIf)){
        if(self::checkRef($df)){
          $data[$field] = new $refclass($this->db,$df);
          $this->data = $data;
        }else{
          $res = array();
          foreach($df as $d){
            if(self::checkRef($d)){
              $res[] = new $refclass($this->db,$d);
            }else{
              $res = $df;
              break;
            }
          }
          $data[$field] = $res;
          $this->data[$field] = $res;
        }
      }
      return $data[$field];
    }
    else return false;
  }

  public function setRefClassName($class){
    $this->refclass = $class;
  }

  public function setDB($db){
    $this->db = $db;
  }

  public static function checkRef($arr){
    return isset($arr['$ref'])&&isset($arr['$id']);
  }
}
