<?php
require_once 'BaseQuery.php';
class UserQuery extends BaseQuery{

  public function __construct(){    
    parent::__construct();
    
  }

  public function identify($user, $pass){
    $q=$this->prepare("SELECT id,username,email,admin,posts,active FROM user WHERE username=? AND password=? LIMIT 1");
    $q->execute(array($user,$pass));
    return $q->fetch(PDO::FETCH_ASSOC);
  }

  public function exists($key,$val){
    $q=$this->prepare("SELECT id FROM user WHERE $key=? LIMIT 1");
    $q->execute(array($val));
    return $q->fetch();
  }

  public function fetchItemByCode(&$model){
    $this->fetchItemBy(&$model,'active');
  }

  public function fetchItemByEmail(&$model){
    $this->fetchitemBy(&$model,'email');
  }
  
  /**
   * TODO: move to BaseQuery
   */
  public function fetchItemBy(&$model,$rowName){
  
    $q = $this->prepare('SELECT * FROM '.$model->tableName.' WHERE '.$rowName.'=? LIMIT 1');
    $q->execute(array($model->$rowName));
    $data = $q->fetch();
    
    foreach($data as $k => $v){
      $model->$k = $v;
    }
  }

  public function insert($m){
    $q = $this->prepare("INSERT INTO user (username,password,email,registered,active,admin) VALUES (?,?,?,?,?,?)");
    echo $q->execute(array(
      $m->username,
      $m->password,
      $m->email,
      $m->registered,
      $m->active,
      $m->admin
    ));
  }

  public function update($m){
    $tn = $m->tableName;
    $q= $this->prepare("UPDATE $tn SET email=?, posts=?, active=?, password=? WHERE id=?");
    $q->execute(array($m->email,$m->posts,$m->active,$m->password, $m->id));
  
  }

}
