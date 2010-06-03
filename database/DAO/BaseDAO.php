<?php

/**
 * Some common methods for each DAO
 */

class BaseDAO{
  protected $query;
  protected $filter;
  protected $valid = true;

  public function __construct(){
    $qn = $this->getQueryname();
    require_once FORUM_ROOT . 'database/queries/' . $this->getQueryname() . ".php";
    $this->query = new $qn();
  }
  
  public function getQuery(){
    return $this->query;
  }
  
  public function __get($name){
    return $this->$name;
  }

  /**
   * If you want to filter field defined in DAO (data access obj),
   * you can write a method fieldnameFilter($data){}
   * and return whatever data you want.
   * Usually we want to filter user input :)
   */
  public function __set($name, $val){
    $methods = get_class_methods($this);
    $filterMeth = $name."Filter";
    if(array_search($filterMeth, $methods)){
      $this->$name = $this->$filterMeth($val);
    }else{
      $this->$name = $val;
    }
  }

  public function save(){
    return $this->getQuery()->save($this);
  
  }
  
  public function delete(){
    return $this->getQuery()->delete($this);
  
  }
  
  /**
   * DAO and filtered DAO both share the same query
   * Filtered DAO is used for validating user input
   * Generate query class name using DAO class name
   */
  protected function getQueryname(){
    if(get_parent_class($this)!='BaseDAO'){
      $classname = get_parent_class($this);
    }else{
      $classname = get_class($this);
    }
    $classname = substr($classname,0,strlen($classname)-3);
    return $classname . 'Query';
  }
  
  
  public function fetchItem($id){
    $this->getQuery()->fetchItem($this);
  }
  
  public function fetchAll($parent=1){
    return $this->getQuery()->fetchAll($parent,$this); 
  }

  public function validate(){
    return $this->valid;
  }

}  

?>
